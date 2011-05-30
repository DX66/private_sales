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
 * Event wishlist
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: giftreg_wishlist.php,v 1.31.2.1 2011/01/10 13:11:57 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

/**
 * Get the events list data
 */
$events_list = func_query("SELECT * FROM $sql_tbl[giftreg_events] WHERE userid='$logged_userid' ORDER BY event_date");

if (is_array($events_list)) {
/**
 * Expand the events list data
 */
    foreach($events_list as $k=>$v) {
        $events_list[$k]['emails'] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[giftreg_maillist] WHERE event_id='$v[event_id]'");
        $events_list[$k]['products'] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[wishlist] WHERE userid='$logged_userid' AND event_id='$v[event_id]'");
    }

    $smarty->assign('events_list', $events_list);
    $smarty->assign('events_lists_count', (is_array($events_list) ? count($events_list) : 0));
}

if ($REQUEST_METHOD == 'POST' && $mode == 'move_product' && !empty($wlitem)) {
/**
 * Move item
 */
    $login_cond = "userid='$logged_userid' AND wishlistid='$wlitem'";
    $quantity = intval($move_quantity);
    $eventid_to = intval($eventid_to);

    if ($quantity > 0) {

        $wlitem_data = func_query_first("SELECT * FROM $sql_tbl[wishlist] WHERE $login_cond");

        if ($wlitem_data['productid'] == 0) {

        // Move the Gift Certificate

            db_query("UPDATE $sql_tbl[wishlist] SET event_id='$eventid_to' WHERE $login_cond");
        }
        else {

        // Move product to the wish list for other event

            $quantity = min($quantity, $wlitem_data['amount']);
            $rest_quantity = $wlitem_data['amount'] - $quantity;

            $same_item_exists = func_query_first("SELECT wishlistid FROM $sql_tbl[wishlist] WHERE userid='$logged_userid' AND productid='$wlitem_data[productid]' AND options='".addslashes($wlitem_data['options'])."' AND object='".addslashes($wlitem_data['object'])."' AND event_id='$eventid_to'");
            if ($same_item_exists) {

            // If this product already exists in the destination wish list

                db_query("UPDATE $sql_tbl[wishlist] SET event_id='$eventid_to', amount=amount+$quantity WHERE wishlistid='$same_item_exists[wishlistid]'");
            }
            else {

            // If this item does not exist - insert it

                $fields = array();
                foreach ($wlitem_data as $k=>$v) {
                    if ($k == 'amount')
                        $v = $quantity;
                    if ($k == 'event_id')
                        $v = $eventid_to;
                    if ($k != 'wishlistid') {
                        $fields[$k] = addslashes($v);
                    }
                }
                func_array2insert('wishlist', $fields);
                db_query("UPDATE $sql_tbl[wishlist] SET amount='$rest_quantity' WHERE $login_cond");
            }

            if ($rest_quantity == 0)

            // Delete product from the source wish list

                db_query("DELETE FROM $sql_tbl[wishlist] WHERE $login_cond");
            else
                db_query("UPDATE $sql_tbl[wishlist] SET amount='$rest_quantity' WHERE $login_cond");
        }

        if (x_session_is_registered('mail_data'))
            x_session_unregister('mail_data');
    }

    if ($wlitem_data['event_id'] == 0)
        func_header_location("cart.php?mode=wishlist");
    else
        func_header_location("giftreg_manage.php?eventid=$wlitem_data[event_id]&mode=products");
}
?>
