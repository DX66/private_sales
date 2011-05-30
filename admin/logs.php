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
 * Shop logs
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: logs.php,v 1.33.2.1 2011/01/10 13:11:45 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './auth.php';
require $xcart_dir.'/include/security.php';
include $xcart_dir.'/include/safe_mode.php';

$location[] = array(func_get_langvar_by_name('lbl_shop_logs'), 'logs.php');

function logs_convert_date($posted_data)
{
    $start_date = false;
    $end_date = XC_TIME;

    switch ($posted_data['date_period']) {
        case 'D': // Today
            $start_date = XC_TIME;
            break;

        case 'W': // This week
            $first_weekday = $end_date - (date('w',$end_date) * 86400);
            $start_date = func_prepare_search_date($first_weekday);
            break;

        case 'M': // This month
            $start_date = mktime(0,0,0,date('n',$end_date),1,date('Y',$end_date));
            break;

        case 'C': // Custom range
            $start_date = $posted_data['start_date'];
            $end_date = $posted_data['end_date'];
            break;

        default:
            $start_date = 0;
            $end_date = XC_TIME;
    }

    return array($start_date, $end_date);
}

/**
 * Log names translation
 */
$log_labels = x_log_get_names();

x_session_register('logs_search_data');

if ($REQUEST_METHOD == 'POST' && !empty($posted_data)) {

    $need_advanced_options = false;
    foreach ($posted_data as $k=>$v) {
        if (!is_array($v) && !is_numeric($v))
            $posted_data[$k] = stripslashes($v);

        if (is_array($v)) {
            $tmp = array();
            foreach ($v as $k1 => $v1) {
                $tmp[$v1] = 1;
            }
            $posted_data[$k] = $tmp;
        }
    }

    if (empty($posted_data['logs'])) {
        $posted_data['logs'] = false;
    }

    if ($start_date) {
        $posted_data['start_date'] = func_prepare_search_date($start_date);
        $posted_data['end_date']   = func_prepare_search_date($end_date, true);
    }

    $logs_search_data = $posted_data;

    if ($mode == 'clean') {
        list($start_date, $end_date) = logs_convert_date($posted_data);
        $labels = array();
        if (!empty($posted_data['logs']) && is_array($posted_data['logs']))
            $labels = array_keys($posted_data['logs']);

        $error_files = array();
        $_tmp = x_log_list_files($labels, $start_date, $end_date);
        if (is_array($_tmp)) {
            foreach ($_tmp as $l => $d) {
                foreach ($d as $ts => $file) {
                    $file = $var_dirs['log'].'/'.$file;
                    if (file_exists($file) && filesize($file) == strlen(X_LOG_SIGNATURE)) {
                        @unlink($_tmp[$l][$ts]);

                    } elseif (file_exists($file) && @unlink($file) === true && ini_get('error_log') !== $file) {
                        $error_files[] = $file;
                    }
                }
            }
        }

        if (!is_array($_tmp) || count($_tmp) == 0) {
            $top_message = array(
                'type' => 'E',
                'content' => func_get_langvar_by_name('err_logs_empty')
            );

        } elseif (!empty($error_files)) {
            $top_message = array(
                'type' => 'E',
                'content' => func_get_langvar_by_name('err_files_delete_perms', array('files' => implode('<br />', $error_files)))
            );

        } else {
            $top_message = array(
                'content' => func_get_langvar_by_name('msg_logs_deleted_ok')
            );
        }
        func_header_location('logs.php');
    }

    func_header_location('logs.php?mode=search');
}

if ($mode == 'search' && $logs_search_data) {
    $posted_data = $logs_search_data;

    if (!isset($posted_data['date_period']))
        $posted_data['date_period'] = 'D';

    $posted_data['count'] = (isset($posted_data['count']) && $posted_data['count'] >= 0) ? intval($posted_data['count']) : 0;

    $logs_search_data = $posted_data;

    list($start_date, $end_date) = logs_convert_date($posted_data);

    $logs_data = array();
    $labels = array();

    if (!empty($posted_data['logs']) && is_array($posted_data['logs']))
        $labels = array_keys($posted_data['logs']);

    if (!empty($labels))
        x_log_flag('log_activity', 'ACTIVITY', "Following logging groups were viewed by '$login' user : ".implode(', ', $labels));

    $_tmp = x_log_get_contents($labels, $start_date, $end_date, true, $posted_data['count']);
    if (is_array($_tmp) && !empty($_tmp)) {
        foreach ($_tmp as $label => $_data) {
            $dialog_tools_data['left'][] = array("link" => '#result_'.$label, 'title' => (!empty($log_labels[$label]) ? $log_labels[$label] : $label));
        }
        $logs_data = $_tmp;

    } elseif (empty($top_message['content'])) {
        $smarty->assign(
            'top_message',
            array(
                'type' => 'W',
                'content' => func_get_langvar_by_name("lbl_warning_no_search_results", false, false, true)
            )
        );
    }

    $smarty->assign('show_results', 1);

} else {
    $posted_data = array(
        'date_period' => 'D',
        'count' => 5
    );

    foreach ($log_labels as $k => $v) {
        $posted_data['logs'][$k] = 1;
    }
}

$dialog_tools_data['right'][] = array("link" => 'configuration.php?option=Logging', 'title' => func_get_langvar_by_name('option_title_Logging'));
$dialog_tools_data['right'][] = array('link' => $xcart_web_dir.DIR_ADMIN.'/general.php', 'title' => func_get_langvar_by_name('lbl_summary'));
$dialog_tools_data['right'][] = array('link' => $xcart_web_dir.DIR_ADMIN.'/tools.php', 'title' => func_get_langvar_by_name('lbl_tools'));
$dialog_tools_data['right'][] = array('link' => $xcart_web_dir.DIR_ADMIN.'/snapshots.php', 'title' => func_get_langvar_by_name('lbl_snapshots'));
$dialog_tools_data['right'][] = array('link' => $xcart_web_dir.DIR_ADMIN.'/user_access_control.php', 'title' => func_get_langvar_by_name('lbl_user_access_control'));

// Assign the section navigation data
$smarty->assign('dialog_tools_data', $dialog_tools_data);

$smarty->assign('log_labels', $log_labels);
$smarty->assign('search_prefilled', $posted_data);
$smarty->assign('logs', $logs_data);
$smarty->assign('location', $location);
$smarty->assign('main', 'logs');

if (
    file_exists($xcart_dir.'/modules/gold_display.php')
    && is_readable($xcart_dir.'/modules/gold_display.php')
) {
    include $xcart_dir.'/modules/gold_display.php';
}
func_display('admin/home.tpl', $smarty);

?>
