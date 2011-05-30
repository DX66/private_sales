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
 * Process commands to Google checkout from the back-end
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: gcheckout_admin.php,v 1.18.2.1 2011/01/10 13:11:57 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/**
 * Google checkout: Order processing commands
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

set_time_limit(86400);

$gcheckout_cancel_reason_list = array(
    1 => 'Not as described/expected',
    2 => 'Wrong size',
    3 => 'Found better prices elsewhere',
    4 => 'Product is missing parts',
    5 => 'Product is defective/damaged',
    6 => 'Took too long to deliver',
    7 => 'Item out of stock',
    8 => 'Customer request to cancel',
    9 => 'Item discontinued',
    10 => 'Other'
);

if ($current_area == 'A' || ($current_area == 'P' && !empty($active_modules['Simple_Mode'])))
    $gcheckout_admin = 'Y';
else
    $gcheckout_admin = '';

if ($REQUEST_METHOD == 'POST' && $mode == 'gcheckout') {

    x_load('http', 'xml');

    func_gcheckout_debug("*** Order processing command has been issued: '$gcmode' ($login)");

    $goid = $order_data['order']['gcheckout_data']['goid'];

    func_gcheckout_debug("\t+ Google Checkout order number (goid): '$goid'");

    $error = 0;
    $change_archived_status = '';

    if (empty($gcheckout_admin) && in_array($gcmode, array('charge', 'refund', 'cancel', 'archive', 'unarchive')))
        return;

    switch ($gcmode) {

        case 'charge': {

            // Send 'charge-order' request

            $total_cost = $order_data['order']['gcheckout_data']['total'];

            func_gcheckout_debug("\t+ Sending message: charge-order (total cost: $total_cost)");

            $_xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<charge-order xmlns="http://checkout.google.com/schema/2" google-order-number="$goid">
    <amount currency="{$config['Google_Checkout']['gcheckout_currency']}">{$total_cost}</amount>
</charge-order>
XML;

            break;
        }

        case 'refund': {

            // Send 'refund-order' request

            $refund_amount = price_format($refund_amount);
            $refund_comment = func_google_encode(stripslashes($refund_comment));
            $refund_reason = func_google_encode(stripslashes($refund_reason));

            func_gcheckout_debug("\t+ Sending message: refund-order (refund amount: $refund_amount)");

            $_xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<refund-order xmlns="http://checkout.google.com/schema/2" google-order-number="$goid">
    <amount currency="{$config['Google_Checkout']['gcheckout_currency']}">{$refund_amount}</amount>
    <comment>{$refund_comment}</comment>
    <reason>{$refund_reason}</reason>
</refund-order>
XML;

            break;
        }

        case 'cancel': {

            // Send 'cancel-order' request

            $cancel_reason_sel = intval($cancel_reason_sel);
            if (!empty($gcheckout_cancel_reason_list[$cancel_reason_sel]))
                $cancel_reason = $gcheckout_cancel_reason_list[$cancel_reason_sel];
            elseif (empty($cancel_reason))
                $cancel_reason = 'Other';

            $cancel_comment = func_google_encode(stripslashes($cancel_comment));
            $cancel_reason = func_google_encode(stripslashes($cancel_reason));

            func_gcheckout_debug("\t+ Sending message: cancel-order");

            $_xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<cancel-order xmlns="http://checkout.google.com/schema/2" google-order-number="$goid">
    <reason>{$cancel_reason}</reason>
    <comment>{$cancel_comment}</comment>
</cancel-order>
XML;
            break;
        }

        case 'process': {

            // Send 'process-order' request

            func_gcheckout_debug("\t+ Sending message: process-order");

            $_xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<process-order xmlns="http://checkout.google.com/schema/2" google-order-number="$goid"/>
XML;
            break;

        }

        case 'deliver': {

            // Send 'deliver-order' request

            $carrier = $order_data['order']['shipping_carrier'];
            $tracking_number = func_google_encode($order_data['order']['tracking']);
            $send_email = (!empty($deliver_send_email) ? 'true' : 'false');

            func_gcheckout_debug("\t+ Sending message: deliver-order");
            func_gcheckout_debug("\t+ Send to email: $send_email");
            func_gcheckout_debug("\t+ Carrier: $carrier");
            func_gcheckout_debug("\t+ Tracking number: $tracking_number");

            $_xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<deliver-order xmlns="http://checkout.google.com/schema/2" google-order-number="$goid">
    <tracking-data>
        <carrier>{$carrier}</carrier>
        <tracking-number>{$tracking_number}</tracking-number>
    </tracking-data>
    <send-email>{$send_email}</send-email>
</deliver-order>
XML;
            break;

        }

        case 'add_tracking': {

            // Send 'add-tracking-data' request

            $carrier = $order_data['order']['shipping_carrier'];
            $tracking_number = func_google_encode($order_data['order']['tracking']);

            func_gcheckout_debug("\t+ Sending message: add-tracking-data");
            func_gcheckout_debug("\t+ Carrier: $carrier");
            func_gcheckout_debug("\t+ Tracking number: $tracking_number");

            $_xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<add-tracking-data xmlns="http://checkout.google.com/schema/2" google-order-number="$goid">
    <tracking-data>
        <carrier>{$carrier}</carrier>
        <tracking-number>{$tracking_number}</tracking-number>
    </tracking-data>
</add-tracking-data>
XML;
            break;

        }

        case 'send_message': {

            // Send 'send-buyer-message' request

            $message = func_google_encode(stripslashes($message));
            $send_email = (!empty($send_email_message) ? 'true' : 'false');

            func_gcheckout_debug("\t+ Sending message: send-buyer-message");
            func_gcheckout_debug("\t+ Send to email: $send_email");
            func_gcheckout_debug("\t+ Message: $message");

            $_xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<send-buyer-message xmlns="http://checkout.google.com/schema/2" google-order-number="$goid">
    <message>{$message}</message>
    <send-email>{$send_email}</send-email>
</send-buyer-message>
XML;
            break;

        }

        case 'archive': {

            // Send 'archive-order' request

            func_gcheckout_debug("\t+ Sending message: archive-order");

            $_xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<archive-order xmlns="http://checkout.google.com/schema/2" google-order-number="$goid" />
XML;
            $change_archived_status = 'Y';

            break;

        }

        case 'unarchive': {

            // Send 'unarchive-order' request

            func_gcheckout_debug("\t+ Sending request: unarchive-order");

            $_xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<unarchive-order xmlns="http://checkout.google.com/schema/2" google-order-number="$goid" />
XML;
            $change_archived_status = 'N';

            break;

        }
        default: {
            $error = 1;
        }

    }

    if (!$error) {

        if (in_array($gcmode, array('charge', 'send_message', 'add_tracking')) && substr($order_data['order']['gcheckout_data']['fulfillment_state'], 0, 3) == 'NEW') {

            // Send 'process-order' request automatically if issued command is 'charge', 'send_message' or 'add_tracking'
            // and order fulfillment state is NEW

            func_gcheckout_debug("\t+ Sending message: process-order");

            $_process_order_xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<process-order xmlns="http://checkout.google.com/schema/2" google-order-number="$goid"/>
XML;

            func_gcheckout_send_xml($_process_order_xml);

        }

        $parsed = func_gcheckout_send_xml($_xml);

        $bill_error = 0;

        if (empty($parsed)) {
            $bill_error = 1;
            $bill_output['code'] = 2;
            $bill_output['billmes'] = "Error: Empty server response";
        } elseif ($error_msg = func_array_path($parsed, "ERROR/ERROR-MESSAGE/0/#")) {
            $bill_error = 1;
            $bill_output['code'] = 2;
            $bill_output['billmes'] = "Google Checkout error: ".$error_msg;

            if (!empty($change_archived_status) && preg_match('/archived/', $error_msg)) {
                // Update 'archived' status of the order
                func_array2update(
                    'gcheckout_orders',
                    array(
                        'archived' => $change_archived_status
                    ),
                    "goid = '$goid'"
                );
            }
        }

    }

    if ($bill_error || $error) {

        // Error: request has not been sent

        func_gcheckout_debug("\t+ ".$bill_output['billmes']);
        $top_message['content'] = $bill_output['billmes'];
        $top_message['type'] = 'E';

    }

    else {

        // Request is successfully sent

        if (!empty($change_archived_status)) {

            // Update 'archived' status of the order
            func_array2update(
                'gcheckout_orders',
                array(
                    'archived' => $change_archived_status
                ),
                "goid = '$goid'"
            );

        }
        elseif ($gcmode == 'charge') {

            $orderids_arr = func_query_column("SELECT orderid FROM $sql_tbl[gcheckout_orders] WHERE goid='$goid'");
            $orderids = implode(',', $orderids_arr);
            $total_cost = func_query_first_cell("SELECT total FROM $sql_tbl[gcheckout_orders] WHERE goid='$goid'");

            // Insert record for order processing to the service table
            func_array2insert(
                'cc_pp3_data',
                array(
                    'ref' => $goid,
                    'param1' => $orderids,
                    'param2' => $total_cost
                ),
                true
            );

        }

        func_gcheckout_debug("\t+ Request has been successfully sent");

        $top_message['content'] = 'Request has been successfully sent';

    }

    func_header_location("order.php?orderid=$orderid");

}

$smarty->assign('gcheckout_admin', $gcheckout_admin);
$smarty->assign('gcheckout_cancel_reason_list', $gcheckout_cancel_reason_list);

?>
