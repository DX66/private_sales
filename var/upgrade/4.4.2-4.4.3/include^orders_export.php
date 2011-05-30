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
 * Orders export
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: orders_export.php,v 1.68.2.1 2011/01/10 13:11:50 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

/**
 * Global export definitions
 */
$step_row = 50;    // Number of steps processed in one pass
$dot_per_row = 10;

x_session_register('orders_export_storage');
x_session_register('referer', $HTTP_REFERER);

if ($mode != 'export_continue') {
    $referer = $HTTP_REFERER;
} else {
    $HTTP_REFERER = $referer;
}

if (
    $REQUEST_METHOD != 'POST' &&
    (!isset($orders_export_key) || (empty($orders_export_key) || !isset($orders_export_storage[$orders_export_key]) || $mode != 'export_continue'))
) {
    func_header_location(func_is_internal_url($HTTP_REFERER) ? $HTTP_REFERER : 'orders.php');
    return;
}

x_load('crypt','export');

if (!isset($orders_export_key)) {

    // Prepare the orderid condition

    if ($mode == 'export') {
        // Passed here via include/process_order.php
        if (!empty($orderids) && is_array($orderids)) {
            $orderids = array_keys($orderids);

        } else {
            $top_message['content'] = func_get_langvar_by_name('msg_adm_warn_orders_sel');
            func_header_location("orders.php?mode=search");
        }
    }

    $condition = array();
    if (!empty($orderids) && is_array($orderids)) {
        $maxid = max($orderids);
        $plain_ids = true;
        for ($i = min($orderids)+1; $i < $maxid && $plain_ids; $i++) {
            if (!in_array($i, $orderids))
                $plain_ids = false;
        }

        if ($plain_ids)
            $condition[] = "($sql_tbl[orders].orderid >= '".min($orderids)."' AND $sql_tbl[orders].orderid <= '".$maxid."')";
        else
            $condition[] = "$sql_tbl[orders].orderid IN (".implode(",",$orderids).")";
    }
    if ($provider)
        $condition[] = "$sql_tbl[order_details].provider='$provider'";

    $condition = implode(" AND ", $condition);
    if (!empty($condition))
        $condition = " WHERE ".$condition;

    // Export through standart export procedure
    if (empty($export_fmt) || $export_fmt == 'std') {
        if ($mode == 'export_all' && !func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[orders]")) {
            $top_message['content'] = func_get_langvar_by_name("lbl_no_orders_found");
            $top_message['type'] = "I";
            func_header_location(func_is_internal_url($HTTP_REFERER) ? $HTTP_REFERER : 'orders.php');
        }

        func_export_range_save('ORDERS', "SELECT $sql_tbl[orders].orderid FROM $sql_tbl[orders] LEFT JOIN $sql_tbl[order_details] ON $sql_tbl[orders].orderid = $sql_tbl[order_details].orderid LEFT JOIN $sql_tbl[shipping] ON $sql_tbl[orders].shippingid = $sql_tbl[shipping].shippingid $condition GROUP BY $sql_tbl[orders].orderid ORDER BY orderid");

        $condition_details = $condition_gc = '';
        if (!empty($condition))
            $condition_details = str_replace("$sql_tbl[orders]", "$sql_tbl[order_details]", $condition);
        func_export_range_save('ORDER_ITEMS', "SELECT $sql_tbl[order_details].itemid FROM $sql_tbl[order_details] $condition_details GROUP BY $sql_tbl[order_details].itemid ORDER BY itemid");

        if (!empty($condition) && strstr($condition, "$sql_tbl[orders].orderid") !== false) {
            $condition_gc = str_replace("$sql_tbl[orders]", "$sql_tbl[giftcerts]", $condition);
            // The xcart_giftcerts does not have provider field. Remove it.
            $condition_gc = str_replace("AND $sql_tbl[order_details].provider='$provider'", "", $condition_gc);
        }
        func_export_range_save('GIFT_CERTIFICATES', "SELECT $sql_tbl[giftcerts].gcid FROM $sql_tbl[giftcerts] $condition_gc GROUP BY $sql_tbl[giftcerts].gcid ORDER BY gcid");

        $top_message['content'] = func_get_langvar_by_name("lbl_export_orders_add");
        $top_message['type'] = "I";

        func_header_location("import.php?mode=export");
    }

    if ($provider) {

        // SQL query without Gift certificates data
        $search_orders_query = "SELECT $sql_tbl[orders].*, $sql_tbl[order_details].itemid, $sql_tbl[shipping].shipping as shipping_method FROM $sql_tbl[orders] LEFT JOIN $sql_tbl[order_details] ON $sql_tbl[orders].orderid = $sql_tbl[order_details].orderid LEFT JOIN $sql_tbl[shipping] ON $sql_tbl[orders].shippingid = $sql_tbl[shipping].shippingid $condition GROUP BY $sql_tbl[orders].orderid ORDER BY $sql_tbl[orders].orderid DESC";
    } else {

        // SQL query with Gift certificates data
        $search_orders_query = "SELECT $sql_tbl[orders].*, $sql_tbl[order_details].itemid, $sql_tbl[giftcerts].gcid, $sql_tbl[shipping].shipping as shipping_method, $sql_tbl[customers].login FROM $sql_tbl[orders] LEFT JOIN $sql_tbl[order_details] ON $sql_tbl[orders].orderid = $sql_tbl[order_details].orderid LEFT JOIN $sql_tbl[giftcerts] ON $sql_tbl[orders].orderid = $sql_tbl[giftcerts].orderid LEFT JOIN $sql_tbl[shipping] ON $sql_tbl[orders].shippingid = $sql_tbl[shipping].shippingid LEFT JOIN $sql_tbl[customers] ON $sql_tbl[orders].userid = $sql_tbl[customers].id $condition GROUP BY $sql_tbl[orders].orderid ORDER BY $sql_tbl[orders].orderid DESC";
    }

    $log_op_message = "Login: $login\nIP: $REMOTE_ADDR\nOperation: export orders";
    if (!empty($orderids) && is_array($orderids))
        $log_op_message .= "(".implode(',',$orderids).")";
    else
        $log_op_message .= "(all found)";

    $log_op_message .= "\nUsed SQL query: ".$search_orders_query;
    x_log_flag('log_orders_export', 'ORDERS', $log_op_message, true);
    $start_row = 0;

    $orders_result = db_query($search_orders_query);
    $total_lines = db_num_rows($orders_result);
    if (empty($total_lines)) {
        $smarty->debugging = $_tmp_smarty_debug;
        $top_message['content'] = func_get_langvar_by_name('lbl_no_orders_found');
        func_header_location($HTTP_REFERER);
        exit;
    }

} else {
    $search_orders_query = $orders_export_storage[$orders_export_key]['search_orders_query'];
    $export_fmt = $orders_export_storage[$orders_export_key]['export_fmt'];
    $start_row = intval($orders_export_storage[$orders_export_key]['line']);
    $total_lines = intval($orders_export_storage[$orders_export_key]['total_lines']);
}

$step_row = $step_row+$start_row > $total_lines ? ($total_lines-$start_row) : $step_row;

$orders_result = db_query($search_orders_query." LIMIT ".$start_row.", ".$step_row);

if (db_num_rows($orders_result) <= 0) {
    if (isset($orders_export_key)) {
        func_display_service_header();
        $orders_export_storage[$orders_export_key]['status'] = "E";
        func_flush(func_get_langvar_by_name('txt_order_export_back_note', array('orders_export_key' => $orders_export_key, 'referer' => $HTTP_REFERER ? $HTTP_REFERER : 'orders.php'), false, true, true));
        func_header_location("process_order.php?mode=get_export_storage&orders_export_key=".$orders_export_key, true, 302, false, false);
    }

    $smarty->debugging = $_tmp_smarty_debug;
    exit;
}

$export_file = 'orders.txt';
$delimiter = "\t";
$ctype = 'text/plain';

$export_tbl = array (
    'csv_tab' => array (
        'ctype' => 'application/csv',
        'delim' => "\t",
        'file' => 'orders.csv'
    ),
    'csv_semi' => array (
        'ctype' => 'application/csv',
        'delim' => ';',
        'file' => 'orders.csv'
    ),
    'csv_comma' => array (
        'ctype' => 'application/csv',
        'delim' => ',',
        'file' => 'orders.csv'
    ),
);

if (!empty($active_modules['QuickBooks'])) {
    $export_tbl['qb'] = array (
        'ctype' => 'application/csv',
        'file' => 'orders.IIF'
    );
}

if (isset($export_tbl[$export_fmt]['ctype']))
    $ctype = $export_tbl[$export_fmt]['ctype'];

if (isset($export_tbl[$export_fmt]['delim']))
    $delimiter = $export_tbl[$export_fmt]['delim'];

if (isset($export_tbl[$export_fmt]['file']))
    $export_file = $export_tbl[$export_fmt]['file'];

$smarty->assign('delimiter', $delimiter);

$_tmp_smarty_debug = $smarty->debugging;
$smarty->debugging = false;

$date_fields = array(
    'date',
    'add_date'
);
$text_fields = array(
    'details',
    'notes',
    'customer_notes'
);

if (!isset($orders_export_key)) {
    $orders_export_key = md5(XC_TIME);
    $orders_export_storage[$orders_export_key] = array(
        'path' => func_allow_file(tempnam($file_temp_dir, 'orders_export'), true),
        'ctype' => $ctype,
        'export_file' => $export_file,
        'time' => XC_TIME,
        'status' => 'C',
        'search_orders_query' => $search_orders_query,
        'export_fmt' => $export_fmt,
        'line' => 0,
        'total_lines' => $total_lines
    );
    if (!empty($orders_export_storage[$orders_export_key]['path']))
        $orders_export_fp = @fopen($orders_export_storage[$orders_export_key]['path'], "w");

} else {
    $orders_export_fp = @fopen($orders_export_storage[$orders_export_key]['path'], "a");
}

if (empty($orders_export_storage[$orders_export_key]['path']) || !$orders_export_fp) {
    func_unset($orders_export_storage, $orders_export_key);
    unset($orders_export_key);
    $top_message = array(
        'content' => func_get_langvar_by_name('txt_order_export_failed'),
        'type' => 'E'
    );
    func_header_location(func_is_internal_url($HTTP_REFERER) ? $HTTP_REFERER : 'orders.php');
}

$i = 0;
func_display_service_header(
    func_get_langvar_by_name(
        'txt_exporting_orders_line',
        array(
            'begin' => $orders_export_storage[$orders_export_key]['line']+1,
            'end' => $orders_export_storage[$orders_export_key]['line']+$step_row,
            'total' => $orders_export_storage[$orders_export_key]['total_lines']
        ),
        false,
        true,
        true
    ),
    true
);

while ($data = db_fetch_array($orders_result)) {
    if (empty($data['itemid']) && empty($data['gcid']))
        continue;

    list($data['b_address'], $data['b_address_2']) = preg_split("/[\r\n]+/", $data['b_address']);
    list($data['s_address'], $data['s_address_2']) = preg_split("/[\r\n]+/", $data['s_address']);

    if (!defined('IS_ADMIN_USER')) {
        unset($data['details']);
    }

    $data['date'] += $config['Appearance']['timezone_offset'];

    $orders_full = array();

    // Get products data
    if (!empty($data['itemid'])) {
        $row = $data;
        func_unset($row, 'gcid');

        $order_details = db_query("SELECT p.*, od.* FROM $sql_tbl[order_details] as od LEFT JOIN $sql_tbl[products] as p ON od.productid = p.productid WHERE od.orderid = '$row[orderid]'");
        if ($order_details) {
            while ($product = db_fetch_array($order_details)) {
                $product['add_date'] += $config['Appearance']['timezone_offset'];
                $orders_full[] = func_array_merge($row, $product);
            }
            db_free_result($order_details);
        }
    }

    // Get Gift certificates data
    if (!empty($data['gcid'])) {
        $row = $data;

        $giftcerts_details = db_query("SELECT * FROM $sql_tbl[giftcerts] WHERE orderid = '$row[orderid]'");
        if ($giftcerts_details) {
            while($gift = db_fetch_array($giftcerts_details)) {
                $gift['giftcert_status'] = $gift['status'];
                unset($gift['status']);
                $gift['add_date'] += $config['Appearance']['timezone_offset'];
                $orders_full[] = func_array_merge($row, $gift);
            }
            db_free_result($giftcerts_details);
        }
    }

    if (empty($orders_full))
        continue;

    $export_data = false;

    if ($active_modules['QuickBooks'] == 'Y' && $export_fmt == 'qb') {
        // QuickBooks export

        include $xcart_dir.'/modules/QuickBooks/orders_export.php';

    } else {

        // Standart export procedure
        foreach ($orders_full as $key => $value) {

            // Data quotation
            foreach ($value as $subkey => $subvalue) {
                if (is_array($subvalue) || strlen($subvalue) == 0 || (!in_array($subkey, $date_fields) && preg_match("/^\d+$/S", $subvalue)))
                    continue;

                if ($subkey == 'details')
                    $subvalue = text_decrypt($subvalue);

                if (in_array($subkey, $date_fields)) {

                    // Date fields
                    $subvalue = strftime($config['Appearance']['date_format'], $subvalue)." ".strftime($config['Appearance']['time_format'], $subvalue);

                } elseif (is_numeric($subvalue)) {

                    // Numeric fields
                    $subvalue = sprintf("%01.03f", $subvalue);

                } elseif(in_array($subkey, $text_fields)) {

                    // Text fields
                    $subvalue = preg_replace("/[\r\n\t]/S", " ", $subvalue);

                } elseif($subkey == 'product_options') {

                    // Product options data
                    $subvalue = str_replace("\n", ", ", $subvalue);
                    $subvalue = preg_replace("/[\r\t]/S", '', $subvalue);

                } else {
                    $subvalue = preg_replace("/[\r\n\t]/S", '', $subvalue);
                }

                $orders_full[$key][$subkey] = '"'.str_replace('"', '""', $subvalue).'"';
            }

            if (++$i % $dot_per_row == 0)
                func_flush('.');
        }

        $smarty->assign('orders', $orders_full);
        $export_data = func_display('main/orders_export.tpl', $smarty, false);
    }

    if (empty($export_data))
        continue;

    $orders_export_storage[$orders_export_key]['line']++;

    fwrite($orders_export_fp, $export_data);
}

db_free_result($orders_result);

fclose($orders_export_fp);
func_chmod_file($orders_export_storage[$orders_export_key]['path']);

if ($orders_export_storage[$orders_export_key]['total_lines'] <= $orders_export_storage[$orders_export_key]['line']) {
    $orders_export_storage[$orders_export_key]['status'] = "E";
    func_flush("<br />\n<br/>\n".func_get_langvar_by_name('txt_order_export_back_note', array('orders_export_key' => $orders_export_key, 'referer' => $HTTP_REFERER ? $HTTP_REFERER : 'orders.php'), false, true, true));
    func_header_location("process_order.php?mode=get_export_storage&orders_export_key=".$orders_export_key, true, 302, false, false);
}

func_header_location("orders.php?mode=export_continue&orders_export_key=".$orders_export_key);

exit;
?>
