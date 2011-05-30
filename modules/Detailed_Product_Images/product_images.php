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
 * Gets detailed product images data
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: product_images.php,v 1.63.2.1 2011/01/10 13:11:56 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

/**
 * Collect product images
 */
$images = func_query("SELECT imageid, id, image_path, image_type, image_x, image_y, alt FROM $sql_tbl[images_D] WHERE id = '$productid' AND avail = 'Y' ORDER BY orderby, imageid");

if (is_array($images) && !empty($images)) {

    // Get thumbnail's URL (uses only if images stored in FS)

    $max_x = 0;
    $max_y = 0;
    $max_det_image_x = 0;
    $max_det_image_y = 0;

    foreach ($images as $k => $v) {

        $images[$k]['image_url'] = func_get_image_url($v['imageid'], "D", $v["image_path"]);

        list($det_image_x, $det_image_y) = func_crop_dimensions($v['image_x'], $v['image_y'], $config['Appearance']['image_width'], $config['Appearance']['image_height']);

        $max_x = max($max_x, $v['image_x']);
        $max_y = max($max_y, $v['image_y']);

    }

    if ($config['Detailed_Product_Images']['det_image_popup'] == 'Y') {

        $ids = array();

        foreach ($images as $v) {
            $ids[] = $v['imageid'];
        }

        $ids = func_image_cache_get_image('D', 'dpicon', $ids);

        $icon_max_width = 0;
        $icon_max_height = 0;

        foreach ($images as $k => $v) {

            $images[$k]['icon_url'] = $ids[$v['imageid']]['url'];
            $icon_max_width = max($icon_max_width, $ids[$v['imageid']]['width']);
            $icon_max_height = max($icon_max_height, $ids[$v['imageid']]['height']);
        }

        $smarty->assign('icon_box_width', $icon_max_width + 2);
        $smarty->assign('icon_box_height', $icon_max_height + 2);
    }

    $smarty->assign('max_x', $max_x);
    $smarty->assign('max_y', $max_y);

    $images_counter = count($images);

    if ($config['Detailed_Product_Images']['det_image_icons_box'] == 'Y') {

        // Icons box preprocess

        $config['Detailed_Product_Images']['det_image_box_width'] = min(6, max(1, $config['Detailed_Product_Images']['det_image_box_width']));

        $ids = array();

        foreach ($images as $v) {
            $ids[] = $v['imageid'];
        }

        $thbns = func_image_cache_get_image('D', 'dpthmbn', $ids);
        $ids = func_image_cache_get_image('D', 'dpicon', $ids);

        $icon_max_width = 0;
        $icon_max_height = 0;

        foreach ($images as $k => $v) {

            $images[$k]['icon_url'] = $ids[$v['imageid']]['url'];
            $images[$k]['icon_image_x'] = $ids[$v['imageid']]['width'];
            $images[$k]['icon_image_y'] = $ids[$v['imageid']]['height'];

            $icon_max_width = max($icon_max_width, $ids[$v['imageid']]['width']);
            $icon_max_height = max($icon_max_height, $ids[$v['imageid']]['height']);

            $images[$k]['thbn_url'] = $thbns[$v['imageid']]['url'];
            $images[$k]['thbn_image_x'] = $thbns[$v['imageid']]['width'];
            $images[$k]['thbn_image_y'] = $thbns[$v['imageid']]['height'];
            $images[$k]['is_png'] = $v['image_type'] == 'image/png' ? 1 : 0;

            $max_det_image_x = max($max_det_image_x, $images[$k]['thbn_image_x']);
            $max_det_image_y = max($max_det_image_y, $images[$k]['thbn_image_y'], 30);
        }

        $box_width = $config['Detailed_Product_Images']['det_image_box_width'] * ($icon_max_width + 10);

        $smarty->assign(
            'det_image_box_width',
            max(
                $box_width,
                $config['Appearance']['image_width'] > 0 ? $config['Appearance']['image_width'] : $product_info['image_x']
            )
        );

        $max_det_image_x = max($max_det_image_x, $box_width);

        $custom_styles['.dpimages-icons-box'] = array(
            'width' => $box_width . 'px',
        );

        $custom_styles['.dpimages-icons-box a, .dpimages-icons-box a:link, .dpimages-icons-box a:visited, .dpimages-icons-box a:hover, .dpimages-icons-box a:active'] = array(
            'width' => ($icon_max_width + 6) . 'px',
            'height' => ($icon_max_height + 6) . 'px',
        );
    }

    $smarty->assign('images_counter', $images_counter);
    $smarty->assign('images', $images);

}
?>
