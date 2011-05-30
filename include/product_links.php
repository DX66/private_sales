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
 * Product links
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: product_links.php,v 1.31.2.1 2011/01/10 13:11:50 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('product');

$current_area = 'C';
$login_type = 'C';
$product_info = func_select_product($productid, '',false, false, true);

if ($product_info['product_type'] != 'C') {

if (!empty($active_modules['Magnifier']))
    include $xcart_dir.'/modules/Magnifier/product_magnifier.php';

if (!empty($active_modules['Product_Options']))
    include $xcart_dir.'/modules/Product_Options/customer_options.php';

if(!empty($active_modules['Extra_Fields'])) {
    $extra_fields_provider=$product_info['provider'];
    include $xcart_dir.'/modules/Extra_Fields/extra_fields.php';
}

if(!empty($active_modules['Subscriptions'])) {
    $_products = $products;
    $products = array($product_info);
    include_once $xcart_dir.'/modules/Subscriptions/subscription.php';
    $products = $_products;
}

if(!empty($active_modules['Feature_Comparison']))
    include $xcart_dir.'/modules/Feature_Comparison/product.php';

if (!empty($active_modules['Wholesale_Trading']) && empty($product_info['variantid']))
    include $xcart_dir.'/modules/Wholesale_Trading/product.php';

if (!empty($active_modules['Product_Configurator']) && !empty($_GET['pconf']))
    include $xcart_dir.'/modules/Product_Configurator/slot_product.php';

}

if (!empty($active_modules['Special_Offers']))
    include $xcart_dir.'/modules/Special_Offers/product_offers.php';

$location[] = array(func_get_langvar_by_name('lbl_product_links'), "product_links.php?productid=$productid");
$location[] = array($product_info['product'], '');

$smarty->assign('http_customer_location', $http_location . DIR_CUSTOMER);
$smarty->assign('http_host', "http://".$xcart_http_host);

$smarty->assign('product', $product_info);

$smarty->assign('main','product_links');
?>
