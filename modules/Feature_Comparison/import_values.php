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
 * Import/export comparison options values for products
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import_values.php,v 1.26.2.1 2011/01/10 13:11:56 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/******************************************************************************
Used cache format:
Products (by Product ID):
    data_type:  PI
    key:        <Product ID>
    value:      [<Product code> | RESERVED]
Products (by Product code):
    data_type:  PR
    key:        <Product code>
    value:      [<Product ID> | RESERVED]
Products (by Product name):
    data_type:  PN
    key:        <Product name>
    value:      [<Product ID> | RESERVED]
Product class option:
    data_type:  FO
    key:        <Class name>EOL<Option name>
    value:      [<Option ID> | RESERVED]

Note: RESERVED is used if ID is unknown
******************************************************************************/

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if ($import_step == 'process_row') {
/**
 * PROCESS ROW from import file
 */

    // Check productid / productcode / product
    list($_productid, $_variantid) = func_import_detect_product($values);
    if (is_null($_productid) || ($action == 'do' && empty($_productid))) {
        func_import_module_error('msg_err_import_log_message_14');
        return false;
    }
    $values['productid'] = $_productid;

    // Check class and option
    if (!is_array($values['option']))
        $values['option'] = array($values['option']);
    if (!is_array($values['value']))
        $values['value'] = array($values['value']);

    $values['values'] = array();
    $values['fclassid'] = false;
    foreach ($values['option'] as $k => $v) {
        if (empty($v))
            continue;
        if (!isset($values['value'][$k]))
            $values['value'][$k] = "";
        $_optionid = func_import_get_cache('FO', $values['class']."\n".$v);
        if (is_null($_optionid) && $import_file['drop']['product_classes'] != 'Y') {
            $_optionid = func_query_first_cell("SELECT foptionid FROM $sql_tbl[feature_classes], $sql_tbl[feature_options] WHERE $sql_tbl[feature_classes].fclassid = $sql_tbl[feature_options].fclassid AND $sql_tbl[feature_classes].class = '".addslashes($values['class'])."' AND $sql_tbl[feature_options].option_name = '".addslashes($v)."'");
            if (empty($_optionid)) {
                $_optionid = NULL;
            } else {
                func_import_save_cache('FO', $values['class']."\n".$v, $_optionid);
            }
        }

        if (is_null($_optionid) || ($action == 'do' && empty($_optionid))) {
            func_import_module_error('msg_err_import_log_message_27');
            return false;
        }

        $values['values'][] = array("optionid" => $_optionid, "value" => $values['value'][$k]);
    }

    $values['fclassid'] = func_query_first_cell("SELECT fclassid FROM $sql_tbl[feature_classes] WHERE class = '".addslashes($values['class'])."'");

    $data_row[] = $values;

} elseif ($import_step == 'finalize') {
/**
 * FINALIZE rows processing: update database
 */

    // Drop old data
    if ($import_file['drop'][strtolower($section)] == 'Y') {

        // Delete data by provider
        if ($provider_condition) {
            $fclasses = db_query("SELECT fclassid FROM $sql_tbl[feature_classes] WHERE provider = '".addslashes($import_data_provider)."'");
            if ($fclasses) {
                while ($fclassid = db_fetch_array($fclasses)) {
                    $fclassid = $fclassid['fclassid'];
                    db_query("DELETE FROM $sql_tbl[product_features] WHERE fclassid = '$fclassid'");
                    $optionids = func_query_column("SELECT foptionid FROM $sql_tbl[feature_options] WHERE fclassid = '$fclassid'");
                    if (!empty($optionids)) {
                        db_query("DELETE FROM $sql_tbl[product_foptions] WHERE foptionid IN ('".implode("','", $optionids)."')");
                    }
                }
            }

        // Delete all old data
        } else {
            db_query("DELETE FROM $sql_tbl[product_features]");
            db_query("DELETE FROM $sql_tbl[product_foptions]");
        }

        $import_file['drop'][strtolower($section)] = '';
    }

    foreach ($data_row as $row) {

    // Import data...

        // Import product feature class options

        // Import link product -> feature class
        $is_new = false;
        if(func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[product_features] WHERE productid = '$row[productid]' AND fclassid = '$row[fclassid]'") == 0) {
            db_query("DELETE FROM $sql_tbl[product_features] WHERE productid = '$row[productid]'");
            func_array2insert('product_features', array('productid' => $row['productid'], "fclassid" => $row['fclassid']));
            $is_new = true;
        }
        db_query("DELETE FROM $sql_tbl[product_foptions] WHERE productid = '$row[productid]'");

        // Import values
        foreach ($row['values'] as $v) {
            $type = func_query_first_cell("SELECT option_type FROM $sql_tbl[feature_options] WHERE foptionid = '$v[optionid]'");

            if ($type == 'S' || $type == 'M') {
                $_v = func_query_first_cell("SELECT $sql_tbl[feature_variants].fvariantid FROM $sql_tbl[feature_variants] LEFT JOIN $sql_tbl[feature_variants_lng] ON $sql_tbl[feature_variants].fvariantid=$sql_tbl[feature_variants_lng].fvariantid WHERE $sql_tbl[feature_variants].foptionid='$v[optionid]' AND code='$config[default_admin_language]' AND variant_name='$v[value]'");
                if (empty($_v))
                    continue;

                // Detect value from variants list
                if ($type == 'M') {
                    $_vv = func_sql_unserialize(func_query_first_cell("SELECT value FROM $sql_tbl[product_foptions] WHERE productid = '$row[productid]' AND foptionid = '$v[optionid]'"));
                    if (is_array($_vv) && !empty($_vv)) {
                        $__vv = func_query_column("SELECT variant_name FROM $sql_tbl[feature_variants_lng] WHERE code='$config[default_admin_language]' AND fvariantid IN ('".@implode("','", $_vv)."')");
                        if (!in_array($_v, $__vv)) {
                            array_push($_vv, $_v);
                        }
                        $_v = $_vv;
                    } else {
                        $_v = array($_v);
                    }
                    $_v = func_sql_serialize($_v);
                }
                $v['value'] = $_v;
            }

            func_array2insert('product_foptions', array('productid' => $row['productid'], "foptionid" => $v['optionid'], "value" => addslashes($v['value'])), true);
            if ($is_new) {
                $result[strtolower($section)]['added']++;
            } else {
                $result[strtolower($section)]['updated']++;
            }
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
        $row = func_query("SELECT $sql_tbl[feature_classes].class, $sql_tbl[feature_options].option_name as 'option', $sql_tbl[feature_options].option_hint, $sql_tbl[product_foptions].value, $sql_tbl[feature_options].option_type FROM $sql_tbl[product_foptions], $sql_tbl[products], $sql_tbl[feature_options], $sql_tbl[feature_classes] WHERE $sql_tbl[feature_classes].fclassid = $sql_tbl[feature_options].fclassid AND $sql_tbl[product_foptions].productid = $sql_tbl[products].productid AND $sql_tbl[feature_options].foptionid = $sql_tbl[product_foptions].foptionid AND $sql_tbl[product_foptions].productid = '$id'".(empty($provider_sql) ? "" : " AND $sql_tbl[products].provider = '$provider_sql'"));
        if (empty($row))
            continue;

        // Get product signature
        $p_row = func_export_get_product($id);
        if (empty($p_row))
            continue;

        $p_row['option'] = array();
        $p_row['value'] = array();

        // Define service arrays for columns optionid and value
        foreach ($row as $srow) {

            // If feature option - selector
            if (in_array($srow['option_type'], array('S', 'M'))) {

                // If feature option - simple selector
                if ($srow['option_type'] == "S") {
                    $srow['value'] = func_query_first_cell("SELECT variant_name FROM $sql_tbl[feature_variants_lng] WHERE fvariantid='$srow[value]' AND code='$config[default_admin_language]'");

                // If feature option - multiple selector
                } else {
                    $_value = func_sql_unserialize($srow['value']);
                    foreach ($_value as $vk) {
                        if (empty($p_row['class']))
                            $p_row['class'] = $srow['class'];
                        $p_row['option'][] = $srow['option'];
                        $p_row['value'][] = func_query_first_cell("SELECT variant_name FROM $sql_tbl[feature_variants_lng] WHERE fvariantid='$vk' AND code='$config[default_admin_language]'");
                    }
                    continue;
                }
            }

            // Add row to service array
            if (empty($p_row['class']))
                $p_row['class'] = $srow['class'];
            $p_row['option'][] = $srow['option'];
            $p_row['value'][] = $srow['value'];
        }

        // Write row
        if (!func_export_write_row($p_row))
            break;
    }
}

?>
