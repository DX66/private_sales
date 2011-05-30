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
 * Repository of data cache functions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: data_cache.php,v 1.42.2.22 2011/05/03 08:31:29 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

if (defined('X_PHP5x_COMPAT'))
    require_once($xcart_dir . '/include/classes/class.xc_cache_lite.php');
else
    require_once($xcart_dir . '/include/classes4/class.xc_cache_lite.php');

// if has_private_data is true then add php die(); to header of the cache file, PEAR::Cache* is not applicable
$data_caches = array(
    'modules'             => array(
        'func' => 'func_dc_modules'
    ),
    'setup_images'         => array(
        'func' => 'func_dc_setup_images'
    ),
    'charsets'             => array(
        'func' => 'func_dc_charsets'
    ),
    'languages'         => array(
        'func' => 'func_dc_languages'
    ),
    'payments_https'     => array(
        'func' => 'func_dc_payments_https',
        'has_private_data' => true
    ),
    'sql'                 => array(
        'func' => 'func_dc_sql',
        'ttl' => SQL_CACHE_TTL,
        'exclude_keys' => array('query'),
        'has_private_data' => true
    ),
    'get_categories_tree'     => array(
        'func' => 'func_dc_get_categories_tree'
    ),
    'get_language_vars'     => array(
        'func' => 'func_dc_get_language_vars'
    ),
    'get_offers_categoryid'     => array(
        'func' => 'func_dc_get_offers_categoryid'
    ),
    'sql_vars'     => array(
        'func' => 'func_dc_sql_vars',
        'ttl' => 600, // 10 minutes to invalidate cache bt:#0092173
        'has_private_data' => true
    ),
    'get_default_fields'     => array(
        'func' => 'func_get_default_fields',
        'use_func_cache_logic' => true
    ),
    'sql_tables_fields'     => array(
        'func' => 'func_dc_sql_tables_fields'
    ),
);

if (!defined('QUICK_START')) {

    $_data_cache_ttl = $config['General']['data_cache_ttl'] > 0 ? $config['General']['data_cache_ttl'] : 3;
    $_data_cache_ttl *= 3600;

    if (
        empty($config['data_cache_expiration'])
        || (XC_TIME-$config['data_cache_expiration']) > $_data_cache_ttl
    ) {

        $config['data_cache_expiration'] = XC_TIME;

        func_array2insert(
            'config',
            array(
                'value'    => $config['data_cache_expiration'],
                'name'     => 'data_cache_expiration',
                'defvalue' => '',
                'variants' => ''
            ),
            true
        );

        func_data_cache_clear();
    }
    $_data_cache_ttl = null;
}

/**
 * Sort active_modules according order to initialization
 */
function func_sort_active_modules($a, $b)
{
    static $sort_order = array(
        'Amazon_Checkout' => 1, #can be unsetted in config/init.php
        'Flyout_Menus' => 1, #can be unsetted in config/init.php
        'Google_Checkout' => 1, #can be unsetted in config/init.php
        'Magnifier' => 1, #can be unsetted in config/init.php
        'Wishlist' => 11, #Gift_Registry depends on Wishlist
        'Gift_Registry' => 12, #can be unsetted in config/init.php
        'News_Management' => 31,
        'Survey' => 32,
        'Image_Verification' => 33,# Image_Verification depends on Survey/News_Management
        'Manufacturers' => 23,
        'XAffiliate' => 24, #XAffiliate depends on Manufacturers
    );
    $key_a = isset($sort_order[$a]) ? -$sort_order[$a] : -1000;
    $key_b = isset($sort_order[$b]) ? -$sort_order[$b] : -1001;
    return $key_b - $key_a;
}

function func_dc_modules()
{
    global $sql_tbl;

    $all_active_modules = func_query_column("SELECT module_name FROM " . $sql_tbl['modules'] . " USE INDEX (active) WHERE active='Y'");

    $active_modules = array();

    if ($all_active_modules) {
        usort($all_active_modules, 'func_sort_active_modules');
        foreach($all_active_modules as $active_module) {
            $active_modules[$active_module] = true;
        }
    }

    return $active_modules;
}

function func_dc_setup_images()
{
    global $sql_tbl, $xcart_dir;

    $setup_images = func_query_hash("SELECT * FROM " . $sql_tbl['setup_images'], "itype", false);

    if(!empty($setup_images)) {

        $default_images = array();

        foreach($setup_images as $k => $v) {

            if (!empty($v['default_image'])) {

                $tmp = isset($default_images[md5($v['default_image'])])
                    ? $default_images[md5($v['default_image'])]
                    : func_get_image_size($xcart_dir.XC_DS.$v['default_image']);

            }

            if (is_array($tmp)) {
                $setup_images[$k]['image_x'] = $tmp[1];
                $setup_images[$k]['image_y'] = $tmp[2];
            }

        }

    }

    return $setup_images;
}

function func_dc_charsets()
{
    global $sql_tbl;

    return func_query_hash(
        "SELECT " . $sql_tbl['languages'] . ".code, " . $sql_tbl['language_codes'] . ".charset FROM " . $sql_tbl['languages'] . ", " . $sql_tbl['language_codes'] . " WHERE " . $sql_tbl['languages'] . ".code = " . $sql_tbl['language_codes'] . ".code AND " . $sql_tbl['language_codes'] . ".disabled != 'Y' GROUP BY " . $sql_tbl['languages'] . ".code",
        'code',
        false,
        true
    );
}

function func_dc_languages($code)
{
    global $sql_tbl, $current_location;

    $_codes = func_query_column("SELECT DISTINCT code FROM $sql_tbl[languages]");

    $languages_codes = implode("', '", $_codes);

    if (version_compare(X_MYSQL_VERSION, '4.1.0') >= 0) {
        $languages = func_query_hash("
                SELECT tmp_lng.*, IFNULL(lng_l.value, tmp_lng.language) AS language,
                       $sql_tbl[images_G].image_path, $sql_tbl[images_G].image_x, $sql_tbl[images_G].image_y
                  FROM (
                      SELECT DISTINCT $sql_tbl[language_codes].*, CONCAT('language_', code) AS _language_code
                        FROM $sql_tbl[language_codes]
                       WHERE code IN ('$languages_codes')
                 ) AS tmp_lng
                  LEFT JOIN $sql_tbl[languages] AS lng_l
                    ON lng_l.code               = '$code'
                   AND lng_l.name               = tmp_lng._language_code
                  LEFT JOIN $sql_tbl[images_G]
                    ON $sql_tbl[images_G].id    = tmp_lng.lngid
                 ORDER BY language", 'code', false, false);
    } else {
        $languages = func_query_hash("
                SELECT $sql_tbl[language_codes].*, IFNULL(lng_l.value, $sql_tbl[language_codes].language) as
                       language, $sql_tbl[images_G].image_path, $sql_tbl[images_G].image_x,
                       $sql_tbl[images_G].image_y
                  FROM $sql_tbl[languages], $sql_tbl[language_codes]
                  LEFT JOIN $sql_tbl[languages] as lng_l
                    ON lng_l.code               = '$code'
                   AND lng_l.name               = CONCAT('language_', $sql_tbl[language_codes].code)
                  LEFT JOIN $sql_tbl[images_G]
                    ON $sql_tbl[images_G].id    = $sql_tbl[language_codes].lngid
                 WHERE $sql_tbl[languages].code = $sql_tbl[language_codes].code
                 GROUP BY $sql_tbl[languages].code
                 ORDER BY language", 'code', false, false);
    }                             



    if (!empty($languages)) {

        foreach ($languages as $k => $v) {

            $languages[$k]['code'] = $k;
            unset($languages[$k]['_language_code']);

            if (!is_null($v['image_path'])) {
                if ($languages[$k]['is_url'] = is_url($v['image_path'])) {
                    $languages[$k]['tmbn_url'] = $v['image_path'];
                } else {
                    $languages[$k]['tmbn_url'] = func_get_image_url($v['lngid'], 'G', $v['image_path']);
                    $languages[$k]['tmbn_url'] = str_replace($current_location, '', $languages[$k]['tmbn_url']);
                }
            }    
        }

    }

    return $languages;
}

function func_dc_payments_https()
{
    global $sql_tbl;

    return func_query("SELECT $sql_tbl[payment_methods].paymentid, $sql_tbl[ccprocessors].processor FROM $sql_tbl[payment_methods] USE INDEX (protocol) LEFT JOIN $sql_tbl[ccprocessors] ON $sql_tbl[payment_methods].paymentid = $sql_tbl[ccprocessors].paymentid WHERE $sql_tbl[payment_methods].protocol = 'https'");
}

function func_dc_sql($md5, $query, $type)
{
    switch ($type) {
        case 'first':
            return func_query_first($query);

        case 'first_cell':
            return func_query_first_cell($query);

        case 'column':
            return func_query_column($query);

        default:
            return func_query($query);
    }

    return null;
}

function func_dc_get_categories_tree($root, $simple, $language, $membershipid)
{
    global $sql_tbl;

    x_load('category');
    return func_get_categories_tree($root, $simple, $language, $membershipid);
}

/*
* Cache all languages in a file
*/
function func_dc_get_language_vars($lng_code)
{
    assert('/*Func_dc_get_language_vars @param*/ 
    is_string($lng_code) && !empty($lng_code)');

    global $sql_tbl, $config, $all_languages;

    $lng = array();    

    if (empty($lng_code))
        return $lng;

    $default_language = empty($config['default_customer_language']) ? $config['default_admin_language'] : $config['default_customer_language'];

    if (
        count($all_languages) == 1 
        || $lng_code == $default_language
    ) {
        $labels = db_query("SELECT name, value FROM xcart_languages WHERE code = '$lng_code'");
    } elseif(version_compare(X_MYSQL_VERSION, '5.0.1') > 0) {
        // Obtain all languages with $lng_code, add $default_language for empty names. Thanks2Abr.
        db_query("CREATE OR REPLACE VIEW base_lang AS SELECT name,value FROM xcart_languages WHERE code ='$lng_code'");
        $labels = db_query("
                SELECT * FROM base_lang
                UNION 
                SELECT name, value
                    FROM $sql_tbl[languages]
                    WHERE code = '$default_language'
                    AND name NOT IN ( SELECT DISTINCT name FROM base_lang)"
        );
    }

    if ($labels) {
        while ($v = db_fetch_array($labels)) {
            $lng[$v['name']] = $v['value'];
        }
        db_free_result($labels);
    }

    assert('/*Func_dc_get_language_vars @return*/
    !empty($lng) && is_array($lng)');
    return $lng;
}

/*
 Cache offers for category in a file
*/
function func_dc_get_offers_categoryid($categoryid) 
{
    global $active_modules;

    if (empty($active_modules['Special_Offers']))
        return false;

    return func_get_offers_categoryid($categoryid);
}

function func_dc_sql_vars()
{
    if (version_compare(X_MYSQL_VERSION, '5.0.3') >= 0)
        return func_query_hash("SHOW VARIABLES WHERE Variable_name in ('max_allowed_packet', 'lower_case_table_names', 'max_join_size', 'character_set_client')", 'Variable_name', false, true);
    else        
        return func_query_hash("SHOW VARIABLES", 'Variable_name', false, true);
}

/*
 Cache functions call in a file
*/
function func_save_cache_func($data, $cache_key, $name) {
    global $data_caches;

    if (
        !isset($data_caches[$name])
        || empty($data_caches[$name]['func'])
        || !function_exists($data_caches[$name]['func'])
        || empty($data_caches[$name]['use_func_cache_logic'])
        || !empty($data_caches[$name]['has_private_data'])
    ) { 
        return false;
    } 

    $no_save = defined('BLOCK_DATA_CACHE_' . strtoupper($name));
    if (
        !defined('USE_DATA_CACHE')
        || !constant('USE_DATA_CACHE')
        || $no_save
    ) {
        return false;
    }

    $cache_lite = XC_Cache_Lite::get_instance(); 
    if ($cache_lite)
        return $cache_lite->save($data, $cache_key, $name);
    else 
        return false;
}

function func_get_cache_func($cache_key, $name) {
    global $data_caches;

    if (
        !isset($data_caches[$name])
        || empty($data_caches[$name]['func'])
        || !function_exists($data_caches[$name]['func'])
        || empty($data_caches[$name]['use_func_cache_logic'])
        || !empty($data_caches[$name]['has_private_data'])
    ) { 
        return false;
    } 

    $no_save = defined('BLOCK_DATA_CACHE_' . strtoupper($name));
    if (
        !defined('USE_DATA_CACHE')
        || !constant('USE_DATA_CACHE')
        || $no_save
    ) {
        return false;
    }

    $cache_lite = XC_Cache_Lite::get_instance();

    if ($cache_lite) {
        $ttl = !empty($data_caches[$name]['ttl'])
            ? $data_caches[$name]['ttl']
            : $cache_lite->default_ttl;
        $cache_lite->setLifeTime($ttl);

        return $cache_lite->get($cache_key, $name);
    } else {
        return false;
    }
}

function func_dc_sql_tables_fields()
{
    global $sql_tbl;
    $all_tables = func_query_column("SHOW TABLES");

    if (empty($all_tables))
        return false;

    $storage = array();
    foreach ($all_tables as $k => $v) { 
        $storage[strtolower($v)] = func_query_column('SHOW FIELDS FROM ' . $v);
    }    

    return $storage;
}

?>
