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
 * Process news related operations
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: news.php,v 1.52.2.2 2011/01/24 15:36:35 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }

x_load('files');

x_session_register('search_data');

if (!in_array($mode, array('create', 'update', 'modify', 'import', 'messages', 'subscribers', 'delete', 'archive', 'unsubscribers')))
    $mode = '';

settype($targetlist, 'int');

if ($REQUEST_METHOD == 'POST' || ($mode == 'messages' && $action == 'send_continue')) {

    if ($mode == 'delete' && !empty($to_delete)) {

        // Delete all information associated with selected newslist

        if (is_array($to_delete)) {
            foreach ($to_delete as $k=>$v) {
                db_query("DELETE FROM $sql_tbl[newslist_subscription] WHERE listid='$k'");
                db_query("DELETE FROM $sql_tbl[newslists] WHERE listid='$k'");
                db_query("DELETE FROM $sql_tbl[newsletter] WHERE listid='$k'");
            }

            $top_message['content'] = func_get_langvar_by_name('msg_adm_newslists_del');
        }
    }

    if ($mode == 'update') {

        // Update news lists

        if (is_array($posted_data)) {
            foreach ($posted_data as $listid=>$v) {
                $show_as_news = (empty($v['show_as_news']) ? 'N' : 'Y');
                $avail = (empty($v['avail']) ? 'N' : 'Y');
                db_query("UPDATE $sql_tbl[newslists] SET show_as_news='$show_as_news', avail='$avail' WHERE listid='$listid'");
            }

            $top_message['content'] = func_get_langvar_by_name('msg_adm_newslists_upd');
        }
    }

    if ($mode == 'modify' || $mode == 'create') {

        // Create new newslist or edit newslist details

        if (is_array($list)) {
            $list['name'] = @trim($list['name']);
            $list['descr'] = @trim($list['descr']);
            $list = func_array_map('stripslashes',$list);

            $error = array();
            $err = false;
            foreach (array('name', 'descr') as $key) {
                $err = $err || ($error[$key] = empty($list[$key]));
            }

            if (!$err) {
                $list = func_array_map('addslashes',$list);
                $mode = '';
                $list_values = $list;
                func_unset($list_values,'listid');

                if (!empty($list['listid'])) {
                    func_array2update('newslists', $list_values, "listid='$list[listid]'");
                    $top_message['content'] = func_get_langvar_by_name('msg_adm_newslist_upd');
                }
                else {
                    $list_values['lngcode'] = empty($edit_lng) ? $shop_language : $edit_lng;
                    func_array2insert('newslists', $list_values);
                    $list['listid'] = db_insert_id();
                    $top_message['content'] = func_get_langvar_by_name('msg_adm_newslists_add');
                }
            }
            else {
                $top_message['content'] = func_get_langvar_by_name('err_filling_form');
                $top_message['type'] = 'E';
                x_session_register('nwslt_object');
                $nwslt_object['error'] = $error;
                $nwslt_object['list'] = $list;

                func_header_location("news.php?mode=$mode&targetlist=".$list['listid']);
            }
        }

        func_header_location("news.php?mode=modify&targetlist=".$list['listid']);
    }
    elseif ($mode == 'subscribers') {

        // Modify subscriptions for the selected newslist

        if ($action == 'add' && !empty($new_email)) {
            $count = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[newslist_subscription] WHERE listid='$targetlist' AND email='$new_email'");
            if ($count<1) {
                db_query("INSERT INTO $sql_tbl[newslist_subscription] (listid, email, since_date) VALUES ('$targetlist','$new_email','".XC_TIME."')");
                $top_message['content'] = func_get_langvar_by_name('msg_adm_news_subscriber_add');
            }
            else {
                $top_message['content'] = func_get_langvar_by_name('msg_adm_err_news_subscriber_add');
                $top_message['type'] = 'E';
            }
        }
        elseif ($action == 'delete' && is_array($to_delete)) {
            foreach ($to_delete as $email=>$flag) {
                db_query("DELETE FROM $sql_tbl[newslist_subscription] WHERE listid='$targetlist' AND email='$email'");
            }

            $top_message['content'] = func_get_langvar_by_name('msg_adm_news_subscribers_del');
        }
        elseif ($action == 'import' && !empty($userfile)) {
            x_load('mail');
            $userfile = func_move_uploaded_file('userfile');
            $fp = func_fopen($userfile, 'r', true);
            if ($fp) {
                $line_buffer = '';
                while ($line = fgets($fp, 255)) {
                    $line = $line_buffer.$line;
                    $line_buffer = '';
                    $new_emails = array();
                    if (preg_match("/\r/", trim($line))) {
                        $new_emails = preg_split("/\r/", $line);
                        if (!feof($fp)) {
                            $tail = array_pop($new_emails);
                            if (strlen(trim($tail))) $line_buffer = $tail;
                        }
                    } else {
                        $new_emails = array($line);
                    }
                    foreach ($new_emails as $new_email) {
                        $new_email = trim($new_email);
                        if (preg_match('/^".+"$/S', $new_email))
                            $new_email = str_replace('""', '"', substr($new_email, 1, -1));

                        if (!func_check_email($new_email))
                            continue;

                        $new_email = addslashes($new_email);
                        $count = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[newslist_subscription] WHERE listid='$targetlist' AND email='$new_email'");
                        if ($count < 1) {
                            db_query("INSERT INTO $sql_tbl[newslist_subscription] (listid, email, since_date) VALUES ('$targetlist','$new_email','".XC_TIME."')");
                        }
                    }
                }

                fclose($fp);
                $top_message['content'] = func_get_langvar_by_name('msg_adm_news_subscribers_imp');
            }

            @unlink($userfile);
        }
        elseif (($action == 'export' && !empty($to_delete)) || $action == 'export_all') {
            header("Content-type: application/force-download");
            header("Content-disposition: attachment; filename=subscribers.txt");

            $email_condition = ($action == 'export') ? "AND email IN ('".implode("','", array_keys($to_delete))."')" : "";
            $subscribers = func_query("SELECT * FROM $sql_tbl[newslist_subscription] WHERE listid='$targetlist' $email_condition ORDER BY email");
            if (is_array($subscribers)) {
                foreach ($subscribers as $value) {
                    echo $value['email']."\n";
                }
            }

            exit;
        }

        func_header_location("news.php?mode=subscribers&targetlist=".$targetlist);
    }
    elseif ($mode == 'messages') {

        // Manage messages of newslist

        if (is_array($message)) {
            $message['subject'] = @trim($message['subject']);
            $message = func_array_map('stripslashes',$message);

            $smarty->assign('action', 'modify');
            $smarty->assign('message', $message);
            $error = array(); $err = false;
            foreach (array('subject', 'body') as $key) {
                $err = $err || ($error[$key] = empty($message[$key]));
            }

            if (!$err) {
                $message = func_array_map('addslashes',$message);

                $mode = '';
                if (!empty($message['newsid'])) {
                    db_query("UPDATE $sql_tbl[newsletter] SET subject='$message[subject]', body='$message[body]', allow_html='$message[allow_html]', show_as_news='$message[show_as_news]', email1='$message[email1]', email2='$message[email2]', email3='$message[email3]' WHERE newsid='$message[newsid]'");
                    $top_message['content'] = func_get_langvar_by_name('msg_adm_news_message_upd');
                }
                else {
                    db_query("INSERT INTO $sql_tbl[newsletter] (listid, date, subject, body, allow_html, show_as_news, email1, email2, email3) VALUES ('$targetlist','".XC_TIME."','$message[subject]', '$message[body]', '$message[allow_html]', '$message[show_as_news]', '$message[email1]', '$message[email2]', '$message[email3]')");
                    $message['newsid'] = db_insert_id();
                    $top_message['content'] = func_get_langvar_by_name('msg_adm_news_message_add');
                }

                if (!$admin_safe_mode) {
                    include $xcart_dir.'/modules/News_Management/news_send.php';
                }
            }
            else {
                x_session_register('nwslt_object');
                $nwslt_object['error'] = 'error';
                $nwslt_object['message'] = $message;
            }

            func_header_location("news.php?mode=messages&targetlist=".$targetlist."&messageid=".$message['newsid']."&action=modify");
        }
        elseif ($action == 'send' || $action == 'send_continue') {
            include $xcart_dir.'/modules/News_Management/news_send.php';
            if (count($recipients) > 0 || (isset($news_send_data[$messageid]) && $news_send_data[$messageid]['sent_email_count'] > 0)) {
                $_count = max(count($recipients), intval($news_send_data[$messageid]['sent_email_count']));
                $top_message['content'] = func_get_langvar_by_name('msg_adm_news_message_sent', array('count' => $_count));
            } else {
                $top_message['content'] = func_get_langvar_by_name('msg_adm_err_news_message_not_sent');
                $top_message['type'] = 'E';
            }

            if (!empty($news_send_data))
                x_session_unregister('news_send_data');
        }
        elseif ($action == 'delete' && !empty($to_delete)) {
            if (is_array($to_delete)) {
                foreach ($to_delete as $k=>$v) {
                    db_query("DELETE FROM $sql_tbl[newsletter] WHERE newsid='$k'");
                }

                $top_message['content'] = func_get_langvar_by_name('msg_adm_news_message_del');
            }
        }

        func_header_location("news.php?mode=messages&targetlist=".$targetlist);

    }
    elseif ($mode == 'unsubscribers') {

        if ($action == 'import' && !empty($userfile)) {
            $userfile = func_move_uploaded_file('userfile');
            $fp = func_fopen($userfile, 'r', true);
            $success = 0;
            if ($fp) {
                $line_buffer = '';
                $list_condition = ($do_not_need_targetlist == 'Y') ? '' : " AND listid='$targetlist'";
                while ($line = fgets($fp, 255)) {
                    $line = $line_buffer.$line;
                    $line_buffer = '';
                    $new_emails = array();
                    if (preg_match("/\r/", trim($line))) {
                        $new_emails = preg_split("/\r/", $line);
                        if (!feof($fp)) {
                            $tail = array_pop($new_emails);
                            if (strlen(trim($tail))) $line_buffer = $tail;
                        }
                    } else {
                        $new_emails = array($line);
                    }

                    foreach ($new_emails as $new_email) {
                        $new_email = trim($new_email);
                        if (preg_match('/^".+"$/S', $new_email))
                            $new_email = str_replace('""', '"', substr($new_email, 1, -1));

                        $new_email = addslashes($new_email);
                        $count = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[newslist_subscription] WHERE email='$new_email' $list_condition");
                        if ($count >= 1) {
                            $success ++;
                            db_query("DELETE FROM $sql_tbl[newslist_subscription] WHERE email = '$new_email' $list_condition");
                        }
                    }
                }
                fclose($fp);

                if ($success) {
                    $top_message['content'] = func_get_langvar_by_name('msg_adm_news_unsubscribers_imp_success'.($do_not_need_targetlist == 'Y' ? '_all' : ''), array('X' => $success));
                } else {
                    $top_message['content'] = func_get_langvar_by_name('msg_adm_news_unsubscribers_imp_fail');
                }
            }
            @unlink($userfile);
        }

        func_header_location("news.php?mode=subscribers&targetlist=".$targetlist);
    }

    func_header_location('news.php');
}

/**
 * Process the GET request
 */

if (!empty($mode))
    $location[count($location)-1][1] = 'news.php';

if (!empty($targetlist)) {
    $list = func_query_first("SELECT * FROM $sql_tbl[newslists] WHERE listid='$targetlist'");

    if ($mode == 'modify')
        $location[] = array($list['name'], '');
    else
        $location[] = array($list['name'], "news.php?mode=modify&targetlist=$targetlist");

    // Define data for the navigation within section

    $dialog_tools_data['left'][] = array('link' => "news.php?mode=modify&targetlist=$targetlist", 'title' => func_get_langvar_by_name('lbl_details'));
    $dialog_tools_data['left'][] = array('link' => "news.php?mode=subscribers&targetlist=$targetlist", 'title' => func_get_langvar_by_name('lbl_subscriptions'));
    $dialog_tools_data['left'][] = array('link' => "news.php?mode=messages&targetlist=$targetlist", 'title' => func_get_langvar_by_name('lbl_messages'));

    $dialog_tools_data['right'][] = array('link' => 'news.php', 'title' => func_get_langvar_by_name('lbl_news_lists'));
    $dialog_tools_data['right'][] = array('link' => "news.php?mode=create", 'title' => func_get_langvar_by_name('lbl_add_news_list'));
} else {
    $dialog_tools_data = array();
}

if (!empty($list['listid'])) {
    if ($list['lngcode'] != $shop_language && is_array($d_langs) && !in_array($list['lngcode'], $d_langs)) {
        func_header_location("news.php?mode=modify&targetlist=$targetlist&edit_lng=$list[lngcode]&old_lng=$shop_language");
    }
}

if ($mode == 'modify') {

    // Get the news list details and display it

    if (empty($list)) {
        $top_message['content'] = func_get_langvar_by_name('msg_adm_err_newslist_not_exists');
        func_header_location('news.php');
    }

    $smarty->assign('list', $list);
}
elseif ($mode == 'create') {
    $location[] = array(func_get_langvar_by_name('lbl_add_new_list'), '');
}
elseif ($mode == 'subscribers') {

    if (!empty($sort) && in_array($sort, array('email','since_date'))) {
        // Store the sorting type in the session
        $search_data['subscribers']['sort_field'] = $sort;
        $search_data['subscribers']['sort_direction'] = abs(intval($search_data['subscribers']['sort_direction']) - 1);
        $flag_save = true;
    }

    if ($flag_save)
        x_session_save('search_data');

    if (is_array($search_data['subscribers'])) {
        $data = $search_data['subscribers'];
        foreach ($data as $k=>$v) {
            if (!is_array($v) && !is_numeric($v))
                $data[$k] = addslashes($v);
        }
    }

    $sort_string = "$sql_tbl[newslist_subscription].since_date DESC";

    if (!empty($data['sort_field'])) {
        // Sort the search results...

        $direction = ($data['sort_direction'] ? 'DESC' : 'ASC');
        switch ($data['sort_field']) {
            case 'email':
                $sort_string = "$sql_tbl[newslist_subscription].email $direction";
                break;
            case 'since_date':
                $sort_string = "$sql_tbl[newslist_subscription].since_date $direction";
        }
    }

    $total_items = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[newslist_subscription] WHERE listid='$targetlist'");

    if (!empty($total_items)) {
        $objects_per_page = $config['News_Management']['news_subscribers_per_page'];
        include $xcart_dir.'/include/navigation.php';
        $smarty->assign('navigation_script',"news.php?mode=subscribers&targetlist=".$targetlist);
        $smarty->assign('first_item', $first_page+1);
        $smarty->assign('last_item', min($first_page+$objects_per_page, $total_items));

        $subscribers = func_query("SELECT * FROM $sql_tbl[newslist_subscription] WHERE listid='$targetlist' ORDER BY $sort_string  LIMIT $first_page, $objects_per_page");
    }
    else {
        $subscribers = array();
    }

    if (is_array($subscribers)) {
        foreach ($subscribers as $k=>$v) {
            $subscribers[$k]['since_date'] += $config["Appearance"]["timezone_offset"];
        }
    }

    if (!empty($subscribers))
        $smarty->assign('subscribers', $subscribers);

    $smarty->assign('search_prefilled', $search_data['subscribers']);

    $location[] = array(func_get_langvar_by_name('lbl_subscribers_title'), '');
}
elseif ($mode == 'messages') {
    if ($action == 'modify') {
        $message = func_query_first("SELECT * FROM $sql_tbl[newsletter] WHERE newsid='$messageid'");
        $smarty->assign('message', $message);
    }
    else {

        $objects_per_page = $config['News_Management']['newsletter_limit'];
        if (!empty($page)) {
            $page = preg_replace("/[^\d]/", '', $page);
        }
        $search_query = "FROM $sql_tbl[newsletter] WHERE listid='$targetlist' ORDER BY date DESC";
        $total_items = func_query_first_cell("SELECT COUNT(*) $search_query");

        if ($total_items > 0) {

            // Navigation code

            require $xcart_dir.'/include/navigation.php';
            $smarty->assign('navigation_script',"news.php?mode=messages&targetlist=$targetlist");

            $messages = func_query("SELECT * $search_query LIMIT $first_page, $objects_per_page");
            if (is_array($messages)) {
                foreach ($messages as $k=>$v) {
                    if ($messages[$k]['send_date'] > 0)
                        $messages[$k]['send_date'] += $config["Appearance"]["timezone_offset"];
                    $messages[$k]['date'] += $config["Appearance"]["timezone_offset"];
                }
            }

            $smarty->assign('messages', $messages);
        }
    }

    $location[] = array(func_get_langvar_by_name('lbl_messages'), '');
}

if (x_session_is_registered('nwslt_object')) {
    x_session_register('nwslt_object');
    if (is_array($nwslt_object)) {
        foreach ($nwslt_object as $k=>$v)
            $smarty->assign($k, $v);
    }

    x_session_unregister('nwslt_object');
}

if (!empty($targetlist)) {
    $targetlistname = func_query_first_cell("SELECT name FROM $sql_tbl[newslists] WHERE listid='$targetlist'");
    $smarty->assign('targetlistname', $targetlistname);
    $smarty->assign('targetlist', $targetlist);
}

$smarty->assign('need_unsubscribe_section', func_query_first_cell("SELECT listid FROM $sql_tbl[newslist_subscription]"));
$smarty->assign('not_empty_list', func_query_first_cell("SELECT listid FROM $sql_tbl[newslist_subscription] WHERE listid='$targetlist'"));

$lists = func_query("SELECT * FROM $sql_tbl[newslists] WHERE lngcode='$shop_language'");
$smarty->assign('lists', $lists);
$smarty->assign('action', $action);
$smarty->assign('mode', $mode);

?>
