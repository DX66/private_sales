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
 * Wholesale prices import/export
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import.php,v 1.27.2.1 2011/01/10 13:12:04 ferz Exp $
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
Memberships:
    data_type:  M
    key:        <Membership name>
    value:      <Membership ID>
Deleted product data:
    data_type:  DP
    key:        <Product ID>
    value:      <Flags>
Deleted variant data:
    data_type:  DV
    key:        <Variant ID>
    value:      <Flags>
Saved price id:
    data_type:  PP
    key:        <Product ID>_<Quantity>_<Membership ID>_<Variant ID>
    value:      <Price ID>

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
    if (!empty($values['variantcode']) && !empty($active_modules['Product_Options'])) {
        $_variantid = func_import_get_cache('VC', $values['variantcode']);
        if (is_null($_variantid) && $import_file['drop']['product_options'] != 'Y' && $import_file["drop"]["product_variants"] != 'Y') {
            if (!empty($_productid)) {
                $_variantid = func_query_first_cell("SELECT variantid FROM $sql_tbl[variants] WHERE productid = '$_productid' AND productcode = '".addslashes($values['variantcode'])."'");

                if (empty($_variantid)) {
                    $_variantid = NULL;

                } elseif ($action == 'do') {
                    func_import_save_cache('VC', $values['variantcode'], $_variantid);
                }
            }
        }

        if (is_null($_variantid) || ($action == 'do' && empty($_variantid))) {
            func_import_module_error('msg_err_import_log_message_48', array('sku' => $values['variantcode']));

        } else {
            $values['variantid'] = $_variantid;
        }
    }

    foreach ($values['quantity'] as $k => $v) {
        if (empty($v))
            continue;

        // Check membershipid
        if (!empty($values['membershipid'][$k])) {
            if (!func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[memberships] WHERE membershipid = '".$values['membershipid'][$k]."'")) {
                func_import_module_error('msg_err_import_log_message_13', array('membershipid' => $values['membershipid'][$k]));
                unset($values['membershipid'][$k]);
            }
        }

        // Check membership
        if (!empty($values['membership'][$k]) && empty($values['membershipid'][$k])) {
            $_membershipid = func_import_get_cache('M', $values['membership'][$k]);
            if (empty($_membershipid)) {
                $_membershipid = func_detect_membership($values['membership'][$k]);
                if ($_membershipid == 0) {
                    // Membership is specified but does not exist
                    func_import_module_error('msg_err_import_log_message_5', array('membership'=>$values['membership'][$k]));
                }
            }
            if (!empty($_membershipid))
                $values['membershipid'][$k] = $_membershipid;
        }

        // Check quantity
        $values['quantity'][$k] = abs(intval($v));
    }

    $data_row[] = $values;

    // Save price id
    if (empty($import_file['WP_save_priceid'])) {
        $res = db_query("SELECT $sql_tbl[pricing].* FROM $sql_tbl[products], $sql_tbl[pricing] WHERE $sql_tbl[products].productid = $sql_tbl[pricing].productid AND ($sql_tbl[pricing].quantity != '1' OR $sql_tbl[pricing].membershipid != '0') $provider_condition");
        if ($res) {
            while ($row = db_fetch_array($res)) {
                func_import_save_cache('PP', $row['productid']."_".$row['quantity']."_".$row['membershipid']."_".$row['variantid'], $row['priceid'], true);
            }
            db_free_result($res);
        }
        $import_file['WP_save_priceid'] = "Y";
    }

}
elseif ($import_step == 'finalize') {
/**
 * FINALIZE rows processing: update database
 */

    if ($import_file['drop'][strtolower($section)] == 'Y') {

        // Delete data by provider
        if ($provider_condition) {
            $products_to_delete = db_query("SELECT productid FROM $sql_tbl[products] WHERE 1 $provider_condition");
            if ($products_to_delete) {
                while ($value = db_fetch_array($products_to_delete))
                    db_query("DELETE FROM $sql_tbl[pricing] WHERE (quantity != '1' OR membershipid != '0') AND productid = '$value[productid]'");
            }

        // Delete all products and related information...
        } else {
            db_query("DELETE FROM $sql_tbl[pricing] WHERE quantity != '1' OR membershipid != '0'");
        }

        $import_file['drop'][strtolower($section)] = '';
    }

    foreach ($data_row as $price) {

    // Import pricing data...

        // Delete old data
        if (empty($price['variantid'])) {
            $tmp = func_import_get_cache('DP', $price['productid']);
            if (strpos($tmp, 'W') === false) {
                db_query("DELETE FROM $sql_tbl[pricing] WHERE (quantity != '1' OR membershipid != '0') AND productid = '$price[productid]'");
                func_import_save_cache('DP', $price['productid'], $tmp."W");
            }
        } else {
            $tmp = func_import_get_cache('DV', $price['variantid']);
            if (strpos($tmp, 'W') === false) {
                db_query("DELETE FROM $sql_tbl[pricing] WHERE (quantity != '1' OR membershipid != '0') AND variantid = '$price[variantid]'");
                func_import_save_cache('DV', $price['variantid'], $tmp."W");
            }
        }

        foreach ($price['quantity'] as $k => $v) {
            $data = array(
                'productid'        => $price['productid'],
                'quantity'        => $v,
                'membershipid'    => intval($price['membershipid'][$k]),
                'variantid'        => intval($price['variantid']),
                'price'            => doubleval($price['price'][$k])
            );

            // Get saved price id
            $_priceid = func_import_get_cache('PP', $data['productid']."_".$data['quantity']."_".$data['membershipid']."_".$data['variantid']);

            // Add new price
            if (empty($_priceid) || func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[pricing] WHERE priceid = '$_priceid'") == 0) {
                if (!empty($_priceid))
                    $data['priceid'] = $_priceid;
                $_priceid = func_array2insert('pricing', $data);
                $result[strtolower($section)]['added']++;

            // Update price
            } else {
                func_array2update('pricing', array('price' => doubleval($price['price'][$k])), "priceid = '$_priceid'");
                $result[strtolower($section)]['updated']++;
            }
        }

        func_import_save_cache('Prp', $price['productid'], $price['productid']);
        func_flush(". ");

    }

// Post-import step
} elseif ($import_step == 'complete') {

    $is_display_header = false;
    while (list($pid, $tmp) = func_import_read_cache('Prp')) {
        if (!$is_display_header) {
            $message = func_get_langvar_by_name('txt_products_counting_',NULL,false,true);
            func_import_add_to_log($message);
            echo "<br />".$message."<br />";
            func_flush();
            $is_display_header = true;
        }
        func_import_rebuild_product($pid);

        func_flush(". ");
    }
    func_import_erase_cache('Prp');

// Export data
} elseif ($import_step == 'export') {

    while ($id = func_export_get_row($data)) {
        if (empty($id))
            continue;

        // Get data
        if (empty($active_modules['Product_Options'])) {
            $mrow = func_query("SELECT $sql_tbl[pricing].variantid FROM $sql_tbl[pricing], $sql_tbl[products] WHERE $sql_tbl[pricing].productid = $sql_tbl[products].productid AND ($sql_tbl[pricing].membershipid > '0' OR $sql_tbl[pricing].quantity > '1') AND $sql_tbl[pricing].productid = '$id'".(empty($provider_sql) ? "" : " AND $sql_tbl[products].provider = '$provider_sql'")." GROUP BY $sql_tbl[pricing].variantid");
        } else {
            $mrow = func_query("SELECT $sql_tbl[pricing].variantid, IFNULL($sql_tbl[variants].productcode, $sql_tbl[products].productcode) as productcode FROM $sql_tbl[products], $sql_tbl[pricing] LEFT JOIN $sql_tbl[variants] ON $sql_tbl[pricing].variantid = $sql_tbl[variants].variantid WHERE $sql_tbl[pricing].productid = $sql_tbl[products].productid AND ($sql_tbl[pricing].membershipid > '0' OR $sql_tbl[pricing].quantity > '1') AND $sql_tbl[pricing].productid = '$id'".(empty($provider_sql) ? "" : " AND $sql_tbl[products].provider = '$provider_sql'")." GROUP BY $sql_tbl[pricing].variantid");
        }
        if (empty($mrow))
            continue;

        // Get product signature
        $p_row = func_export_get_product($id);
        if (empty($p_row))
            continue;

        foreach ($mrow as $row) {

            // Get prices
            $prices = func_query("SELECT $sql_tbl[pricing].*, $sql_tbl[memberships].membership FROM $sql_tbl[products], $sql_tbl[pricing] LEFT JOIN $sql_tbl[memberships] ON $sql_tbl[memberships].membershipid = $sql_tbl[pricing].membershipid WHERE $sql_tbl[pricing].productid = $sql_tbl[products].productid AND ($sql_tbl[pricing].membershipid > '0' OR $sql_tbl[pricing].quantity > '1') AND $sql_tbl[pricing].productid = '$id' AND $sql_tbl[pricing].variantid = '$row[variantid]' ".(empty($provider_sql) ? "" : " AND $sql_tbl[products].provider = '$provider_sql'"));
            if (empty($prices))
                continue;

            if ($row['variantid'] > 0) {
                $p_row['variantcode'] = $row['productcode'];
            }

            $row = $p_row;
            foreach ($prices as $v) {
                $row['quantity'][] = $v['quantity'];
                $row['membership'][] = $v['membership'];
                $row['membershipid'][] = $v['membershipid'];
                $row['price'][] = $v['price'];
            }

            // Write row
            if (!func_export_write_row($row))
                break;
        }
    }

}

?>
