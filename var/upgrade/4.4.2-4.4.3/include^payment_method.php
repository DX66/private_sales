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
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: payment_method.php,v 1.95.2.1 2011/01/10 13:11:50 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (
    file_exists('../top.inc.php')
    && is_readable('../top.inc.php')
) {
    include_once '../top.inc.php';
}

if (!defined('XCART_START')) die("ERROR: Can not initiate application! Please check configuration.");

include_once $xcart_dir . '/payment/auth.php';

x_load(
    'cart',
    'user'
);

x_session_register('cart');
x_session_register('order_secureid');
x_session_register('intershipper_rates');
x_session_register('dhl_ext_country_store');

if (
    func_is_ajax_request()
    && $action = 'apply_gc'
) {
    return;
}

foreach (
    array(
        'card_expire_Year',
        'card_valid_from_Year',
    ) as $__year
) {
    if (isset($$__year) && strlen($$__year) > 0)
        $$__year = sprintf("%04d", intval($$__year));
}

foreach (
    array(
        'card_expire_Month',
        'card_valid_from_Month',
    ) as $__month
) {
    if (isset($$__month) && strlen($$__month) > 0)
        $$__month = sprintf('%02d', intval($$__month));
}

if (
    !isset($card_expire)
    && isset($card_expire_Month)
) {
    $card_expire = $card_expire_Month . substr($card_expire_Year, 2);
}

if (
    isset($card_valid_from_Month)
    && !empty($card_valid_from_Month)
) {
    $card_valid_from = $card_valid_from_Month . substr($card_valid_from_Year, 2);
}

if (
    isset($card_expire)
    && !empty($card_expire)
) {
    $_POST['card_expire'] = sprintf("%04d",intval($card_expire));
}

if (
    isset($card_valid_from)
    && !empty($card_valid_from)
) {
    $_POST['card_valid_from'] = sprintf("%04d", intval($card_valid_from));
}

if (!empty($logged_userid)) {

    $userinfo = func_userinfo($logged_userid, $login_type, false, true, array('C','H'));

    if (
        $mailchimp_subscription == 'Y'
        && !(
            empty($active_modules['Mailchimp_Subscription'])
            || empty($userinfo['email'])
        )
    ) {
        $mailchimp_response = func_mailchimp_subscribe($userinfo['email']);
    }

} else {

    $userinfo = func_userinfo(0, $login_type, false, true, array('C','H'));

}

/**
 * Use stored CC info instead of wildcarded one
 */
if ($config['General']['disable_cc'] != 'Y') {

    if (
        !empty($_POST['card_number'])
        && preg_match("/^\*+\d{4}$/", $_POST['card_number'])
    ) {
        $_POST['card_number'] = $card_number = $userinfo['card_number'];
    }

    if (
        !empty($_POST['card_cvv2'])
        && preg_match("/^\*+$/", $_POST['card_cvv2'])
    ) {
        $_POST['card_cvv2'] = $card_cvv2 = $userinfo['card_cvv2'];
    }

}

unset($userinfo['card_number']);

unset($userinfo['card_cvv2']);

// Check if customer accepted terms and conditions
if (
    !isset($_POST['accept_terms'])
    || empty($_POST['accept_terms'])
) {

    $top_message = array(
        'type'    => 'E',
        'content' => func_get_langvar_by_name('txt_accept_terms_err'),
    );

    func_header_location($xcart_catalogs['customer'] . '/cart.php?mode=checkout&paymentid=' . $paymentid);

}

/**
 * Get userinfo and cart products and output an error if empty
 */
if (
    empty($userinfo)
    || func_is_cart_empty($cart)
    || (
        empty($_POST['paymentid'])
        && $cart['total_cost'] > 0
    )
) {

    $top_message = array(
        'type'         => 'E',
        'content'     => func_get_langvar_by_name('err_payment_processor_msg')
    );

    func_header_location($xcart_catalogs['customer'] . "/cart.php?mode=checkout&paymentid=" . $paymentid);

}

// Check required fields
if (
    !empty($userinfo)
    && (
        $userinfo['status'] != 'A'
        || $is_anonymous
    )
    && !func_check_required_fields($userinfo)
) {
    $top_message = array(
        'type' => 'E',
        'content' => func_get_langvar_by_name('txt_registration_error')
    );
}

$payment_info = func_query_first("SELECT * FROM $sql_tbl[payment_methods] WHERE paymentid = '$paymentid'");

if (
    (
        $payment_info['active'] != 'Y'
        || $payment_info['payment_script'] != basename($php_url['url'])
    )
    && $cart['total_cost'] > 0
) {
    func_header_location($xcart_catalogs['customer'] . "/error_message.php?error_ccprocessor_baddata");
}

$userinfo = func_array_merge($userinfo, $_POST);

include_once $xcart_dir . '/include/cc_detect.php';

if (!empty($paymentid)) {

    if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[payment_methods] WHERE paymentid = '$paymentid' AND af_check = 'Y'")) {

        define('IS_AF_CHECK', true);

    }

}

// Only for compatibility with old code in payment modules.
// Please, use $cart['products'] in new code instead of $products.
if (
    !empty($cart['products'])
    && is_array($cart['products'])
) {

    $products = $cart['products'];

} else {

    $products = array();

}

$bill_firstname = empty($userinfo['b_firstname'])
    ? $userinfo['firstname']
    : $userinfo['b_firstname'];

$bill_lastname     = empty($userinfo['b_lastname'])
    ? $userinfo['lastname']
    : $userinfo['b_lastname'];

$bill_name         = $bill_firstname;

if (!empty($bill_lastname)) {
    $bill_name .= (
        empty($bill_firstname)
            ? ''
            : " "
    )
    . $bill_lastname;
}

$ship_firstname = empty($userinfo['s_firstname'])
    ? $userinfo['firstname']
    : $userinfo['s_firstname'];

$ship_lastname     = empty($userinfo['s_lastname'])
    ? $userinfo['lastname']
    : $userinfo['s_lastname'];

$ship_name         = $ship_firstname;

if (!empty($ship_lastname)) {
    $ship_name .= (
        empty($ship_firstname)
            ? ''
            : " "
    )
    . $ship_lastname;
}

// Check non existing/updated Gift certificate in the applied_giftcerts array
if (
    !empty($active_modules['Gift_Certificates'])
    && !empty($cart['applied_giftcerts'])
) {
    $_invalid_gcs = func_check_applied_giftcerts();

    if (
        !empty($_invalid_gcs)
        && is_array($_invalid_gcs)
    ) {
        $top_message = array(
            'type' => 'E',
            'content' => func_get_langvar_by_name('err_gc_invalid_gcs', array('invalid_gcs' => implode(', ', $_invalid_gcs)))
        );

        func_header_location($xcart_catalogs['customer'] . "/cart.php?mode=checkout&err=gc_notfound&paymentid=" . $paymentid);
    }
}

if (
    isset($cart['split_query'])
    && isset($cart['split_query']['paid_amount'])
) {

    // Paid amount must be substracted from Total cost
    $cart['total_cost'] -= $cart['split_query']['paid_amount'];

}

?>
