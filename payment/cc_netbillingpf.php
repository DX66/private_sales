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
 * "Netbilling gateway - Payment Form" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_netbillingpf.php,v 1.19.2.2 2011/01/10 13:12:06 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/**
 * Netbilling gateway - Payment Form
 */

if (!isset($REQUEST_METHOD))
    $REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];

if ($REQUEST_METHOD == 'POST' && !empty($_POST['Ecom_ConsumerOrderID']) && !empty($_POST['Ecom_Ezic_Response_StatusCode'])) {
    require './auth.php';

    if (!func_is_active_payment('cc_netbillingpf.php'))
        exit;

    $status = $_POST['Ecom_Ezic_Response_StatusCode'];
    $nb_oid = ($_POST['Ecom_ConsumerOrderID']);
    $approval_code = $_POST['approval_code'];
    $md5_hash = $_POST['Ecom_Ezic_Security_HashValue_MD5'];

    x_session_register('secure_oid');

    list($ordr,$a) = explode("-", $nb_oid);
    $tr_data = func_query_first("SELECT sessionid, param1 FROM $sql_tbl[cc_pp3_data] WHERE ref='".$ordr."'");
    if (!empty($tr_data)) {
        $bill_output['sessid'] = $tr_data['sessionid'];
        $md5_initial_hash = $tr_data['param1'];
    }

    if (empty($md5_initial_hash) || empty($md5_hash) || $md5_initial_hash != $md5_hash) {
        // received md5hash does not match initial hash
        $bill_output['code'] = 2;
        $bill_output['billmes'] = "Rejected: the data sent with the transaction does not match the encrypted order integrity data)";
    } elseif ($status != '0' && $status != 'F') {
        $bill_output['code'] = 1;
        $bill_output['billmes'] = $_POST["Ecom_Ezic_Response_AuthMessage"]
            . (!empty($_POST['Ecom_Ezic_Response_AuthCode']) ? "\nAuthorization Code: ". $_POST['Ecom_Ezic_Response_AuthCode'] : "")
            . (!empty($_POST['Ecom_Ezic_Response_TransactionID']) ? "\nTransaction ID: ".$_POST['Ecom_Ezic_Response_TransactionID'] : "");
        $bill_output['avsmes'] = $_POST['Ecom_Ezic_Response_Card_AVSCode'];
        $bill_output['cvvmes'] = $_POST['Ecom_Ezic_Response_Card_VerificationCode'];

        if ($bill_output['code'] == 1 && ($_POST['Ecom_Ezic_Payment_AuthorizationType'] == 'PREAUTH' || func_is_preauth_force_enabled($secure_oid)))
            $bill_output['is_preauth'] = true;
        $extra_order_data = array(
            'nb_oid' => $nb_oid,
            'capture_status' => $bill_output['is_preauth'] ? 'A' : ''
        );
    } else {
        $bill_output['code'] = 2;
        $bill_output['billmes'] = "Declined: ".$_POST['Ecom_Ezic_Response_AuthMessage']." (Reason Code: ".$_POST['Ecom_Ezic_Response_AuthCode']." / Sub: ".$_POST['Ecom_Ezic_Response_StatusSubCode'].")";
    }

    require $xcart_dir.'/payment/payment_ccend.php';

} else {

    if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

    $transaction_type_name = ($module_params['use_preauth'] == 'Y' || func_is_preauth_force_enabled($secure_oid)) ? 'PREAUTH' : 'SALE';

    $accountid = $module_params['param01'];
    $site_tag = $module_params['param02'];
    $hash_key = $module_params['param03'];
    $order_prefix = $module_params['param04'];
    $formid = $module_params['param05'];

    $nb_oid = $order_prefix.join("-",$secure_oid);
    $description = "Order(s) #" . $nb_oid . ", customer: ".$userinfo['firstname'] . " "  . $userinfo['lastname'] . " (" . $userinfo['login'] . ")";

    $md5_hash_fields = "Ecom_Cost_Total Ecom_Receipt_Description Ecom_Ezic_Payment_AuthorizationType Ecom_Ezic_PaymentFormId";
    $md5_hash = md5($hash_key.$cart['total_cost'].$description.$transaction_type_name.$formid);

    if (!$duplicate)
        db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid, param1) VALUES ('".addslashes($nb_oid)."','".$XCARTSESSID."','".addslashes($md5_hash)."')");

    $post = array(
        'Ecom_BillTo_Postal_Name_First' => $userinfo['firstname'],
        'Ecom_BillTo_Postal_Name_Last' => $userinfo['lastname'],
        'Ecom_BillTo_Postal_Street_Line1' => $userinfo['b_address'],
        'Ecom_BillTo_Postal_Street_Line2' => '',
        'Ecom_BillTo_Postal_City' => $userinfo['b_city'],
        'Ecom_BillTo_Postal_StateProv' => $userinfo['b_state'],
        'Ecom_BillTo_Postal_PostalCode' => $userinfo['b_zipcode'],
        'Ecom_BillTo_Postal_CountryCode' => $userinfo['b_country'],
        'Ecom_BillTo_Telecom_Phone_Number' => $userinfo['b_phone'],
        'Ecom_BillTo_Online_Email' => $userinfo['email'],

        'Ecom_ShipTo_Postal_Name_First' => $userinfo['s_firstname'],
        'Ecom_ShipTo_Postal_Name_Last' => $userinfo['s_lastname'],
        'Ecom_ShipTo_Postal_Street_Line1' => $userinfo['s_address'],
        'Ecom_ShipTo_Postal_Street_Line2' => '',
        'Ecom_ShipTo_Postal_City' => $userinfo['s_city'],
        'Ecom_ShipTo_Postal_StateProv' => $userinfo['s_state'],
        'Ecom_ShipTo_Postal_PostalCode' => $userinfo['s_zipcode'],
        'Ecom_ShipTo_Postal_CountryCode' => $userinfo['s_country'],
        'Ecom_ShipTo_Telecom_Phone_Number' => $userinfo['s_phone'],
        'Ecom_ShipTo_Online_Email' => $userinfo['email'],

        'Ecom_Ezic_AccountAndSitetag' => $accountid.":".$site_tag,
        'Ecom_Cost_Total' => $cart['total_cost'],
        'Ecom_Cost_Tax' => $cart['tax_cost'],
        'Ecom_Receipt_Description' => $description,
        'Ecom_Ezic_Misc_Information' => $customer_notes,
        'Ecom_ConsumerOrderID' => $nb_oid,

        'Ecom_Ezic_Fulfillment_ReturnURL' => $https_location.'/payment/cc_netbillingpf.php',
        'Ecom_Ezic_Fulfillment_GiveUpURL' => $https_location.'/payment/cc_netbillingpf.php',
        'Ecom_Ezic_Fulfillment_ReturnMethod' => 'POST',
        'Ecom_Ezic_Payment_AuthorizationType' => $transaction_type_name,
        'Ecom_Ezic_Security_HashFields' => $md5_hash_fields,
        'Ecom_Ezic_Security_HashValue_MD5' => $md5_hash
    );

    func_create_payment_form("https://secure.netbilling.com/gw/native/interactive2.2", $post, $module_params['module_name']);
    exit();
}

?>
