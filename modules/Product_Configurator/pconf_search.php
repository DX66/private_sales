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
 * Product configurator search library
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: pconf_search.php,v 1.37.2.2 2011/01/10 13:12:00 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

x_load('product');

x_session_register('pconf_search_data');

if ($REQUEST_METHOD == 'POST') {
/**
 * Update the search_data
 */
    if (is_array($post_data)) {

        foreach ($post_data as $k=>$v) {

            $pconf_search_data[$k] = is_array($v)
                ? $v
                : trim(stripslashes($v));

        }

        if (empty($post_data['search_in_subcategories'])) {

            $pconf_search_data['search_in_subcategories'] = '';

        }

        // This actions need to avoid the lost of previous seletions

        if (
            $pconf_search_data['product_status'] != 'C'
            && empty($post_data['no_product_type'])
        ) {

            $pconf_search_data['no_product_type'] = '';

            if (empty($post_data['product_type']))
                $pconf_search_data['product_type'] = '';

        }

    } // if (is_array($post_data))

    func_header_location("pconf.php?mode=search&action=go");

} // if ($REQUEST_METHOD == 'POST')

if (
    $action == 'go'
    && is_array($pconf_search_data)
) {
/**
 * Perform the search
 */
    foreach ($pconf_search_data as $k => $v) {

        $search_data[$k] = is_array($v)
            ? $v
            : addslashes($v);

    }

    $old_search_data = $search_data['products'];

    $search_data['products'] = array(
        'productid'                 => $search_data['productid'],
        'substring'                 => $search_data['substring'],
        'categoryid'                 => $search_data['categoryid'],
        'search_in_subcategories'     => $search_data['search_in_subcategories'],
        'category_main'             => 'Y',
    );

    // Search for product types
    if ($search_data['product_status'] == 'B') {

        $search_data['products']['forsale'] = 'B';

    } elseif (!empty($search_data['product_status'])) {

        $search_data['products']['_']['where'] = array(
            "$sql_tbl[products].product_type='" . $search_data["product_status"] . "'",
        );

    }

    $search_data['products']['_']['fields'] = array();

    // If search for non configurable products
    if ($search_data['product_status'] != 'C') {

        if (!empty($search_data['no_product_type'])) {
            // Search for products with no any product types assigned

            $search_data['products']['_']['fields'][]                                 = "COUNT($sql_tbl[pconf_products_classes].productid) as counter";
            $search_data['products']['_']['fields_count'][]                         = "COUNT($sql_tbl[pconf_products_classes].productid) as counter";
            $search_data['products']['_']['left_joins']['pconf_products_classes']     = array(
                'on' => "$sql_tbl[products].productid = $sql_tbl[pconf_products_classes].productid",
            );
            $search_data['products']['_']['having']                                 = array(
                "counter = 0",
            );

        } elseif (!empty($search_data['product_type']) && is_array($search_data['product_type'])) {
            // Search for products with specified product types assigned

            $search_data['products']['_']['inner_joins']['pconf_products_classes'] = array(
                'on' => "$sql_tbl[products].productid = $sql_tbl[pconf_products_classes].productid AND $sql_tbl[pconf_products_classes].ptypeid IN (" . implode(",", $search_data['product_type']) . ")",
            );

        }

    } // if ($search_data['product_status'] != 'C')

    $REQUEST_METHOD = 'GET';

    $mode = 'search';

    include $xcart_dir . '/include/search.php';

    $search_data['products'] = $old_search_data;

    x_session_save('search_data');

    // Calculate types counters
    if (!empty($products)) {

        $ids = array();

        foreach($products as $k => $v) {

            if ($v['product_type'] != 'C') {

                $ids[] = $v['productid'];

            }

        }

        if (!empty($ids)) {

            $types_counts = func_query_hash("SELECT productid, COUNT(*) as cnt FROM $sql_tbl[pconf_products_classes] WHERE productid IN ('".implode("','", $ids)."') GROUP BY productid", "productid", false, true);

            foreach($products as $k => $v) {

                if ($v['product_type'] != 'C') {

                    $products[$k]['types_count'] = intval($types_counts[$v['productid']]);

                }

            }

            $smarty->assign('products', $products);

        } // if (!empty($ids))

    } // if (!empty($products))

    $smarty->assign('navigation_script', "pconf.php?mode=search&action=go");

} else {

    x_load('category');
    $smarty->assign('allcategories', func_data_cache_get("get_categories_tree", array(0, true, $shop_language, $user_account['membershipid'])));

}

/**
 * Get the prodict types information
 */
$product_types = func_query("SELECT * FROM $sql_tbl[pconf_product_types] WHERE 1 $provider_condition ORDER BY orderby, ptype_name");

if (is_array($product_types)) {

    if (!empty($search_data['product_type'])) {

        foreach ($product_types as $k => $v) {

            foreach ($search_data['product_type'] as $k1 => $v1) {

                if ($v['ptypeid'] == $v1) {
                    $product_types[$k]['selected'] = 1;
                }

            }

        }

    }

    $smarty->assign('product_types', $product_types);
}

$smarty->assign('search_data',     $pconf_search_data);

$smarty->assign('mode',         'search');

?>
