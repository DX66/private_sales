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
 * $Id $
 */

if (!isset($REQUEST_METHOD))
    $REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];

function func_goem_avs_response($code="")
{

    $code = trim($code);

    if (empty($code))
        return false;

    $avserr = array(
        'A' => "Address matches - Zip Code does not ",
        'B' => "Street address match, Postal code in wrong format. (international issuer) ",
        'C' => "Street address and postal code in wrong formats ",
        'D' => "Street address and postal code match (international issuer) ",
        'E' => "AVS Error ",
        'G' => "Service not supported by non-US issuer ",
        'I' => "Address information not verified by international issuer. ",
        'M' => "Street Address and Postal code match (international issuer) ",
        'N' => "No match on address or Zip Code ",
        'O' => "No Response sent ",
        'P' => "Postal codes match, Street address not verified due to incompatible formats. ",
        'R' => "Retry - system is unavailable or timed out ",
        'S' => "Service not supported by issuer ",
        'U' => "Address information is unavailable ",
        'W' => "9-digit Zip Code matches - address does not ",
        'X' => "Exact match ",
        'Y' => "Address and 5-digit Zip Code match ",
        'Z' => "5-digit zip matches - address does not ",
        '0' => "No Response sent "
    );

    return ($avserr[$code] ? $avserr[$code] : "AVSCode: ".$code);
}

if ($REQUEST_METHOD == 'GET' && isset($_GET['Status']) && isset($_GET['OrderID'])) {

    require './auth.php';

    if (!func_is_active_payment('cc_goem_pf.php')){
        exit;
    }

    x_session_register('secure_oid');

    $bill_output['sessid'] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".addslashes($_GET["OrderID"])."'");

    $bill_output['code'] = ($_GET["Status"] == "1") ? 1 : 2;
    $bill_output['billmes'] = $_GET["authresponse"];
    if (!empty($_GET['approval_code']))
        $bill_output['billmes'] .= "(Approval code: ".$_GET["approval_code"].")";
    $avsmes = func_goem_avs_response($_GET['avs']);
    if (!empty($avsmes))
        $bill_output['avsmes'] = func_goem_avs_response($_GET["avs"]);

    require $xcart_dir.'/payment/payment_ccend.php';

} else {

    if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

    $pp_merch = $module_params['param01'];

    $goem_oid = $module_params['param03'].join("-",$secure_oid);
    if (!$duplicate)
        db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($goem_oid)."','".$XCARTSESSID."')");

    $post = array(
        'Merchant' => $pp_merch,
        'OrderId' => $goem_oid,
        'total' => $cart['total_cost'],
        'email' => $userinfo['email'],
        'URL' => $https_location.'/payment/cc_goem_pf.php'
    );

    func_create_payment_form("https://secure.goemerchant.com/secure/gateway/process.aspx", $post, $module_params['module_name']);
    exit();
}

?>
