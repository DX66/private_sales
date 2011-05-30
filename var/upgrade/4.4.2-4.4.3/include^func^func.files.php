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
 * Files-related functions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.files.php,v 1.81.2.5 2011/01/10 13:11:51 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

/**
 * Get image size abstract function
 */
function func_get_image_size($filename, $is_image = false)
{
    static $img_types = array (
        '1' => 'image/gif',
        '2' => 'image/jpeg',
        '3' => 'image/png',
        '4' => 'application/x-shockwave-flash',
        '5' => 'image/psd',
        '6' => 'image/bmp',
        '13' => 'application/x-shockwave-flash',
    );

    if (empty($filename))
        return false;

    if (
        $is_image
        && is_url($filename)
    ) {

        $url_info = @parse_url($filename);

        list($width, $height, $type) = @getimagesize(str_replace(" ", "%20", $filename));

        if (
            ini_get('allow_url_fopen')
            && $url_info['scheme'] == 'http'
            && !empty($type)
        ) {

            $filename = str_replace(" ", "%20", $filename);

        } else {

            $filename = func_url_get($filename);

            if (!$filename)
                return false;

            $is_image = true;

        }

    }

    if ($is_image) {

        $size = strlen($filename);

        $filename = func_temp_store($filename);

        if (!$filename)
            return false;

    }

    if (empty($type))
        list($width, $height, $type) = @getimagesize($filename);

    if (!empty($img_types[$type])) {

        $type = $img_types[$type];

    } else {

        if ($is_image)
            @unlink($filename);

        return false;
    }

    if ($is_image) {

        @unlink($filename);

    } else {

        $size = func_filesize($filename);

    }

    return array(
        intval($size),
        $width,
        $height,
        $type,
    );
}

/**
 * Determine that $userfile is image file with non zero size
 */
function func_is_image_userfile($userfile, $userfile_size, $userfile_type)
{
    return ($userfile != 'none')
        && ($userfile != '')
        && ($userfile_size > 0)
        && (
            substr($userfile_type, 0, 6) == 'image/'
            || $userfile_type == 'application/x-shockwave-flash'
        );
}

/**
 * Recursively deletes directory with all its contents
 */
function func_rm_dir($directory, $keep_root = false, $rec_level = 0)
{
    $result = array(
        'is_large' => false,
    );

    if ($rec_level ++ > MAX_FUNC_NESTING_LEVEL) {

        $result['is_large'] = true;

        return $result;

    }

    $dir = @opendir($directory);

    if (!$dir) return false;

    while (($file = readdir($dir)) !== false) {

        if (($file == '.') || ($file == '..')) continue;

        $path = $directory . XC_DS . $file;

        if (is_file($path)) {

            @unlink($path);
            rewinddir($dir);

        } else {

            $temp = func_rm_dir($path, false, $rec_level);

            if ($temp['is_large']) $result['is_large'] = true;

        }

    }

    closedir($dir);

    if (!$keep_root) {
        @rmdir($directory);
    }

    return $result;
}

/**
 * This function compare file extension with disallowed extensions list
 */
function func_is_allowed_file($file)
{
    global $config;

    if (empty($config['Security']['disallowed_file_exts']))
        return true;

    $info = pathinfo($file);

    $info['extension'] = (!empty($info['extension']) ? $info['extension'] : '');

    // usage of 'S' preg flag do not give any additional speed
    return !preg_match(
        '!,\s*' . preg_quote($info['extension'], '!') . '\s*,!Ui',
        ',' . $config["Security"]["disallowed_file_exts"] . ','
    );
}

/**
 * Emulator for the is_executable function if it doesn't exists (f.e. under windows)
 */
function func_is_executable($file, $check_link = true)
{
    $count = 0;

    while (
        $check_link 
        && strlen($file) > 0
        && file_exists($file)
        && is_link($file)
        && $count < 2
    ) {
        $readlink = readlink($file);

        $file = preg_match('!^/!', $readlink)
            ? $readlink
            : dirname($file) . XC_DS . $readlink;

        $count ++;

    }

    if (function_exists('is_executable'))
        return is_executable($file);

    return is_readable($file);
}

/**
 * Executable lookup
 * Check prefered file first, then do search in PATH environment variable.
 * Will return false if no executable is found.
 */
function func_find_executable($filename, $prefered_file = false)
{
    global $xcart_dir;

    if (ini_get('open_basedir') != '' && !empty($prefered_file))
        return $prefered_file;

    $path_sep = X_DEF_OS_WINDOWS ? ';' : ':';

    if ($prefered_file) {

        if (!X_DEF_OS_WINDOWS && func_is_executable($prefered_file))
            return $prefered_file;

        if (X_DEF_OS_WINDOWS) {

            $info = pathinfo($prefered_file);

            if (empty($info['extension'])) $prefered_file .= '.exe';

            if (func_is_executable($prefered_file))
                return $prefered_file;

        }

    }

    $directories = explode($path_sep, getenv('PATH'));

    array_unshift($directories, $xcart_dir . XC_DS . 'payment', '/usr/bin', '/usr/local/bin');

    foreach ($directories as $dir){

        $file = $dir . XC_DS . $filename;

        if (!X_DEF_OS_WINDOWS && func_is_executable($file))
            return $file;

        if (X_DEF_OS_WINDOWS && func_is_executable($file . '.exe'))
            return $file . '.exe';

    }

    return false;
}

/**
 * Get thumbnail URL (if images are stored on the FS only)
 */
function func_get_image_url($id, $type = 'T', $image_path = false)
{
    global $config, $sql_tbl, $xcart_dir, $current_location;

    if (is_null($image_path))
        return func_get_default_image($type);

    if (!is_string($image_path) || zerolen($image_path)) {

        $field = ($config['available_images'][$type] == "U") ? "id" : "imageid";

        $info = func_query_first('SELECT filename, image_path, (image IS NOT NULL AND LENGTH(image) > \'0\') AS in_db FROM ' . $sql_tbl['images_' . $type] . ' WHERE ' . $field . ' = \'' . $id . '\'');

        if (!empty($info['in_db']))
            return $current_location . '/image.php?type=' . $type . '&id=' . $id;

        $image_path = isset($info['image_path']) ? $info['image_path'] : '';

        if (zerolen($image_path)) {

            $image_path = false;

            if (
                isset($info['filename'])
                && !zerolen($info['filename'])
            ) {

                x_load('image');

                $image_path = func_image_dir($type) . '/' . $info['filename'];

            }

        }

    }

    if ($image_path) {

        if (is_url($image_path)) {
            // image_path is an URL
            return $image_path;
        }

        $image_path = func_realpath($image_path);

        if (
            !strncmp($xcart_dir, $image_path, strlen($xcart_dir))
            && file_exists($image_path)
        ) {

            $bname = basename($image_path);
            $dir = substr($image_path, 0, strlen($bname)*-1);
            $image_path = $dir . rawurlencode($bname);

            // image_path is an locally placed image
            return $current_location . str_replace(XC_DS, '/', substr($image_path, strlen(preg_replace('/' . preg_quote(XC_DS, '/') . '$/S', '', $xcart_dir))));
        }
    }

    return func_get_default_image($type);
}

/**
 * Get images URL by pairs 'type' => 'id'
 */
function func_get_image_url_by_types($ids, $prefered_image_type = false)
{
    global $sql_tbl, $config;
    static $result = array();

    $key = md5(serialize($ids) . $prefered_image_type);
    if (isset($result[$key]))
        return $result[$key];

    $data = array('images' => array());
    $image_data = array();

    if (defined('X_MYSQL40_COMP_MODE')) {

        $query = array();
        foreach ($ids as $type => $id) {
            if (!isset($config['available_images'][$type])) {
                unset($ids[$type]);
                continue;
            }

            $query[] = 'SELECT \'' . $type . '\' as image_type, id, image_path, image_x, image_y FROM ' . $sql_tbl['images_' . $type] . ' WHERE id = \'' . $id . '\'';
        }

        $image_data = func_query_hash(implode(' UNION ', $query), 'image_type', false);

    } else {

        foreach ($ids as $type => $id) {

            if (!isset($config['available_images'][$type])) {

                unset($ids[$type]);

                continue;

            }

            $tpm = func_query_first('SELECT id, image_path, image_x, image_y FROM ' . $sql_tbl['images_' . $type] . ' WHERE id = \'' . $id . '\'');

            if (!empty($tpm))
                $image_data[$type] = $tpm;

        }

    }

    $return_type = '';

    foreach ($ids as $type => $id) {

        if (
            !isset($image_data[$type])
            || !is_array($image_data[$type])
            || !isset($image_data[$type]['id'])
        ) {

            $data['images'][$type] = array(
                'url'        => func_get_default_image($type),
                'x'          => $config['setup_images'][$type]['image_x'],
                'y'          => $config['setup_images'][$type]['image_y'],
                'is_default' => true,
            );

        } else {

            $d = $image_data[$type];

            $data['images'][$type] = array(
                'url' => func_get_image_url($d['id'], $type, $d['image_path']),
                'x'   => $d['image_x'],
                'y'   => $d['image_y'],
                'id'  => $d['id'],
            );

            if ($return_type == '') {
                $return_type = $type;
            }

        }

    }

    if (
        $return_type != $prefered_image_type
        && isset($image_data[$prefered_image_type])
        && !$data['images'][$prefered_image_type]['is_default']
    ) {
        $return_type = $prefered_image_type;
    }

    if (
        isset($data['images'][$return_type]['is_default'])
        && $data['images'][$return_type]['is_default'] !== false
    ) {
        foreach($data['images'] as $image_type => $image_data) {
            if (!$image_data['is_default']) {
                $return_type = $image_type;
                break;
            }
        }
    }

    $data['image_url']  = $data['images'][$return_type]['url'];
    $data['image_x']    = $data['images'][$return_type]['x'];
    $data['image_y']    = $data['images'][$return_type]['y'];
    $data['image_id']   = $data['images'][$return_type]['id'];
    $data['image_type'] = $return_type;
    
    $result[$key] = $data;

    return $data;
}

/**
 * This function creates a temporary file and stores some data in it.
 * It returns filename on success and 'false' in case of failure.
 */
function func_temp_store($data)
{
    global $file_temp_dir;

    $tmpfile = tempnam($file_temp_dir, 'xctmp');

    if (empty($tmpfile)) {

        return false;
    }

    $fp = func_fopen($tmpfile, 'w', true);

    if (!$fp) {

        @unlink($tmpfile);

        return false;

    }

    fwrite($fp, $data);

    @fclose($fp);

    func_chmod_file($tmpfile);

    return $tmpfile;
}

/**
 * Get tmpfile content
 */
function func_temp_read($tmpfile, $delete = false)
{
    if (empty($tmpfile))
        return false;

    $data = file_get_contents($tmpfile);

    if (false === $data) {

        return false;

    }

    if ($delete) {

        @unlink($tmpfile);

    }

    return $data;
}

/**
 * realpath() wrapper
 */
function func_realpath($path)
{
    global $xcart_dir;

    if (
        X_DEF_OS_WINDOWS
        && preg_match('!^((?:\\\\\\\\[^\\\\]+)|(?:\w:))(.*)!S', $path, $matched)
    ) {
        // windows paths: \\server\path and DRIVE:\path

        $path = $matched[1] . func_normalize_path($matched[2]);

    } else {

        // other paths
        if ($path[0] != '/' && $path[0] != '\\')
            $path = $xcart_dir . XC_DS . $path;

        $path = func_normalize_path($path);

        $cache = array ();

        do {
            $cache[$path] = true; // prevent the loop

            $path = func_resolve_fs_symlinks($path);

            if ($path === false) {
                // cannot resolve, broken path
                return false;
            }

        } while (empty($cache[$path]));
    }

    return $path;
}

/**
 * Helper function for func_realpath()
 * Works only under Unix like operating systems
 * Note: will not work when open_basedir is in effect
 */
function func_resolve_fs_symlinks($path)
{
    if (
        X_DEF_OS_WINDOWS
        || strlen($path) < 2
        || strlen(ini_get('open_basedir')) > 0
    ) {
        return $path;
    }

    $parts = explode('/', substr($path,1));

    $resolved = '';

    $normalize = false;

    while (!empty($parts)) {

        $elem = array_shift($parts);

        if (strlen($elem) == 0)
            continue;

        $resolved .= '/' . $elem;

        if (!file_exists($resolved))
            continue;

        if (is_link($resolved)) {

            $normalize = true;
            $link = readlink($resolved);

            if (
                $link === false
                || strlen($link) == 0
                || !strcmp($link, $elem)
            ) {
                // cannot resolve, broken path
                return false;
            }

            $link = preg_replace('!/+$!S','',$link);

            if (strlen($link) == 0) {

                $resolved = '/';

            } elseif ($link[0] == '/') {

                $resolved = $link;

            } else {

                $resolved .= '/../' . $link;

            }

        }

    }

    $path = $resolved;

    if ($normalize)
        $path = func_normalize_path($path);

    return strlen($path) == 0 ? false : $path;
}

/**
 * This function decide to allow/deny to use path for files
 * Returns: full path for the file if path is allowed,
 *          'false', if path is not allowed to use.
 * Relative path is changed to absolute path.
 */
function func_allowed_path($allowed_path, $path, $is_dir = false)
{
    global $xcart_dir;

    if (empty($path)) return false;

    if (
        !$is_dir
        && is_dir($path)
    ) {
        return false;
    }

    if (X_DEF_OS_WINDOWS) {

        $allowed_path = strtolower($allowed_path);
        $path = strtolower($path);
        $_xcart_dir = strtolower($xcart_dir);

    } else {

        $_xcart_dir = $xcart_dir;

    }

    $allowed_path = func_realpath($allowed_path);

    if (empty($allowed_path) || strncmp($allowed_path, $_xcart_dir, strlen($_xcart_dir)) != 0) return false;

    // absolute path
    if (
        (
            X_DEF_OS_WINDOWS
            && preg_match("/^(\\\\)|(\w:)/S",$path)
        ) || (
            !X_DEF_OS_WINDOWS
            && $path[0] == '/'
        )
    ) {

        $path = func_realpath($path);

    } else {

        $path = func_realpath($allowed_path . XC_DS . $path);

    }

    if (!strcmp($allowed_path, $path))
        return $allowed_path;

    if ($allowed_path[strlen($allowed_path) - 1] != XC_DS)
        $allowed_path .= XC_DS;

    if (!strncmp($path, $allowed_path, strlen($allowed_path)))
        return $path;

    return false;
}

/**
 * Check filename for present in X-Cart directory
 * Returns: full path for the file if file is allowed,
 *          'false', if path/file is not allowed to use.
 * Relative path is changed to absolute path.
 */
function func_allow_file($file, $is_root = false)
{
    global $xcart_dir, $logged_userid, $single_mode, $current_area, $active_modules, $files_dir_name, $files_dir_prefix;

    if (empty($file) || !func_is_allowed_file($file))
        return false;

    if (!is_url($file)) {

        $dir = $xcart_dir;

        if (!$is_root) {

            if (
                $current_area == 'A'
                || (
                    (
                        !empty($active_modules['Simple_Mode'])
                        || $single_mode
                    ) && $current_area == 'P'
                )
            ) {

                $dir = $files_dir_name;

            } elseif (
                $current_area == 'P'
                || $current_area == 'A'
            ) {

                $dir = $files_dir_name . XC_DS . $files_dir_prefix . $logged_userid;

            } else {

                $dir = $files_dir_name;

            }

        }

        $file = func_allowed_path($dir, $file);

    }

    return $file;
}

/**
 * fopen() wrapper
 */
function func_fopen($file, $perm = 'r', $is_root = false)
{
    $file = func_allow_file($file, $is_root);

    if ($file === false) {

        return false;

    }

    return @fopen($file, $perm);
}

/**
 * fopen + fread wrapper
 */
function func_file_get($file, $is_root = false)
{
    if (is_url($file)) {

        return func_url_get($file);

    }

    $file = func_allow_file($file, $is_root);

    if (false === $file) {

        return false;

    }

    return file_get_contents($file);
}

/**
 * readfile() wrapper
 */
function func_readfile($file, $is_root = false)
{
    $file = func_allow_file($file, $is_root);

    if (false === $file) {

        return false;

    }

    if (is_url($file)) {

        echo func_url_get($file);

        return true;

    } else {

        return readfile($file);

    }

}

/**
 * move_uploaded_file() wrapper
 */
function func_move_uploaded_file($file)
{
    global $file_temp_dir;

    if (!func_check_uploaded_files_sizes($file))
        return false;

    if (empty($file) || !isset($_FILES[$file]))
        return false;

    $path = tempnam($file_temp_dir, preg_replace('/^.*[\/\\\]/S', '', $_FILES[$file]['name']));

    if (empty($path))
        $path = $file_temp_dir . XC_DS . preg_replace('/^.*[\/\\\]/S', '', $_FILES[$file]['name']);

    $path = func_allow_file($path, true);

    if (empty($path))
        return false;

    if (move_uploaded_file($_FILES[$file]['tmp_name'], $path))
        return $path;

    @unlink($path);

    func_chmod_file($path);

    return false;
}

/**
 * file() wrapper
 */
function func_file($file, $is_root = false)
{
    $file = func_allow_file($file, $is_root);

    if ($file === false) return array();

    $result = @file($file);

    return (is_array($result) ? $result : array());
}

/**
 * Normalize path: remove '../', './' and duplicated slashes
 */
function func_normalize_path($path, $separator = XC_DS)
{
    $qs = preg_quote($separator,'!');

    $path = preg_replace("/[\\\\\/]+/S",$separator,$path);

    $path = preg_replace('!' . $qs . '\.' . $qs . '!S', $separator, $path);

    $regexp = '!' . $qs . '[^' . $qs . ']+' . $qs . '\.\.' . $qs . '!S';

    for ($old = '', $prev = '1'; $old != $path; $path = preg_replace($regexp, $separator, $path)) {

        $old = $path;

    }

    return $path;
}

/**
 * Create path to file/directory relating to $home_dir
 */
function func_relative_path($dir, $home_dir = false)
{
    global $xcart_dir;

    if (empty($dir))
        return false;

    if ($home_dir === false)
        $home_dir = $xcart_dir;

    $home_dir = preg_replace('/' . preg_quote(XC_DS, '/') . '$/', '', $home_dir);

    $dir = func_realpath($dir);

    $is_dir = is_dir($dir);

    // Get paths as arrays
    $d = explode(XC_DS, $is_dir ? $dir : dirname($dir));
    $h = explode(XC_DS, $home_dir);

    $dir_disc = strtoupper(array_shift($d));
    $home_disc = strtoupper(array_shift($h));

    if (X_DEF_OS_WINDOWS) {

        // Check disk letters
        if (($dir_disc !== $home_disc)) {
            return false;

        // Check net devies names
        } elseif(
            empty($dir_disc)
            && empty($home_disc)
            && empty($d[0])
            && empty($h[0])
        ) {
            array_shift($d);
            array_shift($h);

            $dir_disc = array_shift($d);
            $home_disc = array_shift($h);

            if ($dir_disc != $home_disc)
                return false;

        }

    }

    $max = count($h);

    if (count($d) < $max)
        $max = count($d);

    // Define equal root for both paths
    $root = 0;

    for ($x = 0; $x < $max; $x++) {

        if ($d[$x] !== $h[$x])
            break;

        $root++;

    }

    // Build prefix (return from home dir to destination dir) for result path
    $prefix_str = str_repeat('..' . XC_DS, count($h) - $root);

    if (empty($prefix_str)) {

        $prefix_str = '.' . XC_DS;

    }

    // Remove root from destination dir
    if ($root > 0) {
        array_splice($d, 0, $root);
    }

    if (!empty($d)) {
        $prefix_str .= implode(XC_DS, $d) . XC_DS;
    }

    if (!$is_dir) {
        $prefix_str .= basename($dir);
    }

    return $prefix_str;
}

function is_url($url)
{
    if (empty($url) || !is_string($url))
        return false;

    return preg_match('/^(http|https|ftp):\/\//isS', $url);
}

function func_get_image_type($image_type)
{
    static $imgtypes = array (
        '/gif/i'        => 'GIF',
        '/jpg|jpeg/i'   => 'JPEG',
        '/png/i'        => 'PNG',
        '/bmp/i'        => 'BMP',
    );

    foreach ($imgtypes as $k => $v) {

        if (preg_match($k, $image_type))
            return $v;

    }

    return 'JPEG';
}

/**
 * Determine size of local or http(s):// file
 */
function func_filesize($file, $read_file = true)
{
    global $config;

    // without can return zero for just uploaded, non-zero size and exists files (affected: PHP 4.4.0 CGI)
    clearstatcache();

    if (!is_url($file)) {

        return @filesize($file);

    }

    if (($length = func_url_get($file, 'CONTENT-LENGTH')) !== false) {
        return intval($length);
    }

    if (
        $read_file
        && ($fp = func_fopen(func_urlencode($file), 'rb', true))
    ) {

        while (($string_len = strlen($str = fread($fp, 8192))) > 0) {

            $length += $string_len;

        }

        @fclose($fp);
    }

    return intval($length);
}

function func_is_full_path($path)
{
    return (
        is_url($path)
        || (
            X_DEF_OS_WINDOWS
            && preg_match("/^(?:\w:\\\)|^(?:\\\\\\\\\\w+\\\)/S", $path)
        ) || (
            !X_DEF_OS_WINDOWS
            && preg_match("/^\//S", $path)
        )
    );
}

/**
 * Performs creation of all directories in a directory path.
 *
 * @param   mixed    $dir     Directory path.
 * @param   mixed    $mode optional. Desired directory permissions. By default x-cart tries to come up with reasonable permissions automatically.
 * @access  public
 * @return  boolean  True on success, false in case of failure.
 */
function func_mkdir($dir, $mode = NULL)
{
    $dir = func_realpath($dir);

    $dirstack = array();

    while (
        !is_dir($dir)
        && $dir != '/'
    ) {

        if ($dir != '.') {

            array_unshift($dirstack, $dir);

        }

        $dir = dirname($dir);

    }

    $old_umask = umask(0000);

    while ($newdir = array_shift($dirstack)) {

        if (is_null($mode)) {

            $dir_permissions = func_fs_get_dir_permissions($newdir);

            $mode = !is_null($dir_permissions) ? $dir_permissions : 0777;

        }

        if (!@mkdir($newdir, $mode)) {
            // Failed to create the directory
            umask($old_umask);

            return false;

        }

    }

    umask($old_umask);

    return true;
}

/**
 * XC_DS independent comparison of paths
 * $use_len = 1 // for path1
 * $use_len = 2 // for path2
 * $use_len = NULL // for exact match
 */
function func_pathcmp($path1, $path2, $use_len = NULL)
{
    static $func_defs = array (
        0 => array (
            'strcmp',
            'strncmp',
        ),
        1 => array (
            'strcasecmp',
            'strncasecmp',
        ),
    );

    $index = (int)(X_DEF_OS_WINDOWS);

    $func = $func_defs[$index];

    $path1 = func_normalize_path($path1);
    $path2 = func_normalize_path($path2);

    if (is_null($use_len))
        return !$func[0]($path1, $path2);

    $len = ($use_len == 1) ? strlen($path1) : strlen($path2);

    return !$func[1]($path1, $path2, $len);
}

/**
 * This function is a wrapper for md5_file() function
 * Note: $mode is used in admin/snapshots.php script
 */
function func_md5_file($file, $mode = 1)
{
    if ($mode == '2') {

        global $files_md5_list;

        return $files_md5_list[$file];

    }

    if (!is_url($file))
        return md5_file($file);

    $content = func_url_get($file);

    return false === $content
        ? false
        : md5($content);
}

/**
 * Check - URL is exists or not
 */
function func_url_is_exists($url)
{
    static $allow_url_fopen = null;

    if (!is_url($url)) return false;

    if (is_null($allow_url_fopen)) {
        $allow_url_fopen = ini_get('allow_url_fopen');
    }

    if (
        $allow_url_fopen
        && ($fp = func_fopen(func_urlencode($url), 'rb', true))
    ) {
        @fclose($fp);

        return true;
    }

    if (func_url_get($url, " 200 OK") !== false) {
        return true;
    }

    return false;
}

/**
 * Check URL headers
 */
function func_url_check_headers($result, $header_name = '')
{
    list($headers, $content) = $result;

    $error = is_array($headers) ? $headers['ERROR'] : $headers;

    $is_200_ok = preg_match("/HTTP\/.*\s*200\s*OK/i", $error);

    #Calling function requests to check if server code response is 200 OK
    if ($is_200_ok && preg_match("/200\s*OK/i", $header_name))
        return $header_name;

    if (!empty($header_name)) {
        $header_data = null;
        if (is_array($headers)) {
            $header_data = $headers[$header_name];
        } else {

            $_headers = explode("\r\n", $headers);
            if (is_array($_headers)) {
                foreach($_headers as $_header) {
                    if (strpos($_header, ': ') === false)
                        continue;

                    list($key, $value) = explode(': ', $_header, 2);
                    if (strcasecmp($key,$header_name) == 0) {
                        $header_data = $value;
                        break;
                    }
                }    

            } 
            
            if (!isset($header_data)) {
                preg_match('/'.preg_quote($header_name, '/').": (.*)/i", $headers, $matches);
                $header_data = $matches[1];
            }
        }
    }

    if ($is_200_ok) {
        return (empty($header_name) ? $content : $header_data);
    }

    return false;
}

/**
 * Get URL headers/content
 */
function func_url_get($url, $header_name = '')
{
    static $allow_url_fopen = null;

    if (!is_url($url)) {

        return false;

    }

    if (is_null($allow_url_fopen)) {

        $allow_url_fopen = ini_get('allow_url_fopen');

    }

    if (
        empty($header_name)
        && $allow_url_fopen
    ) {
        return file_get_contents($url);
    }

    $host = @parse_url($url);

    if (empty($host['port'])) {
        $host['port'] = 80;
    }

    x_load('http');

    $http_functions = array();

    $method = empty($header_name) ? 'GET' : 'HEAD';

    $http_function = empty($header_name) ? 'func_http_get_request' : 'func_http_head_request';

    $_url = $host['host'] . ':' . $host['port'];

    $result = $host['scheme'] == 'http'
            ? $http_function($_url, $host['path'], $host['query'])
            : func_https_request($method, $url);

    if (($data = func_url_check_headers($result, $header_name)) !== false) {

        return $data;

    }

    $result = func_fsockopen_request($method, $_url, $host['path'], $host['query']);

    if (($data = func_url_check_headers($result, $header_name)) !== false) {

        return $data;

    }

    return false;
}

/**
 * Assemble URL by array (array as parse_url() function result)
 */
function func_assemble_url($data)
{
    if (empty($data['host']))
        return false;

    $str = ($data['scheme'] ? $data['scheme'] : 'http').'://';

    if (!empty($data['user'])) {

        $str .= $data['user'];

        if (!empty($data['pass']))
            $str .= ':' . $data['pass'];

        $str .= '@';
    }

    $str .= $data['host'];

    if (!empty($data['path']))
        $str .= $data['path'];

    if (!empty($data['query']))
        $str .= '?' . $data['query'];

    if (!empty($data['fragment']))
        $str .= '#' . $data['fragment'];

    return $str;
}

/**
 * Returns default file permissions for a file system entity.
 *
 * @param   string  $entity         File system entity name (directory/file name).
 * @param   string  $entity_type    File system entity type (dir - directory or file).
 * @access  public
 * @return  mixed   File permission as an octal number, NULL in case of failure.
 * @since   4.1.10
 */
function func_fs_get_default_permissions($entity, $entity_type)
{
    global $xcart_fs_default_permissions;

    if (
        strlen($entity) == 0
        || !in_array($entity_type, array('file', 'dir'))
    ) {

        return NULL;

    }

    if (
        $entity_type == 'file'
        && preg_match("/\.php$/sS", basename($entity))
    ) {
        $entity_type = 'phpfile';
    }

    $exec_mode = func_get_php_execution_mode();

    return $xcart_fs_default_permissions[$entity_type][$exec_mode];
}

/**
 * Returns file permissions for file system entities of specified type.
 *
 * @param   string  $entity         File system entity name (directory/file name).
 * @param   string  $entity_type    File system entity type (dir - directory or file).
 * @access  public
 * @return  mixed   File permission as an octal number, NULL in case of failure.
 * @since   4.1.10
 */
function func_fs_get_entity_permissions($entity, $entity_type)
{
    global $xcart_dir, $xcart_fs_permissions_map;

    // Input validation
    if (
        strlen($entity) == 0
        || !in_array($entity_type, array('dir', 'file'))
    ) {

        return NULL;

    }

    $entity = func_realpath($entity);

    $xcart_dir_replace_str = '/^' . preg_quote($xcart_dir . XC_DS, '/') . '/sS';

    if (
        !preg_match($xcart_dir_replace_str, $entity)
    ) {

        // We can only provide permissions information for directories/files managed by X-Cart.
        return NULL;

    }

    $exec_mode = func_get_php_execution_mode();

    // get relative path
    $entity = preg_replace($xcart_dir_replace_str, '', $entity);

    if (strlen($entity) == 0) {

        return NULL;

    }

    $dir_paths = explode(XC_DS, $entity);

    if (empty($dir_paths)) {

        // Return default permissions for specified entity type
        return func_fs_get_default_permissions($entity, $entity_type);

    }

    while (!empty($dir_paths)) {

        $check_dir = implode(XC_DS, $dir_paths);

        if (in_array($check_dir, array_keys($xcart_fs_permissions_map))) {

            // Found a match, return corresponding entity permissions
            return $xcart_fs_permissions_map[$check_dir][$entity_type][$exec_mode];

        }

        array_pop($dir_paths);
    }

    // Return default permissions for specified entity type
    return func_fs_get_default_permissions($entity, $entity_type);
}

/**
 * Returns file system permissions for specified file name (with full file system path).
 *
 * @param   string  $filename File path.
 * @access  public
 * @return  mixed   File permission as an octal number, NULL in case of failure.
 * @since   4.1.10
 */
function func_fs_get_file_permissions($filename)
{
    if (strlen($filename) == 0) {

        return NULL;
    }

    return func_fs_get_entity_permissions($filename, 'file');
}

/**
 * Returns file system permissions for specified directory.
 *
 * @param   string   $dir   Directory path.
 * @access  public
 * @return  mixed    Directory permissions as an octal number, NULL in case of failure.
 * @since   4.1.10
 */
function func_fs_get_dir_permissions($dir)
{
    if (strlen($dir) == 0) {

        return NULL;
    }

    return func_fs_get_entity_permissions($dir, 'dir');
}

/**
 * Wrapper for urlencode function.It can encode only query string from whole url.
 *
 * @param   string    $url
 * @access  public
 * @return  string    Urlencoded string
 * @since   4.3.1
 */
function func_urlencode($url, $only_query = true)
{
    if ($only_query) {

        $res = preg_replace("%(.*\?)(.*)(#.*)%Ssie", "'\\1'.urlencode('\\2').'\\3'", $url);

        $res = empty($res)
            ? $url
            : $res;

    } else {

        $res = urlencode($url);

    }

    return $res;
}

/**
 * Returns max filesize (in bytes) allowed for uploading to a server.
 *
 * @access  public
 * @return  number  the filesize in bytes allowed for uploading to a server.
 * @since   4.3.1
 */
function func_upload_max_filesize()
{
    static $max_filesize;

    if (!isset($max_filesize)) {

        $post_max_size = func_convert_to_byte(ini_get('post_max_size'));

        $upload_max_filesize = func_convert_to_byte(ini_get('upload_max_filesize'));

        $max_filesize = intval(min($post_max_size, $upload_max_filesize));

    }

    return $max_filesize;
}

/**
 * Return if smarty template has a backup copy
 *
 * @param string $smarty_file full path to smarty template file
 *
 * @return boolean
 * @see    ____func_see____
 * @since  1.0.0
 */
function func_has_skin_backup($smarty_file)
{
    $backup = func_get_backup_name($smarty_file);

    return file_exists($backup) && is_readable($backup);
}

/**
 * Copy source file to target one. Content binary safe copy.
 *
 * @param string $source source file full path name
 * @param string $target target file full path name
 *
 * @return boolean
 * @see    ____func_see____
 * @since  1.0.0
 */
function func_write_file_copy($source, $target)
{
    $data = file_get_contents($source);

    $result = false;

    if (false !== $data) {

        $fp = @fopen($target, 'wb');

        if (false !== $fp) {

            fwrite($fp, $data);

            @fclose($fp);

            $result = true;
        }

    }

    return $result;
}

/**
 * Make backup of smarty template file
 *
 * @param string $smarty_file smarty file full path
 *
 * @return boolean
 * @see    ____func_see____
 * @since  1.0.0
 */
function func_backup_skin($smarty_file)
{
    $result = false;

    // only not yet backed up file could be stored into backup repository
    if (!func_has_skin_backup($smarty_file)) {

        $backup = func_get_backup_name($smarty_file);

        $result = func_write_file_copy($smarty_file, $backup);

    }

    return $result;
}

/**
 * Restore smarty file from its backup copy
 *
 * @param string $smarty_file full path of smarty template
 *
 * @return boolean
 * @see    ____func_see____
 * @since  1.0.0
 */
function func_restore_skin_from_backup($smarty_file)
{
    $result = false;

    if (func_has_skin_backup($smarty_file)) {

        $backup = func_get_backup_name($smarty_file);

        $result = func_write_file_copy($backup, $smarty_file);

        if (true === $result) {

            @unlink($backup);

        }

    }

    return $result;
}

/**
 * Returns name of backup file for smarty file
 *
 * @param string $smarty_file full path of smarty template
 *
 * @return boolean
 * @see    ____func_see____
 * @since  1.0.0
 */
function func_get_backup_name($smarty_file)
{
    global $xcart_dir, $templates_repository_dir;

    return $xcart_dir . $templates_repository_dir . XC_DS . 'backup.' . md5($smarty_file) . '.tpl';
}

?>
