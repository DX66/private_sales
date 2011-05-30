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
 * States management
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: states.php,v 1.56.2.1 2011/01/10 13:11:47 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './auth.php';
require $xcart_dir.'/include/security.php';

$location[] = array(func_get_langvar_by_name('lbl_states_management'), '');

/**
 * States per page
 */
$objects_per_page = 25;

if (!empty($page)) {
    $page = preg_replace("/[^\d]/", '', $page);
}

/**
 * Get the list of countries for defined states
 */
$states_for_countries = func_query("SELECT $sql_tbl[states].country_code, IFNULL(lng1.value, lng2.value) as country FROM $sql_tbl[states], $sql_tbl[countries] LEFT JOIN $sql_tbl[languages] as lng1 ON lng1.name = CONCAT('country_', $sql_tbl[countries].code) AND lng1.code = '$shop_language' LEFT JOIN $sql_tbl[languages] as lng2 ON lng2.name = CONCAT('country_', $sql_tbl[countries].code) AND lng2.code = '$config[default_admin_language]' WHERE $sql_tbl[states].country_code=$sql_tbl[countries].code AND $sql_tbl[countries].active='Y' GROUP BY $sql_tbl[states].country_code ORDER BY country");

$found = false;

if (is_array($states_for_countries)) {

    if (isset($country)) {

        foreach ($states_for_countries as $k => $v) {

            if ($country == $v['country_code']) {

                $found = true;

                break;

            }

        }

    }

    if (!$found)
        $country = $states_for_countries[0]['country_code'];
}

if ($REQUEST_METHOD == 'POST') {

    x_load('backoffice', 'debug');

    $update_countries = array();

    if ($mode == 'delete') {

        // Delete the selected states

        if (is_array($selected)) {

            foreach ($selected as $stateid=>$v) {

                $state_data = func_query_first("SELECT code, country_code FROM $sql_tbl[states] WHERE stateid='$stateid'");

                if (!empty($state_data)) {

                    $state_code = $state_data['code'];
                    $country_code = $state_data['country_code'];

                    if (!in_array($country_code, $update_countries))
                        $update_countries[] = $country_code;

                    db_query("DELETE FROM $sql_tbl[states] WHERE stateid='$stateid'");
                    db_query("DELETE FROM $sql_tbl[zone_element] WHERE field_type = 'S' AND field = '".$country_code."_".$state_code."' ");
                    db_query("DELETE FROM $sql_tbl[counties] WHERE stateid='$stateid'");

                    if (!empty($active_modules['Survey'])) {
                        db_query("DELETE FROM $sql_tbl[survey_events] WHERE param = 'S' AND id = '$stateid'");
                    }

                }

            }

            $top_message['content'] = func_get_langvar_by_name('msg_adm_states_del');
        }
        else {
            $top_message['content'] = func_get_langvar_by_name('msg_adm_warn_states_del');
            $top_message['type'] = 'W';
        }
    }

    if ($mode == 'update') {

        // Update states

        if (is_array($posted_data)) {
            foreach ($posted_data as $k => $v) {
                $is_code_exists = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[states] WHERE code = '$v[code]' AND country_code = '$country'") > 0;
                if ($is_code_exists)
                    func_unset($v, 'code');
                func_array2update('states', $v, "stateid = '$k'");
            }

            $top_message['content'] = func_get_langvar_by_name('msg_adm_states_upd');
        }
    }

    if ($mode == 'add') {

        // Add new state

        $is_code_exists = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[states] WHERE code = '$new_state_code' AND country_code = '$new_country_code'") > 0;
        if ($new_state_name && $new_state_code && $new_country_code && !$is_code_exists) {
            $query_data = array(
                'state' => $new_state_name,
                'code' => $new_state_code,
                'country_code' => $new_country_code
            );
            func_array2insert('states', $query_data);
            $top_message['content'] = func_get_langvar_by_name('msg_adm_states_add');
            $country = $new_country_code;
            $update_countries = $new_country_code;

        } elseif ($is_code_exists) {
            $top_message = array(
                'content' => func_get_langvar_by_name('msg_adm_warn_states_duplicate'),
                'type' => 'W'
            );

        } else {
            $top_message = array(
                'content' => func_get_langvar_by_name('msg_adm_warn_states_add'),
                'type' => 'W'
            );
        }
    }

    // Update the 'display_states' field of countries
    func_update_country_states($update_countries);

    func_header_location("states.php?country=$country".(!empty($page)?"&page=$page":''));
}

include $xcart_dir.'/include/countries.php';

$search_query = "FROM $sql_tbl[states], $sql_tbl[countries] LEFT JOIN $sql_tbl[languages] as lng1 ON lng1.name = CONCAT('country_', $sql_tbl[countries].code) AND lng1.code = '$shop_language' LEFT JOIN $sql_tbl[languages] as lng2 ON lng2.name = CONCAT('country_', $sql_tbl[countries].code) AND lng2.code = '$config[default_admin_language]' WHERE $sql_tbl[states].country_code=$sql_tbl[countries].code AND $sql_tbl[states].country_code='$country'";

$total_items = func_query_first_cell("SELECT COUNT(*) $search_query");

if ($total_items > 0) {

    // Navigation code

    require $xcart_dir.'/include/navigation.php';

    $smarty->assign('navigation_script',"states.php?country=$country");

    $states = func_query ("SELECT $sql_tbl[states].*, IFNULL(lng1.value, lng2.value) as country $search_query ORDER BY country_code, state LIMIT $first_page, $objects_per_page");

    if ($config['General']['use_counties'] == 'Y') {
        foreach ($states as $k=>$v)
            $states[$k]['counties'] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[counties] WHERE stateid='$v[stateid]'");
    }

    $smarty->assign ('states', $states);
}

if (count($states_for_countries) > 1)
    $smarty->assign('states_for_countries', $states_for_countries);

$smarty->assign('country', $country);

$smarty->assign('main','states_edit');

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
