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
 * "Quantum Gateway - QGWdatabase Engine" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_quantum.php,v 1.18.2.1 2011/01/10 13:12:07 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['trans_result']) && !empty($_POST['trans_result'])) {

    require './auth.php';
    if (!func_is_active_payment('cc_quantum.php'))
        exit;

    $bill_output['sessid'] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref = '".$ID."'");

    if ($trans_result == 'APPROVED') {
        $bill_output['code'] = 1;
        $bill_output['billmes'] = "Approved.";

    } else {
        $bill_output['code'] = 2;
        $bill_output['billmes'] = "Declined: ".$decline_reason.";";
    }

    $bill_output['billmes'] .= " Transaction ID: ".$transID."; MaxMind score: ".$max_score;

    $bill_output['avsmes'] = $avs_result;
    $bill_output['cvvmes'] = $cvv2_result;

    require $xcart_dir.'/payment/payment_ccend.php';

} else {

    if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

    $ordr = $module_params['param04'].join("-", $secure_oid);
    if (!$duplicate)
        db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref, sessionid) VALUES ('".addslashes($ordr)."', '".$XCARTSESSID."')");

    $fields = array(
        'gwlogin' => $module_params["param01"],
        'amount' => $cart["total_cost"],
        'BADDR1' => $userinfo["b_address"],
        'BCUST_EMAIL' => $userinfo["email"],
        'BCOUNTRY' => $userinfo['b_country'],
        'BCITY' => $userinfo['b_city'],
        'BSTATE' => $userinfo['b_state'],
        'FNAME' => $bill_firstname,
        'LNAME' => $bill_lastname,
        'PHONE' => $userinfo['phone'],
        'post_return_url_approved' => $current_location."/payment/cc_quantum.php",
        'post_return_url_declined' => $current_location."/payment/cc_quantum.php",
        'returning_visit' => "N",
        'BZIP1' => $userinfo["b_zipcode"],
        'ID' => $ordr
    );

    if (!empty($module_params['param02'])) {
        $fields['RestrictKey'] = $module_params["param02"];
    }

    func_create_payment_form("https://secure.quantumgateway.com/cgi/qgwdbe.php", $fields, 'QUANTUM');
    exit;
}
?>
