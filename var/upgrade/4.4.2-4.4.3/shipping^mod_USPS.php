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
 * USPS shipping library
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: mod_USPS.php,v 1.80.2.5 2011/01/10 13:12:10 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

function func_shipper_USPS($items, $userinfo, $orig_address, $debug, $cart)
{
    global $config, $sql_tbl, $shop_language;
    global $allowed_shipping_methods, $intershipper_rates, $active_modules, $ship_packages_uniq;

    $USPS_username = $config['Shipping']['USPS_username'];
    $USPS_servername = $config['Shipping']['USPS_servername'];

    $first_intershipper_rates = $intershipper_rates;

    $use_usps_https = false;

    if (empty($USPS_username) || empty($USPS_servername))
        return;

    $USPS_FOUND = false;
    if (is_array($allowed_shipping_methods)) {
        foreach ($allowed_shipping_methods as $key=>$value) {
            if ($value['code'] == 'USPS') {
                $USPS_FOUND = true;
                break;
            }
        }
    }

    if (!$USPS_FOUND)
        return;

    x_load('http','xml');

    $params = func_query_first ("SELECT * FROM $sql_tbl[shipping_options] WHERE carrier='USPS'");

    $mailtype = $params['param00'];
    $package_size = $params['param01'];
    $machinable = $params['param02'];
    $container_express = $params['param03'];
    $container_priority = $params['param04'];
    $first_class_mail_type = empty($params['param05']) ? 'LETTER' : $params['param05'];

    $specified_dims = array();

    list($specified_dims['length'], $specified_dims['width'], $specified_dims['height'], $specified_dims['girth']) = explode(':', $params["param06"]);

    foreach($specified_dims as $k => $v) {
        if($v>0)
            $specified_dims[$k] = doubleval($v);
        else
            unset($specified_dims[$k]);
    }

    $value_of_content = intval($params['param07']);
    $container_express = (empty($container_express) || 'None' === $container_express) ? '' : '<Container>' . $container_express . '</Container>';
    $container_priority = (empty($container_priority) || 'None' === $container_priority) ? '' : '<Container>' . $container_priority . '</Container>';

    $userinfo['s_country'] = func_USPS_country_normalize($userinfo['s_country']);
    $orig_address['country'] = func_USPS_country_normalize($orig_address['country']);

    $dst_country = USPS_get_country($userinfo['s_country']);

    if (empty($dst_country)) {
        $dst_country = func_query_first_cell("SELECT value FROM $sql_tbl[languages] WHERE name = 'country_".$userinfo['s_country']."' AND code = '$shop_language'");
    }

    $USPS_file = ($USPS_servername=="testing.shippingapis.com")? '/ShippingAPITest.dll' : '/ShippingAPI.dll';

    $intl_use = $userinfo['s_country'] != $orig_address['country'];

    $ZO = $orig_address['zipcode'];
    $ZD = $userinfo['s_zipcode'];

    $package_limits = func_get_package_limits_USPS($intl_use);

    $pounds = 0;
    $rates = $used_requests = array();

    // The items are related to one provider only
    $provider = $items[0]['provider'];

    // Pass info about packages to func_place_order, using ship_packages_uniq variable
    x_session_register('ship_packages_uniq');
    $ship_packages_uniq[$provider . 'USPS'] = $previous_pack = array();
    $previous_pack_limit_key = '';

    foreach ($package_limits as $pack_limit_key => $package_limit) {

        $is_first_class = false;
        if (isset($package_limit['first_class']) && $package_limit['first_class'] == 'Y') {
            unset($package_limit['first_class']);
            $is_first_class = true;
        }

        $usps_rates = array();

        $packages = func_get_packages($items, $package_limit, 100);

        if (!empty($packages) && is_array($packages)) {

            $ship_packages_uniq[$provider . 'USPS'][$pack_limit_key] = array();

            foreach ($packages as $num => $pack) {

                // Save packages configuration for Shipping Label Generator

                if (!empty($active_modules['Shipping_Label_Generator'])) {
                    if ($pack === $previous_pack && $pack_limit_key == $previous_pack_limit_key) {
                        $_arr = $ship_packages_uniq[$provider . 'USPS'][$pack_limit_key];
                        $ship_packages_uniq[$provider . 'USPS'][$pack_limit_key][count($_arr)-1]['packages_number']++;
                    } else {
                        // Accumulate uniq package configurations into ship_packages_uniq variable
                        $ship_packages_uniq[$provider . 'USPS'][$pack_limit_key][] = array(
                            'packages_number' => 1,
                            'package' => $pack
                        );
                        $previous_pack = $pack;
                        $previous_pack_limit_key = $pack_limit_key;
                    }
                }
                $ounces = ceil(func_units_convert(func_weight_in_grams($pack['weight']), 'g', 'oz', 3));

                if($params['param09']=="Y")
                    $pack = func_array_merge($pack, $specified_dims);

                $dim_xml = '';
                $dim_xml .= "<Width>".func_units_convert(func_dim_in_centimeters($pack['width']), 'cm', 'in', 1)."</Width>";
                $dim_xml .= "<Length>".func_units_convert(func_dim_in_centimeters($pack['length']), 'cm', 'in', 1)."</Length>";
                $dim_xml .= "<Height>".func_units_convert(func_dim_in_centimeters($pack['height']), 'cm', 'in', 1)."</Height>";

                $dim_girth_xml = '';
                if($specified_dims['girth']>0 && $params['param04']=="NonRectangular")
                    $dim_girth_xml = "<Girth>".func_units_convert(func_dim_in_centimeters($specified_dims['girth']), 'cm', 'in', 1)."</Girth>";

                if ($intl_use) {
                    $value_of_content_xml = ($value_of_content > 0) ? "<ValueOfContents>$value_of_content</ValueOfContents>" : '';
                    $query=<<<EOT
<IntlRateRequest USERID="$USPS_username">
<Package ID="0">
<Pounds>$pounds</Pounds>
<Ounces>$ounces</Ounces>
<MailType>$mailtype</MailType>
$value_of_content_xml
<Country>$dst_country</Country>
</Package>
</IntlRateRequest>
EOT;
                } elseif ($is_first_class) {
                    $query = <<<EOT
<RateV3Request USERID="$USPS_username">
    <Package ID="0">
        <Service>FIRST CLASS</Service>
        <FirstClassMailType>$first_class_mail_type</FirstClassMailType>
        <ZipOrigination>$ZO</ZipOrigination>
        <ZipDestination>$ZD</ZipDestination>
        <Pounds>$pounds</Pounds>
        <Ounces>$ounces</Ounces>
        <Container>None</Container>
        <Size>$package_size</Size>
        $dim_xml
        $dim_girth_xml
        <Machinable>$machinable</Machinable>
    </Package>
</RateV3Request>
EOT;
                } else {
                    $query =<<<EOT
<RateV3Request USERID="$USPS_username">
    <Package ID="0">
        <Service>EXPRESS</Service>
        <ZipOrigination>$ZO</ZipOrigination>
        <ZipDestination>$ZD</ZipDestination>
        <Pounds>$pounds</Pounds>
        <Ounces>$ounces</Ounces>
        $container_express
        <Size>$package_size</Size>
        <Machinable>$machinable</Machinable>
    </Package>
    <Package ID="2">
        <Service>PRIORITY</Service>
        <ZipOrigination>$ZO</ZipOrigination>
        <ZipDestination>$ZD</ZipDestination>
        <Pounds>$pounds</Pounds>
        <Ounces>$ounces</Ounces>
        $container_priority
        <Size>$package_size</Size>
        $dim_xml
        $dim_girth_xml
    </Package>
    <Package ID="3">
        <Service>PARCEL</Service>
        <ZipOrigination>$ZO</ZipOrigination>
        <ZipDestination>$ZD</ZipDestination>
        <Pounds>$pounds</Pounds>
        <Ounces>$ounces</Ounces>
        <Container>None</Container>
        <Size>$package_size</Size>
        <Machinable>$machinable</Machinable>
    </Package>
    <Package ID="4">
        <Service>BPM</Service>
        <ZipOrigination>$ZO</ZipOrigination>
        <ZipDestination>$ZD</ZipDestination>
        <Pounds>$pounds</Pounds>
        <Ounces>$ounces</Ounces>
        <Container>None</Container>
        <Size>$package_size</Size>
    </Package>
    <Package ID="5">
        <Service>LIBRARY</Service>
        <ZipOrigination>$ZO</ZipOrigination>
        <ZipDestination>$ZD</ZipDestination>
        <Pounds>$pounds</Pounds>
        <Ounces>$ounces</Ounces>
        <Container>None</Container>
        <Size>$package_size</Size>
    </Package>
    <Package ID="6">
        <Service>MEDIA</Service>
        <ZipOrigination>$ZO</ZipOrigination>
        <ZipDestination>$ZD</ZipDestination>
        <Pounds>$pounds</Pounds>
        <Ounces>$ounces</Ounces>
        <Container>None</Container>
        <Size>$package_size</Size>
    </Package>
    <Package ID="7">
        <Service>EXPRESS HFP</Service>
        <ZipOrigination>$ZO</ZipOrigination>
        <ZipDestination>$ZD</ZipDestination>
        <Pounds>$pounds</Pounds>
        <Ounces>$ounces</Ounces>
        $container_express
        <Size>$package_size</Size>
        <Machinable>$machinable</Machinable>
    </Package>
    <Package ID="8">
        <Service>EXPRESS SH</Service>
        <ZipOrigination>$ZO</ZipOrigination>
        <ZipDestination>$ZD</ZipDestination>
        <Pounds>$pounds</Pounds>
        <Ounces>$ounces</Ounces>
        $container_express
        <Size>$package_size</Size>
        <Machinable>$machinable</Machinable>
    </Package>
</RateV3Request>
EOT;
                }

                $use_usps_call = true;
                $md5_request = md5($query);

                // Send the same requests within one pack configuration bt #91321
                // Do not send the same requests within different configuration
                if (isset($used_requests[$md5_request]) && $used_requests[$md5_request] != $pack_limit_key)
                    continue;

                $used_requests[$md5_request] = $pack_limit_key;

                if ((func_is_shipping_result_in_cache($md5_request)) &&  ($debug != 'Y')) {
                    $usps_rates[$num] = func_get_shipping_result_from_cache($md5_request);
                    $use_usps_call = false;
                }

                $rate_api = ($intl_use) ? 'IntlRate' : 'RateV3';

                if ($use_usps_call) {

                    list($header, $result) = $use_usps_https
                        ? func_https_request('GET', 'https://' . $USPS_servername . ':443' . $USPS_file . '?API=' . $rate_api . '&XML=' . urlencode($query))
                        : func_http_get_request($USPS_servername, $USPS_file, 'API=' . $rate_api . '&XML=' . urlencode($query));

                    $xml = func_xml_parse($result, $err);

                    $err = ($intl_use) ? func_array_path($xml, 'IntlRateResponse/Package/Error') : array();
                    // Get <Package> elements
                    $_packages = func_array_path($xml, $rate_api.'Response/Package'.($intl_use ? '/Service' : ''));

                    if (is_array($_packages) && empty($err)) {

                        $intershipper_rates = array();
                        $new_method_is_added = false;

                        foreach ($_packages as $p) {

                            // Get <Error> element
                            $err = ($intl_use) ? array() : func_array_path($p, 'Error');
                            if (!empty($err))
                                continue;

                            // Get shipping method name
                            $sname = func_array_path($p, ($intl_use) ? "SvcDescription/0/#" : "Postage/MailService/0/#");
                            $sname = trim($sname);

                            $sname = func_convert_trademark($sname);
                            $sname = preg_replace("/(.*)\*\*$/s", "\\1", $sname);

                            // Get delivery time
                            $delivery_time = ($intl_use) ? func_array_path($p, 'SvcCommitments/0/#') : '';
                            // Get rate
                            $rate = func_array_path($p, ($intl_use) ? "Postage/0/#" : "Postage/Rate/0/#");

                            if (empty($sname) || zerolen($rate))
                                continue;

                            // Define shipping method
                            $is_found = false;
                            foreach ($allowed_shipping_methods as $sm) {
                                if (
                                    $sm['code'] == 'USPS'
                                    && $sm['destination'] == (($intl_use) ? 'I' : 'L')
                                    && preg_match('/^' . preg_quote($sm['shipping'], '/') . '$/S', 'USPS ' . $sname)
                                ) {
                                    $intershipper_rates[] = array(
                                        'methodid'           => $sm['subcode'],
                                        'rate'               => $rate,
                                        'slg_shippingid'     => $sm['shippingid'],
                                        'slg_pack_limit_key' => $pack_limit_key,
                                        'warning'            => ''
                                    );

                                    $is_found = true;
                                    break;
                                }
                            }

                            if (!$is_found) {

                                // Add new shipping method
                                $params = array();
                                $params['destination'] = (($intl_use) ? 'I' : 'L');
                                if (!empty($delivery_time)) {
                                    $params['shipping_time'] = $delivery_time;
                                }
                                func_add_new_smethod('USPS ' . $sname, 'USPS', $params);
                                $new_method_is_added = true;
                            }
                        }

                        $intershipper_rates = func_normalize_shipping_rates($intershipper_rates, 'USPS');

                        if (
                            $debug != 'Y'
                            && !$new_method_is_added
                        ) {
                            func_save_shipping_result_to_cache($md5_request, $intershipper_rates);
                        }

                        $usps_rates[$num] = $intershipper_rates;

                    } // if (is_array($packages))

                    if ($debug == 'Y') {

                        // Display debug info
                        print "<h1>USPS Debug Information</h1>";
                        if ($query) {
                            $query = preg_replace("/(USERID[=][^ \t<>]*)/i", "USERID=\"xxx\"", $query);
                            $query = preg_replace("/(PASSWORD[=][^ \t<>]*)/i", "PASSWORD=\"xxx\"", $query);
                            print "<h2>USPS Request</h2>";
                            print "<pre>".htmlspecialchars($query)."</pre>";
                            print "<h2>USPS Response</h2>";
                            $result = preg_replace("/(>)(<[^\/])/", "\\1\n\\2", $result);
                            $result = preg_replace("/(<\/[^>]+>)([^\n])/", "\\1\n\\2", $result);
                            print "<pre>".htmlspecialchars($result)."</pre>";

                        } else {
                            print "It seems, you have forgotten to fill in an USPS account information.";
                        }
                    }
                }
            } // foreach $packages

        } //if (!empty($packages) && is_array($packages)) {

        $rates = func_array_merge($rates, func_intersect_rates($usps_rates));

    } // foreach $package_limits

    if (
        (
            substr($cart['delivery'], 0, 4) == 'USPS'
            || substr($cart['shipping'], 0, 4) == 'USPS'
        )
        && is_array($rates)
        && !empty($active_modules['Shipping_Label_Generator'])
    ) {

        // Correlate current USPS shipping method with related package configuration

        $found = false;
        foreach($rates as $rate) {
            if ($rate['slg_shippingid'] == $cart['shippingid']) {
                $ship_packages_uniq[$provider . 'USPS'] = $ship_packages_uniq[$provider . 'USPS'][$rate['slg_pack_limit_key']];
                $found = true;
                break;
            }
        }

        // Choose last package limit configuration
        if (!$found && !empty($ship_packages_uniq[$provider . 'USPS']))
            $ship_packages_uniq[$provider . 'USPS'] = array_pop($ship_packages_uniq[$provider . 'USPS']);
    } else {
        $ship_packages_uniq[$provider . 'USPS'] = array();
    }
    x_session_save('ship_packages_uniq');

    $intershipper_rates = func_array_merge($first_intershipper_rates, $rates);
}

/**
 * Get USPS country code
 */
function USPS_get_country($code)
{
    static $usps_countries = array(
        'AE' => 'United Arab Emirates',
        'PG' => 'Papua New Guinea',
        'AF' => 'Afghanistan',
        'NZ' => 'New Zealand',
        'FI' => 'Finland',
        'AL' => 'Albania',
        'DZ' => 'Algeria',
        'AD' => 'Andorra',
        'AO' => 'Angola',
        'AI' => 'Anguilla',
        'AG' => 'Antigua and Barbuda',
        'AR' => 'Argentina',
        'AM' => 'Armenia',
        'AW' => 'Aruba',
        'AU' => 'Australia',
        'AT' => 'Austria',
        'AZ' => 'Azerbaijan',
        'BS' => 'Bahamas',
        'BH' => 'Bahrain',
        'BD' => 'Bangladesh',
        'BB' => 'Barbados',
        'BY' => 'Belarus',
        'BE' => 'Belgium',
        'BZ' => 'Belize',
        'BJ' => 'Benin',
        'BM' => 'Bermuda',
        'BT' => 'Bhutan',
        'BO' => 'Bolivia',
        'BA' => 'Bosnia-Herzegovina',
        'BW' => 'Botswana',
        'BR' => 'Brazil',
        'VG' => 'British Virgin Islands',
        'BN' => 'Brunei Darussalam',
        'BG' => 'Bulgaria',
        'BF' => 'Burkina Faso',
        'MM' => 'Burma',
        'BI' => 'Burundi',
        'KH' => 'Cambodia',
        'CM' => 'Cameroon',
        'CA' => 'Canada',
        'CV' => 'Cape Verde',
        'KY' => 'Cayman Islands',
        'CF' => 'Central African Rep.',
        'TD' => 'Chad',
        'CL' => 'Chile',
        'CN' => 'China',
        'CO' => 'Colombia',
        'KM' => 'Comoros',
        'CG' => 'Congo, Democratic Republic of the',
        'CR' => 'Costa Rica',
        'CI' => 'Cte d\'Ivoire',
        'HR' => 'Croatia',
        'CU' => 'Cuba',
        'CY' => 'Cyprus',
        'CZ' => 'Czech Republic',
        'DK' => 'Denmark',
        'DJ' => 'Djibouti',
        'DM' => 'Dominica',
        'DO' => 'Dominican Republic',
        'EC' => 'Ecuador',
        'EG' => 'Egypt',
        'SV' => 'El Salvador',
        'GQ' => 'Equatorial Guinea',
        'ER' => 'Eritrea',
        'EE' => 'Estonia',
        'ET' => 'Ethiopia',
        'FK' => 'Falkland Islands',
        'FO' => 'Faroe Islands',
        'FJ' => 'Fiji',
        'FR' => 'France',
        'GF' => 'French Guiana',
        'PF' => 'French Polynesia',
        'GA' => 'Gabon',
        'GM' => 'Gambia',
        'GE' => 'Georgia, Republic of',
        'DE' => 'Germany',
        'GH' => 'Ghana',
        'GI' => 'Gibraltar',
        'GB' => 'Great Britain and Northern Ireland',
        'GR' => 'Greece',
        'GL' => 'Greenland',
        'GD' => 'Grenada',
        'GP' => 'Guadeloupe',
        'GU' => 'Guam',
        'GT' => 'Guatemala',
        'GN' => 'Guinea',
        'GW' => 'Guinea-Bissau',
        'GY' => 'Guyana',
        'HT' => 'Haiti',
        'HN' => 'Honduras',
        'HK' => 'Hong Kong',
        'HU' => 'Hungary',
        'IS' => 'Iceland',
        'IN' => 'India',
        'ID' => 'Indonesia',
        'IR' => 'Iran',
        'IQ' => 'Iraq',
        'IE' => 'Ireland',
        'IL' => 'Israel',
        'IT' => 'Italy',
        'JM' => 'Jamaica',
        'JP' => 'Japan',
        'JO' => 'Jordan',
        'KZ' => 'Kazakhstan',
        'KE' => 'Kenya',
        'KI' => 'Kiribati',
        'KP' => 'Korea, Democratic People\'s Republic of',
        'KR' => 'Korea, Republic of (South Korea)',
        'KW' => 'Kuwait',
        'KG' => 'Kyrgyzstan',
        'LA' => 'Laos',
        'LV' => 'Latvia',
        'LB' => 'Lebanon',
        'LS' => 'Lesotho',
        'LR' => 'Liberia',
        'LY' => 'Libya',
        'LI' => 'Liechtenstein',
        'LT' => 'Lithuania',
        'LU' => 'Luxembourg',
        'MO' => 'Macao',
        'MK' => 'Macedonia',
        'MG' => 'Madagascar',
        'MW' => 'Malawi',
        'MY' => 'Malaysia',
        'MV' => 'Maldives',
        'ML' => 'Mali',
        'MT' => 'Malta',
        'MH' => 'Marshall Islands',
        'MQ' => 'Martinique',
        'MR' => 'Mauritania',
        'MU' => 'Mauritius',
        'MX' => 'Mexico',
        'FM' => 'Micronesia, Federated States of',
        'MD' => 'Moldova',
        'MN' => 'Mongolia',
        'MS' => 'Montserrat',
        'MA' => 'Morocco',
        'MZ' => 'Mozambique',
        'NA' => 'Namibia',
        'NR' => 'Nauru',
        'NP' => 'Nepal',
        'NL' => 'Netherlands',
        'AN' => 'Netherlands Antilles',
        'NC' => 'New Caledonia',
        'NI' => 'Nicaragua',
        'NE' => 'Niger',
        'NG' => 'Nigeria',
        'MP' => 'Northern Mariana Islands, Commonwealth',
        'NO' => 'Norway',
        'OM' => 'Oman',
        'AS' => 'American Samoa',
        'PK' => 'Pakistan',
        'PW' => 'Palau',
        'PA' => 'Panama',
        'PY' => 'Paraguay',
        'PE' => 'Peru',
        'PH' => 'Philippines',
        'PN' => 'Pitcairn Island',
        'PL' => 'Poland',
        'PT' => 'Portugal',
        'PR' => 'Puerto Rico',
        'QA' => 'Qatar',
        'RE' => 'Reunion',
        'RO' => 'Romania',
        'RU' => 'Russia',
        'RW' => 'Rwanda',
        'KN' => 'Saint Christopher (St. Kitts) and Nevis',
        'SH' => 'Saint Helena',
        'LC' => 'Saint Lucia',
        'PM' => 'Saint Pierre and Miquelon',
        'VC' => 'Saint Vincent and the Grenadines',
        'WS' => 'Samoa, American',
        'SM' => 'San Marino',
        'ST' => 'Sao Tome and Principe',
        'SA' => 'Saudi Arabia',
        'SN' => 'Senegal',
        'SC' => 'Seychelles',
        'SL' => 'Sierra Leone',
        'SG' => 'Singapore',
        'SK' => 'Slovak Republic',
        'SI' => 'Slovenia',
        'SB' => 'Solomon Islands',
        'SO' => 'Somalia',
        'ZA' => 'South Africa',
        'ES' => 'Spain',
        'LK' => 'Sri Lanka',
        'SD' => 'Sudan',
        'SR' => 'Suriname',
        'SZ' => 'Swaziland',
        'SE' => 'Sweden',
        'CH' => 'Switzerland',
        'SY' => 'Syrian Arab Republic',
        'TW' => 'Taiwan',
        'TJ' => 'Tajikistan',
        'TZ' => 'Tanzania',
        'TH' => 'Thailand',
        'TG' => 'Togo',
        'TO' => 'Tonga',
        'TT' => 'Trinidad and Tobago',
        'TN' => 'Tunisia',
        'TR' => 'Turkey',
        'TM' => 'Turkmenistan',
        'TC' => 'Turks and Caicos Islands',
        'TV' => 'Tuvalu',
        'UG' => 'Uganda',
        'UA' => 'Ukraine',
        'UY' => 'Uruguay',
        'UZ' => 'Uzbekistan',
        'VU' => 'Vanuatu',
        'VA' => 'Vatican City',
        'VE' => 'Venezuela',
        'VN' => 'Vietnam',
        'VI' => 'Virgin Islands U.S.',
        'WF' => 'Wallis and Futuna Islands',
        'YE' => 'Yemen',
        'YU' => 'Yugoslavia',
        'ZM' => 'Zambia',
        'ZW' => 'Zimbabwe',
        'CC' => 'Cocos Island',
        'CK' => 'Cook Islands',
        'TP' => 'East Timor',
        'YT' => 'Mayotte',
        'MC' => 'Monaco',
        'NU' => 'Niue',
        'NF' => 'Norfolk Island',
        'TK' => 'Tokelau (Union) Group',
        'UK' => 'United Kingdom',
        'CX' => 'Christmas Island',
        'US' => 'United States',
    );

    if (isset($usps_countries[$code]))
        return $usps_countries[$code];

    return false;
}

/**
 * Return package limits for USPS
 */
function func_get_package_limits_USPS($intl_use)
{
    global $sql_tbl;

    $package_limits = ($intl_use)
        ? array(
            array('weight' => 20),
            array('weight' => 64, 'girth' => 107),
            array('weight' => 65, 'width' => 9.5, 'height' => 12.5),
            array('weight' => 65, 'girth' => 79, 'length' => 42),
            array('weight' => 69, 'width' => 12.5, 'height' => 15),
            array('weight' => 69, 'girth' => 107, 'length' => 45, 'width' => 34, 'height' => 45),
        )
        : array(
            array('weight' => 0.8125, 'first_class' => 'Y')
        );

    $params = func_query_first("SELECT param08, param06, param04, param01 FROM $sql_tbl[shipping_options] WHERE carrier='USPS'");
    list($dim['length'], $dim['width'], $dim['height'], $dim['girth']) = explode(':', $params['param06']);

    // Convert user-defined dimensions to inches
    foreach($dim as $k=>$v)
        $dim[$k] = func_units_convert(func_dim_in_centimeters($v), 'cm', 'in', 2);

    if (!$intl_use) {
        $girth = ($params['param01'] == 'Regular') ? 84 : (($params['param01'] == 'Large') ? 108 : 130);
        $dimensions_array = array();

        foreach (array('width', 'height', 'length') as $_dim) {

            if (isset($dim[$_dim]) && $dim[$_dim] != '') {

                $dimensions_array[$_dim] = $dim[$_dim];
            }

        }

        $package_limits = ($params['param04'] != 'Flat Rate Box')
            ? func_array_merge(
                $package_limits,
                array(
                    func_array_merge(array('weight' => 70, 'girth' => $girth), $dimensions_array)
                )
            )
            : func_array_merge(
                $package_limits,
                array(
                    array('weight' => 70, 'girth' => $girth, 'width' => 12, 'length' => 14, 'height' => 3.5),
                    array('weight' => 70, 'girth' => $girth, 'width' => 8.75, 'length' => 11.25, 'height' => 6)
                )
            );
    }

    $max_weight = doubleval($params['param08']);
    $max_weight = func_weight_in_lbs($max_weight);
    foreach($package_limits as $k => $v) {
        if($max_weight>0 && !$intl_use)
            $v['weight'] = min($v['weight'], $max_weight);
        $package_limits[$k] = func_correct_dimensions($v);
    }
    return $package_limits;
}

/**
 * Check if USPS allows box
 */
function func_check_limits_USPS($box)
{
    global $sql_tbl;

    $package_size = func_query_first_cell ("SELECT param01 FROM $sql_tbl[shipping_options] WHERE carrier='USPS'");

    $avail = false;
    $box['weight'] = isset($box['weight']) ? $box['weight'] : 0;

    foreach (array(false, true) as $intl_use) {
        $pack_limits = func_get_package_limits_USPS($intl_use);
        foreach ($pack_limits as $pack_limit) {
            if (!$intl_use) {
                if ($pack_limit['package_size'] != $package_size)
                    continue;
                unset($pack_limit['package_size']);
            }

            if (isset($pack_limit['first_class']))
                unset($pack_limit['first_class']);

            $avail = $avail || (func_check_box_dimensions($box, $pack_limit) && $pack_limit['weight'] > $box['weight']);
        }
    }
    return $avail;
}

/**
 * Changes several country codes into US one (GU, PR, VI)
 *
 * @param string $country country code
 *
 * @return string new normalized country code
 * @see    ____func_see____
 * @since  1.0.0
 */
function func_USPS_country_normalize($country)
{
    return in_array($country, array('GU', 'PR', 'VI'))
        ? 'US'
        : $country;
}

?>
