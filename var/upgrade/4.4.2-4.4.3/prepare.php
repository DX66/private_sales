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
 * This module provides compatibility with different hostings and versions of PHP.
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: prepare.php,v 1.118.2.1 2011/01/10 13:11:44 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: index.php"); die("Access denied"); }

$__quotes_sybase_qpc = ini_get('magic_quotes_sybase') && ini_get('magic_quotes_gpc');

if (
    file_exists($xcart_dir . '/check_requirements.php')
    && is_readable($xcart_dir . '/check_requirements.php')
) {
    include $xcart_dir . '/check_requirements.php';
}

//
// DO NOT CHANGE ANYTHING BELOW THIS LINE UNLESS
// YOU REALLY KNOW WHAT YOU ARE DOING
//

if (function_exists('set_magic_quotes_runtime'))
    @set_magic_quotes_runtime(0);

ini_set('magic_quotes_sybase',0);
ini_set('session.bug_compat_42',1);
ini_set('session.bug_compat_warn',0);

if (!defined('X_USE_NEW_HTMLSPECIALCHARS'))
    define('X_USE_NEW_HTMLSPECIALCHARS', phpversion() >= '4.1.0');

if (
    defined('X_USE_PHPIDS')
    && constant('X_USE_PHPIDS')
    && function_exists('version_compare')
    && version_compare(phpversion(), '5.1.6') >= 0
    && function_exists('simplexml_load_file')
    && file_exists($xcart_dir . '/include/lib/IDS')
) {

    set_include_path(get_include_path() . PATH_SEPARATOR . $xcart_dir . '/include/lib');

    require_once('IDS/Init.php');
    $request = array(
        'REQUEST'     => $_REQUEST,
        'GET'         => $_GET,
        'POST'         => $_POST,
        'COOKIE'     => $_COOKIE,
    );

    $init = IDS_Init::init($xcart_dir . '/include/lib/IDS/Config/Config.ini');

    // Configure PHP IDS
    $init->config['General']['base_path']     = $xcart_dir . "/include/lib/IDS";
    $init->config['General']['filter_path'] = $xcart_dir . "/include/lib/IDS/default_filter.xml";
    $init->config['General']['tmp_path']     = $xcart_dir . "/var/tmp";
    $init->config['Logging']['path']         = $xcart_dir . "/var/log/phpids_log.txt";
    $init->config['Caching']['path']         = $xcart_dir . "/var/cache/default_filter.cache";

    $ids = new IDS_Monitor(
        array(
            'REQUEST'     => $_REQUEST,
            'GET'         => $_GET,
            'POST'         => $_POST,
            'COOKIE'     => $_COOKIE,
        ),
        $init
    );

    $result = $ids->run();

    if (!$result->isEmpty()) {

        // Build security message
        $msg = array();
        foreach ($result->getIterator() as $e) {

            list($_ch, $_var) = explode('.', $e->getName(), 2);

            $msg[] = 'Variable: ' . $_var . ' (channel: ' . $_ch . ")\n\tData: " . $e->getValue() . "\n\tSecurity events:";

            $msg2 = array();

            foreach ($e->getFilters() as $f) {

                $msg2[] = $f->getDescription() . '; Level: ' . $f->getImpact() . '; Tags: ' . implode(', ', $f->getTags());

            }

            $msg[] = "\t\t" . implode("\n\t\t", $msg2) . "\n";

            unset($msg2);

        }

        define('X_PHPIDS_MSG', implode("\n", $msg));

        unset($msg);

    }

    unset($init, $ids, $result);
}

$__quotes_qpc = function_exists('get_magic_quotes_gpc')
    ? get_magic_quotes_gpc()
    : false;

function func_microtime()
{

    list(
        $usec,
        $sec
    ) = explode(" ", microtime());

    return ((float)$usec + (float)$sec);

}

function func_unset(&$array)
{

    $keys = func_get_args();

    array_shift($keys);

    if (
        !empty($keys)
        && !empty($array)
        && is_array($array)
    ) {

        foreach ($keys as $key) {

            unset($array[$key]);

        }

    }

}

/**
 * Responsible version of empty()
 */

function zerolen()
{

    foreach (func_get_args() as $arg) {

        if (strlen($arg) == 0) return true;

    }

    return false;
}

function func_array_map($func, $var)
{

    if (!is_array($var)) return $var;

    foreach($var as $k => $v)
        $var[$k] = call_user_func($func, $v);

    return $var;
}

/**
 * Variant of the function array_map(), where user function is used both for
 * the value of an array element and for its key
 */
function func_array_map_hash($func, $var)
{

    if (!is_array($var))
        return $var;

    $var_proc = array();

    foreach ($var as $k => $v) {

        $var_proc[call_user_func($func, $k)] = call_user_func($func, $v);

        unset($var[$k]);

    }

    return $var_proc;
}

function func_array_merge()
{

    $vars = func_get_args();

    $result = array();

    if (
        !is_array($vars)
        || empty($vars)
    ) {

        return $result;

    }

    foreach($vars as $v) {

        if (
            is_array($v)
            && !empty($v)
        ) {

            $result = array_merge($result, $v);

        }

    }

    return $result;
}

function func_stripslashes($var)
{
    return is_array($var)
        ? func_array_map_hash('func_stripslashes', $var)
        : stripslashes($var);
}

/**
 * Strip Sybase-style magic quotes
 */
function func_stripslashes_sybase($data)
{
    return is_array($data)
        ? func_array_map_hash('func_stripslashes_sybase', $data)
        : str_replace("''", "'", $data);
}

if (addslashes(chr(0)) == '\\'.chr(0)) {

    function func_addslashes($var) {
        return is_array($var)
            ? func_array_map_hash('func_addslashes', $var)
            : str_replace(chr(0), '0', addslashes($var));
    }

    function func_addslashes_null($var) {
        return is_array($var)
            ? func_array_map_hash('func_addslashes_null', $var)
            : str_replace(chr(0), '0', $var);
    }

    function func_addslashes_keys($var) {

        if (!is_array($var))
            return str_replace(chr(0), '0', addslashes($var));

        $var_proc = array();

        foreach ($var as $k => $v) {

            unset($var[$k]);

            $var_proc[func_addslashes_keys($k)] = $v;

        }

        return $var_proc;
    }

    if ($__quotes_sybase_qpc) {

        // Strip Sybase-style magic quotes
        foreach(
            array(
                '_GET',
                '_POST',
                '_COOKIE',
                '_ENV',
            ) as $__avar
        ) {

            $GLOBALS[$__avar] = func_addslashes(func_stripslashes_sybase($GLOBALS[$__avar]));

        }

        $__quotes_sybase_qpc = false;

    } elseif ($__quotes_qpc) {

        // Strip slashes
        foreach (
            array(
                '_GET',
                '_POST',
                '_COOKIE',
                '_ENV',
            ) as $__avar
        ) {

            $GLOBALS[$__avar] = func_addslashes_null($GLOBALS[$__avar]);

        }

    }

} else {

    function func_addslashes($var) {
        return is_array($var)
            ? func_array_map_hash('func_addslashes', $var)
            : addslashes($var);
    }

    function func_addslashes_keys($var) {

        if (!is_array($var))
            return addslashes($var);

        $var_proc = array();

        foreach ($var as $k => $v) {

            unset($var[$k]);

            $var_proc[func_addslashes_keys($k)] = $v;

        }

        return $var_proc;
    }

}

function func_strip_tags($var)
{
    return is_array($var)
        ? func_array_map_hash('func_strip_tags', $var)
        : strip_tags($var);
}

function func_have_script_tag($var)
{

    if (!is_array($var)) {

        return (stristr($var, '<script') !== false);

    }

    foreach ($var as $item) {

        if (!is_array($var)) {

            if (stristr($var, '<script') !== false) return true;

        } elseif (func_have_script_tag($item)) {

            return true;

        }

    }

    return false;
}

function func_allowed_var($name)
{
    global $reject;

    if (
        in_array($name, $reject)
        && !defined('ADMIN_UNALLOWED_VAR_FLAG')
    ) {

        define('ADMIN_UNALLOWED_VAR_FLAG', 1);

    }

    return !in_array($name, $reject);
}

function func_get_request_uri()
{

    if (isset($_SERVER['REQUEST_URI'])) {

        return $_SERVER['REQUEST_URI'];

    }

    if (isset($_SERVER['HTTP_X_ORIGINAL_URL'])) {

        return $_SERVER['HTTP_X_ORIGINAL_URL'];

    } else if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {

        return $_SERVER['HTTP_X_REWRITE_URL'];

    }

    if (
        isset($_SERVER['PATH_INFO'])
        && !zerolen($_SERVER['PATH_INFO'])
    ) {

        if ($_SERVER['PATH_INFO'] == $_SERVER['PHP_SELF']) {

            $request_uri = $_SERVER['PHP_SELF'];

        } else {

            $request_uri = $_SERVER['PHP_SELF'] . $_SERVER['PATH_INFO'];

        }

    } else {

        $request_uri = $_SERVER['PHP_SELF'];

    }

    // Append query string
    if (
        isset($_SERVER['argv'])
        && isset($_SERVER['argv'][0])
        && !zerolen($_SERVER['argv'][0])
    ) {

        $request_uri .= '?' . $_SERVER['argv'][0];

    } elseif (
        isset($_SERVER['QUERY_STRING'])
        && !zerolen($_SERVER['QUERY_STRING'])
    ) {

        $request_uri .= '?' . $_SERVER['QUERY_STRING'];

    }

    return $request_uri;
}

function func_is_var_excluded($array_name, $var_name)
{
    // Skip variable to be set as GLOBAL

    // i.e. array('_SERVER' => 'id');
    $excluded_vars = array();

    if (
        is_array($excluded_vars)
        && !empty($excluded_vars)
    ) {

        foreach ($excluded_vars as $an => $vn) {

            if (
                $array_name == $an
                && $var_name == $vn
            ) {
                return true;
            }

        }

    }

    return false;
}

define('X_REJECT_OVERRIDE', 1);

define('X_REJECT_CLEAN', 2);

function func_init_reject($option = 0)
{
    static $reject = false;

    if ($option & X_REJECT_CLEAN) {

        $reject = false;

        return true;
    }

    if (
        !$reject
        || $option & X_REJECT_OVERRIDE
    ) {

        $reject = array_keys($GLOBALS);
        $reject[] = 'reject';
        $reject[] = '__name';
        $reject[] = '__avar';
        $reject[] = 'GLOBALS';
        $reject[] = '_GET';
        $reject[] = '_POST';
        $reject[] = '_SERVER';
        $reject[] = '_ENV';
        $reject[] = '_COOKIE';
        $reject[] = '_FILES';
        $reject[] = '_SESSION';
        $reject[] = 'XCART_SESSION_VARS';
        $reject[] = 'XCART_SESSION_UNPACKED_VARS';
        $reject[] = 'HTTP_RAW_POST_DATA';

    }

    return $reject;
}

// Clean input variables which were already defined as global
function func_var_cleanup($__avar)
{
    global $dfh_vars;

    foreach ($GLOBALS[$__avar] as $__var => $__res) {

        if (func_is_var_excluded($__avar, $__var))
            continue;

        if (func_allowed_var($__var)) {

            if (
                in_array($__var, $dfh_vars)
                && is_array($__res)
                && count($__res) == 1
            ) {
                $__res = $GLOBALS[$__avar][$__var] = array_pop($__res);
            }

            $GLOBALS[$__var] = $__res;

        } else {

            func_unset($GLOBALS[$__avar], $__var);

        }

    }

    reset($GLOBALS[$__avar]);
}

if (!defined('XCART_EXT_ENV')) {

    if (
        isset($_COOKIE['is_robot'])
        && $_COOKIE['is_robot']
    ) {
        define('IS_ROBOT', 1);

        if (
            isset($_COOKIE['robot'])
            && !zerolen($_COOKIE['robot'])
        ) {

            define('ROBOT', $_COOKIE['robot']);

        }

    }

    if (!$__quotes_qpc) {
        // Add slashes
        foreach (
            array(
                '_GET',
                '_POST',
                '_COOKIE',
            ) as $__avar
        ) {

            $GLOBALS[$__avar] = func_addslashes($GLOBALS[$__avar]);

        }

    } elseif ($__quotes_sybase_qpc) {
        // Strip Sybase-style magic quotes
        foreach(
            array(
                '_GET',
                '_POST',
                '_COOKIE'
            ) as $__avar
        ) {

            $GLOBALS[$__avar] = func_stripslashes_sybase($GLOBALS[$__avar]);

            $GLOBALS[$__avar] = func_addslashes($GLOBALS[$__avar]);

        }

    } else {
        // Add slashes for keys
        foreach(
            array(
                '_GET',
                '_POST',
                '_COOKIE',
            ) as $__avar
        ) {

            $GLOBALS[$__avar] = func_addslashes_keys($GLOBALS[$__avar]);

        }

    }

    // strong validation for the SERVER variables
    foreach ($_SERVER as $__var => $__res) {

        $_SERVER[$__var] = func_strip_tags($__res);

    }

    // simple validation for the GET variables
    foreach ($_GET as $__var => $__res) {

        if (
            defined('USE_TRUSTED_GET_VARS')
            && in_array($__var, explode(",", USE_TRUSTED_GET_VARS))
        ) {

            if (
                !defined('USE_TRUSTED_SCRIPT_VARS')
                && func_have_script_tag($__res)
            ) {

                unset($$__var);

                unset($_GET[$__var]);

            }

        } else {

            $_GET[$__var] = func_strip_tags($__res);

        }

    }

    // simple validation for the COOKIE variables
    foreach ($_COOKIE as $__var => $__res) {

        $_COOKIE[$__var] = func_strip_tags($__res);

    }

    // validation for the POST variables: strip html tags from untrusted variables
    foreach ($_POST as $__var => $__res) {

        if (
            defined('USE_TRUSTED_POST_VARIABLES')
            && in_array($__var, $trusted_post_variables)
        ) {
            // ignore trusted variables: these variables used in product/category modify etc

            if (
                !defined('USE_TRUSTED_SCRIPT_VARS')
                && func_have_script_tag($__res)
            ) {

                unset($$__var);

                unset($_POST[$__var]);

            }

        } else {

            $_POST[$__var] = func_strip_tags($__res);

        }

    }

    $_trust = array('substring');
    $trusted_vars = (empty($trusted_vars) || !is_array($trusted_vars))
        ? $_trust
        : func_array_merge($trusted_vars, $_trust);

    unset($__avar, $__var, $__res);

    // Disable form history variables' names
    $dfh_vars = array(
        'card_name',
        'card_type',
        'card_number',
        'card_expire',
        'card_expire_Day',
        'card_expire_Month',
        'card_expire_Year',
        'card_valid_from',
        'card_valid_from_Day',
        'card_valid_from_Month',
        'card_valid_from_Year',
        'card_cvv2',
        'card_issue_no',
    );

    // register allowed global variables from request
    $reject = func_init_reject(X_REJECT_OVERRIDE);

    foreach(
        array(
            '_GET',
            '_POST',
            '_COOKIE',
            '_SERVER',
        ) as $__avar
    ) {
        func_var_cleanup($__avar);
    }

    func_init_reject(X_REJECT_CLEAN);

    foreach ($_FILES as $__name => $__value) {

        if (!func_allowed_var($__name)) continue;

        $$__name = $__value['tmp_name'];

        foreach($__value as $__k=>$__v) {

            $__varname_ = $__name.'_'.$__k;

            if (!func_allowed_var($__varname_)) continue;

            $$__varname_ = $__v;

        }

    }
    unset($reject, $__avar, $__var, $__res);

    $int_values = array(
        'sort_direction',
        'page',
    );

    foreach ($int_values as $int_value) {

        if (isset($$int_value)) {

            $$int_value = intval($$int_value);

        }

    }

    if (
        !isset($HTTP_RAW_POST_DATA)
        && phpversion() == '5.2.2'
    ) {
        // Generate $HTTP_RAW_POST_DATA due to the bug in PHP 5.2.2 (http://bugs.php.net/bug.php?id=41293)

        $HTTP_RAW_POST_DATA = file_get_contents("php://input");

        if (empty($HTTP_RAW_POST_DATA))
            unset($HTTP_RAW_POST_DATA);

    }

}

/**
 * OS detection
 */
define('X_DEF_OS_WINDOWS', (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'));

if (!defined('PATH_SEPARATOR')) {

    define('PATH_SEPARATOR', X_DEF_OS_WINDOWS ? ';' : ':');
}

$_SERVER['REQUEST_URI'] = $REQUEST_URI = func_get_request_uri();

if (
    file_exists($xcart_dir . '/include/https_detect.php')
    && is_readable($xcart_dir . '/include/https_detect.php')
) {
    include $xcart_dir . '/include/https_detect.php';
}

/**
 * HTTP_REFERER override
 */
if (
    isset($_GET['iframe_referer'])
    && !empty($_GET['iframe_referer'])
) {
    $HTTP_REFERER = urldecode($_GET['iframe_referer']);
}

if (
    isset($HTTP_REFERER)
    && !empty($HTTP_REFERER)
    && strncasecmp($HTTP_REFERER, 'http://', 7)
    && strncasecmp($HTTP_REFERER, 'https://', 8)
) {
    $HTTP_REFERER = '';

    if (
        isset($_SERVER['HTTP_REFERER'])
        && !empty($_SERVER['HTTP_REFERER'])
    ) {
        unset($_SERVER['HTTP_REFERER']);
    }

    if (
        isset($_GET['iframe_referer'])
        && !empty($_GET['iframe_referer'])
    ) {
        unset($_GET['iframe_referer']);
    }
}

/**
 * Proxy IP
 */
$PROXY_IP = '';

if (
    isset($HTTP_X_FORWARDED_FOR)
    && !empty($HTTP_X_FORWARDED_FOR)
) {

    $PROXY_IP = $HTTP_X_FORWARDED_FOR;

} elseif (
    isset($HTTP_X_FORWARDED)
    && !empty($HTTP_X_FORWARDED)
) {

    $PROXY_IP = $HTTP_X_FORWARDED;

} elseif (
    isset($HTTP_FORWARDED_FOR)
    && !empty($HTTP_FORWARDED_FOR)
) {

    $PROXY_IP = $HTTP_FORWARDED_FOR;

} elseif (
    isset($HTTP_FORWARDED)
    && !empty($HTTP_FORWARDED)
) {

    $PROXY_IP = $HTTP_FORWARDED;

} elseif (
    isset($HTTP_CLIENT_IP)
    && !empty($HTTP_CLIENT_IP)
) {

    $PROXY_IP = $HTTP_CLIENT_IP;

} elseif (
    isset($HTTP_X_COMING_FROM)
    && !empty($HTTP_X_COMING_FROM)
) {

    $PROXY_IP = $HTTP_X_COMING_FROM;

} elseif (
    isset($HTTP_COMING_FROM)
    && !empty($HTTP_COMING_FROM)
) {

    $PROXY_IP = $HTTP_COMING_FROM;

}

$REMOTE_ADDR = isset($_SERVER['REMOTE_ADDR'])
    ? addslashes($_SERVER['REMOTE_ADDR'])
    : false;

$PROXY_IP = addslashes($PROXY_IP);

if (!empty($PROXY_IP)) {

    $CLIENT_IP = $PROXY_IP;

    $PROXY_IP = isset($REMOTE_ADDR)
        ? $REMOTE_ADDR
        : false;

} else {

    $CLIENT_IP = isset($REMOTE_ADDR)
        ? $REMOTE_ADDR
        : false;

}

if(
    isset($_GET['benchmark'])
    || isset($_POST['benchmark'])
) {
    define('START_TIME', func_microtime());
}

/**
 * Initializations for the required variables
 */
foreach (
    array(
        'QUERY_STRING',
        'HTTP_REFERER',
        'antibot_input_str',
        'paymentid',
        'pconf_update',
        'action',
        'user',
        'mode',
        'submode',
        'parent',
    ) as $var
) {
    $$var = isset($$var)
        ? $$var
        : '';
}

/**
 * Miscellaneous constants
 */
define('SECONDS_PER_DAY', 86400); // 60 * 60 * 24
define('SECONDS_PER_WEEK', 604800); // 60 * 60 * 24 * 7

$max_execution_time = ini_get('max_execution_time');

if ($max_execution_time == 0) {

    $max_execution_time = constant('SECONDS_PER_WEEK');

}

?>
