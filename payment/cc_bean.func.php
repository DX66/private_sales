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
 * "" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_bean.func.php,v 1.17.2.2 2011/03/01 09:56:31 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

function func_cc_bean_verify($md, $return_url)
{
    global $module_params, $secure_oid, $userinfo, $cart, $bill_name;

    $return_url = preg_replace("/\?.*$/", '', $return_url);

    $post = array(
        "requestType=BACKEND",
        "merchant_id=".$module_params['param01'],
        "trnCardOwner=".urlencode($userinfo['card_name']),
        "trnCardNumber=".$userinfo['card_number'],
        "trnCardCvd=".$userinfo['card_cvv2'],
        "trnExpMonth=".substr($userinfo['card_expire'],0,2),
        "trnExpYear=".substr($userinfo['card_expire'],2,2),
        "trnOrderNumber=".$module_params['param04'].join("-", $secure_oid).'VBV',
        "trnAmount=".$cart['total_cost'],
        "ordEmailAddress=".$userinfo['email'],
        "ordName=".urlencode($bill_name),
        "ordPhoneNumber=".urlencode($userinfo['phone']),
        "ordAddress1=".urlencode($userinfo['b_address']),
        "ordCity=".urlencode($userinfo['b_city']),
        "ordProvince=".(strlen($userinfo['b_state']) != 2 ? "--" : $userinfo['b_state']),
        "ordPostalCode=".urlencode($userinfo['b_zipcode']),
        "ordCountry=".$userinfo['b_country'],
        "termUrl=".$return_url,
    );

    list($a, $return) = func_https_request('POST', "https://www.beanstream.com:443/scripts/process_transaction.asp", $post);

    $res = array();
    parse_str($return, $res);

    if (isset($res['pageContents']))
        $res['pageContents'] = stripslashes(stripslashes($res['pageContents']));

    $r = array();
    if (
        $res['responseType'] == 'R' &&
        preg_match("/<form [^>]*action\s*=\s*['\"]?([^'\"\s>]+)['\"]?[\s>]/si", $res['pageContents'], $url_match) &&
        preg_match_all("/<input ([^>]+)>/si", $res['pageContents'], $data_match)
    ) {
        $r['no_iframe'] = 'Y';
        $r['form_url'] = $url_match[1];
        $r['form_data'] = array();
        foreach($data_match[1] as $v) {
            $params = func_array_map('trim', preg_split("/\s/", $v));
            $name = false;
            $value = false;
            foreach($params as $p) {
                if (preg_match("/name\s*=\s*['\"]?([^'\"]+)['\"]?/S", $p, $match))
                    $name = $match[1];
                elseif (preg_match("/value\s*=\s*['\"]?([^'\"]+)['\"]?/S", $p, $match))
                    $value = $match[1];
            }

            if (!empty($name) && !empty($value))
            $r['form_data'][$name] = $value;
        }

    } elseif (count($res) > 1 && $res['messageId'] && $res['messageText']) {
        $r['data'] = $res;

    } else {
        $r['error_msg'] = $return;
    }

    if (isset($r['form_data']['MD']))
        $r['md'] = $r['form_data']['MD'];

    return $r;
}

function func_cc_bean_validate()
{

    $data = array();

    if (!empty($_GET) && is_array($_GET)) {
        foreach($_GET as $k => $v)
            $data[] = $k."=".$v;
    }

    if (!empty($_POST) && is_array($_POST)) {
        foreach($_POST as $k => $v)
            $data[] = $k."=".$v;
    }

    list($a, $return) = func_https_request('POST', "https://www.beanstream.com:443/scripts/process_transaction_auth.asp", $data);

    $res = array();
    parse_str($return, $res);

    $r = array();
    if (is_array($res) && count($res) > 3) {
        $r['data'] = $res;

    } else {
        $r['error'] = -1;
        $r['error_msg'] = $return;
    }

    return $r;
}

?>
