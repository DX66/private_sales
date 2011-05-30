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
 * Manage news related data
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: news_manage.php,v 1.27.2.1 2011/01/10 13:11:59 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }

x_load('mail');
x_session_register('validated_emails', array());
x_session_register('stored_newsemail');

$subscribe_lng = $store_language;
if (empty($mode)) {
    $mode = $REQUEST_METHOD == 'GET' ? 'archive' : 'view';
}

if ($REQUEST_METHOD == 'POST') {

    $subscribe_err = false;

    // Check email
    $email = trim($newsemail);
    if (!func_check_email($email)) {
        $top_message = array(
            'type' => 'E',
            'content' => func_get_langvar_by_name('err_subscribe_email_invalid')
        );
        $subscribe_err = true;
    }

    $listids = func_query_column("SELECT $sql_tbl[newslists].listid FROM $sql_tbl[newslists], $sql_tbl[newslist_subscription] WHERE $sql_tbl[newslists].listid=$sql_tbl[newslist_subscription].listid AND $sql_tbl[newslists].lngcode='$subscribe_lng' AND $sql_tbl[newslist_subscription].email='$email'");

    if (!empty($listids)) {
        db_query("DELETE FROM $sql_tbl[newslist_subscription] WHERE email='$email' AND listid IN ('".implode("','", $listids)."')");
    }

    // Get the newslists
    $lists = func_query_column("SELECT listid FROM $sql_tbl[newslists] WHERE avail = 'Y' AND subscribe = 'Y' AND lngcode = '$subscribe_lng'");
    if (!is_array($lists) || empty($lists)) {
        $top_message['type'] = 'I';
        $top_message['content'] = func_get_langvar_by_name('lbl_no_subscr_news');
        $subscribe_err = true;
    }

    // Image verification feature
    if ($mode == 'view' && !empty($active_modules['Image_Verification']) && func_validate_image("on_news_panel", $antibot_input_str)) {
        $top_message = array(
            'type' => 'E',
            'content' => func_get_langvar_by_name('msg_err_antibot')
        );
        $subscribe_err = true;
    }

    if ($subscribe_err) {
        $stored_newsemail = $email;
        func_header_location(func_is_internal_url($HTTP_REFERER) ? $HTTP_REFERER : 'news.php');
    }

    $news_lists_num = count($lists);
    if ($news_lists_num > 1 && $mode == 'view') {
        $validated_emails[] = stripslashes($email);
        func_header_location("news.php?mode=list&email=".urlencode(stripslashes($email)));
    }

    if ($mode == 'view' || ($mode == "subscribe" && in_array(stripslashes($email), $validated_emails))) {
        if ($news_lists_num == 1) {
            $s_lists = $lists;
        } elseif (empty($s_lists) || !is_array($s_lists)) {
            func_header_location('news.php');
        }

        foreach ($s_lists as $listid) {
            func_array2insert('newslist_subscription', array('listid' => $listid, 'email' => $email, 'since_date' => XC_TIME));
        }

        $saved_lng = $shop_language;

        // Send mail notification to customer

        $mail_smarty->assign('email', stripslashes($email));
        if ($config['News_Management']['eml_newsletter_subscribe'] == 'Y') {
            $shop_language = $subscribe_lng;
            func_send_mail($email, 'mail/newsletter_subscribe_subj.tpl', 'mail/newsletter_subscribe.tpl', $config['News_Management']['newsletter_email'], false);
        }

        // Send mail notification to admin

        if ($config['News_Management']['eml_newsletter_subscribe_admin'] == 'Y') {
            $shop_language = '';
            func_send_mail($config['News_Management']['newsletter_email'], 'mail/newsletter_admin_subj.tpl', 'mail/newsletter_admin.tpl', $email, true);
        }

        $shop_language = $saved_lng;

        func_header_location("news.php?mode=subscribed&email=".urlencode(stripslashes($email)));
    }
}

if ($REQUEST_METHOD == 'GET' && $mode == 'list') {
    if (empty($email) || !in_array(stripslashes($email), $validated_emails) || !func_check_email($email))
        func_header_location('news.php');

    $lists = func_query("SELECT * FROM $sql_tbl[newslists] WHERE avail='Y' AND subscribe='Y' AND lngcode='$subscribe_lng'");
    if (!is_array($lists) || empty($lists)) {
        $top_message['type'] = 'I';
        $top_message['content'] = func_get_langvar_by_name('lbl_no_subscr_news');
        func_header_location('news.php');
    }

    $location[] = array(func_get_langvar_by_name('lbl_news_subscribe_to_newslists'), '');
    $smarty->assign('main', 'news_lists');
    $smarty->assign('lists', $lists);
    $smarty->assign('newsemail', $email);

} else {

    // Show the news from archive

    $location[] = array(func_get_langvar_by_name('lbl_news_archive'), '');

    $total_items = func_news_get($shop_language, false, true);
    $objects_per_page = $config['News_Management']['newsletter_limit'];

    include $xcart_dir.'/include/navigation.php';

    $smarty->assign('main', 'news_archive');

    $smarty->assign('news_messages', func_news_get($shop_language, false, false, "$first_page, $objects_per_page"));
    $smarty->assign('navigation_script', "news.php?");

    if (!empty($stored_newsemail)) {
        $smarty->assign('newsemail', $stored_newsemail);
        $stored_newsemail = false;
    }
}

$smarty->assign('location', $location);

?>
