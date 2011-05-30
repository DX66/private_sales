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
 * Functions for "NetRegistry e-commerce" payment module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.cc_nrecom.php,v 1.15.2.1 2011/01/10 13:11:53 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

/**
 * Do NetRegistry transaction
 */
function func_cc_nrecom_do($order, $command)
{
    global $sql_tbl;

    x_load('crypt');

    $module_params = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE paymentid='" . $order['order']['paymentid'] . "'");
    $ccdata = explode("\n", text_decrypt($order['order']['extra']['ccdata']));

    $extra = array(
        'name' => 'txnid',
        'value' => $order['order']['extra']['txnid']
    );

    if (empty($ccdata) || empty($order['order']['extra']['txnid'])) {
        return array('Q', func_get_langvar_by_name("txt_delayed_payment_transaction_failed"), $extra);
    }

    $ccdata = explode("\n", $ccdata);

    $post = array(
        "LOGIN=" . $module_params['param01'] . '/' . $module_params['param02'],
        "COMMAND=" . $command,
        "PREAUTHNUM=" . $order['order']['extra']['txnid'],
        "AMOUNT=" . $order['order']["total"],
        "COMMENT=Capture transaction",
        "CCNUM=" . $ccdata[0],
        "CCEXP=" . $ccdata[1]
    );

    list($a, $return) = func_https_request('POST', "https://4tknox.au.com:443/cgi-bin/themerchant.au.com/ecom/external2.pl", $post);

    $return = "&" . strtr($return, "\n", "&") . "&";

    $msg = '';
    if (preg_match("/&status=(.*)&/U", $return, $out))
        $msg = $out[1];

    return array(
        (preg_match("/&status=approved&/i", $return) && preg_match("/&result=1&/i", $return)),
        $msg,
        $extra
    );
}

/**
 * Do NetRegistry Void trnsaction
 */
function func_cc_nrecom_do_capture($order)
{
    return func_cc_nrecom_do($order, 'completion');
}

/**
 * Do NetRegistry Void trnsaction
 */
function func_cc_nrecom_do_void($order)
{
    return func_cc_nrecom_do($order, 'refund');
}

?>
