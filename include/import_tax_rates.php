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
 * Tax rates import/export
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import_tax_rates.php,v 1.34.2.1 2011/01/10 13:11:49 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/******************************************************************************
Used cache format:
Zones:
    data_type:  Z
    key:        <Zone name>
    value:      [<Zone ID> | RESERVED]
Memberships:
    data_type:  M
    key:        <Membership name>
    value:      <Membership ID>
Taxes:
    data_type:  T
    key:        <Tax service name>
    value:      [<Tax ID> | RESERVED]
Tax rates:
    data_type:  TR
    key:        <Tax rate ID>
    value:      <Tax rate ID>
Providers:
    data_type:  P
    key:        <Provider login>
    value:      <Provider login>

Note: RESERVED is used if ID is unknown
******************************************************************************/

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if (!defined('IMPORT_TAX_RATES')) {
/**
 * Make default definitions (only on first inclusion!)
 */
    define('IMPORT_TAX_RATES', 1);

    $import_specification['TAX_RATES'] = array(
        'script'        => '/include/import_tax_rates.php',
        'permissions'   => 'AP', // Admin and provider can import tax rates
        'need_provider' => 1,
        'parent'        => 'TAXES',
        'export_sql'    => "SELECT taxid FROM $sql_tbl[tax_rates] GROUP BY taxid",
        'columns'       => array(
            'rateid'        => array( // Integer: rateid
                'is_key'    => true,
                'type'      => 'N',
                'required'  => 0,  // Required field
                'default'   => 0), // Default value
            'tax'           => array( // String: tax service name
                'is_key'    => true,
                'required'  => 1),
            'zone'          => array( // String: destination zone name
                'is_key'    => true),
            'rate'          => array( // Numeric: rate value
                'is_key'    => true,
                'required'  => 1,
                'type'      => 'N',
                'default'   => 0),
            'type'          => array( // Char: rate type ('%' or '$')
                'is_key'    => true,
                'required'  => 1,
                'default'   => "%"),
            'membership'    => array( // String: membership name
                'array'     => true),
            'formula'       => array(), // String: tax formula
            'provider'      => array()
        )
    );

    return;
}

$provider_condition = ($single_mode ? '' : " AND provider='".addslashes($import_data_provider)."'");

if ($import_step == 'process_row') {
/**
 * PROCESS ROW from import file
 */

    // Set provider for current tax rate...

    if (func_import_is_provider_rewrite($values)) {
        $_provider = func_import_get_cache('P', $values['provider']);
        if (is_null($_provider)) {
            $_provider = func_query_first_cell("SELECT id FROM $sql_tbl[customers] WHERE login='".addslashes($values["provider"])."' AND usertype='P'");
            if (empty($_provider)) {
                $_provider = NULL;
            } else {
                func_import_save_cache('P', $values['provider'], $_provider);
            }
        }
        if (is_null($_provider) || ($action == 'do' && empty($_provider))) {
            func_import_module_error('msg_err_import_log_message_34', array('provider' => $values['provider']));
            return false;
        }
        $values['provider'] = $_provider;
    } else {
        $values['provider'] = $import_data_provider;
    }

    // Prepare rateid...

    if (!empty($values['rateid'])) {
        $values['rateid'] = intval($values['rateid']);
        $tmp = func_import_get_cache('R', $values['rateid']);
        if (empty($tmp) && $values['rateid'] > 0) {
            func_import_save_cache('R', $values['rateid'], $values['rateid']);
        } elseif (!empty($tmp)) {
            $values['rateid'] = $tmp;
        }

        if (!$simple_mode) {
            $values['rateid'] = (func_query_first_cell("SELECT provider FROM $sql_tbl[tax_rates] WHERE rateid='$values[rateid]' AND provider<>'".addslashes($values["provider"])."'") == $values["provider"] ? "" : $values["rateid"]);
        }
    }

    // Check if tax exists or cached and get taxid (taxid is required)

    $_taxid = NULL;
    if (!empty($values['tax'])) {
        $_taxid = func_import_get_cache('T', $values['tax']);
        if (is_null($_taxid) && $import_file['drop']['taxes'] != 'Y') {
            $_taxid = func_query_first_cell("SELECT taxid FROM $sql_tbl[taxes] WHERE tax_name='".addslashes($values["tax"])."'");
            if (!empty($_taxid)) {
                func_import_save_cache('T', $values['tax'], $_taxid);
            } else {
                $_taxid = NULL;
            }
        }
    }

    if (is_null($_taxid) || ($action == 'do' && empty($_taxid))) {
    // Tax not found: add the error message into the log
        func_import_module_error('msg_err_import_log_message_3');
    }

    // Check if zone exists and get zoneid

    if (!empty($values['zone'])) {
        $_zoneid = func_import_get_cache('Z', $values['zone']);
        if (is_null($_zoneid)) {
            $_zoneid = func_query_first_cell("SELECT zoneid FROM $sql_tbl[zones] WHERE zone_name='".addslashes($values["zone"])."' $provider_condition");
            if (!empty($_zoneid)) {
                func_import_save_cache('Z', $values['zone'], $_zoneid);
            } else {
                $_zoneid = NULL;
            }
        }

        if (is_null($_zoneid) || ($action == 'do' && empty($_zoneid))) {
            func_import_module_error('msg_err_import_log_message_6', array('zone' => $values['zone']));
        }
    }

    // Check if membership exists or cached (membership is optional)

    $values['membershipid'] = array();
    if (!empty($values['membership'])) {
        if (!is_array($values['membership']))
            $values['membership'] = array($values['membership']);
        foreach ($values['membership'] as $v) {
            if (empty($v))
                continue;
            $_membershipid = func_import_get_cache('M', $v);
            if (empty($_membershipid)) {
                $_membershipid = func_detect_membership($v, 'C');
                if ($_membershipid == 0) {
                    // Membership is specified but does not exist
                    func_import_module_error('msg_err_import_log_message_5', array('membership'=>$v));
                } else {
                    func_import_save_cache('M', $v, $_membershipid);
                }
            }
            if (!empty($_membershipid))
                $values['membershipid'][] = $_membershipid;
        }
    }

    $data_row[] = $values;

}
elseif ($import_step == 'finalize') {
/**
 * FINALIZE rows processing: update database
 */

    if ($import_file['drop']['tax_rates'] == 'Y') {

    // Drop old tax rates related info

        if ($provider_condition) {
            // Search the tax rates created by provider...
            $tax_rates_to_delete = db_query("SELECT rateid FROM $sql_tbl[tax_rates] WHERE 1 $provider_condition");
            if ($tax_rates_to_delete) {
                while ($value = db_fetch_array($tax_rates_to_delete)) {
                // Delete tax rates related information...
                    db_query("DELETE FROM $sql_tbl[tax_rate_memberships] WHERE rateid='$value[rateid]'");
                }
                // Delete tax rate information...
                db_query("DELETE FROM $sql_tbl[tax_rates] WHERE 1 $provider_condition");
            }
        }
        else {
        // Delete all tax rates and related information...
            db_query("DELETE FROM $sql_tbl[tax_rate_memberships]");
            db_query("DELETE FROM $sql_tbl[tax_rates]");
        }

        $import_file['drop']['tax_rates'] = '';
    }

    foreach ($data_row as $tax_rate) {

        // Validate some fields...
        $zoneid = 0;
        if (!empty($tax_rate['zone']))
            $zoneid = func_import_get_cache('Z', $tax_rate['zone']);
        $taxid = func_import_get_cache('T', $tax_rate['tax']);

        $tax_rate['rate'] = doubleval($tax_rate['rate']);
        if ($tax_rate['type'] != "%")
            $tax_rate['type'] = "$";

        // Search if rate already exists...
        $_rateid = false;
        if (!empty($tax_rate['rateid']) && func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[tax_rates] WHERE rateid='".intval($tax_rate["rateid"])."'".$provider_condition) > 0)
            $_rateid = $tax_rate['rateid'];
        if (empty($_rateid))
            $_rateid = func_query_first_cell("SELECT rateid FROM $sql_tbl[tax_rates] WHERE taxid='$taxid' AND zoneid='$zoneid' AND formula='".addslashes($tax_rate["formula"])."' AND rate_value='$tax_rate[rate]' AND rate_type='$tax_rate[type]' ".$provider_condition);

        $data = array(
            'taxid'            => $taxid,
            'zoneid'        => $zoneid,
            'formula'        => addslashes($tax_rate['formula']),
            'rate_value'    => $tax_rate['rate'],
            'rate_type'        => $tax_rate['type'],
            'provider'        => addslashes($tax_rate['provider'])
        );

        // Update tax rate
        if (!empty($_rateid)) {
            func_array2update('tax_rates', $data, "rateid='$_rateid'");
            $result['tax_rates']['updated']++;

        // Add tax rate
        } else {
            if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[tax_rates] WHERE rateid='".intval(@$tax_rate["rateid"])."'") == 0)
                $data['rateid'] = $tax_rate['rateid'];
            $_rateid = func_array2insert('tax_rates', $data);
            if (!empty($_rateid))
                $result['tax_rates']['added']++;
        }

        if (empty($_rateid))
            continue;

        // Update tax rate memberships
        db_query("DELETE FROM $sql_tbl[tax_rate_memberships] WHERE rateid='$_rateid'");

        if (!empty($tax_rate['membershipid'])) {
            foreach ($tax_rate['membershipid'] as $_membershipid) {
                func_array2insert('tax_rate_memberships', array('rateid' => $_rateid, 'membershipid' => $_membershipid));
            }
        }

        func_flush(". ");
    }

// Export data
} elseif ($import_step == 'export') {

    while ($id = func_export_get_row($data)) {
        if (empty($id))
            continue;

        // Get data
        $mrow = func_query("SELECT $sql_tbl[tax_rates].*, $sql_tbl[zones].zone_name as zone, $sql_tbl[taxes].tax_name as tax FROM $sql_tbl[taxes], $sql_tbl[tax_rates] LEFT JOIN $sql_tbl[zones] ON $sql_tbl[zones].zoneid = $sql_tbl[tax_rates].zoneid WHERE $sql_tbl[taxes].taxid = $sql_tbl[tax_rates].taxid AND $sql_tbl[tax_rates].taxid = '$id'".(empty($provider_sql) ? "" : " AND $sql_tbl[tax_rates].provider = '$provider_sql'"));
        if (empty($mrow))
            continue;

        foreach ($mrow as $row) {
            $row = func_export_rename_cell($row, array('rate_value' => 'rate', 'rate_type' => 'type'));
            func_unset($row, 'provider');

            // Get rate memberships
            $mems = func_query_column("SELECT membership FROM $sql_tbl[memberships], $sql_tbl[tax_rate_memberships] WHERE $sql_tbl[memberships].membershipid = $sql_tbl[tax_rate_memberships].membershipid AND $sql_tbl[tax_rate_memberships].rateid = '$row[rateid]'");
            if (!empty($mems)) {
                $row['membership'] = $mems;
            }

            // Write row
            if (!func_export_write_row($row))
                break;
        }
    }
}

?>
