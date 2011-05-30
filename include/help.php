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
 * Help section actions processor
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: help.php,v 1.101.2.5 2011/04/13 09:54:14 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('crypt','mail','user','templater','pages');

$location[] = array(func_get_langvar_by_name('lbl_help_zone'), 'help.php');

if (!empty($logged_userid)) {

    $userinfo = func_userinfo($logged_userid, $login_type);

}

if (empty($section))
    $section = '';

if (empty($action))
    $action = '';

if ($action == 'contactus' || $section == 'contactus') {

    x_session_register('store_contactus');

    $additional_fields = func_get_add_contact_fields($current_area);

    $default_fields = func_get_default_fields($current_area, 'contact_us');

    $is_areas = array(
        'C' => (
            !empty($default_fields['title']['avail']) ||
            !empty($default_fields['firstname']['avail']) ||
            !empty($default_fields['lastname']['avail']) ||
            !empty($default_fields['company']['avail'])
        ),
        'A' => (
            !empty($default_fields['b_address']['avail']) ||
            !empty($default_fields['b_address_2']['avail']) ||
            !empty($default_fields['b_city']['avail']) ||
            !empty($default_fields['b_county']['avail']) ||
            !empty($default_fields['b_state']['avail']) ||
            !empty($default_fields['b_country']['avail']) ||
            !empty($default_fields['b_zipcode']['avail']) ||
            !empty($default_fields['phone']['avail']) ||
            !empty($default_fields['fax']['avail']) ||
            !empty($default_fields['email']['avail']) ||
            !empty($default_fields['url']['avail'])
        ),
    );

    include $xcart_dir . '/include/states.php';
    include $xcart_dir . '/include/countries.php';

    if ($config['General']['use_counties'] == 'Y') {
        include $xcart_dir . '/include/counties.php';
    }

    if (isset($err) && !empty($store_contactus)) {
        $userinfo = $store_contactus;
        if ($userinfo['fillerror'])
            $fillerror = $userinfo['fillerror'];

        if ($userinfo['antibot_contactus_err'])
            $antibot_contactus_err = $userinfo['antibot_contactus_err'];

        func_unset($userinfo, 'fillerror', 'antibot_contactus_err');
    }
}

if ($REQUEST_METHOD == 'POST' && $action == 'contactus') {

    // Send mail to support

    $body = $_POST['body'] = stripslashes($_POST['body']);

    $contact = $_POST;

    $contact['titleid'] = func_detect_title($contact['title']);

    x_session_register('antibot_contactus_err');
    $antibot_contactus_err = (!empty($active_modules['Image_Verification']) && func_validate_image("on_contact_us", $antibot_input_str));

    // For email as login option, replace username with email
    if ($default_fields['username']['avail'] == 'Y' && $config['email_as_login'] == 'Y') {
        $contact['username'] = $contact['email'];
    }

    //Fill values for func_check_required_fields/emails templates
    if (is_array($additional_fields)) {
        $contact['additional_fields'] = array();
        foreach ($additional_fields as $k => $v) 
            $additional_fields[$k]['value'] = $contact['additional_fields'][$k]['value'] = stripslashes($contact['additional_values'][$k]);
    }


    // Check required fields
    $fillerror = !func_check_required_fields($contact, $current_area, 'contact_us');

    // Check email
    if (
        (
            $default_fields['email']['required'] == 'Y'
            || !empty($contact['email'])
        )
        && !func_check_email($contact['email'])
    ) {
        $fillerror = true;
    }

    // Check subject and body
    if (!$fillerror) {
        $fillerror = (empty($subject) || empty($body));
    }

    if (!$fillerror && !$antibot_contactus_err) {

        $contact['b_statename'] = func_get_state(stripslashes($contact['b_state']), stripslashes($contact['b_country']));
        $contact['b_countryname'] = func_get_country(stripslashes($contact['b_country']));

        if ($config['General']['use_counties'] == 'Y')
            $contact['b_countyname'] = func_get_county($contact['b_county']);

        $contact = func_stripslashes($contact);

        if (!empty($active_modules['SnS_connector']) && $current_area == 'C')
            func_generate_sns_action('FillContactForm');

        $mail_smarty->assign('contact', $contact);
        $mail_smarty->assign('default_fields', $default_fields);
        $mail_smarty->assign('is_areas', $is_areas);
        $mail_smarty->assign('additional_fields', $additional_fields);

        if (
            !func_send_mail(
                $config['Company']['support_department'],
                'mail/help_contactus_subj.tpl',
                'mail/help_contactus.tpl',
                $contact['email'],
                false
            )
        ) {
            $top_message = array(
                'type'         => 'E',
                'content'     => func_get_langvar_by_name("lbl_send_mail_error")
            );

            $userinfo = $_POST;
            $userinfo['login'] = $userinfo['uname'];
            $store_contactus = $userinfo;

        } else {

            $store_contactus = false;
            func_header_location("help.php?section=contactus&mode=sent");

        }

    } else {

        $userinfo                          = func_stripslashes($_POST);
        $userinfo['login']                 = $userinfo['uname'];
        $userinfo['fillerror']             = $fillerror;
        $userinfo['antibot_contactus_err'] = $antibot_contactus_err;

        $store_contactus = $userinfo;

        $top_message = array(
            'type'    => 'E',
            'content' => func_get_langvar_by_name($fillerror ? 'txt_registration_error' : 'msg_err_antibot')
        );

    }

    func_header_location("help.php?section=contactus&mode=update&err=1");
}

/**
 * Recover password
 */
if (
    $REQUEST_METHOD == 'POST'
    && $action == 'recover_password'
    && !empty($username)
) {

    $utype = !empty($active_modules['Simple_Mode']) && $current_area == 'A' ? 'P' : $current_area;
    $account = func_query_first("SELECT id, login, password, email, usertype FROM $sql_tbl[customers] WHERE login='$username' AND usertype='$utype' AND status='Y'");

    if (empty($account)) {

        func_header_location('help.php?section=Password_Recovery_error&username=' . urlencode(stripslashes($username)));

    }

    db_query("LOCK TABLES $sql_tbl[change_password] WRITE");

    db_query("DELETE FROM $sql_tbl[change_password] WHERE userid='$account[id]'");

    do {

        $account['password_reset_key'] = md5(uniqid(rand(),true));

    } while (func_query_first_cell("SELECT COUNT(password_reset_key) FROM $sql_tbl[change_password] WHERE userid='$account[id]' AND password_reset_key='$account[password_reset_key]'"));

    func_array2insert(
        'change_password',
        array(
            'userid' => $account['id'],
            'password_reset_key' => $account['password_reset_key'],
            'password_reset_key_date' => XC_TIME
        ),
        true
    );

    db_query("UNLOCK TABLES");

    switch ($account['usertype']) {
        case 'A':
            $userpath = DIR_ADMIN;
            break;

        case 'P':
            $userpath = !empty($active_modules['Simple_Mode']) ? DIR_ADMIN : DIR_PROVIDER;
            break;

        case 'B':
            $userpath = DIR_PARTNER;
            break;

        default:
            $userpath = DIR_CUSTOMER;
    }

    $mail_smarty->assign('account', $account);
    $mail_smarty->assign('userpath', $userpath);

    func_send_mail($account['email'], 'mail/password_recover_subj.tpl', 'mail/password_recover.tpl', $config['Company']['support_department'], false);

    func_header_location('help.php?section=Password_Recovery_message&email=' . urlencode(stripslashes($account['email'])));

} elseif (
    $REQUEST_METHOD == 'POST'
    && $action == 'recover_password'
    && empty($username)
) {

    $top_message['type']    = 'E';
    $top_message['content'] = func_get_langvar_by_name('txt_registration_error');

    func_header_location("help.php?section=Password_Recovery_error&username=" . urlencode(stripslashes($username)));

}

$pageid = -1;
switch ($section) {
    case 'Password_Recovery':
    case 'Password_Recovery_error':
        $location[] = array(func_get_langvar_by_name('lbl_forgot_password'), '');
        break;

    case 'Password_Recovery_message':
        $location[] = array(func_get_langvar_by_name('lbl_confirmation'), '');
        break;

    case 'FAQ':
        $location[] = array(func_get_langvar_by_name('lbl_faq'), '');
        $pageid = func_get_pageid_by_alias($section);
        break;

    case 'contactus':
        $location[] = array(func_get_langvar_by_name('lbl_contact_us'), '');
        break;

    case 'about':
        $location[] = array(func_get_langvar_by_name('lbl_about_our_site'), '');
        $pageid = func_get_pageid_by_alias($section);
        break;

    case 'business':
        $location[] = array(func_get_langvar_by_name('lbl_privacy_statement'), '');
        $pageid = func_get_pageid_by_alias($section);
        break;

    case 'conditions':
        $location[] = array(func_get_langvar_by_name('lbl_terms_n_conditions'), '');
        $pageid = func_get_pageid_by_alias($section);
        break;

    case 'publicity':
        $location[] = array(func_get_langvar_by_name('lbl_publicity'), '');
        $pageid = func_get_pageid_by_alias($section);
        break;
}

if ($current_area == 'C') {

    if ($pageid > 0) {

        func_header_location('pages.php?pageid=' . $pageid);

    } elseif (!$pageid) {

        func_page_not_found();

    }

    if (!empty($active_modules['SnS_connector']) && $section != 'contactus') {

        func_generate_sns_action(($section == 'business' || $section == 'conditions') ? "ViewLegalInfo" : "ViewHelp");

    }

}

if (isset($username)) {
    $username = preg_replace(
        '/' . func_login_validation_regexp(true) . '/s',
        '',
        stripslashes($username)
    );
    $smarty->assign('username', $username);
}

if (isset($email)) {
    $email = stripslashes($email);
    if (func_check_email($email)) {
        $smarty->assign('email', $email);
    }
}

if (
    !empty($active_modules['Image_Verification'])
    && !empty($antibot_contactus_err)
) {

    $smarty->assign('antibot_contactus_err', @$antibot_contactus_err);
    x_session_unregister('antibot_contactus_err');
}

$smarty->assign('userinfo',             @$userinfo);
$smarty->assign('fillerror',            @$fillerror);
$smarty->assign('default_fields',       @$default_fields);
$smarty->assign('additional_fields',    @$additional_fields);
$smarty->assign('prepare_fields',       func_wm_tpl_prep(@$vid));
$smarty->assign('titles',               func_get_titles());
$smarty->assign('help_section',         @$section);
$smarty->assign('main',                 'help');

$smarty->clear_assign('location');

?>
