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
 * Data import functions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.import.php,v 1.25.2.3 2011/01/10 13:11:51 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

/**
 * Detect product by productid / productcode / product name
 * Output: array
 *        productid
 *        variantid (if Product Options module is enabled)
 */
function func_import_detect_product($values, $use_provider = true)
{
    global $action, $active_modules, $sql_tbl, $import_data_provider, $single_mode, $import_file;

    $provider_condition = '';
    if (!$use_provider && !$single_mode && !empty($import_data_provider))
        $provider_condition = " AND $sql_tbl[products].provider='".$import_data_provider."'";

    $values_exists = 0;
    $values_not_added = 0;

    $_productid = $_variantid = NULL;
    if (!empty($values['productid'])) {
        $values_exists++;
        $_productid = func_import_get_cache('PI', $values['productid']);
        if (is_null($_productid) || ($action == 'do' && empty($_productid))) {
            $values_not_added++;
            $_productid = func_query_first_cell("SELECT productid FROM $sql_tbl[products] WHERE productid = '$values[productid]'".$provider_condition);
            if (!empty($_productid))
                func_import_save_cache('PI', $values['productid'], $_productid);
        }
        unset($values['productid']);
    }
    if (!empty($values['productcode']) && (is_null($_productid) || ($action == "do" && empty($_productid)))) {
        $values_exists++;
        $_productid = func_import_get_cache('PR', $values['productcode']);
        if (is_null($_productid) || ($action == 'do' && empty($_productid))) {
            $values_not_added++;
            $_productid = func_query_first_cell("SELECT productid FROM $sql_tbl[products] WHERE productcode = '".addslashes($values['productcode'])."'".$provider_condition);
            if (!empty($_productid))
                func_import_save_cache('PR', $values['productcode'], $_productid);
        }
        if ((is_null($_productid) || ($action == 'do' && empty($_productid))) && !empty($active_modules['Product_Options'])) {
            $tmp = func_query_first("SELECT $sql_tbl[variants].variantid, $sql_tbl[products].productid FROM $sql_tbl[products], $sql_tbl[variants] WHERE $sql_tbl[products].productid = $sql_tbl[variants].productid AND $sql_tbl[variants].productcode = '".addslashes($values['productcode'])."'".$provider_condition);
            if (!empty($tmp)) {
                $_productid = $tmp['productid'];
                $_variantid = $tmp['variantid'];
            }
        }
        unset($values['productcode']);
    }
    if (!empty($values['product']) && (is_null($_productid) || ($action == "do" && empty($_productid)))) {
        $values_exists++;
        $_productid = func_import_get_cache('PN', $values['product']);
        if (is_null($_productid) || ($action == 'do' && empty($_productid))) {
            $values_not_added++;
            $_productid = func_query_first_cell("SELECT productid FROM $sql_tbl[products] WHERE product = '".addslashes($values['product'])."'".$provider_condition);
            if (!empty($_productid))
                func_import_save_cache('PN', $values['product'], $_productid);
        }
        unset($values['product']);
    }

    // Check: product MUST be added if section PRODUCTS will be droped
    if ($values_exists == $values_not_added && $import_file['drop']['products'] == 'Y')
        func_import_module_error('msg_err_import_log_message_14');

    return array($_productid, $_variantid);
}

/**
 * Get standart product signature (productid / productcode / product)
 */
function func_export_get_product($productid)
{
    global $sql_tbl, $provider_sql;

    return func_query_first("SELECT productid, productcode, product FROM $sql_tbl[products] WHERE productid = '$productid'".(empty($provider_sql) ? "" : " AND $sql_tbl[products].provider = '$provider_sql'"));
}

/**
 * Get cell from import cache (product signature based)
 */
function func_import_get_pb_cache($values, $type, $add_key = '', $force_save = false)
{
    if (empty($type) || (empty($values['productid']) && empty($values['productcode']) && empty($values['product'])))
        return NULL;

    $key = '';
    if (!empty($add_key))
        $key = "\n".$add_key;

    list($_productid, $_variantid) = func_import_detect_product($values);

    $id = NULL;
    $do_force_save = false;
    if (!empty($values['productid'])) {
        $id = func_import_get_cache($type.'i', $values['productid'].$key);
        if (is_null($id) && $force_save)
            $do_force_save = true;

        if (!is_null($id) && empty($id)) {
            list($_productid, $_variantid) = func_import_detect_product($values);
            if (!empty($_productid) && $values['productid'] != $_productid)
                $id = func_import_get_cache($type.'i', $_productid.$key);
        }

    } elseif (!empty($values['productcode']) && (is_null($id) || $force_save)) {
        $id = func_import_get_cache($type.'s', $values['productcode'].$key);
        if (is_null($id) && $force_save)
            $do_force_save = true;

    } elseif (!empty($values['product']) && (is_null($id) || $force_save)) {
        $id = func_import_get_cache($type.'n', $values['product'].$key);
        if (is_null($id) && $force_save)
            $do_force_save = true;
    }

    if ($do_force_save)
        func_import_save_pb_cache($values, $type, $add_key, '');

    return $id;
}

/**
 * Save data to import cache (product signature based)
 */
function func_import_save_pb_cache($values, $type, $add_key = NULL, $value, $force_save = false)
{
    if (empty($type) || (empty($values['productid']) && empty($values['productcode']) && empty($values['product'])))
    return NULL;

    $key = '';
    if (!empty($add_key))
        $key = "\n".$add_key;

    if (!empty($values['productid'])) {
        func_import_save_cache($type.'i', $values['productid'].$key, $value, $force_save);
    }
    if (!empty($values['productcode'])) {
        func_import_save_cache($type.'s', $values['productcode'].$key, $value, $force_save);
    }
    if (!empty($values['product'])) {
        func_import_save_cache($type.'n', $values['product'].$key, $value, $force_save);
    }
    return true;
}

function func_import_rebuild_product($productid)
{
    global $active_modules;
    if ($active_modules['Recommended_Products']) {
        func_refresh_product_rnd_keys($productid);
    }

    func_build_quick_flags($productid);
    func_build_quick_prices($productid);
}

function func_import_detect_category($values)
{
    global $action, $active_modules, $sql_tbl, $import_file;

    $values_exists    = 0;
    $values_not_added = 0;
    $_categoryid      = NULL;

    if (!empty($values['categoryid'])) {

        $values_exists++;
        $tmp = func_import_get_cache('CI', $values['categoryid']);

        if (is_null($tmp) || ($action == 'do' && empty($tmp))) {

            $values_not_added++;
            $c = func_query_first("SELECT categoryid, category FROM $sql_tbl[categories] WHERE categoryid = '$values[categoryid]'");

            if (!empty($c)) {

                $_categoryid = $c['categoryid'];

                x_load('category');
                $ids = func_get_category_path($c['categoryid']);

                if (count($ids) == 1) {
                    $cname = $c['category'];
                } else {
                    $where = array();
                    $orderby = "CASE categoryid ";
                    for ($x = 0; $x < count($ids); $x++) {
                        $where[] = "(categoryid = '".$ids[$x]."' AND parentid = '".(($x == 0) ? 0 : $ids[$x-1])."')";
                        $orderby .= "WHEN ".$ids[$x]." THEN ".$x." ";
                    }
                    $orderby .= 'END';
                    $ids = func_query_column("SELECT category FROM $sql_tbl[categories] WHERE ".implode(" OR ", $where)." ORDER BY ".$orderby);
                    $cname = implode($import_file['category_sep'], $ids);
                }

                if (!empty($cname) && !empty($_categoryid)) {
                    func_import_save_cache('CI', $values['categoryid'], $cname);
                }
            }
        } else {
            $_categoryid = $values['categoryid'];
        }
        unset($values['categoryid']);
    }

    if (!empty($values['category']) && (is_null($_categoryid) || ($action == "do" && empty($_categoryid)))) {
        $values_exists++;
        $_categoryid = func_import_get_cache('C', $values['category']);
        if (is_null($_categoryid) || ($action == 'do' && empty($_categoryid))) {
            $ids = explode($import_file['category_sep'], $values['category']);
            $_parentid = 0;
            for ($x = 0; $x < count($ids); $x++) {
                $_categoryid = func_query_first_cell("SELECT categoryid FROM $sql_tbl[categories] WHERE category = '".addslashes($ids[$x])."' AND parentid = '$_parentid'");
                if (empty($_categoryid))
                    break;
                $_parentid = $_categoryid;
            }
            if (!empty($_categoryid))
                func_import_save_cache('C', $values['category'], $_categoryid);
        }

        unset($values['category']);
    }

    // Check: category MUST be added if section CATEGORIES will be droped
    if ($values_exists == $values_not_added && $import_file['drop']['categories'] == 'Y')
        func_import_module_error('msg_err_import_log_message_18');

    return $_categoryid;
}

/**
 * Get standard category signature (categoryid / category)
 */
function func_export_get_category($categoryid)
{
    global $sql_tbl, $export_data;

    $cat['category'] = func_get_category_path($categoryid, 'category', true, $export_data['options']['category_sep']);
    
    return $cat;
}

// Check uniqueness of clean URL in a CSV file.
function func_import_check_clean_url_uniqueness()
{
    global $values, $action;

    if (!isset($values['clean_url']) || zerolen($values['clean_url'])) {
        return true;
    }

    $tmp = func_import_get_cache('CIU', $values['clean_url']);
    if (is_null($tmp)) {
        $check_result = true;
        func_import_save_cache('CIU', $values['clean_url']);
    } elseif ($tmp === false && $action == 'check') {
        // Clean URL is a unique value and can not be used by several entries in a CSV file.
        $check_result = false;
        func_import_module_error('msg_err_import_log_message_54');
        unset($values['clean_url']);
    }

    return $check_result;
}

/**
 * Get category_sep which is not used in category names
 */
function func_import_get_category_sep($category_sep_local = '/')
{
    global $mode, $action, $REQUEST_METHOD, $sql_tbl, $continue;

    if ($REQUEST_METHOD != 'GET')
        return $category_sep_local;

    $possible_category_seps = array('/','|','||','|||','#','##','###','|-|','--','@','@@','@@@','!!','!!!','$$','$$$','^^','!^^!','++','==','===','!==!','=!!=','||-||','=|-|=');

    $category_sep = empty($category_sep_local) ? '/': $category_sep_local;
    $i = 0;
    while (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[categories] WHERE category LIKE '%".addslashes($category_sep)."%'") && $i < count($possible_category_seps))
        $category_sep = $possible_category_seps[++$i];

    return $category_sep;
}

/**
 * Check if there are some categories with category_sep in the name
 */
function func_check_category_sep($category_sep_local)
{
    global $top_message, $sql_tbl, $HTTP_REFERER, $mode;

    $category_sep_local = empty($category_sep_local) ? '/' : $category_sep_local;
    $bad_cat_names = func_query_column("SELECT CONCAT('<a href=\"category_modify.php?cat=', categoryid, '\">', category, '</a>') as
g FROM $sql_tbl[categories] WHERE category LIKE '%$category_sep_local%' LIMIT 10");
    if (!empty($bad_cat_names)) {
        $top_message['content'] = func_get_langvar_by_name('msg_err_import_category_sep', array('cats'=>implode('<br />', $bad_cat_names)), false, true);
        $top_message['type'] = 'E';

        $redirect_to = 'import.php';
        if ($mode == 'import' || empty($mode))
            $redirect_to .= '?mode=import&open_options=Y';
        elseif ($mode == 'export')
            $redirect_to .= '?mode=export';

        func_header_location($redirect_to);
    }

    return true;
}

/**
 * Check array elements emptiness
 */
function func_array_empty($data)
{
    if (empty($data))
        return true;

    if (!is_array($data))
        return empty($data);

    foreach ($data as $v) {
        if (is_array($v)) {
            if (!func_array_empty($v))
                return false;

        } elseif (!empty($v)) {
            return false;
        }
    }

    return true;
}

/**
 * This function checks if current row contains the section tag
 */
function func_import_tag($columns)
{
    if (!preg_match("/^\[([\w_ ]+)\]$/S", trim($columns[0]), $found))
        return false;

    for ($i = 1; $i < count($columns); $i++) {
        if (!empty($columns[$i]))
            return false;
    }

    return trim(strtoupper($found[1]));
}

/**
 * This function adds message to the import log file
 */
function func_import_add_to_log($message, $columns=array(), $value="", $line_index=0)
{
    global $logf;

    if ($line_index > 0)
        $message = "Error on line $line_index: ".$message;

    if (!empty($value))
        $message .= " ($value)";
    if (!empty($columns)) {
        if (is_array($columns)) {
            $message .= ":\n".implode(";", $columns);
        } else {
             $message .= ":\n".$columns;
        }
    }

    if (!empty($message))
        fwrite($logf, $message."\n\n");

    return true;
}

/**
 * This function adds error message to the import log file
 */
function func_import_error($msg, $params = NULL)
{
    global $import_pass, $columns, $section, $is_past_data, $prev_columns;

    $import_pass['error']++;
    $message = func_get_langvar_by_name($msg, $params, false, true);
    if ($is_past_data)
        func_import_add_to_log($message, $prev_columns, $section, $import_pass['line_index']-1);
    else
        func_import_add_to_log($message, $columns, $section, $import_pass['line_index']);
}

/**
 * This function adds error message to the import log file from import modules
 */
function func_import_module_error($msg, $params = NULL, $internal_index = false)
{
    global $import_pass, $current_row, $section, $last_row_idx, $xcart_dir, $import_file;

    $import_pass['error']++;
    $row = $current_row;
    foreach ($row as $k => $v) {

        // Replace 'Array' string into more informative file_path in the log

        foreach(array('image','thumbnail','icon') as $img) {
            if (isset($v[$img]) && isset($v[$img]['file_path'])) {
                $v[$img] = str_replace(array($import_file['images_directory'], $xcart_dir), '', $v[$img]['file_path']);
            }
        }

        $row[$k] = implode(";", $v);
    }
    $message = func_get_langvar_by_name($msg, $params, false, true);
    func_import_add_to_log($message, implode("\n", $row), $section, $internal_index !== false ? $internal_index : $last_row_idx);
}

/**
 * Get real file line index for Finalize step
 */
function func_import_get_real_idx($data, $idx)
{
    global $last_row_idx;

    return $last_row_idx - count($data) + $idx + 1;

}

/**
 * This function prepares the import result message,
 * writes this message to the import log file and displays it in the browser
 */
function func_import_display_results($section, $result = NULL)
{
    global $_ok;

    $_import_result_message = func_get_langvar_by_name('txt_'.$section.'_import_result', $result,false,true);

    $message=<<<OUT
<font color='green'>$_ok</font>
<br />$_import_result_message<br />
OUT;

    // Add stripped message to the log...
    func_import_add_to_log(func_get_langvar_by_name('lbl_'.$section.'_importing_',false,false,true) . (strip_tags(str_replace(array("&nbsp;","<br />"), array('',"\n"), $message))));

    // Display message...
    func_flush($message);

    return true;
}

/**
 * Save data cache as hash array
 */
function func_import_save_cache($type, $id, $value = NULL, $force_save = false)
{
    global $logged_userid, $action, $old_sections, $section, $import_specification, $sql_tbl;

    if (is_array($id))
        $id = implode('_', $id);

    if ($action != 'do' && !$force_save) {
        foreach ($import_specification as $s => $sec) {
            if (isset($sec['depending']) && isset($old_sections[$s]) && in_array($type, $sec['depending'])) {
                $value = NULL;
                break;
            }
        }
        if (isset($import_specification[$section]['depending']) && in_array($type, $import_specification[$section]['depending']))
            $value = NULL;
    }

    db_query("REPLACE INTO $sql_tbl[import_cache] VALUES ('$type','".addslashes($id)."','".(empty($value) ? "RESERVED" : addslashes($value))."','$logged_userid')");
}

/**
 * Get value from data cache (by data type and cell id)
 */
function func_import_get_cache($type, $id)
{
    global $logged_userid, $sql_tbl;

    if (is_array($id))
        $id = implode('_', $id);

    $data = func_query_first_cell("SELECT value FROM $sql_tbl[import_cache] WHERE data_type = '$type' AND id = '".addslashes($id)."' AND userid = '$logged_userid'");
    if ($data === false) {
        $data = NULL;

    } elseif ($data == 'RESERVED') {
        $data = false;
    }

    return $data;
}

/**
 * Save old object IDs
 */
function func_import_save_cache_ids($type, $sql_query)
{
    global $action;

    $return = false;
    $ids = db_query($sql_query);
    if ($ids) {
        if (db_num_fields($ids) < 2)
            return false;

        $return = db_num_rows($ids);
        while ($row = db_fetch_row($ids)) {
            $id = array_shift($row);
            func_import_save_cache($type, implode('_', $row), $id, true);
        }
        db_free_result($ids);
    }

    return $return;
}

/**
 * Erase cache cell (or row)
 */
function func_import_erase_cache($type, $id = false)
{
    global $logged_userid, $sql_tbl;

    if ($id === false) {
        if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[import_cache] WHERE data_type = '$type' AND userid = '$logged_userid'")) {
            db_query("DELETE FROM $sql_tbl[import_cache] WHERE data_type = '$type' AND userid = '$logged_userid'");
            return true;
        }
        return false;
    }

    $data = func_query_first_cell("SELECT value FROM $sql_tbl[import_cache] WHERE data_type = '$type' AND id = '".addslashes($id)."' AND userid = '$logged_userid'");
    if ($data === false)
        return false;

    if ($data == 'RESERVED')
        db_query("DELETE FROM $sql_tbl[import_cache] WHERE data_type = '$type' AND id = '".addslashes($id)."' AND userid = '$logged_userid'");

    return true;
}

/**
 * Returns information on whether import cache of specified type has cache entries or not.
 *
 * @param   mixed    $type    Import cache type.
 * @access  public
 * @return  boolean    True if import cache of specified type has cache entries and false in other case.
 * @since   4.2.0
 */
function func_import_cache_has_records($type)
{
    global $logged_userid, $sql_tbl;

    if (!is_string($type)) {
        return false;
    }

    return func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[import_cache] WHERE data_type = '".addslashes($type)."' AND userid= '$logged_userid'") > 0;
}

/**
 * Read cache data step-by-step
 */
function func_import_read_cache($type)
{
    global $_cache_id, $logged_userid, $sql_tbl;

    if (!@$_cache_id[$type]) {
        $_cache_id[$type] = db_query("SELECT id, value FROM $sql_tbl[import_cache] WHERE data_type = '$type' AND userid = '$logged_userid'");
    }

    if (!$_cache_id[$type])
        return false;

    if ($tmp = db_fetch_row($_cache_id[$type]))
        return $tmp;

    db_free_result($_cache_id[$type]);
    $_cache_id[$type] = false;

    return false;
}

/**
 * Display section header
 */
function func_import_section_start()
{
    global $section_start, $section, $colnames, $values, $import_step, $action, $_values;

    if (empty($section) || empty($colnames) || empty($values))
        return false;

    $import_step = 'process_row';
    $_values = $values;

    if (!$section_start)
        return true;

    if ($action == 'do') {
        $lbl = func_get_langvar_by_name('lbl_'.strtolower($section).'_importing_',false,false,true);
    } else {
        $lbl = func_get_langvar_by_name('lbl_'.strtolower($section).'_checking_',false,false,true);
    }
    if (!empty($lbl)) {
        func_flush("<br />".$lbl."<br />");
    }
    $section_start = false;

    return true;
}

/**
 * Display section import header
 */
function func_import_section_do()
{
    global $action, $section, $data_row, $import_specification, $import_pass, $result, $import_step, $values, $_values;

    if (!empty($_values))
        $values = $_values;

    if (empty($data_row) || empty($section) || $action != 'do')
        return false;

    if ($import_specification[$section]['finalize'])
        $import_pass['is_finalize'] = true;

    $result[strtolower($section)] = array(
        'added' => 0,
        'updated' => 0
    );
    $import_step = 'finalize';

    return true;
}

/**
 * Get subrows count
 */
function func_import_get_count($values)
{
    $max_cnt = 0;
    foreach ($values as $v) {
        if (is_array($v)) {
            foreach ($v as $i => $v2) {
                if ($i > $max_cnt)
                    $max_cnt = $i;
            }
        }
    }
    return $max_cnt;
}

/**
 * Define $data array for direct insert/update
 */
function func_import_define_data($row, $keys, $check_empty = true)
{
    $data = array();
    foreach ($row as $k => $v) {
        $key = false;
        if (!empty($keys[$k])) {
            $key = $keys[$k];

        } elseif (($pos = array_search($k, $keys)) !== false) {
            if (is_int($pos))
                $key = $keys[$pos];
        }

        if (!empty($key) || (isset($key) && !$check_empty)) {
            $data[$key] = is_string($v) ? addslashes($v) : $v;
        }
    }

    return $data;
}

/**
 * Save image id by image type
 */
function func_import_save_image($type)
{
    global $sql_tbl;
    $res = db_query("SELECT imageid, id FROM ".$sql_tbl['images_'.$type]);
    if ($res) {
        while ($row = db_fetch_array($res)) {
            func_import_save_cache('I', $type.'_'.$row['id'], $row['imageid']);
        }
        db_free_result($res);
    }
    return true;
}

/**
 * Save image for import cell to DB
 */
function func_import_save_image_data($type, $id, $data, $_imageid = false)
{
    global $config, $sql_tbl, $xcart_dir;

    if (empty($data))
        return false;

    $temp_file_upload_data = array($type => $data);
    if (func_check_image_posted($temp_file_upload_data, $type)) {
        if ($config['available_images'][$type] == 'U') {
            $prop = func_query_first("SELECT image_path, filename, (image IS NOT NULL AND LENGTH(image) > '0') AS in_db FROM ".$sql_tbl['images_'.$type]." WHERE id = '$id'");

            // Check imported image the same that assigned
            if (!zerolen($prop['image_path']) && !is_url($prop['image_path']) && !$prop['in_db']) {
                $image_path = $prop['image_path'];
                if (zerolen($image_path)) {
                    $image_path = func_relative_path(func_image_dir($type).'/'.$prop['filename']);
                }

                $image_file = realpath($xcart_dir.'/'.$image_path);
                if (strcasecmp($temp_file_upload_data[$type]['file_path'], $image_file) == 0) {
                    // skip image import
                    return true;
                }
            }

            func_delete_image($id, $type);
        }

        if (empty($_imageid))
            $_imageid = func_import_get_cache('I', $type.'_'.$id);

        return func_save_image($temp_file_upload_data, $type, $id, array(), $_imageid);
    }
    return false;
}

function func_import_is_provider_rewrite($values)
{
    global $current_area, $single_mode, $logged_userid;

    if (!is_array($values)) {
        $values['provider'] = $values;
    }

    return (
        !empty($values['provider'])
        && (
            $current_area == 'A'
            || $single_mode
            || $logged_userid == $values['provider']
        )
    );
}

/**
 * Return the same colname or alias if any (for backward compatibility) and define change logic rule for some fields
 */
function func_import_get_colname($section, $colname, $column_id)
{
    global $import_specification, $renamed_cols;

    // Array of aliases for names from old X-Cart version

    $columns_aliases = array(
        'CATEGORIES' => array(#for 4.1.12-4.1.0
            'meta_descr' => 'meta_description'
        ),
        'PRODUCTS' => array(#for 4.1.12-4.1.0
            'dim_x' => 'length',
            'dim_y' => 'width',
            'dim_z' => 'height',
        ),
        'PRODUCT_CONFIGURATOR_STEPS' => array( #for 4.1.3-4.1.0
            'step' => 'stepid', #this field has func_import_change_stepid function
        )
    );

    if (isset($import_specification[$section]['columns'][$colname])) {
        return $colname;
    }
    elseif (isset($columns_aliases[$section][$colname])) {

        // Check if we should change logic of the column for the backward compatibility

        if (function_exists('func_import_change_' . $columns_aliases[$section][$colname])) #like func_import_change_stepid
            $renamed_cols[$column_id] = 'func_import_change_' . $columns_aliases[$section][$colname];

        return $columns_aliases[$section][$colname];
    }

    return $colname;
}

/**
 * Change logic of step field to stepid field in the PRODUCT_CONFIGURATOR_STEPS section #for 4.1.3-4.1.0 import files
 */
function func_import_change_stepid($field)
{
    global $language_var_names;
    return preg_replace('/'.preg_quote($language_var_names['step_name'], "/")."/si", "", $field);
}

/**
 * Check if the colname is deprecated
 */
function func_import_col_is_deprecated($section, $colname)
{
    global $import_specification;

    $columns_deprecated = array(
        'USERS' => array(#for 4.1.3-4.1.0
            'password_hint'=>1, 'password_hint_answer'=>1, 'parent'=>1, 'pending_plan_id'=>1
        ),
        'PRODUCT_CONFIGURATOR_STEPS' => array( #for 4.1.3-4.1.0
            'descr'=>1
        ),
        'PRODUCT_CONFIGURATOR_SLOTS' => array( #for 4.1.3-4.1.0
            'descr'=>1, 'slot'=>1, 'step'=>1
        ),
        'PRODUCT_VARIANTS' => array( #for 4.1.3-4.1.0
            'optionid'=>1
        ),
        'SHIPPING_RATES' => array( #for 4.1.3-4.1.0
            'maxamount'=>1
        ),
        'PRODUCTS' => array( #for 4.2.1
            'generate_thumbnail'=>1
        )
    );

    if (!isset($import_specification[$section]['columns'][$colname]) && isset($columns_deprecated[$section][$colname]))
        return true;

    return false;
}

/**
 * Detect user by id / login+usertype
 *
 * @param mixed $values Import data
 *
 * @return null/int
 * @see    ____func_see____
 */
function func_import_detect_user($values)
{
    global $action, $active_modules, $sql_tbl, $import_data_provider, $single_mode, $import_file;

    $values_exists = 0;
    $values_not_added = 0;

    $_userid = null;

    // Check by ID
    if (!empty($values['userid'])) {

        $values_exists++;
        $_userid = func_import_get_cache('UI', $values['userid']);

        if (
            is_null($_userid)
            || (
                $action == 'do'
                && empty($_userid)
            )
        ) {
            $values_not_added++;
            $_userid = func_query_first_cell("SELECT id FROM $sql_tbl[customers] WHERE id = '$values[userid]'");

            if (!empty($_userid)) {
                func_import_save_cache('UI', $values['userid'], $_userid);
            }
        }
        unset($values['userid']);
    }

    // Check by ID+usertype
    if (
        !empty($values['login'])
        && !empty($values['usertype'])
        && (
            is_null($_userid)
            || (
                $action == 'do'
                && empty($_userid)
            )
        )
    ) {
        $values_exists++;

        $_userid = func_import_get_cache('UL' . $values['usertype'], $values['login']);
        if (
            is_null($_userid)
            || (
                $action == 'do'
                && empty($_userid)
            )
        ) {
            $values_not_added++;
            $_userid = func_query_first_cell("SELECT id FROM $sql_tbl[customers] WHERE login = '" . addslashes($values['login']) . "' AND usertype = '$values[usertype]'");

            if (!empty($_userid)) {
                func_import_save_cache('UL' . $values['usertype'], $values['login'], $_userid);
            }
        }
    }

    // Check: user MUST be added if section PRODUCTS will be droped
    if (
        $values_exists == $values_not_added
        && $import_file['drop']['users'] == 'Y'
    ) {
        func_import_module_error('msg_err_import_log_message_57');
    }

    return $_userid;
}

/**
 * Get default user signature (id / login / usertype)
 *
 * @param mixed $userid ____param_comment____
 *
 * @return void
 * @see    ____func_see____
 */
function func_export_get_user($userid)
{
    global $sql_tbl;

    $userid = abs(intval($userid));

    return func_query_first("SELECT id AS userid, login, usertype FROM $sql_tbl[customers] WHERE id = '$userid'");
}

?>
