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
 * This script provide admin the general information about online store
 * and allows him to ckear redundant data from the database
 * (this needs to be performed before shop go to live)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: general.php,v 1.114.2.3 2011/03/15 11:59:05 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './auth.php';
require $xcart_dir.'/include/security.php';

x_load('files','http','tests');

$anchors = array(
    'License'        => 'lbl_license_info',
    'General'        => 'lbl_general_info',
    'PaymentMethods' => 'lbl_payments_methods_info',
    'Environment'    => 'lbl_environment_info',
);

foreach ($anchors as $anchor=>$anchor_label) {

    $dialog_tools_data['left'][] = array(
        'link'  => "#" . $anchor,
        'title' => func_get_langvar_by_name($anchor_label),
    );

}

if (@$_GET['mode'] == 'phpinfo') {

    phpinfo();

    exit;

}

if (@$_GET['mode'] == 'perlinfo') {

    echo test_perl(true);

    exit;

}

require $xcart_dir . '/include/install.php';

x_session_register('previous_login_date');

$https_modules = array(
    'ssleay'     => "Net::SSLeay",
    'libcurl'     => 'libCURL',
    'curl'         => "CURL executable",
    'openssl'     => "OpenSSL executable",
);

if (
    !empty($mode)
    && $mode == 'test_https_module'
) {

    $url = isset($url) ? trim(stripslashes($url)) : '';

    if (!zerolen($url)) {

        list($headers, $data) = func_https_request('GET', $url);

        $smarty->assign('headers_data', $headers);
        $smarty->assign('response_data', $data);

    }

    $smarty->assign('url', $url);
    $smarty->assign('template_name', 'admin/main/test_https_module.tpl');

    $active_bouncer = test_active_bouncer();

    $smarty->assign('popup_title', func_get_langvar_by_name('lbl_test_of_https_module', array('name' => $https_modules[$active_bouncer])));

    func_display('help/popup_info.tpl',$smarty);

    exit;
}

$location[] = array(func_get_langvar_by_name('lbl_summary'), '');

/**
 * Function to get OS type
 */
function test_os()
{
    list($os_type, $tmp) = explode(" ", php_uname());

    return $os_type;
}

function test_dirs_rights()
{
    global $xcart_dir, $var_dirs, $all_languages, $smarty_skin_dir;

    $directories = array(
        $xcart_dir . '/files',
        $xcart_dir . '/catalog',
        $var_dirs['templates_c'],
        $var_dirs['log'],
        $var_dirs['tmp'],
        $var_dirs['upgrade'],
        $var_dirs['cache'],
        $xcart_dir . $smarty_skin_dir,
        $xcart_dir . $smarty_skin_dir . '/pages',
    );

    if (!empty($all_languages) && is_array($all_languages)) {

        foreach($all_languages as $v) {

            $directories[] = $xcart_dir . $smarty_skin_dir . '/pages/' . $v['code'];

        }

    }

    sort($directories);

    $rc = '';

    $root_dir = func_normalize_path($xcart_dir);

    foreach ($directories as $dir) {

        $testdir = func_normalize_path($dir);

        if (func_pathcmp($root_dir . XC_DS, $testdir, 1)) {

            $testdir = substr($testdir, strlen($root_dir) + 1);

        }

        $rc[] = array (
            'directory'     => $testdir,
            'exists'         => is_dir($dir),
            'writable'         => is_writable($dir),
        );
    }

    return $rc;
}

function test_webserver()
{

    if (isset($_SERVER['SERVER_SOFTWARE'])) {
        return $_SERVER['SERVER_SOFTWARE'];
    }

    return '';
}

function test_gd()
{

    if (
        extension_loaded('gd')
        && function_exists('gd_info')
    ) {

        $gd_config = gd_info();

        return $gd_config['GD Version'];

    }

}

/**
 * This function collects knowledge about X-Cart environment
 */
function test_environment()
{
    global $version, $xcart_dir;
    global $https_modules;

    $env = array();

    $env[] = array(
        'item'         => func_get_langvar_by_name('lbl_env_software_version'),
        'data'         => $version,
        'default'     => 'unknown',
    );

    $env[] = array(
        'item'         => func_get_langvar_by_name('lbl_env_software_directory'),
        'data'         => $xcart_dir,
        'default'     => 'unknown',
    );

    $env[] = array(
        'item'         => 'PHP',
        'data'         => phpversion(),
        'details'     => "javascript: window.open('general.php?mode=phpinfo','phpinfo')",
        'default'     => 'unknown',
    );

    $env[] = array(
        'item'         => 'GD',
        'default'     => test_gd(),
    );

    $env[] = array(
        'item'         => func_get_langvar_by_name('lbl_env_mysql_server'),
        'default'     => mysql_get_server_info(),
    );

    $env[] = array(
        'item' => func_get_langvar_by_name('lbl_env_mysql_client'),
        'data' => mysql_get_client_info()
    );

    $tables = func_query('SHOW TABLE STATUS');
    $dbsize = 0;

    foreach ($tables as $v) {
        $dbsize += $v['Data_length'] + $v['Index_length'];
    }

    if ($dbsize > 0) {

        $env[] = array(
            'item' => func_get_langvar_by_name('lbl_env_dbsize'),
            'data' => round($dbsize / 1024 / 1024, 3) . 'Mb',
        );

    }

    $env[] = array(
        'item'         => func_get_langvar_by_name('lbl_env_web_server'),
        'data'         => test_webserver(),
        'default'     => 'unknown',
    );

    $env[] = array(
        'item'         => func_get_langvar_by_name('lbl_env_os'),
        'data'         => test_os(),
        'default'     => 'unknown',
    );

    $env[] = array(
        'item'         => 'Perl',
        'data'         => test_perl(),
        'details'     => "javascript: window.open('general.php?mode=perlinfo','perlinfo')",
    );

    $env[] = array(
        'item'         => func_get_langvar_by_name('lbl_env_xml_parser'),
        'data'         => test_expat(),
        'warning'     => true,
    );

    if (@$_GET['estimate_dir_size'] == 'Y') {
        $xcart_dir_status = func_get_dir_status($xcart_dir, true);
        $_data = ($xcart_dir_status['is_large'] ? func_get_langvar_by_name('lbl_more_than')." " : '').$xcart_dir_status['size'];
    } else {
        $_data = "<a href='general.php?estimate_dir_size=Y#Environment'>" . func_get_langvar_by_name('lbl_estimate_dir_size') . "</a>";
    }

    $env[] = array(
        'item' => func_get_langvar_by_name('lbl_env_xcartsize'),
        'data' => $_data,
    );

    // HTTPS modules
    $env[] = array(
        'row_txt' => func_get_langvar_by_name('lbl_https_modules'),
    );

    $details_txt = func_get_langvar_by_name('lbl_active');

    $details = "javascript: window.open('general.php?mode=test_https_module','HTTPSTEST','width=600,height=460,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no');";

    $active_bouncer = test_active_bouncer();

    foreach ($https_modules as $_bouncer => $_bouncer_name) {

        $_test_func = "test_$_bouncer";

        $_bouncer_details = array(
            'item'             => $_bouncer_name,
            'data'             => $_test_func(),
            'details_txt'     => '',
            'details'         => '',
        );

        if ($_bouncer == $active_bouncer) {

            $_bouncer_details['details_txt']     = $details_txt;
            $_bouncer_details['details']         = $details;

        }

        $env[] = $_bouncer_details;

    }

    foreach($env as $idx => $item) {

        if (
            @$item['item'] == 'Perl'
            && empty($item['data'])
        ) {
            $env[$idx]['details'] = ''; // Do not show link if Perl not found
        }

        if (!isset($item['default'])) {
            $env[$idx]['default'] = func_get_langvar_by_name('lbl_not_found');
        }

    }

    return $env;
}

/**
 * Get the orders info
 */
$curtime = XC_TIME + $config['Appearance']['timezone_offset'];

$start_dates[] = $previous_login_date;  // Since last login

$start_dates[] = func_prepare_search_date($curtime) - $config['Appearance']['timezone_offset']; // Today

$start_week = $curtime - (date('w',$curtime))*24*3600; // Week starts since Sunday

$start_dates[] = func_prepare_search_date($start_week) - $config['Appearance']['timezone_offset']; // Current week

$start_dates[] = mktime(0,0,0,date('m',$curtime),1,date('Y',$curtime))-$config['Appearance']['timezone_offset']; // Current month

$curtime = XC_TIME;

foreach($start_dates as $start_date) {

    $date_condition = "AND date>='$start_date' AND date<='$curtime'";

    $orders['P'][] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[orders] WHERE status='P' $date_condition");
    $orders['F'][] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[orders] WHERE (status='F' OR status='D') $date_condition");
    $orders['I'][] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[orders] WHERE status='I' $date_condition");
    $orders['Q'][] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[orders] WHERE status='Q' $date_condition");

}

/**
 * Get the shipping methods info
 */
$shipping_methods_count = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[shipping] WHERE active='Y'");
$shipping_mod_enabled = func_query("SELECT code, COUNT(*) as count FROM $sql_tbl[shipping] WHERE active='Y' GROUP BY code ORDER BY code");

if (
    !empty($active_modules['UPS_OnLine_Tools'])
    && $config['Shipping']['use_intershipper'] != 'Y'
) {

    $condition = '';
    $ups_only = true;

    include $xcart_dir.'/modules/UPS_OnLine_Tools/ups_shipping_methods.php';

    $ups_shipping_methods_count = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[shipping] WHERE active='Y' $condition");

    for ($i = 0; $i < count($shipping_mod_enabled); $i++) {

        if ($shipping_mod_enabled[$i]['code'] == 'UPS') {

            $shipping_methods_count -= ($shipping_mod_enabled[$i]['count'] - $ups_shipping_methods_count);
            $shipping_mod_enabled[$i]['count'] = $ups_shipping_methods_count;

            break;

        }

    }

}

/**
 * Get the shipping rates info
 */
$shipping_rates_count = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[shipping_rates]");

$shipping_rates_enabled = func_query("SELECT $sql_tbl[shipping].code, COUNT(*) as count FROM $sql_tbl[shipping], $sql_tbl[shipping_rates] WHERE $sql_tbl[shipping].shippingid=$sql_tbl[shipping_rates].shippingid GROUP BY $sql_tbl[shipping].code ORDER BY $sql_tbl[shipping].code");

/**
 * Get the X-Cart version
 */
$version = func_query_first_cell("SELECT value FROM $sql_tbl[config] WHERE name='version'");

/**
 * Get the countries info
 */
$enable_country = func_query_first_cell("SELECT code FROM $sql_tbl[countries] WHERE active='Y'");

/**
 * Get the products critical properties
 */
$empty_prices = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products], $sql_tbl[quick_prices], $sql_tbl[pricing] WHERE $sql_tbl[products].productid = $sql_tbl[quick_prices].productid AND $sql_tbl[pricing].priceid = $sql_tbl[quick_prices].priceid AND $sql_tbl[pricing].price = '0.00' AND $sql_tbl[products].product_type != 'C'");

/**
 * Testing payment methods and CC processors
 */
$payment_methods = func_query("SELECT $sql_tbl[payment_methods].*, $sql_tbl[ccprocessors].module_name, $sql_tbl[ccprocessors].processor, $sql_tbl[ccprocessors].type FROM $sql_tbl[payment_methods] LEFT JOIN $sql_tbl[ccprocessors] ON $sql_tbl[payment_methods].paymentid = $sql_tbl[ccprocessors].paymentid WHERE $sql_tbl[payment_methods].active = 'Y' ORDER BY $sql_tbl[payment_methods].orderby");

$payment_methods = test_payment_methods($payment_methods);

if (!empty($active_modules['Google_Checkout'])) {

    $payment_methods[] = array(
        'payment_method'     => "Google Checkout",
        'payment_script'     => 'ps_gcheckout.php',
        'protocol'             => 'https',
        'active'             => 'Y',
        'module_name'         => "Google Checkout",
        'processor'         => 'ps_gcheckout.php',
        'type'                 => 'P',
        'is_down'             => !func_is_gcheckout_enabled(),
        'in_testmode'         => $config['Google_Checkout']['gcheckout_test_mode'] == 'Y'
    );

}

if (!empty($active_modules['Amazon_Checkout'])) {

    $payment_methods[] = array(
        'payment_method'     => "Checkout by Amazon",
        'payment_script'     => 'ps_amazon.php',
        'protocol'             => 'https',
        'active'             => 'Y',
        'module_name'         => "Amazon Checkout",
        'processor'         => 'ps_gcheckout.php',
        'type'                 => 'P',
        'is_down'             => !func_is_acheckout_enabled(),
        'in_testmode'         => $config['Amazon_Checkout']['amazon_test_mode'] == 'Y'
    );

}

if ($config['active_subscriptions_processor']) {

    $active_sb_params = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor='$config[active_subscriptions_processor]'");

    $active_sb = test_ccprocessor($active_sb_params);

} else {

    $active_sb = array(
        'status' => 1
    );

}

// Get report about missing Clean URLs.
$missing_clean_urls_stats = func_clean_url_get_missing_urls_stats();

if (
    !empty($missing_clean_urls_stats) 
    && is_array($dialog_tools_data['left'])
) {
    // A link to the Clean URLs section added to 'In this section' menu
    $new_item = array(array(
        'link'  => "#CleanUrls",
        'title' => func_get_langvar_by_name('lbl_clean_urls_info')
    ));
    array_splice($dialog_tools_data['left'], 2, 0, $new_item);

}

/**
 * Set up the smarty templates variables
 */
$smarty->assign('single_mode',                 $single_mode);
$smarty->assign('orders',                     $orders);
$smarty->assign('shipping_methods_count',     $shipping_methods_count);
$smarty->assign('shipping_mod_enabled',     $shipping_mod_enabled);
$smarty->assign('shipping_rates_count',     $shipping_rates_count);
$smarty->assign('shipping_rates_enabled',     $shipping_rates_enabled);
$smarty->assign('empty_prices',             $empty_prices);
$smarty->assign('test_dirs_rights',         test_dirs_rights());
$smarty->assign('environment_info',         test_environment());
$smarty->assign('payment_methods',            $payment_methods);
$smarty->assign('active_sb',                 $active_sb);
$smarty->assign('active_sb_params',         @$active_sb_params);
$smarty->assign('auth_code',                 $installation_auth_code);
$smarty->assign('enable_country',             $enable_country);
$smarty->assign('missing_clean_urls_stats', $missing_clean_urls_stats);

$smarty->assign('main',                        'general_info');

// Assign the current location line
$smarty->assign('location',                 $location);

// Assign the section navigation data
$smarty->assign('dialog_tools_data',         $dialog_tools_data);

if (
    file_exists($xcart_dir . '/modules/gold_display.php')
    && is_readable($xcart_dir . '/modules/gold_display.php')
) {
    include $xcart_dir . '/modules/gold_display.php';
}
func_display('admin/home.tpl', $smarty);
?>
