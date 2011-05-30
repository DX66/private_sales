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
 * Users adding interface
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: user_add.php,v 1.60.2.1 2011/01/10 13:11:47 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './auth.php';
require $xcart_dir.'/include/security.php';

if (
    !empty($active_modules['Simple_Mode'])
    && $usertype == 'A'
    && $current_area == 'A'
) {
    func_header_location("user_add.php?usertype=P");
}

define('USER_ADD', 1);

$display_antibot = false;

$location[] = array(func_get_langvar_by_name('lbl_users_management'), 'users.php');

$_usertype = (($usertype == 'P' and !empty($active_modules['Simple_Mode'])) ? 'A' : $usertype);

switch ($_usertype) {
    case 'A':
        $location[] = array(func_get_langvar_by_name('lbl_create_admin_profile'), '');
        break;
    case 'P':
        $location[] = array(func_get_langvar_by_name('lbl_create_provider_profile'), '');
        break;
    case 'C':
        $location[] = array(func_get_langvar_by_name('lbl_create_customer_profile'), '');
        break;
    case 'B':
        $location[] = array(func_get_langvar_by_name('lbl_create_partner_profile'), '');
}

include './users_tools.php';

$smarty->assign('usertype_name', $usertypes[$usertype]);

$mode = 'add';

$login_         = $login;
$login_type_     = $login_type;
$logged_userid_ = $logged_userid;

$login             = $_GET['user'];
$login_type     = $_GET['usertype'];
$logged_userid     = '';

/**
 * Where to forward <form action
 */

$smarty->assign(
    'register_script_name',
    (($config['Security']['use_https_login'] == 'Y') ? $xcart_catalogs_secure['admin'] . "/" : "")
    . 'user_add.php'
);

require $xcart_dir . '/include/register.php';

/**
 * Update profile or create new
 */
switch ($usertype) {
    case 'P':
        $tpldir = 'provider';
        break;

    case 'B':
        $tpldir = 'partner';
        break;

    default:
        $tpldir = 'admin';
}

if (
    !empty($active_modules['Simple_Mode'])
    && (
        $usertype == 'A'
        || $usertype == 'P'
    )
) {
    $tpldir = 'admin';
}

// Display the 'Activity' input box for admin, provider or partner
if (in_array($usertype, array('A', 'P', 'B'))) {

    $smarty->assign('display_activity_box', 'Y');

}

$smarty->assign('main',        'user_add');
$smarty->assign('tpldir',     $tpldir);

$login             = $login_;
$login_type     = $login_type_;
$logged_userid     = $logged_userid_;

x_session_save();

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
func_display('admin/home.tpl', $smarty);
?>
