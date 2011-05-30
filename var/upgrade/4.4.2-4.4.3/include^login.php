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
 * Log in / log out actions processor
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: login.php,v 1.214.2.2 2011/01/10 13:11:49 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header('Location: ../'); die("Access denied"); }

x_load(
    'crypt',
    'mail',
    'user',
    'backoffice'
);

x_session_register('username');
x_session_register('login_antibot_on');
x_session_register('logout_user');
x_session_register('previous_login_date');
x_session_register('login_attempt');
x_session_register('cart');
x_session_register('intershipper_recalc');
x_session_register('merchant_password');
x_session_register('antibot_err');
x_session_register('login_redirect');

$merchant_password = '';

// Redirect already logged user to home page
if (!$mode && !empty($login)) {

    if (func_is_ajax_request()) {
        func_close_window();
    }

    func_header_location('home.php');
}

if ($REQUEST_METHOD == 'POST') {

    $intershipper_recalc = 'Y';

    if ($mode == 'login') {

        if (!empty($login)) {
            // Already logged in

            func_login_error(1, 'home.php');

        } elseif (strlen($_POST['username']) > 128) {
            // Username length error

            func_login_error(2);

        } elseif (strlen($_POST['password']) > 64) {
            // Password length error

            func_login_error(3);

        }

        // Image verification module
        if (
            !empty($active_modules['Image_Verification'])
            && $login_antibot_on
        ) {

            if (func_validate_image('on_login', $_POST['antibot_input_str'])) {

                func_login_error(4);

            }

        }

        // Check for existing user
        $username = trim($_POST['username']);

        $usertype = ($current_area == 'A' && !empty($active_modules['Simple_Mode']))
            ? 'P'
            : $current_area;

        $user_data = func_query_first("SELECT * FROM $sql_tbl[customers] WHERE login='$username' AND usertype='$usertype'");

        if (!$user_data) {

            func_login_error();

        }

        $userid = $user_data['id'];

        // Check account activity
        if (!func_check_account_activity($userid)) {

            func_login_error(5);

        }

        // Suspend admin account which was not logged in for N days
        if (
            (
                $usertype == 'A'
                || (
                    $usertype == 'P'
                    && !empty($active_modules['Simple_Mode'])
                )
            )
            && intval($config['Security']['suspend_admin_after']) > 0
            && $user_data['last_login'] > 0
            && (XC_TIME > ($user_data['last_login'] + $config['Security']['suspend_admin_after']*24*3600))
            && func_suspend_account($user_data['id'], $usertype, 'long_unused')
        ) {

                db_query("UPDATE $sql_tbl[customers] SET last_login='" . XC_TIME . "' WHERE id='$user_data[id]'");

                func_login_error(6);

        }

        // Force password change if non-customer password was not changed for 90 days
        if (
            $config['Security']['force_change_password_days'] > 0
            && $usertype != 'C'
            && $user_data['change_password_date'] > 0
            && (
                XC_TIME > ($user_data['change_password_date'] + $config['Security']['force_change_password_days']*24*3600)
                && $user_data['change_password'] != 'Y'
            )
        ) {

            if ($usertype != 'C') {

                db_query("UPDATE $sql_tbl[customers] SET change_password='Y' WHERE id='$user_data[id]'");

                x_session_register('login_change');
                x_session_register('require_change_password');

                $require_change_password[$usertype] = true;
                $login_change[$usertype] = $user_data['id'];

                func_login_error(7, 'change_password.php');
            }
        }

        $allow_login = $allow_admin_ip = true;

        // Check by IP for admin staff
        if (
            $usertype == 'A'
            || (
                $usertype == 'P'
                 && !empty($active_modules['Simple_Mode'])
            )
        ) {

            $iplist = preg_grep("/^\d+\.\d+\.\d+\.\d+$/", array_unique(preg_split('/[ ,]+/', trim($admin_allowed_ip))));

            $allow_login = count($iplist) > 0 ? func_compare_ip($REMOTE_ADDR, $iplist) : true;

            if (
                $allow_login
                && !empty($config['allowed_ips'])
                && !func_check_allow_admin_ip()
            ) {

                func_send_admin_ip_reg('L', $username);

                if (
                    empty($user_data['first_login'])
                    && empty($user_data['last_login'])
                ) {

                    func_register_admin_ip($REMOTE_ADDR);

                    $allow_admin_ip = true;

                } else {

                    $allow_login = $allow_admin_ip = false;

                    func_login_error(9, null, false);

                }

            }

        }

        // Check password

        $password = stripslashes($password);

        if (!func_is_password_correct($password, $user_data['password'])) {

            func_login_error();

        }

        // Register IP for new admin / provider

        if ($allow_login) {

            // Success login
            func_authenticate_user($userid);

            $logout_user     = false;
            $redirect_url     = 'home.php';

            if (
                $login_type == 'C'
                && $user_data['cart']
                && func_is_cart_empty($cart)
            ) {
                $cart = unserialize($user_data['cart']);
            }

            if ($login_type == 'C') {

                // Clean anonymous profile data
                x_session_unregister('anonymous_userinfo');

                // Redirect to saved URL
                x_session_register('remember_data');

                if (
                    isset($is_remember)
                    && $is_remember == 'Y'
                    && !empty($remember_data)
                ) {

                    if (
                        $HTTPS
                        && preg_match("/^http:\/\//", trim($remember_data['URL']))
                        && $config['Security']['leave_https'] != 'Y'
                    ) {
                        $remember_data['URL'] = preg_replace("/^" . preg_quote($http_location, "/") . "/", $https_location, trim($remember_data['URL']));
                    }

                    $redirect_url = $remember_data['URL'];

                } elseif (!func_is_cart_empty($cart)) {

                    // Redirect to cart page
                    $login_redirect = false;

                    if(
                        strpos($HTTP_REFERER, "mode=auth") === false
                        && strpos($HTTP_REFERER, "mode=checkout") === false
                    ) {

                        $redirect_url = 'cart.php';

                    } else {

                        $redirect_url = 'cart.php?mode=checkout';

                    }

                } elseif (!empty($HTTP_REFERER)) {

                    // Redirect to HTTP_REFERER
                    if (
                        func_is_internal_url($HTTP_REFERER)
                        && !preg_match('/(error_message\.php|login\.php|help\.php\?section=Password_Recovery)/s', $HTTP_REFERER)
                        && (preg_match('/\.php(?:\?|$)/s', $HTTP_REFERER) || $config['SEO']['clean_urls_enabled'] == 'Y')
                    ) {

                        $qs = strrchr(func_qs_remove($HTTP_REFERER, $XCART_SESSION_NAME), '/');

                        $redirect_url = func_get_area_catalog($login_type) . $qs;

                    }

                }

            }

            // If shopping cart is not empty then user is redirected to cart.php
            // Default password alert

            if (
                $login_type == 'A'
                || $login_type == 'P'
            ) {

                $redirect_url = (
                        !empty($active_modules['Simple_Mode'])
                        || $login_type == 'A'
                    ? $xcart_catalogs['admin']
                    : $xcart_catalogs['provider']
                ) . '/home.php';

                // Return to saved last working URL if we have one for specified login type.
                $_tmp_last_working_url = func_url_get_last_working_url($login_type);

                if (!zerolen($_tmp_last_working_url)) {

                    $redirect_url = $_tmp_last_working_url;

                }

                unset($_tmp_last_working_url);

                $current_area = $login_type;

                if (!defined('GET_LANGUAGE')) {

                    include $xcart_dir . '/include/get_language.php';

                }

                // Check expiration for preauth orders
                x_load('payment');

                func_check_preauth_expiration();

                x_session_register('show_adv');

                $show_adv = true;

            } else {

                $redirect_url = $redirect_url;

            }

            if ($login_type == 'P') {

                x_session_register('show_seller_address_warning');

                $show_seller_address_warning = true;

            }

            // Ajax request
            if (func_is_ajax_request()) {

                func_reload_parent_window();

            }

            $default_accounts = func_check_default_passwords($logged_userid);

            if (!empty($default_accounts)) {

                $current_area = $login_type;

                $txt_message = func_get_langvar_by_name('txt_your_password_warning_js',false,false,true);

                $javascript_message = func_js_alert($txt_message, $redirect_url);

            } elseif (
                $usertype == 'A'
                || (
                    $usertype == 'P'
                    && !empty($active_modules['Simple_Mode'])
                )
            ) {

                $default_accounts = func_check_default_passwords();

                $is_allowed_membership = $user_data['membershipid'] <= 0 || func_query_first_cell("SELECT flag FROM $sql_tbl[memberships] WHERE membershipid  = '$user_data[membershipid]'") != 'FS';

                if (
                    !empty($default_accounts)
                    && $is_allowed_membership
                ) {

                    $txt_message = func_get_langvar_by_name('txt_default_passwords_warning_js', array('accounts'=>implode(", ", $default_accounts)),false,true);
                    $javascript_message = func_js_alert($txt_message, $redirect_url);

                }

            }

            if (
                !empty($javascript_message)
                && $admin_safe_mode == false
            ) {

                x_session_save();

                echo $javascript_message;

                exit;

            }

            func_header_location($redirect_url);

        }

    }

}

/**
 * Check for valid activation key and activate account on success
 */
if (
    isset($activation_key)
    && preg_match("/^[a-f0-9]{32}$/i", $activation_key)
    && $username = func_enable_account($activation_key)
) {

    $top_message = array(
        'type'    => 'I',
        'content' => func_get_langvar_by_name(
            'txt_account_activated',
            array(
                'username' => $username,
            ),
            false,
            true
        )
    );

    func_header_location('login.php');
}

if ($mode == 'logout') {

    $login_antibot_on = false;

    $login_attempt = 0;

    x_session_register('payment_cc_fields');

    $payment_cc_fields = array();

    if ($current_area == 'C') {

        x_load('paypal');

        func_paypal_clear_ec_token();

    }

    // Insert into login history
    if (
        !empty($active_modules['Simple_Mode'])
        && $login_type == 'A'
    ) {
        $login_type = 'P';
    }

    func_store_login_action($logged_userid, $login_type, 'logout', 'success');

    x_log_flag(
        'log_activity',
        'ACTIVITY',
        "User '$login' ('$login_type' user type) has logged out. Remote IP '$REMOTE_ADDR'"
    );

    // Clear user session identifiers
    func_end_user_session();

    if ($current_area == 'C') {

        $cart = '';

    }

    $access_status     = '';
    $merchant_password = '';
    $logout_user       = true;

    if (
        $current_area == 'A'
        || $current_area == 'P'
    ) {

        func_ge_erase();

        x_session_register('recent_payment_methods');

        $recent_payment_methods = array();

    }

    x_session_unregister('hide_security_warning');
    x_session_unregister('initial_state_orders');
    x_session_unregister('initial_state_show_notif');

    $login_redirect = 1;

    func_header_location('home.php');

}

$qs_match = array(
    'login.php',
    'mode=order_message',
    'mode=wishlist',
    'bonuses.php',
    'returns.php',
    'giftreg_manage.php',
    'order.php',
    'error_message.php',
    'register.php?mode=delete',
    'register.php?mode=update',
);

$qs_match_str = implode('|', $qs_match);

if (
    isset($old_login_type)
    && $old_login_type == 'C'
    && func_is_internal_url($HTTP_REFERER)
    && !preg_match('/('.$qs_match.')/Ss', $HTTP_REFERER)
) {
    func_header_location(strrchr(func_qs_remove($HTTP_REFERER, $XCART_SESSION_NAME), '/'), false);
}

if ($login_antibot_on) {

   $smarty->assign('login_antibot_on', $login_antibot_on);

}

?>
