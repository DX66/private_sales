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
 * Import/export Feature comparison module related data
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import.php,v 1.26.2.1 2011/01/10 13:11:56 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('backoffice');

/******************************************************************************
Used cache format:
Product class:
    data_type:  FC
    key:        <Class name>
    value:      [<Class ID> | RESERVED]

Note: RESERVED is used if ID is unknown
******************************************************************************/

if ($import_step == 'process_row') {
/**
 * PROCESS ROW from import file
 */

    // Check fclass name
    $_fclassid = func_import_get_cache('FC', $values['class']);
    if (is_null($_fclassid)) {
        func_import_save_cache('FC', $values['class']);
    }

    $data_row[] = $values;

} elseif ($import_step == 'finalize') {
/**
 * FINALIZE rows processing: update database
 */

    // Drop old data
    if ($import_file['drop'][strtolower($section)] == 'Y') {

        func_import_save_image('F');
        // Delete data by provider
        if ($provider_condition) {
            $fclasses = db_query("SELECT fclassid FROM $sql_tbl[feature_classes] WHERE provider = '".addslashes($import_data_provider)."'");
            if ($fclasses) {
                db_query("DELETE FROM $sql_tbl[feature_classes] WHERE provider = '".addslashes($import_data_provider)."'");
                while ($fclassid = db_fetch_array($fclasses)) {
                    $fclassid = $fclassid['fclassid'];
                    db_query("DELETE FROM $sql_tbl[feature_classes_lng] WHERE fclassid = '$fclassid'");
                    db_query("DELETE FROM $sql_tbl[product_features] WHERE fclassid = '$fclassid'");
                    $optionids = func_query_column("SELECT foptionid FROM $sql_tbl[feature_options] WHERE fclassid = '$fclassid'");
                    if (!empty($optionids)) {
                        db_query("DELETE FROM $sql_tbl[feature_options] WHERE fclassid = '$fclassid'");
                        db_query("DELETE FROM $sql_tbl[feature_options_lng] WHERE foptionid IN ('".implode("','", $optionids)."')");
                        db_query("DELETE FROM $sql_tbl[product_foptions] WHERE foptionid IN ('".implode("','", $optionids)."')");
                    }
                }
            }

        // Delete all old data
        } else {
            db_query("DELETE FROM $sql_tbl[feature_classes]");
            db_query("DELETE FROM $sql_tbl[feature_classes_lng]");
            db_query("DELETE FROM $sql_tbl[product_features]");
            db_query("DELETE FROM $sql_tbl[feature_options]");
            db_query("DELETE FROM $sql_tbl[feature_options_lng]");
            db_query("DELETE FROM $sql_tbl[product_foptions]");
        }

        $import_file['drop'][strtolower($section)] = '';
    }

    foreach ($data_row as $row) {

    // Import data...

        // Import product feature classes

        // Detect fclassid
        $_fclassid = false;
        if (!empty($row['fclassid'])) {
            $_tmp = func_query_first("SELECT fclassid, provider FROM $sql_tbl[feature_classes] WHERE fclassid = '$row[fclassid]'");
            if ($_tmp && $import_data_provider && $_tmp['provider'] && $_tmp['provider'] != $import_data_provider) {
                $_ut = false;

                if (constant('AREA_TYPE') == 'A')
                    $_ut = func_query_first_cell("SELECT usertype FROM $sql_tbl[customers] WHERE id = '" . $_tmp['provider'] . "'");

                if ($_ut != 'A')
                    $_tmp = false;
            }

            if ($_tmp)
                $_fclassid = $_tmp['fclassid'];
        }

        $data = func_import_define_data($row, array('class', 'avail', 'orderby'));

        // Add class
        if (empty($_fclassid)) {
            if (!empty($row['fclassid']) && func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[feature_classes] WHERE fclassid = '$row[fclassid]'") == 0)
                $data['fclassid'] = $row['fclassid'];
            $data['provider'] = (empty($import_data_provider) ? $logged_userid : addslashes($import_data_provider));
            $_fclassid = func_array2insert('feature_classes', $data);
            $result[strtolower($section)]['added']++;

        // Update class
        } else {
            func_array2update('feature_classes', $data, "fclassid = '$_fclassid'");
            $result[strtolower($section)]['updated']++;
        }

        if (empty($_fclassid))
            continue;

        func_import_save_cache('FC', $row['class'], $_fclassid);

        // Import image
        if (!empty($row['image'])) {
            func_import_save_image_data('F', $_fclassid, $row['image']);
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
        $row = func_query_first("SELECT * FROM $sql_tbl[feature_classes] WHERE fclassid = '$id'".(empty($provider_sql) ? "" : " AND provider = '$provider_sql'"));
        if (!$row)
            continue;

        // Export image
        $row['image'] = $row['fclassid'];

        // Write row
        if (!func_export_write_row($row))
            break;
    }

}

?>
