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
 * "Bean Stream" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_bean.php,v 1.26.2.3 2011/01/25 15:24:54 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

if (isset($secure_3d) && isset($secure_3d['data'])) {
    $mass = $secure_3d['data'];
} else {

    x_load('http');

    $an_login = $module_params['param01'];
    $an_prefix = $module_params['param04'];

    $post = array();
    $post[] = "requestType=BACKEND";
    $post[] = "merchant_id=".$an_login;
    $post[] = "trnOrderNumber=".$an_prefix.join("-",$secure_oid);
    $post[] = "trnType=P"; // P - purchase; PA - pre-auth...
    $post[] = "trnCardOwner=".$userinfo['card_name'];
    $post[] = "trnCardNumber=".$userinfo['card_number'];
    $post[] = "trnExpMonth=".substr($userinfo['card_expire'],0,2);
    $post[] = "trnExpYear=".substr($userinfo['card_expire'],2,2);
    $post[] = "trnCardCvd=".$userinfo['card_cvv2'];
    $post[] = "errorPage=".$https_location.DIR_CUSTOMER.'/home.php';

    $post[] = "ordName=".$bill_name;
    $post[] = "ordEmailAddress=".$userinfo['email'];
    $post[] = "ordPhoneNumber=".$userinfo['b_phone'];
    $post[] = "ordAddress1=".$userinfo['b_address'];
    $post[] = "ordCity=".$userinfo['b_city'];
    $post[] = "ordProvince=".(strlen($userinfo['b_state'])!=2 ? "--" : $userinfo['b_state']);
    $post[] = "ordPostalCode=".$userinfo['b_zipcode'];
    $post[] = "ordCountry=".$userinfo['b_country'];

    $post[] = "shipAddress1=".$userinfo['s_address'];
    $post[] = "shipCity=".$userinfo['s_city'];
    $post[] = "shipProvince=".(strlen($userinfo['s_state'])!=2 ? "--" : $userinfo['s_state']);
    $post[] = "shipPostalCode=".$userinfo['s_zipcode'];
    $post[] = "shipCountry=".$userinfo['s_country'];
    $post[] = "shipPhoneNumber=".$userinfo['s_phone'];

    $post[] = "trnAmount=".$cart['total_cost'];

    if (defined('BEANSTREAM_DEBUG')) {
        func_pp_debug_log('beanstream', 'I', $post);
    }

    list($a, $return) = func_https_request('POST',"https://www.beanstream.com:443/scripts/process_transaction.asp", $post);

    if (defined('BEANSTREAM_DEBUG')) {
        func_pp_debug_log('beanstream', 'R', $return);
    }

    $mass = array();
    parse_str($return, $mass);
}

$bill_output['billmes'] = $mass['messageText'].$mass['errorMessage'];

if ($mass['messageId'] == 1 && !empty($mass['authCode'])) {
    $bill_output['code'] = 1;
    $bill_output['billmes'] .= " (authCode: ".$mass['authCode'].")";
} else {
    $bill_output['code'] = 2;
}

if (!empty($mass['trnId']))
    $bill_output['billmes'] .= " (TrnID: ".$mass['trnId'].")";

if ($mass['avsMessage'])
    $bill_output['avsmes'] = $mass['avsMessage']." (Code: ".$mass['avsResult'].")";
?>
