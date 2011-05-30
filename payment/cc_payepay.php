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
 * "" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_payepay.php,v 1.38.2.1 2011/01/10 13:12:07 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST["option1"]) && !empty($_POST["cc_status"])) {

    require './auth.php';

    if (!func_is_active_payment('cc_payepay.php'))
        exit;

    $bill_output['sessid'] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$option1."'");

    $bill_output['code'] = (($cc_status == 'pass') ? 1 : 2);
    if ($cc_status!="pass")
        $bill_output['billmes'] = 'Declined';
    if (!$orderid)
        $bill_output['billoutput'] .= " (OrderID: ".$orderid.")";
    if (!$sku)
        $bill_output['billoutput'] .= " (SKU: ".$sku.")";

    require($xcart_dir.'/payment/payment_ccend.php');

} else {
    if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

    $pp_companyid = $module_params['param01'];
    $pp_payepaylink = $module_params['param03'];
    $ordr = $module_params['param02'];
    if (!$duplicate)
        func_array2insert(
            'cc_pp3_data',
            array(
                'ref' => addslashes($ordr),
                'sessionid' => $XCARTSESSID
            )
        );

    $string = '';
    foreach ($products as $product)
        $string[] = " - ".$product['product']." (".$product['price']." x ".$product['amount'].")";

    if (@is_array($cart['giftcerts']) && count($cart['giftcerts']) > 0) {
        foreach ($cart['giftcerts'] as $tmp_gc)
            $string[] = " - GIFT CERTIFICATE (".$tmp_gc['amount']." x 1)";
    }

    $returnurl = $http_location.'/payment/cc_payepay.php';

    $fields = array(
        'companyid' => $pp_companyid,
        'tr_type' => '1A2B3C',
        'total' => $cart['total_cost'],
        'product1' => $string,
        'b_firstname' => $bill_firstname,
        's_firstname' => $ship_firstname,
        'b_middlename' => '',
        's_middlename' => '',
        'b_lastname' => $bill_lastname,
        's_lastname' => $ship_lastname,
        'email' => $userinfo['email'],
        'b_address' => $userinfo['b_address'],
        's_address' => $userinfo['s_address'],
        'b_city' => $userinfo['b_city'],
        's_city' => $userinfo['s_city'],
        'b_country' => $userinfo['b_country'],
        's_country' => $userinfo['s_country'],
        'b_zip' => $userinfo['b_zipcode'],
        's_zip' => $userinfo['s_zipcode'],
        'b_state' => $userinfo['b_state'],
        's_state' => $userinfo['s_state'],
        'b_tel' => $userinfo['phone'],
        'delivery' => 'N',
        'formget' => 'N',
        'option1' => $ordr,
        'redirect' => $returnurl,
        'redirectfail' => $returnurl
    );
    func_create_payment_form($pp_payepaylink, $fields, 'PayEPay');

    exit;
}

?>
