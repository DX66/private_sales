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
 * International descriptions for slots 
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import_slots_lng.php,v 1.18.2.1 2011/01/10 13:11:59 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/******************************************************************************
Used cache format:
Wizards step slots:
    data_type:  Sl
    key:        <Slot ID>
    value:      <Slot ID>

Note: RESERVED is used if ID is unknown
******************************************************************************/

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if ($import_step == 'process_row') {
/**
 * PROCESS ROW from import file
 */

    // Check slotid
    $slotid = func_import_get_cache('Sl', $values['slotid']);
    if (is_null($slotid) && $import_file['drop']['product_configurator_steps'] != 'Y') {
        $slotid = func_query_first_cell("SELECT $sql_tbl[pconf_slots].slotid FROM $sql_tbl[pconf_slots], $sql_tbl[pconf_wizards], $sql_tbl[products] WHERE $sql_tbl[pconf_slots].stepid = $sql_tbl[pconf_wizards].stepid AND $sql_tbl[pconf_wizards].productid = $sql_tbl[products].productid AND $sql_tbl[products].provider = '".addslashes($import_data_provider)."' AND $sql_tbl[pconf_slots].slotid = '$values[slotid]'");
        if (empty($slotid)) {
            $slotid = NULL;

        } else {
            func_import_save_cache('Sl', $values['slotid'], $values['slotid'], true);
        }
    }

    if (is_null($slotid) || ($action == 'do' && empty($slotid))) {
        func_import_module_error('msg_err_import_log_message_49');
        return false;
    }

    $values['slotid'] = $slotid;

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
                    $slotids = func_query_column("SELECT $sql_tbl[pconf_slots].slotid FROM $sql_tbl[pconf_slots], $sql_tbl[pconf_wizards] WHERE $sql_tbl[pconf_slots].stepid = $sql_tbl[pconf_wizards].stepid AND $sql_tbl[pconf_wizards].productid = '$productid'");
                    if (!empty($slotids)) {
                        foreach ($slotids as $_id) {
                            db_query("DELETE FROM $sql_tbl[languages_alt] WHERE name LIKE '".$language_var_names["slot_name"].$_id."' OR name LIKE '".$language_var_names["slot_descr"].$_id."'");
                        }
                    }
                }
            }
        } else {
            db_query("DELETE FROM $sql_tbl[languages_alt] WHERE name LIKE '".$language_var_names["slot_name"]."%' OR name LIKE '".$language_var_names["slot_descr"]."%'");
        }

        $import_file['drop'][strtolower($section)] = '';
    }

    foreach ($data_row as $row) {
        $slotid = $row['slotid'];
        foreach ($row['code'] as $k => $ccode) {
            if (empty($row['slot'][$k]) && empty($row['descr'][$k])) {
                db_query("DELETE FROM $sql_tbl[languages_alt] WHERE name LIKE '".$language_var_names["slot_name"].$slotid."' OR name LIKE '".$language_var_names["slot_descr"].$slotid."'");
                continue;
            }

            if (!empty($row['slot'][$k]))
                func_languages_alt_insert($language_var_names['slot_name'].$slotid, addslashes($row['slot'][$k]), $ccode);

            if (!empty($row['descr'][$k]))
                func_languages_alt_insert($language_var_names['slot_descr'].$slotid, addslashes($row['descr'][$k]), $ccode);

            $result[strtolower($section)]['added']++;
        }

        func_flush(". ");

    }

} elseif ($import_step == 'export') {

    while ($slotid = func_export_get_row($data)) {
        if (empty($slotid))
            continue;

        // Get data
        $mrow = func_query("SELECT $sql_tbl[pconf_slots].slotid FROM $sql_tbl[pconf_slots], $sql_tbl[pconf_wizards], $sql_tbl[products] WHERE $sql_tbl[pconf_slots].stepid = $sql_tbl[pconf_wizards].stepid AND $sql_tbl[pconf_wizards].productid = $sql_tbl[products].productid AND $sql_tbl[pconf_slots].slotid = '$slotid'".(empty($provider_sql) ? "" : " AND $sql_tbl[products].provider = '$provider_sql'"));
        if (empty($mrow))
            continue;

        $row = array(
            'code' => $current_code,
            'slotid' => $slotid,
            'slot' => func_get_languages_alt($language_var_names['slot_name'].$slotid, $current_code),
            'descr' => func_get_languages_alt($language_var_names['slot_descr'].$slotid, $current_code)
        );

        if (!func_export_write_row($row))
            break;
    }
}

?>
