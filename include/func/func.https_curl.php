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
 * cURL HTTPS module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.https_curl.php,v 1.20.2.1 2011/01/10 13:11:51 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

// INPUT:
//
// $method          [string: POST|GET]
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
// $rhead           [string]
//    $rhead = '...';
//
// $rbody           [string]
//    $rbody = '...';

function func_https_request_curl($method, $url, $data="", $join="&", $cookie="", $conttype="application/x-www-form-urlencoded", $referer="", $cert="", $kcert="", $headers="", $timeout = 0, $use_ssl3 = false)
{
    global $config;

    if ($method != 'POST' && $method != 'GET')
        return array('0',"X-Cart HTTPS: Invalid method");

    if (!preg_match("/^(https?:\/\/)(.*\@)?([a-z0-9_\.\-]+):(\d+)(\/.*)$/SUi",$url,$m))
        return array('0',"X-Cart HTTPS: Invalid URL");

    $curl_binary = func_find_executable('curl');
    if (!$curl_binary)
        return array('0',"X-Cart HTTPS: curl executable is not found");

    if (!X_DEF_OS_WINDOWS)
        putenv("LD_LIBRARY_PATH=".getenv('LD_LIBRARY_PATH').":".dirname($curl_binary));

    $tmpfile = func_temp_store('');

    if (empty($tmpfile))
        return array(0, "X-Cart HTTPS: cannot create temporaly file");

    $execline = func_shellquote($curl_binary)." --http1.0 -D-";
    @exec(func_shellquote($curl_binary)." --version", $output);
    $version = @$output[0];
    // -k|--insecure key is supported by curl since version 7.10
    $supports_insecure = false;
    if (preg_match('/curl ([^ $]+)/S', $version, $m) ){
        $parts = explode('.',$m[1]);
        if( $parts[0] > 7 || ($parts[0] == 7 && $parts[1] >= 10) )
            $supports_insecure = true;
    }

    if (!empty($config['General']['https_proxy'])) {
        $execline .= " --proxy ".$config['General']['https_proxy'];
        // uncomment this line if you need proxy tunnel
        // $execline .= " --proxytunnel";
    }

    // Set GET method flag
    if ($method=="GET")
        $execline.= " --get";

    // Set TimeOut parameter
    $timeout = abs(intval($timeout));
    if (!empty($timeout)) {
        $execline.= " --connect-timeout ".$timeout." -m ".$timeout;
    }

    // Combine REQUEST string
    $request_file = false;
    if ($data) {
        if ($join && is_array($data)) {
            foreach($data as $k => $v) {
                list($a, $b) = explode("=", trim($v), 2);
                $data[$k] = $a."=".urlencode($b);
            }
        }

        $request_file = func_temp_store(is_array($data) ? join($join, $data) : $data);
        $execline .= " -d ".func_shellquote('@'.$request_file);
    }

    // Add SSL Certificate
    if ($cert) {
        $execline.= " --cert ".func_shellquote($cert);

        // Add SSL Key-Certificate
        if ($kcert)
            $execline.= " --key ".func_shellquote($kcert);
    }

    if ($supports_insecure )
        $execline.= " -k ";

    if ($cookie)
        $execline.=" --cookie ".func_shellquote(join(';',$cookie));

    // Add Content-Type...
    if ($conttype != "application/x-www-form-urlencoded") {
        $execline.=" -H ".func_shellquote('Content-Type: '.$conttype);
    }

    // Add referer
    if ($referer != '') {
        $execline.=" -H ".func_shellquote('Referer: '.$referer);
    }

    // Additional headers
    if ($headers != '') {
        foreach ($headers as $k=>$v) {
            if (is_integer($k)) {
                $execline .= " -H \"".addslashes($v)."\"";
            }
            else {
                $execline .= " -H \"$k: ".addslashes($v)."\"";
            }
        }
    }

    // SSL 3
    if ($use_ssl3)
        $execline .= ' --sslv3';

    $fp = popen($execline." ".func_shellquote($url)." 2>".func_shellquote($tmpfile), 'r');
    if (!$fp) {
        @unlink($tmpfile);
        return array(0, "X-Cart HTTPS: curl execution failed");
    }

    //$fp = str_ireplace("HTTP/1.0 200 Connection established\r\n\r\n", '', $fp);
    $res = func_https_receive_result($fp);

    pclose($fp);
    @unlink($tmpfile);
    if ($request_file !== false)
        @unlink($request_file);

    func_https_ctl('PUT', $res);

    return $res;
}

?>
