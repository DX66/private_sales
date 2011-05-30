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
 * CC processing payment module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: payment_cc.php,v 1.127.2.5 2011/03/10 09:01:03 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require '../include/payment_method.php';

x_load(
    'crypt',
    'order',
    'payment',
    'tests'
);

x_session_unregister('logged_paymentid');

if ($REQUEST_METHOD != 'POST') {

    func_header_location($current_location . DIR_CUSTOMER . "/cart.php?mode=checkout");

}

// Get parameters of the payment module
$module_params = func_query_first("SELECT * FROM " . $sql_tbl['ccprocessors'] . " where paymentid='" . $paymentid . "'");


if (
    $checkout_module == 'One_Page_Checkout'
    && isset($module_params['processor'])
    && $module_params['processor'] == 'ps_paypal_pro.php'
) {
    $paypal_express_paymentid = func_cart_get_paypal_express_id();

    if (
        $paypal_express_paymentid == $paymentid
        && !func_is_confirmed_paypal_express()
    )
        func_header_location($xcart_catalogs['customer']."/cart.php?mode=checkout&paymentid=".$paymentid);
}

// Check if the payment is processed using direct post
// of the cc data to a payment gateway
// (web-based method with ccinfo form enabled)
if (
    !empty($module_params)
    && func_is_direct_post_payment_method($module_params)
) {

    include_once $xcart_dir . '/include/payment_form.php';

} else {

    if (@$module_params['background'] == 'I') 
        $smarty->assign('use_iframe', 'Y');

    require_once $xcart_dir . '/include/payment_wait.php';

}

x_session_register('payment_cc_fields');

$is_paypal_pro = func_query_first_cell("SELECT COUNT(*) FROM " . $sql_tbl['payment_methods'] . " WHERE paymentid='" . $paymentid . "' AND processor_file='ps_paypal_pro.php'");

if ($is_paypal_pro) {

    $is_emulated_paypal = false;

    if (!empty($active_modules['XPayments_Connector'])) {

        func_xpay_func_load();

        $is_emulated_paypal = xpc_is_emulated_paypal($paymentid);

    }

    if ($is_emulated_paypal) {

        $payment_cc_data = xpc_get_module_params($paymentid);

    } else {

        $payment_cc_data = func_query_first("SELECT * FROM " . $sql_tbl['ccprocessors'] . " WHERE  processor = 'ps_paypal_pro.php'");

    }

} else {

    $payment_cc_data = func_query_first("SELECT * FROM " . $sql_tbl['ccprocessors'] . " WHERE paymentid='" . $paymentid . "'");

}

if (
    $paymentid == 1
    && !empty($config['card_types'])
) {

    foreach($config['card_types'] as $v) {

        if (
            $v['code'] != $card_type
            || empty($v['active'])
        ) {
            continue;
        }

        if (empty($v['cvv2'])) {

            unset($card_cvv2);

        }

        break;

    }

}

if (
    (
        (
            $paymentid == 1
            || @$payment_cc_data['type'] == 'C'
        )
        && @$payment_cc_data['disable_ccinfo'] != 'Y'
        && @$payment_cc_data['background'] == 'Y'
    )
    || (
        @$payment_cc_data['processor'] == 'ps_paypal_pro.php'
        && isset($card_name)
        && isset($card_type)
        && isset($card_number)
    )
) {

    if (
        empty($card_name)
        || empty($card_type)
        || empty($card_number)
        || empty($card_expire)
        || (
            empty($card_cvv2)
            && isset($card_cvv2)
        )
    ) {

        $payment_cc_fields = array(
            'card_name'             => $card_name,
            'card_type'             => $card_type,
            'card_number'           => $card_number,
            'card_expire'           => $card_expire,
            'card_expire_time'      => $card_expire
                ? mktime(0, 0, 0, $card_expire_Month, 1, $card_expire_Year)
                : '',
            'card_valid_from_time'  => $card_valid_from
                ? mktime(0, 0, 0, $card_valid_from_Month, 1, $card_valid_from_Year)
                : '',
            'card_cvv2'             => $card_cvv2,
        );

        $top_message['content'] = func_get_langvar_by_name("err_filling_form");
        $top_message['anchor']  = "ccinfo";
        $top_message['type']    = 'E';

        func_header_location($xcart_catalogs['customer']."/cart.php?mode=checkout&err=fields&paymentid=" . $paymentid);

    }

}

/**
 * Make order details
 */
$_order_details_rval = array();

foreach (func_order_details_fields() as $_details_field => $_field_label) {

    if (isset($GLOBALS[$_details_field])) {

        $_order_details_rval[] = $_field_label . ": " . stripslashes($GLOBALS[$_details_field]);

    }

}

$order_details = implode("\n", $_order_details_rval);

$customer_notes = $Customer_Notes;


if ($is_paypal_pro) {

    $module_params = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor='ps_paypal_pro.php'");

    $module_params['cmpi'] = in_array($config['paypal_solution'], array('uk', 'pro'))
        ? 'Y'
        : 'N';

} else {

    $module_params = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE paymentid='" . $paymentid . "'");

}

if (!empty($module_params['processor'])) {

    x_session_register('logged_paymentid');

    $logged_paymentid = $paymentid;

    // Get active processor's data and module parameters

    $duplicate = true;

    x_session_register('secure_oid');
    x_session_register('secure_oid_cost');
    x_session_register('initial_state_orders', array());
    x_session_register('initial_state_show_notif', 'Y');

    $current_cart_hash = func_calculate_cart_hash($cart);

    if (
        !empty($cart['split_query'])
        && $cart['total_cost'] <= 0
    ) {

        $top_message = array(
            'content'   => func_get_langvar_by_name('lbl_total_cost_less_than_paid_amount'),
            'type'      => 'I',
        );

        // if customer already paid amount more than total cost then (s)he MUST remove some transactions 
        func_header_location('cart.php?mode=checkout&paymentid=' . $paymentid);

    }

    if (
        !empty($cart['split_query'])
        && $cart['split_query']['cart_hash'] === $current_cart_hash
    ) {

        $orderids    = $cart['split_query']['orderid'];
        $paid_amount = $cart['split_query']['paid_amount'];

    } elseif (
        empty($secure_oid)
        || $secure_oid_cost != $cart['total_cost']
    ) {

        // Put order in table

        $orderids = func_place_order(
            stripslashes($payment_method) . " (" . $module_params['module_name'] . (get_cc_in_testmode($module_params) ? ", in test mode" : '') . ")",
            'I',
            $order_details,
            $customer_notes
        );

        if (
            is_null($orderids)
            || $orderids === false
        ) {

            $top_message = array(
                'content'   => func_get_langvar_by_name("err_product_in_cart_expired_msg"),
                'type'      => 'E',
            );

            func_header_location($xcart_catalogs['customer'] . "/cart.php?mode=checkout&paymentid=" . $paymentid);
        }

        $secure_oid      = $orderids;
        $secure_oid_cost = $cart['total_cost'];
        $duplicate       = false;

        $initial_state_orders     = func_array_merge($initial_state_orders, $orderids);
        $initial_state_show_notif = 'Y';

    } else {

        $orderids = $secure_oid;

    }

    func_split_checkout_check_decline_order($cart, $orderids);

    if (
        func_is_preauth_force_enabled($orderids)
        && $module_params['use_preauth'] != 'Y'
    ) {

        define('STATUS_CHANGE_REF', 6);

        func_change_order_status(
            $orderids,
            'Q',
            func_get_langvar_by_name('txt_antifraud_order_note', array(), $config['default_admin_language'], true)
        );

        x_session_register('cart');

        $cart = '';

        x_session_save();

        if ($store_cc) {

            $query_data = array(
                'card_name'         => $card_name,
                'card_type'         => $card_type,
                'card_number'       => addslashes(text_crypt($card_number)),
                'card_expire'       => $card_expire,
                'card_valid_from'   => $card_valid_from,
                'card_issue_no'     => $card_issue_no,
            );

            if ($store_cvv2) {

                $query_data['card_cvv2'] = addslashes(text_crypt($card_cvv2));

            }

            func_array2update(
                'customers',
                $query_data,
                "id='$logged_userid' AND usertype='$login_type'"
            );

            $query_data = array();

        }

        if (!empty($active_modules['SnS_connector'])) {

            func_generate_sns_action('CartChanged');

        }

        func_header_location($xcart_catalogs['customer'] . "/cart.php?mode=order_message&orderids=" . func_get_urlencoded_orderids($orderids));

        exit;

    }

    x_session_save();

    // Set CVV2 info line...
    $a = isset($userinfo['card_cvv2']) ? strlen($userinfo['card_cvv2']) : 0;

    $bill_output = array(
        'cvvmes' => ($a ? ($a . ' digit(s)') : 'not set') . ' / ',
    );

    func_pm_load(basename($module_params['processor']));

    if (
        $module_params['cmpi'] == 'Y'
        && file_exists($xcart_dir . '/payment/cmpi.php')
        && $config['CMPI']['cmpi_enabled'] == 'Y'
        && in_array($card_type, array('VISA', 'MC', 'JCB', 'SW'))
    ) {

        require $xcart_dir . '/payment/cmpi.php';

    } elseif ($module_params['cmpi'] == 'B') {

        require $xcart_dir . '/payment/3dsecure.php';

    }

    if ($module_params['background'] == 'I') {

        $smarty->assign('payment_method', $module_params['module_name']);

        func_flush(func_display('payments/iframe_init.tpl', $smarty, false));

    }

    if (
        isset($cart['split_query']['transaction_query'][$paymentid])
        && !empty($cart['split_query']['transaction_query'][$paymentid])
    ) {
        define('POSSIBLE_TRANSACTION_QUERY', true);
    }

    require $xcart_dir . '/payment/' . basename($module_params['processor']);

    require $xcart_dir . '/payment/payment_ccend.php';

} else {

    // Manual processing

    $orderids = func_place_order(
        stripslashes($payment_method) . " (manual processing)",
        'Q',
        $order_details,
        $customer_notes
    );

    if (
        is_null($orderids)
        || $orderids === false
    ) {

        $top_message = array(
            'content' => func_get_langvar_by_name("err_product_in_cart_expired_msg"),
            'type'    => 'E',
        );

        func_header_location($xcart_catalogs['customer']."/cart.php?mode=checkout&paymentid=".$paymentid);

    }

    $_orderids = func_get_urlencoded_orderids ($orderids);

    $cart = '';

    x_session_save();

    // If successful - Store CC number in database

    if ($store_cc) {

        $query_data = array(
            'card_name'       => $card_name,
            'card_type'       => $card_type,
            'card_number'     => addslashes(text_crypt($card_number)),
            'card_expire'     => $card_expire,
            'card_valid_from' => $card_valid_from,
            'card_issue_no'   => $card_issue_no,
        );

        if ($store_cvv2) {

            $query_data['card_cvv2'] = addslashes(text_crypt($card_cvv2));

        }

        func_array2update(
            'customers',
            $query_data,
            "id='$logged_userid' AND usertype='$login_type'"
        );

        $query_data = array();

    }

    if (!empty($active_modules['SnS_connector'])) {

        func_generate_sns_action('CartChanged');

    }

    func_header_location($xcart_catalogs['customer'] . "/cart.php?mode=order_message&orderids=$_orderids");

}

exit;

?>
