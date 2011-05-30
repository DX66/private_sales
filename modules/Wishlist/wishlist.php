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
 * Process wishlist-related actions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: wishlist.php,v 1.113.2.3 2011/03/01 09:26:24 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

x_load(
    'cart',
    'mail',
    'product'
);

x_session_register('wlid_eventid');

if (
    !empty($login)
    && $REQUEST_METHOD == 'GET'
    && $mode != 'checkout'
) {

    $counts = func_query("SELECT COUNT(wishlistid) as count, wishlistid, productid, options, event_id, object FROM $sql_tbl[wishlist] WHERE userid = '$logged_userid' GROUP BY productid, options, event_id, object HAVING count > '1'");

    if (!empty($counts)) {

        foreach ($counts as $c) {

            $c = func_array_map('addslashes', $c);
            $sum = func_query_first_cell("SELECT SUM(amount) FROM $sql_tbl[wishlist] WHERE userid = '$logged_userid' AND productid = '$c[productid]' AND options = '$c[options]' AND event_id = '$c[event_id]' AND object = '$c[object]'");

            db_query("DELETE FROM $sql_tbl[wishlist] WHERE userid = '$logged_userid' AND productid = '$c[productid]' AND options = '$c[options]' AND event_id = '$c[event_id]' AND object = '$c[object]' AND wishlistid != '$c[wishlistid]'");
            db_query("UPDATE $sql_tbl[wishlist] SET amount = '$sum' WHERE wishlistid = '$c[wishlistid]'");

        }

        func_header_location("cart.php?".$QUERY_STRING);

    }

}

if ($mode == 'addgc2wl') {

    // Add Gift Certificate to the wish list

    if (!empty($gcindex)) {

        db_query("UPDATE $sql_tbl[wishlist] SET object='".addslashes(serialize($giftcert))."' WHERE wishlistid='$gcindex'");

        $eventid = func_query_first_cell("SELECT event_id FROM $sql_tbl[wishlist] WHERE wishlistid='$gcindex'");

        if (
            $eventid > 0
            && !empty($active_modules['Gift_Registry'])
        ) {

            func_header_location("giftreg_manage.php?eventid=$eventid&mode=products");

        } else {

            func_header_location("cart.php?mode=wishlist");

        }

    } else {

        db_query("insert into $sql_tbl[wishlist] (userid, amount, options, object) values ('$logged_userid', '1', '', '".addslashes(serialize($giftcert))."')");

    }

    func_header_location("cart.php?mode=wishlist");

} elseif ($action == 'update_quantity') {

    // Update quantity for product

    if (
        !empty($wlitem)
        && isset($quantity)
    ) {

        $eventid = intval($eventid);

        if ($quantity > 0) {
            db_query("UPDATE $sql_tbl[wishlist] SET amount='$quantity' WHERE wishlistid='$wlitem'");
        } else {
            db_query("DELETE FROM $sql_tbl[wishlist] WHERE userid='$logged_userid' AND wishlistid='$wlitem' AND event_id='$eventid'");
        }

        if ($eventid == 0)
            func_header_location("cart.php?mode=wishlist");
        else
            func_header_location("giftreg_manage.php?eventid=$eventid&mode=products");

    }

} elseif (
    $mode == 'add2wl'
    && $productid
) {

    // Add product to the wish list

    $added_product = func_select_product($productid, @$user_account['membershipid'], true, true);

    $_options = '';

    if (!empty($active_modules['Product_Options'])) {

        if (!is_array($product_options))
            $product_options = func_get_default_options($productid, $amount, @$user_account['membershipid']);

        if (is_array($product_options)) {

            // Check the received(default) options

            if (!func_check_product_options ($productid, $product_options)) {

                if (
                    !empty($active_modules['Product_Configurator'])
                    && $added_product['product_type'] == 'C'
                ) {

                    func_header_location("pconf.php?productid=$productid&err=options");

                } else {

                    func_header_location(func_get_resource_url('product', $productid, 'err=options'));

                }

            }

            $product_options = func_array_map('stripslashes', $product_options);

        }

        if (!is_array($product_options)) {

            unset($product_options);

        } else {

            $_options = addslashes(serialize($product_options));

        }

        // Check the received variants amount
        if (
            $config['General']['unlimited_products'] != 'Y'
            && !empty($product_options)
        ) {

            $variantid = func_get_variantid($product_options, $productid);

            if (!empty($variantid)) {

                // Get the variant amount

                $added_product['avail'] = func_get_options_amount($product_options, $productid);

                if (!empty($cart['products']))  {

                    foreach ($cart['products'] as $k => $v) {
                        if ($v['productid'] == $productid && $variantid == $v['variantid'])
                            $added_product['avail'] -= $v['amount'];
                    }
                }

                // Add to wish list amount of items that is not much than in stock

                if ($amount > $added_product['avail'])
                    $amount = $added_product['avail'];

                if ($amount < $added_product['min_amount'] && $variantid)
                    func_header_location(func_get_resource_url('product', $productid, 'err=options'));

            }

        }

    }

    $oamount = 0;
    $wlid = false;
    $object = '';

    // Detect event

    if (
        !empty($active_modules['Gift_Registry'])
        && !empty($eventid)
    ) {

        $eventid = abs(intval($eventid));
        $eventid = func_query_first_cell("SELECT IFNULL(event_id,0) FROM $sql_tbl[giftreg_events] WHERE userid='$logged_userid' AND event_id='$eventid'");

    } else {

        $eventid = 0;

    }

    if ($added_product['product_type'] == 'C') {

        x_session_register('configurations');

        $object = addslashes(serialize($configurations[$productid]));

    } else {

        // Check if the product already exists in some wishlist
        // if found, the $existing_wl array extracts to _wishlistid, amount

        $existing_wl = func_query_first("SELECT wishlistid, amount FROM $sql_tbl[wishlist] WHERE userid='$logged_userid' AND productid='$productid' AND options='$_options' AND event_id='$eventid'");

        if (!empty($existing_wl)) {

            $_wishlistid = $existing_wl['wishlistid'];

            if (empty($added_product['distribution']) || empty($active_modules['Egoods']))
                $oamount = $existing_wl['amount'];

        }

    }

    // Add to or update the wish list

    if (!empty($_wishlistid)) {

        func_array2update(
            'wishlist',
            array(
                'amount' => $amount + $oamount,
            ),
            "wishlistid='$_wishlistid'"
        );

    } else {

        func_array2insert(
            'wishlist',
            array(
                'userid'    => $logged_userid,
                'productid' => $productid,
                'amount'    => $amount,
                'options'   => $_options,
                'object'    => $object,
                'event_id'  => $eventid,
            )
        );
    }

    if (!empty($active_modules['SnS_connector']))
        func_generate_sns_action('AddToWishList');

    if (!empty($active_modules['Gift_Registry']) && $eventid>0)
        func_header_location("giftreg_manage.php?eventid=$eventid&mode=products");
    else
        func_header_location("cart.php?mode=wishlist");

} elseif (
    $mode == 'wl2cart'
    && (
        $wlitem
        || (
            $fwlitem
            && (
                !empty($wlid)
                || !empty($eventid)
            )
        )
    )
) {

    // Add to cart product from wish list

    if (!empty($eventid)) {

        $wishlistid = $fwlitem;
        $login_cond = "wl.event_id='$eventid' AND wl.wishlistid='$fwlitem' AND wl.userid = c.id";

        $wlid = func_query_first_cell("SELECT c.id FROM $sql_tbl[wishlist] AS wl, $sql_tbl[customers] AS c WHERE $login_cond");
        $wlid_eventid = $eventid;

    } else {

        if ($wlitem) {

            $wishlistid = $wlitem;
            $login_cond = "wl.userid='$logged_userid' AND wl.wishlistid='$wlitem'";

        } else {

            $wishlistid = $fwlitem;
            $login_cond = "c.id='$wlid' AND wl.wishlistid='$fwlitem'";

        }

    }

    if (!empty($wlid)) {

        $giftreg = array(
            'wlid'    => $wlid,
            'eventid' => $eventid,
        );

    }

    $wlproduct = func_query_first("SELECT wl.wishlistid, wl.productid, IF(wl.event_id > 0, wl.amount-amount_purchased, wl.amount) AS amount, wl.options, wl.object FROM $sql_tbl[wishlist] AS wl, $sql_tbl[customers] AS c WHERE wl.userid = c.id AND $login_cond");

    if (isset($amount))
        $wlproduct['amount'] = abs(intval($amount));

    if ($wlproduct) {

        if ($wlproduct['productid'] == 0) {

            // Add gift certificate to the cart

            $giftcert = unserialize($wlproduct['object']);

            if (!isset($cart['giftcerts']))
                $cart['giftcerts'] = array();

            $cart['giftcerts'][] = func_array_merge($giftcert, array('wishlistid'=>$wishlistid));

        } else {

            // Add product to the cart

            if (
                !empty($active_modules['Product_Configurator'])
                && !empty($wlproduct['object'])
            ) {

                if (!is_array($configurations)) {
                    $configurations = array();
                }

                $configurations[$wlproduct['productid']] = unserialize($wlproduct['object']);

            }

            $add_product = array();
            $add_product['productid']       = abs(intval($wlproduct['productid']));
            $add_product['amount']          = abs(intval($wlproduct['amount']));
            $add_product['product_options'] = unserialize($wlproduct['options']);
            $add_product['price']           = abs(doubleval($wlproduct['price']));

            if ($wishlistid) {
                $add_product['wishlistid'] = abs(intval($wishlistid));
            }

            $result = func_add_to_cart($cart, $add_product);

            // Recalculate cart totals after new item added
            list($cart, $products) = func_generate_products_n_recalculate_cart();
        }

    }

    func_header_location('cart.php');

} elseif (
    $mode == 'wldelete'
    && $wlitem
) {

    // Delete from wish list

    $eventid = intval($eventid);

    db_query("DELETE FROM $sql_tbl[wishlist] WHERE userid='$logged_userid' AND wishlistid='$wlitem' AND event_id='$eventid'");

    if ($eventid > 0)
        func_header_location("giftreg_manage.php?eventid=$eventid&mode=products");
    else
        func_header_location("cart.php?mode=wishlist");

} elseif ($mode == 'wlclear') {

    // Clear wish list

    db_query("DELETE FROM $sql_tbl[wishlist] WHERE userid='$logged_userid' AND event_id='0'");

    func_header_location("cart.php?mode=wishlist");

} elseif (
    $mode == 'wishlist'
    || (
        !empty($login)
        && $mode == 'send_friend'
        && $action == 'entire_list'
    )
    || (
        $mode == 'friend_wl'
        && !empty($wlid)
    )
) {

    if (
        $mode == 'friend_wl'
        && !empty($wlid)
        && !empty($wlid_eventid)
    ) {
        func_header_location("giftregs.php?eventid=$wlid_eventid");
    }

    // Obtain wishlist from database
    if (
        $mode == 'send_friend'
        && !func_check_email($friend_email)
    ) {
        $top_message = array(
            'type'         => 'E',
            'content'     => func_get_langvar_by_name('err_wrong_email')
        );

        func_header_location("cart.php?mode=wishlist");
    }

    $ids_redirect = array();

    if ($mode == 'friend_wl') {

        $wl_raw = func_query("SELECT $sql_tbl[wishlist].*, $sql_tbl[products].forsale FROM $sql_tbl[wishlist] LEFT JOIN $sql_tbl[customers] ON $sql_tbl[customers].id=$sql_tbl[wishlist].userid LEFT JOIN $sql_tbl[products] ON $sql_tbl[wishlist].productid = $sql_tbl[products].productid WHERE $sql_tbl[customers].id='$wlid' AND $sql_tbl[wishlist].event_id='0'");

        $smarty->assign('giftregistry', 'giftregistry');

    } else {

        $wl_raw = func_query("SELECT $sql_tbl[wishlist].*, $sql_tbl[products].forsale FROM $sql_tbl[wishlist] LEFT JOIN $sql_tbl[products] ON $sql_tbl[wishlist].productid = $sql_tbl[products].productid WHERE $sql_tbl[wishlist].userid='$logged_userid' AND $sql_tbl[wishlist].event_id='0'");

        $smarty->assign('allow_edit', 'Y');

    }

    if (!empty($wl_raw)) {

        foreach ($wl_raw as $index => $wl_product) {

            if (
                $wl_product['productid'] > 0
                && $wl_product['forsale'] != 'Y'
                && $wl_product['forsale'] != 'H'
            ) {

                $ids_redirect[$wl_product['wishlistid']] = $wl_product['productid'];

                unset($wl_raw[$index]);

                break;

            }

            $wl_raw[$index]['options'] = unserialize($wl_product['options']);

            if (
                !empty($wl_raw[$index]['options'])
                && !empty($active_modules['Product_Options'])
            ) {
                $wl_raw[$index]['variantid'] = func_get_variantid($wl_raw[$index]['options'], $wl_product['productid']);
            }

            $wl_raw[$index]['amount_requested'] = $wl_product['amount'];

            $remain = $wl_raw[$index]['amount_remain'] = max($wl_product['amount'] - $wl_product['amount_purchased'],0);

            if (
                $remain > 0
                && $mode == 'friend_wl'
            ) {
                $wl_raw[$index]['amount'] = $remain;
            }

        }

    }

    $wl_products = func_products_from_scratch($wl_raw, $user_account['membershipid'], true );

    if (!empty($active_modules['Subscriptions'])) {

        if (!function_exists('SubscriptionProducts'))
            if (
                file_exists($xcart_dir . '/modules/Subscriptions/subscription.php')
                && is_readable($xcart_dir . '/modules/Subscriptions/subscription.php')
            ) {
                include $xcart_dir . '/modules/Subscriptions/subscription.php';
            }

        $wl_products = SubscriptionProducts($wl_products);

    }

    if (!empty($active_modules['Product_Configurator'])) {

        include $xcart_dir . '/modules/Product_Configurator/pconf_customer_wishlist.php';

    }

    if (!empty($active_modules['Gift_Certificates'])) {

        $wl_raw = func_query("select wishlistid, amount, amount_purchased, object from $sql_tbl[wishlist] WHERE userid='$logged_userid' AND event_id='0' AND productid='0'");

        if (is_array($wl_raw)) {

            foreach ($wl_raw as $k=>$v) {
                $object = unserialize($v['object']);
                $wl_giftcerts[] = func_array_merge($v, $object);
            }

            if (!empty($wl_giftcerts))
                $smarty->assign('wl_giftcerts', $wl_giftcerts);

        }

    }

    if (!empty($active_modules['Gift_Registry'])) {

        if (
            file_exists($xcart_dir . '/modules/Gift_Registry/giftreg_wishlist.php')
            && is_readable($xcart_dir . '/modules/Gift_Registry/giftreg_wishlist.php')
        ) {
            include $xcart_dir . '/modules/Gift_Registry/giftreg_wishlist.php';
        }

    }

    if (
        !empty($ids_redirect)
        && is_array($ids_redirect)
    ) {
        db_query("DELETE FROM $sql_tbl[wishlist] WHERE wishlistid IN ('".implode("','", array_keys($ids_redirect))."')");

        foreach($ids_redirect as $k => $id) {

            $ids_redirect[$k] = func_query_first_cell("SELECT IF($sql_tbl[products_lng].product != '', $sql_tbl[products_lng].product, $sql_tbl[products].product) FROM $sql_tbl[products] LEFT JOIN $sql_tbl[products_lng] ON $sql_tbl[products].productid = $sql_tbl[products_lng].productid AND $sql_tbl[products_lng].code = '$shop_language' WHERE $sql_tbl[products].productid = '$id'");

        }

        $top_message = array(
            'content' => func_get_langvar_by_name(
                'txt_wishlist_disabled_products',
                array(
                    'product_list' => "<br />&nbsp;&nbsp;&nbsp;" . implode("<br />&nbsp;&nbsp;&nbsp;", $ids_redirect)
                )
            ),
            'type' => 'W'
        );

        func_header_location("cart.php?".$QUERY_STRING);

    }

    if ($mode == 'send_friend') {

        func_safe_mode();

        $mail_smarty->assign('wlid', $logged_userid);
        $mail_smarty->assign('wl_products', $wl_products);
        $mail_smarty->assign('userinfo', func_array_map('func_html_entity_decode',$userinfo));

        if (
            !func_send_mail(
                $friend_email,
                'mail/wishlist_sendall2friend_subj.tpl',
                'mail/wishlist_sendall2friend.tpl',
                $userinfo['email'],
                false
            )
        ) {

            $top_message['type'] = 'E';
            $top_message['content'] = func_get_langvar_by_name("lbl_send_mail_error");

            func_header_location("cart.php?mode=wishlist");

        } else {

            $top_message = array(
                'content' => func_get_langvar_by_name('txt_wishlist_sent')
            );

            func_header_location("cart.php?mode=wishlist");

        }

    } else {

        $location[] = array(func_get_langvar_by_name('lbl_wish_list'), '');

        if (!empty($wl_products))
            $smarty->assign('wl_products',$wl_products);

        $main = 'wishlist';

    }

} elseif (
    !empty($login)
    && $mode == 'send_friend'
    && !empty($friend_email)
) {

    $product = func_select_product($productid, $user_account['membershipid']);

    $mail_smarty->assign('product', $product);
    $mail_smarty->assign('userinfo', $userinfo);

    func_send_mail(
        $friend_email,
        'mail/wishlist_send2friend_subj.tpl',
        'mail/wishlist_send2friend.tpl',
        $userinfo['email'],
        false
    );

    $top_message = array(
        'content' => func_get_langvar_by_name('txt_recommendation_sent')
    );

    func_header_location("cart.php?mode=wishlist");

} elseif (
    !empty($login)
    && $mode == 'send_friend'
    && empty($friend_email)
) {

    $top_message = array(
        'type'         => 'E',
        'content'     => func_get_langvar_by_name('err_wrong_email')
    );

    func_header_location("cart.php?mode=wishlist");

}

?>
