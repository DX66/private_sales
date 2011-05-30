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
 * Module configuration script
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: admin_config.php,v 1.16.2.1 2011/01/10 13:11:57 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }

if (!in_array($option, array('Flyout_Menus', 'Appearance')))
    return false;

if ($option == 'Appearance') {
    if ($config['Appearance']['count_products'] != $_POST['count_products'] && !empty($_POST['count_products']) && func_fc_use_cache()) {

        // Update categories data cache
        // (category box is display products and subcategories counts.
        // $config.Appearance.count_products option is control this functionality)
        func_fc_build_categories(1);
    }

    return true;
}

/**
 * Get categories cache data rebuilding flag
 */
if ($REQUEST_METHOD == 'POST' && !empty($_POST)) {
    $flag = false;

    $_fancy_cache = func_query_first_cell("SELECT value FROM $sql_tbl[config] WHERE name = 'fancy_cache'");
    if ($config['Flyout_Menus']['fancy_categories_skin'] != $_POST['fancy_categories_skin']) {

        // Change subskin
        $config['Flyout_Menus']['fancy_cache'] = $_fancy_cache;
        $flag = func_fc_use_cache($_POST['fancy_categories_skin']);

    } elseif ($_fancy_cache == 'Y') {

        // Check common options
        $flag = $config['Flyout_Menus']['fancy_cache'] != 'Y';

        // Check critical skin variables
        if ($flag == false) {
            $ini = func_parse_ini($fancy_config_path);
            foreach ($ini as $k => $v) {
                if (!is_array($v) || $v['critical'] != 'Y')
                    continue;

                $new_value = func_query_first_cell("SELECT value FROM $sql_tbl[config] WHERE name = '".$fancy_prefix.$k."'");
                if ($config['Flyout_Menus'][$fancy_prefix.$k] != $new_value) {
                    $flag = true;
                    break;
                }
            }

            unset($ini);
        }
    }

    if ($flag) {
        func_header_location("configuration.php?option=$option&fc_build_categories=Y");
    }

} elseif ($REQUEST_METHOD == 'GET' && !empty($configuration)) {

    if ($fc_build_categories == 'Y') {
        if (func_fc_use_cache())
            func_fc_build_categories(1);

        func_header_location("configuration.php?option=".$option);
    }

    // Get skins names
    $path = $xcart_dir . $smarty_skin_dir . XC_DS . $fcat_module_path . XC_DS;

    foreach ($fcat_skins as $k => $v) {

        if (!file_exists($path . $k . XC_DS . 'config.ini')) {

            unset($fcat_skins[$k]);

            continue;
        }

        $name = func_get_langvar_by_name('opt_fc_skin_'.$k, NULL, false, true);

        // Add name if name is empty
        if (empty($name)) {
            $ini = func_parse_ini($path.$k.XC_DS.'config.ini');

            if (!empty($ini['name_' . $shop_language])) {

                $fcat_skins[$k]['name'] = $ini['name_' . $shop_language];

            } elseif (!empty($ini['name'])) {

                $fcat_skins[$k]['name'] = $ini['name'];

            }

            unset($ini);
            $query_data = array(
                'code' => $shop_language,
                'name' => 'opt_fc_skin_'.$k,
                'value' => $fcat_skins[$k]['name'],
                'topic' => 'Options'
            );
            $query_data = func_addslashes($query_data);
            func_array2insert('languages', $query_data);

        } else {
            $fcat_skins[$k]['name'] = $name;
        }
    }

    // Unset configuration variables of another skins
    if (isset($fcat_skins[$config['Flyout_Menus']['fancy_categories_skin']])) {
        foreach ($configuration as $k => $v) {
            if ($v['type'] != 'separator' && !in_array($v['name'], array("fancy_categories_skin","fancy_js","fancy_download","fancy_preload","fancy_cache")) && strpos($v['name'], $fancy_prefix) !== 0) {
                unset($configuration[$k]);
            }
        }
    }

    // Check skin config variables
    if (file_exists($fancy_config_path)) {
        $ini = func_parse_ini($fancy_config_path);

        // Check absented in xcart_config table config variables
        foreach ($ini as $k => $v) {
            if (!is_array($v))
                continue;

            $key = $fancy_prefix.$k;
            $found = false;
            foreach ($configuration as $cv) {
                if ($cv['name'] == $key) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                func_fc_add_cfg_var($config, $key, $v);
                $configuration[] = func_query_first("SELECT * FROM $sql_tbl[config] WHERE name = '".addslashes($key)."'");
            }
        }
    }

    // Modify properties of configuration variable
    foreach ($configuration as $k => $v) {
        if ($v['name'] == 'fancy_categories_skin') {
            $configuration[$k]['variants'] = "";
            foreach($fcat_skins as $kv => $vv) {
                $configuration[$k]['variants'] .= $kv.":".$vv['name']."\n";
            }

            $configuration[$k]['auto_submit'] = true;

            if (count($configuration[$k]['variants']) < 2)
                unset($configuration[$k]);
        }
    }

    $smarty->assign('fcat_skins', $fcat_skins);
}

?>
