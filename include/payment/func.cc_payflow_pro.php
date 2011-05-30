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
 * Functions for "PayFlow - Pro" payment module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.cc_payflow_pro.php,v 1.12.2.1 2011/01/10 13:11:53 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load('http','xml');

/**
 * Return several common PayFlow Pro parameters
 */
function func_get_payflow_pro_params()
{
    global $vs_user, $vs_vendor, $vs_partner, $vs_pwd, $vs_host, $module_params;

    $module_params = func_get_pm_params('cc_payflow_pro.php');

    $vs_user = func_pfpro_xml_encode($module_params['param01']);
    $vs_vendor = func_pfpro_xml_encode($module_params['param02']);
    $vs_partner = func_pfpro_xml_encode($module_params['param03']);
    $vs_pwd = func_pfpro_xml_encode($module_params['param04']);
    $vs_host = $module_params['testmode'] != 'N' ? "pilot-payflowpro.paypal.com" : 'payflowpro.paypal.com';
}

/**
 * Make request to PayFlow Pro gateway
 */
function func_payflow_pro_request($post)
{
    global $module_params, $secure_oid, $config, $vs_host;

    $headers = array(
        'X-VPS-REQUEST-ID' => $module_params["param05"].join("-", $secure_oid).XC_TIME,
        'X-VPS-VIT-CLIENT-CERTIFICATION-ID' => '7894b92104f04ffb4f38a8236ca48db3',
        'X-VPS-VIT-INTEGRATION-PRODUCT' => 'X-Cart',
        'X-VPS-VIT-INTEGRATION-VERSION' => $config['version'],
        'X-VPS-CLIENT-TIMEOUT' => 45
    );

    if (defined('PAYPAL_DEBUG')) {
        $log_str = "*** Request:\n\n$post\n\n";
        ob_start();
        print_r($headers);
        $log_str .= ob_get_contents()."\n\n";
        ob_end_clean();
    }

    $url = "https://".$vs_host.":443/transaction";
    list($a, $return) = func_https_request('POST', $url, array($post), '', '', 'text/xml', '', '', '', $headers);

    if (defined('PAYPAL_DEBUG')) {
        $log_str .= "*** Response: \n\n$return\n\n";
        ob_start();
        print_r($a);
        $log_str .= ob_get_contents()."\n\n";
        ob_end_clean();
        x_log_add('paypal', $log_str);
        unset($log_str);
    }

    return $return;
}

/**
 * Analyze PayFlow Pro request
 */
function func_payflow_pro_analyze_request($xml)
{
    global $result, $message, $avsresult, $avsresults, $avsresultz, $cvsresult, $pnref, $authcode;

    $result = func_array_path($xml, "XMLPayResponse/ResponseData/TransactionResults/TransactionResult/Result/0/#");
    $message = func_array_path($xml, "XMLPayResponse/ResponseData/TransactionResults/TransactionResult/Message/0/#");
    $avsresult = func_array_path($xml, "XMLPayResponse/ResponseData/TransactionResults/TransactionResult/IAVSResult/0/#");
    $avsresults = func_array_path($xml, "XMLPayResponse/ResponseData/TransactionResults/TransactionResult/AVSResult/StreetMatch/0/#");
    $avsresultz = func_array_path($xml, "XMLPayResponse/ResponseData/TransactionResults/TransactionResult/AVSResult/ZipMatch/0/#");
    $cvsresult = func_array_path($xml, "XMLPayResponse/ResponseData/TransactionResults/TransactionResult/CVResult/0/#");
    $pnref = func_array_path($xml, "XMLPayResponse/ResponseData/TransactionResults/TransactionResult/PNRef/0/#");
    $authcode = func_array_path($xml, "XMLPayResponse/ResponseData/TransactionResults/TransactionResult/AuthCode/0/#");
}

/**
 * Do PayFlow Pro transactions
 */
function func_cc_payflow_pro_do($order, $transaction_type)
{
    global $xcart_dir, $secure_oid, $result, $message, $vs_user, $vs_vendor, $vs_partner, $vs_pwd, $vs_host;

    if (!in_array($transaction_type, array('Capture', 'Void'))) {
        return false;
    }

    $secure_oid = array($order['orderid']);
    $pnref = $order['order']['extra']['payflow_pro_pnref'];
    $amount = $order['order']['total'];

    // get PayFlow Pro parameters to use
    func_get_payflow_pro_params();

    $invoice = $transaction_type == 'Capture' ? "<Invoice><TotalAmt>$amount</TotalAmt></Invoice>" : "";

    $post = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<XMLPayRequest Timeout='45' version="2.0">
<RequestData>
    <Vendor>$vs_vendor</Vendor>
    <Partner>$vs_partner</Partner>
    <Transactions>
        <Transaction>
            <$transaction_type>
                <PNRef>$pnref</PNRef>
                $invoice
            </$transaction_type>
        </Transaction>
    </Transactions>
</RequestData>
<RequestAuth>
    <UserPass>
        <User>$vs_user</User>
        <Password>$vs_pwd</Password>
    </UserPass>
</RequestAuth>
</XMLPayRequest>
XML;

    $return = func_payflow_pro_request($post);

    $xml = func_xml_parse($return, $err);

    func_payflow_pro_analyze_request($xml);

    $status = ($result == 0);
    $err_msg = ($status) ? '' : $message;

    $extra = array(
        'name' => 'payflow_pro_pnref',
        'value' => $pnref
    );

    return array($status, $err_msg, $extra);
}

/**
 * Do PayFlow Pro Capture transaction
 */
function func_cc_payflow_pro_do_capture($order)
{
    return func_cc_payflow_pro_do($order, 'Capture');
}

/**
 * Do PayFlow Pro Void transaction
 */
function func_cc_payflow_pro_do_void($order)
{
    return func_cc_payflow_pro_do($order, 'Void');
}

/**
 * PayFlow Pro encode
 */
function func_pfpro_xml_encode($str)
{
    global $sql_tbl, $shop_language, $all_languages;

    static $charset = null;
    if (is_null($charset)) {
        $charset = $all_languages[$shop_language]['charset'];
    }

    $str = func_xml_escape($str);

    if (preg_match("/^utf-8$/i", $charset)) {
        $str = @utf8_decode($str);
        return @utf8_encode($str);
    }

    if (preg_match("/^iso[-]?8859-1$/i", $charset)) {
        return @utf8_encode($str);
    }

    for ($i = 0; $i < strlen($str); $i++) {
        if (ord($str{$i}) > 127) {
            $str{$i} = "?";
        }
    }

    return $str;
}

?>
