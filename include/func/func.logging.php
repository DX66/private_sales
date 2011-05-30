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
 * Logging functions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.logging.php,v 1.27.2.6 2011/02/07 15:34:45 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

define('X_LOG_SIGNATURE', '<'.'?php die(); ?'.">\n");
define('X_LOG_SIGNATURE_LENGTH', strlen(X_LOG_SIGNATURE));

function x_log_add($label, $message, $add_backtrace=false, $stack_skip=0, $email_addresses=false, $email_only=false)
{
    global $var_dirs;
    global $PHP_SELF;
    global $config;

    $filename = sprintf("%s/x-errors_%s-%s.php", $var_dirs['log'], strtolower($label), date('ymd'));

    if ($label == 'SQL')
        $type = 'error';
    elseif ($label == 'INI' || $label == 'SHIPPING')
        $type = 'warning';
    else
        $type = 'message';

    $uri = $PHP_SELF;
    if (!empty($_SERVER['QUERY_STRING'])) $uri .= '?'.$_SERVER['QUERY_STRING'];

    if ($add_backtrace) {
        $stack = func_get_backtrace(1+$stack_skip);
        $backtrace = "Backtrace:\n".implode("\n", $stack)."\n";
}
    else
        $backtrace = '';

    if (is_array($message) || is_object($message)) {
        ob_start();
        print_r($message);
        $message = ob_get_contents();
        ob_end_clean();
    } else {
        $message = trim($message);
    }

    $local_time = '';
    if (!empty($config)) {
        $local_time = "(shop: ".date('d-M-Y H:i:s', XC_TIME + $config['Appearance']['timezone_offset']).") ";
    }

    $message = str_replace("\n", "\n    ", "\n".$message);
    $message = str_replace("\t", "    ", $message);

    $data = sprintf("[%s] %s%s %s:%s\nRequest URI: %s\n%s-------------------------------------------------\n",
        date('d-M-Y H:i:s'),
        $local_time,
        $label, $type,
        $message,
        $uri,
        $backtrace
    );

    if (!$email_only && x_log_check_file($filename) !== false) {
        $fp = @fopen($filename, 'a');
        if ($fp !== false) {
            fwrite($fp, $data);
            fclose($fp);
            func_chmod_file($filename);
        }
    }

    if (!empty($email_addresses) && is_array($email_addresses)) {
        x_load('mail');

        foreach ($email_addresses as $k=>$email) {
            func_send_simple_mail(
                $email,
                $config['Company']['company_name'].": $label $type notification",
                $data, $config['Company']['site_administrator']);
        }
    }

    return $filename;
}

function x_log_flag($flag_key, $label, $message, $add_backtrace=false, $stack_skip=0)
{
    static $email_addresses = false;
    global $config;

    if ($email_addresses === false && isset($config['Logging']['email_addresses'])) {
        $email_addresses = array_unique(preg_split('/[;,\s]+/', $config['Logging']['email_addresses']));
    }

    $do_log =  empty($config);
    $addresses = false;
    $do_email = false;

    if (isset($config['Logging'][$flag_key])) {
        $value = $config['Logging'][$flag_key];
        $do_log = (strpos($value,'L') !== false);
        $do_email = (strpos($value,'E') !== false);
    }

    if ($do_email)
        $addresses = $email_addresses;

    if ($do_log || $do_email)
        x_log_add($label, $message, $add_backtrace, $stack_skip+1, $addresses, ($do_email && !$do_log));
}

// For testing purpose: set parameters of debugging functions
/**
 * Operations:
 * 'P' - display/not display debug messages
 */
function x_debug_ctl($operation, $arg=null)
{
    static $print_status = true;

    switch ($operation) {
        case 'P':
            if (is_null($arg))
                return $print_status;
            $print_status = $arg;
            return true;
    }

    return false;
}

function x_log_list_files($labels = false, $start=false, $end=false)
{
    global $var_dirs;

    $regexp = '/^x-errors_(' . func_login_validation_regexp() . ')-(\d{6})\.php$/S';

    $dp = @opendir($var_dirs['log']);
    if ($dp === false) return false;

    $start = ($start !== false && $start !== 0) ? (int)date('ymd', $start) : 0;

    $end = (int)date('ymd', ($end === false) ? XC_TIME + 86400 * 30 : $end);

    $return = array();

    if (!is_array($labels)) {
        if (!empty($labels))
            $labels = array (strtoupper($labels));
    }
    else {
        foreach ($labels as $k=>$v) {
            $labels[$k] = strtoupper($v);
        }
    }

    while ($file = readdir($dp)) {
        if (!preg_match($regexp, $file, $matches)) {
            continue;
        }

        $time_str = $matches[2];
        $ts = (int)$time_str;

        if ($ts < $start || $ts > $end) {
            continue;
        }

        $prefix = strtoupper($matches[1]);
        if ($labels !== false && is_array($labels) && !in_array($prefix, $labels)) {
            continue;
        }

        if (!isset($return[$prefix]))
            $return[$prefix] = array();

        $time_ts = mktime(0,0,0, substr($time_str,2,2), substr($time_str,4,2), substr($time_str,0,2));

        $return[$prefix][$time_ts] = $file;
    }

    foreach ($return as $prefix=>$data) {
        ksort($return[$prefix]);
    }

    return $return;
}

function x_log_get_contents($labels = false, $start=false, $end=false, $html_safe=false, $count=0)
{
    global $var_dirs;
    static $regexp = '!^\[\d{2}-.{3}-\d{4} \d{2}:\d{2}:\d{2}\] !S';

    $logs = x_log_list_files($labels, $start, $end);

    if (empty($logs)) return false;

    $logs_data = array();

    if ($count < 0) $count = 0;

    foreach ($logs as $label=>$data) {
        $contents = '';
        $records = array();
        foreach ($data as $ts=>$file) {
            $fp = @fopen($var_dirs['log'].'/'.$file, "rb");
            if ($fp !== false) {
                fseek($fp, X_LOG_SIGNATURE_LENGTH, SEEK_SET);
                $buffer = '';
                while (($line = fgets($fp, 8192)) !== false) {
                    if (!$count) {
                        $contents .= $line;
                        continue;
                    }

                    if (preg_match($regexp, $line)) {
                        if (!empty($buffer)) {
                            $records[] = $buffer;
                            if (count($records) > $count) array_splice($records, 0, -$count);
                        }

                        $buffer = $line;
                    }
                    else {
                        $buffer .= $line;
                    }
                }

                if (!empty($buffer)) {
                    $records[] = $buffer;
                    if (count($records) > $count) array_splice($records, 0, -$count);
                }

                fclose($fp);
            }
        }

        if (!empty($records)) {
            $contents .= implode('', $records);
            $records = false;
        }

        if ($html_safe) {
            $contents = htmlspecialchars($contents);
            $contents = str_replace('  ', '&nbsp; ', $contents);
        }

        if (!empty($contents)) {
            $logs_data[$label] = $contents;
        }
    }

    return $logs_data;
}

function x_log_count_messages($labels=false, $start=false, $end=false)
{
    global $var_dirs;
    static $regexp = '!^\[\d{2}-.{3}-\d{4} \d{2}:\d{2}:\d{2}\] !S';

    $logs = x_log_list_files($labels, $start, $end);

    if (!is_array($logs) || empty($logs))
        return false;

    $return = array();

    foreach ($logs as $label=>$list) {
        if (!is_array($list) || empty($list)) continue;

        foreach ($list as $timestamp=>$file) {
            // count records in single log file
            $fp = @fopen($var_dirs['log'].'/'.$file, 'r');
            if ($fp === false)
                continue;

            $count = 0;
            while (($line = fgets($fp, 8192)) !== false) {
                if (preg_match($regexp, $line)) $count++;
            }

            fclose($fp);

            $return[$label][$timestamp] = $count;
        }
    }

    return $return;
}

function x_log_get_names($labels=false, $force_output=false)
{
    static $all_labels = false;

    if ($all_labels === false) {
        $all_labels = array (
            'DATABASE' => 'lbl_log_database_operations',
            'FILES' => 'lbl_log_file_operations',
            'ORDERS' => 'lbl_log_orders_operations',
            'PRODUCTS' => 'lbl_log_products_operations',
            'SHIPPING' => 'lbl_log_shipping_errors',
            'PAYMENTS' => 'lbl_log_payment_errors',
            'PHP' => 'lbl_log_php_errors',
            'SQL' => 'lbl_log_sql_errors',
            'ENV' => 'lbl_log_env_changes',
            'DEBUG' => 'lbl_log_debug_messages',
            'DECRYPT' => 'lbl_decrypt_errors',
            'BENCH' => 'lbl_log_bench_reports',
            'HTTPS' => 'lbl_log_https_errors',
            'GD' => 'opt_log_gd_errors',
            'ACTIVITY' => 'opt_log_activity',
            'XSS' => 'opt_log_xss_attempts',
            'SNAPSHOT' => 'opt_log_snapshot'
        );
    }

    if ($force_output !== false && $force_output !== true)
        $force_output = false;

    $keys = array_keys($all_labels);
    if (empty($labels) || !is_array($labels))
        $labels = $keys;
    else {
        $labels = array_intersect($labels, $keys);
        if (empty($labels))
            $labels = $keys;
    }

    $result = array ();
    foreach ($labels as $label) {
        $result[$label] = func_get_langvar_by_name($all_labels[$label], NULL, false, $force_output);
    }

    return $result;
}

function x_log_check_file($filename)
{
    $fp = @fopen($filename, "a+");
    if ($fp === false)
        return false;

    if (func_filesize($filename) ==0) {
        @fwrite($fp, X_LOG_SIGNATURE);
        @fclose($fp);
        func_chmod_file($filename);
        if (!preg_match("/x-errors_log/", $filename)) {
            global $login, $REMOTE_ADDR;
            if (empty($REMOTE_ADDR))
                $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
            x_log_flag('log_log', 'LOG', "'$filename' logfile has been created".($login != "" ? " by '$login' user" : "").". remote ip '$REMOTE_ADDR'");
        }
        return $filename;
    }

    if (@fseek($fp, 0, SEEK_SET) < 0) {
        @fclose($fp);
        func_chmod_file($filename);
        return false;
    }

    $tmp = @fread($fp, X_LOG_SIGNATURE_LENGTH);
    if (strcmp($tmp, X_LOG_SIGNATURE)) {
        @fseek($fp, 0, SEEK_SET);
        @ftruncate($fp, 0);
        @fwrite($fp, X_LOG_SIGNATURE);
    }
    @fclose($fp);
    func_chmod_file($filename);

    return $filename;
}

function func_array_compare($orig, $new)
{
    $result = array (
        'removed' => false,
        'added' => false,
        'delta' => false,
        'changed' => false
    );

    $keys = array();
    if (is_array($orig)) $keys = array_keys($orig);
    if (is_array($new)) $keys = array_merge($keys, array_keys($new));
    $keys = array_unique($keys);

    foreach ($keys as $key) {
        $in_orig = isset($orig[$key]);
        $in_new = isset($new[$key]);

        if ($in_orig && !$in_new) {
            $result['removed'][$key] = $orig[$key];
        }
        elseif (!$in_orig && $in_new) {
            $result['added'][$key] = $new[$key];
        }
        else {
            // check for changed values
            if (!is_array($new[$key])) {
                if (!strcmp((string)$orig[$key], (string)$new[$key])) {
                    continue;
                }

                $is_numeric = preg_match('!^((\d+)|(\d+\.\d+))$!S', $new[$key]);

                if ($is_numeric) {
                    $result['delta'][$key] = $new[$key] - $orig[$key];
                }

                $result['changed'][$key] = $new[$key];
            }
            else {
                $tmp = func_array_compare($orig[$key],$new[$key]);

                foreach ($tmp as $tmp_key=>$tmp_value) {
                    if ($tmp_value === false) continue;

                    $result[$tmp_key][$key] = $tmp_value;
                }
            }
        }
    }

    // remove not used arrays
    foreach ($result as $k=>$v) {
        if ($v === false)
            unset($result[$k]);
    }

    return $result;
}

/**
 * Function to get backtrace for debugging
 */
function func_get_backtrace($skip=0)
{
    $result = array();
    if (!function_exists('debug_backtrace')) {
        $result[] = '[func_get_backtrace() is supported only for PHP version 4.3.0 or better]';
        return $result;
    }
    $trace = debug_backtrace();

    if (is_array($trace) && !empty($trace)) {
        if ($skip>0) {
            if ($skip < count($trace))
                $trace = array_splice($trace, $skip);
            else
                $trace = array();
        }

        foreach ($trace as $item) {
            if (!empty($item['file']))
                $result[] = $item['file'].':'.$item['line'];
        }
    }

    if (empty($result)) {
        $result[] = '[empty backtrace]';
    }

    return $result;
}

/**
 * Function to get backtrace for debugging HTML output
 */
function func_get_backtrace_html($max_string_length = 3000, $cmd_line = false)
{
    $output = "<div style='text-align: left; font-family: monospace;'>\n";
    $output .= "<b>Backtrace:</b><br />\n";
    $backtrace = debug_backtrace();

    foreach ($backtrace as $bt) {
        $args = '';
        foreach ($bt['args'] as $a) {
            if (!empty($args)) {
                $args .= ', ';
            }
            switch (gettype($a)) {
                case 'integer':
                case 'double':
                    $args .= $a;
                    break;
                case 'string':
                    $a = htmlspecialchars(substr($a, 0, $max_string_length)).((strlen($a) > $max_string_length) ? '...' : '');
                    $args .= "\"$a\"";
                    break;
                case 'array':
                    $args .= 'Array('.count($a).')';
                    break;
                case 'object':
                    $args .= 'Object('.get_class($a).')';
                    break;
                case 'resource':
                    $args .= 'Resource('.strstr($a, '#').')';
                    break;
                case 'boolean':
                    $args .= $a ? 'True' : 'False';
                    break;
                case 'NULL':
                    $args .= 'Null';
                    break;
                default:
                    $args .= 'Unknown';
            }
        }

        settype($bt['class'], 'string');
        settype($bt['type'], 'string');
        $output .= "<br />\n";
        $output .= "<b>file:</b> {$bt['line']} - {$bt['file']}<br />\n";
        $output .= "<b>call:</b> {$bt['class']}{$bt['type']}{$bt['function']}($args)<br />\n";
    }

    $output .= "</div>\n";

    if ($cmd_line) 
        $output = strip_tags($output); 

    return $output; 
}

/**
 * Error handler
 */
function func_error_handler($errno, $errstr, $errfile, $errline)
{
    static $hash_errors = array();

    if (!(ini_get('error_reporting') & $errno))
        return;

    if (ini_get('display_errors') == 0 && ini_get('log_errors') == 0)
        return;

    // If error has been supressed with an @
    if (error_reporting() == 0)
        return;

    if (ini_get('ignore_repeated_errors') == 1 && isset($hash_errors[$errno]) && isset($hash_errors[$errno][$errfile.":".$errline]))
        return;

    $date = date("d-M-Y H:i:s");

    $errortypes = array(
        E_ERROR                => 'Error',
        E_WARNING            => 'Warning',
        E_PARSE                => "Parsing Error",
        E_NOTICE            => 'Notice',
        E_CORE_ERROR        => 'Error',
        E_CORE_WARNING        => 'Warning',
        E_COMPILE_ERROR        => 'Error',
        E_COMPILE_WARNING    => 'Warning',
        E_USER_ERROR        => 'Error',
        E_USER_WARNING        => 'Warning',
        E_USER_NOTICE        => 'Notice',
    );

    if (defined('E_STRICT'))
        $errortypes[E_STRICT] = "Runtime Notice";

    $errortype = isset($errortypes[$errno]) ? $errortypes[$errno] : "Unknown Error";

    if (ini_get('display_errors') != 0) {

        // Display error
        global $REQUEST_METHOD;
        if (empty($REQUEST_METHOD))
            echo "$errortype: $errstr in $errfile on line $errline\n";
        else
            echo "<b>$errortype</b>: $errstr in <b>$errfile</b> on line <b>$errline</b><br />\n";
    }

    if (ini_get('log_errors') == 1 && ini_get('error_log') != '') {

        // Write error to file
        $bt = '';
        if (func_constant('LOG_WITH_BACKTRACE') || func_constant('DEVELOPMENT_MODE')) {
            $bt = "\nREQUEST_URI: ".$_SERVER['REQUEST_URI'];
            $bt .= "\nBacktrace:\n\t".implode("\n\t", func_get_backtrace(1));
        }

        error_log(
            "[$date] $errortype: $errstr in $errfile on line $errline $bt\n",
            3,
            ini_get('error_log')
        );
    }

    if (ini_get('ignore_repeated_errors') == 1) {
        if (!isset($hash_errors[$errno]))
            $hash_errors[$errno] = array();
        $hash_errors[$errno][$errfile.":".$errline] = true;
    }
}

/**
 * Log the whole temporary file
 */
function x_log_tmp_file($tmp_file)
{
    $file = file($tmp_file);
    if (empty($file))
        return;
    $file = implode("\n", file($tmp_file));
    x_log_flag('log_tmp', 'TMP', $file);
}

function func_check_phpini_changes()
{
    global $config, $var_dirs;

    if ($config['General']['skip_log_phpini_changes'] == 'Y')
        return false;

    /**
     * Log changes of PHP.ini settings
     */
    $old_settings = false;

    if (file_exists($var_dirs['log'] . '/data.phpini.php')) {

        $fp = @fopen($var_dirs['log'] . '/data.phpini.php', 'rb');

        if ($fp) {

            @fseek($fp, X_LOG_SIGNATURE_LENGTH);

            $old_settings = @unserialize(@fread($fp, @filesize($var_dirs['log'] . '/data.phpini.php') - X_LOG_SIGNATURE_LENGTH));

            fclose($fp);

        }

        unset($fp);
    }

    $current_settings = ini_settings_storage();

    // these optionas are set in config.php
    func_unset(
        $current_settings,
        'error_log',
        'ignore_repeated_errors',
        'log_errors',
        'log_errors_max_len',
        'magic_quotes_runtime',
        'session.bug_compat_warn',
        'max_execution_time',
        'mbstring.internal_encoding'
    );

    $_tmp_changed = false;

    if (
        is_array($old_settings)
        && !empty($old_settings)
        && is_array($current_settings)
    ) {

        $changed_settings = func_array_compare($old_settings, $current_settings);

        $_msg = array();

        if (!empty($changed_settings['removed'])) {

            $_lines = array();

            foreach ($changed_settings['removed'] as $_k => $_v) {

                $_lines[] = "\t$_k = ``$_v''";

            }

            $_msg[] = "Removed options:\n" . implode("\n", $_lines);

            unset($_lines);

        }

        if (!empty($changed_settings['added'])) {

            $_lines = array();

            foreach ($changed_settings['added'] as $_k => $_v) {

                $_lines[] = "\t$_k = ``$_v''";

            }

            $_msg[] = "Added options:\n" . implode("\n", $_lines);

            unset($_lines);

        }

        if (!empty($changed_settings['changed'])) {

            $_lines = array();

            foreach ($changed_settings['changed'] as $_k => $_v) {

                $_lines[] = "\t$_k = '$_v' (was: '" . $old_settings[$_k]."')";

            }

            $_msg[] = "Changed options:\n" . implode("\n", $_lines);

            unset($_lines);

        }

        if (!empty($_msg)) {

            x_log_add('ENV', implode("\n", $_msg));

            $_tmp_changed = true;

        }

        unset($changed_settings);
        unset($_msg);
    }

    if (
        empty($old_settings)
        || $_tmp_changed
    ) {
        $_tmp_fp = @fopen($var_dirs['log'] . '/data.phpini.php', 'wb');

        if ($_tmp_fp) {

            @fwrite($_tmp_fp, X_LOG_SIGNATURE . serialize($current_settings));

            @fclose($_tmp_fp);

            func_chmod_file($var_dirs['log'] . '/data.phpini.php');

        }

        unset($_tmp_fp);
    }

}

?>
