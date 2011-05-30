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
 * Sort products
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: pconf_customer_sort_products.php,v 1.17.2.1 2011/01/10 13:12:00 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

if (
    empty($active_modules['Product_Configurator'])
    || empty($products)
) {
    return;
}

// Check - products from Wishlist module or not
foreach ($products as $k => $product) {
    if (isset($product['object']))
        return true;
}

// Remove uncomplited products
foreach ($products as $k => $product) {

    if ($product['product_type'] != 'C')
        continue;

    $req_slots = func_query_column("SELECT $sql_tbl[pconf_slots].slotid FROM $sql_tbl[pconf_wizards], $sql_tbl[pconf_slots] WHERE $sql_tbl[pconf_wizards].productid = '$product[productid]' AND $sql_tbl[pconf_wizards].stepid = $sql_tbl[pconf_slots].stepid AND $sql_tbl[pconf_slots].status = 'M'");

    foreach ($products as $_k => $_product) {

        if (
            !$_product['hidden']
            || $_product['hidden'] != $product['cartid']
        ) {
            continue;
        }

        $key = array_search($_product['slotid'], $req_slots);

        if ($key !== false)
            unset($req_slots[$key]);

    }

    if (!empty($req_slots)) {
        unset($products[$k]);

        foreach ($products as $_k => $_product) {

            if (
                !$_product['hidden']
                || $_product['hidden'] != $product['cartid']
            ) {
                continue;
            }

            unset($products[$_k]);

        }

    }

} // foreach ($products as $k => $product)

// Sort products
$tmp_products = array();

foreach ($products as $k => $product) {

    if (!empty($product['hidden'])) {
        continue;
    }

    $tmp_products[] = $product;

    if ($product['product_type'] != 'C')
        continue;

    $ids = array();

    foreach ($products as $key => $subproduct) {

        if ($subproduct['hidden'] != $product['cartid'])
            continue;

        $ids[$subproduct['slotid']] = $key;

    }

    if (empty($ids))
        continue;

    $order = func_query_column("SELECT $sql_tbl[pconf_slots].slotid FROM $sql_tbl[pconf_slots], $sql_tbl[pconf_wizards], $sql_tbl[pconf_slot_rules], $sql_tbl[pconf_products_classes] WHERE $sql_tbl[pconf_slots].slotid IN ('".implode("','", array_keys($ids))."') AND $sql_tbl[pconf_products_classes].ptypeid = $sql_tbl[pconf_slot_rules].ptypeid AND $sql_tbl[pconf_wizards].stepid = $sql_tbl[pconf_slots].stepid AND $sql_tbl[pconf_slots].slotid = $sql_tbl[pconf_slot_rules].slotid GROUP BY $sql_tbl[pconf_slots].slotid ORDER BY $sql_tbl[pconf_wizards].orderby, $sql_tbl[pconf_slots].orderby");

    if (is_array($order)) {

        $pconf_list_orderby = array();

        foreach ($order as $pid) {

            $pconf_list_orderby[] = $ids[$pid];

        }

    } else {

        $pconf_list_orderby = array_values($ids);

    }

    foreach ($pconf_list_orderby as $idx) {

        $tmp_products[] = $products[$idx];

    }

}

$products = $tmp_products;
?>
