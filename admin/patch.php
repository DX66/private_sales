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
 * Patch/upgrade interface
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: patch.php,v 1.97.2.1 2011/01/10 13:11:46 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

define('USE_TRUSTED_SCRIPT_VARS', 1);
define('USE_TRUSTED_POST_VARIABLES', 1);
define('NO_ALT_SKIN', 'Y');

$trusted_post_variables = array('patch_query');

define('SKIP_CHECK_SESSGION_FG', 1);

require './auth.php';

require $xcart_dir . '/include/security.php';

func_set_time_limit(86400);

x_load(
    'files',
    'compat'
);

$init_memory = function_exists('memory_get_usage') ? memory_get_usage() : 0;

$new_memory_limit = $init_memory ? max(ceil(($init_memory+8000000) / 1048576), 16) : 16;

$memory_limit_is_set = func_set_memory_limit($new_memory_limit.'M');

x_session_register('patch_old_files_mode', array());

require $xcart_dir . DIR_ADMIN . '/patch_sql.php';

require $xcart_dir . '/include/patch.php';

$location[] = array(func_get_langvar_by_name('lbl_patch_upgrade_center'), '');

$upgrade_repository = $xcart_dir . '/upgrade';
$patch_tmp_folder   = $var_dirs['upgrade'];
$patch_logfile      = $patch_tmp_folder . '/patch.log';
$patch_reverse      = (@$reverse == 'Y');

$ready_to_patch    = true;

$customer_files = array(
    '404.php',
    'adaptive.php',
    'address_book.php',
    'auth.php',
    'bonuses.php',
    'cart.php',
    'change_password.php',
    'check_requirements.php',
    'choosing.php',
    'comparison.php',
    'comparison_list.php',
    'download.php',
    'error_message.php',
    'featured_products.php',
    'giftcert.php',
    'giftreg_manage.php',
    'giftregs.php',
    'help.php',
    'home.php',
    'https.php',
    'login.php',
    'manufacturers.php',
    'minicart.php',
    'news.php',
    'offers.php',
    'order.php',
    'orders.php',
    'pages.php',
    'pconf.php',
    'popup_address.php',
    'popup_ask.php',
    'popup_edit_label.php',
    'popup_estimate_shipping.php',
    'popup_fc_products.php',
    'popup_image.php',
    'popup_info.php',
    'popup_magnifier.php',
    'popup_poptions.php',
    'process_order.php',
    'product.php',
    'products.php',
    'recommends.php',
    'referer.php',
    'register.php',
    'returns.php',
    'search.php',
    'send_to_friend.php',
    'slabel.php',
    'survey.php',
);

$smarty->assign('xcart_http_host', $xcart_http_host);

if ($REQUEST_METHOD == 'POST') {

    require $xcart_dir . '/include/safe_mode.php';

    // Check post_max_size exceeding

    func_check_uploaded_files_sizes('patch_file', 621);

    //  Patch by file upload

    if (
        $patch_file != 'none'
        && $patch_file != ''
    ) {

        move_uploaded_file($patch_file, $file_temp_dir . XC_DS . $patch_file_name);

        $patch_filename = $patch_file_name;

    } elseif ($patch_url != '') {

        // Patch is downloaded from URL

        if ($patch_lines = @func_file($patch_url)) {

            // Write file to $file_temp_dir

            $parsed_url = @parse_url($patch_url);
            $patch_filename = basename($parsed_url['path']);

            $fw = fopen($file_temp_dir . XC_DS . $patch_filename, 'w');
            foreach ($patch_lines as $patch_line)
                fputs($fw, $patch_line);

            fclose($fw);
            func_chmod_file($file_temp_dir . XC_DS . $patch_filename);

        } else {
            func_header_location("error_message.php?cant_open_file");
        }

    } elseif ($patch_query) {

        // Save custom queries into file

        $patch_filename = 'query' . XC_TIME;

        $fw = fopen($file_temp_dir . XC_DS . $patch_filename, 'w');
        fputs($fw, stripslashes($patch_query));
        fclose($fw);
        func_chmod_file($file_temp_dir . XC_DS . $patch_filename);
    }

    // Perform upgrade

    if (
        $mode == 'upgrade'
        && $patch_filename
    ) {

        if (
            $confirmed != 'Y'
            && (
                !is_array($chk)
                || count($chk) != 5
            )
        ) {
            func_header_location('patch.php');
        }

        $target_version = $patch_filename;

        $patch_tmp_folder = $var_dirs['upgrade'] . XC_DS . $target_version;
        $patch_logfile = $patch_tmp_folder . XC_DS . 'patch.log';

        if (
            !is_dir($patch_tmp_folder)
            && !func_mkdir($patch_tmp_folder)
        ) {
            $top_message['type'] = 'E';
            $top_message['content'] = func_get_langvar_by_name("lbl_failed_to_create_work_dir", array("dir" => $patch_tmp_folder));

            func_header_location('patch.php');
        }

        // Read all .diff files from $upgrade_repository/$target_version


        // target version is stored in patch_filename variable

        $patch_files_lst = $upgrade_repository . XC_DS . $target_version . XC_DS . 'file.lst';
        $integrity_result = array();
        $files_to_patch = array();
        $all_files_to_patch = array();
        $patch_lines = array();

        // Extract patch_files array from temporaly storage

        func_restore_phase_result(true);
        $patch_files = isset($phase_result['patch_files']) ? $phase_result['patch_files'] : '';
        $phase_result = '';

        if (
            !isset($patch_files)
            || empty($patch_files)
            || $confirmed != 'Y'
        ) {

            if (
                !$memory_limit_is_set
                && !$skip_memory_limit
            ) {

                $top_message = array(
                    'type' => 'E',
                    'content' => func_get_langvar_by_name('lbl_patch_memory_limit_error', array('memory_limit' => $new_memory_limit))
                );

                func_header_location('patch.php');
            }

            // Prepare patch files list and do all necessary tests

            $patch_files = array();
            $patch_old_files_mode = array();

            if ($_patch_files = @file($patch_files_lst)) {

                $result = func_correct_files_lst($_patch_files, $upgrade_repository . XC_DS . $target_version);

                if ($result !== true) {
                    $top_message['type'] = 'E';
                    $top_message['content'] = implode('<br /><br />', $result);
                    func_header_location('patch.php');
                }

                $patch_files = func_test_patch($_patch_files);
            }
        }

        $patch_lines[] = "\n\n --- " . func_get_langvar_by_name('txt_skipped_see_diff_files', NULL, false, true) . " ---";

        $phase_result['patch_text']         = htmlspecialchars(implode('',$patch_lines));
        $phase_result['patch_filename']     = $target_version;
        $phase_result['patch_type']         = 'upgrade';
        $phase_result['patch_files']        = $patch_files;
        $phase_result['ready_to_patch']     = $ready_to_patch;
        $phase_result['could_not_patch']    = $could_not_patch;
        $phase_result['all_files_to_patch'] = 1;
        $phase_result['mode']               = $mode;
        $phase_result['confirmed']          = $confirmed;
        $phase_result['patch_exe']          = $patch_exe;

        if ($confirmed) {

            // Apply upgrade patches

            if (is_dir($patch_tmp_folder))
                require $xcart_dir . DIR_ADMIN . '/upgrade.php';

            // Log patch activity

            if (
                !@is_link($patch_logfile)
                && $LOG = fopen($patch_logfile, "a+")
            ) {

                fputs($LOG, strftime("%T %D ", XC_TIME) . str_repeat("=", 5) . " START " . str_repeat("=", 25) . "\n");

                fputs($LOG, "PATCH FILES\n");

                foreach ($patch_files as $patch_file_info) {
                    fputs($LOG, $patch_file_info['orig_file'] . " ... [" . $patch_file_info['status_lbl'] . "]\n");
                }

                fputs($LOG, "\nPATCH RESULTS\n");

                foreach ($patch_result as $patch_resul_str) {
                    fputs($LOG, $patch_resul_str . "\n");
                }

                fputs($LOG, "\nPATCH LOG\n");

                foreach ($patch_log as $patch_log_str) {
                    fputs($LOG, $patch_log_str . "\n");
                }

                fputs($LOG, str_repeat("=", 25) . " END " . str_repeat("=", 25) . "\n");

                fclose($LOG);

                func_chmod_file($patch_logfile);
            }
        }

        func_store_phase_result();

    } elseif (
        $patch_filename
        && $mode == 'normal'
    ) {

        if (
            !is_dir($patch_tmp_folder)
            && !func_mkdir($patch_tmp_folder)
        ) {
            $top_message['type'] = 'E';
            $top_message['content'] = func_get_langvar_by_name("lbl_failed_to_create_work_dir", array("dir" => $patch_tmp_folder));

            func_header_location('patch.php');
        }

        // Generate 'files to patch' list
        // A list of files needed to be patch but are not writeable


        // Extract patch_files array from temporaly storage

        func_restore_phase_result(true);

        $patch_files = isset($phase_result['patch_files']) ? $phase_result['patch_files'] : '';

        $phase_result = '';

        $patch_realfile = $file_temp_dir.XC_DS.$patch_filename;

        if (
            !is_array($patch_files)
            || empty($patch_files)
            || $confirmed != 'Y'
        ) {

            $patch_lines = func_file($patch_realfile, true);
            $phase_result['patch_text'] = htmlspecialchars(implode('', $patch_lines));

            if (!empty($patch_files))
                remove_tmp_files($patch_files);

            $patch_files = func_test_patch($patch_lines, false);
        }

        $phase_result['patch_filename'] = $patch_filename;
        $phase_result['patch_type'] = 'text';
        $phase_result['patch_files'] = $patch_files;
        $phase_result['ready_to_patch'] = $ready_to_patch;
        $phase_result['could_not_patch'] = $could_not_patch;
        $phase_result['all_files_to_patch'] = 1;
        $phase_result['mode'] = $mode;
        $phase_result['confirmed'] = $confirmed;
        $phase_result['reverse'] = $reverse;
        $phase_result['patch_exe'] = $patch_exe;

        if ($confirmed) {

            // Apply patch

            $patch_result = array();

            if (is_dir($patch_tmp_folder)) {

                $patch_result = array();
                $patch_log = array();
                $sql_errorcode = 1;
                $patch_errorcode = 1;

                func_auto_scroll(func_get_langvar_by_name('txt_applying_patch_wait', NULL, false, true) . "<hr />\n");

                require $xcart_dir . DIR_ADMIN . '/patch_files.php';

                $patch_completed = $patch_errorcode;
            }

            $phase_result['patched_files']      = $patched_files;
            $phase_result['excluded_files']     = $excluded_files;
            $phase_result['patch_log']          = $patch_log;
            $phase_result['patch_phase']        = 'upgrade_final';
            $phase_result['patch_result']       = $patch_result;
            $phase_result['patch_completed']    = $patch_completed;
        }

        func_store_phase_result();

    } elseif (
        $patch_filename
        && $mode == 'sql'
    ) {

        $confirmed = 'Y';

        $patch_lines = func_file($file_temp_dir . XC_DS . $patch_filename, true);

        $phase_result['patch_text'] = htmlspecialchars(implode('', $patch_lines));
        $phase_result['patch_filename'] = $patch_filename;
        $phase_result['patch_type'] = 'sql';
        $phase_result['ready_to_patch'] = 1;
        $phase_result['mode'] = $mode;
        $phase_result['all_files_to_patch'] = 1;
        $phase_result['confirmed'] = $confirmed;

        if ($confirmed) {

            // Apply SQL patch

            $patch_result = array();

            $sql_error = ExecuteSqlQuery(implode('', $patch_lines));

            // Generate Result text

            if (!empty($sql_error)) {

                $patch_result[] = '<font color="red">' . func_get_langvar_by_name("lbl_sql_patch_failed_at_query", NULL, false, true) . ":</font>";
                $patch_result[] = $sql_error;
                $patch_completed = false;

                @unlink($file_temp_dir . XC_DS . $patch_filename);

            } else {
                $patch_result[] = func_get_langvar_by_name('txt_db_successfully_patched', NULL, false, true);
                $patch_completed = true;
            }

            $phase_result['patch_phase'] = 'upgrade_final';
            $phase_result['patch_result'] = $patch_result;
            $phase_result['patch_completed'] = $patch_completed;
        }

        func_store_phase_result();
    }

} elseif (
    $REQUEST_METHOD == 'GET'
    && $mode == 'result'
) {

    func_restore_phase_result();

    if (is_array($phase_result)) {
        foreach ($phase_result as $key => $val)
            $smarty->assign($key, $val);
    }

} else {

    func_restore_phase_result(true);

    if (
        is_array($phase_result['patch_files'])
        && $phase_result['patch_type'] == 'text'
    ) {

        remove_tmp_files($phase_result['patch_files']);

        @unlink("$file_temp_dir/$phase_result[patch_filename]");

    }
}

/**
 * Get the list of target versions available in ./upgrade
 */

$corrupted_versions = array();
$target_versions    = array();

if ($dir = @opendir($upgrade_repository)) {

    while (($file = readdir($dir)) !== false) {

        if (
            !is_dir($upgrade_repository . XC_DS . $file)
            || $file == '.'
            || $file == '..'
        ) {
            continue;
        }

        $file = strtr($file, '_', ' ');

        $_versions = explode("-", $file, 2);

        if (count($_versions) == 2) {

            list($source_version, $target_version) = $_versions;

            if ($config['version'] == $source_version) {

                $patch_files_lst = $upgrade_repository . XC_DS . $file . XC_DS . 'file.lst';

                $is_correct_pack = is_readable($patch_files_lst) && file($patch_files_lst);

                if ($is_correct_pack)
                    $target_versions[] = $target_version;
                else
                    $corrupted_versions[] = $target_version;

            }
        }
    }

    closedir($dir);
}

if (!$memory_limit_is_set) {

    $smarty->assign('memory_limit_not_is_set', true);

    $smarty->assign('new_memory_limit', $new_memory_limit);

    $lbl_patch_memory_limit_error = func_get_langvar_by_name('lbl_patch_memory_limit_error', array(), false, true);

    if (empty($lbl_patch_memory_limit_error))
        $lbl_patch_memory_limit_error = "The upgrade script could not set a PHP option 'memory_limit' to {{memory_limit}}M. That is why the upgrading process may not be completed correctly due to memory shortage.";

    $smarty->assign('lbl_patch_memory_limit_error', $lbl_patch_memory_limit_error);

    $lbl_patch_continue_upgrading_anyway = func_get_langvar_by_name('lbl_patch_continue_upgrading_anyway', array(), false, true);

    if (empty($lbl_patch_continue_upgrading_anyway))
        $lbl_patch_continue_upgrading_anyway = "Tick here to continue upgrading anyway";

    $smarty->assign('lbl_patch_continue_upgrading_anyway', $lbl_patch_continue_upgrading_anyway);
}

$anchors = array(
    'upgrade'         => 'lbl_upgrade',
    'apply_patch'     => 'lbl_apply_patch',
    'apply_sql_patch' => 'lbl_apply_sql_patch'
);

foreach ($anchors as $anchor => $anchor_label) {

    $dialog_tools_data['left'][] = array(
        'link' => "#" . $anchor,
        'title' => func_get_langvar_by_name($anchor_label),
    );

}

$dialog_tools_data['right'][] = array(
    'link' => "https://secure.qtmsoft.com/customer.php?area=filearea&amp;target=upgrade_pack&amp;brand=xcart&amp;version=" . urlencode($config['version']) . "&amp;shop_url=" . urlencode($xcart_http_host),
    'title' => func_get_langvar_by_name('lbl_check_for_upgrade_patches'),
);

$smarty->assign('dialog_tools_data', $dialog_tools_data);

$smarty->assign('main', 'patch');
$smarty->assign('target_versions', $target_versions);
$smarty->assign('corrupted_versions', $corrupted_versions);

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
