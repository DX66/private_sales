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
 * This script implements checkout facility
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Cart
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: checkout.php,v 1.24.2.2 2011/01/10 13:11:48 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header('Location: ../'); die('Access denied'); }

// Common checkout code goes here

if (
    $cart['display_subtotal'] < $config['General']['minimal_order_amount']
    && $config['General']['minimal_order_amount'] > 0
) {

    // ERROR: Cart total must exceeds the minimum order total amount (defined in General settings)

    func_header_location('error_message.php?error_min_order');
}

if (
    $config['General']['maximum_order_amount'] > 0
    && $cart['display_subtotal'] > $config['General']['maximum_order_amount']
) {

    // ERROR: Cart total must not exceed the maximum order total amount
    // (defined in General settings)

    func_header_location('error_message.php?error_max_order');
}

if (
    $config['General']['maximum_order_items'] > 0
    && func_cart_count_items($cart) > $config['General']['maximum_order_items']
) {

    // ERROR: Cart total must not exceed the maximum total quantity
    // of products in an order (defined in General settings)

    func_header_location('error_message.php?error_max_items');
}

$smarty->assign('partner', $partner);

if (
    empty($login)
    && $config['General']['enable_anonymous_checkout'] == 'Y'
) {
    // Anonymous checkout
    $smarty->assign('anonymous', 'Y');
}

// Count available shipping carriers
$carriers_count = 0;

if (
    isset($_carriers)
    && is_array($_carriers)
    && isset($_carriers['UPS'])
    && isset($_carriers['other'])
) {
    $carriers_count = $_carriers['UPS'] + $_carriers['other'];
}

// Generate uniq orderid which will identify order session
$order_secureid = md5(uniqid(rand()));

if (
    !empty($active_modules['Google_Analytics'])
    && $config['Google_Analytics']['ganalytics_e_commerce_analysis'] == 'Y'
) {
    $ga_track_commerce = 'Y';
}

x_session_register('login_antibot_on');

if ($login_antibot_on) {
    // Show antibot image after 3 unsucceful attempts to login
    $smarty->assign('login_antibot_on', $login_antibot_on);
}

// Do not show the 'on_registration antibot image' for customers passed verification procedure
$display_antibot = empty($login) && empty($anonymous_userinfo);
$smarty->assign('display_antibot', $display_antibot);

define('CHECKOUT_STARTED', 1);

include $xcart_dir . '/modules/' . $checkout_module . '/checkout.php';

$smarty->assign('paymentid',        $paymentid);
$smarty->assign('payment_cc_data',  @$payment_cc_data);
$smarty->assign('payment_data',     $payment_data);
$smarty->assign('userinfo',         $userinfo);
$smarty->assign('main',             'checkout');
$smarty->assign('payment_methods',  $payment_methods);

?>
