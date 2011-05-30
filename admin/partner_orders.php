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
 * Manage orders placed by customers referred to the store site by partners
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: partner_orders.php,v 1.31.2.3 2011/01/25 09:43:11 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './auth.php';
require $xcart_dir.'/include/security.php';

if (empty($active_modules['XAffiliate']))
    func_403(27);

$location[] = array(func_get_langvar_by_name('lbl_partners_orders'), '');

x_session_register('search_data');

/**
 * Define data for the navigation within section
 */
$dialog_tools_data['right'][] = array('link' => 'partner_report.php', 'title' => func_get_langvar_by_name('lbl_partner_accounts'));
$dialog_tools_data['right'][] = array('link' => 'payment_upload.php', 'title' => func_get_langvar_by_name('lbl_payment_upload'));

if ($REQUEST_METHOD == 'POST') {

    if ($mode == 'update') {

        $users = array();
        foreach($paid as $pid => $tmp) {
            $c = func_query_first_cell("SELECT * FROM $sql_tbl[partner_payment] WHERE payment_id = '$pid' AND paid != 'Y'");
            if (!$c)
                continue;

            if (!isset($users[$c['userid']]))
                $users[$c['userid']] = array('total' => 0, 'pids' => array());

            $users[$c['userid']]['total'] += $c['commissions'];
            $users[$c['userid']]['pids'][] = $pid;
        }

        $count_ok = 0;
        $count_fail = 0;
        foreach ($users as $l => $v) {
            $min_paid = func_query_first_cell("SELECT $sql_tbl[partner_plans].min_paid FROM $sql_tbl[partner_plans], $sql_tbl[partner_commissions] WHERE $sql_tbl[partner_plans].plan_id = $sql_tbl[partner_commissions].plan_id AND $sql_tbl[partner_commissions].userid = '$l'");
            if ($v['total'] > $min_paid) {
                $count_ok++;
                func_array2update('partner_payment', array('paid' => 'Y'), "payment_id IN ('" . implode("','", $v['pids']). "')");

            } else {
                $count_fail++;
            }
        }

        if ($count_ok && $count_fail) {
            $top_message = array(
                'type' => 'W',
                'content' => func_get_langvar_by_name("txt_partner_payment_updated_warning")
            );

        } elseif ($count_ok) {
            $top_message = array(
                'type' => 'I',
                'content' => func_get_langvar_by_name("txt_partner_payment_updated")
            );

        } elseif ($count_fail) {
            $top_message = array(
                'type' => 'E',
                'content' => func_get_langvar_by_name("txt_partner_payment_updated_error")
            );
        }

        func_header_location('partner_orders.php?mode=search');
    }

    if ($start_date) {
        $search['start_date'] = func_prepare_search_date($start_date);
        $search['end_date']   = func_prepare_search_date($end_date, true);
    }

    $search['delimiter'] = $delimiter;

    $search_data['partner_orders'] = $search;
    if ($mode != 'export')
        func_header_location("partner_orders.php?mode=search");
}

if (($REQUEST_METHOD == 'GET' && $mode == 'search') || ($REQUEST_METHOD == 'POST' && $mode == 'export')) {
    $search = $search_data['partner_orders'];

    $where = array();

    if ($search['start_date'] && $search['end_date'])
        $where[] = $search['end_date']." > $sql_tbl[orders].date AND $sql_tbl[orders].date > ".$search['start_date'];

    if ($search['userid'])
        $where[] = "$sql_tbl[partner_payment].userid = '$search[userid]'";

    if($search['status'])
        $where[] = "$sql_tbl[orders].status = '$search[status]'";

    if($search['orderid'])
        $where[] = "$sql_tbl[orders].orderid = '$search[orderid]'";

    if ($search['paid'])
        $where[] = " IF($sql_tbl[partner_payment].paid = 'Y', 'Y', IF($sql_tbl[orders].status IN ('C','P'), 'A', 'N')) = '$search[paid]'";

    if ($where)
        $where_condition = " AND ".implode(" AND ", $where);

    $report = func_query("SELECT $sql_tbl[partner_payment].*, $sql_tbl[customers].*, ($sql_tbl[orders].subtotal-$sql_tbl[orders].discount-$sql_tbl[orders].coupon_discount) as subtotal, $sql_tbl[orders].date, $sql_tbl[orders].status AS order_status, IF($sql_tbl[partner_payment].paid = 'Y', 'Y', IF($sql_tbl[orders].status IN ('C','P'), 'A', '')) as paid FROM $sql_tbl[partner_payment], $sql_tbl[orders], $sql_tbl[customers] WHERE $sql_tbl[partner_payment].userid = $sql_tbl[customers].id AND $sql_tbl[partner_payment].orderid = $sql_tbl[orders].orderid AND $sql_tbl[customers].status = 'Y' AND $sql_tbl[customers].usertype = 'B'".$where_condition." ORDER BY $sql_tbl[partner_payment].add_date, $sql_tbl[customers].id");
    $orders_cnt = is_array($report) ? count($report) : 0;

    if ($mode == 'export') {
        if ($report) {
            foreach ($report as $key => $value) {
                foreach ($value as $rk => $rv) {
                    $report[$key][$rk] = '"' . str_replace ("\"", "\"\"", $report[$key][$rk]) . '"';
                }
            }

            $smarty->assign ('report', $report);
            $smarty->assign ('delimiter', $search['delimiter']);

            header("Content-Type: text/csv");
            header("Content-Disposition: attachment; filename=partner_orders.csv");
            func_display('admin/main/partner_orders_export.tpl', $smarty);
            exit;

        } else {
            func_header_location("partner_orders.php?mode=search");

        }

    } elseif ($mode == 'search') {

        if (!empty($report)) {

            $users = array();

            foreach($report as $k => $v) {
                if (!in_array($v['userid'], $users)) {
                    $users[] = $v['userid'];
                }

                if ($v['affiliate'] > 0) {
                    $report[$k]['affiliate_login'] = func_get_login_by_userid($v['affiliate']);
                }
            }

            $ready = array();
            foreach($users as $v) {
                $sum = func_query_first_cell("SELECT SUM($sql_tbl[partner_payment].commissions) FROM $sql_tbl[partner_payment], $sql_tbl[orders] WHERE $sql_tbl[partner_payment].userid = '$v' AND $sql_tbl[partner_payment].orderid = $sql_tbl[orders].orderid AND $sql_tbl[orders].status IN ('C','P') AND $sql_tbl[partner_payment].paid != 'Y'");
                $min_paid = func_query_first_cell("SELECT $sql_tbl[partner_plans].min_paid FROM $sql_tbl[partner_plans], $sql_tbl[partner_commissions] WHERE $sql_tbl[partner_plans].plan_id = $sql_tbl[partner_commissions].plan_id AND $sql_tbl[partner_commissions].userid = '$v'");
                if ($sum >= $min_paid)
                    $ready[$v] = $min_paid;
            }

            if (count($ready) > 0) {
                foreach($report as $k => $v) {
                    if ($v['paid'] == 'A' && isset($ready[$v['userid']]))
                        $report[$k]['ready'] = true;
                }

                $arr = func_query("SELECT firstname, lastname, id AS userid, login FROM $sql_tbl[customers] WHERE id IN ('" . implode("','", array_keys($ready)) . "') ORDER BY login");
                if ($arr) {
                    foreach($arr as $k => $v) {
                        $arr[$k]['min_paid'] = isset($ready[$v['userid']]) ? doubleval($ready[$v['userid']]) : 0;
                    }
                    $smarty->assign('ready', $arr);
                }
            }

            unset($users, $ready);

        } elseif (empty($top_message['content'])) {
            $no_results_warning = array(
                'type' => 'W',
                'content' => func_get_langvar_by_name("lbl_warning_no_search_results", false, false, true)
            );
            $smarty->assign('top_message', $no_results_warning);
        }
    }

    $smarty->assign ('orders', $report);
    $smarty->assign ('orders_cnt', $orders_cnt);
}

$smarty->assign ('main', 'partner_orders');

$partners = func_query("SELECT id AS userid, login, firstname, lastname FROM $sql_tbl[customers] WHERE usertype = 'B' AND status = 'Y' ORDER BY login");
if ($partners) {
    $smarty->assign('partners', $partners);
}

$smarty->assign('search', $search_data['partner_orders']);
$smarty->assign('mode', $mode);
$smarty->assign ('month_begin', mktime(0, 0, 0, date('m'), 1, date('Y')));

$smarty->assign('dialog_tools_data', $dialog_tools_data);

// Assign the current location line
$smarty->assign('location', $location);

if (
    file_exists($xcart_dir.'/modules/gold_display.php')
    && is_readable($xcart_dir.'/modules/gold_display.php')
) {
    include $xcart_dir.'/modules/gold_display.php';
}
func_display('admin/home.tpl',$smarty);
?>
