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
 * Type:     modifier
 * Name:     escape
 * Purpose:  Escape the string according to escapement type
 * Comment:  Replaces the original Smarty-modifier because of 4.0.6 incompatibility
 * -------------------------------------------------------------
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: modifier.escape.php,v 1.27.2.3 2011/01/10 13:11:54 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../../../"); die("Access denied"); }

function smarty_modifier_escape($string, $esc_type = 'html', $char_set = false)
{
    global $default_charset, $current_area;

    if (zerolen($string))
        return $string;

    if (!$char_set)
        $char_set = (isset($default_charset) && ($default_charset != '')) ? $default_charset : "ISO-8859-1";

    switch ($esc_type) {
        case 'html':
            if (X_USE_NEW_HTMLSPECIALCHARS)
                return @htmlspecialchars($string, ENT_QUOTES, $char_set);
            else
                return htmlspecialchars($string, ENT_QUOTES);

        case 'htmlcompat':
            if (X_USE_NEW_HTMLSPECIALCHARS) 
                return @htmlspecialchars($string, ENT_COMPAT, $char_set);
            else 
                return htmlspecialchars($string, ENT_COMPAT);

        case 'htmlall':
            return @htmlentities($string, ENT_QUOTES, $char_set);

        case 'url':
            return rawurlencode($string);

        case 'urlpathinfo':
            return str_replace('%2F', '/', rawurlencode($string));

        case 'quotes':
            return preg_replace("/(?<!\\\\)'/Ss", "\\'", $string);

        case 'hex':
            $s = '%';
        case 'hexentity':
            if (!$s)
                $s = '&#x';
        case 'decentity':
            if (!$s)
                $s = '&#';
            $f = ($esc_type == 'decentity') ? "ord" : "bin2hex";
            $l = strlen($string);
            $return = '';
            for ($x = 0; $x < $l; $x++)
                $return .= $s.$f(substr($string, $x, 1)).';';

            return $return;

        case 'javascript':
            return strtr($string, array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n','</'=>'<\/'));

        case 'mail':
            return strtr($string, array('@', '.'), array(' [AT] ', ' [DOT] '));

        case 'nonstd':
            $return = '';
            $l = strlen($string);
            for ($i = 0; $i < $l; $i++) {
                $symbol = substr($string, $i, 1);
                $ord = ord($symbol);
                $return .= ($ord >= 126) ? ('&#'.$ord.';') : $symbol;
            }
            return $return;

    }

    return $string;
}
?>
