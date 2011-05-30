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
 * Orders-related actions processor
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: process_order.php,v 1.39.2.2 2011/01/10 13:11:50 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('order');

x_session_register('orders_to_delete');

if ($REQUEST_METHOD == 'POST' || ($mode=="delete" && !empty($orderid))) {
/**
 * Process POST request
 */

    if ($mode == 'tracking_data') {
        include $xcart_dir.'/include/orders_tracking.php';
    }

    if ($mode == 'refund') {

        // Refund charged payment transaction
        x_load('payment');
        if (isset($refund_amount) && strlen($refund_amount) != 0 && (!is_numeric($refund_amount) || doubleval($refund_amount) <= 0)) {
            $top_message = array(
                'type' => 'E',
                'content' => func_get_langvar_by_name('txt_refund_amount_not_numeric')
            );
            func_header_location("order.php?orderid=" . $orderid);
        }

        $refund_amount = doubleval($refund_amount);
        $top_message = func_payment_do_refund($orderid, $refund_amount > 0 ? $refund_amount : null);
        func_header_location("order.php?orderid=" . $orderid);
    }

    if (!empty($export_fmt)) {
        $search_data['orders']['export_fmt'] = $export_fmt;
        x_session_save('search_data');
    }

    if ($mode == 'update') {

    // Update orders info (status)

        $flag = 0;
        define('ORDERS_LIST_UPDATE', 1);
        define('STATUS_CHANGE_REF', 1);

        if (is_array($order_status) && is_array($order_status_old)) {
            foreach($order_status as $orderid=>$status) {
                if (is_numeric($orderid) && $status != $order_status_old[$orderid] && $status != 'A') {
                    func_change_order_status($orderid, $status);
                    $flag = 1;
                }
            }
        }
        if ($flag)
            $top_message['content'] = func_get_langvar_by_name('msg_adm_orders_upd');
        func_header_location("orders.php?mode=search");

    } // /if ($mode == 'update')

    elseif ($mode == 'delete' || $mode == 'delete_all') {

    // Delete the selected orders

        if ($confirmed == 'Y') {
        // Deleting is confirmed
            if ($mode == 'delete_all') {
                include $xcart_dir.'/include/orders_deleteall.php';
                $top_message['content'] = func_get_langvar_by_name('msg_adm_all_orders_del');
                func_header_location("orders.php?mode=search");
            }

            if (is_array($orders_to_delete)) {
                foreach ($orders_to_delete as $k=>$v) {
                    // Delete order
                    func_delete_order($k);
                }
                $orders_to_delete = '';

                // Prepare the information message

                $top_message['content'] = func_get_langvar_by_name('msg_adm_orders_del');
                func_header_location('orders.php');
            }
        }
        else {
            if (empty($orderids) && !empty($orderid))
                $orderids[$orderid] = 1;
            $orders_to_delete = (!empty($orderids) ? $orderids : '');
            func_header_location("process_order.php?mode=$mode");
        }

    } // /if ($mode == 'delete')

    elseif ($mode == 'invoice' and !empty($orderids)) {

    // Display invoices

        $orders_to_delete = (!empty($orderids) ? $orderids : '');
        func_header_location("process_order.php?mode=invoice");
    }
    elseif ($mode == 'label' and !empty($orderids)) {

    // Display labels

        $orders_to_delete = (!empty($orderids) ? $orderids : '');
        func_header_location("process_order.php?mode=label");

    }

    // Export selected order(s)
    elseif ($mode == 'export' and !empty($orderids)) {
        include $xcart_dir.'/include/orders_export.php';
    }

    $orders_to_delete = '';
    $top_message['content'] = func_get_langvar_by_name('msg_adm_warn_orders_sel');
    $top_message['type'] = 'W';

    func_header_location("orders.php?mode=search");

} // /if ($REQUEST_METHOD == 'POST')

if ($mode == 'capture') {

    // Capture pre-authorized payment transaction
    x_load('payment');
    $top_message = func_payment_do_capture($orderid);
    func_header_location("order.php?orderid=" . $orderid);

} elseif ($mode == 'void') {

    // Void pre-authorized payment transaction
    x_load('payment');
    $top_message = func_payment_do_void($orderid);
    func_header_location("order.php?orderid=" . $orderid);

} elseif ($mode == 'accept') {

    // Accept blocked payment transaction
    x_load('payment');
    $top_message = func_payment_do_accept($orderid);
    func_header_location("order.php?orderid=" . $orderid);

} elseif ($mode == 'decline') {

    // Decline blocked payment transaction
    x_load('payment');
    $top_message = func_payment_do_decline($orderid);
    func_header_location("order.php?orderid=" . $orderid);

} elseif ($mode == 'get_info') {

    // Get info about payment transaction
    x_load('payment');
    $top_message = func_payment_do_get_info($orderid);
    func_header_location("order.php?orderid=" . $orderid);

} elseif ($mode == 'xpayments' && $orderid && $active_modules['XPayments_Connector']) {

    require_once ($xcart_dir . '/modules/XPayments_Connector/admin.php');

}

if ($mode == 'invoice' || $mode == 'label') {
/**
 * Display the printable version of order invoices
 */
    if (is_array($orders_to_delete)) {
        $orderid = implode(",", array_keys($orders_to_delete));
        $orders_to_delete = '';
        x_session_save('orders_to_delete');
        include $xcart_dir.'/include/history_order.php';
    }
}

if ($mode == 'delete') {
/**
 * Prepare for deleting products
 */
    if (is_array($orders_to_delete)) {

        $location[] = array(func_get_langvar_by_name('lbl_orders_management'), 'search.php');
        $location[] = array(func_get_langvar_by_name('lbl_delete_orders'), '');
        $smarty->assign('location', $location);

        foreach ($orders_to_delete as $k=>$v) {
            $condition[] = "orderid='".addslashes($k)."'";
        }
        $search_condition = implode(" OR ", $condition);

        $orders = func_query("SELECT orderid, status, date, total FROM $sql_tbl[orders] WHERE $search_condition ORDER BY orderid");

        if (is_array($orders)) {
            foreach ($orders as $k=>$v) {
                $orders[$k]['date'] += $config['Appearance']['timezone_offset'];
                if (!$single_mode)
                    $orders[$k]['provider_login'] = func_query_first_cell("SELECT c.login FROM $sql_tbl[order_details] as od LEFT JOIN $sql_tbl[customers] as c ON c.id = od.provider WHERE orderid='$v[orderid]'");
            }

            $smarty->assign('orders', $orders);

            $smarty->assign('main','order_delete_confirmation');

            // Show admin template because only admin can delete orders

            if (
                file_exists($xcart_dir.'/modules/gold_display.php')
                && is_readable($xcart_dir.'/modules/gold_display.php')
            ) {
                include $xcart_dir.'/modules/gold_display.php';
            }
            func_display('admin/home.tpl',$smarty);
            exit;
        }

    }

}
elseif ($mode == 'delete_all') {
/**
 * Prepare the confirmation page for deleting all orders
 */
    $location[] = array(func_get_langvar_by_name('lbl_orders_management'), 'search.php');
    $location[] = array(func_get_langvar_by_name('lbl_delete_orders'), '');
    $smarty->assign('location', $location);

    $orders_count = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[orders]");
    $smarty->assign('orders_count', $orders_count);

    $smarty->assign('mode','delete_all');
    $smarty->assign('main','order_delete_confirmation');

    // Show admin template because only admin can delete orders

    if (
        file_exists($xcart_dir.'/modules/gold_display.php')
        && is_readable($xcart_dir.'/modules/gold_display.php')
    ) {
        include $xcart_dir.'/modules/gold_display.php';
    }
    func_display('admin/home.tpl',$smarty);
    exit;

} elseif ($mode == 'get_export_storage') {

    // Get orders export storage
    x_session_register('orders_export_storage');

    if (empty($orders_export_key) || !isset($orders_export_storage[$orders_export_key]) || func_filesize($orders_export_storage[$orders_export_key]['path']) < 1 || $orders_export_storage[$orders_export_key]['status'] != 'E') {
        if (!empty($orders_export_key) && isset($orders_export_storage[$orders_export_key]))
            unset($orders_export_storage[$orders_export_key]);

        $top_message = array(
            'content' => func_get_langvar_by_name('txt_order_export_failed'),
            'type' => 'E'
        );
        func_header_location('orders.php');
    }

    #bt:65625 The following Pragma and Cache-Control lines are necessary as the overcome an issue that IE has
    // in some server configurations when the no-cache header is sent.
    header("Pragma: public");
    header("Cache-Control: max-age=0");

    header("Content-Type: ".$orders_export_storage[$orders_export_key]['ctype']);
    header("Content-Disposition: attachment; filename=\"".$orders_export_storage[$orders_export_key]['export_file']."\"");
    header("Content-Length: ".func_filesize($orders_export_storage[$orders_export_key]['path']));

    readfile($orders_export_storage[$orders_export_key]['path']);
    exit;
}

$orders_to_delete = '';

$top_message['content'] = func_get_langvar_by_name('msg_adm_warn_orders_sel');
$top_message['type'] = 'W';

func_header_location("orders.php?mode=search");

?>
