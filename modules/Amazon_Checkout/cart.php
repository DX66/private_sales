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
 * @version    $Id: cart.php,v 1.6.2.1 2011/01/10 13:11:55 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

if (empty($cart['products']) || !is_array($cart['products'])) {
    return false;
}

x_load(
    'xml'
);

$xml_hash = array();
$xml_hash['Cart']['Items']['Item'] = array();

foreach ($cart['products'] as $k => $product) {
    $new_item = array();
    $new_item['SKU'] = $product['productcode'].'|'.$product['cartid'];
    $new_item['MerchantId'] = $config['Amazon_Checkout']['amazon_mid'];
    $new_item['Title'] = func_xml_escape($product['product']);
    $options_txt = '';
    $options_txt = "$product[product]\n";
    if (!empty($product['product_options']) && is_array($product['product_options'])) {
        $options_txt = func_get_langvar_by_name('lbl_selected_options', NULL, false, true).":\n";
        foreach ($product['product_options'] as $pk => $po) {
            $options_txt .= "$po[class]: $po[option_name]\n";
        }
    }
    $new_item['Description'] = func_xml_escape(substr($options_txt,0,255));
    $new_item['Price'] = array('Amount' => $product['display_price'], 'CurrencyCode' => $config['Amazon_Checkout']['amazon_currency']);
    $new_item['Quantity'] = $product['amount'];
    if ($product['weight']) {
        $new_item['Weight'] = array('Amount' => func_units_convert(func_weight_in_grams($product['weight']), "g", "lbs", 2), 'Unit' => 'lb');
    }
    $new_item['Images']['Image']['URL'] = "$http_location/image.php?type=P&amp;id=$product[productid]";

    $xml_hash['Cart']['Items']['Item'][] = $new_item;
}

x_load('crypt');
$xml_hash['Cart']['CartCustomData']['Session'] = text_crypt($XCARTSESSID);

$_index = 0;
while ($_index <= 10) {
    $_index++;
    $unique_id = md5(uniqid(rand()));
    if (!func_query_first("SELECT ref FROM $sql_tbl[cc_pp3_data] WHERE ref='$unique_id'")) {
        break;
    }
}

$xml_hash['Cart']['CartCustomData']['Ref'] = $unique_id;

$xml_hash['Cart']['CartCustomData']['Chk'] = md5(serialize($cart));

$xml_hash['OrderCalculationCallbacks']['CalculateTaxRates'] = 'true';
$xml_hash['OrderCalculationCallbacks']['CalculatePromotions'] = 'true';
$xml_hash['OrderCalculationCallbacks']['CalculateShippingRates'] = 'true';
$xml_hash['OrderCalculationCallbacks']['OrderCallbackEndpoint'] = "$https_location/payment/ps_amazon.php";
$xml_hash['OrderCalculationCallbacks']['ProcessOrderOnCallbackFailure'] = 'false';

$xml_hash = func_amazon_hash2xml($xml_hash, 'Order');

$encoded_cart = 'order:'.base64_encode($xml_hash);
if (!empty($config['Amazon_Checkout']['amazon_secret_key'])) {
    $encoded_cart = 'type:merchant-signed-order/aws-accesskey/1;'.$encoded_cart.';signature:'.func_amazon_sign($xml_hash).';aws-access-key-id:'.$config['Amazon_Checkout']['amazon_access_key'];
} else {
    $encoded_cart = 'type:unsigned-order;'.$encoded_cart;
}

$smarty->assign('amazon_cart', $encoded_cart);

unset($xml_hash);

?>
