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
 * Default generator
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: default.php,v 1.16.2.1 2011/01/10 13:11:58 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }

function wave($foreground_color, $background_color, $img, $width, $height)
{
    $center = $width / 2;

    // periods
    $rand1 = mt_rand(750000,1200000)/10000000;
    $rand2 = mt_rand(750000,1200000)/10000000;
    $rand3 = mt_rand(750000,1200000)/10000000;
    $rand4 = mt_rand(750000,1200000)/10000000;

    // phases
    $rand5 = mt_rand(0,3141592)/500000;
    $rand6 = mt_rand(0,3141592)/500000;
    $rand7 = mt_rand(0,3141592)/500000;
    $rand8 = mt_rand(0,3141592)/500000;

    // amplitudes
    $rand9  = mt_rand(330,420)/110;
    $rand10 = mt_rand(330,450)/110;

    $img2 = imagecreatetruecolor($width - 10, $height + 21);

    $bgcolor = imagecolorallocate($img2, $background_color['red'], $background_color['green'], $background_color['blue']);

    imagefilledrectangle($img2, 0, 0, imagesx($img2), imagesy($img2), $bgcolor);

    for ($x = 0; $x < $width; $x++) {

        for ($y = -10; $y < $height + 20; $y++) {

            $sx = $x + (sin($x * $rand1 + $rand5) + sin($y * $rand3 + $rand6)) * $rand9 - $width / 2 + $center + 1 - 8;
            $sy = $y + (sin($x * $rand2 + $rand7) + sin($y * $rand4 + $rand8)) * $rand10;

            if ($sx < 0 || $sy < 0 || $sx >= $width - 1 || $sy >= $height - 1) {

                $color = 255;
                $color_x = 255;
                $color_y = 255;
                $color_xy = 255;

            } else {

                $color = imagecolorat($img, $sx, $sy) & 0xFF;
                $color_x = imagecolorat($img, $sx+1, $sy) & 0xFF;
                $color_y = imagecolorat($img, $sx, $sy+1) & 0xFF;
                $color_xy = imagecolorat($img, $sx+1, $sy+1) & 0xFF;

            }

            if($color == 0 && $color_x == 0 && $color_y == 0 && $color_xy == 0){

                $newred = $foreground_color[0];
                $newgreen = $foreground_color[1];
                $newblue = $foreground_color[2];

            } elseif ($color == 255 && $color_x == 255 && $color_y == 255 && $color_xy == 255) {

                $newred = $background_color[0];
                $newgreen = $background_color[1];
                $newblue = $background_color[2];

            } else {

                $frsx = $sx - floor($sx);
                $frsy = $sy - floor($sy);
                $frsx1 = 1 - $frsx;
                $frsy1 = 1 - $frsy;

                $newcolor = (
                            $color * $frsx1 * $frsy1 +
                            $color_x * $frsx * $frsy1 +
                            $color_y * $frsx1 * $frsy +
                            $color_xy * $frsx * $frsy);

                if ($newcolor > 255) $newcolor = 255;

                $newcolor = $newcolor / 255;

                $newcolor0 = 1 - $newcolor;

                $newred = $newcolor0 * $foreground_color[0] + $newcolor * $background_color[0];
                $newgreen = $newcolor0 * $foreground_color[1] + $newcolor * $background_color[1];
                $newblue = $newcolor0 * $foreground_color[2] + $newcolor * $background_color[2];
            }

            imagesetpixel($img2, $x, $y + 10, imagecolorallocate($img2,$newred,$newgreen,$newblue));

        }

    }

    return $img2;

}

function move_pixels($col, $col_height, $dest, $size, $im)
{
    if ($dest == 'up') {

        for ($i = 0; $i < $col_height - $size; $i++) {

            $next_pixel_color = imagecolorat($im, $col, $i + $size);

            imagesetpixel($im, $col, $i, $next_pixel_color);

        }

    } elseif ($dest == 'down') {

        for ($i = $col_height; $i >= $size; $i--) {

            $next_pixel_color = imagecolorat($im, $col, $i - $size);

            imagesetpixel($im, $col, $i, $next_pixel_color);

        }

    }

    return $im;

}

function draw_lines($im, $width, $height)
{

    $line_color = array (
        'red'     => 198,
        'green' => 189,
        'blue'     => 165,
    );

    $linecolor = imagecolorallocate($im, $line_color['red'], $line_color['green'], $line_color['blue']);

    for ($i = 0; $i <= $height; $i += 10) {

        imagedashedline($im, 0, $i, $width, $i, $linecolor);

    }

    for ($i = 0; $i <= $width; $i += 10) {

        imagedashedline($im, $i, 0, $i, $height, $linecolor);

    }

    return $im;
}

function draw_pixels($width, $height, $im)
{
    for ($i = 0; $i < $width; $i++) {

        $color = imagecolorallocate($im, mt_rand(0,255),mt_rand(50,255), mt_rand(0, 50));

        imagesetpixel ( $im, rand(1, $width), rand(1, $height), $color);

    }

    return $im;
}

function get_char_by_index_from_font($index)
{
    global $symbol_width;
    global $height;
    global $xcart_dir;

    $font_im = imagecreatefrompng($xcart_dir . '/modules/Image_Verification/img_generators/default/font.png');

    $character_im = imagecreatetruecolor($symbol_width, $height);

    imagecopymerge($character_im, $font_im, 0, 0, ($index * $symbol_width), 0, $symbol_width, $height, 100);

    imagedestroy($font_im);

    return $character_im;

}

function generate_image($code, $im)
{
    global $characters;
    global $symbol_width;
    global $height;

    for ($i = 0, $x = 0; $i < strlen($code); $i++, $x += $symbol_width) {

        $char_im = get_char_by_index_from_font($characters[$code[$i]]);

        imagecopymerge($im, $char_im, $x, 0, 0, 0, $symbol_width, $height, 100);

    }

    return $im;
}

global $symbol_width;
global $height;
global $characters;

$symbol_width = 20;
$width = strlen($generation_str) * $symbol_width + 20;
$height = 20;
$code = $generation_str;

$bg_color = array (
    'red' => 255,
    'green' => 255,
    'blue' => 255,
    0 => 255,
    1 => 255,
    2 => 255,
);

$text_color = array (
    'red' => 0,
    'green' => 0,
    'blue' => 0,
    0 => 0,
    1 => 0,
    2 => 0,
);

$characters = array (
    '1' => 0,
    '2' => 1,
    '3' => 2,
    '4' => 3,
    '5' => 4,
    '6' => 5,
    '7' => 6,
    '8' => 7,
    '9' => 8,
    '0' => 9,
    'A' => 10,
    'B' => 11,
    'C' => 12,
    'D' => 13,
    'E' => 14,
    'F' => 15,
    'G' => 16,
    'H' => 17,
    'I' => 18,
    'J' => 19,
    'K' => 20,
    'L' => 21,
    'M' => 22,
    'N' => 23,
    'O' => 24,
    'P' => 25,
    'Q' => 26,
    'R' => 27,
    'S' => 28,
    'T' => 29,
    'U' => 30,
    'V' => 31,
    'W' => 32,
    'X' => 33,
    'Y' => 34,
    'Z' => 35,
);

$im = imagecreatetruecolor($width, $height);

$bgcolor = imagecolorallocate($im, $bg_color['red'], $bg_color['green'], $bg_color['blue']);

imagefilledrectangle($im, 0, 0, imagesx($im), imagesy($im), $bgcolor);

$im = generate_image($code, $im);

$im = wave($text_color, $bg_color, $im, $width, $height);

$im = draw_lines($im, $width - 10, $height + 20);

header("Content-type:image/png");

ob_start();

imagepng($im);

$image = ob_get_contents();

ob_end_clean();

header("Content-Length: " . strlen($image));

echo $image;

imagedestroy($im);

?>
