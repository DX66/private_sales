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
 * Functions for shipping packages mechanism
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.pack.php,v 1.35.2.2 2011/01/10 13:11:52 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

define('MAX_ITEMS_FOR_PACKING', 2000);

/**
 * Generate packages or get them from cache
 */
function func_get_packages($items, $package_limits, $max_number_of_packs=1000000)
{
    global $sql_tbl, $XCARTSESSID;

    $packages = false;

    if (!defined('PACKING_DEBUG')) {
        $md5_args = md5(serialize(func_get_args()));

        $packages = func_query_first("SELECT packages FROM $sql_tbl[packages_cache] WHERE md5_args='$md5_args' AND session_id='$XCARTSESSID'");

        $packages = (!empty($packages))? unserialize($packages['packages']) : false;
    }

    if ($packages === false) {
        $packages = func_get_packages_internal($items, $package_limits, $max_number_of_packs);
        func_array2insert('packages_cache', array('md5_args' => $md5_args, 'session_id' => $XCARTSESSID, 'packages' => addslashes(serialize($packages))), true);
    }

    return $packages;
}

/**
 * Generates packages for items list
 */
function func_get_packages_internal($items, $package_limits, $max_number_of_packs)
{

    global $config;

    func_set_time_limit(0);

    $time = func_microtime();

    if (!is_array($items))
        return false;

    $pending_items = $items;

    // Reset dimensions for small items or if 'use_dimensions_for_packing' option disabled
    foreach($pending_items as $k=>$v) {
        if ($config['Shipping']['use_dimensions_for_packing'] != 'Y' || (isset($v['small_item']) && $v['small_item'] == 'Y')) {
            $v['length'] = $v['width'] = $v['height'] = 0;
            $pending_items[$k] = $v;
        }
    }

    usort($pending_items, 'func_sort_items');

    // For debugging purposes
    if (defined('PACKING_DEBUG')) {
        global $pack_debug_str;
        $pack_debug_str = '';

        ob_start();
        echo "Sorted Items:\n";
        print_r($pending_items);
        echo "\nPackage limits:\n";
        print_r($package_limits);
        echo "\n";
        $pack_debug_str = ob_get_contents()."\n";
        ob_end_clean();
    }

    // Generate separate boxes for specific items
    $separate_boxes = func_prepare_separate_boxes($pending_items, $package_limits, $max_number_of_packs);

    $max_number_of_packs -= count($separate_boxes);
    if ($max_number_of_packs < 0) $separate_boxes = -1;

    // Generate common packages for rest items if separated boxes did not return error code
    if ($separate_boxes != -1 && !empty($pending_items)) {

        $packages = func_pack_items($pending_items, $package_limits, $max_number_of_packs);

        // Use default package dimensions defined for packages with zero dimensions
        // (e.g. products that is marked as  small items)

        if (is_array($packages)) {
            foreach ($packages as $k=>$package) {
                if ($package['box']['length'] * $package['box']['width'] * $package['box']['height'] == 0) {
                    $packages[$k]['box']['length'] = $config['Shipping']['small_items_box_length'];
                    $packages[$k]['box']['width'] = $config['Shipping']['small_items_box_width'];
                    $packages[$k]['box']['height'] = $config['Shipping']['small_items_box_height'];
                }
            }
        }
    }

    $time = func_microtime() - $time;

    // For debugging purposes
    if (defined('PACKING_DEBUG')) {

        ob_start();

        echo "Total packages: " . sprintf("%d", (is_array($separate_boxes) ? count($separate_boxes) : 0) + (is_array($packages) ? count($packages) : 0)) . "\n";

        echo "Packages generation time: $time\n";

        if (constant('PACKING_DEBUG') == 1) {
            echo "Separate packages:\n";
            print_r($separate_boxes);
            echo "\n";

            if (isset($packages)) {
                echo "Packages:\n";
                print_r($packages);
                echo "\n";
            }
        }

        $out = ob_get_contents();
        ob_end_clean();
        $pack_debug_str .= "\n".$out;
        register_shutdown_function('func_pack_debug_output');
    }

    if ((isset($packages) && $packages == -1) || $separate_boxes == -1)
        return -1;

    // Save in the result packages array separate boxes
    $result_packages = $separate_boxes;

    // Save in the result packages array common packages boxes
    if (is_array($packages))
        foreach ($packages as $package)
            $result_packages[] = $package['box']; // Save only boxes

    // Free memory
    $packages = $separate_boxes = null;

    return $result_packages;

}

/**
 * Generate packages for items, which should be shipped in separated packages
 */
function func_prepare_separate_boxes(&$pending_items, $package_limits, $max_number_of_packs)
{

    global $config;

    $separate_boxes = array();
    $_pending_items = array();
    $boxid = 0;
    $total_items_for_packing = 0;

    $item_is_placed = true;
    $stop_packing = false;

    while ( !$stop_packing && ($item = array_pop($pending_items)) != null) {

        $item['items_per_box'] = $item['items_per_box'] != 0 ? $item['items_per_box'] : 1;

        if (!isset($item['separate_box']) || $item['separate_box'] != 'Y') {

            if ($total_items_for_packing < MAX_ITEMS_FOR_PACKING) {
                $total_items_for_packing += $item['amount'];
                $pending_item = $item;
                $item['amount'] = max(0, $total_items_for_packing - MAX_ITEMS_FOR_PACKING);
                $pending_item['amount'] -= $item['amount'];
                $_pending_items[] = $pending_item;
                if ($item['amount'] <= 0)
                    continue;
            }

            $item['items_per_box'] = 1;
        }

        // If packing routine should take in consideration product dimensions...
        if ($config['Shipping']['use_dimensions_for_packing'] == 'Y') {

            if (!func_check_box_dimensions($item, $package_limits)) {
                $_tmp_box = $item;
                $_tmp_box['length'] = $item['width'];
                $_tmp_box['width'] = $item['length'];

                if (!func_check_box_dimensions($_tmp_box, $package_limits)) {
                    $item_is_placed = false;
                    break;
                }
            }
        }

        $number_of_boxes = ceil($item['amount'] / $item['items_per_box']);

        for ($i = 0; $i < $number_of_boxes; $i++) {
            $_item = $item;
            $_item['amount'] = min($item['items_per_box'], $item['amount']);
            $_item['weight'] *= $_item['amount'];

            if (!func_check_item_weight($_item['weight'], $package_limits)) {
                $item_is_placed = false;
                break;
            }

            if (!func_check_item_price($_item['price'], $package_limits)) {
                $item_is_placed = false;
                break;
            }

            $separate_boxes[$boxid++] = $_item;

            // check if the max number of packages is not exceeded
            if (count($separate_boxes) > $max_number_of_packs) {
                func_pack_debug("Too many packages [func_prepare_separate_boxes]: (".count($separate_boxes).">".$max_number_of_packs."). Stop packing.\n");
                $stop_packing = true;
                $item_is_placed = false;
                break;
            }

            $item['amount'] -= $_item['amount'];
        }

        if (!$item_is_placed) break;
    }

    if (!$item_is_placed)
        return -1;

    // $_pending_items array is used for correction of numeric keys of $pending_items after unsetting some values
    $pending_items = array_reverse($_pending_items);

    return $separate_boxes;
}

/**
 * Generate packages depending on package limits and items weight/dimensions
 */
function func_pack_items(&$pending_items, $package_limits, $max_number_of_packs)
{

    global $config;

    $packages = array();

    $stop_packing = false;

    $current_item_number = 0;
    $package_level = 1;

    // Scan a pending items array until it's empty or $stop_packing flag occured
    while (!empty($pending_items) && !$stop_packing) {

        // Get current package
        $current_package_id = 0;
        $current_package = func_get_current_package($packages, $current_package_id);


        // Get current item from pending items list
        $current_item = $pending_items[$current_item_number];

        // Always pack one item
        $current_item['amount'] = 1;

        $item_is_placed = false;

        // Check if item box weight do not exceeds package weight limit
        if (func_check_item_weight($current_item['weight'] + $current_package['box']['weight'], $package_limits) && func_check_item_price($current_item['price'] + $current_package['box']['price'], $package_limits)) {

            // If packing routine should take in consideration product dimensions...
            if ($config['Shipping']['use_dimensions_for_packing'] == 'Y' && @$current_item['small_item'] != 'Y') {
                // Try to place item box into the package according to package dimensions limits
                $box = func_place_item_by_dimensions($current_item, $current_package, $package_limits, $package_level);
                if ($box)
                    $item_is_placed = true;
            }
            else {
                // Update only weight, dimensions are not used
                $box = array('weight' => $current_package['box']['weight'] + $current_item['weight'],
                             'price' => $current_package['box']['price'] + $current_item['price']);

                $item_is_placed = true;
            }
        }

        // If item has been placed successfully...
        if ($item_is_placed) {

            func_pack_debug("Package $current_package_id; Level $package_level; Item: $current_item[cartid] - Placed\n");

            // Add item box variant into the package
            $current_package = func_add_item_to_package($current_package, $box, $current_item, $package_level);

            // Add current item to the current package
            $packages[$current_package_id] = $current_package; // Save current package in the packages list

            // Update $pending_items
            func_update_pending_items_array($pending_items, $current_item_number);

        }
        // If item has not been placed...
        else {

            func_pack_debug("Package $current_package_id; Level $package_level; Item: $current_item[cartid] - Declined ");

            // Go to next item in pending items list
            $current_item_number++;

            // If next item is not available...
            if (!isset($pending_items[$current_item_number])) {

                $current_item_number = 0;

                // If current package level contains any items...
                if (!empty($current_package['level_'.$package_level]['items'])) {
                    $package_level++; // Go to next package level
                    func_pack_debug("(go to next level)\n");
                }
                // If entire package contains any items...
                elseif (!empty($current_package['level_1']['items'])) {
                    func_pack_debug("(go to next package)\n");
                    $duplicates = 0;

                    while(func_check_duplicate_package($current_package, $pending_items)) {
                        $packages[] = $current_package;
                        $duplicates++;
                    }

                    if($duplicates>0) {
                        func_pack_debug("Package $current_package_id - $duplicates duplicate package(s) added\n");
                        if(!empty($pending_items)) {
                            $packages[] = func_create_new_package(); // Add new package...
                            $package_level = 1; // ...and try to fill it out with first level
                        }
                    } else {
                        $packages[] = func_create_new_package(); // Add new package...
                        $package_level = 1; // ...and try to fill it out with first level
                    }

                    // check if the max number of packages is not exceeded
                    if (count($packages) > $max_number_of_packs) {
                        func_pack_debug("Too many packages [func_pack_items]: (".count($packages).">".$max_number_of_packs."). Stop packing.\n");
                        $stop_packing = true;
                        break;
                    }

                }
                // Stop packing if item could not be placed into package and package is empty
                else {
                    $stop_packing = true;
                    func_pack_debug("(error: item can not be shipped)\n");
                }
            }
            else
                func_pack_debug("(go to next item)\n");

        }

    } // while

    // Return error code if packing has been stopped
    if ($stop_packing) {
        $packages = null;
        return -1;
    }

    return $packages;

}

/**
 * Get the current package from packages array or generate a default package
 */
function func_get_current_package($packages, &$current_package_id)
{

    $current_package = array();

    // Get the current package - last package from the packages list
    $current_package_id = count($packages);
    if ($current_package_id > 0)
        $current_package_id--;

    // Initialize current package
    if (!empty($packages))
        $current_package = $packages[$current_package_id];
    else {
        $current_package = func_create_new_package();
    }

    return $current_package;

}

/**
 * Create new package with default weight/dimensions
 */
function func_create_new_package()
{

    $package = array();
    $package['box'] = array(
        'weight' => 0,
        'length' => 0,
        'width'  => 0,
        'height' => 0
    );
    $package['level_1']['box'] = $package['box'];
    $package['level_1']['items'] = array();

    return $package;
}

/**
 * Check if sum of item weight and current package weight exceeds the package weight limit
 */
function func_check_item_weight($weight, $package_limits)
{

    if (isset($package_limits['weight']) && $weight > $package_limits['weight'])
        return false;

    return true;

}

/**
 * Check if sum of item prices and current package price exceeds the package price limit
 */
function func_check_item_price($price, $package_limits)
{

    if (isset($package_limits['price']) && $price > $package_limits['price'])
        return false;

    return true;

}

/**
 * Check if current item could be placed into the current package according to the dimensions limit
 */
function func_place_item_by_dimensions($current_item, $current_package, $package_limits, $package_level)
{

    // Prepare all available configurations of the item box within package in one level
    // Note: it is supposed only length<->width replacement during packing items,
    // vertical rotation is not supposed
    $dim_keys = array('width'=>'length', 'length'=>'width');

    // Prepare current package level box for comparison
    if (isset($current_package['level_'.$package_level]['box']))
        $current_level_box = $current_package['level_'.$package_level]['box'];
    else {
        $current_level_box = array();
        $current_level_box['length'] = $current_level_box['width'] = $current_level_box['height'] = $current_level_box['weight'] = 0;
    }

    // Generate boxes list for each variant of placement item into the current package level
    $boxes = array();
    foreach ($dim_keys as $key_box) {
        foreach ($dim_keys as $key_item) {
            $_box = array();
            $_box[$key_box] = $current_level_box[$key_box] + $current_item[$key_item];
            $_box[$dim_keys[$key_box]] = max($current_level_box[$dim_keys[$key_box]], $current_item[$dim_keys[$key_item]]);
            $_box['height'] = max($current_level_box['height'], $current_item['height']);
            $_box['weight'] = $current_level_box['weight'] + $current_item['weight'];
            $_box['price'] = $current_level_box['price'] + $current_item['price'];

            $_current_package = func_add_item_to_package($current_package, $_box, $current_item, $package_level);

            // Check if package satisfies package limits after adding a current item box variant
            if (func_check_box_dimensions($_current_package['box'], $package_limits))
                $boxes[] = $_box;
        }
    }

    // If any available item box variant was found...
    if (!empty($boxes)) {

        // Select box variant with minimal square (length/width)
        $box_id = 0;
        $box_square = 0;
        foreach ($boxes as $_box_id=>$_box) {
            $_box_square = $_box['length'] * $_box['width'];
            if ($_box_square < $box_square)
                $box_id = $_box_id;
        }

        return $boxes[$box_id];

    }

    return false;

}

/**
 * Add item into the package
 */
function func_add_item_to_package($package, $box, $item, $package_level)
{

    global $config;

    // Update entire package box dimensions from selected item box variant
    if (isset($box['length']))
        $package['box']['length'] = max($package['box']['length'], $box['length']);
    if (isset($box['width']))
        $package['box']['width'] = max($package['box']['width'], $box['width']);

    // Update current level of package box weight/dimensions
    if (isset($package['level_'.$package_level]['box']))
        $package['level_'.$package_level]['box'] = array_merge($package['level_'.$package_level]['box'], $box);
    else
        $package['level_'.$package_level]['box'] = $box;

    // Add current item to the placed items list
    $package['level_'.$package_level]['items'][] = $item;

    // Update height and weight of the current package box
    func_update_package_box($package);

    return $package;

}

/**
 * Update package box weight and height
 */
function func_update_package_box(&$package)
{

    $box_height = 0;
    $box_weight = 0;
    $box_price = 0;
    $level = 1;
    $last_level = array();

    // Sum of an average heights of all box levels
    while (!empty($package['level_'.$level]['box'])) {
        $last_level = $package['level_'.$level]['box'];
        $box_height += $last_level['height'];
        $box_weight += $last_level['weight'];
        $box_price += $last_level['price'];
        $level++;
    }

    $package['box']['height'] = $box_height;
    $package['box']['weight'] = $box_weight;
    $package['box']['price'] = $box_price;

}

/**
 * Update pending items list after removing specified item (which is placed into the package box)
 */
function func_update_pending_items_array(&$pending_items, &$current_item_number)
{

    // If specified item quantity more than one...
    if ($pending_items[$current_item_number]['amount'] > 1) {
        $pending_items[$current_item_number]['amount']--; // Decrease quantity only
    }
    // If specified item quantity is one...
    else {
        func_unset($pending_items, $current_item_number); // Remove placed item from the pending items list

        // Update pending items array keys (reset integer keys value to 0,1,2,3...)
        $_pending_items = array();
        foreach ($pending_items as $_item)
            $_pending_items[] =$_item;
        $pending_items = $_pending_items;

        // Update current item number
        if ($current_item_number > 0)
            $current_item_number--;

    }

}

/**
 * Sort items by dimensional weight
 */
function func_sort_items($pack1, $pack2)
{

    $dim_weight1 = func_dim_weight($pack1);
    $dim_weight2 = func_dim_weight($pack2);

    return ($dim_weight1 > $dim_weight2 ? -1 : ($dim_weight1 < $dim_weight2 ? 1 : 0));

}

/**
 * Calculate girth of a box
 */
function func_girth($box)
{
    $girth = $box['length'] + $box['width'] * 2 + $box['height'] * 2;

    return $girth;
}

/**
 * Calculate dimensional weight of a box
 */
function func_dim_weight($box, $usa_domestic=true)
{
    $dim_weight = $box['length'] * $box['width'] * $box['height'];

    return round(($usa_domestic ? $dim_weight/194 : $dim_weight/166), 4);
}

/**
 * Check if box dimensions does not exceed package limit
 */
function func_check_box_dimensions(&$box, $package_limits)
{

    foreach ($package_limits as $key=>$value) {

        if ($key == 'weight')
            continue;

        if ($key == 'girth' && !isset($box['girth']))
            $box['girth'] = func_girth($box);

        if ($key == 'dim_weight' && !isset($box['dim_weight']))
            $box['dim_weight'] = func_dim_weight($box);

        if (!isset($box[$key]))
            return false;

        if ($box[$key] > $value)
            return false;
    }

    return true;
}

/**
 * Add message to debug log
 */
function func_pack_debug($str)
{
    global $pack_debug_str;

    if (defined('PACKING_DEBUG')) {
        $pack_debug_str .= $str;
        return true;
    }

    return false;
}

/**
 * Output of the debug log
 */
function func_pack_debug_output()
{
    global $pack_debug_str;

    if (defined('PACKING_DEBUG')) {
        echo "\n<!-- Packing debug info\n".$pack_debug_str."-->";
        return true;
    }

    return false;
}

/**
 * Prepare an items array - select only fields that is required for packing routine
 */
function func_prepare_items_list ($cart_products, $ignore_freight = false, $all_products_free_shipping = false)
{

    global $sql_tbl;

    $updated_items = array();
    $needed_fields = array('weight', 'amount', 'cartid', 'price', 'provider');
    $additional_fields = array('length', 'width', 'height', 'small_item', 'separate_box', 'items_per_box');

    if (!empty($cart_products) && is_array($cart_products)) {

        foreach ($cart_products as $product) {

            if (@$product['deleted']) continue; // for Advanced_Order_Management module

            if (!func_check_product_shipable($product, $ignore_freight, $all_products_free_shipping))
                continue;

            $_item = array();
            foreach ($needed_fields as $field) {
                $_item[$field] = $product[$field];
            }
            $_tmp = func_query_first("SELECT ".implode(", ", $additional_fields)." FROM $sql_tbl[products] WHERE productid='$product[productid]'");
            $_item = func_array_merge($_item, $_tmp);
            if (!empty($_item))
                $updated_items[] = $_item;
        }
    }

    return $updated_items;

}

/**
 * Checking if product should be ignored in shipping calculations or not
 */
function func_check_product_shipable($product, $ignore_freight=false, $all_products_free_shipping=false)
{

    global $active_modules, $config;

    // Product is not shipable if this is a downloadable product

    if (!empty($active_modules['Egoods']) && !empty($product['distribution']))
        return false;

    // Product with defined shipping freight should not to be considered as shipable
    // if option 'replace_shipping_with_freight' is enabled

    if ($product['shipping_freight'] > 0 && $config['Shipping']['replace_shipping_with_freight'] == 'Y' && !$ignore_freight)
        return false;

    $free_shipping_product = ($product['free_shipping'] == 'Y' || !empty($product['free_shipping_used']));

    // Product is not shipable if it is marked as 'free shipping' and option
    // 'free_shipping_weight_select' is disabled

    if ($free_shipping_product && $config['Shipping']['free_shipping_weight_select'] != 'Y')
        return false;

    // Product is not shipable if it is marked as 'free shipping' and option
    // 'free_shipping_weight_select' is enabled but
    // cart contains some products which did not mark as 'free shipping' ($all_products_free_shipping) and
    // shipping freight should not be ignored ($ignore_freight)

    if ($free_shipping_product && $config['Shipping']['free_shipping_weight_select'] == 'Y' && !$ignore_freight && !$all_products_free_shipping)
        return false;

    return true;

}

/**
 * Check whether $pending_items array contains a duplication of $current_package
 * If so, remove the duplication from $pending_items and return true,
 */
function func_check_duplicate_package(&$current_package, &$pending_items)
{

    $dup_item = null;
    $dup_items_amount = 0;

    // Check whether all items in the current package are equal

    foreach ($current_package as $k=>$v) {
        if (substr($k,0,6) == 'level_') {
            foreach ($v['items'] as $item) {
                $amount = $item['amount'];
                unset($item['amount']);
                if ($dup_item == null) {
                    $dup_item = $item;
                }
                elseif ($dup_item != $item) {
                    return false;
                }
                $dup_items_amount += $amount;
            }
        }
    }

    if ($dup_items_amount < 1) return false;

    // Check whether the head of $pending_items contains a duplication of $current_package

    $tmp_amount = $dup_items_amount;

    foreach ($pending_items as $item) {

        foreach ($dup_item as $k=>$v) {
            if ($v != $item[$k])
                return false;
        }

        $tmp_amount -= $item['amount'];

        if ($tmp_amount <= 0)
            break;
    }

    if ($tmp_amount > 0) return false;

    // Remove the duplicate package from the pending items

    foreach ($pending_items as $k=>$item) {

        if ($dup_items_amount >= $item['amount']) {
            $dup_items_amount -= $item['amount'];
            func_unset($pending_items, $k);
        }
        else {
            $pending_items[$k]['amount'] -= $dup_items_amount;
            $dup_items_amount = 0;
        }

        if ($dup_items_amount <= 0)
            break;
    }

    // Re-index the pending items array
    $pending_items = array_values($pending_items);

    return true;
}

?>
