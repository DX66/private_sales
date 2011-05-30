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
 * Import/export feature options
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import_options.php,v 1.29.2.1 2011/01/10 13:11:56 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/******************************************************************************
Used cache format:
Product class:
    data_type:  FC
    key:        <Class name>
    value:      [<Class ID> | RESERVED]
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

    // Check class
    $_fclassid = func_import_get_cache('FC', $values['class']);
    if (is_null($_fclassid) && $import_file['drop']['product_classes'] != 'Y') {
        $_fclassid = func_query_first_cell("SELECT fclassid FROM $sql_tbl[feature_classes] WHERE class = '".addslashes($values['class'])."'");
        if (empty($_fclassid)) {
            $_fclassid = NULL;
        } else {
            func_import_save_cache('FC', $values['class'], $_fclassid);
        }
    }

    if (is_null($_fclassid) || ($action == 'do' && empty($_fclassid))) {
        func_import_module_error('msg_err_import_log_message_26');
        return false;
    }
    $values['fclassid'] = $_fclassid;

    $tmp = func_import_get_cache('FO', $values['class']."\n".$values['option']);
    if (is_null($tmp)) {
        func_import_save_cache('FO', $values['class']."\n".$values['option']);
    }

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
                        $_vars = func_query_column("SELECT fvariantid FROM $sql_tbl[feature_variants] WHERE foptionid IN ('".implode("','", $optionids)."')");
                        if (!empty($_vars)) {
                            db_query("DELETE FROM $sql_tbl[feature_variants] WHERE foptionid IN ('".implode("','", $optionids)."')");
                            db_query("DELETE FROM $sql_tbl[feature_variants_lng] WHERE fvariantid IN ('".implode("','", $_vars)."')");
                        }
                        db_query("DELETE FROM $sql_tbl[feature_options_lng] WHERE foptionid IN ('".implode("','", $optionids)."')");
                        db_query("DELETE FROM $sql_tbl[product_foptions] WHERE foptionid IN ('".implode("','", $optionids)."')");
                    }
                }
            }

        // Delete all old data
        } else {
            db_query("DELETE FROM $sql_tbl[product_features]");
            db_query("DELETE FROM $sql_tbl[feature_options]");
            db_query("DELETE FROM $sql_tbl[feature_options_lng]");
            db_query("DELETE FROM $sql_tbl[product_foptions]");
            db_query("DELETE FROM $sql_tbl[feature_variants]");
            db_query("DELETE FROM $sql_tbl[feature_variants_lng]");
        }

        $import_file['drop'][strtolower($section)] = '';
    }

    foreach ($data_row as $row) {

    // Import data...

        // Import product feature class options

        // Detect option
        $_optionid = false;
        if (!empty($row['optionid']))
            $_optionid = func_query_first_cell("SELECT foptionid FROM $sql_tbl[feature_options] WHERE fclassid = '$row[fclassid]' AND foptionid = '$row[optionid]'");
        if (empty($_optionid) && empty($row['optionid']))
            $_optionid = func_query_first_cell("SELECT foptionid FROM $sql_tbl[feature_options] WHERE fclassid = '$row[fclassid]' AND option_name = '".addslashes($row['option'])."'");

        $data = func_import_define_data($row, array("fclassid", 'option' => 'option_name', 'type' => 'option_type', 'format', 'avail' ,'orderby', 'option_hint', 'show_in_search'));

        unset($data['variants']);

        // Add option
        if (empty($_optionid)) {
            if (!empty($row['optionid']) && func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[feature_options] WHERE foptionid = '$row[optionid]'") == 0)
                $data['foptionid'] = $row['optionid'];

            $_optionid = func_array2insert('feature_options', $data);
            $result[strtolower($section)]['added']++;

        // Update option
        } else {
            func_array2update('feature_options', $data, "foptionid = '$_optionid'");
            $result[strtolower($section)]['updated']++;
        }

        if (!empty($_optionid)) {
            if (!empty($row['variants']) && in_array($row['type'], array("S","M"))) {
                db_query("DELETE FROM $sql_tbl[product_foptions] WHERE foptionid='$_optionid'");
                func_remove_feature_variants($_optionid);

                if (!is_array($row['variants']))
                    $row['variants'] = array($row['variants']);
                foreach ($row['variants'] as $v)
                    func_add_feature_variant($_optionid, $v, $shop_language);
            }

            $_fclass = func_query_first_cell("SELECT class FROM $sql_tbl[feature_classes] WHERE fclassid = '$data[fclassid]'");
            func_import_save_cache('FO', $_fclass."\n".$data['option_name'], $_optionid);
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
        $mrow = func_query("SELECT $sql_tbl[feature_classes].class, $sql_tbl[feature_options].* FROM $sql_tbl[feature_classes], $sql_tbl[feature_options] WHERE $sql_tbl[feature_options].fclassid = $sql_tbl[feature_classes].fclassid AND $sql_tbl[feature_classes].fclassid = '$id'".(empty($provider_sql) ? "" : " AND $sql_tbl[feature_classes].provider = '$provider_sql'"));
        if (!$mrow)
            continue;

        foreach ($mrow as $row) {
            $row = func_export_rename_cell($row, array('option_name' => 'option', 'option_type' => 'type', 'foptionid' => 'optionid'));

            // Define service array for variants column
            if ($row['type'] == "S" || $row['type'] == "M") {
                $row['variants'] = func_query_column("SELECT variant_name FROM $sql_tbl[feature_variants] LEFT JOIN $sql_tbl[feature_variants_lng] ON $sql_tbl[feature_variants].fvariantid=$sql_tbl[feature_variants_lng].fvariantid AND code='$shop_language' WHERE foptionid='$row[optionid]'");
            }

            // Write row
            if (!func_export_write_row($row))
                break;
        }
    }

}

?>
