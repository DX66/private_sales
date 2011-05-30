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
 * PayPal CC processing module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: ps_paypal.php,v 1.59.2.3 2011/01/10 13:12:08 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

// $custom variable exists in data POSTed by PayPal:
// 1) callback (POST)
// 2) return from PayPal (GET)
// it contains order_secureid

/**
 * Successful return from PayPal
 */
if ((isset($_GET['mode']) && $_GET['mode'] == 'success') || (isset($_POST['mode']) && $_POST['mode'] == 'success')) {
    require './auth.php';

    $skey = $_GET['secureid'];
    require($xcart_dir.'/payment/payment_ccview.php');
}

if ((isset($_GET['mode']) && $_GET['mode'] == 'cancel') || (isset($_POST['mode']) && $_POST['mode'] == 'cancel')) {
    require './auth.php';

    $skey = $_GET['secureid'];
    $bill_output['code'] = 2;
    $bill_output['billmes'] = "Canceled by the user";

    require $xcart_dir.'/payment/payment_ccend.php';
}
/**
 * Callback by PayPal
 */
elseif ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['payment_type']) && !empty($_POST['payment_type'])) {
    require './auth.php';

    if ($config['paypal_solution'] == 'uk')
        exit;

    x_load('http', 'paypal');

    if (isset($_POST['mc_gross'])) {
        $payment_return = array(
            'total' => $_POST['mc_gross']
        );
    }

    $skey = $_POST['custom'];
    $bill_output['sessid'] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$skey."'");

    if (!empty($_GET['notify_from']) && $_GET['notify_from'] == 'pro') {
        $_processor = 'ps_paypal_pro.php';
    }
    else {
        $_processor = 'ps_paypal.php';
    }

    $module_params = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor='$_processor'");
    $cur = func_paypal_get_currency($module_params);

    $testmode = func_query_first_cell("SELECT testmode FROM $sql_tbl[ccprocessors] WHERE processor='$_processor'");

    $pp_host = ($testmode == 'N' ? "www.paypal.com" : "www.sandbox.paypal.com");

    $https_success = true;
    $https_msg = '';

    if ($config['paypal_solution'] != 'uk') {
        // do PayPal (IPN) background request...
        $post = array();
        foreach ($_POST as $key => $val)
            $post[] = "$key=".func_stripslashes(func_html_entity_decode($val));

        list($a,$result) = func_https_request('POST',"https://$pp_host:443/cgi-bin/webscr?cmd=_notify-validate", $post);
        $is_verified = preg_match('/VERIFIED/i', $result);

        if (empty($a)) {
            // HTTPS client error
            $https_success = false;
            $https_msg = $result;
        }

    } else {
        $is_verified = true;
    }

    if (!$https_success) {
        $bill_message = "Queued: HTTPS client error ($https_msg).";
        $bill_output['code'] = 3;
    } elseif (!$is_verified) {
        $bill_output['code'] = 2;
        $bill_message = "Declined (invalid request)";

    } elseif (!strcasecmp($payment_status,'Completed') || !strcasecmp($payment_status, 'Pending')) {

        $bill_output['code'] = 2;
        if (!strcasecmp($payment_status, 'Pending')) {
            $bill_message = 'Queued';
            $bill_output['code'] = 3;

            // It is pre-authorization response
            if ($transaction_entity == 'auth') {
                if ($_processor == 'ps_paypal.php') {
                    $bill_output['is_preauth'] = true;
                    $extra_order_data = array(
                        'paypal_type' => 'USSTD',
                        'paypal_txnid' => $txn_id,
                        'capture_status' => 'A'
                    );

                } else {
                    exit;
                }
            }

        } elseif (!strcasecmp($payment_status,'Completed') && ($orderids = func_paypal_get_capture_orderid($auth_id))) {

            // Order(s) captured on PayPal backend
            $total = func_query_first_cell("SELECT SUM(total) FROM $sql_tbl[orders] WHERE orderid IN ('" . implode("','", $orderids) . "')");
            if ($cur == $_POST['mc_currency'] && $total == $_POST['mc_gross']) {
                x_load('order');
                func_order_process_capture($orderids);
            }
            exit;

        } elseif ($cur != $_POST['mc_currency']) {
            $bill_message = "Declined: Payment amount mismatch: wrong order currency ( ".$cur." <> ".$_POST['mc_currency']." ).";

        } elseif ($is_verified) {
            $bill_output['code'] = 1;
            $bill_message = 'Accepted';

        } else {
            $bill_message = "Declined (processor error)";
        }

        $_oids = explode("|", func_query_first_cell("SELECT trstat FROM $sql_tbl[cc_pp3_data] WHERE ref='".$skey."'"));
        array_shift($_oids);
        if (!empty($_oids)) {
            foreach($_oids as $_oid)
                func_paypal_update_order($_oid);
        }

    } elseif (!strcasecmp($payment_status, 'Voided')) {

        // Order(s) voided on PayPal backend
        $orderids = func_paypal_get_capture_orderid($auth_id);
        x_load('order');
        func_order_process_void($orderids);
        exit;

    } elseif (!strcasecmp($payment_status, 'Refunded')) {
        // Register Refund transaction
        if (!empty($parent_txn_id))
            func_paypal_reg_refund($parent_txn_id, $txn_id);

        exit;

    } else {
        $bill_message = 'Declined';
        $bill_output['code'] = 2;
    }

    $bill_output['billmes'] = "$bill_message Status: $payment_status (TransID #$txn_id)";
    if (!empty($pending_reason))
        $bill_output['billmes'] .= " Reason: $pending_reason";

    require $xcart_dir.'/payment/payment_ccmid.php';
    require $xcart_dir.'/payment/payment_ccwebset.php';
}
/**
 * Checkout
 */
else {

    if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

    if ($config['paypal_solution'] == 'uk')
        exit;

    x_load('paypal');

    $pp_supported_charsets = array (
        'Big5', 'EUC-JP', 'EUC-KR', 'EUC-TW', 'gb2312', 'gbk', 'HZ-GB-2312', 'ibm-862', 'ISO-2022-CN', 'ISO-2022-JP', 'ISO-2022-KR', 'ISO-8859-1', 'ISO-8859-2', 'ISO-8859-3', 'ISO-8859-4', 'ISO-8859-5', 'ISO-8859-6', 'ISO-8859-7', 'ISO-8859-8', 'ISO-8859-9', 'ISO-8859-13', 'ISO-8859-15', 'KOI8-R', 'Shift_JIS', 'UTF-7', 'UTF-8', 'UTF-16', 'UTF-16BE', 'UTF-16LE', 'UTF16_PlatformEndian', 'UTF16_OppositeEndian', 'UTF-32', 'UTF-32BE', 'UTF-32LE', 'UTF32_PlatformEndian', 'UTF32_OppositeEndian', 'US-ASCII', 'windows-1250', 'windows-1251', 'windows-1252', 'windows-1253', 'windows-1254', 'windows-1255', 'windows-1256', 'windows-1257', 'windows-1258', 'windows-874', 'windows-949', 'x-mac-greek', 'x-mac-turkish', 'x-maccentraleurroman', 'x-mac-cyrillic', 'ebcdic-cp-us', 'ibm-1047'
    );
    foreach ($pp_supported_charsets as $k=>$v) {
        $pp_supported_charsets[$k] = strtolower($v);
    }

    $pp_charset = strtolower($all_languages[$shop_language]['charset']);
    if (!in_array($pp_charset, $pp_supported_charsets)) {
        $pp_charset = "ISO-8859-1";
    }

    $pp_acc = $module_params['param08'];
    $pp_for = $module_params['param09'];
    $pp_curr = func_paypal_get_currency($module_params);
    $pp_prefix = preg_replace("/[ '\"]+/","",$module_params['param06']);
    $pp_ordr = $pp_prefix.join("-",$secure_oid);

    $pp_total = func_paypal_convert_to_BasicAmountType($cart["total_cost"], $pp_curr);

    $pp_host = ($module_params['testmode'] == 'N' ? "www.paypal.com" : "www.sandbox.paypal.com");

    db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($order_secureid)."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");

    // Filling $_location variable depending protocol value
    $res = func_query_first("SELECT protocol FROM $sql_tbl[payment_methods] WHERE paymentid='".$paymentid."'");
    $_location = ($res['protocol'] == 'https') ? $https_location : $http_location;

    $fields = array(
        'charset' => $pp_charset,
        'cmd' => "_ext-enter",
        'custom' => $order_secureid,
        'invoice' => $pp_ordr,
        'redirect_cmd' => '_xclick',
        'item_name' => $pp_for . ' (Order #' . $pp_ordr . ')',
        'mrb' => "R-2JR83330TB370181P",
        'pal' => 'RDGQCFJTT6Y6A',
        'rm' => '2',
        'email' => $userinfo['email'],
        'first_name' => $bill_firstname,
        'last_name' => $bill_lastname,
        'business' => $pp_acc,
        'amount' => $pp_total,
        'tax_cart' => 0,
        'shipping' => 0,
        'handling' => 0,
        'weight_cart' => 0,
        'currency_code' => $pp_curr,
        'return' => $_location."/payment/ps_paypal.php?mode=success&secureid=$order_secureid",
        'cancel_return' => $_location.DIR_CUSTOMER."/payment/ps_paypal.php?mode=cancel&secureid=$order_secureid",
        'shopping_url' => $_location.DIR_CUSTOMER."/payment/ps_paypal.php?mode=cancel&secureid=$order_secureid",
        'notify_url' => $_location.'/payment/ps_paypal.php',
        'upload' => 1,
        'bn' => "x-cart"
    );

    if ($config['paypal_address_override'] == 'Y') {
        $fields['address_override'] = 1;
    }

    $u_phone = preg_replace('![^\d]+!', '', $userinfo["phone"]);
    if (!empty($u_phone)) {
        if ($userinfo['b_country'] == 'US') {
            $fields['night_phone_a'] = substr($u_phone, -10, -7);
            $fields['night_phone_b'] = substr($u_phone, -7, -4);
            $fields['night_phone_c'] = substr($u_phone, -4);
        } else {
            $fields['night_phone_b'] = substr($u_phone, -10);
        }
    }

    if ($module_params['use_preauth'] == 'Y')
        $fields['paymentaction'] = 'authorization';

    x_load('user');
    $areas = func_get_profile_areas(empty($login) ? 'H' : 'C');

    if ($areas['B']) {
        $fields['country'] = $userinfo['b_country'];
        $fields['state'] = ($userinfo['b_country'] == 'US') ? $userinfo['b_state'] : $userinfo['b_statename'];

        if (!empty($userinfo['b_address']))
            $fields['address1'] = $userinfo["b_address"];
        if (!empty($userinfo['b_address_2']))
            $fields['address2'] = $userinfo["b_address_2"];
        if (!empty($userinfo['b_city']))
            $fields['city'] = $userinfo["b_city"];
        if (!empty($userinfo['b_zipcode']))
            $fields['zip'] = $userinfo["b_zipcode"];
    }

    if (!$areas['S'] && !$areas['B']) {
        $fields['no_shipping'] = 1;
    }

    $address_fields = array('address1', 'address2', 'city', 'zip');
    foreach($address_fields as $k) {
        if (empty($fields[$k]))
            $fields[$k] = 'n/a';
    }

    if ($config['paypal_suppress_encoding'] == 'Y') {
        if (preg_match("/UTF\-8/i", $pp_charset)) {
            foreach ($fields as $k=>$v) {
                $fields[$k] = utf8_decode($v);
            }
            $fields['charset'] = 'ISO-8859-1';
        }
        foreach ($fields as $k=>$field) {
            for ($i = 0; $i < strlen($field); $i++) {
                if (ord($field{$i}) > 127) {
                    // replace encoding-specific characters from the fields
                    $field{$i} = "?";
                }
            }
            $fields[$k] = $field;
        }
    }

    func_create_payment_form("https://$pp_host/cgi-bin/webscr", $fields, 'PayPal');
}
exit;

?>
