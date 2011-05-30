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
 * Module functions
 *
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @category   X-Cart
 * @package    Modules
 * @subpackage XML Sitemap
 * @version    $Id: func.php,v 1.13.2.2 2011/01/10 13:12:04 ferz Exp $
 * @since      4.4.0
 */

if (!defined('XCART_START')) { header('Location: ../../'); die('Access denied'); }

/**
 * Generate xml sitemap
 *
 * @return string|void Error text if any
 */
function xmlmap_generate()
{
    func_display_service_header();

    global $config, $xcart_dir;

    // Define absolute path to the xml sitemap file
    $filename = $xcart_dir . '/' . $config['XML_Sitemap']['filename'];

    // If file is not exists, create it
    if (!file_exists($filename)) {

        $handle = fopen($filename, 'a');

        fclose($handle);

        func_flush(func_get_langvar_by_name('xmlmap_log_filecreated', null, false, true));

    } else {

        func_flush(func_get_langvar_by_name('xmlmap_log_fileexists', null, false, true));

    }

    // If file is not writable, fail with error
    if (!is_writable($filename)) {

        func_flush(func_get_langvar_by_name('xmlmap_log_filenotwritable', null, false, true));

        return func_get_langvar_by_name('xmlmap_error_filenotwritable');

    } else {

        func_flush(func_get_langvar_by_name('xmlmap_log_generationstart', null, false, true));

    }

    $prepared_items = array();

    foreach ($config['XML_Sitemap']['items'] as $spec) {

        $items_function = $spec['items_function'];
        
        if (function_exists($items_function)) {
            
            func_flush(func_get_langvar_by_name('xmlmap_log_type' . $spec['type'], null, false, true));
            func_flush(func_get_langvar_by_name('xmlmap_log_itemsquery', null, false, true));
            
            $items = $items_function();
            
        } else {
            
            continue;
            
        }
        
        if (!empty($items)) {

            func_flush(func_get_langvar_by_name('xmlmap_log_itemsfound', array('count' => count((array) $items)), false, true));
            func_flush(func_get_langvar_by_name('xmlmap_log_itemsprepare', null, false, true));

            array_walk($items, 'xmlmap_assign_properties', array($spec['type'], $spec['properties']));

        } else {

            func_flush(func_get_langvar_by_name('xmlmap_log_itemsfound', array('count' => 0), false, true));
            func_flush(func_get_langvar_by_name('xmlmap_log_gotonext', null, false, true));

            continue;

        }

        if (!empty($items)) {

            func_flush(func_get_langvar_by_name('xmlmap_log_itemsmerge', null, false, true));

            $prepared_items = array_merge($prepared_items, $items);

        } else {

            func_flush(func_get_langvar_by_name('xmlmap_log_itemsprepareno', null, false, true));
            func_flush(func_get_langvar_by_name('xmlmap_log_gotonext', null, false, true));

            continue;

        }

    }

    if (!empty($prepared_items)) {

        func_flush(func_get_langvar_by_name('xmlmap_log_generatexml', null, false, true));

        global $smarty;

        // pass collected items to smarty where they will be formated for xml file
        $smarty->assign('xmlmap_items', $prepared_items);

        $src = func_display('modules/XML_Sitemap/sitemap.tpl', $smarty, false);

        $smarty->clear_assign('xmlmap_items');

        func_flush(func_get_langvar_by_name('xmlmap_log_writexml', null, false, true));

        // Write collected data to file
        $handle = fopen($filename, 'w');

        fwrite($handle, $src);

        fclose($handle);

        func_flush(func_get_langvar_by_name('xmlmap_log_generationend', null, false, true));

    } else {

        func_flush(func_get_langvar_by_name('xmlmap_log_generateno', null, false, true));

        return func_get_langvar_by_name('xmlmap_error_generateno');

    }

}

/**
 * Assign specific xml properties to passed items
 * Callback function
 *
 * @param $item   array array(id, lastmod, url)
 * @param $key    int
 * @param $params array array(item type, item params)
 */
function xmlmap_assign_properties(&$item, $key, $params)
{
    $type       = $params[0];
    $properties = $params[1];

    if (is_array($properties)) {
        
        foreach ($properties as $name => $property) {
            if (!empty($property) && (!isset($item[$name]) || empty($item[$name]))) {
                $item[$name] = $property;
            }
        }
        
    }

    if (!isset($item['loc']) || empty($item['loc'])) {
        $item['loc'] = xmlmap_get_url($type, $item['id']);
    }
    
    // Convert all unallowed characters to HTML entities
    $item['loc'] = htmlentities($item['loc'], ENT_QUOTES, 'UTF-8');

    return $item;
}

/**
 * Get ids of all avaliable categories
 *
 * @return array
 */
function xmlmap_get_categories()
{
    global $sql_tbl;
    
    $query = "SELECT $sql_tbl[categories].categoryid as id, $sql_tbl[xmlmap_lastmod].date as lastmod FROM $sql_tbl[categories] LEFT JOIN $sql_tbl[xmlmap_lastmod] ON $sql_tbl[xmlmap_lastmod].id = $sql_tbl[categories].categoryid AND $sql_tbl[xmlmap_lastmod].type = 'C' WHERE $sql_tbl[categories].avail='Y'";
    $items = func_query($query);

    return $items;
}

/**
 * Get ids of all avaliable products
 *
 * @return array
 */
function xmlmap_get_products()
{
    global $sql_tbl, $config;
    
    $query = "SELECT $sql_tbl[products].productid as id, $sql_tbl[xmlmap_lastmod].date as lastmod FROM $sql_tbl[products] LEFT JOIN $sql_tbl[xmlmap_lastmod] ON $sql_tbl[xmlmap_lastmod].id = $sql_tbl[products].productid AND $sql_tbl[xmlmap_lastmod].type = 'P' WHERE $sql_tbl[products].forsale='Y'" . ($config['General']['unlimited_products'] == 'N' ? " AND $sql_tbl[products].avail > 0" : '');
    $items = func_query($query);

    return $items;
}

/**
 * Get ids of all avaliable manufacturers
 *
 * @return array
 */
function xmlmap_get_manufacturers()
{
    global $sql_tbl;
    
    $query = "SELECT $sql_tbl[manufacturers].manufacturerid as id, $sql_tbl[xmlmap_lastmod].date as lastmod FROM $sql_tbl[manufacturers] LEFT JOIN $sql_tbl[xmlmap_lastmod] ON $sql_tbl[xmlmap_lastmod].id = $sql_tbl[manufacturers].manufacturerid AND $sql_tbl[xmlmap_lastmod].type = 'M' WHERE $sql_tbl[manufacturers].avail='Y'";
    $items = func_query($query);

    return $items;
}

/**
 * Get ids of all avaliable static pages
 *
 * @return array
 */
function xmlmap_get_pages()
{
    global $sql_tbl;
    
    $query = "SELECT $sql_tbl[pages].pageid as id, $sql_tbl[xmlmap_lastmod].date as lastmod FROM $sql_tbl[pages] LEFT JOIN $sql_tbl[xmlmap_lastmod] ON $sql_tbl[xmlmap_lastmod].id = $sql_tbl[pages].pageid AND $sql_tbl[xmlmap_lastmod].type = 'S' WHERE $sql_tbl[pages].active='Y' AND $sql_tbl[pages].level='E'";
    $items = func_query($query);

    return $items;
}

/**
 * Get ids of all avaliable extra pages
 *
 * @return array
 */
function xmlmap_get_extra()
{
    global $sql_tbl;
    
    $query = "SELECT $sql_tbl[xmlmap_extra].id as id, $sql_tbl[xmlmap_extra].url as loc FROM $sql_tbl[xmlmap_extra] WHERE $sql_tbl[xmlmap_extra].active = 'Y'";
    $items = func_query($query);

    return $items;
}

/**
 * Get home page url
 *
 * @return array
 */
function xmlmap_get_home()
{
    global $config;
    
    $items[] = array('id' => 0);

    return $items;
}

/**
 * Add current date to db for provided item
 *
 * @param  string $type item type (C|P|M|S)
 * @param  int    $id   item id
 * @return void
 */
function xmlmap_update_lastmod($type, $id)
{
    global $sql_tbl;
    $id = intval($id);
    $result = db_query("REPLACE INTO $sql_tbl[xmlmap_lastmod] (id, type, date) VALUES ($id, '$type', CONCAT(CURDATE(), 'T', CURTIME(), '+00:00'))");
    db_free_result($result);
}

/**
 * Remove lastmod entry from db for deleting items
 *
 * @return void
 */
function xmlmap_delete_lastmod()
{
    global $sql_tbl;

    x_load('category');

    // Category is deleting
    if (
        isset($_POST['confirmed'])
        && $_POST['confirmed'] == 'Y'
        && isset($_POST['cat'])
    ) {
        $cat = intval($_POST['cat']);

        $root_cat = func_category_get_position($cat);

        $ids = func_query_column("SELECT categoryid FROM $sql_tbl[categories] WHERE lpos BETWEEN " . $root_cat['lpos'] . ' AND ' .  $root_cat['rpos']);

        // If deleting category has products, delete entries for them too
        $products_ids = func_query_column("SELECT $sql_tbl[products_categories].productid FROM $sql_tbl[categories], $sql_tbl[products_categories] WHERE $sql_tbl[categories].lpos BETWEEN " . $root_cat['lpos'] . ' AND ' .  $root_cat['rpos'] . " AND $sql_tbl[products_categories].categoryid=$sql_tbl[categories].categoryid AND $sql_tbl[products_categories].main='Y'");

        if (is_array($products_ids)) {
            $result = db_query("DELETE FROM $sql_tbl[xmlmap_lastmod] WHERE type = 'P' AND id IN ('" . implode("','", $products_ids) . "')");
            db_free_result($result);
        }

        $result = db_query("DELETE FROM $sql_tbl[xmlmap_lastmod] WHERE type = 'C' AND id IN ('" . implode("','", $ids) . "')");

        db_free_result($result);

    // Products are deleting
    } elseif ($_POST['confirmed'] == 'Y') {

        x_session_register('products_to_delete');
        global $products_to_delete;

        if (is_array($products_to_delete['products'])) {

            $ids = array_keys($products_to_delete['products']);
            $result = db_query("DELETE FROM $sql_tbl[xmlmap_lastmod] WHERE type = 'P' AND id IN ('" . implode("','", $ids) . "')");

            db_free_result($result);

        }

    // Manufacturers are deleting
    } elseif (
        isset($_POST['to_delete'])
        && is_array($_POST['to_delete'])
    ) {

        $ids = array_keys($_POST['to_delete']);
        $result = db_query("DELETE FROM $sql_tbl[xmlmap_lastmod] WHERE type = 'M' AND id IN ('" . implode("','", $ids) . "')");

        db_free_result($result);

    // Static pages are deleting
    } elseif (
        is_array($_POST['posted_data'])
        && $_POST['sec'] == 'E'
    ) {

        $ids = array_keys($_POST['posted_data']);
        $result = db_query("DELETE FROM $sql_tbl[xmlmap_lastmod] WHERE type = 'S' AND id IN ('" . implode("','", $ids) . "')");

        db_free_result($result);
    }
}

/**
 * Add extra URL to the database
 *
 * @param  string $url
 * @return string|void
 */
function xmlmap_extra_addurl($data)
{
    if (isset($data['url']) && empty($data['url'])) {
        return func_get_langvar_by_name('err_filling_form');
    }

    $url = trim(($data['url']));

    global $http_location;

    if (strpos(strtolower($url), strtolower($http_location)) === false) {
        $url = $http_location . '/' . $url;
    }
    
    $insert = array(
        'url' => $url,
        'active' => ($data['active'] == 'Y' ? $data['active'] : 'N'),
    );
    func_array2insert('xmlmap_extra', $insert);
}

/**
 * Remove extra urls from db
 *
 * @param  array $ids
 * @return string|void
 */
function xmlmap_extra_delurls($ids)
{
    if (!is_array($ids) || empty($ids)) {

        return func_get_langvar_by_name('lbl_no_items_have_been_selected');
        
    } else {
        
        global $sql_tbl;
        db_query("DELETE FROM $sql_tbl[xmlmap_extra] WHERE id IN ('" . implode("','", $ids) . "')");

    }
}

/**
 * Update extra urls
 *
 * @param  array       $urls
 * @return string|void error text
 */
function xmlmap_extra_updateurls($urls)
{
    if (!is_array($urls)) {
        return func_get_langvar_by_name('lbl_permission_denied');
    }

    foreach ($urls as $id => $data) {
        $id = intval($id);
        if (0 > $id) {
            return func_get_langvar_by_name('lbl_permission_denied');
        }
        $update = array(
            'url' => trim(($data['url'])),
            'active' => ($data['active'] == 'Y' ? $data['active'] : 'N'),
        );
        func_array2update('xmlmap_extra', $update, "id = '$id'");
    }
}

/**
 * Get extra urls from db
 *
 * @return array
 */
function xmlmap_extra_geturls()
{
    global $sql_tbl;
    
    $query = "SELECT $sql_tbl[xmlmap_extra].* FROM $sql_tbl[xmlmap_extra]";
    $urls = func_query($query);
    
    if (!is_array($urls)) {
        $urls = array();
    }
    
    return $urls;
}

/**
 * Creates URL using avaliable processor. Currently avaliable:
 * - Clean URLs
 * - X-SEO: Friendly URLs
 * - html catalog
 * - default php ones
 *
 * @param  string $type   C|P|M|S|H
 * @param  int    $id     item id
 * @param  array  $params additional params. now only params[url] is used. if it passed, exactly this url will be returned
 * @return string
 */
function xmlmap_get_url($type, $id)
{
    global $config, $active_modules, $xseo, $http_location;

    $id = intval($id);

    $url = '';

    if ($config['XML_Sitemap']['xmlmap_use_hc'] == 'Y') {
        $url = xmlmap_get_hc_url($type, $id);
    } else if (isset($active_modules['XSEO']) && ( isset($xseo['modules']['urls']) && $xseo['modules']['urls']['active'] != false)) {
        global $shop_language;
        $url = xseo_urls_get_url($id, $type, $shop_language, true);
    } else if (isset($config['SEO']['clean_urls_enabled'])) {
        $url = func_get_resource_url($type, $id, '', false);
    }

    if (empty($url)) {
        switch ($type) {
            case 'C':
                $url = 'home.php?cat=' . $id;
                break;
            case 'P':
                $url = 'product.php?productid=' . $id;
                break;
            case 'M':
                $url = 'manufacturers.php?manufacturerid=' . $id;
                break;
            case 'S':
                $url = 'pages.php?pageid=' . $id;
                break;
            case 'H':
                $url = ($config['XML_Sitemap']['xmlmap_useroot'] == 'Y') ? '/' : 'home.php';
                break;
            default:
                $url = '';
                break;
        }
    }

    if (empty($url)) {
        $url = 'home.php';
    }
    
    $url = $http_location . constant('DIR_CUSTOMER') . '/' . rtrim($url, '/');

    return $url;
}

/**
 * Generate URL for item in html catalog
 *
 * @param $type  string P|C|M|S|H
 * @param $id    int    item id
 * @param $name  string item name (if empty, will be get from db)
 * @param $extra array  additional params array(page, sort_direction, sort_field)
 * @param $lng   string force language code
 */
function xmlmap_get_hc_url($type, $id, $name = '', $extra = array(), $lng = '')
{
    x_load('html_catalog');
    
    global $sql_tbl, $config, $max_name_length, $template_max_length, $current_lng, $store_language, $shop_language, $templates_data, $name_delimiters, $hc_state;
    
    $max_name_length = 64;
    $template_max_length = 200;
    
    if (!empty($lng)) {
        $current_lng = $lng;
    } else if (!empty($store_language)) {
        $current_lng = $store_language;
    } else if (!empty($shop_language)) {
        $current_lng = $shop_language;
    } else {
        $current_lng = $config['default_customer_language'];
    }
    
    $templates_data = array("category" => array("default_template" => "{catname}-{order}-p-{page}-c-{catid}", "keywords" => array("catname" => array("required" => false, "datafield" => "category"), "catid" => array("required" => true, "datafield" => "categoryid"), "order" => array("required" => true, "datafield" => "order"), "page" => array("required" => true, "datafield" => "page"))), "product" => array("default_template" => "{prodname}-p-{productid}", "keywords" => array("prodname" => array("required" => false, "datafield" => "product"), "productid" => array("required" => true, "datafield" => "productid"))), "manufacturer" => array("default_template" => "{manufname}-{order}-p-{page}-mf-{manufid}", "keywords" => array("manufname" => array("required" => false, "datafield" => "manufacturer"), "manufid" => array("required" => true, "datafield" => "manufacturerid"), "order" => array("required" => true, "datafield" => "order"), "page" => array("required" => true, "datafield" => "page"))), "staticpage" => array("default_template" => "{pagename}-sp-{pageid}", 'keywords' => array('pagename' => array('required' => false, 'datafield' => 'page'), 'pageid' => array('required' => true, 'datafield' => 'pageid'))));
    $name_delimiters = array('-' => func_get_langvar_by_name('lbl_name_delimiter_hyphen', false, false, true), '_' => func_get_langvar_by_name('lbl_name_delimiter_underscore', false, false, true));
    $hc_state = array('templates' => func_get_saved_page_name_templates(), 'name_delim' => func_get_saved_name_delim());
    
    $id = intval($id);
    
    $types = array('C', 'P', 'M', 'S', 'H');
    if (!in_array($type, $types)) {
        return false;
    }
    
    switch ($type) {
        case 'C':
            $extra['page'] = isset($extra['page']) ? $extra['page'] : 1;
            $extra['sort_field'] = isset($extra['sort_field']) ? $extra['sort_field'] : $config['Appearance']['products_order'];
            $extra['sort_direcion'] = isset($extra['sort_direcion']) ? $extra['sort_direcion'] : 0;
            $url = category_filename($id, $name, $extra['page'], $extra['sort_field'], $extra['sort_direcion']);
            break;
        case 'P':
            $url = product_filename($id, $name);
            break;
        case 'M':
            $extra['page'] = isset($extra['page']) ? $extra['page'] : 1;
            $extra['sort_field'] = isset($extra['sort_field']) ? $extra['sort_field'] : $config['Appearance']['products_order'];
            $extra['sort_direcion'] = isset($extra['sort_direcion']) ? $extra['sort_direcion'] : 0;
            $url = manufacturer_filename($id, $name, $extra['page'], $extra['sort_field'], $extra['sort_direcion']);
            break;
        case 'S':
            $url = staticpage_filename($id, $name);
            if ($url == 'index.html') {
                return false;
            }
            break;
        case 'H':
            $url = category_filename('', '', 1, $config['Appearance']['products_order'], 0);
            break;
    }
    
    unset($GLOBALS['max_name_length'], $GLOBALS['template_max_length'], $GLOBALS['current_lng'], $GLOBALS['templates_data'], $GLOBALS['name_delimiters'], $GLOBALS['hc_state']);
    
    $dirs = unserialize($config['html_catalog_dirs']);
    $dir = $dirs[$current_lng];
    $dir = str_replace('\\', '/', $dir);
    $dir = trim($dir, '/');

    if ($dir) {
        $url = $dir . '/' . $url;
    }

    return $url;
}
?>
