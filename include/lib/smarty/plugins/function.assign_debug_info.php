<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {assign_debug_info} function plugin
 *
 * Type:     function<br>
 * Name:     assign_debug_info<br>
 * Purpose:  assign debug info to the template<br>
 * @author Monte Ohrt <monte at ohrt dot com>
 * @param array unused in this plugin, this plugin uses {@link Smarty::$_config},
 *              {@link Smarty::$_tpl_vars} and {@link Smarty::$_smarty_debug_info}
 * @param Smarty
 */
function smarty_function_assign_debug_info($params, &$smarty)
{
    $assigned_vars = $smarty->get_template_vars();

    ksort($assigned_vars);

    if (@is_array($smarty->_config[0])) {

        $config_vars = $smarty->_config[0];

        ksort($config_vars);

        $smarty->assign("_debug_config_keys", array_keys($config_vars));
        $smarty->assign("_debug_config_vals", array_values($config_vars));
    }
    
    $included_templates = $smarty->_smarty_debug_info;

    array_walk($included_templates, 'func_concat_array');

    $smarty->assign("_debug_keys", array_keys($assigned_vars));
    $smarty->assign("_debug_vals", array_values($assigned_vars));
    
    $smarty->assign("_debug_tpls", $included_templates);
}

/**
 * array_walk callback routine. Checks if the template is available for reading.
 * 
 * @param array $elem array element
 *  
 * @return array
 * @see    ____func_see____
 * @since  1.0.0
 */
function func_concat_array(&$elem)
{
    global $alt_skin_info;

    $altName = $alt_skin_info['path'] . XC_DS . $elem['filename'];

    if (
        is_file($altName)
        && is_readable($altName)
    ) {

        $elem['filename'] = $alt_skin_info['alt_schemes_skin_name'] . '/' . $elem['filename'];

    } else {

        $elem['filename'] = 'common_files' . '/' . $elem['filename'];

    }

    return $elem;
}

/* vim: set expandtab: */

?>
