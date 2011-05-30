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
 * File operations library 
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: file_operations.php,v 1.109.2.1 2011/01/10 13:11:48 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('files');

function fo_local_log_add($operation, $op_status, $op_message = false)
{
    global $login;
    global $REMOTE_ADDR;

    if ($op_message !== false)
        $op_message = trim($op_message);

    $message = sprintf("Login: %s\nIP: %s\nOperation: %s\nOperation status: %s%s",
        $login,
        $REMOTE_ADDR,
        $operation,
        ($op_status ? 'success' : 'failure'),
        (!empty($op_message) ? "\n".$op_message : '')
    );

    x_log_flag('log_file_operations', 'FILES', $message);
}

/**
 * This function generates a list of all files with '.tpl' extension
 */
function list_all_templates ($dir, $parent_dir)
{
    $all_files = array();

    if (!$handle = opendir($dir))
        return $all_files;

    while (($file = readdir($handle)) !== false) {

        if (
            is_file($dir . XC_DS . $file)
            && substr($file, -4, 4) == '.tpl'
        ) {

            $all_files[$parent_dir . XC_DS . $file] = 'Q';

        } elseif (
            is_dir($dir . XC_DS . $file)
            && $file != '.'
            && $file != '..'
        ) {

            $all_files = func_array_merge($all_files, list_all_templates ($dir . XC_DS . $file, $parent_dir . XC_DS . $file));

        }

    }

    closedir($handle);

    return $all_files;
}

if (!empty($opener)) {
    $opener_str_end   = "&opener=" . $opener;
    $opener_str_begin = "?opener=" . $opener;
}

if ($dir)
    $dir_loc = urlencode($dir);

if ($REQUEST_METHOD == 'POST') {

    require $xcart_dir . '/include/safe_mode.php';

    $dir = stripslashes($dir);

    $toggle_area = (isset($toggle_edit_area) && $toggle_edit_area == 'Y')
        ? "&toggle_edit_area=Y"
        : '';

    // Process the POST request

    if (
        $mode == 'save_file'
        && defined('IS_ADMIN_USER')
    ) {

        // Save file (Edit templates section)

        $path = func_allowed_path($root_dir, $root_dir.$filename);

        $op_status = false;

        if (
            $path === false
            || empty($filename)
            || !func_is_allowed_file($root_dir . $filename)
        ) {
            // Path is not allowed or empty new dir name

            $top_message['content'] = func_get_langvar_by_name('msg_err_file_wrong');
            $top_message['type']    = 'E';

         } elseif ($fw = func_fopen($path, 'w', true)) {
            // Success

            $filebody = str_replace("\r", '', $filebody);

            fputs($fw, stripslashes($filebody));

            fclose($fw);

            func_chmod_file($path);

            $top_message['content'] = func_get_langvar_by_name('msg_file_saved');

            $op_status = true;

        } else {
            // File operation is failed

            $top_message['content'] = func_get_langvar_by_name('msg_err_file_operation');
            $top_message['type']    = 'E';
        }

        fo_local_log_add('save_file', $op_status, "Filename: $filename");

        func_header_location($action_script . "?dir=$dir_loc&file=$filename" . $toggle_area . $opener_str_end);

    } elseif (
        $mode == 'restore'
        && defined('IS_ADMIN_USER')
    ) {

        // This facility restores the corrupted template from the repository

        if (!empty($filename))
            $path = func_allowed_path($root_dir, $root_dir.$filename);

        $op_status = false;

        if (
            empty($filename)
            || $path === false
            || empty($filename)
            || !func_is_allowed_file($root_dir . $filename)
        ) {
            // Path is not allowed or empty new dir name

            $top_message['content'] = func_get_langvar_by_name('msg_err_file_wrong');
            $top_message['type']     = 'E';

        } elseif (!func_restore_skin_from_backup($root_dir . XC_DS . $filename)) {
            // File operation is failed

            $top_message['content'] = func_get_langvar_by_name('msg_err_file_operation');
            $top_message['type'] = 'E';

        } else {
            // Success

            $top_message['content'] = func_get_langvar_by_name('msg_template_restored');
            $op_status = true;
        }

        fo_local_log_add('restore', $op_status, "Filename: $filename");

        func_header_location($action_script . "?dir=$dir_loc&file=$filename" . $toggle_area . $opener_str_end);

    } elseif (
        $mode == 'compile_all'
        && defined('IS_ADMIN_USER')
    ) {

        // Compile all templates from $template_repository

        func_set_time_limit(86400);

        $files_to_restore = list_all_templates ($root_dir,'');

        if (!empty($files_to_restore)) {

            // Generate search and replace arrays for preg_replace

            $search_array     = array();
            $search1_array    = array();
            $replace_array    = array();
            $replace1_array   = array();
            $language_entries = func_query("SELECT name, value FROM $sql_tbl[languages] WHERE code='$language'");

            foreach ($language_entries as $language_entry) {

                $language_entry['value'] = str_replace(array("{{", "}}", '$'), array("~~", "~~", '\$'), $language_entry["value"]);

                $value_no_delim_inner    = str_replace(array("{", "}"), array('`$ldelim`','`$rdelim`'), $language_entry["value"]);

                $value_no_delim          = str_replace(array('`$ldelim`', '`$rdelim`'), array('{$ldelim}','{$rdelim}'), $value_no_delim_inner);

                $search_array[]  = "'{.lng\.$language_entry[name]}'S";
                $search1_array[] = "'`.lng\.$language_entry[name]`'S";
                $search2_array[] = "'.lng\.$language_entry[name](\W)'S";

                $replace_array[]  = $value_no_delim;
                $replace1_array[] = $value_no_delim_inner;
                $replace2_array[] = "\"" . str_replace('"', '\"', $value_no_delim_inner) . "\"\\1";

            }

            // Perform compilation

print <<<JSCODE
<script type="text/javascript">
//<![CDATA[
var loaded = false;
var finished = false;

function refresh()
{
    window.scroll(0, 100000);

    if (finished == false && loaded == false)
        setTimeout(refresh, 1000);
}

setTimeout(refresh, 1000);
//]]>
</script>
JSCODE;

            $op_status    = true;
            $op_msg_lines = array();

            foreach ($files_to_restore as $file_to_restore => $file_status) {

                echo func_get_langvar_by_name(
                    'lbl_compiling_n',
                    array(
                        'file' => $file_to_restore,
                    ),
                    false,
                    true
                    )
                . " - ";

                $op_line = func_get_langvar_by_name(
                    'lbl_compiling_n',
                    array(
                        'file' => $file_to_restore,
                    ),
                    false,
                    true
                )
                . " - ";

                if (
                    is_writable($root_dir . $file_to_restore)
                    && is_readable($root_dir . $file_to_restore)
                ) {

                    $file_strings = file($root_dir . $file_to_restore);

                    $fp = func_fopen($root_dir . $file_to_restore, 'w', true);

                    // Patching head.tpl to disable languages <select>

                    if ($file_to_restore == '/head.tpl') {

                        if (rtrim($file_strings[0]) != "{assign var=\"all_languages_cnt\" value=0}")
                            array_unshift($file_strings, "{assign var=\"all_languages_cnt\" value=0}\n");
                    }

                    $newfile_strings  = preg_replace($search_array, $replace_array, $file_strings);
                    $newfile_strings1 = preg_replace($search1_array, $replace1_array, $newfile_strings);
                    $newfile_strings2 = preg_replace($search2_array, $replace2_array, $newfile_strings1);

                    foreach ($newfile_strings2 as $newfile_string2) {

                        fputs($fp, $newfile_string2);

                    }

                    echo "<font color='green'>"
                        . func_get_langvar_by_name(
                            'lbl_ok',
                            false,
                            false,
                            true
                        )
                    . "</font>";

                    $op_line .= func_get_langvar_by_name(
                        'lbl_ok',
                        false,
                        false,
                        true
                    );

                    fclose($fp);

                    func_chmod_file($root_dir . $file_to_restore);

                } else {

                    echo "<b><font color='red'>"
                        . func_get_langvar_by_name(
                            'lbl_failed',
                            false,
                            false,
                            true
                        )
                        . "</font></b>";

                    $op_line .= func_get_langvar_by_name(
                        'lbl_failed',
                        false,
                        false,
                        true
                    );

                    $op_status = false;
                }

                echo "<br />\n";

                func_flush();
            }

print <<<JSCODE2
<script type="text/javascript">
//<![CDATA[
var finished = true;
//]]>
</script>
JSCODE2;

            $top_message['content'] = func_get_langvar_by_name('msg_templates_compiled');

            $op_message = "----\n" . implode("\n", $op_msg_lines);

            fo_local_log_add('compile_all', $op_status, $op_message);

            func_html_location('file_edit.php',30);

            exit;

        } else {
            // Templates repository is not found
            $top_message['content'] = func_get_langvar_by_name('msg_err_repository_not_found');
            $top_message['type']    = 'E';

            fo_local_log_add('compile_all', false, func_get_langvar_by_name("msg_err_repository_not_found",false,false,true));
        }

        func_header_location($action_script . $opener_str_begin);

    } elseif ($mode == "New directory") {

        // Create new directory

        if (!empty($new_directory)) {

            $path = func_allowed_path(
                $root_dir,
                $root_dir . $dir . XC_DS . $new_directory,
                true
            );

        }

        $op_status = false;

        if (
            $path === false
            || empty($new_directory)
        ) {
            // Path is not allowed or empty new dir name
            $top_message['content'] = func_get_langvar_by_name('msg_err_file_wrong');
            $top_message['type']    = 'E';

        } elseif (is_dir($path)) {
            // Directory already exists
            $top_message['content'] = func_get_langvar_by_name('msg_err_dir_exists');
            $top_message['type']    = 'E';

        } elseif (!func_mkdir($path)) {
            // Creation of the directory is failed
            $top_message['content'] = func_get_langvar_by_name('msg_err_file_operation');
            $top_message['type']    = 'E';

        } else {
            // Success
            $top_message['content'] = func_get_langvar_by_name('msg_directory_created');

            $op_status = true;
        }

        fo_local_log_add('New directory', $op_status, "Directory: " . $dir . XC_DS . $new_directory);

        func_header_location(
            $action_script
            . (
                !empty($dir_loc)
                ? "?dir=" . $dir_loc . $opener_str_end
                : $opener_str_begin
            )
        );

    } elseif (
        $mode == "New file"
        && defined('IS_ADMIN_USER')
    ) {

        // Create new file (for 'Edit templates' section)

        if (!empty($new_file)) {

            $path = func_allowed_path(
                $root_dir,
                $root_dir . $dir . XC_DS . $new_file
            );

        }

        $op_status = false;

        if (
            $path === false
            || empty($new_file)
            || !func_is_allowed_file($new_file)
        ) {
            // Path is not allowed or empty new dir name
            $top_message['content'] = func_get_langvar_by_name('msg_err_file_wrong');
            $top_message['type']    = 'E';

        } elseif (file_exists ($path)) {
            // File already exists
            $top_message['content'] = func_get_langvar_by_name('msg_err_file_exists');
            $top_message['type']    = 'E';

        } elseif ($fw = func_fopen($path, 'w', true)) {
            // Success

            fclose ($fw);

            func_chmod_file($path);

            $top_message['content'] = func_get_langvar_by_name('msg_file_created');

            $op_status = true;

        } else {
            // Creation of the file is failed
            $top_message['content'] = func_get_langvar_by_name('msg_err_file_operation');
            $top_message['type']    = 'E';
        }

        fo_local_log_add('New file', $op_status, "Directory: " . $dir . XC_DS . $new_file);

        func_header_location(
            $action_script
            . (
                !empty($dir_loc)
                    ? "?dir=" . $dir_loc . $opener_str_end
                    : $opener_str_begin
            )
        );

    } elseif ($mode == 'Delete') {

        // Delete selected file or directory

        if (!empty($filename)) {

            $path = func_allowed_path(
                $root_dir,
                $root_dir . $filename,
                true
            );

        }

        $op_status = false;

        if (
            $path === false
            || empty($filename)
        ) {
            // Path is not allowed or empty new dir name
            $top_message['content'] = func_get_langvar_by_name('msg_err_file_wrong');
            $top_message['type']    = 'E';

        } elseif (
            file_exists($path)
            && filetype($path) == 'file'
        ) {

            if (!@unlink ($path)) {
                // Deletion of the file is failed
                $top_message['content'] = func_get_langvar_by_name('msg_err_file_operation');
                $top_message['type']    = 'E';

            } else {

                $top_message['content'] = func_get_langvar_by_name('msg_file_deleted');

                $op_status = true;

            }

        } elseif (is_dir($path)){

            func_rm_dir($path);

            clearstatcache();

            if (is_dir($path)) {

                $top_message['content'] = func_get_langvar_by_name('msg_err_file_operation');
                $top_message['type']    = 'E';

            } else {

                $top_message['content'] = func_get_langvar_by_name('msg_dir_deleted');

                $op_status = true;

            }

        } else {
            // Deletion of the file is failed
            $top_message['content'] = func_get_langvar_by_name('msg_err_file_operation');
            $top_message['type']    = 'E';
        }

        fo_local_log_add('Delete', $op_status, "Directory/file: " . $filename);

        func_header_location(
            $action_script
            . (
                !empty($dir_loc)
                    ? "?dir=" . $dir_loc . $opener_str_end
                    : $opener_str_begin
            )
        );

    } elseif ($mode == 'Upload') {

        // Upload file


        // Check post_max_size exceeding

        func_check_uploaded_files_sizes('userfile', 523);

        $path = func_allowed_path(
            $root_dir,
            $root_dir . $dir . XC_DS . $userfile_name
        );

        $op_status = false;

        if (
            $path === false
            || !func_is_allowed_file($userfile_name)
        ) {
            // Path is not allowed or empty new dir name
            $top_message['content'] = func_get_langvar_by_name('msg_err_file_wrong');
            $top_message['type']    = 'E';

        } elseif (
            file_exists($path)
            && empty($rewrite_if_exists)
        ) {
            // File already exists
            $top_message['content'] = func_get_langvar_by_name('msg_err_file_exists');
            $top_message['type']    = 'E';

        } elseif (
            $userfile_name == 'none'
            || $userfile_name == ''
        ) {
            // No files to upload
            $top_message['content'] = func_get_langvar_by_name('msg_err_file_upload');
            $top_message['type']    = 'E';

        } elseif (!@move_uploaded_file ($userfile, $path)) {
            // File operation is failed
            $top_message['content'] = func_get_langvar_by_name('msg_err_file_operation');
            $top_message['type']    = 'E';

        } else {
            // Success
            $top_message['content'] = func_get_langvar_by_name('msg_file_uploaded');

            func_chmod_file($path);

            $op_status = true;

        }

        fo_local_log_add('Upload', $op_status, "Filename: " . $userfile_name . "\nTarget directory: " . $dir);

        func_header_location(
            $action_script
            . (
                !empty($dir_loc)
                    ? "?dir=" . $dir_loc . $opener_str_end
                    : $opener_str_begin
            )
        );

    } elseif ($mode == "Copy to") {

        // COPY FILE

        if (!empty($filename)) {

            $path = func_allowed_path(
                $root_dir,
                $root_dir . $dir . XC_DS . $copy_file
            );

            $path_from = func_allowed_path(
                $root_dir,
                $root_dir . $filename
            );

        }

        $op_status = false;

        if (
            empty($filename)
            || $path_from === false
            || $path === false
            || !func_is_allowed_file($copy_file)
            || file_exists ($path)
        ) {
            // Path is not allowed or empty new dir name
            $top_message['content'] = func_get_langvar_by_name('msg_err_file_wrong');
            $top_message['type']    = 'E';

        } elseif (!@copy ($path_from, $path)) {
            // File operation is failed
            $top_message['content'] = func_get_langvar_by_name('msg_err_file_operation');
            $top_message['type']    = 'E';

        } else {

            $top_message['content'] = func_get_langvar_by_name('msg_file_copied');

            $op_status = true;

        }

        fo_local_log_add('Copy to', $op_status, "Filename: " . $filename . "\nTarget filename: " . $dir . '/' . $copy_file);

        func_header_location(
            $action_script
            . (
                !empty($dir_loc)
                    ? "?dir=" . $dir_loc . $opener_str_end
                    : $opener_str_begin
            )
        );

    }

} // /if ($REQUEST_METHOD == 'POST')

/**
 * Process GET-request
 */

if (!empty($file)) {

    // Edit file mode
    $path = func_allowed_path($root_dir, $root_dir . $file);

    if (
        $path === false
        || empty($file)
    ) {
        // Path is not allowed or empty new dir name
        $top_message['content'] = func_get_langvar_by_name('msg_err_file_wrong');
        $top_message['type']    = 'E';

        fo_local_log_add('Open file', false, "Filename: " . $file);

        func_header_location(
            $action_script
            . (
                !empty($dir_loc)
                    ? "?dir=" . $dir_loc . $opener_str_end
                    : $opener_str_begin
            )
        );

    } elseif (!is_readable($path)) {
        // Permission denied
        $top_message['content'] = func_get_langvar_by_name('msg_err_file_read_permission_denied');
        $top_message['type']    = 'E';

        fo_local_log_add('Open file', false, "Filename: " . $file);

        func_header_location(
            $action_script
            . (
                !empty($dir_loc)
                    ? "?dir=" . $dir_loc . $opener_str_end
                    : $opener_str_begin
            )
        );

    } else {

        $op_status = true;

        if (@getimagesize($path)) {

            $smarty->assign('file_type', 'image');

        } else {

            switch (preg_replace('/^.+\.(\w+)$/s', '\1', $path)) {
                case 'tpl':
                    $smarty->assign('file_ext', 'tpl');
                    break;

                case 'html':
                    $smarty->assign('file_ext', 'html');
                    break;

                case 'css':
                    $smarty->assign('file_ext', 'css');
                    break;

                case 'js':
                    $smarty->assign('file_ext', 'js');
                    break;

                default:
                    $smarty->assign('file_ext',          'html');
                    $smarty->assign('file_ext_selector', true);

            }

            $smarty->assign('is_writable',   is_writable($path));
            $smarty->assign('use_edit_area', true);

            $smarty->assign('filebody', file($path));
        }
    }

    $smarty->assign('filename', $file);
    $smarty->assign('main',     'edit_file');

} else {

    // Browse directory tree mode

    $maindir = func_allowed_path($root_dir, $root_dir . $dir, true);

    if ($maindir === false)
        $maindir = $root_dir;

    if ($dh = @opendir($maindir)) {

        while (($file = readdir($dh)) !== false) {

            if ($file == '.' || preg_match("/^\.[^.]/S", $file))
                continue;

            $dir_entries[] = array(
                'file' => $file,
                'href' => stripslashes(
                    (
                        $file == '..'
                            ? preg_replace("/\/[^\/]*$/", '', stripslashes($dir))
                            : $dir . '/' . $file
                    )
                ),
                'filetype' => @filetype($maindir . XC_DS . $file)
            );

        }

        function myfilesortfunc($a,$b) {
            return strcasecmp($a['filetype'], $b['filetype']) * 1000 + strcasecmp($a['file'], $b['file']);
        }

        usort ($dir_entries, 'myfilesortfunc');

        closedir($dh);

    }

    $smarty->assign('root_dir',         $root_dir);
    $smarty->assign('dir_entries',      $dir_entries);
    $smarty->assign('dir_entries_half', ceil(sizeof($dir_entries)/2));
    $smarty->assign('main',             'edit_dir');
    $smarty->assign('is_writeable',     is_writable($root_dir));
}

$smarty->assign('preview_image',       $xcart_web_dir . '/' . $preview_image);
$smarty->assign('upload_max_filesize', func_convert_to_megabyte(func_upload_max_filesize()));

?>
