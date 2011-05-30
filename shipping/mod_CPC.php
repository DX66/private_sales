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
 * Canada Post shipping library
 * (only from Canada)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: mod_CPC.php,v 1.52.2.1 2011/01/10 13:12:10 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('xml','http');

function func_shipper_CPC($items, $userinfo, $orig_address, $debug, $cart)
{
    global $config, $sql_tbl;
    global $allowed_shipping_methods;
    global $shipping_calc_service, $intershipper_error, $intershipper_rates;

    if ($orig_address['country'] != 'CA' || empty($config['Shipping']['CPC_merchant_id']))
        return;

    $cpc_methods = array();
    foreach ($allowed_shipping_methods as $v) {
        if ($v['code']=="CPC") {
            $cpc_methods[] = $v;
        }
    }

    if (empty($cpc_methods)) return;

    $params = func_query_first ("SELECT * FROM $sql_tbl[shipping_options] WHERE carrier='CPC'");

    $cp_merchant = $config['Shipping']['CPC_merchant_id'];
    $cp_language = 'en';

    $cp_description = $params['param00'];

    $cp_insured_value = $params['param04'];

    $cp_dest_country = $userinfo['s_country'];
    $cp_dest_city = $userinfo['s_city'];
    $cp_dest_zip = $userinfo['s_zipcode'];
    $cp_dest_state = empty($userinfo['s_state']) ? 'NA' : $userinfo['s_state'];

    $cp_orig_zip = $orig_address['zipcode'];

    // Server DNS; if does not work, use 'cybervente.postescanada.ca:30000'
    $cp_host = "sellonline.canadapost.ca:30000";

    $specified_dims = array();
    foreach(array('length'=>'param01', 'width'=>'param02', 'height'=>'param03') as $k => $p) {
        $dim = doubleval($params[$p]);
        if($dim>0) $specified_dims[$k] = $dim;
    }

    $package_limits = func_get_package_limits_CPC($userinfo['s_country']);

    // Get packages

    $packages = func_get_packages($items, $package_limits, 200);
    if(empty($packages) || !is_array($packages)) return;

    if($params['param07']=="Y") {
        foreach($packages as $p => $package)
            $packages[$p] = func_array_merge($package, $specified_dims);
    }

    $lineItems = "<lineItems>\n";

    foreach($packages as $package) {
        // item weight in kilograms
        $weight = max(0.001, func_units_convert(func_weight_in_grams($package['weight']), 'g', 'kg', 3));

        // item dimensions in centimeters
        $length = max(0.01, round(func_dim_in_centimeters($package['length']),2));
        $width = max(0.01, round(func_dim_in_centimeters($package['width']),2));
        $height = max(0.01, round(func_dim_in_centimeters($package['height']),2));

        $lineItems .= "    <item>\n";
        $lineItems .= "      <quantity>1</quantity>\n";
        $lineItems .= "      <weight>$weight</weight>\n";
        $lineItems .= "      <length>$length</length>\n";
        $lineItems .= "      <width>$width</width>\n";
        $lineItems .= "      <height>$height</height>\n";
        $lineItems .= "      <description>$cp_description</description>\n";
        $lineItems .= "      <readyToShip/>\n";
        $lineItems .= "    </item>\n";
    }

    $lineItems .= "  </lineItems>";

    if (isset($cart['discounted_subtotal']))
        $itemsPrice = "<itemsPrice>$cart[discounted_subtotal]</itemsPrice>";
    elseif (!empty($cp_insured_value))
        $itemsPrice = "<itemsPrice>$cp_insured_value</itemsPrice>";
    else
        $itemsPrice = '';

    $cp_request =
        "<?xml version=\"1.0\" ?>\n".
        "<eparcel>".
        "<language>$cp_language</language>\n".
        "<ratesAndServicesRequest>\n".
        "  <merchantCPCID>$cp_merchant</merchantCPCID>\n".
        "  <fromPostalCode>$cp_orig_zip</fromPostalCode>\n".
        "  $itemsPrice\n".
        "  $lineItems\n".
        "  <city>$cp_dest_city</city>\n".
        "  <provOrState>$cp_dest_state</provOrState>\n".
        "  <country>$cp_dest_country</country>\n".
        "  <postalCode>$cp_dest_zip</postalCode>\n".
        "</ratesAndServicesRequest>\n".
        "</eparcel>";

    $md5_request = md5($cp_request);

    if ((!func_is_shipping_result_in_cache($md5_request)) ||  ($debug == 'Y')) {

        list($a,$result) = func_http_post_request($cp_host, '/', $cp_request);

        $parse_errors = false;
        $options = array(
            'XML_OPTION_CASE_FOLDING' => 1,
            'XML_OPTION_TARGET_ENCODING' => 'ISO-8859-1'
        );

        $parsed = func_xml_parse($result, $parse_errors, $options);

        $products =& func_array_path($parsed, 'EPARCEL/RATESANDSERVICESRESPONSE/PRODUCT');

        $rates = array();

        if (is_array($products)) {
            foreach ($products as $product) {
                $pid = $product['@']['ID'];
                $rate = func_array_path($product,'RATE/0/#');
                if ($pid === false || $rate === false) continue;

                $is_found = false;
                foreach ($cpc_methods as $v) {
                    if ($v['service_code'] == $pid) {
                        $rates[] = array(
                            'methodid' => $v['subcode'],
                            'rate' => $rate,
                        );

                        $is_found = true;
                        break;
                    }
                }

                if (!empty($pid) && !$is_found) {
                    $tmp_name = func_array_path($product,"NAME/0/#");
                    func_add_new_smethod($tmp_name, 'CPC', array('service_code' => $pid));
                }
            }
        }

        $intershipper_rates = func_array_merge($intershipper_rates, func_normalize_shipping_rates($rates, 'CPC'));

        if ($debug != 'Y') {
            func_save_shipping_result_to_cache($md5_request, $intershipper_rates);
        }

        $error_code = func_array_path($parsed, 'EPARCEL/ERROR/STATUSCODE/0/#');

        if ($error_code !== false) {
            $error_msg  = func_array_path($parsed, 'EPARCEL/ERROR/STATUSMESSAGE/0/#');
            $shipping_calc_service = "Canada Post";
            $intershipper_error = $error_msg;
        }

    } else {

        $intershipper_rates = func_get_shipping_result_from_cache($md5_request, $intershipper_rates);

    }

    if ($debug=="Y") {
        print "<h1>CPC Debug Information</h1>";
        if ($cp_request) {
            $query = preg_replace("|<merchantCPCID>.*</merchantCPCID>|i","<merchantCPCID>xxx</merchantCPCID>",$cp_request);

            print "<h2>CPC Request</h2>";
            print "<pre>".htmlspecialchars($query)."</pre>";
            print "<h2>CPC Response</h2>";
            print "<pre>".htmlspecialchars($result)."</pre>";
        }
        else {
            print "It seems, you have forgotten to fill in an CPC account information, or destination information (City, State, Country or ZipCode). Please check it, and try again.";
        }

        if ($intershipper_error) {
            print "<h2>CPC Error Information</h2>";
            print $intershipper_error;
        }
    }
}

/**
 * Return package limits for Canada POST
 */
function func_get_package_limits_CPC($country)
{
    global $config, $sql_tbl;

    $package_limits = array();

    // package max weight (kilograms)
    $package_limits['weight'] = 30;

    // Package max dimensions and girth (centimeters)

    switch($country) {
        case 'CA':
                // Domestic Delivery Services
                $package_limits['length'] = 200;
                $package_limits['width'] = 200;
                $package_limits['height'] = 200;
                $package_limits['girth'] = 300;
                break;
        case 'US':
                // Xpresspost USA
                $package_limits['length'] = 200;
                $package_limits['width'] = 200;
                $package_limits['height'] = 200;
                $package_limits['girth'] = 274;
                break;
        default:
                // International Purolator
                $package_limits['length'] = 100;
                $package_limits['width'] = 100;
                $package_limits['height'] = 100;
                $package_limits['girth'] = 200;
    }

    // Brings a package limits weight and dimension units in accordance to the units
    // specified in the store's General settings

    // Convert dimension limits to the dimension unit specified in the store's General settings is different
    $convert_fields = array('length', 'width', 'height', 'girth');
    foreach ($convert_fields as $field)
        if (!empty($package_limits[$field]))
            $package_limits[$field] = $package_limits[$field] * 1.00 / $config['General']['dimensions_symbol_cm'];

    // Convert weight limit to the weight unit specified in the store's General settings is different
    $package_limits['weight'] = $package_limits['weight'] * 1000 / $config['General']['weight_symbol_grams'];

    $params = func_query_first("SELECT * FROM $sql_tbl[shipping_options] WHERE carrier='CPC'");

    if($params['param06'] > 0) $package_limits['weight'] = $params['param06'];

    $dims_specified = true;

    $dim_params = array('length'=>'param01', 'width'=>'param02', 'height'=>'param03');
    foreach($dim_params as $k => $v) {
        if($params[$v] > 0) {
            $package_limits[$k] = min($package_limits[$k], $params[$v]);
        } else {
            $dims_specified = false;
        }
    }

    if($dims_specified) {
        unset($package_limits['girth']);
    }

    return $package_limits;
}

/**
 * Check if Canada POST allows box
 */
function func_check_limits_CPC($box)
{
    global $sql_tbl;

    $avail = false;
    $box['weight'] = isset($box['weight']) ? $box['weight'] : 0;

    foreach (array('CA', 'US', ' ') as $country) {
        $pack_limit = func_get_package_limits_CPC($country);
        $avail = $avail || (func_check_box_dimensions($box, $pack_limit) && $pack_limit['weight'] > $box['weight']);
    }
    return $avail;
}

?>
