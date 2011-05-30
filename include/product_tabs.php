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
 * Define product tabs
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: product_tabs.php,v 1.10.2.2 2011/01/25 09:43:12 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header('Location: ../'); die('Access denied'); }

if (isset($printable)) {
    return;
}

$product_tabs = array();

if (!empty($active_modules['Magnifier']) && $config['Magnifier']['magnifier_image_popup'] != 'Y' && !empty($zoomer_images)) {
    $product_tabs[] = array(
        'title'  => func_get_langvar_by_name('lbl_x_magnifier'),
        'tpl'    => 'modules/Magnifier/product_magnifier.tpl',
        'anchor' => 'xmagnifier'
    );
}

if (!empty($active_modules['Special_Offers']) && !empty($offers)) {
    $product_tabs[] = array(
        'title'  => func_get_langvar_by_name('lbl_special_offers'),
        'tpl'    => 'modules/Special_Offers/customer/product_offers_short_list.tpl',
        'anchor' => 'soffers'
    );
}

if ($config['Appearance']['send_to_friend_enabled'] == 'Y') {
    $product_tabs[] = array(
        'title'  => func_get_langvar_by_name('lbl_send_to_friend'),
        'tpl'    => 'customer/main/send_to_friend.tpl',
        'anchor' => 'send2friend'
    );
}

if (!empty($active_modules['Detailed_Product_Images']) && $config['Detailed_Product_Images']['det_image_popup'] != 'Y' && !empty($images)) {
    $product_tabs[] = array(
        'title'  => func_get_langvar_by_name('lbl_detailed_images'),
        'tpl'    => 'modules/Detailed_Product_Images/product_images.tpl',
        'anchor' => 'dpimages'
    );
}

if (!empty($active_modules['Upselling_Products']) && !empty($product_links)) {
    $product_tabs[] = array(
        'title'  => func_get_langvar_by_name('lbl_related_products'),
        'tpl'    => 'modules/Upselling_Products/related_products.tpl',
        'anchor' => 'related'
    );
}

if (!empty($active_modules['Recommended_Products']) && !empty($recommends)) {
    $product_tabs[] = array(
        'title'  => func_get_langvar_by_name('txt_recommends_comment'),
        'tpl'    => 'modules/Recommended_Products/recommends.tpl',
        'anchor' => 'recommends'
    );
}

if (
    !empty($active_modules['Customer_Reviews'])
    && (
        $config['Customer_Reviews']['customer_reviews'] == 'Y'
        || $config['Customer_Reviews']['customer_voting'] == 'Y'
    )
) {
    $product_tabs[] = array(
        'title'  => func_get_langvar_by_name('lbl_customers_feedback'),
        'tpl'    => 'modules/Customer_Reviews/vote_reviews.tpl',
        'anchor' => 'feedback'
    );
}

if (!empty($product_tabs)) {
    $smarty->assign('product_tabs', $product_tabs);
    if ($config['Appearance']['display_product_tabs'] == 'Y') {
        $smarty->assign('show_as_tabs', true);
    }
}

?>
