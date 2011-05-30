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
 * Upgrade interface
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: upgrade.php,v 1.43.2.2 2011/01/18 08:53:16 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

require $xcart_dir.'/include/safe_mode.php';

$patch_result    = array();
$patch_log       = array();
$patch_errorcode = 1;
$sql_errorcode   = 1;
$prepatch_errorcode = 1;
$postpatch_errorcode = 1;
$patch_completed = 0;

list($xcart_current_version, $xcart_target_version) =
    explode("-", strtr($target_version, '_'," "));

func_auto_scroll(func_get_langvar_by_name('txt_applying_patch_wait', NULL, false, true)."<hr />\n");

/**
 * Prepare patch stage
 */
if (
    file_exists($upgrade_repository . XC_DS . $patch_filename . XC_DS . 'patch_pre.php')
    && is_readable($upgrade_repository . XC_DS . $patch_filename . XC_DS . 'patch_pre.php')
) {
    $prepatch_errorcode = 0;

    echo func_get_langvar_by_name('lbl_applying_pre_patch',false,false,true);
    flush();

    include $upgrade_repository . XC_DS . $patch_filename . XC_DS . 'patch_pre.php';

    if ($prepatch_errorcode == 1) {
        $patch_result[] = func_get_langvar_by_name('lbl_pre_patch_was_applied_successfully');
        func_flush(func_get_langvar_by_name('lbl_ok',false,false,true)."<br />\n");
    } else {
        $patch_result[] = "<font color=\"red\">".func_get_langvar_by_name('lbl_pre_patch_was_not_applied',false,false,true)."</font>";
        func_flush("<font color=\"red\">".func_get_langvar_by_name('lbl_error',false,false,true)."</font><br />");
    }
}

/**
 * Begin upgrade only if prepatch was successful
 */
if ($prepatch_errorcode == 1) {

require $xcart_dir . DIR_ADMIN . '/patch_files.php';

if ($dir = @opendir($upgrade_repository . XC_DS . $target_version)) {

    // Apply .sql patches

    if ($patch_errorcode == 1) {

        // Check for DB version first

        $db_version = func_query_first_cell("SELECT value FROM $sql_tbl[config] WHERE name='version'");

        if (empty($db_version)) {
            $patch_result[] = "<font color=\"red\">".func_get_langvar_by_name('lbl_wrong_db_version', NULL, false, true)."</font>";
            $sql_errorcode  = 0;
        }
        elseif ($db_version == $xcart_target_version) {
            $patch_result[] = "<font color=\"blue\">".func_get_langvar_by_name('lbl_db_patched_already', NULL, false ,true)."</font>";
        }
        else {

            // Patch database

            $patch_lines = array();
            $sql_error   = array();
            $sql_patch_files = array();
            $_mods = func_query_column("SELECT module_name FROM $sql_tbl[modules]");

            // SQL-Patch names convention:

            // patch.sql - patch for X-Cart core
            // patch_*.sql - reserved for addons.
            // *.sql - other possbile patch
            //         (cannot contain prefix 'patch_')

            //                    \1 - patch for addon       \2 - wrong patch
            $re = '@^(?:patch(?:_('.implode('|',$_mods).'))|(patch_).*|.*).sql@';
            while (($file = readdir($dir)) !== false) {
                if (preg_match($re, $file, $refs) && empty($refs[2]))
                    $sql_patch_files[] = $file;
            }
            closedir($dir);

            sort($sql_patch_files);

            $sql_apply_error = false;
            $sql_empty_patch = true;
            foreach ($sql_patch_files as $file) {
                $patch_lines = file("$upgrade_repository/$target_version/$file");

                if (empty($patch_lines))
                    continue;

                $sql_empty_patch = false;
                $sql_error = ExecuteSqlQuery(implode('',$patch_lines));

                if (!empty($sql_error)) {
                    $patch_result[] = "<font color=red>SQL PATCH ``$file'' FAILED AT QUERY:</font>";
                    $patch_result[] = $sql_error;

                    $_sql_file = array(
                        'status' => 10,
                        'orig_file' => $file
                    );

                    $_sql_file['status_lbl'] = "SQL PATCH `$file' FAILED AT QUERY:";
                    if (is_string($sql_error)) {
                        $_sql_file['status_txt'] = strip_tags($sql_error, '<br>');
                    }
                    $phase_result['failed_files'][] = $_sql_file;

                    $sql_apply_error = true;
                }
                else {
                    $patch_result[] = "SQL PATCH: ``$file'' was successuly applied";
                }
            }

            if ($sql_apply_error) {
                $sql_errorcode = 0;
            }
            else {
                $sql_errorcode = 1;
                $patch_result[] = "<font color=\"green\">".func_get_langvar_by_name('txt_db_successfully_patched', NULL, false, true)."</font>";
            }

            if ($sql_empty_patch) {
                $patch_result[] = "<font color=\"blue\">".func_get_langvar_by_name('lbl_empty_sql_patch', NULL, false, true)."</font>";
            }
        }
    } else {
        $patch_result[] = "<font color=\"red\">".func_get_langvar_by_name('lbl_diff_patch_failed', NULL, false, true)."</font>";
        $sql_errorcode  = 0;
    }
}

/**
 * Run post-patch script
 */

if ($patch_errorcode == 1 && $sql_errorcode == 1) {
    if (
        file_exists($upgrade_repository . XC_DS . $patch_filename . 'patch_post.php')
        && is_readable($upgrade_repository . XC_DS . $patch_filename . 'patch_post.php')
    ) {
        $postpatch_errorcode = 0;

        echo func_get_langvar_by_name('lbl_applying_after_patch',false,false,true);
        flush();

        include $upgrade_repository . XC_DS . $patch_filename . 'patch_post.php';

        if ($postpatch_errorcode == 1) {
            $patch_result[] = func_get_langvar_by_name('lbl_after_patch_was_applied_successfully',false,false,true);
            echo func_get_langvar_by_name('lbl_ok',false,false,true)."<br />\n";
        } else {
            $patch_result[] = "<font color=\"red\">".func_get_langvar_by_name('lbl_after_patch_was_no_applied',false,false,true)."</font>";
            echo "<font color=\"red\">".func_get_langvar_by_name('lbl_error',false,false,true)."</font><br />";
        }
        flush();
    }
}

/**
 * Update version & upgrade history if files and sql DB are patched OK
 */
if ($patch_errorcode == 1 && $sql_errorcode == 1 && $postpatch_errorcode == 1) {
    $patch_result[] = "Updating DB version info.";
    db_query("UPDATE $sql_tbl[config] SET value='$config[upgrade_history]\n$xcart_current_version-$xcart_target_version' WHERE name='upgrade_history'");
    db_query("UPDATE $sql_tbl[config] SET value='".$xcart_target_version."' WHERE name='version'");
    $patch_completed = 1;
    x_session_unregister('patch_files');
} else {
    $patch_result[] = "<font color=\"red=\">".func_get_langvar_by_name('lbl_db_version_has_not_been_updated', NULL, false, true)."</font>";
}

}

/**
 * Storing phase results
 */

$phase_result['patched_files'] = $patched_files;
$phase_result['excluded_files'] = $excluded_files;
$phase_result['patch_log'] = $patch_log;
$phase_result['patch_phase'] = 'upgrade_final';
$phase_result['patch_result'] = $patch_result;
$phase_result['patch_completed']= $patch_completed;

?>
