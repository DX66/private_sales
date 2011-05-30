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
 * Defer loader plugin.
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: function.load_defer_code.php,v 1.11.2.6 2011/03/07 08:56:34 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

// Templater plugin
// -------------------------------------------------------------
// Type:     function
// Name:     load_defer_code
// Input:    none
// -------------------------------------------------------------

if (!defined('XCART_START')) { header("Location: ../../../"); die("Access denied"); }

/**
 * Defer loader plugin.
 *
 * @param array  $params should have 'type' element
 * @param Smarty $smarty Smarty object
 *
 * @return string always empty string
 * @see    ____func_see____
 * @since  1.0.0
 */
function smarty_function_load_defer_code($params, &$smarty)
{
    global $var_dirs, $xcart_dir, $smarty_skin_dir, $deferRegistry, $directInfoRegistry, $config, $var_dirs_web;

    if (
        !isset($params['type'])
        || empty($params['type'])
        || !in_array($params['type'], array('js', 'css'))
    ) {

        return '';
    }

    $type = $params['type'];

    if (
        (!isset($deferRegistry[$type]) || empty($deferRegistry[$type]))
        && (!isset($directInfoRegistry[$type]) || empty($directInfoRegistry[$type]))
    ) {

        return '';
    }

    $type = $params['type'];

    $queue = array();

    if (isset($deferRegistry[$type])) {

        $queue = array_merge(array_keys($deferRegistry[$type]), $queue);

    }

    if (isset($directInfoRegistry[$type])) {

        $queue = array_merge(array_keys($directInfoRegistry[$type]), $queue);

    }

    if (
        isset($config['General']['speedup_' . $type])
        && 'Y' == $config['General']['speedup_' . $type]
        && defined('AREA_TYPE')
        && 'C' == constant('AREA_TYPE')
    ) {

        $maxFtime = 0;
        $queue = array_unique($queue);

        foreach ($queue as $elem) {

             if (isset($deferRegistry[$type][$elem])) {

                foreach ($deferRegistry[$type][$elem] as $file) {

                    $ftime = intval(filemtime($file));

                    $maxFtime = max($maxFtime, $ftime);
                }

            }

        }

        $md5Suffix = md5(
            serialize($deferRegistry[$type])
            . (!empty($directInfoRegistry[$type]) ? serialize($directInfoRegistry[$type]) : '')
            . $maxFtime
            . $smarty_skin_dir
        );

        $cacheFile = $var_dirs['cache'] . XC_DS . '_' . $md5Suffix . '.' . $type;
        $cacheWebFile = $var_dirs_web['cache'] . '/' . '_' . $md5Suffix . '.' . $type;

        if (!is_file($cacheFile)) {

            $fp = @fopen($cacheFile, 'w');

            foreach ($queue as $elem) {

                $cache = '';

                if (
                    isset($deferRegistry[$type][$elem])
                    && !empty($deferRegistry[$type][$elem])
                ) {

                    foreach ($deferRegistry[$type][$elem] as $web => $file) {

                        $dir = '../..' . dirname($web) . '/';

                        $fileSource = file_get_contents($file);

                        if ('css' == $type) {

                            // Remove " and ' from URI path
                            $fileSource = preg_replace('/(url\()[\'" ]*([^)\'"]*)[\'" ]*(\))/', '\1\2\3', $fileSource);

                            // Add path to var/cache
                            $fileSource = preg_replace('/(url\()(?!http|\/)(.*)/S', '\1' . $dir . '\2', $fileSource);

                        }

                        $cache .= $fileSource;

                    }

                    unset($deferRegistry[$type][$elem]);

                }

                if (
                    'css' == $type
                    && '' !== $cache
                ) {

                    @fwrite($fp, $cache);

                }

                if (
                    isset($directInfoRegistry[$type][$elem])
                    && !empty($directInfoRegistry[$type][$elem])
                ) {

                    foreach ($directInfoRegistry[$type][$elem] as $id => $value) {

                        @fwrite($fp, $value);

                    }

                }

                if (
                    'js' == $type
                    && '' !== $cache
                ) {

                    @fwrite($fp, $cache);

                }

            } // foreach ($queue as $elem)

            @fclose($fp);

        }

        unset($deferRegistry[$type]);
        unset($directInfoRegistry[$type]);

        $result = ('js' == $type)
            ? '<script type="text/javascript" src="' . $cacheWebFile . '"></script>'
            : '<link rel="stylesheet" type="text/css" href="' . $cacheWebFile . '" />';

    } else {

        $result = '';

    }

    return $result;
}
?>
