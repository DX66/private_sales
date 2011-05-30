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
 * Functions to grab shipping methods with calculated rates (intershipper)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: myshipper.php,v 1.63.2.1 2011/01/10 13:12:10 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

include_once $xcart_dir.'/shipping/shipping_cache.php';

/**
 * This function calculates shipping rates from my own shipper module
 */
function func_shipper ($items, $userinfo, $orig_address, $debug="N", $cart=false)
{
    global $allowed_shipping_methods,$intershipper_rates;
    global $shipping_calc_service, $intershipper_error;
    global $sql_tbl;
    global $config;
    global $active_modules;
    global $xcart_dir;
    global $current_carrier;
    global $empty_other_carriers;
    global $empty_ups_carrier;

    $empty_other_carriers = 'N';

    if (
        empty($userinfo)
        && (
            $config['General']['apply_default_country'] == 'Y'
            || $debug == 'Y'
        )
    ) {
        $userinfo['s_country']     = $config['General']['default_country'];
        $userinfo['s_state']     = $config['General']['default_state'];
        $userinfo['s_zipcode']     = $config['General']['default_zipcode'];
        $userinfo['s_city']     = $config['General']['default_city'];

    } elseif (empty($userinfo)) {

        return array();

    }

    $allowed_shipping_methods = func_query("SELECT * FROM $sql_tbl[shipping] WHERE active = 'Y'");

    $intershipper_rates = array();

    $ups_rates_only = (!empty($active_modules['UPS_OnLine_Tools']) && $current_carrier == 'UPS');

    $ship_mods = array();
    $alt_ship_mods = array();

    if ($ups_rates_only) {

        $alt_ship_mods[] = 'AP';

    } else {

        $ship_mods[] = 'AP';

    }

    x_load('tests');

    // Shipping modules depend on XML parser (EXPAT extension)

    $mods = array(
        'USPS',
        'CPC',
        'ARB',
        'FEDEX',
    );

    if (test_expat() != '') {

        if ($ups_rates_only) {

            $ship_mods[] = 'UPS';
            $alt_ship_mods = func_array_merge($alt_ship_mods, $mods);

        } else {

            $ship_mods = func_array_merge($ship_mods, $mods);

        }

    }

    if (defined('ALL_CARRIERS')) {
        $ship_mods = func_array_merge($ship_mods, $alt_ship_mods);
        $ship_mods = array_unique($ship_mods);
        $alt_ship_mods = array();
    }

    foreach ($ship_mods as $ship_mod) {

        if (file_exists($xcart_dir . '/shipping/mod_' . $ship_mod . '.php'))
            include_once $xcart_dir . '/shipping/mod_' . $ship_mod . '.php';

        $func_ship = 'func_shipper_' . $ship_mod;

        if (function_exists($func_ship))
            $func_ship($items, $userinfo, $orig_address, $debug, $cart);

    }

    if ($ups_rates_only) {

        $tmp_rates = $intershipper_rates;
        $intershipper_rates = array();

        foreach ($alt_ship_mods as $alt_ship_mod) {

            if (file_exists($xcart_dir.'/shipping/mod_'.$alt_ship_mod.'.php'))
                include_once $xcart_dir.'/shipping/mod_'.$alt_ship_mod.'.php';

            $func_ship = 'func_shipper_'.$alt_ship_mod;
            if (function_exists($func_ship))
                $func_ship($items, $userinfo, $orig_address, $debug, $cart);

        }

        if (empty($intershipper_rates)) {
            $empty_other_carriers = 'Y';
        }

        $intershipper_rates = $tmp_rates;

    } elseif (
        !empty($active_modules['UPS_OnLine_Tools'])
        && $current_carrier != 'UPS'
    ) {
        $tmp_rates = $intershipper_rates;
        $intershipper_rates = array();
        $alt_ship_mods[] = 'UPS';

        foreach ($alt_ship_mods as $alt_ship_mod) {
            if (file_exists($xcart_dir.'/shipping/mod_'.$alt_ship_mod.'.php'))
                include_once $xcart_dir.'/shipping/mod_'.$alt_ship_mod.'.php';

            $func_ship = 'func_shipper_'.$alt_ship_mod;

            if (function_exists($func_ship))
                $func_ship($items, $userinfo, $orig_address, $debug, $cart);
        }

        if (empty($intershipper_rates)) {
            $empty_ups_carrier = 'Y';
        }

        $intershipper_rates = $tmp_rates;

    }

    if ($debug == 'Y') {

        func_shipper_show_rates($intershipper_rates);

    }

    return $intershipper_rates;
}

/**
 * Function normalizes shipping rates
 *
 * @param array  $rates   intershipper rates array
 * @param string $carrier carrier code
 *
 * @return array
 * @see    ____func_see____
 * @since  1.0.0
 */
function func_normalize_shipping_rates($rates, $carrier)
{
    global $sql_tbl;

    static $currencyRates = null;

    if (is_null($currencyRates)) {

        $currencyRates = func_query_hash('SELECT currency_rate, carrier FROM ' . $sql_tbl['shipping_options'], 'carrier', false);

    }

    $currencyRate = $currencyRates[$carrier]['currency_rate'];

    $currencyRate = (0 >= $currencyRate) ? 1 : $currencyRate;

    foreach ($rates as $index => $value) {

        $rates[$index]['rate'] *= $currencyRate;

    }

    return $rates;
}

?>
