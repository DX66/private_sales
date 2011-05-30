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
 * Collect infos about ordered products
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: history_order.php,v 1.68.2.1 2011/01/10 13:11:49 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('order');

x_session_register('session_orders', array());

if (empty($mode)) $mode = '';

if (
    in_array(
        $mode,
        array(
            'invoice',
            'label',
            'history',
        )
    )
) {

    $charset = $smarty->get_template_vars('default_charset');
    $charset_text = ($charset)
        ? "; charset=$charset"
        : '';

    header("Content-Type: text/html$charset_text");
    header("Content-Disposition: inline; filename=invoice.txt");

    $orders = explode(",", $orderid);

    if ($orders) {

        $orders_data = array();

        foreach ($orders as $orderid) {

            $order_data = func_order_data($orderid);

            if (empty($order_data))
                continue;

            // Security check if order owned by another customer

            if (
                $current_area == 'C'
                && $order_data['userinfo']['userid'] != $logged_userid
                && !in_array($orderid, $session_orders)
                && (
                    empty($access_key)
                    || $order_data['order']['access_key'] != $access_key
                )
            ) {
                func_403(34);
            }

            $order     = $order_data['order'];
            $customer  = $order_data['userinfo'];
            $giftcerts = $order_data['giftcerts'];
            $products  = $order_data['products'];

            $orders_data[] = array (
                'order'     => $order,
                'customer'  => $customer,
                'products'  => $products,
                'giftcerts' => $giftcerts,
            );
        }

        if (empty($orders_data)) {
            func_page_not_found($current_area);
        }

        $smarty->assign('orders_data', $orders_data);

        $_tmp_smarty_debug = $smarty->debugging;
        $smarty->debugging = false;

        if (
            $mode == 'history'
            && !empty($active_modules['Advanced_Order_Management'])
        ) {

            include $xcart_dir.'/modules/Advanced_Order_Management/history.php';

            $smarty->assign('history',$order['history']);

            func_display('modules/Advanced_Order_Management/popup_history.tpl',$smarty);

        } elseif ($mode == 'invoice') {

            if (defined('IS_ADMIN_USER')) {
                $smarty->assign('show_order_details', 'Y');
            }

            func_display('main/order_invoice_print.tpl',$smarty);

        } elseif ($mode == 'label') {

            func_display('main/order_labels_print.tpl',$smarty);

        }

        $smarty->debugging = $_tmp_smarty_debug;
    }

    exit;

} else {

    $order_data = func_order_data($orderid);

    $split_checkout_data = func_get_split_checkout_order_data_by_orderid($orderid);

    if (false !== $split_checkout_data) {

        $smarty->assign('split_checkout_data', $split_checkout_data);

        $order_data['order']['payment_method'] = $split_checkout_data['payment_method'] . ', ' . $order_data['order']['payment_method'];

    }

    if (empty($order_data)) {
        func_page_not_found($current_area);
    }

    $shippingid = $order_data['order']['shippingid'];
    $ship_code = func_query_first_cell("SELECT code FROM $sql_tbl[shipping] WHERE shippingid='$shippingid'");

    if ($ship_code == 'ARB') {

        $dhl_account = func_query_first_cell("SELECT value FROM $sql_tbl[order_extras] WHERE orderid='$orderid' AND khash='arb_account'");

        $smarty->assign('dhl_account', $dhl_account);
        $smarty->assign('is_ship_ARB', true);
    }

    // Security check if order owned by another customer

    if (
        $current_area == 'C'
        && $order_data['userinfo']['userid'] != $logged_userid
        && !in_array($orderid, $session_orders)
        && (empty($access_key) || $order_data['order']['access_key'] != $access_key)
    ) {
        func_403(35);
    }

    $smarty->assign('order_details_fields_labels', func_order_details_fields_as_labels());
    $smarty->assign('order',                       $order_data['order']);
    $smarty->assign('customer',                    $order_data['userinfo']);
    $smarty->assign('products',                    $order_data['products']);
    $smarty->assign('giftcerts',                   $order_data['giftcerts']);

    $joins = array("LEFT JOIN $sql_tbl[order_details] ON $sql_tbl[order_details].orderid=$sql_tbl[orders].orderid");
    $where = array();

    if (
        $order_data
        && !empty($login)
    ) {

        if ($current_area == 'C') {

            $where[] = "$sql_tbl[orders].userid = '" . $logged_userid . "'";
            $where[] = "($sql_tbl[order_details].orderid IS NOT NULL OR $sql_tbl[giftcerts].orderid IS NOT NULL)";
            $joins[] = "LEFT JOIN $sql_tbl[giftcerts] ON $sql_tbl[orders].orderid = $sql_tbl[giftcerts].orderid";

        } elseif (
                $current_area == 'P'
                && !$single_mode
        ) {

            $where[] = "$sql_tbl[order_details].provider = '" . $logged_userid . "'";
            $where[] = "$sql_tbl[order_details].orderid IS NOT NULL";

        } else {

            $joins[] = "LEFT JOIN $sql_tbl[giftcerts] ON $sql_tbl[orders].orderid=$sql_tbl[giftcerts].orderid";
            $where[] = "($sql_tbl[order_details].orderid IS NOT NULL OR $sql_tbl[giftcerts].orderid IS NOT NULL)";

        }

        if (!empty($where)) {
            $where = " AND " . implode(" AND ", $where);
        }

        x_session_register('search_data');

        $search_condition = isset($search_data['orders']['search_condition'])
            ? $search_data['orders']['search_condition']
            : '';

        // find next
        if (!empty($search_condition)) {

            $tmp = func_query_first_cell("SELECT $sql_tbl[orders].orderid ".$search_condition." AND $sql_tbl[orders].orderid > '" . $orderid . "' GROUP BY $sql_tbl[orders].orderid ORDER BY $sql_tbl[orders].orderid ASC");

        } else {

            $tmp = func_query_first_cell("SELECT $sql_tbl[orders].orderid FROM $sql_tbl[orders] " . implode(" ", $joins) . " WHERE $sql_tbl[orders].orderid > '" . $orderid . "' $where GROUP BY $sql_tbl[orders].orderid ORDER BY $sql_tbl[orders].orderid ASC");

        }

        if (!empty($tmp))
            $smarty->assign('orderid_next', $tmp);

        // find prev
        if (!empty($search_condition)) {

            $tmp = func_query_first_cell("SELECT $sql_tbl[orders].orderid " . $search_condition . " AND $sql_tbl[orders].orderid < '" . $orderid . "' GROUP BY $sql_tbl[orders].orderid ORDER BY $sql_tbl[orders].orderid DESC");

        } else {

            $tmp = func_query_first_cell("SELECT $sql_tbl[orders].orderid FROM $sql_tbl[orders] " . implode(" ", $joins) . " WHERE $sql_tbl[orders].orderid < '" . $orderid . "' GROUP BY $sql_tbl[orders].orderid ORDER BY $sql_tbl[orders].orderid DESC");

        }

        if (!empty($tmp)) {
            $smarty->assign('orderid_prev', $tmp);
        }

        if (isset($search_data['orders'])) {
            $smarty->assign('search_data_orders', $search_data['orders']);
        }
    }
}

if (
    $order_data
    && $mode == 'view_cnote'
) {

    $default_charset = $e_langs[$order_data['order']['language']];

    if ($default_charset) {

        $smarty->assign('default_charset',     $default_charset);
        $smarty->assign('current_language', $order_data['order']['language']);

        header("Content-Type: text/html; charset=" . $default_charset);
        header("Content-Language: " . $order_data['order']['language']);

    }

    func_display('main/order_view_cnotes.tpl', $smarty);
    exit;
}

$location[] = array(func_get_langvar_by_name('lbl_orders_management'), 'orders.php');
$location[] = array(func_get_langvar_by_name('lbl_order_details_location', array('orderid' => $orderid)), '');

if(!empty($active_modules['RMA'])) {
    include $xcart_dir . '/modules/RMA/add_returns.php';
}

if(!empty($active_modules['Anti_Fraud'])) {
    include $xcart_dir . '/modules/Anti_Fraud/order.php';
}

?>
