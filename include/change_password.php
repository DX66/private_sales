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
 * Change password processor
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: change_password.php,v 1.60.2.2 2011/01/10 13:11:48 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load(
    'crypt',
    'user'
);

x_session_register('login');
x_session_register('logged_userid');
x_session_register('login_change');
x_session_register('chpass_referer', array());

if (!isset($chpass_referer[AREA_TYPE])) {

    if (
        !empty($HTTP_REFERER)
        && !preg_match('/change_password.php|help.php\?section=Password_Recovery|error_message.php\?/', $HTTP_REFERER)
        && func_is_internal_url($HTTP_REFERER)
    ) {

        $chpass_referer[AREA_TYPE] = $HTTP_REFERER;

    } else {

        $chpass_referer[AREA_TYPE] = 'home.php';

    }

}

if (!empty($logged_userid)) {

    $status = func_query_first_cell("SELECT status FROM $sql_tbl[customers] WHERE id = '$logged_userid'");

    if (trim($status) == 'A') {

        $url = $chpass_referer[AREA_TYPE];

        func_unset($chpass_referer, AREA_TYPE);

        func_header_location($url);

    }

}

$reset_password = false;

unset($account);

if (
    !empty($password_reset_key)
    && !empty($user)
) {

    $user = intval($user);

    $account = func_query_first("SELECT userid, password_reset_key, password_reset_key_date FROM $sql_tbl[change_password] WHERE userid='$user' AND password_reset_key='".addslashes($password_reset_key)."'");

    $is_account_valid = is_array($account) && !empty($account);
    $is_url_expired   = XC_TIME > ($account['password_reset_key_date'] + 3600);

    if (
        $is_account_valid
        && $is_url_expired
    ) {

        // Password recovery key is expired
        db_query("DELETE FROM $sql_tbl[change_password] WHERE userid='$user'");

        $top_message = array(
            'type' => 'E',
            'content' => func_get_langvar_by_name('password_reset_url_expired')
        );

        func_header_location('home.php');

    } elseif (!$is_account_valid) {

        $top_message['type'] = 'E';
        $top_message['content'] = func_get_langvar_by_name('password_reset_url_invalid');

        func_header_location('home.php');

    } elseif (!$is_url_expired) {

        $tmp = func_query_first("SELECT usertype, login FROM $sql_tbl[customers] WHERE id='$user'");

        $account = func_array_merge($account, $tmp);

        $smarty->assign('mode',                 'recover_password');
        $smarty->assign('password_reset_key',     $account['password_reset_key']);

        $reset_password = true;

    }

}

if ($REQUEST_METHOD == 'GET') {

    if ($reset_password === true) {

        $xlogin      = $account['login'];
        $xlogin_type = $account['usertype'];
        $xuserid     = $account['userid'];

    } elseif ($mode == 'updated') {

        $smarty->assign('mode', $mode);

    } elseif (
        empty($login)
        && !isset($login_change[AREA_TYPE])
    ) {

        $top_message['content'] = func_get_langvar_by_name('txt_chpass_login');

        func_header_location('home.php');

    } elseif (isset($login_change[AREA_TYPE])) {

        $xuserid     = $login_change[AREA_TYPE];
        $xlogin_type = AREA_TYPE;
        $xlogin      = func_get_login_by_userid($xuserid);

    } else {

        $xlogin      = $login;
        $xlogin_type = $login_type;
        $xuserid     = $logged_userid;

    }

    $smarty->assign('username', $xlogin);
    $smarty->assign('usertype', $xlogin_type);
    $smarty->assign('userid',   $xuserid);

} elseif ($REQUEST_METHOD == 'POST') {

    if ($reset_password === true) {

        $xlogin      = $account['login'];
        $xlogin_type = $account['usertype'];
        $xuserid     = $account['userid'];

    } elseif (isset($login_change[AREA_TYPE])) {

        $xuserid     = $login_change[AREA_TYPE];
        $xlogin_type = AREA_TYPE;
        $xlogin      = func_get_login_by_userid($xuserid);

    } else {

        $xlogin      = $login;
        $xlogin_type = $login_type;
        $xuserid     = $logged_userid;

        if (
            $xlogin_type == 'A'
            && !empty($active_modules['Simple_Mode'])
        ) {
            $xlogin_type = 'P';
        }

    }

    $smarty->assign('username', $xlogin);
    $smarty->assign('usertype', $xlogin_type);
    $smarty->assign('userid',   $xuserid);

    $userinfo = func_userinfo($xuserid, $xlogin_type, true);

    $smarty->assign('old_password',     $old_password);
    $smarty->assign('new_password',     $new_password);
    $smarty->assign('confirm_password', $confirm_password);

    if ($reset_password === true)
        $old_password = $userinfo['password'];

    if ($userinfo['password'] == '') {

        func_header_location('error_message.php');

    } elseif ($userinfo['password'] != $old_password) {

        $top_message['content'] = func_get_langvar_by_name('txt_chpass_wrong');
        $top_message['type'] = 'E';

    } elseif ($new_password != $confirm_password) {

        $top_message['content'] = func_get_langvar_by_name('txt_chpass_match');
        $top_message['type'] = 'E';

    } elseif ($new_password == $userinfo['password']) {

        $top_message['content'] = func_get_langvar_by_name('txt_chpass_another');
        $top_message['type'] = 'E';

    } elseif (empty($new_password)) {

        $top_message['content'] = func_get_langvar_by_name('txt_chpass_empty');
        $top_message['type'] = 'E';

    } elseif (strlen($new_password) > 64) {

        $top_message['content'] = func_get_langvar_by_name('txt_wrong_password_len');
        $top_message['type'] = 'E';

    } elseif (func_is_password_weak($new_password)) {

        $top_message['content'] = func_get_langvar_by_name('txt_simple_password');
        $top_message['type'] = 'E';

    } elseif (
        $new_password == $xlogin
        && $config['Security']['use_complex_pwd'] == 'Y'
    ) {

        $top_message['content'] = func_get_langvar_by_name('txt_simple_password');
        $top_message['type'] = 'E';

    } elseif ($config['Security']['check_old_passwords'] == 'Y') {

        // Checking whether the password entered by the user is the same as any of the four previously used passwords
        $count = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[old_passwords] WHERE userid='$xuserid' AND password='" . addslashes(md5($new_password)) . "'");

        if ($count == '0') {

            $old_passwords_ids = func_query_column("SELECT id FROM $sql_tbl[old_passwords] WHERE userid='$xuserid' ORDER BY id DESC LIMIT 2");

            if (
                is_array($old_passwords_ids)
                && !empty($old_passwords_ids)
            ) {

                $old_passwords_ids = implode("', '", $old_passwords_ids);

                db_query("DELETE FROM $sql_tbl[old_passwords] WHERE id NOT IN ('$old_passwords_ids') AND userid='$xuserid'");

            }

            func_array2insert(
                'old_passwords',
                array(
                    'userid'   => $xuserid,
                    'password' => addslashes(md5($userinfo["password"])),
                ),
                true
            );

            db_query("DELETE FROM $sql_tbl[change_password] WHERE userid='$xuserid'");

            func_array2update(
                'customers',
                array(
                    'password'             => addslashes(text_crypt($new_password)),
                    'change_password'      => 'N',
                    'change_password_date' => XC_TIME,
                ),
                "id='$xuserid'"
            );

            x_log_flag(
                'log_activity',
                'ACTIVITY',
                "'$xlogin' user has changed password using 'Change password' page"
            );

            func_unset($login_change, AREA_TYPE);

            $top_message['content'] = $reset_password
                ? func_get_langvar_by_name('txt_chpass_reset')
                : func_get_langvar_by_name('txt_chpass_changed');

            func_unset($require_change_password, $xlogin_type);

            $url = $chpass_referer[AREA_TYPE];

            func_unset($chpass_referer, AREA_TYPE);

            func_header_location($url);

        } else {

            $top_message['content'] = func_get_langvar_by_name('txt_chpass_another');
            $top_message['type'] = 'E';

        }

    } else {

        $old_passwords_ids = func_query_column("SELECT id FROM $sql_tbl[old_passwords] WHERE userid='$xuserid' ORDER BY id DESC LIMIT 2");

        if (
            is_array($old_passwords_ids)
            && !empty($old_passwords_ids)
        ) {

            $old_passwords_ids = implode("', '", $old_passwords_ids);

            db_query("DELETE FROM $sql_tbl[old_passwords] WHERE id NOT IN ('$old_passwords_ids') AND userid='$xuserid'");

        }

        func_array2insert(
            'old_passwords',
            array(
                'userid'    => $xuserid,
                'password'  => addslashes(md5($userinfo["password"])),
            ),
            true
        );

        db_query("DELETE FROM $sql_tbl[change_password] WHERE userid='$xuserid'");

        func_array2update(
            'customers',
            array(
                'password'              => addslashes(text_crypt($new_password)),
                'change_password'       => 'N',
                'change_password_date'  => XC_TIME,
            ),
            "id='$xuserid'"
        );

        x_log_flag(
            'log_activity',
            'ACTIVITY',
            "'$xlogin' user has changed password using 'Change password' page"
        );

        func_unset($login_change, AREA_TYPE);

        $top_message['content'] = $reset_password
            ? func_get_langvar_by_name('txt_chpass_reset')
            : func_get_langvar_by_name('txt_chpass_changed');

        func_unset($require_change_password, $xlogin_type);

        $url = $chpass_referer[AREA_TYPE];

        func_unset($chpass_referer, AREA_TYPE);
        
        func_authenticate_user($xuserid);

        func_header_location($url);

    }

    if ($reset_password === true) {

        func_header_location("change_password.php?password_reset_key=$password_reset_key&user=$xuserid");

    } else {

        func_header_location('change_password.php');

    }

}

$location[] = array(func_get_langvar_by_name('lbl_chpass'), '');

?>
