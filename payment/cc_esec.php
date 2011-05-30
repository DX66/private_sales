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
 * "eSec - ReDirect" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_esec.php,v 1.37.2.1 2011/01/10 13:12:06 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!isset($REQUEST_METHOD)) {
    $REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
}

if ($REQUEST_METHOD == 'GET' && !empty($_GET["ref-id"]) && !empty($_GET['message'])) {
    require './auth.php';

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

    $bill_output['sessid'] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$_GET["ref-id"]."'");

    preg_match("/^(\d{3})/U", $message, $out);
    $code = $out[1];

    if( $code >= 200 && $code <= 299) {
        $bill_output['code'] = 1;
    } else {
        $bill_output['code'] = 2;
    }

    $bill_output['billmes'] = $message;

    if ($_GET["auth-id"]) {
        $bill_output['billmes'] .= " (Auth ID: ".$_GET["auth-id"].")";
    }
    if ($_GET["txn-id"]) {
        $bill_output['billmes'] .= " (Txn ID: ".$_GET["txn-id"].")";
    }
    if ($_GET["eft-response"]) {
        $bill_output['billmes'] .= " (EFT ".(empty($staerr[$_GET["eft-response"]]) ? "Code: ".$_GET["eft-response"] : "Response: ".$staerr[$_GET["eft-response"]]).")";
    }
    if ($_GET['signature']) {
        $bill_output['billmes'] .= " (Signature: ".$_GET['signature'].")";
    }

    require $xcart_dir.'/payment/payment_ccend.php';
} else {
    if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

    $esec_login = $module_params['param01'];
    $esec_prefix = $module_params['param03'];
    $esec_3dsecure_enabled = ($module_params['param04'] == 'Y');
    $esec_eps_password = $module_params['param05'];
    $esec_eps_merchantid = $module_params['param06'];

    $ordr = $esec_prefix.join('-', $secure_oid);
    if (!$duplicate) {
        db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($ordr)."', '".$XCARTSESSID."')");
    }

    if ($module_params['testmode'] == 'N') {
        $test = 'false';
        if (is_visa($userinfo['card_number'])) {
            $userinfo['card_type'] = 'visa';
        } elseif (is_amex($userinfo['card_number'])) {
            $userinfo['card_type'] = 'amex';
        } elseif (is_mc($userinfo['card_number'])) {
            $userinfo['card_type'] = 'mastercard';
        } elseif (is_dc($userinfo['card_number'])) {
            $userinfo['card_type'] = 'dinersclub';
        } elseif (is_jcb($userinfo['card_number'])) {
            $userinfo['card_type'] = 'jcb';
        }
    } else {
        $test = 'true';
        $esec_login = 'test';
        $esec_eps_password = 'abc123';
        $esec_eps_merchantid = '22123456';
        $userinfo['card_type'] = 'testcard';
        $userinfo['card_number'] = $module_params['testmode'] == 'A' ? 'testsuccess' : 'testfailure';
        $userinfo['card_cvv2'] = '999';
    }

    $post_url = "https://sec.aba.net.au/cgi-bin/service/authorise/".$esec_login;
    $fields = array(
        'EPS_MERCHANT' => $esec_login,
        'EPS_RESULTURL' => $current_location.'/payment/cc_esec.php',
        'EPS_REDIRECT' => 'true',
        'EPS_VERSION' => 3,
        'EPS_TEST' => "$test",
        'EPS_REFERENCEID' => $ordr,
        'EPS_CARDNUMBER' => $userinfo['card_number'],
        'EPS_CARDTYPE' => $userinfo['card_type'],
        'EPS_EXPIRYMONTH' => (0 + substr($userinfo['card_expire'], 0, 2)),
        'EPS_EXPIRYYEAR' => (2000 + substr($userinfo['card_expire'], 2, 2)),
        'EPS_CCV' => $userinfo['card_cvv2'],
        'EPS_NAMEONCARD' => $userinfo['card_name'],
        'EPS_AMOUNT' => $cart['total_cost'],
        'EPS_3DSECURE' => 'false'
    );

    if ($esec_3dsecure_enabled) {
        $post_url = "https://www.securepay.com.au/3dsecure/verifyEnrollment.jsp";
        $fields['EPS_VERSION'] = 4;
        $fields['EPS_3DSECURE'] = "true";
        $fields['EPS_PASSWORD'] = $esec_eps_password;
        $fields['MerchantID'] = $esec_eps_merchantid;
        $fields['3D_XID'] = strtoupper(substr(uniqid(rand(), true), 0, 20));
    }

    func_create_payment_form($post_url, $fields, 'eSec');
}
exit;

?>
