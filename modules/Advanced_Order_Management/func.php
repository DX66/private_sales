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
 * Functions related to the Advanced Order Management module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.php,v 1.25.2.1 2011/01/10 13:11:54 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

/**
 * Prepare the data containing the modified fields
 */

function func_aom_prepare_diff($type, $new_data, $old_data, $extra = false)
{
    if ($type=="A") {
        // Get products changes
        if (!empty($extra['products'])) {
            $diff['P'] = func_aom_get_products_diff($extra['products'], $old_data['products']);
        }

        // Get GC changes
        if (!empty($extra['giftcerts'])) {
            $diff['G'] = func_aom_get_gc_diff($extra['giftcerts'], $old_data['giftcerts']);
        }

        // Get totals changes
        $totals_fields = array(
            'total',
            'subtotal',
            'discount',
            'shipping_cost',
            'tax',
            'payment_method',
            'shipping',
            'coupon_discount',
            'coupon',
        );

        foreach ($totals_fields as $field) {
            $data_t[$field] = $new_data['order'][$field];
        }

        $diff['T'] = array_diff_assoc($data_t, $old_data['order']);

        unset ($data_t);

        // Get customer information changes
        $profile_fields = array_keys(func_get_default_fields('C'));
        $profile_fields[] = 'membershipid';

        foreach($profile_fields as $field) {
            if ($new_data['userinfo'][$field])
                $data_u[$field] = $new_data['userinfo'][$field];
        }

        $diff['U'] = array_diff_assoc($data_u, $old_data['userinfo']);

        unset ($data_u);

    } else {

        $diff[$type] = array_diff_assoc($new_data, $old_data);

    }

    // Unset empty sections
    foreach (array_keys($diff) as $section) {
        if (empty($diff[$section]))
            func_unset($diff,$section);
    }

    return $diff;
}

/**
 * Common function that writes order changes to the history
 */
// type: relation to the module / processor
//    X - status and/or common details changed
//    A - order details changed in X-AOM
//    R - order details changed in X-RMA
function func_aom_save_history($orderid, $type = 'X', $details)
{
    global $config, $sql_tbl, $logged_userid;

    $details['type'] = $type;

    if (
        $type == 'X'
        && defined('STATUS_CHANGE_REF')
    ) {
        $details['reference'] = constant('STATUS_CHANGE_REF');
    }

    $insert_data = array (
        'orderid'     =>     $orderid,
        'userid'     =>     $logged_userid,
        'date_time' =>     XC_TIME,
        'details'     =>     addslashes(serialize($details))
    );

    return func_array2insert('order_status_history', $insert_data);
}

/**
 * Function gets information about order changes
 */
function func_aom_get_history($orderid)
{
    global $config, $sql_tbl, $active_modules;

    $history = array();

    $records = func_query("SELECT osh.*, c.login FROM $sql_tbl[order_status_history] as osh LEFT JOIN $sql_tbl[customers] as c ON osh.userid = c.id WHERE orderid='$orderid' ORDER BY date_time DESC");

    if (!empty($records)) {

        foreach($records as $k => $rec) {

            $tmp = $rec['details'] = unserialize($rec['details']);

            if (isset($tmp['reference'])) {
                $rec['status_note'] = func_get_langvar_by_name('lbl_aom_order_status_note_' . $tmp['reference']);
            }

            if (
                $tmp['type'] == 'X'
                && $tmp['old_status'] != $tmp['new_status']
            ) {
                $rec['event_header'] = empty($tmp['old_status'])
                    ? func_get_langvar_by_name('lbl_aom_order_placement_' . $tmp['new_status'])
                    : func_get_langvar_by_name(
                        'lbl_aom_order_status_changed_from_to',
                        array(
                            'old' => func_aom_get_order_status($tmp['old_status']),
                            'new' => func_aom_get_order_status($tmp['new_status'])
                        )
                    );
            }

            $history[$k] =  $rec;
        }

    }

    return $history;
}

/**
 * Function compares old and new products
 */
function func_aom_get_products_diff($new_products, $old_products)
{
    $diff = array();

    foreach ($new_products as $k => $v) {

        $changed = (
            $v['deleted']
            || $v['new']
            || $v['price'] != $old_products[$k]['price']
            || $v['amount'] != $old_products[$k]['amount']
        );

        if ($changed) {
            $diff[] = array(
                'deleted'         => $v['deleted'],
                'new'             => $v['new'],
                'old_price'     => price_format($old_products[$k]['price']),
                'price'         => price_format($v['price']),
                'old_amount'     => $old_products[$k]['amount'],
                'amount'         => $v['amount'],
                'productcode'     => $v['productcode'],
                'product'         => $v['product'],
            );
        }

    }

    return $diff;
}

/**
 * Function compares old and new Gift certificates
 */
function func_aom_get_gc_diff($new_gc, $old_gc)
{
    $diff = array();

    foreach ($new_gc as $k => $v) {

        $changed = (
            $v['deleted']
            || $v['amount'] != $old_gc[$k]['amount']
        );

        if ($changed) {
            $diff[] = array(
                'deleted'         => $v['deleted'],
                'old_amount'     => price_format($old_gc[$k]['amount']),
                'amount'         => price_format($v['amount']),
                'gcid'             => $v['gcid'],
            );
        }

    }

    return $diff;
}

/**
 * Get default field's name label
 */
function func_aom_get_field_name($name)
{
    $add = '';
    $prefix = substr($name, 0, 2);

    if (
        $prefix == 's_'
        || $prefix == 'b_'
    ) {
        $add = " (" . func_get_langvar_by_name('lbl_aom_' . $prefix . 'prefix') . ")";

        $name = substr($name, 2);
    }

    if (!in_array($name, array('customer_notes'))) {
        $name = str_replace(
            array(
                'firstname',
                'lastname',
                'zipcode',
                'membershipid',
                'notes',
                'tracking',
            ),
            array(
                'first_name',
                'last_name',
                'zip_code',
                'membership',
                'order_notes',
                'tracking_number',
            ),
            $name
        );
    }

    return func_get_langvar_by_name('lbl_' . $name) . $add;
}

/**
 * With no parameter returns a hash array with order statuses definitions,
 * otherwise returns a status definition
 */
function func_aom_get_order_status($status = false)
{
    global $sql_tbl;

    $statuses = array(
        'I' => func_get_langvar_by_name('lbl_not_finished'),
        'Q' => func_get_langvar_by_name('lbl_queued'),
        'A' => func_get_langvar_by_name('lbl_pre_authorized'),
        'P' => func_get_langvar_by_name('lbl_processed'),
        'D' => func_get_langvar_by_name('lbl_declined'),
        'B' => func_get_langvar_by_name('lbl_backordered'),
        'F' => func_get_langvar_by_name('lbl_failed'),
        'C' => func_get_langvar_by_name('lbl_complete'),
    );

    return ($status && isset($statuses[$status]))
        ? $statuses[$status]
        : $statuses;
}

/**
 * Replace current rate value to saved value from order detailes
 * Rate is identified by tax_name and taxid bt:0095797
 */
function func_aom_tax_rates_replace($productid, $current_tax, $current_tax_rate)
{
    global $global_store, $sql_tbl;
    $global_store_taxes = $global_store['product_taxes'];

    if (!isset($global_store_taxes[$productid]) || !is_array($global_store_taxes[$productid]))
        return $current_tax_rate;

    foreach ($global_store_taxes[$productid] as $aom_tax_name => $aom_tax) {
        if (
            $aom_tax_name == $current_tax['tax_name']
            && $aom_tax['taxid'] == $current_tax['taxid']
        ) {
            $current_tax_rate['formula'] = $aom_tax['formula'];
            $current_tax_rate['rate_value'] = $aom_tax['rate_value'];
            $current_tax_rate['rate_type'] = $aom_tax['rate_type'];
            $current_tax_rate['tax_display_name'] = $global_store['tax_display_names'][$aom_tax_name];
            break;
        }
    }

    return $current_tax_rate;
}

?>
