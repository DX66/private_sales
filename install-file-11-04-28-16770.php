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
 * X-Cart installation wizard
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Customer interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: install.php,v 1.337.2.5 2011/01/10 13:11:43 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!(basename(__FILE__) === 'install.php')) { // is not install.php
    die();
}

/**
 * X-Cart SQL tables count (184)
 */
define('XC_TABLES_COUNT', 184);

include './top.inc.php';

/**
 * Check if store has already been installed.
 *
 * @return boolean
 * @see    ____func_see____
 * @since  1.0.0
 */
function is_installed()
{
    global $xcart_dir;

    if (!is_readable($xcart_dir . '/config.php'))
        return false;

    require_once $xcart_dir . '/config.php';

    if (
        file_exists($xcart_dir . '/config.local.php')
        && is_readable($xcart_dir . '/config.local.php')
    ) {
        require_once $xcart_dir . '/config.local.php';
    }

    $is_installed = false;

    $link = @mysql_connect($sql_host, $sql_user, $sql_password);

    if (
        !empty($link)
        && is_resource($link)
    ) {
        $is_db = @mysql_select_db($sql_db, $link);

        if (true === $is_db) {

            $query = @mysql_query('SHOW TABLES', $link);

            if (
                !empty($query)
            ) {
                $rows = @mysql_num_rows($query);

                if (constant('XC_TABLES_COUNT') <= $rows) {

                    $is_installed = true;

                }
            }
        }
    }

    return $is_installed;
}

function func_phishing($arr)
{
    global $sql_conf_trusted_vars;

    if (is_array($arr) && !empty($arr)) {

        foreach($arr as $k => $v) {

            if (is_array($v)) {
                $arr[$k] = func_phishing($v);
                continue;
            }

            if (!in_array($k, $sql_conf_trusted_vars))
                $arr[$k] = htmlspecialchars($arr[$k], ENT_QUOTES);
        }
    }

    return $arr;
}

$sql_conf_trusted_vars = array('mysqlhost','mysqluser','mysqlpass');

foreach(array('_GET', '_POST', '_COOKIE') as $__avar) {
    $GLOBALS[$__avar] = func_phishing($GLOBALS[$__avar]);
}

require_once $xcart_dir.'/include/func/func.core.php';

x_load('compat');

if (!defined('XCART_SESSION_START')) {

    define('XCART_SESSION_START',1);

    // Send anti-cache headers
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

    if (
        isset($_SERVER)
        && (
            isset($_SERVER['HTTPS'])
            && (
                stristr($_SERVER['HTTPS'], 'on')
                || $_SERVER['HTTPS'] == 1
            )
            || isset($_SERVER['SERVER_PORT'])
            && $_SERVER['SERVER_PORT'] == 443
        )
    ) {

        header("Cache-Control: private, no-store, no-cache, must-revalidate, post-check=0, pre-check=0");

    } else {

        header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");

    }

}

if (!defined('XCART_START'))
    define('XCART_START',1);

define('XCART_EXT_ENV', true);

if (!defined('PHP_EOL')) {

    switch (strtoupper(substr(PHP_OS, 0, 3))) {
        // Windows
        case 'WIN':
            define('PHP_EOL', "\r\n");
            break;

        // Mac
        case 'DAR':
            define('PHP_EOL', "\r");
            break;

        // Unix
        default:
            define('PHP_EOL', "\n");
    }

}

/**
 * Predefined common variables
 */

$min_ver = '4.4.0';

$directories_to_create = array('var/log', 'var/tmp', 'var/templates_c', 'var/upgrade');
$directories_to_create[] = 'files/userfiles_1';

// Check permissions of specified files/directories
$check_permissions = array(
    'files' => array(
        'type' => 'directory',
        'mode' => 'writable',
        'permissions' => array(
            'nonprivileged' => 0777,
            'privileged' => 0700
        )
    ),
    'catalog' => array(
        'type' => 'directory',
        'mode' => 'writable',
        'permissions' => array(
            'nonprivileged' => 0777,
            'privileged' => 0711
        )
    ),
    'images' => array(
        'type' => 'directory',
        'mode' => 'writable',
        'permissions' => array(
            'nonprivileged' => 0777,
            'privileged' => 0711
        )
    ),
    'skin' => array(
        'type' => 'directory',
        'mode' => 'writable',
        'permissions' => array(
            'nonprivileged' => 0777,
            'privileged' => 0711
        )
    ),
    'var' => array(
        'type' => 'directory',
        'mode' => 'writable',
        'permissions' => array(
            'nonprivileged' => 0777,
            'privileged' => 0711
        )
    ),
    'var'.XC_DS.'cache' => array(
        'type' => 'directory',
        'mode' => 'writable',
        'permissions' => array(
            'nonprivileged' => 0777,
            'privileged' => 0711
        )
    ),
    'config.php' => array(
        'type' => 'file',
        'mode' => 'writable',
        'permissions' => array(
            'nonprivileged' => 0666,
            'privileged' => 0600
        )
    ),
    'admin'.XC_DS.'newsletter.sh' => array(
        'type' => 'file',
        'mode' => 'executable',
        'permissions' => array(
            'nonprivileged' => 0755,
            'privileged' => 0755
        )
    ),
    'payment'.XC_DS.'ccash.pl' => array(
        'type' => 'file',
        'mode' => 'executable',
        'permissions' => array(
            'nonprivileged' => 0755,
            'privileged' => 0755,
        )
    ),
    'payment'.XC_DS.'csrc.pl' =>  array(
        'type' => 'file',
        'mode' => 'executable',
        'permissions' => array(
            'nonprivileged' => 0755,
            'privileged' => 0755
        )
    ),
    'payment'.XC_DS.'netssleay.pl' => array(
        'type' => 'file',
        'mode' => 'executable',
        'permissions' => array(
            'nonprivileged' => 0755,
            'privileged' => 0755
        )
    )
);

$post_install_permissions = array(
    'var' => array(
        'nonprivileged' => 0755,
        'privileged' => 0711
    ),
    'config.php' => array(
        'nonprivileged' => 0644,
        'privileged' => 0600
    )
);

$init_blowfish_key = '8d5db63ada15e11643a0b1c3477c2c5c';

$installation_product = "X-Cart";

// Check integrity of these files.
$check_files = array(
	'include/func/func.ajax.php' => 'b0436b56aefee8e9a23f4d75c0fcfbcd',
	'include/func/func.backoffice.php' => '61c0dc0ea45dad4ba3836b43a9b595ab',
	'include/func/func.cart.php' => '6da9337451a0c40f7acac73709713b7b',
	'include/func/func.category.php' => '9c100063df6fbd3d5964ce3b8e0a130d',
	'include/func/func.clean_urls.php' => '0158d4a120adeb73d99ac76a15c2746d',
	'include/func/func.compat.php' => '80e4516e991d4a156df3b5e9bed1b004',
	'include/func/func.core.php' => 'd6bd993db5fb385e51c94fe38d302a04',
	'include/func/func.crypt.php' => 'a7138b6ef2e0aa1768572d227bfb8ccc',
	'include/func/func.db.php' => '28fc8e1b078aade05fc0c3625eeadf9d',
	'include/func/func.debug.php' => '074dbaa57dac6680e5fd51e879b7801b',
	'include/func/func.export.php' => '14ec0713c216cedcf54b7d0b1840ee3a',
	'include/func/func.files.php' => '9c7e9236d75d3957f7c048653a3687a0',
	'include/func/func.gd.php' => 'c981f830b368b7229e86ce36e57226fb',
	'include/func/func.html_catalog.php' => '7d716b61f1d5603ba6c87e6abdf1ae03',
	'include/func/func.http.php' => '1966a1e2182d1fdfc8cc51626e4e4174',
	'include/func/func.https_curl.php' => 'aa6dd917dad8aac94cb265fea51119ee',
	'include/func/func.https_libcurl.php' => '227b930543ff89349f38b9e51f54e37c',
	'include/func/func.https_openssl.php' => '1ec0ac020d204c50a38e13ffa77b7ecc',
	'include/func/func.https_ssleay.php' => 'bfc402b400f86b1a3cef1ea666a17d5c',
	'include/func/func.image.php' => '4d21e52e4a0356519017d1452d074f0c',
	'include/func/func.import.php' => 'c926836f90d0e050bfe6c50ffe82537e',
	'include/func/func.iterations.php' => '2340cd237cabfd9620f0acc944799382',
	'include/func/func.logging.php' => '4fc9fd43e128f8e2cc4bb0be4cc93290',
	'include/func/func.mail.php' => '217c33863a87ca267647567db538ebb1',
	'include/func/func.memcache.php' => '0c2ebaa64a5458ab044b9fb95e8778c8',
	'include/func/func.order.php' => '8d011f8315ee1fd23a2893bb35a9721e',
	'include/func/func.pack.php' => '96d0a33256319f944ba9ca15c9d1584b',
	'include/func/func.pages.php' => '67ec644ea147efb32e18ee94dd5f14a9',
	'include/func/func.payment.php' => '398d68f5b52321be8ce50996472a89f0',
	'include/func/func.paypal.php' => '0ebf23c94760a00b8884958b48c4283d',
	'include/func/func.product.php' => 'a6bb1d9b2a517b904059513f94cc2234',
	'include/func/func.quick_search.php' => 'dacea9dafe4ec3f2cb2d5b5d96fbca20',
	'include/func/func.shipping.php' => 'efa2a604df61987b1abada6de0386508',
	'include/func/func.snapshots.php' => '5f829e9a6c3cc1a4021e702fe68307bc',
	'include/func/func.taxes.php' => '4ae3e25c42ed1d4b2eb98fb1e4f745ba',
	'include/func/func.templater.php' => 'd77486341c9696783d789f3f95ecc5f2',
	'include/func/func.tests.php' => '113b767f967a95440e507ba645cf318f',
	'include/func/func.user.php' => '9157ac66adce361ec3fd97c9b0b8999d',
	'include/func/func.xml.php' => '79f34d4d1896c3143116668a4011ba37'
);

// Check if we got called from x-cart trial/demo version script.
if (base64_encode($installation_product) == 'WC1DYXJ0IERlbW8=') {
    define('XCART_TRIAL', true);
}

// Technical problems report constants.
define('X_REPORT_PRODUCT_TYPE', 'XC');

if (defined('XCART_TRIAL')) {

    define('X_REPORT_URL', 'https://secure.qtmsoft.com/service.php?target=install_feedback_report');

} else {

    define('X_REPORT_URL', 'https://secure.qtmsoft.com/customer.php?target=customer_info&amp;action=install_feedback_report');

}

$used_functions = array();

if (
    file_exists($xcart_dir . '/include/used_functions.php')
    && is_readable($xcart_dir . '/include/used_functions.php')
) {
    include $xcart_dir . '/include/used_functions.php';
}

$required_functions = is_array($used_functions) ? $used_functions : array('popen', 'exec', 'pclose', 'ini_set', 'fsockopen');

unset($used_functions);

// Modules definition
// used in include/install.php (install subsystem)

// This array describes what to do at the current step of installation:
// - key in $modules - number of step
// - $modules[$step]['name'] - suffix of function name
//   (e.g. module_language for 'language')
// - $modules[$step]['comment'] - name of language variable that
//   content will appears at page (see include/install_lng_*.php)

// Each module function should accept at least one argument: $params
// Expected return value of module function:
// - false on success
// - true on failure (and set up global variable $error)

$modules = array (
    0 => array(
            'name' => 'language',
            'sb_title' => 'title_language',
            'comment' => 'mod_language'
    ),
    1 => array(
            'name' => 'default',
            'comment' => 'mod_license',
            'sb_title' => 'title_license',
            'js_next' => 1
        ),
    2 => array(
            'name' => 'check_cfg',
            'sb_title' => 'title_check_cfg',
            'comment' => 'mod_check_cfg'
        ),
    3 => array(
            'name' => 'cfg_install_db',
            'sb_title' => 'title_install_db',
            'comment' => 'mod_cfg_install_db',
            'js_next' => 1,
            'is_complete' => 4
        ),
    4 => array(
            'name' => 'install_db',
            'comment' => 'mod_install_db'
        ),
    5 => array(
            'name' => 'cfg_install_dirs',
            'sb_title' => 'title_install_dirs',
            'comment' => 'mod_cfg_install_dirs',
            'is_complete' => 5
        ),
    6 => array(
            'name' => 'install_dirs',
            'comment' => 'mod_install_dirs',
        ),
    7 => array(
            'name' => 'cfg_enable_paypal',
            'sb_title' => 'title_enable_paypal',
            'comment' => 'mod_cfg_enable_paypal',
            'is_complete' => 8
        ),
    8 => array(
            'name' => 'enable_paypal',
            'comment' => 'mod_enable_paypal'
        ),
    9 => array(
            'name' => 'generate_snapshot',
            'sb_title' => 'title_generate_snapshot',
            'comment' => 'mod_generate_snapshot'
        ),
    10 => array(
            'name' => 'install_done',
            'comment' => 'mod_install_done',
            'param' => 'func_success'
        )
);

// Do not display some steps in the status bar
$sb_excludes = array(4, 6, 9);

###############################################################
/**
 * Common functions goes here
 */
###############################################################

function change_config($params, $force_blowfish_key = false)
{
    $current_directory = str_replace("\\", '/', realpath('.'));

    $allfile = '';

    // Write data to config.php
    if (!($fp = @fopen('config.php', "r+")))
        return false;

    $xconfig_vars = array(
        'sql_host'           => $params['mysqlhost'],
        'sql_user'           => $params['mysqluser'],
        'sql_db'             => $params['mysqlbase'],
        'sql_password'       => $params['mysqlpass'],
        'xcart_http_host'    => $params['xcart_http_host'],
        'xcart_https_host'   => $params['xcart_https_host'],
        'xcart_web_dir'      => $params['xcart_web_dir'],
        'XCART_SESSION_NAME' => $params['session_name'],
    );

    while (!feof($fp)) {

        $buffer = fgets($fp, 4096);

        foreach($xconfig_vars as $varname => $val) {

            if (preg_match('/^\$'.$varname.' *=/', $buffer))
                $buffer = '$' . $varname . ' = \'' . str_replace("'", "\'", $val) . '\';' . PHP_EOL;

        }

        /*
            When the option "Update config.php only" is enabled, Blowfish key is not
            regenerated
            (This is not done intentionally, because, if the Blowfish key gets regenerated,
            the new key will be different from the key that was used to encrypt all the
            data, and the data will not be able to be decrypted).
        */
        if ((empty($params['config_only']) || $force_blowfish_key) && preg_match('/^\$blowfish_key\s*=/', $buffer))
            $buffer = preg_replace('/=.*;/S', "= '".$params["blowfish_key"]."';", $buffer);

        $allfile .= $buffer;

    }

    ftruncate($fp, 0);

    rewind($fp);

    $wl = fwrite($fp, $allfile);

    fclose($fp);

    return $wl && $wl == strlen($allfile);
}

/**
 * Recrypt all encrypted data
 */
function recrypt_data(&$params)
{
    global $bf_crypted_tables, $blowfish;

    if (!$blowfish)
        return false;

    $tbls = myquery("SHOW TABLES");

    if (!$tbls)
        return false;

    while ($tbl = mysql_fetch_row($tbls)) {

        $tbl = preg_replace("/^xcart_/S", '', $tbl[0]);

        if (!isset($bf_crypted_tables[$tbl]))
            continue;

        $data = myquery("SELECT ".$bf_crypted_tables[$tbl]['key'].", ".implode(", ", $bf_crypted_tables[$tbl]['fields'])." FROM xcart_".$tbl." WHERE 1 ".$bf_crypted_tables[$tbl]['where']);

        if (!$data)
            continue;

        $opt_where = (isset($bf_crypted_tables[$tbl]['use_where']) && ($bf_crypted_tables[$tbl]['use_where'] == 'Y'))
            ? $bf_crypted_tables[$tbl]['where']
            : '';

        while ($row = mysql_fetch_assoc($data)) {

            $key = array_shift($row);

            if (empty($row) || empty($key))
                continue;

            $update = array();

            foreach ($row as $fname => $fvalue) {
                if (substr($fvalue, 0, 1) == 'B')
                    $update[] = $fname.' = "'.addslashes(recrypt_field($fvalue, $params)).'"';
            }

            if (!empty($update)) {
                myquery("UPDATE xcart_$tbl SET ".implode(", ", $update)." WHERE ".$bf_crypted_tables[$tbl]['key']." = '".addslashes($key)."'".$opt_where);
            }

        }

        mysql_free_result($data);
    }

    mysql_free_result($tbls);

    // Generate new cron key
    myquery("UPDATE xcart_config SET value = '" . md5(uniqid(rand(),true)) . "' WHERE name = 'cron_key'");

    return true;
}

/**
 * Recrypt field
 */
function recrypt_field($field, &$params)
{
    global $init_blowfish_key;

    if (empty($init_blowfish_key) || empty($params['blowfish_key']) || strlen($field) < 3 || substr($field, 0, 1) != 'B')
        return $field;

    if (substr($field, 1, 1) == '-') {

        $field = trim(func_bf_decrypt(substr($field, 2), $init_blowfish_key));
        $init_crc32 = substr($field, -8);
        $field = substr($field, 0, -8);

    } else {

        $init_crc32 = substr($field, 1, 8);

        $field = trim(func_bf_decrypt(substr($field, 9), $init_blowfish_key));

    }

    $crc32 = crc32(md5($field));

    if (crc32('test') != -662733300 && $crc32 > 2147483647)
        $crc32 -= 4294967296;

    $crc32 = dechex(abs($crc32));

    $crc32 = str_repeat('0', 8-strlen($crc32)).$crc32;

    return "B-".func_bf_crypt($field.$crc32, $params['blowfish_key']);
}

/**
 * Crypt field
 */
function crypt_field($field, $current_blowfish_key)
{
    if (empty($current_blowfish_key))
        return $field;

    $crc32 = crc32(md5($field));

    if (crc32('test') != -662733300 && $crc32 > 2147483647)
        $crc32 -= 4294967296;

    $crc32 = dechex(abs($crc32));
    $crc32 = str_repeat('0', 8-strlen($crc32)).$crc32;

    return "B-".func_bf_crypt($field.$crc32, $current_blowfish_key);
}

/**
 * Check all encrypted data
 */
function check_crypted_data($current_blowfish_key)
{
    global $xcart_dir, $bf_crypted_tables, $blowfish;

    include_once $xcart_dir . '/include/func/func.core.php';

    x_load('db','files','compat','crypt');

    include_once $xcart_dir.'/include/blowfish.php';

    if ($current_blowfish_key !== false)
        $blowfish_key = $current_blowfish_key;

    if (empty($bf_crypted_tables) || empty($blowfish) || empty($blowfish_key))
        return false;

    $tbls = myquery("SHOW TABLES");

    if (!$tbls)
        return false;

    $i = 0;
    while ($tbl = mysql_fetch_row($tbls)) {
        $tbl = preg_replace("/^xcart_/S", '', $tbl[0]);

        if (!isset($bf_crypted_tables[$tbl]))
            continue;

        $data = myquery("SELECT ".$bf_crypted_tables[$tbl]['key'].", ".implode(", ", $bf_crypted_tables[$tbl]['fields'])." FROM xcart_".$tbl." WHERE 1 ".$bf_crypted_tables[$tbl]['where']);
        if (!$data)
            continue;

        while ($row = mysql_fetch_assoc($data)) {
            $key = array_shift($row);

            if (empty($row) || empty($key))
                continue;

            foreach ($row as $fname => $field) {
                if (substr($field, 0, 1) != 'B')
                    continue;

                if (substr($field, 1, 1) == '-') {
                    $field = trim(func_bf_decrypt(substr($field, 2), $blowfish_key));
                    $init_crc32 = substr($field, -8);
                    $field = substr($field, 0, -8);
                    $crc32 = func_crc32(md5($field));

                } else {
                    $init_crc32 = substr($field, 1, 8);
                    $field = trim(func_bf_decrypt(substr($field, 9), $blowfish_key));
                    $crc32 = func_crc32($field);
                }

                if ($init_crc32 != $crc32)
                    return false;

                if (++$i % 10 == 0) {
                    echo ". ";
                    flush();
                }
            }
        }

        mysql_free_result($data);
    }

    mysql_free_result($tbls);

    return true;
}

function config_get($dir)
{
    static $var_defs = array (
        'sql_host', 'sql_user', 'sql_db', 'sql_password',
        'xcart_http_host', 'xcart_https_host', 'xcart_web_dir',
        'license'
    );

    static $config_files = array (
        'config.php', 'config.local.php'
    );

    $cnf = false;

    foreach ($config_files as $f) {
        $file = $dir.'/'.$f;

        $fp = @fopen($file, 'r');
        if (!$fp)
            continue;

        while (!feof($fp)) {
            $buffer = fgets($fp, 4096);
            foreach ($var_defs as $var) {
                $regexp = '!^\s*\$'.preg_quote($var).'\s*=\s*[\'"](.+)[\'"];!';

                if (preg_match($regexp, $buffer, $matches)) {
                    $cnf[$var] = $matches[1];
                }
            }
        }

        fclose($fp);
    }

    return $cnf;
}

function check_password($password)
{
    if (preg_match('/[a-z]/is',$password) && preg_match('/[0-9]/s',$password))
        return false;
    else
        return true;
}

function get_skins_names()
{
    global $schemes_repository;

    $file_list = array();
    if (is_file($schemes_repository.'/templates/skins.ini'))
        $file_list = parse_ini_file($schemes_repository.'/templates/skins.ini');
    if ($dir = @opendir($schemes_repository.'/templates')) {
        while (($file = readdir($dir)) !== false) {
            if ($file!="." && $file!=".." && @is_dir($file)) {
                if (empty($file_list[$file]))
                    $file_list[$file] = ucwords(strtolower(str_replace('_'," ",$file)));
            }
        }
        closedir($dir);
    }
    return $file_list;
}

###############################################################
/**
 * Modules goes here
 */
###############################################################

// start: Default module
// Shows Terms & Conditions

function module_default(&$params)
{
    global $error, $templates_directory;
    global $installation_auth_code;
    global $installation_product;
    global $install_lng;
?>
<center>

<?php
    if (!file_exists('./COPYRIGHT')) {
        fatal_error(lng_get('no_license_file'));
        exit;
     }
?>
<div id="copyright_notice">
<?php
ob_start();
require './COPYRIGHT';
$tmp = ob_get_contents();
ob_end_clean();
echo nl2br(htmlspecialchars($tmp));
?>
</div>

<?php if (is_installed()) { ?>

<table>
<tr>
    <td align="right">
        <strong><?php echo_lng('auth_code'); ?>:&nbsp;</strong>
    </td>
    <td>
        <input type="text" name="auth_code" size="20" />
        <input type="hidden" name="params[force_current]" value="2" />
    </td>
</tr>
</table>

<div class="auth-code-note">
    <?php echo_lng('auth_code_note'); ?>
</div>

<?php } else { ?>

<input type="hidden" name="params[auth_code]" value="<?php echo func_crypt_auth_code($installation_auth_code); ?>" />

<?php } ?>

<table>
<tr>
    <td valign="middle"><input id="agree" type="checkbox" name="params[agree]" /></td>
    <td valign="middle"><label for="agree"><?php echo_lng('i_accept_license'); ?></label></td>
</tr>
</table>

</center>
<br />

<?php
    return false;
}

/**
 * 'next' button handler. checks 'agree' button checked
 */

function module_default_js_next()
{
?>
    function step_next() {
        if (document.getElementById('agree').checked) {
            return true;
        } else {
            alert("<?php echo_lng_js('mod_license_alert'); ?>");
        }
        return false;
    }
<?php
}

/**
 * end: Default module
 */

// start: Check_cfg module
// Get info about current php configuration

function module_check_cfg(&$params)
{
    global $min_ver, $error, $check_permissions;

    $check_errors = func_get_env_srv_state();

    $error = !empty($check_errors['env']) || !empty($check_errors['critical']);
    $check_failed = $error || !empty($check_errors['noncritical']);
?>
<script type="text/javascript">
//<![CDATA[
var prefix = '<?php $prefix = ($error) ? "failed" : "passed"; echo $prefix; ?>';
if (document.getElementById('check_status')) {
    document.getElementById('check_status').innerHTML = '<?php echo_lng("check_cfg_".$prefix); ?>';
    document.getElementById('check_status').style.color = (prefix == 'passed') ? '#008000' : '#a10000';
}
//]]>
</script>
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="60%" valign="top">

<table cellspacing="0" cellpadding="4">

<tr>
    <td colspan="3" class="check_cfg_subhead"><?php echo_lng('env_checking'); ?></td>
</tr>

<tr class="clr3">
    <td align="center"><b><?php echo_lng('verification_steps'); ?></b></td>
    <td width="1%">&nbsp;</td>
    <td width="1%" align="center"><b><?php echo_lng('status'); ?></b></td>
</tr>

<?php

    // Check integrity of required files.

    $status = !empty($check_errors['noncritical']['int_check_files']["type"]) ?  $check_errors['noncritical']['int_check_files']["type"] : true;
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('int_check_files'); ?> ...</td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status) ?></td>
</tr>

<?php
    if (!empty($check_permissions) && is_array($check_permissions)) {
?>

<tr><td colspan="3">&nbsp;</td></tr>

<tr class="clr3">
    <td align="center"><b><?php echo_lng('checking_file_permissions'); ?></b></td>
    <td width="1%">&nbsp;</td>
    <td width="1%" align="center"><b><?php echo_lng('status'); ?></b></td>
</tr>

<?php
        foreach ($check_permissions as $entity_name => $entity) {
            if (empty($entity) || !is_array($entity) || empty($entity_name)) {
                continue;
            }
?>
            <tr class="<?php cycle_class('clr'); ?>">
                <td align="left"><?php echo_lng('perm_check_entity', 'entity_type', $entity['type'], 'entity', $entity_name, 'entity_mode', $entity['mode']); ?> ...</td>
                <td width="1%">-</td>
                <td width="1%" align="center"><?php echo status($check_permissions[$entity_name]['check_result']) ?></td>
            </tr>
<?php
        }
    }
?>

<tr><td colspan="3">&nbsp;</td></tr>

<tr>
    <td colspan="3" class="check_cfg_subhead"><?php echo_lng("cheÓking_results"); ?></td>
</tr>

<tr class="clr3">
    <td align="center"><?php echo_lng('critical_dependencies'); ?></td>
    <td width="1%">&nbsp;</td>
    <td width="1%" align="center"><?php echo_lng('status'); ?></td>
</tr>

<?php
/**
 * PHP Version must be not less than $min_ver
 */

    $ver = phpversion();
    $status = !isset($check_errors['critical']['dep_php_ver']);
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td nowrap="nowrap" align="left"><?php echo_lng('php_ver_min','version',$min_ver); ?> ... <?php echo $ver ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status) ?></td>
</tr>

<?php

/**
 * PRCE extension must be On
 */

    $status = !isset($check_errors['critical']['dep_pcre']);
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('pcre_extension_is'); ?> ... <?php echo on_off($status) ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status) ?></td>
</tr>

<?php

/**
 * PHP Safe mode must be Off
 */

    $status = !isset($check_errors['critical']['dep_safe_mode']);
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('php_safe_mode_is'); ?> ... <?php echo on_off(!$status) ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status) ?></td>
</tr>

<?php

/**
 * ini_set must be allowed
 */

    $status = !isset($check_errors['critical']['dep_ini_set']);
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('php_ini_set_presence'); ?> ... <?php if ($status) { echo lng_get('bool_on'); } else { echo lng_get('bool_off'); } ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status) ?></td>
</tr>

<?php

/**
 * File uploads must be On
 */

    $status = !isset($check_errors['critical']['dep_uploads']);
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('php_fileuploads_are'); ?> ... <?php echo on_off($status) ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status) ?></td>
</tr>

<?php

/**
 * Check magic_quotes_sybase
 */

    $status = !isset($check_errors['critical']['magic_quotes_sybase']);
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('magic_quotes_sybase_is'); ?> ... <?php echo on_off(!$status) ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status) ?></td>
</tr>

<?php

/**
 * Check sql.safe_mode
 */

    $status = !isset($check_errors['critical']['sql_safe_mode']);
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('sql_safe_mode_is'); ?> ... <?php echo on_off(!$status) ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status) ?></td>
</tr>

<?php
/**
 * Check memory_limit
 */

    $status = isset($check_errors['critical']['memory_limit']) ? $check_errors['critical']['memory_limit'] : true;
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('memory_limit_is'); ?> ... <?php echo number_format(func_convert_to_byte(bool_get('memory_limit')), 0, '', '.') . ' '; echo_lng('bytes'); ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status === true) ?></td>
</tr>

<?php
    if (isset($check_errors['critical']['memory_limit_set'])) {
/**
 * Check memory_limit set
 */

        $status = !isset($check_errors['critical']['memory_limit_set']);
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('memory_limit_set'); ?> ... <?php echo on_off($status) ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status) ?></td>
</tr>

<?php
    }

/**
 * MySQL functions must present
 */

    $status = !isset($check_errors['critical']['dep_mysql']);
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('php_mysql_support_is'); ?> ... <?php echo on_off($status) ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status) ?></td>
</tr>

<tr><td colspan="3">&nbsp;</td></tr>

<tr class="clr3">
    <td align="center"><?php echo_lng('non_critical_dependencies'); ?></td>
    <td width="1%">&nbsp;</td>
    <td width="1%" align="center"><b><?php echo_lng('status'); ?></b></td>
</tr>
<?php

    if (isset($check_errors['noncritical']['memory_limit'])) {
/**
 * Check memory_limit
 */
        $status = isset($check_errors['noncritical']['memory_limit']) ? $check_errors['noncritical']['memory_limit'] : true;
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('memory_limit_is'); ?> ... <?php echo number_format(func_convert_to_byte(bool_get('memory_limit')), 0, '', '.') . ' '; echo_lng('bytes'); ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status === true ? 'warning' : true) ?></td>
</tr>

<?php
    }

    if (isset($check_errors['noncritical']['memory_limit_none'])) {
/**
 * Check if memory limitation is disabled
 */
        $status = $check_errors['noncritical']['memory_limit_none'];
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('memory_limit_is'); ?> ... <?php echo on_off(false) ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status) ?></td>
</tr>

<?php
    }

    if (isset($check_errors['noncritical']['memory_limit_set'])) {
/**
 * Check memory_limit set
 */
        $status = !isset($check_errors['noncritical']['memory_limit_set']);
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('memory_limit_set'); ?> ... <?php echo on_off($status) ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status('warning') ?></td>
</tr>

<?php
    }
    $status = !isset($check_errors['noncritical']['dep_disable_funcs']);
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('php_disabled_funcs'); ?> ... <?php echo ($status ? lng_get('php_disabled_funcs_none') : $check_errors['noncritical']['dep_disable_funcs']) ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status) ?></td>
</tr>

<?php
    $status = !isset($check_errors['noncritical']['dep_upl_max']);
    $res = ini_get('upload_max_filesize');
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('php_upload_maxsize_is'); ?> ... <?php echo $res ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status) ?></td>
</tr>

<?php
    $status = !isset($check_errors['noncritical']['dep_fopen']);
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('php_test_fopen'); ?> ... <?php echo on_off($status) ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status) ?></td>
</tr>

<?php
    $status = !isset($check_errors['noncritical']['dep_gd']);
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('php_gd'); ?> ... <?php echo ($status ? lng_get('status_ok') : $check_errors['noncritical']['dep_gd']); ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status ? $status : 'warning'); ?></td>
</tr>

<?php
    $status = !isset($check_errors['noncritical']['dep_blowfish']);
?>
<tr class="<?php cycle_class('clr'); ?>">
    <td align="left"><?php echo_lng('php_test_blowfish'); ?> ... <?php echo ($status ? lng_get('status_ok') : $check_errors['noncritical']['dep_blowfish']); ?></td>
    <td width="1%">-</td>
    <td width="1%" align="center"><?php echo status($status); ?></td>
</tr>

</table>

</td>

<?php

    if($check_failed) {
?>
<!-- Check results pane -->
<td width="30"><img src="skin/common_files/images/spacer.gif" width="30" height="1" alt =""/></td>
<td width="40%" id="server_check_results_pane">

<?php
    if (isset($check_errors['env']) && !empty($check_errors['env'])) {
?>
        <h2 class="cfg-error-header"><?php echo_lng('env_checks_failed'); ?></h2>
        <div class="cfg-error-details">
<?php
        foreach ($check_errors['env'] as $name => $value) {
            if (is_array($value)) {
                $value = func_get_check_error_value($name, $value);
            }
            func_show_check_err($name, $value);
        }
        echo "</div>\n";
    }

    if (isset($check_errors['critical']) && !empty($check_errors['critical'])) {
?>
        <h2 class="cfg-error-header"><?php echo_lng('critical_deps_failed'); ?></h2>
        <div class="cfg-error-details">
<?php
        foreach ($check_errors['critical'] as $name => $value) {
            func_show_check_err($name, $value);
        }
        echo "</div>\n";
    }

    if (isset($check_errors['noncritical']) && !empty($check_errors['noncritical'])) {
?>
        <h2 class="cfg-warning-header"><?php echo_lng('non_critical_deps_failed'); ?></h2>
        <div class="cfg-error-details">
<?php
        foreach ($check_errors['noncritical'] as $name => $value) {
            if (is_array($value)) {
                $value = func_get_check_error_value($name, $value);
            }
            func_show_check_err($name, $value);
        }
        echo "</div>\n";
    }

    if($error) {
        echo '<div class="cfg-error-details">' . lng_get("check_env_srv_settings_js", "current", $_POST['current']) . '</div>';
    }

    if ($check_failed) {
?>
        <div class="error-report">
            <div class="error-report-content">
<?php echo_lng('test_found_errors'); ?><br /><br /><input type="submit" name="send_problem_report" value="<?php echo_lng('send_report'); ?>"/>
            </div>
        </div>
<?php
    }
?>

</td>
<?php
    }
?>

<!-- /Check results pane -->
</tr>

</table>

<?php
    return false;
}

/**
 * end: Check_cfg module
 */

// start: Cfg_install_db module
// Get mysql server info and check it before installing db

function module_cfg_install_db(&$params)
{
    global $error, $schemes_repository;
    global $xcart_dir;

    if (!isset($params['mysqlhost'])) {
        $mysqlhost = 'localhost';
        $mysqluser = '';
        $mysqlpass = '';
        $mysqlbase = 'xcart';
?>
<span id="step_title"><?php echo_lng('install_web_mysql'); ?>:</span>
<br /><br />
<table width="100%" border="0" cellpadding="4">

<tr class="<?php cycle_class('clr'); ?>">
    <td width="70%"><?php echo_lng('install_http_name'); ?></td>
    <td><input type="text" name="params[xcart_http_host]" size="30" value="<?php echo $_SERVER['HTTP_HOST']; ?>" /></td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_https_name'); ?></td>
    <td><input type="text" name="params[xcart_https_host]" size="30" value="<?php echo $_SERVER['HTTP_HOST']; ?>" /></td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_webdir'); ?></td>
    <td><input type="text" name="params[xcart_web_dir]" size="30" value="<?php echo preg_replace("/\/install\.php$/", '', $_SERVER['PHP_SELF']); ?>" /></td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_mysqlhost'); ?></td>
    <td><input type="text" name="params[mysqlhost]" size="30" value="<?php echo $mysqlhost; ?>" /></td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_mysqldb'); ?></td>
    <td><input name="params[mysqlbase]" size="30" type="text" value="<?php echo $mysqlbase; ?>" /></td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_mysqluser'); ?></td>
    <td><input name="params[mysqluser]" size="30" type="text" value="<?php echo $mysqluser; ?>" /></td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_mysqlpass'); ?></td>
    <td><input name="params[mysqlpass]" size="30" type="text" value="<?php echo $mysqlpass; ?>" /></td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td width="70%"><?php echo_lng('install_email'); ?></td>
    <td><input type="text" name="params[company_email]" size="30" value="" /></td>
</tr>

</table>

    <input type="hidden" name="params[session_name]" size="30" value="<?php echo 'xid_' . substr(md5(uniqid(rand())), 0, 5); ?>" />

<br />
<?php
        return true;
    } else {
/**
 * Now trying to check if there is already database named $params['mysqlbase']
 */
        $ck_res = 1;

        $mylink = @mysql_connect($params['mysqlhost'], $params['mysqluser'], $params['mysqlpass']);

        if ($mylink) {
            $mysql_version = 'unknown';
            if (preg_match("/^(\d+\.\d+\.\d+)/", mysql_get_server_info(), $match)) {
                $mysql_version = $match[1];
            }

            if (version_compare($mysql_version, '5.0.50') === 0 || version_compare($mysql_version, '5.0.51') === 0) {
                warning_error(lng_get('install_mysql_version_alert','version',$mysql_version));
            }

            // Check min mysql version
            if (version_compare($mysql_version, '3.23.0') < 0) {
                $ck_res &= fatal_error(lng_get('install_mysql_min_version'));
            }
        }

        if (!$mylink) {
            $ck_res &= fatal_error(lng_get('error_connect'));
        }
        else if (!@mysql_select_db($params['mysqlbase'])) {
            // Attempt to create database
            $db_create_success = runquery("CREATE DATABASE `" . addslashes($params['mysqlbase'] . "`"));
            if (!$db_create_success)
                $ck_res &= fatal_error(lng_get('error_select_db', 'db', $params['mysqlbase']));
        }
        else if (!is_writable('config.php')) {
            $ck_res &= fatal_error(lng_get('error_check_write_config'));
        }
        else if (!preg_match("/^[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z](?:[a-z0-9-]*[a-z0-9])?$/is", $params['company_email'])) {
            $ck_res &= fatal_error(lng_get('error_check_email'));
        }
        else {
            $mystring = '';
            $first = true;

            $res = @mysql_list_tables($params['mysqlbase']);

            while ($row = @mysql_fetch_row($res)) {
                $ctable = $row[0];
                if ($ctable == 'xcart_products')
                    warning_error(lng_get('warning_db_tables_exists'));
            }

            @mysql_close ($mylink);
        }

        $country_languages = get_lang_names_re($xcart_dir.'/sql',
            '!^xcart_language_(..)\.sql$!S',$params['lngcode'], 'language');

        $country_states = get_lang_names_re($xcart_dir.'/sql',
            '!^states_(..)\.sql$!S',$params['lngcode'],'country');

        $country_preconf = get_lang_names_re($xcart_dir.'/sql',
            '!^xcart_conf_(..)\.sql$!S',$params['lngcode'],'country');

        if (count($country_preconf) > 1) {
            $country_preconf[''] = '&nbsp;'; // no preconfiguration by default
            asort($country_preconf);
        }

        if (!empty($params['xcart_http_host']))
            $params['xcart_http_host'] = strtolower($params['xcart_http_host']);
        if (!empty($params['xcart_https_host']))
            $params['xcart_https_host'] = strtolower($params['xcart_https_host']);
?>

<table width="100%" cellpadding="4">

<tr class="<?php cycle_class('clr'); ?>">
    <td width="70%"><?php echo_lng('install_http_name'); ?></td>
    <td><?php echo $params['xcart_http_host'] ?></td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_https_name'); ?></td>
    <td><?php echo $params['xcart_https_host'] ?></td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_webdir'); ?></td>
    <td><?php echo $params['xcart_web_dir'] ?></td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_mysqlhost'); ?></td>
    <td><?php echo htmlspecialchars($params['mysqlhost'], ENT_QUOTES) ?></td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_mysqldb'); ?></td>
    <td><?php echo $params['mysqlbase'] ?></td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_mysqluser'); ?></td>
    <td><?php echo htmlspecialchars($params['mysqluser'], ENT_QUOTES) ?></td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_mysqlpass'); ?></td>
    <td><?php echo htmlspecialchars($params['mysqlpass'], ENT_QUOTES) ?></td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_email'); ?></td>
    <td><?php echo $params['company_email'] ?></td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_languages'); ?></td>
    <td>
    <select name="params[languages][]" multiple="multiple" size="4">
<?php
foreach ($country_languages as $code=>$name) {
    printf("<option value=\"%s\"%s>%s</option>\n", $code,
        ($code == $params['lngcode']) ? " selected=\"selected\"" : "",
        $name);
}
?>
    </select>
    </td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_states'); ?></td>
    <td>
    <select name="params[states][]" multiple="multiple" size="5">
<?php
foreach ($country_states as $code=>$name) {
    printf("<option value=\"%s\">%s</option>\n", $code, $name);
}
?>
    </select>
    </td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_demodata'); ?></td>
    <td>
    <select name="params[demo]">
        <option value="1"><?php echo_lng('lbl_yes'); ?></option>
        <option value="0"><?php echo_lng('lbl_no'); ?></option>
    </select>
    </td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_configuration'); ?></td>
    <td>
    <select name="params[conf]">
<?php
foreach ($country_preconf as $code=>$name) {
    printf("<option value=\"%s\">%s</option>\n", $code, $name);
}
?>
    </select>
    </td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_email_as_login'); ?></td>
    <td><input type="checkbox" id="email_as_login" name="params[email_as_login]" checked="checked" value="Y" /></td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_update_config'); ?></td>
    <td><input type="checkbox" id="config_only" name="params[config_only]" value="Y" onclick="javascript: var o = document.getElementById('previous_blowfish_key'); if (o) o.disabled = !this.checked;" /></td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_blowfish_key'); ?></td>
    <td>
        <input type="text" id="previous_blowfish_key" name="params[previous_blowfish_key]" value="" size="32" maxlength="32" />
<script type="text/javascript">
//<![CDATA[
if (window.addEventListener)
    window.addEventListener('load', new Function('', "var o = document.getElementById('previous_blowfish_key'); var c = document.getElementById('config_only'); if (o && c) o.disabled = !c.checked;"), false);
else if (window.attachEvent)
     window.attachEvent('onload', new Function('', "var o = document.getElementById('previous_blowfish_key'); var c = document.getElementById('config_only'); if (o && c) o.disabled = !c.checked;"));
//]]>
</script>
    </td>
</tr>

<tr class="<?php cycle_class('clr'); ?>">
    <td><?php echo_lng('install_store_images_in'); ?></td>
    <td>
    <select name="params[images_location]">
        <option value="FS" selected="selected"><?php echo_lng('install_store_images_fs'); ?></option>
        <option value=""><?php echo_lng('install_store_images_db'); ?></option>
    </select>
    </td>
</tr>

</table>

<br />
<?php
        $error = !$ck_res;
        return false;
    }
}

function module_cfg_install_db_js_next()
{
?>
    function step_next() {
        for (var i = 0; i < document.ifrm.elements.length; i++) {
            if (document.ifrm.elements[i].name.search('mysqlhost') != -1) {
                if (document.ifrm.elements[i].value == '') {
                    alert ("<?php echo_lng_js('install_mysqlhost_alert'); ?>");
                    return false;
                }
            }

            if (document.ifrm.elements[i].name.search('mysqluser') != -1) {
                if (document.ifrm.elements[i].value == '') {
                    alert ("<?php echo_lng_js('install_mysqluser_alert'); ?>");
                    return false;
                }
            }

            if (document.ifrm.elements[i].name.search('mysqlbase') != -1) {
                if (document.ifrm.elements[i].value == '') {
                    alert ("<?php echo_lng_js('install_mysqldb_alert'); ?>");
                    return false;
                }
            }
        }
        return true;
    }
<?php
}

/**
 * end: Cfg_install_db module
 */

/**
 * start: Install_db module
 */

function module_install_db(&$params)
{
    global $error;
    global $installation_auth_code;
?>
</td>
</tr>
</table>

<script type="text/javascript" language="javascript">
//<![CDATA[
scrollDown();
//]]>
</script>

<?php
    $ck_res = 1;

    $mylink = @mysql_connect($params['mysqlhost'], $params['mysqluser'], $params['mysqlpass']);
    if (!$mylink) {
        $ck_res = $ck_res && fatal_error(lng_get('error_unexp_connect'));

    } elseif (!@mysql_select_db($params['mysqlbase'])) {
        $ck_res = $ck_res && fatal_error(lng_get('error_unexp_select_db', 'db', $params['mysqlbase']));

    } elseif (!is_writable('config.php')) {
        $ck_res = $ck_res && fatal_error(lng_get('error_check_write_config'));

    } else {

        $old_blowfish_key = false;
        if (!empty($params['config_only'])) {
            echo "<br /><b>".lng_get('check_crypted_data')."</b>...\n";
            flush();
            $res = check_crypted_data(empty($params['previous_blowfish_key']) ? false : $params['previous_blowfish_key']);
            echo status($res)."<br />\n";

            if (!$res) {
                fatal_error(lng_get(empty($params['previous_blowfish_key']) ? "check_crypted_data_failed" : "check_w_oldkey_crypted_data_failed"));

            } elseif (!empty($params['previous_blowfish_key'])) {
                $old_blowfish_key = $blowfish_key = $params['previous_blowfish_key'];
            }

            $ck_res = $ck_res && $res;

        }

        if ($ck_res) {

            // Generate new Blowfish key
            if ($old_blowfish_key) {
                $params['blowfish_key'] = $old_blowfish_key;

            } else {
                mt_srand(XC_TIME);
                $params['blowfish_key'] = md5(mt_rand(0, XC_TIME));
            }

            // Updating config.php file
            echo "<br /><b>".lng_get('updating_config_file')."</b>...\n"; flush();

            $res = change_config($params, (bool)$old_blowfish_key);
            echo status($res)."<br />\n";

            if (!$res) {
                fatal_error(lng_get('error_cannot_open_config'));
            }

            $ck_res = $ck_res && $res;

            if (empty($params['config_only'])) {
                $ck_res = $ck_res && do_install_db($params);
            }

        }
    }
?>

<table class="TableTop" width="100%" border="0" cellspacing="0" cellpadding="0">

<tr>
    <td>
<script type="text/javascript" language="javascript">
//<![CDATA[
    loaded = true;
//]]>
</script>

<?php
    $error = !$ck_res;
    return false;
}

function do_install_db(&$params)
{
    global $installation_auth_code;
    global $config, $xcart_dir, $sql_tbl, $str_out, $images_step;
    global $active_modules, $data_caches, $var_dirs;
    global $data_caches, $memcache;

    echo "<br /><b>".lng_get('creating_tables')."</b><br />\n";

    $ck_res = true;

    if ($ck_res) $ck_res = query_upload('sql/dbclear.sql');
    if ($ck_res) $ck_res = query_upload('sql/xcart_tables.sql');

    if ($ck_res) echo "<br /><b>".lng_get('importing_data')."</b><br />\n"; flush();

    if ($ck_res) $ck_res = query_upload('sql/xcart_data.sql');

    // Importing languages

    if ($ck_res) {
        if (empty($params['languages']))
            $params['languages'] = array($params['lngcode']);
        echo "<br /><b>".lng_get('importing_languages')."</b><br />\n"; flush();
        if (is_array($params['languages'])) {
            foreach ($params['languages'] as $_k=>$lng_code)
                if ($ck_res) $ck_res = query_upload('sql/xcart_language_'.$lng_code.'.sql');
        }
    }

    // Importing states

    if ($ck_res && !empty($params['states'])) {
        echo "<br /><b>".lng_get('importing_states')."</b><br />\n"; flush();
        if (is_array($params['states'])) {
            foreach($params['states'] as $_k=>$country_code) {
                if ($ck_res) $ck_res = query_upload('sql/states_'.$country_code.'.sql');
            }
        }
    }

    // Importing sample data

    if ($ck_res && $params['demo'] == 1) {
        echo "<br /><b>".lng_get('importing_demodata')."</b><br />\n"; flush();

        $demo_files = array('sql/xcart_demo.sql','sql/xcart_demo_'.$params['conf'].'.sql');
        foreach ($demo_files as $_file) {
            if (!file_exists($xcart_dir.'/'.$_file)) continue;
            $ck_res = $ck_res && query_upload($_file);
            if (!$ck_res) break;
        }
    }

    // Apply pre-configured settings to selected country

    if ($ck_res && !empty($params['conf'])) {
        echo "<br /><b>".lng_get('importing_data')."</b><br />\n"; flush();

        $ck_res = $ck_res && query_upload('sql/xcart_conf_'.$params['conf'].'.sql');
    }

    if ($ck_res && !empty($params['company_email'])) {
        $ck_res = $ck_res && runquery("UPDATE xcart_config SET value='$params[company_email]' WHERE name in ('orders_department','support_department','newsletter_email','users_department','site_administrator')");
        if ($params['email_as_login']) {
            $ck_res = $ck_res && runquery("UPDATE xcart_customers SET email='$params[company_email]', login='$params[company_email]'");
        } else {
            $ck_res = $ck_res && runquery("UPDATE xcart_config SET value='N' WHERE name='email_as_login'");
            $ck_res = $ck_res && runquery("UPDATE xcart_customers SET email='$params[company_email]', login=username");
        }
    } else {
        $ck_res = $ck_res && runquery("UPDATE xcart_customers SET login=username");
    }

    // Move images to the file system

    if ($ck_res && $params['images_location'] == "FS") {
        echo "<br /><b>".lng_get('moving_images_to_fs')."</b><br />\n"; flush();

        include $xcart_dir . '/init.php';

        x_load('backoffice','image');

        // process N images per pass
        $images_step = 50;

        foreach (array_keys($config['available_images']) as $avail_type) {
            $str_out = '';
            $moved = func_move_images($avail_type, array('location' => 'FS'));

            if (!$moved) {
                $ck_res = false;
                break;
            }
        }

        runquery("UPDATE xcart_setup_images SET location='FS'");
        func_build_quick_flags();
        func_data_cache_get('setup_images', array(), true);
    }

    if (!$ck_res) {
        fatal_error(lng_get('fatal_error_install_db'));

    } else {
        recrypt_data($params);
        @myquery("REPLACE INTO xcart_config (value,name,defvalue,variants) VALUES ('".XC_TIME."', 'bf_generation_date', '', '')");

        $field = 'TEST';
        $crc32 = crc32(md5($field));

        if (crc32('test') != -662733300 && $crc32 > 2147483647)
            $crc32 -= 4294967296;

        $crc32 = dechex(abs($crc32));
        $field .= str_repeat('0', 8-strlen($crc32)) . $crc32;

        @myquery("REPLACE INTO xcart_config (name, value) VALUES ('crypted_data', 'B-" . func_bf_crypt($field, $params['blowfish_key']) . "')");
        @myquery("REPLACE INTO xcart_config (name, value) VALUES ('db_backup_date', '".XC_TIME."')");

        $params['db_is_installed'] = 'Y';
    }

    return $ck_res;
}

/**
 * end: Install_db module
 */

// start: Cfg_install_dirs module
// Get color/layout settings

function module_cfg_install_dirs(&$params)
{
    global $error;

    $altSkins = func_get_schemes();

?>

<script type="text/javascript">
//<![CDATA[
var previewShots = [];
<?php
foreach ($altSkins as $skinId => $skin_info) {
    echo ('previewShots[\'' . $skinId . '\']=\'.' . $skin_info['screenshot'] . '\';' . "\n");
}
?>
//]]>
</script>

<table width="100%" cellpadding="4">

<tr>
    <td width="50%" valign="top" height="210">
        <?php echo_lng('select_layout'); ?><br /><br />
        <img id="screenshot" src="skin/common_files/images/spacer.gif" style="border: solid 1px #afb9c9;" alt="" />
<script type="text/javascript">
//<![CDATA[
document.getElementById('screenshot').src=previewShots['10_2-columns'];
//]]>
</script>
    </td>
    <td width="50%" valign="top" align="left" style="padding-left:8px;">
    <select name="params[layout]" onchange="javascript:document.getElementById('screenshot').src=previewShots[this.value];">
<?php
foreach ($altSkins as $skinId => $skin_info) {
    echo "\t\t<option value=\"$skinId\">" . htmlspecialchars($skin_info['name'], ENT_QUOTES) . "</option>\n";
}
?>
    </select>
    </td>
</tr>

</table>
<br />
<?php
}
/**
 * end: Cfg_install_dirs module
 */

/**
 * start: Install_dirs module
 */

function module_install_dirs(&$params)
{
    global $directories_to_create, $templates_repository, $schemes_repository, $error;
    global $xcart_dir;
    global $templates_directory;
    global $sql_tbl;
    global $data_caches, $var_dirs, $memcache;

    $altSkins = func_get_schemes();

    $skin_info = @$altSkins[$params['layout']];

    include $xcart_dir . '/init.php';

?>
</td>
</tr>
</table>

<script type="text/javascript" language="javascript">
//<![CDATA[
scrollDown();
//]]>
</script>

<?php

    $ck_res = 1;

    if (empty($params['flags']['skip_dirs'])) {
        echo "<br /><b>" . lng_get('creating_directories')."</b><br />\n";

        $ck_res = $ck_res && create_dirs($directories_to_create);
    }

    $ck_res = $ck_res && myquery('UPDATE xcart_config SET value=\'' . $params['layout'] . '\' WHERE name=\'alt_skin\' AND category=\'\'');

    if (!$ck_res) {

        fatal_error(lng_get('error_creating_directories'));

    } else {

        // Clean var/templates_c and var/cache directories
        $clean_dirs = array(
            './var/templates_c',
            './var/cache',
        );

        foreach($clean_dirs as $cd) {

            if (!@is_dir($cd) || !file_exists($cd))
                continue;

            $d = @opendir($cd);

            if (!$d)
                continue;

            while ($f = readdir($d)) {

                if ($f == '.' || $f == '..')
                    continue;

                @unlink($cd . XC_DS . $f);
            }

            closedir($d);
        }

        $cnf = config_get($xcart_dir);

        $location = 'home.php';

        if (!empty($cnf['xcart_web_dir']))
            $location = 'http://'.$cnf['xcart_http_host'].$cnf['xcart_web_dir'].DIR_CUSTOMER."/home.php";

        $location .= "?is_install_preview=Y";
?>
<a name="preview"></a>
<div style="text-align: center; margin-bottom: 15px;">
<h3><?php echo_lng('color_layout_preview'); ?> (<a href="javascript:void(0);" onclick="javascript: if (loaded) refreshPreview();"><?php echo_lng('click_to_refresh'); ?></a>)</h3>
<iframe id="preview_frame" src="" scrolling="auto" frameborder="0" style="border: 1px solid black; width: 90%; height: 400px;"></iframe>
</div>
<?php
    }
?>

<table class="TableTop" width="100%" cellspacing="0" cellpadding="0">

<tr>
    <td>
<input type="hidden" name="ck_res" value="<?php echo (int)$ck_res ?>" />

<br />

<script type="text/javascript" language="javascript">
//<![CDATA[
    var previewObj = document.getElementById('preview_frame');
    var previewLoc = '<?php echo $location; ?>';

    function refreshPreview() {
        var _ts = new Date();
        if(previewObj)
            previewObj.src = previewLoc + '&amp;' + _ts.valueOf();
        return true;
    }

    loaded = true;
    refreshPreview();
//]]>
</script>

<?php
    $error = !$ck_res;
    return false;
}

/**
 * end: Install_dirs module
 */

/**
 * start: Cfg_enable_paypal module
 */

function module_cfg_enable_paypal(&$params)
{
?>
<?php echo_lng('paypal_question'); ?>
&nbsp;
<select name="params[force_current]">
    <option value="8"><?php echo_lng('lbl_yes'); ?></option>
    <option value="9" selected="selected"><?php echo_lng('lbl_no'); ?></option>
</select>
<br /><br /><br />
<?php
}

/**
 * end: Cfg_enable_paypal module
 */

/**
 * start: Enable_paypal module
 */

function module_enable_paypal(&$params)
{
?>
<p><?php message(lng_get('install_web_paypal')); ?></p>

<table width="100%" border="0" cellpadding="4">

<tr class="clr">
    <td width="70%"><?php echo_lng('install_paypal_account'); ?></td>
    <td><input type="text" name="params[paypal_account]" size="30" value="" /></td>
</tr>

</table>

<?php echo_lng('install_web_paypal_comment'); ?>

<br /><br />
<?php
}

/**
 * end: Enable_paypal module
 */

/**
 * start: Install_done module
 */

function func_success()
{
    global $xcart_package;
    global $installation_auth_code;
    global $install_language_charset;
    global $params;
    global $xcart_dir;
    global $installation_product;
    global $smarty, $mail_smarty;
    global $sql_tbl, $config;
    global $data_caches, $var_dirs, $memcache;

    srand(XC_TIME);
    $php_exec_mode = func_get_php_execution_mode();

    list ($success_rename, $install_name) = func_rename_install_script();

    if (
        file_exists($xcart_dir.'/init.php')
        && is_readable($xcart_dir.'/init.php')
    ) {
        include $xcart_dir.'/init.php';
    }

    x_load('mail');

    $paypal_enable_id = false;
    if (!empty($params['paypal_account']) && trim($params['paypal_account']) != '') {
        $paypal_account = trim($params['paypal_account']);
        $processor = 'ps_paypal.php';
        $template = 'customer/main/payment_offline.tpl';

        $paypal_enable_id = md5(uniqid(microtime()));
        db_query("REPLACE INTO $sql_tbl[config] (category, name, value) VALUES ('', 'paypal_enable_id','$paypal_enable_id')");
        $paymentid = func_query_first_cell("SELECT paymentid FROM $sql_tbl[payment_methods] WHERE payment_method='PayPal'");

        if ($paymentid === false) {
            $insert_params = array (
                'payment_method' => 'PayPal',
                'payment_script' => 'payment_cc.php',
                'payment_template' => $template,
                'active' => 'N',
                'orderby' => '999',
                'processor_file' => $processor
            );

            $paymentid = func_array2insert('payment_methods', $insert_params);
            db_query("UPDATE $sql_tbl[ccprocessors] SET paymentid='".$paymentid."', param01='".$paypal_account."', param02='".addslashes($config['Company']['company_name'])."', param03='USD' WHERE processor='".$processor."'");

            $tmp = func_query_first("SELECT * from $sql_tbl[ccprocessors] WHERE processor='ps_paypal_pro.php'");
            $cc_processor = $tmp['module_name'];
            // PayPal ExpressCheckout
            $insert_params['payment_method'] = $cc_processor.': '.$tmp['param08'];
            $insert_params['processor_file'] = 'ps_paypal_pro.php';
            $paymentid = func_array2insert('payment_methods', $insert_params);
            db_query("UPDATE $sql_tbl[ccprocessors] SET paymentid='".$paymentid."' WHERE processor='ps_paypal_pro.php'");

            // PayPal DirectPayment
            $insert_params['payment_template'] = 'customer/main/payment_cc.tpl';
            $insert_params['payment_method'] = $cc_processor.': '.$tmp['param09'];
            func_array2insert('payment_methods', $insert_params);
        }
        else {
            db_query("UPDATE $sql_tbl[ccprocessors] SET paymentid='".$paymentid."', param01='".$paypal_account."' WHERE processor='".$processor."'");
            db_query("UPDATE $sql_tbl[payment_methods] SET active='N' WHERE paymentid='".$paymentid."'");
        }

        $mail_smarty->assign('paypal_enable_id', $paypal_enable_id);
        func_send_mail($paypal_account, 'mail/paypal_enable_subj.tpl', 'mail/paypal_enable.tpl', $config["Company"]["site_administrator"], true);
    }

    ob_start();
?>
<div class="interfaces">
<ul>
<li><u><a href="<?php echo $xcart_catalogs['customer']; ?>/home.php"><b><?php echo_lng("customer_area"); ?></b></a></u></li>
<?php if ($xcart_package=="PRO") { ?>

<li><u><a href="<?php echo $xcart_catalogs['admin']; ?>/home.php"><b><?php echo_lng("admin_area"); ?></b></a></u>
<?php if (isset($params['db_is_installed'])) { ?>
<span>[<?php echo_lng('username'); ?>:
<strong style="padding-left: 5px;">
<?php if ($params['email_as_login']) echo $params['company_email']; else echo 'admin'; ?>
</strong>,
<?php echo_lng('password');
    do {
        $password = substr(md5(uniqid(rand(), true)), 0, 7);
    } while (check_password($password));

    echo(': <strong style="padding-right: 5px;">'.$password).'</strong>';
    db_query("UPDATE $sql_tbl[customers] SET password='".crypt_field($password,$params["blowfish_key"])."' WHERE id='2'");
?>]</span>
<?php } ?>
</li>
<?php } ?>

<li><u><a href="<?php echo $xcart_catalogs['provider']; ?>/home.php"><b><?php echo lng_get($xcart_package=="PRO" ? "provider_area" : "admin_area") ?></b></a></u>
<?php if (isset($params['db_is_installed'])) { ?>
<span>[
<?php echo_lng('username'); ?>: <?php echo '<strong style="padding-left: 5px;">' . ($params['email_as_login'] ? $params['company_email'] : ($xcart_package=="PRO" ? "provider" : "master")) ?></strong>,
<?php echo_lng('password');
    do {
        $password = substr(md5(uniqid(rand(), true)), 0, 7);
    } while (check_password($password));

    echo(': <strong style="padding-right: 5px;">'.$password).'</strong>';
    db_query("UPDATE $sql_tbl[customers] SET password='" .crypt_field($password, $params["blowfish_key"]) . "' WHERE id='" . ($xcart_package=='PRO' ? '3' : '1')."'");
?>]</span>
<?php } ?>
</li>
</ul>
</div>
<?php
    unset($password);
    db_query("UPDATE $sql_tbl[customers] SET last_login='".XC_TIME."' WHERE id < 5");
    $interfaces = ob_get_contents();
    ob_end_clean();

?>
<?php if (!empty($paypal_enable_id)) { ?>
<?php echo_lng('install_paypal_mail_note'); ?>
<br />

<?php } ?>

<?php
    $post_install_permissions_notice = func_install_get_post_install_notice($php_exec_mode);

    if ($success_rename) {
        $install_rename = lng_get('install_rename_success', 'install_name', $install_name, 'product', $installation_product);
    } else {
        $install_rename = lng_get('install_rename_failed', 'product', $installation_product);
    }

    $change_password_note = lng_get('change_password_note', 'area', ($xcart_package == 'PRO') ? lng_get('note_pro') : lng_get('note_gold'));

    require_once $xcart_dir.'/config.php';

    echo_lng('evaluation_notice', 'http_location', "http://" . $xcart_http_host . $xcart_web_dir);
?>
    <div class="remove-package-recommend">
<?php
    echo_lng('distribution_warning', 'product', $installation_product);
?>
    </div>
    <br />
<?php

    if (
        function_exists('func_is_default_auth_code')
        && func_is_default_auth_code($installation_auth_code)
    ) {
        $change_auth_code = "<br />" . lng_get('change_auth_code');
    } else {
        $change_auth_code = '';
    }

    echo_lng('xcart_final_note', 'code', $installation_auth_code, 'install_rename', $install_rename, 'post_install_permissions_notice', $post_install_permissions_notice, 'interfaces', $interfaces, 'product', $installation_product, 'email', $params['company_email'], 'change_auth_code', $change_auth_code);

    if ((!empty($params['flags']['noinfomail']) || empty($params['company_email'])) && $config["Company"]["site_administrator"] != "") {
        $params['company_email'] = $config["Company"]["site_administrator"];
        $params['flags']['noinfomail'] = "";
        $keys_information = '';
    } else {

        $keys_information = lng_get('keys_information',
            'installation_auth_code', $installation_auth_code,
            'blowfish_key', $params['blowfish_key'],
            'product', $installation_product,
            'change_auth_code', $change_auth_code
        );
    }

    $email_message = lng_get('final_email_message',
        'install_rename', $install_rename,
        'keys_information', $keys_information,
        'product', $installation_product,
        'interfaces', $interfaces,
        'post_install_permissions_notice', $post_install_permissions_notice
    );
    if (!empty($paypal_enable_id))
        $email_message .= "<br />".lng_get('install_paypal_mail_note')."<br />";

    if (empty($params['flags']['noinfomail']) && !empty($params['company_email'])) {
        $lend = (X_DEF_OS_WINDOWS?"\r\n":"\n");
        if (X_DEF_OS_WINDOWS)
            $message = preg_replace("/(?<!\r)\n/", "\r\n", $message);

        $install_wiz = lng_get('install_wiz', 'product', $installation_product);
        $email_message = <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>$install_wiz</title>
<style type="text/css">
<!--
BODY, DIV, TH, TD, P, INPUT, SELECT, TEXTAREA, TT, A {
    FONT-FAMILY: Verdana, Tahoma, Arial, Helvetica, sans-serif;
    COLOR: #2C3E49;
    FONT-SIZE: 10px;
}
A:link {
    COLOR: #043fa0;
}
A:visited {
    COLOR: #043fa0;
}
A:hover {
    COLOR: #043fa0;
}
A:active  {
    COLOR: #043fa0;
}
H1, H2, H3 {
    COLOR: #2C3E49;
    PADDING: 0;
    MARGIN: 2px 0 2px 0;
}
H1 {
    FONT-SIZE: 14px;
}
H2 {
    FONT-SIZE: 13px;
}
H3 {
    font-size: 12px;
}
HTML, BODY {
    HEIGHT: 100%;
    MARGIN: 0px;
    PADDING: 15px;
    BACKGROUND-COLOR: #FFFFFF;
}
FORM {
    MARGIN: 0px;
}
TABLE, IMG {
    BORDER: 0px;
}
LI {
    PADDING-BOTTOM: 5px;
}
UL LI {
    LIST-STYLE: square;
}
CODE {
    BACKGROUND-COLOR: #EEEEEE;
}
#dialog-message .box {
  position: relative;
  margin-left: 0%;
  margin-right: 0%;
  border: #ddddd9 1px solid;
  padding: 10px 25px 10px 59px;
  vertical-align: middle;
  text-align: left;
  min-height: 32px;
}
#dialog-message .message-w {
  color: #3e3104;
  background: #fafafa url($http_location$smarty_skin_dir/images/icon_warning.gif) no-repeat 10px 10px;
}
-->
</style>
</head>
<body>
$email_message
<br />
<hr size="1" noshade="noshade" />
$install_wiz
</body>
</html>
EOT;
        $headers =
            "From: \"$install_wiz\" <$params[company_email]>" .  $lend .
            "X-Mailer: X-Cart" . $lend .
            "MIME-Version: 1.0" . $lend .
            "Content-Type: text/html; charset=" . $install_language_charset . $lend;

        if (preg_match('/([^ @,;<>]+@[^ @,;<>]+)/S', $params['company_email'], $m)) {
            @mail($params['company_email'], lng_get("install_complete"), $email_message, $headers, "-f".$m[1]);
        } else {
            @mail($params['company_email'], lng_get("install_complete"), $email_message, $headers);
        }
    }

    return false;
}

/**
 * end: Install_done module
 */
function func_get_disabled_funcs()
{
    $disabled_functions = preg_split('/[, ]/', ini_get("disable_functions"));
    if (!empty($disabled_functions) && is_array($disabled_functions)) {
        $tmp = array();
        foreach ($disabled_functions as $f) {
            if (!empty($f)) {
                $tmp[] = $f;
            }
        }
        $disabled_functions = $tmp;
    } else {
        $disabled_functions = array();
    }

    return $disabled_functions;
}

/**
 * Check environment and server configuration.
 */
function func_get_env_srv_state()
{
    global $min_ver, $required_functions, $check_files, $check_permissions;

    $check_errors = array('env' => array(), 'critical' => array(), 'noncritical' => array());

    if (!empty($check_files) && is_array($check_files)) {
        $integrity_check_result = array();
        $status = true;
        foreach ($check_files as $file => $md5) {
            if (!@file_exists($file)) {
                $status = false;
                $integrity_check_result[$file] = 'int_check_file_not_found';
                continue;
            }
            if (!@is_readable($file)) {
                $status = false;
                $integrity_check_result[$file] = 'int_check_not_readable';
                continue;
            }
            if (md5(join('', file($file))) != $md5) {
                $status = false;
                $integrity_check_result[$file] = 'int_check_md5_nok';
                continue;
            }
            #$integrity_check_result[$file] = 'int_check_ok';
        }

        if ($status == false) {
            $check_errors['noncritical']['int_check_files'] = array(
                'type'             =>     'warning',
                'files_list'     =>     $integrity_check_result
            );
        }
    }

    $check_permission_errors = array();
    $exec_mode = func_get_php_execution_mode();
    if (!empty($check_permissions) && is_array($check_permissions)) {
        foreach ($check_permissions as $entity_name => $entity) {
            if (empty($entity) || !is_array($entity) || empty($entity_name))
                continue;

            $func_name = 'is_' . $entity['mode'];
            if ($entity['mode'] == 'executable' && strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
                $check_permissions[$entity_name]['check_result'] = file_exists($entity_name);
                if (!$check_permissions[$entity_name]['check_result'])
                    $check_permission_errors[] = $entity_name;

                continue;
            }

            if ($func_name == 'is_executable' && !function_exists($func_name))
                $func_name = 'is_readable';

            // Trying to automatically fix permissions if installer PHP script works in privileged mode.
            if ($exec_mode == 'privileged' && file_exists($entity_name)) {
                func_chmod_file($entity_name, $check_permissions[$entity_name]['permissions'][$exec_mode]);
            }

            // Check permissions
            $check_permissions[$entity_name]['check_result'] = file_exists($entity_name) && (function_exists($func_name) ? call_user_func($func_name, $entity_name) : false);

            if (!$check_permissions[$entity_name]['check_result']) {
                $check_permission_errors[] = $entity_name;

                if (preg_match('/(?:^.*(\.pl|\.sh))$/', $entity_name))
                    $check_permissions[$entity_name]['check_result'] = 'warning';
            }
        }
    }

    if (!empty($check_permission_errors)) {
        foreach ($check_permission_errors as $check_permission_error)
            if (preg_match('/^.*(\.pl|\.sh)$/',$check_permission_error))
                $check_errors['noncritical']['non_critical_permissions'][] = $check_permission_error;
            else
                $check_errors['env']['permissions'][] = $check_permission_error;
    }

    // Detect the list of disabled functions.
    $disabled_functions = func_get_disabled_funcs();

    // Check PHP version.
    $ver = phpversion();
    $status = ($min_ver > $ver ? 0 : 1);
    if (!$status) {
        $check_errors['critical']['dep_php_ver'] = $ver;
    }

    // Check PCRE extension presence.
    $status = function_exists('preg_match') ? 1 : 0;
    if (!$status) {
        $check_errors['critical']['dep_pcre'] = on_off($status);
    }

    // Check if Safe mode is enabled.
    $res = bool_get('safe_mode');
    $status = (!empty($res) ? 0 : 1);
    if (!$status) {
        $check_errors['critical']['dep_safe_mode'] = on_off(!$status);
    }

    // ini_set must be allowed.
    $status = !in_array('ini_set', $disabled_functions) && is_callable('ini_set');
    if (!$status) {
        $check_errors['critical']['dep_ini_set'] = join(", ", $disabled_functions);
    }

    // File uploads must be On.
    $res = bool_get('file_uploads');
    $status = (!empty($res) ? 1 : 0);
    if (!$status) {
        $check_errors['critical']['dep_uploads'] = on_off($status);
    }

    // magic_quotes_sybase
    $res = bool_get('magic_quotes_sybase');
    if (!empty($res)) {
        $check_errors['critical']['magic_quotes_sybase'] = on_off(1);
    }

    // sql.safe_mode
    $res = bool_get('sql.safe_mode');
    if (!empty($res)) {
        $check_errors['critical']['sql_safe_mode'] = on_off(1);
    }

    // memory_limit
    $res = func_convert_to_byte(bool_get('memory_limit'));

    if ($res === '') {
        $check_errors['noncritical']['memory_limit_none'] = 'warning';
    } else {
        if ($res < (32 * 1024 * 1024)) {
            $check_errors['critical']['memory_limit'] = $res;
        }

        // memory_limit set
        $new_val = $res + 1024 * 1024;

        @ini_set('memory_limit', $new_val);
        $res = func_convert_to_byte(ini_get('memory_limit'));

        if ($new_val != $res) {
            if (isset($check_errors['critical']['memory_limit']))
                $check_errors['critical']['memory_limit_set'] = 1;
            else
                $check_errors['noncritical']['memory_limit_set'] = 1;
        } elseif (isset($check_errors['critical']['memory_limit'])) {
            $check_errors['noncritical']['memory_limit'] = ($check_errors['critical']['memory_limit'] <= (64 * 1024 * 1024)) ? true : $check_errors['critical']['memory_limit'];
            unset($check_errors['critical']['memory_limit']);
        }
    }

    // MySQL functions must present.
    $status = function_exists('mysql_connect');
    if (!$status) {
        $check_errors['critical']['dep_mysql'] = lng_get("bool_off");
    }

    // Disabled functions list should not include required functions.
    if (is_array($disabled_functions) && !empty($disabled_functions)) {
        $tmp = array_intersect($disabled_functions, $required_functions);
        if (count($tmp) > 0) {
            $check_errors['noncritical']['dep_disable_funcs'] = join(", ", $tmp);
        }
    }

    // Check maximum allowed size of an uploaded file.
    $res = ini_get('upload_max_filesize');
    if (!$res) {
        $check_errors['noncritical']['dep_upl_max'] = $res;
    }

    // Check if fopen can open URLs.
    $res = bool_get('allow_url_fopen');
    $status = (!empty($res) ? 1 : 0);
    if (!$status) {
        $check_errors['noncritical']['dep_fopen'] = on_off($res);
    }

    // Check gdlib.
    $status = extension_loaded('gd') && function_exists("gd_info") ? 1 : 0;
    if ($status) {
        $gd_config = gd_info();
        $status = preg_match('/[^0-9]*2\./',$gd_config['GD Version']) ? 1 : 0;
    }
    if (!$status) {
        $check_errors['noncritical']['dep_gd'] = on_off($status);
    }

    // Check blowfish encryption mode.
    $res = false;

    global $xcart_dir;
    if (
        file_exists($xcart_dir.'/include/blowfish.php')
        && is_readable($xcart_dir.'/include/blowfish.php')
    ) {
        include_once $xcart_dir.'/include/blowfish.php';
    }

    if (defined('BF_MODE')) {
        $res = constant('BF_MODE');
    } else if (function_exists('func_bf_check_env')) {
        $blowfish = new ctBlowfish();
        func_bf_check_env();
        $res = constant('BF_MODE');
    }

    if (empty($res) || $res == 3) {
        $check_errors['noncritical']['dep_blowfish'] = $res ? 'bitwise emulation' : 'unknown blowfish encryption mode';
    }

    return $check_errors;
}

/**
 * Generate server check report in text format.
 */
function func_generate_check_report()
{
    global $installation_product;
    global $install_language_code;

    $check_errors = func_get_env_srv_state();

    $old_install_language_code = $install_language_code;
    $install_language_code = 'US';
    $delimiter = str_repeat("-", 80)."\n";

    $xcart_version = 'unknown';
    if (@file_exists('VERSION')) {
        $xcart_version = trim(join('', file('VERSION')));
    }
    $report = $installation_product . ' version: '.$xcart_version."\n".$delimiter;
    $report .= "Report time: " . date('r')."\n".$delimiter;
    if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
        $report .= "HTTP_REFERER: " . $_SERVER['HTTP_REFERER']."\n".$delimiter;
    }

    // Environment checks.
    if (!empty($check_errors['env'])) {
        $report .= "ENVIRONMENT CHECK ERRORS:'.'\n".$delimiter;
        foreach ($check_errors['env'] as $k => $v) {
            if (is_array($v)) {
                $v = func_get_check_error_value($k, $v);
            }
            $report .= "- "  . strip_tags(lng_get($k.'_title', 'value', $v))."\n[CHECK RESULT]:\n" . strip_tags($v) . "\n";
        }
        $report .= $delimiter;
    }

    // Server checks.
    foreach (array('critical', 'noncritical') as $type) {
        if (!empty($check_errors[$type])) {
            $report .= strtoupper($type)." ERRORS:'.'\n".$delimiter;
            foreach ($check_errors[$type] as $k => $v) {
                if (is_array($v)) {
                    $v = func_get_check_error_value($k, $v);
                }
                $report .= "- " . strip_tags(lng_get($k.'_title', 'value', $v))." [CHECK RESULT: ".strip_tags($v)."]\n";
            }
            $report .= $delimiter;
        }
    }

    // PHP info
    $report .= "\n============================= PHP INFO =============================\n";
    $disabled_functions = func_get_disabled_funcs();
    if (is_array($disabled_functions) && in_array('phpinfo', $disabled_functions)) {
        $phpinfo = "phpinfo() disabled.\n";
    } else {
        ob_start();
        phpinfo();
        $phpinfo = ob_get_contents();
        ob_end_clean();

        // prepare phpinfo
        $phpinfo = preg_replace("/<t(d|h)[^>]*>/iU", " | ", $phpinfo);
        $phpinfo = preg_replace("/<[^>]+>/iU", '', $phpinfo);
        $phpinfo = preg_replace("/(?:&lt;)((?!&gt;).)*?&gt;/i", '', $phpinfo);

        $pos = strpos($phpinfo, "PHP Version");
        if ($pos !== false) {
            $phpinfo = substr($phpinfo, $pos);
        }

        $pos = strpos($phpinfo, "PHP License");
        if ($pos !== false) {
            $phpinfo = substr($phpinfo, 0, $pos);
        }
        $phpinfo = preg_replace("/ {2,}/mS", " ", $phpinfo);
    }
    $report .= $phpinfo;

    $install_language_code = $old_install_language_code;

    return $report;
}

/**
 * Detects php.ini file location.
 */
function func_get_php_ini_path()
{
    static $php_ini_path;

    if (isset($php_ini_path)) {
        return $php_ini_path;
    }

    ob_start();
    phpinfo(INFO_GENERAL);
    $php_info = ob_get_contents();
    ob_end_clean();

    $ver = phpversion();
    if ($ver >= '5.0.0')
        $pattern = '!<tr><td class="e">Loaded Configuration File </td><td class="v">([^<]*)</td></tr>!Si';
    else
        $pattern = '!<tr><td class="e">Configuration File[^<]+</td><td class="v">([^<]*)</td></tr>!Si';

    if (preg_match($pattern, $php_info, $m)) {
        $php_ini_path = trim(strip_tags($m[1]));
    }

    $php_ini_path = ($php_ini_path ? (($ver < '5.0.0') ? $php_ini_path.XC_DS.'php.ini' : $php_ini_path) : 'php.ini');

    return $php_ini_path;
}

/**
 * Output a check error description.
 */
function func_show_check_err($name, $value)
{
    $php_ini_path = func_get_php_ini_path();

    echo lng_get($name.'_title', 'value', $value, 'php_ini_path', $php_ini_path);
    echo '<div class="show-hide">';
    echo '<img class="toggle-img" src="skin/common_files/images/plus.gif" alt="'.lng_get('click_to_open').'" id="close'.$name.'" onclick="javascript: visibleBox(\''.$name.'\');" />';
    echo '<img class="toggle-img" src="skin/common_files/images/minus.gif" alt="'.lng_get('click_to_close').'" style="display:none;" id="open'.$name.'" onclick="javascript: visibleBox(\''.$name.'\');" />&nbsp;';
    echo '<a href="install.php?mode=show_check_error&amp;error='.$name.'" onclick="javascript: visibleBox(\''.$name.'\'); return false;" target="_blank">'.lng_get('err_show_details').'</a><br />'."\n";
    echo '<div id="box'.$name.'"  style="display: none">'.lng_get($name.'_descr', 'value', $value, 'php_ini_path', $php_ini_path)."</div>\n";
    echo '</div>';
    echo "\n";
}

/**
 * Prepare a error value
 */
function func_get_check_error_value($error, $value)
{
    global $check_permissions;

    switch ($error) {
    case 'int_check_files':
        $val = '';
        $value = $value['files_list'];
        if (is_array($value) && !empty($value)) {
            $val = "<ul>\n";
            foreach ($value as $k => $v) {
                $val .= '<li>' . $k . ' - ' . '<strong>'.lng_get($v)."</strong></li>\n";
            }
            $val .= "</ul>";
        }

        return $val;
    case 'permissions':
        $val = '';
        $exec_mode = func_get_php_execution_mode();
        if (is_array($value) && !empty($value)) {
            $val = "<ul>\n";
            foreach ($value as $entity) {
                if (!in_array($entity, array_keys($check_permissions))) {
                    continue;
                }
                $entity_full_path = dirname(__FILE__).XC_DS.$entity;
                if (!stristr($_SERVER['HTTP_USER_AGENT'],"MSIE 6") && !stristr($_SERVER['HTTP_USER_AGENT'],"Opera"))
                    $entity_full_path = str_replace(XC_DS,XC_DS.'&#8203;', $entity_full_path);

                $val .= '<li><strong>' . $entity . '</strong> - ' . lng_get("permission_".$check_permissions[$entity]['type']."_".$check_permissions[$entity]['mode'], "entity", $entity, "permissions", sprintf("%o", $check_permissions[$entity]['permissions'][$exec_mode]), "entity_full_path", $entity_full_path)."</li>\n";
            }
            $val .= "</ul>";
        }

        return $val;
    case 'non_critical_permissions':
        $val = '';
        $exec_mode = func_get_php_execution_mode();
        if (is_array($value) && !empty($value)) {
            $val = "<ul>\n";
            foreach ($value as $entity) {
                if (!in_array($entity, array_keys($check_permissions))) {
                    continue;
                }
                $entity_full_path = dirname(__FILE__).XC_DS.$entity;
                if (!stristr($_SERVER['HTTP_USER_AGENT'],"MSIE 6") && !stristr($_SERVER['HTTP_USER_AGENT'],"Opera"))
                    $entity_full_path = str_replace(XC_DS,XC_DS.'&#8203;', $entity_full_path);

                $val .= '<li><strong>' . $entity . '</strong> - ' . lng_get("permission_".$check_permissions[$entity]['type']."_".$check_permissions[$entity]['mode'], "entity", $entity, "permissions", sprintf("%o", $check_permissions[$entity]['permissions'][$exec_mode]), "entity_full_path", $entity_full_path)."</li>\n";
            }
            $val .= "</ul>";
        }

        return $val;
    default:
        return 'UNKNOWN ERROR CODE';
    }
}

function func_install_get_post_install_notice($exec_mode = 'nonprivileged')
{
    global $post_install_permissions, $installation_product;

    $correct_permissions_for = array();

    if ($exec_mode == 'nonprivileged') {
        $correct_permissions_for = array_keys($post_install_permissions);
    } else {
        foreach ($post_install_permissions as $entity => $permissions) {
            if (!func_chmod_file($entity, $permissions[$exec_mode])) {
                $correct_permissions_for[] = $entity;
            }
        }
    }

    if (empty($correct_permissions_for)) {
        return '';
    }

    if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
        $notice_text = lng_get('post_install_permissions_notice_intro', "product", $installation_product)."\n<ul class=\"permissions-list\">";
        foreach ($correct_permissions_for as $entity) {
            $notice_text .= '<li>chmod '. sprintf("%o", $post_install_permissions[$entity][$exec_mode]).' ' .$entity.'</li>';
        }
        $notice_text .= "</ul><br />\n";
    } else {
        $notice_text = lng_get('post_install_permissions_notice_intro_windows', "product", $installation_product, "user", @get_current_user());
    }

    return $notice_text;
}

/**
 * Show environment/server check error.
 */
function module_check_error(&$params)
{

    $php_ini_path = func_get_php_ini_path();
    $check_errors = func_get_env_srv_state();

    $found_error = false;
    $value = null;
    foreach (array('env', 'critical', 'noncritical') as $type) {
        if (isset($check_errors[$type][$_GET['error']])) {
            $found_error = true;
            $value = $check_errors[$type][$_GET['error']];
            if (is_array($value)) {
                $value = func_get_check_error_value($_GET['error'], $value);
            }
            break;
        }
    }

    if (!$found_error) {
        echo "<h2>".lng_get('err_unknown_check_error')."</h2>\n";
        return;
    }

    $err_title = lng_get($_GET['error'].'_title', 'value', $value);
    $err_descr = lng_get($_GET['error'].'_descr', 'php_ini_path', $php_ini_path, 'value', $value);
    echo '<table width="90%" cellspacing="0" cellpadding="0" align="center"><tr><td>';
    if (!empty($err_title) && !empty($err_descr)) {
        echo '<h2 class="dep-error">'.$err_title."</h2>\n";
        echo $err_descr;
    } else {
        echo "<h2>".lng_get('err_unknown_check_error')."</h2>\n";
    }
    echo "</td></tr></table>\n";

    return false;
}

/**
 * Prepare "Technical problems report" form.
 */
function module_send_problem_report(&$params)
{

    $check_errors = func_get_env_srv_state();

    if (empty($check_errors['env']) && empty($check_errors['critical']) && empty($check_errors['noncritical'])) {
        echo '<h2 align="center">'.lng_get("techrep_no_errors").'</h2>';
        return false;
    }

    echo '<table width="90%" cellspacing="0" cellpadding="0" align="center"><tr><td>';
    echo "<h1>".lng_get('technical_problems_report')."</h1>\n";
    echo lng_get('techrep_intro')."\n";
    echo '<form method="post" name="report_form" action="'.constant("X_REPORT_URL").'" onsubmit="javascript: if (this.user_email &amp;&amp; this.user_email.value == \'\') { alert(\''.lng_get('techrep_err_empty_email').'\'); this.user_email.focus(); return false;} return true;">';
    echo '<input type="hidden" name="product_type" value="'.constant("X_REPORT_PRODUCT_TYPE").'" />'."\n";
    echo '<strong>'.lng_get('techrep_your_email').':</strong> <input type="text" name="user_email" size="33" value="" /><br /><br />'."\n";
    echo '<strong>'.lng_get("technical_problems_report").':</strong><br />'."\n";
    echo '<textarea name="report" cols="80" rows="25" readonly="readonly" class="tech-report-textarea">'.func_generate_check_report().'</textarea><br /><br />'."\n";
    echo '<strong>'.lng_get("techrep_user_note").':</strong><br />'."\n";
    echo '<textarea name="user_note" cols="80" rows="10" class="tech-report-textarea"></textarea><br /><br />'."\n";
    echo '<input type="button" value="'.lng_get("button_back").' " onclick="javascript: return step_back();" />&nbsp;&nbsp;&nbsp;'."\n";
    echo '<input type="submit" value="'.lng_get("techrep_send_report").'" /><br /><br />'."\n";
    if (!defined('XCART_TRIAL')) {
        echo lng_get('techrep_send_note')."\n";
    }
    echo "</form>\n";
    echo "</td></tr></table>\n";

    return false;
}

/**
 * Cycle tr class values
 */
function cycle_class($class_name, $force_prefix = false)
{
    global $prev_hl_prefix;

    $prev_hl_prefix = ($force_prefix) ? $force_prefix : (($prev_hl_prefix != '1') ? '1' : '2');
    echo $class_name.$prev_hl_prefix;
}

// If we got called in 'show_check_error' mode, show check error on the only installation step.
if (isset($_GET['mode']) && $_GET['mode'] == 'show_check_error' && !empty($_GET['error'])) {
    $_POST['current'] = 0;
    $modules = array (
        0 => array(
            'name' => 'check_error',
            'comment' => 'mod_check_error'
        ),
        1 => array(
            'name' => 'check_error',
            'comment' => 'mod_check_error'
        )
    );
    define('XCART_SKIP_INSTALLER_FORM', 1);
    $sb_excludes = array(0,1);
}

// If customer pressed on "Send report" button at "Checking PHP configuration" step, then show
// a technical problem report page.
if (isset($_POST['send_problem_report'])) {
    $_POST['current'] = 0;
    $modules = array (
        0 => array(
            'name' => 'send_problem_report',
            'comment' => 'mod_send_problem_report'
        ),
        1 => array(
            'name' => 'send_problem_report',
            'comment' => 'mod_send_problem_report'
        ),
    );
    // Tech report page does not require standard installer form.
    define('XCART_SKIP_INSTALLER_FORM', 1);
    $sb_excludes = array(0,1);
}

include './include/install.php';

?>
