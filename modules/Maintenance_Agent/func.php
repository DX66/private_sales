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
 * Functions for the Maintenance agent
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.php,v 1.22.2.3 2011/02/09 12:32:19 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }

function func_get_usertype_generic_stat(&$visits, $usertype, $date_from, $date_to)
{
    global $sql_tbl;

    $unique = func_query_first_cell("SELECT COUNT(DISTINCT userid) AS visits FROM $sql_tbl[login_history] WHERE date_time>='$date_from' AND date_time<='$date_to' AND action='login' AND usertype='$usertype'");
    $all = func_query_first_cell("SELECT COUNT(userid) AS visits FROM $sql_tbl[login_history] WHERE date_time>='$date_from' AND date_time<='$date_to' AND action='login' AND usertype='$usertype'");
    $last = func_query_first_cell("SELECT MAX(date_time) FROM $sql_tbl[login_history] WHERE date_time>='$date_from' AND date_time<='$date_to' AND action='login' AND usertype='$usertype'");
    if (!empty($visits)) {
        $visits[] = array (
            'usertype' => $usertype,
            'visits_unique' => $unique,
            'visits_all' => $all,
            'date_time' => $last
        );
    }
}

function func_send_periodical_email()
{
    global $config;
    global $sql_tbl;
    global $mail_smarty, $active_modules;

    x_load('mail');

    if (empty($config['Company']['site_administrator'])) {
        // no recipient set up
        return;
    }

    $tmp = XC_TIME-SECONDS_PER_DAY;
    $date_to = func_prepare_search_date($tmp, true);

    switch($config['Maintenance_Agent']['periodic_type']) {
        case 'D': // once a day
            $date_from = $date_to - (SECONDS_PER_DAY-1); // set date_from to the begin of day
            break;

        case 'W': // once a week
            $date_from = $date_to - (SECONDS_PER_WEEK-1);
            break;

        case 'M': // once a month
            $date_from = func_prepare_search_date($date_to);
            break;

        default:
            // ERROR: uknown period
            return;
    }

    if ((int)$config['periodic_last_time'] >= $date_from) {
        // already sent
        return;
    }

    db_query("UPDATE $sql_tbl[config] SET value='$date_to' WHERE name='periodic_last_time'");

    if ((int)$config['periodic_last_time'] < $date_from) {
        $date_from = (int)$config['periodic_last_time']+1;
    }

    $period = strftime($config['Appearance']['date_format'], $date_from);
    if ( ($date_to-$date_from) > SECONDS_PER_DAY)
        $period.= ' - '.strftime($config["Appearance"]["date_format"], $date_to);

    $mail_smarty->assign('periodic_subject_period', $period);

    $clear_vars = array();

    if ($config['Maintenance_Agent']['periodic_visits'] == 'Y') {
        // collect informaition about logins
        $visits = func_query("SELECT userid, usertype, COUNT(*) AS visits, max(date_time) as date_time FROM $sql_tbl[login_history] WHERE date_time>='$date_from' AND date_time<='$date_to' AND action='login' AND usertype IN ('A','P') GROUP BY userid, usertype ORDER BY usertype ASC, visits DESC, userid ASC");
        if (!is_array($visits)) $visits = array();

        func_get_usertype_generic_stat($visits, 'C', $date_from, $date_to);

        $user_types = array (
            'A' => func_get_langvar_by_name('lbl_administrator'),
            'P' => func_get_langvar_by_name('lbl_provider'),
            'C' => func_get_langvar_by_name('lbl_customers')
        );

        if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[modules] WHERE module_name='XAffiliate'") > 0) {
            $user_types['B'] = func_get_langvar_by_name('lbl_partners');
            func_get_usertype_generic_stat($visits, 'B', $date_from, $date_to);
        }

        if (!empty($active_modules['Simple_Mode']))
            $user_types['P'] = $user_types['A'];

        if (is_array($visits)) {
            foreach($visits as $k=>$v) {
                $utype = $v['usertype'];
                $visits[$k]['usertype_txt'] = (!empty($user_types[$utype]) ? $user_types[$utype] : $utype);
                $visits[$k]['date_time_formated'] = strftime($config["Appearance"]["datetime_format"], $v['date_time']);
            }
        }

        $mail_smarty->assign('stat_visits', empty($visits)?false:$visits);
        $clear_vars[] = 'stat_visits';
    }

    if ($config['Maintenance_Agent']['periodic_orders'] == 'Y') {
        // collect informaition about orders
        $date_condition = "AND date>='$date_from' AND date<='$date_to'";

        $orders['P'] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[orders] WHERE status='P' $date_condition");
        $orders['F'] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[orders] WHERE (status='F' OR status='D') $date_condition");
        $orders['I'] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[orders] WHERE status='I' $date_condition");
        $orders['Q'] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[orders] WHERE status='Q' $date_condition");

        $mail_smarty->assign('stat_orders', $orders);
        $clear_vars[] = 'stat_orders';
    }

    if (!empty($config['Maintenance_Agent']['periodic_logs'])) {
        // collect informaition about logs
        $log_labels = explode(',', $config['Maintenance_Agent']['periodic_logs']);
        $logs_data = x_log_get_contents($log_labels, $date_from, $date_to);
        $log_names = x_log_get_names($log_labels);

        $mail_smarty->assign('stat_log_names', $log_names);
        $mail_smarty->assign('stat_logs_data', $logs_data);
        $clear_vars[] = 'stat_log_names';
        $clear_vars[] = 'stat_logs_data';
    }

    if (!empty($clear_vars)) {
        func_send_mail($config['Company']['site_administrator'], 'mail/periodic_subj.tpl', 'mail/periodic.tpl', $config["Company"]["site_administrator"], true, true);
        $mail_smarty->clear_assign($clear_vars);
    }
}

?>
