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
 * "Caledon" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_caledon.php,v 1.22.2.1 2011/01/10 13:12:05 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

func_set_time_limit(180);

x_load('http');

$avserr = array(
    'X' => "Exact match with 9-digit zip",
    'Y' => "Exact match with 5-digit zip",
    'A' => "Address match only",
    'W' => "9-digit zip match only",
    'Z' => "5-digit zip match only",
    'N' => "No address or zip match",
    'U' => "Address unavailable",
    'G' => "Non-US issuer does not participate",
    'R' => "Issuer system unavailable",
    'E' => "Not a mail/phone order (ineligible)",
    'S' => "Service not supported",
    '0' => "Transaction declined, AVS not processed"
);

$pp_term = $module_params['param01'];
$pp_oper = $module_params['param02'];

$_ref = urlencode(trim(substr(preg_replace("/[^a-zA-Z0-9\/-]/s", '', $module_params['param03'].join("-", $secure_oid)), 0, 60)));

$post = array();
$post[] = "TERMID=".urlencode(trim($pp_term));
$post[] = "TYPE=P";
$post[] = "OPERID=".urlencode(trim($pp_oper));
$post[] = "CARD=".urlencode(trim($userinfo['card_number']));
$post[] = "EXP=".urlencode(trim($userinfo['card_expire']));
$post[] = "AMT=".urlencode(trim(100*$cart['total_cost']));
$post[] = "CVV2=".urlencode(trim($userinfo['card_cvv2']));
$post[] = "AVS=".urlencode(trim(preg_replace("/[^\w]/",'',strtoupper($userinfo['b_address'].$userinfo['b_zipcode']))));
$post[] = "REF=".$_ref;

if (isset($cmpi_result) && !empty($cmpi_result['Cavv'])) {
    $post[] = "VBV_STATUS=".$cmpi_result['PAResStatus'];
    $post[] = "VBV_CAVV=".$cmpi_result['Cavv'];
    $post[] = "VBV_XID=".$cmpi_result['Xid'];
}

list($a, $return) = func_https_request('GET',"https://lt3a.caledoncard.com:443/".join("&",$post));
parse_str($return, $ret);

if ($ret['CODE'] === '0000') {
    if (($module_params['use_preauth'] != "Y") && (!func_is_preauth_force_enabled($secure_oid))) {
        $post = array();
        $post[] = "TERMID=".urlencode(trim($pp_term));
        $post[] = "TYPE=C";
        $post[] = "OPERID=".urlencode(trim($pp_oper));
        $post[] = "CARD=0";
        $post[] = "EXP=0000";
        $post[] = "AMT=".urlencode(trim(100*$cart['total_cost']));
        $post[] = "AVS=".urlencode(trim(preg_replace("/[^\w]/",'',strtoupper($userinfo['b_address'].$userinfo['b_zipcode']))));
        $post[] = "REF=".$_ref;

        list($a, $return) = func_https_request('GET',"https://lt3a.caledoncard.com:443/".join("&",$post));
        parse_str($return, $ret);

    } else {
        $bill_output['is_preauth'] = true;
        $extra_order_data = array(
            'caledon_tid' => $_ref,
            'capture_status' => 'A',
        );
    }
}

$bill_output['code'] = ($ret['CODE'] === '0000') ? 1 : 2;

if ($ret['TEXT'])
    $bill_output['billmes'] = $ret['TEXT'];

if ($ret['AUTH'])
    $bill_output['billmes'].= " (AuthCode: ".$ret['AUTH'].")";

if ($ret['WARNING'])
    $bill_output['billmes'].= " (Warning: ".$ret['WARNING'].")";

if ($ret['UID'])
    $bill_output['billmes'].= " (UID: ".$ret['UID'].")";

if ($ret['AVS'])
    $bill_output['avsmes'] = (empty($avserr[$ret['AVS']]) ? "Code: ".$ret['AVS'] : $avserr[$ret['AVS']]);
?>
