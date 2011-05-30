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
 * Functions for "SkipJack" payment module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.cc_skipjack.php,v 1.18.2.1 2011/01/10 13:11:53 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

/**
 * Do SkipJack transaction
 */
function func_cc_skipjack_do($data, $type = 'authorize')
{
    if (!is_array($data))
        return array(array('status' => 3, 'message' => 'Internal error'), false, false);

    $module_params = func_get_pm_params('cc_skipjack.php');

    if ($type != 'authorize') {
        $serial_number_field = 'szSerialNumber';
        $dev_serial_number_field = 'szDeveloperSerialNumber';

    } else {
        $serial_number_field = 'SerialNumber';
        $dev_serial_number_field = 'DeveloperSerialNumber';
    }

    $post = array(
        $serial_number_field . '=' . $module_params["param01"]
    );

    if (!empty($module_params['param02'])) {
        $post[] = $dev_serial_number_field . '=' . $module_params['param02'];
    }

    $post = array_merge($post, $data);

    switch ($type) {
        case 'get_status':
            if ($module_params['testmode'] == 'Y')
                $url = 'https://developer.skipjackic.com:443/scripts/evolvcc.dll?SJAPI_TransactionStatusRequest';
            else
                $url = 'https://www.skipjackic.com:443/scripts/evolvcc.dll?SJAPI_TransactionStatusRequest';

            break;

        case 'change_status':
            if ($module_params['testmode'] == 'Y')
                $url = 'https://developer.skipjackic.com/scripts/evolvcc.dll?SJAPI_TransactionChangeStatusRequest';
            else
                $url = 'https://www.skipjackic.com/scripts/evolvcc.dll?SJAPI_TransactionChangeStatusRequest';

            break;

        default:
            if ($module_params['testmode'] == 'Y')
                $url = 'https://developer.skipjackic.com:443/scripts/EvolvCC.dll?AuthorizeAPI';
            else
                $url = 'https://www.skipjackic.com:443/scripts/EvolvCC.dll?AuthorizeAPI';
    }

    x_load('http');

    list($headers, $return) = func_https_request('POST', $url, $post);

    $rows = func_cc_skipjack_parse_response($return);

    $errors = array(
        0 => 'Success',
        -1 => 'Invalid Command',
        -2 => 'Parameter Missing',
        -3 => 'Failed retrieving response',
        -4 => 'Invalid Status',
        -5 => 'Failed reading security flags',
        -6 => 'Developer serial number not found',
        -7 => 'Invalid Serial Number',
        -51 => 'Invalid Amount'
    );

    $r = array(array(), $headers, $return);

    if (!is_array($rows)) {

        // Internal error
        $r[0] = array(
            'status' => 3,
            'message' => 'Internal error'
        );

        func_pp_error_log($return);

    } elseif ($type == 'get_status') {

        // Process 'Get Transaction Status' response
        if ($rows[0][1] == '0') {

            $h = array_shift($rows);
            $trans = array();
            foreach ($rows as $arr) {
                $trans[] = array(
                    'serial_number' => $arr[0],
                    'amount' => $arr[1],
                    'status' => $arr[2],
                    'message' => $arr[3],
                    'date' => $arr[4],
                    'transaction_id' => $arr[5],
                    'approval_code' => $arr[6],
                    'batch_number' => $arr[7],
                    'parsed_status' => func_cc_skipjack_parse_status($arr[2])
                );
            }

            $r[0] = array(
                'status' => 1,
                'transactions' => $trans
            );

        } else {

            $r[0] = array(
                'status' => 2,
                'error_code' => $rows[0][1],
                'message' => $errors[$rows[0][1]]
            );
        }

    } elseif ($type == 'change_status') {

        // Process 'Change Transaction Status' response
        if ($rows[0][1] == '0') {

            $h = array_shift($rows);
            $trans = array();
            foreach ($rows as $arr) {
                $trans[] = array(
                    'serial_number' => $arr[0],
                    'amount' => $arr[1],
                    'new_status' => $arr[2],
                    'status_changed' => $arr[3],
                    'date' => $arr[4],
                    'message' => $arr[5],
                    'order_number' => $arr[6],
                    'audit_id' => $arr[7]
                );
            }

            return array(
                array(
                    'status' => 1,
                    'transactions' => $trans
                ),
                $headers,
                $return
            );

        } else {

            $r[0] = array(
                'status' => 2,
                'error_code' => $rows[0][1],
                'message' => $errors[$rows[0][1]]
            );
        }

    } else {

        // Process 'Authorize' trtansaction
        $tmp = array();
        foreach ($rows[0] as $i => $j) {
            if ($j != '')
                $tmp[$j] = $rows[1][$i];
        }

        $r[0] = $tmp;

    }

    return $r;
}

/**
 * Parse SkipJack response
 */
function func_cc_skipjack_parse_response($data)
{
    if (!preg_match('/^".+"$/s', trim($data)))
        return $data;

    $arr = explode("\n", trim($data));

    $rows = array();
    foreach ($arr as $r) {
        $rows[] = explode('","', substr($r, 1, -1));
    }

    return $rows;
}

/**
 * Parse SkipJack status code (2-digit)
 */
function func_cc_skipjack_parse_status($status)
{
    if (!is_string($status) || strlen($status) != 2)
        return array('first' => false, 'second' => false, 'primary' => 'Internal error', 'secondary' => 'Internal error');

    $first = array(
        'Idle',
        'Authorized',
        'Denied',
        'Settled',
        'Credited',
        'Deleted',
        'Archived',
        'Pre-Authorized',
        'Split Settled'
    );
    $second = array(
        'Idle',
        'Pending Credit',
        'Pending Settlement',
        'Pending Delete',
        'Pending Authorization',
        'Pending Manual Settlement (Manual Settlement accounts)',
        'Pending Recurring',
        'Submitted for Settlement'
    );

    $a = intval(substr($status, 0, 1));
    $b = intval(substr($status, 1, 1));

    return array(
        'first' => $a,
        'second' => $b,
        'primary' => $first[$a],
        'secondary' => $second[$b]
    );
}

/**
 * Do SkipJack 'Get transaction status' transaction
 */
function func_cc_skipjack_get_status($order_number)
{
    $data = array(
        'szOrderNumber=' . $order_number
    );

    list($result, $headers, $raw_result) = func_cc_skipjack_do($data, 'get_status');
    if ($result['status'] != 1)
        return array(false, false);

    $tmp = array_shift($result['transactions']);
    return array($tmp['parsed_status']['first'], $tmp['parsed_status']['primary']);
}

/**
 * Do SkipJack 'Change transaction status' transaction
 */
function func_cc_skipjack_change_status($order_number, $status, $amount = null, $force_settlement = null)
{
    $data = array(
        'szOrderNumber=' . $order_number,
        'szDesiredStatus=' . $status
    );

    if (!is_null($amount))
        $data[] = 'szAmount=' . $amount;

    if (!is_null($force_settlement))
        $data[] = 'zsForceSettlement=' . $force_settlement;

    list($result, $headers, $raw_result) = func_cc_skipjack_do($data, 'change_status');
    if ($result['status'] != 1)
        return array(false, false);

    $tmp = array_shift($result['transactions']);
    return array($tmp['status_changed'] == 'SUCCESSFUL', $tmp['new_status'], $tmp['message']);
}

/**
 * Do SkipJack Capture transaction
 */
function func_cc_skipjack_do_capture($order)
{
    $extra = array(
        'name' => 'orderid',
        'value' => $order['order']['extra']['orderid']
    );

    list($current_status, $current_status_msg) = func_cc_skipjack_get_status($order['order']['extra']['orderid']);
    if ($current_status === false)
        array(false, 'Internal error', $extra);

    if ($current_status == 3)
        array(X_PAYMENT_TRANS_ALREADY_CAPTURED, '', $extra);

    if ($current_status != 1)
        array(false, func_get_langvar_by_name('lbl_current_trans_status_is_x', array('status' => $current_status_msg)), $extra);

    list($status, $new_status, $message) = func_cc_skipjack_change_status($order['order']['extra']['orderid'], 'SETTLE', $order['order']['total'], '1');

    return array(
        $status,
        $status ? '' : $message,
        $extra
    );
}

/**
 * Do SkipJack Void transaction
 */
function func_cc_skipjack_do_void($order)
{
    $extra = array(
        'name' => 'orderid',
        'value' => $order['order']['extra']['orderid']
    );

    list($current_status, $current_status_msg) = func_cc_skipjack_get_status($order['order']['extra']['orderid']);
    if ($current_status === false) {
        return array(false, 'Internal error', $extra);
    }

    if ($current_status == 5) {
        return array(X_PAYMENT_TRANS_ALREADY_VOIDED, '', $extra);
    }

    if ($current_status != 1) {
        return array(false, func_get_langvar_by_name('lbl_current_trans_status_is_x', array('status' => $current_status_msg)), $extra);
    }

    list($status, $new_status, $message) = func_cc_skipjack_change_status($order['order']['extra']['orderid'], 'DELETE');

    return array(
        $status,
        $status ? '' : $message,
        $extra
    );
}

?>
