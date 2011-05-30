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
 * Discount coupons import/export
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import.php,v 1.24.2.1 2011/01/10 13:11:56 ferz Exp $
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
Categories:
    data_type:    C
    key:        <Category full path>
    value:        [<Category ID> | RESERVED]
Categories (by Category ID):
    data_type:    CI
    key:        <Category ID>
    value:        [<Category full path> | RESERVED]

Note: RESERVED is used if ID is unknown
******************************************************************************/

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if ($import_step == 'process_row') {
/**
 * PROCESS ROW from import file
 */

    $_categoryid = $_productid = $_variantid = false;

    if (preg_match('/' . func_coupon_validation_regexp() . '/', $values['coupon'])) {
        func_import_module_error('msg_err_import_log_message_58');
        return false;
    }

    if (!empty($values['productid']) || !empty($values['product']) || !empty($values['productcode']))
        list($_productid, $_variantid) = func_import_detect_product($values);

    if (!empty($values['categoryid']) || !empty($values['category']))
        $_categoryid = func_import_detect_category($values);

    if ($action == 'do') {
        if (!empty($_productid)) {
            $values['productid'] = $_productid;

        } elseif (!empty($_categoryid)) {
            $values['categoryid'] = $_categoryid;
        }
    }

    $data_row[] = $values;

}
elseif ($import_step == 'finalize') {
/**
 * FINALIZE rows processing: update database
 */

    if ($import_file['drop'][strtolower($section)] == 'Y') {
        if ($provider_condition) {
            db_query("DELETE FROM $sql_tbl[discount_coupons] WHERE provider = '".addslashes($import_data_provider)."'");
        }
        else {
            db_query("DELETE FROM $sql_tbl[discount_coupons]");
        }

        $import_file['drop'][strtolower($section)] = '';
    }

    foreach ($data_row as $row) {

    // Import data...

        if ($row['coupon_type'] == "%") {
            $row['coupon_type'] = "percent";
        } elseif ($row['coupon_type'] == "$") {
            $row['coupon_type'] = "absolute";
        } elseif (strtolower($row['coupon_type']) == "fs") {
            $row['coupon_type'] = "free_ship";
        }

        $data = array(
            'discount' => $row['discount'],
            'coupon_type' => $row['coupon_type'],
            'productid' => $row['productid'],
            'categoryid' => $row['categoryid'],
            'minimum' => $row['minimum'],
            'times' => $row['times'],
            'per_user' => $row['per_user'],
            'times_used' => $row['times_used'],
            'expire' => $row['expire'],
            'provider' => $import_data_provider,
            'apply_category_once' => $row['apply_category_once'],
            'apply_product_once' => $row['apply_product_once']
        );
        if (!empty($row['categoryid']))
            $data['recursive'] = $row['recursive'];

        // Check coupon
        $is_new = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[discount_coupons] WHERE coupon = '".addslashes($row['coupon'])."'") == 0;
        $data = func_addslashes($data);

        // Insert new coupon
        if ($is_new) {
            if (empty($row['status']))
                $row['status'] = "A";
            $data['status'] = $row['status'];
            $data['coupon'] = addslashes($row['coupon']);
            func_array2insert('discount_coupons', $data);
            $result[strtolower($section)]['added']++;

        // Update old coupon
        } else {
            func_array2update('discount_coupons', $data, "coupon = '".addslashes($row['coupon'])."'");
            $result[strtolower($section)]['updated']++;
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
        $row = func_query_first("SELECT * FROM $sql_tbl[discount_coupons] WHERE coupon = '".addslashes($id)."'".(empty($provider_sql) ? "" : " AND provider = '$provider_sql'"));
        if (empty($row))
            continue;

        $c_row = func_export_get_category($row['categoryid']);
        if (!empty($c_row))
            $row = func_array_merge($row, $c_row);

        $p_row = func_export_get_product($row['productid']);
        if (!empty($p_row))
            $row = func_array_merge($row, $p_row);

        // Export row
        if (!func_export_write_row($row))
            break;

    }
}

?>
