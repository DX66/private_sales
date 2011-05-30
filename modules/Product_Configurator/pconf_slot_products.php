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
 * Gets compatible slot products
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: pconf_slot_products.php,v 1.26.2.2 2011/01/10 13:12:00 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

if (empty($active_modules['Product_Configurator'])) {
    func_header_location(func_get_resource_url('product', $productid));
}

if (!empty($slot)) { // $slot is $sql_tbl['pconf_slots'].slotid field

/**
 * Get the products list that satisfied with slot rules
 */
    if ($current_area == 'C') {
        if (is_array($current_step['slots'])) {
            foreach ($current_step['slots'] as $k=>$v) {
                if ($v['slotid'] == $slot) {
                    $current_slot = $v;
                    break;
                }
            }
        }

        if (empty($current_slot)) {
            func_page_not_found();
        }
    }

    // Get the rule array for the slot

    list($rules_by_or, $rules_prepared, $ptypes_and, $ptype_condition) = func_pconf_get_slot_rules($slot);

    // Get the initial products list

    if (!empty($ptype_condition)) {

        $old_search_data = $search_data['products'];
        $search_data['products'] = array(
        );

        $search_data['products']['_']['left_joins']['pconf_products_classes'] = array(
            'on' => "$sql_tbl[products].productid = $sql_tbl[pconf_products_classes].productid"
        );
        $search_data['products']['_']['where'] = array(
            "$sql_tbl[pconf_products_classes].productid IS NOT NULL",
            "$sql_tbl[products].forsale != 'N'",
            $ptype_condition
        );

        if ($current_area == 'P' && !$single_mode) {
            $search_data['products']['provider'] = $logged_userid;
        }

        $REQUEST_METHOD = 'GET';
        $_page = $page;
        $objects_per_page = 999999999;
        $mode = 'search';
        $_inner_search = true;
        include $xcart_dir.'/include/search.php';
        $_inner_search = false;

        $search_data['products'] = $old_search_data;
        x_session_save('search_data');

        $slot_products = $products;
        $page = $_page;
        unset($products, $old_search_data, $_page, $objects_per_page, $total_nav_pages, $objects_per_page, $total_pages);

        if (!empty($slot_products)) {
            foreach ($slot_products as $k => $v) {
                $slot_products[$k]['classes'] = func_query("SELECT classid, ptypeid FROM $sql_tbl[pconf_products_classes] WHERE productid = '$v[productid]'");
                if ($current_area == 'C')
                    $slot_products[$k]['alt_url'] = "product.php?productid=".$v['productid']."&pconf=".$productid."&slot=".$slot;
            }

            // Check if the found products satisfy the slot rules

            if (!empty($rules_prepared)) {
                $matched_products = array();
                foreach ($slot_products as $k => $_product) {
                    if (func_pconf_product_match_rules($_product['productid'], $rules_prepared))
                        $matched_products[] = $_product;
                }
                $slot_products = $matched_products;
                unset($matched_products);
            }

            if (!empty($active_modules['Feature_Comparison']))
                $smarty->assign('products_has_fclasses', $products_has_fclasses);
        }
    }

    if (is_array($configurations)) {

        if (!empty($slot_products)) {
            foreach ($configurations as $c_productid => $c_step_info) {
                if (!is_array($c_step_info['steps']) || empty($c_step_info['steps']))
                    continue;

                foreach ($c_step_info['steps'] as $c_stepid => $c_slot_info) {
                    if (!is_array($c_slot_info['slots']) || empty($c_slot_info['slots']))
                        continue;

                    foreach ($c_slot_info['slots'] as $c_slotid => $c_product_info) {

                        foreach ($slot_products as $spk => $sp) {

                            if ($c_product_info['productid'] != $sp['productid'] || !empty($sp['variantid']))
                                continue;

                            $slot_products[$spk]['avail'] -= $c_product_info["amount"];
                        }
                    }
                }
            }
        }

        // Get the list of products that already are in the slots and the requirements (on specifications) list

        list($filled_slots, $found_products_specs, $required_specifications) = func_pconf_prepare_requirements($slot, $configurations[$productid], $ptypes_and);

        // Checking if found products are satisfied to requirements

        if (is_array($slot_products) && is_array($required_specifications)) {
            $matched_products = array();
            foreach ($slot_products as $k => $v) {
                if (func_pconf_product_match_requirements($v, $required_specifications, $filled_slots, $found_products_specs)) {
                    $matched_products[] = $v;
                }
            }
            $slot_products = $matched_products;
        }

        $smarty->assign('filled_slots', $filled_slots);
    }

    if ($current_area == 'C' && !empty($active_modules['Product_Options']) && is_array($slot_products)) {
        foreach ($slot_products as $k=>$v) {
            $slot_products[$k]['product_options_counter'] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[classes] WHERE productid='$v[productid]'");
        }
    }

    if ($current_area == 'C' && is_array($slot_products)) {

        // Navigation pages

        $total_items = $total_items_in_search = count($slot_products);
        $objects_per_page = $config['Appearance']['products_per_page'];

        include $xcart_dir.'/include/navigation.php';

        $smarty->assign('navigation_script',"pconf.php?productid=$productid&slot=$slot");
        $slot_products = array_slice($slot_products, $first_page, $objects_per_page);
        foreach ($slot_products as $k=>$v) {
            $slot_products[$k]['taxes'] = func_get_product_taxes($slot_products[$k], $logged_userid);

            if ($current_slot['multiple']=="Y") {
                if ($current_slot['product'] && $current_slot['product']['productid'] == $v['productid'])
                    $v['avail'] += $current_slot['product']['amount'];
                    $slot_products[$k]['appearance'] = func_get_appearance_data($v);
            }
        }
    }

    $location[] = array(func_get_langvar_by_name('lbl_pconf_select_the_n', array('slot' => $current_slot['slot_name']), false, true),"");
    $smarty->assign('slot_products', $slot_products);
    $smarty->assign('rules_by_or', $rules_by_or);
    $smarty->assign('slot_data', $current_slot);
    $smarty->assign('slot', $slot);

    if (!empty($active_modules['Feature_Comparison']))
        $smarty->assign('products_has_fclasses', $products_has_fclasses);
}
?>
