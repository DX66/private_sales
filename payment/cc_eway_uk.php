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
 * "eWay UK (Hosted payment page)" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_eway_uk.php,v 1.12.2.2 2011/01/10 13:12:06 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/**
 * eWay UK - Hosted payment page
 */

// Uncomment the below line to enable the debug log
// define('EWAY_DEBUG', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['AccessPaymentCode'])) {

    require './auth.php';

    $success_codes = array('00', '08', '10', '11', '16');

    $module_params = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor='cc_eway_uk.php'");

    $post = array(
        'CustomerID'        => $module_params['param01'],
        'UserName'             => $module_params['param02'],
        'AccessPaymentCode' => $_POST['AccessPaymentCode']
    );

    $url = 'https://www.ewaygateway.com:443/Gateway/UK/Results.aspx?' . func_http_build_query($post);

    // Send request to receive payment details
    x_load('http', 'xml');
    list($a, $return) = func_https_request('POST', $url);

    // Parse response
    $tmp = func_xml2hash($return);

    if (!is_array($tmp) || empty($tmp)) {
        exit();
    }

    $response = $tmp['TransactionResponse'];

    if (defined('EWAY_DEBUG')) {
        func_pp_debug_log('eway', 'R', $response);
    }

    $bill_output['code'] = in_array($response['ResponseCode'], $success_codes) ? 1 : 2;
    $bill_output['sessid'] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='" . $response['MerchantReference'] . "'");

    $bill_output['billmes'] = $response['TrxnResponseMessage'];

    if (!empty($response['ErrorMessage']))
        $bill_output['billmes'] .= "\nErrorMessage: " . $response['ErrorMessage'];

    if (!empty($response['AuthCode']))
        $bill_output['billmes'] .= "\nAuthCode: " . $response['AuthCode'];

    if (!empty($response['TrxnNumber']))
        $bill_output['billmes'] .= "\nTrxnNumber: " . $response['TrxnNumber'];

    if (isset($response['ReturnAmount'])) {
        $payment_return = array(
            'total' => $response['ReturnAmount']
        );
    }

    require $xcart_dir . '/payment/payment_ccend.php';

} else {

    if (!defined('XCART_START')) { header('Location: ../'); die('Access denied'); }

    $order = $module_params['param05'] . join('-', $secure_oid);

    if (!$duplicate)
        db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($order)."','".$XCARTSESSID."')");

    $post = array(
        'CustomerID'        => $module_params['param01'],
        'UserName'             => $module_params['param02'],
        'Amount'            => $cart['total_cost'],
        'Currency'            => $module_params ['param03'],
        'ReturnURL'            => $current_location . '/payment/cc_eway_uk.php',
        'CancelURL'            => $current_location . '/payment/cc_eway_uk.php',
        'PageTitle'         => $module_params['param06'],
        'PageDescription'   => $module_params['param07'],
        'PageFooter'        => $module_params['param08'],
        'Language'          => $module_params['param04'],
        'CompanyName'       => $config['Company']['company_name'],
        'CustomerFirstName' => $bill_firstname,
        'CustomerLastName'  => $bill_lastname,
        'CustomerAddress'   => $userinfo['b_address'],
        'CustomerCity'      => $userinfo['b_city'],
        'CustomerState'     => $userinfo['b_state'],
        'CustomerPostcode'  => $userinfo['b_zipcode'],
        'CustomerCountry'   => $userinfo['b_country'],
        'CustomerPhone'     => $userinfo['phone'],
        'CustomerEmail'     => $userinfo['email'],
        'MerchantReference' => $order,
        'MerchantInvoice'     => $order,
        'InvoiceDescription'=> 'Order #' . $order,
        'UseAVS'            => 'True',
        'UseZIP'            => 'True'
    );

    if (defined('EWAY_DEBUG')) {
        func_pp_debug_log('eway', 'I', $post);
    }

    $url = 'https://payment.ewaygateway.com:443/Request/?' . func_http_build_query($post);

    // Send request to receive payment URL
    x_load('http', 'xml');
    list($a, $return) = func_https_request('POST', $url);

    // Parse response
    $tmp = func_xml2hash($return);
    if (is_array($tmp) && !empty($tmp)) {
        $response = $tmp['TransactionRequest'];
        if ($response['Result'] != 'True' || !empty($response['Error']) || empty($response['URI'])) {
            $bill_output['code'] = 2;
            $bill_output['billmes'] = $response['Error'];
            require $xcart_dir . '/payment/payment_ccend.php';
        }
        else {
            func_header_location($response['URI']);
        }
    }
}

exit;

?>
