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
 * PayPoint Fast Track
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_paypointft.php,v 1.15.2.1 2011/01/10 13:12:07 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ($_SERVER['REQUEST_METHOD'] == "POST" && $_GET['mode'] == 'response') {

    // Callback
    require './auth.php';

    if (!func_is_active_payment('cc_paypointft.php'))
        exit;

    $module_params = func_get_pm_params('cc_paypointft.php');
    if ($module_params['param04']) {
        if ($module_params['param04'] != $_SERVER['PHP_AUTH_USER'] || ($module_params['param05'] && $module_params['param05'] != $_SERVER['PHP_AUTH_PW'])) {
            header('WWW-Authenticate: Basic');
            header('HTTP/1.0 401 Unauthorized');
            x_log_flag('log_payment_processing_errors', 'PAYMENTS', 'PayPoint Fast Track payment module: Caller authorization failed.');
            exit;
        }
    }

    $skey = $strCartID;
    $bill_output['sessid'] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref = '" . $skey . "'");
    $bill_output['code'] = $intStatus == 1 ? 1 : 2;

    if (!empty($strMessage))
        $bill_output['billmes'] = $strMessage;

    if (!empty($intTransID))
        $bill_output['billmes'] .= " (TransId: " . $intTransID . ")";

    if (isset($fltAmount)) {
        $payment_return = array(
            'total' => $fltAmount
        );

        if (isset($strCurrency)) {
            $payment_return['currency'] = $strCurrency;
            $payment_return['_currency'] = func_query_first_cell("SELECT param02 FROM $sql_tbl[ccprocessors] WHERE processor='cc_paypointft.php'");
        }
    }

    if (!empty($strTransID) && $bill_output['code'] == 1) {
        $extra_order_data = array(
            'xnid' => $strTransID,
            'capture_status' => $intAuthMode == 2 ? 'A' : ''
        );
    }

    require $xcart_dir.'/payment/payment_ccmid.php';
    require $xcart_dir.'/payment/payment_ccwebset.php';

} elseif ($_SERVER['REQUEST_METHOD'] == "POST" && $_GET['mode'] == 'return') {

    // Return
    require './auth.php';

    if (!func_is_active_payment('cc_paypointft.php'))
        exit;

    $skey = $strCartID;
    require($xcart_dir.'/payment/payment_ccview.php');

} else {

    // Redirect to gateway server
    if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

    $ordr = $module_params['param06'] . join("-", $secure_oid);
    if (!$duplicate)
        db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."')");

    $fields = array(
        'intInstID' => $module_params["param01"],
        'strCartID' => $ordr,
        'strDesc' => 'Order(s) #' . join("; #", $secure_oid),
        'fltAmount' => $cart["total_cost"],
        'strCurrency' => $module_params["param02"],
        'intAuthMode' => $module_params['use_preauth'] == 'Y' ? 2 : 1,
        'intTestMode' => $module_params["testmode"] == 'Y' ? 1 : 0,
        'strAddress' => substr($userinfo['b_address'] . ' ' . $userinfo["b_address_2"], 0, 255),
        'strCity' => substr($userinfo['b_city'], 0, 40),
        'strState' => substr($userinfo['b_state'], 0, 40),
        'strPostcode' => substr($userinfo['b_zipcode'], 0, 15),
        'strCountry' => substr($userinfo['b_country'], 0, 2),
        'strTel' => substr($userinfo['phone'], 0, 50),
        'strFax' => substr($userinfo['fax'], 0, 50),
        'strEmail' => substr($userinfo['email'], 0, 100),
        'strCardHolder' => substr($userinfo['b_firstname'] . ' ' . $userinfo["b_lastname"], 0, 20)
    );
    func_create_payment_form("https://secure.metacharge.com/mcpe/purser", $fields, "PayPoint Fast Track");
}

exit;

?>
