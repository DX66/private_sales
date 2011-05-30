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
 * @version    $Id: config.php,v 1.23.2.1 2011/01/10 13:11:56 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }
/**
 * Global definitions for Discount Coupons module
 */

$css_files['Discount_Coupons'][] = array();

if (defined('IS_IMPORT')) {
    $modules_import_specification['DISCOUNT_COUPONS'] = array(
        'script'        => '/modules/Discount_Coupons/import.php',
        'tpl'              => array(
            'main/import_option_category_path_sep.tpl'),
        'permissions'    => 'AP',
        'need_provider'    => true,
        'export_sql'    => "SELECT coupon FROM $sql_tbl[discount_coupons]",
        'orderby'        => 90,
        'columns'        => array(
            'coupon'        => array(
                'required'    => true),
            'discount'        => array(
                'type'        => 'N',
                'default'    => 0.00),
            'coupon_type'    => array(
                'required'    => true),
            'productid'        => array(
                'type'        => 'N',
                'default'    => 0),
            'productcode'    => array(),
            'product'        => array(),
            'categoryid'    => array(
                'type'        => 'N',
                'default'    => 0),
            'category'        => array(),
            'recursive'        => array(
                'type'        => 'B'),
            'minimum'        => array(
                'type'        => 'P',
                'default'    => 0.00),
            'times'            => array(
                'required'    => true,
                'type'        => 'N'),
            'per_user'  => array(
                'type'      => 'B',
                'default'   => 'N'),
            'times_used'    => array(
                'type'        => 'N'),
            'expire'        => array(
                'required'    => true,
                'type'        => 'D'),
            'status'        => array(
                'type'        => 'E',
                'variants'    => array('A','D','U')),
            'apply_category_once'  => array(
                'type'      => 'B',
                'default'   => 'N'),
            'apply_product_once'  => array(
                'type'      => 'B',
                'default'   => 'N'),
        )
    );
}

if (defined('TOOLS')) {
    $tbl_keys['discount_coupons.productid'] = array(
        'keys' => array('discount_coupons.productid' => 'products.productid'),
        'where' => "discount_coupons.productid != 0",
        'fields' => array('coupon')
    );
    $tbl_keys['discount_coupons.categoryid'] = array(
        'keys' => array('discount_coupons.categoryid' => 'categories.categoryid'),
        'where' => "discount_coupons.categoryid != 0",
        'fields' => array('coupon')
    );
    $tbl_keys['discount_coupons.provider'] = array(
        'keys' => array('discount_coupons.provider' => 'customers.id'),
        'where' => "customers.usertype IN ('A','P')",
        'fields' => array('coupon')
    );
    $tbl_demo_data['Discount_Coupons'] = array(
        'discount_coupons' => '',
        'discount_coupons_login' => ''
    );
}

?>
