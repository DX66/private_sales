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
 * Defer loading plugin.
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: function.load_defer.php,v 1.18.2.9 2011/03/04 14:31:53 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

// Templater plugin
// -------------------------------------------------------------
// Type:     function
// Name:     load_defer
// Input:    file
// -------------------------------------------------------------

if (!defined('XCART_START')) { header("Location: ../../../"); die("Access denied"); }

/**
 * Javascript defer plugins. Registers files to use in defer loading
 *
 * @param array  $params should have 'file' and 'type' elements
 * @param Smarty $smarty Smarty object
 *
 * @return string always empty string
 * @see    ____func_see____
 * @since  1.0.0
 */
function smarty_function_load_defer($params, &$smarty)
{
    global $xcart_web_dir, $xcart_dir, $smarty_skin_dir, $deferRegistry, $directInfoRegistry, $config, $alt_skin_info, $alt_skin_dir;
    global $already_included_files;

    if (is_null($already_included_files)) {

        $already_included_files = array();

    }

    if (
        !isset($params['file'])
        || empty($params['file'])
    ) {

        return '';
    }

    if (
        !isset($params['type'])
        || empty($params['type'])
        || !in_array($params['type'], array('js', 'css'))
    ) {

        return '';
    }

    if (
        !empty($alt_skin_dir)
    ) {
        if (is_readable($xcart_dir . $alt_skin_info['alt_skin_dir'] . XC_DS . $params['file'])) {

            $file = $alt_skin_info['alt_skin_dir'] . '/' . $params['file'];

        } else {

            $file = $smarty_skin_dir . '/' . $params['file'];

        }

    } else {

        $file = $smarty_skin_dir . '/' . $params['file'];

    }

    $type = $params['type'];

    $queue = isset($params['queue']) ? intval($params['queue']) : 0;

    $result = '';

    if (
        isset($config['General']['speedup_' . $type])
        && 'Y' == $config['General']['speedup_' . $type]
        && defined('AREA_TYPE')
        && 'C' == constant('AREA_TYPE')
    ) {

        if (
            !isset($params['direct_info']) 
            && !in_array($file, $already_included_files)
            && is_readable($xcart_dir . $file)
        ) {

            $deferRegistry[$type][$queue][$file] = $xcart_dir . $file;
            $already_included_files[] = $file;
        } elseif (isset($params['direct_info'])) {

            $directInfoRegistry[$type][$queue][$file] = 'css' == $type
                ? func_get_direct_css($params['direct_info'])
                : $params['direct_info'];

        }

    } else {

        if (!isset($params['direct_info'])) {


            if (!in_array($file, $already_included_files) && is_readable($xcart_dir . $file)) {

                $result = '';

                if ('js' == $type) {
                
                    $result = '<script type="text/javascript" src="' . $xcart_web_dir . $file . '"></script>';

                } elseif (
                    !empty($params['css_inc_mode']) 
                    && 'css' == $type
                ) {
                      // include css via @import directive: hack to get around IE limitation                                  
                      // applied to the number of css files    
                      $result = '@import url("' . $xcart_web_dir . $file . '"); ' . "\n";

                } else {

                    $result = '<link rel="stylesheet" type="text/css" href="' . $xcart_web_dir . $file . '" />';

                }
                
                $already_included_files[] = $file;

            }

        } else {

            $result = ('css' == $type)
                ? '<style type="text/css">' . "\n"
                  . '<!--' . "\n"
                  . func_get_direct_css($params['direct_info']) . "\n"
                  . '-->' . "\n"
                  . '</style>'
                : '<script type="text/javascript">' . "\n"
                  . '//<![CDATA[' . "\n"
                  . $params['direct_info'] . "\n"
                  . '//]]>' . "\n"
                  .'</script>';

        }

    }

    static $first_call = true;
    if (
        func_constant('DEVELOPMENT_MODE')
        && $first_call
    ) {
        $first_call = false;
        register_shutdown_function('func_check_load_defer_plugin_integrity');
    }

    return $result;
}

/**
 * return custom css styles
 *
 * @param array $directInfo custom CSS styles array
 *
 * @return string
 * @see    ____func_see____
 * @since  1.0.0
 */
function func_get_direct_css($directInfo)
{

    $styles = array();

    if (!is_array($directInfo))
        return '';

    foreach ($directInfo as $id => $css) {

        $styles[$id] = $id . ' {';

        foreach ($css as $name => $value) {

            $styles[$id] .= $name . ': ' . $value . ';' . "\n";
        }

        $styles[$id] .= '}';

    }

    return implode("\n", $styles);
}

function func_check_load_defer_plugin_integrity()
{
    global $deferRegistry, $directInfoRegistry;

    assert('empty($deferRegistry) && empty($directInfoRegistry) /*Func_check_load_defer_plugin_integrity <b>It seems load_defer_code call should be added at the end of the page</b>*/');
}
?>
