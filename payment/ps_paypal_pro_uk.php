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
 * PayPal Website Payments Pro (2.0 version; for UK and US)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: ps_paypal_pro_uk.php,v 1.33.2.6 2011/02/24 13:50:32 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

$pp_customer_url = $module_params['test_mode'] == "N" ? "https://www.paypal.com" : "https://www.sandbox.paypal.com";

$avs_codes = array (
    'Y' => 'Match',
    'N' => "No match",
    'X' => "The cardholders bank does not support this service"
);

$cvv_codes = array (
    'Y' => 'Match',
    'N' => "No match",
    'X' => "The cardholders bank does not support this service"
);

if ($REQUEST_METHOD == 'GET' && $mode == 'express') {
    // start express checkout

    x_session_register('paypal_begin_express');
    $paypal_begin_express = false;
    x_session_save('paypal_begin_express');

    x_session_register('paypal_payment_id');
    x_session_register('paypal_mode');

    $paypal_payment_id = $payment_id;
    $paypal_mode = 'express';

    $result = func_paypal_sec_uk($paypal_payment_id, $pp_total, $userinfo);
    if ($result['status'] == 'error') {
        $top_message = array(
            'type' => 'E',
            'content' => $result['error']
        );
        func_header_location($xcart_catalogs['customer'].'/cart.php?mode=checkout');
    }

    x_session_register('paypal_token_ttl');
    $paypal_token_ttl = XC_TIME;

    // move to the PayPal
    func_header_location($result['redirect_url']);

} elseif ($REQUEST_METHOD == 'GET' && !empty($_GET['token'])) {
    // return from PayPal
    // send GetExpressCheckoutDetailsRequest

    x_session_register('paypal_payment_id');

    $result = func_paypal_gec_uk($paypal_payment_id, $_GET['token']);

    if ($result['status'] == 'error') {
        $top_message = array(
            'type' => 'E',
            'content' => $result['error']
        );
        func_header_location($xcart_catalogs['customer'].'/cart.php?mode=checkout');
    }

    $state_err = 0;

    $address = array (
        'address' => preg_replace('![\s\n\r]+!s', ' ', $result['shiptostreet']),
        'city' => $result['shiptocity'],
        'state' => func_paypal_detect_state($result['shiptocountry'], $result['shiptostate'], $state_err),
        'country' => $result['shiptocountry'],
        'zipcode' => $result['shiptozip']
    );

    if (!empty($result['shiptostreet2']))
        $address['address'] .= "\n".$result['shiptostreet2'];

    if ($config['General']['use_counties'] == 'Y') {
        $address['county'] = func_query_first_cell(
            "SELECT $sql_tbl[counties].countyid FROM $sql_tbl[counties], $sql_tbl[states]"
            . " WHERE $sql_tbl[counties].stateid = $sql_tbl[states].stateid"
            . " AND $sql_tbl[states].code = '" . addslashes($address['state']) . "'"
            . " AND $sql_tbl[states].country_code = '" . addslashes($address['zipcode']) . "'"
            . " ORDER BY $sql_tbl[states].state, $sql_tbl[counties].county");
    }

    x_session_register('login');
    x_session_register('login_type');
    x_session_register('logged_userid');

    x_load('user');
    if (!empty($login) && $login_type == 'C') {
        
        $cart = func_set_cart_address($cart, 'S', $address);

    }
    elseif ($config['General']['enable_anonymous_checkout'] == 'Y') {


        // Fill-in anonymous customer profile

        $pp_anon_user = array (
            'title'         => '', // unknown
            'firstname'     => $result['firstname'],
            'lastname'      => $result['lastname'],
            'email'         => $result['email'],
            'status'        => 'Y',
            'referer'       => @$RefererCookie,
            'address'       => array(
                'B' => array(
                    'firstname' => $result['firstname'],
                    'lastname'  => $result['lastname']
                ),
                'S' => array(
                    'firstname' => $result['firstname'],
                    'lastname'  => $result['lastname']
                ),
            )
        );

        foreach ($address as $k => $v) {
            $pp_anon_user['address']['B'][$k] =
            $pp_anon_user['address']['S'][$k] = $v;
        }

        x_load('crypt');
        // save anonymous customer info in session
        func_set_anonymous_userinfo($pp_anon_user);

    }
    else {
        // Display a warning message about expired session
        $top_message = array(
            'type' => 'E',
            'content' => func_get_langvar_by_name('txt_paypal_expired_session_warn')
        );
        func_header_location($xcart_catalogs['customer'] . '/login.php');
    }

    x_session_register('paypal_token');
    x_session_register('paypal_express_details');
    $paypal_token = $result['token'];
    $paypal_express_details = $result;

    switch ($state_err) {
        case 1:
            $top_message = array(
                'type' => 'W',
                'content' => func_get_langvar_by_name('lbl_paypal_wrong_country_note')
            );
            break;

        case 2:
            $top_message = array(
                'type' => 'W',
                'content' => func_get_langvar_by_name('lbl_paypal_wrong_state_note')
            );
    }

    func_header_location($xcart_catalogs['customer'] . '/cart.php?paymentid=' . $paypal_payment_id . '&mode=checkout');

} elseif ($REQUEST_METHOD == 'POST' && $_POST["action"] == 'place_order' && $pp_dp_allowed) {

    // do DirectPayment

    db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($order_secureid)."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");

    $result = func_paypal_sdp_uk($paymentid, $userinfo, $cart, $order_secureid, join("-",$secure_oid), $cmpi_result);

    if ($result['status'] == 'success') {
        $bill_output['code'] = 1;
        $bill_message = 'Accepted ('.$result['respmsg'].')';
    } else {
        $bill_output['code'] = 2;
        $bill_output['hide_mess'] = true;
        $bill_message = "Reason: ".$result['error'];

        if (isset($result['error_code']) && in_array($result['error_code'], array('12','22','23','24'))) {
            $bill_output['hide_mess'] = false;
        } else {
            $bill_output['is_error'] = true;
        }
    }

    $bill_message .= "\nPayPal method: PayPal Website Payments Pro PayFlow Edition / Direct Payment";

    $bill_output['billmes'] = $bill_message;

    $bill_output['avsmes'] = '';

    if (isset($result['avsaddr']))
        $bill_output['avsmes'] .= "AVS address: ".(empty($avs_codes[$result['avsaddr']]) ? "Code: ".$result['avsaddr'] : $avs_codes[$result['avsaddr']])."; ";

    if (isset($result['avszip']))
        $bill_output['avsmes'] .= "AVS zipcode: ".(empty($avs_codes[$result['avszip']]) ? "Code: ".$result['avszip'] : $avs_codes[$result['avszip']])."; ";

    if (isset($result['cvv2match']))
        $bill_output['cvvmes'] = (empty($cvv_codes[$result['cvv2match']]) ? "Code: ".$result['cvv2match'] : $cvv_codes[$result['cvv2match']]);

    if ($pp_final_action != 'Sale')
        $bill_output['is_preauth'] = true;

    $extra_order_data = array(
        'pnref' => $result['pnref'],
        'paypal_type' => 'UKDP',
        'capture_status' => $pp_final_action != 'Sale' ? 'A' : ''
    );

} elseif ($REQUEST_METHOD == 'POST' && $_POST["action"] == 'place_order') {

    // Finisn ExpressCheckout

    db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($order_secureid)."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");

    x_session_register('paypal_express_details');

    $result = func_paypal_dec_uk(
        $paymentid,
        $paypal_express_details['token'],
        $paypal_express_details['payerid'],
        func_paypal_convert_to_BasicAmountType($cart['total_cost']),
        $userinfo,
        join("-",$secure_oid),
        $order_secureid
    );

    if ($result['status'] == 'success') {
        $bill_output['code'] = 1;
        $bill_message = 'Accepted ('.$result['respmsg'].')';
    } else {
        $bill_output['code'] = 2;
        $bill_output['hide_mess'] = true;
        $bill_message = "Reason: ".$result['error'];

        if (isset($result['error_code']) && in_array($result['error_code'], array('12','22','23','24'))) {
            $bill_output['hide_mess'] = false;
        } else {
            $bill_output['is_error'] = true;
        }

    }

    $bill_output['billmes'] = $bill_message;

    $bill_output['avsmes'] = '';

    if (isset($result['avsaddr']))
        $bill_output['avsmes'] .= "AVS address: ".(empty($avs_codes[$result['avsaddr']]) ? "Code: ".$result['avsaddr'] : $avs_codes[$result['avsaddr']])."; ";

    if (isset($result['avszip']))
        $bill_output['avsmes'] .= "AVS zipcode: ".(empty($avs_codes[$result['avszip']]) ? "Code: ".$result['avszip'] : $avs_codes[$result['avszip']])."; ";

    if (isset($result['cvv2match']))
        $bill_output['cvvmes'] = (empty($cvv_codes[$result['cvv2match']]) ? "Code: ".$result['cvv2match'] : $cvv_codes[$result['cvv2match']]);

    if ($pp_final_action != 'Sale')
        $bill_output['is_preauth'] = true;

    $extra_order_data = array(
        'pnref' => $result['pnref'],
        'paypal_type' => 'UKEC',
        'capture_status' => $pp_final_action != 'Sale' ? 'A' : ''
    );
}
?>
