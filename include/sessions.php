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
 * Sessions-related functionality
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: sessions.php,v 1.110.2.5 2011/04/22 14:20:59 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

/**
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 * DO NOT CHANGE ANYTHING BELOW THIS LINE UNLESS
 * YOU REALLY KNOW WHAT YOU ARE DOING
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 */

if (defined('XCART_SESSION_START'))
    return;

define('XCART_SESSION_START', 1);

/**
 * Session unknown id limit
 */
define('X_SESSION_UNKNOWN_LIMIT', 3);

/**
 * PHP build-in sessions tuning (for type '1' & '2')
 */

// PHP 4.3.0 and higher allow to turn off trans-sid using this command:
ini_set('url_rewriter.tags','');
// Let's garbage collection will occurs more frequently
ini_set('session.gc_probability',90);
ini_set('session.gc_divisor',100); // for PHP >= 4.3.0
ini_set('session.use_cookies', false);

/**
 * Anti cache block
 */

if (defined('SET_EXPIRE')) {
    header("Expires: ".gmdate("D, d M Y H:i:s", constant('SET_EXPIRE'))." GMT");
} else {
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
}

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

if (defined('SET_EXPIRE')) {
    header("Cache-Control: public");
}
elseif ($HTTPS) {
    header("Cache-Control: private, must-revalidate");
}
else {
    header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
    header("Pragma: no-cache");
}

/**
 * P3P header
 */
if (!empty($config['Security']['p3p_compact_policy']))
    header("P3P: " . (!empty($config['Security']['p3p_policyref']) ? "policyref=\"{$config['Security']['p3p_policyref']}\", " : "") . "CP=\"{$config['Security']['p3p_compact_policy']}\"");

if (defined('DO_NOT_START_SESSION')) {

    return;

}

/**
 * Get session id
 */
if (isset($_POST[$XCART_SESSION_NAME])) {

    $session_source = 'P';

    $XCARTSESSID = $_POST[$XCART_SESSION_NAME];

} elseif (isset($_GET[$XCART_SESSION_NAME])) {

    $session_source = 'G';

    $XCARTSESSID = $_GET[$XCART_SESSION_NAME];

} elseif (isset($_COOKIE[$XCART_SESSION_NAME])) {

    $session_source = 'C';

    $XCARTSESSID = $_COOKIE[$XCART_SESSION_NAME];

} else {

    $session_source = false;

    $XCARTSESSID = false;

}

if (
    defined('USE_SESSION_HISTORY')
    && constant('USE_SESSION_HISTORY')
) {

    $session_host = empty($HTTP_HOST) ? "" : $HTTP_HOST;

    $is_session_exists = !empty($XCARTSESSID) && func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[sessions_data] WHERE sessid = '$XCARTSESSID'") > 0;

    // Remembers xid
    if (!empty($XCARTSESSID) && !$is_session_exists) {

        if ($session_source == 'C') {

            // Remembers xid if a user goes to old host
            $remember_xid = func_query_first_cell("SELECT dest_xid FROM $sql_tbl[session_history] WHERE ip = INET_ATON('$REMOTE_ADDR') AND host = '$session_host' AND xid = '$XCARTSESSID'");

            if ($remember_xid) {

                $XCARTSESSID = $remember_xid;

                $session_source = 'R';

            }

        } elseif (
            isset($_COOKIE[$XCART_SESSION_NAME])
            && in_array($session_source, array('G', 'P'))
            && func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[sessions_data] WHERE sessid = '" . $_COOKIE[$XCART_SESSION_NAME] . "'")
        ) {
            // Remembers xid if a user goes to cached page with old xid (form / link)
            $XCARTSESSID = $_COOKIE[$XCART_SESSION_NAME];
            $session_source = 'R';
        }
    }
}

$init_xid = $XCARTSESSID;
x_session_start($XCARTSESSID);

register_shutdown_function('x_session_finish');

// Save current xid for current host
if (
    defined('USE_SESSION_HISTORY')
    && constant('USE_SESSION_HISTORY')
    && !empty($HTTP_HOST)
    && func_is_valid_ip($REMOTE_ADDR)
) {
    $old_xid = func_query_first_cell("SELECT xid FROM $sql_tbl[session_history] WHERE ip = INET_ATON('$REMOTE_ADDR') AND host = '$session_host'");

    if (empty($old_xid)) {

        db_query("REPLACE INTO $sql_tbl[session_history] (`ip`, `host`, `xid`) VALUES (INET_ATON('$REMOTE_ADDR'), '$session_host', '$XCARTSESSID')");

    } else {

        func_array2update(
            'session_history',
            array(
                'xid' => $XCARTSESSID,
                'dest_xid' => '',
            ),
            "ip = INET_ATON('$REMOTE_ADDR') AND host = '$session_host'"
        );
    }

    // Update destination xid for other hosts
    if (
        !empty($init_xid)
        && $init_xid != $XCARTSESSID
    ) {
        db_query("UPDATE $sql_tbl[session_history] SET dest_xid = '$XCARTSESSID' WHERE ip = INET_ATON('$REMOTE_ADDR') AND host != '$session_host' AND (xid = '$init_xid' OR dest_xid = '$init_xid')");
    }
}

$smarty->assign('XCARTSESSNAME', $XCART_SESSION_NAME);
$smarty->assign('XCARTSESSID',   $XCARTSESSID);

/**
 * Remove xid from URL
 */
if (
    $REQUEST_METHOD == 'GET'
    && !empty($QUERY_STRING)
    && preg_match("/(?:^|\&)" . preg_quote($XCART_SESSION_NAME, '/') . "=[\d\w]+/", $QUERY_STRING)
) {
    x_session_register('xid_remove_try', true);

    if (
        $xid_remove_try
        || !empty($_COOKIE)
    ) {

        $xid_remove_try = false;

        $qs = func_qs_remove($QUERY_STRING, $XCART_SESSION_NAME);

        func_header_location($php_url['url'] . (empty($qs) ? "" : ("?" . $qs)), true, 302, true);

    }

    $xid_remove_try = true;
}

if (defined('NEW_SESSION')) {

    x_session_register('is_new_session', true);

} elseif (x_session_is_registered('is_new_session')) {

    x_session_unregister('is_new_session');

    func_session_truncate_unknown_xid();

}

#################################################################
//   FUNCTIONS
#################################################################

/**
 * Start session
 */
function x_session_start($sessid = '')
{
    global $XCART_SESSION_VARS, $XCART_SESSION_NAME, $XCARTSESSID, $XCART_SESSION_EXPIRY;

    global $sql_tbl, $config, $xcart_http_host, $xcart_https_host;

    // $sessid should contain only '0'..'9' or 'a'..'z' or 'A'..'Z'
    if (!is_string($sessid) && !preg_match('/^[0-9a-z]{32}$/Ssi', $sessid)) {
        $sessid = '';
    }

    // Always generate unique id for new sessions

    $XCART_SESSION_VARS = array();

    $l = isset($_SERVER['REMOTE_PORT']) ? $_SERVER['REMOTE_PORT'] : 0;

    list($usec, $sec) = explode(' ', microtime());

    srand((float) $sec + ((float) $usec * 1000000) + (float)$l);

    if (
        !empty($sessid) &&
        func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[sessions_data] WHERE sessid='$sessid'") == 0
    ) {

        func_session_process_unknown_xid($sessid);

        $sessid = '';

    }

    if (empty($sessid)) {

        $sessid = x_session_generateid();

    }

    if (empty($config['Sessions']['session_length']))
        $config['Sessions']['session_length'] = 30;

    $expiry_time = XC_TIME + $config['Sessions']['session_length'];

    // Delete expired sessions
    $delete_ids = func_query_column("SELECT sessid FROM $sql_tbl[sessions_data] WHERE expiry < '" . XC_TIME . "'");

    if (!empty($delete_ids)) {

        db_query("DELETE FROM $sql_tbl[sessions_data] WHERE expiry < '" . XC_TIME . "'");

        func_delete_session_related_data($delete_ids);

    }

    $sess_data = func_query_first("SELECT * FROM $sql_tbl[sessions_data] WHERE sessid='$sessid'");

    if ($sess_data) {

        $XCART_SESSION_VARS = unserialize($sess_data['data']);

        // Enable only for admin and provider areas because does not work correctly in the IE 8 browser in the customer area.
        if (
            !func_skip_fingerprint_check()
            && $XCART_SESSION_VARS['_saved_session_fg'] != x_session_get_fg()
            && in_array(AREA_TYPE, array('A', 'P'))
        ) {

            // Check session by fingerprint
            $sess_data = false;

            db_query("DELETE FROM $sql_tbl[sessions_data] WHERE sessid='$sessid'");

            $sessid = x_session_generateid();

        } elseif (
            isset($XCART_SESSION_VARS['_session_force_regenerate'])
            || func_check_regen_sess_condition()
        ) {

            // Change session
            $new_sessid = x_session_generateid();

            func_session_change($sessid, $new_sessid);

            db_query("UPDATE $sql_tbl[sessions_data] SET sessid='$new_sessid' WHERE sessid='$sessid'");

            $sessid = $new_sessid;
        }
    }

    if (empty($sess_data)) {

        if (!defined('NEW_SESSION'))
            define('NEW_SESSION', true);

        $XCART_SESSION_VARS = array(
            '_saved_session_fg' => x_session_get_fg(),
        );

        func_array2insert(
            'sessions_data',
            array(
                'sessid' => $sessid,
                'start'  => XC_TIME,
                'expiry' => $expiry_time,
                'data'   => addslashes(serialize($XCART_SESSION_VARS)),
            ),
            true
        );

    } elseif ($XCART_SESSION_VARS['_saved_session_fg'] != x_session_get_fg()) {

        $XCART_SESSION_VARS['_saved_session_fg'] = x_session_get_fg();

        func_array2update(
            'sessions_data',
            array(
                'data'   => addslashes(serialize($XCART_SESSION_VARS)),
                'expiry' => $expiry_time,
            ),
            'sessid = \'' . $sessid . '\''
        );

    } else {

        func_array2update(
            'sessions_data',
            array(
                'expiry' => $expiry_time,
            ),
            'sessid = \'' . $sessid . '\''
        );

    }

    $XCART_SESSION_EXPIRY = $expiry_time;

    $XCARTSESSID = $sessid;

    func_setcookie($XCART_SESSION_NAME, $XCARTSESSID);
}

/**
 * Change current session to session with specified ID
 */
function x_session_id($sessid = '')
{
    global $sql_tbl, $XCART_SESSION_VARS, $XCARTSESSID, $XCART_SESSION_UNPACKED_VARS;

    $XCART_SESSION_VARS = array();

    if ($sessid) {

        $sess_data = func_query_first("SELECT * FROM $sql_tbl[sessions_data] WHERE sessid='$sessid'");

        $XCARTSESSID = $sessid;

        if ($sess_data) {

            $XCART_SESSION_VARS = unserialize($sess_data['data']);

            if (!empty($XCART_SESSION_UNPACKED_VARS)) {

                foreach ($XCART_SESSION_UNPACKED_VARS as $var => $v) {

                    if (isset($GLOBALS[$var]))
                        unset($GLOBALS[$var]);

                    unset($XCART_SESSION_UNPACKED_VARS[$var]);

                }

            }

        } else {

            x_session_start($sessid);

        }

    } else {

        $sessid = $XCARTSESSID;

    }

    return $sessid;
}

/**
 * Cut off variable if it came from _GET, _POST or _COOKIES
 */
function check_session_var($varname)
{
    return !isset($_GET[$varname]) && !isset($_POST[$varname]) && !isset($_COOKIE[$varname]);
}

/**
 * Register variable XCART_SESSION_VARS array from the database
 */
function x_session_register($varname, $default = '')
{
    global $XCART_SESSION_VARS, $XCART_SESSION_UNPACKED_VARS;

    if (empty($varname))
        return false;

    // Register variable $varname in $XCART_SESSION_VARS array
    if (!isset($XCART_SESSION_VARS[$varname])) {

        $XCART_SESSION_VARS[$varname] = isset($GLOBALS[$varname]) && check_session_var($varname)
            ? $GLOBALS[$varname]
            : $default;

    } elseif (isset($GLOBALS[$varname]) && check_session_var($varname)) {

        $XCART_SESSION_VARS[$varname] = $GLOBALS[$varname];

    }

    // Unpack variable $varname from $XCART_SESSION_VARS array
    $XCART_SESSION_UNPACKED_VARS[$varname] = $XCART_SESSION_VARS[$varname];

    $GLOBALS[$varname] = $XCART_SESSION_VARS[$varname];
}

/**
 * Save the XCART_SESSION_VARS array in the database
 */
function x_session_save()
{
    global $XCARTSESSID, $XCART_SESSION_VARS, $XCART_SESSION_UNPACKED_VARS, $sql_tbl;
    
    $old_data = func_query_first_cell("SELECT data FROM $sql_tbl[sessions_data] WHERE sessid = '$XCARTSESSID'");

    if (!empty($old_data))
        $old_data = unserialize($old_data);

    if (!is_array($old_data))
        $old_data = array();

    $varnames = func_get_args();

    if (empty($varnames))
        $varnames = is_array($XCART_SESSION_UNPACKED_VARS) ? array_keys($XCART_SESSION_UNPACKED_VARS) : array();

    $data = array();

    foreach (array_keys($XCART_SESSION_VARS) as $varname) {

        $data[$varname] = in_array($varname, $varnames)
            ? $GLOBALS[$varname]
            : @$old_data[$varname];

    }

    // Save session variables in the database
    $data = serialize($data);

    if (
        defined('BENCH')
        && constant('BENCH')
    ) {

        global $bench_max_session;

        $bench_max_session = max($bench_max_session, strlen($data));

    }

    $is_robot = function_exists('func_is_external_robot') && func_is_external_robot();

    if (!$is_robot) {
        func_array2update(
            'sessions_data',
            array(
                'data' => addslashes($data),
            ),
            'sessid = \'' . $XCARTSESSID . '\''
        );
    }
}

/**
 * Unregister variable $varname from $XCART_SESSION_VARS array
 */
function x_session_unregister($varname, $unset_global = false)
{
    global $XCART_SESSION_VARS, $XCART_SESSION_UNPACKED_VARS;

    if (empty($varname))
        return false;

    func_unset($XCART_SESSION_VARS, $varname);

    func_unset($XCART_SESSION_UNPACKED_VARS, $varname);

    if ($unset_global) {

        func_unset($GLOBALS, $varname);

    }

}

/**
 * Find out whether a global variable $varname is registered in
 * $XCART_SESSION_VARS array
 */
function x_session_is_registered($varname)
{
    global $XCART_SESSION_VARS;

    return !empty($varname) && isset($XCART_SESSION_VARS[$varname]);
}

/**
 * Generate unique session id
 */
function x_session_generateid()
{
    global $sql_tbl;

    do {

        $sessid = md5(uniqid(rand(), true));

    } while (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[sessions_data] WHERE sessid='$sessid'") > 0);

    return $sessid;
}

/**
 * Get session fingerprint
 */
function x_session_get_fg()
{
    global $HTTP_USER_AGENT;

    return md5($HTTP_USER_AGENT);
}

/**
 * Check - regenerate session or not
 */
function func_check_regen_sess_condition()
{
    global $PHP_SELF, $REQUEST_METHOD;

    return $REQUEST_METHOD == 'POST' && preg_match("/login\.php/", $PHP_SELF);
}

/**
 * Check - skip or not session fingerprint checking procedure
 */
function func_skip_fingerprint_check()
{
    return defined('SKIP_CHECK_SESSGION_FG');
}

/**
 * Delete session related data
 */
function func_delete_session_related_data($delete_ids)
{
    global $sql_tbl;

    if (empty($delete_ids) || !is_array($delete_ids))
        return false;

    $deleteSet = implode("','", $delete_ids);

    if (
        defined('USE_SESSION_HISTORY')
        && constant('USE_SESSION_HISTORY')
    ) {
        db_query("DELETE FROM $sql_tbl[session_history] WHERE dest_xid IN ('" . $deleteSet . "') OR xid IN ('" . $deleteSet . "')");
    }

    if (
        defined('AREA_TYPE')
        && in_array(constant('AREA_TYPE'), array('A', 'P'))
    ) {
        // Erase old service array (Group editing of products functionality)
        $res = db_query("SELECT geid FROM $sql_tbl[ge_products] WHERE sessid IN ('" . $deleteSet . '\')');

        if ($res) {

            while ($row = db_fetch_row($res)) {
                func_ge_erase($row[0]);
            }

            db_free_result($res);
        }

        // Erase form ids
        db_query("DELETE FROM $sql_tbl[form_ids] WHERE sessid IN ('" . $deleteSet . '\')');

        // Erase iterations
        db_query("DELETE FROM $sql_tbl[iterations] WHERE sessid IN ('" . $deleteSet . '\')');

    } elseif (
        defined('AREA_TYPE')
        && constant('AREA_TYPE') == 'C'
    ) {
        // Erase old service array (3D secure service data)
        db_query("DELETE FROM $sql_tbl[secure3d_data] WHERE sessid IN ('" . $deleteSet . '\')');
    }

    // Clear shipping packages cache
    db_query("DELETE FROM $sql_tbl[packages_cache] WHERE session_id IN ('" . $deleteSet . '\')');

    // Clear shipping rates cache
    db_query("DELETE FROM $sql_tbl[shipping_cache] WHERE session_id IN ('" . $deleteSet . '\')');

    return true;
}

/**
 * Change session related data if session id was changed
 */
function func_session_change($old_sessid, $new_sessid)
{
    global $sql_tbl;

    db_query("UPDATE $sql_tbl[form_ids] SET sessid = '$new_sessid' WHERE sessid = '$old_sessid'");
    db_query("UPDATE $sql_tbl[ge_products] SET sessid = '$new_sessid' WHERE sessid = '$old_sessid'");
    db_query("UPDATE $sql_tbl[secure3d_data] SET sessid = '$new_sessid' WHERE sessid = '$old_sessid'");
    db_query("UPDATE $sql_tbl[users_online] SET sessid = '$new_sessid' WHERE sessid = '$old_sessid'");

    return true;
}

function x_session_reset()
{
    global $XCART_SESSION_UNPACKED_VARS;

    $XCART_SESSION_UNPACKED_VARS = array();

    return true;
}

function x_session_finish()
{
    if (!defined('X_SESSION_FINISHED'))
        x_session_save();
}

function func_session_process_unknown_xid($sessid)
{
    global $sql_tbl, $robot;

    if (
        !isset($_SERVER['REMOTE_ADDR'])
        || !defined('X_SESSION_UNKNOWN_LIMIT')
        || defined('QUICK_START')
        || defined('ANTIBOT_SKIP_INIT')
        || intval(constant('X_SESSION_UNKNOWN_LIMIT')) <= 0
    ) {
        return false;
    }

    // Patch to HTML catalog work properly bt:84751
    if ($robot == 'X-Cart Catalog Generator')
        return false;

    $ip = $_SERVER['REMOTE_ADDR'];

    if (!func_is_valid_ip($ip))
        return false;

    $cnt = intval(func_query_first_cell("SELECT cnt FROM $sql_tbl[session_unknown_sid] WHERE ip = INET_ATON('$ip') AND sessid = '$sessid'")) + 1;
    db_query("REPLACE INTO $sql_tbl[session_unknown_sid] (`ip`, `sessid`, `cnt`) VALUES (INET_ATON('$ip'), '$sessid', '$cnt')");
    if ($cnt > X_SESSION_UNKNOWN_LIMIT)
        define('X_ERR_UNKNOWN_SESSION_ID', true);

    return true;
}

function func_session_truncate_unknown_xid()
{
    global $sql_tbl;

    if (!isset($_SERVER['REMOTE_ADDR']))
        return false;

    $ip = $_SERVER['REMOTE_ADDR'];

    if (!func_is_valid_ip($ip))
        return false;

    db_query("DELETE FROM $sql_tbl[session_unknown_sid] WHERE ip = INET_ATON('$ip')");

    return true;
}
?>
