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
 * Home / category page interface
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Customer interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: home.php,v 1.36.2.1 2011/01/10 13:11:43 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

define('OFFERS_DONT_SHOW_NEW',1);

define('STORE_NAVIGATION_SCRIPT', 'Y');

require './auth.php';

$cat = isset($cat) ? abs(intval($cat)) : 0;

if (
    $cat > 0
    && $config['SEO']['clean_urls_enabled'] == 'Y'
    && !defined('DISPATCHED_REQUEST')
) {
    func_clean_url_permanent_redirect('C', intval($cat));
}

include $xcart_dir . '/include/common.php';

if (
    $cat > 0
    && $current_category = func_get_category_data($cat)
) {
    include './products.php';

    $subcat_div_height = 0;
    $subcat_div_width = 100;

    if ($categories = func_get_categories_list($cat, false)) {

        foreach ($categories as $k_sub => $subcat) {

            // Set minimum image size

            $categories[$k_sub]['image_x'] = max($categories[$k_sub]['image_x'], 1);
            $categories[$k_sub]['image_y'] = max($categories[$k_sub]['image_y'], 1);

            // Decrease images size if its real size is greater than maximum
            if ($subcat['is_icon']) {
                list(
                    $categories[$k_sub]['image_x'],
                    $categories[$k_sub]['image_y']
                ) = func_crop_dimensions(
                    $categories[$k_sub]['image_x'],
                    $categories[$k_sub]['image_y'],
                    $config['Appearance']['thumbnail_width'],
                    $config['Appearance']['thumbnail_height']
                );
            }

            // Set div size
            $subcat_div_height = max($subcat_div_height, $categories[$k_sub]['image_y']);
            $subcat_div_width  = max($subcat_div_width, $categories[$k_sub]['image_x']);
        }

        if ($subcat_div_height == 0) {
            $subcat_div_height = 100;
        }

        $smarty->assign('subcat_div_width',  $subcat_div_width);
        $smarty->assign('subcat_div_height', $subcat_div_height + 70);
        $smarty->assign('subcat_img_height', $subcat_div_height);
        $smarty->assign('categories',        $categories);
    }

    $smarty->assign('current_category', $current_category);

} elseif ($cat > 0) {

    $category_is_exists = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[categories] WHERE categoryid = '$cat'") > 0;

    if ($category_is_exists) {
        func_header_location($current_location . DIR_CUSTOMER . '/home.php');
    }

    func_page_not_found();

}

include './featured_products.php';

if (
    !empty($current_category)
    && is_array($current_category['category_location'])
) {

    foreach ($current_category['category_location'] as $k => $v) {

        $location[] = $v;

    }

}

if (!empty($active_modules['Special_Offers'])) {
    include $xcart_dir . '/modules/Special_Offers/category_offers.php';
}

if (!empty($active_modules['Gift_Registry'])) {
    include $xcart_dir . '/modules/Gift_Registry/customer_events.php';
}

$smarty->assign('meta_page_type', 'C');
$smarty->assign('meta_page_id',   $cat);

/**
 * Assign Smarty variables and show template
 */
$smarty->assign('main',     'catalog');
$smarty->assign('cat',      $cat);
$smarty->assign('location', $location);

func_display('customer/home.tpl', $smarty);
?>
