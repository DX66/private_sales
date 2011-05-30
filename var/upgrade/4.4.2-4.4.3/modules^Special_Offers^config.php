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
 * Configuration script
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: config.php,v 1.29.2.1 2011/01/10 13:12:02 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }

$config['available_images']['S'] = "U";

$addons['Special_Offers'] = true;

$css_files['Special_Offers'][] = array();

$sql_tbl['offers'] = 'xcart_offers';
$sql_tbl['offers_lng'] = 'xcart_offers_lng';
$sql_tbl['offer_product_sets'] = 'xcart_offer_product_sets';
$sql_tbl['offer_product_params'] = 'xcart_offer_product_params';
$sql_tbl['offer_conditions'] = 'xcart_offer_conditions';
$sql_tbl['offer_condition_params'] = 'xcart_offer_condition_params';
$sql_tbl['offer_bonuses'] = 'xcart_offer_bonuses';
$sql_tbl['offer_bonus_params'] = 'xcart_offer_bonus_params';
$config['special_offers_mark_products'] = true;

$sql_tbl['customer_bonuses'] = 'xcart_customer_bonuses';
$sql_tbl['images_S'] = 'xcart_images_S';
$sql_tbl['condition_memberships'] = 'xcart_condition_memberships';
$sql_tbl['bonus_memberships'] = 'xcart_bonus_memberships';

$sp_offer_types = array('applied', 'free', 'promo');

$sp_total_types = array(
    'ST' => "[".func_get_langvar_by_name('lbl_subtotal')."]",
    'OT' => "[".func_get_langvar_by_name('lbl_subtotal')." - ".func_get_langvar_by_name('lbl_sp_discount')."]",
);
$smarty->assign('sp_total_types', $sp_total_types);

$fake_product_set_id = -1;
$smarty->assign('fake_product_set_id', $fake_product_set_id);

$sp_promo_texts = array(
    'promo_short'            => func_get_langvar_by_name('lbl_sp_promo_text'),
    'promo_long'            => func_get_langvar_by_name('lbl_sp_promo_long'),
    'promo_checkout'        => func_get_langvar_by_name('lbl_sp_promo_checkout'),
    'promo_items_amount'    => func_get_langvar_by_name('lbl_sp_promo_items_amount'),
);
$smarty->assign('sp_promo_texts', $sp_promo_texts);

if (defined('TOOLS')) {
    $tbl_demo_data['Special_Offers'] = array(
        'offer_bonus_params' => '',
        'offer_bonuses' => '',
        'offer_condition_params' => '',
        'offer_conditions' => '',
        'offers' => '',
        'offers_lng' => '',
        'bonus_memberships' => '',
        'condition_memberships' => '',
        'offer_condition_params' => ''
    );
}

?>
