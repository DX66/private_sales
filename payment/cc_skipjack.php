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
 * "SkipJack" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_skipjack.php,v 1.43.2.2 2011/01/10 13:12:07 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('http');

$staerr = array(
    '-35' => 'Error invalid credit card number',
    '-37' => 'Error failed communication',
    '-39' => 'Error length serial number',
    '-51' => 'Error length zip code',
    '-52' => 'Error length shipto zip code',
    '-53' => 'Error length expiration date',
    '-54' => 'Error length account number date',
    '-55' => 'Error length street address',
    '-56' => 'Error length shipto street address',
    '-57' => 'Error length transaction amount',
    '-58' => 'Error length name',
    '-59' => 'Error length location',
    '-60' => 'Error length state',
    '-61' => 'Error length shipto state',
    '-62' => 'Error length order string',
    '-64' => 'Error invalid phone number',
    '-65' => 'Error empty name',
    '-66' => 'Error empty email',
    '-67' => 'Error empty street address',
    '-68' => 'Error empty city',
    '-69' => 'Error empty state',
    '-79' => 'Error length customer name',
    '-80' => 'Error length shipto customer name',
    '-81' => 'Error length customer location',
    '-82' => 'Error length customer state',
    '-83' => 'Error length shipto phone',
    '-84' => 'Pos error duplicate ordernumber',
    '-91' => "Pos_error_CVV2",
    '-92' => "Pos_error_Error_Approval_Code",
    '-93' => "Pos_error_Blind_Credits_Not_Allowed",
    '-94' => "Pos_error_Blind_Credits_Failed",
    '-95' => "Pos_error_Voice_Authorizations_Not_Allowed "
);

$avserr = array(
    'X' => "Exact match, 9 digit zip",
    'Y' => "Exact match, 5 digit zip",
    'A' => 'Address match only',
    'W' => "9 digit match only",
    'Z' => "5 digit match only",
    'N' => 'No address or zip match',
    'U' => 'Address unavailable',
    'R' => 'Issuer system unavailable',
    'E' => "Not a mail/phone order",
    'S' => 'Service not supported'
);

$ordr = join("-", $secure_oid);
$ordr_len = strlen($ordr);
if ($ordr_len < 20) {
    $ordr = substr($module_params['param03'], 0, 20 - $ordr_len) . $ordr;
}

$os = '';
foreach ($products as $product) {
    $os .= substr(preg_replace("/[^\d\w ]/Ss", '', $product['productcode']), 0, 20)."~".
        substr(preg_replace("/[^\d\w ]/Ss", '', $product['product']), 0, 120)."~".
        sprintf("%.02f", $product['price'])."~".$product['amount'] .
        "~N~||";
}

if (isset($cart['giftcerts']) && is_array($cart['giftcerts']) && count($cart['giftcerts']) > 0) {
    foreach ($cart['giftcerts'] as $k => $tmp_gc) {
        $os .= 'GC' . $k . "~GIFT CERTIFICATE~" . sprintf("%.02f", $tmp_gc['amount']) . "~1~N~||";
    }
}

$sj_name = trim($userinfo['card_name']);
if (empty($sj_name)) {
    $sj_name = empty($bill_name) ? 'NA' : $bill_name;
}

$post = array(
    "SJName=" . substr($sj_name, 0, 40),
    "Email=" . (!empty($userinfo['email']) ? substr($userinfo["email"], 0, 40) : 'None'),
    "StreetAddress=" . (!empty($userinfo['b_address']) ? substr($userinfo["b_address"], 0, 40) : 'None'),
    "City=" . (!empty($userinfo['b_city']) ? substr($userinfo["b_city"], 0, 40) : 'None'),
    "State=" . (!empty($userinfo['b_state']) ? substr($userinfo['b_state'], 0, 40) : 'XX'),
    "ZipCode=" . (!empty($userinfo['b_zipcode']) ? substr($userinfo["b_zipcode"], 0, 10) : '00000'),
    "Country=" . substr($userinfo['b_country'], 0, 40),
    "Phone=" . substr(preg_replace('/\D/s', '', $userinfo["b_phone"]), 0, 12),
    "ShipToPhone=" . (!empty($userinfo['s_phone']) ? substr(preg_replace('/\D/s', '', $userinfo["s_phone"]), 0, 12) : '0000000000'),

    "OrderNumber=" . $ordr,
    "AccountNumber=" . substr(preg_replace('/\D/s', '', $userinfo["card_number"]), 0, 18),
    "Month=" . substr($userinfo['card_expire'], 0, 2),
    "Year=" . substr($userinfo['card_expire'], 2, 2),
    "CVV2=" . substr($userinfo['card_cvv2'], 0, 4),
    "TransactionAmount=" . sprintf("%.02f", $cart['total_cost']),
    "OrderString=" . $os
);

if (!empty($userinfo['b_address_2'])) {
    $post[] = "StreetAddress2=" . substr($userinfo['b_address_2'], 0, 40);
}
if (!empty($userinfo['fax'])) {
    $post[] = "Fax=" . substr(preg_replace('/\D/s', '', $userinfo["fax"]), 0, 12);
}

// Shipping information
if (!empty($ship_name)) {
    $post[] = "ShipToName=" . substr($ship_name, 0, 40);
}
if (!empty($userinfo['s_address'])) {
    $post[] = "ShipToStreetAddress=" . substr($userinfo['s_address'], 0, 40);
}
if (!empty($userinfo['s_address_2'])) {
    $post[] = "ShipToStreetAddress2=" . substr($userinfo['s_address_2'], 0, 40);
}
if (!empty($userinfo['s_city'])) {
    $post[] = "ShipToCity=" . substr($userinfo['s_city'], 0, 40);
}
if (!empty($userinfo['s_state'])) {
    $post[] = "ShipToState=" . substr($userinfo['s_state'], 0, 40);
}
if (!empty($userinfo['s_zipcode'])) {
    $post[] = "ShipToZipcode=" . substr($userinfo['s_zipcode'], 0, 10);
}
if (!empty($userinfo['s_country'])) {
    $post[] = "ShipToCountry=" . substr($userinfo['s_country'], 0, 40);
}

list($res, $a, $return) = func_cc_skipjack_do($post);

if ($res['szIsApproved'] == 1) {
    $bill_output['code'] = 1;

    if (isset($res['szTransactionFileName']) && !empty($res['szTransactionFileName'])) {
        $bill_output['billmes'] .=  ' (Transaction ID: ' . $res["szTransactionFileName"]. ')';
    }

    if (isset($res['AUTHCODE']) && !empty($res['AUTHCODE']))
        $bill_output['billmes'] .=  ' (Auth.code: ' . $res["AUTHCODE"]. ')';

    $bill_output['is_preauth'] = true;
    $extra_order_data = array(
        'txnid' => $res["szTransactionFileName"],
        'orderid' => $ordr,
        'capture_status' => 'A'
       );

    if (!($module_params['use_preauth'] == 'Y' || func_is_preauth_force_enabled($secure_oid))) {

        // Capture
        list($status, $new_status, $message) = func_cc_skipjack_change_status($ordr, 'SETTLE', sprintf("%.02f", $cart["total_cost"]), '1');
        if ($status) {
            $extra_order_data['capture_status'] = '';
            $bill_output['is_preauth'] = false;

        } else {
            $bill_output['billmes'] .= ' (Capture transaction is failed. Error message: ' . $message . ')';
        }
    }

} else {
    $bill_output['code'] = 2;
    $bill_output['billmes'] = (empty($res['szAuthorizationDeclinedMessage']) ? $staerr[$res['szReturnCode']] : $res['szAuthorizationDeclinedMessage'])." (ReturnCode: ".$res['szReturnCode'].')';
}

if (!empty($res['szAVSResponseCode']) || !empty($res['szAVSResponseMessage']))
    $bill_output['avsmes'] = (empty($res['szAVSResponseMessage']) ? $avserr[$res['szAVSResponseCode']] : $res['szAVSResponseMessage']) .
        " (".$res['szAVSResponseCode'].')';

if (!empty($res['szCVV2ResponseCode']) || !empty($res['szCVV2ResponseMessage']))
    $bill_output['cvvmes'] .= $res['szCVV2ResponseMessage'] . " (" . $res['szCVV2ResponseCode'] . ')';

if (!empty($res['szSerialNumber']))
    $bill_output['billmes'] .= ' (SerialNumber: ' . $res["szSerialNumber"] . ')';

?>
