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
 * Functions for Stop list module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.php,v 1.27.2.2 2011/02/07 15:34:46 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

function func_is_allowed_trans()
{
    global $sql_tbl, $REMOTE_ADDR, $PROXY_IP, $config, $cart, $active_modules, $HTTP_VIA, $session_failed_transaction, $add_to_cart_time;

    if (
        empty($REMOTE_ADDR)
        || empty($cart['products'])
        || empty($active_modules['Stop_List'])
    ) {
        return true;
    }

    // Check transaction limit
    if ($config['Stop_List']['slist_max_transaction'] > 0) {

        $counter = func_query_first_cell ("SELECT COUNT(*) FROM $sql_tbl[orders], $sql_tbl[order_extras] WHERE $sql_tbl[order_extras].orderid = $sql_tbl[orders].orderid AND $sql_tbl[orders].date > '".(XC_TIME-86400)."' AND $sql_tbl[order_extras].khash = 'ip' AND $sql_tbl[order_extras].value = '".$REMOTE_ADDR."'");

        if ($counter >= $config['Stop_List']['slist_max_transaction'])
            return false;

    }

    // Check stop list
    if (!func_ip_check($REMOTE_ADDR))
        return false;

    // Check failed transaction limit
    if ($config['Stop_List']['slist_failed_transaction_limit'] > 0) {

        $counter = func_query_first_cell ("SELECT COUNT(*) FROM $sql_tbl[orders], $sql_tbl[order_extras] WHERE $sql_tbl[order_extras].orderid = $sql_tbl[orders].orderid AND $sql_tbl[orders].status = 'F' AND $sql_tbl[order_extras].khash = 'ip' AND $sql_tbl[order_extras].value = '".$REMOTE_ADDR."'");

        if ($counter >= $config['Stop_List']['slist_failed_transaction_limit']) {

            func_add_ip_to_slist($REMOTE_ADDR, 'T');

            return false;

        }

    }

    // Check whether the product set of the current order coincides with the product set of the order placed by a user with an IP from the stop list
    if ($config['Stop_List']['slist_P_check_enabled'] == 'Y') {

        $pids = array();

        foreach ($cart['products'] as $v) {
            $pids[] = $v['productid'];
        }

        if (!empty($pids)) {

            $ips = func_query("SELECT COUNT($sql_tbl[order_details].productid) as counter, $sql_tbl[order_extras].value FROM $sql_tbl[order_extras], $sql_tbl[orders], $sql_tbl[order_details] WHERE $sql_tbl[order_extras].khash = 'ip' AND $sql_tbl[orders].orderid = $sql_tbl[order_extras].orderid AND $sql_tbl[orders].status IN ('F', 'D') AND $sql_tbl[order_details].orderid = $sql_tbl[orders].orderid AND $sql_tbl[order_details].productid IN ('".implode("','", $pids)."') GROUP BY $sql_tbl[order_details].productid, $sql_tbl[orders].orderid");

            if ($ips) {

                foreach ($ips as $v) {

                    if (
                        ($v['counter'] == count($pids))
                        && !func_ip_check($v['value'])
                    ) {

                        func_add_ip_to_slist($REMOTE_ADDR, 'P');

                        return false;

                    }

                }

            } // if ($ips)

        } // if (!empty($pids))

    } // if ($config['Stop_List']['slist_P_check_enabled'] == 'Y')

    // Check session failed transaction
    if (
        $config['Stop_List']['slist_sess_failed_trans_limit'] > 0
        && $session_failed_transaction > 0
    ) {

        if ($session_failed_transaction >= $config['Stop_List']['slist_sess_failed_trans_limit']) {

            func_add_ip_to_slist($REMOTE_ADDR, 'S');

            return false;

        }

    }

    // Check add to cart time for this IP address
    if (
        $config['Stop_List']['slist_fast_order_number'] > 0
        && !empty($add_to_cart_time)
        && (XC_TIME - $add_to_cart_time) <= $config['Stop_List']['slist_fast_order_time']
    ) {
        if (func_query_first_cell ("SELECT COUNT(*) FROM $sql_tbl[order_extras] as oe1, $sql_tbl[order_extras] as oe2 WHERE oe1.orderid = oe2.orderid AND oe1.khash = 'add_to_cart_time' AND oe1.value <= ".(int)$config['Stop_List']['slist_fast_order_time']." AND oe2.khash = 'ip' AND oe2.value = '$REMOTE_ADDR'") >= $config['Stop_List']['slist_fast_order_number']) {

            func_add_ip_to_slist($REMOTE_ADDR, 'F');

            return false;

        }

    }

    // Check proxy ip - anonymous or not
    if ($config['Stop_List']['slist_cancel_proxy_anonymous'] == 'Y') {

        if (
            !empty($HTTP_VIA)
            && empty($PROXY_IP)
        ) {

            func_add_ip_to_slist($REMOTE_ADDR, 'A');

            return false;

        }

    }

    return true;
}

/**
 * Add IP address to Stop list
 */
function func_add_ip_to_slist($ip, $reason = 'M', $ip_type = "B")
{
    global $sql_tbl;

    if (empty($ip))
        return false;

    $octet = explode('.', $ip);

    if (count($octet) != 4)
        return false;

    foreach ($octet as $k => $v) {

        if ($v == '*') {

            $octet[$k] = -1;

        } elseif ($v > 255) {

            $octet[$k] = 255;

        }

    }

    if (func_ip_exist_slist(implode('.', $octet)))
        return false;

    $data_query = array(
        'octet1'     => $octet[0],
        'octet2'     => $octet[1],
        'octet3'     => $octet[2],
        'octet4'     => $octet[3],
        'ip'         => $ip,
        'reason'     => $reason,
        'date'         => XC_TIME,
        'ip_type'     => $ip_type,
    );

    return func_array2insert('stop_list', $data_query, true);
}

/**
 * IP address exists in Stop list
 */
function func_ip_exist_slist($ip)
{
    global $sql_tbl;

    $octet = explode('.', $ip);

    return (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[stop_list] WHERE octet1 IN ('$octet[0]', '-1') AND octet2 IN ('$octet[1]', '-1') AND octet3 IN ('$octet[2]', '-1') AND octet4 IN ('$octet[3]', '-1')") > 0 ? true : false);
}

/**
 * IP address check in Stop list
 */
function func_ip_check($ip)
{
    global $sql_tbl;

    $octet = explode('.', $ip);

    return (func_query_first_cell("SELECT ip_type FROM $sql_tbl[stop_list] WHERE octet1 IN ('$octet[0]', '-1') AND octet2 IN ('$octet[1]', '-1') AND octet3 IN ('$octet[2]', '-1') AND octet4 IN ('$octet[3]', '-1')") == 'B' ? false : true);
}

?>
