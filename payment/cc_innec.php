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
 * "Innovative E-Commerce" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_innec.php,v 1.25.2.2 2011/01/10 13:12:06 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('http');

$pp_merch    = $module_params['param01'];
$pp_pass    = $module_params['param02'];
$pp_shift    = $module_params['param03'];
$pp_approve    = $module_params['param04'];

$post = '';
$post[] = "target_app=WebCharge_v5.06";
$post[] = "response_mode=simple";
$post[] = "response_fmt=url_encoded";
$post[] = "cardtype=".strtolower($userinfo['card_type']);
$post[] = "delimited_fmt_include_fields=true";
$post[] = "delimited_fmt_field_delimiter==";
$post[] = "delimited_fmt_value_delimiter=|";
$post[] = "username=".$pp_merch;
$post[] = "pw=".$pp_pass;
$post[] = "ccname=".$userinfo['card_name'];
$post[] = "ccnumber=".$userinfo['card_number'];
$post[] = "month=".substr($userinfo['card_expire'],0,2);
$post[] = "year=".substr($userinfo['card_expire'],2,2);
$post[] = "baddress=".$userinfo['b_address'];
$post[] = "bcity=".$userinfo['b_city'];
$post[] = "bstate=".$userinfo['b_state'];
$post[] = "bzip=".$userinfo['b_zipcode'];
$post[] = "bcountry=".$userinfo['b_country'];
$post[] = "bphone=".$userinfo['b_phone'];
$post[] = "email=".$userinfo['email'];
$post[] = "ccidentifier1=".$userinfo['card_cvv2'];
$post[] = "ReceiptEmail=no";
$post[] = "upg_auth=zxcvlkjh";

if ($pp_approve == 'Y') {
    $post[] = "test_override_errors=1";
}

$follow_on = in_array($trantype, array('postauth', 'void'));
$is_preauth = ($module_params['use_preauth'] == 'Y' || func_is_preauth_force_enabled($secure_oid));

if ($follow_on) {
    $post[] = "reference=".$order['order']['extra']['approval'];
    $post[] = "ordernumber=".$order['order']['extra']['innec_ordr'];
    $post[] = "trans_id=".$innec_txnid;
    if ($trantype == 'postauth') {
        $post[] = "authamount=".$total_cost;
    }
} else {
    $trantype = $is_preauth ? 'preauth' : 'sale';
}

$post[] = "trantype=".$trantype;
$post[] = "fulltotal=".($follow_on ? $total_cost : $cart['total_cost']);

list($a,$ret)=func_https_request('POST',"https://transactions.innovativegateway.com:443/servlet/com.gateway.aai.Aai",$post);

$a = explode("&", $ret);
$ret = '';
if (!empty($a)) {
    foreach($a as $k) {
        list($b, $c) = explode("=", $k, 2);
        $ret[strtolower(urldecode($b))] = strip_tags(urldecode($c));
    }
}

$avserr = array(
    'X' => "Both the zip code (the AVS 9-digit) and the street address match.",
    'Y' => "Both the zip (the AVS 5-digit) and the street address match.",
    'A' => "The street address matches, but the zip code does not match.",
    'W' => "The 9-digit zip codes matches, but the street address does not match.",
    'Z' => "The 5-digit zip codes matches, but the street address does not match.",
    'N' => "Neither the street address nor the postal code matches.",
    'R' => "Retry, System unavailable (maybe due to timeout).",
    'S' => "Service not supported.",
    'U' => "Address information unavailable.",
    'E' => "Data not available/error invalid.",
    'G' => "Non-US card issuer that does not participate in AVS"
);

if(!empty($ret['approval'])) {

    $bill_output['code'] = 1;
    $bill_output['billmes'] = $ret['approval'];

    if ($trantype == 'preauth' && $is_preauth) {
        $bill_output['is_preauth'] = true;
        $extra_order_data = array(
            'approval'            => $ret['approval'],
            'innec_txnid'        => $ret['anatransid'],
            'innec_ordr'        => $ret['ordernumber'],
            'ccdata'            => text_crypt($userinfo['card_type'].":".
                                              $userinfo['card_name'].":".
                                              $userinfo['card_number'].":".
                                              $userinfo['card_expire']),
            'capture_status'    => 'A',
        );
    }

} elseif(!empty($ret['error'])) {

    $bill_output['code'] = 2;
    $bill_output['billmes'] = $ret['error'];

}

if($ret['avs'])
    $bill_output['avsmes'] = (empty($avserr[$ret['avs']]) ? "Error Code: ".$ret['avs'] : $avserr[$ret['avs']]);

$bill_output['billmes'].= " (ANATransId: ".$ret['anatransid'].") ";

?>
