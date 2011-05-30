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
 * Functions for the Product configurator module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.php,v 1.27.2.2 2011/02/07 15:34:46 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */
if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

/**
 * This function calculates the available quantity for sale for product
 */
function func_pconf_get_max_quantity($configuration)
{
    global $sql_tbl, $cart, $active_modules;
    global $config;

    $max_quantity = "not defined";

    if (is_array($configuration['steps'])) {

        foreach ($configuration['steps'] as $stepid => $stepinfo) {

            if (is_array($stepinfo['slots'])) {

                foreach ($stepinfo['slots'] as $slotid => $slotinfo) {

                    $variantid = empty($active_modules['Product_Options'])
                        ? 0
                        : func_get_variantid($slotinfo['options'], $slotinfo['productid']);

                    // Search in cart
                    $blocked = 0;

                    if (
                        !empty($cart)
                        && !empty($cart['products'])
                    ) {

                        foreach ($cart['products'] as $cart_item) {

                            if (
                                $cart_item['productid'] == $slotinfo['productid']
                                && $cart_item['variantid'] == $variantid
                            ) {

                                if (
                                    $cart_item['hidden']
                                    && $cart_item['hidden'] == $configuration['cartid']
                                ) {
                                    continue;
                                }

                                $blocked += $cart_item['amount'];

                            }

                        }

                    }

                    // Search in current configuration
                    foreach ($configuration['steps'] as $_stepid => $_stepinfo) {

                        if (!is_array($_stepinfo['slots']))
                            continue;

                        foreach ($_stepinfo['slots'] as $_slotid => $_slotinfo) {

                            $_variantid = empty($active_modules['Product_Options'])
                                ? 0
                                : func_get_variantid($slotinfo['options'], $slotinfo['productid']);

                            if (
                                $_slotinfo['productid'] == $slotinfo['productid']
                                && $variantid == $_variantid
                                && $_slotid != $slotid
                            ) {
                                $blocked++;
                            }

                        }

                    }

                    if ($config['General']['unlimited_products'] == 'Y') {

                        $avail = $config['Appearance']['max_select_quantity'];

                    } elseif (empty($variantid)) {

                        $avail = func_query_first_cell("SELECT avail-$blocked FROM $sql_tbl[products] WHERE productid = '$slotinfo[productid]'");

                    } else {

                        $avail = func_query_first_cell("SELECT avail-$blocked FROM $sql_tbl[variants] WHERE productid = '$slotinfo[productid]' AND variantid = '$variantid'");

                    }

                    if (
                        $max_quantity == "not defined"
                        || $max_quantity > $avail
                    ) {
                        $max_quantity = $avail;
                    }

                } // foreach ($stepinfo['slots'] as $slotid => $slotinfo)

            } // if (is_array($stepinfo['slots']))

        } // foreach ($configuration['steps'] as $stepid => $stepinfo)

    } elseif ($config['Product_Configurator']['allow_to_add_empty_product_to_cart'] == 'Y') {

        $max_quantity = 1;

    }

    return $max_quantity > 0
        ? $max_quantity
        : 0;
}

/**
 * Get product quantity
 */
function func_pconf_get_product_avail($productid, $variantid = 0)
{
    global $sql_tbl, $active_modules, $cart;

    if (
        empty($variantid)
        || empty($active_modules['Product_Options'])
    ) {

        $avail = func_query_first_cell("SELECT avail FROM $sql_tbl[products] WHERE productid = '$productid'");

    } else {

        $avail = func_query_first_cell("SELECT avail FROM $sql_tbl[variants] WHERE productid = '$productid' AND variantid = '$variantid'");

    }

    if (isset($cart['products'])) {

        foreach ($cart['products'] as $p) {

            if (
                $p['productid'] != $productid
                || $p['variantid'] != $variantid
            ) {
                continue;
            }

            $avail -= $p['amount'];

        }

    }

    return intval($avail);
}

/**
 * Get wizards info for the product
 */
function func_pconf_get_wizards($productid)
{
    global $sql_tbl;

    return func_query("SELECT * FROM $sql_tbl[pconf_wizards] WHERE productid='$productid' ORDER BY orderby, stepid");
}

/**
 * Get the markups data for the slot
 */
function func_pconf_get_markups($slot)
{
    global $sql_tbl;

    return func_query("SELECT * FROM $sql_tbl[pconf_slot_markups] WHERE slotid='$slot' ORDER BY markupid");
}

/**
 * Get the slot data
 */
function func_pconf_get_slot_data($slot)
{
    global $sql_tbl, $language_var_names, $current_language;

    $slot_data = func_query_first("SELECT * FROM $sql_tbl[pconf_slots] WHERE slotid='$slot'");

    if (empty($slot_data))
        return false;

    $slot_data['slot_name']     = func_get_languages_alt($language_var_names['slot_name'] . $slot_data['slotid'], $current_language);
    $slot_data['slot_descr']     = func_get_languages_alt($language_var_names['slot_descr'] . $slot_data['slotid'], $current_language);

    // Get default product
    if (!empty($slot_data['default_productid'])) {

        $slot_data['default_product'] = func_query_first_cell("SELECT IF($sql_tbl[products_lng].productid != '', $sql_tbl[products_lng].product, $sql_tbl[products].product) as product FROM $sql_tbl[products] LEFT JOIN $sql_tbl[products_lng] ON $sql_tbl[products_lng].code='$current_language' AND $sql_tbl[products_lng].productid = $sql_tbl[products].productid WHERE $sql_tbl[products].productid = '".$slot_data["default_productid"]."'");

    }

    return $slot_data;
}

/**
 * Check if the slot may contain several items of the product
 */
function func_pconf_is_multiple($slot)
{
    global $sql_tbl;

    return func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[pconf_slots] WHERE slotid='$slot' AND multiple='Y'");
}

/**
 * Get rules data for the slot
 */
function func_pconf_get_slot_rules($slot)
{
    global $sql_tbl, $shop_language;

    // Get the indexes by AND
    $indexes_by_and = func_query("SELECT index_by_and FROM $sql_tbl[pconf_slot_rules] WHERE slotid='$slot' GROUP BY index_by_and ORDER BY index_by_and");

    if (is_array($indexes_by_and)) {

        // Get the indexes by OR and generate the query string to search products
        foreach ($indexes_by_and as $k => $v) {

            $rule_by_or['index_by_and'] = $v['index_by_and'];
            $rule_by_or['rules_by_and'] = func_query("SELECT $sql_tbl[pconf_product_types].* FROM $sql_tbl[pconf_product_types], $sql_tbl[pconf_slot_rules] WHERE $sql_tbl[pconf_product_types].ptypeid=$sql_tbl[pconf_slot_rules].ptypeid AND $sql_tbl[pconf_slot_rules].slotid='$slot' AND $sql_tbl[pconf_slot_rules].index_by_and='$v[index_by_and]'");

            if (is_array($rule_by_or['rules_by_and'])) {

                foreach ($rule_by_or['rules_by_and'] as $k1 => $v1) {

                    $ptypes_and[] = $v1['ptypeid'];

                    $tmp = func_get_languages_alt('ptype_name_' . $v1['ptypeid'], $shop_language);

                    if (!empty($tmp)) {
                        $rule_by_or['rules_by_and'][$k1]['ptype_name'] = $tmp;
                    }

                }

                $ptype_or_condition_array[] = "$sql_tbl[pconf_products_classes].ptypeid IN (" . implode(",", $ptypes_and) . ")";

            }

            $ptype_condition = "(" . implode(" OR ", $ptype_or_condition_array) . ")";

            $rules_by_or[] = $rule_by_or;

        } // foreach ($indexes_by_and as $k => $v)

    } // if (is_array($indexes_by_and))

    // Prepare the product tyoe indexes array
    $rules_prepared = array();

    for ($i = 0; $i < count($rules_by_or); $i++) {

        if (
            is_array($rules_by_or[$i]['rules_by_and'])
            && count($rules_by_or[$i]['rules_by_and']) > 1
        ) {

            $rule_by_and = array();

            for ($j = 0; $j < count($rules_by_or[$i]['rules_by_and']); $j++) {

                $rule_by_and[] = $rules_by_or[$i]['rules_by_and'][$j]['ptypeid'];

            }

        } else {

            $rule_by_and = $rules_by_or[$i]['rules_by_and'][0]['ptypeid'];

        }

        $rules_prepared[] = $rule_by_and;

    }

    return array(
        $rules_by_or,
        $rules_prepared,
        $ptypes_and,
        $ptype_condition,
    );

}

/**
 * Check if the found products satisfy the slot rules
 */
function func_pconf_product_match_rules($productid, $rules_prepared)
{
    global $sql_tbl;

    $matched = false;

    if (empty($rules_prepared))
        $matched = true;

    foreach ($rules_prepared as $_rule) {

        if (is_array($_rule)) {

            $_rule_condition = "IN ('" . implode("','", $_rule) . "')";

            $_ptype_limit = count($_rule);

        } else {

            $_rule_condition = "= '$_rule'";

            $_ptype_limit = 1;

        }

        if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products], $sql_tbl[pconf_products_classes] WHERE $sql_tbl[products].productid=$sql_tbl[pconf_products_classes].productid AND $sql_tbl[products].productid = '$productid' AND $sql_tbl[pconf_products_classes].ptypeid ".$_rule_condition) == $_ptype_limit) {

            $matched = true;

            break;

        }

    }

    return $matched;
}

/**
 * Prepare the data that will be used during further check
 * for product compatibility with slot
 */
function func_pconf_prepare_requirements($slot, $configuration, $ptypes_and = false)
{
    global $sql_tbl, $shop_language;
    global $check_bidirectional_requirements, $language_var_names;

    // Get list of products that already are in the slots

    $found_products_specs     = array();
    $filled_slots             = array();

    if (is_array($configuration['steps'])) {

        foreach ($configuration['steps'] as $c_stepid => $c_slot_info) {

            if (!is_array($c_slot_info['slots']))
                continue;

            foreach ($c_slot_info['slots'] as $c_slotid => $c_product_info) {

                if ($c_slotid == $slot)
                    continue;

                $filled_slot['slot_name']     = func_query_first_cell("SELECT slot_name FROM $sql_tbl[pconf_slots] WHERE slotid='$c_slotid'");
                $filled_slot['slot_name']     = func_get_languages_alt($language_var_names['slot_name'] . $c_slotid, $shop_language);
                $filled_slot['product']     = $c_product_info;

                if ($check_bidirectional_requirements == 'Y') {

                    $tmp_ptype_info = func_query_first("SELECT ptypeid, classid FROM $sql_tbl[pconf_products_classes] WHERE productid='$c_product_info[productid]'");

                    $tmp_specs_info = func_query("SELECT specid FROM $sql_tbl[pconf_class_specifications] WHERE classid='$tmp_ptype_info[classid]'");

                    if (is_array($tmp_specs_info)) {

                        foreach ($tmp_specs_info as $t_spec) {

                            $found_products_specs[$c_product_info['productid']][$tmp_ptype_info['ptypeid']][] = $t_spec['specid'];

                        }

                    } // if (is_array($tmp_specs_info))

                } // if ($check_bidirectional_requirements == 'Y')

                $filled_slots[] = $filled_slot;

            } // foreach ($c_slot_info['slots'] as $c_slotid => $c_product_info)

        } // foreach ($configuration['steps'] as $c_stepid => $c_slot_info)

    } // if (is_array($configuration['steps']))

    // Get the requirements (on specifications) list

    if (
        is_array($filled_slots)
        && is_array($ptypes_and)
    ) {

        foreach ($filled_slots as $k => $v) {

            $classes = func_query("SELECT * FROM $sql_tbl[pconf_products_classes] WHERE productid='".$v["product"]["productid"]."'");

            if (!is_array($classes))
                continue;

            foreach ($classes as $k1 => $v1) {

                $requirements = func_query("SELECT * FROM $sql_tbl[pconf_class_requirements] WHERE classid='$v1[classid]' ORDER BY ptypeid");

                if (!is_array($requirements))
                    continue;

                foreach ($requirements as $k2 => $v2) {

                    if (
                        in_array($v2['ptypeid'], $ptypes_and)
                        && $v2['specid'] > 0
                    ) {

                        if (
                            @is_array($required_specifications[$v2['ptypeid']])
                            && in_array($v2['specid'], $required_specifications[$v2['ptypeid']])
                        ) {

                            continue;

                        } else {

                            $required_specifications[$v2['ptypeid']][] = $v2['specid'];

                        }

                    }

                } // foreach ($requirements as $k2 => $v2)

            } // foreach ($classes as $k1 => $v1)

        } // foreach ($filled_slots as $k => $v)

    }

    return array(
        $filled_slots,
        $found_products_specs,
        $required_specifications,
    );
}

function func_pconf_product_match_requirements($product, $required_specifications, $filled_slots = false, $found_products_specs = false)
{
    global $sql_tbl, $active_modules;
    global $check_product_type_requirements_by_or;
    global $check_bidirectional_requirements;

    // Main step: Check if the product is satisfied to requirements

    $flag1 = $check_product_type_requirements_by_or !== 'Y';

    if (
        is_array($product['classes'])
        && !empty($product['classes'])
    ) {

        foreach ($product['classes'] as $_pclass) {

            if (!is_array($required_specifications[$_pclass['ptypeid']]))
                continue;

            $spec_query = " AND specid IN (" . implode(",", $required_specifications[$_pclass['ptypeid']]) . ")";

            $check_requirements = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[pconf_class_specifications] WHERE classid='$_pclass[classid]' $spec_query");

            $flag1_tmp = ($check_requirements == count($required_specifications[$_pclass['ptypeid']]));

            $flag1 = $check_product_type_requirements_by_or == 'Y'
                ? $flag1 || $flag1_tmp
                : $flag1 && $flag1_tmp;

        }

    }

    // Additional step: check if slotted products are satisfied reqs of the found products

    $flag2 = true;

    if (
        $check_bidirectional_requirements == 'Y'
        && !empty($filled_slots)
        && !empty($found_products_specs)
        && $flag1
    ) {

        $required_specifications = func_query_hash("SELECT $sql_tbl[pconf_class_requirements].ptypeid, $sql_tbl[pconf_class_requirements].specid FROM $sql_tbl[pconf_class_requirements], $sql_tbl[pconf_products_classes] WHERE $sql_tbl[pconf_products_classes].classid = $sql_tbl[pconf_class_requirements].classid AND $sql_tbl[pconf_products_classes].productid = '".$product["productid"]."' AND $sql_tbl[pconf_class_requirements].specid > '0' ORDER BY $sql_tbl[pconf_class_requirements].ptypeid", "ptypeid", true, true);

        // Checking found product...
        if (
            is_array($required_specifications)
            || !empty($required_specifications)
        ) {

            foreach ($required_specifications as $t_ptypeid => $t_specs) {

                foreach ($found_products_specs as $t_prodtypes) {

                    if (
                        !is_array($t_prodtypes)
                        || empty($t_prodtypes[$t_ptypeid])
                    ) {
                        continue;
                    }

                    $found = false;

                    foreach ($t_specs as $t_specid) {

                        if (in_array($t_specid, $t_prodtypes[$t_ptypeid])) {

                            $found = true;

                            break;

                        }

                    }

                    if (!$found) {

                        $flag2 = false;

                        break;

                    }

                } // foreach ($found_products_specs as $t_prodtypes)

                if (!$flag2)
                    break;

            } // foreach ($required_specifications as $t_ptypeid => $t_specs)

        }

    }

    return ($flag1 && $flag2);
}

?>
