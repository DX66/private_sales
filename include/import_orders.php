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
 * Orders import/export library
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import_orders.php,v 1.30.2.2 2011/03/30 11:46:07 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if ($import_step == 'define') {

    $import_specification['ORDERS'] = array(
        'script'        => '/include/import_orders.php',
        'no_import'        => true,
        'permissions'    => 'AP',
//        'tpls'            => array(
//            'main/import_option_order_details_crypt.tpl'),
        'is_range'        => "orders.php?is_range",
        'export_sql'    => "SELECT orderid FROM $sql_tbl[orders]",
        'orderby'        => 80,
        'table'            => 'orders',
        'key_field'        => 'orderid',
        'allow_fullfillment' => true,
        'columns'        => array(
            'orderid'                => array(
                'required'    => true,
                'is_key'    => true,
                'type'        => 'N'),
            'login'                    => array(
                'required'  => true),
            'membership'            => array(),
            'total'                    => array(
                'type'      => 'P',
                'required'  => true),
            'giftcert_discount'        => array(
                'type'      => 'P'),
            'applied_giftcert_id'    => array(
                'array'        => true),
            'applied_giftcert_cost'    => array(
                'array'        => true,
                'type'        => 'P'),
            'subtotal'                => array(
                'type'      => 'P',
                'required'  => true),
            'discount'                => array(
                'type'      => 'P'),
            'coupon'                => array(),
            'coupon_discount'        => array(
                'type'      => 'P'),
            'shippingid'            => array(
                'type'      => 'N'),
            'tracking'                => array(),
            'shipping_cost'            => array(
                'type'      => 'P'),
            'tax'                    => array(
                'type'      => 'P'),
            'taxes_applied'            => array(),
            'date'                    => array(
                'is_key'    => true,
                'type'        => 'D',
                'required'  => true),
            'status'                => array(
                'type'        => 'E',
                'variants'    => array('I','Q','P','C','F','D','B'),
                'default'    => 'Q'),
            'payment_method'        => array(
                'required'  => true),
            'flag'                    => array(
                'type'        => 'B',
                'default'    => 'N'),
            'customer_notes'        => array(),
            'notes'                    => array(),
            'details'                => array(),
            'clickid'                => array(
                'type'        => 'N'),
            'b_title'                => array(),
            'b_firstname'            => array(),
            'b_lastname'            => array(),
            'b_address'                => array(),
            'b_address_2'            => array(),
            'b_city'                => array(),
            'b_county'                => array(),
            'b_state'                => array(),
            'b_country'                => array(),
            'b_zipcode'                => array(),
            'b_phone'                  => array(),
            'b_fax'                    => array(),
            'title'                    => array(),
            'firstname'                => array(),
            'lastname'                => array(),
            'company'                => array(),
            's_title'                => array(),
            's_firstname'            => array(),
            's_lastname'            => array(),
            's_address'                => array(),
            's_address_2'            => array(),
            's_city'                => array(),
            's_county'                => array(),
            's_state'                => array(),
            's_country'                => array(),
            's_zipcode'                => array(),
            's_phone'                  => array(),
            's_fax'                    => array(),
            'email'                    => array(),
            'url'                    => array(),
            'tax_number'            => array(),
            'tax_exempt'            => array(
                'type'        => 'B'),
            'language'                => array(
                'type'        => 'C'),
            'extra_field'            => array(
                'array'        => true),
            'extra_value'            => array(
                'array'        => true)
        )
    );
}
elseif ($import_step == 'export') {

    // Export data
    while ($id = func_export_get_row($data)) {
        if (empty($id))
            continue;

        // Get data
        if ($single_mode || AREA_TYPE == 'A') {
            $row = func_query_first("SELECT * FROM $sql_tbl[orders] WHERE orderid = '$id'");
        } else {
            $row = func_query_first("SELECT $sql_tbl[orders].*, $sql_tbl[customers].login FROM $sql_tbl[orders], $sql_tbl[order_details] LEFT JOIN $sql_tbl[customers] ON $sql_tbl[orders].userid = $sql_tbl[customers].id WHERE $sql_tbl[orders].orderid = '$id' AND $sql_tbl[orders].orderid = $sql_tbl[order_details].orderid AND $sql_tbl[order_details].provider = '$logged_userid'");
        }

        if (empty($row))
            continue;

        list($row['b_address'], $row['b_address_2']) = preg_split("/[\r\n]+/", $row['b_address'], 2);
        list($row['s_address'], $row['s_address_2']) = preg_split("/[\r\n]+/", $row['s_address'], 2);

        $row['b_zipcode'] .= $row['b_zip4'];
        $row['s_zipcode'] .= $row['s_zip4'];

        // Time zone offset correction
        $row['date'] += $config['Appearance']['timezone_offset'];

        // Export applied gift certificates
        $row['applied_giftcert_id'] = array();
        $row['applied_giftcert_cost'] = array();
        if (!empty($row['giftcert_ids']) && ($single_mode || AREA_TYPE == 'A')) {
            $tmp = explode("*", $row['giftcert_ids']);
            foreach ($tmp as $v) {
                if (empty($v))
                    continue;

                list($gid, $gcost) = explode(":", $v);
                if (empty($gid) || empty($gcost))
                    continue;

                $row['applied_giftcert_id'][] = $gid;
                $row['applied_giftcert_cost'][] = $gcost;
            }
        }

        func_unset($row, 'giftcert_ids');

        if ($single_mode || AREA_TYPE == 'A') {
            $ctype = func_get_crypt_type($row['details']);
            if (in_array($ctype, array('N', 'B')) || ($ctype == 'C' && func_get_crypt_key($ctype))) {
                $row['details'] = text_decrypt($row['details']);
                $row['details'] = (string)$row['details'];

            } else {
                $row['details'] = '';
            }

            // Export extra fields
            $ef = func_query("SELECT khash, value FROM $sql_tbl[order_extras] WHERE orderid = '$id'");
            if (!empty($ef)) {
                foreach ($ef as $v) {
                    $row['extra_field'][] = $v['khash'];
                    $row['extra_value'][] = $v['value'];
                }
            }
            unset($ef);

        } else {

            unset($row['details']);
            unset($row['clickid']);
        }

        // Export row
        if (!func_export_write_row($row))
            break;
    }
}

?>
