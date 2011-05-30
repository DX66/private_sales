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
 * Functions for Flyout menus module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.php,v 1.24.2.2 2011/01/10 13:11:57 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

define('X_FANCYCAT_CACHE_HEADER', "<?php if (!defined('XCART_START')) die(); ?>\n");

/**
 * Check whether caching is enabled for the 
 * generated categories tree 
 * 
 * @param mixed $skin Flyout menus skin name
 *  
 * @return bool
 * @see    ____func_see____
 */
function func_fc_use_cache($skin = false)
{
    global $fcat_module_path, $config, $xcart_dir, $smarty_skin_dir;

    if (!$skin)
        $skin = $config['Flyout_Menus']['fancy_categories_skin'];

    $path = $xcart_dir . $smarty_skin_dir . XC_DS . $fcat_module_path . XC_DS . $skin . XC_DS . 'config.ini';

    if (!file_exists($path))
        return false;

    $ini = func_parse_ini($path, true);

    return $ini['cache'] && $config["Flyout_Menus"]['fancy_cache'] == 'Y';
}

/**
 * Build subcategories data cache as JS-code 
 * 
 * @param int   $tick         Iteration counter (display dot)
 * @param mixed $membershipids Membership ids
 * @param mixed $languages    Languages
 *  
 * @return bool
 * @see    ____func_see____
 */
function func_fc_build_categories($tick = 0, $membershipids = false, $languages = false, $display_header = true)
{
    global $sql_tbl, $shop_language, $xcart_dir, $all_languages, $current_area, $smarty, $var_dirs, $config, $fcat_module_path;

    $path = $var_dirs['cache'];

    $tpl = $fcat_module_path . '/' . $config['Flyout_Menus']['fancy_categories_skin'] . '/';

    if (
        $config['Flyout_Menus']['fancy_categories_skin'] == 'Icons'
        && $config['Flyout_Menus']['icons_mode'] == 'C'
    ) {
        $tpl .= 'fancy_subcategories_exp.tpl';

    } else {

        $tpl .= 'fancy_subcategories.tpl';
    }

    $cat = 0;
    x_load('categories');


    // Get memberships list
    if (!is_array($membershipids)) {
        $tmp = func_get_memberships('C');
        $membershipids = array(0);
        if (!empty($tmp)) {
            foreach ($tmp as $mid) {
                $membershipids[] = $mid['membershipid'];
            }
        }
        unset($tmp);

    }

    // Get languages list
    if (!is_array($languages)) {

        $languages = array();

        foreach ($all_languages as $l) {
            $languages[] = $l['code'];
        }
    }

    if (count($membershipids) == 0 || count($languages) == 0) {
        return false;
    }

    // Display service header
    if ($display_header) {
        func_display_service_header(
            func_get_langvar_by_name(
                'lbl_rebuilding_subcategory_cache',
                array(
                    'mcount' => count($membershipids),
                    'lcount' => count($languages)
                ),
                false,
                true
            ),
            true
        );
    }

    $shop_language_old = $shop_language;
    $user_account_old  = $user_account;
    $current_area_old  = $current_area;
    $current_area      = 'C';

    // Disable 'fancy_cache' option
    $old_fancy_cache = func_query_first_cell("SELECT value FROM $sql_tbl[config] WHERE name = 'fancy_cache'");

    if ($old_fancy_cache == 'Y') {
        func_array2update('config', array('value' => 'N'), "name = 'fancy_cache'");
    }

    $i = 0;

    $cache_pre_path = $path . '/fc.' . $config['Flyout_Menus']['fancy_categories_skin'] . '.';

    foreach ($languages as $shop_language) {

        foreach ($membershipids as $mid) {

            $user_account['membershipid'] = $mid;

            $categories = func_fc_prepare_categories();

            $cache_path = $cache_pre_path . $user_account['membershipid'] . "." . $shop_language . ".php";
            $fp = @fopen($cache_path, 'w');
            if (!$fp) {
                break;
            }

            $smarty->assign('categories_menu_list', $categories);
            $smarty->assign('level', 0);

            fwrite($fp, X_FANCYCAT_CACHE_HEADER . func_display($tpl, $smarty, false));
            fclose($fp);
            func_chmod_file($cache_path, 0644);

            $i++;

            if ($tick > 0 && $i % $tick == 0) {
                func_flush('. ');
            }
        }
    }

    // Enable 'fancy_cache' option
    if ($old_fancy_cache == 'Y') {
        func_array2update('config', array('value' => 'Y'), "name = 'fancy_cache'");
    }

    $shop_language = $shop_language_old;
    $user_account  = $user_account_old;
    $current_area  = $current_area_old;

    return true;
}

/**
 * Remove subcategories cache 
 * 
 * @param int   $tick          Iteration counter (display dot)
 * @param mixed $categories    Categories array
 * @param mixed $membershipids Membership id
 * @param mixed $languages     Languages array
 *  
 * @return bool
 * @see    ____func_see____
 */
function func_fc_remove_cache($tick = 0, $categories = false, $membershipids = false, $languages = false)
{
    global $xcart_dir, $var_dirs;

    $path = $var_dirs['cache'];
    $dir = @opendir($path);

    if ($dir) {

        func_display_service_header('lbl_deleting_subcategory_cache');

        $i = 0;

        while ($file = readdir($dir)) {

            if ($file == '.' || $file == '..' || !preg_match("/^fc\.([^\.]+)\.(\d+)\.([\w]{2})\.php$/S", $file, $match))
                continue;

            if (
                (!empty($categories) && !in_array($match[4], $categories)) ||
                (!empty($membershipids) && !in_array($match[2], $membershipids)) ||
                (!empty($languages) && !in_array($match[3], $languages))
            ) {
                continue;
            }

            @unlink($path . XC_DS . $file);

            $i++;

            if ($tick > 0 && $i % $tick == 0) {
                func_flush('. ');
            }
        }

        closedir($dir);

        return true;
    }

    return false;
}

/**
 * Returns path to categories cache file 
 * 
 * @return string 
 * @see    ____func_see____
 */
function func_fc_get_cache_path()
{
    global $shop_language, $user_account, $var_dirs, $fcat_module_path, $config;

    return $var_dirs['cache'] . '/fc.' . $config['Flyout_Menus']['fancy_categories_skin'] 
        . '.' . intval($user_account['membershipid']) . '.' . $shop_language . '.php';
}

/**
 * Check if the categories tree is already in cache 
 * 
 * @return bool
 * @see    ____func_see____
 */
function func_fc_has_cache()
{
    return file_exists(func_fc_get_cache_path());
}

/**
 * Prepare categories tree
 * 
 * @param array $all_categories All categories array
 * @param array $categories     Categories array
 * @param array $catexp_path    Explode path
 *  
 * @return array
 * @see    ____func_see____
 */
function func_fc_prepare_categories($categories = array(), $catexp_path = array())
{
    global $config;

    x_load('category');

    if (empty($categories)) {
        $categories = func_get_categories_list(0, false);
    }

    $all_categories = func_get_categories_list(0, false, true, $config['Flyout_Menus']['icons_levels_limit']);

    if (
        is_array($all_categories) 
        && !empty($all_categories)
    ) {

        foreach ($all_categories as $k => $v) {

            if (in_array($k, $catexp_path)) {

                $all_categories[$k]['expanded'] = true;

                if (isset($categories[$k]))
                    $categories[$k]['expanded'] = true;
            }

            if (empty($v['parentid'])) {
                continue;
            }

            if (
                isset($all_categories[$v['parentid']]['childs'])
                && !is_array($all_categories[$v['parentid']]['childs'])
            ) {

                $all_categories[$v['parentid']]['childs'] = array(
                    $k => &$all_categories[$k],
                );

            } else {

                $all_categories[$v['parentid']]['childs'][$k] = &$all_categories[$k];

            }

            if (isset($categories[$v['parentid']])) {

                $categories[$v['parentid']]['childs'] = $all_categories[$v['parentid']]['childs'];

            }

        }

    }

    func_mark_last_categories($categories);

    return $categories;
}

/**
 * Mark last categories
 * 
 * @param mixed $categories Categories
 * @param array $columns    Columns
 *  
 * @return void
 * @see    ____func_see____
 */
function func_mark_last_categories(&$categories, $columns = array())
{
    if (
        empty($categories)
        || !is_array($categories)
    ) {
        return false;
    }

    end($categories);

    $last = key($categories);

    reset($categories);

    $categories[$last]['last'] = true;

    foreach ($categories as $k => $v) {

        if (
            isset($v['childs']) 
            && !empty($v['childs']) 
            && is_array($v['childs'])
        ) {

            $c = $columns;

            $c[] = isset($v['last']) ? !$v['last'] : true;

            func_mark_last_categories($categories[$k]['childs'], $c);
        }

        $categories[$k]['columns'] = $columns;
    }
}

/**
 * Check if category thumbnails should be regenerated
 * 
 * @return bool
 * @see    ____func_see____
 */
function func_fc_need_regenerate_catthumbn($new_alt_skin_key)
{
    global $config, $alt_skin_info, $altSkinsInfo;

    if (!in_array($new_alt_skin_key, array_keys($altSkinsInfo)))
        return false;

    $new_skin = $altSkinsInfo[$new_alt_skin_key];
    $old_skin = $alt_skin_info;

    // Get skins icons dimensions
    x_load('image');
    $new_skin = func_array_merge($new_skin, func_ic_get_size_catthumbn(null, null, $new_skin['name']));
    $old_skin = func_array_merge($old_skin, func_ic_get_size_catthumbn(null, null, $old_skin['name']));

    if (
        $new_skin['width'] != $old_skin['width']
        || $new_skin['height'] != $old_skin['height']
    ) {
        return min($new_skin['width'], $new_skin['height']) > 0;
    }

    return false;
}

?>
