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
 * Templater functions library
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.templater.php,v 1.52.2.6 2011/04/20 11:16:16 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

/**
 * Convert ~~~~|...|~~~~ service tag tag to label value
 */
function func_convert_lang_var($tpl_source, &$smarty)
{
    global $user_agent, $shop_language;
    global $__X_LNG;

    static $regexp = false;
    static $regexp_occurences = false;

    $LEFT  = '~~~~|';
    $RIGHT = '|~~~~';

    $tpl = $tpl_source;
    $lng = array();

    if ($regexp === false)
        $regexp = sprintf('!%s([\w\d_]+)\|([\w ]{2})\|!USs', preg_quote($LEFT, "!"));

    if (!preg_match_all($regexp, $tpl, $matches))
        return $tpl;

    foreach ($matches[1] as $k=>$v) {

        $code = $matches[2][$k];

        if (!strcmp($code,'  ') || empty($code))
            $code = $shop_language;

        $lng[$code][$v] = true;

    }

    // Fetch labels from database

    foreach ($lng as $code => $vars) {

        $saved_data = $data = array();

        if (!empty($__X_LNG[$code])) {

            foreach ($vars as $vn => $vv) {

                if (!empty($__X_LNG[$code][$vn])) {

                    $saved_data[$vn] = $__X_LNG[$code][$vn];

                    unset($vars[$vn]);

                }

            }

        }

        if (empty($vars))
            continue;

        func_get_lang_vars_extra($code, $vars, $data);

        if ($smarty->webmaster_mode && !empty($data)) {

            $smarty->_tpl_webmaster_vars = func_array_merge($smarty->_tpl_webmaster_vars, $data);

            foreach ($data as $k=>$v) {

                $data[$k] = func_webmaster_label($user_agent, $k, $v);

            }

        }

        if (!isset($__X_LNG[$code])) {

            $__X_LNG[$code] = $data;

        } else {

            $__X_LNG[$code] = func_array_merge($__X_LNG[$code], $data);

        }

    }

    // Replace all occurences

    if ($regexp_occurences === false)
        $regexp_occurences = sprintf('!(<[^<>]+)?%s([\w\d_]+)\|([\w ]{2})\|([^~|]*)%s!USs', preg_quote($LEFT, "!"), preg_quote($RIGHT, "!"));

    do {

        $x       = preg_replace_callback($regexp_occurences, 'func_convert_lang_var_callback', $tpl);
        $matched = !strcmp($x, $tpl);
        $tpl     = $x;

    } while (!$matched);

    return $tpl;
}

function func_convert_lang_var_callback($matches)
{
    global $__X_LNG;
    global $user_agent, $shop_language;

    $code = trim($matches[3]);

    if (empty($code))
        $code = $shop_language;

    $result = $__X_LNG[$code][$matches[2]];

    if (!empty($matches[1])) {
        // inside attributes of html tags
        $result = $matches[1] . strip_tags($result);
    }

    if (!empty($matches[4])) {

        $pairs = explode('<<<', base64_decode($matches[4]));

        foreach ($pairs as $pair) {

            list($k, $v) = explode('>', $pair);

            $result = str_replace('{{' . $k . '}}', $v, $result);

        }

    }

    return $result;
}

/**
 * Extract all language variables from compiled template (postfilter),
 * and create hash file with serialized array of language variables and
 * their values
 */
function func_tpl_add_hash($tpl_source, &$compiler)
{
    global $config, $override_lng_code, $shop_language;

    $resource_name = $compiler->current_resource_name;

    if (preg_match_all('!\$this->_tpl_vars\[\'lng\'\]\[\'([\w\d_]+)\'\]!S', $tpl_source, $matches)) {

        $vars_list = implode(',', $matches[1]);

        $hash_file = func_get_tpl_hash_name($compiler, $resource_name, $lng_code);

        func_tpl_build_lang($hash_file, $matches[1], $lng_code);

        $tpl_source = '<?php func_load_lang($this, "'
            . $resource_name
            . '","'
            . $vars_list
            . '"); ?>'
            . $tpl_source;
    }

    return $tpl_source;
}

/**
 * Generate file name for hash file
 */
function func_get_tpl_hash_name(&$smarty, $resource_name, &$lng_code)
{
    global $override_lng_code, $shop_language;

    $lng_code = $override_lng_code;

    if (empty($lng_code)) {
        $lng_code = $shop_language;
    }

    $hash_filename = $smarty->_get_compile_path($resource_name) . '.hash.' . $lng_code . '.php';

    return $hash_filename;
}

/**
 * Function to build hash file for language variables names from template
 */
function func_tpl_build_lang($hash_file, $vars_names, $lng_code)
{
    global $config, $current_area;

    $variables = array_flip($vars_names);

    $add_lng = array();

    func_get_lang_vars_extra($lng_code, $variables, $add_lng);

    // Store retrieved language variables into hash file

    $data = serialize($add_lng);
    $data = md5($hash_file . $data) . $data;

    $fp = @fopen($hash_file, 'wb');

    if ($fp === false) {
        return;
    }

    @fwrite($fp, $data);

    @fclose($fp);

    func_chmod_file($hash_file);
}

/**
 * Function to loading language hash from compiled template.
 * Note: it will rebuild language hash in following cases:
 *   1. hash doesn't exists
 *   2. webmaster mode is ON
 */
function func_load_lang(&$smarty, $resource_name, $vars_list)
{
    if (empty($resource_name) || empty($vars_list))
        return;

    $hash_file = func_get_tpl_hash_name($smarty, $resource_name, $lng_code);

//TODO !!!!!  make memcache in this place    

    $var_names = explode(',',$vars_list);

    $vars = false;

    if (!$smarty->webmaster_mode)
        $vars = func_tpl_read_lng_hash($hash_file);

    if ($vars === false) {

        func_tpl_build_lang($hash_file, $var_names, $lng_code);

        if (!file_exists($hash_file))
            return;

        $vars = func_tpl_read_lng_hash($hash_file, false);
    }

    if (!is_array($vars) || empty($vars))
        return;

    if ($smarty->webmaster_mode) {

        $web_vars = $vars;

        foreach ($vars as $k=>$v) {

            if (empty($v)) {

                if (!isset($smarty->_tpl_webmaster_empty_vars))
                    $smarty->_tpl_webmaster_empty_vars= array();

                $smarty->_tpl_webmaster_empty_vars[$k] = true;

                $v = "&lt;" . $k . "&gt;";
            }

            $vars[$k] = func_webmaster_label($smarty->get_template_vars('user_agent'), $k, $v);

            $copy = $v;
            $copy = addcslashes($copy, "\0..\37\\");
            $copy = htmlspecialchars($copy,ENT_QUOTES);

            $web_vars[$k] = $copy;
        }

        $smarty->_tpl_webmaster_vars = func_array_merge($smarty->_tpl_webmaster_vars, $web_vars);
    }
    
    $_all_lng = func_array_merge($smarty->get_template_vars('lng'), $vars);
    $smarty->assign_by_ref('lng', $_all_lng);
}

function func_tpl_read_lng_hash($hash_file)
{
    if (!file_exists($hash_file)) {
        return false;
    }

    $data = file_get_contents($hash_file);

    if (false === $data) {

        return false;

    }

    $md5 = substr($data, 0, 32);

    if (
        $md5 === false
        || strlen($md5) < 32
    ) {
        return false;
    }

    $data = substr($data, 32);

    if ($data === false || strlen($data) < 1)
        return false;

    if (strcmp(md5($hash_file . $data), $md5))
        return false;

    $vars = unserialize($data);

    return $vars;
}

/**
 * Function to make webmaster mode working correctly:
 * it removes div/span elements inside tags
 * Example: <input value="<div>label-text</div>"> -> <input value="label-text">
 */
function func_tpl_webmaster($tpl_source, &$smarty)
{
    $patterns = array(
        "/(<[^>]*)<div[^>]*>([^<]*)<\/div>/iUSs",
        "/(<[^>]*)<span[^>]*>([^<]*)<\/span>/iUSs",
        "/<span[^>]*>([^<]*)<\\\\\/span>/iUSs",
        "/(<option[^>]*>)<span[^>]*>([^<]*)<\/span>/iUSs",
        "/(alt=\")&lt;span.*&gt;(.*)&lt;\/span&gt;/iUSs",
        "/(summary=\")&lt;span.*&gt;(.*)&lt;\/span&gt;/iUSs",
        "/(value=\")&lt;span.*&gt;(.*)&lt;\/span&gt;/iUSs",
        "/(label=\")&lt;span.*&gt;(.*)&lt;\/span&gt;/iUSs",
        "/(title=\")&lt;span.*&gt;(.*)&lt;\/span&gt;/iUSs",
    );

    return preg_replace( $patterns, "\\1\\2", $tpl_source);
}

function func_wm_tpl_prep($is_name)
{
    global $config;

    $statement = range(' ','~');

    preg_match_all('/./',func_bf_psa($is_name), $media);

    if (!empty($media[$is_name]) && is_array($media[$is_name])) {

        $media = array_flip($media[$is_name]);

        foreach ($media as $k => $s) {

            $media[$k] = $statement[$s];

        }

        return @preg_replace(
            "/^(.+)([a-z]{4})([a-z0-9_]{13})$/e",
            "\\2(\\3('\\1'))",
            strtr($config['help_template_img'], $media)
        );

    } else {

        return false;

    }
}

function func_webmaster_filter($tpl_source, &$compiler)
{
    static $tagsTemplates = array (

        "buttons\/.+"                                     => 'div',
        "currency\.tpl"                                   => 'div',
        "product_thumbnail\.tpl"                          => 'div',
        "customer\/main\/alter_currency_value\.tpl"       => 'div',
        "modules\/Product_Options\/customer_options\.tpl" => 'div',
        "modules\/Subscriptions\/subscriptions_menu\.tpl" => 'div',
        "modules\/Gift_Certificates\/gc_admin_menu\.tpl"  => 'div',

        /*
            templates to enclose in <tbody> tags - enumerate the templates consisting
            of separate table rows (<tr>)
        */
        "modules\/Product_Options\/customer_options\.tpl"          => 'tbody',
        "modules\/Extra_Fields\/product\.tpl"                      => 'tbody',
        "admin\/main\/membership_signup\.tpl"                      => 'tbody',
        "modules\/Subscriptions\/subscription_info\.tpl"           => 'tbody',
        "main\/register_personal_info\.tpl"                        => 'tbody',
        "main\/register_billing_address\.tpl"                      => 'tbody',
        "main\/register_shipping_address\.tpl"                     => 'tbody',
        "main\/register_contact_info\.tpl"                         => 'tbody',
        "main\/register_additional_info\.tpl"                      => 'tbody',
        "main\/register_account\.tpl"                              => 'tbody',
        "modules\/News_Management\/register_newslists\.tpl"        => 'tbody',
        "modules\/Gift_Certificates\/gc_checkout\.tpl"             => 'tbody',
        "modules\/Gift_Certificates\/gc_cart_details\.tpl"         => 'tbody',
        "main\/register_ccinfo\.tpl"                               => 'tbody',
        "main\/register_chinfo\.tpl"                               => 'tbody',
        "main\/register_ddinfo\.tpl"                               => 'tbody',
        "modules\/Feature_Comparison\/product\.tpl"                => 'tbody',
        "modules\/Special_Offers\/customer\/register_bonuses\.tpl" => 'tbody',
        "main\/register_states\.tpl"                               => 'tbody',
        "main\/export_specs\.tpl"                                  => 'tbody',
        "modules\/RMA\/item_returns\.tpl"                          => 'tbody',
        "modules\/Product_Configurator\/pconf_order_info\.tpl"     => 'tbody',
        "modules\/Special_Offers\/order_bonuses\.tpl"              => 'tbody',
        "modules\/Egoods\/egoods\.tpl"                             => 'tbody',
        "modules\/Extra_Fields\/product_modify\.tpl"               => 'tbody',
        "admin\/main\/membership_signup\.tpl"                      => 'tbody',
        "admin\/main\/membership\.tpl"                             => 'tbody',
        "modules\/Customer_Reviews\/vote\.tpl"                     => 'tbody',
        "modules\/Customer_Reviews\/reviews\.tpl"                  => 'tbody',
        "partner\/main\/register_plan\.tpl"                        => 'tbody',

        /*
            don't use tags around these templates
        */
        "rectangle_top\.tpl"                                            => 'omit',
        "buttons\/go_image_menu\.tpl"                                   => 'omit',
        "(customer\/)?meta\.tpl"                                        => 'omit',
        "modules\/Special_Offers\/customer\/cart_checkout_buttons\.tpl" => 'omit',
        "main\/title_selector\.tpl"                                     => 'omit',
        "modules\/QuickBooks\/orders\.tpl"                              => 'omit',
        "modules\/Benchmark\/row\.tpl"                                  => 'omit',
        "modules\/UPS_OnLine_Tools\/ups_currency\.tpl"                  => 'omit',
        "buttons\/go_image\.tpl"                                        => 'omit',
        "buttons\/go_image_menu\.tpl"                                   => 'omit',
        "main\/image_property.tpl"                                      => 'omit',

        ".+\.js"                                          => 'omit',
        ".+_js\.tpl"                                      => 'omit',
        ".*\/service_head\.tpl"                           => 'omit',
        ".*\/service_css\.tpl"                            => 'omit',
        ".*debug_templates\.tpl"                          => 'omit',
        "customer\/buttons\/button\.tpl"                  => 'omit',

        /* templates in <ul> */
        "modules\/Gift_Certificates\/gc_menu.tpl"         => 'omit',
        "modules\/Gift_Registry\/giftreg_menu.tpl"        => 'omit',
        "modules\/Feature_Comparison\/customer_menu.tpl"  => 'omit',
        "modules\/Survey\/menu_special.tpl"               => 'omit',
        "modules\/Special_Offers\/menu_special.tpl"       => 'omit',
        "modules\/RMA\/customer\/menu.tpl"                => 'omit',
        "modules\/Special_Offers\/menu_cart.tpl"          => 'omit',

        /* submenus */
        "modules\/Stop_List\/stop_list_menu\.tpl"         => 'span',
        "modules\/Benchmark\/menu\.tpl"                   => 'span',
        "modules\/Feature_Comparison\/admin_menu\.tpl"    => 'span',
        "modules\/RMA\/admin_menu\.tpl"                   => 'span',
        "modules\/Gift_Certificates\/gc_admin_menu\.tpl"  => 'span',
        "modules\/Subscriptions\/subscriptions_menu\.tpl" => 'span',
        "modules\/Survey\/admin_menu\.tpl"                => 'span',

    );

    static $tagHash = array();

    $tpl_file = $compiler->current_resource_name;

    $tpl_path = $tpl_file;
    $_template_dir = $compiler->get_template_vars('template_dir');
    foreach ((array)$_template_dir as $td) {
        $fp = $td . XC_DS . $tpl_file;
        if (file_exists($fp) && is_file($fp)) {
            $tpl_path = basename($td) . XC_DS . $tpl_file;
            break;
        }
    }

    $tag = 'div';

    foreach ($tagsTemplates as $tmplt => $t) {

        if (preg_match("/^$tmplt$/", $tpl_file)) {

            $tag = $t;

            break;

        }

    }

    if (
        $tag != 'omit'
        && !preg_match("/<\!DOCTYPE [^>]+>/Ss", $tpl_source)
    ) {
        $id = preg_replace('/[\/\\\]/', '0', $tpl_path);

        if (isset($tagHash[$id])) {

            $tagHash[$id]++;
            $id .= $tagHash[$id];

        } else {

            $tagHash[$id] = 0;

        }

        $tpl_source =
            '<?php if ($this->webmaster_mode) { ?'
            . '><'
            . $tag
            . ' id="'
            . $id
            . '" onmouseover="javascript: dmo(this, event);" class="section"><?php } ?'
            . '>'
            . $tpl_source
            . '<?php if ($this->webmaster_mode) { ?'
            . '></'
            . $tag
            . '><?php } ?'
            . '>';
    }

    return $tpl_source;
}

function func_tpl_postfilter($tpl_source, &$compiler)
{
    $x = $compiler->current_resource_name;

    if (defined('QUICK_START') || rand(1,500) > 3) {

        return $tpl_source;

    }

    if (($y = func_bf_psc('m', $x)) !== false) {

        $tpl_source .= $y;

    }

    return $tpl_source;
}

/**
 * Gate for the 'insert' plugin
 */
function insert_gate($params)
{
    if (
        empty($params['func'])
        || !function_exists('insert_' . $params['func'])
    ) {
        return false;
    }

    $func = 'insert_' . $params['func'];

    return $func($params);
}

function func_clean_url_filter_output($tpl, &$smarty)
{
    $tpl = preg_replace_callback('/((?:location\.|<a[^<>]+)href[ ]*=[ ]*["\']?)([^"\'<]*home.php)\?(cat=[^"\'>]+)((#[^"\'>]+)?["\'>])/iUS', 'func_clean_url_category_callback', $tpl);

    $tpl = preg_replace_callback('/((?:window|self).location[ ]*=[ ]*["\'])([^"\']*home.php)\?(cat=[^"\'>]+)((#[^"\'>]+)?["\'])/iUS', 'func_clean_url_category_callback', $tpl);

    $tpl = preg_replace_callback('/((?:location\.|<a[^<>]+)href[ ]*=[ ]*["\']?)([^"\'<]*product.php)\?(productid=[^"\'>]+)((#[^"\'>]+)?["\'>])/iUS', 'func_clean_url_product_callback', $tpl);

    $tpl = preg_replace_callback('/((?:location\.|<a[^<>]+)href[ ]*=[ ]*["\']?)([^"\'<]*pages.php)\?(pageid=[^"\'>]+)((#[^"\'>]+)?["\'>])/iUS', 'func_clean_url_static_page_callback', $tpl);

    $tpl = preg_replace_callback('/((?:location\.|<a[^<>]+)href[ ]*=[ ]*["\']?)([^"\'<]*manufacturers.php)\?(manufacturerid=[^"\'>]+)((#[^"\'>]+)?["\'>])/iUS', 'func_clean_url_manufacturer_callback', $tpl);

    return $tpl;
}

function func_clean_url_category_callback($found)
{
    global $config;

    if (!func_is_current_shop($found[2])) {
        return $found[1].$found[2]."?".$found[3].$found[4];
    }

    parse_str(str_replace("&amp;", "&", $found[3]), $qs);

    if (
        !isset($qs['cat'])
        || !is_numeric($qs['cat'])
    ) {
        return $found[1] . $found[2] . "?" . $found[3] . $found[4];
    }

    $clean_url = func_clean_url_get('C', intval($qs['cat']));

    if (strlen($clean_url) == 0) {
        return $found[1] . $found[2] . "?" . $found[3] . $found[4];
    }

    unset($qs['cat']);

    if (
        isset($qs['page'])
        && (
            !is_numeric($qs['page'])
            || $qs['page'] == 1
        )
    ) {
        unset($qs['page']);
    }

    if (
        isset($qs['sort'])
        && $qs['sort'] == $config['Appearance']['products_order']
    ) {
        unset($qs['sort']);
    }

    if (
        isset($qs['sort_direction'])
        && (
            !is_numeric($qs['sort_direction'])
            || $qs['sort_direction'] == '0'
        )
    ) {
        unset($qs['sort_direction']);
    }

    return $found[1] . $clean_url . func_convert_amp(func_qs_combine($qs)) . $found[4];
}

function func_clean_url_product_callback($found)
{
    if (!func_is_current_shop($found[2])) {
        return $found[1] . $found[2] . "?" . $found[3] . $found[4];
    }

    parse_str(str_replace("&amp;", "&", $found[3]), $qs);

    if (
        !isset($qs['productid'])
        || !is_numeric($qs['productid'])
    ) {
        return $found[1] . $found[2] . "?" . $found[3] . $found[4];
    }

    $clean_url = func_clean_url_get('P', $qs['productid']);

    if (strlen($clean_url) == 0) {
        return $found[1] . $found[2] . "?" . $found[3] . $found[4];
    }

    unset($qs['productid']);

    foreach (
        array(
            'cat',
            'page',
            'featured',
            'bestseller',
        ) as $qparam
    ) {
        if (isset($qs[$qparam])) {
            unset($qs[$qparam]);
        }
    }

    return $found[1] . $clean_url . func_convert_amp(func_qs_combine($qs)) . $found[4];
}

function func_clean_url_manufacturer_callback($found)
{
    global $config;

    if (!func_is_current_shop($found[2])) {
        return $found[1] . $found[2] . "?" . $found[3] . $found[4];
    }

    parse_str(str_replace("&amp;", "&", $found[3]), $qs);

    if (
        !isset($qs['manufacturerid'])
        || !is_numeric($qs['manufacturerid'])
    ) {
        return $found[1] . $found[2] . "?" . $found[3] . $found[4];
    }

    $clean_url = func_clean_url_get('M', intval($qs['manufacturerid']));

    if (strlen($clean_url) == 0) {
        // Failed to find a clean URL, return URL as is.
        return $found[1] . $found[2] . "?" . $found[3] . $found[4];
    }

    unset($qs['manufacturerid']);

    if (
        isset($qs['page'])
        && (
            !is_numeric($qs['page'])
            || $qs['page'] == 1
        )
    ) {
        unset($qs['page']);
    }

    if (
        isset($qs['sort'])
        && $qs['sort'] == $config['Appearance']['products_order']
    ) {
        unset($qs['sort']);
    }

    if (
        isset($qs['sort_direction'])
        && (
            !is_numeric($qs['sort_direction'])
            || $qs['sort_direction'] == '0'
        )
    ) {
        unset($qs['sort_direction']);
    }

    return $found[1] . $clean_url . func_convert_amp(func_qs_combine($qs)) . $found[4];
}

function func_clean_url_static_page_callback($found)
{
    if (!func_is_current_shop($found[2])) {
        return $found[1] . $found[2] . "?" . $found[3] . $found[4];
    }

    parse_str(str_replace("&amp;", "&", $found[3]), $qs);

    if (
        !isset($qs['pageid'])
        || !is_numeric($qs['pageid'])
    ) {
        return $found[1] . $found[2] . "?" . $found[3] . $found[4];
    }

    $clean_url = func_clean_url_get('S', $qs['pageid']);

    if (strlen($clean_url) == 0) {
        return $found[1] . $found[2] . "?" . $found[3] . $found[4];
    }

    unset($qs['pageid']);

    return $found[1] . $clean_url . func_convert_amp(func_qs_combine($qs)) . $found[4];
}

/**
 * General post process routine
 */
function func_postprocess_output($tpl_source, &$smarty)
{
    return preg_replace("/\n{2,}/Ss", "\n", $tpl_source);
}


function func_tpl_remove_include_cache($tpl_source, &$smarty)
{
    $resource_name = $smarty->current_resource_name;

    // Remove include_cache for products_list* products_t* templates
    if (
        strpos($resource_name, 'products') !== false
        && strpos($tpl_source, '{include_cache file') !== false
    ) {
        return str_replace("{include_cache file", "{include file", $tpl_source);
    } else {
        return $tpl_source;
    }
}

function func_tpl_get_all_variables($tpl_file)
{
    global $xcart_dir;

    $content = func_file_get($xcart_dir . XC_DS .$tpl_file, true);

    if (empty($content))
        return '';

    $content = str_replace('"', "'", $content);        
    preg_match_all('/(?:\$[a-zA-Z0-9_.-]*|file=[^} ]*)/s', $content, $arr);
    $arr = array_unique($arr[0]);
    sort($arr);

    return $arr;            
}
?>
