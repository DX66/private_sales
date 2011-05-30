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
 * Module functions
 *
 * @category   X-Cart
 * @package    Modules
 * @subpackage Remember Me
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.php,v 1.0.0 2011/03/20 20:16:56 stan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header('Location: ../../'); die('Access denied'); }

/**
 * Generate auth cookie value
 */
function func_gen_auth_cookie($userid = 0, $password = '')
{

    $userid = intval($userid);
    
    $password = (string) $password;

    $password_scratch = substr($password, 2, 12);

    $hash = md5($userid . '|' . $password_scratch . '|' . X_AUTH_COOKIE_HASH);

    $cookie = $userid . '|' . $hash;

    return $cookie;

}

/**
 * Set auth cookie
 */
function func_set_auth_cookie($userid = 0, $password = '')
{

    $cookie = func_gen_auth_cookie($userid, $password);

    return func_setcookie(X_AUTH_COOKIE_NAME, $cookie, X_AUTH_COOKIE_EXPIRE, false);

}

/**
 * Remove auth cookie
 */
function func_clear_auth_cookie()
{
    
    return func_setcookie(X_AUTH_COOKIE_NAME, '', 0, false);

}

/**
 * Check user for auth cookie
 */ 
function func_has_user_auth_cookie()
{
    global $XCART_SESSION_NAME, $current_area;

    return (isset($_COOKIE[X_AUTH_COOKIE_NAME]) && !empty($_COOKIE[X_AUTH_COOKIE_NAME])) ? true : false;

}

/**
 * Check auth cookie
 */
function func_validate_auth_cookie()
{
    global $active_modules, $identifiers, $cart;

    // Check if user logged in or has auth cookie
    if (!empty($identifiers[AREA_TYPE]) || !func_has_user_auth_cookie()) {

        return true;

    }

    $cookie_data = $_COOKIE[X_AUTH_COOKIE_NAME];
    
    list($cookie_userid, $cookie_hash) = explode('|', $cookie_data);

    $cookie_userid = intval($cookie_userid);

    x_load('user');

    $cookie_usertype = (!empty($active_modules['Simple_Mode']) && AREA_TYPE == 'A') ? 'P' : AREA_TYPE;

    // Check userid
    $user_data = func_userinfo($cookie_userid, $cookie_usertype);

    if (empty($user_data) || !is_array($user_data)) {

        // Clear auth cookie
        func_clear_auth_cookie();
        
        return false;

    }

    // Check hash
    $valid_cookie = func_gen_auth_cookie($user_data['id'], $user_data['password']);
    
    list($valid_userid, $valid_hash) = explode('|', $valid_cookie);
    
    if ($valid_hash != $cookie_hash) {

        // Clear auth cookie
        func_clear_auth_cookie();
        
        return false;

    }

    // Start user session
    func_start_user_session($user_data['id']);
    
    // Get cart content
    x_session_register('cart');

    $cart = unserialize($user_data['cart']);

    return $user_data['id'];

}

/**
 * Check current area 
 */
function func_is_auth_cookie_allowed($area = 'C')
{
    global $config, $active_modules;

    $auth_cookie_allowed = false;

    switch ($area) {

        case 'A':
            $auth_cookie_allowed = ($config['Remember_Me']['auth_cookie_admin_enabled'] == 'Y') ? true : false;
            break;
        
        case 'B':
            $auth_cookie_allowed = (
                $config['Remember_Me']['auth_cookie_partner_enabled'] == 'Y'
                && !empty($active_modules['XAffiliate'])
            ) ? true : false;
            break;

        case 'C':
            $auth_cookie_allowed = ($config['Remember_Me']['auth_cookie_customer_enabled'] == 'Y') ? true : false;
            break;
        
        case 'P':
            $auth_cookie_allowed = (
                $config['Remember_Me']['auth_cookie_provider_enabled'] == 'Y'
                && empty($active_modules['Simple_Mode'])
            ) ? true : false;
            break;
        
        default:

    }

    return $auth_cookie_allowed;

}

?>
