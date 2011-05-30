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
 * Google checkout
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: checkout.php,v 1.11.2.2 2011/01/10 13:11:57 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

set_time_limit(86400);

x_session_register('cart_locked');

define('ALL_CARRIERS', 1);

if (defined('CHECKOUT_STARTED')) {
// Start the Google checkout...

    if ($func_is_cart_empty)
        return;

    $_index = 0;
    while (true) {
        $_index++;
        $unique_id = md5(uniqid(rand()));
        @db_query("INSERT INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('$unique_id','".$XCARTSESSID."','GO|')");
        if (db_affected_rows() > 0)
            break;
        if ($_index > 10) // Impossible error: just to avoid the potential infinite loop
            die(func_get_langvar_by_name('txt_gcheckout_impossible_error', '', false, true));
    }

    // Get the available taxes list

    $_taxes = func_gcheckout_get_taxes($cart);

    if (!empty($_taxes)) {

        $_default_tax = '';
        $_alter_tax = '';

        foreach ($_taxes as $_tax_name => $_tax) {

            if (!preg_match("/(DST|SH)/", $_tax['formula']))
                continue;

            $_tax_rate_xml = '';
            $_def_tax_rate_xml = '';

            foreach ($_tax['rates'] as $_rate) {

                $_rate_area = '';
                $_zone_rate = $_rate['zone'];
                if ($_rate['zoneid'] == 0) {
                    // Default zone
                    $_rate_area = "<us-country-area country-area=\"ALL\"/>";

                } else {
                    // Non-US countries are not allowed
                    if (!empty($_zone_rate['C']) && !in_array('US', $_zone_rate['C']))
                        continue;

                    if (!empty($_zone_rate['Z'])) {
                        // Zipcode masks
                        $_zipcode_area = '';
                        foreach ($_zone_rate['Z'] as $_zip_)
                            $_zipcode_area .= "\t\t\t\t\t\t\t\t\t\t<zip-pattern>" . str_replace('%', '*', $_zip_) . "</zip-pattern>";
                        $_rate_area .= "\t\t<us-zip-area>\n$_zipcode_area\n\t\t\t\t\t\t\t\t\t</us-zip-area>";
                    }

                    elseif (!empty($_zone_rate['S'])) {
                        // Area restricted by states

                        if (count($_zone_rate['S']) == 50) {
                            // All US states
                            $_rate_area = "<us-country-area country-area=\"FULL_50_STATES\"/>\n";

                        } elseif (count($_zone_rate['S']) == 48 && !in_array('US_AK', $_zone_rate['S']) && !in_array('US_HI', $_zone_rate['S'])) {
                            // All US continental states
                            $_rate_area = "<us-country-area country-area=\"CONTINENTAL_48\"/>\n";

                        } else {
                            // Specific US states
                            $_state_codes = '';
                            foreach ($_zone_rate['S'] as $_state_code) {

                                if (!preg_match("/^US_[A-Z]{2}/", $_state_code))
                                    continue;

                                $_state_codes .= "\t\t\t\t\t\t\t\t\t\t<state>" . str_replace('US_', '', $_state_code) . "</state>\n";
                            }
                            if (!empty($_state_codes))
                                $_rate_area .= "<us-state-area>\n$_state_codes\t\t\t\t\t\t\t\t\t</us-state-area>\n";
                        }
                    }

                    else {
                        // Entire US
                        $_rate_area = "<us-country-area country-area=\"ALL\"/>";
                    }

                }

                $_rate_value = sprintf("%.4f", $_rate['rate_value'] * 0.01);

                if (preg_match('/SH/', $_tax['formula'])) {
                    // Default tax rules
                    $_def_tax_rate_xml .=<<<OUT
<default-tax-rule>
                        <shipping-taxed>true</shipping-taxed>
                        <rate>$_rate_value</rate>
                        <tax-area>
                            $_rate_area
                        </tax-area>
                    </default-tax-rule>

OUT;
                }

                if (preg_match('/DST/', $_tax['formula'])) {
                    // Alternative tax rules
                    $_alt_tax_rate_xml .=<<<OUT
                            <alternate-tax-rule>
                                <rate>$_rate_value</rate>
                                <tax-area>
                            $_rate_area
                                </tax-area>
                            </alternate-tax-rule>

OUT;
                }

            } // foreach ($_tax['rates']...

            if (!empty($_def_tax_rate_xml)) {
                $_default_tax =<<<OUT
                <default-tax-table>
                    <tax-rules>
                        $_def_tax_rate_xml
                    </tax-rules>
                </default-tax-table>
OUT;
            }

            if (!empty($_alt_tax_rate_xml)) {
                $_tax_name = func_google_encode($_tax_name);
                $_alter_tax =<<<OUT
                    <alternate-tax-table standalone="true" name="$_tax_name">
                        <alternate-tax-rules>
$_alt_tax_rate_xml
                        </alternate-tax-rules>
                    </alternate-tax-table>
OUT;
            }

        } // foreach ($_taxes...

        if (!empty($_alter_tax))
            $_alter_tax_tables = "\t\t\t\t<alternate-tax-tables>\n$_alter_tax\n\t\t\t\t</alternate-tax-tables>";

        $tax_tables = <<<OUT
<tax-tables merchant-calculated="true">
$_default_tax
$_alter_tax_tables
            </tax-tables>
OUT;

    } // if (!empty($_taxes))

    $items = array();

    if (!empty($cart['products'])) {

        // Generate products list

        foreach ($cart['products'] as $_product) {

            $_descr = '';

            if (!empty($_product['product_options']) && is_array($_product['product_options'])) {
                $_descr_arr = array();
                foreach ($_product['product_options'] as $k=>$v) {
                    $_descr_arr[] = "$v[class]: $v[option_name]";
                }
                $_descr = "(" . implode('; ', $_descr_arr) . ")";
            }

            $_descr .= " " . strip_tags(func_query_first_cell("SELECT IF($sql_tbl[products_lng].descr != '', $sql_tbl[products_lng].descr, $sql_tbl[products].descr) AS descr FROM $sql_tbl[products] LEFT JOIN $sql_tbl[products_lng] ON $sql_tbl[products].productid = $sql_tbl[products_lng].productid AND $sql_tbl[products_lng].code = '".$shop_language."' WHERE $sql_tbl[products].productid='$_product[productid]'"));

            $length = 160;
            if (strlen($_descr) > $length) {
                $_descr = substr($_descr, 0, $length);
            }

            $_title = func_google_encode($_product['product']);
            $_descr = func_google_encode($_descr);

            if (!empty($_product['taxes'])) {
                $_tax_name = current($_product['taxes']);
                $_tax_name = func_google_encode($_tax_name['tax_name'].(!$single_mode ? '_' . $_product['provider'] : ''));
                $_taxable_selector = "<tax-table-selector>$_tax_name</tax-table-selector>\n";
            }
            else
                $_taxable_selector = '';

            $weight = func_units_convert(func_weight_in_grams($_product['weight']), 'g', 'lbs', 4);

            $items[] = <<<ITEM
                <item-name>$_title</item-name>
                <item-description>$_descr</item-description>
                <unit-price currency="{$config['Google_Checkout']['gcheckout_currency']}">{$_product['display_price']}</unit-price>
                <quantity>{$_product['amount']}</quantity>
                <item-weight unit="LB" value="{$weight}" />
                $_taxable_selector
ITEM;
        }

    }

    if (!empty($cart['giftcerts'])) {

        // Generate gift certificates list

        foreach ($cart['giftcerts'] as $_giftcert) {

            $_descr = func_google_encode(func_get_langvar_by_name('lbl_recipient', '', false, true) . ': ' . $_giftcert['recipient']);
            $_title = func_google_encode(func_get_langvar_by_name('lbl_gift_certificate', '', false, true));
            $items[] = <<<ITEM
                <item-name>$_title</item-name>
                <item-description>$_descr</item-description>
                <unit-price currency="{$config['Google_Checkout']['gcheckout_currency']}">{$_giftcert['amount']}</unit-price>
                <quantity>1</quantity>\n
ITEM;
        }

    }

    if (doubleval($cart['discount']) > 0) {

        // Add a discount as item with negative price

        $_title = func_google_encode(func_get_langvar_by_name('lbl_gcheckout_item_discount', '', false, true));
        $items[] = <<<ITEM
                <item-name>$_title</item-name>
                <item-description></item-description>
                <unit-price currency="{$config['Google_Checkout']['gcheckout_currency']}">-{$cart['discount']}</unit-price>
                <quantity>1</quantity>\n
ITEM;

    }

    $items = "<item>\n".implode("\t\t\t</item>\n\t\t\t<item>\n", $items)."\t\t\t</item>";

    $merchant_calculations = array();

    // Use discount coupons
    if (!empty($active_modules['Discount_Coupons']))
        $merchant_calculations[] = "\t\t\t\t<accept-merchant-coupons>true</accept-merchant-coupons>";
    else
        $merchant_calculations[] = "\t\t\t\t<accept-merchant-coupons>false</accept-merchant-coupons>";

    // Use Gift certificates
    if (
        !empty($active_modules['Gift_Certificates']) &&
        func_query_first_cell("SELECT gcid FROM $sql_tbl[giftcerts] WHERE status='A'") > 0 &&
        func_query_first_cell("SELECT paymentid FROM $sql_tbl[payment_methods] WHERE payment_script='payment_giftcert.php' AND active='Y'") > 0
    )
        $merchant_calculations[] = "\t\t\t\t<accept-gift-certificates>true</accept-gift-certificates>";
    else
        $merchant_calculations[] = "\t\t\t\t<accept-gift-certificates>false</accept-gift-certificates>";

    $merchant_calculations = implode("\n", $merchant_calculations);

    $script_location = ($config['Google_Checkout']['gcheckout_test_mode'] == 'N' ? $https_location : $current_location);

    $merchant_calculations_xml = <<<OUT
<merchant-calculations>
                <merchant-calculations-url>$script_location/payment/ps_gcheckout.php</merchant-calculations-url>
$merchant_calculations
            </merchant-calculations>
OUT;

    $shipping_xml = '';

    $_need_shipping = false;

    if ($config['Shipping']['enable_shipping'] == 'Y') {
        foreach ($cart['products'] as $_prd) {
            if ($active_modules['Egoods'] && $_prd['distribution'] != '')
                continue;
            if ($_prd['free_shipping'] == 'Y' && $config['Shipping']['do_not_require_shipping'] == 'Y')
                continue;
            $_need_shipping = true;
            break;
        }
    }

    if (!$single_mode)
        $number_of_providers = count(func_get_products_providers($cart['products']));
    else
        $number_of_providers = 1;

    $default_shipping_rate = price_format($config['Google_Checkout']['gcheckout_default_shipping_cost']) * $number_of_providers;

    if ($_need_shipping && $config['Google_Checkout']['gcheckout_use_gc_shipping'] != 'Y') {

        // Some options require adjustment
        $config['Shipping']['enable_all_shippings'] = 'N';
        $config['Shipping']['realtime_shipping'] = 'N';

        // Get list of all shipping methods that are potentially available for customers
        $shipping_methods = func_get_shipping_methods_list($cart, $cart['products'], $cart['userinfo'], true);

        if (!empty($shipping_methods)) {

            foreach ($shipping_methods as $_ship_method) {

                $_ship_method['shipping'] = func_google_encode(func_insert_trademark($_ship_method['shipping'], 'use_alt'));
                $shipping_xml .= <<<OUT
                <merchant-calculated-shipping name="{$_ship_method['shipping']}">
                    <price currency="{$config['Google_Checkout']['gcheckout_currency']}">{$default_shipping_rate}</price>
                    <address-filters>
                        <allowed-areas>
                            <world-area />
                        </allowed-areas>
                    </address-filters>
                </merchant-calculated-shipping>

OUT;
            }

            $shipping_xml = "<shipping-methods>\n$shipping_xml\n\t\t\t</shipping-methods>\n";

        }
    } else {
        $shipping_methods = func_gcheckout_get_shipping();

        if (!empty($shipping_methods)) {
            $shipping_xml = <<<OUT
      <shipping-methods>
        <carrier-calculated-shipping>
          <carrier-calculated-shipping-options>
OUT;
            $shipping_options = '';

            if (!empty($config['Google_Checkout']['gcheckout_fixed_charge'])) {
                $shipping_options .= "<additional-fixed-charge currency=\"".$config['Google_Checkout']['gcheckout_currency']."\">".$config["Google_Checkout"]["gcheckout_fixed_charge"]."</additional-fixed-charge>";
            }

            if (!empty($config['Google_Checkout']['gcheckout_variable_charge'])) {
                $shipping_options .= "<additional-variable-charge-percent>".$config['Google_Checkout']['gcheckout_variable_charge']."</additional-variable-charge-percent>";
            }

            if (!empty($config['Google_Checkout']['gcheckout_pickup'])) {
                $shipping_options .= "<carrier-pickup>".$config['Google_Checkout']['gcheckout_pickup']."</carrier-pickup>";
            }

            $pack = array();

            if (!empty($config['Google_Checkout']['gcheckout_package_type']))
            switch($config['Google_Checkout']['gcheckout_package_type']) {
                case 'use_packaging':
                    x_load('pack');
                    $items_for_packing = func_prepare_items_list($cart['products'], true);
                    $package = func_get_packages($items_for_packing, array('weight'=>150));
                    if ($package !== -1) {
                        $pack = array(
                            'length' => $package[0]['length'],
                            'width' => $package[0]['width'],
                            'height' => $package[0]['height']
                        );
                    }
                    break;
                case 'use_dimensions':
                    $pack = array(
                        'length' => $config['Google_Checkout']['gcheckout_length'],
                        'width' => $config['Google_Checkout']['gcheckout_width'],
                        'height' => $config['Google_Checkout']['gcheckout_height']
                    );
            }

            $shipping_package_xml = '';
            if (!empty($pack)) {
                foreach ($pack as $attr=>$val) {
                    $shipping_package_xml .= "<$attr unit=\"IN\" value=\"$val\" />";
                }
            }

            foreach ($shipping_methods as $shipping) {
                $shipping_xml .= <<<OUT
            <carrier-calculated-shipping-option>
              <price currency="{$config['Google_Checkout']['gcheckout_currency']}">{$default_shipping_rate}</price>
              <shipping-company>{$shipping['code']}</shipping-company>
              <shipping-type>{$shipping['gc_shipping']}</shipping-type>
                {$shipping_options}
            </carrier-calculated-shipping-option>
OUT;
            }
            $shipping_xml .= <<<OUT
          </carrier-calculated-shipping-options>
          <shipping-packages>
            <shipping-package>
                {$shipping_package_xml}
              <ship-from id="ABC">
                <city>{$config['Company']['location_city']}</city>
                <region>{$config['Company']['location_state']}</region>
                <country-code>{$config['Company']['location_country']}</country-code>
                <postal-code>{$config['Company']['location_zipcode']}</postal-code>
              </ship-from>
            </shipping-package>
          </shipping-packages>
        </carrier-calculated-shipping>
      </shipping-methods>
OUT;
        }
    }

    $analyticsdata = isset($analyticsdata) ? '<analytics-data>' . $analyticsdata . '</analytics-data>' : '';

    $cart_xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<checkout-shopping-cart xmlns="http://checkout.google.com/schema/2">
    <shopping-cart>
        <merchant-private-data>
            <merchant-note>$unique_id</merchant-note>
        </merchant-private-data>
        <items>
            $items
        </items>
    </shopping-cart>
    <checkout-flow-support>
        <merchant-checkout-flow-support>
            <platform-id>429557754556845</platform-id>
            <request-buyer-phone-number>true</request-buyer-phone-number>
            $shipping_xml
            $merchant_calculations_xml
            <edit-cart-url>$xcart_catalogs[customer]/cart.php</edit-cart-url>
            <continue-shopping-url>$current_location/payment/ps_gcheckout_return.php?mode=continue&#x26;skey=$unique_id</continue-shopping-url>
            $tax_tables
            $analyticsdata
        </merchant-checkout-flow-support>
    </checkout-flow-support>
</checkout-shopping-cart>
XML;

    $cart_xml = trim($cart_xml);

    $parsed = func_gcheckout_send_xml($cart_xml);

    $redirect_url = func_array_path($parsed, "CHECKOUT-REDIRECT/REDIRECT-URL/0/#");

    if ($redirect_url) {
        // when PHP5 is used with libxml 2.7.1, HTML entities are stripped from any XML content
        // this is a workaround for https://qa.mandriva.com/show_bug.cgi?id=43486
        if (strpos($redirect_url, 'shoppingcartshoppingcart') !== false) {
            $redirect_url = str_replace('shoppingcartshoppingcart', "shoppingcart&shoppingcart", $redirect_url);
        }

        // Lock cart for all operations
        $cart_locked = true;

        // these addresses will be saved in the order
        x_session_register('gcheckout_saved_ips');
        $gcheckout_saved_ips = array('ip' => $CLIENT_IP, 'proxy_ip' => $PROXY_IP);
        x_session_save();

        // Redirect customer to the Google checkout
        func_header_location($redirect_url);

    } else {
        $error = func_array_path($parsed, "ERROR/ERROR-MESSAGE/0/#");
        x_log_flag('log_payment_processing_errors', 'PAYMENTS', "Google checkout payment module: Checkout cannot be started as it is impossible to redirect to the Google Checkout server ($error).", true);
        $top_message['content'] = func_get_langvar_by_name('txt_gcheckout_error_redirect', false, false, true);
        $top_message['type'] = 'E';
        func_header_location('cart.php');
    }

}
elseif (defined('IS_STANDALONE')) {

    if (defined('GCHECKOUT_DEBUG') && $gcheckout_log_detailed_data) {
        // Save received data to the unique log file
        $filename = $var_dirs['log'] . "/gcheckout-" . date("Ymd-His") . "-" . uniqid(rand()) . '.log.php';
        if ($fd = @fopen($filename, "a+")) {

            $str[] = "PROXY_IP: $PROXY_IP";
            $str[] = "CLIENT_IP: $CLIENT_IP";

            ob_start();
            echo "\n_GET:\n";
            print_r($_GET);
            echo "\n_POST:\n";
            print_r($_POST);
            echo "\nHTTP_RAW_POST_DATA:\n";
            print_r($HTTP_RAW_POST_DATA);
            $str[] = ob_get_contents();
            ob_end_clean();
            fwrite($fd, "<?php die(); ?>\n\n" . implode("\n\n", $str));
            fclose($fd);
            func_chmod_file($filename);
        }
    }

    if ($mode == 'continue') {

        // Customer returned back to X-Cart

        if (empty($active_modules['Google_Checkout']))
            func_header_location($xcart_catalogs['customer']."/cart.php");

        func_gcheckout_debug("\t+ Customer returned back to the shop");

        if (!empty($skey)) {
            $ret = func_query_first("SELECT * FROM $sql_tbl[cc_pp3_data] WHERE ref='$skey'");

            $order_status = $ret['param2'];
            $_orderids = $ret['param3'];

            x_session_register('gcheckout_jump_counter', 0);

            if ((empty($order_status) || empty($_orderids)) && ++$gcheckout_jump_counter < 10) {
                // There are no orders found
                $smarty->assign('time', 3);
                $smarty->assign('url', $current_location."/payment/ps_gcheckout_return.php?mode=continue&amp;skey=$skey");
                x_session_save();
                func_display('modules/Google_Checkout/waiting.tpl', $smarty);
                exit;
            }

            $gcheckout_jump_counter = 0;

            x_session_register('cart');

            db_query("DELETE FROM $sql_tbl[cc_pp3_data] WHERE ref='$skey'");

            $cart_locked = false;

            if (empty($order_status) || $order_status == 'F') {
                $bill_error="error_ccprocessor_error";
                $reason = "&bill_message=".urlencode($ret['param4']);
                $redirect_url = $current_location.DIR_CUSTOMER."/error_message.php?error=".$bill_error.$reason;
            }
            else {
                $cart = '';
                $redirect_url = $xcart_catalogs['customer']."/cart.php?mode=order_message&orderids=$_orderids";

                if (!empty($active_modules['SnS_connector']))
                    func_generate_sns_action('CartChanged');

            }

            func_gcheckout_debug("\t+ Redirect to: $redirect_url");

            func_header_location($redirect_url);

        }
        else
            func_header_location($xcart_catalogs['customer']."/cart.php");

        exit;
    }

    include $xcart_dir . '/modules/Google_Checkout/gcheckout_callback.php';

}

exit;

?>
