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
 * "AuthorizeNet - SIM" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_authorizenet_sim.php,v 1.53.2.2 2011/01/10 13:12:05 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['x_response_code']) {

    require './auth.php';

    $bill_output['sessid'] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$_POST["x_invoice_num"]."'");

    $avserr = array(
        'A' => "Address (Street) matches, ZIP does not",
        'B' => "Address information not provided for AVS check",
        'E' => "AVS error",
        'G' => "Non-U.S. Card Issuing Bank",
        'N' => "No Match on Address (Street) or ZIP",
        'P' => "AVS not applicable for this transaction",
        'R' => "Retry - System unavailable or timed out",
        'S' => "Service not supported by issuer",
        'U' => "Address information is unavailable",
        'W' => "9 digit ZIP matches, Address (Street) does not",
        'X' => "Address (Street) and 9 digit ZIP match",
        'Y' => "Address (Street) and 5 digit ZIP match",
        'Z' => "5 digit ZIP matches, Address (Street) does not"
    );

    $cvverr = array(
        'M' => 'Match',
        'N' => "No Match",
        'P' => "Not Processed",
        'S' => "Should have been present",
        'U' => "Issuer unable to process"
    );

    $err = array(
        '1' => "This transaction has been approved.",
        '2' => "This transaction has been declined.",
        '3' => "This transaction has been declined.",
        '4' => "This transaction has been declined. The code returned from the processor indicating that the card used needs to be picked up.",
        '5' => "A valid amount is required. The value submitted in the amount field did not pass validation for a number.",
        '6' => "The credit card number is invalid.",
        '7' => "The credit card expiration date is invalid. The format of the date submitted was incorrect.",
        '8' => "The credit card has expired.",
        '9' => "The ABA code is invalid. The value submitted in the x_Bank_ABA_Code field did not pass validation or was not for a valid financial institution.",
        '10' => "The account number is invalid. The value submitted in the x_Bank_Acct_Num field did not pass validation.",
        '11' => "A duplicate transaction has been submitted. A transaction with identical amount and credit card information was submitted two minutes prior.",
        '12' => "An authorization code is required but not present. A transaction that required x_Auth_Code to be present was submitted without a value.",
        '13' => "The merchant Login ID is invalid or the account is inactive.",
        '14' => "The Referrer or Relay Response URL is invalid. The Relay Response or Referrer URL does not match the merchant's configured value(s) or is absent. Applicable only to SIM and WebLink APIs.",
        '15' => "The transaction ID is invalid. The transaction ID value is non-numeric or was not present for a transaction that requires it (i.e., VOID, PRIOR_AUTH_CAPTURE, and CREDIT).",
        '16' => "The transaction was not found. The transaction ID sent in was properly formatted but the gateway had no record of the transaction.",
        '17' => "The merchant does not accept this type of credit card. The merchant was not configured to accept the credit card submitted in the transaction.",
        '18' => "ACH transactions are not accepted by this merchant. The merchant does not accept electronic checks.",
        '19' => "An error occurred during processing. Please try again in 5 minutes.",
        '20' => "An error occurred during processing. Please try again in 5 minutes.",
        '21' => "An error occurred during processing. Please try again in 5 minutes.",
        '22' => "An error occurred during processing. Please try again in 5 minutes.",
        '23' => "An error occurred during processing. Please try again in 5 minutes.",
        '24' => "The Nova Bank Number or Terminal ID is incorrect. Call Merchant Service Provider.",
        '25' => "An error occurred during processing. Please try again in 5 minutes.",
        '26' => "An error occurred during processing. Please try again in 5 minutes.",
        '27' => "The transaction resulted in an AVS mismatch. The address provided does not match billing address of cardholder.",
        '28' => "The merchant does not accept this type of credit card. The Merchant ID at the processor was not configured to accept this card type.",
        '29' => "The PaymentTech identification numbers are incorrect. Call Merchant Service Provider.",
        '30' => "The configuration with the processor is invalid. Call Merchant Service Provider.",
        '31' => "The FDC Merchant ID or Terminal ID is incorrect. Call Merchant Service Provider. The merchant was incorrectly set up at the processor.",
        '32' => "This reason code is reserved or not applicable to this API.",
        '33' => "FIELD cannot be left blank. The word FIELD will be replaced by an actual field name. This error indicates that a field the merchant specified as required was not filled in.",
        '34' => "The VITAL identification numbers are incorrect. Call Merchant Service Provider. The merchant was incorrectly set up at the processor.",
        '35' => "An error occurred during processing. Call Merchant Service Provider. The merchant was incorrectly set up at the processor.",
        '36' => "The authorization was approved, but settlement failed.",
        '37' => "The credit card number is invalid.",
        '38' => "The Global Payment System identification numbers are incorrect. Call Merchant Service Provider. The merchant was incorrectly set up at the processor.",
        '39' => "The supplied currency code is either invalid, not supported, not allowed for this merchant or doesn't have an exchange rate.",
        '40' => "This transaction must be encrypted.",
        '41' => "This transaction has been declined. Only merchants set up for the FraudScreen.Net service would receive this decline. This code will be returned if a given transaction's fraud score is higher than the threshold set by the merchant.",
        '42' => "There is missing or invalid information in a required field. This is applicable only to merchants processing through the Wells Fargo SecureSource product who have requirements for transaction submission that are different from merchants not processing through Wells Fargo.",
        '43' => "The merchant was incorrectly set up at the processor. Call your merchant service provider. The merchant was incorrectly set up at the processor.",
        '44' => "This transaction has been declined. The merchant would receive this error if the Card Code filter has been set in the Merchant Interface and the transaction received an error code from the processor that matched the rejection criteria set by the merchant.",
        '45' => "This transaction has been declined. This error would be returned if the transaction received a code from the processor that matched the rejection criteria set by the merchant for both the AVS and Card Code filters.",
        '46' => "Your session has expired or does not exist. You must log in to continue working.",
        '47' => "The amount requested for settlement may not be greater than the original amount authorized. This occurs if the merchant tries to capture funds greater than the amount of the original authorization-only transaction.",
        '48' => "This processor does not accept partial reversals. The merchant attempted to settle for less than the originally authorized amount.",
        '49' => "A transaction amount greater than $99,999 will not be accepted.",
        '50' => "This transaction is awaiting settlement and cannot be Credits or refunds may only be performed against settled transactions. The transaction refunded. against which the credit/refund was submitted has not been settled, so a credit cannot be issued.",
        '51' => "The sum of all credits against this transaction is greater than the original transaction amount.",
        '52' => "The transaction was authorized, but the client could not be notified; the transaction will not be settled.",
        '53' => "The transaction type was invalid for ACH transactions. If x_Method = ECHECK, x_Type cannot be set to CAPTURE_ONLY.",
        '54' => "The referenced transaction does not meet the criteria for issuing a credit.",
        '55' => "The sum of credits against the referenced transaction would exceed the original debit amount. The transaction is rejected if the sum of this credit and prior credits exceeds the original debit amount",
        '56' => "This merchant accepts ACH transactions only; no credit card transactions are accepted. The merchant processes eCheck transactions only and does not accept credit cards.",
        '57' => "An error occurred in processing. Please try again in 5 minutes.",
        '58' => "An error occurred in processing. Please try again in 5 minutes.",
        '59' => "An error occurred in processing. Please try again in 5 minutes.",
        '60' => "An error occurred in processing. Please try again in 5 minutes.",
        '61' => "An error occurred in processing. Please try again in 5 minutes.",
        '62' => "An error occurred in processing. Please try again in 5 minutes.",
        '63' => "An error occurred in processing. Please try again in 5 minutes.",
        '64' => "The referenced transaction was not approved. This error is applicable to Wells Fargo SecureSource merchants only. Credits or refunds cannot be issued against transactions that were not authorized.",
        '65' => "This transaction has been declined. The transaction was declined because the merchant configured their account through the Merchant Interface to reject transactions with certain values for a Card Code mismatch.",
        '66' => "This transaction cannot be accepted for processing. The transaction did not meet gateway security guidelines.",
        '67' => "The given transaction type is not supported for this merchant. This error code is applicable to merchants using the Wells Fargo SecureSource product only. This product does not allow transactions of type CAPTURE_ONLY.",
        '68' => "The version parameter is invalid. The value submitted in x_Version was invalid.",
        '69' => "The transaction type is invalid. The value submitted in x_Type was invalid.",
        '70' => "The transaction method is invalid.The value submitted in x_Method was invalid.",
        '71' => "The bank account type is invalid. The value submitted in x_Bank_Acct_Type was invalid.",
        '72' => "The authorization code is invalid.The value submitted in x_Auth_Code was more than six characters in length.",
        '73' => "The driver's license date of birth is invalid. The format of the value submitted in x_Drivers_License_Num was invalid.",
        '74' => "The duty amount is invalid. The value submitted in x_Duty failed format validation.",
        '75' => "The freight amount is invalid. The value submitted in x_Freight failed format validation.",
        '76' => "The tax amount is invalid. The value submitted in x_Tax failed format validation.",
        '77' => "The SSN or tax ID is invalid. The value submitted in x_Customer_Tax_ID failed validation.",
        '78' => "The Card Code (CVV2/CVC2/CID) is invalid. The value submitted in x_Card_Code failed format validation.",
        '79' => "The driver's license number is invalid. The value submitted in x_Drivers_License_Num failed format validation.",
        '80' => "The driver's license state is invalid. The value submitted in x_Drivers_License_State failed format validation.",
        '81' => "The requested form type is invalid. The merchant requested an integration method not compatible with the ADC Direct Response API.",
        '82' => "Scripts are only supported in version 2.5. The system no longer supports version 2.5; requests cannot be posted to scripts.",
        '83' => "The requested script is either invalid or no longer supported. The system no longer supports version 2.5; requests cannot be posted to scripts.",
        '84' => "This reason code is reserved or not applicable to this API.",
        '85' => "This reason code is reserved or not applicable to this API.",
        '86' => "This reason code is reserved or not applicable to this API.",
        '87' => "This reason code is reserved or not applicable to this API.",
        '88' => "This reason code is reserved or not applicable to this API.",
        '89' => "This reason code is reserved or not applicable to this API.",
        '90' => "This reason code is reserved or not applicable to this API.",
        '91' => "Version 2.5 is no longer supported.",
        '92' => "The gateway no longer supports the requested method of integration.",
        '93' => "A valid country is required. This code is applicable to Wells Fargo SecureSource merchants only. Country is required field and must contain the value of a supported country.",
        '94' => "The shipping state or country is invalid. This code is applicable to Wells Fargo SecureSource merchants only.",
        '95' => "A valid state is required. This code is applicable to Wells Fargo SecureSource merchants only.",
        '96' => "This country is not authorized for buyers. This code is applicable to Wells Fargo SecureSource merchants only. Country is a required field and must contain the value of a supported country.",
        '97' => "This transaction cannot be accepted. Applicable only to SIM API. Fingerprints are only valid for a short period of time. This code indicates that the transaction fingerprint has expired.",
        '98' => "This transaction cannot be accepted. Applicable only to SIM API. The transaction fingerprint has already been used.",
        '99' => "This transaction cannot be accepted. Applicable only to SIM API. The server-generated fingerprint does not match the merchant-specified fingerprint in the x_FP_Hash field.",
        '100' => "The eCheck type is invalid. Applicable only to eCheck. The value specified in the x_Echeck_type field is invalid.",
        '101' => "The given name on the account and/or the account type does not match the actual account. Applicable only to eCheck. The specified name on the account and/or the account type do not match the NOC record for this account.",
        '102' => "This request cannot be accepted. A password or transaction key was submitted with this WebLink request. This is a high security risk.",
        '103' => "This transaction cannot be accepted. A valid fingerprint, transaction key, or password is required for this transaction.",
        '104' => "This transaction is currently under review. Applicable only to eCheck. The value submitted for country failed validation.",
        '105' => "This transaction is currently under review. Applicable only to eCheck. The values submitted for city and country failed validation.",
        '106' => "This transaction is currently under review. Applicable only to eCheck. The value submitted for company failed validation.",
        '107' => "This transaction is currently under review. Applicable only to eCheck. The value submitted for bank account name failed validation.",
        '108' => "This transaction is currently under review. Applicable only to eCheck. The values submitted for first name and last name failed validation.",
        '109' => "This transaction is currently under review. Applicable only to eCheck. The values submitted for first name and last name failed validation.",
        '110' => "This transaction is currently under review. Applicable only to eCheck. The value submitted for bank account name does not contain valid characters.",
        '111' => "A valid billing country is required. This code is applicable to Wells Fargo SecureSource merchants only.",
        '112' => "A valid billing state/province is This code is applicable to Wells Fargo",
        '127' => "The transaction resulted in an AVS mismatch. The address provided does not match billing address of cardholder. The system-generated void for the original AVS-rejected transaction failed.",
        '141' => "This transaction has been declined. The system-generated void for the original FraudScreen-rejected transaction failed.",
        '145' => "This transaction has been declined. The system-generated void for the original card code-rejected and AVS-rejected transaction failed.",
        '152' => "The transaction was authorized, but the client could not be notified; the transaction will not be settled. The system-generated void for the original transaction failed. The response for the original transaction could not be communicated to the client.",
        '165' => "This transaction has been declined. The system-generated void for the original card code-rejected transaction failed."
    );

    $bill_output['code'] = (($x_response_code==1) ? 1 : 2);
    $bill_output['billmes'] = $err[$x_response_reason_code];

    if(!empty($x_auth_code))            $bill_output['billmes'].= " (AuthCode: ".$x_auth_code.") ";
    if(!empty($x_trans_id))                $bill_output['billmes'].= " (TransID: ".$x_trans_id.") ";
    if(!empty($x_response_subcode))        $bill_output['billmes'].= " (subcode/reasoncode: ".$x_response_subcode.'/'.$x_response_reason_code.") ";
    if(!empty($x_avs_code))                $bill_output['avsmes']  = ($avserr[$x_avs_code] ? $avserr[$x_avs_code] : "AVS Code: ".$x_avs_code);
    if(!empty($x_CVV2_Resp_Code))        $bill_output['avsmes']  = ($cvverr[$x_CVV2_Resp_Code] ? $cvverr[$x_CVV2_Resp_Code] : "CVV Code: ".$x_CVV2_Resp_Code);

    if (isset($x_Amount)) {
        $payment_return = array(
            'total' => $x_Amount
        );
    }

    if ($x_type == 'auth_only')
        $bill_output['is_preauth'] = true;

    $extra_order_data = array(
        'authorizenet_sim_txnid' => $x_trans_id,
        'capture_status' => $x_type == 'auth_only' ? 'A' : ''
    );

    $weblink = 2;

    require($xcart_dir.'/payment/payment_ccend.php');

} else {

    if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

    if ($REQUEST_METHOD == 'POST' && $_POST['action'] == 'place_order') {
        $transaction_type = ($module_params['use_preauth'] == 'Y' || func_is_preauth_force_enabled($secure_oid)) ? 'C': 'P';
    }

    $transaction_type_name = func_check_cc_trans ('AuthorizeNet - SIM', $transaction_type, array("P" => "auth_capture", "C" => "auth_only", "X" => "prior_auth_capture", "V" => "void"));

    $follow_on = in_array($transaction_type_name, array('credit', 'prior_auth_capture', 'void'));

    // function from AuthorizeNET SIM implementation pakage
    function hmac ($key, $data) {
        // RFC 2104 HMAC implementation for php. Creates an md5 HMAC. Eliminates the need to install mhash to compute a HMAC. Hacked by Lance Rushing

        $b = 64; // byte length for md5
        if (strlen($key) > $b)
            $key = pack("H*",md5($key));
        $key  = str_pad($key, $b, chr(0x00));
        $ipad = str_pad('', $b, chr(0x36));
        $opad = str_pad('', $b, chr(0x5c));
        $k_ipad = $key ^ $ipad ;
        $k_opad = $key ^ $opad;

        return md5($k_opad . pack("H*",md5($k_ipad . $data)));
    }

    $loginid         = $module_params['param01'];
    $txnkey         = $module_params['param02'];
    $currencycode     = $module_params['param05'];

    if (!$follow_on) {

        $sequence = rand(1, 1000);

        $tstamp = (XC_TIME - (int)$module_params['param06']);

        $fp_hash = hmac($txnkey, $loginid . "^" . $sequence . "^" . $tstamp . "^" . $cart['total_cost'] . "^");

        $_orderids = $module_params ['param04'] . join("-", $secure_oid);

        if (!$duplicate) {
            db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($_orderids)."','".$XCARTSESSID."')");
        }

        $fp_array = addslashes(serialize(array($sequence, $tstamp, $fp_hash)));

        foreach($secure_oid as $oid) {

            func_array2insert(
                'order_extras',
                array(
                    'orderid'     => $oid,
                    'khash'     => 'fp_array',
                    'value'     => $fp_array,
                ),
                true
            );

        }

        $fields = array(
            'x_fp_sequence'         => $sequence,
            'x_fp_timestamp'         => $tstamp,
            'x_fp_hash'             => $fp_hash,
            'x_show_form'             => 'PAYMENT_FORM',
            'x_amount'                 => $cart['total_cost'],
            'x_method'                 => 'CC',
            'x_delim_data'          => 'FALSE',
            'x_first_name'             => $bill_firstname,
            'x_last_name'             => $bill_lastname,
            'x_phone'                 => $userinfo['phone'],
            'x_company'             => $userinfo['company'],
            'x_email'                 => $userinfo['email'],
            'x_cust_id'             => $userinfo['login'],
            'x_address'             => $userinfo['b_address'],
            'x_city'                 => $userinfo['b_city'],
            'x_state'                 => $userinfo['b_state'] ? $userinfo['b_state'] : 'n/a',
            'x_zip'                 => $userinfo['b_zipcode'],
            'x_country'             => $userinfo['b_country'],
            'x_ship_to_first_name'     => $userinfo['s_firstname'],
            'x_ship_to_last_name'     => $userinfo['s_lastname'],
            'x_ship_to_address'     => $userinfo['s_address'],
            'x_ship_to_city'         => $userinfo['s_city'],
            'x_ship_to_state'         => $userinfo['s_state'] ? $userinfo['s_state'] : 'n/a',
            'x_ship_to_zip'         => $userinfo['s_zipcode'],
            'x_ship_to_country'     => $userinfo['s_country'],
            'x_invoice_num'         => $_orderids,
            'x_relay_response'         => 'TRUE',
            'x_relay_url'             => $current_location . '/payment/cc_authorizenet_sim.php',
            'x_customer_ip'         => func_get_valid_ip($REMOTE_ADDR),
        );

    } else {

        $fields = array(
            'x_fp_sequence'     => $fp_array[0],
            'x_fp_timestamp'     => $fp_array[1],
            'x_fp_hash'         => $fp_array[2],
            'x_trans_id'         => $transaction_id,
            'x_version'         => '3.1',
            'x_delim_data'         => 'TRUE',
            'x_delim_char'         => ',',
            'x_encap_char'         => '|',
            'x_relay_response'     => 'FALSE',
        );

    }

    $url = 'https://secure.authorize.net/gateway/transact.dll';

    $common_fields = array(
        'x_test_request'     => $module_params['testmode'] == 'N' ? 'FALSE' : 'TRUE',
        'x_login'             => $loginid,
        'x_type'             => $transaction_type_name,
    );

    $fields = func_array_merge($fields, $common_fields);

    if (!$follow_on) {

        func_create_payment_form($url, $fields, 'Authorize.Net');

        exit;

    } else {

        x_load('http');

        $post = array();

        foreach ($fields as $var => $val) {
            $post[] = $var . '=' . $val;
        }

        list($a, $return) = func_https_request('POST', $url, $post);

        $mass = explode("|,|", "|," . $return);

        if($mass[1] == 1) {

            $bill_output['code'] = 1;

        } elseif($mass[1] == 4) {

            $bill_output['code'] = 3;
            $bill_output['billmes'] = $mass[4]." (Reason Code ".$mass[3]." / Sub ".$mass[2].")";

        } else {

            $bill_output['code'] = 2;
            $bill_output['billmes'] = ($mass[1]==2 ? "Declined" : "Error").": ";
            $bill_output['billmes'].= $mass[4]." (Reason Code ".$mass[3]." / Sub ".$mass[2].")";

        }

    }

}
?>
