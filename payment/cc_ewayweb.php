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
 * eWay Web
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_ewayweb.php,v 1.35.2.1 2011/01/10 13:12:06 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST["ewayTrxnStatus"])) {
    require './auth.php';

    $bill_output['sessid'] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$_POST["eWAYoption1"]."'");

    $bill_output['code'] = (preg_match("/^true$/i",$_POST['ewayTrxnStatus']) ? 1 : 2);
    $bill_output['billmes'] = '';
    if(!empty($_POST['eWAYAuthCode']))    $bill_output['billmes'].= " (ewayAuthCode: ".$_POST['eWAYAuthCode'].") ";
    if(!empty($_POST['ewayTrxnError']))    $bill_output['billmes'].= " (ewayTrxnError: ".$_POST['ewayTrxnError'].") ";

    if (isset($ewayReturnAmount)) {
        $ewayReturnAmount = preg_replace("/[^\d\.]/", '', $ewayReturnAmount);
        $payment_return = array(
            'total' => (empty($ewayReturnAmount) ? 0 : $ewayReturnAmount / 100)
        );
    }

    require($xcart_dir.'/payment/payment_ccend.php');

} else {

    if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

    $_orderids = $module_params ['param03'] . join('-', $secure_oid);
    if (!$duplicate)
        db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($_orderids)."','".$XCARTSESSID."')");

    $fields = array(
        'ewayCustomerID' => $module_params ["param01"],
        'ewayTotalAmount' => 100 * $cart["total_cost"],
        'ewayCustomerInvoiceRef' => $_orderids,
        'ewayCustomerFirstName' => $bill_firstname,
        'ewayCustomerLastName' => $bill_lastname,
        'ewayCustomerEmail' => $userinfo["email"],
        'ewayCustomerAddress' => $userinfo["b_address"],
        'ewayCustomerPostcode' => $userinfo["b_zipcode"],
        'ewayOption1' => $_orderids,
        'ewayOption3' => $module_params['testmode'] == 'Y' ? 'TRUE' : 'FALSE',
        'ewayURL' => $http_location . '/payment/cc_ewayweb.php'
    );

    func_create_payment_form('https://www.eway.com.au/gateway/payment.asp', $fields, 'eWay');
}

exit;

?>
