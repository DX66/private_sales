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
 * Checkout by Amazon
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: order_notifications.php,v 1.9.2.10 2011/04/08 09:59:18 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

x_load('order');
$type = key($parsed);

func_acheckout_debug("\t+ Sub-message: $type");
$amazon_oid = func_array_path($parsed, "$type/PROCESSEDORDER/AMAZONORDERID/0/#");
func_acheckout_debug("\t+ \$amazon_oid: $amazon_oid");

if ($type == 'NEWORDERNOTIFICATION') {

    if (func_query_first_cell("SELECT COUNT(amazon_oid) FROM $sql_tbl[amazon_orders] WHERE amazon_oid='$amazon_oid'")) {
        // Already placed!
        func_acheckout_debug("\t+ [Error]: Duplicate NEWORDERNOTIFICATION transaction goid: #$amazon_oid.");
        func_amazon_header_exit(403);
    }

    $new_cart = array();

    $_products = func_array_path($parsed, "$type/PROCESSEDORDER/PROCESSEDORDERITEMS/PROCESSEDORDERITEM");

    if (empty($_products)) {
        func_amazon_header_exit(500);
    }

    $amazon_carts = array();
    $_has_giftcert = '';
    foreach ($_products as $k => $v) {
        $v = $v['#'];
        $_ref = func_array_path($v,"CARTCUSTOMDATA/REF/0/#");
        $ProductType = func_array_path($v,"ITEMCUSTOMDATA/PRODUCTTYPE/0/#");
        list($_sku, $_cartid) = explode("|", func_array_path($v,"SKU/0/#"));
        if ($ProductType == 'giftcert') { 
            $_sku = 'giftcert' . intval($_cartid);
            $_has_giftcert = '+giftcerts';
        }            

        $amazon_carts[$_ref][$_cartid] = array('productcode' => $_sku, 'raw_data' => $v);

    }
    func_acheckout_debug("\t+ " . count($_products) . " products$_has_giftcert incoming");

    if (empty($amazon_carts)) {
        func_amazon_header_exit(500);
    }

    @func_array2insert(
        'amazon_orders',
        array(
            'amazon_oid' => $amazon_oid,
        )
    );

    $amazon_shipping = func_array_path($parsed, "$type/PROCESSEDORDER/SHIPPINGSERVICELEVEL/0/#");
    $shipping_name = func_array_path($parsed, "$type/PROCESSEDORDER/DISPLAYABLESHIPPINGLABEL/0/#");
    $customer_info = func_amazon_customer_info(func_array_path($parsed, "$type/PROCESSEDORDER/SHIPPINGADDRESS/0/#"));

    @list($customer_info['firstname'], $customer_info['lastname']) = explode(' ', func_array_path($parsed, "$type/PROCESSEDORDER/BUYERINFO/BUYERNAME/0/#"));
    $customer_info['firstname'] = empty($customer_info['firstname']) ? @$customer_info['address']['S']['firstname'] : $customer_info['firstname'];
    $customer_info['lastname'] = empty($customer_info['lastname']) ? @$customer_info['address']['S']['lastname'] : $customer_info['lastname'];
    $customer_info['email'] = func_array_path($parsed, "$type/PROCESSEDORDER/BUYERINFO/BUYEREMAILADDRESS/0/#");
    $customer_info = func_addslashes($customer_info);

    x_load('user', 'cart', 'shipping');
    foreach ($amazon_carts as $ref => $_products) {

        func_acheckout_debug("\t+ skey: $ref");
        func_acheckout_restore_session_n_global($ref);    
        func_acheckout_debug("\t+ login: $login, logged_userid: $logged_userid");

        if (empty($cart)) {
            func_acheckout_debug("\t+ Cannot restore cart for $ref skey");
            continue;
        }

        $products = func_products_in_cart($cart, (!empty($user_account['membershipid']) ? $user_account['membershipid'] : ''));

        if (empty($login) || $login_type != 'C') {
            // Fill anonymous profile
            $customer_info['usertype'] = 'C';
            func_set_anonymous_userinfo($customer_info);
            func_acheckout_debug("\t+ Anonymous profile created");

        } else {
            $cart = func_set_cart_address($cart, 'S', $customer_info['address']['S']);
            $cart = func_set_cart_address($cart, 'B', $customer_info['address']['B']);

            // Fill address book for logged customer
            if (func_is_address_book_empty($logged_userid)) {
                foreach ($customer_info['address'] as $addr_type => $val) {
                    $val['address'] = $val['address'] . "\n" . @$val['address_2'];
                    func_unset($val, 'address_2');
                    $val['default_' . strtolower($addr_type)] = 'Y';
                    func_save_address($logged_userid, 0, $val);

                    func_acheckout_debug("\t+ New address($addr_type) has been is added to $login\'s address book");
                }
            } else {
                func_acheckout_debug("\t+ Address book is not updated for $login customer");
            }
        }

        // Update current user info to place it into the order
        $cart['userinfo'] = func_array_merge($cart['userinfo'], $customer_info);
        $userinfo = $cart['userinfo'] = func_userinfo_from_scratch($cart['userinfo'], 'userinfo_for_payment');
        
        $shipping = func_amazon_get_choosen_shipping3($cart, $products, $shipping_name, $amazon_shipping);

        $cart = func_cart_set_shippingid($cart, $shipping['shippingid']);
        $totals = func_amazon_get_totals3($parsed, $_products, $amazon_oid);
        $cart['shipping_cost'] = $totals['shipping_cost'];
        $cart['total_cost'] = $totals['total_cost'];

        list($cart, $products) = func_generate_products_n_recalculate_cart();
        $cart = func_cart_set_shippingid($cart, $shipping['shippingid']);
        func_acheckout_debug("\t+ Shipping selected - #$cart[shippingid]. '$shipping[shipping]': $$cart[shipping_cost]");

        // Restore the values from session, not from global scope
        unset($secure_oid, $secure_oid_cost, $partner, $partner_clickid, $adv_campaignid);
        x_session_register('secure_oid');
        x_session_register('secure_oid_cost');
        x_session_register('partner');
        x_session_register('partner_clickid');
        x_session_register('adv_campaignid');

        $extra = array();
        $extra['amazon_oid'] = $amazon_oid;
        // Skip Anti Fraud for Amazon orders
        $extra['is_gcheckout'] = true;

        $order_details = func_amazon_get_order_details3($parsed, $_products, $amazon_oid);
        $orderids = func_place_order("Checkout by Amazon " . ($config['Amazon_Checkout']['amazon_test_mode'] == 'Y' ? ' (in test mode)' : ''), "Q", $order_details, '', '', $extra);
        if (empty($orderids)) {
            x_log_flag('log_payment_processing_errors', 'PAYMENTS', "Amazon checkout payment module: Order has not been created in X-Cart as products in cart are expired.", true);
            x_session_save();
            func_acheckout_debug("\t+ [Error] Order has not been created in X-Cart as products in cart are expired");
            continue;
        } else {
            func_acheckout_debug("\t+ Order placed: orderids (" . implode(',',$orderids) . ")");
        }

        func_cart_unlock();
        $secure_oid = $orderids;
        $secure_oid_cost = $cart['total_cost'];

        $oids = implode(',',$secure_oid);
        foreach ($orderids as $orderid) {
            func_array2insert('amazon_orders', 
                array(
                    'amazon_oid' => $amazon_oid, 
                    'orderid' => $orderid, 
                    'total' => $totals['total_cost']
                ), 
            true);
        }

        @db_query("DELETE FROM $sql_tbl[amazon_data] WHERE ref='$ref'");
        assert("$totals[total_cost] == $cart[total_cost] /*Amazon Checkout NEWORDERNOTIFICATION*/");
        func_acheckout_debug("\t+ Calculated total_cost: $cart[total_cost], Received total_cost:$totals[total_cost]");

        func_array2update('cc_pp3_data', array('param1' => $amazon_oid, 'param2' => 'Q', 'param3' => $oids, 'param4' => '', 'trstat' => 'RECV|'), "ref='$ref'");

        $cart = array();
        x_session_save();
    }

} elseif ($type == 'ORDERREADYTOSHIPNOTIFICATION' || $type == 'ORDERCANCELLEDNOTIFICATION') {

    if (!($orderids = func_query_column("SELECT orderid FROM $sql_tbl[amazon_orders] WHERE amazon_oid='$amazon_oid'"))) {
        func_acheckout_debug("\t+ Related X-Cart orders were not found for $amazon_oid amazon_oid");
        func_amazon_header_exit(500);
    }

    if ($type == 'ORDERREADYTOSHIPNOTIFICATION') {
        $order_status = 'P';
    } elseif ($type == 'ORDERCANCELLEDNOTIFICATION') {
        $order_status = 'D';
    }

    func_acheckout_debug("\t+ Order statuses is changed to $order_status for orderids (" . implode(',',$orderids) . ")");
    func_change_order_status($orderids, $order_status);
}

?>
