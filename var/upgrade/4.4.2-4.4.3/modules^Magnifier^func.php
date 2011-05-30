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
 * Module functions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.php,v 1.30.2.1 2011/01/10 13:11:58 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

// Functions of the Magnifier module

/**
 * Write XML data to file
 */
function func_magnifier_write_xml($data, $fname)
{
    $fp = @fopen($fname, "w+");
    if (!$fp)
        return false;

    fwrite($fp, $data);
    fclose($fp);
    func_chmod_file($fname);

    return true;
}

/**
 * Write image content to file
 */
function func_magnifier_write_tile($data, $fname, $qlt)
{
    if (!is_writable(dirname($fname)))
        return false;

    imagejpeg($data, $fname, $qlt);

    return true;
}

/**
 * Directory copy
 */
function func_magnifier_dircpy($source, $dest)
{
    $ddir = @opendir($source);
    if (!$ddir)
        return false;

    while ($file = readdir($ddir)) {
        if ($file == '.' || $file == '..' || !is_file($source.XC_DS.$file))
            continue;

        @copy($source.XC_DS.$file, $dest.XC_DS.$file);
    }
    closedir($ddir);
}

/**
 * Check writable permissions
 */
function func_check_dir_permissions($source)
{
    $ddir = @opendir($source);
    if (!$ddir)
        return false;

    while ($file = readdir($ddir)) {
        if ($file == '..' || (!is_file($source.$file) && $file != '.'))
            continue;

        if (!is_writable($source.$file))
            return false;
    }
    closedir($ddir);

    return true;
}

function func_magnifier_get_levels_gradation($x_, $y_, $max_)
{
    global $magnifier_sets;

    if ($x_/$y_ > $magnifier_sets['x_work_area']/$magnifier_sets['y_work_area']) {
        $x_fit = $magnifier_sets['x_work_area'];
        $y_fit = round($x_fit*$y_/$x_);
    } else {
        $y_fit = $magnifier_sets['y_work_area'];
        $x_fit = round($y_fit*$x_/$y_);
    }

    if ($max_ >= 1500) {
        $num_levels = 4;
    } elseif ($max_ >= 2*max($magnifier_sets['x_work_area'], $magnifier_sets['y_work_area']) && $max_ < 1500) {
        $num_levels = 3;
    } elseif ($max_ >= max($magnifier_sets['x_work_area'], $magnifier_sets['y_work_area']) && $max_ < 2*max($magnifier_sets['x_work_area'], $magnifier_sets['y_work_area'])) {
        $num_levels = 2;
    } else {
        $num_levels = 1;
    }

    if ($num_levels > 1) {
        $max_zoom = $x_/$x_fit;
        $zoom_step = ($max_zoom - 1)/($num_levels-1);
    } else {
        $max_zoom = 1;
    }

    $data_lvls = array();

    for ($i=$num_levels-1; $i>=0; $i--) {
        $data_lvls[$i]['zoom'] = round(($i*$zoom_step + 1)*100);
        $data_lvls[$i]['rate_zoom'] = $data_lvls[$i]['zoom']/(100*$max_zoom);
        $data_lvls[$i]['slice'] = (($i == 0) ? false : true);
        $data_lvls[$i]['save_level'] = (($i == 0) ? true : false);
        $data_lvls[$i]['relative_rate_zoom'] = ( empty($data_lvls[$i+1]['rate_zoom']) ? 1 : $data_lvls[$i]['rate_zoom']/$data_lvls[$i+1]['rate_zoom'] );
    }

    return $data_lvls;
}

function func_magnifier_get_image_data($pict)
{

    $image_data = array();

    list($image_data['image_x'], $image_data['image_y'], $image_data['type']) = @getimagesize($pict);

    switch ($image_data['type']) {
    case 1:
        if (!function_exists('imagecreatefromgif')) {

            return false;
        }
        $image_data['data'] = @imagecreatefromgif($pict);
        break;

    case 2:
        if (!function_exists('imagecreatefromjpeg')) {

            return false;
        }
        $image_data['data'] = @imagecreatefromjpeg($pict);
        break;

    case 3:
        if (!function_exists('imagecreatefrompng')) {

            return false;
        }
        $image_data['data'] = @imagecreatefrompng($pict);
        break;

    default:
        return false;
    }

    return $image_data;
}

function func_create_thumbnail($pict, $x_start, $y_start, $x_end, $y_end, $folder2save, $extension){
    global $magnifier_sets;

    $init_image = func_magnifier_get_image_data($pict);
    if (empty($init_image) || !is_array($init_image) || empty($init_image['data'])) {

        return false;
    }

    $x_start = round($x_start);
    $y_start = round($y_start);
    $x_end = round($x_end)-$x_start;
    $y_end = round($y_end)-$y_start;

    $thmb_data = imageCreatetruecolor($magnifier_sets['x_thmb'], $magnifier_sets['y_thmb']);
    $grey_color = imagecolorallocate($thmb_data, 0xF0, 0xF0, 0xF0);

    imagecopyresampled($thmb_data, $init_image['data'], 0, 0, $x_start, $y_start, $magnifier_sets['x_thmb'], $magnifier_sets['y_thmb'], $x_end, $y_end);
    if($x_start<0 || $y_start<0) {
        imagefill ($thmb_data, 0, 0, $grey_color);
    }
    if($x_end>$init_image['image_x'] || $y_end>$init_image['image_y']) {
        imagefill ($thmb_data, $magnifier_sets['x_thmb']-1, $magnifier_sets['y_thmb']-1, $grey_color);
    }

    if (!func_magnifier_write_tile($thmb_data, $folder2save.'thumbnail.'.$extension, $magnifier_sets['jpg_qlt_thmb']))
        return false;

    imagedestroy($thmb_data);
    imagedestroy($init_image['data']);

    return true;
}

function func_magnifier_get_levels($pict, $folder2save){
    global $magnifier_sets;

    $init_image = func_magnifier_get_image_data($pict);
    if (empty($init_image) || !is_array($init_image) || empty($init_image['data'])) {

        return false;
    }

    $init_image['max'] = max($init_image['image_x'], $init_image['image_y']);

    // Resize if the image is to big
    if ($init_image['max'] > $magnifier_sets['max_image_size']) {
        $new_x = round($init_image['image_x'] * $magnifier_sets['max_image_size']/$init_image['max']);
        $new_y = round($init_image['image_y'] * $magnifier_sets['max_image_size']/$init_image['max']);
        imagecopyresampled($init_image['data'], $init_image['data'], 0, 0, 0, 0, $new_x, $new_y, $init_image['image_x'], $init_image['image_y']);

        $init_image['image_x'] = $new_x;
        $init_image['image_y'] = $new_y;
    }

    if ($magnifier_sets['save_init_image']) {
        // Save init image in the same folder

        if ($init_image['max'] > $magnifier_sets['max_image_size']) {
            // Copy part of the image
            $save_init_image_data = imageCreatetruecolor($init_image['image_x'], $init_image['image_y']);
            imagecopyresampled($save_init_image_data, $init_image['data'], 0, 0, 0, 0, $init_image['image_x'], $init_image['image_y'], $init_image['image_x'], $init_image['image_y']);

            if (!func_magnifier_write_tile($save_init_image_data, $folder2save.'init_image.jpg', $magnifier_sets['jpg_qlt_level']))
                return false;

            imagedestroy($save_init_image_data);

        } else {
            // Save the current
            if (!func_magnifier_write_tile($init_image['data'], $folder2save.'init_image.jpg', $magnifier_sets['jpg_qlt_level']))
                return false;
        }
    }

    $data_lvls = func_magnifier_get_levels_gradation($init_image['image_x'], $init_image['image_y'], $init_image['max']);

    $xml_content = array();
    foreach($data_lvls as $key => $lvl) {

        $final_x =  round($init_image['image_x'] * $lvl['rate_zoom']);
        $final_y =  round($init_image['image_y'] * $lvl['rate_zoom']);

        $prev_lvl_x = floor($final_x / $lvl['relative_rate_zoom']);
        $prev_lvl_y = floor($final_y / $lvl['relative_rate_zoom']);

        imagecopyresampled($init_image['data'], $init_image['data'], 0, 0, 0, 0, $final_x, $final_y, $prev_lvl_x, $prev_lvl_y);

        if ($lvl['slice']) {
            // get tiles and save it

            $tile = imageCreatetruecolor($magnifier_sets['x_tile_size'], $magnifier_sets['y_tile_size']);
            $x_current_size = 0;
            $xi = 0;

            while ($x_current_size < $final_x) {
                $y_current_size = 0;
                $yi = 0;

                while ($y_current_size < $final_y) {

                    if ((($y_current_size + $magnifier_sets['y_tile_size']) > $final_y || ($x_current_size + $magnifier_sets['x_tile_size']) > $final_x)) {
                        // We need part of the tile
                        $x_boundary_tile = ( ($x_current_size + $magnifier_sets['x_tile_size'])>$final_x ? $final_x - $x_current_size : $magnifier_sets['x_tile_size']);
                        $y_boundary_tile = ( ($y_current_size + $magnifier_sets['y_tile_size'])>$final_y ? $final_y - $y_current_size : $magnifier_sets['y_tile_size']);

                        $boundary_tile = imageCreatetruecolor($x_boundary_tile,$y_boundary_tile);
                        imagecopyresampled($boundary_tile, $init_image['data'], 0, 0, $x_current_size, $y_current_size, $x_boundary_tile, $y_boundary_tile, $x_boundary_tile, $y_boundary_tile);
                        if (!func_magnifier_write_tile($boundary_tile, $folder2save.$key.'_'.$yi.'_'.$xi.'.'.$magnifier_sets['extension'], $magnifier_sets['jpg_qlt_tile']))
                            return false;
                        imagedestroy($boundary_tile);

                    } else {
                        // We need the whole tile
                        imagecopyresampled($tile, $init_image['data'], 0, 0, $x_current_size, $y_current_size, $magnifier_sets['x_tile_size'], $magnifier_sets['y_tile_size'], $magnifier_sets['x_tile_size'], $magnifier_sets['y_tile_size']);
                        if (!func_magnifier_write_tile($tile, $folder2save.$key.'_'.$yi.'_'.$xi.'.'.$magnifier_sets['extension'], $magnifier_sets['jpg_qlt_tile']))
                            return false;
                    }

                    $yi++;
                    $y_current_size = $y_current_size + $magnifier_sets['y_tile_size'];
                }

                $xi++;
                $x_current_size = $x_current_size + $magnifier_sets['x_tile_size'];
            }

            imagedestroy($tile);
        }

        if ($lvl['save_level']) {
            // save entire picture
            $level_data = imageCreatetruecolor($final_x, $final_y);
            imagecopyresampled($level_data, $init_image['data'], 0, 0, 0, 0, $final_x, $final_y, $final_x, $final_y);
            if (!func_magnifier_write_tile($level_data, $folder2save.'level_'.$key.'.'.$magnifier_sets['extension'], $magnifier_sets['jpg_qlt_level']))
                return false;
            imagedestroy($level_data);
        }

        $xi = ( empty($xi) ? 1 : $xi );
        $yi = ( empty($yi) ? 1 : $yi );
        $xml_content[$key] = array('num' => $key, 'rows' => $yi, 'cols' => $xi, 'zoom' => $lvl['zoom']);

    }

    if(!empty($xml_content)) {
        // Create XML description file

        $xml_data = "<image extension=\"".$magnifier_sets['extension']."\">\n";
        for ($i = 0; $i < count($xml_content); $i++) {
            $xml_data .= "    <level num=\"".$xml_content[$i]['num']."\" nRows=\"".func_magnifier_num2code($xml_content[$i]['rows'], 98453)."\" nColumns=\"".func_magnifier_num2code($xml_content[$i]['cols'], 87211)."\" zoom=\"".$xml_content[$i]['zoom']."\"/>\n";
        }
        $xml_data .= "</image>\n";

        if (!func_magnifier_write_xml($xml_data, $folder2save.'description.xml'))
            return false;
    }

    // Create thumbnail
    if ($final_x/$final_y > $magnifier_sets['x_thmb']/$magnifier_sets['y_thmb']) {
        $x_visible = $magnifier_sets['x_thmb']*$final_y/$magnifier_sets['y_thmb'];
        $x_start = floor(($final_x - $x_visible)/2);
        $x_end = $x_visible;
        $y_start = 0;
        $y_end = $final_y;

    } else {
        $y_visible =  $magnifier_sets['y_thmb']*$final_x/$magnifier_sets['x_thmb'];
        $y_start = floor(($final_y - $y_visible)/2);
        $y_end = $y_visible;
        $x_start = 0;
        $x_end = $final_x;
    }
    $thmb_data = imageCreatetruecolor($magnifier_sets['x_thmb'], $magnifier_sets['y_thmb']);
    imagecopyresampled($thmb_data, $init_image['data'], 0, 0, $x_start, $y_start, $magnifier_sets['x_thmb'], $magnifier_sets['y_thmb'], $x_end, $y_end);
    if (!func_magnifier_write_tile($thmb_data, $folder2save.'thumbnail.'.$magnifier_sets['extension'], $magnifier_sets['jpg_qlt_thmb']))
        return false;

    imagedestroy($thmb_data);
    imagedestroy($init_image['data']);

    return array('x' => $init_image['image_x'], 'y' => $init_image['image_y'] );
}

/**
 * Convert number to code
 */
function func_magnifier_num2code($code, $add_factor)
{
    static $rings = array(
        '3389346389738472262599114482527855889427381238444584595943195771',
        '6543431752478212416915624651999643875859187898938638181555644598',
        '2491485317542518145612579559617757785326169219124574632676537313',
        '4556148722172396539996566819578923627598695825577466123613669461',
        '1485625192626198631849785736115253725224476985848932991473184598'
    );

    for ($i = 0; $i < strlen((string)$add_factor); $i++) {
        $code = $code*substr((string)$add_factor, $i, 1);
    }
    $code = $code*substr($rings[0], substr($add_factor, 0, 1), 1);
    $numbers = array();

    // Get 'crc'
    $crc = 0;
    $str = str_replace('.', '', (string)$code);
    for( $i = 0; $i < strlen($str); $i++) {
        $crc += ord(substr($str, $i, 1));
        $numbers[] = substr($str, $i, 1);
    }
    $crc = dechex(round($crc / substr($add_factor, -1)));
    $crc .= dechex(strlen($crc));

    // Multiply rings and number in cycle
    $x = 0;
    foreach($rings as $i => $ring) {
        $step = substr($add_factor, $i, 1);

        foreach ($numbers as $idx => $n) {
            if ($x >= strlen($ring)) {
                $x -= strlen($ring)-1;
            }
            $numbers[$idx] = $n*substr($ring, $x, 1);
            $x += $step;
        }
    }

    // Convert number to HEX
    $result = '';
    foreach ($numbers as $n) {
        $s = dechex($n);
        $result .= dechex(strlen($s)).$s;
    }

    return $result.$crc;
}

/**
 * Check image structure
 */
function func_magnifier_image_check($id)
{
    global $sql_tbl, $magnifier_sets;

    // Check record in DB
    $data = func_query_first("SELECT * FROM $sql_tbl[images_Z] WHERE imageid = '$id'");
    if (empty($data))
        return false;

    // Check directory structure
    x_load('image');
    $dir = func_image_dir('Z');
    if (!file_exists($dir) || !is_dir($dir))
        return false;

    $dir .= '/'.$data['id'];

    if (!file_exists($dir) || !is_dir($dir))
        return false;

    $dir .= '/'.$id;

    if (!file_exists($dir) || !is_dir($dir))
        return false;

    // Get XML structure
    $fp = @fopen($dir.'/description.xml', 'r');
    if (!$fp)
        return false;

    $xml = fread($fp, func_filesize($dir.'/description.xml'));
    fclose($fp);

    // Check XML structure
    x_load('xml');

    $parse_errors = false;
    $options = array(
        'XML_OPTION_CASE_FOLDING' => 2,
        'XML_OPTION_TARGET_ENCODING' => 'ISO-8859-1'
    );
    $parsed = func_xml_parse($xml, $parse_errors, $options);
    if (!$parsed)
        return false;
;
    $levels = func_array_path($parsed, 'IMAGE/LEVEL');
    $ext = func_array_path($parsed, 'IMAGE');
    $ext = (empty($ext['@']['EXTENSION']) ? "jpg" : $ext['@']['EXTENSION']);

    // Check common files
    $files = array('description.xml', 'level_0.'.$ext, 'thumbnail.'.$ext);
    foreach ($files as $f) {
        if (!file_exists($dir.'/'.$f) || !is_file($dir.'/'.$f) || !is_readable($dir.'/'.$f))
            return false;
    }

    if (empty($levels))
        return false;

    if (isset($levels['#']))
        $levels = array($levels);

    for ($i = 0; $i < count($levels); $i++) {
        if (
            $levels[$i]['@']['NUM'] != $i ||
            !isset($levels[$i]['@']['ZOOM']) ||
            !is_numeric($levels[$i]['@']['ZOOM']) ||
            empty($levels[$i]['@']['NROWS']) ||
            empty($levels[$i]['@']['NCOLUMNS'])
        )
            return false;

        if ($i == 0)
            continue;

        // Check tile [0,0]
        if (!file_exists($dir.'/'.$i."_0_0.".$ext) || !is_readable($dir.'/'.$i."_0_0.".$ext))
            return false;

    }

    return true;
}

function func_magnifier_check_md5_uniqueness($md5, $pids = NULL, $check_count = false)
{
    global $sql_tbl;

    $pids_condition = '';
    if (!empty($pids) && is_array($pids)) {
        $pids_condition = " AND id IN ('".join("', '", $pids)."') ";
    }

    $images = func_query_hash("SELECT id, imageid FROM $sql_tbl[images_Z] WHERE md5 = '".addslashes($md5)."'".$pids_condition." ORDER BY id", "id", true, true);

    if (empty($images) || !is_array($images)) {

        return false;
    }

    if ($check_count && !empty($pids) && is_array($pids) && array_keys($images) != $pids) {

        return false;
    }

    $unique_image_per_product = true;
    foreach ($images as $pid => $imageids) {
        if (count($imageids) != 1) {
            $unique_image_per_product = false;

            break;
        }
    }

    return $unique_image_per_product;
}

function func_magnifier_reslice_image($imageid, $productid)
{
    global $xcart_dir, $magnifier_sets;

    $folder2save = func_image_dir('Z').XC_DS.$productid.XC_DS.$imageid.XC_DS;
    $file_path = $folder2save.'init_image.jpg';

    if (!file_exists($file_path)) {

        // The image does not have initial/source image.
        return array('E', func_get_langvar_by_name('msg_adm_no_init_image'));
    }

    if (!func_check_dir_permissions($folder2save)) {

        // Can not write to the image storage directory
        return array('E', func_get_langvar_by_name('msg_adm_incorrect_store_n_files_perms', array('path' => $folder2save)));
    }

    $old_magnifier_save_init_image = $magnifier_sets['save_init_image'];
    $magnifier_sets['save_init_image'] = 0;
    $image_size = func_magnifier_get_levels($file_path, $folder2save);
    $magnifier_sets['save_init_image'] = $old_magnifier_save_init_image;

    if (!$image_size) {

        // Failed to reslice image
        return array('E', func_get_langvar_by_name('msg_adm_incorrect_format_4zoomer'));
    }

    $query_data = array(
        'image_path' => addslashes('./images/Z/'.$productid.'/'.$imageid.'/thumbnail.'.$magnifier_sets['extension']),
        'md5' => func_md5_file($xcart_dir.'/images/Z/'.$productid.'/'.$imageid.'/init_image.jpg'),
        'image_size' => func_filesize($xcart_dir.'/images/Z/'.$productid.'/'.$imageid.'/thumbnail.'.$magnifier_sets['extension']),
        'filename' => 'thumbnail.'.$magnifier_sets['extension']
    );

    func_array2update('images_Z', $query_data, "imageid = '$imageid'");

    // Image was successfully resliced
    return array('I', '');
}

function func_magnifier_reslice($imageid)
{
    global $sql_tbl, $magnifier_sets, $xcart_dir;

    $imageid = intval($imageid);
    $zimage = func_query_first("SELECT id, image_path FROM $sql_tbl[images_Z] WHERE imageid='$imageid'");

    if (empty($zimage['id']))
        return false;

    $folder2save = func_image_dir('Z').XC_DS.$zimage['id'].XC_DS.$imageid.XC_DS;
    if (!file_exists($folder2save))
        func_mkdir($folder2save);

    $old_magnifier_save_init_image = $magnifier_sets['save_init_image'];
    $magnifier_sets['save_init_image'] = 1;
    $image_size = func_magnifier_get_levels($xcart_dir.XC_DS.$zimage['image_path'], $folder2save);
    $magnifier_sets['save_init_image'] = $old_magnifier_save_init_image;

    if (!$image_size)
        return false;

    $query_data = array(
        'image_path' => addslashes('./images/Z/'.$zimage['id']."/".$imageid."/thumbnail.".$magnifier_sets["extension"]),
        'md5' => func_md5_file($xcart_dir.'/images/Z/'.$zimage['id']."/".$imageid."/init_image.jpg"),
        'image_size' => func_filesize($xcart_dir.'/images/Z/'.$zimage['id']."/".$imageid."/thumbnail.".$magnifier_sets["extension"])
    );

    func_array2update('images_Z', $query_data, "imageid = '$imageid'");

    return true;
}

?>
