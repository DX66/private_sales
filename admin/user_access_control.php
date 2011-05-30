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
 * User access control
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: user_access_control.php,v 1.15.2.1 2011/01/10 13:11:47 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './auth.php';
require $xcart_dir.'/include/security.php';

$is_enabled = defined('SECURITY_BLOCK_UNKNOWN_ADMIN_IP') && constant('SECURITY_BLOCK_UNKNOWN_ADMIN_IP');

if ($is_enabled) {
    if (!isset($config['allowed_ips']) || empty($config['allowed_ips']))
        $config['allowed_ips'] = array();
    elseif (!is_array($config['allowed_ips']))
        $config['allowed_ips'] = func_array_map("trim", explode(",", $config['allowed_ips']));

    if (!isset($config['ip_register_codes']) || empty($config['ip_register_codes']))
        $config['ip_register_codes'] = array();
    elseif (!is_array($config['ip_register_codes']))
        $config['ip_register_codes'] = unserialize($config['ip_register_codes']);

    if (!is_array($config['ip_register_codes']))
        $config['ip_register_codes'] = array();

    if ($REQUEST_METHOD == 'POST' && $mode == 'add') {

        // Add new allowed IP
        if (empty($ip) || !func_is_valid_ip($ip, true)) {
            $top_message = array(
                'type' => 'E',
                'content' => func_get_langvar_by_name('lbl_ip_added_not_successfull')
            );

        } else {
            func_register_admin_ip($ip);
            $top_message = array(
                'content' => func_get_langvar_by_name('lbl_ip_added_successfull')
            );
        }

    } elseif ($REQUEST_METHOD == 'POST' && $mode == 'delete' && !empty($ips) && is_array($ips)) {

        // Delete allowed IP
        foreach ($ips as $ip) {
            $key = array_search($ip, $config['allowed_ips']);
            if ($key !== false)
                func_unset($config['allowed_ips'], $key);
        }
        func_array2insert('config', array('name' => 'allowed_ips', 'value' => addslashes(implode(",", $config['allowed_ips']))), true);

        $top_message = array(
            'content' => func_get_langvar_by_name('lbl_ip_deleted_successfull')
        );

    } elseif ($REQUEST_METHOD == 'POST' && $mode == 'delete_reg' && !empty($ids) && is_array($ids)) {

        // Delete suspended IP
        if (func_remove_ip_request($ids)) {
            $top_message = array(
                'content' => func_get_langvar_by_name('lbl_ip_request_deleted')
            );
        }

    } elseif ($REQUEST_METHOD == 'POST' && $mode == 'register') {

        // Register suspended IP
        $cnt = 0;
        if (!empty($ids) && is_array($ids)) {
            foreach ($ids as $id) {
                if (!empty($config['ip_register_codes']) && isset($config['ip_register_codes'][$id]) && func_register_admin_ip($config['ip_register_codes'][$id]['ip'])) {
                    $cnt++;
                    func_unset($config['ip_register_codes'], $id);
                }
            }
        }

        if ($cnt > 0) {
            func_array2insert('config', array('name' => 'ip_register_codes', 'value' => addslashes(serialize($config['ip_register_codes']))), true);

            $top_message = array(
                'content' => func_get_langvar_by_name('lbl_access_for_ip_granted')
            );

        } else {
            $top_message = array(
                'type' => 'E',
                'content' => func_get_langvar_by_name('lbl_ip_request_not_registered')
            );
        }
    }

    if ($REQUEST_METHOD == 'POST')
        func_header_location('user_access_control.php');

    $smarty->assign('allowed_ips', $config['allowed_ips']);
    if (!empty($config['ip_register_codes']))
        $smarty->assign('suspended_ips', $config['ip_register_codes']);

    $smarty->assign('current_ip', $REMOTE_ADDR);
}

$smarty->assign('func_is_enabled', $is_enabled);

$smarty->assign('main', 'user_access_control');

$location[] = array(func_get_langvar_by_name('lbl_user_access_control'), '');

// Assign the current location line
$smarty->assign('location', $location);

$dialog_tools_data['right'][] = array('link' => $xcart_web_dir.DIR_ADMIN.'/general.php', 'title' => func_get_langvar_by_name('lbl_summary'));
$dialog_tools_data['right'][] = array('link' => $xcart_web_dir.DIR_ADMIN.'/tools.php', 'title' => func_get_langvar_by_name('lbl_tools'));
$dialog_tools_data['right'][] = array('link' => $xcart_web_dir.DIR_ADMIN.'/snapshots.php', 'title' => func_get_langvar_by_name('lbl_snapshots'));
$dialog_tools_data['right'][] = array('link' => $xcart_web_dir.DIR_ADMIN.'/logs.php', 'title' => func_get_langvar_by_name('lbl_shop_logs'));

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
