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
 * Gift certificate processing payment module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: payment_giftcert.php,v 1.66.2.3 2011/01/10 13:12:08 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require '../include/payment_method.php';

x_load(
    'cart',
    'order',
    'payment'
);

/**
 * Perform some checks of the applied Gift Certificate
 */
$err = false;
$gc_error_code = func_giftcert_check($gcid);

if ($gc_error_code == 1) {

    // Empty Gift certificate code
    $err     = 'fields';
    $errdesc = 'err_filling_form';

} elseif ($gc_error_code == 2) {

    // Gift certificate has already been applied

    $err     = 'gc_used';
    $errdesc = 'err_gc_used';

}

if (!$err) {

    $gc = func_giftcert_data($gcid, true);

    if (false === $gc) {

        // Non-existing Gift certificate

        $err     = 'gc_notfound';
        $errdesc = 'err_gc_error';

    } elseif (false === func_giftcert_apply($gc)) {

        // Not enough money - continue checkout

        $err     = 'gc_not_enough_money';
        $errdesc = 'txt_gc_not_enough_money';

    }
}

// Re-calculate cart totals

if (!$err) {

    $cart['applied_giftcerts'][count($cart['applied_giftcerts']) - 1]['giftcert_cost'] = $cart['total_cost'];

    $cart['giftcert_discount'] += $cart['total_cost'];

    $cart['total_cost'] = 0;

    if ($cart['orders']) {

        foreach($cart['orders'] as $k => $v) {

            $cart['orders'][$k]['total_cost'] = 0;

        }

    }

    $products = func_products_in_cart($cart, (!empty($userinfo['membershipid']) ? $userinfo['membershipid'] : 0));

    $cart = func_array_merge($cart, func_calculate($cart, $products, $logged_userid, $login_type));

}

if (
    $checkout_module == 'One_Page_Checkout'
    && func_is_ajax_request()
) {

    // Output errors / apply GC

    if ($err == 'gc_not_enough_money') {
        $err = false;
    }

    func_register_ajax_message(
        'opcUpdateCall',
        array(
            'action'    => 'updateGC',
            'gc_total'  => $cart['giftcert_discount'],
            'covered'   => $cart['total'] > 0 ? 0 : 1,
            'status'    => $err ? 0 : 1,
            'message'   => $errdesc
                ? func_get_langvar_by_name($errdesc, false, false, true)
                : null,
        )
    );

    x_session_save();

    func_ajax_finalize();

    exit;

} elseif ($err && $cart['total_cost'] > 0) {

    $top_message = array(
        'content' => func_get_langvar_by_name($errdesc),
        'type'    => 'E'
    );

    // Return to payment methods list
    if ($err == 'gc_not_enough_money')
        $paymentid = 0;

    $redirect = $xcart_catalogs['customer']
        . '/cart.php?mode=checkout'
        . '&paymentid=' . $paymentid
        . '&err=' . $err;

    func_header_location($redirect);
}

/**
 * Process order
 */
require_once $xcart_dir . '/include/payment_wait.php';

$customer_notes = $Customer_Notes;

$orderids = func_place_order(
    stripslashes($payment_method),
    'I',
    '', 
    $customer_notes
);

func_split_checkout_check_decline_order($cart, $orderids);

if (
    is_null($orderids)
    || $orderids === false
) {

    $top_message = array(
        'content' => func_get_langvar_by_name("err_product_in_cart_expired_msg"),
        'type'    => 'E',
    );

    func_header_location($xcart_catalogs['customer'] . "/cart.php?mode=checkout&paymentid=" . $paymentid);

}

define('STATUS_CHANGE_REF', 9);

func_change_order_status($orderids, 'P');

$_orderids = func_get_urlencoded_orderids ($orderids);

/**
 * Remove all from cart
 */

$cart = '';

if (!empty($active_modules['SnS_connector'])) {

    func_generate_sns_action('CartChanged');

}

func_header_location($xcart_catalogs['customer'] . "/cart.php?mode=order_message&orderids=" . $_orderids);

?>
