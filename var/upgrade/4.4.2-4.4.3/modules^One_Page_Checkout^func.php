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
 * Common functions for One Page Checkout Module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.php,v 1.23.2.5 2011/01/10 13:11:59 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header('Location: ../../'); die('Access denied'); }

/**
 * Gets profile edit form
 *
 * @return void
 * @see    ____func_see____
 */
function func_ajax_block_opc_profile()
{
    global $smarty, $config, $sql_tbl, $active_modules, $xcart_dir;
    global $logged_userid, $login_type, $login, $cart, $anonymous_userinfo, $is_anonymous, $user_account;
    global $xcart_catalogs, $xcart_catalogs_secure;

    $current_area = 'C';
    $main   = 'checkout';
    $mode   = 'update';

    $REQUEST_METHOD = 'GET';

    // Do not show the 'on_registration antibot image' for customers passed verification procedure
    $display_antibot = empty($login) && empty($anonymous_userinfo);

    include $xcart_dir . '/include/register.php';

    // Check if billing/shipping address section needed
    if (
        empty($userinfo['address'])
        || @$is_areas['B']
        && empty($userinfo['address']['B'])
        || @$is_areas['S']
        && empty($userinfo['address']['S'])
        || isset($_POST['edit_profile'])
    ) {
        $smarty->assign('need_address_info',    true);
        $smarty->assign('force_change_address', true);
        $smarty->assign('address_fields',       func_get_default_fields('H', 'address_book'));
    }

    $smarty->assign(
        'register_script_name',
        (
            ($config['Security']['use_https_login'] == 'Y')
                ? $xcart_catalogs_secure['customer'] . '/'
                : ''
        )
        . 'cart.php?mode=checkout'
    );

    return func_ajax_trim_div(func_display('modules/One_Page_Checkout/opc_profile.tpl', $smarty, false));
}

/**
 * Gets shipping methods block
 *
 * @return void
 * @see    ____func_see____
 */
function func_ajax_block_opc_shipping()
{
    global $smarty, $config, $sql_tbl, $active_modules, $xcart_dir;
    global $logged_userid, $login_type, $login, $cart, $userinfo, $is_anonymous, $user_account, $anonymous_userinfo;
    global $xcart_catalogs, $xcart_catalogs_secure, $current_area;
    global $current_carrier, $shop_language;
    global $intershipper_rates, $intershipper_recalc, $dhl_ext_country_store, $checkout_module, $empty_other_carriers, $empty_ups_carrier, $amazon_enabled, $paymentid;

    x_load(
        'cart',
        'shipping',
        'product',
        'user'
    );

    x_session_register('cart');

    $userinfo = func_userinfo($logged_userid, $login_type, false, false, 'H');

    x_session_register('cart');
    x_session_register('intershipper_rates');
    x_session_register('intershipper_recalc');
    x_session_register('current_carrier','UPS');
    x_session_register('dhl_ext_country_store');

    $intershipper_recalc = 'Y';

    // Prepare the products data
    $products = func_products_in_cart($cart, $userinfo['membershipid']);

    include $xcart_dir . '/include/cart_calculate_totals.php';

    $smarty->assign('userinfo', $userinfo);

    return func_ajax_trim_div(func_display('modules/One_Page_Checkout/opc_shipping.tpl', $smarty, false));
}

/**
 * Gets totals block
 *
 * @return void
 * @see    ____func_see____
 */
function func_ajax_block_opc_totals()
{
    global $smarty, $config, $sql_tbl, $active_modules, $xcart_dir;
    global $logged_userid, $login_type, $login, $cart, $userinfo, $is_anonymous, $user_account;
    global $xcart_catalogs, $xcart_catalogs_secure;
    global $current_carrier, $shop_language, $current_area, $checkout_module;
    global $intershipper_rates, $intershipper_recalc, $dhl_ext_country_store;

    x_load(
        'cart',
        'shipping',
        'product',
        'user'
    );

    x_session_register('cart');
    x_session_register('intershipper_rates');
    x_session_register('intershipper_recalc');
    x_session_register('current_carrier','UPS');
    x_session_register('dhl_ext_country_store');

    $userinfo = func_userinfo($logged_userid, $login_type, false, false, 'H');

    // Prepare the products data
    $products = func_products_in_cart($cart, $userinfo['membershipid']);

    $intershipper_recalc = 'Y';

    include $xcart_dir . '/include/cart_calculate_totals.php';

    $smarty->assign('userinfo',    $userinfo);
    $smarty->assign('products',    $products);

    return func_ajax_trim_div(func_display('modules/One_Page_Checkout/summary/cart_totals.tpl', $smarty, false));
}

/**
 * Gets authbox (greeting message) content
 *
 * @return void
 * @see    ____func_see____
 */
function func_ajax_block_opc_authbox()
{
    global $smarty;

    return func_ajax_trim_div(func_display('modules/One_Page_Checkout/opc_authbox.tpl', $smarty, false));
}

?>
