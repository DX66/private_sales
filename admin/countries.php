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
 * Countries management interface
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: countries.php,v 1.46.2.1 2011/01/10 13:11:45 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './auth.php';
require $xcart_dir.'/include/security.php';

$location[] = array(func_get_langvar_by_name('lbl_countries_management'), '');

$zones[] = array('zone' => 'ALL', 'title' => func_get_langvar_by_name('lbl_all_regions'));
$zones[] = array('zone' => 'NA', 'title' => func_get_langvar_by_name('lbl_na'));
$zones[] = array('zone' => 'EU', 'title' => func_get_langvar_by_name('lbl_eu'));
$zones[] = array('zone' => 'AU', 'title' => func_get_langvar_by_name('lbl_au'));
$zones[] = array('zone' => 'LA', 'title' => func_get_langvar_by_name('lbl_la'));
#$zones[] = array('zone' => 'SU', 'title' => func_get_langvar_by_name('lbl_su'));
$zones[] = array('zone' => 'AS', 'title' => func_get_langvar_by_name('lbl_asia'));
$zones[] = array('zone' => 'AF', 'title' => func_get_langvar_by_name('lbl_af'));
$zones[] = array('zone' => 'AN', 'title' => func_get_langvar_by_name('lbl_an'));

$zone_is_valid = false;
if (!empty($zone)) {
    foreach ($zones as $k=>$v) {
        if ($zone == $v['zone']) {
            $zone_is_valid = true;
            break;
        }
    }
    if ($zone == 'ALL')
        $zone = '';
}
else {
    if ($REQUEST_METHOD != 'POST')
        $zone = func_query_first_cell("SELECT region FROM $sql_tbl[countries] WHERE code='".$config["Company"]["location_country"]."'");
    else
        $zone = 'ALL';
}

/**
 * Countries per page
 */
$objects_per_page = 40;

if ($REQUEST_METHOD == 'POST') {

    if (!empty($mode)) {
        if ($mode == 'deactivate_all') {
            db_query("UPDATE $sql_tbl[countries] SET active='N'");
            $top_message['content'] = func_get_langvar_by_name('msg_adm_countries_disabled');
        }
        if ($mode == 'activate_all') {
            db_query("UPDATE $sql_tbl[countries] SET active='Y'");
            $top_message['content'] = func_get_langvar_by_name('msg_adm_countries_enabled');
        }
        func_header_location("countries.php?zone=$zone&page=$page");
    }

    if (is_array($posted_data)) {
        foreach ($posted_data as $k=>$v) {
            $v['active'] = (isset($v['active']) ? 'Y' : 'N' );

            if ($k == $config['General']['default_country']) {
                if ($v['display_states'] != 'Y') {
                    db_query("UPDATE $sql_tbl[config] SET value='' WHERE name='default_state' AND category='General'");
                } else {
                    if (empty( $config['General']['default_state'])) {
                        $def_state = func_query_first_cell("SELECT code FROM $sql_tbl[states] WHERE country_code='$k'");
                        db_query("UPDATE $sql_tbl[config] SET value='$def_state' WHERE name='default_state' AND category='General'");
                    }
                }
            }

            db_query("UPDATE $sql_tbl[countries] SET active='$v[active]', display_states='$v[display_states]' WHERE code='$k'");
            db_query("UPDATE $sql_tbl[languages] SET value = '$v[country]' WHERE name = 'country_$k' AND code = '$shop_language'");
//            db_query("UPDATE $sql_tbl[languages] SET value = '$v[language]' WHERE name = 'language_$k'");
        }
        $top_message['content'] = func_get_langvar_by_name('msg_adm_countries_upd');
        func_data_cache_get('languages', array($shop_language), true);
    }

    func_header_location("countries.php?zone=$zone&page=$page");
}

$condition = '';
if (!empty($zone)) {
    if ($zone == 'SU') {
        $condition = " WHERE $sql_tbl[countries].code IN ('AM','AZ','BY','EE','GE','KZ','KG','LV','LT','MD','RU','TJ','TM','UA','UZ')";
    } else {
        $condition = " WHERE $sql_tbl[countries].region='$zone'";
    }
}

$total_items = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[countries] $condition");

/**
 * Navigation code
 */
require $xcart_dir.'/include/navigation.php';

$smarty->assign('navigation_script',"countries.php?zone=".(empty($zone)?'ALL':$zone));

$countries = func_query ("SELECT $sql_tbl[countries].*, IFNULL(lng1c.value, lng2c.value) as country FROM $sql_tbl[countries] LEFT JOIN $sql_tbl[languages] as lng1c ON lng1c.name = CONCAT('country_', $sql_tbl[countries].code) AND lng1c.code = '$shop_language' LEFT JOIN $sql_tbl[languages] as lng2c ON lng2c.name = CONCAT('country_', $sql_tbl[countries].code) AND lng2c.code = '$config[default_admin_language]' $condition ORDER BY country LIMIT $first_page, $objects_per_page");

$smarty->assign('countries', $countries);

$smarty->assign('zones', $zones);
$smarty->assign('zone', $zone);

$smarty->assign('main','countries_edit');

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
