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
 * Database-related functions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.db.php,v 1.57.2.9 2011/01/20 09:25:30 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

/**
 * Database abstract layer functions
 */
function db_connect($sql_host, $sql_user, $sql_password)
{
    return mysql_connect($sql_host, $sql_user, $sql_password);
}

function db_select_db($sql_db)
{
    return mysql_select_db($sql_db);
}

function db_query($query, $delayed_query_type = false)
{
    global $debug_mode;
    global $mysql_autorepair, $sql_max_allowed_packet;
    global $memcache;

    if ($delayed_query_type) {
        return func_add_delayed_query($query, $delayed_query_type);
    }

    if (defined('START_TIME')) {

        global $__sql_time;

        $t = func_microtime();

    }

    if (
        !defined('REMOVE_MCACHE_CONFIG')
        && $memcache
        && func_detect_config_changes($query)
    ) {
        define('REMOVE_MCACHE_CONFIG', 1);
    }

    if (
        $sql_max_allowed_packet
        && strlen($query) > $sql_max_allowed_packet
    ) {

        // Check max. allowed packet size
        global $current_location, $REMOTE_ADDR, $login;

        $len = strlen($query);

        $query = substr($query, 0, 1024) . '...';

        $mysql_error = '10001 : The size of the data package being transmitted is greater than maximum allowed by the server';

        $msg  = 'Site                : ' . $current_location . "\n";
        $msg .= 'Remote IP           : ' . $REMOTE_ADDR . "\n";
        $msg .= 'Logged as           : ' . $login. "\n";
        $msg .= 'Query length        : ' . $len . "\n";
        $msg .= 'Max. allowed packet : ' . $sql_max_allowed_packet . "\n";
        $msg .= 'SQL query           : ' . $query . "\n";
        $msg .= 'Error code          : 10001' . "\n";
        $msg .= 'Description         : The size of the data package being transmitted is greater than maximum allowed by the server';

        db_error_generic($query, $mysql_error, $msg);

        return false;
    }

    __add_mark();

    $result = mysql_query($query);

    if (
        !$result
        && preg_match('/Lost connection|server has gone away/', mysql_error())
    ) {

        db_connection();

        $result = mysql_query($query);

    }

    $t_end = func_microtime();

    if (defined('START_TIME')) {

        $__sql_time += func_microtime() - $t;

    }

    // Auto repair

    $mysql_error = mysql_error();

    if (
        !$result
        && $mysql_autorepair
        && preg_match("/(?:'(\S+)\.(?:MYI|MYD))|(?:Table\s+'(\S+)'\s+is\s+marked\s+as\s+crashed\s+and\s+should\s+be\s+repaired)/", $mysql_error, $m)
    ) {

        $m = $m[1];

        error_log('Repairing table ' . $m, 0);

        $mysql_error = mysql_errno() . " : " . $mysql_error;

        if ($debug_mode == 1 || $debug_mode == 3) {

            echo '<strong color="darkred">Repairing table ' . $m . '...</strong>' . $mysql_error . '<br />';

            flush();

        }

        $result = mysql_query('REPAIR TABLE ' . $m . ' EXTENDED');

        if (!$result) {

            error_log('Repairing table ' . $m . ' failed: ' . $mysql_error, 0);

        } else {

            $result = mysql_query($query);

        }

    }

    if (
        $debug_mode == 1
        && db_error($result, $query)
    ) {
        $_func = function_exists('func_get_backtrace_html') 
            ? 'func_get_backtrace_html' 
            : 'debug_backtrace';

        print_r($_func());
        exit;
    }

    if (!$result) {
        db_error($result, $query);
    }    

    $explain = array();

    if (
        defined('BENCH')
        && constant('BENCH')
        && !defined('BENCH_BLOCK')
        && !defined('BENCH_DISPLAY')
        && defined('BENCH_DISPLAY_TYPE')
        && constant('BENCH_DISPLAY_TYPE') == 'A'
        && !strncasecmp('SELECT', $query, 6)
    ) {
        $r = mysql_query('EXPLAIN ' . $query);

        if ($r !== false) {

            while ($arr = db_fetch_array($r))
                $explain[] = $arr;

            db_free_result($r);

        }

    }

    __add_mark(
        array(
            'query'   => $query,
            'explain' => $explain,
        ),
        'SQL'
    );

    return $result;
}

function db_result($result, $offset)
{
    return mysql_result($result, $offset);
}

function db_fetch_row($result)
{
    return mysql_fetch_row($result);
}

function db_fetch_array($result, $flag = MYSQL_ASSOC)
{
    return mysql_fetch_array($result, $flag);
}

function db_fetch_field($result, $num = 0)
{
    return mysql_fetch_field($result, $num);
}

function db_free_result($result)
{
    @mysql_free_result($result);
}

function db_num_rows($result)
{
    $res = $result ? intval(mysql_num_rows($result)) : false;
    return $res;
}

function db_num_fields($result)
{
    return intval(mysql_num_fields($result));
}

function db_insert_id()
{
    return mysql_insert_id();
}

function db_affected_rows()
{
    return mysql_affected_rows();
}

function db_error($mysql_result, $query)
{
    global $config, $login, $REMOTE_ADDR, $current_location;

    if ($mysql_result)
        return false;

    $mysql_error = mysql_errno() . ' : ' . mysql_error();

    $msg  = 'Site        : ' . $current_location . "\n";
    $msg .= 'Remote IP   : ' . $REMOTE_ADDR . "\n";
    $msg .= 'Logged as   : ' . $login . "\n";
    $msg .= 'SQL query   : ' . $query . "\n";
    $msg .= 'Error code  : ' . mysql_errno() . "\n";
    $msg .= 'Description : ' . mysql_error();

    db_error_generic($query, $mysql_error, $msg);

    return true;
}

function db_error_generic($query, $query_error, $msg)
{
    global $debug_mode, $config, $mysql_error_count;

    $mysql_error_count ++;

    $email = false;

    if ('Y' == $config['Email_Note']['admin_sqlerror_notify']) {
        $email = array($config['Company']['site_administrator']);
    }

    if ($debug_mode == 1 || $debug_mode == 3) {

        echo '<b><font color="darkred">INVALID SQL: </font></b>' . htmlspecialchars($query_error) . '<br />';
        echo '<b><font color="darkred">SQL QUERY FAILURE:</font></b>' . htmlspecialchars($query) . '<br />';

        flush();
    }

    $do_log = ($debug_mode == 2 || $debug_mode == 3);

    if (
        $email !== false
        || $do_log
    ) {

        if (!defined('SKIP_CHARSET_SELECTION')) {
            define('SKIP_CHARSET_SELECTION', 1);
        }

        x_log_add('SQL', $msg, true, 1, $email, !$do_log);

    }

}

function db_prepare_query($query, $params)
{
    static $prepared = array();

    if (!empty($prepared[$query])) {

        $info = $prepared[$query];
        $tokens = $info['tokens'];

    } else {

        $tokens = preg_split('/((?<!\\\)\?)/S', $query, -1, PREG_SPLIT_DELIM_CAPTURE);

        $count = 0;

        foreach ($tokens as $k => $v) {
            if ($v === '?') $count ++;
        }

        $info = array (
            'tokens'      => $tokens,
            'param_count' => $count,
        );

        $prepared[$query] = $info;

    }

    if (count($params) != $info['param_count']) {
        return array (
            'info'     => 'mismatch',
            'expected' => $info['param_count'],
            'actual'   => count($params),
        );
    }

    $pos = 0;

    foreach ($tokens as $k => $val) {

        if ($val !== '?') continue;

        if (!isset($params[$pos])) {
            return array (
                'info'     => 'missing',
                'param'    => $pos,
                'expected' => $info['param_count'],
                'actual'   => count($params),
            );
        }

        $val = $params[$pos];

        if (is_array($val)) {

            $val = func_array_map('addslashes', $val);

            $val = implode("','", $val);

        } else {

            $val = addslashes($val);
        }

        $tokens[$k] = "'" . $val . "'";

        $pos ++;

    }

    return implode('', $tokens);
}

// New DB API: Executing parameterized queries
// Example1:
//   $query = "SELECT * FROM table WHERE field1=? AND field2=? AND field3='\\?'"
//   $params = array (val1, val2)
//   query to execute:
//      "SELECT * FROM table WHERE field1='val1' AND field2='val2' AND field3='\\?'"
// Example2:
//   $query = "SELECT * FROM table WHERE field1=? AND field2 IN (?)"
//   $params = array (val1, array(val2,val3))
//   query to execute:
//      "SELECT * FROM table WHERE field1='val1' AND field2 IN ('val2','val3')"
/**
 * Warning:
 *  1) all parameters must not be escaped with addslashes()
 *  2) non-parameter symbols '?' must be escaped with a '\'
 */
function db_exec($query, $params = array())
{
    global $config, $login, $REMOTE_ADDR, $current_location;

    if (!is_array($params))
        $params = array ($params);

    $prepared = db_prepare_query($query, $params);

    if (!is_array($prepared)) {

        return db_query($prepared);

    }

    $error = 'Query preparation failed';

    if ('mismatch' == $prepared['info']) {

        $error .= ': parameters mismatch (passed ' . $prepared['actual'] . ', expected ' . $prepared['expected'] . ')';

    } elseif ('missing' == $prepared['info']) {

        $error .= ': parameter ' . $prepared['param'] . ' is missing';

    }

    $msg  = 'Site        : ' . $current_location . "\n";
    $msg .= 'Remote IP   : ' . $REMOTE_ADDR . "\n";
    $msg .= 'Logged as   : ' . $login . "\n";
    $msg .= 'SQL query   : ' . $query . "\n";
    $msg .= 'Description : ' . $error;

    db_error_generic($query, $error, $msg);

    return false;
}

/**
 * Execute mysql query and store result into associative array with
 * column names as keys
 */
function func_query($query, $use_cache = false)
{
    if ($use_cache)
        return db_get_cache($query);

    $result = false;

    if ($p_result = db_query($query)) {

        while ($arr = db_fetch_array($p_result))
            $result[] = $arr;

        db_free_result($p_result);

    }

    return $result;
}

/**
 * Execute mysql query and store result into associative array with
 * column names as keys and then return first element of this array
 * If array is empty return array().
 */
function func_query_first($query, $use_cache = false, $limit_first = true)
{
    $query = trim($query);

    // Add limit directive for the performance reason
    if (
        $limit_first
        && !preg_match('/\sLIMIT\s/Ssi', $query)
        && preg_match('/^SELECT\s/Ssi', $query)
    ) {
        $query .= ' LIMIT 1';
    }

    if ($use_cache) {

      return db_get_cache($query, 'first');

    }

    if ($p_result = db_query($query)) {

        $result = db_fetch_array($p_result);

        db_free_result($p_result);

    }

    return is_array($result)
        ? $result
        : array();
}

/**
 * Execute mysql query and store result into associative array with
 * column names as keys and then return first cell of first element of this array
 * If array is empty return false.
 */
function func_query_first_cell($query, $use_cache = false, $limit_first = true)
{
    $query = trim($query);

    // Add limit directive for the performance reason
    if (
        $limit_first
        && !preg_match('/\sLIMIT\s/Ssi', $query)
        && preg_match('/^SELECT\s/Ssi', $query)
    ) {
        $query .= ' LIMIT 1';
    }

    if ($use_cache) {
        return db_get_cache($query, 'first_cell');
    }

    if ($p_result = db_query($query)) {

        $result = db_fetch_row($p_result);

        db_free_result($p_result);

    }

    return is_array($result) ? $result[0] : false;
}

function func_query_column($query, $column = 0)
{
    $result = array();

    $fetch_func = is_int($column)
        ? 'db_fetch_row'
        : 'db_fetch_array';

    if ($p_result = db_query($query)) {

        while ($row = $fetch_func($p_result))
            $result[] = $row[$column];

        db_free_result($p_result);
    }

    return $result;
}

/**
 * Insert array data to table
 */
function func_array2insert ($tbl, $arr, $is_replace = false, $is_ignore = false)
{
    global $sql_tbl;

    if (empty($tbl) || empty($arr) || !is_array($arr))
        return false;

    if (!empty($sql_tbl[$tbl]))
        $tbl = $sql_tbl[$tbl];

    $query = $is_replace ? 'REPLACE' : ('INSERT' . ($is_ignore ? ' IGNORE' : ''));

    $arr_keys = array_keys($arr);

    func_check_tbl_fields($tbl, $arr_keys);

    foreach ($arr_keys as $k => $v) {

        if (!preg_match('/^`.*`$/Si', $v, $out)) {

            $arr_keys[$k] = "`" . $v . "`";

        }

    }

    $arr_values = array_values($arr);

    foreach ($arr_values as $k => $v) {

        if (!preg_match('/^["\'].*["|\']$/Si', $v, $out)) {

            $arr_values[$k] = "'" . $v . "'";

        }

    }

    $query .= ' INTO ' . $tbl . ' (' . implode(', ', $arr_keys) . ') VALUES (' . implode(', ', $arr_values) . ')';

    $r = db_query($query);

    if ($r) {

        return db_insert_id();

    }

    return false;
}

/**
 * Update array data to table + where statament
 */
function func_array2update ($tbl, $arr, $where = '', $skip_quotes = false)
{
    global $sql_tbl;

    if (
        empty($tbl)
        || empty($arr)
        || !is_array($arr)
    ) {
        return false;
    }

    if ($sql_tbl[$tbl])
        $tbl = $sql_tbl[$tbl];

    $r = array();

    foreach ($arr as $k => $v) {

        if (!(
            ($k{0} == '`')
            && ($k{strlen($k) - 1} == '`')
            )
        ) {
            $k = "`$k`";
        }

        if (
            !$skip_quotes
            && !preg_match('/^["\'].*["|\']$/Si', $v, $out)
        ) {
            $v = "'" . $v . "'";
        }

        $r[] = $k . "=" . $v;

    }

    func_check_tbl_fields($tbl, array_keys($arr));

    $query = 'UPDATE ' . $tbl . ' SET ' . implode(', ', $r) . ($where ? ' WHERE ' . $where : '');

    return db_query($query);
}

function func_query_hash($query, $column = false, $is_multirow = true, $only_first = false)
{
    $result = array();

    $is_multicolumn = false;

    if ($p_result = db_query($query)) {

        if ($column === false) {

            // Get first field name
            $c = db_fetch_field($p_result);

            $column = $c->name;

        } elseif (is_array($column)) {

            if (count($column) == 1) {

                $column = current($column);

            } else {

                $is_multicolumn = true;

            }

        }

        while ($row = db_fetch_array($p_result)) {

            // Get key(s) column value and remove this column from row
            if ($is_multicolumn) {

                $keys = array();

                foreach ($column as $c) {

                    $keys[] = $row[$c];

                    func_unset($row, $c);

                }

                $keys = implode('"]["', $keys);

            } else {

                $key = $row[$column];

                func_unset($row, $column);

            }

            if ($only_first)
                $row = array_shift($row);

            if ($is_multicolumn) {

                // If keys count > 1
                if ($is_multirow) {

                    eval('$result["' . $keys . '"][] = $row;');

                } else {

                    eval('$is = isset($result["' . $keys . '"]);');

                    if (!$is) {

                        eval('$result["' . $keys . '"] = $row;');

                    }

                }

            } elseif ($is_multirow) {

                $result[$key][] = $row;

            } elseif (!isset($result[$key])) {

                $result[$key] = $row;

            }

        }

        db_free_result($p_result);

    }

    return $result;
}

/**
 * Generate SQL-query relations
 */
function func_generate_joins($joins, $parent = false)
{
    $str = '';

    foreach ($joins as $jname => $j) {

        if (!isset($j['parent']))
            $j['parent'] = false;

        if (
            (
                empty($parent)
                && !empty($j['parent'])
            ) || (
                !empty($parent)
                && $parent != $j['parent']
            )
        ) {
            continue;
        }

        $str .= func_build_join($jname, $j);

        unset($joins[$jname]);

        list($js, $tmp) = func_generate_joins($joins, (empty($j['tblname']) ? $jname : $j['tblname']));

        $str .= $tmp;

        $keys = array_diff(array_keys($joins), array_keys($js));

        if (!empty($keys)) {

            foreach ($joins as $k => $v) {

                if (in_array($k, $keys))
                    unset($joins[$k]);

            }

        }

    }

    if (empty($parent) && !empty($joins)) {

        foreach ($joins as $jname => $j) {

            $str .= func_build_join($jname, $j);

        }

        unset($joins);

    }

    return false === $parent
        ? $str
        : array($joins, $str);
}

/**
 * Get [LEFT | INNER] JOIN string
 */
function func_build_join($jname, $join)
{
    global $sql_tbl;

    $str = ' ' . (!empty($join['is_inner']) ? 'INNER' : 'LEFT') . ' JOIN ';

    if (!empty($join['tblname'])) {

        $str .= $sql_tbl[$join['tblname']] . " as " . $jname;

    } else {

        $str .= $sql_tbl[$jname];

    }

    $str .= ' ON ' . $join['on'];

    return $str;
}

/**
 * Check table fields names
 */
function func_check_tbl_fields($tbl, $fields)
{
    static $storage = array();
    
    if (empty($storage))
        $storage = func_data_cache_get('sql_tables_fields');

    global $sql_tbl;

    if (empty($fields))
        return;

    if (!is_array($fields)) {
        func_403(77);
    }

    if (!is_array($tbl)) {
        $tbl = array($tbl);
    }

    $fields_orig = array();

    foreach ($tbl as $t) {

        if (isset($sql_tbl[$t])) {

            $t = $sql_tbl[$t];

        }

        $t = strtolower($t);
        if (!isset($storage[$t])) {
            func_403(78);
        }

        $fields_orig = func_array_merge($fields_orig, $storage[$t]);
    }

    $fields_orig = array_unique($fields_orig);

    $res = array_diff($fields, $fields_orig);

    if (!empty($res)) {
        func_403(79);
    }

}

/**
 * The function creates a DB connection
 */

function db_connection ($_sql_host = '', $_sql_user = '', $_sql_db = '', $_sql_password = '')
{
    global $sql_host, $sql_user, $sql_db, $sql_password, $debug_mode, $display_critical_errors;

    if ($_sql_host == '') $_sql_host = $sql_host;
    if ($_sql_user == '') $_sql_user = $sql_user;
    if ($_sql_db == '') $_sql_db = $sql_db;
    if ($_sql_password == '') $_sql_password = $sql_password;

    $db_connect_limit = 5;
    $error_text = '';
    $web_error_text = '';
    $sql_connection = false;
    $sql_select_db = false;

    while ($db_connect_limit-- > 0 && !$sql_connection) {
        $sql_connection = @db_connect($_sql_host, $_sql_user, $_sql_password);
    }

    if (!$sql_connection) {

        $error_text .=  db_error_msg();

        if ($display_critical_errors)
            $web_error_text .= db_error_msg(true);
    } else {

        $sql_select_db = db_select_db($_sql_db);
    }

    if (
        $sql_connection
        && !$sql_select_db
    ) {
        $error_text .= db_error_msg();

        if ($display_critical_errors)
            $web_error_text .= db_error_msg(true);
    }

    if (!$sql_connection || !$sql_select_db) {

        $config_php = '<b><a href="http://help.qtmsoft.com/index.php?title=X-Cart:Config.php">config.php</a></b>';

        if ($debug_mode > 1)
            func_direct_log('sql', $error_text);

        func_show_error_page(
            'Cannot connect to MySQL server',
            $web_error_text,
            'Please make sure of the following:
            <ul>
                <li>The MySQL credentials in the ' . $config_php . ' file are correct.</li>
                <li>Your MySQL server is installed and is up and running properly.</li>
                <li>If you have recently updated the MySQL credentials in the config.php file, moved your store to another server, or updated the password of your hosting control panel (some control panels update the MySQL password automatically if you change the main password), make sure the MySQL credentials specified in your store\'s ' . $config_php . ' file match the MySQL credentials provided by your hosting.</li>
            </ul>'
        );
    }

    return $sql_connection;
}

/**
 * Compose mySQL error text containing error number, error message with links to mysql.com
 */

function db_error_msg($web = false)
{
    return (false !== $web)
        ? '<a href="http://search.mysql.com/search?q=' . mysql_errno() . '">' . mysql_errno() . '</a>: ' . mysql_error() . "<br /><br />\n"
        : mysql_errno() . ': ' . mysql_error() . "\n";
}

/**
 * SQL cache handler
 */
function db_get_cache($query, $type = '')
{
    global $memcache;

    if ($memcache) {

        return func_get_data_from_func(
            'inner_sql', 
            'func_dc_sql', 
            array(
                md5($query),
                $query,
                $type,
            ), 
            SQL_CACHE_TTL
        );

    }

    return func_data_cache_get(
        'sql',
        array(
            'md5'   => md5($query),
            'query' => $query,
            'type'  => $type,
        )
    );
}

function func_detect_config_changes($query)
{
    global $sql_tbl;

    $query = strtoupper($query);

    return false !== strpos($query, strtoupper($sql_tbl['config']))
        && (
            false !== strpos($query, 'UPDATE')
            || false !== strpos($query, 'INSERT')
            || false !== strpos($query, 'REPLACE')
        );
}

function func_add_delayed_query($query, $query_type)
{
    global $sql_tbl;

    db_query("INSERT INTO $sql_tbl[delayed_queries] (query_type, query, date) VALUES('$query_type','" . addslashes($query) . "','".XC_TIME."')");
}

function func_run_delayed_query($query_type = '')
{
    global $sql_tbl, $current_area;
    
    $flush_threshold = array(
        'views_stats_products' => 10
    );

    // Do not use sorting for views_stats_products/views_stats_categories query types
    $sort_by = "ORDER BY id ASC";
    if (strpos($query_type, 'views_stats') !== false)
        $sort_by = '';

    if (strpos($query_type, '%') !== false) {
        $where = " WHERE query_type LIKE '$query_type' ";

    } elseif(!empty($query_type)) {
        $where = " WHERE query_type='$query_type' ";

    } else {
        $where = '';
    }

    $flush_queries = true;
    if (
        $current_area == 'C' 
        && isset($flush_threshold[$query_type])
    ) {
        $count = func_query_first_cell("SELECT COUNT(id) FROM $sql_tbl[delayed_queries] $where");
        if ($count < intval($flush_threshold[$query_type]))
            $flush_queries = false;
    }

    if ($flush_queries) {
        $res = db_query("SELECT query FROM $sql_tbl[delayed_queries] $where $sort_by");

        $is_found = false;
        while ($row = db_fetch_array($res)) {
            db_query($row['query']);
            $is_found = true;
        }

        db_free_result($res);

        if ($is_found) {
            db_query("DELETE FROM $sql_tbl[delayed_queries] $where");
            return true;
        }
    }

    return false;
}

?>
