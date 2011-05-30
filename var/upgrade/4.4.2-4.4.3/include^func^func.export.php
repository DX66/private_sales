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
 * Data export functions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.export.php,v 1.28.2.1 2011/01/10 13:11:51 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

/**
 * Save export range
 */
function func_export_range_save($section, $data)
{
    global $sql_tbl, $export_ranges, $logged_userid;

    if (empty($data))
        return false;

    $section = strtoupper($section);
    if (is_string($data)) {
        $export_ranges[$section] = $data;
        db_query("DELETE FROM $sql_tbl[export_ranges] WHERE sec = '".addslashes($section)."' AND userid='$logged_userid'");
    }
    elseif (is_array($data)) {
        func_unset($export_ranges, $section);
        db_query("DELETE FROM $sql_tbl[export_ranges] WHERE sec = '".addslashes($section)."' AND userid='$logged_userid'");
        foreach ($data as $v) {
            func_array2insert('export_ranges', array('sec' => addslashes($section), 'id' => $v, 'userid' => $logged_userid), true);
        }
    }
    else {
        return false;
    }

    return true;
}

// Get export range
function func_export_range_get($section)
{
    global $sql_tbl, $export_ranges, $logged_userid;

    $type = func_export_range_type($section);
    if ($type == 'S') {
        return $export_ranges[$section];

    } elseif ($type == 'C') {

        // Use numeric sorting for ORDERS ORDER_ITEMS PRODUCTS keys

        if (in_array($section, array('ORDERS','ORDER_ITEMS','PRODUCTS')))
            return "SELECT (id+0) as id FROM $sql_tbl[export_ranges] WHERE sec = '".addslashes($section)."' AND userid='$logged_userid' ORDER BY id";
        else
            return "SELECT id FROM $sql_tbl[export_ranges] WHERE sec = '".addslashes($section)."' AND userid='$logged_userid' ORDER BY id";
    }

    return false;
}

/**
 * Get export range type
 */
function func_export_range_type($section)
{
    global $sql_tbl, $export_ranges, $logged_userid;

    $section = strtoupper($section);
    if (is_array($export_ranges) && isset($export_ranges[$section])) {
        return 'S';
    }
    else {
        if (func_query_column("SELECT id FROM $sql_tbl[export_ranges] WHERE sec = '".addslashes($section)."' AND userid='$logged_userid' ORDER BY id"))
            return 'C';
    }
    return false;
}

/**
 * Get parent section with not empty export range
 */
function func_export_range_detect($section, $last_range = '')
{
    global $sql_tbl, $import_specification;

    $section = strtoupper($section);
    if (func_export_range_get_num($section) !== false)
        $last_range = $section;

    if (!empty($import_specification[$section]['parent']))
        return func_export_range_detect($import_specification[$section]['parent'], $last_range);

    return $last_range;
}

/**
 * Get count of export range
 */
function func_export_range_get_num($section)
{
    $tmp = func_export_range_get($section);
    if ($tmp === false)
        return false;

    if (is_string($tmp) && !zerolen($tmp)) {
        $res = db_query($tmp);
        if ($res) {
            $tmp = db_num_rows($res);
            db_free_result($res);

            return $tmp;
        }

        return 0;
    }

    return false;
}

// Erase export range
function func_export_range_erase($section, $id = false)
{
    global $sql_tbl, $export_ranges, $logged_userid;

    func_unset($export_ranges, $section);
    db_query("DELETE FROM $sql_tbl[export_ranges] WHERE sec = '".addslashes($section)."' AND userid='".$logged_userid."'".(($id !== false) ? " AND id = '".$id."'" : ""));

    return true;
}

/**
 * Get export sections tree
 */
function func_export_define($data, $parent = '')
{
    global$_export_define_hash;

    if (empty($data))
        return false;

    // Create service hash array
    if (empty($parent)) {
        foreach ($data as $k => $v) {
            $_export_define_hash[$k] = true;
        }
    }

    // Build tree
    $ret = array();
    foreach ($data as $k => $v) {
        if (!isset($_export_define_hash[$v['parent']]))
            $v['parent'] = "";
        if ($v['parent'] != $parent)
            continue;
        $v['name'] = $k;
        $v['display_title'] = str_replace('_', ' ', $k);
        $cnt = func_export_range_get_num($k);
        $v['range_count'] = ($cnt === false) ? -1 : $cnt;
        $ret[$k] = $v;
        $tmp = func_export_define($data, $k);
        if (!empty($tmp))
            $ret[$k]['subsections'] = $tmp;
    }
    if (empty($ret))
        return false;

    // Sort by orderby field and section name
    uasort($ret, 'func_export_cmp_orderby');
    return $ret;
}

/**
 * Sorting function: sort sections list by 'orderby' field
 */
function func_export_cmp_orderby($a, $b)
{
    if ($a['orderby'] == $b['orderby']) {
        return strcmp($a['name'], $b['name']);
    }
    return $a['orderby'] > $b['orderby'] ? 1 : -1;
}

/**
 * This function adds message to the export log file
 */
function func_export_add_to_log($message)
{
    global $logf;

    if (!empty($message) && $logf)
        fwrite($logf, $message."\n");

    return true;
}

/**
 * Select export file
 */
function func_export_open_file()
{
    global $section, $export_data, $export_fp, $current_code, $current_code, $config, $md5_login;

    $is_reselect = false;
    $is_rw_header = false;

    // Check current file competence
    if ($export_data['line'] > $export_data['rows_per_file'] && $export_data['rows_per_file'] > 0 && empty($export_data['last_code'])) {

        // The export line limit has been exceeded
        $export_data['part']++;
        $export_data['line'] = 0;
        $is_rw_header = true;
        $is_reselect = true;

    } elseif ($current_code != $export_data['last_code']) {

        // Export data has different language code
        if ($current_code == $config['default_admin_language']) {
            if ($export_data['last_code'] != false) {
                $export_data['last_section'] = $section;
                $export_data['last_code'] = false;
                $export_data['last_limit'] = 0;
                $is_reselect = true;
            }

        } else {

            $export_data['last_section'] = $section;
            $export_data['last_code'] = $current_code;
            $export_data['last_limit'] = 0;
            $is_reselect = true;
        }

    } elseif ($section != $export_data['last_section']) {

        // Export data has different section
        $export_data['last_section'] = $section;
        $export_data['last_limit'] = 0;
        $is_reselect = true;
    }

    if ($is_reselect || !$export_fp) {
        // Define export file name
        $c = '';
        if ($export_data['part'] > 0)
            $c .= '_'.str_repeat('0', 3-strlen($export_data['part'])).$export_data['part'];

        if (!empty($export_data['last_code']))
            $c .= '_' . $export_data['last_code'];

        $name = $export_data['prefix'].$c."_".$md5_login.".csv.php";

        if ($export_fp) {
            fclose($export_fp);
            func_chmod_file($name);

            if (!$is_rw_header) {
                $export_data['header'] = array();
            }
        }

        // Open file
        $is_new = !file_exists($name);
        $export_fp = @fopen($name, 'a');
        if (!$export_fp) {
            global $top_message;
            $top_message['content'] = func_get_langvar_by_name("err_cannot_open_the_export_file");
            $top_message['type'] = "E";
            return false;
        }

        // Write header to file if file is new
        if ($is_new) {
            fwrite($export_fp, X_LOG_SIGNATURE);
            $export_data['pos'] = strlen(X_LOG_SIGNATURE);
        }
        if ($is_rw_header)
            func_export_write_header(NULL, true);
    }

    return $export_fp;
}

/**
 * Init export header
 */
function func_export_write_header($data = NULL, $is_rw = false)
{
    global $section, $export_data, $import_specification;

    if (!empty($export_data['header']) && !$is_rw)
        return true;

    // Write only section header
    if ($is_rw && !empty($export_data['header'])) {
        return func_export_write_header2file();
    }

    $is_new = empty($export_data['last_section']);

    $export_data['header'] = array();

    if (empty($data) || !is_array($data)) {
        $data = array_keys($import_specification[$section]['columns']);
    }

    $fp = func_export_open_file();
    if (!$fp)
        return false;

    foreach ($data as $k => $v) {
        if (empty($v)) {
            unset($data[$k]);
        } elseif (!isset($import_specification[$section]['columns'][$v])) {
            unset($data[$k]);
        } elseif ($export_data['options']['export_images'] != 'Y' && $import_specification[$section]['columns'][$v]['type'] == "I") {
            unset($data[$k]);
        }
    }
    if (empty($data))
        return false;

    $export_data['header'] = array_flip(array_values($data));

    return true;
}

/**
 * Write header to export file
 */
function func_export_write_header2file()
{
    global $export_data, $section;

    if (empty($export_data['header']))
        return false;

    $fp = func_export_open_file();
    if (!$fp)
        return false;

    $data = array_map('strtoupper', array_keys($export_data['header']));
    fwrite($fp, "[".$section."]\n");
    fwrite($fp, "!".implode($export_data['delimiter']."!", $data)."\n");
    $export_data['pos'] = ftell($fp);

    return true;
}

/**
 * Write export row
 */
function func_export_write_row($data)
{
    global $section, $export_data, $import_specification, $line, $dot_per_row, $is_continue;
    global $logf;

    if (empty($data) || !is_array($data) || empty($export_data['header']))
        return false;

    $fp = func_export_open_file();
    if (!$fp)
        return false;

    if (!$is_continue) {
        if (!func_export_write_header2file())
            return false;
        $is_continue = true;
    }

    // Check row
    $row = array();
    $subrow = array();
    $max_subrow = 0;
    foreach ($export_data['header'] as $k => $v) {
        if (!isset($import_specification[$section]['columns'][$k]))
            continue;

        // Check cell
        if (!isset($data[$k])) {
            // Add empty cell
            $row[$v] = '';
            continue;
        }

        // Check array-cell as subrow
        if (is_array($data[$k]) && $import_specification[$section]['columns'][$k]['array']) {
            $row[$v] = func_export_cell_format(array_shift($data[$k]), $import_specification[$section]['columns'][$k]);
            if (!empty($data[$k])) {
                $data[$k] = array_values($data[$k]);

                if (!empty($data[$k])) {
                    // Define subrows service array
                    foreach ($data[$k] as $sk => $sv) {
                        $subrow[$sk][$v] = func_export_cell_format($sv, $import_specification[$section]['columns'][$k]);
                    }
                    if ($max_subrow < count($data[$k]))
                        $max_subrow = count($data[$k]);
                }
            }
        } else {
            $row[$v] = func_export_cell_format((string)$data[$k], $import_specification[$section]['columns'][$k]);
        }
    }

    if (empty($row))
        return false;

    // Write row
    ksort($row, SORT_NUMERIC);
    fwrite($fp, implode($export_data['delimiter'], $row)."\n");

    // Write subrows
    if (!empty($subrow)) {
        for ($x = 0; $x < $max_subrow; $x++) {
            foreach ($row as $k => $v) {
                if(!isset($subrow[$x][$k]))
                    $subrow[$x][$k] = '';
            }

            ksort($subrow[$x], SORT_NUMERIC);
            fwrite($fp, implode($export_data['delimiter'], $subrow[$x])."\n");
        }

    }

    $export_data['pos'] = ftell($fp);
    $export_data['line']++;
    $export_data['total_line']++;
    $export_data['pass_line']++;
    $line++;

    // Echo dot
    if (($line % $dot_per_row == 0) && !empty($dot_per_row) && !empty($line)) {
        fwrite($logf, '.');
        echo '.';
        if (($line % ($dot_per_row * 100) == 0) && !empty($dot_per_row) && !empty($line)) {
            fwrite($logf, "\n");
            echo "<br />\n";
        }
        func_flush();
    }

    return true;
}

/**
 * Format cell by type
 */
function func_export_cell_format($data, $cell)
{
    global $export_data;

    // Check numeric-cell
    if ($cell['type'] == "N" || $cell['type'] == "P") {
        $data = doubleval($data);
        if ($data != floor($data))
            $data = sprintf("%01.03f", $data);

    // Check date-cell
    } elseif ($cell['type'] == "D") {
        if (is_numeric($data))
            $data = date('l d F Y h:i:s A', $data);

    // Check enumerated-cell
    } elseif ($cell['type'] == "E" && !empty($cell['variants']) && !in_array($data, $cell['variants'])) {
        $data = '';

    // Check image-cell
    } elseif ($cell['type'] == "I" && !empty($cell['itype'])) {

        if ($export_data['options']['export_images'] == 'Y') {
            $data = func_copy_image_to_fs($data, $cell['itype'], $export_data['data_dir']);
        } else {
            $data = '';
        }

    // Check date-cell
    } elseif (($cell['type'] == "S" || empty($cell['type'])) && !empty($cell['eol_safe'])) {
        $data = preg_replace("/\n/Ss", "<EOL>", $data);
    }

    return func_value_normalize($data, ($cell['type'] == "S" || empty($cell['type'])));
}

/**
 * Column value normaliztion function
 */
function func_value_normalize($value, $force_quote = false)
{
    global $export_data;
    $value = preg_replace("/\r\n|\n|\r/Ss", " ", $value);
    if ($force_quote || @preg_match("/(".preg_quote($export_data['delimiter'], "/").")|\t/S", $value)) {
        $value = '"'.str_replace('"', '""', $value).'"';
        if (substr($value, -2) == '\"' && preg_match('/[^\\\](\\\+)"$/Ss', $value, $preg) && strlen($preg[1]) % 2 != 0) {
            $value = substr($value, 0, -2)."\\".substr($value, -2);
        }
    }
    return $value;
}

/**
 * Read section data
 */
function func_export_read_data($section)
{
    global $sql_tbl, $export_ranges, $export_data, $current_code, $import_specification, $parent_range_query;

    $section = strtoupper($section);
    $type = func_export_range_type($section);
    if ($type == 'S' || $type == 'C') {
        $query = func_export_range_get($section);
    } else {
        $query = $import_specification[$section]['export_sql'];
    }

    if (empty($query))
        return false;

    if (!empty($current_code)) {
        $query = str_replace("{{code}}", $current_code, $query);
    }

    $parent_range_query = false;
    if ($import_specification[$section]['parent'] && !in_array($type, array("S","C"))) {

        // Check parent sections chains by range conditions
        $s = $section;
        $childs = array();

        // Define nearest parent section wuth range condition
        while (!empty($import_specification[$s]['parent']) && empty($import_specification[$s]['is_range'])) {
            if (empty($import_specification[$s]['table']) || empty($import_specification[$s]['key_field']) || empty($sql_tbl[$import_specification[$s]['table']])) {

                $s = false;
                break;
            }
            $childs[] = array(
                'table' => $import_specification[$s]['table'],
                'key_field' => $import_specification[$s]['key_field'],
                'parent_key_field' => $import_specification[$s]['parent_key_field']
            );
            $s = $import_specification[$s]['parent'];
        }

        if (!empty($s) && !empty($import_specification[$s]['is_range'])) {
            // Check parent section range condition
            $type = func_export_range_type($s);
            if ($type == 'S' || $type == 'C')
                $parent_range_query = func_export_range_get($s);

            if (!empty($parent_range_query)) {
                $parent_range_query = array(
                    'section' => $s,
                    'type' => $type,
                    'childs' => $childs,
                    'query' => $parent_range_query
                );

                // Replace child cond by the parent one

                if (!empty($parent_range_query['query'])
                 && !empty($import_specification[$section]['key_field'])
                 && $import_specification[$section]['key_field'] == $import_specification[$parent_range_query['section']]['key_field']) {
                    $query = preg_replace('/ LIMIT .*/si', ' ', $parent_range_query['query']);
                    $parent_range_query = false;
                }
            }
        }
    }

    return db_query($query." LIMIT ".$export_data['last_limit'].", 999999999");
}

/**
 * Read section data row (step by step)
 */
function func_export_get_row($res)
{
    global $section, $import_specification, $parent_range_query, $sql_tbl;

    if (!$res)
        return false;

    func_export_line();

    $row = db_fetch_array($res);
    if ($row === false)
        return false;

    if (empty($parent_range_query))
        return array_shift($row);

    // Check parent section range condition
    $for = array();
    $where = array();
    $last_key = "'{{KEY}}'";
    foreach ($parent_range_query['childs'] as $k => $v) {
        $for[] = $sql_tbl[$v['table']]." as tbl$k";
        $where[] = "tbl$k.".$v['key_field']." = ".$last_key;
        $last_key = "tbl$k.";

        if (!empty($v['parent_key_field'])) {
            $last_key .= $v['parent_key_field'];

        } elseif ($k+1 < count($parent_range_query['childs'])) {
            $last_key .= $parent_range_query['childs'][$k+1]['key_field'];

        } else {
            $last_key .= $import_specification[$parent_range_query['section']]['key_field'];
        }
    }

    // Define SQL query (get parent section ID by current section ID)
    $query = "SELECT ".$sql_tbl[$import_specification[$parent_range_query['section']]['table']].".".$import_specification[$parent_range_query['section']]['key_field']." FROM ".$sql_tbl[$import_specification[$parent_range_query['section']]['table']].", ".implode(", ", $for)." WHERE ".$sql_tbl[$import_specification[$parent_range_query['section']]['table']].".".$import_specification[$parent_range_query['section']]['key_field']." = ".$last_key." AND ".implode(" AND ", $where);

    while ($row !== false) {
        $row = array_shift($row);

        // Get parent section ID by current section ID
        $ids = func_query_column(str_replace("{{KEY}}", addslashes($row), $query));

        if (!empty($ids)) {

            // Check defined parent section IDs
            $tmp = db_query($parent_range_query['query']);
            if ($tmp) {
                while ($id = db_fetch_array($tmp)) {
                    $id = array_shift($id);
                    if (in_array($id, $ids))
                        return $row;
                }
                db_free_result($tmp);
            }
        }

        // Get next current section ID
        $row = db_fetch_array($res);
    }

    return false;
}

/**
 * Increments the counter of exported lines and
 * performs self-redirect if the counter exceeds a certain amount
 */
function func_export_line()
{
    global $sql_tbl, $export_data, $import_specification, $section, $current_code, $step_row, $line;

    if ($step_row <= $export_data['pass_line'] && !empty($step_row)) {

        // Display section footer
        $message = func_get_langvar_by_name('lbl_rows', NULL, false, true).": ".$line;
        func_export_add_to_log("\n".$message."\n");
        echo "<br />\n".$message."<br />\n<br />\n";
        func_flush();

        // Self-redirect
        $export_data['last_section'] = $section;
        $export_data['last_code'] = $current_code;
        $export_data['pass']++;

        func_html_location("import.php?mode=export&action=continue", 3);
    }

    $export_data['last_limit']++;
}

/**
 * Export image to file system
 */
function func_copy_image_to_fs($id, $type, $file_path)
{
    global $sql_tbl, $config, $xcart_dir, $active_modules, $magnifier_sets;

    if (!isset($sql_tbl['images_'.$type]))
        return false;

    if ($config['available_images'][$type] == "M") {
        $where = " WHERE imageid = '$id'";
    } else {
        $where = " WHERE id='$id'";
    }

    // Get image data
    $v = func_query_first("SELECT * FROM ".$sql_tbl['images_'.$type].$where);

    if (empty($v))
        return false;

    if (!empty($active_modules['Magnifier']) && $type == 'Z' && $magnifier_sets['save_init_image'] == 1) {
        $v['image_path'] = preg_replace("/\/$v[filename]/", '/init_image.jpg', $v['image_path']);
        $v['filename'] = 'init_image.jpg';
        $v['image_size'] = @filesize($xcart_dir.XC_DS.$v['image_path']);
    }

    $file_path .= XC_DS.$type;
    if (!is_dir($file_path) || !file_exists($file_path)) {
        func_mkdir($file_path);
    }

    if (!is_dir($file_path) || !file_exists($file_path))
        return false;

    // Copy image from DB/FS to temp export directory
    if (!empty($v['image']) || (!empty($v['image_path']) && !is_url($v['image_path']))) {
        $fname = (empty($v['filename']) ? strtolower($type)."_".$id : $v['filename']);

        // Detect file extension
        $ftype = 'gif';
        if (empty($v['filename']) && !empty($v['image_type'])) {
            if (preg_match("/\/(.+)$S/", $v['image_type'], $match))
                $ftype = $match[1];
        }
        if(preg_match("/^(.+)\.([^\.]*)$/S", $v['filename'], $match) && !empty($v['filename'])) {
            $fname = $match[1];
            $ftype = $match[2];
        }

        // Detect unique filename
        $cnt = 1;
        $fname_orig = $fname;
        while (file_exists($file_path.XC_DS.$fname.'.'.$ftype) && $cnt < 99) {
            $fname = $fname_orig.$cnt++;
        }
        $file_name = $file_path.XC_DS.$fname.'.'.$ftype;

        // Get image content if image stored on FS
        if (empty($v['image'])) {
            if ($fp = @fopen($xcart_dir.XC_DS.$v['image_path'], "rb", true)) {
                $v['image'] = fread($fp, @filesize($xcart_dir.XC_DS.$v['image_path']));
                fclose($fp);
            } else {
                return false;
            }
        }

        // Write to temp export directory and return filename
        if ($fp = @fopen($file_name, 'wb')) {
            fwrite($fp, $v['image']);
            fclose($fp);
            func_chmod_file($file_name);

            return $type.'/'.$fname.'.'.$ftype;
        }

    } elseif (!empty($v['image_path']) && is_url($v['image_path'])) {

        // Return full image path (URL) if image stored as URL
        return $v['image_path'];

    }

    return false;
}

/**
 * Rename hash array cell
 */
function func_export_rename_cell($data, $rename)
{
    if (empty($data) || !is_array($data) || !is_array($rename) || empty($rename))
        return $data;

    foreach ($rename as $k => $v) {
        if (isset($data[$k])) {
            $data[$v] = $data[$k];
            unset($data[$k]);
        }
    }
    return $data;
}

/**
 * Check if X-Cart can write to export_dir
 */
function func_export_dir_is_writable() 
{
    global $export_dir;

    if (!is_writable($export_dir) || !is_dir($export_dir)) {
        return false;
    }

    $check_dir = $export_dir . XC_DS . 'check_export_dir' . XC_TIME;

    if (!func_mkdir($check_dir)) {
        // Try to change perm 
        func_chmod($export_dir, 'dir');
        if (!func_mkdir($check_dir)) {
            return false;
        }    
    }

    func_rm_dir($check_dir);

    return true;
}

?>
