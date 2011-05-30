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
 * Shipping options
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: shipping_options.php,v 1.77.2.3 2011/04/13 13:57:48 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './auth.php';
require $xcart_dir.'/include/security.php';

x_session_register('saved_user_data');

/**
 * This function gather the FedEx meter number from FedEx server
 */
function func_fedex_get_meter_number($userinfo, &$error)
{
    global $config;

    x_load('http','xml');

    // FedEx host
    $fedex_host = ($config['Shipping']['FEDEX_test_server'] == 'Y' ? 'gatewaybeta.fedex.com/GatewayDC' : 'gateway.fedex.com/GatewayDC');

    $xml_contact_fields = array();
    $xml_address_fields = array();

    $userinfo = array_map('htmlspecialchars',$userinfo);

    if (!empty($userinfo['company_name']))
        $xml_contact_fields[] = "<CompanyName>{$userinfo['company_name']}</CompanyName>";

    if (!empty($userinfo['pager_number']))
        $xml_contact_fields[] = "<PagerNumber>{$userinfo['pager_number']}</PagerNumber>";

    if (!empty($userinfo['fax_number']))
        $xml_contact_fields[] = "<FaxNumber>{$userinfo['fax_number']}</FaxNumber>";

    if (!empty($userinfo['email']))
        $xml_contact_fields[] = "<E-MailAddress>{$userinfo['email']}</E-MailAddress>";

    if (!empty($userinfo['address_2']))
        $xml_address_fields[] = "<Line2>{$userinfo['address_2']}</Line2>";

    $xml_contact_fields_str = implode("\n\t\t", $xml_contact_fields);
    $xml_address_fields_str = implode("\n\t\t", $xml_address_fields);

    if (!empty($userinfo['state']) && in_array($userinfo['country'], array("US", "CA", "PR")))
        $state = "<StateOrProvinceCode>{$userinfo['state']}</StateOrProvinceCode>";
    else
        $state = '';

    $xml_query = <<<OUT
<?xml version="1.0" encoding="UTF-8" ?>
<FDXSubscriptionRequest xmlns:api="http://www.fedex.com/fsmapi" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="FDXSubscriptionRequest.xsd">
    <RequestHeader>
        <CustomerTransactionIdentifier>String</CustomerTransactionIdentifier>
        <AccountNumber>{$config['Shipping']['FEDEX_account_number']}</AccountNumber>
    </RequestHeader>
    <Contact>
        <PersonName>{$userinfo['person_name']}</PersonName>
        <PhoneNumber>{$userinfo['phone_number']}</PhoneNumber>
$xml_contact_fields_str
    </Contact>
    <Address>
        <Line1>{$userinfo['address_1']}</Line1>
$xml_address_fields_str
        <City>{$userinfo['city']}</City>
        $state
        <PostalCode>{$userinfo['zipcode']}</PostalCode>
        <CountryCode>{$userinfo['country']}</CountryCode>
    </Address>
</FDXSubscriptionRequest>
OUT;

    $data = explode("\n", $xml_query);
    $host = "https://".$fedex_host;
    list($header, $result) = func_https_request('POST', $host, $data,'','','text/xml');

    if (defined('FEDEX_DEBUG'))
        x_log_add('fedex', $xml_query . "\n\n" . $header . "\n\n" . $result);

    $parse_error = false;
    $options = array(
        'XML_OPTION_CASE_FOLDING' => 1,
        'XML_OPTION_TARGET_ENCODING' => 'ISO-8859-1'
    );

    $parsed = func_xml_parse($result, $parse_error, $options);

    $error = array();

    if (empty($parsed)) {
        x_log_flag('log_shipping_errors', 'SHIPPING', "FedEx module: Received data could not be parsed correctly.", true);
        $error['msg'] = func_get_langvar_by_name("msg_fedex_meter_number_incorrect_data_err");
        return false;
    }

    $type = key($parsed);

    $meter_number = func_array_path($parsed, $type."/METERNUMBER/0/#");

    if (empty($meter_number)) {

        $error['code'] = func_array_path($parsed, $type."/ERROR/CODE/0/#");
        $error['msg'] = func_array_path($parsed, $type."/ERROR/MESSAGE/0/#");

        if (empty($error['code'])) {
            $error['code'] = func_array_path($parsed, "ERROR/CODE/0/#");
            $error['msg'] = func_array_path($parsed, "ERROR/MESSAGE/0/#");
        }

        if (!empty($error['code'])) {
            x_log_flag('log_shipping_errors', 'SHIPPING', "FedEx module error: [{$error['code']}] {$error['msg']}", true);
        }
        else {
            x_log_flag('log_shipping_errors', 'SHIPPING', "FedEx module error: Empty meter number received", true);
            $error['msg'] = func_get_langvar_by_name("msg_fedex_meter_number_empty_err");
        }

        return false;

    }

    return $meter_number;

}

$location[] = array(func_get_langvar_by_name('lbl_shipping_options'), '');

$carriers = func_get_carriers();

$carrier_valid = false;

foreach ($carriers as $k => $v) {
    if ($v[0] == $carrier) {
        $carrier_valid = true;
        break;
    }
}

$carrier = $carrier_valid ? $carrier : '';

if ($carrier_valid && $REQUEST_METHOD == 'POST') {
/**
 * Update the shipping options
 */
    $topMessage = '';
    $suffix = '';
    $shippingOptions = array();

    if ($carrier == 'FDX') {

    // FEDEX options update

        if (isset($get_meter_number)) {
            // Get the meter number from FedEx
            $state_error = in_array(
                $posted_data['country'],
                array('US', 'CA', 'PR')
            ) && empty($posted_data['state']);

            $saved_user_data = $posted_data;

            if (empty($posted_data['person_name']) || empty($posted_data['phone_number']) || empty($posted_data['address_1']) || empty($posted_data['city']) || $state_error || empty($posted_data['zipcode']) || empty($posted_data['country'])) {

                $topMessage = func_get_langvar_by_name('err_filling_form');
                $top_message['type'] = 'E';
                $suffix = "&error=1";

            } else {

                $subscriber_info = $posted_data;
                $error = false;
                $meter_number = func_fedex_get_meter_number($subscriber_info, $error);

                if ($meter_number) {

                    func_array2insert(
                        'config',
                        array(
                            'name'  => 'FEDEX_meter_number',
                            'value' => addslashes($meter_number),
                        ),
                        true
                    );

                    $topMessage = func_get_langvar_by_name('msg_fedex_meter_number_added');

                } else {

                    $topMessage = "The following error has been received from FedEx server: " . $error['msg'];
                    $top_message['type'] = 'E';

                }

            }

        } elseif(isset($clear_meter_number)) {

            // Remove meter number
            db_query("UPDATE $sql_tbl[config] SET value='' WHERE name='FEDEX_meter_number'");
            $topMessage = func_get_langvar_by_name('msg_fedex_meter_number_removed');

        } elseif (isset($update_options)) {

            // Update the FedEx options
            $carrier_codes = isset($carrier_codes) && !empty($carrier_codes) ? implode('|', $carrier_codes) : '';

            $fedex_options = array(
                'carrier_codes' => $carrier_codes,
                'packaging' => $packaging,
                'dropoff_type' => $dropoff_type,
                'ship_date' => intval($ship_date),
                'dim_length' => intval($dim_length),
                'dim_width' => intval($dim_width),
                'dim_height' => intval($dim_height),
                'max_weight' => abs(doubleval($max_weight)),
                'cod_value' => sprintf("%.2f", $cod_value),
                'cod_type' => $cod_type,
                'alcohol' => (empty($alcohol) ? 'N' : 'Y'),
                'hold_at_location' => (empty($hold_at_location) ? 'N' : 'Y'),
                'dry_ice' => (empty($dry_ice) ? 'N' : 'Y'),
                'nonstandard_container' => (empty($nonstandard_container) ? 'N' : 'Y'),
                'inside_pickup' => (empty($inside_pickup) ? 'N' : 'Y'),
                'inside_delivery' => (empty($inside_delivery) ? 'N' : 'Y'),
                'saturday_pickup' => (empty($saturday_pickup) ? 'N' : 'Y'),
                'saturday_delivery' => (empty($saturday_delivery) ? 'N' : 'Y'),
                'residential_delivery' => (empty($residential_delivery) ? 'N' : 'Y'),
                'dg_accessibility' => $dg_accessibility,
                'signature' => $signature,
                'handling_charges_amount' => sprintf("%.2f", $handling_charges_amount),
                'handling_charges_type' => $handling_charges_type,
                'currency_code' => $currency_code,
                'param01' => $param01,
                'param02' => $param02
            );

            $shippingOptions['param00'] = addslashes(serialize($fedex_options));

        }

    } elseif ($carrier == 'USPS') {

    // USPS options update

        $dim = abs(doubleval($dim_length)) . ':' . abs(doubleval($dim_width)) . ':' . abs(doubleval($dim_height)) . ':' . abs(doubleval($dim_girth));

        $large_containers = array('RECTANGULAR', 'NONRECTANGULAR');
        if ($package_size == 'LARGE') {
            // Business rule is "RECTANGULAR or NONRECTANGULAR must be indicated when <Size>LARGE</Size>."
            if (!in_array($container_express, $large_containers))
                $container_express = 'RECTANGULAR';

            if (!in_array($container_priority, $large_containers))
                $container_priority = 'RECTANGULAR';

            if (!in_array($container_intl, $large_containers))
                $container_intl = 'RECTANGULAR';
                
        } else {
            if (in_array($container_express, $large_containers))
                $container_express = 'Flat Rate Envelope';

            if (in_array($container_priority, $large_containers))
                $container_priority = 'Flat Rate Envelope';
        }

        settype($use_maximum_dimensions, 'string');
        $shippingOptions = array(
            'param00' => $mailtype,
            'param01' => $package_size,
            'param02' => $machinable,
            'param03' => $container_express,
            'param04' => $container_priority,
            'param05' => $firstclassmailtype,
            'param06' => $dim,
            'param07' => intval($value_of_content),
            'param08' => abs(doubleval($max_weight)),
            'param09' => ('Y' !== $use_maximum_dimensions) ? 'N' : $use_maximum_dimensions,
            'param10' => $container_intl,
        );

    } elseif ($carrier == 'Intershipper') {

    // INTERSHIPPER options update

        $shippingOptions = array(
            'param00' => $delivery,
            'param01' => $shipmethod,
            'param02' => abs(doubleval($length)),
            'param03' => abs(doubleval($width)),
            'param04' => abs(doubleval($height)),
            'param05' => is_array($options) ? implode('|', $options) : '',
            'param06' => $packaging,
            'param07' => $contents,
            'param08' => abs(doubleval($codvalue)),
            'param09' => abs(doubleval($weight)),
            'param10' => ('Y' !== $use_maximum_dimensions) ? 'N' : $use_maximum_dimensions,
        );

    } elseif ($carrier == 'CPC') {

    // Canada Post options update

        $shippingOptions = array(
            'param00' => $descr,
            'param01' => abs(doubleval($length)),
            'param02' => abs(doubleval($width)),
            'param03' => abs(doubleval($height)),
            'param04' => $insvalue,
            'param06' => abs(doubleval($weight)),
            'param07' => ('Y' !== $use_maximum_dimensions) ? 'N' : $use_maximum_dimensions,
        );

    } elseif ($carrier == 'ARB') {

    // Airborne ShipIt options update

        $shippingOptions = array(
            'param00' => $param00,
            'param01' => intval($param01),
            'param02' => intval($param02),
            'param03' => intval($param03),
            'param04' => intval($param04),
            'param05' => (intval($param06) < 1) ? 'NR' : $param05,
            'param06' => intval($param06),
            'param07' => $opt_haz . ',' . $opt_own_account,
            'param08' => !in_array($param08, array('M', 'P')) ? 'M' : $param08,
            'param09' => intval($param09),
            'param10' => abs(doubleval($param10)),
            'param11' => isset($param11) ? $param11 : 'N',
        );

    } elseif ($carrier == 'APOST') {

    // Australia Post options update

        $shippingOptions = array(
            'param00' => abs(doubleval($param00)),
            'param01' => abs(doubleval($param01)),
            'param02' => abs(doubleval($param02)),
            'param03' => isset($param03) ? $param03 : 'N',
            'param04' => abs(doubleval($param04)),
            'param05' => isset($param05) ? $param05 : 'N',
        );

    }

    if (!empty($shippingOptions)) {

        $shippingOptions['currency_rate'] = isset($currency_rate) ? abs(doubleval($currency_rate)) : 1;
        $shippingOptions['carrier'] = $carrier;

        func_array2insert(
            'shipping_options',
            $shippingOptions,
            true
        );

    }

    $top_message['content'] = empty($topMessage)
        ? func_get_langvar_by_name('msg_adm_shipping_option_upd')
        : $topMessage;

    func_header_location('shipping_options.php?carrier=' . $carrier . $suffix);

} // /if ($REQUEST_METHOD == 'POST')

/**
 * Collect options for current carrier
 */
$shipping_options = array ();

$shipping_options [strtolower($carrier)] = func_query_first("SELECT * FROM $sql_tbl[shipping_options] WHERE carrier='$carrier'");

if ($carrier == 'FDX') {

    // Prepare FedEx (API) information
    if (!empty($error))
        $smarty->assign('fill_error', 'Y');

        $prepared_user_data = $saved_user_data;

    if (empty($prepared_user_data)) {
        $prepared_user_data = array();
        $prepared_user_data['person_name'] = $user_account['firstname'] . ' ' . $user_account['lastname'];
        $prepared_user_data['company_name'] = $config['Company']['company_name'];
        $prepared_user_data['phone_number'] = preg_replace("/[^\d]/", "", $config['Company']['company_phone']);
        $prepared_user_data['email'] = $config['Company']['site_administrator'];
        $prepared_user_data['address_1'] = $config['Company']['location_address'];
        $prepared_user_data['city'] = $config['Company']['location_city'];
        $prepared_user_data['state'] = $config['Company']['location_state'];
        $prepared_user_data['zipcode'] = $config['Company']['location_zipcode'];
        $prepared_user_data['country'] = $config['Company']['location_country'];
    }

    $smarty->assign('prepared_user_data', $prepared_user_data);

    include_once $xcart_dir.'/include/countries.php';
    include_once $xcart_dir.'/include/states.php';

    $fedex_options = $shipping_options['fdx']['param00'];
    $shipping_options['fdx'] = unserialize($fedex_options);
}
#####################################

if ($carrier == 'Intershipper') {
/**
 * Get the shipping options for Intershipper service
 */
    $options = explode('|',$shipping_options["intershipper"]["param05"]);
    foreach($options as $option) {
        $shipping_options['intershipper']['options'][$option] = 'Y';
    }

    $smarty->assign('max_intershipper_weight', round(150 * 453.6 / $config['General']['weight_symbol_grams'], 4));
}

if ($carrier == 'CPC') {
    $smarty->assign('max_cpc_weight', round(30 * 1000 / $config['General']['weight_symbol_grams'], 4));
}

if ($carrier == 'ARB') {
    $_data = explode(',',$shipping_options["arb"]["param07"]);
    $shipping_options['arb']['opt_haz'] = @$_data[0];
    $shipping_options['arb']['opt_own_account'] = @$_data[1];
    $smarty->assign('max_arb_weight', round(149 * 453.6 / $config['General']['weight_symbol_grams'], 4));
}

if ($carrier == 'USPS') {
    $_dim = explode(':', $shipping_options["usps"]["param06"]);
    $shipping_options['usps']['dim_length'] = $_dim[0];
    $shipping_options['usps']['dim_width'] = $_dim[1];
    $shipping_options['usps']['dim_height'] = $_dim[2];
    $shipping_options['usps']['dim_girth'] = $_dim[3];
}

if ($carrier == 'FDX') {
    if (!empty($shipping_options['fdx']['carrier_codes'])) {
        $ccodes = explode('|', $shipping_options["fdx"]["carrier_codes"]);
        $shipping_options['fdx']['carrier_codes'] = array();
        foreach ($ccodes as $code) {
            $shipping_options['fdx']['carrier_codes'][$code] = 1;
        }
    }
}

$smarty->assign('carriers', $carriers);
$smarty->assign('carrier', $carrier);

$smarty->assign ('shipping_options', $shipping_options);

$smarty->assign('main','shipping_options');

// Assign the current location line
$smarty->assign('location', $location);

include './shipping_tools.php';

// Assign the section navigation data
$smarty->assign('dialog_tools_data', $dialog_tools_data);

if (
    file_exists($xcart_dir.'/modules/gold_display.php')
    && is_readable($xcart_dir.'/modules/gold_display.php')
) {
    include $xcart_dir.'/modules/gold_display.php';
}
func_display('admin/home.tpl',$smarty);
?>
