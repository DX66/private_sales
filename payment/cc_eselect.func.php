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
 * Functions for eSelect Plus DirectPost 3
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_eselect.func.php,v 1.18.2.1 2011/01/10 13:12:06 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('http');

function func_cc_eselect_verify($tranid, $return_url)
{
    global $cart, $userinfo, $module_params, $HTTP_ACCEPT, $HTTP_USER_AGENT;

    $id = sprintf("%'920d", XC_TIME);
    $amount = sprintf("%.02f", $cart['total_cost']);
    $pan = $userinfo['card_number'];
    $expdate = substr($userinfo['card_expire'],2,2).substr($userinfo['card_expire'],0,2);

    $return_url = preg_replace("/&(?!amp;)/S", "&amp;", func_eselect_stripxml($return_url));
    $accept = func_eselect_stripxml($HTTP_ACCEPT);
    $user_agent = func_eselect_stripxml($HTTP_USER_AGENT);

    $md = $tranid;

    $store_id = $module_params['param01'];
    $api_token = $module_params['param02'];

    $xml = <<<XML
<?xml version="1.0"?>
<MpiRequest>
    <store_id>$store_id</store_id>
    <api_token>$api_token</api_token>
    <txn>
        <xid>$id</xid>
        <amount>$amount</amount>
        <pan>$pan</pan>
        <expdate>$expdate</expdate>
        <MD>$md</MD>
        <merchantUrl>$return_url</merchantUrl>
        <accept>$accept</accept>
        <userAgent>$user_agent</userAgent>
        <currency></currency>
        <recurFreq></recurFreq>
        <recurEnd></recurEnd>
        <install></install>
    </txn>
</MpiRequest>
XML;

    $xml = preg_replace("/>\s+</s", "><", $xml);

    if ($module_params['param03'] == 'CA')
        list($a, $return) = func_https_request('POST', "https://".(($module_params['testmode'] == 'Y') ? 'esqa' : 'www3').".moneris.com:443/mpi/servlet/MpiServlet", array($xml), '');
    else
        list($a, $return) = func_https_request('POST', "https://".(($module_params['testmode'] == 'Y') ? 'esplusqa' : 'esplus').".moneris.com:443/mpi/servlet/MpiServlet", array($xml), '');

    $pareq = false;
    $success = false;
    $termurl = false;
    $acsurl = false;
    $md = false;
    $message = false;

    if (preg_match("/<PaReq>(.+)<\/PaReq>/si", $return, $match))
        $pareq = $match[1];

    if (preg_match("/<success>(.+)<\/success>/is", $return, $match))
        $success = $match[1];

    if (preg_match("/<termurl>(.+)<\/termurl>/is", $return, $match))
        $termurl = $match[1];

    if (preg_match("/<acsurl>(.+)<\/acsurl>/is", $return, $match))
        $acsurl = $match[1];

    if (preg_match("/<md>(.+)<\/md>/is", $return, $match))
        $md = $match[1];

    if (preg_match("/<message>(.+)<\/message>/is", $return, $match))
        $message = $match[1];

    $r = array();
    if ($pareq && $success && $termurl && $acsurl && $md && $message == 'Y') {
        $r['no_iframe'] = 'Y';
        $r['form_url'] = $acsurl;
        $r['form_data'] = array(
            'PaReq' => $pareq,
            'MD' => $md,
            'TermUrl' => $termurl
        );
        $r['md'] = $md;

    } elseif ($message) {
        if (preg_match("/is NOT a VBV participant/", $message) || $message == 'N') {
            $r['data'] = array("crypt_type" => 6);

        } elseif ($message == 'U') {
            $r['data'] = array("crypt_type" => 7);

        } else {
            $r['error_msg'] = $match[1];
            $r['error_msg'] = preg_replace("/(?:stored_id|api_token)\s*=\s*['\"][^'\"]+['\"]/s", '', $r['error_msg']);
            $r['error_msg'] = preg_replace("/:\s*$/s", "", $r['error_msg']);
        }

    } else {
        $r['error_msg'] = "Internal error";
    }

    return $r;
}

function func_cc_eselect_validate()
{
    global $module_params, $PaRes, $MD;

    $store_id = $module_params['param01'];
    $api_token = $module_params['param02'];

    $pares = stripslashes($PaRes);
    $md = stripslashes($MD);

    $xml = <<<XML
<?xml version="1.0"?>
<MpiRequest>
    <store_id>$store_id</store_id>
    <api_token>$api_token</api_token>
    <acs>
        <PaRes>$pares</PaRes>
        <MD>$md</MD>
    </acs>
</MpiRequest>
XML;

    $xml = preg_replace("/>\s+</s", "><", $xml);

    if ($module_params['param03'] == 'CA')
        list($a, $return) = func_https_request("POST", "https://".(($module_params['testmode'] == 'Y') ? 'esqa' : 'www3').".moneris.com:443/mpi/servlet/MpiServlet", array($xml), '', '', 'text/xml');
    else
        list($a, $return) = func_https_request("POST", "https://".(($module_params['testmode'] == 'Y') ? 'esplusqa' : 'esplus').".moneris.com:443/mpi/servlet/MpiServlet", array($xml), '', '', 'text/xml');

    $success = false;
    $message = false;
    $cavv = false;

    if (preg_match("/<success>(.+)<\/success>/is", $return, $match))
        $success = $match[1];

    if (preg_match("/<message>(.+)<\/message>/is", $return, $match))
        $message = $match[1];

    if (preg_match("/<cavv>(.+)<\/cavv>/is", $return, $match))
        $cavv = $match[1];

    $r = array();
    if (strtolower($success) == 'true' && $cavv) {
        $r['data'] = array(
            'cavv' => $cavv
        );

    } elseif ($message) {
        $r['error_msg'] = $message;

    } else {
        $r['error_msg'] = "Internal error";
    }

    return $r;
}

function func_eselect_stripxml($str)
{
    return $str;
}

?>
