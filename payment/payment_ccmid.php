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
 * Middle phase of the payment action
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: payment_ccmid.php,v 1.48.2.1 2011/01/10 13:12:08 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load(
    'http',
    'order',
    'payment',
    'tests'
);

if (!defined('GET_LANGUAGE')) {

    // Let's include language
    $current_area = 'C';

    require($xcart_dir . '/include/get_language.php');

}

/**
 * This code reports error and inserts order
 */
/*
$bill_output['code'];       error code
$bill_output['billmes'];    bill reason
$bill_output['cvvmes'];     cvv info
$bill_output['avsmes'];     avs info
$bill_output['cavvmes'];    cavv info
$bill_output['sessid'];     session id for restoring session in web-based payment processors
$weblink                    if 2 - JS autoredirector; if 1 - use "<a href=...>Return to X-Cart</a>, else "header_location(...);"
*/

$log_payment_failure = false;

if (!empty($bill_output['sessid'])) {

    // check the security

    if (func_check_webinput() == 'err') {

        $log_payment_failure = true;

        if ($bill_output['code'] == 1) {

            $__transaction_status = 'successful';

            $bill_output['code'] = 3;

        } elseif ($bill_output['code'] == 3) {

            $__transaction_status = 'queued';

        } else {

            $__transaction_status = 'declined';

        }

        $bill_output['billmes'] = "Gateway reported of $__transaction_status transaction but it's response came from the IP that is not specified in the list of valid IPs: ".func_get_valid_ip($_SERVER["REMOTE_ADDR"])."\n-- response ----\n".$bill_output["billmes"];

    }

    $sessurl = $XCART_SESSION_NAME . "=" . $bill_output['sessid'] . "&";

    x_session_id($bill_output['sessid']);

    x_session_register('cart');
    x_session_register('secure_oid');
    x_session_register('initial_state_orders', array());
    x_session_register('initial_state_show_notif', 'Y');

    $orderids = $secure_oid;

} else {

    $sessurl = '';

}

$bill_error = $reason = '';

$fatal = false;

$saved_bill_output = empty($bill_output)
    ? false
    : $bill_output;

if (!empty($skey)) {

    // web+callback
    func_array2update(
        'cc_pp3_data',
        array(
            'is_callback' => 'Y',
        ),
        "ref = '" . $skey . "'"
    );

    $__tmp = func_query_first_cell("SELECT trstat FROM $sql_tbl[cc_pp3_data] WHERE ref = '" . $skey . "'");

    $__oids = explode('|',$__tmp);

    array_shift($__oids);

    $orderids = $__oids;

}

if (empty($orderids)) {

    // order was lost
    $bill_error = 'error_ccprocessor_error';

    $bill_output['billmes'] = "Error: Your order was lost";

    $reason = "&bill_message=" . urlencode($bill_output['billmes']);

    $fatal = true;

} elseif (
    empty($cart)
    && empty($skey)
) {

    // cart was lost
    $bill_error = 'error_ccprocessor_error';

    $bill_output['billmes'] = "Error: Your cart was lost";

    $reason = "&bill_message=" . urlencode($bill_output['billmes']);

    $fatal = true;

} elseif ($bill_output['code'] == 3) {

    // queue

    if ($bill_output['hide_mess']) {

        $reason = "&bill_message=" . urlencode(func_get_langvar_by_name('txt_payment_transaction_is_queued', array(), false, true, true));

    } else {

        $reason = "&bill_message=" . urlencode($bill_output['billmes']);

    }

} elseif ($bill_output['code'] == 2) {

    // declined
    $bill_error = 'error_ccprocessor_error';

    if ($bill_output['hide_mess']) {

        if ($bill_output['is_error']) {

            $reason = "&bill_message=" . urlencode(func_get_langvar_by_name('txt_payment_transaction_error', array(), false, true, true));

        } else {

            $reason = "&bill_message=" . urlencode(func_get_langvar_by_name('txt_payment_transaction_is_failed', array(), false, true, true));

        }

    } else {

        $reason = "&bill_message=" . urlencode($bill_output['billmes']);

    }

} elseif ($bill_output['code'] == 1) {

    // approved

    // Response checking
    if (
        isset($payment_return)
        && !empty($payment_return)
    ) {

        if (isset($payment_return['total'])) {

            $_oids = is_array($orderids)
                ? $orderids
                : array($orderids);

            $sum = 0;

            foreach ($_oids as $_oid) {

                $o = func_order_data($_oid);

                $sum += $o['order']['total'];

            }

            if (sprintf("%01.2f", $sum) != sprintf("%01.2f", doubleval($payment_return['total']))) {

                $bill_output['code'] = 2;

                $bill_output['billmes'] .= "; Payment amount mismatch: wrong order total ( ".sprintf("%01.2f", $sum)." <> ".sprintf("%01.2f", doubleval($payment_return['total']))." ).";

            }

        } // if (isset($payment_return['total']))

        if (
            $bill_output['code'] != 2
            && isset($payment_return['currency'])
            && isset($payment_return['_currency'])
            && !empty($payment_return['_currency'])
            && $payment_return['currency'] != $payment_return['_currency']
        ) {

            $bill_output['code'] = 2;

            $bill_output['billmes'] .= "; Payment amount mismatch: wrong order currency ( ".$payment_return['currency']." <> ".$payment_return['_currency']." ).";

        }

    }

    if ($bill_output['code'] == 1) {

        $bill_output['billmes'] = 'Approved'
            . (
                empty($bill_output['billmes'])
                    ? '.'
                    : ': ' . $bill_output["billmes"]
            );

    } else {

        $bill_error = 'error_ccprocessor_error';

        $reason = "&bill_message=" . urlencode($bill_output['billmes']);

        $bill_output['billmes'] = "Declined: " . $bill_output['billmes'];

    }

} elseif ($bill_output['code'] == 4) {

    // CMPI declined

    $bill_error = 'error_cmpi_error';

    $reason = "&bill_message=" . urlencode($bill_output['billmes']);

} elseif (
    $bill_output['code'] == 5
    && !empty($split_checkout)
) {

    // SPLIT CHECKOUT
    if (
        empty($cart['split_query'])
        || $cart['split_query']['orderid'] == $split_checkout['orderid']
    ) {

        if (!isset($cart['split_query']['cart_hash'])) {

            $cart['split_query']['cart_hash'] = func_calculate_cart_hash($cart);

        }

        $split_checkout['paymentid'] = $paymentid;
        $split_checkout['module_params'] = $module_params;

        $cart['split_query']['orderid'] = $split_checkout['orderid'];

        $cart['split_query']['transaction_query'][$paymentid][$split_checkout['id']] = $split_checkout;

        $cart['split_query']['paid_amount'] = func_calculate_paid_amount($cart);

        func_store_split_checkout_data($cart['split_query']);

    } else {

        $cart['split_query'] = '';

    }

} else {

    // unavailable

    $bill_error = 'error_ccprocessor_unavailable';

    $bill_output['billmes'] = "Error: Payment gateway is unavailable";

}

if (
    !empty($cart['split_query'])
    && $bill_output['code'] == 1
) {

    // If the order is successfully processed and there are several transaction queries in split checkout
    // then the core must complete open queries

    func_process_split_checkout($cart['split_query'], $paymentid);

}

if (
    $log_payment_failure
    || (!empty($bill_error))
) {

    x_session_register('logged_paymentid');

    $payment_name = "unable to determine";

    if (!empty($logged_paymentid)) {

        $method_name = func_query_first_cell("SELECT payment_method FROM $sql_tbl[payment_methods] WHERE paymentid='$logged_paymentid'");

        $payment_module_info = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE paymentid='$logged_paymentid'");

        if (
            empty($payment_module_info)
            && !empty($method_name)
        ) {

            // for PayPal Pro
            $payment_module_info = func_query_first("SELECT $sql_tbl[ccprocessors].* FROM $sql_tbl[ccprocessors], $sql_tbl[payment_methods] WHERE $sql_tbl[payment_methods].paymentid = '$logged_paymentid' AND $sql_tbl[payment_methods].processor_file = $sql_tbl[ccprocessors].processor");

        }

        if (
            empty($method_name)
            && empty($payment_module_info)
        ) {

            $payment_name = 'Unknown';

        } elseif (empty($payment_module_info)) {

            $payment_name = $method_name;

        } else {

            $payment_name = sprintf("%s (%s%s)",
                $method_name,
                $payment_module_info['module_name'],
                (get_cc_in_testmode($payment_module_info) ? ", in test mode" : '')
            );

        }

    } // if (!empty($logged_paymentid))

    ob_start();

    echo "Payment method: $payment_name\n";

    echo "bill_output = ";

    print_r($bill_output);

    if ($saved_bill_output !== false) {

        echo "original_bill_output = ";

        print_r($saved_bill_output);

    }

    $https_responses = func_https_ctl('GET');

    if (!empty($https_responses)) {

        echo "responses of https requests = ";

        print_r($https_responses);

        func_https_ctl('PURGE');

    }

    if (
        $REQUEST_METHOD != 'POST'
        || empty($_POST['action'])
        || $_POST['action'] != 'place_order'
    ) {

        echo "_GET = ";

        print_r($_GET);

        echo "_POST = ";

        print_r($_POST);

    }

    $op_msg_data = ob_get_contents();

    ob_end_clean();

    x_session_register('login');

    if (
        $bill_output['code'] != 1
        && $bill_output['code'] != 3
    ) {

        $op_message = "Payment processing failure.\nLogin: $login\nIP: $REMOTE_ADDR\n----\n" . $op_msg_data;

        x_log_flag('log_payment_processing_errors', 'PAYMENTS', $op_message, true);

    } else {

        $op_message = "Payment processing notice.\nLogin: $login\nIP: $REMOTE_ADDR\n----\n" . $op_msg_data;

        x_log_flag('log_checkout_processing_notices', 'CHECKOUT', $op_message, true);

    }

}

if (!$fatal) {

    $order_status = $bill_error
        ? 'F'
        : (
            $bill_output['code'] == 3
                ? 'Q'
                : 'P'
        );

    if (
        !empty($cart['split_query'])
        && $bill_output['code'] != 1
    ) {

        $order_status = 'I';

    }

    if (
        $bill_output['code'] == 1
        || $bill_output['code'] == 3
    ) {

        if (
            empty($skey)
            || !in_array(
                func_query_first_cell("SELECT is_callback FROM $sql_tbl[cc_pp3_data] WHERE ref = '$skey'"),
                array(
                    'R',
                    'N',
                )
            )
        ) {

            $cart = '';

            if (!empty($active_modules['SnS_connector']))
                func_generate_sns_action('CartChanged');
        }

    }

    if (
        in_array(
            $order_status,
            array(
                'P',
                'Q',
            )
        )
        && $bill_output['is_preauth']
    ) {
        $order_status = 'A';
    }

    $advinfo = array();

    $advinfo[] = "Reason: " . $bill_output['billmes'];

    if ($bill_output['avsmes']) {
        $advinfo[] = "AVS info: " . $bill_output['avsmes'];
    }

    if ($bill_output['cvvmes']) {
        $advinfo[] = "CVV info: " . $bill_output['cvvmes'];
    }

    if ($bill_output['cavvmes']) {
        $advinfo[] = "CAVV info: " . $bill_output['cavvmes'];
    }

    if (isset($cmpi_result)) {

        $advinfo[] = "3-D Secure Transaction:";

        if (isset($cmpi_result['Enrolled'])) {

            $advinfo[] = "  TransactionId: " . $cmpi_result['TransactionId'];
            $advinfo[] = "  Enrolled: " . $cmpi_result['Enrolled'];

        } else {

            $advinfo[] = "  PAResStatus: " . $cmpi_result['PAResStatus'];
            $advinfo[] = "  PAResStatusDesc: " . $cmpi_result['PAResStatusDesc'];
            $advinfo[] = "  CAVV: " . $cmpi_result['Cavv'];
            $advinfo[] = "  SignatureVerification: " . $cmpi_result['SignatureVerification'];
            $advinfo[] = "  Xid: " . $cmpi_result['Xid'];
            $advinfo[] = "  EciFlag: " . $cmpi_result['EciFlag'];

        }

        if (!empty($cmpi_result['ErrorNo'])) {
            $advinfo[] = "  ErrorNo: " . $cmpi_result['ErrorNo'];
        }

        if (!empty($cmpi_result['ErrorDesc'])) {
            $advinfo[] = "  ErrorDesc: " . $cmpi_result['ErrorDesc'];
        }

    }

    define('STATUS_CHANGE_REF', 7);

    $oids = is_array($orderids)
        ? $orderids
        : array($orderids);

    $_orderids = func_get_urlencoded_orderids($orderids);

    require_once $xcart_dir . '/payment/prepare_payment_ccwebset.php';

    func_change_order_status($orderids, $order_status, join("\n", $advinfo));

    if (
        !empty($extra_order_data)
        && empty($_GET['extra_order_data'])
        && empty($_POST['extra_order_data'])
        && empty($_COOKIE['extra_order_data'])
    ) {

        foreach($extra_order_data as $khash => $value) {

            foreach($oids as $oid) {

                func_array2insert(
                    'order_extras',
                    array(
                        'orderid' => $oid,
                        'khash'   => $khash,
                        'value'   => $value,
                    ),
                    true
                );

            }

        }

        unset($extra_order_data);

    }

    if (
        !empty($extra_order_data_inner)
        && empty($_GET['extra_order_data_inner'])
        && empty($_POST['extra_order_data_inner'])
        && empty($_COOKIE['extra_order_data_inner'])
    ) {

        foreach($oids as $oid) {

            $extra = func_query_first_cell("SELECT extra FROM $sql_tbl[orders] WHERE orderid = '$oid'");

            if (empty($extra))
                continue;

            $extra = unserialize($extra);

            if (empty($extra))
                continue;

            $extra = func_array_merge($extra, $extra_order_data_inner);

            func_array2update(
                'orders',
                array(
                    'extra' => addslashes(serialize($extra)),
                ),
                "orderid = '$oid'"
            );

        }

        unset($extra_order_data_inner);

    }

    if (
        !empty($initial_state_orders)
        && is_array($initial_state_orders)
    ) {

        foreach ($oids as $k => $v) {

            if (in_array($v, $initial_state_orders)) {

                unset($initial_state_orders[$k]);

            }

        }

        if (!empty($initial_state_orders)) {

            $initial_state_show_notif = 'Y';

        }

    }

} // if (!$fatal)

x_session_unregister('secure_oid');

x_session_save();

?>
