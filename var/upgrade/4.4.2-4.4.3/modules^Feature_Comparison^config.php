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
 * Global definitions for Feature Comparison module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: config.php,v 1.44.2.2 2011/01/10 13:11:56 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_SESSION_START')) { header("Location: ../../"); die("Access denied"); }

$addons['Feature_Comparison'] = true;

$sql_tbl['feature_classes'] = 'xcart_feature_classes';
$sql_tbl['feature_options'] = 'xcart_feature_options';
$sql_tbl['feature_variants'] = 'xcart_feature_variants';
$sql_tbl['feature_variants_lng'] = 'xcart_feature_variants_lng';
$sql_tbl['feature_classes_lng'] = 'xcart_feature_classes_lng';
$sql_tbl['feature_options_lng'] = 'xcart_feature_options_lng';
$sql_tbl['product_features'] = 'xcart_product_features';
$sql_tbl['product_foptions'] = 'xcart_product_foptions';
$sql_tbl['images_F'] = 'xcart_images_F';

$config['available_images']['F'] = "U";

$data_caches['fc_count'] = array("func" => "func_dc_fc_count");

$css_files['Feature_Comparison'][] = array();
$css_files['Feature_Comparison'][] = array('suffix' => 'IE6', 'browser' => 'MSIE', 'version' => '6.0');
$css_files['Feature_Comparison'][] = array('suffix' => 'IE7', 'browser' => 'MSIE', 'version' => '7.0');

if (defined('IS_IMPORT')) {

    $modules_import_specification['PRODUCT_CLASSES'] = array(
        'script'        => '/modules/Feature_Comparison/import.php',
        'permissions'    => 'AP',
        'need_provider'    => true,
        'export_sql'    => "SELECT fclassid FROM $sql_tbl[feature_classes]",
        'table'         => 'feature_classes',
        'key_field'     => 'fclassid',
        'columns'        => array(
            'fclassid'        => array(
                'type'        => 'N'),
            'class'            => array(
                'required'    => true),
            'avail'            => array(
                'type'        => 'B',
                'default'    => 'Y'),
            'image'            => array(
                'type'        => 'I',
                'itype'        => 'F'),
            'orderby'        => array(
                'type'        => 'N')
        )
    );

    $modules_import_specification['MULTILANGUAGE_PRODUCT_CLASSES'] = array(
        'script'        => '/modules/Feature_Comparison/import_lng.php',
        'permissions'    => 'AP',
        'need_provider'    => true,
        'is_language'    => true,
        'parent'        => 'PRODUCT_CLASSES',
        'export_sql'    => "SELECT fclassid FROM $sql_tbl[feature_classes_lng] WHERE code = '{{code}}'",
        'table'         => 'feature_classes_lng',
        'key_field'     => 'fclassid',
        'columns'        => array(
            'class'        => array(
                'is_key'    => true,
                'required'    => true),
            'code'            => array(
                'type'        => 'C',
                'array'        => true,
                'required'    => true),
            'class_name'    => array(
                'array'        => true,
                'required'    => true)
        )
    );

    $modules_import_specification['PRODUCT_CLASS_OPTIONS'] = array(
        'script'        => '/modules/Feature_Comparison/import_options.php',
        'permissions'    => 'AP',
        'need_provider'    => true,
        'parent'        => 'PRODUCT_CLASSES',
        'export_sql'    => "SELECT fclassid FROM $sql_tbl[feature_options] GROUP BY fclassid",
        'table'         => 'feature_options',
        'key_field'     => 'fclassid',
        'columns'        => array(
            'class'            => array(
                'is_key'    => true,
                'required'    => true),
            'optionid'        => array(
                'type'        => 'N',
                'is_key'    => true),
            'option'        => array(
                'is_key'    => true,
                'required'    => true),
            'option_hint'    => array(
                'is_key'    => false,
                'required'  => false),
            'show_in_search'=> array(
                'type'      => 'B',
                'default'   => 'Y'),
            'type'            => array(
                'type'        => 'E',
                'variants'    => array('T','S','N','B','D','M'),
                'default'    => 'T'),
            'format'        => array(),
            'variants'        => array(
                'array'        => true),
            'avail'            => array(
                'type'        => 'B',
                'default'    => 'Y'),
            'orderby'        => array(
                'type'        => 'N')
        )
    );

    $modules_import_specification['MULTILANGUAGE_PRODUCT_CLASS_OPTIONS'] = array(
        'script'        => '/modules/Feature_Comparison/import_options_lng.php',
        'permissions'    => 'AP',
        'need_provider'    => true,
        'is_language'    => true,
        'parent'        => 'PRODUCT_CLASS_OPTIONS',
        'export_sql'    => "SELECT $sql_tbl[feature_options].fclassid FROM $sql_tbl[feature_options], $sql_tbl[feature_options_lng] WHERE $sql_tbl[feature_options].foptionid = $sql_tbl[feature_options_lng].foptionid AND $sql_tbl[feature_options_lng].code = '{{code}}' GROUP BY $sql_tbl[feature_options].fclassid",
        'table'         => 'feature_options',
        'key_field'     => 'fclassid',
        'columns'        => array(
            'class'        => array(
                'is_key'    => true,
                'required'    => true),
            'option'        => array(
                'array'        => true,
                'is_key'    => true,
                'required'    => true),
            'code'            => array(
                'required'    => true,
                'type'        => 'C',
                'array'        => true),
            'option_name'    => array(
                'array'     => true,
                'required'    => true),
            'option_hint'   => array(
                'array'     => true,
                'required'  => false)
        )
    );

    $modules_import_specification['PRODUCT_FEATURE_VALUES'] = array(
        'script'        => '/modules/Feature_Comparison/import_values.php',
        'permissions'    => 'AP',
        'need_provider'    => true,
        'parent'        => 'PRODUCTS',
        'export_sql'    => "SELECT productid FROM $sql_tbl[product_foptions] GROUP BY productid",
        'table'         => 'product_foptions',
        'key_field'     => 'productid',
        'columns'        => array(
            'productid'        => array(
                'is_key'    => true,
                'type'        => 'N',
                'default'    => 0),
            'productcode'    => array(
                'is_key'    => true),
            'product'        => array(
                'is_key'    => true),
            'class'            => array(
                'is_key'    => true,
                'required'    => true),
            'option'        => array(
                'array'     => true,
                'required'    => true),
            'value'            => array(
                'array'     => true)
        )
    );

}

if (defined('TOOLS')) {
    $tbl_keys['product_features.productid'] = array(
        'keys' => array('product_features.productid' => 'products.productid'),
        'fields' => array('fclassid')
    );
    $tbl_keys['product_features.fclassid'] = array(
        'keys' => array('product_features.fclassid' => 'feature_classes.fclassid'),
        'fields' => array('productid')
    );
    $tbl_keys['feature_options.fclassid'] = array(
        'keys' => array('feature_options.fclassid' => 'feature_classes.fclassid'),
        'fields' => array('option_name')
    );
    $tbl_keys['feature_classes_lng.fclassid'] = array(
        'keys' => array('feature_classes_lng.fclassid' => 'feature_classes.fclassid'),
        'fields' => array('code')
    );
    $tbl_keys['feature_classes_lng.code'] = array(
        'keys' => array('feature_classes_lng.code' => 'languages.code'),
        'fields' => array('fclassid'),
        'type'    => 'W'
    );
    $tbl_keys['feature_options_lng.foptionid'] = array(
        'keys' => array('feature_options_lng.foptionid' => 'feature_options.foptionid'),
        'fields' => array('code'),
        'type' => 'W'
    );
    $tbl_keys['feature_options_lng.code'] = array(
        'keys' => array('feature_options_lng.code' => 'languages.code'),
        'fields' => array('foptionid')
    );
    $tbl_keys['product_foptions.productid'] = array(
        'keys' => array('product_foptions.productid' => 'products.productid'),
        'fields' => array('foptionid')
    );
    $tbl_keys['product_foptions.foptionid'] = array(
        'keys' => array('product_foptions.foptionid' => 'feature_options.foptionid'),
        'fields' => array('productid')
    );
    $tbl_keys['images_F.id'] = array(
        'keys' => array('images_F.id' => 'feature_classes.fclassid'),
        'fields' => array('imageid')
    );
    $tbl_demo_data['Feature_Comparison'] = array(
        'feature_classes' => '',
        'feature_options' => '',
        'feature_classes_lng' => '',
        'feature_options_lng' => '',
        'product_features' => '',
        'product_foptions' => '',
        'images_F' => 'images'
    );
}
?>
