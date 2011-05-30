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
 * Netbilling.com CC processing module
 * API 3.1
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_netbilling.php,v 1.33.2.1 2011/01/10 13:12:06 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

func_set_time_limit(100);

x_load('http');

$netbilling_account = $module_params['param01'];
$netbilling_site_tag = $module_params['param02'];

$post = array();
$post[] = "account_id=".$netbilling_account;
$post[] = "site_tag=".$netbilling_site_tag;
$post[] = "pay_type=C";
$post[] = "tran_type=S";
$post[] = "amount=".$cart['total_cost'];

$post[] = "bill_name1=".$bill_firstname;
$post[] = "bill_name2=".$bill_lastname;
$post[] = "bill_street=".$userinfo['b_address'];
$post[] = "bill_city=".$userinfo['b_city'];
$post[] = "bill_state=".$userinfo['b_state'];
$post[] = "bill_zip=".$userinfo['b_zipcode'];
$post[] = "bill_country=".$userinfo['b_country'];

$post[] = "ship_name1=".$userinfo['s_firstname'];
$post[] = "ship_name2=".$userinfo['s_lastname'];
$post[] = "ship_street=".$userinfo['s_address'];
$post[] = "ship_city=".$userinfo['s_city'];
$post[] = "ship_state=".$userinfo['s_state'];
$post[] = "ship_zip=".$userinfo['s_zipcode'];
$post[] = "ship_country=".$userinfo['s_country'];

$post[] = "cust_ip=".func_get_valid_ip($REMOTE_ADDR);
$post[] = "cust_browser=".$HTTP_USER_AGENT;
$post[] = "description="."Order(s) #".join("-",$secure_oid)."; customer: ".$userinfo['login'];
$post[] = "cust_email=".substr($userinfo['email'], 0, 60);
$post[] = "cust_phone=".substr($userinfo['phone'], 0, 40);

$post[] = "card_number=".$userinfo['card_number'];
$post[] = "card_expire=".$userinfo['card_expire'];
$post[] = "card_cvv2=".$userinfo['card_cvv2'];

// level 2 information
$post[] = "tax_amount=".$cart['tax_cost'];
$post[] = "ship_amount=".$cart['shipping_cost'];
$post[] = "purch_order=".substr(join("-", $secure_oid), 0, 17);

if(isset($cmpi_result)) {
    $post[] = "3ds_cavv=".$cmpi_result['Cavv'];
    $post[] = "3ds_xid=".$cmpi_result['Xid'];
}

list($a,$return)=func_https_request('POST',"https://secure.netbilling.com:1402/gw/sas/direct3.1",$post,'&');

if(preg_match("/HTTP\/\d\.\d 200 /Ss", $a)) {
    $return = explode("&", $return);
    $hash = array();
    foreach($return as $v) {
        $pos = strpos($v, "=");
        if($pos === false)
            continue;
        $hash[substr($v, 0, $pos)] = urldecode(substr($v, $pos+1));
    }

    if($hash['status_code'] != "0" && $hash['status_code'] != "F") {
        $bill_output['code'] = 1;
        $bill_output['billmes'] = " Approval Code: ".$hash['auth_code']. (!empty($hash['trans_id']) ? "; Transaction ID: ".$hash['trans_id'] : "") . "\nSettlement amount: ".$hash["settle_amount"]."\nSettlement currency: ".$hash["settle_currency"];
        $bill_output['avsmes'] = $hash['avs_code'];
        $bill_output['cvvmes'] = $hash['cvv2_code'];

    } else {
        $bill_output['code'] = 2;
        $bill_output['billmes'] = "Declined: ".$hash['auth_msg']." (Reason Code: ".$hash['auth_code']." / Sub: ".$hash['reason_code2'].")";
    }
    unset($hash);
} else {
    $bill_output['code'] = 2;
    if(preg_match("/HTTP\/\d\.\d (\d{3}) (.+)$/Sm", $a, $preg)) {
        $bill_output['billmes'] = "Error: ".$preg[2]." (Reason Code: ".urldecode($preg[1]).")";
    } else {
        $bill_output['billmes'] = "Error: ".urldecode($return);
    }
}
unset($a, $return, $post);

?>
