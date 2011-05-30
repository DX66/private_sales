<?php
/* vim: set ts=4 sw=4 sts=4 et: */
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| X-Cart                                                                      |
| Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>                  |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
| PLEASE READ  THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE "COPYRIGHT" |
| FILE PROVIDED WITH THIS DISTRIBUTION. THE AGREEMENT TEXT IS ALSO AVAILABLE  |
| AT THE FOLLOWING URL: http://www.x-cart.com/license.php                     |
|                                                                             |
| THIS  AGREEMENT  EXPRESSES  THE  TERMS  AND CONDITIONS ON WHICH YOU MAY USE |
| THIS SOFTWARE   PROGRAM   AND  ASSOCIATED  DOCUMENTATION   THAT  RUSLAN  R. |
| FAZLYEV (hereinafter  referred to as "THE AUTHOR") IS FURNISHING  OR MAKING |
| AVAILABLE TO YOU WITH  THIS  AGREEMENT  (COLLECTIVELY,  THE  "SOFTWARE").   |
| PLEASE   REVIEW   THE  TERMS  AND   CONDITIONS  OF  THIS  LICENSE AGREEMENT |
| CAREFULLY   BEFORE   INSTALLING   OR  USING  THE  SOFTWARE.  BY INSTALLING, |
| COPYING   OR   OTHERWISE   USING   THE   SOFTWARE,  YOU  AND  YOUR  COMPANY |
| (COLLECTIVELY,  "YOU")  ARE  ACCEPTING  AND AGREEING  TO  THE TERMS OF THIS |
| LICENSE   AGREEMENT.   IF  YOU    ARE  NOT  WILLING   TO  BE  BOUND BY THIS |
| AGREEMENT, DO  NOT INSTALL OR USE THE SOFTWARE.  VARIOUS   COPYRIGHTS   AND |
| OTHER   INTELLECTUAL   PROPERTY   RIGHTS    PROTECT   THE   SOFTWARE.  THIS |
| AGREEMENT IS A LICENSE AGREEMENT THAT GIVES  YOU  LIMITED  RIGHTS   TO  USE |
| THE  SOFTWARE   AND  NOT  AN  AGREEMENT  FOR SALE OR FOR  TRANSFER OF TITLE.|
| THE AUTHOR RETAINS ALL RIGHTS NOT EXPRESSLY GRANTED BY THIS AGREEMENT.      |
|                                                                             |
| The Initial Developer of the Original Code is Ruslan R. Fazlyev             |
| Portions created by Ruslan R. Fazlyev are Copyright (C) 2001-2011           |
| Ruslan R. Fazlyev. All Rights Reserved.                                     |
+-----------------------------------------------------------------------------+
\*****************************************************************************/

/**
 * Google checkout (callback processing)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: gcheckout_callback.php,v 1.34.2.4 2011/01/10 13:11:58 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

define('GOOGLE_CHECKOUT_CALLBACK', 1);

func_gcheckout_debug('*** CALLBACK RECEIVED');

if (!defined('GCHECKOUT_IGNORE_AUTH')) {

    // For Apache CGI predefined variables $PHP_AUTH_USER $PHP_AUTH_PW are not set

    if (!isset($_SERVER['PHP_AUTH_USER']) && !isset($_SERVER['PHP_AUTH_PW'])) {
        if (isset($_SERVER['REMOTE_USER']) && preg_match('/Basic +(.*)$/i', $_SERVER['REMOTE_USER'], $matches))
            // $REMOTE_USER has been rewrited
            $_remoute_user = $matches[1];
        elseif (isset($_SERVER['REDIRECT_REMOTE_USER']) && preg_match('/Basic +(.*)$/i', $_SERVER['REDIRECT_REMOTE_USER'], $matches))
            // $REDIRECT_REMOTE_USER has been rewrited
            $_remoute_user = $matches[1];

        list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode($_remoute_user));
    }

    // Check if callback-request has been successfully authorized
    if ($_SERVER['PHP_AUTH_USER'] != $config['Google_Checkout']['gcheckout_mid'] || $_SERVER['PHP_AUTH_PW'] != $config['Google_Checkout']['gcheckout_mkey']) {
        header('WWW-Authenticate: Basic');
        header('HTTP/1.0 401 Unauthorized');

        x_log_flag('log_payment_processing_errors', 'PAYMENTS', "Google checkout payment module: Script called without authorization, or caller authorization failed.", true);
        exit;
    }

    func_gcheckout_debug("\t+ Authorization passed");

}
else {
    // HTTP authentication ignored
    func_gcheckout_debug("\t+ Authorization ignored due to GCHECKOUT_IGNORE_AUTH defined");
}

// Check if no data passed
if (empty($HTTP_RAW_POST_DATA)) {
    x_log_flag('log_payment_processing_errors', 'PAYMENTS', "Google checkout payment module: Script called with no data passed to it.", true);
    exit;
}

if ($HTTP_RAW_POST_DATA == 'test') {
    func_gcheckout_debug("\t+ Testing callback URL");
    die('Success');
}

$parse_error = false;
$options = array(
    'XML_OPTION_CASE_FOLDING' => 1,
    'XML_OPTION_TARGET_ENCODING' => 'ISO-8859-1'
);

$HTTP_RAW_POST_DATA = str_replace("\r",'', $HTTP_RAW_POST_DATA);
$parsed = func_xml_parse($HTTP_RAW_POST_DATA, $parse_error, $options);

if (empty($parsed)) {
    x_log_flag('log_payment_processing_errors', 'PAYMENTS', "Google checkout payment module: Received data could not be identified correctly.", true);
    exit;
}

$type = key($parsed);

func_gcheckout_debug("\t+ Message received: $type");

$avs_info = array(
    'Y' => "Full AVS match (address and postal code)",
    'P' => "Partial AVS match (postal code only)",
    'A' => "Partial AVS match (address only)",
    'N' => "No AVS match",
    'U' => "AVS not supported by issuer"
);

$cvv_info = array(
    'M' => "CVN match",
    'N' => "No CVN match",
    'U' => "CVN not available",
    'E' => "CVN error"
);

$goid = func_array_path($parsed, $type."/GOOGLE-ORDER-NUMBER/0/#");

$is_exit = false;
$skey = false;
$send_charge_order_xml = false;
$send_add_merchant_order_number_xml = false;

x_load(
    'user', 
    'shipping', 
    'category' // For Func_is_valid_coupon->Func_get_category_path function call stack
);

##############################################################################

if ($type == 'MERCHANT-CALCULATION-CALLBACK') {

    // merchant-calculation-callback message
    // Step 0: callback from Google

    $skey = func_array_path($parsed, $type."/SHOPPING-CART/MERCHANT-PRIVATE-DATA/MERCHANT-NOTE/0/#");

    // Restore the session
    $sessid = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref = '$skey'");

    x_session_id($sessid);
    x_session_register('login');
    x_session_register('logged_userid');
    x_session_register('cart');
    x_session_register('user_tmp', array());
    x_session_register('current_carrier', 'UPS');

    if (!empty($logged_userid)) {
        $user_account['membershipid'] = func_query_first_cell("SELECT membershipid FROM $sql_tbl[customers] WHERE id='$logged_userid'");
    }

    $products = func_products_in_cart($cart, (!empty($user_account['membershipid']) ? $user_account['membershipid'] : ''));

    func_gcheckout_debug("\t+ skey: $skey");

    // Get addresses, shipping methods and coupon codes from XML request

    $buyer_id = func_array_path($parsed, $type."/BUYER-ID/0/#");
    $addresses = func_array_path($parsed, $type."/CALCULATE/ADDRESSES/ANONYMOUS-ADDRESS");
    $shipping_requested = func_array_path($parsed, $type.'/CALCULATE/SHIPPING/METHOD');
    $pcodes = func_array_path($parsed, $type."/CALCULATE/MERCHANT-CODE-STRINGS/MERCHANT-CODE-STRING");
    $calculate_tax = func_array_path($parsed, $type."/CALCULATE/TAX/0/#");

    $calculate_tax = ($calculate_tax == 'true' ? true : false);

    func_gcheckout_debug("\t+ Buyer-id: $buyer_id");

    // Validate the coupon and Gift Certificate codes

    $result_code = array();

    // Remove all applied gift certificates and discount coupon from the cart
    func_unset($cart, 'discount_coupon');

    if (!empty($active_modules['Gift_Certificates'])) {
        if (!empty($cart['applied_giftcerts']) && is_array($cart['applied_giftcerts'])) {
            foreach ($cart['applied_giftcerts'] as $_gc_code) {
                // Unblock Gift certificate with specified code
                func_giftcert_unset($_gc_code['giftcert_id']);
            }
        }
        func_unset($cart, 'applied_giftcerts', 'giftcert_discount');
    }

    // Prepare coupon validation XML code
    if (!empty($pcodes) && is_array($pcodes)) {

        $coupons_counter = 0;

        // Validate the coupon code and apply it to the cart
        foreach ($pcodes as $_pcode) {

            $error_msg = '';
            $_promo_code = $_pcode['@']['CODE'];
            $_pcode_result = 'incorrect';
            if (!empty($active_modules['Discount_Coupons']))
                $coupon_result = func_is_valid_coupon($_promo_code);

            if (!empty($active_modules['Discount_Coupons']) && $coupon_result == 0) {
                // The valid discount coupon is was found: only one discount coupon can be applied
                $coupons_counter++;

                if ($coupons_counter == 1) {
                    // Get the discount value and generate XML
                    $coupon_provider = func_query_first_cell("SELECT provider FROM $sql_tbl[discount_coupons] WHERE coupon='$_promo_code'");
                    $discount_result = func_calculate_discounts(0, $products, $_promo_code, $coupon_provider);
                    $cart['discount_coupon'] = $_promo_code;

                    $discount_result['coupon_discount'] = price_format($discount_result['coupon_discount']);
                    $_pcode_result = 'discount coupon';
                    if ($discount_result['coupon_type'] == 'free_ship')
                        $_pcode_message = func_google_encode(func_get_langvar_by_name($config['Google_Checkout']['gcheckout_use_gc_shipping'] == 'Y' ? 'lbl_gcheckout_coupon_freeship_do_not_work' : 'lbl_gcheckout_coupon_freeship', array('coupon_code'=>$_promo_code), 'en', true, true));
                    else
                        $_pcode_message = func_google_encode(func_get_langvar_by_name('lbl_gcheckout_coupon_applied', array('coupon_code'=>$_promo_code), 'en', true, true));

                    $result_code[] =<<<OUT
                <coupon-result>
                    <valid>true</valid>
                    <code>{$_promo_code}</code>
                    <calculated-amount currency="{$config['Google_Checkout']['gcheckout_currency']}">{$discount_result['coupon_discount']}</calculated-amount>
                    <message>{$_pcode_message}</message>
                </coupon-result>
OUT;
                }
                else
                    $error_msg = 'Only one discount coupon can be applied';
            }

            elseif (!empty($active_modules['Gift_Certificates']) && ($gc_error_code = func_giftcert_check($_promo_code)) == 0) {
                // The valid Gift certificate was found
                $gc = func_giftcert_data($_promo_code, true);
                if (!empty($gc)) {
                    func_giftcert_apply($gc);
                    $_pcode_result = 'gift certificate';
                    $gc_debit = price_format($gc['debit']);
                    $_pcode_message = func_google_encode(func_get_langvar_by_name('lbl_gcheckout_giftcert_applied', false, 'en', true, true));
                    $result_code[] =<<<OUT
                <gift-certificate-result>
                    <valid>true</valid>
                    <code>{$_promo_code}</code>
                    <calculated-amount currency="{$config['Google_Checkout']['gcheckout_currency']}">{$gc_debit}</calculated-amount>
                    <message>{$_pcode_message}</message>
                </gift-certificate-result>
OUT;
                }
                else
                    $error_msg = 'Sorry, you entered an invalid code';
            }

            else {
                // Coupon code was not found
                $error_msg = 'Sorry, you entered an invalid coupon code';
            }

            if (!empty($error_msg)) {
                // Wrong coupon code XML
                $error_msg = func_google_encode($error_msg);
                $result_code[] = <<<OUT
                <coupon-result>
                    <valid>false</valid>
                    <code>{$_promo_code}</code>
                    <message>$error_msg</message>
                </coupon-result>
OUT;
            }
            if (!empty($active_modules['Discount_Coupons']))
                func_gcheckout_debug("\t+ promo code: '{$_promo_code}' ($_pcode_result)" . (!empty($error_msg) ? ' Error message: [' . $coupon_result . '] ' . $error_msg : ''));
        }

        x_session_save('cart');

        // Prepare XML code with results of coupons validation
        $result_code = implode("\n", $result_code);
        $coupon_code_xml = <<<OUT
            <merchant-code-results>
$result_code
            </merchant-code-results>
OUT;

    }
    else {

        func_gcheckout_debug("\t+ There are no promo codes specified");

    }

    if (empty($user_tmp)) {
        // Save buyer-id value
        $user_tmp['buyer-id'] = $buyer_id;
        $user_tmp['buyer-ids'] = array();
    }
    elseif ($user_tmp['buyer-id'] != $buyer_id) {
        // Check if buyer-id is equal to the value in $user_tmp['buyer-id'] (just in case)
        func_gcheckout_debug("\t+ [Warning] 'buyer-id' field value passed via callback does not match: user_tmp[buyer-id]='".$user_tmp['buyer-id']."'");
        $user_tmp['buyer-ids'][] = $user_tmp['buyer-id'];
        $user_tmp['buyer-id'] = $buyer_id;
    }

    $user_tmp['addresses'] = array();

    // Calculate shipping for each address

    // Prepare array of the requested shipping methods
    if (!empty($shipping_requested)) {
        $_shipping_requested = array();
        for ($i = 0; $i < count($shipping_requested); $i++) {
            $_shipping_requested[] = array(
                'shipping' => $shipping_requested[$i]['@']['NAME'],
                'rate' => 0.00,
                'allowed' => false,
                'tax_cost' => 0.00
            );
        }
        $shipping_requested = $_shipping_requested;
        unset($_shipping_requested);
    }
    else {

        func_gcheckout_debug("\t+ No shipping calculation requested");

    }

    if (!empty($addresses) && is_array($addresses)) {

        for ($i = 0; $i < count($addresses); $i++) {

            // Each address has unique ID
            $_address_id = func_array_path($addresses[$i], "@/ID");

            $user_tmp['addresses'][$_address_id] = array();

            $_tmp = array();
            $_tmp['b_country'] = $_tmp['s_country'] = func_array_path($addresses[$i], "#/COUNTRY-CODE/0/#");
            $_state_code = func_query_first_cell("SELECT code FROM $sql_tbl[states] WHERE country_code='".addslashes($_tmp['b_country'])."' AND state='".addslashes(func_array_path($addresses[$i], "#/REGION/0/#"))."'");
            if (empty($_state_code))
                $_state_code = func_array_path($addresses[$i], "#/REGION/0/#");
            $_tmp['b_state'] = $_tmp['s_state'] = $_state_code;
            $_tmp['b_city'] = $_tmp['s_city'] = func_array_path($addresses[$i], "#/CITY/0/#");
            $_tmp['b_zipcode'] = $_tmp['s_zipcode'] = func_array_path($addresses[$i], "#/POSTAL-CODE/0/#");

            $cart['userinfo'] = func_array_merge($cart['userinfo'], $_tmp);

            // Emulate the 'apply_default_country' option enabled
            $config['General']['apply_default_country'] = 'Y';
            $config['General']['default_country'] = $_tmp['b_country'];
            $config['General']['default_zipcode'] = $_tmp['b_zipcode'];
            $config['General']['default_state'] = $_tmp['b_state'];
            $config['General']['default_city'] = $_tmp['b_city'];

            $config['Shipping']['enable_all_shippings'] = 'N';

            if (!empty($shipping_requested)) {

                // Get the shipping methods list with rates
                $intershipper_recalc = 'Y';
                $_allowed_shipping_methods = func_get_shipping_methods_list($cart, $products, $cart['userinfo']);

                // Generate the full list of shipping methods for current address
                $_result_shipping_methods = $shipping_requested;
                for ($j = 0; $j < count($shipping_requested); $j++) {
                    if (!empty($_allowed_shipping_methods)) {
                        foreach ($_allowed_shipping_methods as $_ship_method) {
                            if (func_insert_trademark($_ship_method['shipping'], 'use_alt') == utf8_decode($shipping_requested[$j]['shipping'])) {
                                $_result_shipping_methods[$j] = $_ship_method;
                                break;
                            }
                        }
                    }
                }

                $_tmp['shipping_methods'] = $_result_shipping_methods;

            } elseif (!empty($calculate_tax)) {

                // Calculate tax cost
                $tmp_cart = $cart;
                $calc_result = func_calculate($tmp_cart, $products, '', 'C');
                $_tmp['tax_cost'] = price_format($calc_result["tax_cost"]);

            }

            $user_tmp['addresses'][$_address_id] = $_tmp;

        }

        foreach ($user_tmp['addresses'] as $_address_id => $_address) {
            func_gcheckout_debug("\t+ address id: $_address_id ({$_address['s_country']})");
            $_shipping_name_xml_attr = '';

            if (!empty($_address['shipping_methods']) && is_array($_address['shipping_methods'])) {
                foreach ($_address['shipping_methods'] as $_ship_method) {
                    $_ship_method['shipping'] = func_google_encode(func_insert_trademark(utf8_decode($_ship_method['shipping']), 'use_alt'));
                    $_ship_method['rate'] = price_format($_ship_method['rate']);
                    $_ship_method['tax_cost'] = price_format($_ship_method['tax_cost']);
                    $_shippable = ($_ship_method['allowed'] ? 'true' : 'false');

                    if (!empty($calculate_tax))
                        $total_tax_xml =<<<OUT
            <total-tax currency="{$config['Google_Checkout']['gcheckout_currency']}">{$_ship_method['tax_cost']}</total-tax>
OUT;
                    else
                        $total_tax_xml = '';

                    $result_xml_arr[] =<<<OUT
        <result shipping-name="{$_ship_method['shipping']}" address-id="{$_address_id}">
            <shipping-rate currency="{$config['Google_Checkout']['gcheckout_currency']}">{$_ship_method['rate']}</shipping-rate>
            <shippable>$_shippable</shippable>
            $total_tax_xml
$coupon_code_xml
        </result>
OUT;
                    if ($_ship_method['allowed'])
                        func_gcheckout_debug("\t\t+ {$_ship_method['shipping']}: {$_ship_method['rate']}, tax cost: " . (!empty($calculate_tax) ? $_ship_method['tax_cost'] : 'n/a'));
                }
            }
            else {
                if (!empty($calculate_tax)) {
                    $_address['tax_cost'] = price_format($_address['tax_cost']);
                    $total_tax_xml =<<<OUT
            <total-tax currency="{$config['Google_Checkout']['gcheckout_currency']}">{$_address['tax_cost']}</total-tax>
OUT;
                    func_gcheckout_debug("\t\t+ tax cost: {$_address['tax_cost']}");
                }
                else
                    $total_tax_xml = '';

                $result_xml_arr[] =<<<OUT
        <result address-id="{$_address_id}">
            $total_tax_xml
$coupon_code_xml
        </result>
OUT;
            }
        }

        $result_xml = implode("\n", $result_xml_arr);

        $result =<<<OUT
<?xml version="1.0" encoding="UTF-8"?>
<merchant-calculation-results xmlns="http://checkout.google.com/schema/2">
    <results>
$result_xml
    </results>
</merchant-calculation-results>
OUT;

        func_gcheckout_debug("\t+ Message sent: MERCHANT-CALCULATION-RESULTS");

        func_gcheckout_debug($result, true);

    }
    else {

        func_gcheckout_debug("\t+ There are no addresses received");

    }

    echo $result;

    x_session_save();
    exit;

##############################################################################

} elseif ($type == 'NEW-ORDER-NOTIFICATION') {

    // new-order-notification message
    $exists = func_query_first_cell("SELECT COUNT(goid) FROM $sql_tbl[gcheckout_orders] WHERE goid='$goid'");
    if (intval($exists) > 0) {
        func_gcheckout_debug("\t+ [Error]: Duplicate NEW-ORDER-NOTIFICATION transaction goid: #$goid.");
        return;
    }

    func_array2insert(
        'gcheckout_orders',
        array(
            'goid' => $goid,
        ),
        true
    );

    // Step 1: callback from Google

    $skey = func_array_path($parsed, $type."/SHOPPING-CART/MERCHANT-PRIVATE-DATA/MERCHANT-NOTE/0/#");
    $fulfillment_os = func_array_path($parsed, $type."/FULFILLMENT-ORDER-STATE/0/#");
    $financial_os = func_array_path($parsed, $type."/FINANCIAL-ORDER-STATE/0/#");
    $total_cost = func_array_path($parsed, $type."/ORDER-TOTAL/0/#");
    $merchant_calculation_successful = func_array_path($parsed, $type."/ORDER-ADJUSTMENT/MERCHANT-CALCULATION-SUCCESSFUL/0/#");

    func_gcheckout_debug("\t+ skey: $skey");

    // Check if callback is valid
    func_gcheckout_is_valid_callback($skey);

    $_pp3_data = func_query_first("SELECT sessionid, trstat FROM $sql_tbl[cc_pp3_data] WHERE ref = '$skey'");
    if ($financial_os != 'REVIEWING' || !preg_match("/^GO|/", $_pp3_data['trstat'])) {
        $bill_error = 1;
        $bill_output['code'] = 2;
        $bill_output['billmes'] = "Inner error";

        $is_exit = true;

        func_gcheckout_debug("\t+ [Error] - financial_os: '$financial_os'; trstat: '$_pp3_data[trstat]'");

    } else {

        // Restore the session
        $sessid = $_pp3_data['sessionid'];
        x_session_id($sessid);
        x_session_register('cart');
        x_session_register('login');
        x_session_register('logged_userid');
        x_session_register('user_tmp');
        x_session_register('current_carrier', 'UPS');

        x_load('cart','crypt','user','order');

        if (!empty($logged_userid)) {
            $user_account['membershipid'] = func_query_first_cell("SELECT membershipid FROM $sql_tbl[customers] WHERE id='$logged_userid'");
        }

        $products = func_products_in_cart($cart, (!empty($user_account['membershipid']) ? $user_account['membershipid'] : ''));

        $profile_values = array();
        $profile_values['firstname']    = func_array_path($parsed, $type."/BUYER-BILLING-ADDRESS/STRUCTURED-NAME/FIRST-NAME/0/#");
        $profile_values['lastname']     = func_array_path($parsed, $type."/BUYER-BILLING-ADDRESS/STRUCTURED-NAME/LAST-NAME/0/#");
        $profile_values['company']      = func_array_path($parsed, $type."/BUYER-BILLING-ADDRESS/COMPANY-NAME/0/#");
        $profile_values['email']        = func_array_path($parsed, $type."/BUYER-BILLING-ADDRESS/EMAIL/0/#");
        $profile_values['phone']        = func_array_path($parsed, $type."/BUYER-BILLING-ADDRESS/PHONE/0/#");
        $profile_values['fax']          = func_array_path($parsed, $type."/BUYER-BILLING-ADDRESS/FAX/0/#");

        $profile_values['address'] = array();

        $profile_values['address']['B'] = array();

        $profile_values['address']['B']['firstname']    = $profile_values['firstname'];
        $profile_values['address']['B']['lastname']     = $profile_values['lastname'];
        $profile_values['address']['B']['address']      = func_array_path($parsed, $type."/BUYER-BILLING-ADDRESS/ADDRESS1/0/#");
        $profile_values['address']['B']['address_2']    = func_array_path($parsed, $type."/BUYER-BILLING-ADDRESS/ADDRESS2/0/#");
        $profile_values['address']['B']['city']         = func_array_path($parsed, $type."/BUYER-BILLING-ADDRESS/CITY/0/#");
        $profile_values['address']['B']['state']        = func_array_path($parsed, $type."/BUYER-BILLING-ADDRESS/REGION/0/#");
        $profile_values['address']['B']['country']      = func_array_path($parsed, $type."/BUYER-BILLING-ADDRESS/COUNTRY-CODE/0/#");
        $profile_values['address']['B']['zipcode']      = func_array_path($parsed, $type."/BUYER-BILLING-ADDRESS/POSTAL-CODE/0/#");
        $profile_values['address']['B']['phone']        = func_array_path($parsed, $type."/BUYER-SHIPPING-ADDRESS/PHONE/0/#");
        $profile_values['address']['B']['fax']          = func_array_path($parsed, $type."/BUYER-BILLING-ADDRESS/FAX/0/#");

        $profile_values['address']['S'] = array();

        $profile_values['address']['S']['firstname']    = func_array_path($parsed, $type."/BUYER-SHIPPING-ADDRESS/STRUCTURED-NAME/FIRST-NAME/0/#");
        $profile_values['address']['S']['lastname']     = func_array_path($parsed, $type."/BUYER-SHIPPING-ADDRESS/STRUCTURED-NAME/LAST-NAME/0/#");
        $profile_values['address']['S']['address']      = func_array_path($parsed, $type."/BUYER-SHIPPING-ADDRESS/ADDRESS1/0/#");
        $profile_values['address']['S']['address_2']    = func_array_path($parsed, $type."/BUYER-SHIPPING-ADDRESS/ADDRESS2/0/#");
        $profile_values['address']['S']['city']         = func_array_path($parsed, $type."/BUYER-SHIPPING-ADDRESS/CITY/0/#");
        $profile_values['address']['S']['state']        = func_array_path($parsed, $type."/BUYER-SHIPPING-ADDRESS/REGION/0/#");
        $profile_values['address']['S']['country']      = func_array_path($parsed, $type."/BUYER-SHIPPING-ADDRESS/COUNTRY-CODE/0/#");
        $profile_values['address']['S']['zipcode']      = func_array_path($parsed, $type."/BUYER-SHIPPING-ADDRESS/POSTAL-CODE/0/#");
        $profile_values['address']['S']['phone']        = func_array_path($parsed, $type."/BUYER-SHIPPING-ADDRESS/PHONE/0/#");
        $profile_values['address']['S']['fax']          = func_array_path($parsed, $type."/BUYER-SHIPPING-ADDRESS/FAX/0/#");

        // State code correction: for non-US countries GC returns state full name instead of state code

        $_b_state_code = func_query_first_cell("SELECT code FROM $sql_tbl[states] WHERE country_code='".addslashes($profile_values['address']['B']['country'])."' AND state='".addslashes($profile_values['address']['B']['state'])."'");
        if (!empty($_b_state_code))
            $profile_values['address']['B']['state'] = $_b_state_code;

        $_s_state_code = func_query_first_cell("SELECT code FROM $sql_tbl[states] WHERE country_code='".addslashes($profile_values['address']['S']['country'])."' AND state='".addslashes($profile_values['address']['S']['state'])."'");
        if (!empty($_s_state_code))
            $profile_values['address']['S']['state'] = $_s_state_code;

        x_session_register('login');
        x_session_register('login_type');
        x_session_register('logged_userid');

        if (empty($login) || $login_type != 'C') {

            // Fill anonymous profile
            $profile_values['usertype'] = 'C';

            x_session_register('anonymous_userinfo', array());
            $anonymous_userinfo = func_array_merge($profile_values, $ship_to_details);

            func_gcheckout_debug("\t+ Anonymous profile created");

        } else {

            // Update the existing user profile
            $query_data = $profile_values;
            func_unset($query_data, 'address');

            $cart['used_s_address'] = $profile_values['address']['S'];
            $cart['used_b_address'] = $profile_values['address']['B'];

            func_unset($query_data, 'address', 'email');
            func_array2update('customers', $query_data, "id = '$logged_userid' AND usertype = 'C'");
            
            func_gcheckout_debug("\t+ Customer profile updated - login: '$login'");

            x_load('user');

            // Fill address book
            if (func_is_address_book_empty($logged_userid)) {
                foreach ($profile_values['address'] as $addr_type => $val) {
                    $val['address'] = $val['address'] . "\n" . $val['address_2'];
                    func_unset($val, 'address_2');
                    $val['default_' . strtolower($a_type)] = 'Y';
                    func_save_address($logged_userid, 0, $val);
            
                    func_gcheckout_debug("\t+ New address has been is added to $login\'s address book");
                }
            }
        }

        // Update current user info to place it into the order
        $userinfo = $cart['userinfo'] = func_array_merge($cart['userinfo'], $profile_values);
        if (!empty($login)) {
            $userinfo['login'] = $login;
            $userinfo['id'] = $logged_userid;
        }

        // Get shipping from Google order
        $shipping_name = func_array_path($parsed,
            ($config['Google_Checkout']['gcheckout_use_gc_shipping'] == 'Y') ?
                $type."/ORDER-ADJUSTMENT/SHIPPING/CARRIER-CALCULATED-SHIPPING-ADJUSTMENT/SHIPPING-NAME/0/#" :
                $type."/ORDER-ADJUSTMENT/SHIPPING/MERCHANT-CALCULATED-SHIPPING-ADJUSTMENT/SHIPPING-NAME/0/#");

        if (!empty($shipping_name)) {

            if ($config['Google_Checkout']['gcheckout_use_gc_shipping'] != 'Y') {
                // Get the shipping methods list with rates
                $_allowed_shipping_methods = func_get_shipping_methods_list($cart, $products, $cart['userinfo'], ($merchant_calculation_successful == 'false' ? true : false));

                // Generate the full list of shipping methods for current address
                $_shippingid = 0;
                if (!empty($_allowed_shipping_methods)) {
                    foreach ($_allowed_shipping_methods as $_ship_method) {
                        if (func_insert_trademark($_ship_method['shipping'], 'use_alt') == $shipping_name) {
                            $_shippingid = $_ship_method['shippingid'];
                            break;
                        }
                    }
                }
            } else {
                $_shippingid = func_gcheckout_get_shippingid($shipping_name);
            }

            $cart['shippingid'] = $_shippingid;
            if ($merchant_calculation_successful == 'false' && $config["Google_Checkout"]["gcheckout_use_gc_shipping"] != "Y") {
                // Use fixed shipping cost if merchant calculations failed
                $cart['use_shipping_cost_alt'] = 'Y';
                $cart['shipping_cost_alt'] = $config['Google_Checkout']['gcheckout_default_shipping_cost'];
            }
            else {
                $cart['shipping_cost'] = func_array_path($parsed,
                    ($config['Google_Checkout']['gcheckout_use_gc_shipping'] == 'Y') ?
                        $type."/ORDER-ADJUSTMENT/SHIPPING/CARRIER-CALCULATED-SHIPPING-ADJUSTMENT/SHIPPING-COST/0/#" :
                        $type."/ORDER-ADJUSTMENT/SHIPPING/MERCHANT-CALCULATED-SHIPPING-ADJUSTMENT/SHIPPING-COST/0/#");

                if ($config['Google_Checkout']['gcheckout_use_gc_shipping'] == 'Y') {
                    $cart['use_shipping_cost_alt'] = 'Y';
                    $cart['shipping_cost_alt'] = $cart['shipping_cost'];
                    $merchant_calculation_successful = true;
                }

            }

            func_gcheckout_debug("\t+ Shipping selected - #$cart[shippingid]. '$shipping_name': $$cart[shipping_cost]");
        }

        // Recalculate the cart
        $cart = func_calculate($cart, $products, $logged_userid, 'C');

        // Restore $shippingid again because it may be lost after func_calculate()
        $cart['shippingid'] = $_shippingid;

        $email_allowed = func_array_path($parsed, $type."/BUYER-MARKETING-PREFERENCES/EMAIL-ALLOWED/0/#");
        if ($email_allowed == 'true') {
            // TODO: need to subscribe user to newsletter
            // Problem: we may have separated emails specified for billing and shipping addresses
            // Question: which from them need to be subscribed?

            $_emails = $profile_values['email'];
            if ($_emails != $ship_to_details['email'])
                $_emails .= ', ' . $ship_to_details['email'];
            func_gcheckout_debug("\t+ Customer subscribed to newsletter: '$_emails'");

        }

        $_google_order_currency = func_array_path($parsed, $type."/ORDER-TOTAL/0/@/CURRENCY");

        if (!empty($user_tmp['buyer-ids']) && is_array($user_tmp['buyer-ids']))
            $data_for_order_details["Warning! Other BUYER-IDs were used during checkout"] = implode(', ', $user_tmp['buyer-ids']);

        // Prepare data for order details
        $data_for_order_details = array(
            'google-order-number' => $goid,
            'buyer-id' => func_array_path($parsed, $type."/BUYER-ID/0/#"),
            'currency-code' => $_google_order_currency,
            'adjustment-total' => func_array_path($parsed, $type."/ORDER-ADJUSTMENT/ADJUSTMENT-TOTAL/0/#"),
            'total-cost' => $total_cost
        );

        $order_details = '';
        foreach ($data_for_order_details as $k=>$v) {
            $order_details .= "$k: $v\n";
        }

        $extra = array();
        $extra['goid'] = $goid;
        $extra['is_gcheckout'] = true;

        // Place order

        x_session_register('secure_oid');
        x_session_register('secure_oid_cost');
        x_session_register('cart_locked');
        x_session_register ('partner');
        x_session_register ('partner_clickid');
        x_session_register ('adv_campaignid');

        $orderids = func_place_order("Google Checkout" . ($config['Google_Checkout']['gcheckout_test_mode'] == 'Y' ? ' (in test mode)' : ''), "Q", $order_details, '', '', $extra);

        func_gcheckout_debug("\t+ Order placed: $orderids (" . implode(',',$orderids) . ")");

        $cart_locked = false;

        if (empty($orderids)) {
            x_log_flag('log_payment_processing_errors', 'PAYMENTS', "Google checkout payment module: Order has not been created in X-Cart as products in cart are expired.", true);
            x_session_save();

            func_gcheckout_debug("\t+ [Error] Order has not been created in X-Cart as products in cart are expired");

            // Send 'notification-acknowledgment' message
            func_gcheckout_send_notification_acknowledgment();

            exit;
        }

        $secure_oid = $orderids;
        $secure_oid_cost = $cart['total_cost'];

        $oids = implode(',',$secure_oid);

        func_gcheckout_debug("\t+ Order created: - orderid: '$oids'; status: 'Q'; total_cost: '$cart[total_cost]'");

        $send_add_merchant_order_number_xml = true;

        $_curtime = XC_TIME;

        if (empty($merchant_calculation_successful))
            $merchant_calculation_successful = 'N';

        // Create duplicate record in cc_pp3_data table with reference id = google order number id
        // 'trstat' field    - current google order status
        func_array2insert(
            'cc_pp3_data',
            array(
                'ref' => $goid,
                'sessionid' => $XCARTSESSID,
                'param1' => $oids,
                'param3' => $merchant_calculation_successful,
                'param2' => $total_cost,
                'param5' => $skey,
                'trstat' => $financial_os.'|'.$_curtime
            ),
            true
        );

        // Save new status
        func_array2update(
            'cc_pp3_data',
            array(
                'param1' => $goid,
                'param2' => 'Q',
                'param3' => $oids
            ),
            "ref = '$skey' AND sessionid='$XCARTSESSID'"
        );

        // Insert record about new Google Checkout order in 'xcart_gcheckout_orders' table
        if (is_array($orderids)) {
            foreach ($orderids as $_orderid) {
                func_array2update(
                    'gcheckout_orders',
                    array(
                        'orderid' => $_orderid,
                        'total' => $total_cost,
                        'fulfillment_state' => $fulfillment_os . "|" . $_curtime,
                        'financial_state' => $financial_os . "|" . $_curtime,
                        'state_log' => "Fulfillment state: $fulfillment_os|" . $_curtime . "-Financial state: $financial_os|" . $_curtime
                    ),
                    "goid = '$goid'"
                );
            }
        }

        func_gcheckout_debug("\t+ [Update] - ref: '$goid'; financial_os: '$financial_os'; orderids: '$oids'; total_cost: '$total_cost'");

        x_session_save();

    }

##############################################################################

} elseif ($type == 'ORDER-STATE-CHANGE-NOTIFICATION') {

    // order-state-change-notification
    // Step 2: callback from Google (status = CHARGEABLE)
    // Step 5: callback from Google (status = CHARGING)
    // Step 6: callback from Google (status = CHARGED)

    func_gcheckout_debug("\t+ goid: $goid");

    // Check if callback is valid
    func_gcheckout_is_valid_callback($goid);

    $prev_fulfillment_os = func_array_path($parsed, $type."/PREVIOUS-FULFILLMENT-ORDER-STATE/0/#");
    $new_fulfillment_os = func_array_path($parsed, $type."/NEW-FULFILLMENT-ORDER-STATE/0/#");

    $prev_financial_os = func_array_path($parsed, $type."/PREVIOUS-FINANCIAL-ORDER-STATE/0/#");
    $new_financial_os = func_array_path($parsed, $type."/NEW-FINANCIAL-ORDER-STATE/0/#");

    $reason = func_array_path($parsed, $type."/REASON/0/#");

    $_curtime = XC_TIME;

    // Prepare array for updating 'xcart_gcheckout_orders' table
    $gcheckout_orders_update_fields = array();
    $_message4log = array();

    if ($prev_fulfillment_os != $new_fulfillment_os) {
        func_gcheckout_debug("\t+ Fulfillment state changed: $prev_fulfillment_os -> $new_fulfillment_os");
        $gcheckout_orders_update_fields['fulfillment_state'] = $new_fulfillment_os . "|" . $_curtime;
        $_message4log[] = "Fulfillment state changed from $prev_fulfillment_os to $new_fulfillment_os|" . $_curtime;

    }

    if ($prev_financial_os != $new_financial_os) {
        func_gcheckout_debug("\t+ Financial state changed: $prev_financial_os -> $new_financial_os");
        $gcheckout_orders_update_fields['financial_state'] = $new_financial_os . "|" . $_curtime;
        $_message4log[] = "Financial state changed from $prev_financial_os to $new_financial_os|" . $_curtime;
    }

    if (!empty($_message4log)) {
        db_query("UPDATE $sql_tbl[gcheckout_orders] SET state_log = CONCAT(state_log, '-" . implode('-', $_message4log) . (!empty($reason) ? "-Reason: " . str_replace('-', '_', $reason) : "") . "') WHERE goid = '$goid'");
    }

    // Save new status
    func_array2update(
        'gcheckout_orders',
        $gcheckout_orders_update_fields,
        "goid = '$goid'"
    );

    $_pp3_data = func_query_first("SELECT param2, param4, trstat FROM $sql_tbl[cc_pp3_data] WHERE ref = '$goid'");

    if (!empty($_pp3_data)) {

        $total_cost = price_format($_pp3_data['param2']);

        // Save new status
        db_query("UPDATE $sql_tbl[cc_pp3_data] SET trstat=CONCAT(trstat,'-".$new_financial_os."|".$_curtime."') WHERE ref = '$goid'");
        func_gcheckout_debug("\t+ [Update] - ref: '$goid'; prev_financial_os: '$prev_financial_os'; new_financial_os: '$new_financial_os';");

    }

    if ($prev_financial_os != $new_financial_os) {

        if (!in_array($new_financial_os, array('CHARGEABLE', 'CHARGING', 'CHARGED', 'PAYMENT_DECLINED'))) {
            $bill_error = 1;
            $bill_output['code'] = 2;
            $bill_output['billmes'] = "Declined. Transaction status: $new_financial_os; Google order id: $goid; Previous transaction status: $prev_financial_os; Reason: ".func_array_path($parsed, $type."/REASON/0/#");

            $is_exit = true;

            func_gcheckout_debug("\t+ [Error] - new_financial_os: '$new_financial_os'");

        } elseif ($new_financial_os == 'CHARGEABLE' && $_pp3_data['param4'] == 'RISK-INFO-RECEIVED') {

            // Ready for sending 'charge-order' request
            $send_charge_order_xml = true;

        }
    }
    elseif ($prev_fulfillment_os != $new_fulfillment_os) {

        if ($new_fulfillment_os == 'DELIVERED') {
            $bill_output['code'] = 3;
            $bill_output['billmes'] = "Order delivered. Google order id: $goid; Previous transaction status: $prev_fulfillment_os";

            $is_exit = true;

            func_gcheckout_debug("\t+ Order delivered");

        }
        elseif ($new_fulfillment_os == 'WILL_NOT_DELIVER') {
            $bill_error = 1;
            $bill_output['code'] = 2;
            $bill_output['billmes'] = "Order will not deliver. Google order id: $goid; Previous transaction status: $prev_fulfillment_os";

            $is_exit = true;

            func_gcheckout_debug("\t+ Order will not deliver");

        }

    }

##############################################################################

} elseif ($type == 'RISK-INFORMATION-NOTIFICATION') {

    // risk-information-notification
    // Step 3: callback from Google

    func_gcheckout_debug("\t+ goid: $goid");

    // Check if callback is valid
    func_gcheckout_is_valid_callback($goid);

    $_pp3_data = func_query_first("SELECT param2, param3, trstat FROM $sql_tbl[cc_pp3_data] WHERE ref = '$goid'");

    $avs = func_array_path($parsed, $type."/RISK-INFORMATION/AVS-RESPONSE/0/#");
    $cvn = func_array_path($parsed, $type."/RISK-INFORMATION/CVN-RESPONSE/0/#");
    $prot = func_array_path($parsed, $type."/RISK-INFORMATION/ELIGIBLE-FOR-PROTECTION/0/#");
    $bage = func_array_path($parsed, $type."/RISK-INFORMATION/BUYER-ACCOUNT-AGE/0/#");
    $bip = func_array_path($parsed, $type."/RISK-INFORMATION/IP-ADDRESS/0/#");
    $pcc = func_array_path($parsed, $type."/RISK-INFORMATION/PARTIAL-CC-NUMBER/0/#");

    $for_param3 = implode('|', array($avs, $cvn, $prot, $bage, $bip, $pcc, $_pp3_data['param3']));

    $total_cost = price_format($_pp3_data['param2']);

    func_gcheckout_debug("\t+ total_cost: '$total_cost'");

    $_curtime = XC_TIME;

    db_query("UPDATE $sql_tbl[cc_pp3_data] SET param3 = '$for_param3', param4 = 'RISK-INFO-RECEIVED', trstat = CONCAT(trstat, '-RISK INFO|".$_curtime."') WHERE ref = '$goid'");

    // Save new status
    db_query("UPDATE $sql_tbl[gcheckout_orders] SET state_log = CONCAT(state_log, '-RISK INFO received|".$_curtime."') WHERE goid = '$goid'");

    func_gcheckout_debug("\t+ [Update] - ref: '$goid'; avs: '$avs'; cvn: '$cvn'; protection: '$prot', account age: '$bage'; buyer IP: '$bip'; partial CC: '$pcc'");

    if (preg_match('/CHARGEABLE/', $_pp3_data['trstat']) && doubleval($total_cost) > 0) {

        // Ready for sending 'charge-order' request
        $send_charge_order_xml = true;

    }

##############################################################################

} elseif ($type == 'CHARGE-AMOUNT-NOTIFICATION') {

    // charge-amount-notification
    // Step 7: callback from Google and process order

    func_gcheckout_debug("\t+ goid: $goid");

    // Check if callback is valid
    func_gcheckout_is_valid_callback($goid);

    $trstat = func_query_first_cell("SELECT trstat FROM $sql_tbl[cc_pp3_data] WHERE ref = '$goid'");

    $trstats = explode('-', $trstat);
    list($_c_status, $t) = explode('|', $trstats[count($trstats)-1], 2);

    $bill_output['code'] = 1;
    $bill_output['billmes'] = "Accepted. Transaction status: $_c_status; Google order id: $goid";

    $_curtime = XC_TIME;

    db_query("UPDATE $sql_tbl[cc_pp3_data] SET trstat = CONCAT(trstat, '-CONFIRMED|".$_curtime."') WHERE ref = '$goid'");

    // Save new status
    db_query("UPDATE $sql_tbl[gcheckout_orders] SET financial_state = 'CHARGED (CONFIRMED)|$_curtime', state_log = CONCAT(state_log, '-Confirmation of CHARGED state is received|".$_curtime."') WHERE goid = '$goid'");
    $is_exit = true;

##############################################################################

} elseif ($type == 'REFUND-AMOUNT-NOTIFICATION') {

    // refund-amount-notification
    // This callback from Google initiated by refund-order request from admin area

    func_gcheckout_debug("\t+ goid: $goid");

    // Check if callback is valid
    func_gcheckout_is_valid_callback($goid);

    $refund_amount = func_array_path($parsed, $type."/LATEST-REFUND-AMOUNT/0/#");

    func_gcheckout_debug("\t+ Refunded amount: $refund_amount");

    $order_total_cost = func_query_first_cell("SELECT total-refunded_amount FROM $sql_tbl[gcheckout_orders] WHERE goid='$goid'");

    $bill_output['code'] = 1;
    $bill_output['billmes'] = "Refund: {$config['Google_Checkout']['gcheckout_currency']} $refund_amount; Google order id: $goid";

    $_curtime = XC_TIME;

    // Save new status
    db_query("UPDATE $sql_tbl[gcheckout_orders] SET refunded_amount = refunded_amount+$refund_amount, financial_state = 'REFUNDED (".$config['Google_Checkout']['gcheckout_currency']." $refund_amount)|$_curtime', state_log = CONCAT(state_log, '-REFUNDED (".$config['Google_Checkout']['gcheckout_currency']." $refund_amount)|".$_curtime."') WHERE goid = '$goid'");

##############################################################################

} elseif ($type == 'CHARGEBACK-AMOUNT-NOTIFICATION') {

    // chargeback-amount-notification
    // Google Checkout sends this callback when a customer initiates a chargeback against the order
    // and Google approves the chargeback

    func_gcheckout_debug("\t+ goid: $goid");

    // Check if callback is valid
    func_gcheckout_is_valid_callback($goid);

    $chargeback_amount = func_array_path($parsed, $type."/TOTAL-CHARGEBACK-AMOUNT/0/#");

    func_gcheckout_debug("\t+ Chargeback amount: $chargeback_amount");

    $order_total_cost = func_query_first_cell("SELECT total-refunded_amount FROM $sql_tbl[gcheckout_orders] WHERE goid='$goid'");

    if ($chargeback_amount == $order_total_cost) {
        $bill_error = 1;
        $is_exit = true;
    }

    $bill_output['code'] = 1;
    $bill_output['billmes'] = "Chargeback: {$config['Google_Checkout']['gcheckout_currency']} $chargeback_amount; Google order id: $goid";

    $_curtime = XC_TIME;

    // Save new status
    db_query("UPDATE $sql_tbl[gcheckout_orders] SET refunded_amount = refunded_amount+$chargeback_amount, financial_state = 'CHARGEBACK (".$config['Google_Checkout']['gcheckout_currency']." $chargeback_amount)|$_curtime', state_log = CONCAT(state_log, '-CHARGEBACK (".$config['Google_Checkout']['gcheckout_currency']." $chargeback_amount)|".$_curtime."') WHERE goid = '$goid'");
}

func_gcheckout_debug("\t+ Sending message: notification-acknowledgment");

##############################################################################

/**
 * Send 'notification-acknowledgment' message
 */

func_gcheckout_send_notification_acknowledgment();

##############################################################################

if ($send_add_merchant_order_number_xml) {

    // Send 'add-merchant-order-number' request

    $merchant_orderids = func_google_encode($oids);

    func_gcheckout_debug("\t+ Sending message: add-merchant-order-number ($oids)");

    $_xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>

<add-merchant-order-number xmlns="http://checkout.google.com/schema/2" google-order-number="$goid">
        <merchant-order-number>$merchant_orderids</merchant-order-number>
</add-merchant-order-number>
XML;

    $parsed = func_gcheckout_send_xml($_xml);

    if (empty($parsed)) {
        $bill_error = 1;
        $bill_output['billmes'] = "Error: Empty server response";

    } elseif ($error = func_array_path($parsed, "ERROR/ERROR-MESSAGE/0/#")) {
        $bill_error = 1;
        $bill_output['billmes'] = "Error: ".$error;
    }

    if ($bill_error)
        func_gcheckout_debug("\t+ Error: " . $bill_output['billmes']);
    else
        func_gcheckout_debug("\t+ Message has been successfully sent");

}

##############################################################################

if ($send_charge_order_xml && $config['Google_Checkout']['gcheckout_charge_all_manually'] != 'Y') {

    // Send 'charge-order' request

    // Check if order can be charged according to the specified conditions
    $_pp3_data = func_query_first_cell("SELECT param3 FROM $sql_tbl[cc_pp3_data] WHERE ref = '$goid'");
    $_risk_info_data = explode('|', $_pp3_data);

    $_avs_error = (strstr($config['Google_Checkout']['gcheckout_check_avs'], $_risk_info_data[0]) === false);
    $_cvn_error = (strstr($config['Google_Checkout']['gcheckout_check_cvn'], $_risk_info_data[1]) === false);
    $_prot_error = ($config['Google_Checkout']['gcheckout_check_prot'] == 'Y' && $_risk_info_data[2] != 'true');
    $_mc_error = ($config['Google_Checkout']['gcheckout_merchant_calc'] == 'Y' && $_risk_info_data[6] == 'false');

    if (!$_avs_error && !$_cvn_error && !$_prot_error && !$_mc_error) {

        // Prepare for sending XML

        func_gcheckout_debug("\t+ Sending message: charge-order (total cost: $total_cost)");

        $charge_order_xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<charge-order xmlns="http://checkout.google.com/schema/2" google-order-number="$goid">
    <amount currency="{$config['Google_Checkout']['gcheckout_currency']}">{$total_cost}</amount>
</charge-order>
XML;

        $parsed = func_gcheckout_send_xml($charge_order_xml);

        if (empty($parsed)) {
            $bill_error = 1;
            $bill_output['code'] = 2;
            $bill_output['billmes'] = "Error: Empty server response";

            $is_exit = true;

        } elseif ($error = func_array_path($parsed, "ERROR/ERROR-MESSAGE/0/#")) {
            $bill_error = 1;
            $bill_output['code'] = 2;
            $bill_output['billmes'] = "Error: ".$error;

            $is_exit = true;

        }

    } else {

        $bill_output['code'] = 2;

        $_check_result = array();
        if ($_avs_error)
            $_check_result[] = 'AVS';

        if ($_cvn_error)
            $_check_result[] = 'CVN';

        if ($_prot_error)
            $_check_result[] = "Eligible-protection";

        if ($_mc_error) {
            $_check_result[] = "Merchant calculation failed";
            $bill_error = 1;
            $bill_output['billmes'] = "Order cancelled. Reason: Merchant calculation failed; Google order id: $goid";
        }
        else
            $bill_output['billmes'] = "Not charged. Reason: Risk information failed checking: ".implode(", ", $_check_result).". Transaction status: $_c_status; Google order id: $goid";

        $is_exit = true;

    }

}

##############################################################################

if ($is_exit) {

    x_load('order');

    func_gcheckout_debug("\t+ $bill_output[billmes]");

    func_gcheckout_debug("\t+ Process order");

    // Get the transaction status
    $_pp3_data = func_query_first("SELECT * FROM $sql_tbl[cc_pp3_data] WHERE ref = '$goid'");

    if (!empty($_pp3_data)) {
        // Process transaction initiated by customer checkout
        $sessid = $_pp3_data['sessionid'];
        $orderids = $_pp3_data['param1'];
        $total_cost = price_format($_pp3_data['param2']);
        $risk_info = (!empty($_pp3_data['param3']) ? explode('|', $_pp3_data['param3']) : false);
        $trstat = $_pp3_data['trstat'];
        $skey = $_pp3_data['param5'];
        $trstat_arr = (!empty($trstat) ? explode('-', $trstat) : false);

    }
    else {
        // Process deferred transaction
        $orderids_arr = func_query_column("SELECT orderid FROM $sql_tbl[gcheckout_orders] WHERE goid='$goid'");

        $orderids = implode(',', $orderids_arr);

        func_gcheckout_debug("\t+ Orders for updating: $orderids");

    }

    if (empty($orderids)) {

        func_gcheckout_debug("\t+ [Error] Internal order has been lost (goid: $goid)");

        if (!empty($goid)) {
            // Send 'cancel-order' request for Google Checkout order
            func_gcheckout_debug("\t+ Sending message: cancel-order");

            $_xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<cancel-order xmlns="http://checkout.google.com/schema/2" google-order-number="$goid">
    <reason>Internal order has been lost</reason>
    <comment>Please, contact the Orders department {$config['Company']['orders_department']} for details.</comment>
</cancel-order>
XML;
            func_gcheckout_send_xml($_xml);

        }

        exit;

    }

    if ($_mc_error) {

        func_gcheckout_debug("\t+ [Error] Merchant calculations failed (goid: $goid)");

        if (!empty($goid)) {
            // Send 'cancel-order' request for Google Checkout order
            func_gcheckout_debug("\t+ Sending message: cancel-order");

            $_xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<cancel-order xmlns="http://checkout.google.com/schema/2" google-order-number="$goid">
    <reason>Merchant calculations failed</reason>
    <comment>Please, contact the Orders department {$config['Company']['orders_department']} for details.</comment>
</cancel-order>
XML;
            func_gcheckout_send_xml($_xml);

        }

    }

    // Prepare an additional info
    $advinfo = array();
    $advinfo[] = "Reason: " . $bill_output['billmes'];

    if (!empty($risk_info)) {
        $advinfo[] = "AVS checking result: ".$risk_info[0]." (".($avs_info[$risk_info[0]] ? $avs_info[$risk_info[0]] : 'unknown').")";
        $advinfo[] = "CVV checking result: ".$risk_info[1]." (".($cvv_info[$risk_info[1]] ? $cvv_info[$risk_info[1]] : 'unknown').")";
        $advinfo[] = "Eligible for protection: " . $risk_info[2];
        $advinfo[] = "Buyer account age (days): " . $risk_info[3];
        $advinfo[] = "Buyer IP: " . $risk_info[4];
        $advinfo[] = "Partial CC number: " . $risk_info[5];
        $advinfo[] = "Merchant calculations: " . $risk_info[6];

    }

    if (!empty($trstat_arr) && is_array($trstat_arr)) {
        $advinfo[] = "Google order status log:";
        foreach ($trstat_arr as $_trstat) {
            list($_stat, $_stat_date) = explode('|', $_trstat);
            if (empty($_stat)) continue;
            $advinfo[] = "- [" . date("Y/m/d H:i:s", $_stat_date) . "]: " . $_stat;
        }
    }

    // Calculate the order status
    $order_status = ($bill_error) ? 'F' : (($bill_output['code'] == 1) ? 'P' : (($bill_output['code'] == 3) ? 'C' : 'Q'));

    func_gcheckout_debug("\t+ Updating status for order [$orderids] to '$order_status'");

    // Change order status and save additional info
    define('STATUS_CHANGE_REF', 10);
    func_change_order_status(explode(',', $orderids), $order_status, implode("\n", $advinfo));
    $_orderids = func_get_urlencoded_orderids ($orderids);

    // Clear transaction log to avoid duplication of it during deferred transaction
    func_array2update(
        'cc_pp3_data',
        array(
            'param3' => '',
            'trstat' => ''
        ),
        "ref = '$goid'"
    );

    // Update the first record with new order status
    func_array2update(
        'cc_pp3_data',
        array(
            'param2' => $order_status,
            'param4' => addslashes($bill_output['billmes'])
        ),
        "ref = '$skey' AND param1 = '$goid'"
    );

    if ($order_status == 'P') {
        // Delete manual-created record in cc_pp3_data table
        db_query("DELETE FROM $sql_tbl[cc_pp3_data] WHERE ref = '$goid'");
    }

}

exit;

?>
