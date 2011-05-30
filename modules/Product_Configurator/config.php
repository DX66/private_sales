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
 * @version    $Id: config.php,v 1.58.2.3 2011/04/22 12:14:17 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */
if ( !defined('XCART_START') ) { header('Location: ../../'); die('Access denied'); }
/**
 * Global definitions for X-Configurator module
 */

$addons['Product_Configurator'] = true;

$css_files['Product_Configurator'] = array(
    array(
    ),
    array(
        'suffix'  => 'IE6',
        'browser' => 'MSIE',
        'version' => '6.0',
    ),
    array(
        'suffix'  => 'Opera',
        'browser' => 'Opera',
    )
);

$sql_tbl['pconf_product_types']         = 'xcart_pconf_product_types';
$sql_tbl['pconf_specifications']         = 'xcart_pconf_specifications';
$sql_tbl['pconf_products_classes']         = 'xcart_pconf_products_classes';
$sql_tbl['pconf_class_specifications']     = 'xcart_pconf_class_specifications';
$sql_tbl['pconf_class_requirements']     = 'xcart_pconf_class_requirements';
$sql_tbl['pconf_wizards']                 = 'xcart_pconf_wizards';
$sql_tbl['pconf_slots']                 = 'xcart_pconf_slots';
$sql_tbl['pconf_slot_rules']             = 'xcart_pconf_slot_rules';
$sql_tbl['pconf_slot_markups']             = 'xcart_pconf_slot_markups';

$language_var_names['step_name']     = 'pconf_stepname_';
$language_var_names['step_descr']     = 'pconf_stepdescr_';
$language_var_names['slot_name']     = 'pconf_slotname_';
$language_var_names['slot_descr']     = 'pconf_slotdescr_';

/**
 * Product image width on Summary and Configuration step pages, px
 */
$pconf_summary_image_width = 60;

$pconf_slot_data_image_width = 75;

/**
 * Checking of bidirectional requirements is turned on
 */
$check_bidirectional_requirements = 'Y';

/**
 * Use 'OR' rule while checking of the product type requirements (else 'AND' rule will be used)
 */
$check_product_type_requirements_by_or = 'Y';

/**
 * This option allows to add a configured product with empty slots to the
 * shopping cart.
 * The slots can be empty if at the stage of setting the Configuration Wizard
 * for this product no slots were defined as required, but there were several
 * optional slots.
 */
$config['Product_Configurator']['allow_to_add_empty_product_to_cart'] = 'Y';

if (defined('IS_IMPORT')) {

    $modules_import_specification['PRODUCTS']['columns']['product_type'] = array(
        'default'  => 'N',
    );

    $modules_import_specification['PRODUCT_CONFIGURATOR_TYPES'] = array(
        'script'        => '/modules/Product_Configurator/import.php',
        'permissions'    => 'AP',
        'need_provider'    => true,
        'export_sql'    => "SELECT ptypeid FROM $sql_tbl[pconf_product_types]",
        'table'         => 'pconf_product_types',
        'key_field'     => 'ptypeid',
        'orderby'       => 30,
        'columns'        => array(
            'typeid'        => array(
                'type'        => 'N',
                'is_key'    => true),
            'type'            => array(
                'is_key'    => true,
                'required'    => true),
            'orderby'        => array(
                'type'        => 'N'),
            'specid'        => array(
                'array'        => true,
                'type'        => 'N'),
            'specification'    => array(
                'array'        => true),
            'spec_orderby'    => array(
                'array'        => true,
                'type'        => 'N')
        )
    );

    $modules_import_specification['PRODUCT_CONFIGURATOR_CLASSES'] = array(
        'script'        => '/modules/Product_Configurator/import_classes.php',
        'permissions'    => 'AP',
        'need_provider'    => true,
        'parent'        => 'PRODUCTS',
        'export_sql'    => "SELECT productid FROM $sql_tbl[pconf_products_classes] GROUP BY productid",
        'table'         => 'pconf_products_classes',
        'key_field'     => 'productid',
        'columns'        => array(
            'classid'        => array(
                'is_key'    => true,
                'type'        => 'N'),
            'productid'        => array(
                'is_key'    => true,
                'type'        => 'N',
                'default'    => 0),
            'productcode'    => array(
                'is_key'    => true),
            'product'        => array(
                'is_key'    => true),
            'type'            => array(
                'is_key'    => true,
                'required'    => true),
            'specifications'=> array(
                'array'        => true),
            'required_types'=> array(
                'array'     => true),
            'required_specs'=> array(
                'array'     => true)
        )
    );

    $modules_import_specification['PRODUCT_CONFIGURATOR_STEPS'] = array(
        'script'        => '/modules/Product_Configurator/import_steps.php',
        'permissions'    => 'AP',
        'need_provider'    => true,
        'parent'        => 'PRODUCTS',
        'export_sql'    => "SELECT productid FROM $sql_tbl[pconf_wizards] GROUP BY productid",
        'table'         => 'pconf_wizards',
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
            'stepid'        => array(
                'array'        => true,
                'required'    => true),
            'orderby'        => array(
                'type'        => 'N',
                'array'     => true)
        )
    );

    $modules_import_specification['MULTILANGUAGE_PRODUCT_CONFIGURATOR_STEPS'] = array(
        'script'        => '/modules/Product_Configurator/import_steps_lng.php',
        'permissions'   => 'AP',
        'need_provider' => true,
        'parent'        => 'PRODUCT_CONFIGURATOR_STEPS',
        'is_language'    => true,
        'export_sql'    => "SELECT SUBSTRING(name, 16) as stepid FROM $sql_tbl[languages_alt] WHERE code = '{{code}}' AND name LIKE '".$language_var_names["step_name"]."%'",
        'table'         => 'pconf_wizards',
        'key_field'     => 'stepid',
        'columns'       => array(
            'stepid'    => array(
                'is_key'    => true,
                'type'      => 'N',
                'required'    => true),
            'code'        => array(
                'array'        => true,
                'type'        =>    'C',
                'required'    => true),
            'step'        => array(
                'array'        => true),
            'descr'        => array(
                'array'        => true)
        )
    );

    $modules_import_specification['PRODUCT_CONFIGURATOR_SLOTS'] = array(
        'script'        => '/modules/Product_Configurator/import_slots.php',
        'permissions'    => 'AP',
        'need_provider'    => true,
        'parent'        => 'PRODUCT_CONFIGURATOR_STEPS',
        'export_sql'    => "SELECT productid FROM $sql_tbl[pconf_wizards] GROUP BY productid",
        'table'         => 'pconf_wizards',
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
            'stepid'        => array(
                'type'        => 'N',
                'is_key'    => true,
                'required'    => true),
            'slotid'        => array(
                'is_key'    => true,
                'required'    => true,
                'type'      => 'N'),
            'status'        => array(
                'type'        => 'E',
                'variants'    => array('O','M','N')),
            'multiple'         => array(
                'type'      => 'B',
                'default'   => ''),
            'amount_min'    => array(
                'type'        => 'N',
                'default'    => 1),
            'amount_max'    => array(
                'type'        => 'N',
                'default'    => 1),
            'default_amount'=> array(
                'type'        => 'N',
                'default'    => 1),
            'default_productid'        => array(
                'type'        => 'N',
                'default'    => 0),
            'default_productcode'    => array(),
            'default_product'        => array(),
            'orderby'        => array(
                'type'        => 'N'),
            'rule_types'    => array(
                'array'     => true),
            'markup'        => array(
                'array'     => true,
                'type'        => 'N'),
            'markup_type'    => array(
                'array'     => true,
                'type'        => 'E',
                'variants'    => array("$","%")),
            'membership'    => array(
                'array'     => true)
        )
    );

    $modules_import_specification['MULTILANGUAGE_PRODUCT_CONFIGURATOR_SLOTS'] = array(
        'script'        => '/modules/Product_Configurator/import_slots_lng.php',
        'permissions'    => 'AP',
        'need_provider'    => true,
        'parent'        => 'PRODUCT_CONFIGURATOR_SLOTS',
        'is_language'    => true,
        'export_sql'    => "SELECT SUBSTRING(name, 16) as slotid FROM $sql_tbl[languages_alt] WHERE code = '{{code}}' AND name LIKE '".$language_var_names["slot_name"]."%'",
        'table'            => 'pconf_slots',
        'key_field'        => 'slotid',
        'columns'        => array(
            'slotid'    => array(
                'is_key'    => true,
                'type'        => 'N',
                'required'    => true),
            'code'        => array(
                'array'        => true,
                'type'        =>    'C',
                'required'    => true),
            'slot'        => array(
                'array'        => true),
            'descr'        => array(
                'array'        => true)
        )
    );

}

if (defined('TOOLS')) {
    $tbl_keys['pconf_product_types.provider'] = array(
        'keys' => array('pconf_product_types.provider' => 'customers.id'),
        'where' => "customers.usertype IN ('A','P')",
        'fields' => array('ptypeid','ptype_name')
    );
    $tbl_keys['pconf_specifications.ptypeid'] = array(
        'keys' => array('pconf_specifications.ptypeid' => 'pconf_product_types.ptypeid'),
        'where' => "pconf_product_types.ptypeid != '0'",
        'fields' => array('specid','spec_name')
    );
    $tbl_keys['pconf_products_classes.productid'] = array(
        'keys' => array('pconf_products_classes.productid' => 'products.productid'),
        'fields' => array('classid','ptypeid')
    );
    $tbl_keys['pconf_products_classes.ptypeid'] = array(
        'keys' => array('pconf_products_classes.ptypeid' => 'pconf_product_types.ptypeid'),
        'fields' => array('classid','productid')
    );
    $tbl_keys['pconf_class_specifications.classid'] = array(
        'keys' => array('pconf_class_specifications.classid' => 'pconf_products_classes.classid'),
        'fields' => array('specid')
    );
    $tbl_keys['pconf_class_specifications.specid'] = array(
        'keys' => array('pconf_class_specifications.specid' => 'pconf_specifications.specid'),
        'fields' => array('classid')
    );
    $tbl_keys['pconf_class_requirements.classid'] = array(
        'keys' => array('pconf_class_requirements.classid' => 'pconf_products_classes.classid'),
        'fields' => array('ptypeid','specid')
    );
    $tbl_keys['pconf_class_requirements.specid'] = array(
        'keys' => array('pconf_class_requirements.specid' => 'pconf_specifications.specid'),
        'where' => "pconf_class_requirements.specid != '0'",
        'fields' => array('classid','ptypeid')
    );
    $tbl_keys['pconf_class_requirements.ptypeid'] = array(
        'keys' => array('pconf_class_requirements.ptypeid' => 'pconf_product_types.ptypeid'),
        'fields' => array('classid','specid')
    );
    $tbl_keys['pconf_wizards.productid'] = array(
        'keys' => array('pconf_wizards.productid' => 'products.productid'),
        'fields' => array('stepid','step_name')
    );
    $tbl_keys['pconf_slots.stepid'] = array(
        'keys' => array('pconf_slots.stepid' => 'pconf_wizards.stepid'),
        'fields' => array('slotid','slot_name')
    );
    $tbl_keys['pconf_slot_rules.slotid'] = array(
        'keys' => array('pconf_slot_rules.slotid' => 'pconf_slots.slotid'),
        'fields' => array('ptypeid')
    );
    $tbl_keys['pconf_slot_rules.ptypeid'] = array(
        'keys' => array('pconf_slot_rules.ptypeid' => 'pconf_product_types.ptypeid'),
        'fields' => array('slotid')
    );
    $tbl_keys['pconf_slot_markups.slotid'] = array(
        'keys' => array('pconf_slot_markups.slotid' => 'pconf_slots.slotid'),
        'fields' => array('markupid','markup','markup_type')
    );
    $tbl_keys['pconf_slot_markups.membershipid'] = array(
        'keys' => array('pconf_slot_markups.membershipid' => 'memberships.membershipid'),
        'where' => "memberships.area = 'C' AND pconf_slot_markups.membershipid != '0'",
        'fields' => array('slotid','markupid','markup','markup_type')
    );
    $tbl_demo_data['Product_Configurator'] = array(
        'pconf_product_types' => '',
        'pconf_specifications' => '',
        'pconf_products_classes' => '',
        'pconf_class_specifications' => '',
        'pconf_class_requirements' => '',
        'pconf_wizards' => '',
        'pconf_slots' => '',
        'pconf_slot_rules' => '',
        'pconf_slot_markups' => ''
    );
}

$_module_dir  = $xcart_dir . XC_DS . 'modules' . XC_DS . 'Product_Configurator';
/*
 Load module functions
*/
if (!empty($include_func))
    require_once $_module_dir . XC_DS . 'func.php';
?>
