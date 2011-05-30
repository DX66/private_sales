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
 * eSelect Plus DirectPost 3
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_eselect.php,v 1.44.2.1 2011/01/10 13:12:06 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('http', "xml");

$cvd_indicator = array(
    '0' => "CVD value is deliberately bypassed or is not provided by the merchant.",
    '1' => "CVD value is present.",
    '2' => "CVD value is on the card, but is illegible.",
    '9' => "Cardholder states that the card has no CVD imprint."
);

$cvd_response_codes = array(
    'M' => 'Match',
    'N' => "No Match",
    'P' => "Not Processed",
    'S' => "CVD should be on the card, but Merchant has indicated that CVD is not present",
    'U' => "Issuer is not a CVD participant"
);

$avs_visa_codes = array(
    'A' => "Street addresses match. The street addresses match but the postal/ZIP codes do not, or the request does not include the postal/ZIP code.",
    'B' => "Street addresses match. Postal code not verified due to incompatible formats. (Acquirer sent both street address and postal code.)",
    'C' => "Street address and postal code not verified due to incompatible formats. (Acquirer sent both street address and postal code.)",
    'D' => "Street addresses and postal codes match.",
    'G' => "Address information not verified for international transaction.",
    'I' => "Address information not verified.",
    'M' => "Street address and postal code match.",
    'N' => "No match. Acquirer sent postal/ZIP code only, or street address only, or both postal code and street address.",
    'P' => "Postal code match. Acquirer sent both postal code and street address, but street address not verified due to incompatible formats.",
    'R' => "Retry: System unavailable or timed out. Issuer ordinarily performs its own AVS but was unavailable. Available for U.S. issuers only.",
    'S' => "Not applicable. If present, replaced with G (for international) or U (for domestic) by V.I.P. Available for U.S. Issuers only.",
    'U' => "Address not verified for domestic transaction. Visa tried to perform check on issuers behalf but no AVS information was available on record, issuer is not an AVS participant, or AVS data was present in the request but issuer did not return an AVS result.",
    'W' => "Not applicable. If present, replaced with Z by V.I.P. Available for U.S. issuers only.",
    'X' => "Not applicable. If present, replaced with Y by V.I.P. Available for U.S. issuers only.",
    'Y' => "Street address and postal code match.",
    'Z' => "Postal/ZIP matches; street address does not match or street address not included in request."
);

$avs_discover_codes = array(
    'X' => "All digits match, nine-digit zip code.",
    'A' => "All digits match, five-digit zip code.",
    'Y' => "Address matches, zip code does not match.",
    'T' => "Nine-digit zip code matches, address does not match.",
    'Z' => "Five-digit zip codes matches, address does not match.",
    'N' => "Nothing matches.",
    'W' => "No data from issuer/authorization system.",
    'U' => "Retry, system unable to process.",
    'S' => "AVS not supported at this time."
);

$is_preauth = ($module_params['use_preauth'] == 'Y' || func_is_preauth_force_enabled($secure_oid));

if ($module_params['param03'] == 'CA') {
    $txn_type = $is_preauth ? 'preauth' : 'purchase';
    if (isset($secure_3d) && isset($secure_3d['data']) && $secure_3d['data']['cavv'])
        $txn_type = 'cavv_purchase';

} else {
    $txn_type = $is_preauth ? 'us_preauth' : 'us_purchase';
    if (isset($secure_3d) && isset($secure_3d['data']) && $secure_3d['data']['cavv'])
        $txn_type = 'us_cavv_purchase';
}

$crypt_type = '';
if (isset($secure_3d) && isset($secure_3d['data']) && isset($secure_3d['data']['crypt_type']))
    $crypt_type = $secure_3d['data']['crypt_type'];

elseif (!isset($secure_3d) || !isset($secure_3d['data']))
    $crypt_type = '7';

$data = array(
    'request' => array(
        'store_id' => $module_params['param01'],
        'api_token' => $module_params['param02'],
        $txn_type => array(
            'order_id' => $module_params['param04'].(!empty($module_params['param04']) ? "-" : '') . XC_TIME . "-" . join("-",$secure_oid),
            'cust_id' => $login,
            'amount' => price_format($cart['total_cost']),
            'pan' => $userinfo['card_number'],
            'expdate' => substr($userinfo['card_expire'],2,2).substr($userinfo['card_expire'],0,2),
        )
    )
);

if (isset($secure_3d) && isset($secure_3d['data']) && isset($secure_3d['data']['cavv']))
    $data['request'][$txn_type]['cavv'] = $secure_3d['data']['cavv'];

if (!empty($crypt_type))
    $data['request'][$txn_type]['crypt_type'] = $crypt_type;

// Detect street name and street number
$street_data = false;
if (preg_match("/^\s*(\d+)\s+([\w\d ]+)/s", $userinfo['b_address'], $match)) {
    $street_data = array($match[1], trim($match[2]));

} elseif (preg_match("/([\w\d ]+)\s*[-,#\/]?\s+(\d+)/s", $userinfo['b_address'], $match)) {
    $street_data = array($match[2], trim($match[1]));
}

// Add AVS info
if ($module_params['param05'] == 'Y') {
    if (!empty($street_data)) {
        $data['request'][$txn_type]['avs_info'] = array(
            'avs_street_number' => $street_data[0],
            'avs_street_name' => substr($street_data[1], 0, 19-strlen($street_data[0])),
            'avs_zipcode' => $userinfo['b_zipcode']
        );
    }

    $data['request'][$txn_type]['cvd_info'] = array(
        'cvd_value' => $userinfo['card_cvv2'],
        'cvd_indicator' => '1'
    );
}

// Add Customer info
$data['request'][$txn_type]['cust_info'] = array(
    'email' => $userinfo['email'],
    'instructions' => '',
    'billing' => array(
        'first_name' => $userinfo['b_firstname'],
        'last_name' => $userinfo['b_lastname'],
        'company_name' => $userinfo['company'],
        'address' => $userinfo['b_address'].($userinfo['b_address_2'] ? $userinfo['b_address_2'] : ""),
        'city' => $userinfo['b_city'],
        'province' => $userinfo['b_state'],
        'postal_code' => $userinfo['b_zipcode'],
        'country' => $userinfo['b_country'],
        'phone_number' => $userinfo['phone'],
        'fax' => $userinfo['fax'],
        'tax1' => '',
        'tax2' => '',
        'tax3' => '',
        'shipping_cost' => price_format($cart['shipping_cost']),
    ),
    'shipping' => array(
        'first_name' => $userinfo['s_firstname'],
        'last_name' => $userinfo['s_lastname'],
        'company_name' => $userinfo['company'],
        'address' => $userinfo['s_address'].($userinfo['s_address_2'] ? $userinfo['s_address_2'] : ""),
        'city' => $userinfo['s_city'],
        'province' => $userinfo['s_state'],
        'postal_code' => $userinfo['s_zipcode'],
        'country' => $userinfo['s_country'],
        'phone_number' => $userinfo['phone'],
        'fax' => $userinfo['fax'],
        'tax1' => '',
        'tax2' => '',
        'tax3' => '',
        'shipping_cost' => price_format($cart['shipping_cost']),
    ),
);

$data['request'][$txn_type]['cust_info']['item'] = array();

foreach($products as $product) {
    $data['request'][$txn_type]['cust_info']['item'][] = array(
        'name' => $product['product'],
        'product_code' => $product['productcode'],
        'quantity' => $product['amount'],
        'extended_amount' => price_format($product['price'])
    );
}

if (isset($cart['giftcerts']) && is_array($cart['giftcerts']) && count($cart['giftcerts'])>0) {
    foreach ($cart['giftcerts'] as $gc) {
        $data['request'][$txn_type]['cust_info']['item'][] = array(
            'name' => "GIFT CERTIFICATE",
            'product_code' => $gc['gcid'],
            'quantity' => '1',
            'extended_amount' => price_format($product['amount'])
        );
    }
}

if ($trantype == 'completion' && $eselect_txnid) {
    unset($data['request'][$txn_type]);
    $txn_type = $trantype;
    $data['request'][$txn_type] = array(
        'order_id'        => $eselect_order_id,
        'comp_amount'   => price_format($order['order']['total']),
        'txn_number'    => $eselect_txnid,
        'crypt_type'    => $crypt_type,
    );
}

$xml = '<?xml version="1.0" encoding="iso-8859-1"?>'.trim(preg_replace("/>\s+</s", "><", func_hash2xml($data)));

if ($module_params['param03'] == 'CA')
    $url = "https://".(($module_params['testmode'] == 'Y') ? 'esqa.moneris.com' : 'www3.moneris.com').":443/gateway2/servlet/MpgRequest";
else
    $url = "https://".(($module_params['testmode'] == 'Y') ? 'esplusqa' : 'esplus').".moneris.com:443/gateway_us/servlet/MpgRequest";

list($a, $return) = func_https_request('POST', $url, array($xml), '', '', 'text/xml');

$complete = preg_match("/<complete>\s*true\s*<\/complete>/is", $return);

$message = false;
$responsecode = false;
$referencenum = false;
$authcode = false;

if (preg_match("/<message>(.+)<\/message>/is", $return, $match))
    $message = $match[1];

if (preg_match("/<responsecode>(.+)<\/responsecode>/is", $return, $match))
    $responsecode = $match[1];

if (preg_match("/<referencenum>(.+)<\/referencenum>/is", $return, $match))
    $referencenum = $match[1];

if (preg_match("/<authcode>(.+)<\/authcode>/is", $return, $match))
    $authcode = $match[1];

if (preg_match("/<avsresultcode>(.+)<\/avsresultcode>/is", $return, $match) && $match[1] != '0') {
    require_once $xcart_dir.'/include/cc_detect.php';

    if ((is_visa($userinfo['card_number']) || is_mc($userinfo['card_number'])) && isset($avs_visa_codes[$match[1]])) {
        $bill_output['avsmes'] = "AVS result: ".$avs_visa_codes[$match[1]];

    } elseif (is_dc($userinfo['card_number']) && isset($avs_discover_codes[$match[1]])) {
        $bill_output['avsmes'] = "AVS result: ".$avs_discover_codes[$match[1]];

    } else {
        $bill_output['avsmes'] = "AVS code: ".$match[1];
    }
}

if (preg_match("/<cvdresultcode>(.+)<\/cvdresultcode>/is", $return, $match) && $match[1] != 'null' && preg_match("/^([0129])([MNPSU])$/", trim($match[1]), $submatch)) {
    if (isset($cvd_indicator[$submatch[1]]) && isset($cvd_response_codes[$submatch[2]])) {
        $bill_output['cvvmes'] = "CVD indicator: ".$cvd_indicator[$submatch[1]]."; CVD response code: ".$cvd_response_codes[$submatch[2]];

    } else {
        $bill_output['cvvmes'] = "CVD code: ".$match[1];
    }
}

if ($complete && $responsecode < 50 && $responsecode !== false) {
    $bill_output['code'] = 1;

    if ($is_preauth && $txn_type != 'completion') {
        $bill_output['is_preauth'] = true;

        preg_match("/<transid>(.+)<\/transid>/is", $return, $match);
        $extra_order_data = array(
            'eselect_txnid'        => $match[1],
            'eselect_order_id'    => $data['request'][$txn_type]['order_id'],
            'capture_status'    => 'A',
        );
    }

} elseif ($message) {
    $bill_output['code'] = 2;
    $bill_output['billmes'] = "Declined: ".$message;

} else {
    $bill_output['code'] = 2;
    $bill_output['billmes'] = "Error: Internal error";
}

if ($responsecode)
    $bill_output['billmes'] .= " Response code: $responsecode;";

if ($referencenum)
    $bill_output['billmes'] .= " Reference number: $referencenum;";

if ($authcode)
    $bill_output['billmes'] .= " Auth code: $authcode;";

?>
