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
 * Functions for "Amazon Simple Pay" payment module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.cc_asp.php,v 1.15.2.1 2011/01/10 13:11:52 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load('http', 'xml');

function func_get_asp_signature($post, $key)
{
    ksort($post);
    $signed_key  = '';
    foreach ($post as $k => $value) {
        if ($value != '')
            $signed_key .= $k.$value;
    }
    return base64_encode(hmac_sha1($signed_key, $key));
}

function func_cc_asp_do($order, $trantype)
{
    global $sql_tbl, $http_location;

    $module_params = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor = 'cc_asp.php'");
    $asp_txnid = $order['order']['extra']['asp_txnid'];

    $extra = array(
        'name'    => 'asp_txnid',
        'value'    => $asp_txnid,
    );

    if (empty($asp_txnid)) {
        return array('Q', func_get_langvar_by_name("txt_delayed_payment_transaction_failed"), $extra);
    }

    $AWS_VERSION = "2008-09-17";

    $get = array(
        'AWSAccessKeyId' => $module_params['param01'],
        'TransactionId' => $asp_txnid,
        'SignatureVersion' => '1',
        'Timestamp' => gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", XC_TIME),
        'Version' => $AWS_VERSION,
        'Action' => ($trantype == 'S') ? 'Settle' : 'Cancel'
    );

    $get['Signature'] = urlencode(func_get_asp_signature($get, $module_params['param02']));

    $url = ($module_params['testmode'] == 'Y') ? "https://fps.sandbox.amazonaws.com/" : "https://fps.amazonaws.com/";

    $query = array();
    foreach ($get as $var => $val)
        $query[$var] = $var . "=" . $val;

    $url = $url . "?" . implode("&", $query);

    list($a, $return) = func_https_request('GET', $url, '', '', '', 'text/xml', $http_location.'/payment/payment_cc.php');

    $xml = func_xml2hash($return);

    $status = empty($xml['Response']['Errors']['Error']);
    $err_msg = $status ? '' : $xml['Response']['Errors']['Error']['Code'] . ":" . $xml['Response']['Errors']['Error']['Message'];

    return array($status, $err_msg, $extra);
}

/**
 * Perform Amazon Simple Pay Settle transaction
 */
function func_cc_asp_do_capture($order)
{
    return func_cc_asp_do($order, 'S');
}

/**
 * Perform Amazon Simple Pay Cancel trnsaction
 */
function func_cc_asp_do_void($order)
{
    return func_cc_asp_do($order, 'C');
}

?>
