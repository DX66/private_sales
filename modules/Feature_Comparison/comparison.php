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
 * $Id: comparison.php,v 1.53.2.2 2011/01/10 13:11:56 ferz Exp $
 */
// Product comparison function

if (!defined('XCART_SESSION_START')) { header("Location: ../../"); die("Access denied"); }

if (empty($active_modules['Feature_Comparison'])) {
    func_403(53);
}

$pconf_str = '';

if (
    !empty($active_modules['Product_Configurator'])
    && isset($pconf_productid)
    && isset($pconf_slot)
) {
    $pconf_productid = intval($pconf_productid);
    $pconf_slot = intval($pconf_slot);

    $pconf_str = "&pconf_productid=$pconf_productid&pconf_slot=$pconf_slot";

    $smarty->assign('pconf_productid',  $pconf_productid);
    $smarty->assign('pconf_slot',       $pconf_slot);
}

$show = in_array($show, array('', 'popup')) ? $show : '';

$sort_direction = in_array($sort_direction, array(0, 1)) ? $sort_direction : 0;

$sort = in_array($sort, array('price', 'title', 'productcode')) ? $sort : 'price';

// Define redirect URL
$redirect_url = "comparison.php?show=$show&sort=$sort&sort_direction=$sort_direction" . (($pconf_str) ? $pconf_str : '');

x_session_register('store_productids');
x_session_register('store_comp_options');

if (!empty($comp_options)) {

    $store_comp_options = func_array_merge($store_comp_options, $comp_options);

    func_header_location($redirect_url);

}

$comp_options = $store_comp_options;

// Get data for comparison
if ($mode == 'get_products') {

    if (is_array($productids) && @count($productids) > 0) {

        if (isset($productid))
            $productids[$productid] = 'Y';

        foreach ($productids as $k => $v) {
            if ($v != 'Y') {
                unset($productids[$k]);

            } elseif (!func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[product_features], $sql_tbl[feature_classes] WHERE $sql_tbl[product_features].fclassid = $sql_tbl[feature_classes].fclassid AND $sql_tbl[feature_classes].avail = 'Y' AND $sql_tbl[product_features].productid = '$k'")) {
                unset($productids[$k]);
            }
        }
    }

    // Comparison list is empty
    if (!is_array($productids) || @count($productids) == 0) {
        $top_message['content'] = func_get_langvar_by_name("txt_fc_empty_comparison_list");
        $top_message['type'] = 'E';
        func_header_location(func_is_internal_url($HTTP_REFERER) ? $HTTP_REFERER : 'home.php');
    }

    $store_comp_options = array();
    $store_productids = $productids;
    $is_limit = false;

    // Check 'Maximum number of products which can be compared' limit
    if (count($store_productids) > $config['Feature_Comparison']['fcomparison_comp_product_limit']) {
        $cnt = 0;
        foreach ($store_productids as $k => $v) {
            if (++$cnt > $config['Feature_Comparison']['fcomparison_comp_product_limit']) {
                unset($store_productids[$k]);
                $is_limit = true;
            }
        }
    }
    reset($store_productids);
    $store_comp_options['fclassid'] = func_query_first_cell("SELECT fclassid FROM $sql_tbl[product_features] WHERE productid = '".key($store_productids)."'");

    if ($is_limit) {
        $top_message['content'] = func_get_langvar_by_name("txt_max_number_of_products_exceeded_in_chart");
        $top_message['type'] = 'W';
    }

    func_header_location($redirect_url);

// Remove product from product list
} elseif ($mode == 'delete_products' && (isset($productid) || isset($ids))) {

    if (!empty($productid))
        $ids[] = $productid;

    if (is_array($ids)) {
        foreach ($ids as $v) {
            if (is_scalar($v)) {
                unset($store_productids[$v]);
            }
        }
    }

// Remove feature
} elseif ($mode == 'delete_feature' && (isset($foptionid) || isset($foptionids))) {

    if (!empty($foptionid))
        $foptionids[] = $foptionid;

    $tmp = func_query_hash("SELECT $sql_tbl[feature_options].foptionid, IFNULL($sql_tbl[feature_options_lng].option_name, $sql_tbl[feature_options].option_name) FROM $sql_tbl[feature_options] LEFT JOIN $sql_tbl[feature_options_lng] ON $sql_tbl[feature_options].foptionid = $sql_tbl[feature_options_lng].foptionid AND $sql_tbl[feature_options_lng].code = '$shop_language' WHERE $sql_tbl[feature_options].foptionid IN ('".implode("','", $foptionids)."') AND $sql_tbl[feature_options].avail = 'Y'", "foptionid", false, true);

    foreach ($foptionids as $v) {
        if (isset($tmp[$v]))
            $store_comp_options['disabled_options'][$v] = $tmp[$v];
    }

    unset($tmp);

// Add deleted feature(s)
} elseif ($mode == 'add_feature' && ($foptionid || $foptionids)) {

    if (!empty($foptionid))
        $foptionids[] = $foptionid;

    foreach ($foptionids as $v) {
        unset($store_comp_options['disabled_options'][$v]);
    }

    if (empty($store_comp_options['disabled_options'])) {
        unset($store_comp_options['disabled_options']);
    }

// Add new product in product list
} elseif ($mode == 'add_product' && $productid && @count($store_productids) < $config['Feature_Comparison']['fcomparison_comp_product_limit']) {
    $store_productids[$productid] = true;
}

if (!empty($mode)) {
    func_header_location($redirect_url);
}

$productids = $store_productids;

// Comaprison list/request is empty
if (empty($productids) && empty($store_comp_options['fclassid'])) {
    $top_message['content'] = func_get_langvar_by_name("txt_fc_empty_comparison_list");
    $top_message['type'] = 'E';
    func_header_location(($xcart_catalogs['customer']."/home.php"));
}

// Check classes count
$classes_count = func_query_first_cell("SELECT COUNT(DISTINCT(fclassid)) FROM $sql_tbl[product_features] WHERE productid IN ('".@implode("','", @array_keys((array)$productids))."')");

// If classes more that 1
if ($classes_count > 1) {
    $products = func_query("SELECT $sql_tbl[feature_classes].fclassid, IF($sql_tbl[feature_classes_lng].class IS NOT NULL, $sql_tbl[feature_classes_lng].class, $sql_tbl[feature_classes].class) as class, $sql_tbl[products].* FROM $sql_tbl[product_features], $sql_tbl[products], $sql_tbl[feature_classes] LEFT JOIN $sql_tbl[feature_classes_lng] ON $sql_tbl[feature_classes_lng].fclassid = $sql_tbl[feature_classes].fclassid AND $sql_tbl[feature_classes_lng].code = '$shop_language' WHERE $sql_tbl[feature_classes].avail = 'Y' AND $sql_tbl[feature_classes].fclassid = $sql_tbl[product_features].fclassid AND $sql_tbl[product_features].productid = $sql_tbl[products].productid AND $sql_tbl[products].productid IN ('".implode("','", array_keys($productids))."') ORDER BY $sql_tbl[feature_classes].fclassid, product");
    if (!empty($products)) {
        $classes = array();
        foreach ($products as $k => $v) {
            $p = func_query_first_cell("SELECT product FROM $sql_tbl[products_lng] WHERE $sql_tbl[products_lng].productid = '$v[productid]' AND $sql_tbl[products_lng].code = '$shop_language'");
            if (!empty($p))
                $v['product'] = $p;
            if (!isset($classes[$v['fclassid']])) {
                $classes[$v['fclassid']] = array("class" => $v['class']);
            }
            $classes[$v['fclassid']]['products'][] = $v;
            $products[$k] = $v;
        }
        unset($products);

        $smarty->assign('rate', 3);
        $smarty->assign('percent', floor(100 / 3 - 1));

        $smarty->assign('classes', $classes);
    }
    $mode = 'product_list';

} elseif ($comp_options['fclassid'] > 0) {
    // Compare selected products

    // Get products
    if (!empty($productids)) {
        $old_search_data = $search_data['products'];
        $old_mode = $mode;

        $search_data['products'] = array();
        $search_data['products']['_']['where'][] = "$sql_tbl[products].forsale <> 'N'";
        $search_data['products']['productid'] = array_keys($productids);
        $search_data['products']['_']['left_joins']['product_foptions'] = array(
            'on' => "$sql_tbl[products].productid = $sql_tbl[product_foptions].productid"
        );
        $search_data['products']['_']['fields'] = array(
            "IF($sql_tbl[product_foptions].productid IS NULL,'Y','') as is_empty"
        );

        // Filter products to include only slot-compatible
        if (
            !empty($active_modules['Product_Configurator'])
            && isset($pconf_productid)
            && isset($pconf_slot)
        ) {

            list($rules_by_or, $rules_prepared, $ptypes_and, $ptype_condition) = func_pconf_get_slot_rules($pconf_slot);

            if (!empty($ptype_condition)) {

                $search_data['products']['_']['left_joins']['pconf_products_classes'] = array(
                    'on' => "$sql_tbl[products].productid = $sql_tbl[pconf_products_classes].productid",
                );

                $search_data['products']['_']['where'] = array(
                    "$sql_tbl[pconf_products_classes].productid IS NOT NULL",
                    $ptype_condition,
                );

            }

        }

        $mode = 'search';
        $_old_products_per_page = $config['Appearance']['products_per_page'];
        $config['Appearance']['products_per_page'] = max($config['Feature_Comparison']['fcomparison_comp_product_limit'], $config['Appearance']['products_per_page']);

        include $xcart_dir.'/include/search.php';

        $config['Appearance']['products_per_page'] = $_old_products_per_page;

        if (!empty($active_modules['Subscriptions'])) {
            include $xcart_dir.'/modules/Subscriptions/subscription.php';
        }

        if (isset($sort_fields['orderby'])) {
            unset($sort_fields['orderby']);
            $smarty->assign('sort_fields', $sort_fields);
        }

        $search_data['products'] = $old_search_data;
        $mode = $old_mode;
        $matrix = $products;
        unset($from_tbl, $search_data, $search_condition, $products);
    }
    else {
        // Get temporary data for empty product list
        $matrix = func_query("SELECT $sql_tbl[product_features].productid FROM $sql_tbl[product_features] WHERE $sql_tbl[product_features].fclassid = '$comp_options[fclassid]' LIMIT 1");
    }

    // Get products features
    if (!empty($matrix)) {
        foreach ($matrix as $k => $v) {
            $disabled_options = '';
            if (!empty($comp_options['disabled_options'])) {
                $disabled_options = " AND $sql_tbl[feature_options].foptionid NOT IN ('".implode("','",array_keys($comp_options['disabled_options']))."')";
            }

            // Get features list
            $v['features'] = func_query("SELECT $sql_tbl[feature_options].*, $sql_tbl[product_foptions].value, IF($sql_tbl[product_foptions].productid IS NULL,'Y','') as is_empty, IF($sql_tbl[feature_options_lng].option_name IS NULL, $sql_tbl[feature_options].option_name, $sql_tbl[feature_options_lng].option_name) as option_name FROM $sql_tbl[feature_options] LEFT JOIN $sql_tbl[product_foptions] ON $sql_tbl[feature_options].foptionid = $sql_tbl[product_foptions].foptionid AND $sql_tbl[product_foptions].productid = '$v[productid]' LEFT JOIN $sql_tbl[feature_options_lng] ON $sql_tbl[feature_options_lng].foptionid = $sql_tbl[feature_options].foptionid AND $sql_tbl[feature_options_lng].code = '$shop_language' WHERE $sql_tbl[feature_options].fclassid = '$comp_options[fclassid]' AND $sql_tbl[feature_options].avail = 'Y' $disabled_options ORDER BY $sql_tbl[feature_options].orderby");
            if (empty($v['features'])) {
                $v['features'] = func_query("SELECT $sql_tbl[feature_options].*, '' as value, 'Y' as is_empty, IF($sql_tbl[feature_options_lng].option_name IS NOT NULL, $sql_tbl[feature_options_lng].option_name, $sql_tbl[feature_options].option_name) as option_name FROM $sql_tbl[feature_options] LEFT JOIN $sql_tbl[feature_options_lng] ON $sql_tbl[feature_options_lng].foptionid = $sql_tbl[feature_options].foptionid AND $sql_tbl[feature_options_lng].code = '$shop_language' WHERE $sql_tbl[feature_options].fclassid = '$comp_options[fclassid]' AND $sql_tbl[feature_options].avail = 'Y' $disabled_options ORDER BY $sql_tbl[feature_options].orderby");
            }

            if (empty($v['features']))
                continue;

            // Parse feature list
            foreach ($v['features'] as $kf => $vf) {
                if (($vf['option_type'] == 'S' || $vf['option_type'] == 'M') && !empty($vf['value'])) {
                    if ($vf['option_type'] == 'M') {
                        $_product_value = implode("','", func_sql_unserialize($vf['value']));
                    } else {
                        $_product_value = $vf['value'];
                    }

                    $_def_names = array(
                        'field' => 'lng_shop.variant_name',
                        'table' => ''
                    );
                    if ($shop_language != $config['default_customer_language']) {
                        $_def_names['field'] = 'IFNULL(lng_shop.variant_name, lng_def.variant_name)';
                        $_def_names['table'] = " LEFT JOIN $sql_tbl[feature_variants_lng] as lng_def ON $sql_tbl[feature_variants].fvariantid=lng_def.fvariantid AND lng_def.code='$config[default_customer_language]' ";
                    }

                    $v['features'][$kf]['variants'] = func_query("SELECT $sql_tbl[feature_variants].fvariantid, $_def_names[field] as name FROM $sql_tbl[feature_variants] LEFT JOIN $sql_tbl[feature_variants_lng] as lng_shop ON $sql_tbl[feature_variants].fvariantid=lng_shop.fvariantid AND lng_shop.code='$shop_language' $_def_names[table] WHERE foptionid='$vf[foptionid]' AND $sql_tbl[feature_variants].fvariantid IN ('" . $_product_value . "') ORDER BY orderby");
                }

                if ($vf['option_type'] == 'D') {
                    $v['features'][$kf]['formated_value'] = strftime($vf['format'], $vf['value']);
                }
                elseif ($vf['option_type'] == 'N') {
                    if (empty($vf['format'])) {
                        $v['features'][$kf]['formated_value'] = $vf['value'];
                    }
                    else {
                        $tmp = explode(";", $vf['format']);
                        $v['features'][$kf]['formated_value'] = number_format(doubleval($vf['value']), $tmp[0], $tmp[1], $tmp[2]);
                    }
                }
            }

            $v['rating'] = 0;
            $matrix[$k] = $v;
        }
    }

    // Redirect if matrix is empty
    if (!is_array($matrix) || @count($matrix) < 1) {
        $top_message['content'] = func_get_langvar_by_name("txt_fc_empty_comparison_list");
        $top_message['type'] = 'E';
        func_header_location('home.php');
    }

    $matrix = array_values($matrix);

    // Get product with equal Feature class
    $equal_fclassid = func_query_first_cell("SELECT fclassid FROM $sql_tbl[product_features] WHERE productid = '".$matrix[0]['productid']."'");
    if (!empty($equal_fclassid)) {

        $tmp = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products], $sql_tbl[product_features] WHERE $sql_tbl[products].forsale IN ('Y','B') AND $sql_tbl[products].productid NOT IN ('".@implode("','", @array_keys($productids))."') AND $sql_tbl[product_features].productid = $sql_tbl[products].productid AND $sql_tbl[product_features].fclassid = '$equal_fclassid'");

        if ($tmp > $config['Feature_Comparison']['fcomparison_disp_product_limit']) {

            $smarty->assign('is_product_popup','Y');
            $smarty->assign('no_ids',@implode(",", @array_keys($productids)));

        } else {

            if (
                !empty($active_modules['Product_Configurator'])
                && isset($pconf_productid)
                && isset($pconf_slot)
                && !empty($ptype_condition)
            ) {
                $equal_products = func_query("SELECT $sql_tbl[products].*, IF($sql_tbl[products_lng].product IS NOT NULL, $sql_tbl[products_lng].product, $sql_tbl[products].product) FROM $sql_tbl[product_features], $sql_tbl[products] LEFT JOIN $sql_tbl[products_lng] ON $sql_tbl[products].productid = $sql_tbl[products_lng].productid AND $sql_tbl[products_lng].code = '$shop_language' LEFT JOIN $sql_tbl[pconf_products_classes] ON $sql_tbl[products].productid = $sql_tbl[pconf_products_classes].productid WHERE $sql_tbl[products].forsale IN ('Y','B') AND $sql_tbl[products].productid NOT IN ('".@implode("','", @array_keys($productids))."') AND $sql_tbl[product_features].productid = $sql_tbl[products].productid AND $sql_tbl[product_features].fclassid = '$equal_fclassid' AND $sql_tbl[pconf_products_classes].productid IS NOT NULL AND $ptype_condition");
            } else {
                $equal_products = func_query("SELECT $sql_tbl[products].*, IF($sql_tbl[products_lng].product IS NOT NULL, $sql_tbl[products_lng].product, $sql_tbl[products].product) FROM $sql_tbl[product_features], $sql_tbl[products] LEFT JOIN $sql_tbl[products_lng] ON $sql_tbl[products].productid = $sql_tbl[products_lng].productid AND $sql_tbl[products_lng].code = '$shop_language' WHERE $sql_tbl[products].forsale IN ('Y','B') AND $sql_tbl[products].productid NOT IN ('".@implode("','", @array_keys($productids))."') AND $sql_tbl[product_features].productid = $sql_tbl[products].productid AND $sql_tbl[product_features].fclassid = '$equal_fclassid'");
            }

            if (!empty($equal_products)) {
                $smarty->assign('equal_products',$equal_products);
            }

        }

    }

    // Comparison with original product
    $not_equal_hash = array();

    if (count($matrix) > 1) {

        // Get array of first keys (key is index of compare product)
        $first_key_arr = array();

        foreach ($matrix as $k => $v) {

            if ($v['is_empty'] != 'Y' && !empty($v['features'])) {

                foreach ($v['features'] as $fk => $fv) {

                    if ($fv['is_empty'] != 'Y' && !isset($first_key_arr[$fk])) {
                        $first_key_arr[$fk] = $k;
                    }
                }
            }
        }

        // Compare process
        foreach ($matrix as $k => $v) {
            if (empty($v['features']) || $v['is_empty'] == 'Y')
                continue;

            foreach ($v['features'] as $kf => $vf) {
                if ($not_equal_hash[$vf['foptionid']] || $vf['is_empty'] == 'Y' || (!isset($first_key_arr[$kf]) || $first_key_arr[$kf] == $k))
                    continue;

                $first_key = $first_key_arr[$kf];

                if ($vf['option_type'] == 'M' && is_array($matrix[$first_key]['features'][$kf]['value']) && is_array($vf['value'])) {
                    $int = array_intersect($vf['value'], $matrix[$first_key]['features'][$kf]['value']);
                    if (!$int || @count($int) != @count($vf['value']) || @count($int) != @count($matrix[$first_key]['features'][$kf]['value'])) {
                        $not_equal_hash[$vf['foptionid']] = true;
                    }
                } elseif ($vf['value'] != $matrix[$first_key]['features'][$kf]['value']) {
                    $not_equal_hash[$vf['foptionid']] = true;
                }
            }
        }
    }

    unset($first_key_arr);

    // Remove disabled and/or equal features
    if ((!empty($comp_options['disabled_options']) || $comp_options['show_not_equal'] == 'Y')) {

        foreach ($matrix as $k => $v) {

            if (empty($v['features']) || !is_array($v['features']))
                continue;

            foreach ($v['features'] as $kf => $vf) {
                if ((!@$not_equal_hash[$vf['foptionid']] && $comp_options['show_not_equal'] == 'Y') || $comp_options['disabled_options'][$vf['foptionid']]) {
                    unset($matrix[$k]['features'][$kf]);
                }
            }
        }
    }

    // Remove temporary data for empty product list
    if (empty($productids)) {
        $matrix[0]['productid'] = 0;
    }

    $smarty->assign('matrix',              $matrix);
    $smarty->assign('matrix_cnt',          intval(@count($matrix)));
    $smarty->assign('matrix_features_cnt', intval(@count($matrix[0]['features'])));
    $smarty->assign('not_equal_hash',      $not_equal_hash);

    $mode = 'compare_table';

    $smarty->assign('sort',           $sort);
    $smarty->assign('sort_direction', $sort_direction);
}

if (@count($store_productids) >= $config['Feature_Comparison']['fcomparison_comp_product_limit']) {
    $smarty->assign('no_add', 'Y');
}

$smarty->assign('show',         $show);
$smarty->assign('mode',         $mode);
$smarty->assign('comp_options', $comp_options);

$smarty->assign(
    'toolbar_url',
    array(
        'show_not_equal' => "comparison.php?comp_options[show_not_equal]=" . ($comp_options['show_not_equal'] == 'Y' ? "N" : "Y") . "&show=" . $show,
        'axis'           => "comparison.php?comp_options[axis]=" . ($comp_options['axis'] == 'N' ? "R" : "N") . "&show=" . $show
    )
);

?>
