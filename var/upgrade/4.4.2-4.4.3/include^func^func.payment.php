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
 * Common payment functions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (C) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.payment.php,v 1.68.2.2 2011/01/10 13:11:52 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

define('X_PAYMENT_TRANS_ALREADY_CAPTURED', 1);
define('X_PAYMENT_TRANS_ALREADY_VOIDED', 2);
define('X_PAYMENT_TRANS_ALREADY_REFUNDED', 4);
define('X_PAYMENT_TRANS_ALREADY_ACCEPTED', 8);
define('X_PAYMENT_TRANS_ALREADY_DECLINED', 16);

define('PAYMENT_NEW_STATUS', 1);
define('PAYMENT_AUTH_STATUS', 2);
define('PAYMENT_DECLINED_STATUS', 3);
define('PAYMENT_CHARGED_STATUS', 4);

define('X_USE_PAYPAL_FLOW', true);

/**
 * This function joins order_id's and urlencodes 'em
 */
function func_get_urlencoded_orderids ($orderids)
{
    if (!is_array($orderids))
        return '';

    return urlencode(join (",", $orderids));
}

function func_check_webinput($check_php = 1)
{
    global $config, $sql_tbl, $active_modules;

    static $pfiles = array (
        'cc_epdq.php' => 'cc_epdq_result.php',
        'cc_smartpag.php' => 'cc_smartpag_final.php',
        'cc_payzip.php' => 'cc_payzip_result.php',
        'cc_payflow_link.php' => 'cc_payflow_link_result.php',
        'cc_hsbc.php' => 'cc_hsbc_result.php',
        'cc_ogoneweb.php' => 'cc_ogoneweb_result.php',
        'cc_pswbill.php' => 'cc_pswbill_return.php',
        'cc_triple.php' => 'cc_triple_return.php',
        'cc_paybox.php' => 'cc_paybox_result.php',
        'cc_pp3.php' => array (
            'ebank_ok.php',
            'ebank_nok.php'
        ),
        'cc_pi.php' => 'cc_pi_result.php'
    );

    $allow_php = array(
        'cc_protxdir.php'
    );
    $list = func_query_column("SELECT c.processor FROM $sql_tbl[ccprocessors] c, $sql_tbl[payment_methods] m WHERE m.active = 'Y' AND m.paymentid = c.paymentid AND (c.background <> 'Y' OR c.processor = 'ps_paypal_pro.php')");

    if ($list) {
        if (in_array('ps_paypal_pro.php', $list)) {
            $list[] = 'ps_paypal.php';

            // PayPal Direct payment emulation
            if ($active_modules['XPayments_Connector']) {
                func_xpay_func_load();

                $pro_proc = xpc_get_paypal_dp_processor('pro');
                $uk_proc = xpc_get_paypal_dp_processor('uk');
                if ($pro_proc['use'] == 'xpc' || $uk_proc['use'] == 'xpc') {
                    $list[] = 'cc_xpc.php';
                }
            }
        }

        foreach($list as $file) {
            if (!empty($pfiles[$file])) {
                if (is_array($pfiles[$file]))
                    $allow_php = func_array_merge($allow_php, $pfiles[$file]);
                else
                    $allow_php[] = $pfiles[$file];
            }
            else {
                $allow_php[] = $file;
            }
        }
    }

    $ip = $_SERVER['REMOTE_ADDR'];
    $allow_ip = $config['Security']['allow_ips'];

    $not_found = true;
    if ($check_php && !empty($allow_php)) {
        for ($i = 0; $i < count($allow_php); $i++)
            $allow_php[$i] = preg_quote($allow_php[$i]);
        $script = $_SERVER['PHP_SELF'];
        $re_allow = "!(".implode("|",$allow_php).")$!S";
        $not_found = !preg_match($re_allow, $script);
    }

    if ($not_found) {
        x_log_flag('log_payment_processing_errors', 'PAYMENTS', "The script '".$_SERVER["PHP_SELF"]."' is not an entry point for a payment system!", true);
        header("Location: ../");
        die("Access denied");
    }

    if ($allow_ip) {
        $not_found = true;
        $a = explode(",", $allow_ip);
        foreach ($a as $v) {
            list($aip, $amsk) = explode('/', trim($v));

            // Cannot use 0x100000000 instead 4294967296
            $amsk = 4294967296 - ($amsk ? pow(2,(32-$amsk)) : 1);

            if ((ip2long($ip) & $amsk) == ip2long($aip)) {
                $not_found = false;
                break;
            }
        }

        return ($not_found ? 'err' : 'pass');
    }

    return 'pass';
}

/**
 * Display payment page footer
 */
function func_payment_footer()
{
    global $xcart_dir, $smarty_skin_dir;

    if (defined('DISP_PAYMENT_FOOTER'))
        return false;

    $fn = $xcart_dir . $smarty_skin_dir . '/customer/main/payment_wait_end.tpl';

    $fp = @fopen($fn, 'r');

    if ($fp) {

        $data = fread($fp, func_filesize($fn));

        fclose($fp);

        $data = preg_replace("/\{\*.*\*\}/Us", '', $data);
        $data = preg_replace("/\{\/?literal\}/Us", '', $data);

        func_flush($data);
    }

    define('DISP_PAYMENT_FOOTER', true);
}

/**
 * Prepare smarty variables and display a form to submit
 * to the payment gateway
 *
 * @param string $url            URL to where the form will be submitted
 * @param array  $fields         Associative array as field_name => field_value
 * @param string $name           Name of the payment processor
 * @param string $method         Form submit method: get/post
 * @param bool   $cc_form        Flag to enter cc data on X-Cart side, before submit
 * @param array  $cc_form_fields Associative array containing necessary data about cc fields
 *                               Format as follows:
 *                               xcart_field_name => array(
 *                                  field_name => field name to be passed to a payment gateway
 *                                  required => Y/N
 *                                  cc_types => required for card_type field and contains an array
 *                                              of supported card types and code matches, e.g:
 *                                              'VISA' => 'V', 'MC' => 'M' etc, where key is a card code in X-Cart,
 *                                              value is a code to be sent to a gateway
 *                               )
 *
 *
 * @return void
 * @see    ____func_see____
 */
function func_create_payment_form($url, $fields, $name, $method = 'post', $cc_form = false, $cc_form_fields = array())
{
    global $smarty;

    $method = strtolower($method);
    if (!in_array($method, array('post', 'get'))) {
        $method = 'post';
    }

    // Assign Smarty vars and show form template

    $smarty->assign('request_url', $url);
    $smarty->assign('method', $method);
    $smarty->assign('fields', $fields);
    $smarty->assign('cc_tpl', $cc_tpl);
    $smarty->assign('payment', $name);
    $smarty->assign('cc_current_year', intval(strftime('%Y')));

    if (!$cc_form) {
        $smarty->assign('autosubmit', true);
    } else {
        $smarty->assign('cc_form_fields', $cc_form_fields);
        $smarty->assign('display_cc_form', true);
    }

    func_flush(func_display('payments/payment_form.tpl', $smarty, false));
}

/**
 * Get valid IP
 */
function func_get_valid_ip($ip)
{
    return func_is_valid_ip($ip) ? $ip : '127.0.0.1';
}

/**
 * Check payment activity
 */
function func_is_active_payment($php_script)
{
    global $sql_tbl;

    $cnt = func_query_first_cell("SELECT COUNT($sql_tbl[ccprocessors].processor) FROM $sql_tbl[ccprocessors], $sql_tbl[payment_methods] WHERE $sql_tbl[ccprocessors].processor = '".addslashes($php_script)."' AND $sql_tbl[ccprocessors].paymentid = $sql_tbl[payment_methods].paymentid AND $sql_tbl[payment_methods].active = 'Y'");
    return ($cnt > 0);

}

/**
 * Force enable preauth
 */
function func_is_preauth_force_enabled($orderids)
{
    global $config;

    x_load('order');

    $frf = func_get_orders_fraud_risk_factor($orderids);
    if (!$frf)
        return false;

    $limit = intval($config['Anti_Fraud']['frf_limit_for_preauth']);

    return ($limit > 0 && $frf > $limit);
}

/**
 * Check pre-auth payment transaction with expired TTL
 */
function func_check_preauth_expiration()
{
    global $sql_tbl;

    $prev_event = func_get_event('payment_ttl');
    if ((XC_TIME - $prev_event) <= constant('SECONDS_PER_DAY'))
        return func_get_langvar_by_name('txt_preauth_check_by_ttl_no_orders', array(), false, true);
    func_set_event('payment_ttl');

    x_load('order');

    $orders = func_query("SELECT $sql_tbl[orders].orderid, $sql_tbl[orders].date, $sql_tbl[orders].status, $sql_tbl[ccprocessors].preauth_expire FROM $sql_tbl[order_extras] INNER JOIN $sql_tbl[orders] ON $sql_tbl[order_extras].orderid = $sql_tbl[orders].orderid AND $sql_tbl[orders].status = 'A' INNER JOIN $sql_tbl[ccprocessors] ON $sql_tbl[orders].paymentid = $sql_tbl[ccprocessors].paymentid AND $sql_tbl[ccprocessors].preauth_expire > 0 AND $sql_tbl[ccprocessors].has_preauth = 'Y' WHERE $sql_tbl[order_extras].khash = 'capture_status' AND $sql_tbl[order_extras].value = 'A'");

    if (empty($orders))
        return func_get_langvar_by_name('txt_preauth_decline_by_ttl_no_orders', array(), false, true);

    $expired = array();
    foreach ($orders as $o) {
        if ($o['date'] + $o['preauth_expire'] < XC_TIME && func_order_is_voided(intval($o['orderid']))) {
            define('STATUS_CHANGE_REF', 4);
            func_change_order_status($o['orderid'], 'D');
            func_array2insert(
                'order_extras',
                array(
                    'orderid' => $o['orderid'],
                    'khash' => 'preauth_expired',
                    'value' => 'Y'
                ),
                true
            );
            $expired[] = $o['orderid'];
        }
    }

    if (count($expired) > 0)
        return func_get_langvar_by_name('txt_preauth_decline_by_ttl', array('orders' => implode(", ", $expired)), false, true);

    return func_get_langvar_by_name('txt_preauth_decline_by_ttl_no_orders', array(), false, true);
}

/**
 * Check pre-auth payment transaction TTL
 */
function func_check_preauth_expiration_ttl()
{
    global $sql_tbl, $mail_smarty, $config;

    x_load('order');

    $orders = func_query("SELECT $sql_tbl[orders].orderid, $sql_tbl[orders].date, $sql_tbl[orders].status, $sql_tbl[ccprocessors].preauth_expire FROM $sql_tbl[order_extras] INNER JOIN $sql_tbl[orders] ON $sql_tbl[order_extras].orderid = $sql_tbl[orders].orderid AND $sql_tbl[orders].status = 'A' INNER JOIN $sql_tbl[ccprocessors] ON $sql_tbl[orders].paymentid = $sql_tbl[ccprocessors].paymentid AND $sql_tbl[ccprocessors].preauth_expire > 0 AND $sql_tbl[ccprocessors].has_preauth = 'Y' LEFT JOIN $sql_tbl[order_extras] as oe2 ON oe2.orderid = $sql_tbl[order_extras].orderid AND oe2.khash = 'preath_expire_soon' WHERE $sql_tbl[order_extras].khash = 'capture_status' AND $sql_tbl[order_extras].value = 'A' AND oe2.value IS NULL");

    if (empty($orders) || $config['General']['preauth_expired_period_warning'] <= 0)
        return func_get_langvar_by_name('txt_preauth_check_by_ttl_no_orders', array(), false, true);

    $expired = array();
    foreach ($orders as $o) {
        if (($o['date'] + $o['preauth_expire'] - ($config['General']['preauth_expired_period_warning'] * 86400) < XC_TIME) && func_order_is_authorized(intval($o['orderid']))) {
            $expired[] = $o['orderid'];
            func_array2insert(
                'order_extras',
                array(
                    'orderid' => $o['orderid'],
                    'khash' => 'preath_expire_soon',
                    'value' => 'Y'
                ),
                true
            );
        }
    }

    if (count($expired) > 0) {
        $mail_smarty->assign('orderids', implode(", ", $expired));
        func_send_mail(
            $config['Company']['orders_department'],
            'mail/order_preauth_expire_subj.tpl',
            'mail/order_preauth_expire.tpl',
            $config['Company']['site_administrator'],
            false
        );
        return func_get_langvar_by_name('txt_preauth_check_by_ttl', array('orders' => implode(", ", $expired)), false, true);
    }

    return func_get_langvar_by_name('txt_preauth_check_by_ttl_no_orders', array(), false, true);
}

/**
 * Get payment module parameters
 */
function func_get_pm_params($payment_name)
{
    global $sql_tbl;

    if (is_integer($payment_name)) {
        $data = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE paymentid = '" . $payment_name . "'");
    } else {
        $data = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor = '" . $payment_name . "'");
    }

    return $data;
}

/**
 * Include payment module functions repository
 */
function func_pm_load($payment_name)
{
    global $xcart_dir;
    static $cache = array();

    if (!is_string($payment_name) || zerolen($payment_name))
        return false;

    $payment_name = preg_replace('/\.php$/Ss', '', $payment_name);

    if (isset($cache[$payment_name]))
        return true;

    $path = $xcart_dir . '/include/payment/func.' . $payment_name . '.php';

    if (!file_exists($path) || !is_readable($path))
        return false;

    require_once($path);
    $cache[$payment_name] = true;

    return true;
}

/**
 * Get function name for Capture transaction
 */
function func_payment_get_capture_func($paymentid)
{
    global $sql_tbl, $active_modules;

    $processor_file = func_query_first_cell("SELECT processor_file FROM $sql_tbl[payment_methods] WHERE paymentid = '" . $paymentid . "'");

    if (strpos($processor_file, 'ps_paypal') === 0) {
        x_load('paypal');
    }

    if ($processor_file == 'ps_paypal_pro.php') {
        $ids = func_query_column("SELECT paymentid FROM $sql_tbl[payment_methods] WHERE processor_file = '$processor_file'");
        $processor = func_query_first_cell("SELECT processor FROM $sql_tbl[ccprocessors] WHERE paymentid IN ('" . implode("','", $ids) . "')");

        if ($active_modules['XPayments_Connector']) {
            func_xpay_func_load();

            if (xpc_is_emulated_paypal($paymentid)) {
                return xpc_can_capture($paymentid);
            }
        }

    } elseif ($processor_file == 'cc_xpc.php') {

        if (!$active_modules['XPayments_Connector']) {
            return false;
        }

        func_xpay_func_load();

        return xpc_can_capture($paymentid);

    } else {
        $processor = func_query_first_cell("SELECT processor FROM $sql_tbl[ccprocessors] WHERE paymentid = '" . $paymentid . "'");
    }

    if (!$processor)
        return false;

    $payment_name = preg_replace("/\.php/Ss", '', $processor);

    $func_name = 'func_' . $payment_name .'_do_capture';

    func_pm_load($payment_name);

    return function_exists($func_name) ? $func_name : false;
}

/**
 * Get function name for Void transaction
 */
function func_payment_get_void_func($paymentid)
{
    global $sql_tbl, $active_modules;

    if (strpos($processor_file, 'ps_paypal') === 0) {
        x_load('paypal');
    }

    $processor_file = func_query_first_cell("SELECT processor_file FROM $sql_tbl[payment_methods] WHERE paymentid = '" . $paymentid . "'");
    if ($processor_file == 'ps_paypal_pro.php') {

        $ids = func_query_column("SELECT paymentid FROM $sql_tbl[payment_methods] WHERE processor_file = '$processor_file'");
        $processor = func_query_first_cell("SELECT processor FROM $sql_tbl[ccprocessors] WHERE paymentid IN ('" . implode("','", $ids) . "')");

        if ($active_modules['XPayments_Connector']) {
            func_xpay_func_load();

            if (xpc_is_emulated_paypal($paymentid)) {
                return xpc_can_void($paymentid);
            }
        }

    } elseif ($processor_file == 'cc_xpc.php') {

        if (!$active_modules['XPayments_Connector']) {
            return false;
        }

        func_xpay_func_load();

        return xpc_can_void($paymentid);

    } else {
        $processor = func_query_first_cell("SELECT processor FROM $sql_tbl[ccprocessors] WHERE paymentid = '" . $paymentid . "'");
    }

    if (!$processor)
        return false;

    $payment_name = preg_replace("/\.php/Ss", '', $processor);

    $func_name = 'func_' . $payment_name .'_do_void';

    func_pm_load($payment_name);

    return function_exists($func_name) ? $func_name : false;
}

/**
 * Get function name for Refund transaction
 */
function func_payment_get_refund_func($paymentid)
{
    global $sql_tbl, $active_modules;

    $processor_file = func_query_first_cell("SELECT processor_file FROM $sql_tbl[payment_methods] WHERE paymentid = '" . $paymentid . "'");
    $processor = false;
    if ($processor_file == 'ps_paypal_pro.php') {

        if ($active_modules['XPayments_Connector']) {
            func_xpay_func_load();

            if (xpc_is_emulated_paypal($paymentid)) {
                return xpc_can_refund($paymentid);
            }
        }

    } elseif ($processor_file == 'cc_xpc.php') {

        if (!$active_modules['XPayments_Connector']) {
            return false;
        }

        func_xpay_func_load();

        return xpc_can_refund($paymentid);

    } else {
        $processor = func_query_first_cell("SELECT processor FROM $sql_tbl[ccprocessors] WHERE paymentid = '" . $paymentid . "'");
    }

    if (!$processor)
        return false;

    $payment_name = preg_replace("/\.php/Ss", '', $processor);

    $func_name = 'func_' . $payment_name .'_do_refund';

    func_pm_load($payment_name);

    return function_exists($func_name) ? $func_name : false;
}

/**
 * Get function name for Get refund transaction mode
 */
function func_payment_get_refund_mode($paymentid, $orderid)
{
    global $sql_tbl, $active_modules;

    $processor_file = func_query_first_cell("SELECT processor_file FROM $sql_tbl[payment_methods] WHERE paymentid = '" . $paymentid . "'");
    $return = false;
    $func_name = false;
    if ($processor_file == 'ps_paypal_pro.php') {

        if ($active_modules['XPayments_Connector']) {
            func_xpay_func_load();

            if (xpc_is_emulated_paypal($paymentid)) {
                $func_name = 'xpc_get_refund_mode';
            }
        }

    } elseif ($processor_file == 'cc_xpc.php') {

        if ($active_modules['XPayments_Connector']) {
            func_xpay_func_load();
            $func_name = 'xpc_get_refund_mode';
        }

    } else {
        $processor = func_query_first_cell("SELECT processor FROM $sql_tbl[ccprocessors] WHERE paymentid = '" . $paymentid . "'");
        $payment_name = preg_replace("/\.php/Ss", '', $processor);
        $func_name = 'func_' . $payment_name .'_get_refund_mode';
        func_pm_load($payment_name);
    }

    if ($func_name && function_exists($func_name)) {
        $return = $func_name($paymentid, $orderid);
    }

    return $return;
}

/**
 * Get function name for Accept transaction
 */
function func_payment_get_accept_func($paymentid)
{
    global $sql_tbl, $active_modules;

    $processor_file = func_query_first_cell("SELECT processor_file FROM $sql_tbl[payment_methods] WHERE paymentid = '" . $paymentid . "'");

    if (strpos($processor_file, 'ps_paypal') === 0) {
        x_load('paypal');
    }

    if ($processor_file == 'ps_paypal_pro.php') {
        $ids = func_query_column("SELECT paymentid FROM $sql_tbl[payment_methods] WHERE processor_file = '$processor_file'");
        $processor = func_query_first_cell("SELECT processor FROM $sql_tbl[ccprocessors] WHERE paymentid IN ('" . implode("','", $ids) . "')");

        if ($active_modules['XPayments_Connector']) {
            func_xpay_func_load();

            if (xpc_is_emulated_paypal($paymentid)) {
                return xpc_can_accept($paymentid);
            }
        }

    } elseif ($processor_file == 'cc_xpc.php') {

        if (!$active_modules['XPayments_Connector']) {
            return false;
        }

        func_xpay_func_load();

        return xpc_can_accept($paymentid);

    } else {
        $processor = func_query_first_cell("SELECT processor FROM $sql_tbl[ccprocessors] WHERE paymentid = '" . $paymentid . "'");
    }

    if (!$processor)
        return false;

    $payment_name = preg_replace("/\.php/Ss", '', $processor);

    $func_name = 'func_' . $payment_name .'_do_accept';

    func_pm_load($payment_name);

    return function_exists($func_name) ? $func_name : false;
}

/**
 * Get function name for Decline transaction
 */
function func_payment_get_decline_func($paymentid)
{
    global $sql_tbl, $active_modules;

    $processor_file = func_query_first_cell("SELECT processor_file FROM $sql_tbl[payment_methods] WHERE paymentid = '" . $paymentid . "'");

    if (strpos($processor_file, 'ps_paypal') === 0) {
        x_load('paypal');
    }

    if ($processor_file == 'ps_paypal_pro.php') {
        $ids = func_query_column("SELECT paymentid FROM $sql_tbl[payment_methods] WHERE processor_file = '$processor_file'");
        $processor = func_query_first_cell("SELECT processor FROM $sql_tbl[ccprocessors] WHERE paymentid IN ('" . implode("','", $ids) . "')");

        if ($active_modules['XPayments_Connector']) {
            func_xpay_func_load();

            if (xpc_is_emulated_paypal($paymentid)) {
                return xpc_can_decline($paymentid);
            }
        }

    } elseif ($processor_file == 'cc_xpc.php') {

        if (!$active_modules['XPayments_Connector']) {
            return false;
        }

        func_xpay_func_load();

        return xpc_can_decline($paymentid);

    } else {
        $processor = func_query_first_cell("SELECT processor FROM $sql_tbl[ccprocessors] WHERE paymentid = '" . $paymentid . "'");
    }

    if (!$processor)
        return false;

    $payment_name = preg_replace("/\.php/Ss", '', $processor);

    $func_name = 'func_' . $payment_name .'_do_decline';

    func_pm_load($payment_name);

    return function_exists($func_name) ? $func_name : false;
}

/**
 * Get function name for GetInfo transaction
 */
function func_payment_get_get_info_func($paymentid)
{
    global $sql_tbl, $active_modules;

    $processor_file = func_query_first_cell("SELECT processor_file FROM $sql_tbl[payment_methods] WHERE paymentid = '" . $paymentid . "'");

    if (strpos($processor_file, 'ps_paypal') === 0) {
        x_load('paypal');
    }

    if ($processor_file == 'ps_paypal_pro.php') {
        $ids = func_query_column("SELECT paymentid FROM $sql_tbl[payment_methods] WHERE processor_file = '$processor_file'");
        $processor = func_query_first_cell("SELECT processor FROM $sql_tbl[ccprocessors] WHERE paymentid IN ('" . implode("','", $ids) . "')");

        if ($active_modules['XPayments_Connector']) {
            func_xpay_func_load();

            if (xpc_is_emulated_paypal($paymentid)) {
                return xpc_can_get_info($paymentid);
            }
        }

    } elseif ($processor_file == 'cc_xpc.php') {

        if (!$active_modules['XPayments_Connector']) {
            return false;
        }

        func_xpay_func_load();

        return xpc_can_get_info($paymentid);

    } else {
        $processor = func_query_first_cell("SELECT processor FROM $sql_tbl[ccprocessors] WHERE paymentid = '" . $paymentid . "'");
    }

    if (!$processor)
        return false;

    $payment_name = preg_replace("/\.php/Ss", '', $processor);

    $func_name = 'func_' . $payment_name .'_do_get_info';

    func_pm_load($payment_name);

    return function_exists($func_name) ? $func_name : false;
}

/**
 * Get service message for payment methods with Authorize and without Capture/Void
 */
function func_payment_get_non_capture_message($paymentid)
{
    global $sql_tbl;

    $processor = func_query_first_cell("SELECT processor FROM $sql_tbl[ccprocessors] WHERE paymentid = '" . $paymentid . "'");
    if (!$processor)
        return false;

    $lbl_name = 'lbl_' . preg_replace("/\.php/Ss", '', $processor).'_non_capture_message';

    return func_get_langvar_by_name($lbl_name, array(), false, true);
}

/**
 * Do Capture transaction
 */
function func_payment_do_capture($orderid)
{
    global $sql_tbl;

    $order = func_order_data($orderid);
    if (!$order) {
        return array(
            'type' => 'E',
            'content' => func_get_langvar_by_name('lbl_order_not_found')
        );
    }

    $func_name = func_payment_get_capture_func($order['order']['paymentid']);
    if (!$func_name) {
        return array(
            'type' => 'E',
            'content' => func_get_langvar_by_name('lbl_payment_module_internal_error')
        );
    }

    x_load('order');

    $order['order']['total'] = $order['order']['capture_full_total'];
    if (!func_check_capture_limit(intval($orderid))) {
        $order['order']['total'] = $order['order']['init_total'];
        $override_total = $order['order']['init_total'];
    }

    list($ret, $err_msg, $extra) = $func_name($order);

    func_store_advinfo($orderid, $err_msg);

    if ($ret === true || (is_int($ret) && $ret > 0)) {
        x_load('order');
        $orderids = func_get_order_ids($orderid);

        func_order_process_capture($orderids);

        func_array2insert(
            'order_extras',
            array(
                'orderid' => $orderid,
                'khash' => 'captured_total',
                'value' => $order['order']['total']
            ),
            true
        );

    }

    if (($ret === true || (is_int($ret) && $ret > 0)) && !$err_msg) {

        if (is_int($ret)) {
            if ($ret & X_PAYMENT_TRANS_ALREADY_CAPTURED) {
                return array(
                    'content' => func_get_langvar_by_name('lbl_payment_trans_already_captured')
                );
            }
        }

        if (isset($override_total)) {
            return array(
                'content' => func_get_langvar_by_name('lbl_payment_capture_successfully_differ', array('captured_total' => $override_total))
            );
        }

        return array(
            'content' => func_get_langvar_by_name('lbl_payment_capture_successfully')
        );
    }

    if ($ret === 'Q') {
        x_load('order');
        $orderids = func_get_order_ids($orderid);

        define('STATUS_CHANGE_REF', 5);
        func_change_order_status($orderids, 'Q');
    }

    return array(
        'type' => 'E',
        'content' => $err_msg ? func_get_langvar_by_name('lbl_payment_capture_error', array('error_message' => $err_msg)) : func_get_langvar_by_name('lbl_payment_module_internal_error')
    );
}

/**
 * Do Void transaction
 */
function func_payment_do_void($orderid, $process_mode = 0)
{
    global $sql_tbl;

    $order = func_order_data($orderid);
    if (!$order) {
        return array(
            'type' => 'E',
            'content' => func_get_langvar_by_name('lbl_order_not_found')
        );
    }

    $func_name = func_payment_get_void_func($order['order']['paymentid']);
    if (!$func_name) {
        return array(
            'type' => 'E',
            'content' => func_get_langvar_by_name('lbl_payment_module_internal_error')
        );
    }

    list($ret, $err_msg, $extra) = $func_name($order);

    func_store_advinfo($orderid, $err_msg);

    if ($ret === true || (is_int($ret) && $ret > 0)) {
        x_load('order');
        $orderids = func_get_order_ids($orderid);

        func_order_process_void($orderids, $process_mode);
    }

    if (($ret === true || (is_int($ret) && $ret > 0)) && !$err_msg) {
        if (is_int($ret)) {
            if ($ret & X_PAYMENT_TRANS_ALREADY_VOIDED) {
                return array(
                    'content' => func_get_langvar_by_name('lbl_payment_trans_already_declined')
                );
            }
        }

        return array(
            'content' => func_get_langvar_by_name('lbl_payment_void_successfully')
        );
    }

    return array(
        'type' => 'E',
        'content' => $err_msg ? func_get_langvar_by_name('lbl_payment_void_error', array('error_message' => $err_msg)) : func_get_langvar_by_name('lbl_payment_module_internal_error')
    );
}

/**
 * Do Refund transaction
 */
function func_payment_do_refund($orderid, $total = null)
{
    global $sql_tbl;

    $order = func_order_data($orderid);
    if (!$order) {
        return array(
            'type' => 'E',
            'content' => func_get_langvar_by_name('lbl_order_not_found')
        );
    }

    if (!in_array($order['order']['charge_info']['refund_mode'], array('Y', 'P'))) {
        return array(
            'type' => 'E',
            'content' => func_get_langvar_by_name('lbl_payment_module_internal_error')
        );
    }

    $func_name = func_payment_get_refund_func($order['order']['paymentid']);
    if (!$func_name) {
        return array(
            'type' => 'E',
            'content' => func_get_langvar_by_name('lbl_payment_module_internal_error')
        );
    }

    if ($order['order']['charge_info']['refund_avail'] <= 0) {
        return array(
            'type' => 'E',
            'content' => func_get_langvar_by_name('txt_order_is_totally_refunded')
        );
    }

    if (!is_null($total)) {
        $total = doubleval($total);
        if ($order['order']['charge_info']['refund_mode'] == 'Y') {
            return array(
                'type' => 'E',
                'content' => func_get_langvar_by_name('txt_payment_module_can_only_full_refund')
            );

        } elseif ($total > $order['order']['charge_info']['refund_avail']) {
            return array(
                'type' => 'E',
                'content' => func_get_langvar_by_name('txt_refund_amount_bigger_charged_total')
            );

        } elseif ($total < 0) {
            return array(
                'type' => 'E',
                'content' => func_get_langvar_by_name('txt_refund_amount_has_small_value')
            );
        }
    }

    list($ret, $err_msg, $extra) = $func_name($order, $total);

    func_store_advinfo($orderid, $err_msg);

    if ($ret === true || (is_int($ret) && $ret > 0)) {
        x_load('order');
        $orderids = func_get_order_ids($orderid);

        $total = is_null($total) ? $order['order']['charge_info']['refund_avail'] : $total;

        func_order_process_refund($orderids, $total, $extra);
    }

    if (($ret === true || (is_int($ret) && $ret > 0)) && !$err_msg) {

        if (is_int($ret)) {
            if ($ret & X_PAYMENT_TRANS_ALREADY_REFUNDED) {
                return array(
                    'content' => func_get_langvar_by_name('lbl_payment_trans_already_refunded')
                );
            }
        }

        return array(
            'content' => func_get_langvar_by_name('lbl_payment_refund_successfully')
        );
    }

    if ($ret === 'Q') {
        x_load('order');
        $orderids = func_get_order_ids($orderid);

        define('STATUS_CHANGE_REF', 11);
        func_change_order_status($orderids, 'Q');
    }

    return array(
        'type' => 'E',
        'content' => $err_msg ? func_get_langvar_by_name('lbl_payment_refund_error', array('error_message' => $err_msg)) : func_get_langvar_by_name('lbl_payment_module_internal_error')
    );
}

/**
 * Do Accept transaction
 */
function func_payment_do_accept($orderid)
{
    global $sql_tbl;

    $order = func_order_data($orderid);
    if (!$order) {
        return array(
            'type' => 'E',
            'content' => func_get_langvar_by_name('lbl_order_not_found')
        );
    }

    $func_name = func_payment_get_accept_func($order['order']['paymentid']);
    if (!$func_name) {
        return array(
            'type' => 'E',
            'content' => func_get_langvar_by_name('lbl_payment_module_internal_error')
        );
    }

    list($ret, $err_msg, $extra) = $func_name($order);

    func_store_advinfo($orderid, $err_msg);

    if ($ret === true || (is_int($ret) && $ret > 0)) {
        x_load('order');
        $orderids = func_get_order_ids($orderid);

        func_order_process_accept($orderids);

        if (is_int($ret)) {
            if ($ret & X_PAYMENT_TRANS_ALREADY_ACCEPTED) {
                return array(
                    'content' => func_get_langvar_by_name('lbl_payment_trans_already_accepted')
                );
            }
        }

        return array(
            'content' => func_get_langvar_by_name('lbl_payment_accept_successfully')
        );
    }

    return array(
        'type' => 'E',
        'content' => $err_msg ? func_get_langvar_by_name('lbl_payment_accept_error', array('error_message' => $err_msg)) : func_get_langvar_by_name('lbl_payment_module_internal_error')
    );
}

/**
 * Do Decline transaction
 */
function func_payment_do_decline($orderid)
{
    global $sql_tbl;

    $order = func_order_data($orderid);
    if (!$order) {
        return array(
            'type' => 'E',
            'content' => func_get_langvar_by_name('lbl_order_not_found')
        );
    }

    $func_name = func_payment_get_decline_func($order['order']['paymentid']);
    if (!$func_name) {
        return array(
            'type' => 'E',
            'content' => func_get_langvar_by_name('lbl_payment_module_internal_error')
        );
    }

    list($ret, $err_msg, $extra) = $func_name($order);

    func_store_advinfo($orderid, $err_msg);

    if ($ret === true || (is_int($ret) && $ret > 0)) {
        x_load('order');
        $orderids = func_get_order_ids($orderid);

        func_order_process_decline($orderids);

        if (is_int($ret)) {
            if ($ret & X_PAYMENT_TRANS_ALREADY_DECLINE) {
                return array(
                    'content' => func_get_langvar_by_name('lbl_payment_trans_already_declined')
                );
            }
        }

        return array(
            'content' => func_get_langvar_by_name('lbl_payment_decline_successfully')
        );
    }

    return array(
        'type' => 'E',
        'content' => $err_msg ? func_get_langvar_by_name('lbl_payment_decline_error', array('error_message' => $err_msg)) : func_get_langvar_by_name('lbl_payment_module_internal_error')
    );
}

/**
 * Do GetInfo transaction
 */
function func_payment_do_get_info($orderid)
{
    global $sql_tbl;

    $order = func_order_data($orderid);
    if (!$order) {
        return array(
            'type' => 'E',
            'content' => func_get_langvar_by_name('lbl_order_not_found')
        );
    }

    $func_name = func_payment_get_get_info_func($order['order']['paymentid']);
    if (!$func_name) {
        return array(
            'type' => 'E',
            'content' => func_get_langvar_by_name('lbl_payment_module_internal_error')
        );
    }

    list($ret, $err_msg, $extra, $data) = $func_name($order);

    func_store_advinfo($orderid, $err_msg);

    if ($ret) {
        x_load('order');
        $orderids = func_get_order_ids($orderid);

        func_order_process_get_info($orderids, $data);

        return array(
            'content' => func_get_langvar_by_name('lbl_payment_get_info_successfully')
        );
    }

    return array(
        'type' => 'E',
        'content' => $err_msg ? func_get_langvar_by_name('lbl_payment_get_info_error', array('error_message' => $err_msg)) : func_get_langvar_by_name('lbl_payment_module_internal_error')
    );
}

/**
 * Get capture total limits by processor name
 */
function func_get_capture_limits($processor)
{
    $module_params = func_get_pm_params($processor);
    if (!$module_params)
        return array(false, false);

    foreach (array('capture_min_limit', 'capture_max_limit') as $fn) {
        if (preg_match('/^[\d\.]+%$/', $module_params[$fn])) {
            $module_params[$fn] = doubleval(substr($module_params[$fn], 0, -1)) . '%';

        } elseif (preg_match('/^[\d\.]+$/', $module_params[$fn])) {
            $module_params[$fn] = doubleval($module_params[$fn]);

        } else {
            $module_params[$fn] = '0%';
        }
    }

    return array($module_params['capture_min_limit'], $module_params['capture_max_limit']);
}

/**
 * Check capture transaction by limits
 */
function func_check_capture_limit($orderid)
{
    global $sql_tbl;

    if (!is_int($orderid) || $orderid < 1)
        return false;

    $order = func_query_first("SELECT paymentid, total, init_total FROM $sql_tbl[orders] WHERE orderid = '" . $orderid . "'");
    if (!$order || !$order['paymentid'])
        return false;

    $processor = func_query_first_cell("SELECT processor_file FROM $sql_tbl[payment_methods] WHERE paymentid = '" . $order['paymentid'] . "'");
    if (!$processor)
        return false;

    if ($order['total'] <= 0)
        return false;

    if ($order['init_total'] == $order['total'])
        return true;

    // Check by processor function
    func_pm_load($processor);

    $function_name = 'func_' . preg_replace("/\.php/Ss", "", $processor) . '_check_capture_limit';
    if (function_exists($function_name))
        return $function_name($order['init_total'], $order['total']);

    // Check by porcessor limits
    list($min, $max) = func_get_capture_limits($processor);
    if ($min === false)
        return false;

    if (preg_match('/%$/S', $min))
        $min = $order['init_total'] * substr($min, 0, -1) / 100;
    else
        $min = $order['init_total'] - $min;

    if (preg_match('/%$/S', $max))
        $max = $order['init_total'] * substr($max, 0, -1) / 100 + $order['init_total'];
    else
        $max = $order['init_total'] + $max;

    return $order['total'] >= round($min, 2) && $order['total'] <= round($max, 2);
}

/**
 * Log payment processing error
 */
function func_pp_error_log($msg)
{
    global $login;

    return x_log_flag(
        'log_payment_processing_errors',
        'PAYMENTS',
        "Payment processing failure.\nLogin: " . $login . "\nIP: " . $_SERVER['REMOTE_ADDR'] . "\n----\n" . $msg,
        true
    );
}

/**
 * Get payment module currencies list
 */
function func_pm_get_currencies($processor)
{
    global $sql_tbl;

    $processor = basename($processor);
    if (!func_pm_load($processor))
        return array();

    $payment_name = preg_replace("/\.php/Ss", '', $processor);
    $func_name = 'func_' . $payment_name . '_get_currencies';
    if (function_exists($func_name)) {
        return $func_name(func_query("SELECT * FROM $sql_tbl[currencies]"));
    }

    return array();
}

function func_is_pmethods_list_empty($payment_methods)
{
    $is_empty = true;
    foreach ($payment_methods as $pm) {
        if ($pm['active'] == 'Y' || $pm['processor']) {
            $is_empty = false;
            break;
        }
    }

    return $is_empty;
}

function func_is_complex_processor($processor)
{
    return in_array(
        $processor,
        array(
            'cc_2conew.php', 'cc_worldpay.php', 'cc_asp.php', 'cc_anz.php', 'cc_anz_mh.php',
            'cc_bean.php', 'cc_blue.php', 'cc_caledon.php', 'cc_echo.php', 'cc_goem.php',
            'cc_goem_pf.php', 'cc_goem_xml.php', 'cc_ideal_rb_prof.php', 'cc_ideal_basic.php', 'cc_ideala.php',
            'cc_hsbc.php', 'cc_hsbc_xml.php', 'cc_isecure.php', 'cc_processusa.php', 'cc_mbookers.php',
            'cc_mbookers_wlt.php', 'cc_multicard.php', 'cc_netbanx.php', 'ps_nochex.php', 'cc_ogone.php',
            'cc_ogoneweb.php', 'cc_plugnpaycom.php', 'cc_pnpss.php', 'cc_pp3.php', 'cc_psigate.php',
            'cc_psigate_xml.php', 'cc_securepay.php', 'cc_securetrading.php', 'cc_paypointft.php', 'cc_securetrading.php',
            'cc_csrc_form.php', 'cc_csrc_soap.php', 'cc_nab_transact.php'
        )
    );
}

function func_is_selected_gateway($processor)
{
    return in_array(
        $processor,
        array(
            'cc_payflow_pro.php'
        )
    );
}

function func_decode_processor_code($processor)
{
    $pm_id = false;

    if (preg_match("/cc_xpc\.php_([0-9]+)/", $processor, $match)) {
        $pm_id = intval($match[1]);
        $processor = 'cc_xpc.php';
    }

    return array($processor, $pm_id);
}

function func_get_processor_by_id($processor)
{
    global $sql_tbl;

    list($processor, $pm_id) = func_decode_processor_code($processor);
    $processor_query = "processor = '" . $processor . "'";
    if ($pm_id) {
        $processor_query .= " AND param01 = '$pm_id'";
    }

    return func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE " . $processor_query);
}

function func_add_processor($processor, $orderby = 999)
{
    global $sql_tbl, $recent_payment_methods;

    x_session_register('recent_payment_methods');

    $tmp = func_get_processor_by_id($processor);

    if ($tmp['paymentid']) {
        return $tmp['paymentid'];
    }

    $cc_processor = $tmp['module_name'];
    $processor = $tmp['processor'];
    $background = $tmp['background'];

    if (!zerolen($tmp['c_template'])) {
        $template = $tmp['c_template'];

    } else {
        $type_2_template = array (
            'C' => 'customer/main/payment_cc.tpl',
            'D' => 'customer/main/payment_dd.tpl',
            'H' => 'customer/main/payment_chk.tpl'
        );
        $template = get_value($type_2_template, $tmp['type'], 'customer/main/payment_offline.tpl');
    }

    $insert_params = array (
        'payment_method' => $cc_processor,
        'payment_script' => 'payment_cc.php',
        'payment_template' => $template,
        'active' => 'N',
        'orderby' => $orderby,
        'processor_file' => $processor
    );

    if ($processor == 'cc_xpc.php') {
        $insert_params['protocol'] = 'https';
    }

    $paymentid = func_array2insert('payment_methods', $insert_params);

    $processor_query = "processor = '" . $processor . "'";
    if ($processor == 'cc_xpc.php') {
        $processor_query .= " AND param01 = '" . $tmp['param01'] . "'";
    }

    func_array2update('ccprocessors', array('paymentid' => $paymentid), $processor_query);

    $name = func_query_first_cell("SELECT payment_method FROM $sql_tbl[payment_methods] WHERE paymentid='$paymentid'");

    if ($processor == 'ps_paypal.php') {

        $name = 'PayPal';

        // Paypal
        $tmp = func_query_first("SELECT * from $sql_tbl[ccprocessors] WHERE processor='ps_paypal_pro.php'");
        $cc_processor = $tmp['module_name'];

        // PayPal ExpressCheckout
        $insert_params['payment_method'] = $cc_processor . ': ExpressCheckout';
        $insert_params['processor_file'] = 'ps_paypal_pro.php';
        $_paymentid = func_array2insert('payment_methods', $insert_params);
        func_array2update('ccprocessors', array('paymentid' => $_paymentid), "processor = 'ps_paypal_pro.php'");

        // PayPal DirectPayment
        $insert_params['payment_template'] = 'customer/main/payment_cc.tpl';
        $insert_params['payment_method'] = $cc_processor . ': DirectPayment';
        func_array2insert('payment_methods', $insert_params);
    }

    $recent_payment_methods[$processor.$paymentid] = array(
        'script' => $processor,
        'name' => $name,
        'paymentid' => $paymentid,
        'background' => $tmp["background"]
    );

    return $paymentid;
}

/**
 * Write debug data to log
 *
 * @param string $prefix    log filename prefix
 * @param string $action    action
 * @param array  $data      debug data
 *
 * @return void
 * @access public
 * @see    ____func_see____
 */
function func_pp_debug_log($prefix = 'payment', $action = 'I', $data = array())
{

    if (empty($data))
        return;

    switch ($action) {
        case 'I':
            $log_msg = 'Payment request:';
            break;
        case 'C':
            $log_msg = 'Callback received:';
            break;
        case 'R':
            $log_msg = 'Response received:';
            break;
        case 'F':
            $log_msg = 'Inline frame params:';
            break;
        default:
            $log_msg = 'Request data:';
    }

    ob_start();
    print_r($data);
    $log_msg .= "\n" . ob_get_contents();
    ob_end_clean();

    x_log_add($prefix, $log_msg);
}

?>
