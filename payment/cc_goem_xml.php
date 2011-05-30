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
 * "GoEmerchant - XML Gateway API" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_goem_xml.php,v 1.22.2.1 2011/01/10 13:12:06 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

func_set_time_limit(180);

x_load('http');

$avserr = array(
    'A' => "Address matches - Zip Code does not ",
    'B' => "Street address match, Postal code in wrong format. (international issuer) ",
    'C' => "Street address and postal code in wrong formats ",
    'D' => "Street address and postal code match (international issuer) ",
    'E' => "AVS Error ",
    'G' => "Service not supported by non-US issuer ",
    'I' => "Address information not verified by international issuer. ",
    'M' => "Street Address and Postal code match (international issuer) ",
    'N' => "No match on address or Zip Code ",
    'O' => "No Response sent ",
    'P' => "Postal codes match, Street address not verified due to incompatible formats. ",
    'R' => "Retry - system is unavailable or timed out ",
    'S' => "Service not supported by issuer ",
    'U' => "Address information is unavailable ",
    'W' => "9-digit Zip Code matches - address does not ",
    'X' => "Exact match ",
    'Y' => "Address and 5-digit Zip Code match ",
    'Z' => "5-digit zip matches - address does not ",
    '0' => "No Response sent "
);

$cvverr = array(
    'M' => "CVV2 Match ",
    'N' => "CVV2 No Match ",
    'P' => "Not Processed ",
    'S' => "Issuer indicates that CVV2 data should be present on the card, but the merchant has indicated that the CVV2 data is not present on the card ",
    'U' => "Issuer has not certified for CVV2 or Issuer has not provided Visa with theCVV2 encryption Keys "
);

$pp_merch = $module_params['param01']; #must be a 4 digits number
$pp_passwd = $module_params['param02'];
$pp_gid = $module_params['param04'];

$first4 = 0+substr($userinfo['card_number'],0,4);
if ($first4 >= 4000 && $first4 <= 4999)
    $userinfo['card_type'] = 'Visa'; // VISA
if ($first4 >= 5100 && $first4 <= 5999)
    $userinfo['card_type'] = 'MasterCard'; // MasterCard
if (($first4 >= 3400 && $first4 <= 3499) || ($first4 >= 3700 && $first4 <= 3799))
    $userinfo['card_type'] = 'Amex'; // AmericanExpress
if ($first4 == 6011)
    $userinfo['card_type'] = 'Discover'; // Discover

$_oid = substr(preg_replace("/([^\w\d\s\.\-,@]|[_])+/i", "-", $module_params['param03'].join("-",$secure_oid)), 0, 50);
$_rip = func_get_valid_ip($REMOTE_ADDR);

$is_preauth = ($module_params['use_preauth'] == 'Y' || func_is_preauth_force_enabled($secure_oid));
$trantype = $is_preauth ? 'auth' : 'sale';

$post = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<TRANSACTION>
    <FIELDS>
        <FIELD KEY="merchant">$pp_merch</FIELD>
        <FIELD KEY="password">$pp_passwd</FIELD>
        <FIELD KEY="gateway_id">$pp_gid</FIELD>
        <FIELD KEY="operation_type">$trantype</FIELD>
        <FIELD KEY="order_id">$_oid</FIELD>
        <FIELD KEY="total">$cart[total_cost]</FIELD>
        <FIELD KEY="card_name">$userinfo[card_type]</FIELD>
        <FIELD KEY="card_number">$userinfo[card_number]</FIELD>
        <FIELD KEY="card_exp">$userinfo[card_expire]</FIELD>
        <FIELD KEY="cvv2">$userinfo[card_cvv2]</FIELD>
        <FIELD KEY="owner_name">$userinfo[card_name]</FIELD>
        <FIELD KEY="owner_street">$userinfo[b_address]</FIELD>
        <FIELD KEY="owner_city">$userinfo[b_city]</FIELD>
        <FIELD KEY="owner_state">$userinfo[b_state]</FIELD>
        <FIELD KEY="owner_zip">$userinfo[b_zipcode]</FIELD>
        <FIELD KEY="owner_country">$userinfo[b_country]</FIELD>
        <FIELD KEY="owner_email">$userinfo[email]</FIELD>
        <FIELD KEY="owner_phone">$userinfo[phone]</FIELD>
        <FIELD KEY="recurring">0</FIELD>
        <FIELD KEY="recurring_type">Null</FIELD>
        <FIELD KEY="remote_ip_address">$_rip</FIELD>
    </FIELDS>
</TRANSACTION>
XML;

list($a, $return) = func_https_request(
    'POST',
    "https://secure.goemerchant.com:443/secure/gateway/xmlgateway.aspx",
    array($post),
    '',
    '',
    'text/xml',
    $http_location.'/payment/payment_cc.php'
);

preg_match("/<FIELD KEY=[^\w]*status[^\w]*>(.+)<\/FIELD>/i", $return, $sts);

$bill_output['billmes'] = '';

if ($sts[1] == 1) {

    $bill_output['code'] = 1;

    if (preg_match("/<FIELD KEY=[^\w]*auth_response[^\w]*>(.+)<\/FIELD>/i", $return, $out)) {
        $bill_output['billmes'] .= $out[1]." ";
    }

    if (preg_match("/<FIELD KEY=[^\w]*auth_code[^\w]*>(.+)<\/FIELD>/i", $return, $out)) {
        $bill_output['billmes'] .= " (Auth code: ".$out[1].")";
    }

    if (preg_match("/<FIELD KEY=[^\w]*reference_number[^\w]*>(.+)<\/FIELD>/i", $return, $out)) {
        $bill_output['billmes'] .= " (RefNumber: ".$out[1].")";

        if ($bill_output['code'] == 1 && $is_preauth) {
            $bill_output['is_preauth'] = true;
            $extra_order_data = array(
                'goem_xml_txnid'    => $out[1],
                'capture_status'    => 'A',
            );
        }
    }

} else {

    $bill_output['code'] = 2;
    if (preg_match("/<FIELD KEY=[^\w]*error[^\w]*>(.+)<\/FIELD>/i", $return, $out)) {
        $bill_output['billmes'] .= ($sts[1] == 2 ? 'Declined' : 'Error').": ".$out[1];
    }

}

if (preg_match("/<FIELD KEY=[^\w]*avs_code[^\w]*>(.+)<\/FIELD>/i", $return, $out)) {
    if (!empty($avserr[$out[1]]) || !empty($out[1])) {
        $bill_output['avsmes'] = ($avserr[$out[1]] ? $avserr[$out[1]] : ("AVSCode: ".$out[1]));
    }
}

if (preg_match("/<FIELD KEY=[^\w]*cvv2_code[^\w]*>(.+)<\/FIELD>/i", $return, $out)) {
    if (!empty($cvverr[$out[1]]) || !empty($out[1])) {
        $bill_output['cvvmes'] = ($cvverr[$out[1]] ? $cvverr[$out[1]] : ("CVVCode: ".$out[1]));
    }
}

?>
