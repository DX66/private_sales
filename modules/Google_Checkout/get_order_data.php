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
 * Google checkout: Get order data
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: get_order_data.php,v 1.13.2.2 2011/02/04 10:12:26 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

$order['gcheckout_data'] = func_query_first("SELECT * FROM $sql_tbl[gcheckout_orders] WHERE orderid='$orderid'");

if (empty($order['gcheckout_data']))
    return ;

if ($current_area == 'A' || ($current_area == 'P' && !empty($active_modules['Simple_Mode']))) {
    // Get all linked orders
    $order['gcheckout_data']['linked_orders'] = func_query("SELECT $sql_tbl[orders].orderid, $sql_tbl[orders].total FROM $sql_tbl[gcheckout_orders], $sql_tbl[orders] WHERE $sql_tbl[gcheckout_orders].orderid=$sql_tbl[orders].orderid AND $sql_tbl[gcheckout_orders].goid='{$order['gcheckout_data']['goid']}' ORDER BY $sql_tbl[orders].orderid");

    if (count($order['gcheckout_data']['linked_orders']) <= 1)
        unset($order['gcheckout_data']['linked_orders']);
}

list($order['gcheckout_data']['fulfillment_state'], $order['gcheckout_data']['fulfillment_state_date']) = explode('|', $order['gcheckout_data']['fulfillment_state']);
$order['gcheckout_data']['fulfillment_state_date'] = date("Y/m/d H:i:s", $order['gcheckout_data']['fulfillment_state_date']);

list($order['gcheckout_data']['financial_state'], $order['gcheckout_data']['financial_state_date']) = explode('|', $order['gcheckout_data']['financial_state']);
$order['gcheckout_data']['financial_state_date'] = date("Y/m/d H:i:s", $order['gcheckout_data']['financial_state_date']);

$smarty->assign('order_refunded', (preg_match("/REFUNDED/i", $order['gcheckout_data']['financial_state']) && $order['gcheckout_data']['refunded_amount'] == $order['gcheckout_data']['total']));

$_state_log = explode('-', $order['gcheckout_data']['state_log']);
$_state_log_str = '';

if (is_array($_state_log)) {
    foreach ($_state_log as $_state) {
        list($_state_name, $state_date) = explode('|', $_state);
        $_state_log_str .= "[" . date("Y/m/d H:i:s", $state_date) . "] $_state_name\n";
    }
}

$order['gcheckout_data']['state_log'] = $_state_log_str;

$_carrier = substr($order['shipping'], 0, 3);

switch ($_carrier) {
    case 'UPS':
        $order['shipping_carrier'] = 'UPS';
        break;
    case 'USP':
        $order['shipping_carrier'] = 'USPS';
        break;
    case 'Fed':
        $order['shipping_carrier'] = 'FedEx';
        break;
    case 'DHL':
        $order['shipping_carrier'] = 'DHL';
        break;
    default:
        $order['shipping_carrier'] = 'Other';
}

?>
