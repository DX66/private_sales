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
 * Configuration script
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: config.php,v 1.45.2.1 2011/01/10 13:12:00 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }
/**
 * Global definitions for XAffiliate module
 */

$css_files['Product_Options'][] = array();

$config['available_images']['W'] = "U";
func_image_cache_assign('W', 'pvarthmbn');

$sql_tbl['classes']             = "xcart_classes";
$sql_tbl['class_options']       = "xcart_class_options";
$sql_tbl['class_lng']           = "xcart_class_lng";
$sql_tbl['variants']            = "xcart_variants";
$sql_tbl['variant_items']       = "xcart_variant_items";
$sql_tbl['variant_backups']     = "xcart_variant_backups";
$sql_tbl['product_options_lng'] = "xcart_product_options_lng";
$sql_tbl['product_options_ex']  = "xcart_product_options_ex";
$sql_tbl['product_options_js']  = "xcart_product_options_js";
$sql_tbl['images_W']            = "xcart_images_W";

$variant_properties = array(
    'avail',
    'weight',
    'productcode',
);

define('VARIANT_SKU_PREFIX', 'SKUV');

if (defined('IS_IMPORT')) {

    $modules_import_specification['PRODUCT_OPTIONS'] = array(
        'script'        => '/modules/Product_Options/import.php',
        'permissions'   => 'AP',
        'need_provider' => true,
        'finalize'      => true,
        'parent'        => 'PRODUCTS',
        'orderby'       => 1,
        'export_sql'    => "SELECT productid FROM $sql_tbl[classes] GROUP BY productid",
        'table'         => 'classes',
        'key_field'     => 'productid',
        'orderby'       => 90,
        'columns'       => array(
            'productid'     => array(
                'type'      => 'N',
                'is_key'    => true,
                'default'   => 0),
            'productcode'   => array(
                'is_key'    => true),
            'product'       => array(
                'is_key'    => true),
            'classid'       => array(
                'type'      => 'N',
                'is_key'    => true),
            'class'         => array(
                'is_key'    => true,
                'required'  => true),
            'type'          => array(
                'type'      => 'E',
                'variants'  => array('','T','Y')),
            'descr'         => array(),
            'orderby'       => array(
                'type'      => 'N',
                'default'   => 0),
            'avail'         => array(
                'type'      => 'B',
                'default'   => 'Y'),
            'optionid'      => array(
                'type'      => 'N',
                'array'     => true),
            'option'        => array(
                'array'     => true),
            'price_modifier'=> array(
                'array'     => true,
                'type'      => 'N',
                'default'   => 0.00),
            'modifier_type' => array(
                'array'     => true,
                'type'      => 'E',
                'variants'  => array("%","$"),
                'default'   => "$"),
            'option_orderby'=> array(
                'array'     => true,
                'type'      => 'N',
                'default'   => 0),
            'option_avail'  => array(
                'array'     => true,
                'type'      => 'B',
                'default'   => 'Y')
        )
    );

    $modules_import_specification['MULTILANGUAGE_PRODUCT_OPTIONS'] = array(
        'script'        => '/modules/Product_Options/import_lng.php',
        'permissions'   => 'AP',
        'need_provider' => true,
        'is_language'   => true,
        'parent'        => 'PRODUCT_OPTIONS',
        'export_sql'    => "SELECT $sql_tbl[classes].productid FROM $sql_tbl[classes], $sql_tbl[class_lng] WHERE $sql_tbl[classes].classid = $sql_tbl[class_lng].classid AND $sql_tbl[class_lng].code = '{{code}}' GROUP BY $sql_tbl[classes].productid",
        'table'         => 'classes',
        'key_field'     => 'productid',
        'columns'       => array(
            'productid'     => array(
                'type'      => 'N',
                'is_key'    => true,
                'default'   => 0),
            'productcode'   => array(
                'is_key'    => true),
            'product'       => array(
                'is_key'    => true),
            'code'          => array(
                'type'      => 'C',
                'array'     => true,
                'required'  => true),
            'class'         => array(
                'array'     => true,
                'required'  => true),
            'class_name'    => array(
                'array'     => true),
            'descr'         => array(
                'array'     => true)
        )
    );

    $modules_import_specification['MULTILANGUAGE_PRODUCT_OPTION_VALUES'] = array(
        'script'        => '/modules/Product_Options/import_option_lng.php',
        'permissions'   => 'AP',
        'need_provider' => true,
        'is_language'   => true,
        'parent'        => 'PRODUCT_OPTIONS',
        'export_sql'    => "SELECT $sql_tbl[classes].productid FROM $sql_tbl[classes], $sql_tbl[class_options], $sql_tbl[product_options_lng] WHERE $sql_tbl[classes].classid = $sql_tbl[class_options].classid AND $sql_tbl[product_options_lng].code = '{{code}}' AND $sql_tbl[product_options_lng].optionid = $sql_tbl[class_options].optionid GROUP BY $sql_tbl[classes].productid",
        'table'         => 'classes',
        'key_field'     => 'productid',
        'columns'       => array(
            'productid'     => array(
                'type'      => 'N',
                'is_key'    => true,
                'default'   => 0),
            'productcode'   => array(
                'is_key'    => true),
            'product'       => array(
                'is_key'    => true),
            'code'          => array(
                'type'      => 'C',
                'array'     => true,
                'required'  => true),
            'class'         => array(
                'array'     => true,
                'required'  => true),
            'option'        => array(
                'array'     => true,
                'required'  => true),
            'option_name'   => array(
                'array'     => true)
        )
    );

    $modules_import_specification['PRODUCT_VARIANTS'] = array(
        'script'        => '/modules/Product_Options/import_variants.php',
        'tpls'          => array(
            'main/import_option_images_directory.tpl'),
        'permissions'   => 'AP',
        'finalize'      => true,
        'need_provider' => true,
        'parent'        => 'PRODUCT_OPTIONS',
        'export_sql'    => "SELECT productid FROM $sql_tbl[classes] GROUP BY productid",
        'table'         => 'classes',
        'key_field'     => 'productid',
        'columns'       => array(
            'productid'     => array(
                'type'      => 'N',
                'is_key'    => true,
                'default'   => 0),
            'productcode'   => array(
                'is_key'    => true),
            'product'       => array(
                'is_key'    => true),
            'variantid'     => array(
                'is_key'    => true,
                'type'      => 'N',
                'default'   => 0),
            'variantcode'   => array(
                'is_key'    => true,
                'required'  => true),
            'weight'        => array(
                'type'      => 'N',
                'default'   => 0),
            'price'         => array(
                'type'      => 'P',
                'default'   => 0.00),
            'avail'         => array(
                'type'      => 'N',
                'default'   => 0),
            'default'       => array(
                'type'      => 'B',
                'default'   => 'N'),
            'image'         => array(
                'itype'     => 'W',
                'type'      => 'I'),
            'class'         => array(
                'required'  => true,
                'array'     => true),
            'option'        => array(
                'required'  => true,
                'array'     => true)
        )
    );

    $modules_import_specification['PRODUCT_OPTION_EXCEPTIONS'] = array(
        'script'        => '/modules/Product_Options/import_ex.php',
        'permissions'   => 'AP',
        'need_provider' => true,
        'parent'        => 'PRODUCT_OPTIONS',
        'export_sql'    => "SELECT productid FROM $sql_tbl[classes] GROUP BY productid",
        'table'         => 'classes',
        'key_field'     => 'productid',
        'columns'       => array(
            'productid'     => array(
                'type'      => 'N',
                'is_key'    => true,
                'default'   => 0),
            'productcode'   => array(
                'is_key'    => true),
            'product'       => array(
                'is_key'    => true),
            'exceptionid'   => array(
                'is_key'    => true,
                'type'      => 'N',
                'required'  => true),
            'optionid'      => array(
                'array'     => true,
                'type'      => 'N',
                'default'   => 0),
            'class'         => array(
                'array'     => true,
                'required'  => true),
            'option'        => array(
                'array'     => true,
                'required'  => true)
        )
    );

    $modules_import_specification['PRODUCT_OPTION_JSCRIPT'] = array(
        'script'        => '/modules/Product_Options/import_js.php',
        'permissions'   => 'AP',
        'need_provider' => true,
        'parent'        => 'PRODUCT_OPTIONS',
        'export_sql'    => "SELECT productid FROM $sql_tbl[product_options_js]",
        'table'         => 'product_options_js',
        'key_field'     => 'productid',
        'columns'       => array(
            'productid'     => array(
                'type'      => 'N',
                'default'   => 0),
            'productcode'   => array(),
            'product'       => array(),
            'jscript'       => array(
                'required'  => true),
        )
    );

}

if (defined('TOOLS')) {
    $tbl_keys['classes.productid'] = array(
        'keys' => array('classes.productid' => 'products.productid'),
        'fields' => array('classid','class')
    );
    $tbl_keys['class_lng.classid'] = array(
        'keys' => array('class_lng.classid' => 'classes.classid'),
        'fields' => array('code')
    );
    $tbl_keys['class_lng.code'] = array(
        'keys' => array('class_lng.code' => 'languages.code'),
        'fields' => array('classid'),
        'type' => 'W'
    );
    $tbl_keys['class_options.classid'] = array(
        'keys' => array('class_options.classid' => 'classes.classid'),
        'fields' => array('optionid','option_name')
    );
    $tbl_keys['product_options_lng.optionid'] = array(
        'keys' => array('product_options_lng.optionid' => 'class_options.optionid'),
        'fields' => array('code')
    );
    $tbl_keys['product_options_lng.code'] = array(
        'keys' => array('product_options_lng.code' => 'languages.code'),
        'fields' => array('optionid'),
        'type' => 'W'
    );
    $tbl_keys['product_options_ex.optionid'] = array(
        'keys' => array('product_options_ex.optionid' => 'class_options.optionid'),
        'fields' => array('exceptionid')
    );
    $tbl_keys['product_options_js.productid'] = array(
        'keys' => array('product_options_js.productid' => 'products.productid')
    );
    $tbl_keys['variants.variants'] = array(
        'keys' => array('variants.productid' => 'pricing.productid', 'variants.variantid' => 'pricing.variantid'),
        'on' => "pricing.quantity = '1' AND pricing.membershipid = '0'",
        'fields' => array('productcode')
    );
    $tbl_keys['variants.productid'] = array(
        'keys' => array('variants.productid' => 'products.productid'),
        'fields' => array('productcode')
    );
    $tbl_keys['variant_items.variantid'] = array(
        'keys' => array('variant_items.variantid' => 'variants.variantid'),
        'fields' => array('optionid')
    );
    $tbl_keys['variant_items.optionid'] = array(
        'keys' => array('variant_items.optionid' => 'class_options.optionid'),
        'fields' => array('variantid')
    );
    $tbl_keys['images_W.id'] = array(
        'keys' => array('images_W.id' => 'variants.variantid'),
        'fields' => array('imageid')
    );
    $tbl_keys['pricing.variants'] = array(
        'keys' => array('pricing.productid' => 'variants.productid','pricing.variantid' => 'variants.variantid'),
        'where' => "pricing.variantid != 0",
        'fields' => array('priceid')
    );
    $tbl_keys['quick_prices.variantid'] = array(
        'keys' => array(
            'quick_prices.variantid' => 'variants.variantid',
            'quick_prices.productid' => 'variants.productid'
        ),
        'where' => "quick_prices.variantid != 0",
        'fields' => array('productid','membershipid')
    );
    $tbl_demo_data['Product_Options'] = array(
        'classes' => '',
        'class_options' => '',
        'product_options_ex' => '',
        'product_options_js' => '',
        'class_lng' => '',
        'product_options_lng' => '',
        'variants' => '',
        'variant_items' => '',
        'images_W' => 'images'
    );
}
?>
