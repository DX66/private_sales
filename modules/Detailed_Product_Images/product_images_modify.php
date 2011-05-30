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
 * Detailed images management
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: product_images_modify.php,v 1.50.2.1 2011/01/10 13:11:56 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

x_load('backoffice','product');

// Upload additional product image
if ($mode == 'product_images') {

    $image_perms = func_check_image_storage_perms($file_upload_data, 'D');
    if ($image_perms !== true) {
        $top_message['content'] = $image_perms['content'];
        $top_message['type'] = 'E';
        func_refresh('images');
    }

    $image_posted = func_check_image_posted($file_upload_data, 'D');

    if ($image_posted) {
        $image_id = func_save_image($file_upload_data, 'D', $productid, array('alt' => stripslashes($alt)));
        $ids = array($image_id);
        if ($geid && $fields['new_d_image'] == 'Y') {
            $data = func_query_first("SELECT * FROM $sql_tbl[images_D] WHERE id = '$productid' AND imageid = '$image_id'");
            unset($data['imageid']);
            $data = func_array_map('addslashes', $data);
            while($pid = func_ge_each($geid, 1, $productid)) {
                $id = func_query_first_cell("SELECT imageid FROM $sql_tbl[images_D] WHERE id = '$pid' AND md5 = '$data[md5]'");
                if (!empty($id))
                    func_delete_image($id, 'D', true);

                $data['id'] = $pid;
                $id = func_array2insert('images_D', $data);
                func_image_cache_build('D', $id);
                $ids[] = $id;
            }
        }

        $top_message['content'] = func_get_langvar_by_name('msg_adm_product_images_add');
        $top_message['type'] = 'I';

    }
    func_refresh('images');

// Update product image
} elseif ($mode == 'update_availability' && !empty($image)) {

    foreach ($image as $key => $value) {
        func_array2update('images_D', $value, "imageid = '$key'");
        if($geid && $fields['d_image'][$key] == 'Y') {
            $data = func_query_first("SELECT * FROM $sql_tbl[images_D] WHERE imageid = '$key'");
            unset($data['imageid']);
            $data = func_array_map('addslashes', $data);
            while($pid = func_ge_each($geid, 1, $productid)) {
                $id = func_query_first_cell("SELECT imageid FROM $sql_tbl[images_D] WHERE id = '$pid' AND md5 = '$data[md5]'");
                if (!empty($id))
                    func_delete_image($id, 'D', true);
                $data['id'] = $pid;
                func_array2insert('images_D', $data);
            }
        }
    }
    $top_message['content'] = func_get_langvar_by_name('msg_adm_product_images_upd');
    $top_message['type'] = 'I';
    func_refresh('images');

// Delete product image
} elseif ($mode == 'product_images_delete') {
    if (!empty($iids)) {
        foreach($iids as $imageid => $tmp) {
            $md5 = func_query_first_cell("SELECT md5 FROM $sql_tbl[images_D] WHERE imageid = '$imageid'");
            func_delete_image($imageid, 'D', true);
            if ($geid && $fields['d_image'][$imageid] == 'Y') {
                while($pid = func_ge_each($geid, 1, $productid)) {
                    $id = func_query_first_cell("SELECT imageid FROM $sql_tbl[images_D] WHERE id = '$pid' AND md5 = '$md5'");
                    if (!empty($id))
                        func_delete_image($id, 'D', true);
                }
            }
        }

        $top_message['content'] = func_get_langvar_by_name('msg_adm_product_images_del');
        $top_message['type'] = 'I';
    }
    func_refresh('images');
}

/**
 * Collect product images
 */
$images = func_query("SELECT imageid, id, image_path, image_type, image_x, image_y, image_size, alt, avail, orderby FROM $sql_tbl[images_D] WHERE id = '$productid' ORDER BY orderby, imageid");

if (is_array($images) && !empty($images)) {

    x_load('image');

    list($icon_x, $icon_y) = func_get_proper_dimensions(
        $config['Detailed_Product_Images']['det_image_max_width_icon'], $config['Detailed_Product_Images']['det_image_max_height_icon'],
        50, false
    );

    foreach ($images as $k => $v) {
        $images[$k]['type'] = func_get_image_type($v['image_type']);
        list($images[$k]['thbn_image_x'], $images[$k]['thbn_image_y']) = func_get_proper_dimensions(
            $v['image_x'], $v['image_y'],
            $icon_x, $icon_y
        );

        $images[$k]['thbn_image_x'] = max($images[$k]['thbn_image_x'], 1);
        $images[$k]['thbn_image_y'] = max($images[$k]['thbn_image_y'], 1);
    }

    $smarty->assign(
        'txt_get_images_top_text',
        func_get_langvar_by_name(
            'txt_get_images_top_text',
            array(
                'size' => func_max_upload_image_size($config['setup_images']['D'], false, 'D')
            ),
            false,
            true
        )
    );

    $smarty->assign('images', $images);
}

?>
