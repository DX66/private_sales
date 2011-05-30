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
 * USA ePay Server Method
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.cc_usaepay.php,v 1.19.2.2 2011/01/10 13:11:53 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

/**
 * Common transaction
 */
function func_cc_usaepay_do($command, $data)
{
    global $sql_tbl;
    static $software_version = false;

    if (!is_array($data) || !is_string($command))
        return array(false, false, false);

    x_load('http');

    $module_params = func_get_pm_params('cc_usaepay.php');
    if (!$software_version)
        $software_version = func_query_first_cell("SELECT value FROM $sql_tbl[config] WHERE name = 'version'");

    $post = array(
        'UMkey' => $module_params['param01'],
        'UMcommand' => $command,
        'UMsoftware' => 'X-Cart ' . $software_version
    );

    if ($module_params['testmode'] == 'Y' || $module_params['testmode'] == 'S')
        $post['UMtestmode'] = 1;

    $post = array_merge($post, $data);

    if ($module_params['testmode'] == 'S' || $module_params['testmode'] == 'L')
        $url = 'https://sandbox.usaepay.com:443/gate.php';
    else
        $url = 'https://www.usaepay.com:443/gate.php';

    if (!empty($module_params['param02']) && in_array($command, array('cc:sale', 'sale', 'cc:authonly', 'authonly', 'preauth', 'cc:capture', 'capture', 'void', 'cc:void'))) {
        $seed = XC_TIME;
        $sig = $command . ':' . $module_params['param02'] . ':' . $post['UMamount'] . ':' . $post['UMinvoice'] . ':' . $seed;
        $post['UMhash'] = 'm/' . $seed . '/' . md5($sig) . '/';
    }

    $arr = array();
    foreach ($post as $k => $v)
        $arr[] = $k . '=' . $v;

    list($a, $return) = func_https_request('POST', $url, $arr);

    $res = array();
    @parse_str($return, $res);

    if (!is_array($res) || count($res) == 0) {
        func_pp_error_log($return);

    } else {
        foreach ($res as $k => $v) {
            $res[$k] = urldecode($v);
        }
    }

    return array($a, $res, $return);
}

/**
 * Do cc:sale (Sale) / cc:authonly (Pre-Authorization) transaction
 */
function func_cc_usaepay_do_sale($bill_output, $authonly, $secure_oid, $userinfo, $products, $giftcerts, $total_cost, $cmpi_result = null)
{
    $module_params = func_get_pm_params('cc_usaepay.php');

    $post = array(
        'UMip' => func_get_valid_ip($_SERVER['REMOTE_ADDR']),
        'UMorderid' => $module_params["param03"] . join("-", $secure_oid),
        'UMinvoice' => $module_params["param03"] . join("-", $secure_oid),
        'UMname' => $userinfo["card_name"],
        'UMstreet' => $userinfo["b_address"],
        'UMzip' => $userinfo["b_zipcode"],
        'UMbillname' => $bill_name,
        'UMbillcompany' => $userinfo["company"],
        'UMbillstreet' => $userinfo["b_address"],
        'UMbillcity' => $userinfo["b_city"],
        'UMbillstate' => $userinfo["b_state"],
        'UMbillzip' => $userinfo["b_zipcode"],
        'UMbillcountry' => $userinfo["b_country"],
        'UMbillphone' => $userinfo["b_phone"],
        'UMemail' => $userinfo["email"],
        'UMfax' => $userinfo["fax"],
        'UMwebsite' => $userinfo["url"],
        'UMamount' => $total_cost,
        'UMcard' => $userinfo["card_number"],
        'UMexpir' => $userinfo["card_expire"],
        'UMcvv2' => $userinfo["card_cvv2"],
        'UMshipstreet' => $userinfo["s_address"],
        'UMshipcity' => $userinfo["s_city"],
        'UMshipcountry' => $userinfo["b_country"],
        'UMshipfname' => $userinfo["s_firstname"],
        'UMshiplname' => $userinfo["s_lastname"],
        'UMshipphone' => $userinfo["s_phone"],
        'UMshipzip' => $userinfo["s_zipcode"],
        'UMshipstate' => $userinfo["s_state"],
        'UMcurrency' => $module_params['param05']
    );

    if (isset($cmpi_result) && !empty($cmpi_result) && is_array($cmpi_result)) {
            $post['UMcardauth'] = 'true';
            $post['UMxid'] = $cmpi_result['Xid'];
            $post['UMcavv'] = $cmpi_result['Cavv'];
            $post['UMeci'] = intval($cmpi_result['EciFlag']);
    }

    $i = 1;
    if (is_array($products)) {
        foreach ($products as $p) {
            $post['UMline' . $i . 'sku'] = substr($p['productcode'], 0, 32);
            $post['UMline' . $i . 'name'] = substr($p['product'], 0, 255);
            $post['UMline' . $i . 'description'] = substr($p['descr'], 0, 64000);
            $post['UMline' . $i . 'cost'] = $p['price'];
            $post['UMline' . $i . 'qty'] = $p['amount'];
            $i++;
        }
    }

    if (is_array($giftcerts)) {
        foreach ($giftcerts as $p) {
            $post['UMline' . $i . 'sku'] = substr($p['gcid'], 0, 32);
            $post['UMline' . $i . 'name'] = 'Gift certificate';
            $post['UMline' . $i . 'cost'] = $p['amount'];
            $post['UMline' . $i . 'qty'] = 1;
            $post['UMline' . $i . 'taxable'] = 'N';
            $i++;
        }
    }

    list($http_headers, $result, $raw_result) = func_cc_usaepay_do($authonly ? 'cc:authonly' : 'cc:sale', $post);

    $extra_order_data = array();

    if (!is_array($result) || count($result) == 0) {
        $bill_output['code'] = 2;
        $bill_output['billmes'] = 'Internal error';

    } elseif (isset($result['UMresult']) && $result['UMresult'] == 'A') {
        $bill_output['code'] = 1;
        if (isset($result['UMrefNum'])) {
            $bill_output['billmes'] = '(Reference number: ' . $result['UMrefNum'] . ')';

            $extra_order_data = array(
                'txnid' => $result['UMrefNum'],
                'capture_status' => $authonly ? 'A' : ''
            );

            if ($authonly)
                $bill_output['is_preauth'] = true;

        }

    } else {
        $bill_output['code'] = 2;
        if (isset($result['UMerror']))
            $bill_output['billmes'] = $result['UMerror'];
    }

    if (isset($result['UMavsResult']))
        $bill_output['avsmes'] = $result['UMavsResult'];

    if (isset($result['UMcvv2Result']))
        $bill_output['cvvsmes'] = $result['UMcvv2Result'];

    return array($bill_output, $extra_order_data);
}

/**
 * Do cc:capture (Capture) / cc:void (Void) transactions
 */
function func_cc_usaepay_do_cv($order, $type)
{

    $module_params = func_get_pm_params('cc_usaepay.php');

    $post = array(
        'UMrefNum' => $order["order"]["extra"]["txnid"],
        'UMamount' => $order["order"]["total"]
    );

    list($http_headers, $result, $raw_result) = func_cc_usaepay_do($type, $post);

    if (!is_array($result)) {
        $status = false;
        $err_msg = 'Internal error';

    } elseif (isset($result['UMresult']) && $result['UMresult'] == 'A') {
        $status = true;

    } elseif ($type == 'capture' && intval($result['UMerrorcode']) == 10126) {
        $status = X_PAYMENT_TRANS_ALREADY_CAPTURED;

    } elseif ($type == 'void' && intval($result['UMerrorcode']) == 24) {
        $status = X_PAYMENT_TRANS_ALREADY_VOIDED;

    } else {
        $status = false;
        if (isset($result['UMerror']))
            $err_msg = $result['UMerror'];
    }

    $extra = array(
        'name' => 'txnid',
        'value' => $order['order']['extra']['txnid']
    );

    return array($status, $err_msg, $extra);
}

/**
 * Do cc:capture (Capture) transaction
 */
function func_cc_usaepay_do_capture($order)
{
    return func_cc_usaepay_do_cv($order, 'capture');
}

/**
 * Do void (Void) transaction
 */
function func_cc_usaepay_do_void($order)
{
    return func_cc_usaepay_do_cv($order, 'void');
}

?>
