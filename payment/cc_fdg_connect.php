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
 * First Data Global Gateway Connect
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_fdg_connect.php,v 1.16.2.2 2011/03/10 09:01:03 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!isset($REQUEST_METHOD)) {
    $REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
}

if ($REQUEST_METHOD == 'POST' && (isset($_POST['status']) || isset($_POST['oid']))) {

    require './auth.php';

    include $xcart_dir.'/payment/sha1.php';

    x_load('payment');
    func_pm_load('cc_fdg_connect');

    x_session_register('secure_oid');

    $transaction_data = func_query_first("SELECT * FROM $sql_tbl[cc_pp3_data] WHERE ref='".$oid."'");
    $bill_output['sessid'] = $transaction_data['sessionid'];
    $fdg_check_str = $transaction_data['param1'];

    $module_params = func_get_pm_params('cc_fdg_connect.php');

    $fdg_region = $module_params['param01'];

    if ($fdg_region == 'EMEA') {

        $signature = func_cc_fdg_encrypt(str_replace(FDG_CONNECT_APPROVAL_TOKEN, $approval_code, $fdg_check_str));

        if (strcmp($signature, $response_hash) != 0) {
            $status = 'DECLINED';
            $fail_reason = "Signature check failed";
        }

        $failReason = $fail_reason;

    }

    if ($status != 'APPROVED') {

        $bill_output['code'] = 2;
        $bill_output['billmes'] = $status.": ".$failReason;

    } else {

        $bill_output['code'] = $approval_code ? 1 : 2;

        if ($bill_output['code'] == 1) {

            $is_preauth = ($module_params['use_preauth'] == 'Y' || func_is_preauth_force_enabled($secure_oid));

            if ($is_preauth) {
                $bill_output['is_preauth'] = true;
                $extra_order_data['capture_status'] = 'A';
            }

            $bill_output['billmes'] .= func_cc_fdg_parse_response($approval_code);

            if (
                $response_code_3dsecure
                && isset($fdg_sec3dcodes[$response_code_3dsecure])
            ) {
                $bill_output['billmes'] .= "\n3D secure check: " . $fdg_sec3dcodes[$response_code_3dsecure];
            }
        }

    }

    require $xcart_dir.'/payment/payment_ccend.php';

} else {

    if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

    include $xcart_dir . '/payment/sha1.php';

    $fdg_region         = $module_params['param01'];
    $fdg_currency       = $module_params['param02'];
    $fdg_storename      = $module_params['param03'];
    $fdg_mode           = $module_params['param04'];
    $fdg_prefix         = $module_params['param05'];
    $fdg_secret_key     = $module_params['param06'];
    $fdg_timezome       = $module_params['param07'];

    $is_preauth         = ($module_params['use_preauth'] == 'Y' || func_is_preauth_force_enabled($secure_oid));

    $fdg_total_cost     = number_format($cart['total_cost'], 2, '.', '');
    $trantype           = $is_preauth ? 'preauth' : 'sale';
    $fdg_oid            = fdg_trim($fdg_prefix.join("-", $secure_oid), 100);
    $fdg_subtotal       = number_format($cart['total_cost'] - $cart['shipping_cost'] - $cart['tax_cost'], 2, '.', '');
    $fdg_response_url   = $current_location.'/payment/'.$module_params['processor'];
    $fdg_trim_limit     = ($fdg_region == 'EMEA') ? 30 : 96;
    $fdg_user_name      = fdg_trim($userinfo['firstname']." ".$userinfo['lastname'], $fdg_trim_limit);

    $post = array(

        'storename'         => $fdg_storename,
        'txntype'           => $trantype,
        'oid'               => $fdg_oid,
        'chargetotal'       => $fdg_total_cost,
        'mode'              => $fdg_mode,
        'subtotal'          => $fdg_subtotal,
        'shipping'          => number_format($cart['shipping_cost'], 2, '.', ''),
        'tax'               => number_format($cart['tax_cost'], 2, '.', ''),
        'comments'          => fdg_trim($customer_notes, 1024),

        'bcompany'          => fdg_trim($userinfo['company'], $fdg_trim_limit),
           'bname'          => $fdg_user_name,
           'baddr1'         => fdg_trim($userinfo['b_address'], $fdg_trim_limit),
        'baddr2'            => fdg_trim(@$userinfo['b_address_2'], $fdg_trim_limit),
           'bcity'          => fdg_trim($userinfo['b_city'], $fdg_trim_limit),
           'bcountry'       => $userinfo['b_country'],
        'bzip'              => $userinfo['b_zipcode'],

        'sname'             => $fdg_user_name,
        'saddr1'            => fdg_trim($userinfo['s_address'], $fdg_trim_limit),
        'saddr2'            => fdg_trim(@$userinfo['s_address_2'], $fdg_trim_limit),
        'scity'             => fdg_trim($userinfo['s_city'], $fdg_trim_limit),
        'scountry'          => $userinfo['s_country'],
        'szip'              => $userinfo['s_zipcode'],

        'phone'             => fdg_trim($userinfo['phone'], 20),
        'fax'               => fdg_trim($userinfo['fax'], 20),
        'email'             => fdg_trim($userinfo['email'], 45),
        'userid'            => fdg_trim($userinfo['login'], 30),
        'customerid'        => fdg_trim($userinfo['login'], 30),

    );


    if ($fdg_region == 'EMEA') {

        $fdg_date      = date("Y:m:d-H:i:s");
        $fdg_hash_str  = $fdg_storename.$fdg_date.$fdg_total_cost.$fdg_currency.$fdg_secret_key;
        $fdg_check_str = $fdg_secret_key.FDG_CONNECT_APPROVAL_TOKEN.$fdg_total_cost.$fdg_currency.$fdg_date.$fdg_storename;
        $fdg_3dsecure  = $module_params['param08'];

        $post += array(
            'currency'    => $fdg_currency,
            'hash'        => func_cc_fdg_encrypt($fdg_hash_str),
            'timezone'    => $fdg_timezome,
            'txndatetime' => $fdg_date,
            'tdate'       => $fdg_tdate,
            'authenticateTransaction' => $fdg_3dsecure == 'Y' ? 'true' : 'false',
        );

    } else {
        $fdg_check_str = '';
    }

    foreach (array('b', 's') as $state_type) {
        if ($userinfo[$state_type.'_country'] == 'US') {
            $post[$state_type.'state'] = $userinfo[$state_type.'_state'];
        } else {
            $fdg_state = func_get_state($userinfo[$state_type.'_state'], $userinfo[$state_type.'_country']);
            $post[$state_type.'state2'] = $fdg_state ? fdg_trim($fdg_state, 30) : $userinfo[$state_type.'_state'];
        }
    }

    $post['responseURL'] =
    $post['responseFailURL'] =
    $post['responseSuccessURL'] = $fdg_response_url;

    if (!$duplicate) {
        db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref, sessionid, param1) VALUES ('".addslashes($fdg_oid)."', '".$XCARTSESSID."', '".addslashes($fdg_check_str)."')");
    }

    $fdg_url = func_cc_fdg_get_gateway_url();

    if ($need_payment_form) {
        $cc_fields = func_cc_fdg_connect_get_cc_fields();
    }

    func_create_payment_form($fdg_url, $post, 'First Data Global', 'POST', $need_payment_form, $cc_fields);
    exit();

}

?>
