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
 * Smarty configuration
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Customer interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: smarty.php,v 1.64.2.2 2011/01/10 13:11:44 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: index.php"); die("Access denied"); }

umask(0);

/**
 * Define SMARTY_DIR to avoid problems with PHP 4.2.3 & SunOS
 */
define(
    'SMARTY_DIR',
    $xcart_dir
        . DIRECTORY_SEPARATOR
        . 'include'
        . DIRECTORY_SEPARATOR
        . 'lib'
        . DIRECTORY_SEPARATOR
        . 'smarty'
        . DIRECTORY_SEPARATOR
);

include_once($xcart_dir . '/include/templater/templater.php');

/**
 * Smarty object for processing html templates
 */
$smarty = new Templater;

/**
 * Store all compiled templates to the single directory
 */
$smarty->use_sub_dirs = false;

if (!empty($alt_skin_dir)) {

    $smarty->template_dir  = array(
        $alt_skin_dir,
        $xcart_dir . $smarty_skin_dir,
    );

    $compileDir = $var_dirs['templates_c'] . XC_DS . md5($alt_skin_dir);

    if (!is_dir($compileDir)) {

        func_mkdir($compileDir);

    }

    $smarty->compile_dir   = $compileDir;

    if (@file_exists($alt_skin_dir . XC_DS . 'css' . XC_DS . 'altskin.css')) {
        $smarty->assign('AltImagesDir', $alt_skin_info['web_path'] . '/images');
        $smarty->assign('AltSkinDir',   $alt_skin_info['web_path']);
    }

} else {

    $smarty->template_dir    = $xcart_dir . $smarty_skin_dir;
    $smarty->compile_dir       = $var_dirs['templates_c'];

}

$smarty->config_dir = $xcart_dir . $smarty_skin_dir;
$smarty->cache_dir  = $var_dirs['cache'];
$smarty->secure_dir = $xcart_dir . $smarty_skin_dir;
$smarty->debug_tpl  = 'file:debug_templates.tpl';

if ($config['General']['skip_check_compile'] == 'Y') {
    $smarty->compile_check = false;
}    

$smarty->assign('ImagesDir',        $xcart_web_dir . $smarty_skin_dir . '/images');
$smarty->assign('SkinDir',          $xcart_web_dir . $smarty_skin_dir);
$smarty->assign('template_dir',     $smarty->template_dir);
$smarty->assign('sm_prnotice_txt',  @$_prnotice_txt);

/**
 * Smarty object for processing mail templates
 */
$mail_smarty = $smarty;

// WARNING :
// Please ensure that you have no whitespaces / empty lines below this message.
// Adding a whitespace or an empty line below this line will cause a PHP error.
?>
