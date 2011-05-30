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
 * Functions for Product options module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.php,v 1.128.2.3 2011/01/10 13:12:00 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

/**
 * Get product classes array
 */
function func_get_product_classes($productid, $is_tax = NULL, $area = false)
{
    global $sql_tbl, $current_area, $shop_language, $logged_userid;

    x_load('taxes');

    if ($area === false)
        $area = $current_area;

    if (is_null($is_tax))
        $is_tax = (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[product_taxes] WHERE productid = '$productid'") > 0);

    // Get classes
    $where = '';

    if ($area == 'C') {

        $where = "AND $sql_tbl[classes].avail = 'Y'";

    }

    $classes = func_query("SELECT $sql_tbl[classes].*, $sql_tbl[classes].class as class_orig, IF($sql_tbl[class_lng].class != '', $sql_tbl[class_lng].class, $sql_tbl[classes].class) as class, IF($sql_tbl[class_lng].classtext != '', $sql_tbl[class_lng].classtext, $sql_tbl[classes].classtext) as classtext FROM $sql_tbl[classes] LEFT JOIN $sql_tbl[class_lng] ON $sql_tbl[classes].classid = $sql_tbl[class_lng].classid AND $sql_tbl[class_lng].code = '$shop_language' WHERE $sql_tbl[classes].productid = '$productid' $where ORDER BY $sql_tbl[classes].orderby");

    if (empty($classes))
        return false;

    if ($area == 'C') {

        $product = func_query_first("SELECT productid, provider, free_shipping, shipping_freight, distribution, free_tax FROM $sql_tbl[products] WHERE productid='$productid'");

        $taxes = func_get_product_tax_rates($product, $logged_userid);

    }

    $where = '';

    if ($area == 'C') {

        $where = "AND $sql_tbl[class_options].avail = 'Y'";

    }

    // Get options
    foreach ($classes as $kc => $class) {

        if (
            $class['is_modifier'] == 'T'
            || $class['is_modifier'] == 'A'
        ) {
            continue;
        }

        $classes[$kc]['options'] = func_query_hash("SELECT $sql_tbl[class_options].*, $sql_tbl[class_options].option_name as option_name_orig, IF($sql_tbl[product_options_lng].option_name != '', $sql_tbl[product_options_lng].option_name, $sql_tbl[class_options].option_name) as option_name FROM $sql_tbl[class_options] LEFT JOIN $sql_tbl[product_options_lng] ON $sql_tbl[class_options].optionid = $sql_tbl[product_options_lng].optionid AND $sql_tbl[product_options_lng].code = '$shop_language' WHERE $sql_tbl[class_options].classid = '$class[classid]' $where ORDER BY $sql_tbl[class_options].orderby", "optionid", false);

        if (@count($classes[$kc]['options']) == 0) {

            if ($area == 'C')
                unset($classes[$kc]);

            continue;

        }

        // Calculate taxes for price modificators
        foreach ($classes[$kc]['options'] as $ko => $option) {

            $classes[$kc]['options'][$ko]['optionid'] = $ko;

            if (
                $class['is_modifier'] == 'Y'
                && $area == 'C'
                && $option['price_modifier'] != 0
                && $is_tax
            ) {

                $_taxes = func_tax_price($option['price_modifier'], 0, true, NULL, '', $taxes);

                if ($option['modifier_type'] != '%') {

                    $classes[$kc]['options'][$ko]["price_modifier"] = $_taxes["taxed_price"];

                }

                $classes[$kc]['options'][$ko]["taxes"] = $_taxes["taxes"];

            }

        }

    }

    return $classes;
}

/**
 * Get product variants
 */
function func_get_product_variants($productid, $membershipid = 0, $area = false)
{
    global $sql_tbl, $current_area, $shop_language, $keys, $cart, $user_account, $active_modules, $config;

    x_load('files','taxes');

    $membershipid = intval($membershipid);

    $keys = func_get_hash_options($productid);

    if ($area === false)
        $area = $current_area;

    if (
        empty($membershipid)
        || $area != 'C'
        || empty($active_modules['Wholesale_Trading'])
    ) {

        $pricing_membership = "= '0'";

    } else {

        $pricing_membership = "IN ('$membershipid', '0')";

    }

    // Get variants' common data
    $variants = func_query_hash("SELECT $sql_tbl[variants].*, MIN($sql_tbl[pricing].price) as price, $sql_tbl[images_W].image_path as image_path_W, $sql_tbl[images_W].image_x as image_W_x, $sql_tbl[images_W].image_y as image_W_y, $sql_tbl[images_W].image_type as image_type_W FROM $sql_tbl[variants] USE INDEX (productid) LEFT JOIN $sql_tbl[pricing] ON $sql_tbl[variants].productid = $sql_tbl[pricing].productid AND $sql_tbl[pricing].variantid = $sql_tbl[variants].variantid AND $sql_tbl[pricing].membershipid $pricing_membership AND $sql_tbl[pricing].quantity = '1' LEFT JOIN $sql_tbl[images_W] ON $sql_tbl[images_W].id = $sql_tbl[variants].variantid WHERE $sql_tbl[variants].productid = '$productid' GROUP BY $sql_tbl[variants].variantid", "variantid", false);

    if (empty($variants))
        return false;

    // Get variants' items
    if ($area == 'C') {

        // Check variants' items
        $counts = func_query_column("SELECT COUNT($sql_tbl[variant_items].optionid) FROM $sql_tbl[variant_items], $sql_tbl[variants], $sql_tbl[class_options], $sql_tbl[classes] WHERE $sql_tbl[variant_items].variantid = $sql_tbl[variants].variantid AND $sql_tbl[variants].productid = '$productid' AND $sql_tbl[variant_items].optionid = $sql_tbl[class_options].optionid AND $sql_tbl[classes].classid = $sql_tbl[class_options].classid AND $sql_tbl[class_options].avail = 'Y' AND $sql_tbl[classes].avail = 'Y' GROUP BY $sql_tbl[variant_items].variantid");

        if (
            empty($counts)
            || count($counts) < count($variants)
        ) {

            return false;

        } else {

            $counts = array_unique($counts);

            if (count($counts) != 1)
                return false;

        }

        $chains = func_query_hash("SELECT $sql_tbl[variant_items].* FROM $sql_tbl[variant_items], $sql_tbl[variants], $sql_tbl[class_options], $sql_tbl[classes] WHERE $sql_tbl[variant_items].variantid = $sql_tbl[variants].variantid AND $sql_tbl[variants].productid = '$productid' AND $sql_tbl[variant_items].optionid = $sql_tbl[class_options].optionid AND $sql_tbl[classes].classid = $sql_tbl[class_options].classid AND $sql_tbl[class_options].avail = 'Y' AND $sql_tbl[classes].avail = 'Y'", "variantid", true, true);

    } else {

        $chains = func_query_hash("SELECT $sql_tbl[variant_items].* FROM $sql_tbl[variant_items], $sql_tbl[variants], $sql_tbl[class_options] WHERE $sql_tbl[variant_items].variantid = $sql_tbl[variants].variantid AND $sql_tbl[variants].productid = '$productid' AND $sql_tbl[variant_items].optionid = $sql_tbl[class_options].optionid", "variantid", true, true);
    }

    if (empty($chains))
        return false;

    // Get variants' wholesale prices
    $prices = array();

    if (!empty($active_modules['Wholesale_Trading'])) {

        $pricing_membership = '';
        $min_amount = 1;
        $groupby = "GROUP BY variantid, quantity, membershipid";

        if ($area == 'C') {

            $min_amount = intval(func_query_first_cell("SELECT min_amount FROM $sql_tbl[products] WHERE productid = '$productid'"));

            if (empty($membershipid)) {

                $pricing_membership = "AND membershipid = '0'";

            } else {

                $pricing_membership = "AND membershipid IN ('$membershipid', '0')";

            }

            $groupby = "GROUP BY variantid, quantity";

        }

        $prices = func_query_hash("SELECT *, MIN(price) as price FROM $sql_tbl[pricing] WHERE productid = '$productid' AND variantid > '0' AND (quantity != '1' OR membershipid != '0') $pricing_membership $groupby ORDER BY quantity", "variantid");

        if (!empty($prices)) {

            foreach ($prices as $vid => $ps) {

                $last_key = false;

                foreach ($ps as $pid => $p) {

                    if (
                        !empty($membershipid)
                        && $p['membershipid'] == $membershipid
                        && $p['quantity'] == 1
                    ) {

                        unset($ps[$pid]);

                        continue;

                    }

                    func_unset($ps[$pid], 'productid');

                    if ($last_key !== false) {

                        $ps[$last_key]['next_quantity'] = $p['quantity'];

                        if ($area == 'C') {

                            if ($min_amount > $ps[$last_key]['next_quantity']) {

                                unset($ps[$last_key]);

                            } elseif ($min_amount > $ps[$last_key]['quantity']) {

                                $ps[$last_key]['quantity'] = $min_amount;

                            }

                        }

                    }

                    $last_key = $pid;

                }

                if (empty($ps)) {

                    unset($prices[$vid]);

                    continue;

                }

                $ps[$pid]['next_quantity'] = 0;

                array_unshift(
                    $ps,
                    array(
                        'quantity'         => 0,
                        'next_quantity' => $ps[key($ps)]['quantity'],
                        'membershipid'     => 0,
                    )
                );

                $prices[$vid] = $ps;

            }

        }

    }

    $product = func_query_first("SELECT productid, provider, free_shipping, shipping_freight, distribution, free_tax FROM $sql_tbl[products] WHERE productid='$productid'");

    $taxes = func_get_product_tax_rates($product, $user_account['id']);

    foreach ($variants as $kv => $variant) {
        // Get references to option array

        if (empty($chains[$kv])) {

            if ($area == 'C')
                unset($variants[$kv]);

            continue;

        }

        $variants[$kv]['W_is_png'] = $variant['image_type_W'] == 'image/png' ? 1 : 0;

        // Get wholesale prices
        if (isset($prices[$kv])) {

            $variants[$kv]['wholesale'] = $prices[$kv];

            $variants[$kv]['wholesale'][0]['price'] = $variant['price'];

            unset($prices[$kv]);

            if ($area == 'C') {

                $last_price = $variant['price'];

                foreach($variants[$kv]['wholesale'] as $wpk => $wpv) {

                    if ($wpv['price'] >= $last_price) {

                        unset($variants[$kv]['wholesale'][$wpk]);

                        continue;
                    }

                    $last_price = $wpv['price'];

                }

                if (empty($variants[$kv]['wholesale'])) {

                    unset($variants[$kv]['wholesale']);

                } else {

                    $variants[$kv]['wholesale'] = array_values($variants[$kv]['wholesale']);

                }

            }

        }

        if ($area == 'C') {

            $variants[$kv]['is_image'] = !is_null($variant['image_path_W']);

            $variants[$kv]['image_url'] = func_get_image_url($kv, "W", $variant['image_path_W']);

            if ($variants[$kv]['is_image']) {

                list(
                    $variants[$kv]['variant_image_x'],
                    $variants[$kv]['variant_image_y']
                ) = func_crop_dimensions(
                    $variant['image_W_x'],
                    $variant['image_W_y'],
                    $config['Appearance']['image_width'],
                    $config['Appearance']['image_height']
                );

            }

            // Get variant's tax rates
            $_taxes = func_tax_price($variant['price'], 0, false, NULL, '', $taxes);

            $variants[$kv]['taxed_price'] = $_taxes['taxed_price'];

            if (!empty($_taxes['taxes']))
                $variants[$kv]['taxes'] = $_taxes['taxes'];

            if (!empty($variants[$kv]['wholesale'])) {

                // Get variant's wholesale prices' tax rates
                foreach ($variants[$kv]['wholesale'] as $k => $v) {

                    $_taxes = func_tax_price($v['price'], 0, false, NULL, '', $taxes);

                    $variants[$kv]['wholesale'][$k]["taxed_price"] = $_taxes["taxed_price"];

                    if (!empty($_taxes['taxes']))
                        $variants[$kv]['wholesale'][$k]["taxes"] = $_taxes["taxes"];

                }

            }

            if (
                !empty($cart['products'])
                && is_array($cart['products'])
            ) {

                foreach ($cart['products'] as $v) {

                    if ($v['productid'] != $productid)
                        continue;

                    if ($kv == func_get_variantid($v['options'], $productid))
                        $variants[$kv]['avail'] -= $v['amount'];

                }

            }

        }

        $variants[$kv]['options'] = array();

        foreach ($chains[$kv] as $oid) {

            $variants[$kv]['options'][$oid] = $keys[$oid];

        }

        if (empty($variants[$kv]['options']) && $area == "C")
            unset($variants[$kv]);

    }

    return $variants;
}

/**
 * Get product exceptions
 */
function func_get_product_exceptions($productid, $area = false)
{
    global $sql_tbl, $current_area, $shop_language;

    $keys = func_get_hash_options($productid);

    if ($area === false)
        $area = $current_area;

    $avail_condition = '';

    if ($area == 'C')
        $avail_condition = " AND $sql_tbl[classes].avail = 'Y' AND $sql_tbl[class_options].avail = 'Y'";

    $exceptions = func_query("SELECT $sql_tbl[product_options_ex].* FROM $sql_tbl[product_options_ex], $sql_tbl[classes], $sql_tbl[class_options] WHERE $sql_tbl[classes].classid = $sql_tbl[class_options].classid AND $sql_tbl[class_options].optionid = $sql_tbl[product_options_ex].optionid AND $sql_tbl[classes].productid = '$productid'".$avail_condition." GROUP BY $sql_tbl[product_options_ex].exceptionid, $sql_tbl[product_options_ex].optionid ORDER BY $sql_tbl[classes].orderby");

    if (empty($exceptions))
        return false;

    $return = array();

    foreach ($exceptions as $exception) {

        if (!isset($return[$exception['exceptionid']]))
            $return[$exception['exceptionid']] = array();

        $return[$exception['exceptionid']][$exception['optionid']] = $keys[$exception['optionid']];

    }

    return $return;
}

/**
 * Get product JS code
 */
function func_get_product_js_code($productid)
{
    global $sql_tbl;

    return func_query_first_cell("SELECT javascript_code FROM $sql_tbl[product_options_js] WHERE productid = '$productid'");
}

/**
 * Get product options hash array
 */
function func_get_hash_options($productid, $area = false, $language = false)
{
    global $sql_tbl, $current_area, $shop_language;

    if ($area === false)
        $area = $current_area;

    if ($language === false)
        $language = $shop_language;

    if ($area == 'C') {

        $keys = func_query_hash("SELECT $sql_tbl[classes].*, $sql_tbl[class_options].*, IF($sql_tbl[class_lng].class IS NULL OR $sql_tbl[class_lng].class = '', $sql_tbl[classes].class, $sql_tbl[class_lng].class) as class, IF($sql_tbl[class_lng].classtext IS NULL OR $sql_tbl[class_lng].classtext = '', $sql_tbl[classes].classtext, $sql_tbl[class_lng].classtext) as classtext FROM $sql_tbl[class_options], $sql_tbl[classes] LEFT JOIN $sql_tbl[class_lng] ON $sql_tbl[classes].classid = $sql_tbl[class_lng].classid AND $sql_tbl[class_lng].code = '$language' WHERE $sql_tbl[classes].productid = '$productid' AND $sql_tbl[classes].classid = $sql_tbl[class_options].classid AND $sql_tbl[class_options].avail = 'Y' AND $sql_tbl[classes].avail = 'Y'", "optionid", false);

        if (empty($keys))
            return array();

        $option_names_lng = func_query_hash("SELECT optionid, option_name FROM $sql_tbl[product_options_lng] WHERE optionid IN ('".implode("','", array_keys($keys))."') AND code = '$language'", "optionid", false, true);

        foreach ($keys as $kc => $class) {

            $keys[$kc]['optionid'] = $kc;

            if (!empty($option_names_lng[$kc]))
                $keys[$kc]['option_name'] = $option_names_lng[$kc];
        }

        unset($option_names_lng);

    } else {

        $keys = func_query_hash("SELECT * FROM $sql_tbl[classes], $sql_tbl[class_options] WHERE $sql_tbl[classes].productid = '$productid' AND $sql_tbl[classes].classid = $sql_tbl[class_options].classid", "optionid", false);

        if (empty($keys))
            return array();

        foreach ($keys as $kc => $class) {

            $keys[$kc]['optionid'] = $kc;

        }

    }

    return $keys;
}

/**
 * Rebuild product variants
 */
function func_rebuild_variants($productid, $force_rebuild = false, $tick = 1, $save_variants_data = true)
{
    global $sql_tbl;

    x_load('backoffice', 'image');

    if (!$force_rebuild) {
        // Check variant's matrix

        $options = func_query_column("SELECT $sql_tbl[class_options].optionid FROM $sql_tbl[classes], $sql_tbl[class_options] WHERE $sql_tbl[classes].classid = $sql_tbl[class_options].classid AND $sql_tbl[classes].productid = '$productid' AND $sql_tbl[classes].is_modifier = '' AND $sql_tbl[classes].avail = 'Y' AND $sql_tbl[class_options].avail = 'Y'");

        $variants = func_query_column("SELECT $sql_tbl[variant_items].optionid FROM $sql_tbl[variant_items], $sql_tbl[variants] WHERE $sql_tbl[variants].productid = '$productid' AND $sql_tbl[variants].variantid = $sql_tbl[variant_items].variantid GROUP BY $sql_tbl[variant_items].optionid");

        if (
            count($options) == count($variants)
            && count($options) > 0
        ) {

            $diff = array_diff($options, $variants);

            if (empty($diff))
                return true;

            unset($options, $variants, $diff);

        }

    }

    if ($tick > 0)
        func_display_service_header('lbl_rebuild_variants');

    $ids = func_query_column("SELECT variantid FROM $sql_tbl[variants] WHERE productid = '$productid'");

    if (!empty($ids)) {
        // Save old data

        $vars = func_query_hash("SELECT * FROM $sql_tbl[variants] WHERE productid = '$productid'", "variantid", false);

        if ($save_variants_data) {

            foreach ($vars as $k => $v) {

                $vars[$k]['optionids'] = func_query_column("SELECT optionid FROM $sql_tbl[variant_items] WHERE variantid = '$k'");

            }

        }

        $prices = db_query("SELECT * FROM $sql_tbl[pricing] WHERE productid = '$productid' AND variantid != '0'");

        if ($prices) {

            while ($v = db_fetch_array($prices)) {

                if (!isset($vars[$v['variantid']]))
                    continue;

                $key = $v['quantity'] . "|" . $v['membershipid'];

                if (!isset($vars[$v['variantid']]['prices']))
                    $vars[$v['variantid']]['prices'] = array();

                if (
                    !isset($vars[$v['variantid']]['prices'][$key])
                    || $vars[$v['variantid']]['prices'][$key]['price'] > $v['price']
                ) {
                    $vars[$v['variantid']]['prices'][$key] = $v;
                }

            }

            db_free_result($prices);

        }

        unset($prices);

        $items = func_query_hash("SELECT $sql_tbl[variant_items].*, $sql_tbl[class_options].classid FROM $sql_tbl[variant_items], $sql_tbl[class_options], $sql_tbl[variants] WHERE $sql_tbl[variant_items].optionid = $sql_tbl[class_options].optionid AND $sql_tbl[variant_items].variantid = $sql_tbl[variants].variantid AND $sql_tbl[variants].productid = '$productid'", array("classid", "optionid"), true, true);

        // Delete old variants
        $tmp = func_query_first("SELECT MIN(avail) as avail, MIN(weight) as weight FROM $sql_tbl[variants] WHERE productid = '$productid'");

        db_query("UPDATE $sql_tbl[products] SET avail = '$tmp[avail]', weight = '$tmp[weight]' WHERE productid = '$productid'");

        unset($tmp);

        db_query("DELETE FROM $sql_tbl[pricing] WHERE productid = '$productid' AND variantid != '0'");

        db_query("DELETE FROM $sql_tbl[variant_items] WHERE variantid IN ('" . implode("','", $ids) . "')");

    }

    // Save/restore info related to disabled variants bt#77379

    if ($save_variants_data) {

        $_vars = func_restore_variants_data($vars, $items, $productid);

        func_save_variants_data($vars, $productid);

        $vars = $_vars;

    }

    unset($ids);

    db_query("DELETE FROM $sql_tbl[variants] WHERE productid = '$productid'");

    // Get modifier-classes
    $classes = func_query("SELECT classid FROM $sql_tbl[classes] WHERE productid = '$productid' AND is_modifier = '' AND avail = 'Y' ORDER BY orderby");

    if (empty($classes))
        return false;

    $variant_counts = 1;

    foreach ($classes as $k => $v) {

        $classes[$k]['cnt'] = 0;

        $classes[$k]['options'] = func_query_column("SELECT optionid FROM $sql_tbl[class_options] WHERE classid = '$v[classid]' AND avail = 'Y' ORDER BY orderby");
        if (
            !@count($classes[$k]['options'])
            || !is_array($classes[$k]['options'])
        ) {

            unset($classes[$k]);

        } else {

            $variant_counts *= count($classes[$k]['options']);
        }
    }

    // bt:78994 Use limit for the "Price modifiers"->"Product variants" conversation
    // Create reserve for the sku auto-generation
    $productcode_limit = max(32 - strlen($variant_counts), 26);

    if (empty($classes))
        return false;

    $classes = array_values($classes);

    $classes[0]['cnt'] = -1;

    // Build variant's matrix
    $variants = array();

    // Write variants to DB
    $product = func_query_first("SELECT $sql_tbl[products].productcode, $sql_tbl[products].avail, $sql_tbl[products].weight, $sql_tbl[products].provider, $sql_tbl[pricing].price as price FROM $sql_tbl[products], $sql_tbl[pricing] WHERE $sql_tbl[products].productid = $sql_tbl[pricing].productid AND $sql_tbl[pricing].variantid = '0' AND $sql_tbl[pricing].quantity = '1' AND $sql_tbl[pricing].membershipid = '0' AND $sql_tbl[products].productid = '$productid' GROUP BY $sql_tbl[products].productid");

    if (
        !empty($product)
        && !empty($product['provider'])
    ) {
        $product['provider'] = addslashes($product['provider']);
    }

    $cnt_row = $cnt = 0;

    do {
        $is_end = false;
        $options = array();
        $old_variants = array();

        foreach ($classes as $k => $c) {

            $optionid = 0;

            if (!$is_end) {

                if ($c['cnt'] >= count($c['options'])-1) {

                    $c['cnt'] = 0;

                } else {

                    $c['cnt']++;

                    $is_end = true;

                }

                $classes[$k] = $c;

            }

            $optionid = $c['options'][$c['cnt']];

            if (empty($optionid))
                continue;

            $options[] = $optionid;

            if (isset($items[$c['classid']][$optionid])) {

                if (empty($old_variants)) {

                    $old_variants = $items[$c['classid']][$optionid];

                } else {

                    $old_variants = array_intersect($old_variants, $items[$c['classid']][$optionid]);

                }

            }

        } // foreach ($classes as $k => $c)

        if (!$is_end || empty($options))
            break;

        $_product = $product;

        func_unset($_product, 'provider');

        // Restore old data
        $old_variantid = false;

        if (
            is_array($old_variants)
            && !empty($old_variants)
        ) {
            $old_variantid = array_shift($old_variants);

            if (isset($vars[$old_variantid])) {

                $_product = func_array_merge($_product, $vars[$old_variantid]);

            }

        }

        unset($old_variants);

        // Get unique SKU
        if (strlen($_product['productcode']) > $productcode_limit) {

            $_product['productcode'] = "SKU" . $productid . "v";

        }

        $sku = addslashes($_product['productcode']);

        while (
            func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[variants] WHERE productcode = '$sku'") > 0
            || func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products] WHERE productcode = '$sku' AND provider = '".addslashes($product['provider'])."'") > 0
        ) {
            $sku = $sku . ++$cnt;
        }

        $data = array(
            'productid'        => $productid,
            'avail'            => $_product['avail'],
            'weight'        => $_product['weight'],
            'productcode'    => $sku,
        );

        // Check variantid
        if (
            !empty($old_variantid)
            && func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[variants] WHERE variantid = '$old_variantid'") == 0
        ) {

            $data['variantid'] = $old_variantid;

            $data['def'] = $_product['def'];

        }

        // Insert variant info
        $variantid = func_array2insert('variants', $data);

        if (empty($variantid))
            continue;

        // Write pricing
        if (empty($_product['prices'])) {

            // Write default price (basaed on the product price)
            $data = array (
                'productid'        => $productid,
                'quantity'        => 1,
                'membershipid'    => 0,
                'variantid'        => $variantid,
                'price'            => $_product['price'],
            );

            func_array2insert('pricing', $data);

        } else {

            // Write saved prices
            foreach ($_product['prices'] as $p) {

                $data = array(
                    'productid'        => $productid,
                    'quantity'        => $p['quantity'],
                    'membershipid'    => $p['membershipid'],
                    'variantid'        => $variantid,
                    'price'            => $p['price']
                );

                if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[pricing] WHERE priceid = '$p[priceid]'") == 0) {
                    $data['priceid'] = $p['priceid'];
                }

                func_array2insert('pricing', $data);

            }

        }

        // Restore image
        if (
            !empty($old_variantid)
            && $variantid != $old_variantid
        ) {

            if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[variants] WHERE variantid='$old_variantid' AND productid='$productid'") == 0) {

                func_delete_image($variantid, 'W');

                db_query("UPDATE $sql_tbl[images_W] SET id = '$variantid' WHERE id = '$old_variantid'");

            }

        }

        // Write matrix
        foreach ($options as $i) {

            func_array2insert(
                'variant_items',
                array(
                    'variantid' => $variantid,
                    'optionid'    => $i,
                )
            );

        }

        if (
            $tick > 0
            && $cnt_row++ % $tick == 0
        ) {
            func_flush(". ");
        }

    } while($is_end);

    // Clean old variants images
    $images = func_query_column("SELECT $sql_tbl[images_W].id FROM $sql_tbl[variants] LEFT JOIN $sql_tbl[images_W] ON $sql_tbl[variants].variantid = $sql_tbl[images_W].id WHERE $sql_tbl[images_W].id IS NULL");

    if (!empty($images)) {

        func_delete_image($images, 'W');

    }

    // Clear unassigned images
    if ($save_variants_data) {

        $images = func_query_column("SELECT $sql_tbl[images_W].id FROM $sql_tbl[images_W] LEFT JOIN $sql_tbl[variants] ON $sql_tbl[variants].variantid = $sql_tbl[images_W].id LEFT JOIN $sql_tbl[variant_backups] ON $sql_tbl[variant_backups].variantid = $sql_tbl[images_W].id WHERE $sql_tbl[variants].variantid IS NULL AND $sql_tbl[variant_backups].variantid IS NULL GROUP BY $sql_tbl[images_W].id");

    } else {

        $images = func_query_column("SELECT $sql_tbl[images_W].id FROM $sql_tbl[images_W] LEFT JOIN $sql_tbl[variants] ON $sql_tbl[variants].variantid = $sql_tbl[images_W].id WHERE $sql_tbl[variants].variantid IS NULL");

    }

    if (!empty($images)) {

        func_delete_image($images, 'W');

    }

    return true;
}

/**
 * This function checks for exception of product options for product
 */
function func_check_product_options ($productid, $options, $trusted_options = false)
{
    global $sql_tbl;

    if (empty($options) || !is_array($options))
        return false;

    $textids = func_query_column("SELECT classid FROM $sql_tbl[classes] WHERE classid IN ('" . implode("','", func_array_map("intval", array_keys($options))) . "') AND is_modifier IN ('T', 'A')");

    $where = array();
    $oids = array();

    foreach ($options as $_cid => $oid) {

        $cid = intval($_cid);

        if (empty($cid)) {
            return false;
        }

        if (
            !is_numeric($oid)
            || empty($oid)
        ) {

            $where[] = "$sql_tbl[classes].classid = '$cid' AND $sql_tbl[class_options].optionid IS NULL AND $sql_tbl[classes].is_modifier IN ('T', 'A')";

        } else {

            $where[] = "$sql_tbl[classes].classid = '$cid' AND ($sql_tbl[class_options].optionid = '$oid' OR ($sql_tbl[class_options].optionid IS NULL AND $sql_tbl[classes].is_modifier IN ('T', 'A')))";

            if (
                empty($textids)
                || !in_array($cid, $textids)
            ) {
                $oids[] = $oid;
            }

        }

    }

    if (!$trusted_options) {
        // Get classes data

        $classes = func_query_hash("SELECT $sql_tbl[classes].classid, $sql_tbl[classes].is_modifier FROM $sql_tbl[classes] LEFT JOIN $sql_tbl[class_options] ON $sql_tbl[classes].classid = $sql_tbl[class_options].classid AND $sql_tbl[class_options].avail = 'Y' WHERE $sql_tbl[classes].avail = 'Y' AND $sql_tbl[classes].productid = '".intval($productid)."' AND (" . implode(") OR (", $where) . ") GROUP BY $sql_tbl[classes].classid", "classid", false, true);

        if (count($classes) != count($options)) {
            return false;
        }

    }

    unset($where);

    // Allow admin to choose options within the exceptions list
    if (defined('XAOM'))
        return true;

    // Get number of all product classes
    $counter = @count(func_query_column("SELECT $sql_tbl[classes].classid FROM $sql_tbl[classes], $sql_tbl[class_options] WHERE $sql_tbl[classes].productid = '$productid' AND $sql_tbl[classes].avail = 'Y' AND $sql_tbl[classes].classid = $sql_tbl[class_options].classid AND $sql_tbl[class_options].avail = 'Y' GROUP BY $sql_tbl[classes].classid"));

    $oids_counter = count($oids);

    $oids = implode("','", $oids);

    if ($counter == $oids_counter) {

        // Check full options data
        return !func_query_first_cell("SELECT COUNT(*) as cnt_orig, SUM(IF(e2.optionid IS NULL, 0, 1)) as cnt_ex FROM $sql_tbl[product_options_ex] as e1 LEFT JOIN $sql_tbl[product_options_ex] as e2 ON e1.optionid = e2.optionid AND e2.optionid IN ('".$oids."') GROUP BY e1.exceptionid HAVING cnt_orig = cnt_ex");

    } else {

        $exceptions = func_query_hash("SELECT exceptionid, COUNT(optionid) FROM $sql_tbl[product_options_ex] WHERE optionid IN ('".$oids."') GROUP BY exceptionid", "exceptionid", false, true);

        if (empty($exceptions))
            return true;

        $exception_counters = func_query_hash("SELECT exceptionid, COUNT(optionid) FROM $sql_tbl[product_options_ex] WHERE exceptionid IN ('".implode("','", array_keys($exceptions))."') GROUP BY exceptionid", "exceptionid", false, true);

        foreach ($exceptions as $eid => $cnt) {

            if ($exception_counters[$eid] == $cnt)
                return false;

        }

        // Check partly options data
        $exceptions = func_query_hash("SELECT $sql_tbl[class_options].classid, COUNT($sql_tbl[product_options_ex].exceptionid) FROM $sql_tbl[product_options_ex], $sql_tbl[class_options], $sql_tbl[classes] WHERE $sql_tbl[product_options_ex].optionid = $sql_tbl[class_options].optionid AND $sql_tbl[product_options_ex].exceptionid IN ('".implode("','", array_keys($exceptions))."') AND $sql_tbl[product_options_ex].optionid NOT IN ('".$oids."') AND $sql_tbl[class_options].avail = 'Y' AND $sql_tbl[classes].avail = 'Y' AND $sql_tbl[class_options].classid = $sql_tbl[classes].classid GROUP BY $sql_tbl[class_options].classid", "classid", false, true);

        if (empty($exceptions))
            return true;

        $class_counters = func_query_hash("SELECT classid, COUNT(*) FROM $sql_tbl[class_options] WHERE classid IN ('".implode("','", array_keys($exceptions))."') AND avail = 'Y' GROUP BY classid", "classid", false, true);

        foreach ($exceptions as $cid => $cnt) {

            if (
                isset($class_counters[$cid])
                && $class_counters[$cid] == $cnt
            ) {
                return false;
            }
        }

        return true;
    }
}

/**
 * Get options modifications
 */
function func_get_product_options_data($productid, $options, $membershipid = 0, $language = false)
{
    global $sql_tbl, $shop_language, $active_modules;

    if (empty($options) || !is_array($options))
        return array(false, false);

    $membershipid = intval($membershipid);

    if ($language === false)
        $language = $shop_language;

    $ids = func_array_map('intval', array_keys($options));

    $classes = func_query_hash("SELECT classid, is_modifier FROM $sql_tbl[classes] WHERE productid = '".intval($productid)."' AND classid IN ('".implode("','", $ids)."')", "classid", false, true);

    $ret = array();

    foreach ($options as $k => $v) {

        if (!isset($classes[$k]))
            continue;

        if (
            $classes[$k] != 'T'
            && $classes[$k] != 'A'
        ) {

            $v = intval($v);

            $option = func_query_first("SELECT $sql_tbl[classes].*, $sql_tbl[class_options].*, IF($sql_tbl[class_lng].class IS NULL OR $sql_tbl[class_lng].class = '', $sql_tbl[classes].class, $sql_tbl[class_lng].class) as class, IF($sql_tbl[class_lng].classtext IS NULL OR $sql_tbl[class_lng].classtext = '', $sql_tbl[classes].classtext, $sql_tbl[class_lng].classtext) as classtext FROM $sql_tbl[class_options], $sql_tbl[classes] LEFT JOIN $sql_tbl[class_lng] ON $sql_tbl[classes].classid = $sql_tbl[class_lng].classid AND $sql_tbl[class_lng].code = '$language' WHERE $sql_tbl[class_options].optionid = '$v' AND $sql_tbl[classes].classid = $sql_tbl[class_options].classid AND $sql_tbl[classes].classid = '$k' AND $sql_tbl[classes].avail = 'Y' AND $sql_tbl[class_options].avail = 'Y'");

            $option_name_lng = func_query_first_cell("SELECT option_name FROM $sql_tbl[product_options_lng] WHERE $sql_tbl[product_options_lng].optionid = '$option[optionid]' AND code = '$language'");

            if (!empty($option_name_lng))
                $option['option_name'] = $option_name_lng;

        } else {

            $option = func_query_first("SELECT $sql_tbl[classes].*, IF($sql_tbl[class_lng].class IS NULL OR $sql_tbl[class_lng].class = '', $sql_tbl[classes].class, $sql_tbl[class_lng].class) as class, IF($sql_tbl[class_lng].classtext IS NULL OR $sql_tbl[class_lng].classtext = '', $sql_tbl[classes].classtext, $sql_tbl[class_lng].classtext) as classtext FROM $sql_tbl[classes] LEFT JOIN $sql_tbl[class_lng] ON $sql_tbl[classes].classid = $sql_tbl[class_lng].classid AND $sql_tbl[class_lng].code = '$language' WHERE $sql_tbl[classes].classid = '$k' AND $sql_tbl[classes].avail = 'Y'");

        }

        if (empty($option))
            continue;

        if (
            $classes[$k] == 'T'
            || $classes[$k] == 'A'
        ) {

            $option['option_name'] = stripslashes($v);

        } elseif (empty($classes[$k])) {

            $variants[$k] = $v;

        }

        $ret[$k] = $option;

    }

    $membershipid_string = (
        $membershipid == 0
        || (
            empty($active_modules['Wholesale_Trading'])
            && !defined('XAOM')
        )
    )
    ? "= '0'"
    : "IN ('$membershipid', '0')";

    $variant = false;

    if (!empty($variants)) {

        $variant = func_query_first("SELECT variantid, COUNT(optionid) as count FROM $sql_tbl[variant_items] WHERE optionid IN ('".implode("','", $variants)."') GROUP BY variantid ORDER BY count desc");

        if ($variant['count'] == count($variants)) {

            $variant = func_query_first("SELECT $sql_tbl[variants].*, MIN($sql_tbl[pricing].price) as price, $sql_tbl[images_W].image_path as pimage_path, $sql_tbl[images_W].image_x as pimage_x, $sql_tbl[images_W].image_y as pimage_y, $sql_tbl[images_W].imageid AS pimageid FROM $sql_tbl[pricing], $sql_tbl[variants] LEFT JOIN $sql_tbl[images_W] ON $sql_tbl[variants].variantid = $sql_tbl[images_W].id WHERE $sql_tbl[variants].variantid = '$variant[variantid]' AND $sql_tbl[variants].variantid = $sql_tbl[pricing].variantid AND $sql_tbl[pricing].productid = '$productid' AND $sql_tbl[pricing].quantity = '1' AND $sql_tbl[pricing].membershipid $membershipid_string GROUP BY $sql_tbl[variants].variantid");

        }

    }

    if (empty($ret))
        $ret = false;

    return array($variant, $ret);
}

/**
 * Serialize product options
 */
function func_serialize_options($options, $ex = false, $language = false)
{
    global $sql_tbl, $config;

    if (!is_array($options) || empty($options))
        return false;

    if ($language === false)
        $language = $config['default_admin_language'];

    $return = array();

    $ids = func_array_map('intval', array_keys($options));

    $classes = func_query_hash("SELECT $sql_tbl[classes].classid, IFNULL($sql_tbl[class_lng].class, $sql_tbl[classes].class) as class, $sql_tbl[classes].is_modifier FROM $sql_tbl[classes] LEFT JOIN $sql_tbl[class_lng] ON $sql_tbl[classes].classid = $sql_tbl[class_lng].classid AND $sql_tbl[class_lng].code = '$language' WHERE $sql_tbl[classes].classid IN ('".implode("','", $ids)."')", "classid", false);

    foreach ($options as $c => $o) {

        if (!isset($classes[$c]))
            continue;

        $optionid = (is_array($o) ? $o['optionid'] : $o);

        if (
            $classes[$c]['is_modifier'] != 'T'
            && $classes[$c]['is_modifier'] != 'A'
        ) {

            $optionid = intval($optionid);

            $option = func_query_first_cell("SELECT IFNULL($sql_tbl[product_options_lng].option_name, $sql_tbl[class_options].option_name) as option_name FROM $sql_tbl[class_options] LEFT JOIN $sql_tbl[product_options_lng] ON $sql_tbl[class_options].optionid = $sql_tbl[product_options_lng].optionid AND $sql_tbl[product_options_lng].code = '$language' WHERE $sql_tbl[class_options].optionid = '$optionid' AND $sql_tbl[class_options].classid = '$c'");

            if (strlen($option) == 0)
                continue;

        } else {

            $option = stripslashes($optionid);

        }

        if ($ex) {

            $return[] = trim($classes[$c]['class'])." ($c): ".trim($option);

            if (
                !empty($optionid)
                && $option != $optionid
            ) {
                $return[count($return) - 1] .= " ($optionid)";
            }

        } else {

            $return[] = trim($classes[$c]['class']).": ".trim($option);

        }

    }

    return @implode("\n", $return);
}

/**
 * Unserialize product options
 */
function func_unserialize_options($data)
{
    if (empty($data))
        return array(array(), array());

    $options = array();

    $options_hash = array();

    if (preg_match_all("/^(.+) \((\d+)\): (.+)$/Sm", $data, $preg)) {

        foreach ($preg[1] as $k => $c) {

            if (preg_match("/^(.+) \((\d+)\)$/S", $preg[3][$k], $preg2)) {

                $options[$c] = $preg2[1];
                $options_hash[$preg[2][$k]] = $preg2[2];

            } else {

                $options[$c] = $preg[3][$k];

            }

        }

    } elseif (preg_match_all("/^(.+): (.+)$/Sm", $data, $preg)) {

        foreach ($preg[1] as $k => $c) {

            $options[$c] = $preg[2][$k];

        }

    }

    return array($options, $options_hash);
}

/**
 * Convert product options array to variantid
 */
function func_get_variantid($options, $productid = false)
{
    global $sql_tbl;

    if (empty($options) || !is_array($options))
        return false;

    $ids = func_array_map('intval', array_keys($options));

    $vids = func_query_column("SELECT classid FROM $sql_tbl[classes] WHERE is_modifier != '' AND classid IN ('" . implode("','", $ids) . "')");

    if (!empty($vids)) {

        foreach ($vids as $v) {

            unset($options[$v]);

        }

    }

    if (empty($options))
        return false;

    if ($productid === false) {

        $ids = func_array_map('intval', array_keys($options));

        $productid = func_query_first_cell("SELECT productid FROM $sql_tbl[classes] WHERE classid IN ('" . implode("','", $ids) . "') LIMIT 1");

    }

    $cnt = 0;

    $res = db_query("SELECT $sql_tbl[classes].classid FROM $sql_tbl[classes], $sql_tbl[class_options] WHERE $sql_tbl[classes].is_modifier = '' AND $sql_tbl[classes].avail = 'Y' AND $sql_tbl[classes].productid = '".intval($productid)."' AND $sql_tbl[classes].classid = $sql_tbl[class_options].classid AND $sql_tbl[class_options].avail = 'Y' GROUP BY $sql_tbl[classes].classid");

    if ($res) {

        $cnt = db_num_rows($res);

        db_free_result($res);

    }

    if ($cnt != count($options))
        return false;

    $options = func_array_map('intval', $options);

    return func_query_first_cell("SELECT variantid, COUNT(variantid) as cnt FROM $sql_tbl[variant_items] WHERE $sql_tbl[variant_items].optionid IN ('".implode("','", $options)."') GROUP BY variantid HAVING cnt = ".$cnt." LIMIT 1");
}

/**
 * Get default product options
 */
function func_get_default_options($productid, $amount, $membershipid = 0)
{
    global $sql_tbl, $config, $_orderby;

    $productid = intval($productid);

    $amount = intval($amount);

    $membershipid = intval($membershipid);

    // Get product options
    $classes = func_query_hash("SELECT $sql_tbl[classes].classid, $sql_tbl[classes].is_modifier FROM $sql_tbl[classes] LEFT JOIN $sql_tbl[class_options] ON $sql_tbl[classes].classid = $sql_tbl[class_options].classid AND $sql_tbl[class_options].avail = 'Y' WHERE $sql_tbl[classes].avail = 'Y' AND $sql_tbl[classes].productid = '$productid' AND ($sql_tbl[class_options].classid IS NOT NULL OR $sql_tbl[classes].is_modifier IN ('T', 'A')) GROUP BY $sql_tbl[classes].classid ORDER BY $sql_tbl[classes].orderby", "classid", false);

    if (empty($classes))
        return true;

    $_product_options = array();

    $_orderby = array_keys($classes);

    $_orderby = array_flip($_orderby);

    // Get default variant
    $variant_counter = @count(func_query_column("SELECT $sql_tbl[classes].classid FROM $sql_tbl[classes], $sql_tbl[class_options], $sql_tbl[variant_items] WHERE $sql_tbl[classes].classid = $sql_tbl[class_options].classid AND $sql_tbl[class_options].avail = 'Y' AND $sql_tbl[classes].avail = 'Y' AND $sql_tbl[classes].productid = '$productid' AND $sql_tbl[classes].is_modifier = '' AND $sql_tbl[variant_items].optionid = $sql_tbl[class_options].optionid GROUP BY $sql_tbl[classes].classid"));

    if ($variant_counter > 0) {

        $avail_where = '';

        if ($config['General']['unlimited_products'] == 'N') {

            $avail_where = "AND $sql_tbl[variants].avail >= ".$amount;

        } elseif ($config['General']['enable_outofstock_products'] != 'Y') {

            $avail_where = "AND $sql_tbl[variants].avail > '0'";

        }

        // Detect default variant
        $def_variantid = func_query_first_cell("SELECT variantid FROM $sql_tbl[variants] WHERE productid = '$productid' AND def = 'Y' ".$avail_where);

        if (empty($def_variantid))
            $def_variantid = func_get_default_variantid($productid);

        if (!empty($def_variantid)) {

            $_product_options = func_query_hash("SELECT $sql_tbl[class_options].classid, $sql_tbl[class_options].optionid FROM $sql_tbl[class_options], $sql_tbl[variant_items] WHERE $sql_tbl[variant_items].variantid = '$def_variantid' AND $sql_tbl[variant_items].optionid = $sql_tbl[class_options].optionid", "classid", false, true);

            if (count($_product_options) != $variant_counter)
                return false;

            // Check exceptions
            $exceptions = func_query_hash("SELECT exceptionid, COUNT(optionid) FROM $sql_tbl[product_options_ex] WHERE optionid IN ('".implode("','", $_product_options)."') GROUP BY exceptionid", "exceptionid", false, true);

            if (!empty($exceptions)) {

                // Get exceptions counters
                $exception_counters = func_query_hash("SELECT exceptionid, COUNT(optionid) FROM $sql_tbl[product_options_ex] WHERE exceptionid IN ('".implode("','", array_keys($exceptions))."') GROUP BY exceptionid", "exceptionid", false, true);

                foreach ($exceptions as $eid => $cnt) {

                    if ($exception_counters[$eid] == $cnt) {

                        $_product_options = array();

                        break;

                    }

                }

                if (!empty($_product_options)) {

                    // When the set of exceptions defined for a product covers not only the
                    // combination of options that make the product's default variant, but
                    // also a whole group of non-variant options which can be used in
                    // combination with them, this check-up ensures that a different
                    // (non-exceptional) combination of variant options is selected as the
                    // products's default one.
                    $exceptions = func_query_hash("SELECT $sql_tbl[class_options].classid, COUNT($sql_tbl[product_options_ex].exceptionid) FROM $sql_tbl[product_options_ex], $sql_tbl[class_options] WHERE $sql_tbl[product_options_ex].optionid = $sql_tbl[class_options].optionid AND $sql_tbl[product_options_ex].exceptionid IN ('".implode("','", array_keys($exceptions))."') AND $sql_tbl[product_options_ex].optionid NOT IN ('".implode("','", $_product_options)."') GROUP BY $sql_tbl[class_options].classid", "classid", false, true);

                    if (!empty($exceptions)) {

                        $class_counters = func_query_hash("SELECT classid, COUNT(*) FROM $sql_tbl[class_options] WHERE classid IN ('".implode("','", array_keys($exceptions))."') AND avail = 'Y' GROUP BY classid", "classid", false, true);

                        foreach ($exceptions as $cid => $cnt) {

                            if (
                                isset($classes[$cid])
                                && isset($class_counters[$cid])
                                && $class_counters[$cid] == $cnt
                            ) {

                                $_product_options = array();

                                break;

                            }

                        }

                    }

                }

                unset($exceptions, $exception_counters);

            }

            // Unset variant-type classes
            if (!empty($_product_options)) {

                foreach ($_product_options as $cid => $oid) {

                    if (isset($classes[$cid]))
                        unset($classes[$cid]);

                }

            }

        }

    }

    // Get class options
    $options = func_query_hash("SELECT classid, optionid FROM $sql_tbl[class_options] WHERE classid IN ('".implode("','", array_keys($classes))."') AND avail = 'Y' ORDER BY orderby", "classid", true, true);

    $_flag = false;

    foreach ($classes as $k => $class) {

        if (
            $class['is_modifier'] == 'T'
            || $class['is_modifier'] == 'A'
        ) {

            $_product_options[$k] = '';

            unset($classes[$k]);

            continue;

        }

        $classes[$k]['cnt'] = $_flag ? 0 : -1;

        $_flag = true;

        if (isset($options[$k])) {

            $classes[$k]['options'] = array_values($options[$k]);

        } else {

            unset($classes[$k]);

        }

    }

    unset($options);

    if (empty($classes)) {

        if (empty($_product_options))
            return false;

        uksort($_product_options, 'func_get_default_options_callback');

        return $_product_options;

    }

    // Check if at least one of product variants available

    $max_variant_avail = func_query_first_cell("SELECT MAX(avail) FROM $sql_tbl[variants] WHERE productid = '$productid' GROUP BY productid");

    // Scan & check classes options array
    do {
        $product_options = $_product_options;

        $is_add = true;

        // Build full 'classid->optionid' hash
        foreach ($classes as $k => $class) {
            if ($is_add) {

                if (count($class['options'])-1 <= $class['cnt']) {

                    $class['cnt'] = 0;

                } else {

                    $is_add = false;

                    $class['cnt']++;

                }

            }

            $product_options[$k] = $class['options'][$class['cnt']];

            $classes[$k]['cnt'] = $class['cnt'];

        }

        // Check current product options array
        if (func_check_product_options($productid, $product_options, true)) {

            $variantid = ($max_variant_avail > 0 ? func_get_variantid($product_options, $productid) : '');

            // Check variant quantity in stock
            if (
                empty($variantid)
                || (
                    $config['General']['show_outofstock_products'] == 'Y'
                    && $config['General']['unlimited_products'] == 'Y'
                )
                || func_query_first_cell("SELECT avail FROM $sql_tbl[variants] WHERE variantid = '$variantid'") > 0
            ) {
                break;
            }

        }

    } while(!$is_add);

    if (empty($product_options))
        return false;

    uksort($product_options, 'func_get_default_options_callback');

    return $product_options;
}

function func_get_default_options_callback($a, $b)
{
    global $_orderby;

    $a = $_orderby[$a];

    $b = $_orderby[$b];

    if ($a == $b)
        return 0;

    return $a > $b ? 1 : -1;
}

/**
 * Get default options markup
 */
function func_get_default_options_markup($productid, $price)
{
    global $sql_tbl;

    // Get product options
    $classes = func_query_hash("SELECT $sql_tbl[classes].classid FROM $sql_tbl[classes], $sql_tbl[class_options] WHERE $sql_tbl[classes].classid = $sql_tbl[class_options].classid AND $sql_tbl[class_options].avail = 'Y' AND $sql_tbl[classes].avail = 'Y' AND $sql_tbl[classes].productid = '$productid' AND $sql_tbl[classes].is_modifier = 'Y' GROUP BY $sql_tbl[classes].classid ORDER BY $sql_tbl[classes].orderby", "classid", false);

    if (empty($classes))
        return 0;

    // Get class options
    $options = func_query_hash("SELECT classid, optionid, modifier_type, price_modifier FROM $sql_tbl[class_options] WHERE classid IN ('".implode("','", array_keys($classes))."') AND avail = 'Y' ORDER BY orderby", "classid", true);

    $_flag = false;

    foreach ($classes as $k => $class) {

        $classes[$k]['cnt'] = $_flag ? 0 : -1;

        $_flag = true;

        if (isset($options[$k])) {

            $classes[$k]['options'] = array_values($options[$k]);

        } else {

            unset($classes[$k]);

        }

    }
    unset($options);

    if (empty($classes))
        return 0;

    // Scan & check classes options array
    $markup = 0;
    do {
        $product_options = array();

        $is_add = true;

        $counters = array();

        // Build full 'classid->optionid' hash
        foreach ($classes as $k => $class) {

            if ($is_add) {

                if (count($class['options'])-1 <= $class['cnt']) {

                    $class['cnt'] = 0;

                } else {

                    $is_add = false;

                    $class['cnt']++;

                }

            }

            $counters[$k] = $class['cnt'];

            $product_options[$k] = $class['options'][$class['cnt']]['optionid'];

            $classes[$k]['cnt'] = $class['cnt'];
        }

        // Check current product options array
        if (func_check_product_options($productid, $product_options)) {

            foreach ($counters as $cid => $idx) {

                if ($classes[$cid]['options'][$idx]['modifier_type'] == '$') {

                    $markup += $classes[$cid]['options'][$idx]['price_modifier'];

                } elseif ($price != 0) {

                    $markup += $price / 100 * $classes[$cid]['options'][$idx]['price_modifier'];

                }

            }

            break;

        }

    } while(!$is_add);

    return $markup;
}

/**
 * Get default options markup for products list
 */
function func_get_default_options_markup_list($products)
{
    global $sql_tbl;

    if (empty($products) || !is_array($products))
        return array();

    $in_products = "IN ('" . implode("','", array_keys($products)) . "')";

    // Get product options
    $tmp = func_query_hash("SELECT $sql_tbl[classes].productid, $sql_tbl[classes].classid FROM $sql_tbl[classes], $sql_tbl[class_options] WHERE $sql_tbl[classes].classid = $sql_tbl[class_options].classid AND $sql_tbl[class_options].avail = 'Y' AND $sql_tbl[classes].avail = 'Y' AND $sql_tbl[classes].productid $in_products AND $sql_tbl[classes].is_modifier = 'Y' GROUP BY $sql_tbl[classes].classid ORDER BY $sql_tbl[classes].orderby", "productid", true, true);

    if (empty($tmp))
        return array();

    $classes = array();
    $cids = array();

    foreach ($tmp as $pid => $subclasses) {

        foreach ($subclasses as $cid) {

            $classes[$pid][$cid] = array();

            $cids[] = $cid;

        }

    }

    unset($tmp);

    // Get class options
    if (defined('X_MYSQL41_COMP_MODE')) {

        $options = func_query_hash("SELECT classid, optionid, modifier_type, price_modifier FROM $sql_tbl[class_options] WHERE classid IN ('".implode("','", $cids)."') AND avail = 'Y' ORDER BY orderby, optionid", "classid", true);

    } else {

        $options = func_query_hash("SELECT classid, optionid, modifier_type, price_modifier FROM $sql_tbl[class_options] WHERE classid IN ('".implode("','", $cids)."') AND avail = 'Y' ORDER BY orderby", "classid", true);

    }

    foreach ($classes as $pid => $subclasses) {

        $_flag = false;

        foreach($subclasses as $cid => $class) {

            $classes[$pid][$cid]['cnt'] = $_flag ? 0 : -1;

            $_flag = true;

            if (isset($options[$cid])) {

                $classes[$pid][$cid]['options'] = array_values($options[$cid]);

            } else {

                unset($classes[$pid][$cid]);

            }

        }

        if (empty($classes[$pid]))
            unset($classes[$pid]);

    }

    unset($options);

    if (empty($classes))
        return array();

    // Scan & check classes options array
    $markup = array();

    foreach ($classes as $pid => $subclasses) {

        $markup[$pid] = 0;

        do {
            $product_options = array();

            $is_add = true;

            $counters = array();

            // Build full 'classid->optionid' hash
            foreach ($subclasses as $cid => $class) {

                if ($is_add) {

                    if (count($class['options']) - 1 <= $class['cnt']) {

                        $class['cnt'] = 0;

                    } else {

                        $is_add = false;

                        $class['cnt']++;

                    }

                }

                $counters[$cid] = $class['cnt'];

                $product_options[$cid] = $class['options'][$class['cnt']]['optionid'];

                $subclasses[$cid]['cnt'] = $class['cnt'];

            }

            // Check current product options array
            if (func_check_product_options($pid, $product_options, true)) {

                foreach ($counters as $cid => $idx) {

                    if ($subclasses[$cid]['options'][$idx]['modifier_type'] == '%') {

                        $markup[$pid] += $products[$pid] / 100 * $subclasses[$cid]['options'][$idx]['price_modifier'];

                    } else {

                        $markup[$pid] += $subclasses[$cid]['options'][$idx]['price_modifier'];

                    }

                }

                break;

            }

        } while(!$is_add);

    } // foreach ($classes as $pid => $subclasses)

    return $markup;
}

/**
 * Get default variant
 */
function func_get_default_variantid($productid, $get_anyway = false)
{
    global $sql_tbl, $config;

    // Get classes (variant type)
    $classes = func_query_hash("SELECT $sql_tbl[classes].classid FROM $sql_tbl[classes], $sql_tbl[class_options], $sql_tbl[variant_items] WHERE $sql_tbl[classes].classid = $sql_tbl[class_options].classid AND $sql_tbl[class_options].avail = 'Y' AND $sql_tbl[classes].avail = 'Y' AND $sql_tbl[classes].productid = '$productid' AND $sql_tbl[classes].is_modifier = '' AND $sql_tbl[variant_items].optionid = $sql_tbl[class_options].optionid GROUP BY $sql_tbl[classes].classid", "classid");

    if (empty($classes))
        return false;

    $avail_where = '';

    if (
        $config['General']['show_outofstock_products'] != 'Y'
        || $config['General']['unlimited_products'] != 'Y'
    ) {
        $avail_where = "AND $sql_tbl[variants].avail > '0'";
    }

    // Detect default variant
    $def_variantid = func_query_first_cell("SELECT variantid FROM $sql_tbl[variants] WHERE productid = '$productid' AND def = 'Y' " . $avail_where);

    if (!empty($def_variantid)) {

        $_product_options = func_query_hash("SELECT $sql_tbl[class_options].classid, $sql_tbl[class_options].optionid FROM $sql_tbl[class_options], $sql_tbl[variant_items] WHERE $sql_tbl[variant_items].variantid = '$def_variantid' AND $sql_tbl[variant_items].optionid = $sql_tbl[class_options].optionid", "classid", false, true);

        if (count($_product_options) != count($classes))
            return false;

        // Check exceptions
        $exceptions = func_query_hash("SELECT exceptionid, COUNT(optionid) FROM $sql_tbl[product_options_ex] WHERE optionid IN ('".implode("','", $_product_options)."') GROUP BY exceptionid", "exceptionid", false, true);

        if (!empty($exceptions)) {

            // Get exceptions counters
            $exception_counters = func_query_hash("SELECT exceptionid, COUNT(optionid) FROM $sql_tbl[product_options_ex] WHERE exceptionid IN ('".implode("','", array_keys($exceptions))."') GROUP BY exceptionid", "exceptionid", false, true);

            foreach ($exceptions as $eid => $cnt) {

                if ($exception_counters[$eid] == $cnt) {

                    $_product_options = array();

                    break;

                }

            }

            if (!empty($_product_options)) {

                // When the set of exceptions defined for a product covers not only the
                // combination of options that make the product's default variant, but
                // also a whole group of non-variant options which can be used in
                // combination with them, this check-up ensures that a different
                // (non-exceptional) combination of variant options is selected as the
                // products's default one.
                $exceptions = func_query_hash("SELECT $sql_tbl[class_options].classid, COUNT($sql_tbl[product_options_ex].exceptionid) FROM $sql_tbl[product_options_ex], $sql_tbl[class_options] WHERE $sql_tbl[product_options_ex].optionid = $sql_tbl[class_options].optionid AND $sql_tbl[product_options_ex].exceptionid IN ('".implode("','", array_keys($exceptions))."') AND $sql_tbl[product_options_ex].optionid NOT IN ('".implode("','", $_product_options)."') GROUP BY $sql_tbl[class_options].classid", "classid", false, true);

                if (!empty($exceptions)) {

                    $class_counters = func_query_hash("SELECT $sql_tbl[class_options].classid, COUNT($sql_tbl[class_options].optionid) FROM $sql_tbl[classes], $sql_tbl[class_options] WHERE $sql_tbl[class_options].classid IN ('".implode("','", array_keys($exceptions))."') AND $sql_tbl[class_options].avail = 'Y' AND $sql_tbl[classes].classid = $sql_tbl[class_options].classid AND $sql_tbl[classes].avail = 'Y' GROUP BY $sql_tbl[class_options].classid", "classid", false, true);
                    foreach ($exceptions as $cid => $cnt) {

                        if (
                            isset($class_counters[$cid])
                            && $class_counters[$cid] == $cnt
                        ) {

                            $_product_options = array();

                            break;

                        }

                    }

                }

            }

            unset($exceptions, $exception_counters);

        }

        if (!empty($_product_options))
            return $def_variantid;

    }

    // Get class options
    $options = func_query_hash("SELECT classid, optionid FROM $sql_tbl[class_options] WHERE classid IN ('".implode("','", array_keys($classes))."') AND avail = 'Y' ORDER BY orderby", "classid", true, true);

    $_flag = false;

    foreach ($classes as $k => $class) {

        $classes[$k]['cnt'] = $_flag ? 0 : -1;

        $_flag = true;

        if (isset($options[$k])) {

            $classes[$k]['options'] = array_values($options[$k]);

        } else {

            unset($classes[$k]);

        }

    }

    unset($options);

    if (empty($classes))
        return false;

    // Scan & check classes options array
    $variantid = false;

    $first_variantid = false;

    do {
        $product_options = array();

        $is_add = true;

        // Build full 'classid->optionid' hash
        foreach ($classes as $k => $class) {

            if ($is_add) {

                if (count($class['options']) - 1 <= $class['cnt']) {

                    $class['cnt'] = 0;

                } else {

                    $is_add = false;

                    $class['cnt']++;

                }

            }

            $product_options[$k] = $class['options'][$class['cnt']];

            $classes[$k]['cnt'] = $class['cnt'];

        }

        // Check current product options array
        if (func_check_product_options($productid, $product_options)) {

            $variantid = func_get_variantid($product_options, $productid);

            // Save first valid variant id
            if (!$first_variantid)
                $first_variantid = $variantid;

            // Check variant quantity in stock
            if (
                (
                    $config['General']['show_outofstock_products'] == 'Y'
                    && $config['General']['unlimited_products'] == 'Y'
                )
                || func_query_first_cell("SELECT avail FROM $sql_tbl[variants] WHERE variantid = '$variantid'") > 0
            ) {
                break;
            }

            $variantid = false;

        }

    } while(!$is_add);

    if (
        $variantid === false
        && !empty($first_variantid)
    ) {
        // Get first valid variant if all valid variants is out-of-stock

        $variantid = $first_variantid;

    } elseif (
        $variantid === false
        && empty($first_variantid)
        && $get_anyway
    ) {

        #bt:79832 It seems all the variants are invalid, return first available or def to work the func_build_quick_prices properly
        $first_avail_variantid = func_query_first_cell("SELECT variantid FROM $sql_tbl[variants] WHERE productid = '$productid' ORDER BY def DESC, avail DESC");

        $variantid = !empty($def_variantid)
            ? $def_variantid
            : $first_avail_variantid;

    }

    return $variantid;
}

/**
 * Get Product options amount
 */
function func_get_options_amount($product_options, $productid)
{
    global $sql_tbl, $config;

    $productid = intval($productid);

    if (empty($productid))
        return false;

    if (
        !empty($product_options)
        && is_array($product_options)
    ) {

        $classes = func_query_column("SELECT classid FROM $sql_tbl[classes] WHERE productid = '$productid' AND is_modifier = ''");

        if (count($classes) > 0) {

            $ids = array();

            foreach ($product_options as $k => $v) {

                $k = intval($k);

                if (in_array($k, $classes)) {

                    $ids[] = "$sql_tbl[classes].classid = '$k' AND $sql_tbl[class_options].optionid = '" . intval($v) . "'";

                }

            }

            if (!empty($ids)) {

                $ids = func_query_column("SELECT $sql_tbl[class_options].optionid FROM $sql_tbl[class_options], $sql_tbl[classes], $sql_tbl[variant_items] WHERE $sql_tbl[classes].classid = $sql_tbl[class_options].classid AND $sql_tbl[classes].productid = '$productid' AND $sql_tbl[classes].is_modifier = '' AND $sql_tbl[class_options].optionid = $sql_tbl[variant_items].optionid AND (".implode(") OR (", $ids).") GROUP BY $sql_tbl[class_options].optionid");

                $variant = func_query_first("SELECT variantid, COUNT(optionid) as count FROM $sql_tbl[variant_items] WHERE optionid IN ('".implode("','", $ids)."') GROUP BY variantid ORDER BY count desc");

                if (
                    count($classes) == $variant['count']
                    && func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[variants] WHERE productid = '$productid' AND variantid = '$variant[variantid]'") > 0
                ) {
                    return func_query_first_cell("SELECT avail FROM $sql_tbl[variants] WHERE variantid = '$variant[variantid]'");
                }

            }

        }

    }

    return func_query_first_cell("SELECT avail FROM $sql_tbl[products] WHERE productid = '$productid'");
}

/**
 * Delete product option class
 */
function func_delete_po_class($classid)
{
    global $sql_tbl;

    if (is_numeric($classid)) {

        $where = "= '$classid'";

    } elseif (
        is_array($classid)
        && !empty($classid)
    ) {

        $where = "IN ('" . implode("','", $classid) . "')";

    } else {

        return false;

    }

    $ids = func_query_column("SELECT optionid FROM $sql_tbl[class_options] WHERE classid $where");

    if (!empty($ids)) {

        db_query("DELETE FROM $sql_tbl[class_options] WHERE classid $where");

        db_query("DELETE FROM $sql_tbl[product_options_lng] WHERE optionid IN ('" . implode("','", $ids) . "')");

        db_query("DELETE FROM $sql_tbl[product_options_ex] WHERE optionid IN ('" . implode("','", $ids) . "')");

    }

    db_query("DELETE FROM $sql_tbl[classes] WHERE classid $where");

    db_query("DELETE FROM $sql_tbl[class_lng] WHERE classid $where");

    return true;
}

/**
 * Restore info related to early disabled variants bt#77379
 */
function func_restore_variants_data($vars, &$items, $productid)
{
    global $sql_tbl, $config;

    // Get all enabled options

    $enabled_options = func_query_column("SELECT $sql_tbl[class_options].optionid FROM $sql_tbl[classes], $sql_tbl[class_options] WHERE $sql_tbl[classes].classid = $sql_tbl[class_options].classid AND $sql_tbl[classes].productid = '$productid' AND $sql_tbl[classes].is_modifier = '' AND ($sql_tbl[classes].avail = 'Y' AND $sql_tbl[class_options].avail = 'Y') GROUP BY $sql_tbl[class_options].optionid");

    if (empty($enabled_options) || !is_array($enabled_options))
        return $vars;

    $variants_to_restore = func_query("SELECT vb.*, co.classid FROM $sql_tbl[variant_backups] as vb, $sql_tbl[class_options] as co WHERE vb.productid = '$productid' AND vb.optionid=co.optionid AND vb.optionid IN ('" . implode("','", $enabled_options) . "')");

    if (empty($variants_to_restore))
        return $vars;

    foreach ($variants_to_restore as $k => $v) {

        $items[$v['classid']][$v['optionid']][] = $v['variantid'];

        $vars[$v['variantid']] = unserialize($v['data']);

    }

    return $vars;
}

/**
 * Save info related to disabled variants bt#77379
 */
function func_save_variants_data($vars, $productid)
{
    global $sql_tbl, $config;

    if (empty($vars) || !is_array($vars))
        return false;

    // Find all disabled options
    $disabled_options = func_query_column("SELECT $sql_tbl[class_options].optionid FROM $sql_tbl[classes], $sql_tbl[class_options] WHERE $sql_tbl[classes].classid = $sql_tbl[class_options].classid AND $sql_tbl[classes].productid = '$productid' AND $sql_tbl[classes].is_modifier = '' AND NOT ($sql_tbl[classes].avail = 'Y' AND $sql_tbl[class_options].avail = 'Y') GROUP BY $sql_tbl[class_options].optionid");

    if (
        empty($disabled_options)
        || !is_array($disabled_options)
    ) {

        db_query("DELETE FROM $sql_tbl[variant_backups] WHERE productid = '$productid'");

        return false;

    }

    // Delete all enabled variants from variant_backups
    $disabled_variants = func_query_column("SELECT b.variantid FROM $sql_tbl[variant_backups] AS a,$sql_tbl[variant_backups] AS b WHERE a.variantid=b.variantid AND a.optionid in ('" . implode("','", $disabled_options) . "') GROUP BY b.variantid");

    db_query("DELETE FROM $sql_tbl[variant_backups] WHERE variantid NOT IN ('" . implode("','", $disabled_variants) . "')");

    // Save all disabled variants
    foreach($vars as $variantid => $variant) {

        if (empty($variant['optionids']))
            continue;

        $is_disabled_variant = array_intersect($variant['optionids'], $disabled_options);

        if (empty($is_disabled_variant))
            continue;

        foreach($variant['optionids'] as $optionid) {

            func_array2insert(
                'variant_backups',
                array(
                    'optionid'     => $optionid,
                    'variantid' => $variantid,
                    'productid' => $productid,
                    'data'         => serialize($variant),
                ),
                true
            );

        }

    }

    return true;
}

function func_ic_is_valid_pvarthmbn()
{
    global $active_modules;

    return $active_modules['Product_Options'] && $active_modules['Product_Configurator'];
}

function func_ic_get_size_pvarthmbn($width, $height)
{
    global $config, $pconf_slot_data_image_width, $pconf_summary_image_width;

    return array(
        'width' => min($width, max($config['Appearance']['thumbnail_width'], $pconf_slot_data_image_width, $pconf_summary_image_width)),
        'height' => min($height, max($config['Appearance']['thumbnail_height'], $pconf_slot_data_image_width, $pconf_summary_image_width))
    );
}

function func_ic_is_crop_pvarthmbn()
{
    return false;
}

?>
