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
 * @version    $Id: checkout.php,v 1.22.2.3 2011/01/10 13:11:59 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header('Location: ../../'); die('Access denied'); }

x_session_register('login_antibot_on');

$smarty->assign('allow_popup_login', empty($login_antibot_on));

$paypal_expressid = func_query_first_cell("SELECT $sql_tbl[payment_methods].paymentid FROM $sql_tbl[payment_methods], $sql_tbl[ccprocessors] WHERE $sql_tbl[payment_methods].processor_file='ps_paypal_pro.php' AND $sql_tbl[payment_methods].processor_file=$sql_tbl[ccprocessors].processor AND $sql_tbl[payment_methods].paymentid=$sql_tbl[ccprocessors].paymentid AND $sql_tbl[payment_methods].active='Y'");

if (
    !empty($payment_methods)
    && (
        !isset($cart['paymentid'])
        || empty($cart['paymentid'])
    )
) {

    $_pm = $payment_methods[0]['paymentid'];

    if (
        empty($paypal_expressid)
        || $payment_methods[0]['paymentid'] != $paypal_expressid
        || count($payment_methods) == 1
    ) {

        $cart['paymentid'] = $payment_methods[0]['paymentid'];

    } else {
        
        $cart['paymentid'] = $payment_methods[1]['paymentid'];
    
    }
    
}

$paymentid = (isset($paymentid) && !empty($paymentid))
    ? $paymentid
    : $cart['paymentid'];

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

// Check if paymentid isn't fake
$is_egoods = $config['Egoods']['egoods_manual_cc_processing'] == 'Y'
    ? func_esd_in_cart($cart)
    : false;

$membershipid = $user_account['membershipid'];

$is_valid_paymentid = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[payment_methods] LEFT JOIN $sql_tbl[pmethod_memberships] ON $sql_tbl[pmethod_memberships].paymentid = $sql_tbl[payment_methods].paymentid WHERE $sql_tbl[payment_methods].paymentid='$paymentid'" . (($is_egoods && $paymentid == 1) ? '' : " AND $sql_tbl[payment_methods].active='Y'") . " AND ($sql_tbl[pmethod_memberships].membershipid IS NULL OR $sql_tbl[pmethod_memberships].membershipid = '$membershipid')");

if (!$is_valid_paymentid) {
    // To avoid circularity when $paymentid is not avalaible bt:#0095143
    $cart['paymentid'] = '';
    func_header_location('cart.php?mode=checkout&err=paymentid');
}


if (
    !isset($cart['paymentid'])
    || $cart['paymentid'] != $paymentid
) {
    $cart['paymentid'] = $paymentid;
    $payment_changed = true;
}

if (!empty($payment_cc_fields)) {
    $userinfo = func_array_merge($userinfo, $payment_cc_fields);
}

if ($checkout_step_modifier['payment_methods'] == 1) {
    $smarty->assign('ignore_payment_method_selection', 1);
}

if (!empty($payment_methods)) {

    foreach ($payment_methods as $k => $payment_data) {

        $payment_data['payment_script_url'] = (($payment_data['protocol'] == 'https' || $HTTPS) ? $https_location : $http_location) . '/payment/' . $payment_data['payment_script'];

        if ($payment_data['paymentid'] == $paymentid) {
            $smarty->assign('payment_script_url', $payment_data['payment_script_url']);
            $smarty->assign('payment_method',     $payment_data['payment_method']);
        }

        if ($payment_data['processor_file'] == 'ps_paypal_pro.php') {

            $_cc_data = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor='ps_paypal_pro.php'");
            func_unset($_cc_data, "paymentid", "processor");
            $payment_data = func_array_merge($payment_data, $_cc_data);
            $is_emulated_paypal = false;

            if ($active_modules['XPayments_Connector']) {
                func_xpay_func_load();
                $is_emulated_paypal = xpc_is_emulated_paypal($paymentid);
                if ($is_emulated_paypal) {
                    $payment_cc_data = xpc_get_module_params($paymentid);
                    $payment_data['payment_template'] = false;
                }
            }

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
