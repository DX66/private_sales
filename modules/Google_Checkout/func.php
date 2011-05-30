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
 * Google checkout
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.php,v 1.29.2.14 2011/04/08 09:41:10 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

x_load(
    'payment',
    'http',
    'xml'
);

/**
 * This function prepares string $str for including into the XML request
 */
function func_google_encode($str)
{

    if (empty($str))
        return $str;

    $str = str_replace(array("&", "<", ">", chr(163), chr(174)), array("&#x26;", "&#x3c;", "&#x3e;", "&#xA3;", "&#xAE;"), $str);

    if (function_exists('mb_detect_encoding')) {
        global $e_langs;
        global $default_charset;
        static $_charsets = false;

        // Define static $_charsets var at once
        if (empty($_charsets)) {
            if (in_array(strtoupper($default_charset), array('KOI8-R','CP866','WINDOWS-1251','CP1251')))
                $_charsets = 'RU';
            else {
                $_charsets = $e_langs;
                array_unshift($_charsets, 'UTF-8');
                $_charsets = array_values(array_unique($_charsets));
            }
        }

        if ($_charsets == 'RU') {
            #use predefined charset for russian codepages.
            $charset = $default_charset;
        } else {
            #http://bugs.php.net/bug.php?id=38138 mb_detect_encoding can return false in some cases
            $charset = mb_detect_encoding($str, $_charsets);
        }

        if ($charset == 'UTF-8')
            return $str;
        elseif (empty($charset))
            return mb_convert_encoding($str, "UTF-8", "ISO-8859-1");
        else
            return mb_convert_encoding($str, "UTF-8", $charset);
    }

    return utf8_encode($str);
}

/**
 * This function checks if Google callback is valid
 */
function func_gcheckout_is_valid_callback($ref)
{
    global $sql_tbl;

    $refid = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[cc_pp3_data] WHERE ref = '$ref'");
    $goid = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[gcheckout_orders] WHERE goid = '$ref'");

    if ( $refid == 0 && $goid == 0) {
        x_log_flag('log_payment_processing_errors', 'PAYMENTS', "Google checkout payment module: Script called with a wrong Google order id: '$ref'", true);
        func_gcheckout_debug("\t+ [Error] Google checkout payment module: Script called with a wrong Google order id: '$ref'");
        exit;
    }

    return true;
}

/**
 * This function adds a message to log
 */
function func_gcheckout_debug($message, $xml=false)
{
    global $gcheckout_global_log, $gcheckout_global_xml_log;
    global $gcheckout_log_detailed_data;

    if (!defined('GCHECKOUT_DEBUG') || empty($message))
        return true;

    if (!$xml)
        $gcheckout_global_log .= $message . "\n";
    elseif ($gcheckout_log_detailed_data)
        $gcheckout_global_xml_log .= $message . "\n";

    return true;
}

/**
 * Save log to file (this function registered on 'script shutdown' event)
 */
function func_gcheckout_save_log()
{
    global $gcheckout_global_log, $gcheckout_global_xml_log;
    global $gcheckout_log_detailed_data;

    if (!defined('GCHECKOUT_DEBUG'))
        return true;

    if (!empty($gcheckout_global_log)) {

        list($_usec, $_sec) = explode(" ", constant('XCART_START_TIME'));
        list($_usec2, $_sec2) = explode(" ", microtime());

        $gcheckout_global_log .= "\t+ Running time (in seconds): " . (($_usec2 + $_sec2) - ($_usec + $_sec)) . "\n";

        if (GCHECKOUT_DEBUG != 1) {
            // Preparing for sending to e-mail
            $emails_array = explode(',', GCHECKOUT_DEBUG);
            x_log_add('gcheckout', $gcheckout_global_log, false, 0, $emails_array, true);
        }
        else
            x_log_add('gcheckout', $gcheckout_global_log);
    }

    if (!empty($gcheckout_global_xml_log)) {
        x_log_add('gcheckout_xml', $gcheckout_global_xml_log);
    }

    return true;
}

/**
 * This function is used by func_google_sort_tax_rates() for ordering tax rates
 */
function func_google_sort_tax_rates($a, $b)
{
    if ($a['zone_rating'] == $b['zone_rating'])
        return 0;

    return ($a['zone_rating'] < $b['zone_rating'] ? 1 : -1);
}

/**
 * This function gathers the taxes, rates and rate zones details
 */
function func_gcheckout_get_taxes($cart)
{
    global $sql_tbl, $single_mode, $cart;
    global $user_account;

    $membershipid = intval($user_account['membershipid']);

    static $_zones_cache = array();
    static $result_cache = array();

    if (empty($cart['products']) || !is_array($cart['products']))
        return false;

    $products = $cart['products'];

    $md5_args = md5(serialize(array($products, $membershipid)));
    if (isset($result_cache[$md5_args])) {
        return $result_cache[$md5_args];
    }

    $productids = array();
    foreach ($products as $k => $p) {
        $productids[$p['productid']] = $p;
    }

    $_product_taxes = func_query_hash("SELECT $sql_tbl[taxes].*, $sql_tbl[product_taxes].productid FROM $sql_tbl[taxes], $sql_tbl[product_taxes], $sql_tbl[products] WHERE $sql_tbl[products].productid=$sql_tbl[product_taxes].productid AND $sql_tbl[taxes].taxid=$sql_tbl[product_taxes].taxid AND $sql_tbl[products].free_tax!='Y' AND $sql_tbl[product_taxes].productid IN ('".implode("','", array_keys($productids))."') AND $sql_tbl[taxes].active='Y' ORDER BY $sql_tbl[taxes].priority", "productid");

    if (empty($_product_taxes)) {
        $result_cache[$md5_args] = false;
        return false;
    }        

    $taxes = array();
    $have_tax_rates = false;

    // This rating is used for ordering of the rates within tax-rules container
    $zone_element_rating = array(
        'C' => 1,
        'S' => 1000,
        'G' => 2000,
        'Z' => 3000,
        'A' => 4000
    );

    // Gather the tax rates details
    foreach ($_product_taxes as $productid => $_taxes) {

        if (isset($taxes[$_taxes[0]['tax_name']]))
            continue;

        $taxes[$_taxes[0]['tax_name']] = $_taxes[0];
        $rates = func_query("SELECT $sql_tbl[tax_rates].* FROM $sql_tbl[tax_rates] LEFT JOIN $sql_tbl[tax_rate_memberships] ON $sql_tbl[tax_rates].rateid=$sql_tbl[tax_rate_memberships].rateid WHERE $sql_tbl[tax_rates].taxid='{$_taxes[0]['taxid']}' AND ($sql_tbl[tax_rate_memberships].membershipid = '$membershipid' OR $sql_tbl[tax_rate_memberships].membershipid IS NULL)");

        $_tax_rates = array();

        if (is_array($rates)) {

            $have_tax_rates = true;
            $_tax_rate_tmp = array();

            // Gather the rate zones details
            $_total_rates = count($rates);

            for ($i = 0; $i < $_total_rates; $i++) {

                $_zone = array();
                $_zones = array();
                
                $zone_cache_key = $rates[$i]['zoneid'];

                if (isset($_zones_cache[$zone_cache_key])) {
                    // Get zone details from cache
                    $_zones = $_zones_cache[$zone_cache_key];
                }
                else {
                    // Gather zone details for tax rate
                    $_zones_result = func_query("SELECT $sql_tbl[zone_element].* FROM $sql_tbl[zone_element] WHERE zoneid='$zone_cache_key'");

                    if (!empty($_zones_result))
                        foreach ($_zones_result as $_current_zone)
                            $_zone['zone'][$_current_zone['field_type']][] = $_current_zone['field'];

                    $multiple_rate = array();

                    if (!empty($_zone['zone']['Z']) && count($_zone['zone']['Z']) > 1)
                        $multiple_rate['Z'] = $_zone['zone']['Z'];
                    elseif (!empty($_zone['zone']['S']) && count($_zone['zone']['S']) > 1)
                        $multiple_rate['S'] = $_zone['zone']['S'];

                    if (!empty($multiple_rate)) {
                        foreach ($multiple_rate as $k=>$_mzones) {
                            $_zone_tmp = $_zone;
                            foreach ($_mzones as $_mzone) {
                                $_zone_tmp['zone'][$k] = array($_mzone);
                                $_zones[] = $_zone_tmp;
                            }
                        }
                    }
                    else
                        $_zones[] = $_zone;

                    foreach ($_zones as $k=>$_zone) {
                        if (empty($_zone)) continue;
                        $_zones[$k]['zone_rating'] = 0;
                        foreach ($_zone['zone'] as $_zone_type=>$_zone_arr)
                            $_zones[$k]['zone_rating'] += $zone_element_rating[$_zone_type] * count($_zone_arr);
                    }

                    $_zones_cache[$zone_cache_key] = $_zones;

                }

                $_tmp_rate = array();
                $_tmp_rate = $rates[$i];

                foreach ($_zones as $_zone) {
                    $_tmp_rate['zone'] = @$_zone['zone'];
                    $_tmp_rate['zone_rating'] = @$_zone['zone_rating'];
                    $taxes[$_taxes[0]['tax_name']]['rates'][] = $_tmp_rate;
                }

            } // for ($i = 0; $i < $_total_rates; $i++)

            usort($taxes[$_taxes[0]['tax_name']]['rates'], 'func_google_sort_tax_rates');
        }
    }

    if (!$have_tax_rates) {
        $result_cache[$md5_args] = false;
        return false;
    }        

    if (!$single_mode) {
        $taxes_pro = array();
        foreach ($taxes as $_tax_name => $_tax) {
            $_rates_tmp = array();
            foreach ($_tax['rates'] as $_rate)
                $_rates_tmp[$_tax_name.'_'.$_rate['provider']][] = $_rate;
            $taxes_pro[$_tax_name.'_'.$_rate['provider']] = $_tax;
            $taxes_pro[$_tax_name.'_'.$_rate['provider']]['rates'] = $_rates_tmp;
        }
        $taxes = $taxes_pro;
    }

    $result_cache[$md5_args] = $taxes;
    return $taxes;

}

/**
 * This function checks if Google Checkout button must be enabled or disabled
 */
function func_is_gcheckout_button_enabled ()
{
    global $sql_tbl, $cart;

    $_restriction_found = 0;

    if (doubleval($cart['total_cost']) == 0)
        return false;

    if (!empty($cart['products']) && is_array($cart['products'])) {
        $_pid = array();
        foreach ($cart['products'] as $_product) {
            $_pid[] = $_product['productid'];
        }
        $_restriction_found = func_query_hash("SELECT productid, COUNT(*) as counter FROM $sql_tbl[gcheckout_restrictions] WHERE productid IN ('" . implode("','", $_pid) . "') GROUP BY productid", "productid", false);
        if (!empty($_restriction_found) && is_array($_restriction_found)) {
            foreach ($_restriction_found as $pid => $counter) {
                foreach ($cart['products'] as $k => $_product) {
                    if ($_product['productid'] == $pid)
                        $cart['products'][$k]['valid_for_gcheckout'] = 'N';
                }
            }
        }
    }

    return empty($_restriction_found);

}

/**
 * This function prepares and displays the 'notification-acknowledgment' XML code
 */
function func_gcheckout_send_notification_acknowledgment()
{
    $notification_acknowledgment_xml = <<<OUT
<?xml version="1.0" encoding="UTF-8"?>
<notification-acknowledgment xmlns="http://checkout.google.com/schema/2"/>
OUT;

    func_gcheckout_debug($notification_acknowledgment_xml, true);

    echo $notification_acknowledgment_xml;
}

/**
 * This function sends XML code to the Google Checkout server and parsed an answer
 */
function func_gcheckout_send_xml($xml)
{
    global $config, $gcheckout_xml_url;

    func_gcheckout_debug("*** URL:\n\n" . $gcheckout_xml_url . "\n\n", true);
    func_gcheckout_debug("*** XML REQUEST:\n\n" . $xml . "\n\n", true);

    $h = array(
        'Authorization' => "Basic " . base64_encode($config['Google_Checkout']['gcheckout_mid'] . ":" . $config['Google_Checkout']['gcheckout_mkey']),
        'Accept'         => 'application/xml'
    );

    x_load('http', 'xml');

    // Send XML request and parse result

    list($a, $return) = func_https_request('POST', $gcheckout_xml_url, array($xml), '', '', 'application/xml', '', '', '', $h);

    func_gcheckout_debug("*** RESPONSE HEADERS:\n\n" . $a, true);
    func_gcheckout_debug("*** RESPONSE:\n\n" . $return, true);

    $parse_error = false;
    $options = array(
        'XML_OPTION_CASE_FOLDING' => 1,
        'XML_OPTION_TARGET_ENCODING' => 'ISO-8859-1'
    );

    $parsed = func_xml_parse($return, $parse_error, $options);

    return $parsed;
}

/**
 * This function checks if there is any active google calculated shipping and changes 'top_message' facility
 */
function func_gcheckout_check_shipping()
{
    global $sql_tbl, $permanent_warning, $config;

    // Check active shipping if Google Checkout shipping calculation and Shipping system are active.

    if ($config['Google_Checkout']['gcheckout_use_gc_shipping'] != 'Y' || $config['Shipping']['enable_shipping'] != 'Y')
        return;

    $gc_shipping = func_query_first_cell("SELECT gc_shipping FROM $sql_tbl[shipping] WHERE active='Y' AND gc_shipping!=''");

    if ($gc_shipping == '')
        $permanent_warning[] = "<br />".func_get_langvar_by_name('lbl_gcheckout_no_shipping_methods_warning');
}

/**
 * This function checks if there is any active free shipping coupons and changes 'top_message' facility
 */
function func_gcheckout_check_coupons()
{
    global $active_modules, $sql_tbl, $logged_userid, $single_mode, $permanent_warning, $config, $current_area;

    // Check active shipping if Google Checkout shipping calculation and Shipping system are active.

    if ($config['Google_Checkout']['gcheckout_use_gc_shipping'] != 'Y' || $config['Shipping']['enable_shipping'] != 'Y' || empty($active_modules['Discount_Coupons']))
        return;

    $provider_condition = ($single_mode || $current_area == 'A') ? '' : "AND provider='$logged_userid'";

    $free_shipping_coupons = func_query_first_cell("SELECT coupon, (expire + ".doubleval($config['Appearance']['timezone_offset']).") as expire FROM $sql_tbl[discount_coupons] WHERE coupon_type='free_ship' AND times_used<times AND status='A' AND expire>'".XC_TIME."' ".$provider_condition."");

    if ($free_shipping_coupons != '')
        $permanent_warning[] = "<br />".func_get_langvar_by_name('lbl_gcheckout_no_free_shipping_coupons_warning');
}

/**
 * This function collects active google calculated shipping list
 */
function func_gcheckout_get_shipping()
{
    global $sql_tbl;

    $gc_shipping = func_query("SELECT gc_shipping, IF(code='FDX', 'FedEx', code) as code FROM $sql_tbl[shipping] WHERE active='Y' AND gc_shipping!=''");

    return $gc_shipping;
}

/**
 * This function returns shippingid for google shipping name
 */
function func_gcheckout_get_shippingid($shipping_name)
{
    global $sql_tbl;

    $shippingid = func_query_first_cell("SELECT shippingid, CONCAT(IF(code='FDX', 'FedEx', code), ' ', gc_shipping) as gc FROM $sql_tbl[shipping] HAVING gc='$shipping_name'");
    return $shippingid;
}

/**
 * This function is performing some checking to disable Google Checkout
 */
function func_gcheckout_check_enable(&$smarty)
{
    global $cart, $config, $sql_tbl, $xcart_dir, $payment_methods;

    if (!defined('SHIPPING_SYSTEM')) {
        x_load('shipping');
    }

    $gc_shipping = func_gcheckout_get_shipping();
    if ($config['Google_Checkout']['gcheckout_use_gc_shipping'] != 'Y' || empty($gc_shipping) || empty($cart['products']))
        return;

    $weight = func_units_convert(func_weight_in_grams(func_weight_shipping_products ($cart['products'])), 'g', 'lbs');
    $usps_active = func_query_first_cell("SELECT shippingid FROM $sql_tbl[shipping] WHERE active='Y' AND gc_shipping!='' AND code='USPS'");
    $max_weight = empty($usps_active) ? 70 : 150;

    if ($weight > $max_weight) {
        $smarty->assign('gcheckout_enabled', false);
        if (empty($payment_methods))
            $smarty->assign('top_message', array('type' => 'W', 'content' => func_get_langvar_by_name('lbl_gcheckout_max_weight_warning')));
    }

    $top_message = @$smarty->get_template_vars('top_message');
    if ($smarty->get_template_vars('gcheckout_enabled') === false && empty($payment_methods) && (empty($top_message) || in_array(@$top_message['type'], array('E', 'W')))) {
        if (empty($top_message))
            $top_message = array('type' => 'W', 'content' => '');

        $smarty->assign('top_message', array('type' => $top_message['type'], 'content' => func_get_langvar_by_name('lbl_gcheckout_disabled') . $top_message['content']));
    }
}

function func_gcheckout_wait_for_orders_from_callback($skey, $current_module = 'Google_Checkout', $wait_time=3)
{
    global $gcheckout_jump_counter, $current_location, $smarty;
    
    x_session_register('gcheckout_jump_counter', 0);

    if (++$gcheckout_jump_counter < 10) {
        // There are no orders found
        $smarty->assign('time', $wait_time);

        if ($current_module == 'Google_Checkout')
            $smarty->assign('url', $current_location."/payment/ps_gcheckout_return.php?mode=continue&amp;skey=$skey");
        else            
            $smarty->assign('url', $current_location."/payment/ps_amazon.php?mode=continue&amp;skey=$skey");

        x_session_save();
        func_display('modules/Google_Checkout/waiting.tpl', $smarty);
        exit;
    }   
    $gcheckout_jump_counter = 0;

    return true;
}

/**
 * Check if Google Checkout can be used
 */
function func_is_gcheckout_enabled()
{
    global $gcheckout_enabled;
    return isset($gcheckout_enabled) ? $gcheckout_enabled : false;
}

/**
 * Attention! Must be called from global scope
 */
function func_gcheckout_restore_session_n_global($skey)
{
    global $sql_tbl, $user_account, $products;

    $sessid = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref = '$skey'");

    x_session_id($sessid);
    x_session_register('login');#nolint
    x_session_register('login_type');#nolint
    x_session_register('logged_userid');#nolint
    x_session_register('cart');#nolint
    x_session_register('user_tmp', array());#nolint
    x_session_register('current_carrier', 'UPS');#nolint

    // Do not initialize session vars from global. Use session values
    global $login, $login_type, $logged_userid, $cart, $user_tmp, $current_carrier;
   
    if (!empty($logged_userid)) {
        $user_account['membershipid'] = func_query_first_cell("SELECT membershipid FROM $sql_tbl[customers] WHERE id='$logged_userid'");
    }

    $products = func_products_in_cart($cart, (!empty($user_account['membershipid']) ? $user_account['membershipid'] : ''));

    return !empty($cart);
}

?>
