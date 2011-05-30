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
 * "AuthorizeNet - AIM" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_authorizenet.php,v 1.81.2.6 2011/01/27 12:11:13 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load(
    'crypt',
    'http'
);

if (
    (
        $REQUEST_METHOD == 'POST'
        && $_POST['action'] == 'place_order'
    )
    || isset($cmpi_result)
) {
    $transaction_type = ($module_params['use_preauth'] == 'Y' || func_is_preauth_force_enabled($secure_oid)) ? 'C': 'P';
}

$transaction_type_name = func_check_cc_trans(
    'AuthorizeNet - AIM',
    $transaction_type,
    array(
        'P' => 'auth_capture',
        'C' => 'auth_only',
        'R' => 'credit',
        'X' => 'prior_auth_capture',
        'V' => 'void',
    )
);

$follow_on = in_array(
    $transaction_type_name, 
    array(
        'credit', 
        'prior_auth_capture', 
        'void',
    )
);

$avserr = array(
   'A' => "The street address matches, but the 5-digit ZIP code does not",
   'B' => "Address information was not submitted in the transaction information, so AVS check could not be performed",
   'E' => "The AVS data provided is invalid, or AVS is not allowed for the card type submitted",
   'G' => "The credit card issuing bank is of non-U.S. origin and does not support AVS",
   'N' => "Neither the street address nor the 5-digit ZIP code matches the address and ZIP code on file for the card",
   'R' => "AVS was unavailable at the time the transaction was processed. Retry transaction",
   'S' => "The U.S. card issuing bank does not support AVS",
   'U' => "Address information is not available for the customer's credit card",
   'W' => "The 9-digit ZIP code matches, but the street address does not match",
   'Y' => "The street address and the first 5 digits of the ZIP code match perfectly",
   'Z' => "The first 5 digits of the ZIP code matches, but the street address does not match",
);

$cvverr = array(
    'M' => 'Match',
    'N' => "No Match",
    'P' => "Not Processed",
    'S' => "Should have been present",
    'U' => "Issuer unable to process request",
);

$cavverr = array(
    '0' => "CAVV not validated because erroneous data was submitted",
    '1' => "CAVV failed validation",
    '2' => "CAVV passed validation",
    '3' => "CAVV validation could not be performed; issuer attempt incomplete",
    '4' => "CAVV validation could not be performed; issuer system error",
    '7' => "CAVV attempt - failed validation - issuer available (US issued card/non-US acquirer)",
    '8' => "CAVV attempt - passed validation - issuer available (US issued card/non-US acquirer)",
    '9' => "CAVV attempt - failed validation - issuer unavailable (US issued card/non-US acquirer)",
    'A' => "CAVV attempt - passed validation - issuer unavailable (US issued card/non-US acquirer)",
    'B' => "CAVV passed validation, information only, no liability shift",
);

$module_params['param01'] = text_decrypt($module_params['param01']);
$module_params['param02'] = text_decrypt($module_params['param02']);

if (is_null($module_params['param01'])) {
    x_log_flag('log_decrypt_errors', 'DECRYPT', "Could not decrypt the field 'param01' for AuthorizeNet: AIM CC payment module", true);
}

if (is_null($module_params['param02'])) {
    x_log_flag('log_decrypt_errors', 'DECRYPT', "Could not decrypt the field 'param02' for AuthorizeNet: AIM CC payment module", true);
}

$post = array(
    'x_login=' . $module_params['param01'],
    'x_tran_key=' . $module_params['param02'],
    'x_test_request=' . ($module_params['testmode'] == 'N' ? 'FALSE' : 'TRUE'),
    'x_delim_char=,',
    'x_encap_char=|',
    'x_type=' . $transaction_type_name,
    'x_method=CC',
    'x_allow_partial_Auth=TRUE',
);

if ($module_params['param08'] == 'cp_retail') {
    $post[] = 'x_market_type=2';
    $post[] = 'x_device_type=8';
    $post[] = 'x_response_format=1';
    $post[] = 'x_cpversion=1.0';
} else {
    $post[] = 'x_version=3.1';
    $post[] = 'x_delim_data=TRUE';
    $post[] = 'x_relay_response=FALSE';
}

if (
    defined('POSSIBLE_TRANSACTION_QUERY')
    && true === constant('POSSIBLE_TRANSACTION_QUERY')
) {

    $transaction = current($cart['split_query']['transaction_query'][$paymentid]);

    $transaction_id = $transaction['x_split_tender_id'];

    $post[] = "x_trans_id=" . $transaction_id;

}

if ($follow_on) {

    if (
        $transaction_type_name == 'credit' 
        && is_string($last_4_cc_num) 
        && strlen($last_4_cc_num) == 4
    ) {

        $post[] = "x_card_num=" . $last_4_cc_num;

    } elseif (
        $transaction_type_name == 'prior_auth_capture' 
        && isset($amount) 
        && is_numeric($amount) && $amount > 0
    ) {

        $post[] = "x_amount=" . price_format($amount);

    }

    if (isset($transaction_id)) {

        $post[] = 'x_trans_id=' . $transaction_id;

    } elseif (isset($x_split_tender_id)) {

        $post[] = 'x_split_tender_id' . $x_split_tender_id;

    }

} else {

    $post[] = "x_first_name=" . substr($bill_firstname, 0, 50);
    $post[] = "x_last_name=" . substr($bill_lastname, 0, 50);
    $post[] = "x_address=" . substr($userinfo['b_address'], 0, 60);
    $post[] = "x_company=" . substr(empty($userinfo['company']) ? 'n/a' : $userinfo['company'], 0, 50);
    $post[] = "x_city=" . substr($userinfo['b_city'], 0, 40);
    $post[] = "x_state=" . substr((!empty($userinfo['b_state']) && $userinfo['b_state'] != 'Other') ? $userinfo['b_state'] : "Non US", 0, 40);
    $post[] = "x_zip=" . substr($userinfo['b_zipcode'], 0, 20);
    $post[] = "x_country=" . substr($userinfo['b_country'], 0, 60);

    $post[] = "x_ship_to_first_name=" . ($userinfo['s_firstname'] ? $userinfo['s_firstname'] : $userinfo['firstname']);
    $post[] = "x_ship_to_last_name=" . ($userinfo['s_lastname'] ? $userinfo['s_lastname'] : $userinfo['lastname']);
    $post[] = "x_ship_to_address=" . $userinfo['s_address'];
    $post[] = "x_ship_to_company=" . $userinfo['company'];
    $post[] = "x_ship_to_city=" . $userinfo['s_city'];
    $post[] = "x_ship_to_state=" . ((!empty($userinfo['s_state']) && $userinfo['s_state']!="Other") ? $userinfo['s_state'] : "Non US");
    $post[] = "x_ship_to_zip=" . $userinfo['s_zipcode'];
    $post[] = "x_ship_to_country=" . $userinfo['s_country'];

    $post[] = "x_phone=" . substr(preg_replace('/\D/Ss', '', $userinfo["phone"]), 0, 25);
    $post[] = "x_fax=" . substr(preg_replace('/\D/Ss', '', $userinfo["fax"]), 0, 25);
    $post[] = "x_cust_id=" . substr($userinfo['login'], 0, 20);
    $post[] = "x_customer_ip=" . func_get_valid_ip($REMOTE_ADDR);
    $post[] = "x_email=" . substr($userinfo['email'], 0, 255);
    $post[] = "x_email_customer=FALSE";
    $post[] = "x_merchant_email=" . $config['Company']['orders_department'];
    $post[] = "x_invoice_num=" . substr($module_params['param04'] . join("-", $secure_oid), 0, 20);
    $post[] = "x_description=" . substr("Order(s) #" . join("-",$secure_oid) . "; customer: ".$userinfo['login'], 0, 255);
    $post[] = "x_amount=" . price_format($cart['total_cost']);
    $post[] = "x_recurring_billing=" . ($is_rbilling ? 'YES' : 'NO');
    $post[] = "x_card_num=" . $userinfo['card_number'];
    $post[] = "x_exp_date=" . $userinfo['card_expire'];
    $post[] = "x_card_code=" . $userinfo['card_cvv2'];
    $post[] = "x_tax=" . $cart['tax_cost'];
    $post[] = "x_freight=" . $cart['shipping_cost'];

    if (
        isset($cmpi_result) 
        && !empty($cmpi_result['Cavv'])
    ) {
        $post[] = "x_authentication_indicator=" . intval($cmpi_result['EciFlag']);
        $post[] = "x_cardholder_authentication_value=" . $cmpi_result['Cavv'];
    }
}

if (defined('AUTHORIZENET_DEBUG')) {
    func_pp_debug_log('authorizenet', 'I', "transaction_type = $transaction_type_name\n" . print_r($post, true));
}

$url = 'https://secure.authorize.net/gateway/transact.dll';

list(
    $a,
    $return
) = func_https_request(
    'POST',
    $url,
    $post,
    '&',
    '',
    "application/x-www-form-urlencoded",
    '',
    '',
    '',
    '',
    0,
    true
);

$mass = explode("|,|", "|," . $return);

if (defined('AUTHORIZENET_DEBUG')) {
    func_pp_debug_log('authorizenet', 'R', print_r($return, true) . print_r($mass, true));
}

// Fill response fields array
func_AIM_field('', $mass);

if (
    !empty($module_params['param06'])
    && in_array(func_AIM_field('ResponseCode'), array(1, 4))
    && !$follow_on
) {
    if (
        md5(
            $module_params['param06']
            . $module_params['param01']
            . func_AIM_field('TransactionID', false, $module_params['testmode'] != 'N' ? 0 : null)
            . price_format($cart['total_cost'])
        ) != strtolower(func_AIM_field('MD5hash'))
    ) {
        $mass = array(
            4 => "MD5 transaction signature is incorrect!", // ReasonText
            3 => 0, // ReasonCode
        );
        if ($module_params['param08'] == 'cp_retail') {
            $mass[2] = 3; // ResponseCode 
        } else {
            $mass[1] = 3; // ResponseCode
            $mass[2] = 0; // ResponseSubcode
        }

        // Set new values to response fields array
        func_AIM_field('', $mass);
    }
}

if (
    func_AIM_field('ReasonCode') == 295 
    && func_AIM_field('ResponseCode') == 4
) {

    // Split checkout possibility

    $split_checkout = array(
        'payment'       => 'Authorize.NET', // Payment name (core requirement)
        'id'            => func_AIM_field('TransactionID'), // Record identifier, must be unique with payment field (core requirement)
        'orderid'       => $orderids, // (core requirement)
        'paid_amount'   => func_AIM_field('Amount'), // (core requirement)
        'functions' =>  array( // Function structure (core requirement)
            'void'      => 'func_cc_authorize_instant_void', // to remove transaction instantly
            'complete'  => 'func_cc_authorize_instant_complete', // to complete transaction query instantly
            'params_void' => array( // parameters for void functions
                func_AIM_field('TransactionID'),
            ),
            'params_complete' => array( // parameters for complete functions
                func_AIM_field('SplitTenderID'),
            ),
        ),
        'x_split_tender_id' => func_AIM_field('SplitTenderID'), // split query parameter (specific for Authorize.Net)
    );

    $bill_output['code'] = 5;
    $bill_output['billmes'] = func_AIM_field('ReasonText');

} elseif (func_AIM_field('ResponseCode') == 1) {

    $bill_output['code'] = 1;
    $bill_output['billmes'] = " Approval Code: " 
        . func_AIM_field('AuthorizationCode') 
        . (func_AIM_field('TransactionID') != '' ? 
        "; Transaction ID: " . func_AIM_field('TransactionID') : "");

    if ($transaction_type_name == 'auth_only')
        $bill_output['is_preauth'] = true;

    $extra_order_data = array(
        'authorizenet_txnid'    => func_AIM_field('TransactionID'),
        'last_4_cc_num'         => substr($userinfo['card_number'], -4),
        'capture_status'        => $transaction_type_name == 'auth_only' ? 'A' : ''
    );

} elseif(func_AIM_field('ResponseCode') == 4) {

    $bill_output['code'] = 3;
    $bill_output['billmes'] = func_AIM_field('ReasonText') 
        . " (Reason Code " . func_AIM_field('ReasonCode') 
        . " / Sub " . func_AIM_field('ResponseSubcode') . ")";

} else {

    $bill_output['code'] = 2;
    $bill_output['billmes'] = (func_AIM_field('ResponseCode') == 2 ? "Declined" : "Error") . ": ";
    $bill_output['billmes'] .= 
        func_AIM_field('ReasonText') 
        . " (Reason Code " . func_AIM_field('ReasonCode') 
        . " / Sub " . func_AIM_field('ResponseSubcode') . ")";

}

if (func_AIM_field('AVSCode') != '')
    $bill_output['avsmes'] = 
        (empty($avserr[func_AIM_field('AVSCode')]) ? 
            "Code: " . func_AIM_field('AVSCode') 
            : $avserr[func_AIM_field('AVSCode')]);

if (func_AIM_field('CardCodeResponse') != '')
    $bill_output['cvvmes'] = 
        (empty($cvverr[func_AIM_field('CardCodeResponse')]) ? 
            "Code: " . func_AIM_field('CardCodeResponse') 
            : $cvverr[func_AIM_field('CardCodeResponse')]);

if (func_AIM_field('CardholderAuthenticationVerification') != '')
    $bill_output['cavvmes'] = 
        (empty($cavverr[func_AIM_field('CardholderAuthenticationVerification')]) ? 
            "Code: " . func_AIM_field('CardholderAuthenticationVerification') 
            : $cavverr[func_AIM_field('CardholderAuthenticationVerification')]);

?>
