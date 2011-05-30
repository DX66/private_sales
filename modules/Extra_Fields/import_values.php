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
 * Import/export proucts extra fields values
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import_values.php,v 1.22.2.1 2011/01/10 13:11:56 ferz Exp $
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

Extra fields (by Service names):
    data_type:  EN
    key:        <Service name>
    value:        [<Field ID> | RESERVED]

Note: RESERVED is used if ID is unknown
******************************************************************************/

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

$provider_condition = ($single_mode ? '' : " AND provider = '".$import_data_provider."'");

if ($import_step == 'process_row') {
/**
 * PROCESS ROW from import file
 */

    // Check productid / productcode / product
    list($_productid, $_variantid) = func_import_detect_product($values);
    if (is_null($_productid) || ($action == 'do' && empty($_productid))) {
        func_import_error('msg_err_import_log_message_14');
        return false;
    }

    foreach ($values as $n => $v) {
        if (in_array($n, array('productid', 'productcode', 'product')) || zerolen($n))
            continue;

        // Check column name as service name of extra field
        $_fieldid = func_import_get_cache('EN', $n);
        if (is_null($_fieldid)) {
            $_fieldid = func_query_first_cell("SELECT fieldid FROM $sql_tbl[extra_fields] WHERE service_name = '".addslashes($n)."'".$provider_condition);
            if (empty($_fieldid)) {
                $_fieldid = NULL;
            } elseif ($action == 'do') {
                func_import_save_cache('EN', $n, $_fieldid);
            }
            if (is_null($_fieldid) || ($action == 'do' && empty($_fieldid))) {
                func_import_error('msg_err_import_log_message_41', array('sname' => $n));
                return false;
            }
        }
    }

    $values['productid'] = $_productid;

    $data_row[] = $values;

}
elseif ($import_step == 'finalize') {
/**
 * FINALIZE rows processing: update database
 */

    if ($import_file['drop'][strtolower($section)] == 'Y') {
        if ($provider_condition) {

            // Delete data by provider
            $ids = db_query("SELECT fieldid FROM $sql_tbl[extra_fields] WHERE 1 ".$provider_condition);
            if ($ids) {
                while ($value = db_fetch_array($ids)) {
                    db_query("DELETE FROM $sql_tbl[extra_field_values] WHERE fieldid = '$value[fieldid]'");
                }
                db_free_result($ids);
            }
        }
        else {

            // Delete all data
            db_query("DELETE FROM $sql_tbl[extra_field_values]");
        }

        $import_file['drop'][strtolower($section)] = '';
    }

    foreach ($data_row as $row) {

        // Import data...

        foreach ($row as $n => $v) {
            if (in_array($n, array('productid', 'productcode', 'product')) || zerolen($n))
                continue;

            // Get field ID
            $_fieldid = func_import_get_cache('EN', $n);
            if (empty($_fieldid))
                continue;

            if (!$user_account['allow_active_content'])
                $v = func_xss_free($v);

            $data = array(
                'productid'    => $row['productid'],
                'fieldid'    => $_fieldid,
                'value'        => addslashes($v),
            );

            $is_new = (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[extra_field_values] WHERE productid = '$row[productid]' AND fieldid = '$_fieldid'") == 0);
            func_array2insert('extra_field_values', $data, true);

            if ($is_new) {
                $result[strtolower($section)]['added']++;
            } else {
                $result[strtolower($section)]['updated']++;
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
        $mrow = func_query_hash("SELECT $sql_tbl[extra_fields].service_name, $sql_tbl[extra_field_values].value FROM $sql_tbl[extra_field_values], $sql_tbl[extra_fields], $sql_tbl[products] WHERE $sql_tbl[extra_field_values].fieldid = $sql_tbl[extra_fields].fieldid AND $sql_tbl[extra_field_values].productid = $sql_tbl[products].productid AND $sql_tbl[products].productid = '$id'".(empty($provider_sql) ? "" : " AND $sql_tbl[products].provider = '$provider_sql' AND $sql_tbl[extra_fields].provider = '$provider_sql'")." GROUP BY $sql_tbl[extra_fields].fieldid", "service_name", false, true);
        if (empty($mrow))
            continue;

        // Get product signature
        $p_row = func_export_get_product($id);
        if (empty($p_row))
            continue;

        foreach ($mrow as $sn => $ef) {
            $mrow[strtolower($sn)] = $ef;
        }
        // Write row
        if (!func_export_write_row(array_merge($p_row, $mrow)))
            break;
    }

}

?>
