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
 * PHP GD library functions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.gd.php,v 1.20.2.1 2011/01/10 13:11:51 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load('files');

/**
 * Check if GD extension or GD function are available
 */
function func_check_gd($func_gd = '')
{
    $gd_res = (extension_loaded('gd') && function_exists("gd_info"));
    $res = ($func_gd == '') ? $gd_res : ($gd_res && function_exists($func_gd));
    if ($func_gd != '' && !$res && $gd_res)
        func_gd_log("'$func_gd' function is not available");

    return $res;
}

/**
 * Logs GD processing errors
 */
function func_gd_log($message)
{
    return x_log_flag('log_gd_messages', 'GD', $message, true);
}

/**
 * Universal GD wrapper
 */
function func_gd_wrapper()
{
    $args = func_get_args();
    $func = array_shift($args);

    if (!func_check_gd($func))
        return false;

    $result = @call_user_func_array($func, $args);

    if (!$result) {

        func_gd_log($func . 'with parameters: (\'' . implode('\', \'', $args) . '\') returns error');

    }

    return $result;
}

/**
 * imagedestroy wrapper
 */
function func_imagedestroy($img)
{
    return func_gd_wrapper('imagedestroy', $img);
}

/**
 * imagepng wrapper
 */
function func_imagepng($img, $file)
{
    return func_gd_wrapper('imagepng', $img, $file);
}

/**
 * imagejpeg wrapper
 */
function func_imagejpeg($img, $file)
{
    return func_gd_wrapper('imagejpeg', $img, $file);
}

/**
 * imagegif wrapper
 */
function func_imagegif($img, $file)
{
    return func_gd_wrapper('imagegif', $img, $file);
}

/**
 * imagesx wrapper
 */
function func_imagesx($img)
{
    return func_gd_wrapper('imagesx', $img);
}

/**
 * imagesy wrapper
 */
function func_imagesy($img)
{
    return func_gd_wrapper('imagesy', $img);
}

/**
 * imagecopyresampled wrapper
 */
function func_imagecopyresampled($dst, $src, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)
{
    $d_max_x = func_imagesx($dst);
    $s_max_x = func_imagesx($src);
    $d_max_y = func_imagesy($dst);
    $s_max_y = func_imagesy($src);

    if ($d_max_x && $s_max_x && $d_max_y && $s_max_y) {
        $dst_x = ($d_max_x < $dst_x) ? $d_max_x : ($dst_x < 0 ? 0 : $dst_x);
        $src_x = ($s_max_x < $src_x) ? $s_max_x : ($src_x < 0 ? 0 : $src_x);

        $dst_y = ($d_max_y < $dst_y) ? $d_max_y : ($dst_y < 0 ? 0 : $dst_y);
        $src_y = ($s_max_y < $src_y) ? $s_max_y : ($src_y < 0 ? 0 : $src_y);

        $dst_w = ($dst_x + $dst_w > $d_max_x) ? 0 : $dst_w;
        $dst_h = ($dst_y + $dst_h > $d_max_y) ? 0 : $dst_h;
        $src_w = ($src_x + $src_w > $s_max_x) ? 0 : $src_w;
        $src_h = ($src_y + $src_h > $s_max_y) ? 0 : $src_h;
    }

    return func_gd_wrapper('imagecopyresampled', $dst, $src, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
}

/**
 * imagecolortransparent wrapper
 */
function func_imagecolortransparent($img, $color)
{
    return func_gd_wrapper('imagecolortransparent', $img, $color);
}

/**
 * imagefill wrapper
 */
function func_imagefill($img, $x, $y, $color)
{
    return func_gd_wrapper('imagefill', $img, $x, $y, $color);
}

/**
 * imagecopy wrapper
 */
function func_imagecopy($dst, $src, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h)
{
    return func_gd_wrapper('imagecopy', $dst, $src, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h);
}

/**
 * imagecolorallocate wrapper
 */
function func_imagecolorallocate($img, $r, $g, $b)
{
    return func_gd_wrapper('imagecolorallocate', $img, $r, $g, $b);
}

/**
 * imagecolorat wrapper
 */
function func_imagecolorat($img, $x, $y)
{
    return func_gd_wrapper('imagecolorat', $img, $x, $y);
}

/**
 * imagesetpixel wrapper
 */
function func_imagesetpixel($img, $x, $y, $color)
{
    return func_gd_wrapper('imagesetpixel', $img, $x, $y, $color);
}

/**
 * imagealphablending wrapper
 */
function func_imagealphablending($img, $flag)
{
    return func_gd_wrapper('imagealphablending', $img, $flag);
}

/**
 * imagesavealpha wrapper
 */
function func_imagesavealpha($img, $flag)
{
    return func_gd_wrapper('imagesavealpha', $img, $flag);
}

/**
 * imagecolorallocatealpha wrapper
 */
function func_imagecolorallocatealpha($img, $a, $b, $c, $d)
{
    return func_gd_wrapper('imagecolorallocatealpha', $img, $a, $b, $c, $d);
}

/**
 * imagecreatetruecolor wrapper
 */
function func_imagecreatetruecolor($x, $y)
{
    return func_gd_wrapper('imagecreatetruecolor', $x, $y);
}

/**
 * imagecreatefromjpeg wrapper
 */
function func_imagecreatefromjpeg($file)
{
    return func_gd_wrapper('imagecreatefromjpeg', $file);
}

/**
 * imagecreatefromgif wrapper
 */
function func_imagecreatefromgif($file)
{
    return func_gd_wrapper('imagecreatefromgif', $file);
}

/**
 * imagecreatefrompng wrapper
 */
function func_imagecreatefrompng($file)
{
    return func_gd_wrapper('imagecreatefrompng', $file);
}

/**
 * imagecreatefromxpm wrapper
 */
function func_imagecreatefromxpm($file)
{
    return func_gd_wrapper('imagecreatefromxpm', $file);
}

/**
 * imagecreatefromgd wrapper
 */
function func_imagecreatefromgd($file)
{
    return func_gd_wrapper('imagecreatefromgd', $file);
}

/**
 * imagecreatefromgd2 wrapper
 */
function func_imagecreatefromgd2($file)
{
    return func_gd_wrapper('imagecreatefromgd2', $file);
}

/**
 * imagecreatefromwbmp wrapper
 */
function func_imagecreatefromwbmp($file)
{
    return func_gd_wrapper('imagecreatefromwbmp', $file);
}

?>
