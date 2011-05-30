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
 * Order editing functions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.edit.php,v 1.37.2.3 2011/01/10 13:11:54 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

x_load('cart','mail','order','product','taxes','category');

/**
 * This function calculates the product's quantity in stock to display it
 * on the Edit products dialog
 */
function func_get_quantity_in_stock($productid, $order_status, $options = array(), $order_product = array())
{
    global $sql_tbl, $active_modules;

    $quantity_in_stock = (strpos('PCQI',$order_status) !== false) ? ((!empty($active_modules['Egoods']) && !empty($order_product['distribution'])) ? 0 : $order_product['amount']) : 0;
    if (!empty($active_modules['Product_Options']) && !empty($options)) {
        $is_equal = $is_variants = false;

        if (!empty($order_product['product_options']) && is_array($order_product['product_options'])) {
            $order_options = array();
            foreach ($order_product['product_options'] as $cid => $o) {
                $order_options[$cid] = $o['optionid'];
            }
            $order_variantid = func_get_variantid($order_options);
            $variantid = func_get_variantid($options);

            $is_equal = ($order_variantid == $variantid);
            $is_variants = !empty($order_variantid);
        }

        $quantity_in_stock += ($is_equal) ? func_query_first_cell("SELECT avail FROM $sql_tbl[variants] WHERE variantid='$variantid'") : 0;

        if (!$is_equal) {
            $quantity_in_stock = 0;
        }

        if ($is_variants) {
            $quantity_in_stock += func_query_first_cell("SELECT avail FROM $sql_tbl[variants] WHERE variantid='$variantid'");
        } else {
            $quantity_in_stock += func_get_options_amount($options, $productid);
        }

    } else {
        $quantity_in_stock += func_query_first_cell("SELECT avail FROM $sql_tbl[products] WHERE productid='$productid'");
    }

    if (!empty($active_modules['RMA']))
        $quantity_in_stock -= (int)func_query_first_cell("SELECT SUM(returned_amount) FROM $sql_tbl[returns] WHERE itemid = '$order_product[itemid]'");

    return $quantity_in_stock;
}

/**
 * This function validates the price that can be entered e.g. as $15.07
 */
function func_aom_validate_price($price)
{
    return func_detect_price($price);
}

/**
 * This function updates products prices with VAT values
 */
function func_aom_update_prices($products, $customer_info)
{
    global $config, $real_taxes;

    foreach ($products as $k=>$v) {
        $products[$k]['price_deducted_tax'] = 'Y';
        if ($real_taxes == 'Y')
            $_taxes = func_get_product_taxes($products[$k], $customer_info['id'], false);
        else
            $_taxes = func_get_product_taxes($products[$k], $customer_info['id'], false, $v['extra_data']['taxes']);
        $products[$k]['extra_data']['taxes'] = $products[$k]['taxes'] = $_taxes;
    }
    return $products;
}

/**
 * This function recalculate order totals
 */
function func_recalculate_totals($cart)
{
    global $active_modules, $real_taxes, $order_data, $config, $global_store;

    if ($real_taxes == 'Y') {

    // Calculate taxes etc depending on the current store settings

        global $current_area, $logged_userid, $user_account;
        $_saved_data = compact('current_area', 'login', 'logged_userid', 'user_account');
        $current_area = 'C';
        $login = $cart['userinfo']['login'];
        $logged_userid = $cart['userinfo']['id'];
        $user_account = $cart['userinfo'];
    }

    $saved_state = false;
    if (!empty($active_modules['Special_Offers'])) {
        $saved_state = true;
        unset($active_modules['Special_Offers']);
    }

    if ($cart['use_discount_alt'] == 'Y') {

        if (!defined('XAOM_WO_DISCOUNT_DATA') && !empty($cart['extra']['discount_info']) && !empty($cart['extra']['discount_info']['discount']))
            define('XAOM_WO_DISCOUNT_DATA', 1);

        $global_store['discounts'] = array(array(
            '__override' => true,
            'discountid' => 999999999,
            'minprice' => 0,
            'discount' => ((!empty($cart['extra']['discount_info']) && !empty($cart['extra']['discount_info']['discount'])) ? $cart['extra']['discount_info']['discount'] : $cart['discount_alt']),
            'discount_type' => ((!empty($cart['extra']['discount_info']) && !empty($cart['extra']['discount_info']['discount_type'])) ? $cart['extra']['discount_info']['discount_type'] : "absolute")
        ));
    }

    if (!empty($cart['use_coupon_discount_alt']) && $cart['use_coupon_discount_alt'] == 'Y') {
        $global_store['discount_coupons'] = array(array(
            '__override' => true,
            'coupon' => "Order#".$cart['orderid'],
            'discount' => $cart['coupon_discount_alt'],
            'coupon_type' => 'absolute',
            'minimum' => 0,
            'times' => 999999999,
            'times_used' => 0,
            'expire' => XC_TIME+30879000,
            'status' => 'A',
        ));

    } elseif (isset($cart['extra']['discount_coupon_info']) && !empty($cart['extra']['discount_coupon_info']) && $cart['extra']['discount_coupon_info']['coupon'] == $cart['coupon']) {
        $coupon_data = $cart['extra']['discount_coupon_info'];
        $coupon_data['__override'] = true;
        $global_store['discount_coupons'] = array($coupon_data);
    }

    // Initialization global var for func_calculate_single bt:0095797
    foreach ($cart['products'] as $k => $v) {
        $global_store['product_taxes'][$v['productid']] = $v['extra_data']['taxes'];
    } 

    $cart['products'] = func_aom_update_prices($cart['products'], $cart['userinfo']);

    $cart = func_array_merge($cart, func_calculate($cart, $cart['products'], $cart['userinfo']['id'], $cart['userinfo']['usertype'], $cart['paymentid']));

    $cart['total'] = $cart['total_cost'];

    $cart['applied_taxes'] = $cart['taxes'];

    if (is_array($cart['orders'])) {
        $cart['tax'] = $cart['orders'][0]['tax_cost'];
        $cart['taxes'] = $cart['orders'][0]['taxes'];
    }

    // Correct state, country and county full names (is its modified)

    $uinfo = $cart['userinfo'];

    // Correct the billing address
    if ($uinfo['b_state'].$uinfo['b_country'].$uinfo['b_county'] != $order_data['userinfo']['b_state'].$order_data['userinfo']['b_country'].$order_data['userinfo']['b_county']) {
        $uinfo['b_statename'] = $uinfo['b_state_text'] = func_get_state($uinfo['b_state'], $uinfo['b_country']);
        $uinfo['b_countryname'] = $uinfo['b_country_text'] = func_get_country($uinfo['b_country']);
        if ($config['General']['use_counties'] == 'Y')
            $uinfo['b_countyname'] = $uinfo['b_county_text'] = func_get_county($uinfo['b_county']);
    }

    // Correct the shipping address
    if ($uinfo['s_state'].$uinfo['s_country'].$uinfo['s_county'] != $order_data['userinfo']['s_state'].$order_data['userinfo']['s_country'].$order_data['userinfo']['s_county']) {
        $uinfo['s_statename'] = $uinfo['s_state_text'] = func_get_state($uinfo['s_state'], $uinfo['s_country']);
        $uinfo['s_countryname'] = $uinfo['s_country_text'] = func_get_country($uinfo['s_country']);
        if ($config['General']['use_counties'] == 'Y')
            $uinfo['s_countyname'] = $uinfo['s_county_text'] = func_get_county($uinfo['s_county']);
    }

    $cart['userinfo'] = $uinfo;

    if ($saved_state) {
        $active_modules['Special_Offers'] = true;
    }

    if (!empty($_saved_data))
        extract($_saved_data);

    return $cart;

}

/**
 * This function updates the order info in the database
 */
function func_update_order($cart, $old_order_data = "")
{
    global $sql_tbl, $config, $active_modules, $xcart_dir, $dhl_ext_country, $all_languages, $ship_packages_uniq;
    $old_products = $old_order_data['products'];

    $cart = func_recalculate_totals($cart);

    $userinfo = $cart['userinfo'];
    $products = $cart['products'];
    $giftcerts = $cart['giftcerts'];

    if (!empty($active_modules['XAffiliate'])) {
        $partner = func_query_first_cell("SELECT userid FROM $sql_tbl[partner_payment] WHERE orderid = '$cart[orderid]' AND affiliate = '0'");
        global $single_mode;
    }

    // Update stock level

    if (in_array($cart['status'], array('Q','I','P','C')) && $config['General']['unlimited_products'] != 'Y') {

        $_products = $_old_products = array();

        if (is_array($products)) {
            foreach($products as $k => $product) {
                if (!empty($active_modules['Egoods']) && !empty($product['distribution']))
                    continue;

                if ($product['deleted'])
                    $product['amount'] = 0;

                if ($product['stock_update'] == 'Y') {
                    $amount_orig = (is_array($old_products) && $old_products[$k]['amount']) ? $old_products[$k]['amount'] : 0;
                    $amount_ret = ($active_modules['RMA'] && $product['returned_to_stock']) ? $product['returned_to_stock'] : 0;
                    $amount_change = $amount_orig - $product['amount'] - $amount_ret;

                    if ($amount_change) {
                        $product['amount'] = abs($amount_change);
                        func_update_quantity(array($product), $amount_change > 0);
                    }
                }
            }
        }
    }

    // Prepare data

    $_extra = $cart['extra'];
    $_extra['tax_info']['taxed_subtotal'] = $cart['display_subtotal'];
    $_extra['tax_info']['taxed_discounted_subtotal'] = $cart['display_discounted_subtotal'];
    $_extra['tax_info']['taxed_shipping'] = $cart['display_shipping_cost'];
    unset($_extra['tax_info']['product_tax_name']);
    $_extra['additional_fields'] = $userinfo['additional_fields'];

    if (!empty($dhl_ext_country)) {
        $is_dhl_shipping = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[shipping] WHERE shippingid = '$cart[shippingid]' AND code = 'ARB' AND destination = 'I'") > 0;
        if ($is_dhl_shipping) {
            if (!function_exists('func_shipper_ARB')) {
                require_once $xcart_dir.'/shipping/mod_ARB.php';
            } else {
                global $dhl_ext_countries;
            }

            if (empty($dhl_ext_countries))
                $dhl_ext_country = false;

        } else {
            $dhl_ext_country = false;
        }

    }

    if (!empty($dhl_ext_country)) {
        $_extra['dhl_ext_country'] = $dhl_ext_country;
    } else {
        func_unset($_extra, 'dhl_ext_country');
    }

    // Restore tax names for all languages bt:0096284
    if (is_array($cart['taxes'])) {
        foreach($cart['taxes'] as $ktax => $vtax) {
            $cart['taxes'][$ktax]['intl_tax_names'] = isset($old_order_data['order']['applied_taxes'][$ktax]['intl_tax_names']) ?
                    $old_order_data['order']['applied_taxes'][$ktax]['intl_tax_names'] :
                    func_get_languages_alt('tax_'.$vtax['taxid'], false, false, false, true);
        }
    }

    $taxes_applied = serialize($cart['taxes']);

    if (!empty($cart['use_shipping_cost_alt']))
        $cart['shipping_cost'] = $cart['shipping_cost_alt'];

    $userinfo['b_address'] .= "\n".$userinfo['b_address_2'];
    $userinfo['s_address'] .= "\n".$userinfo['s_address_2'];

    // Update order info

    $memberships = func_get_memberships('C', true);
    $query_data = array(
        'total' => $cart['total'],
        'giftcert_discount' => $cart['giftcert_discount'],
        'giftcert_ids' => $cart['giftcert_ids'],
        'subtotal' => $cart['subtotal'],
        'shipping_cost' => $cart['shipping_cost'],
        'shippingid' => $cart['shippingid'],
        'tax' => $cart['tax'],
        'taxes_applied' => $taxes_applied,
        'discount' => $cart['discount'],
        'coupon' => ($cart['coupon'] ? ((preg_match("/(free_ship|percent|absolute)/S", $cart['coupon_type'])) ? ($cart['coupon_type']."``".$cart['coupon']) : $cart['coupon']) : ''),
        'coupon_discount' => $cart['coupon_discount'],
        'payment_method' => $cart['payment_method'],
        'paymentid' => $cart['paymentid'],
        'payment_surcharge' => $cart['payment_surcharge'],
        'extra' => serialize($_extra),

        'membership' => !empty($memberships[$userinfo['membershipid']]) ? $memberships[$userinfo['membershipid']]['membership'] : '',
        'membershipid' => $userinfo['membershipid'],
        'title' => $userinfo['title'],
        'firstname' => $userinfo['firstname'],
        'lastname' => $userinfo['lastname'],
        'company' => $userinfo['company'],
        'tax_number' => $userinfo['tax_number'],
        'tax_exempt' => $userinfo['tax_exempt'],
        'b_title' => $userinfo['b_title'],
        'b_firstname' => $userinfo['b_firstname'],
        'b_lastname' => $userinfo['b_lastname'],
        'b_address' => $userinfo['b_address'],
        'b_city' => $userinfo['b_city'],
        'b_county' => @$userinfo['b_county'],
        'b_state' => $userinfo['b_state'],
        'b_country' => $userinfo['b_country'],
        'b_zipcode' => $userinfo['b_zipcode'],
        'b_zip4' => $userinfo['b_zip4'],
        'b_phone' => $userinfo['s_phone'],
        'b_fax' => $userinfo['s_fax'],
        's_title' => $userinfo['s_title'],
        's_firstname' => $userinfo['s_firstname'],
        's_lastname' => $userinfo['s_lastname'],
        's_address' => $userinfo['s_address'],
        's_city' => $userinfo['s_city'],
        's_county' => @$userinfo['s_county'],
        's_state' => $userinfo['s_state'],
        's_country' => $userinfo['s_country'],
        's_zipcode' => $userinfo['s_zipcode'],
        's_zip4' => $userinfo['s_zip4'],
        's_phone' => $userinfo['s_phone'],
        's_fax' => $userinfo['s_fax'],
        'email' => $userinfo['email'],
        'url' => $userinfo['url']

    );
    $query_data = func_array_map('addslashes', $query_data);

    func_array2update('orders', $query_data, "orderid='$cart[orderid]'");

    // Store packages for Shipping_Label_Generator
    if (!empty($active_modules['Shipping_Label_Generator'])) {
        x_session_register('ship_packages_uniq');
        $_code = func_query_first_cell("SELECT code FROM $sql_tbl[shipping] WHERE shippingid='$cart[shippingid]'");
        $_pack_index = $products[0]['provider'] . $_code;
        db_query("DELETE FROM $sql_tbl[shipping_labels] WHERE orderid='$cart[orderid]'");
        db_query("DELETE FROM $sql_tbl[order_extras] WHERE orderid='$cart[orderid]' AND khash LIKE 'ship_packages_uniq%'");
        if (!empty($ship_packages_uniq[$_pack_index])) {
            $query_data = array(
                'orderid' => $cart['orderid'],
                'khash' => "ship_packages_uniq$_code",
                'value' => serialize($ship_packages_uniq[$_pack_index])
            );
            $query_data = func_array_map('addslashes', $query_data);
            func_array2insert('order_extras', $query_data, true);
        }
    }

    // Update order details info

    if (is_array($products)) {
        $items = array();
        foreach ($products as $pk => $product) {
            if ($product['deleted'])
                continue;

            if (!empty($active_modules['Product_Options'])) {

                $options = array();

                if (isset($product['keep_options']) && $product['keep_options'] == 'Y') {

                    // Keep original options choice
                    $options = $product['extra_data']['product_options'];
                    $options_alt = $product['extra_data']['product_options_alt'];
                    if (!$options_alt) {
                        $product['product_options'] = func_serialize_options($options);
                    } else {
                        $product['product_options'] = isset($options_alt[$config['default_admin_language']]) ? $options_alt[$config['default_admin_language']] : "";
                    }
                }
                else {

                    // Save selected options
                    if (is_array($product['product_options'])) {
                        foreach ($product['product_options'] as $k=>$v) {
                            $options[intval($v['classid'])] = ($v['is_modifier'] == 'T') ? $v["option_name"] : $v["optionid"];
                        }
                    }

                    if ($all_languages && is_array($all_languages) && count($all_languages) > 1 && !empty($active_modules['Product_Options'])) {
                        foreach($all_languages as $lng) {
                            $options_alt[$lng['code']] = func_serialize_options($options, false, $lng['code']);
                        }
                    }

                    $product['product_options'] = func_serialize_options($options);
                }

            } else {

                $product['product_options'] = '';

            }

            $product['extra_data']['product_options'] = $options;
            $product['extra_data']['product_options_alt'] = $options_alt;
            $product['extra_data']['taxes'] = $product['taxes'];
            $product['extra_data']['display']['price'] = doubleval($product['display_price']);
            $product['extra_data']['display']['discounted_price'] = doubleval($product['display_discounted_price']);
            $product['extra_data']['display']['subtotal'] = doubleval($product['display_subtotal']);

            // For AOM func_is_shipping_method_allowable function bt:#92268
            $product['extra_data']['subtotal'] = $product['subtotal'];
            $product['extra_data']['weight'] = $product['weight'];


            $query_data = array(
                'itemid' => $product['itemid'],
                'orderid' => $cart['orderid'],
                'productid' => $product['productid'],
                'product_options' => $product['product_options'],
                'amount' => $product['amount'],
                'price' => $product['price'],
                'provider' => $product['provider'],
                'extra_data' => serialize($product['extra_data']),
                'productcode' => $product['productcode'],
                'product' => $product['product']
            );
            $query_data = func_array_map('addslashes', $query_data);

            $items[] = $products[$pk]['itemid'] = func_array2insert("order_details", $query_data, true);

        }
        db_query("DELETE FROM $sql_tbl[order_details] WHERE orderid='$cart[orderid]' AND itemid NOT IN ('".implode("','", $items)."')");

        if (!empty($partner) && !empty($active_modules['XAffiliate'])) {
            $_products = $products;
            $ps = array();
            $orderid = $cart['orderid'];
            foreach($products as $v) {
                $ps[$v['provider']][] = $v;
            }

            // Get partner commision record creation date
            $xaff_force_time = func_query_first_cell("SELECT add_date FROM $sql_tbl[partner_payment] WHERE orderid = '$orderid'");
            if (empty($xaff_force_time))
                $xaff_force_time = func_query_first_cell("SELECT date FROM $sql_tbl[orders] WHERE orerid = '$orderid'");
            if (empty($xaff_force_time))
                $xaff_force_time = XC_TIME;

            db_query("DELETE FROM $sql_tbl[partner_payment] WHERE orderid = '$cart[orderid]'");
            db_query("DELETE FROM $sql_tbl[partner_product_commissions] WHERE orderid = '$cart[orderid]'");

            foreach($ps as $k => $products) {
                $current_order['provider'] = $k;
                include $xcart_dir.'/include/partner_commission.php';
            }
            $products = $_products;
            unset($_products, $current_order, $ps);
        }

        $_products = $products;
        $ps = array();
        $orderid = $cart['orderid'];
        foreach($products as $v) {
            $ps[$v['provider']][] = $v;
        }

        // Get provider commision record creation date
        $commission_force_time = func_query_first_cell("SELECT add_date FROM $sql_tbl[provider_commissions] WHERE orderid = '$orderid'");
        if (empty($commission_force_time))
            $commission_force_time = func_query_first_cell("SELECT date FROM $sql_tbl[orders] WHERE orderid = '$orderid'");
        if (empty($commission_force_time))
            $commission_force_time = XC_TIME;

        // Get provider paid commisions value
        db_query("DELETE FROM $sql_tbl[provider_product_commissions] WHERE orderid = '$cart[orderid]'");

        foreach($ps as $k => $products) {
            $current_order['provider'] = $k;
            $paid_commissions_details = func_query_first("SELECT paid_commissions, note FROM $sql_tbl[provider_commissions] WHERE orderid = '$orderid' AND userid = '$current_order[provider]'");
            db_query("DELETE FROM $sql_tbl[provider_commissions] WHERE orderid = '$cart[orderid]' AND userid = '$current_order[provider]'");

            include $xcart_dir.'/include/provider_commission.php';
        }

        $products = $_products;
        unset($_products, $current_order, $ps);
    }

    // Update gift certificates info

    if (is_array($giftcerts)) {
        foreach ($giftcerts as $giftcert) {
            if ($giftcert['deleted']) {
                db_query("DELETE FROM $sql_tbl[giftcerts] WHERE gcid='$giftcert[gcid]' AND orderid='$cart[orderid]'");
                continue;
            }
            $old_data = func_query_first("SELECT amount, debit FROM $sql_tbl[giftcerts] WHERE gcid='$giftcert[gcid]' AND orderid='$cart[orderid]'");
            if (!empty($old_data['debit'])) {
                $amount_diff = $giftcert['amount'] - $old_data['amount'];
                $query_data = array(
                    'amount' => $giftcert['amount'],
                    'debit' => 0
                );
                if ($old_data['amount'] <= $old_data['debit']) {
                    $query_data['debit'] = $giftcert["amount"];
                } elseif (($old_data['debit'] + $amount_diff) > 0) {
                    $query_data['debit'] = $old_data["debit"] + $amount_diff;
                }
                func_array2update('giftcerts', $query_data, "gcid='$giftcert[gcid]' AND orderid='$cart[orderid]'");
            }
        }
    }

    $orderid = $cart['orderid'];

    if (func_is_defined_module_sql_tbl('Subscriptions', 'subscription_customers')) {
        // Remove from subscription_customers table (for subscription products) deleted subscrition products

        foreach ($products as $product) {
            if ($product['deleted']) {
                db_query("DELETE FROM $sql_tbl[subscription_customers] WHERE productid='$product[productid]' AND orderid='$orderid'");
            }
        }

        // Insert into subscription_customers table (for subscription products)

        foreach ($products as $pk => $product) {
            if ($product['deleted']) {
                continue;
            }

            $result = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[subscription_customers] WHERE productid='$product[productid]' AND orderid='$orderid'");
            if ($result > 0) {
                continue;
            }

            if (!empty($active_modules['Subscriptions'])) {
                include $xcart_dir.'/modules/Subscriptions/subscriptions_cust.php';
            }
        }
    }
}

?>
