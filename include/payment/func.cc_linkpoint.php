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
 * Functions for "First Data Global Gateway - LinkPoint" payment module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.cc_linkpoint.php,v 1.15.2.1 2011/01/10 13:11:52 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

/**
 * Do LinkPoint transactions
 */
function func_cc_linkpoint_do($order, $transaction_type)
{
    global $xcart_dir, $sql_tbl;

    $module_params = func_query_first("select * from $sql_tbl[ccprocessors] where processor='cc_linkpoint.php'");
    $transaction_type_name = ($transaction_type == 'X') ? 'POSTAUTH' : 'VOID';
    $tid = $order['order']['extra']['linkpoint_tid'];
    $total_cost = $order['order']['total'];
    $card_info = text_decrypt($order['order']['extra']['ccdata']);
    list($card_number, $card_expire) = explode(":", $card_info);

    x_load('http');

    $pp_login = $module_params['param01'];
    $sert = $module_params['param02'];
    $host =  $module_params['param06'];
    $port = $module_params['param07'];
    $lp_test = 'LIVE';
    switch($module_params['testmode']) {
        case 'A': $lp_test = 'GOOD'; break;
        case 'D': $lp_test = 'DECLINE'; break;
    }

    $post = array();
    $post[] = "<order>";

    $post[] = "<orderoptions>";
    $post[] = "<ordertype>$transaction_type_name</ordertype>";
    $post[] = "<result>".$lp_test."</result>";
    $post[] = "</orderoptions>";

    $post[] = "<creditcard>";
    $post[] = "<cardnumber>".$card_number."</cardnumber>";
    $post[] = "<cardexpmonth>".substr($card_expire,0,2)."</cardexpmonth>";
    $post[] = "<cardexpyear>".substr($card_expire,2,2)."</cardexpyear>";
    $post[] = "</creditcard>";

    $post[] = "<merchantinfo>";
    $post[] = "<configfile>".$pp_login."</configfile>";
    $post[] = "<keyfile>".$sert."</keyfile>";
    $post[] = "<host>".$host."</host><port>".$port."</port>";
    $post[] = "</merchantinfo>";

    $post[] = "<payment>";
    $post[] = "<chargetotal>".$total_cost."</chargetotal>";
    $post[] = "</payment>";

    $post[] = "<transactiondetails>";
    $post[] = "<oid>".$tid."</oid>";
    $post[] = "</transactiondetails>";

    $post[] = "</order>";

    list($a,$return)=func_https_request('POST',"https://$host:$port/LSGSXML",$post,'','',"application/x-www-form-urlencoded",'',$sert,$sert);

    $bill_output['code'] = 2;
    preg_match("/<r_approved>(.*)<\/r_approved>/",$return, $status);

    $bill_output['billmes'] = '';

    if ($status[1] == 'APPROVED') {
        $bill_output['code'] = 1;
    }

    if ($bill_output['code'] == 2) {
        preg_match("/<r_error>(.*)<\/r_error>/",$return,$out);
        $bill_output['billmes'] = "[".$status[1]."] ".$out[1].$bill_output['billmes'];
    }

    if (preg_match("/<r_authresponse>(.+)<\/r_authresponse>/",$return,$out))
        $bill_output['billmes'] .= " (AuthResponse: ".$out[1].")";

    if (preg_match("/<r_message>(.+)<\/r_message>/",$return,$out))
        $bill_output['billmes'] .= " (Message: ".$out[1].")";

    $status = $bill_output['code'] == 1;
    $err_msg = (!$status) ? $bill_output['billmes'] : '';
    $extra = array(
        'name' => 'linkpoint_tid',
        'value' => $tid
    );

    return array($status, $err_msg, $extra);
}

/**
 * Do LinkPoint Capture transaction
 */
function func_cc_linkpoint_do_capture($order)
{
    return func_cc_linkpoint_do($order, 'X');
}

/**
 * Do LinkPoint Void transaction
 */
function func_cc_linkpoint_do_void($order)
{
    return func_cc_linkpoint_do($order, 'V');
}

?>
