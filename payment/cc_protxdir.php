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
 * SagePay direct integration
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_protxdir.php,v 1.59.2.2 2011/01/10 13:12:07 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!isset($REQUEST_METHOD)) {
    $REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
}
if ($REQUEST_METHOD == 'POST' && !empty($_POST['PaRes']) && !empty($_POST['MD'])) {
    require './auth.php';

    if (defined('PROTXDIR_DEBUG')) {
        func_pp_debug_log('protxdir', 'R', "3-D Secure callback\n" . $_POST);
    }

    $md = $_POST['MD'];
    $post = array();
    $post['MD'] = $md;
    $post['PARes'] = $_POST['PaRes'];
    $bill_output['sessid'] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$_POST['MD']."'");
    $secure_verified_3d = true;
    x_session_register('module_params');
}

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('http');

$pp_merch = $module_params['param01'];
$pp_curr = $module_params['param03'];
$pp_shift = $module_params['param05'];

if (empty($secure_oid)) {
    x_session_register('secure_oid');
}

$_orderids = join("-",$secure_oid);

// Determine request URL (simulator, test server or live server)
switch ($module_params['testmode']) {
    case 'S':
        $pp_test = 'https://test.sagepay.com:443/Simulator/VSPDirectGateway.asp';
        break;
    case 'Y':
        $pp_test = 'https://test.sagepay.com:443/gateway/service/vspdirect-register.vsp';
        break;
    default:
        $pp_test = 'https://live.sagepay.com:443/gateway/service/vspdirect-register.vsp';
}

x_session_register('already_posted');
func_pm_load('cc_protx_common');

if ($secure_verified_3d && $already_posted) {
    func_header_location($current_location.DIR_CUSTOMER.'/home.php');
    exit;

} else if ($secure_verified_3d && !$already_posted) {
    // Determine 3-D Secure callback URL (simulator, test server or live server)
    switch ($module_params['testmode']) {
    case 'S':
        $pp_test = 'https://test.sagepay.com:443/Simulator/VSPDirectCallback.asp';
        break;
    case 'Y':
        $pp_test = 'https://test.sagepay.com:443/gateway/service/direct3dcallback.vsp';
        break;
    default:
        $pp_test = 'https://live.sagepay.com:443/gateway/service/direct3dcallback.vsp';
    }

    $post = func_sagepay_clean_inputs($post);

    if (defined('PROTXDIR_DEBUG')) {
        func_pp_debug_log('protxdir', 'I', "Determine 3-D Secure callback\n" . $post);
    }

    list($a, $return) = func_https_request('POST', $pp_test, $post);

    if (defined('PROTXDIR_DEBUG')) {
        func_pp_debug_log('protxdir', 'R', "Determine 3-D Secure callback\n" . $return);
    }

    $already_posted = true;

} else {

    $already_posted = false;

    $valid_cc_types = array("VISA", "MC", 'DELTA', 'SOLO', 'MAESTRO', 'UKE', 'AMEX', 'DC', 'JCB');

    if (is_visa($userinfo['card_number'])) {
        if ($userinfo['card_type'] != 'UKE')
            $userinfo['card_type'] = 'VISA';
    } elseif (is_switch($userinfo['card_number']))
        $userinfo['card_type'] = 'MAESTRO';
    elseif (is_amex($userinfo['card_number']))
        $userinfo['card_type'] = 'AMEX';
    elseif (is_mc($userinfo['card_number']))
        $userinfo['card_type'] = 'MC';
    elseif (is_solo($userinfo['card_number']))
        $userinfo['card_type'] = 'SOLO';
    elseif (is_delta($userinfo['card_number']))
        $userinfo['card_type'] = 'DELTA';
    elseif (is_dc($userinfo['card_number']))
        $userinfo['card_type'] = 'DINERS';
    elseif (is_jcb($userinfo['card_number']))
        $userinfo['card_type'] = 'JCB';
    elseif (in_array(strtoupper(trim($userinfo['card_type'])), $valid_cc_types))
        $userinfo['card_type'] = strtoupper(trim($userinfo['card_type']));
    elseif (!defined('INNER_PAYMENT')) {
        $top_message = array(
            'content' => func_get_langvar_by_name('txt_protxdir_wrong_cc_type'),
            'type' => 'E',
            'anchor' => 'ccinfo'
        );
        func_header_location($xcart_catalogs['customer']."/cart.php?mode=checkout&err=fields&paymentid=".$paymentid);

    } else {
        $bill_output = array(
            'code' => 2,
            'billmes' => "Declined: ".func_get_langvar_by_name('txt_protxdir_wrong_cc_type')
        );
        return;
    }

    $post = array();
    $post['VPSProtocol'] = '2.23';
    $post['TxType'] = (($module_params['use_preauth'] == 'Y' || func_is_preauth_force_enabled($secure_oid)) ? "DEFERRED" : "PAYMENT");
    $post['Vendor'] = $pp_merch;
    $post['VendorTxCode'] = $pp_shift.$_orderids;
    $post['ReferrerID'] = "653E8C42-AD93-4654-BB91-C645678FA97B";
    $post['Amount'] = $cart['total_cost'];
    $post['Currency'] = $pp_curr;
    $post['Description'] = "Your Cart";

    // Card info
    $post['CardHolder'] = $userinfo['card_name'];
    $post['CardNumber'] = $userinfo['card_number'];
    $post['ExpiryDate'] = $userinfo['card_expire'];
    $post['CV2'] = $userinfo['card_cvv2'];
    $post['CardType'] = $userinfo['card_type'];

    // Billing information
    $post['BillingSurname'] = $bill_lastname;
    $post['BillingFirstnames'] = $bill_firstname;
    $post['BillingAddress1'] = $userinfo['b_address'];
    if (!empty($userinfo['b_address_2']))
        $post['BillingAddress2'] = $userinfo['b_address_2'];
    $post['BillingCity'] = $userinfo['b_city'];
    $post['BillingPostCode'] = $userinfo['b_zipcode'];
    $post['BillingCountry'] = $userinfo['b_country'];
    if ($userinfo['b_country'] == 'US' && !empty($userinfo['b_state']) && $userinfo['b_state'] != 'Other')
        $post['BillingState'] = $userinfo['b_state'];

    // Shipping information
    $post['DeliverySurname'] = $ship_lastname;
    $post['DeliveryFirstnames'] = $ship_firstname;
    $post['DeliveryAddress1'] = $userinfo['s_address'];
    if (!empty($userinfo['s_address_2']))
        $post['DeliveryAddress2'] = $userinfo['s_address_2'];
    $post['DeliveryCity'] = $userinfo['s_city'];
    $post['DeliveryPostCode'] = $userinfo['s_zipcode'];
    $post['DeliveryCountry'] = $userinfo['s_country'];
    if ($userinfo['s_country'] == 'US' && !empty($userinfo['s_state']) && $userinfo['s_state'] != 'Other')
        $post['DeliveryState'] = $userinfo['s_state'];

    $post['CustomerEMail'] = $userinfo['email'];
    $post['Basket'] = func_cc_protx_get_basket();
    $post['GiftAidPayment'] = '0';
    $post['ApplyAVSCV2'] = $module_params['param06'];
    $post['Apply3DSecure'] = $module_params['param07'];
    $post['ClientIPAddress'] = func_get_valid_ip($REMOTE_ADDR);

    if ($userinfo['card_type'] == 'SOLO' || $userinfo["card_type"] == 'SWITCH' || $userinfo["card_type"] == 'MAESTRO') {
        $userinfo['card_issue_no'] = (!isset($userinfo['card_issue_no'])) ? "" : $userinfo['card_issue_no'];
        $post['IssueNumber'] = trim($userinfo['card_issue_no']);
    }

    if (defined('PROTXDIR_DEBUG')) {
        func_pp_debug_log('protxdir', 'I', $post);
    }

    // Tide up the entire values
    $post = func_sagepay_clean_inputs($post);

    list($a, $return) = func_https_request('POST', $pp_test, $post);

    if (defined('PROTXDIR_DEBUG')) {
        func_pp_debug_log('protxdir', 'R', $return);
    }
}
$ret = str_replace("\r\n","&",$return);

$ret_arr = explode("&",$ret);
$response = array();
foreach ($ret_arr as $ret) {
    preg_match("/([^=]+?)=(.+)/", $ret, $matches);
    $response[$matches[1]] = $matches[2];
}

if (trim($response['Status']) == "3DAUTH") {

    x_session_register('module_params');
    db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".trim($response['MD'])."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");

?>
<form action="<?php echo $response['ACSURL']; ?>" method="post" id="3d_form">
<input type="hidden" name="PaReq" value="<?php echo $response['PAReq']; ?>"/>
<input type="hidden" name="TermUrl" value="<?php echo $https_location.'/payment/cc_protxdir.php'?>"/>
<input type="hidden" name="MD" value="<?php echo $response['MD']; ?>"/>
<noscript>
<center><p>Please click button below to Authenticate your card</p><input type="submit" value="Go"/></p></center>
</noscript>
</form>

<script type="text/javascript">
//<![CDATA[
    document.getElementById('3d_form').submit();
//]]>
</script>

<?php

    die("Redirecting to 3D secure server...");

} elseif (trim($response['Status']) == "OK") {
    $bill_output['code'] = 1;
    $bill_output['billmes'] = "AuthNo: ".$response['TxAuthNo']."; SecurityKey: ".$response['SecurityKey']."\n";

    if ($module_params['use_preauth'] == 'Y' || func_is_preauth_force_enabled($secure_oid)) {
        $bill_output['is_preauth'] = true;
        $extra_order_data = array(
            'txnid' => $response['VPSTxId']."\n".$response['SecurityKey']."\n".$response['TxAuthNo']."\n".$pp_shift.$_orderids,
            'capture_status' => 'A'
        );
    }

} else {
    $bill_output['code'] = 2;
    $bill_output['billmes'] = "Status: ".$response['StatusDetail']." (".trim($response['Status']).")\n";
}

if (!empty($response['VPSTxId'])) {
    $bill_output['billmes'].= " (TxID: ".trim($response['VPSTxId']).")\n";
}

if (!empty($response['AVSCV2'])) {
    $bill_output['billmes'].= " (AVS/CVV2: {".trim($response['AVSCV2'])."})\n";
}
if (!empty($response['AddressResult'])) {
    $bill_output['billmes'] .= " (Address: {".trim($response['AddressResult'])."})\n";
}
if (!empty($response['PostCodeResult'])) {
    $bill_output['billmes'] .= " (PostCode: {".trim($response['PostCodeResult'])."})\n";
}
if (!empty($response['CV2Result'])) {
    $bill_output['billmes'] .= " (CV2: {".trim($response['CV2Result'])."})\n";
}
if (!empty($response['3DSecureStatus'])) {
    $bill_output['billmes'] .= " (3D Result: {".trim($response['3DSecureStatus'])."})\n";
}
if ($secure_verified_3d) {
    $skey = $md;
    $bill_output['sessid'] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".trim($skey)."'");

    x_session_save();

    require $xcart_dir.'/payment/payment_ccend.php';
    exit;
}
?>
