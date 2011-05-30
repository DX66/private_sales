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
 * "SecurePay - Non-Recurring Interface" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_securepay.php,v 1.29.2.1 2011/01/10 13:12:07 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('http');

$avserr = array(
    'A' => "Address (Street) matches, Zip does not.",
    'E' => "AVS Error",
    'G' => "Issuing bank does not subscribe to the AVS system.",
    'N' => "no match on Address or Zip Code.",
    'R' => "Retry, system unavailable or timed out.",
    'S' => "Service not supported by issuer.",
    'U' => "Address information unavailable.",
    'W' => "9 digit Zip matches, Address does not.",
    'X' => "Exact AVS match.",
    'Y' => "Address and 5 digit zip code match.",
    'Z' => "5 digit Zip Code matches, Address does not."
);

if ($REQUEST_METHOD == 'POST' && $_POST['action'] == 'place_order') {
    $transaction_type = ($module_params['use_preauth'] == 'Y' || func_is_preauth_force_enabled($secure_oid)) ? 'A': 'P';
}

$transaction_type_name = func_check_cc_trans ('SecurePay - Non-Recurring Interface', $transaction_type, array("P" => "SALE", "A" => "PREAUTH", "X" => "FORCE", "V" => "VOID"));

$follow_on = in_array($transaction_type_name, array('FORCE', 'VOID'));

if ($follow_on) {
    if ($transaction_type == 'X' && empty($appnum) && preg_match("/\(Approval Code: (\d+)\)/", $order['details'], $preg)) {
        $transaction_id = $preg[1];

    } elseif ($transaction_type == 'V' && empty($vrnum) && preg_match("/\(VoidRecNum: (\d+)\)/", $order['details'], $preg)) {
        $vrnum = $preg[1];
    }
}

$an_login = $module_params['param01'];
$an_prefix = $module_params['param02'];
$trans_key = $module_params['param03'];

$post = array();
$post[] = "Tr_Type=".$transaction_type_name;
$post[] = "MERCH_ID=".$an_login;
$post[] = "AMOUNT=" . (($follow_on) ? $order['order']["total"] : $cart["total_cost"]);
if (!empty($trans_key))
    $post[] = "TransKey=".$trans_key;
$post[] = "NAME=".$userinfo['card_name'];
$post[] = "CC_NUMBER=".$userinfo['card_number'];
$post[] = "MONTH=".substr($userinfo['card_expire'],0,2);
$post[] = "YEAR=".substr($userinfo['card_expire'],2,2);
$post[] = "STREET=".$userinfo['b_address'];
$post[] = "CITY=".$userinfo['b_city'];
$post[] = "STATE=".$userinfo['b_state'];
$post[] = "ZIP=".$userinfo['b_zipcode'];
$post[] = "EMAIL=".(empty($userinfo['email']) ? 'N/A' : $userinfo['email']);

if (!$follow_on) {
    $post[] = "CC_Method=DataEntry";
    $post[] = "AVSREQ=1";
    $post[] = "COMMENT1=".$an_prefix.join("-",$secure_oid);
} else {
    $post[] = ($transaction_type == 'X') ? "App_Num=".$appnum : "VoidRecNum=".$vrnum;
}

list($a, $return) = func_https_request('POST', "https://www.securepay.com:443/secure1/index.asp", $post);

list($Return_Code, $Approv_Num, $Card_Response, $AVS_Response, $VoidRecNum, $temp) = explode(",", $return);

$bill_output['code'] = ($Return_Code == 'Y' && $Approv_Num != "Not Approved") ? 1 : 2;

if (!empty($Card_Response))
    $bill_output['billmes'] = urldecode($Card_Response);

if ($bill_output['code'] == 1) {
    $bill_output['billmes'] .= " (Approval Code: ".$Approv_Num.")";

    if (!empty($VoidRecNum))
        $bill_output['billmes'] .= " (VoidRecNum: ".$VoidRecNum.")";

    if ($AVS_Response)
        $bill_output['avsmes'] = !empty($avserr[$AVS_Response]) ? $avserr[$AVS_Response] : ("AVS Response Code: ".$AVS_Response);

    if ($transaction_type_name == 'PREAUTH')
        $bill_output['is_preauth'] = true;

    if (!$follow_on) {
        $extra_order_data = array(
            'securepay_appnum' => $Approv_Num,
            'securepay_vrnum' => $VoidRecNum,
            'capture_status' => ($bill_output['is_preauth']) ? 'A' : '',
            'ccdata' => text_crypt($userinfo['card_type'].":". $userinfo['card_name'].":". $userinfo['card_number'].":". $userinfo['card_expire'])
        );
    }
}

?>
