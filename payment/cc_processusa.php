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
 * "iTransact (Process USA) - XML scheme" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_processusa.php,v 1.61.2.1 2011/01/10 13:12:07 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('cart','http');

func_set_time_limit(100);

$avserr = array(
    'A' => "Address match. Zip does not match.",
    'E' => "AVS error.",
    'G' => "Global non-participant. AVS not available for international customers.",
    'I' => "International address not verified.",
    'N' => "No match. Neither address nor ZIP match.",
    'O' => "No response to AVS request.",
    'U' => "AVS unavailable.",
    'Y' => "Address and 5-digit ZIP Code match.",
    'Z' => "ZIP Code match. Address does not match."
);

$cvverr = array(
    'M' => 'Match',
    'N' => "No Match",
    'P' => "Not Processed",
    'S' => "Issuer indicates CVV2 should be present. Merchant indicates not present",
    'U' => "Issuer has not certified for CVV or has not provided CVV encryption keys"
);

if ($REQUEST_METHOD == 'POST' && $_POST['action'] == 'place_order') {
    $auth_only = ($module_params['use_preauth'] == 'Y' || func_is_preauth_force_enabled($secure_oid));
}

$follow_on = in_array($transaction_type, array('V', 'X'));

$transaction_type_name = func_check_cc_trans('iTransact (Process USA) - XML scheme', $transaction_type, array("P" => "SaleRequest", "C" => "SaleRequest", "X" => "PostAuthTransaction", "V" => "VoidTransaction"));

$follow_on = in_array($transaction_type_name, array('PostAuthTransaction', 'VoidTransaction'));

if ($follow_on && empty($processusa_txnid)) {
    if (preg_match("/XID: (\d+)/", $order['details'], $preg))
        $processusa_txnid = $preg[1];
}

$itrans_script_name = ($follow_on) ? 'xmltrans2.cgi' : 'xmltrans.cgi';

$pp_login = $module_params['param01'];
$pp_pass = $module_params['param02'];
$pp_home = $module_params['param03'];

    $post = '';
    $post[] = "<?xml version=\"1.0\"?>";

if (!$follow_on) {
    $post[] = "<SaleRequest>";
    $post[] = "<CustomerData>";
    $post[] = "<Email>".$userinfo['email']."</Email>";
    $post[] = "<BillingAddress>";
    $post[] = "<FirstName>".$bill_firstname."</FirstName>";
    $post[] = "<LastName>".$bill_lastname."</LastName>";
    $post[] = "<Address1>".$userinfo['b_address']."</Address1>";
    $post[] = "<City>".$userinfo['b_city']."</City>";
    $post[] = "<State>".(empty($userinfo['b_state'])?'NONE':$userinfo['b_state'])."</State>";
    $post[] = "<Zip>".$userinfo['b_zipcode']."</Zip>";
    $post[] = "<Country>".$userinfo['b_country']."</Country>";
    $post[] = "<Phone>".(empty($userinfo['phone'])?'NONE':$userinfo['phone'])."</Phone>";
    $post[] = "</BillingAddress>";

    $post[] = "<AccountInfo>";
    $post[] = "<CardInfo>";
    $post[] = "<CCNum>".$userinfo['card_number']."</CCNum>";
    $post[] = "<CCMo>".substr($userinfo['card_expire'],0,2)."</CCMo>";
    $post[] = "<CCYr>".(2000+substr($userinfo['card_expire'],2,2))."</CCYr>";
    $post[] = "<CVV2Number>".$userinfo['card_cvv2']."</CVV2Number>";
    $post[] = "</CardInfo>";
    $post[] = "</AccountInfo>";
    $post[] = "</CustomerData>";
    $post[] = "<TransactionData>";
    if ($auth_only)
        $post[] = "<Preauth/>";
    $post[] = "<VendorId>".$pp_login."</VendorId>";
    $post[] = "<VendorPassword>".$pp_pass."</VendorPassword>";
    $post[] = "<HomePage>".$pp_home."</HomePage>";
    $post[] = "<OrderItems>";

    $post[] = "<Item>";
    $post[] = "<Description>Your Cart</Description>";
    $post[] = "<Cost>".$cart['total_cost']."</Cost>";
    $post[] = "<Qty>1</Qty>";
    $post[] = "</Item>";

    $post[] = "</OrderItems>";
    $post[] = "</TransactionData>";
    $post[] = "</SaleRequest>";

} else {
    $post[] = "<GatewayInterface>";
    $post[] = "<VendorIdentification>";
    $post[] = "<VendorId>".$pp_login."</VendorId>";
    $post[] = "<VendorPassword>".$pp_pass."</VendorPassword>";
    $post[] = "<HomePage>".$pp_home."</HomePage>";
    $post[] = "</VendorIdentification>";
    $post[] = "<".$transaction_type_name.">";
    $post[] = "<OperationXID>".$processusa_txnid."</OperationXID>";
    $post[] = "<TestMode>".($module_params['testmode'] == 'N' ? 'FALSE' : 'TRUE')."</TestMode>";
    $post[] = "</".$transaction_type_name.">";
    $post[] = "</GatewayInterface>";
}

$pst = array("xml=".strtr(join('',$post),array("&"=>"&amp;")));

list($a, $return) = func_https_request('POST',"https://secure.paymentclearing.com:443/cgi-bin/rc/".$itrans_script_name, $pst);

$return = str_replace("\n", '', $return);

preg_match("/<Status>(.*)<\/Status>/",$return,$status);

if(strtoupper($status[1]) == 'OK') {
    $bill_output['is_preauth'] = $auth_only;
    $extra_order_data['capture_status'] = ($auth_only) ? 'A' : '';

    $bill_output['code'] = 1;
    preg_match("/<AuthCode>(.*)<\/AuthCode>/",$return,$out);
    if (!empty($out[1])) {
        $bill_output['billmes'] = "AuthCode: ".$out[1];
        $extra_order_data['processusa_authcode'] = $out[1];
    }

    preg_match("/<XID>(.*)<\/XID>/",$return,$out);
    if(!empty($out[1])) {
        $bill_output['billmes'].= " (XID: ".$out[1].")";
        $extra_order_data['processusa_txnid'] = $out[1];
    }
} else {
    $bill_output['code'] = 2;

    preg_match("/<ErrorCategory>(.*)<\/ErrorCategory>/",$return,$out);
    if (!empty($status[1])) {
        $bill_output['billmes'] .= $status[1];
    }
    if (!empty($out[1])) {
        $bill_output['billmes'] .= " : ".$out[1];
    }

    preg_match("/<ErrorMessage>(.*)<\/ErrorMessage>/",$return,$out);
    if (!empty($out[1])) {
        $bill_output['billmes'] .= " : ".$out[1];
    }
}

preg_match("/<AVSResponse>(.*)<\/AVSResponse>/",$return,$out);
if(!empty($out[1]))
    $bill_output['avsmes'] = empty($avserr[$out[1]]) ? "AVSResponse: ".$out[1] : $avserr[$out[1]];
preg_match("/<AVSCategory>(.*)<\/AVSCategory>/",$return,$out);
if(!empty($out[1]))
    $bill_output['avsmes'].= " (".$out[1].")";

preg_match("/<CVV2Response>(.*)<\/CVV2Response>/",$return,$out);
if(!empty($out[1]))
    $bill_output['cvvmes'] = empty($cvverr[$out[1]]) ? "CVV2Response: ".$out[1] : $cvverr[$out[1]];

?>
