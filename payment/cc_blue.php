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
 * "BluePay" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_blue.php,v 1.37.2.1 2011/01/10 13:12:05 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

func_set_time_limit(100);

x_load('http');

$pp_merch = $module_params['param01'];
$pp_test  = ($module_params['testmode'] == 'Y' ? 'TEST' : 'LIVE');

$is_preauth = ($module_params['use_preauth'] == 'Y' || func_is_preauth_force_enabled($secure_oid));

$post = array();
$post[] = "MERCHANT=".$pp_merch;

if ($trantype && !empty($bluepay_txnid)) {
    $master_id = $bluepay_txnid;
    $post[] = "RRNO=".$bluepay_txnid;
} else {

    $trantype = $is_preauth ? 'AUTH' : 'SALE';
    $total_cost = price_format($cart['total_cost']);
    $master_id = '0';

    $post[] = "CC_NUM=".$userinfo['card_number'];
    $post[] = "CVCCVV2=".$userinfo['card_cvv2'];
    $post[] = "CC_EXPIRES=".substr($userinfo['card_expire'],0,2).'/'.substr($userinfo['card_expire'],2,2);
    $post[] = "NAME=".$userinfo['card_name'];
    $post[] = "ADDR1=".$userinfo['b_address'];
    $post[] = "ADDR2=".$userinfo['b_address_2'];
    $post[] = "CITY=".$userinfo['b_city'];
    $post[] = "STATE=".$userinfo['b_state'];
    $post[] = "ZIPCODE=".$userinfo['b_zipcode'];
    $post[] = "ORDER_ID=".$module_params['param03'].join("-",$secure_oid);
    $post[] = "INVOICE_ID=".$module_params['param03'].join("-",$secure_oid);
    $post[] = "COMMENT=".$config['Company']['company_name'];
    $post[] = "PHONE=".$userinfo['phone'];
    $post[] = "EMAIL=".$userinfo['email'];
    $post[] = "REBILLING=0";
}

$post[] = "TRANSACTION_TYPE=".$trantype;
$post[] = "MODE=".$pp_test;

if ($trantype != 'VOID') {
    $post[] = "AMOUNT=".$total_cost;
}

$md5_str = $module_params['param02'].$pp_merch.$trantype.$total_cost.$master_id.$pp_test;
$post[] = "TAMPER_PROOF_SEAL=".md5($md5_str);

list($a,$return)=func_https_request('POST',"https://secure.bluepay.com:443/interfaces/bp10emu",$post);

$res = array();
if (preg_match("/Location: [\w\d_\/]+\?(.+)$/m", $a, $match)) {
    $res = func_parse_str($match[1],'&','=','urldecode');
}

$avsres = array(
    'A' => "Street address - Match, Zip - No match",
    'N' => "No match",
    'S' => "AVS not supported for this card type",
    'U' => "AVS not available for this card type",
    'W' => "Zip match 9, street no match",
    'X' => "Zip match 9, street match",
    'Y' => "Zip match 5, street match",
    'Z' => "Zip match 5, street no match",
    'E' => "Not eligible",
    'R' => "System unavailable",
    '_' => "Not supported for this network or transaction type"
);

$cvvres = array(
    'M' => "CVV2  Match",
    'N' => "CVV2  No match",
    'P' => "CVV2 was not processed",
    'S' => "CVV2 exists but was not input",
    'U' => "Zip match 9, street no match",
    '_' => "Card issuer does not provide CVV2 service"
);

if($res['Result'] == 'APPROVED') {
    $bill_output['code'] = 1;
    $bill_output['billmes'] = "Approved; Transaction ID: ".$res['RRNO'];
    if (isset($avsres[$res['AVS']]))
        $bill_output['avsmes'] .= $avsres[$res['AVS']];
    if (isset($cvvres[$res['CVV2']]))
        $bill_output['cvvmes'] .= $cvvres[$res['CVV2']];

    if ($trantype == 'AUTH' && $is_preauth) {
        $bill_output['is_preauth'] = true;
        $extra_order_data = array(
            'bluepay_txnid'        => $res['RRNO'],
            'capture_status'    => 'A',
        );
    }
} elseif($res['Result'] == 'DECLINED') {
    $bill_output['code'] = 2;
    $bill_output['billmes'] = 'Declined';
    if (!empty($res['RRNO']))
        $bill_output['billmes'] .= "; Transaction ID: ".$res['RRNO'];
    if (!empty($res['MESSAGE']))
        $bill_output['billmes'] .= "; Reason: ".$res['MESSAGE'];
} elseif($res['Result'] == 'MISSING') {
    $bill_output['code'] = 2;
    $bill_output['billmes'] = "Missing field(s)";
    if (!empty($res['Missing']))
        $bill_output['billmes'] .= ": ".$res['Missing'];
} else {
    $bill_output['code'] = 2;
    $bill_output['billmes'] = 'Error';
    if (!empty($res['MESSAGE']))
        $bill_output['billmes'] .= ": ".$res['MESSAGE'];
}

?>
