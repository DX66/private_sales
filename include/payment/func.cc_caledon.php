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
 * Functions for "Caledon" payment module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.cc_caledon.php,v 1.15.2.1 2011/01/10 13:11:52 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

/**
 * Do Caledon transactions
 */
function func_cc_caledon_do($order, $transaction_type)
{
    global $sql_tbl;

    if (!in_array($transaction_type, array('C', 'V')))
        return array();

    $module_params = func_query_first("select param01, param02 from $sql_tbl[ccprocessors] where processor='cc_caledon.php'");
    $pp_term = $module_params['param01'];
    $pp_oper = $module_params['param02'];

    $post = array();
    $post[] = "TERMID=".urlencode(trim($pp_term));
    $post[] = "TYPE=$transaction_type";
    $post[] = "OPERID=".urlencode(trim($pp_oper));
    $post[] = "CARD=0";
    $post[] = "EXP=0000";
    $post[] = "AMT=".urlencode(trim(100*$order['order']['total']));
    $post[] = "AVS=".urlencode(trim(preg_replace("/[^\w]/",'',strtoupper($order['userinfo']["b_address"].$order['userinfo']["b_zipcode"]))));
    $post[] = "REF=".$order['order']['extra']['caledon_tid'];

    list($a, $return) = func_https_request('POST',"https://lt3a.caledoncard.com:443/".join("&",$post));
    parse_str($return, $ret);

    $status = $ret['CODE'] == '0000';

    $err_msg = (!$status) ? $ret['TEXT'] : '';

    $extra = array(
        'name' => 'caledon_tid',
        'value' => $order['order']['extra']['caledon_tid']
    );

    return array($status, $err_msg, $extra);
}

/**
 * Do Caledon Capture transaction
 */
function func_cc_caledon_do_capture($order)
{
    return func_cc_caledon_do($order, 'C');
}

/**
 * Do LinkPoint Void transaction
 */
function func_cc_caledon_do_void($order)
{
    return func_cc_caledon_do($order, 'V');
}

?>
