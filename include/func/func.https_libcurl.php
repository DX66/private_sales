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
 * LibCurl HTTPS module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.https_libcurl.php,v 1.25.2.1 2011/01/10 13:11:51 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

// INPUT:
//
// $method          [string: POST|GET|HEAD]
//
// $url             [string]
//    www.yoursite.com:443/path/to/script.asp
//
// $data            [array]
//    $data[] = "parametr=value";
//
// $join            [string]
//    $join = "\&";
//
// $cookie          [array]
//    $cookie = "parametr=value";
//
// $conttype        [string]
//    $conttype = 'text/xml';
//
// $referer         [string]
//    $conttype = "http://www.yoursite.com/index.htm";
//
// $cert            [string]
//    $cert = "../certs/demo-cert.pem";
//
// $kcert           [string]
//    $keyc = "../certs/demo-keycert.pem";
//
// OUTPUT:
//
// array($headers,$body );
//
// There is a bug in cURL version 7.9.4 that can cause problems form posts.
// http://curl.haxx.se/mail/lib-2002-02/0029.html

function __curl_headers()
{
    static $headers = '';

    $args = func_get_args();
    if (count($args) == 1) {
        $return = '';
        if ($args[0] == true) $return = $headers;
        $headers = '';
        return $return;
    }

    if (trim($args[1]) != '') $headers .= $args[1];
    return strlen($args[1]);
}

/**
 * Make HTTP request using libCURL
 */
function func_http_request_libcurl($method, $url, $data="", $headers="", $cookie="", $timeout = 0, $conttype="application/x-www-form-urlencoded", $referer="", $join="&")
{
    return func_request_libcurl($method, $url, $data, $join, $cookie, $conttype, $referer, '', '', $headers, $timeout, false, false);
}

/**
 * Make HTTPS request using libCURL
 */
function func_https_request_libcurl($method, $url, $data="", $join="&", $cookie="", $conttype="application/x-www-form-urlencoded", $referer="", $cert="", $kcert="", $headers="", $timeout = 0, $use_ssl3 = false)
{
    return func_request_libcurl($method, $url, $data, $join, $cookie, $conttype, $referer, $cert, $kcert, $headers, $timeout, $use_ssl3, true);
}

/**
 * Common libCURL requestor
 */
function func_request_libcurl($method, $url, $data="", $join="&", $cookie="", $conttype="application/x-www-form-urlencoded", $referer="", $cert="", $kcert="", $headers="", $timeout = 0, $use_ssl3 = false, $_https=true)
{
    global $config;
    global $SERVER_ADDR;

    if (intval($timeout) <= 0)
        $timeout = 120;

    if (!function_exists('curl_init'))
        return array('0',"X-Cart HTTPS: libcurl is not supported");

    if ($method != 'POST' && $method!="GET" && $method != 'HEAD')
        return array('0',"X-Cart HTTPS: Invalid method");

    if (!preg_match("/^(".($_https ? 'https' : 'http')."?:\/\/)(.*\@)?([a-z0-9_\.\-]+):(\d+)(\/.*)$/Ui",$url,$m))
        return array('0',"X-Cart HTTPS: Invalid URL");

    if ($headers != '') {
        $_headers = array();
        foreach($headers as $k=>$v) {
            $_headers[] = is_integer($k) ? $v : ($k.": ".$v);
        }
        $headers = $_headers;
        unset($_headers);
    }
    $headers[] = "Content-Type: ".addslashes($conttype);

    $version = curl_version();
    if (is_array($version)) {
        $version = 'libcurl/'.$version['version'];
    }

    $supports_insecure = false;
    // insecure key is supported by curl since version 7.10
    if (preg_match('/libcurl\/([^ $]+)/', $version, $m) ){
        $parts = explode('.',$m[1]);
        if ($parts[0] > 7 || ($parts[0] = 7 && $parts[1] >= 10))
            $supports_insecure = true;
    }

    $ch = curl_init();
    if (!empty($config['General']['https_proxy'])) {
        // uncomment this line if you need proxy tunnel
        // curl_setopt ($ch, CURLOPT_HTTPPROXYTUNNEL, true);
        curl_setopt ($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
        curl_setopt ($ch, CURLOPT_PROXY, $config['General']['https_proxy']);
    }
    curl_setopt ($ch, CURLOPT_URL, $url);

    if (!empty($SERVER_ADDR))
        curl_setopt($ch, CURLOPT_INTERFACE, $SERVER_ADDR);

    if ($referer)
        curl_setopt ($ch, CURLOPT_REFERER, $referer);
    curl_setopt ($ch, CURLOPT_HEADER, 0);
    curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
    if ($cert) {
        curl_setopt ($ch, CURLOPT_SSLCERT, $cert);
        if ($kcert) { // M:20474
            $kcert_array = explode(':::', $kcert);
            if (!empty($kcert_array[0])) {
                curl_setopt ($ch, CURLOPT_SSLKEY, $kcert_array[0]); // key
            }
            if (!empty($kcert_array[1])) {
                curl_setopt ($ch, CURLOPT_SSLCERTPASSWD, $kcert_array[1]); // pass
            }
        }
    }

    if (!empty($cookie))
        curl_setopt ($ch, CURLOPT_COOKIE, implode("; ", $cookie));

    // Set TimeOut parameter
    $timeout = abs(intval($timeout));
    if (!empty($timeout)) {
        curl_setopt ($ch, CURLOPT_TIMEOUT, $timeout);
    }

    if ($supports_insecure) {
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 1);
    }

    if ($use_ssl3)
        curl_setopt ($ch, CURLOPT_SSLVERSION, 3);

    if( $method == 'GET' )
        curl_setopt ($ch, CURLOPT_HTTPGET, 1);
    elseif ($method == 'POST') {
        curl_setopt ($ch, CURLOPT_POST, 1);
        if($data) {
            if($join && is_array($data)){
                foreach($data as $k=>$v){
                    list($a, $b) = explode("=", trim($v), 2);
                    $data[$k]=$a."=".urlencode($b);
                }
            }
            if (is_array($data))
                $data = join($join,$data);
            curl_setopt ($ch, CURLOPT_POSTFIELDS, $data);
        }
    } else {
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    }

    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_HEADERFUNCTION, '__curl_headers');
    __curl_headers(false);

    $body = curl_exec ($ch);
    $errno = curl_errno ($ch); $error = curl_error($ch);
    curl_close ($ch);
    if( $error )
        return array('0',"X-Cart HTTPS: libcurl error($errno): $error");
    return array(__curl_headers(true), $body);
}

?>
