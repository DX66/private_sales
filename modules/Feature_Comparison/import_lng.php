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
 * Import/export international descriptions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import_lng.php,v 1.24.2.1 2011/01/10 13:11:56 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/******************************************************************************
Used cache format:
Product class (by Class ID):
    data_type:     FC
    key:        <Class name>
    value:        [<Class ID> | RESERVED]

Note: RESERVED is used if ID is unknown
******************************************************************************/

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if ($import_step == 'process_row') {
/**
 * PROCESS ROW from import file
 */

    // Check fclassid
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
                    db_query("DELETE FROM $sql_tbl[feature_classes_lng] WHERE fclassid = '$fclassid'");
                }
            }

        // Delete all old data
        } else {
            db_query("DELETE FROM $sql_tbl[feature_classes_lng]");
        }

        $import_file['drop'][strtolower($section)] = '';
    }

    foreach ($data_row as $row) {

    // Import data...

        // Import product feature classes languages

        foreach ($row['code'] as $k => $v) {
            if (empty($v) || empty($row['class'][$k]))
                continue;
            $data = array(
                'fclassid'    => $row['fclassid'],
                'code'        => addslashes($v),
                'class'        => addslashes($row['class_name'][$k])
            );
            db_query("DELETE FROM $sql_tbl[feature_classes_lng] WHERE fclassid = '$row[fclassid]' AND code = '".addslashes($v)."'");
            func_array2insert('feature_classes_lng', $data, true);
            $result[strtolower($section)]['added']++;
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
        $row = func_query_first("SELECT $sql_tbl[feature_classes_lng].*, $sql_tbl[feature_classes].class, $sql_tbl[feature_classes_lng].class as class_name FROM $sql_tbl[feature_classes], $sql_tbl[feature_classes_lng] WHERE $sql_tbl[feature_classes].fclassid = $sql_tbl[feature_classes_lng].fclassid AND $sql_tbl[feature_classes_lng].code = '$current_code' AND $sql_tbl[feature_classes].fclassid = '$id'".(empty($provider_sql) ? "" : " AND provider = '$provider_sql'"));
        if (!$row)
            continue;

        // Write row
        if (!func_export_write_row($row))
            break;
    }

}

?>
