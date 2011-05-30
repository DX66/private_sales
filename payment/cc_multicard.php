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
 * "MultiCards" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_multicard.php,v 1.35.2.1 2011/01/10 13:12:06 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!isset($REQUEST_METHOD))
        $REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];

if ($REQUEST_METHOD == 'POST' && isset($_POST['Proceed']) && isset($_POST['order_num']) && isset($_POST['user1'])) {
    require './auth.php';

    if (!func_is_active_payment('cc_multicard.php'))
        exit;

    $bill_output['sessid'] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$_POST["user1"]."'");

    $bill_output['code'] = 1;
    $bill_output['billmes'] = " OrderNumber: ".$_POST['order_num'];

    require($xcart_dir.'/payment/payment_ccend.php');

} else {
    if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

    $_orderids = $module_params ['param04'].join("-",$secure_oid);
    if (!$duplicate)
        db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($_orderids)."','".$XCARTSESSID."')");

    $post = array(
        'user1' => $_orderids,
        'mer_id' => $module_params['param01'],
        'mer_url_idx' => $module_params['param02'],
        'cust_name' => $ship_name,
        'cust_company' => $userinfo['company'],
        'info1' => $userinfo['s_address'].", ".$userinfo['s_city'].", ".$userinfo['s_zipcode'].($userinfo['s_statename'] ? ", ".$userinfo['s_statename'] : "").", ".$userinfo['s_countryname'],
        'cust_email' => $userinfo['email'],
        'cust_phone' => $userinfo['phone'],
        'cust_fax' => $userinfo['fax'],
        'cust_address1' => $userinfo['b_address'],
        'cust_zip' => empty($userinfo['b_zipcode']) ? '99999' : $userinfo['b_zipcode'],
        'cust_city' => $userinfo['b_city'],
        'cust_state' => $userinfo['b_state'],
        'cust_country' => $userinfo['b_countryname'],
        'pay_method_type' => $pay_method_type,
        'agree2terms' => '1',
        'num_items' => '1',
        'item1_desc' => "Order // ".$_orderids,
        'item1_price' => $cart['total_cost'],
        'item1_qty' => '1',
        'langcode' => $module_params['param03'],
        'card_name' => $bill_name,
        'next_phase' => 'paydata'
    );

    func_create_payment_form("https://secure.multicards.com/cgi-bin/order2/processorder1.pl", $post, 'MultiCards');
}
    exit;
?>

