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
 * Search for configurable products
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: pconf.php,v 1.22.2.1 2011/01/10 13:11:46 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './auth.php';
require $xcart_dir.'/include/security.php';

if (empty($active_modules['Product_Configurator']))
    func_403(67);

$provider_condition = '';

$location[] = array(func_get_langvar_by_name('lbl_product_configurator'), 'pconf.php');
$pconf_title = func_get_langvar_by_name('lbl_product_configurator');

if ($REQUEST_METHOD != 'POST') {
/**
 * Define data for the navigation within section
 */
    $dialog_tools_data = array();

    $dialog_tools_data['right'][] = array('link' => "pconf.php?mode=search", 'title' => func_get_langvar_by_name('lbl_pconf_search'));
    $dialog_tools_data['right'][] = array('link' => "product_modify.php?mode=pconf", 'title' => func_get_langvar_by_name('lbl_pconf_confproduct'));
    $dialog_tools_data['right'][] = array('link' => "pconf.php?mode=types", 'title' => func_get_langvar_by_name('lbl_pconf_define_types'));
    $dialog_tools_data['right'][] = array('link' => "pconf.php?mode=about", 'title' => func_get_langvar_by_name('lbl_pconf_about'));

}

if (!in_array($mode, array('types', 'search', 'about')))
    $mode = 'search';

if ($mode == 'types') {
    include $xcart_dir.'/modules/Product_Configurator/pconf_types.php';
    $location[] = array(func_get_langvar_by_name('lbl_pconf_define_types'), '');
    $pconf_title = func_get_langvar_by_name('lbl_pconf_define_types');
}

if ($mode == 'search') {
    include $xcart_dir.'/modules/Product_Configurator/pconf_search.php';
    $location[] = array(func_get_langvar_by_name('lbl_search_products'), '');
    $pconf_title = func_get_langvar_by_name('lbl_search_products');
}

if ($mode == 'about') {
    $smarty->assign('mode', 'about');
    $location[] = array(func_get_langvar_by_name('lbl_pconf_about'), '');
    $pconf_title = func_get_langvar_by_name('lbl_pconf_about');
}

$smarty->assign('main','product_configurator');
$smarty->assign('main_mode','manage');

$smarty->assign('pconf_title', $pconf_title);

// Assign the current location line
$smarty->assign('location', $location);

// Assign the section navigation data
$smarty->assign('dialog_tools_data', $dialog_tools_data);

if (
    file_exists($xcart_dir.'/modules/gold_display.php')
    && is_readable($xcart_dir.'/modules/gold_display.php')
) {
    include $xcart_dir.'/modules/gold_display.php';
}
func_display('admin/home.tpl',$smarty);
?>

