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
 * Define data for the navigation within section
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: users_tools.php,v 1.27.2.1 2011/01/10 13:11:47 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

$dialog_tools_data = array();

$dialog_tools_data['left'][] = array('link' => 'users.php', 'title' => func_get_langvar_by_name('lbl_search_users'));

if (empty($active_modules['Simple_Mode'])) {

    $dialog_tools_data['left'][] = array('link' => "user_add.php?usertype=A", 'title' => func_get_langvar_by_name('lbl_create_admin_profile'));

    $dialog_tools_data['left'][] = array('link' => "user_add.php?usertype=P", 'title' => func_get_langvar_by_name('lbl_create_provider_profile'));

} else {

    $dialog_tools_data['left'][] = array('link' => "user_add.php?usertype=P", 'title' => func_get_langvar_by_name('lbl_create_admin_profile'));

}

$dialog_tools_data['left'][] = array('link' => "user_add.php?usertype=C", 'title' => func_get_langvar_by_name('lbl_create_customer_profile'));

if (!empty($active_modules['XAffiliate']))
    $dialog_tools_data['left'][] = array('link' => "user_add.php?usertype=B", 'title' => func_get_langvar_by_name('lbl_create_partner_profile'));

$count_orders = ($usertype == 'C') ? func_query_first_cell("SELECT COUNT(orderid) FROM $sql_tbl[orders] WHERE userid='$user'") : 0;

$dialog_tools_data['right'][] = array('link' => 'orders.php'.($usertype == 'C' ? "?userid=".$user : ''), 'title' => func_get_langvar_by_name(($usertype == 'C' ? 'lbl_customer_orders' : 'lbl_orders') ,array('COUNT_ORDERS' => $count_orders)));

$dialog_tools_data['right'][] = array('link' => 'memberships.php', 'title' => func_get_langvar_by_name('lbl_membership_levels'));

$dialog_tools_data['right'][] = array('link' => "configuration.php?option=User_Profiles", 'title' => func_get_langvar_by_name('option_title_User_Profiles'));

$is_admin_usertype = ($usertype == 'A' || $usertype == 'P' && !empty($active_modules['Simple_Mode']));

if (!$is_admin_usertype && !empty($user)) {
    $dialog_tools_data['right'][] = array('link' => func_get_area_catalog($usertype) . "/home.php?operate_as_user=".$user, 'title' => func_get_langvar_by_name('lbl_operate_as_user'));
}

?>
