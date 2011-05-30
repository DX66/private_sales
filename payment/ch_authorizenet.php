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
 * "AuthorizeNet - eCheck" payment module (check processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: ch_authorizenet.php,v 1.38.2.1 2011/01/10 13:12:07 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('crypt','http');

if (empty($module_params['param07']))
    $module_params['param07'] = 'A';

$module_params['param01'] = text_decrypt($module_params['param01']);
$module_params['param02'] = text_decrypt($module_params['param02']);
if (is_null($module_params['param01'])) {
    x_log_flag('log_decrypt_errors', 'DECRYPT', "Could not decrypt the field 'param01' for AuthorizeNet: AIM CH payment module", true);
}
if (is_null($module_params['param02'])) {
    x_log_flag('log_decrypt_errors', 'DECRYPT', "Could not decrypt the field 'param02' for AuthorizeNet: AIM CH payment module", true);
}

$post = array();
$post[] = "x_login=".$module_params['param01'];
$post[] = "x_tran_key=".$module_params['param02'];
$post[] = "x_version=3.1";
$post[] = "x_test_request=" . ($module_params['testmode'] == 'N' ? 'FALSE' : 'TRUE');
$post[] = "x_delim_data=TRUE";
$post[] = "x_delim_char=,";
$post[] = "x_encap_char=|";

$post[] = "x_first_name=".$bill_firstname;
$post[] = "x_last_name=".$bill_lastname;
$post[] = "x_address=".$userinfo['b_address'];
$post[] = "x_company=".$userinfo['company'];
$post[] = "x_city=".$userinfo['b_city'];
$post[] = "x_state=".((!empty($userinfo['b_state']) && $userinfo['b_state'] != 'Other') ? $userinfo['b_state'] : "Non US");
$post[] = "x_zip=".$userinfo['b_zipcode'];
$post[] = "x_country=".$userinfo['b_country'];

$post[] = "x_ship_to_first_name=".($userinfo['s_firstname'] ? $userinfo['s_firstname'] : $userinfo['firstname']);
$post[] = "x_ship_to_last_name=".($userinfo['s_lastname'] ? $userinfo['s_lastname'] : $userinfo['lastname']);
$post[] = "x_ship_to_address=".$userinfo['s_address'];
$post[] = "x_ship_to_company=".$userinfo['company'];
$post[] = "x_ship_to_city=".$userinfo['s_city'];
$post[] = "x_ship_to_state=".((!empty($userinfo['s_state']) && $userinfo['s_state']!="Other")? $userinfo['s_state'] : "Non US");
$post[] = "x_ship_to_zip=".$userinfo['s_zipcode'];
$post[] = "x_ship_to_country=".$userinfo['s_country'];

$post[] = "x_phone=".$userinfo['phone'];
$post[] = "x_fax=".$userinfo['fax'];
$post[] = "x_cust_id=".$userinfo['login'];
$post[] = "x_customer_ip=".func_get_valid_ip($REMOTE_ADDR);
$post[] = "x_email=".$userinfo['email'];
$post[] = "x_email_customer=FALSE";
$post[] = "x_merchant_email=".$config['Company']['orders_department'];
$post[] = "x_invoice_num=".$module_params['param04'].join("-",$secure_oid);
$post[] = "x_amount=".price_format($cart['total_cost']);
$post[] = "x_currency_code=".$module_params['param05'];
$post[] = "x_method=ECHECK";
$post[] = "x_recurring_billing=".($is_rbilling?'YES':'NO');
$post[] = "x_type=auth_capture";
$post[] = "x_relay_response=FALSE";
$post[] = "x_tax=".$cart['tax_cost'];
$post[] = "x_freight=".$cart['shipping_cost'];

if ($module_params['param07'] == 'W') {
    $post[] = "x_customer_organization_type=".$userinfo['check_wf_org_type'];
    $post[] = "x_customer_tax_id=".$userinfo['check_wf_ssn'];
    $post[] = "x_drivers_license_num=".$userinfo['check_wf_dln'];
    $post[] = "x_drivers_license_state=".$userinfo['check_wf_dls'];
    $post[] = "x_drivers_license_dob=".$userinfo['check_wf_dldob'];
}

$post[] = "x_bank_acct_type=CHECKING";
$post[] = "x_bank_aba_code=".$userinfo['check_brn'];
$post[] = "x_bank_acct_num=".$userinfo['check_ban'];
$post[] = "x_bank_name=".$userinfo['check_bname'];
$post[] = "x_bank_acct_name=".$userinfo['check_name'];
$post[] = "x_echeck_type=WEB";

list($a, $return) = func_https_request('POST', "https://secure.authorize.net:443/gateway/transact.dll", $post);

$mass = explode("|,|", "|," . $return);

if(!empty($module_params['param06'])) {
    if(md5($module_params['param06'].text_decrypt($module_params['param01']).$mass[7].price_format($cart['total_cost'])) != strtolower($mass[38])) {
        $mass = array();
        $mass[1] = 3;
        $mass[4] = "MD5 transaction signature is incorrect!";
        $mass[3] = 0;
        $mass[2] = 0;
    }
}

if ($mass[1] == 1) {
    $bill_output['code'] = 1;
    $bill_output['billmes'] = " Approval Code: ".$mass[7];

} elseif ($mass[2] == 1 && $mass[1] == 4) {
    $bill_output['code'] = 3;
    $bill_output['billmes'] = " This transaction is being held for review. Approval Code: ".$mass[7]."; (N ".$mass[3]." / Sub ".$mass[2].")";

} else {
    $bill_output['code'] = 2;
    $bill_output['billmes'] = ($mass[1] == 2 ? "Declined" : "Error").": ";
    $bill_output['billmes'].= $mass[4]." (N ".$mass[3]." / Sub ".$mass[2].")";
}

?>
