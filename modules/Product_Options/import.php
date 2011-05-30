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
 * Options import/export
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import.php,v 1.35.2.1 2011/01/10 13:12:00 ferz Exp $
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
    data_type:  DP
    key:        <Product ID>
    value:      <Flags>
Deleted product option class:
    data_type:  oO
    key:        <Product ID>_<Class name>
    value:      <Class ID>
Variants for rebuilding:
    data_type:  VR
    key:        <Product ID>
    value:      <Product ID>

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

    $_classid = func_import_get_pb_cache($values, 'PC', $values['class'], true);

    // Check option id
    if (!empty($values['optionid'])) {
        foreach ($values['optionid'] as $v) {
            $tmp = func_import_get_cache('OI', $v);
            if (is_null($tmp))
                func_import_save_cache('OI', $v);
        }
    }

    // Check option name
    if (!empty($values['option'])) {
        foreach ($values['option'] as $v) {
            func_import_get_pb_cache($values, 'PO', $values['class']."\n".$v, true);
        }
    }

    $values['productid'] = $_productid;

    $data_row[] = $values;

}
elseif ($import_step == 'finalize') {
/**
 * FINALIZE rows processing: update database
 */

    if ($import_file['drop']['product_options'] == 'Y') {
        if ($provider_condition) {
            // Search for products created by provider...
            $products_to_delete = db_query("SELECT productid FROM $sql_tbl[products] WHERE 1 ".$provider_condition);
            if ($products_to_delete) {
                while ($value = db_fetch_array($products_to_delete)) {
                    $cids = func_query_column("SELECT classid FROM $sql_tbl[classes] WHERE productid = '$value[productid]'");

                    if (!empty($cids)) {
                        func_import_save_cache_ids('oO', "SELECT classid, productid, class FROM $sql_tbl[classes] WHERE productid = '$value[productid]'");

                        db_query("DELETE FROM $sql_tbl[classes] WHERE productid = '$value[productid]'");
                        db_query("DELETE FROM $sql_tbl[class_lng] WHERE classid IN ('".implode("','", $cids)."')");
                        $oids = func_query_column("SELECT optionid FROM $sql_tbl[class_options] WHERE classid IN ('".implode("','", $cids)."')");
                        if (!empty($oids)) {
                            db_query("DELETE FROM $sql_tbl[class_options] WHERE classid IN ('".implode("','", $cids)."')");
                            db_query("DELETE FROM $sql_tbl[product_options_lng] WHERE optionid IN ('".implode("','", $oids)."')");
                            db_query("DELETE FROM $sql_tbl[variant_items] WHERE optionid IN ('".implode("','", $oids)."')");
                            db_query("DELETE FROM $sql_tbl[variant_backups] WHERE optionid IN ('".implode("','", $oids)."')");
                            db_query("DELETE FROM $sql_tbl[product_options_ex] WHERE optionid IN ('".implode("','", $oids)."')");
                        }
                    }
                }
            }

        } else {

            // Delete all products and related information...
            func_import_save_cache_ids('oO', "SELECT classid, productid, class FROM $sql_tbl[classes]");

            db_query("DELETE FROM $sql_tbl[classes]");
            db_query("DELETE FROM $sql_tbl[class_options]");
            db_query("DELETE FROM $sql_tbl[class_lng]");
            db_query("DELETE FROM $sql_tbl[product_options_lng]");
            db_query("DELETE FROM $sql_tbl[product_options_ex]");
            db_query("DELETE FROM $sql_tbl[variants]");
            db_query("DELETE FROM $sql_tbl[variant_items]");
            db_query("DELETE FROM $sql_tbl[variant_backups]");
            db_query("DELETE FROM $sql_tbl[product_options_js]");
        }

        $import_file['drop']['product_options'] = '';
    }

    foreach ($data_row as $class) {

    // Import pricing data...

        $data = array(
            'productid'        => $class['productid'],
            'class'            => addslashes($class['class']),
            'classtext'        => addslashes($class['descr']),
            'orderby'        => intval($class['orderby'])
        );

        if (isset($class['type'])) {
            $data['is_modifier'] = $class['type'];

            // Check product
            if (empty($data['is_modifier'])) {
                $ptype = func_query_first_cell("SELECT product_type FROM $sql_tbl[products] WHERE productid = '$data[productid]'");
                if ($ptype == 'C') {
                    func_import_module_error('msg_err_import_log_message_50', array('productid' => $data['productid']));
                    continue;
                }
            }
        }

        if (isset($class['avail']))
            $data['avail'] = $class['avail'];

        // Delete old product options
        $tmp = func_import_get_cache('DP', $class['productid']);
        if (strpos($tmp, 'O') === false) {
            $cids = func_query_column("SELECT classid FROM $sql_tbl[classes] WHERE productid = '$class[productid]'");
            if (!empty($cids)) {
                func_import_save_cache_ids('oO', "SELECT classid, productid, class FROM $sql_tbl[classes] WHERE productid = '$class[productid]'");
                func_import_save_cache('VR', $class['productid'], $class['productid']);
                func_delete_po_class($cids);
            }
            unset($cids);
            func_import_save_cache('DP', $class['productid'], $tmp."O");
        }

        $saved_classid = func_import_get_cache('oO', $class['productid']."_".$class['class']);
        $is_new = true;
        if (!empty($class['classid'])) {
            if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[classes] WHERE classid = '$class[classid]'") == 0)
                $data['classid'] = $class['classid'];
            $is_new = ($saved_classid != $class['classid']);

        } elseif (!empty($saved_classid)) {
            $data['classid'] = $saved_classid;
            $is_new = false;

        }

        // Import product option class
        $_classid = func_array2insert('classes', $data);
        if (empty($_classid)) {
            continue;
        } elseif ($is_new) {
            $result[strtolower($section)]['added']++;
        } else {
            $result[strtolower($section)]['updated']++;
        }
        func_import_save_pb_cache($class, 'PC', $class['class'], $_classid);

        // Import product option values
        if (!empty($class['option']) && $class['type'] != 'T') {
            if (!is_array($class['option']))
                $class['option'] = array($class['option']);
            foreach ($class['option'] as $k => $v) {
                $data = array(
                    'classid'        => $_classid,
                    'option_name'    => addslashes($v),
                    'orderby'        => $class['option_orderby'][$k]
                );
                if (isset($class['option_avail'][$k]))
                    $data['avail'] = $class['option_avail'][$k];

                if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[class_options] WHERE optionid = '".$class['optionid'][$k]."'") == 0) {
                    $data['optionid'] = $class['optionid'][$k];
                }
                if ($class['type'] == 'Y' || !isset($class['type'])) {
                    $data['price_modifier'] = $class['price_modifier'][$k];
                    $data['modifier_type'] = $class['modifier_type'][$k];
                }
                $_optionid = func_array2insert('class_options', $data);
                if (!empty($_optionid)) {
                    func_import_save_pb_cache($class, 'PO', $class['class']."\n".$v, $_optionid);
                    func_import_save_cache('OI', $_optionid, $_classid);
                }
            }

            if (empty($class['type'])) {
                func_import_save_cache('VR', $class['productid'], $class['productid']);
            }
        }

        echo ". ";
        func_flush();

    }

} elseif ($import_step == 'complete') {

    $is_display_header = false;
    while (list($pid, $tmp) = func_import_read_cache('VR')) {
        if (!$is_display_header) {
            $message = func_get_langvar_by_name('lbl_product_variants_rebuilding_',NULL,false,true);
            func_import_add_to_log($message);
            func_flush("<br />\n".$message."<br />\n");
            $is_display_header = true;
        }
        func_rebuild_variants($pid, true, 0, false);
        func_import_rebuild_product($pid);

        func_flush(". ");
    }
    func_import_erase_cache('VR');

// Export data
} elseif ($import_step == 'export') {

    while (($id = func_export_get_row($data)) !== false) {
        if (empty($id))
            continue;

        // Get data
        $mrow = db_query("SELECT $sql_tbl[classes].* FROM $sql_tbl[classes], $sql_tbl[products] WHERE $sql_tbl[classes].productid = $sql_tbl[products].productid AND $sql_tbl[classes].productid = '$id'".(empty($provider_sql) ? "" : " AND $sql_tbl[products].provider = '$provider_sql'") . " ORDER BY $sql_tbl[classes].orderby");
        if (!$mrow)
            continue;

        // Get product signature
        $p_row = func_export_get_product($id);
        if (empty($p_row))
            continue;

        while ($row = db_fetch_array($mrow)) {
            $row = func_array_merge($row, $p_row);
            $row = func_export_rename_cell($row, array('classtext' => 'descr', 'is_modifier' => 'type'));

            // Export options
            if ($row['type'] != "T") {
                $options = func_query("SELECT * FROM $sql_tbl[class_options] WHERE classid = '$row[classid]' ORDER BY orderby,optionid");
                if (!empty($options)) {
                    foreach ($options as $v) {
                        $row['optionid'][] = $v['optionid'];
                        $row['option'][] = $v['option_name'];
                        if ($row['type'] == "Y") {
                            $row['price_modifier'][] = $v['price_modifier'];
                            $row['modifier_type'][] = $v['modifier_type'];
                        } else {
                            $row['price_modifier'][] =  0;
                            $row['modifier_type'][] =  "";
                        }
                        $row['option_orderby'][] = $v['orderby'];
                        $row['option_avail'][] = $v['avail'];
                    }
                }
            }

            // Write row
            if (!func_export_write_row($row))
                break;
        }
        db_free_result($mrow);
    }

}

?>
