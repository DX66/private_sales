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
 * Image verification module requrements
 *  
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: spambot_requirements.php,v 1.17.2.1 2011/01/10 13:11:58 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

// GD extensions array
$gd_req_extensions = array (
                                "GIF(".func_get_langvar_by_name('lbl_gif_read_support').")" => "GIF Read Support",
                                "GIF(".func_get_langvar_by_name('lbl_gif_create_support').")" => "GIF Create Support",
                                'JPG' => "JPG Support",
                                'PNG' => "PNG Support"
                        );
if (defined('X_PHP530_COMPAT'))
    $gd_req_extensions['JPG'] = "JPEG Support";

$spambot_requirements = '';
// Check for GD library presence
if (extension_loaded('gd')) {   // If GD loaded
    if (function_exists('gd_info')) { // If gd_info function exists
        $gd_config = gd_info();
        foreach ($gd_req_extensions as $ext=>$conf_name) {
            if (empty($gd_config[$conf_name])) {
                if (empty($spambot_requirements)) {
                    $spambot_requirements = func_get_langvar_by_name('lbl_gd_ext_missing') . $ext;
                } else {
                    $spambot_requirements .= ", $ext";
                }
            }

        }
        if (!empty($spambot_requirements)) {
            $spambot_requirements .= ". <br />".func_get_langvar_by_name('lbl_module_incorrect_work');
        }
    } else {
        $spambot_requirements = func_get_langvar_by_name('lbl_gd_info_missing');
    }
} else {
    $spambot_requirements = func_get_langvar_by_name('lbl_gd_lib_missing');
}
$smarty->assign('spambot_requirements', $spambot_requirements);
?>
