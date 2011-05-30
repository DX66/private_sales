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
 * Functions for the clean URLs functionality
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.clean_urls.php,v 1.23.2.3 2011/02/07 15:34:45 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

function func_clean_url_get_resources_data($resource_type = NULL)
{
    global $sql_tbl;
    static $resources_data;

    if (!isset($resources_data)) {

        $resources_data = array(
            'P' => array(
                'resource_name'         => func_get_langvar_by_name('lbl_products', false, false, true),
                'resource_table'         => $sql_tbl['products'],
                'resource_id_column'     => 'productid',
                'params'                 => array('product', 'productcode'),
                'err_type_title'         => 'lbl_products',
                'err_msg'                 => 'lbl_product_clean_url_err',
            ),
            'C' => array(
                'resource_name'         => func_get_langvar_by_name('lbl_categories', false, false, true),
                'resource_table'         => $sql_tbl['categories'],
                   'resource_id_column'     => 'categoryid',
                'params'                 => array('category'),
                'err_type_title'         => 'lbl_categories',
                'err_msg'                 => 'lbl_category_clean_url_err',
            ),
            'M' => array(
                'resource_name'         => func_get_langvar_by_name('lbl_manufacturers', false, false, true),
                'resource_table'         => $sql_tbl['manufacturers'],
                'resource_id_column'     => 'manufacturerid',
                'params'                 => array('manufacturer'),
                'err_type_title'         => 'lbl_manufacturers',
                'err_msg'                 => 'lbl_manufacturer_clean_url_err',
            ),
            'S' => array(
                'resource_name'         => func_get_langvar_by_name('lbl_static_pages', false, false, true),
                'resource_table'         => $sql_tbl['pages'],
                'resource_id_column'     => 'pageid',
                'params'                 => array('title', 'filename'),
                'err_type_title'         => 'lbl_static_pages',
                'err_msg'                 => 'lbl_staticpage_clean_url_err',
            )
        );

    }

    if (!is_null($resource_type) && is_string($resource_type))  {

        if (!in_array($resource_type, array_keys($resources_data))) {
            return NULL;
        }

        return $resources_data[$resource_type];
    }

    return $resources_data;
}

function func_clean_url_validation_regexp()
{
    return '^([a-zA-Z0-9_.-]{1}|[a-zA-Z0-9_.-][a-zA-Z0-9_.\/-]{0,248}[a-zA-Z0-9_.-])$';
}

function func_clean_url_check_format($clean_url)
{
    if (!is_string($clean_url)) {
        return false;
    }

    if (zerolen($clean_url) || strlen($clean_url) > 250) {
        return false;
    }

    $clean_url_validation_regexp = func_clean_url_validation_regexp();

    if (!preg_match('/' . $clean_url_validation_regexp . '/D', $clean_url)) {
        return false;
    }

    if (preg_match('/\.html$/iD', $clean_url)) {
        return false;
    }

    return true;
}

function func_clean_url_fs_check($clean_url, $skip_format_check = false)
{
    global $xcart_dir;

    if ($skip_format_check == false && !func_clean_url_check_format($clean_url)) {
        return false;
    }

    $check_paths =  array(
        $xcart_dir . XC_DS . $clean_url,
        $xcart_dir . XC_DS . $clean_url . '.html',
    );

    foreach ($check_paths as $path) {

        if (
            is_link($path)
            || is_dir($path)
            || is_file($path)
        ) {

            return false;

        }

    }

    return true;
}

function func_clean_url_validate($clean_url)
{
    global $xcart_dir;

    if (!func_clean_url_check_format($clean_url)) {
        return array(false, 'CLEAN_URL_WRONG_FORMAT');
    }

    if (func_clean_url_has_url($clean_url)) {
        return array(false, 'CLEAN_URL_EXISTING_DB_RECORD');
    }

    if (!func_clean_url_fs_check($clean_url, true)) {
        return array(false, 'CLEAN_URL_EXISTING_FS_ENTITY');
    }

    return array(true, 'CLEAN_URL_OK');
}

function func_clean_url_validate_resource_type($resource_type)
{
    static $supported_resource_types = array('P', 'C', 'M', 'S');

    if (!is_scalar($resource_type)) {
        return false;
    }

    if (in_array($resource_type, $supported_resource_types)) {
        return true;
    }

    return false;
}

function func_clean_url_validate_resource_id($resource_id)
{
    if (is_int($resource_id)) {
        return true;
    }

    return is_numeric($resource_id) && $resource_id == (int)$resource_id;
}

function func_clean_url_cleanup_string($string)
{
    global $default_charset;

    if (!is_string($string)) {
        return NULL;
    }

    $string = trim($string);

    if (zerolen($string)) {
        return '';
    }

    $string = preg_replace('/\&(?!#[0-9]+;)(?!#x[0-9a-f]+;)/', '-and-', preg_replace('/\&amp;/', '-and-', $string));

    return preg_replace('/-$/', '', preg_replace('/[-]+/', '-', preg_replace('/[^a-zA-Z0-9._-]/', '-', func_translit($string, $default_charset, '-'))));
}

function func_clean_url_has_url($clean_url, $excluding_resource = NULL)
{
    global $sql_tbl;

    if (!func_clean_url_check_format($clean_url)) {
        return NULL;
    }

    $exclusion_condition = '';

    if (
        !is_null($excluding_resource)
        && isset($excluding_resource['resource_type'])
        && isset($excluding_resource['resource_id'])
    ) {
        $exclusion_condition = " AND NOT (resource_type = '" . func_addslashes($excluding_resource['resource_type']) . "' AND resource_id = '" . func_addslashes($excluding_resource['resource_id']) . "')";
    }

    $count = func_query_first_cell("SELECT COUNT(*) FROM " . $sql_tbl['clean_urls'] . " WHERE clean_url='" . func_addslashes($clean_url) . "'" . $exclusion_condition);

    return $count;
}

function func_clean_url_resource_has_record($resource_type, $resource_id, $filename = '')
{
    global $sql_tbl;

    if (!func_clean_url_validate_resource_type($resource_type)) {
        return NULL;
    }

    if (
        !(
            $resource_type == 'S'
            && !empty($filename)
        ) && !func_clean_url_validate_resource_id($resource_id)
    ) {
        return NULL;
    }

    $count = func_query_first_cell("SELECT COUNT(*) FROM " . $sql_tbl['clean_urls'] . " WHERE resource_type = '" . func_addslashes($resource_type) . "' AND resource_id = '" . func_addslashes($resource_id) . "'");

    if (
        $count == 0
        && $resource_type == 'S'
    ) {
        $count = func_query_first_cell("SELECT COUNT(*) FROM " . $sql_tbl['clean_urls'] . ", " . $sql_tbl['pages'] . " WHERE " . $sql_tbl['clean_urls'] . ".resource_id = " . $sql_tbl['pages'] . ".pageid AND " . $sql_tbl['clean_urls'] . ".resource_type = 'S' AND " . $sql_tbl['pages'] . ".filename = '" . func_addslashes($filename) . "'");
    }

    return $count;
}

function func_clean_url_get_raw_resource_url($resource_type, $resource_id, $filename = '')
{
    global $sql_tbl;

    global $clean_url_cache;

    $cache_md5 = md5($resource_type . $resource_id . $filename);

    if (isset($clean_url_cache[$cache_md5])) {

        return $clean_url_cache[$cache_md5];

    }

    if (!func_clean_url_validate_resource_type($resource_type)) {
        return NULL;
    }

    if (
        !(
            $resource_type == 'S'
            && !empty($filename)
        ) && !func_clean_url_validate_resource_id($resource_id)
    ) {
        return NULL;
    }

    $resource_type = func_addslashes($resource_type);
    $resource_id = func_addslashes($resource_id);

    if ($resource_type == 'S') {

        if (!empty($resource_id))
            $filename = func_addslashes(func_query_first_cell("SELECT filename FROM $sql_tbl[pages] WHERE pageid='$resource_id'"));

        $pageids = func_query_column("SELECT pageid FROM " . $sql_tbl['pages'] . " WHERE filename='" . $filename . "'");

        $clean_url = func_query_first_cell("SELECT clean_url FROM " . $sql_tbl['clean_urls'] . " WHERE resource_type = '" . $resource_type . "' AND resource_id IN ('" . implode("','", $pageids) . "')");

        return $clean_url;
    }

    $clean_url = func_query_first_cell("SELECT clean_url FROM " . $sql_tbl['clean_urls'] . " WHERE resource_type = '" . $resource_type . "' AND resource_id = '" . $resource_id . "'");

    $clean_url_cache[$cache_md5] = $clean_url;

    return $clean_url;
}

function func_clean_url_get($resource_type, $resource_id, $absolute_path = true)
{
    global $config, $xcart_catalogs;

    if (!func_clean_url_validate_resource_type($resource_type)) {
        return NULL;
    }

    if (!func_clean_url_validate_resource_id($resource_id)) {
        return NULL;
    }

    $clean_url = func_clean_url_get_raw_resource_url($resource_type, $resource_id);

    if (!zerolen($clean_url)) {
        $clean_url = ($absolute_path == true ? $xcart_catalogs['customer'] . '/' : '') . $clean_url . $config['SEO']['clean_urls_ext_' . strtolower($resource_type)];
    }

    return $clean_url;
}

function func_clean_url_add($clean_url, $resource_type, $resource_id)
{
    global $config;

    if (!func_clean_url_check_format($clean_url)) {
        return NULL;
    }

    if (!func_clean_url_validate_resource_type($resource_type)) {
        return NULL;
    }

    if (!func_clean_url_validate_resource_id($resource_id)) {
        return NULL;
    }

    $record = array(
        'clean_url'         => func_addslashes($clean_url),
        'resource_type'     => func_addslashes($resource_type),
        'resource_id'         => func_addslashes($resource_id),
        'mtime'             => XC_TIME - $config['Appearance']['timezone_offset']
    );

    return func_array2insert(
        'clean_urls',
        $record
    ) !== false;
}

function func_clean_url_update($new_clean_url, $resource_type, $resource_id, $move_to_history = true)
{
    global $config;

    if (!func_clean_url_check_format($new_clean_url)) {
        return NULL;
    }

    if (!func_clean_url_validate_resource_type($resource_type)) {
        return NULL;
    }

    if (!func_clean_url_validate_resource_id($resource_id)) {
        return NULL;
    }

    $existing_clean_url = func_clean_url_get_raw_resource_url($resource_type, $resource_id);

    if (empty($existing_clean_url)) {
        // Prevent attempts to update nonexisting clean urls.
        return false;
    }

    $record = array(
        'clean_url' => func_addslashes($new_clean_url),
        'mtime'     => XC_TIME - $config['Appearance']['timezone_offset']
    );

    $update_result = func_array2update(
        'clean_urls',
        $record,
        'resource_type = \'' . func_addslashes($resource_type) . '\' AND resource_id = \'' . func_addslashes($resource_id) . '\''
    );

    if ($update_result == false) {
        return false;
    }

    if (
        $move_to_history
        && $existing_clean_url != $new_clean_url
        && !func_clean_url_history_add($existing_clean_url, $resource_type, $resource_id)
    ) {
        return false;
    }

    return true;
}

function func_clean_url_history_has_record($clean_url, $resource_type, $resource_id)
{
    global $sql_tbl;

    if (!func_clean_url_check_format($clean_url)) {
        return NULL;
    }

    if (!func_clean_url_validate_resource_type($resource_type)) {
        return NULL;
    }

    if (!func_clean_url_validate_resource_id($resource_id)) {
        return NULL;
    }

    $clean_url = func_query_first_cell("SELECT COUNT(*) FROM " . $sql_tbl['clean_urls_history'] . " WHERE clean_url='" . func_addslashes($clean_url) . "' AND resource_type = '" . func_addslashes($resource_type) . "' AND resource_id = '" . func_addslashes($resource_id) . "'");

    return $clean_url;
}

function func_clean_url_history_add($clean_url, $resource_type, $resource_id)
{
    global $sql_tbl, $config;

    if (!func_clean_url_check_format($clean_url)) {
        return NULL;
    }

    if (!func_clean_url_validate_resource_type($resource_type)) {
        return NULL;
    }

    if (!func_clean_url_validate_resource_id($resource_id)) {
        return NULL;
    }

    $record = array(
        'clean_url'     => func_addslashes($clean_url),
        'resource_type' => func_addslashes($resource_type),
        'resource_id'     => func_addslashes($resource_id),
        'mtime'         => XC_TIME - $config['Appearance']['timezone_offset']
    );

    $insert_result = func_array2insert(
        'clean_urls_history',
        $record,
        true
    );

    if ($insert_result == false) {
        return false;
    }

    if ($config['SEO']['clean_urls_history_limit'] > 0) {

        $clean_urls_history = func_query_column("SELECT id FROM " . $sql_tbl['clean_urls_history'] . " WHERE resource_type = '" . func_addslashes($resource_type) . "' AND resource_id = '" . func_addslashes($resource_id) . "' ORDER BY mtime DESC");

        if (count($clean_urls_history) > $config['SEO']['clean_urls_history_limit']) {

            $deleted_items = array_slice($clean_urls_history, $config['SEO']['clean_urls_history_limit']);

            if (!func_clean_url_history_delete($deleted_items)) {
                return false;
            }

        }

    }

    return true;
}

function func_clean_url_history_delete($deleted_items)
{
    global $sql_tbl;

    if (empty($deleted_items) || !is_array($deleted_items)) {
        return false;
    }

    $deleted_items = func_addslashes($deleted_items);

    db_query("DELETE FROM " . $sql_tbl['clean_urls_history'] . " WHERE id IN ('" . implode("', '", $deleted_items) . "')");

    if (db_affected_rows() != count($deleted_items)) {
        return false;
    }

    return true;
}

function func_clean_url_lookup_resource($clean_url, $aux_select_fields = NULL, $aux_select_condition = '')
{
    global $sql_tbl;

    if (!func_clean_url_check_format($clean_url)) {
        return NULL;
    }

    if (!is_string($aux_select_condition)) {
        return NULL;
    }

    $select_fields = array('resource_type', 'resource_id');

    if (
        $aux_select_fields
        && is_array($aux_select_fields)
    ) {
        $select_fields = array_unique(array_merge($select_fields, $aux_select_fields));
    }

    $clean_url_data = func_query_first("SELECT " . implode(", ", $select_fields) . " FROM " . $sql_tbl['clean_urls'] . " WHERE clean_url = '" . func_addslashes($clean_url) . "'" . $aux_select_condition);

    return $clean_url_data;
}

function func_clean_url_history_lookup_resource($clean_url, $aux_select_fields = NULL, $aux_select_condition = '')
{
    global $sql_tbl;

    if (!func_clean_url_check_format($clean_url)) {
        return NULL;
    }

    if (!is_string($aux_select_condition)) {
        return NULL;
    }

    $select_fields = array('resource_type', 'resource_id');

    if (
        $aux_select_fields
        && is_array($aux_select_fields)
    ) {
        $select_fields = array_unique(array_merge($select_fields, $aux_select_fields));
    }

    $clean_url_data = func_query_first("SELECT " . implode(", ", $select_fields) . " FROM " . $sql_tbl['clean_urls_history'] . " WHERE clean_url = '" . func_addslashes($clean_url) . "'" . $aux_select_condition." ORDER BY mtime DESC");

    return $clean_url_data;
}

function func_clean_url_permanent_redirect($resource_type, $resource_id)
{
    global $config, $REQUEST_METHOD, $QUERY_STRING, $XCART_SESSION_NAME;

    if (
        $REQUEST_METHOD == 'POST'
        || $config['SEO']['clean_urls_enabled'] != 'Y'
        || $config['SEO']['clean_urls_redirect_php'] != 'Y'
    ) {
        return false;
    }

    if (!func_clean_url_validate_resource_type($resource_type)) {
        return NULL;
    }

    if (!func_clean_url_validate_resource_id($resource_id)) {
        return NULL;
    }

    $url = func_clean_url_get($resource_type, $resource_id);

    if (zerolen($url)) {
        return false;
    }

    $qs = func_qs_remove(
        $QUERY_STRING,
        'sl',
        $XCART_SESSION_NAME,
        'redirect',
        'is_https_redirect',
        'cat',
        'productid',
        'manufacturerid',
        'pageid'
    );

    if (!empty($qs))
        $url .= '?' . $qs;

    func_header_location($url, true, 301);
}

function func_clean_url_autogenerate($resource_type, $resource_id, $params)
{
    if (!func_clean_url_validate_resource_type($resource_type)) {
        return NULL;
    }

    if (!func_clean_url_validate_resource_id($resource_id)) {
        return NULL;
    }

    $generated_url = NULL;

    $generate_functions = array(
        'C' => 'func_clean_url_autogen_cat_url',
        'P'    => 'func_clean_url_autogen_prod_url',
        'M'    => 'func_clean_url_autogen_manuf_url',
        'S'    => 'func_clean_url_autogen_page_url',
    );

    if (isset($generate_functions[$resource_type])) {

        $generated_url = $generate_functions[$resource_type]($resource_id, $params);

    }

    return $generated_url;
}

/**
 * Construct Clean URL using original Clean URL and suffix with string length normalization.
 */
function func_construct_clean_url($url, $suffix = '')
{
    $max_len = 250;

    if (strlen($url.$suffix) <= $max_len)
        return $url.$suffix;

    $url = substr($url, 0, $max_len);

    return substr($url, 0, strlen($url) - strlen($suffix)) . $suffix;
}

function func_clean_url_autogen_prod_url($productid, $params)
{
    $productid = intval($productid);

    if (isset($params['product']) && !zerolen($params['product'])) {

        $url = func_construct_clean_url(func_clean_url_cleanup_string($params['product']));

        if (!func_clean_url_has_url($url, array('resource_type' => 'P', 'resource_id' => $productid))) {

            return $url;

        }

    }

    $productcode = NULL;

    if (isset($params['productcode']) && !zerolen($params['productcode'])) {

        $productcode = $params['productcode'];

        $url = func_construct_clean_url($url, (empty($url) ? '' : '-') . $productcode);

        if (!func_clean_url_has_url($url, array('resource_type' => 'P', 'resource_id' => $productid))) {

            return $url;

        }

    }

    $url = func_construct_clean_url($url, (empty($url) ? '' : '-pr-') . intval($productid));

    if (!func_clean_url_has_url($url, array('resource_type' => 'P', 'resource_id' => $productid))) {

        return $url;

    }

    x_load('product');

    $max_attempts = 100;

    do {

        $sku = func_generate_sku((isset($params['provider']) ? $params['provider'] : ''), isset($productcode) ? substr($productcode, 0, 26) : '');

        $try_url = func_construct_clean_url($url, (empty($url) ? '' : $url.'-') . $sku);

        if (!func_clean_url_has_url($url, array('resource_type' => 'P', 'resource_id' => $productid))) {

            return $try_url;

        }

    } while ($max_attempts-- > 0);

    return NULL;
}

function func_clean_url_autogen_cat_url($categoryid, $params)
{
    $categoryid = intval($categoryid);

    if (isset($params['category']) && !zerolen($params['category'])) {

        $url = func_construct_clean_url(func_clean_url_cleanup_string($params['category']));

        if (!func_clean_url_has_url($url, array('resource_type' => 'C', 'resource_id' => $categoryid))) {

            return $url;

        }

    }

    $url = func_construct_clean_url($url, (empty($url) ? '' : '-c-') . intval($categoryid));

    if (!func_clean_url_has_url($url, array('resource_type' => 'C', 'resource_id' => $categoryid))) {

        return $url;

    }

    $max_attempts = 100;

    do {

        $uniqid = substr(md5(uniqid('category', true)), 0, 6);

        $try_url = func_construct_clean_url($try_url, (empty($url) ? '' : $url.'-') . $uniqid);

        if (!func_clean_url_has_url($url, array('resource_type' => 'C', 'resource_id' => $categoryid))) {

            return $try_url;

        }

    } while ($max_attempts-- > 0);

    return NULL;
}

function func_clean_url_autogen_manuf_url($manufacturerid, $params)
{
    $manufacturerid = intval($manufacturerid);

    if (isset($params['manufacturer']) && !zerolen($params['manufacturer'])) {

        $url = func_construct_clean_url(func_clean_url_cleanup_string($params['manufacturer']));

        if (!func_clean_url_has_url($url, array('resource_type' => 'M', 'resource_id' => $manufacturerid))) {

            return $url;

        }

    }

    $url = func_construct_clean_url($url, (empty($url) ? '' : '-m-') . intval($manufacturerid));

    if (!func_clean_url_has_url($url, array('resource_type' => 'M', 'resource_id' => $manufacturerid))) {

        return $url;

    }

    $max_attempts = 100;

    do {

        $uniqid = substr(md5(uniqid('manufacturer', true)), 0, 6);

        $try_url = func_construct_clean_url($try_url, (empty($url) ? '' : $url.'-') . $uniqid);

        if (!func_clean_url_has_url($url, array('resource_type' => 'M', 'resource_id' => $manufacturerid))) {

            return $try_url;

        }

    } while ($max_attempts-- > 0);

    return NULL;
}

function func_clean_url_autogen_page_url($pageid, $params)
{
    global $sql_tbl;

    $pageid = intval($pageid);

    // Check if Clean URL was already generated for this page on other language

    if (isset($params['filename']) && !zerolen($params['filename'])) {

        $count = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[clean_urls], $sql_tbl[pages] WHERE $sql_tbl[clean_urls].resource_id = $sql_tbl[pages].pageid AND $sql_tbl[pages].filename = '".func_addslashes($params['filename'])."' AND $sql_tbl[pages].pageid != '$pageid'");

        if ($count > 0)
            return -1;

    }

    if (isset($params['title']) && !zerolen($params['title'])) {

        $url = func_construct_clean_url(func_clean_url_cleanup_string($params['title']));

        if (!func_clean_url_has_url($url, array('resource_type' => 'S', 'resource_id' => $pageid))) {

            return $url;

        }

    }

    $url = func_construct_clean_url($url, (empty($url) ? '' : '-sp-') . intval($pageid));

    if (!func_clean_url_has_url($url, array('resource_type' => 'S', 'resource_id' => $pageid))) {

        return $url;

    }

    $max_attempts = 100;

    do {

        $uniqid = substr(md5(uniqid('page', true)), 0, 6);

        $try_url = func_construct_clean_url($try_url, (empty($url) ? '' : $url.'-') . $uniqid);

        if (!func_clean_url_has_url($url, array('resource_type' => 'S', 'resource_id' => $pageid))) {

            return $try_url;

        }

    } while ($max_attempts-- > 0);

    return NULL;
}

function func_clean_url_get_missing_urls_stats()
{
    global $sql_tbl;

    $resources_data = func_clean_url_get_resources_data();

    $report = array();

    foreach ($resources_data as $resource_type => $resource) {

        $missing_count = func_query_first_cell("SELECT COUNT(*) FROM " . $resource['resource_table'] . " LEFT JOIN " . $sql_tbl['clean_urls'] . " ON " . $sql_tbl['clean_urls'] . ".resource_type = '" . $resource_type . "' AND " . $sql_tbl['clean_urls'] . ".resource_id = " . $resource['resource_table'] . "." . $resource['resource_id_column'] . " WHERE " . $sql_tbl['clean_urls'] . ".resource_id IS NULL");

        if ($missing_count > 0) {

            $report[$resource_type]['total_count']         = func_query_first_cell('SELECT COUNT(*) FROM ' . $resource['resource_table']);
            $report[$resource_type]['missing_count']     = $missing_count;
            $report[$resource_type]['resource_name']     = $resource['resource_name'];

        }

    }

    return $report;
}

function func_clean_url_add_generate_log(&$fp, $resource_type, $resource_id, $params)
{
    global $sql_tbl, $xcart_catalogs;

    static $types = array();

    if (!is_resource($fp))
        return false;

    $data = func_clean_url_get_resources_data($resource_type);

    if (!isset($data['err_type_title']) || !isset($data['err_msg']))
        return false;

    if (!isset($types[$resource_type])) {

        if (count($types) == 0) {

            if (@!fwrite($fp, "\n*** " . func_get_langvar_by_name($data['err_type_title'], false, false, true) . "\n\n"))

                return false;

        }

    }

    $params['id'] = $resource_id;

    $params['admin_location'] = $xcart_catalogs['admin'];

    if (@!fwrite($fp, func_get_langvar_by_name($data['err_msg'], $params, false, true) . "\n"))
        return false;

    return true;
}

function func_is_internal_url($url)
{
    global $config, $http_location, $https_location;

    if (!is_url($url))
        return false;

    $res = @parse_url($url);

    if (!$res)
        return false;

    if ($config['SEO']['clean_urls_enabled'] == 'Y' && func_clean_url_has_url(preg_replace('/^.+([^\/]+)$/is', '\1', $res['path'])))
        return true;

    $location = ($res['scheme'] ? $res['scheme'] : "http") . "://" . $res['host'] . $res['path'];

    if (strncmp($location, $http_location, strlen($http_location)) == 0 || strncmp($location, $https_location, strlen($https_location)) == 0)
        return true;

    return false;
}

function func_get_resource_url($resource_type, $resource_id, $query_string = '', $absolute_path = true)
{
    global $config, $xcart_catalogs;

    if (!func_clean_url_validate_resource_id($resource_id)) {
        return NULL;
    }

    switch($resource_type) {
    case 'P':
    case 'product':
        $php_page = "product.php?productid=" . $resource_id;
        $clean_url_resource_type = 'P';
        break;

    case 'C':
    case 'category':
        $php_page = "home.php?cat=" . $resource_id;
        $clean_url_resource_type = 'C';
        break;

    case 'M':
    case 'manufacturer':
        $php_page = "manufacturers.php?manufacturerid=" . $resource_id;
        $clean_url_resource_type = 'M';
        break;

    case 'S':
    case 'static_page':
        $php_page = "pages.php?pageid=" . $resource_id;
        $clean_url_resource_type = 'S';
        break;

    default:
        return NULL;
    }

    if (!func_clean_url_validate_resource_type($clean_url_resource_type)) {
        return NULL;
    }

    if ($config['SEO']['clean_urls_enabled'] != 'Y') {

        $postfix = $php_page;

    } else {

        $clean_url = func_clean_url_get($clean_url_resource_type, $resource_id, false);

        $postfix = !zerolen($clean_url) ? $clean_url : $php_page;

    }

    $url = ($absolute_path == true ? $xcart_catalogs['customer'] . '/' : '') . $postfix;

    if (!zerolen($query_string)) {

        $url .= ($postfix == $php_page ? '&' : '?') . $query_string;

    }

    return $url;
}
?>
