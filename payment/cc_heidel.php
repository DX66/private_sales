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
 * Heidel Credit card payment processor
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_heidel.php,v 1.22.2.1 2011/01/10 13:12:06 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

func_set_time_limit(100);

x_load('http');

$pp_sender = $module_params['param01'];
$pp_token = $module_params['param02'];
$pp_curr = $module_params['param03'];
$pp_channel = $module_params['param04'];
$pp_user = $module_params['param08'];
$pp_pwd = $module_params['param07'];
$pp_ordr = $module_params['param06'].join("-",$secure_oid);
$pp_script = ($module_params['testmode']=="N")?('ctpe.net'):('test.ctpe.net');
$pp_mode = ($module_params['testmode']=="N")?('LIVE'):('INTEGRATOR_TEST'); // INTEGRATOR_TEST - VALIDATOR_TEST - CONNECTOR_TEST

if(is_visa($userinfo['card_number']))$userinfo['card_type']="VISA";
if(is_mc($userinfo['card_number']))$userinfo['card_type']="MASTER";
if(is_amex($userinfo['card_number']))$userinfo['card_type']="AMEX";
$add_code=0;

$post = '';
$post[] = "REQUEST.VERSION=1.0";
$post[] = "SECURITY.SENDER=".$pp_sender;
$post[] = "SECURITY.TOKEN=".$pp_token;

$post[] = "USER.LOGIN=".$pp_user;
$post[] = "USER.PWD=".$pp_pwd;

$post[] = "TRANSACTION.MODE=".$pp_mode;
$post[] = "TRANSACTION.RESPONSE=SYNC";
$post[] = "TRANSACTION.CHANNEL=".$pp_channel;
$post[] = "IDENTIFICATION.TRANSACTIONID=".$pp_ordr;

$post[] = "PAYMENT.CODE=CC.PA"; // CC:CreditCard; PA:PreAuth/DB:Debit/CP:Capture
$post[] = "PRESENTATION.AMOUNT=".$cart['total_cost'];
$post[] = "PRESENTATION.CURRENCY=".$pp_curr;
$post[] = "PRESENTATION.USAGE=Order ".$pp_ordr;

$post[] = "ACCOUNT.HOLDER=".$userinfo['card_name'];
$post[] = "ACCOUNT.NUMBER=".$userinfo['card_number'];
$post[] = "ACCOUNT.BRAND=".$userinfo['card_type'];
$post[] = "ACCOUNT.MONTH=".substr($userinfo['card_expire'],0,2);
$post[] = "ACCOUNT.YEAR=".(2000+substr($userinfo['card_expire'],2,2));
$post[] = "ACCOUNT.VERIFICATION=".$userinfo['card_cvv2'];

$post[] = "NAME.GIVEN=".$bill_firstname;
$post[] = "NAME.FAMILY=".$bill_lastname;
$post[] = "ADDRESS.STREET=".$userinfo['b_address'];
$post[] = "ADDRESS.ZIP=".$userinfo['b_zipcode'];
$post[] = "ADDRESS.CITY=".$userinfo['b_city'];
$post[] = "ADDRESS.STATE=".$userinfo['b_state'];
$post[] = "ADDRESS.COUNTRY=".$userinfo['b_country'];

$post[] = "CONTACT.EMAIL=".$userinfo['email'];
$post[] = "CONTACT.PHONE=".$userinfo['phone'];
$post[] = "CONTACT.IP=".func_get_valid_ip($_SERVER['REMOTE_ADDR']);

$post[] = "RISKMANAGEMENT.PROCESS=AUTO";

list($a,$return)=func_https_request('POST',"https://".$pp_script.":443/frontend/payment.prc",$post);
parse_str(trim($return),$ret);

if($ret['P3_VALIDATION']=="ACK") {
    $bill_output['code'] = ($ret['PROCESSING_REASON_CODE']=="00" && $ret['PROCESSING_STATUS_CODE']=="90") ? 1 : 2;

    $bill_output['billmes'] = "[".$ret['PROCESSING_REASON']."]:".$ret['PROCESSING_RETURN'].($add_code?" (ProcessingReasonCode: ".$ret['PROCESSING_REASON_CODE']."/PorcessingReturnCode:".$ret['PROCESSING_RETURN_CODE'].")":'');
    $bill_output['billmes'].=" (Status: ".$ret['PROCESSING_STATUS'].($add_code?"/Code: ".$ret['PROCESSING_STATUS_CODE']:'').")";
    $bill_output['billmes'].=" (UniqueID: ".$ret['IDENTIFICATION_UNIQUEID']."; ShortID: ".$ret['IDENTIFICATION_SHORTID'].")";
} else {
    $err = array(
        '2010'=>"Parameter PRESENTATION.AMOUNT missing or not a number",
        '2030'=>"Parameter PRESENTATION.CURRENCY missing",
        '2020'=>"Parameter PAYMENT.CODE missing or wrong",
        '3010'=>"Parameter FRONTEND.MODE missing or wrong",
        '3020'=>"Parameter FRONTEND.NEXT_TARGET wrong",
        '3040'=>"Parameter FRONTEND.LANGUAGE wrong",
        '3050'=>"Parameter FRONTEND. RESPONSE_URL wrong",
        '3070'=>"Parameter FRONTEND. POPUP wrong",
        '3090'=>"Wrong FRONTEND.LINK parameter combination",
        '4010'=>"Parameter SECURITY.TOKEN missing or wrong",
        '4020'=>"Parameter SECURITY.IP missing or wrong",
        '4030'=>"Parameter SECURITY.SENDER missing or wrong",
        '4040'=>"Wrong User/Password combination",
        '4050'=>"Parameter USER.LOGIN missing or wrong",
        '4060'=>"Parameter USER.PWD missing or wrong",
        '4070'=>"Parameter TRANSACTION.CHANNEL missing or wrongissing or wrong"
    );
    $bill_output['code'] = 2;
    $bill_output['billmes'] = $err[$ret['P3_VALIDATION']];
}

?>
