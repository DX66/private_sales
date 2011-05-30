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
 * This script allows administrator to browse thought templates tree
 * and edit files (these files must be writable for httpd daemon).
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: file_edit.php,v 1.75.2.1 2011/01/10 13:11:45 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

define('USE_TRUSTED_POST_VARIABLES' ,1);
define('USE_TRUSTED_SCRIPT_VARS', 1);

$trusted_post_variables = array(
    'filebody',
);

require './auth.php';

require $xcart_dir . '/include/security.php';

x_load('files');

if (isset($editor_mode))
    unset($editor_mode);

x_session_register('editor_mode');

if (
    empty($login)
    && $editor_mode != 'editor'
) {
    func_403(4);
}

$location[] = array(func_get_langvar_by_name('lbl_edit_templates'), 'file_edit.php');

/**
 * Set-up root directory for templates editing or files in providers directory
 */
$root_dir = $xcart_dir . $smarty_skin_root_dir;

$what_to_edit  = 'templates';
$action_script = 'file_edit.php';

$smarty->assign('what_to_edit', $what_to_edit);
$smarty->assign('action_script', $action_script);

if ('save_file' === $mode) {

    func_backup_skin($root_dir . XC_DS . $filename);

}

include $xcart_dir . '/include/file_operations.php';

if(empty($dir)) {
/**
 * Obtain languages list for compiling facility
 */

    $smarty->assign('languages', $all_languages);
}

/**
 * Skin directory relatively X-Cart root directory for displaying on page
 */
$smarty->assign('root_skin_dir', preg_replace("/^(.*)\/([^\/]*)$/", "\\2",  $root_dir));

$smarty->assign('opener', $opener);

$smarty->assign('has_backup', func_has_skin_backup($root_dir . XC_DS . $file));

// Assign the current location line
$smarty->assign('location', $location);

if (
    file_exists($xcart_dir . '/modules/gold_display.php')
    && is_readable($xcart_dir . '/modules/gold_display.php')
) {
    include $xcart_dir . '/modules/gold_display.php';
}

func_display('admin/home.tpl', $smarty);
?>
