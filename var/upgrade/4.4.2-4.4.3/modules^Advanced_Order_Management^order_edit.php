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
 * Order editing interface
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: order_edit.php,v 1.138.2.1 2011/01/10 13:11:54 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

require $xcart_dir.'/modules/Advanced_Order_Management/func.edit.php';

x_load('shipping');

$location[count($location)-1][1] = "order.php?orderid=$orderid";
$location[] = array(func_get_langvar_by_name('lbl_advanced_order_management'), '');

$global_store = array();

x_session_register('intershipper_rates');
x_session_register('intershipper_recalc');
x_session_register('current_carrier','UPS');
x_session_register('dhl_ext_country_store');

if (isset($dhl_ext_country)) {
    $dhl_ext_country_store = $dhl_ext_country;
} else {
    $dhl_ext_country = $dhl_ext_country_store;
}

/**
 * This flag enables the taxes recalculation if customer profile is modified
 */
$real_taxes = 'Y';

if ($real_taxes == 'Y' && !defined('XAOM'))
    define ('XAOM', 1);

$intershipper_recalc = 'Y';

if ($mode != 'edit' || empty($active_modules['Advanced_Order_Management']))
    func_403(39);

if (!defined('IS_ADMIN_USER')) {
    func_403(40);
}

$smarty->assign(
    'default_fields',
    func_get_default_fields('H')
);

$smarty->assign(
    'address_fields',
    func_get_default_fields('H', 'address_book')
);

/**
 * The modification of order is permitted
 */
if (!$single_mode) {  // in PRO mode
    $providers_array = array();
    foreach ($order_data['products'] as $k=>$v) {
        $providers_array[$v['provider']]++;
    }
    if (count($providers_array) > 1) {
        $smarty->assign('rejected', 'Y');
        $smarty->assign('main','order_edit');
        if (
            file_exists($xcart_dir.'/modules/gold_display.php')
            && is_readable($xcart_dir.'/modules/gold_display.php')
        ) {
            include $xcart_dir.'/modules/gold_display.php';
        }
        func_display('admin/home.tpl',$smarty);
    }
}

/**
 * Get show mode and assign it to Smarty
 */
if (!isset($show) || !in_array(strtolower($show), array('preview','products','giftcerts','customer','totals'))) {
    $show = 'preview';
    if ($action != 'save')
        $initial_point = true;
}
$smarty->assign('show', $show);

if (in_array($order_data['order']['extra']['tax_info']['display_taxed_order_totals'], array('Y','N'))) {
    $config['Taxes']['display_taxed_order_totals'] = $order_data['order']['extra']['tax_info']['display_taxed_order_totals'];
    $smarty->assign('config', $config);
}
/**
 * Register temporary order in the session
 */
if (!x_session_is_registered('cart_tmp') || $initial_point) {
    $cart_tmp = $order_data['order'];
    $cart_tmp['orders'][0] = $order_data['order'];
    $cart_tmp['total_cost'] = $cart_tmp['total'];
    $cart_tmp['giftcerts'] = $order_data['giftcerts'];
    $cart_tmp['products'] = $order_data['products'];
    $cart_tmp['userinfo'] = $order_data['userinfo'];
    $cart_tmp['discount_coupon'] = $order_data['order']['coupon'];
    $cart_tmp['use_discount_alt'] = 'Y';
    $cart_tmp['discount_alt'] = $order_data['order']['discount'];

    if (empty($cart_tmp['extra']['discount_info'])) {
        $cart_tmp['extra']['discount_info'] = array(
                    'discount' => $order_data['order']['discount'],
                    'discount_type' => 'absolute'
                );
    }

    $cart_tmp['use_shipping_cost_alt'] = 'Y';
    $cart_tmp['shipping_cost_alt'] = $cart_tmp['shipping_cost'];
    if (func_query_first_cell("SELECT code FROM $sql_tbl[shipping] WHERE shippingid='$cart_tmp[shippingid]'") != 'UPS')
        $current_carrier = '';

    if (is_array($cart_tmp['products'])) {
        foreach ($cart_tmp['products'] as $k => $v) {
            $cart_tmp['products'][$k]['free_price'] = $v['price'];
            $cart_tmp['products'][$k]['price'] = $v['display_price'];
            if (!empty($v['extra_data']['taxes']) && is_array($v['extra_data']['taxes'])) {
                foreach ($v['extra_data']['taxes'] as $_tax) {
                    if (($_tax['price_includes_tax'] == 'Y' || $_tax['display_including_tax'] == 'Y') && $config["Taxes"]["display_taxed_order_totals"] == 'Y')
                        $cart_tmp['products'][$k]['price'] -= price_format($_tax['tax_value_precise']);
                }
            }
            $cart_tmp['products'][$k]['taxed_price'] = $v['display_price'];
            if ($v['product_type'] == 'C') {
                $cart_tmp['products'][$k]['options_surcharge'] = $v['price'];
            }
            if (!empty($active_modules['Product_Options'])) {
                $cart_tmp['products'][$k]['keep_options'] = 'Y';
            }
        }
    }
}

x_session_register('cart_tmp', $cart_tmp);

if ($action == 'delete') {
/**
 * Update order info in the database
 */
    if ($REQUEST_METHOD == 'POST' && $confirmed == 'Y') {
        func_delete_order($orderid);
        $show = 'preview';
        x_session_unregister('cart_tmp');
        func_header_location('orders.php');
    }
    else {
        $show = 'preview';
        $smarty->assign('confirmation', 'Y');
        $smarty->assign('confirm_deletion', 'Y');
    }
}
elseif ($action == 'save') {
/**
 * Update order info in the database
 */
    if ($REQUEST_METHOD == 'POST' && $confirmed == 'Y') {
        func_update_order($cart_tmp, $order_data);

        // Write changes to the history

        $old_order_data = $order_data;
        $order_data = func_order_data($orderid);

        $diff = func_aom_prepare_diff('A', $order_data, $old_order_data, $cart_tmp);
        $details = array(
            'old_status' => $old_order_data['order']['status'],
            'new_status' => $order_data['order']['status'],
            'diff' => $diff,
            'comment' => stripslashes($history_comment),
            'is_public' => $history_is_public,
            'is_edit' => true
        );
        func_aom_save_history($orderid, 'A', $details);

        $show = 'preview';
        x_session_register('message');
        $message = 'saved';
        x_session_unregister('cart_tmp');

        $notify = ($notify_customer || $notify_provider || $notify_orders_dept);
        if ($notify && !empty($order_data)) {
            $mail_smarty->assign('products',$order_data['products']);
            $mail_smarty->assign('giftcerts',$order_data['giftcerts']);
            $mail_smarty->assign('userinfo',$order_data['userinfo']);
            $mail_smarty->assign('order',$order_data['order']);

            // Send notification to customer
            if ($notify_customer) {
                func_send_mail($order_data['userinfo']['email'], 'mail/order_updated_customer_subj.tpl', 'mail/order_updated_customer.tpl', $config['Company']['orders_department'], false);
            }

            // Send notification to Orders department
            if ($notify_orders_dept) {
                func_send_mail($config['Company']['orders_department'], 'mail/order_updated_subj.tpl', 'mail/order_updated.tpl', $config['Company']['site_administrator'], false);
            }

            // Send notification to provider
            if ($notify_provider) {
                $provider_data = func_query_first("SELECT email, language FROM $sql_tbl[customers] WHERE id='".$order_data["products"][0]["provider"]."'");
                if (!empty($provider_data)) {
                    list($email_pro, $to_customer) = array_values($provider_data);
                }
                if (!empty($email_pro) && !($notify_orders_dept && $email_pro == $config['Company']['orders_department'])) {
                    if(empty($to_customer))
                        $to_customer = $config['default_admin_language'];

                    func_send_mail($email_pro, 'mail/order_updated_subj.tpl', 'mail/order_updated.tpl', $config['Company']['orders_department'], false);
                }
            }
        }

        func_header_location("order.php?orderid=$orderid&mode=edit");
    }
    else {
        $show = 'preview';
        $smarty->assign('confirmation', 'Y');
    }
}
elseif ($action == 'cancel') {
/**
 * Cancel order modifications and go to preview
 */
    $show = 'preview';
    $smarty->assign('message', 'cancel');
    x_session_unregister('cart_tmp');
}

if (x_session_is_registered('message')) {
    x_session_register('message');
    $smarty->assign('message', $message);
    x_session_unregister('message');
}

$customer_membershipid = $cart_tmp['userinfo']['membershipid'];

/**
 * Process and update orders data
 */
if ($REQUEST_METHOD == 'POST') {

    $cart_tmp['flag_change'] = true;

    if ($action == 'update_products') {
        if (is_array($product_details)) {
            foreach ($product_details as $k => $v) {

            // Update ordered product details

                $productid = $cart_tmp['products'][$k]['productid'];
                $v['amount'] = intval($v['amount']);

                // Check if product is out of stock
                $count_product_in_stock = func_get_quantity_in_stock($productid, $order_data['order']['status'], $v['product_options'], @$order_data["products"][$k]);
                if ($v['amount'] > 0) {
                    if ($config['General']['unlimited_products'] == 'Y'|| $v['amount'] <= $count_product_in_stock) {
                        $cart_tmp['products'][$k]['amount'] = $v['amount'];
                    } elseif ($cart_tmp['products'][$k]['amount'] > $count_product_in_stock && $count_product_in_stock > 0) {
                        $cart_tmp['products'][$k]['amount'] = $count_product_in_stock;
                    }
                }

                // Mark (unmark) product as 'deleted'
                if (!empty($v['delete']) && $v['delete'] == $productid) {
                    $cart_tmp['products'][$k]['deleted'] = ($cart_tmp['products'][$k]['deleted'] ? false : true);
                    continue;
                }

                $v['price'] = func_aom_validate_price($v['price']);

                $product_options_result = array();

                if (!empty($active_modules['Product_Options']) && ( !empty($v['product_options']) || $v['keep_options'])) {

                    // Update product options selected

                    if ($v['keep_options'] == 'Y') {
                        // Keep originally selected options
                        $product_options_result = $cart_tmp['products'][$k]['product_options'] = $products[$k]['product_options'];
                        $v['product_options'] = $products[$k]['extra_data']['product_options'];
                    } else {
                        // Process selected options
                        if(!func_check_product_options ($productid, $v['product_options']))
                            $v['product_options'] = func_get_default_options($productid, $v['amount'], $cart_tmp['userinfo']['membershipid']);
                        list($variant, $product_options_result) = func_get_product_options_data($productid, $v['product_options'], $cart_tmp['userinfo']['membershipid']);
                    }

                    $cart_tmp['products'][$k]['options_surcharge'] = 0;
                    if (is_array($product_options_result)) {
                        foreach($product_options_result as $key=>$o)
                            $cart_tmp['products'][$k]['options_surcharge'] += ($o['modifier_type'] == '%'?($v['price']*$o['price_modifier']/100):$o['price_modifier']);
                    }

                    if (!empty($variant) && !empty($variant['productcode']) && $variant['productid'] == $cart_tmp['products'][$k]['productid']) {
                        $cart_tmp['products'][$k]['productcode'] = $variant['productcode'];
                        $cart_tmp['products'][$k]['variantid'] = $variant['variantid'];
                    }

                    if ($all_languages && is_array($all_languages) && count($all_languages) > 1) {
                        if ($v['keep_options'] != 'Y') {
                            foreach($all_languages as $lng)
                                $product_options_alt_result[$lng['code']] = func_serialize_options($v['product_options'], false, $lng['code']);
                        } else {
                            $product_options_alt_result = (isset($cart_tmp['products'][$k]['extra_data']['product_options_alt'])) ? $cart_tmp['products'][$k]['extra_data']['product_options_alt'] : array();
                        }
                    }

                    $product_options_txt = $product_options_alt_result[$shop_language] ? $product_options_alt_result[$shop_language] : func_serialize_options($v['product_options'], false);
                }

                if ($cart_tmp['products'][$k]['product_type'] == 'C')
                    $cart_tmp['products'][$k]['options_surcharge'] = $v['price'];

                $cart_tmp['products'][$k]['price'] = $v['price'];
                $cart_tmp['products'][$k]['free_price'] = $v['price'];
                $cart_tmp['products'][$k]['product_options'] = $product_options_result;
                $cart_tmp['products'][$k]['product_options_txt'] = $product_options_txt;
                if (!empty($product_options_txt))
                    $cart_tmp['products'][$k]['force_product_options_txt'] = true;
                $cart_tmp['products'][$k]['extra_data']["product_options"] = $v["product_options"];
                $cart_tmp['products'][$k]['extra_data']["product_options_alt"] = $product_options_alt_result;
                $cart_tmp['products'][$k]['stock_update'] = ($v['stock_update']) ? 'Y' : 'N';
                $cart_tmp['products'][$k]['keep_options'] = $v['keep_options'];
            }
        }

        if (!empty($newproductid) && is_numeric($newproductid)) {
            $saved_data = compact('login', 'login_type', 'logged_userid', 'current_area', 'user_account');
            $login = $cart_tmp['userinfo']['login'];
            $user_account = $cart_tmp['userinfo'];
            $logged_userid = $cart_tmp['userinfo']['id'];
            $login_type = 'C';
            $current_area = 'C';
            if ($prd = func_select_product($newproductid, $customer_membershipid, false, false, true)) {
                if (!$single_mode && is_array($cart_tmp['products'])) {
                    $_providers = array();
                    foreach ($cart_tmp['products'] as $_product)
                        $_providers[$_product['provider']] = 1;
                    if (!in_array($prd['provider'], array_keys($_providers))) {
                        extract($saved_data);
                        $top_message['content'] = func_get_langvar_by_name('txt_aom_product_provider_pro');
                        $top_message['type'] = 'E';
                        func_header_location("order.php?orderid=$orderid&mode=edit&show=products");
                    }
                }
                if ($prd['avail'] <= 0 && $config['General']['unlimited_products'] == 'N') {
                    extract($saved_data);
                    $top_message['content'] = func_get_langvar_by_name('txt_aom_product_is_out_of_stock');
                    $top_message['type'] = 'E';
                    func_header_location("order.php?orderid=$orderid&mode=edit&show=products");
                }

                $prd['catalog_price'] = $prd['price'];

                if ($active_modules['Product_Options']) {
                    $prd['extra_data']["product_options"] = func_get_default_options($newproductid, 1, $cart_tmp["userinfo"]['membershipid']);
                    list($variant, $product_options_result) = func_get_product_options_data($newproductid, $prd['extra_data']["product_options"], $cart_tmp["userinfo"]['membershipid']);
                    $surcharge = 0;
                    $prd['product_options'] = $product_options_result;
                    if($product_options_result) {
                        foreach($product_options_result as $key=>$o)
                            $surcharge += ($o['modifier_type'] == '%'?($prd['price']*$o['price_modifier']/100):$o['price_modifier']);
                    }
                    if (!empty($variant) && !empty($variant['productcode']) && $variant['productid'] == $cart_tmp['products'][$k]['productid']) {
                        $cart_tmp['products'][$k]['productcode'] = $variant['productcode'];
                        $cart_tmp['products'][$k]['variantid'] = $variant['variantid'];
                        $cart_tmp['products'][$k]['catalog_price'] = $prd['price'] = $variant['price'];
                    }

                    $prd['price'] = price_format($prd['price'] + $surcharge);
                }
                $prd['amount'] = 1;
                $prd['new'] = true;
                $cart_tmp['products'][] = $prd;
                unset($prd);

            } else {
                extract($saved_data);
                $top_message['content'] = func_get_langvar_by_name('txt_aom_product_cannot_be_added');
                $top_message['type'] = 'E';
                func_header_location("order.php?orderid=$orderid&mode=edit&show=products");
            }
            extract($saved_data);
        }

        func_header_location("order.php?orderid=$orderid&mode=edit&show=products");
    }
    elseif ($action == 'update_giftcerts') {
        if (is_array($giftcert_details)) {
            foreach ($giftcert_details as $k=>$v) {

                if (!empty($v['delete']) && $v['delete'] == $cart_tmp['giftcerts'][$k]['gcid']) {
                // Delete or restore Gift Certificate in order
                    $cart_tmp['giftcerts'][$k]['deleted'] = ($cart_tmp['giftcerts'][$k]['deleted'] ? false : true);
                    continue;
                }

                $v['amount'] = func_convert_number($v['amount']);
                $cart_tmp['giftcerts'][$k]['amount'] = $v['amount'];
            }
        }
        func_header_location("order.php?orderid=$orderid&mode=edit&show=giftcerts");
    }
    elseif ($action == 'update_customer') {
        if (is_array($customer_info)) {
            $cart_tmp['userinfo'] = func_array_merge($cart_tmp['userinfo'], func_array_map('stripslashes', $customer_info));
            $cart_tmp['userinfo']['title'] = func_get_title($cart_tmp['userinfo']['titleid']);
            $cart_tmp['userinfo']['b_title'] = func_get_title($cart_tmp['userinfo']['b_titleid']);
            $cart_tmp['userinfo']['s_title'] = func_get_title($cart_tmp['userinfo']['s_titleid']);
        }
        if(is_array($additional_fields)) {
            $cart_tmp['userinfo']['additional_fields'] = $additional_fields;
        }
        func_header_location("order.php?orderid=$orderid&mode=edit&show=customer");
    }
    elseif ($action == 'update_totals') {

        if ($config['Shipping']['realtime_shipping'] == 'Y' && !empty($active_modules['UPS_OnLine_Tools']) && $config['Shipping']['use_intershipper'] != 'Y')
            $current_carrier = $selected_carrier;

        if (empty($total_details['shipping_cost_alt'])) $total_details['shipping_cost_alt'] = '0.00';
        if (empty($total_details['discount_alt'])) $total_details['discount_alt'] = '0.00';
        if (empty($total_details['coupon_discount_alt'])) $total_details['coupon_discount_alt'] = '0.00';

        if (!empty($total_details['use_shipping_cost_alt']) && !empty($total_details['shipping_cost_alt'])) {
        // Use fixed shipping cost
            $total_details['shipping_cost_alt'] = func_aom_validate_price($total_details['shipping_cost_alt']);
            $cart_tmp['shipping_cost'] = $total_details['shipping_cost_alt'];
            $cart_tmp['shipping_cost_alt'] = $total_details['shipping_cost_alt'];
            $cart_tmp['use_shipping_cost_alt'] = 'Y';
        }
        else {
            unset($cart_tmp['use_shipping_cost_alt']);
        }

        if (!empty($total_details['use_discount_alt']) && !empty($total_details['discount_alt'])) {
        // Use alt discount
            $cart_tmp['discount_alt'] = $cart_tmp['discount'] = $total_details['discount_alt'] = func_aom_validate_price($total_details['discount_alt']);
            $cart_tmp['use_discount_alt'] = 'Y';
            $cart_tmp['extra']['discount_info']['discount'] = price_format($total_details['discount_alt']);
            $cart_tmp['extra']['discount_info']['discount_type'] = $total_details['discount_type_alt'];
        } else {
            unset($cart_tmp['use_discount_alt']);
        }

        if (!empty($total_details['use_coupon_discount_alt']) && !empty($total_details['coupon_discount_alt'])) {

            // Use alt coupon discount
            $cart_tmp['__last_coupon'] = $cart_tmp['coupon'];
            $cart_tmp['coupon_discount_alt'] = $cart_tmp['coupon_discount'] = $total_details['coupon_discount_alt'] = func_aom_validate_price($total_details['coupon_discount_alt']);
            $cart_tmp['use_coupon_discount_alt'] = 'Y';
            $cart_tmp['discount_coupon'] = $cart_tmp['coupon'] = "Order#".$cart_tmp['orderid'];
            $cart_tmp['use_old_coupon_discount'] = false;

        } elseif (!empty($total_details['coupon_alt'])) {

            func_unset($cart_tmp, 'use_coupon_discount_alt', 'coupon_discount_alt', 'coupon_discount', 'discount_coupon', 'coupon', 'use_old_coupon_discount');

            if ($total_details['coupon_alt'] == '__old_coupon__') {

                // Use old deleted coupon
                $cart_tmp['discount_coupon'] = $cart_tmp['coupon'] = $order_data['order']['coupon'];
                $cart_tmp['use_coupon_discount_alt'] = $order_data['order']['coupon_type'] == "free_ship" ? "N" : "Y";
                $cart_tmp['use_old_coupon_discount'] = true;
                $cart_tmp['coupon_discount_alt'] = $cart_tmp['coupon_discount'] = $total_details['coupon_discount_alt'] = $order_data['order']['coupon_discount'];

            } else {

                // Use exists coupon
                $cart_tmp['discount_coupon'] = $cart_tmp['coupon'] = stripslashes($total_details['coupon_alt']);
            }
        } elseif (empty($total_details['coupon_alt'])) {
            func_unset($cart_tmp,'coupon','discount_coupon','coupon_discount');
        }

        if ($total_details['use_payment_alt']) {
            $_payment_method = explode(":::", $total_details['payment_alt']);
            $cart_tmp['payment_method'] = $_payment_method[1];
            $cart_tmp['paymentid'] = $_payment_method[0];
        }
        else {
            $cart_tmp['payment_method'] = $total_details['payment_method'];
        }

        $cart_tmp['shippingid'] = $total_details['shippingid'];
        $cart_tmp['shipping'] = func_query_first_cell("SELECT shipping FROM $sql_tbl[shipping] WHERE shippingid='".$total_details["shippingid"]."'");
        func_header_location("order.php?orderid=$orderid&mode=edit&show=totals");
    }

    func_header_location("order.php?orderid=$orderid&mode=edit&show=preview");

}

if ($show == 'products') {
/**
 * Get the products info
 */

    if (!empty($active_modules['Product_Options'])) {
        $ids = array();
        $options_markups = array();
        foreach($cart_tmp['products'] as $pk => $product) {
            $ids[] = $product['productid'];
        }

        if (!empty($ids))
            $options_markups = func_get_default_options_markup_list($ids);
    }

    foreach ($cart_tmp['products'] as $pk => $product) {

        $productid = $product['productid'];

        // Check if the product was not deleted from the db
        // and get the current price and amount

        if ($product['is_deleted'] != 'Y') {
            $cart_tmp['products'][$pk]['items_in_stock'] = func_get_quantity_in_stock($productid, $order_data['order']['status'], $options, @$order_data['products'][$pk]);
            $cart_tmp['products'][$pk]['catalog_price'] = func_query_first_cell("SELECT $sql_tbl[pricing].price FROM $sql_tbl[pricing], $sql_tbl[quick_prices] WHERE $sql_tbl[quick_prices].productid = '$productid' AND $sql_tbl[quick_prices].priceid = $sql_tbl[pricing].priceid AND $sql_tbl[quick_prices].membershipid IN ('$customer_membershipid', '0')");
        }

        // Update product options with selected values

        if (!empty($active_modules['Product_Options'])) {

            if (!empty($options_markups[$productid]))
                $cart_tmp['products'][$pk]['catalog_price'] += $options_markups[$productid];

            $options = $product['extra_data']['product_options'];

            $old_user_account = $user_account;
            $user_account = $userinfo = $cart_tmp['userinfo'];
            $old_current_area = $current_area;
            $current_area = 'C';

            include $xcart_dir.'/modules/Product_Options/customer_options.php';

            // Check if the options were changed or deleted
            // since order placement (last edit)
            $orig_options = $products[$pk]['extra_data']['product_options'];
            $adv_opt_choice = 'N';

            if (!isset($product['new']) || $product['new'] !== true) {
                // Compare original and currently selected options
                if (
                    !func_check_product_options($productid, $orig_options) ||
                    ( !empty($orig_options) && empty($product_options) ) ||
                    ( empty($orig_options) && !empty($product_options) ) ||
                    ( count($orig_options) != count($product_options) )
                )
                {
                    $adv_opt_choice = 'Y';
                }
            }

            $cart_tmp['products'][$pk]['adv_option_choice'] = $adv_opt_choice;

            $user_account = $old_user_account;
            $current_area = $old_current_area;
            $cart_tmp['products'][$pk]['display_options'] = $product_options;

            // Correct catalog price
            if (!empty($variants) && !empty($cart_tmp['products'][$pk]['variantid'])) {
                $vid = $cart_tmp['products'][$pk]['variantid'];
                $cart_tmp['products'][$pk]['catalog_price'] = $variants[$vid]['price'];
            }

        }
    } // /foreach

    // Select the product configurations for displaying

    $pconf_found = false;
    foreach ($cart_tmp['products'] as $k=>$v) {
        if (!empty($v['extra_data']['pconf']['cartid'])) {
            $cartids_[$v['extra_data']['pconf']['cartid']] = "#$v[productid]. $v[product]";
            $pconf_found = true;
        }
    }
    if ($pconf_found) {
        foreach ($cart_tmp['products'] as $k=>$v) {
            if (!empty($v['extra_data']['pconf']['parent']))
                $cart_tmp['products'][$k]['pconf_parent'] = $cartids_[$v['extra_data']['pconf']['parent']];
        }
    }

    $smarty->assign('total_products', count($cart_tmp['products']));
    $smarty->assign('cart_products', $cart_tmp['products']);
    $smarty->assign('orig_products', $products);
} // /if ($show == 'products')

if ($show == 'giftcerts') {
/**
 * Get the ordered gift certificates info
 */
    $smarty->assign('cart_giftcerts', $cart_tmp['giftcerts']);
    $smarty->assign('orig_giftcerts', $giftcerts);
}

if ($show == 'customer') {
/**
 * Get the ordered customer info
 */
    include_once $xcart_dir.'/include/countries.php';
    include_once $xcart_dir.'/include/states.php';
    $smarty->assign('cart_customer', $cart_tmp['userinfo']);
    $smarty->assign('membership_levels', func_get_memberships('C', true));
}

/**
 * Get the allowed shipping methods list
 */

if ($real_taxes == 'Y') {
/**
 * Calculate taxes etc depending on the current store settings
 */
    global $current_area, $logged_userid, $user_account;
    $_saved_data = compact('current_area', 'login', 'logged_userid', 'user_account');
    $current_area = 'C';
    $login = $cart_tmp['userinfo']['login'];
    $user_account = $cart_tmp['userinfo'];
    $logged_userid = $cart_tmp['userinfo']['id'];
}

$shipping_cost_alt_old = $cart_tmp['use_shipping_cost_alt'];
$cart_tmp['use_shipping_cost_alt'] = '';

// Initialization global var for func_calculate_single bt:0095797
foreach ($cart_tmp['products'] as $k => $v) {
    $global_store['product_taxes'][$v['productid']] = $v['extra_data']['taxes'];
}

// Global tax names for func_get_product_tax_rate function bt:0096284
if (is_array($order['applied_taxes'])) {
    foreach ($order['applied_taxes'] as $k => $v) {
        $global_store['tax_display_names'][$k] = func_get_order_tax_name($v);
    }
}

$shipping = func_get_shipping_methods_list($cart_tmp, $cart_tmp['products'], $cart_tmp['userinfo']);
$cart_tmp['use_shipping_cost_alt'] = $shipping_cost_alt_old;

if (!empty($_saved_data))
    extract($_saved_data);

if (is_array($shipping)) {
    $found = false;
    foreach($shipping as $k=>$v)
        if ($order_data['order']['shippingid'] == $v['shippingid']) {
            $found = true;
            break;
        }
    if (!$found && empty($order_data['order']['shippingid'])) {
    }
    else {
        if (!$found && $cart_tmp['shippingid'] == $order_data['order']['shippingid']) {
            $cart_tmp['shippingid'] = $shipping[0]['shippingid'];
            $cart_tmp['shipping'] = $shipping[0]['shipping'];
        }
        if (!$found)
            $smarty->assign('shipping_lost', $shipping);
    }
}

if ($show == 'totals') {

    // Get the allowed payment methods list

    $payment_methods = func_query("SELECT $sql_tbl[payment_methods].paymentid, $sql_tbl[payment_methods].payment_method FROM $sql_tbl[payment_methods] LEFT JOIN $sql_tbl[pmethod_memberships] ON $sql_tbl[payment_methods].paymentid = $sql_tbl[pmethod_memberships].paymentid WHERE $sql_tbl[payment_methods].active='Y' AND ($sql_tbl[pmethod_memberships].membershipid = '$customer_membershipid' OR $sql_tbl[pmethod_memberships].membershipid IS NULL) ORDER BY $sql_tbl[payment_methods].orderby");

    $cart_tmp = func_recalculate_totals($cart_tmp);

    $config['Appearance']['allow_update_quantity_in_cart'] = 'N';
    $smarty->assign('config', $config);
    $smarty->assign('payment_methods', $payment_methods);
    $smarty->assign('shipping', @$shipping);
    $smarty->assign('cart_order', $cart_tmp);
    $smarty->assign('orig_order', $order_data['order']);
    $smarty->assign('cart', $cart_tmp);
    $smarty->assign('list_length', count(@$cart_tmp['products']) + count(@$cart_tmp['giftcerts']));
    $smarty->assign('products_length', count(@$cart_tmp['products']));
}

if ($show == 'preview') {
    $cart_tmp = func_recalculate_totals($cart_tmp);
    if ($initial_point) {

    // Replace some fields by original values

        $fields_to_orig = array('shipping_cost', 'total', 'tax', 'discount', 'coupon_discount');
        foreach ($fields_to_orig as $k=>$v)
            if ($order[$v] != $cart_tmp[$v])
                $cart_tmp[$v] = $order[$v];
    }
    $smarty->assign('products', $cart_tmp['products']);
    $smarty->assign('giftcerts', $cart_tmp['giftcerts']);
    $smarty->assign('customer', $cart_tmp['userinfo']);
}

$empty_order = true;
if (is_array($cart_tmp['products'])) {
    foreach ($cart_tmp['products'] as $product) {
        if (!$product['deleted']) {
            $empty_order = false;
            break;
        }
    }
}
if (is_array($cart_tmp['giftcerts'])) {
    foreach ($cart_tmp['giftcerts'] as $gc) {
        if (!$gc['deleted']) {
            $empty_order = false;
            break;
        }
    }
}

$smarty->assign('empty_order', $empty_order);

x_session_save('cart_tmp');

$smarty->assign('order', $cart_tmp);

$smarty->assign('has_giftcerts', !empty($cart_tmp['giftcerts']) ? 'Y' : '');

$smarty->assign('current_carrier', $current_carrier);

$smarty->assign('titles', func_get_titles());

$coupons = func_query("SELECT * FROM $sql_tbl[discount_coupons] WHERE coupon_type != 'free_ship' ORDER BY coupon");
if (!empty($order_data['order']['coupon']) && !func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[discount_coupons] WHERE coupon_type != 'free_ship' AND coupon = '".addslashes($order_data['order']['coupon'])."'")) {
    $coupons[] = array(
        'coupon' => $order_data['order']['coupon'],
        'discount' => $order_data['order']['coupon_discount'],
        'coupon_type' => $order_data['order']['coupon_type'],
        '__deleted' => true
    );
}

if (!empty($coupons)) {
    $smarty->assign('coupons', $coupons);
}

if (!empty($dhl_ext_country))
    $smarty->assign('dhl_ext_country', $dhl_ext_country);
if (!empty($dhl_ext_countries))
    $smarty->assign('dhl_ext_countries', $dhl_ext_countries);

/**
 * Set Smarty template to display
 */
$smarty->assign('main','order_edit');
?>
