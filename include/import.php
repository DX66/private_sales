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
 * Data import library
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import.php,v 1.117.2.6 2011/03/14 09:09:50 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load(
    'backoffice',
    'files',
    'image',
    'import'
);

set_time_limit(86400);

func_set_memory_limit('32M');

/**
 * Store some information about importing in the session variables
 */
x_session_register('import_data_provider');
x_session_register('import_file'); // CSV-file
x_session_register('import_pass', array()); // Import process information
x_session_register('import_data', array()); // Import start data
x_session_register('no_import_sections', array()); // Import process information
x_session_register('final_notes');

/**
 * Select provider
 */
if (
    $REQUEST_METHOD == 'POST'
    && !empty($data_provider)
    && $data_provider != $import_data_provider
) {

    if (func_query_first_cell("SELECT id FROM $sql_tbl[customers] WHERE id = '" . intval($data_provider) . "' AND usertype = 'P'")) {

        $import_data_provider = $data_provider;

    } else {

        $top_message = array(
            'content'     => func_get_langvar_by_name('msg_adm_no_provider_found'),
            'type'         => 'E'
        );

        func_header_location('import.php');

    }

    if (AREA_TYPE == 'A') {

        func_header_location('import.php');

    }

}

if ($REQUEST_METHOD == 'POST') {

    require $xcart_dir . '/include/safe_mode.php';

}

/**
 * Global import definitions
 */
$max_line_size         = 65536 * 3;    // Max CSV file line length
$max_errors         = 2;    // Max number of errors before break importing
$step_row             = 500;    // Number of steps processed in one pass
$process_row_dot     = 1;

$_ok = func_get_langvar_by_name('lbl_ok', false, false, true);

// The import log file name
$import_log_filename = 'x-errors_import-'
    . addslashes(preg_replace('/' . func_login_validation_regexp(true) . '/', '', $login))
    . '-'
    . date('ymd')
    . '.php';

// File with log of import
$import_log = $var_dirs['log']
    . '/'
    . $import_log_filename;

// URL of the file with log of import
$import_log_url = "get_log.php?file="
    . $import_log_filename;

// Possible values for $action variable...
$_possible_actions = array(
    'do',
    'check',
);

if ($need_select_provider) {
    $providers = func_query("SELECT id, login, title, firstname, lastname FROM $sql_tbl[customers] WHERE usertype='P' ORDER BY login, lastname, firstname");

    if (!empty($providers)) {

        $smarty->assign('providers', $providers);

    }

    $smarty->assign('data_provider', $import_data_provider);

}

/**
 * Validate the $action variable: check CSV-file, perform the data importing
 * or change provider...
 */
if (!in_array($action, $_possible_actions))
    $action = 'check';

$_cache_id = array();

$numeric_h_limit = pow(2, 31);
$numeric_l_limit = pow(2, 31)*-1;

/**
 * Define data for the navigation within section
 */
include_once $xcart_dir . '/include/import_tools.php';

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

$provider_only_sections_tags = array();

// Check section permission and call oninitimport event
if (
    is_array($import_specification)
    && !empty($import_specification)
) {

    foreach ($import_specification as $k => $v) {
        if (
            (
                strpos($v['permissions'], $login_type) === false
                && empty($active_modules['Simple_Mode'])
            )
            || empty($v['script'])
            || empty($v['columns'])
            || !@file_exists($xcart_dir.$v['script'])
            || (
                !empty($v['import_memberships'])
                && (
                    (
                        !empty($user_account['flag'])
                        && !in_array($user_account['flag'], $v['import_memberships'])
                    )
                    || empty($user_account['flag'])
                )
            )
        ) {

            unset($import_specification[$k]);

        } elseif (
            !empty($v['need_provider'])
            && empty($import_data_provider)
            && $user_account['usertype'] == 'A'
        ) {
            unset($import_specification[$k]);

            $provider_only_sections_tags[] = $k;

        } elseif (
            !empty($v['oninitimport'])
            && function_exists($v['oninitimport'])
        ) {

            $res = $v['oninitimport']($k, $import_specification[$k]);

            if (!$res) {

                unset($import_specification[$k]);

            }

        }

    }

}

// Check import specifications and define import options service array
$import_options = array();

if (
    is_array($import_specification)
    && !empty($import_specification)
) {
    foreach ($import_specification as $k => $v) {

        if (
            !empty($v['tpls'])
            && is_array($v['tpls'])
        ) {
            $import_options = func_array_merge($import_options, $v['tpls']);
        }

    }

}
$import_options = array_unique($import_options);

/**
 * Process the import CSV-file
 */
$provider_condition = (
    $single_mode
        ? ''
        : " AND $sql_tbl[products].provider='" . $import_data_provider . "'"
);

if (
    $REQUEST_METHOD == 'POST'
    || !empty($continue)
    || $action == 'do'
) {

    if ($REQUEST_METHOD == 'POST') {

        db_query("DELETE FROM $sql_tbl[import_cache] WHERE userid = '$logged_userid'");

        if (empty($import_pass)) {

            func_check_category_sep($options['category_sep']);

        }

    }

    if (empty($import_specification)) {
        // Display error and exit if no import specification defined

        $top_message['content'] = func_get_langvar_by_name('msg_adm_no_data_can_be_imported', '', false, true);

        func_header_location('import.php');

    }

    if (empty($import_file)) {

        // Prepare the source of importing...

        $import_file = array();

        if (empty($source)) {

            $source = 'upload';

        }

        if (
            empty($source)
            || $source == 'upload'
        ) {
            func_check_uploaded_files_sizes('userfile', 1322);
        }

        if (
            $source == 'server'
            && !empty($localfile)
        ) {
            // File is located on the server
            $localfile = stripslashes($localfile);

            if (func_allow_file($localfile, true)) {

                $import_file['location']     = $localfile;
                $import_file['file_size']     = func_filesize($localfile);
                $import_file['uploaded']     = false;

            }

        } elseif (
            $source == 'upload'
            && !empty($userfile)
        ) {
            // File is uploaded to the server from home computer
            $userfile = func_move_uploaded_file('userfile');

            if ($userfile !== false) {

                $import_file['location']     = $userfile;
                $import_file['file_size']     = func_filesize($userfile);
                $import_file['uploaded']     = true;

            }

        } elseif (
            $source == 'url'
            && !empty($urlfile)
        ) {
            // File is uploaded to the server from remote host
            $urlfile = stripslashes($urlfile);

            $fsize = func_filesize($urlfile);

            if (
                $fsize > 0
                && is_url($urlfile)
            ) {

                $import_file['location']     = $urlfile;
                $import_file['file_size']     = $fsize;
                $import_file['uploaded']     = false;

            }

        }

        if (!empty($import_file)) {
        // Save CSV-delimiter and data provider
            if ($delimiter == 'tab') {

                $delimiter = "\t";

            }

            $import_file['delimiter'] = $delimiter;

            if (
                !empty($drop)
                && is_array($drop)
            ) {
                $tmp = array();

                foreach ($drop as $k => $v) {

                    $tmp[strtolower($k)] = $v;

                }

                $import_file['drop'] = $tmp;

                unset($drop, $tmp);

            }

            if (
                !empty($options)
                && is_array($options)
            ) {

                foreach ($options as $k => $v) {

                    $options[$k] = $import_file[$k] = stripslashes($v);

                }

            }

            if (!empty($import_file['images_directory'])) {

                if (is_url($import_file['images_directory'])) {

                    if (substr($import_file['images_directory'], -1) != '/') {

                        $import_file['images_directory'] .= '/';

                    }

                } else {

                    $rpath = func_realpath($import_file['images_directory']);

                    if (
                        file_exists($rpath)
                        && is_dir($rpath)
                    ) {

                        $import_file['images_directory'] = $rpath;

                        if (substr($import_file['images_directory'], -1) != XC_DS) {

                            $import_file['images_directory'] .= XC_DS;

                        }

                    } else {

                        $import_file['images_directory'] = '';

                    }

                }

            }
        }

    }

    // Open import file

    if (
        $import_file != ''
        && isset($import_file['location'])
    ) {
        $fp = @fopen($import_file['location'], 'r');

        if (
            !@func_filesize($import_file['location'])
            || $fp === false
        ) {

            if ($fp !== false) {

                fclose($fp);

                $fp = false;

            }

            if ($import_file['uploaded']) {

                @unlink($import_file['location']);

            }

            $import_file = '';

        }

    }

    if (empty($import_file)) {
        // File cannot be opened: display error
        x_session_unregister('import_file');

        $top_message['content'] = func_get_langvar_by_name('msg_err_file_wrong');
        $top_message['type']     = 'E';

        func_header_location('import.php');

    }

    func_display_service_header();

    if ($first_pass = empty($import_pass)) {
        // Prepeare the information about first import passing...
        $import_pass = array(
            'file_position'         => 0,
            'section'                 => '',
            'line_index'             => 0,
            'colnames'                 => array(),
            'deprecated_cols'         => array(),
            'renamed_cols'             => array(),
            'error'                 => 0,
            'step'                     => 1,
            'is_subrow'             => false,
            'values'                 => array(),
            'old_sections'             => array(),
            'section_lines_counter' => 0,
            'is_finalize'             => false,
        );

        @unlink($import_log);

        $echo_str = func_get_langvar_by_name(
            ('do' == $action ? 'lbl_process_import_data_' : 'lbl_check_import_data_'),
            false,
            false,
            true
        );

        if (
            !empty($source)
            && $REQUEST_METHOD == 'POST'
        ) {
            if ($delimiter == 'tab') {

                $delimiter = "\t";

            }

            $import_data = array(
                'source'     => $source,
                'localfile' => $localfile,
                'urlfile'     => $urlfile,
                'delimiter' => $delimiter,
                'options'     => $options,
            );
        }

    } else {

        $percent = $import_file['file_size'] && $import_pass['file_position']
            ? sprintf('%.0f', round($import_pass['file_position'] / $import_file['file_size'] * 100, 2))
            : '0.00';

        $echo_str = func_get_langvar_by_name(
            'lbl_total_completed',
            array(
                'percent' => ('do' == $action) ? ($percent / 2 + 50) : ($percent / 2),
            ),
            false,
            true
        );

        $echo_str .= '<br />';

        $echo_str .= func_get_langvar_by_name(
            ('do' == $action) ? 'lbl_process_import_data_step_N_' : 'lbl_check_import_data_step_N_',
            array(
                'step'         => $import_pass['step'],
                'percent'     => $percent,
            ),
            false,
            true
        );

    }

    func_flush("<b>" . $echo_str . "</b><br />");

    $section_tags = array_keys($import_specification);

    // Open the log file for writing

    if (!($logf = @fopen($import_log, "a+"))) {

        $top_message['content'] = func_get_langvar_by_name('msg_err_import_log_writing');
        $top_message['type']     = 'E';

        func_header_location('import.php');

    }

    if ($first_pass) {
        // Start log file writing...
        $current_date = date(
            "d-M-Y H:i:s",
            XC_TIME + $config['Appearance']['timezone_offset']
        );

        $message =<<<OUT
Date: $current_date
Launched by: $login

OUT;

        $message = X_LOG_SIGNATURE . $message;

        func_import_add_to_log($message);
    }

    // Prepare the variables

    $old_sections             = $import_pass['old_sections'];
    $section_lines_counter     = $import_pass['section_lines_counter'];
    $colnames                 = $import_pass['colnames'];
    $deprecated_cols         = $import_pass['deprecated_cols'];
    $renamed_cols             = $import_pass['renamed_cols'];
    $section                 = $import_pass['section'];
    $values                 = $import_pass['values'];
    $current_row             = @$import_pass['current_row'];
    $last_row_idx             = @$import_pass['last_row_idx'];
    $data_row                 = @$import_pass['data_row'];
    $is_subrow                 = $import_pass['is_subrow'];
    $line_index             = 1;
    $file_position             = $import_pass['file_position'];
    $section_start             = true;
    $prev_columns             = array();
    $no_import_sections     = array();

    if (
        !$single_mode
        && $user_account['usertype'] == 'P'
        && !empty($import_data_provider)
    ) {

        $allow_active_content = func_get_allow_active_content($import_data_provider);

    } else {

        $allow_active_content = true;

    }

    // Position the file pointer
    if (
        $file_position > 0
        && $fp
    ) {

        if (is_url($import_file['location'])) {

            for ($x = 0; $x < floor($file_position / 8192); $x++) {

                fread($fp, 8192);

            }

            fread($fp, $file_position % 8192);

        } else {

            fseek($fp, $file_position);

        }

    }

    // Get key columns
    $key_columns = array();

    if (isset($import_specification[$section])) {

        $is_array_fields = false;

        foreach ($import_specification[$section]['columns'] as $k => $v) {

            if ($v['is_key'])
                $key_columns[] = $k;

            if ($v['array'])
                $is_array_fields = true;

        }

        if (!$is_array_fields)
            $key_columns = array();

    }

    $message = <<<OUT
<script type="text/javascript">
//<![CDATA[
    var loaded = false;

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

    // PROCESS THE CSV-FILE ROWS

    while (
        ($columns = fgetcsv ($fp, $max_line_size, $import_file['delimiter']))
        || $import_pass['file_position'] != $file_position
    ) {

        // Break import if too many errors occured
        if ($import_pass['error'] >= $max_errors) {

            func_flush('<script type="text/javascript">//<![CDATA[
loaded = true;
//]]></script>');

            func_html_location("import.php?error=1", 0);

        }

        if (
            $line_index > $step_row
            || $columns === false
        ) {

            if (!empty($section)) {

                $old_sections[$section] = true;

                if ($columns === false) {

                    if (func_import_section_start()) {

                        include $xcart_dir . $import_specification[$section]['script'];

                        if (func_import_section_do()) {

                            include $xcart_dir . $import_specification[$section]['script'];

                            func_import_display_results(strtolower($section), $result[strtolower($section)]);

                        } else {

                            func_flush(". <font color='green'>$_ok</font>");

                        }

                    } elseif (empty($colnames)) {
                        // Empty section header

                        func_import_error('msg_err_import_log_message_42', array('section' => $section));

                    } elseif (empty($section_lines_counter)) {
                        // Empty section body

                        func_import_error('msg_err_import_log_message_43', array('section' => $section));

                    }

                } elseif (!empty($values)) {

                    func_flush(". <font color='green'>$_ok</font>");

                }

            }

            // Follow to the next step of importing...
            $import_pass['old_sections']             = $old_sections;
            $import_pass['section_lines_counter']     = $section_lines_counter;
            $import_pass['section']                 = $section;
            $import_pass['file_position']             = $file_position;
            $import_pass['colnames']                 = $colnames;
            $import_pass['deprecated_cols']         = $deprecated_cols;
            $import_pass['renamed_cols']             = $renamed_cols;

            if ($action == 'do') {
                $import_pass['data_row']             = $data_row;
            }

            $import_pass['values']                     = $values;
            $import_pass['current_row']             = $current_row;
            $import_pass['last_row_idx']             = $last_row_idx;
            $import_pass['is_subrow']                 = $is_subrow;

            $import_pass['step']++;

            fclose($logf);

            fclose($fp);

            func_chmod_file($import_log);

            func_flush('<script type="text/javascript">//<![CDATA[
loaded = true;
//]]></script>');

            if ($columns === false) {

                if (!empty($import_pass['error'])) {

                    func_html_location("import.php?error=1", 1);

                } elseif ($action == 'do') {

                    if ($import_pass['is_finalize']) {

                        func_html_location("import.php?finalize", 1);

                    } else {

                        func_html_location("import.php?complete", 1);

                    }

                } elseif (empty($old_sections)) {

                    $top_message = array(
                        'content'     => func_get_langvar_by_name('msg_data_import_no_sections', false, false, false),
                        'type'         => 'W'
                    );

                    func_html_location("import.php?complete", 1);

                } else {

                    $import_pass = array();

                    func_html_location("import.php?action=do", 1);

                }

            } else {

                func_html_location(
                    "import.php?continue=1"
                        . (
                            $action == 'do'
                                ? "&action=do"
                                : ''
                        ),
                    1
                );

            }

        }

        $file_position = ftell($fp);

        $line_index++;

        $import_pass['line_index']++;

        // Remove deprecated col for data column and change logic of the renamed fields

        if (
            !empty($colnames)
            && !func_import_tag($columns, $section_tags)
            && count(preg_grep("/^\s*\!([\w\d_]+)\s*$/S", $columns)) != count($columns)
        ) {

            if (!empty($renamed_cols)) {

                foreach ($renamed_cols as $ren_col => $ren_func) {

                    $columns[$ren_col] = $ren_func($columns[$ren_col]);

                }

            }

            if (!empty($deprecated_cols)) {

                foreach($deprecated_cols as $dep_col) {

                    unset($columns[$dep_col]);

                }

                $columns = array_values($columns);

            }

        }

        // Clear empty cells on the line tail or add empty cells
        if (empty($colnames)) {

            for ($x = count($columns) - 1; $x >= 0; $x--) {

                if (!empty($columns[$x]))
                    break;

                unset($columns[$x]);

            }

        } elseif (count($columns) != count($colnames)) {

            $count_colnames = count($colnames);

            $count_columns = count($columns);

            if ($count_columns > $count_colnames) {

                for ($x = $count_colnames; $x < $count_columns; $x++) {

                    unset($columns[$x]);

                }

            }

            if ($count_columns < $count_colnames) {

                for ($x = $count_columns; $x < $count_colnames; $x++) {

                    $columns[$x] = '';

                }

            }

        }

        // Check if line is empty...
        if (func_array_empty($columns))
            continue;

        // Check the section tag...
        // e.g. [ZONES]
        if ($_section = func_import_tag($columns, $section_tags)) {

            // Finalize the importing of data from previous section
            if (in_array($_section, $section_tags)) {

                if (func_import_section_start()) {

                    include $xcart_dir . $import_specification[$section]['script'];

                    if (func_import_section_do()) {

                        include $xcart_dir . $import_specification[$section]['script'];

                        func_import_display_results(strtolower($section), $result[strtolower($section)]);

                    } else {

                        func_flush(". <font color='green'>$_ok</font>");

                    }

                } elseif (
                    !empty($section)
                    && empty($colnames)
                ) {
                    // Empty section header

                    func_import_error('msg_err_import_log_message_42', array('section' => $section));

                } elseif (
                    !empty($section)
                    && empty($section_lines_counter)
                ) {
                    // Empty section body

                    func_import_error('msg_err_import_log_message_43', array('section' => $section));

                }

                if (
                    !empty($import_specification[$_section]['need_provider'])
                    && empty($import_data_provider)
                ) {

                    // Check section permission
                    func_import_error('msg_err_import_log_message_2');

                    $_section = '';

                } elseif (!empty($import_specification[$_section]['no_import'])) {

                    $no_import_sections[] = $_section;

                    // Check section flag 'no_import'

                    $_section = '';

                } elseif (
                    !empty($import_specification[$_section]['onstartimportsection'])
                    && function_exists($import_specification[$_section]['onstartimportsection'])
                ) {

                    // Check section 'onstartimportsection' event
                    $res = $import_specification[$_section]['onstartimportsection']($_section, $import_specification[$_section]);

                    if (!$res) {

                        $_section = '';

                    }

                }

                $section                     = $_section;
                $old_sections[$section]     = true;
                $section_lines_counter         = 0;
                $last_row_idx                 = false;
                $section_start                 = true;

                $current_row = $data_row = $values = $_values = array();

                // Get key columns
                $key_columns = array();

                if (isset($import_specification[$section])) {

                    $is_array_fields = false;

                    foreach ($import_specification[$section]['columns'] as $k => $v) {

                        if (!empty($v['is_key']))
                            $key_columns[] = $k;

                        if (!empty($v['array']))
                            $is_array_fields = true;

                    }

                    if (!$is_array_fields)
                        $key_columns = array();

                }

                $colnames             = array();
                $deprecated_cols     = array();
                $renamed_cols         = array();

                func_flush("<br />");

                continue;

            } else {
                // Add message into the log file
                if (in_array($_section, $provider_only_sections_tags)) {

                    $section = '';

                    func_import_error('msg_err_import_log_message_55', array('section' => $_section));

                } else {

                    $section = $_section;

                    func_import_error('msg_err_import_log_message_1');

                    $section = '';

                }

            }

        } // if ($_section = func_import_tag($columns, $section_tags))

        // Get column names (header within section)...
        // e.g. !ZONE;!COUNTRY;!STATE;!COUNTY;!CITY;!ADDRESS;!ZIP
        if (
            !empty($section)
            && empty($colnames)
            && count(preg_grep("/^\s*\!([\w\d_]+)\s*$/S", $columns)) == count($columns)
        ) {

            for ($i = 0; $i < count($columns); $i++) {

                $colnames[$i] = func_import_get_colname($section, trim(strtolower(substr($columns[$i], 1))), $i);

                if (func_import_col_is_deprecated($section, $colnames[$i])) {

                    unset($colnames[$i]);
                    unset($renamed_cols[$i]);

                    $deprecated_cols[] = $i;

                    continue;

                }

                // Column name does not comply with defined for this section
                if (!isset($import_specification[$section]['columns'][$colnames[$i]])) {

                    func_import_error(
                        'msg_err_import_log_message_4',
                        array(
                            'column'  => strtoupper($colnames[$i]),
                            'section' => strtoupper($section),
                        )
                    );

                    $section = '';

                    break;

                }

            }

            if (!empty($deprecated_cols))
                $colnames = array_values($colnames);

            if (!empty($section)) {

                foreach ($import_specification[$section]['columns'] as $cn => $cv) {

                    if (
                        !empty($cv['required'])
                        && !in_array($cn, $colnames)
                    ) {

                        func_import_error('msg_err_import_log_message_7', array('column' => strtoupper($cn)));

                    }

                }

            }

            continue;

        }

        // Next row if column names was not defined...
        if (empty($colnames))
            continue;

        // Detect subrow
        $is_subrow = false;

        if (
            !empty($values)
            && !empty($key_columns)
            && is_array($key_columns)
        ) {
            $is_subrow = true;

            for ($i = 0; $i < count($columns); $i++) {

                if (
                    in_array($colnames[$i], $key_columns)
                    && !empty($columns[$i])
                    && $columns[$i] != $values[$colnames[$i]]
                ) {

                    $is_subrow = false;

                    break;

                }

            }

        }

        // Process current row of values with subrows: validate and prepare for importing
        if (
            !$is_subrow
            && !empty($values)
        ) {

            if (func_import_section_start()) {

                $is_past_data = true;

                include $xcart_dir . $import_specification[$section]['script'];

                if (
                    $line_index % $process_row_dot == 0
                    && !empty($line_index)
                ) {
                    func_flush(". ");
                }

                if (
                    func_import_section_do()
                    && (
                        count($data_row) >= $step_row
                        || strlen(serialize($data_row)) >= $sql_max_allowed_packet * 0.9
                    )
                ) {
                    include $xcart_dir . $import_specification[$section]['script'];

                    func_import_display_results(strtolower($section), $result[strtolower($section)]);

                    $data_row = array();

                }

                $is_past_data = false;

            }

            $current_row = $values = array();

            $last_row_idx = false;

        }

        // Generate the array of values...
        $orig_values = array();

        for ($i = 0; $i < count($columns); $i++) {

            $columns[$i] = preg_replace("/^[ ]+/S", '', preg_replace("/[ ]+$/S", '', $columns[$i]));

            // Check value
            if (!zerolen($columns[$i])) {

                $col_type = 'S';

                if (!empty($import_specification[$section]['columns'][$colnames[$i]]['type']))
                    $col_type = $import_specification[$section]['columns'][$colnames[$i]]['type'];

                $wrong_data_type         = false;
                $wrong_data_type_error     = false;
                $wrong_data_type_lbl     = false;
                $wrong_msg_params         = array();

                if ($col_type == 'S') {

                    if (
                        empty($import_specification[$section]['columns'][$colnames[$i]]['allow_tags'])
                        && func_have_script_tag($columns[$i])
                    ) {
                        $columns[$i] = strip_tags($columns[$i]);
                    }

                    if (
                        isset($import_specification[$section]['columns'][$colnames[$i]]['maxlength'])
                        && intval($import_specification[$section]['columns'][$colnames[$i]]['maxlength']) > 0
                        && strlen($columns[$i]) > intval($import_specification[$section]['columns'][$colnames[$i]]['maxlength'])
                    ) {
                        $columns[$i] = substr($columns[$i], 0, intval($import_specification[$section]['columns'][$colnames[$i]]['maxlength']));
                    }

                // Check integer/float value
                } elseif ($col_type == 'N') {

                    if (!func_is_numeric($columns[$i])) {

                        $wrong_data_type = true;

                    } else {

                        $columns[$i] = (float)$columns[$i];

                        if (
                            $columns[$i] > $numeric_h_limit
                            || $columns[$i] < $numeric_l_limit
                        ) {

                            $wrong_data_type         = true;
                            $wrong_data_type_lbl     = 'msg_err_import_log_message_52';

                        }

                    }

                // Check boolean value
                } elseif ($col_type == 'B') {

                    $columns[$i] = substr(strtoupper($columns[$i]), 0, 1);

                    if (
                        !empty($columns[$i])
                        && !in_array($columns[$i], array('Y','N'))
                    ) {
                        $wrong_data_type = true;
                    }

                // Check enumerated value
                } elseif ($col_type == 'E') {

                    if (
                        !empty($import_specification[$section]['columns'][$colnames[$i]]['variants'])
                        && is_array($import_specification[$section]['columns'][$colnames[$i]]['variants'])
                    ) {

                        if (!@in_array($columns[$i], $import_specification[$section]['columns'][$colnames[$i]]['variants'])) {

                            $wrong_data_type = true;

                        }

                    }

                // Check price value
                } elseif ($col_type == 'P') {

                    if (!func_is_price($columns[$i])) {

                        $wrong_data_type = true;

                        $columns[$i] = '';

                    } else {

                        $columns[$i] = func_detect_price($columns[$i]);

                    }

                // Check markup value
                } elseif ($col_type == 'M') {

                    $cur_symbol = "$";

                    // Detect type of markup (percent or absolute)
                    if (preg_match("/([%$]|" . preg_quote($cur_symbol, '/') . ")$/S", $columns[$i], $match)) {

                        $markup_postfix = $match[1] == "%"
                            ? "%"
                            : "$";

                        $columns[$i] = substr($columns[$i], 0, strlen($match[1]) * -1);

                    } else {

                        $markup_postfix = "$";

                    }

                    // Detect markup as price formatted
                    $columns[$i] = func_detect_price($columns[$i]);

                    if ($columns[$i] === false) {

                        $wrong_data_type = true;

                        $columns[$i] = '';

                    } else {

                        $columns[$i] .= $markup_postfix;

                    }

                // Check language code value
                } elseif ($col_type == 'C') {

                    if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[languages] WHERE code = '" . addslashes($columns[$i]) . "'") == 0) {

                        $wrong_data_type = true;

                        $columns[$i] = '';

                    }

                // Check data value
                } elseif ($col_type == 'D') {

                    // Data as UNIX timestamp
                    if (is_numeric($columns[$i])) {
                        $columns[$i] = abs(intval($columns[$i]));

                    // Data as formatted string
                    } else {

                        $columns[$i] = strtotime($columns[$i]);

                        if ($columns[$i] == -1) {

                            $wrong_data_type = true;

                            $columns[$i] = 0;

                        }

                    }

                // Check image path value
                } elseif ($col_type == 'I') {

                    // Get path to image
                    if (func_is_full_path($columns[$i])) {

                        $file_path = $columns[$i];

                    } elseif (empty($import_file['images_directory'])) {

                        $file_path = $xcart_dir . XC_DS . $columns[$i];

                    } else {

                        $file_path = $import_file['images_directory'] . $columns[$i];

                    }

                    $image_is_url = is_url($file_path);

                    // Check file size (and file availability)
                    if (
                        !$image_is_url
                        && !file_exists($file_path)
                    ) {

                        func_import_error('msg_err_import_log_message_45', array('column' => strtoupper($colnames[$i])));

                        $columns[$i] = '';

                        $wrong_data_type = true;

                        $wrong_data_type_error = true;

                    } elseif (!func_allow_file($file_path, true)) {

                        func_import_error('msg_err_import_log_message_46', array('column' => strtoupper($colnames[$i])));

                        $columns[$i] = '';

                        $wrong_data_type = true;

                        $wrong_data_type_error = true;

                    } else {

                        // Image type exist and registered
                        if ($config['setup_images'][$import_specification[$section]["columns"][$colnames[$i]]["itype"]]) {

                            $data = array(
                                'source'     => $image_is_url ? 'U' : 'S',
                                'type'         => $import_specification[$section]['columns'][$colnames[$i]]['itype'],
                                'date'         => XC_TIME,
                                'file_path' => $file_path,
                                'filename'     => basename($file_path),
                            );

                            list(
                                $data['file_size'],
                                $data['image_x'],
                                $data['image_y'],
                                $data['image_type']
                            ) = func_get_image_size($data['file_path']);

                            if (
                                $data['file_size'] == 0
                                || empty($data['image_type'])
                            ) {
                                // Image file is empty
                                func_import_error('msg_err_import_log_message_47', array('column' => strtoupper($colnames[$i])));

                                $columns[$i] = '';

                                $wrong_data_type = true;

                                $wrong_data_type_error = true;

                            } elseif (($image_perms = func_check_image_perms($data['type'])) !== true) {
                                // Check permissions
                                func_import_error($image_perms['label'], array("path" => $image_perms['path']));

                                $columns[$i] = '';

                                $wrong_data_type = true;

                                $wrong_data_type_error = true;

                            } else {
                                // Save prepared data to cell

                                $columns[$i] = $data;

                            }

                        // Image type exist and not registered
                        } elseif (!empty($import_specification[$section]['columns'][$colnames[$i]]['itype'])) {

                            $wrong_data_type = true;

                            $columns[$i] = '';

                        // Image type does not exist
                        } else {

                            $columns[$i] = $file_path;

                        }

                    }

                } elseif ($col_type == 'U') {
                    // Check clean url format

                    if (!func_clean_url_check_format($columns[$i])) {

                        $wrong_data_type = true;

                        $wrong_data_type_lbl = 'err_clean_url_wrong_format';

                        $columns[$i] = '';

                    } elseif (!func_clean_url_fs_check($columns[$i], true)) {

                        $wrong_data_type = true;

                        $wrong_data_type_lbl = 'err_clean_url_existing_fs_entity';

                        $columns[$i] = '';

                    }

                } elseif ($col_type == 'Z') {
                    // Find index of related country field in columns
                    $_prefix = str_replace('zipcode', '', $colnames[$i]);

                    $_country_code = '';

                    $_country_idx = array_search($_prefix . 'country', $colnames);

                    if ($_country_idx !== false)
                        $_country_code = $columns[$_country_idx];

                    // Check zipcode format
                    if (!func_check_zip($columns[$i], $_country_code)) {

                        $wrong_data_type = true;

                        if (!empty($_country_code)) {

                            $wrong_data_type_lbl = 'msg_err_import_log_message_57';

                            $wrong_msg_params = array(
                                'zipcode_value' => $columns[$i],
                                'zipcode_field' => $colnames[$i],
                                'country_code'     => $_country_code,
                                'country_field' => $colnames[$_country_idx],
                            );

                        } else {
                            // Country code is not provided -> produce common error

                            $wrong_data_type_lbl = 'txt_error_common_zip_code';

                            $wrong_msg_params = array(
                                'address' => '',
                                'zip4_format' => ''
                            );

                        }

                        $columns[$i] = '';

                    }

                }

                if (
                    $wrong_data_type
                    && !$wrong_data_type_error
                ) {
                    if ($wrong_data_type_lbl) {

                        func_import_error($wrong_data_type_lbl, array_merge(array('column' => strtoupper($colnames[$i])), $wrong_msg_params));

                    } else {

                        func_import_error('msg_err_import_log_message_12', array('column' => strtoupper($colnames[$i])));

                    }

                }

                // EOL tag converting
                if (
                    $col_type == 'S'
                    && $import_specification[$section]['columns'][$colnames[$i]]['eol_safe']
                    && strpos($columns[$i], "<EOL>") !== false
                ) {
                    $columns[$i] = str_replace("<EOL>", "\n", $columns[$i]);
                }

            }

            // Remove tags for untrusted providers to avoid xss attacks
            if (
                !is_numeric($columns[$i])
                && !$allow_active_content
            ) {

                $columns[$i] = func_strip_tags($columns[$i]);

            }

            // Set default value
            if (
                empty($columns[$i])
                && isset($import_specification[$section]['columns'][$colnames[$i]]['default'])
            ) {

                if ($import_specification[$section]['columns'][$colnames[$i]]['type'] == "D") {

                    if (!empty($import_specification[$section]['columns'][$colnames[$i]]['default'])) {

                        $tmp = strtotime($import_specification[$section]['columns'][$colnames[$i]]['default']);

                        if ($tmp != -1)
                            $columns[$i] = $tmp;
                    }

                } else {

                    $columns[$i] = $import_specification[$section]['columns'][$colnames[$i]]['default'];

                }

            }

            // Check for required fields
            if (
                !empty($import_specification[$section]['columns'][$colnames[$i]]['required'])
                && empty($columns[$i])
                && !is_numeric($columns[$i])
                && (
                    !$is_subrow
                    || $import_specification[$section]['columns'][$colnames[$i]]['array']
                )
            ) {
                func_import_error('msg_err_import_log_message_7', array('column' => strtoupper($colnames[$i])));
            }

            if ($last_row_idx === false)
                $last_row_idx = $import_pass['line_index'];

            // Set value as subrow
            if (!empty($import_specification[$section]['columns'][$colnames[$i]]['array'])) {

                if ($is_subrow) {

                    $values[$colnames[$i]][] = $columns[$i];

                } else {

                    $values[$colnames[$i]] = array($columns[$i]);

                }

            // Set value as string
            } elseif (
                !$is_subrow
                && !empty($colnames[$i])
            ) {

                $values[$colnames[$i]] = $columns[$i];

            }

            // Save the original value from the current row
            // (is used within some import modules)
            if (!empty($colnames[$i]))
                $orig_values[$colnames[$i]] = $columns[$i];

        }

        $section_lines_counter++;

        $current_row[] = $orig_values;

        $prev_columns = $columns;

    } // end while

    // Close log file

    fclose($logf);

    func_chmod_file($import_log);

    // Prepare the QUERY_STRING for returning...

    if (!empty($import_pass['error'])) {

        // Error occured - stop importing

        db_query("DELETE FROM $sql_tbl[import_cache] WHERE userid = '$logged_userid'");

        $query_str = "error=1";

    } elseif ($action == 'do') {

        func_flush('<script type="text/javascript">//<![CDATA[
loaded = true;
//]]></script>');

        if ($import_pass['is_finalize']) {

            func_html_location("import.php?finalize", 1);

        } else {

            func_html_location("import.php?complete", 1);

        }

    } elseif ($action == 'finalize') {

        // Import successfully completed

        $top_message['content'] = func_get_langvar_by_name('msg_data_import_success', false, false, false);

        $query_str = 'complete';

    } elseif (
        empty($old_sections)
        && empty($action)
    ) {
        // Import file hasn't any sections

        $top_message = array(
            'content'     => func_get_langvar_by_name('msg_data_import_no_sections', false, false, false),
            'type'         => 'W'
        );

        $query_str = 'complete';

    } elseif (
        !empty($section)
        && empty($colnames)
    ) {
        // Empty section header

        func_import_error('msg_err_import_log_message_42', array('section' => $section));

    } elseif (
        !empty($section)
        && empty($section_lines_counter)
    ) {
        // Empty section body

        func_import_error('msg_err_import_log_message_43', array('section' => $section));

    } else {
        // Continue processing

        $query_str = "action=do";

    }

    // Process finished - need to clear these variables
    $import_pass = array();

    func_flush('<script type="text/javascript">//<![CDATA[
loaded = true;
//]]></script>');

    func_html_location("import.php?" . $query_str, 3);

/**
 * last step (after importing)
 */
} elseif (
    isset($_GET['finalize'])
    && $REQUEST_METHOD == 'GET'
) {

    func_display_service_header();

    func_flush("<b>" . func_get_langvar_by_name('lbl_finalize_import_data', NULL, false, true) . "</b><br />");

    if (!empty($import_pass['old_sections'])) {

        if (!($logf = @fopen($import_log, "a+"))) {

            $top_message['content'] = func_get_langvar_by_name('msg_err_import_log_writing');
            $top_message['type']     = 'E';

            func_header_location('import.php');

        }

        foreach ($import_pass['old_sections'] as $section => $tmp) {

            if (empty($section))
                continue;

            $import_step = 'complete';

            include $xcart_dir . $import_specification[$section]['script'];
        }

        fclose($logf);

        func_chmod_file($import_log);

    }

    db_query("DELETE FROM $sql_tbl[import_cache] WHERE userid = '$logged_userid'");

    $import_pass = array();

    $top_message['content'] = func_get_langvar_by_name('msg_data_import_success', false, false, false);

    func_flush('<script type="text/javascript">//<![CDATA[
loaded = true;
//]]></script>');

    func_html_location("import.php?complete", 3);

} elseif ($REQUEST_METHOD == 'POST') {

    func_header_location('import.php');

} elseif (isset($_GET['complete'])) {

    if (!empty($no_import_sections)) {

        $top_message = $smarty->get_template_vars('top_message');

        if (isset($top_message['content'])) {

            $top_message['content'] .= "<br />";

        } else {

            $top_message['content'] = '';

        }

        $top_message['content'] .= func_get_langvar_by_name('msg_data_no_import_sections', false, false, false)
            . ": <b>"
            . implode(", ", $no_import_sections)
            . "</b>";

        $smarty->assign('top_message', $top_message);

        $top_message = '';

    }

    if (!empty($final_notes)) {

        $top_message = $smarty->get_template_vars('top_message');

        $top_message['type'] = 'W';

        foreach ($final_notes as $f_note) {

            $br = ($top_message['content'] == "")
                ? ''
                : "<br />";

            $top_message['content'] .= $br
                . func_get_langvar_by_name($f_note, false, false, false);
        }

        $smarty->assign('top_message', $top_message);

        $top_message = '';

        $final_notes = array();

    }

}

/**
 * Delete uploaded file after it is processed
 */
if (!empty($import_file['uploaded']))
    @unlink($import_file['location']);

$import_file = array();

$import_pass = array();

x_session_save();

if ($fl = @fopen($import_log, 'r')) {

    // Prepare data about import log file for displaying

    $content = fread($fl, 16000);

    fclose($fl);

    $content = str_replace(X_LOG_SIGNATURE, '', $content);
    $content = htmlentities($content);
    $content = str_replace("\n", "<br />", $content);

    $smarty->assign('import_log_content',     $content);
    $smarty->assign('import_log_url',         $import_log_url);

}

$smarty->assign('import_log_file',             $import_log);

if (!empty($error))
    $smarty->assign('show_error', 1);

x_load('category');
$smarty->assign('allcategories', func_data_cache_get("get_categories_tree", array(0, true, $shop_language, $user_account['membershipid'])));

foreach ($import_specification as $s => $v) {

    if (!empty($v['import_note'])) {

        $import_specification[$s]['import_note'] = func_get_langvar_by_name($v['import_note'], array(), false, true);

    }
}

$smarty->assign('import_specification',     $import_specification);
$smarty->assign('import_options',             $import_options);
$smarty->assign('my_files_location',         func_get_files_location() . XC_DS);

$import_data['options']['category_sep'] = func_import_get_category_sep(@$import_data['options']['category_sep']);

if (empty($import_data['source']))
    $import_data['source'] = 'server';

$smarty->assign('import_data',                 $import_data);

switch ($import_data['source']) {
    case 'upload':
        $import_data_filesrc = 2;
        break;

    case 'url':
        $import_data_filesrc = 3;
        break;

    default:
        $import_data_filesrc = 1;
}

$smarty->assign('import_data_filesrc',         $import_data_filesrc);
$smarty->assign('upload_max_filesize',         func_convert_to_megabyte(func_upload_max_filesize()));
$smarty->assign('allow_url_fopen',             (bool)ini_get('allow_url_fopen'));

?>
