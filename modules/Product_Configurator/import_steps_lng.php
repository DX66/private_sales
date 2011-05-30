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
 * Import/export configuration steps
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import_steps_lng.php,v 1.18.2.1 2011/01/10 13:11:59 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/******************************************************************************
Used cache format:
Wizard steps (by Step ID):
    data_type:  Si
    key:        <Step ID>
    value:      <Step ID>

Note: RESERVED is used if ID is unknown
******************************************************************************/

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if ($import_step == 'process_row') {
/**
 * PROCESS ROW from import file
 */

    // Check stepid
    $_stepid = func_import_get_cache('Si', $values['stepid']);
    if (is_null($_stepid) && $import_file['drop']['product_configurator_steps'] != 'Y') {
        $_stepid = func_query_first_cell("SELECT $sql_tbl[pconf_wizards].stepid FROM $sql_tbl[pconf_wizards], $sql_tbl[products] WHERE $sql_tbl[pconf_wizards].productid = $sql_tbl[products].productid AND $sql_tbl[products].provider = '".addslashes($import_data_provider)."' AND $sql_tbl[pconf_wizards].stepid = '$values[stepid]'");
        if (empty($_stepid)) {
            $_stepid = NULL;

        } else {
            func_import_save_cache('Si', $values['stepid'], $_stepid);
        }
    }

    if (is_null($_stepid) || ($action == 'do' && empty($_stepid))) {
        func_import_module_error('msg_err_import_log_message_24');
        return false;
    }

    $values['stepid'] = $_stepid;

    $data_row[] = $values;

}
elseif ($import_step == 'finalize') {
/**
 * FINALIZE rows processing: update database
 */

    if ($import_file['drop'][strtolower($section)] == 'Y') {
        if ($provider_condition) {
            // Search for products created by provider...
            $products = db_query("SELECT productid FROM $sql_tbl[products] WHERE provider = '".addslashes($import_data_provider)."'");
            if ($products) {
                while ($productid = db_fetch_array($products)) {
                    $productid = $productid['productid'];
                    $stepids = func_query_column("SELECT stepid FROM $sql_tbl[pconf_wizards] WHERE productid = '$productid'");
                    if (!empty($stepids)) {
                        foreach ($stepids as $_id) {
                            db_query("DELETE FROM $sql_tbl[languages_alt] WHERE name LIKE '".$language_var_names["step_name"].$_id."' OR name LIKE '".$language_var_names["step_descr"].$_id."'");
                        }
                    }
                }
            }
        } else {
            db_query("DELETE FROM $sql_tbl[languages_alt] WHERE name LIKE '".$language_var_names["step_name"]."%' OR name LIKE '".$language_var_names["step_descr"]."%'");
        }

        $import_file['drop'][strtolower($section)] = '';
    }

    foreach ($data_row as $row) {
        $_stepid = $row['stepid'];
        foreach ($row['code'] as $k => $ccode) {
            if (empty($row['step'][$k]) && empty($row['descr'][$k])) {
                db_query("DELETE FROM $sql_tbl[languages_alt] WHERE name LIKE '".$language_var_names["step_name"].$_stepid."' OR name LIKE '".$language_var_names["step_descr"].$_stepid."'");
                continue;
            }

            if (!empty($row['step'][$k]))
                func_languages_alt_insert($language_var_names['step_name'].$_stepid, addslashes($row['step'][$k]), $ccode);

            if (!empty($row['descr'][$k]))
                func_languages_alt_insert($language_var_names['step_descr'].$_stepid, addslashes($row['descr'][$k]), $ccode);

            $result[strtolower($section)]['added']++;
        }

        func_flush(". ");

    }

} elseif ($import_step == 'export') {

    while ($_stepid = func_export_get_row($data)) {
        if (empty($_stepid))
            continue;

        // Get data
        $mrow = func_query("SELECT $sql_tbl[pconf_wizards].stepid FROM $sql_tbl[pconf_wizards], $sql_tbl[products] WHERE $sql_tbl[pconf_wizards].productid = $sql_tbl[products].productid AND $sql_tbl[pconf_wizards].stepid = '$_stepid'".(empty($provider_sql) ? "" : " AND $sql_tbl[products].provider = '$provider_sql'"));
        if (empty($mrow))
            continue;

        $row = array(
            'code' => $current_code,
            'stepid' => $_stepid,
            'step' => func_get_languages_alt($language_var_names['step_name'].$_stepid, $current_code),
            'descr' => func_get_languages_alt($language_var_names['step_descr'].$_stepid, $current_code)
        );

        if (!func_export_write_row($row))
            break;
    }
}

?>
