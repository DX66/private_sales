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
 * This script implements checkout facility for Fast Lane Checkout module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: checkout.php,v 1.25.2.10 2011/03/07 08:09:44 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!empty($paymentid)) {

    if (
        empty($_GET['paymentid'])
        || empty($_GET['mode'])
    ) {
        func_header_location('cart.php?mode=checkout&paymentid=' . $paymentid);
    }

    /**
     * Prepare the last step of checkout
     */
    x_load('cart'); 
    $paypal_expressid = func_cart_get_paypal_express_id();

    $paymentid = func_cart_get_paymentid($cart, $checkout_module);
    if (!func_is_valid_payment_method($paymentid)) {
        $cart = func_cart_set_paymentid($cart, 0);
        $top_message['content'] = func_get_langvar_by_name('err_payment_cc_not_available');
        $top_message['type']    = 'E';
        func_header_location('cart.php');
    } else {
        $cart = func_cart_set_paymentid($cart, $paymentid);
    }

    if (
        !empty($paypal_expressid)
        && $paypal_expressid == $paymentid
    ) {

        if (
            !empty($active_modules['Fast_Lane_Checkout'])
            && empty($shipping)
            && $need_shipping
            && $config['Shipping']['enable_shipping'] == 'Y'
        ) {

            $top_message['content'] = func_get_langvar_by_name('msg_flc_select_shipping_err');
            $top_message['type'] = 'E';

            func_header_location('cart.php?mode=checkout');

        }

        x_session_register('paypal_begin_express');

        if (!func_is_confirmed_paypal_express()) {

            $paypal_begin_express = true;

            func_header_location($current_location . '/payment/ps_paypal_pro.php?payment_id=' . $paymentid . '&mode=express');

        }

    }

    // Show payment details checkout page
    $payment_cc_data = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE paymentid='$paymentid'");

    // Generate payment script URL depending on HTTP/HTTPS settings
    if (empty($cart['shippingid'])) {
        $payment_data = func_query_first("SELECT $sql_tbl[payment_methods].*, $sql_tbl[payment_methods].payment_method as payment_method_orig, IFNULL(l1.value, $sql_tbl[payment_methods].payment_method) as payment_method, IFNULL(l2.value, $sql_tbl[payment_methods].payment_details) as payment_details FROM $sql_tbl[payment_methods] LEFT JOIN $sql_tbl[languages_alt] as l1 ON l1.name = CONCAT('payment_method_', $sql_tbl[payment_methods].paymentid) AND l1.code = '$shop_language' LEFT JOIN $sql_tbl[languages_alt] as l2 ON l2.name = CONCAT('payment_details_', $sql_tbl[payment_methods].paymentid) AND l2.code = '$shop_language' WHERE $sql_tbl[payment_methods].paymentid='$paymentid'");

    } else {
        $payment_data = func_query_first("SELECT $sql_tbl[payment_methods].*, $sql_tbl[payment_methods].payment_method as payment_method_orig, IFNULL(l1.value, $sql_tbl[payment_methods].payment_method) as payment_method, IFNULL(l2.value, $sql_tbl[payment_methods].payment_details) as payment_details FROM $sql_tbl[payment_methods] LEFT JOIN $sql_tbl[languages_alt] as l1 ON l1.name = CONCAT('payment_method_', $sql_tbl[payment_methods].paymentid) AND l1.code = '$shop_language' LEFT JOIN $sql_tbl[languages_alt] as l2 ON l2.name = CONCAT('payment_details_', $sql_tbl[payment_methods].paymentid) AND l2.code = '$shop_language' LEFT JOIN $sql_tbl[shipping] ON $sql_tbl[shipping].shippingid = '$cart[shippingid]' WHERE $sql_tbl[payment_methods].paymentid='$paymentid' AND ($sql_tbl[payment_methods].is_cod <> 'Y' || $sql_tbl[shipping].is_cod = 'Y')");
    }

    if (empty($payment_data)) {
        func_header_location('cart.php?mode=checkout');
    }

    $payment_data['payment_script_url'] = (($payment_data['protocol'] == 'https' || $HTTPS) ? $https_location : $http_location) . '/payment/' . $payment_data['payment_script'];

    if (!empty($payment_cc_fields)) {
        $userinfo = func_array_merge($userinfo, $payment_cc_fields);
    }

    if ($checkout_step_modifier['payment_methods'] == 1)
        $smarty->assign('ignore_payment_method_selection', 1);

    $checkout_step = 2 + $checkout_step_modifier['anonymous'] - $checkout_step_modifier['payment_methods'];

    if ($payment_data['processor_file'] == 'ps_paypal_pro.php') {
        x_load('paypal');
        // Adjust cc_data and payment template for paypal
        $payment_data['paymentid'] = $paymentid;
        list($payment_data, $payment_cc_data) = func_paypal_adjust_payment_data($payment_data, 'Fast_Lane_Checkout');
    }

    // Check if only one shipping method is available and hide link on the checkout page

    if (
        count($shipping) > 1
        || (
            count($shipping) == 1
            && $carriers_count > 1
        )
    ) {
        $smarty->assign('change_shipping_link', 'Y');
    }

    $payment_data['module_params'] = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE paymentid = '$payment_data[paymentid]'");

    $location[] = array(func_get_langvar_by_name('lbl_payment_details'), '');

} else {

    // Prepare the page for payment method selection

    $force_change_shipping = (
        count($shipping) > 1
        || (
            count($shipping) == 1
            && $carriers_count > 1
            || (
                $need_shipping
                && empty($shipping)
            )
        )
    );

    if (
        is_array($payment_methods)
        && count($payment_methods) == 1
        && !$force_change_shipping
    ) {
        // Skip payment method selection if only one method is available
        func_header_location('cart.php?paymentid=' . @$payment_methods[0]['paymentid'] . '&mode=checkout');
    }

    if (!empty($payment_methods)) {
        $payment_methods[0]['is_default'] = 1;
    }

    $checkout_step = 1 + $checkout_step_modifier['anonymous'] - $checkout_step_modifier['payment_methods'];

    $smarty->assign('payment_methods', $payment_methods);

    $location[] = array(func_get_langvar_by_name('lbl_payment_details'), '');

}

/**
 * Tabs array for displaying on the checkout
 */

$checkout_tabs = array(
    'cart'     => array(
        'title'     => func_get_langvar_by_name('lbl_your_cart'),
        'link'         => 'cart.php'
    ),
    'account' => array(
        'title'     => func_get_langvar_by_name('lbl_personal_details'),
        'link'         => 'cart.php?mode=checkout&edit_profile&paymentid=' . $paymentid
    ),
    'method' => array(
        'title'     => func_get_langvar_by_name('lbl_shipping_and_payment'),
        'link'         => 'cart.php?mode=checkout'
    ),
    'place' => array(
        'title'     => func_get_langvar_by_name('lbl_place_order'),
        'link'         => 'cart.php?paymentid=' . $paymentid . '&mode=checkout'
    )
);

$checkout_tabs_hash = array(
    0 => 'account',
    1 => 'account',
    2 => 'method',
    3 => 'place'
);

if (
    $mode == 'checkout'
    && !empty($paymentid)
) {

    if (
        empty($shipping)
        && (
            !empty($login)
            || (
                $config['General']['enable_anonymous_checkout'] == 'Y'
                && !empty($userinfo)
            )
        )
        && $need_shipping
        && $config['Shipping']['enable_shipping'] == 'Y'
    ) {

        $top_message['content'] = func_get_langvar_by_name('msg_flc_select_shipping_err');
        $top_message['type']     = 'E';

        $checkout_step = 2;

        func_header_location('cart.php?mode=checkout');

    } else {

        $checkout_step = 3;

    }

} else {

    $checkout_step = 2;

}

x_load('user');
$_anonymous_userinfo = func_get_anonymous_userinfo();
if (
    empty($login)
    && empty($_anonymous_userinfo)
) {

    $checkout_step = 0;

} elseif (
    isset($edit_profile)
) {

    $checkout_step = 1;

}

if (
    !empty($payment_methods)
    && (
        !$need_shipping
        || count($shipping) == 1
        && $carriers_count == 1
    )
) {
    if (count($payment_methods) <= 1) {

        unset($checkout_tabs['method']);

    }
}

if ($checkout_step >= 0) {

    $checkout_tabs[$checkout_tabs_hash[$checkout_step]]['selected'] = true;

    if (
        is_array($location)
        && $checkout_step != 1
    ) {
        unset($location[count($location)-1]);
    }

    $selected_idx = 0;

    foreach ($checkout_tabs as $k => $v) {
        if (!empty($v['selected']))
            break;

        $selected_idx++;
    }

    $idx = 0;

    foreach ($checkout_tabs as $k => $v) {

        if ($selected_idx > $idx) {

            $checkout_tabs[$k]['selected_before'] = true;
            $checkout_tabs[$k]['selected_after'] = true;

        } elseif ($selected_idx == $idx) {

            $checkout_tabs[$k]['selected_before'] = true;

        }

        $idx++;

    }

}

$smarty->assign('checkout_tabs',       $checkout_tabs);
$smarty->assign('checkout_tabs_count', count($checkout_tabs));
$smarty->assign('checkout_step',       $checkout_step);

// Assign the current location line
$smarty->assign('location', $location);

?>
