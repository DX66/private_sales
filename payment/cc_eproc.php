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
 * "eProcessingNetwork - Transparent Database Engine" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_eproc.php,v 1.24.2.1 2011/01/10 13:12:06 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('http');

$an_login = $module_params['param01'];
$an_prefix = $module_params['param02'];
$an_cvv = $module_params['param03'];

$post[] = "ePNAccount=".$an_login;
$post[] = "Address=".$userinfo['b_address'];
$post[] = "Zip=".$userinfo['b_zipcode'];
$post[] = "Email=".$userinfo['email'];
$post[] = "Total=".$cart['total_cost'];
$post[] = "CardNo=".$userinfo['card_number'];
$post[] = "ExpMonth=".substr($userinfo['card_expire'],0,2);
$post[] = "ExpYear=".substr($userinfo['card_expire'],2,2);
$post[] = "CVV2=".$userinfo['card_cvv2'];
$post[] = "CVV2Type=".$an_cvv;

if (!empty($module_params['param04'])) {
    $post[] = "RestrictKey=".$module_params['param04'];
}

list($a,$return) = func_https_request('POST',"https://www.eprocessingnetwork.com:443/cgi-bin/tdbe/transact.pl",$post);

preg_match("/>(\".*\")</",$return,$out);

$return = "\",".($out[1] ? $out[1] : trim($return)).",\"";
$mass = explode('","', $return);

preg_match("/^(.)(.*)$/",$mass[1],$out);

if($out[1]=="Y") {
    $bill_output['code'] = 1;
    $bill_output['billmes'] = "Approval Response: ".$out[2];
} else {
    $bill_output['code'] = 2;
    $bill_output['billmes'] = $out[2];
}

if(!empty($mass[2]))$bill_output['avsmes'] = $mass[2];

if(!empty($mass[3]))$bill_output['cvvmes'] = $mass[3];

?>
