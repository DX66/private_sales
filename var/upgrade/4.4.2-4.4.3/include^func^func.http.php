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
 * HTTP & HTTPS subsystem
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.http.php,v 1.37.2.1 2011/01/10 13:11:51 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load('https_libcurl');

/**
 * Extract cookies from headers.
 * Note: $cookies array should contain only "SET-COOKIE" headers.
 */
function func_parse_cookie_array(&$http_header, $cookies)
{
    $deleted = array();
    $valid = array();
    foreach ($cookies as $line) {
        if (!preg_match_all('!^\s*([^\n\r=]+)=([^\r\n; ]+)?!S', $line, $m))
            continue;

        if (empty($m[1]) || !is_array($m[1]))
            continue;

        foreach ($m[1] as $k=>$v) {
            if ($m[2][$k] == 'deleted') {
                $deleted[$v] = true;
                if (isset($valid[$v]))
                    unset($valid[$v]);
            }
            else {
                $valid[$v] = $m[2][$k];
                if (isset($deleted[$v]))
                    unset($deleted[$v]);
            }
        }
    }

    $http_header['cookies_deleted'] = $deleted;
    $http_header['cookies'] = $valid;
}

function func_fsockopen_request($method, $host, $post_url, $post_str = '', $post_cookies = array(), $user = '', $pass = '', $timeout = 30)
{
    global $config;

    $request_url = "http://".$host.$post_url.(($method == 'POST') ? '' : "?".$post_str);

    if (!empty($config['General']['https_proxy'])) {
        $matches = array_reverse(explode("@", $config['General']['https_proxy']));
        list($proxy_host, $proxy_port) = explode(":", $matches[0]);
        list($proxy_user, $proxy_pass) = explode(":", $matches[1]);
    }

    list($request_host, $request_port) = explode(":", $host);
    if (!isset($request_port) || !is_numeric($request_port)) {
        $request_port = 80;
        $host = $request_host.":".$request_port;
    }

    $cookie = '';
    $result = '';
    $header_passed = false;

    if (!($fp = @fsockopen($request_host, $request_port, $errno, $errstr, $timeout))) {
        $fp = @fsockopen($proxy_host, $proxy_port, $errno, $errstr, $timeout);
    }

    if (!$fp) {

        if (defined('X_SHOW_HTTP_ERRORS')) {
            echo "Could not open socket connection to ".$request_url." for ".$method." request<br />";
        }
        return array('', '');

    } else {

        fputs($fp, $method." ".$request_url." HTTP/1.0\r\n");

        fputs($fp, "Host: ".$host."\r\n");
        fputs($fp, "User-Agent: Mozilla/4.5 [en]\r\n");

        if ($user) {
            fputs($fp, "Authorization: Basic ".base64_encode($user.($pass ? ":".$pass : ''))."\r\n");
        }

        if ($proxy_user) {
            fputs($fp, "Proxy-Authorization: Basic ".base64_encode($proxy_user.($proxy_pass ? ":".$proxy_pass : ''))."\r\n");
        }

        if (!empty($post_cookies)) {
            fputs($fp, "Cookie: ".join("; ",$post_cookies)."\r\n");
        }

        if ($method == 'POST') {
            fputs($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
            fputs($fp, "Content-Length: ".strlen($post_str)."\r\n");
            fputs($fp, "\r\n");
            fputs($fp, $post_str."\r\n\r\n");
        }

        fputs($fp,"\r\n");

        if (doubleval(phpversion()) >= 4.3) {
            @stream_set_timeout($fp, $timeout);
        }

        $http_header = array ();
        $http_header['ERROR'] = chop(fgets($fp,4096));
        $cookies = array ();

        while (!feof($fp)) {
            if (!$header_passed) {
                $line = fgets($fp, 4096);
            } else {
                $result .= fread($fp, 65536);
            }

            if ($header_passed == false && ($line == "\n" || $line == "\r\n")) {
                $header_passed = true;
                continue;
            }

            if ($header_passed == false) {
                $header_line = explode(": ", $line, 2);
                $header_line[0] = strtoupper($header_line[0]);
                $http_header[$header_line[0]] = chop($header_line[1]);

                if ($header_line[0] == "SET-COOKIE") {
                    array_push($cookies, chop($header_line[1]));
                }
            }
        }

        fclose($fp);

    }

    func_parse_cookie_array($http_header, $cookies);

    return array($http_header, $result);
}

function func_http_get_request($host, $post_url, $post_str, $post_cookies=array(), $user = false, $pass = false)
{
    if (function_exists('curl_init')) {

        $fix_http_url = func_https_fix_url("http://" . $host . $post_url, false);

        return func_http_request_libcurl(
            'GET',
            $fix_http_url ."?" . $post_str,
            '',
            ($user)
                ? array(
                    'Authorization' => "Basic "
                        . base64_encode(
                            $user
                            . ($pass ? ":" . $pass : '')
                        ),
                )
                : '',
            $post_cookies,
            1500
        );
    }

    return func_fsockopen_request('GET', $host, $post_url, $post_str, $post_cookies, $user, $pass, 15);
}

function func_http_post_request($host, $post_url, $post_str, $cook = '', $user = false, $pass = false)
{
    if (function_exists('curl_init')) {

        $fix_http_url = func_https_fix_url("http://" . $host . $post_url, false);

        return func_http_request_libcurl(
            'POST',
            $fix_http_url,
            $post_str,
            ($user)
                ? array(
                    'Authorization' => "Basic " . base64_encode($user . ($pass ? ":" . $pass : ''))
                )
                : '',
            $cook,
            15
        );

    }

    return func_fsockopen_request('POST', $host, $post_url, $post_str, $cook, $user, $pass, 15);
}

/**
 * HEAD HTTP request
 */
function func_http_head_request($host, $post_url, $post_str, $post_cookies=array(), $user = false, $pass = false)
{
    if (function_exists('curl_init')) {

        $fix_http_url = func_https_fix_url("http://" . $host . $post_url, false);

        return func_http_request_libcurl(
            'HEAD',
            $fix_http_url,
            $post_str,
            ($user)
                ? array(
                    'Authorization' => "Basic " . base64_encode($user . ($pass ? ":" . $pass : ''))
                )
                : '',
            $post_cookies,
            15
        );

    }

    return func_fsockopen_request('HEAD', $host, $post_url, $post_str, $post_cookies, $user, $pass, 15);
}

/**
 * Prepare request for HTTPS request
 */
function func_https_prepare_request($method, $parsed_url, $data = '', $join = "&", $cookie = '', $conttype = "application/x-www-form-urlencoded", $referer = '', $headers = '')
{
    if ($parsed_url['query']) {

        $parsed_url['location'] = $parsed_url['path']."?".$parsed_url['query'];

    } else {

        $parsed_url['location'] = $parsed_url['path'];

    }

    // HTTP/1.0 protocol (RFC1945) does not support the "Host:" header,
    // so HTTP/1.0 server should ignore them. Currently all
    // HTTP/1.1 (RFC2616) servers accept these headers without errors.

    // "Host:" header is important for virtual "name-based" hostings.

    $request = array();

    $request[] = $method." ".$parsed_url['location']." HTTP/1.0";
    $request[] = "Host: ".$parsed_url['host'];
    $request[] = "User-Agent: Mozilla/4.5 [en]";

    // Additional headers
    if ($headers != '') {
        foreach($headers as $k=>$v) {
            if (is_integer($k)) {
                $request[] = $v;
            }
            else {
                $request[] =$k.": ".$v;
            }
        }
    }

    if ($method == 'POST') {
        if ($data) {
            if ($join && is_array($data)) {
                foreach ($data as $k=>$v){
                    list($a, $b) = explode("=", trim($v), 2);
                    $data[$k]=$a."=".urlencode($b);
                }
            }

            if (is_array($data))
                $data = join($join,$data);
        }

        $request[] = "Content-Type: $conttype";
        $request[] = "Content-Length: ".strlen($data);
    }

    if ($cookie)
        $request[] = "Cookie: ".join('; ',$cookie);

    if ($referer)
        $request[] = "Referer: $referer";

    if (!empty($parsed_url['user']) && !empty($parsed_url['pass']))
        $request[] = "Authorization: Basic ".base64_encode($parsed_url['user'].":".$parsed_url['pass']);

    $request[] = '';
    if ($method == 'POST') {
        $request[] =  $data;
        $request[] = '';

    }

    $request[] = '';

    return join("\r\n",$request);
}

/**
 * Receive response from pipe and separate it into headers and data
 */
function func_https_receive_result($connection)
{
    $get_state = 0;
    $headers = '';
    $result = '';
    $debug = '';

    while (!feof($connection)) {
        $line = fgets($connection, 65536);

        switch ($get_state){
            case 0: // strip out any possible debug output
                if (!strncmp($line, 'HTTP', 4)) {
                    $debug .= $line;
                    break;
                }

            // FALL-THROUGH
            case 1: // get headers

                if (trim($line) === '' ) { // end of headers

                    $get_state = 2;

                } else {

                    $headers .= $line;
                    $get_state = 1;

                }

                break;
            case 2: // get data
                $result .= $line;
        }

    }

    if (empty($headers)) {

        return array('0', $debug);

    } else {

        return array($debug . $headers, $result);

    }
}

/**
 * Generic transport function for HTTPS
 */
function func_https_tunnel_request($connection, $method, $parsed_url, $data = '', $join = "&", $cookie = '', $conttype = "application/x-www-form-urlencoded", $referer = '', $headers = '')
{
    $request = func_https_prepare_request($method, $parsed_url,$data,$join,$cookie,$conttype,$referer,$headers);

    fputs($connection, $request);

    return func_https_receive_result($connection);
}

// Control function for HTTPS subsystem
/**
 * Currently used as internal buffer. Contents of internal buffer are used for
 * logging reasons later. (e.g. when payment transaction is failed)
 */
// Available commands:
//  PUT - store content in internal buffer
//  GET - get content from internal buffer
//  IGNORE - ignore 'PUT' commands
//  STORE - do not ignore 'PUT' commands
//  PURGE - clean internal buffer

function func_https_ctl($command, $arg = false)
{
    static $responses = array();
    static $store_responses = true;

    switch ($command) {
    case 'GET':
        return $responses;
    case 'PUT':
        if ($store_responses) {
            list($sec, $usec) = explode(' ', microtime());
            $label = date('d-m-Y H:i:s', $sec).' '.$usec;
            $responses[$label] = $arg;
        }
        return true;
    case 'PURGE':
        $responses = array();
        break;
    case 'STORE':
        $store_responses = true;
        break;
    case 'IGNORE':
        $store_responses = false;
        break;
    }

    return false;
}

/**
 * Perform HTTPS request using GET or POST methods
 * For full list of parameters see include/func/func.https_*.php
 */
function func_https_request()
{
    global $httpsmod_active, $config;

    if (is_null($httpsmod_active) || !isset($httpsmod_active)) {
        x_load('tests');
        $httpsmod_active = test_active_bouncer();
    }

    if (!empty($httpsmod_active))
        x_load('https_'.$httpsmod_active);

    $func = 'func_https_request_'.$httpsmod_active;
    if (empty($httpsmod_active) || !function_exists($func)) {
        $result = array('0',"X-Cart HTTPS: could not find suitable HTTPS module to commit secure transaction.");
    }
    else {
        $args = func_get_args();
        $args[1] = func_https_fix_url($args[1]); // fix URL (e.g. add :443). for details see include/func/func.https_*.php
        $result = call_user_func_array($func, $args);
    }

    func_https_ctl('PUT', $result);

    if ($result[0] == '0') {
        // log HTTPS bouncer errors
        $error_msg = "HTTPS module: $httpsmod_active\nError message: ".addslashes($result['1']);
        x_log_flag('log_https', 'HTTPS', $error_msg);

        $do_email = isset($config['Logging']['log_https']) && (strpos($config['Logging']['log_https'], "E") !== false);

        $result[1] = func_get_langvar_by_name($do_email ? 'lbl_payment_error_no_info' : 'lbl_payment_error_with_info', $do_email ? NULL : array('email_address' => $config['Company']['site_administrator'])). ($do_email ? '' : "<br /><br />" . $result[1]);

        if (defined('X_SHOW_HTTP_ERRORS'))
            echo $error_msg."<br />";
    }

    return $result;
}

/**
 * Correct function URL for https modules. (use setting '_https' parameter to 'false' to work with HTTP scheme)
 * Currently add port (:443 or :80) when not mentioned
 */
function func_https_fix_url($url, $_https=true)
{
    $p = @parse_url($url);
    if (!is_array($p) || empty($p['scheme']) || $p['scheme'] != ($_https ? 'https': 'http'))
        return false;

    if (empty($p['port']))
        $p['port'] = $_https ? 443 : 80;

    if (empty($p['path']))
        $p['path'] = '/';

    $r = $_https ? 'https://' : "http://";
    if (!empty($p['user'])) {
        $r .= $p['user'];
        if (!empty($p['pass']))
            $r .= ':'.$p['pass'];
        $r .= '@';
    }

    $r .= $p['host'].':'.$p['port'].$p['path'];
    if (!empty($p['query']))
        $r .= '?'.$p['query'];

    if (!empty($p['fragment']))
        $r .= '#'.$p['fragment'];

    return $r;
}

/**
 * Process redirection HTTP headers
 */
function func_process_http_redirect($headers, $url = false)
{
    $location = false;
    if (is_array($headers)) {
        if (isset($headers['LOCATION']) && !empty($headers['LOCATION']))
            $location = trim($headers['LOCATION']);

    } elseif (preg_match("/Location[ \t]*:[ \t]+(\S+)/is", $headers, $match)) {
        $location = trim($match[1]);
    }

    if (empty($location))
        return false;

    if (!empty($url) && !is_url($location)) {
        $first = substr($location, 0, 1);

        $parse = @parse_url($url);

        $url = (!empty($parse['scheme']) ? $parse['scheme'] : 'http')."://";
        if (!empty($parse['user'])) {
            $url .= $parse['user'];
            if (!empty($parse['pass']))
                $url .= ":".$parse['pass'];

            $url .= "@";
        }
        $url .= $parse['host'];
        if ($parse['port'])
            $url .= ":".$parse['port'];

        if ($first == '/') {
            $url .= $location;

        } elseif ($first == '?') {
            $url .= $parse['path'].$location;

         } elseif ($first == '#') {
            $url .= $parse['path'];
            if ($parse['query']);
                $url .= "?".$parse['query'];

            $url .= $location;

        } elseif (preg_match("/^[\w\d_]+\.+[\w\d_]+/", $location)) {
            $url .= preg_replace("/\/[\w\d_]+\.+[\w\d_]+/", '/', $parse['path']).$location;

        } else {
            return $location;
        }

        $location = $url;
    }

    return $location;
}
?>
