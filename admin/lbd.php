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
 * Service script
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: lbd.php,v 1.56.2.3 2011/01/10 13:11:45 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (
    file_exists('./auth.php')
    && is_readable('./auth.php')
) {
    include './auth.php';
}

$rev = array(88,45,67,65,82,84,32,86,101,114,115,105,111,110,32,32,46,32,46,32,32,32,60,98,114,62,10,67,111,112,121,114,105,103,104,116,32,38,99,111,112,121,59,32,50,48,48,49,45,50,48,48,57,32,82,117,115,108,97,110,32,82,46,32,70,97,122,108,105,101,118,46,60,98,114,62,10,119,119,119,46,120,45,99,97,114,116,46,99,111,109);

$topics = array ('Labels', 'Text', 'Errors', "E-Mail");

/**
 * Check labels
 */
if (!is_array($languages))
    $languages = array();
foreach ($languages as $key=>$value) {
    $languages[$key]['disabled'] = (in_array ($value['language'], $d_langs) ? 'Y' : 'N');
}
$new_languages = array ();
if (!$lbl_result) {
    $rev[15] = ord('4');
    $rev[17] = ord('4');
    $rev[19] = ord('2');

    for($i=0; $i<count($rev); $i++)
        echo chr($rev[$i]);
}
if (false) { #($_new_languages) {
    foreach ($_new_languages as $key=>$value) {
        $found = false;
        if ($languages) {
            foreach ($languages as $subkey=>$subvalue) {
                if ($value['code'] == $subvalue['code'])
                    $found = true;
            }
        }
        if (!$found)
            $new_languages [] = $value;
    }
}
$new_languages = $_new_languages;

$smarty->assign ('languages', $languages);
$smarty->assign ('new_languages', $new_languages);
?>
