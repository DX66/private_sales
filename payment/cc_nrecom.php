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
 * "NetRegistry e-commerce" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_nrecom.php,v 1.29.2.1 2011/01/10 13:12:06 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

func_set_time_limit(100);

x_load('http');

$comment = "OID:" . join("-", $secure_oid) . ";CardHolder:" . $userinfo['card_name'];
if (strlen($comment) > 34)
    $comment = "OID:" . join("-", $secure_oid);

$post = array(
    "LOGIN=" . $module_params['param01'] . '/' . $module_params['param02'],
    "COMMAND=" . ($module_params['use_preauth'] != 'Y' || func_is_preauth_force_enabled($secure_oid)) ? "purchase" : "preauth",
    "AMOUNT=" . $cart['total_cost'],
    "COMMENT=" . $comment,
    "CCNUM=" . $userinfo['card_number'],
    "CCEXP=" . substr($userinfo['card_expire'], 0, 2) . '/' . substr($userinfo['card_expire'], 2, 2)
);

list($a, $return) = func_https_request('POST',"https://4tknox.au.com:443/cgi-bin/themerchant.au.com/ecom/external2.pl", $post);

$return = "&" . strtr($return, "\n", "&") . "&";

$extra_order_data = array();

if (preg_match("/&status=approved&/i", $return) && preg_match("/&result=1&/i", $return)) {
    $bill_output['code'] = 1;

    if (preg_match("/authentication=(.*)&/U", $return, $out))
        $bill_output['billmes'] = "(authentication=[" . $out[1] . "])";

    if ($module_params['use_preauth'] == 'Y' || func_is_preauth_force_enabled($secure_oid)) {
        $bill_output['is_preauth'] = true;
        x_load('crypt');
        $extra_order_data['ccdata'] = text_crypt($userinfo["card_number"] . "\n" . substr($userinfo["card_expire"], 0, 2) . "/" . substr($userinfo["card_expire"], 2, 2));
    }

} elseif (preg_match("/response_text=(.*)&/U", $return, $out) || preg_match("/&failed&(.*)&/U", $return, $out)) {
    $bill_output['code'] = 2;
    $bill_output['billmes'] = $out[1];
} else {
    $bill_output['code'] = 5; //unavailable
}

if (preg_match("/&status=(.*)&/U", $return, $out))
    $bill_output['billmes'] .= "(Status = " . $out[1] . ")";

if (preg_match("/bank_ref=(.*)&/U", $return, $out))
    $bill_output['billmes'] .= "(Bank ref=" . $out[1] . ")";

if (preg_match("/txn_ref=(.*)&/U", $return, $out)) {
    $bill_output['billmes'] .= "(Txn=" . $out[1] . ")";
    $extra_order_data['txnid'] = $out[1];

} else {
    $extra_order_data = array();
}

$bill_output['cvvmes'] .= "Not support";
$bill_output['avsmes'] = "Not support";

?>
