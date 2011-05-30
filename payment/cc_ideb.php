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
 * "DIBS" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_ideb.php,v 1.24.2.1 2011/01/10 13:12:06 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (defined('XCART_START')) {

    // Redirect to gateway

    $ordr = str_replace(" ", '', $module_params['param05'].join("-",$secure_oid));
    if (!$duplicate)
        db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."','".implode("|", $secure_oid)."')");

    $fields = array(
        'merchant' => $module_params['param01'],
        'amount' => (100*$cart['total_cost']),
        'currency' => $module_params['param04'],
        'orderid' => $ordr,
        'accepturl' => $current_location.'/payment/cc_ideb.php',
        'cancelurl' => $current_location."/payment/cc_ideb.php?orderid=".$ordr,
        'declineurl' => $current_location."/payment/cc_ideb.php?orderid=".$ordr,
        'cardno' => $userinfo['card_number'],
        'expmon' => substr($userinfo['card_expire'], 0, 2),
        'expyear' => substr($userinfo['card_expire'], 2, 2),
        'cvc' => $userinfo['card_cvv2'],
        'uniqueoid' => 'true',
        'ip' => func_get_valid_ip($REMOTE_ADDR)
    );

    if ($module_params['testmode'] == 'Y')
        $fields['test'] = 'yes';

    if (is_visa($userinfo['card_number']) || is_mc($userinfo['card_number']))
        func_create_payment_form("https://payment.architrade.com/cgi-ssl/3dsecure.cgi", $fields, 'DIBS');
    else
        func_create_payment_form("https://payment.architrade.com/cgi-ssl/auth.cgi", $fields, 'DIBS');

} else {

    // Return

    require './auth.php';

    if (!func_is_active_payment('cc_ideb.php'))
        exit;

    x_load('payment');

    func_pm_load('cc_ideb');

    if (!empty($orderid)) {
        $pp3_data = func_query_first("SELECT sessionid, trstat FROM $sql_tbl[cc_pp3_data] WHERE ref = '".$orderid."'");
        $bill_output['sessid'] = $pp3_data['sessionid'];
        $secure_oid = explode("|", $pp3_data['trstat']);
    }

    if (empty($authkey) || !empty($reason) || empty($orderid)) {
        $bill_output['code'] = 2;
        if (empty($orderid)) {
            $bill_output['billmes'] = "Error: Your order was lost";
        } elseif (!empty($reason)) {
            $bill_output['billmes'] = func_cc_ideb_get_error($reason, 'A', $message);
        }
    } else {
        $module_params = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor = 'cc_ideb.php'");

        $bill_output['code'] = 1;
        $payment_return = array(
            'cost' => $amount/100,
            'currency' => $currency,
            '_currency' => $module_params['param04']
        );

        if (($module_params['use_preauth'] != "Y") && (!func_is_preauth_force_enabled($secure_oid))) {
            x_load('http');
            $post = array(
                "merchant=".$module_params['param01'],
                "amount=".intval($amount),
                "transact=".intval($transact),
                "orderid=".$orderid,
                "textreply=yes"
            );

            list($a, $return) = func_https_request('POST', "https://payment.architrade.com/cgi-bin/capture.cgi", $post);

            $ret = array();
            parse_str($return, $ret);

        } else {
            $bill_output['is_preauth'] = true;
            $extra_order_data = array(
                'ideb_tid' => $transact."|".$orderid,
                'capture_status' => 'A',
            );
            $ret['result'] = 0;
        }

        if (!isset($ret['result'])) {
            $bill_output['code'] = 2;
            $bill_output['billmes'] = "Internal error.";

        } elseif ($ret['result'] == 0) {
            $bill_output['code'] = 1;

        } else {
            $bill_output['code'] = 2;
            $bill_output['billmes'] = func_cc_ideb_get_error($ret['result'], 'C', $message);
        }
    }

    require_once $xcart_dir.'/payment/payment_ccend.php';
}
exit;
?>
