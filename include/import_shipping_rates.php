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
 * Import/export shipping rates
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import_shipping_rates.php,v 1.27.2.1 2011/01/10 13:11:49 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/******************************************************************************
Used cache format:
Zones:
    data_type:  Z
    key:        <Zone name>
    value:      [<Zone ID> | RESERVED]
Shipping methods:
    data_type:  SH
    key:        <Shipping method name>
    value:      [<Shipping method ID> | RESERVED]
Shipping methods (by Shipping method ID):
    data_type:  Sh
    key:        <Shipping method ID>
    value:      [<Shipping method ID> | RESERVED]

Note: RESERVED is used if ID is unknown
******************************************************************************/

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if ($import_step == 'define') {

    $import_specification['SHIPPING_RATES'] = array(
        'script'        => '/include/import_shipping_rates.php',
        'permissions'   => 'AP',
        'need_provider' => true,
        'export_sql'    => "SELECT rateid FROM $sql_tbl[shipping_rates]",
        'orderby'       => 30,
        'columns'       => array(
            'shippingid'    => array(
                'type'      => 'N'),
            'shipping'      => array(),
            'zone'          => array(),
            'minweight'     => array(
                'type'      => 'N'),
            'maxweight'     => array(
                'type'      => 'N'),
            'mintotal'      => array(
                'type'      => 'P'),
            'maxtotal'      => array(
                'type'      => 'P'),
            'absolute_rate' => array(
                'type'      => 'N'),
            'item_rate'     => array(
                'type'      => 'N'),
            'weight_rate'   => array(
                'type'      => 'N'),
            'percent_rate'  => array(
                'type'      => 'N'),
            'type'          => array(
                'type'      => 'E',
                'variants'  => array('D','R'),
                'default'   => 'D')
        )
    );

} elseif ($import_step == 'process_row') {
/**
 * PROCESS ROW from import file
 */
    $provider_condition = ($single_mode ? '' : " AND provider = '".addslashes($import_data_provider)."'");

    $_shippingid = NULL;
    // Check shippingid
    if (!empty($values['shippingid'])) {
        $_shippingid = func_import_get_cache('Sh', $values['shippingid']);
        if (is_null($_shippingid)) {
            $_shippingid = func_query_first_cell("SELECT shippingid FROM $sql_tbl[shipping] WHERE shippingid = '$values[shippingid]'");
            if (empty($_shippingid)) {
                $_shippingid = NULL;
            } else {
                func_import_save_cache('Sh', $values['shippingid'], $_shippingid, true);
            }
        }
    }

    // Check shipping method name
    if (!empty($values['shipping']) && (is_null($_shippingid) || ($action == 'do' && empty($_shippingid)))) {
        $_shippingid = func_import_get_cache('SH', $values['shipping']);
        if (is_null($_shippingid)) {
            $_shippingid = func_query_first_cell("SELECT shippingid FROM $sql_tbl[shipping] WHERE shipping = '".addslashes($values['shipping'])."'");
            if (empty($_shippingid)) {
                $_shippingid = NULL;
            } else {
                func_import_save_cache('SH', $values['shipping'], $_shippingid, true);
            }
        }
    }

    if (is_null($_shippingid) || ($action == 'do' && empty($_shippingid))) {
        func_import_module_error('msg_err_import_log_message_31');
        return false;
    }
    $values['shippingid'] = $_shippingid;

    // Check zone
    $_zoneid = 0;
    if (!empty($values['zone'])) {
        $_zoneid = func_import_get_cache('Z', $values['zone']);
        if (is_null($_zoneid)) {
            $_zoneid = func_query_first_cell("SELECT zoneid FROM $sql_tbl[zones] WHERE zone_name='".addslashes($values["zone"])."' $provider_condition");
            if (empty($_zoneid)) {
                $_zoneid = NULL;
            } else {
                func_import_save_cache('Z', $values['zone'], $_zoneid);
            }
        }
        if (is_null($_zoneid) || ($action == 'do' && empty($_zoneid))) {
            func_import_module_error('msg_err_import_log_message_6', array('zone' => $values['zone']));
            return false;
        }
    }
    $values['zoneid'] = $_zoneid;

    $data_row[] = $values;

} elseif ($import_step == 'finalize') {
/**
 * FINALIZE rows processing: update database
 */

    // Drop old data
    if ($import_file['drop'][strtolower($section)] == 'Y') {

        // Delete data by provider
        if ($provider_condition) {
            db_query("DELETE FROM $sql_tbl[shipping_rates] WHERE 1 $provider_condition");

        // Delete all data
        } else {
            db_query("DELETE FROM $sql_tbl[shipping_rates]");
        }
        $import_file['drop'][strtolower($section)] = '';
    }

    foreach ($data_row as $row) {

    // Import data...

        // Import shipping rates

        // Detect rateid
        $_rateid = func_query_first_cell("SELECT rateid FROM $sql_tbl[shipping_rates] WHERE zoneid = '$row[zoneid]' AND shippingid = '$row[shippingid]' $provider_condition AND maxamount = '$row[maxamount]' AND mintotal = '$row[mintotal]' AND maxtotal = '$row[maxtotal]' AND minweight = '$row[minweight]' AND maxweight = '$row[maxweight]' AND type = '$row[type]'");

        // Add shipping rate
        if (empty($_rateid)) {
            $data = array(
                'shippingid'    => $row['shippingid'],
                'zoneid'        => $row['zoneid'],
                'provider'        => $import_data_provider,
                'maxamount'        => $row['maxamount'],
                'mintotal'        => $row['mintotal'],
                'maxtotal'        => $row['maxtotal'],
                'minweight'        => $row['minweight'],
                'maxweight'        => $row['maxweight'],
                'type'            => $row['type'],
                'rate'            => $row['absolute_rate'],
                'item_rate'        => $row['item_rate'],
                'weight_rate'    => $row['weight_rate'],
                'rate_p'        => $row['percent_rate']
            );
            $_rateid = func_array2insert('shipping_rates', $data);
            $result[strtolower($section)]['added']++;

        // Update shipping rate
        } else {
            $data = array(
                'rate'            => $row['absolute_rate'],
                'item_rate'        => $row['item_rate'],
                'weight_rate'    => $row['weight_rate'],
                'rate_p'        => $row['percent_rate']
            );
            func_array2update('shipping_rates', $data, "rateid = '$_rateid'");
            $result[strtolower($section)]['updated']++;
        }

        echo ". ";
        func_flush();

    }

// Export data
} elseif ($import_step == 'export') {

    while ($id = func_export_get_row($data)) {
        if (empty($id))
            continue;

        // Get data
        $row = func_query_first("SELECT $sql_tbl[shipping_rates].*, $sql_tbl[zones].zone_name as zone, $sql_tbl[shipping].shipping FROM $sql_tbl[shipping], $sql_tbl[shipping_rates] LEFT JOIN $sql_tbl[zones] ON $sql_tbl[shipping_rates].zoneid = $sql_tbl[zones].zoneid WHERE $sql_tbl[shipping].shippingid = $sql_tbl[shipping_rates].shippingid AND $sql_tbl[shipping_rates].rateid = '$id'".(empty($provider_sql) ? "" : " AND $sql_tbl[shipping_rates].provider = '$provider_sql'"));
        if (!$row)
            continue;

        $row = func_export_rename_cell($row, array('rate' => 'absolute_rate', 'rate_p' => 'percent_rate'));
        func_unset($row, 'rateid', 'provider');

        // Write row
        if (!func_export_write_row($row))
            break;
    }
}

?>
