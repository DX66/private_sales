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
 * Categories-related functions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.category.php,v 1.67.2.9 2011/01/10 13:11:51 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load(
    'files',
    'image'
);

/**
 * Delete category recursively and all subcategories and products
 */
function func_delete_category($cat, $tick = 0)
{
    global $sql_tbl, $active_modules;

    $catpair = func_query_first("SELECT categoryid, parentid, lpos, rpos FROM $sql_tbl[categories] WHERE categoryid='$cat'");

    if ($catpair === false) // category is missing
        return 0;

    // Delete products from subcategories

    $parent_categoryid = $catpair['parentid'];
    $cat_path = func_get_category_path($cat);

    $prods = db_query("SELECT $sql_tbl[products_categories].productid FROM $sql_tbl[categories], $sql_tbl[products_categories] WHERE $sql_tbl[categories].lpos BETWEEN " . $catpair['lpos'] . ' AND ' . $catpair['rpos'] . " AND $sql_tbl[products_categories].categoryid=$sql_tbl[categories].categoryid AND $sql_tbl[products_categories].main='Y'");

    if ($prods) {
        x_load('product');

        if ($tick > 0) {
            func_display_service_header('lbl_deleting_category_products');
        }

        $i = 0;
        while ($prod = db_fetch_array($prods)) {
            $i++;
            func_delete_product($prod['productid'], false);
            if ($tick > 0 && $i % $tick == 0) {
                func_flush('.');
                if ($i % ($tick * 50) == 0) {
                    func_flush("<br />\n");
                }
            }
        }

        if ($tick > 0) {
            func_flush("<br />\n");
        }

        db_free_result($prods);
    }

    // Delete subcategories

    $subcats = func_query_column("SELECT categoryid FROM $sql_tbl[categories] WHERE lpos BETWEEN " . $catpair['lpos'] . ' AND ' . $catpair['rpos']);

    if (is_array($subcats) && !empty($subcats)) {
        x_load('image');

        db_exec("DELETE FROM $sql_tbl[categories] WHERE categoryid IN (?)", array($subcats));
        db_exec("DELETE FROM $sql_tbl[products_categories] WHERE categoryid IN (?)", array($subcats));
        db_exec("DELETE FROM $sql_tbl[categories_subcount] WHERE categoryid IN (?)", array($subcats));
        db_exec("DELETE FROM $sql_tbl[featured_products] WHERE categoryid IN (?)", array($subcats));
        db_exec("DELETE FROM $sql_tbl[categories_lng] WHERE categoryid IN (?)", array($subcats));
        db_exec("DELETE FROM $sql_tbl[category_memberships] WHERE categoryid IN (?)", array($subcats));

        if (!empty($active_modules['Special_Offers'])) {
            db_query("DELETE FROM $sql_tbl[offer_bonus_params] WHERE param_type = 'C' AND param_id IN ('".implode("','", $subcats)."')");
            db_query("DELETE FROM $sql_tbl[offer_condition_params] WHERE param_type = 'C' AND param_id IN ('".implode("','", $subcats)."')");
        }

        func_delete_image($subcats, 'C');
    }

    array_pop($cat_path);
    if (!empty($path)) {
        if ($tick > 0) {
            func_recalc_subcat_count($path, 10);
        }
    }

    // Delete Clean URLs data.
    db_exec("DELETE FROM $sql_tbl[clean_urls] WHERE resource_type = 'C' AND resource_id IN (?)", array($subcats));
    db_exec("DELETE FROM $sql_tbl[clean_urls_history] WHERE resource_type = 'C' AND resource_id IN (?)", array($subcats));

    // Rebuild node indexes
    func_cat_tree_rebuild();

    return $parent_categoryid;
}

/**
 * Recalculate product count in Categories table and Categories counts table
 */
function func_recalc_product_count($categoryid = false, $tick = 0)
{
    global $sql_tbl, $config, $single_mode;

    $forsale_condition = "($sql_tbl[products].forsale = 'Y' OR $sql_tbl[products].forsale = 'B')";

    // Get mysql resource
    $where = '';
    if ($categoryid !== false) {
        if (empty($categoryid))
            return false;

        if (is_array($categoryid)) {
            if (is_array(current($categoryid))) {
                foreach ($categoryid as $k => $v) {
                    $categoryid[$k] = $v['categoryid'];
                }
            }
            $where = "WHERE $sql_tbl[categories].categoryid IN ('".implode("','", $categoryid)."')";

        } elseif (!is_array($categoryid) && !is_resource($categoryid)) {
            $where = "WHERE $sql_tbl[categories].categoryid = '$categoryid'";

        }

        if (!is_resource($categoryid)) {
            $categoryid = db_query("SELECT categoryid FROM $sql_tbl[categories] ".$where);
        }

    } else {
        $categoryid = db_query("SELECT categoryid FROM $sql_tbl[categories]");

    }

    if (!$categoryid)
        return false;

    // Get membership levels
    $lvl = func_query_column("SELECT membershipid FROM $sql_tbl[memberships] WHERE area = 'C'");
    $lvl[] = 0;

    if ($tick > 0)
        func_display_service_header();

    $finished = false;
    $cnt = 0;
    while ($c = db_fetch_array($categoryid)) {

        // Get category position
        $pos = func_category_get_position($c['categoryid']);
        $c = $c['categoryid'];

        // Get common counter
        if (!$single_mode) {
            $res = db_query("SELECT COUNT(*) FROM $sql_tbl[customers], $sql_tbl[products], $sql_tbl[products_categories], $sql_tbl[categories] WHERE $sql_tbl[customers].id=$sql_tbl[products].provider AND $sql_tbl[customers].activity='Y' AND $sql_tbl[products].productid=$sql_tbl[products_categories].productid AND $forsale_condition AND $sql_tbl[products_categories].categoryid = $sql_tbl[categories].categoryid AND $sql_tbl[categories].lpos > $pos[lpos] AND $sql_tbl[categories].rpos < $pos[rpos] AND $sql_tbl[categories].avail = 'Y' GROUP BY $sql_tbl[products].productid");
            $res_top = db_query("SELECT COUNT(*) FROM $sql_tbl[customers], $sql_tbl[products], $sql_tbl[products_categories], $sql_tbl[categories] WHERE $sql_tbl[customers].id=$sql_tbl[products].provider AND $sql_tbl[customers].activity='Y' AND $sql_tbl[products].productid=$sql_tbl[products_categories].productid AND $forsale_condition AND $sql_tbl[products_categories].categoryid = $sql_tbl[categories].categoryid AND $sql_tbl[categories].categoryid = '$c' GROUP BY $sql_tbl[products].productid");

        } else {
            $res = db_query("SELECT COUNT(*) FROM $sql_tbl[products], $sql_tbl[products_categories], $sql_tbl[categories] WHERE $sql_tbl[products].productid=$sql_tbl[products_categories].productid AND $forsale_condition AND $sql_tbl[products_categories].categoryid = $sql_tbl[categories].categoryid AND $sql_tbl[categories].lpos > $pos[lpos] AND $sql_tbl[categories].rpos < $pos[rpos] AND $sql_tbl[categories].avail = 'Y' GROUP BY $sql_tbl[products].productid");
            $res_top = db_query("SELECT COUNT(*) FROM $sql_tbl[products], $sql_tbl[products_categories], $sql_tbl[categories] WHERE $sql_tbl[products].productid=$sql_tbl[products_categories].productid AND $forsale_condition AND $sql_tbl[products_categories].categoryid = $sql_tbl[categories].categoryid AND $sql_tbl[categories].categoryid = '$c' GROUP BY $sql_tbl[products].productid");
        }

        $product_count = 0;
        $top_product_count = 0;
        if ($res && $res_top) {
            $top_product_count = db_num_rows($res_top);
            $product_count = db_num_rows($res) + $top_product_count;
            db_free_result($res);
            db_free_result($res_top);
        }

        func_array2update(
            'categories',
            array(
                'product_count' => $product_count,
                'top_product_count' => $top_product_count
            ),
            "categoryid = '$c'"
        );

        if (count($lvl) == 1) {

            // If membership list is empty
            $query_data = array(
                'product_count' => $product_count,
                'top_product_count' => $top_product_count
            );

            if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[categories_subcount] WHERE categoryid = '$c' AND membershipid = '0'") > 0) {
                func_array2update('categories_subcount', $query_data, "categoryid = '$c' AND membershipid = '0'");

            } else {
                $query_data['categoryid'] = $c;
                $query_data['membershipid'] = 0;
                func_array2insert('categories_subcount', $query_data);
            }

        } else {

            // If membeship list is not empty

            // Get product counter (common products)
            if (!$single_mode) {
                $res = db_query("SELECT COUNT(*) FROM $sql_tbl[customers], $sql_tbl[products_categories], $sql_tbl[categories], $sql_tbl[products] LEFT JOIN $sql_tbl[product_memberships] ON $sql_tbl[product_memberships].productid = $sql_tbl[products].productid WHERE $sql_tbl[customers].id=$sql_tbl[products].provider AND $sql_tbl[customers].activity='Y' AND $sql_tbl[products].productid=$sql_tbl[products_categories].productid AND $forsale_condition AND $sql_tbl[products_categories].categoryid = $sql_tbl[categories].categoryid AND $sql_tbl[categories].lpos > $pos[lpos] AND $sql_tbl[categories].rpos < $pos[rpos] AND $sql_tbl[categories].avail = 'Y' AND $sql_tbl[product_memberships].productid IS NULL GROUP BY $sql_tbl[products].productid");
                $res_top = db_query("SELECT COUNT(*) FROM $sql_tbl[customers], $sql_tbl[products_categories], $sql_tbl[categories], $sql_tbl[products] LEFT JOIN $sql_tbl[product_memberships] ON $sql_tbl[product_memberships].productid = $sql_tbl[products].productid WHERE $sql_tbl[customers].id=$sql_tbl[products].provider AND $sql_tbl[customers].activity='Y' AND $sql_tbl[products].productid=$sql_tbl[products_categories].productid AND $forsale_condition AND $sql_tbl[products_categories].categoryid = $sql_tbl[categories].categoryid AND $sql_tbl[categories].categoryid = '$c' AND $sql_tbl[product_memberships].productid IS NULL GROUP BY $sql_tbl[products].productid");

            } else {
                $res = db_query("SELECT COUNT(*) FROM $sql_tbl[products_categories], $sql_tbl[categories], $sql_tbl[products] LEFT JOIN $sql_tbl[product_memberships] ON $sql_tbl[product_memberships].productid = $sql_tbl[products].productid WHERE $sql_tbl[products].productid=$sql_tbl[products_categories].productid AND $forsale_condition AND $sql_tbl[products_categories].categoryid = $sql_tbl[categories].categoryid AND $sql_tbl[categories].lpos > $pos[lpos] AND $sql_tbl[categories].rpos < $pos[rpos] AND $sql_tbl[categories].avail = 'Y' AND $sql_tbl[product_memberships].productid IS NULL GROUP BY $sql_tbl[products].productid");
                $res_top = db_query("SELECT COUNT(*) FROM $sql_tbl[products_categories], $sql_tbl[categories], $sql_tbl[products] LEFT JOIN $sql_tbl[product_memberships] ON $sql_tbl[product_memberships].productid = $sql_tbl[products].productid WHERE $sql_tbl[products].productid=$sql_tbl[products_categories].productid AND $forsale_condition AND $sql_tbl[products_categories].categoryid = $sql_tbl[categories].categoryid AND $sql_tbl[categories].categoryid = '$c' AND $sql_tbl[product_memberships].productid IS NULL GROUP BY $sql_tbl[products].productid");
            }

            $add_product_count = 0;
            $add_top_product_count = 0;
            if ($res && $res_top) {
                $add_top_product_count = db_num_rows($res_top);
                $add_product_count = db_num_rows($res) + $add_top_product_count;;
                db_free_result($res);
            }

            // Get product counters (by mebership levels)
            $product_count_member = array();
            if ($add_product_count != $product_count) {
                $product_count_member = array();
                $top_product_count_member = array();
                if ($single_mode) {
                    $res = db_query("SELECT IFNULL($sql_tbl[product_memberships].membershipid, 0) as membershipid, COUNT(*) as cnt FROM $sql_tbl[customers], $sql_tbl[products_categories], $sql_tbl[categories], $sql_tbl[products], $sql_tbl[product_memberships] WHERE $sql_tbl[product_memberships].productid = $sql_tbl[products].productid AND $sql_tbl[customers].id=$sql_tbl[products].provider AND $sql_tbl[customers].activity='Y' AND $sql_tbl[products].productid=$sql_tbl[products_categories].productid AND $forsale_condition AND $sql_tbl[products_categories].categoryid = $sql_tbl[categories].categoryid AND $sql_tbl[categories].lpos > $pos[lpos] AND $sql_tbl[categories].rpos < $pos[rpos] AND $sql_tbl[categories].avail = 'Y' GROUP BY $sql_tbl[product_memberships].membershipid, $sql_tbl[products].productid");
                    $res_top = db_query("SELECT IFNULL($sql_tbl[product_memberships].membershipid, 0) as membershipid, COUNT(*) as cnt FROM $sql_tbl[customers], $sql_tbl[products_categories], $sql_tbl[categories], $sql_tbl[products], $sql_tbl[product_memberships] WHERE $sql_tbl[product_memberships].productid = $sql_tbl[products].productid AND $sql_tbl[customers].id=$sql_tbl[products].provider AND $sql_tbl[customers].activity='Y' AND $sql_tbl[products].productid=$sql_tbl[products_categories].productid AND $forsale_condition AND $sql_tbl[products_categories].categoryid = $sql_tbl[categories].categoryid AND $sql_tbl[categories].categoryid = '$c' GROUP BY $sql_tbl[product_memberships].membershipid, $sql_tbl[products].productid");

                } else {
                    $res = db_query("SELECT IFNULL($sql_tbl[product_memberships].membershipid, 0) as membershipid, COUNT(*) as cnt FROM $sql_tbl[products_categories], $sql_tbl[categories], $sql_tbl[products], $sql_tbl[product_memberships] WHERE $sql_tbl[product_memberships].productid = $sql_tbl[products].productid AND $sql_tbl[products].productid=$sql_tbl[products_categories].productid AND $forsale_condition AND $sql_tbl[products_categories].categoryid = $sql_tbl[categories].categoryid AND $sql_tbl[categories].lpos > $pos[lpos] AND $sql_tbl[categories].rpos < $pos[rpos] AND $sql_tbl[categories].avail = 'Y' GROUP BY $sql_tbl[product_memberships].membershipid, $sql_tbl[products].productid");
                    $res_top = db_query("SELECT IFNULL($sql_tbl[product_memberships].membershipid, 0) as membershipid, COUNT(*) as cnt FROM $sql_tbl[products_categories], $sql_tbl[categories], $sql_tbl[products], $sql_tbl[product_memberships] WHERE $sql_tbl[product_memberships].productid = $sql_tbl[products].productid AND $sql_tbl[products].productid=$sql_tbl[products_categories].productid AND $forsale_condition AND $sql_tbl[products_categories].categoryid = $sql_tbl[categories].categoryid AND $sql_tbl[categories].categoryid = '$c' GROUP BY $sql_tbl[product_memberships].membershipid, $sql_tbl[products].productid");
                }

                if ($res && $res_top) {
                    while ($row = db_fetch_array($res)) {
                        if (!isset($product_count_member[$row['membershipid']]))
                            $product_count_member[$row['membershipid']] = 0;

                        $product_count_member[$row['membershipid']]++;
                    }
                    db_free_result($res);

                    while ($row = db_fetch_array($res_top)) {
                        if (!isset($top_product_count_member[$row['membershipid']]))
                            $top_product_count_member[$row['membershipid']] = 0;

                        $top_product_count_member[$row['membershipid']]++;
                    }
                    db_free_result($res);

                }
            }

            foreach ($lvl as $l) {
                $query_data = array(
                    'product_count' => $add_product_count,
                    'top_product_count' => $add_top_product_count
                );
                if (isset($product_count_member[$l]))
                    $query_data['product_count'] += $product_count_member[$l];

                if (isset($top_product_count_member[$l])) {
                    $query_data['product_count'] += $top_product_count_member[$l];
                    $query_data['top_product_count'] += $top_product_count_member[$l];
                }

                if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[categories_subcount] WHERE categoryid = '$c' AND membershipid = '$l'") > 0) {
                    func_array2update('categories_subcount', $query_data, "categoryid = '$c' AND membershipid = '$l'");

                } else {
                    $query_data['categoryid'] = $c;
                    $query_data['membershipid'] = $l;
                    func_array2insert('categories_subcount', $query_data);
                }
            }
        }

        $cnt++;
        if ($tick > 0 && $cnt % $tick == 0) {
            func_flush(". ");
        }
    }

    db_free_result($categoryid);

    return true;
}

/**
 * Recalculate child categories count in Categories counts table
 */
function func_recalc_subcat_count($categoryid = false, $tick = 0)
{
    global $sql_tbl, $config;

    $where = '';

    if ($categoryid !== false) {

        if (empty($categoryid)) {

            return false;

        } elseif (!is_array($categoryid)) {

            $where = "categoryid = '$categoryid'";

        } elseif (is_array($categoryid)) {

            if (is_array(current($categoryid))) {

                foreach ($categoryid as $k => $v) {
                    $categoryid[$k] = $v['categoryid'];
                }
            }

            $where = "categoryid IN ('".implode("','", $categoryid)."')";

        }
    }

    $dwhere = $where ? $dwhere = ' WHERE ' . $where : '';

    db_query("DELETE FROM $sql_tbl[categories_subcount]" . $dwhere);

    $swhere = $where ? " WHERE $sql_tbl[categories]." . $where : '';

    $res = db_query("SELECT $sql_tbl[categories].categoryid, $sql_tbl[categories].lpos, $sql_tbl[categories].rpos, IF($sql_tbl[category_memberships].categoryid IS NULL, '', 'Y') as mexists FROM $sql_tbl[categories] LEFT JOIN $sql_tbl[category_memberships] ON $sql_tbl[category_memberships].categoryid = $sql_tbl[categories].categoryid".$swhere." GROUP BY $sql_tbl[categories].categoryid");

    if (!$res) {
        return false;
    }

    if ($tick > 0)
        func_display_service_header('lbl_recalc_subcat_count');

    $lvl = func_query_column("SELECT membershipid FROM $sql_tbl[memberships] WHERE area = 'C'");
    $cnt = 0;
    $cat_limit = 100;
    $cat_collector = array();

    while ($c = db_fetch_array($res)) {
        $mexists = $c['mexists'];
        $lpos = $c['lpos'];
        $rpos = $c['rpos'];
        $c = $c['categoryid'];

        // Category is common
        if (empty($mexists)) {
            $subcat_count = func_query_first_cell("SELECT COUNT(*)-1 FROM $sql_tbl[categories] USE INDEX (pa) WHERE $sql_tbl[categories].lpos BETWEEN $lpos AND $rpos AND $sql_tbl[categories].avail = 'Y'");
            $query_data = array(
                'categoryid' => $c,
                'subcategory_count' => $subcat_count,
                'membershipid' => 0
            );

            func_array2insert('categories_subcount', $query_data);

            if (!empty($lvl)) {

                foreach ($lvl as $v) {
                    $query_data['membershipid'] = $v;
                    func_array2insert('categories_subcount', $query_data);
                }
            }

        } elseif (!empty($lvl)) {

            // Category is limited by memberships
            $subcat_count = func_query_hash("SELECT COUNT(*) as subcategory_count, IFNULL($sql_tbl[category_memberships].membershipid, 0) as membershipid FROM $sql_tbl[categories] USE INDEX (pa) LEFT JOIN $sql_tbl[category_memberships] ON $sql_tbl[category_memberships].categoryid = $sql_tbl[categories].categoryid WHERE $sql_tbl[categories].lpos BETWEEN $lpos AND $rpos AND $sql_tbl[categories].avail = 'Y' GROUP BY $sql_tbl[category_memberships].membershipid", "membershipid", false, true);

            if (!empty($subcat_count)) {

                $zero_count = intval($subcat_count[0]);

                foreach ($lvl as $l) {
                    func_array2insert(
                        'categories_subcount',
                        array(
                            'membershipid' => $l,
                            'subcategory_count' => (isset($subcat_count[$l]) ? $subcat_count[$l] : 0) + $zero_count,
                            'categoryid' => $c
                        )
                    );
                }
            }
        }

        $cat_collector[] = $c;
        if (count($cat_collector) > $cat_limit) {
            func_recalc_product_count($cat_collector, $tick);
            $cat_collector = array();
        }

        $cnt++;
        if ($tick > 0 && $cnt % $tick == 0) {
            func_flush(". ");
        }
    }

    db_free_result($res);

    if (!empty($cat_collector))
        func_recalc_product_count($cat_collector, $tick);

    return true;
}

/**
 * Get parent categories chain
 */
function func_get_category_parents($categoryid)
{
    global $sql_tbl;

    if (!is_array($categoryid)) {
        $categoryid = array($categoryid);
    }

    $cats = array();

    foreach ($categoryid as $c) {
        
        $pos = func_category_get_position($c);

        $tmp = func_query_column("SELECT categoryid FROM $sql_tbl[categories] WHERE lpos < $pos[lpos] AND rpos > $pos[rpos]");

        if (!empty($tmp)) {
            $cats = array_unique(func_array_merge($cats, $tmp));
        }
    }

    return $cats;
}

/**
 * Get category's title
 */
function func_get_category_title($categoryid, $category = null)
{
    global $sql_tbl, $config;

    if (!is_int($categoryid) || $categoryid < 1 || (!is_null($category) && !is_array($category)))
        return false;

    if (!is_array($category) || !isset($category['title_tag'])) {
        $category = func_query_first("SELECT title_tag FROM $sql_tbl[categories] WHERE categoryid = '$categoryid'");
    }

    if (!is_array($category) || !isset($category['title_tag']))
        return false;

    $category['title_tag'] = trim($category['title_tag']);

    $parents = array();
    $ids = array();

    if (empty($category['title_tag'])) {

        $ids = array_reverse(func_get_category_path($categoryid));

        array_shift($ids);
        $parents = func_query_hash("SELECT categoryid, title_tag FROM $sql_tbl[categories] WHERE categoryid IN ('".implode("', '", $ids)."') AND override_child_meta = 'Y'", "categoryid", false);

        while ((list(, $cid) = each($ids)) && empty($category['title_tag'])) {
            $parents[$cid]['title_tag'] = trim($parents[$cid]['title_tag']);

            if (empty($category['title_tag']) && !empty($parents[$cid]['title_tag']))
                $category['title_tag'] = $parents[$cid]['title_tag'];
        }
    }

    if (empty($category['title_tag']))
        $category['title_tag'] = trim($config['SEO']['site_title']);

    return $category['title_tag'];
}

/**
 * Get category's meta description and meta keywords data
 */
function func_get_category_meta($categoryid, $category = null)
{
    global $sql_tbl, $config;

    if (!is_int($categoryid) || $categoryid < 1 || (!is_null($category) && !is_array($category)))
        return false;

    if (!is_array($category) || !isset($category['meta_description']) || !isset($category['meta_keywords']))
        $category = func_query_first("SELECT meta_description, meta_keywords FROM $sql_tbl[categories] WHERE categoryid = '$categoryid'");

    if (!is_array($category) || !isset($category['meta_description']))
        return false;

    $category['meta_description'] = trim($category['meta_description']);
    $category['meta_keywords'] = trim($category['meta_keywords']);

    $parents = array();
    $ids = array();

    if (empty($category['meta_description']) || empty($category['meta_keywords'])) {

        $ids = array_reverse(func_get_category_path($categoryid));

        array_shift($ids);
        $parents = func_query_hash("SELECT categoryid, meta_description, meta_keywords FROM $sql_tbl[categories] WHERE categoryid IN ('".implode("', '", $ids)."') AND override_child_meta = 'Y'", "categoryid", false);

        while ((list(,$cid) = each($ids)) && (empty($category['meta_description']) || empty($category['meta_keywords']))) {
            $parents[$cid]['meta_description'] = trim($parents[$cid]['meta_description']);
            $parents[$cid]['meta_keywords'] = trim($parents[$cid]['meta_keywords']);

            if (empty($category['meta_description']) && !empty($parents[$cid]['meta_description']))
                $category['meta_description'] = $parents[$cid]['meta_description'];

            if (empty($category['meta_keywords']) && !empty($parents[$cid]['meta_keywords']))
                $category['meta_keywords'] = $parents[$cid]['meta_keywords'];
        }
    }

    if (empty($category['meta_description']))
        $category['meta_description'] = trim($config['SEO']['meta_descr']);

    if (empty($category['meta_keywords']))
        $category['meta_keywords'] = trim($config['SEO']['meta_keywords']);

    return array($category['meta_description'], $category['meta_keywords']);
}

/**
 * This function builds the categories list of specified category 
 * 
 * @param int  $cat           Root categoryid
 * @param bool $short_list    Collect only minimum information
 * @param bool $need_sublevel Collect only current level categories 
 * @param int  $max_level     Maximum depth level 
 *  
 * @return mixed
 * @see    ____func_see____
 */
function func_get_categories_list($cat = 0, $short_list = true, $need_sublevel = false, $max_level = 0)
{
    global $current_area, $sql_tbl, $shop_language, $active_modules, $config;

    $cat = abs(intval($cat));

    $to_search =
    $search_condition =
    $join_tbl = array();

    $from = "$sql_tbl[categories] AS node ";

    $having = '';

    if (false === $need_sublevel) {

        $search_condition[] = "node.parentid = '$cat'";

    } else if ($cat > 0) {

        $root_cat = func_category_get_position($cat);
        $search_condition[] = "node.lpos BETWEEN " . $root_cat['lpos'] . ' AND ' . $root_cat['rpos'];

    }

    if (
        $current_area == 'C'
        || $current_area == 'B'
    ) {

        global $user_account;

        $search_condition[] = "node.avail = 'Y'";
        $search_condition[] = "($sql_tbl[category_memberships].membershipid IS NULL OR $sql_tbl[category_memberships].membershipid = '$user_account[membershipid]')";
    }

    $sort_condition = " ORDER BY node.lpos, " 
        . ( $current_area == 'A' ?  "node.category" : 'category' );

    if ($short_list) {
        $to_search[] = 'node.categoryid';
        $to_search[] = 'node.parentid';
        $to_search[] = 'node.rpos';
        $to_search[] = 'node.lpos';
        $to_search[] = 'node.category';
        $to_search[] = 'node.avail';
        $to_search[] = 'node.order_by';
        $to_search[] = 'node.product_count';

    } else {
        
        $to_search[] = 'node.*';
    }

    if (
        $current_area == 'C'
        || $current_area == 'B'
    ) {

        $join_tbl[] = array(
            'tbl' => $sql_tbl['categories_lng'],
            'on'  => "$sql_tbl[categories_lng].code='$shop_language' AND $sql_tbl[categories_lng].categoryid=node.categoryid",
            'idx' => 'PRIMARY'
        );
 
        $join_tbl[] = array(
            'tbl' => $sql_tbl['category_memberships'],
            'on'  => "$sql_tbl[category_memberships].categoryid = node.categoryid"
        );
        
        $to_search[] = "IF($sql_tbl[categories_lng].categoryid IS NOT NULL AND $sql_tbl[categories_lng].category != '', $sql_tbl[categories_lng].category, node.category) AS category";

        // Count the subcategories for 'root' and 'level' flag values

        $join_tbl[] = array(
            'tbl' => $sql_tbl['categories_subcount'],
            'on'  => "$sql_tbl[categories_subcount].categoryid = node.categoryid"
                     . " AND $sql_tbl[categories_subcount].membershipid = '$user_account[membershipid]'",
            'idx' => 'PRIMARY'
        );

        $to_search[] = "$sql_tbl[categories_subcount].subcategory_count";
        $to_search[] = "$sql_tbl[categories_subcount].product_count";

        if (!empty($active_modules['Flyout_Menus'])) {
            $to_search[] = "$sql_tbl[categories_subcount].top_product_count";
        }

    } else {

        $join_tbl[] = array(
            'tbl' => $sql_tbl['categories_subcount'],
            'on'  => "$sql_tbl[categories_subcount].categoryid = node.categoryid",
            'idx' => 'PRIMARY'
        );

        $to_search[] = "MAX($sql_tbl[categories_subcount].subcategory_count) AS subcategory_count";
        $to_search[] = "MAX($sql_tbl[categories_subcount].product_count) AS product_count";
    }

    if ($need_sublevel) {

        // Get categoryid path and level

        $from = "$sql_tbl[categories] AS parent ," . $from;
        $to_search[] = "(COUNT(parent.categoryid)-1) AS category_level";
        $search_condition[] = "node.lpos BETWEEN parent.lpos AND parent.rpos";

        if ($max_level > 0) {
            $having = " HAVING category_level <= $max_level";
        }
    }

    // Check category icons

    if (true !== $short_list) {

        $to_search[] = "$sql_tbl[images_C].image_path";
        $to_search[] = "$sql_tbl[images_C].image_x";
        $to_search[] = "$sql_tbl[images_C].image_y";
        $to_search[] = "$sql_tbl[images_C].imageid";

        $join_tbl[] = array(
            'tbl' => $sql_tbl['images_C'],
            'on'  => "node.categoryid = $sql_tbl[images_C].id"
        );
    }

    // Prepare join string
    $join_string = '';

    foreach ($join_tbl as $t) {
        $join_string .= ' LEFT JOIN ' . $t['tbl'] 
            . (isset($t['idx']) ? ' USE INDEX (' . $t['idx'] . ')' : '') 
            . ' ON ' . $t['on'];
    }

    $search_string = implode(', ', $to_search);

    $result = db_query(
        "SELECT $search_string FROM " . $from . $join_string
            . (!empty($search_condition) ? ' WHERE ' . implode(' AND ', $search_condition) : '')
            . (strlen($join_string) > 0 ? " GROUP BY node.categoryid" : '') . $having
            . ' ' . $sort_condition
    );

    if (!$result) {
        return false;
    }

    $categories = array();

    while ($category = db_fetch_array($result)) {

        // Get the full path for category name

        $category['is_icon']  = !is_null($category["image_path"]);

        if (
            !empty($active_modules['Flyout_Menus'])
            && $category['is_icon']
            && $config['Flyout_Menus']['icons_mode'] == 'E'
            && $config['Flyout_Menus']['icons_icons_in_categories'] >= $category['category_level']
        ) {
            $thumb_url = func_image_cache_get_image('C', 'catthumbn', $category['imageid']);
            if (!empty($thumb_url)) {
                $category['thumb_url'] = $thumb_url['url'];
                $category['thumb_x']   = $thumb_url['width'];
                $category['thumb_y']   = $thumb_url['height'];
                unset($thumb_url);
            }
        }

        $categories[$category['categoryid']] = $category;
    }

    return $categories;
}

/**
 * This function gathering the current category data
 */
function func_get_category_data($cat)
{
    global $current_area, $sql_tbl, $shop_language, $user_account, $current_location, $config;

    $cat = intval($cat);

    $join_tbl = " LEFT JOIN $sql_tbl[images_C] ON $sql_tbl[images_C].id = $sql_tbl[categories].categoryid LEFT JOIN $sql_tbl[categories_subcount] ON $sql_tbl[categories_subcount].categoryid = $sql_tbl[categories].categoryid".(($current_area == 'C' || $current_area == 'B')?" AND $sql_tbl[categories_subcount].membershipid = '".@$user_account['membershipid']."'":"")." LEFT JOIN $sql_tbl[category_memberships] ON $sql_tbl[category_memberships].categoryid = $sql_tbl[categories].categoryid ";
    $to_search = ", $sql_tbl[categories].category, $sql_tbl[images_C].image_path, $sql_tbl[images_C].image_x, $sql_tbl[images_C].image_y";
    if ($current_area == 'C' || $current_area == 'B') {
        $to_search .= ", $sql_tbl[categories_subcount].subcategory_count";
    } else {
        $to_search .= ", MAX($sql_tbl[categories_subcount].subcategory_count) as subcategory_count";
    }

    $search_condition = '';
    if ($current_area == 'C' || $current_area == 'B') {
        $join_tbl .= " LEFT JOIN $sql_tbl[categories_lng] ON $sql_tbl[categories_lng].code='$shop_language' AND $sql_tbl[categories_lng].categoryid=$sql_tbl[categories].categoryid ";
        $to_search .= ", IF(($sql_tbl[categories_lng].category IS NOT NULL AND $sql_tbl[categories_lng].category != ''), $sql_tbl[categories_lng].category, $sql_tbl[categories].category) as category, IF(($sql_tbl[categories_lng].description IS NOT NULL AND $sql_tbl[categories_lng].description != ''), $sql_tbl[categories_lng].description, $sql_tbl[categories].description) as description, $sql_tbl[categories].category as category_name_orig";
        $search_condition = "AND $sql_tbl[categories].avail='Y' AND ($sql_tbl[category_memberships].membershipid = '".$user_account["membershipid"]."' OR $sql_tbl[category_memberships].membershipid IS NULL)";
    }

    $join_tbl .= " LEFT JOIN $sql_tbl[clean_urls] ON resource_type = 'C' AND resource_id = '$cat'";
    $to_search .= ", $sql_tbl[clean_urls].clean_url, $sql_tbl[clean_urls].mtime";

    $category = func_query_first("SELECT $sql_tbl[categories].* $to_search FROM $sql_tbl[categories] $join_tbl WHERE $sql_tbl[categories].categoryid='$cat' $search_condition".(strlen($join_tbl) > 0 ? "GROUP BY $sql_tbl[categories].categoryid " : ""));

    if (!empty($category)) {

        $tmp = func_query("SELECT membershipid FROM $sql_tbl[category_memberships] WHERE categoryid = '$cat'");
        if (!empty($tmp)) {
            $category['membershipids'] = array();
            foreach ($tmp as $v) {
                $category['membershipids'][$v['membershipid']] = 'Y';
            }
        }

        // Get the array of all parent categories

        $_cat_sequense = func_get_category_path($cat);

        // Generate category sequence, i.e.
        // Books, Books/Poetry, Books/Poetry/Philosophy ...

        if(!empty($_cat_sequense)) {
            $search_condition_2 = '';
            if ($current_area == 'C' || $current_area == 'B') {
                $search_condition_2 = " AND $sql_tbl[categories].avail = 'Y'";
            }

            $_cat_names = func_query_hash("SELECT $sql_tbl[categories].categoryid $to_search FROM $sql_tbl[categories] $join_tbl WHERE $sql_tbl[categories].categoryid IN ('".implode("','", $_cat_sequense)."')".$search_condition_2.(strlen($join_tbl) > 0 ? " GROUP BY $sql_tbl[categories].categoryid" : ""), "categoryid", false);
            if(count($_cat_names) != count($_cat_sequense))
                return false;

            foreach ($_cat_sequense as $_catid) {
                $_cat_name = $_cat_names[$_catid];
                $category['category_location'][] = array($_cat_name['category'], "home.php?cat=$_catid");
                if (is_null($category['image_path']) && !is_null($_cat_name['image_path'])) {
                    $category['image_path'] = $_cat_name['image_path'];
                    $category['image_x'] = $_cat_name['image_x'];
                    $category['image_y'] = $_cat_name['image_y'];
                }
            }
        }

        $category['is_icon'] = !is_null($category["image_path"]);

        if ($current_area == 'C' || $current_area == 'B') {
            if ($category['description'] == strip_tags($category['description'])) {
                $category['description'] = str_replace("\n", "<br />", $category['description']);
            }
        }

        $category['clean_urls_history'] = func_query_hash("SELECT id, clean_url FROM $sql_tbl[clean_urls_history] WHERE resource_type = 'C' AND resource_id = '$cat' ORDER BY mtime DESC", "id", false, true);

        list(
            $category['image_x'],
            $category['image_y']
        ) = func_crop_dimensions(
            $category['image_x'],
            $category['image_y'],
            $config['Appearance']['thumbnail_width'],
            $config['Appearance']['thumbnail_height']
        );

        return $category;
    }

    return false;
}

/**
 * Recursively processes all categories tree
 * and rebuilds left and right keys of the nested set 
 * 
 * @param int $parent       Parent categoryid
 * @param int $left         Left index
 * @param int $init_phase   Initial phase indicator
 *  
 * @return int
 * @see    ____func_see____
 */
function func_cat_tree_rebuild($parent = 0, $left = 0, $init_phase = true)
{
    global $sql_tbl, $updated;

    if ($init_phase) {

        func_display_service_header('txt_rebuilding_category_indexes_');
        $_ok = func_get_langvar_by_name('lbl_ok', NULL, false, true);

        db_query('LOCK TABLES ' . $sql_tbl['categories'] . ' WRITE');
    }

    $right = $left + 1;
 
    $result = db_query('SELECT categoryid FROM ' . $sql_tbl['categories'] 
        . ' WHERE parentid=\'' . $parent . '\''
        . ' ORDER BY order_by ASC, category ASC'
    );

    while ($row = db_fetch_array($result)) {
        
        $right = func_cat_tree_rebuild($row['categoryid'], $right, false);
            
        $updated ++;
        echo '.';
        if ($updated % 100 == 0) {
            echo "<br />\n";
        }
        func_flush();
    }

    db_free_result($result);
 
    db_query('UPDATE ' . $sql_tbl['categories'] . ' SET lpos=' . $left . ', rpos= '
        . $right . ' WHERE categoryid=' . $parent );  
    
    if ($init_phase) {
        db_query('UNLOCK TABLES');
        func_flush($_ok . "<br />\n");
    }
 
    func_data_cache_clear('get_categories_tree');
    func_data_cache_clear('get_offers_categoryid');

    return $right + 1;
}

/**
 * Display categories tree   
 * 
 * @param int $root Root categoryid
 *  
 * @return void
 * @see    ____func_see____
 */
function func_get_categories_tree($root = 0, $simple = true, $language = false, $membershipid = false)
{
    global $sql_tbl, $current_area;
    global $shop_language, $user_account;

    if (empty($language)) 
        $language = $shop_language;

    if (empty($membershipid)) 
        $membershipid = $user_account['membershipid'];

    settype($root, 'int');
    settype($simple, 'bool');

    if ($root > 0)
        $root_cat = func_category_get_position($root);

    $right = $tree = array();

    $to_search = array(
        'c.categoryid',
        'c.lpos',
        'c.rpos'
    );

    $search_condition =
    $join_tbl = array();

    if ($current_area == 'A' || $current_area == 'P') {

        $to_search[] = 'c.category';

    } else {
       

        $join_tbl[] = array(
            'tbl' => "$sql_tbl[categories_lng] AS cl",
            'on'  => "cl.code='$language' AND cl.categoryid=c.categoryid",
            'idx' => 'PRIMARY'
        );

        $join_tbl[] = array(
            'tbl' => "$sql_tbl[category_memberships] AS cm",
            'on'  => "cm.categoryid = c.categoryid"
        );

        $to_search[] = "IF(cl.categoryid IS NOT NULL AND cl.category != '', cl.category, c.category) AS category";
        
        $search_condition[] = "c.avail = 'Y'";
        $search_condition[] = "(cm.membershipid IS NULL OR cm.membershipid = '" . intval($membershipid) . "')";
    }

    // Prepare join string
    $join_string = '';

    foreach ($join_tbl as $t) {
        $join_string .= ' LEFT JOIN ' . $t['tbl']
            . (isset($t['idx']) ? ' USE INDEX (' . $t['idx'] . ')' : '')
            . ' ON ' . $t['on'];
    }

    $search_string = implode(', ', $to_search);
    $search_condition_string  = (!empty($search_condition)) ? ' AND ' . implode(' AND ', $search_condition) : '';
 
    $result = db_query(
        $a = 'SELECT ' . $search_string . ' FROM ' . $sql_tbl['categories'] . ' AS c'
        . $join_string
        . ' WHERE 1 '
        . (($root > 0) ? "lpos BETWEEN $root_cat[lpos] AND $root_cat[rpos] " : "")
        . $search_condition_string
        . ' ORDER BY lpos ASC'
    );

    if ($result) {

        while ($row = db_fetch_array($result)) {

            if (count($right) > 0) {

                while ($right[count($right)-1]['rpos'] < $row['rpos']) {

                    array_pop($right);

                    if (empty($right))
                        break;
                }

            }

            $right[] = $row;

            if ($simple) {

                $tree[$row['categoryid']] = func_implode_assoc($right, 'category');

            } else {

                $tmp = $tree[$row['categoryid']] = $right[count($right)-1];

                $tree[$row['categoryid']] += array(
                    'category_path' => func_implode_assoc($right, 'category'),
                    'childs'        => ($tmp['rpos'] - $tmp['lpos'] - 1) / 2,
                );

            }
 
        }

        // Sort categories
        if (
            !empty($tree)
            && (
                (
                    $current_area != 'C'
                    && $current_area != 'B'
                )
            )
        ) {
            if (!function_exists('func_categories_tree_sort')) {

                function func_categories_tree_sort($a, $b) {

                    return is_array($a)
                        ? strcmp($a['category_path'], $b['category_path'])
                        : strcmp($a, $b);

                }

            }

            uasort($tree, 'func_categories_tree_sort');
        }

        db_free_result($result);

    }

    return $tree;
}

/**
 * Returns category node path
 * 
 * @param int|array $cat       Category id or array including category data
 * @param string    $field     Field that goes into path values
 * @param bool      $as_string Return string path
 * @param bool      $delim     String path delimiter
 *
 * @return mixed
 * @see    ____func_see____
 */
function func_get_category_path($cat = 0, $field = 'categoryid', $as_string = false, $delim = '/')
{
    global $sql_tbl;

    $cat = abs(intval($cat));

    $cats = func_query_column('SELECT parent.' . $field . ' FROM ' . $sql_tbl['categories'] . ' AS node, '
        . $sql_tbl['categories'] . ' AS parent WHERE node.lpos BETWEEN parent.lpos AND parent.rpos'
        . ' AND node.categoryid=' . $cat . ' ORDER BY parent.lpos');

    if (empty($cats)) {

        return (false !== $as_string) ? '' : array();

    } elseif (false !== $as_string) {

        return implode($delim, $cats);

    } else {

        return $cats;
    
    }
}
 
/**
 * Returns left/right index of the category node
 *
 * @param int $cat Category id
 * 
 * @return void
 * @see    ____func_see____
 */
function func_category_get_position($cat = 0)
{
    global $sql_tbl;

    if (
        !is_numeric($cat)
        || $cat <= 0
    ) {

        return func_query_first('SELECT MIN(lpos) AS lpos, MAX(rpos) AS rpos FROM ' . $sql_tbl['categories']);

    } else {

        return func_query_first('SELECT lpos, rpos FROM ' . $sql_tbl['categories']
            . ' WHERE categoryid=' . $cat);
    }
}

/**
 * Shift node positions, generate index for the new category
 * 
 * @param  int  $parent   Parent category ID
 * @param  int  $order_by Position
 * @param  int  $name     Category name
 *  
 * @return int  New category ID
 * @see    ____func_see____
 */
function func_insert_category($parent = 0, $order_by = 0, $name = '')
{
    global $sql_tbl;

    db_query("LOCK TABLE $sql_tbl[categories] WRITE");
    $parent = intval($parent);           
    $order_by = intval($order_by);

    // Find a child node on the same level before the current one(order_by,category)
    $child_node_before = func_query_first("SELECT rpos, lpos FROM $sql_tbl[categories] WHERE parentid='" . $parent . "' AND order_by <= '$order_by' AND category <= '$name' ORDER BY order_by DESC, category DESC");

    if (empty($child_node_before)) {

        $parent_node = func_query_first("SELECT rpos, lpos FROM $sql_tbl[categories] WHERE categoryid='" . $parent . "'");
        $index = intval($parent_node['lpos']);

    } else {

        $index = intval($child_node_before['rpos']);
    }

    // shift nodes positions and insert category

    db_query("UPDATE $sql_tbl[categories] SET rpos = rpos + 2 WHERE rpos > '$index'");
    db_query("UPDATE $sql_tbl[categories] SET lpos = lpos + 2 WHERE lpos > '$index'");

    $cat = func_array2insert(
        'categories',
        array(
            'parentid'    => $parent,
            'lpos'        => $index + 1,
            'rpos'        => $index + 2,
            'description' => ''
        ),
        false,
        true
    );

    db_query('UNLOCK TABLES');

    func_data_cache_clear('get_categories_tree');
    func_data_cache_clear('get_offers_categoryid');

    return $cat;
}

?>
