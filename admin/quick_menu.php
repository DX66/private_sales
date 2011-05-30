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
 * Define quick menu items
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: quick_menu.php,v 1.21.2.1 2011/01/10 13:11:47 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

/**
 * GENERATE ITEMS FOR QUICK MENU
 */

/**
 * Users/Orders Management group
 */
$group_name = func_get_langvar_by_name('lbl_users_orders_management');

$quick_menu[$group_name][] = array ('link' => $xcart_catalogs['admin'].'/users.php', 'title' => func_get_langvar_by_name('lbl_search_users'));

if (empty($active_modules['Simple_Mode'])) {
    $quick_menu[$group_name][] = array('link' => $xcart_catalogs['admin']."/user_add.php?usertype=A", 'title' => func_get_langvar_by_name('lbl_create_admin_profile'));

    $quick_menu[$group_name][] = array('link' => $xcart_catalogs['admin']."/user_add.php?usertype=P", 'title' => func_get_langvar_by_name('lbl_create_provider_profile'));
} else {
    $quick_menu[$group_name][] = array('link' => $xcart_catalogs['admin']."/user_add.php?usertype=P", 'title' => func_get_langvar_by_name('lbl_create_admin_profile'));
}

$quick_menu[$group_name][] = array ('link' => $xcart_catalogs['admin']."/user_add.php?usertype=C", 'title' => func_get_langvar_by_name('lbl_create_customer_profile'));

if (!empty($active_modules['XAffiliate']))
    $quick_menu[$group_name][] = array ('link' => $xcart_catalogs['admin']."/user_add.php?usertype=B", 'title' => func_get_langvar_by_name('lbl_create_partner_profile'));

$quick_menu[$group_name][] = '';

$quick_menu[$group_name][] = array ('link' => $xcart_catalogs['admin'].'/orders.php', 'title' => func_get_langvar_by_name('lbl_orders_management'));

$quick_menu[$group_name][] = array ('link' => $xcart_catalogs['admin']."/orders.php?date=D", 'title' => func_get_langvar_by_name('lbl_search_today_orders'));

$quick_menu[$group_name][] = array ('link' => $xcart_catalogs['admin']."/orders.php?date=W", 'title' => func_get_langvar_by_name('lbl_search_this_week_orders'));

$quick_menu[$group_name][] = array ('link' => $xcart_catalogs['admin']."/orders.php?date=M", 'title' => func_get_langvar_by_name('lbl_search_this_month_orders'));

/**
 * Products Management group
 */
$group_name = func_get_langvar_by_name('lbl_products_management');

$quick_menu[$group_name][] = array ('link' => $xcart_catalogs['admin'].'/search.php', 'title' => func_get_langvar_by_name('lbl_search_products'));

$quick_menu[$group_name][] = array ('link' => $xcart_catalogs['admin'].'/product_modify.php', 'title' => func_get_langvar_by_name('lbl_add_product'));

if (!empty($active_modules['Customer_Reviews']))
    $quick_menu[$group_name][] = array ('link' => $xcart_catalogs['admin'].'/ratings_edit.php', 'title' => func_get_langvar_by_name('lbl_edit_ratings'));

/**
 * Content Management group
 */
$group_name = func_get_langvar_by_name('lbl_content_management');

$quick_menu[$group_name][] = array ('link' => $xcart_catalogs['admin'].'/categories.php', 'title' => func_get_langvar_by_name('lbl_categories').'/'.func_get_langvar_by_name('lbl_featured_products'));

$quick_menu[$group_name][] = array ('link' => $xcart_catalogs['admin'].'/manufacturers.php', 'title' => func_get_langvar_by_name('lbl_manufacturers'));

if (!empty($active_modules['News_Management']))
    $quick_menu[$group_name][] = array ('link' => $xcart_catalogs['admin'].'/news.php', 'title' => func_get_langvar_by_name('lbl_news_management'));

$quick_menu[$group_name][] = array ('link' => $xcart_catalogs['admin'].'/editor_mode.php', 'title' => func_get_langvar_by_name('lbl_webmaster_mode'));

$quick_menu[$group_name][] = array ('link' => $xcart_catalogs['admin'].'/pages.php', 'title' => func_get_langvar_by_name('lbl_static_pages'));

$quick_menu[$group_name][] = array ('link' => $xcart_catalogs['admin'].'/speed_bar.php', 'title' => func_get_langvar_by_name('lbl_speed_bar'));

$quick_menu[$group_name][] = array ('link' => $xcart_catalogs['admin'].'/html_catalog.php', 'title' => func_get_langvar_by_name('lbl_html_catalog'));

$quick_menu[$group_name][] = array ('link' => $xcart_catalogs['admin'].'/file_manage.php', 'title' => func_get_langvar_by_name('lbl_files_management'));

$quick_menu[$group_name][] = array ('link' => $xcart_catalogs['admin'].'/file_edit.php', 'title' => func_get_langvar_by_name('lbl_templates_management'));

$quick_menu[$group_name][] = array ('link' => $xcart_catalogs['admin'].'/languages.php', 'title' => func_get_langvar_by_name('lbl_languages_management'));

/**
 * Store Configuration group
 */
$group_name = func_get_langvar_by_name('lbl_store_configuration');

$quick_menu[$group_name][] = array ('link' => $xcart_catalogs['admin'].'/modules.php', 'title' => func_get_langvar_by_name('lbl_modules'));

$quick_menu[$group_name][] = array ('link' => $xcart_catalogs['admin'].'/configuration.php', 'title' => func_get_langvar_by_name('lbl_general_settings'));

$quick_menu[$group_name][] = array ('link' => $xcart_catalogs['admin'].'/memberships.php', 'title' => func_get_langvar_by_name('lbl_membership_levels'));

$quick_menu[$group_name][] = array ('link' => 'taxes.php', 'title' => func_get_langvar_by_name('lbl_taxing_system'));

$quick_menu[$group_name][] = array ('link' => 'countries.php', 'title' => func_get_langvar_by_name('lbl_countries'));

$quick_menu[$group_name][] = array ('link' => 'states.php', 'title' => func_get_langvar_by_name('lbl_states'));

/**
 * Payment System Configuration group
 */
$group_name = func_get_langvar_by_name('lbl_payment_system_configuration');

$quick_menu[$group_name][] = array ('link' => $xcart_catalogs['admin'].'/payment_methods.php', 'title' => func_get_langvar_by_name('lbl_payment_methods'));

$quick_menu[$group_name][] = array ('link' => $xcart_catalogs['admin'].'/card_types.php', 'title' => func_get_langvar_by_name('lbl_credit_card_types'));

$smarty->assign('quick_menu', $quick_menu);

?>
