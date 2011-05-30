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
 * Languages management
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Administration
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: languages.php,v 1.111.2.6 2011/01/10 13:11:45 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

define('USE_TRUSTED_POST_VARIABLES',1);
define('USE_TRUSTED_GET_VARS','topic');
define('USE_TRUSTED_SCRIPT_VARS',1);

$trusted_post_variables = array('var_value', 'new_var_value', 'topic', 'new_topic');

require './auth.php';

require $xcart_dir.'/include/security.php';

x_load('files', 'backoffice');

x_session_register('serverfile');

$topics = func_query_column("SELECT topic FROM $sql_tbl[languages] WHERE topic<>'' GROUP BY topic ORDER BY topic");

$_topic = stripslashes($topic);

if (!in_array($_topic, $topics))
    $topic = $_topic = '';

$languages = $all_languages;

if (
    $REQUEST_METHOD == 'GET'
    && empty($mode)
    && !isset($language)
    && count($languages) == 1
) {
    // If only one language available in the store
    // then automatically select it
    func_header_location('languages.php?language=' . $current_language);
}

settype($edit_default_language, 'string');
if (
    $REQUEST_METHOD == 'GET'
    && !isset($language)
    && $edit_default_language == 'Y'
) {
    $language = empty($config['default_customer_language']) ? $config['default_admin_language'] : $config['default_customer_language'];
}


if ($mode == 'change_defaults') {

    require $xcart_dir.'/include/safe_mode.php';

    if (!empty($new_customer_language))
        db_query("UPDATE $sql_tbl[config] SET value='$new_customer_language' WHERE name='default_customer_language'");

    if (!empty($new_admin_language))
        db_query("UPDATE $sql_tbl[config] SET value='$new_admin_language' WHERE name='default_admin_language'");

    func_header_location('languages.php' . ($language ? ('?language=' . $language) : ''));
}

if (!empty($language) && !empty($mode)) {

    // Check post_max_size exceeding

    func_check_uploaded_files_sizes('import_file', 883);

    if ($mode == 'update_charset') {

        require $xcart_dir.'/include/safe_mode.php';

        func_array2update(
            'language_codes',
            array(
                'charset' => $charset,
                'r2l' => (isset($text_dir) && $text_dir == 'Y') ? 'Y' : ''
            ),
            "code = '$language'"
        );

        $lngid = func_query_first_cell("SELECT lngid FROM $sql_tbl[language_codes] WHERE code = '" . $language . "'");

        if (func_check_image_posted($file_upload_data, 'G') && $lngid > 0) {
            func_save_image($file_upload_data, 'G', $lngid);
        }

        func_data_cache_get('charsets', array(), true);
        func_data_cache_clear('languages');

    } elseif ($mode == 'delete_image') {

        require $xcart_dir.'/include/safe_mode.php';

        $lngid = func_query_first_cell("SELECT lngid FROM $sql_tbl[language_codes] WHERE code = '" . $language . "'");

        func_delete_image($lngid, 'G');

        func_data_cache_clear('languages');

    } elseif ($mode == 'update') {

        require $xcart_dir.'/include/safe_mode.php';

        if ($var_value) {

            foreach ($var_value as $key => $value) {

                $exists = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[languages] WHERE code='$language' AND name='$key'");

                if ($exists) {

                    func_array2update('languages', array('value' => $value), "code='$language' AND name='$key'");

                } else {

                    $_topic = func_query_first_cell("SELECT topic FROM $sql_tbl[languages] WHERE name='$key'");

                    func_array2insert('languages',
                        array(
                            'code'  => $language,
                            'name'  => $key,
                            'value' => $value,
                            'topic' => $_topic
                        ),
                        true
                    );

                }

                if ($memcache) {

                    func_delete_mcache_data('inner_lng_' . $language . $key);

                }

            }

            func_data_cache_clear('get_language_vars');
            func_data_cache_clear('get_default_fields');
        }

        if ($topic == 'Languages') {
            func_data_cache_get('languages', array($language), true);
        }

        $top_message = array(
            'content' => func_get_langvar_by_name('lbl_lng_variable_updated')
        );

        $smarty->clear_all_cache();
        $smarty->clear_compiled_tpl();

        func_header_location("languages.php?language=$language&page=$page&filter=".urlencode(stripslashes($filter))."&topic=$_topic");

    } elseif ($mode == 'add') {

        require $xcart_dir.'/include/safe_mode.php';

        if (empty($new_var_name)) {
            $top_message = array(
                'content' => func_get_langvar_by_name("msg_err_empty_label"),
                'type' => "E"
            );
            func_header_location("languages.php?language=$language&page=$page&filter=".urlencode(stripslashes($filter))."&topic=$_topic");

        } elseif ($new_var_name != preg_replace('/[^A-Za-z0-9_]/', '', $new_var_name)) {
            $top_message = array(
                'content' => func_get_langvar_by_name("msg_err_invalid_label"),
                'type' => "E"
            );
            func_header_location("languages.php?language=$language&page=$page&filter=".urlencode(stripslashes($filter))."&topic=$_topic");
        }

        $topic = in_array(stripslashes($new_topic), $topics) ? $new_topic : $topics[0];

        $is_exists = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[languages] WHERE name = '$new_var_name' AND code='$language'") > 0;
        if ($is_exists) {
            func_array2update('languages',
                array(
                    'value' => $new_var_value
                ),
                "name='$new_var_name' AND code='$language'"
            );
        } else {
            foreach ($languages as $key=>$value) {
                func_array2insert('languages',
                    array(
                        'code' => $value['code'],
                        'name' => $new_var_name,
                        'value' => $new_var_value,
                        'topic' => $topic
                    ),
                    true
                );
            }
        }

        if ($topic == 'Languages') {
            func_data_cache_get('languages', array($language), true);
        }

        $top_message = array(
            'content' => func_get_langvar_by_name('lbl_lng_variable_added')
        );

        func_data_cache_clear('get_language_vars');
        func_data_cache_clear('get_default_fields');
        func_header_location("languages.php?language=$language&page=$page&filter=".urlencode(stripslashes($filter))."&topic=$_topic");

    } elseif ($mode == 'delete' && !empty($ids)) {

        require $xcart_dir.'/include/safe_mode.php';

        db_query ("DELETE FROM $sql_tbl[languages] WHERE name IN ('".implode("','", $ids)."')");

        if ($topic == 'Languages') {
            func_data_cache_get('languages', array($language), true);
        }

        $top_message = array(
            'content' => func_get_langvar_by_name('lbl_lng_variables_deleted')
        );

        func_header_location("languages.php?language=$language&page=$page&filter=".urlencode(stripslashes($filter))."&topic=$topic");

    } elseif ($mode == 'del_lang') {

        require $xcart_dir.'/include/safe_mode.php';

        db_query ("DELETE FROM $sql_tbl[languages] WHERE code='$language'");
        db_query ("DELETE FROM $sql_tbl[products_lng] WHERE code='$language'");

        $lngs = func_query_column("SELECT code FROM $sql_tbl[languages] GROUP BY code");
        if (!empty($lngs)) {
            foreach ($lngs as $v) {
                func_data_cache_get('languages', array($v), true);
            }
        }

        if (!empty($active_modules['Flyout_Menus'])) {
            func_fc_remove_cache(10, false, false, array($language));
        }

        $top_message = array(
            'content' => func_get_langvar_by_name('lbl_languages_has_been_deleted')
        );

        func_header_location('languages.php');

    } elseif ($mode == 'export') {
        $smarty->assign ('csv_delimiter', $delimiter);

        $lng_res = func_query_first_cell ("SELECT value FROM $sql_tbl[languages] WHERE name='language_$language' AND code='$current_language'");

        $data = func_query ("SELECT * FROM $sql_tbl[languages] WHERE code='$language' ORDER BY name");

        if ($data) {
            foreach ($data as $key => $value) {
                $data[$key]['value'] = "\"" . str_replace("\"", "\"\"", $value['value']) . "\"";
            }

            $smarty->assign ('data', $data);

            header ("Content-Type: text/csv");
            header ("Content-Disposition: attachment; filename=lng_".(!empty($lng_res) ? $lng_res : $language).'.csv');

            $_tmp_smarty_debug = $smarty->debugging;
            $smarty->debugging = false;

            func_display('main/lng_export.tpl',$smarty);

            $smarty->debugging = $_tmp_smarty_debug;
            exit;
        }

    } elseif ($mode == 'change') {

        require $xcart_dir.'/include/safe_mode.php';

        db_query("UPDATE $sql_tbl[language_codes] SET disabled = IF(disabled = 'Y', '', 'Y') WHERE code = '$language'");
        func_data_cache_clear('languages');
        func_data_cache_clear('charsets');
    }

    func_header_location("languages.php?language=" . $language);
}

if (!func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[languages] WHERE code = '$config[default_admin_language]'")) {
    $config['default_admin_language'] = func_query_first_cell("SELECT code FROM $sql_tbl[languages] ORDER BY code");
}

if ($mode == 'add_lang') {
    require $xcart_dir.'/include/safe_mode.php';

    if (!$new_language) {
        func_header_location('languages.php');
    }

    $exists_result = func_query_first ("SELECT * FROM $sql_tbl[languages] WHERE code='$new_language'");

    if (!$exists_result) {
        $result = func_query("SELECT l.*, IFNULL(IFNULL(IFNULL(lc.value, la.value), ld.value), l.value) as value FROM $sql_tbl[languages] as l LEFT JOIN $sql_tbl[languages] as lc ON lc.code = '$config[default_customer_language]' AND lc.name = l.name LEFT JOIN $sql_tbl[languages] as la ON la.code = '$config[default_admin_language]' AND la.name = l.name LEFT JOIN $sql_tbl[languages] as ld ON ld.code = 'en' AND ld.name = l.name GROUP BY l.name");
        if ($result) {
            foreach ($result as $value) {
                $value['code'] = $new_language;
                func_array2insert('languages', func_addslashes($value));
            }
        }

        $lngs = func_query_column("SELECT code FROM $sql_tbl[languages] GROUP BY code");
        if (!empty($lngs)) {
            foreach ($lngs as $v) {
                func_data_cache_get('languages', array($v), true);
                func_data_cache_clear('get_language_vars');
                func_data_cache_clear('get_default_fields');
            }
        }
    }

    if ($source == 'server' && !empty($localfile)) {
        // File is located on the server
        $localfile = stripslashes($localfile);
        if (func_allow_file($localfile, true) && is_file($localfile)) {
            $import_file = $localfile;
            $is_import = true;
        } else {
            $top_message['content'] = func_get_langvar_by_name('msg_err_file_wrong');
            $top_message['type'] = 'W';

            func_data_cache_get('charsets', array(), true);
            func_data_cache_clear('languages');

            $serverfile = $localfile;
            func_header_location('languages.php');
        }
    } elseif ($source == 'upload' && $import_file && $import_file != 'none') {
        $import_file = func_move_uploaded_file('import_file');
        $is_import = true;

    } else {
        $is_import = false;
    }

    if ($is_import) {

        $added = 0;
        func_display_service_header('lbl_language_importing_');

        if ($fp = func_fopen($import_file, 'r', true)) {

            $lngs = $all_languages;

            while ($columns = fgetcsv ($fp, 65536, $delimiter)) {

                $columns = func_addslashes($columns);
                if (sizeof($columns) >= 4) {
                    $res = func_query_first ("SELECT * FROM $sql_tbl[languages] WHERE name='$columns[0]' AND $sql_tbl[languages].code = '$new_language'");
                    if ($res) {
                        db_query ("UPDATE $sql_tbl[languages] SET value='".$columns[1]."', topic='".$columns[3]."' WHERE name='$columns[0]' AND code='$new_language'");
                    } else {
                        db_query ("INSERT INTO $sql_tbl[languages] (code, name, value, topic) VALUES ('$new_language','$columns[0]','".$columns[1]."','".$columns[3]."')");
                    }
                }

                $added++;
                echo '.';
                if ($added % 200 == 0) {
                    echo "<br />\n";
                }
                func_flush();

            }
            fclose($fp);
        }
    }

    func_data_cache_get('charsets', array(), true);
    func_data_cache_clear('languages');
    func_data_cache_clear('get_language_vars');
    func_data_cache_clear('get_default_fields');

    func_header_location("languages.php?language=$new_language&topic=$topic&page=$page");

}

if ($language) {

    $r = func_query_first("SELECT $sql_tbl[language_codes].*, IFNULL($sql_tbl[languages].value, $sql_tbl[language_codes].language) as language, IFNULL($sql_tbl[images_G].id, 0) as has_icon FROM $sql_tbl[language_codes] LEFT JOIN $sql_tbl[languages] ON $sql_tbl[languages].code = $sql_tbl[language_codes].code AND $sql_tbl[languages].name = 'language_$language' LEFT JOIN $sql_tbl[images_G] ON $sql_tbl[images_G].id = $sql_tbl[language_codes].lngid WHERE $sql_tbl[language_codes].code = '$language'");
    if (empty($r))
        func_header_location('languages.php');

    $smarty->assign('language_data', $r);

    $smarty->assign('default_charset', $r['charset']);

    $where = ' WHERE ' . ($topic ? "l0.topic = '$topic'" : "l0.topic <> ''");

    $query = "SELECT l0.*, IFNULL(IFNULL(lc.value, la.value), lu.value) as value FROM $sql_tbl[languages] as l0 LEFT JOIN $sql_tbl[languages] as lc ON lc.name = l0.name AND lc.code = '$language' LEFT JOIN $sql_tbl[languages] as la ON la.name = l0.name AND la.code = '$config[default_admin_language]' LEFT JOIN $sql_tbl[languages] as lu ON lu.name = l0.name AND lu.code = '$config[default_customer_language]' $where GROUP BY l0.name ".(empty($filter) ? "" : "HAVING (name LIKE '%$filter%' OR value LIKE '%$filter%')")." ORDER BY l0.topic, l0.name";

    $objects_per_page = 20;

    $result = db_query($query);
    $total_items = $total_labels_in_search = db_num_rows($result);

    if ($total_items > 0) {
        include $xcart_dir.'/include/navigation.php';

        $data = func_query($query." LIMIT $first_page, $objects_per_page");
        if (!empty($data)) {
            foreach($data as $k => $v) {
                if (is_null($v['value']))
                    $data[$k]['value'] = func_query_first_cell("SELECT value FROM $sql_tbl[languages] WHERE name = '".addslashes($v['name'])."'");
            }

            $smarty->assign('data', $data);
        }
        unset($data);

    } elseif (empty($top_message['content'])) {
        $no_results_warning = array(
            'type' => 'W',
            'content' => func_get_langvar_by_name("lbl_warning_no_search_results", false, false, true)
        );
        $smarty->assign('top_message', $no_results_warning);
    }

    $smarty->assign('total_labels_found', $total_labels_in_search);
    $smarty->assign ('navigation_script', "languages.php?language=$language&topic=$topic&filter=".urlencode(stripslashes($filter)));

    $anchors = array(
        'edit_lng' => 'lbl_edit_language',
        'edit_lng_ent' => 'lbl_edit_language_entries',
        'def_lng' => 'lbl_default_languages',
        'add_lng' => 'lbl_add_new_language'
    );

    $location[] = array(func_get_langvar_by_name('lbl_edit_languages'), 'languages.php');
    $location[] = array($r['language'], "");

} else {
    $anchors = array(
        'edit_lng' => 'lbl_edit_language',
        'def_lng' => 'lbl_default_languages',
        'add_lng' => 'lbl_add_new_language'
    );

    $location[] = array(func_get_langvar_by_name('lbl_edit_languages'), '');

}

foreach ($anchors as $anchor => $anchor_label) {
    $dialog_tools_data['left'][] = array(
        'link' => "#".$anchor,
        'title' => func_get_langvar_by_name($anchor_label)
    );
}

$smarty->assign('dialog_tools_data', $dialog_tools_data);

$exists = func_query_column("SELECT code FROM $sql_tbl[languages] GROUP BY code");

$new_languages = func_query ("SELECT $sql_tbl[language_codes].*, IFNULL(lng1l.value, IFNULL(lng2l.value, $sql_tbl[language_codes].language)) as language FROM $sql_tbl[language_codes] LEFT JOIN $sql_tbl[languages] as lng1l ON lng1l.name = CONCAT('language_', $sql_tbl[language_codes].code) AND lng1l.code = '$shop_language' LEFT JOIN $sql_tbl[languages] as lng2l ON lng2l.name = CONCAT('language_', $sql_tbl[language_codes].code) AND lng2l.code = '$config[default_admin_language]' WHERE lng1l.value != '' OR lng2l.value != '' GROUP BY $sql_tbl[language_codes].code ORDER BY language");

$smarty->assign ('filter', stripslashes($filter));
$smarty->assign ('editing_language', stripslashes($language));
$smarty->assign ('languages', $languages);
$smarty->assign ('new_languages', $new_languages);
$smarty->assign ('no_flags', !func_check_languages_flags());
$smarty->assign ('topics', $topics);

if (!func_check_languages_flags()) {
    $no_flags_list = array();
    foreach ($all_languages as $l) {
        if (!isset($l['tmbn_url']))
            $no_flags_list[] = '<li>' . $l['language']. '</li>';
    }

    $smarty->assign ('no_flags_list', implode("\n", $no_flags_list));
}

$smarty->assign ('upload_max_filesize', func_convert_to_megabyte(func_upload_max_filesize()));
$smarty->assign ('my_files_location', func_get_files_location());

if (!empty($serverfile)) {
    $smarty->assign ('localfile', $serverfile);
    $serverfile = false;

} else {
    $smarty->assign ('localfile', func_get_files_location().'/lng_file.csv');
}

$smarty->assign('main','languages');
$smarty->assign('topic', $_topic);

// Assign the current location line
$smarty->assign('location', $location);

if (
    file_exists($xcart_dir.'/modules/gold_display.php')
    && is_readable($xcart_dir.'/modules/gold_display.php')
) {
    include $xcart_dir.'/modules/gold_display.php';
}
func_display('admin/home.tpl', $smarty);
?>
