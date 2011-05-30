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
 * Product magnified images management
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: product_magnifier_modify.php,v 1.25.2.1 2011/01/10 13:11:58 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */


if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

if (!empty($geid)) {
    $ge_pids = func_query_column("SELECT productid FROM $sql_tbl[ge_products] WHERE geid = '$geid' ORDER BY productid", "productid");
}

if ($mode == 'product_zoomer') {

    // Add image
    if (empty($file_upload_data) || empty($file_upload_data['Z'])) {
        func_refresh('zoomer');
    }

    $root_folder2save = func_image_dir('Z');
    if (!file_exists($root_folder2save))
        func_mkdir($root_folder2save);

    if (!file_exists($root_folder2save)) {
        $top_message['content'] = func_get_langvar_by_name('msg_adm_incorrect_store_path', array('path' => $root_folder2save));
        $top_message['type'] = 'E';
        func_refresh('zoomer');
    }

    $query_data = array(
        'id' => $productid,
        'date' => XC_TIME
    );
    $imageid = func_array2insert('images_Z', $query_data);

    $folder2save = $root_folder2save.XC_DS.$productid.XC_DS.$imageid.XC_DS;
    if (!file_exists($folder2save))
        func_mkdir($folder2save);

    if (!file_exists($folder2save)) {
        db_query("DELETE FROM $sql_tbl[images_Z] WHERE imageid = '$imageid'");
        $top_message['content'] = func_get_langvar_by_name('msg_adm_incorrect_store_perms', array('path' => $root_folder2save));
        $top_message['type'] = 'E';
        func_refresh('zoomer');
    }

    $image_size = func_magnifier_get_levels($file_upload_data['Z']['file_path'], $folder2save);
    if (!$image_size) {
        func_rm_dir($xcart_dir.'/images/Z/'.$productid.'/'.$imageid);
        db_query("DELETE FROM $sql_tbl[images_Z] WHERE imageid = '$imageid'");
        @unlink($file_upload_data['Z']['file_path']);

        $top_message['content'] = func_get_langvar_by_name('msg_adm_incorrect_format_4zoomer');
        $top_message['type'] = 'E';
        func_refresh('zoomer');
    }

    $query_data = array(
        'image_x' => $image_size['x'],
        'image_y' => $image_size['y'],
        'image_path' => './images/Z/'.$productid.'/'.$imageid.'/thumbnail.'.$magnifier_sets['extension'],
        'image_type' => 'image/jpeg',
        'md5' => func_md5_file($xcart_dir.'/images/Z/'.$productid.'/'.$imageid.'/init_image.jpg'),
        'image_size' => func_filesize($xcart_dir.'/images/Z/'.$productid.'/'.$imageid.'/thumbnail.'.$magnifier_sets['extension']),
        'filename' => addslashes($file_upload_data['Z']['filename']),
        'orderby' => func_query_first_cell("SELECT MAX(orderby) FROM $sql_tbl[images_Z] WHERE id = '$productid'")+1
    );
    func_array2update('images_Z', $query_data, "imageid = '$imageid'");

    if (!empty($geid) && !empty($fields['new_z_image'])) {
        while ($pid = func_ge_each($geid, 1, $productid)) {
            $query_data['id'] = $pid;
            $query_data['date'] = XC_TIME;
            $pimageid = func_array2insert('images_Z', $query_data);

            $pfolder2save = $root_folder2save.XC_DS.$pid.XC_DS.$pimageid.XC_DS;
            if (!file_exists($pfolder2save)) {
                func_mkdir($pfolder2save);
            }

            func_magnifier_dircpy($folder2save, $pfolder2save);

            $query_data['image_path'] = "./images/Z/".$pid."/".$pimageid."/thumbnail.".$magnifier_sets["extension"];
            func_array2update('images_Z', $query_data, "imageid = '$pimageid'");
        }
    }

    @unlink($file_upload_data['Z']['file_path']);

    $top_message['content'] = func_get_langvar_by_name('msg_adm_images_added_4zoomer');
    func_refresh('zoomer');

} elseif ($mode == 'reslice' && !empty($iids) && is_array($iids)) {

    $z_reslice_error_text = array();
    $z_reslice_error_counter = 0;
    $z_reslice_error_pos = array();
    foreach($iids as $imageid => $v) {
        $image_md5 = func_query_first_cell("SELECT md5 FROM $sql_tbl[images_Z] WHERE imageid = '$imageid'");

        if (!empty($geid) && isset($fields['z_image']) && is_array($fields['z_image']) && !empty($fields['z_image'][$imageid])) {
            if (!empty($image_md5) && !empty($ge_pids) && func_magnifier_check_md5_uniqueness($image_md5, $ge_pids, true)) {
                $ge_images = func_query("SELECT id, imageid FROM $sql_tbl[images_Z] WHERE md5 = '".$image_md5."' AND id IN ('" . join("', '", $ge_pids) . "') ORDER BY id");
                foreach ($ge_images as $zimage) {
                    list ($z_error_type, $z_error_content) = func_magnifier_reslice_image($zimage['imageid'], $zimage['id']);
                    if ($z_error_type == 'E') {
                        $z_reslice_error_counter++;
                        $z_reslice_error_pos[$imageid] = func_get_langvar_by_name('lbl_pos')." ".$zoomer_image[$imageid]['orderby'];
                        $z_reslice_error_text[$imageid] = $z_error_content;
                    }
                }
            }
        } else {
            list ($z_error_type, $z_error_content) = func_magnifier_reslice_image($imageid, $productid);
            if ($z_error_type == 'E') {
                $z_reslice_error_counter++;
                $z_reslice_error_pos[$imageid] = func_get_langvar_by_name('lbl_pos')." ".$zoomer_image[$imageid]['orderby'];
                $z_reslice_error_text[$imageid] = $z_error_content;
            }
        }
    }
    if (!empty($z_reslice_error_text)) {
        $error_text = '';
        if ($z_reslice_error_counter > 1) {
            foreach($z_reslice_error_text as $imageid => $v)
                $error_text = $error_text.$v." (".$z_reslice_error_pos[$imageid].")<br/>";
        }else {
            $error_text = array_pop($z_reslice_error_text);
        }
        $top_message['type'] = 'E';
        $top_message['content'] = $error_text;
    } else {
        $top_message['content'] = func_get_langvar_by_name('msg_adm_images_updated_4zoomer');
    }

    func_refresh('zoomer');

} elseif ($mode == 'zoomer_update_availability' && !empty($zoomer_image) && is_array($zoomer_image)) {

    // Update images
    foreach ($zoomer_image as $key => $value) {
        $image_md5 = func_query_first_cell("SELECT md5 FROM $sql_tbl[images_Z] WHERE imageid = '$key'");
        db_query("UPDATE $sql_tbl[images_Z] SET orderby='".$value["orderby"]."', avail='".$value["avail"]."' WHERE imageid='$key'");

        if (!empty($geid) && isset($fields['z_image']) && is_array($fields['z_image']) && !empty($fields['z_image'][$key])) {
            if (!empty($image_md5) && !empty($ge_pids) && func_magnifier_check_md5_uniqueness($image_md5, $ge_pids, true)) {
                db_query("UPDATE $sql_tbl[images_Z] SET orderby='".$value["orderby"]."', avail='".$value["avail"]."' WHERE md5 ='" . $image_md5. "' AND id IN ('" . join("', '", $ge_pids)."')");
            }
        }
    }

    $top_message['content'] = func_get_langvar_by_name('msg_adm_images_updated_4zoomer');
    func_refresh('zoomer');

} elseif ($mode == 'product_zoomer_delete' && !empty($iids) && is_array($iids)) {

    // Delete images
    foreach(array_keys($iids) as $imageid) {
        $image_md5 = func_query_first_cell("SELECT md5 FROM $sql_tbl[images_Z] WHERE imageid = '$imageid'");

        if (!empty($geid) && isset($fields['z_image']) && is_array($fields['z_image']) && !empty($fields['z_image'][$imageid])) {
            if (!empty($image_md5) && !empty($ge_pids) && func_magnifier_check_md5_uniqueness($image_md5, $ge_pids, true)) {
                $ge_images = func_query("SELECT id, imageid FROM $sql_tbl[images_Z] WHERE md5 = '".$image_md5."' AND id IN ('" . join("', '", $ge_pids) . "') ORDER BY id");
                foreach ($ge_images as $zimage) {
                    db_query("DELETE FROM $sql_tbl[images_Z] WHERE imageid = '".$zimage['imageid']."'");
                    func_rm_dir(func_image_dir('Z').XC_DS.$zimage['id'].XC_DS.$zimage['imageid']);
                }
            }
        } else {
            db_query("DELETE FROM $sql_tbl[images_Z] WHERE imageid = '".$imageid."'");
            func_rm_dir(func_image_dir('Z').XC_DS.$productid.XC_DS.$imageid);
        }
    }

    $top_message['content'] = func_get_langvar_by_name('msg_adm_images_deleted_4zoomer');
    func_refresh('zoomer');
}

if (!empty($productid)) {
    $zoomer_images = func_query("SELECT * FROM $sql_tbl[images_Z] WHERE id = '".$productid."' ORDER BY orderby, imageid");
    if (!empty($zoomer_images)) {
        foreach ($zoomer_images as $z_key => $z_image) {
            if (file_exists(func_image_dir('Z').XC_DS.$productid.XC_DS.$z_image['imageid'].XC_DS.'init_image.jpg')) {
                $zoomer_images[$z_key]['init_image'] = 1;
            }

            if (!empty($geid) && !empty($ge_pids) && func_magnifier_check_md5_uniqueness($z_image['md5'], $ge_pids, true)) {
                $zoomer_images[$z_key]['common_image'] = 'Y';
            }
        }

        $smarty->assign('zoomer_images', $zoomer_images);
    }
}

$smarty->assign('gd_not_loaded', $gd_not_loaded);
$smarty->assign('gd_config', $gd_config);

?>
