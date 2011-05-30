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
 * Shop statistics interface
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: atracking.php,v 1.48.2.1 2011/01/10 13:11:44 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

$date = "date+'".$config["Appearance"]["timezone_offset"]."'";
$date_condition = "($date>='$start_date' AND $date<='$end_date')";

$cart_funnel = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[stats_cart_funnel] WHERE $date_condition");
$product_funnel = func_query_first_cell("SELECT COUNT(pv.pageid) FROM $sql_tbl[stats_pages_views] as pv, $sql_tbl[stats_pages] as p WHERE p.pageid=pv.pageid AND p.page LIKE '%product.php?productid=%' AND $date_condition");

$all_views = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[stats_pages_views] WHERE $date_condition");

if ($mode == 'pagesviews') {
/**
 * Display page views statistics
 */
    $location[] = array(func_get_langvar_by_name('lbl_top_pages_views'), '');

    $statistics = func_query("SELECT p.page, COUNT(pv.pageid) as views, ROUND(AVG(pv.time_avg)) as time_avg FROM $sql_tbl[stats_pages_views] as pv, $sql_tbl[stats_pages] as p WHERE p.pageid=pv.pageid AND $date_condition GROUP BY p.page ORDER BY views DESC, time_avg DESC LIMIT 50");

    if ($statistics) {
        foreach($statistics as $k => $v) {
            $statistics[$k]['percent'] = $v['views'] / $all_views * 100;
        }
    }

} elseif ($mode == 'toppaths') {
/**
 * Display top site paths statistics
 */
    $location[] = array(func_get_langvar_by_name('lbl_top_paths_thru_site'), '');

    $all_views = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[stats_pages_paths] WHERE path LIKE '%-%' AND $date_condition");
    $statistics = func_query("SELECT path, COUNT(path) as counter FROM $sql_tbl[stats_pages_paths] WHERE path LIKE '%-%' AND $date_condition GROUP BY path ORDER BY counter DESC LIMIT 25");
    if (!empty($statistics))
        foreach($statistics as $k => $v) {
            $pages = explode("-", $v['path']);
            if (!empty($pages)) {
                foreach($pages as $k1=>$v1) {
                    $query = "SELECT p.page, ROUND(AVG(pv.time_avg),2) as time_avg FROM $sql_tbl[stats_pages_views] as pv, $sql_tbl[stats_pages] as p WHERE p.pageid=pv.pageid AND pv.pageid='$v1' AND $date_condition GROUP BY p.page";
                    $tmp = func_query_first($query);
                    $statistics[$k][pages][] = $tmp;
                }
            }
            $statistics[$k]['percent'] = $v['counter'] / $all_views * 100;
        }

} elseif ($mode == 'cartfunnel' && ($cart_funnel > 0 || $product_funnel > 0)) {
/**
 * Display cart conversion funnel statistics
 */
    $location[] = array(func_get_langvar_by_name('lbl_shopping_cart_conversion_funnel'), '');

    $statistics[0][step] = 'product_page';
    $statistics[0][visits] = $product_funnel;
    $statistics[0][percent_all] = sprintf("%2.02f", ( $statistics[0][visits] / ($all_views?$all_views:1) ) * 100);
    $statistics[1][step] = 'start_page';
    $statistics[1][visits] = func_query_first_cell("SELECT count(start_page) FROM $sql_tbl[stats_cart_funnel] WHERE start_page>'0' AND $date_condition");
    $statistics[1][percent_parent] = sprintf("%2.02f", ( $statistics[1][visits] / ($statistics[0][visits]?$statistics[0][visits]:1) ) * 100);
    $statistics[1][percent_all] = sprintf("%2.02f", ( $statistics[1][visits] / ($all_views?$all_views:1) ) * 100);
    $statistics[2][step] = 'step1';
    $statistics[2][visits] = func_query_first_cell("SELECT count(step1) FROM $sql_tbl[stats_cart_funnel] WHERE step1>'0' AND $date_condition");
    $statistics[2][percent_parent] = sprintf("%2.02f", ( $statistics[2][visits] / ($statistics[1][visits]?$statistics[1][visits]:1) ) * 100);
    $statistics[2][percent_all] = sprintf("%2.02f", ( $statistics[2][visits] / ($all_views?$all_views:1) ) * 100);
    $statistics[3][step] = 'step2';
    $statistics[3][visits] = func_query_first_cell("SELECT count(step2) FROM $sql_tbl[stats_cart_funnel] WHERE step2>'0' AND $date_condition");
    $statistics[3][percent_parent] = sprintf("%2.02f", ( $statistics[1][visits] + $statistics[2][visits] ) == 0 ? 0 : (( $statistics[3][visits] / ( $statistics[1][visits] + $statistics[2][visits] ) ) * 100));
    $statistics[3][percent_all] = sprintf("%2.02f", ( $statistics[3][visits] / ($all_views?$all_views:1) ) * 100);
    $statistics[3][percent_parent2] = sprintf("%2.02f", ( $statistics[3][visits] / ($statistics[0][visits]?$statistics[0][visits]:1) ) * 100);
    $statistics[4][step] = 'step3';
    $statistics[4][visits] = func_query_first_cell("SELECT count(step3) FROM $sql_tbl[stats_cart_funnel] WHERE step3>'0' AND $date_condition");
    $statistics[4][percent_parent] = sprintf("%2.02f", ( $statistics[4][visits] / ($statistics[3][visits]?$statistics[3][visits]:1) ) * 100);
    $statistics[4][percent_all] = sprintf("%2.02f", ( $statistics[4][visits] / ($all_views?$all_views:1) ) * 100);
    $statistics[5][step] = 'final_page';
    $statistics[5][visits] = func_query_first_cell("SELECT count(final_page) FROM $sql_tbl[stats_cart_funnel] WHERE final_page>'0' AND $date_condition");
    $statistics[5][percent_parent] = sprintf("%2.02f", ( $statistics[5][visits] / ($statistics[4][visits]?$statistics[4][visits]:1) ) * 100);
    $statistics[5][percent_all] = sprintf("%2.02f", ( $statistics[5][visits] / ($all_views?$all_views:1) ) * 100);
}
elseif ($mode == 'cartfunnel') {
/**
 * Display empty cart conversion funnel statistics
 */
    $location[] = array(func_get_langvar_by_name('lbl_shopping_cart_conversion_funnel'), '');
}
elseif ($mode == 'logins') {
/**
 * Display login history
 */
    $location[] = array(func_get_langvar_by_name('lbl_log_in_history'), '');

    $date = "$sql_tbl[login_history].date_time+'" . $config["Appearance"]["timezone_offset"] . "'";
    $date_condition = "($date >= '$start_date' AND $date <= '$end_date')";

    if ($REQUEST_METHOD == 'POST') {

    // Delete log in history

        if ($action == 'delete') {
            db_query("DELETE FROM $sql_tbl[login_history] WHERE ".$date_condition);
            $top_message['content'] = func_get_langvar_by_name('msg_adm_loginhistory_range_del');
        }
        elseif ($action == 'delete_all') {
            db_query("DELETE FROM $sql_tbl[login_history]");
            $top_message['content'] = func_get_langvar_by_name('msg_adm_loginhistory_all_del');
        }
        func_header_location("statistics.php?".$QUERY_STRING);
    }

    $statistics = func_query("SELECT $sql_tbl[login_history].*, $sql_tbl[customers].login FROM $sql_tbl[login_history], $sql_tbl[customers] WHERE $sql_tbl[login_history].userid = $sql_tbl[customers].id AND ".$date_condition." ORDER BY date_time DESC");
    if (!empty($statistics)) {
        foreach ($statistics as $k=>$v) {
            $statistics[$k]['date_time'] += $config['Appearance']['timezone_offset'];
        }
    }
}
else {
    $location[count($location)-1][1] = '';
}

$smarty->assign('mode', $mode);
$smarty->assign('start_date', $start_date);
$smarty->assign('end_date', $end_date);
$smarty->assign('all_views', $all_views);
$smarty->assign('statistics', $statistics);
$smarty->assign('main','atracking');

?>
