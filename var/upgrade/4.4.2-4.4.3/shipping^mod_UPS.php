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
 * UPS shipping library
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: mod_UPS.php,v 1.79.2.1 2011/01/10 13:12:10 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

function func_shipper_UPS($items, $userinfo, $orig_address, $debug, $cart)
{
    global $config, $sql_tbl, $smarty, $active_modules;
    global $ups_services, $origin_code, $dest_code;
    global $mod_UPS_tags, $mod_UPS_service;
    global $mod_UPS_errorcode, $mod_UPS_errordesc;
    global $mod_UPS_convrate;
    global $ups_services, $ups_packages;
    global $UPS_url;
    global $show_XML;
    global $allowed_shipping_methods, $intershipper_rates;
    global $shipping_calc_service, $intershipper_error;

    if (empty($active_modules['UPS_OnLine_Tools']))
        return;

    x_load('crypt','http');

    $UPS_username = text_decrypt(trim($config['UPS_OnLine_Tools']['UPS_username']));
    $UPS_password = text_decrypt(trim($config['UPS_OnLine_Tools']['UPS_password']));
    $UPS_accesskey = text_decrypt(trim($config['UPS_OnLine_Tools']['UPS_accesskey']));

    if (empty($UPS_username) || empty($UPS_password) || empty($UPS_accesskey))
        return;

    if (empty($allowed_shipping_methods) || !is_array($allowed_shipping_methods))
        return;

    $UPS_FOUND = false;
    foreach ($allowed_shipping_methods as $key=>$value) {
        if ($value['code'] == 'UPS')
            $UPS_FOUND = true;
    }

    if (!$UPS_FOUND)
        return;


    // Need to display UPS OnLine Tools trademarks

    $smarty->assign('display_ups_trademarks', 1);

    // Default UPS shipping options (if it wasn't defined yet by admin)

    $ups_parameters_default = array(
        'pickup_type' => '01',
        'packaging_type' => '02',#01
        'length' => 0,#02
        'width' => 0, #03
        'height' => 0,#04
        'upsoptions' => '', #05
        'delivery_conf' => 0,
        'conversion_rate' => 1,
        'av_status' => 'Y',
        'av_quality' => 'close',
        'lbs_countries' => array('DO','PR','US','CA'),
        'residential' => 'Y'
    );

    $params = func_query_first ("SELECT * FROM $sql_tbl[shipping_options] WHERE carrier='UPS'");

    $ups_parameters = unserialize($params['param00']);

    if (!is_array($ups_parameters)) {
        $ups_parameters = $ups_parameters_default;
    } else {
        $ups_parameters['lbs_countries'] = explode(";",$ups_parameters['lbs_countries']);
    }

    // Save original parameters for possible updating currency code
    $orig_ups_parameters = $ups_parameters;

    switch ($ups_parameters['pickup_type']) {
    case '01':
        $ups_parameters['customer_classification_code'] = '01';
        break;
    case '06':
    case '07':
    case '19':
    case '20':
        $ups_parameters['customer_classification_code'] = '03';
        break;
    case '03':
    case '11':
        $ups_parameters['customer_classification_code'] = '04';
    }

    // Get stored UPS OnLine Tools registraion information
    $ups_reg_data = unserialize($config['UPS_reginfo']);

    // Use company address if $ups_reg_data not found
    if (!is_array($ups_reg_data)) {
        $ups_reg_data = array();
        $ups_reg_data['city'] = $config["Company"]["location_city"];
        $ups_reg_data['state'] = $config["Company"]["location_state"];
        $ups_reg_data['country'] = $config["Company"]["location_country"];
        $ups_reg_data['postal_code'] = $config["Company"]["location_zipcode"];
    }

    $ups_reg_data['city'] = func_ups_xml_quote($ups_reg_data['city']);
    $ups_reg_data['state'] = func_ups_xml_quote($ups_reg_data['state']);

    // The origin address - from Company options
    // (suppose that ShipperAddress and ShipFrom is equal)

    $src_country_code = $orig_address['country'];
    $src_city = func_ups_xml_quote($orig_address['city']);
    $src_state_code = func_ups_xml_quote(strtoupper($orig_address['state']));
    $src_zipcode = $orig_address['zipcode'];

    // The destination address - from user's profile

    $dest_code = $dst_country_code = $userinfo['s_country'];
    
    $userinfo['s_state'] = strtoupper($userinfo['s_state']);

    if (($userinfo['s_state'] == 'PR') || ($userinfo['s_state'] == 'VI')) {
        $dest_code = $dst_country_code = $userinfo['s_state'];
    }
    $dst_city = func_ups_xml_quote($userinfo['s_city']);
    $dst_state_code = func_ups_xml_quote($userinfo['s_state']);
    $dst_zipcode = $userinfo['s_zipcode'];

    if ($src_country_code == 'US' && !empty($ups_parameters['customer_classification_code'])) {

        // CustomerClassification section is valid for origin country = 'US' only

        $customer_classification_code = $ups_parameters['customer_classification_code'];
        $customer_classification_query=<<<EOT
    <CustomerClassification>
        <Code>$customer_classification_code</Code>
    </CustomerClassification>
EOT;
    }

    // Pickup Type and Packaging Type

    $pickup_type = $ups_parameters['pickup_type'];
    $packaging_type = $ups_parameters['packaging_type'];

    $srvopts = array();
    foreach (explode("|",$ups_parameters['upsoptions']) as $opt) {
        switch($opt) {
            case 'AH': $pkgparams .= "\t\t\t<AdditionalHandling/>"; break;
            case 'SP': $srvopts[] = "\t\t\t<SaturdayPickupIndicator/>\n"; break;
            case 'SD': $srvopts[] = "\t\t\t<SaturdayDeliveryIndicator/>\n"; break;
        }
    }

    if (!empty($ups_parameters['shipper_number'])) {
        $shipper_number_xml=<<<EOT
            <ShipperNumber>$ups_parameters[shipper_number]</ShipperNumber>
EOT;
    }
    else
        $shipper_number_xml = '';

    // Residential / commercial address indicator
    if ($ups_parameters['residential'] == 'Y')
        $residental_flag = "\t\t\t<ResidentialAddressIndicator/>";
    else
        $residental_flag="";

    if (count($srvopts)>0)
        $shipment_options_xml .= "\t\t<ShipmentServiceOptions>\n".join('', $srvopts)."\t\t</ShipmentServiceOptions>";

    if (!empty($ups_parameters['negotiated_rates'])) {
        $negotiated_rates_xml =<<<EOT
        <RateInformation>
            <NegotiatedRatesIndicator/>
        </RateInformation>
EOT;
    }

    // Weight and dimension units depends on ShipFrom address

    if (in_array($src_country_code, $ups_parameters['lbs_countries'])) {
        $UPS_wunit = 'LBS';
        $UPS_dunit = 'IN';
    }
    else {
        $UPS_wunit = 'KGS';
        $UPS_dunit = 'CM';
    }

    // Custom package units defined on UPS OnLineTools settings page

    $custom_package_dunit = (in_array($ups_reg_data['country'], $ups_parameters["lbs_countries"]) ? 'IN' : 'CM');
    $custom_package_wunit = (in_array($ups_reg_data['country'], $ups_parameters["lbs_countries"]) ? 'LBS' : 'KGS');

    // Dimensions, specified in UPS configuration
    $specified_dims = array();

    // Define package limits (in LBS/IN)

    if($packaging_type=="02") {

        // Get dimensions, specified in UPS configuration,
        // and convert them to inches
        foreach(array('length', 'width', 'height') as $k) {
            $dim = ($custom_package_dunit == 'IN' ? $ups_parameters[$k] : func_units_convert($ups_parameters[$k], 'cm', 'in', 2)); // inches
            if($dim>0) $specified_dims[$k] = $dim;
        }

        $package_limits = $specified_dims;

        if($ups_parameters['weight']>0)
            $package_limits['weight'] = ($custom_package_wunit == 'LBS' ?
                $ups_parameters['weight'] :
                func_units_convert($ups_parameters['weight'], 'kg', 'lbs', 2)); // lbs

    } else {

        $package_limits = $ups_packages[$packaging_type]['limits'];

    }

    // if some limits are not specified, use limits for unknown packages
    $package_limits = func_array_merge($ups_packages['00']['limits'], $package_limits);

    // set girth limit if any of the dimension limits are not specified

    if(count($specified_dims)<3) {
        $package_limits['girth'] = 165; // length + girth, inches
    }

    $package_limits['price'] = 50000; // USD

    // Brings weight and dimension units in accordance to the units
    // specified in the store's General settings

    $specified_dims = func_correct_dimensions($specified_dims, false);
    $package_limits = func_correct_dimensions($package_limits);

    // Get packages

    $packages = func_get_packages($items, $package_limits, 200);

    $packages_xml = '';

    if (!empty($packages) && is_array($packages)) {

        foreach ($packages as $package) {

            if($ups_parameters['use_maximum_dimensions']=="Y")
                $package = func_array_merge($package, $specified_dims);

            $pkgopt = array();

            $UPS_weight = max(0.1, func_units_convert(func_weight_in_grams($package['weight']), "g", (($UPS_wunit=="LBS") ? "lbs" : "kg"), 1));

            // Dimensions of a package

            $UPS_length = func_units_convert(func_dim_in_centimeters($package['length']), "cm", (($UPS_dunit=="IN") ? "in" : "cm"), 2);
            $UPS_width = func_units_convert(func_dim_in_centimeters($package['width']), "cm", (($UPS_dunit=="IN") ? "in" : "cm"), 2);
            $UPS_height = func_units_convert(func_dim_in_centimeters($package['height']), "cm", (($UPS_dunit=="IN") ? "in" : "cm"), 2);

            if ($UPS_length + $UPS_width + $UPS_height > 0) {

                // Insert the Dimensions section

                $dimensions_query=<<<DIM
            <Dimensions>
                <UnitOfMeasurement>
                    <Code>$UPS_dunit</Code>
                </UnitOfMeasurement>
                <Length>$UPS_length</Length>
                <Width>$UPS_width</Width>
                <Height>$UPS_height</Height>
            </Dimensions>

DIM;

                $UPS_girth = $UPS_length + (2 * $UPS_width) + (2 * $UPS_height);

                if ($UPS_dunit == 'CM')
                    $UPS_girth = func_units_convert($UPS_girth, 'cm', 'in');

                if ($UPS_girth > 165) {
                    $dimensions_query .=<<<DIM
            <LargePackageIndicator />
DIM;
                }

            }

            // Declared value

            $insvalue_xml = '';
            if (!empty($package['price'])) {
                $insvalue = round(doubleval($package['price']), 2);
                if ($insvalue > 0.1) {
                    $pkgopt[] =<<<EOT
                <InsuredValue>
                    <CurrencyCode>$ups_parameters[currency_code]</CurrencyCode>
                    <MonetaryValue>$insvalue</MonetaryValue>
                </InsuredValue>

EOT;
                }
            }

            // Delivery confirmation option

            $delivery_conf = intval($ups_parameters['delivery_conf']);
            if ($delivery_conf > 0 && $delivery_conf < 4 && $src_country_code == 'US' && $dst_country_code == 'US' && !($cod_is_allowed && $codvalue >= 0.01)) {
                $pkgopt[] =<<<EOT
                <DeliveryConfirmation>
                    <DCISType>$delivery_conf</DCISType>
                </DeliveryConfirmation>

EOT;
            }

            $pkgparams = (count($pkgopt) > 0)?"\t\t\t<PackageServiceOptions>\n".join('',$pkgopt)."\t\t\t</PackageServiceOptions>\n":'';

            // Package description XML

            $package_xml=<<<EOT
        <Package>
            <PackagingType>
                <Code>$packaging_type</Code>
            </PackagingType>
            <PackageWeight>
                <UnitOfMeasurement>
                    <Code>$UPS_wunit</Code>
                </UnitOfMeasurement>
                <Weight>$UPS_weight</Weight>
            </PackageWeight>
$dimensions_query
$pkgparams
        </Package>
EOT;
            $packages_xml .= "\n".$package_xml;
        } // foreach

    }
    else
        return;

    $query=<<<EOT
<?xml version='1.0'?>
<AccessRequest xml:lang='en-US'>
    <AccessLicenseNumber>$UPS_accesskey</AccessLicenseNumber>
    <UserId>$UPS_username</UserId>
    <Password>$UPS_password</Password>
</AccessRequest>
<?xml version='1.0'?>
<RatingServiceSelectionRequest xml:lang='en-US'>
    <Request>
        <TransactionReference>
            <CustomerContext>Rating and Service</CustomerContext>
            <XpciVersion>1.0001</XpciVersion>
        </TransactionReference>
        <RequestAction>Rate</RequestAction>
        <RequestOption>shop</RequestOption>
    </Request>
    <PickupType>
        <Code>$pickup_type</Code>
    </PickupType>
$customer_classification_query
    <Shipment>
        <Shipper>
$shipper_number_xml
            <Address>
                <City>$ups_reg_data[city]</City>
                <StateProvinceCode>$ups_reg_data[state]</StateProvinceCode>
                <PostalCode>$ups_reg_data[postal_code]</PostalCode>
                <CountryCode>$ups_reg_data[country]</CountryCode>
            </Address>
        </Shipper>
        <ShipFrom>
            <Address>
                <City>$src_city</City>
                <StateProvinceCode>$src_state_code</StateProvinceCode>
                <PostalCode>$src_zipcode</PostalCode>
                <CountryCode>$src_country_code</CountryCode>
            </Address>
        </ShipFrom>
        <ShipTo>
            <Address>
                <City>$dst_city</City>
                <StateProvinceCode>$dst_state_code</StateProvinceCode>
                <PostalCode>$dst_zipcode</PostalCode>
                <CountryCode>$dst_country_code</CountryCode>
$residental_flag
            </Address>
        </ShipTo>
$packages_xml
$shipment_options_xml
$negotiated_rates_xml
    </Shipment>
</RatingServiceSelectionRequest>
EOT;

    $post = preg_split("/(\r\n|\r|\n)/",$query, -1, PREG_SPLIT_NO_EMPTY);

    // Perform the XML request

    if ($show_XML) $debug = 'Y';

    $md5_request = md5($query);
    if ((func_is_shipping_result_in_cache($md5_request)) &&  ($debug != 'Y')) {
        $intershipper_rates = func_get_shipping_result_from_cache($md5_request, $intershipper_rates);
        return;
    }

    list ($a,$result) = func_https_request("POST",$UPS_url."Rate",$post,'','','text/xml', '', '', '', '', 0, true);

    $init_intershipper_rates = $intershipper_rates;

    $mod_UPS_tags = array();
    $mod_UPS_service = '';
    $mod_UPS_errorcode = '';

    if ((float)$ups_parameters['conversion_rate'] != 0)
        $mod_UPS_convrate = (float)$ups_parameters['conversion_rate'];
    else
        $mod_UPS_convrate = 1.0;

    $origin_code = u_get_origin_code($src_country_code);
    $dest_code = u_get_origin_code($dest_code);

    $xml_parser = xml_parser_create("ISO-8859-1");
    xml_parser_set_option($xml_parser, XML_OPTION_TARGET_ENCODING, "ISO-8859-1");
    xml_set_element_handler($xml_parser, 'UPS_startElement', 'UPS_endElement');
    xml_set_character_data_handler($xml_parser, 'UPS_characterData');
    xml_parse($xml_parser, $result);
    xml_parser_free($xml_parser);

    if (!empty($mod_UPS_errorcode) && $mod_UPS_errorcode == '110971') {
        $mod_UPS_errordesc = '';
    }

    if (!empty($mod_UPS_errordesc)) {
        $shipping_calc_service = 'UPS';
        $intershipper_error = $mod_UPS_errordesc." (errorcode: ".$mod_UPS_errorcode.")";
    }
    elseif (!empty($intershipper_rates)) {
        $_intershipper_rates = array();
        foreach ($intershipper_rates as $k=>$v) {
            if (empty($v['shipping_time'])) {
                foreach ($allowed_shipping_methods as $_method_data) {
                    if ($_method_data['subcode'] == $v['methodid']) {
                        $v['shipping_time'] = $_method_data['shipping_time'];
                        break;
                    }
                }
            }

            if (!empty($v['methodid']))
                $_intershipper_rates[] = $v;
        }

        $intershipper_rates = $_intershipper_rates;
    }

    if (!empty($intershipper_rates)) {
        if ($ups_parameters['currency_code'] != $intershipper_rates[0]['currency']) {
            $orig_ups_parameters['currency_code'] = $intershipper_rates[0]['currency'];
            $orig_ups_parameters['lbs_countries'] = implode(";", $orig_ups_parameters['lbs_countries']);
            db_query("UPDATE $sql_tbl[shipping_options] SET param00='".addslashes(serialize($orig_ups_parameters))."' WHERE carrier='UPS'");
        }
    }

    if ($debug != 'Y')
        func_save_shipping_result_to_cache($md5_request, $intershipper_rates, $init_intershipper_rates);

    unset($init_intershipper_rates);

    if ($debug=="Y") {
        print "<h1>UPS Debug Information</h1>";
        if ($query) {
            $query=preg_replace("|<AccessLicenseNumber>.*</AccessLicenseNumber>|i","<AccessLicenseNumber>xxx</AccessLicenseNumber>",$query);
            $query=preg_replace("|<UserId>.*</UserId>|i","<UserId>xxx</UserId>",$query);
            $query=preg_replace("|<Password>.*</Password>|i","<Password>xxx</Password>",$query);

            print "<h2>UPS Request URL</h2>";
            print "<pre>".htmlspecialchars($UPS_url.'Rate')."</pre>";
            print "<h2>UPS Request</h2>";
            print "<pre>".htmlspecialchars($query)."</pre>";
            print "<h2>UPS Response</h2>";
            $result = preg_replace("/(>)(<[^\/])/", "\\1\n\\2", $result);
            $result = preg_replace("/(<\/[^>]+>)([^\n])/", "\\1\n\\2", $result);
            print "<pre>".htmlspecialchars($result)."</pre>";
            if ($intershipper_error != '') {
                print "<h1>Error processing request at UPS</h1>";
                print $intershipper_error;
            }
        }
        else {
            print "Before Rates & Service Selection Tool will be enabled you need to go through licensing and registering with UPS.";
        }
    }

    $query = null;
    $result = null;
}

/**
 * Functions to parse XML-response
 */
function UPS_startElement($parser, $name, $attrs)
{
    global $mod_UPS_tags;

    array_push($mod_UPS_tags,$name);
}

function UPS_characterData($parser, $data)
{
    global $mod_UPS_tags, $mod_UPS_service;
    global $allowed_shipping_methods, $intershipper_rates;
    global $mod_UPS_errorcode, $mod_UPS_errordesc;
    global $mod_UPS_currency, $mod_UPS_convrate;
    global $ups_services, $origin_code, $dest_code;
    global $RatedShipmentWarning;
    global $rate_added_flag;

    if ($mod_UPS_tags[2] == 'GUARANTEEDDAYSTODELIVERY' && $rate_added_flag) {
        $_current_index = count($intershipper_rates)-1;

        if (in_array($intershipper_rates[$_current_index]['service_code'], $ups_services[$mod_UPS_service])) {
            $intershipper_rates[$_current_index]['shipping_time'] = $data;
            $rate_added_flag = false;
        }
    }

    if ($mod_UPS_tags[2] == 'RATEDSHIPMENTWARNING') {
        if (preg_match('/location/i', $data))
            $RatedShipmentWarning .= $data."<br />";
    }

    if (count($mod_UPS_tags)>=4) {
        if ($mod_UPS_tags[2]=="SERVICE" && $mod_UPS_tags[3]=="CODE") {
            $mod_UPS_service=$data;
        }
        elseif ($mod_UPS_tags[2]=="TOTALCHARGES" && $mod_UPS_tags[3]=="CURRENCYCODE") {
            $mod_UPS_currency = $data;
        }
        elseif ($mod_UPS_tags[2]=="TOTALCHARGES" && $mod_UPS_tags[3]=="MONETARYVALUE") {
            $is_general_rate = true;
        }
        elseif ($mod_UPS_tags[2]=="NEGOTIATEDRATES" && $mod_UPS_tags[3]=="NETSUMMARYCHARGES" && $mod_UPS_tags[4]=="GRANDTOTAL" && $mod_UPS_tags[5]=="MONETARYVALUE") {
            $is_negotiated_rate = true;
        }

        if ($is_general_rate || $is_negotiated_rate) {
            $orig_rate = $data;
            $data = round($mod_UPS_convrate * $data, 2);
            $is_found = false;
            foreach ($allowed_shipping_methods as $sk=>$sv) {
                if ($sv['code']!="UPS")
                    continue;

                if ($sv['service_code'] == $ups_services[$mod_UPS_service][$origin_code]) {
                    if ($sv['service_code'] == '14' && $origin_code == 'US' && $dest_code == 'CA')
                        $subcode = 110; // UPS Standard to Canada
                    elseif ($sv['service_code'] == '12') {
                        if ($dest_code == 'US' || $dest_code == 'PR')
                            $subcode = $sv['subcode']; // UPS Saver
                        elseif ($origin_code == 'US' || $origin_code == 'PR')
                            $subcode = 145; // UPS Worldwide Saver (SM)
                        elseif (($origin_code == 'CA' && ($dest_code == 'US' || $dest_code == 'CA')) || ($origin_code == 'EU' && $dest_code == 'EU'))
                            $subcode = 146; // UPS Express Saver (SM)
                        else
                            $subcode = 144; // UPS Worldwide Express Saver (SM)
                    }
                    else
                        $subcode = $sv['subcode'];

                    $_rate_data = array(
                        'methodid'=>$subcode,
                        'rate'=>$data,
                        'currency'=>$mod_UPS_currency,
                        'orig_rate'=>$orig_rate,
                        'warning'=>$RatedShipmentWarning,
                        'service_code'=>$sv['service_code']
                    );

                    if ($is_negotiated_rate && is_array($intershipper_rates)) {
                        foreach ($intershipper_rates as $k=>$v) {
                            if ($v['methodid'] == $subcode) {
                                $intershipper_rates[$k] = $_rate_data;
                                break;
                            }
                        }
                    }
                    else
                        $intershipper_rates[] = $_rate_data;

                    $RatedShipmentWarning = '';
                    $rate_added_flag = true;
                    $is_found = true;
                    break;
                }
            }

            if (!empty($mod_UPS_service) && !$is_found && empty($ups_services[$mod_UPS_service][$origin_code])) {
                func_add_new_smethod("UPS #".$mod_UPS_service, 'UPS', array('service_code' => $mod_UPS_service));
            }
        }

        if ($mod_UPS_tags[3] == 'ERRORCODE')
            $mod_UPS_errorcode = $data;

        if ($mod_UPS_tags[3] == 'ERRORDESCRIPTION')
            $mod_UPS_errordesc = $data;
    }
}

function UPS_endElement($parser, $name)
{
    global $mod_UPS_tags;

    array_pop($mod_UPS_tags);
}

/**
 * Check if UPS allows box
 */
function func_check_limits_UPS($box)
{
    global $ups_packages;

    $box['weight'] = (isset($box['weight'])) ? $box['weight'] : 0;
    $avail = false;

    if (!empty($ups_packages)) {
        foreach ($ups_packages as $package)
            if (!empty($package['limits'])) {
                $package['limits'] = func_correct_dimensions($package['limits']);
                $avail = $avail || (func_check_box_dimensions($box, $package['limits']) && $package['limits']['weight'] > $box['weight']);
            }
    }

    return $avail;
}

?>
