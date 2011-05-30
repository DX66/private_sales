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
 * GoEmerchant - EZ Payment Gateway Direct payment processing script
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_goem.php,v 1.25.2.1 2011/01/10 13:12:06 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!isset($REQUEST_METHOD))
    $REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];

if ($REQUEST_METHOD == 'GET' && isset($_GET['Status']) && isset($_GET['OrderID'])) {

    require './auth.php';

    if (!func_is_active_payment('cc_goem.php')){
        exit;
    }

    func_pm_load('cc_goem');

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
        'Cardstreet' => $userinfo['b_address'],
        'Cardcity' => $userinfo['b_city'],
        'Cardstate' => $userinfo['b_state'],
        'Cardzip' => $userinfo['b_zipcode'],
        'Cardcountry' => $userinfo['b_country'],
        'URL' => $https_location.'/payment/cc_goem.php'
    );

    $cc_fields = func_cc_goem_get_cc_fields();

    func_create_payment_form(
        'https://secure.goemerchant.com/secure/gateway/direct.aspx',
        $post,
        'GoEmerchant',
        'post',
        true,
        $cc_fields
    );

    exit();
}

?>
