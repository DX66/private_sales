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
 * Survey actions processing library
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: survey.php,v 1.20.2.2 2011/01/10 13:12:03 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load('backoffice');

x_session_register('saved_post_data');
x_session_register('filter_surveys', array());

$location[] = array(func_get_langvar_by_name('lbl_survey_surveys'), 'surveys.php');

if ($REQUEST_METHOD == 'POST' && $mode == 'add' && !empty($surveyid) && empty($section)) {

    // Modify survey
    $data['valid_from_date'] = func_prepare_search_date($data['valid_from_date']);
    $data['expires_data']    = func_prepare_search_date($data['expires_data'], true);
    $data['publish_results'] = $data['publish_results'];
    $data['display_on_frontpage'] = $data['display_on_frontpage'];

    func_array2update('surveys', $data, "surveyid = '$surveyid'");

    $top_message = array(
        'content' => func_get_langvar_by_name('txt_survey_is_modifyed')
    );

    if (empty($add_data['survey'])) {
        db_query("DELETE FROM $sql_tbl[languages_alt] WHERE code = '$shop_language' AND name = 'survey_name_$surveyid'");
    } else {
        func_languages_alt_insert('survey_name_'.$surveyid, $add_data['survey'], $shop_language);
    }

    if (empty($add_data['header'])) {
        db_query("DELETE FROM $sql_tbl[languages_alt] WHERE code = '$shop_language' AND name = 'survey_header_$surveyid'");
    } else {
        func_languages_alt_insert('survey_header_'.$surveyid, $add_data['header'], $shop_language);
    }

    if (empty($add_data['footer'])) {
        db_query("DELETE FROM $sql_tbl[languages_alt] WHERE code = '$shop_language' AND name = 'survey_footer_$surveyid'");
    } else {
        func_languages_alt_insert('survey_footer_'.$surveyid, $add_data['footer'], $shop_language);
    }

    if (empty($add_data['complete'])) {
        db_query("DELETE FROM $sql_tbl[languages_alt] WHERE code = '$shop_language' AND name = 'survey_complete_$surveyid'");
    } else {
        func_languages_alt_insert('survey_complete_'.$surveyid, $add_data['complete'], $shop_language);
    }

    if ($go_to == 'next')
        func_header_location("survey.php?surveyid=".$surveyid."&section=maillist");
    elseif ($go_to == 'finish')
        func_header_location('surveys.php');
    else
        func_header_location("survey.php?surveyid=".$surveyid);

} elseif ($mode == 'structure' && $section == 'structure' && !empty($data)) {

    // Modify survey structure
    foreach ($data as $qid => $d) {
        func_array2update('survey_questions', $d, "questionid = '$qid'");
    }

    if ($go_to == 'next')
        func_header_location("survey.php?surveyid=$surveyid");
    else
        func_header_location("survey.php?surveyid=$surveyid&section=structure");

} elseif ($mode == 'delete' && $section == 'structure' && !empty($check)) {

    // Delete question
    if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[survey_results] WHERE surveyid = '$surveyid'") > 0)
        func_delete_survey_stats($surveyid);

    $ids = func_query_column("SELECT answerid FROM $sql_tbl[survey_answers] WHERE questionid IN ('".implode("','", $check)."')");
    db_query("DELETE FROM $sql_tbl[survey_answers] WHERE questionid IN ('".implode("','", $check)."')");
    if (!empty($ids)) {
        foreach ($ids as $aid) {
            db_query("DELETE FROM $sql_tbl[languages_alt] WHERE name = 'answer_name_".$aid."'");
        }
    }
    db_query("DELETE FROM $sql_tbl[survey_questions] WHERE questionid IN ('".implode("','", $check)."')");
    foreach ($check as $id) {
        db_query("DELETE FROM $sql_tbl[languages_alt] WHERE name = 'question_name_".$id."'");
    }
    db_query("DELETE FROM $sql_tbl[survey_result_answers] WHERE questionid IN ('".implode("','", $check)."')");

    $top_message = array(
        'content' => func_get_langvar_by_name('txt_survey_questions_are_deleted')
    );
    func_header_location("survey.php?surveyid=$surveyid&section=structure");

} elseif ($mode == 'question' && $section == 'question' && !empty($data)) {

    $data['col'] = abs(intval($data['col']));
    if (empty($data['col']))
        $data['col'] = 1;

    if (empty($add_data['question']) && empty($qid)) {
        $top_message = array(
            'type' => 'E',
            'content' => func_get_langvar_by_name('txt_survey_question_text_is_empty')
        );

    } elseif (empty($qid)) {

        if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[survey_results] WHERE surveyid = '$surveyid'") > 0)
            func_delete_survey_stats($surveyid);

        $data['surveyid'] = $surveyid;
        if (empty($data['orderby']))
            $data['orderby'] = func_query_first_cell("SELECT MAX(orderby) FROM $sql_tbl[survey_questions] WHERE surveyid = '$surveyid'")+10;

        $qid = func_array2insert('survey_questions', $data);

        $top_message = array(
            'content' => func_get_langvar_by_name('txt_survey_question_is_added')
        );
    } else {

        // Modify survey structure
        func_array2update('survey_questions', $data, "questionid = '$qid'");

        $top_message = array(
            'content' => func_get_langvar_by_name('txt_survey_question_is_modifyed')
        );
    }

    if (!empty($qid) && !empty($add_data['question'])) {
        func_languages_alt_insert('question_name_'.$qid, $add_data['question'], $shop_language);

        // Update answers list
        if (!empty($answers)) {
            foreach ($answers as $aid => $d) {
                func_languages_alt_insert('answer_name_'.$aid, $d['answer'], $shop_language);
                unset($d['answer']);

                func_array2update('survey_answers', $d, "answerid = '$aid'");
            }
        }

        // Add new answer(s)
        if (!empty($new_answer)) {

            $_updated = false;

            foreach ($new_answer['answer'] as $i => $answer_name) {
                if (zerolen($answer_name))
                    continue;

                $_updated = true;

                if (empty($new_answer['orderby'][$i]))
                    $new_answer['orderby'][$i] = func_query_first_cell("SELECT MAX(orderby) FROM $sql_tbl[survey_answers] WHERE questionid = '$qid'")+10;

                $query_data = array(
                    'questionid' => $qid,
                    'textbox_type' => $new_answer['textbox_type'][$i],
                    'orderby' => $new_answer['orderby'][$i]
                );

                $aid = func_array2insert('survey_answers', $query_data);

                if (!empty($aid))
                    func_languages_alt_insert('answer_name_'.$aid, $answer_name, $shop_language);
            }

            if ($_updated && func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[survey_results] WHERE surveyid = '$surveyid'") > 0)
                func_delete_survey_stats($surveyid);

        }

        $saved_post_data = false;

        func_header_location("survey.php?surveyid=$surveyid&section=structure");
    }

    $saved_post_data = $data;
    $saved_post_data['question'] = $add_data['question'];
    $saved_post_data['preanswers'] = array();
    if (!empty($new_answer)) {
        foreach ($new_answer['answer'] as $k => $v) {
            $saved_post_data['preanswers'][] = array(
                'answer' => $v,
                'textbox_type' => $new_answer['textbox_type'][$k],
                'orderby' => $new_answer['orderby'][$k],
            );
        }
    }

    func_header_location("survey.php?surveyid=$surveyid&section=question&qid=$qid");

} elseif ($mode == 'delete' && $section == 'question' && !empty($check) && !empty($qid)) {

    // Delete question answer(s)

    if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[survey_results] WHERE surveyid = '$surveyid'") > 0)
        func_delete_survey_stats($surveyid);

    db_query("DELETE FROM $sql_tbl[survey_answers] WHERE answerid IN ('".implode("','", $check)."')");
    foreach ($check as $aid) {
        db_query("DELETE FROM $sql_tbl[languages_alt] WHERE name = 'answer_name_".$aid."'");
    }
    db_query("DELETE FROM $sql_tbl[survey_result_answers] WHERE answerid IN ('".implode("','", $check)."')");

    $top_message = array(
        'content' => func_get_langvar_by_name('txt_survey_question_answers_are_deleted')
    );
    func_header_location("survey.php?surveyid=$surveyid&section=question&qid=$qid");

} elseif ($mode == 'events' && $section == 'maillist' && !empty($surveyid)) {

    if (!$allow_events)
        $event_type = '';

    // Modify survey event
    func_array2update('surveys', array('event_type' => $event_type, 'event_logic' => $event_logic), "surveyid = '$surveyid'");

    if (!empty($event_type) && !empty($new_element)) {

        // Add / Update event with conditions
        foreach ($new_element as $param => $ids) {
            if (empty($param))
                continue;

            $query_data = array(
                'surveyid' => $surveyid,
                'param' => $param
            );
            foreach ($ids as $id) {
                if ($param == 'T')
                    $id = func_convert_number($id);

                if (empty($id)) {
                    if ($param == 'T')
                        db_query("DELETE FROM $sql_tbl[survey_events] WHERE surveyid = '$surveyid' AND param = '$param'");

                    continue;
                }

                $query_data['id'] = $id;
                func_array2insert('survey_events', $query_data, true);
            }
        }

        db_query("DELETE FROM $sql_tbl[survey_events] WHERE surveyid = '$surveyid' AND param = ''");

    } else {

        db_query("DELETE FROM $sql_tbl[survey_events] WHERE surveyid = '$surveyid'");
    }

    $top_message = array(
        'content' => func_get_langvar_by_name('txt_survey_conditions_are_updated')
    );

    if ($go_to == 'finish')
        func_header_location('surveys.php');
    else
        func_header_location("survey.php?surveyid=$surveyid&section=maillist");

} elseif ($mode == 'delete_event' && $section == 'maillist' && !empty($surveyid)) {

    // Delete event parameter(s)

    if (!empty($check) && !empty($delete_param) && !empty($check[$delete_param])) {
        db_query("DELETE FROM $sql_tbl[survey_events] WHERE surveyid = '$surveyid' AND param = '$delete_param' AND id IN ('".implode("','", $check[$delete_param])."')");

        $top_message = array(
            'content' => func_get_langvar_by_name('lbl_survey_event_delete_successfull')
        );

    } else {
        $top_message = array(
            'content' => func_get_langvar_by_name('lbl_survey_event_delete_unsuccessfull')
        );
    }

    func_header_location("survey.php?surveyid=$surveyid&section=maillist");

} elseif ($mode == 'add' && $section == 'maillist') {

    // Add survey maillist email(s)
    if (empty($new_email) || (count($new_email) == 1 && empty($new_email[0]))) {
        $top_message = array(
            'type' => 'E',
            'content' => func_get_langvar_by_name('txt_survey_added_email_is_empty')
        );
        func_header_location("survey.php?surveyid=".$surveyid."&section=".$section."&show=manual");
    }

    $cnt = 0;
    $orig_cnt = count($new_email);
    foreach ($new_email as $k => $em) {
        if (!func_check_unique_email($em, $surveyid))
            continue;

        func_array2insert('survey_maillist', array('surveyid' => $surveyid, 'email' => $em, 'date' => XC_TIME));
        unset($new_email[$k]);
        $cnt++;
    }

    if ($cnt == $orig_cnt) {
        $top_message = array(
            'content' => func_get_langvar_by_name('txt_survey_respondents_are_added')
        );

    } else {
        $top_message = array(
            'content' => func_get_langvar_by_name('txt_survey_respondents_arenot_added', array('emails' => "<br />\n&nbsp;".implode("<br />\n&nbsp;", $new_email)), false, true)
        );
    }
    func_header_location("survey.php?surveyid=".$surveyid."&section=".$section."&show=manual");

} elseif (($mode == 'delete' || $mode == 'clear_list') && $section == 'maillist') {

    // Delete respondent(s)
    if ((!empty($check) && $mode == 'delete')) {
        db_query("DELETE FROM $sql_tbl[survey_maillist] WHERE surveyid = '$surveyid' AND email IN ('".implode("','", $check)."')");
        $top_message = array(
            'content' => func_get_langvar_by_name('txt_survey_respondents_are_deleted')
        );

    } elseif ($mode == 'clear_list') {
        db_query("DELETE FROM $sql_tbl[survey_maillist] WHERE surveyid = '$surveyid'");
        $top_message = array(
            'content' => func_get_langvar_by_name('txt_survey_respondents_list_is_cleaned')
        );

    }

    func_header_location("survey.php?surveyid=".$surveyid."&section=".$section);

} elseif ($mode == 'add_users' && $section == 'maillist') {

    // Add repondents from registered users list
    $userids = explode(";", $userids);
    foreach ($userids as $k => $v) {
        if (empty($v))
            unset($userids[$k]);
    }

    if (!empty($userids))
        $userids = func_query("SELECT id, login, email FROM $sql_tbl[customers] WHERE id IN ('".implode("','", $userids)."') AND email <> ''");

    if (empty($userids)) {
        $top_message = array(
            'type' => 'E',
            'content' => func_get_langvar_by_name('txt_survey_users_list_is_empty')
        );
        func_header_location("survey.php?surveyid=".$surveyid."&section=".$section."&show=users");
    }

    foreach ($userids as $l) {
        if (!func_check_unique_email($l['email'], $surveyid))
            continue;

        func_array2insert(
            'survey_maillist',
            array(
                'surveyid' => $surveyid,
                'email' => addslashes($l['email']),
                'userid' => $l['id'],
                'date' => XC_TIME
            )
        );
    }

    $top_message = array(
        'content' => func_get_langvar_by_name('txt_survey_respondents_are_added')
    );
    func_header_location("survey.php?surveyid=".$surveyid."&section=".$section."&show=users");

} elseif ($mode == 'add_news_users' && $section == 'maillist') {

    // Add respondents from news list
    if (empty($newslist)) {
        $top_message = array(
            'type' => 'E',
            'content' => func_get_langvar_by_name('lbl_survey_news_list_is_empty')
        );
        func_header_location("survey.php?surveyid=".$surveyid."&section=".$section."&show=news");
    }

    foreach ($newslist as $listid) {
        $users = func_query_column("SELECT email FROM $sql_tbl[newslist_subscription] WHERE listid = '$listid'");
        if (empty($users))
            continue;

        foreach ($users as $em) {
            if (!func_check_unique_email($em, $surveyid))
                continue;

            func_array2insert('survey_maillist', array('surveyid' => $surveyid, 'email' => $em, 'date' => XC_TIME));
        }
    }

    $top_message = array(
        'content' => func_get_langvar_by_name('txt_survey_respondents_are_added')
    );
    func_header_location("survey.php?surveyid=".$surveyid."&section=".$section."&show=news");

} elseif ($mode == 'add_survey_users' && $section == 'maillist') {

    // Add respondents from another survey(s)
    if (empty($surveylist)) {
        $top_message = array(
            'type' => 'E',
            'content' => func_get_langvar_by_name('txt_survey_list_is_empty')
        );
        func_header_location("survey.php?surveyid=".$surveyid."&section=".$section."&show=respondents");
    }

    foreach ($surveylist as $sid) {
        $users = func_query("SELECT userid, email FROM $sql_tbl[survey_maillist] WHERE surveyid = '$sid' ORDER BY date DESC");
        if (empty($users))
            continue;

        foreach ($users as $l) {
            if (!func_check_unique_email($l['email'], $surveyid))
                continue;

            func_array2insert(
                'survey_maillist',
                array(
                    'surveyid' => $surveyid,
                    'email' => addslashes($l['email']),
                    'userid' => $l['userid'],
                    'date' => XC_TIME
                )
            );
        }
    }

    $top_message = array(
        'content' => func_get_langvar_by_name('txt_survey_respondents_are_added')
    );
    func_header_location("survey.php?surveyid=".$surveyid."&section=".$section."&show=respondents");

} elseif ($mode == 'import_users' && $section == 'maillist') {

    // Import emails from CSV file
    if (empty($userfile)) {
        $top_message = array(
            'type' => 'E',
            'content' => func_get_langvar_by_name('txt_survey_import_file_wasnt_assigned')
        );
        func_header_location("survey.php?surveyid=".$surveyid."&section=".$section."&show=import");
    }
    $userfile = func_move_uploaded_file('userfile');

    $fp = func_fopen($userfile, 'r', true);
    if ($fp) {
        while ($em = fgets($fp, 255)) {
            $em = trim($em);
            if (!func_check_unique_email($em, $surveyid))
                continue;

            func_array2insert('survey_maillist', array('surveyid' => $surveyid, 'email' => $em, 'date' => XC_TIME));
        }
        fclose($fp);

        $top_message = array(
            'content' => func_get_langvar_by_name('msg_adm_news_subscribers_imp')
        );

    }
    @unlink($userfile);

    func_header_location("survey.php?surveyid=".$surveyid."&section=".$section."&show=import");

} elseif ($section == 'maillist' && !empty($check) && $mode == 'send' && !empty($surveyid)) {
    // Send survey invitations
    $cnt = 0;
    foreach ($check as $e) {
        $user = func_query_first("SELECT email, userid FROM $sql_tbl[survey_maillist] WHERE surveyid = '$surveyid' AND email = '$e'");
        if ($user['email'] != stripslashes($e))
            continue;

        if (func_send_survey_invitation($surveyid, $e, $user['login'], true) === true)
            $cnt++;
    }

    if ($cnt > 0) {
        $top_message = array(
            'content' => func_get_langvar_by_name('lbl_survey_invitations_are_sent')
        );
    }

    func_header_location("survey.php?surveyid=".$surveyid."&section=maillist");

} elseif ($section == 'maillist' && $mode == 'send_all' && !empty($surveyid)) {
    // Send all unsent survey invitations
    $emails = db_query("SELECT email, userid FROM $sql_tbl[survey_maillist] WHERE surveyid = '$surveyid' ORDER BY date DESC");
    $cnt = 0;
    if ($emails) {
        while ($row = db_fetch_array($emails)) {
            if (func_send_survey_invitation($surveyid, $row['email'], $row['login']) === true)
                $cnt++;
        }
        db_free_result($emails);
    }

    if ($cnt > 0) {
        $top_message = array(
            'content' => func_get_langvar_by_name('lbl_survey_invitations_are_sent')
        );
    }

    func_header_location("survey.php?surveyid=".$surveyid."&section=maillist");

} elseif ($section == 'instances' && $mode == 'delete' && !empty($check)) {

    // Delete survey instance(s)
    db_query("DELETE FROM $sql_tbl[survey_results] WHERE surveyid = '$surveyid' AND sresultid IN ('".implode("','", $check)."')");
    db_query("DELETE FROM $sql_tbl[survey_result_answers] WHERE sresultid IN ('".implode("','", $check)."')");

    $top_message = array(
        'content' => func_get_langvar_by_name('txt_survey_instances_are_deleted')
    );

    func_header_location("survey.php?surveyid=".$surveyid."&section=instances");

} elseif ($mode == 'clear_stats' && !empty($surveyid)) {

    // Delete survey statistics
    func_delete_survey_stats($surveyid);

    $top_message = array(
        'content' => func_get_langvar_by_name('txt_survey_statistics_is_cleared')
    );

    func_header_location("survey.php?surveyid=".$surveyid."&section=stats");

} elseif (empty($section) && $mode == 'create') {

    // Create survey
    $query_data = array(
        'survey_type' => 'D',
        'created_date' => XC_TIME,
        'valid_from_date' => XC_TIME,
        'expires_data' => mktime(23,59,59, date('m')+1, date('d'), date('Y')),
        'orderby' => func_query_first_cell("SELECT MAX(orderby) FROM $sql_tbl[surveys]")+10,
    );
    $surveyid = func_array2insert('surveys', $query_data);

    func_languages_alt_insert('survey_name_'.$surveyid, func_get_langvar_by_name('lbl_survey_default_name', array('surveyid' => $surveyid), false, true, true), $shop_language);

    func_header_location("survey.php?surveyid=".$surveyid."&section=question");

} elseif ($section == 'answer_texts') {

    if (empty($questionid)) {
        if (empty($as_text))
            func_close_window();

        exit;
    }

    // Display answer comments as popup window
    $surveyid = func_query_first_cell("SELECT surveyid FROM $sql_tbl[survey_questions] WHERE questionid = '$questionid'");
    if (empty($surveyid)) {
        if (empty($as_text))
            func_close_window();

        exit;
    }

    $texts = func_survey_get_comments($questionid, $answerid, $filter_surveys[$surveyid]);

    if (!empty($as_text)) {
        if (empty($texts)) {
            echo func_get_langvar_by_name('txt_survey_answer_hasnt_texts', false, false, true);

        } else {
            $smarty->assign('texts', $texts);
            func_display('modules/Survey/survey_texts.tpl', $smarty);
        }

    } else {
        $smarty->assign('texts', $texts);

        $smarty->assign('template_name', 'modules/Survey/survey_texts.tpl');
        $smarty->assign('popup_title', func_get_langvar_by_name('lbl_survey_answers_texts'));

        func_display('help/popup_info.tpl', $smarty);
    }

    exit;

} elseif ($mode == 'filter' && ($section == 'stats' || $section == 'instances') && !empty($surveyid)) {

    // Update the survey filter

    if ($start_date && $end_date) {
        $filter_surveys[$surveyid] = array(
            'date_from' => func_prepare_search_date($start_date),
            'date_to'   => func_prepare_search_date($end_date, true)
        );
    }
    func_header_location("survey.php?surveyid=$surveyid&section=$section");

} elseif ($mode == 'reset_filter' && ($section == 'stats' || $section == 'instances') && !empty($surveyid)) {

    // Reset the survey filter

    if (isset($filter_surveys[$surveyid]))
        unset($filter_surveys[$surveyid]);

    func_header_location("survey.php?surveyid=$surveyid&section=$section");

} elseif (empty($surveyid)) {

    // Redirect to surveys list if survey not found

    if ($section == 'preview')
        func_close_window();

    $top_message = array(
        'content' => func_get_langvar_by_name('txt_survey_not_found'),
        'type' => 'E'
    );
    func_header_location('surveys.php');
}

$surveys_count = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[surveys]");

// Get survey data
if (!empty($surveyid)) {

    if ($section == 'maillist') {
        // Pre-select service data
        include_once $xcart_dir.'/include/countries.php';
        include_once $xcart_dir.'/include/states.php';

        x_load('category');
        $smarty->assign('allcategories', func_data_cache_get("get_categories_tree", array(0, true, $shop_language, $user_account['membershipid'])));
    }

    $survey = func_get_survey($surveyid);
    $survey['questions_count'] = (is_array($survey['questions']) ? count($survey['questions']) : 0);

    if (empty($survey)) {
        // Survey is empty

        if ($section == 'preview')
            func_close_window();

        $top_message = array(
            'type' => 'E',
            'content' => func_get_langvar_by_name('txt_survey_not_found')
        );
        func_header_location('surveys.php');

    }

    $smarty->assign('survey', $survey);

    if (!in_array($section, array('stats', 'instances')))
        $location[] = array(func_get_langvar_by_name('lbl_survey_modify_survey'), '');

    if ($section == '') {
        $location[] = array(func_get_langvar_by_name('lbl_survey_details'), '');
    }

    if (!empty($qid) && $section == 'question') {

        // Get survey question data
        if (!isset($survey['questions'][$qid])) {
            $top_message = array(
                'type' => 'E',
                'content' => func_get_langvar_by_name('txt_survey_question_not_found')
            );
            func_header_location("survey.php?surveyid=".$surveyid);
        }

        $smarty->assign('question', $survey['questions'][$qid]);
        $smarty->assign('qid', $qid);
    }

    if ($section == 'question') {
        if (!empty($saved_post_data))
            $smarty->assign('question', $saved_post_data);
        $saved_post_data = false;

        $smarty->assign('answers_types', func_get_answers_types());
        $location[] = array(func_get_langvar_by_name('lbl_survey_structure'), '');
    }

    if ($section == 'structure') {
        $location[] = array(func_get_langvar_by_name('lbl_survey_structure'), '');
    }

    if ($section == 'maillist') {
        $smarty->assign('survey_events', func_get_survey_events());
    }

    if ($section == 'maillist') {

        // Get maillist (with pagination)
        $total_items = func_query_first_cell("SELECT COUNT(surveyid) FROM $sql_tbl[survey_maillist] WHERE surveyid = '$surveyid'");
        $objects_per_page = $config['Survey']['survey_email_addresses_per_page'];
        include $xcart_dir.'/include/navigation.php';
        if ($total_items > 0) {
            $maillist = func_query("SELECT sm.*, c.login FROM $sql_tbl[survey_maillist] as sm LEFT JOIN $sql_tbl[customers] as c ON sm.userid = c.id WHERE sm.surveyid = '$surveyid' ORDER BY date DESC LIMIT $first_page, $objects_per_page");
            $smarty->assign('maillist', $maillist);
        }

        $smarty->assign('navigation_script',"survey.php?section=maillist&surveyid=".$surveyid);
        $smarty->assign('first_item', $first_page+1);
        $smarty->assign('last_item', min($first_page+$objects_per_page, $total_items));

        // Get news lists
        if (!empty($active_modules['News_Management'])) {
            $news_list = func_query("SELECT $sql_tbl[newslists].listid, $sql_tbl[newslists].name FROM $sql_tbl[newslists], $sql_tbl[newslist_subscription] WHERE $sql_tbl[newslists].listid = $sql_tbl[newslist_subscription].listid AND $sql_tbl[newslists].lngcode = '".$shop_language."' GROUP BY $sql_tbl[newslists].listid");
            if (!empty($news_list))
                $smarty->assign('news_list', $news_list);
        }

        // Get another surveys
        $another_surveys = func_query_hash("SELECT $sql_tbl[surveys].surveyid FROM $sql_tbl[surveys], $sql_tbl[survey_maillist] WHERE $sql_tbl[surveys].surveyid != '$surveyid' AND $sql_tbl[surveys].surveyid = $sql_tbl[survey_maillist].surveyid GROUP BY $sql_tbl[surveys].surveyid", "surveyid", false);
        if (!empty($another_surveys)) {
            foreach ($another_surveys as $sid => $v) {
                $another_surveys[$sid] = func_get_languages_alt('survey_name_'.$sid);
            }
            $smarty->assign('another_surveys', $another_surveys);
        }

        $location[] = array(func_get_langvar_by_name('lbl_survey_maillist'), '');
    }

    if ($section == 'stats' || $section == "instances") {
        $smarty->assign('filter', $filter_surveys[$surveyid]);
        $smarty->assign('is_filter', !empty($filter_surveys[$surveyid]['date_from']) && !empty($filter_surveys[$surveyid]['date_to']));

        $smarty->assign('now', XC_TIME);
        $smarty->assign('prev_month', mktime(date('H'), date('i'), date('s'), date('m')-1, date('d'), date('Y')));
    }

    if ($section == 'stats') {
        $survey = func_get_survey_results($surveyid, true, $filter_surveys[$surveyid]);

        $smarty->assign('survey', $survey);

        if ($mode == 'printable') {

            if (is_array($survey['questions'])) {
                foreach ($survey['questions'] as $_questionid => $_question) {
                    if (is_array($_question['answers'])) {
                        foreach ($_question['answers'] as $_answerid => $_answer) {
                            if ($_answer['textbox_type'] == 'Y' && $_answer['result_comment'] > 0)
                                $survey['questions'][$_questionid]['answers'][$_answerid]['texts'] = func_survey_get_comments($_questionid, $_answerid, $filter_surveys[$surveyid]);
                        }

                    } elseif ($_question['answers_type'] == 'N' && $_question['result_filled']) {
                        $survey['questions'][$_questionid]['answers'][0]['texts'] = func_survey_get_comments($_questionid, 0, $filter_surveys[$surveyid]);
                    }
                }
            }

            $smarty->assign('mode', 'printable');
            $smarty->assign('survey', $survey);

            func_display('modules/Survey/survey_stats_printable.tpl', $smarty);
            exit;
        }
        $location[] = array(func_get_langvar_by_name('lbl_survey_statistics'), '');
    }

    if ($section == 'instances') {

        if (!empty($sresultid)) {
            // Get survey instance
            $survey = func_get_survey_instance($sresultid);
            if (empty($survey)) {
                $top_message = array(
                    'content' => func_get_langvar_by_name(''),
                    'type' => 'E'
                );
                func_header_location("survey.php?section=instances&surveyid=".$surveyid);
            }

            $smarty->assign('survey', $survey);
            $location[] = array(func_get_langvar_by_name('lbl_survey_instances'), "survey.php?surveyid=".$surveyid."&section=instances");
            $location[] = array(func_get_langvar_by_name('lbl_survey_instances'), '');

        } else {
            // Get survey instances list
            $where = '';
            if (!empty($filter_surveys[$surveyid]))
                $where = "date > ".intval($filter_surveys[$surveyid]['date_from'])." AND date < ".intval($filter_surveys[$surveyid]['date_to'])." AND";

            $total_items = func_query_first_cell("SELECT COUNT(surveyid) FROM $sql_tbl[survey_results] WHERE $where surveyid = '$surveyid'");
            $objects_per_page = $config['Survey']['survey_results_per_page'];
            include $xcart_dir.'/include/navigation.php';

            if ($total_items > 0) {
                $results =  func_query_hash("SELECT $sql_tbl[survey_results].*, $sql_tbl[customers].usertype, $sql_tbl[customers].login FROM $sql_tbl[survey_results] LEFT JOIN $sql_tbl[customers] ON $sql_tbl[survey_results].userid = $sql_tbl[customers].id WHERE $where $sql_tbl[survey_results].surveyid = '$surveyid' ORDER BY $sql_tbl[survey_results].date DESC LIMIT $first_page, $objects_per_page", "sresultid", false);

                if (!empty($results)) {
                    foreach ($results as $k => $v) {
                        if (!empty($v['completed']))
                            $results[$k]['completed_msg'] = func_get_langvar_by_name("lbl_survey_completed_type_".$v['completed']);
                        if (!empty($v['as_result']))
                            $results[$k]['obj_link'] = func_as_result2obj_link($v['as_result']);
                    }

                    $smarty->assign('total_items', $total_items);
                    $smarty->assign('results', $results);
                    $smarty->assign('navigation_script',"survey.php?section=instances&surveyid=".$surveyid);
                    $smarty->assign('first_item', $first_page+1);
                    $smarty->assign('last_item', min($first_page+$objects_per_page, $total_items));
                }
            }

            $smarty->assign('filter', $filter_surveys[$surveyid]);
            $smarty->assign('is_filter', !empty($filter_surveys[$surveyid]['date_from']) && !empty($filter_surveys[$surveyid]['date_to']));
            $location[] = array(func_get_langvar_by_name('lbl_survey_instances'), '');
        }
    }

    if ($section == 'preview') {
        $smarty->assign('usertype', 'C');
        $smarty->assign('html_page_title', func_get_langvar_by_name('lbl_preview'));

        if ($mode == 'fill') {
            $smarty->assign('template_name', 'modules/Survey/customer_view_message.tpl');
            $smarty->assign('section', 'preview');
        } else {
            $smarty->assign('template_name', 'modules/Survey/survey_preview.tpl');
        }
        func_display('customer/help/popup_info.tpl', $smarty);
        exit;
    }

    $has_stats = func_query_first_cell("SELECT * FROM $sql_tbl[survey_results] WHERE surveyid = '$surveyid'") > 0;
    if ($has_stats)
        $smarty->assign('has_stats', true);

} else {
    $survey = array();
    $ctime = XC_TIME;
    $survey['expires_data'] = mktime(0, 0, 0, date('m', $ctime)+1  , date('d', $ctime), date('Y', $ctime));
    $smarty->assign('survey', $survey);
    $location[] = array(func_get_langvar_by_name('lbl_survey_add_survey'), '');
}

if ($surveys_count > 0) {
    $dialog_tools_data['left'][] = array(
        'link' => 'surveys.php',
        'title' => func_get_langvar_by_name('lbl_survey_surveys_list')
    );
    $dialog_tools_data['left'][] = array(
        'link' => "survey.php?mode=create",
        'title' => func_get_langvar_by_name('lbl_survey_add_survey')
    );
}

$dialog_tools_data['right'][] = array(
    'link' => "configuration.php?option=Survey",
    'title' => func_get_langvar_by_name('lbl_survey_general_settings')
);

if (empty($surveyid))
    $section = '';

$smarty->assign('surveyid', $surveyid);
$smarty->assign('section', $section);
$smarty->assign('show', $show);
$smarty->assign('dialog_tools_data', $dialog_tools_data);

$smarty->assign('survey_types', func_get_survey_types());
?>
