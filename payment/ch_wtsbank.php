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
 * "" payment module (check processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: ch_wtsbank.php,v 1.35.2.1 2011/01/10 13:12:07 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('http','payment');

@set_time_limit(100);

/**
 * Based on cc_plugnpaycom.php
 */
// simulator ports: 48966, 49015, 49063, 49016, 49064, 49017, 49065

$pid = $module_params ['param01'];
$sid = $module_params ['param02'];
$cur = $module_params ['param03'];

$post = '';
$bill_output = '';
$post[] = "parent_id=".$pid;
$post[] = "sub_id=".$sid;
$post[] = "pmt_type=chk";
$post[] = "billing_cycle=-1";
$post[] = "max_num_billing=0";
$post[] = "currency=".$cur;
$post[] = "action_code=P";
$post[] = "merordernumber=#".func_get_urlencoded_orderids($secure_oid);
$post[] = "custname=".$bill_name;
$post[] = "custemail=".$userinfo['email'];
$post[] = "custaddress1=".$userinfo['b_address'];
$post[] = "custcity=".$userinfo['b_city'];
$post[] = "custstate=".$userinfo['b_state'];
$post[] = "custzip=".$userinfo['b_zipcode'];
$post[] = "custphone=".$userinfo['phone'];
$post[] = "shipaddress1=".$userinfo['s_address'];
$post[] = "shipcity=".$userinfo['s_city'];
$post[] = "shipstate=".$userinfo['s_state'];
$post[] = "shipzip=".$userinfo['s_zipcode'];
$post[] = "initial_amount=".$cart['total_cost'];
$post[] = "acct_name=".$userinfo['check_name'];
$post[] = "chk_acct=".$userinfo['check_ban'];
$post[] = "chk_aba=".$userinfo['check_brn'];
$post[] = "chk_fract=".$userinfo['check_number'];

list($a, $return)=func_https_request('POST',"https://join.achbill.com:443/cgi-bin/man_trans.cgi",$post);

// return lines ...
#status=declined
#reason=Merchant's id was not found in the database.
#PostedVars=BEGIN
#parent_id=AB02
#sub_id=ZY04
#pmt_type=chk
#custname=sdg sdg
#custemail=sdg@rrf.ru
#custaddress1=sdg
#custcity=sdg
#custstate=
#custzip=50001
#custphone=413737
#shipaddress1=sdg
#shipcity=sdg
#shipstate=
#shipzip=50001
#initial_amount=59.99
#currency=CN
#merordernumber=[#13]
#action_code=V
#phpsessid=821c175cd836a885cac783ea53f14174
#PostedVars=END

if (preg_match("/status=Accepted/", $return)) {
    $bill_output['code'] = 1;
    preg_match("/order_id=(.*)&/U", $return . "&", $out);
    if ($out[1])
        $bill_output['billmes'] = "(OrderID: " . $out[1] . ")";

} else {

    $bill_output['code'] = 2;
    preg_match("/status=(.*)&.*reason=(.*)&/U", $return, $out);
    $bill_output['billmes'] = $out[1] . ": " . $out[2];
}

?>
