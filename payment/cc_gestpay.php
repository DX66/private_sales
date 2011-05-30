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
 * "GestPay" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_gestpay.php,v 1.51.2.1 2011/01/10 13:12:06 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (isset($_POST) && isset($_POST['mode']) && $_POST['mode'] == 'import_passwords') {
    require '../top.inc.php';
    require $xcart_dir.DIR_ADMIN.'/auth.php';
    require $xcart_dir.'/include/security.php';

    $success = false;

    function func_import_gestpay_passwords ($file, $type) {
        global $file_temp_dir, $sql_tbl;
        $return = false;

        x_load('files');

        $file = func_move_uploaded_file($file);
        if ($file === false)
            return $return;

        $fp = func_fopen($file,'rt',true);
        if ($fp) {
            while (!feof($fp)) {
                $pass = trim(fgets($fp, 33));
                if ($pass) {
                    func_array2insert('cc_gestpay_data', array('value' => addslashes($pass), 'type' => addslashes($type)), true);
                    $return = true;
                }
            }

            fclose ($fp);
        }
        @unlink ($file);

        return $return;
    }

    if ($delete_all == 'Y')
        db_query ("DELETE FROM $sql_tbl[cc_gestpay_data]");

    if (isset($_FILES['ric']))
        $success = func_import_gestpay_passwords ('ric', 'C');

    if (isset($_FILES['ris']))
        $success = func_import_gestpay_passwords ('ris', 'S') || $success;

    x_session_register('top_message');

    $top_message = array(
        'type' => $success ? 'I' : 'E',
        'content' => func_get_langvar_by_name($success ? 'msg_data_import_success' : 'msg_data_import_no_sections')
    );

    func_header_location($xcart_catalogs['admin']."/cc_processing.php?mode=update&cc_processor=cc_gestpay.php");
    exit;
}

if (!isset($REQUEST_METHOD))
    $REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];

if (!isset($QUERY_STRING))
    $QUERY_STRING = $_SERVER['QUERY_STRING'];

if ($REQUEST_METHOD == 'GET' && !empty($_GET['a']) && !empty($_GET['b'])) {

    require './auth.php';

    $responses = array();
    if ($_GET['b']) {
        $tmp = explode("*P1*", $_GET['b']);
        if (!empty($tmp)) {
            foreach ($tmp as $value) {
                $tmp2 = explode("=", $value, 2);
                $responses[$tmp2[0]] = $tmp2[1];
            }
        }
    }

    $result = $responses['PAY1_TRANSACTIONRESULT'];
    $otp = $responses['PAY1_OTP'];
    $key = $responses['PAY1_SHOPTRANSACTIONID'];

    // One Time Password checking
    $res = func_query_first("SELECT * FROM $sql_tbl[cc_gestpay_data] WHERE value='$otp' AND type='S'");
    if ($res)
        db_query("DELETE FROM $sql_tbl[cc_gestpay_data] WHERE value='$otp' AND type='S'");

    // Session restore
    $bill_output['sessid'] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$key."'");

    // Response checking
    if ((strtolower($result) == 'ok') && !empty($res)) {
        $bill_output['code'] = 1;
        $bill_output['billmes'] = "Status: OK.\n";

    } else if ((strtolower($result) == 'ok') && empty($res)) {
        $bill_output['code'] = 2;
        $bill_output['billmes'] = "Status: Declined. Wrong OTP value.\n";

    } else if (strtolower($result) == 'ko') {
        $bill_output['code'] = 2;
        $bill_output['billmes'] = "Status: Declined.\n";

    } else if (strtolower($result) == 'xx') {
        $bill_output['code'] = 3;
        $bill_output['billmes'] = "Status: Queued.\n";

    } else {
        $bill_output['code'] = 2;
        $bill_output['billmes'] = "Status: Declined.\n";
    }

    if (!empty($responses['PAY1_ERRORDESCRIPTION']))
        $bill_output['billmes'] .= $responses['PAY1_ERRORDESCRIPTION']."\n";

    if (!empty($responses['PAY1_ERRORCODE']) && $responses['PAY1_ERRORCODE'] > 0)
        $bill_output['billmes'] .= "Error code: ".$responses['PAY1_ERRORCODE']."\n";

    if (!empty($responses['PAY1_AUTHORIZATIONCODE']))
        $bill_output['billmes'] .= "Autorization code: ".$responses['PAY1_AUTHORIZATIONCODE']."\n";

    if (!empty($responses['PAY1_BANKTRANSACTIONID']))
        $bill_output['billmes'] .= "Bank transaction ID: ".$responses['PAY1_BANKTRANSACTIONID']."\n";

    require $xcart_dir.'/payment/payment_ccend.php';

} else if ($REQUEST_METHOD == 'GET' && $QUERY_STRING) {

    x_load('debug');
    $log = "Wrong GestPay response on cc_gestpay.php\n'.'_GET:\n".var_export($_GET, true)."\n";
    x_log_flag('log_debug_messages', 'DEBUG', $log, true);

} else {
    if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

    $merchant_id = $module_params['param01'];
    $currency = $module_params['param02'];
    $ordr = $module_params ['param04'].join("-",$secure_oid);

    $payment_url = "https://".($module_params['testmode'] == 'Y' ? 'test':'').'ecomm.sella.it/gestpay/pagam.asp';

    if (!$duplicate)
        func_array2insert('cc_pp3_data', array('ref' => $ordr, 'sessionid' => $XCARTSESSID), true);

    // Fetch OTP here and Delete OTP from the database if exist
    $otp = func_query_first_cell("SELECT value FROM $sql_tbl[cc_gestpay_data] WHERE type='C'");
    if ($otp)
        db_query ("DELETE FROM $sql_tbl[cc_gestpay_data] WHERE value='$otp' AND type='C'");

    $b = "PAY1_UICCODE=".$currency."*P1*PAY1_AMOUNT=".round($cart['total_cost'],2)."*P1*PAY1_SHOPTRANSACTIONID=".$ordr."*P1*PAY1_OTP=".$otp;
    $fields = array(
        'a' => $merchant_id,
        'b' => $b
    );
    func_create_payment_form($payment_url, $fields, 'GestPay', 'GET');
}
exit;

?>
