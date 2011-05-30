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
 * Functions for "GoEmerchant - XML Gateway API" payment module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.cc_goem_xml.php,v 1.15.2.1 2011/01/10 13:11:52 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load('http');

/**
 * Perform GoEmerchant XML transaction
 */
function func_cc_goem_xml_do($order, $trantype)
{
    global $sql_tbl, $http_location;

    $module_params = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor = 'cc_goem_xml.php'");
    $goem_xml_txnid = $order['order']['extra']['goem_xml_txnid'];

    $extra = array(
        'name'    => 'goem_xml_txnid',
        'value'    => $goem_xml_txnid,
    );

    if (empty($goem_xml_txnid)) {
        return array('Q', func_get_langvar_by_name("txt_delayed_payment_transaction_failed"), $extra);
    }

    if ($trantype == 'X') {
        $total_cost_line = "<FIELD KEY=\"settle_amount1\">".$order['order']['total']."</FIELD>";
        $trantype = 'settle';
    } else {
        $trantype = 'void';
    }

    $post = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<TRANSACTION>
    <FIELDS>
        <FIELD KEY="merchant">$module_params[param01]</FIELD>
        <FIELD KEY="password">$module_params[param02]</FIELD>
        <FIELD KEY="gateway_id">$module_params[param04]</FIELD>
        <FIELD KEY="operation_type">$trantype</FIELD>
        <FIELD KEY="total_number_transactions">1</FIELD>
        <FIELD KEY="reference_number1">$goem_xml_txnid</FIELD>
        $total_cost_line
    </FIELDS>
</TRANSACTION>
XML;

    list($a, $return) = func_https_request('POST', "https://secure.goemerchant.com:443/secure/gateway/xmlgateway.aspx", array($post), '', '', 'text/xml', $http_location.'/payment/payment_cc.php');

    preg_match("/<FIELD KEY=[^\w]*status1[^\w]*>(.+)<\/FIELD>/i", $return, $sts);
    preg_match("/<FIELD KEY=[^\w]*response1[^\w]*>(.+)<\/FIELD>/i", $return, $out);

    $status = ($sts[1] == 1);
    $err_msg = $status ? '' : ((($sts[1] == 2) ? 'Declined' : 'Error').": ".$out[1]);

    return array($status, $err_msg, $extra);
}

/**
 * Perform GoEmerchant XML Capture transaction
 */
function func_cc_goem_xml_do_capture($order)
{
    return func_cc_goem_xml_do($order, 'X');
}

/**
 * Perform GoEmerchant XML Void trnsaction
 */
function func_cc_goem_xml_do_void($order)
{
    return func_cc_goem_xml_do($order, 'V');
}

?>
