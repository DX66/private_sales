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
 * Categories management interface
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: categories.php,v 1.50.2.3 2011/01/10 13:11:45 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './auth.php';
require $xcart_dir.'/include/security.php';

$location[] = array(func_get_langvar_by_name('lbl_categories_management'), 'categories.php');

x_load('category');

if ($categories = func_get_categories_list($cat)) {

    /**
     * Override subcategory_count for Admin area
     */
    $product_counts = func_query_hash("SELECT categoryid, COUNT(*) FROM $sql_tbl[products_categories] WHERE categoryid IN ('".implode("','", array_keys($categories))."') GROUP BY categoryid", "categoryid", false, true);

    foreach ($categories as $k => $v) {

        $categories[$k]['subcategory_count']    = ($v['rpos']-$v['lpos']-1)/2;
        $categories[$k]['product_count_global'] = $categories[$k]['product_count'];
        $categories[$k]['product_count']        = isset($product_counts[$k]) ? intval($product_counts[$k]) : 0;

    }

    $smarty->assign('categories', $categories);
}

if ($cat > 0) {

    if ($current_category = func_get_category_data($cat)) {

        $smarty->assign('current_category', $current_category);

    } else {

        $top_message = array(
            'content' => func_get_langvar_by_name('msg_category_not_exist'),
            'type'    => 'E',
        );

        func_header_location('categories.php');

    }

    $smarty->assign('cat', $cat);
}

if (!isset($mode)) {
    $mode = '';
}

/**
 * Ajust category_location array
 */
require './location_adjust.php';

$category_location[count($category_location)-1][1] = '';
$smarty->assign('category_location', $category_location);

// FEATURED PRODUCTS
$f_cat = (empty ($cat) ? '0' : $cat);

if ($REQUEST_METHOD == 'POST') {

    if ($mode == 'update') {

        // Update featured products list

        if (is_array($posted_data)) {
            foreach ($posted_data as $productid => $v) {
                $query_data = array(
                    'avail' => (!empty($v['avail']) ? 'Y' : 'N'),
                    'product_order' => intval($v['product_order'])
                );
                func_array2update('featured_products', $query_data, "productid='$productid' AND categoryid='$f_cat'");
            }
            $top_message['content'] = func_get_langvar_by_name('msg_adm_featproducts_upd');
            $top_message['anchor'] = 'featured';
        }

    } elseif ($mode == 'delete') {

        // Delete selected featured products from the list

        if (is_array($posted_data)) {
            foreach ($posted_data as $productid=>$v) {
                if (empty($v['to_delete']))
                    continue;
                db_query ("DELETE FROM $sql_tbl[featured_products] WHERE productid='$productid' AND categoryid='$f_cat'");
            }
            $top_message['content'] = func_get_langvar_by_name('msg_adm_featproducts_del');
        }

    } elseif ($mode == 'add' && intval($newproductid) > 0) {

        // Add new featured product

        $newavail = (!empty($newavail) ? 'Y' : 'N');
        if ($neworder == '') {
            $maxorder = func_query_first_cell("SELECT MAX(product_order) FROM $sql_tbl[featured_products] WHERE categoryid='$f_cat'");
            $neworder = $maxorder + 10;
        }

        if (func_query_first("SELECT productid FROM $sql_tbl[products] WHERE productid='$newproductid'")) {
            db_query("REPLACE INTO $sql_tbl[featured_products] (productid, product_order, avail, categoryid) VALUES ('$newproductid','$neworder','$newavail', '$f_cat')");
            $top_message['content'] = func_get_langvar_by_name('msg_adm_featproducts_upd');
        }
    }

    $top_message['anchor'] = 'featured';

    func_data_cache_clear('get_categories_tree');
    func_data_cache_clear('get_offers_categoryid');

    func_header_location("categories.php?cat=$cat");

}

$products = func_query ("SELECT $sql_tbl[featured_products].productid, $sql_tbl[products].product, $sql_tbl[featured_products].product_order, $sql_tbl[featured_products].avail from $sql_tbl[featured_products], $sql_tbl[products] where $sql_tbl[featured_products].productid=$sql_tbl[products].productid AND $sql_tbl[featured_products].categoryid='$f_cat' order by $sql_tbl[featured_products].product_order");

$anchors = array(
    'Categories' => 'lbl_categories'
);

if (!empty($products)) {
    $anchors['featured'] = 'lbl_featured_products';
}

foreach ($anchors as $anchor => $anchor_label) {

    $dialog_tools_data['left'][] = array(
        'link'  => "#" . $anchor,
        'title' => func_get_langvar_by_name($anchor_label)
    );
}

$smarty->assign('dialog_tools_data', $dialog_tools_data);

$smarty->assign ('products', $products);
$smarty->assign ('f_cat', $f_cat);

$smarty->assign('main','categories');

// Assign the current location line
$smarty->assign('location', $location);

if (
    file_exists($xcart_dir.'/modules/gold_display.php')
    && is_readable($xcart_dir.'/modules/gold_display.php')
) {
    include $xcart_dir.'/modules/gold_display.php';
}
func_display('admin/home.tpl',$smarty);
?>
