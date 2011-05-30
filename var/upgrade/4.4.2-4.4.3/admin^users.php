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
 * Users management interface
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: users.php,v 1.79.2.1 2011/01/10 13:11:47 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './auth.php';
require $xcart_dir.'/include/security.php';

x_load('export');

x_session_register('search_data');

$location[] = array(func_get_langvar_by_name('lbl_users_management'), '');

include './users_tools.php';

$advanced_options = array("usertype", "membershipid", 'registration_type', 'address_type', 'phone', 'url', 'registration_date', 'last_login_date', 'suspended_by_admin', 'auto_suspended', 'orders_min', 'orders_max');

x_session_unregister('users_search_condition');

if ($REQUEST_METHOD == 'POST') {
/**
 * Update the session $search_data variable from $posted_data
 */
    if (!empty($posted_data)) {

        $need_advanced_options = false;
        foreach ($posted_data as $k=>$v) {
            if (!is_array($v) && !is_numeric($v))
                $posted_data[$k] = stripslashes($v);
            if (in_array($k, $advanced_options) && !empty($v))
                $need_advanced_options = true;
        }

        $posted_data['need_advanced_options'] = $need_advanced_options;

        if (empty($search_data['users']['sort_field'])) {
            $posted_data['sort_field'] = 'last_login';
            $posted_data['sort_direction'] = 1;
        }
        else {
            $posted_data['sort_field'] = $search_data['users']['sort_field'];
            $posted_data['sort_direction'] = $search_data['users']['sort_direction'];
        }

        if ($start_date) {
            $posted_data['start_date'] = func_prepare_search_date($start_date);
            $posted_data['end_date']   = func_prepare_search_date($end_date, true);
        }

        if (!empty($posted_data['membershipid'])) {
            list($posted_data['usertype'], $posted_data['membershipid']) = explode("-", $posted_data['membershipid']);
        }

        $search_data['users'] = $posted_data;

    }
    func_header_location("users.php?mode=search");
}

if ($mode == 'search') {
/**
 * Perform search and display results
 */

    $data = array();

/**
 * Prepare the search data
 */
    if (!empty($sort) && in_array($sort, array('username','name','email','usertype','last_login', 'cnt'))) {
        $search_data['users']['sort_field'] = $sort;
        $search_data['users']['sort_direction'] = abs(intval($search_data['users']['sort_direction']) - 1);
        $flag_save = true;
    }

    if (!empty($page) && $search_data['users']['page'] != intval($page)) {
        // Store the current page number in the session
        $search_data['users']['page'] = $page;
        $flag_save = true;
    }

    if (isset($flag_save))
        x_session_save('search_data');

    if (is_array($search_data['users'])) {
        $data = $search_data['users'];
        foreach ($data as $k=>$v)
            if (!is_array($v) && !is_numeric($v))
                $data[$k] = addslashes($v);
    }

    if (
        isset($data['is_export'])
        || (
            isset($export)
            && $export == 'export_found'
        )
    ) {
        $data['_get_sql_query'] = true;
    }

    $data['_objects_per_page'] = $config["Appearance"]["users_per_page_admin"];

    include $xcart_dir.'/include/search_users.php';

    if (
        (
            isset($data['is_export'])
            || (
                isset($export)
                && $export == 'export_found'
            )
        ) && !empty($sql_query)
    ) {
        // Export all found users
        func_export_range_save('USERS', $sql_query);

        $top_message['content'] = func_get_langvar_by_name("lbl_export_users_add");
        $top_message['type'] = 'I';

        func_header_location("import.php?mode=export");
    }

    x_session_register('users_search_condition', $search_condition);
    x_session_save('users_search_condition');

    if (!empty($users)) {

        // Assign the Smarty variables
        if ($active_modules['XAffiliate']) {
            foreach ($users as $u) {
                if ($u['usertype'] == 'B') {
                    $smarty->assign('users_has_partner', true);
                    $plans = func_query("SELECT * FROM $sql_tbl[partner_plans] WHERE status = 'A' ORDER BY plan_title");
                    if ($plans)
                        $smarty->assign('plans', $plans);

                    break;
                }
            }
        }

        $smarty->assign('navigation_script', "users.php?mode=search");
        $smarty->assign('users', $users);
        $smarty->assign('first_item', $first_page+1);
        $smarty->assign('last_item', min($first_page+$objects_per_page, $total_items));

    } elseif (empty($top_message['content'])) {
        $no_results_warning = array('type' => 'W', 'content' => func_get_langvar_by_name("lbl_warning_no_search_results", false, false, true));
        $smarty->assign('top_message', $no_results_warning);
    }

    $smarty->assign('total_items', $total_items);
    $smarty->assign('mode', $mode);

} // /if ($mode == 'search')

if (empty($users)) {
/**
 * Get the states and countries list for search form
 */
    include $xcart_dir.'/include/states.php';
    include $xcart_dir.'/include/countries.php';
}

$smarty->assign('usertypes',$usertypes);

$smarty->assign('search_prefilled', @$search_data['users']);

$memberships = array('A' => array(), 'P' => array(), 'C' => array());
if (!empty($active_modules['XAffiliate'])) {
    $memberships['B'] = array();
}
$tmp = func_query("SELECT $sql_tbl[memberships].area, $sql_tbl[memberships].membershipid, IFNULL($sql_tbl[memberships_lng].membership, $sql_tbl[memberships].membership) as membership FROM $sql_tbl[memberships] LEFT JOIN $sql_tbl[memberships_lng] ON $sql_tbl[memberships].membershipid = $sql_tbl[memberships_lng].membershipid AND $sql_tbl[memberships_lng].code = '$shop_language' WHERE $sql_tbl[memberships].active = 'Y' ORDER BY IF(FIELD($sql_tbl[memberships].area, 'A','P','C','B') > 0, FIELD($sql_tbl[memberships].area, 'A','P','C','B'), 100), $sql_tbl[memberships].orderby");
if (!empty($tmp)) {
    foreach ($tmp as $v) {
        $memberships[$v['area']][] = $v;
    }
}
if (!empty($active_modules['Simple_Mode'])) {
    unset($memberships['A']);
}
$smarty->assign('memberships', $memberships);

$memberships_lbls = array();
foreach ($memberships as $k => $v) {
    $type = ($k == 'P' && !empty($active_modules['Simple_Mode'])) ? "A" : $k;
    $memberships_lbls[$k] = func_get_langvar_by_name('lbl_'.$type.'_usertype');
}
$smarty->assign('memberships_lbls', $memberships_lbls);

/**
 * Assign Smarty variables and show template
 */
$smarty->assign('main','users');

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
