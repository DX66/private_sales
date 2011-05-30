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
 * Apply/unset discount coupons
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: discount_coupons.php,v 1.47.2.4 2011/03/01 09:26:23 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

if (empty($cart)) return;

x_load(
    'cart', 
    'category' // For Func_is_valid_coupon->Func_get_category_path function call stack
);

if (
    !empty($cart['discount_coupon'])
    && func_is_valid_coupon($cart['discount_coupon']) > 0
) {

    $cart['discount_coupon']     = '';
    $cart['coupon_type']         = '';

}

if ($mode == 'add_coupon') {
/**
 * Check if coupon is valid
 */
    if (empty($coupon))
        $coupon = '';

    $my_coupon = func_is_valid_coupon(stripslashes($coupon));

    // Bad coupon provider
    if ($my_coupon == 2) {

        $top_message['content'] = func_get_langvar_by_name("err_bad_coupon_provider_msg", false, false, true);

    // Coupon already used by this customer
    } elseif ($my_coupon == 5) {

        $top_message['content'] = func_get_langvar_by_name("txt_coupon_already_used_by_customer", false, false, true);

    // Overstepping of coupon order total
    } elseif ($my_coupon == 3) {

        $top_message['content'] = func_get_langvar_by_name("txt_overstepping_coupon_order_total", false, false, true);

    // Not found coupon target
    } elseif ($my_coupon == 4) {

        $top_message['content'] = func_get_langvar_by_name("txt_cart_not_contain_coupon_products", false, false, true);

    // Coupon is empty
    } elseif ($my_coupon == 7) {

        $top_message['content'] = func_get_langvar_by_name("lbl_dcoupon_code_is_empty", false, false, true);

    // Bad coupon or 'free_ship' coupon is applied while shipping is disabled
    } elseif (
        $my_coupon == 1
        || $my_coupon == 6
    ) {

        $top_message['content'] = func_get_langvar_by_name("err_bad_coupon_code_msg", false, false, true);

    // Add discount coupon
    } elseif ($my_coupon == 0) {

        $cart['discount_coupon'] = stripslashes($coupon);

        list($cart, $products) = func_generate_products_n_recalculate_cart();

        if ($cart['coupon_discount'] == 0 && $cart['coupon_type'] != 'free_ship') {

            $top_message['content'] = func_get_langvar_by_name("err_zero_coupon_discount_msg", false, false, true);

            $my_coupon = 1;

        }

    }

    if ($my_coupon > 0) {

        $cart['discount_coupon']     = '';
        $cart['coupon_type']         = '';

        $top_message['type'] = 'E';
    }

    func_register_ajax_message(
        'opcUpdateCall',
        array(
            'action'      => 'updateCoupon',
            'status'      => $my_coupon > 0 ? 0 : 1,
            'message'     => $top_message['content'],
            'update_ship' => $cart['coupon_type'] == 'free_ship' ? 1 : 0
        )
    );

    if (func_is_ajax_request()) {
        $top_message = '';

        if (func_cart_is_payment_methods_list_changed(@$payment_methods)) { 
            func_register_ajax_message(
                'opcUpdateCall',
                array(
                    'action' => 'paymentMethodListChanged'
                )
            );
        }
    }

    func_header_location('cart.php');

} elseif ($mode == 'unset_coupons') {

    func_register_ajax_message(
        'opcUpdateCall',
        array(
            'action'      => 'updateCoupon',
            'status'      => 1,
            'update_ship' => $cart['coupon_type'] == 'free_ship' ? 1 : 0
        )
    );

    $cart['discount_coupon']     = '';
    $cart['coupon_type']         = '';

    if (func_is_ajax_request()) {
        $top_message = '';


        if (func_cart_is_payment_methods_list_changed(@$payment_methods, 'run_func_calculate')) { 
            func_register_ajax_message(
                'opcUpdateCall',
                array(
                    'action' => 'paymentMethodListChanged'
                )
            );
        }

    }

    func_header_location('cart.php');
}

?>
