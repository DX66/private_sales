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
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: partner_adv_stats.php,v 1.31.2.2 2011/01/25 09:43:11 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './auth.php';
require $xcart_dir.'/include/security.php';

if (empty($active_modules['XAffiliate']))
    func_403(17);

$location[] = array(func_get_langvar_by_name('lbl_adv_statistics'), '');

/**
 * Define data for the navigation within section
 */
$dialog_tools_data['left'][] = array('link' => 'banner_info.php', 'title' => func_get_langvar_by_name('lbl_banners_statistics'));
$dialog_tools_data['left'][] = array('link' => 'referred_sales.php', 'title' => func_get_langvar_by_name('lbl_referred_sales'));
$dialog_tools_data['left'][] = array('link' => 'partner_top_performers.php', 'title' => func_get_langvar_by_name('lbl_top_performers'));
$dialog_tools_data['left'][] = array('link' => 'affiliates.php', 'title' => func_get_langvar_by_name('lbl_affiliates_tree'));
$dialog_tools_data['left'][] = array('link' => 'partner_adv_stats.php', 'title' => func_get_langvar_by_name('lbl_adv_statistics'));
$dialog_tools_data['right'][] = array('link' => 'partner_adv_campaigns.php', 'title' => func_get_langvar_by_name('lbl_advertising_campaigns'));

if ($start_date) {
    $search['start_date'] = func_prepare_search_date($start_date);
    $search['end_date']   = func_prepare_search_date($end_date, true);
}

if ($search) {
    $where = array();
    if ($search['campaignid'])
        $where[] = "$sql_tbl[partner_adv_campaigns].campaignid = '$search[campaignid]'";

    if ($search['start_date'] && $search['end_date']) {
        if($search['end_date'] < $search['start_date']) {
            $tmp = $search['end_date'];
            $search['end_date'] = $search['start_date'];
            $search['start_date'] = $tmp;
        }

        $where[] = " ($sql_tbl[partner_adv_campaigns].start_period BETWEEN $search[start_date] AND $search[end_date] OR $sql_tbl[partner_adv_campaigns].end_period BETWEEN $search[start_date] AND $search[end_date] OR $search[start_date] BETWEEN $sql_tbl[partner_adv_campaigns].start_period AND $sql_tbl[partner_adv_campaigns].end_period OR $search[end_date] BETWEEN $sql_tbl[partner_adv_campaigns].start_period AND $sql_tbl[partner_adv_campaigns].end_period)";
    }

    if ($where)
        $where = " WHERE ".implode(" AND ", $where);

    $result = func_query("SELECT $sql_tbl[partner_adv_campaigns].*, COUNT($sql_tbl[partner_adv_clicks].add_date) as clicks FROM $sql_tbl[partner_adv_campaigns] LEFT JOIN $sql_tbl[partner_adv_clicks] ON $search[end_date] > $sql_tbl[partner_adv_clicks].add_date AND $sql_tbl[partner_adv_clicks].add_date > $search[start_date] AND $sql_tbl[partner_adv_campaigns].campaignid = $sql_tbl[partner_adv_clicks].campaignid $where GROUP BY $sql_tbl[partner_adv_campaigns].campaignid");
    if ($result) {
        $total = array();
        foreach ($result as $k => $v) {
            $start = (($v['start_period'] > $search['start_date'])?$v['start_period']:$search['start_date']);
            $end = (($v['end_period'] < $search['end_date'])?$v['end_period']:$search['end_date']);

            $per_day = $v['per_period']/ceil(abs($v['end_period']-$v['start_period'])/86400);
            $v['ee'] = round(($v['per_visit']*$v['clicks']) + $per_day*ceil(abs($end-$start)/86400), 2);
            $tmp = func_query_first("SELECT SUM($sql_tbl[orders].total) as sum, COUNT($sql_tbl[orders].total) as cnt FROM $sql_tbl[orders], $sql_tbl[partner_adv_orders] WHERE $sql_tbl[partner_adv_orders].campaignid = '$v[campaignid]' AND $sql_tbl[partner_adv_orders].orderid = $sql_tbl[orders].orderid AND $sql_tbl[orders].status IN ('P', 'C') AND $sql_tbl[orders].date BETWEEN $start AND $end");
            if($v['ee'] > 0 && $tmp['cnt'] > 0) {
                $v['acost'] = $v['ee']/$tmp['cnt'];
            } else {
                $v['acost'] = 0;
            }

            $v['total'] = $tmp['sum'];
            if($v['total'] > 0 && $v['ee'] > 0) {
                $v['roi'] = round($v['total']/$v['ee']*100, 2);
            } else {
                $v['roi'] = 0;
            }

            $total['clicks'] += $v['clicks'];
            $total['ee'] += $v['ee'];
            $total['acost'] += $v['acost'];
            $total['total'] += $v['total'];
            $result[$k] = $v;
        }

        $total['roi'] = 0;
        if ($total['total'] > 0 && $total['ee'] > 0) {
            $total['roi'] = round($total['total']/$total['ee']*100, 2);
        }

        $smarty->assign('total', $total);
        $smarty->assign('result', $result);
    } elseif (empty($top_message['content'])) {
        $no_results_warning = array('type' => 'W', 'content' => func_get_langvar_by_name("lbl_warning_no_search_results", false, false, true));
        $smarty->assign('top_message', $no_results_warning);
    }
}

$campaigns = func_query("SELECT * FROM $sql_tbl[partner_adv_campaigns]");
$smarty->assign('campaigns', $campaigns);

$smarty->assign('search', $search);

$smarty->assign ('main', 'partner_adv_stats');

$smarty->assign ('month_begin', mktime(0,0,0,date('m'),1,date('Y')));

// Assign the current location line
$smarty->assign('location', $location);

// Assign the section navigation data
$smarty->assign('dialog_tools_data', $dialog_tools_data);

if (
    file_exists($xcart_dir.'/modules/gold_display.php')
    && is_readable($xcart_dir.'/modules/gold_display.php')
) {
    include $xcart_dir.'/modules/gold_display.php';
}
func_display('admin/home.tpl',$smarty);
?>
