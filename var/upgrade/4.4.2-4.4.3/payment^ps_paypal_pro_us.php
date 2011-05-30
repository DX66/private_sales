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
 * PayPal Website Payments Pro (1.5 version for US only)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: ps_paypal_pro_us.php,v 1.56.2.6 2011/01/10 13:12:08 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

$pp_username = $module_params['param01'];
$pp_password = $module_params['param02'];
$pp_currency = func_paypal_get_currency($module_params);
$pp_cert_file = $xcart_dir.'/payment/certs/'.$module_params['param04'];
$pp_signature = $module_params['param05'];
$pp_prefix = preg_replace("/[ '\"]+/","",$module_params['param06']);

$res = func_query_first("SELECT protocol FROM $sql_tbl[payment_methods] WHERE paymentid='".$module_params['paymentid']."'");
$_location = ($res['protocol'] == 'https') ? $https_location : $http_location;
$notify_url = $_location."/payment/ps_paypal.php?notify_from=pro";

$pp_use_cert = ($module_params['param07'] == 'C');
$pp_signature_txt = $pp_use_cert ? '' : "<Signature>".$pp_signature."</Signature>";

$pp_subject = '';
if ($config['paypal_express_method'] != 'api' && isset($config['paypal_express_email']) && !empty($config['paypal_express_email']) && $config['paypal_solution'] == 'express') {
    $pp_subject = '<Subject>' . $config['paypal_express_email'] . '</Subject>';
}

if ($pp_test == 'N') {
    $pp_url = $module_params['param07'] == 'C' ? "https://api.paypal.com:443/2.0/" : "https://api-3t.paypal.com:443/2.0/";
    $pp_customer_url = "https://www.paypal.com";

} else {
    $pp_url = $module_params['param07'] == 'C' ? "https://api.sandbox.paypal.com:443/2.0/" : "https://api-aa.sandbox.paypal.com:443/2.0/";
    $pp_customer_url = "https://www.sandbox.paypal.com";
}

if ($REQUEST_METHOD == 'GET' && $mode == 'express') {
    // start express checkout
    x_session_register('paypal_token');

    $pp_return_url = $current_location.'/payment/ps_paypal_pro.php?mode=express_return';
    $pp_cancel_url = $xcart_catalogs['customer'] . '/cart.php?mode=express_cancel';

    x_session_register('paypal_begin_express');
    $paypal_begin_express = false;
    x_session_save('paypal_begin_express');

    x_session_register('paypal_payment_id');
    x_session_register('paypal_mode');

    $paypal_payment_id = $payment_id;
    $paypal_mode = 'express';

    if ($pp_subject) {
        $pp_username = '';
        $pp_password = '';
        $pp_use_cert = false;
        $pp_signature_txt = '';
        $pp_final_action = 'Sale';
    }

    if (!empty($do_return) && !empty($paypal_token)) {
        $str_token = "<Token>$paypal_token</Token>";
    }

    $pp_locale_code = in_array($all_languages[$shop_language]['contry_code'], $pp_locale_codes) ? $all_languages[$shop_language]['contry_code'] : 'US';

    $address = '';

    if (!empty($logged_userid)) {

        x_load('user');
        $userinfo = func_userinfo($logged_userid, 'C');

        if (!empty($userinfo)) {
            // Escape XML string
            x_load('xml');
            $userinfo = func_array_map('func_xml_escape',$userinfo);

            $ship_firstname = empty($userinfo['s_firstname']) ? $userinfo['firstname'] : $userinfo['s_firstname'];
            $ship_lastname = empty($userinfo['s_lastname']) ? $userinfo['lastname'] : $userinfo['s_lastname'];
            $state = ($userinfo['s_country'] == 'US' || $userinfo['s_country'] == 'CA' || $userinfo['s_state'] != "") ? $userinfo['s_state'] : 'Other';

            $areas = func_get_profile_areas('C');

            if ($areas['S'] || $areas['B']) {
                $address_details = <<<ADDR
                  <ShipToAddress>
                    <Name>$ship_firstname $ship_lastname</Name>
                    <Street1>$userinfo[s_address]</Street1>
                    <Street2>$userinfo[s_address_2]</Street2>
                    <CityName>$userinfo[s_city]</CityName>
                    <StateOrProvince>$state</StateOrProvince>
                    <PostalCode>$userinfo[s_zipcode]</PostalCode>
                    <Country>$userinfo[s_country]</Country>
                    <Phone>$userinfo[phone]</Phone>
                  </ShipToAddress>
ADDR;
                $address_flags = <<<ADDR
                    <AddressOverride>1</AddressOverride>
                    <ReqConfirmShipping>0</ReqConfirmShipping>
ADDR;
            } else {
                $address_details = '';
                $address_flags = "<NoShipping>1</NoShipping>";
            }
        }
    }

    // Get line items if possible bt:99384#523489
    $pp_paypal_totals = func_paypal_is_line_items_allowed($cart, $pp_total);
    if (!empty($pp_paypal_totals)) {
        $PaymentDetailsItems = func_paypal_get_payment_details_items_soap($cart['products']);

    } else {
        $PaymentDetailsItems = '';
        $pp_paypal_totals = array(
            'OrderTotal' => $pp_total,
            'ItemTotal' => $pp_total,
            'ShippingTotal' => 0,
            'TaxTotal' => 0,
            'HandlingTotal' => 0
        );
    }        

    // send SetExpressCheckoutRequest to PayPal
    $request = <<<EOT
<?xml version="1.0" encoding="$pp_charset"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
  <soap:Header>
    <RequesterCredentials xmlns="urn:ebay:api:PayPalAPI">
      <Credentials xmlns="urn:ebay:apis:eBLBaseComponents">
        <Username>$pp_username</Username>
        <ebl:Password xmlns:ebl="urn:ebay:apis:eBLBaseComponents">$pp_password</ebl:Password>
        $pp_signature_txt
        $pp_subject
      </Credentials>
    </RequesterCredentials>
  </soap:Header>
  <soap:Body>
    <SetExpressCheckoutReq xmlns="urn:ebay:api:PayPalAPI">
      <SetExpressCheckoutRequest>
        <Version xmlns="urn:ebay:apis:eBLBaseComponents">60.0</Version>
        <SetExpressCheckoutRequestDetails xmlns="urn:ebay:apis:eBLBaseComponents">
          <ReturnURL>$pp_return_url</ReturnURL>
          <CancelURL>$pp_cancel_url</CancelURL>
          <PaymentDetails>
            <OrderTotal currencyID="$pp_currency">$pp_paypal_totals[OrderTotal]</OrderTotal>
            <ItemTotal currencyID="$pp_currency">$pp_paypal_totals[ItemTotal]</ItemTotal>
            <ShippingTotal currencyID="$pp_currency">$pp_paypal_totals[ShippingTotal]</ShippingTotal>
            <TaxTotal currencyID="$pp_currency">$pp_paypal_totals[TaxTotal]</TaxTotal>
            <HandlingTotal currencyID="$pp_currency">$pp_paypal_totals[HandlingTotal]</HandlingTotal>
            $PaymentDetailsItems
            $address_details
          </PaymentDetails>
          $address_flags
          <PaymentAction>$pp_final_action</PaymentAction>
          $str_token
          <LocaleCode>$pp_locale_code</LocaleCode>
        </SetExpressCheckoutRequestDetails>
      </SetExpressCheckoutRequest>
    </SetExpressCheckoutReq>
  </soap:Body>
</soap:Envelope>
EOT;

    $result = func_paypal_request($request);

    // receive SetExpressCheckoutResponse
    if ($result['success'] && !empty($result['Token'])) {
        $paypal_token = $result['Token'];

        x_session_register('paypal_token_ttl');
        $paypal_token_ttl = XC_TIME;

        // move to the PayPal
        func_header_location($pp_customer_url . '/webscr?cmd=_express-checkout&token=' . $result['Token']);
    }

    $top_message = array(
        'type' => 'E',
        'content' => '[PayPal response] ' . $result['error']['ShortMessage'] . (empty($result['error']['LongMessage']) ? '' : ': ' . $result['error']['LongMessage'])
    );
    func_header_location($pp_cancel_url);

} elseif ($REQUEST_METHOD == 'GET' && $mode == 'express_return' && !empty($_GET['token'])) {

    // return from PayPal

    if ($pp_subject) {
        $pp_username = '';
        $pp_password = '';
        $pp_use_cert = false;
        $pp_signature_txt = '';
        $pp_final_action = 'Sale';
    }

    // send GetExpressCheckoutDetailsRequest
    $token = $_GET['token'];
    $request =<<<EOT
<?xml version="1.0" encoding="$pp_charset"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
  <soap:Header>
    <RequesterCredentials xmlns="urn:ebay:api:PayPalAPI">
      <Credentials xmlns="urn:ebay:apis:eBLBaseComponents">
        <Username>$pp_username</Username>
        <ebl:Password xmlns:ebl="urn:ebay:apis:eBLBaseComponents">$pp_password</ebl:Password>
        $pp_signature_txt
        $pp_subject
      </Credentials>
    </RequesterCredentials>
  </soap:Header>
  <soap:Body>
    <GetExpressCheckoutDetailsReq xmlns="urn:ebay:api:PayPalAPI">
      <GetExpressCheckoutDetailsRequest>
        <Version xmlns="urn:ebay:apis:eBLBaseComponents">60.0</Version>
        <Token>$token</Token>
      </GetExpressCheckoutDetailsRequest>
    </GetExpressCheckoutDetailsReq>
  </soap:Body>
</soap:Envelope>
EOT;
    $result = func_paypal_request($request);

    $state_err = 0;

    $address = array (
        'firstname' => $result['address']['FirstName'],
        'lastname'  => $result['address']['LastName'],
        'address'   => preg_replace('![\s\n\r]+!s', ' ', $result['address']['Street1'])."\n".preg_replace('![\s\n\r]+!s', ' ', $result['address']['Street2']),
        'city'      => $result['address']['CityName'],
        'state'     => func_paypal_detect_state($result['address']['Country'], $result['address']['StateOrProvince'], $state_err),
        'country'   => $result['address']['Country'],
        'zipcode'   => $result['address']['PostalCode'],
        'phone'     => $result['address']['ContactPhone']
    );

    if ($config['General']['use_counties'] == 'Y') {
        $default_county = func_default_county($address['state'], $address['country']);
        $address['county'] = empty($default_county) ? $result['address']['StateOrProvince'] : $default_county;
    }

    x_session_register('login');
    x_session_register('login_type');
    x_session_register('logged_userid');

    if (!empty($login) && $login_type == 'C') {

        $cart['used_s_address'] = $address;

        // Fill empty address book
        if (func_is_address_book_empty($logged_userid)) {
            foreach ($profile_values['address'] as $addr_type => $val) {
                $val['address'] = $val['address'] . "\n" . $val['address_2'];
                func_unset($val, 'address_2');
                $val['default_' . strtolower($a_type)] = 'Y';
                func_save_address($logged_userid, 0, $val);
            }
        }

    }
    elseif ($config['General']['enable_anonymous_checkout'] == 'Y') {

        x_load('user');

        // Fill-in anonymous customer profile

        $pp_anon_user = array (
            'title'         => '', // unknown
            'firstname'     => $result['FirstName'],
            'lastname'      => $result['LastName'],
            'email'         => $result['Payer'],
            'referer'       => @$RefererCookie,
            'address'       => array(
                'B' => array(
                    'firstname' => $result['FirstName'],
                    'lastname'  => $result['LastName'],
                    'phone'     => $result['ContactPhone']
                ),
                'S' => array(
                    'firstname' => $result['address']['FirstName'],
                    'lastname'  => $result['address']['LastName'],
                    'phone'     => $result['address']['ContactPhone']
                ),
            )
        );
        
        foreach ($address as $k => $v) {
            $pp_anon_user['address']['B'][$k] = 
            $pp_anon_user['address']['S'][$k] = $v;
        }

        // save anonymous customer info in session
        x_session_register('anonymous_userinfo', array());
        $anonymous_userinfo = $pp_anon_user;
        $anonymous_userinfo['usertype'] = empty($usertype) ? 'C' : $usertype;

    }
    else {
        // Display a warning message about expired session
        $top_message = array(
            'type' => 'E',
            'content' => func_get_langvar_by_name('txt_paypal_expired_session_warn')
        );
        func_header_location($xcart_catalogs['customer'] . '/login.php');
    }

    x_session_register('paypal_payment_id');
    x_session_register('paypal_express_details');
    $paypal_express_details = $result;

    switch ($state_err) {
        case 1:
            $top_message = array(
                'type' => 'W',
                'content' => func_get_langvar_by_name('lbl_paypal_wrong_country_note')
            );
            break;

        case 2:
            $top_message = array(
                'type' => 'W',
                'content' => func_get_langvar_by_name('lbl_paypal_wrong_state_note')
            );
    }

    func_header_location($xcart_catalogs['customer'].'/cart.php?paymentid='.$paypal_payment_id.'&mode=checkout');

} elseif ($REQUEST_METHOD == 'POST' && $_POST["action"] == 'place_order' && $pp_dp_allowed) {

    x_load('payment');
    // do DirectPayment
    $avs_codes = array (
        'A' => "Address Address only (no ZIP)",
        'B' => "International 'A'. Address only (no ZIP)",
        'C' => "International 'N'",
        'D' => "International 'X'. Address and Postal Code",
        'E' => "Not allowed for MOTO (Internet/Phone) transactions",
        'F' => "UK-specific X Address and Postal Code",
        'G' => "Global Unavailable",
        'I' => "International Unavailable",
        'N' => 'None',
        'P' => "Postal Code only (no Address)",
        'R' => 'Retry',
        'S' => "Service not Supported",
        'U' => 'Unavailable',
        'W' => "Nine-digit ZIP code (no Address)",
        'X' => "Exact match. Address and five-digit ZIP code",
        'Y' => "Address and five-digit ZIP",
        'Z' => "Five-digit ZIP code (no Address)"
    );

    $cvv_codes = array (
        'M' => 'Match',
        'N' => "No match",
        'P' => "Not Processed",
        'S' => "Service not Supported",
        'U' => 'Unavailable',
        'X' => "No response"
    );

    include_once $xcart_dir.'/include/cc_detect.php';

    $pp_cardtype = '';

    if (is_visa($userinfo['card_number']))
        $pp_cardtype = 'Visa';

    if (is_mc($userinfo['card_number']))
        $pp_cardtype = 'MasterCard';

    if (is_dc($userinfo['card_number']))
        $pp_cardtype = 'Discover';

    if (is_amex($userinfo['card_number']))
        $pp_cardtype = 'Amex';

    if (is_solo($userinfo['card_number']))
        $pp_cardtype = 'Solo';

    if (is_switch($userinfo['card_number']))
        $pp_cardtype = 'Switch';

    if (empty($pp_cardtype)) {
        $top_message = array(
            'content' => func_get_langvar_by_name('txt_paypal_us_wrong_cc_type'),
            'type' => 'E'
        );
        func_header_location($xcart_catalogs['customer']."/cart.php?mode=checkout&paymentid=".$paymentid);
    }

    $payer = array();
    foreach ($userinfo as $k => $v) {
        if (is_array($v)) {
            continue;
        }
        $payer[$k] = htmlspecialchars($v);
    }

    $payer['s_state'] = ($payer['s_country'] == 'US' || $payer['s_country'] == 'CA' || $payer['s_state'] != "") ? $payer['s_state'] : 'Other';
    $payer_state = ($payer['b_country'] == 'US' || $payer['b_country'] == 'CA' || $payer['b_state'] != "") ? $payer['b_state'] : 'Other';

    $payer_ipaddress = func_get_valid_ip($REMOTE_ADDR);

    db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($order_secureid)."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");
    $pp_ordr = $pp_prefix.join("-",$secure_oid);

    $pp_exp_month = (int)substr($userinfo['card_expire'],0,2);
    $pp_exp_year = (2000+substr($userinfo['card_expire'],2,2));

    $s_name = '';
    if (!empty($payer['s_firstname'])) {
        $s_name = $payer['s_firstname'];
    } elseif (!empty($payer['firstname'])) {
        $s_name = $payer['firstname'];
    }

    if (!empty($payer['s_lastname'])) {
        $s_name .= (empty($s_name) ? '' : " ").$payer['s_lastname'];
    } elseif (!empty($payer['lastname'])) {
        $s_name .= (empty($s_name) ? '' : " ").$payer['lastname'];
    }

    if (!empty($s_name)) {
        $s_name = substr($s_name, 0, 32);
    }

    if (empty($payer['b_firstname'])) {
        $payer['b_firstname'] = empty($payer['firstname']) ? "Unknown" : $payer['firstname'];
    }
    if (empty($payer['b_lastname'])) {
        $payer['b_lastname'] = empty($payer['lastname']) ? "Unknown" : $payer['lastname'];
    }

    $solo_details = '';
    if (in_array($pp_cardtype, array('Solo', 'Switch'))) {

        if ($pp_currency != 'GBP') {
            $top_message = array(
                'type' => 'E',
                'content' => func_get_langvar_by_name("txt_paypal_solomaestro_currency_note")
            );
            x_log_flag(
                'log_payment_processing_errors',
                'PAYMENTS',
                "Payment processing failure.\nLogin: $login\nIP: $REMOTE_ADDR\n----\n" .
                    func_get_langvar_by_name('txt_paypal_solomaestro_currency_log', array('transaction' => 'Website Payments Pro (Direct payment)', 'currency' => $pp_currency), false, true),
                true
            );
            func_header_location($xcart_catalogs['customer']."/cart.php?mode=checkout&paymentid=".$paymentid);
        }

        $pp_sexp_month = intval(substr($userinfo['card_valid_from'], 0, 2));
        $pp_sexp_year = 2000 + substr($userinfo['card_valid_from'], 2, 2);

        $solo_details = '
            <StartMonth>' . $pp_sexp_month . '</StartMonth>
            <StartYear>' . $pp_sexp_year . '</StartYear>
            <IssueNumber>' . substr($payer['card_issue_no'], 0, 2) . '</IssueNumber>
        ';
    }

    $ship_to_address = '';
    $areas = func_get_profile_areas(empty($login) ? 'H' : 'C');
    if ($areas['S']) {
        $ship_to_address = <<<EOT
            <ShipToAddress>
              <Name>$s_name</Name>
              <Street1>$payer[s_address]</Street1>
              <Street2>$payer[s_address_2]</Street2>
              <CityName>$payer[s_city]</CityName>
              <StateOrProvince>$payer[s_state]</StateOrProvince>
              <PostalCode>$payer[s_zipcode]</PostalCode>
              <Country>$payer[s_country]</Country>
            </ShipToAddress>
EOT;
    }

    $secure3dstring = '';

    if ($cmpi_result && $pp_cardtype != 'Solo') {
        $auth_status = $cmpi_result['PAResStatus'];
        $cavv = $cmpi_result['Cavv'];
        $eci = $cmpi_result['EciFlag'];
        $xid = $cmpi_result['Xid'];

        $secure3dstring = <<<EOT
          <ThreeDSecureRequest>
            <AuthStatus3ds>$auth_status</AuthStatus3ds>
            <MpiVendor3ds>Y</MpiVendor3ds>
            <Cavv>$cavv</Cavv>
            <Eci3ds>$eci</Eci3ds>
            <Xid>$xid</Xid>
          </ThreeDSecureRequest>
EOT;
    }

    $request=<<<EOT
<?xml version="1.0" encoding="$pp_charset"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
  <soap:Header>
    <RequesterCredentials xmlns="urn:ebay:api:PayPalAPI">
      <Credentials xmlns="urn:ebay:apis:eBLBaseComponents">
        <Username>$pp_username</Username>
        <ebl:Password xmlns:ebl="urn:ebay:apis:eBLBaseComponents">$pp_password</ebl:Password>
        $pp_signature_txt
      </Credentials>
    </RequesterCredentials>
  </soap:Header>
  <soap:Body>
    <DoDirectPaymentReq xmlns="urn:ebay:api:PayPalAPI">
      <DoDirectPaymentRequest>
        <Version xmlns="urn:ebay:apis:eBLBaseComponents">59.0</Version>
        <DoDirectPaymentRequestDetails xmlns="urn:ebay:apis:eBLBaseComponents">
          <PaymentAction>$pp_final_action</PaymentAction>
          <PaymentDetails>
            <OrderTotal currencyID="$pp_currency">$pp_total</OrderTotal>
            <ButtonSource>X-cart_shoppingcart_DP_US</ButtonSource>
            <NotifyURL>$notify_url</NotifyURL>
$ship_to_address
            <InvoiceID>$pp_ordr</InvoiceID>
            <Custom>$order_secureid</Custom>
          </PaymentDetails>
          <CreditCard>
            <CreditCardType>$pp_cardtype</CreditCardType>
            <CreditCardNumber>$payer[card_number]</CreditCardNumber>
            <ExpMonth>$pp_exp_month</ExpMonth>
            <ExpYear>$pp_exp_year</ExpYear>
            <CardOwner>
              <PayerStatus>verified</PayerStatus>
              <Payer>$payer[email]</Payer>
              <PayerName>
                <FirstName>$payer[b_firstname]</FirstName>
                <LastName>$payer[b_lastname]</LastName>
              </PayerName>
              <PayerCountry>$payer[b_country]</PayerCountry>
              <Address>
                <Street1>$payer[b_address]</Street1>
                <Street2>$payer[b_address_2]</Street2>
                <CityName>$payer[b_city]</CityName>
                <StateOrProvince>$payer_state</StateOrProvince>
                <Country>$payer[b_country]</Country>
                <PostalCode>$payer[b_zipcode]</PostalCode>
              </Address>
            </CardOwner>
            <CVV2>$payer[card_cvv2]</CVV2>
            $solo_details
            $secure3dstring
          </CreditCard>
          <IPAddress>$payer_ipaddress</IPAddress>
          <ReturnFMFDetails>1</ReturnFMFDetails>
        </DoDirectPaymentRequestDetails>
      </DoDirectPaymentRequest>
    </DoDirectPaymentReq>
  </soap:Body>
</soap:Envelope>
EOT;

    $result = func_paypal_request($request);

    $bill_output['code'] = 2;

    if ($result['success']) {
        if (isset($result['fmf']) && $result['fmf']) {
            $bill_output['code'] = 1;
            $bill_message = 'Accepted';

        } else {
            $bill_output['code'] = 3;
            $bill_message = 'Pending';
        }

    } else {
        $bill_message = 'Declined';
    }

    $additional_fields = array();
    foreach (array('TransactionID') as $add_field) {
        if (isset($result[$add_field]) && strlen($result[$add_field]) > 0)
            $additional_fields[] = ' '.$add_field.': '.$result[$add_field];
    }

    if (!empty($additional_fields))
        $bill_message .= ' ('.implode(', ', $additional_fields).')';

    if (!empty($result['error'])) {
        $bill_message .= sprintf (
            " Error: %s (Code: %s, Severity: %s)",
            $result['error']['LongMessage'],
            $result['error']['ErrorCode'],
            $result['error']['Severity']);
    }

    $bill_output['billmes'] = $bill_message;

    if (isset($result['AVSCode'])) {
        $bill_output['avsmes'] = (empty($avs_codes[$result['AVSCode']]) ? "Code: ".$result['AVSCode'] : $avs_codes[$result['AVSCode']]);
    }

    if (isset($result['CVV2Code'])) {
        $bill_output['cvvmes'] = (empty($cvv_codes[$result['CVV2Code']]) ? "Code: ".$result['CVV2Code'] : $cvv_codes[$result['CVV2Code']]);
    }

    if ($pp_final_action != 'Sale')
        $bill_output['is_preauth'] = true;

    $extra_order_data = array(
        'paypal_type' => 'USDP',
        'paypal_txnid' => $result['TransactionID'],
        'capture_status' => $pp_final_action != 'Sale' ? 'A' : '',
        'filters' => $result['filters']
    );

    if (isset($result['fmf']) && $result['fmf']) {
        $extra_order_data['fmf'] = 1;
    }

} elseif ($REQUEST_METHOD == 'POST' && $_POST["action"] == 'place_order') {

    // finish ExpressCheckout

    if ($pp_subject) {
        $pp_username = '';
        $pp_password = '';
        $pp_use_cert = false;
        $pp_signature_txt = '';
        $pp_final_action = 'Sale';
    }

    db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($order_secureid)."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");
    $pp_ordr = $pp_prefix.join("-",$secure_oid);

    x_session_register('paypal_express_details');

    $request =<<<EOT
<?xml version="1.0" encoding="$pp_charset"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
  <soap:Header>
    <RequesterCredentials xmlns="urn:ebay:api:PayPalAPI">
      <Credentials xmlns="urn:ebay:apis:eBLBaseComponents">
        <Username>$pp_username</Username>
        <ebl:Password xmlns:ebl="urn:ebay:apis:eBLBaseComponents">$pp_password</ebl:Password>
        $pp_signature_txt
        $pp_subject
      </Credentials>
    </RequesterCredentials>
  </soap:Header>
  <soap:Body>
    <DoExpressCheckoutPaymentReq xmlns="urn:ebay:api:PayPalAPI">
      <DoExpressCheckoutPaymentRequest>
        <Version xmlns="urn:ebay:apis:eBLBaseComponents">60.0</Version>
        <DoExpressCheckoutPaymentRequestDetails xmlns="urn:ebay:apis:eBLBaseComponents">
          <PaymentAction>$pp_final_action</PaymentAction>
          <Token>$paypal_express_details[Token]</Token>
          <PayerID>$paypal_express_details[PayerID]</PayerID>
          <PaymentDetails>
            <OrderTotal currencyID="$pp_currency">$pp_total</OrderTotal>
            <ItemTotal currencyID="$pp_currency">$pp_total</ItemTotal>
            <ShippingTotal currencyID="$pp_currency">0</ShippingTotal>
            <TaxTotal currencyID="$pp_currency">0</TaxTotal>
            <HandlingTotal currencyID="$pp_currency">0</HandlingTotal>
            $address_details
            <ButtonSource>X-cart_shoppingcart_EC_US</ButtonSource>
            <NotifyURL>$notify_url</NotifyURL>
            <InvoiceID>$pp_ordr</InvoiceID>
            <Custom>$order_secureid</Custom>
          </PaymentDetails>
          <ReturnFMFDetails>1</ReturnFMFDetails>
        </DoExpressCheckoutPaymentRequestDetails>
      </DoExpressCheckoutPaymentRequest>
    </DoExpressCheckoutPaymentReq>
  </soap:Body>
</soap:Envelope>
EOT;

    $result = func_paypal_request($request);

    $bill_output['code'] = 2;

    if (!strcasecmp($result['PaymentStatus'],'Completed') || !strcasecmp($result['PaymentStatus'],'Processed')) {
        $bill_output['code'] = 1;
        $bill_message = 'Accepted';

    } elseif (!strcasecmp($result['PaymentStatus'], 'Pending')) {
        $bill_output['code'] = 3;
        if (isset($result['fmf']) && $result['fmf']) {
            $bill_message = 'Pending';
        } else {
            $bill_message = 'Queued';
        }

    } else {
        $bill_message = 'Declined';
    }

    $bill_message .= " Status: ".$result['PaymentStatus'];
    if (!empty($result['PendingReason']) && strtolower(trim($result['PendingReason'])) != 'none')
        $bill_message .= ' Reason: '.$result['PendingReason'];

    $additional_fields = array();
    foreach (array('TransactionID','TransactionType','PaymentType','GrossAmount','FeeAmount','SettleAmount','TaxAmount','ExchangeRate') as $add_field) {
        if (isset($result[$add_field]) && strlen($result[$add_field]) > 0)
            $additional_fields[] = ' '.$add_field.': '.$result[$add_field];
    }

    if (!empty($additional_fields))
        $bill_message .= ' ('.implode(', ', $additional_fields).')';

    if (!empty($result['error'])) {
        $bill_message .= sprintf (
            " Error: %s (Code: %s, Severity: %s)",
            $result['error']['LongMessage'],
            $result['error']['ErrorCode'],
            $result['error']['Severity']);
    }

    $bill_output['billmes'] = $bill_message;
    if ($pp_final_action != 'Sale')
        $bill_output['is_preauth'] = true;

    $extra_order_data = array(
        'paypal_type' => 'USEC',
        'paypal_txnid' => $result['TransactionID'],
        'capture_status' => $pp_final_action != 'Sale' ? 'A' : '',
        'filters' => $result['filters']
    );

    if (isset($result['fmf']) && $result['fmf']) {
        $extra_order_data['fmf'] = 1;
    }

    require $xcart_dir.'/payment/payment_ccend.php';
}
?>
