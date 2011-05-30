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
 * Display advertising statistics
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: referred_sales.php,v 1.30.2.1 2011/01/10 13:11:50 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

function func_sales_sort_callback($a, $b)
{
    global $search;

    if (!isset($a[$search['sort']]) || !isset($b[$search['sort']]) || $a[$search['sort']] == $b[$search['sort']])
        return 0;

    return ($a[$search['sort']] < $b[$search['sort']] ? -1 : 1) * ($search['sort_direction'] == 1 ? 1 : -1);
}

x_session_register('search_data');

$save_key = 'reffered_sales_'.$current_area;

if ($REQUEST_METHOD == 'POST') {

    if ($start_date) {
        $search['start_date'] = func_prepare_search_date($start_date);
        $search['end_date']   = func_prepare_search_date($end_date, true);
    }

    $search_data[$save_key] = $search;

    func_header_location("referred_sales.php?mode=search");
}

if ($REQUEST_METHOD == 'GET' && $mode == 'search') {

    if (isset($_GET['sort']))
        $search_data[$save_key]['sort'] = $_GET['sort'];

    if (isset($_GET['sort_direction']))
        $search_data[$save_key]['sort_direction'] = $_GET['sort_direction'];

    if (!isset($search_data[$save_key]['sort']))
        $search_data[$save_key]['sort'] = ($config['XAffiliate']['partner_allow_see_total'] != 'Y' && $current_area == 'B') ? 'product' : 'total';

    if (!isset($search_data[$save_key]['sort_direction']))
        $search_data[$save_key]['sort_direction'] = 1;

    $search = $search_data[$save_key];

    $where = array();

    if ($search['partner'])
        $where[] = "$sql_tbl[customers].id = '$search[partner]'";

    if ($search['productcode'])
        $where[] = "$sql_tbl[products].productcode = '$search[productcode]'";

    if ($search['status'])
        $where[] = "IF($sql_tbl[partner_payment].paid = 'Y', 'Y', IF($sql_tbl[orders].status IN ('C','P'), 'A', 'N')) = '$search[status]'";

    if ($search['start_date'] && $search['end_date'])
        $where[] = "'".$search['end_date']."' > $sql_tbl[partner_payment].add_date AND $sql_tbl[partner_payment].add_date > '".$search['start_date']."'";

    if ($current_area == 'B')
        $where[] = "$sql_tbl[partner_payment].userid = '$logged_userid'";

    if ($where)
        $where_condition = " AND ".implode(" AND ", $where);

    $sales = array();
    if ($search['top']) {
        $res = db_query("SELECT $sql_tbl[products].product, $sql_tbl[products].productid, $sql_tbl[partner_payment].commissions,  $sql_tbl[order_details].amount, $sql_tbl[partner_product_commissions].product_commission, $sql_tbl[order_details].extra_data FROM $sql_tbl[customers], $sql_tbl[partner_payment], $sql_tbl[order_details], $sql_tbl[products], $sql_tbl[orders], $sql_tbl[partner_product_commissions] WHERE $sql_tbl[partner_product_commissions].itemid = $sql_tbl[order_details].itemid AND $sql_tbl[partner_product_commissions].orderid = $sql_tbl[order_details].orderid AND $sql_tbl[partner_product_commissions].userid = $sql_tbl[customers].id AND $sql_tbl[customers].usertype = 'B' AND $sql_tbl[customers].status = 'Y' AND $sql_tbl[partner_payment].userid = $sql_tbl[customers].id AND $sql_tbl[partner_payment].orderid = $sql_tbl[order_details].orderid AND $sql_tbl[orders].orderid = $sql_tbl[order_details].orderid AND $sql_tbl[order_details].productid = $sql_tbl[products].productid ".$where_condition);

        if ($res) {
            while ($row = db_fetch_array($res)) {
                $row['total'] = 0;
                if (!empty($row['extra_data'])) {
                    $row['extra_data'] = unserialize($row['extra_data']);
                    if (is_array($row['extra_data']))
                        $row['total'] = $row['extra_data']['display']['discounted_price'];
                }

                if (!isset($sales[$row['productid']])) {
                    $sales[$row['productid']] = $row;
                    $sales[$row['productid']]['sales'] = 1;
                    continue;
                }

                $sales[$row['productid']]['commissions'] += $row['commissions'];
                $sales[$row['productid']]['amount'] += $row['amount'];
                $sales[$row['productid']]['product_commission'] += $row['product_commission'];
                $sales[$row['productid']]['total'] += $row['total'];
                $sales[$row['productid']]['sales']++;
            }
            db_free_result($res);

            usort($sales, 'func_sales_sort_callback');
        }

    } else {
        $sales = func_query("SELECT $sql_tbl[partner_product_commissions].product_commission, $sql_tbl[partner_payment].*, $sql_tbl[customers].*, $sql_tbl[order_details].*, $sql_tbl[products].product, $sql_tbl[products].productid, $sql_tbl[order_details].extra_data, IF($sql_tbl[partner_payment].paid = 'Y', 'Y', IF($sql_tbl[orders].status IN ('C','P'), 'A', '')) as paid FROM $sql_tbl[customers], $sql_tbl[partner_payment], $sql_tbl[order_details], $sql_tbl[products], $sql_tbl[orders], $sql_tbl[partner_product_commissions] WHERE $sql_tbl[partner_product_commissions].itemid = $sql_tbl[order_details].itemid AND $sql_tbl[partner_product_commissions].orderid = $sql_tbl[order_details].orderid AND $sql_tbl[partner_product_commissions].userid = $sql_tbl[customers].id AND $sql_tbl[customers].usertype = 'B' AND $sql_tbl[customers].status = 'Y' AND $sql_tbl[partner_payment].userid = $sql_tbl[customers].id AND $sql_tbl[partner_payment].orderid = $sql_tbl[order_details].orderid AND $sql_tbl[orders].orderid = $sql_tbl[order_details].orderid AND $sql_tbl[order_details].productid = $sql_tbl[products].productid ".$where_condition." GROUP BY $sql_tbl[partner_payment].payment_id, $sql_tbl[order_details].itemid");

        if (!empty($sales)) {

            $total = 0;
            foreach ($sales as $k => $v) {
                $sales[$k]['total'] = 0;
                if (!empty($v['extra_data'])) {
                    $v['extra_data'] = unserialize($v['extra_data']);
                    if (is_array($v['extra_data'])) {
                        $sales[$k]['total'] = $v['extra_data']['display']['discounted_price'];
                    }
                    if ($v['parent'] > 0) {
                        $sales[$k]['parent_login'] = func_get_login_by_userid($v['parent']);
                    }
                }
                $total += $sales[$k]['total'];
            }

            $smarty->assign('total_total', $total);

            usort($sales, 'func_sales_sort_callback');

            if ($current_area == 'B') {
                $level = func_get_affiliate_level(addslashes($logged_userid));
                $parent_pending = 0;
                $parent_paid = 0;
                foreach ($sales as $k => $v) {
                    if (!empty($v['affiliate'])) {
                        if ($v['paid'] == 'Y')
                            $parent_paid += $v['product_commission'];
                        else
                            $parent_pending += $v['product_commission'];
                        $sales[$k]['level'] = func_get_affiliate_level(addslashes($v['affiliate']));
                        $sales[$k]['level_delta'] = $sales[$k]['level'] - $level + 1;
                    }
                }
                $smarty->assign('parent_pending', $parent_pending);
                $smarty->assign('parent_paid', $parent_paid);
            }

        }

    }

    if ($sales) {

        $total_amount = 0;
        $total_product_commissions = 0;
        foreach ($sales as $s) {
            $total_amount += $s['amount'];
            $total_product_commissions += $s['product_commission'];
        }

        $smarty->assign('sales', $sales);
        $smarty->assign('total_amount', $total_amount);
        $smarty->assign('total_product_commissions', $total_product_commissions);

    } elseif (empty($top_message['content'])) {

        $no_results_warning = array(
            'type' => 'W',
            'content' => func_get_langvar_by_name("lbl_warning_no_search_results", false, false, true)
        );
        $smarty->assign('top_message', $no_results_warning);
    }
}

$smarty->assign('search', $search_data[$save_key]);

$smarty->assign('month_begin', mktime(0, 0, 0, date('m'), 1, date('Y')));
?>
