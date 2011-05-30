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
 * Process storefont survey-related actions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: customer_survey.php,v 1.26.2.1 2011/01/10 13:12:03 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

// Validate the surveyid
if (!empty($surveyid))
    $surveyid = intval($surveyid);

x_session_register('antibot_survey_err');
x_session_register('saved_post_data');
x_session_register('mandatory_questions_err');

if ($REQUEST_METHOD == 'POST' && $mode == 'fill' && !empty($surveyid)) {
    // Save survey results

    // Check survey
    $survey = func_get_survey($surveyid);

    $now = XC_TIME;
    if (!$survey['valid'] || func_check_survey_filling($surveyid)) {
        $top_messsage = array(
            'content' => func_get_langvar_by_name('txt_survey_is_invalid_customer_warning'),
            'type' => 'E'
        );
        func_header_location('survey.php');
    }

    // Detect user
    $fill_userid = $logged_userid;
    $fill_user = false;
    if (!empty($survey_key)) {
        $fill_user = func_query_first("SELECT email, userid, as_result FROM $sql_tbl[survey_maillist] WHERE access_key = '$survey_key' AND surveyid = '$surveyid'");

        if (!empty($fill_user['userid']))
            $fill_userid = $fill_user['userid'];

    }

    $page = 'on_surveys';
    if (!empty($surveyid)) {
        $page .= '_'.$surveyid;
    }
    $antibot_survey_err = (!empty($active_modules['Image_Verification']) && func_validate_image($page, $antibot_input_str));
    if ($antibot_survey_err) {
        $top_message['type'] = 'E';
        $top_message['content'] = func_get_langvar_by_name('msg_err_antibot');
        $saved_post_data = $data;

        func_header_location("survey.php?surveyid=".$surveyid);
    }

    // Check if survey results is empty
    if (func_is_survey_result_empty($data)) {
        $top_message['type'] = 'W';
        $top_message['content'] = func_get_langvar_by_name('txt_survey_is_empty_message');

        func_header_location("survey.php?surveyid=$surveyid");
    }

    // Check if one or more mandatory question were not answered
    $mandatory_questions_err = func_mandatory_questions_answered($surveyid, $data);
    if (!empty($mandatory_questions_err)) {
        $top_message['type'] = 'W';
        $top_message['content'] = func_get_langvar_by_name('txt_survey_mandatory_questions_warning');
        $saved_post_data = $data;

        func_header_location("survey.php?surveyid=$surveyid");
    }

    // Save common data
    $query_data = array(
        'surveyid' => $surveyid,
        'date' => XC_TIME,
        'ip' => $CLIENT_IP,
        'userid' => $fill_userid,
        'code' => $shop_language,
        'from_mail' => empty($fill_user) ? 'N' : 'Y',
        'as_result' => empty($fill_user) ? '' : $fill_user['as_result']
    );
    $sresultid = func_array2insert('survey_results', $query_data);

    $quids = func_query_hash("SELECT questionid FROM $sql_tbl[survey_questions] WHERE surveyid = '$surveyid'", "questionid", false);
    $quids_count = count($quids);
    if (!empty($data)) {
        foreach ($data as $qid => $v) {
            $qid = intval($qid);

            // Get question
            $question = func_query_first("SELECT * FROM $sql_tbl[survey_questions] WHERE questionid = '$qid' AND surveyid = '$surveyid'");
            if (empty($question)) {
                unset($data[$qid]);
                continue;
            }

            $query_data = array(
                'sresultid' => $sresultid,
                'questionid' => $qid,
            );

            if ($question['answers_type'] == 'N') {
                // Save question with text field only

                $query_data['comment'] = $v['comment'];
                func_array2insert('survey_result_answers', $query_data);
                if (empty($v['comment']))
                    continue;

            } else {
                // Save question with answers

                // Get answers
                $answers = func_query_hash("SELECT * FROM $sql_tbl[survey_answers] WHERE questionid = '$qid'", "answerid", false);
                if (empty($answers)) {
                    unset($data[$qid]);
                    continue;
                }

                if (!empty($v['answers'])) {
                    if (!is_array($v['answers']))
                        $v['answers'] = array($v['answers']);

                    foreach ($v['answers'] as $aid) {
                        if (!isset($answers[$aid]))
                            continue;

                        $query_data['answerid'] = $aid;

                        if ($answers[$aid]['textbox_type'] != 'N' && isset($v['comment'][$aid]))
                            $query_data['comment'] = $v['comment'][$aid];

                        func_array2insert('survey_result_answers', $query_data);

                        if ($question['answers_type'] == 'R')
                            continue;

                    }
                }

            }

            func_unset($quids, $qid);
        }

    }

    $completed = empty($quids) ? 'Y' : (count($quids) == $quids_count ? 'E' : 'N');
    func_array2update('survey_results', array('completed' => $completed), "sresultid = '$sresultid'");

    $filled_surveys[$sresultid] = $surveyid;

    if (!empty($active_modules['SnS_connector']))
        func_generate_sns_action('FillSurvey', $sresultid);

    $top_message = array(
        'content' => !empty($survey['complete']) ? $survey['complete'] : func_get_langvar_by_name("txt_survey_default_complete_message")
    );

    $publish_results = func_query_first_cell("SELECT publish_results FROM $sql_tbl[surveys] WHERE surveyid = '$surveyid'");
    if ($publish_results == 'Y') {
        func_header_location("survey.php?surveyid=".$surveyid."&mode=view");
    } else {
        func_header_location('home.php');
    }

}

if ($REQUEST_METHOD == 'POST')
    func_header_location('survey.php');

include $xcart_dir . '/include/common.php';

if ((!empty($surveyid) || !empty($survey_key)) && empty($mode)) {
    // Display survey

    if (!empty($survey_key))
        $surveyid = func_query_first_cell("SELECT surveyid FROM $sql_tbl[survey_maillist] WHERE access_key = '$survey_key'");

    $survey = func_get_survey($surveyid);

    if (!empty($survey) && $survey['valid'] && ($survey['survey_type'] != 'R' || !empty($login) || !empty($survey_key))) {
        if (($check_res = func_check_survey_filling($surveyid))) {
            $top_message = array('type' => 'W');

            if ($check_res == 1) {
                $top_message['content'] = func_get_langvar_by_name("txt_survey_is_already_filling_sess");

            } elseif ($check_res == 2) {
                $top_message['content'] = func_get_langvar_by_name("txt_survey_is_already_filling_ip");

            } elseif ($check_res == 3) {
                $top_message['content'] = func_get_langvar_by_name("txt_survey_is_already_filling_login");
            }

            func_header_location('survey.php');
        }

        if (!empty($saved_post_data)) {
            $survey['questions'] = func_restore_user_answers($saved_post_data, $survey['questions']);

            $saved_post_data = false;
        }
        $smarty->assign('survey', $survey);
        $smarty->assign('survey_key', @$survey_key);

    } else {
        $top_message = array(
            'content' => func_get_langvar_by_name('txt_survey_is_invalid_customer_warning'),
            'type' => 'E'
        );
        func_header_location('survey.php');
    }

    $location[] = array($survey['survey']);
    $smarty->assign('main','survey');

} elseif ($mode == 'view' && !empty($surveyid)) {

    // Display survey results
    $survey = func_get_survey_results($surveyid);

    if (empty($survey) || !$survey['valid']) {
        $top_messsage = array(
            'content' => func_get_langvar_by_name('txt_survey_is_invalid_customer_warning'),
            'type' => 'E'
        );
        func_header_location('survey.php');

    }

    if (empty($filled_surveys) || !in_array($survey['surveyid'], $filled_surveys) || $survey['publish_results'] != 'Y' || $survey['count'] == 0)
        func_header_location('survey.php');

    // Check count of available and unfilled surveys
    $avail_unfilled_surveys = func_get_surveys_ids(true);
    if (!empty($avail_unfilled_surveys))
        $smarty->assign('avail_unfilled_surveys', true);

    $smarty->assign('survey', $survey);
    $location[] = array(func_get_langvar_by_name('lbl_survey_surveys'), 'survey.php');
    $location[] = array(func_get_langvar_by_name('lbl_survey_results'));
    $smarty->assign('main','view_results');

} else {
    // Get surveys list

    $now = XC_TIME;
    $allow_ids = array();
    if (!empty($allowed_surveys) && is_array($allowed_surveys))
        $allow_ids = func_array_merge($allow_ids, array_values($allowed_surveys));

    if (!empty($filled_surveys) && is_array($filled_surveys))
        $allow_ids = func_array_merge($allow_ids, $filled_surveys);

    $surveys = func_query_hash("SELECT $sql_tbl[surveys].* FROM $sql_tbl[surveys], $sql_tbl[survey_questions] WHERE $sql_tbl[surveys].valid_from_date < $now AND $sql_tbl[surveys].expires_data > $now AND $sql_tbl[surveys].surveyid = $sql_tbl[survey_questions].surveyid AND ($sql_tbl[surveys].survey_type = 'P'".(empty($login) ? "" : " OR $sql_tbl[surveys].survey_type = 'R'")." OR ($sql_tbl[surveys].survey_type = 'H' AND $sql_tbl[surveys].surveyid IN ('".implode("','", $allow_ids)."'))) GROUP BY $sql_tbl[surveys].surveyid ORDER BY $sql_tbl[surveys].orderby", "surveyid", false);

    $count_surveys = 0;
    $count_filled = 0;

    if (!empty($surveys)) {
        foreach($surveys as $sid => $v) {
            list($is_valid, $messages) = func_check_survey($sid);
            if (!$is_valid) {
                unset($surveys[$sid]);
                continue;
            }

            $count_surveys++;
            if (func_check_survey_filling($sid)) {
                $surveys[$sid]['is_filled'] = true;
                $count_filled++;
            }

            if (!empty($filled_surveys) && in_array($sid, $filled_surveys) && $v['publish_results'] == 'Y') {
                $id = array_search($sid, $filled_surveys);
                $rid = func_query_first_cell("SELECT sresultid FROM $sql_tbl[survey_results] WHERE sresultid = '$id' AND surveyid = '$sid'");
                if (!empty($rid)) {
                    $surveys[$sid]['is_view_results'] = true;
                } else {
                    func_unset($filled_surveys, $id);
                }
            }

            $surveys[$sid]['survey'] = func_get_languages_alt("survey_name_".$sid, false, true);
        }

        if (!empty($surveys)) {
            $smarty->assign('surveys', $surveys);
            $smarty->assign('count_surveys', $count_surveys);
            $smarty->assign('count_filled', $count_filled);
            $smarty->assign('count_unfilled', $count_surveys-$count_filled);
        }

    }

    $location[] = array(func_get_langvar_by_name('lbl_survey_surveys'));
    $smarty->assign('main','surveys');
}

if (!empty($antibot_survey_err)) {
    $smarty->assign('antibot_survey_err', $antibot_survey_err);
    $antibot_survey_err = false;
}

if (!empty($mandatory_questions_err)) {
    $smarty->assign('mandatory_questions_err', $mandatory_questions_err);
    $mandatory_questions_err = false;
}

// Assign the current location line
$smarty->assign('location', $location);

func_display('customer/home.tpl', $smarty);
?>
