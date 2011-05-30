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
 * Gift Registry module functions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.php,v 1.21.2.2 2011/02/07 15:34:46 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

/**
 * Returns the events list for the certain customer
 */
function func_giftreg_get_events_list($userid, $detailed=false)
{
    global $sql_tbl;

    $events_list = func_query("SELECT $sql_tbl[giftreg_events].*, $sql_tbl[customers].firstname, $sql_tbl[customers].lastname, $sql_tbl[customers].email FROM $sql_tbl[giftreg_events], $sql_tbl[customers] WHERE $sql_tbl[customers].id=$sql_tbl[giftreg_events].userid AND $sql_tbl[giftreg_events].userid='$userid' ORDER BY $sql_tbl[giftreg_events].event_date");

    if (!empty($events_list) && $detailed) {
        foreach ($events_list as $k=>$v) {
            $events_list[$k]['emails'] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[giftreg_maillist] WHERE event_id='$v[event_id]'");
            $events_list[$k]['allow_to_send'] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[giftreg_maillist] WHERE event_id='$v[event_id]' AND status='Y'");
            $events_list[$k]['products'] = func_query_first_cell("SELECT COUNT($sql_tbl[wishlist].event_id) FROM $sql_tbl[wishlist], $sql_tbl[products] WHERE $sql_tbl[wishlist].userid='$userid' AND $sql_tbl[wishlist].event_id='$v[event_id]' AND $sql_tbl[wishlist].productid = $sql_tbl[products].productid AND $sql_tbl[products].forsale <> 'N'");

            if ($v['guestbook'] == 'Y') {
                $events_list[$k]['gb_count'] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[giftreg_guestbooks] WHERE event_id='$v[event_id]'");
            }
        }
    }
    return $events_list;
}

/**
 * Returns event data
 */
function func_giftreg_get_event_data($eventid)
{
    global $sql_tbl;

    return func_query_first("SELECT $sql_tbl[giftreg_events].*, $sql_tbl[customers].title AS creator_title, $sql_tbl[customers].firstname, $sql_tbl[customers].lastname FROM $sql_tbl[giftreg_events], $sql_tbl[customers] WHERE $sql_tbl[customers].id=$sql_tbl[giftreg_events].userid AND event_id='$eventid'");
}

/**
 * Returns eventid by wishlistid
 */
function func_giftreg_get_eventid($wishlistid)
{
    global $sql_tbl;

    return func_query_first_cell("SELECT $sql_tbl[giftreg_events].event_id FROM $sql_tbl[wishlist], $sql_tbl[giftreg_events] WHERE $sql_tbl[wishlist].wishlistid = '$wishlistid' AND $sql_tbl[giftreg_events].event_id = $sql_tbl[wishlist].event_id");
}

/**
 * This function generate the gift wrapping tax rates array
 */
function func_get_giftwrap_tax_rates($taxes, $provider)
{
    global $sql_tbl, $user_account, $config, $single_mode, $logged_userid;
    static $saved_tax_rates = array();

    if (empty($taxes) || !is_array($taxes))
        return array();

    $membershipid = $user_account['membershipid'];

    // Define available customer zones
    $tax_rates = $address_zones = $_tax_names = array();
    foreach ($taxes as $pid => $_tax) {
        foreach ($_tax as $k => $v) {
            $_tax_names['tax_'.$v['taxid']] = true;
        }
    }
    // Get tax names
    $_tax_names = func_get_languages_alt(array_keys($_tax_names));

    if ($config['Taxes']['enable_user_tax_exemption'] == 'Y') {

        // Get the 'tax_exempt' feature of customer

        static $_customer_tax_exempt;

        if (empty($_customer_tax_exempt)) {
            $_customer_tax_exempt = func_query_first_cell("SELECT tax_exempt FROM $sql_tbl[customers] WHERE id='$logged_userid'");
        }

        if ($_customer_tax_exempt == 'Y') {
            $tax_rate['skip'] = true;
        }
    }
    else {
        $_customer_tax_exempt = '';
    }

    // Generate tax rates array
    foreach ($taxes as $k => $v) {

        $provider_condition = '';
        if (!$single_mode)
            $provider_condition = "AND $sql_tbl[tax_rates].provider = '$provider'";

        if (!isset($address_zones[$provider][$v['address_type']])) {
            $address_zones[$provider][$v['address_type']] = array_keys(func_get_customer_zones_avail($logged_userid, $provider, $v['address_type']));
        }
        $zones = $address_zones[$provider][$v['address_type']];

        $tax_rate = array();
        if (!empty($zones) && is_array($zones)) {
            foreach ($zones as $zoneid) {
                if (!$single_mode && isset($saved_tax_rates[$provider][$v['taxid']][$zoneid][$membershipid])) {

                    // Get saved data (by provider name, zoneid and membershipid)
                    $tax_rate = $saved_tax_rates[$provider][$v['taxid']][$zoneid][$membershipid];

                } elseif ($single_mode && isset($saved_tax_rates[$v['taxid']][$zoneid][$membershipid])) {

                    // Get saved data (by zoneid and membershipid)
                    $tax_rate = $saved_tax_rates[$v['taxid']][$zoneid][$membershipid];

                } else {

                    $tax_rate = func_query_first("SELECT $sql_tbl[tax_rates].taxid, $sql_tbl[tax_rates].formula, $sql_tbl[tax_rates].rate_value, $sql_tbl[tax_rates].rate_type FROM $sql_tbl[tax_rates] LEFT JOIN $sql_tbl[tax_rate_memberships] ON $sql_tbl[tax_rate_memberships].rateid = $sql_tbl[tax_rates].rateid WHERE $sql_tbl[tax_rates].taxid = '$v[taxid]' $provider_condition AND $sql_tbl[tax_rates].zoneid = '$zoneid' AND ($sql_tbl[tax_rate_memberships].membershipid = '$membershipid' OR $sql_tbl[tax_rate_memberships].membershipid IS NULL) ORDER BY $sql_tbl[tax_rate_memberships].membershipid DESC");

                    if (!$single_mode) {
                        // Save data (by provider name, zoneid and membershipid)
                        $saved_tax_rates[$provider][$v['taxid']][$zoneid][$membershipid] = $tax_rate;

                    } else {
                        // Save data (by zoneid and membershipid)
                        $saved_tax_rates[$v['taxid']][$zoneid][$membershipid] = $tax_rate;
                    }
                }

                if (!empty($tax_rate))
                    break;
            }
        }

        if (empty($tax_rate) || $_customer_tax_exempt == 'Y') {
            if ($v['price_includes_tax'] != 'Y')
               continue;
            $tax_rate = func_query_first("SELECT $sql_tbl[tax_rates].taxid, $sql_tbl[tax_rates].formula, $sql_tbl[tax_rates].rate_value, $sql_tbl[tax_rates].rate_type FROM $sql_tbl[tax_rates] LEFT JOIN $sql_tbl[tax_rate_memberships] ON $sql_tbl[tax_rate_memberships].rateid = $sql_tbl[tax_rates].rateid WHERE $sql_tbl[tax_rates].taxid='$v[taxid]' $provider_condition AND ($sql_tbl[tax_rate_memberships].membershipid = '$membershipid' OR $sql_tbl[tax_rate_memberships].membershipid IS NULL) ORDER BY $sql_tbl[tax_rates].rate_value DESC");
            $tax_rate['skip'] = true;
        }

        if (empty($tax_rate['formula']))
            $tax_rate['formula'] = $v['formula'];

        $tax_rate['rate_value'] *= 1;
        $tax_rate['tax_display_name'] = isset($_tax_names['tax_'.$v['taxid']]) ? $_tax_names['tax_'.$v['taxid']] : $v['tax_name'];

        $tax_rates[$v['tax_name']] = func_array_merge($v, $tax_rate);
    }

    return $tax_rates;
}

?>
