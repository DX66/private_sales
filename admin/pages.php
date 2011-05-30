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

// $Id: pages.php,v 1.83.2.1 2011/01/10 13:11:46 ferz Exp $

// This script allow to create static html pages within  X-Cart

define('USE_TRUSTED_POST_VARIABLES',1);
define('USE_TRUSTED_SCRIPT_VARS',1);
define('NO_ALT_SKIN', 'Y');

$trusted_post_variables = array('pagetitle', 'pagecontent');

define('IS_MULTILANGUAGE', 1);

require './auth.php';

require $xcart_dir . '/include/security.php';

x_load('files');

$location[] = array(func_get_langvar_by_name('lbl_static_pages'), '');

x_session_register('page_modified');

$pages_dir = $xcart_dir . $smarty_skin_dir . XC_DS . 'pages';

function func_pages_dir($level, $language = false)
{
    global $xcart_dir, $smarty, $current_language, $pages_dir;

    $_language = !empty($language) ? $language : $current_language;

    if ($level == 'R') {

        $pages_dir = $xcart_dir . XC_DS;

    } else {

        if (!is_dir($pages_dir)) {

            func_mkdir($pages_dir);

        }

        $pages_dir = $pages_dir . XC_DS . $_language . XC_DS;

    }

    return $pages_dir;
}

$pageid = intval(@$pageid);

if (!empty($pageid)) {

    $pageids = func_query_column("SELECT p1.pageid FROM $sql_tbl[pages] as p1 INNER JOIN $sql_tbl[pages] as p2 ON p1.filename = p2.filename AND p1.pageid <> '$pageid' AND p2.pageid = '$pageid'");

    $pageids = func_query_column("SELECT resource_id FROM $sql_tbl[clean_urls] WHERE resource_type = 'S' AND resource_id IN ('".implode("','", $pageids)."')");

    $clean_url_pageid = array_shift($pageids);

    if (count($pageids) > 0) {

        db_query("DELETE FROM $sql_tbl[clean_urls] WHERE resource_type = 'S' AND resource_id IN ('".implode("','", $pageids)."')");

    }

    if (empty($clean_url_pageid))
        $clean_url_pageid = $pageid;

}

if ($REQUEST_METHOD == 'POST') {
/**
 * Process the POST request
 */
    require $xcart_dir.'/include/safe_mode.php';

    if ($mode == 'delete') {

    // Delete selected pages

        if (is_array($to_delete)) {

            foreach($to_delete as $pageid => $v) {

                $_filename = func_query_first_cell("SELECT filename FROM $sql_tbl[pages] WHERE pageid='$pageid' AND level = '$sec'");

                // Delete all pages related to the _filename
                $pages = func_query("SELECT * FROM $sql_tbl[pages] WHERE filename = '".func_addslashes($_filename)."' AND level = '$sec'");

                if (
                    is_array($pages)
                    && !empty($pages)
                    && !empty($v['to_delete'])
                ) {
                    foreach($pages as $page_data) {
                        $_pageid = $page_data['pageid'];
                        @unlink(func_pages_dir($page_data['level'], $page_data['language']) . $page_data['filename']);

                        db_query("DELETE FROM $sql_tbl[pages] WHERE pageid='$_pageid'");
                        db_query("DELETE FROM $sql_tbl[clean_urls] WHERE resource_type = 'S' AND resource_id = '$_pageid'");
                        db_query("DELETE FROM $sql_tbl[clean_urls_history] WHERE resource_type = 'S' AND resource_id = '$_pageid'");
                    }
                }
            }

            $top_message['content'] = func_get_langvar_by_name('msg_adm_pages_del');
        }

        func_header_location('pages.php');
    }

    if ($mode == 'update') {

    // Update pages list

        if (is_array($posted_data)) {
            foreach($posted_data as $pageid=>$v) {
                func_array2update(
                    'pages',
                    array(
                        'orderby'      => intval($v['orderby']),
                        'active'       => @$v['active'],
                        'show_in_menu' => @$v['show_in_menu'],
                    ),
                    'pageid = \'' . $pageid . '\''
                );
            }
        }

        if ($parse_smarty_tags != 'Y')
            $parse_smarty_tags = 'N';

        db_query("UPDATE $sql_tbl[config] SET value='$parse_smarty_tags' WHERE name='parse_smarty_tags' AND category='General'");

        $top_message['content'] = func_get_langvar_by_name('msg_adm_pages_upd');
    }

    if ($mode == 'modified') {

        // Save created/modified page

        $fillerr = (empty($pagetitle) || empty($pagecontent) || !in_array($active, array('Y','N')));

        $page_modified = array(
            'pagetitle'         => $pagetitle,
            'pagecontent'       => $pagecontent,
            'meta_keywords'     => $meta_keywords,
            'meta_description'  => $meta_description,
            'title_tag'         => $title_tag,
            'clean_url'         => $clean_url
        );

        $pages_dir = func_pages_dir($level);

        $valid_filename = empty($_POST['filename'])
            ? true
            : func_allowed_path(
                $pages_dir,
                $pages_dir . $_POST['filename']
            ) && func_allow_file(
                $_POST['filename']
            ) && !file_exists(
                $pages_dir . $_POST['filename']
            );

        if (empty($pageid) && !$valid_filename) {
            $fillerr = true;
            $file_error = true;
        }

        if (!$fillerr) {
            if (!is_dir($pages_dir)) {
                func_mkdir($pages_dir);
            }

            $orderby = intval($orderby);

            if (empty($pageid)) {

                if ($valid_filename) {

                    $filename = $_POST['filename'];
                    @unlink($pages_dir.$filename);

                } else {

                    $index = 0;

                    do {

                        $index++;
                        $default_filename = sprintf("page_%03d.html",$index);

                    } while (file_exists($pages_dir.$default_filename));

                    $filename = $default_filename;
                }

            } else {

                $filename = func_query_first_cell("SELECT filename FROM $sql_tbl[pages] WHERE pageid='$pageid'");

                if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[pages] WHERE pageid='$pageid' AND language = '$shop_language'") == 0) {

                    $pageid = func_query_first_cell("SELECT pageid FROM $sql_tbl[pages] WHERE filename = '" . addslashes($filename) . "' AND language = '$shop_language'");

                }
            }

            if ($level == 'E') {

                $clean_url = trim(stripslashes($clean_url));

                $current_clean_url = NULL;

                if (
                    !empty($clean_url_pageid)
                    || !empty($filename)
                ) {
                    $current_clean_url = func_clean_url_get_raw_resource_url('S', $clean_url_pageid, $filename);
                }

                if (
                    $config['SEO']['clean_urls_enabled'] == 'N'
                    || (
                        !empty($pageid)
                        && !zerolen($current_clean_url)
                        && $current_clean_url == $clean_url
                    ) || (
                        empty($pageid)
                        && !zerolen($current_clean_url)
                    )
                ) {

                    $clean_url_check_result = true;

                } else {

                    list($clean_url_check_result, $check_url_error_code) = func_clean_url_validate($clean_url);

                }

                if ($clean_url_check_result == false) {

                    $top_message = array(
                        'content'               => func_get_langvar_by_name('err_' . strtolower($check_url_error_code)),
                        'type'                  => 'E',
                        'clean_url_fill_error'  => true,
                    );

                    func_header_location("pages.php?level=$level&pageid=$pageid");
                }
            }

            if (
                empty($pageid)
                && file_exists($pages_dir . $filename)
            ) {

                $top_message['content'] = func_get_langvar_by_name('msg_err_page_file_exists');
                $top_message['type'] = 'E';

                func_header_location('pages.php');

            } elseif ($fd = func_fopen($pages_dir . $filename, 'w', true)) {

                fwrite($fd, stripslashes($pagecontent));
                fclose($fd);
                func_chmod_file($pages_dir . $filename);

            } else {

                $top_message['content'] = func_get_langvar_by_name('msg_err_file_permission_denied');
                $top_message['type'] = 'E';
                func_header_location('pages.php');

            }

            if (empty($pageid)) {

                $query_data = array(
                    'filename'         => $filename,
                    'title'            => $pagetitle,
                    'level'            => $level,
                    'orderby'          => intval($orderby),
                    'active'           => $active,
                    'language'         => $current_language,
                    'show_in_menu'     => $show_in_menu,
                    'meta_keywords'    => $meta_keywords,
                    'meta_description' => $meta_description,
                    'title_tag'        => $title_tag,
                );

                $pageid = func_array2insert(
                    'pages',
                    $query_data
                );

                $top_message['content'] = func_get_langvar_by_name('msg_adm_pages_add');

            } else {

                $query_data = array(
                    'title'            => $pagetitle,
                    'orderby'          => intval($orderby),
                    'active'           => $active,
                    'show_in_menu'     => $show_in_menu,
                    'meta_keywords'    => $meta_keywords,
                    'meta_description' => $meta_description,
                    'title_tag'        => $title_tag,
                );

                func_array2update(
                    'pages',
                    $query_data,
                    'pageid = \'' . $pageid . '\''
                );

                $top_message['content'] = func_get_langvar_by_name('msg_adm_page_upd');

            }

            $page_modified = array();

            // Insert/Update Clean URL.
            if ($level == 'E') {

                if (empty($clean_url_pageid)) {

                    $clean_url_pageid = $pageid;

                } else {

                    $pageid = $clean_url_pageid;

                }

                if ($config['SEO']['clean_urls_enabled'] == 'N') {
                    // Autogenerate clean URL.
                    $clean_url = func_clean_url_autogenerate('S', $clean_url_pageid, array('title' => $pagetitle));
                    $clean_url_save_in_history = false;
                }

                // Insert/Update Clean URL.
                if (func_clean_url_resource_has_record('S', $clean_url_pageid, $filename)) {

                    func_clean_url_update($clean_url, 'S', $clean_url_pageid, $clean_url_save_in_history == 'Y');

                } else {

                    func_clean_url_add($clean_url, 'S', $clean_url_pageid);

                }

            }

        } else {

            $top_message['content'] = func_get_langvar_by_name($file_error ? 'msg_err_file_wrong' : 'err_filling_form');
            $top_message['type'] = 'E';

        }

        func_header_location("pages.php?level=$level&pageid=$pageid");
    }

    if ($mode == 'check') {

    // Find already existed static pages that is not encountered in the database

        $languages = func_query("SELECT DISTINCT(code) FROM $sql_tbl[languages]");

        foreach ($languages as $k => $v) {

            $dirs[] = $xcart_dir . $smarty_skin_dir . '/pages/' . $v['code'];

        }

        $dirs[] = $xcart_dir;

        foreach ($dirs as $dir) {

            if ($dp = @opendir($dir)) {

                while ($file = readdir($dp)) {

                    if (
                        is_file($dir . XC_DS . $file)
                        && (
                            substr($file, -5, 5) == '.html'
                            || substr($file, -4, 4) == '.htm'
                        )
                    ) {

                        if ($dir == $xcart_dir) {

                            $root_pages[] = $dir . XC_DS . $file;

                        } else {

                            $embedded_pages[] = $dir . XC_DS . $file;

                        }
                    }
                }

                closedir($dp);
            }

        }

        if (is_array($root_pages)) {

            $orderby = func_query_first_cell("SELECT MAX(orderby) FROM $sql_tbl[pages] WHERE level='R'");

            foreach ($root_pages as $k=>$file) {

                if (!preg_match("/^(.+)[\/\\\](.*)$/S", $file, $found))
                    continue;

                $file = addslashes($found[2]);
                $title = addslashes(basename($file, '.'.pathinfo($file, PATHINFO_EXTENSION)));

                if (!func_query_first("SELECT filename FROM $sql_tbl[pages] WHERE filename='$file' AND level='R'")) {

                    $orderby += 10;

                    db_query("INSERT INTO $sql_tbl[pages] (filename, title, level, orderby, active, language) VALUES ('$file', '$title', 'R', '$orderby', 'Y', '$current_language')");

                }

            }

        }

        if (is_array($embedded_pages)) {

            $orderby = func_query_first_cell("SELECT MAX(orderby) FROM $sql_tbl[pages] WHERE level='E'");

            foreach ($embedded_pages as $k=>$file) {

                if (!preg_match("/^(.+)[\/\\\](.*)[\/\\\](.*)$/S", $file, $found))
                    continue;

                $file  = addslashes($found[3]);
                $lang  = $found[2];
                $title = addslashes(basename($file, '.'.pathinfo($file, PATHINFO_EXTENSION)));

                if (!func_query_first("SELECT filename FROM $sql_tbl[pages] WHERE filename='$file' AND level='E' AND language='$lang'")) {
                    $orderby += 10;
                    db_query("INSERT INTO $sql_tbl[pages] (filename, title, level, orderby, active, language) VALUES ('$file', '$title', 'E', '$orderby', 'Y', '$lang')");
                }
            }
        }
    }

    if ($mode == 'clean_urls_history') {

        if (
            empty($clean_urls_history)
            || !is_array($clean_urls_history)
        ) {

            $top_message['content'] = func_get_langvar_by_name('err_clean_urls_history_empty');
            $top_message['type'] = 'E';

            func_header_location("pages.php?pageid=$pageid");
        }

        if (func_clean_url_history_delete(array_keys($clean_urls_history))) {

            $top_message['content'] = func_get_langvar_by_name('txt_clean_urls_history_deleted');
            $top_message['type'] = 'I';

        } else {

            $top_message['content'] = func_get_langvar_by_name('err_clean_urls_history_delete');
            $top_message['type'] = 'E';

        }

        func_header_location("pages.php?pageid=$pageid");

    }

    func_header_location('pages.php');

} // /if ($REQUEST_METHOD == 'POST')

if (isset($_GET['pageid'])) {
/**
 * Prepare data for editing
 */
    $page_query = "SELECT pageid, filename, title, level, orderby, active, language, show_in_menu, meta_keywords, meta_description, title_tag, $sql_tbl[clean_urls].clean_url, $sql_tbl[clean_urls].mtime FROM $sql_tbl[pages] LEFT JOIN $sql_tbl[clean_urls] ON $sql_tbl[clean_urls].resource_type = 'S' AND $sql_tbl[clean_urls].resource_id = '".@$clean_url_pageid."' ";

    $page_data = func_query_first($page_query . " WHERE pageid='$pageid'");

    if (
        !empty($page_data)
        && $page_data['language'] != $shop_language
    ) {

        $tmp = func_query_first($page_query . " WHERE filename = '" . addslashes($page_data['filename']) . "' AND language = '$shop_language'");

        if (!empty($tmp)) {

            $page_data = $tmp;

        }

    }

    if (!empty($page_data['pageid']))
        $smarty->assign('pageid', $page_data['pageid']);

    if ($page_data) {

        $pages_dir = func_pages_dir($page_data['level']);

        $filename = $pages_dir . $page_data['filename'];

        if ($fd = func_fopen($filename, 'r', true)) {

            $page_content = '';

            if (func_filesize($filename) > 0) {
                $page_content = fread(
                    $fd,
                    func_filesize($filename)
                );
            }

            fclose($fd);

        } else {

            $page_content = func_get_langvar_by_name('lbl_file_has_not_been_found', array(), false, true);

        }

        $page_data['clean_urls_history'] = func_query_hash("SELECT id, clean_url FROM $sql_tbl[clean_urls_history] WHERE resource_type = 'S' AND resource_id = '" . $clean_url_pageid . "' ORDER BY mtime DESC", "id", false, true);

        $level = $page_data['level'];

        $smarty->assign('page_path',    $filename);
        $smarty->assign('page_data',    $page_data);
        $smarty->assign('page_content', $page_content);

        $location[count($location)-1][1] = 'pages.php';

        $location[] = array(func_get_langvar_by_name('lbl_edit_page', array('title' => $page_data['title'])), "");

    } else {

        $pages_dir = func_pages_dir($_GET['level']);

        $smarty->assign('page_path', $pages_dir);

        $flag = true;
        $index = 0;

        while($flag) {
            $index++;
            $default_filename = sprintf("page_%03d.html",$index);
            if (!file_exists($pages_dir.$default_filename))
                $flag = false;
        }

        $level = ($_GET['level']=="E" || $_GET['level']=="R" ? $_GET['level'] : 'E');

        $default_orderby = func_query_first_cell("SELECT MAX(orderby) FROM $sql_tbl[pages] WHERE level='$level'");

        $location[count($location)-1][1] = 'pages.php';
        $location[] = array(func_get_langvar_by_name('lbl_create_page'), '');

        $smarty->assign('default_orderby',  $default_orderby + 10);
        $smarty->assign('default_filename', $default_filename);
        $smarty->assign('default_index',    $index);

        if (empty($page_modified)) {

            $smarty->assign('default_page_title', "Page$index");
            $smarty->assign('default_page_content', "Page$index content");

        } else {

            $smarty->assign('default_page_content', $page_modified['pagecontent']);
            $smarty->assign('default_page_title', $page_modified['pagetitle']);
            $smarty->assign('default_meta_keywords', $page_modified['meta_keywords']);
            $smarty->assign('default_meta_description', $page_modified['meta_description']);
            $smarty->assign('default_title_tag', $page_modified['title_tag']);

            if (
                $level == 'E'
                && $config['SEO']['clean_urls_enabled'] == 'Y'
            ) {

                $smarty->assign('default_clean_url', $page_modified['clean_url']);

            }
        }
    }

    $smarty->assign('level', $level);
    $smarty->assign('main',  'page_edit');

} else {
/**
 * Prepare data for pages list
 */
    $pages = func_query("SELECT * FROM $sql_tbl[pages] WHERE language='$current_language' ORDER BY orderby, title");

    $smarty->assign('pages', $pages);
    $smarty->assign('main',  'pages');

}

$smarty->assign('is_writable', @is_writable($xcart_dir . $smarty_skin_dir));

// Assign the current location line
$smarty->assign('location', $location);

if (
    file_exists($xcart_dir . '/modules/gold_display.php')
    && is_readable($xcart_dir . '/modules/gold_display.php')
) {
    include $xcart_dir . '/modules/gold_display.php';
}
func_display('admin/home.tpl', $smarty);

?>
