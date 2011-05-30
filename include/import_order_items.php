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
 * Import/export ordered items
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import_order_items.php,v 1.22.2.1 2011/01/10 13:11:49 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if ($import_step == 'define') {

    $import_specification['ORDER_ITEMS'] = array(
        'script'        => '/include/import_order_items.php',
        'no_import'     => true,
        'permissions'   => 'AP',
        'parent'        => 'ORDERS',
        'export_sql'    => "SELECT itemid FROM $sql_tbl[order_details]",
        'table'         => 'order_details',
        'allow_fullfillment' => true,
        'key_field'     => 'itemid',
        'columns'       => array(
            'orderid'       => array(
                'is_key'    => true,
                'required'  => true,
                'type'      => 'N'),
            'itemid'        => array(
                'is_key'    => true,
                'required'  => true,
                'type'      => 'N'),
            'productid'     => array(
                'type'      => 'N'),
            'productcode'   => array(),
            'product'       => array(),
            'price'         => array(
                'type'      => 'P'),
            'amount'        => array(
                'type'      => 'N'),
            'provider'      => array(),
            'product_class' => array(
                'array'     => true),
            'product_class_option' => array(
                'array'     => true),
            'extra_data'    => array()
        )
    );

// Export data
} elseif ($import_step == 'export') {

    while ($id = func_export_get_row($data)) {
        if (empty($id))
            continue;

        if ($single_mode || AREA_TYPE == 'A') {
            $row = func_query_first("SELECT * FROM $sql_tbl[order_details] WHERE itemid = '$id'");
        } else {
            $row = func_query_first("SELECT * FROM $sql_tbl[order_details] WHERE itemid = '$id' AND $sql_tbl[order_details].provider = '$logged_userid'");
        }
        if (empty($row))
            continue;

        // Export product options
        if ($row['product_options']) {
            $tmp = explode("\n", $row['product_options']);
            foreach ($tmp as $v) {
                $pos = strpos($v, ": ");
                if ($pos !== false) {
                    $row['product_class'][] = substr($v, 0, $pos);
                    $row['product_class_option'][] = substr($v, $pos+2);
                }
            }
        }

        if (!func_export_write_row($row))
            break;
    }

}

?>
