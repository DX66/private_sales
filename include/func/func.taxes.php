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
 * Taxes-related functions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.taxes.php,v 1.39.2.4 2011/03/03 13:27:04 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load('cart');

/**
 * This function gathers the product taxes information
 */
function func_get_product_taxes(&$product, $userid = 0, $calculate_discounted_price = false, $taxes = '')
{
    global $config;

    $amount = (isset($product['amount']) ? $product['amount'] : 1);

    if ($calculate_discounted_price && isset($product['discounted_price']))
        $price = $product['discounted_price'] / $amount;
    else
        $price = $product['price'];

    if (empty($taxes))
        $taxes = func_get_product_tax_rates($product, $userid);

    if (defined('XAOM')) {
        global $sql_tbl;
        if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products] WHERE productid = '$product[productid]'") == 0)
            $taxes = $product['extra_data']['taxes'];
    }

    $price_deducted_tax_flag = '';
    $total_tax_percent = 0;
    $total_tax_value = 0;

    x_load('user');
    $_anonymous_userinfo = func_get_anonymous_userinfo();

    foreach ($taxes as $k=>$tax_rate) {

        if (empty($userid) && $config['General']['apply_default_country'] != 'Y')
            continue;

        if ($tax_rate['price_includes_tax'] != 'Y' || $product['price_deducted_tax'] == 'Y')
            continue;

        if (!preg_match("!\b(DST|ST)\b!", $tax_rate['formula']))
            continue;

        if ($tax_rate['rate_type'] == "%") {
            $total_tax_percent += $tax_rate['rate_value'];
        }
        else {
            $total_tax_value += $tax_rate['rate_value'];
        }

        $price_deducted_tax_flag = 'Y';
    }

    if (!empty($total_tax_percent)) {
        $total_tax_value += ($price - $total_tax_value) * (1-100 / ($total_tax_percent + 100) );
    }

    if (!empty($total_tax_value)) {
        $product['price'] = $price = $price - $total_tax_value;
    }

    if (!defined('XAOM'))
        $product['price_deducted_tax'] = $price_deducted_tax_flag;

    $taxed_price = $price;

    $formula_data['ST'] = $price;

    foreach ($taxes as $k=>$tax_rate) {

        // Calculate the tax value

        if (!empty($tax_rate['skip']) || (empty($userid) && empty($_anonymous_userinfo) && $config['General']['apply_default_country'] != 'Y'))
            continue;

        $assessment = func_calculate_assessment($tax_rate['formula'], $formula_data);

        if ($tax_rate['rate_type'] == "%") {
            $tax_rate['tax_value_precise'] = $assessment *  $tax_rate['rate_value'] / 100;
            $tax_rate['tax_value'] = $tax_rate['tax_value_precise'];
        }
        else {
            $tax_rate['tax_value'] = $tax_rate['tax_value_precise'] = $tax_rate['rate_value'];
        }

        $tax_rate['taxed_price'] = $price + $tax_rate['tax_value'];

        if ($tax_rate['display_including_tax'] == 'Y')
            $taxed_price += $tax_rate['tax_value'];

        $taxes[$k] = $tax_rate;

        $formula_data[$k] = $tax_rate['tax_value'];
    }

    if (is_array($taxes)) {
        foreach ($taxes as $k=>$v) {
            $taxes[$k]['tax_value'] = $v['tax_value_precise'] * $amount;
        }
    }

    $product['taxed_price'] = price_format($taxed_price);

    return $taxes;

}

/**
 * This function generate the product tax rates array
 */
function func_get_product_tax_rates($product, $userid)
{
    global $sql_tbl, $user_account, $config, $single_mode, $global_store;
    static $saved_tax_rates = array();

    // Define input data
    $is_array = true;
    if (isset($product['productid'])) {
        $is_array = false;
        $_product = array($product['productid'] => $product);

    } else {
        $_product = array();
        foreach ($product as $k => $p) {
            $_product[$p['productid']] = $p;
        }
    }

    unset($product);

    $membershipid = $user_account['membershipid'];

    // Select taxes data
    $_taxes = func_query_hash("SELECT $sql_tbl[taxes].*, $sql_tbl[product_taxes].productid FROM $sql_tbl[taxes], $sql_tbl[product_taxes] WHERE $sql_tbl[taxes].taxid=$sql_tbl[product_taxes].taxid AND $sql_tbl[product_taxes].productid IN ('".implode("','", array_keys($_product))."') AND $sql_tbl[taxes].active='Y' ORDER BY $sql_tbl[taxes].priority", "productid");

    if (empty($_taxes) || !is_array($_taxes))
        return array();

    // Define available customer zones
    $zone_account = defined('XAOM') ? $user_account['id'] : $userid;
    $tax_rates = $address_zones = $_tax_names = array();
    foreach ($_taxes as $pid => $_tax) {
        foreach ($_tax as $k => $v) {
            $_tax_names['tax_'.$v['taxid']] = true;
        }
    }

    // Get tax names
    $_tax_names = func_get_languages_alt(array_keys($_tax_names));

    if ($config['Taxes']['enable_user_tax_exemption'] == 'Y') {

        // Get the 'tax_exempt' feature of customer

        static $_customer_tax_exempt;

        if (empty($_customer_tax_exempt)) {
            $_customer_tax_exempt = func_query_first_cell("SELECT tax_exempt FROM $sql_tbl[customers] WHERE id='$zone_account'");
        }

        if ($_customer_tax_exempt == 'Y') {
            $tax_rate['skip'] = true;
        }
    }
    else {
        $_customer_tax_exempt = '';
    }

    foreach ($_product as $productid => $product) {
        if (@$product['free_tax'] == 'Y' || !is_array($_taxes[$productid]) || empty($_taxes[$productid]))
            continue;

        $taxes = $_taxes[$productid];

        // Generate tax rates array
        foreach ($taxes as $k => $v) {

            $provider_condition = '';
            if (!$single_mode)
                $provider_condition = "AND $sql_tbl[tax_rates].provider = '$product[provider]'";

            if (!isset($address_zones[$product['provider']][$v["address_type"]])) {
                $address_zones[$product['provider']][$v["address_type"]] = array_keys(func_get_customer_zones_avail($zone_account, $product['provider'], $v["address_type"]));
            }
            $zones = $address_zones[$product['provider']][$v["address_type"]];

            $tax_rate = array();
            if (!empty($zones) && is_array($zones)) {
                foreach ($zones as $zoneid) {
                    if (!$single_mode && isset($saved_tax_rates[$product['provider']][$v['taxid']][$zoneid][$membershipid])) {

                        // Get saved data (by provider name, zoneid and membershipid)
                        $tax_rate = $saved_tax_rates[$product['provider']][$v['taxid']][$zoneid][$membershipid];

                    } elseif ($single_mode && isset($saved_tax_rates[$v['taxid']][$zoneid][$membershipid])) {

                        // Get saved data (by zoneid and membershipid)
                        $tax_rate = $saved_tax_rates[$v['taxid']][$zoneid][$membershipid];

                    } else {

                        $tax_rate = func_query_first("SELECT $sql_tbl[tax_rates].taxid, $sql_tbl[tax_rates].formula, $sql_tbl[tax_rates].rate_value, $sql_tbl[tax_rates].rate_type FROM $sql_tbl[tax_rates] LEFT JOIN $sql_tbl[tax_rate_memberships] ON $sql_tbl[tax_rate_memberships].rateid = $sql_tbl[tax_rates].rateid WHERE $sql_tbl[tax_rates].taxid = '$v[taxid]' $provider_condition AND $sql_tbl[tax_rates].zoneid = '$zoneid' AND ($sql_tbl[tax_rate_memberships].membershipid = '$membershipid' OR $sql_tbl[tax_rate_memberships].membershipid IS NULL) ORDER BY $sql_tbl[tax_rate_memberships].membershipid DESC");
                        
                        // Use original rate_value/formula/rate_type for aom order calculation bt:0095797
                        if (defined('XAOM') && !empty($global_store['product_taxes'])) {
                            $tax_rate = func_aom_tax_rates_replace($productid, $v, $tax_rate);
                        }    

                        if (!$single_mode) {
                            // Save data (by provider name, zoneid and membershipid)
                            $saved_tax_rates[$product['provider']][$v['taxid']][$zoneid][$membershipid] = $tax_rate;

                        } else {
                            // Save data (by zoneid and membershipid)
                            $saved_tax_rates[$v['taxid']][$zoneid][$membershipid] = $tax_rate;
                        }
                    }

                    if (!empty($tax_rate))
                        break;
                }
            }

            if (empty($tax_rate) || $tax_rate['rate_value'] == 0 || $_customer_tax_exempt == 'Y') {
                if ($v['price_includes_tax'] != 'Y')
                    continue;
                $tax_rate = func_query_first("SELECT $sql_tbl[tax_rates].taxid, $sql_tbl[tax_rates].formula, $sql_tbl[tax_rates].rate_value, $sql_tbl[tax_rates].rate_type FROM $sql_tbl[tax_rates] LEFT JOIN $sql_tbl[tax_rate_memberships] ON $sql_tbl[tax_rate_memberships].rateid = $sql_tbl[tax_rates].rateid WHERE $sql_tbl[tax_rates].taxid='$v[taxid]' $provider_condition AND ($sql_tbl[tax_rate_memberships].membershipid = '$membershipid' OR $sql_tbl[tax_rate_memberships].membershipid IS NULL) ORDER BY $sql_tbl[tax_rates].rate_value DESC");

                // Use original rate_value/formula/rate_type for aom order calculation bt:0095797
                if (defined('XAOM') && !empty($global_store['product_taxes'])) {
                    $tax_rate = func_aom_tax_rates_replace($productid, $v, $tax_rate);
                }    

                $tax_rate['skip'] = true;
            }

            if (empty($tax_rate['formula']))
                $tax_rate['formula'] = $v['formula'];

            $tax_rate['rate_value'] *= 1;

            // Do not overwrite originally saved tax names for aom order calculation bt:0096284
            if (!defined('XAOM') || empty($tax_rate['tax_display_name'])) 
                $tax_rate['tax_display_name'] = isset($_tax_names['tax_'.$v['taxid']]) ? $_tax_names['tax_'.$v['taxid']] : $v['tax_name'];

            if ($is_array) {
                $tax_rates[$productid][$v['tax_name']] = func_array_merge($v, $tax_rate);
            } else {
                $tax_rates[$v['tax_name']] = func_array_merge($v, $tax_rate);
            }
        }
    }

    return $tax_rates;
}

/**
 * This function get the taxed price
 */
function func_tax_price($price, $productid=0, $disable_abs=false, $discounted_price=NULL, $customer_info="", $taxes="", $price_deducted_tax=false, $amount=1)
{
    global $sql_tbl, $config, $active_modules, $shop_language;

    if (empty($customer_info)) {
        global $logged_userid;
        $customer_info['id'] = $logged_userid;
    }

    $return_taxes = array();

    $no_discounted_price = false;
    if (is_null($discounted_price)) {
        $discounted_price = $price;
        $no_discounted_price = true;
    }

    if ($productid > 0) {

        // Get product taxes

        $product = func_query_first("SELECT productid, provider, free_shipping, shipping_freight, distribution, '$price' as price FROM $sql_tbl[products] WHERE productid='$productid'");

        $taxes = func_get_product_tax_rates($product, $customer_info['id']);
    }

    $total_tax_cost = 0;

    if (is_array($taxes)) {

        // Calculate price and tax_value

        foreach ($taxes as $k=>$tax_rate) {
            if ($tax_rate['price_includes_tax'] != 'Y' || $price_deducted_tax)
                continue;

            if (!preg_match("!\b(DST|ST)\b!S", $tax_rate['formula']))
                continue;

            if (!empty($tax_rate['skip']) || (empty($customer_info['id']) && $config['General']['apply_default_country'] != 'Y'))
                continue;

            if ($tax_rate['rate_type'] == "%") {
                $_tax_value = $price - $price*100/($tax_rate['rate_value'] + 100);
                $price -= $_tax_value;
                if ($discounted_price > 0)
                    $_tax_value = $discounted_price - $discounted_price*100/($tax_rate['rate_value'] + 100);

                $discounted_price -= $_tax_value;

            }
            else {
                $price -= $tax_rate['rate_value'] * $amount;
                $discounted_price -= $tax_rate['rate_value'] * $amount;
            }
        }

        $taxed_price = $discounted_price;

        $formula_data['ST'] = $price;
        if (!$no_discounted_price)
            $formula_data['DST'] = $discounted_price;

        foreach ($taxes as $k=>$v) {
            if (!empty($v['skip']))
                continue;

            if ($v['display_including_tax'] != 'Y')
                continue;

            if (empty($customer_info['id']) && $config['General']['apply_default_country'] != 'Y')
                continue;

            if ($v['rate_type'] == "%") {
                $assessment = func_calculate_assessment($v['formula'], $formula_data);
                $tax_value = price_format($assessment * $v['rate_value'] / 100);
            }
            elseif (!$disable_abs) {
                $tax_value = $v['rate_value'] * $amount;
            }

            $formula_data[$v['tax_name']] = $tax_value;

            $total_tax_cost += $tax_value;

            $taxed_price += $tax_value;

            $return_taxes['taxes'][$v['taxid']] = $tax_value;
        }
    }

    $return_taxes['taxed_price'] = $taxed_price;
    $return_taxes['net_price'] = $taxed_price - $total_tax_cost;

    return $return_taxes;
}

/**
 * This function calculates the assessment according to the formula string
 */
function func_calculate_assessment($formula, $formula_data)
{
    $return = 0;
    if (is_array($formula_data)) {
        // Correct the default values...
        if (!isset($formula_data['DST']))
            $formula_data['DST'] = $formula_data['ST'];

        if (empty($formula_data['SH']))
            $formula_data['SH'] = 0;

        // Preparing math expression...
        $_formula = $formula;
        foreach ($formula_data as $unit=>$value) {
            if (!is_numeric($value))
                $value = 0;

            $_formula = preg_replace("/\b".preg_quote($unit,'/')."\b/S", $value, $_formula);
        }

        $to_eval = "\$return = $_formula;";
        // Perform math expression...
        eval($to_eval);
    }

    return $return;
}

?>
