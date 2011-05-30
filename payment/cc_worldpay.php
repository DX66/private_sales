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
 * WorldPay - HTML Redirect - Select Junior
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_worldpay.php,v 1.66.2.1 2011/01/10 13:12:07 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST["cartId"]) && !empty($_POST["transStatus"])) {
    require './auth.php';

    if (!func_is_active_payment('cc_worldpay.php'))
        exit;

    $bill_output['sessid'] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$cartId."'");
    $bill_output['code'] = $transStatus == 'Y' ? 1 : 2;

    if (!empty($rawAuthMessage))
        $bill_output['billmes'] = $rawAuthMessage;

    if (!empty($transId))
        $bill_output['billmes'] .= " (TransId: ".$transId.")";

    if (isset($cost)) {
        $payment_return = array(
            'total' => $cost
        );

        if (isset($currency)) {
            $payment_return['currency'] = $currency;
            $payment_return['_currency'] = func_query_first_cell("SELECT param02 FROM $sql_tbl[ccprocessors] WHERE processor='cc_worldpay.php'");
        }
    }

    if (isset($AVS))
        $bill_output['avsmes'] = 'AVS code: ' . $AVS;

    x_session_id($bill_output['sessid']);
    x_session_register('is_redirect');
    $weblink = $is_redirect == 'Y' ? 2 : 1;

    echo "<wpdisplay item=banner><br />\n";

    require($xcart_dir . '/payment/payment_ccend.php');

} else {

    if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

    x_session_register('is_redirect');

    $is_redirect = $module_params['param05'];

    $url = ($module_params['testmode'] == 'N') ? 'https://select.wp3.rbsworldpay.com/wcc/purchase' : "https://select-test.wp3.rbsworldpay.com/wcc/purchase";

    $ordr = str_replace(" ", '', $module_params['param04']) . join("-", $secure_oid);
    if (!$duplicate)
        db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."')");

    $fields = array(
        'instId' => $module_params['param01'],
        'cartId' => $ordr,
        'authMode' => 'A',
        'amount' => $cart['total_cost'],
        'currency' => $module_params['param02'],
        'desc' => "Order #" . $ordr,
        'name' => $bill_name,
        'tel' => $userinfo['phone'],
        'fax' => $userinfo['fax'],
        'email' => $userinfo['email'],
        'address' => $userinfo['b_address']." ".$userinfo['b_address_2'].", ".$userinfo['b_city'].", ".$userinfo['b_statename'].", ".$userinfo['b_countryname'],
        'postcode' => $userinfo['b_zipcode'],
        'country' => $userinfo['b_country']
    );

    $fields['testMode'] = (($module_params['testmode'] == 'N') ? 0 : 100);

    switch ($module_params['testmode']) {
        case 'T':
            $fields['name'] = 'AUTHORISED';
            break;

        case 'R':
            $fields['name'] = 'REFUSED';
            break;

        case 'E':
            $fields['name'] = 'ERROR';
            break;

        case 'C':
            $fields['name'] = 'CAPTURED';
            break;
    }

    if ($module_params['param06']) {
        $fields['signatureFields'] = 'amount:currency:cartId';
        $fields['signature'] = md5($module_params['param06'] . ':' . $fields['amount'] . ':' . $fields['currency'] . ':' . $fields['cartId']);
    }

    if ($module_params['use_preauth'] == 'Y' || func_is_preauth_force_enabled($secure_oid))
        $fields['authMode'] = 'E';

    func_create_payment_form($url, $fields, $module_params['module_name']);

}

exit;

?>
