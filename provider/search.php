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
 * Products search interface
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Provder interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: search.php,v 1.78.2.2 2011/02/02 16:01:15 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

define('NUMBER_VARS', "posted_data['price_min'],posted_data['price_max'],posted_data['avail_min'],posted_data['avail_max'],posted_data['weight_min'],posted_data['weight_max']");
require './auth.php';
require $xcart_dir.'/include/security.php';

x_session_register('search_data');

/**
 * Define data for the navigation within section
 */
$dialog_tools_data['left'][] = array('link' => 'search.php', 'title' => func_get_langvar_by_name('lbl_search_products'));

$dialog_tools_data['left'][] = array('link' => 'product_modify.php', 'title' => func_get_langvar_by_name('lbl_add_product'));

if (defined('IS_ADMIN_USER')) {
    $dialog_tools_data['right'][] = array('link' => $xcart_catalogs['admin']."/categories.php", "title" => func_get_langvar_by_name("lbl_categories"));
}

if (!empty($active_modules['Product_Configurator']))
    $dialog_tools_data['right'][] = array('link' => 'pconf.php', 'title' => func_get_langvar_by_name('lbl_product_configurator'));

if (!empty($active_modules['Manufacturers']))
    $dialog_tools_data['right'][] = array('link' => 'manufacturers.php', 'title' => func_get_langvar_by_name('lbl_manufacturers'));

$dialog_tools_data['right'][] = array('link' => 'orders.php', 'title' => func_get_langvar_by_name('lbl_orders'));

if (empty($search_data['products'])) {
    $search_data['products'] = array(
        'category_main'        => true,
        'category_extra'    => true,
        'by_title'            => true,
        'by_shortdescr'        => true,
        'by_fulldescr'        => true,
        'by_keywords'        => true,
    );
    $search_data['products']['search_in_subcategories'] = true;
}

if ($cat && $mode == 'search') {
    $search_data['products'] = array();
    $search_data['products']['categoryid'] = $cat;
    $search_data['products']['category_main'] = 'Y';
    $search_data['products']['category_extra'] = 'Y';
    if ($subcats == 'Y') {
        $search_data['products']['search_in_subcategories'] = $subcats;
    }
    if (!isset($sort)) {
        $sort = $search_data['products']['sort_field'] = 'orderby';
    }
    if (!isset($sort_direction)) {
        $search_data['products']['sort_direction'] = 0;
    }
}

/**
 * Use this condition when single mode is disabled
 */
if (!$single_mode) {
    $search_data['products']['provider'] = $logged_userid;
    x_session_save('search_data');
}

include $xcart_dir.'/include/search.php';

if(!$single_mode) {
    unset($search_data['products']['provider']);
    if(empty($search_data['products']))
        $search_data['products'] = '';
    $smarty->assign('search_prefilled', $search_data['products']);
    $search_data['products']['provider'] = $logged_userid;
    x_session_save('search_data');
}

if ($REQUEST_METHOD == 'GET' && $mode == 'search' && empty($products) && empty($top_message['content'])) {
    $no_results_warning = array('type' => 'W', 'content' => func_get_langvar_by_name("lbl_warning_no_search_results", false, false, true));
    $smarty->assign('top_message', $no_results_warning);
}

$location[] = array(func_get_langvar_by_name('lbl_products_management'), 'search.php');

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
func_display('provider/home.tpl',$smarty);

?>
