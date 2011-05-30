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
 * Functions for "SecurePay - Non-Recurring Interface" payment module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.cc_securepay.php,v 1.15.2.1 2011/01/10 13:11:53 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

/**
 * Perform Securepay transactions
 */
function func_cc_securepay_do($order, $transaction_type)
{
    global $xcart_dir, $sql_tbl;

    $module_params = func_query_first("select * from $sql_tbl[ccprocessors] where processor='cc_securepay.php'");

    if ($transaction_type == 'X') {
        $appnum = $order['order']['extra']['securepay_appnum'];
    } else {
        $vrnum = $order['order']['extra']['securepay_vrnum'];
    }

    // prepare the data for further processing

    $card_info = text_decrypt($order['order']['extra']['ccdata']);
    list($card_type, $card_name, $card_number, $card_expire) = explode(":", $card_info);

    $userinfo = array();
    $userinfo['card_name'] = $card_name;
    $userinfo['card_number'] = $card_number;
    $userinfo['card_expire'] = $card_expire;

    $userinfo['email'] = $order['order']['email'];
    $userinfo['b_address'] = $order['order']['b_address'];
    $userinfo['b_city'] = $order['order']['b_city'];
    $userinfo['b_state'] = $order['order']['b_state'];
    $userinfo['b_zipcode'] = $order['order']['b_zipcode'];

    require $xcart_dir.'/payment/cc_securepay.php';

    $status = $bill_output['code'] == 1;
    $err_msg = (!$status) ? $bill_output['billmes'] : '';

    if ($transaction_type == 'X') {
        $extra = array('name' => 'securepay_appnum', 'value' => $Approv_Num);
    } else {
        $extra = array('name' => 'securepay_appnum', 'value' => $VoidRecNum);
    }

    return array($status, $err_msg, $extra);
}

/**
 * Perform Securepay Capture transaction
 */
function func_cc_securepay_do_capture($order)
{
    return func_cc_securepay_do($order, 'X');
}

/**
 * Perform Securepay Void transaction
 */
function func_cc_securepay_do_void($order)
{
    return func_cc_securepay_do($order, 'V');
}

?>
