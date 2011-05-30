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
 * Stop list module - blocked IP-addresses management
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: stop_list.php,v 1.25.2.1 2011/01/10 13:11:47 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './auth.php';
require $xcart_dir.'/include/security.php';

if(empty($active_modules['Stop_List']))
    func_403(28);

$location[] = array(func_get_langvar_by_name('lbl_stop_list'), '');

$dialog_tools_data['left'][] = array('link' => 'stop_list.php', 'title' => func_get_langvar_by_name('lbl_stop_list'));
$dialog_tools_data['left'][] = array('link' => "stop_list.php?mode=add", 'title' => func_get_langvar_by_name('lbl_add_ip_address'));

/**
 * Add/Modify IP
 */
if ($mode == 'add' && $octet && @count($octet) == 4) {
    $flag_int = true;
    foreach ($octet as $k => $v) {
        if ($v != '*')
            $octet[$k] = (int)$v;
        if ($octet[$k] > 255 || $octet[$k] < 0)
            $flag_int = false;
    }
    if ($octet[0] == 0 || !$flag_int) {
        $top_message['content'] = func_get_langvar_by_name('txt_stop_list_warning');
        $top_message['type'] = 'E';
        func_header_location("stop_list.php?mode=add");
    }
    $ip = implode('.', $octet);
    if (empty($ipid)) {
        func_add_ip_to_slist($ip, 'M', $ip_type);
    } else {
        foreach ($octet as $k => $v) {
            if ($v == "*")
                $octet[$k] = -1;
        }
        $data_query = array(
            'octet1' => $octet[0],
            'octet2' => $octet[1],
            'octet3' => $octet[2],
            'octet4' => $octet[3],
            'ip' => $ip,
            'ip_type' => $ip_type
        );
        func_array2update('stop_list', $data_query, "ipid = '$ipid'");
    }

/**
 * Delete IP
 */
} elseif ($mode == 'delete' && $to_delete && is_array($to_delete)) {
    db_query("DELETE FROM $sql_tbl[stop_list] WHERE ip IN ('".implode("','", array_keys($to_delete))."')");
    $top_message['content'] = func_get_langvar_by_name('msg_adm_ip_address_del');
}

if (!empty($mode) && $REQUEST_METHOD == 'POST') {
    func_header_location('stop_list.php');
}

// Load sort method (DESC/ASC and 0/1)
x_session_register('sort_type');
x_session_register('sort_by');
x_session_register('current_page');

if (empty($sort_type)) {
    $sort_type = 0;
}

if (empty($sort)) {
    if (!empty($sort_by)) {
        $sort = $sort_by;
    } else {
        $sort = 'ip';
    }
} else {
    $sort_by = $sort;
    $sort_type = !$sort_type;
}

if (!$sort_type) {
    $sort_type_value = 'DESC';
} else {
    $sort_type_value = 'ASC';
}
if (empty($page)) {
    $page = $current_page;
}
// Get number of total stop list items
$total_items = func_query_first_cell("SELECT COUNT(*) as count FROM $sql_tbl[stop_list]");
/**
 * Prepare the page navigation
 */
$smarty->assign('navigation_script', "stop_list.php?");
$objects_per_page = $config['Appearance']['orders_per_page_admin'];

include $xcart_dir.'/include/navigation.php';

$current_page = $page;

// Get stop list for current page
if ($total_items > 0)
    $stop_list = func_query("SELECT * FROM $sql_tbl[stop_list] ORDER BY $sort $sort_type_value LIMIT ".(($page-1)*$objects_per_page).", $objects_per_page");

// Save sorting info to smarty
$sort_info = array (
    'field' => $sort,
    'type' => $sort_type
);
$smarty->assign('sort_info', $sort_info);

if (!empty($stop_list)) {
    foreach ($stop_list as $k => $v) {
        if ($v['reason'] == 'M') {
            $stop_list[$k]['reason_text'] = func_get_langvar_by_name("lbl_added_by_admin");

        } elseif (in_array($v['reason'], array('T','P','S','F','A'))) {
            $stop_list[$k]['reason_text'] = func_get_langvar_by_name("lbl_slist_reason_".strtolower($v['reason']));

        } else {
            $stop_list[$k]['reason_text'] = func_get_langvar_by_name("lbl_unknown");
        }
    }

    $smarty->assign('stop_list', $stop_list);
}

if ($mode == 'add' && !empty($ipid)) {
    $ip = func_query_first("SELECT * FROM $sql_tbl[stop_list] WHERE ipid = '$ipid'");
    if (!empty($ip)) {
        $location[count($location)-1][1] = 'stop_list.php';
        $location[] = array($ip['ip'], "");
    }

} else {
    $ip = array('octet1' => 0,'octet2' => 0,'octet3' => 0,'octet4' => 0);
}

$smarty->assign('main', 'stop_list');
$smarty->assign('mode', $mode);
$smarty->assign('ip', $ip);

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
