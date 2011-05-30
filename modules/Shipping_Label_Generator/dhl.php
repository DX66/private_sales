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
 * DHL shipping labels module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: dhl.php,v 1.17.2.1 2011/01/10 13:12:01 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

/**
 * Fetch DHL/Airborne shipping label
 */
function func_slg_handler_ARB($order)
{
    global $xcart_dir, $config, $sql_tbl;

    x_load('xml','http');

    require_once $xcart_dir.'/shipping/mod_ARB.php';

    $response = array('label' => '', 'mime_type' => '', 'error' => '');

    if (empty($order) || empty($order['products'])) {
        $response['error'] = func_get_langvar_by_name("lbl_slg_order_no_products", false, false, true);
        return $response;
    }

    $sender_company = htmlspecialchars(func_slg_cut_value($config['Company']['company_name'], 25));
    $sender_phone = $config['Company']['company_phone'];
    while (strpos($sender_phone, '0') === 0) {
        $sender_phone = substr($sender_phone, 1);
    }
    $sender_street_addr = $config['Company']['location_address'];
    $sender_city = $config['Company']['location_city'];
    $sender_state = $config['Company']['location_state'];
    $sender_postal_code = $config['Company']['location_zipcode'];
    $sender_email = $config['Company']['orders_department'];

    $rc_address = $order['order']['s_address'];
    $rc_address_2 = $order['order']['s_address_2'];
    $rc_city = $order['order']['s_city'];
    $rc_state = $order['order']['s_state'];
    $rc_zipcode = $order['order']['s_zipcode'];
    $rc_country = $order['order']['s_country'];
    $rc_phone = $order['order']['phone'];
    while (strpos($rc_phone, '0') === 0) {
        $rc_phone = substr($rc_phone, 1);
    }

    $rc_email = $order['order']['email'];
    $rc_company = htmlspecialchars(func_slg_cut_value($order['order']['company'], 35));
    $rc_name = $order['order']['s_firstname']." ".$order['order']['s_lastname'];

    if (empty($rc_company)) {
        $rc_company = 'None';
    }

    $params = func_query_first("SELECT * FROM $sql_tbl[shipping_options] WHERE carrier='ARB'");

    $ab_packaging = $params['param00'];
    $ab_ship_length = $params['param02'];
    $ab_ship_width = $params['param03'];
    $ab_ship_height = $params['param04'];
    $ab_ship_prot_code = $params['param05'];
    $ab_ship_prot_value = $params['param06'];
    $ab_ship_codpmt = $params['param08'];
    $ab_ship_codval = (float)$params['param09'];
    // options
    list($ab_ship_haz,$ab_ship_own_account) = explode(',',$params["param07"]);

    $_additional_protection = '';
    if ($ab_ship_prot_code == 'AP') {
        $_additional_protection = "<AdditionalProtection><Code>$ab_ship_prot_code</Code><Value>$ab_ship_prot_value</Value></AdditionalProtection>";
    }

    $_ship_date = func_arb_get_ship_date($params['param01']);

    $ab_id = $config['Shipping']['ARB_id'];
    $ab_password = $config['Shipping']['ARB_password'];
    $ab_ship_accnum = $config['Shipping']['ARB_account'];
    $ab_testmode = $config['Shipping']['ARB_testmode'];

    if (empty($ab_id) || empty($ab_password) || empty($ab_ship_accnum) || $config['Company']['location_country'] != 'US') {
        $response['error'] = 'Empty DHL/Airborne credentials or store is not located in US (please check General Settings &gt; Company options page.';
        return $response;
    }

    if ($ab_testmode == 'Y') {
        $ab_url = "https://eCommerce.airborne.com:443/ApiLandingTest.asp";
    } else {
        $ab_url = "https://ecommerce.airborne.com:443/ApiLanding.asp";
    }

    $ab_ship_key = ab_get_ship_key($ab_url, $ab_id, $ab_password, $ab_ship_accnum, $config['Company']['location_zipcode'], $ab_testmode, ($rc_country != 'US'));

    $service_type = func_dhl_check_shippingid($order['order']['shippingid'], $rc_country);
    $main_service_code = $service_type['main'];
    $xml_service_addon = '';
    if ((!empty($service_type['add'])) || ($ab_ship_haz == "Y")) {
        $xml_service_addon = "<SpecialServices>";
        if (!empty($service_type['add'])) {
            $xml_service_addon .= "<SpecialService><Code>".$service_type['add']."</Code></SpecialService>";
        }
        if ($ab_ship_haz == 'Y') {
            $xml_service_addon .= "<SpecialService><Code>HAZ</Code></SpecialService>";
        }
        $xml_service_addon .= "</SpecialServices>";
    }

    $weight = 0.0;
    foreach ($order['products'] as $product) {
        $weight += $product['weight'];
    }

    $ship_weight = max(1, func_units_convert(func_weight_in_grams($weight), 'g', 'lbs', 0));
    $ship_weight_oz = func_units_convert(func_weight_in_grams($weight), 'g', 'oz', 0);

    $_dimensions = '';
    $_weight = '';
    if ($ab_packaging == 'P') {
        $_dimensions = "<Dimensions><Width>$ab_ship_width</Width><Height>$ab_ship_height</Height><Length>$ab_ship_length</Length></Dimensions>";
        $_weight = "<Weight>$ship_weight</Weight>";
    }

    $intl_ship = '';
    if ($main_service_code != 'IE') {
        $query =<<<EOT
<?xml version='1.0'?>
<eCommerce action="Request" version="1.1">
<Requestor>
<ID>$ab_id</ID>
<Password>$ab_password</Password>
</Requestor>
<Shipment action='GenerateLabel' version='1.0'>
<ShippingCredentials>
<ShippingKey>$ab_ship_key</ShippingKey>
<AccountNbr>$ab_ship_accnum</AccountNbr>
</ShippingCredentials>
<ShipmentDetail>
<ShipDate>$_ship_date</ShipDate>
<Service>
<Code>$main_service_code</Code>
</Service>
$xml_service_addon
<ShipmentType>
<Code>$ab_packaging</Code>
</ShipmentType>
$_weight
$_dimensions
$_additional_protection
</ShipmentDetail>
<Dutiable>
<DutiableFlag>N</DutiableFlag>
<CustomsValue>0</CustomsValue>
</Dutiable>
<Billing>
<Party>
<Code>S</Code>
</Party>
</Billing>
<Receiver>
<AttnTo>$rc_name</AttnTo>
<PhoneNbr>$rc_phone</PhoneNbr>
<Address>
<CompanyName>$rc_company</CompanyName>
<Street>$rc_address</Street>
<City>$rc_city</City>
<State>$rc_state</State>
<Country>$rc_country</Country>
<PostalCode>$rc_zipcode</PostalCode>
</Address>
</Receiver>
<Sender>
<PhoneNbr>$sender_phone</PhoneNbr>
<SentBy>$sender_company</SentBy>
</Sender>
<ShipmentProcessingInstructions>
<Label>
<ImageType>PNG</ImageType>
</Label>
</ShipmentProcessingInstructions>
</Shipment>
</eCommerce>
EOT;
    } else {
        $intl_ship = 'INTL';
        $query =<<<EOT
<?xml version='1.0'?>
<eCommerce action="Request" version="1.1">
    <Requestor>
        <ID>$ab_id</ID>
        <Password>$ab_password</Password>
    </Requestor>
    <IntlShipment action="GenerateLabel" version="1.0">
        <ShippingCredentials>
            <ShippingKey>$ab_ship_key</ShippingKey>
            <AccountNbr>$ab_ship_accnum</AccountNbr>
        </ShippingCredentials>
    <ShipmentDetail>
        <ShipDate>$_ship_date</ShipDate>
        <Service>
            <Code>IE</Code>
        </Service>
        <ShipmentType>
            <Code>$ab_packaging</Code>
        </ShipmentType>
        $_weight
        $_dimensions
        $_additional_protection
        <ContentDesc>Product</ContentDesc>
    </ShipmentDetail>
    <Dutiable>
        <DutiableFlag>N</DutiableFlag>
        <CustomsValue>1</CustomsValue>
        <IsSEDReqd>N</IsSEDReqd>
    </Dutiable>
    <Billing>
        <Party>
            <Code>S</Code>
        </Party>
        <DutyPaymentType>R</DutyPaymentType>
    </Billing>
    <Sender>
        <Address>
            <CompanyName>$sender_company</CompanyName>
            <Street>$sender_street_addr</Street>
            <City>$sender_city</City>
            <State>$sender_state</State>
            <Country>US</Country>
            <PostalCode>$sender_postal_code</PostalCode>
        </Address>
        <SentBy>$sender_company</SentBy>
        <PhoneNbr>$sender_phone</PhoneNbr>
        <Email>$sender_email</Email>
    </Sender>
    <Receiver>
        <Address>
            <CompanyName>$rc_company</CompanyName>
            <Street>$rc_address</Street>
            <StreetLine2>$rc_address_2</StreetLine2>
            <City>$rc_city</City>
            <State>$rc_state</State>
            <Country>$rc_country</Country>
            <PostalCode>$rc_zipcode</PostalCode>
        </Address>
        <AttnTo>$rc_name</AttnTo>
        <PhoneNbr>$rc_phone</PhoneNbr>
        <Email>$rc_email</Email>
    </Receiver>
    <ShipmentProcessingInstructions>
        <Label>
            <ImageType>PNG</ImageType>
        </Label>
    </ShipmentProcessingInstructions>
</IntlShipment>
</eCommerce>
EOT;
    }

    $post = explode("\n", $query);
    list($a, $ab_response) = func_https_request('POST', $ab_url, $post, '','', 'text/xml');

    $options = array(
        'XML_OPTION_CASE_FOLDING' => 1,
        'XML_OPTION_TARGET_ENCODING' => 'ISO-8859-1'
    );
    $parsed = func_xml_parse($ab_response, $parse_errors, $options);
    $image = func_array_path($parsed, "ECOMMERCE/#/".$intl_ship."SHIPMENT/0/#/LABEL/0/#/IMAGE/0/#");

    if (!$image) {
        // Collect information about DHL request error
        $faults = func_array_path($parsed, "ECOMMERCE/#/".$intl_ship."SHIPMENT/0/#/FAULTS/0/#/FAULT");
        if (empty($faults)) {
            $faults = func_array_path($parsed, "ECOMMERCE/#/FAULTS/0/#/FAULT");
            if (!empty($faults)) {
                foreach ($faults as $fault) {
                    $response['error'] .= func_array_path($fault, "#/DESCRIPTION/0/#");
                }
            }
        } else {
            foreach ($faults as $fault) {
                $response['error'] .= func_array_path($fault, '#/DESC/0/#');
            }
        }
    } else {
        // Store label data
        $response['label'] = base64_decode(str_replace(array("\n"), array(""), $image));
        $response['mime_type'] = "image/png";
    }

    return $response;
}

?>
