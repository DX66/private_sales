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
 * Merchant password change interface
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: change_mpassword.php,v 1.34.2.1 2011/01/10 13:11:45 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/**
 * Change Merchant password
 */

require './auth.php';
require $xcart_dir.'/include/security.php';

x_load('order','crypt');

if (!$merchant_password && !empty($config['mpassword'])) {
    func_403(14);
}

x_session_register('change_mpassword_referer');

if ((empty($change_mpassword_referer) || empty($err)) && $REQUEST_METHOD == 'GET' && func_is_internal_url($HTTP_REFERER)) {
    $change_mpassword_referer = $HTTP_REFERER;
}

if ($REQUEST_METHOD == 'POST') {
    $top_message['type'] = 'E';
    $top_message['content'] = false;

    // Check old merchant password

    if ($merchant_password != $old_password && !empty($config['mpassword'])) {
        $top_message['content'] = func_get_langvar_by_name('txt_wrong_old_mpassword');
    }

    // Check differences (new and old passwords)

    if ($new_password != $confirm_password) {
        $top_message['content'] = func_get_langvar_by_name('txt_different_mpasswords');
    }

    // Check password length

    if (strlen($new_password) < 6) {
        $top_message['content'] = func_get_langvar_by_name('txt_small_mpassword');
    }

    if ($top_message['content'] !== false) {
        func_header_location("change_mpassword.php?err=Y".($from_config ? "&from_config=".$from_config : ''));
    }

    // Update merchant password

    $old_password = $merchant_password;
    func_array2insert('config', array('name' => 'mpassword', 'value' => text_crypt('Merchant password test phrase','C',$new_password)), true);
    $merchant_password = $new_password;
    $top_message['type'] = 'I';
    if (empty($config['mpassword'])) {
        $top_message['content'] = func_get_langvar_by_name('txt_added_mpassword')."<br /><br /><font color=\"red\">".func_get_langvar_by_name('lbl_add_mpassword_warn')."</font>";
        db_query("UPDATE $sql_tbl[config] SET value = 'Y' WHERE name = 'blowfish_enabled'");
        $config['Security']['blowfish_enabled'] = 'Y';
        func_data_recrypt();
    }
    else {
        $top_message['content'] = func_get_langvar_by_name('txt_changed_mpassword');
        func_change_mpassword_recrypt($old_password);
    }

    if ($from_config)
        func_html_location("configuration.php?option=".$from_config, 0);

    if (!empty($change_mpassword_referer)) {
        $url = $change_mpassword_referer;
        $change_mpassword_referer = '';
        x_session_unregister('change_mpassword_referer');
        func_html_location($url, 0);
    }
    func_html_location('change_mpassword.php', 0);
}

$title = func_get_langvar_by_name((empty($config['mpassword']) ? "lbl_add_mpassword" : "lbl_change_mpassword"));
$location[] = array($title, '');
$smarty->assign('section_title',$title);

if (!empty($from_config)) {
    $smarty->assign('from_config', $from_config);
}

$smarty->assign('main','change_mpassword');

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
