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
 * Provider commissions library
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: commissions.php,v 1.10.2.3 2011/01/20 08:06:35 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if ($REQUEST_METHOD == 'POST') {

    /**
     * Update the session $search_data variable from $posted_data
     */
    if ($start_date) {
        $search['start_date'] = func_prepare_search_date($start_date);
        $search['end_date']   = func_prepare_search_date($end_date, true);
    }

    $search_data['commissions'] = $search;

    func_header_location('commissions.php?mode=search');
}

if ($mode == 'search') {

    /**
     * Process search action
     */
    $search = $search_data['commissions'];

    $where = array();

    if (!empty($search['start_date']) && !empty($search['end_date'])) {
        $where[] = $search['end_date']." > $sql_tbl[orders].date AND $sql_tbl[orders].date > ".$search['start_date'];
    }

    if (!empty($search['userid'])) {
        $where[] = "$sql_tbl[provider_payment].userid = '$search[userid]'";
    }

    if (!empty($search['status'])) {
        $where[] = "$sql_tbl[orders].status = '$search[status]'";
    }

    if (!empty($search['orderid'])) {
        $where[] = "$sql_tbl[orders].orderid = '$search[orderid]'";
    }

    if (!empty($search['paid'])) {
        $where[] = " IF($sql_tbl[provider_commissions].paid = 'Y', 'Y', IF($sql_tbl[orders].status IN ('C','P'), 'A', 'N')) = '$search[paid]'";
    }

    if (
        empty($active_modules['Simple_Mode'])
        && $current_area == 'P'
    ) {
        $where[] =  "$sql_tbl[provider_commissions].userid='$logged_userid'";
    } elseif (!empty($search['provider'])) {
        $where[] = " $sql_tbl[provider_commissions].userid='" . $search['provider'] . "'";
    }

    if (!empty($where)) {
        $where_condition = " AND " . implode(" AND ", $where);
    }

    $report = func_query("SELECT $sql_tbl[provider_commissions].*, ($sql_tbl[provider_commissions].commissions - $sql_tbl[provider_commissions].paid_commissions) AS not_paid_commissions, $sql_tbl[customers].firstname, $sql_tbl[customers].lastname, $sql_tbl[customers].login, ($sql_tbl[orders].subtotal-$sql_tbl[orders].discount-$sql_tbl[orders].coupon_discount) as subtotal, $sql_tbl[orders].date, $sql_tbl[orders].status AS order_status, IF($sql_tbl[provider_commissions].paid = 'Y', 'Y', IF($sql_tbl[orders].status IN ('C','P'), 'A', 'N')) as paid FROM $sql_tbl[provider_commissions] INNER JOIN $sql_tbl[orders] ON $sql_tbl[orders].orderid=$sql_tbl[provider_commissions].orderid INNER JOIN $sql_tbl[customers] ON $sql_tbl[customers].id = $sql_tbl[provider_commissions].userid AND $sql_tbl[customers].status = 'Y' AND $sql_tbl[customers].usertype = 'P' WHERE 1 ".$where_condition." ORDER BY $sql_tbl[provider_commissions].add_date, $sql_tbl[customers].id");

    if (!empty($report)) {

        $users = array();

        foreach($report as $k => $v) {
            if (!in_array($v['userid'], $users)) {
                $users[] = $v['userid'];
            }
        }

        $ready = array();

        foreach($users as $userid) {

            $sum = func_query_first_cell("SELECT SUM($sql_tbl[provider_commissions].commissions) FROM $sql_tbl[provider_commissions], $sql_tbl[orders] WHERE $sql_tbl[provider_commissions].userid = '$userid' AND $sql_tbl[provider_commissions].orderid = $sql_tbl[orders].orderid AND $sql_tbl[orders].status IN ('C','P') AND $sql_tbl[provider_commissions].paid != 'Y'");

            if ($sum >= $config['General']['providers_commission_min']) {
                $ready[$userid] = $config['General']['providers_commission_min'];
            }
        }

        if (count($ready) > 0) {
            foreach($report as $k => $v) {
                if ($v['paid'] == 'A' && isset($ready[$v['userid']])) {
                    $report[$k]['ready'] = true;
                }
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

        $smarty->assign ('orders',     $report);

    } else {
        $no_results_warning = array(
            'type' => 'W',
            'content' => func_get_langvar_by_name(
                'lbl_warning_no_search_results',
                false,
                false, true
            )
        );
        $smarty->assign('top_message', $no_results_warning);
    }

    $smarty->assign ('orders_cnt', empty($report) ? 0 : count($report));

}

$smarty->assign('mode', $mode);
$smarty->assign('search', $search_data['commissions']);
$smarty->assign('main', 'commissions');

if (defined('IS_ADMIN_USER')) {
    $providers = func_query("SELECT id, login, title, firstname, lastname FROM $sql_tbl[customers] WHERE usertype='P' ORDER BY login, lastname, firstname");
    if (!empty($providers)) {
        $smarty->assign('providers', $providers);
    }
}

$location[] = array(
    func_get_langvar_by_name('lbl_provider_commissions'),
    'commissions.php'
);

$smarty->assign('location', $location);

?>
