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
 * Displays an image in a pop up window
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Customer interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: popup_image.php,v 1.35.2.1 2011/01/10 13:11:43 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './top.inc.php';
require './init.php';

include $xcart_dir . '/include/get_language.php';

x_load(
    'files',
    'image'
);

/**
 * Check input data
 */
if (!isset($config['setup_images'][$type])) {
    func_close_window();
}

// Get image(s)
$images = func_query("SELECT ".(($config['available_images'][$type] == "U") ? "id" : "imageid")." as id, imageid, image_path, image_x, image_y, image_size, alt FROM ".$sql_tbl['images_'.$type]." WHERE id = '$id' AND avail = 'Y' ORDER BY orderby, imageid");

if (empty($images)) {
    func_close_window();
}

$objects_per_page = 1;

$total_items = count($images);

include $xcart_dir . '/include/navigation.php';

$max_x = 0;
$max_y = 0;

foreach ($images as $k => $v) {

    $images[$k]['url'] = func_get_image_url($v['id'], $type, $v["image_path"]);

    $max_x = max($max_x, $v['image_x']);
    $max_y = max($max_y, $v['image_y']);

}

$smarty->assign('max_x', $max_x);
$smarty->assign('max_y', $max_y);

if (!empty($title)) {

    $title = stripslashes($title);

    $smarty->assign('title', $title);

}

if (count($images) > 1) {

    $smarty->assign('js_selector', true);

}

if ($type == 'D') {

    $ids = array();

    foreach ($images as $v) {
        $ids[] = $v['imageid'];
    }

    $ids = func_image_cache_get_image('D', 'dpicon', $ids);

    $icon_max_width = 0;
    $icon_max_height = 0;

    foreach ($images as $k => $v) {

        $images[$k]['icon_url'] = $ids[$v['imageid']]['url'];
        $images[$k]['icon_image_x'] = $ids[$v['imageid']]['width'];
        $images[$k]['icon_image_y'] = $ids[$v['imageid']]['height'];

        $icon_max_width     = max($icon_max_width, $ids[$v['imageid']]['width']);
        $icon_max_height     = max($icon_max_height, $ids[$v['imageid']]['height']);
    }

} else {

    $icon_max_width = 64;
    $icon_max_height = 52;

    foreach ($images as $k => $v) {

        list(
            $images[$k]['icon_image_x'],
            $images[$k]['icon_image_y']
        ) = func_get_proper_dimensions($v['image_x'], $v['image_x'], $icon_max_width, $icon_max_height);

    }

}

$smarty->assign('icon_box_width',       $icon_max_width + 2);
$smarty->assign('icon_box_height',      $icon_max_height + 2);

$smarty->assign('href',                 "popup_image.php?type=$type&amp;id=$id&amp;title=" . urlencode($title));
$smarty->assign('navigation_script',    "popup_image.php?type=$type&amp;open_in_layer=$open_in_layer&amp;id=$id&amp;title=" . urlencode($title));

$smarty->assign('images_count',         count($images));
$smarty->assign('images',               $images);
$smarty->assign('id',                   $id);
$smarty->assign('type',                 $type);
$smarty->assign('area',                 $area);

$smarty->assign('page',                 $page);
$smarty->assign('current_image',        $images[$page - 1]);

$smarty->assign('template_name',        'main/popup_image.tpl');

func_display('customer/help/popup_info.tpl', $smarty);

?>
