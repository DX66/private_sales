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
 * X-Cart test functions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.tests.php,v 1.32.2.2 2011/01/10 13:11:52 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load('files');

// PayPal internal error codes
define('X_PAYPAL_ERR_EMPTY', 1);
define('X_PAYPAL_ERR_EC_EMAIL', 2);
define('X_PAYPAL_ERR_WPP_API_UN', 4);
define('X_PAYPAL_ERR_WPP_API_PWD', 8);
define('X_PAYPAL_ERR_WPP_SIG', 16);
define('X_PAYPAL_ERR_WPP_CERT', 32);
define('X_PAYPAL_ERR_WPPPE_VENDOR', 64);
define('X_PAYPAL_ERR_WPPPE_PARTNER', 128);
define('X_PAYPAL_ERR_WPPPE_USER', 256);
define('X_PAYPAL_ERR_WPPPE_PWD', 512);

/**
 * Function to test Perl presence
 */
function test_perl($details = false)
{
    global $config;
    $perl_binary = func_find_executable('perl', $config['General']['perl_binary']);
    if( $perl_binary ) {
        $fn = func_temp_store('print $];');
        if (empty($fn)) return '';
        $tmpfn = func_temp_store('');

        @exec(func_shellquote($perl_binary)." < ".func_shellquote($fn)." 2>".func_shellquote($tmpfn), $output);
        @unlink($fn);
        @unlink($tmpfn);
        if (!empty($output[0])) {
            if ($details) {
                @exec(func_shellquote($perl_binary)." -V 2>".func_shellquote($tmpfn), $output);
                @unlink($tmpfn);
                return implode("<br />", $output);
            }
            return $output[0];
        }
    }
    return '';
}

/**
 * Function to test Perl Net:SSLeay module presence
 */
function test_ssleay()
{
    global $xcart_dir;
    global $config;

    $perl_binary = func_find_executable('perl', $config['General']['perl_binary']);
    if ($perl_binary) {
        $script = "require Net::SSLeay; Net::SSLeay->import(qw(get_https post_https sslcat make_headers make_form)); print Net::SSLeay->VERSION;";
        $fn = func_temp_store($script);
        if (empty($fn)) return '';

        $tmpfn = func_temp_store('');
        $includes  = " -I".func_shellquote($xcart_dir.'/payment');
        $includes .= " -I".func_shellquote($xcart_dir.'/payment/Net');
        @exec(func_shellquote($perl_binary).' '.$includes." -w < ".func_shellquote($fn)." 2>".func_shellquote($tmpfn), $output);
        @unlink($fn);
        @unlink($tmpfn);

        if (!empty($output))
            return $output[0];
    }
    return '';
}

/**
 * Function to test libCURL module presence
 */
function test_libcurl()
{
    if (function_exists('curl_init')) {
        $info = curl_version();
        if (is_array($info)) {
            if (in_array('https', $info['protocols']))
                return $info['version'];
        }
        elseif (stristr($info,'ssl') || stristr($info,'tls')) {
            return $info;
        }
    }
    return '';
}

/**
 * Function to test CURL executable presence
 */
function test_curl()
{
    $curl = func_find_executable('curl');
    if ($curl) {
        @exec(func_shellquote($curl)." --version", $output);
        if (!empty($output) && stristr($output[0],'ssl') && preg_match("/^curl\s+([\d\.]+)/", $output[0], $match)) {
            $tmp = explode('.', $match[1]);
            if ($tmp[0] > 6 && ($tmp[1] > 9 || ($tmp[1] == 9 && intval($tmp[2]) > 0)))
                return $output[0];
        }
    }
    return '';
}

/**
 * Function to test OpenSSL module presence
 */
function test_openssl()
{
    $bin = func_find_executable('openssl');
    if( $bin )
        return @exec(func_shellquote($bin)." version");
    return '';
}

/**
 * This function selects which https module to use
 */
function test_active_bouncer($force=false)
{
    global $config;
    global $var_dirs;
    static $module_active = null;

    if (!$force && !is_null($module_active))
        return $module_active;

    $bouncers = array ('libcurl', 'curl', 'openssl', 'ssleay');

    if ($config['General']['httpsmod'])
        array_unshift($bouncers, $config['General']['httpsmod']);

    $result = false;
    foreach ($bouncers as $k => $bouncer ){
        $fn = "test_$bouncer";
        if (function_exists($fn) && $fn()) {
            $result = $bouncer;
            break;
        }
    }

    $old_module = false;
    $data_file = $var_dirs['log'].'/data.httpsmodule.php';
    if (file_exists($data_file)) {
        ob_start();
        readfile($data_file);
        $old_module = ob_get_contents();
        ob_end_clean();
        $old_module = substr($old_module, strlen(X_LOG_SIGNATURE));
    }

    if (!empty($old_module) && strcmp($old_module, $result)) {
        x_log_add('ENV', "HTTPS module is changed to: $result (was: $old_module)");
    }

    if ($old_module === false || strcmp($old_module, $result)) {
        $_tmp_fp = @fopen($data_file, 'wb');
        if ($_tmp_fp !== false) {
            @fwrite($_tmp_fp, X_LOG_SIGNATURE.$result);
            @fclose($_tmp_fp);
            func_chmod_file($data_file);
        }
    }

    $module_active = $result;

    return $result;
}

/**
 * Function to test EXPAT module presence
 */
function test_expat()
{
    return function_exists('xml_parser_create') ? 'found' : '';
}

/**
 * This function tests the requirements of payment methods.
 * It will disable the method, if its requirements aren't fulfilled.
 */
function test_payment_methods($methods, $hide_disfunctional=false)
{
    global $sql_tbl, $config, $xcart_dir, $httpsmod_active;

    if (!is_array($methods))
        return '';

    $result = array();

    foreach ($methods as $index=>$method) {
        $is_down = false;
        $in_testmode = false;

        if ($method['processor']) {
            $rc = test_ccprocessor(func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor='".$method["processor"]."'"));
            $is_down = !$rc['status'];
            if ($is_down && $hide_disfunctional)
                continue;

            $in_testmode = $rc['in_testmode'];
        }

        $method['is_down'] = $is_down;
        $method['in_testmode'] = $in_testmode;
        if ($is_down) {
            $lbl = false;

            if ($rc['failed_func'] == 'httpsmod') {
                $lbl = func_get_langvar_by_name('err_HTTPS_module', array(), false, true);

            } elseif ($method['processor'] == 'ps_paypal_pro.php') {

                $err_code = test_paypal_pro(func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor='ps_paypal_pro.php'"), true);
                if ($err_code & X_PAYPAL_ERR_WPP_CERT) {
                    $lbl = func_get_langvar_by_name('lbl_'.str_replace('.php', '', $method['processor']).'_requirement_failed_c', array(), false, true);
                } elseif ($err_code & X_PAYPAL_ERR_WPP_SIG) {
                    $lbl = func_get_langvar_by_name('lbl_'.str_replace('.php', '', $method['processor']).'_requirement_failed_s', array(), false, true);
                }

            } else {
                $lbl = func_get_langvar_by_name('lbl_'.str_replace('.php', '', $method['processor']).'_requirement_failed', array(), false, true);
            }

            if (!empty($lbl))
                $method['down_lbl'] = $lbl;
        }
        $result[] = $method;
    }

    return $result;
}

/**
 * This function tests the requirements of group of CC processors.
 */
function test_ccprocessors($ccprocessors, $hide_disfunctional=false)
{
    $result = '';
    foreach ($ccprocessors as $index=>$processor) {
        $result = test_ccprocessor($processor);

        $processor['is_down'] = !$result['status'];
        if (!$is_down || !$hide_disfunctional)
            $result[] = $processor;
    }
    return $result;
}

// This function tests the requirements of single CC processor.
/**
 * Note:
 * if file $xcart_dir.'/payment/test.'$module_params['processor'] is found, it should define the following variables:
 *     $good ::= true | false
 *     $requirement ::= testfunc | testexec | httpsmod
 *     $param = param of failed requirement
 */
function test_ccprocessor($module_params)
{
    global $httpsmod_active;
    global $xcart_dir;
    $good = true;

    if (empty($module_params)) return array('status'=>true,'in_testmode'=>false);

    if (!isset($httpsmod_active) || is_null($httpsmod_active)) {
        $httpsmod_active = test_active_bouncer();
    }

    $in_testmode = get_cc_in_testmode($module_params);

    $ptest_script = $xcart_dir . '/payment/test.' . basename($module_params['processor']);
    if (
        file_exists($ptest_script)
        && is_readable($ptest_script)
    ) {
        include $ptest_script;
    }
    else {
        $requirements = get_ccrequirements($module_params);

        if (empty($requirements))
            return array('status'=>true,'in_testmode'=>$in_testmode);

        foreach($requirements as $requirement=>$param) {
            switch($requirement) {
            case 'testfunc': $good = $good && $param($module_params); break;
            case 'testexec': $good = $good && func_is_executable($param); break;
            case 'httpsmod': $good = $good && !empty($httpsmod_active); break;
            }

            if (!$good) break;
        }
    }

    return array('status'=>$good,'failed_func'=>$requirement,'failed_param'=>$param,'in_testmode'=>$in_testmode);
}

// This function defines the requirements of CC processors
/**
 * Possible requirements:
 *   $result['testexec'] = 'filename' - try to execute specified executable
 *   $result['testfunc'] = "function name" - try to execute specified function
 *   $result['httpsmod'] = true - processor depends on https modules
 */
// Note: if file $xcart_dir.'/payment/req.'$module_params['processor'] is found, it should define the $result variable.

function get_ccrequirements($module_params)
{
    global $sql_tbl, $xcart_dir, $httpsmod_active;
    global $config;

    if (empty($module_params) || empty($module_params['processor'])) return array(true,'');

    $result = '';

    $preq_script = $xcart_dir . '/payment/req.' . basename($module_params['processor']);

    if (
        file_exists($preq_script)
        && is_readable($preq_script)
    ) {
        include $preq_script;

    } else {

        switch ($module_params['processor']) {
            case 'ch_wtsbank.php':
            case 'ch_authorizenet.php':
            case 'cc_epdq.php':
            case 'ps_paypal.php':
            case 'ps_nochex.php':
                $result['httpsmod'] = true;
                break;
            case 'ps_paypal_pro.php':
                $result['httpsmod'] = true;
                $result['testfunc'] = 'test_paypal_pro';
                break;
            case 'cc_payflow_pro.php':
                break;

            default:
                if ($module_params['background'] == 'Y') {
                    $result['httpsmod'] = true;
                }
        }
    }

    return $result;
}

function test_paypal_pro($module_params, $verbose = false)
{
    global $xcart_dir, $config;

    $result = 0;

    if (empty($module_params) || !is_array($module_params)) {
        $result |= X_PAYPAL_ERR_EMPTY;

    } elseif ($config['paypal_solution'] == 'express' && $config['paypal_express_method'] == 'email') {
        if (empty($config['paypal_express_email'])) {
            $result |= X_PAYPAL_ERR_EC_EMAIL;
        }

    } elseif ($config['paypal_solution'] == 'pro' || $config['paypal_solution'] == 'express') {

        if (empty($module_params['param01'])) {
            $result |= X_PAYPAL_ERR_WPP_API_UN;
        }

        if (empty($module_params['param02'])) {
            $result |= X_PAYPAL_ERR_WPP_API_PWD;
        }

        if ($module_params['param07'] == 'S') {
            // API Signature
            if (empty($module_params['param05'])) {
                $result |= X_PAYPAL_ERR_WPP_SIG;
            }

        } else {

            // API Certificate file
            $pp_cert_file = $xcart_dir.'/payment/certs/'.$module_params['param04'];
            if (empty($module_params['param04']) || !file_exists($pp_cert_file) || !is_file($pp_cert_file) || !is_readable($pp_cert_file)) {
                $result |= X_PAYPAL_ERR_WPP_CERT;
            }
        }

    } elseif ($config['paypal_solution'] == 'uk') {
        if (empty($module_params['param01'])) {
            $result |= X_PAYPAL_ERR_WPPPE_VENDOR;
        }

        if (empty($module_params['param02'])) {
            $result |= X_PAYPAL_ERR_WPPPE_PARTNER;
        }

        if (empty($module_params['param04'])) {
            $result |= X_PAYPAL_ERR_WPPPE_USER;
        }

        if (empty($module_params['param05'])) {
            $result |= X_PAYPAL_ERR_WPPPE_PWD;
        }
    }

    return $verbose ? $result : $result === 0;
}

/**
 * This function returns Live/Test mode status:
 * true then cc in test mode and false overwise.
 */
function get_cc_in_testmode($module_params)
{
    if (empty($module_params) || $module_params['processor']=="cc_test.php") return true;

    x_load('payment');

    $payment_name = preg_replace("/\.php/Ss", '', $module_params['processor']);
    func_pm_load($payment_name);
    $func_name = 'func_get_' . $payment_name .'_in_testmode';

    return function_exists($func_name) ? $func_name($module_params) : $module_params['testmode'] != 'N';
}

?>
