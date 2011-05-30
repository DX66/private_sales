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
 * Functions used in operations with images
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.image.php,v 1.85.2.3 2011/01/10 13:11:51 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

define('X_IMAGE_CACHE_NAME_PATTERN', '[a-z\d_]{4,64}');

define('X_IMAGE_CACHE_DISPLAY_HEADER', 1);
define('X_IMAGE_CACHE_DISPLAY_TICK', 2);
define('X_IMAGE_CACHE_EXTERNAL_RES_CHECK', 4);

x_load('gd');

/**
 * Get images directory path
 */
function func_get_images_root()
{
    global $xcart_dir;
    return $xcart_dir . '/images';
}

/**
 * Construct path to directory of images of type $type
 */
function func_image_dir($type)
{
    $dir = func_get_images_root() . '/' . $type;
    if (!is_dir($dir) && file_exists($dir))
        unlink($dir);

    if (!file_exists($dir))
        func_mkdir($dir);

    return $dir;
}

/**
 * Get image file extension using mime type of image
 */
function func_get_image_ext($mime_type)
{
    static $corrected = array (
        'application/x-shockwave-flash' => 'swf',
        'image/pjpeg' => 'jpg',
        'image/jpeg' => 'jpg'
    );

    if (!is_string($mime_type) || zerolen($mime_type))
        return 'img';

    if (isset($corrected[$mime_type]))
        return $corrected[$mime_type];

    if (preg_match("/^image\/(.+)$/Ss", $mime_type, $m))
        return $m[1];

    return 'img'; // unknown generic file extension
}

/**
 * Check uniqueness of image filename
 */
function func_image_filename_is_unique($file, $type, $imageid=false)
{
    global $config, $sql_tbl, $xcart_dir;

    if (empty($config['available_images'][$type]) || empty($config['setup_images'][$type])) {
        // ERROR: unknown or not aavailable image type
        return false;
    }

    $_table = $sql_tbl['images_' . $type];
    $_where = "filename='" . addslashes($file) . "'";
    if (!empty($imageid)) {
        // ignore ourself
        $_where .= " AND imageid <> '" . addslashes($imageid) . "'";
    }

    if (func_query_first_cell("SELECT COUNT(*) FROM " . $_table . " WHERE " . $_where) > 0)
        return false;

    return !file_exists(func_image_dir($type) . '/' . $file);
}

/**
 * Generate unique filename for image in directory defined for $type
 * and corresponding database table
 */
function func_image_gen_unique_filename($file_name, $type, $mime_type="image/jpg", $id=false, $imageid=false)
{
    static $max_added_idx = 99999;
    static $last_max_idx = array();

    if (!zerolen($file_name) && !func_is_allowed_file($file_name))
        $file_name = '';

    if (zerolen($file_name)) {
        // File name is empty
        $file_name = strtolower($type);
        if (!zerolen((string)$id))
            $file_name .= "-" . $id;

        if (!zerolen((string)$imageid))
            $file_name .= "-" . $imageid;

        $file_ext = func_get_image_ext($mime_type);

    } elseif (preg_match("/^(.+)\.([^\.]+)$/Ss", $file_name, $match)) {
        // Detect file extension
        $file_name = $match[1];
        $file_ext = $match[2];
    }

    $is_unique = func_image_filename_is_unique($file_name . '.' . $file_ext, $type, $imageid);

    if ($is_unique)
        return $file_name . '.' . $file_ext;

    // Generate unique name
    $idx = isset($last_max_idx[$type][$file_name]) ? $last_max_idx[$type][$file_name] : func_get_next_unique_id($file_name, $type);
    $name_tmp = $file_name;
    $dest_dir = func_image_dir($type);

    do {
        $file_name = sprintf("%s-%02d", $name_tmp, $idx++);
        $is_unique = func_image_filename_is_unique($file_name . '.' . $file_ext, $type, $imageid);
    } while (!$is_unique && $idx < $max_added_idx);

    if (!$is_unique) {
        // ERROR: cannot generate unique name
        return false;
    }

    if ($idx > 2) {

        // Save last suffix
        if (!isset($last_max_idx[$type]))
            $last_max_idx[$type] = array();
        $last_max_idx[$type][$name_tmp] = $idx-1;
    }

    return $file_name . '.' . $file_ext;
}

/**
 * Get last unique id for image file name
 */
function func_get_next_unique_id($file, $type)
{
    global $config, $sql_tbl, $xcart_dir;

    $max = 1;
    if (empty($config['available_images'][$type]) || empty($config['setup_images'][$type])) {
        // ERROR: unknown or not aavailable image type
        return $max;
    }

    $res = db_query("SELECT filename FROM ".$sql_tbl['images_'.$type]." WHERE SUBSTRING(filename, 1, " . (strlen($file) + 1) . ") = '" . addslashes($file) . "-'");
    if ($res) {

        while ($f = db_fetch_array($res)) {
            $f = substr(array_pop($f), strlen($file)+1);
            if (preg_match("/^(\d+)/Ss", $f, $match) && $max < intval($match[1]))
                $max = intval($match[1]);

        }
        db_free_result($res);
        if ($max > 1)
            $max++;

        return $max;
    }

    return $max;
}

/**
 * Move images of $type to the new location (generic function)
 */
function func_move_images($type, $config_data)
{
    global $sql_tbl, $config, $images_step, $str_out, $xcart_dir;

    if (zerolen($type, $config_data['location'])) {
        return false;
    }

    $image_table = $sql_tbl['images_' . $type];
    $count = func_query_first_cell("SELECT COUNT(*) FROM ".$image_table);
    if (!$count)
        return true; // success

    // Transfer images by $images_step per pass

    $move_functions = array (
        'FS' => 'func_move_images_to_fs',
        'DB' => 'func_move_images_to_db'
    );

    $move_func = $move_functions[$config_data['location']];

    $error = false;
    // $rec_no used for displaying dots
    for ($rec_no = 0, $pos = 0; $pos < $count && !$error; $pos += $images_step) {
        $sd = db_query("SELECT * FROM ".$image_table." LIMIT $pos,$images_step");

        $error = $error || ($sd === false);
        if (!$sd || !function_exists($move_func))
            continue;

        $error = $error || !$move_func($sd, $type, $rec_no, $config_data);

        db_free_result($sd);
    }

    return !$error;
}

/**
 * Move images of $type to the filesystem
 * Please use func_move_images() instead.
 */
function func_move_images_to_fs($db_image_set, $type, &$rec_no, $config_data)
{
    global $sql_tbl, $str_out, $xcart_dir;

    $dest_dir = func_image_dir($type);

    // Storing of image_path field for images stored in filesystem
    // is necessary for compatibility with data caching
    $update_query = "UPDATE ".$sql_tbl['images_'.$type]." SET image_path=?, filename=?, image='', md5=?, date=?, image_size=?, image_x=?, image_y=?, image_type=? WHERE imageid=?";

    $error = false;
    while ($v = db_fetch_array($db_image_set)) {
        if (zerolen($v['image']) && (!is_url($v['image_path']) || $config_data['save_url'] != 'Y')) {
            // 1. URL images are NOT moving (if 'save_url' option is disabled)
            // 2. for empty 'image' assume what image in filesystem already
            continue;
        }

        if (!empty($v['image_path']))
            $v['filename'] = basename($v['image_path']);

        $str_out .= "image #".$v['imageid']." (owner: ".$v['id'].")";

        $moved = false;
        $reason = '';
        if (is_url($v['image_path']) && $config_data['save_url'] == 'Y')
            $v['file_path'] = $v['image_path'];

        $file = func_store_image_fs($v, $type);

        if ($file === false) {
            $reason = 'cannot create file for the image';
            $error = true;
        } else {
            $new_data = func_get_image_size($file);
            if (!$new_data) {
                $reason = 'cannot create file for the image';
                $error = true;
            } else {
                $image_path = func_relative_path($file);
                $str_out .= " (file: ".$image_path.") - ";

                $file_name = basename($file);
                $md5 = func_md5_file($file);

                if (empty($v['date']))
                    $v['date'] = XC_TIME;

                $update_params = array(
                    $image_path,
                    $file_name,
                    $md5,
                    $v['date']
                );
                $update_params = func_array_merge($update_params, $new_data);
                $update_params[] = $v['imageid'];

                $moved = db_exec($update_query, $update_params);

                $error = $error || !$moved;

                if (!$moved) {
                    $reason = "cannot update database";
                    unlink(func_realpath($file));
                }
            }
        }

        $str_out .= ($moved ? 'OK' : "<b>Failed ($reason)</b>")."\n";

        func_echo_dot($rec_no, 1, 100);
    }
    return !$error;
}

/**
 * Move images of $type to the database.
 * Please use func_move_images() instead.
 */
function func_move_images_to_db($db_image_set, $type, &$rec_no, $config_data)
{
    global $config, $sql_tbl, $str_out;

    $update_query = "UPDATE " . $sql_tbl['images_' . $type] . " SET image_path='', image=?, md5=?, date=?, image_size=?, image_x=?, image_y=?, image_type=? WHERE imageid=?";

    $src_dir = func_image_dir($type).XC_DS;

    $error = false;

    while (!$error && ($v = db_fetch_array($db_image_set))) {
        if (!zerolen($v['image']) || (is_url($v['image_path']) && $config_data['save_url'] != 'Y')) {
            // image in database already ?
            continue;
        }

        if (!empty($v['image_path']) && is_url($v['image_path'])) {
            $file = $fn = $v['image_path'];

        } elseif (!empty($v['image_path'])) {
            $file = $v['image_path'];
            $fn = func_relative_path($file);

        } else {
            $file = $src_dir.$v['filename'];
            $fn = func_relative_path($file);
        }

        $str_out .= $fn." (ID: ".$v['id'].") - ";

        $moved = false;
        $reason = '';

        $image = func_file_get($file, true);
        if ($image === false) {
            $reason = 'cannot open';
        }
        elseif (zerolen($image)) {
            $reason = 'empty image';
        }
        else {
            if (empty($v['date']))
                $v['date'] = XC_TIME;

            $new_data = func_get_image_size($image, true);
            if (!$new_data) {
                $reason = 'cannot get image size';
                $error = true;
            } else {
                $update_params = array(
                    $image,
                    md5($image),
                    $v['date']
                );
                $update_params = func_array_merge($update_params, $new_data);
                $update_params[] = $v['imageid'];

                $moved = db_exec($update_query, $update_params);

                $error = $error || !$moved;
                if (!$moved) {
                    $reason = "cannot update database";
                }
            }
        }

        // check if image is used in other places
        $is_found = false;
        foreach ($config['available_images'] as $k => $i) {
            $is_found = func_query_first_cell("SELECT COUNT(*) FROM ".$sql_tbl['images_'.$k]." WHERE image_path='".addslashes($file)."'".($k == $v['image_type'] ? " AND imageid != '$v[imageid]'" : '')) > 0;
            if ($is_found) break;
        }

        if (!$is_found && $moved && !is_url($file)) {
            // finish transfer of image
            @unlink(func_realpath($file));
        }

        $str_out .= ($moved ? 'OK' : "<b>Failed ($reason)</b>")."\n";

        func_echo_dot($rec_no, 1, 100);
    }

    return !$error;
}

/**
 * Check image permissions
 */
function func_check_image_storage_perms($file_upload_data, $type = 'T', $get_message = true)
{
    return !func_check_image_posted($file_upload_data, $type) || func_check_image_perms($type, $get_message);
}

/**
 * Check image type permissions
 */
function func_check_image_perms($type, $get_message = true)
{
    global $config, $xcart_dir;

    if (!isset($config['setup_images'][$type]) || $config['setup_images'][$type]['location'] == 'DB')
        return true;

    $path = func_image_dir($type);
    $arr = explode('/', substr($path, strlen($xcart_dir) + 1));
    $suffix = $xcart_dir;

    foreach ($arr as $p) {
        $suffix .= XC_DS . $p;

        $return = array();
        if (!is_writable($suffix))
            $return[] = 'w';

        if (!is_readable($suffix))
            $return[] = 'r';

        if (count($return) > 0) {
            $return['path'] = $suffix;
            if ($get_message) {
                if (in_array('r', $return) && in_array('w', $return)) {
                    $return['label'] = 'msg_err_image_cannot_saved_both_perms';

                } elseif (in_array('r', $return)) {
                    $return['label'] = 'msg_err_image_cannot_saved_read_perms';

                } else {
                    $return['label'] = 'msg_err_image_cannot_saved_write_perms';
                }
                $return['content'] = func_get_langvar_by_name($return['label'], array('path' =>  $return['path']));
            }

            return $return;
        }
    }

    return true;
}

/**
 * Checking that posted image is exist
 */
function func_check_image_posted($file_upload_data, $type = 'T')
{
    global $config;

    $return = false;
    $config_data = $config['setup_images'][$type];

    $image_posted = $file_upload_data[$type];

    if (!func_allow_file($image_posted['file_path'], true))
        return false;

    if ($image_posted['source'] == "U") {
        if (func_url_is_exists($image_posted['file_path']))
            $return = true;

    } else {
        $return = file_exists($image_posted['file_path']);
    }

    if ($return) {
        $return = ($image_posted['file_size'] <= $config_data['size_limit'] || $config_data['size_limit'] == '0');
    }

    return $return;
}

/**
 * Prepare posted image for saving
 */
function func_prepare_image($file_upload_data, $type = 'T', $id = 0)
{
    global $config, $xcart_dir, $sql_tbl;

    if ((empty($file_upload_data[$type]['file_path']) && empty($file_upload_data[$type]['image'])) || empty($config['setup_images'][$type]) || !in_array($file_upload_data[$type]['source'], array("U","S","L"))) {
        // ERROR: incorrect value
        return false;
    }

    $image_data = $file_upload_data[$type];

    $config_data = $config['setup_images'][$type];

    $file_path = $image_data['file_path'];
    if (!is_url($file_path)) {
        $file_path = func_realpath($file_path);
    } else {
        $host = @parse_url($file_path);
        if (is_array($host) && $host['scheme'] === 'https') {
            x_load('http');
            list($header,$content) = func_https_request('GET',$file_path);
            if (!$header)
                return false;
            preg_match("/CONTENT-LENGTH: (\d*)/",strtoupper($header),$out);
            if (intval($out[1]) > 0)
                $image = $content;
            else
                return false;
        }
    }

    if ($image == false) {
        $image = func_file_get($file_path, true);
    }

    if ($image == false) {
        $image = $image_data['image'];
    }

    if ($image === false) {
        return false;
    }

    $prepared = array(
        'image_size' => strlen($image),
        'md5' => md5($image),
        'filename' => $image_data['filename'],
        'image_type' => $image_data['image_type'],
        'image_x' => $image_data['image_x'],
        'image_y' => $image_data['image_y'],
    );

    if ($config_data['location'] == "FS") {
        $prepared['image_path'] = '';

        if (!is_url($file_path) || $config_data['save_url'] == 'Y') {

            $dest_file = func_image_dir($type);
            if (!zerolen($prepared['filename'])) {
                $dest_file .= '/'.$prepared['filename'];
            }

            $prepared['image_path'] = func_store_image_fs($image_data, $type);

            if (zerolen($prepared['image_path']))
                return false;

            $prepared['filename'] = basename($prepared['image_path']);

            $path = func_relative_path($prepared['image_path'], $xcart_dir);
            if ($path !== false) {
                $prepared['image_path'] = $path;
            }

        } else {
            $prepared['image_path'] = $file_path;

        }

    } else {

        if (is_url($file_path) && $config_data['save_url'] != 'Y') {
            $prepared['image_path'] = $file_path;
        } else {
            $prepared['image'] = $image;
        }
        unset($image);
        if ($image_data['source'] == "L") {
            @unlink(func_realpath($file_path));
        }
    }

    return $prepared;
}

/**
 * Save uploaded/changed image
 */
function func_save_image(&$file_upload_data, $type, $id, $added_data = array(), $_imageid = NULL)
{
    global $sql_tbl, $config, $skip_image;

    $image_data = func_prepare_image($file_upload_data, $type, $id);
    if (empty($image_data) || empty($id))
        return false;

    if ($skip_image[$type] == 'Y') {
        if (!empty($file_upload_data[$type]['is_copied'])) {
            // Should delete image file
            @unlink($file_upload_data[$type]['file_path']);
        }
        unset($file_upload_data[$type]);
        return false;
    }

    $image_data['id'] = $id;
    $image_data['date'] = XC_TIME;
    if (!empty($added_data)) {
        $image_data = func_array_merge($image_data, $added_data);
    }

    $image_data = func_addslashes($image_data);
    unset($file_upload_data[$type]);

    $_table = $sql_tbl['images_' . $type];

    if ($config['available_images'][$type] == 'U') {
        if (!empty($_imageid)) {
            $_old_id = func_query_first_cell("SELECT id FROM " . $_table . " WHERE imageid = '$_imageid'");
            if (empty($_old_id) || $_old_id == $id)
                $image_data['imageid'] = $_imageid;
        }

        if (empty($image_data['imageid']))
            $image_data['imageid'] = func_query_first_cell("SELECT imageid FROM " . $_table . " WHERE id = '$id'");

        if (!empty($image_data['imageid'])) {
            func_delete_image($id, $type);
        }
    }

    $res = func_array2insert('images_' . $type, $image_data);
    if ($res) {
        func_image_cache_build($type, $res);
    }

    return $res;
}

/**
 * Store image in FS
 * Return: path to the file or FALSE
 */
function func_store_image_fs($image_data, $type)
{
    $dest_dir = func_image_dir($type);

    if (isset($image_data['file_path'])) {
        // this is uploaded image
        // add some missing fields

        if (!isset($image_data['id']))
            $image_data['id'] = false;
        $image_data['imageid'] = false;
        $image_data['image'] = func_file_get($image_data['file_path'],true);
    }

    // unique file location
    $file_name = func_image_gen_unique_filename(
        $image_data['filename'], $type, $image_data['image_type'],
        $image_data['id'], $image_data['imageid']);

    if ($file_name === false) {
        // ERROR: cannot continue
        return false;
    }

    $file = $dest_dir.'/'.$file_name;

    $fd = func_fopen($file, 'wb', true);
    if ($fd === false) {
        // ERROR: cannot continue
        return false;
    }

    fwrite($fd, $image_data['image']);
    fclose($fd);
    func_chmod_file($file);

    if (!empty($image_data['is_copied'])) {
        // should present only in structure of uploaded image
        unlink(func_realpath($image_data['file_path']));
    }

    return $file;
}

function func_echo_dot(&$rec_no, $threshold_dot, $threshold_newline)
{
    $rec_no ++;
    if ($threshold_dot==1 || ($rec_no % $threshold_dot) == 0) {
        echo '.';
        flush();
    }

    if ($threshold_newline==1 || ($rec_no % $threshold_newline) == 0) {
        echo "<br />\n";
        flush();
    }
}

/**
 * Get image properties
 */
function func_image_properties($type, $id)
{
    global $config, $sql_tbl;

    if (empty($config['available_images'][$type]) || empty($config['setup_images'][$type]))
        return false;

    return func_query_first("SELECT image_x, image_y, image_type, image_size FROM ".$sql_tbl['images_'.$type]." WHERE id = '$id'");
}

/**
 * Delete single image
 */
function func_delete_image($id, $type = 'T', $is_unique = false)
{
    global $config, $sql_tbl, $xcart_dir;

    $where = ($is_unique ? 'imageid' : 'id');
    if (is_array($id)) {
        $where .= " IN ('".implode("','", $id)."')";
    }
    else {
        $where .= " = '$id'";
    }

    return func_delete_images($type, $where);
}

/**
 * Delete group of images.
 * Advanced version of func_delete_image()
 */
function func_delete_images($type = 'T', $where = '')
{
    global $config, $sql_tbl, $xcart_dir, $active_modules;

    if (!isset($config['available_images'][$type]))
        return false;

    if (!empty($where))
        $where = " WHERE ".$where;

    $_table = $sql_tbl['images_'.$type];

    if (func_query_first_cell("SELECT COUNT(*) FROM " . $_table . $where) == 0)
        return false;

    $res = db_query("SELECT imageid, image_path, filename, (image IS NOT NULL AND LENGTH(image) > '0') AS in_db FROM " . $_table . $where);
    if ($res) {
        x_load('image');
        $img_dir = func_image_dir($type) . '/';
        while ($v = db_fetch_array($res)) {
            func_image_cache_remove($type, false, $v['imageid']);

            if ((!zerolen($v['image_path']) && is_url($v['image_path'])) || ($v['in_db'] && zerolen($v['image_path']))) {
                // Ignore URL and images in database
                continue;
            }

            $image_path = $v['image_path'];
            if (zerolen($image_path)) {
                $image_path = func_relative_path($img_dir.$v['filename']);
            }

            $is_found = false;
            // check other types
            foreach ($config['available_images'] as $k => $i) {
                $is_found = func_query_first_cell("SELECT COUNT(*) FROM ".$sql_tbl['images_'.$k]." WHERE image_path='".addslashes($image_path)."'".($k == $type ? " AND imageid != '$v[imageid]'" : '')) > 0;
                if ($is_found)
                    break;
            }

            $image_file = $xcart_dir . '/' . $image_path;
            if (!$is_found && file_exists($image_file))
                @unlink($image_file);
        }

        db_free_result($res);
    }

    db_query("DELETE FROM ".$_table.$where);

    return true;
}

/**
 * Determinate the maximum allowed image size
 */
function func_max_upload_image_size($config_data,$xcart_limit_only = false, $image_type = 'T')
{

    $config_limit = intval(($image_type == 'Z') ? 0 : $config_data['size_limit']);

    // If image will be saved into file system or database, then its size cannot be limited only by X-Cart
    if ($xcart_limit_only && $config_data['save_url'] != "Y")
        return $config_limit;

    $max_filesize = func_upload_max_filesize();
    $max_filesize = ($config_data['location'] == 'DB') ? func_convert_to_byte(func_get_max_upload_size()) : $max_filesize;

    return ($config_limit < $max_filesize && $config_limit > 0) ? $config_limit : $max_filesize;
}

/**
 * Function gets image from URL and sets $top_message variable if error has occuried.
 * It is used in func_generate_image function
 */
function func_get_url_image ($path)
{
    $url_image = func_url_get($path);
    if ($url_image)
        return func_temp_store($url_image);

    global $top_message;
    $top_message = array(
        'content' => func_get_langvar_by_name('lbl_auto_resize_could_not_get_file_for_resizing', NULL, false, true),
        'type' => "E"
    );
    return false;
}

/**
 * Generate 'from_type' image to 'to_type' image with new dimensions
 * (used when script generates thumbnail from product image)
 * if new dimensions are lesser than older ones and allow_not_resize is set then there will be no resizing
 */
function func_generate_image($id, $from_type = 'P', $to_type = 'T', $allow_not_resize = true, $temporary = false)
{
    global $config, $sql_tbl, $top_message, $auto_thumb_error;

    $from = 'images_' . $from_type;
    $to   = 'images_' . $to_type;

    $get_temporary = false;

    if ($temporary) {

        x_session_register('file_upload_data');

        global $file_upload_data;

        if (
            !empty($file_upload_data[$from_type]) 
            && !isset($file_upload_data[$from_type]['is_redirect'])
        ) {

            $image_filename = $file_upload_data[$from_type]['file_path'];
            $image_type     = $file_upload_data[$from_type]['image_type'];
            $image          = $file_upload_data[$from_type];

            if (is_url($image_filename)) {

                $image_filename = func_get_url_image($image_filename);

                if (!$image_filename) 
                    return false;
            }

            $get_temporary = true;
        }
    }

    if (!$get_temporary) {

        $image = func_query_first("SELECT image, image_type, image_x, image_y, image_path FROM ".$sql_tbl[$from]." WHERE id='$id'");

        if (is_url($image['image_path'])) {

            $image_filename = func_get_url_image($image['image_path']);

            if (!$image_filename)
                return false;

        } else {

            $image_filename = ($config['setup_images'][$from_type]['location'] == "DB")
                ? func_temp_store($image['image']) 
                : func_image_dir($from_type) . XC_DS . basename($image['image_path']);

        }

    }

    $image_type = func_get_image_ext($image['image_type']);

    if (empty($image_filename)) {

        $top_message = array(
            'content' => func_get_langvar_by_name('lbl_auto_resize_could_not_get_file_for_resizing', NULL, false, true),
            'type'    => "E",
        );

        return false;
    }

    $new_x = $config['images_dimensions'][$to_type]['width'];
    $new_y = $config['images_dimensions'][$to_type]['height'];

    if (
        $allow_not_resize 
        && $new_x >= $image['image_x'] 
        && $new_y >= $image['image_y']
    ) {
        $new_x = $image['image_x'];
        $new_y = $image['image_y'];
    }

    unset($image);

    $auto_thumb_error = '';
    $new_image = func_resize_image($image_filename, $new_x, $new_y, $image_type, true);

    if ($new_image === false) {

        $lbl_message = ($auto_thumb_error == '') ? 'lbl_auto_resize_could_not_resize_image' : $auto_thumb_error;

        $top_message = array(
            'content' => func_get_langvar_by_name($lbl_message, NULL, false, true),
            'type' => "E"
        );

        return false;
    }

    if ($temporary) {

        // Store Image into session

        if (!empty($file_upload_data[$to_type]['is_copied']))
            @unlink($file_upload_data[$to_type]['file_path']);

        $file_upload_data[$to_type] = array_merge(
            $new_image,
            array(
                'is_copied' => true,
                'filename'  => basename($new_image['file_path']).'.png',
                'source'    => 'L',
                'id'        => $id,
                'type'      => $to_type,
                'date'      => XC_TIME,
            )
        );

        x_session_save();

    } else {

        // Prepare data to store image

        if ($config['setup_images'][$from_type]['location'] == "DB")
            @unlink($image_filename);

        $temp_image = func_temp_read($new_image['file_path'], true);
        $new_image['md5'] = md5($temp_image);

        if ($config['setup_images'][$to_type]['location'] == "DB") {

            // Store image to DB

            $new_image['image']    = $temp_image;
            $new_image['date']     = XC_TIME;
            $new_image['filename'] = '';

            $new_image = func_addslashes($new_image);

        } else {

            // Store image to FS

            $new_image          = func_addslashes($new_image);
            $new_image['id']    = $id;
            $new_image['image'] = '';
            $new_image['date']  = XC_TIME;

            $image = $new_image;

            $image['is_copied'] = true;

            $new_image['image_path'] = func_relative_path(func_store_image_fs($image, $to_type));

            if (!$new_image['image_path']) {

                $top_message = array(
                    'content' => func_get_langvar_by_name("lbl_auto_resize_could_not_store_image_FS", NULL, false, true),
                    'type' => "E"
                );

                return false;

            }

            $new_image['filename'] = basename($new_image['image_path']);

            unset($image);

            $old_file = func_query_first_cell("SELECT image_path FROM ".$sql_tbl[$to]." WHERE id='$id'");

            if (!empty($old_file))
                @unlink($old_file);
        }

        unset($new_image['file_path']);

        $imageid = func_query_first_cell("SELECT imageid FROM ".$sql_tbl[$to]." WHERE id='$id'");

        if ($imageid) {

            func_array2update($to, $new_image, "imageid='$imageid'");

        } else {

            $new_image['id'] = $id;

            func_array2insert($to, $new_image);

        }

    }

    // $auto_thumb_error contains non-critical errors now (show it as a warning)

    $top_message = array(
        'content' => func_get_langvar_by_name(($auto_thumb_error == '') ? 'lbl_auto_resize_generate_success' : $auto_thumb_error, NULL, false, true),
        'type'    => ($auto_thumb_error == '') ? "I" : "W"
     );

    return true;
}

// Resize image using GDLib to PNG image file
/**
 * Function takes image file, resize it with new dimensions to PNG format,
 * returns file name which is stored in temporary directory and new dimensions of new image.
 * If 'proportional' is set then new dimensions will be recalculated to the proportional ones.
 * 'Image Type' must be set to the correct one. (image filename could be without proper image extension)
 */
function func_resize_image($image_filename, $new_x, $new_y, $image_type = 'jpeg', $proportional = true)
{
    global $auto_thumb_error, $xcart_dir;

    func_set_memory_limit('32M');

    if (!is_file($image_filename)) {
        $auto_thumb_error = 'lbl_auto_resize_no_file';

        return false;
    }

    if ($image_type == 'jpg')
        $image_type = 'jpeg';

    $func = 'func_imagecreatefrom' . $image_type;

    if (!function_exists($func)) {
        $auto_thumb_error = 'lbl_auto_resize_no_gd_function';

        return false;
    }

    list($image_x, $image_y) = @getimagesize($image_filename);

    if ($proportional) {
        list ($new_x, $new_y) = func_get_proper_dimensions($image_x, $image_y, $new_x, $new_y);
    }

    $new_x = intval(round($new_x));
    $new_y = intval(round($new_y));

    $image = $func($image_filename);

    if ($image === false) {
        $auto_thumb_error = 'lbl_auto_resize_could_not_create_image';

        return false;
    }

    $new_image = func_imagecreatetruecolor($new_x, $new_y);

    if ($new_image === false) {
        $auto_thumb_error = 'lbl_auto_resize_could_not_create_image';

        return false;
    }

    if (version_compare(PHP_VERSION, '4.3.2')) {

        func_imagealphablending($new_image, false);
        func_imagesavealpha($new_image, true);

    } else {

        func_imagecolortransparent($new_image, func_imagecolorallocate($new_image, 0, 0, 0));

    }

    $res = func_imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_x, $new_y, $image_x, $image_y);

    if ($res === false) {
        $auto_thumb_error = 'lbl_auto_resize_could_not_resize_image';

        return false;
    }

    $_image = false;

    if (
        file_exists($xcart_dir . '/include/lib/phpunsharpmask.php')
        && is_readable($xcart_dir . '/include/lib/phpunsharpmask.php')
    ) {
        require_once $xcart_dir . '/include/lib/phpunsharpmask.php';
    }

    if (function_exists('UnsharpMask')) {
        $_image = UnsharpMask($new_image);
    }

    if ($_image === false) {
        // work with image without resizing if time limit warning is detected
        $auto_thumb_error = 'lbl_auto_resize_time_limit';

        $_image = $new_image;
    }

    $new_file = func_temp_store('');

    if ($new_file === false) {
        $auto_thumb_error = 'lbl_auto_resize_could_not_create_file';

        return false;
    }

    $res = func_imagepng($_image, $new_file);

    if ($res === false) {
        @unlink($new_file);

        $auto_thumb_error = 'lbl_auto_resize_could_not_convert_image';

        return false;
    }

    func_imagedestroy($_image);
    func_imagedestroy($image);

    return array(
        'file_path'     => $new_file,
        'image_x'       => $new_x,
        'image_y'       => $new_y,
        'image_size'    => func_filesize($new_file),
        'image_type'    => 'image/png',
    );
}

/**
 * Get new dimensions with old proportions
 */
function func_get_proper_dimensions ($old_x, $old_y, $new_x, $new_y)
{

    if ($old_x <= 0 || $old_y <= 0 || ($new_x <= 0 && $new_y <= 0))
        return array($old_x, $old_y);

    if ($new_x <= 0) {

        $new_x = round($new_y / $old_y * $old_x, 0);

    } elseif ($new_y <= 0) {

        $new_y = round($new_x / $old_x * $old_y, 0);

    } else {

        $_kx = $new_x / $old_x;
        $_ky = $new_y / $old_y;

        if ($_kx < $_ky) {

            $new_y = round($_kx * $old_y, 0);

        } elseif ($_kx > $_ky) {

            $new_x = round($_ky * $old_x, 0);

        }

    }

    return array($new_x, $new_y);
}

/**
 * Crop image dimensions by limit
 */
function func_crop_dimensions ($x, $y, $limit_x, $limit_y)
{

    if ($x <= 0 || $y <= 0)
        return array($x, $y);

    if ($limit_x > 0 && $limit_y > 0) {

        $kx = $x > $limit_x ? $limit_x / $x : 1;
        $ky = $y > $limit_y ? $limit_y / $y : 1;
        $k = $kx < $ky ? $kx : $ky;

    } elseif ($limit_x > 0) {

        $k = $x > $limit_x ? $limit_x / $x : 1;

    } elseif ($limit_y > 0) {

        $k = $y > $limit_y ? $limit_y / $y : 1;

    } else {

        $k = 1;

    }

    return array(round($k * $x, 0), round($k * $y, 0));
}

/**
 * Assign image cache name
 */
function func_image_cache_assign($type, $name)
{
    global $image_caches, $config;

    if (!is_string($type) || !isset($config['available_images'][$type]) || !preg_match('/^' . X_IMAGE_CACHE_NAME_PATTERN . '$/Sis', $name))
        return false;

    if (!isset($image_caches[$type]))
        $image_caches[$type] = array();

    $image_caches[$type][] = $name;
    $image_caches[$type] = array_unique($image_caches[$type]);

    return true;
}

/**
 * Get image cache service data
 */
function func_image_cache_get_data($type, $name)
{
    global $image_caches, $config;
    static $results_cache = array();

    if (
        !is_string($type) 
        || !isset($config['available_images'][$type]) 
        || !is_string($name) 
        || !preg_match('/^' . X_IMAGE_CACHE_NAME_PATTERN . '$/Sis', $name) 
        || !isset($image_caches[$type]) 
        || !in_array($name, $image_caches[$type])
    ) {
        return false;
    }

    $data_key = $type . $name;

    if (isset($results_cache[$data_key]))
        return $results_cache[$data_key];

    $data = array(
        'is_valid' => 'func_ic_is_valid_' . $name,
        'get_size' => 'func_ic_get_size_' . $name,
        'is_crop'  => 'func_ic_is_crop_' . $name,
    );

    if (!function_exists($data['is_valid']) || !function_exists($data['get_size']) || !function_exists($data['is_crop']))
        return false;

    $data['path'] = func_image_dir($type) . '.cache.' . $name;

    $results_cache[$data_key] = $data;

    return $data;
}

/**
 * Get list of a image cache service data
 */
function func_image_cache_get_datas($type, $name = false)
{
    global $image_caches, $config;

    if (!is_string($type) || !isset($config['available_images'][$type]))
        return array();

    if (!$name) {

        $name = $image_caches[$type];

    } elseif (!is_array($name)) {

        $name = array($name);

    }

    if (!is_array($name) || count($name) == 0)
        return array();

    $tmp = array();

    foreach ($name as $k => $n) {

        $res = func_image_cache_get_data($type, $n);

        if ($res)
            $tmp[$n] = $res;
    }

    return $tmp;
}

/**
 * Build image cache
 */
function func_image_cache_build($type, $id = false, $name = false, $options = 0)
{
    global $config, $sql_tbl, $xcart_dir;

    if (!func_check_gd())
        return array(0, 0, 'nogd');

    $names = func_image_cache_get_datas($type, $name);

    foreach ($names as $k => $n) {
        if (!$n['is_valid']())
            unset($names[$k]);
    }

    if (count($names) == 0)
        return array(0, 0, 'nocache');

    $where = '';

    if ($id) {

        if (is_array($id)) {

            $where = ' WHERE imageid IN ("' . implode('","', $id) . '")';

        } elseif (is_numeric($id)) {

            $where = ' WHERE imageid = "' . $id . '"';

        }

    }

    $res = db_query('SELECT imageid, id, image, image_type, filename, image_x, image_y, image_path FROM ' . $sql_tbl['images_' . $type] . $where);

    if (!$res)
        return array(0, 0, 'noimages');

    if ($options & X_IMAGE_CACHE_DISPLAY_HEADER)
        func_display_service_header('lbl_image_cache_generate_start');

    $path = func_image_dir($type);

    // Check permissions
    $paths = array();

    foreach ($names as $n) {
        $paths[$n['path']] = true;
    }

    $err_paths = array();

    foreach (array_keys($paths) as $fpath) {

        if (!file_exists($fpath)) {

            $ppath = dirname($fpath);

            if (!is_writable($ppath) || !is_readable($ppath) || !func_is_executable($ppath, false))
                func_chmod($ppath, 'dir');

            func_mkdir($fpath);
        }

        if (!file_exists($fpath)) {

            $err_paths[] = $fpath;

        } elseif (!is_writable($fpath) || !is_readable($fpath) || !func_is_executable($fpath, false)) {

            func_chmod($fpath, 'dir');

            if (!is_writable($fpath) || !is_readable($fpath))
                $err_paths[] = $fpath;

        }

    }

    if (count($err_paths) > 0)
        return array(0, 0, 'noperms');

    $total = db_num_rows($res) * count($names);
    $cnt = 0;
    $i = 0;

    while (($row = db_fetch_array($res))) {

        if (
            !is_numeric($row['image_x']) || $row['image_x'] < 1 ||
            !is_numeric($row['image_y']) || $row['image_y'] < 1
        ) {
            continue;
        }

        if (!($options & X_IMAGE_CACHE_EXTERNAL_RES_CHECK) && !func_check_sysres()) {

            define('X_TIME_LIMIT_DETECTED', '1');

            return array($cnt, $total, 'ttl');

        }

        $is_temp = false;

        if ($config['setup_images'][$type]['location'] == 'DB' && $row['image']) {

            $image_filename = func_temp_store($row['image']);

            $is_temp = true;

        } elseif ($config['setup_images'][$type]['location'] != 'DB' && $row['image_path']) {

            if (file_exists($xcart_dir . XC_DS . $row['image_path'])) {

                $image_filename = $xcart_dir . XC_DS . $row['image_path'];

            } elseif ($row['image']) {

                $image_filename = func_temp_store($row['image']);

                $is_temp = true;

            }

        }

        if (!$image_filename)
            continue;

        $row['image_x'] = intval($row['image_x']);
        $row['image_y'] = intval($row['image_y']);

        foreach ($names as $n) {

            $is_crop = $n['is_crop']($row['image_x'], $row['image_y']);
            $size = $n['get_size']($row['image_x'], $row['image_y']);

            if (!$size)
                continue;

            $ext = func_get_image_ext($row['image_type']);

            if (!in_array($ext, array('gif', 'jpg', 'png')))
                continue;

            $fpath = $n['path'] . XC_DS . $row['imageid'] . '.' . $ext;

            if ((!$is_crop && $row['image_x'] != $size['width'] && $row['image_y'] != $size['height']) || $row['image_x'] > $size['width'] || $row['image_y'] > $size['height']) {

                $new_image = func_resize_image($image_filename, $size['width'], $size['height'], $ext, true);

                if ($new_image) {

                    if (rename($new_image['file_path'], $fpath)) {

                        func_chmod_file($fpath);

                        $cnt++;

                    } else {

                        @unlink($new_image['file_path']);

                    }

                }

            } else {

                $cnt++;

            }

            $i++;

            if ($options & X_IMAGE_CACHE_DISPLAY_TICK) {

                func_flush('. ');

            }

        }

        if ($is_temp) {

            @unlink($image_filename);

        }

    }

    db_free_result($res);

    return array($cnt, $total, true);
}

/**
 * Remove image cache
 */
function func_image_cache_remove($type = false, $name = false, $imageid = false)
{
    global $config;

    if (is_string($type) && !isset($config['available_images'][$type]))
        return false;

    if (is_string($name)) {
        if (!$type) {
            $name = false;

        } elseif (!func_image_cache_get_data($type, $name)) {
            return false;
        }
    }

    if ($imageid) {
        if (is_numeric($imageid)) {
            $imageid = array($imageid);

        } elseif (!is_array($imageid)) {
            return false;
        }
    }

    $path = dirname(func_image_dir($type ? $type : 'T'));

    $d = @opendir($path);

    if (!$d)
        return false;

    $reg = '/^' . ($type ? $type : '[A-Z]'). '\.cache\.' . ($name ? preg_quote($name, '/') : X_IMAGE_CACHE_NAME_PATTERN) . '$/Ss';

    $reg_file = false;

    if ($imageid)
        $reg_file = '/^(?:' . implode('|', $imageid). ')\..+$/Ss';

    while (($file = readdir($d))) {

        if ($file == '.' || $file == '..' || !preg_match($reg, $file) || !is_dir($path . XC_DS . $file))
            continue;

        if ($imageid) {

            $d2 = @opendir($path . XC_DS . $file);

            while (($f2 = readdir($d2))) {

                if ($f2 == '.' || $f2 == '..' || !preg_match($reg_file, $f2))
                    continue;

                @unlink($path . XC_DS . $file . XC_DS . $f2);

            }

            closedir($d2);

        } else {

            func_rm_dir($path . XC_DS . $file, true);

        }

    }

    closedir($d);

    return true;
}

/**
 * Translate image cache building result array to top message
 */
function func_image_cache_get_msg($res)
{
    if (!is_array($res) || count($res) != 3)
        return false;

    $lbl = false;
    $type = 'I';

    if ($res[2] === true) {

        $lbl = $res[0] == $res[1] ? 'lbl_image_cache_build_successfull' : 'lbl_image_cache_build_unsuccessfull';
        $type = $res[0] == $res[1] ? 'I' : 'W';

    } elseif ($res[2] == 'ttl') {

        $type = 'E';
        $lbl = 'lbl_image_cache_build_ttl_err';

    } elseif ($res[2] == 'nogd') {

        $type = 'E';
        $lbl = 'lbl_image_cache_build_gd_err';

    }

    if (!$lbl)
        return false;

    return array(
        'type'    => $type,
        'content' => func_get_langvar_by_name($lbl, array('count' => $res[0], 'total' => $res[1]), false, true)
    );
}

/**
 * Get image data from image cache repository
 */
function func_image_cache_get_image($type, $name, $imageid = false)
{
    global $sql_tbl, $xcart_dir, $current_location, $config;

    $data = func_image_cache_get_data($type, $name);

    if (!$data)
        return false;

    $path = $data['path'] . XC_DS;

    $where = '';

    $is_one = false;

    if (is_array($imageid)) {

        $where = ' WHERE imageid IN ("' . implode('","', $imageid) . '")';

    } elseif ($imageid) {

        $where = ' WHERE imageid = "' . $imageid . '"';

        $is_one = true;

    }

    $types = func_query_hash('SELECT imageid, id, image_type, image_x, image_y, image_path FROM ' . $sql_tbl['images_' . $type] . $where, 'imageid', false, false);

    $res = array();

    foreach ($types as $id => $t) {

        $ext = func_get_image_ext($t['image_type']);

        if (in_array($ext, array('gif', 'jpg', 'png'))) {

            $image_path = $path . $id . '.' . $ext;

            $size = file_exists($image_path) ? @getimagesize($image_path) : false;

            if ($size) {

                $res[$id] = array(
                    'url'    => $current_location . str_replace(XC_DS, "/", substr($image_path, strlen(preg_replace("/" . preg_quote(XC_DS, "/") . "$/S", '', $xcart_dir)))),
                    'width'  => $size[0],
                    'height' => $size[1],
                );
            }
        }

        if (!isset($res[$id])) {

            $size = $data['get_size']($t['image_x'], $t['image_y']);

            if (!$size) {
                $size['width'] = $t['image_x'];
                $size['height'] = $t['image_y'];
            }

            $is_crop = $data['is_crop']($t['image_x'], $t['image_y']);

            if (
                (
                    !$is_crop
                    && $t['image_x'] != $size['width']
                    && $t['image_y'] != $size['height']
                )
                || $t['image_x'] > $size['width']
                || $t['image_y'] > $size['height']
            ) {
                list(
                    $size['width'],
                    $size['height']
                ) = func_get_proper_dimensions(
                    $t['image_x'],
                    $t['image_y'],
                    $size['width'],
                    $size['height']
                );
            }

            $res[$id] = array(
                'url'    => func_get_image_url(($config['available_images'][$type] == 'U' ? $t['id'] : $id), $type, $t['image_path']),
                'width'  => $size['width'],
                'height' => $size['height'],
            );

        }

    }

    return $is_one ? array_pop($res) : $res;
}

function func_ic_is_valid_catthumbn()
{
    return true;
}

function func_ic_get_size_catthumbn($width, $height, $skin_name = '')
{
    global $alt_skin_info;
    static $result = array();

    if (empty($skin_name))
        $skin_name = $alt_skin_info['name'];

    if (isset($result[$skin_name])) {
        return $result[$skin_name];
    }

    // Get thumb_dims for schemes bt:88678 bt:93891
    $icon_sizes = array (
        "Light & Lucid (2-column)"                  => array('width' => '16', 'height' => '16'),
        "Light & Lucid (3-column)"                  => array('width' => '16', 'height' => '16'),
        "Artistic Tunes (Business)"                 => array('width' => '16', 'height' => '16'),
        "Artistic Tunes (Car Tires)"                => array('width' => '16', 'height' => '16'),
        "Artistic Tunes (Fragrances and Makeup)"    => array('width' => '16', 'height' => '16'),
        "Artistic Tunes (Water Colour)"             => array('width' => '16', 'height' => '16'),
        "Fashion Mosaic (Blue)"                     => array('width' => '16', 'height' => '16'),
        "Fashion Mosaic (Green)"                    => array('width' => '16', 'height' => '16'),
        "Fashion Mosaic (Grey)"                     => array('width' => '16', 'height' => '16'),
        "Fashion Mosaic (Pink)"                     => array('width' => '16', 'height' => '16'),
        "Vivid Dreams (Aquamarine)"                 => array('width' => '16', 'height' => '16'),
        "Vivid Dreams (Chromo)"                     => array('width' => '32', 'height' => '32'),
        "Vivid Dreams (Lotus)"                      => array('width' => '32', 'height' => '32'),
        "Vivid Dreams (Violet)"                     => array('width' => '16', 'height' => '16')
    );

    $result[$skin_name] = $icon_sizes[$skin_name];

    return $result[$skin_name];
}

function func_ic_is_crop_catthumbn()
{
    return false;
}

?>
