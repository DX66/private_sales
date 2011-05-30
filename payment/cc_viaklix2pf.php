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
 * "viaKlix v2 - viaKlix provided form" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_viaklix2pf.php,v 1.18.2.1 2011/01/10 13:12:07 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ($_SERVER['REQUEST_METHOD'] == "GET" && (isset($_GET['errorCode']) || isset($_GET['ssl_result']))) {

    require './auth.php';

    if (!func_is_active_payment('cc_viaklix2pf.php'))
        exit;

    $results = array (
        'APPROVAL' => 'Approved',
        'APPROVED' => 'Approved',
        'ACCEPTED' => "Frequency Approval",
        "BAL.: 99999999.99" => "Debit Card Balance Inquiry Response",
        "PICK UP CARD" => "Pick up card",
        "AMOUNT ERROR" => "Tran Amount Error",
        "APPL TYPE ERROR" => "Call for Assistance",
        'DECLINED' => "Do Not Honor",
        "DECLINED-HELP 9999" => "System Error",
        "EXCEEDS BAL." => "Req. exceeds balance",
        "EXPIRED CARD" => "Expired Card",
        "INVALID CARD" => "Invalid Card",
        "INCORRECT PIN" => "Invalid PIN",
        "INVALID TERM ID" => "Invalid Terminal ID",
        "INVLD TERM ID 1" => "Invalid Merchant Number",
        "INVLD TERM ID 2" => "Invalid SE Number",
        "INVLD VOID DATA" => "Invalid Data",
        "SERV NOT ALLOWED" => "Invalid request",
        "MUST SETTLE MMDD" => "Must settle POS Device, open batch is more than 7 days old.",
        "ON FILE" => "Cardholder not found",
        "RECORD NOT FOUND" => "Record not on Host",
        "FOUND SERV NOT ALLOWED" => "Invalid request",
        "SEQ ERR PLS CALL" => "Call for Assistance",
        "CALL AUTH. CENTER" => "Refer to Issuer",
        "CALL REF.; 999999" => "Refer to Issuer",
        "DECLINE CVV2" => "Do Not Honor; Declined due to CVV2 mismatch \ failure"
    );

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

    $bill_output['sessid'] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='" . $ssl_invoice_number . "'");

    if (isset($errorCode)) {
        $bill_output['code'] = 2;
        $bill_output['billmes'] = $errorName . ': ' . $errorMsg . ' (error code: ' . $errorCode . ')';

    } else {

        x_session_register('secure_oid');

        $bill_output['code'] = $ssl_result == 0 ? 1 : 2;
        if (isset($ssl_result_message))
            $bill_output['billmes'] = isset($results[$ssl_result_message]) ? $results[$ssl_result_message] : $ssl_result_message;

        if (isset($ssl_txn_id))
            $bill_output['billmes'] .= ' (Transaction ID: ' . $ssl_txn_id . '; Approval code: ' . $ssl_approval_code . ')';

        if ($ssl_avs_response)
            $bill_output['avsmes'] = 'AVS Code: ' . $avserr[$ssl_avs_response] . ' (' . $ssl_avs_response . ')';

        if ($ssl_cvv2_response)
            $bill_output['cvvmes'] = 'CVV Code: ' . $cvverr[$ssl_cvv2_response] . ' (' . $ssl_cvv2_response . ')';

        if ($ssl_amount) {
            $payment_return = array(
                'total' => $ssl_amount
            );
        }

    }

    require($xcart_dir.'/payment/payment_ccend.php');

} else {

    if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

    $ssl_invoice_number = $module_params['param04'] . join('-', $secure_oid);

    if (!$duplicate)
        db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($ssl_invoice_number)."','".$XCARTSESSID."')");

    $post = array(
        'ssl_invoice_number' => $ssl_invoice_number,
        'ssl_merchant_id' => substr($module_params['param01'], 0, 15),
        'ssl_user_id' => substr($module_params['param07'], 0, 15),
        'ssl_pin' => substr($module_params['param02'], 0, 6),
        'ssl_customer_code' => '1111',
        'ssl_salestax' => $cart['tax_cost'],
        'ssl_description' => substr($ssl_invoice_number, 0, 255),
        'ssl_test_mode' => $module_params['testmode'] == 'Y' ? 'TRUE' : 'FALSE',

        'ssl_receipt_apprvl_method' => 'REDG',
        'ssl_receipt_apprvl_get_url' => $current_location . '/payment/cc_viaklix2pf.php',
        'ssl_receipt_decl_method' => 'REDG',
        'ssl_receipt_decl_get_url' => $current_location . '/payment/cc_viaklix2pf.php',

        'ssl_amount' => price_format($cart['total_cost']),
        'ssl_transaction_type' => 'SALE',

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
        'ssl_show_form' => 'true',

        'ssl_ship_to_company' => substr($userinfo['company'], 0, 50),
        'ssl_ship_to_first_name' => substr($userinfo['s_firstname'], 0, 20),
        'ssl_ship_to_last_name' => substr($userinfo['s_lastname'], 0, 30),
        'ssl_ship_to_address' => substr($userinfo['s_address'], 0, 30),
        'ssl_ship_to_city' => substr($userinfo['s_city'], 0, 30),
        'ssl_ship_to_state' => substr(($userinfo['s_state'] ? $userinfo['s_state'] : 'n/a'), 0, 10),
        'ssl_ship_to_country' => substr($userinfo['s_country'], 0, 50),
        'ssl_ship_to_zip' => substr($userinfo['s_zipcode'], 0, 10),
    );

    if ($module_params['param06'] == 'Y') {
        $post['ssl_avs_address'] = substr($userinfo['b_address'], 0, 30);
        $post['ssl_avs_zip'] = substr($userinfo['b_zipcode'], 0, 10);
    }

    if ($module_params['testmode'] == 'D')
        $url = "https://demo.viaKLIX.com/process.asp";
    else
        $url = "https://www.viaKLIX.com/process.asp";

    func_create_payment_form($url, $post, "viaKlix (Provided Form)");
}
exit;

?>
