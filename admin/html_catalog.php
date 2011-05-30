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
 * This script generates search engine friendly HTML catalog for X-cart
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: html_catalog.php,v 1.136.2.4 2011/01/10 13:11:45 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

define('BENCH_BLOCK', true);

require './auth.php';
require $xcart_dir.'/include/security.php';

func_set_time_limit(2700);

/**
 * Templates data
 */
$templates_data = array(
    'category' => array(
        'default_template' => "{catname}-{order}-p-{page}-c-{catid}",
        'keywords' => array(
            'catname' => array(
                'required' => false,
                'datafield' => 'category'
            ),
            'catid' => array(
                'required' => true,
                'datafield' => 'categoryid'
            ),
            'order' => array(
                'required' => true,
                'datafield' => 'order'
            ),
            'page' => array(
                'required' => true,
                'datafield' => 'page'
            )
        )
    ),
    'product' => array(
        'default_template' => "{prodname}-p-{productid}",
        'keywords' => array(
            'prodname' => array(
                'required' => false,
                'datafield' => 'product'
            ),
            'productid' => array(
                'required' => true,
                'datafield' => 'productid'
            )
        )
    ),
    'manufacturer' => array(
        'default_template' => "{manufname}-{order}-p-{page}-mf-{manufid}",
        'keywords' => array(
            'manufname' => array(
                'required' => false,
                'datafield' => 'manufacturer'
            ),
            'manufid' => array(
                'required' => true,
                'datafield' => 'manufacturerid'
            ),
            'order' => array(
                'required' => true,
                'datafield' => 'order'
            ),
            'page' => array(
                'required' => true,
                'datafield' => 'page'
            )
        )
    ),
    'staticpage' => array(
        'default_template' => "{pagename}-sp-{pageid}",
        'keywords' => array(
            'pagename' => array(
                'required' => false,
                'datafield' => 'page'
            ),
            'pageid' => array(
                'required' => true,
                'datafield' => 'pageid'
            )
        )
    )
);

/**
 * Values for 'Replace spaces and slashes in page filenames by this character' option
 */
$name_delimiters = array(
    '-' => func_get_langvar_by_name('lbl_name_delimiter_hyphen', false, false, true),
    '_' => func_get_langvar_by_name('lbl_name_delimiter_underscore', false, false, true)
);

/**
 * Template filename maximum length
 */
$template_max_length = 200;

x_load(
    'files',
    'http',
    'category',
    'html_catalog'
);

$location[] = array(func_get_langvar_by_name('lbl_html_catalog'), '');

define('DIR_CATALOG', '/catalog');
define('ALLOW_HTTP_REDIRECT', false);

$sort_fields = array(
    'title',
    'price',
    'orderby',
    'quantity',
);

if ($config['Appearance']['display_productcode_in_list'] == 'Y')
    array_unshift($sort_fields, 'productcode');

$per_page = $config['Appearance']['products_per_page'];

if ($per_page <= 0)
    $per_page = 10;

$max_name_length = 64;

$php_scripts = array(
    'search.php',
    'giftcert.php',
    'help.php',
    'cart.php',
    'product.php',
    'register.php',
    'home.php',
    'pconf.php',
    'giftregs.php',
    'manufacturers.php',
    'news.php',
    'orders.php',
    'giftreg_manage.php',
    'returns.php',
    'survey.php',
    'antibot_image.php',
    'image.php',
);

$site_location = @parse_url($http_location);

if (!isset($site_location['path']))
    $site_location['path'] = "";

// Please, synchronize related links in tpls regarding the 'X-Cart Catalog Generator' phrase
$robot_cookies = array("is_robot=Y", "robot=X-Cart Catalog Generator");

if (!empty($active_modules['Feature_Comparison'])) {
    include $xcart_dir . '/modules/Feature_Comparison/html_catalog.php';
}

if (!empty($active_modules['Special_Offers'])) {
    include $xcart_dir . '/modules/Special_Offers/html_catalog.php';
}

$stored_catalog_dirs = unserialize(func_query_first_cell("SELECT value FROM $sql_tbl[config] WHERE name='html_catalog_dirs'"));
$is_cat_arr_empty    = ( empty($stored_catalog_dirs) || !is_array($stored_catalog_dirs) );
$catalogs            = func_generate_catalog_arr($stored_catalog_dirs);

if (
    $REQUEST_METHOD == 'POST'
    && $mode == 'catalog_gen'
    || $REQUEST_METHOD == 'GET'
    && $mode == 'continue'
) {

    require $xcart_dir . '/include/safe_mode.php';

    func_display_service_header();

    echo func_get_langvar_by_name('lbl_generating_catalog', false, false, true) . "<br /><br />";

    func_flush();

    // variables initiation
    x_session_register('hc_state');

    $shop_closed_var = '';

    if (
        $config['General']['shop_closed'] == 'Y'
        && !empty($config['General']['shop_closed_key'])
    ) {
        $shop_closed_var = "&shopkey=".$config['General']['shop_closed_key'];
    }

    $post_process_products = array(); // collect products, which don't have html page generated.

    if (
        empty($hc_state)
        || $REQUEST_METHOD == 'POST'
    ) {
        $hc_state = array();

        $hc_state['category_processed']         = false;
        $hc_state['catproducts_processed']      = false;
        $hc_state['last_cid']                   = 0;
        $hc_state['last_sorts']                 = array();
        $hc_state['last_sorts_defined']         = array();
        $hc_state['man_first_page_processed']   = array();
        $hc_state['man_completed_pages']        = array();
        $hc_state['last_pid']                   = 0;
        $hc_state['cat_pages']                  = 0;
        $hc_state['cat_page']                   = 1;
        $hc_state['last_pageid']                = 0;
        $hc_state['last_manufacturerid']        = 0;
        $hc_state['count']                      = 0;
        $hc_state['start_category']             = $start_category;
        $hc_state['pages_per_pass']             = $pages_per_pass;
        $hc_state['gen_action']                 = isset($gen_action) && is_array($gen_action)
            ? array_sum($gen_action)
            : 0;
        $hc_state['process_subcats']            = isset($process_subcats);
        $hc_state['process_staticpages']        = isset($process_staticpages) && $process_staticpages == 'Y';
        $hc_state['process_manufacturers']      = isset($process_manufacturers) && !empty($active_modules['Manufacturers']) && $process_manufacturers == 'Y';
        $hc_state['time_start']                 = func_microtime();

        $saved_templates = func_get_saved_page_name_templates();

        foreach ($templates as $k => $v) {

            if (!isset($saved_templates[$k])) {

                func_unset($templates, $k);

            } elseif (!func_check_page_name_template($k, $v)) {

                $templates[$k] = $saved_templates[$k];

            }

        }

        func_save_page_name_templates($templates);

        $hc_state['templates'] = $templates;

        if (func_check_name_delim($name_delimiter)) {

            func_save_name_delim($name_delimiter);

        } else {

            $name_delimiter = func_get_saved_name_delim();

        }
 
        $hc_state['name_delim'] = $name_delimiter;

        $genlng = array();
        $lngdel = array();
        $catalog_arr = array();

        if (is_array($lngcat)) {
            foreach ($lngcat as $code => $path) {
                if (trim($path) != '') {

                    $path = stripslashes($path);
                    $path = stripslashes($path);

                    $catalog_path = func_normalize_path($path);

                    if (substr($catalog_path, 0, 1) != XC_DS)
                        $catalog_path = XC_DS.$catalog_path;

                    $genlng[] = array(
                        'code'    => $code,
                        'path'    => func_normalize_path($xcart_dir.'/'.$path),
                        'webpath' => func_normalize_path($site_location['path'].'/'.$path.'/','/'),
                    );

                    $catalog_arr[$code] = $catalog_path;

                } else {

                    $lngdel[] = $code;

                }

            }

            func_save_catalogs($catalog_arr);
        }

        if (empty($genlng)) {
            $top_message['content'] = func_get_langvar_by_name('msg_err_hc_no_languages');
            $top_message['type']    = 'E';

            func_header_location('html_catalog.php');
        }

        if (
            !$hc_state['gen_action']
            && !$hc_state['process_staticpages']
            && !$hc_state['process_manufacturers']
            && !is_array($additional_hc_data)
        ) {
            $top_message['content'] = func_get_langvar_by_name('msg_err_hc_no_gen_action');
            $top_message['type']    = 'E';

            func_header_location('html_catalog.php');
        }

        if (!empty($lngdel)) {
            $hc_state['remove_lng_line'] = implode("|", $lngdel);
        }

        $hc_state['catalog_dirs'] = $genlng;
        $hc_state['catalog_idx']  = 0;

        // If only one language, then remove the "select language" form
        if (count($hc_state['catalog_dirs']) == 1) {
            $hc_state['remove_slform'] = true;
        }

        // Check catalog directories state.
        foreach ($hc_state['catalog_dirs'] as $k => $catdir) {

            if (!@is_dir($catdir['path'])) {
                @unlink($catdir['path']);
            }

            if (!@file_exists($catdir['path'])) {
                // Try to create a catalog directory.
                func_mkdir($catdir['path']);
            }

            if (!@file_exists($catdir['path']) || !@is_dir($catdir['path'])) {

                func_unset($hc_state['catalog_dirs'], $k);

                echo func_get_langvar_by_name('msg_err_hc_wrong_cat_dir', array('path' => $catdir['path']), false, true).'<br />';

                func_flush();

                continue;
            }

            if (!@file_exists($catdir['path'].XC_DS.'.htaccess')) {

                if ($fp = @fopen($catdir['path'].XC_DS.'.htaccess', 'w')) {

                    @fwrite($fp, "<Files \"*.php\">\nDeny from all\n</Files>\n<Files \"*.pl\">\nDeny from all\n</Files>\nAllow from all\n");
                    @fclose($fp);

                    func_chmod_file($catdir['path'] . XC_DS . ".htaccess");
                }

            }

        }

        if (empty($hc_state['catalog_dirs'])) {
            $top_message['content'] = func_get_langvar_by_name('msg_err_hc_no_cat_dirs');
            $top_message['type']    = 'E';

            func_html_location('html_catalog.php', 10);
        }

        if (
            isset($drop_pages)
            && $drop_pages == 'on'
        ) {
            echo func_get_langvar_by_name('lbl_deleting_old_catalog',false,false,true)."<br />";

            func_flush();

            $__tmp = func_query("SELECT filename FROM $sql_tbl[pages] WHERE level='R' AND active = 'Y'");

            $static_root_pages = array();

            if (is_array($__tmp)) {
                foreach($__tmp as $__v)
                    $static_root_pages[] = $__v['filename'];
            }

            foreach ($hc_state['catalog_dirs'] as $catdir) {

                if (!file_exists($catdir['path']))
                    continue;

                if (!is_dir($catdir['path'])) {
                    unlink($catdir['path']);
                    continue;
                }

                $dir = opendir($catdir['path']);

                while ($file = readdir($dir)) {

                    if (
                        in_array(
                            $file,
                            array(
                                '.',
                                '..',
                                'shop_closed.html',
                                'message.html',
                                'under_update.html',
                            )
                        )
                        || strstr($file, '.html') != '.html'
                    ) {
                        continue;
                    }

                    if (in_array($file, $static_root_pages)) continue;

                    if ((filetype($catdir['path'] . XC_DS . $file) != 'dir')) {
                        unlink ($catdir['path'] . XC_DS . $file);
                    }

                }

            }

        }

        echo func_get_langvar_by_name('lbl_converting_pages_to_html', false, false, true) . "<br />";

        func_flush();

        // Dump X-cart home page to disk

        foreach ($hc_state['catalog_dirs'] as $catdir) {

            $hc_state['catalog'] = $catdir;
            $current_lng = $catdir['code'];

            list(
                $http_headers,
                $page_src
            ) = func_fetch_page(
                $site_location['host'] . ':' . $site_location['port'],
                $site_location['path'] . DIR_CUSTOMER . '/home.php',
                'sl=' . $catdir['code'] . $shop_closed_var,
                $robot_cookies
            );

            if (empty($hc_state['sid'])) {

                if (!empty($http_headers['cookies'][$XCART_SESSION_NAME])) {

                    $sid = $http_headers['cookies'][$XCART_SESSION_NAME];

                } else {

                    $sid = md5('HTML_Catalog_'.rand());

                }

                $hc_state['sid'] = $sid;
                $robot_cookies[] = $XCART_SESSION_NAME . '=' . $sid;
            }

            if (!$hc_state['process_staticpages']) {
                $php_scripts [] = 'pages.php';
            }

            $_php_scripts = array();

            foreach ($php_scripts as $k => $v) {
                $_php_scripts[$k] = preg_quote($v, '/');
            }

            $php_scripts_long = implode('|', $_php_scripts);

            convert_page($catdir['path'],$page_src,'');

            if (preg_match_all("/home.php\?cat=0?&(?:amp;)?sort=&(?:amp;)?sort_direction=&(?:amp;)?page=(\d)/Ss", $page_src, $match)) {

                $max_index_page = max($match[1])+1;

                for ($i = 2; $i < $max_index_page; $i++) {

                    list(
                        $http_headers,
                        $subpage_src
                    ) = func_fetch_page(
                        $site_location['host'] . ":" . $site_location['port'],
                        $site_location['path'] . DIR_CUSTOMER . '/home.php',
                        "cat=0&sort=&sort_direction=&page=$i",
                        $robot_cookies
                    );

                    convert_page(
                        $catdir['path'],
                        $subpage_src,
                        'category',
                        array(
                            0,
                            'index',
                            $i,
                            $config['Appearance']['products_order'],
                            0,
                        )
                    );
                }
            }

        } // foreach ($hc_state['catalog_dirs'] as $catdir)

        // Generate hc files for additional pages
        // @see modules/Sitemap/config.php | modules/Sitemap/func.php
        if (is_array($additional_hc_data)) {
            foreach ($additional_hc_data as $additional_hc_data_current) {
                if (file_exists($additional_hc_data_current['generation_script'])) {
                    include_once($additional_hc_data_current['generation_script']);
                }
            }
        }
        
        if ($hc_state['gen_action']) {

            // Take featured products into account: store them for further check, whether the pages were generated for them during the rest catalog building.

            $featured_products = get_category_featured_products(0);

            $post_process_products = func_array_merge_assoc($post_process_products, $featured_products);

            // Take bestsellers into account: store them for further check, whether the pages were generated for them during the rest catalog building.

            $bestsellers = get_category_bestsellers(0);

            $post_process_products = func_array_merge_assoc($post_process_products, $bestsellers);

        }

        $hc_state['catalog'] = $hc_state['catalog_dirs'][$hc_state['catalog_idx']];

    } else {

        echo func_get_langvar_by_name('lbl_continue_converting_pages_to_html', array('count' => $hc_state['count']),false,true)."<br />"; func_flush();

        if (!$hc_state['process_staticpages']) {
            $php_scripts [] = 'pages.php';
        }

        $_php_scripts = array();

        foreach ($php_scripts as $k => $v) {
            $_php_scripts[$k] = preg_quote($v, '/');
        }

        $php_scripts_long = implode("|", $_php_scripts);

        if (empty($hc_state['sid'])) {
            $hc_state['sid'] = md5('HTML_Catalog_'.rand());
        }

        $robot_cookies[] = $XCART_SESSION_NAME.'='.$hc_state['sid'];
    }

    // Process static pages

    if ($hc_state['process_staticpages']) {

        $current_lng = $hc_state['catalog']['code'];

        $pages_data = db_query("SELECT pageid, title FROM $sql_tbl[pages] WHERE active = 'Y' AND pageid > '$hc_state[last_pageid]' AND level='E' AND language='$current_lng' ORDER BY pageid");

        while ($pages_data && ($page_data = db_fetch_array($pages_data))) {

            $hc_state['last_pageid'] = $page_data['pageid'];

            list(
                $http_headers,
                $page_src
            ) = func_fetch_page(
                $site_location['host'] . ":" . $site_location['port'],
                $site_location['path'] . DIR_CUSTOMER . '/pages.php',
                "pageid=$page_data[pageid]&sl=" . $hc_state['catalog']['code'] . $shop_closed_var,
                $robot_cookies
            );

            convert_page(
                '',
                $page_src,
                'staticpage',
                array(
                    $page_data['pageid'],
                    $page_data['title'],
                )
            );

        }

        db_free_result($pages_data);
    }

    // Process manufacturers

    if (
        $hc_state['process_manufacturers']
        && !empty($active_modules['Manufacturers'])
    ) {

        $current_lng = $hc_state['catalog']['code'];

        $pages_data = db_query("
SELECT $sql_tbl[manufacturers].manufacturerid,
IF($sql_tbl[manufacturers_lng].manufacturer IS NOT NULL AND $sql_tbl[manufacturers_lng].manufacturer<>'',$sql_tbl[manufacturers_lng].manufacturer,$sql_tbl[manufacturers].manufacturer) AS manufacturer
FROM $sql_tbl[manufacturers]
    LEFT JOIN $sql_tbl[manufacturers_lng] ON
        $sql_tbl[manufacturers].manufacturerid=$sql_tbl[manufacturers_lng].manufacturerid AND
        $sql_tbl[manufacturers_lng].code='$current_lng'
WHERE avail='Y' AND $sql_tbl[manufacturers].manufacturerid > '$hc_state[last_manufacturerid]'
ORDER BY $sql_tbl[manufacturers].manufacturerid
");

        while ($pages_data && ($page_data = db_fetch_array($pages_data))) {

            // Proccess first page
            list(
                $http_headers,
                $page_src
            ) = func_fetch_page(
                $site_location['host'] . ":" . $site_location['port'],
                $site_location['path'] . DIR_CUSTOMER . '/manufacturers.php',
                "manufacturerid=$page_data[manufacturerid]&sl=" . $current_lng . $shop_closed_var,
                $robot_cookies
            );

            // Check if it is the last page in the current pass
            if (!in_array($page_data['manufacturerid'], $hc_state['man_first_page_processed'])) {

                convert_page(
                    '',
                    $page_src,
                    'manufacturer',
                    array(
                        $page_data['manufacturerid'],
                        $page_data['manufacturer'],
                        1,
                        $config['Appearance']['products_order'],
                        0,
                    )
                );

                $hc_state['man_completed_pages'] = array();

                $hc_state['man_completed_pages'][] = $page_data['manufacturerid'] . '_1_' . $config['Appearance']['products_order'] . "_0";

            }

            $hc_state['man_first_page_processed'][] = $page_data['manufacturerid'];

            $sorts = array();

            if (preg_match_all("/manufacturers.php\?manufacturerid=".$page_data['manufacturerid']."&[^\"']*sort=([\w\d_]+)[&\"']/S", $page_src, $msorts)) {
                $sorts = array_unique($msorts[1]);
            }

            if (empty($sorts)) {
                $sorts = $sort_fields;
            }

            $max_pages = 2;

            if (preg_match_all("/manufacturers.php\?manufacturerid=".$page_data['manufacturerid']."&[^\"']*page=(\d+)[&\"']/S", $page_src, $mpages)) {
                // Process other pages (by page number, sort field and sort direction)

                $max_pages = max($mpages[1])+1;

            }

            foreach ($sorts as $s) {

                for ($pn = 1; $pn < $max_pages; $pn++) {

                    for ($sd = 0; $sd < 2; $sd++) {

                        if (
                            $sd == 0
                            && $s == $config['Appearance']['products_order']
                            && $pn == 1
                        ) {
                            continue;
                        }

                        list(
                            $http_headers,
                            $page_src
                        ) = func_fetch_page(
                            $site_location['host'] . ":" . $site_location['port'],
                            $site_location['path'] . DIR_CUSTOMER . '/manufacturers.php',
                            "manufacturerid=$page_data[manufacturerid]&page=$pn&sort=$s&sort_direction=$sd&sl=" . $current_lng . $shop_closed_var,
                            $robot_cookies
                        );

                        if (
                            $pn > 1
                            && $pn >= $max_pages - 1
                            && preg_match_all(
                                "/manufacturers.php\?manufacturerid="
                                    . $page_data['manufacturerid']
                                    . "&[^\"']*page=(\d+)[&\"']/S",
                                $page_src,
                                $mpages
                            )
                        ) {
                            $local_max_pages = max($mpages[1])+1;

                            if ($local_max_pages > $max_pages)
                                $max_pages = $local_max_pages;
                        }

                        if (
                            !in_array(
                                $page_data['manufacturerid'] . '_' . $pn . '_' . $s . '_' . $sd,
                                $hc_state['man_completed_pages']
                            )
                        ) {
                            $hc_state['man_completed_pages'][] = $page_data['manufacturerid'] . '_' . $pn . '_' . $s . '_' . $sd;

                            convert_page(
                                '',
                                $page_src,
                                'manufacturer',
                                array(
                                    $page_data['manufacturerid'],
                                    $page_data['manufacturer'],
                                    $pn,
                                    $s,
                                    $sd,
                                )
                            );
                        }
                    }
                }
            }

            $hc_state['last_manufacturerid'] = $page_data['manufacturerid'];
        }

        db_free_result($pages_data);
    }

    // Let's generate the catalog
 
    if ($hc_state['cat_pages'] > 0 || isset($hc_state['catproducts'])) {

        $categories_cond = "$sql_tbl[categories].categoryid >= '" . $hc_state["last_cid"] . "'";

    } else {

        $categories_cond = "$sql_tbl[categories].categoryid > '" . $hc_state["last_cid"] . "'";

    }
    if (!empty($hc_state['start_category'])) {

        if (isset($hc_state["process_subcats"])) {

            x_load('category');

            $root_cat = func_category_get_position($hc_state['start_category']);

            $categories_cond .= " AND $sql_tbl[categories].lpos BETWEEN " . $root_cat['lpos'] . ' AND ' .  $root_cat['rpos'];

        } else {

            $categories_cond .= " AND $sql_tbl[categories].categoryid='" . $hc_state["start_category"] . "'";

        }
    }
    $categories_cond .= " AND $sql_tbl[categories].avail = 'Y' AND $sql_tbl[category_memberships].categoryid IS NULL";

    $categories_data = db_query("SELECT $sql_tbl[categories].categoryid, $sql_tbl[categories].category FROM $sql_tbl[categories] LEFT JOIN $sql_tbl[category_memberships] ON $sql_tbl[categories].categoryid = $sql_tbl[category_memberships].categoryid WHERE " . $categories_cond . " GROUP BY $sql_tbl[categories].categoryid ORDER BY $sql_tbl[categories].categoryid");

    $avail_condition = '';
    $avail_join      = '';
    $avail_group_by  = '';
    $avail_condition = '';
    $func_counter = "array_shift";

    if (
        $config['General']['unlimited_products'] != 'Y'
        && $config['General']['show_outofstock_products'] != 'Y'
    ) {
        if (!empty($active_modules['Product_Options'])) {

            $avail_join = "INNER JOIN $sql_tbl[quick_flags] ON $sql_tbl[quick_flags].productid = $sql_tbl[products].productid LEFT JOIN $sql_tbl[variants] ON $sql_tbl[variants].productid = $sql_tbl[products].productid";

            $avail_condition = " AND IFNULL($sql_tbl[variants].avail, $sql_tbl[products].avail) > 0 ";

            $avail_group_by = "GROUP BY $sql_tbl[products].productid";

            $func_counter = "count";

        } else {

            $avail_condition = " AND $sql_tbl[products].avail>'0' ";

        }

    }

    if ($categories_data) {

        while ($category_data = db_fetch_array($categories_data)) {

            // Check parent categories availability
            $parents = func_get_category_path($category_data['categoryid']);

            array_pop($parents);

            if (!empty($parents)) {
                $res = db_query("SELECT $sql_tbl[categories].categoryid FROM $sql_tbl[categories] LEFT JOIN $sql_tbl[category_memberships] ON $sql_tbl[categories].categoryid = $sql_tbl[category_memberships].categoryid WHERE $sql_tbl[categories].categoryid IN ('".implode("','", $parents)."') AND $sql_tbl[categories].avail = 'Y' AND $sql_tbl[category_memberships].categoryid IS NULL GROUP BY $sql_tbl[categories].categoryid");

                if (!$res)
                    continue;

                $parents_cnt = db_num_rows($res);

                db_free_result($res);

                if ($parents_cnt != count($parents))
                    continue;
            }

            $hc_state['last_cid'] = $category_data['categoryid'];

            if (($hc_state['gen_action'] & 1) === 1 && !isset($hc_state['catproducts'])) {

                if ($hc_state['cat_pages'] == 0 && !isset($hc_state['cat_done'])) {

                    $product_count_column = func_query_column($sql = "
                    SELECT COUNT(*)
                      FROM xcart_products_categories
                     INNER JOIN xcart_products
                        ON xcart_products_categories.categoryid = '$category_data[categoryid]'
                       AND xcart_products_categories.productid  = xcart_products.productid
                       AND xcart_products.forsale               = 'Y' 
                     $avail_join
                      LEFT JOIN xcart_product_memberships
                        ON xcart_products.productid             = xcart_product_memberships.productid
                     WHERE xcart_product_memberships.productid IS NULL $avail_condition $avail_group_by
                    ");

                    $product_count = is_array($product_count_column) ? $func_counter($product_count_column) : 0;

                    $pages = ceil($product_count/$per_page);

                    if ($pages == 0) $pages = 1;

                    $first = 1;

                    $hc_state['cat_pages'] = $pages;

                    $hc_state['cat_done'] = false;

                } else {

                    $first = $hc_state['cat_page'];

                    $pages = $hc_state['cat_pages'];

                }

                // process pages of category
                if (!isset($hc_state['cat_done']) || !@$hc_state['cat_done']) {

                    $current_lng = $hc_state['catalog']['code'];

                    for ($i = $first; $i <= $pages; $i++) {

                        $page_query = "cat=$category_data[categoryid]&page=$i&sl=" . $hc_state['catalog']['code'] . $shop_closed_var;

                        $hc_state['cat_page'] = $i;

                        if (empty($hc_state['last_sorts_defined'])) {

                            list(
                                $http_headers,
                                $page_src
                            ) = func_fetch_page(
                                $site_location['host'] . ":" . $site_location['port'],
                                $site_location['path'] . DIR_CUSTOMER . '/home.php',
                                $page_query,
                                $robot_cookies
                            );

                            $hc_state['last_sorts_defined'] = get_sorts($page_src);

                            convert_page(
                                '',
                                $page_src,
                                'category',
                                array(
                                    $category_data['categoryid'],
                                    $category_data['category'],
                                    $i,
                                    $config['Appearance']['products_order'],
                                    0,
                                )
                            );

                        }

                        foreach ($hc_state['last_sorts_defined'] as $sf) {

                            if (!in_array($sf, $sort_fields))
                                continue;

                            if (
                                !isset($hc_state['last_sorts'][$sf])
                                || !in_array(1, $hc_state['last_sorts'][$sf])
                            ) {

                                $hc_state['last_sorts'][$sf] = array(1);

                                list(
                                    $http_headers,
                                    $page_src
                                ) = func_fetch_page(
                                    $site_location['host'] . ":" . $site_location['port'],
                                    $site_location['path'] . DIR_CUSTOMER . '/home.php',
                                    $page_query . "&sort=" . $sf . "&sort_direction=1",
                                    $robot_cookies
                                );

                                convert_page(
                                    '',
                                    $page_src,
                                    'category',
                                    array(
                                        $category_data['categoryid'],
                                        $category_data['category'],
                                        $i,
                                        $sf,
                                        1,
                                    )
                                );
                            }

                            if ($config['Appearance']['products_order'] == $sf)
                                continue;

                            if (
                                !isset($hc_state['last_sorts'][$sf])
                                || !in_array(0, $hc_state['last_sorts'][$sf])
                            ) {

                                $hc_state['last_sorts'][$sf][] = 0;

                                list(
                                    $http_headers,
                                    $page_src
                                ) = func_fetch_page(
                                    $site_location['host'] . ":" . $site_location['port'],
                                    $site_location['path'] . DIR_CUSTOMER . '/home.php',
                                    $page_query . "&sort=" . $sf . "&sort_direction=0",
                                    $robot_cookies
                                );

                                convert_page(
                                    '',
                                    $page_src,
                                    'category',
                                    array(
                                        $category_data['categoryid'],
                                        $category_data['category'],
                                        $i,
                                        $sf,
                                        0,
                                    )
                                );
                            }
                        }

                        $hc_state['last_sorts'] = array();
                        $hc_state['last_sorts_defined'] = array();
                    }
                }

                unset($hc_state['cat_done']);

                $hc_state['cat_page']           = 1;
                $hc_state['cat_pages']          = 0;
                $hc_state['last_sorts']         = array();
                $hc_state['last_sorts_defined'] = array();
            }

            if ($hc_state['gen_action']) {

                // Take featured products into account: store them for further check,
                // whether the pages were generated for them during the rest catalog building.

                $featured_products = get_category_featured_products($category_data['categoryid']);

                $post_process_products = func_array_merge_assoc($post_process_products, $featured_products);

                // Take bestsellers into account: store them for further check,
                // whether the pages were generated for them during the rest catalog building.

                $bestsellers = get_category_bestsellers($category_data['categoryid']);

                $post_process_products = func_array_merge_assoc($post_process_products, $bestsellers);

            }

            // process products in category
            if (($hc_state['gen_action'] & 2) === 2) {

                $prod_cond = " AND $sql_tbl[products].productid > '".$hc_state["last_pid"]."'";

                $products_data = db_query("SELECT $sql_tbl[products].productid, $sql_tbl[products].product FROM $sql_tbl[products_categories], $sql_tbl[products] LEFT JOIN $sql_tbl[product_memberships] ON $sql_tbl[products].productid = $sql_tbl[product_memberships].productid $avail_join WHERE $sql_tbl[products_categories].categoryid = '$category_data[categoryid]' AND $sql_tbl[products_categories].productid = $sql_tbl[products].productid AND $sql_tbl[products].forsale != 'N' AND $sql_tbl[product_memberships].productid IS NULL $prod_cond $avail_condition $avail_group_by  ORDER BY $sql_tbl[products].productid");

                if ($products_data) {

                    $hc_state['catproducts'] = false;

                    while($product_data = db_fetch_array($products_data)) {

                        if (isset($post_process_products[$product_data['productid']])) {
                            unset($post_process_products[$product_data['productid']]);
                        }

                        $hc_state['last_pid'] = $product_data['productid'];

                        $current_lng = $hc_state['catalog']['code'];

                        list(
                            $http_headers,
                            $page_src
                        ) = func_fetch_page(
                            $site_location['host'] . ":" . $site_location['port'],
                            $site_location['path'] . DIR_CUSTOMER . '/product.php',
                            "productid=$product_data[productid]&sl=" . $hc_state['catalog']['code'] . $shop_closed_var,
                            $robot_cookies
                        );

                        convert_page(
                            '',
                            $page_src,
                            'product',
                            array(
                                $product_data['productid'],
                                $product_data['product'],
                            )
                        );

                    }

                    $hc_state['last_pid'] = 0;

                    unset($hc_state['catproducts']);

                } // if ($products_data)

            } // if (($hc_state['gen_action'] & 2) === 2)

        } // while ($category_data = db_fetch_array($categories_data))

    } // if ($categories_data)

/**
 * Generate html pages for products, skipped during categories processing:
 */
    if (is_array($post_process_products)) {

        foreach ($post_process_products as $productid=>$productname) {

            $hc_state['last_pid'] = $productid;

            $current_lng = $hc_state['catalog']['code'];

            list(
                $http_headers,
                $page_src
            ) = func_fetch_page(
                $site_location['host'] . ":" . $site_location['port'],
                $site_location['path'] . DIR_CUSTOMER . '/product.php',
                "productid=$productid&sl=" . $hc_state['catalog']['code'] . $shop_closed_var,
                $robot_cookies
            );

            convert_page(
                '',
                $page_src,
                'product',
                array(
                    $productid,
                    $productname,
                )
            );
        }
    }

    $hc_state['catalog_idx']++;

    if (isset($hc_state['catalog_dirs'][$hc_state['catalog_idx']])) {

        $hc_state['catalog']               = $hc_state['catalog_dirs'][$hc_state['catalog_idx']];
        $hc_state['category_processed']    = false;
        $hc_state['catproducts_processed'] = false;
        $hc_state['man_first_page_processed'] = array();
        $hc_state['last_cid']              = 0;
        $hc_state['last_pid']              = 0;
        $hc_state['cat_pages']             = 0;
        $hc_state['cat_page']              = 1;
        $hc_state['last_pageid']           = 0;
        $hc_state['last_manufacturerid']   = 0;

        echo "<hr />";

        func_html_location("html_catalog.php?mode=continue",20);

    }

    $time_end = func_microtime();

    echo "<br />".func_get_langvar_by_name('lbl_html_catalog_created_successfully',false,false,true)."<br />";

    echo func_get_langvar_by_name('lbl_time_elapsed_n_secs', array('sec' => round($time_end-$hc_state['time_start'],2)),false,true);

    x_session_unregister('hc_state');

    func_html_location('html_catalog.php',30);

} else {

    // Grab all categories

    x_session_unregister('hc_state');

    x_load('category');

    $smarty->assign('categories', func_data_cache_get("get_categories_tree", array(0, true, $shop_language, $user_account['membershipid'])));

    // Smarty display code goes here

    $smarty->assign('cat_dir',              func_normalize_path($xcart_dir . DIR_CATALOG));
    $smarty->assign('cat_url',              $http_location . DIR_CATALOG . '/index.html');
    $smarty->assign('default_catalog_path', DIR_CATALOG);
    $smarty->assign('cat_info',             $catalogs);
    $smarty->assign('is_cat_empty',         $is_cat_arr_empty);

    $templates = func_get_saved_page_name_templates();

    $name_delim = func_get_saved_name_delim();

    foreach ($templates_data as $k => $t) {

        $used_keywords = func_get_keywords_from_template($k, $templates[$k]);

        $rtags  = array();
        $nrtags = array();

        foreach ($t['keywords'] as $kk => $kv) {

            $t['keywords'][$kk]['used'] = in_array($kk, $used_keywords);
            $t['keywords'][$kk]['alt']  = func_get_langvar_by_name("lbl_" . $k . "_" . $kk . "_template_keyword", array(), false, true);

            if ($kv['required']) {

                $rtags[] = $kk;

            } else {

                $nrtags[] = $kk;

            }

        }

        $templates[$k] = array(
            'template' => $templates[$k],
            'keywords' => $t['keywords'],
            'label'    => func_get_langvar_by_name('lbl_' . $k . '_page_name_format', array(), false, true),
            'rtags'    => "{" . implode("}, {", $rtags) . "}",
            'nrtags'   => "{" . implode("}, {", $nrtags) . "}"
        );

    }

    $smarty->assign('templates',           $templates);
    $smarty->assign('name_delimiters',     $name_delimiters);
    $smarty->assign('name_delim',          $name_delim);
    $smarty->assign('template_max_length', intval($template_max_length));

    $smarty->assign('main', 'html_catalog');

    // Assign the current location line
    $smarty->assign('location', $location);

    if (
        file_exists($xcart_dir.'/modules/gold_display.php')
        && is_readable($xcart_dir.'/modules/gold_display.php')
    ) {
        include $xcart_dir.'/modules/gold_display.php';
    }

    func_display('admin/home.tpl', $smarty);
}
?>
