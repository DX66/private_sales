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
 * Base authentication, defining common variables 
 * and including common scripts
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: auth.php,v 1.85.2.4 2011/05/04 12:41:32 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

define('AREA_TYPE', 'A');

if (
    file_exists('../top.inc.php')
    && is_readable('../top.inc.php')
) {
    include_once '../top.inc.php';
}

if (!defined('DIR_CUSTOMER')) die("ERROR: Can not initiate application! Please check configuration.");

require_once $xcart_dir . '/init.php';

x_load('backoffice');

x_session_register('login');
x_session_register('login_type');
x_session_register('logged');

x_session_register('export_ranges');

$smarty->assign('js_enabled', 'Y');

x_session_register('top_message');

if (!empty($top_message)) {

    $smarty->assign('top_message', $top_message);

    if($config['Adaptives']['is_first_start'] != 'Y') {

        $top_message = '';

    }

    x_session_save('top_message');
}

x_session_register('login_antibot_on', '');

$smarty->assign('login_antibot_on', $login_antibot_on);

$current_area = 'A';

require_once $xcart_dir . '/include/admin_security.php';
include $xcart_dir . '/https.php';

$location = array();
$location[] = array(func_get_langvar_by_name('lbl_main_page'), 'home.php');

if (
    file_exists($xcart_dir . '/modules/gold_auth.php')
    && is_readable($xcart_dir . '/modules/gold_auth.php')
) {
    include $xcart_dir . '/modules/gold_auth.php';
}

include $xcart_dir . '/include/check_useraccount.php';

include $xcart_dir . '/include/get_language.php';

$login_type = !empty($active_modules['Simple_Mode']) ? 'P' : $login_type;

x_session_register('require_change_password');

if (
    !empty($login)
    && !strstr($PHP_SELF,'change_password.php')
    && !empty($require_change_password[$login_type])
) {

    // Require password change before proceed
    $top_message['content'] = func_get_langvar_by_name('txt_chpass_msg');
    $top_message['type']    = 'E';

    func_header_location('change_password.php');
}

x_session_register('show_adv');

$has_user_account = !empty($login) && ($user_account['flag'] != 'FS');

$smarty->assign('need_storefront_link', $has_user_account);

if (
    $has_user_account
    && $REQUEST_METHOD == 'GET'
) {

    if (
        !$shop_evaluation
        && !empty($_GET['do_not_show_adv'])
    ) {
        // Admin clicks on 'Do not show this message next time' link
        db_query("REPLACE INTO $sql_tbl[config] SET name='do_not_show_adv', value='Y'");
        $config['do_not_show_adv'] = 'Y';
    }

    if ($show_adv != 'already_displayed') {

        $show_adv = func_get_adv();

        $smarty->assign('show_adv', $show_adv);

        $show_adv = 'already_displayed';

    }

    if ($shop_evaluation == 'WRONG_DOMAIN') {

        $smarty->assign('license_url', func_query_first_cell("SELECT value FROM $sql_tbl[config] WHERE name='license_url'"));

        $wrong_domain = $http_location;

        if ($xcart_http_host != $xcart_https_host) {

            $wrong_domain .= ' (' . $https_location . ')';

        }

        $smarty->assign('wrong_domain', $wrong_domain);
    }

    func_storefront_update();
}

x_session_save();

#func_set_resellers();

// Create the user types list for search form

$usertypes = func_get_usertypes();

$smarty->assign('redirect', 'admin');
?>
