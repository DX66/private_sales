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
 * "First Data Global Gateway - LinkPoint" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_linkpoint.php,v 1.57.2.1 2011/01/10 13:12:06 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

func_set_time_limit(100);

x_load('http');
x_session_register('linkpoint_ids');

$pp_login = $module_params['param01'];
$sert = $module_params['param02'];
$host =  $module_params['param06'];
$port = $module_params['param07'];
$lp_test = 'LIVE';
switch($module_params['testmode']) {
    case 'A': $lp_test = 'GOOD'; break;
    case 'D': $lp_test = 'DECLINE'; break;
}

$addrnum = preg_replace("/[^\d]/",'',$userinfo['b_address']);

$tid = $origtid = $module_params['param05'].join("-",$secure_oid);
$i = 1;
while (is_array($linkpoint_ids) && in_array($tid, $linkpoint_ids)) {
    $tid = $origtid.'t'.$i++;
}
$linkpoint_ids[] = $tid;

$post = array();
$post[] = "<order>";

$post[] = "<orderoptions>";
$post[] = "<ordertype>PREAUTH</ordertype>";
$post[] = "<result>".$lp_test."</result>";
$post[] = "</orderoptions>";

$post[] = "<creditcard>";
$post[] = "<cardnumber>".$userinfo['card_number']."</cardnumber>";
$post[] = "<cardexpmonth>".substr($userinfo['card_expire'],0,2)."</cardexpmonth>";
$post[] = "<cardexpyear>".substr($userinfo['card_expire'],2,2)."</cardexpyear>";
$post[] = "<cvmvalue>".$userinfo['card_cvv2']."</cvmvalue>";
$post[] = "<cvmindicator>".$module_params['param04']."</cvmindicator>";
$post[] = "</creditcard>";

$post[] = "<merchantinfo>";
$post[] = "<configfile>".$pp_login."</configfile>";
$post[] = "<keyfile>".$sert."</keyfile>";
$post[] = "<host>".$host."</host><port>".$port."</port>";
$post[] = "</merchantinfo>";

$post[] = "<payment>";
$post[] = "<chargetotal>".price_format($cart['total_cost'])."</chargetotal>";
$post[] = "</payment>";

$post[] = "<billing>";
$post[] = "<name>".htmlspecialchars($bill_name)."</name>";
$post[] = "<address1>".htmlspecialchars($userinfo['b_address'])."</address1>";
$post[] = "<company>".htmlspecialchars($userinfo['company'])."</company>";
$post[] = "<address2>".htmlspecialchars($userinfo['b_address_2'])."</address2>";
$post[] = "<addrnum>".$addrnum."</addrnum>";
$post[] = "<city>".htmlspecialchars($userinfo['b_city'])."</city>";
$post[] = "<state>".htmlspecialchars($userinfo['b_state'])."</state>";
$post[] = "<zip>".htmlspecialchars($userinfo['b_zipcode'])."</zip>";
$post[] = "<country>".htmlspecialchars($userinfo['b_country'])."</country>";
$post[] = "<phone>".htmlspecialchars($userinfo['phone'])."</phone>";
$post[] = "<fax>".htmlspecialchars($userinfo['fax'])."</fax>";
$post[] = "<email>".htmlspecialchars($userinfo['email'])."</email>";
$post[] = "</billing>";

$cnt = 0;
$weight = 0;
if (!empty($products)) {
    foreach ($products as $v) {
        $cnt += $v['amount'];
        $weight += $v['weight']*$v['amount'];
    }
}

$post[] = "<shipping>";
$post[] = "<name>".htmlspecialchars($userinfo['s_firstname']." ".$userinfo['s_lastname'])."</name>";
$post[] = "<address1>".htmlspecialchars($userinfo['s_address'])."</address1>";
$post[] = "<address2>".htmlspecialchars($userinfo['s_address_2'])."</address2>";
$post[] = "<city>".htmlspecialchars($userinfo['s_city'])."</city>";
$post[] = "<state>".htmlspecialchars($userinfo['s_state'])."</state>";
$post[] = "<zip>".htmlspecialchars($userinfo['s_zipcode'])."</zip>";
$post[] = "<country>".htmlspecialchars($userinfo['s_country'])."</country>";
$post[] = "<weight>".$weight."</weight>";
$post[] = "<items>".$cnt."</items>";
$post[] = "<total>".price_format($cart['total_cost']-$cart['shipping_cost'])."</total>";
$post[] = "</shipping>";

$post[] = "<transactiondetails>";
$post[] = "<oid>".$tid."</oid>";
$post[] = "<ip>".func_get_valid_ip($REMOTE_ADDR)."</ip>";
$post[] = "</transactiondetails>";

$post[] = "</order>";
list($a, $return) = func_https_request('POST', "https://$host:$port/LSGSXML", $post, '', '', "application/x-www-form-urlencoded", '', $sert, $sert);

$avserr = array(
    'YY' => "Address matches, zip code matches",
    'YN' => "Address matches, zip code does not match",
    'YX' => "Address matches, zip code comparison not available",
    'NY' => "Address does not match, zip code matches",
    'XY' => "Address comparison not available, zip code matches",
    'NN' => "Address comparison does not match, zip code does not match",
    'NX' => "Address does not match, zip code comparison not available",
    'XN' => "Address comparison not available, zip code does not match",
    'XX' => "Address comparisons not available, zip code comparison not available",
);

$cvverr = array(
    'M' => "Card Code Match",
    'N' => "Card code does not match",
    'P' => "Not processed",
    'S' => "Merchant has indicated that the card code is not present on the card",
    'U' => "Issuer is not certified and/or has not provided encryption keys"
);

$bill_output['avsmes'] = $bill_output['cvvmes'] = '';
$bill_output['code'] = 2;
preg_match("/<r_approved>(.*)<\/r_approved>/",$return, $status);

$bill_output['billmes'] = '';
if (preg_match("/<r_code>(.*)<\/r_code>/", $return, $out)) {
    $bill_output['billmes'] .= "Code [".$out[1]."] :: ";
    if (preg_match("/(\w{6})(\w{10}):(\w{2})(\w)([ \w]):(.*):$/", trim($out[1]), $pars)) {
        if ($module_params['param08'] == 'Y' && ($pars[3] == 'NN' || $pars[4] == 'N' || $pars[5] == 'N') && $status[1] == "APPROVED") {
            $status[1] = 'FRAUD';
        }

        if ($cvverr[$pars[5]])
            $bill_output['cvvmes'] = $cvverr[$pars[5]];
        if ($pars[5])
            $bill_output['cvvmes'].= " (CVV code: ".$pars[5].")";

        if ($avserr[$pars[3]])
            $bill_output['avsmes'] = $avserr[$pars[3]];
        if ($pars[3])
            $bill_output['avsmes'].= " (AVS code: ".$pars[3].$pars[4].")";
    }
}

if ($status[1] == 'APPROVED') {

    if ($module_params['use_preauth'] != "Y" && !func_is_preauth_force_enabled($secure_oid)) {
        $post[2] = "<ordertype>POSTAUTH</ordertype>";
        sleep(2);
        list($a,$return) = func_https_request('POST',"https://$host:$port/LSGSXML",$post,'','',"application/x-www-form-urlencoded",'',$sert,$sert);

        if (preg_match("/<r_approved>APPROVED<\/r_approved>/",$return)) {
            $bill_output['code'] = 1;
        } else {
            preg_match("/<r_approved>(.*)<\/r_approved>/",$return, $status);
        }

    } else {
        $bill_output['code'] = 1;
        $bill_output['is_preauth'] = true;

        $extra_order_data = array(
            'linkpoint_tid' => $tid,
            'capture_status' => 'A',
            'ccdata' => text_crypt($userinfo['card_number'].":".$userinfo['card_expire'])
        );

    }
}

if ($bill_output['code'] == 2) {
    preg_match("/<r_error>(.*)<\/r_error>/",$return,$out);
    $bill_output['billmes'] = "[".$status[1]."] ".$out[1].$bill_output['billmes'];
}

if (preg_match("/<r_authresponse>(.+)<\/r_authresponse>/",$return,$out))
    $bill_output['billmes'] .= " (AuthResponse: ".$out[1].")";

if (preg_match("/<r_message>(.+)<\/r_message>/",$return,$out))
    $bill_output['billmes'] .= " (Message: ".$out[1].")";

?>
