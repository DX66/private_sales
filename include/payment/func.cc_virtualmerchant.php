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
 * Functions for "Virtual Merchant - Merchant Provided Form" payment module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.cc_virtualmerchant.php,v 1.14.2.1 2011/01/10 13:11:53 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

/**
 * Do Virtual merchant Capture transaction
 */
function func_cc_virtualmerchant_do_capture($order)
{

    x_load('xml', 'http');

    $module_params = func_get_pm_params('cc_virtualmerchant.php');
    list($card_number, $card_expire) = explode("\n", text_decrypt($order['order']['extra']['ccdata']));

    $data = array(
        'ssl_transaction_type' => 'CCFORCE',
        'ssl_merchant_id' => substr($module_params['param01'], 0, 15),
        'ssl_user_id' => substr($module_params['param07'], 0, 15),
        'ssl_pin' => substr($module_params['param02'], 0, 6),
        'ssl_amount' => price_format($order['order']['total']),
        'ssl_txn_id' => $order['order']['extra']['txnid'],
        'ssl_approval_code' => $order['order']['extra']['approval_code'],
        'ssl_card_number' => substr($card_number, 0, 19),
        'ssl_exp_date' => substr($card_expire, 0, 4),
    );

    $xmldata = "<txn>\n";
    foreach ($data as $k => $v)
        $xmldata .= "\t<$k>$v</$k>\n";
    $xmldata .= "</txn>";

    if ($module_params['testmode'] == 'D')
        $url = "https://demo.myvirtualmerchant.com/VirtualMerchantDemo/processxml.do";
    else
        $url = "https://www.myvirtualmerchant.com/VirtualMerchant/processxml.do";

    list($a, $return) = func_https_request('POST', $url, array("xmldata=" . $xmldata));

    $xml = func_xml2hash($return);

    if (!is_array($xml) || !isset($xml['txn'])) {
        $status = 3;
        $err_msg = 'Internal error';
        if (!is_array($xml))
            $err_msg .= ': ' . $return;

        func_pp_error_log('Virtual merchant (Capture transaction) error: ' . $return);

    } else {

        $status = $xml['txn']['ssl_result'] == '0';
        if (!$status && isset($xml['txn']['errorCode']))
            $err_msg = "Error: #" . $xml['txn']['errorCode'] . " " . $xml['txn']['errorName'] . ": " . $xml['txn']['errorMessage'];
    }

    $extra = array(
        'name' => 'approval_code',
        'value' => $order['order']['extra']['approval_code']
    );

    return array($status, $err_msg, $extra);
}

?>
