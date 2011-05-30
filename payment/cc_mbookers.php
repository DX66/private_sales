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
 * MoneyBookers payment gateway
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_mbookers.php,v 1.44.2.1 2011/01/10 13:12:06 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ($_SERVER['REQUEST_METHOD'] == "GET" && !empty($_GET["status"]) && !empty($_GET["oid"])) {

    // User returns to store
    require './auth.php';

    if (defined('MBOOKERS_DEBUG')) {
        func_pp_debug_log('mbookers', 'R', $_GET);
    }

    $skey = $oid;
    if ($status == 'ok') {

        require $xcart_dir.'/payment/payment_ccview.php';

    } else {

        // User cancels the transaction

        $bill_output['sessid'] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] where ref='".$oid."'");
        $bill_output['billmes'] = "Cancelled by user";
        $bill_output['code'] = 2;

        require $xcart_dir.'/payment/payment_ccend.php';
    }

} elseif(

    $_SERVER['REQUEST_METHOD'] == "POST"
    && !empty($_POST['status'])
    && !empty($_POST['transaction_id'])
    && !empty($_POST['merchant_id'])
    && !empty($_POST['mb_transaction_id'])
) {

    // This is a callback from PG

    require './auth.php';

    $module_params = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor='cc_mbookers"
        . (
            (defined('MB_PAYMENT_METHOD') && MB_PAYMENT_METHOD == 'WLT')
            ? '_wlt'
            : ''
        )
        . ".php'");

    $sessid = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] where ref='" . $transaction_id . "'");

    // Check md5 signature

    $is_valid_sign = 'Y';
    $md5_string = strtoupper(md5($merchant_id . $transaction_id . strtoupper(md5(strtolower($module_params['param05']))) . $mb_amount . $mb_currency . $status));

    if (
        $md5_string != $md5sig
        || $orderid_hash != md5($transaction_id . $sessid)
        || $pay_to_email != $module_params['param01']
    ) {
        $is_valid_sign = '';
    }

    list($mb_status, $amount, $currency) = array($status, $amount, $currency);

    $bill_output['sessid'] = $sessid;
    $bill_output['code'] = ((empty($mb_status) || $mb_status == -2) ? 2 : ($mb_status == 2 ? 1 : 3));

    if (strlen($mb_amount) > 0) {
        $payment_return = array(
            'total' => $amount
        );

        if (strlen($mb_currency) > 0) {
            $payment_return['currency'] = $currency;
            $payment_return['_currency'] = $module_params['param02'];;
        }
    }

    if ($mb_status == 2) {
        $bill_output['billmes'] = "Status: Processed\n";
    } elseif ($mb_status == -2) {
        $bill_output['billmes'] = "Status: Failed\n";
    } elseif ($mb_status == 0) {
        $bill_output['billmes'] = "Status: Pending\n";
    } elseif ($mb_status == -1) {
        $bill_output['billmes'] = "Status: Cancelled\n";
    } elseif ($mb_status == -3) {
        $bill_output['billmes'] = "Status: Chargeback\n";
    } else {
        $bill_output['billmes'] = "Status: Unknown\n";
    }

    if ($is_valid_sign != 'Y') {
        $bill_output['code'] = 2;
        $bill_output['billmes'] .= "Warning! MD5 signature is not valid, order declined!\n";
    }

    $mb_details = "TransactionID: " . $mb_transaction_id . "\nMerchantID: " . $merchant_id . "\nPay from Email: " . $pay_from_email;
    $bill_output['billmes'] .= $mb_details;

    if (defined('MBOOKERS_DEBUG')) {
        func_pp_debug_log('mbookers', 'C', $_POST);
    }

    $skey = $transaction_id;

    include ($xcart_dir . '/payment/payment_ccmid.php');
    include ($xcart_dir . '/payment/payment_ccwebset.php');

} else {

    if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }
    x_load('mail', 'http');

    $ordr = $module_params['param04'].join("-",$secure_oid);
    $url = $current_location.'/payment/cc_mbookers'. ( (defined('MB_PAYMENT_METHOD') && MB_PAYMENT_METHOD == 'WLT') ? "_wlt" : "") .".php";
    $urla = "?oid=".$ordr."&status=";
    $pp_script = 'https://www.moneybookers.com/app/payment.pl';

    if (isset($userinfo['b_country']) && !empty($userinfo['b_country']))
        $b_country_a3 = func_query_first_cell("SELECT code_A3 FROM $sql_tbl[countries] WHERE code='".$userinfo["b_country"]."'");

    $post = array(
        "pay_to_email=" . $module_params['param01'],
        "payment_methods=" . (defined('MB_PAYMENT_METHOD') ? MB_PAYMENT_METHOD : "ACC"),
        "recipient_description=" . substr($config['Company']['company_name'],0,30),
        "transaction_id=" . $ordr,
        "return_url=" . $url.$urla.'ok',
        "cancel_url=" . $url.$urla.'nok',
        "status_url=" . $url,
        "language=" . $module_params['param03'],
        "prepare_only=" . 1,
        "pay_from_email=" . $userinfo['email'],
        "amount=" . $cart['total_cost'],
        "currency=" . $module_params['param02'],
        "firstname=" . $bill_firstname,
        "lastname=" . $bill_lastname,
        "address=" . $userinfo['b_address'],
        "phone_number=" . preg_replace('%[^0-9]%si', '', $userinfo["phone"]),
        "postal_code=" . preg_replace('%[^0-9a-zA-Z_]%si', '', $userinfo["b_zipcode"]),
        "city=" . $userinfo['b_city'],
        "state=" . $userinfo['b_state'],
        "country=" . $b_country_a3,
        "merchant_fields=" . "referring_platform,orderid_hash",
        "referring_platform=" . 'xcart',
        "orderid_hash=" . md5(addslashes($ordr) . $XCARTSESSID),
        "detail1_description=" . "Order #",
        "detail1_text=" . join("-", $secure_oid)
    );

    // Hide the prominent login section for credit card payment
    if ($post[1] == "payment_methods=ACC")
        $post[] = "hide_login=1";

    // Display logo at the payment page
    if (!empty($module_params['param06']))
        $post[] = "logo_url=".$module_params['param06'];

    if (!empty($module_params['param07']) && func_check_email($module_params['param07']))
        $post[] = 'status_url2=mailto: ' . $module_params["param07"];

    if (defined('MBOOKERS_DEBUG')) {
        func_pp_debug_log('mbookers', 'I', $post);
    }

    list($a, $return) = func_https_request('POST', $pp_script, $post);

    if (defined('MBOOKERS_DEBUG')) {
        func_pp_debug_log('mbookers', 'R', $return);
    }

    $sid = '';
    if (is_string($a)) {
        preg_match('%Set-Cookie: SESSION_ID=(.*?);%i', $a, $arr);
        $sid = $arr[1];
    }

    if (empty($sid) && is_string($return))
        $sid = $return;

    if (empty($sid)) {
        // PG did not return SESSION_ID
        $bill_output['billmes'] = func_get_langvar_by_name('err_payment_cc_not_available');
        $bill_output['code'] = 2;
        require $xcart_dir.'/payment/payment_ccend.php';
    }

    if (!$duplicate) {
        db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");
    }

    func_header_location($pp_script . "?sid=$sid");
}

exit;

?>
