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
 * Gets necessary event data for displaying
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: giftreg_display.php,v 1.38.2.2 2011/01/25 09:43:13 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

x_load('cart');

if ($eventid) {

    // Get the event information

    $event_data = func_giftreg_get_event_data($eventid);

    if (empty($event_data) or $event_data['status'] == 'D') {
        func_page_not_found();
    }

    $location[] = array($event_data['title'], '');

    if ($event_data['guestbook'] == 'Y')
        $event_data['gb_count'] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[giftreg_guestbooks] WHERE event_id='$eventid'");

    if ($event_data['userid'] != $logged_userid && $event_data['status'] == 'P' && $access_status[$eventid] != 'Y') {

        // Private events

        $has_access = 1;
        if (!empty($login)) {
            $email = func_query_first_cell("SELECT email FROM $sql_tbl[customers] WHERE id='$logged_userid'");
            $has_access = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[giftreg_maillist] WHERE recipient_email='".addslashes($email)."' AND event_id='$eventid'");
        }
        else {
            $has_access = 0;
        }

        if ($has_access == 0)
            func_header_location("error_message.php?giftreg_is_private");

        $access_status[$eventid] = 'Y';
    }

    $smarty->assign('eventid', $eventid);
    $smarty->assign('event_data', $event_data);

    // Get the products information

    $wl_raw = func_query("SELECT wishlistid, productid, amount, amount_purchased, options, object FROM $sql_tbl[wishlist] WHERE event_id='$eventid' AND productid>'0'");

    if (is_array($wl_raw)) {
        foreach ($wl_raw as $index=>$wl_product) {
            $wl_raw[$index]['options'] = unserialize($wl_product['options']);

            $wl_raw[$index]['amount_requested'] = $wl_product['amount'];
            $remain = $wl_raw[$index]['amount_remain'] = max($wl_product['amount'] - $wl_product['amount_purchased'], 0);
            if ($remain > 0) {
                $wl_raw[$index]['amount'] = $remain;
            }
        }

        $wl_products = func_products_from_scratch($wl_raw, $user_account['membershipid'], true);

        if (!empty($active_modules['Subscriptions'])) {
            if (!function_exists('SubscriptionProducts'))
                if (
                    file_exists($xcart_dir.'/modules/Subscriptions/subscription.php')
                    && is_readable($xcart_dir.'/modules/Subscriptions/subscription.php')
                ) {
                    include $xcart_dir.'/modules/Subscriptions/subscription.php';
                }

            $wl_products = SubscriptionProducts($wl_products);
        }

        if (!empty($active_modules['Product_Configurator'])) {
            include $xcart_dir.'/modules/Product_Configurator/pconf_customer_wishlist.php';
        }
    }

    $smarty->assign('wl_products',$wl_products);

    if (!empty($active_modules['Gift_Certificates'])) {
        $wl_raw = func_query("select wishlistid, amount, amount_purchased, object from $sql_tbl[wishlist] where event_id='$eventid' AND productid='0'");
        if (is_array($wl_raw)) {
            foreach ($wl_raw as $k=>$v) {
                $object = unserialize($v['object']);
                $wl_giftcerts[] = func_array_merge($v, $object);
            }

            $smarty->assign('wl_giftcerts', $wl_giftcerts);
        }
    }
}

$mode = 'event_details';

$smarty->assign('mode', $mode);

$smarty->assign('main','giftreg');
?>
