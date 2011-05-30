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
 * Functions for "HSBC - XML API integration" payment module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.cc_hsbc_xml.php,v 1.17.2.1 2011/01/10 13:11:52 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

/**
 * Perform HSBC (XML) transactions
 */
function func_cc_hsbc_xml_do($order, $trantype)
{
    global $xcart_dir, $sql_tbl;

    $module_params = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor = 'cc_hsbc_xml.php'");
    $hsbc_txnid = $order['order']['extra']['hsbc_txnid'];

    $extra = array(
        'name'    => 'hsbc_txnid',
        'value'    => $hsbc_txnid,
    );

    if (empty($hsbc_txnid)) {
        return array('Q', func_get_langvar_by_name('txt_delayed_payment_transaction_failed'), $extra);
    }

    $trantype = ($trantype == 'X') ? 'PostAuth' : 'Void';
    $total_cost = $order['order']['total'];

    include $xcart_dir.'/payment/cc_hsbc_xml.php';

    $status = ($bill_output['code'] == 1);
    $err_msg = $status ? '' : $bill_output['billmes'];

    return array($status, $err_msg, $extra);
}

/**
 * Perform HSBC (XML) Capture transaction
 */
function func_cc_hsbc_xml_do_capture($order)
{
    return func_cc_hsbc_xml_do($order, 'X');
}

// Perform HSBC (XML) Void transaction

function func_cc_hsbc_xml_do_void($order)
{
    return func_cc_hsbc_xml_do($order, 'V');
}

/**
 * Return true if the processor in the testmode
 */
function func_get_cc_hsbc_xml_in_testmode($module_params)
{
    if (empty($module_params) || $module_params['processor'] != 'cc_hsbc_xml.php')
        return true;

    return $module_params['testmode'] != 'P';
}

?>
