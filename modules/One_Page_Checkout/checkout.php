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
 * This script implements checkout facility for One Page Checkout module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage One Page Checkout
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: checkout.php,v 1.22.2.8 2011/03/07 08:09:44 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header('Location: ../../'); die('Access denied'); }

x_session_register('login_antibot_on');

$smarty->assign('allow_popup_login', empty($login_antibot_on));

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

    x_session_register('paypal_begin_express');

    if (!func_is_confirmed_paypal_express()) {

        $paypal_begin_express = true;

        func_header_location($current_location . '/payment/ps_paypal_pro.php?payment_id=' . $paymentid . '&mode=express');

    }

}

if (
    !empty($shipping)
    && (
        !isset($cart['shippingid'])
        || empty($cart['shippingid'])
    )
) {
    $cart['shippingid'] = $shipping[0]['shippingid'];
}

if ($cart['total_cost'] == 0) {
    x_session_unregister('paypal_begin_express');
}

$shippingid = (isset($shippingid) && !empty($shippingid))
    ? $shippingid
    : $cart['shippingid'];

/**
 * Prepare checkout details
 */


if (!empty($payment_cc_fields)) {
    $userinfo = func_array_merge($userinfo, $payment_cc_fields);
}

if ($checkout_step_modifier['payment_methods'] == 1) {
    $smarty->assign('ignore_payment_method_selection', 1);
}

if (!empty($payment_methods)) {
    x_load('paypal');

    foreach ($payment_methods as $k => $payment_data) {

        $payment_data['payment_script_url'] = (($payment_data['protocol'] == 'https' || $HTTPS) ? $https_location : $http_location) . '/payment/' . $payment_data['payment_script'];

        if ($payment_data['paymentid'] == $paymentid) {
            $smarty->assign('payment_script_url', $payment_data['payment_script_url']);
            $smarty->assign('payment_method',     $payment_data['payment_method']);
        }

        if ($payment_data['processor_file'] == 'ps_paypal_pro.php') {
            // Adjust cc_data and payment template for paypal
            list($payment_data) = func_paypal_adjust_payment_data($payment_data, 'One_Page_Checkout');
        }

        $payment_methods[$k] = $payment_data;
    }
}

$smarty->assign('cart', $cart);

if (!empty($paypal_express_details)) {
    $smarty->assign('paypal_express_selected', true);
    $smarty->assign('paypal_expressid', $paypal_expressid);
}

?>
