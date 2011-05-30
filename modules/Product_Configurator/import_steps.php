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
 * Import/export configuration steps params
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import_steps.php,v 1.28.2.1 2011/01/10 13:11:59 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/******************************************************************************
Used cache format:
Products (by Product ID):
    data_type:     PI
    key:        <Product ID>
    value:        [<Product code> | RESERVED]
Products (by Product code):
    data_type:     PR
    key:        <Product code>
    value:        [<Product ID> | RESERVED]
Products (by Product name):
    data_type:  PN
    key:        <Product name>
    value:        [<Product ID> | RESERVED]
Wizard steps (by Product ID / Product code / Product name):
    data_type:     St
    key:        <Product ID / Product code / Product name><EOL><Step ID>
    value:        [<Step ID> | RESERVED]
Wizard steps (by Step ID):
    data_type:     Si
    key:        <Step ID>
    value:        <Step ID>
Old step IDs:
    data_type:    oPW
    key:        <Product ID>_<Step name>
    value:        <Step ID>
Deleted product data:
    data_type:    DP
    key:        <Product ID>
    value:        <Flags>

Note: RESERVED is used if ID is unknown
    EOL - End-of_line symbol (\n)
******************************************************************************/

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if ($import_step == 'process_row') {

    // PROCESS ROW from import file

    // Check productid / productcode / product
    list($_productid, $_variantid) = func_import_detect_product($values);
    if (is_null($_productid) || ($action == 'do' && empty($_productid))) {
        func_import_module_error('msg_err_import_log_message_14');
        return false;
    }

    // Check step
    foreach ($values['stepid'] as $k => $v) {
        func_import_get_pb_cache($values, 'St', $v, true);
        func_import_save_cache('Si', $v, $v);
    }

    $values['productid'] = $_productid;

    $data_row[] = $values;
}
elseif ($import_step == 'finalize') {

    // FINALIZE rows processing: update database

    // Drop old data
    if ($import_file['drop'][strtolower($section)] == 'Y') {
        // Delete data by provider
        if ($provider_condition) {
            $products = db_query("SELECT productid FROM $sql_tbl[products] WHERE provider = '".addslashes($import_data_provider)."'");
            if ($products) {
                while ($productid = db_fetch_array($products)) {
                    $productid = $productid['productid'];
                    $stepids = func_query_column("SELECT stepid FROM $sql_tbl[pconf_wizards] WHERE productid = '$productid'");
                    if (!empty($stepids)) {
                        func_import_save_cache_ids('oPW', "SELECT stepid, productid, step_name FROM $sql_tbl[pconf_wizards] WHERE productid = '$productid'");
                        db_query("DELETE FROM $sql_tbl[pconf_wizards] WHERE productid = '$productid'");
                        $slotids = func_query_column("SELECT slotid FROM $sql_tbl[pconf_slots] WHERE stepid IN ('".implode("','", $stepids)."')");
                        if (!empty($slotids)) {
                            db_query("DELETE FROM $sql_tbl[pconf_slots] WHERE slotid IN ('".implode("','", $slotids)."')");
                            db_query("DELETE FROM $sql_tbl[pconf_slot_rules] WHERE slotid IN ('".implode("','", $slotids)."')");
                            db_query("DELETE FROM $sql_tbl[pconf_slot_markups] WHERE slotid IN ('".implode("','", $slotids)."')");
                        }
                    }
                }
            }
        }
        else {
            // Delete all old data
            func_import_save_cache_ids('oPW', "SELECT stepid, productid, step_name FROM $sql_tbl[pconf_wizards]");

            db_query("DELETE FROM $sql_tbl[pconf_wizards]");
            db_query("DELETE FROM $sql_tbl[pconf_slots]");
            db_query("DELETE FROM $sql_tbl[pconf_slot_rules]");
            db_query("DELETE FROM $sql_tbl[pconf_slot_markups]");
        }

        $import_file['drop'][strtolower($section)] = '';
    }

    // Import data...

    foreach ($data_row as $row) {
        // Import product configurator steps
        foreach ($row['stepid'] as $k => $v) {
            // Detect step
            $_stepid = func_query_first_cell("SELECT $sql_tbl[pconf_wizards].stepid FROM $sql_tbl[pconf_wizards], $sql_tbl[products] WHERE $sql_tbl[pconf_wizards].productid = $sql_tbl[products].productid AND $sql_tbl[pconf_wizards].productid = '$row[productid]' AND $sql_tbl[pconf_wizards].stepid = '$v' AND $sql_tbl[products].provider = '".addslashes($import_data_provider)."'");

            $data = array(
                'productid'    => $row['productid'],
            );

            if (isset($row['orderby'][$k]))
                $data['orderby'] = addslashes($row['orderby'][$k]);

            if (empty($_stepid)) {

                // Add step
                $is_exists = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[pconf_wizards] WHERE stepid = '$v'") > 0;
                if ($is_exists)
                    $_stepid = func_import_get_cache('oPW', array($row['productid'], $v));
                else
                    $_stepid = $v;

                if (!empty($_stepid) && func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[pconf_wizards] WHERE stepid = '$_stepid'") == 0)
                    $data['stepid'] = $_stepid;

                $_stepid = func_array2insert('pconf_wizards', $data);
                func_array2update(
                    'pconf_wizards',
                    array(
                        'step_name' => $language_var_names['step_name'].$_stepid,
                        'step_descr'=> $language_var_names['step_descr'].$_stepid
                    ),
                    "stepid = '$_stepid'"
                );
                func_languages_alt_insert($language_var_names['step_name'].$_stepid, 'Step #'.$_stepid, $shop_language);
                func_languages_alt_insert($language_var_names['step_descr'].$_stepid, 'Step #'.$_stepid, $shop_language);

                $result[strtolower($section)]['added']++;

            } else {

                // Update step
                func_array2update('pconf_wizards', $data, "stepid = '$_stepid'");
                $result[strtolower($section)]['updated']++;
            }

            if (!empty($_stepid)) {
                func_import_save_pb_cache($row, 'St', $v, $_stepid);
                func_import_save_cache('Si', $v, $_stepid);
            }
        }

        func_flush(". ");
    }
}
elseif ($import_step == 'export') {
    // Export data

    while (($id = func_export_get_row($data)) !== false) {
        if (empty($id))
            continue;

        // Get data
        $mrow = func_query("SELECT $sql_tbl[pconf_wizards].* FROM $sql_tbl[pconf_wizards], $sql_tbl[products] WHERE $sql_tbl[pconf_wizards].productid = $sql_tbl[products].productid AND $sql_tbl[pconf_wizards].productid = '$id'".(empty($provider_sql) ? "" : " AND $sql_tbl[products].provider = '$provider_sql'"));
        if (empty($mrow))
            continue;

        // Get product signature
        $p_row = func_export_get_product($id);
        if (empty($p_row))
            continue;

        foreach ($mrow as $row) {
            $p_row['stepid'][] = $row['stepid'];
            $p_row['orderby'][] = $row['orderby'];
        }

        // Write row
        if (!func_export_write_row($p_row))
            break;
    }
}

?>
