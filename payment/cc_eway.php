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
 * "eWAY - XML payment" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_eway.php,v 1.35.2.1 2011/01/10 13:12:06 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('http');

func_set_time_limit(100);

$pp_login = $module_params['param01'];
$pp_test = ($module_params['testmode']=="N")?(''):('TRUE');
$script = ($module_params['testmode']=="N")?('gateway_cvn/xmlpayment.asp'):('gateway_cvn/xmltest/TestPage.asp');

$post = '';
$post[] = "<ewaygateway>";
$post[] = "<ewayCustomerID>".$pp_login."</ewayCustomerID>";
$post[] = "<ewayTotalAmount>".(100*$cart['total_cost'])."</ewayTotalAmount>";
$post[] = "<ewayCustomerFirstName>".$bill_firstname."</ewayCustomerFirstName>";
$post[] = "<ewayCustomerLastName>".$bill_lastname."</ewayCustomerLastName>";
$post[] = "<ewayCustomerEmail>".$userinfo['email']."</ewayCustomerEmail>";
$post[] = "<ewayCustomerAddress>".$userinfo['b_address']."</ewayCustomerAddress>";
$post[] = "<ewayCustomerPostcode>".$userinfo['b_zipcode']."</ewayCustomerPostcode>";
$post[] = "<ewayCustomerInvoiceDescription>".$descr."</ewayCustomerInvoiceDescription>";
$post[] = "<ewayCustomerInvoiceRef>".$module_params['param03'].join("-",$secure_oid)."</ewayCustomerInvoiceRef>";
$post[] = "<ewayCustomerIPAddress>".func_get_valid_ip($REMOTE_ADDR)."</ewayCustomerIPAddress>";
$post[] = "<ewayCustomerBillingCountry>".$userinfo['b_country']."</ewayCustomerBillingCountry>";
$post[] = "<ewayCardHoldersName>".$userinfo['card_name']."</ewayCardHoldersName>";
$post[] = "<ewayCardNumber>".$userinfo['card_number']."</ewayCardNumber>";
$post[] = "<ewayCardExpiryMonth>".substr($userinfo['card_expire'],0,2)."</ewayCardExpiryMonth>";
$post[] = "<ewayCardExpiryYear>".substr($userinfo['card_expire'],2,2)."</ewayCardExpiryYear>";
$post[] = "<ewayTrxnNumber></ewayTrxnNumber>";
$post[] = "<ewayOption1></ewayOption1>";
$post[] = "<ewayOption2></ewayOption2>";
$post[] = "<ewayOption3>".$pp_test."</ewayOption3>";
$post[] = "<ewayCVN>".$userinfo['card_cvv2']."</ewayCVN>";
$post[] = "</ewaygateway>";

list($a,$return)=func_https_request('POST',"https://www.eway.com.au:443/".$script,$post,'','','text/xml');

$bill_output['avsmes'] = "Not support";

preg_match("/<ewayTrxnStatus>(.*)<\/ewayTrxnStatus>/",$return,$out);

if ($out[1] == 'True') {
    preg_match("/<ewayAuthCode>(.*)<\/ewayAuthCode>/",$return,$out);
    $bill_output['code'] = 1; $bill_output['billmes'] = $out[1];
}
else {
    preg_match("/<ewayTrxnError>(.*)<\/ewayTrxnError>/",$return,$out);
    $bill_output['code'] = 2; $bill_output['billmes'] = $out[1];
}

preg_match("/<ewayTrxnNumber>(.*)<\/ewayTrxnNumber>/",$return,$out);
$bill_output['billmes'].= " (TrnxNum=".$out[1].")";

preg_match("/<ewayBeagleScore>(.*)<\/ewayBeagleScore>/",$return,$out);
$bill_output['billmes'].= " (ewayBeagleScore=".$out[1].")";

?>
