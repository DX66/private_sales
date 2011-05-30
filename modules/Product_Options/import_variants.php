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
 * Product variants import/export
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import_variants.php,v 1.38.2.1 2011/01/10 13:12:00 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('backoffice');

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
Variants for rebuilding:
    data_type:  VR
    key:        <Product ID>
    value:      <Product ID>
Old variant IDs:
    data_type:  oVR
    key:        <Product ID>_<Variant code>
    value:      <Variant ID>
Variant code:
    data_type:  VC
    key:        <Variant code>
    value:      <Variant ID>

Note: RESERVED is used if ID is unknown
******************************************************************************/

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

    // Check class & option columns
    $values['optionid'] = array();
    foreach ($values['class'] as $k => $v) {
        // Check class name
        $_classid = func_import_get_pb_cache($values, 'PC', $v);
        if (is_null($_classid) && $import_file['drop']['product_options'] != 'Y') {
            $_classid = func_query_first_cell("SELECT classid FROM $sql_tbl[classes] WHERE class = '".addslashes($v)."' AND productid = '$_productid'");
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

        // Check class type
        if (!empty($_classid)) {
            $is_modifier = func_query_first_cell("SELECT is_modifier FROM $sql_tbl[classes] WHERE classid = '$_classid'");
            if (!empty($is_modifier)) {
                func_import_module_error('msg_err_import_log_message_53', array('class' => $v));
            }
        }

        // Check option name
        $_optionid = func_import_get_pb_cache($values, 'PO', $v."\n".$values['option'][$k]);
        if (is_null($_optionid) && !empty($_classid) && $import_file['drop']['product_options'] != 'Y') {
            $_optionid = func_query_first_cell("SELECT optionid FROM $sql_tbl[class_options] WHERE classid = '$_classid' AND option_name = '".addslashes($values['option'][$k])."'");
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

    $values['optionid'] = array_values($values['optionid']);
    $values['productid'] = $_productid;

    if (!empty($values['variantcode'])) {
        $tmp = func_import_get_cache('VC', $values['variantcode']);
        if (is_null($tmp))
            func_import_save_cache('VC', $values['variantcode'], '');
    }
    $data_row[] = $values;

    // Save price id
    if (empty($import_file['PV_save_priceid'])) {
        $res = db_query("SELECT $sql_tbl[pricing].productid, $sql_tbl[pricing].variantid, $sql_tbl[pricing].priceid FROM $sql_tbl[products], $sql_tbl[pricing], $sql_tbl[variants] WHERE $sql_tbl[variants].productid = $sql_tbl[products].productid AND $sql_tbl[products].productid = $sql_tbl[pricing].productid AND $sql_tbl[variants].variantid = $sql_tbl[pricing].variantid AND $sql_tbl[pricing].quantity = '1' AND $sql_tbl[pricing].membershipid = '0' ".$provider_condition);
        if ($res) {
            while ($row = db_fetch_array($res)) {
                func_import_save_cache('PP', $row['productid']."_1_0_".$row['variantid'], $row['priceid'], true);
            }
            db_free_result($res);
        }
        $import_file['PV_save_priceid'] = "Y";
    }

}
elseif ($import_step == 'finalize') {
/**
 * FINALIZE rows processing: update database
 */

    if ($import_file['drop'][strtolower($section)] == 'Y') {
        if ($provider_condition) {
            // Search for products created by provider...
            $products_to_delete = db_query("SELECT productid FROM $sql_tbl[products] WHERE 1 ".$provider_condition);
            if ($products_to_delete) {
                while ($value = db_fetch_array($products_to_delete)) {
                    $vids = func_query_column("SELECT variantid FROM $sql_tbl[variants] WHERE productid = '$value[productid]'");
                    if (!empty($vids)) {
                        func_import_save_cache_ids('oVR', "SELECT variantid, productid, productcode FROM $sql_tbl[variants] WHERE productid = '$value[productid]'");
                        db_query("DELETE FROM $sql_tbl[variants] WHERE productid = '$value[productid]'");
                        db_query("DELETE FROM $sql_tbl[variant_items] WHERE variantid IN ('".implode("','", $vids)."')");
                        db_query("DELETE FROM $sql_tbl[variant_backups] WHERE variantid IN ('".implode("','", $vids)."')");
                        db_query("DELETE FROM $sql_tbl[pricing] WHERE variantid IN ('".implode("','", $vids)."')");
                    }
                }
            }

        } else {
            // Delete all products and related information...
            func_import_save_cache_ids('oVR', "SELECT variantid, productid, productcode FROM $sql_tbl[variants]");

            db_query("DELETE FROM $sql_tbl[variants]");
            db_query("DELETE FROM $sql_tbl[variant_items]");
            db_query("DELETE FROM $sql_tbl[variant_backups]");
            db_query("DELETE FROM $sql_tbl[pricing] WHERE variantid != '0'");
        }

        $import_file['drop'][strtolower($section)] = '';
    }

    foreach ($data_row as $key=>$variant) {
        $exists_variant = func_query_first("SELECT variantid, avail, weight, productcode, def FROM $sql_tbl[variants] WHERE productid = '$variant[productid]' AND productcode = '".addslashes($variant['variantcode'])."'");

        if (!isset($exists_variant['variantid']) || $exists_variant['variantid'] <= 0)
            continue;

        if (!isset($variant['weight'])) {
            $data_row[$key]['weight'] = $exists_variant['weight'];
        }

        if (!isset($variant['avail'])) {
            $data_row[$key]['avail'] = $exists_variant['avail'];
        }

        if (!isset($variant['default'])) {
            $data_row[$key]['default'] = $exists_variant['def'];
        }

        if (!isset($variant['price'])) {
            $exists_price = func_query_first_cell("SELECT price FROM $sql_tbl[pricing] WHERE productid = '$variant[productid]' AND variantid = '$exists_variant[variantid]'");
            if (!empty($exists_price)) {
                $data_row[$key]['price'] = $exists_price;
            }
        }
    }

    foreach ($data_row as $variant) {

    // Import pricing data...

        $data = array(
            'productid'        => $variant['productid'],
            'productcode'    => addslashes($variant['variantcode']),
            'weight'        => $variant['weight'],
            'avail'            => $variant['avail'],
            'def'            => $variant['default']
        );

        // Delete old variants
        $tmp = func_import_get_cache('DP', $variant['productid']);
        if (strpos($tmp, 'V') === false) {
            $vids = func_query_column("SELECT variantid FROM $sql_tbl[variants] WHERE productid = '$variant[productid]'");
            if (!empty($vids)) {
                func_import_save_cache_ids('oVR', "SELECT variantid, productid, productcode FROM $sql_tbl[variants] WHERE productid = '$variant[productid]'");
                db_query("DELETE FROM $sql_tbl[variants] WHERE productid = '$variant[productid]'");
                db_query("DELETE FROM $sql_tbl[variant_items] WHERE variantid IN ('".implode("','", $vids)."')");
                db_query("DELETE FROM $sql_tbl[variant_backups] WHERE variantid IN ('".implode("','", $vids)."')");
                db_query("DELETE FROM $sql_tbl[pricing] WHERE productid = '$variant[productid]' AND variantid != '0'");
            }
            func_import_save_cache('DP', $variant['productid'], $tmp."V");
        }

        // Check varaintid
        $is_new = true;
        $_priceid = false;
        $saved_variantid = func_import_get_cache('oVR', array($variant['productid'], $variant['variantcode']));
        if (!empty($variant['variantid'])) {
            $_priceid = func_import_get_cache('PP', array($data['productid'], "1", "0", $variant['variantid']));
            if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[variants] WHERE variantid = '$variant[variantid]'") == 0)
                $data['variantid'] = $variant['variantid'];
            $is_new = ($saved_variantid != $variant['variantid']);

        } elseif (!empty($saved_variantid) && func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[variants] WHERE variantid = '$saved_variantid'") == 0) {
            $data['variantid'] = $saved_variantid;
            $is_new = false;
        }

        // Check product
        $ptype = func_query_first_cell("SELECT product_type FROM $sql_tbl[products] WHERE productid = '$data[productid]'");
        if ($ptype == 'C') {
            func_import_module_error('msg_err_import_log_message_50', array('productid' => $data['productid']));
            continue;
        }

        // Check variant product code
        $variantcode = $data['productcode'];
        $last_productcode_cnt = 1;
        $productcode_long = false;
        $sku_provdier = addslashes(func_query_first_cell("SELECT provider FROM $sql_tbl[products] WHERE productid = '$data[productid]'"));
        while (
            (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[variants] WHERE productcode = '$variantcode'") > 0 ||
            func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products] WHERE productcode = '$variantcode' AND provider = '$sku_provdier'") > 0) &&
            !$productcode_long
        ) {
            $variantcode = $data['productcode'].$last_productcode_cnt++;
            if (strlen($variantcode) > 32)
                $productcode_long = true;
        }

        if ($productcode_long) {
            $variantcode = 'SKU_VAR';
            $last_productcode_cnt = 0;
            while (
                func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[variants] WHERE productcode = '$variantcode'") > 0 ||
                func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products] WHERE productcode = '$variantcode' AND provider = '$sku_provdier'") > 0
            ) {
                $variantcode = 'SKU_VAR'.$last_productcode_cnt++;
            }
        }

        $data['productcode'] = $variantcode;

        // Import variant
        $_variantid = func_array2insert('variants', $data);
        if (empty($_variantid)) {
            continue;
        } elseif ($is_new) {
            $result[strtolower($section)]['added']++;
        } else {
            $result[strtolower($section)]['updated']++;
        }

        // Import variant basic price
        if (trim($variant['price']) == "") {
            $variant['price'] = func_query_first_cell("SELECT price FROM $sql_tbl[pricing] WHERE productid = '$variant[productid]' AND variantid = '0' AND quantity = '1' AND membershipid = '0'");
        }

        $data = array(
            'productid'        => $variant['productid'],
            'variantid'        => $_variantid,
            'quantity'        => 1,
            'membershipid'    => 0,
            'price'            => $variant['price']
        );

        // Detect old price id
        if (!empty($_priceid) && func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[pricing] WHERE priceid = '$_priceid'") == 0) {
            $data['priceid'] = $_priceid;
        }

        func_array2insert('pricing', $data);

        // Import variant matrix
        if (!empty($variant['optionid'])) {
            if (!is_array($variant['optionid']))
                $variant['optionid'] = array($variant['optionid']);
            foreach ($variant['optionid'] as $k => $v) {
                $data = array(
                    'variantid'    => $_variantid,
                    'optionid'    => $v
                );
                func_array2insert('variant_items', $data);
            }
        }

        // Import variant image
        if (!empty($variant['image'])) {
            func_import_save_image_data('W', $_variantid, $variant['image']);
        }

        func_import_save_cache('VR', $variant['productid'], $variant['productid']);
        func_import_save_cache('VC', $variantcode, $_variantid);

        func_flush(". ");
    }

// Post-import step
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
        $mrow = db_query("SELECT $sql_tbl[variants].*, $sql_tbl[variants].variantid as image, $sql_tbl[pricing].price FROM $sql_tbl[products], $sql_tbl[variants], $sql_tbl[pricing] WHERE $sql_tbl[variants].productid = $sql_tbl[products].productid AND $sql_tbl[pricing].variantid = $sql_tbl[variants].variantid AND $sql_tbl[pricing].quantity = '1' AND $sql_tbl[pricing].membershipid = '0' AND $sql_tbl[variants].productid = '$id'".(empty($provider_sql) ? "" : " AND $sql_tbl[products].provider = '$provider_sql'"));
        if (!$mrow)
            continue;

        // Get product signature
        $p_row = func_export_get_product($id);
        if (empty($p_row))
            continue;

        while ($row = db_fetch_array($mrow)) {

            $row = func_export_rename_cell($row, array('def' => 'default', 'productcode' => 'variantcode'));
            $row = func_array_merge($row, $p_row);

            // Get variant items
            $items = func_query("SELECT $sql_tbl[classes].class, $sql_tbl[class_options].option_name as 'option' FROM $sql_tbl[classes], $sql_tbl[class_options], $sql_tbl[variant_items] WHERE $sql_tbl[classes].classid = $sql_tbl[class_options].classid AND $sql_tbl[class_options].optionid = $sql_tbl[variant_items].optionid AND $sql_tbl[variant_items].variantid = '$row[variantid]'");
            if (empty($items))
                continue;

            foreach ($items as $item) {
                foreach ($item as $c => $v) {
                    $row[$c][] = $v;
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
