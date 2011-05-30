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
 * Configuration step
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: pconf_customer_step.php,v 1.89.2.1 2011/01/10 13:12:00 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

if(empty($active_modules['Product_Configurator'])) {

    func_header_location(func_get_resource_url('product', $productid));

}

x_load(
    'product',
    'taxes'
);

x_session_register('cart');

$pconf_out_of_stock = false;

/**
 * This function gather the step information
 */
function func_get_slots_info($stepid, $step_counter)
{
    global $sql_tbl, $config, $continue_button, $user_account;
    global $configurations, $productid;
    global $language_var_names, $store_language;
    global $pconf_slot_data_image_width;

    // Get slots data

    $slots_data = func_query("SELECT * FROM $sql_tbl[pconf_slots] WHERE stepid='$stepid' AND status!='N' ORDER BY orderby, slotid");

    if (is_array($slots_data)) {

        foreach ($slots_data as $k => $v) {

            $slots_data[$k]['slot_name'] = func_get_languages_alt($language_var_names['slot_name'] . $v['slotid'], $store_language);
            $slots_data[$k]['slot_descr'] = func_get_languages_alt($language_var_names['slot_descr'] . $v['slotid'], $store_language);

        // Each slot must have the rules for products inserting

            $slots_data[$k]['have_rules'] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[pconf_slot_rules] WHERE slotid='$v[slotid]'");

            if (!empty($configurations[$productid]['steps'][$step_counter]['slots'][$v['slotid']])) {

                $product_selected = $configurations[$productid]['steps'][$step_counter]['slots'][$v['slotid']];

                $slots_data[$k]['have_rules'] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[pconf_slot_rules] WHERE slotid='$v[slotid]'");

                // Get the price modifier data

                $slots_data[$k]['price_modifier'] = func_query_first("SELECT * FROM $sql_tbl[pconf_slot_markups] WHERE slotid='$v[slotid]' AND membershipid IN ('$user_account[membershipid]', '0') ORDER BY membershipid DESC");

                $configurations[$productid]['steps'][$step_counter]['slots'][$v['slotid']]['price_modifier'] = $slots_data[$k]['price_modifier'];

                // Get the product data

                global $pconf;
                global $active_modules;

                $pconf = $productid;

                $slots_data[$k]['product'] = func_select_product($product_selected['productid'], @$user_account['membershipid'], false, true, false, 'T');

                if (is_array($slots_data[$k]['product'])) {

                // Correct the product data

                    $slots_data[$k]['product'] = func_array_merge($product_selected, $slots_data[$k]['product']);

                    if (
                        !empty($active_modules['Wholesale_Trading'])
                        && $slots_data[$k]['product']['variantid'] == 0
                        && $product_selected['amount'] > 1
                    ) {
                        $wprice = func_query_first_cell("SELECT MIN($sql_tbl[pricing].price) as price FROM $sql_tbl[pricing] WHERE $sql_tbl[pricing].productid='$product_selected[productid]' AND $sql_tbl[pricing].membershipid IN (".intval($user_account['membershipid']).", '0') AND $sql_tbl[pricing].quantity <= '".$product_selected['amount']."' AND $sql_tbl[pricing].variantid = '0'");

                        $slots_data[$k]['product']['price'] = $wprice ? min($wprice,$slots_data[$k]['product']['price']) : $slots_data[$k]['product']['price'];
                    }

                    if ($slots_data[$k]['product']['options']) {

                        list(
                            $variant,
                            $slots_data[$k]['product']['product_options']
                        ) = func_get_product_options_data($slots_data[$k]['product']['productid'], $slots_data[$k]['product']['options'], @$user_account['membershipid']);

                        if ($variant) {

                            // Get thumbnails for variant images

                            if (!is_null($variant['pimageid'])) {
                                $thumb_url = func_image_cache_get_image('W', 'pvarthmbn', $variant['pimageid']);
                                if (!empty($thumb_url)) {
                                    // Use cached W-thumbnail
                                    $variant['image_url'] = $thumb_url['url'];
                                    $variant['slot_image_x'] = $thumb_url['width'];
                                    $variant['slot_image_y'] = $thumb_url['height'];
                                }
                            }

                            $slots_data[$k]['product'] = func_array_merge($slots_data[$k]['product'], $variant);

                            if (
                                !empty($active_modules['Wholesale_Trading'])
                                && $product_selected['amount'] > 1
                            ) {
                                $wprice = func_query_first_cell("SELECT MIN($sql_tbl[pricing].price) as price FROM $sql_tbl[pricing] WHERE $sql_tbl[pricing].productid='$product_selected[productid]' AND $sql_tbl[pricing].membershipid IN (".intval($user_account['membershipid']).", '0') AND $sql_tbl[pricing].quantity <= '".$product_selected['amount']."' AND $sql_tbl[pricing].variantid = '".$slots_data[$k]["product"]["variantid"]."'");

                                $slots_data[$k]['product']['price'] = $wprice ? min($wprice,$slots_data[$k]['product']['price']) : $slots_data[$k]['product']['price'];
                            }
                        }

                        $slots_data[$k]['product']['options_surcharge'] = 0;

                        if ($slots_data[$k]['product']['product_options']) {

                            foreach($slots_data[$k]['product']['product_options'] as $o) {

                                $slots_data[$k]['product']['options_surcharge'] += ($o['modifier_type'] == '%'?($slots_data[$k]["product"]["price"]*$o['price_modifier']/100):$o['price_modifier']);

                            }

                        }

                        $slots_data[$k]['product']['price'] += $slots_data[$k]['product']['options_surcharge'];
                    }

                    // Apply price modifier

                    if ($slots_data[$k]['price_modifier']['markup_type'] == "$") {

                        $slots_data[$k]['product']['price'] = max($slots_data[$k]['product']['price'] + $slots_data[$k]['price_modifier']['markup'], 0);

                    } elseif ($slots_data[$k]['price_modifier']['markup_type'] == "%") {

                        $slots_data[$k]['product']['price'] = max($slots_data[$k]['product']['price'] + price_format($slots_data[$k]['product']['price'] * $slots_data[$k]['price_modifier']['markup'] / 100), 0);

                    }

                    // Get the taxes info and taxed price

                    $slots_data[$k]['product']['taxes'] = func_get_product_taxes($slots_data[$k]['product'], $user_account['id']);

                    // Adjust image dimensions

                    list(
                        $slots_data[$k]['product']['slot_image_x'],
                        $slots_data[$k]['product']['slot_image_y']
                    ) = func_crop_dimensions($slots_data[$k]['product']['image_x'], $slots_data[$k]['product']['image_y'], $pconf_slot_data_image_width, false);

                }

            } elseif ($slots_data[$k]['status'] == 'M') {

                $continue_button = false;

            }

        } // foreach ($slots_data as $k => $v)

    } // if (is_array($slots_data))

    return $slots_data;
}

/**
 * This function makes a default configuration
 */
function func_pconf_get_default_configuration($productid)
{
    global $sql_tbl, $user_account;
    global $pconf;

    $pconf                     = $productid;
    $configuration             = array();
    $requirement_mismatch     = false;

    $wizards = func_pconf_get_wizards($productid);

    if (!empty($wizards)) {

        $found_products_specs = array();

        $filled_slots = array();

        foreach ($wizards as $k => $v) {

            $wizards[$k]['slots'] = func_get_slots_info($v['stepid'], $v['step_counter']);

            if (!is_array($wizards[$k]['slots']))
                continue;

            foreach ($wizards[$k]['slots'] as $k1 => $v1) {

                if (!empty($v1['default_productid'])) {

                    $product_info = func_select_product($v1['default_productid'], $user_account['membershipid'], false, true, false, 'T');

                    // Check product amount and slot compatibility
                    $min_amount = ($v1['multiple'] == 'Y')
                        ? $v1['amount_min']
                        : 1;

                    if (
                        $product_info['avail'] < $min_amount
                        || !func_pconf_is_slot_compatible($v1['slotid'], $product_info, $configuration)
                    ) {
                        $requirement_mismatch = true;

                        break;
                    }

                    $product = array(
                        'productid'     => $v1['default_productid'],
                        'productcode'     => $product_info['productcode'],
                        'product'         => $product_info['product'],
                        'amount'         => ($product_info['avail'] < $v1['default_amount'])
                            ? $product_info['avail']
                            : $v1['default_amount'],
                        'options'         => $product_info['options'],
                    );

                    $configuration['steps'][$k + 1]['slots'][$v1['slotid']] = $product;

                    $configuration['prefilled_with_defaults'] = true;
                }
            }

            if ($requirement_mismatch) {
                $configuration = array();
                break;
            }
        }
    }

    return array(
        $configuration,
        $requirement_mismatch,
    );
}

/**
 * This function checks whether the product item(s) are available in stock
 * and the product is compatible with already filled slots
 */
function func_pconf_is_slot_compatible($slot, $product, $configuration)
{
    global $sql_tbl;
    global $check_bidirectional_requirements;
    global $check_product_type_requirements_by_or;

    $matched = true;

    // Get the rule array for the slot
    list(
        $rules_by_or,
        $rules_prepared,
        $ptypes_and,
        $ptype_condition
    ) = func_pconf_get_slot_rules($slot);

    if (
        !empty($rules_prepared)
        && !func_pconf_product_match_rules($product['productid'], $rules_prepared)
    ) {
        $matched = false;
    }

    if ($matched) {

        // Get the list of products that already are in the slots and the requirements (on specifications) list
        $product['classes'] = func_query("SELECT classid, ptypeid FROM $sql_tbl[pconf_products_classes] WHERE productid = '$product[productid]'");

        list(
            $filled_slots,
            $found_products_specs,
            $required_specifications
        ) = func_pconf_prepare_requirements($slot, $configuration, $ptypes_and);

        if (
            !empty($required_specifications)
            && !func_pconf_product_match_requirements($product, $required_specifications, $filled_slots, $found_products_specs)
        ) {
            $matched = false;
        }

    }

    return $matched;
}

if (!is_array($configurations))
    $configurations = array();

/**
 * Get the default configuration
 */
list(
    $def_configuration,
    $def_configuration_warn
) = func_pconf_get_default_configuration($productid);

/**
 * Pre-fill the slots with default products
 */
if (
    !isset($configurations[$productid])
    && !empty($def_configuration)
) {
    $configurations[$productid] = $def_configuration;
}

/**
 * Get the current step id
 */
if (empty($configurations[$productid]['current_stepid']))
    $configurations[$productid]['current_stepid'] = 1;

$current_stepid = $configurations[$productid]['current_stepid'];

if (!isset($step))
    $step = $current_stepid;

if ($mode == 'continue') {

    $configurations[$productid]['current_stepid']++;

    if ($configurations[$productid]['reconfigure'])
        $configurations[$productid]['current_stepid'] = 'last';

    func_header_location("pconf.php?productid=" . $productid);
}

if ($mode == 'back') {

    $steps_number = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[pconf_wizards] WHERE productid='$productid'");

    if ($current_stepid == 'last') {
        $configurations[$productid]['current_stepid'] = $steps_number;
    } else {
        $configurations[$productid]['current_stepid'] = ($current_stepid - 1 < 1)
            ? 1
            : $current_stepid - 1;
    }

    func_header_location("pconf.php?productid=" . $productid);
}

if ($mode == 'update') {

    $step = intval($step);

    $configurations[$productid]['current_stepid'] = ($step>0?$step:1);

    func_header_location("pconf.php?productid=".$productid);
}

if ($mode == 'clean') {

    if (
        isset($step)
        && !empty($step)
        && isset($configurations[$productid]['steps'][$step])
    ) {

        func_unset($configurations[$productid]['steps'], $step);
        func_unset($configurations[$productid], 'prefilled_with_defaults');

    } else {

        $configurations[$productid] = array();

    }

    func_header_location("pconf.php?productid=" . $productid . ((!empty($step)) ? "&step=$step&mode=update" : ''));
}

if ($mode == 'reset') {

    $configurations[$productid] = $def_configuration;

    if ($def_configuration_warn) {

        $top_message = array(
            'type'         => 'W',
            'content'     => func_get_langvar_by_name('msg_pconf_default_configuration_warn')
        );

    }

    func_header_location("pconf.php?productid=" . $productid);
}

if ($mode == 'reconfigure') {

    if (!empty($wlitem)) {

        $wldata = func_query_first("SELECT * FROM $sql_tbl[wishlist] WHERE wishlistid='$wlitem'");

        if (!empty($wldata)) {
            $configurations[$productid]                 = unserialize($wldata['object']);
            $configurations[$productid]['reconfigure']     = true;
            $configurations[$productid]['wishlistid']     = $wlitem;
            $configurations[$productid]['cartid']         = '';
            $configurations[$productid]['amount']         = $wldata['amount'];
            $configurations[$productid]['options']         = unserialize($wldata['options']);
        }

    } elseif (
        !empty($itemid)
        && is_array($cart['products'])
    ) {
        foreach ($cart['products'] as $k => $v) {

            if ($v['cartid'] == $itemid) {
                $configurations[$v['productid']]                     = $v['pconf_data'];
                $configurations[$v['productid']]['reconfigure']     = true;
                $configurations[$v['productid']]['cartid']             = $itemid;
                $configurations[$v['productid']]['wishlistid']         = '';
                $configurations[$v['productid']]['amount']             = $v['amount'];
                $configurations[$v['productid']]['options']         = $v['options'];

                break;
            }

        }

    }

    func_header_location("pconf.php?productid=" . $productid);
}

if (
    $REQUEST_METHOD == 'POST'
    || (
        $REQUEST_METHOD == 'GET'
        && $mode == 'add'
    )
) {

    if ($mode == 'pconf_update') {

    // Update the configurable product data...

        if (intval($amount) <= 0)
            func_header_location("pconf.php?productid=$productid");

        $max_quantity = func_pconf_get_max_quantity($configurations[$productid]);

        if (intval($amount) > $max_quantity)
            func_header_location("pconf.php?productid=$productid&error=amount");

        // Get the wizards info for this product

        $wizards = func_pconf_get_wizards($productid);

        $product_price = 0;

        if (is_array($wizards)) {

            $counter = 1;

            foreach ($wizards as $k => $v) {

                $v['step_counter']         = $counter++;
                $wizards[$k]['slots']     = func_get_slots_info($v['stepid'], $v['step_counter']);

                if (is_array($wizards[$k]['slots'])) {

                    foreach ($wizards[$k]['slots'] as $k1 => $v1) {

                        $product_price += ($v1['product']['price'] * $v1['product']['amount']);

                    }

                }

            }

        }

        if (!empty($configurations[$productid]['cartid'])) {

        // in the cart

            if (is_array($cart['products'])) {

                $cartid = $configurations[$productid]['cartid'];

                foreach ($cart['products'] as $k => $v) {

                    if ($v['cartid'] == $cartid) {

                    // Configurable product in the cart is found

                        if (!empty($active_modules['Product_Options'])) {

                        // Do addition to cart with options

                            if(
                                !empty($product_options)
                                && func_check_product_options ($productid, $product_options)
                            ) {
                                $cart['products'][$k]['options'] = $product_options;
                            }

                        }

                        $cart['products'][$k]['amount']         = $amount;
                        $cart['products'][$k]['pcitem_amount']     = $amount;
                        $cart['products'][$k]['price']             = $product_price;
                        $cart['products'][$k]['free_price']     = $product_price;
                        $cart['products'][$k]['pconf_data']     = $configurations[$productid];

                        // Delete all subproducts

                        foreach ($cart['products'] as $k1 => $v1) {

                            if ($v1['hidden'] != $cartid)
                                $products[] = $v1;

                        }

                        $cart['products'] = $products;

                        $mode             = 'add';
                        $productindex     = $k;
                        $added_product     = func_select_product($productid, @$user_account['membershipid']);

                        include $xcart_dir . '/modules/Product_Configurator/pconf_customer_cart.php';

                        break;

                    } // if ($v['cartid'] == $cartid)

                } // foreach ($cart['products'] as $k => $v)

            } // if (is_array($cart['products']))

            func_header_location('cart.php');

        } elseif (
            !empty($active_modules['Wishlist'])
            && !empty($configurations[$productid]['wishlistid'])
        ) {

        // in the wish list

            $wishlistid = $configurations[$productid]['wishlistid'];

            func_array2update(
                'wishlist',
                array(
                    'amount'     => $amount,
                    'options'     => $addslashes(serialize($product_options)),
                    'object'     => addslashes(serialize($configurations[$productid])),
                ),
                'wishlistid=\'' . $wishlistid . '\''
            );

            unset($configurations[$productid]);

            if (!empty($active_modules['Gift_Registry'])) {

            // Redirect to the gift registry

                $eventid = func_query_first_cell("SELECT event_id FROM $sql_tbl[wishlist] WHERE wishlistid='$wishlistid'");

                if (!empty($eventid)) {
                    func_header_location("giftreg_manage.php?eventid=$eventid&mode=products");
                }

            }

            // Redirect to the wish list

            func_header_location("cart.php?mode=wishlist");

        }

        func_header_location("pconf.php?productid=$productid");

    } // if ($mode == 'pconf_update')

    if ($mode == 'add') {

        $pconf             = $productid;
        $product_info     = func_select_product($addproductid, $user_account['membershipid'], false, true, false, 'T');

        if (
            !empty($product_info)
            && func_pconf_is_slot_compatible($slot, $product_info, $configurations[$productid])
        ) {
            $slot_info     = func_pconf_get_slot_data($slot);

            $amount     = (intval($amount) <= 0)
                ? 1
                : intval($amount);

            $amount     = $slot_info['multiple'] != 'Y'
                ? 1
                : (
                    $amount < $slot_info['amount_min']
                        ? $slot_info['amount_min']
                        : min($amount, $slot_info['amount_max'])
                );

            $options = array();

            if (!empty($active_modules['Product_Options'])) {

                if (!empty($product_options)) {

                    if (func_check_product_options ($addproductid, $product_options)) {

                        $options = $product_options;

                    }

                } else {

                    $options = func_get_default_options($addproductid, $amount, @$user_account['membershipid']);

                    if ($options === false) {

                        func_403(30);

                    }

                }

            }

            $product = array(
                'productid'     => $addproductid,
                'productcode'     => $product_info['productcode'],
                'product'         => $product_info['product'],
                'amount'         => $amount,
                'options'         => $options,
            );

            $configurations[$productid]['steps'][$current_stepid]['slots'][$slot] = $product;

            func_unset($configurations[$productid], 'prefilled_with_defaults');

        }

    } // if ($mode == 'add')

    func_header_location("pconf.php?productid=$productid");

}

if (
    $mode == 'delete'
    && !empty($productid)
    && !empty($slot)
) {
/**
 * Delete product from the slot
 */
    unset($configurations[$productid]['steps'][$current_stepid]['slots'][$slot]);

    func_header_location("pconf.php?productid=$productid");
}

$smarty->assign('mode', 'configure_step');

/**
 * Get the wizards info for this product
 */
$wizards = func_pconf_get_wizards($productid);

if (is_array($wizards)) {
/**
 * Assign counters for steps and get the current step info
 */
    $counter = 1;

    foreach ($wizards as $k => $v) {

        $wizards[$k]['step_name']         = func_get_languages_alt($language_var_names['step_name'] . $v['stepid'], $store_language);
        $wizards[$k]['step_counter']     = $counter++;

        if ($current_stepid == $wizards[$k]['step_counter'])
            $current_step = $wizards[$k];
    }

    if (
        empty($current_step)
        || $current_stepid == 'last'
    ) {

    // Prepare for the configurator summary displaying

        $smarty->assign('mode',                         'pconf_summary');
        $smarty->assign('pconf_summary_image_width',     $pconf_summary_image_width);

        $configurations[$productid]['reconfigure'] = true;

        $current_stepid = $configurations[$productid]['current_stepid'] = 'last';

        x_session_save('configurations');

        $max_quantity = func_pconf_get_max_quantity($configurations[$productid]);

        $smarty->assign('max_quantity', $max_quantity);

        if(!empty($active_modules['Product_Options'])) {

            $options = $configurations[$productid]['options'];

            include $xcart_dir . '/modules/Product_Options/customer_options.php';

        }

        $smarty->assign('amount', $configurations[$productid]['amount']);

        $total_cost = $taxed_total_cost = $product_info['taxed_price'];

        $taxes = array();

        foreach ($wizards as $k => $v) {

            $wizards[$k]['slots'] = func_get_slots_info($v['stepid'], $v['step_counter']);

            if (!is_array($wizards[$k]['slots']))
                continue;

            foreach ($wizards[$k]['slots'] as $k1 => $v1) {

                if (empty($v1['product'])) {

                    $wizards[$k]['slots'][$k1]['product']['image_url']             = func_get_default_image('T');
                    $wizards[$k]['slots'][$k1]['product']['summary_image_x']     = $pconf_summary_image_width;

                    continue;
                }

                list(
                    $wizards[$k]['slots'][$k1]['product']['summary_image_x'],
                    $wizards[$k]['slots'][$k1]['product']['summary_image_y']
                ) = func_crop_dimensions($wizards[$k]['slots'][$k1]['product']['image_x'], $wizards[$k]["slots"][$k1]["product"]['image_y'], $pconf_summary_image_width, false);

                $total_cost += ($v1['product']['price'] * $v1['product']['amount']);

                $taxed_total_cost += ($v1['product']['taxed_price'] * $v1['product']['amount']);

                if (is_array($v1['product']['taxes'])) {

                    foreach ($v1['product']['taxes'] as $tax_name => $product_tax) {

                        if (!isset($taxes[$tax_name])) {

                            $taxes[$tax_name] = $product_tax;
                            $taxes[$tax_name]['tax_value'] = 0;

                        }

                        $taxes[$tax_name]['tax_value'] += $product_tax['tax_value'];
                    }

                }

                $configurations[$productid]['steps'][$v['step_counter']]['slots'][$v1['slotid']]['price'] = $v1['product']['price'];

            } // foreach ($wizards[$k]['slots'] as $k1 => $v1)

        } // foreach ($wizards as $k => $v)

        $configurations[$productid]['price'] = price_format($total_cost);

        x_session_save('configurations');

        $smarty->assign('total_cost',         $total_cost);
        $smarty->assign('taxed_total_cost', $taxed_total_cost);
        $smarty->assign('taxes',             $taxes);

        if (
            !empty($configurations[$productid]['cartid'])
            && is_array($cart['products'])
        ) {
            foreach ($cart['products'] as $k => $v) {

                if ($v['cartid'] == $configurations[$productid]['cartid']) {

                    $smarty->assign('update', 'cart');

                    break;
                }

            }

        } elseif (!empty($configurations[$productid]['wishlistid'])) {

            $smarty->assign('update', 'wishlist');

        }

        $product_info['avail']         = $max_quantity;
        $product_info['appearance'] = func_get_appearance_data($product_info);

    } else {

    // Prepare step for displaying

        $continue_button = true;

        $current_step['slots']         = func_get_slots_info($current_step['stepid'], $current_stepid);
        $current_step['step_descr'] = func_get_languages_alt($language_var_names['step_descr'] . $current_step['stepid'], $store_language);

        $smarty->assign('continue_button', $continue_button);
    }

    if (
        is_array($configurations[$productid]['steps'])
        && !empty($configurations[$productid]['steps'])
        && $config['General']['unlimited_products'] != 'Y'
    ) {

        $products_avails = array();
        $filled_slots = array();

        foreach ($configurations[$productid]['steps'] as $c_stepid => $c_slot_info) {

            if (
                !is_array($c_slot_info['slots'])
                || empty($c_slot_info['slots'])
            ) {
                continue;
            }

            $filled_slots[$c_stepid]++;

            foreach ($c_slot_info['slots'] as $c_slotid => $c_product_info) {

                $vid = 0;

                if (
                    !empty($active_modules['Product_Options'])
                    && !empty($c_product_info['options'])
                ) {
                    $vid = intval(func_get_variantid($c_product_info['options'], $c_product_info["productid"]));
                }

                if (!isset($products_avails[$c_product_info['productid']][$vid])) {

                    $products_avails[$c_product_info['productid']][$vid] = func_pconf_get_product_avail($c_product_info['productid'], $vid);

                }

            } // foreach ($c_slot_info['slots'] as $c_slotid => $c_product_info)

        } // foreach ($configurations[$productid]['steps'] as $c_stepid => $c_slot_info)

        foreach ($configurations as $c_productid => $c_step_info) {

            if (
                !is_array($c_step_info['steps'])
                || empty($c_step_info['steps'])
                || $c_productid == $productid
            ) {
                continue;
            }

            foreach ($c_step_info['steps'] as $c_stepid => $c_slot_info) {

                if (
                    !is_array($c_slot_info['slots'])
                    || empty($c_slot_info['slots'])
                ) {
                    continue;
                }

                foreach ($c_slot_info['slots'] as $c_slotid => $c_product_info) {

                    $vid = 0;

                    if (
                        !empty($active_modules['Product_Options'])
                        && !empty($c_product_info['options'])
                    ) {
                        $vid = intval(func_get_variantid($c_product_info['options'], $c_product_info["productid"]));
                    }

                    if (!isset($products_avails[$c_product_info['productid']][$vid]))
                        continue;

                    $products_avails[$c_product_info['productid']][$vid]--;

                } // foreach ($c_slot_info['slots'] as $c_slotid => $c_product_info)

            } // foreach ($c_step_info['steps'] as $c_stepid => $c_slot_info)

        } // foreach ($configurations as $c_productid => $c_step_info)

        foreach ($configurations[$productid]['steps'] as $c_stepid => $c_slot_info) {

            if (
                !is_array($c_slot_info['slots'])
                || empty($c_slot_info['slots'])
            ) {
                continue;
            }

            foreach ($c_slot_info['slots'] as $c_slotid => $c_product_info) {

                $vid = 0;

                if (
                    !empty($active_modules['Product_Options'])
                    && !empty($c_product_info['options'])
                ) {
                    $vid = intval(func_get_variantid($c_product_info['options'], $c_product_info["productid"]));
                }

                if (!isset($products_avails[$c_product_info['productid']][$vid]))
                    continue;

                $products_avails[$c_product_info['productid']][$vid]--;

                if ($products_avails[$c_product_info['productid']][$vid] < 0) {

                    $configurations[$productid]['steps'][$c_stepid]['slots'][$c_slotid]['pconf_out_of_stock'] = true;

                    foreach ($wizards as $wk => $wstep) {

                        if (
                            empty($wstep['slots'])
                            || !is_array($wstep['slots'])
                        ) {
                            continue;
                        }

                        foreach ($wstep['slots'] as $wsk => $wslot) {

                            if ($wslot['slotid'] != $c_slotid)
                                continue;

                            $wizards[$wk]['slots'][$wsk]['pconf_out_of_stock'] = true;

                            $pconf_out_of_stock = true;

                        } // foreach ($wstep['slots'] as $wsk => $wslot)

                    } // foreach ($wizards as $wk => $wstep)

                } // if ($products_avails[$c_product_info['productid']][$vid] < 0)

            } // foreach ($c_slot_info['slots'] as $c_slotid => $c_product_info)

        } // foreach ($configurations[$productid]['steps'] as $c_stepid => $c_slot_info)

    }

} // if (is_array($wizards))

/**
 * Assign the current step for displaying in the location line
 */
if ($current_stepid == 'last') {

    $location[] = array(
        func_get_langvar_by_name('lbl_summary'),
        '',
    );

} else {

    $location[] = array(
        func_get_langvar_by_name('lbl_step') . " $current_stepid",
        "pconf.php?productid=$productid",
    );

}

if ($current_stepid > 1)
    $previous_step = $current_stepid;

/**
 * Get the products list compatible with slot
 */
include $xcart_dir . '/modules/Product_Configurator/pconf_slot_products.php';

$smarty->assign('wizards',                 $wizards);
$smarty->assign('wizard_data',             $current_step);
$smarty->assign('step',                 $current_stepid);
$smarty->assign('previous_step',         $previous_step);
$smarty->assign('pconf_out_of_stock',     $pconf_out_of_stock);

/**
 * Define which action buttons to display
 */

$smarty->assign(
    'need_reset_btn',
    (
        !empty($def_configuration)
        && !$def_configuration_warn
        && !$configurations[$productid]['prefilled_with_defaults']
    )
);

$smarty->assign(
    'need_clean_btn',
    (
        isset($filled_slots[$current_stepid])
        && (count($wizards) > 1)
    )
);

$smarty->assign('need_cleanall_btn',     !empty($filled_slots));

?>
