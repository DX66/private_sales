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
 * "CyberSource - Hosted Order Page" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_csrc_form.php,v 1.14.2.1 2011/01/10 13:12:05 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!isset($REQUEST_METHOD)) {
    $REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
}

if ($REQUEST_METHOD == 'POST' && !empty($_POST['signedFields'])) {

    require './auth.php';

    x_load('payment');
    func_pm_load('cc_csrc_form');

    $result = func_cc_csrc_form_verify_signature($_POST);

    include $xcart_dir.'/payment/cc_csrc.resp_codes.php';

    $bill_output['sessid'] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref = '".$orderNumber."'");

    if ($result) {
        $bill_output['code'] = (strtoupper($decision) == 'ACCEPT') ? 1 : 2;
        $bill_output['billmes'] = $reason[$reasonCode]."(code ".$reasonCode.")'.'\n";
    } else {
        $bill_output['code'] = 2;
        $bill_output['billmes'] = "Signature check failed";
    }

    if ($requestID) {
        $bill_output['billmes'] .= "RequestID: ".$requestID."\n";
    }
    if ($orderNumber) {
        $bill_output['billmes'] .= "OrderNumber: ".$orderNumber."\n";
    }
    if ($ccAuthReply_avsCode) {
        $bill_output['avsmes'] = $avserr[$ccAuthReply_avsCode];
    }
    if ($ccAuthReply_cvCode) {
        $bill_output['cvvmes'] = $cvverr[$ccAuthReply_cvCode];
    }

    if ($bill_output['code'] == 1 && $orderPage_transactionType == 'authorization') {
        $bill_output['is_preauth'] = true;
    }

    $skey = $orderNumber;
    require $xcart_dir.'/payment/payment_ccend.php';

} else {

    if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

    $cs_merchant_id = $module_params['param01'];
    $cs_serial_num    = $module_params['param02'];
    $cs_mod_curr    = $module_params['param03'];
    $cs_order_pref  = $module_params['param04'];
    $cs_public_key    = $module_params['param05'];
    $cs_secret_key    = $module_params['param06'];

    $ordr        = $cs_order_pref.join("-", $secure_oid);
    $timestamp    = func_cc_csrc_form_get_timestamp();
    $total_cost    = $cart['total_cost'];

    $is_preauth = ($module_params['use_preauth'] == 'Y' || func_is_preauth_force_enabled($secure_oid));

    $trantype        = $is_preauth ? 'authorization' : 'sale';
    $signature_data = $cs_merchant_id.$total_cost.$cs_mod_curr.$timestamp.$trantype;
    $signature        = func_cc_csrc_form_generate_signature($signature_data);

    $cs_callback_url        = $current_location.'/payment/'.$module_params['processor'];
    $cs_return_url_success    = $current_location.DIR_CUSTOMER."/cart.php?mode=order_message&orderids=".implode(",", $secure_oid);
    $cs_return_url_decline    = $current_location.DIR_CUSTOMER."/error_message.php?error=error_ccprocessor_error";
    $cs_return_link_text    = "Return to ".$config['Company']['company_name'];

    $csrc_url = "https://orderpage".(($module_params['testmode'] == 'Y') ? 'test' : '').'.ic3.com/hop/orderform.jsp';

    if (!$duplicate) {
        db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref, sessionid, trstat) VALUES ('".addslashes($ordr)."', '".$XCARTSESSID."', 'GO|".implode("|", $secure_oid)."')");
    }

    $cs_user_info = array();
    foreach ($userinfo as $k => $v) {
        if (is_array($v)) continue;
        $cs_user_info[$k] = htmlspecialchars($v);
    }

    $post = array(
        'merchantID'=> $cs_merchant_id,

        'orderPage_serialNumber'            => $cs_serial_num,
        'orderNumber'                        => $ordr,
        'orderPage_transactionType'            => $trantype,
        'orderPage_timestamp'                => $timestamp,
        'orderPage_version'                    => '4',
        'orderPage_signaturePublic'            => $signature,
        'orderPage_sendMerchantURLPost'        => 'true',
        'orderPage_merchantURLPostAddress'    => $cs_callback_url,
        'orderPage_receiptResponseURL'        => $cs_return_url_success,
        'orderPage_receiptLinkText'            => $cs_return_link_text,
        'orderPage_declineResponseURL'        => $cs_return_url_decline,
        'orderPage_declineLinkText'            => $cs_return_link_text,

        'billTo_firstName'                    => $cs_user_info['firstname'],
        'billTo_lastName'                    => $cs_user_info['lastname'],
        'billTo_street1'                    => $cs_user_info['b_address'],
        'billTo_city'                        => $cs_user_info['b_city'],
        'billTo_state'                        => $cs_user_info['b_state'],
        'billTo_postalCode'                    => $cs_user_info['b_zipcode'],
        'billTo_country'                    => $cs_user_info['b_country'],
        'billTo_phoneNumber'                => $cs_user_info['phone'],
        'billTo_email'                        => $cs_user_info['email'],

        'amount'                            => $total_cost,
        'currency'                            => $cs_mod_curr,
    );

    func_create_payment_form($csrc_url, $post, 'CyberSource');

    exit();

}

?>
