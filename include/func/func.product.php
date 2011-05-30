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
 * Functions related to products functionality
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.product.php,v 1.124.2.9 2011/02/07 13:53:01 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

/**
 * Delete product from products table + all associated information
 * $productid - product's id
 */
function func_delete_product($productid, $update_categories = true, $delete_all = false)
{
    global $sql_tbl, $xcart_dir, $smarty;

    x_load('backoffice', 'category', 'image', 'export');

    if ($delete_all === true) {

        db_query("DELETE FROM $sql_tbl[pricing]");
        db_query("DELETE FROM $sql_tbl[product_links]");
        db_query("DELETE FROM $sql_tbl[featured_products]");
        db_query("DELETE FROM $sql_tbl[products]");
        db_query("DELETE FROM $sql_tbl[delivery]");
        db_query("DELETE FROM $sql_tbl[extra_field_values]");
        db_query("DELETE FROM $sql_tbl[products_categories]");
        db_query("DELETE FROM $sql_tbl[product_taxes]");
        db_query("DELETE FROM $sql_tbl[product_votes]");
        db_query("DELETE FROM $sql_tbl[product_reviews]");
        db_query("DELETE FROM $sql_tbl[products_lng]");
        db_query("DELETE FROM $sql_tbl[download_keys]");
        db_query("DELETE FROM $sql_tbl[discount_coupons]");
        db_query("DELETE FROM $sql_tbl[stats_customers_products]");
        db_query("DELETE FROM $sql_tbl[wishlist]");
        db_query("DELETE FROM $sql_tbl[product_bookmarks]");
        db_query("DELETE FROM $sql_tbl[product_memberships]");
        db_query("DELETE FROM $sql_tbl[ge_products]");

        func_delete_images('T');
        func_delete_images('P');
        func_delete_images('D');

        // Subscriptions module
        if (func_is_defined_module_sql_tbl('Subscriptions', 'subscriptions')) {
            db_query("DELETE FROM $sql_tbl[subscriptions]");
            db_query("DELETE FROM $sql_tbl[subscription_customers]");
        }

        // Feature comparison module
        if (func_is_defined_module_sql_tbl('Feature_Comparison', 'product_features')) {
            db_query("DELETE FROM $sql_tbl[product_features]");
            db_query("DELETE FROM $sql_tbl[product_foptions]");
        }

        // Product options module
        if (func_is_defined_module_sql_tbl('Product_Options', 'class_options')) {

            db_query("DELETE FROM $sql_tbl[classes]");
            db_query("DELETE FROM $sql_tbl[class_options]");
            db_query("DELETE FROM $sql_tbl[class_lng]");
            db_query("DELETE FROM $sql_tbl[product_options_lng]");
            db_query("DELETE FROM $sql_tbl[product_options_ex]");
            db_query("DELETE FROM $sql_tbl[product_options_js]");
            db_query("DELETE FROM $sql_tbl[variant_items]");
            db_query("DELETE FROM $sql_tbl[variant_backups]");
            db_query("DELETE FROM $sql_tbl[variants]");

            func_delete_images('W');

        }

        // Product configurator module
        if (func_is_defined_module_sql_tbl('Product_Configurator', 'pconf_products_classes')) {

            db_query("DELETE FROM $sql_tbl[pconf_products_classes]");
            db_query("DELETE FROM $sql_tbl[pconf_class_specifications]");
            db_query("DELETE FROM $sql_tbl[pconf_class_requirements]");
            db_query("DELETE FROM $sql_tbl[pconf_wizards]");
            db_query("DELETE FROM $sql_tbl[pconf_slots]");
            db_query("DELETE FROM $sql_tbl[pconf_slot_rules]");
            db_query("DELETE FROM $sql_tbl[pconf_slot_markups]");
        }

        // Magnifier module
        if (func_is_defined_module_sql_tbl('Magnifier', 'images_Z')) {

            db_query("DELETE FROM $sql_tbl[images_Z]");

            $dir_z = func_image_dir('Z');

            if (is_dir($dir_z) && file_exists($dir_z))
                func_rm_dir($dir_z);
        }

        // Special Offers
        if (func_is_defined_module_sql_tbl('Special_Offers', 'offer_bonus_params')) {

            db_query("DELETE FROM $sql_tbl[offer_bonus_params] WHERE param_type = 'P'");
            db_query("DELETE FROM $sql_tbl[offer_condition_params] WHERE param_type = 'P'");
            db_query("DELETE FROM $sql_tbl[offer_product_params]");

        }

        if ($update_categories) {

            $res = db_query("SELECT categoryid FROM $sql_tbl[categories]");

            func_recalc_product_count($res);
        }

        func_data_cache_get('fc_count', array('Y'), true);
        func_data_cache_get('fc_count', array('N'), true);

        db_query("DELETE FROM $sql_tbl[quick_flags]");
        db_query("DELETE FROM $sql_tbl[quick_prices]");
        db_query("DELETE FROM $sql_tbl[clean_urls] WHERE resource_type = 'P'");
        db_query("DELETE FROM $sql_tbl[clean_urls_history] WHERE resource_type = 'P'");

        func_export_range_erase('PRODUCTS');

        return true;
    }

    $product_categories = func_query_column("SELECT $sql_tbl[categories].categoryid FROM $sql_tbl[categories], $sql_tbl[products_categories] WHERE $sql_tbl[categories].categoryid = $sql_tbl[products_categories].categoryid AND $sql_tbl[products_categories].productid='$productid'");

    db_query("DELETE FROM $sql_tbl[pricing] WHERE productid='$productid'");
    db_query("DELETE FROM $sql_tbl[product_links] WHERE productid1='$productid' OR productid2='$productid'");
    db_query("DELETE FROM $sql_tbl[featured_products] WHERE productid='$productid'");
    db_query("DELETE FROM $sql_tbl[products] WHERE productid='$productid'");
    db_query("DELETE FROM $sql_tbl[delivery] WHERE productid='$productid'");
    db_query("DELETE FROM $sql_tbl[extra_field_values] WHERE productid='$productid'");
    db_query("DELETE FROM $sql_tbl[products_categories] WHERE productid='$productid'");
    db_query("DELETE FROM $sql_tbl[product_memberships] WHERE productid='$productid'");
    db_query("DELETE FROM $sql_tbl[ge_products] WHERE productid='$productid'");

    func_delete_image($productid, 'T');
    func_delete_image($productid, 'P');
    func_delete_image($productid, 'D');

    // Feature comparison module
    if (func_is_defined_module_sql_tbl('Feature_Comparison', 'product_foptions')) {

        db_query("DELETE FROM $sql_tbl[product_features] WHERE productid='$productid'");
        db_query("DELETE FROM $sql_tbl[product_foptions] WHERE productid='$productid'");
    }

    // Product options module
    if (func_is_defined_module_sql_tbl('Product_Options', 'class_options')) {

        $classes = func_query_column("SELECT classid FROM $sql_tbl[classes] WHERE productid='$productid'");
        db_query("DELETE FROM $sql_tbl[classes] WHERE productid='$productid'");
        if (!empty($classes)) {
            $options = func_query_column("SELECT optionid FROM $sql_tbl[class_options] WHERE classid IN ('".implode("','", $classes)."')");
            db_query("DELETE FROM $sql_tbl[class_lng] WHERE classid IN ('".implode("','", $classes)."')");
            if (!empty($options)) {
                db_query("DELETE FROM $sql_tbl[class_options] WHERE classid IN ('".implode("','", $classes)."')");
                db_query("DELETE FROM $sql_tbl[product_options_lng] WHERE optionid IN ('".implode("','", $options)."')");
                db_query("DELETE FROM $sql_tbl[product_options_ex] WHERE optionid IN ('".implode("','", $options)."')");
                db_query("DELETE FROM $sql_tbl[variant_items] WHERE optionid IN ('".implode("','", $options)."')");
                db_query("DELETE FROM $sql_tbl[variant_backups] WHERE optionid IN ('".implode("','", $options)."')");
            }
        }

        db_query("DELETE FROM $sql_tbl[product_options_js] WHERE productid='$productid'");
        $vids = db_query("SELECT variantid FROM $sql_tbl[variants] WHERE productid='$productid'");
        if ($vids) {
            while ($row = db_fetch_array($vids)) {
                func_delete_image($row['variantid'], "W");
            }
            db_free_result($vids);
        }
        db_query("DELETE FROM $sql_tbl[variants] WHERE productid='$productid'");
    }

    // Magnifier module
    if (func_is_defined_module_sql_tbl('Magnifier', 'images_Z')) {

        db_query("DELETE FROM $sql_tbl[images_Z] WHERE id = '$productid'");
        $dir_z = func_image_dir('Z').XC_DS.$productid;
        if (is_dir($dir_z) && file_exists($dir_z))
            func_rm_dir($dir_z);
    }

    // Special Offers
    if (func_is_defined_module_sql_tbl('Special_Offers', 'offer_bonus_params')) {

        db_query("DELETE FROM $sql_tbl[offer_bonus_params] WHERE param_type = 'P' AND param_id = '$productid'");
        db_query("DELETE FROM $sql_tbl[offer_condition_params] WHERE param_type = 'P' AND param_id = '$productid'");
        db_query("DELETE FROM $sql_tbl[offer_product_params] WHERE productid = '$productid'");
    }

    // Subscriptions
    if (func_is_defined_module_sql_tbl('Subscriptions', 'subscriptions')) {

        db_query("DELETE FROM $sql_tbl[subscriptions] WHERE productid='$productid'");
        db_query("DELETE FROM $sql_tbl[subscription_customers] WHERE productid='$productid'");
    }

    db_query("DELETE FROM $sql_tbl[product_taxes] WHERE productid='$productid'");
    db_query("DELETE FROM $sql_tbl[product_votes] WHERE productid='$productid'");
    db_query("DELETE FROM $sql_tbl[product_reviews] WHERE productid='$productid'");
    db_query("DELETE FROM $sql_tbl[products_lng] WHERE productid='$productid'");
    db_query("DELETE FROM $sql_tbl[download_keys] WHERE productid='$productid'");
    db_query("DELETE FROM $sql_tbl[discount_coupons] WHERE productid='$productid'");
    db_query("DELETE FROM $sql_tbl[stats_customers_products] WHERE productid='$productid'");
    db_query("DELETE FROM $sql_tbl[wishlist] WHERE productid='$productid'");
    db_query("DELETE FROM $sql_tbl[product_bookmarks] WHERE productid='$productid'");

    // Product configurator module
    if (func_is_defined_module_sql_tbl('Product_Configurator', 'pconf_products_classes')) {

        $classes = func_query_column("SELECT classid FROM $sql_tbl[pconf_products_classes] WHERE productid='$productid'");
        if (!empty($classes)) {

            // Delete all classification info related with this product

            db_query("DELETE FROM $sql_tbl[pconf_class_specifications] WHERE classid IN ('".implode("','", $classes)."')");
            db_query("DELETE FROM $sql_tbl[pconf_class_requirements] WHERE classid IN ('".implode("','", $classes)."')");
        }

        db_query("DELETE FROM $sql_tbl[pconf_products_classes] WHERE productid='$productid'");

        // Delete configurable product

        $steps = func_query_column("SELECT stepid FROM $sql_tbl[pconf_wizards] WHERE productid='$productid'");
        if (!empty($steps)) {

            // Delete the data related with wizards' steps

            $slots = func_query_column("SELECT slotid FROM $sql_tbl[pconf_slots] WHERE stepid IN ('".implode("','", $steps)."')");
            if (!empty($slots)) {

                // Delete data related with slots

                db_query("DELETE FROM $sql_tbl[pconf_slots] WHERE stepid IN ('".implode("','", $steps)."')");
                db_query("DELETE FROM $sql_tbl[pconf_slot_rules] WHERE slotid IN ('".implode("','", $slots)."')");
                db_query("DELETE FROM $sql_tbl[pconf_slot_markups] WHERE slotid IN ('".implode("','", $slots)."')");
            }
        }

        db_query("DELETE FROM $sql_tbl[pconf_wizards] WHERE productid='$productid'");
    }

    // Update product count for categories

    if ($update_categories && !empty($product_categories)) {
        $cats = array();
        foreach ($product_categories as $c) {
            $cats = array_merge($cats, func_get_category_path($c));
        }
        $cats = array_unique($cats);
        func_recalc_product_count($cats);
    }

    func_data_cache_get('fc_count', array('Y'), true);
    func_data_cache_get('fc_count', array('N'), true);

    db_query("DELETE FROM $sql_tbl[quick_flags] WHERE productid = '$productid'");
    db_query("DELETE FROM $sql_tbl[quick_prices] WHERE productid = '$productid'");

    // Delete Clean URLs data.
    db_query("DELETE FROM $sql_tbl[clean_urls] WHERE resource_type = 'P' AND resource_id = '$productid'");
    db_query("DELETE FROM $sql_tbl[clean_urls_history] WHERE resource_type = 'P' AND resource_id = '$productid'");

    func_export_range_erase('PRODUCTS', $productid);

    return true;
}

/**
 * Search for products in products database
 */
function func_search_products($query, $membershipid, $orderby = '', $limit = '', $id_only = false)
{
    global $current_area, $user_account, $active_modules, $xcart_dir, $current_location, $single_mode;
    global $store_language, $sql_tbl, $shop_language;
    global $config;
    global $cart, $logged_userid;
    global $active_modules;

    static $orderby_rules = NULL;

    x_load(
        'files',
        'taxes'
    );

    if (is_null($orderby_rules)) {
        $orderby_rules = array (
            'title'       => 'product',
            'quantity'    => $sql_tbl['products'] . '.avail',
            'orderby'     => $sql_tbl['products_categories'] . '.orderby',
            'quantity'    => $sql_tbl['products'] . '.avail',
            'price'       => 'price',
            'productcode' => $sql_tbl['products'] . '.productcode',
        );
    }

    // Generate ORDER BY rule

    if (empty($orderby)) {

        $orderby = $config['Appearance']['products_order']
            ? $config['Appearance']['products_order']
            : 'orderby';

        if (!empty($orderby_rules))
            $orderby = $orderby_rules[$orderby];

    }

    // Initialize service arrays

    $fields      = array();
    $from_tbls   = array();
    $inner_joins = array();
    $left_joins  = array();
    $where       = array();
    $groupbys    = array();
    $orderbys    = array();
    $possible_groupbys = array();

    if (is_array($query)) {
        foreach (
            array(
                'fields',
                'from_tbls',
                'inner_joins',
                'left_joins',
                'where',
                'groupbys',
                'orderbys',
            ) as $fn
        ) {
            if (isset($query[$fn]))
                $$fn = $query[$fn];
        }

        $query = isset($query['query']) ? $query['query'] : '';
    }

    // Generate membershipid condition

    $membershipid_condition = '';

    if ($current_area == 'C') {

        $where[] = "(" . $sql_tbl['category_memberships'] . ".membershipid = '" . $membershipid . "' OR " . $sql_tbl['category_memberships'] . ".membershipid IS NULL)";
        $where[] = "(" . $sql_tbl['product_memberships'] . ".membershipid = '" . $membershipid . "' OR " . $sql_tbl['product_memberships'] . ".membershipid IS NULL)";

    }

    // Generate products availability condition

    $inner_joins['pricing'] = array(
        'on' => "$sql_tbl[pricing].productid = $sql_tbl[products].productid AND $sql_tbl[pricing].quantity = '1'"
    );

    $possible_groupbys[] = $sql_tbl['pricing'];
    $possible_groupbys[] = $sql_tbl['products'];

    $inner_joins['products_categories'] = array(
        'on' => "$sql_tbl[products_categories].productid = $sql_tbl[products].productid"
    );

    $possible_groupbys[] = $sql_tbl['products_categories'];

    if (
        $config['General']['check_main_category_only'] == 'Y'
        && $current_area == 'C'
    ) {
        $inner_joins['products_categories']['on'] .= " AND $sql_tbl[products_categories].main = 'Y'";
    }


    $inner_joins['categories'] = array(
        'on' => "$sql_tbl[categories].categoryid = $sql_tbl[products_categories].categoryid AND $sql_tbl[categories].avail = 'Y'"
    );

    $fields[] = $sql_tbl['products'] . '.productid';
    $fields[] = $sql_tbl['products'] . '.provider';

    if ($current_area == 'C') {

        $left_joins['products_lng'] = array(
            'on' => $sql_tbl['products'] . ".productid = " . $sql_tbl['products_lng'] . ".productid AND code = '" . $store_language . "'",
        );

        $fields[] = "IF(" . $sql_tbl['products_lng'] . ".productid != '', " . $sql_tbl['products_lng'] . ".product, " . $sql_tbl['products'] . ".product) as product";

    } else {

        $fields[] = $sql_tbl['products'] . ".product";

    }

    $fields[] = $sql_tbl['products'] . ".productcode";
    $fields[] = $sql_tbl['products'] . ".avail";

    if ($current_area != 'C') {

        $fields[] = "MIN($sql_tbl[pricing].price) as price";

    } else {

        if ($membershipid == 0 || empty($active_modules['Wholesale_Trading'])) {

            $fields[] = "$sql_tbl[pricing].price";

            $membershipid_string = "= '0'";

        } else {

            $fields[] = "MIN($sql_tbl[pricing].price) as price";

            $membershipid_string = "IN ('$membershipid', '0')";

        }

        $inner_joins['quick_prices'] = array(
            'on' => "$sql_tbl[quick_prices].productid = $sql_tbl[products].productid AND $sql_tbl[quick_prices].membershipid $membershipid_string AND $sql_tbl[quick_prices].priceid = $sql_tbl[pricing].priceid"
        );
        $possible_groupbys[] = $sql_tbl['quick_prices'];

    }

    if ($current_area == 'C' && !$single_mode) {

        $inner_joins['ACHECK'] = array(
            'tblname' => 'customers',
            'on' => "$sql_tbl[products].provider=ACHECK.id AND ACHECK.activity='Y'",
        );

    }

    $left_joins['category_memberships'] = array(
        'on' => "$sql_tbl[category_memberships].categoryid = $sql_tbl[categories].categoryid"
    );

    $left_joins['product_memberships'] = array(
        'on' => "$sql_tbl[product_memberships].productid = $sql_tbl[products].productid"
    );

    if (
        empty($membershipid)
        || empty($active_modules['Wholesale_Trading'])
    ) {
        $where[] = "$sql_tbl[pricing].membershipid = '0'";
    } else {
        $where[] = "$sql_tbl[pricing].membershipid IN ('$membershipid', '0')";
    }

    if ($current_area == 'C') {
        
        $inner_joins['pricing']['on'] .= " AND " . $sql_tbl['products'] . ".forsale='Y'";

        if (empty($active_modules['Product_Configurator'])) {
            $inner_joins['pricing']['on'] .= " AND $sql_tbl[products].product_type <> 'C'";
        }
    }

    if (
        $current_area == 'C'
        && !empty($active_modules['Product_Options'])
    ) {

        $where[] = "($sql_tbl[pricing].variantid = '0' OR $sql_tbl[variants].variantid = $sql_tbl[pricing].variantid)";

    } else {

        $where[] = "$sql_tbl[pricing].variantid = '0'";

    }

    if (
        (
            $current_area == 'C'
            || $current_area == 'B'
        )
        && $config['General']['show_outofstock_products'] != 'Y'
    ) {

        if (!empty($active_modules['Product_Options'])) {

            $where[] = "(IFNULL($sql_tbl[variants].avail, $sql_tbl[products].avail) > '0' OR $sql_tbl[products].product_type NOT IN ('','N'))";

        } else {

            $where[] = "($sql_tbl[products].avail > '0' OR $sql_tbl[products].product_type NOT IN ('','N'))";

        }

    }

    $groupbys[] = "$sql_tbl[products].productid";

    if (!empty($orderby))
        $orderbys[] = $orderby;

    // Check if product have prodyct class (Feature comparison)

    if (
        !empty($active_modules['Feature_Comparison'])
        && $current_area == 'C'
    ) {

        global $comparison_list_ids;

        $left_joins['product_features'] = array(
            'on' => "$sql_tbl[product_features].productid = $sql_tbl[products].productid"
        );

        $fields[] = "$sql_tbl[product_features].fclassid";

        if (($config['Feature_Comparison']['fcomparison_show_product_list'] == 'Y') && $config['Feature_Comparison']['fcomparison_max_product_list'] > @count((array)$comparison_list_ids)) {

            $fields[] = "IF($sql_tbl[product_features].fclassid IS NULL || $sql_tbl[product_features].productid IN ('".@implode("','",@array_keys((array)$comparison_list_ids))."'),'','Y') as is_clist";

        }

    }

    // Check if product have product options (Product options)

    if (!empty($active_modules['Product_Options'])) {

        $left_joins['classes'] = array(
            'on' => "$sql_tbl[classes].productid = $sql_tbl[products].productid"
        );

        if ($current_area == 'C') {

            $left_joins['variants'] = array(
                'on' => "$sql_tbl[variants].productid = $sql_tbl[products].productid AND $sql_tbl[quick_prices].variantid = $sql_tbl[variants].variantid",
            );

            $fields[] = "$sql_tbl[quick_prices].variantid";

            global $variant_properties;

            foreach ($variant_properties as $property) {
                $fields[] = "IFNULL($sql_tbl[variants].$property, $sql_tbl[products].$property) as ".$property;
            }

        } else {

            $left_joins['variants'] = array(
                'on' => "$sql_tbl[variants].productid = $sql_tbl[products].productid",
            );

        }

        $fields[] = "IF($sql_tbl[classes].classid IS NULL,'','Y') as is_product_options";
        $fields[] = "IF($sql_tbl[variants].variantid IS NULL,'','Y') as is_variant";

    }

    if ($config['setup_images']['T']['location'] == "FS") {

        $left_joins['images_T'] = array(
            'on' => "$sql_tbl[images_T].id = $sql_tbl[products].productid"
        );

        $fields[] = "$sql_tbl[images_T].image_path";

    }

    if ($current_area == 'C') {

        $left_joins['product_taxes'] = array(
            'on' => "$sql_tbl[product_taxes].productid = $sql_tbl[products].productid"
        );

        $fields[] = "$sql_tbl[product_taxes].taxid";

    }

    // Generate search query

    foreach ($inner_joins as $j) {
        if (!empty($j['fields']) && is_array($j['fields']))
            $fields = func_array_merge($fields, $j['fields']);
    }

    foreach ($left_joins as $j) {
        if (!empty($j['fields']) && is_array($j['fields']))
            $fields = func_array_merge($fields, $j['fields']);
    }

    if ($id_only != true) {
        $search_query = "SELECT " . implode(", ", $fields) . " FROM ";
    } else {
        $search_query = "SELECT $sql_tbl[products].productid FROM ";
    }

    if (!empty($from_tbls)) {

        foreach ($from_tbls as $k => $v) {
            $from_tbls[$k] = $sql_tbl[$v];
        }

        $search_query .= implode(", ", $from_tbls) . ", ";

    }

    $search_query .= $sql_tbl['products'];

    foreach ($inner_joins as $ijname => $ij) {

        $search_query .= " INNER JOIN ";

        if (!empty($ij['tblname'])) {
            $search_query .= $sql_tbl[$ij['tblname']]." as ".$ijname;
        } else {
            $search_query .= $sql_tbl[$ijname];
        }

        $search_query .= " ON ".$ij['on'];

        foreach ($left_joins as $ljname => $lj) {

            if (empty($lj['parent']) || $lj['parent'] != $ijname)
                continue;

            $search_query .= " LEFT JOIN ";

            if (!empty($lj['tblname'])) {
                $search_query .= $sql_tbl[$lj['tblname']]." as ".$ljname;
            } else {
                $search_query .= $sql_tbl[$ljname];
            }

            $search_query .= " ON ".$lj['on'];

        }

    }

    foreach ($left_joins as $ljname => $lj) {

        if (!empty($lj['parent']))
            continue;

        $search_query .= " LEFT JOIN ";

        if (!empty($lj['tblname'])) {
            $search_query .= $sql_tbl[$lj['tblname']] . " as " . $ljname;
        } else {
            $search_query .= $sql_tbl[$ljname];
        }

        $search_query .= " ON ".$lj['on'];

    }

    if (!empty($where))
        $search_query .= " WHERE " . implode(" AND ", $where);
    
    $search_query .= $query;

    if (
        count($groupbys) == 1 
        && $groupbys[0] == "$sql_tbl[products].productid"
    ) {
        $groupbys = func_get_low_cost_sql_groupby($search_query, $possible_groupbys, array(), $orderbys, $groupbys, 'productid');
    }

    if (!empty($groupbys))
        $search_query .= " GROUP BY " . implode(", ", $groupbys);

    if (!empty($orderbys))
        $search_query .= " ORDER BY " . implode(", ", $orderbys);

    $limit = max(intval($limit), 0);

    if (!empty($limit))
        $search_query .= " LIMIT " . $limit;

    db_query("SET OPTION SQL_BIG_SELECTS=1");

    $result = func_query($search_query, USE_SQL_DATA_CACHE);

    if ($id_only == true) {
        return $result;
    }

    $ids = array();

    if (!empty($result)) {
        foreach($result as $v) {
            $ids[] = $v['productid'];
        }
    }

    if (
        $result
        && (
            $current_area == 'C'
            || $current_area == 'B'
        )
    ) {
        // Post-process the result products array

        if (!empty($active_modules['Extra_Fields'])) {
            $products_ef = func_query_hash("SELECT $sql_tbl[extra_fields].*, $sql_tbl[extra_field_values].*, IF($sql_tbl[extra_fields_lng].field != '', $sql_tbl[extra_fields_lng].field, $sql_tbl[extra_fields].field) as field FROM $sql_tbl[extra_field_values], $sql_tbl[extra_fields] LEFT JOIN $sql_tbl[extra_fields_lng] ON $sql_tbl[extra_fields].fieldid = $sql_tbl[extra_fields_lng].fieldid AND $sql_tbl[extra_fields_lng].code = '$shop_language' WHERE $sql_tbl[extra_fields].fieldid = $sql_tbl[extra_field_values].fieldid AND $sql_tbl[extra_field_values].productid IN (".implode(",", $ids).") AND $sql_tbl[extra_fields].active = 'Y' ORDER BY $sql_tbl[extra_fields].orderby", "productid");
        }

        $thumb_dims = func_query_hash("SELECT id, image_x, image_y FROM $sql_tbl[images_T] WHERE id IN ('" . implode("','", $ids) . "')", 'id', false);


        if (
            !empty($active_modules['Product_Options'])
            && !empty($ids)
        ) {
            $options_markups = func_get_default_options_markup_list($ids);
        }

        foreach ($result as $key => $value) {

            $value['taxed_price'] = $result[$key]['taxed_price'] = $value['price'];

            if (
                !empty($active_modules['Product_Options'])
                && !empty($options_markups[$value['productid']])
            ) {
                // Add product options markup
                if ($result[$key]['price'] != 0)
                    $result[$key]['price'] += $options_markups[$value['productid']];

                $result[$key]['taxed_price'] = $result[$key]['price'];

                $value = $result[$key];

            }

            if (
                !empty($cart)
                && !empty($cart['products'])
                && $current_area == 'C'
            ) {

                // Update quantity for products that already placed into the cart

                $in_cart = 0;

                foreach ($cart['products'] as $cart_item) {
                    if (
                        $cart_item['productid'] == $value['productid']
                        && $cart_item['variantid'] == $value['productid']['variantid']
                    ) {
                        $in_cart += $cart_item['amount'];
                    }
                }

                $result[$key]['avail'] -= $in_cart;

            }

            if (!empty($active_modules['Extra_Fields'])) {

                if (isset($products_ef[$value['productid']])) {
                    $result[$key]['extra_fields'] = $products_ef[$value['productid']];
                }

            }

            // Get thumbnail's URL (uses only if images stored in FS)

            if ($config['setup_images']['T']['location'] == "FS")
                $value['is_thumbnail'] = !is_null($value['image_path']);

            $image_ids['T'] = $value['productid'];
            $image_ids['P'] = $value['productid'];

            if (isset($value['is_thumbnail']) && $value['is_thumbnail'] && !empty($value['image_path'])) {

                // FS thumbnail is available. It is not required to process P image
                $image_data['image_url'] = func_get_image_url($value['productid'], 'T', $value['image_path']);

            } elseif ($config['setup_images']['T']['location'] == "FS") {

                // FS thumbnail is not available. It is not required to process T image
                unset($image_ids['T']);

                $image_data = func_get_image_url_by_types($image_ids, 'P');

            } else {

                $image_data = func_get_image_url_by_types($image_ids, 'T');

            }   

            $result[$key]['tmbn_url'] = $image_data['image_url'];

            unset($result[$key]['image_path']);

            $dims_tmp = isset($thumb_dims[$value['productid']]) 
                ? $thumb_dims[$value['productid']]
                : $config['setup_images']['T']; 

            $result[$key] = func_array_merge($result[$key], $dims_tmp);

            $_limit_width = $config['Appearance']['simple_thumbnail_width'];
            $_limit_height = $config['Appearance']['simple_thumbnail_height'];
            $result[$key] = func_get_product_tmbn_dims($result[$key], $_limit_width, $_limit_height);

            if (
                $current_area == 'C'
                && $value['taxid'] > 0
            ) {
                $result[$key]['taxes'] = func_get_product_taxes($result[$key], $logged_userid);
            }

        } // foreach ($result as $key => $value)

    }

    return $result;
}

/**
 * Put all product info into $product array
 */
function func_select_product($id, $membershipid, $redirect_if_error = true, $clear_price = false, $always_select = false, $prefered_image_type = 'P')
{
    global $logged_userid, $login_type, $current_area, $single_mode, $cart, $current_location;
    global $store_language, $sql_tbl, $config, $active_modules;
    global $REQUEST_METHOD; //To avoid PHP notices in modules/Google_Checkout/product_modify.php included below

    x_load('files','taxes', 'image');

    $in_cart = 0;

    $id = intval($id);

    $membershipid = intval($membershipid);

    $p_membershipid_condition = $membershipid_condition = '';

    if ($current_area == 'C') {

        $membershipid_condition = " AND ($sql_tbl[category_memberships].membershipid = '$membershipid' OR $sql_tbl[category_memberships].membershipid IS NULL) ";
        $p_membershipid_condition = " AND ($sql_tbl[product_memberships].membershipid = '$membershipid' OR $sql_tbl[product_memberships].membershipid IS NULL) ";
        $price_condition = " AND $sql_tbl[quick_prices].membershipid ".((empty($membershipid) || empty($active_modules['Wholesale_Trading'])) ? "= '0'" : "IN ('$membershipid', '0')")." AND $sql_tbl[quick_prices].priceid = $sql_tbl[pricing].priceid";

    } else {

        $price_condition = " AND $sql_tbl[pricing].membershipid = '0' AND $sql_tbl[products].productid = $sql_tbl[pricing].productid AND $sql_tbl[pricing].quantity = '1' AND $sql_tbl[pricing].variantid = '0'";

    }

    if (
        $current_area == 'C'
        && !empty($cart)
        && !empty($cart['products'])
    ) {

        foreach ($cart['products'] as $cart_item) {

            if ($cart_item['productid'] == $id) {

                $in_cart += $cart_item['amount'];

            }

        }

    }

    $login_condition = '';

    if (!$single_mode) {
        $login_condition = ((!empty($logged_userid) && $login_type == 'P') ? "AND $sql_tbl[products].provider='$logged_userid'" : "");
    }

    $add_fields = '';

    $join = '';

    if ($current_area == 'C') {
        $join .= " INNER JOIN $sql_tbl[quick_flags] ON $sql_tbl[products].productid = $sql_tbl[quick_flags].productid";
    }    

    if (!empty($active_modules['Product_Options']) && $current_area != "C" && $current_area != "B") {
        $join .= " LEFT JOIN $sql_tbl[variants] ON $sql_tbl[products].productid = $sql_tbl[variants].productid";
        $add_fields .= ", IF($sql_tbl[variants].productid IS NULL, '', 'Y') as is_variants";
    }

    if (!empty($active_modules['Feature_Comparison'])) {
        $join .= " LEFT JOIN $sql_tbl[product_features] ON $sql_tbl[product_features].productid = $sql_tbl[products].productid";
        $add_fields .= ", $sql_tbl[product_features].fclassid";
    }

    if (!empty($active_modules['Manufacturers'])) {
        $join .= " LEFT JOIN $sql_tbl[manufacturers] ON $sql_tbl[manufacturers].manufacturerid = $sql_tbl[products].manufacturerid";
        $add_fields .= ", $sql_tbl[manufacturers].manufacturer";
    }

    if (!empty($active_modules['Special_Offers'])) {
        $join .= " LEFT JOIN $sql_tbl[offer_product_params] ON $sql_tbl[offer_product_params].productid = $sql_tbl[products].productid";
        $add_fields .= ", $sql_tbl[offer_product_params].sp_discount_avail, $sql_tbl[offer_product_params].bonus_points";
    }

    if ($current_area == 'C') {

        $add_fields .= ", IF($sql_tbl[products_lng].product != '' AND $sql_tbl[products_lng].product IS NOT NULL, $sql_tbl[products_lng].product, $sql_tbl[products].product) as product, IF($sql_tbl[products_lng].descr != '' AND $sql_tbl[products_lng].descr IS NOT NULL, $sql_tbl[products_lng].descr, $sql_tbl[products].descr) as descr, IF($sql_tbl[products_lng].fulldescr != '' AND $sql_tbl[products_lng].fulldescr IS NOT NULL, $sql_tbl[products_lng].fulldescr, $sql_tbl[products].fulldescr) as fulldescr, $sql_tbl[quick_flags].*, $sql_tbl[quick_prices].variantid, $sql_tbl[quick_prices].priceid";

        if (empty($membershipid) || empty($active_modules['Wholesale_Trading'])) {
            $membershipid_condition = " = '0'";
        } else {
            $membershipid_condition = " IN ('$membershipid', 0)";
        }

        $join .= " INNER JOIN $sql_tbl[quick_prices] ON $sql_tbl[products].productid = $sql_tbl[quick_prices].productid AND $sql_tbl[quick_prices].membershipid $membershipid_condition LEFT JOIN $sql_tbl[products_lng] ON $sql_tbl[products_lng].code='$store_language' AND $sql_tbl[products_lng].productid = $sql_tbl[products].productid ";
    }

    $join .= " LEFT JOIN $sql_tbl[product_memberships] ON $sql_tbl[product_memberships].productid = $sql_tbl[products].productid";

    if ($current_area == 'C' && empty($active_modules['Product_Configurator'])) {
        $login_condition .= " AND $sql_tbl[products].product_type <> 'C' AND $sql_tbl[products].forsale <> 'B' ";
    }

    $join .= " LEFT JOIN $sql_tbl[clean_urls] ON $sql_tbl[clean_urls].resource_type = 'P' AND $sql_tbl[clean_urls].resource_id = $sql_tbl[products].productid";

    $add_fields .= ", $sql_tbl[clean_urls].clean_url, $sql_tbl[clean_urls].mtime";

    $product = func_query_first("SELECT $sql_tbl[products].*, $sql_tbl[products].avail-$in_cart AS avail, MIN($sql_tbl[pricing].price) as price $add_fields FROM $sql_tbl[pricing] INNER JOIN $sql_tbl[products] ON $sql_tbl[pricing].productid = $sql_tbl[products].productid AND $sql_tbl[products].productid='$id' $join WHERE 1 ".$login_condition.$p_membershipid_condition.$price_condition." GROUP BY $sql_tbl[products].productid");

    $categoryid = func_query_first_cell("SELECT $sql_tbl[products_categories].categoryid FROM $sql_tbl[products_categories] INNER JOIN $sql_tbl[categories] ON $sql_tbl[products_categories].categoryid=$sql_tbl[categories].categoryid LEFT JOIN $sql_tbl[category_memberships] ON $sql_tbl[category_memberships].categoryid = $sql_tbl[categories].categoryid WHERE $sql_tbl[products_categories].productid = '$id' ORDER BY main DESC");

    // Check product's provider activity
    if (
        !$single_mode
        && $current_area == 'C'
        && !empty($product)
    ) {
        if (!func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[customers] WHERE id = '$product[provider]' AND activity='Y'"))
            $product = array();
    }

    // Error handling

    if (
        !$product
        || !$categoryid
    ) {

        if ($redirect_if_error) {

            $product_is_exists = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products] WHERE productid = '$id'") > 0;

            if ($product_is_exists) {
                func_403(33);
            } else {
                func_page_not_found();
            }

        } else {

            return false;

        }

    }

    $product['productid'] = $id;
    $product['categoryid'] = $categoryid;

    $tmp = func_query_column("SELECT membershipid FROM $sql_tbl[product_memberships] WHERE productid = '$product[productid]'");

    if (!empty($tmp) && is_array($tmp)) {

        $product['membershipids'] = array();

        foreach ($tmp as $v) {
            $product['membershipids'][$v] = 'Y';
        }
    }

    if (!empty($product['variantid']) && !empty($active_modules['Product_Options'])) {

        $tmp = func_query_first("SELECT * FROM $sql_tbl[variants] WHERE variantid = '$product[variantid]'");

        if (!empty($tmp)) {

            func_unset($tmp, 'def');

            $product = func_array_merge($product, $tmp);

        } else {

            func_unset($product, 'variantid');

        }

    }

    // Detect product thumbnail and image
    $image_ids = array();

    if (
        !empty($product['variantid'])
        && !empty($active_modules['Product_Options'])
        && (
            $current_area == 'C'
            || $current_area == 'B'
        )
    ) {
        $image_ids['W'] = $product['variantid'];
    }

    $image_ids['P'] = $product['productid'];
    $image_ids['T'] = $product['productid'];

    $image_data = func_get_image_url_by_types($image_ids, $prefered_image_type);

    $product['taxed_price'] = $product['price'];

    if (
        $current_area == 'C'
        || $current_area == 'B'
    ) {

        // Check if product is not available for sale

        if (empty($active_modules['Egoods']))
            $product['distribution'] = '';

        global $pconf;

        if ($product['forsale'] == 'B' && empty($pconf)) {

            if (
                isset($cart['products'])
                && is_array($cart['products'])
            ) {

                foreach ($cart['products'] as $k => $v) {

                    if ($v['productid'] == $product['productid']) {

                        $pconf = $product['productid'];

                        break;

                    }

                }

            }

            if (empty($pconf)) {

                x_session_register('configurations');

                global $configurations;

                if (!empty($configurations)) {

                    foreach ($configurations as $c) {

                        if (empty($c['steps']) || !is_array($c['steps']))
                            continue;

                        foreach ($c['steps'] as $s) {
                            if (empty($s['slots']) || !is_array($s['slots']))
                                continue;

                            foreach($s['slots'] as $sl) {
                                if ($sl['productid'] == $product["productid"]) {
                                    $pconf = $product['productid'];
                                    break;
                                }
                            }

                        }

                    }

                }

            }

        }

        if (
            !$always_select
            && (
                $product['forsale'] == 'N'
                || (
                    $product['forsale'] == 'B'
                    && empty($pconf)
                )
            )
        ) {

            if ($redirect_if_error)
                func_header_location("error_message.php?product_disabled");
            else
                return false;

        }

        if (
            $current_area == 'C'
            && !$clear_price
        ) {

            // Calculate taxes and price including taxes

            global $logged_userid;

            $orig_price = $product['price'];

            $product['taxes'] = func_get_product_taxes($product, $logged_userid);

            // List price corrections
            if (($product['taxed_price'] != $orig_price) && ($product['list_price'] > 0))
                $product['list_price'] = price_format($product['list_price'] * $product['taxed_price'] / $orig_price);
        }

    } else {

        $product['is_thumbnail'] = func_query_first_cell("SELECT id FROM $sql_tbl[images_T] WHERE id = '$product[productid]'") != false;
        $product['is_pimage'] = func_query_first_cell("SELECT id FROM $sql_tbl[images_P] WHERE id = '$product[productid]'") != false;

        if ($product['is_thumbnail']) {

            list($x, $y) = func_crop_dimensions(
                $image_data['images']['T']['x'],
                $image_data['images']['T']['y'],
                $config['images_dimensions']['T']['width'],
                $config['images_dimensions']['T']['height']
            );

            if (
                $image_data['images']['T']['x'] <= $x
                && $image_data['images']['T']['y'] <= $y
            ) {
                $x = $image_data['images']['T']['x'];
                $y = $image_data['images']['T']['y'];
            }

            $image_data['images']['T']['new_x'] = $x;
            $image_data['images']['T']['new_y'] = $y;

        }

        if ($product['is_pimage']) {

            list($x, $y) = func_crop_dimensions(
                $image_data['images']['P']['x'],
                $image_data['images']['P']['y'],
                $config['images_dimensions']['P']['width'],
                $config['images_dimensions']['P']['height']
            );

            if (
                $image_data['images']['P']['x'] <= $x
                && $image_data['images']['P']['y'] <= $y
            ) {
                $x = $image_data['images']['P']['x'];
                $y = $image_data['images']['P']['y'];
            }

            $image_data['images']['P']['new_x'] = $x;
            $image_data['images']['P']['new_y'] = $y;

        }

    }

    if (!empty($active_modules['Google_Checkout']) > 0) {

        global $xcart_dir;

        include $xcart_dir . '/modules/Google_Checkout/product_modify.php';

    }

    // Add product features
    if (
        !empty($active_modules['Feature_Comparison'])
        && $product['fclassid'] > 0
    ) {
        $product['features'] = func_get_product_features($product['productid']);
        $product['is_clist'] = func_check_comparison($product['productid'], $product['fclassid']);
    }

    if (
        !empty($active_modules['Special_Offers'])
        && empty($product['sp_discount_avail'])
    ) {
        $product['sp_discount_avail'] = 'Y';
    }

    $product['producttitle'] = $product['product'];

    if (
        $current_area == 'C'
        || $current_area == 'B'
    ) {

        $product['descr']         = func_eol2br($product['descr']);
        $product['fulldescr']     = func_eol2br($product['fulldescr']);

        $product['allow_active_content'] = func_get_allow_active_content($product['provider']);

        if (!$product['allow_active_content']) {
            $product['descr']         = func_xss_free($product['descr']);
            $product['fulldescr']     = func_xss_free($product['fulldescr']);
        }

    }

    // Get thumbnail's URL (uses only if images stored in FS)

    if (is_array($image_data)) {

        if (
            $current_area == 'C'
            || $current_area == 'B'
        ) {
            list($image_data['image_x'], $image_data['image_y']) = func_crop_dimensions(
                $image_data['image_x'],
                $image_data['image_y'],
                $config['Appearance']['image_width'],
                $config['Appearance']['image_height']
            );
        }

        $product = array_merge($product, $image_data);

    }

    $product['clean_urls_history'] = func_query_hash("SELECT id, clean_url FROM $sql_tbl[clean_urls_history] WHERE resource_type = 'P' AND resource_id = '".$product['productid']."' ORDER BY mtime DESC", "id", false, true);

    $product['appearance'] = func_get_appearance_data($product);

    if (
        !empty($active_modules['Customer_Reviews'])
        && $config['Customer_Reviews']['customer_voting'] == 'Y'
    ) {
        $product['rating_data'] = func_get_product_rating($product['productid']);
    }

    if (
        $current_area != 'C'
        && $current_area != 'B'
    ) {
        $product['add_categoryids'] = func_query_hash($a = "SELECT categoryid, productid FROM $sql_tbl[products_categories] WHERE main = 'N' AND productid='$id'", 'categoryid', false, true);
    }
    
    return $product;
}

/**
 * Get delivery options by product ID
 */
function func_select_product_delivery($id)
{
    global $sql_tbl;

    return func_query("select $sql_tbl[shipping].*, count($sql_tbl[delivery].productid) as avail from $sql_tbl[shipping] left join $sql_tbl[delivery] on $sql_tbl[delivery].shippingid=$sql_tbl[shipping].shippingid and $sql_tbl[delivery].productid='$id' where $sql_tbl[shipping].active='Y' group by shippingid");
}

/**
 * Add data to service array (Group editing of products functionality)
 */
function func_ge_add($data, $geid = false)
{
    global $sql_tbl, $XCARTSESSID;

    if (strlen($geid) < 32)
        $geid = md5(uniqid(rand(0, XC_TIME)));

    if (!is_array($data))
        $data = array($data);

    $query_data = array(
        'sessid' => $XCARTSESSID,
        'geid'   => $geid,
    );

    foreach ($data as $pid) {

        if (empty($pid))
            continue;

        $query_data['productid'] = $pid;

        func_array2insert(
            'ge_products',
            $query_data
        );

    }

    return $geid;
}

/**
 * Get length of service array (Group editing of products functionality)
 */
function func_ge_count($geid)
{
    global $sql_tbl;

    return func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[ge_products] WHERE geid = '$geid'");
}

/**
 * Get next line of service array (Group editing of products functionality)
 */
function func_ge_each($geid, $limit = 1, $productid = 0)
{
    global $__ge_res, $sql_tbl;

    if (
        !is_bool($__ge_res)
        && (
            !is_resource($__ge_res)
            || strpos(get_resource_type($__ge_res), "mysql ") !== 0
        )
    ) {
        $__ge_res = false;
    }

    if ($__ge_res === true) {

        $__ge_res = false;

        return false;

    } elseif ($__ge_res === false) {

        $__ge_res = db_query("SELECT productid FROM $sql_tbl[ge_products] WHERE geid = '$geid'");

        if (!$__ge_res) {

            $__ge_res = false;

            return false;

        }

    }

    $res = true;
    $ret = array();

    $limit = intval($limit);

    if ($limit <= 0)
        $limit = 1;

    $orig_limit = $limit;

    while (($limit > 0) && ($res = db_fetch_row($__ge_res))) {

        if ($productid == $res[0])
            continue;

        $ret[] = $res[0];

        $limit--;

    }

    if (!$res) {

        func_ge_reset($geid);

        $__ge_res = !empty($ret);

    }

    if (empty($ret))
        return false;

    return ($orig_limit == 1)
        ? $ret[0]
        : $ret;
}

/**
 * Check element of service array (Group editing of products functionality)
 */
function func_ge_check($geid, $id)
{
    global $sql_tbl;

    return (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[ge_products] WHERE geid = '$geid' AND productid = '$id'") > 0);
}

/**
 * Reset pointer of service array (Group editing of products functionality)
 */
function func_ge_reset($geid)
{
    global $__ge_res;

    if ($__ge_res !== false)
        @db_free_result($__ge_res);

    $__ge_res = false;
}

/**
 * Get stop words list
 */
function func_get_stopwords($code = false)
{
    global $xcart_dir, $shop_language;

    if ($code === false)
        $code = $shop_language;

    if (!file_exists($xcart_dir . '/include/stopwords_' . $code . '.php'))
        return false;

    $stopwords = array();

    include $xcart_dir . '/include/stopwords_' . $code  .'.php';

    return $stopwords;
}

/**
 * Create unique product code (SKU)
 */
function func_generate_sku($provider, $prefix = false, $max = 0)
{
    global $sql_tbl, $logged_userid, $active_modules;

    if (empty($prefix))
        $prefix = 'SKU';

    if (empty($provider))
        $provider = $logged_userid;

    if (strlen($prefix) > 26)
        $prefix = 'SKU';

    $len = strlen($prefix);
    $cnt = 100;

    $max_p = intval(func_query_first_cell("SELECT MAX(SUBSTRING(productcode, ".($len+1).")) as max FROM $sql_tbl[products] WHERE SUBSTRING(productcode, 1, $len) = '".addslashes($prefix)."' AND provider = '$provider'"));

    $max_v = empty($active_modules['Product_Options'])
        ? 0
        : intval(func_query_first_cell("SELECT MAX(SUBSTRING($sql_tbl[variants].productcode, ".($len+1).")) as max FROM $sql_tbl[variants], $sql_tbl[products] WHERE SUBSTRING($sql_tbl[variants].productcode, 1, $len) = '".addslashes($prefix)."' AND $sql_tbl[products].provider = '$provider' AND $sql_tbl[products].productid = $sql_tbl[variants].productid"));

    $max = max($max_p, $max_v);

    do {

        $sku_new = $prefix . ++$max;

    } while (
        strlen($sku_new) < 33
        && !func_sku_is_unique($sku_new, $provider)
        && $cnt-- > 0
    );

    if (
        strlen($sku_new) > 32
        || !func_sku_is_unique($sku_new, $provider)
    ) {

        $cnt = 100;

        do {

            $sku_new = substr($prefix . md5(uniqid('SKU', true)), 0, 32);

        } while (
            !func_sku_is_unique($sku_new, $provider)
            && $cnt-- > 0
        );

        if (!func_sku_is_unique($sku_new, $provider)) {

            do {

                $sku_new = md5(uniqid('SKU', true));

            }  while (!func_sku_is_unique($sku_new, $provider));

        }

    }

    return $sku_new;
}

/**
 * Check SKU - exists or not
 */
function func_sku_is_unique($sku, $provider)
{
    global $sql_tbl, $active_modules;

    if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products] WHERE productcode = '" . addslashes($sku) . "' AND provider = '" . $provider . "'") > 0)
        return false;

    return (
        empty($active_modules['Product_Options'])
        || func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[variants] WHERE $sql_tbl[variants].productcode = '" . addslashes($sku) . "'") == 0
    );
}

/**
 * Get product's title
 */
function func_get_product_title($productid)
{
    global $sql_tbl, $config;

    if (!is_int($productid) || $productid < 1)
        return false;

    $product = func_query_first("SELECT $sql_tbl[products].title_tag, $sql_tbl[categories].categoryid FROM $sql_tbl[products] INNER JOIN $sql_tbl[products_categories] ON $sql_tbl[products].productid = $sql_tbl[products_categories].productid AND $sql_tbl[products_categories].main = 'Y' INNER JOIN $sql_tbl[categories] ON $sql_tbl[categories].categoryid = $sql_tbl[products_categories].categoryid WHERE $sql_tbl[products].productid = '$productid'");

    if (!is_array($product) || count($product) == 0)
        return false;

    $product['title_tag'] = trim($product['title_tag']);

    if (empty($product['title_tag'])) {

        x_load('category');

        $ids = array_reverse(func_get_category_path($product['categoryid']));

        $parents = func_query_hash("SELECT categoryid, title_tag FROM $sql_tbl[categories] WHERE categoryid IN ('".implode("', '", $ids)."') AND override_child_meta = 'Y'", "categoryid", false);

        while ((list(, $cid) = each($ids)) && empty($product['title_tag'])) {

            $parents[$cid]['title_tag'] = trim($parents[$cid]['title_tag']);

            if (empty($product['title_tag']) && !empty($parents[$cid]['title_tag']))
                $product['title_tag'] = $parents[$cid]['title_tag'];

        }

    }

    if (empty($product['title_tag']))
        $product['title_tag'] = trim($config['SEO']['site_title']);

    return $product['title_tag'];
}

/**
 * Get product's meta description and meta keywords data
 */
function func_get_product_meta($productid)
{
    global $sql_tbl, $config;

    if (!is_int($productid) || $productid < 1)
        return false;

    $product = func_query_first("SELECT $sql_tbl[products].meta_description, $sql_tbl[products].meta_keywords, $sql_tbl[categories].categoryid FROM $sql_tbl[products] INNER JOIN $sql_tbl[products_categories] ON $sql_tbl[products].productid = $sql_tbl[products_categories].productid AND $sql_tbl[products_categories].main = 'Y' INNER JOIN $sql_tbl[categories] ON $sql_tbl[categories].categoryid = $sql_tbl[products_categories].categoryid WHERE $sql_tbl[products].productid = '$productid'");

    if (!is_array($product) || count($product) == 0)
        return false;

    $product['meta_description'] = trim($product['meta_description']);
    $product['meta_keywords']    = trim($product['meta_keywords']);

    if (
        empty($product['meta_description'])
        || empty($product['meta_keywords'])
    ) {

        $ids = array_reverse(func_get_category_path($product['categoryid']));

        $parents = func_query_hash("SELECT categoryid, meta_description, meta_keywords FROM $sql_tbl[categories] WHERE categoryid IN ('".implode("', '", $ids)."') AND override_child_meta = 'Y'", "categoryid", false);

        while ((list(,$cid) = each($ids)) && (empty($product['meta_description']) || empty($product['meta_keywords']))) {

            $parents[$cid]['meta_description']     = trim($parents[$cid]['meta_description']);
            $parents[$cid]['meta_keywords']     = trim($parents[$cid]['meta_keywords']);

            if (empty($product['meta_description']) && !empty($parents[$cid]['meta_description']))
                $product['meta_description'] = $parents[$cid]['meta_description'];

            if (empty($product['meta_keywords']) && !empty($parents[$cid]['meta_keywords']))
                $product['meta_keywords'] = $parents[$cid]['meta_keywords'];
        }
    }

    if (empty($product['meta_description']))
        $product['meta_description'] = trim($config['SEO']['meta_descr']);

    if (empty($product['meta_keywords']))
        $product['meta_keywords'] = trim($config['SEO']['meta_keywords']);

    return array(
        $product['meta_description'],
        $product['meta_keywords'],
    );
}

function func_get_allow_active_content($provider)
{
    global $sql_tbl, $active_modules;

    if (!empty($active_modules['Simple_Mode']))
        return true;

    $user = func_query_first("SELECT usertype, trusted_provider FROM $sql_tbl[customers] WHERE id='$provider'");

    return ($user['trusted_provider'] == 'Y' || $user["usertype"] != "P");
}

/**
 * Get appearance product service data
 */
function func_get_appearance_data($product)
{
    global $config, $active_modules, $current_area, $login, $is_comparison_list;

    $appearance = array(
        'empty_stock'   => $config['General']['unlimited_products'] != "Y"
            && (
                $product['avail'] <= 0
                || $product['avail'] < $product['min_amount']
            ),

        'has_price' => $product['taxed_price'] > 0
            || (
                !empty($product['variantid'])
                && isset($product['variants_has_price'])
                && !empty($product['variants_has_price'])
            ),

        'has_market_price' => $product['list_price'] > 0
            && $product['taxed_price'] < $product['list_price'],

        'buy_now_enabled'  => $current_area == 'C'
            && $config['Appearance']['buynow_button_enabled'] == "Y",

        'buy_now_form_enabled' => $product['price'] > 0
            || (
                !empty($active_modules['Special_Offers'])
                && isset($product['use_special_price'])
            ) || $product['product_type'] == 'C',

        'min_quantity' => max(1, $product['min_amount']),

        'max_quantity' => $config['General']['unlimited_products'] == "Y"
            ?       max($config['Appearance']['max_select_quantity'], $product['min_amount'])
            : min(  max($config['Appearance']['max_select_quantity'], $product['min_amount']), $product['avail']),

        'buy_now_buttons_enabled' => $config['General']['unlimited_products'] == "Y"
            || (
                $product['avail'] > 0
                && $product['avail'] >= $product['min_amount']
            ) || (
                !empty($product['variantid'])
                && $product['avail'] > 0
            ),

        'force_1_amount' => $product['distribution']
            || (
                !empty($active_modules['Subscriptions'])
                && !empty($product['catalogprice'])
            ),
    );

    $appearance['quantity_input_box_enabled'] = $config['Appearance']['show_quantity_as_box'] == 'Y';

    $appearance['is_auction'] = !(
        (
            $appearance['empty_stock']
            && !empty($product['variantid'])
        ) || (
            $product['taxed_price'] != 0
            || (
                !empty($product['variantid'])
                && isset($product['variants_has_price'])
                && $product['variants_has_price']
            ) || (
                !empty($active_modules['Special_Offers'])
                && isset($product['use_special_price'])
                && $product['use_special_price']
            )
        )
    );

    if ($appearance['has_market_price'])
        $appearance['market_price_discount'] = sprintf("%3.0f", 100 - ($product['taxed_price'] / $product['list_price']) * 100);

    $cart_enabled_product_options = isset($product['is_product_options']) && $product['is_product_options'] == 'Y'
        ? $config['Product_Options']['buynow_with_options_enabled'] != 'Y'
        : true;

    $cart_enabled_avail = $config['General']['unlimited_products'] == "Y"
        ? true
        : $product['avail'] > 0 || empty($product['variantid']) || !$product['variantid'];

    $appearance['buy_now_cart_enabled'] = $appearance['buy_now_form_enabled'] && $cart_enabled_product_options && $cart_enabled_avail;

    $appearance['loop_quantity'] = $appearance['max_quantity'] + 1;

    $appearance['buy_now_add2wl_enabled'] = (
        $login
        || $config['Wishlist']['add2wl_unlogged_user'] == 'Y'
    ) && !empty($active_modules['Wishlist'])
    && $appearance['buy_now_buttons_enabled'];

    // Add to list button
    global $giftreg_events;

    if (
        $appearance['buy_now_add2wl_enabled']
        && (
            (
                !empty($active_modules['Feature_Comparison'])
                && !empty($product['fclassid'])
            ) || (
                !empty($active_modules['Gift_Registry'])
                && isset($giftreg_events)
                && !empty($giftreg_events)
            )
        )
    ) {
        $appearance['dropout_actions'] = array(
            'W' => true,
            'C' => (!empty($active_modules['Feature_Comparison']) && (!empty($product["fclassid"]))),
            'G' => (!empty($active_modules['Gift_Registry']) && !empty($giftreg_events)),
        );
    }

    return $appearance;
}

/**
 * Correct some depending ship box data settings bt:84873
 */
function func_adjust_ship_box_data($data, $small_item = 'N', $separate_box = 'N', $items_per_box = 1)
{
    if (func_num_args() == 1 && is_array($data))
        extract($data);

    $items_per_box = intval($items_per_box);

    if ($small_item == 'Y') {
        $data['separate_box'] = 'N';
    }

    if ($separate_box == 'Y')
        $data['items_per_box'] = ($items_per_box > 0) ? $items_per_box : 1;

    return $data;
}

/*
 * Get product thumbnail image dims with defined limits
 */
function func_get_product_tmbn_dims($product, $limit_width, $limit_height)
{
    if (
        !empty($product['image_x']) 
        && !empty($product['image_y'])
    ) {
        x_load('image');

        $product['tmbn_x'] = $product['image_x'];
        $product['tmbn_y'] = $product['image_y'];

        $need_resize = ($product['tmbn_x'] > $limit_width || $product['tmbn_y'] > $limit_height);

        if ($need_resize) {
            list(
                $product['tmbn_x'],
                $product['tmbn_y']
            ) = func_get_proper_dimensions(
                $product['image_x'],
                $product['image_y'],
                $limit_width,
                $limit_height
            );
        }
    }

    return $product;
}

?>
