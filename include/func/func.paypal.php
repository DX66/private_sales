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
 * PayPal functions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.paypal.php,v 1.45.2.3 2011/02/15 07:41:37 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load('payment');

define('X_PAYPAL_EC_TOKEN_TTL', 10800);

/**
 * Convert PayPal timestamp format to Unix timestamp
 */
function func_paypal_timestamp($ts)
{
    if (!preg_match("/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(?:\.\d{2})?Z$/", trim($ts), $match))
        return false;

    return gmmktime($match[4], $match[5], $match[6], $match[2], $match[3], $match[1]);
}

/**
 * Parse error for US version
 */
function func_paypal_prepare_errors($res)
{
    $errors = array();
    $i = 0;

    while(
        isset($res['l_errorcode' . $i])
        && isset($res['l_shortmessage' . $i])
        && isset($res['l_longmessage' . $i])
    ) {
        $errors[] = array(
            'code'     => $res['l_errorcode' . $i],
            'title' => $res['l_shortmessage' . $i],
            'desc'     => $res['l_longmessage' . $i]
        );

        $i++;
    }

    return $errors;
}

/**
 * Parse errors for UK version
 */
function func_paypal_prepare_errors_uk($res)
{
    if (empty($res)) {

        return array(
            'status'     => 'error',
            'error'     => "Unknown error",
        );

    } elseif (!isset($res[2])) {

        return array(
            'status'     => 'error',
            'error'     => "Unknown error: " . $res[1],
        );

    } elseif (
        isset($res[2]['result'])
        && $res[2]['result'] > 0
    ) {

        return array(
            'status'         => 'error',
            'error'         => $res[2]['result'] . "// " . $res[2]['respmsg'],
            'error_code'     => $res[2]['result']
        );

    }

    return false;
}

/**
 * Common request to PayPal US version
 */
function func_paypal_request($request, $regexp=false)
{
    global $pp_url, $pp_cert_file, $pp_signature, $pp_use_cert;

    x_load('http');

    if (defined('PAYPAL_DEBUG'))
        $log_str = "*** Request:\n\n$request\n\n";

    $post = explode("\n",$request);

    if ($pp_use_cert)
        list($headers, $response) = func_https_request('POST', $pp_url, $post, '', '', 'text/xml', '', $pp_cert_file);
    else
        list($headers, $response) = func_https_request('POST', $pp_url, $post, '', '', 'text/xml', '');

    if (
        function_exists('utf8_decode')
        && preg_match("/encoding=\"UTF-8\"/i", $response)
    ) {
        $response = utf8_decode($response);
    }

    if (defined('PAYPAL_DEBUG')) {

        $log_str .= "*** Response:\n\n$headers\n\n$response\n\n";

        x_log_add('paypal', $log_str);

    }

    if ($headers == '0') {
        return array(
            'success'     => false,
            'error'     => array('ShortMessage' => $response),
        );
    }

    $result = array (
        'headers'     => $headers,
        'response'     => $response,
    );

    if (!empty($regexp)) {

        $matches = array();

        preg_match($regexp, $response, $matches);

        $result['matches'] = $matches;

    }

    // Parse and fill common fields

    $result['success'] = false;

    $ord_fields = array (
        'Ack',
        'TransactionID',
        'Token', // Note: expires after three hours (Express Checkout Integration Guide, p30)
        'AVSCode',
        'CVV2Code',
        'PayerID',
        'PayerStatus',
        'FirstName',
        'LastName',
        'ContactPhone',
        'TransactionType', // e.g. express-checokut
        'PaymentStatus', // e.g. Pending
        'PendingReason', // e.g. authorization
        'ReasonCode',
        'GrossAmount',
        'FeeAmount',
        'SettleAmount',
        'TaxAmount',
        'ExchangeRate',
        'FMFDetails',
    );

    foreach ($ord_fields as $field) {
        if (preg_match('/<' . $field . '(?: [^>]*)?>([^>]+)<\/' . $field . '>/', $response, $out)) {
            $result[$field] = $out[1];
        }
    }

    if (!strcasecmp($result['Ack'], 'Success'))
        $result['success'] = true;

    if (!strcasecmp($result['Ack'], 'SuccessWithWarning')) {
        $result['success'] = true;
        $result['fmf'] = true;
        $result['filters'] = serialize(func_ps_paypal_pro_parse_filters($result['FMFDetails']));
    }

    if (preg_match('!<Payer(?:\s[^>]*)?>([^>]+)</Payer>!', $response, $out)) {
        $result['Payer'] = $out[1]; // e-mail address
    }

    if (preg_match('!<Errors[^>]*>(.+)</Errors>!', $response, $out_err)) {
        $error = array();

        if (preg_match('!<SeverityCode[^>]*>([^>]+)</SeverityCode>!', $out_err[1], $out))
            $error['SeverityCode'] = $out[1];

        if (preg_match('!<ErrorCode[^>]*>([^>]+)</ErrorCode>!', $out_err[1], $out))
            $error['ErrorCode'] = $out[1];

        if (preg_match('!<ShortMessage[^>]*>([^>]+)</ShortMessage>!', $out_err[1], $out))
            $error['ShortMessage'] = $out[1];

        if (preg_match('!<LongMessage[^>]*>([^>]+)</LongMessage>!', $out_err[1], $out))
            $error['LongMessage'] = $out[1];

        $result['error'] = $error;
    }

    if (preg_match('!<Address[^>]*>(.+)</Address>!', $response, $out)) {

        $out_addr = $out[1];

        $address = array();

        if (preg_match('!<Name[^>]*>([^>]+)</Name>!', $out_addr, $out)) {
            $__name = explode(' ',$out[1], 2);
            $address['FirstName'] = $__name[0];
            $address['LastName'] = $__name[1];
            unset($__name);
        }

        if (preg_match('!<Street1[^>]*>([^>]+)</Street1>!', $out_addr, $out))
            $address['Street1'] = $out[1];
        if (preg_match('!<Street2[^>]*>([^>]+)</Street2>!', $out_addr, $out))
            $address['Street2'] = $out[1];

        if (preg_match('!<CityName[^>]*>([^>]+)</CityName>!', $out_addr, $out))
            $address['CityName'] = $out[1];

        if (preg_match('!<StateOrProvince[^>]*>([^>]+)</StateOrProvince>!', $out_addr, $out))
            $address['StateOrProvince'] = $out[1];

        if (preg_match('!<Country[^>]*>([^>]+)</Country>!', $out_addr, $out))
            $address['Country'] = $out[1];

        if (preg_match('!<PostalCode[^>]*>([^>]+)</PostalCode>!', $out_addr, $out))
            $address['PostalCode'] = $out[1];

        if (preg_match('!<AddressOwner[^>]*>([^>]+)</AddressOwner>!', $out_addr, $out))
            $address['AddressOwner'] = $out[1];

        if (preg_match('!<AddressStatus[^>]*>([^>]+)</AddressStatus>!', $out_addr, $out))
            $address['AddressStatus'] = $out[1];

        $result['address'] = $address;
    }

    return $result;
}

/**
 * Create refund
 */
function func_paypal_create_refund($orderid, $amount, $note)
{
    global $sql_tbl;

    $data = func_query_first("SELECT $sql_tbl[orders].paymentid, $sql_tbl[order_extras].value FROM $sql_tbl[orders], $sql_tbl[order_extras] WHERE $sql_tbl[orders].orderid = $sql_tbl[order_extras].orderid AND $sql_tbl[order_extras].khash IN ('paypal_txnid', 'pnref') AND $sql_tbl[orders].orderid = '$orderid'");

    if (empty($data) || empty($data['value']))
        return false;

    $paypal = func_paypal_get_order_data($orderid);

    if (empty($paypal) || $paypal['no_refund_total'] <= 0)
        return false;

    $note = stripslashes($note);

    if ($paypal['method'] == 'UK') {

        $res = func_paypal_refund_uk($data['paymentid'], $data['value'], $note);

        if ($res['status'] == 'error')
            return array(
                array(
                    'desc' => $res['error']
                )
            );

        $res['refundtransactionid'] = $res['pnref'];
        $res['amount'] = $paypal['no_refund_total'];

    } else {

        if ($amount <= 0)
            return false;

        if ($amount > $paypal['no_refund_total'])
            $amount = $paypal['no_refund_total'];

        if ($amount == $paypal['no_refund_total'] && $amount == $paypal['order_total'])
            $amount = false;

        $res = func_paypal_refund($data['paymentid'], $data['value'], $amount, $note);

        $res['amount'] = $res['grossrefundamt'];

        if (empty($res))
            return false;

        if (strtolower($res['ack']) != 'success')
            return func_paypal_prepare_errors($res);
    }
    $res['note'] = $note;

    return func_paypal_reg_refund(
        $data['value'],
        $res['refundtransactionid'],
        $res
    );
}

/**
 * Registration refund record for PayPal transaction
 */
function func_paypal_reg_refund($txid, $refundid, $uk_refund_data = false)
{
    global $sql_tbl;

    $oids = func_query("SELECT $sql_tbl[orders].orderid, $sql_tbl[orders].paymentid, $sql_tbl[orders].extra FROM $sql_tbl[orders], $sql_tbl[order_extras] WHERE $sql_tbl[orders].orderid = $sql_tbl[order_extras].orderid AND $sql_tbl[order_extras].khash IN ('paypal_txnid', 'pnref') AND $sql_tbl[order_extras].value = '$txid'");

    if (empty($oids))
        return false;

    $refund = array();

    foreach($oids as $v) {

        $extra = unserialize($v['extra']);

        if (!isset($extra['paypal']))
            $extra['paypal'] = array();

        if (!isset($extra['paypal']['subtrans']))
            $extra['paypal']['subtrans'] = array();

        if ($uk_refund_data) {

            $extra['paypal']['subtrans'][$refundid] = array(
                'type'             => 'Refunded',
                'date'             => XC_TIME,
                'amount'         => $uk_refund_data['amount'],
                'note'             => $uk_refund_data['note'],
                'currencycode'     => $uk_refund_data['currencycode'],
            );

        } else {

            if (empty($refund)) {

                $refund = func_paypal_get_status($v['paymentid'], $refundid);

                if (empty($refund))
                    return false;

            }

            $extra['paypal']['subtrans'][$refundid] = array(
                'type'                 => 'Refunded',
                'pendingreason'     => $refund['pendingreason'],
                'reason'             => $refund['reasoncode'],
                'amount'             => abs($refund['amt']),
                'currencycode'         => $refund['currencycode'],
                'note'                 => $refund['note'],
                'date'                 => func_paypal_timestamp($refund['ordertime']),
            );
        }

        func_array2update(
            'orders',
            array(
                'extra' => addslashes(serialize($extra)),
            ),
            "orderid = '$v[orderid]'"
        );
    }

    return true;
}

/**
 * Update order's PayPal transaction data (only US version)
 */
function func_paypal_update_order($orderid)
{
    global $sql_tbl;

    $data = func_query_first("SELECT $sql_tbl[orders].paymentid, $sql_tbl[orders].extra, $sql_tbl[order_extras].value FROM $sql_tbl[orders], $sql_tbl[order_extras] WHERE $sql_tbl[orders].orderid = $sql_tbl[order_extras].orderid AND $sql_tbl[order_extras].khash = 'paypal_txnid' AND $sql_tbl[orders].orderid = '$orderid'");

    if (empty($data) || empty($data['value']))
        return false;

    $res = func_paypal_get_status($data['paymentid'], $data['value']);

    if (empty($res) || strtolower($res['ack']) != 'success')
        return false;

    $extra = unserialize($data['extra']);

    $extra['paypal']['main'] = array(
        'txnid'             => $data['value'],
        'txn_type'             => $res['transactiontype'],
        'address_status'     => $res['addressstatus'],
        'payer_id'             => $res['payerid'],
        'payment_status'     => $res['paymentstatus'],
        'payer_status'         => $res['payerstatus'],
        'payer_email'         => $res['email'],
        'receiver_id'         => $res['receiverid'],
        'amount'             => $res['amt'],
        'currencycode'         => $res['currencycode'],
        'pendingreason'     => $res['pendingreason'],
        'reasoncode'         => $res['reasoncode'],
        'update_date'         => XC_TIME,
    );

    func_array2update(
        'orders',
        array(
            'extra' => addslashes(serialize($extra)),
        ),
        "orderid = '$orderid'"
    );

    return true;
}

/**
 * Do RefundTransaction request (US version)
 */
function func_paypal_refund($paymentid, $txnid, $refund_type = true, $note = '')
{
    $query = array(
        'method'         => 'RefundTransaction',
        'TransactionID' => $txnid,
        'RefundType'     => ((is_numeric($refund_type) && !empty($refund_type)) ? 'Partial' : 'Full')
    );

    if ($query['RefundType'] == 'Partial')
        $query['Amt'] = $refund_type;

    if (!empty($note))
        $query['Note'] = $note;

    $res = func_paypal_do_nvp($paymentid, $query);

    if (empty($res))
        return false;

    return $res[2];
}

/**
 * Do GetTransactionDetails request (US version)
 */
function func_paypal_get_status($paymentid, $txnid)
{
    $res = func_paypal_do_nvp(
        $paymentid,
        array(
            'method'         => 'GetTransactionDetails',
            'TransactionID' => $txnid,
        )
    );

    if (empty($res))
        return false;

    return $res[2];
}

/**
 * Do Set ExpressCheckout request (UK version)
 */
function func_paypal_sec_uk($paymentid, $amount, $userinfo, $returnurl = false, $cancelurl = false)
{
    global $xcart_catalogs, $current_location, $shop_language, $pp_final_action;

    static $paypal_languages = array(
        'AU' => 'AU',
        'DE' => 'DE',
        'FR' => 'FR',
        'UK' => 'GB',
        'IT' => 'IT',
        'JP' => 'JP',
        'US' => 'US',
    );

    if (!$returnurl)
        $returnurl = $current_location.'/payment/ps_paypal_pro.php?mode=express_return';

    if (!$cancelurl)
        $cancelurl = $xcart_catalogs['customer'].'/cart.php?mode=checkout';

    $post = array(
        'action'                 => 'S',
        'trxtype'                 => $pp_final_action == 'Sale' ? 'S' : 'A',
        'tender'                 => 'P',
        'amt'                     => $amount,
        'currency'                 => true,
        'returnurl'             => $returnurl,
        'cancelurl'             => $cancelurl,
        'reqconfirmshipping'     => true,
        'pagestyle'             => true,
        'hdrimg'                 => true,
     );

    if (isset($paypal_languages[$shop_language]))
        $post['localecode'] = $paypal_languages[$shop_language];

    if (!empty($userinfo) && is_array($userinfo)) {
        $post['noshipping']     = "1";
        $post['shiptostreet']     = $userinfo['b_address'];
        $post['shiptocity']     = $userinfo['b_city'];
        $post['shiptocountry']     = $userinfo['b_country'];
        $post['shiptostate']     = $userinfo['b_state'];
        $post['shiptozip']         = $userinfo['b_zip'];
    }

    $res = func_paypal_do_uk($paymentid, $post);

    $err = func_paypal_prepare_errors_uk($res);

    if ($err)
        return $err;

    $res = $res[2];
    $res['status'] = 'success';

    $access = func_paypal_define_access($paymentid);

    $res['redirect_url'] = (
        $access['test_mode']
            ? "https://test-expresscheckout.paypal.com"
            : "https://www.paypal.com/uk"
    ) . '/cgi-bin/webscr?cmd=_express-checkout&token=' . $res['token'];

    return $res;
}

/**
 * Do GetExpressCheckout request (UK version)
 */
function func_paypal_gec_uk($paymentid, $token)
{
    global $pp_final_action;

    $res = func_paypal_do_uk(
        $paymentid,
        array(
            'action'     => 'G',
            'trxtype'     => $pp_final_action == 'Sale' ? 'S' : 'A',
            'tender'     => 'P',
            'token'     => $token,
        )
    );

    $err = func_paypal_prepare_errors_uk($res);

    if ($err)
        return $err;

    $res = $res[2];
    $res['status'] = 'success';

    return $res;
}

/**
 * Do DoExpressCheckout request (UK version)
 */
function func_paypal_dec_uk($paymentid, $token, $payerid, $amt, $userifo, $orderid, $secureid)
{
    global $pp_final_action;

    $res = func_paypal_do_uk(
        $paymentid,
        array(
            'action'         => 'D',
            'trxtype'         => $pp_final_action == 'Sale' ? 'S' : 'A',
            'tender'         => 'P',
            'token'         => $token,
            'payerid'         => $payerid,
            'amt'             => $amt,
            'invnum'         => $orderid,
            'custom'         => $secureid,
            'currency'         => true,
            'buttonsource'     => 'X-Cart_Cart_PRO2EC',
//            'notifyurl' => true,
            'shiptostreet'     => $userifo['s_address'],
            'shiptocity'     => $userifo['s_city'],
            'shiptostate'     => $userifo['s_state'],
            'shiptocountry' => $userifo['s_country'],
            'shiptozip'     => $userifo['s_zipcode'],
        )
    );

    $err = func_paypal_prepare_errors_uk($res);

    if ($err)
        return $err;

    $res = $res[2];
    $res['status'] = 'success';

    return $res;
}

/**
 * Do DirectPayment (Sale) request (UK version)
 */
function func_paypal_sdp_uk($paymentid, $userinfo, $cart, $order_secureid, $orderid, $cmpi_result = null)
{
    global $xcart_dir, $REMOTE_ADDR, $pp_final_action;

    require_once $xcart_dir . '/include/cc_detect.php';

    $is_extcard = false;
    $cardtype = 8;

    if (is_visa($userinfo['card_number']))
        $cardtype = 0;
    elseif (is_mc($userinfo['card_number']))
        $cardtype = 1;
    elseif (is_dc($userinfo['card_number']))
        $cardtype = 2;
    elseif (is_amex($userinfo['card_number']))
        $cardtype = 3;
    elseif (is_diners($userinfo['card_number']))
        $cardtype = 4;
    elseif (is_jcb($userinfo['card_number']))
        $cardtype = 5;
    elseif (is_solo($userinfo['card_number'])) {

        $is_extcard = true;

        $cardtype = 'S';

    } elseif (is_switch($userinfo['card_number'])) {

        $is_extcard = true;

        $cardtype = 9;
    }

    $post = array(
        'tender'         => 'C',
        'trxtype'         => $pp_final_action == 'Sale' ? 'S' : 'A',
        'acct'             => $userinfo['card_number'],
        'accttype'         => $cardtype,
        'amt'             => $cart['total_cost'],
        'street'         => $userinfo['b_address'],
        'city'             => $userinfo['b_city'],
        'state'         => $userinfo['b_state'],
        'country'         => $userinfo['b_country'],
        'zip'             => $userinfo['b_zipcode'],
        'buttonsource'     => 'X-Cart_Cart_PRO2DP',
        'clientip'         => func_get_valid_ip($REMOTE_ADDR),
        'currency'         => true,
        'custom'         => $order_secureid,
        'cvv2'             => $userinfo['card_cvv2'],
        'email'         => $userinfo['email'],
        'expdate'         => $userinfo['card_expire'],
        'invnum'         => $orderid,
//        'notifyurl' => true,
        'shiptostreet'     => $userinfo['s_address'],
        'shiptocity'     => $userinfo['s_city'],
        'shiptostate'     => $userinfo['s_state'],
        'shiptocountry' => $userinfo['s_country'],
        'shiptozip'     => $userinfo['s_zip'],
        'firstname'     => $userinfo['firstname'],
        'lastname'         => $userinfo['lastname'],
    );

    if ($is_extcard) {
        $post['cardissue'] = $userinfo['card_issue_no'];
        $post['cardstart'] = $userinfo["card_valid_from"];
    }

    if (isset($cmpi_result)) {
        $post['AUTHSTATUS3DS']     = $cmpi_result['PAResStatus'];
        $post['MPIVENDOR3DS']     = 'Y';
        $post['CAVV']             = $cmpi_result['Cavv'];
        $post['ECI']             = $cmpi_result['EciFlag'];
        $post['XID']             = $cmpi_result['Xid'];
    }

    $res = func_paypal_do_uk($paymentid, $post);

    $err = func_paypal_prepare_errors_uk($res);

    if ($err)
        return $err;

    $res = $res[2];
    $res['status'] = 'success';

    return $res;

}

/**
 * Do RefundTransaction request (UK version)
 */
function func_paypal_refund_uk($paymentid, $pnref, $note)
{
    $res = func_paypal_do_uk(
        $paymentid,
        array(
            'trxtype'     => 'C',
            'tender'     => 'C',
            'origid'     => $pnref,
            'memo'         => $note,
        )
    );

    $err = func_paypal_prepare_errors_uk($res);

    if ($err)
        return $err;

    $res = $res[2];
    $res['status'] = 'success';

    return $res;
}

/**
 * Do Capture (US & UK version)
 */
function func_paypal_capture($orderid)
{
    global $sql_tbl, $shop_language, $http_location, $https_location, $xcart_dir;

    $paypal = func_paypal_get_order_data($orderid);

    if (empty($paypal) || $paypal['capture_status'] == '')
        return false;

    if ($paypal['capture_status'] == 'A')
        return 'captured';

    if ($paypal['method'] == 'UK') {
        $res = func_paypal_do_uk(
            $paypal['paymentid'],
            array(
                'trxtype'     => 'D',
                'tender'     => 'C',
                'origid'     => $paypal['main']['txnid'],
            )
        );

        $err = func_paypal_prepare_errors_uk($res);

        if ($err)
            return $err;

        $res = $res[2];
        $res['status'] = 'success';

        return $res;

    } else {

        $query = array(
            'method'             => 'DoCapture',
            'AuthorizationID'     => $paypal['main']['txnid'],
            'AMT'                 => $paypal['order_total'],
            'completetype'         => 'COMPLETE',
        );

        $res = func_paypal_do_nvp(
            $paypal['paymentid'],
            array(
                'method'             => 'DoCapture',
                'AuthorizationID'     => $paypal['main']['txnid'],
                'AMT'                 => $paypal['order_total'],
                'completetype'         => 'COMPLETE',
            )
        );

        if (empty($res))
            return false;

        return $res[2];
    }
}

/**
 * Define access parameters for payment module
 */
function func_paypal_define_access($access)
{
    global $sql_tbl, $xcart_dir;

    if (empty($access))
        return false;

    if (is_array($access)) {

        if (!$access['vendor'] && !$access['partner']) {

            if (empty($access['user']) || empty($access['pwd']) || empty($access['access_value']))
                return false;

            if (!isset($access['test_mode']))
                $access['test_mode'] = false;

            if (!in_array($access['access_type'], array("C", "S"))) {

                $access['access_type'] = file_exists($access['access_value']) ? "C" : "S";

                if ($access['access_type'] == 'S' && file_exists($xcart_dir . '/payment/certs/' . $access['access_value'])) {

                    $access['access_type']     = 'C';
                    $access['access_value'] = $xcart_dir . '/payment/certs/' . $access['access_value'];

                }

            }

        }

    } else {

        global $config;

        $payment_method = func_query_first("SELECT $sql_tbl[ccprocessors].*, $sql_tbl[payment_methods].protocol FROM $sql_tbl[ccprocessors], $sql_tbl[payment_methods] WHERE $sql_tbl[ccprocessors].paymentid = '$access' AND $sql_tbl[ccprocessors].paymentid = $sql_tbl[payment_methods].paymentid");

        if (empty($payment_method)) {
            $payment_method = func_query_first("SELECT $sql_tbl[ccprocessors].*, $sql_tbl[payment_methods].protocol FROM $sql_tbl[ccprocessors], $sql_tbl[payment_methods] WHERE $sql_tbl[ccprocessors].processor = 'ps_paypal_pro.php' AND $sql_tbl[ccprocessors].paymentid = $sql_tbl[payment_methods].paymentid");
        }

        if (empty($payment_method) || ($payment_method['processor'] != 'ps_paypal_pro.php' && $payment_method['processor'] != 'ps_paypal.php'))
            return false;

        if ($config['paypal_solution'] == 'uk') {

            $access = array(
                'vendor'                 => $payment_method['param01'],
                'partner'                 => $payment_method['param02'],
                'user'                     => $payment_method['param04'],
                'pwd'                     => $payment_method['param05'],
                'reqconfirmshipping'     => $payment_method['param07'],
                'pagestyle'             => $payment_method['param08'],
                'hdrimg'                 => $payment_method['param09'],
            );

        } else {

            $access = array(
                'user'             => $payment_method['param01'],
                'pwd'             => $payment_method['param02'],
                'access_type'     => $payment_method['param07'],
                'access_value'     => $payment_method['param07'] == 'S'
                    ? $payment_method['param05']
                    : $xcart_dir . '/payment/certs/' . $payment_method['param04'],
            );

        }

        $access['test_mode']     = $payment_method['testmode'] == 'Y';
        $access['currency']     = func_paypal_get_currency($payment_method);
        $access['prefix']         = $payment_method['param06'];
        $access['protocol']     = $payment_method['protocol'];
    }

    return $access;
}

/**
 * Do NVP API request (US version)
 */
function func_paypal_do_nvp($access, $post)
{

    if (empty($post['method']))
        return false;

    $access = func_paypal_define_access($access);

    if (!$access)
        return false;

    $post['version']     = "56.0";
    $post['user']         = $access['user'];
    $post['pwd']         = $access['pwd'];

    if (!empty($access['access_value']) && $access['access_type'] == 'S')
        $post['signature'] = $access['access_value'];

    if (!empty($access['currency'])) {
        $post['currencycode'] = $access['currency'];
    }

    $str = array();

    foreach($post as $k => $v) {
        $str[] = strtoupper($k) . "=" . $v;
    }

    if (defined('PAYPAL_DEBUG')) {

        $log_str = "*** Request:\n\n" . implode("\n", $str);

        x_log_add('paypal', $log_str);

    }

    x_load('http');

    if ($access['access_type'] == 'C') {

        $url = $access['test_mode']
            ? "https://api.sandbox.paypal.com:443/nvp"
            : "https://api.paypal.com:443/nvp";

        list($headers, $response) = func_https_request('POST', $url, $str, "&", '', 'text/html', '', $access['access_value']);

     } else {

        $url = $access['test_mode']
            ? "https://api-3t.sandbox.paypal.com:443/nvp"
            : "https://api-3t.paypal.com:443/nvp";

        list($headers, $response) = func_https_request('POST', $url, $str, "&", '', 'text/html', '');
    }

    if (defined('PAYPAL_DEBUG')) {

        $log_str = "*** Response:\n\n$headers\n\n$response\n\n";

        x_log_add('paypal', $log_str);

    }

    if (!preg_match("/^\s*HTTP\/\d\.\d\s+200\s+/", $headers)) {
        return array(
            $headers,
            $response,
            "Server error: " . $response,
        );
    }

    $res = func_parse_str($response, '&', '=', 'urldecode');

    $result = array();

    foreach($res as $k => $v) {
        $result[strtolower($k)] = $v;
    }

    return array(
        $headers,
        $response,
        $result,
    );
}

/**
 * Get PayPal-specific order data
 */
function func_paypal_get_order_data($orderid)
{
    global $sql_tbl, $config;

    $orderid = intval($orderid);

    if ($orderid < 1)
        return false;

    $data = func_query_first_cell("SELECT extra FROM $sql_tbl[orders] WHERE orderid = '$orderid'");

    if (empty($data))
        return false;

    $type = func_query_first_cell("SELECT value FROM $sql_tbl[order_extras] WHERE orderid = '$orderid' AND khash = 'paypal_type'");
    $txnid = func_query_first_cell("SELECT value FROM $sql_tbl[order_extras] WHERE orderid = '$orderid' AND khash IN ('paypal_txnid', 'pnref')");

    $capture_status = func_query_first_cell("SELECT value FROM $sql_tbl[order_extras] WHERE orderid = '$orderid' AND khash = 'paypal_capture_status'");

    if (empty($txnid) || empty($type))
        return false;

    $_orders_result =  func_query_first("SELECT total, paymentid FROM " . $sql_tbl['orders'] . " WHERE orderid = '" . $orderid . "'");

    $result = array(
        'main'                 => array(
            'txnid' => $txnid,
        ),
        'method'             => substr($type, 0, 2),
        'api'                 => substr($type, 2),
        'capture_status'     => $capture_status,
        'no_refund_total'     => $_orders_result['total'],
        'paymentid'         => $_orders_result['paymentid'],
        'fmf'                 => func_query_first_cell("SELECT value FROM $sql_tbl[order_extras] WHERE orderid = '$orderid' AND khash = 'fmf'"),
        'filters'             => func_query_first_cell("SELECT value FROM $sql_tbl[order_extras] WHERE orderid = '$orderid' AND khash = 'filters'")
    );

    $result['order_total'] = $result['no_refund_total'];

    $data = unserialize($data);

    $is_save = false;

    if (isset($data['paypal'])) {

        if (isset($data['paypal']['subtrans'])) {

            foreach($data['paypal']['subtrans'] as $tid => $t) {

                if ($t['type'] != 'Refunded')
                    continue;

                func_unset($t, 'type');

                if (!isset($result['refunds']))
                    $result['refunds'] = array();

                if (isset($t['date']))
                    $t['date'] += $config["Appearance"]["timezone_offset"];

                $result['refunds'][$tid] = $t;
                $result['no_refund_total'] -= $t['amount'];

                if ($result['method'] == 'UK')
                    break;

            }

        }

        if (isset($data['paypal']['main']) && !empty($data['paypal']['main']) && is_array($data['paypal']['main'])) {

            if ($result['main']['txnid'] == $data['paypal']['main']['txnid']) {

                $result['main'] = func_array_merge($result['main'], $data['paypal']['main']);

            } else {

                func_unset($data['paypal'], 'main');

                $is_save = true;

            }

        }

        if (isset($result['main']['pendingreason']) && !empty($result['main']['pendingreason'])) {
            $result['main']['pendingreason_text'] = func_get_langvar_by_name('txt_paypal_pendingreason_' . $result['main']['pendingreason'], array(), false, true);
        }

        if (isset($result['main']['reasoncode']) && !empty($result['main']['reasoncode'])) {
            $result['main']['reasoncode_text'] = func_get_langvar_by_name('txt_paypal_reversereason_' . $result['main']['reasoncode'], array(), false, true);
        }

    }

    if ($result['method'] == 'UK' && !empty($result['refunds']))
        $result['no_refund_total'] = 0;

    if (!empty($result['filters'])) {
        $result['filters'] = unserialize($result['filters']);
    }

    if ($is_save)
        func_array2update(
            'orders',
            array(
                'extra' => addslashes(serialize($data)),
            ),
            "orderid = '$orderid'"
        );

    return $result;
}

/**
 * Do NVP API request (UK version)
 */
function func_paypal_do_uk($access, $post)
{
    $access = func_paypal_define_access($access);

    if (!$access) {
        return array('', "Inner error: The payment method is disabled or its configuration settings are incorrect");
    }

    x_load('http');

    $str = array();

    $str['vendor']     = $access['vendor'];
    $str['partner'] = $access['partner'];
    $str['user']     = $access['user'];
    $str['pwd']         = $access['pwd'];

    $requestid = isset($post['requestid']) ? $post['requestid'] : XC_TIME;

    func_unset($post, 'requestid');

    if (isset($post['currency']) && $access['currency'])
        $post['currency'] = $access['currency'];

    if (isset($post['invnum']))
        $post['invnum'] = $access['prefix'].$post['invnum'];

    if (isset($post['reqconfirmshipping']))
        $post['reqconfirmshipping'] = $access['reqconfirmshipping'] ? 1 : 0;

    if (isset($post['pagestyle']) && !empty($access['pagestyle']))
        $post['pagestyle'] = $access['pagestyle'];

    if (isset($post['hdrimg']) && !empty($access['hdrimg']))
        $post['hdrimg'] = $access['hdrimg'];

    if (isset($post['notifyurl'])) {

        global $https_location, $http_location;

        $post['notifyurl'] = (($access["protocol"] == "https") ? $https_location : $http_location) . "/payment/ps_paypal.php?notify_from=pro";

    }

    $str = func_array_merge($str, $post);

    $data = array();

    foreach($str as $k => $v) {

        $data[] = strtoupper($k) . "[" . strlen($v) . "]=" . $v;

    }

    $data = implode("&", $data);

    $url = $access['test_mode']
        ? "https://pilot-payflowpro.paypal.com:443"
        : "https://payflowpro.paypal.com:443";

    $headers = array(
        "X-VPS-REQUEST-ID"                     => $requestid,
        "X-VPS-VIT-CLIENT-CERTIFICATION-ID" => '7894b92104f04ffb4f38a8236ca48db3',
    );

    list($headers, $response) = func_https_request('POST', $url, $data, '', '', "application/x-www-form-urlencoded", '', '', '', $headers);

    if (empty($response))
        return array($headers, $response);

    $result = array();

    $tmp = array();

    parse_str($response, $tmp);

    if (empty($tmp) || !is_array($tmp))
        return array($headers, $response);

    foreach($tmp as $k => $v) {

        $result[strtolower($k)] = urldecode($v);

    }

    return array($headers, $response, $result);
}

/**
 * Detect state by PayPal response data
 */
function func_paypal_detect_state($country, $state, &$err)
{
    global $sql_tbl;

    if (empty($state))
        return '';

    $state         = func_addslashes($state);
    $country     = func_addslashes($country);

    $state_exists = (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[states] WHERE country_code = '$country' AND code = '$state'") > 0);

    if ($state_exists)
        return $state;

    $country_data = func_query_first("SELECT code, display_states FROM $sql_tbl[countries] WHERE code = '$country' AND active = 'Y'");

    if (empty($country_data)) {
        $err = 1;
        return '';
    }

    if ($country_data['display_states'] != 'Y')
        return $state;

    $has_states = (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[states] WHERE country_code = '$country'") > 0);

    if (!$has_states)
        return $state;

    $state_code = func_query_first_cell("SELECT code FROM $sql_tbl[states] WHERE state = '$state' AND country_code = '$country'");

    if (!empty($state_code))
        return $state_code;

    $err = 2;

    return func_query_first_cell("SELECT code FROM $sql_tbl[states] WHERE country_code = '$country'");

}

function func_paypal_get_capture_orderid($txnid)
{
    global $sql_tbl;

    $res = func_query_column("SELECT cs.orderid FROM $sql_tbl[order_extras] as cs INNER JOIN $sql_tbl[order_extras] as txnid ON txnid.khash = 'paypal_txnid' AND txnid.value = '$txnid' WHERE cs.khash = 'capture_status' AND cs.value = 'A' AND cs.orderid = txnid.orderid");

    return $res
        ? array_unique($res)
        : false;
}

function func_ps_paypal_pro_do_capture($order)
{
    global $config;

    if ($order['order']['paypal']['fmf'])
        return array(
            false,
            func_get_langvar_by_name('txt_paypal_fmf_note', array(), false, true),
        );

    if ($config['paypal_solution'] == 'uk') {

        if ($order['order']['paypal']['method'] != 'UK')
            return array(false, false);

        $extra = array(
            'name'     => 'pnref',
            'value' => $order['order']['paypal']['main']['txnid'],
        );

        $post = array(
            'tender'     => 'C',
            'trxtype'     => 'D',
            'origid'     => $order['order']['paypal']['main']['txnid'],
        );

        $res = func_paypal_do_uk($order['order']['paymentid'], $post);

        $err = func_paypal_prepare_errors_uk($res);

        if ($err && $err['error_code'] == '111')
            return array(
                true,
                $err['error'], $extra
            );

        return array(
            !$err,
            ($err ? $err['error'] : ''),
            $extra);

    } else {

        if ($order['order']['paypal']['method'] != 'US')
            return array(
                false,
                false,
            );

        $query = array(
            'method'             => 'DoCapture',
            'AuthorizationID'     => $order['order']['paypal']['main']['txnid'],
            'AMT'                 => $order['order']['total'],
            'CompleteType'         => 'Complete',
        );

        $res = func_paypal_do_nvp($order['order']['paymentid'], $query);

        $status = false;

        $err_msg = false;

        if ($res) {

            if (strtolower($res[2]['ack']) != 'success') {

                $err = func_paypal_prepare_errors($res[2]);

                foreach ($err as $e) {

                    if ($e['code'] == 10602)
                        $status = true;

                    $err_msg .= $e['desc'] . " (error code: " . $e['code'] . ")<br />\n";

                }

            } else {

                $status = true;

            }

        }

        $extra = array(
            'name'     => 'paypal_txnid',
            'value' => $order['order']['paypal']['main']['txnid'],
        );

        return array(
            $status,
            $err_msg,
            $extra,
        );

    }

}

function func_ps_paypal_pro_do_void($order)
{
    global $config;

    if ($config['paypal_solution'] == 'uk') {

        if ($order['order']['paypal']['method'] != 'UK')
            return array(
                false,
                false,
            );

        $extra = array(
            'name'     => 'pnref',
            'value' => $order['order']['paypal']['main']['txnid'],
        );

        $post = array(
            'tender'     => 'C',
            'trxtype'     => 'V',
            'origid'     => $order['order']['paypal']['main']['txnid'],
        );

        $res = func_paypal_do_uk($order['order']['paymentid'], $post);

        $err = func_paypal_prepare_errors_uk($res);

        if ($err && $err['error_code'] == '12')
            return array(
                true,
                $err['error'],
                $extra,
            );

        return array(
            !$err,
            ($err ? $err['error'] : ''),
            $extra,
        );

    } else {

        if ($order['order']['paypal']['method'] != 'US')
            return array(false, false);

        $query = array(
            'method'             => 'DoVoid',
            'AuthorizationID'     => $order['order']['paypal']['main']['txnid'],
        );

        $res = func_paypal_do_nvp($order['order']['paymentid'], $query);

        $status = false;

        $err_msg = false;

        if ($res) {

            if (strtolower($res[2]['ack']) != 'success') {

                $err = func_paypal_prepare_errors($res[2]);

                foreach ($err as $e) {

                    if ($e['code'] == 10600)
                        $status = true;

                    $err_msg .= $e['desc'] . " (error code: " . $e['code'] . ")<br />\n";
                }

            } else {

                $status = true;

            }

        }

        $extra = array(
            'name'     => 'paypal_txnid',
            'value' => $order['order']['paypal']['main']['txnid'],
        );

        return array(
            $status,
            $err_msg,
            $extra,
        );

    }

}

function funct_ps_paypal_pro_fmf($orderid, $approve)
{
    global $sql_tbl;

    $order = func_order_data($orderid);

    if ($config['paypal_solution'] == 'uk') {

       if ($order['order']['paypal']['method'] != 'UK')
            return array(
                false,
                false,
            );

/*
        $extra = array(
            'name' => 'pnref',
            'value' => $order['order']['paypal']['main']['txnid']
        );

        $post = array(
            'tender' => 'C',
            'trxtype' => 'V',
            'origid' => $order['order']['paypal']['main']['txnid']
        );

        $res = func_paypal_do_uk($order['order']['paymentid'], $post);

        $err = func_paypal_prepare_errors_uk($res);

        if ($err && $err['error_code'] == '12')
            return array(true, $err['error'], $extra);

        return array(!$err, ($err ? $err['error'] : ''), $extra);
*/
    } else {

        if ($order['order']['paypal']['method'] != 'US')
            return array(
                false,
                false,
            );

        $query = array(
            'method'         => 'ManagePendingTransactionStatus',
            'transactionid' => $order['order']['paypal']['main']['txnid'],
            'action'         => $approve ? 'Accept' : 'Deny',
        );

        $res = func_paypal_do_nvp($order['order']['paymentid'], $query);

        $status = false;

        $err_msg = false;

        if ($res) {

            if (strtolower($res[2]['ack']) != 'success') {

                $err = func_paypal_prepare_errors($res[2]);

                foreach ($err as $e) {
                    $err_msg .= $e['desc'] . " (error code: " . $e['code'] . ")<br />\n";
                }

            } else {

                $status = true;

                $orderids = func_get_order_ids($order['order']['orderid']);

                db_query("UPDATE $sql_tbl[order_extras] SET value = '0' WHERE khash = 'fmf' AND orderid IN ('" . implode("','", $orderids). "')");

                if ($approve) {

                    // Change status of all orders to P if current status is Q
                    $current_status = func_query_first_cell("SELECT status FROM $sql_tbl[orders] WHERE orderid = '" . $order['order']['orderid']. "'");

                    if ($current_status == 'Q') {

                        foreach ($orderids as $oid) {

                            func_change_order_status($oid, 'P');

                        }

                    }

                } else {

                    // Decline all orders with this transaction if transaction is declined
                    foreach ($orderids as $oid) {

                        func_change_order_status($oid, 'D');

                    }

                }

            }

        }

        return array(
            $status,
            $err_msg,
        );

    }

}

function func_ps_paypal_do_capture($order)
{
    return func_ps_paypal_pro_do_capture($order);
}

function func_ps_paypal_do_void($order)
{
    return func_ps_paypal_pro_do_void($order);
}

/**
 * Parse FMF details
 * TODO (wait bugfix in PayPal - Aug 2009)
 */
function func_ps_paypal_pro_parse_filters($response)
{
    return array();
}

function func_paypal_check_ec_token_ttl()
{
    global $paypal_token, $paypal_token_ttl;

    x_session_register('paypal_token_ttl');
    x_session_register('paypal_token');

    if (
        !empty($paypal_token)
        && $paypal_token_ttl + X_PAYPAL_EC_TOKEN_TTL < XC_TIME
    ) {

        func_paypal_clear_ec_token();

        return false;

    }

    return true;
}

function func_paypal_clear_ec_token()
{
    global $paypal_token, $paypal_express_details, $paypal_token_ttl;

    x_session_register('paypal_token');
    x_session_register('paypal_express_details');
    x_session_register('paypal_token_ttl');

    $paypal_token             = false;
    $paypal_express_details = false;
    $paypal_token_ttl         = false;

    return true;
}

/*
 * Check if ItemTotal = sum(Amount*Quantity) and
 * OrderTotal = ItemTotal + ShippingTotal + TaxTotal + HandlingTotal
 */
function func_paypal_is_line_items_allowed($cart, $pp_total = 0)
{
    if (empty($cart))
        return array();

    $pp_currency = func_paypal_get_currency();

    if (empty($pp_total))
        $pp_total = func_paypal_convert_to_BasicAmountType($cart["total_cost"], $pp_currency);

    $result = array(
        'OrderTotal' => $pp_total,
        'ItemTotal' => $pp_total,
        'ShippingTotal' => 0,
        'TaxTotal' => 0,
        'HandlingTotal' => 0
    );

    if (isset($cart['display_subtotal']))
        $result['ItemTotal'] = $cart['display_subtotal'];
   
    if (isset($cart['display_shipping_cost']))
        $result['ShippingTotal'] = $cart['display_shipping_cost'];

    $delta = 0.0000000001;
    $result['TaxTotal'] = $result['OrderTotal'] - $result['ItemTotal'] - $result['ShippingTotal'];

    if (abs($result['TaxTotal']) < $delta)
        $result['TaxTotal'] = 0;

    settype($cart['products'], 'array');
    $products_total = 0;
    foreach ($cart['products'] as $p) {
        $price = func_paypal_convert_to_BasicAmountType($p["display_price"], $pp_currency);
        $products_total += $price * $p['amount'];
    }

    if (
        $result['TaxTotal'] < 0
        || abs($products_total - $result['ItemTotal']) > $delta
    ) {
        return array();
    } else {
        return $result;
    }
}

function func_paypal_get_payment_details_items_soap($products)
{
   
    if (empty($products))
        return '';

    x_load('clean_urls');

    $pp_currency = func_paypal_get_currency();

    $out = '';
    foreach ($products as $p) {
        $name = substr($p['product'], 0, 127);
        $url = func_get_resource_url("P", $p['productid']);
        
        $amount = func_paypal_convert_to_BasicAmountType($p["display_price"], $pp_currency);
        $out .= <<<EOT

          <PaymentDetailsItem>
            <Name>$name</Name>
            <Amount currencyID="$pp_currency">$amount</Amount>
            <Quantity>$p[amount]</Quantity>
            <ItemURL>$url</ItemURL>
          </PaymentDetailsItem>
EOT;
    }     

    return $out . "\n";
}

function func_paypal_convert_to_BasicAmountType($val, $pp_curr='')
{
    settype($val, 'float');

    if (empty($pp_curr))
        $pp_curr = func_paypal_get_currency();

    if (in_array($pp_curr, array('JPY','TWD','HUF'))) {
        #bt:0089724
        $new_val = sprintf("%0.2f", floor($val));
    } else {
        $new_val = sprintf("%0.2f", $val);
    }

    return $new_val;
}

function func_paypal_get_currency($params = array())
{
    if (empty($params)) {
        global $module_params;
        return isset($module_params['param03']) ? $module_params['param03'] : 'USD';
    } else {
        return isset($params['param03']) ? $params['param03'] : 'USD';
    }
}

/**
 * Get payment_cc_data(Fast_Lane_Checkout) and get payment_data(XPayments_Connector) for emulated_paypal
 * Disable Direct paypal payment_template for emulated_paypal 
 */
function func_paypal_adjust_payment_data($payment_data, $checkout_module)
{
    global $sql_tbl, $active_modules;

    $payment_cc_data = $_cc_data = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor='ps_paypal_pro.php'");

    if ($checkout_module == 'One_Page_Checkout') {
        func_unset($_cc_data, "paymentid", "processor");
        $payment_data = func_array_merge($payment_data, $_cc_data);
    }

    $is_emulated_paypal = false;

    if (!empty($active_modules['XPayments_Connector'])) {
        func_xpay_func_load();
        $is_emulated_paypal = xpc_is_emulated_paypal($payment_data['paymentid']);
        if ($is_emulated_paypal) {
            $payment_cc_data = xpc_get_module_params($payment_data['paymentid']);
            $payment_data['payment_template'] = false;
        }
    }

    if ($checkout_module == 'One_Page_Checkout')
        return array($payment_data); //For One_Page_Checkout
    else
        return array($payment_data, $payment_cc_data); //For Fast_Lane_Checkout
}

?>
