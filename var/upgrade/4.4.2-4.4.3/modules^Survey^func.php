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
 * Functions of the Survey module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.php,v 1.35.2.2 2011/01/10 13:12:03 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

define('X_SURVEY_BAR_LENGTH', 200);

// This function checks if survey result is empty or no
// Parameters:
// $data - array posted by respondent
function func_is_survey_result_empty($data)
{
    if (is_array($data)) {
        foreach ($data as $_data) {
            if (!func_is_survey_result_empty($_data))
                return false;
        }
    }
    // Survey result is not empty
    else {
        $data = trim($data);
        if (!empty($data))
            return false;
    }

    // Survey result is empty
    return true;
}

/**
 * Check if all mandatory questions have been answered
 */
function func_mandatory_questions_answered($surveyid, $data)
{
    global $sql_tbl;

    $err = array();
    $mandatory_questions = func_query_hash("SELECT questionid, answers_type FROM $sql_tbl[survey_questions] WHERE surveyid='$surveyid' AND required='Y'","questionid",false,true);

    if (!empty($mandatory_questions)) {
        foreach($mandatory_questions as $qid => $type) {
            $field = ($type == 'N') ? 'comment' : 'answers';
            if (!$data || !$data[$qid] || empty($data[$qid][$field]))
                $err[$qid] = 1;

        }
    }
    return $err;
}

function func_restore_user_answers($user_answers, $survey_questions)
{
    foreach ($user_answers as $question_id => $answer) {

        if (!empty($answer['answers'])) {

            if (!is_array($answer['answers'])) {

                if (isset($survey_questions[$question_id]['answers'][$answer['answers']])) {

                    $survey_questions[$question_id]['answers'][$answer['answers']]['selected'] = true;

                }

            } else {

                foreach ($answer['answers'] as $answer_id) {

                    if (isset($survey_questions[$question_id]['answers'][$answer_id])) {

                        $survey_questions[$question_id]['answers'][$answer_id]['selected'] = true;

                    }

                }

            }

        }

        if (!empty($answer['comment'])) {

            if (is_array($answer['comment'])) {

                foreach ($answer['comment'] as $answer_id => $comment) {

                    if (isset($survey_questions[$question_id]['answers'][$answer_id])) {

                        $survey_questions[$question_id]['answers'][$answer_id]['comment'] = $comment;

                    }

                }

            } else {

                $survey_questions[$question_id]['comment'] = $answer['comment'];

            }

        }

    }

    return $survey_questions;
}

/**
 * Delete survey
 */
function func_delete_survey($surveyid)
{
    global $sql_tbl;

    x_load('backoffice');

    if (!is_array($surveyid))
        $surveyid = array($surveyid);

    $where = "surveyid IN ('".implode("','", $surveyid)."')";

    foreach ($surveyid as $id) {
        db_query("DELETE FROM $sql_tbl[languages_alt] WHERE name = 'survey_name_$id'");
        db_query("DELETE FROM $sql_tbl[languages_alt] WHERE name = 'survey_header_$id'");
        db_query("DELETE FROM $sql_tbl[languages_alt] WHERE name = 'survey_footer_$id'");
        db_query("DELETE FROM $sql_tbl[languages_alt] WHERE name = 'survey_complete_$id'");
    }

    db_query("DELETE FROM $sql_tbl[surveys] WHERE ".$where);
    $ids = func_query_column("SELECT questionid FROM $sql_tbl[survey_questions] WHERE ".$where);
    if (!empty($ids)) {
        foreach ($ids as $id) {
            db_query("DELETE FROM $sql_tbl[languages_alt] WHERE name = 'question_name_$id'");
        }

        $aids = func_query_column("SELECT answerid FROM $sql_tbl[survey_answers] WHERE questionid IN ('".implode("','", $ids)."')");
        if (!empty($aids)) {
            foreach ($aids as $id) {
                db_query("DELETE FROM $sql_tbl[languages_alt] WHERE name = 'answer_name_$id'");
            }
            db_query("DELETE FROM $sql_tbl[survey_answers] WHERE questionid IN ('".implode("','", $ids)."')");
        }
    }
    db_query("DELETE FROM $sql_tbl[survey_questions] WHERE ".$where);
    db_query("DELETE FROM $sql_tbl[survey_maillist] WHERE ".$where);

    func_delete_survey_stats($surveyid);

    return true;
}

/**
 * Delete survey statistics
 */
function func_delete_survey_stats($surveyid)
{
    global $sql_tbl, $filled_surveys;

    if (empty($surveyid))
        return false;

    if (!is_array($surveyid))
        $surveyid = array($surveyid);

    $where = "surveyid IN ('".implode("','", $surveyid)."')";

    $ids = func_query_column("SELECT sresultid FROM $sql_tbl[survey_results] WHERE ".$where);
    if (!empty($ids)) {
        db_query("DELETE FROM $sql_tbl[survey_result_answers] WHERE sresultid IN ('".implode("','", $ids)."')");
        db_query("DELETE FROM $sql_tbl[survey_results] WHERE ".$where);
        if (!empty($filled_surveys) && is_array($filled_surveys)) {
            foreach ($ids as $id) {
                if (isset($filled_surveys[$id]))
                    unset($filled_surveys[$id]);
            }
        }

        return true;
    }

    return false;
}

/**
 * Get survey types
 */
function func_get_survey_types($force = false)
{
    global $survey_types;
    $tmp = array();
    foreach ($survey_types as $t) {
        $tmp[$t] = array(
            'label' => func_get_langvar_by_name('lbl_survey_type_'.$t, array(), false, $force),
            'note' => func_get_langvar_by_name('lbl_survey_type_note_'.$t, array(), false, $force),
        );
    }

    return $tmp;
}

/**
 * Get answers types
 */
function func_get_answers_types($force = false)
{
    global $answers_types;
    $tmp = array();
    foreach ($answers_types as $t) {
        $tmp[$t] = func_get_langvar_by_name('lbl_survey_answers_type_'.$t, array(), false, $force);
    }

    return $tmp;
}

/**
 * Get survey_events
 */
function func_get_survey_events($force = false)
{
    global $survey_events;
    $tmp = array();
    foreach ($survey_events as $t) {
        $tmp[$t] = func_get_langvar_by_name('lbl_survey_event_'.$t, array(), false, $force);
    }

    return $tmp;
}

/**
 * Get survey
 */
function func_get_survey($surveyid)
{
    global $sql_tbl, $current_area, $shop_language, $config, $xcart_dir;

    $survey = func_query_first("SELECT * FROM $sql_tbl[surveys] WHERE surveyid = '$surveyid'");

    if (empty($survey))
        return false;

    $survey['survey'] = func_get_languages_alt("survey_name_".$surveyid, false, true);
    $survey['header'] = func_get_languages_alt("survey_header_".$surveyid, false, true);
    $survey['footer'] = func_get_languages_alt("survey_footer_".$surveyid, false, true);
    $survey['complete'] = func_get_languages_alt("survey_complete_".$surveyid, false, true);

    // Get questions
    $survey['questions'] = func_query_hash("SELECT * FROM $sql_tbl[survey_questions] WHERE surveyid = '$surveyid' ORDER BY orderby", "questionid", false);

    if (!empty($survey['questions'])) {

        $hash_name = array();
        $hash_aname = array();

        foreach ($survey['questions'] as $qid => $q) {

            $hash_name[] = 'question_name_'.$qid;

            if ($q['answers_type'] == 'N')
                continue;

            $q['answers'] = func_query_hash("SELECT * FROM $sql_tbl[survey_answers] WHERE questionid = '$qid' ORDER BY orderby", "answerid", false);
            if (!empty($q['answers'])) {
                foreach ($q['answers'] as $aid => $a) {
                    $hash_aname[] = 'answer_name_'.$aid;
                }
            }

            $survey['questions'][$qid] = $q;
        }

        $hash_name = func_get_languages_alt($hash_name, false, ($current_area == 'C' || $current_area == 'B'));
        $hash_aname = func_get_languages_alt($hash_aname, false, ($current_area == 'C' || $current_area == 'B'));

        foreach ($survey['questions'] as $qid => $q) {

            if (isset($hash_name['question_name_'.$qid]))
                $q['question'] = $survey['questions'][$qid]['question'] = $hash_name['question_name_'.$qid];

            if ($q['answers_type'] == 'N' || empty($q['answers']))
                continue;

            foreach ($q['answers'] as $aid => $a) {
                if (isset($hash_aname['answer_name_'.$aid]))
                    $q['answers'][$aid]['answer'] = $hash_aname['answer_name_'.$aid];
                    $q['answers'][$aid]['answerid'] = $aid;
            }

            $survey['questions'][$qid] = $q;
        }

        unset($hash_name, $hash_aname);
    }

    // Get events
    if (!empty($survey['event_type'])) {

        $tmp = func_query_hash("SELECT param, id FROM $sql_tbl[survey_events] WHERE surveyid = '$surveyid'", "param", true, true);

        if (!empty($tmp)) {

            $survey['event_elements'] = array();

            foreach ($tmp as $ep => $ids) {

                foreach ($ids as $id) {

                    $obj = false;

                    if ('P' == $ep) {

                        x_load('product');

                        $obj = func_select_product($id, 0, false, false, true);

                    } elseif ('D' == $ep) {

                        x_load('category');

                        $obj = func_get_category_data($id);

                        if (!empty($obj)) {
                            x_load('category');
                            $obj['category_path'] = func_get_category_path($obj['categoryid'], 'category', true);
                        }

                    } elseif ('T' == $ep) {

                        $obj = $id;

                        $id = 0;

                    } elseif (empty($obj)) {

                        continue;

                    }

                    $survey['event_elements'][$ep][$id] = $obj;

                } // foreach ($ids as $id)

            } // foreach ($tmp as $ep => $ids)

        }

    }

    list($survey['valid'], $survey['error_messages']) = func_check_survey($surveyid);

    if (empty($survey['error_messages']))
        $survey['error_messages'] = "";

    $survey['has_invitations'] = func_query_first_cell("SELECT COUNT(*) FROM " . $sql_tbl['survey_maillist'] . " WHERE surveyid = '" . $surveyid . "' AND sent_date = '0'") > 0;

    return $survey;
}

/**
 * Send survey invitation
 */
function func_send_survey_invitation($surveyid, $email, $userid, $force = false)
{
    global $mail_smarty, $config, $sql_tbl, $http_location;
    global $xcart_dir, $admin_safe_mode, $xcart_catalogs, $current_location;

    static $store_surveys = array();

    include $xcart_dir.'/include/safe_mode.php';

    x_load('mail', 'user');

    // Check respondent status
    $is_valid = (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[survey_maillist] WHERE surveyid = '$surveyid' AND email = '".addslashes($email)."' AND sent_date = '0'") > 0);
    if (!$is_valid && !$force)
        return -1;

    if (!isset($store_surveys[$surveyid])) {
        $store_surveys[$surveyid] = func_get_survey($surveyid);
    }

    $survey = $store_surveys[$surveyid];
    $survey['complete'] = func_eol2br($survey['complete']);
    $survey['header'] = func_eol2br($survey['header']);
    $survey['footer'] = func_eol2br($survey['footer']);
    $mail_smarty->assign('survey', $survey);

    // Geet access key
    $access_key = func_query_first_cell("SELECT access_key FROM $sql_tbl[survey_maillist] WHERE surveyid = '$surveyid' AND email = '".addslashes($email)."'");

    if (empty($access_key)) {
        // Generate access key
        srand(func_microtime());
        $access_key = md5(rand(0, XC_TIME));
        func_array2update('survey_maillist', array('access_key' => $access_key), "surveyid = '$surveyid' AND email = '".addslashes($email)."'");
    }
    $mail_smarty->assign('link', $http_location."/survey.php?survey_key=".$access_key);

    // Get repondents info (if respondent - customer)
    $userinfo = array();
    if (!empty($userid)) {
        $userinfo = func_userinfo($userid, 'C');
        $mail_smarty->assign('userinfo', $userinfo);

    } else {
        $mail_smarty->assign('userinfo', false);
    }

    // Send invitation
    $to_customer = ($userinfo['language'] ? $userinfo['language'] : $config['default_customer_language']);
    func_send_mail($email, 'mail/survey_invitation_subj.tpl', 'mail/survey_invitation.tpl', $config['Company']['support_department'], false);

    func_array2update('survey_maillist', array('sent_date' => XC_TIME, 'delay_date' => 0), "surveyid = '$surveyid' AND email = '".addslashes($email)."'");

    return true;
}

/**
 * Send survey invitations (surveyid based)
 */
function func_send_survey_invitations($surveyid, $limit = 0, $onsend = false)
{
    global $sql_tbl, $mail_smarty, $config;

    x_load('user', 'backoffice');

    $limit_str = $limit > 0 ? " LIMIT 0, $limit" : '';

    $survey = func_get_survey($surveyid);
    if (empty($survey) || !$survey['valid'])
        return -1;

    // Get respondents list
    $list = db_query("SELECT email, userid FROM $sql_tbl[survey_maillist] WHERE surveyid = '$surveyid' AND sent_date = '0' AND (delay_date = '0' OR delay_date < '".XC_TIME."') ORDER BY delay_date, date DESC".$limit_str);

    if (!$list)
        return -2;

    $mail_smarty->assign('survey', $survey);
    $cnt = 0;
    while ($row = db_fetch_array($list)) {

        // Send invitation
        func_send_survey_invitation($surveyid, $row['email'], $row['userid']);

        // Call 'onsend' event handler
        if (!empty($onsend) && function_exists($onsend))
            $onsend($row, $survey);

        $cnt++;
    }
    db_free_result($list);

    return $cnt;
}

/**
 * Periodically sending surveys invitations
 */
function func_send_survey_invitations_list($surveyid = 'all')
{
    global $config, $sql_tbl;

    $is_all = (strtolower($surveyid) == 'all');

    if ($is_all) {
        $where = '';

    } elseif (is_array($surveyid)) {
        $where = " AND $sql_tbl[surveys].surveyid IN ('".implode("','", $surveyid)."')";

    } else {
        $where = " AND $sql_tbl[surveys].surveyid = '$surveyid'";
    }

    $now = XC_TIME;
    // Get survey IDs list
    $ids = db_query("SELECT $sql_tbl[surveys].surveyid FROM $sql_tbl[surveys], $sql_tbl[survey_maillist] WHERE $sql_tbl[surveys].surveyid = $sql_tbl[survey_maillist].surveyid AND $sql_tbl[surveys].survey_type != 'D' AND $sql_tbl[surveys].valid_from_date < '$now' AND $sql_tbl[surveys].expires_data > '$now'".$where." GROUP BY $sql_tbl[surveys].surveyid");
    if (!$ids)
        return false;

    // Define survey limit
    $limit = abs(intval($config['Survey']['survey_sending_limit']));

    $i = 0;
    while (($surveyid = db_fetch_array($ids)) && ($limit > 0 || empty($config['Survey']['survey_sending_limit']))) {
        $surveyid = array_shift($surveyid);

        $t = XC_TIME;
        x_log_add('invitation', "Started sending survey invitations: survey #".$surveyid);

        $res = func_send_survey_invitations($surveyid, $limit);
        if ($res == -1) {
            // Survey can't found or survey is invalid
            x_log_add('invitation', "Error: survey can't found or survey is invalid");
            x_log_add('invitation', "Finished sending survey invitations");
            if (!$is_all)
                return false;

        } elseif ($res == -2) {
            // Survey has empty respondents list
            x_log_add('invitation', "Note: survey hasn't not sent invitations");
            x_log_add('invitation', "Finished sending survey invitations");
            if (!$is_all)
                return false;

        } else {
            if (!empty($config['Survey']['survey_sending_limit']))
                $limit -= $res;
            $i += $res;
        }

        x_log_add('invitation', "Finished sending survey invitations (".func_display_time_period(XC_TIME-$t).")");
    }

    return $i;
}

/**
 * 'onsend' event handler
 */
function func_survey_onsend_handler($row, $surveyid)
{
    x_log_add('invitation', date("m/d/Y H:i:s")." invitaion sent: ".$row['email']);
}

/**
 * Check respondent email
 */
function func_check_unique_email($email, $surveyid)
{
    global $sql_tbl;

    x_load('mail');
    if (!func_check_email($email))
        return false;

    return func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[survey_maillist] WHERE surveyid = '$surveyid' AND email = '$email'") == 0;
}

/**
 * Check surveys events
 */
function func_check_surveys_events($event, $data = array(), $userid = false)
{
    global $survey_events, $logged_userid, $config, $sql_tbl, $allowed_surveys;

    if (!in_array($event, $survey_events))
        return false;

    if (empty($userid)) {
        $userid = $logged_userid;
    }

    // Get survey's IDs
    $now = XC_TIME;
    $ids = func_query_hash("SELECT $sql_tbl[surveys].surveyid, $sql_tbl[survey_events].param, $sql_tbl[survey_events].id FROM $sql_tbl[surveys] LEFT JOIN $sql_tbl[survey_events] ON $sql_tbl[surveys].surveyid = $sql_tbl[survey_events].surveyid WHERE $sql_tbl[surveys].event_type = '$event' AND $sql_tbl[surveys].survey_type != 'D' AND $sql_tbl[surveys].valid_from_date < '$now' AND $sql_tbl[surveys].expires_data > '$now'", "surveyid");
    if (empty($ids))
        return false;

    $i = 0;

    foreach ($ids as $id => $params) {

        if (!empty($params) && !empty($params[0]['param'])) {

            // Check by event conditions
            $is_or = (func_query_first_cell("SELECT event_logic FROM $sql_tbl[surveys] WHERE surveyid = '$id'") == 'O');
            $params_count = count(func_query_column("SELECT COUNT(*) FROM $sql_tbl[survey_events] WHERE surveyid = '$id' GROUP BY param"));
            $avail = array();
            foreach ($params as $p) {

                if (empty($p['id']))
                    continue;

                if ('T' == $p['param']) {

                    if ($data['order']['total'] > $p['id'])
                        $avail[$p['param']]++;

                } elseif ('P' == $p['param']) {

                    foreach ($data['products'] as $product) {

                        if ($product['productid'] == $p['id']) {

                            $avail[$p['param']]++;

                            break;

                        }

                    }

                } elseif ('D' == $p['param']) {

                    foreach ($data['products'] as $product) {

                        if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products_categories] WHERE productid = '$product[productid]' AND categoryid = '$p[id]'") > 0) {

                            $avail[$p['param']]++;

                            break;

                        }

                    }

                }

            }

            if (count($avail) == 0 || (count($avail) < $params_count && !$is_or))
                continue;

        }

        // Check survey availability
        list($valid, $tmp) = func_check_survey($id);

        if (!$valid)
            continue;

        // Get user email
        $email = func_query_first_cell("SELECT email FROM $sql_tbl[customers] WHERE id = '$userid'");

        if (empty($email))
            continue;

        // Check login and email
        $mail_users = func_query_column("SELECT id FROM $sql_tbl[customers] WHERE email = '" . addslashes($email) . "'");

        $user_exists = false;

        if (!empty($mail_users)) {
            $user_exists = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[survey_results] WHERE userid IN ('" . implode("','", $mail_users) ."') AND surveyid = '$id'") > 0;
        }

        if (func_check_unique_email($email, $id) && !$user_exists) {

            $as_result = '';

            if (in_array($event, array('OPL', 'OPC', 'OPP', 'OPB')))
                $as_result = $event.$data['order']['orderid'];

            // Add unique email
            $query_data = array(
                'email' => addslashes($email),
                'userid' => $userid,
                'surveyid' => $id,
                'as_result' => $as_result,
                'date' => XC_TIME
            );

            func_array2insert('survey_maillist', $query_data);

            if ($config['Survey']['survey_send_after_event'] == 'Y') {
                if ($config['Survey']['survey_event_sent_delay'] > 0) {
                    func_array2update('survey_maillist', array('delay_date' => XC_TIME+$config['Survey']['survey_event_sent_delay']*3600), "email = '".addslashes($email)."' AND surveyid = '$id'");
                } else {
                    func_send_survey_invitation($id, $email, $userid);
                }
            }
        }

        $allowed_surveys[$id] = $id;

        $i++;
    }

    return $i;
}

/**
 * Check survey filling of current customer
 */
function func_check_survey_filling($surveyid)
{
    global $logged_userid, $CLIENT_IP, $sql_tbl, $config, $filled_surveys;
    static $result_cache = array();

    if (defined('X_TEST_MODE')) 
        return false;

    if (!is_array($surveyid)) {
        $surveyid = array((int)$surveyid);
    }

    $key = md5(serialize($surveyid) . serialize($filled_surveys));

    if (isset($result_cache[$key]))
        return $result_cache[$key];

    if (!empty($filled_surveys)) {
        $tmp = array_intersect($surveyid, $filled_surveys);
        if (!empty($tmp)) {
            $result_cache[$key] = 1;
            return 1;
        }    
    }

    // Block by IP
    if ($config['Survey']['survey_ip_blocked_period'] > 0) {
        $date = intval(func_query_first_cell("SELECT MAX(date) FROM $sql_tbl[survey_results] WHERE ip = '$CLIENT_IP' AND surveyid IN ('".implode("','", $surveyid)."')"));
        if ($date > 0 && $date > (XC_TIME-intval($config['Survey']['survey_ip_blocked_period'])*86400)) {
            $result_cache[$key] = 2;
            return 2;
        }    
    }

    if (!empty($logged_userid) && func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[survey_results] WHERE userid = '$logged_userid' AND surveyid IN ('".implode("','", $surveyid)."')") > 0) {
        $result_cache[$key] = 3;
        return 3;
    }    

    $result_cache[$key] = false;
    return false;
}

/**
 * Get survey results
 */
function func_get_survey_results($surveyid, $is_general = false, $filter_surveys = array())
{
    global $sql_tbl;

    $survey = func_get_survey($surveyid);
    if (!empty($survey['questions'])) {

        // Prepare the date range for survey results
        $date_condition = '';
        if (
            defined('AREA_TYPE') &&
            constant('AREA_TYPE') == 'A' &&
            isset($filter_surveys['date_from']) &&
            isset($filter_surveys['date_to']) &&
            !empty($filter_surveys['date_from']) &&
            !empty($filter_surveys['date_to'])
        ) {
            $date_condition = "AND $sql_tbl[survey_results].date >= '".intval($filter_surveys['date_from'])."' AND $sql_tbl[survey_results].date <= '".intval($filter_surveys['date_to'])."'";
        }

        // Get count of survey instances
        $survey['count'] = intval(func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[survey_results] WHERE surveyid = '$surveyid' $date_condition"));
        foreach ($survey['questions'] as $qid => $q) {
            if ($q['answers_type'] != 'N' && empty($q['answers'])) {
                unset($survey['questions'][$qid]);
                continue;
            }

            // Get question filling counter
            if ($q['answers_type'] == 'N') {
                $q['result_filled'] = intval(func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[survey_result_answers], $sql_tbl[survey_results] WHERE $sql_tbl[survey_result_answers].sresultid=$sql_tbl[survey_results].sresultid AND $sql_tbl[survey_result_answers].questionid = '$qid' AND $sql_tbl[survey_result_answers].answerid = '0' AND $sql_tbl[survey_result_answers].comment != '' $date_condition"));
                $q['bar_width'] = ($survey['count'] > 0) ? floor($q['result_filled'] / $survey['count'] * X_SURVEY_BAR_LENGTH) : 0;
                $q['percent'] = ($survey['count'] > 0) ? round($q['result_filled'] / $survey['count'] * 100, 2) : 0;
                $survey['questions'][$qid] = $q;
                continue;

            } else {
                $q['result_filled'] = 0;
                $tmp = db_query("SELECT COUNT(*) FROM $sql_tbl[survey_result_answers], $sql_tbl[survey_results] WHERE $sql_tbl[survey_result_answers].sresultid=$sql_tbl[survey_results].sresultid AND $sql_tbl[survey_result_answers].questionid = '$qid' $date_condition GROUP BY $sql_tbl[survey_result_answers].sresultid");
                if ($tmp) {
                    $q['result_filled'] = db_num_rows($tmp);
                    db_free_result($tmp);
                }
            }

            // Get question answers results
            $max_result = 0;
            $max_answerid = 0;
            $hash_results = func_query_hash("SELECT answerid, COUNT(*) as cnt FROM $sql_tbl[survey_result_answers], $sql_tbl[survey_results] WHERE $sql_tbl[survey_result_answers].sresultid=$sql_tbl[survey_results].sresultid AND $sql_tbl[survey_result_answers].questionid = '$qid' $date_condition GROUP BY $sql_tbl[survey_result_answers].answerid", "answerid", false, true);
            $hash_comments = func_query_hash("SELECT answerid, COUNT(*) as cnt FROM $sql_tbl[survey_result_answers], $sql_tbl[survey_results] WHERE $sql_tbl[survey_result_answers].sresultid=$sql_tbl[survey_results].sresultid AND $sql_tbl[survey_result_answers].questionid = '$qid' AND $sql_tbl[survey_result_answers].comment != '' GROUP BY $sql_tbl[survey_result_answers].answerid", "answerid", false, true);

            foreach ($q['answers'] as $aid => $a) {

                $q['answers'][$aid]['result'] = $a['result'] = intval($hash_results[$aid]);

                if ($q['answers'][$aid]['result'] > $max_result) {
                    $max_result = $q['answers'][$aid]['result'];
                    $max_answerid = $aid;
                }

                $q['answers'][$aid]['result_comment'] = intval(@$hash_comments[$aid]);
                $q['answers'][$aid]['bar_width'] = ($q['result_filled'] > 0) ? floor($a['result'] / $q['result_filled'] * X_SURVEY_BAR_LENGTH) : 0;
                $q['answers'][$aid]['bar_width_invert'] = X_SURVEY_BAR_LENGTH - $q['answers'][$aid]['bar_width'];
                $q['answers'][$aid]['percent'] = ($q['result_filled'] > 0) ? round($a['result'] / $q['result_filled'] * 100, 2) : 0;
            }

            if ($max_answerid > 0) {
                // Define highlighted answer with maximum result
                $cnt = 0;
                foreach ($q['answers'] as $aid => $a) {
                    if ($a['result'] == $max_result) {
                        $cnt++;
                    }
                }

                if ($cnt == 1)
                    $q['answers'][$max_answerid]['highlighted'] = true;
            }

            $q['percent'] = ($survey['count'] > 0) ? round($q['result_filled'] / $survey['count'] * 100 , 2) : 0;

            $survey['questions'][$qid] = $q;
        }

        if ($is_general) {
            // Generate general statistics
            $survey['general_stats'] = array(
                'maillist_count' => intval(func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[survey_maillist] WHERE surveyid = '$surveyid'")),
                'invitations_sent' => intval(func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[survey_maillist] WHERE sent_date > '0' AND surveyid = '$surveyid'")),
                'started_filling' => intval(func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[survey_results] WHERE surveyid = '$surveyid'")),
                'completed_filling' => intval(func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[survey_results] WHERE surveyid = '$surveyid' AND completed = 'Y'")),
                'first_filled' => intval(func_query_first_cell("SELECT date FROM $sql_tbl[survey_results] WHERE surveyid = '$surveyid' ORDER BY date")),
                'last_filled' => intval(func_query_first_cell("SELECT date FROM $sql_tbl[survey_results] WHERE surveyid = '$surveyid' ORDER BY date DESC")),
            );
        }
    }

    return $survey;
}

/**
 * Get survey result
 */
function func_get_survey_instance($sresultid)
{
    global $sql_tbl;

    $result = func_query_first("SELECT $sql_tbl[survey_results].*, $sql_tbl[customers].usertype, $sql_tbl[customers].login FROM $sql_tbl[survey_results] LEFT JOIN $sql_tbl[customers] ON $sql_tbl[survey_results].userid = $sql_tbl[customers].id WHERE $sql_tbl[survey_results].sresultid = '$sresultid'");
    if (empty($result))
        return false;

    $survey = func_get_survey($result['surveyid']);
    $survey['result'] = $result;

    if (!empty($survey['questions'])) {
        foreach ($survey['questions'] as $qid => $q) {
            if (empty($q['answers']) && $q['answers_type'] != 'N')
                continue;

            if (empty($q['answers'])) {
                $survey['questions'][$qid]['comment'] = func_query_first_cell("SELECT comment FROM $sql_tbl[survey_result_answers] WHERE questionid = '$qid' AND sresultid = '$sresultid'");
                continue;
            }

            foreach ($q['answers'] as $aid => $a) {
                $tmp = func_query_first("SELECT answerid, comment FROM $sql_tbl[survey_result_answers] WHERE questionid = '$qid' AND sresultid = '$sresultid' AND answerid = '$aid'");
                $q['answers'][$aid]['selected'] = ($tmp['answerid'] == $aid);
                $q['answers'][$aid]['comment'] = $tmp['comment'];
            }
            $survey['questions'][$qid] = $q;
        }
    }

    return $survey;
}

function func_check_survey($surveyid)
{
    global $sql_tbl, $usertype;
    static $result_cache = array();

    if (isset($result_cache[$surveyid]))
        return $result_cache[$surveyid];

    $is_exists = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[surveys] WHERE surveyid = '$surveyid'");
    if (empty($is_exists)) {
        $result_cache[$surveyid] = array(false, array(array('label' => 'no_exists')));
        return array(false, array(array('label' => 'no_exists')));
    }    

    $messages = array();

    // Define error conditions
    $errors = array(
        'no_questions' => array(
            'sql' => "SELECT COUNT(*) FROM $sql_tbl[survey_questions] LEFT JOIN $sql_tbl[survey_answers] ON $sql_tbl[survey_questions].questionid = $sql_tbl[survey_answers].questionid WHERE $sql_tbl[survey_questions].surveyid = '$surveyid' AND ($sql_tbl[survey_answers].questionid IS NOT NULL OR $sql_tbl[survey_questions].answers_type = 'N')",
            'error_is_empty' => true,
            'go_link' => 'structure'
        ),
        'hidden_no_events_or_maillist' => array(
            'sql_condition' => "SELECT COUNT(*) FROM $sql_tbl[surveys] WHERE survey_type = 'H' AND surveyid = '$surveyid'",
            'sql' => "SELECT COUNT(*) FROM $sql_tbl[surveys] LEFT JOIN $sql_tbl[survey_maillist] ON $sql_tbl[surveys].surveyid = $sql_tbl[survey_maillist].surveyid LEFT JOIN $sql_tbl[survey_events] ON $sql_tbl[surveys].surveyid = $sql_tbl[survey_events].surveyid WHERE $sql_tbl[surveys].surveyid = '$surveyid' AND ($sql_tbl[survey_maillist].surveyid IS NOT NULL OR $sql_tbl[survey_events].surveyid IS NOT NULL)",
            'error_is_empty' => true,
            'go_link' => 'maillist'
        ),
        'time_period_in_future' => array(
            'sql' => "SELECT COUNT(*) FROM $sql_tbl[surveys] WHERE valid_from_date < '".XC_TIME."' AND surveyid = '$surveyid'",
            'error_is_empty' => true,
            'go_link' => 'details'
        ),
        'time_period_in_past' => array(
            'sql' => "SELECT COUNT(*) FROM $sql_tbl[surveys] WHERE expires_data > '".XC_TIME."' AND surveyid = '$surveyid'",
            'error_is_empty' => true,
            'go_link' => 'details'
        ),
        'disabled' => array(
            'sql' => "SELECT COUNT(*) FROM $sql_tbl[surveys] WHERE survey_type <> 'D' AND surveyid = '$surveyid'",
            'error_is_empty' => true,
            'go_link' => 'details'
        ),
        'question_without_answers' => array(
            'sql' => "SELECT COUNT(*) as cnt FROM $sql_tbl[survey_questions] LEFT JOIN $sql_tbl[survey_answers] ON $sql_tbl[survey_questions].questionid = $sql_tbl[survey_answers].questionid WHERE $sql_tbl[survey_questions].surveyid = '$surveyid' AND $sql_tbl[survey_questions].answers_type != 'N' GROUP BY $sql_tbl[survey_questions].questionid HAVING cnt < '2'",
            'error_is_not_empty' => true,
            'warning' => true,
            'go_link' => 'structure'
        ),
        'big_menu_survey' => array(
            'sql' => "SELECT COUNT(*) FROM $sql_tbl[surveys] LEFT JOIN $sql_tbl[survey_questions] ON $sql_tbl[surveys].surveyid = $sql_tbl[survey_questions].surveyid WHERE $sql_tbl[surveys].surveyid = '$surveyid' AND $sql_tbl[surveys].display_on_frontpage = 'Y'",
            'error_is_high_limit' => 2,
            'warning' => true,
            'go_link' => 'structure'
        )
    );

    // Check error conditions
    $is_error = false;
    foreach ($errors as $ecode => $data) {
        if (!empty($data['sql_condition'])) {
            $res = func_query_first_cell($data['sql_condition']);
            if (empty($res))
                continue;
        }

        $res = func_query_first_cell($data['sql']);

        $found = (empty($res) && isset($data['error_is_empty'])) ||
            (!empty($res) && isset($data['error_is_not_empty'])) ||
            (isset($data['error_is_high_limit']) && $res > $data['error_is_high_limit']);

        if ($found) {
            $messages[$ecode] = array(
                'label' => func_get_langvar_by_name('lbl_survey_error_msg_'.$ecode),
                'warning' => $data['warning'],
                'go_link' => $data['go_link']
            );
            if (empty($data['warning']))
                $is_error = true;
        }

        if ($is_error && $usertype == 'C') {
            $result_cache[$surveyid] = array(false, $messages);
            return array(false, $messages);
        }    
    }

    $result_cache[$surveyid] = array(!$is_error, $messages);
    return array(!$is_error, $messages);
}

/**
 * Clone survey(s)
 */
function func_clone_survey($surveyid)
{
    global $sql_tbl;

    if (!is_array($surveyid))
        $surveyid = array($surveyid);

    $i = 0;
    foreach ($surveyid as $id) {
        $data = func_query_first("SELECT * FROM $sql_tbl[surveys] WHERE surveyid = '$id'");
        if (empty($data))
            continue;

        // Clone survey details
        unset($data['surveyid']);
        $data['survey_type'] = 'D';
        func_addslashes($data);
        $newid = func_array2insert('surveys', $data);
        if (!$newid)
            continue;

        // Clone survey multilanguage variables
        $vars = array('name', 'header', 'footer', 'complete');
        foreach ($vars as $v) {
            $lang = func_query_hash("SELECT code, value FROM $sql_tbl[languages_alt] WHERE name = 'survey_".$v."_".$id."'", "code", false, true);
            if (empty($lang))
                continue;

            foreach ($lang as $c => $l) {
                if ($v == 'name')
                    $l .= " (CLONE)";

                func_languages_alt_insert('survey_'.$v.'_'.$newid, addslashes($l), $c);
            }
        }

        // Clone survey questions
        $questions = func_query_hash("SELECT * FROM $sql_tbl[survey_questions] WHERE surveyid = '$id'", "questionid", false);
        if (!empty($questions)) {
            foreach ($questions as $qid => $q) {
                $q = func_addslashes($q);
                $q['surveyid'] = $newid;
                $newqid = func_array2insert('survey_questions', $q);
                if (empty($newqid))
                    continue;

                // Clone question names
                $lang = func_query_hash("SELECT code, value FROM $sql_tbl[languages_alt] WHERE name = 'question_name_".$qid."'", "code", false, true);
                if (!empty($lang)) {
                    foreach ($lang as $c => $l) {
                        func_languages_alt_insert('question_name_'.$newqid, addslashes($l), $c);
                    }
                }

                if ($q['answers_type'] == 'N')
                    continue;

                // Clone question answers
                $answers = func_query_hash("SELECT * FROM $sql_tbl[survey_answers] WHERE questionid = '$qid'", "answerid", false);
                if (empty($answers))
                    continue;

                foreach ($answers as $aid => $a) {
                    $a = func_addslashes($a);
                    $a['questionid'] = $newqid;
                    $newaid = func_array2insert('survey_answers', $a);
                    if (empty($newaid))
                        continue;

                    // Clone answer names
                    $lang = func_query_hash("SELECT code, value FROM $sql_tbl[languages_alt] WHERE name = 'answer_name_".$aid."'", "code", false, true);
                    if (!empty($lang)) {
                        foreach ($lang as $c => $l) {
                            func_languages_alt_insert('answer_name_'.$newaid, addslashes($l), $c);
                        }
                    }

                }
            }
        }

        // Clone events
        $events = func_query("SELECT * FROM $sql_tbl[survey_events] WHERE surveyid = '$id'");
        if (!empty($events)) {
            foreach ($events as $e) {
                $e['surveyid'] = $newid;
                func_array2insert('survey_events', $e);
            }
        }

        $i++;
    }

    return $i;
}

/**
 * Get available survey IDs list
 */
function func_get_surveys_ids($no_filled = false)
{
    global $sql_tbl, $allowed_surveys, $filled_surveys, $config, $logged_userid, $CLIENT_IP;

    $no_filled_where = '';

    $no_filled_join = '';

    if ($no_filled && !defined('X_TEST_MODE')) {
        // Filter filled surveys

        $time = XC_TIME - intval($config['Survey']['survey_ip_blocked_period']) * 86400;

        $no_filled_join = "LEFT JOIN $sql_tbl[survey_results] ON $sql_tbl[surveys].surveyid = $sql_tbl[survey_results].surveyid AND (($sql_tbl[survey_results].ip = '$CLIENT_IP' AND $sql_tbl[survey_results].date > $time)" . (empty($logged_userid) ? "" : " OR $sql_tbl[survey_results].userid = '$logged_userid'") . ")";

        $no_filled_where = " AND $sql_tbl[survey_results].sresultid IS NULL";

        if (!empty($filled_surveys))
            $no_filled_where .= " AND $sql_tbl[surveys].surveyid NOT IN ('".implode("','", $filled_surveys)."')";

    }

    $now = XC_TIME;

    $ids = func_query_column("
        SELECT $sql_tbl[surveys].surveyid
        FROM $sql_tbl[surveys]
        LEFT JOIN $sql_tbl[survey_questions] ON $sql_tbl[surveys].surveyid = $sql_tbl[survey_questions].surveyid
        LEFT JOIN $sql_tbl[survey_answers] ON $sql_tbl[survey_questions].questionid = $sql_tbl[survey_answers].questionid
        LEFT JOIN $sql_tbl[survey_maillist] ON $sql_tbl[surveys].surveyid = $sql_tbl[survey_maillist].surveyid
        LEFT JOIN $sql_tbl[survey_events] ON $sql_tbl[surveys].surveyid = $sql_tbl[survey_events].surveyid
        $no_filled_join
        WHERE ($sql_tbl[surveys].survey_type = 'P' OR ($sql_tbl[surveys].survey_type = 'R' AND '$logged_userid' <> '') OR ($sql_tbl[surveys].survey_type = 'H' AND $sql_tbl[surveys].surveyid IN ('".implode("','", array_values((array)$allowed_surveys))."'))) AND $sql_tbl[survey_questions].questionid IS NOT NULL AND ($sql_tbl[survey_answers].questionid IS NOT NULL OR $sql_tbl[survey_questions].answers_type = 'N') AND ($sql_tbl[surveys].survey_type != 'H' OR $sql_tbl[survey_maillist].surveyid IS NOT NULL OR $sql_tbl[survey_events].surveyid IS NOT NULL) AND $sql_tbl[surveys].valid_from_date < '$now' AND $sql_tbl[surveys].expires_data > '$now' $no_filled_where
        GROUP BY $sql_tbl[surveys].surveyid
        ORDER BY $sql_tbl[surveys].orderby
    ");

    return $ids;
}

/**
 * Get the answer comments and question comments
 */
function func_survey_get_comments ($questionid, $answerid, $filter_surveys = array())
{
    global $sql_tbl;

    if (constant('AREA_TYPE') == 'A' && !empty($filter_surveys['date_from']) && !empty($filter_surveys['date_to']))
        $date_condition = "AND $sql_tbl[survey_results].date>='".intval($filter_surveys['date_from'])."' AND $sql_tbl[survey_results].date<='".intval($filter_surveys['date_to'])."'";

    $texts = func_query("SELECT $sql_tbl[survey_results].surveyid, $sql_tbl[survey_results].sresultid, $sql_tbl[survey_results].date, $sql_tbl[survey_result_answers].comment FROM $sql_tbl[survey_result_answers], $sql_tbl[survey_results] WHERE $sql_tbl[survey_result_answers].sresultid=$sql_tbl[survey_results].sresultid AND $sql_tbl[survey_result_answers].answerid = '$answerid' AND $sql_tbl[survey_result_answers].questionid = '$questionid' AND $sql_tbl[survey_result_answers].comment != '' $date_condition ORDER BY $sql_tbl[survey_results].date DESC");

    return $texts;
}

/**
 * Get link to object based on as_result column from xcart_surve_results or xcart_survey_maillist tables
 */
function func_as_result2obj_link($as_result)
{
    global $survey_events, $sql_tbl;

    if (empty($as_result))
        return false;

    $event = substr($as_result, 0, 3);
    $id = substr($as_result, 3);
    if (!in_array($event, $survey_events) || empty($id))
        return false;

    if (in_array($event, array('OPL', 'OPC', 'OPP', 'OPB'))) {
        $return = array('obj_name' => func_get_langvar_by_name('lbl_order')." #".$id);
        if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[orders] WHERE orderid = '$id'") > 0)
            $return['link'] = "order.php?orderid=".$id;
        return $return;
    }

    return false;
}

/**
 * Call event FillSurvey for SnS connector
 */
function func_survey_fillsurvey_event(&$cpost, $sresultid)
{
    global $sql_tbl;

    $surveyid = func_query_first_cell("SELECT surveyid FROM $sql_tbl[survey_results] WHERE sresultid = '$sresultid'");
    if (empty($surveyid))
        return false;

    $cpost = "survey_name=".urlencode(func_get_languages_alt('survey_name_'.$surveyid, false, true));

    return true;
}
?>
