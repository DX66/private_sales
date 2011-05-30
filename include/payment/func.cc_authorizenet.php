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
 * Functions for "AuthorizeNet - AIM" payment module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.cc_authorizenet.php,v 1.16.2.3 2011/01/10 13:11:52 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

/**
 * Do Authorizenet:AIM transactions
 */
function func_cc_authorizenet_do($order, $transaction_type)
{
    global $xcart_dir;

    $module_params = func_get_pm_params('cc_authorizenet.php');
    $transaction_id = $order['order']['extra']['authorizenet_txnid'];
    $last_4_cc_num = $order['order']['extra']['last_4_cc_num'];
    $amount = $order['order']['total'];

    require $xcart_dir . '/payment/cc_authorizenet.php';

    $status = $bill_output['code'] == 1;

    $err_msg = (!$status) ? $bill_output['billmes'] : '';

    $extra = array(
        'name' => 'authorizenet_txnid',
        'value' => $transaction_id
    );

    return array($status, $err_msg, $extra);
}

/**
 * Do Authorizenet:AIM Capture transaction
 */
function func_cc_authorizenet_do_capture($order)
{
    return func_cc_authorizenet_do($order, 'X');
}

/**
 * Do Authorizenet:AIM Void transaction
 */
function func_cc_authorizenet_do_void($order)
{
    return func_cc_authorizenet_do($order, 'V');
}

/**
 * Do Authorizenet:AIM Refund transaction
 */
function func_cc_authorizenet_do_refund($order)
{
    return func_cc_authorizenet_do($order, 'R');
}

/**
 * Make instant void of transaction
 * 
 * @param mixed $transaction_id Transaction ID
 *  
 * @return boolean MUST be TRUE when success
 * @see    ____func_see____
 * @since  1.0.0
 */
function func_cc_authorize_instant_void($transaction_id)
{
    global $xcart_dir;

    $module_params = func_get_pm_params('cc_authorizenet.php');

    $transaction_type = 'V';

    require $xcart_dir . '/payment/cc_authorizenet.php';

    return $bill_output['code'] == 1;
}

/**
 * Make instant complete of the transaction query (partially paid)
 * 
 * @param mixed $x_split_tender_id query ID
 *  
 * @return boolean MUST be TRUE when success
 * @see    ____func_see____
 * @since  1.0.0
 */
function func_cc_authorize_instant_complete($x_split_tender_id)
{
    global $xcart_dir;

    $module_params = func_get_pm_params('cc_authorizenet.php');

    $transaction_type = 'X';

    require $xcart_dir . '/payment/cc_authorizenet.php';

    return $bill_output['code'] == 1;
}

/**
 * Get a field from response query, related to the current account type(market type)
 */
function func_AIM_field($field, $mass = false, $def_value = null)
{
    static $trans = array();
    
    if (!empty($mass)) {
        global $module_params;

        if (empty($module_params)) {
            $module_params = func_get_pm_params('cc_authorizenet.php');
        }    

        $type = $module_params['param08'];

        $response_fields = array(
            'cp_retail' => array(
                'MD5hash' => $mass[9],
                'ResponseCode' => $mass[2],
                'TransactionID' => $mass[8],
                'ResponseSubcode' => 0,
                'CardCodeResponse' => $mass[7],
                'CardholderAuthenticationVerification' => '',

            ),

            'cnp_ecommerce' => array(
                'MD5hash' => $mass[38],
                'ResponseCode' => $mass[1],
                'TransactionID' => $mass[7],
                'ResponseSubcode' => $mass[2],
                'CardCodeResponse' => $mass[39],
                'CardholderAuthenticationVerification' => $mass[40],
            ),
        );

        $common_fields = array(
            'ReasonText' => $mass[4],
            'ReasonCode' => $mass[3],
            'Amount' => $mass[10],
            'SplitTenderID' => $mass[43],
            'AuthorizationCode' => $mass[5],
            'AVSCode' => $mass[6],
        );

        $trans = $response_fields[$type];

        $trans = func_array_merge($trans, $common_fields);

        return true;
    }
    
    if (isset($def_value) && empty($trans[$field]))
        $trans[$field] = $def_value;

    return $trans[$field];
}

?>
