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
 * Exceptions import/export
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import_ex.php,v 1.27.2.1 2011/01/10 13:12:00 ferz Exp $
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
Product options (classes) by Product ID / Product code / Product name:
    data_type:  PC
    key:        <Product ID / Product code / Product name>EOL<Class>
    value:      [<Class ID> | RESERVED]
Product options (values) by Product ID / Product code / Product name:
    data_type:  PO
    key:        <Product ID / Product code / Product name>EOL<Class>EOL<Option>
    value:      [<Option ID> | RESERVED]
Product options (values - by Option ID):
    data_type:  OI
    key:        <Option ID>
    value:      [<Class ID> | RESERVED]
Deleted product data:
    data_type:    DP
    key:        <Product ID>
    value:        <Flags>

Note: RESERVED is used if ID is unknown
******************************************************************************/

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

$provider_condition = ($single_mode ? '' : " AND $sql_tbl[products].provider='".$import_data_provider."'");

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

    // Data normalize
    if (!is_array($values['class']))
        $values['class'] = array($values['class']);
    if (!is_array($values['option']))
        $values['option'] = array($values['option']);
    if (!is_array($values['optionid']))
        $values['optionid'] = array($values['optionid']);

    // Check optionid
    foreach ($values['optionid'] as $k => $v) {
        if (empty($v)) {
            unset($values['optionid'][$k]);
            continue;
        }
        $_classid = func_import_get_cache('OI', $v);
        if (is_null($_classid) && $import_file['drop']['product_options'] != 'Y') {
            $_classid = func_query_first_cell("SELECT classid FROM $sql_tbl[class_options] WHERE optionid = '$v'");
            if (empty($_classid)) {
                $_classid = NULL;
            } elseif ($action == 'do') {
                func_import_save_cache('OI', $v, $_classid);
            }
        }
        if (is_null($_classid) || ($action == 'do' && empty($_classid))) {
            func_import_module_error('msg_err_import_log_message_15', array('optionid' => $v));
            unset($values['optionid'][$k]);
        }
    }

    // Check class & option columns
    if (!empty($values['class']) && !empty($values['option'])) {
        foreach ($values['class'] as $k => $v) {
            if (empty($v) || empty($values['option'][$k]) || isset($values['optionid'][$k]))
                continue;

            // Check class name
            $_classid = func_import_get_pb_cache($values, 'PC', $v);
            if (is_null($_classid) && $import_file['drop']['product_options'] != 'Y') {
                $_classid = func_query_first_cell("SELECT classid FROM $sql_tbl[classes] WHERE class = '".addslashes($v)."'");
                if (empty($_classid)) {
                    $_classid = NULL;
                } elseif ($action == 'do') {
                    func_import_save_pb_cache($values, 'PC', $v, $_classid);
                }
            }
            if (is_null($_classid) || ($action == 'do' && empty($_classid))) {
                func_import_module_error('msg_err_import_log_message_16', array('class' => $v));
                unset($values['class'][$k], $values['option'][$k]);
            }

            // Check option name
            $_optionid = func_import_get_pb_cache($values, 'PO', $v."\n".$values['option'][$k]);
            if (is_null($_optionid) && !empty($_classid) && $import_file['drop']['product_options'] != 'Y') {
                $_optionid = func_query_first_cell("SELECT optionid FROM $sql_tbl[class_options] WHERE classid = '$_classid' AND optionid = '".addslashes($values['option'][$k])."'");
                if (empty($_optionid)) {
                    $_optionid = NULL;
                } elseif ($action == 'do') {
                    func_import_save_pb_cache($values, 'PO', $v."\n".$values['option'][$k], $_optionid);
                }
            }
            if (is_null($_optionid) || ($action == 'do' && empty($_optionid))) {
                func_import_module_error('msg_err_import_log_message_17', array('option' => $values['option'][$k]));
                unset($values['class'][$k], $values['option'][$k]);
            }

            $values['optionid'][] = $_optionid;
        }
    }
    $values['optionid'] = array_values($values['optionid']);

    $data_row[] = $values;

}
elseif ($import_step == 'finalize') {
/**
 * FINALIZE rows processing: update database
 */

    if ($import_file['drop']['product_option_exceptions'] == 'Y') {
        if ($provider_condition) {
            // Search for products created by provider...
            $products_to_delete = db_query("SELECT productid FROM $sql_tbl[products] WHERE 1 ".$provider_condition);
            if ($products_to_delete) {
                while ($value = db_fetch_array($products_to_delete)) {
                    $oids = func_query_column("SELECT $sql_tbl[class_options].optionid FROM $sql_tbl[classes], $sql_tbl[class_options] WHERE $sql_tbl[classes].classid = $sql_tbl[class_options].classid AND $sql_tbl[classes].productid = '$value[productid]'");
                    if (!empty($oids)) {
                        db_query("DELETE FROM $sql_tbl[product_options_ex] WHERE optionid IN ('".implode("','", $oids)."')");
                    }
                }
            }
        }
        else {
        // Delete all products and related information...
            db_query("DELETE FROM $sql_tbl[product_options_ex]");
        }

        $import_file['drop']['product_option_exceptions'] = '';
    }

    foreach ($data_row as $ex) {

    // Import pricing data...

        $exid = $ex['exceptionid'];
        $is_new = false;
        if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[product_options_ex] WHERE exceptionid = '$exid'")) {
            $exid = func_query_first_cell("SELECT MAX(exceptionid) FROM $sql_tbl[product_options_ex]")+1;
            $is_new = true;
        }
        if (empty($ex['optionid']))
            continue;

        // Delete old exceptions
        $tmp = func_import_get_cache('DP', $ex['productid']);
        if (strpos($tmp, 'E') === false) {
            $oids = func_query_column("SELECT $sql_tbl[class_options].optionid FROM $sql_tbl[classes], $sql_tbl[class_options] WHERE $sql_tbl[classes].classid = $sql_tbl[class_options].classid AND $sql_tbl[classes].productid = '$ex[productid]'");
            if (!empty($oids)) {
                db_query("DELETE FROM $sql_tbl[product_options_ex] WHERE optionid IN ('".implode("','", $oids)."')");
            }
            func_import_save_cache('DP', $ex['productid'], $tmp."E");
        }

        // Import exception element
        foreach ($ex['optionid'] as $v) {
            func_array2insert('product_options_ex', array('exceptionid' => $exid, 'optionid' => $v));
        }

        if ($is_new) {
            $result[strtolower($section)]['added']++;
        } else {
            $result[strtolower($section)]['updated']++;
        }

        func_flush(". ");

    }

// Export data
} elseif ($import_step == 'export') {

    while (($id = func_export_get_row($data)) !== false) {
        if (empty($id))
            continue;

        // Get data
        $mrow = func_query("SELECT $sql_tbl[class_options].optionid, $sql_tbl[classes].class, $sql_tbl[class_options].option_name as 'option', $sql_tbl[product_options_ex].exceptionid FROM $sql_tbl[classes], $sql_tbl[products], $sql_tbl[class_options], $sql_tbl[product_options_ex] WHERE $sql_tbl[classes].classid = $sql_tbl[class_options].classid AND $sql_tbl[class_options].optionid = $sql_tbl[product_options_ex].optionid AND $sql_tbl[classes].productid = $sql_tbl[products].productid AND $sql_tbl[classes].productid = '$id'".(empty($provider_sql) ? "" : " AND $sql_tbl[products].provider = '$provider_sql'")." ORDER BY $sql_tbl[product_options_ex].exceptionid");
        if (empty($mrow))
            continue;

        // Get product signature
        $p_row = func_export_get_product($id);
        if (empty($p_row))
            continue;

        foreach ($mrow as $row) {
            $row = func_array_merge($row, $p_row);

            // Write row
            if (!func_export_write_row($row))
                break;
        }
    }

}

?>
