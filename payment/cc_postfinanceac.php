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
 * "Post Finance (Advanced e-Commerce)" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_postfinanceac.php,v 1.16.2.1 2011/01/10 13:12:07 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ($_SERVER['REQUEST_METHOD'] == "GET" && isset($_GET['_status']) && isset($_GET['key'])) {
    require './auth.php';

    if (!func_is_active_payment('cc_postfinanceac.php'))
        exit;

    $module_params = func_get_pm_params('cc_postfinanceac.php');

    x_load('crypt');

    $ref = text_decrypt($_GET['key']);

    require_once ($xcart_dir . '/payment/sha1.php');

    $statuses = array(
        0 => 'Incomplete or invalid',
        1 => 'Cancelled by client',
        2 => 'Authorization refused',
        4 => 'Order stored',
        41 => 'Waiting client payment',
        5 => 'Authorized',
        51 => 'Authorization waiting',
        52 => 'Authorization not known',
        55 => 'Stand-by',
        59 => 'Authoriz. to get manually',
        6 => 'Authorized and cancelled',
        61 => 'Author. deletion waiting',
        62 => 'Author. deletion uncertain',
        63 => 'Author. deletion refused',
        64 => 'Authorized and cancelled',
        7 => 'Payment deleted',
        71 => 'Payment deletion pending',
        72 => 'Payment deletion uncertain',
        73 => 'Payment deletion refused',
        74 => 'Payment deleted',
        75 => 'Deletion processed by merchant',
        8 => 'Refund',
        81 => 'Refund pending',
        82 => 'Refund uncertain',
        83 => 'Refund refused',
        84 => 'Payment declined by the acquirer',
        85 => 'Refund processed by merchant',
        9 => 'Payment requested',
        91 => 'Payment processing',
        92 => 'Payment uncertain',
        93 => 'Payment refused',
        94 => 'Refund declined by the acquirer',
        95 => 'Payment processed by merchant',
        99 => 'Being processed'
    );

    $bill_output['sessid'] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref = '" . $ref . "'");

    $sha = strtoupper(sha1($orderID . $currency . $amount . $PM . $ACCEPTANCE . $STATUS . $CARDNO . $PAYID . $NCERROR . $BRAND . $module_params['param05']));

    if (isset($SHASIGN) && $SHASIGN != $sha) {

        // SHA signature is wrong
        $bill_output['code'] = 2;
        $bill_output['billmes'] = 'Error: SHA signature is wrong';

    } else {
        if ($_status == 1 && ($STATUS == 5 || $STATUS == 9)) {
            $bill_output['code'] = 1;

        } elseif ($STATUS == 51) {
            $bill_output['code'] = 3;

        } else {
            $bill_output['code'] = 2;
        }

        if (isset($statuses[$STATUS]))
            $bill_output['billmes'] = $statuses[$STATUS];

        if (isset($PAYID)) {
            $bill_output['billmes'] .= ' (Transaction ID: ' . $PAYID . ')';

            $extra_order_data = array(
                'txnid' => $PAYID,
                'capture_status' => ($bill_output['code'] == 1 && $STATUS == 5) ? 'A' : ''
            );

            if ($bill_output['code'] == 1 && $STATUS == 5)
                $bill_output['is_preauth'] = true;
        }

        if ($bill_output['code'] == 2 && $NCERROR > 0)
            $bill_output['billmes'] .= ' (error code: ' . $NCERROR . ')';

        if (isset($amount) && isset($currency) && ($bill_output['code'] == 1 || $bill_output['code'] == 3)) {
            $payment_return = array(
                'total' => $amount,
                'currency' => $currency,
                '_currency' => $module_params['param02']
            );
        }

        if (isset($AVVCHECK))
            $bill_output['avsmes'] = $AVVCHECK;

        if (isset($CVCCHECK))
            $bill_output['cvvmes'] = $CVCCHECK;

    }

    require($xcart_dir.'/payment/payment_ccend.php');

} elseif ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_GET['callback'])) {

    // Callback for Direct sale

    require './auth.php';

    if (!func_is_active_payment('cc_postfinanceac.php'))
        exit;

    x_load('crypt');

    $ref = text_decrypt($_GET['key']);
    $cnt = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[cc_pp3_data] WHERE ref = '" . $ref . "'");
    if (!$cnt)
        exit;

    echo '<?xml version="1.0"?>
    <ncresponse orderID="' . $_POST['orderID'] . '" amount="' . $_POST['amount'] . '" currency="' . $_POST['currency'] . '" />';

} else {

    if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

    $ordr = $module_params['param04'] . join("-", $secure_oid);
    if (!$duplicate)
        db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."')");

    require_once ($xcart_dir . '/payment/sha1.php');

    x_load('crypt');

    $key = text_crypt($ordr);

    $total = preg_replace('/\D/Ss', '', $cart["total_cost"] * 100);

    $fields = array(
        'PSPID' => $module_params["param01"],
        'orderID' => $ordr,
        'amount' => $total,
        'currency' => $module_params["param02"],
        'language' => 'en_US',
        'CN' => $userinfo['b_firstname'] . ($userinfo['b_firstname'] ? ' ' : '') . $userinfo['b_lastname'],
        'EMAIL' => $userinfo['email'],
        'owneraddress' => $userinfo['b_address'] . ' ' . $userinfo["b_address_2"],
        'ownerZIP' => $userinfo['b_zipcode'],
        'ownertown' => $userinfo['b_city'],
        'ownercty' => $userinfo['b_country'],
        'ownerelno' => $userinfo['phone'],
        'COM' => 'Order(s) #' . join("; #", $secure_oid),
        'accepturl' => $current_location . '/payment/cc_postfinanceac.php?_status=1&key=' . $key,
        'declineurl' => $current_location . '/payment/cc_postfinanceac.php?_status=2&key=' . $key,
        'exceptionurl' => $current_location . '/payment/cc_postfinanceac.php?_status=3&key=' . $key,
        'cancelurl' => $current_location . '/payment/cc_postfinanceac.php?_status=4&key=' . $key
    );

    $sha_sig = $ordr . $total . $module_params['param02'] . $module_params['param01'] . $module_params['param03'];

    $fields['SHASign'] = strtoupper(sha1($sha_sig));

    func_create_payment_form(
        'https://e-payment.postfinance.ch/ncol/' . ($module_params["testmode"] == 'Y' ? 'test' : 'prod') . '/orderstandard.asp',
        $fields,
        "PostFinance (Advanced e-Commerce)"
    );

}
exit;

?>
