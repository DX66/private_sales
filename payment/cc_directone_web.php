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
 * "DirectOne - Standard Integration" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_directone_web.php,v 1.14.2.1 2011/01/10 13:12:05 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!isset($REQUEST_METHOD))
    $REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];

if ($REQUEST_METHOD == 'GET' && isset($_GET['result'])) {
    require './auth.php';

    if (!func_is_active_payment('cc_directone_web.php'))
        exit;

    $pp3_data = func_query_first("SELECT * FROM $sql_tbl[cc_pp3_data] WHERE ref='".$_GET['oid']."'");

    if ($_GET['result'] == 'reply') {

        $bill_output['sessid'] = $pp3_data['sessionid'];
        $bill_output['code'] = 1;
        $bill_output['billmes'].= " (payment_number: ".$_GET['payment_number'].") ";

        $skey = $_GET['oid'];
        require $xcart_dir.'/payment/payment_ccmid.php';
        require $xcart_dir.'/payment/payment_ccwebset.php';

    } elseif ($_GET['result'] == 'return') {

        $skey = $_GET['oid'];
        require $xcart_dir.'/payment/payment_ccview.php';
    }

} else {

    if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

    $pp_test = ($module_params['testmode']=='Y') ?     "https://vault.safepay.com.au/cgi-bin/test_payment.pl" :
                                                "https://vault.safepay.com.au/cgi-bin/make_payment.pl";

    $pp_vendor_name = $module_params['param01'];
    $ordr = $module_params['param02'].implode("-", $secure_oid);

    if(!$duplicate)
        db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");

    $return_url = $http_location."/payment/cc_directone_web.php?oid=$ordr&";

    $post = array();

    $post = func_array_merge($post, array(
        'vendor_name' => $pp_vendor_name,
        'payment_reference' => $ordr,
        'information_fields' => "Billing_name,Billing_address1,Billing_address2,Billing_city,Billing_state,Billing_zip,Billing_country,Contact_email,Contact_phone",
        "Order total" => $cart['total_cost'],
        'Contact_phone' => $userinfo['phone'],
        'Contact_email' => $userinfo['email'],
        'Billing_name' => $userinfo['b_firstname'].' '. $userinfo['b_lastname'],
        'Billing_country' => $userinfo['b_country'],
        'Billing_address1' => $userinfo['b_address'],
        'Billing_address2' => $userinfo['b_address_2'],
        'Billing_city' => $userinfo['b_city'],
        'Billing_state' => $userinfo['b_state'],
        'Billing_zip' => $userinfo['b_zipcode'],
        'Billing_country' => $userinfo['b_country'],
        'return_link_url' => $return_url."result=return&payment_number=",
        'reply_link_url' => $return_url."result=reply&payment_number="
    ));

    func_create_payment_form($pp_test, $post, "DirectOne gateway");
}

exit;
?>
