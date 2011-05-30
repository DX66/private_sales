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
 * ARB shipping library
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: mod_ARB.php,v 1.50.2.1 2011/01/10 13:12:10 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

global $dhl_ext_countries;

$dhl_ext_countries = array();
if (
    $userinfo['s_country'] != 'US' &&
    !empty($config['Shipping']['ARB_id']) &&
    !empty($config['Shipping']['ARB_password']) &&
    !empty($config['Shipping']['ARB_account'])
) {
    switch ($userinfo['s_country']) {
        case 'AN':
            $dhl_ext_countries = array(
                "AN-ST. MAARTEN",
                "AN-BONAIRE",
                "AN-CURACAO",
                "ST. EUSTATIUS"
            );
            break;

        case 'GB':
            $dhl_ext_countries = array(
                "UK-ENGLAND",
                "UK-NORTHERN IRELAND",
                "UK-SCOTLAND",
                "UK-WALES",
                'JERSEY',
            );
            break;

        case 'IL':
            $dhl_ext_countries = array(
                "IL-GAZA STRIP",
                "IL-WEST BANK"
            );
            break;

        case 'GP':
            $dhl_ext_countries = array(
                "ST. BARTHELEMY"
            );
            break;

        case 'ES':
            $dhl_ext_countries = array(
                "CANARY ISLANDS"
            );
            break;

    }
}

function func_shipper_ARB($items, $userinfo, $orig_address, $debug, $cart)
{
    global $config, $sql_tbl, $override_arb_request;
    global $arb_account_used, $airborne_account;
    global $intershipper_rates, $intershipper_error;
    global $allowed_shipping_methods, $is_cached;
    global $smarty;

    x_session_register('arb_account_used');
    x_session_register('airborne_account');

    $arb_account_used = false;

    $ARB_FOUND = false;
    if (is_array($allowed_shipping_methods)) {
        foreach ($allowed_shipping_methods as $key=>$value) {
            if ($value['code'] == 'ARB') {
                $ARB_FOUND = true;
                break;
            }
        }
    }

    if (!$ARB_FOUND)
        return;

    $smarty->assign('has_active_arb_smethods', 'Y');

    x_load('http','xml');

    list($ab_id, $ab_password, $ab_ship_accnum) = (!empty($orig_address["ARB_id"])) ? array($orig_address["ARB_id"], $orig_address["ARB_password"], $orig_address['ARB_account']) : array($config['Shipping']['ARB_id'], $config['Shipping']['ARB_password'], $config['Shipping']['ARB_account']);

    $ab_testmode = $config['Shipping']['ARB_testmode'];

    // Currently shipping only from US is supported

    if (empty($ab_id) || empty($ab_password) || empty($ab_ship_accnum) || $orig_address['country'] != 'US')
        return;

    if ($ab_testmode == 'Y')
        $ab_url = "https://ecommerce.airborne.com:443/ApiLandingTest.asp";
    else
        $ab_url = "https://ecommerce.airborne.com:443/ApiLanding.asp";

    $ab_ship_key = ab_get_ship_key($ab_url, $ab_id, $ab_password, $ab_ship_accnum, $orig_address['zipcode'], $ab_testmode, $orig_address, ($userinfo['s_country'] != 'US'), $debug);
    if (empty($ab_ship_key)) {
        if ($debug == 'Y')
            ab_show_faults();

        ab_conv_faults();
        return;
    }

    $first_intershipper_rates = $intershipper_rates;

    $params = func_query_first ("SELECT * FROM $sql_tbl[shipping_options] WHERE carrier='ARB'");

    $specified_dims = array();
    foreach(array('length'=>'param02', 'width'=>'param03', 'height'=>'param04') as $k => $p) {
        $dim = doubleval($params[$p]);
        if($dim>0) $specified_dims[$k] = $dim;
    }

    $ab_packaging = $params['param00'];
    $ab_ship_prot_code = $params['param05'];
    $ab_ship_prot_value = $params['param06'];
    $ab_ship_codpmt = $params['param08'];
    $ab_ship_codval = (float)$params['param09'];
    // options
    list($ab_ship_haz,$ab_ship_own_account) = explode(',',$params["param07"]);

    global $current_arb_ship_date;

    global $mod_AB_ship_flags;
    $mod_AB_ship_flags = array (
        109 => array('code'=>'G', 'sub'=>''),        // Airborne Ground
        31  => array('code'=>'S', 'sub'=>''),        // Airborne Second Day Service
        33  => array('code'=>'N', 'sub'=>''),        // Airborne Next Afternoon
        32  => array('code'=>'E', 'sub'=>''),        // Airborne Express
        124 => array('code'=>'E', 'sub'=>'1030'),    // Airborne Express 10:30 AM
        125 => array('code'=>'E', 'sub'=>'SAT'),    // Airborne Express Saturday
        32    => array('code'=>'IE','sub'=>'')        // Airborne International Express
    );

    $package_limit = func_get_package_limits_ARB();

    $packages = func_get_packages($items, $package_limit, 100);

    if($params['param11']=="Y" && is_array($packages)) {
        foreach($packages as $p => $package)
            $packages[$p] = func_array_merge($package, $specified_dims);
    }

    if ($userinfo['s_country'] != 'US')
        return ab_int_ratings(
            $allowed_shipping_methods,
            $userinfo,
            $debug,
            array(
                'package' => $ab_packaging,
                'id' => $ab_id,
                'password' => $ab_password,
                'account' => $ab_ship_accnum,
                'testmode' => $ab_testmode,
                'url' => $ab_url,
                'skey' => ($orig_address['ARB_shipping_key_intl']) ? $orig_address['ARB_shipping_key_intl'] : $config["Shipping"]["ARB_shipping_key_intl"],
                'ship_date' => $_ship_date,
                'ttl' => $params['param01']
            ),
            $orig_address,
            $packages
        );

    $dhl_rates = array();

    if (!empty($packages) && is_array($packages))
    foreach ($packages as $num => $pack) {

        $dhl_rates[$num] = array();

        $ship_weight = max(1, func_units_convert(func_weight_in_grams($pack['weight']), "g", "lbs", 0));
        $ship_weight_oz = func_units_convert(func_weight_in_grams($pack['weight']), "g", "oz", 0);
        $ab_ship_length = func_units_convert(func_dim_in_centimeters($pack['length']), 'cm', 'in');
        $ab_ship_width = func_units_convert(func_dim_in_centimeters($pack['width']), 'cm', 'in');
        $ab_ship_height = func_units_convert(func_dim_in_centimeters($pack['height']), 'cm', 'in');

        $override_arb_request = true;
        $max_repeat = 5;

        while ($override_arb_request && $max_repeat-- > 0) {
            $override_arb_request = false;

            $current_arb_ship_date = $_ship_date = func_arb_get_ship_date($params['param01']);

            $shipments = '';
            $cnt = 0;
            $ship_reqs = array();

            foreach ($allowed_shipping_methods as $method) {
                if (!(
                    $method['code'] == 'ARB' &&
                    ($ship_weight < $method['weight_limit'] || $method['weight_limit'] == 0.00) &&
                    isset($mod_AB_ship_flags[$method['subcode']]) &&
                    $method['destination'] == 'L')
                )
                    continue;

                $_ship_srv_key = $mod_AB_ship_flags[$method['subcode']]['code'];
                $_ship_srv_sub = $mod_AB_ship_flags[$method['subcode']]['sub'];

                if ($_ship_srv_key == 'G' && $ab_packaging == 'L') {
                    // Letter express is not allowed with Ground Shipments. (Code=4119)
                    continue;
                }

                if ($_ship_srv_key == 'G' && $_ship_srv_sub == 'SAT') {
                    // Saturday pickup service is not available for Ground shipments. (Code=4105).
                    continue;
                }

                $_shipproc_instr = '';
                $_secial_express = '';
                if ($_ship_srv_key == 'E') {
                    // Express Saturday & Express 10:30AM services are not compatible within "Hazardous Materials"
                    if ($ab_ship_haz == 'Y' && $_ship_srv_sub != '')
                        continue;

                    if ($_ship_srv_sub == 'SAT') {
                        $_shipproc_instr = "<ShipmentProcessingInstructions><Overrides><Override><Code>ES</Code></Override></Overrides></ShipmentProcessingInstructions>";
                        $_secial_express = "<SpecialServices><SpecialService><Code>SAT</Code></SpecialService></SpecialServices>";
                    } elseif ($_ship_srv_sub == '1030') {
                        $_secial_express = "<SpecialServices><SpecialService><Code>1030</Code></SpecialService></SpecialServices>";
                    }
                }

                $_additional_protection = '';
                if ($ab_ship_prot_code == 'AP') {
                    $_additional_protection = "<AdditionalProtection><Code>$ab_ship_prot_code</Code><Value>$ab_ship_prot_value</Value></AdditionalProtection>";
                }

                $_secial_haz = '';
                if ($ab_ship_haz == 'Y') {
                    $_secial_haz = "<SpecialServices><SpecialService><Code>HAZ</Code></SpecialService></SpecialServices>";
                }

                $_cod_payment = '';
                if ($ab_ship_codval > 0 && $_party_code == 'S') {
                    // When using COD service freight charges must be billed to sender. (Code=4116)
                    $_cod_payment = "<CODPayment><Code>$ab_ship_codpmt</Code><Value>$ab_ship_codval</Value></CODPayment>";
                }

                $_dimensions = '';
                if ($ab_packaging == 'P') {
                    $_dimensions = "<Weight>$ship_weight</Weight><Dimensions><Width>$ab_ship_width</Width><Height>$ab_ship_height</Height><Length>$ab_ship_length</Length></Dimensions>";
                }
                else {
                    if ($ship_weight_oz > 8) {
                        // Shipment exceeds allowable weight for Letter. (Code=4118)
                        // Letter Express packages must be in Letter Express envelopes and weigh 8 ounces or less.
                        continue;
                    }
                }

                $shipment =<<<EOT
    <Shipment action='RateEstimate' version='1.0'>
        <ShippingCredentials>
            <ShippingKey>$ab_ship_key</ShippingKey>
            <AccountNbr>$ab_ship_accnum</AccountNbr>
        </ShippingCredentials>
        <ShipmentDetail>
            <ShipDate>$_ship_date</ShipDate>
            <Service>
                <Code>$_ship_srv_key</Code>
            </Service>
            <ShipmentType>
                <Code>$ab_packaging</Code>
            </ShipmentType>
            $_secial_express
            $_secial_haz
            $_dimensions
            $_additional_protection
        </ShipmentDetail>
        <Billing>
            $_cod_payment
            <Party>
                <Code>S</Code>
            </Party>
        </Billing>
        <Receiver>
            <Address>
                <City>$userinfo[s_city]</City>
                <State>$userinfo[s_state]</State>
                <Country>$userinfo[s_country]</Country>
                <PostalCode>$userinfo[s_zipcode]</PostalCode>
            </Address>
        </Receiver>
        $_shipproc_instr
    </Shipment>
EOT;
                $shipments .= $shipment;
                $cnt++;
                if ($cnt >= 5) {
                    $cnt = 0;
                    if ($shipments != '')
                        $ship_reqs[] = $shipments;
                    $shipments = '';
                }
            } // foreach $allowed_shipping_methods

            if ($shipments != '')
                $ship_reqs[] = $shipments;

            if (count($ship_reqs) > 0) {
                $ab_request = '';

                $intershipper_error = '';
                $intershipper_rates = array();
                foreach ($ship_reqs as $req)
                    ab_rate_estimate($ab_url, $ab_id, $ab_password, $debug, $req);
                $dhl_rates[$num] = func_array_merge($dhl_rates[$num], $intershipper_rates);
            }
        }
    } // foreach $packages

    $rates = func_intersect_rates($dhl_rates);

    $intershipper_rates = func_array_merge($first_intershipper_rates, func_normalize_shipping_rates($rates, 'ARB'));

    unset($first_intershipper_rates);
}

function ab_rate_estimate($ab_url, $ab_id, $ab_password, $debug, $req)
{
    global $mod_AB_faults, $intershipper_rates;

    $ab_request =<<<EOT
<?xml version='1.0'?>
<eCommerce action="Request" version="1.1">
    <Requestor>
        <ID>$ab_id</ID>
        <Password>$ab_password</Password>
    </Requestor>
    $req
</eCommerce>
EOT;

    $post = preg_split("/(\r\n|\r|\n)/",$ab_request, -1, PREG_SPLIT_NO_EMPTY);
    $md5_request = md5($ab_request);
    $is_cached = func_is_shipping_result_in_cache($md5_request);

    if (!$is_cached || $debug == 'Y') {
        $mod_AB_faults = array();
        list ($a, $ab_response) = func_https_request('POST', $ab_url, $post, '','','text/xml');

        $init_intershipper_rates = $intershipper_rates;
        ab_parse_response($ab_response);

        if ($debug == 'Y') {
            print "<h1>DHL/Airborne Debug Information</h1>";
            print "<h2>DHL/Airborne Request</h2>";
            print "<pre>".htmlspecialchars(func_arb_prepare_debug($ab_request))."</pre>";
            print "<h2>DHL/Airborne Response</h2>";
            print "<pre>".htmlspecialchars(func_arb_prepare_debug($ab_response))."</pre>";

        } else {
            func_save_shipping_result_to_cache($md5_request, $intershipper_rates, $init_intershipper_rates);
        }
        unset($init_intershipper_rates);

        if (!empty($mod_AB_faults)) {
            if ($debug == 'Y') ab_show_faults();

            ab_conv_faults();
        }
    } else {
        $intershipper_rates = func_get_shipping_result_from_cache($md5_request, $intershipper_rates);
    }
}

function ab_get_ship_key($ab_url, $ab_id, $ab_password, $ab_ship_accnum, $zipcode, $ab_testmode, $orig_address, $is_int = false, $debug = false)
{
    global $config, $sql_tbl;
    global $mod_AB_faults;
    global $mod_AB_shipkey;

    if ($is_int) {
        if (!empty($orig_address['ARB_shipping_key_intl']))
            return $orig_address['ARB_shipping_key_intl'];

        if (!empty($config['Shipping']['ARB_shipping_key_intl']))
            return $config['Shipping']['ARB_shipping_key_intl'];

        $request_type = 'IntlShipment';

    } else {
        if (!empty($orig_address['ARB_shipping_key']))
            return $orig_address['ARB_shipping_key'];

        if (!empty($config['Shipping']['ARB_shipping_key']))
            return $config['Shipping']['ARB_shipping_key'];

        $request_type = 'Shipment';

    }

    // Request new Shipping Key

    $request =<<<EOT
<?xml version='1.0'?>
<eCommerce action="Request" version="1.1">
    <Requestor>
        <ID>$ab_id</ID>
        <Password>$ab_password</Password>
    </Requestor>
    <Register action='ShippingKey' version='2.0' type='$request_type'>
        <AccountNbr>$ab_ship_accnum</AccountNbr>
        <PostalCode>$zipcode</PostalCode>
        <TransactionTrace>XC9f144f</TransactionTrace>
    </Register>
</eCommerce>
EOT;

    $post = preg_split("/(\r\n|\r|\n)/",$request, -1, PREG_SPLIT_NO_EMPTY);
    list ($a, $result) = func_https_request('POST', $ab_url, $post, '','','text/xml');

    if ($debug == 'Y') {
        print "<h1>DHL/Airborne Debug Information</h1>";
        print "<h2>DHL/Airborne Request for Shipping key</h2>";
        print "<pre>".htmlspecialchars(func_arb_prepare_debug($request))."</pre>";
        print "<h2>DHL/Airborne Response</h2>";
        print "<pre>".htmlspecialchars(func_arb_prepare_debug($result))."</pre>";
    }

    ab_parse_response($result);
    if (!empty($mod_AB_faults)) return '';

    $intl = ($is_int) ? '_intl' : '';

    if (!isset($orig_address['login'])) {
        $config['Shipping']["ARB_shipping_key$intl"] = $mod_AB_shipkey;
        db_query("UPDATE $sql_tbl[config] SET value='".addslashes($mod_AB_shipkey)."' WHERE name='ARB_shipping_key$intl'");
    } else {
        db_query("UPDATE $sql_tbl[seller_addresses] SET ARB_shipping_key$intl='".addslashes($mod_AB_shipkey)."' WHERE userid='$orig_address[userid]'");
    }

    return $mod_AB_shipkey;
}

function ab_show_faults()
{
    global $mod_AB_faults;

    if (empty($mod_AB_faults) || !is_array($mod_AB_faults))
        return;

    echo "<h1>DHL/Airborne request faults</h1>";
    $code = array();
    foreach ($mod_AB_faults as $fault) {
        if (isset($code[$fault['CODE']])) continue;

        echo $fault['DESC']." (Code=".$fault['CODE'].") <br />";
        $code[$fault['CODE']] = true;
    }
}

function ab_conv_faults()
{
    global $mod_AB_faults;
    global $intershipper_error, $shipping_calc_service;
    static $code = array();

    if (empty($mod_AB_faults) || !is_array($mod_AB_faults))
        return;

    $str = '';
    foreach ($mod_AB_faults as $fault) {
        if (isset($code[$fault['CODE']])) continue;

        $str .= $fault['DESC']." (Code=".$fault['CODE']."). ";
        $code[$fault['CODE']] = true;
    }

    if ($str != '') {
        $shipping_calc_service = 'DHL/Airborne';
        $intershipper_error .= $str;
    }
}

/**
 * Functions to parse XML-response
 */
function ab_parse_response($result)
{
    global $allowed_shipping_methods;
    global $intershipper_rates;
    global $mod_AB_ship_flags;
    global $mod_AB_shipkey;

    $parse_errors = false;
    $options = array(
        'XML_OPTION_CASE_FOLDING' => 1,
        'XML_OPTION_TARGET_ENCODING' => 'ISO-8859-1'
    );

    $parsed = func_xml_parse($result, $parse_errors, $options);

    $r = func_array_path($parsed, 'ECOMMERCE/REGISTER/SHIPPINGKEY/0/#');
    if ($r !== false) {
        $mod_AB_shipkey = $r;
    }

    ab_add_faults($parsed, 'ECOMMERCE/FAULTS/FAULT');
    ab_add_faults($parsed, 'ECOMMERCE/REGISTER/FAULTS/FAULT');

    $shipments =& func_array_path($parsed, 'ECOMMERCE/SHIPMENT');

    if (is_array($shipments)) {
        foreach ($shipments as $shipment) {
            ab_add_faults($shipment, 'FAULTS/FAULT');

            $mod_AB_SRVCODE = func_array_path($shipment,'ESTIMATEDETAIL/SERVICE/CODE/0/#');
            $mod_AB_SRVSUBCODE = '';

            $desc = func_array_path($shipment, 'ESTIMATEDETAIL/SERVICELEVELCOMMITMENT/DESC/0/#');
            if (!empty($desc) && $mod_AB_SRVCODE == 'E') {
                if (stristr($desc,'Saturday')!==false)
                    $mod_AB_SRVSUBCODE = 'SAT';
                elseif (strstr($desc,"10:30")!==false)
                    $mod_AB_SRVSUBCODE = '1030';
            }

            $rate = func_array_path($shipment, 'ESTIMATEDETAIL/RATEESTIMATE/TOTALCHARGEESTIMATE/0/#');
            if ($rate === false || (float)trim($rate) <= 0)
                continue;

            $shipping_time = func_array_path($shipment, 'ESTIMATEDETAIL/SERVICELEVELCOMMITMENT/DESC/0/#');

            foreach ($allowed_shipping_methods as $method) {
                if ($method['code'] != 'ARB' || empty($mod_AB_ship_flags[$method['subcode']]))
                    continue;

                $method_flags = $mod_AB_ship_flags[$method['subcode']];

                if ($method_flags['code'] == $mod_AB_SRVCODE && $method_flags['sub'] == $mod_AB_SRVSUBCODE) {
                    $current_rate = array (
                        'methodid' => $method['subcode'],
                        'rate' => trim($rate)
                    );
                    if ($shipping_time !== false)
                        $current_rate['shipping_time'] = $shipping_time;

                    $intershipper_rates[] = $current_rate;
                    break;
                }
            }
        }
    }
}

function ab_add_faults($parsed, $path)
{
    global $mod_AB_faults;

    $faults = func_array_path($parsed, $path);
    if (!is_array($faults))
        return;

    foreach ($faults as $fault) {
        if (in_array(func_array_path($fault,'CODE/0/#'), array('4112', '3083')))
            func_arb_register_holiday();

        $desc = func_array_path($fault,'DESC/0/#');
        if ($desc === false) {
            $code = func_array_path($fault,'CODE/0/#');
            $descr = func_array_path($fault,'DESC/0/#');
            if (empty($descr)) {
                $descr = func_array_path($fault,'DESCRIPTION/0/#');
            }
            $context = func_array_path($fault,'CONTEXT/0/#');
            $mod_AB_faults[] = array (
                'CODE' => $code,
                'DESC' => $descr,
                'CONTEXT' => $context
            );
        }
        else {
            $mod_AB_faults[] = array (
                'CODE' => func_array_path($fault,'CODE/0/#'),
                'DESC' => $desc,
                'SOURCE' => func_array_path($fault,'SOURCE/0/#')
            );
        }
    }
}

function ab_int_ratings($allowed_shipping_methods, $userinfo, $debug, $params, $orig_address, $packages)
{
    global $config, $sql_tbl, $intershipper_error, $intershipper_rates, $mod_AB_ship_flags, $dhl_ext_country_store, $current_arb_ship_date;

    if ($orig_address['country'] != 'US' || $userinfo['s_country'] == 'US')
        return array();

    x_load('xml','http');

    // Define transaction parameters
    $siteid = $params['id'];
    $pass = $params['password'];
    $skey = $params['skey'];
    $anumber = $params['account'];

    $sh_email = $config['Company']['orders_department'];
    $sh_address = htmlspecialchars($orig_address['address']);
    $sh_city = $orig_address['city'];
    $sh_state = $orig_address['state'];
    $sh_zipcode = $orig_address['zipcode'];
    $sh_country = $orig_address['country'];

    $rc_address = htmlspecialchars($userinfo['s_address']);
    $rc_address_2 = htmlspecialchars($userinfo['s_address_2']);
    $rc_city = $userinfo['s_city'];
    $rc_state = $userinfo['s_state'];
    $rc_zipcode = $userinfo['s_zipcode'];
    $rc_country = $userinfo['s_country'];
    $rc_phone = $userinfo['phone'];
    $rc_email = $userinfo['email'];

    $p_type = $params['package'];

    $c_name = htmlspecialchars($config['Company']['company_name']);
    $c_phone = $config['Company']['company_phone'];
    $c_fax = $config['Company']['company_fax'];

    // Correct United Kingdom code - UK
    if ($rc_country == 'GB')
        $rc_country = 'UK';

    if (!empty($dhl_ext_country_store))
        $rc_country = $dhl_ext_country_store;

    $first_intershipper_rates = $intershipper_rates;
    $dhl_rates = array();

    if (!empty($packages) && is_array($packages))
    foreach ($packages as $num => $pack) {

        $dhl_rates[$num] = array();
        $max_repeat = 5;
        $override_arb_request = true;

        while($override_arb_request && $max_repeat-- > 0) {
            $override_arb_request = false;

            $current_arb_ship_date = $ship_date = func_arb_get_ship_date($params['ttl']);

            // Define request header
            $post = <<<REQ
<?xml version='1.0'?><ECommerce action='Request' version='1.1'>
    <Requestor>
        <ID>$siteid</ID>
        <Password>$pass</Password>
    </Requestor>
REQ;

            if ($p_type == 'L')
                return;

            // Define request body
            $i = 0;

            $ship_weight = max(1, func_units_convert(func_weight_in_grams($pack['weight']), "g", "lbs", 0));
            $ship_weight_oz = func_units_convert(func_weight_in_grams($pack['weight']), "g", "oz", 0);
            $p_length = func_units_convert(func_dim_in_centimeters($pack['length']), 'cm', 'in');
            $p_width = func_units_convert(func_dim_in_centimeters($pack['width']), 'cm', 'in');
            $p_height = func_units_convert(func_dim_in_centimeters($pack['height']), 'cm', 'in');

            $dutiable = ($userinfo['s_country'] == "PR") ? "Y" : "N";

            foreach ($allowed_shipping_methods as $method) {
                if ($method['code'] != 'ARB' || ($params['weight'] > $method["weight_limit"] && $method["weight_limit"] > 0) || empty($method["subcode"]) || $method['destination'] == 'L' || !isset($mod_AB_ship_flags[$method["subcode"]]))
                    continue;

                $scode = $mod_AB_ship_flags[$method['subcode']]['code'];
                $i++;

                $dims = '';
                if ($p_type == 'P') {
                    $dims = <<<DIMS
        <Weight>$ship_weight</Weight>
        <Dimensions>
            <Length>$p_length</Length>
            <Width>$p_width</Width>
            <Height>$p_height</Height>
        </Dimensions>
DIMS;
                }

                $post .= <<<REQ
 <IntlShipment action = 'RateEstimate' version = '1.0'>
    <ShippingCredentials>
        <ShippingKey>$skey</ShippingKey>
        <AccountNbr>$anumber</AccountNbr>
    </ShippingCredentials>
    <ShipmentDetail>
        <ShipDate>$ship_date</ShipDate>
        <Service>
            <Code>$scode</Code>
        </Service>
        <ShipmentType>
            <Code>$p_type</Code>
        </ShipmentType>
        <ContentDesc>Big Box</ContentDesc>
        $dims
    </ShipmentDetail>
    <Dutiable>
        <DutiableFlag>$dutiable</DutiableFlag>
        <CustomsValue>1</CustomsValue>
    </Dutiable>
    <Billing>
        <Party>
            <Code>S</Code>
        </Party>
        <DutyPaymentType>S</DutyPaymentType>
    </Billing>
    <Sender>
        <Address>
            <CompanyName>$c_name</CompanyName>
            <Street>$sh_address</Street>
            <City>$sh_city</City>
            <State>$sh_state</State>
            <PostalCode>$sh_zipcode</PostalCode>
            <Country>$sh_country</Country>
        </Address>
        <PhoneNbr>$c_phone</PhoneNbr>
        <Email>$sh_email</Email>
    </Sender>
    <Receiver>
        <Address>
            <Street>$rc_address</Street>
            <StreetLine2>$rc_address_2</StreetLine2>
            <City>$rc_city</City>
            <PostalCode>$rc_zipcode</PostalCode>
            <State>$rc_state</State>
            <Country>$rc_country</Country>
        </Address>
        <PhoneNbr>$rc_phone</PhoneNbr>
        <Email>$rc_email</Email>
    </Receiver>
</IntlShipment>
REQ;

            }

            $post .= "</ECommerce>";

            if (empty($i))
                return array();

            $mod_AB_faults = array();

            // Request
            list($a, $result) = func_https_request('POST', $params['url'], array($post), "", "", "text/xml");

            $parse_errors = false;
            $options = array(
                'XML_OPTION_CASE_FOLDING' => 1,
                'XML_OPTION_TARGET_ENCODING' => 'ISO-8859-1'
            );

            $parsed = func_xml_parse($result, $parse_errors, $options);

            // Detect common errors
            $errors = func_array_path($parsed, 'ECOMMERCE/FAULT');

            if (!empty($errors)) {
                if ($debug == 'Y')
                    echo "<h1>DHL/Airborne request faults</h1>\n";

                foreach ($errors as $k => $v) {
                    $errors[$k] = func_array_path($v, "#/CODE/0/#").": ".func_array_path($v, "#/DESCRIPTION/0/#");
                    if ($debug == 'Y')
                        echo $errors[$k]."<br />\n";
                }
                $intershipper_error .= implode("\n", $errors);
                return array();
            }

            // Detect rates
            $methods = func_array_path($parsed, 'ECOMMERCE/INTLSHIPMENT');
            if (empty($methods))
                return array();

            if ($debug == 'Y') {
                print "<h1>DHL/Airborne Debug Information</h1>";
                print "<h2>DHL/Airborne Request</h2>";
                print "<pre>".htmlspecialchars(func_arb_prepare_debug($post))."</pre>";
                print "<h2>DHL/Airborne Response</h2>";
                print "<pre>".htmlspecialchars(func_arb_prepare_debug($result))."</pre>";
            }

            $intershipper_rates = array();

            foreach ($methods as $m) {

                // Detect rate error
                $errs = func_array_path($m, "#/FAULTS");
                if (!empty($errs)) {
                    $errors = array();

                    foreach ($errs as $e) {
                        $suberrors = func_array_path($e, "#/FAULT");
                        if (!empty($suberrors)) {
                            foreach($suberrors as $se) {
                                if (in_array(func_array_path($se, "#/CODE/0/#"), array('4112', '3083')))
                                    func_arb_register_holiday();

                                $errors[] = func_array_path($se, "#/CODE/0/#").": ".func_array_path($se, "#/DESC/0/#");
                            }
                        }
                    }

                    $intershipper_error .= implode("<br />\n", $errors);
                    continue;
                }

                // Detect rate
                $id = trim(func_array_path($m, "ESTIMATEDETAIL/SERVICE/CODE/0/#"));
                $rate = doubleval(trim(func_array_path($m, "ESTIMATEDETAIL/RATEESTIMATE/0/#/TOTALCHARGEESTIMATE/0/#")));

                // Save rate
                foreach ($allowed_shipping_methods as $method) {
                    if (
                        $method['code'] != 'ARB' ||
                        ($weight > $method['weight_limit'] && $method['weight_limit'] > 0) ||
                        $mod_AB_ship_flags[$method['subcode']]['code'] != $id ||
                        $method['destination'] == 'L'
                    )
                        continue;

                    $intershipper_rates[] = array(
                        'methodid' => $method['subcode'],
                        'rate' => $rate,
                        'shipping_time' => trim(func_array_path($m, "ESTIMATEDETAIL/SERVICELEVELCOMMITMENT/DESC/0/#")),
                    );

                }
            } // foreach $methods

            $dhl_rates[$num] = func_array_merge($dhl_rates[$num], $intershipper_rates);
        }
    } // foreach $packages

    $rates = func_intersect_rates($dhl_rates);

    $intershipper_rates = func_array_merge($first_intershipper_rates, $rates);
    unset($first_intershipper_rates);
}

/**
 * Format XML string for displaying in debug info
 */
function func_arb_prepare_debug($query)
{
    $query = preg_replace("|<ID>.*</ID>|iUS","<ID>xxx</ID>",$query);
    $query = preg_replace("|<Password>.*</Password>|iUS", "<Password>xxx</Password>", $query);
    $query = preg_replace("|<ShippingKey>.*</ShippingKey>|iUS", "<ShippingKey>xxx</ShippingKey>", $query);
    $query = preg_replace("|<AccountNbr>.*</AccountNbr>|iUS", "<AccountNbr>xxx</AccountNbr>", $query);

    x_load('xml');
    return func_xml_format($query);
}

/**
 * Get ship date
 */
function func_arb_get_ship_date($ttl)
{
    global $config;

    $date = XC_TIME+$ttl*86400+$config['Appearance']['timezone_offset'];
    if (date('w', $date) == '0')
        $date += 86400;

    if (!empty($config['arb_holidays'])) {
        if (!is_array($config['arb_holidays']))
            $config['arb_holidays'] = unserialize($config['arb_holidays']);

        if (!is_array($config['arb_holidays']))
            $config['arb_holidays'] = array();

        if (is_array($config['arb_holidays']) && !empty($config['arb_holidays'])) {
            while (in_array(date("m-d", $date), $config['arb_holidays']))
                $date += 86400;
        }
    }

    return date("Y-m-d", $date);
}

/**
 *  Register holiday
 */
function func_arb_register_holiday()
{
    global $current_arb_ship_date, $config, $sql_tbl, $override_arb_request;

    $key = preg_replace("/^([\d]{4}\-)([\d]{2}\-)([\d]{2})$/", "\\2\\3", $current_arb_ship_date);  // $current_arb_ship_date is a date in 'Y-m-d' format

    if (!empty($config['arb_holidays']) && !is_array($config['arb_holidays']))
        $config['arb_holidays'] = unserialize($config['arb_holidays']);

    if (!is_array($config['arb_holidays']))
        $config['arb_holidays'] = array();

    $config['arb_holidays'][] = $key;

    $config['arb_holidays'] = array_unique($config['arb_holidays']);

    if (count($config['arb_holidays']) > 20)
        array_shift($config['arb_holidays']);

    func_array2insert(
        'config',
        array(
            'name' => 'arb_holidays',
            'value' => addslashes(serialize($config['arb_holidays']))
        ),
        true
    );

    $override_arb_request = true;

    return true;
}

/**
 * Return package limits for DHL/Airborne
 */
function func_get_package_limits_ARB()
{
    global $sql_tbl;

    $params = func_query_first("SELECT * FROM $sql_tbl[shipping_options] WHERE carrier='ARB'");

    $limits = func_correct_dimensions(array('weight' => 149, 'width' => 30, 'height' => 30, 'length' => 30));

    foreach(array("length"=>'param02', 'width'=>'param03', 'height'=>'param04', 'weight'=>'param10') as $k => $p) {
        $limit = doubleval($params[$p]);
        if($limit>0) $limits[$k] = $limit;
    }

    return $limits;
}

/**
 * Check if DHL/Airborne allows box
 */
function func_check_limits_ARB($box)
{
    $package_limits = func_get_package_limits_ARB();
    $box['weight'] = (isset($box['weight'])) ? $box['weight'] : 0;

    return (func_check_box_dimensions($box, $package_limits) && $package_limits['weight'] > $box['weight']);
}

?>
