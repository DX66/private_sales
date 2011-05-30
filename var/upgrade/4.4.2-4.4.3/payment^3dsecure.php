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
 * 3D Secure payment gateway
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: 3dsecure.php,v 1.23.2.2 2011/01/10 13:12:05 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) {

    require './auth.php';

    define('3DSECURE_STAND_ALONE', true);

}

/**
 * 3D Secure debug mode
 */
define('3DSECURE_DEBUG', false);

x_load('http');

// Output:
// $secure_3d = array(
//    'error' =>
//    'error_msg' =>
//    'data' => array()
// )

// Check function output:
//    true     - 3D Secure enabled
//    false    - 3D Secure disabled
//    CMPI    - use CMPI-version

// Verify function input:
//    return URL

// Verify function output:
// array(
//    'error' =>
//    'error_msg' =>
//    'data' =>
//    'form_url' =>
//    'form_data' =>
//    'md' =>
//    'no_iframe' =>
// )

// Validate function output:
// array(
//    'error' =>
//    'error_msg' =>
//    'data' =>
// )


// Generate unique tranid
function func_generate_3dsecure_tranid()
{
    global $sql_tbl;

    #The random number generator is seeded automatically since 4.2.0
    if (version_compare(phpversion(), '4.2.0') < 1)
        mt_srand ((double) microtime() * 1000000);

    $max = 500;

    while(true) {

        mt_srand();

        $tranid = md5(mt_rand(0, XC_TIME));

        if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[secure3d_data] WHERE tranid = '$tranid'") == 0)
            break;

        if ($max-- < 0)
            return false;

    }

    return $tranid;
}

// Check 3dsecure tranid
function func_check_3dsecure($tranid)
{
    global $xcart_dir, $XCARTSESSID, $sql_tbl;

    static $serialized_fields = array(
        'get_data',
        'session_data',
        'form_data',
        'return_data',
        'service_data',
    );

    if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[secure3d_data] WHERE tranid = '$tranid'") == 0)
        return false;

    $data = func_query_first("SELECT * FROM $sql_tbl[secure3d_data] WHERE tranid = '$tranid'");

    if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[sessions_data] WHERE sessid = '$data[sessid]'") == 0) {

        db_query("DELETE FROM $sql_tbl[secure3d_data] WHERE tranid = '$tranid'");

        return false;

    }

    $func_file = $xcart_dir . '/payment/' . str_replace('.php', '.func.php', $data['processor']);

    if (
        file_exists($func_file)
        && is_readable($func_file)
    ) {
        require_once $func_file;
    }

    if (
        !function_exists($data['verify_funcname'])
        || !function_exists($data['validate_funcname'])
    ) {

        db_query("DELETE FROM $sql_tbl[secure3d_data] WHERE tranid = '$tranid'");

        return false;

    }

    foreach($serialized_fields as $sf) {

        if (
            isset($data[$sf])
            && !empty($data[$sf])
        ) {
            $data[$sf] = unserialize($data[$sf]);
        }

    }

    if ($XCARTSESSID != $data['sessid']) {

        x_session_id($data['sessid']);

    }

    foreach ($data['session_data'] as $vn) {

        if (is_string($vn)) {

            x_session_register($vn);

        }

    }

    return $data;
}

// Close iframe
function func_close_iframe_3dsecure($tranid)
{
?>
<script type="text/javascript">
<!--
if (window.parent)
    window.parent.location = '3dsecure.php?action=close_iframe&tranid=<?php echo $tranid; ?>';
else
    window.location = '3dsecure.php?action=close_iframe&tranid=<?php echo $tranid; ?>';
-->
</script>
<?php
    exit;
}

// Debug
function func_debug_3dsecure($mess)
{
    if (
        !defined('3DSECURE_DEBUG')
        || !constant('3DSECURE_DEBUG')
    ) {
        return;
    }

    global $login;

    $log = <<<MESS
3D Secure debug
Logged as: $login
Message: $mess
MESS;

    x_log_flag('log_debug_messages', 'DEBUG', $log, true, 1);

}

// Restore global variables
function func_restore_env_3dsecure($data, $exceptions = array())
{
    if (
        !empty($data['get_data'])
        && is_array($data['get_data'])
    ) {

        foreach($data['get_data'] as $k => $v) {

            if (
                empty($k)
                || !is_string($k)
                || (
                    !empty($exceptions)
                    && in_array($k, $exceptions)
                )
            ) {
                continue;
            }

            $GLOBALS[$k] = $v;

        }

    }

    return true;
}

if ($action == 'place_order') {

    func_debug_3dsecure('START');

    if (
        empty($module_params)
        || !is_array($module_params)
        || !func_is_active_payment($module_params['processor'])
    ) {

        $bill_output = array(
            'code'         => 2,
            'billmes'     => "3-D Secure payment authentication error: Input data is invalid or missing. Please contact the store administrator.",
        );

        func_debug_3dsecure($bill_output['billmes']);

        require $xcart_dir . '/payment/payment_ccend.php';

        exit;

    }

    if (isset($verify_funcname))
        unset($verify_funcname);

    if (isset($validate_funcname))
        unset($validate_funcname);

    $func_file = $xcart_dir . '/payment/' . str_replace('.php', '.func.php', $module_params['processor']);

    if (
        file_exists($func_file)
        && is_readable($func_file)
    ) {
        require_once $func_file;
    }

    if (!isset($check_funcname)) {
        $check_funcname = 'func_' . str_replace('.php', '', $module_params['processor']) . "_check";
    }

    if (!isset($verify_funcname)) {
        $verify_funcname = 'func_' . str_replace('.php', '', $module_params['processor']) . "_verify";
    }

    if (!isset($validate_funcname)) {
         $validate_funcname = 'func_' . str_replace('.php', '', $module_params['processor']) . "_validate";
    }

    // First run from payment module
    if (
        empty($verify_funcname)
        || !function_exists($verify_funcname)
        || empty($validate_funcname)
        || !function_exists($validate_funcname)
    ) {
        $bill_output = array(
            'code'         => 2,
            'billmes'     => "3-D Secure payment authentication error: Input data is invalid or missing. Please contact the store administrator.",
        );

        func_debug_3dsecure($bill_output['billmes']);

        require $xcart_dir . '/payment/payment_ccend.php';

        exit;

    }

    if (
        !empty($check_funcname)
        && function_exists($check_funcname)
    ) {

        $check_res = call_user_func($check_funcname);

        if ($check_res === false) {

            func_debug_3dsecure("3D Secure is disabled");

            return;

        } elseif ($check_res === 'CMPI') {

            if (
                file_exists($xcart_dir . '/payment/cmpi.php')
                && $config['CMPI']['cmpi_enabled'] == 'Y'
                && in_array(
                    $card_type,
                    array(
                        'VISA',
                        'MC',
                        'JCB',
                    )
                )
            ) {

                func_debug_3dsecure("Use CMPI version");

                require $xcart_dir.'/payment/cmpi.php';

            } else {

                func_debug_3dsecure("Use CMPI version and CMPI is disabled");

            }

            return;
        }

    }

    $tranid = func_generate_3dsecure_tranid();

    if (!$tranid) {

        $bill_output = array(
            'code'         => 2,
            'billmes'     => "3-D Secure payment authentication error: Unique transaction code is missing. Please contact the store administrator.",
        );

        func_debug_3dsecure($bill_output['billmes']);

        require $xcart_dir . '/payment/payment_ccend.php';

        exit;

    }

    func_debug_3dsecure("Create tranId: ".$tranid);

    $saved_vars = array(
        'products',
        'userinfo',
        'bill_firstname',
        'bill_lastname',
        'bill_name',
        'ship_firstname',
        'ship_lastname',
        'ship_name',
        '_POST',
        '_GET',
        'card_expire',
        'card_expire_Month',
        'card_expire_Year',
        'card_valid_from',
        'card_valid_from_Month',
        'card_valid_from_Year',
        'card_name',
        'card_type',
        'card_number',
        'card_cvv2',
        'paymentid',
        'payment_cc_data',
        'module_params',
        'bill_output',
    );

    $query = array(
        'tranid'                 => $tranid,
        'date'                     => XC_TIME,
        'get_data'                 => array(),
        'session_data'             => array(),
        'sessid'                 => $XCARTSESSID,
        'processor'             => $module_params['processor'],
        'verify_funcname'         => $verify_funcname,
        'validate_funcname'     => $validate_funcname,
    );

    foreach ($saved_vars as $vn) {

        $query['get_data'][$vn] = $$vn;

    }

    $query['get_data'] = serialize($query['get_data']);

    if (!empty($XCART_SESSION_VARS)) {

        $query['session_data'] = array_keys($XCART_SESSION_VARS);

    }

    $query['session_data'] = serialize($query['session_data']);

    func_array2insert(
        'secure3d_data',
        func_addslashes($query)
    );

    $res = call_user_func(
        $verify_funcname,
        $tranid,
        $current_location . "/payment/3dsecure.php?action=return&tranid=" . $tranid
    );

    func_debug_3dsecure("TranId: $tranid;\nVerify response: ".var_export($res, true));

    if (
        empty($res)
        || !is_array($res)
    ) {

        // Error (empty response) in verify function
        db_query("DELETE FROM $sql_tbl[secure3d_data] WHERE tranid = '$tranid'");

        $bill_output = array(
            'code'         => 2,
            'billmes'     => "3-D Secure payment authentication error: Payment module functionality responsible for conducting the payment transaction was aborted. Please contact the store administrator.",
        );

        func_debug_3dsecure("TranId: $tranid;\n" . $bill_output['billmes']);

        require $xcart_dir . '/payment/payment_ccend.php';

        exit;

    } elseif (
        $res['error'] < 0
        || !empty($res['error_msg'])
    ) {

        // Error in verify function
        db_query("DELETE FROM $sql_tbl[secure3d_data] WHERE tranid = '$tranid'");

        if (empty($res['data'])) {

            $bill_output = array(
                'code'         => 2,
                'billmes'     => "3-D Secure payment authentication error: ".$res['error_msg'],
            );

            func_debug_3dsecure("TranId: $tranid;\n" . $bill_output['billmes']);

            require $xcart_dir . '/payment/payment_ccend.php';

            exit;
        }

        $secure_3d = array(
            'error'         => $res['error'],
            'error_msg'     => $res['error_msg'],
            'data'             => $res['data'],
        );

        func_debug_3dsecure("TranId: $tranid;\nReturn (non-VbV): " . var_export($res, true));

        return;
    }

    if (
        empty($res['form_url'])
        && empty($res['form_data'])
    ) {

        if (!empty($res['data'])) {

            $secure_3d = array(
                'error'         => $res['error'],
                'error_msg'     => $res['error_msg'],
                'data'             => $res['data'],
            );

            func_debug_3dsecure("TranId: $tranid;\nReturn (non-VbV): " . var_export($res, true));

            return;

        } else {

            $bill_output = array(
                'code'         => 2,
                'billmes'     => "3-D Secure payment authentication error: Internal error. Please contact the store administrator.",
            );

            func_debug_3dsecure("TranId: $tranid;\n" . $bill_output['billmes']);

            require $xcart_dir . '/payment/payment_ccend.php';

            exit;

        }

    }

    $query = array(
        'form_url'         => $res['form_url'],
        'form_data'     => serialize($res['form_data']),
        'md'             => $res['md'],
        'no_iframe'     => $res['no_iframe'],
        'service_data'     => serialize($res['service_data']),
    );

    func_array2update(
        'secure3d_data',
        func_addslashes($query),
        "tranid = '$tranid'"
    );

    func_debug_3dsecure("TranId: $tranid;\nOpen IFRAME");

    if ($res['no_iframe']) {

        func_header_location("3dsecure.php?action=iframe&tranid=" . $tranid);

    } else {

?>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
    <td valign="top" align="center"><iframe width="410" height="410" scrolling="yes" marginwidth="0" marginheight="0" src="3dsecure.php?action=iframe&amp;tranid=<?php echo $tranid; ?>"></iframe></td>
</tr>
</table>
<?php
    }

} elseif ($action == 'iframe') {

    // Display IFRAME content
    $data = func_check_3dsecure($tranid);

    if (!$data) {
        func_close_iframe_3dsecure($tranid);
    }

    if (
        !empty($data['form_url'])
        && !empty($data['form_data'])
    ) {

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<body onload="javascript: document.fpsform.submit();">
<form action="<?php echo $data['form_url']; ?>" method="POST" name="fpsform">
<?php
        foreach($data['form_data'] as $fn => $fv) {
?>
<input type="hidden" name="<?php echo $fn; ?>" value="<?php /* echo htmlspecialchars($fv, ENT_QUOTES); */ echo $fv; ?>" />
<?php
        }
?>
<noscript>
<b>Note:</b> Click the Submit button if Javascript is disabled in your web browser and/or you haven't been redirected to the next page in 3-5 seconds.<br />
<input type="submit" />
</noscript>
</form>
</body>
</html>
<?php

    } elseif (!empty($data['form_data'])) {

        echo $data['form_data'];

    }

    func_debug_3dsecure("TranId: $tranid;\nDisplay IFRAME");

} elseif (
    $action == 'return'
    || (
        empty($action)
        && defined('3DSECURE_STAND_ALONE')
    )
) {

    // BeanStream fix
    foreach($_GET as $k => $v) {

        $k = preg_replace("/^amp;/", '', $k);

        $_GET[$k] = $$k = $v;

    }

    if (
        empty($tranid)
        && !empty($MD)
    ) {

        $tranid = func_query_first_cell("SELECT tranid FROM $sql_tbl[secure3d_data] WHERE md = '$MD'");

    }

    func_debug_3dsecure("TranId: $tranid;\nRedirect to shop:\n\tGET: ".var_export($_GET, true)."\n\tPOST: ".var_export($_POST, true));

    // Process 'Return to shop' action
    $data = func_check_3dsecure($tranid);

    if (!$data) {

        func_close_iframe_3dsecure($tranid);

    }

    func_restore_env_3dsecure($data, array('_GET', '_POST'));

    $res = call_user_func($data['validate_funcname']);

    func_debug_3dsecure("TranId: $tranid;\nValidate response: ".var_export($res, true));

    if (
        empty($res)
        || !is_array($res)
    ) {

        $return_data = array(
            'error'     => -3,
            'error_msg' => "Payment module functionality responsible for conducting the payment transaction was aborted. Please contact the store administrator.",
        );

    } elseif ($res['error'] < 0) {

        $return_data = array(
            'error'         => $res['error'],
            'error_msg'     => $res['error_msg'],
            'data'             => $res['data'],
        );

    } else {

        $return_data = $res;

    }

    func_array2update(
        'secure3d_data',
        array(
            'return_data' => func_addslashes(serialize($return_data)),
        ),
        "tranid = '$tranid'"
    );

    func_close_iframe_3dsecure($tranid);

} elseif ($action == 'close_iframe') {

    // Close IFRAME
    $secure_3d_full = func_check_3dsecure($tranid);

    if (!$secure_3d_full)
        exit;

    func_debug_3dsecure("TranId: $tranid;\nClose IFRAME");

    if ($secure_3d_full['return_data']) {

        $secure_3d = $secure_3d_full['return_data'];

    } else {

        $secure_3d = array(
            'error'     => -4,
            'error_msg' => "Internal error. Please contact the store administrator.",
        );

    }

    func_restore_env_3dsecure($secure_3d_full);

    if (
        $secure_3d['error'] < 0
        || !empty($secure_3d['error_msg'])
    ) {
        $bill_output = array(
            'code'         => 2,
            'billmes'     => "3-D Secure payment authentication error: " . $secure_3d['error_msg'],
        );
    }

    func_debug_3dsecure("TranId: $tranid;\nPrepare data\n\tsecure_3d: ".var_export($secure_3d, true)."\n\tbill_output: ".var_export($bill_output, true));

    if (!empty($secure_3d['data'])) {

        $fn = $xcart_dir . '/payment/' . basename($secure_3d_full['processor']);

        if (
            !file_exists($fn)
            || is_dir($fn)
        ) {
            exit;
        }

        func_debug_3dsecure("TranId: $tranid;\nCall processor '" . basename($secure_3d_full["processor"])."';\n\tpath: ".$fn);

        require $fn;

    }

    db_query("DELETE FROM $sql_tbl[secure3d_data] WHERE tranid = '$tranid'");

    $orderids = $secure_oid;

    func_debug_3dsecure("TranId: $tranid;\nGo to payment system core\n\tbill_output: " . var_export($bill_output, true));

    require $xcart_dir . '/payment/payment_ccend.php';

}

exit;

?>
