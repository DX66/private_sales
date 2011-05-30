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
 * 'Webmaster' mode initialization script
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: webmaster.php,v 1.49.2.2 2011/01/10 13:11:51 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if (
    isset($_POST['editor_mode'])
    || isset($_GET['editor_mode'])
    || (
        isset($_COOKIE['editor_mode'])
        && $_COOKIE['editor_mode'] == 'editor'
    )
) {
    func_403(38);
}

if (isset($editor_mode))
    unset($editor_mode);

x_session_register('editor_mode');

$smarty->webmaster_mode = $editor_mode=='editor';
$smarty->assign('webmaster_mode', $editor_mode);

if (strpos($HTTP_USER_AGENT,'Opera') !== false)
    $user_agent = 'opera';
elseif (strpos($HTTP_USER_AGENT,'MSIE') !== false)
    $user_agent = 'ie';
else
    $user_agent = 'ns';

$smarty->assign('user_agent', $user_agent);

/**
 * Used from get_languages to convert 'lng' smarty variable.
 * Replaces each variable 'value' with "<div ...>value</div>"
 * except some listed in 'if' statement (see below). Add variables
 * which could appear in javascript code into this 'if'.
 */
function func_webmaster_convert_labels (&$lang)
{
    global $user_agent;
    global $smarty;

    if (is_array($lang)) {

        $lang_copy = array();

        foreach ($lang as $name => $val) {

            $lang_copy[$name] = addcslashes($val, "\0..\37\\");

            $lang_copy[$name] = htmlspecialchars($lang_copy[$name],ENT_QUOTES);

            $lang[$name] = func_webmaster_label($user_agent,$name,$val);

        }

        $smarty->assign('webmaster_lng', $lang_copy);

    }

}

function func_webmaster_label($user_agent, $label, $value)
{
    static $disabled = array(
        'lbl_site_title',
        'txt_site_title',
        'lbl_site_path',
        'lbl_close_storefront',
        'lbl_open_storefront_warning',
        'lbl_search',
        'txt_minicart_total_note'
    );

    // check for exceptions
    if (in_array($label, $disabled) || preg_match('/txt_subtitle.*/i', $label))
        return $value;

    return '<span class="lbl" id="' . $label . '" onmouseover="javascript: lmo(this, event);">' . $value . '</span>';
}

?>
