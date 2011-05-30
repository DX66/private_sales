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
 * Input number vars convertion
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: number_conv.php,v 1.31.2.1 2011/01/10 13:11:49 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if (defined('NUMBER_VARS')) {

    // Get variables list
    $tmp = explode(",", constant('NUMBER_VARS'));

    foreach ($tmp as $v) {

        $v = trim($v);

        if (preg_match("/^([\w\d_]+)(\[[\w\d_]+\])+$/S", $v, $match)) {
            // Variable is cell of array

            eval('$var = isset($' . $v . ');');

            if ($var) {

                eval('$' . $v . ' = func_convert_number($' . $v . ');');

                $pos = strpos($v, "[");

                if ($pos !== false) {

                    $v_array = substr($v, $pos);

                    $v_orig = substr($v, 0, $pos - 1);

                    eval('$var = isset($_POST[' . $v_orig . ']' . $v_array . ');');

                    if ($var) {

                        eval('$_POST[' . $v_orig . ']' . $v_array . ' = $' . $v . ';');

                    } else {

                        eval('$var = isset($_GET[' . $v_orig . ']' . $v_array . ');');

                        if ($var) {
                            eval('$_GET[' . $v_orig . ']' . $v_array . ' = $' . $v . ';');
                        }

                    }

                }

            }

        } elseif (isset($$v) && is_string($$v)) {
            // Variable is string

            $$v = func_convert_number($$v);

            if (isset($_POST[$v])) {

                $_POST[$v] = $$v;

            } elseif (isset($_GET[$v])) {

                $_GET[$v] = $$v;

            }
        }

    }

}

/**
 * Function validates month, year and day values
 * to be processed through the date() function
 */
function func_validate_date_param($val, $param)
{

    $val = abs(intval($val));

    switch($param) {
        case 'Month':
            // Numeric representation of a month, with leading zeros    (01 through 12)
            $val = sprintf("%02d", min( max(1, $val), 12));
            break;
        case 'Year':
            // A full numeric representation of a year, 4 digits (1999 or 2003)
            $val = sprintf("%04d", min( max(1901, $val), 2038));
            break;
        case 'Day':
            // Day of the month, 2 digits with leading zeros (01 to 31)
            $val = sprintf("%02d", min( max(1, $val), 31));
            break;
        default:
            break;
    }

    return $val;
}

/**
 * Validate variables posted through
 * the Smarty html_select_date selector form
 */

$dateform_used_vars = array('StartMonth', 'StartDay', 'StartYear', 'EndMonth', 'EndDay', 'EndYear');

foreach ($dateform_used_vars as $varname) {
    if (!isset($_GET[$varname]) && !isset($_POST[$varname]))
        continue;

    $date_param = preg_replace('/^(Start|End)/', '', $varname);

    if (isset($_GET[$varname]))
        $_GET[$varname] = func_validate_date_param($_GET[$varname], $date_param);

    if (isset($_POST[$varname]))
        $_POST[$varname] = func_validate_date_param($_POST[$varname], $date_param);
}

$smarty->assign('number_format_dec', $config['Appearance']['number_format']{1});
$smarty->assign('number_format_th', isset($config['Appearance']['number_format']{2}) ? $config['Appearance']['number_format']{2} : "");
$smarty->assign('number_format_point', intval($config['Appearance']['number_format']{0}));

$smarty->assign('zero', func_format_number(0));
?>
