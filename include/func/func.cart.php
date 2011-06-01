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
 * This script contains common functions for cart operating
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Cart
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (C) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.cart.php,v 1.126.2.43 2011/04/14 08:45:16 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load(
    'files',
    'user',
    'taxes'
);

/**
 * Get the customer's zone
 */
function func_get_customer_zone_ship ($user, $provider, $type)
{
    global $sql_tbl;
    global $single_mode;

    $zones = func_get_customer_zones_avail($user, $provider, 'S');
    $zone = 0; // default zone

    if (is_array($zones)) {

        $provider_condition = ($single_mode) ? '' : " AND provider='$provider'";

        $tmp = func_query_column("SELECT zoneid FROM $sql_tbl[shipping_rates] WHERE zoneid IN ('" . implode("','", array_keys($zones)) . "') $provider_condition AND type='$type' GROUP BY zoneid");

        if (is_array($tmp) && !empty($tmp)) {

            $unused = $zones;
            // remove not available zones
            foreach($tmp as $v) {
                if (isset($unused[intval($v)]))
                    unset($unused[intval($v)]);
            }

            if (!empty($unused)) {
                foreach($unused as $k => $v)
                    unset($zones[$k]);
            }

            reset($zones);
            $zone = key($zones); #extract first zone

        }

    }

    return $zone;
}

/**
 * Get the customer's zones
 */
function func_get_customer_zones_avail ($user = 0, $provider = 0, $address_type = 'S')
{
    global $sql_tbl, $config, $single_mode;

    static $z_flags = array (
        'C' => 0x01,
        'S' => 0x02,
        'G' => 0x04,
        'T' => 0x08,
        'Z' => 0x10,
        'A' => 0x20,
    );

    static $zone_element_types = array (
        'S' => 'state',
        'G' => 'county',
        'T' => 'city',
        'Z' => 'zipcode',
        'A' => 'address',
    );

    static $results_cache = array();

    if ($config['General']['use_counties'] != 'Y') {

        unset($z_flags['G']);
        unset($zone_element_types['G']);

    }

    // Define which address type should be compared
    $address_prefix = 'B' === $address_type ? 'b_' : 's_';

    $zones = array();

    $_anonymous_userinfo = func_get_anonymous_userinfo();
    if (is_array($user)) {

        $customer_info = $user;

    } elseif (
        !empty($_anonymous_userinfo)
        || !empty($user)
    ) {

        $customer_info = func_userinfo($user, 'C');

    } elseif ($config['General']['apply_default_country'] == 'Y') {

        // Set the default user address
        $customer_info[$address_prefix . 'country'] = $config['General']['default_country'];
        $customer_info[$address_prefix . 'state']   = $config['General']['default_state'];
        $customer_info[$address_prefix . 'county']  = func_default_county($config['General']['default_state'], $config['General']['default_country']);
        $customer_info[$address_prefix . 'zipcode'] = $config['General']['default_zipcode'];
        $customer_info[$address_prefix . 'city']    = $config['General']['default_city'];

    }

    $customer_id = '';

    if (!empty($customer_info)) {

        if (!empty($customer_info['id'])) {

            $customer_id = $customer_info['id'];

        }

        $provider_condition = ($single_mode ? '' : "AND provider='$provider'");

        // Check local zones cache
        settype($customer_info[$address_prefix . 'county'], 'string');
        $data_key = md5(
            $customer_id
            . $provider_condition
            . $customer_info[$address_prefix . 'country']
            . $customer_info[$address_prefix . 'state']
            . $customer_info[$address_prefix . 'county']
            . $customer_info[$address_prefix . 'zipcode']
            . $customer_info[$address_prefix . 'city']
        );

        if (isset($results_cache[$data_key])) {

            return $results_cache[$data_key];

        }

        // Generate the zones list

        // Possible zones for customer's country...
        $possible_zones = func_query_column("SELECT $sql_tbl[zone_element].zoneid FROM $sql_tbl[zone_element], $sql_tbl[zones] WHERE $sql_tbl[zone_element].zoneid=$sql_tbl[zones].zoneid AND $sql_tbl[zone_element].field='".$customer_info[$address_prefix."country"]."'  AND $sql_tbl[zone_element].field_type='C' $provider_condition GROUP BY $sql_tbl[zone_element].zoneid");

        if (is_array($possible_zones) && !empty($possible_zones)) {
            if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[zone_element] WHERE field='%'"))
                $empty_condition = " AND $sql_tbl[zone_element].field<>'%'";
            else
                $empty_condition = '';

            // Zones with 2 or more countries in one zone (for speed up optimization)
            $two_countries_zones = func_query_column("SELECT zoneid, COUNT(*) as c FROM $sql_tbl[zone_element] WHERE field_type='C' AND zoneid IN (" . implode(",", $possible_zones) . ") GROUP BY zoneid,field_type HAVING c>1");

            $zones_completion = array();

            $_possible_zones = func_query_hash("SELECT zoneid,field_type FROM $sql_tbl[zone_element] WHERE zoneid IN (" . implode(",", $possible_zones) . ") $empty_condition GROUP BY zoneid, field_type", 'zoneid', true, true);

            $cs_state     = $customer_info[$address_prefix.'state'];
            $cs_county     = $customer_info[$address_prefix.'county'];
            $cs_country = $customer_info[$address_prefix.'country'];
            $cs_pair     = $cs_country . '_' . $cs_state;

            // Do not use S condtion if the country has disabled states

            $has_states = func_is_display_states($cs_country);

            if (!$has_states) {
                unset($z_flags['S']);
                unset($zone_element_types['S']);
            }

            foreach ($_possible_zones as $_pzoneid => $_elements) {

                $zones_completion[$_pzoneid] = 0;

                if (is_array($_elements)) {

                    foreach ($_elements as $k => $v) {

                        // Admin can combine a country which has states and a country which does not have states, in one destination zone. We should match both the countries(The same is for counties)

                        if (
                            !empty($cs_country)
                            && (
                                $v == 'S'
                                || $v == 'G'
                            )
                            && in_array($_pzoneid, $two_countries_zones)
                        ) {
                            #Try to find states zone condition for customer's country, skip if no

                            if (!func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[zone_element] WHERE $sql_tbl[zone_element].zoneid='$_pzoneid' AND $sql_tbl[zone_element].field LIKE '" . addslashes($cs_country) . "\_%' AND $sql_tbl[zone_element].field_type='S'"))
                                continue;

                        }

                        if (
                            !empty($cs_country)
                            && !empty($cs_state)
                            && $v == 'G'
                            && $config['General']['use_counties'] == 'Y'
                        ) {
                            #Try to find county zone condition for the customer's state, skip if no

                            if (!func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[zone_element],$sql_tbl[states],$sql_tbl[counties] WHERE $sql_tbl[zone_element].zoneid='$_pzoneid' AND $sql_tbl[zone_element].field_type='G' AND $sql_tbl[counties].stateid=$sql_tbl[states].stateid AND $sql_tbl[states].country_code='" . addslashes($cs_country) . "' AND $sql_tbl[states].code='" . addslashes($cs_state) . "' AND $sql_tbl[zone_element].field=$sql_tbl[counties].countyid"))
                                continue;

                        }

                        $zones_completion[$_pzoneid] += @$z_flags[$v];

                    }

                }

            }

            foreach ($possible_zones as $pzoneid) {

                $zones[$pzoneid] = $z_flags['C'];

                $state_required = false;

                // If only country is defined for this zone, skip further actions
                if ($zones_completion[$pzoneid] == $z_flags['C'])
                    continue;

                // State is required if its found in zone definition and customer provided state
                if (
                    (
                        $zones_completion[$pzoneid] & $z_flags['S']
                    )
                    && !empty($cs_state)
                ) {
                    $state_required = true;
                }

                $state_found = false;

                foreach ($z_flags as $field_type => $field_type_flag) {

                    if ($field_type == 'C')
                        continue;

                    if ($zones_completion[$pzoneid] & $field_type_flag) {
                        // Checking the field for  equal...

                        $found_zones = array();

                        if ($field_type == 'S') {
                            // Checking the state...

                            $found_zones = func_query_first_cell("SELECT zoneid FROM $sql_tbl[zone_element], $sql_tbl[states] WHERE $sql_tbl[zone_element].field='" . addslashes($cs_pair) . "' AND $sql_tbl[zone_element].field_type='S' AND $sql_tbl[states].code='" . addslashes($cs_state) . "' AND $sql_tbl[states].country_code='" . addslashes($cs_country) . "' AND $sql_tbl[zone_element].zoneid='$pzoneid'");
                            $state_found = $found_zones;

                        } elseif ($field_type == 'G') {
                            // Checking the county...

                            $found_zones = func_query_first_cell("SELECT zoneid FROM $sql_tbl[zone_element] WHERE field_type='G' AND field='" . func_addslashes($customer_info[$address_prefix . "county"]) . "' AND zoneid='$pzoneid'");

                        } elseif (
                            !$state_required
                            || $state_required
                            && $state_found
                        ) {
                            // Checking the rest fields (city, zipcode, address)

                            $found_zones = func_query_first_cell("SELECT $sql_tbl[zone_element].zoneid FROM $sql_tbl[zone_element], $sql_tbl[zones] WHERE $sql_tbl[zone_element].zoneid=$sql_tbl[zones].zoneid AND $sql_tbl[zone_element].field_type='$field_type' AND '" . addslashes($customer_info[$address_prefix . $zone_element_types[$field_type]]) . "' LIKE $sql_tbl[zone_element].field  AND $sql_tbl[zone_element].zoneid='$pzoneid' $empty_condition $provider_condition");

                        }

                        if (!empty($found_zones)) {
                            // Field is found: increase the priority

                            $zones[$pzoneid] += $field_type_flag;

                        } else {
                            // Remove zone from available zones list

                            unset($zones[$pzoneid]);
                            continue;

                        }

                    }

                } // /foreach ($z_flags)

            } // /foreach ($possible_zones)

        }

    }

    $zones[0] = 0;
    arsort($zones, SORT_NUMERIC);

    if (!empty($customer_info)) {

        $results_cache[$data_key] = $zones;

    }

    return $zones;
}

function func_get_products_providers ($products) 
{
    if (empty($products) || !is_array($products))
        return array();

    $providers = array ();
    foreach ($products as $product)
        $providers[$product['provider']] = 1;

    return array_keys($providers);
}

/**
 * Will return array of products with preserved indexes
 */
function func_get_products_by_provider ($products, $provider) 
{
    global $single_mode;

    if (!is_array($products) || empty($products))
        return array();

    if ($single_mode) return $products;

    $result = array ();

    foreach ($products as $k => $product) {

        if ($product['provider'] == $provider) {

            $result[$k] = $product;

        }

    }

    return $result;
}

/**
 * This function do real shipping calcs
 */
function func_real_shipping($delivery, $provider = '') 
{
    global $intershipper_rates, $real_time_rates, $sql_tbl, $single_mode;

    $shipping_codes = func_query_first("SELECT code, subcode FROM $sql_tbl[shipping] WHERE shippingid='$delivery'");

    $rates = $single_mode ? $intershipper_rates : $real_time_rates[$provider];

    if (
        !empty($rates)
        && is_array($rates)
    ) {

        foreach($rates as $rate) {

            if ($rate['methodid'] == $shipping_codes['subcode'])
                return $rate['rate'];

        }

    }

    return '0.00';
}

/**
 * This function calculates costs of contents of shopping cart
 */
function func_calculate($cart, $products, $user, $login_type, $paymentid = NULL)
{
    global $config, $single_mode, $sql_tbl;
    global $xcart_dir, $active_modules;

    $return = array ();
    $return ['orders'] = array ();

    if (!empty($active_modules['Special_Offers'])) {

        x_session_register('customer_unused_offers');#nolint

        global $customer_unused_offers, $special_offers_max_cartid;

        $customer_unused_offers = false;

        $special_offers_max_cartid = !empty($cart['max_cartid']) ? $cart['max_cartid'] : 1;

    }

    if ($single_mode) {

        $result = func_calculate_single ($cart, $products, $user, $login_type);

        $return = $result;
        $return ['orders'][0] = $result;
        $return ['orders'][0]['provider'] = (!empty($products) ? $products[0]['provider'] : '');

        if (!empty($active_modules['Special_Offers'])) {

            include $xcart_dir . '/modules/Special_Offers/calculate_return.php';

        }

    } else {

        $products_providers = func_get_products_providers ($products);

        $key = 0;

        $return['products'] = array ();
        $return['total_cost'] = 0;

        // Define common fields which will be sump up
        // for each product/giftcert

        $sum_up_fields = array(
            'total_cost',
            'shipping_cost',
            'display_shipping_cost',
            'tax_cost',
            'discount',
            'coupon_discount',
            'subtotal',
            'display_subtotal',
            'discounted_subtotal',
            'display_discounted_subtotal',
        );

        foreach ($products_providers as $provider_for) {

            $_products = func_get_products_by_provider ($products, $provider_for);

            $result = func_calculate_single ($cart, $_products, $user, $login_type, $provider_for);

            // Sum up totals
            $return = func_array_sum_assoc($return, $result, $sum_up_fields);

            if (isset($result['coupon'])) {

                $return['coupon'] = $result['coupon'];

            }

            $return['products'] = func_array_merge($return['products'], $result['products']);

            if (empty($return['taxes'])) {

                $return['taxes'] = $result['taxes'];

            } elseif (is_array($result['taxes'])) {

                foreach ($result['taxes'] as $k => $v) {

                    if (in_array($k, array_keys($return['taxes']))) {

                        $return['taxes'][$k]['tax_cost'] += $v['tax_cost'];
                        $return['taxes'][$k]['tax_cost_no_shipping'] += $v['tax_cost_no_shipping'];

                    } else {

                        $return['taxes'][$k] = $v;

                    }

                }

            }

            $return ['orders'][$key] = $result;
            $return ['orders'][$key]['provider'] = $provider_for;

            if (!empty($active_modules['Special_Offers'])) {

                include $xcart_dir . '/modules/Special_Offers/calculate_return.php';

            }

            $key ++;

        }

        if (!empty($cart['giftcerts'])) {

            $_products = array ();

            $result = func_calculate_single ($cart, $_products, $user, $login_type);

            $return = func_array_sum_assoc($return, $result, $sum_up_fields);

            $return['orders'][$key]             = $result;
            $return['orders'][$key]['provider'] = ''; #$provider_for;

            $key ++;

        }

        $return ['is_multiorder'] = (count($products_providers) > 1) ? 'Y' : '';

    }

    $_payment_surcharge = 0;

    if (!is_null($paymentid)) {

        // Apply the payment method surcharge or discount

        $_payment_surcharge = func_payment_method_surcharge($return['total_cost'], $paymentid);

        if ($_payment_surcharge != 0) {

            $_payment_surcharge = price_format($_payment_surcharge);

            $return['total_cost']        += $_payment_surcharge;
            $return['payment_surcharge']  = $_payment_surcharge;
            $return['paymentid']          = $paymentid;

            if (!$single_mode) {
                // Distribute the payment method surcharge or discount among orders

                $_payment_surcharge_part = price_format($_payment_surcharge / count($return['orders']));

                for ($i = 0; $i < count($return['orders']) - 1; $i ++) {

                    $return['orders'][$i]['total_cost'] += $_payment_surcharge_part;
                    $return['orders'][$i]['payment_surcharge'] = $_payment_surcharge_part;

                }

                $_payment_surcharge_rest = price_format($_payment_surcharge - ($_payment_surcharge_part * (count($return['orders'])-1)));

                $return['orders'][count($return['orders']) - 1]['total_cost'] += $_payment_surcharge_rest;
                $return['orders'][count($return['orders']) - 1]['payment_surcharge'] = $_payment_surcharge_rest;

            }

        } else { // if ($_payment_surcharge != 0)

            $return['payment_surcharge'] = 0;

            if (!$single_mode) {

                for ($i = 0; $i < count($return['orders']); $i++) {

                    $return['orders'][$i]['payment_surcharge'] = 0;

                }

            }

        } // if ($_payment_surcharge != 0)

    } // if (!is_null($paymentid))

    // Calculate gift wrapping
    include $xcart_dir . '/modules/Gift_Registry/calculate_gift_wrap.php';

    $return['display_cart_products_tax_rates'] = 'N';
    $return['product_tax_name']                = '';

    if ($config['Taxes']['display_cart_products_tax_rates'] == 'Y') {

        $_taxes = array();

        foreach ($return['orders'] as $k => $v) {

            if (is_array($v['products'])) {

                foreach ($v['products'] as $i => $j) {

                    if (!is_array(@$j['taxes']))
                        continue;

                    foreach ($j['taxes'] as $_tn => $_tax) {

                        if ($_tax['tax_value'] == 0)
                            continue;

                        if (!isset($_taxes[$_tn]))
                            $_taxes[] = $_tax['tax_display_name'];

                    }

                }

            }

        }

        if (count($_taxes) > 0) {

            $return['display_cart_products_tax_rates'] = 'Y';

            if (count($_taxes) == 1) {

                $return['product_tax_name'] = $_taxes[0];
            }

        }

    } // if ($config['Taxes']['display_cart_products_tax_rates'] == 'Y')

    // Recalculating applied gift certificates

    $giftcert_cost = 0;

    $applied_giftcerts = array();

    if (!empty($cart['applied_giftcerts'])) {

        $gc_payed_sum = 0;

        $applied_giftcerts = array();

        foreach ($cart['applied_giftcerts'] as $k => $v) {

            if ($gc_payed_sum < $return['total_cost']) {

                $v['giftcert_cost'] = min(($return['total_cost'] - $gc_payed_sum), $v['giftcert_cost']);

                $gc_payed_sum += $v['giftcert_cost'];

                $applied_giftcerts[] = $v;

                continue;

            }

            func_array2update(
                'giftcerts',
                array(
                    'status' => 'A',
                ),
                'gcid=\'' . $v['giftcert_id'] . '\''
            );

        }

        $giftcert_cost = $gc_payed_sum;

    }

    $return['giftcert_discount'] = $return['total_cost'] >= $giftcert_cost
        ? $giftcert_cost
        : $giftcert_cost - $return['total_cost'];

    $return['total_cost'] = price_format($return['total_cost'] - $return['giftcert_discount']);

    $return['applied_giftcerts'] = $applied_giftcerts;

    if ($single_mode) {

        $return ['orders'][0]['total_cost'] = $return['total_cost'];

    } elseif (is_array($applied_giftcerts)) {

        // Apply GC to all orders in cart in single_mode Off

        foreach ($return['orders'] as $k => $order) {

            $giftcert_discount = $return['orders'][$k]['giftcert_discount'] = 0;

            foreach ($applied_giftcerts as $k1 => $applied_giftcert) {

                if ($applied_giftcert['giftcert_cost'] == 0)
                    continue;

                if ($applied_giftcert['giftcert_cost'] > $order['total_cost']) {

                    $applied_giftcert['giftcert_cost'] = $order['total_cost'];

                }

                $giftcert_discount += $applied_giftcert['giftcert_cost'];

                $order['total_cost'] -= $applied_giftcert['giftcert_cost'];

                $applied_giftcert['giftcert_cost'] = price_format($applied_giftcert['giftcert_cost']);

                $applied_giftcerts[$k1]['giftcert_cost'] -= $applied_giftcert['giftcert_cost'];

                $return['orders'][$k]['applied_giftcerts'][] = $applied_giftcert;
                $return['orders'][$k]['giftcert_discount'] = price_format($giftcert_discount);
            }

            $return['orders'][$k]['total_cost'] = price_format($return['orders'][$k]['total_cost'] - $return['orders'][$k]['giftcert_discount']);

        }

    }

    if (!empty($active_modules['Special_Offers'])) {

        include $xcart_dir . '/modules/Special_Offers/calculate_bp_bonus.php';

    }

    return $return;
}

/**
 * This function distributes the discount among the product prices and
 * decreases the subtotal
 */
function func_distribute_discount($field_name, $products, $discount, $discount_type, $avail_discount_total = 0, $taxes = array())
{
    global $config, $active_modules;

    $sum_discount = 0;
    $return = array();
    $_orig_discount = $taxed_discount = $discount;

    if (
        !empty($taxes)
        && $config['Taxes']['display_taxed_order_totals'] == 'Y'
        && $config['Taxes']['apply_discount_on_taxed_amount'] == 'Y'
    ) {
        if ($discount_type=="absolute") {

            if (
                defined('XAOM_WO_DISCOUNT_DATA')
                && is_array($taxes)
            ) {

                foreach ($taxes as $_tn => $_tv) {

                    $taxes[$_tn]['price_includes_tax'] = 'Y';

                }

            }

            $_taxes = func_tax_price($discount, 0, false, NULL, '', $taxes, false);
            $taxed_discount = $_taxes ['net_price'];

        } else {

            $_taxes = func_tax_price($discount, 0, false, NULL, '', $taxes, true);
            $taxed_discount = $_taxes ['taxed_price'];

        }

    }

    if (
        $discount_type == 'absolute'
        && $avail_discount_total > 0
    ) {
        // Distribute absolute discount among the products

        $index = 0;
        $_considered_sum_discount = 0;
        $_considered_sum_taxed_discount = 0;
        $_total_discounted_products = 0;

        foreach ($products as $k => $product) {

            if (@$product['deleted']) continue; // for Advanced_Order_Management module

            if (
                !empty($active_modules['Special_Offers'])
                && func_sp_is_discount_unavail($product)
            ) {
                continue;
            }

            $_total_discounted_products++;

        }

        foreach ($products as $k => $product) {

            if (@$product['deleted']) continue; // for Advanced_Order_Management module

            if (
                !empty($active_modules['Special_Offers'])
                && func_sp_is_discount_unavail($product)
            ) {
                continue;
            }

            $index++;

            if (
                $field_name == 'coupon_discount'
                || $product['discount_avail'] == 'Y'
            ) {
                $koefficient = $product['price'] / $avail_discount_total;

                if ($index < $_total_discounted_products) {

                    $products[$k][$field_name] = $products[$k]['taxed_' . $field_name] = price_format($taxed_discount * $koefficient * $product['amount']);

                    $_considered_sum_discount       += $products[$k][$field_name];
                    $_considered_sum_taxed_discount += $products[$k]['taxed_'.$field_name];

                } else {

                    $products[$k][$field_name]          = $taxed_discount - $_considered_sum_discount;
                    $products[$k]['taxed_'.$field_name] = $taxed_discount - $_considered_sum_taxed_discount;

                }

                $products[$k]['discounted_price'] = max($products[$k]['discounted_price'] - $products[$k][$field_name], 0.00);

            }

        }

    } elseif ($discount_type == 'percent') {
        // Distribute percent discount among the products

        foreach ($products as $k => $product) {

            if (@$product['deleted']) continue; // for Advanced_Order_Management module

            if (
                !empty($active_modules['Special_Offers'])
                && func_sp_is_discount_unavail($product)
            ) {
                continue;
            }

            if (
                $field_name == 'coupon_discount'
                || $product['discount_avail'] == 'Y'
            ) {

                $products[$k][$field_name] = price_format($product['discounted_price'] * $discount / 100);

                if ($taxed_discount != $discount) {

                    $_price = $product['display_price'] > 0
                        ? $product['display_price'] - $product['taxed_discount'] / $product['amount']
                        : $_price = $product['taxed_price'];

                    $products[$k]['taxed_' . $field_name] = price_format($_price * $_orig_discount / 100 * $product['amount']);

                } else {

                    $products[$k]['taxed_' . $field_name] = $products[$k][$field_name];

                }

                $products[$k]['discounted_price'] = max($product['discounted_price'] - $products[$k][$field_name], 0.00);

            }

        }

    }

    foreach($products as $product) {

        if (@$product['deleted']) continue; // for Advanced_Order_Management module

        if (
            !empty($active_modules['Special_Offers'])
            && func_sp_is_discount_unavail($product)
        ) {
            continue;
        }

        $sum_discount += $product['taxed_' . $field_name];
    }

    if (
        $discount_type == 'absolute'
        && $sum_discount > $discount
    ) {
        $sum_discount = $discount;
    }

    $return[$field_name . '_orig'] = $discount_type == 'percent'
        ? $sum_discount
        : $_orig_discount;

    $return['products']  = $products;
    $return[$field_name] = $sum_discount;

    return $return;
}

/**
 * Sort discounts in func_calculate_discounts in descent order
 */
function func_sort_max_discount($a, $b)
{
    return $b['max_discount'] - $a['max_discount'];
}

/**
 * This function calculates discounts on subtotal
 */
function func_calculate_discounts($membershipid, $products, $discount_coupon = '', $provider = '')
{
    global $sql_tbl, $config, $active_modules, $single_mode, $global_store, $shop_language;

    // Prepare provider condition for discounts gathering

    $provider_condition = ($single_mode ? '' : ($provider ? "AND provider='$provider'" : ""));

    // Search for subtotal to apply the global discounts

    $avail_discount_total = 0;
    $total = 0;
    $_taxes = array();

    foreach($products as $k => $product) {

        if (@$product['deleted']) continue; // for Advanced_Order_Management module

        if (
            !empty($active_modules['Special_Offers'])
            && func_sp_is_discount_unavail($product)
        ) {
            continue;
        }

        $products[$k]['discount']        = 0;
        $products[$k]['coupon_discount'] = 0;

        if (
            $products[$k]['product_type'] == 'C'
            && $product['price'] == 0
        ) {
            continue;
        }

        $item_price = $product['price'] * $product['amount'];

        $products[$k]['discounted_price'] = $item_price;

        if ($product['discount_avail'] == 'Y') {

            $avail_discount_total += $item_price;

        }

        $total += $item_price;

        if (
            $config['Taxes']['apply_discount_on_taxed_amount'] == 'Y'
            && is_array($product['taxes'])
        ) {
            $_taxes = func_array_merge($_taxes, $product['taxes']);
        }

    }

    $return = array(
        'discount'        => 0,
        'coupon_discount' => 0,
        'discount_coupon' => $discount_coupon,
        'products'        => $products,
    );

    if ($avail_discount_total > 0) {

        // Calculate global discount

        if (!empty($global_store['discounts'])) {

            $discount_info = array();
            $__discounts = $global_store['discounts'];

            foreach ($__discounts as $k => $v) {

                $__discounts[$k]['max_discount'] = $v['discount_type'] == 'absolute'
                    ? $v['discount']
                    : $avail_discount_total * $v['discount'] / 100;

            }

            usort($__discounts, 'func_sort_max_discount');

            foreach ($__discounts as $v) {

                if (
                    $v['__override']
                    || (
                        $v['minprice'] <= $avail_discount_total
                        && (
                            empty($v['memberships'])
                            || @in_array($membershipid, $v['memberships'])
                        ) && (
                            $single_mode
                            || $v['provider'] == $provider
                        )
                    )
                ) {
                    $discount_info = $v;
                    break;
                }

            }

            unset($__discounts);

        } else { // if (!empty($global_store['discounts']))

            $max_discount_str = '' .
"IF ($sql_tbl[discounts].discount_type='absolute', $sql_tbl[discounts].discount, ('$avail_discount_total' * $sql_tbl[discounts].discount / 100)) as max_discount ";

            $discount_info = func_query_first("SELECT $sql_tbl[discounts].*, $max_discount_str FROM $sql_tbl[discounts] LEFT JOIN $sql_tbl[discount_memberships] ON $sql_tbl[discounts].discountid = $sql_tbl[discount_memberships].discountid WHERE minprice<='$avail_discount_total' $provider_condition AND ($sql_tbl[discount_memberships].membershipid IS NULL OR $sql_tbl[discount_memberships].membershipid = '$membershipid') ORDER BY max_discount DESC");

        } // if (!empty($global_store['discounts']))

        if (
            !empty($discount_info)
            && $discount_info['discount_type'] == 'percent'
            && $discount_info['discount'] > 100
        ) {
            unset($discount_info);
        }

        if (!empty($discount_info)) {

            if (
                $discount_info['discount_type'] == 'absolute'
                && $discount_info['discount'] > $total
            ) {

                $discount_info['discount'] = 100;
                $discount_info['discount_type'] = 'percent';

            }

            $return['discount'] += price_format($discount_info['max_discount']);

            // Save info about discount
            $return['discount_info'] = $discount_info;

            // Distribute the discount among the products prices

            $updated = func_distribute_discount(
                'discount',
                $products,
                $discount_info['discount'],
                $discount_info['discount_type'],
                $avail_discount_total,
                $_taxes
            );

            /*
            $products= ?,$discount= ?,$discount_orig= ?,$coupon_discount_orig= ? are extracted from the array $updated
            */
            extract($updated);

            unset($updated);

            $return['products'] = $products;
            $return['discount'] = $discount;

            $total -= $discount;

            if (isset($discount_orig)) {

                $return['discount_orig'] = $discount_orig;

            }

        } // if (!empty($discount_info))

    } // if ($avail_discount_total > 0)

    // Apply discount coupon

    if (
        !empty($active_modules['Discount_Coupons'])
        && !empty($discount_coupon)
    ) {

        // Calculate discount value of the discount coupon

        $coupon_total = 0;
        $coupon_amount = 0;

        if (!empty($global_store['discount_coupons'])) {

            $discount_coupon_data = array();

            foreach ($global_store['discount_coupons'] as $v) {

                if (
                    $v['__override']
                    || (
                        $v['coupon'] == $discount_coupon
                        && (
                            $single_mode
                            || $v['provider'] == $provider
                        )
                    )
                ) {

                    $discount_coupon_data = $v;
                    break;

                }

            }

        } else {

            $discount_coupon_data = func_query_first("SELECT * FROM $sql_tbl[discount_coupons] WHERE coupon='" . addslashes($discount_coupon) . "' $provider_condition");

            if (
                !$single_mode
                && (
                    $discount_coupon_data['provider'] != $provider
                    || empty($products)
                )
            ) {
                $discount_coupon_data = '';
            }
        }

        $return['discount_coupon_data'] = $discount_coupon_data;

        $return['coupon_type'] = $discount_coupon_data['coupon_type'];

        if (
            !empty($discount_coupon_data)
            && (
                $discount_coupon_data['coupon_type'] == 'absolute'
                || $discount_coupon_data['coupon_type'] == 'percent'
            )
        ) {
            $coupon_discount = 0;

            if ($discount_coupon_data['productid'] > 0) {

                // Apply coupon to product

                foreach($products as $k => $product) {

                    if (@$product['deleted']) continue; // for Advanced_Order_Management module

                    if (
                        !empty($active_modules['Special_Offers'])
                        && func_sp_is_discount_unavail($product)
                    ) {
                        continue;
                    }

                    if ($product['productid'] != $discount_coupon_data['productid'])
                        continue;

                    $price = $product['discounted_price'];

                    if (
                        $discount_coupon_data['coupon_type'] == 'absolute'
                        && $discount_coupon_data['discount'] > $price
                    ) {

                        $discount_coupon_data['discount']    = 100;
                        $discount_coupon_data['coupon_type'] = 'percent';

                    }

                    $multiplier = (
                            $discount_coupon_data['coupon_type'] == 'absolute'
                            && $discount_coupon_data['apply_product_once'] == 'N'
                        )
                        ? $product['amount']
                        : 1;

                    $_coupon_discount = $_taxed_coupon_discount = $discount_coupon_data['discount'] * $multiplier;

                    if (
                        $config['Taxes']['apply_discount_on_taxed_amount'] == 'Y'
                        && !empty($product['taxes'])
                        && is_array($product['taxes'])
                    ) {
                        $_taxes = func_tax_price(
                            $_coupon_discount,
                            0,
                            false,
                            NULL,
                            '',
                            $product['taxes'],
                            $discount_coupon_data['coupon_type'] == 'percent'
                        );

                        $_taxed_coupon_discount = $_taxes['taxed_price'];
                        $_coupon_discount = $_taxes['net_price'];

                    }

                    if ($discount_coupon_data['coupon_type'] == 'absolute') {

                        $taxed_coupon_discount = $_taxed_coupon_discount;
                        $coupon_discount       = $_coupon_discount;

                    } else {

                        $taxed_coupon_discount = price_format($price * $_taxed_coupon_discount / 100 );
                        $coupon_discount       = price_format($price * $_coupon_discount / 100 );

                    }

                    $products[$k]['coupon_discount']  = $coupon_discount;
                    $products[$k]['discounted_price'] = max($price - $coupon_discount, 0.00);

                    $return['coupon_discount'] += $coupon_discount;

                    if ($discount_coupon_data["apply_product_once"] == "Y")
                        break;

                } // foreach($products as $k => $product)

            } elseif ($discount_coupon_data['categoryid'] > 0) {

                // Apply coupon to category (and subcategories)

                $category_ids = array($discount_coupon_data['categoryid']);

                if ($discount_coupon_data['recursive'] == 'Y') {

                    $category_ids = func_data_cache_get("get_categories_tree", array($discount_coupon_data['categoryid'], true, $shop_language, $membershipid));
                    if (is_array($category_ids))
                        $category_ids = array_keys($category_ids);
                    else
                        $category_ids = array($discount_coupon_data['categoryid']);

                }

                if ($discount_coupon_data['coupon_type'] == 'absolute') {

                    // Check if absolute discount does not exceeds total

                    $sum_discount = 0;
                    foreach ($products as $k => $product) {

                        if (@$product['deleted']) continue; // for Advanced_Order_Management module

                        if (
                            !empty($active_modules['Special_Offers'])
                            && func_sp_is_discount_unavail($product)
                        ) {
                            continue;
                        }

                        $product_categories = func_query("SELECT categoryid FROM $sql_tbl[products_categories] WHERE productid='$product[productid]'");
                        $is_valid_product = false;

                        foreach ($product_categories as $pc) {

                            if (in_array($pc['categoryid'], $category_ids)) {

                                $is_valid_product = true;
                                break;

                            }

                        }

                        if ($is_valid_product) {

                            $multiplier = (
                                $discount_coupon_data['coupon_type'] == 'absolute'
                                && $discount_coupon_data['apply_product_once'] == 'N'
                            )
                            ? $product['amount']
                            : 1;

                            $sum_discount += $discount_coupon_data['discount'] * $multiplier;

                        }

                    }

                    if (
                        $discount_coupon_data['coupon_type'] == 'absolute'
                        && $discount_coupon_data['apply_product_once'] == 'Y'
                        && $discount_coupon_data['apply_category_once'] == 'Y'
                    ) {
                        $sum_discount = $discount_coupon_data['discount'];
                    }

                    if ($sum_discount > $total) {
                        // Transform coupon discount to 100%

                        $discount_coupon_data['discount']    = 100;
                        $discount_coupon_data['coupon_type'] = 'percent';

                    }

                } // if ($discount_coupon_data['coupon_type'] == 'absolute')

                // Apply coupon to one category

                foreach ($products as $k => $product) {

                    if (@$product['deleted']) continue; // for Advanced_Order_Management module

                    if (
                        !empty($active_modules['Special_Offers'])
                        && func_sp_is_discount_unavail($product)
                    ) {
                        continue;
                    }

                    $product_categories = func_query("SELECT categoryid FROM $sql_tbl[products_categories] WHERE productid='$product[productid]'");
                    $is_valid_product = false;

                    foreach ($product_categories as $pc) {

                        if (in_array($pc['categoryid'], $category_ids)) {

                            $is_valid_product = true;
                            break;

                        }

                    }

                    if ($is_valid_product) {

                        $multiplier = (
                            $discount_coupon_data['coupon_type'] == 'absolute'
                            && $discount_coupon_data['apply_product_once'] == 'N'
                        )
                        ? $product['amount']
                        : 1;

                        $_coupon_discount = $_taxed_coupon_discount = $discount_coupon_data['discount'] * $multiplier;

                        if (
                            $config['Taxes']['apply_discount_on_taxed_amount'] == 'Y'
                            && !empty($product['taxes'])
                            && is_array($product['taxes'])
                        ) {
                            $_taxes = func_tax_price(
                                $_coupon_discount,
                                0,
                                false,
                                NULL,
                                '',
                                $product['taxes'],
                                $discount_coupon_data['coupon_type'] == 'percent'
                            );

                            $_taxed_coupon_discount = $_taxes['taxed_price'];
                            $_coupon_discount = $_taxes['net_price'];

                        }

                        $price = $product['discounted_price'];

                        if ($discount_coupon_data['coupon_type']=="absolute") {

                            $taxed_coupon_discount = $_taxed_coupon_discount;
                            $coupon_discount = $_coupon_discount;

                        } else {

                            $taxed_coupon_discount = price_format($price * $_taxed_coupon_discount / 100 );
                            $coupon_discount = price_format($price * $_coupon_discount / 100 );

                        }

                        $taxed_coupon_discount = price_format($taxed_coupon_discount);

                        $products[$k]['coupon_discount'] = $coupon_discount;
                        $products[$k]['discounted_price'] = max($price - $coupon_discount, 0.00);

                        $return['coupon_discount'] += $taxed_coupon_discount;

                        if (
                            $discount_coupon_data['coupon_type'] == 'absolute'
                            && $discount_coupon_data['apply_category_once'] == 'Y'
                        ) {
                            break;
                        }

                    }

                }

            } else {

                // Apply coupon to subtotal

                if (
                    $discount_coupon_data['coupon_type'] == 'absolute'
                    && $discount_coupon_data['discount'] > $total
                ) {

                    $discount_coupon_data['discount'] = 100;
                    $discount_coupon_data['coupon_type'] = 'percent';

                }

                if ($discount_coupon_data['coupon_type'] == 'absolute') {

                    $return['coupon_discount'] = $discount_coupon_data['discount'];

                } elseif ($discount_coupon_data['coupon_type'] == 'percent') {

                    $return['coupon_discount'] = $total * $discount_coupon_data['discount'] / 100;
                }

                $updated = func_distribute_discount(
                    'coupon_discount',
                    $products,
                    $discount_coupon_data['discount'],
                    $discount_coupon_data['coupon_type'],
                    $total,
                    $_taxes
                );

                // $products and $discount are extracted from the array $updated

                extract($updated);
                unset($updated);

                $return['coupon_discount'] = $coupon_discount;

            }

            // Save info about discount coupon
            $return['discount_coupon_info'] = $discount_coupon_data;

        }

        $return['coupon_discount_orig'] = isset($coupon_discount_orig)
            ? $coupon_discount_orig
            : $return['coupon_discount'];

        $return['products'] = $products;

    }

    return $return;
}

/**
 * This function calculates delivery cost
 */
function func_calculate_shippings($products, $shipping_id, $customer_info, $provider = '')
{
    global $sql_tbl, $config, $active_modules, $single_mode;

    $return = array(
        'shipping_cost' => 0,
    );

    // Initial definitions

    $total_types = array(
        'valid' => 0,
        'apply' => 0,
    );

    $apply_to = array(
        'DST' => 0,
        'ST' => 0,
    );

    $total_cost = $total_weight = $total_types;

    foreach ($total_cost as $k => $v) {

        $total_cost[$k] = $apply_to;

    }

    $total_ship_items = 0;
    $shipping_cost = 0;
    $shipping_freight = 0;

    $shipping_type = 'D';

    if (is_array($products)) {

        foreach($products as $k => $product) {

            if (@$product['deleted']) continue; // for Advanced_Order_Management module

            if (
                !empty($active_modules['Egoods'])
                && $product['distribution'] != ''
            ) {
                continue;
            }

            // Calculate total_cost and total_weight for selection condition

            if (
                $product['free_shipping'] != 'Y'
                || $config['Shipping']['free_shipping_weight_select'] == 'Y'
            ) {

                $total_cost['valid']['DST'] += $product['subtotal'];
                $total_cost['valid']['ST']  += price_format($product['price'] * $product['amount']);
                $total_weight['valid']      += $product['weight'] * $product['amount'];

            }

            if (
                !empty($active_modules['Special_Offers'])
                && isset($product['sp_use_certain_free_ship'])
                && $product['sp_use_certain_free_ship']
                && empty($product['free_shipping_ids'][$shipping_id])
            ) {
                $product['free_shipping'] = 'N';
            }

            if ($product['free_shipping'] == 'Y') continue;

            if (
                $product['shipping_freight'] <= 0
                || $config['Shipping']['replace_shipping_with_freight'] != 'Y'
            ) {

                $total_cost['apply']['DST'] += $product['subtotal'];
                $total_cost['apply']['ST']  += price_format($product['price'] * $product['amount']);
                $total_weight['apply']      += $product['weight'] * $product['amount'];

                if ($product['product_type'] != 'C') {
                    $total_ship_items += $product['amount'];
                }

            }

            $shipping_freight += $product['shipping_freight'] * $product['amount'];

        }

    }

    // Nothing to ship

    if (
        $total_ship_items <= 0
        && $shipping_freight <= 0
    ) {
        return $return;
    }

    $result = func_query_first("SELECT * FROM $sql_tbl[shipping] WHERE shippingid = '" . $shipping_id . "' AND code != ''");

    if (
        $config['Shipping']['realtime_shipping'] == 'Y'
        && $result
    ) {

        $shipping_cost = func_real_shipping($shipping_id, $provider);
        $shipping_type = 'R';

    }

    if (
        $shipping_type == 'D'
        || (
            $shipping_type == 'R'
            && $shipping_cost > 0
        )
    ) {
        $customer_zone = func_get_customer_zone_ship($customer_info, $provider, $shipping_type);

        $total_condition = "IF(apply_to = 'ST', '" . $total_cost["valid"]["ST"] . "', '" . $total_cost["valid"]["DST"] . "')";

        $provider_condition = ($single_mode ? '' : "AND provider = '" . $provider . "'");

        $shipping = func_query("SELECT * FROM $sql_tbl[shipping_rates] WHERE shippingid = '".$shipping_id."' ".$provider_condition." AND zoneid = '".$customer_zone."' AND mintotal <= ".$total_condition." AND maxtotal >= ".$total_condition." AND minweight <= '".$total_weight["valid"]."' AND maxweight >= '".$total_weight["valid"]."' AND type = '".$shipping_type."' ORDER BY maxtotal, maxweight");

        if ($shipping) {

            $apply_to = ($shipping[0]['apply_to'] == 'ST') ? 'ST' : 'DST';

            $shipping_cost +=
                $shipping[0]['rate'] +
                $total_weight['apply'] * $shipping[0]['weight_rate'] +
                $total_ship_items * $shipping[0]['item_rate'] +
                $total_cost['apply'][$apply_to] * $shipping[0]['rate_p'] / 100;

        }

    }

    $return['shipping_cost'] = $shipping_cost += $shipping_freight;

    return $return;
}

/**
 * This function calculates taxes
 */
function func_calculate_taxes(&$products, $customer_info, $shipping_cost)
{
    global $config, $active_modules;

    $taxes = array(
        'total'        => 0,
        'shipping'    => 0,
    );

    $_tmp_taxes = array();

    foreach ($products as $k => $product) {

        $__taxes = array();

        if (@$product['deleted']) continue; // for Advanced_Order_Management module

        if (
            !empty($active_modules['Special_Offers']) 
            && isset($product['is_free_product']) 
            && $product['is_free_product'] == 'Y'
        ) {
            // Skip tax calculation for offer free products bt:#88618
            continue; 
        }

        if ($product['free_tax'] != 'Y') {

            $products[$k]['taxes'] = $product_taxes = func_get_product_taxes($products[$k], @$customer_info['id'], true);

            if ($config['Taxes']['display_taxed_order_totals'] == 'Y') {

                $products[$k]['display_price'] = isset($product['taxed_price']) ? $product['taxed_price'] : $product['price'];

            }

            if (is_array($product_taxes)) {

                $formula_data = array(
                    'ST'  => $product['price'] * $product['amount'],
                    'DST' => $product['discounted_price'],
                    'SH'  => 0,
                );

                $tax_result = array();

                if (empty($shipping_cost)) {

                    $index = 1;
                    $tax_result[1] = 0;

                } else {

                    $index = 0;

                }

                while ($index++ < 2) {

                    foreach ($product_taxes as $tax_name => $v) {

                        if (!empty($v['skip'])) continue;

                        if (!isset($taxes['taxes'][$tax_name])) {

                            $taxes['taxes'][$tax_name] = $v;
                            $taxes['taxes'][$tax_name]['tax_cost'] = 0;

                        }

                        if ($index == 2) {

                            $formula_data['SH'] = $shipping_cost;

                            if (!empty($__taxes[$tax_name])) {

                                $formula_data['SH'] = 0;

                            } else {

                                $__taxes[$tax_name] = true;

                            }

                        }

                        if ($v['rate_type'] == "%") {

                            $assessment = func_calculate_assessment($v['formula'], $formula_data);
                            $tax_value = $assessment * $v['rate_value'] / 100;

                        } else {

                            $tax_value = $v['rate_value'] * $product['amount'];

                        }

                        settype($tax_result[$index], 'float');
                        $tax_result[$index] += $tax_value;

                        if (empty($formula_data['SH'])) {

                            settype($taxes['taxes'][$tax_name]['tax_cost_no_shipping'], 'float');
                            $_tmp_taxes[$tax_name]['tax_cost_no_shipping'] = $tax_value;
                            $taxes['taxes'][$tax_name]['tax_cost_no_shipping'] += $tax_value;

                        }

                        $formula_data[$tax_name] = $tax_value;
                        if ($index == 2) {
                            if ($product['free_shipping'] == 'Y') {
                                $taxes['taxes'][$tax_name]['tax_cost_shipping'] = 0;
                                $formula_data[$tax_name] = $taxes['taxes'][$tax_name]['tax_cost_no_shipping'];
                            } else {
                                $taxes['taxes'][$tax_name]['tax_cost_shipping'] = $tax_value - $_tmp_taxes[$tax_name]['tax_cost_no_shipping'];
                            }    

                        }

                    }

                }

            }

        }

    }

    if (
        isset($taxes['taxes'])
        && is_array($taxes['taxes'])
    ) {

        foreach ($taxes['taxes'] as $tax_name => $tax) {

            $taxes['taxes'][$tax_name]['tax_cost'] = price_format($tax['tax_cost_no_shipping'] + $tax['tax_cost_shipping']);

            $taxes['total'] += $taxes['taxes'][$tax_name]['tax_cost'];
            $taxes['shipping'] += $tax['tax_cost_shipping'];

        }

    }

    if ($shipping_cost == 0) {

        $taxes['shipping'] = 0;

    }

    return $taxes;
}

// Calculate total products price
// 1) calculate total sum,
// 2) a) total = total - discount
//    b) total = total - coupon_discount
// 3) calculate shipping
// 4) calculate tax
// 5) total_cost = total + shipping + tax
// 6) total_cost = total_cost + giftcerts_cost

function func_calculate_single($cart, $products, $user, $login_type, $provider_for = '')
{
    global $single_mode;
    global $active_modules, $config, $sql_tbl;
    global $xcart_dir;

    $config['Taxes']['apply_discount_on_taxed_amount'] = ($config['Taxes']['display_taxed_order_totals'] == 'Y') ? 'Y' : 'N';

    if ($products) {

        // Set the fields filter to avoid storing too much redundant data
        // in the session

        list($tmp_k, $tmp_v) = each($cart['products']);

        foreach(array_keys($tmp_v) as $k)
            $product_keys[] = $k;

        unset($tmp_k, $tmp_v);
        reset($cart['products']);

        $product_keys[] = 'cartid';
        $product_keys[] = 'product';
        $product_keys[] = 'productcode';
        $product_keys[] = 'price';
        $product_keys[] = 'display_price';
        $product_keys[] = 'display_discounted_price';
        $product_keys[] = 'display_subtotal';
        $product_keys[] = 'free_price';
        $product_keys[] = 'discount';
        $product_keys[] = 'coupon_discount';
        $product_keys[] = 'discounted_price';
        $product_keys[] = 'taxes';
        $product_keys[] = 'subtotal';
        $product_keys[] = 'product_type';
        $product_keys[] = 'extra_data'; // Additional data for storing in the DB
        $product_keys[] = 'provider';
        $product_keys[] = 'discount_avail';
        $product_keys[] = 'weight';
        $product_keys[] = 'free_shipping';
        $product_keys[] = 'shipping_freight';
        $product_keys[] = 'split_query';

        if (!empty($active_modules['Product_Options'])) {
            $product_keys[] = 'product_options';
            $product_keys[] = 'variantid';
            $product_keys[] = 'options_expired';
            $product_keys[] = 'options_surcharge';
        }

        if (!empty($active_modules['Google_Checkout']))
            $product_keys[] = 'valid_for_gcheckout';

        if (!empty($active_modules['Wishlist']))
            $product_keys[] = 'wishlistid';

        if (!empty($active_modules['Gift_Registry']))
            $product_keys[] = 'event_data';

        if (!empty($active_modules['Egoods']))
            $product_keys[] = 'distribution';

        if (!empty($active_modules['Advanced_Order_Management'])) {
            $product_keys[] = 'deleted';
            $product_keys[] = 'new';
            $product_keys[] = 'use_shipping_cost_alt';
            $product_keys[] = 'shipping_cost_alt';
        }

        if (!empty($active_modules['Product_Configurator'])) {
            $product_keys[] = 'hidden';
            $product_keys[] = 'pconf_price';
            $product_keys[] = 'pconf_display_price';
            $product_keys[] = 'pconf_data';
            $product_keys[] = 'slotid';
            $product_keys[] = 'price_modifier';
            $product_keys[] = 'pcitem_amount';
            $product_keys[] = 'price_show_sign';
        }

        if (!empty($active_modules['Subscriptions'])) {
            $product_keys[] = 'catalogprice';
            $product_keys[] = 'sub_plan';
            $product_keys[] = 'sub_days_remain';
            $product_keys[] = 'sub_onedayprice';
        }

        if (!empty($active_modules['Special_Offers'])) {
            $product_keys[] = 'free_amount';
            $product_keys[] = 'have_offers';
            $product_keys[] = 'special_price_used';
            $product_keys[] = 'free_shipping_used';
            $product_keys[] = 'saved_original_price';
            $product_keys[] = 'sp_use_certain_free_ship';
            $product_keys[] = 'free_shipping_ids';
            $product_keys[] = 'is_free_product';
        }

    } else {

        $products = array();

    }

    // Calculate totals for one provider only or for all ($single_mode=true)

    $provider_condition = ($single_mode ? '' : "and provider='$provider_for'");

    $shipping_id     = @$cart['shippingid'];
    $giftcerts       = @$cart['giftcerts'];
    $discount_coupon = @$cart['discount_coupon'];

    // Get the user information

    $customer_info = func_userinfo($user, $login_type);

    if (defined('XAOM')) {

        $customer_info = func_array_merge($customer_info, $cart['userinfo']);

    }

    if (!empty($active_modules['Special_Offers'])) {
        include $xcart_dir . '/modules/Special_Offers/calculate_prepare.php';
        include $xcart_dir . '/modules/Special_Offers/calculate.php';
    }

    if (!empty($products)) {

        // Apply discounts to the products

        $discounts_ret = func_calculate_discounts(@$customer_info['membershipid'], $products, $discount_coupon, $provider_for);

        /* 
         Extract returned variables to global variables set:
         $coupon_type= ?,$discount= ?,$coupon_discount= ?,$discount_coupon= ?,$products= ?,$discount_coupon_data= ?
         $coupon_discount_orig= ?, $discount_orig= ?
        */ 
        extract($discounts_ret);
        unset($discounts_ret);
    } else {
        $coupon_discount = 0;
    }

    // Initial definitions

    $subtotal = 0;
    $discounted_subtotal = 0;
    $shipping_cost = 0;
    $total_tax = 0;
    $giftcerts_cost = 0;

    // Update $products array: calculate discounted prices, subtotal and
    // discounted subtotal

    foreach($products as $k => $product) {

        if (@$product['deleted']) continue; // for Advanced_Order_Management module

        if (
            empty($product['discount'])
            && empty($product['coupon_discount'])
        ) {
            $product['discounted_price'] = $product['price'] * $product['amount'];
        }

        if ($product['product_type'] == 'C') {

            // Corrections for Product Configurator module
            $product['pconf_data']['base_price'] = $product['price'];

            $product['pconf_price'] = $product['price'];

            foreach ($products as $k1 => $v1) {

                if ($v1['hidden'] == $product['cartid']) {

                    $product['pconf_price'] += price_format($v1['price'] * $v1['pcitem_amount']);

                }

            }

            // Calculate options surcharge
            if (
                !empty($active_modules['Product_Options'])
                && $product['product_options']
            ) {

                $product['options_surcharge'] = 0;

                foreach($product['product_options'] as $o) {

                    $product['options_surcharge'] += ($o['modifier_type'] == '%'
                        ? ($product['pconf_price'] * $o['price_modifier'] / 100)
                        : $o['price_modifier']);

                }

                $product['price']       += $product['options_surcharge'];
                $product['pconf_price'] += $product['options_surcharge'];

                $product['pconf_data']['options_surcharge'] = $product['options_surcharge'];

            }

            $product['discounted_price']    = $product['price'] * $product['amount'];
            $product['pconf_display_price'] = $product['pconf_price'];
            $product['pconf_subtotal']      = $product['pconf_price'] * $product['amount'];

        }

        $product['subtotal']                 = price_format($product['discounted_price']);
        $product['display_price']            = price_format($product['price']);
        $product['display_discounted_price'] = $product['discounted_price'];
        $product['display_subtotal']         = $product['subtotal'] + @$product['pconf_subtotal'];

        if (
            $product['product_type'] == 'C'
            && $product['display_price'] < 0
        ) {
            $product['price_show_sign'] = true;
        }

        $products[$k] = $product;

        if (!empty($active_modules['Special_Offers'])) {

            include $xcart_dir . '/modules/Special_Offers/calculate_subtotal.php';

        } else {

            if ($config['Taxes']['display_taxed_order_totals'] != 'Y') {

                $subtotal += price_format($product['price']) * $product['amount'];
                $discounted_subtotal = $subtotal - $discount - $coupon_discount;

            } else {

                $subtotal += $product['price'] * $product['amount'];
                $discounted_subtotal += $product['subtotal'];

            }

        }

    }

    $total = $subtotal;
    $display_subtotal = $subtotal;
    $display_discounted_subtotal = $discounted_subtotal;

    // Enable shipping and taxes calculation if 'apply_default_country' is ticked.

    $calculate_enable_flag = true;

    if (empty($customer_info)) {

        // If user is not logged in or anonymous profile data is not filled in

        if ($config['General']['apply_default_country'] == 'Y') {

            $customer_info['s_country'] = $config['General']['default_country'];
            $customer_info['s_state']   = $config['General']['default_state'];
            $customer_info['s_zipcode'] = $config['General']['default_zipcode'];
            $customer_info['s_city']    = $config['General']['default_city'];

        } else {

            $calculate_enable_flag = false;

        }

    }

    if (
        (
            $config['Shipping']['enable_shipping'] == 'Y'
            && $calculate_enable_flag
        ) || (
            isset($cart['use_shipping_cost_alt'])
            && $cart['use_shipping_cost_alt'] == 'Y'
        )
    ) {

        // Calculate shipping cost

        if (
            isset($cart['use_shipping_cost_alt'])
            && $cart['use_shipping_cost_alt'] == 'Y'
        ) {

            $shipping_cost = $cart['shipping_cost_alt'];

        } else {

            $shippings_ret = func_calculate_shippings($products, $shipping_id, $customer_info, $provider_for);

            // Extract returned variables to global variables set:
            // $shipping_cost

            extract($shippings_ret);
            unset($shippings_ret);

        }

        if (
            !empty($coupon_type)
            && $coupon_type == 'free_ship'
        ) {

            // Apply discount coupon 'Free shipping'

            if (
                $single_mode
                || $provider_for == $discount_coupon_data['provider']
            ) {
                $coupon_discount = $shipping_cost;
                $shipping_cost = 0;
            }
        }
    }

    $display_shipping_cost = $shipping_cost;

    if (
        $calculate_enable_flag
        && !(
            @$customer_info['tax_exempt'] == 'Y'
            && (
                $config['Taxes']['enable_user_tax_exemption'] == 'Y'
                || defined('XAOM')
            )
        )
    ) {

        // Calculate taxes cost

        $taxes = func_calculate_taxes($products, $customer_info, $shipping_cost);

        $total_tax = $taxes['total'];

        if ($config['Taxes']['display_taxed_order_totals'] == 'Y') {

            $_display_discounted_subtotal_tax = 0;

            if (is_array($taxes['taxes'])) {

                // Calculate the additional tax value if 'display_including_tax'
                // option for tax is disabled (for $_display_discounted_subtotal)

                foreach ($taxes['taxes'] as $k => $v) {

                    if ($v['display_including_tax'] != 'Y') {

                        $_display_discounted_subtotal_tax += $v['tax_cost'];

                    }

                }

            }

            $display_shipping_cost = $shipping_cost + $taxes['shipping'];
            $_display_subtotal = 0;
            $_display_discounted_subtotal = 0;

            if (is_array($products)) {

                foreach ($products as $k => $v) {

                    if (@$v['deleted']) continue; // for Advanced_Order_Management module

                    $v['display_price'] = $products[$k]['display_price'] = price_format($products[$k]['display_price']);

                    if (
                        is_array($v['taxes'])
                        && !(
                            $v['product_type'] == 'C'
                            && doubleval($v['price']) == 0
                        )
                    ) {
                        // Correct $_display_subtotal if 'display_including_tax'
                        // option for the tax is disabled
                        if (empty($_display_discounted_subtotal_tax)) {

                            foreach ($v['taxes'] as $tn => $tv) {

                                if ($tv['display_including_tax'] == 'N') {

                                    $_display_subtotal += $tv['tax_value'];

                                }

                            }

                        }

                    }

                    if (
                        !empty($v['discount'])
                        || !empty($v['coupon_discount'])
                    ) {
                        $subscription_flag = empty($active_modules['Subscriptions']) || !$v['sub_plan'];

                        $_taxes = func_tax_price($v['price']*$v['amount'], $v['productid'], false, $v['discounted_price'], $customer_info, '', $subscription_flag, $v['amount']);

                        if ($v['discounted_price'] > 0) {

                            $products[$k]['display_discounted_price'] = price_format($_taxes['taxed_price']);

                        }

                    } else {

                        $products[$k]['display_discounted_price'] = $v['display_price'] * $v['amount'];

                    }

                    $products[$k]['display_subtotal'] = $products[$k]['display_discounted_price'];

                    $_display_discounted_subtotal += $products[$k]['display_subtotal'];

                    if ($v['product_type'] == 'C') {

                        // Corrections for Product Configurator module

                        $products[$k]['display_price']         = $_pconf_display_price = $v['price'];
                        $products[$k]['display_subtotal']     = $_pconf_display_price * $products[$k]['amount'];

                        if ($products[$k]['display_subtotal'] > 0) {

                            $_display_subtotal += $products[$k]['display_subtotal'];

                        }

                        $_pconf_taxes = array();

                        foreach ($products as $k1 => $v1) {

                            if (@$v1['deleted']) continue; // for Advanced_Order_Management module

                            if ($v1['hidden'] == $v['cartid']) {

                                $_pconf_display_price += price_format($v1['display_price']);

                                if (is_array($v1['taxes'])) {

                                    foreach ($v1['taxes'] as $_tax_name => $_tax) {

                                        if (!isset($_pconf_taxes[$_tax_name])) {

                                            $_pconf_taxes[$_tax_name] = $_tax;
                                            $_pconf_taxes[$_tax_name]['tax_value'] = 0;

                                        }

                                        $_pconf_taxes[$_tax_name]['tax_value'] += $_tax['tax_value'];

                                    }

                                }

                            }

                        }

                        $products[$k]['taxes'] = $_pconf_taxes;
                        $products[$k]['pconf_display_price'] = $_pconf_display_price;

                    } else {

                        $_display_subtotal += $v['display_price'] * $v['amount'];

                    }

                    if (
                        !empty($active_modules['Subscriptions'])
                        && empty($active_modules['Special_Offers'])
                        && $products[$k]['sub_plan']
                        && $config['Taxes']['display_taxed_order_totals'] == 'Y'
                    ) {
                        $subscription_markup = $products[$k]['sub_days_remain'] * $products[$k]['sub_onedayprice'];
                        $_display_subtotal += $subscription_markup;
                        $products[$k]['display_price'] += $subscription_markup;

                        if ($display_subtotal == $display_discounted_subtotal) {

                            $products[$k]['display_subtotal'] += $subscription_markup;

                        }

                    }

                }

                if (!empty($_display_discounted_subtotal_tax)) {

                    $_display_subtotal += $_display_discounted_subtotal_tax;

                }

                $display_discounted_subtotal = (empty($coupon_discount) && empty($discount))
                    ? $_display_subtotal
                    : $_display_discounted_subtotal;

                $display_subtotal = $_display_subtotal;
            }

        }

    } elseif (
        $calculate_enable_flag
        && is_array($products)
    ) {

        foreach ($products as $k => $v) {

            if (@$v['deleted']) continue; // for Advanced_Order_Management module

            $products[$k]['taxes'] = array();

        }

    }

    // Calculate Gift Certificates cost (purchased giftcerts)

    if (
        (
            $single_mode
            || !$provider_for
        )
        && $giftcerts
    ) {
        foreach($giftcerts as $giftcert) {

            if (@$giftcert['deleted']) continue; // for Advanced_Order_Management module

            $giftcerts_cost += $giftcert['amount'];

        }

    }

    $subtotal                         += $giftcerts_cost;
    $display_subtotal                 += $giftcerts_cost;
    $discounted_subtotal             += $giftcerts_cost;
    $display_discounted_subtotal     += $giftcerts_cost;

    if ($discount > $display_subtotal) {

        $discount = $display_subtotal - $display_discounted_subtotal;

    }

    if (
        $coupon_discount > $display_subtotal
        && $coupon_type != 'free_ship'
    ) {
        $coupon_discount = $display_subtotal - $display_discounted_subtotal;
    }

    $display_shipping_cost = price_format($display_shipping_cost);
    $display_discounted_subtotal = price_format($display_discounted_subtotal);

    // Calculate total

    if ($config['Taxes']['display_taxed_order_totals'] == 'Y') {

        if (
            $config['Taxes']['apply_discount_on_taxed_amount'] == 'Y'
            && $display_discounted_subtotal != $display_subtotal - $coupon_discount_orig - $discount_orig
        ) {

            $display_discounted_subtotal     = $display_subtotal - $coupon_discount_orig - $discount_orig;
            $coupon_discount                 = $coupon_discount_orig;
            $discount                         = $discount_orig;

        } else {

            if (
                $discount <= 0
                && !empty($coupon_type)
                && $coupon_type != 'free_ship'
            ) {

                $coupon_discount = $display_subtotal - ($display_discounted_subtotal + $discount);

            } else {

                $discount = $display_subtotal - ($display_discounted_subtotal + ($coupon_type != 'free_ship' ? $coupon_discount : 0));

            }

        }

        $total = $display_discounted_subtotal + $display_shipping_cost;

    } else {

        $total = $discounted_subtotal + $shipping_cost + $total_tax;

    }

    $_products = array();

    foreach($products as $index => $product) {

        foreach($product as $key => $value) {

            if (in_array($key, $product_keys)) {

                $_products[$index][$key] = $value;

            }

        }

    }

    $return = array(
        'total_cost'                  => price_format($total),
        'shipping_cost'               => price_format($shipping_cost),
        'taxes'                       => !empty($taxes['taxes']) ? $taxes['taxes'] : array(),
        'tax_cost'                    => price_format(!empty($taxes['total']) ? $taxes['total'] : 0),
        'discount'                    => price_format($discount),
        'discount_info'               => isset($discount_info) ? $discount_info : '',
        'coupon'                      => isset($discount_coupon) ? $discount_coupon : '',
        'coupon_discount'             => price_format($coupon_discount),
        'discount_coupon_info'        => isset($discount_coupon_info) ? $discount_coupon_info : '',
        'subtotal'                    => price_format($subtotal),
        'display_subtotal'            => price_format($display_subtotal),
        'discounted_subtotal'         => price_format($discounted_subtotal),
        'display_shipping_cost'       => price_format($display_shipping_cost),
        'display_discounted_subtotal' => price_format($display_discounted_subtotal),
        'products'                    => $_products,
    );

    if (!empty($active_modules['Special_Offers'])) {

        include $xcart_dir . '/modules/Special_Offers/calculate_result.php';

    }

    return $return;
}

/**
 * This function calculates the payment method surcharge
 */
function func_payment_method_surcharge($total, $paymentid)
{
    global $sql_tbl, $user_account;

    $payment_methods = check_payment_methods(@$user_account['membershipid']);
    if (
        !empty($payment_methods[$paymentid])
        && $payment_methods[$paymentid]['surcharge'] != 0
    ) {
        $surcharge = func_query_first_cell("SELECT IF (surcharge_type='$', surcharge, surcharge * $total / 100) as surcharge FROM $sql_tbl[payment_methods] WHERE paymentid='$paymentid' AND payment_script!='payment_giftcert.php'");
    } else {
        $surcharge = 0;
    }

    return $surcharge;
}

/**
 * Generate products array in $cart
 */
function func_products_in_cart($cart, $membershipid)
{
    $membershipid = !empty($membershipid)
        ? abs(intval($membershipid))
        : 0;

    return (empty($cart) || empty($cart['products']))
        ? array()
        : func_products_from_scratch($cart['products'], $membershipid, false);
}

/**
 * Generate products array from scratch
 */
function func_products_from_scratch($scratch_products, $membershipid, $persistent_products)
{
    global $active_modules, $sql_tbl, $config, $xcart_dir;
    global $logged_userid, $store_language;
    static $results_cache = array();

    x_load('image');
    $products = array();

    if (empty($scratch_products))
        return $products;

    settype($membershipid, 'int');
    settype($persistent_products, 'bool');
    $md5_args = md5(serialize(array($scratch_products, $membershipid, $persistent_products)));

    if (isset($results_cache[$md5_args])) {
        return $results_cache[$md5_args];
    }

    $pids = array();

    foreach ($scratch_products as $product_data) {

        $pids[] = $product_data['productid'];

    }

    $int_res = func_query_hash("SELECT * FROM $sql_tbl[products_lng] WHERE code = '$store_language' AND productid IN ('".implode("','", $pids)."')", "productid", false);

    unset($pids);

    $hash = array();

    foreach ($scratch_products as $product_data) {

        $productid  = $product_data['productid'];
        $cartid     = $product_data['cartid'];
        $amount     = $product_data['amount'];
        $variantid  = $product_data['variantid'];

        if (!is_numeric($amount))
            $amount = 0;

        if (!empty($active_modules['Product_Options'])) {

            if (is_array(($default_options = func_get_default_options($productid, $amount)))) {

                $options = array();

                foreach ($default_options as $key => $value) {

                    $options[$key] = isset($product_data['options'][$key])
                        ? $product_data['options'][$key]
                        : $value;

                }

                if ($options != $product_data['options']) {

                    $product_data['options_expired'] = true;

                }

            } else {

                $options = $product_data['options'];

            }

        }

        $product_options    = false;
        $variant            = array();
        $image_x            = 0;
        $image_y            = 0;

        if (
            !empty($active_modules['Product_Options'])
            && !empty($options)
            && is_array($options)
        ) {

            if (!func_check_product_options($productid, $options))
                continue;

            list($variant, $product_options) = func_get_product_options_data($productid, $options, $membershipid);

            if (
                empty($variantid)
                && isset($variant['variantid'])
            ) {
                $variantid = $variant['variantid'];
            }

            if (
                $variant['variantid'] != $variantid
                && !empty($variantid)
            ) {
                continue;
            }

            if (
                $config['General']['unlimited_products'] == 'N'
                && !$persistent_products
            ) {

                if (
                    isset($variant['avail'])
                    && $variant['avail'] < $amount
                ) {
                    $amount = $variant['avail'];
                }

            }

        }

        $avail_condition = '';

        if (
            $config['General']['unlimited_products'] == 'N'
            && !$persistent_products
            && empty($variant)
        ) {
            $avail_condition = "($sql_tbl[products].avail >= '".doubleval($amount)."' OR $sql_tbl[products].product_type = 'C') AND ";
        }

        $membershipid_string = ($membershipid == 0 || empty($active_modules['Wholesale_Trading']))
            ? "= '0'"
            : "IN ('$membershipid', '0')";

        if (defined('X_MYSQL5018_COMP_MODE')) {

            $products_array = func_query_first("SELECT $sql_tbl[products].*, MIN($sql_tbl[pricing].price) as price FROM $sql_tbl[pricing],$sql_tbl[products] WHERE $sql_tbl[products].productid=$sql_tbl[pricing].productid AND $sql_tbl[products].forsale != 'N' AND $sql_tbl[products].productid='$productid' AND $avail_condition ".(empty($active_modules['Wholesale_Trading']) ? "$sql_tbl[pricing].quantity = 1" : "$sql_tbl[pricing].quantity<='$amount'")." AND $sql_tbl[pricing].membershipid $membershipid_string AND $sql_tbl[pricing].variantid = '$variantid' GROUP BY $sql_tbl[products].productid ORDER BY $sql_tbl[pricing].quantity DESC");

            if (!empty($products_array)) {

                $tmp = func_query_first("SELECT image_path, image_x, image_y FROM $sql_tbl[images_T] WHERE id = '$productid'");

                $products_array['image_path'] = $tmp['image_path'];
                $products_array['image_x'] = $tmp['image_x'];
                $products_array['image_y'] = $tmp['image_y'];

                $tmp = func_query_first("SELECT image_path, image_x, image_y FROM $sql_tbl[images_P] WHERE id = '$productid'");

                $products_array['pimage_path'] = $tmp['image_path'];
                $products_array['pimage_x'] = $tmp['image_x'];
                $products_array['pimage_y'] = $tmp['image_y'];
            }

        } else {

            $products_array = func_query_first("SELECT $sql_tbl[products].*, MIN($sql_tbl[pricing].price) as price, $sql_tbl[images_T].image_path, $sql_tbl[images_T].image_x, $sql_tbl[images_T].image_y, IF($sql_tbl[images_P].id IS NULL, '', 'P') as is_pimage, $sql_tbl[images_P].image_path as pimage_path, $sql_tbl[images_P].image_x as pimage_x, $sql_tbl[images_P].image_y as pimage_y FROM $sql_tbl[pricing],$sql_tbl[products] LEFT JOIN $sql_tbl[images_T] ON $sql_tbl[images_T].id = $sql_tbl[products].productid LEFT JOIN $sql_tbl[images_P] ON $sql_tbl[images_P].id = $sql_tbl[products].productid WHERE $sql_tbl[products].productid=$sql_tbl[pricing].productid AND $sql_tbl[products].forsale != 'N' AND $sql_tbl[products].productid='$productid' AND $avail_condition ".(empty($active_modules['Wholesale_Trading']) ? "$sql_tbl[pricing].quantity = 1" : "$sql_tbl[pricing].quantity<='$amount'")." AND $sql_tbl[pricing].membershipid $membershipid_string AND $sql_tbl[pricing].variantid = '$variantid' GROUP BY $sql_tbl[products].productid ORDER BY $sql_tbl[pricing].quantity DESC");

        }

        if ($products_array) {

            $products_array = func_array_merge($product_data, $products_array);

            $hash_key = $productid;

            // If priduct's price is 0 then use customer-defined price

            $free_price = false;

            if (
                $products_array['price'] == 0
                && empty($products_array['slotid'])
                && $products_array['product_type'] != 'C'
            ) {

                $free_price = true;
                $products_array['taxed_price'] = $products_array['price'] = price_format($product_data['free_price'] ? $product_data['free_price'] : 0);

            }

            if (
                !empty($active_modules['Product_Options'])
                && $options
            ) {

                if (
                    !empty($variant)
                    && $products_array['product_type'] != 'C'
                ) {

                    unset($variant['price']);

                    if (is_null($variant['pimage_path'])) {

                        func_unset($variant, 'pimage_path', 'pimage_x', 'pimage_y');

                    } else {

                        $variant['is_pimage'] = 'W';

                    }

                    $products_array = func_array_merge($products_array, $variant);

                }

                $hash_key .= "|".$products_array['variantid'];

                if ($product_options === false) {

                    unset($product_options);

                } else {

                    $variant['price'] = $products_array['price'];

                    $products_array['options_surcharge'] = 0;

                    if ($product_options) {

                        foreach($product_options as $o) {

                            $products_array['options_surcharge'] += (
                                $o['modifier_type'] == '%'
                                    ? ($products_array['price'] * $o['price_modifier'] / 100)
                                    : $o['price_modifier']
                            );
                        }

                    }

                }

            }

            $hash[$hash_key] = !empty($hash[$hash_key])
                ? $hash[$hash_key]
                : 0;

            if (
                $config['General']['unlimited_products'] == 'N'
                && !$persistent_products
                && ($products_array['avail'] - $hash[$hash_key]) < $amount
                && $products_array['product_type'] != 'C'
            ) {
                continue;
            }

            // Get thumbnail's URL (uses only if images stored in FS)

            $image_path = null;
            $image_type = 'P';

            $products_array['is_thumbnail'] = !is_null($products_array['image_path']);

            if (!is_null($products_array['pimage_path'])) {

                $image_path = $products_array['pimage_path'];
                $image_type = $products_array['is_pimage'];

            } elseif ($products_array['is_thumbnail']) {

                $image_path = $products_array['image_path'];
                $image_type = 'T';

            }

            $products_array['pimage_url'] = func_get_image_url(
                $image_type == 'W'
                    ? $products_array['variantid']
                    : $products_array['productid'],
                $image_type,
                $image_path
            );

            $products_array['display_imageid'] = $products_array['is_pimage'] == 'W'
                ? $products_array['variantid']
                : $products_array['productid'];

            if (
                !empty($products_array['pimage_x'])
                && !empty($products_array['pimage_y'])
            ) {

                $image_x = $products_array['pimage_x'];
                $image_y = $products_array['pimage_y'];

            }

            if (
                !empty($products_array['image_x'])
                && !empty($products_array['image_y'])
                && empty($image_x)
                && empty($image_y)
            ) {

                $image_x = $products_array['image_x'];
                $image_y = $products_array['image_y'];

            }

            if (
                !empty($image_x)
                && !empty($image_y)
            ) {

                list(
                    $products_array['tmbn_x'],
                    $products_array['tmbn_y']
                ) = func_crop_dimensions(
                    $image_x,
                    $image_y,
                    $config['Appearance']['thumbnail_width'],
                    $config['Appearance']['thumbnail_height']
                );

            }

            if ($products_array['product_type'] != 'C') {

                $products_array['price'] += @$products_array['options_surcharge'];

                if ($products_array['price'] < 0) {

                    $products_array['price'] = 0;

                }

            }

            if (
                !empty($active_modules['Product_Configurator'])
                && !empty($products_array['slotid'])
            ) {
                /**
                 * Calculate the price modifier for the slot
                 */
                $price_modifier_data = func_query_first("SELECT markup_type, markup FROM " . $sql_tbl['pconf_slot_markups'] . " WHERE slotid='" . $products_array['slotid'] . "' AND membershipid IN ('" . $membershipid . "', '0') ORDER BY membershipid DESC");

                if (!empty($price_modifier_data)) {

                    if ($price_modifier_data['markup_type'] == "$") {

                        $products_array['price_modifier'] = $price_modifier_data['markup'];

                    } elseif ($price_modifier_data['markup_type'] == "%") {

                        $products_array['price_modifier'] = price_format($products_array['price'] * $price_modifier_data['markup'] / 100);

                    }

                    if ($products_array['price'] + $products_array['price_modifier'] < 0)
                        $products_array['price_modifier'] = $products_array['price'] * (-1);

                    $products_array['price'] += $products_array['price_modifier'];

                }

            }

            // Check if the product is purchased for the certain event
            if (
                !empty($active_modules['Gift_Registry'])
                && !empty($products_array['wishlistid'])
            ) {

                $eventid = func_giftreg_get_eventid($products_array['wishlistid']);

                if ($eventid > 0) {

                    $_event_data = func_giftreg_get_event_data($eventid);

                    if (!empty($_event_data)) {

                        func_unset($_event_data, 'html_content', 'sent_date', 'description', 'guestbook');

                        $_event_data['wishlistid'] = $products_array['wishlistid'];

                        $products_array['event_data'] = $_event_data;
                    }
                }
            }

            if ($products_array['product_type'] != 'C') {

                $skip_tax_calc = false;
                if (!empty($active_modules['Special_Offers'])) {
                    // For bonus points per product feature bt#82922

                    $products_array['bonus_params'] = func_query_first("SELECT * FROM $sql_tbl[offer_product_params] WHERE productid = '".$productid."'");
                    // Skip tax calculation for offer free products bt:#88618
                    $skip_tax_calc = isset($products_array['is_free_product']) && $products_array['is_free_product'] == 'Y';
                    $products_array['taxes'] = array();
                }



                if (!$skip_tax_calc) {
                    $products_array['taxes'] = func_get_product_taxes($products_array, $logged_userid);

                }

            }

            $products_array['total']           = price_format($amount * $products_array['price']);
            $products_array['product_options'] = $product_options;
            $products_array['options']         = $options;
            $products_array['amount']          = $amount;
            $products_array['cartid']          = $cartid;
            $products_array['product_orig']    = $products_array['product'];

            x_load('product');

            if (!func_get_allow_active_content($products_array['provider'])) {

                if (!empty($int_res[$productid])) {

                    foreach ($int_res[$productid] as $key => $value) {

                        $int_res[$productid][$key] = func_xss_free($value);

                    }

                } else {

                    $products_array['descr']     = func_xss_free($products_array['descr']);
                    $products_array['fulldescr'] = func_xss_free($products_array['fulldescr']);

                }

            }

            if (isset($int_res[$productid])) {

                $products_array['product']   = stripslashes($int_res[$productid]['product']);
                $products_array['descr']     = stripslashes($int_res[$productid]['descr']);
                $products_array['fulldescr'] = stripslashes($int_res[$productid]['fulldescr']);

            }

            if ($products_array['descr'] == strip_tags($products_array['descr'])) {

                $products_array['descr'] = str_replace("\n", "<br />", $products_array['descr']);

            }

            if ($products_array['fulldescr'] == strip_tags($products_array['fulldescr'])) {

                $products_array['fulldescr'] = str_replace("\n", "<br />", $products_array['fulldescr']);

            }

            $products[] = $products_array;

            $hash[$hash_key] += $amount;

        }

    }

    if (!empty($active_modules['Product_Configurator'])) {

        include $xcart_dir.'/modules/Product_Configurator/pconf_customer_sort_products.php';

    }

    $results_cache[$md5_args] = $products;
    return $products;
}

/**
 * This function generates the unique cartid number
 */
function func_generate_cartid()
{
    global $cart;

    if (empty($cart['max_cartid']))
        $cart['max_cartid'] = 0;

    $cart['max_cartid'] ++;

    return $cart['max_cartid'];
}

/**
 * Detect ESD product(s) in cart
 */
function func_esd_in_cart($cart, $whole_cart = false)
{
    global $active_modules;
    
    if (empty($active_modules['Egoods'])) 
        return false;

    if (!empty($cart['products'])) {

        $esd_count = 0;

        foreach($cart['products'] as $p) {

            if (!empty($p['distribution'])) {

                if (!$whole_cart)
                    return true;
                else
                    $esd_count ++;

            }

        }

        // The whole cart is esd
        if (
            $esd_count 
            && $esd_count == count($cart['products'])
        ) {
            return true;
        }
    }

    return false;
}

/**
 * Calculate total amount of all products in cart. Used for cart validation
 */
function func_get_cart_products_amount($products)
{

    $amount = 0;

    if (!empty($products) && is_array($products)) {

        foreach ($products as $product) {

            $amount += $product['amount'];

        }

    }

    return $amount;
}

/**
 * Validate cart contents
 */
function func_cart_is_valid($cart, $userinfo)
{
    // test: all total amount should not change

    $current_amount         = func_get_cart_products_amount($cart['products']);
    $validated_products     = func_products_in_cart($cart, @$userinfo['membershipid']);
    $validated_amount         = func_get_cart_products_amount($validated_products);

    $is_valid = ($current_amount == $validated_amount);

    return $is_valid;
}

/**
 * Get the payment methods list
 */
function check_payment_methods($membershipid)
{
    global $sql_tbl, $config, $cart, $shop_language, $active_modules;

    static $result = array();

    $has_esd = func_esd_in_cart($cart);

    $is_zero_total_cost = func_cart_is_zero_total_cost($cart);
    $skip_payments_test = func_is_ajax_request();
    $giftcerts = isset($cart['giftcerts']) ? $cart['giftcerts'] : '';
    settype($membershipid, 'int');

    $md5_args = md5(serialize(array(
        $membershipid,
        $skip_payments_test,
        $giftcerts,
        $has_esd,
        $is_zero_total_cost,
        $shop_language
    )));

    if (isset($result[$md5_args])) {
        return $result[$md5_args];
    }

    $condition = (
            !empty($giftcerts)
            && 'Y' !== $config['Gift_Certificates']['allow_use_gc_for_buying_gc']
        )
        ? ' AND pm.paymentid != \'14\''
        : '';

    $payment_methods = func_query(
        "SELECT pm.*, cc.module_name, cc.processor, cc.type, pm.payment_method as payment_method_orig,"
        . " IFNULL(l1.value, pm.payment_method) as payment_method, IFNULL(l2.value, pm.payment_details) as payment_details,"
        . " cc.has_preauth, cc.use_preauth, cc.background, cc.disable_ccinfo"
        . " FROM $sql_tbl[payment_methods] as pm"
        . " LEFT JOIN $sql_tbl[ccprocessors] as cc ON pm.paymentid = cc.paymentid"
        . " LEFT JOIN $sql_tbl[pmethod_memberships] ON $sql_tbl[pmethod_memberships].paymentid = pm.paymentid"
        . " LEFT JOIN $sql_tbl[languages_alt] as l1 ON l1.name = CONCAT('payment_method_', pm.paymentid) AND l1.code = '$shop_language'"
        . " LEFT JOIN $sql_tbl[languages_alt] as l2 ON l2.name = CONCAT('payment_details_', pm.paymentid) AND l2.code = '$shop_language'"
        . " WHERE pm.active='Y' AND ($sql_tbl[pmethod_memberships].membershipid ='$membershipid' OR $sql_tbl[pmethod_memberships].membershipid IS NULL)"
        . $condition . " ORDER BY pm.orderby"
    );

    if (!$skip_payments_test) {
        x_load('tests');
        $payment_methods = test_payment_methods($payment_methods, true);
    }        

    // Remove online payments and add offline method
    $is_online_removed = false;
    if (
        $has_esd
        && func_egoods_use_offline_payments(@$cart['products'])
    ) {
        list($is_online_removed, $payment_methods) = func_egoods_remove_online_payments($payment_methods);
    }        

    if (
        !$is_online_removed
        && $is_zero_total_cost
    ) {
        list($is_online_removed, $payment_methods) = func_cart_remove_online_payments($payment_methods);        
    }

    if (
        $is_online_removed
        && empty($payment_methods)
    ) {
        $_set_surcharge_zero = $is_zero_total_cost;
        $payment_methods = func_cart_add_offline_payment($condition, $_set_surcharge_zero);
    }    

    // Disable X-Cart CC form for direct post payment methods
    if (!empty($payment_methods)) 
    foreach ($payment_methods as $k=>$pm) {
        if (func_is_direct_post_payment_method($pm))
            $payment_methods[$k]['payment_template'] = '';
    }

    $result[$md5_args] = $payment_methods;
    return $payment_methods;
}

/**
 * Check if payment with $paymentid can be used
 */
function func_is_valid_payment_method($paymentid)
{
    global $user_account;

    settype($paymentid, 'int');
    $membershipid = isset($user_account['membershipid']) ? $user_account['membershipid'] : 0;

    /*
     * Do not validate paymentid for AJAX queris to avoid time delay
     */
    if (func_is_ajax_request())
        return true;

    if (empty($paymentid))        
        return false;

    $payment_methods = check_payment_methods($membershipid);

    if (empty($payment_methods))
        return false;

    foreach ($payment_methods as $payment) {
        if ($payment['paymentid'] == $paymentid)
            return true;
    }

    return false;
}

/**
 * This function perform actions to normalize cart content
 */
function func_cart_normalize(&$cart)
{
    global $active_modules, $xcart_dir;

    if (empty($cart['products']))
        return false;

    $hash = array();
    $cart_changed = false;

    foreach ($cart['products'] as $k => $p) {

        if (
            !empty($p['hidden'])
            || !empty($p['pconf_data'])
        ) {
            continue;
        }

        $po = !empty($p['options']) && is_array($p['options'])
            ? serialize($p['options'])
            : '';

        $key = $p['productid'] . $po . $p['free_price'];

        if (@$p['free_amount'] > 0) {
            // for X-SpecialOffers
            $key .= '-fa1';
        }

        if (isset($p['event_data']) && !empty($p['event_data'])) {
            // for X-GiftRegistry
            $key .= serialize($p['event_data']);
        }

        if (isset($hash[$key])) {
            // Unite several product items
            $cart_changed = true;

            if (!empty($active_modules['Egoods']) && !empty($p['distribution'])) {

                $cart['products'][$hash[$key]]['amount'] = 1;

            } else {

                $cart['products'][$hash[$key]]['amount'] += $p['amount'];

            }

            unset($cart['products'][$k]);

        } else {

            $hash[$key] = $k;

        }

    }

    if (!empty($active_modules['Product_Configurator']))
        require $xcart_dir.'/modules/Product_Configurator/pconf_customer_cart_normalization.php';

    return $cart_changed;
}

/**
 * This function is used to add product to the cart
 */
function func_add_to_cart(&$cart, $product_data)
{
    global $user_account;
    global $active_modules, $config, $top_message, $xcart_dir, $HTTP_REFERER, $xcart_catalogs, $sql_tbl;
    global $from;

    $return = array(
        'status'       => 1,
        'productindex' => 0,
        'quantity'     => 0,
        'changed'      => 0,
    );

    // Extracts to: $productid, $amount, $product_options, $price, $wishlistid, $is_free_product(from special_offer_module)
    extract($product_data);//$productid= ?, $price= ?

    $added_product = func_select_product(
        $productid,
        (!empty($user_account['membershipid']) ? $user_account['membershipid'] : 0)
        , false
        , true
    );

    $wishlistid      = (isset($wishlistid)) ? intval($wishlistid) : 0;
    $product_options = (isset($product_options)) ? $product_options : array();

    if ($added_product['forsale'] == 'B') {

        // Bundled product cannot be added to the cart directly (related to X-Configurater)

        $top_message['content'] = func_get_langvar_by_name('txt_pconf_product_is_bundled');
        $top_message['type']    = 'W';

        $return['status']       = 'bundled';
        $return['redirect_to']  = $HTTP_REFERER;

        return $return;
    }

    $amount = (!empty($active_modules['Egoods']) && !empty($added_product['distribution']))
        ? 1
        : abs(intval($amount));

    if (!empty($active_modules['Subscriptions'])) {

        $subscribed_product = func_query_first_cell("SELECT pay_period_type FROM $sql_tbl[subscriptions] WHERE productid='$productid'");

        if (!empty($subscribed_product))
            $amount = 1;
    }

    if (!empty($active_modules['Product_Options'])) {

        // Prepare the product options for added products

        if (!empty($product_options)) {

            // Check the received options
            if (!func_check_product_options($productid, $product_options)) {

                $return['status'] = 'options';

                $return['redirect_to'] = (!empty($active_modules['Product_Configurator']) && $added_product['product_type'] == 'C')
                    ? "pconf.php?productid=$productid&err=options"
                    : "product.php?productid=$productid&err=options";

                return $return;
            }

        } else {

            // Get default options
            $product_options = func_get_default_options($productid, $amount, @$user_account['membershipid']);

            if ($product_options === false) {

                $return['status']      = 'internal_error';
                $return['redirect_to'] = "error_message.php?access_denied&id=30";

                return $return;

            } elseif ($product_options === true) {

                $product_options = array();

            }

        }

        // Get the variantid of options
        $variantid = func_get_variantid($product_options, $productid);

        if (!empty($variantid)) {

            // Get the variant amount
            $added_product['avail'] = func_get_options_amount($product_options, $productid);

            if (!empty($cart['products']))  {
                foreach ($cart['products'] as $k => $v) {
                    if (
                        $v['productid'] == $productid
                        && $variantid == $v['variantid']
                    ) {
                        $added_product['avail'] -= $v['amount'];
                    }
                }
            }
        }

    }

    if (
        $config['General']['unlimited_products'] == 'N'
        && $added_product['product_type'] != 'C'
    ) {

        // Add to cart amount of items that is not much than in stock

        if ($amount > $added_product['avail'])
            $amount = $added_product['avail'];
    }

    if (
        $from == 'partner'
        && empty($amount)
    ) {
        $return['redirect_to'] = $xcart_catalogs['customer'] . "/product.php?productid=" . $productid;

        return $return;
    }

    if (
        $amount < $added_product['min_amount']
        && $variantid
    ) {
        $return['status']      = 'options';
        $return['redirect_to'] = "product.php?productid=$productid&err=options";

        return $return;
    }

    if ($productid && $amount) {

        if ($amount < $added_product['min_amount']) {
            $return['status']      = 'amount';
            $return['redirect_to'] =  "error_message.php?access_denied&id=31";

            return $return;
        }

        $found = false;

        if (
            !empty($cart)
            && @$cart['products']
            && $added_product['product_type'] != 'C'
        ) {
            foreach ($cart['products'] as $k => $v) {

                if (
                    $v['productid'] == $productid
                    && !$found
                    && $v['options'] == $product_options
                    && empty($v['hidden'])
                ) {

                    if (doubleval($v['free_price']) != $price) {
                        continue;
                    }

                    if ($v['wishlistid'] != $wishlistid) {
                        continue;
                    }

                    $found = true;

                    if (
                        $cart['products'][$k]['amount'] >= 1
                        && (
                            !empty($added_product['distribution'])
                            || !empty($subscribed_product)
                        )
                    ) {
                        $cart['products'][$k]['amount'] = 1;
                        $amount = 0;
                    }

                    $return['productindex'] = $v['cartid'];
                    $return['quantity']     = $cart["products"][$k]["amount"];
                    $return['changed']         = $amount;

                    $cart['products'][$k]['amount'] += $amount;
                }
            } // foreach ($cart['products'] as $k => $v)

        }

        if (!$found) {

            // Add product to the cart

            if (!empty($price)) {
                // price value is defined by customer if admin set it to '0.00'
                $free_price = abs(doubleval($price));
            }

            $cartid = func_generate_cartid();

            if (empty($cart['products'])) {

                $add_to_cart_time = XC_TIME;

            }

            $cart['products'][] = array(
                'cartid'         => $cartid,
                'productid'     => $productid, #nolint come from above extract($product_data);
                'amount'         => $amount,
                'options'         => $product_options,
                'free_price'     => @price_format(@$free_price),
                'distribution'     => $added_product['distribution'],
                'wishlistid'     => $wishlistid,
                'variantid'     => $variantid,
            );

            $return['productindex'] = $cartid;
            $return['changed']         = $amount;

            if (!empty($active_modules['Product_Configurator'])) {
                $mode = 'add';
                include $xcart_dir.'/modules/Product_Configurator/pconf_customer_cart.php';
            }

            if (
                !empty($active_modules['Special_Offers'])
                && isset($is_free_product)
            ) {

                $cart['products'][count($cart['products']) - 1]['is_free_product'] = $is_free_product;#nolint

            }
        }

    }
    
    //@adam - hacked here to add this variable
    $cart['dx66_cart_expiry'] = XC_TIME + 30;//30 seconds for testing

    return $return;
}

/**
 * This function is used to delete product from the cart
 */
function func_delete_from_cart(&$cart, $productindex)
{
    global $active_modules, $config, $xcart_dir, $sql_tbl;

    $mode = 'delete';

    if (!empty($active_modules['Product_Configurator'])) {

        include $xcart_dir.'/modules/Product_Configurator/pconf_customer_cart.php';

    }

    $productid     = 0;
    $quantity     = 0;

    foreach ($cart['products'] as $k => $v) {

        if ($v['cartid'] == $productindex) {

            $productid     = $v['productid'];
            $quantity     = $v['amount'];

            if (!empty($active_modules['Advanced_Statistics'])) {

                if (
                    file_exists($xcart_dir.'/modules/Advanced_Statistics/prod_del.php')
                    && is_readable($xcart_dir.'/modules/Advanced_Statistics/prod_del.php')
                ) {
                    include $xcart_dir.'/modules/Advanced_Statistics/prod_del.php';
                }

            }

            array_splice($cart['products'], $k, 1);

            break;
        }

    }

    if (!empty($active_modules['Special_Offers'])) {

        $cart['sp_deleted_products'][$productid] = true;

    }

    return array($productid, $quantity);
}

/**
 * This function updates the quantity of products in the cart
 */
function func_update_quantity_in_cart(&$cart, $productindexes)
{
    global $active_modules, $config, $xcart_dir, $sql_tbl;

    if (empty($cart['products']))
        return array(false, false);

    $productindexes_tmp = array();

    $changes = array();

    foreach ($cart['products'] as $p) {

        $changes[$p['cartid']] = array(
            'productid' => $p['productid'],
            'quantity'     => $p['amount'],
        );

    }

    $action = 'update';

    foreach ($productindexes as $_cartid => $new_quantity) {

        foreach ($cart['products'] as $k => $v) {

            if ($v['cartid'] == $_cartid) {
                $productindexes_tmp[$k] = $new_quantity;
                break;
            }

        }

    }

    $productindexes = $productindexes_tmp;

    unset($productindexes_tmp);

    if (!empty($active_modules['Product_Configurator'])) {

        include $xcart_dir.'/modules/Product_Configurator/pconf_customer_cart.php';

    }

    $min_amount_warns = array();

    foreach ($cart['products'] as $k => $v) {

        $tot = 0;
        $tot_amount = 0;
        $min_amount = func_query_first_cell("SELECT min_amount FROM $sql_tbl[products] WHERE productid = '$v[productid]'");

        foreach ($productindexes as $productindex => $new_quantity) {

            if (!is_numeric($new_quantity))
                continue;

            if (
                $cart['products'][$productindex]['productid'] == $v['productid']
                && $cart['products'][$productindex]['variantid'] == $v['variantid']
            ) {

                if (
                    $new_quantity < $min_amount
                    && $new_quantity > 0
                ) {

                    $productindexes[$productindex] = $new_quantity = ($v['amount'] > $min_amount)
                        ? $v['amount']
                        : $min_amount;

                    $min_amount_warns[$v['cartid']] = $min_amount;

                }

                $tot += floor($new_quantity);

            }

        }

        foreach ($cart['products'] as $k2 => $v2) {

            if (
                $v['productid'] == $v2['productid']
                && $v2['variantid'] == $v['variantid']
            ) {
                $tot_amount += $v2['amount'];
            }

        }

        $updates_array[$k] = array(
            'quantity'             => $v['amount'],
            'total_quantity'     => $tot,
            'total_amount'         => $tot_amount,
        );

    }

    // Create hash array with variants

    $hash = array();

    if (!empty($active_modules['Product_Options'])) {

        foreach ($productindexes as $productindex => $new_quantity) {

            if (!empty($cart['products'][$productindex]['options'])) {

                $variantid = $cart['products'][$productindex]['variantid'];

                if ($variantid) {

                    if (!isset($hash[$variantid])) {
                        $hash[$variantid]['avail'] = func_get_options_amount($cart['products'][$productindex]['options'], $cart['products'][$productindex]['productid']);
                        $hash[$variantid]['old'] = $hash[$variantid]['new'] = 0;
                    }

                    $hash[$variantid]['old']   += $cart['products'][$productindex]['amount'];
                    $hash[$variantid]['new']   += $new_quantity;
                    $hash[$variantid]['ids'][]  = $cart['products'][$productindex]['productid'];

                    $cart['products'][$productindex]['variantid'] = $variantid;

                }
            }
        }
    }

    // Update the quantities

    foreach ($productindexes as $productindex => $new_quantity) {

        if (!is_numeric($new_quantity) || empty($cart['products'][$productindex]))
            continue;

        $new_quantity   = floor($new_quantity);
        $productid      = $cart['products'][$productindex]['productid'];
        $total_quantity = $updates_array[$productindex]['total_quantity'];
        $total_amount   = $updates_array[$productindex]['total_amount'];

        if (
            $config['General']['unlimited_products'] == 'N'
            && $cart['products'][$productindex]['product_type'] != 'C'
        ) {

            if (!empty($cart['products'][$productindex]['variantid'])) {

                $amount_max     = $hash[$cart['products'][$productindex]['variantid']]['avail'];
                $total_quantity = $hash[$cart['products'][$productindex]['variantid']]['old'];

            } else {

                $amount_max = func_query_first_cell("SELECT avail FROM $sql_tbl[products] WHERE productid='$productid'");

            }

        } else {

            $amount_max = $total_quantity + 1;

        }

        $amount_min = func_query_first_cell("SELECT min_amount FROM $sql_tbl[products] WHERE productid='$productid'");

        // Do not change

        if ($config['General']['unlimited_products'] == 'Y') {
            $cart['products'][$productindex]['amount'] = $new_quantity;
            continue;
        }

        if (
            $new_quantity >= $amount_min
            && !empty($active_modules['Egoods'])
            && !empty($cart['products'][$productindex]['distribution'])
        ) {

            $cart['products'][$productindex]['amount'] = 1;

        } elseif (
            $new_quantity >= $amount_min
            && $new_quantity <= ($amount_max - $total_amount + $cart['products'][$productindex]['amount'])
        ) {
            $cart['products'][$productindex]['amount'] = $new_quantity;

            if(!empty($cart['products'][$productindex]['variantid'])) {

                $hash[$cart['products'][$productindex]['variantid']]['old'] += ($new_quantity - $cart['products'][$productindex]['amount']);

            } else {

                $updates_array[$productindex]['total_amount'] += ($new_quantity-$cart['products'][$productindex]['amount']);

            }

        } elseif ($new_quantity >= $amount_min) {

            $old_amount = $cart['products'][$productindex]['amount'];

            $cart['products'][$productindex]['amount'] = ($amount_max - $total_amount + $cart['products'][$productindex]['amount']);

            if (!empty($cart['products'][$productindex]['variantid'])) {

                $hash[$cart['products'][$productindex]['variantid']]['old'] += ($amount_max - $total_amount + $cart['products'][$productindex]['amount'] - $old_amount);

            } else {

                $updates_array[$productindex]['total_amount'] += ($amount_max - $total_amount + $cart['products'][$productindex]['amount'] - $old_amount);

            }

        } else {

            $cart['products'][$productindex]['amount'] = 0;

        }

        if ($cart['products'][$productindex]['amount'] < 0)
            $cart['products'][$productindex]['amount'] = 0;

        if (!empty($active_modules['Special_Offers'])) {

            $cart['sp_deleted_products'][$cart['products'][$productindex]['productid']] = true;

        }

    }

    if (!empty($active_modules['Product_Configurator'])) {
        $pconf_update = 'post_update';
        include $xcart_dir.'/modules/Product_Configurator/pconf_customer_cart.php';
    }

    $products = array();

    foreach ($cart['products'] as $product) {
        if ($product['amount'] > 0)
            $products[] = $product;
    }

    $cart['products'] = $products;

    foreach ($cart['products'] as $p) {
        if (!isset($changes[$p['cartid']]))
            continue;

        $changes[$p['cartid']]['change'] = $changes[$p['cartid']]['quantity'] - $p['amount'];
    }

    return array($min_amount_warns, $changes);
}

/**
 * This function generates the min order amount warning message
 */
function func_generate_min_amount_warning($min_amount_warns, $productindexes, $products)
{
    global $sql_tbl, $shop_language;

    if (empty($min_amount_warns) || empty($products)) return false;

    $top_message = array();
    $min_amount_ids = array();

    foreach ($products as $k => $v) {

        if (
            !isset($min_amount_warns[$v['cartid']])
            || !isset($productindexes[$v['cartid']])
            || isset($min_amount_ids[$v['productid']])
        ) {
            continue;
        }

        $product_name = func_query_first_cell("SELECT IF($sql_tbl[products_lng].product IS NULL OR $sql_tbl[products_lng].product = '', $sql_tbl[products].product, $sql_tbl[products_lng].product) as product FROM $sql_tbl[products] LEFT JOIN $sql_tbl[products_lng] ON $sql_tbl[products].productid = $sql_tbl[products_lng].productid AND $sql_tbl[products_lng].code = '$shop_language' WHERE $sql_tbl[products].productid = '$v[productid]'");

        $top_message['content'] .= (empty($top_message['content'])
            ? ''
            : "<br />\n")
                . func_get_langvar_by_name(
                    'lbl_cannot_buy_less_X',
                    array(
                        'quantity'     => $min_amount_warns[$v['cartid']],
                        'product'     => $product_name,
                    )
                );

        $min_amount_ids[$v['productid']] = true;
    }

    if (!empty($top_message['content']))
        $top_message['type'] = "W";

    return $top_message;
}

/**
 * This function counts the total quantity of products in the cart
 */
function func_cart_count_items(&$cart)
{
    if (empty($cart) || empty($cart['products'])) return 0;

    $count = 0;
    foreach ($cart['products'] as $product) {
        $count += $product['amount'];
    }

    return $count;
}

/**
 * This function saves the cart details to the xcart_customers table
 */
function func_save_customer_cart($user = false, $_cart = false)
{
    global $sql_tbl, $cart, $logged_userid;

    $user = ($user) ? intval($user) : $logged_userid;
    $cart = ($_cart) ? $_cart : $cart;

    if (!empty($user)) {
        db_query("UPDATE $sql_tbl[customers] SET cart='".addslashes(serialize($cart))."' WHERE id='$user'");
    }
}

/**
 * Return unique hash of cart array
 * 
 * @param array $cart cart array
 *  
 * @return string
 * @see    ____func_see____
 * @since  1.0.0
 */
function func_calculate_cart_hash($cart)
{
    unset($cart['split_query']);

    return md5(serialize($cart));
}

/**
 * Check if cart is changed during the split checkout and decline previous placed orders
 * 
 * @param array   $cart     cart structure
 * @param array   $orderids order IDs (new ones that will be connected with this split checkout data)
 * @param boolean $force    force flag, if TRUE then decline and change orders anyway
 *  
 * @return void
 * @see    ____func_see____
 * @since  1.0.0
 */
function func_split_checkout_check_decline_order(&$cart, $orderids, $force = false)
{
    if (
        $force
        || (
            !empty($cart['split_query'])
            && $cart['split_query']['cart_hash'] !== func_calculate_cart_hash($cart)
        )
    ) { 

        $prev_orderids = explode('|', $cart['split_query']['orderid']);

        unset($prev_orderids[0]);
        unset($prev_orderids[count($prev_orderids)]);

        func_decline_order($prev_orderids);

        $cart['split_query']['orderid'] = $orderids;

        func_store_split_checkout_data($cart['split_query']);

    }
}

/**
 * Calculate and return paid amount from transaction query
 * 
 * @param array $cart cart structure
 *  
 * @return integer
 * @see    ____func_see____
 * @since  1.0.0
 */
function func_calculate_paid_amount($cart)
{
    $paid_amount = 0;

    foreach ($cart['split_query']['transaction_query'] as $query) {
        foreach ($query as $elem) {
            $paid_amount += $elem['paid_amount'];
        }   
    }   

    return $paid_amount;

}

/**
 * Check if there are taxes with display_including_tax=Y setting
 * 
 * @param array $taxes from $cart variable
 *  
 * @return boolean
 * @see    ____func_see____
 * @since  1.0.0
 */
function func_is_display_including_tax($taxes)
{
    if (empty($taxes) || !is_array($taxes))
        return false;

    $found = false;
    foreach($taxes as $k => $tax) {
        if ($tax['display_including_tax'] == 'Y') {
            $found = true;
            break;
        }
    }

    return $found;
}

/**
 * Merge calculated product taxes into product taxes from scratch
 * 
 * @param array $products products generated by func_products_in_cart
 * @param array $cart_products products from $cart[products]
 *  
 * @return array
 * @see    ____func_see____
 * @since  1.0.0
 */
function func_merge_cart_products_taxes($products, $cart_products)
{
    $backup_products = $products;
    $error = false;
    foreach($products as $k => $product) {
        if (
            $product['productid'] == $cart_products[$k]['productid']
            && $product['cartid'] == $cart_products[$k]['cartid']
            && is_array($product['taxes'])
            && is_array($cart_products[$k]['taxes'])
        ) {
            $products[$k]['taxes'] = func_array_merge($product['taxes'], $cart_products[$k]['taxes']);
        } else {
            // Unknown error cannot be merged
            $error = true;
            break;
        }

    }

    if ($error)
        return $backup_products;
    else
        return $products;
}

/**
 * Check if a customer confirms an order on the paypal express checkout side
 */
function func_is_confirmed_paypal_express()
{
    global $paypal_begin_express, $paypal_token, $paypal_express_details;

    x_session_register('paypal_begin_express');
    x_session_register('paypal_token');
    x_session_register('paypal_express_details');

    if (
        (
            $paypal_begin_express !== false
            && empty($paypal_token)
            && empty($paypal_express_details)
        ) || (
            !empty($paypal_token)
            && (
                empty($paypal_express_details)
                || (
                    $paypal_token != $paypal_express_details['Token']
                    && $paypal_token != $paypal_express_details['token']
                )
            )
        )
    ) { 
        return false;
    }

    return true;
}

/*
 * Try to get paymentid from POST GET or $cart['paymentid']
 */
function func_cart_get_paymentid($cart, $checkout_module='')
{
    global $paymentid, $user_account;

    $return_paymentid = 0;
    
    // Firstly use global paymentid
    if (
        empty($return_paymentid)
        && !empty($paymentid)
    ) {
        $return_paymentid = $paymentid;
    }        

    // Secondly Use session paymentid from $cart['paymentid'] for One_Page_Checkout
    if (
        empty($return_paymentid)
        && !empty($cart['paymentid'])
        && $checkout_module === 'One_Page_Checkout'
    ) {
        $return_paymentid = $cart['paymentid'];
    }

    // Thirdly use first paymentid from payment_methods for One_Page_Checkout
    if (
        empty($return_paymentid) 
        && $checkout_module === 'One_Page_Checkout'
    ) {

        $payment_methods = check_payment_methods(@$user_account['membershipid']);

        if (
            !empty($payment_methods)
            && empty($cart['paymentid'])
        ) {
            $paypal_expressid = func_cart_get_paypal_express_id();

            foreach ($payment_methods as $payment) {
                if (
                    $payment['paymentid'] != $paypal_expressid
                    && $payment['is_cod'] != 'Y'
                ) {
                    $return_paymentid = $payment['paymentid'];
                    break;
                }
            }

            if (empty($return_paymentid)) {
                $return_paymentid = $payment_methods[0]['paymentid'];
            }
        }
    } 

    // Check the paymentid
    if (func_is_valid_payment_method($return_paymentid))
        return $return_paymentid;
    else        
        return 0;
}

/*
 * Set paymentid in cart session var
 */
function func_cart_set_paymentid($cart, $paymentid)
{
    $func_is_cart_empty = func_is_cart_empty($cart);

    if ($func_is_cart_empty)
        return $cart;

    if (!func_is_valid_payment_method($paymentid)) {
        $paymentid = 0;
    }   

    $old_paymentid = @$cart['paymentid'];

    $cart['paymentid'] = $paymentid;
    $cart['is_payment_changed'] = ($old_paymentid !== $paymentid);
    return $cart;
}

/*
 * Get paymentid for paypal_express if it enabled
 */
function func_cart_get_paypal_express_id()
{
    global $sql_tbl;

    static $paypal_express_enabled;

    if (isset($paypal_express_enabled))
        return $paypal_express_enabled;

    $paypal_express_enabled = func_query_first_cell("SELECT $sql_tbl[payment_methods].paymentid FROM $sql_tbl[ccprocessors], $sql_tbl[payment_methods] WHERE $sql_tbl[ccprocessors].processor='ps_paypal_pro.php' AND $sql_tbl[ccprocessors].paymentid=$sql_tbl[payment_methods].paymentid AND $sql_tbl[payment_methods].active='Y' ORDER BY $sql_tbl[payment_methods].protocol DESC");
    return $paypal_express_enabled;
}

/*
 * Check if payment method is realtime
 */
function func_is_online_payment_method($payment)
{
    settype($payment, 'array');
    return !empty($payment['processor_file']);
}

/**
 * Check if total cost is zero 
 */
function func_cart_is_zero_total_cost($cart)
{
    return !empty($cart) && isset($cart['total_cost']) && $cart['total_cost'] == 0;
}

/**
 * Remove online payment methods
 */
function func_cart_remove_online_payments($payment_methods)
{
    $is_online_pm_removed = false;

    if (empty($payment_methods))
        return array($is_online_pm_removed, $payment_methods);

    foreach ($payment_methods as $k => $p) {

        if (func_is_online_payment_method($p)) {
            unset($payment_methods[$k]);
            $is_online_pm_removed = true;
        }
    }

    $payment_methods = array_values($payment_methods);

    return array($is_online_pm_removed, $payment_methods);
}

/**
 * Force add first offline method(for ex, paymentid=1) to payments list if all online are disabled and egoods_manual_cc_processing/user_preauth_for_esd is 'Y'
 * or cart[total_cost] == 0
 */
function func_cart_add_offline_payment($condition='', $set_surcharge_zero=false)
{
    global $sql_tbl, $shop_language, $config;

    $force_offline_paymentid = intval($config['Egoods']['force_offline_paymentid']);
    if (empty($force_offline_paymentid))
        return array();

    $payment_methods = func_query(
        "SELECT pm.*, '' AS module_name, '' AS processor, '' AS type, pm.payment_method AS payment_method_orig,"
        . " IFNULL(l1.value, pm.payment_method) AS payment_method, IFNULL(l2.value, pm.payment_details) AS payment_details,"
        . " '' AS has_preauth, '' AS use_preauth, '' AS background, '' AS disable_ccinfo"
        . " FROM $sql_tbl[payment_methods] AS pm"
        . " LEFT JOIN $sql_tbl[languages_alt] AS l1 ON l1.name = CONCAT('payment_method_', pm.paymentid) AND l1.code = '$shop_language'"
        . " LEFT JOIN $sql_tbl[languages_alt] AS l2 ON l2.name = CONCAT('payment_details_', pm.paymentid) AND l2.code = '$shop_language'"
        . " WHERE pm.processor_file = '' AND pm.paymentid='$force_offline_paymentid'"
        . $condition
    );

    if (!empty($payment_methods)) {
        $payment_methods[0]['active'] = 'Y';

        if ($set_surcharge_zero) {
            $payment_methods[0]['surcharge'] = '0.00';
            $payment_methods[0]['surcharge_type'] = '$';
        }            
    }        
    
    return $payment_methods;
} 

/**
 * Check if payment methods list was changed after some customer action
 * Presume that IN $payment_methods is defined before last Func_calculate
 */
function func_cart_is_payment_methods_list_changed($payment_methods, $run_func_calculate = '')
{
    global $user_account, $cart, $current_area, $logged_userid;

    if (!empty($run_func_calculate)) {
        // Recalculate cart to obtain cart['total_cost']
        list($cart, $products) = func_generate_products_n_recalculate_cart();
    }

    settype($payment_methods, 'array');
    $_new_payment_methods = check_payment_methods(@$user_account['membershipid']);
    settype($_new_payment_methods, 'array');

    return count($payment_methods) != count($_new_payment_methods);
}

/*
 * Recalculate shippings and check session shippingid regarding possible methods
 */
function func_cart_get_shippingid($cart, $userinfo='')
{
    global $intershipper_recalc;

    if (empty($userinfo)) {
        x_load('user');
        $userinfo = func_userinfo_from_scratch(array(), 'userinfo_for_cart');
    }        

    $intershipper_recalc = 'Y';

    x_load('shipping');
    $shippings = func_get_shipping_methods_list($cart, $cart['products'], $userinfo);

    if (is_array($shippings)) {

        $found = false;
        $shippingid = $cart['shippingid'];

        for ($i = 0; $i < count($shippings); $i++) {
            if ($shippingid == $shippings[$i]['shippingid']) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            $shippingid = $shippings[0]['shippingid'];
        }

    } else {

        $shippingid = 0;

    }

    return $shippingid;
}

/*
 * Set shippingid in cart session var. Cannot be used from functions where $cart declated as "global $cart"
 */    
function func_cart_set_shippingid($cart, $shippingid)
{
    $func_is_cart_empty = func_is_cart_empty($cart);

    if ($func_is_cart_empty)
        return $cart;

    $cart['shippingid'] = $shippingid; 
    return $cart;
}

/**
 * Fully recalculate cart and update $products list
 */
function func_generate_products_n_recalculate_cart($paymentid = 0) {
    global $intershipper_recalc, $user_account, $logged_userid, $current_area;
    global $cart;

    x_session_register('cart');
    x_session_register('intershipper_recalc');

    $intershipper_recalc = 'Y';

    // Recalculate cart totals after new item added
    $products     = func_products_in_cart(
        $cart,
        (!empty($user_account['membershipid'])
            ? $user_account['membershipid']
            : ''
        )
    );

    $cart         = func_array_merge(
        $cart,
        func_calculate(
            $cart,
            $products,
            $logged_userid,
            $current_area,
            (!empty($paymentid) ? intval($paymentid) : 0)
        )
    );
    
    $cart = func_cart_set_flag('need_recalculate', null);
    return array($cart, $products);
}

/**
* Set boolean flag for cart
*/
function func_cart_set_flag($flag, $value)
{
    global $cart;
    $possible_flags = array('need_recalculate' => '');

    if (empty($cart))
        x_session_register('cart');

    if (!isset($possible_flags[$flag]))        
        return $cart;

    if (func_is_cart_empty($cart))        
        return $cart;

    if (is_null($value)) {
        unset($cart[$flag]);
    } else {
        $cart[$flag] = $value;
    }

    return $cart;
}

/**
* Get boolean flag from cart
*/
function func_cart_get_flag($flag){
    global $cart;

    if (empty($cart))
        x_session_register('cart');

    if (isset($cart[$flag]))
        return $cart[$flag];
    else 
        return null;
}
/**
 * Check if minicart content should be updated
 */
function func_is_minicart_update_needed()
{
    $need_recalculate = func_cart_get_flag('need_recalculate');
    return !empty($need_recalculate);
}

/**
 * Check if the payment is processed using direct post
 * of the cc data to a payment gateway
 * (web-based method with ccinfo form enabled)
*/
function func_is_direct_post_payment_method($payment_data)
{
    return (
        !empty($payment_data['processor']) 
        && @$payment_data['background'] == 'N' 
        && @$payment_data['disable_ccinfo'] == 'N'
    );
}

/**
 * Generate some unique key and save it in DB. Used for Start Amazon/Google Checkout checkout request
 */
function func_generate_n_save_uniqueid($err_lbl='txt_gcheckout_impossible_error', $max_attempts=10)
{
    global $XCARTSESSID, $sql_tbl;

    $_index = 0;
    while (true) {
        $_index++;
        $unique_id = md5(uniqid(rand()));
        @db_query("INSERT INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('$unique_id','".$XCARTSESSID."','GO|')");
        if (db_affected_rows() > 0)
            break;
        if ($_index > $max_attempts) // Impossible error: just to avoid the potential infinite loop
            die(func_get_langvar_by_name($err_lbl, '', false, true));
    }

    return $unique_id;
}

/**
 * Generate international product description based on product_descr and product options. Used for payments
 */
function func_payment_product_description($product, $limit=0, $language='')
{
    global $shop_language, $sql_tbl;

    if (empty($language))
        $language = $shop_language;

    $_descr = '';

    if (!empty($product['product_options']) && is_array($product['product_options'])) {
        $_descr_arr = array();
        foreach ($product['product_options'] as $k=>$v) {
            $_descr_arr[] = "$v[class]: $v[option_name]";
        }
        $_descr = "(" . implode('; ', $_descr_arr) . ")";
    }

    $_descr .= " " . strip_tags(func_query_first_cell("SELECT IF($sql_tbl[products_lng].descr != '', $sql_tbl[products_lng].descr, $sql_tbl[products].descr) AS descr FROM $sql_tbl[products] LEFT JOIN $sql_tbl[products_lng] ON $sql_tbl[products].productid = $sql_tbl[products_lng].productid AND $sql_tbl[products_lng].code = '".$shop_language."' WHERE $sql_tbl[products].productid='$product[productid]'"));

    if (
        !empty($limit)
        && strlen($_descr) > $limit
    ) {
        $_descr = substr($_descr, 0, $limit);
    }

    return $_descr;
} 

/**
 * Check if shipping is needed fo the cart
 */
function func_cart_is_need_shipping($cart, $products, $userinfo, $check_free_ship_coupon = 'yes')
{
    global $active_modules, $config;

    $need_shipping = false;

    if (
        $config['Shipping']['enable_shipping'] == 'Y'
        && is_array($products)
        && (
            !empty($userinfo)
            || $config['General']['apply_default_country'] == 'Y'
            || $config['Shipping']['enable_all_shippings'] == 'Y'
        )
    ) {
        foreach ($products as $pKey => $product) {

            if (
                !empty($product["distribution"])
                || (
                    (
                        $product['free_shipping'] == 'Y'
                        || @$product['free_shipping_used']
                    )
                    && $config['Shipping']['do_not_require_shipping'] == 'Y'
                )
            ) {
                continue;
            }

            // Check if all products in the cart have shipping freight

            if (
                $config['Shipping']['replace_shipping_with_freight'] == 'Y'
                && $product['shipping_freight'] > 0
            ) {
                continue;
            }

            $need_shipping = true;

            break;

        }

    }

    // Process discount coupons
    if (
        !empty($active_modules['Discount_Coupons'])
        && isset($cart['coupon_type'])
        && $cart['coupon_type'] == 'free_ship'
        && $config['Shipping']['do_not_require_shipping'] == 'Y'
        && $need_shipping
        && $check_free_ship_coupon === 'yes'
    ) {
        $need_shipping = false;
    }

    return $need_shipping;
}

/**
 * Lock cart for change
 */
function func_cart_lock($by_process, $run_x_session_save = '')
{
    global $cart_locked_by_process;

    $cart_locked_by_process = $by_process;
    x_session_register('cart_locked_by_process');

    if (!empty($run_x_session_save)) {
        x_session_save('cart_locked_by_process');
    }

    return true;
}

/**
 * Release cart for all operations
 */
function func_cart_unlock($run_x_session_save = '')
{
    global $cart_locked_by_process;

    $cart_locked_by_process = false;
    x_session_register('cart_locked_by_process');

    if (!empty($run_x_session_save)) {
        x_session_save('cart_locked_by_process');
    }

    return true;
}

/**
 * Get lock status for cart
 */
function func_cart_is_locked()
{
    global $cart_locked_by_process;

    if (!isset($cart_locked_by_process))
        x_session_register('cart_locked_by_process');

    return $cart_locked_by_process;
}

?>
