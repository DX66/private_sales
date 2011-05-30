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
 * "Sage Pay Go - Form protocol" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_protx.php,v 1.61.2.1 2011/01/10 13:12:07 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!isset($REQUEST_METHOD))
    $REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];

if ($REQUEST_METHOD == 'GET' && isset($_GET['crypt'])) {

    require './auth.php';

    x_load('payment');
    func_pm_load('cc_protx_common');

    $pass = func_query_first_cell("SELECT param02 FROM $sql_tbl[ccprocessors] WHERE processor='cc_protx.php'");
    $response = array();
    parse_str(simpleXor(base64Decode($crypt), $pass), $response);
    $bill_output['sessid'] = func_query_first_cell("select sessionid from $sql_tbl[cc_pp3_data] where ref='".func_addslashes($response['VendorTxCode'])."'");

    if (trim($response['Status']) == "OK") {
        $bill_output['code'] = 1;
        $bill_output['billmes'] = "AuthNo: ".$response['TxAuthNo'].";\n";
    } else {
        $bill_output['code'] = 2;
        $bill_output['billmes'] = "Status: ".$response['StatusDetail']." (".trim($response['Status']).")\n";
    }

    if (!empty($response['VPSTxId'])) {
        $bill_output['billmes'] .= " (TxID: ".trim($response['VPSTxId']).")\n";
    }

    if (!empty($response['AVSCV2'])) {
        $bill_output['billmes'] .= " (AVS/CVV2: {".trim($response['AVSCV2'])."})\n";
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

    if (isset($response['Amount'])) {
        $payment_return = array(
            'total' => str_replace(",", '',$response['Amount'])
        );
    }

    require($xcart_dir.'/payment/payment_ccend.php');

} else {

        if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

        func_pm_load('cc_protx_common');

        $pp_merch = $module_params['param01'];
        $pp_pass = $module_params['param02'];
        $pp_curr = $module_params['param03'];
        // Determine request URL (simulator, test server or live server)
        switch ($module_params['testmode']) {
        case 'S':
            $pp_test = 'https://test.sagepay.com/Simulator/VSPFormGateway.asp';
            break;
        case 'Y':
            $pp_test = 'https://test.sagepay.com/gateway/service/vspform-register.vsp';
            break;
        default:
            $pp_test = 'https://live.sagepay.com/gateway/service/vspform-register.vsp';
        }
        $pp_shift = preg_replace("/[^\w\d_-]/S", '', $module_params['param05']);
        $_orderids = join("-",$secure_oid);

        if (!$duplicate)
            db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($pp_shift.$_orderids)."','".$XCARTSESSID."')");

        $crypt['VendorTxCode'] = $pp_shift.$_orderids;
        $crypt['ReferrerID'] = "653E8C42-AD93-4654-BB91-C645678FA97B";
        $crypt['Amount'] = price_format($cart['total_cost']);
        $crypt['Currency'] = $pp_curr;
        $crypt['Description'] = "Your Cart";
        $crypt['SuccessURL'] = $current_location.'/payment/cc_protx.php';
        $crypt['FailureURL'] = $current_location.'/payment/cc_protx.php';

        $crypt['CustomerName'] = $bill_name;
        $crypt['CustomerEMail'] = $userinfo['email'];
        $crypt['VendorEMail'] = $config['Company']['orders_department'];
        $crypt['SendEMail'] = 1;

        // Billing information
        $crypt['BillingSurname'] = $bill_lastname;
        $crypt['BillingFirstnames'] = $bill_firstname;
        $crypt['BillingAddress1'] = $userinfo['b_address'];
        if (!empty($userinfo['b_address_2']))
            $crypt['BillingAddress2'] = $userinfo['b_address_2'];
        $crypt['BillingCity'] = $userinfo['b_city'];
        $crypt['BillingPostCode'] = $userinfo['b_zipcode'];
        $crypt['BillingCountry'] = $userinfo['b_country'];
        if ($userinfo['b_country'] == 'US' && !empty($userinfo['b_state']) && $userinfo['b_state'] != 'Other')
            $crypt['BillingState'] = $userinfo['b_state'];

        // Shipping information
        $crypt['DeliverySurname'] = $ship_lastname;
        $crypt['DeliveryFirstnames'] = $ship_firstname;
        $crypt['DeliveryAddress1'] = $userinfo['s_address'];
        if (!empty($userinfo['s_address_2']))
            $crypt['DeliveryAddress2'] = $userinfo['s_address_2'];
        $crypt['DeliveryCity'] = $userinfo['s_city'];
        $crypt['DeliveryPostCode'] = $userinfo['s_zipcode'];
        $crypt['DeliveryCountry'] = $userinfo['s_country'];
        if ($userinfo['s_country'] == 'US' && !empty($userinfo['s_state']) && $userinfo['s_state'] != 'Other')
            $crypt['DeliveryState'] = $userinfo['s_state'];

        $crypt['Basket'] = func_cc_protx_get_basket();

        $crypt['AllowGiftAid'] = '0';
        $crypt['ApplyAVSCV2'] = $module_params['param06'];
        $crypt['Apply3DSecure'] = $module_params['param07'];

        // Tide up the entire values
        $crypt = func_sagepay_clean_inputs($crypt);

        $crypt_str = join("&",$crypt);
        func_create_payment_form(
            $pp_test,
            array(
                'VPSProtocol' => '2.23',
                'Vendor' => $pp_merch,
                'TxType' => 'PAYMENT',
                'Crypt' => base64Encode(simpleXor($crypt_str, $pp_pass))
            ),
            "Sage Pay"
        );
}
exit;

?>
