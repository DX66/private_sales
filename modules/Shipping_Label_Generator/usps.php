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
 * U.S.P.S module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: usps.php,v 1.26.2.1 2011/01/10 13:12:01 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

/**
 * Fetch USPS shipping label
 */
function func_slg_handler_USPS($order, $from = null)
{
    global $sql_tbl, $config;

    x_load('xml','http');

    $response = array('label' => '', 'mime_type' => '', 'error' => '');

    if (empty($order) || empty($order['products'])) {
        $response['error'] = func_get_langvar_by_name("lbl_slg_order_no_products", false, false, true);
        return $response;
    }

    if (empty($from)) {
        $from = array(
            's_address' => $config['Company']['location_address'],
            's_city' => $config['Company']['location_city'],
            's_state' => $config['Company']['location_state'],
            's_country' => $config['Company']['location_country'],
            's_zipcode' => $config['Company']['location_zipcode'],
            's_firstname' => !empty($config['Shipping_Label_Generator']['usps_s_firstname']) ? $config['Shipping_Label_Generator']['usps_s_firstname'] : $config['Company']['company_name'],
            's_lastname'  => !empty($config['Shipping_Label_Generator']['usps_s_lastname']) ? $config['Shipping_Label_Generator']['usps_s_lastname'] : $config['Company']['company_name'],
            'company' => $config['Company']['company_name'],
            'phone' => $config['Company']['company_phone']);
    }

    $service_type = func_usps_check_shippingid($order['order']['shippingid']);
    if (!$service_type) {
        $response['error'] = func_get_langvar_by_name("lbl_shipping_label_error",false,false,true). " (shippingid: " . $order['order']['shippingid'] . ")";
    }
    if ($service_type == 'Error') {
        $response['error'] = func_get_langvar_by_name("lbl_shipping_label_error",false,false,true);
    }

    $user_id = $config['Shipping_Label_Generator']['usps_userid'];
    if (empty($user_id)) {
        $response['error'] = func_get_langvar_by_name("lbl_shipping_label_error_usps_empty",false,false,true);
    }

    // Stop processing if we got an error.
    if (!empty($response['error'])) {
        return $response;
    }

    // Request shipping label
    $usps_server = "https://secure.shippingapis.com/ShippingAPI.dll";
    $image_type = $config['Shipping_Label_Generator']['usps_image_type'];
    $usps_image_to_mime_type = array('TIF' => 'image/tiff', 'JPG' => 'image/jpeg', 'PDF' => 'application/pdf', 'GIF' => 'image/gif');
    // USPS shipping options
    $usps_params = func_query_first ("SELECT * FROM $sql_tbl[shipping_options] WHERE carrier='USPS'");
    $value_of_content = intval($usps_params['param07']);
    $container_express = $usps_params['param03'];

    // Set API call specific data.
    $label_elm = 'LabelImage';
    switch($service_type) {
    case 'ExpressMail':
        $head = $config['Shipping_Label_Generator']['usps_sample_mode'] == 'Y' ? "ExpressMailLabelCertify" : "ExpressMailLabel";
        $api = $head;
        $label_elm = 'EMLabel';
        break;
    case 'ExpressMailIntl':
        $head = $config['Shipping_Label_Generator']['usps_sample_mode'] == 'Y' ? "ExpressMailIntlCertify" : "ExpressMailIntl";
        $api = $head;
        break;
    case 'PriorityMailIntl':
        $head = $config['Shipping_Label_Generator']['usps_sample_mode'] == 'Y' ? "PriorityMailIntlCertify" : "PriorityMailIntl";
        $api = $head;
        break;
    case 'FirstClassMailIntl':
        $head = $config['Shipping_Label_Generator']['usps_sample_mode'] == 'Y' ? "FirstClassMailIntlCertify" : "FirstClassMailIntl";
        $api = $head;
        break;
    default:
        if ($config['Shipping_Label_Generator']['usps_sample_mode'] == 'Y') {
            $head = 'DelivConfirmCertifyV3.0';
            $api =  'DelivConfirmCertifyV3';
        } else {
            $head = 'DeliveryConfirmationV3.0';
            $api =  'DeliveryConfirmationV3';
        }
        $label_elm = 'DeliveryConfirmationLabel';
    }

    // Determine package weight
    $weight_in_ounces = $packages_number = 0;
    $descr = '';
    foreach ($order['products'] as $product) {
        $weight_in_ounces += $product['weight']*$product['amount'];
        $descr .= $product['package_descr'];
        $packages_number += $product['packages_number'];
    }
    $weight_in_ounces = ceil(func_units_convert(func_weight_in_grams($weight_in_ounces), 'g', 'oz', 3));
    if ($weight_in_ounces < 1) {
        $weight_in_ounces = 1;
    }

    // Set request sender/recipient data
    $to = $order['userinfo'];

    list($from['s_address1'], $from['s_address2']) = explode("\n", $from['s_address']);
    foreach ($from as $key=>$value) {
        $from[$key] = htmlspecialchars($value);
    }
    $from_fname = $from['s_firstname'];
    $from_lname = $from['s_lastname'];
    $from_firm = $from['company'];
    $from_addr1 = $from['s_address2'];
    $from_addr2 = $from['s_address1'];
    $from_city = $from['s_city'];
    $from_state = $from['s_state'];
    $from_zip4 = trim($from['s_zipcode4']);
    $from_zip5 = trim($from['s_zipcode']);
    $from_phone = substr(preg_replace("|[^\d]|i",'',$from['phone']), -10);
    $to_fname = htmlspecialchars(
        !empty($to['s_firstname']) ?
        $to['s_firstname'] : (
            !empty($to['firstname']) ?
            $to['firstname'] :
            func_get_langvar_by_name('txt_not_available',false,false,true)
        )
    );
    $to_lname = htmlspecialchars(
        !empty($to['s_lastname']) ?
        $to['s_lastname'] : (
            !empty($to['lastname']) ?
            $to['lastname'] :
            func_get_langvar_by_name('txt_not_available',false,false,true)
        )
    );
    $to_firm = htmlspecialchars($to['company']);
    $to_addr1 = htmlspecialchars($to['s_address_2']);
    $to_addr2 = htmlspecialchars($to['s_address']);
    $to_phone = htmlspecialchars($to['phone']);
    $to_country = htmlspecialchars($to['s_countryname']);
    $to_city = htmlspecialchars($to['s_city']);
    $to_state = htmlspecialchars($to['s_state']);
    $to_zip4 = htmlspecialchars($to['s_zipcode4']);
    $to_zip5 = htmlspecialchars($to['s_zipcode']);
    $value = htmlspecialchars($order['order']['subtotal']);
    $description = htmlspecialchars("Order #".$to['orderid']);
    $postage = htmlspecialchars($order['order']['shipping_cost']);

    // Create XML request
    $xml_head = $head.'Request';
    if ($service_type == 'ExpressMail') {
        $image_type = 'PDF'; // ExpressMail call supports GIF and PDF response. Request PDF.
        if (!empty($container_express) && $container_express == 'Flat Rate Envelope') {
            $flat_rate_and_weight_xml =  "<WeightInOunces/>\n";
            $flat_rate_and_weight_xml .= "<FlatRate>TRUE</FlatRate>";
        } else {
            $flat_rate_and_weight_xml =  "<WeightInOunces>".$weight_in_ounces."</WeightInOunces>\n";
            $flat_rate_and_weight_xml .= "<FlatRate></FlatRate>";
        }
        $query=<<<EOT
<$xml_head USERID="$user_id">
<Option/>
<EMCAAccount/>
<EMCAPassword/>
<ImageParameters/>
<FromFirstName>$from_fname</FromFirstName>
<FromLastName>$from_lname</FromLastName>
<FromFirm>$from_firm</FromFirm>
<FromAddress1>$from_addr1</FromAddress1>
<FromAddress2>$from_addr2</FromAddress2>
<FromCity>$from_city</FromCity>
<FromState>$from_state</FromState>
<FromZip5>$from_zip5</FromZip5>
<FromZip4>$from_zip4</FromZip4>
<FromPhone>$from_phone</FromPhone>
<ToFirstName>$to_fname</ToFirstName>
<ToLastName>$to_lname</ToLastName>
<ToFirm/>
<ToAddress1>$to_addr1</ToAddress1>
<ToAddress2>$to_addr2</ToAddress2>
<ToCity>$to_city</ToCity>
<ToState>$to_state</ToState>
<ToZip5>$to_zip5</ToZip5>
<ToZip4>$to_zip4</ToZip4>
<ToPhone></ToPhone>
$flat_rate_and_weight_xml
<POZipCode/>
<ImageType>$image_type</ImageType>
</$xml_head>
EOT;
    } elseif ($service_type == 'ExpressMailIntl') {
        $query=<<<EOT
<$xml_head USERID="$user_id">
<Option/>
<ImageParameters/>
<FromFirstName>$from_fname</FromFirstName>
<FromLastName>$from_lname</FromLastName>
<FromFirm>$from_firm</FromFirm>
<FromAddress1>$from_addr1</FromAddress1>
<FromAddress2>$from_addr2</FromAddress2>
<FromCity>$from_city</FromCity>
<FromState>$from_state</FromState>
<FromZip5>$from_zip5</FromZip5>
<FromZip4>$from_zip4</FromZip4>
<FromPhone>$from_phone</FromPhone>
<ToName>$to_fname $to_lname</ToName>
<ToFirm/>
<ToAddress1>$to_addr1</ToAddress1>
<ToAddress2>$to_addr2</ToAddress2>
<ToCity>$to_city</ToCity>
<ToProvince>$to_state</ToProvince>
<ToCountry>$to_country</ToCountry>
<ToPostalCode>$to_zip5</ToPostalCode>
<ToPOBoxFlag>N</ToPOBoxFlag>
<ToPhone>$to_phone</ToPhone>
<ShippingContents>
    <ItemDetail>
        <Description>$description</Description>
        <Quantity>1</Quantity>
        <Value>$value</Value>
        <NetPounds/>
        <NetOunces>$weight_in_ounces</NetOunces>
        <HSTariffNumber/>
        <CountryOfOrigin/>
    </ItemDetail>
</ShippingContents>
<InsuredAmount>$value_of_content</InsuredAmount>
<Postage></Postage>
<GrossPounds></GrossPounds>
<GrossOunces>$weight_in_ounces</GrossOunces>
<ContentType>Other</ContentType>
<ContentTypeOther>$description</ContentTypeOther>
<Agreement>Y</Agreement>
<ImageType>$image_type</ImageType>
</$xml_head>
EOT;
    } elseif ($service_type == 'PriorityMailIntl') {
        $query=<<<EOT
<$xml_head USERID="$user_id">
<Option/>
<ImageParameters/>
<FromFirstName>$from_fname</FromFirstName>
<FromLastName>$from_lname</FromLastName>
<FromFirm>$from_firm</FromFirm>
<FromAddress1>$from_addr1</FromAddress1>
<FromAddress2>$from_addr2</FromAddress2>
<FromCity>$from_city</FromCity>
<FromState>$from_state</FromState>
<FromZip5>$from_zip5</FromZip5>
<FromZip4>$from_zip4</FromZip4>
<FromPhone>$from_phone</FromPhone>
<ToName>$to_fname $to_lname</ToName>
<ToFirm/>
<ToAddress1>$to_addr1</ToAddress1>
<ToAddress2>$to_addr2</ToAddress2>
<ToCity>$to_city</ToCity>
<ToProvince>$to_state</ToProvince>
<ToCountry>$to_country</ToCountry>
<ToPostalCode>$to_zip5</ToPostalCode>
<ToPOBoxFlag>N</ToPOBoxFlag>
<ToPhone>$to_phone</ToPhone>
<ShippingContents>
    <ItemDetail>
        <Description>$description</Description>
        <Quantity>1</Quantity>
        <Value>$value</Value>
        <NetPounds/>
        <NetOunces>$weight_in_ounces</NetOunces>
        <HSTariffNumber/>
        <CountryOfOrigin/>
    </ItemDetail>
</ShippingContents>
<InsuredAmount>$value_of_content</InsuredAmount>
<Postage></Postage>
<GrossPounds></GrossPounds>
<GrossOunces>$weight_in_ounces</GrossOunces>
<ContentType>Other</ContentType>
<ContentTypeOther>$description</ContentTypeOther>
<Agreement>Y</Agreement>
<ImageType>$image_type</ImageType>
</$xml_head>
EOT;
    } elseif ($service_type == 'FirstClassMailIntl') {
        $query=<<<EOT
<$xml_head USERID="$user_id">
<Option/>
<ImageParameters/>
<FromFirstName>$from_fname</FromFirstName>
<FromLastName>$from_lname</FromLastName>
<FromFirm>$from_firm</FromFirm>
<FromAddress1>$from_addr1</FromAddress1>
<FromAddress2>$from_addr2</FromAddress2>
<FromCity>$from_city</FromCity>
<FromState>$from_state</FromState>
<FromZip5>$from_zip5</FromZip5>
<FromZip4>$from_zip4</FromZip4>
<FromPhone>$from_phone</FromPhone>
<ToName>$to_fname $to_lname</ToName>
<ToFirm/>
<ToAddress1>$to_addr1</ToAddress1>
<ToAddress2>$to_addr2</ToAddress2>
<ToCity>$to_city</ToCity>
<ToProvince>$to_state</ToProvince>
<ToCountry>$to_country</ToCountry>
<ToPostalCode>$to_zip5</ToPostalCode>
<ToPOBoxFlag>N</ToPOBoxFlag>
<ToPhone>$to_phone</ToPhone>
<ShippingContents>
    <ItemDetail>
        <Description>$description</Description>
        <Quantity>1</Quantity>
        <Value>$value</Value>
        <NetPounds/>
        <NetOunces>$weight_in_ounces</NetOunces>
        <HSTariffNumber/>
        <CountryOfOrigin/>
    </ItemDetail>
</ShippingContents>
<Postage></Postage>
<GrossPounds></GrossPounds>
<GrossOunces>$weight_in_ounces</GrossOunces>
<ContentType>Other</ContentType>
<ContentTypeOther>$description</ContentTypeOther>
<Agreement>Y</Agreement>
<ImageType>$image_type</ImageType>
</$xml_head>
EOT;
    } else {
        // Delivery Confirmation API
        $query=<<<EOT
<$xml_head USERID="$user_id">
<Option>1</Option>
<ImageParameters></ImageParameters>
<FromName>$from_fname</FromName>
<FromFirm>$from_firm</FromFirm>
<FromAddress1>$from_addr1</FromAddress1>
<FromAddress2>$from_addr2</FromAddress2>
<FromCity>$from_city</FromCity>
<FromState>$from_state</FromState>
<FromZip5>$from_zip5</FromZip5>
<FromZip4>$from_zip4</FromZip4>
<ToName>$to_fname $to_lname</ToName>
<ToFirm>$to_firm</ToFirm>
<ToAddress1>$to_addr1</ToAddress1>
<ToAddress2>$to_addr2</ToAddress2>
<ToCity>$to_city</ToCity>
<ToState>$to_state</ToState>
<ToZip5>$to_zip5</ToZip5>
<ToZip4>$to_zip4</ToZip4>
<WeightInOunces>$weight_in_ounces</WeightInOunces>
<ServiceType>$service_type</ServiceType>
<SeparateReceiptPage></SeparateReceiptPage>
<ImageType>$image_type</ImageType>
<AddressServiceRequested/>
</$xml_head>
EOT;
    }
    $query_prepared = urlencode($query);

    // Perform request
    list($header, $return) = func_https_request('GET', $usps_server."?API=$api&XML=".$query_prepared);

    if (defined('USPS_DEBUG'))
        x_log_add('usps', 'URL: ' .  $usps_server."?API=$api" . "\n\nQuery:\n" . $query . "\n\n" . $header . "\n\n" . $return);

    // Parse server response
    $res = func_xml2hash($return);
    if ($res['Error']) {
        $response['error'] = $res['Error']['Description'];
    } elseif ($res[$head.'Response']) {
        // Label data
        $response['label'] = base64_decode(str_replace(array("\n"), array(""), $res[$head.'Response'][$label_elm]));
        $response['descr'] = $descr;
        $response['packages_number'] = $packages_number;
        $additional_images = array();
        foreach (range(2, 9) as $page) {
            $page_elm = 'Page'.$page.'Image';
            if (isset($res[$head.'Response'][$page_elm]) && !empty($res[$head.'Response'][$page_elm])) {
                $additional_images[] = base64_decode(str_replace(array("\n"), array(''), $res[$head.'Response'][$page_elm]));
            }
        }
        if (!empty($additional_images)) {
            $response['label'] = array_merge((array)$response['label'], $additional_images);
        }

        // Label MIME type.
        if (in_array($image_type, array_keys($usps_image_to_mime_type))) {
            $response['mime_type'] = $usps_image_to_mime_type[$image_type];
        } else {
            // Failed to determine MIME type. Set generic MIME type.
            $response['mime_type'] = 'application/octet-stream';
        }
    } else {
        $response['error'] = func_get_langvar_by_name("lbl_shipping_label_error_usps_response", false, false, true);
    }

    unset($res);

    return $response;
}

?>
