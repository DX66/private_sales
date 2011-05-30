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
 * Data export library
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: export.php,v 1.74.2.3 2011/01/10 13:11:48 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('backoffice','files','export','import');

/**
 * Define data for the navigation within section
 */
include $xcart_dir.'/include/import_tools.php';

x_session_register('export_data');

$step_row = 0; // Number of steps processed in one pass
$dot_per_row = 100;

// The export log file name
$export_log_filename = 'x-errors_import-' . addslashes(preg_replace('/' . func_login_validation_regexp(true) . '/', '', $login)) . '-' . date('ymd') . '.php';

// File with log of export
$export_log = $var_dirs['log'].'/'.$export_log_filename;

// URL of the file with log of export
$export_log_url = "get_log.php?file=".$export_log_filename;

if (empty($active_modules['Simple_Mode'])) {
    $providers = func_query("SELECT id, login, title, firstname, lastname FROM $sql_tbl[customers] WHERE usertype='P' ORDER BY login, lastname, firstname");
    if (!empty($providers)) {
        $smarty->assign('providers', $providers);
    }
}

$md5_login = md5($login);

/**
 * Fill the array of available types of importable data
 * Key is the name of a section in a CSV-file which must be used
 * for identifying the type of data being imported
 */
$import_step = 'define';

$allowed_import_types = array(
    'address_book',
    'categories',
    'categories_lng',
    'config',
    'featured_products',
    'memberships',
    'order_items',
    'orders',
    'product_links',
    'products',
    'products_lng',
    'shipping_rates',
    'states',
    'tax_rates',
    'taxes',
    'users',
    'zones'
);

foreach ($allowed_import_types as $_type) {
    $fpath = $xcart_dir . '/include/import_' . $_type . '.php';
    if (
        file_exists($fpath)
        && is_readable($fpath)
    ) {
        include $fpath;
    }
}
unset($import_step);

// Add import specifications for the modules specific data
if (!empty($modules_import_specification)) {
    $import_specification = func_array_merge_ext(
        $import_specification,
        $modules_import_specification
    );
}

// Check sections data and call 'oninitexport' event
if (is_array($import_specification) && !empty($import_specification)) {
    foreach ($import_specification as $k => $v) {
        if (
            (
                strpos($v['permissions'], $login_type) === false
                && empty($active_modules['Simple_Mode'])
            )
            || empty($v['script'])
            || empty($v['columns'])
            || !@file_exists($xcart_dir . $v['script'])
            || empty($v['export_sql'])
            || (
                $user_account['flag'] == 'FS'
                && !$v['allow_fullfillment']
            )
        ) {
            unset($import_specification[$k]);

        } elseif (
            !empty($v['oninitexport'])
            && function_exists($v['oninitexport'])
        ) {
            $res = $v['oninitexport']($k, $import_specification[$k]);
            if (!$res)
                unset($import_specification[$k]);
        }
    }
}

// Define import options service array
$export_options = array();
if (is_array($import_specification) && !empty($import_specification)) {
    foreach ($import_specification as $k=>$v) {
        if (is_array($v['export_tpls']) && !empty($v['export_tpls'])) {
            $export_options = func_array_merge($export_options, $v['export_tpls']);
        }
    }
}
$export_options = array_unique($export_options);

/**
 * Get and sort available languages
 */
$tmp = func_data_cache_get('languages', array($shop_language));
$export_languages = array();
if (!empty($tmp) && is_array($tmp)) {
    foreach ($tmp as $k => $v) {
        if ($config['default_admin_language'] == $v['code']) {
            $export_languages[] = $v;
            unset($tmp[$k]);
            break;
        }
    }
}
if (empty($export_languages)) {
    $export_languages = $tmp;

} elseif (!empty($tmp)) {
    $export_languages = func_array_merge($export_languages, $tmp);
    $export_languages = array_values($export_languages);
}

unset($tmp);

// Change provider
if ($REQUEST_METHOD == 'POST' && !empty($data_provider) && empty($active_modules['Simple_Mode'])) {
    $export_data['provider'] = $data_provider;
}

// Clear range
if ($REQUEST_METHOD == 'GET' && !empty($section) && $action == 'clear_range') {
    func_export_range_erase($section);
    if ($section == 'orders') {
        func_export_range_erase('GIFT_CERTIFICATES');
        func_export_range_erase('ORDER_ITEMS');
    }
    $lbl = func_get_langvar_by_name('lbl_export_'.strtolower($section).'_clear', NULL, false, true);
    if (!empty($lbl)) {
        $top_message['content'] = $lbl;
        $top_message['type'] = 'I';
    }

    func_header_location("import.php?mode=export");

// Save export data
} elseif ($REQUEST_METHOD == 'POST' && !empty($check) && !empty($data)) {
    if ($data['delimiter'] == 'tab')
        $data['delimiter'] = "\t";

    if  (empty($active_modules['Simple_Mode'])) {
        $data['provider'] = $export_data['provider'];
    }

    $export_data = $data;
    $export_data['check'] = array_keys($check);
    $export_data['options'] = $options;
    $action = 'export';

// Delete export pack
} elseif ($REQUEST_METHOD == 'POST' && !empty($packs) && $action == 'delete_pack') {
    $is_delete = false;
    
    if (!func_export_dir_is_writable()) {
        $top_message['content'] = func_get_langvar_by_name("msg_err_export_dir_permission_denied",  array('path' => $export_dir));
        $top_message['type'] = "E";
        func_header_location("import.php?mode=export");
    }

    foreach ($packs as $key) {
        if (!preg_match("/^(\d{8})(\d{6})$/S", $key, $found))
            continue;

        $filename = 'export_'.$found[1].'_'.$found[2];
        $dp = @opendir($export_dir);
        if (!$dp)
            continue;

        while ($file = readdir($dp)) {
            if ($file == '.' || $file == '..')
                continue;

            if (preg_match("/^".$filename.'/S', $file)) {
                $is_delete = true;
                if (is_dir($export_dir.XC_DS.$file)) {
                    func_rm_dir($export_dir.XC_DS.$file);
                } else {
                    unlink($export_dir.XC_DS.$file);
                }
            }
        }
        closedir($dp);
    }

    if ($is_delete) {
        $top_message['content'] = func_get_langvar_by_name("txt_export_pack_has_been_successfully_removed");
        $top_message['type'] = "I";
    }
    func_header_location("import.php?mode=export&status=success#packs");
}

// Export data
if ($action == 'export' || $action == 'continue') {

    if ($action == 'export' && !func_export_dir_is_writable()) {
        $top_message['content'] = func_get_langvar_by_name("msg_err_export_dir_permission_denied",  array('path' => $export_dir));
        $top_message['type'] = "E";
        func_header_location("import.php?mode=export");
    }

    // Open the log file for writing
    if (!($logf = @fopen($export_log, ($action == 'export' ? "w+" : "a+")))) {
        $top_message['content'] = func_get_langvar_by_name('msg_err_import_log_writing');
        $top_message['type'] = 'E';
        func_header_location("import.php?mode=export");
    }
    func_chmod_file($export_log);

    func_display_service_header();

    // First pass
    if (empty($export_data['prefix'])) {

        // Start log file writing...
        $current_date = date("d-M-Y H:i:s", mktime() + $config['Appearance']['timezone_offset']);
        $message =<<<OUT
Date: $current_date
Launched by: $login

OUT;
        $message = constant('X_LOG_SIGNATURE').$message;
        func_export_add_to_log($message);

        // Check category separator
        if ($user_account['flag'] != 'FS') {
            func_check_category_sep($export_data['options']['category_sep']);
        }

        if (
            $current_area == 'P'
            && empty($active_modules['Simple_Mode'])
        ) {
            $export_data['provider'] = $logged_userid;
        }

        $export_data['prefix'] = $export_dir."/export_".date("Ymd_His");
        $export_data['data_dir'] = $export_data['prefix'];
        $export_data['pos'] = 0;
        $export_data['total_line'] = 0;
        $export_data['pass_line'] = 0;
        $export_data['line'] = 0;
        $export_data['part'] = 0;
        $export_data['last_section'] = "";
        $export_data['last_code'] = false;
        $export_data['last_limit'] = 0;
        $export_data['pass'] = 1;
        $export_fp = false;

        // Clean and check image directory
        if ($export_data['options']['export_images'] == 'Y') {

            // Create directory if it does not exist
            if (!is_dir($export_data['data_dir'])) {
                func_mkdir($export_data['data_dir']);
            }

            // Clean directory
            if (is_dir($export_data['data_dir'])) {
                func_rm_dir($export_data['data_dir'], true);
            } else {
                $top_message['content'] = func_get_langvar_by_name("msg_images_directory_dnot_exist");
                $top_message['type'] = "E";
                func_header_location("import.php?mode=export");
            }

            // Check write permissions
            if (!is_writable($export_data['data_dir'])) {
                $top_message['content'] = func_get_langvar_by_name("msg_err_image_dir_permission_denied");
                $top_message['type'] = "E";
                func_header_location("import.php?mode=export");
            }
        }

        // Display export header
        $message = func_get_langvar_by_name('lbl_exporting_data_', NULL, false, true);
        if (!empty($message)) {
            echo "<b>".$message."</b><br />\n";
            func_flush();
        }

        // Flush all delayed queries to data tables
        func_run_delayed_query();

    // Next (non-first) pass
    } elseif ($export_data['pass'] > 1) {

        // Display export header
        $message = func_get_langvar_by_name('lbl_exporting_data_pass_', array('pass' => $export_data['pass']), false, true);
        if (!empty($message)) {
            echo "<b>".$message."</b><br />\n";
            func_flush();
        }
    }

    $message = <<<OUT
<script type="text/javascript">
//<![CDATA[
    loaded = false;

    function refresh() {
        window.scroll(0, 100000);
        if (loaded == false)
            setTimeout('refresh()', 1000);
    }

    setTimeout('refresh()', 1000);
//]]>
</script>
OUT;

    func_flush($message);

    $end_message = <<<OUT
<script type="text/javascript">
//<![CDATA[
    loaded = true;
//]]>
</script>
OUT;

    $export_data['pass_line'] = 0;
    $last_section = $export_data['last_section'];
    $last_code = $export_data['last_code'];
    $is_continue = false;
    $provider_sql = !empty($export_data['provider'])
        ? abs(intval($export_data['provider']))
        : '';

    // List sections
    foreach ($export_data['check'] as $section) {

        if (!isset($import_specification[$section]))
            continue;

        $is_continue = false;
        if ($action == 'continue' && !empty($last_section)) {
            if ($section != $last_section)
                continue;

            $last_section = false;
            $is_continue = true;
        }

        $line = 0;

        if ($import_specification[$section]['is_language']) {

            // Export multilanguage section

            $is_export = false;
            foreach ($export_languages as $c) {

                $current_code = $c['code'];
                $line = 0;

                $is_continue = false;
                if ($action == 'continue' && !empty($last_code)) {
                    if ($last_code != $current_code)
                        continue;

                    $last_code = false;
                    $is_continue = true;
                }

                if ($action == 'export' || ($export_data['last_code'] != $current_code))
                    $export_data['last_limit'] = 0;

                // Get MySQL-resource
                $data = func_export_read_data($section);
                if (!$data)
                    continue;
                if (db_num_rows($data) == 0)
                    continue;

                if (!$is_continue) {
                    $export_data['header'] = array();
                    if (!func_export_write_header())
                        break;
                }

                // Display section header
                $message = func_get_langvar_by_name('lbl_'.strtolower($section).'_exporting_', NULL, false, true);
                if (!empty($message)) {
                    func_export_add_to_log($message."\n(".$c['language'].")");
                    echo "<b>".$message."</b><br />\n(".$c['language'].")<br />\n";
                    func_flush();
                }

                // Call section script
                $import_step = 'export';
                include $xcart_dir.$import_specification[$section]['script'];
                $is_export = true;
                if ($data)
                    @db_free_result($data);

                if ($export_fp)
                    fwrite($export_fp, "\n");

                // Display section footer
                $message = func_get_langvar_by_name('lbl_rows', NULL, false, true).": ".$line;
                func_export_add_to_log("\n".$message."\n");
                echo "<br />\n".$message."<br />\n<br />\n";
                func_flush();

            }
            $current_code = false;
            if (!$is_export)
                continue;

        } else {

            // Export regular section

            if ($action == 'export' || ($export_data['last_section'] != $section))
                $export_data['last_limit'] = 0;

            // Get MySQL-resource
            $data = func_export_read_data($section);
            if (!$data)
                continue;
            if (db_num_rows($data) == 0)
                continue;

            if (!$is_continue) {
                $export_data['header'] = array();
                if (!func_export_write_header())
                    break;
            }

            // Display section header
            $message = func_get_langvar_by_name('lbl_'.strtolower($section).'_exporting_', NULL, false, true);
            if (!empty($message)) {
                func_export_add_to_log($message);
                func_flush("<b>".$message."</b><br />\n");
            }

            // Call section script
            $import_step = 'export';
            include $xcart_dir.$import_specification[$section]['script'];
            if ($data)
                @db_free_result($data);

            if ($export_fp)
                fwrite($export_fp, "\n");

            // Display section footer
            $message = func_get_langvar_by_name('lbl_rows', NULL, false, true).": ".$line;
            func_export_add_to_log("\n".$message."\n");
            func_flush("<br />\n".$message."<br />\n<br />\n");
        }

    }

    $export_data = array();
    $top_message['content'] = func_get_langvar_by_name("lbl_export_success");
    $top_message['type'] = 'I';
    func_html_location("import.php?mode=export&status=success", 3);

}

// Get export sepifications
if (!empty($import_specification)) {
    $export_spec = func_export_define($import_specification);
    if (!empty($export_spec)) {
        $smarty->assign('export_spec', $export_spec);
        $smarty->assign('export_spec_ids', array_keys($import_specification));
    }
}

// Get export packs list
if (($dp = @opendir($export_dir))) {
    $export_packs = array();
    while ($file = readdir($dp)) {

        if (is_dir($export_dir.XC_DS.$file))
            continue;

        if (!empty($active_modules['Simple_Mode']) || $user_account['usertype'] == 'A')
            $pattern = "/^export_(\d{4})(\d{2})(\d{2})_(\d{2})(\d{2})(\d{2})/S";
        else
            $pattern = "/^export_(\d{4})(\d{2})(\d{2})_(\d{2})(\d{2})(\d{2})_([A-Z]{2}_|)".$md5_login.'/S';

        if (!preg_match($pattern, $file, $found))
            continue;

        if (isset($found[7])) unset($found[7]);

        $fn = array_shift($found);
        $found = array_values($found);
        $key = implode('', $found);
        if (!isset($export_packs[$key])) {

            // Check export temporary directory
            $dir_exists = false;
            if (is_dir($export_dir.XC_DS.$fn) && ($imgdir = @opendir($export_dir.XC_DS.$fn))) {
                while ($tmp = readdir($imgdir)) {
                    if ($tmp != '.' && $tmp != '..') {
                        $dir_exists = true;
                        break;
                    }
                }
                closedir($imgdir);
            }

            $export_packs[$key] = array(
                'date'    => mktime($found[3], $found[4], $found[5], $found[1], $found[2], $found[0]),
                'files'    => array(),
                'count'    => $dir_exists ? 1 : 0,
                'dir_exists' => $dir_exists ? $export_dir.XC_DS.$fn : false
            );
        }

        // Get export file section(s) and language code
        if(($fe = @fopen($export_dir.XC_DS.$file, 'r'))) {
            $file = str_replace('.php', '', $file);
            $file_name = preg_replace("/^export_(\d{8})(_\d{6})((_[a-zA-Z]{2}|)_[a-z0-9]+).csv/S","export_$1$2$4.csv",$file);

            $export_packs[$key]['files'][$file] = array(
                'file_name' => $file_name,
                'sections' => array(),
                'code' => false,
                'code_name' => false
            );
            if (preg_match("/_(\w{2})\./S", $file, $found)) {
                $export_packs[$key]['files'][$file]['code'] = $found[1];
                $export_packs[$key]['files'][$file]['code_name'] = func_get_langvar_by_name("language_".$found[1], NULL, false, true);
            }
            while ($s = fgets($fe, 8192)) {
                if (preg_match("/^\[([\w_ ]+)\]$/S", trim($s), $found)) {
                    $tmp = strtoupper(trim($found[1]));
                    if (isset($import_specification[$tmp]))
                        $export_packs[$key]['files'][$file]['sections'][] = $tmp;
                }
            }

            // Check permissions of current user on this export pack
            if (empty($export_packs[$key]['files'][$file]['sections']) || array_diff($export_packs[$key]['files'][$file]['sections'], array_keys($import_specification))) {
                unset($export_packs[$key]['files'][$file]);
                continue;
            }

            $export_packs[$key]['files'][$file]['sections_count'] = count($export_packs[$key]['files'][$file]['sections']);
            $export_packs[$key]['count'] += ($export_packs[$key]['files'][$file]['sections_count'] > 0) ? $export_packs[$key]['files'][$file]['sections_count'] : 1;
            fclose($fe);
        }

    }
    closedir($dp);

    foreach ($export_packs as $key => $ep) {
        if (!empty($ep['files']))
            continue;

        unset($export_packs[$key]);
    }

    if (!empty($export_packs)) {
        krsort($export_packs, SORT_NUMERIC);
        $smarty->assign('export_packs', $export_packs);
    }
}

// Get last log content
$filename = $var_dirs['tmp'].'/'.$export_log_filename;
if (($fe = @fopen($filename, 'r')) !== false) {
    fseek($fe, strlen(X_LOG_SIGNATURE), SEEK_SET);
    $smarty->assign('export_log_content', fread($fe, func_filesize($filename)-strlen(X_LOG_SIGNATURE)));
    fclose($fe);
    $smarty->assign('export_log_url', $export_log_url);
}

$export_data['category_sep'] = func_import_get_category_sep();
$smarty->assign('export_dir', $export_dir);
$smarty->assign('export_options', $export_options);
$smarty->assign('export_data', $export_data);
$smarty->assign('export_images_dir', $xcart_dir.XC_DS.'images');

$smarty->assign('mode', $mode);

?>
