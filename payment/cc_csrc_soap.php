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
 * "CyberSource - SOAP Toolkit API" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_csrc_soap.php,v 1.16.2.1 2011/01/10 13:12:05 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

func_set_time_limit(100);

x_load('http', 'files', 'xml');

/* WSDL version */
$cs_wsdl_ver = 43;

$cs_merchant_id    = $module_params['param01'];
$cs_key_file    = $module_params['param02'];
$cs_mod_curr    = $module_params['param03'];
$cs_order_pref    = $module_params['param04'];

/* cvIndicator:
 0: CV number service not requested
 1: CV number service requested and supported
 2: CV number on credit card is illegible
 9: CV number was not imprinted on credit card
*/
$cs_cv_num_ind = 1;

$cv_transact_key = func_file_get($xcart_dir.CSRS_SOAP_CERT_PATH.$cs_key_file, true);
if ($cv_transact_key === false || empty($cv_transact_key)) {

    $bill_output['code'] = 2;
    $bill_output['billmes'] = "Processor error. Please use another payment method.";

    return false;
}

include $xcart_dir.'/payment/cc_csrc.resp_codes.php';

$is_preauth    = ($module_params['use_preauth'] == 'Y' || func_is_preauth_force_enabled($secure_oid));

if (!empty($userinfo['card_name'])) {
    list($bill_firstname, $bill_lastname) = explode(" ", $userinfo['card_name'], 2);
}

$data = array(
    'merchantID'            => $cs_merchant_id,
    'merchantReferenceCode'    => ($trantype == 'ccCaptureService') ? $csrc_ordid : $cs_order_pref.join("-", $secure_oid),
    'clientLibrary'            => 'PHP',
    'clientLibraryVersion'    => phpversion(),
    'clientEnvironment'        => htmlspecialchars(php_uname()),
);

if ($trantype == 'ccCaptureService') {

    $xml_transaction_data = "<authRequestID>".$csrc_txnid."</authRequestID>";
} else {

    $trantype = 'ccAuthService';
    $csrc_ordid = $cs_order_pref.join("-", $secure_oid);
    $total_cost = $cart['total_cost'];

    $cs_user_info = array();
    foreach ($userinfo as $k => $v) {
        if (is_array($v)) continue;
        $cs_user_info[$k] = htmlspecialchars($v);
    }

    $data += array(
        'billTo'         => array(
            'firstName'            => $bill_firstname,
            'lastName'            => $bill_lastname,
            'street1'            => $cs_user_info['b_address'],
            'city'                => $cs_user_info['b_city'],
            'state'                => $cs_user_info['b_state'],
            'postalCode'        => $cs_user_info['b_zipcode'],
            'country'            => $cs_user_info['b_country'],
            'phoneNumber'        => $cs_user_info['phone'],
            'email'                => $cs_user_info['email'],
            'ipAddress'            => func_get_valid_ip($REMOTE_ADDR),
        ),
        'purchaseTotals' => array(
        ),
        'card'             => array(
            'fullName'            => $cs_user_info['card_name'],
            'accountNumber'        => $cs_user_info['card_number'],
            'expirationMonth'    => substr($userinfo['card_expire'], 0, 2),
            'expirationYear'    => '20'.substr($userinfo['card_expire'], 2, 2),
            'cvIndicator'        => $cs_cv_num_ind,
            'cvNumber'            => $cs_user_info['card_cvv2'],
        ),
    );

    $xml_additional_transaction = $is_preauth ? '' : "<ccCaptureService run=\"true\" />";
}

$data['purchaseTotals'] = array(
    'currency'            => ($trantype == 'ccCaptureService') ? $csrc_currency : $cs_mod_curr,
    'grandTotalAmount'    => $total_cost,
);

$xml_data = trim(func_hash2xml($data, 3, " "));

$post = <<<XML
<?xml version="1.0" encoding="iso-8859-1"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:data="urn:schemas-cybersource-com:transaction-data-1.$cs_wsdl_ver">
 <SOAP-ENV:Header>
  <wsse:Security SOAP-ENV:mustUnderstand="1" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
   <wsse:UsernameToken>
    <wsse:Username>$cs_merchant_id</wsse:Username>
    <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wssusername-token-profile-1.0#PasswordText">$cv_transact_key</wsse:Password>
   </wsse:UsernameToken>
  </wsse:Security>
 </SOAP-ENV:Header>
 <SOAP-ENV:Body>
  <requestMessage xmlns="urn:schemas-cybersource-com:transaction-data-1.$cs_wsdl_ver">
   $xml_data
   <$trantype run="true">$xml_transaction_data</$trantype>
   $xml_additional_transaction
  </requestMessage>
 </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
XML;

$csrc_url = "https://".(($module_params['testmode'] == 'Y') ? 'ics2wstest.ic3.com' : 'ics2ws.ic3.com').'/commerce/1.x/transactionProcessor/CyberSourceTransaction_1.'.$cs_wsdl_ver.'.wsdl';

list($a, $return) = func_https_request('POST', $csrc_url, array($post), '', '', 'text/xml');

$ord_fields = array(
    'requestID',
    'decision',
    'reasonCode',
    'avsCode',
    'cvCode',
    'merchantReferenceCode',
);

$result = array();
foreach ($ord_fields as $field) {
    if (preg_match("!<c:".$field.">([^>]+)</c:".$field.">!", $return, $out)) {
        $result[$field] = trim($out[1]);
    }
}

if (strtoupper($result['decision']) == 'ACCEPT') {

    $bill_output['code'] = 1;

    if ($is_preauth && $trantype == 'ccAuthService') {
        $bill_output['is_preauth'] = true;
        $extra_order_data = array(
            'csrc_txnid'     => $result['requestID'],
            'csrc_ordid'     => $result['merchantReferenceCode'],
            'csrc_currency'     => $cs_mod_curr,
            'capture_status' => 'A',
        );
    }
} else {

    $bill_output['code'] = 2;

    if (preg_match("!<faultstring>([^>]+)</faultstring>!", $return, $out) && !empty($out[1])) {
        $bill_output['billmes'] = trim($out[1]);
    }
}

if (!empty($result['reasonCode'])) {
    $bill_output['billmes'] .= $reason[$result['reasonCode']]."(code ".$result['reasonCode'].")'.'\n";
}

if ($result['requestID']) {
    $bill_output['billmes'] .= "RequestID: ".$result['requestID']."\n";
}
if ($result['merchantReferenceCode']) {
    $bill_output['billmes'] .= "MerchantReferenceCode: ".$result['merchantReferenceCode']."\n";
}
if ($result['avsCode']) {
    $bill_output['avsmes'] = $avserr[$result['avsCode']];
}
if ($result['cvCode']) {
    $bill_output['cvvmes'] = $cvverr[$result['cvCode']];
}

?>
