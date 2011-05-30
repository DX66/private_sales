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
 * @version    $Id: func.php,v 1.23.2.11 2011/04/18 07:42:06 aim Exp $
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
    global $logged_userid, $login_type, $login, $cart, $is_anonymous, $user_account;
    global $xcart_catalogs, $xcart_catalogs_secure;
    
    // To fix PHP Notice: Undefined variable
    global $av_error, $intershipper_recalc, $secure_oid, $saved_address_book, $saved_userinfo, $reg_error, $antibot_reg_err, $identifiers, $submode, $shop_language, $usertype;

    $current_area = 'C';
    $main   = 'checkout';
    $mode   = 'update';

    $REQUEST_METHOD = 'GET';

    // Do not show the 'on_registration antibot image' for customers passed verification procedure
    x_load('user');
    $_anonymous_userinfo = func_get_anonymous_userinfo();
    $display_antibot = empty($login) && empty($_anonymous_userinfo);

    include $xcart_dir . '/include/register.php';

    // Check if billing/shipping address section needed
    if (
        empty($userinfo['address']) #nolint The var is defined in include/register.php
        || @$is_areas['B'] #nolint The var is defined in include/register.php
        && empty($userinfo['address']['B']) #nolint 
        || @$is_areas['S'] #nolint
        && empty($userinfo['address']['S']) #nolint
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

    if (
        empty($login)
        && $config['General']['enable_anonymous_checkout'] == 'Y'
    ) {
        // Anonymous checkout
        $smarty->assign('anonymous', 'Y');
    }


    $check_smarty_vars = array('XCARTSESSID', 'XCARTSESSNAME', 'anonymous', 'display_antibot', 'reg_antibot_err', 'reg_error', 'show_antibot', 'ship2diff', 'address_fields', 'hide_header', 'login_field_name', 'membership_levels', 'show_passwd_note', 'force_uk_ccinfo', 'is_cc_required');
    func_assign_smarty_vars($check_smarty_vars);

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
    global $logged_userid, $login_type, $login, $cart, $userinfo, $is_anonymous, $user_account;
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
    $products = func_products_in_cart($cart, @$userinfo['membershipid']);

    include $xcart_dir . '/include/cart_calculate_totals.php';

    $check_smarty_vars = array('arb_account_used', 'checkout_module', 'is_other_carriers_empty', 'is_ups_carrier_empty', 'need_shipping', 'shipping_calc_error', 'shipping_calc_service', 'main', 'current_carrier', 'show_carriers_selector', 'dhl_ext_countries', 'has_active_arb_smethods', 'dhl_ext_country');
    func_assign_smarty_vars($check_smarty_vars);
    $smarty->assign('main', 'checkout');
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
    $products = func_products_in_cart($cart, @$userinfo['membershipid']);

    $intershipper_recalc = 'Y';

    include $xcart_dir . '/include/cart_calculate_totals.php';

    $check_smarty_vars = array('zero', 'transaction_query', 'shipping_cost', 'reg_error', 'paid_amount', 'need_shipping', 'minicart_total_items', 'force_change_address', 'paymentid', 'need_alt_currency');
    func_assign_smarty_vars($check_smarty_vars);
    $smarty->assign('main', 'checkout');


    $smarty->assign('userinfo',    $userinfo);
    $smarty->assign('products',    $products);
    $smarty->assign('cart_totals_standalone', true);

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

    $check_smarty_vars = array('fullname', 'login');
    func_assign_smarty_vars($check_smarty_vars);

    return func_ajax_trim_div(func_display('modules/One_Page_Checkout/opc_authbox.tpl', $smarty, false));
}

/*
* Return css class which should be used for user field on the One Page Checkout page.
* Used in One_Page_Checkout/profile/address_fields.tpl and One_Page_Checkout/profile/address_fields.tpl templates
*/                                                                   
function func_tpl_get_user_field_cssclass($current_field, $default_fields)
{                                                                    
    global $smarty;                                                  

    $fields_group = array('zipcode','phone','title','firstname','lastname');
    
    if (in_array($current_field, $fields_group)) {

        // Find next field after the current one
        $current_is_found = $next_field = false;
        foreach($default_fields as $key=>$field) {
            if ($field['avail'] != 'Y')
                continue;

            if ($current_is_found) {
                // Step 2. Find next
                $next_field = $key;
                break;
            }                

            if ($key == $current_field) {
                // Step 1. Find current
                $current_is_found = true;
            } 
        }

        if (in_array($next_field, $fields_group)) {
            $current_class = 'fields-group';
        } else {
            $current_class = 'fields-group last';
        }
    } else {
        $current_class = 'single-field';
    }

    return $current_class;
        
} 

?>
