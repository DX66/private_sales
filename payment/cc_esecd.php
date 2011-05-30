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
 * "eSec - Direct" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_esecd.php,v 1.26.2.1 2011/01/10 13:12:06 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

func_set_time_limit(180);

x_load('http');

$staerr = array(
    '00' => "successful approval",
    '01' => "refer to issuer",
    '02' => "refer to issuer's special conditions",
    '03' => "invalid merchant",
    '04' => "pickup card",
    '05' => "do not honour",
    '06' => 'error',
    '07' => "pickup card, special conditions",
    '08' => "honour with ID (signature)(corresponds to 200 response)",
    '09' => "request in progress",
    '10' => "approved for partial amount",
    '11' => "approved VIP",
    '12' => "invalid transaction",
    '13' => "invalid amount",
    '14' => "invalid card number",
    '15' => "no such issuer",
    '16' => "approved, update track 3",
    '17' => "customer cancellation",
    '18' => "customer dispute",
    '19' => "re-enter transaction",
    '20' => "invalid response",
    '21' => "no action taken",
    '22' => "suspected malfunction",
    '23' => "unacceptable transaction fee",
    '24' => "file date not supported",
    '25' => "unable to locate record on file",
    '26' => "duplicate file update record, old record replaced",
    '27' => "file update field error",
    '28' => "file update file locked out",
    '29' => "file update not successful, contact acquirer",
    '30' => "format error",
    '31' => "bank not supported by switch",
    '32' => "completed partially",
    '33' => "expired card",
    '34' => "suspected fraud",
    '35' => "contact acquirer",
    '36' => "restricted card",
    '37' => "contact acquirer security",
    '38' => "allowable PIN retries exceeded",
    '39' => "no credit account",
    '40' => "request function not supported",
    '41' => "lost card",
    '42' => "no universal account",
    '43' => "stolen card",
    '44' => "no investment account",
    '51' => "insufficient funds",
    '52' => "no cheque account",
    '53' => "no savings account",
    '54' => "expired card",
    '55' => "incorrect PIN",
    '56' => "no card record",
    '57' => "transaction not permitted to cardholder",
    '58' => "transaction not permitted to terminal",
    '59' => "suspected fraud",
    '60' => "contact acquirer",
    '61' => "exceeds withdrawal amount limit",
    '62' => "restricted card",
    '63' => "security violation",
    '64' => "original amount incorrect",
    '65' => "exceeds withdrawal frequency limit",
    '66' => "contact acquirer security",
    '67' => "hard capture",
    '68' => "response received too late",
    '75' => "allowable number of PIN retries exceeded",
    '90' => "cutoff in progress",
    '91' => "issuer inoperative",
    '92' => "financial institution cannot be found",
    '93' => "transaction cannot be completed, violation of law",
    '94' => "duplicate transmission",
    '95' => "reconcile error",
    '96' => "system malfunction",
    '97' => "reconciliation totals have been reset",
    '98' => "MAC error",
    '99' => "reserved, will not be returned "
);

$pp_merch = $module_params['param01'];

if($module_params['testmode'] == 'N') {
    $test = 'false';
    $first4 = 0+substr($userinfo['card_number'],0,4);
    if($first4>=4000 && $first4<=4999)$userinfo['card_type']="visa"; // VISA
    if($first4>=5100 && $first4<=5999)$userinfo['card_type']="mastercard"; // MasterCard
    if($first4>=3400 && $first4<=3499)$userinfo['card_type']="amex"; // AmericanExpress
    if($first4>=3700 && $first4<=3799)$userinfo['card_type']="amex"; // AmericanExpress
    if($first4>=3000 && $first4<=3059)$userinfo['card_type']="dinersclub"; // Diners
    if($first4>=3600 && $first4<=3699)$userinfo['card_type']="dinersclub"; // Diners
    if($first4>=3800 && $first4<=3889)$userinfo['card_type']="dinersclub"; // Diners
    if($first4>=3528 && $first4<=3589)$userinfo['card_type']="jcb"; // JCB
} else {
    $test = 'true';
    $pp_merch = 'test';
    $userinfo['card_type']="testcard";
    $userinfo['card_number']=($module_params['testmode']=="A" ? 'testsuccess' : 'testfailure');
    $userinfo['card_cvv2'] = '999';
}

$post = '';
$post[] = "EPS_MERCHANT=".$pp_merch;
$post[] = "EPS_REFERENCEID=".$module_params['param03'].join("-",$secure_oid);
$post[] = "EPS_CARDNUMBER=".$userinfo['card_number'];
$post[] = "EPS_CARDTYPE=".$userinfo['card_type'];
$post[] = "EPS_EXPIRYMONTH=".(0+substr($userinfo['card_expire'],0,2));
$post[] = "EPS_EXPIRYYEAR=".(2000+substr($userinfo['card_expire'],2,2));
$post[] = "EPS_NAMEONCARD=".$userinfo['card_name'];
$post[] = "EPS_AMOUNT=".$cart['total_cost'];
$post[] = "EPS_CCV=".$userinfo['card_cvv2'];
$post[] = "EPS_VERSION=3";
$post[] = "EPS_TEST=".$test;

if (isset($cmpi_result) && !empty($cmpi_result) && isset($cmpi_result['Xid']) && isset($cmpi_result['Cavv']) && isset($cmpi_result['EciFlag'])) {
    $post[] = "EPS_3DSECURE=true";
    $post[] = "3D_XID=".$cmpi_result['Xid'];
    $post[] = "3D_CAVV=".$cmpi_result['Cavv'];
    $post[] = "3D_SLI=".sprintf("%02d", intval($cmpi_result['EciFlag']));
} else {
    $post[] = "EPS_3DSECURE=false";
}

list($a,$return)=func_https_request('POST',"https://sec.aba.net.au:443/cgi-bin/service/authint",$post);
$resp = explode("\n", $return);
foreach($resp as $v)
    if($v){ list($a, $b) = preg_split("/=/", $v, 2); $ret[$a] = trim($b); }

preg_match("/^(\d{3})/U",$ret['message'],$out);$code = $out[1];

if($code>=200 && $code<=299)
    $bill_output['code'] = 1;
else
    $bill_output['code'] = 2;

$bill_output['billmes'] = $ret['message'];

if($ret["auth-id"])
    $bill_output['billmes'].= " (Auth ID: ".$ret["auth-id"].")";
if($ret["txn-id"])
    $bill_output['billmes'].= " (Txn ID: ".$ret["txn-id"].")";
if($ret["eft-response"])
    $bill_output['billmes'].= " (EFT ".(empty($staerr[$ret["eft-response"]]) ? "Code: ".$ret["eft-response"] : "Response: ".$staerr[$ret["eft-response"]]).")";

?>
