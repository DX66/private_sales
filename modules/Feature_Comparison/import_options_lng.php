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
 * Import/export international descriptions for comparison options
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import_options_lng.php,v 1.25.2.1 2011/01/10 13:11:56 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/******************************************************************************
Used cache format:
Product class option:
    data_type:     FO
    key:        <Option ID>
    value:        [<Class ID> | RESERVED]

Note: RESERVED is used if ID is unknown
******************************************************************************/

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if ($import_step == 'process_row') {
/**
 * PROCESS ROW from import file
 */

    // Check option name
    foreach ($values['option'] as $k => $v) {
        if (empty($v))
            continue;
        $_optionid = func_import_get_cache('FO', $values['class']."\n".$v);
        if (is_null($_optionid) && $import_file['drop']['product_classes'] != 'Y') {
            $_optionid = func_query_first_cell("SELECT foptionid FROM $sql_tbl[feature_options] WHERE option_name = '".addslashes($v)."'");
            if (empty($_optionid)) {
                $_optionid = NULL;
            } else {
                func_import_save_cache('FO', $values['class']."\n".$v, $_optionid);
            }
        }

        if (is_null($_optionid) || ($action == 'do' && empty($_optionid))) {
            func_import_module_error('msg_err_import_log_message_27');
            continue;
        }
        $values['optionid'][$k] = $_optionid;
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
                    $optionids = func_query_column("SELECT foptionid FROM $sql_tbl[feature_options] WHERE fclassid = '$fclassid'");
                    if (!empty($optionids)) {
                        db_query("DELETE FROM $sql_tbl[feature_options_lng] WHERE foptionid IN ('".implode("','", $optionids)."')");
                    }
                }
            }

        // Delete all old data
        } else {
            db_query("DELETE FROM $sql_tbl[feature_options_lng]");
        }

        $import_file['drop'][strtolower($section)] = '';
    }

    foreach ($data_row as $row) {

    // Import data...

        // Import product feature class options languages

        foreach ($row['code'] as $k => $v) {
            if (empty($v) || empty($row['option_name'][$k]) || empty($row['optionid'][$k]))
                continue;
            $v = addslashes($v);

            $data = array(
                'option_name'    => addslashes($row['option_name'][$k]),
                'option_hint'    => addslashes($row['option_hint'][$k])
            );

            // Add data
            if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[feature_options_lng] WHERE foptionid = '".$row['optionid'][$k]."' AND code = '$v'") == 0) {
                $data['foptionid']    = $row['optionid'][$k];
                $data['code']        = $v;
                func_array2insert('feature_options_lng', $data, true);
                $result[strtolower($section)]['added']++;

            // Update data
            } else {
                func_array2update('feature_options_lng', $data, "foptionid = '".$row['optionid'][$k]."' AND code = '$v'");
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
        $mrow = func_query("SELECT $sql_tbl[feature_options_lng].code, $sql_tbl[feature_options_lng].option_name, $sql_tbl[feature_options_lng].option_hint, $sql_tbl[feature_classes].class, $sql_tbl[feature_options].option_name as 'option', $sql_tbl[feature_options].option_hint as hint FROM $sql_tbl[feature_classes], $sql_tbl[feature_options], $sql_tbl[feature_options_lng] WHERE $sql_tbl[feature_options_lng].foptionid = $sql_tbl[feature_options].foptionid AND $sql_tbl[feature_options].fclassid = $sql_tbl[feature_classes].fclassid AND $sql_tbl[feature_classes].fclassid = '$id' AND $sql_tbl[feature_options_lng].code = '$current_code'".(empty($provider_sql) ? "" : " AND $sql_tbl[feature_classes].provider = '$provider_sql'"));
        if (empty($mrow))
            continue;

        foreach ($mrow as $row) {

            // Write row
            if (!func_export_write_row($row))
                break;
        }
    }

}

?>
