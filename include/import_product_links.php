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
 * Import/export upselling links
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import_product_links.php,v 1.26.2.1 2011/01/10 13:11:49 ferz Exp $
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
Deleted product data:
    data_type:    DP
    key:        <Product ID>
    value:        <Flags>
Deleted product_links:
    data_type:  oPl
    key:        <productid1>_<productid2>
    value:      <productid2>

Note: RESERVED is used if ID is unknown
******************************************************************************/

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if ($import_step == 'define') {

    $import_specification['PRODUCT_LINKS'] = array(
        'script'        => '/include/import_product_links.php',
        'permissions'   => 'AP',
        'need_provider' => true,
        'parent'        => 'PRODUCTS',
        'export_sql'    => "SELECT productid1 FROM $sql_tbl[product_links] GROUP BY productid1",
        'table'         => 'product_links',
        'key_field'     => 'productid1',
        'parent_key_field' => 'productid1',
        'columns'       => array(
            'productid'     => array(
                'type'      => 'N',
                'is_key'    => true,
                'default'   => 0),
            'productcode'   => array(
                'is_key'    => true),
            'product'       => array(
                'is_key'    => true),
            'productid_to'  => array(
                'array'     => true,
                'type'      => 'N',
                'default'   => 0),
            'productcode_to'=> array(
                'array'     => true),
            'product_to'    => array(
                'array'     => true),
            'orderby'       => array(
                'array'     => true,
                'type'      => 'N')
        )
    );

} elseif ($import_step == 'process_row') {
/**
 * PROCESS ROW from import file
 */

    // Check productid / productcode / product
    list($_productid, $_variantid) = func_import_detect_product($values);
    if (is_null($_productid) || ($action == 'do' && empty($_productid))) {
        func_import_module_error('msg_err_import_log_message_14');
        return false;
    }
    $values['productid1'] = $_productid;

    if (!is_array($values['productid_to']))
        $values['productid_to'] = array($values['productid_to']);
    if (!is_array($values['product_to']))
        $values['product_to'] = array($values['product_to']);
    if (!is_array($values['productcode_to']))
        $values['productcode_to'] = array($values['productcode_to']);
    if (!is_array($values['orderby']))
        $values['orderby'] = array($values['orderby']);

    $cnt = func_import_get_count($values);
    for ($x = 0; $x <= $cnt; $x++) {
        if (empty($values['productid_to'][$x]) && empty($values['productcode_to'][$x]) && empty($values['product_to'][$x]))
            continue;
        $tmp = array(
            'productid'        => $values['productid_to'][$x],
            'product'        => $values['product_to'][$x],
            'productcode'    => $values['productcode_to'][$x]
        );
        list($_productid, $_variantid) = func_import_detect_product($tmp);
        if (is_null($_productid) || ($action == 'do' && empty($_productid))) {
            func_import_module_error('msg_err_import_log_message_14');
            return false;
        } elseif ($values['productid1'] == $_productid && !empty($values['productid1']) && !empty($_productid)) {
            func_import_module_error('msg_err_import_log_message_33');
            return false;
        }
        $values['productid2'][$_productid] = $values['orderby'][$x];
    }

    $data_row[] = $values;

} elseif ($import_step == 'finalize') {
/**
 * FINALIZE rows processing: update database
 */

    // Drop old data
    if ($import_file['drop'][strtolower($section)] == 'Y') {

        if ($provider_condition) {
            // Search for products created by provider...
            $products_to_delete = db_query("SELECT productid FROM $sql_tbl[products] WHERE 1 ".$provider_condition);
            if ($products_to_delete) {
                while ($value = db_fetch_array($products_to_delete)) {
                    db_query("DELETE FROM $sql_tbl[product_links] WHERE productid1 = '$value[productid]' OR productid2 = '$value[productid]'");
                }
            }
        }
        else {
        // Delete all products and related information...
            db_query("DELETE FROM $sql_tbl[product_links]");
        }
        $import_file['drop'][strtolower($section)] = '';
    }

    foreach ($data_row as $row) {

    // Import data...

        // Delete old data
        $tmp = func_import_get_cache('DP', $row['productid1']);
        if (strpos($tmp, 'l') === false) {
            func_import_save_cache_ids('oPl', "SELECT productid2, productid1, productid2  FROM $sql_tbl[product_links] WHERE productid1 = '$row[productid1]'");
            db_query("DELETE FROM $sql_tbl[product_links] WHERE productid1 = '$row[productid1]'");
            func_import_save_cache('DP', $row['productid1'], $tmp."l");
        }

        // Import product links
        if (!empty($row['productid2'])) {
            foreach ($row['productid2'] as $k => $v) {

                $saved_link = func_import_get_cache('oPl', $row['productid1'].'_'.$k);
                if ($saved_link) {
                    // Update product link
                    func_array2update('product_links', array('orderby' => $v), "productid1 = '$row[productid1]' AND productid2 = '$k'");
                    $result[strtolower($section)]['updated']++;

                } else {
                    // Add product link
                    $data = array(
                        'productid1'    => $row['productid1'],
                        'productid2'    => $k,
                        'orderby'        => $v
                    );
                    func_array2insert('product_links', $data);
                    $result[strtolower($section)]['added']++;
                }
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
        $mrow = func_query("SELECT $sql_tbl[product_links].* FROM $sql_tbl[product_links], $sql_tbl[products] WHERE $sql_tbl[product_links].productid1 = $sql_tbl[products].productid AND $sql_tbl[product_links].productid1 = '$id'".(empty($provider_sql) ? "" : " AND $sql_tbl[products].provider = '$provider_sql'")." ORDER BY $sql_tbl[product_links].orderby");
        if (empty($mrow))
            continue;

        // Get product signature
        $p_row = func_export_get_product($id);
        if (empty($p_row))
            continue;

        foreach ($mrow as $row) {
            // Get product signature (destination product)
            $p2_row = func_export_get_product($row['productid2']);
            if (empty($p2_row))
                continue;

            $p_row['productid_to'][]    = $p2_row['productid'];
            $p_row['product_to'][]        = $p2_row['product'];
            $p_row['productcode_to'][]    = $p2_row['productcode'];
            $p_row['orderby'][]            = $row['orderby'];
        }

        if (empty($p_row['productid_to']))
            continue;

        // Write row
        if (!func_export_write_row($p_row))
            break;
    }

}

?>
