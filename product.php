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
 * Product page interface
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Customer interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: product.php,v 1.67.2.2 2011/01/10 13:11:44 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

define('OFFERS_DONT_SHOW_NEW',1);

require './auth.php';

if (
    isset($productid)
    && !empty($productid)
    && $config['SEO']['clean_urls_enabled'] == 'Y'
    && !defined('DISPATCHED_REQUEST')
) {
    func_clean_url_permanent_redirect('P', intval($productid));
}

x_load(
    'product',
    'templater'
);

/**
 * Put all product info into $product array
 */

$product_info = func_select_product($productid, @$user_account['membershipid']);

$cat = isset($cat) ? abs(intval($cat)) : 0;

if ($cat > 0) {
    $is_product_cat = func_query_first_cell("SELECT productid FROM $sql_tbl[products_categories] WHERE productid='$productid' AND categoryid='$cat'");
}

if (
    $cat == 0
    || empty($is_product_cat)
) {
    $cat = $product_info['categoryid'];
}

include $xcart_dir . '/include/common.php';

$main = 'product';

$smarty->assign('main', $main);

if (!empty($product_info['productid'])) {

    $product_info['appearance'] = func_get_appearance_data($product_info);

}

include $xcart_dir . DIR_CUSTOMER . '/send_to_friend.php';

if (!empty($send_to_friend_info)) {

    $smarty->assign('send_to_friend_info', $send_to_friend_info);

    if (!empty($active_modules['Image_Verification'])) {

        $smarty->assign('antibot_friend_err', $send_to_friend_info['antibot_err']);

    }

    x_session_unregister('send_to_friend_info');

}

if (!empty($active_modules['Detailed_Product_Images']))
    include $xcart_dir . '/modules/Detailed_Product_Images/product_images.php';

if (!empty($active_modules['Magnifier']))
    include $xcart_dir . '/modules/Magnifier/product_magnifier.php';

if (!empty($active_modules['Gift_Registry'])) {
    include $xcart_dir . '/modules/Gift_Registry/customer_wlproduct.php';
}

if (!empty($active_modules['Product_Options']))
    include $xcart_dir . '/modules/Product_Options/customer_options.php';

if (!empty($active_modules['Upselling_Products']))
    include $xcart_dir . '/modules/Upselling_Products/related_products.php';

if (!empty($active_modules['Advanced_Statistics']) && !defined('IS_ROBOT'))
    include $xcart_dir . '/modules/Advanced_Statistics/prod_viewed.php';

if ($product_info['product_type'] != 'C') {

    // If this product is not configurable

    if (
        $config['General']['show_outofstock_products'] != 'Y'
        && empty($product_info['distribution'])
    ) {

        $is_avail = true;

        if (
            $product_info['avail'] <= 0
            && empty($variants)
        ) {

            $is_avail = false;

        } elseif(!empty($variants)) {

            $is_avail = false;

            foreach($variants as $v) {

                if ($v['avail'] > 0) {

                    $is_avail = true;

                    break;

                }

            }

        }

        if (
            !empty($cart['products'])
            && !$is_avail
        ) {

            foreach($cart['products'] as $v) {

                if($product_info['productid'] == $v['productid']) {

                    $is_avail = true;

                    break;

                }

            }

        }

        if (!$is_avail) {

            func_header_location("error_message.php?product_disabled");

        }

    }

    if(!empty($active_modules['Extra_Fields'])) {

        $extra_fields_provider = $product_info['provider'];

        include $xcart_dir . '/modules/Extra_Fields/extra_fields.php';

    }

    if(!empty($active_modules['Subscriptions'])) {

        $_products = $products;

        $products = array($product_info);

        include_once $xcart_dir . '/modules/Subscriptions/subscription.php';

        $products = $_products;

    }

    if(!empty($active_modules['Feature_Comparison']))
        include $xcart_dir . '/modules/Feature_Comparison/product.php';

    if (!empty($active_modules['Wholesale_Trading']) && empty($product_info['variantid']))
        include $xcart_dir . '/modules/Wholesale_Trading/product.php';

    if (
        !empty($active_modules['Product_Configurator'])
        && !empty($_GET['pconf'])
        && $mode != 'add_vote'
    ) {
        include $xcart_dir . '/modules/Product_Configurator/slot_product.php';
    }

}

if (
    !empty($product_info['images'])
    && is_array($product_info['images'])
) {
    list(
        $product_image_width,
        $product_image_height
    ) = func_crop_dimensions(
        $product_info['image_x'],
        $product_info['image_y'],
        $config['Appearance']['image_width'],
        $config['Appearance']['image_height']
    );
}

if (is_array($variants)) {

    $max_var_image_x = 0;

    $max_var_image_y = 0;

    foreach ($variants as $k => $v) {

        if (!empty($v['is_image'])) {

            list(
                $var_image_x,
                $var_image_y
            ) = func_crop_dimensions(
                $v['image_W_x'],
                $v['image_W_y'],
                $config['Appearance']['image_width'],
                $config['Appearance']['image_height']
            );

            $max_var_image_x = max($var_image_x, $max_var_image_x);

            $max_var_image_y = max($var_image_y, $max_var_image_y);

        }

    }

    $product_image_width = ($max_var_image_x)
        ? $max_var_image_x
        : $product_image_width;

    $product_image_height = ($max_var_image_y)
        ? $max_var_image_y
        : $product_image_height;

}

$smarty->assign('max_image_width',  isset($max_det_image_x) ? max($product_image_width, $max_det_image_x) : $product_image_width);
$smarty->assign('max_image_height', isset($max_det_image_y) ? max($product_image_height, $max_det_image_y) : $product_image_height);

$smarty->assign('prepare_fields',   func_wm_tpl_prep(@$vid));

if (!empty($active_modules['Recommended_Products']))
    include $xcart_dir . '/recommends.php';

if (!empty($active_modules['SnS_connector']))
    include $xcart_dir . '/modules/SnS_connector/product.php';

if (!empty($active_modules['Customer_Reviews']))
    include $xcart_dir . '/modules/Customer_Reviews/vote.php';

// Get category location
if (
    $cat > 0
    && $current_category = func_get_category_data($cat)
) {
    
    if (is_array($current_category['category_location'])) {
        foreach ($current_category['category_location'] as $k => $v) {

            $location[] = $v;

        }
    }

}

if (!empty($product_info)) {

    $location[] = array(
        $product_info['product'],
        '',
    );

}

if (!empty($active_modules['Special_Offers'])) {
    include $xcart_dir . '/modules/Special_Offers/product_offers.php';
}

if (!empty($active_modules['Recently_Viewed'])) {
   rviewed_save_product($_GET['productid']);
}

$product_info['quantity_input_box_enabled'] = $config['Appearance']['show_quantity_as_box'] == 'Y';

$smarty->assign('product', $product_info);

// Define product tabs
include $xcart_dir . '/include/product_tabs.php';

$smarty->assign('meta_page_type', 'P');
$smarty->assign('meta_page_id',   $product_info['productid']);

// Assign the current location line
$smarty->assign('location',       $location);

func_display('customer/home.tpl', $smarty);
?>
