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
 * Checkout by Amazon
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: checkout_callback.php,v 1.8.2.3 2011/01/10 13:11:55 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

/**
 * Callback:
 * Calculate taxes, shipping and etc
 * Then store cart and transaction details
 */

$root_node = 'ORDERCALCULATIONSREQUEST';

$ref = func_array_path($parsed, "$root_node/CALLBACKORDERCART/CARTCUSTOMDATA/REF/0/#");

$sessid = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='$ref'");
if (empty($sessid)) {
    x_load('crypt');
    $sessid = text_decrypt(func_array_path($parsed, "$root_node/CALLBACKORDERCART/CARTCUSTOMDATA/SESSION/0/#"));
    @db_query("INSERT INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('$ref','$sessid','GO|')");
}

x_session_id($sessid);
x_session_register('login');
x_session_register('current_carrier', 'UPS');

$saved_cart = func_query_first_cell("SELECT cart FROM $sql_tbl[amazon_data] WHERE ref='$ref'");
if ($saved_cart) {
    // Cart is already stored, restore it from amazon_data
    $cart = unserialize($saved_cart);
} else {
    // Cart is not stored, so it's a first Amazon callback request - need to validate the cart
    x_session_register('cart');
    $chk = func_array_path($parsed, "$root_node/CALLBACKORDERCART/CARTCUSTOMDATA/CHK/0/#");
    if ($chk != md5(serialize($cart))) {
        // Cart was modified before callback - stop processing it
        x_log_flag(
            'log_payment_processing_errors',
            'PAYMENTS',
            "Amazon checkout payment module: Cart was modified before callback!\nChk = $chk\nmd5 = " . md5(serialize($cart)),
            true
        );
        func_amazon_header_exit(500);
    }
}

if (!empty($login)) {
    $user_account['membershipid'] = func_query_first_cell("SELECT membershipid FROM $sql_tbl[customers] WHERE id='$logged_userid'");
}

$_addr = func_array_path($parsed, "$root_node/CALLBACKORDERS/CALLBACKORDER/ADDRESS/0/#");
$customer_info['s_country'] = $customer_info['b_country'] = $_addr['COUNTRYCODE'][0]['#'];
$customer_info['s_address'] = $customer_info['b_address'] = $_addr['ADDRESSFIELDONE'][0]['#']."\n".$_addr['ADDRESSFIELDTWO'][0]['#'];
$customer_info['s_city'] = $customer_info['b_city'] = $_addr['CITY'][0]['#'];
$customer_info['s_state'] = $customer_info['b_state'] = func_amazon_detect_state($_addr['STATE'][0]['#'], $customer_info['s_country']);
$customer_info['s_zipcode'] = $customer_info['b_zipcode'] = $_addr['POSTALCODE'][0]['#'];

// Emulate the 'apply_default_country' option enabled and user not logged in
$config['General']['apply_default_country'] = 'Y';
$config['General']['default_country'] = $customer_info['s_country'];
$config['General']['default_zipcode'] = $customer_info['s_zipcode'];
$config['General']['default_state'] = $customer_info['s_state'];
$config['General']['default_city'] = $customer_info['s_city'];
$config['Shipping']['enable_all_shippings'] = 'N';
$config['Shipping']['realtime_shipping'] = 'N';

$_login = $login;
$login = '';
$products = func_products_in_cart($cart, (!empty($user_account['membershipid']) ? $user_account['membershipid'] : ''));
$cart = func_array_merge($cart, func_calculate($cart, $products, $logged_userid, 'C', 0));

$login = $_login;
// Get the shipping methods list with rates
$intershipper_recalc = 'Y';

$_allowed_shipping_methods = func_get_shipping_methods_list($cart, $products, $customer_info);

$amazon_shipping_methods = array();
if (!empty($_allowed_shipping_methods)) {
    foreach ($_allowed_shipping_methods as $v) {
        if (!empty($v['amazon_service'])) {
            if (!array_key_exists($v['amazon_service'], $amazon_shipping_methods)
            || $amazon_shipping_methods[$v['amazon_service']]['rate'] > $v['rate']) {
                    $amazon_shipping_methods[$v['amazon_service']] = array('shippingid' => $v['shippingid'], 'rate' => $v['rate'], 'shipping' => $v['shipping']);
            }
        }
    }
}

$response_hash = array();

if (empty($amazon_shipping_methods)) {
    $response_hash['Response']['Error']['Code'] = 'INVALID_SHIPPING_ADDRESS';
    $response_hash['Response']['Error']['Message'] = 'No shipping methods available for this address.';
    $response_xml = func_amazon_hash2xml($response_hash, 'OrderCalculationsResponse');
    echo func_amazon_prepare_response($response_xml, 'order-calculations-response');
    exit;
}

$calc_taxes = (func_array_path($parsed, "$root_node/ORDERCALCULATIONCALLBACKS/CALCULATETAXRATES/0/#") == 'true');

$calc_promos = (func_array_path($parsed, "$root_node/ORDERCALCULATIONCALLBACKS/CALCULATEPROMOTIONS/0/#") == 'true');

$cart['amazon_shippings'] = $amazon_shipping_methods;

@db_query("REPLACE INTO $sql_tbl[amazon_data] (ref,cart) VALUES ('$ref','".addslashes(serialize($cart))."')");

$response_hash['Response']['CallbackOrders']['CallbackOrder']['Address']['AddressId'] = $_addr['ADDRESSID'][0]['#'];
$response_hash['Response']['CallbackOrders']['CallbackOrder']['CallbackOrderItems']['CallbackOrderItem'] = array();

$_products = func_array_path($parsed, "$root_node/CALLBACKORDERCART/CALLBACKORDERCARTITEMS/CALLBACKORDERCARTITEM");

if (empty($_products)) {
    func_amazon_header_exit(403);
}

foreach ($_products as $_k => $_v) {
    $_v = $_v['#'];
    $_itemid = func_array_path($_v,"CALLBACKORDERITEMID/0/#");
    list($_sku, $_cartid) = explode("|", func_array_path($_v,"ITEM/SKU/0/#"));

    foreach ($products as $k => $product) {
        if ($product['cartid'] == $_cartid) {
            $item['CallbackOrderItemId'] = $_itemid;

            // Enable shipping methods for each product
            $item['ShippingMethodIds']['ShippingMethodId'] = array();
            foreach ($amazon_shipping_methods as $sv) {
                $item['ShippingMethodIds']['ShippingMethodId'][] = $sv['shippingid'];
            }

            $response_hash['Response']['CallbackOrders']['CallbackOrder']['CallbackOrderItems']['CallbackOrderItem'][] = $item;

            break;
        }
    }
}

if ($calc_promos) {
    $response_hash['Promotions']['Promotion'] = array();
    $prm = array();
    $prm['PromotionId'] = 'total-discount';
    $prm['Description'] = 'Total cart discount';
    $prm['Benefit']['FixedAmountDiscount'] = array('Amount' => price_format($cart['coupon_discount']+$cart['discount']), 'CurrencyCode' => $config['Amazon_Checkout']['amazon_currency']);
    $response_hash['Promotions']['Promotion'][] = $prm;

}

// Shipping list
$response_hash['ShippingMethods']['ShippingMethod'] = array();
foreach ($amazon_shipping_methods as $k => $v) {
    $mtd = array();
    $mtd['ShippingMethodId'] = $v['shippingid'];
    $mtd['ServiceLevel'] = $k;
    $mtd['Rate']['ShipmentBased'] = array('Amount' => price_format($v['rate']), 'CurrencyCode' => $config['Amazon_Checkout']['amazon_currency']);
    $mtd['IncludedRegions']['PredefinedRegion'] = 'WorldAll';
    $mtd['DisplayableShippingLabel'] = $v['shipping'];
    $response_hash['ShippingMethods']['ShippingMethod'][] = $mtd;
}

if ($calc_promos) {
    $response_hash['CartPromotionId'] = 'total-discount';
}

if ($calc_taxes) {
    $response_hash['CartTaxAmount'] = array('Amount' => price_format(@$cart['tax_cost']), 'CurrencyCode' => $config['Amazon_Checkout']['amazon_currency']);
}

$response_xml = func_amazon_hash2xml($response_hash, 'OrderCalculationsResponse');

x_session_save();

/*
// debug
$filename = $var_dirs['log'] . "/amazon-response-" . date("Ymd-His") . "-" . uniqid(rand()) . '.log.php';
if ($fd = @fopen($filename, "a+")) {
    @fwrite($fd, "<?php die(); ?>\n\n" . $response_xml);
    @fclose($fd);
    @func_chmod_file($filename);
}
// /debug
*/

echo func_amazon_prepare_response($response_xml, 'order-calculations-response');

?>
