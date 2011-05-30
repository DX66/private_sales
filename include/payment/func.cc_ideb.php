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
 * Functions for "DIBS" payment module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.cc_ideb.php,v 1.16.2.1 2011/01/10 13:11:52 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

/**
 * Return error message for iDeb payment. Type should be one of the following values:
 * A - authorization, C - capture or cancel
 */
function func_cc_ideb_get_error($code, $type = 'A', $message = "")
{
    $errcodes = array(
    'A' => array(
        '0' => "Rejected by acquirer",
        '1' => "Communication problems",
        '2' => "Error in the parameters sent to the DIBS server",
        '3' => "Error at the acquirer",
        '4' => "Credit card expired",
        '5' => "Your shop does not support this credit card type, the credit card type could not be identified, or the credit card number was not modulus correct",
        '6' => "Instant capture failed",
        '7' => "The order number (orderid) is not unique",
        '8' => "There number of amount parameters does not correspond to the number given in the split parameter",
        '9' => "Control numbers (cvc) are missing",
        '10' => "The credit card does not comply with the credit card type",
        '11' => "Declined by DIBS Defender"
    ),
    'C' => array(
        '1' => "No response from acquirer",
        '2' => "Error in parameters sent to the DIBS server",
        '3' => "Credit card expired",
        '4' => "Rejected by acquirer",
        '5' => "Authorisation older than 7 days",
        '6' => "Transaction status on the DIBS server does not allow capture",
        '7' => "Amount too high",
        '8' => "Amount is zero",
        '9' => "Order number (orderid) does not correspond to the authorisation order number",
        '10' => "Re-authorisation of the transaction was rejected",
        '11' => "Not able to communicate with the acquirer",
        '15' => "Capture was blocked by DIBS"
    ));

    if (isset($errcodes[$type][$code])) {
        $msg = $errcodes[$type][$code].(!empty($message) ? ": ".$message : '')."; code: ".$code;
    } else {
        $msg = (!empty($message) ? $message.". " : '')."Code: ".$code;
    }
    return $msg;
}

/**
 * Do DIBS Capture transactions
 */
function func_cc_ideb_do_capture($order)
{
    global $sql_tbl;

    $module_params = func_query_first("select param01 from $sql_tbl[ccprocessors] where processor='cc_ideb.php'");

    list($tid, $orderid) = explode("|", $order['order']['extra']['ideb_tid'], 2);

    $post = array(
        "merchant=".$module_params['param01'],
        "amount=".intval($order['order']['total']),
        "transact=".intval($tid),
        "orderid=".$orderid,
        "textreply=yes"
    );

    list($a, $return) = func_https_request('POST', "https://payment.architrade.com/cgi-bin/capture.cgi", $post);

    $ret = array();
    parse_str($return, $ret);

    $status = $ret['result'] == 0;
    $err_msg = (!$status) ? func_cc_ideb_get_error($ret['result'], 'C') : '';
    $extra = array(
        'name' => 'ideb_tid',
        'value' => $order['order']['extra']['ideb_tid']
    );

    return array($status, $err_msg, $extra);
}

/**
 * Do DIBS Cancel transaction
 */
function func_cc_ideb_do_void($order)
{
    global $sql_tbl;

    $module_params = func_query_first("SELECT param01, param06 FROM $sql_tbl[ccprocessors] WHERE processor = 'cc_ideb.php'");
    $login = $module_params['param01'];
    $password = $module_params['param06'];

    list($tid, $orderid) = explode("|", $order['order']['extra']['ideb_tid'], 2);

    $post = array(
        "merchant=".$login,
        "amount=".intval($order['order']['total']),
        "transact=".intval($tid),
        "orderid=".$orderid,
        "textreply=yes"
    );

    list($a, $return) = func_https_request('POST', "https://$login:$password@payment.architrade.com/cgi-adm/cancel.cgi", $post);
    $ret = array();
    parse_str($return, $ret);

    $status = $ret['status'] == 'ACCEPTED';
    $err_msg = (!$status) ? func_cc_ideb_get_error($ret['reason'], 'C') : '';
    $extra = array(
        'name' => 'ideb_tid',
        'value' => $order['order']['extra']['ideb_tid']
    );

    return array($status, $err_msg, $extra);
}

?>
