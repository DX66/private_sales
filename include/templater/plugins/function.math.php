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
 * Templater plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     math
 * Purpose:  allow mathematical equations
 * -------------------------------------------------------------
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: function.math.php,v 1.22.2.1 2011/01/10 13:11:53 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../../../"); die("Access denied"); }

function smarty_function_math($params, &$smarty)
{
    global $mode, $PHP_SELF;
    static $reserved_params = array ('assign', 'equation', 'format');
    static $allowed_funcs = array (
        'ceil','floor','round',
        'int','float','base_convert',
        'abs','max','min','pi','rand','lcg_value',
        'cos','sin','tan','acos','asin','atan',
        'log','log10','exp','pow','sqrt');

    $error_prefix = 'math ``<b>'.htmlspecialchars($params['equation']).'</b>\'\' in ``'.$smarty->current_resource_name.'\'\': ';

    if (!isset($params['equation'])) {
        $smarty->trigger_error($error_prefix.'missing equation');
        return;
    }

    $equation = $params['equation'];
    $result = null;
    if (empty($equation)) {
        $result = $equation;
    }
    else {
        if (basename($PHP_SELF) == 'file_edit.php' && $mode == 'preview')
            error_reporting (0);

        if (substr_count($equation,"(") != substr_count($equation,")")) {
            $smarty->trigger_error($error_prefix.'unbalanced parenthesis');
            return;
        }

        if (!isset($params['skip_func_check'])) {
            // match all vars in equation, make sure all are passed
            preg_match_all("!(?:0x[a-fA-F0-9]+)|([a-zA-Z]+[a-zA-Z0-9_]*)!S",$equation, $match);

            foreach($match[1] as $curr_var) {
                if ($curr_var && !in_array($curr_var, array_keys($params)) && !in_array($curr_var, $allowed_funcs)) {
                    $smarty->trigger_error($error_prefix."function call $curr_var is not allowed");
                    return;
                }
            }
        }

        $keys_empty = array();
        $keys_not_numeric = array();
        $error = false;

        // substitute parameters in equation
        foreach($params as $key => $val) {
            if (in_array($key, $reserved_params)) continue;

            if (strlen($val)==0) {
                $keys_empty[] = $key;
                $error = true;
                continue;
            }
            if (!is_numeric($val)) {
                $keys_not_numeric[] = $key;
                $error = true;
                continue;
            }

            if (!$error) {
                $equation = preg_replace("!\b$key\b!S",$val, $equation);
            }
        }

        if ($error) {
            $err_arr = array();
            $err_def = array (
                'parameter%s ``<b>%s</b>\'\' %s empty' => $keys_empty,
                'parameter%s ``<b>%s</b>\'\' %s not numeric' => $keys_not_numeric
            );
            foreach ($err_def as $fmt => $keys_arr) {
                $cnt = count($keys_arr);
                if ($cnt < 1) continue;
                $err_arr[] = sprintf( $fmt,
                    ($cnt>1?'s':''),
                    implode('</b>\'\', ``<b>', $keys_arr),
                    ($cnt>1?'are':'is')
                );
            }

            $smarty->trigger_error($error_prefix.implode('; ', $err_arr));
            return;
        }

        @eval("\$result = ".$equation.";");
    }

    if (!empty($params['format']))
        $result = sprintf($params['format'], $result);

    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $result);
        return '';
    }

    return $result;
}

?>
