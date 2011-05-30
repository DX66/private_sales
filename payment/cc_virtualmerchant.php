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
 * "Virtual Merchant - Merchant Provided Form" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_virtualmerchant.php,v 1.22.2.1 2011/01/10 13:12:07 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load('http', 'xml');

$avserr = array(
    'A' => "Address (Street) matches, ZIP does not",
    'B' => "Street address match, Postal code in wrong format. (International issuer)",
    'C' => "Street address and postal code in wrong formats",
    'D' => "Street address and postal code match (international issuer)",
    'E' => "AVS error",
    'G' => "Service not supported by non-US issuer",
    'I' => "Address information not verified by international issuer.",
    'M' => "Street Address and Postal code match (international issuer)",
    'N' => "No Match on Address (Street) or ZIP",
    'O' => "No Response sent",
    'P' => "AVS not applicable for this transaction",
    'R' => "Retry. System unavailable or timed out",
    'S' => "Service not supported by issuer",
    'U' => "Address information is unavailable",
    'W' => "9 digit ZIP matches, Address (Street) does not match",
    'X' => "Exact AVS Match",
    'Y' => "Address (Street) and 5 digit ZIP match",
    'Z' => "5 digit ZIP matches, Address (Street) does not match"
);

$cvverr = array(
    'M' => "CVV2 Match",
    'N' => "CVV2 No Match",
    'P' => "Not Processed",
    'S' => "Issuer indicates that CVV2 data should be present on the card, but the merchant has indicated that the CVV2 data is not resent on the card",
    'U' => "Issuer has not certified for CVV2 or Issuer has not provided Visa with the CVV2 encryption Keys"
);

$data = array(
    'ssl_invoice_number' => substr(join("-", $secure_oid), 0, 25),
    'ssl_merchant_id' => substr($module_params['param01'], 0, 15),
    'ssl_user_id' => substr($module_params['param07'], 0, 15),
    'ssl_pin' => substr($module_params['param02'], 0, 6),
       'ssl_customer_code' => '1111',
       'ssl_salestax' => $cart['tax_cost'],
    'ssl_description' => substr($module_params['param04'] . join("-", $secure_oid), 0, 255),
    'ssl_amount' => price_format($cart['total_cost']),
    'ssl_transaction_type' => ($module_params['use_preauth'] == 'Y' || func_is_preauth_force_enabled($secure_oid))? "CCAUTHONLY" : "CCSALE",
    'ssl_card_number' => substr($userinfo['card_number'], 0, 19),
    'ssl_exp_date' => substr($userinfo['card_expire'], 0, 4),

    'ssl_company' => substr($userinfo['company'], 0, 50),
    'ssl_first_name' => substr($bill_firstname, 0, 20),
    'ssl_last_name' => substr($bill_lastname, 0, 30),
    'ssl_address1' => substr($userinfo['b_address'], 0, 30),
    'ssl_address2' => substr($userinfo['b_address_2'], 0, 30),
    'ssl_city' => substr($userinfo['b_city'], 0, 30),
    'ssl_state' => substr(($userinfo['b_state'] ? $userinfo['b_state'] : 'n/a'), 0, 10),
    'ssl_zip' => substr($userinfo['b_zipcode'], 0, 10),
    'ssl_country' => substr($userinfo['b_country'], 0, 50),
    'ssl_phone' => substr($userinfo['phone'], 0, 20),
    'ssl_email' => substr($userinfo['email'], 0, 100),
    'ssl_show_form' => 'false',

    'ssl_ship_to_company' => substr($userinfo['company'], 0, 50),
    'ssl_ship_to_first_name' => substr($userinfo['s_firstname'], 0, 20),
    'ssl_ship_to_last_name' => substr($userinfo['s_lastname'], 0, 30),
    'ssl_ship_to_address' => substr($userinfo['s_address'], 0, 30),
    'ssl_ship_to_city' => substr($userinfo['s_city'], 0, 30),
    'ssl_ship_to_state' => substr(($userinfo['s_state'] ? $userinfo['s_state'] : 'n/a'), 0, 10),
    'ssl_ship_to_country' => substr($userinfo['s_country'], 0, 50),
    'ssl_ship_to_zip' => substr($userinfo['s_zipcode'], 0, 10),

    'ssl_cvv2cvc2_indicator' => '1',
    'ssl_cvv2cvc2' => substr($userinfo['card_cvv2'], 0, 4)
);

if ($module_params['testmode'] != 'N')
    $data['ssl_test_mode'] = 'TRUE';

if ($module_params['param06'] == 'Y') {
    $data['ssl_avs_address'] = substr($userinfo['b_address'], 0, 20);
    $data['ssl_avs_zip'] = substr($userinfo['b_zipcode'], 0, 10);
}

$xmldata = "<txn>\n";
foreach ($data as $k => $v)
    $xmldata .= "\t<$k>$v</$k>\n";
$xmldata .= "</txn>";

if ($module_params['testmode'] == 'D')
    $url = "https://demo.myvirtualmerchant.com/VirtualMerchantDemo/processxml.do";
else
    $url = "https://www.myvirtualmerchant.com/VirtualMerchant/processxml.do";

if (defined('VIRTUALMERCHANT_DEBUG')) {
    func_pp_debug_log('virtualmerchant', 'I', $xmldata);
}

list($a, $return) = func_https_request(
    "POST",
    $url,
    array("xmldata=" . $xmldata),
    "&",
    "",
    "application/x-www-form-urlencoded",
    $php_url['url']
);


$xml = func_xml2hash($return);

if (defined('VIRTUALMERCHANT_DEBUG')) {
    func_pp_debug_log('virtualmerchant', 'R', $xml);
}

if (is_array($xml) && isset($xml['txn'])) {

    $bill_output['code'] = (isset($xml['txn']['ssl_result']) && $xml['txn']['ssl_result'] == '0') ? 1 : 2;

    if (isset($xml['txn']['errorCode'])) {
        $bill_output['billmes'] = "Error: #" . $xml['txn']['errorCode'] . " " . $xml['txn']['errorName'] . ": " . $xml['txn']['errorMessage'];

    } else {
        $bill_output['billmes'] = $xml['txn']['ssl_result_message'];
    }

    if ($bill_output['code'] == 1 && ($module_params['use_preauth'] == 'Y' || func_is_preauth_force_enabled($secure_oid)))
        $bill_output['is_preauth'] = true;

    x_load('crypt');
    if (isset($xml['txn']['ssl_txn_id'])) {
        $extra_order_data = array(
            'txnid' => $xml['txn']['ssl_txn_id'],
            'approval_code' => $xml['txn']['ssl_approval_code'],
            'capture_status' => $bill_output['is_preauth'] ? 'A' : '',
            'ccdata' => text_crypt($userinfo["card_number"] . "\n" . $userinfo["card_expire"])
        );
        $bill_output['billmes'] .= ' (Transaction Id: ' . $xml['txn']['ssl_txn_id'] . '; Approval code: ' . $xml['txn']['ssl_approval_code'] . ')';
    }

    if ($xml['txn']['ssl_avs_response'])
        $bill_output['avsmes'] = "AVS Code: ".$avserr[$xml['txn']['ssl_avs_response']] . " (" . $xml['txn']['ssl_avs_response']. ")";

    if ($xml['txn']['ssl_cvv2_response'])
        $bill_output['cvvmes'] = "CVV Code: ".$cvverr[$xml['txn']['ssl_cvv2_response']] . " (" . $xml['txn']['ssl_cvv2_response'] . ")";

} else {

    $bill_output['code'] = 2;
    $bill_output['billmes'] = "Unknown result code";

}
?>
