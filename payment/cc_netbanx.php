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
 * "NetBanx" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_netbanx.php,v 1.40.2.3 2011/01/10 13:12:06 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!isset($REQUEST_METHOD))
    $REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];

if ($REQUEST_METHOD == 'GET' && isset($_GET['oid'])) {
    require './auth.php';

    if (defined('NETBANX_DEBUG')) {
        func_pp_debug_log('netbanx', 'R', $_GET);
    }

    $skey = $_GET['oid'];

    $tmp = func_query_first("SELECT sessionid, trstat FROM $sql_tbl[cc_pp3_data] WHERE ref='" . $skey . "'");
    $pg_flow_status = explode("|", $tmp['trstat']);

    if($pg_flow_status[0] == 'GO' && $_GET['client_return'] == '1') {

        $bill_output['sessid'] = $tmp['sessionid'];
        $bill_output['code'] = 2;

        $bill_output['billmes'] = 'Canceled by customer';

        include($xcart_dir.'/payment/payment_ccend.php');

    } else {

        include($xcart_dir.'/payment/payment_ccview.php');

    }

} elseif ($REQUEST_METHOD == 'POST' && isset($_POST['nbx_status']) && $_POST['nbx_merchant_reference']) {
    require './auth.php';

    if (!func_is_active_payment('cc_netbanx.php'))
        exit;

    if (defined('NETBANX_DEBUG')) {
        func_pp_debug_log('netbanx', 'C', $_POST);
    }

    $oid = $_POST['nbx_merchant_reference'];

    $bill_output['sessid'] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$oid."'");
    if (empty($bill_output['sessid']))
        exit;

    $bill_output['code'] = ($_POST['nbx_status'] == 'authorized' || $_POST['nbx_status'] == 'passed') ? 1 : 2;
    $bill_output['billmes'] = $_POST['nbx_status'];

    if(!empty($_POST['nbx_netbanx_reference']))
        $bill_output['billmes'] .= " (NetBanx Reference: ".$_POST['nbx_netbanx_reference'].")";
    if(!empty($_POST['nbx_CVV_auth']))
        $bill_output['billmes'] .= " (CVV Auth: ".$_POST['nbx_CVV_auth'].")";
    if(!empty($_POST['nbx_houseno_auth']) || !empty($_POST['nbx_postcode_auth']))
        $bill_output['billmes'] .= " (AVS Auth: ".$_POST['nbx_houseno_auth']." ".$_POST['nbx_postcode_auth'].")";

    // nbx_payment_amount nbx_currency_code nbx_merchant_reference netbanx_reference secret_key
    if ($_POST['nbx_checksum']) {
        require_once ($xcart_dir . '/payment/sha1.php');
        $secret_key = func_query_first_cell("select param06 from $sql_tbl[ccprocessors] where processor='cc_netbanx.php'");
        $signature = sha1($nbx_payment_amount.$nbx_currency_code.$oid.$_POST['nbx_netbanx_reference'].$secret_key);
        if ($signature != $_POST['nbx_checksum']) {
            $bill_output['billmes'] .= " (Warning: SHA1 checksum NOT MATCHED, order declined)";
            $bill_output['code'] = 2;
        } else {
            $bill_output['billmes'] .= " (SHA1 checksum matched)";
        }
    }

    if (isset($nbx_payment_amount)) {
        $payment_return = array(
            'total' => ($nbx_currency_code == 'JPY') ? $nbx_payment_amount : $nbx_payment_amount/100
        );
    }

    $skey = $oid;
    require($xcart_dir.'/payment/payment_ccmid.php');
    require($xcart_dir.'/payment/payment_ccwebset.php');

} else {

    if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

    $pp_merch = $module_params['param01'];
    $pp_url = $module_params['param02'];
    $ordr = $module_params['param03'].join("-",$secure_oid);
    $currency = $module_params['param04'];

    $tmp = @unserialize($module_params['param05']);
    $pp_types = is_array($tmp) ? implode(", ", $tmp) : '';

    $cb_url = $current_location.'/payment/cc_netbanx.php';
    $cb_url_redirect = $cb_url . "?oid=" . htmlspecialchars($ordr);

    if(!$duplicate)
        db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");

    // if currency is japanese yen, pass the amount as is,
    // otherwise passing the minor units (digits) only
    $pay_amount = ($currency == 'JPY') ? $cart['total_cost'] : $cart['total_cost']*100;

    $fields = array(
        'nbx_payment_amount' => $pay_amount,
        'nbx_merchant_reference' => $ordr,
        'nbx_currency_code' => $currency,
        'nbx_language' => 'EN',
        'nbx_payment_type' => $pp_types,
        'nbx_success_url' => $cb_url,
        'nbx_failure_url' => $cb_url,
        "nbx_success_redirect_url" => $cb_url_redirect, 
        "nbx_failure_redirect_url" => $cb_url_redirect, 
        "nbx_return_url" => $cb_url_redirect . "&client_return=1",
        'nbx_email' => $userinfo['email'],
        'nbx_cardholder_name' => $bill_firstname." ".$bill_lastname,
        'nbx_houseno' => '',
        'nbx_postcode' => $userinfo['b_zipcode'],
        'nbx_success_show_content' => 1,
        'nbx_failure_show_content' => 1,
        'nbx_redirect_exclude' => 'nbx_return_url,nbx_houseno_auth,nbx_postcode_auth,nbx_merchant_reference,nbx_timeout,nbx_success_redirect_url,nbx_houseno,nbx_netbanx_reference,nbx_email,nbx_cardholder_name,nbx_currency_code,nbx_CVV_auth,nbx_postcode,nbx_failure_redirect_url,nbx_success_url,nbx_checksum,nbx_failure_url,nbx_payment_amount',
        'nbx_cgi_exclude' => 'nbx_redirect_exclude,nbx_return_url,nbx_success_show_content,nbx_failure_show_content,nbx_success_redirect_url,nbx_failure_redirect_url,nbx_failure_url,nbx_success_url'
    );

    // nbx_payment_amount nbx_currency_code nbx_merchant_reference secret_key
    if (!empty($module_params['param06'])) {
        require_once ($xcart_dir . '/payment/sha1.php');
        $signature = sha1($pay_amount.$currency.$ordr.$module_params['param06']);
        $fields['nbx_checksum'] = $signature;
    }

    if (defined('NETBANX_DEBUG')) {
        func_pp_debug_log('netbanx', 'I', $fields);
    }

    func_create_payment_form($pp_url, $fields, 'NetBanx');
    exit();
}

?>
