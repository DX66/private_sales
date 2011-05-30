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
 * Process registration and profile update actions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: register.php,v 1.359.2.19 2011/01/10 13:11:50 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load(
    'cart',
    'category',
    'crypt',
    'mail',
    'user'
);

x_session_register ('intershipper_recalc');
x_session_register ('av_error', false);
x_session_unregister('secure_oid');
x_session_register('saved_address_book', array());
x_session_register('saved_userinfo');

$country_where = (isset($submode) && ($submode == 'seller_address'))
    ? " AND $sql_tbl[countries].code='" . $config["Company"]["location_country"] . "'"
    : '';

require $xcart_dir . '/include/countries.php';
require $xcart_dir . '/include/states.php';

if ($config['General']['use_counties'] == 'Y') {
    include $xcart_dir . '/include/counties.php';
}

x_session_register('reg_error', array());

// Process UPS suggestion
if (
    $REQUEST_METHOD == 'POST'
    && !empty($active_modules['UPS_OnLine_Tools'])
    && $av_suggest
) {

    // Shipping Address Validation by UPS OnLine Tools module
    $av_data = func_ups_av_process_suggestion($av_suggest, $rank);

    if ($av_suggest == 'R') {

        // Restore saved data to re-enter
        $reg_error['saved_data'] = $_POST;
        $REQUEST_METHOD = 'GET';

    } elseif ($av_suggest == 'K') {

        // Restore and process saved data

    } elseif (
        $av_suggest == 'Y'
        && !empty($av_data)
    ) {
        // Apply suggestion
        foreach ($av_data as $f => $val) {
            $_POST['address_book']['S'][$f] = addslashes($val);
            if (!$_POST['ship2diff']) {
                $_POST['address_book']['B'][$f] = addslashes($val);
            }
        }
    }

    if (func_is_ajax_request()) {

        func_register_ajax_message(
            'popupDialogCall',
            array(
                'action' => 'close'
            )
        );
    }

    extract($_POST);
    extract($_GET);

    $mode = 'update';
}

$user = (isset($user)) ? intval($user) : 0;

if ($current_area == 'C') {
    include $xcart_dir . '/include/register_ccfields.php';
}

if (
    isset($newbie)
    && $newbie == 'Y'
    && !isset($edit_profile)
) {

    // Register/Modify own profile

    $location[] = array(func_get_langvar_by_name('lbl_profile_details'), '');
}

$mode = !empty($mode) ? $mode : '';
$main = !empty($main) ? $main : '';

$is_admin_editor = false;

if (
    $current_area == 'C'
    && (
        isset($edit_profile)
        || $main == 'checkout'
    )
) {

    $fields_area = 'H';

} elseif ($current_area == 'C') {

    $fields_area = 'C';

} elseif (
    defined('IS_ADMIN_USER')
    && (
        defined('USER_MODIFY')
        || defined('USER_ADD')
    )

) {
    $fields_area = isset($usertype) ? $usertype : $current_area;

    $is_admin_editor = true;
    $smarty->assign('is_admin_editor', true);

} else {

    $fields_area = !empty($active_modules['Simple_Mode']) && $current_area == 'A'
        ? 'P'
        : $current_area;

}

$additional_fields  = func_get_additional_fields($fields_area, $logged_userid);
$default_fields     = func_get_default_fields($fields_area);
$address_fields     = func_get_default_fields($fields_area, 'address_book');

$name_fields = array();

$is_areas = func_get_profile_areas($fields_area);

$allow_pwd_modify =
    empty($login)
    || (
        defined('IS_ADMIN_USER')
        && (
            $REQUEST_METHOD == 'GET'
            || (
                $REQUEST_METHOD == 'POST'
                && $password_is_modified == 'Y'
            )
        )
    );

if (
    $REQUEST_METHOD == 'POST'
    && isset($_POST['usertype'])
) {

    /**
     * Process the POST request and create/update profile
     * or collect errors if any
     */

    if (isset($cart_operation))
        return;

    // Assign email to username if Email as login option is enabled
    if (
        !empty($login)
            || (
                !empty($passwd1)
                && !empty($passwd2)
            )
    ) {
        $uname = $config['email_as_login'] == 'Y'
            ? $email
            : $uname;
    }

    $uname = trim($uname);

    // Adjust mode for anonymous customers
    if (
        $mode == 'update'
        && !empty($uname)
        && empty($login)
    ) {
        $mode = 'register';
    }

    /**
     *  Anonymous registration/update
     */
    $is_anonymous = false;

    if (
        $current_area == 'C'
        && $config['General']['enable_anonymous_checkout'] == 'Y'
        && !defined('USER_MODIFY')
        && !defined('USER_ADD')
        && (
            (
                $mode != 'update'
                && empty($login)
                && empty($uname)
            ) || (
                $mode == 'update'
                && empty($login)
            )
        )
    ) {
        $is_anonymous = true;
    }

    /**
     * Check if user have permissions to update/create profile
     */
    $allowed_registration = (
        $usertype == 'C'
        || (
            $usertype == 'B' 
            && $config['XAffiliate']['partner_register'] == 'Y'
        )
        || (
            $usertype == 'P' 
            && $config['General']['provider_register'] == 'Y'
        )
        || defined('IS_ADMIN_USER')
    );

    $allowed_update = (
        (
            $usertype == $current_area
            && !empty($login)
            && !empty($uname)
            && (
                $login == $uname
                || $config['General']['allow_change_login'] == 'Y'
                || $config['email_as_login'] == 'Y'
            )
        ) || defined('IS_ADMIN_USER')
        || $is_anonymous
    );

    $allow_set_login = (
        $mode != 'update'
        || $config['General']['allow_change_login'] == 'Y'
        || $config['email_as_login'] == 'Y'
    );

    if (
        (
            $mode != 'update'
            && !$allowed_registration
        ) || (
            $mode == 'update'
            && !$allowed_update
        )
    ) {
        func_403(36);
    }

    /**
     * User registration info passed to register.php via POST method
     * Errors check
     */

    $errors = array();

    if (
        $mode == 'update'
    ) {
        $old_userinfo = func_userinfo($logged_userid, $login_type, $allow_pwd_modify, false, $fields_area, false);

        // Make checksum of the previous shipping address
        if (
            $current_area == 'C'
            && $main == 'checkout'
        ) {
            $shipping_checksum_fields = array(
                'city',
                'state',
                'country',
                'county',
                'zipcode',
            );

            $_tmp = is_array($cart['used_s_address']) ? $cart['used_s_address'] : $old_userinfo['address']['S'];
            $shipping_checksum_init = func_generate_checksum($_tmp, $shipping_checksum_fields);
        }

        if (
            !empty($old_userinfo)
            && !$allow_set_login
            && !$is_anonymous
        ) {
            $uname = addslashes($old_userinfo['login']);
        }
    }

    // Check if user already exists on register / change login

    if (!empty($uname)) {

        $existing_user = (func_query_first_cell("SELECT COUNT(id) FROM $sql_tbl[customers] WHERE login='$uname' AND usertype='$usertype'") > 0);

        if (
            $existing_user
            && (
                $mode != 'update'
                || $uname != $login
            )
        ) {
            $errors[] = func_reg_error(1);
        }

        // Check login
        if (
            !preg_match('/^' . func_login_validation_regexp() . '$/is', stripslashes($uname))
            || strlen($uname) > 127
        ) {
            $errors[] = func_reg_error(6);
        }

    }

    x_session_register('antibot_reg_err');

    if (
        !empty($login)
        || !empty($anonymous_userinfo)
        || in_array($current_area, array('A'))
        || (
            $current_area == 'B'
            && !empty($login)
        )
    ) {

        $antibot_reg_err = false;

    } else {

        $antibot_reg_err = (
            !defined('USER_MODIFY')
            && !defined('USER_ADD')
            && !empty($active_modules['Image_Verification'])
            && func_validate_image('on_registration', $antibot_input_str)
        );

    }

    if ($antibot_reg_err) {
        $errors[] = func_reg_error(3);
    }

    if (
        !$is_anonymous
        && (
            $allow_pwd_modify
            && $passwd1 != $passwd2
            || strlen($passwd1) > 64
            || strlen($passwd2) > 64
        )
    ) {
        $errors[] = func_reg_error(15);
    }

    $fillerror = (
        !$is_anonymous
        && (
            empty($uname)
            || (
                $allow_pwd_modify
                && (
                    empty($passwd1)
                    || empty($passwd2)
                )
            )
        )
    );

    if (
        !$is_anonymous
        && !$fillerror
        && $allow_pwd_modify
    ) {

        if (
            $config['Security']['use_complex_pwd'] == 'Y'
            && (
                func_is_password_weak($passwd1)
                || $passwd1 == $uname
                || (
                    !empty($login)
                    && $login != $uname
                )
            )
        ) {
            $errors[] = func_reg_error(5);
        }

        if (!empty($old_userinfo)) {

            $old_userinfo['password'] = text_decrypt($old_userinfo['password']);

            if ($config['Security']['check_old_passwords'] == 'Y') {

                $old_passwords_count = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[old_passwords] WHERE userid='" . addslashes($logged_userid) . "' AND password='" . addslashes(md5($passwd1)) . "'");

                if ($passwd1 == $old_userinfo['password']) {
                    $errors[] = func_reg_error(4);
                }

            }

        }

    }

    // Check required fields
    if (
        !$fillerror
        && is_array($default_fields)
    ) {
        foreach ($default_fields as $k => $v) {
            if (
                $v['required'] == 'Y'
                && empty(${$k})
            ) {
                $fillerror = ($k == 'state' || ($k == 'county' && $config['General']['use_counties'] == 'Y'))
                    ? func_is_display_states($country)
                    : true;
            }
        }
    }

    // Check additional fields
    if (
        !$fillerror
        && $additional_fields
    ) {
        foreach ($additional_fields as $v) {
            if (
                $v['required'] == 'Y'
                && empty($additional_values[$v['fieldid']])
                && $v['avail'] == 'Y'
                && !$is_admin_editor
            ) {
                $fillerror = true;
                break;
            }
        }
    }

    // Check email
    if (!func_check_email($email)) {
        $errors[] = func_reg_error(2);
    }

    // Some of required fields are empty
    if (
        $fillerror
        && !$is_admin_editor
    ) {
        $errors[] = func_reg_error(14);
    }

    // Check address book
    if (isset($address_book)) {

        $addr_errors = array();

        foreach ($address_book as $addrid => $data) {

            if (
                $current_area == 'C'
                && $addrid == 'S'
                && !$ship2diff
            ) {
                continue;
            }

            if (
                $current_area != 'C'
                && isset($delete_address)
                && isset($delete_address[$addrid])
            ) {
                continue;
            }

            $_result = func_check_address($data, $fields_area, true);
            
            if (isset($_result['not_filled'])) {

                // All fields are empty
                func_unset($address_book, $addrid);
                continue;
            }

            if (!empty($_result['errors'])) {
                $addr_errors[$addrid] = $_result['errors'];
            }
        }
    }
    
    if (
        empty($errors) 
        && empty($addr_errors)
    ) {

        // Fields filled without errors. User registered successfully

        if ($allow_pwd_modify) {
            $crypted = addslashes(text_crypt($passwd1));
        }

        // Add new member to newsletter list

        $cur_subs = array();

        if (!empty($old_userinfo)) {

            $tmp = func_query("SELECT DISTINCT($sql_tbl[newslist_subscription].listid) FROM $sql_tbl[newslist_subscription], $sql_tbl[newslists] WHERE $sql_tbl[newslist_subscription].email='".addslashes($old_userinfo["email"])."' AND $sql_tbl[newslist_subscription].listid=$sql_tbl[newslists].listid AND $sql_tbl[newslists].lngcode='$shop_language'");

            if (is_array($tmp)) {
                foreach ($tmp as $v)
                    $cur_subs[] = $v['listid'];
            }
        }

        $ext_subs = array();

        $tmp = func_query("SELECT DISTINCT($sql_tbl[newslist_subscription].listid) FROM $sql_tbl[newslist_subscription], $sql_tbl[newslists] WHERE $sql_tbl[newslist_subscription].email='$email' AND $sql_tbl[newslist_subscription].listid=$sql_tbl[newslists].listid AND $sql_tbl[newslists].lngcode='$shop_language'");
        if (is_array($tmp)) {
            foreach ($tmp as $v)
                $ext_subs[] = $v['listid'];
        }

        $subs_keys = array();
        if (is_array($subscription)) $subs_keys = array_keys($subscription);

        $delid = array_diff($cur_subs,$subs_keys);
        $insid = array_diff($subs_keys,$cur_subs,$ext_subs);
        $updid = array_intersect($cur_subs, $subs_keys);
        $updid = array_diff($updid, $ext_subs);

        if (count($delid) > 0) {
            db_query("DELETE FROM $sql_tbl[newslist_subscription] WHERE email='$old_userinfo[email]' AND listid IN ('".implode("','",$delid)."')");
        }

        if (
            count($updid)>0
            && $old_userinfo['email'] != stripslashes($email)
        ) {
            db_query("UPDATE $sql_tbl[newslist_subscription] SET email='$email' WHERE email='$old_userinfo[email]' AND listid IN ('".implode("','",$updid)."')");
        }

        foreach ($insid as $id) {
            db_query("INSERT INTO $sql_tbl[newslist_subscription] (listid, email, since_date) VALUES ('$id','$email', '".XC_TIME."')");
        }

        // URL normalization
        if (!empty($url)) {
            if(strpos($url, 'http') !== 0) {
                $url = "http://".$url;
            }
        }

        if ($uname == $parent) {
            $parent = '';
        } else {
            $parent = func_query_first_cell("SELECT id FROM $sql_tbl[customers] WHERE id='$parent' AND usertype = 'B'");
        }

        // Fill customer's name from address book entry
        // during registration at checkout
        if (
            $current_area == 'C'
            && $main == 'checkout'
            && isset($address_book)
            && isset($address_book['B'])
        ) {
            
            $name_fields = array(
                'title',
                'firstname',
                'lastname',
            );

            foreach($name_fields as $k => $f) {
                if (
                    (
                        !isset(${$f})
                        || empty(${$f})
                    )
                    && isset($address_book['B'][$f])
                ) {
                    ${$f} = $address_book['B'][$f];
                } else {
                    unset($name_fields[$k]);
                }
            }
        }

        // Update/Insert user info

        $common_profile_fields = array(
            'title',
            'firstname',
            'lastname',
            'company',
            'email',
            'url',
            'pending_membershipid',
            'ssn',
            'parent',
        );

        $profile_values = array();

        foreach ($common_profile_fields as $field) {

            if (isset(${$field}))
                $profile_values[$field] = ${$field};

        }

        // Store new password

        if ($allow_pwd_modify) {

            $old_passwords_ids = func_query_column("SELECT id FROM $sql_tbl[old_passwords] WHERE id='".addslashes($uname)."' ORDER BY id DESC LIMIT 2");

            if (
                is_array($old_passwords_ids)
                && !empty($old_passwords_ids)
            ) {
                $old_passwords_ids = implode("', '",$old_passwords_ids);

                db_query("DELETE FROM $sql_tbl[old_passwords] WHERE id NOT IN ('$old_passwords_ids') AND id='".addslashes($uname)."'");
            }

            if (!empty($old_userinfo['password'])) {
                db_query("REPLACE INTO $sql_tbl[old_passwords] (id, password) VALUES ('".addslashes($uname)."','".addslashes(md5($old_userinfo["password"]))."')");
            }

            $profile_values['password'] = $crypted;
            $profile_values['change_password_date'] = XC_TIME;
        }

        if (
            $current_area == 'C'
            || $current_area == 'B'
        ) {

            if (!empty($cc_profile_data)) {
                $profile_values = func_array_merge($profile_values, $cc_profile_data);
            }

            $fields = array_keys($profile_values);

            foreach ($default_fields as $_field => $_avail) {
                if (
                    $_avail['avail'] != 'Y'
                    && in_array($_field, $fields)
                    && !in_array($_field, $name_fields)
                ) {
                    unset($profile_values[$_field]);
                }
            }

            unset($fields);

            if (
                $config['Taxes']['allow_user_modify_tax_number'] == 'Y'
                || !$existing_user
                || func_query_first_cell("SELECT tax_exempt FROM $sql_tbl[customers] WHERE id='$logged_userid'") != "Y"
            ) {
                // Existing customer cannot edit 'tax_number' if
                // 'tax_exempt' == 'Y' and
                // 'allow_user_modify_tax_number' option == 'N'
                if (isset($tax_number))
                    $profile_values['tax_number'] = $tax_number;
            }

        } elseif (defined('IS_ADMIN_USER')) {

            // Administrator can edit 'tax_number' and 'tax_exempt'

            $profile_values['tax_number']             = $tax_number;
            $profile_values['tax_exempt']             = (@$tax_exempt == 'Y' ? 'Y' : 'N');
            $profile_values['trusted_provider']     = ($login_type == 'P' && empty($active_modules["Simple_Mode"])) ? $trusted_provider : 'Y';
        }

        $activity_changed = false;

        if (
            defined('USER_MODIFY')
            || defined('USER_ADD')
        ) {

            $profile_values['change_password'] = empty($change_password) ? 'N' : 'Y';

            $profile_values['status']             = empty($status) ? 'N' : $status;
            $profile_values['suspend_date']     = ($old_userinfo['status'] != $status) ? XC_TIME : '';

            if ($profile_values['status'] != 'N')
                $profile_values['activation_key'] = '';

            $profile_values['activity'] = empty($activity) ? 'N' : $activity;

            $activity_changed = ($profile_values['activity'] != $old_userinfo['activity']);
        }

        if ($is_anonymous) {
            /**
             * Store anonymous profile in session
             */

            x_session_register('anonymous_userinfo', array());
            $anonymous_userinfo = $profile_values;
            $anonymous_userinfo['additional_fields'] = $additional_fields;
            $anonymous_userinfo['additional_values'] = $additional_values;
            $anonymous_userinfo['usertype'] = empty($usertype) ? 'C' : $usertype;

        } elseif ($mode == 'update') {

            /**
             * Update existing profile
             */

            $intershipper_recalc = 'Y';

            if (
                isset($profile_values['password'])
                && $profile_values['password'] != func_query_first_cell("SELECT password FROM $sql_tbl[customers] WHERE id='$logged_userid' AND usertype='$login_type'")
            ) {
                x_log_flag('log_activity', 'ACTIVITY', defined('USER_MODIFY') ? "'$login_' has changed password to '$login' user" : "'$login' has changed password");
            }

            if ($old_userinfo['login'] != stripslashes($uname)) {
                // Change login
                $profile_values['login'] = $uname;
                $identifiers[$login_type]['login'] = stripslashes($uname);
            }

            if (defined('IS_ADMIN_USER')) {

                $profile_values['membershipid'] = $membershipid;

            }

            $profile_values = func_array_map('trim', $profile_values);

            if (!empty($profile_values)) {
                func_array2update(
                    'customers',
                    $profile_values,
                    "id='$logged_userid' AND usertype='$login_type'"
                );
            }

            if (in_array($login_type, array('P', 'A'))) {
                x_log_flag(
                    'log_activity',
                    'ACTIVITY',
                    defined('USER_MODIFY')
                        ? "'$login_' user has updated '$login' profile"
                        : "'$login' user has updated '$login' profile"
                );
            }

            db_query("DELETE FROM $sql_tbl[register_field_values] WHERE userid = '$logged_userid'");

            if ($additional_values) {
                foreach ($additional_values as $k => $v) {
                    func_array2insert(
                        'register_field_values',
                        array(
                            'fieldid'     => $k,
                            'userid'    => $logged_userid,
                            'value'        => $v,
                        )
                    );
                }
            }

            if ($login_type == 'B') {

                $planId = false;

                if (in_array(constant('AREA_TYPE'), array('A', 'P'))) {

                    $planId = !$plan_id
                        ? $config['XAffiliate']['default_affiliate_plan']
                        : $plan_id;

                } elseif (
                    $config['XAffiliate']['partner_register_moderated'] != 'Y'
                    && constant('AREA_TYPE') == 'B'
                ) {

                    $planId = $pending_plan_id;

                }

                if (false !== $planId) {

                    func_array2insert(
                        'partner_commissions',
                        array(
                            'userid'     => $logged_userid,
                            'plan_id'    => $planId,
                        ),
                        true
                    );
                }
            }

            $registered = 'Y';

            // Send mail notifications to customer department and signed customer

            $newuser_info = func_userinfo($logged_userid, $login_type, $allow_pwd_modify, NULL, $fields_area, false);

            $mail_smarty->assign('userinfo', $newuser_info);

            // Send mail to registered user

            $to_customer = $newuser_info['language'];

            if($config['Email_Note']['eml_profile_modified_customer'] == 'Y') {

                func_send_mail(
                    $newuser_info['email'],
                    'mail/profile_modified_subj.tpl',
                    'mail/profile_modified.tpl',
                    $config['Company']['users_department'],
                    false
                );

            }

            // Send mail to customers department

            if($config['Email_Note']['eml_profile_modified_admin'] == 'Y') {

                func_send_mail(
                    $config['Company']['users_department'],
                    'mail/profile_admin_modified_subj.tpl',
                    'mail/profile_admin_modified.tpl',
                    $newuser_info['email'],
                    true
                );

            }

            if (
                !empty($active_modules['Greet_Visitor'])
                && $login_type == 'C'
                && $current_area == 'C'
            ) {
                func_store_greeting($profile_values);
            }

        } else {

            /**
             * Add new person to customers table
             * or store anonymous profile in session
             */

            $intershipper_recalc = 'Y';

            $profile_values['login']    = $profile_values['username'] = $uname;
            $profile_values['usertype'] = $usertype;
            $profile_values['status']   = 'Y';

            if (
                !defined('USER_MODIFY')
                && !defined('USER_ADD')
            ) {
                $profile_values['change_password'] = 'N';
                $profile_values['activity']        = 'Y';
            }

            if (
                (
                    (
                        $usertype == 'B' 
                        && $config['XAffiliate']['partner_register_moderated'] == 'Y'
                    )
                    || (
                        $usertype == 'P' 
                        && $config['General']['provider_register_moderated'] == 'Y'
                    )
                )
                && !defined('USER_MODIFY')
                && !defined('USER_ADD')
            ) {
                $profile_values['status']    = 'Q';
            }

            if (defined('IS_ADMIN_USER')) {

                $profile_values['membershipid'] = $membershipid;

            }

            if (!isset($profile_values['cart'])) {

                $profile_values['cart'] = '';

            }

            if (
                defined('AREA_TYPE')
                && in_array(constant('AREA_TYPE'), array('C', 'B'))
                && isset($_COOKIE['RefererCookie'])
            ) {
                $profile_values['referer'] = $_COOKIE["RefererCookie"];
            }

            // Set prefered language for new customer
            $_user_lngcode = 'en';

            if (defined('USER_ADD')) {

                $_user_lngcode = $usertype == 'C'
                    ? $config['default_customer_language']
                    : $config['default_admin_language'];

            } elseif ($store_language) {

                $_user_lngcode = $store_language;

            }

            $profile_values['language'] = $_user_lngcode;

            // Auto log-in
            $isAutoLogin = (
                $usertype == 'C'
                || (
                    $usertype == 'B'
                    && $login == ''
                )
                || (
                    $usertype == 'P'
                    && $login == '' 
                )
            );

            if ($isAutoLogin) {

                $profile_values['last_login'] = $profile_values['first_login'] = XC_TIME;

            }

            $profile_values = func_array_map('trim', $profile_values);

            $newuserid = func_array2insert(
                'customers',
                $profile_values
            );

            func_call_event('user.register.aftersave', $newuserid);

            $saved_userinfo = $anonymous_userinfo = array();

            if (in_array($usertype, array('A', 'P'))) {
                x_log_flag(
                    'log_activity',
                    'ACTIVITY',
                    defined('USER_ADD')
                        ? "'$login_' user has added '$login' user, '$usertype' usertype"
                        : "'$login' user has added '$login' user, '$usertype' usertype"
                );
            }

            $new_user_flag = true;

            db_query("DELETE FROM $sql_tbl[register_field_values] WHERE userid = '$newuserid'");

            if ($additional_values) {

                foreach ($additional_values as $k => $v) {

                    func_array2insert(
                        'register_field_values',
                        array(
                            'fieldid' => $k,
                            'userid'  => $newuserid,
                            'value'   => $v,
                        )
                    );
                }
            }

            if ($usertype == 'B') {

                $planId = false;

                if (in_array(constant('AREA_TYPE'), array('A', 'P'))) {

                    $planId = !$plan_id
                        ? $config['XAffiliate']['default_affiliate_plan']
                        : $plan_id;

                } elseif (
                    $config['XAffiliate']['partner_register_moderated'] != 'Y'
                    && constant('AREA_TYPE') == 'B'
                ) {
                    $planId = $pending_plan_id;
                }

                if (false !== $planId) {
                    func_array2insert(
                        'partner_commissions',
                        array(
                            'userid'  => $newuserid,
                            'plan_id' => $planId,
                        )
                    );
                }
            }

            $registered = 'Y';

            // Send mail notifications to customer department and signed customer

            $newuser_info = func_userinfo($newuserid, $usertype, true, NULL, $fields_area, false);

            $mail_smarty->assign('userinfo', $newuser_info);

            // Send mail to registered user (do not send to anonymous)

            $mail_smarty->assign('full_usertype', func_get_langvar_by_name($usertype == 'B' ? 'lbl_partner' : ($usertype == 'P' ? 'lbl_provider' : 'lbl_customer')));

            if (!empty($email)) {

                if ($usertype == 'B') {

                    if ($config['XAffiliate']['eml_signin_partner_notif'] == 'Y')

                        func_send_mail(
                            $email,
                            'mail/signin_notification_subj.tpl',
                            'mail/signin_partner_notif.tpl',
                            $config['Company']['users_department'],
                            false
                        );

                } elseif ($usertype == 'P') {

                    if ($config['Email_Note']['eml_signin_provider_notif'] == 'Y')

                        func_send_mail(
                            $email,
                            'mail/signin_notification_subj.tpl',
                            'mail/signin_provider_notif.tpl',
                            $config['Company']['users_department'],
                            false
                        );

 

                } else {

                    if ($config['Email_Note']['eml_signin_notif'] == 'Y') {
                        func_send_mail(
                            $email,
                            'mail/signin_notification_subj.tpl',
                            'mail/signin_notification.tpl',
                            $config['Company']['users_department'],
                            false
                        );
                    }

                }

            }

            // Send mail to customers department
            if ($config['Email_Note']['eml_signin_notif_admin'] == 'Y') {

                func_send_mail(
                    $config['Company']['users_department'],
                    'mail/signin_admin_notif_subj.tpl',
                    'mail/signin_admin_notification.tpl',
                    $email,
                    true
                );

            }

            // Auto-log in

            if ($isAutoLogin) {

                func_store_login_action($newuserid, $usertype, 'login', 'success');

                $login         = $uname;
                $login_type    = $usertype;
                $logged_userid = $newuserid;

                x_session_register('identifiers',array());

                $identifiers[$usertype] = array (
                    'login'      => $login,
                    'login_type' => $login_type,
                    'userid'     => $logged_userid,
                );
            }

        }

        if (
            !empty($active_modules['SnS_connector'])
            && $usertype == 'C'
            && defined('AREA_TYPE')
            && constant('AREA_TYPE') == 'C'
        ) {
            func_generate_sns_action('Register');

            if ($isAutoLogin) {
                func_generate_sns_action('Login');
            }
        }

        if (
            !empty($active_modules['Special_Offers'])
            && $usertype == 'C'
            && (
                defined('USER_MODIFY')
                || defined('USER_ADD')
            )
        ) {
            include $xcart_dir.'/modules/Special_Offers/register_customer.php';
        }

        // Save address book
        include $xcart_dir . '/include/address_book.php';

    } else {

        // Fill $userinfo array if error occured
        $userinfo = $_POST;

        if (
            !empty($_POST['additional_values'])
            && !empty($additional_fields)
        ) {
            foreach ($additional_fields as $k => $v) {
                $additional_fields[$k]['value'] = $additional_values[$v['fieldid']];
            }
        }

        $saved_userinfo[$user]                      = func_stripslashes($userinfo);
        $saved_userinfo[$user]['additional_fields'] = $additional_fields;

        func_call_event('user.register.filluserinfo');

        if (isset($address_book)) {

            if (
                $current_area == 'C'
                && $main == 'checkout'
                && !$ship2diff
            ) {
                $address_book['S'] = $address_book['B'];
            }

            $saved_userinfo[$user]['address'] = $address_book;
        }

        if (
            !empty($active_modules['News_Management'])
            && is_array($subscription)
        ) {
            $saved_userinfo[$user]['subscription'] = $subscription;
        }

    }

    if (!empty($av_error)) {
        $errors = array();
        $errors[] = func_reg_error(13);
    }

    if (
        !empty($errors) 
        || !empty($addr_errors)
    ) {

        $error_text = func_get_langvar_by_name('txt_registration_errors', false, false, true);

        foreach ($errors as $err) {

            $error_text .= $err['error'] . '<br />';

        }

        // Prepare errors data
        $top_message = array(
            'content' => $error_text,
            'type'    => 'E',
        );

        if (!empty($errors)) {
            $reg_error = func_prepare_error($errors);
        }

        if (!empty($addr_errors)) {
            foreach ($addr_errors as $id => $err) {
                $reg_error['address'][$id] = func_prepare_error($err);
            }
        }

    } else {

        if (
            isset($new_user_flag)
            && true == $new_user_flag
        ) {

            // Profile is created
            $top_message['content'] = func_get_langvar_by_name('msg_profile_add', false, false, true);

        } else {

            if ($is_anonymous) {

                // Anonymous profile is updated
                $top_message['content'] = func_get_langvar_by_name('msg_anonymous_profile_upd', false, false, true);

            } else {

                // Profile is updated
                $top_message['content'] = func_get_langvar_by_name('msg_profile_upd', false, false, true);

            }

        }

        $saved_userinfo = array();

        // Create provider directory
        if (
            !$single_mode 
            && $usertype == 'P'
        ) {
            if (
                $mode != 'update'
                && $config['General']['provider_register_moderated'] == 'N'
            ) {
                
                func_mkdir(func_get_files_location($newuserid, $usertype));

            } elseif (
                defined('USER_MODIFY') 
                || defined('USER_ADD')
            ) {
                
                func_mkdir(func_get_files_location($user, $usertype));

            }
        }    
    }

    $script = basename($PHP_SELF) . '?' . $QUERY_STRING;
    settype($new_user_flag, 'bool');
    if (
        $new_user_flag
        && !defined('USER_ADD')
        && $current_area == 'C'
        && !isset($edit_profile)
        && $main != 'checkout'
    ) {
        // Redirect just registered customer to address book
        $script = 'address_book.php';
    }

    if (
        defined('USER_MODIFY')
        || defined('USER_ADD')
    ) {
        $login          = $login_;
        $logged_userid  = $logged_userid_;
        $login_type     = $login_type_;

        if (
            defined('USER_ADD')
            && !$reg_error
        ) {
            $script = 'user_modify.php?' . $QUERY_STRING . '&user=' . $newuserid;
        }

        if (
            $usertype == 'P'
            && $activity_changed
            && !$single_mode
        ) {
            $p_categories = db_query("SELECT $sql_tbl[products_categories].categoryid FROM $sql_tbl[products], $sql_tbl[products_categories] WHERE $sql_tbl[products].productid = $sql_tbl[products_categories].productid AND $sql_tbl[products].provider='$uname' GROUP BY $sql_tbl[products_categories].categoryid");

            if ($p_categories) {

                $cats = array();

                while ($row = db_fetch_array($p_categories)) {

                    $cats[] = $row['categoryid'];

                    if (count($cats) >= 100) {
                        func_recalc_product_count(func_array_merge($cats, func_get_category_parents($cats)));
                        $cats = array();
                    }

                }

                if (!empty($cats))
                    func_recalc_product_count(func_array_merge($cats, func_get_category_parents($cats)));

                db_free_result($p_categories);
            }
        }

    } elseif (isset($edit_profile)) {

        $script = 'cart.php?mode=checkout';

        if (!empty($paymentid)) {
            $script .= '&paymentid=' . intval($paymentid);
        }

        if (!empty($reg_error) || !empty($av_error)) {
            $script .= '&edit_profile';
        }

    } elseif (
        $current_area == 'C'
        && !empty($cart)
    ) {

        x_load('shipping');

        $shippings = func_get_shipping_methods_list($cart, $cart['products'], $userinfo);

        if (is_array($shippings)) {

            $found = false;
            $shippingid = $cart['shippingid'];

            for ($i = 0; $i < count($shippings); $i++) {
                if ($shippingid == $shippings[$i]['shippingid']) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $shippingid = $shippings[0]['shippingid'];
            }

        } else {

            $shippingid = 0;

        }

        $cart['shippingid'] = $shippingid;

        $products = func_products_in_cart(
            $cart,
            (!empty($userinfo['membershipid']) ? $userinfo['membershipid'] : 0)
        );

        $cart = func_array_merge(
            $cart,
            func_calculate(
                $cart,
                $products,
                $logged_userid,
                'C',
                (!empty($paymentid) ? intval($paymentid) : 0)
            )
        );

        // And again, because shippingid is not saved after func_calculate
        $cart['shippingid'] = $shippingid;

    } elseif (
        $current_area == 'B'
        && $login_type == 'B'
        && $newuser_info['status'] == 'Q'
    ) {

        $script = $xcart_catalogs['partner'] . "/home.php?mode=profile_created";

    } elseif (
        $current_area == 'P'
        && $login_type == 'P'
        && $newuser_info['status'] == 'Q'
    ) {
        $script = $xcart_catalogs['provider'] . '/home.php?mode=profile_created';
    }

    if (
        $current_area == 'C'
        && $main == 'checkout'
    ) {

        $_tmp = $address_book[$ship2diff ? 'S' : 'B'];

        $shipping_checksum = func_generate_checksum($_tmp, $shipping_checksum_fields);

        func_register_ajax_message(
            'opcUpdateCall',
            array(
                'action'    => 'profileUpdate',
                'status'    => empty($reg_error) ? 1 : 0,
                'error'     => $reg_error,
                'av_error'  => $av_error ? 1 : 0,
                'content'   => $top_message['content'],
                'new_user'  => $new_user_flag ? 1 : 0,
                's_changed' => $shipping_checksum != $shipping_checksum_init ? 1 : 0
            )
        );

        if (func_is_ajax_request()) {
            $reg_error = $top_message = array();
        }

    }

    func_header_location($script);

} else {

    /**
     * Process GET-request
     */

    if ($mode == 'update') {

        if (
            !empty($logged_userid)
            || $main == 'checkout'
        ) {

            $userinfo = func_userinfo($logged_userid, $login_type, $allow_pwd_modify, false, $fields_area, false);

        } elseif (!defined('USER_MODIFY')) {

            func_header_location('register.php');

        }

    } elseif (
        'delete' === $mode
        && 'POST' === $REQUEST_METHOD
        && 'Y' === $confirmed
        && !empty($logged_userid)
    ) {
        require $xcart_dir . '/include/safe_mode.php';

        $olduser_info = func_userinfo($logged_userid, $login_type, true, false, null, false);

        $to_customer = $olduser_info['language'];

        // Clear last working URL to avoid automatic profile deletion within current session.
        func_url_unset_last_working_url($login_type);

        // Remove profile from db
        func_delete_profile($logged_userid, $login_type, true, true, (isset($next_provider) ? $next_provider : false));

        if (in_array($login_type, array('P', 'A')))
            x_log_flag('log_activity', 'ACTIVITY', "'$login' user has deleted '$login' profile");

        $login = $login_type = $logged_userid = '';

        $smarty->clear_assign('login');
        $smarty->clear_assign('logged_userid');

        // Send mail notifications to customer department and signed customer
        $mail_smarty->assign('userinfo',$olduser_info);

        if ($config['Email_Note']['eml_profile_deleted'] == 'Y') {
            func_send_mail(
                $olduser_info['email'],
                'mail/profile_deleted_subj.tpl',
                'mail/profile_deleted.tpl',
                $config['Company']['users_department'],
                false
            );
        }

        // Send mail to customers department

        if ($config['Email_Note']['eml_profile_deleted_admin'] == 'Y') {
            func_send_mail(
                $config['Company']['users_department'],
                'mail/profile_admin_deleted_subj.tpl',
                'mail/profile_admin_deleted.tpl',
                $olduser_info['email'],
                true
            );
        }

        $top_message = array(
            'content' => func_get_langvar_by_name('txt_profile_deleted')
        );

        func_header_location('home.php');

    } // mode delete, confirmed

} // Request GET

if (
    !empty($active_modules['Special_Offers'])
    && $usertype == 'C'
    && (
        defined('USER_MODIFY')
        || defined('USER_ADD')
    )
) {
    include $xcart_dir.'/modules/Special_Offers/register_customer.php';
}

if (
    $current_area == 'C'
    && !empty($active_modules['UPS_OnLine_Tools'])
) {

    /**
     * Get the UPS OnLine Tools module settings
     */
    $params = func_query_first ("SELECT * FROM $sql_tbl[shipping_options] WHERE carrier='UPS'");

    $ups_parameters = unserialize($params['param00']);

    if (!is_array($ups_parameters)) {
        $ups_parameters['av_status'] = 'N';
    }

    $smarty->assign('av_enabled', $ups_parameters['av_status']);
}

if ($REQUEST_METHOD == 'GET') {

    /**
     * Restore user info from the $saved_userinfo session variable
     */

    if (
        is_numeric($user)
        && !empty($saved_userinfo)
        && !empty($saved_userinfo[$user])
    ) {
        $userinfo          = func_array_merge($userinfo, $saved_userinfo[$user]);
        $additional_fields = func_array_merge($saved_userinfo[$user]['additional_fields']);

        if (
            defined('USER_MODIFY')
            && !empty($_GET['user'])
            && defined('IS_ADMIN_USER')
        ) {
            $user_exists = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[customers] WHERE id = '" . $_GET["user"] . "'");

            if ($user_exists > 0) {
                $userinfo['login'] = $userinfo['uname'];
            }
        }
    }

    if (!empty($reg_error)) {
        $smarty->assign('reg_error', $reg_error);
        $reg_error = array();
    }

}

$ship2diff = false;

if (!empty($userinfo)) {

    if ($main == 'checkout') {

        func_adjust_customer_address($cart, $userinfo);

    }

    // Get address book information
    if (
        $current_area != 'C'
        || $main == 'checkout'
        || defined('USER_MODIFY')
    ) {

        if (!$is_anonymous) {

            $address_book = func_get_address_book($logged_userid);

            if (!empty($saved_address_book)) {

                foreach ($saved_address_book as $_id => $_data) {
                    if ($saved_address_book[$_id]) {
                        $address_book[$_id] = $saved_address_book[$_id];
                    }
                }

                $saved_address_book = array();
            }
        }

        if (
             $main == 'checkout'
             && (
                $is_anonymous
                || empty($address_book)
                || (
                    $is_areas['B']
                    && empty($userinfo['address']['B'])
                ) || (
                    $is_areas['S']
                    && empty($userinfo['address']['S'])
                )
            )
        ) {
            $need_address_info = true;
            $smarty->assign('need_address_info', true);
        }

        // Check if ship2diff section should be expanded

        if (
            !empty($userinfo['address'])
            && is_array($userinfo['address']['B'])
            && is_array($userinfo['address']['S'])
        ) {

            $b_address = $userinfo['address']['B'];
            $s_address = $userinfo['address']['S'];

            $addr_intersect = array_intersect_assoc($b_address, $s_address);

            if (
                empty($userinfo['address']['S'])
                || count($b_address) != count($s_address)
                || count($b_address) != count($addr_intersect)
            ) {
               $ship2diff = true;
            }

            if (
                !$ship2diff
                && $address_fields['zipcode']['avail'] == 'Y'
                && !empty($s_address['zipcode'])
                && !empty($s_address['country'])
            ) {
                $ship2diff = !func_check_zip($s_address['zipcode'], $s_address['country'], false);
            }
        }

        $smarty->assign('address_fields', $address_fields);
        if (!empty($address_book)) {
            $smarty->assign('address_book', $address_book);
        }

        $b_display_states = func_is_display_states(addslashes($b_address['country']));

        $s_display_states = (!$ship2diff)
            ? $b_display_states :
            func_is_display_states(addslashes($s_address['country']));

        $userinfo['address']['B']['display_states'] = $b_display_states;
        $userinfo['address']['S']['display_states'] = $s_display_states;

    }

    $smarty->assign('userinfo', $userinfo);

    if (
        $REQUEST_METHOD == 'GET'
        && !empty($active_modules['News_Management'])
    ) {
        if (empty($saved_userinfo[$user])) {

            $tmp = func_query("SELECT listid FROM $sql_tbl[newslist_subscription] WHERE email='" . addslashes($userinfo['email']) . "'");

            if (is_array($tmp)) {
                $subscription = array();
                foreach ($tmp as $v) {
                    $subscription[$v['listid']] = true;
                }
            }

        } else {

            $subscription = $saved_userinfo[$user]['subscription'];

        }
    }
}

$smarty->assign('ship2diff', $ship2diff);

if (isset($subscription)) {
    $smarty->assign('subscription', $subscription);
}

$newslists = func_query("SELECT * FROM $sql_tbl[newslists] WHERE avail='Y' AND subscribe='Y' AND lngcode='$shop_language'");
$smarty->assign('newslists', $newslists);

if ($allow_pwd_modify) {
    $smarty->assign('allow_pwd_modify', 'Y');
}

if (!empty($registered)) {
    $smarty->assign('registered', $registered);
}

if ($mode == 'delete') {

    if (empty($login)) {
        func_header_location('home.php');
    }

    $location[count($location)-1] = array(func_get_langvar_by_name('lbl_delete_profile'), '');

    $smarty->assign('main', 'profile_delete');

    if (in_array($login_type, array('A', 'P'))) {

        $smarty->assign('provider_counters',     func_get_provider_counters($logged_userid));
        $smarty->assign('is_provider_profile',     ($login_type == 'P' && !$single_mode));
        $smarty->assign('move_to_providers',     func_get_next_providers($logged_userid));

    }

} elseif ($mode == 'notdelete') {

    if (!empty($login))
        $top_message['content'] = func_get_langvar_by_name('txt_profile_not_deleted');

    func_header_location('home.php');

} else {

    $smarty->assign('main', 'register');
}

if (
    !empty($active_modules['XAffiliate'])
    && (
        (
            $mode == 'update'
            && $login_type == 'B'
        )
        || $current_area == 'B'
    )
) {
    $plans = func_query("SELECT * FROM $sql_tbl[partner_plans] WHERE status = 'A' ORDER BY plan_title");

    $smarty->assign('plans', $plans);
}

if (isset($_GET['parent'])) {
    $smarty->assign('parent', $parent);
}

if ($submode == 'seller_address') {

    $default_fields = array(
        'address' => array(
            'title'     => 'address',
            'field'     => 'address',
            'avail'     => 'Y',
            'required'     => 'N',
        ),
        'city' => array(
            'title'        => 'city',
            'field'        => 'city',
            'avail'        => 'Y',
            'required'     => 'Y',
        ),
        'state' => array(
            'title'     => 'state',
            'field'        => 'state',
            'avail'        => 'Y',
            'required'     => 'Y',
        ),
        'country' => array(
            'title'        => 'country',
            'field'        => 'country',
            'avail'        => 'Y',
            'required'     => 'Y',
        ),
        'zipcode' => array(
            'title'        => 'zipcode',
            'field'        => 'zipcode',
            'avail'        => 'Y',
            'required'     => 'Y',
        ),
    );

    func_unset($additional_fields);
}

if (
    !defined('USER_MODIFY')
    && !defined('USER_ADD')
    && !empty($active_modules['Image_Verification'])
) {
    x_session_register('antibot_reg_err');

    if ($antibot_reg_err) {

        $smarty->assign('reg_antibot_err', $antibot_reg_err);

        x_session_unregister('antibot_reg_err');
    }

    $smarty->assign('display_antibot', $display_antibot);
}

$smarty->assign('default_fields',    $default_fields);
$smarty->assign('additional_fields', $additional_fields);
$smarty->assign('is_areas',          $is_areas);
$smarty->assign('av_error',          $av_error);

$m_usertype = empty($_GET['usertype'])
    ? $current_area
    : $_GET['usertype'];

$membership_levels = func_get_memberships($m_usertype);

if (!empty($membership_levels)) {
    $smarty->assign('membership_levels', $membership_levels);
}

$smarty->assign('titles', func_get_titles());

x_session_save();
?>
