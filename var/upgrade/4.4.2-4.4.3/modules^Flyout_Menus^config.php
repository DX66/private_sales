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
 * Configuration script
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: config.php,v 1.19.2.1 2011/01/10 13:11:57 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

/**
 * Add config variable from config.ini to xcart_config table
 */
function func_fc_add_cfg_var(&$config, $key, $value)
{
    global $sql_tbl;

    $value = func_array_map('trim', $value);

    $query_data = array(
        'name'             => $key,
        'value'         => $value['default'],
        'category'         => 'Flyout_Menus',
        'type'             => $value['type'],
        'orderby'         => func_query_first_cell("SELECT MAX(orderby) FROM $sql_tbl[config] WHERE category = 'Flyout_Menus'")+100,
        'defvalue'         => '',
        'variants'         => '',
        'validation'     => $value['validation'],
    );

    // Check and get variants in selector or multiselector config type
    if (in_array($value['type'], array("selector", "multiselector"))) {

        $vars = preg_grep("/^variant.*$/S", array_keys($value));

        if (empty($vars) || !is_array($vars)) {
            return false;
        }

        foreach ($vars as $vname) {
            if (!isset($value[$vname]))
                continue;

            $query_data['variants'] .= (empty($query_data['variants']) ? "" : "\n").$value[$vname];
        }

        unset($vars);
    }

    // Add data to xcart_config table
    $query_data = func_addslashes($query_data);

    func_array2insert(
        'config',
        $query_data,
        true
    );

    $config['Flyout_Menus'][$key] = $value['default'];

    // Define and add multilanguage variable descriptions
    $comments = preg_grep("/^description_\w{2}$/S", array_keys($value));

    if (empty($comments))
        return true;

    foreach ($comments as $cname) {

        if (!isset($value[$cname]))
            continue;

        $code = strtolower(substr($cname, 12, 2));

        if (strlen($code) != 2)
            continue;

        if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[languages] WHERE code = '$code'") == 0)
            continue;

        if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[languages] WHERE name = 'opt_".$key."' AND code = '$code'"))
            continue;

        $query_data = array(
            'code' => $code,
            'name' => 'opt_'.$key,
            'value' => addslashes($value[$cname]),
            'topic' => 'Options'
        );

        func_array2insert('languages', $query_data);

    }

    return true;
}

$fcat_module_path = 'modules/Flyout_Menus';
$fancy_cat_prefix = 'cat';

// Detect skins
$fcat_skins = array();

$path = $xcart_dir. $smarty_skin_dir . XC_DS . $fcat_module_path . XC_DS;

$dp = @opendir($path);

if ($dp) {

    while ($fn = readdir($dp)) {

        if (
            $fn == '.'
            || $fn == '..'
            || !is_dir($path . $fn)
            || !file_exists($path . $fn . XC_DS . 'config.ini')
        ) {
            continue;
        }

        $fcat_skins[$fn] = array(
            'name' => str_replace('_', " ", $fn),
        );
    }

    closedir($dp);
}

if (empty($fcat_skins)) {

    unset($active_modules['Flyout_Menus']);

    return false;

} elseif (!isset($fcat_skins[$config['Flyout_Menus']['fancy_categories_skin']])) {

    reset($fcat_skins);

    $config['Flyout_Menus']['fancy_categories_skin'] = key($fcat_skins);

    reset($fcat_skins);

}

// Detect config variables in xcart_config table from current skin
$fancy_prefix = strtolower($config['Flyout_Menus']['fancy_categories_skin']).'_';

$fancy_config_path = $path.$config['Flyout_Menus']['fancy_categories_skin'].XC_DS.'config.ini';

$found = false;

foreach ($config['Flyout_Menus'] as $k => $v) {
    if (strpos($k, $fancy_prefix) === 0) {
        $found = true;
        break;
    }
}

// Add config variables to xcart_config table from current skin
if (!$found) {

    $ini = func_parse_ini($fancy_config_path);

    if (!empty($ini)) {

        $css_files = array();

        foreach ($ini as $k => $v) {
            if (is_array($v)) {
                func_fc_add_cfg_var($config, $fancy_prefix.$k, $v);

            } elseif (preg_match("/^css_file_(.+)$/Ss", $k, $m)) {
                $css_files[$m[1]] = $v;
            }
        }

        if (count($css_files) > 0) {
            $config[$fancy_prefix.'css_files'] = serialize($css_files);
            func_array2insert(
                'config',
                array(
                    'name' => $fancy_prefix.'css_files',
                    'value' => $config[$fancy_prefix.'css_files']
                ),
                true
            );
        }
    }

    unset($ini);
}

$container_classes[] = 'fancycat-page-skin-' . strtolower($config["Flyout_Menus"]["fancy_categories_skin"]);

if ($config['Flyout_Menus']['fancy_categories_skin'] == 'Icons') {
    $container_classes[] = 'fancycat-page-subskin-' . strtolower($config["Flyout_Menus"]["icons_mode"]);
}

$smarty->assign('fcat_module_path', $fcat_module_path);
$smarty->assign('fc_skin_path', $fcat_module_path.'/'.$config['Flyout_Menus']['fancy_categories_skin']);
$smarty->assign('fc_skin_web_path', $smarty->get_template_vars('SkinDir')."/".$fcat_module_path."/".$config["Flyout_Menus"]["fancy_categories_skin"]);
$smarty->assign('fancy_cat_prefix', $fancy_cat_prefix);
?>
