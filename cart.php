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
 * This script implements shopping cart facility
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Customer interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (C) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cart.php,v 1.211.2.15 2011/03/23 12:03:16 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './auth.php';

if (!empty($active_modules['Wishlist'])) {

    require $xcart_dir . '/modules/Wishlist/check_user.php';

}

x_load(
    'cart',
    'product'
);

x_session_register('cart');

/**
 * variables with integer type 
 */
foreach (
    array(
        'paymentid', 
        'transactionid', 
        'productid', 
        'shippingid', 
        'amount',
    ) as $int_var
) {
    if(isset($$int_var)) {
        $$int_var = intval($$int_var);
    }
}

if (
    'transaction_remove' === $mode
    && isset($cart['split_query'])
    && isset($cart['split_query']['transaction_query'][$paymentid][$transactionid])
) {

    $transaction = $cart['split_query']['transaction_query'][$paymentid][$transactionid];

    $payment_name = func_query_first_cell('SELECT processor FROM ' . $sql_tbl['ccprocessors'] . ' WHERE paymentid = \'' . $paymentid . '\'');
    $payment_name = preg_replace("/\.php/Ss", '', $payment_name);

    func_pm_load($payment_name);

    if (
        !empty($transaction['functions']['void'])
        && function_exists($transaction['functions']['void'])
    ) {

        $return = call_user_func_array($transaction['functions']['void'], $transaction['functions']['params_void']);

        // Void function MUST return TRUE value to remove transaction from the query
        if (true === $return) {

            unset($cart['split_query']['transaction_query'][$paymentid][$transactionid]);

            func_store_split_checkout_data($cart['split_query']);

        }

    }

    func_header_location('cart.php?mode=checkout&paymentid=' . $paymentid_return);
}

/**
 * Check if the cart is empty
 */
$func_is_cart_empty = func_is_cart_empty($cart);

require $xcart_dir . '/include/checkout_init.php';

/**
 * Normalize cart content
 */
if (
    !$func_is_cart_empty
    && !in_array($mode, array('wishlist', 'wl2cart'))
) {
    $cart_changed = func_cart_normalize($cart);
}

/**
 * Clear entire cart
 */
if ($mode == 'clear_cart') {

    $changes = array();

    if (!empty($cart['products'])) {

        foreach($cart['products'] as $p) {

            $changes[$p['cartid']] = array(
                'productid' => $p['productid'],
                'quantity'  => $p['amount'],
                'change'    => $p['amount'] * (-1),
            );

        }

    }

    if (!empty($cart['giftcerts'])) {

        foreach($cart['giftcerts'] as $gid => $g) {

            $changes[$gid] = array(
                'gcindex'  => $gid,
                'quantity' => 1,
                'change'   => -1,
            );

        }

    }

    if (
        !empty($active_modules['SnS_connector'])
        && !empty($cart['products'])
    ) {

        foreach ($cart['products'] as $p) {

            $is_sns_action['DeleteFromCart'][] = $p['productid'];

        }

    }

    $cart = '';

    func_register_ajax_message(
        'cartChanged',
        array(
            'changes' => $changes,
            'isEmpty' => true,
            'status'  => 1
        )
    );

    $cart_checksum = '';
    func_header_location('cart.php');
}

/**
 * Unset Gift Certificate
 */
if (
    !empty($active_modules['Gift_Certificates'])
    && $mode == 'unset_gc'
    && $gcid
) {
    func_giftcert_unset($gcid);

    func_register_ajax_message(
        'opcUpdateCall',
        array(
            'action'   => 'updateGC',
            'status'   => 1,
            'gc_total' => $cart['giftcert_discount']
        )
    );

    if (
        func_is_ajax_request()
        && func_cart_is_payment_methods_list_changed(@$payment_methods)
    ) {
        func_register_ajax_message(
            'opcUpdateCall',
            array(
                'action' => 'paymentMethodListChanged'
            )
        );
    }

    func_header_location('cart.php?mode=checkout' . ($paymentid ? '&paymentid=' . $paymentid : ''));
}

/**
 * Update Gift wrapping
 */
include $xcart_dir . '/modules/Gift_Registry/giftreg_customer_cart.php';

$smarty->assign(
    'register_script_name',
    (
        ($config['Security']['use_https_login'] == 'Y')
            ? $xcart_catalogs_secure['customer'] . '/'
            : ''
    )
    . 'cart.php'
);

$return_url = '';

/**
 * Add product to cart
 */
if (
    $mode == 'add'
    && !empty($productid)
) {
    $add_product = array(
        'productid'       => abs($productid),
        'amount'          => abs($amount),
        'product_options' => isset($product_options) ? $product_options : array(),
        'price'           => isset($price) ? func_convert_number($price) : null,
    );

    if (!empty($active_modules['Special_Offers'])) {

        include $xcart_dir . '/modules/Special_Offers/add_to_cart.php';

    }

    $result = func_add_to_cart($cart, $add_product);

    if (
        !isset($_GET['redirect_to_referer'])
        && !empty($result['redirect_to'])
    ) {
        func_header_location($result['redirect_to']);
    }

    // Recalculate cart totals after new item added
    list($cart, $products) = func_generate_products_n_recalculate_cart();

    if (!empty($active_modules['Special_Offers'])) {

        include $xcart_dir . '/modules/Special_Offers/add_free_products.php';

    }

    if (isset($_GET['redirect_to_referer'])) {

        func_header_location($HTTP_REFERER . '&redirect_from_cart=Y');

    }

    $func_is_cart_empty = func_is_cart_empty($cart);

    func_register_ajax_message(
        'cartChanged',
        array(
            'changes'     => array(
                $result['productindex'] => array(
                    'productid' => $add_product['productid'],
                    'quantity'  => $result['quantity'],
                    'changed'   => $result['changed'],
                )
            ),
            'isEmpty' => empty($cart['products']) && empty($cart['giftcerts']),
            'status'  => $result['status'],
        )
    );

    // Redirect
    if (
        $config['General']['redirect_to_cart'] == 'Y'
        || isset($_GET['redirect_to_cart'])
    ) {

        if (!empty($active_modules['SnS_connector']))
            $is_sns_action['AddToCart'][] = $productid;

        $return_url = 'cart.php';

    } else {

        list($cart, $products) = func_generate_products_n_recalculate_cart(); 

        if (!empty($active_modules['SnS_connector']))
            func_generate_sns_action('AddToCart', $productid);

        func_save_customer_cart();

        if (func_is_internal_url($HTTP_REFERER)) {

            $tmp = @parse_url($HTTP_REFERER);
            $return_url = $HTTP_REFERER;

            if (
                $config['General']['return_to_dynamic_part'] == 'Y'
                && !empty($is_submit_from_html_page)
                && is_array($tmp)
                && (
                    strpos($tmp['path'], '.html') !== false
                    || substr($tmp['path'], -1) == '/'
                )
            ) {

                if (substr($tmp['path'], -1) == '/') {

                    $return_url = 'home.php';

                } elseif (strpos($HTTP_REFERER, '-c-') !== false) {

                    $return_url = func_get_resource_url('category', $cat, 'page=' . $page);

                } else {

                    $return_url = func_get_resource_url('product', $add_product['productid']);

                }

            }

        } elseif(!empty($cat)) {

            $return_url = func_get_resource_url('category', $cat, 'page=' . $page);

        }

    }

}

/**
 * Delete product from the cart
 */
if (
    $mode == 'delete'
    && !empty($productindex)
) {
    $productid = 0;
    $quantity = 0;

    if (
        !empty($cart['products'])
        && is_array($cart['products'])
    ) {
        list($productid, $quantity) = func_delete_from_cart($cart, $productindex);
    }

    if ($productid > 0) {

        if (!empty($active_modules['SnS_connector']))
            $is_sns_action['DeleteFromCart'][] = $productid;

        // Recalculate cart totals after updating
        list($cart, $products) = func_generate_products_n_recalculate_cart();
    }

    func_register_ajax_message(
        'cartChanged',
        array(
            'changes' => array(
                $productindex   => array(
                    'productid' => $productid,
                    'quantity'  => $quantity,
                    'changed'   => $quantity * -1
                )
            ),
            'isEmpty' => empty($cart['products']) && empty($cart['giftcerts']),
            'status'  => $productid > 0 ? 1 : 2
        )
    );

    $func_is_cart_empty = func_is_cart_empty($cart);

    $return_url = 'cart.php';
}

if (empty($action)) {

    $action = '';

}

$changes = false;

/**
 * Update shopping cart
 */
if (
    $action == 'update'
    && !$func_is_cart_empty
) {
    // Update Gift registry links, if any
    if (!empty($active_modules['Gift_Registry'])) {

        include $xcart_dir . '/modules/Gift_Registry/giftreg_customer_cart.php';

    }

    // Update quantity of products in cart
    if (!empty($productindexes)) {

        list($min_amount_warns, $changes) = func_update_quantity_in_cart($cart, $productindexes);

        $top_message = func_generate_min_amount_warning($min_amount_warns, $productindexes, $cart['products']);

        if (!empty($active_modules['SnS_connector']))
            $is_sns_action['CartChanged'][] = false;

        $intershipper_recalc = 'Y';
    }

    // Update shipping method
    if (
        $config['Shipping']['realtime_shipping'] == 'Y'
        && !empty($active_modules['UPS_OnLine_Tools'])
        && $config['Shipping']['use_intershipper'] != 'Y'
        && isset($selected_carrier)
    ) {
        $current_carrier = $selected_carrier;
    }

    if (
        !empty($shippingid)
        && $cart['shippingid'] != $shippingid
    ) {

        $cart = func_cart_set_shippingid($cart, $shippingid);

        func_register_ajax_message(
            'opcUpdateCall',
            array(
                'action' => 'shippingChanged',
                'value'  => $shippingid
            )
        );
    }

    if (!empty($cart['is_payment_changed'])) {

        $cart['is_payment_changed'] = false;

        func_register_ajax_message(
            'opcUpdateCall',
            array(
                'action' => 'paymentChanged',
                'value'  => $paymentid
            )
        );
    }

    if (!empty($mode)) {

        $url_args[] = 'mode=' . $mode;

    }

    if (
        !empty($paymentid)
        && $checkout_module != 'One_Page_Checkout' // Paymentid is stored in cart['paymentid'] for One_Page_Checkout
    ) {

        $url_args[] = 'paymentid=' . $paymentid;

    }

    $return_url = 'cart.php' . (!empty($url_args) ? '?' . implode('&', $url_args) : '');

    func_register_ajax_message(
        'cartChanged',
        array(
            'changes' => is_array($changes) ? $changes : array(),
            'isEmpty' => empty($cart['products']) && empty($cart['giftcerts']),
            'status'  => 1,
        )
    );

    $func_is_cart_empty = func_is_cart_empty($cart);
}

// Prepare the products data

$products = func_products_in_cart($cart, @$userinfo['membershipid']);

/**
 * Apply / unset discount coupon
 */

if (!empty($active_modules['Discount_Coupons'])) {

    include $xcart_dir . '/modules/Discount_Coupons/discount_coupons.php';

}

/**
 * Calculate totals
 */

include $xcart_dir . '/include/cart_calculate_totals.php';

if (!empty($active_modules['SnS_connector'])) {
    func_sns_exec_actions($is_sns_action);
}

if (
    isset($totals_checksum)
    && $totals_checksum_init !== $totals_checksum
) {

    func_register_ajax_message(
        'opcUpdateCall',
        array(
            'action' => 'updateTotals'
        )
    );

    if (
        func_is_ajax_request()
        && func_cart_is_payment_methods_list_changed(@$payment_methods)
    ) {
        func_register_ajax_message(
            'opcUpdateCall',
            array(
                'action' => 'paymentMethodListChanged'
            )
        );
    }

}

if ($return_url) {

    func_header_location($return_url);

}

/**
 * Wishlist facility
 */
if (
    !empty($active_modules['Wishlist'])
    && $mode != 'checkout'
) {
    if (
        $mode == 'move_product'
        && !empty($active_modules['Gift_Registry'])
    ) {
        include $xcart_dir . '/modules/Gift_Registry/giftreg_wishlist.php';
    }

    include $xcart_dir . '/modules/Wishlist/wishlist.php';
}

if (
    $mode != 'wishlist'
    || empty($active_modules['Wishlist'])
) {
    $location[] = ($mode == 'checkout')
        ? array(func_get_langvar_by_name('lbl_checkout'), '')
        : array(func_get_langvar_by_name('lbl_your_shopping_cart'), '');
}

// Include common scripts
include $xcart_dir . '/include/common.php';

$giftcerts = !empty($cart['giftcerts'])
    ? $cart['giftcerts']
    : array();

// Update minicart
include $xcart_dir . '/minicart.php';

if (
    isset($userinfo)
    && !empty($userinfo)
) {
    $smarty->assign('userinfo', $userinfo);
}

if (!$func_is_cart_empty) {
    $smarty->assign('products',        $products);
    $smarty->assign('giftcerts',       $giftcerts);
    $smarty->assign('list_length',     count($products) + count($giftcerts));
    $smarty->assign('products_length', count($products));
}

func_save_customer_cart();

if (
    !$func_is_cart_empty
    && $mode == 'checkout'
) {
    require $xcart_dir . '/include/checkout.php';
}

x_session_save();

if (
    !isset($main)
    || empty($main)
) {
    $main = 'cart';
}

$smarty->assign('main', $main);

if (!$func_is_cart_empty) {

    if ($main == 'cart' || $main == 'checkout') {

        $page_container_class .= ' ' . 'checkout-container';
        $smarty->assign('page_container_class', $page_container_class);

        if ($main != 'checkout') {
            $smarty->assign('shipping_estimate_fields', $shipping_estimate_fields);
        }
    }

} else {

    $smarty->assign('cart_empty', true);

}

// Assign the current location line
$smarty->assign('location', $location);

func_display('customer/home.tpl', $smarty);

?>
