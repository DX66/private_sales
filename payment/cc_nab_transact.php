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
 * "NAB Transact - Hosted Payment Page" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_nab_transact.php,v 1.17.2.1 2011/01/10 13:12:06 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/**
 * NAB Transact (Hosted Payments Page) payment module
 */

define('QS_PARAM_NAME_BANK_REF', 'bank_reference');
define('QS_PARAM_NAME_CARD_TYPE', 'card_type');
define('QS_PARAM_NAME_AMNT_PAID', 'payment_amount');
define('QS_PARAM_NAME_REF_NUM', 'payment_number');
define('QS_PARAM_NAME_CUST_IP', 'remote_ip');
define('QS_PARAM_NAME_ORDER_ID', 'oid');

if (!isset($REQUEST_METHOD))
    $REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];

if ($REQUEST_METHOD == 'POST' && isset($_GET[QS_PARAM_NAME_BANK_REF])) {

    // This section is executed when the the reply_link_url (server-to-server silent call)
    // is called.
    // Write the payment details to the order and update the order status

    require './auth.php';

    if (!func_is_active_payment('cc_nab_transact.php')){
        exit;
    }

    if(!isset($_GET[QS_PARAM_NAME_AMNT_PAID])){
        exit;
    }

    $payment_return['total'] = $_GET[QS_PARAM_NAME_AMNT_PAID];
    $skey = $_GET[QS_PARAM_NAME_ORDER_ID];

    // If this function is being called that means the payment has gone through
    $bill_output['code'] = 1;
    $bill_output['sessid'] = $_GET[$XCART_SESSION_NAME];
    $bill_output['billmes'] = "NAB Transact Ref Number: ".$skey.":".$_GET[payment_number].chr(10);
    $bill_output['billmes'] .= "Bank Txn Id: ".$_GET[QS_PARAM_NAME_BANK_REF].chr(10);
    $bill_output['billmes'] .= "Card Type: ".$_GET[QS_PARAM_NAME_CARD_TYPE].chr(10);
    $bill_output['billmes'] .= "Payment Amount: ".$payment_return['total'].chr(10);

    require($xcart_dir.'/payment/payment_ccmid.php');
    require($xcart_dir.'/payment/payment_ccwebset.php');

} elseif ($REQUEST_METHOD == 'GET' && isset($_GET[QS_PARAM_NAME_ORDER_ID])) {

    // This section is called when the customer clicks the link in the payment receipt and get back to the cart
    // All this section does is redirects the customer to Order Confirmation Page

    require './auth.php';
    if (!func_is_active_payment('cc_nab_transact.php')){
        exit;
    }

    // Empty the cart
    x_session_unregister('cart');
    $skey = $_GET[QS_PARAM_NAME_ORDER_ID];

    require($xcart_dir.'/payment/payment_ccview.php');

} else {

    if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

    if (strtoupper($module_params['testmode']) == 'Y') {
        $gateway_url = $module_params['param08']; // NAB_TRANSACT_TEST_URL;
    } else {
        $gateway_url = $module_params['param09']; // NAB_TRANSACT_LIVE_URL;
    }

    $_orderids = join("-",$secure_oid);

    // Create the variables for NT gateway

    $gateway_vars = array();
    $gateway_vars['vendor_name'] = $module_params["param01"];
    $gateway_vars['return_link_url'] = $xcart_catalogs['customer']."/payment/".basename($module_params["processor"])."?".QS_PARAM_NAME_ORDER_ID."=".$_orderids;
    $gateway_vars['reply_link_url'] = $xcart_catalogs['customer']."/payment/".basename($module_params["processor"])."?".QS_PARAM_NAME_ORDER_ID."=".$_orderids."&".QS_PARAM_NAME_BANK_REF."=&".QS_PARAM_NAME_AMNT_PAID."=&".QS_PARAM_NAME_CARD_TYPE."=&".QS_PARAM_NAME_REF_NUM."=";
    $gateway_vars['print_zero_qty'] = 'false';
    $gateway_vars['receipt_address'] = $userinfo["email"];
    $gateway_vars['payment_reference'] = $_orderids;
    $gateway_vars['return_link_text'] = 'Click here to return to '.$config['Company']['company_name']. ' website';
    $gateway_vars['payment_alert'] = $module_params["param05"];

    if (!empty($cart['products'])) {
        foreach($cart['products'] as $product) {
            $product_name = $product['productcode'].'-'.$product["product"];
            if (is_array($product['product_options']) && count($product['product_options']) > 0) {
                $product_name .= ' (';
                foreach($product['product_options'] as $option){
                    $product_name .= $option['class'].': '.$option['option_name'].', ';
                }

                // trim the trailing ', '
                $product_name = substr($product_name, 0, -2);
                $product_name .= ')';
            }
            $gateway_vars[$product_name] = $product['amount'].','.number_format($product['price'], 2, '.', '');
        }
    }

    if (!empty($cart['giftcerts'])) {
        foreach ($cart['giftcerts'] as $index => $giftcert) {
            $amount = number_format($giftcert['amount'], 2, '.', '');
            $gateway_vars["Gift Certificate (". $config['General']['currency_symbol'] . $amount . ")"] = "1,".$amount;
        }
    }

    // Shipping
    $shipping_cost = (float)$cart['shipping_cost'];
    if ($shipping_cost > 0) {
        $shipping_desc = $cart['delivery'];
        $gateway_vars[(strlen($shipping_desc) > 0 ? $shipping_desc : 'Shipping')] = number_format($shipping_cost, 2, '.', '');
        unset($shipping_desc);
    }

    // Tax
    if (is_array($cart['taxes']) && !empty($cart['taxes'])) {
        foreach($cart['taxes'] as $tax) {
          $gateway_vars[$tax['tax_display_name']] = number_format($tax['tax_cost'], 2, '.', '');
        }
    }

    // Discount
    $discount = (float)$cart['discount'];
    if ($discount > 0) {
        $gateway_vars['Discount'] = '-'.number_format($discount, 2, '.', '');
    }

    // Discount coupons
    // Only 1 coupon can be used per order
    $coupon_discount = (float)$cart['coupon_discount'];
    if ($coupon_discount > 0) {
        $gateway_vars['Discount Coupon: '.$cart['coupon']] = '-'.number_format($coupon_discount, 2, '.', '');
    }

    // Gift certs
    if (!empty($cart['applied_giftcerts'])) {
        foreach($cart['applied_giftcerts'] as $cert) {
            $gateway_vars['Gift Certificate: '.$cert['giftcert_id']] = '-'.number_format($cert['giftcert_cost'], 2, '.', '');
        }
    }

    // Payment method surcharge
    $surcharge = (float)$cart['payment_surcharge'];
    if ($surcharge > 0) {
        $gateway_vars['Payment method surcharge'] = number_format($surcharge, 2, '.', '');
    }

    // Display billing details
    $information_fields = array();
    if (strtoupper($module_params['param02']) == 'Y') {
        $information_fields['Customer_Name'] = $bill_name;
        $information_fields['Customer_Address'] = $userinfo["b_address"]." ".$userinfo["b_address_2"];
        $information_fields['Customer_City'] = $userinfo["b_city"];
        $information_fields['Customer_State'] = $userinfo["b_state"];
        $information_fields['Customer_PostCode'] = $userinfo["b_zipcode"];
        $information_fields['Customer_Country'] = $userinfo["b_countryname"];
        $information_fields['Customer_Phone'] = $userinfo["phone"];
        $information_fields['Customer_Email'] = $userinfo["email"];

    }

    // Display shipping details
    if (strtoupper($module_params['param03']) == 'Y') {
        $information_fields['Shipping_Name'] = $userinfo["s_firstname"]." ".$userinfo["s_lastname"];
        $information_fields['Shipping_Address'] = $userinfo["s_address"]." ".$userinfo["s_address_2"];
        $information_fields['Shipping_City'] = $userinfo["s_city"];
        $information_fields['Shipping_State'] = $userinfo["s_state"];
        $information_fields['Shipping_PostCode'] = $userinfo["s_zipcode"];
        $information_fields['Shipping_Country'] = $userinfo["s_countryname"];
    }
    // Display customer comments
    if (strtoupper($module_params['param04']) == 'Y') {
        $information_fields['Customer_Notes'] = $userinfo["Customer_Notes"];
    }
    if (count($information_fields) > 0) {
        $gateway_vars = array_merge($gateway_vars, $information_fields);
        $gateway_vars['information_fields'] = implode(',', array_keys($information_fields));
    }
    unset($information_fields);

    if(!$duplicate) {
        db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($_orderids)."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");
    }

    func_create_payment_form($gateway_url, $gateway_vars, $payment_method);
    unset($gateway_vars);
    exit;
}
?>
