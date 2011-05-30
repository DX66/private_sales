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
 * HTML catalog generation functions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.html_catalog.php,v 1.40.2.3 2011/01/10 13:11:51 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */
if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

function func_save_catalogs($cat_arr = array())
{
    return func_array2insert('config', array('name' => 'html_catalog_dirs', 'value' => addslashes(serialize($cat_arr)), 'type' => 'text'), true);
}

function func_generate_catalog_arr($cat_arr = array())
{
    global $all_languages;
    global $xcart_dir;
    global $http_location;

    $catalogs = array();
    foreach ($all_languages as $language) {
        $cat_relative_dir = preg_replace("/\/+$/Ss", '', preg_replace("/^\/+/Ss", '', func_normalize_path($cat_arr[$language['code']], "/")));

        $cat_url = $http_location.'/'.$cat_relative_dir.'/index.html';
        if (strncmp($xcart_dir, func_normalize_path($xcart_dir.XC_DS.$cat_relative_dir), strlen($xcart_dir)) !== 0) {
            $parsed = @parse_url($cat_url);
            $parsed['path'] = func_normalize_path($parsed['path'], "/");
            if (strpos($parsed['path'], '..') !== false)
                continue;

            $cat_url = func_assemble_url($parsed);
        }

        $catalogs[] = array (
            'lang_code' => $language['code'],
            'language' => $language['language'],
            'path' => $cat_arr[$language['code']],
            'cat_dir' => func_normalize_path($xcart_dir.'/'.$cat_arr[$language['code']]),
            'cat_url' => $cat_url
        );
    }

    return $catalogs;
}

function func_fetch_page($host, $path, $arg, $cookies)
{
    $max_count = 5;

    while ($max_count >= 0) {

        $result = func_http_get_request($host, $path, $arg, $cookies);

        list($http_headers, $page_src) = $result;

        if (defined('ALLOW_HTTP_REDIRECT') && constant('ALLOW_HTTP_REDIRECT')) {

            $redirect_url = func_process_http_redirect($http_headers, "http://".$host.$path."?".$arg);

            if (!empty($redirect_url) && is_url($redirect_url)) {

                $parsed = @parse_url($redirect_url);
                $host = '';

                if ($parsed['user']) {

                    $host .= $parsed['user'];

                    if ($parsed['pass'])
                        $host .= ":" . $parsed['pass'];

                    $host .= "@";
                }

                $host .= $parsed['host'];

                if ($parsed['port'])
                    $host .= ":" . $parsed['port'];

                $path = $parsed['path'];
                $arg = $parsed['query'];
                $page_src = false;
            }
        }

        if (is_string($http_headers) && (substr($http_headers, 9, 6) != '200 OK' || $http_headers == '0')) {

            $page_src = false;

            $result = array(
                $http_headers,
                $page_src,
            );

        }

        if (!empty($page_src))
            break;

        sleep(1);

        $max_count--;

    }

    return $result;
}

function my_save_data($filename, $data)
{
    global $hc_state;

    $filename = func_normalize_path($filename);

    if ($data == '') {
        func_flush(func_relative_path($filename) . ". <font class='Star'>" . func_get_langvar_by_name("lbl_empty_response", null, false, true) . "</font><br />\n");

        return;
    }

    $fp = @fopen($filename, "w+");

    if ($fp === false) {
        echo "<font class=\"Star\">" . func_get_langvar_by_name('lbl_cannot_save_file_N', array('file' => func_relative_path($filename)),false,true) . "</font>";
        x_session_save();

        exit;
    }

    fwrite($fp, $data);
    fclose($fp);

    func_chmod_file($filename);

    $hc_state['count'] ++;

    func_flush(func_relative_path($filename).". " . func_get_langvar_by_name('lbl_ok', null, false, true) . " <br />\n");

    if ($hc_state['pages_per_pass'] > 0 && $hc_state['count'] > 0 && ($hc_state['count'] % $hc_state['pages_per_pass']) == 0) {
        echo "<hr />";
        func_html_location("html_catalog.php?mode=continue",1);
    }
}

function normalize_name($name)
{
    global $max_name_length, $hc_state;

    static $r_match = false;
    static $r_repl = false;

    if ($r_match == false) {
        $r_match = array(
            "/[ \/".$hc_state['name_delim']."]+/Ss",
            "/[^A-Za-z0-9_".$hc_state['name_delim']."]+/Ss"
        );
        $r_repl = array($hc_state['name_delim'], '');
    }

    if (strlen($name) > $max_name_length)
        $name = substr($name, 0, $max_name_length);

    return preg_replace($r_match, $r_repl, func_translit($name, false, $hc_state['name_delim']));
}

/**
 * Generate filename for a category page
 */
function category_filename($cat, $cat_name, $page = 1, $sort_field, $sort_direction){
    global $sql_tbl, $config, $hc_state;

    if (empty($cat_name))
        $cat_name = func_query_first_cell("SELECT category FROM $sql_tbl[categories] where categoryid='$cat'");

    if (empty($cat_name))
        $cat_name = $cat;

    if (empty($cat_name) && $page == 1 && $sort_field == $config['Appearance']['products_order'] && $sort_direction == 0)
        return 'index.html';

    if (empty($cat_name))
        $cat_name = 'index';

    return func_process_page_name_template(
        'category',
        $hc_state['templates']['category'],
        $hc_state['name_delim'],
        array(
            'category' => normalize_name($cat_name),
            'order' => func_assemble_order_field($sort_field, $sort_direction),
            'page' => $page,
            'categoryid' => $cat
        )
    );
}

/**
 * Generate filename for a product page
 */

function product_filename($productid, $prod_name=false){
    global $sql_tbl, $hc_state;

    if (empty($prod_name))
        $prod_name = func_query_first_cell("SELECT product FROM $sql_tbl[products] WHERE productid = '$productid'");

    if (empty($prod_name))
        $prod_name = $productid;

    return func_process_page_name_template(
        'product',
        $hc_state['templates']['product'],
        $hc_state['name_delim'],
        array(
            'product' => normalize_name($prod_name),
            'productid' => $productid
        )
    );
}

function staticpage_filename($pageid, $page_name=false)
{
    global $sql_tbl, $hc_state, $current_lng;

    if (empty($page_name)) {
        $page_name = func_query_first_cell("SELECT title FROM $sql_tbl[pages] WHERE active = 'Y' AND pageid='$pageid' AND level='E' AND language='$current_lng'");
        if (empty($page_name)) {
            $page_name_other = func_query_first("SELECT p2.title, p2.pageid FROM $sql_tbl[pages], $sql_tbl[pages] as p2 WHERE $sql_tbl[pages].active = 'Y' AND $sql_tbl[pages].pageid='$pageid' AND $sql_tbl[pages].level='E' AND $sql_tbl[pages].filename = p2.filename AND p2.level='E' AND p2.language='$current_lng' AND p2.active = 'Y'");
            if (!empty($page_name_other)) {
                $page_name = $page_name_other['title'];
                $pageid = $page_name_other['pageid'];
            }
        }
    }

    if (empty($page_name))
        return 'index.html';

    return func_process_page_name_template(
        'staticpage',
        $hc_state['templates']['staticpage'],
        $hc_state['name_delim'],
        array(
            'page' => normalize_name($page_name),
            'pageid' => $pageid
        )
    );
}

function manufacturer_filename($id, $name, $page, $sort_field, $sort_direction)
{
    global $sql_tbl, $hc_state, $current_lng;

    if (empty($name))
        $name = func_query_first_cell("SELECT manufacturer FROM $sql_tbl[manufacturers_lng] WHERE manufacturerid='$id' AND code='$current_lng'");

    if (empty($name))
        $name = func_query_first_cell("SELECT manufacturer FROM $sql_tbl[manufacturers] WHERE manufacturerid='$id'");

    if (empty($name))
        $name = $id;

    return func_process_page_name_template(
        'manufacturer',
        $hc_state['templates']['manufacturer'],
        $hc_state['name_delim'],
        array(
            'manufacturer' => normalize_name($name),
            'order' => func_assemble_order_field($sort_field, $sort_direction),
            'page' => $page,
            'manufacturerid' => $id
        )
    );
}

function category_callback($found)
{
    global $hc_state, $config;

    if (!func_is_current_shop($found[2]))
        return $found[1].$found[2]."?".$found[3].$found[4];

    $cat = false;
    $fn = array(0,1);
    $sort = array($config['Appearance']['products_order'], 0);
    if (preg_match("/cat=([0-9]+)/S",$found[3], $m)) $fn[0] = $cat = $m[1];
    if (preg_match("/page=([0-9]+)/S",$found[3], $m)) $fn[1] = $m[1];
    if (preg_match("/sort=([\w\d_]+)/S",$found[3], $m)) $sort[0] = $m[1];
    if (preg_match("/sort_direction=([01]+)/S",$found[3], $m)) $sort[1] = $m[1];

    return $found[1].$hc_state['catalog']['webpath'].category_filename($fn[0],false,$fn[1], $sort[0], $sort[1]).$found[4];
}

function product_callback($found)
{
    global $hc_state;

    if (!func_is_current_shop($found[2]))
        return $found[1].$found[2]."?".$found[3].$found[4];

    if (preg_match("/productid=(\d+)/S", $found[3], $m))
        return $found[1].$hc_state['catalog']['webpath'].product_filename($m[1]).$found[4];

    return $found[1].$found[4];
}

function staticpage_callback($found)
{
    global $hc_state;

    if (!func_is_current_shop($found[2]))
        return $found[1].$found[2]."?".$found[3].$found[4];

    if (preg_match("/pageid=(\d+)/S", $found[3], $m))
        return $found[1].$hc_state['catalog']['webpath'].staticpage_filename($m[1]).$found[4];

    return $found[1].$found[4];
}

function manufacturer_callback($found)
{
    global $hc_state, $config;

    if (!func_is_current_shop($found[2]))
        return $found[1].$found[2]."?".$found[3].$found[4];

    if (preg_match("/manufacturerid=(\d+)/S", $found[3], $m)) {

        $id = $m[1];
        $page = 1;
        $sort_field = $config['Appearance']['products_order'];
        $sort_direction = 0;
        if (preg_match("/page=(\d+)/S", $found[3], $m))
            $page = $m[1];

        if (preg_match("/sort=([\w\d_]+)/S", $found[3], $m))
            $sort_field = $m[1];

        if (preg_match("/sort_direction=([01]+)/S", $found[3], $m))
            $sort_direction = $m[1];

        return $found[1].$hc_state['catalog']['webpath'].manufacturer_filename($id, false, $page, $sort_field, $sort_direction).$found[4];
    }

    return $found[1].$found[4];
}

function add_html_location($found)
{
    global $site_location, $hc_state, $xcart_catalogs;

    $xcart_web_location = $site_location['scheme']."://".$site_location['host'].$hc_state["catalog"]["webpath"];
    if (substr($xcart_web_location, -1, 1) != '/')
        $xcart_web_location .= '/';

    if (strpos(trim($found[2]), '#') === 0)
        return $found[1].trim($found[2]).$found[3];

    if (strstr($found[2], $site_location['path']) == "") {
        if (substr($found[2], 0, 1) == '/')
            return $found[1].$site_location['scheme']."://".$site_location['host'].$found[2].$found[3];

        $p = $xcart_catalogs['customer'];
        if (substr($p, -1, 1) != '/')
            $p .= '/';

        $found[2] = $p.$found[2];
    }

    return $found[1].$found[2].$found[3];
}

function add_html_location_compat($found)
{
    return add_html_location(
        array(
            $found[0],
            $found[1].'"',
            trim($found[2]),
            '"'.$found[3]
        )
    );
}

function make_page_name($name_func, $name_params, $lng_code=null)
{
    global $current_lng;

    if (empty($name_func)) {
        $page_name = 'index.html';

    } else {
        if (!is_null($lng_code)) {
            $saved_lng = $current_lng;
            $current_lng = $lng_code;
            $page_name = call_user_func_array($name_func.'_filename', $name_params);

            $current_lng = $saved_lng;

        } else {
            $page_name = call_user_func_array($name_func.'_filename', $name_params);
        }

    }

    return $page_name;
}

/**
 * Modify hyperlinksks to point to HTML pages of the catalogue
 */
function process_page($page_src, $page_name, $name_func, $name_params)
{
    global $php_scripts_long, $active_modules, $xcart_web_dir;
    global $XCART_SESSION_NAME;
    global $site_location;
    global $hc_state;
    global $current_lng;
    global $config;
    global $xcart_http_host;
    $webdir = (empty($xcart_web_dir) ? '/' : $xcart_web_dir);

    $js = <<<JS
<script type="text/javascript">
<!--
function setNewLng()
{
    var d = new Date(new Date().getTime() + 31536000000);
    setCookie('store_language', '$current_lng', '$webdir', d, '$xcart_http_host');
    if (!getCookie('RefererCookie') && document.referrer != '')
        setCookie('RefererCookie', document.referrer, '$webdir', d, '$xcart_http_host');
}

if (window.addEventListener)
    window.addEventListener('load', setNewLng, false);
else if (window.attachEvent)
    window.attachEvent('onload', setNewLng);
else
    setTimeout(setNewLng, 1000);
-->
</script>
</head>
JS;

    $page_src = preg_replace("/<\/head>/Ss", $js, $page_src);

    // Convert the "select language" block

    if ($hc_state['remove_slform']) {
        $page_src = preg_replace("!<div[^<>]*class=[^<>]*languages (languages-row|languages-flags|languages-select).*</div>!isUS", '', $page_src);
    } else {
        if ($config['Appearance']['line_language_selector'] == 'N')
            $page_src = preg_replace('!(<select[^<>]*name=[^<>]*sl.*)javascript: this.form.submit\(\)(;[\">])!isUS',"\\1javascript: window.location=this.form.sl.value\\2",$page_src);

        foreach ($hc_state['catalog_dirs'] as $inst) {
            $lng_page_name = $page_name;
            if ($current_lng != $inst['code']) {
                $lng_name_params = $name_params;
                $lng_name_params[1] = false; // remove name of item
                $lng_page_name = make_page_name($name_func, $lng_name_params, $inst['code']);
            }

            $path = $inst['webpath'].$lng_page_name;

            // Change language link to language path
            if ($config['Appearance']['line_language_selector'] == 'N')
                $page_src = preg_replace("!(<form[^<>]*name=[^<>]*sl_form.*<option[^<>]*value=\")".$inst['code']."(\".*</form>)!isUS","\\1".$path."\\2",$page_src);
            else
                $page_src = preg_replace('!(<a[^<>]+href[ ]*=[ ]*["\'])[^"\']*home.php\?sl=' . $inst["code"] . '(["\'].*</a>)!isUS', "\\1" . $path . "\\2", $page_src);

            $updated_lng[] = $inst['code'];
        }

        if (isset($hc_state['remove_lng_line'])) {
            // Remove unused languages
            if ($config['Appearance']['line_language_selector'] == 'N')
                $page_src = preg_replace("!<option[^<>]*value=\"(".$hc_state['remove_lng_line'].")\".*</option>!isUS",'',$page_src);
            else
                $page_src = preg_replace('!<a[^<>]+href[ ]*=[ ]*["\'][^"\']*home.php\?sl=(' . $hc_state["remove_lng_line"] . ')["\'].*</a>!isUS', "", $page_src);
        }
    }

    $page_src = preg_replace('/(<a[^<>]+href[ ]*=[ ]*["\']*)[^"\']*home.php(["\'])/iS', "\\1".$hc_state["catalog"]["webpath"]."index.html\\2", $page_src);

    // Modify links to categories
    $page_src = preg_replace_callback('/(<a[^<>]+href[ ]*=[ ]*["\'])([^"\']*home.php)\?(cat=[^"\'>]+)((#[^"\'>]+)?["\'])/iUS', "category_callback", $page_src);
    // FancyCategories links
    $page_src = preg_replace_callback('/((?:window|self).location[ ]*=[ ]*["\'])([^"\']*home.php)\?(cat=[^"\'>]+)((#[^"\'>]+)?["\'])/iUS', "category_callback", $page_src);

    // Modify links to products
    $page_src = preg_replace_callback('/(<a[^<>]+href[ ]*=[ ]*["\']?)([^"\']*product.php)\?(productid=[^"\'>]+)((#[^"\'>]+)?["\'>])/iUS', "product_callback", $page_src);

    if ($hc_state['process_staticpages']) {
        // Modify links to static_pages
        $page_src = preg_replace_callback('/(<a[^<>]+href[ ]*=[ ]*["\']?)([^"\']*pages.php)\?(pageid=[^"\'>]+)((#[^"\'>]+)?["\'>])/iUS', "staticpage_callback", $page_src);
    }

    // Manufacturers
    if ($hc_state['process_manufacturers'] && !empty($active_modules['Manufacturers'])) {
        $page_src = preg_replace_callback('/(<a[^<>]+href[ ]*=[ ]*["\']?)([^"\']*manufacturers.php)\?(manufacturerid=[^"\'>]+)((#[^"\'>]+)?["\'>])/iUS', "manufacturer_callback", $page_src);
    }

    // Modify links to PHP scripts

    $_path = $site_location['path'].DIR_CUSTOMER;
    if (substr($_path, -1, 1) != '/')
        $_path .= '/';

    $page_src = preg_replace("/<a(.+)href[ ]*=[ ]*[\"'](".$php_scripts_long.")([^\"^']*)[\"']/iUS", "<a\\1href=\"".$_path."\\2\\3\"", $page_src);
    $page_src = preg_replace("/self\.location[ ]*=[ ]*([\"'])(".$php_scripts_long.")([^\"^']*)[\"']/iUS", "self.location=\\1".$_path."\\2\\3\\1", $page_src);
    $page_src = preg_replace("/<img(.+)src[ ]*=[ ]*[\"'](".$php_scripts_long.")([^\"^']*)[\"']/iUS", "<img\\1src=\"".$_path."\\2\\3\"", $page_src);

    $page_src = preg_replace("/<a(.+)href[ ]*=[ ]*(".$php_scripts_long.")([^ >]*)([ >])/iUS", "<a\\1href=\"".$_path."\\2\\3\"\\4", $page_src);
    $page_src = preg_replace("/<img(.+)src[ ]*=[ ]*(".$php_scripts_long.")([^ >]*)([ >])/iUS", "<img\\1src=\"".$_path."\\2\\3\"\\4", $page_src);

    // Modify action values in HTML forms

    $page_src = preg_replace("/action[ \t]*=[ \t]*[\"'](".$php_scripts_long.")([^\"^']*)[\"']/iUS", "action=\"".$_path."\\1\\2\"", $page_src);
    $page_src = preg_replace("/action[ \t]*=[ \t]*(".$php_scripts_long.")([^ >]*)([ >])/iUS", "action=\"".$_path."\\1\\2\"\\4", $page_src);

    // Strip all PHP transsids if any
    while (preg_match("/<a(.+)href[ ]*=[ ]*[\"']([^\"^']*)(\?".$XCART_SESSION_NAME."=|&".$XCART_SESSION_NAME."=)([^\"^']*)[\"']/iS", $page_src))
        $page_src = preg_replace("/<a(.+)href[ \t]*=[ \t]*[\"']*([^\"^']*)(\?".$XCART_SESSION_NAME."=|&".$XCART_SESSION_NAME."=)([^\"^']*)[\"']/iS", "<a\\1href=\"\\2\"", $page_src);

    $page_src = preg_replace("/<input[ \t]+type\s*=\s*[\"']?hidden[\"']?[ \t]+name\s*=\s*[\"']?".$XCART_SESSION_NAME."[\"']?[ \t]+value\s*=\s*[\"']?[\da-fA-F]*[\"']?[ \t]*[\/]?>/iS", "", $page_src);

    $page_src = preg_replace("/(<form[ \t][^>]+>)/Ssi", "\\1<input type=\"hidden\" name=\"is_hc\" value=\"Y\" />", $page_src);

    // Modify relative links to absolute links
    $page_src = preg_replace_callback("/(<a[^<>]+href[\s]*=[\s]*[\"']+)([^\"':]+)([\"'])/i", "add_html_location", $page_src);
    $page_src = preg_replace_callback("/(<img[^<>]+src[\s]*=[\s]*[\"']+)([^\"':]+)([\"'])/i", "add_html_location", $page_src);
    $page_src = preg_replace_callback("/(<script[^<>]+src[\s]*=[\s]*[\"']+)([^\"':+]+)([\"'])/i", "add_html_location", $page_src);

    $page_src = preg_replace_callback("/(<a[^<>]+href[\s]*=[\s]*)([^ \"'>:]+)([ >])/i", "add_html_location_compat", $page_src);
    $page_src = preg_replace_callback("/(<img[^<>]+src[\s]*=[\s]*)([^ \"'>:]+)([ >])/i", "add_html_location_compat", $page_src);
    $page_src = preg_replace_callback("/(<script[^<>]+src[\s]*=[\s]*)([^ \"'>:]+)([ >])/i", "add_html_location_compat", $page_src);

    // Replace URLs to additional pages by hc ones
    // @example in modules/Sitemap/config.php | modules/Sitemap/func.php
    global $additional_hc_data;
    if (is_array($additional_hc_data)) {
        foreach ($additional_hc_data as $additional_hc_data_current) {
            $function = isset($additional_hc_data_current['src_func']) ? $additional_hc_data_current['src_func'] : '';
            if (function_exists($function)) {
                $page_src = $function($additional_hc_data_current, $page_src);
            }
        }
    }
    
    return $page_src;
}

function convert_page($store_path, $page_src, $name_func, $name_params=array())
{
    global $hc_state;

    if (empty($store_path))
        $store_path = $hc_state['catalog']['path'];

    $page_name = make_page_name($name_func, $name_params);
    $page_src = process_page($page_src, $page_name, $name_func, $name_params);
    my_save_data($store_path.'/'.$page_name, $page_src);
}

/**
 * Detect sort page fields
 */
function get_sorts($page_src, $page_name = 'home.php')
{
    if (preg_match_all('/'.preg_quote($page_name, '/')."\?[^\"' >]+&(?:amp;)?sort\=([\w\d_]+)/S", $page_src, $match))
        return array_unique($match[1]);

    return array();
}

function get_category_featured_products($category_id)
{
    global $sql_tbl;

    $result = array();
    $featured_products = db_query("SELECT $sql_tbl[products].productid, $sql_tbl[products].product FROM  $sql_tbl[products], $sql_tbl[featured_products] WHERE $sql_tbl[products].productid=$sql_tbl[featured_products].productid AND $sql_tbl[featured_products].avail='Y' AND $sql_tbl[featured_products].categoryid='".intval($category_id)."' AND $sql_tbl[products].forsale = 'Y' ");
    while ($_featured_products = db_fetch_array($featured_products)) {
        $result[$_featured_products['productid']] = $_featured_products['product'];
    }

    db_free_result($featured_products);
    return $result;
}

function get_category_bestsellers($category_id)
{
    global $xcart_dir, $active_modules, $config, $sql_tbl, $smarty;

    $result = array();
    $cat = $category_id;
    if ($active_modules['Bestsellers'])
        include $xcart_dir.'/modules/Bestsellers/bestsellers.php';

    if (is_array($bestsellers)) {
        foreach ($bestsellers as $bs) {
            $result[$bs['productid']] = $bs['product'];
        }
    }
    return $result;
}

/**
 * Check page name template
 */
function func_check_page_name_template($type, $template)
{
    global $templates_data, $template_max_length;

    if (!is_string($type) || strlen($type) == 0)
        return false;

    if (!is_string($template) || strlen($template) == 0)
        return false;

    if (!isset($templates_data[$type]))
        return false;

    $clean_template = $template;
    foreach ($templates_data[$type]['keywords'] as $k => $kdata) {
        if ($kdata['required'] && strpos($template, '{'.$k.'}') === false)
            return false;

        $clean_template = str_replace('{'.$k.'}', '', $clean_template);
    }

    // check template format
    if (!zerolen($clean_template) && !preg_match("/^[a-zA-Z0-9_\-\.]+$/S", $clean_template))
        return false;

    // check length
    if (strlen($template) > $template_max_length)
        return false;

    return true;
}

/**
 * Get saved page name templates
 */
function func_get_saved_page_name_templates()
{
    global $config, $templates_data;

    $templates = unserialize($config['html_catalog_templates']);
    if (!is_array($templates))
        $templates = array();

    foreach ($templates_data as $k => $dt) {
        if (!isset($templates[$k]) || !func_check_page_name_template($k, $templates[$k]))
            $templates[$k] = $dt['default_template'];
    }

    return $templates;
}

function func_save_page_name_templates($templates)
{
    global $sql_tbl, $config, $templates_data;

    if (!is_array($templates))
        return false;

    // Check template keys
    $diff = array_diff(array_keys($templates_data), array_keys($templates));
    if (count($diff) > 0)
        return false;

    $result = array();
    foreach ($templates as $k => $t) {
        if (!isset($templates_data[$k])) {
            unset($templates[$k]);

        } elseif (func_check_page_name_template($k, $t)) {
            $result[] = $k;

        } else {
            $templates[$k] = $templates_data[$k]['default_template'];
        }
    }

    $config['html_catalog_templates'] = serialize($templates);

    func_array2insert(
        'config',
        array(
            'name' => 'html_catalog_templates',
            'value' => addslashes($config['html_catalog_templates'])
        ),
        true
    );

    return $result;
}

/**
 * Check name delimiter
 */
function func_check_name_delim($name_delim)
{
    global $name_delimiters;
    return is_string($name_delim) && in_array($name_delim, array_keys($name_delimiters));
}

/**
 * Get saved name delimiter
 */
function func_get_saved_name_delim()
{
    global $config, $name_delimiters;

    $name_delim = $config['html_catalog_name_delim'];
    if (!func_check_name_delim($name_delim)) {
        $tmp = array_keys($name_delimiters);
        $name_delim = array_shift($tmp);
    }

    return $name_delim;
}

/**
 * Save name delimite Save name delimiter
 */
function func_save_name_delim($name_delim)
{
    global $config, $sql_tbl;

    if (!func_check_name_delim($name_delim))
        return false;

    $config['html_catalog_name_delim'] = $name_delim;

    func_array2insert(
        'config',
        array(
            'name' => 'html_catalog_name_delim',
            'value' => addslashes($config['html_catalog_name_delim'])
        ),
        true
    );

    return true;
}

/**
 * Process page name template
 */
function func_process_page_name_template($type, $template, $name_delim, $data)
{
    global $templates_data;

    if (!is_string($type) || !isset($templates_data[$type]))
        return false;

    if (!func_check_page_name_template($type, $template))
        return false;

    if (!func_check_name_delim($name_delim))
        return false;

    if (!is_array($data))
        return false;

    $result = $template . '.html';

    // replace keywords
    foreach ($templates_data[$type]['keywords'] as $k => $d) {
        if (!isset($data[$d['datafield']])) {
            return false;
}

        $result = str_replace('{'.$k.'}', $data[$d['datafield']], $result);
    }

    // replace name delimiter
    $name_delim_quoted = preg_quote($name_delim, '/');
    $result = preg_replace(
        array("/[ \/" . $name_delim_quoted . "]+/S", "/[^A-Za-z0-9_\-\." . $name_delim_quoted . "]+/S"),
        array($name_delim, ''),
        $result
    );

    return $result;
}

/**
 * Assemble order field
 */
function func_assemble_order_field($order_field, $order_direction)
{
    if (!is_string($order_field) || strlen($order_field) == 0)
        return false;

    if ($order_direction !== '1' && $order_direction !== '0' && $order_direction !== 1 && $order_direction !== 0)
        return false;

    if (is_string($order_direction))
        $order_direction = intval($order_direction);

    return $order_field.'_'.($order_direction ? '1' : '0');
}

/**
 * Get keywords list from template
 */
function func_get_keywords_from_template($type, $template)
{
    global $templates_data;

    if (!func_check_page_name_template($type, $template))
        return array();

    $used = array();
    foreach ($templates_data[$type]['keywords'] as $k => $kd) {
        if (strpos($template, '{' . $k . '}') !== false)
            $used[] = $k;
    }

    return $used;
}
?>
