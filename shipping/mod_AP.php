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
 * Australia Post shipping library
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: mod_AP.php,v 1.35.2.1 2011/01/10 13:12:09 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('http');

function func_shipper_AP($items, $userinfo, $orig_address, $debug, $cart)
{
    global $sql_tbl;
    global $allowed_shipping_methods, $intershipper_rates;

    if ($orig_address['country'] != 'AU' || !is_array($allowed_shipping_methods) || empty($allowed_shipping_methods)) {
        return false;
    }

    $APOST_FOUND = false;
    if (is_array($allowed_shipping_methods)) {
        foreach ($allowed_shipping_methods as $value) {
            if ($value['code'] == 'APOST') {
                $APOST_FOUND = true;
                break;
            }
        }
    }

    if (!$APOST_FOUND)
        return;

    $stypes = array(
        1001 => 'STANDARD',
        1002 => 'EXPRESS',
        1003 => 'AIR',
        1005 => 'SEA',
        1006 => 'ECI_D',
        1007 => 'ECI_M',
        1008 => 'EPI'
    );

    $ap_host = 'drc.edeliver.com.au';
    $ap_url = '/ratecalc.asp';

    $options = func_query_first("SELECT * FROM $sql_tbl[shipping_options] WHERE carrier = 'APOST'");

    $specified_dims = array();
    foreach(array('length'=>'param00', 'width'=>'param01', 'height'=>'param02') as $k => $o) {
        $dim = doubleval($options[$o]);
        if($dim>0) $specified_dims[$k] = $dim;
    }

    $zipcode = preg_replace("|[^\d\w]|i",'',$userinfo['s_zipcode']);

    $package_limit = func_get_package_limits_AP();

    $_post = "Pickup_Postcode=".$orig_address['zipcode'] .
        "&Destination_Postcode=".$zipcode .
        "&Country=".$userinfo['s_country'];

    if ($debug == 'Y') {

        // Display debug info (header)
        print "<h1>Australia Post Debug Information</h1>";
        $is_display_debug = false;
    }

    $packages = func_get_packages($items, $package_limit, ($options['param03']== 'Y')? 100 : 1);

    if ((count($packages) > 1) && $options['param03'] != 'Y')
        return array();

    $ap_rates = array();

    $first_intershipper_rates = $intershipper_rates;

    if (!empty($packages) && is_array($packages))
    foreach ($packages as $num => $pack) {

        if($options['param05']=="Y")
            $pack = func_array_merge($pack, $specified_dims);

        $ap_rates[$num] = array();
        $intershipper_rates = array();

        foreach (array('height', 'width', 'length') as $f)
                $pack[$f] = max(50, func_dim_in_centimeters($pack[$f]) * 10);

        foreach ($allowed_shipping_methods as $value) {

            if ($value['code'] != 'APOST' || !isset($stypes[$value['service_code']]))
                continue;

            if (($userinfo['s_country'] != 'AU' && $value['destination'] == "L") || ($userinfo['s_country'] == 'AU' && $value['destination'] == "I")) {
                continue;
            }

            $post = $_post.
            "&Weight=".ceil(func_weight_in_grams($pack['weight'])) .
            "&Length=".ceil($pack['length']).
            "&Width=".ceil($pack['width']) .
            "&Height=".ceil($pack['height']) .
            "&Quantity=1";

            $md5_request = md5($post.$stypes[$value['service_code']]);

            if ((!func_is_shipping_result_in_cache($md5_request)) ||  ($debug == 'Y')){

                list ($header, $result) = func_http_get_request ($ap_host, $ap_url, $post."&Service_type=".$stypes[$value['service_code']]);

                if (empty($result))
                    continue;

                $return = array();

                if (preg_match_all("/^([^=]+)=(.*)$/Sm", $result, $preg)) {
                    foreach($preg[1] as $k => $v) {
                        $return[$v] = trim($preg[2][$k]);
                    }
                }

                if ($return['err_msg'] == "OK") {
                    $intershipper_rates[] = array(
                        'methodid' => $value['subcode'],
                        'rate' => $return['charge'],
                        'shipping_time' => $return['days']
                    );
                    $cached_value = array(
                        'methodid' => $value['subcode'],
                        'rate' => $return['charge'],
                        'shipping_time' => $return['days']
                    );

                    if ($debug != 'Y')
                        func_save_shipping_result_to_cache($md5_request, $cached_value);
                }

            } else {

                $intershipper_rates[] = func_get_shipping_result_from_cache($md5_request);

            }

            if ($debug == 'Y') {

                // Display debug info
                print "<h2>Australia Post Request</h2>";
                print "<pre>".htmlspecialchars($post."&Service_type=".$stypes[$value['service_code']])."</pre>";
                print "<h2>Australia Post Response</h2>";
                print "<pre>".htmlspecialchars($result)."</pre>";
                $is_display_debug = true;
            }

        } // foreach $allowed_shipping_methods

        $ap_rates[$num] = $intershipper_rates;

    } // foreach $packages

    $intershipper_rates = func_array_merge(
        $first_intershipper_rates,
        func_normalize_shipping_rates(
            func_intersect_rates($ap_rates),
            'APOST'
        )
    );

    unset($first_intershipper_rates);

    if ($debug == 'Y' && !$is_display_debug) {
        print "It seems, you have forgotten to fill in an Australia Post account information.";
    }

}

/**
 * Return package limits for Australian POST
 */
function func_get_package_limits_AP()
{
    global $sql_tbl, $config;

    $options = func_query_first("SELECT * FROM $sql_tbl[shipping_options] WHERE carrier = 'APOST'");

    $limits = array();

    $limits['weight'] = doubleval($options['param04']);
    if($limits['weight']<=0) {
        // default weight limit is 20 kg
        $limits['weight'] = 20 * 1000 / $config['General']['weight_symbol_grams'];
    }

    $dims_specified = true;

    $dim_params = array('length'=>'param00', 'width'=>'param01', 'height'=>'param02');
    foreach($dim_params as $k => $v) {
        $limits[$k] = doubleval($options[$v]);
        if($limits[$k]<=0) {
            // default dimensions limit is 105 cm
            $limits[$k] = 105 / $config['General']['dimensions_symbol_cm'];
            $dims_specified = false;
        }
    }

    // set girth limit if any of the dimension limits are not specified

    if(!$dims_specified) {
        // default girth limit is 140 cm
        $limits['girth'] = 140 / $config['General']['dimensions_symbol_cm'];
    }

    return $limits;
}

/**
 * Check if Australian POST allows box
 */
function func_check_limits_AP($box)
{
    $package_limits = func_get_package_limits_AP();
    $box['weight'] = (isset($box['weight'])) ? $box['weight'] : 0;

    return (func_check_box_dimensions($box, $package_limits) && $package_limits['weight'] > $box['weight']);
}
?>
