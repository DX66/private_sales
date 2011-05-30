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
 * Zones management interface
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Provder interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: zones.php,v 1.41.2.2 2011/03/03 13:27:04 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/**
 * Zone_element types:
 *    'C' - country
 *    'S' - state
 *    'G' - county
 *    'T' - city (town) mask
 *    'Z' - zipcode mask
 *    'A' - address mask
 */

require './auth.php';
require $xcart_dir.'/include/security.php';

x_load('backoffice');

/**
 * Sort order for zone elements in the notes
 */
function sort_zone_elements ($a, $b)
{
    static $sort_order;

    $sort_order = array_flip(array('C','S','G','T','Z','A'));

    if ($sort_order[$a['element_type']] > $sort_order[$b['element_type']])
        return 1;
    else
        return 0;
}

$zones[] = array('zone' => 'ALL', 'title' => func_get_langvar_by_name('lbl_all_regions'));
$zones[] = array('zone' => 'NA', 'title' => func_get_langvar_by_name('lbl_na'));
$zones[] = array('zone' => 'EU', 'title' => func_get_langvar_by_name('lbl_eu'));
$zones[] = array('zone' => 'AU', 'title' => func_get_langvar_by_name('lbl_au'));
$zones[] = array('zone' => 'LA', 'title' => func_get_langvar_by_name('lbl_la'));
#$zones[] = array('zone' => 'SU', 'title' => func_get_langvar_by_name('lbl_su'));
$zones[] = array('zone' => 'AS', 'title' => func_get_langvar_by_name('lbl_asia'));
$zones[] = array('zone' => 'AF', 'title' => func_get_langvar_by_name('lbl_af'));
$zones[] = array('zone' => 'AN', 'title' => func_get_langvar_by_name('lbl_an'));

if (!in_array($mode, array('add', 'details', 'delete', 'rename', 'clone')))
    $mode = '';

$provider_condition = ($single_mode ? '' : "AND provider='$logged_userid'");

$zoneid = intval(@$zoneid);

if ($REQUEST_METHOD == 'POST') {

    $redirect_to = '';

    if ($mode == 'details') {

    // Create/Rename zone

        $new_zone = $error = false;

        $zone_name = trim($zone_name);
        if (!empty($zone_name)) {
            if (empty($zoneid)) {
                db_query("INSERT INTO $sql_tbl[zones] (zone_name, provider) VALUES ('$zone_name', '$logged_userid')");
                $zoneid = db_insert_id();
                $new_zone = true;
                $top_message['content'] = func_get_langvar_by_name('msg_zone_add');
                $redirect_to = "?zoneid=$zoneid";
            }
            else {
                db_query("UPDATE $sql_tbl[zones] SET zone_name='$zone_name' WHERE zoneid='$zoneid'");
                $top_message['content'] = func_get_langvar_by_name('msg_zone_renamed');
            }
        }
        else {
            $top_message['content'] = func_get_langvar_by_name('msg_err_zone_rename');
            $top_message['type'] = 'W';
            $redirect_to = "?mode=add";
            $error = true;
        }

    // Update zone elements

        if (!$error && $zoneid > 0) {

            $zone_countries = explode(";", $zone_countries_store);
            $zone_states = explode(";", $zone_states_store);
            $zone_counties = @explode(";", $zone_counties_store);

            if ($zone_states) {
                foreach($zone_states as $v) {
                    if (preg_match('!^(.+)_!S',$v, $m))
                        $zone_countries[] = $m[1];
                }
            }
            if ($zone_counties) {
                foreach($zone_counties as $countyid) {
                    if (empty($countyid))
                        continue;
                    $stateid = func_query_first_cell("SELECT stateid FROM $sql_tbl[counties] WHERE countyid = '$countyid'");
                    $state_info = func_query_first("SELECT code, country_code FROM $sql_tbl[states] WHERE stateid = '$stateid'");
                    $zone_countries[] = $state_info['country_code'];
                    $zone_states[] = $state_info['country_code'] . '_' . $state_info['code'];
                }
            }

            // Update zone countries...
            func_insert_zone_element($zoneid, 'C', $zone_countries);
            // Update zone states...
            func_insert_zone_element($zoneid, 'S', $zone_states);
            // Update zone counties...
            func_insert_zone_element($zoneid, 'G', $zone_counties);
            // Update zone city masks...
            func_insert_zone_element($zoneid, 'T', explode("\n", $zone_cities));
            // Update zone zip code masks...
            func_insert_zone_element($zoneid, 'Z', explode("\n", $zone_zipcodes));
            // Update zone address masks...
            func_insert_zone_element($zoneid, 'A', explode("\n", $zone_addresses));

            func_zone_cache_update($zoneid, true);

            $top_message['content'] = func_get_langvar_by_name('msg_zone_upd');

            $redirect_to = "?zoneid=$zoneid";

        }
    }

    if ($mode == 'delete') {

    // Delete selected zones

        if (is_array($to_delete)) {
            foreach ($to_delete as $zoneid=>$v) {
                if (!empty($zoneid)) {
                    db_query("DELETE FROM $sql_tbl[zones] WHERE zoneid='$zoneid'");
                    db_query("DELETE FROM $sql_tbl[zone_element] WHERE zoneid='$zoneid'");
                    db_query("DELETE FROM $sql_tbl[shipping_rates] WHERE zoneid='$zoneid'");
                    db_query("DELETE FROM $sql_tbl[tax_rates] WHERE zoneid='$zoneid'");
                }
            }
            $top_message['content'] = func_get_langvar_by_name('msg_zone_del');
        }
    }

    if ($mode == 'clone') {

    // Clone zone info

        $zone_data = func_query_first("SELECT * FROM $sql_tbl[zones] WHERE zoneid='$zoneid'");
        if (!empty($zone_data)) {
            // Duplicate main zone data
            $zone_data['zoneid'] = '';
            $zone_data['provider'] = $logged_userid;
            $zone_data['zone_name'] = $zone_data['zone_name']." (clone)";
            foreach ($zone_data as $k=>$v) {
                $zone_data[$k] = "'".addslashes($v)."'";
            }
            db_query("INSERT INTO $sql_tbl[zones] (".implode(",",array_keys($zone_data)).") VALUES (".implode(",",$zone_data).")");
            $new_zoneid = db_insert_id();

            $zone_elements = func_query("SELECT * FROM $sql_tbl[zone_element] WHERE zoneid='$zoneid'");
            if (is_array($zone_elements)) {
                foreach ($zone_elements as $k=>$zone_element) {
                    db_query("INSERT INTO $sql_tbl[zone_element] (zoneid, field, field_type) VALUES ('$new_zoneid', '".addslashes($zone_element["field"])."', '$zone_element[field_type]')");
                }
            }
            $top_message['content'] = func_get_langvar_by_name('msg_zone_cloned');
        }

        $redirect_to = "?zoneid=$new_zoneid";

    }

    func_header_location('zones.php'.$redirect_to);
}

$location[] = array(func_get_langvar_by_name('lbl_destination_zones'), '');

if ($mode == 'add' or !empty($zoneid)) {
/**
 * Display zone details page
 */
    $location[count($location) - 1][1] = 'zones.php';
    $location[] = array(func_get_langvar_by_name('lbl_zone_details'), '');

    if (!empty($zoneid))
        $zone = func_query_first("SELECT * FROM $sql_tbl[zones] WHERE zoneid='$zoneid' $provider_condition");

    if (empty($zone)) {
        $zone = '';
        $mode = 'add';
    }        

    // Countries in this zone and rest

    $zone_countries = func_query("SELECT $sql_tbl[countries].code, $sql_tbl[languages].value as country FROM $sql_tbl[zone_element], $sql_tbl[countries], $sql_tbl[languages] WHERE $sql_tbl[zone_element].field_type='C' AND $sql_tbl[zone_element].field=$sql_tbl[countries].code AND $sql_tbl[languages].name = CONCAT('country_', $sql_tbl[countries].code) AND $sql_tbl[languages].code='$shop_language' AND $sql_tbl[countries].active='Y' AND $sql_tbl[zone_element].zoneid='$zoneid' ORDER BY country");

    $rest_countries = func_query("SELECT $sql_tbl[countries].code, $sql_tbl[countries].region, $sql_tbl[languages].value as country, $sql_tbl[zone_element].zoneid FROM $sql_tbl[languages], $sql_tbl[countries] LEFT JOIN $sql_tbl[zone_element] ON $sql_tbl[zone_element].field_type='C' AND $sql_tbl[zone_element].field=$sql_tbl[countries].code AND $sql_tbl[zone_element].zoneid='$zoneid' WHERE $sql_tbl[countries].active='Y' AND $sql_tbl[languages].name = CONCAT('country_', $sql_tbl[countries].code) AND $sql_tbl[languages].code='$shop_language' AND zoneid IS NULL ORDER BY country");
    $rest_zones = array();
    if($rest_countries) {
        foreach($rest_countries as $v)
            $rest_zones[$v['region']][] = $v['code'];
//        $rest_zones['SU'] = array('AM','AZ','BY','EE','GE','KZ','KG','LV','LT','MD','RU','TJ','TM','UA','UZ');
    }

    $smarty->assign('countries_box_size', min(20, max(count($rest_countries)+5, count($zone_countries)+5)));

    // States in this zone and rest

    $zone_states = func_query("SELECT $sql_tbl[states].* FROM $sql_tbl[states], $sql_tbl[zone_element] WHERE $sql_tbl[zone_element].field_type='S' AND $sql_tbl[zone_element].field=CONCAT($sql_tbl[states].country_code,'_',$sql_tbl[states].code) AND $sql_tbl[zone_element].zoneid='$zoneid' ORDER BY $sql_tbl[states].country_code, $sql_tbl[states].state");

    $rest_states = func_query("SELECT $sql_tbl[states].*, $sql_tbl[zone_element].zoneid FROM $sql_tbl[countries], $sql_tbl[states] LEFT JOIN $sql_tbl[zone_element] ON $sql_tbl[zone_element].field_type='S' AND $sql_tbl[zone_element].field=CONCAT($sql_tbl[states].country_code,'_',$sql_tbl[states].code) AND $sql_tbl[zone_element].zoneid='$zoneid' WHERE $sql_tbl[countries].code=$sql_tbl[states].country_code AND $sql_tbl[countries].active='Y' AND zoneid IS NULL ORDER BY $sql_tbl[states].country_code, $sql_tbl[states].state");

    $_distinct_countries = func_query("SELECT DISTINCT country_code, $sql_tbl[languages].value as country FROM $sql_tbl[states], $sql_tbl[languages] WHERE $sql_tbl[languages].name = CONCAT('country_', $sql_tbl[states].country_code) AND $sql_tbl[languages].code='$shop_language'");

    $state_country = array();
    if (is_array($_distinct_countries)) {
        foreach ($_distinct_countries as $k=>$v)
            $state_country[$v['country_code']] = $v['country'];
    }

    if (is_array($zone_states)) {
        foreach ($zone_states as $k=>$v)
            $zone_states[$k]['country'] = $state_country[$v['country_code']];
    }
    if (is_array($rest_states)) {
        foreach ($rest_states as $k=>$v)
            $rest_states[$k]['country'] = $state_country[$v['country_code']];
    }

    $smarty->assign('states_box_size', min(20, max(count($rest_states)+5, count($zone_states)+5)));

    if ($config['General']['use_counties'] == 'Y') {

    // Counties in this zone and rest

        if (func_query_first_cell("SELECT countyid FROM $sql_tbl[counties]")) {
            $zone_counties = func_query("SELECT $sql_tbl[counties].*, $sql_tbl[states].code as state_code, $sql_tbl[states].state, $sql_tbl[countries].code as country_code FROM $sql_tbl[counties], $sql_tbl[zone_element], $sql_tbl[states], $sql_tbl[countries] WHERE $sql_tbl[zone_element].field_type='G' AND $sql_tbl[zone_element].field=$sql_tbl[counties].countyid AND $sql_tbl[zone_element].zoneid='$zoneid' AND $sql_tbl[states].stateid=$sql_tbl[counties].stateid AND $sql_tbl[countries].code=$sql_tbl[states].country_code AND $sql_tbl[countries].active='Y' ORDER BY $sql_tbl[states].country_code, $sql_tbl[states].state, $sql_tbl[counties].county");

            $rest_counties = func_query("SELECT $sql_tbl[counties].*, $sql_tbl[states].code as state_code, $sql_tbl[states].state, $sql_tbl[countries].code as country_code, $sql_tbl[zone_element].zoneid FROM $sql_tbl[states], $sql_tbl[countries], $sql_tbl[counties] LEFT JOIN $sql_tbl[zone_element] ON $sql_tbl[zone_element].field_type='G' AND $sql_tbl[zone_element].field=$sql_tbl[counties].countyid AND $sql_tbl[zone_element].zoneid='$zoneid' WHERE $sql_tbl[states].stateid=$sql_tbl[counties].stateid AND $sql_tbl[countries].code=$sql_tbl[states].country_code AND $sql_tbl[countries].active='Y' AND zoneid IS NULL ORDER BY $sql_tbl[states].country_code, $sql_tbl[states].state, $sql_tbl[counties].county");

            if (!empty($zone_counties) && is_array($zone_counties)) {
                foreach ($zone_counties as $k => $v) {
                    $zone_counties[$k]['country'] = $state_country[$v['country_code']];
                }
            }

            if (!empty($rest_counties) && is_array($rest_counties)) {
                foreach ($rest_counties as $k => $v) {
                    $rest_counties[$k]['country'] = $state_country[$v['country_code']];
                }
            }

            $smarty->assign('zone_counties', $zone_counties);
            $smarty->assign('rest_counties', $rest_counties);
            $smarty->assign('counties_box_size', min(20, max(count($rest_counties)+5, count($zone_counties)+5)));
        }
    }

    // City/Zipcode/Address masks in this zone and rest

    $zone_elements = func_query("SELECT * FROM $sql_tbl[zone_element] WHERE zoneid='$zoneid' AND field_type IN ('T','Z','A')");

    $smarty->assign('zone_elements', $zone_elements);

    $smarty->assign('cities_box_size', min(10, func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[zone_element] WHERE zoneid='$zoneid' AND field_type='T'") + 9));

    $smarty->assign('zipcodes_box_size', min(10, func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[zone_element] WHERE zoneid='$zoneid' AND field_type='Z'") + 9));

    $smarty->assign('addresses_box_size', min(10, func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[zone_element] WHERE zoneid='$zoneid' AND field_type='A'") + 9));

    $smarty->assign('zoneid', $zoneid);

    $smarty->assign('zone', $zone);

    $smarty->assign('zone_countries', $zone_countries);
    $smarty->assign('rest_countries', $rest_countries);
    $smarty->assign('rest_zones', $rest_zones);

    $smarty->assign('zone_states', $zone_states);
    $smarty->assign('rest_states', $rest_states);

    $smarty->assign('zone_elements', $zone_elements);

    $smarty->assign('main', 'zone_edit');
}
else {
/**
 * Get the zones list
 */
    $zones = func_query("SELECT $sql_tbl[zones].* FROM $sql_tbl[zones] WHERE 1 $provider_condition ORDER BY $sql_tbl[zones].zone_name");

    if (!empty($zones)) {

    // Gather the additional information on each zone (for notes field)

        foreach ($zones as $k=>$zone) {
            if (!empty($zone['zone_cache'])) {
                $zone_cache_array = explode("-", $zone['zone_cache']);
                for ($i = 0; $i < count($zone_cache_array); $i++) {
                    if (preg_match("/^([\w])([0-9]+)$/", $zone_cache_array[$i], $match)) {
                        $zones[$k]['elements'][$i]['element_type'] = $match[1];
                        $zones[$k]['elements'][$i]['counter']= $match[2];

                        if ($match[2] == 1) {

                            $_element = func_query_first_cell("SELECT field FROM $sql_tbl[zone_element] WHERE zoneid='$zone[zoneid]' AND field_type='$match[1]'");
                            if ($match[1] == 'C')
                                $element_name = func_get_country($_element);

                            elseif ($match[1] == 'S')
                                $element_name = func_get_state(substr($_element, strpos($_element, '_')+1), substr($_element, 0, strpos($_element, '_')));

                            elseif ($match[1] == 'G')
                                $element_name = func_get_county($_element);

                            else
                                $element_name = $_element;

                            $zones[$k]['elements'][$i]['element_name'] = $element_name;

                        }
                    }
                }

                usort($zones[$k]['elements'], 'sort_zone_elements');

            }
        }
    }

    $smarty->assign('zones', $zones);
    $smarty->assign('main', 'zones');
}

$smarty->assign('mode', $mode);
$smarty->assign('zones', $zones);

/**
 * Define data for the navigation within section
 */
$dialog_tools_data = array();

$dialog_tools_data['right'][] = array('link' => 'import.php', 'title' => func_get_langvar_by_name('lbl_import_zones'));

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
func_display('provider/home.tpl',$smarty);
?>
