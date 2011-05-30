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
 * This module generates lists to be displayed in advanced statistics
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: display_stats.php,v 1.52.2.2 2011/01/10 13:11:54 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

x_load('category');

/**
 * Navigation code
 */
$objects_per_page = 15;

$last_visited = "last_visited+'".$config["Appearance"]["timezone_offset"]."'";

$total_items = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[referers] WHERE ($last_visited>='$start_date' AND $last_visited<='$end_date')");

require $xcart_dir.'/include/navigation.php';

/**
 * List of category views
 */
if (!empty($cat)) {
    $current_category = func_get_category_data($cat);
    $cat_pos = func_category_get_position($cat);
    $search_condition = ' AND c.cleft BETWEEN ' . $cat_pos['lpos'] . ' AND ' .  $cat_pos['rpos'];
}
else {
    $search_condition = " AND c.parentid='0'";
}

func_run_delayed_query('views_stats%');

$category_viewes = func_query("SELECT c.categoryid, c.category, COUNT(ss.id) as views_stats FROM $sql_tbl[categories] as c, $sql_tbl[stats_shop] as ss WHERE ss.action='C' AND ss.id=c.categoryid $search_condition AND $date_condition GROUP BY c.categoryid ORDER BY views_stats DESC");

$max_category_viewes = 0;

if (is_array($category_viewes)) {

    $cat_path = func_get_category_path($v['categoryid'], 'category', true);

    $re_cat_path = "/^" . preg_quote($cat_path, '/') . "\//S";

    foreach($category_viewes as $k => $v) {

        // Get the maximum of category_views
        $max_category_viewes = max($max_category_viewes, $v['views_stats']);

        // Get the category path
        $_category_names[$v['categoryid']] = $v['category'];
        $category_path = preg_replace($re_cat_path, '', func_get_category_path($v['categoryid'], 'category', true));

        $category_viewes[$k]['category_path'] = preg_replace($re_cat_path, '', func_get_category_path($v['categoryid'], 'category', true));
    }
}

/**
 * Make navigation bar
 */
$nav_bar = $current_category['category_location'];

if (is_array($nav_bar))
    foreach ($nav_bar as $k=>$v)
        $nav_bar[$k][1] = str_replace("home.php?","statistics.php?mode=shop&", $v[1]);

$product_viewes  =
$product_sales   =
$product_deleted = array();

if (!empty($current_category)) {

    // List of product views
    $product_viewes = func_query("SELECT p.productid, p.product, COUNT(ss.id) as views_stats FROM $sql_tbl[products] as p, $sql_tbl[categories] as c, $sql_tbl[products_categories] as pc, $sql_tbl[stats_shop] as ss WHERE p.productid=pc.productid AND pc.categoryid=c.categoryid AND c.categoryid = '$current_category[categoryid]' AND ss.id=p.productid AND ss.action='V' AND $date_condition GROUP BY p.productid ORDER BY views_stats DESC");
    if (!is_array($product_viewes))
        $product_viewes = array();

    // List of product sales
    $product_sales = func_query("SELECT p.productid, p.product, SUM(ss.multi) as sales_stats FROM $sql_tbl[products] as p, $sql_tbl[categories] as c, $sql_tbl[products_categories] as pc, $sql_tbl[stats_shop] as ss WHERE p.productid=pc.productid AND pc.categoryid=c.categoryid AND c.categoryid = '$current_category[categoryid]' AND ss.id=p.productid AND ss.action='S' AND $date_condition GROUP BY p.productid ORDER BY sales_stats DESC");
    if (!is_array($product_sales))
        $product_sales = array();

    // List of deleted from the cart products
    $product_deleted = func_query("SELECT p.productid, p.product, COUNT(id) as del_stats FROM $sql_tbl[products] as p, $sql_tbl[categories] as c, $sql_tbl[products_categories] as pc, $sql_tbl[stats_shop] as ss WHERE p.productid=pc.productid AND pc.categoryid=c.categoryid AND c.categoryid = '$current_category[categoryid]' AND ss.id=p.productid AND ss.action='D' AND $date_condition GROUP BY p.productid ORDER BY del_stats DESC");
    if (!is_array($product_deleted))
        $product_deleted = array();
}

$f_product_viewes = func_query("SELECT p.productid, p.product, COUNT(ss.id) as views_stats FROM $sql_tbl[products] as p, $sql_tbl[featured_products] as pc, $sql_tbl[stats_shop] as ss WHERE p.productid=pc.productid AND pc.categoryid = '$current_category[categoryid]' AND ss.id=p.productid AND ss.action='V' AND $date_condition GROUP BY p.productid ORDER BY views_stats DESC");
if (is_array($f_product_viewes))
    $product_viewes = func_array_merge($product_viewes, $f_product_viewes);

$f_product_sales = func_query("SELECT p.productid, p.product, SUM(ss.multi) as sales_stats FROM $sql_tbl[products] as p, $sql_tbl[featured_products] as pc, $sql_tbl[stats_shop] as ss WHERE p.productid=pc.productid AND pc.categoryid = '$current_category[categoryid]' AND ss.id=p.productid AND ss.action='S' AND $date_condition GROUP BY p.productid ORDER BY sales_stats DESC");

 if (is_array($f_product_sales) && !empty($f_product_sales) && empty($product_sales)) {
    $product_sales_title = func_get_langvar_by_name('lbl_featured_product_sales');
} else {
    $product_sales_title = func_get_langvar_by_name('lbl_product_sales');
}

if (is_array($f_product_sales))
    $product_sales = func_array_merge($product_sales, $f_product_sales);

$f_product_deleted = func_query("SELECT p.productid, p.product, COUNT(id) as del_stats FROM $sql_tbl[products] as p, $sql_tbl[featured_products] as pc, $sql_tbl[stats_shop] as ss WHERE p.productid=pc.productid AND pc.categoryid = '$current_category[categoryid]' AND ss.id=p.productid AND ss.action='D' AND $date_condition GROUP BY p.productid ORDER BY del_stats DESC");
if (is_array($f_product_deleted))
    $product_deleted = func_array_merge($product_deleted, $f_product_deleted);

foreach(array('viewes' => 'views_stats', 'sales' => 'sales_stats', 'deleted' => 'del_stats') as $n => $fn) {
    if (!is_array(${'f_product_'.$n}))
        continue;

    if (count(${'product_'.$n}) > 0) {
        foreach(${'f_product_'.$n} as $k => $v) {
            foreach(${'product_'.$n} as $k2 => $v2) {
                if ($v['productid'] == $v2['productid'])
                    unset(${'f_product_'.$n}[$k]);
            }
        }

        if (count(${'f_product_'.$n}) > 0)
            ${'product_'.$n} = func_array_merge(${'product_'.$n}, ${'f_product_'.$n});

    } else {
        ${'product_'.$n} = ${'f_product_'.$n};
    }
}

$max_product_viewes = 0;
if (is_array($product_viewes) && !empty($product_viewes))
    foreach($product_viewes as $k=>$v) {
        $max_product_viewes = max($max_product_viewes, $v['views_stats']);
    }

$max_product_sales = 0;
if (is_array($product_sales) && !empty($product_sales))
    foreach($product_sales as $k=>$v) {
        $max_product_sales = max($max_product_sales, $v['sales_stats']);
    }

$max_product_deleted = 0;
if (is_array($product_deleted) && !empty($product_deleted))
    foreach($product_deleted as $k=>$v) {
        $max_product_deleted = max($max_product_deleted, $v['del_stats']);
    }

/**
 * Prepare statistics on referers
 */
$referers_array = func_query("SELECT * FROM $sql_tbl[referers] WHERE ($last_visited>='$start_date' AND $last_visited<='$end_date') ORDER BY visits DESC LIMIT $first_page, $objects_per_page");
$res = func_query_first("SELECT MAX(visits) FROM $sql_tbl[referers]");
$max_visits = $res["MAX(visits)"];

function func_calc_adv_stats($arr, $max, $field)
{
    if (is_array($arr)) {
        foreach ($arr as $k => $v) {
            $arr[$k]['bar_begin'] = floor($v[$field] / $max * 100);
            $arr[$k]['bar_end'] = 100 - $arr[$k]['bar_begin'];
        }
    }

    return $arr;
}

$product_sales = func_calc_adv_stats($product_sales, $max_product_sales, 'sales_stats');
$product_views = func_calc_adv_stats($product_views, $max_product_views, 'views_stats');
$product_deleted = func_calc_adv_stats($product_deleted, $max_product_deleted, 'views_stats');
$category_viewes = func_calc_adv_stats($category_viewes, $max_category_viewes, 'del_stats');
$referers_array = func_calc_adv_stats($referers_array, $max_visits, 'visits');

/**
 * Assign Smarty variables
 */
$smarty->assign('category_viewes', $category_viewes);
if (is_array($product_viewes) && !empty($product_viewes))
    $smarty->assign('product_viewes', $product_viewes);
if (is_array($product_sales) && !empty($product_sales))
    $smarty->assign('product_sales', $product_sales);
if (is_array($product_deleted) && !empty($product_deleted))
    $smarty->assign('product_deleted', $product_deleted);
$smarty->assign('product_sales_title', $product_sales_title);
$smarty->assign('referers_array', $referers_array);
$smarty->assign('max_category_viewes', $max_category_viewes);
$smarty->assign('max_product_viewes', $max_product_viewes);
$smarty->assign('max_product_sales', $max_product_sales);
$smarty->assign('max_product_deleted', $max_product_deleted);
$smarty->assign('max_visits', $max_visits);
$smarty->assign('cat_name', $cat_name);
$smarty->assign('nav_bar', $nav_bar);
$smarty->assign('navigation_script',"statistics.php?cat=$cat&mode=shop");

?>
