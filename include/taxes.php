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
 * Taxes-related operations processor
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: taxes.php,v 1.51.2.3 2011/03/03 13:27:04 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('backoffice');

if (!in_array($mode, array('add', 'details', 'delete', 'update', 'rate_details', 'delete_rates', 'update_rates', 'tax_options', 'apply')))
    $mode = '';

$taxid = intval(@$taxid);

if ($REQUEST_METHOD == 'POST') {
    $redirect_to = '';

    if ($mode == 'tax_options' && defined('IS_ADMIN_USER')) {

        if (is_array($posted_data)) {

            foreach ($posted_data as $k=>$v) {

                if (!in_array($v, array('Y','N')))
                    $v = 'N';

                db_query("UPDATE $sql_tbl[config] SET value='$v' WHERE name='$k' AND category='Taxes'");

            }

            $top_message['content'] = func_get_langvar_by_name('msg_taxes_options_updated');
        }

    }
    elseif ($mode == 'delete' && defined('IS_ADMIN_USER')) {

        // Delete selected taxes

        if (!empty($to_delete) && is_array($to_delete)) {
            foreach ($to_delete as $taxid=>$v) {
                db_query("DELETE FROM $sql_tbl[taxes] WHERE taxid='$taxid'");
                $rateids = func_query_column("SELECT rateid FROM $sql_tbl[tax_rates] WHERE taxid='$taxid'");
                db_query("DELETE FROM $sql_tbl[tax_rates] WHERE taxid='$taxid'");
                db_query("DELETE FROM $sql_tbl[product_taxes] WHERE taxid='$taxid'");
                if (!empty($rateids)) {
                    db_query("DELETE FROM $sql_tbl[tax_rate_memberships] WHERE rateid IN ('".implode("','", $rateids)."')");
                }
            }

            $top_message['content'] = func_get_langvar_by_name('msg_taxes_deleted');
        }
    }
    elseif ($mode == 'update' && defined('IS_ADMIN_USER')) {

        // Update taxes list

        if (!empty($posted_data) && is_array($posted_data)) {
            foreach ($posted_data as $taxid=>$v) {
                db_query("UPDATE $sql_tbl[taxes] SET active='$v[active]', priority='$v[tax_priority]' WHERE taxid='$taxid'");
            }
            $top_message['content'] = func_get_langvar_by_name('msg_taxes_updated');
        }
    }
    elseif ($mode == 'details' && defined('IS_ADMIN_USER')) {

        // Add/Update tax details

        $tax_formula = preg_replace("/^=/", '', $tax_formula);
        $price_includes_tax = (!empty($price_includes_tax) ? 'Y' : 'N');
        $display_including_tax = (!empty($display_including_tax) ? 'Y' : 'N');
        $tax_priority = intval($tax_priority);
        if (!in_array($address_type, array('S','B')))
            $address_type = 'S';

        if (!@in_array($display_info, array('R','V','A')))
            $display_info = '';

        $tax_service_name = trim($tax_service_name);

        $error = false;
        if ((strlen($tax_service_name) == 0) || (strlen($tax_formula) == 0)) {

            // Form filled with errors
            $error = true;
            $top_message['content'] = func_get_langvar_by_name('err_filling_form');

        } elseif (!func_check_tax_service_name($tax_service_name)) {

            // Invalid format of tax service name
            $error = true;
            $top_message['content'] = func_get_langvar_by_name('txt_invalid_format_tax_service_name');

        } elseif (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[taxes] WHERE tax_name='$tax_service_name' AND taxid!='$taxid'") > 0) {

            //  tax service name is exists
            $error = true;
            $top_message['content'] = func_get_langvar_by_name('msg_err_duplicate_tax');
        }

        if ($error) {
            $tax_details['taxid'] = $taxid;
            $tax_details['tax_name'] = func_stripslashes($tax_service_name);
            $tax_details['tax_display_name'] = func_stripslashes($tax_display_name);
            $tax_details['active'] = $active;
            $tax_details['formula'] = $tax_formula;
            $tax_details['address_type'] = $address_type;
            $tax_details['price_includes_tax'] = $price_includes_tax;
            $tax_details['display_including_tax'] = $display_including_tax;
            $tax_details['display_info'] = $display_info;
            $tax_details['regnumber'] = $tax_regnumber;
            $tax_details['priority'] = $tax_priority;
            x_session_register('tmp_tax_details');
            $tmp_tax_details = $tax_details;

            $top_message['type'] = 'E';
            $redirect_to = empty($taxid) ? "?mode=add" : "?taxid=$taxid";
        }
        else {

            // Update/insert new tax

            if (empty($taxid)) {

                // Create new tax

                db_query("INSERT INTO $sql_tbl[taxes] (taxid) VALUES ('')");
                $taxid = db_insert_id();
                $top_message['content'] = func_get_langvar_by_name('msg_new_tax_created');
            }
            else {
                $top_message['content'] = func_get_langvar_by_name('msg_tax_upd');
            }

            // Update tax details

            $query_data = array(
                'tax_name' => $tax_service_name,
                'formula' => $tax_formula,
                'address_type' => $address_type,
                'active' => $active,
                'price_includes_tax' => $price_includes_tax,
                'display_including_tax' => $display_including_tax,
                'display_info' => $display_info,
                'regnumber' => $tax_regnumber,
                'priority' => $tax_priority
            );
            func_array2update('taxes', $query_data, "taxid='$taxid'");

            // Insert the display name of tax

            if (empty($tax_display_name))
                $tax_display_name = $tax_service_name;

            func_languages_alt_insert('tax_'.$taxid, $tax_display_name, $current_language);

            $redirect_to = "?taxid=$taxid";
        }
    }
    elseif ($mode == 'delete_rates' && !empty($taxid)) {

        // Delete selected tax rates

        if (!empty($to_delete) && is_array($to_delete)) {

            if (!$single_mode)
                $rate_ids = func_query_column("SELECT rateid FROM $sql_tbl[tax_rates] WHERE rateid IN ('".implode("','", array_keys($to_delete))."') ". $provider_condition);
            else
                $rate_ids = array_keys($to_delete);

            db_query("DELETE FROM $sql_tbl[tax_rates] WHERE rateid IN ('".implode("','", $rate_ids)."')");
            db_query("DELETE FROM $sql_tbl[tax_rate_memberships] WHERE rateid IN ('".implode("','", $rate_ids)."')");

            $top_message['content'] = func_get_langvar_by_name('msg_tax_rate_del');
            $top_message['anchor'] = 'rates';
        }

        $redirect_to = "?taxid=$taxid";
    }
    elseif ($mode == 'update_rates' && !empty($taxid)) {

        // Update tax rates

        if (!empty($posted_data) && is_array($posted_data)) {
            foreach ($posted_data as $rateid=>$v) {
                $rate_value = func_convert_number($v['rate_value'], '3'.substr($config['Appearance']['number_format'], 1));
                $rate_type = $v['rate_type'];
                if (!in_array($rate_type, array("%","$")))
                    $rate_type = "%";

                db_query("UPDATE $sql_tbl[tax_rates] SET rate_value='$rate_value', rate_type='$rate_type' WHERE rateid='$rateid' ".$provider_condition);
            }

            $top_message['content'] = func_get_langvar_by_name('msg_tax_rate_upd');
            $top_message['anchor'] = 'rates';
        }

        $redirect_to = "?taxid=$taxid";
    }
    elseif ($mode == 'rate_details' && !empty($taxid)) {

        // Add/Update new tax rate

        $rateid = intval(@$rateid);
        $rate_value = func_convert_number($rate_value, '3'.substr($config['Appearance']['number_format'], 1));
        $zoneid = intval($zoneid);
        if (!in_array($rate_type, array("%","$")))
            $rate_type = "%";

        if (empty($membershipids) || in_array(-1, $membershipids)) {
            $membershipids_where = "IS NULL ";
        }
        else {
            $membershipids_where = "IN ('".implode("','", $membershipids)."') ";
        }

        if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[tax_rates] LEFT JOIN $sql_tbl[tax_rate_memberships] ON $sql_tbl[tax_rates].rateid = $sql_tbl[tax_rate_memberships].rateid WHERE $sql_tbl[tax_rates].taxid = '$taxid' AND $sql_tbl[tax_rates].rateid != '$rateid' AND $sql_tbl[tax_rates].zoneid = '$zoneid' AND $sql_tbl[tax_rate_memberships].membershipid ".$membershipids_where.$provider_condition) == 0) {
            $rate_formula = preg_replace("/^=/", '', $rate_formula);

            $query_data = array(
                'zoneid' => $zoneid,
                'formula' => $rate_formula,
                'rate_value' => $rate_value,
                'rate_type' => $rate_type
            );

            if (!empty($rateid)) {

                // Update tax rate

                func_array2update('tax_rates', $query_data, "rateid='$rateid' ".$provider_condition);
                db_query("DELETE FROM $sql_tbl[tax_rate_memberships] WHERE rateid='$rateid'");
                $top_message['content'] = func_get_langvar_by_name('msg_tax_rate_upd');
            }
            else {

                // Add new tax rate

                $query_data['taxid'] = $taxid;
                $query_data['provider'] = $logged_userid;
                $rateid = func_array2insert('tax_rates', $query_data);
                $top_message['content'] = func_get_langvar_by_name('msg_tax_rate_add');
            }
            func_membership_update('tax_rate', $rateid, $membershipids, 'rateid');
        }
        else {
            $top_message['content'] = func_get_langvar_by_name('msg_err_tax_rate_add');
            $top_message['type'] = 'E';
        }

        $top_message['anchor'] = 'rates';

        $redirect_to = "?taxid=$taxid";
    }
    elseif ($mode == 'apply' && !empty($to_delete) && is_array($to_delete)) {

        // Apply the selected taxes to all the products

        if (!$single_mode) {
            $res = db_query("SELECT productid FROM $sql_tbl[products] WHERE provider = '$logged_userid'");
        }
        else {
            $res = db_query("SELECT productid FROM $sql_tbl[products]");
        }

        if ($res) {
            $to_delete = array_keys($to_delete);
            while ($p = db_fetch_array($res)) {
                foreach ($to_delete as $taxid) {
                    $query_data = array(
                        'productid' => $p['productid'],
                        'taxid' => $taxid
                    );
                    func_array2insert('product_taxes', $query_data, true);
                    func_build_quick_flags($p['productid']);
                    func_build_quick_prices($p['productid']);
                }
            }
        }

        $top_message = array(
            'content' => func_get_langvar_by_name('lbl_taxes_applying_is_success')
        );

    }

    func_header_location('taxes.php'.$redirect_to);
}

if ($mode == 'add' || !empty($taxid)) {

    // Display tax details page

    $location[count($location) - 1][1] = 'taxes.php';
    $location[] = array(func_get_langvar_by_name('lbl_tax_details'), '');

    if (!empty($taxid))
        $tax_details = func_query_first("SELECT * FROM $sql_tbl[taxes] WHERE taxid='$taxid'");

    if (empty($tax_details)) {
        $mode = 'add';
        if (x_session_is_registered('tmp_tax_details')) {
            x_session_register('tmp_tax_details');
            $tax_details = $tmp_tax_details;
            x_session_unregister('tmp_tax_details');
        }
    }
    else {
        $tax_details['tax_display_name'] = func_get_languages_alt('tax_'.$taxid, $current_language);

        if (!empty($provider_condition))
            $provider_condition_rates = preg_replace("/AND\s*provider\s*=/i", "AND $sql_tbl[tax_rates].provider=", $provider_condition);
        else
            $provider_condition_rates = '';

        $tax_rates = func_query("SELECT $sql_tbl[tax_rates].*, $sql_tbl[zones].zone_name FROM $sql_tbl[tax_rates] LEFT JOIN $sql_tbl[zones] ON $sql_tbl[tax_rates].zoneid=$sql_tbl[zones].zoneid WHERE $sql_tbl[tax_rates].taxid='$taxid' $provider_condition_rates ORDER BY $sql_tbl[zones].zone_name, $sql_tbl[tax_rates].rate_value");
        if (!empty($tax_rates)) {
            foreach ($tax_rates as $k => $v) {
                $tmp = func_get_memberships();
                $keys = func_query_column("SELECT membershipid FROM $sql_tbl[tax_rate_memberships] WHERE rateid = '$v[rateid]'");
                if (!empty($tmp) && !empty($keys)) {
                    $tax_rates[$k]['membershipids'] = array();
                    foreach ($tmp as $m) {
                        if (in_array($m['membershipid'], $keys))
                            $tax_rates[$k]['membershipids'][$m['membershipid']] = $m['membership'];
                    }
                }
            }
        }

        $smarty->assign('tax_rates', $tax_rates);

        if (!empty($rateid) && !empty($tax_rates) && is_array($tax_rates)) {
            $rate_formula = '';
            foreach ($tax_rates as $k=>$v) {
                if ($v['rateid'] == $rateid) {
                    $rate_details = $v;
                    break;
                }
            }

            $smarty->assign('rate_details', $rate_details);
        }

        $zones = func_query("SELECT * FROM $sql_tbl[zones] WHERE 1 $provider_condition ORDER BY zone_name");
        $smarty->assign('zones', $zones);
    }

    if (is_array($taxes_units)) {

        // Correct the tax formula units description

        foreach ($taxes_units as $k=>$v) {
            $taxes_units[$k] = func_get_langvar_by_name($v);
        }

        $_taxes = func_query("SELECT taxid, tax_name FROM $sql_tbl[taxes] WHERE taxid!='$taxid' ORDER BY tax_name");
        if (is_array($_taxes)) {
            foreach ($_taxes as $k=>$v) {
                $taxes_units[$v['tax_name']] = func_get_languages_alt('tax_'.$v['taxid'], $current_language);
            }
        }

        $smarty->assign('taxes_units', $taxes_units);
    }

    if (isset($tax_details))
        $smarty->assign('tax_details', $tax_details);

    $smarty->assign('main','tax_edit');
}
else {
    $taxes = func_query("SELECT $sql_tbl[taxes].*, COUNT($sql_tbl[tax_rates].taxid) as rates_count FROM $sql_tbl[taxes] LEFT JOIN $sql_tbl[tax_rates] ON $sql_tbl[tax_rates].taxid=$sql_tbl[taxes].taxid $provider_condition GROUP BY $sql_tbl[taxes].taxid ORDER BY priority, tax_name");

    $smarty->assign('taxes', $taxes);
    $smarty->assign('main','taxes');

}

$dialog_tools_data = array();
if (defined('IS_ADMIN_USER')) {

    // Define data for the navigation within section

    $dialog_tools_data['right'][] = array('link' => $xcart_catalogs['provider'].'/import.php', 'title' => func_get_langvar_by_name('lbl_import_tax_rates'));
}

$smarty->assign('mode',$mode);

$smarty->assign('memberships',func_get_memberships());

?>
