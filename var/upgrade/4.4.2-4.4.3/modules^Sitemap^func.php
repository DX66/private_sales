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
 * @subpackage Sitemap
 * @version    $Id: func.php,v 1.8.2.1 2011/01/10 13:12:02 ferz Exp $
 * @since      4.4.0
 */
if (!defined('XCART_START')) { header('Location: ../../'); die('Access denied');}

/**
 * Collect all items for the sitemap
 *
 * @param  string $code
 * @return array
 */
function sitemap_get_items($code)
{
    global $config;

    if ($config['Sitemap']['sitemap_use_cache'] == 'Y') {
        global $var_dirs;
        $filename = sitemap_get_cache_filename($code);
        if (file_exists($filename) && is_readable($filename)) {
            return file_get_contents($filename);
        }
    }

    $items = array();
    if (is_array($config['Sitemap']['items'])) {
        foreach ($config['Sitemap']['items'] as $item) {
            $function_name = 'sitemap_get_' . $item;
            if (sitemap_is_item_avaliable($item) && function_exists($function_name)) {
                $items[$item] = $function_name(null, null, $code);
            }
        }
    }
    return $items;
}

//
// Categories related functions
//

/**
 * Get categories array for sitemap
 *
 * @param  int    $start
 * @param  int    $limit
 * @param  string $code
 * @return array
 */
function sitemap_get_categories($start = null, $limit = null, $code = null)
{
    global $config;
    
    $items = sitemap_get_categories_recurs(0, $start, $limit, $code);
    
    return sitemap_define_urls($items, 'C');
}

/**
 * Get total count of categories
 *
 * @return int
 */
function sitemap_get_categories_total()
{
    global $config;

    $count = 0;

    if ($config['Sitemap']['sitemap_display_categories'] == 'Y') {
        $code = $GLOBALS['config']['default_customer_language'];
        $query = sitemap_get_categories_query(true, $code, null, null, true);
        $count = intval(func_query_first_cell($query));
    }

    return $count;
}

/**
 * Recursevly build categories chain
 *
 * @param  int    $parentid
 * @param  int    $start
 * @param  int    $limit
 * @param  string $code
 * @return array
 */
function sitemap_get_categories_recurs($parentid, $start, $limit, $code)
{
    global $sql_tbl, $config;

    static $level = 0;
    $level++;

    if ($parentid != 0) {
        $start = $limit = null;
    }
    
    $query = sitemap_get_categories_query(false, $code, $start, $limit, false, $parentid);

    $result = db_query($query);

    if (db_num_rows($result) > 0) {
        while ($row = db_fetch_array($result)) {
            $row['subs'] = sitemap_get_categories_recurs($row['id'], $start, $limit, $code);
            $row['subs'] = sitemap_define_urls($row['subs'], 'C');
            $row['products'] = sitemap_get_products_categories($row['id']);
            $items[] = $row;
            $level--;
        }
        return $items;
    } else {
        return false;
    }
}

/**
 * Create a sql query to fetch categories
 *
 * @param  bool   $count
 * @param  string $code
 * @param  int    $start
 * @param  int    $end
 * @param  bool   $root_only
 * @param  int    $parentid
 * @return string
 */
function sitemap_get_categories_query($count = false, $code = null, $start = null, $end = null, $root_only = false, $parentid = 0)
{
    global $sql_tbl, $config;

    $select[] = "$sql_tbl[categories].categoryid AS id";
    
    if ($root_only == false) {
        $select[] = "$sql_tbl[categories].parentid";
    }

    if ($config['Sitemap']['sitemap_multilang'] == 'Y') {
        $code = $GLOBALS['shop_language'];
        $select[] = "IF ($sql_tbl[categories_lng].categoryid IS NOT NULL AND $sql_tbl[categories_lng].category != '', $sql_tbl[categories_lng].category, $sql_tbl[categories].category) AS name";
        $l_join[] = "$sql_tbl[categories_lng] USE INDEX (PRIMARY) ON $sql_tbl[categories_lng].code = '$code' AND $sql_tbl[categories_lng].categoryid = $sql_tbl[categories].categoryid";
    } else {
        $select[] = "$sql_tbl[categories].category AS name";
    }

    $from[] = "$sql_tbl[categories]";

    if ($config['Sitemap']['sitemap_membership'] == 'Y') {
        global $user_account;
        $l_join[] = "$sql_tbl[category_memberships] ON $sql_tbl[category_memberships].categoryid = $sql_tbl[categories].categoryid";
        $where[] = "($sql_tbl[category_memberships].membershipid IS NULL OR $sql_tbl[category_memberships].membershipid = '$user_account[membershipid]')";
    }

    $where[] = "$sql_tbl[categories].parentid = $parentid";
        
    $where[] = "$sql_tbl[categories].avail = 'Y'";

    if ($config['Sitemap']['sitemap_categories_order'] == 'ASC') {
        $order_by[] = 'name';
    } else {
        $order_by[] = "$sql_tbl[categories].order_by";
        $order_by[] = 'name';
    }

    if ($start) {
        $limit[] = $start;
    }

    if ($end) {
        $limit[] = $end;
    }
    
    if ($count != false) {
        $select   = array("COUNT($sql_tbl[categories].categoryid)");
        $order_by = null;
    }
    
    return sitemap_build_query($select, $from, $where, $order_by, $l_join, $limit);
}

//
// Products related functions
//

/**
 * Get products array for sitemap
 *
 * @param  int    $start
 * @param  int    $limit
 * @param  string $code
 * @return array
 */
function sitemap_get_products($start = null, $limit = null, $code = null)
{
    global $config;
    
    $query = sitemap_get_products_query(false, $code, $start, $limit);
    $items = func_query($query);
    
    return sitemap_define_urls($items, 'P');
}

/**
 * Get total count of products
 *
 * @return int
 */
function sitemap_get_products_total()
{
    global $config;

    $count = 0;

    if ($config['Sitemap']['sitemap_display_products'] == 'Y') {
        $code = $GLOBALS['config']['default_customer_language'];
        $query = sitemap_get_products_query(true, $code);
        $count = intval(func_query_first_cell($query));
    }

    return $count;
}

/**
 * Get all products assigned for the specified category
 *
 * @param  int   $categoryid
 * @return array
 */
function sitemap_get_products_categories($categoryid)
{
    global $config;
    
    if ($config['Sitemap']['sitemap_display_products_categor'] == 'Y') {
        $categoryid = intval($categoryid);
        $query = sitemap_get_products_query(false, null, null, null, 'categories', $categoryid);
        $items = func_query($query);
        if ($items != false) {
            return sitemap_define_urls($items, 'P');
        }
    }
    
    return false;
}

/**
 * Get all products assigned for the specified manufacturer
 * used as callback function for sitemap_get_manufacturers
 *
 * @param  array $manufacturer
 * @return array
 */
function sitemap_get_products_manufacturers($manufacturer)
{
    $id = intval($manufacturer['id']);
    $query = sitemap_get_products_query(false, null, null, null, 'manufacturers', $id);
    $items = func_query($query);
    
    if ($items != false) {
        $items = sitemap_define_urls($items, 'P');
        $manufacturer['products'] = $items;
    }
    
    return $manufacturer;
}

/**
 * Create a sql query to fetch products
 *
 * @param  bool   $count
 * @param  string $code
 * @param  int    $start
 * @param  int    $end
 * @param  bool   $for
 * @param  int    $id
 * @return string
 */
function sitemap_get_products_query($count = false, $code = null, $start = null, $end = null, $for = null, $id = null)
{
    global $sql_tbl, $config;

    $select[] = "$sql_tbl[products].productid AS id";

    if ($config['Sitemap']['sitemap_multilang'] == 'Y') {
        $code     = isset($code) ? $code : $GLOBALS['shop_language'];
        $select[] = "IF ($sql_tbl[products_lng].productid IS NOT NULL AND $sql_tbl[products_lng].product != '', $sql_tbl[products_lng].product, $sql_tbl[products].product) AS name";
        $l_join[]   = "$sql_tbl[products_lng] ON $sql_tbl[products_lng].code = '$code' AND $sql_tbl[products_lng].productid = $sql_tbl[products].productid";
    } else {
        $select[] = "$sql_tbl[products].product AS name";
    }

    if ($config['Sitemap']['sitemap_membership'] == 'Y' && $count != true) {
        global $user_account;
        $l_join[]  = "$sql_tbl[product_memberships] ON $sql_tbl[product_memberships].productid = $sql_tbl[products].productid";
        $where[] = "($sql_tbl[product_memberships].membershipid IS NULL OR $sql_tbl[product_memberships].membershipid = '$user_account[membershipid]')";
    }

    if ($config['General']['unlimited_products'] == 'N') {
        $where[] = "$sql_tbl[products].avail > 0";
    }

    $from[] = "$sql_tbl[products]";

    if ($config['Sitemap']['sitemap_products_order'] == 'ASC' || $for != 'category') {
        $order_by[] = 'name';
    } else {
        $order_by[] = "$sql_tbl[products_categories].orderby";
        $order_by[] = 'name';
    }

    if ($start) {
        $limit[] = $start;
    }

    if ($end) {
        $limit[] = $end;
    }

    if ($count != false) {
        $select   = array("COUNT($sql_tbl[products].productid)");
        $order_by = null;
    } else if ($for == 'manufacturers') {
        $where[] = "$sql_tbl[products].manufacturerid = $id";
    } else if ($for == 'categories') {
        $l_join[] = "$sql_tbl[products_categories] ON $sql_tbl[products].productid = $sql_tbl[products_categories].productid";
        $where[] = "$sql_tbl[products_categories].categoryid = $id";
    }

    return sitemap_build_query($select, $from, $where, $order_by, $l_join, $limit);
}

//
// Manufacturers related functions
//

/**
 * Get manufacturers array for sitemap
 *
 * @param  int   $start
 * @param  int   $limit
 * @param  sting $code
 * @return array
 */
function sitemap_get_manufacturers($start = null, $limit = null, $code = null)
{
    global $sql_tbl, $config, $active_modules;
    
    if (isset($active_modules['Manufacturers'])) {
        $query = sitemap_get_manufacturers_query(false, $code, $start, $limit);
        $items = func_query($query);
        if ($items != false) {
            $items = sitemap_define_urls($items, 'M');
            if ($config['Sitemap']['sitemap_display_products_manufac'] == 'Y') {
                $items = array_map('sitemap_get_products_manufacturers', $items);
            }
            return $items;
        }
    }
    
    return false;
}

/**
 * Get total count of manufacturers
 *
 * @return int
 */
function sitemap_get_manufacturers_total()
{
    global $config, $active_modules;

    $count = 0;

    if ($config['Sitemap']['sitemap_display_manufacturers'] == 'Y' && isset($active_modules['Manufacturers'])) {
        $code = $GLOBALS['config']['default_customer_language'];
        $query = sitemap_get_manufacturers_query(true, $code);
        $count = intval(func_query_first_cell($query));
    }

    return $count;
}

/**
 * Create a sql query to fetch manufacturers
 *
 * @param  bool   $count
 * @param  string $code
 * @param  int    $start
 * @param  int    $end
 * @return string
 */
function sitemap_get_manufacturers_query($count = false, $code = null, $start = null, $end = null)
{
    global $sql_tbl, $config;

    $select[]    = "$sql_tbl[manufacturers].manufacturerid as id";
    $from[]      = "$sql_tbl[manufacturers]";
    $where[]     = "$sql_tbl[manufacturers].avail = 'Y'";
    $order_by[]  = ($config['Sitemap']['sitemap_manufacturers_order'] == 'ASC') ? 'name' : "$sql_tbl[manufacturers].orderby, name";


    if ($config['Sitemap']['sitemap_multilang'] == 'Y') {
        $code = isset($code) ? $code : $GLOBALS['shop_language'];
        $select[] = "IFNULL($sql_tbl[manufacturers_lng].manufacturer, $sql_tbl[manufacturers].manufacturer) AS name";
        $l_join[] = "$sql_tbl[manufacturers_lng] ON $sql_tbl[manufacturers].manufacturerid = $sql_tbl[manufacturers_lng].manufacturerid AND $sql_tbl[manufacturers_lng].code = '$code'";
    } else {
        $select[] = "$sql_tbl[manufacturers].manufacturer AS name";

    }

    if ($start) {
        $limit[] = $start;
    }

    if ($end) {
        $limit[] = $end;
    }
    
    if ($count != false) {
        $select   = array("COUNT($sql_tbl[manufacturers].manufacturerid)");
        $order_by = null;
    }
  
    return sitemap_build_query($select, $from, $where, $order_by, $l_join, $limit);
}

//
// Static pages related functions
//

/**
 * Get static pages array for sitemap
 *
 * @param  int    $start
 * @param  int    $limit
 * @param  string $code
 * @return array
 */
function sitemap_get_pages($start = null, $limit = null, $code = null)
{
    global $config;
    
    $query = sitemap_get_pages_query(false, $code, $start, $limit);
    $items = func_query($query);
    
    return sitemap_define_urls($items, 'S');
}

/**
 * Get total count of static pages
 *
 * @return int
 */
function sitemap_get_pages_total()
{
    global $config;

    $count = 0;

    if ($config['Sitemap']['sitemap_display_pages'] == 'Y') {
        $code = $GLOBALS['config']['default_customer_language'];
        $query = sitemap_get_pages_query(true, $code);
        $count = intval(func_query_first_cell($query));
    }

    return $count;
}

/**
 * Create a sql query to fetch static pages
 *
 * @param  bool   $count
 * @param  string $code
 * @param  int    $start
 * @param  int    $end
 * @return string
 */
function sitemap_get_pages_query($count = false, $code = null, $start = null,  $end = null)
{
    global $sql_tbl, $config;

    $select[]   = "$sql_tbl[pages].pageid as id";
    $select[]   = "$sql_tbl[pages].title as name";
    $from[]     = "$sql_tbl[pages]";
    $where[]    = "$sql_tbl[pages].active = 'Y'";
    $where[]    = "$sql_tbl[pages].level='E'";
    $order_by[] = ($config['Sitemap']['sitemap_pages_order'] == 'ASC') ? 'name' : "$sql_tbl[pages].orderby, name";

    if ($config['Sitemap']['sitemap_multilang'] == 'Y') {
        $code    = isset($code) ? $code : $GLOBALS['shop_language'];
        $where[] = "$sql_tbl[pages].language='$code'";
    }

    if ($start) {
        $limit[] = $start;
    }

    if ($end) {
        $limit[] = $end;
    }
    
    if ($count != false) {
        $select   = array("COUNT($sql_tbl[pages].pageid)");
        $order_by = null;
    }

    return sitemap_build_query($select, $from, $where, $order_by, $l_join, $limit);
}

//
// Extra pages related functions
//

/**
 * Get extra pages array for sitemap
 *
 * @param  int    $start
 * @param  int    $limit
 * @param  string $code
 * @return array
 */
function sitemap_get_extra($start = null, $limit = null, $code = null)
{
    global $config;
    
    $query = sitemap_get_extra_query(false, $code, $start, $limit);
    $items = func_query($query);
    
    return sitemap_define_urls($items, 'E');
}

/**
 * Get total count of extra pages
 *
 * @return int
 */
function sitemap_get_extra_total()
{
    global $config;

    $count = 0;

    if ($config['Sitemap']['sitemap_display_extra'] == 'Y') {
        $code = $GLOBALS['config']['default_customer_language'];
        $query = sitemap_get_extra_query(true, $code);
        $count = intval(func_query_first_cell($query));
    }

    return $count;
}

/**
 * Create a sql query to fetch extra pages
 *
 * @param  bool   $count
 * @param  string $code
 * @param  int    $start
 * @param  int    $end
 * @return string
 */
function sitemap_get_extra_query($count = false, $code = null, $start = null, $end = null)
{
    global $sql_tbl, $config;

    $select[]   = "$sql_tbl[sitemap_extra].name as name";
    $select[]   = "$sql_tbl[sitemap_extra].url as url";
    $select[]   = "$sql_tbl[sitemap_extra].id as id";
    $from[]     = "$sql_tbl[sitemap_extra]";
    $where[]    = "$sql_tbl[sitemap_extra].active = 'Y'";
    $order_by[] = ($config['Sitemap']['sitemap_extra_order'] == 'ASC') ? 'name' : "$sql_tbl[sitemap_extra].orderby, name";

    if ($count != false) {
        $select   = array("COUNT($sql_tbl[sitemap_extra].id)");
        $order_by = null;
    }

    if ($start) {
        $limit[] = $start;
    }

    if ($end) {
        $limit[] = $end;
    }
    
    return sitemap_build_query($select, $from, $where, $order_by, $l_join, $limit);
}

/**
 * Add extra URL to db
 *
 * @param  array       $url
 * @return string|void error text
 */
function sitemap_extra_addurl($url)
{
    if (empty($url['name']) || empty($url['url'])) {
        return func_get_langvar_by_name('err_filling_form');
    }

    $insert = array(
        //'id' => intval($url['id']),
        'name' => $url['name'],
        'url' => trim(($url['url'])),
        'active' => ($url['active'] == 'Y' ? $url['active'] : 'N'),
        'orderby' => intval($url['orderby'])
    );
    func_array2insert('sitemap_extra', $insert);
}

/**
 * Remove extra URLs from db
 *
 * @param  array       $ids
 * @return string|void error text
 */
function sitemap_extra_delurls($ids)
{
    if (!is_array($ids) || empty($ids)) {
        return func_get_langvar_by_name('lbl_no_items_have_been_selected');
    } else {
        global $sql_tbl;
        db_query("DELETE FROM $sql_tbl[sitemap_extra] WHERE id IN ('" . implode("','", $ids) . "')");
    }
}

/**
 * Update extra urls
 *
 * @param  array       $urls
 * @return string|void error text
 */
function sitemap_extra_updateurls($urls)
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
            'name' => $data['name'],
            'url' => trim(($data['url'])),
            'active' => ($data['active'] == 'Y' ? $data['active'] : 'N'),
            'orderby' => intval($data['orderby'])
        );
        func_array2update('sitemap_extra', $update, "id = '$id'");
    }
}

/**
 * Get all exrta URLs from db
 *
 * @return array
 */
function sitemap_extra_geturls()
{
    global $sql_tbl;
    
    $query = "SELECT $sql_tbl[sitemap_extra].* FROM $sql_tbl[sitemap_extra] ORDER BY $sql_tbl[sitemap_extra].orderby";
    $urls = func_query($query);
    if (!is_array($urls)) {
        $urls = array();
    }
    
    return $urls;
}

//
// Html catalog related functions
//

/**
 * Generate page filename for html catalog
 *
 * @param  string $name
 * @return string
 */
function sitemap_filename($name)
{
    if (empty($name)) {
        return __FUNCTION__;
    } else {
        return $name;
    }
}

/**
 * Modify url to point to HTML pages of the catalog
 *
 * @param  array  $data current $additional_hc_data spec
 * @param  string $src page content
 * @return string
 */
function sitemap_process_page($data, $src)
{
    $pattern = '/(<a[^<>]+href[ ]*=[ ]*["\']?)([^"\']*' . $data['page_url'] . ')((#[^"\'>]+)?["\'>])/iUS';

    $GLOBALS['sitemap_page_name'] = $data['name_func_params'][0];

    $page_src = preg_replace_callback($pattern, 'sitemap_process_page_callback', $src);

    unset($GLOBALS['sitemap_page_name']);

    return $page_src;
}

/**
 * Callback function for sitemap_process_page
 *
 * @param  array $found generated by preg_replace_callback
 * @return url
 */
function sitemap_process_page_callback($found)
{
    global $hc_state;

    if (!func_is_current_shop($found[2])) {
        return $found[1] . $found[2] . '?' . $found[3];
    }

    $url = $found[1] . $hc_state['catalog']['webpath'] . $GLOBALS['sitemap_page_name'] . $found[3];

    return $url;
}

//
// Cache generation related functions
//

/**
 * Prepare data for the cache
 *
 * @param  int  $limit_general
 * @param  int  $limit_categories
 * @return void
 */
function sitemap_start_generate_cache($limit_general, $limit_categories)
{
    func_display_service_header();

    $items = $GLOBALS['config']['Sitemap']['items'];

    if (is_array($items)) {
        $items = array_filter($items, 'sitemap_is_item_avaliable');
    } else {
        return 'No items to generate cache for';
    }

    if (count($items) <= 0) {
        return 'No items to generate cache for';
    }

    $items = array_map('sitemap_get_total', $items);

    $items = array_filter($items, 'sitemap_item_has_total');

    foreach($items as $k => $item) {
        $items[$k]['start'] = 0;
        if ($items[$k]['item'] == 'categories') {
            $items[$k]['limit'] = $limit_categories;
        } else {
            $items[$k]['limit'] = $limit_general;
        }
    }

    $languages = sitemap_get_all_languages();
    if (!is_array($languages)) {
        return 'Internal error: no language code found. Aborted';
    }
    
    foreach ($languages as $code) {
        $file = sitemap_get_cache_filename($code);
        sitemap_create_cache_file($file);
        $data[] = array(
            'code' => $code,
            'file' => $file,
            'items' => $items
        );
    }

    x_session_register('sitemap_cache_data');
    global $sitemap_cache_data;
    $sitemap_cache_data = $data;
    
    $url = $_SERVER['REQUEST_URI'] . '&' . func_qs_combine(array('cache_generation' => 'true'), false);
    func_header_location($url);
}

/**
 * Generate cache for the sitemap
 *
 * @return void
 */
function sitemap_generate_cache()
{
    func_display_service_header();

    x_session_register('sitemap_cache_data');
    global $sitemap_cache_data;

    x_session_register('sitemap_log');
    global $sitemap_log;
    
    if (!is_array($sitemap_cache_data) || count($sitemap_cache_data) == 0) {
        sitemap_finish_generation();
    }

    foreach ($sitemap_cache_data as $i => $data) {
        if (!is_array($data['items']) || count($data['items']) == 0) {
            unset($sitemap_cache_data[$i]);
            continue;
        }

        foreach ($data['items'] as $k => $item) {
            $function = 'sitemap_get_' . $item['item'];
            if (!function_exists($function)) {
                unset($sitemap_cache_data[$i]['items'][$k]);
                continue;
            }

            $GLOBALS['shop_language'] = $data['code'];
            
            $urls = $function($item['start'], $item['limit'], $data['code']);
            
            if ($urls == false) {
                unset($sitemap_cache_data[$i]['items'][$k]);
                continue;
            }
            
            $section = null;
            if ($item['start'] == 0) {
                $section = 'header';
            }
            if ($item['total'] - ($item['start'] + $item['limit']) <= 0) {
                $section .= 'footer';
            }

            $src = sitemap_generate_cache_data($item['item'], $urls, $section);
            $file = sitemap_get_cache_filename($data['code']);
            sitemap_write_cache_to_file($file, $src);

            if ($item['total'] - ($item['start'] + $item['limit']) <= 0) {
                unset($sitemap_cache_data[$i]['items'][$k]);
            } else {
                $sitemap_cache_data[$i]['items'][$k]['start'] += $item['limit'];
            }
            break;
        }
    }

    func_header_location($_SERVER['REQUEST_URI']);
}

/**
 * Redirect to the admin area when cache generation finished
 *
 * @return void
 */
function sitemap_finish_generation()
{
    x_session_register('top_message');
    global $top_message;
    
    $top_message['content'] = func_get_langvar_by_name('lbl_done');
    $top_message['type'] = 'I';
    $url = func_qs_remove($_SERVER['REQUEST_URI'], 'cache_generation');
    func_header_location($url);
}

/**
 * Get html code for passed items
 *
 * @param  string $type
 * @param  array  $items
 * @param  string $section
 * @return string
 */
function sitemap_generate_cache_data($type, $items, $section = null)
{
    global $smarty;

    $smarty->assign('items', $items);

    $src = func_display('modules/Sitemap/item_' . $type . '.tpl', $smarty, false);
    
    $smarty->clear_assign('items');

    if (strpos($section, 'header') !== false) {
        $src = func_display('modules/Sitemap/item_' . $type . '_header.tpl', $smarty, false) . $src;
    }

    if (strpos($section, 'footer') !== false) {
        $src = $src . func_display('modules/Sitemap/item_' . $type . '_footer.tpl', $smarty, false);
    }
    
    return $src;
}

/**
 * Filename where cache is located
 *
 * @param sting   $language
 * @return string
 */
function sitemap_get_cache_filename($language = null)
{
    if (!$language) {
        $language = $GLOBALS['config']['default_customer_language'];
    }

    $language = strtolower($language);

    $filename = $GLOBALS['var_dirs']['cache'] . '/' . sprintf($GLOBALS['config']['Sitemap']['cache_filename'], $language);

    return $filename;
}

/**
 * Create an empty file
 *
 * @param  string $filename
 * @return void
 */
function sitemap_create_cache_file($filename)
{
    $handle = fopen($filename, 'w');

    fclose($handle);
}

/**
 * Write data to file
 *
 * @param  string $filename
 * @param  string $data
 * @return void
 */
function sitemap_write_cache_to_file($filename, $data)
{
    $handle = fopen($filename, 'a+');

    fwrite($handle, $data);

    fclose($handle);
}

//
// Urls format related functions
//

/**
 * Creates URL using avaliable processor. Currently avaliable:
 * - Clean URLs
 * - X-SEO: Friendly URLs
 * - default php ones
 *
 * @param  string $type   C|P|M|S|H
 * @param  int    $id     item id
 * @param  array  $params additional params. now only params[url] is used. if it passed, exactly this url will be returned
 * @return string
 */
function sitemap_get_url($type, $id, $params = array())
{
    global $config, $active_modules, $xseo;

    $id = intval($id);

    $url = '';

    if (isset($params['url'])) {
        $url = $params['url'];
    } else if (isset($config['SEO']['clean_urls_enabled'])) {
        $url = func_get_resource_url($type, $id, '', false);
    } else if (isset($active_modules['XSEO']) && ( isset($xseo['modules']['urls']) && $xseo['modules']['urls']['active'] != false)) {
        global $shop_language;
        $url = xseo_urls_get_url($id, $type, $shop_language, true);
    } else {
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
                $url = 'home.php';
                break;
            default:
                $url = '';
                break;
        }
    }

    if (empty($url)) {
        $url = 'home.php';
    }

    return $url;
}

/**
 * Assign URLs to all items in the passed array
 *
 * @param  array $items
 * @param  string $type C|P|M|S|H
 * @return array
 */
function sitemap_define_urls($items, $type)
{
    if (is_array($items)) {
        array_walk($items, 'sitemap_define_urls_callback', $type);
    }
    return $items;
}

/**
 * Callback function. Adds url value to the passed item
 *
 * @param array $item
 * @param int    $key
 * @param string $type C|P|M|S|H
 */
function sitemap_define_urls_callback(&$item, $key, $type)
{
    if (isset($item['id'])) {
        $item['url'] = sitemap_get_url($type, $item['id']);
    }
}

//
// General functions
//

/**
 * Get numeric array of all languages of the store
 *
 * @return array
 */
function sitemap_get_all_languages()
{
    global $config;
    
    $all_languages = func_data_cache_get('languages', array($config['default_customer_language']));
    
    return array_keys($all_languages);
}

/**
 * Build SQL query from the passed params
 *
 * @param  array        $select
 * @param  array        $from
 * @param  array        $where
 * @param  array|null   $order_by
 * @param  array|null   $l_join
 * @param  array|null   $limit
 * @return string|false complete query or false if no params passed
 */
function sitemap_build_query($select, $from, $where, $order_by = null, $l_join = null, $limit = null)
{
    if (is_array($select)) {
        $query[] = 'SELECT ' . implode(', ', $select);
    }

    if (is_array($from)) {
        $query[] = 'FROM ' . implode(',', $from);
    }

    if (is_array($l_join)) {
        $query[] = 'LEFT JOIN ' . implode(' LEFT JOIN ', $l_join);
    }

    if (is_array($where)) {
        $query[] = 'WHERE ' . implode(' AND ', $where);
    }

    if (is_array($order_by)) {
        $query[] = 'ORDER BY ' . implode(', ', $order_by);
    }

    if (is_array($limit)) {
        $query[] = 'LIMIT ' . implode(', ', $limit);
    }

    if (is_array($query)) {
        return implode(' ', $query);
    } else {
        return false;
    }
}

/**
 * Check if the passed item is enabled in the admin area
 *
 * @param  string $item
 * @return bool
 */
function sitemap_is_item_avaliable($item)
{
    global $config;

    return ($config['Sitemap']['sitemap_display_' . $item] == 'Y') ? true : false;
}

/**
 * Call aproporiate function for getting total items count
 *
 * @param  string $item
 * @return array
 */
function sitemap_get_total($item)
{
    $function = 'sitemap_get_' . $item . '_total';

    if (function_exists($function)) {
        $return = array('item' => $item, 'total' => $function());
    } else {
        $return = array('item' => $item, 'total' => 0);
    }
    
    return $return;
}

/**
 * Check if item total more then zero
 *
 * @param unknown_type $item
 * @return void
 */
function sitemap_item_has_total($item)
{
    return ($item['total'] > 0) ? true : false;
}

?>
