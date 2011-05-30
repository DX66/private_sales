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
 * FedEx shipping library
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: mod_FEDEX.php,v 1.88.2.4 2011/01/28 12:21:12 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('cart','http','xml');

function func_shipper_FEDEX($items, $userinfo, $orig_address, $debug, $cart)
{
    global $config, $sql_tbl;
    global $products;
    global $active_modules;
    global $allowed_shipping_methods, $intershipper_rates;

    if (empty($config['Shipping']['FEDEX_account_number']) || empty($config['FEDEX_meter_number']))
        return;

    $FEDEX_FOUND = false;
    if (is_array($allowed_shipping_methods)) {
        foreach ($allowed_shipping_methods as $key=>$value) {
            if ($value['code'] == 'FDX') {
                $FEDEX_FOUND = true;
                break;
            }
        }
    }

    if (!$FEDEX_FOUND)
        return;

    $f_gr = ($userinfo['s_country'] == $orig_address['country']) ? '43' : '78';

    $fedex_services = array(
        'EUROPE_FIRST_INTERNATIONAL_PRIORITY'     => '138',
        'FEDEX_1_DAY_FREIGHT'                     => '133',
        'FEDEX_2_DAY'                             => '41',
        'FEDEX_2_DAY_FREIGHT'                     => '134',
        'FEDEX_3_DAY_FREIGHT'                     => '135',
        'FEDEX_EXPRESS_SAVER'                     => '42',
        'FEDEX_GROUND'                             => $f_gr,
        'FIRST_OVERNIGHT'                         => '47',
        'GROUND_HOME_DELIVERY'                     => '44',
        'INTERNATIONAL_ECONOMY'                 => '49',
        'INTERNATIONAL_ECONOMY_FREIGHT'         => '137',
        'INTERNATIONAL_FIRST'                     => '96',
        'INTERNATIONAL_PRIORITY'                 => '48',
        'INTERNATIONAL_PRIORITY_FREIGHT'         => '136',
        'PRIORITY_OVERNIGHT'                     => '45',
        'SMART_POST'                             => '175',
        'STANDARD_OVERNIGHT'                     => '46',
        'FEDEX_FREIGHT'                         => '176',
        'FEDEX_NATIONAL_FREIGHT'                 => '177',
    );

    // Default FedEx shipping options (if it wasn't defined yet by admin)

    $fedex_options = array (
        'carrier_codes' => 'FDXE|FDXG|FXSP',
        'dropoff_type'     => 'REGULAR_PICKUP',
        'packaging'     => 'FEDEX_ENVELOPE',
        'list_rate'     => 'false',
        'ship_date'     => 0,
        'package_count' => 1,
        'currency_code' => 'USD',
        'param01'         => 'Y',
        'param02'         => 'Y'
    );

    // FedEx host
    $fedex_host = ($config['Shipping']['FEDEX_test_server'] == 'Y' ? 'gatewaybeta.fedex.com:443/web-services' : 'gateway.fedex.com:443/web-services');

    // Get stored FedEx options.
    $params = func_query_first ("SELECT param00 FROM $sql_tbl[shipping_options] WHERE carrier='FDX'");

    $fedex_options_saved = unserialize($params['param00']);
    if (is_array($fedex_options_saved)) {
        $fedex_options = func_array_merge($fedex_options, $fedex_options_saved);

        if (!empty($fedex_options['carrier_codes'])) {
            $fedex_options['carrier_codes'] = explode('|', $fedex_options['carrier_codes']);
        } else {
            $fedex_options['carrier_codes'] = array();
        }
    }

    $specified_dims = array();
    foreach (array('length' => 'dim_length', 'width' => 'dim_width', 'height' => 'dim_height') as $k => $o) {
        $dim = doubleval($fedex_options[$o]);
        if ($dim > 0) {
            $specified_dims[$k] = $dim;
        }
    }

    // Get the declared value of package
    if ($debug=="Y") {
        $decl_value = '1.00';
    }
    else {
        $is_admin = defined('AREA_TYPE') && (AREA_TYPE=='A' || AREA_TYPE=='P' && !empty($active_modules['Simple_Mode']));

        if ($is_admin && !empty($active_modules['Advanced_Order_Management']) && x_session_is_registered('cart_tmp')) {
            global $cart_tmp;

            if (!isset($cart_tmp) && is_array($cart_tmp))
                $cart = $cart_tmp;
        }

        $cart2 = func_calculate($cart, $products, $userinfo['id'], $userinfo['usertype']);
        $decl_value = $cart2['subtotal'];
    }

    $fedex_options['declared_value'] = $decl_value;

    $fedex_options['dim_units'] = "IN";

    $carrier_codes = $fedex_options['carrier_codes'];

    $package_limits = func_get_package_limits_FEDEX($fedex_options);

    if (!empty($carrier_codes) && is_array($carrier_codes)) {

        if ($debug == 'Y')
            print "<h1>FedEx Debug Information</h1>";

        $_fedex_rates = $fedex_rates = array();

        $pack_limits = $package_limits[$fedex_options['packaging']];

        if (!isset($pack_limits['price']))
            $pack_limits['price'] = 50000;

        $packages = func_get_packages($items, $pack_limits, ($fedex_options['param01'] == "Y") ? 100 : 1);

        if (!empty($packages) && is_array($packages)) {

            if($fedex_options['param02']=="Y")
                $pack = func_array_merge($pack, $specified_dims);

            $_fedex_rates[$_carrier_code][$num] = $_fedex_rates_tmp = array();

            $xml_query = func_fedex_prepare_xml_query($packages, $fedex_options, $userinfo, $pack, $orig_address);

            $md5_request = md5($xml_query);

            if ($debug != 'Y' && func_is_shipping_result_in_cache($md5_request)) {

                // Get shipping rates from the cache

                $_fedex_rates_tmp = func_get_shipping_result_from_cache($md5_request);
            }

            if (empty($_fedex_rates_tmp)) {

            // Get shipping rates from FedEx server

                $data = preg_split("/(\r\n|\r|\n)/",$xml_query, -1, PREG_SPLIT_NO_EMPTY);
                $host = "https://".$fedex_host;
                list($header, $result) = func_https_request('POST', $host, $data,'','','text/xml');

                if (defined('FEDEX_DEBUG'))
                    x_log_add('fedex_rates', $xml_query . "\n\n" . $header . "\n\n" . $result);

                // Parse XML reply
                $parse_error = false;
                $options = array(
                    'XML_OPTION_CASE_FOLDING' => 1,
                    'XML_OPTION_TARGET_ENCODING' => 'ISO-8859-1'
                );

                $parsed = func_xml_parse($result, $parse_error, $options);

                $error = array();

                if (empty($parsed)) {
                // Error of parsing XML reply from FedEx
                    x_log_flag('log_shipping_errors', 'SHIPPING', "FedEx module (rates): Received data could not be parsed correctly.", true);
                    $error['msg'] = "FedEx module (rates): Received data could not be parsed correctly.";
                    return false;
                }

                $type = "SOAPENV:ENVELOPE/SOAPENV:BODY";

                $error['code'] = func_array_path($parsed, $type."/SOAPENV:FAULT/FAULTCODE/0/#");
                if (!empty($error['code'])) {
                // FedEx returned an error
                    $error['msg'] = func_array_path($parsed, $type."/SOAPENV:FAULT/FAULTSTRING/0/#");
                    x_log_flag('log_shipping_errors', 'SHIPPING', "FedEx module error: [{$error['code']}] {$error['msg']}", true);
                    $intershipper_error = $error['msg'];
                    $shipping_calc_service = 'FedEx';
                }
                else {
                // FedEx returned a valid reply, get the rates
                    $entries = func_array_path($parsed, $type."/V7:RATEREPLY/V7:RATEREPLYDETAILS");

                    if (is_array($entries)) {
                        foreach ($entries as $k=>$entry) {
                            $service_type = func_array_path($parsed, $type."/V7:RATEREPLY/V7:RATEREPLYDETAILS/$k/V7:SERVICETYPE/0/#");
                            $estimated_rate = func_array_path($parsed, $type."/V7:RATEREPLY/V7:RATEREPLYDETAILS/$k/V7:RATEDSHIPMENTDETAILS/V7:SHIPMENTRATEDETAIL/V7:TOTALNETCHARGE/V7:AMOUNT/0/#");

                            $variable_handling_charge = func_array_path($parsed, $type."/V7:RATEREPLY/V7:RATEREPLYDETAILS/$k/V7:RATEDSHIPMENTDETAILS/V7:SHIPMENTRATEDETAIL/V7:TOTALVARIABLEHANDLINGCHARGES/V7:VARIABLEHANDLINGCHARGE/V7:AMOUNT/0/#");
                            if (doubleval($variable_handling_charge) > 0)
                                $estimated_rate += $variable_handling_charge;

                            foreach ($allowed_shipping_methods as $key=>$value) {
                                if ($value['code'] == 'FDX' && $value['subcode'] == $fedex_services[$service_type])
                                    $_fedex_rates_tmp[] = array('methodid'=>$value['subcode'], 'rate'=>$estimated_rate, 'shipping_time' => $estimated_time);
                            }
                        }
                    }
                }

                if ($debug == 'Y') {
                // Display a debug information (on testing real-time shipping page)

                    if ($xml_query) {
                        $display_query = preg_replace("|<AccountNumber>.+</AccountNumber>|i","<AccountNumber>xxx</AccountNumber>",$xml_query);
                        $display_query = preg_replace("|<MeterNumber>.+</MeterNumber>|i","<MeterNumber>xxx</MeterNumber>",$display_query);

                        $display_result = preg_replace("|><|", ">\n<", $result);

                        print "<h2>FedEx Request</h2>";
                        print "<pre>".htmlspecialchars($display_query)."</pre>";
                        print "<h2>FedEx Response</h2>";
                        print "<pre>".htmlspecialchars($display_result)."</pre>";
                    }
                    else {
                        print "It seems, you have forgotten to fill in a FedEx account information, or destination information (City, State, Country or ZipCode). Please check it, and try again.";
                    }
                }
            } // endif (empty($_fedex_rates_tmp))

            if (!empty($_fedex_rates_tmp)) {
                $_fedex_rates[$_carrier_code][$num] = func_array_merge($_fedex_rates[$_carrier_code][$num], $_fedex_rates_tmp);

                // Save calculated rates to the cache
                if ($debug != 'Y')
                    func_save_shipping_result_to_cache($md5_request, $_fedex_rates_tmp);
            }
        } // endif

        if (!empty($_fedex_rates[$_carrier_code]))
        $fedex_rates = func_array_merge($fedex_rates, func_intersect_rates($_fedex_rates[$_carrier_code]));
    }

    unset($_fedex_rates);

    if (!empty($fedex_rates)) {
        $tmp = array();
        foreach ($fedex_rates as $fedex_rate) {
            if (!in_array($fedex_rate['methodid'], $tmp)) {
                $tmp[] = $fedex_rate['methodid'];
                $intershipper_rates[] = $fedex_rate;
            }
        }
    }
}

/**
 * This function prepares the XML query
 */
function func_fedex_prepare_xml_query($packages, $fedex_options, $userinfo, $pack, $orig_address)
{
    global $config;

    $fedex_weight = func_units_convert(func_weight_in_grams($pack['weight']), "g", "lbs", 1);
    if ($fedex_weight < 1)
        $fedex_weight = 1;

    $_time = XC_TIME + $config['Appearance']['timezone_offset'] + intval($fedex_options['ship_date'])*24*3600;
    $fedex_options['ship_date_ready'] = date("Y-m-d", $_time)."T".date("H:i:s", $_time);

    $fedex_options['account_number'] = $config['Shipping']['FEDEX_account_number'];
    $fedex_options['meter_number'] = $config['FEDEX_meter_number'];
    $fedex_options['key'] = $config['Shipping']['FEDEX_key'];
    $fedex_options['password'] = $config['Shipping']['FEDEX_password'];

    $fedex_options['original_country_code'] = $orig_address["country"];
    if (in_array($fedex_options['original_country_code'], array('US', 'CA'))) {
        $fedex_options['original_postal_code'] = preg_replace("/[^A-Za-z0-9]/", "", $orig_address["zipcode"]);
        $fedex_options['original_state_code'] = $orig_address["state"];
    }
    else {
        $fedex_options['original_postal_code'] = preg_replace("/[^A-Za-z0-9]/", "", $orig_address["zipcode"]);
        $fedex_options['original_state_code'] = '';
    }

    $fedex_options['destination_country_code'] = $userinfo["s_country"];
    $fedex_options['destination_postal_code'] = preg_replace("/[^A-Za-z0-9]/", "", $userinfo["s_zipcode"]);

    if (in_array($fedex_options['destination_country_code'], array('US', 'CA'))) {
        $fedex_options['destination_state_code'] = $userinfo["s_state"];
    }

    // Carrier codes

    $carriers_xml = '';
    foreach ($fedex_options['carrier_codes'] as $carrier) {
        $carriers_xml .= <<<OUT
    <q0:CarrierCodes>{$carrier}</q0:CarrierCodes>
OUT;
    }

    // Special services

    $special_services_types = $special_services = array(
        'package'     => array(),
        'shipment'     => array()
    );

    if (!empty($fedex_options['cod_value']) && doubleval($fedex_options['cod_value']) > 0) {
        $special_services['shipment'][] = <<<OUT
            <q0:CodDetail>
                <q0:CollectionType>{$fedex_options['cod_type']}</q0:CollectionType>
            </q0:CodDetail>
OUT;
            $special_services['shipment'][] = <<<OUT
        <q0:CodCollectionAmount>
            <q0:Currency>{$fedex_options['currency_code']}</q0:Currency>
            <q0:Amount>{$fedex_options['cod_value']}</q0:Amount>
        </q0:CodCollectionAmount>
OUT;
            $special_services_types['shipment'][] = 'COD';
    }

    if ($fedex_options['hold_at_location'] == 'Y') {
        $special_services_types['shipment'][] = 'HOLD_AT_LOCATION';
        $special_services['shipment'][] = "<q0:HoldAtLocationDetail><q0:PhoneNumber>$userinfo[phone]</q0:PhoneNumber></q0:HoldAtLocationDetail>";
    }

    if (!empty($fedex_options['dg_accessibility'])) {
        $special_services['package'][] = <<<OUT
        <q0:DangerousGoodsDetail>
            <q0:Accessibility>{$fedex_options['dg_accessibility']}</q0:Accessibility>
        </q0:DangerousGoodsDetail>
OUT;
        $special_services_types['package'][] = 'DANGEROUS_GOODS';
    }

    if ($fedex_options['dry_ice'] == 'Y') {
        $special_services['package'][] = <<<OUT
        <q0:DryIceWeight>
            <q0:Units>LB</q0:Units>
            <q0:Value>{$fedex_weight}</q0:Value>
        </q0:DryIceWeight>
OUT;
        $special_services_types['package'][] = 'DRY_ICE';
    }

    if ($fedex_options['inside_pickup'] == 'Y')
        $special_services_types['shipment'][] = 'INSIDE_PICKUP';

    if ($fedex_options['inside_delivery'] == 'Y')
        $special_services_types['shipment'][] = 'INSIDE_DELIVERY';

    if ($fedex_options['saturday_pickup'] == 'Y')
        $special_services_types['shipment'][] = 'SATURDAY_PICKUP';

    if ($fedex_options['saturday_delivery'] == 'Y')
        $special_services_types['shipment'][] = 'SATURDAY_DELIVERY';

    if ($fedex_options['nonstandard_container'] == "Y")
        $special_services_types['package'][] = 'NON_STANDARD_CONTAINER';

    if (!empty($fedex_options['signature']))
        $special_services['package'][] = <<<OUT
        <q0:SignatureOptionDetail>
            <q0:OptionType>{$fedex_options['signature']}</q0:OptionType>
        </q0:SignatureOptionDetail>
OUT;

    foreach ($special_services_types as $k => $ss_types) {
        if (!empty($ss_types)) {
            foreach ($ss_types as $key => $ss_type) {
                $special_services_types[$k][$key] = "<q0:SpecialServiceTypes>".$ss_type."</q0:SpecialServiceTypes>";
            }
        }
        $special_services[$k] = func_array_merge($special_services_types[$k], $special_services[$k]);
    }

    foreach ($special_services as $k => $ss) {
        if (!empty($ss)) {
            $special_services_xml[$k] = '';
            foreach ($ss as $_service)
                $special_services_xml[$k] .= "\t\t".$_service."\n";
            $special_services_xml[$k] = "<q0:SpecialServicesRequested>".$special_services_xml[$k]."</q0:SpecialServicesRequested>";
        }
        else
            $special_services_xml[$k] = '';
    }

    // Packages query

    $package_count = count($packages);

    $i = 1;
    $items_xml = '';

    foreach ($packages as $pack) {
        $dimensions_xml = func_fedex_prepare_dimensions_xml($pack, $fedex_options);

        // Declared value
        $declared_value_xml = '';

        if (!empty($pack['price']) && doubleval($pack['price']) > 0) {
            $declared_value_xml = <<<OUT
            <q0:InsuredValue>
                <q0:Currency>{$fedex_options['currency_code']}</q0:Currency>
                <q0:Amount>{$pack['price']}</q0:Amount>
            </q0:InsuredValue>
OUT;
        }

        $pack[weight] = func_units_convert(func_weight_in_grams($pack[weight]), "g", "lbs", 1);

        $items_xml .= <<<EOT
        <q0:RequestedPackageLineItems>
            <q0:SequenceNumber>{$i}</q0:SequenceNumber>
            {$declared_value_xml}
            <q0:Weight>
                <q0:Units>LB</q0:Units>
                <q0:Value>{$pack[weight]}</q0:Value>
            </q0:Weight>
            {$dimensions_xml}
            {$special_services_xml['package']}
        </q0:RequestedPackageLineItems>
EOT;
        $i++;
    }

    $residential = ($fedex_options['residential_delivery'] == 'Y') ? "<q0:Residential>true</q0:Residential>" : "";

    // Handling charges

    if (!empty($fedex_options['handling_charges_amount']) && doubleval($fedex_options['handling_charges_amount']) > 0) {
        $_handling_type = ($fedex_options['handling_charges_type'] == "FIXED_AMOUNT") ? "<q0:FixedValue><q0:Currency>$fedex_options[currency_code]</q0:Currency><q0:Amount>$fedex_options[handling_charges_amount]</q0:Amount></q0:FixedValue>" : "<q0:PercentValue>$fedex_options[handling_charges_amount]</q0:PercentValue>";

        $handling_charges_xml = <<<OUT
    <q0:VariableHandlingChargeDetail>
        <q0:VariableHandlingChargeType>{$fedex_options['handling_charges_type']}</q0:VariableHandlingChargeType>
        $_handling_type
    </q0:VariableHandlingChargeDetail>
OUT;
    }
    else
        $handling_charges_xml = '';

    // Prepare the XML request

    $xml_query = <<<OUT
<?xml version="1.0" encoding="UTF-8" ?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:q0="http://fedex.com/ws/rate/v7" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
<soapenv:Body>
<q0:RateRequest>
    <q0:WebAuthenticationDetail>
        <q0:UserCredential>
            <q0:Key>{$fedex_options['key']}</q0:Key>
            <q0:Password>{$fedex_options['password']}</q0:Password>
        </q0:UserCredential>
    </q0:WebAuthenticationDetail>

    <q0:ClientDetail>
        <q0:AccountNumber>{$fedex_options['account_number']}</q0:AccountNumber>
        <q0:MeterNumber>{$fedex_options['meter_number']}</q0:MeterNumber>
    </q0:ClientDetail>

    <q0:TransactionDetail>
        <q0:CustomerTransactionId>Basic Rate</q0:CustomerTransactionId>
    </q0:TransactionDetail>

    <q0:Version>
        <q0:ServiceId>crs</q0:ServiceId>
        <q0:Major>7</q0:Major>
        <q0:Intermediate>0</q0:Intermediate>
        <q0:Minor>0</q0:Minor>
    </q0:Version>

    {$carriers_xml}

    <q0:RequestedShipment>
        <q0:ShipTimestamp>{$fedex_options['ship_date_ready']}</q0:ShipTimestamp>
        <q0:DropoffType>{$fedex_options['dropoff_type']}</q0:DropoffType>
        <q0:PackagingType>{$fedex_options['packaging']}</q0:PackagingType>

        <q0:Shipper>
            <q0:Address>
                <q0:StateOrProvinceCode>{$fedex_options['original_state_code']}</q0:StateOrProvinceCode>
                <q0:PostalCode>{$fedex_options['original_postal_code']}</q0:PostalCode>
                <q0:CountryCode>{$fedex_options['original_country_code']}</q0:CountryCode>
            </q0:Address>
        </q0:Shipper>

        <q0:Recipient>
            <q0:Address>
                <q0:StateOrProvinceCode>{$fedex_options['destination_state_code']}</q0:StateOrProvinceCode>
                <q0:PostalCode>{$fedex_options['destination_postal_code']}</q0:PostalCode>
                <q0:CountryCode>{$fedex_options['destination_country_code']}</q0:CountryCode>
                {$residential}
            </q0:Address>
        </q0:Recipient>

        <q0:ShippingChargesPayment>
            <q0:PaymentType>SENDER</q0:PaymentType>
            <q0:Payor>
                <q0:AccountNumber>{$fedex_options['account_number']}</q0:AccountNumber>
                <q0:CountryCode>{$fedex_options['original_country_code']}</q0:CountryCode>
            </q0:Payor>
        </q0:ShippingChargesPayment>

        {$special_services_xml['shipment']}

        {$handling_charges_xml}

        <q0:RateRequestTypes>ACCOUNT</q0:RateRequestTypes>
        <q0:PackageCount>{$package_count}</q0:PackageCount>
        <q0:PackageDetail>INDIVIDUAL_PACKAGES</q0:PackageDetail>

        {$items_xml}

    </q0:RequestedShipment>
</q0:RateRequest>
</soapenv:Body>
</soapenv:Envelope>
OUT;

    return $xml_query;
}

/**
 * Return package limits for FedEx
 */
function func_get_package_limits_FEDEX($fedex_options)
{

    // Default limits (in pounds and inches)

    $limits = array(
            'YOUR_PACKAGING'     => array('weight' => 150, 'girth' => 165),
            'FEDEX_ENVELOPE'     => array('weight' => 1.1, 'price' => 100),
            'FEDEX_PAK'         => array('weight' => 20),
            'FEDEX_BOX'         => array('weight' => 20),
            'FEDEX_TUBE'         => array('weight' => 20),
            'FEDEX_10KG_BOX'     => array('weight' => 22),
            'FEDEX_25KG_BOX'     => array('weight' => 55)
    );

    // Convert default limits to store's units of weight and measure

    foreach($limits as $k1 => $v1)
        $limits[$k1] = func_correct_dimensions($v1);

    // User-defined limens (in store's units of weight and measure)

    $max_weight = doubleval($fedex_options['max_weight']);
    $max_length = doubleval($fedex_options['dim_length']);
    $max_width = doubleval($fedex_options['dim_width']);
    $max_height = doubleval($fedex_options['dim_height']);

    // Merge user-defined limits and default limits

    foreach($limits as $k1 => $v1) {
        $dims_specified = true;

        foreach(array('weight', 'length', 'width', 'height') as $key) {
            $max_key = "max_$key";
            $user_limit = doubleval($$max_key);
            $default_limit = doubleval($v1[$key]);
            if($user_limit > 0) {
                $limits[$k1][$key] = ($default_limit > 0) ? min($user_limit, $default_limit) : $user_limit;
            }
            if($key!="weight") $dims_specified &= ($user_limit > 0 && $user_limit == $limits[$k1][$key]);
        }

        if($dims_specified) unset($limits[$k1]['girth']);
    }

    return $limits;
}

/**
 * Check if FedEx allows box
 */
function func_check_limits_FEDEX($box)
{
    global $sql_tbl;

    $params = unserialize(func_query_first_cell("SELECT param00 FROM $sql_tbl[shipping_options] WHERE carrier='FDX'"));
    $package_limits = func_get_package_limits_FEDEX($params);
    $avail = false;
    $box['weight'] = (isset($box['weight'])) ? $box['weight'] : 0;

    foreach ($package_limits as $pack_limit) {
        $avail = $avail || (func_check_box_dimensions($box, $pack_limit) && $pack_limit['weight'] > $box['weight']);
    }

    return $avail;
}

/**
 * Prepare dimensions xml query
 */
function func_fedex_prepare_dimensions_xml($pack, $fedex_options)
{

    if ($fedex_options['packaging'] == 'YOUR_PACKAGING') {
        $dims = array($pack['length'], $pack['width'], $pack['height']);

        foreach($dims as $k=>$v)
            $dims[$k] = intval(func_units_convert(func_dim_in_centimeters($v), 'cm', $fedex_options['dim_units'], 1));

        list($dim_length, $dim_width, $dim_height) = $dims;

        $dimensions_xml = <<<OUT
    <q0:Dimensions>
        <q0:Length>{$dim_length}</q0:Length>
        <q0:Width>{$dim_width}</q0:Width>
        <q0:Height>{$dim_height}</q0:Height>
        <q0:Units>{$fedex_options['dim_units']}</q0:Units>
    </q0:Dimensions>
OUT;
    } else {
        $dimensions_xml = '';
    }

    return $dimensions_xml;
}

?>
