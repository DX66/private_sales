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
 * @version    $Id: order_notifications.php,v 1.9.2.4 2011/01/10 13:11:55 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

x_load('order');

$root_node = strtoupper($NotificationType);

$amazon_orderid = func_array_path($parsed, "$root_node/PROCESSEDORDER/AMAZONORDERID/0/#");

if ($NotificationType == 'NewOrderNotification') {

    if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[amazon_orders] WHERE amazon_oid='$amazon_orderid'")) {
        // Already placed!
        func_amazon_header_exit(403);
    }

    $_addr = func_array_path($parsed, "$root_node/PROCESSEDORDER/SHIPPINGADDRESS/0/#");
   
    $customer_info['address'] = array();

    $customer_info['address']['B']['country']   = $_addr['COUNTRYCODE'][0]['#'];
    $customer_info['address']['B']['address']   = $_addr['ADDRESSFIELDONE'][0]['#'] . "\n" . $_addr['ADDRESSFIELDTWO'][0]['#'];
    $customer_info['address']['B']['city']      = $_addr['CITY'][0]['#'];
    $customer_info['address']['B']['state']     = func_amazon_detect_state($_addr['STATE'][0]['#'], $customer_info['address']['B']['country']);
    $customer_info['address']['B']['zipcode']   = $_addr['POSTALCODE'][0]['#'];

    list($customer_info['address']['B']['firstname'], $customer_info['address']['B']['lastname']) = explode(' ', $_addr['NAME'][0]['#']);

    $customer_info['address']['S'] = $customer_info['address']['B'];

    list($customer_info['firstname'], $customer_info['lastname']) = explode(' ',func_array_path($parsed, "$root_node/PROCESSEDORDER/BUYERINFO/BUYERNAME/0/#"));
    $customer_info['email'] = func_array_path($parsed, "$root_node/PROCESSEDORDER/BUYERINFO/BUYEREMAILADDRESS/0/#");

    $new_cart = array();

    $amazon_shipping = func_array_path($parsed, "$root_node/PROCESSEDORDER/SHIPPINGSERVICELEVEL/0/#");

    $_products = func_array_path($parsed, "$root_node/PROCESSEDORDER/PROCESSEDORDERITEMS/PROCESSEDORDERITEM");
    $amazon_carts = array();

    if (empty($_products)) {
        func_amazon_header_exit(500);
    }
    foreach ($_products as $k => $v) {
        $v = $v['#'];
        $_ref = func_array_path($v,"CARTCUSTOMDATA/REF/0/#");
        list($_sku, $_cartid) = explode("|", func_array_path($v,"SKU/0/#"));

        $amazon_carts[$_ref][$_cartid] = array('productcode' => $_sku, 'raw_data' => $v);

    }

    if (empty($amazon_carts)) {
        func_amazon_header_exit(500);
    }

    $customer_info = func_addslashes($customer_info);

    foreach ($amazon_carts as $ref => $_products) {

        $sessid = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='$ref'");

        x_session_id($sessid);

        x_session_register('login');
        x_session_register('logged_userid');
        x_session_register('login_type');
        x_session_register('current_carrier', 'UPS');

        if (!empty($login)) {
            $user_account['membershipid'] = func_query_first_cell("SELECT membershipid FROM $sql_tbl[customers] WHERE id='$logged_userid'");
        }

        if (empty($login) || $login_type != 'C') {

            x_session_register('anonymous_userinfo', array());
            $anonymous_userinfo = $customer_info;
            $anonymous_userinfo['usertype'] = empty($usertype) ? 'C' : $usertype;

        } else {

            // Update the existing user profile
            $query_data = $customer_info;
            func_unset($query_data, 'address', 'email');

            func_array2update('customers', $query_data, "id = '$logged_userid'");

            x_load('user');

            // Fill address book
            if (func_is_address_book_empty($logged_userid)) {
                foreach ($customer_info['address'] as $addr_type => $val) {
                    $val['address'] = $val['address'] . "\n" . $val['address_2'];
                    func_unset($val, 'address_2');
                    $val['default_' . strtolower($a_type)] = 'Y';
                    func_save_address($logged_userid, 0, $val);
                }
            }
        }

        $cart = func_query_first_cell("SELECT cart FROM $sql_tbl[amazon_data] WHERE ref='$ref'");

        if (!$cart) {
            func_amazon_header_exit(403);
        }

        $cart = unserialize($cart);
        $products = func_products_in_cart($cart, (!empty($user_account['membershipid']) ? $user_account['membershipid'] : ''));
        $shipping = $cart['amazon_shippings'][$amazon_shipping];
        $cart['shippingid'] = $shipping['shippingid'];
        $cart['shipping_cost'] = $shipping['rate'];

        $userinfo = $cart['userinfo'] = func_array_merge($cart['userinfo'], $customer_info);
        $userinfo['login'] = $login;

        $cart = func_calculate($cart, $products, $logged_userid, 'C');
        $cart['shippingid'] = $shipping['shippingid'];

        $extra = array();
        $extra['amazon_oid'] = $amazon_orderid;

        $order_details = '';
        $order_details .= "AmazonOrderId: $amazon_orderid\n\n";
        $order_details .= "OrderChannel: ".func_array_path($parsed, "$root_node/PROCESSEDORDER/ORDERCHANNEL/0/#")."\n\n";

        $order_details .= "Ordered products:\n\n";
        $a_total = 0;
        foreach ($_products as $_prd) {
            $order_details .= "SKU: $_prd[productcode]\n";
            $order_details .= "AmazonOrderItemCode: ".func_array_path($_prd['raw_data'],"AMAZONORDERITEMCODE/0/#")."\n";
            $order_details .= "CartId: ".func_array_path($_prd['raw_data'],"CARTID/0/#")."\n";
            $charges = func_array_path($_prd['raw_data'],"ITEMCHARGES/COMPONENT");
            $p_total = 0;
            foreach ($charges as $charge) {
                $_type = func_array_path($charge,"TYPE/0/#");
                $_charge = func_array_path($charge,"CHARGE/AMOUNT/0/#");
                if ($_type != 'PrincipalPromo' && $_type != 'ShippingPromo') {
                    $p_total += $_charge;
                } else {
                    $p_total -= $_charge;
                }
            }
            $a_total += $p_total;
        }

        x_session_register('secure_oid');
        x_session_register('secure_oid_cost');
        x_session_register('cart_locked');
        x_session_register('partner');
        x_session_register('partner_clickid');
        x_session_register('adv_campaignid');
        $cart_locked = false;

        $orderids = func_place_order("Checkout by Amazon " . ($config['Amazon_Checkout']['amazon_test_mode'] == 'Y' ? ' (in test mode)' : ''), "I", $order_details, '', '', $extra);
        $oids = 'n/a';
        if ($orderids) {
            $secure_oid = $orderids;
            $secure_oid_cost = $cart['total_cost'];

            $oids = implode(',',$secure_oid);

            foreach ($orderids as $orderid) {
                @func_array2insert('amazon_orders', array('amazon_oid' => $amazon_orderid, 'orderid' => $orderid, 'total' => $a_total));
            }

            @db_query("DELETE FROM $sql_tbl[amazon_data] WHERE ref='$ref'");
        }
        @func_array2update('cc_pp3_data', array('param1' => $amazon_orderid, 'param2' => $oids, 'param3' => $a_total, 'trstat' => 'RECV|'),"ref='$ref'");

        x_session_register('cart');
        $cart = array();

        x_session_save();
    }

} elseif ($NotificationType == 'OrderReadyToShipNotification' || $NotificationType == 'OrderCancelledNotification') {

    if (!($orderids = func_query_column("SELECT orderid FROM $sql_tbl[amazon_orders] WHERE amazon_oid='$amazon_orderid'"))) {
        func_amazon_header_exit(500);
    }

    if ($NotificationType == 'OrderReadyToShipNotification') {
        $order_status = 'P';
    } elseif ($NotificationType == 'OrderCancelledNotification') {
        $order_status = 'D';
    }

    func_change_order_status($orderids, $order_status);

}

?>
