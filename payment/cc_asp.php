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
 * "Amazon Simple Pay" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_asp.php,v 1.15.2.1 2011/01/10 13:12:05 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!isset($REQUEST_METHOD))
    $REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];

if (
    $REQUEST_METHOD == 'GET' 
    && isset($_GET['referenceId']) 
    && isset($_GET['result'])
) {
    require './auth.php';

    if (!func_is_active_payment('cc_asp.php'))
        exit;

    $module_params = func_query_first("SELECT param02, use_preauth FROM $sql_tbl[ccprocessors] WHERE processor='cc_asp.php'");

    $pp_secretKey = $module_params['param02'];
    $use_preauth  = $module_params['use_preauth'];

    $return_variables = array(
        'transactionId',
        'referenceId',
        'status',
        'errorMessage',
        'operation',
        'paymentReason',
        'transactionAmount',
        'transactionDate',
        'paymentMethod',
        'recipientName',
        'buyerName',
        'recipientEmail',
        'buyerEmail',
        'addressName',
        'addressLine1',
        'addressLine2',
        'city',
        'state',
        'country',
        'zip',
        'phoneNumber',
        'result',
    );

    $response = array();

    foreach ($return_variables as $key)
        if (isset($_GET[$key]))
            $response[$key] = $_GET[$key];

    $pp_warning = '';

    if (isset($_GET['signature'])) {

        $test_signature = func_get_asp_signature($response, $pp_secretKey);

        if ($test_signature != $_GET['signature']) {
            $pp_warning = "\n Wrong signature!";
        }
    }

    $pp3_data = func_query_first("SELECT * FROM $sql_tbl[cc_pp3_data] WHERE ref='".$response['referenceId']."'");

    $bill_output['sessid'] = $pp3_data['sessionid'];

    $bill_output['code'] = 2;

    if (
        $response['status'] == 'PS' 
        || $response['status'] == 'PR'
    ) {
        $bill_output['code'] = 1;

        $secure_oid = explode('|', $pp3_data['trstat']);

        if (
            $module_params['use_preauth'] == "Y" 
            || func_is_preauth_force_enabled($secure_oid)
        ) {

            $bill_output['is_preauth'] = true;

            $extra_order_data = array(
                'asp_txnid'      => $response['transactionId'],
                'capture_status' => 'A',
            );

        }

    }

    if (
        $response['status'] == 'PI' 
        || $pp_warning != ''
    ) {
        $bill_output['code'] = 3;
    }

    if ($bill_output['code'] != 1) {
        $bill_output['billmes'] = $response['errorMessage'] . $pp_warning;
    }

    require($xcart_dir . '/payment/payment_ccend.php');

} else {

    if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

    $pp_test = ($module_params['testmode']=='Y') 
        ? 'https://authorize.payments-sandbox.amazon.com/pba/paypipeline'
        : 'https://authorize.payments.amazon.com/pba/paypipeline';

    $pp_accessKey = $module_params['param01'];
    $pp_secretKey = $module_params['param02'];
    $pp_paymentsAccountId = $module_params['param03'];
    $pp_collectShippingAddress = ($module_params['param04'] == 'Y') ? 'TRUE' : '';

    $ordr = $module_params['param05'] . join('-', $secure_oid);

    if(!$duplicate)
        db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."','".implode('|',$secure_oid)."')");

    $return_url = $http_location . '/payment/cc_asp.php';

    // Amazon Simple Pay is working with US only
    $country_name = array (
        'US' => 'United States',
    );

    $post = array (
        'amount'                    => 'USD ' . $cart['total_cost'],
        'description'               => $ordr,
        'referenceId'               => $ordr,
        'immediateReturn'           => 'TRUE',
        'returnUrl'                 => $return_url . '?result=success',
        'abandonUrl'                => $return_url . '?result=cancel',
        'accessKey'                 => $pp_accessKey,
        'amazonPaymentsAccountId'   => $pp_paymentsAccountId,
        'processImmediate'          => ($module_params['use_preauth'] != 'Y' && !func_is_preauth_force_enabled($secure_oid)) ? 'TRUE' : '',
        'collectShippingAddress'    => $pp_collectShippingAddress,
        'addressName'               => $userinfo['b_firstname'] . ' ' . $userinfo['b_lastname'],
        'addressLine1'              => $userinfo['b_address'],
        'addressLine2'              => $userinfo['b_address_2'],
        'city'                      => $userinfo['b_city'],
        'state'                     => $userinfo['b_state'],
        'zip'                       => $userinfo['b_zipcode'],
        'country'                   => $country_name[$userinfo['b_country']],
        'phoneNumber'               => $userinfo['phone'],
    );

    $post['signature'] = func_get_asp_signature($post, $pp_secretKey);

    func_create_payment_form($pp_test, $post, 'Amazon Simple Pay');
}

exit;
?>
