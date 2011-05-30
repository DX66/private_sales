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
 * Import/export products interbational descriptions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import_products_lng.php,v 1.31.2.1 2011/01/10 13:11:49 ferz Exp $
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

Note: RESERVED is used if ID is unknown
******************************************************************************/

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if (!defined('IMPORT_PRODUCTS_LNG')) {
/**
 * Make default definitions (only on first inclusion!)
 */
    define('IMPORT_PRODUCTS_LNG', 1);
    $modules_import_specification['MULTILANGUAGE_PRODUCTS'] = array(
        'script'        => '/include/import_products_lng.php',
        'permissions'   => 'AP',
        'need_provider' => true,
        'is_language'   => true,
        'export_sql'    => "SELECT productid FROM $sql_tbl[products_lng] WHERE code = '{{code}}'",
        'table'         => 'products_lng',
        'key_field'     => 'productid',
        'parent'        => 'PRODUCTS',
        'columns'       => array(
            'productid'     => array(
                'type'      => 'N',
                'is_key'    => true,
                'default'   => 0),
            'productcode'   => array(
                'is_key'    => true),
            'product'       => array(
                'is_key'    => true),
            'code'          => array(
                'array'     => true,
                'type'      =>    'C',
                'required'  => true),
            'product_name'  => array(
                'array'     => true),
            'descr'         => array(
                'eol_safe'  => true,
                'array'     => true),
            'fulldescr'     => array(
                'eol_safe'  => true,
                'array'     => true),
            'keywords'      => array(
                'array'     => true)
        )
    );
}

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
    $values['lbls'] = array();
    foreach ($values['code'] as $k => $v) {
        if (empty($values['product_name'][$k]) && empty($values['descr'][$k]) && empty($values['fulldescr'][$k]) && empty($values['keywords'][$k]))
            continue;
        if (!func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[languages] WHERE code = '$v'"))
            continue;
        $values['lbls'][$v] = array(
            'product'    => $values['product_name'][$k],
            'descr'        => $values['descr'][$k],
            'keywords'    => $values['keywords'][$k],
            'fulldescr'    => $values['fulldescr'][$k]);
    }
    unset($values['code']);

    $data_row[] = $values;

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
                    db_query("DELETE FROM $sql_tbl[products_lng] WHERE productid = '$value[productid]'");
                }
            }
        }
        else {
        // Delete all products and related information...
            db_query("DELETE FROM $sql_tbl[products_lng]");
        }

        $import_file['drop'][strtolower($section)] = '';
    }

    foreach ($data_row as $row) {

    // Import data...

        // Import multilanguage product labels
        foreach ($row['lbls'] as $k => $v) {

            // Delete old data
            $tmp = func_import_get_cache('DP', $row['productid']);
            $is_new = true;
            if (strpos($tmp, 'L'.strtolower($k)) === false) {
                db_query("DELETE FROM $sql_tbl[products_lng] WHERE productid = '$row[productid]' AND code = '$k'");
                if (db_affected_rows() > 0)
                    $is_new = false;
                func_import_save_cache('DP', $row['productid'], $tmp."L".strtolower($k));
            }

            $data = $v;
            $data['productid'] = $row['productid'];
            $data['code'] = $k;
            if (!$user_account['allow_active_content']) {
                foreach ($data as $key => $item)
                    $v = $data[$key] = func_xss_free($data[$key]);
            }
            func_array2insert('products_lng', func_addslashes($data), true);
            if ($k == $config['default_admin_language']) {
                func_array2update('products', func_addslashes($v), "productid = '".$row['productid']."'");
            }

            if ($is_new) {
                $result[strtolower($section)]['added']++;
            } else {
                $result[strtolower($section)]['updated']++;
            }
        }

        func_flush(". ");

    }

} elseif ($import_step == 'export') {

    while ($productid = func_export_get_row($data)) {
        if (empty($productid))
            continue;

        // Get product signature
        $p_row = func_export_get_product($productid);
        if (empty($p_row))
            continue;

        $row = func_query_first("SELECT * FROM $sql_tbl[products_lng] WHERE productid = '$productid' AND code = '$current_code'");
        if (empty($row))
            continue;

        $row['product_name'] = $row['product'];
        $row['productcode'] = $p_row['productcode'];

        func_unset($row, 'product');

        if (!func_export_write_row($row))
            break;
    }
}

?>
