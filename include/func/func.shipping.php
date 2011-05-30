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
 * Common shipping functions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Cart
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.shipping.php,v 1.23.2.10 2011/04/07 10:41:31 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

define('SHIPPING_SYSTEM', 1);

x_load(
    'cart',
    'pack'
);

/**
 * This function creates the shipping methods/rates list
 */
function func_get_shipping_methods_list($cart, $products, $userinfo, $return_all_available = false)
{
    global $sql_tbl, $config, $active_modules, $single_mode, $smarty;
    global $xcart_dir;
    global $intershipper_recalc, $intershipper_rates;
    global $intershipper_error;
    global $real_time_rates;
    global $shipping_calc_service;
    global $current_carrier;
    global $login;
    global $arb_account_used;
    global $empty_other_carriers, $empty_ups_carrier;
    global $_carriers;
    global $checkout_module;

    if (
        empty($products)
        || $config['Shipping']['enable_shipping'] != 'Y'
    ) {
        return;
    }

    if (
        (
            empty($login)
            || (
                $config['General']['enable_anonymous_checkout'] == 'Y'
                && empty($userinfo)
            )
        )
        && $config['General']['apply_default_country'] != 'Y'
        && $config['Shipping']['enable_all_shippings'] == 'Y'
    ) {
        $enable_all_shippings = true;
        $smarty->assign('force_delivery_dropdown_box', 'Y');
    }

    // If $enable_shipping then calculate shipping rates

    $enable_shipping = (
        (
            !empty($userinfo)
            && (
                !empty($login)
                || $config['General']['enable_anonymous_checkout'] == 'Y'
            )
        )
        || $config['General']['apply_default_country'] == 'Y'
    );

    // Check if all products have free shipping

    $all_products_free_shipping = true;
    if (!empty($cart['products'])) {

        foreach($cart['products'] as $k => $product)
            if (
                $product['free_shipping'] != 'Y'
                && !@$product['free_shipping_used']
            ) {
                $all_products_free_shipping = false;
                break;
            }
    }

    // Get the total products weight that is valid for rates calculation

    $total_weight_shipping_valid = func_weight_shipping_products($cart['products'], true, $all_products_free_shipping, false);

    // Get the total products weight that is valid for rates calculation per provider

    $total_weight_shipping_array = func_weight_shipping_products($cart['products'], true, $all_products_free_shipping, false, true);

    // Get the max weight of products that is valid for rates calculation

    $max_weight_shipping_valid = func_weight_shipping_products($cart['products'], true, $all_products_free_shipping, true);

    // Collect products subtotal

    $cart_subtotal = 0;
    if (!empty($cart['products'])) {

        foreach($cart['products'] as $k=>$product) {

            // for Advanced_Order_Management module
            if (@$product['deleted'])
                continue;

            if (!empty($active_modules['Egoods']) && $product['distribution'] != '')
                continue;

            // Calculate total_cost and total_+weight for selection condition
            if (
                $product['free_shipping'] != 'Y'
                || $config['Shipping']['free_shipping_weight_select'] == 'Y'
            ) {
                $cart_subtotal += $product['subtotal'];
            }

        }

    }

    // The preparing to search the allowable shipping methods

    $weight_condition = " AND weight_min<='$total_weight_shipping_valid' AND (weight_limit='0' OR weight_limit>='$max_weight_shipping_valid')";

    if (
        (
            $enable_shipping
            || $config['Shipping']['enable_all_shippings'] != 'Y'
        )
        && !$return_all_available
    ) {
        $destination_condition = " AND destination="
            . (
                !empty($userinfo)
                && (
                    (
                        empty($userinfo['s_country'])
                        && $config['Company']['location_country'] == $config['General']['default_country']
                    )
                    || $userinfo['s_country'] == $config['Company']['location_country']
                )
                ? "'L'"
                : "'I'"
            );
    } else {
        $destination_condition = '';
    }

    if (
        $config['Shipping']['realtime_shipping'] == 'Y'
        && $enable_shipping
        && $intershipper_recalc != 'N'
    ) {
        x_load('http');

        $default_seller_address = array(
            'city'         => $config['Company']['location_city'],
            'state'     => $config['Company']['location_state'],
            'country'     => $config['Company']['location_country'],
            'zipcode'     => $config['Company']['location_zipcode']
        );

        // Prepare products list for packing
        /*
            $ignore_freight= ? is used to ignore $product['shipping_freight'] for Amazon/Google checkouts
        */
        $ignore_freight = 
            $checkout_module == 'Amazon_Checkout'
            || $checkout_module == 'Google_Checkout'
            || defined('GOOGLE_CHECKOUT_CALLBACK')
            || defined('AMAZON_CHECKOUT_CALLBACK');

        $items_for_packing = func_prepare_items_list($cart['products'], $ignore_freight, $all_products_free_shipping);

        if (!$single_mode) {
            $products_providers = func_get_products_providers($items_for_packing);
            $providers_data = array();

            if (is_array($products_providers)) {

                foreach ($products_providers as $_provider) {

                    $providers_data[$_provider]['seller_address'] = (func_is_seller_address_empty($_provider, 'N'))
                        ? $default_seller_address
                        : func_query_first("SELECT * FROM $sql_tbl[seller_addresses] WHERE userid='$_provider'");

                }

                foreach ($items_for_packing as $item) {

                    $providers_data[$item['provider']]['items'][] = $item;

                }
            }

        } else {

            $providers_data['provider']['seller_address'] = $default_seller_address;
            $providers_data['provider']['items'] = $items_for_packing;

        }

        // Get the real time shipping rates

        if ($config['Shipping']['use_intershipper'] == 'Y') {

            include_once $xcart_dir . '/shipping/intershipper.php';

        } else {

            include_once $xcart_dir . '/shipping/myshipper.php';

        }

        func_https_ctl('IGNORE');

        $real_time_rates = array();

        foreach ($providers_data as $_provider => $data) {

            $intershipper_rates = func_shipper($data['items'], $userinfo, $data['seller_address'], 'N', $cart);

            if (empty($intershipper_rates)) {

                $real_time_rates = array();
                break;

            } else {

                $real_time_rates[$_provider] = $intershipper_rates;

            }
        }

        // Intersect rates arrays to get list of methodid(s) available for all providers
        if (!empty($real_time_rates))
            $intershipper_rates = func_intersect_rates($real_time_rates);

        if ($empty_other_carriers == 'Y') {

            $shipping = func_query("SELECT * FROM $sql_tbl[shipping] WHERE code='' AND active='Y' $destination_condition $weight_condition ORDER BY orderby");

            if (!empty($shipping)) {
                $tmp_shipping = array();
                foreach ($shipping as $k => $v) {

                    if ($v['code']=="") {

                        $v['allowed'] = $is_method_allowed = func_is_shipping_method_allowable(
                            $v['shippingid'],
                            $userinfo,
                            $products,
                            $total_weight_shipping_valid,
                            $cart_subtotal,
                            $total_weight_shipping_array
                        );

                        if (!$return_all_available && !$is_method_allowed)
                            continue;
                    }

                    $tmp_shipping[] = $v;
                }

                if (is_array($tmp_shipping)) {

                    $tmp_cart = $cart;

                    foreach ($tmp_shipping as $k => $v) {

                    // Fetch shipping rate if it wasn't defined

                        if (!$v['allowed'])
                            continue;

                        $tmp_cart['shippingid'] = $v['shippingid'];
                        $calc_result = func_calculate($tmp_cart, $products, $userinfo['id'], $userinfo['usertype']);

                        if (!empty($calc_result['display_shipping_cost'])) {

                            $empty_other_carriers = 'N';

                        }
                    }

                    unset($tmp_cart);
                }

            }

        }

        $smarty->assign('is_other_carriers_empty',     $empty_other_carriers);
        $smarty->assign('is_ups_carrier_empty',     $empty_ups_carrier);

        func_https_ctl('STORE');

        if (!empty($intershipper_error)){

            $smarty->assign('shipping_calc_service',    $shipping_calc_service ? $shipping_calc_service : 'Intershipper');
            $smarty->assign('shipping_calc_error',        $intershipper_error);

            $msg  = "Service: " . ($shipping_calc_service ? $shipping_calc_service : 'Intershipper') . "\n";
            $msg .= "Error: " . $intershipper_error . "\n";
            $msg .= "Login: " . $login . "\n";
            $msg .= "Shipping address: " . $userinfo['s_address'] . " " . @$userinfo['s_address_2'] . "\n";
            $msg .= "Shipping city: " . $userinfo['s_city'] . "\n";
            $msg .= "Shipping state: " . $userinfo['s_statename'] . "\n";
            $msg .= "Shipping country: " . $userinfo['s_countryname'] . "\n";
            $msg .= "Shipping zipcode: " . $userinfo['s_zipcode'];

            x_log_add('SHIPPING', $msg);
        }

        $intershipper_recalc = 'N';
    }

    if (
        !empty($active_modules['UPS_OnLine_Tools'])
        && $config['Shipping']['use_intershipper'] != 'Y'
    ) {

        $condition = '';

        if (!empty($enable_all_shippings)) {

            global $ups_services;

            include $xcart_dir . '/modules/UPS_OnLine_Tools/ups_shipping_methods.php';

        }

        $ups_condition = $condition;

        if (
            $config['Shipping']['realtime_shipping'] == 'Y'
            && $current_carrier == 'UPS'
        ) {
            $ups_condition .= " AND $sql_tbl[shipping].code='UPS' AND $sql_tbl[shipping].service_code!=''";
        }

        if (!defined('ALL_CARRIERS')) {

            $weight_condition .= $ups_condition;

        }

    }

    $_carriers = array(
        'UPS'     => 0,
        'other' => 0,
    );

    if (
        !empty($active_modules['UPS_OnLine_Tools'])
        && $config['Shipping']['realtime_shipping'] == 'Y'
        && $config['Shipping']['use_intershipper'] != 'Y'
    ) {

        $_carriers['UPS'] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[shipping] WHERE code='UPS' AND service_code!='' AND weight_min<='$total_weight_shipping_valid' AND (weight_limit='0' OR weight_limit>='$max_weight_shipping_valid') AND active='Y'");

        $_carriers['other'] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[shipping] WHERE code<>'UPS' AND weight_min<='$total_weight_shipping_valid' AND (weight_limit='0' OR weight_limit>='$max_weight_shipping_valid') AND active='Y'");

        if (
            $_carriers['UPS'] == 0
            || $_carriers['other'] == 0
        ) {

            $current_carrier = ($_carriers['UPS'] == 0 ? '' : 'UPS');
            x_session_save('current_carrier');

        } else {

            $smarty->assign('show_carriers_selector', 'Y');

        }

    }

    if (
        !$enable_shipping
        || $config['Shipping']['realtime_shipping'] != 'Y'
    ) {

        // Get ALL shipping methods according to the conditions (W/O real time)

        $shipping = func_query("SELECT * FROM $sql_tbl[shipping] WHERE active='Y' $destination_condition $weight_condition ORDER BY orderby");

    } else {

        // Gathering the defined shipping methods

        $shipping = func_query ("SELECT * FROM $sql_tbl[shipping] WHERE code='' AND active='Y' $destination_condition $weight_condition ORDER BY orderby");

        if ($intershipper_rates) {

            // Gathering the shipping methods from $intershipper_rates

            foreach ($intershipper_rates as $intershipper_rate) {

                $ship_time = '';

                if (!empty($intershipper_rate['shipping_time'])) {
                    $ship_time = is_numeric($intershipper_rate['shipping_time'])
                        ? $intershipper_rate['shipping_time'] . ' ' . func_get_langvar_by_name('lbl_day_s', array(), false, false, true)
                        : $intershipper_rate['shipping_time'];
                }

                $ship_time_column = '' == $ship_time
                    ? 'shipping_time'
                    : '\'' . $ship_time . '\' AS shipping_time';

                settype($intershipper_rate['warning'], 'string');
                $result = func_query_first("SELECT *, '$intershipper_rate[rate]' AS rate, '$intershipper_rate[warning]' AS warning, $ship_time_column FROM $sql_tbl[shipping] WHERE subcode='$intershipper_rate[methodid]' AND active='Y' $weight_condition ORDER BY orderby");

                if ($result) {
                    $result['allowed'] = true;
                    $shipping[] = $result;
                }
            }
        }

        if (is_array($shipping)) {

            usort($shipping, 'usort_array_cmp_orderby');

        }

    }

    if (!empty($shipping)) {

        // Final preparing the shipping methods list

        $tmp_shipping = array();

        if (
            (
                defined('GOOGLE_CHECKOUT_CALLBACK')
                || defined('AMAZON_CHECKOUT_CALLBACK')
                || $return_all_available
            )
            && !empty($shipping)
        ) {
            $shipping = func_rename_duplicate_shippings($shipping);
        }

        foreach ($shipping as $k => $v) {
            if (
                (
                    $config['Shipping']['realtime_shipping'] == 'Y'
                    && $v['code'] == ''
                )
                || $config['Shipping']['realtime_shipping'] != 'Y'
            ) {

                // Check accessibility only for defined shipping methods

                $v['allowed'] = $is_method_allowed = func_is_shipping_method_allowable(
                    $v['shippingid'],
                    $userinfo,
                    $products,
                    $total_weight_shipping_valid,
                    $cart_subtotal,
                    $total_weight_shipping_array
                );

                if (!$return_all_available && !$is_method_allowed)
                    continue;

            } elseif (
                $config['Shipping']['realtime_shipping'] == 'Y'
                && $v['code'] != ''
                && !$enable_shipping
            ) {
                continue;
            }

            $tmp_shipping[] = $v;
        }

        $shipping = $tmp_shipping;
        unset($tmp_shipping);

        if (is_array($shipping)) {

            $tmp_cart = $cart;

            foreach ($shipping as $k=>$v) {

                // Fetch shipping rate if it wasn't defined

                if (!$v['allowed'])
                    continue;

                $tmp_cart['shippingid'] = $v['shippingid'];

                $calc_result = func_calculate(
                    $tmp_cart,
                    $products,
                    @$userinfo['id'],
                    @$userinfo['usertype']
                );

                $shipping[$k]['rate']         = $calc_result['display_shipping_cost'];
                $shipping[$k]['tax_cost']     = price_format($calc_result['tax_cost']);
            }

            unset($tmp_cart);
        }

    }

    if (
        $arb_account_used
        && is_array($shipping)
    ) {
        foreach ($shipping as $v) {
            if (
                $v['code'] == 'ARB'
                && $v['shippingid'] == $cart['shippingid']
            ) {
                $smarty->assign('arb_account_used', true);
                break;
            }
        }
    }

    if ($shipping)
        return $shipping;
    else
        return;
}

/**
 * This function checks if shipping method have defined shipping rates
 */
function func_is_shipping_method_allowable($shippingid, $customer_info, $products, $weight = 0, $subtotal=0, $weight_array=array())
{
    global $sql_tbl, $config, $single_mode;
    global $login;

    if (empty($customer_info)) {
        if ($config['Shipping']['enable_all_shippings'] == 'Y')
            return true;

        if ($config['General']['apply_default_country'] != 'Y')
            return false;
    }

    $shipping_query = "SELECT COUNT(*) FROM $sql_tbl[shipping_rates] WHERE shippingid='$shippingid' AND minweight<='{{weight}}' AND maxweight>='{{weight}}' AND mintotal<='$subtotal' AND maxtotal>='$subtotal' AND type='D'";

    if ($single_mode) {
        $customer_zone = func_get_customer_zone_ship($customer_info, '', 'D');
        $shipping = func_query_first_cell(str_replace('{{weight}}', $weight, $shipping_query)." AND zoneid='".addslashes($customer_zone)."'");

        return !empty($shipping);
    }

    $product_providers = func_get_products_providers($products);

    if (empty($product_providers) || !is_array($product_providers)) {

        return false;

    }

    $shipping = true;

    foreach ($product_providers as $provider) {
        $provider_condition = " AND provider='$provider'";
        $customer_zone = func_get_customer_zone_ship($customer_info, $provider, 'D');

        $shipping = $shipping && func_query_first_cell(
            str_replace('{{weight}}', $weight_array[$provider], $shipping_query)
            . " AND zoneid='"
            . addslashes($customer_zone)
            . "' "
            . $provider_condition
        );

        if (!$shipping) {
            return false;
        }
    }

    return $shipping;
}

/**
 * Add new realtime shipping method
 */
function func_add_new_smethod($method, $code, $added = array())
{
    global $sql_tbl;

    if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[shipping] WHERE code = '".addslashes($code)."'") == 0)
        return false;

    if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[shipping] WHERE shipping = '".addslashes($method)."' AND code = '".addslashes($code)."'") > 0)
        return false;

    if (isset($added['service_code'])) {
        if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[shipping] WHERE code = '".addslashes($code)."' AND service_code = '".addslashes($added['service_code'])."'") > 0)
            return false;
    }

    $max_subcode = func_query_first_cell("SELECT MAX(subcode+0) FROM $sql_tbl[shipping]") + 1;
    $data = array(
        'shipping'    => addslashes($method),
        'subcode'    => $max_subcode,
        'active'    => 'N',
        'is_new'    => 'Y',
        'code'        => $code
    );

    if (!empty($added) && is_array($added))
        $data = func_array_merge($data, $added);

    $id = func_array2insert('shipping', $data);
    if (empty($id))
        return false;

    return $id;
}

function func_weight_shipping_products ($products, $ignore_freight = false, $all_products_free_shipping = false, $get_max_weight = false, $group_by_provider = false)
{
    static $result = array();

    $md5_args = md5(serialize(array($products, $ignore_freight, $all_products_free_shipping, $get_max_weight)));

    if (isset($result[$md5_args])) {
        return $group_by_provider
            ? $result[$md5_args][1]
            : $result[$md5_args][0];
    }

    $weight = 0;

    $weight_array = array();
    foreach ($products as $product) {
        if (!isset($weight_array[$product['provider']]))
            $weight_array[$product['provider']] = 0;

        if (@$product['deleted']) continue; // for Advanced_Order_Management module

        if (!func_check_product_shipable($product, $ignore_freight, $all_products_free_shipping))
            continue;

        if ($get_max_weight) {
            $weight_array[$product['provider']] = $weight = max($product['weight'], $weight); // Calculate max item weight
        } else {
            $weight += $product['weight'] * $product['amount']; // Calculate total items weight
            $weight_array[$product['provider']]    += $product['weight'] * $product['amount'];
        }
    }

    $result[$md5_args] = array($weight, $weight_array);

    return $group_by_provider ? $weight_array : $weight;
}

/**
 * Sort shipping list by the 'orderby' field
 */
function usort_array_cmp_orderby($a, $b)
{
    return $a['orderby'] - $b['orderby'];
}

function func_shipper_show_rates($rates_list)
{
    global $config, $sql_tbl;

    echo "<h1>Shipping Rates</h1>";

    if (empty($rates_list)) {
        echo "No rates";
        return;
    }

    $l_search = array("##SM##","##R##");
    $l_replace = array("<sup>SM</sup>","&#174;");

    foreach ($rates_list as $rate) {
        $shipping_method = func_query_first_cell("SELECT shipping FROM $sql_tbl[shipping] WHERE subcode='$rate[methodid]'");

        $_currency_symbol = (
                !empty($rate['currency'])
                && !preg_match('/^UPS.*/Ss', $shipping_method)
            )
            ? $rate['currency']
            : $config['General']['currency_symbol'];

        echo "<p>" . str_replace($l_search, $l_replace, $shipping_method) . " ($_currency_symbol " . price_format($rate['rate']) . ")" . "</p>";
    }
}

/**
 * Intersect rates arrays
 */
function func_intersect_rates($rates)
{

    $result_rates = array();

    // Generate keys hash of each rates list
    $hash_keys = array();
    foreach ($rates as $provider=>$rates_data)
        foreach ($rates_data as $k=>$v)
            $hash_keys[$provider][$k] = $v['methodid'];

    if (count($hash_keys) < count($rates))
        return array();

    // Get intersection of keys hash
    $hash_keys_intersect = array();
    $flag = true;
    foreach ($hash_keys as $v) {
        if ($flag) {
            $hash_keys_intersect = $v;
            $flag = false;
            continue;
        }
        $hash_keys_intersect = array_intersect($hash_keys_intersect, $v);
    }

    // Fill result rates array from intersected rates of $rates array
    if (!empty($hash_keys_intersect)) {
        foreach ($hash_keys_intersect as $methodid) {
            $rate_data = array();
            foreach ($hash_keys as $provider=>$hash_data) {
                $key = array_search($methodid, $hash_data);

                if (empty($rate_data)) {

                    $rate_data = $rates[$provider][$key];

                } else {

                    $rate_data['rate'] += $rates[$provider][$key]['rate'];
                    $rate_data['orig_rate'] = $rate_data['rate'];
                    $rate_data['shipping_time'] = max($rate_data['shipping_time'], $rates[$provider][$key]['shipping_time']);

                    if (!empty($rates[$provider][$key]['warning'])) {

                        if (!is_array($rate_data['warning'])) {
                            $rate_data['warning'] = empty($rate_data['warning'])
                                ? array()
                                : array($rate_data['warning']);
                        }

                        $rate_data['warning'][] = $rates[$provider][$key]['warning'];

                    }

                }

            }

            if (!empty($rate_data['warning']) && is_array($rate_data['warning'])) {
                $rate_data['warning'] = array_unique($rate_data['warning']);
                $rate_data['warning'] = implode("\n", $rate_data['warning']);
            }

            $result_rates[] = $rate_data;
        }

    }

    return $result_rates;
}

// This function changes shippings names for Google Checkout
/**
 * First circulation:
 *  Find all duplicate shippings names, check its destinations and fill prefix types array.
 *  prifix types: S = string, N = number
 */
// Second circulation:
//  If destinations are differents then add string prefix else number.

// Next circulations:
//  Find all duplicate shippings names and add number prefixes.
/**
 * Example:
 *  Theare are 4 shippings with same names 'Shipping'. Three shippings have national destinations
 *  but one shipping has international destination.
 *  Shipping names format will be:
 */
//    Shipping (National) (1)
//    Shipping (National) (2)
//    Shipping (National) (3)
//    Shipping (Intl)

function func_rename_duplicate_shippings($shipping, $iteration = 0, $prefix_types = array())
{

    $shipping_names = array();
    $prefixes = array();
    $is_duplicate = false;

    foreach ($shipping as $k => $v) {

        $shipping[$k]['shipping'] = str_replace("\"", "'", $v['shipping']);

        $shipping_name = addslashes($shipping[$k]['shipping']);

        if (!empty($shipping_names) && isset($shipping_names[$shipping_name])) {

            $is_duplicate = true;
            $first_key = $shipping_names[$shipping_name];

            $prefix_types[$shipping_name] = (
                    empty($iteration)
                    && $v['destination'] != $shipping[$first_key]['destination']
                )
                ? 'S'
                : 'N';

            if (!empty($iteration)) {

                if (
                    !empty($prefix_types)
                    && $prefix_types[$shipping_name] == 'S'
                )  {

                    $intl_prefix = " (" . func_get_langvar_by_name('lbl_intl', false, false, true) . ")";
                    $national_prefix = " (" . func_get_langvar_by_name('lbl_national', false, false, true) . ")";

                    if (empty($prefixes[$shipping_name])) {
                        $prefixes[$shipping_name] = true;
                        $shipping[$first_key]['shipping'] .= ($shipping[$first_key]['destination'] == "L") ? $national_prefix : $intl_prefix;
                    }

                    $shipping[$k]['shipping'] .= ($v['destination'] == "L") ? $national_prefix : $intl_prefix;

                } else {

                    if (empty($prefixes[$shipping_name])) {
                        $prefixes[$shipping_name] = 1;
                        $shipping[$first_key]['shipping'] .= " (".$prefixes[$shipping_name].")";
                    }

                    $prefixes[$shipping_name]++;
                    $shipping[$k]['shipping'] .= " (".$prefixes[$shipping_name].")";

                }

            }

        } else {

            $shipping_names[$shipping_name] = $k;

        }

    }

    if (!empty($is_duplicate)) {

        $shipping = func_rename_duplicate_shippings($shipping, ++$iteration, $prefix_types);

    }

    return $shipping;
}

?>
