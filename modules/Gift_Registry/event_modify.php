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
 * Giftregistry event management
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: event_modify.php,v 1.67.2.3 2011/01/10 13:11:57 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

x_load('cart','files','mail','user');

/**
 * This function generate the unique confirmation code for sending to recipient
 */
function func_get_confirmation_code()
{
    global $sql_tbl;

    while (true) {
        $confid = strtoupper(md5(uniqid(rand())));
        if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[giftreg_maillist] WHERE confirmation_code='$confid'") == 0) {
            break;
        }
    }

    return $confid;
}

x_session_register('mail_data');

if ($mode != 'send')
    x_session_unregister('mail_data');

if ($REQUEST_METHOD == 'POST') {

    // Process the POST request

    if ($mode == 'delete' && is_array($ids) && !empty($ids)) {

        $ids = func_query_column("SELECT event_id FROM $sql_tbl[giftreg_events] WHERE event_id IN ('" . implode("','", $ids) . "') AND userid = '".addslashes($logged_userid)."'");

        if (!empty($ids)) {

            // Delete events
            db_query("DELETE FROM $sql_tbl[giftreg_events] WHERE event_id IN ('" . implode("','", $ids) . "') AND userid = '".addslashes($logged_userid)."'");
            db_query("DELETE FROM $sql_tbl[giftreg_maillist] WHERE event_id IN ('" . implode("','", $ids) . "')");
            db_query("DELETE FROM $sql_tbl[giftreg_guestbooks] WHERE event_id IN ('" . implode("','", $ids) . "')");
            db_query("DELETE FROM $sql_tbl[wishlist] WHERE event_id IN ('" . implode("','", $ids) . "') AND userid = '".addslashes($logged_userid)."'");

        }

        func_header_location('giftreg_manage.php');

    }

    if ($mode == 'send') {

        // Send notification about event

        $mail_data['message'] = func_html_entity_decode(stripslashes($posted_mail_message));
        $mail_data['subj'] = func_html_entity_decode(stripslashes($posted_mail_subj));

        $mail_data['message'] = func_xss_free($mail_data['message'], false, true);
        $mail_data['subj'] = func_xss_free($mail_data['subj'], false, true);

        if ($action == 'preview') {

            $smarty->assign('mail_data',$mail_data);
            $smarty->assign('display_only_body', true);

            if ($config['Email']['html_mail'] == 'Y') {

                $mail_prefix = ($config['Email']['html_mail'] == 'Y') ? 'html/' : '';
                $smarty->assign('mail_body_template', 'mail/html/giftreg_notification.tpl');

                func_display('mail/html/html_message_template.tpl',$smarty);

            } else {

                func_display('mail/giftreg_notification.tpl',$smarty);

            }

            exit;

        } else {

            // Send notifications
            x_session_register('do_sending', 'Y');

            $mail_data['message'] = $mail_data['message'];

            func_header_location("giftreg_manage.php?eventid=$eventid&mode=send");
        }

    }

    if ($mode == 'maillist') {

        // Update mailing list

        if ($config['Gift_Registry']['hide_import_export_recipients'] != 'Y') {

            if ($action == 'export') {

                // Export recipients list

                $mailing_list = func_query("SELECT * FROM $sql_tbl[giftreg_maillist] WHERE event_id='$eventid' ORDER BY recipient_name, recipient_email");
                if (is_array($mailing_list)) {

                    header("Content-type: text/csv");
                    header("Content-disposition: attachment; filename=recipients.csv");

                    foreach ($mailing_list as $k=>$v) {

                        $recipient_name = preg_replace("/\r\n|\n|\r/Ss", " ", func_html_entity_decode($v['recipient_name']));

                        if (@preg_match("/(".preg_quote($delimiter, '/').")|\t/S", $recipient_name)) {

                            $recipient_name = '"'.str_replace('"', '""', $recipient_name).'"';

                            if (substr($recipient_name, -2) == '\"' && preg_match('/[^\\\](\\\+)"$/Ss', $recipient_name, $preg) && strlen($preg[1]) % 2 != 0) {
                                $recipient_name = substr($recipient_name, 0, -2)."\\".substr($recipient_name, -2);
                            }
                        }
                        echo $recipient_name.$delimiter.addslashes($v['recipient_email'])."\n";
                    }
                }

                exit;

            } elseif ($action == 'import') {

                // Import recipients list

                $userfile = func_move_uploaded_file('userfile');

                if ($userfile !== false) {
                    $fp = func_fopen($userfile, 'r', true);
                    $counter = 0;
                    while ($row = fgetcsv ($fp, 65536, $delimiter)) {
                        $columns[] = $row;
                    }

                    if (is_array($cols)) {
                        foreach($cols as $k => $v) {
                            $k = intval($k);
                            if ($v == 'recipient_name')
                                $recipient_name_index = $k;
                            elseif ($v == 'recipient_email')
                                $recipient_email_index = $k;
                        }
                    }

                    if (is_array($columns)) {
                        $recipients_count = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[giftreg_maillist] WHERE event_id='$eventid'");
                        foreach ($columns as $k => $v) {
                            $email = $v[$recipient_email_index];

                            if (func_check_email($email)) {
                                if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[giftreg_maillist] WHERE recipient_email='".addslashes($email)."' AND event_id='$eventid'") > 0) {
                                    if (empty($override))
                                        continue;

                                    $data = array(
                                        'recipient_name' => addslashes(htmlspecialchars($v[$recipient_name_index], ENT_QUOTES)),
                                        'status' => 'P'
                                    );

                                    func_array2update('giftreg_maillist', $data ,"recipient_email = '".addslashes($email)."' AND event_id = '$eventid'");
                                } elseif ($recipients_count < $config['Gift_Registry']['recipients_limit']) {
                                    $confid = func_get_confirmation_code();
                                    $data = array(
                                        'event_id' => $eventid,
                                        'status_date' => XC_TIME,
                                        'confirmation_code' => $confid,
                                        'status' => 'P'
                                    );

                                    foreach ($v as $fk => $fv) {
                                        $data[$cols[$fk]] = addslashes(htmlspecialchars($fv, ENT_QUOTES));
                                    }

                                    func_array2insert('giftreg_maillist', $data);
                                    $recipients_count++;
                                }
                            }
                        }
                    }

                    fclose($fp);
                    @unlink($userfile);

                } else {
                    die("Error of opening file $userfile");
                }

                x_session_register('message', 'maillist_imported');
                func_header_location("giftreg_manage.php?eventid=$eventid&mode=maillist");

            }

        }  elseif ($action == 'import' || $action == 'export') {

            $top_message['type'] = 'E';
            $top_message['content'] = func_get_langvar_by_name('err_giftreg_import_export_disabled');

            func_header_location("giftreg_manage.php?eventid=$eventid&mode=maillist");

        }

        // Process existing recipients

        $recipient_details_err_emails = array();

        if (is_array($recipient_details)) {

            foreach ($recipient_details as $k=>$v) {

                $k = intval($k);

                if (!empty($v['checked'])) {

                    if ($action == 'delete') {

                        // Delete recipients from the mailing list
                        db_query("DELETE FROM $sql_tbl[giftreg_maillist] WHERE regid='$k' AND event_id='$eventid'");

                    } elseif ($action == 'send_conf') {

                        func_safe_mode();

                        // Send confirmation request to the recipients
                        $recipient_data = func_query_first("SELECT * FROM $sql_tbl[giftreg_maillist] WHERE regid='$k' AND event_id='$eventid'");

                        if (!func_check_email($recipient_data['recipient_email'])) {
                            $top_message['type'] = 'E';
                            $top_message['content'] = func_get_langvar_by_name('err_subscribe_email_invalid');
                            func_header_location("giftreg_manage.php?eventid=$eventid&mode=maillist");
                        }

                        $event_data = func_query_first("SELECT * FROM $sql_tbl[giftreg_events] WHERE userid='$logged_userid' AND event_id='$eventid'");

                        $mail_smarty->assign('userinfo', $user_account);
                        $mail_smarty->assign('recipient_data',$recipient_data);
                        $mail_smarty->assign('confirmation_code', md5($recipient_data['confirmation_code'].'_confirmed'));
                        $mail_smarty->assign('decline_code', md5($recipient_data['confirmation_code'].'_declined'));
                        $mail_smarty->assign('event_data',$event_data);
                        $mail_smarty->assign('http_customer_location', $http_location.DIR_CUSTOMER);

                        $from_mail = func_query_first_cell("SELECT email FROM $sql_tbl[customers] WHERE id='$logged_userid'");

                        func_send_mail($recipient_data['recipient_email'], 'mail/giftreg_confirmation_subj.tpl', 'mail/giftreg_confirmation.tpl', $from_mail, false);
                        db_query("UPDATE $sql_tbl[giftreg_maillist] SET status='S', status_date='".XC_TIME."' WHERE regid='$k' AND event_id='$eventid'");
                    }
                }

                // Update recipients data

                $email = trim($v['recipient_email']);
                if (!func_check_email($email) || (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[giftreg_maillist] WHERE recipient_email='".addslashes($email)."' AND event_id='$eventid' AND regid <> '$k'") > 0)) {
                    $recipient_email = func_query_first_cell("SELECT recipient_email FROM $sql_tbl[giftreg_maillist] WHERE regid='$k' AND event_id='$eventid'");
                    if ($recipient_email)
                        $recipient_details_err_emails[] = $recipient_email;

                } elseif (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[giftreg_maillist] WHERE recipient_email='".addslashes($email)."' AND event_id='$eventid'")) {
                    db_query("UPDATE $sql_tbl[giftreg_maillist] SET recipient_name='".$v["recipient_name"]."' WHERE regid='$k' AND event_id='$eventid'");

                } else {
                    db_query("UPDATE $sql_tbl[giftreg_maillist] SET recipient_name='".$v["recipient_name"]."', recipient_email='".addslashes($email)."', status='P' WHERE regid='$k' AND event_id='$eventid'");
                }
            }
        }

        if (!empty($new_recipient_name) && !empty($new_recipient_email)) {

            // Add new member into the recipients list

            if (!func_check_email($new_recipient_email)) {
                $top_message['type'] = 'E';
                $top_message['content'] = func_get_langvar_by_name('err_wrong_email');
                func_header_location("giftreg_manage.php?eventid=$eventid&mode=maillist");
            }

            $recipients_count = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[giftreg_maillist] WHERE event_id='$eventid'");
            $is_exists = (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[giftreg_maillist] WHERE event_id = '$eventid' AND recipient_email = '$new_recipient_email'") > 0);

            if (!$is_exists && $recipients_count < $config['Gift_Registry']['recipients_limit']) {

                $confid = func_get_confirmation_code();

                db_query("INSERT INTO $sql_tbl[giftreg_maillist] (event_id, recipient_name, recipient_email, status, status_date, confirmation_code) VALUES('$eventid', '$new_recipient_name', '$new_recipient_email', 'P', '".XC_TIME."', '$confid')");

            }

        }

        if (count($recipient_details_err_emails) > 0) {

            if (isset($top_message['content'])) {

                $top_message['content'] .= '<br /><br />' . func_get_langvar_by_name("txt_giftreg_wrong_emails_warn");

            } else {

                $top_message = array(
                    'type' => 'E',
                    'content' => func_get_langvar_by_name("txt_giftreg_wrong_emails_warn")
                );
            }

            $top_message['recipients_wrong_emails'] = $recipient_details_err_emails;

        }

        func_header_location("giftreg_manage.php?eventid=$eventid&mode=maillist");
    }

    // Update event details

    if (is_array($event_details)) {

        $event_details['event_date'] = func_prepare_search_date($event_date);

        if (empty($event_details['title']))
            $error = 'fill_error';

        if (empty($error)) {
            if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[giftreg_events] WHERE event_id='$eventid'") == 0) {

                // Create new event list

                $count = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[giftreg_events] WHERE userid='$logged_userid'");
                if ($count < $config['Gift_Registry']['events_lists_limit']) {
                    db_query("INSERT INTO $sql_tbl[giftreg_events] (userid) VALUES ('$logged_userid')");
                    $eventid = db_insert_id();

                } else {
                    $top_message = array(
                        'type' => 'E',
                        'content' => func_get_langvar_by_name('err_giftreg_event_warning')
                    );
                    func_header_location('giftreg_manage.php');
                }
            }

            if (empty($error)) {
                func_array2update('giftreg_events', $event_details, "event_id = '$eventid'");
            }
        }
    }

    if (!empty($error)) {
        x_session_register('error_sess', $error);
    }

    x_session_register('event_details_sess', $event_details);

    func_header_location("giftreg_manage.php?eventid=".(empty($eventid) ? 'new' : $eventid));
} #/ if ($REQUEST_METHOD == 'POST')


/**
 * Process the GET request
 */

/**
 * Get the events list information
 */
$events_list = func_giftreg_get_events_list($logged_userid, true);

/**
 * Get the current event data
 */
if (is_array($events_list) && !empty($eventid)) {

    foreach ($events_list as $k=>$v) {

        if (!empty($eventid) && $eventid == $v['event_id']) {

            $event_data = $events_list[$k];

            $smarty->assign('eventid', $eventid);

            $current_event_index = $k;

        }

    }

}

if (!empty($eventid)) {

    if (!in_array($mode, array('maillist','products','send','gb')))
        $mode = 'modify';

    if ($mode == 'maillist') {

        // Get the mail list information

        $mailing_list = func_query("SELECT * FROM $sql_tbl[giftreg_maillist] WHERE event_id='$eventid' ORDER BY recipient_name, recipient_email");

        $recipients_count = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[giftreg_maillist] WHERE event_id='$eventid'");

        if ($recipients_count >= $config['Gift_Registry']['recipients_limit'])
            $smarty->assign('recipients_limit_reached', 'Y');

        $_top_message = $smarty->get_template_vars('top_message');
        if (
            $mailing_list 
            && $_top_message
            && $_top_message['recipients_wrong_emails'] 
            && is_array($_top_message['recipients_wrong_emails'])
        ) {
            foreach ($mailing_list as $k => $v) {
                if (in_array($v['recipient_email'], $_top_message['recipients_wrong_emails']))
                    $mailing_list[$k]['is_error'] = true;
            }
        }

        $columns = array('recipient_name', 'recipient_email');
        $show = 'maillist';

        $smarty->assign('columns', $columns);
        $smarty->assign('mailing_list', $mailing_list);
    }

    if (empty($event_data) && $mode != 'send') {

        // Display the error if creates new event but limit is reached

        $count = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[giftreg_events] WHERE userid='$logged_userid'");
        if ($count >= $config['Gift_Registry']['events_lists_limit']) {
            $top_message = array(
                'type' => 'E',
                'content' => func_get_langvar_by_name('err_giftreg_event_warning')
            );
            func_header_location('giftreg_manage.php');
        }
    }

    if ($mode == 'products' || $mode == 'send') {

        // Get the products information

        $wl_raw = func_query("select * from $sql_tbl[wishlist] WHERE userid='$logged_userid' AND event_id='$eventid' AND productid > '0'");

        if (is_array($wl_raw)) {

            foreach ($wl_raw as $index => $wl_product) {

                $wl_raw[$index]['options'] = unserialize($wl_product['options']);

                $wl_raw[$index]['amount_requested'] = $wl_product['amount'];
                $remain = $wl_raw[$index]['amount_remain'] = max($wl_product['amount'] - $wl_product['amount_purchased'],0);

                if ($remain > 0 && $mode == 'friend_wl')
                    $wl_raw[$index]['amount'] = $remain;
            }

            $wl_products = func_products_from_scratch($wl_raw, $user_account['membershipid'], true );

            if (!empty($active_modules['Subscriptions'])) {
                if (!function_exists('SubscriptionProducts'))
                    if (
                        file_exists($xcart_dir.'/modules/Subscriptions/subscription.php')
                        && is_readable($xcart_dir.'/modules/Subscriptions/subscription.php')
                    ) {
                        include $xcart_dir . '/modules/Subscriptions/subscription.php';
                    }

                $wl_products = SubscriptionProducts($wl_products);
            }

            if (!empty($active_modules['Product_Configurator'])) {
                include $xcart_dir . '/modules/Product_Configurator/pconf_customer_wishlist.php';
            }

            $smarty->assign('wl_products', $wl_products);

        }

        if (!empty($active_modules['Gift_Certificates'])) {

            $wl_raw = func_query("select wishlistid, amount, amount_purchased, object from $sql_tbl[wishlist] WHERE userid='$logged_userid' AND event_id='$eventid' AND productid='0'");

            if (is_array($wl_raw)) {

                foreach ($wl_raw as $k=>$v) {

                    $object = unserialize($v['object']);

                    $wl_giftcerts[] = func_array_merge($v, $object);

                }

                $smarty->assign('wl_giftcerts', $wl_giftcerts);

            }

        }

        $show = 'products';
    }

    if ($mode == 'send') {

        // Prepare/send e-mail notifications to the recipients

        if (empty($mail_data['message'])) {
            $smarty->assign('wlid', $logged_userid);
            $smarty->assign('display_only_body', true);
            $mail_prefix = ($config['Email']['html_mail'] == 'Y') ? 'html/' : '';
            $mail_data['message'] = func_display('mail/'.$mail_prefix.'giftreg_notification.tpl',$smarty,false);
            $mail_data['subj'] = func_get_langvar_by_name('eml_giftreg_notification_subj');
        }
        $smarty->assign('mail_data', $mail_data);
        $smarty->assign('userinfo', func_userinfo($logged_userid, 'C'));

        if (x_session_is_registered('do_sending')) {

            // Send notification to all confirmed recipients

            x_session_unregister('do_sending');

            $mail_smarty = $smarty;
            $mailing_list = func_query("SELECT * FROM $sql_tbl[giftreg_maillist] WHERE event_id='$eventid' AND status='Y'");
            $mail_smarty->assign('mail_data',$mail_data);
            foreach ($mailing_list as $k => $v) {
                if (func_send_mail($v['recipient_email'], 'mail/giftreg_notification_subj.tpl', 'mail/giftreg_notification.tpl', $config['Company']['company_mail_from'], false))
                    $recipients_sent[] = $v;
            }

            db_query("UPDATE $sql_tbl[giftreg_events] SET sent_date='".XC_TIME."' WHERE event_id='$eventid' AND userid='$logged_userid'");
            $smarty->assign('recipients_sent', $recipients_sent);
            x_session_register('message', 'notification_sent');
            x_session_unregister('mail_data');
            $events_list[$current_event_index]['sent_date'] = XC_TIME;
        }
    }
}

if (x_session_is_registered('message')) {
    x_session_register('message');
    $smarty->assign('message', $message);
    x_session_unregister('message');
}

if (x_session_is_registered('event_details_sess')) {
    x_session_register('event_details_sess');
    $event_data = func_array_map('stripslashes', $event_details_sess);
    x_session_unregister('event_details_sess');
}

if (!empty($event_data))
    $smarty->assign('event_data', $event_data);

if (x_session_is_registered('error_sess')) {
    x_session_register('error_sess');
    $smarty->assign('error', $error_sess);
    x_session_unregister('error_sess');
}

if (!in_array(@$show, array('events_list')))
    $show = 'events_list';

$smarty->assign('allow_edit', 'Y');

if (!empty($events_list))
    $smarty->assign('events_list', $events_list);

$smarty->assign('events_lists_count', (is_array($events_list) ? count($events_list) : 0));
$smarty->assign('main_mode', 'manager');
$smarty->assign('mode', $mode);
$smarty->assign('main','giftreg');

?>
