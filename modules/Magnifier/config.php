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
 * Module configuration
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: config.php,v 1.31.2.2 2011/04/22 12:14:17 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header('Location: ../../'); die('Access denied'); }

// Global definitions for Magnifier module
/**
 * PLEASE DO NOT CHANGE ANY VALUES DEFINED IN THIS FILE, AS IT WILL HAVE
 * A NEGATIVE EFFECT ON THE LOOK OF X-MAGNIFIER IMAGE VIEWER AND
 * THE FUNCTIONALITY OF X-MAGNIFIER IN GENERAL
 */

$addons['Magnifier'] = true;

$css_files['Magnifier'][] = array();

$gd_not_loaded = false;
if (extension_loaded('gd') && function_exists("gd_info")) {
    $gd_config = gd_info();
    if (!empty($gd_config["GIF Read Support"]) && (!empty($gd_config["JPG Support"]) || !empty($gd_config["JPEG Support"])) && !empty($gd_config["PNG Support"])) {
        $gd_config['correct_version'] = true;
    }
} else {
    $gd_not_loaded = true;
}

$sql_tbl['images_Z'] = "xcart_images_Z";

$config['available_images']['Z'] = "M";
$predefined_setup_images['Z'] = array(
    'itype' => $k,
    'location' => 'FS',
    'save_url' => '',
    'size_limit' => 0,
    'md5_check' => '',
    'default_image' => './default_image.gif'
);
define('NO_CHANGE_LOCATION_Z', true);

define('ZOOMER_HINT_MAGNIFIER', "Use Ctrl to zoom out");

if (defined('TOOLS')) {
    $tbl_demo_data['Magnifier'] = array(
        'images_Z' => 'images'
    );
}

$magnifier_sets = array();

/**
 * path to Magnifier specific skins directory
 */
$magnifier_sets['skins_folder'] = $xcart_dir . $smarty_skin_dir . '/modules/Magnifier/skins';

/**
 * URL to Magnifier specific skins directory
 */
$magnifier_sets['url_skins_folder'] = $xcart_web_dir . $smarty_skin_dir . '/modules/Magnifier/skins';

/**
 * Maximum dimension value of image file (both for width and height)
 */
$magnifier_sets['max_image_size'] = 2000;

/**
 * Tile image file width and height
 */
$magnifier_sets['x_tile_size'] = 100;
$magnifier_sets['y_tile_size'] = 100;

/**
 * Thumbnail image file width and height
 */
$magnifier_sets['x_thmb'] = 80;
$magnifier_sets['y_thmb'] = 65;

/**
 * Width and height of working area block
 */
$magnifier_sets['x_work_area'] = $config['Magnifier']['magnifier_width'] - 24 - 2;
$magnifier_sets['y_work_area'] = $config['Magnifier']['magnifier_height'] - 129 - 2;

/**
 * Width and height of 'Create Thumbnail' popup window.
 */
$magnifier_sets['x_crt_thmb'] = 500;
$magnifier_sets['y_crt_thmb'] = 500;

/**
 * JPEG quality parameters for tile, level and thumbnail images
 */
$magnifier_sets['jpg_qlt_tile'] = '80';
$magnifier_sets['jpg_qlt_level'] = '85';
$magnifier_sets['jpg_qlt_thmb'] = '95';

/**
 * Extension of image files (jpeg only supported)
 */
$magnifier_sets['extension'] = 'jpg';

/**
 * Saving of initial image file flag. (1 - initial file will be saved)
 */
$magnifier_sets['save_init_image'] = 1;

$smarty->assign('magnifier_sets', $magnifier_sets);

if (defined('IS_IMPORT')) {
    $modules_import_specification['MAGNIFIER_IMAGES'] = array(
        'script'        => '/modules/Magnifier/import.php',
        'permissions'   => 'A',
        'need_provider' => true,
        'parent'        => 'PRODUCTS',
        'export_sql'    => "SELECT id as productid FROM $sql_tbl[images_Z] GROUP BY id",
        'table'         => 'images_Z',
        'key_field'     => 'id',
        'parent_key_field' => 'id',
        'columns'       => array(
            'productid'     => array(
                'is_key'    => true,
                'type'      => 'N',
                'default'   => 0),
            'productcode'   => array(
                'is_key'    => true),
            'product'       => array(
                'is_key'    => true),
            'image'         => array(
                'array'     => true,
                'type'         => 'I',
                'itype'        => 'Z',
                'required'   => true),
            'alt'           => array(
                'array'     => true),
            'orderby'       => array(
                'type'      => 'N',
                'array'     => true)
        )
    );
}

$required_functions = array(
    'imagejpeg',
    'imagecopyresampled',
    'imageCreatetruecolor',
    'imagecolorallocate',
    'imagefill',
    'imagedestroy'
);

foreach($required_functions as $function)
    if (!function_exists($function)) {
        unset($active_modules['Magnifier']);
        return;
    }

$_module_dir  = $xcart_dir . XC_DS . 'modules' . XC_DS . 'Magnifier';
/*
 Load module functions
*/
if (!empty($include_func))
    require_once $_module_dir . XC_DS . 'func.php';
?>
