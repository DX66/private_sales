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
 * Part of Patch/Upgrade center
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: patch.php,v 1.39.2.2 2011/04/26 08:23:48 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */
// Note: function func_patch_apply() currently able to handle only unified diffs

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load('files');

define('PATCH_UNIFIED', 1);
define('PATCH_CONTEXT', 2);

define('PATCH_MASK_UNIFIED', '!^(\s*)(\@\@ -(\d+)(?:,(\d+))? \+(\d+)(?:,(\d+))? \@\@)!S');

function func_patch_set_write_perm($file)
{
    global $patch_old_files_mode;

    $stat = stat($file);
    $mode = $stat[2];

    $orig_mode = '';
    for ($i = 0; $i < 3; $i++) {
        $orig_mode = ($mode & 7).$orig_mode;
        $mode = $mode >> 3;
    }

    $patch_old_files_mode[$file] = octdec($orig_mode);

    $new_mode = octdec((intval(substr($orig_mode, 0, 1)) | 2).substr($orig_mode, 1));

    return func_chmod_file($file, $new_mode) && is_writeable($file);
}

/**
 * Function to apply patch against file
 * Pass empty $outfile to check patch applicability
 */
function func_patch_apply($origfile, $patchfile, $rejfile, $backupfile, &$log, &$rejects, $check=false, $reverse=false)
{
    static $masks = array (
        PATCH_UNIFIED => PATCH_MASK_UNIFIED
    );
    $type = PATCH_UNIFIED;

    if (empty($origfile) || empty($patchfile) || !file_exists($patchfile))
        return false;

    $orig = array();
    if (file_exists($origfile))
        $orig = file($origfile);

    $empty_orig = empty($orig);

    $diff = file($patchfile);

    $outdata = $orig;
    $log[] = "Patching file $origfile ...";

    $idx = 0;
    $hunk_number = 1;
    $i_offset = 0;
    $rejected = array();
    $rejects = false;
    $changed = false;
    $is_new_file = false;

    while ($idx < count($diff)) {
        $line = $diff[$idx];
        if (preg_match($masks[PATCH_UNIFIED], $line, $m)) {
            if ($reverse) {
                list(,$space, $range, $o_start, $o_lines, $i_start, $i_lines) = $m;
            }
            else {
                list(,$space, $range, $i_start, $i_lines, $o_start, $o_lines) = $m;
            }

            $is_new_file = $is_new_file
                || ($m[3] === '0' && $m[4] === '0');

            if (empty($i_lines)) $i_lines = 1;
            if (empty($o_lines)) $o_lines = 1;
            $hunk = array();
            $idx ++;

            $can_apply = true;
            $additions = false;

            for (;$idx < count($diff) && !preg_match($masks[PATCH_UNIFIED], $diff[$idx]); $idx++) {
                $diff_line = $diff[$idx];

                if (!preg_match('!^(.)(.*)$!sS', $diff_line, $parsed_line)) continue;

                if ($parsed_line[1] == '\\') continue;

                if ($reverse) {
                    switch ($parsed_line[1]) {
                    case '+':
                        $parsed_line[1] = '-'; break;
                    case '-':
                        $parsed_line[1] = '+'; break;
                    }

                    $diff_line = $parsed_line[1].$parsed_line[2];
                }

                $hunk[] = $diff_line;

                // Cannot apply 'new file' hunk(s) against non empty files
                $additions = $additions
                    || $parsed_line[1] == '+';
            }

            $can_apply = !$is_new_file
                || $empty_orig
                || ($is_new_file && !$empty_orig && !$additions);

            if ($can_apply) {
                $data = func_pch_apply($hunk_number, $outdata, $hunk, $space, $i_start+$i_offset, $i_lines, $o_start, $o_lines);
                #We do not need to check all hunks in the testing phase
                if ($check && !$data['success'])
                    return false;
            }
            else {
                $data = array (
                    'pos' => false,
                    'success' => false
                );
            }

            if (is_array($data)) {
                if ($data['pos'] === false) {
                    $hunk_pos = $i_start + $i_offset;
                } else {
                    $hunk_pos = $i_start + $i_offset + $data['pos'];
                }
                if (!$data['success']) {
                    $log[] = sprintf("Hunk #%d failed at %d.", $hunk_number, $hunk_pos);
                    $rejected[] = array (
                        'start' => $hunk_pos,
                        'hunk' => $hunk
                    );
                    $i_offset += $o_lines - $i_lines;
                } else {
                    $log[] = sprintf("Hunk #%d succeeded at %d.", $hunk_number, $hunk_pos);
                    array_splice($outdata, $hunk_pos-1, $i_lines, $data['replace']);
                    $changed = true;
                    // correct offset for next hunks
                    $i_offset += $o_lines - $i_lines + $data['pos'];
                }
            }
            $hunk_number++;
        }
        else $idx++;
    }

    if (!empty($rejected)) {
        if (empty($rejfile)) {
            $log[] = sprintf("%d out of %d hunks ignored", count($rejected), $hunk_number-1);
        }
        else {
            $log[] = sprintf("%d out of %d hunks ignored--saving rejects to %s", count($rejected), $hunk_number-1, $rejfile);

            $rejects = true;
            if (!$check) {
                if (is_writeable(dirname($rejfile))) {
                    $r = func_pch_write_rejfile($rejfile, $rejected);
                    if (!$r) {
                        $log[] = "Write to $rejfile is failed!";
                    }
                } else {
                    $log[] = "No permissions to write $rejfile!";
                }
            }
        }
    }

    if (!$check) {
        if (!empty($backupfile) && $changed) {
            if (file_exists($backupfile))
                @unlink($backupfile);

            if (!file_exists($origfile))
                $r = touch($backupfile);
            else
                $r = copy($origfile, $backupfile);

            if (!$r) {
                $log[] = "Cannot backup file ``$origfile'' to ``$backupfile''!";
            }
        }
        $r = func_pch_write($origfile, $outdata);
        if (!$r) {
            $log[] = "Write to $origfile is failed!";
        }

        global $patch_old_files_mode;
        if (isset($patch_old_files_mode[$origfile])) {
            func_chmod_file($origfile, $patch_old_files_mode[$origfile]);
            unset($patch_old_files_mode[$origfile]);
        }

    }
    $log[] = 'done';

    return empty($rejected);
}

function func_pch_apply($num, &$outdata, &$hunk, $space, $i_start, $i_lines, $o_start, $o_lines)
{
    $offset = func_pch_locate($outdata, $hunk, $i_start, $i_lines);

    $result = array (
        'pos' => $offset,
        'success' => false
    );

    if ($offset === false) {
        return $result;
    }

    $work_copy = array_slice($outdata,$i_start-1+$offset,$i_lines);
    $pos = 0;
    foreach ($hunk as $line) {
        if (strlen($line)>0) {
            $cmd = $line[0];
            $line = substr($line,1);
        }
        else $cmd = '';

        switch ($cmd) {
            case '-':
                $_line = trim($line);
                $_work_copy = trim($work_copy[$pos]);
                if ($_line != $_work_copy) {
                    // FAILED
                    return $result;
                }
                array_splice($work_copy,$pos,1);
                break;
            case '+':
                func_pch_array_insert($work_copy,$line,$pos);
                $pos++;
                break;
            default :
                // skip ...
                $pos++;
        }
    }

    $result['success'] = true;
    $result['replace'] = $work_copy;

    return $result;
}

function func_pch_array_insert(&$array, $value, $pos)
{
    if (!is_array($array)) return FALSE;

    $last = array_splice($array, $pos);
    array_push($array, $value);
    $array = array_merge($array, $last);
    return $pos;
}

function func_pch_locate(&$data, &$hunk, $start, $lines)
{
    $data_len = count($data);

    // Hunk start position is outside of the orig file line range
    if ($start > $data_len) return false;

    $max_after = $data_len - $start - $lines;
    for ($offset = 0; ; $offset++) {
        $check_after = ($offset <= $max_after);
        $check_before = ($offset <= $start);

        if ($check_after && func_pch_match($data, $hunk, $start+$offset)) {
            return $offset;
        }
        else
        if ($check_before && func_pch_match($data, $hunk, $start-$offset)) {
            return -$offset;
        }
        else
        if (!$check_after && !$check_before) {
            return false;
        }
    }

    return false;
}

function func_pch_match(&$data, &$hunk, $pos)
{
    $len = count($hunk);
    $data_len = count($data);

    for ($i=0, $hunk_pos=0; $hunk_pos<$len && $pos+$i < $data_len; ) {
        if (!preg_match('!^(.)(.*)$!sS', $hunk[$hunk_pos], $matched)) {
            return false;
        }

        if ($matched[1] == '+') {
            $hunk_pos++;
            continue;
        }

        $_data = trim($data[$pos+$i-1]);
        $_match = trim($matched[2]);
        if ($_data != $_match) {
            return false;
        }

        $i++; $hunk_pos++;
    }

    return true;
}

function func_pch_write($filename, $data)
{
    func_mkdir(dirname($filename));
    $fp = fopen($filename, 'wb');
    if (!$fp) return false;

    fwrite($fp,implode('',$data));
    fclose($fp);
    func_chmod_file($filename);

    return true;
}

function func_pch_write_rejfile($filename, $rejected)
{
    $fp = fopen($filename, 'w');
    if (!$fp) return false;

    foreach ($rejected as $saved) {
        $removed = array();
        $added = array();
        foreach($saved['hunk'] as $line) {
            if (!preg_match('!^((.).*)$!S', $line, $matched)) {
                continue; // garbage ???
            }

            switch ($matched[2]) {
                case '-':
                    $removed[] = $matched[1];
                    break;
                case '+':
                    $added[] = $matched[1];
                    break;
                default:
                    $removed[] = $matched[1];
                    $added[] = $matched[1];
            }
        }

        $data = "***************\n";

        $first = $saved['start'];
        $removed_last = $first + (!empty($removed) ? count($removed) - 1 : 0);
        $added_last = $first + (!empty($added) ? count($added) - 1 : 0);

        if ($removed_last != $first)
            $data .= "*** $first,$removed_last ****\n";
        else
            $data .= "*** $first ****\n";

        if (!empty($removed)) $data .= implode("\n", $removed)."\n";

        if ($added_last != $first)
            $data .= "--- $first,$added_last ----\n";
        else
            $data .= "--- $first ----\n";

        if (!empty($added)) $data .= implode("\n", $added)."\n";

        fwrite($fp, $data);
    }

    fclose($fp);
    func_chmod_file($filename);
    return true;
}

function func_prepare_list ($patch_lines)
{
    $list = '';

    $diff_data = '';
    $orig_file = '';
    $index_found = false;

    if (empty($patch_lines) || !is_array($patch_lines))
        return false;

    foreach($patch_lines as $patch_line) {
        if(preg_match('/(^Index: (.+))|(^diff)|(^((---)|(\+\+\+)|(\*\*\*)) ([^\t:]+))/S',$patch_line, $m)) {
            if (!empty($m[2]) || !empty($m[3]) && !$index_found) {
                if (!empty($orig_file)) {
                    $diff_file = func_store_in_tmp(join('',$diff_data),false);
                    $list[] = $orig_file.",".$diff_file.",";
                    $orig_file = '';
                    $index_found = false;
                    $diff_data = '';
                }
            }
            // from Index field
            if (!empty($m[2])) {
                $index_found = true;
                if (empty($orig_file) || strlen($orig_file) > strlen($m[2]))
                    $orig_file = $m[2];
            }
            // from ---/***/+++ field
            elseif (!empty($m[9])) {
                if (empty($orig_file) || strlen($orig_file) > strlen($m[9]))
                    $orig_file = $m[9];
            }
        }
        $diff_data[] = $patch_line;
    }
    if (!empty($orig_file) && !empty($diff_data)) {
        $diff_file = func_store_in_tmp(join('',$diff_data),false);
        $list[] = $orig_file.",".$diff_file.",";
    }

    return $list;
}

function func_read_lst($file, $split=false, $with_sections=true)
{
    $result = array();
    $fp = @fopen($file, 'r');
    if (!$fp) return array();

    $section = false;
    while ($line = fgets($fp, 4096)) {
        $line = trim($line);
        if (empty($line)) continue;

        if (!$with_sections) {
            $result[$line] = true;
            continue;
        }

        if (substr($line, 0, 1) == '=') {
            $section = substr($line, 1);
            if (empty($section)) $section = '';

            if (!isset($result[$section]))
                $result[$section] = array();
        }
        elseif ($section !== false) {
            if ($split) {
                $tmp = explode(',',$line);
                $result[$section][$tmp[0]] = $tmp;
            }
            else {
                $result[$section][] = $line;
            }
        }
    }

    fclose($fp);

    return $result;
}

function func_correct_files_lst(&$_patch_files, $upgrade_prefix, $installed_modules=false)
{
    global $sql_tbl, $xcart_dir;
    global $smarty;

    $error = array();

    // PARTS:
    // 1. read addons.lst & check $sql_tbl[modules]
    // 2. read skin1/.skin_descr & templates.lst (don't forget about *_full.lst)
    // Update: point 2 is removed since 4.4.0 - skin structure logic changes

    // normalize main list
    $files_list = array();
    $override_list = array();
    foreach ($_patch_files as $line) {
        $line = trim($line);
        $triple = explode(',',$line);
        $files_list[$triple[0]] = $triple;
        if (empty($triple[1]))
            $override_list[$triple[0]] = false; // mark entry for removal
    }

    // PART1: correct addons

    $addons_list = func_read_lst($upgrade_prefix.'/addons.lst',true);
    if (empty($installed_modules))
        $installed_modules = func_query_column("SELECT module_name FROM $sql_tbl[modules]");
    $installed_addons = array_intersect(array_keys($addons_list), $installed_modules);

    $missing_addons = array();

    foreach ($addons_list as $addon_name=>$addon_files) {
        if (!in_array($addon_name, $installed_addons))
            continue;

        if (empty($addon_files)) {
            // ERROR, Addon installed, but not included in upgrade pack
            $missing_addons[] = str_replace('_', ' ', $addon_name);
            continue;
        }

        // add/replace diffs for this addon
        foreach ($addon_files as $file=>$triple) {
            $override_list[$file] = $triple;
        }
    }

    if (!empty($missing_addons)) {
        $error[] = "Your shop has installed following module(s): <b>".implode('</b>, <b>',$missing_addons)."</b>, but necessary patches are not included in this upgrade pack";
    }

    // PART3: finalize corrections

    // correct $files_list
    foreach ($override_list as $k=>$triple) {
        if (empty($triple))
            func_unset($files_list,$k);
        else
            $files_list[$k] = $triple;
    }
    ksort($files_list);

    // write changes to $_patch_files
    $_patch_files = array();
    foreach ($files_list as $triple) {
        $_patch_files[] = implode(',',$triple);
    }

    if (!empty($error)) return $error;

    return true; // success
}

function func_test_patch( $_patch_files, $is_upgrade=true )
{
    global $xcart_dir, $upgrade_repository, $target_version;
    global $ready_to_patch, $could_not_patch, $patch_cmd, $patch_rcmd;
    global $patch_lines;
    global $customer_files;
    global $patch_reverse;
    global $smarty;

    $could_not_patch    = 0;

    // Parse patch info

    echo "<script language='javascript'> loaded = false; function refresh() { window.scroll(0, 100000); if (loaded == false) setTimeout('refresh()', 1000); } setTimeout('refresh()', 1000); </script>";
    echo func_get_langvar_by_name('txt_testing_patch_applicability',false,false,true)."<hr />\n";
    flush();

    if (!$is_upgrade)
        $_patch_files = func_prepare_list($_patch_files);

    if (empty($_patch_files) || !is_array($_patch_files))
        return false;

    foreach($_patch_files as $patch_file_info) {
        $patch_file_info = trim($patch_file_info);
        if ($patch_file_info == '' || $patch_file_info[0] == "#") continue;

        $parsed_info = preg_split( "/[ ,\t]/S", $patch_file_info);

        list($orig_file, $diff_file, $md5_sum) = $parsed_info;
        $is_copy = !empty($parsed_info[3]); // new file from schemes/*

        echo $orig_file." ... "; flush();

        $real_file = $xcart_dir.preg_replace(
            array(
                '!^(/(?:'.implode('|',$customer_files).'))$!S',
                '!^/admin!S',
                '!^/provider!S',
                '!^/partner!S' ),
            array(
                DIR_CUSTOMER.'\1',
                DIR_ADMIN,
                DIR_PROVIDER,
                DIR_PARTNER ),
            '/'.$orig_file);

        if ($is_upgrade && $is_copy) {
            // new file. comes from schemes/*
            $real_diff = $xcart_dir.'/'.$diff_file;
        }
        elseif ($is_upgrade) {
            $real_diff = $upgrade_repository.'/'.$target_version.'/'.$diff_file;
        }
        else {
            $real_diff = $diff_file;
        }

        $patch_file = array(
            'orig_file' => $orig_file,
            'diff_file' => $diff_file,
            'real_file' => $real_file,
            'real_diff' => $real_diff,
            'md5_sum'   => $md5_sum,
            'is_copy'   => $is_copy,
            'status'    => 1,
            'extra'     => ''
        );

        if ($is_upgrade && !$is_copy) {

            // check checksums
            if ($md5_file = @file($real_diff)) {
                if (count($patch_lines) < 150) {
                    $patch_lines = func_array_merge($patch_lines, $md5_file);
                }

                if ($md5_sum != md5(implode('', $md5_file))) {
                    $patch_file['status'] = 2;
                }
            }
            else {
                $patch_file['status'] = 3;
                $patch_file['extra'] = array(
                    'filename' => basename($real_diff)
                );
            }
        }

        if (!file_exists($real_file) && ($is_copy || func_pch_is_create_new($real_diff, $patch_reverse))) {

            // Assume diff will create new file
            $dir = dirname($real_file);

            if ($patch_file['status'] == 1 && (!file_exists($dir) || !is_dir($dir))) {
                $patch_file['status'] = 4;
            }

            if ($patch_file['status'] == 1 && !is_writable($dir)) {
                $patch_file['status'] = 5;
            }
        }
        else {

            // Check write permissions

            if ($patch_file['status'] == 1 && !file_exists($real_file)) {
                $patch_file['status'] = 6;
            }

            if ($patch_file['status'] == 1 && !is_file($real_file)) {
                $patch_file['status'] = 7;
            }

            if ($patch_file['status'] == 1 && !is_writable($real_file) && !func_patch_set_write_perm($real_file)) {
                $patch_file['status'] = 8;
            }
        }

        if ($patch_file['status'] != 1) {

            $ready_to_patch = false;

        } else {

            // Check patch applicability
            $patch_result_ = array();
            $rejects_ = false;
            $patch_errorcode_ = !func_patch_apply($real_file, $real_diff, false, false, $patch_result_, $rejects_, true, $patch_reverse);
            if ($patch_errorcode_ != 0) {
                // Check for applied patches:
                $patch_result_ = array();
                $rejects_ = false;
                $patch_errorcode_ = !func_patch_apply($real_file, $real_diff, false, false, $patch_result_, $rejects_, true, !$patch_reverse);
                if ($patch_errorcode_ == 0) {
                    $patch_file['status'] = 9; // Already patched
                } else {
                    $patch_file['status'] = 10; // Could not patch
                    $could_not_patch ++;
                    $patch_file['testapply_failed'] = 1;
                }
            }
        }

        list($patch_file['status_lbl'], $patch_file['status_txt']) = func_patch_status($patch_file['status'], $patch_file['extra']);

        $patch_files[] = $patch_file;

        func_flush(
            '<font color="' . ( $patch_file['status'] == 1 ? 'green' : ( $patch_file['status'] == 9 ? 'blue' : 'red' )) . '">'
            . $patch_file['status_lbl']
            . "</font><br />\n"
        );
    }

    return $patch_files;
}

/**
 * Function to store data in temporaly file
 * Return value: filename on success, FALSE overwise.
 */
function func_store_in_tmp($data, $serialize = true)
{
    global $file_temp_dir;

    $file = tempnam($file_temp_dir,'xctmp');
    if (!$file) return false;

    $fp = @fopen($file, 'w');
    if (!$fp) {
        @unlink($file);
        return false;
    }

    if ($serialize) $data = serialize($data);

    if (@fwrite($fp, $data) != strlen($data)) {
        @fclose($fp);
        @unlink($file);
        return false;
    }

    @fclose($fp);
    func_chmod_file($file);
    return $file;
}

/**
 * This function is used to make window scrolled to the bottom edge
 */
function func_auto_scroll($title)
{
        echo "<script language='javascript'> loaded = false; function refresh() { window.scroll(0, 100000); if (loaded == false) setTimeout('refresh()', 1000); } setTimeout('refresh()', 1000); </script>".$title;
        func_flush();
}

function func_store_phase_result()
{
    global $patch_phase_results_file, $phase_result;

    x_session_register('patch_phase_results_file');
    $patch_phase_results_file = func_store_in_tmp($phase_result);

    if ($patch_phase_results_file !== false) {
        x_session_save();
        func_html_location("patch.php?mode=result",0);
    }
    else {

        // Error saving phase results in temporaly storage

        x_session_unregister('patch_phase_results_file');
        die("Upgrade/patch process cannot continue:<br />There is a problem saving temporaly data at your server. Please check permissions and/or amount of free space in your TEMP directory.<br /><br /><a href=\"patch.php\">Click here to return to X-Cart</a>");
    }
}

function func_restore_phase_result($remove_files = false)
{
    global $phase_result, $patch_phase_results_file, $patch_files;

    x_session_register('patch_phase_results_file');
    $phase_result = false;

    if ($patch_phase_results_file !== false) {
        ob_start();
        @readfile($patch_phase_results_file);
        $phase_result = unserialize(ob_get_contents());
        ob_end_clean();
        if ($remove_files)
            @unlink($patch_phase_results_file);
    }

    if ($remove_files) {
        x_session_unregister('patch_phase_results_file');
    }
}

function remove_tmp_files($patch_files)
{

    // Removing temporaly files

    if (isset($patch_files[0])) {
        foreach ($patch_files as $f)
            @unlink($f['real_diff']);
    }
}

function func_pch_is_create_new($patchfile, $reverse)
{
    if (!file_exists($patchfile))
        return false;

    $patch = file($patchfile);

    $started = false;
    $regexp = '!^'.($reverse?'-':'\+').'!S';
    foreach ($patch as $line) {
        if (!$started) {
            if (!strncmp($line, '@@', 2))
                $started = true;

            continue;
        }

        if (!preg_match($regexp, $line))
            return false;
    }

    return true;
}

/**
 * Return patch status description
 *
 * @param int   $status Status
 * @param mixed $extra  Substitution
 *
 * @return array
 * @see    ____func_see____
 */
function func_patch_status($status = 1, $extra = false)
{
    global $sql_tbl;

    $statuses = array(
        1   => 'lbl_ok',
        2   => 'lbl_checksum_error',
        3   => 'lbl_patch_file_not_found',
        4   => 'msg_dir_not_found',
        5   => 'msg_dir_not_writeable',
        6   => 'lbl_not_found',
        7   => 'lbl_not_a_file',
        8   => 'lbl_not_writable',
        9   => 'lbl_already_patched',
        10  => 'lbl_could_not_patch'
    );

    $descriptions = array(
        1   => 'txt_patch_status_ok',
        2   => 'txt_patch_status_checksum',
        3   => '',
        4   => '',
        5   => '',
        6   => 'txt_patch_status_notexist',
        7   => 'txt_patch_status_notfile',
        8   => 'txt_patch_status_nonwrite',
        9   => '',
        10  => 'txt_patch_status_cannot_patch'
    );

    if (!isset($statuses[$status])) {

        return array('', '');

    } else {

        return array(
            func_get_langvar_by_name($statuses[$status], $extra, false, true),
            func_get_langvar_by_name($descriptions[$status], $extra, false, true)
        );
    }
}

?>
