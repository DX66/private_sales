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
 * PSiGate
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_psigate.php,v 1.38.2.1 2011/01/10 13:12:07 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ($_SERVER['REQUEST_METHOD'] == "GET" && isset($_GET["OrdNo"])) {
    require './auth.php';

    $bill_output['sessid'] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$OrdNo."'");

    if (!empty($RefNo) && empty($Err)) {
        $bill_output['code'] = 1;
        $bill_output['billmes'] = "RefNo: ".$RefNo." (Approval Code: ".$Code.")";

    } else {
        $bill_output['code'] = 2;
        $bill_output['billmes'] = $_GET['Err'];
    }

    if (isset($Total)) {
        $payment_return = array(
            'total' => $Total
        );
    }

    require($xcart_dir.'/payment/payment_ccend.php');

} else {

    if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

    $expiry_month = substr($userinfo['card_expire'], 0, 2);
    $expiry_year = substr($userinfo['card_expire'], 2, 2);;

    $ordr = $module_params ['param02'] . join('-', $secure_oid);
    $url = $http_location.'/payment/cc_psigate.php';

    if (!$duplicate)
        db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."')");

    $fields = array(
        'Email' => $userinfo["email"],
        'Bcity' => $userinfo["b_city"],
        'Bcountry' => $userinfo["b_country"],
        'Bname' => $bill_name,
        'Bzip' => $userinfo["b_zipcode"],
        'Bstate' => $userinfo["b_state"],
        'Baddr1' => $userinfo["b_address"],
        'Phone' => $userinfo["phone"],
        'MerchantID' => $module_params ["param01"],
        'Oid' => $ordr,
        'Userid' => $cart["login"],
        'CardNumber' => $userinfo["card_number"],
        'ExpMonth' => $expiry_month,
        'ExpYear' => $expiry_year,
        'IP' => func_get_valid_ip($REMOTE_ADDR),
        'FullTotal' => $cart["total_cost"],
        'ChargeType' => '1',
        'ThanksURL' => $url,
        'NoThanksURL' => $url
    );

    func_create_payment_form('https://order.psigate.com/psigate.asp', $fields, 'PSiGate');
}

exit;

?>
