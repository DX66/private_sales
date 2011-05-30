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
 * OpenSSL HTTPS module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.https_openssl.php,v 1.19.2.1 2011/01/10 13:11:51 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

// INPUT:
//
// $method          [string: POST|GET]
//
// $url             [string]
//  www.yoursite.com:443/path/to/script.asp
//
// $data            [array]
//  $data[] = "parametr=value";
//
// $join            [string]
//  $join = "\&";
//
// $cookie          [array]
//  $cookie = "parametr=value";
//
// $conttype        [string]
//  $conttype = 'text/xml';
//
// $referer         [string]
//  $conttype = "http://www.yoursite.com/index.htm";
//
// $cert            [string]
//  $cert = "../certs/demo-cert.pem";
//
// $kcert           [string]
//  $keyc = "../certs/demo-keycert.pem";
//
// $rhead           [string]
//  $rhead = '...';
//
// $rbody           [string]
//  $rbody = '...';
//
// [15:53][mclap@rrf:S4][~]$ openssl version
// OpenSSL 0.9.7a Feb 19 2003

function func_https_request_openssl($method, $url, $data="", $join="&", $cookie="", $conttype="application/x-www-form-urlencoded", $referer="", $cert="", $kcert="", $headers="", $timeout = 0, $use_ssl3 = false)
{

    if ($method != 'POST' && $method != 'GET')
        return array('0',"X-Cart HTTPS: Invalid method");

    if (!preg_match("/^(https?:\/\/)(.*\@)?([a-z0-9_\.\-]+):(\d+)(\/.*)$/Ui",$url,$m))
        return array('0',"X-Cart HTTPS: Invalid URL");

    $openssl_binary = func_find_executable('openssl');
    if (!$openssl_binary)
        return array('0',"X-Cart HTTPS: openssl executable is not found");

    if (!X_DEF_OS_WINDOWS)
        putenv("LD_LIBRARY_PATH=".getenv('LD_LIBRARY_PATH').":".dirname($openssl_binary));

    $ui = @parse_url($url);

    // build args
    $args[] = "-connect $ui[host]:$ui[port]";
    if ($cert) $args[] = '-cert '.func_shellquote($cert);
    if ($kcert) $args[] = '-key '.func_shellquote($kcert);

    if ($use_ssl3)
        $args[] = '-ssl3';

    $request = func_https_prepare_request($method, $ui,$data,$join,$cookie,$conttype,$referer,$headers);
    $tmpfile = func_temp_store($request);
    $tmpignore = func_temp_store('');

    if (empty($tmpfile)) {
        @unlink($tmpignore);
        return array(0, "X-Cart HTTPS: cannot create temporaly file");
    }

    $cmdline = func_shellquote($openssl_binary)." s_client ".join(' ',$args)." -quiet < ".func_shellquote($tmpfile)." 2>".func_shellquote($tmpignore);

    // make pipe
    $fp = popen($cmdline, 'r');

    x_log_tmp_file($tmpignore);

    if( !$fp ) {
        @unlink($tmpfile);
        @unlink($tmpignore);
        return array(0, "X-Cart HTTPS: openssl execution failed");
    }

    $res = func_https_receive_result($fp);
    pclose($fp);

    @unlink($tmpfile);
    @unlink($tmpignore);

    return $res;
}

?>
