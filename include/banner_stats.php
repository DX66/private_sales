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
 * Display banners statistics
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: banner_stats.php,v 1.36.2.2 2011/02/11 14:42:02 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_session_register('search_data');

/**
 * Callback function for banners list sorting
 */
function func_banners_sort($a, $b)
{
    if ($a['bannerid'] == $b['bannerid']) {
        if ($a['target_name'] == $b['target_name'])
            return 0;

        return $a['target_name'] > $b['target_name'] ? 1 : -1;
    }

    return $a['bannerid'] > $b['bannerid'] ? 1 : -1;
}

if ($REQUEST_METHOD == 'POST') {
    if ($start_date) {
        $search['start_date'] = func_prepare_search_date($start_date);
        $search['end_date']   = func_prepare_search_date($end_date, true);
    }

    if ($search)
        $search_data['banner_stats'] = $search;

    func_header_location("banner_info.php?mode=search");
}

if ($mode == 'search') {
    if (!isset($search_data['banner_stats']) || empty($search_data['banner_stats'])) {
        func_header_location('banner_info.php');
    }

    $search = $search_data['banner_stats'];

    if ($current_area == 'B') {
        $search['partner'] = $logged_userid;
    }

    $where = array();
    $where_clicks = array();

    if ($search['start_date'] && $search['end_date']) {
        $where[] = "'$search[end_date]' > $sql_tbl[partner_views].add_date AND $sql_tbl[partner_views].add_date > '$search[start_date]'";
        $where_clicks[] = "'$search[end_date]' > $sql_tbl[partner_clicks].add_date AND $sql_tbl[partner_clicks].add_date > '$search[start_date]'";
    }

    if ($search['partner']) {
        $where[] = "$sql_tbl[partner_views].userid = '$search[partner]'";
        $where_clicks[] = "$sql_tbl[partner_clicks].userid = '$search[partner]'";
    }

    if ($where)
        $where_condition = " AND ".implode(" AND ", $where);

    if ($where_clicks)
        $where_clicks_condition = " AND ".implode(" AND ", $where_clicks);

    $views = func_query("SELECT $sql_tbl[partner_banners].*, $sql_tbl[partner_views].*, COUNT($sql_tbl[partner_views].bannerid) as views, 0 as order_count, 0 as total FROM $sql_tbl[partner_banners] INNER JOIN $sql_tbl[partner_views] ON $sql_tbl[partner_banners].bannerid = $sql_tbl[partner_views].bannerid $where_condition GROUP BY $sql_tbl[partner_banners].bannerid, $sql_tbl[partner_views].target, $sql_tbl[partner_views].targetid");

    $clicks = func_query("SELECT $sql_tbl[partner_banners].*, $sql_tbl[partner_clicks].*, COUNT($sql_tbl[partner_clicks].bannerid) as clicks, COUNT($sql_tbl[orders].total) as order_count, SUM($sql_tbl[orders].total) as total FROM $sql_tbl[partner_banners] INNER JOIN $sql_tbl[partner_clicks] ON $sql_tbl[partner_banners].bannerid = $sql_tbl[partner_clicks].bannerid $where_clicks_condition LEFT JOIN $sql_tbl[orders] ON $sql_tbl[orders].clickid = $sql_tbl[partner_clicks].clickid GROUP BY $sql_tbl[partner_banners].bannerid, $sql_tbl[partner_clicks].target, $sql_tbl[partner_clicks].targetid");
    $clicks_unknown = func_query("SELECT $sql_tbl[partner_clicks].*, COUNT($sql_tbl[partner_clicks].clickid) as clicks, COUNT($sql_tbl[orders].total) as order_count, SUM($sql_tbl[orders].total) as total FROM $sql_tbl[partner_clicks] LEFT JOIN $sql_tbl[orders] ON $sql_tbl[orders].clickid = $sql_tbl[partner_clicks].clickid WHERE $sql_tbl[partner_clicks].bannerid = '' $where_clicks_condition GROUP BY $sql_tbl[partner_clicks].target, $sql_tbl[partner_clicks].targetid");
    if (!is_array($clicks))
        $clicks = array();

    if (is_array($clicks_unknown))
        $clicks = func_array_merge($clicks, $clicks_unknown);

    $total = array('clicks' => 0, 'views' => 0);
    if (!empty($views) || !empty($clicks)) {
        $banners = array();

        $is_empty = false;
        if (empty($views)) {
            $tmp = $clicks;
            $is_empty = 'V';

        } elseif (empty($clicks)) {
            $tmp = $views;
            $is_empty = 'C';

        } else {
            $tmp = func_array_merge($views, $clicks);
        }

        $len = count($tmp);
        for($k = 0; $k < $len; $k++) {
            if (empty($tmp[$k]))
                continue;

            $v = $tmp[$k];

            if ($v['target'] && $v['targetid']) {
                switch ($v['target']) {
                    case 'P':
                        $v['target_name'] = func_query_first_cell("SELECT product FROM $sql_tbl[products] WHERE productid = '$v[targetid]'");
                        break;

                    case 'C':
                        $v['target_name'] = func_query_first_cell("SELECT category FROM $sql_tbl[categories] WHERE categoryid = '$v[targetid]'");
                        break;

                    case 'F':
                        $v['target_name'] = func_query_first_cell("SELECT manufacturer FROM $sql_tbl[manufacturers] WHERE manufacturerid = '$v[targetid]'");
                        break;
                }
            }

            if ($is_empty == 'V') {
                $v['views'] = 0;
                $v['click_rate'] = 0;

            } elseif ($is_empty == 'C') {
                $v['clicks'] = 0;
                $v['click_rate'] = 0;

            } else {

                for ($i = $k+1; $i < $len; $i++) {
                    if (
                        empty($tmp[$i]) ||
                        $tmp[$i]['bannerid'] != $v['bannerid'] ||
                        $tmp[$i]['target'] != $v['target'] ||
                        $tmp[$i]['targetid'] != $v['targetid']
                    )
                        continue;

                    if (!isset($v['clicks'])) {
                        $v['clicks'] = $tmp[$i]['clicks'];
                        $v['order_count'] = $tmp[$i]['order_count'];
                        $v['total'] = $tmp[$i]['total'];

                    } else {
                        $v['views'] = $tmp[$i]['views'];
                    }
                    $tmp[$i] = false;
                    break;
                }

                if (!isset($v['clicks']))
                    $v['clicks'] = 0;

                if (!isset($v['views']))
                    $v['views'] = 0;

                if ($v['clicks'] == 0 || $v['views'] == 0) {
                    $v['click_rate'] = 0;
                } else {
                    $v['click_rate'] = round($v['clicks'] / $v['views'], 2);
                }
            }

            $total['clicks'] += $v['clicks'];
            $total['views'] += $v['views'];
            $total['order_count'] += $v['order_count'];
            $total['total'] += $v['total'];

            $banners[] = $v;
        }

        foreach($banners as $k => $v) {
            $banners[$k]['cr'] = $v['total'] > 0 ? round($v['total'] / $v['clicks'], 2) : 0;
        }

        $total['cr'] = $total['total'] > 0 ? round($total['total'] / $total['clicks'], 2) : 0;

        unset($tmp, $views, $clicks, $len);

        if ($total['clicks'] == 0 || $total['views'] == 0) {
            $total['click_rate'] = 0;

        } else {
            $total['click_rate'] = round($total['clicks']/$total['views'], 2);
        }

        usort($banners, 'func_banners_sort');
        $smarty->assign ('banners', $banners);
        $smarty->assign ('total', $total);
    }

    if ($total['clicks'] == 0 && $total['views'] == 0 && empty($top_message['content'])) {
        $no_results_warning = array(
            'type' => 'W',
            'content' => func_get_langvar_by_name("lbl_warning_no_search_results", false, false, true)
        );
        $smarty->assign('top_message', $no_results_warning);
    }
}

$smarty->assign ('partners', func_query("SELECT * FROM $sql_tbl[customers] WHERE usertype = 'B' AND status = 'Y'"));

$smarty->assign ('search', $search);
$smarty->assign ('month_begin', mktime(0, 0, 0, date('m'), 1, date('Y')));

?>
