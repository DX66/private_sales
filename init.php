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
 * X-Cart initialization
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: init.php,v 1.179.2.21 2011/05/03 08:31:30 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: index.php"); die("Access denied"); }

require_once $xcart_dir . '/prepare.php';
set_include_path($xcart_dir . XC_DS . 'include' .XC_DS. 'lib' .XC_DS. 'PEAR');
require_once $xcart_dir . '/include/func/func.core.php';

x_load(
    'db',
    'files',
    'compat',
    'gd',
    'clean_urls',
    'memcache'
);

func_set_memory_limit('32M');

/**
 * Allow displaying content in functions, registered in register_shutdown_function()
 */
$zlib_oc = ini_get('zlib.output_compression');

if (
    !empty($zlib_oc)
    || version_compare(phpversion(), '4.0.6') <= 0
) {
    define('NO_RSFUNCTION', true);
}

unset($zlib_oc);

if (version_compare(phpversion(), '5.0.0') >= 0) {
    define('X_PHP5x_COMPAT', true);
}

if (
    function_exists('date_default_timezone_get')
    && function_exists('date_default_timezone_set')
) {
    @date_default_timezone_set(@date_default_timezone_get());
}

if (version_compare(phpversion(), '5.3.0') >= 0) {

    define('X_PHP530_COMPAT', true);

}

if (!@is_readable($xcart_dir . '/config.php')) {

    func_show_error_page("Cannot read config!");

}

require_once $xcart_dir . '/config.php';

if (is_readable($xcart_dir . '/config.local.php')) {
    include_once $xcart_dir . '/config.local.php';
}

/**
 * This directive defines if some secured information would be
 * shown on the WEB (file system structure, MySQL internal error)
 * Currently it depends on $debug_mode value.
 */
$display_critical_errors = in_array($debug_mode, array(1, 3));

/**
 * HTTP & HTTPS locations
 */
$http_location    = 'http://' . $xcart_http_host . $xcart_web_dir;
$https_location   = 'https://' . $xcart_https_host . $xcart_web_dir;

$current_location = $HTTPS ? $https_location : $http_location;

if (
    (
        !isset($is_install_preview)
        || $is_install_preview != 'Y'
    )
    && !defined('XCART_EXT_ENV')
    && (
        empty($sql_host)
        || $sql_host == '%SQL_HOST%'
        || empty($sql_user)
        || $sql_user == '%SQL_USER%'
        || empty($sql_db)
        || $sql_db == '%SQL_DB%'
        || $sql_password == '%SQL_PASSWORD%'
    )
) {

    $message = "X-Cart software cannot connect to the MySQL database because your MySQL account information is missing from X-Cart's configuration file config.php.";

    $install_script = $xcart_dir . XC_DS . 'install.php';

    $install_script = (is_readable($install_script))
        ? func_get_xcart_home() . '/install.php'
        : false;

    $extra_info = "<p>This may be caused by that X-Cart installation has not been carried out or the file config.php has been edited in a wrong way. ";

    if ($install_script) {
        $extra_info .= "If you think X-Cart installation has not been performed or has not been completed properly, use the link below to run X-Cart's installation script.";
    }

    $extra_info .= "</p>";

    $extra_info .= "<p>If the installation process has been completed, but you are getting this message, the problem is likely caused by incorrect information in your config.php file. Check the file config.php and make sure the SQL database details settings in it are correct.</p>";

    if (false !== $install_script) {

        $extra_info .= "<p><a href='$install_script'>Run the installation script</a></p>";

    }

    func_show_error_page("Cannot connect to the database", $message, $extra_info);
}

$file_temp_dir = $var_dirs['tmp'];

/**
 * SQL tables aliases...
 */

// WARNING!!!
// Do not change the table name prefix in $sql_tbl!
// Otherwise you will not be able to upgrade and reinstall the software.
$sql_tbl = array (
    'address_book'                      => 'xcart_address_book',
    'amazon_data'                       => 'xcart_amazon_data',
    'amazon_orders'                     => 'xcart_amazon_orders',
    'delayed_queries'                   => 'xcart_delayed_queries',
    'benchmark_pages'                   => 'xcart_benchmark_pages',
    'categories'                        => 'xcart_categories',
    'categories_subcount'               => 'xcart_categories_subcount',
    'categories_lng'                    => 'xcart_categories_lng',
    'category_bookmarks'                => 'xcart_category_bookmarks',
    'category_memberships'              => 'xcart_category_memberships',
    'cc_gestpay_data'                   => 'xcart_cc_gestpay_data',
    'cc_pp3_data'                       => 'xcart_cc_pp3_data',
    'ccprocessors'                      => 'xcart_ccprocessors',
    'change_password'                   => 'xcart_change_password',
    'clean_urls'                        => 'xcart_clean_urls',
    'clean_urls_history'                => 'xcart_clean_urls_history',
    'config'                            => 'xcart_config',
    'contact_fields'                    => 'xcart_contact_fields',
    'counties'                          => 'xcart_counties',
    'countries'                         => 'xcart_countries',
    'country_currencies'                => 'xcart_country_currencies',
    'currencies'                        => 'xcart_currencies',
    'customers'                         => 'xcart_customers',
    'delivery'                          => 'xcart_delivery',
    'discount_coupons'                  => 'xcart_discount_coupons',
    'discount_coupons_login'            => 'xcart_discount_coupons_login',
    'discounts'                         => 'xcart_discounts',
    'discount_memberships'              => 'xcart_discount_memberships',
    'download_keys'                     => 'xcart_download_keys',
    'export_ranges'                     => 'xcart_export_ranges',
    'extra_fields'                      => 'xcart_extra_fields',
    'extra_fields_lng'                  => 'xcart_extra_fields_lng',
    'extra_field_values'                => 'xcart_extra_field_values',
    'featured_products'                 => 'xcart_featured_products',
    'form_ids'                          => 'xcart_form_ids',
    'gcheckout_orders'                  => 'xcart_gcheckout_orders',
    'gcheckout_restrictions'            => 'xcart_gcheckout_restrictions',
    'ge_products'                       => 'xcart_ge_products',
    'giftcerts'                         => 'xcart_giftcerts',
    'images_G'                          => 'xcart_images_G',
    'images_T'                          => 'xcart_images_T',
    'images_P'                          => 'xcart_images_P',
    'images_D'                          => 'xcart_images_D',
    'images_C'                          => 'xcart_images_C',
    'images_M'                          => 'xcart_images_M',
    'import_cache'                      => 'xcart_import_cache',
    'iterations'                        => 'xcart_iterations',
    'language_codes'                    => 'xcart_language_codes',
    'languages'                         => 'xcart_languages',
    'languages_alt'                     => 'xcart_languages_alt',
    'login_history'                     => 'xcart_login_history',
    'manufacturers'                     => 'xcart_manufacturers',
    'manufacturers_lng'                 => 'xcart_manufacturers_lng',
    'memberships'                       => 'xcart_memberships',
    'memberships_lng'                   => 'xcart_memberships_lng',
    'modules'                           => 'xcart_modules',
    'newsletter'                        => 'xcart_newsletter',
    'newslist_subscription'             => 'xcart_newslist_subscription',
    'newslists'                         => 'xcart_newslists',
    'old_passwords'                     => 'xcart_old_passwords',
    'order_details'                     => 'xcart_order_details',
    'order_extras'                      => 'xcart_order_extras',
    'orders'                            => 'xcart_orders',
    'packages_cache'                    => 'xcart_packages_cache',
    'pages'                             => 'xcart_pages',
    'payment_methods'                   => 'xcart_payment_methods',
    'pmethod_memberships'               => 'xcart_pmethod_memberships',
    'pricing'                           => 'xcart_pricing',
    'product_bookmarks'                 => 'xcart_product_bookmarks',
    'product_links'                     => 'xcart_product_links',
    'product_memberships'               => 'xcart_product_memberships',
    'product_reviews'                   => 'xcart_product_reviews',
    'product_rnd_keys'                  => 'xcart_product_rnd_keys',
    'product_taxes'                     => 'xcart_product_taxes',
    'product_votes'                     => 'xcart_product_votes',
    'products'                          => 'xcart_products',
    'products_categories'               => 'xcart_products_categories',
    'products_lng'                      => 'xcart_products_lng',
    'provider_product_commissions'      => 'xcart_provider_product_commissions',
    'provider_commissions'              => 'xcart_provider_commissions',
    'quick_flags'                       => 'xcart_quick_flags',
    'quick_prices'                      => 'xcart_quick_prices',
    'referers'                          => 'xcart_referers',
    'register_fields'                   => 'xcart_register_fields',
    'register_field_values'             => 'xcart_register_field_values',
    'secure3d_data'                     => 'xcart_secure3d_data',
    'seller_addresses'                  => 'xcart_seller_addresses',
    'session_history'                   => 'xcart_session_history',
    'sessions_data'                     => 'xcart_sessions_data',
    'session_unknown_sid'               => 'xcart_session_unknown_sid',
    'setup_images'                      => 'xcart_setup_images',
    'shipping'                          => 'xcart_shipping',
    'shipping_cache'                    => 'xcart_shipping_cache',
    'shipping_labels'                   => 'xcart_shipping_labels',
    'shipping_options'                  => 'xcart_shipping_options',
    'shipping_rates'                    => 'xcart_shipping_rates',
    'split_checkout'                    => 'xcart_split_checkout',
    'states'                            => 'xcart_states',
    'stats_adaptive'                    => 'xcart_stats_adaptive',
    'stats_cart_funnel'                 => 'xcart_stats_cart_funnel',
    'stats_customers_products'          => 'xcart_stats_customers_products',
    'stats_pages'                       => 'xcart_stats_pages',
    'stats_pages_paths'                 => 'xcart_stats_pages_paths',
    'stats_pages_views'                 => 'xcart_stats_pages_views',
    'stats_search'                      => 'xcart_stats_search',
    'stats_shop'                        => 'xcart_stats_shop',
    'tax_rate_memberships'              => 'xcart_tax_rate_memberships',
    'tax_rates'                         => 'xcart_tax_rates',
    'taxes'                             => 'xcart_taxes',
    'temporary_data'                    => 'xcart_temporary_data',
    'titles'                            => 'xcart_titles',
    'wishlist'                          => 'xcart_wishlist',
    'users_online'                      => 'xcart_users_online',
    'zone_element'                      => 'xcart_zone_element',
    'zones'                             => 'xcart_zones',
);

/**
 * Redefine error_reporting option
 */
if (
    defined('X_PHP530_COMPAT')
    && $x_error_reporting != -1
) {

    $x_error_reporting = $x_error_reporting & ~(E_DEPRECATED | E_USER_DEPRECATED);

}

error_reporting($x_error_reporting);

/**
 * Fix broken path for some hostings
 */
$_tmp = @parse_url($current_location);

$xcart_web_dir = empty($_tmp['path']) ? '' : $_tmp['path'];

if ($HTTPS_RELAY) {

    // Fix wrong PHP_SELF for HTTPS relay
    $_tmp = @parse_url($http_location);

    $PHP_SELF = empty($_tmp['path'])
        ? $xcart_web_dir . $PHP_SELF
        : $xcart_web_dir . preg_replace("/^" . preg_quote($_tmp['path'], "/")."/", "", $PHP_SELF);

    $_SERVER['PHP_SELF'] = $PHP_SELF;

    $xcart_web_dir = preg_replace("/\/[\w\d_-]+\.[\w\d]+$/", '', $PHP_SELF);

    $for_replace = false;

    switch(AREA_TYPE) {

        case 'C':
            $for_replace = DIR_CUSTOMER;
            break;

        case 'A':
            $for_replace = DIR_ADMIN;
            break;

        case 'P':
            $for_replace = DIR_PROVIDER;
            break;

        case 'B':
            $for_replace = DIR_PARTNER;
            break;
    }

    if (false !== $for_replace) {

        $xcart_web_dir = preg_replace('/' . preg_quote($for_replace, '/') . "$/", '', $xcart_web_dir);

    }
}

$_tmp = @parse_url($https_location);
$xcart_https_host = $_tmp['host'];
unset($_tmp);

$_tmp = @parse_url($http_location);
$xcart_http_host = $_tmp['host'];
unset($_tmp);

/**
 * Create URL
 */
$request_uri_info = @parse_url($REQUEST_URI);
$php_url = array(
    'url'     => 'http'
        . (
            $HTTPS
            ? 's://'
                . $xcart_https_host
            : '://'
                . $xcart_http_host
        )
        . (
            !zerolen($request_uri_info['path'])
                ? $request_uri_info['path']
                : $PHP_SELF
        ),
    'query_string' => $QUERY_STRING,
);

/**
 * Check internal temporary directories
 */
$var_dirs_rules = array (
    'cache' => array (
        '.htaccess' => "<FilesMatch \"\\.(css|js)$\">\nAllow from all\n</FilesMatch>\n"
    )
);

foreach ($var_dirs as $k => $v) {

    if (
        !file_exists($v)
        || !is_dir($v)
    ) {
        @unlink($v);
        func_mkdir($v);
    }

    if (
        !is_writable($v)
        || !is_dir($v)
    ) {
        $dir_info = $display_critical_errors ? $v : '';
        func_show_error_page("Cannot write data to the temporary directory $dir_info", "Please check if it exists, and has writable permissions.");
    }

    if (
        !empty($var_dirs_rules[$k])
        && is_array($var_dirs_rules[$k])
    ) {
        foreach ($var_dirs_rules[$k] as $f => $c) {
            if (file_exists($v . '/' . $f))
                continue;

            if ($__fp = @fopen($v . '/' . $f, 'w')) {
                @fwrite($__fp, $c);
                @fclose($__fp);
                func_chmod_file($v . '/' . $f, 0644);
            }
        }
    }
}

if (!file_exists($xcart_dir . '/var/.htaccess')) {

    if ($fp = @fopen($xcart_dir . '/var/.htaccess', 'w')) {

        @fwrite($fp, "Order Deny,Allow\nDeny from all\n");

        @fclose($fp);

        func_chmod_file($xcart_dir . '/var/.htaccess', 0644);

    }

}

/**
 * Initialize logging
 */
require_once $xcart_dir . '/include/logging.php';

/**
 * Include functions
 */
include_once($xcart_dir . '/include/bench.php');

/**
 * Connect to database
 */

$mysql_error_count = 0;

db_connection($sql_host, $sql_user, $sql_db, $sql_password);

/**
 * Read config variables from Database
 * These variables are used inside php scripts, not in smarty templates
 */

global $memcache;

$get_config = true;

if ($memcache) {

    $config = func_get_mcache_data('inner_config');

    $get_config = false === $config;

    register_shutdown_function('func_remove_mcache_config');
}

if ($get_config) {

    $c_result = db_query("SELECT name, value, category FROM $sql_tbl[config] WHERE type != 'separator'");

    $config = array();

    if ($c_result) {

        while ($row = db_fetch_row($c_result)) {

            if (!empty($row[2])) {

                if ('XCART_INNER_EVENTS' !== $row[2]) {

                    $config[$row[2]][$row[0]] = $row[1];

                }

            } else {

                $config[$row[0]] = $row[1];

            }

        }

    }

    db_free_result($c_result);

    if ($memcache) {

        func_store_mcache_data('inner_config', $config);

    }

}

/*
 * Check PHP ini since last launch and write changes to log file
*/
if ($config['General']['skip_log_phpini_changes'] != 'Y') {
    func_check_phpini_changes();
}    

/**
 * Initialize alt_skin feature
 */
require_once $xcart_dir . '/include/alt_skin.php';

/**
 * Create Smarty object
 */

if (!include $xcart_dir . '/smarty.php') {
    func_show_error_page("Cannot launch template engine!", '');
}

$smarty->assign('alt_skin_info',  $alt_skin_info);
$smarty->assign('alt_skins_info', $altSkinsInfo);

/**
 * Init miscellaneous vars
 */
$smarty     ->assign('skin_config',      $skin_config_file);
$mail_smarty->assign('skin_config',      $skin_config_file);
$smarty     ->assign('http_location',    $http_location);
$mail_smarty->assign('http_location',    $http_location);
$smarty     ->assign('https_location',   $https_location);
$mail_smarty->assign('https_location',   $https_location);
$smarty     ->assign('xcart_web_dir',    $xcart_web_dir);
$smarty     ->assign('current_location', $current_location);
$smarty     ->assign('php_url',          $php_url);

foreach ($var_dirs_web as $k => $v) {

    $var_dirs_web[$k] = $current_location . $v;

}

$smarty->assign_by_ref('var_dirs_web', $var_dirs_web);

$xcart_catalogs = array (
    'admin'    => $current_location . DIR_ADMIN,
    'customer' => $current_location . DIR_CUSTOMER,
    'provider' => $current_location . DIR_PROVIDER,
    'partner'  => $current_location . DIR_PARTNER,
);

$xcart_catalogs_insecure = array (
    'admin'    => $http_location . DIR_ADMIN,
    'customer' => $http_location . DIR_CUSTOMER,
    'provider' => $http_location . DIR_PROVIDER,
    'partner'  => $http_location . DIR_PARTNER,
);

$xcart_catalogs_secure = array (
    'admin'    => $https_location . DIR_ADMIN,
    'customer' => $https_location . DIR_CUSTOMER,
    'provider' => $https_location . DIR_PROVIDER,
    'partner'  => $https_location . DIR_PARTNER,
);

$smarty      ->assign('catalogs',        $xcart_catalogs);
$smarty      ->assign('catalogs_secure', $xcart_catalogs_secure);
$mail_smarty ->assign('catalogs',        $xcart_catalogs);
$mail_smarty ->assign('catalogs_secure', $xcart_catalogs_secure);

/**
 * Files directories
 */
$files_dir_name      = $xcart_dir . $files_dir;
$files_http_location = $http_location . $files_webdir;

$smarty->assign('files_location', $files_dir_name);

$templates_repository = $xcart_dir . $templates_repository_dir;

/**
 * Include data cache functionality
 */
$_mysql_version = preg_match("/^(\d+\.\d+\.\d+)/", mysql_get_server_info(), $match); 
if ($_mysql_version) {
    define('X_MYSQL_VERSION', $match[1]);
}

include_once($xcart_dir . '/include/data_cache.php');

$sql_vars = func_data_cache_get('sql_vars');

$sql_max_allowed_packet = intval($sql_vars['max_allowed_packet']);

if ($_mysql_version) {

    if (version_compare(X_MYSQL_VERSION, '5.0.0') >= 0)
        db_query("SET sql_mode = 'MYSQL40'");

    if (version_compare(X_MYSQL_VERSION, '5.0.17') > 0)
        define('X_MYSQL5_COMP_MODE', true);

    if (version_compare(X_MYSQL_VERSION, '5.0.18') == 0)
        define('X_MYSQL5018_COMP_MODE', true);

    if (version_compare(X_MYSQL_VERSION, '4.1.0') >= 0)
        define('X_MYSQL41_COMP_MODE', true);
}
unset($_mysql_version);

if (is_numeric($sql_vars['lower_case_table_names'])) {
    define('X_MYSQL_LOWER_CASE_TABLE_NAMES', intval($sql_vars['lower_case_table_names']));
}

$md5_check_devlicense = '726e5429de89a8afb5fe2ed1040fb852';

/**
 * Set MySQL variable 'max_join_size'
 */
if (intval($sql_vars['max_join_size']) < 1073741824) {
    db_query("SET OPTION SQL_MAX_JOIN_SIZE=1073741824");
}

/**
 * Retrive registration information from database
 */
$shop_evaluation = func_is_evaluation();

$smarty->assign('shop_evaluation', $shop_evaluation);

/**
 * Schema to test .htaccess file if some configuration variables are on.
 */
$schemaTestHtaccess = array(
    array(
        'config' => array(
            'SEO',
            'clean_urls_enabled',
        ),
        'htaccessWord' => 'dispatcher.php [L]',
    ),
);

$htaccessWarning = array();

foreach ($schemaTestHtaccess as $schemaUnit) {
    if (
        'Y' == $config[$schemaUnit['config'][0]][$schemaUnit['config'][1]]
        && !func_test_htaccess($schemaUnit['htaccessWord'])
    ) {
        if (
            defined('AREA_TYPE')
            && 'C' == constant('AREA_TYPE')
        ) {

            $config[$schemaUnit['config'][0]][$schemaUnit['config'][1]] = 'N';

        } else {

            $htaccessWarning[$schemaUnit['config'][0]] = "Y";

        }
    }
}

$smarty->assign('htaccess_warning', $htaccessWarning);

$config['Sessions']['session_length'] = $use_session_length;

/**
 * Timezone offset (sec) = N hours x 60 minutes x 60 seconds
 */
$config['Appearance']['timezone_offset'] = intval($config['Appearance']['timezone_offset'] * 3600);

/**
 * Define 'End year' for date selectors in the templates
 */
$config['Company']['end_year'] = func_date('Y', XC_TIME + $config['Appearance']['timezone_offset']);

/**
 * Last database backup date
 */
if (!empty($config['db_backup_date']))
    $config['db_backup_date'] += $config['Appearance']['timezone_offset'];

$config['available_images']['T'] = "U";
$config['available_images']['P'] = "U";
$config['available_images']['C'] = "U";
$config['available_images']['G'] = "U";

$config['substitute_images']['P'] = "T";

$httpsmod_active = NULL;

if (!defined('QUICK_START')) {

    if (empty($config['Appearance']['thumbnail_width']))
        $config['Appearance']['thumbnail_width'] = 0;

    if (empty($config['Appearance']['date_format']))
        $config['Appearance']['date_format'] = "%d-%m-%Y";

    $config['Appearance']['datetime_format'] =
        $config['Appearance']['date_format'] . " " . $config['Appearance']['time_format'];

}

$config['Appearance']['thumbnail_width'] = intval($config['Appearance']['thumbnail_width']);

/**
 * Prepare session
 */
include_once $xcart_dir . '/include/sessions.php';

include_once $xcart_dir . '/include/unallowed_request.php';

// Search engine bots & spiders identificator
if (is_readable($xcart_dir . '/include/bots.php')) {
    require_once $xcart_dir . '/include/bots.php';
}

if (!defined('QUICK_START')) {

    include_once($xcart_dir . '/include/blowfish.php');

    // Start Blowfish class
    $blowfish = new ctBlowfish();
}

/**
 * Prepare number variables
 */
include_once $xcart_dir . '/include/number_conv.php';

if (!defined('QUICK_START')) {

    /**
     * Define default user profile fields
     */
    $default_user_profile_fields = array(
        'title'         => array(
            'avail'     => array('A' => 'N', 'P' => 'N', 'B' => 'N', 'C' => 'N', 'H' => 'N'),
            'required'  => array('A' => 'N', 'P' => 'N', 'B' => 'N', 'C' => 'N', 'H' => 'N')
        ),
        'firstname'     => array(
            'avail'     => array('A' => 'Y', 'P' => 'Y', 'B' => 'Y', 'C' => 'Y', 'H' => 'N'),
            'required'  => array('A' => 'Y', 'P' => 'Y', 'B' => 'Y', 'C' => 'Y', 'H' => 'N')
        ),
        'lastname'      => array(
            'avail'     => array('A' => 'Y', 'P' => 'Y', 'B' => 'Y', 'C' => 'Y', 'H' => 'N'),
            'required'  => array('A' => 'Y', 'P' => 'Y', 'B' => 'Y', 'C' => 'Y', 'H' => 'N')
        ),
        'company'       => array(
            'avail'     => array('A' => 'Y', 'P' => 'Y', 'B' => 'Y', 'C' => 'Y', 'H' => 'N'),
            'required'  => array('A' => 'N', 'P' => 'N', 'B' => 'N', 'C' => 'N', 'H' => 'N')
        ),
        'url'           => array(
            'avail'     => array('A' => 'Y', 'P' => 'Y', 'B' => 'Y', 'C' => 'Y', 'H' => 'N'),
            'required'  => array('A' => 'N', 'P' => 'N', 'B' => 'N', 'C' => 'N', 'H' => 'N')
        ),
        'ssn'           => array (
            'avail'     => array('A' => 'N', 'P' => 'N', 'B' => 'Y', 'C' => 'N' ,'H' => 'N'),
            'required'  => array('A' => 'N', 'P' => 'N', 'B' => 'Y', 'C' => 'N', 'H' => 'N')
        ),
        'tax_number'    => array (
            'avail'     => array('A' => 'N', 'P' => 'N', 'B' => 'Y', 'C' => 'Y' ,'H' => 'N'),
            'required'  => array('A' => 'N', 'P' => 'N', 'B' => 'Y', 'C' => 'N', 'H' => 'N')
        )
    );

    /**
     * Define default address book fields
     */
    $default_address_book_fields = array(
        'title' => array (
            'avail'     => array('A' => 'N', 'P' => 'N', 'B' => 'N', 'C' => 'N', 'H' => 'N'),
            'required'  => array('A' => 'N', 'P' => 'N', 'B' => 'N', 'C' => 'N', 'H' => 'N')
        ),
        'firstname'     => array(
            'avail'     => array('A' => 'N', 'P' => 'N', 'B' => 'N', 'C' => 'Y', 'H' => 'Y'),
            'required'  => array('A' => 'N', 'P' => 'N', 'B' => 'N', 'C' => 'Y', 'H' => 'Y')
        ),
        'lastname'      => array(
            'avail'     => array('A' => 'N', 'P' => 'N', 'B' => 'N', 'C' => 'Y', 'H' => 'Y'),
            'required'  => array('A' => 'N', 'P' => 'N', 'B' => 'N', 'C' => 'Y', 'H' => 'Y')
        ),
        'address'       => array(
            'avail'     => array('A' => 'N', 'P' => 'N', 'B' => 'N', 'C' => 'Y', 'H' => 'Y'),
            'required'  => array('A' => 'N', 'P' => 'N', 'B' => 'N', 'C' => 'Y', 'H' => 'Y')
        ),
        'address_2'     => array(
            'avail'     => array('A' => 'N', 'P' => 'N', 'B' => 'N', 'C' => 'Y', 'H' => 'Y'),
            'required'  => array('A' => 'N', 'P' => 'N', 'B' => 'N', 'C' => 'N', 'H' => 'N')
        ),
        'city'          => array(
            'avail'     => array('A' => 'N', 'P' => 'N', 'B' => 'N', 'C' => 'Y', 'H' => 'Y'),
            'required'  => array('A' => 'N', 'P' => 'N', 'B' => 'N', 'C' => 'Y', 'H' => 'Y')
        ),
        'county'        => array(
            'avail'     => array('A' => 'N', 'P' => 'N', 'B' => 'N', 'C' => 'N', 'H' => 'N'),
            'required'  => array('A' => 'N', 'P' => 'N', 'B' => 'N', 'C' => 'N', 'H' => 'N')
        ),
        'state'         => array(
            'avail'     => array('A' => 'N', 'P' => 'N', 'B' => 'N', 'C' => 'Y', 'H' => 'Y'),
            'required'  => array('A' => 'N', 'P' => 'N', 'B' => 'N', 'C' => 'Y', 'H' => 'Y')
        ),
        'country'       => array(
            'avail'     => array('A' => 'N', 'P' => 'N', 'B' => 'N', 'C' => 'Y', 'H' => 'Y'),
            'required'  => array('A' => 'N', 'P' => 'N', 'B' => 'N', 'C' => 'Y', 'H' => 'Y')
        ),
        'zipcode'       => array(
            'avail'     => array('A' => 'N', 'P' => 'N', 'B' => 'N', 'C' => 'Y', 'H' => 'Y'),
            'required'  => array('A' => 'N', 'P' => 'N', 'B' => 'N', 'C' => 'Y', 'H' => 'Y')
        ),
        'phone'         => array(
            'avail'     => array('A' => 'N', 'P' => 'N', 'B' => 'N', 'C' => 'Y', 'H' => 'Y'),
            'required'  => array('A' => 'N', 'P' => 'N', 'B' => 'N', 'C' => 'N', 'H' => 'N')
        ),
        'fax'           => array(
            'avail'     => array('A' => 'N', 'P' => 'N', 'B' => 'N', 'C' => 'Y', 'H' => 'Y'),
            'required'  => array('A' => 'N', 'P' => 'N', 'B' => 'N', 'C' => 'N', 'H' => 'N')
        )
    );

    /**
     * Define default contact us fields
     */
    $default_contact_us_fields = array(
        'department'    => array(
            'avail'     => 'Y',
            'required'  => 'Y'
        ),
        'username'      => array(
            'avail'     => 'Y',
            'required'  => 'Y'
        ),
        'title'         => array(
            'avail'     => 'N',
            'required'  => 'N'
        ),
        'firstname'     => array(
            'avail'     => 'Y',
            'required'  => 'Y'
        ),
        'lastname'      => array(
            'avail'     => 'Y',
            'required'  => 'Y'
        ),
        'company'       => array(
            'avail'     => 'Y',
            'required'  => 'N'
        ),
        'b_address'     => array(
            'avail'     => 'Y',
            'required'  => 'Y'
        ),
        'b_address_2'   => array(
            'avail'     => 'Y',
            'required'  => 'N'
        ),
        'b_city'        => array(
            'avail'     => 'Y',
            'required'  => 'Y'
        ),
        'b_county'      => array(
            'avail'     => 'Y',
            'required'  => 'Y'
        ),
        'b_state'       => array(
            'avail'     => 'Y',
            'required'  => 'Y'
        ),
        'b_country'     => array(
            'avail'     => 'Y',
            'required'  => 'Y'
        ),
        'b_zipcode'     => array(
            'avail'     => 'Y',
            'required'  => 'Y'
        ),
        'phone'         => array(
            'avail'     => 'Y',
            'required'  => 'Y'
        ),
        'email'         => array(
            'avail'     => 'Y',
            'required'  => 'Y'
        ),
        'fax'           => array(
            'avail'     => 'Y',
            'required'  => 'N'
        ),
        'url'           => array(
            'avail'     => 'Y',
            'required'  => 'N'
        )
    );

    /**
     * Define shipping estimator fields
     */

    $shipping_estimate_fields = array(
        'city' => array(
            'avail'    => 'Y',
            'required' =>  ''
        ),
        'county' => array(
            'avail'    => 'Y',
            'required' =>  ''
        ),
        'state' => array(
            'avail'    => 'Y',
            'required' => 'Y'
        ),
        'country' => array(
            'avail'    => 'Y',
            'required' => 'Y'
        ),
        'zipcode' => array(
            'avail'    => 'Y',
            'required' => 'Y'
        )
    );

    if ($config['General']['use_counties'] != 'Y') {

        // Disable county usage

        $default_address_book_fields['county']['avail']    = 'N';
        $default_address_book_fields['county']['required'] = 'N';

        $default_contact_us_fields['b_county']['avail']    = 'N';
        $default_contact_us_fields['b_county']['required'] = 'N';

        $shipping_estimate_fields['county']['avail']       = 'N';
    }

    $taxes_units = array(
        'ST'  => 'lbl_subtotal',
        'DST' => 'lbl_discounted_subtotal',
        'SH'  => 'lbl_shipping_cost',
    );

    // Unserialize & Assign Right-to-Left languages
    if (isset($config['r2l_languages']))
        $config['r2l_languages'] = unserialize ($config['r2l_languages']);

    // Unserialize & Assign card types
    if (!empty($config['card_types']))
        $config['card_types'] = unserialize ($config['card_types']);

    if (
        defined('AREA_TYPE')
        && 'C' == constant('AREA_TYPE')
        && is_array($config['card_types'])
    ) {

        foreach ($config['card_types'] as $key => $value) {

            if (empty($value['active'])) {

                unset($config['card_types'][$key]);

            }

        }

    }

    $smarty->assign ('card_types', $config['card_types']);

    if (!defined('XCART_EXT_ENV')) {
        // Include webmaster mode
        if (is_readable($xcart_dir . '/include/webmaster.php')) {
            include_once $xcart_dir . '/include/webmaster.php';
        }

        if(
            $config['General']['enable_debug_console'] == 'Y'
            || $editor_mode == 'editor'
        ) {
            $smarty->debugging = true;
        }
    }

    // IP addresses
    $smarty->assign('PROXY_IP',         $PROXY_IP);
    $smarty->assign('CLIENT_IP',        $CLIENT_IP);
    $smarty->assign('REMOTE_ADDR',      $REMOTE_ADDR);
    $mail_smarty->assign('PROXY_IP',    $PROXY_IP);
    $mail_smarty->assign('CLIENT_IP',   $CLIENT_IP);
    $mail_smarty->assign('REMOTE_ADDR', $REMOTE_ADDR);

    // Disable Clean URLs functionality if a request is performed by the HTML Catalog generator script.
    if (defined('IS_ROBOT') && defined('ROBOT') && constant('ROBOT') == 'X-Cart Catalog Generator') {
        $config['SEO']['clean_urls_enabled'] = 'N';
    }

    // Adaptives section
    if (
        is_readable($xcart_dir . '/include/adaptives.php')
        && !defined('XCART_EXT_ENV')
    ) {
        include_once $xcart_dir . '/include/adaptives.php';
    }

}

/**
 * Crontab tasks list
 */
$cron_tasks = array();

$cron_tasks[] = array(
    'x_load'   => 'payment',
    'function' => 'func_check_preauth_expiration'
);

$cron_tasks[] = array(
    'x_load'   => 'payment',
    'function' => 'func_check_preauth_expiration_ttl'
);

/**
 * Read Modules and put in into $active_modules
 */
$import_specification = array();
$active_modules = func_data_cache_get('modules');

if (!is_array($active_modules))
    $active_modules = array();

$active_modules["Simple_Mode"] = true;
$shop_type = "GOLD";
$addons = array();
$body_onload = '';
$tbl_demo_data = $tbl_keys = array();
$css_files = array();
$custom_styles = array();
$container_classes = array();
$predefined_setup_images = array();
$image_caches = array();
$smarty->assign('shop_type', $shop_type);

x_load('image');

// Define checkout module

if (!defined('AREA_TYPE') || AREA_TYPE == 'C') {

    x_session_register('flc_forced', false);

    $flc_forced = isset($force_flc);

    $checkout_module = empty($config['General']['checkout_module']) || $flc_forced
        ? 'Fast_Lane_Checkout'
        : $config['General']['checkout_module'];

    $active_modules[$checkout_module] = true;

    $smarty->assign('checkout_module', $checkout_module);
}

if ($active_modules) {
    // Load functions for module (run include "modules/<module_name>/func.php")
    $include_func = true;

    // Init modules (run include "modules/<module_name>/init.php")
    $include_init = true; 

    $_active_modules = $active_modules;
    foreach ($_active_modules as $active_module => $tmp) {

        $_module_dir  = $xcart_dir . XC_DS . 'modules' . XC_DS . $active_module;
        $_config_file = $_module_dir . XC_DS . 'config.php';

        if (is_readable($_config_file)) {
            include $_config_file;
        }

    }
    unset($include_func, $include_init, $_active_modules);
}

$smarty->assign_by_ref('active_modules', $active_modules);
$mail_smarty->assign_by_ref('active_modules', $active_modules);

$config['setup_images'] = func_data_cache_get("setup_images");

foreach ($config['available_images'] as $k => $v) {

    if (isset($config['setup_images'][$k]))
        continue;

    if (isset($predefined_setup_images[$k])) {
        $config['setup_images'][$k] = $predefined_setup_images[$k];
        continue;
    }

    $config['setup_images'][$k] = array (
        'itype'         => $k,
        'location'      => 'DB',
        'save_url'      => '',
        'size_limit'    => 0,
        'md5_check'     => '',
        'default_image' => './default_image.gif',
        'image_x'       => 124,
        'image_y'       => 74
    );
}

$config['images_dimensions']['T']['width']  = $config['Appearance']['thumbnail_width'];
$config['images_dimensions']['T']['height'] = $config['Appearance']['thumbnail_height'];
$config['images_dimensions']['P']['width']  = 300;
$config['images_dimensions']['P']['height'] = 225;

$preview_image = 'preview_image.gif';

if (empty($config['User_Profiles']['register_fields']))
    $config['User_Profiles']['register_fields'] = serialize(array());

if (empty($config['User_Profiles']['address_book_fields']))
    $config['User_Profiles']['address_book_fields'] = serialize(array());

$config['Appearance']['ui_date_format'] = func_get_ui_date_format();

$smarty->assign('single_mode', $single_mode);

func_image_cache_assign('C', 'catthumbn');

/**
 * If Antibot turned off after it was loaded
 */
if (empty($active_modules['Image_Verification'])) {
    x_session_unregister('antibot_validation_val');
    x_session_unregister('antibot_friend_err');
    x_session_unregister('antibot_contactus_err');
    x_session_unregister('antibot_err');
}

if (!defined('QUICK_START')) {

    // Assign config array to smarty
    $smarty ->assign_by_ref('config', $config);
    $mail_smarty->assign_by_ref('config', $config);

    // Assign Smarty delimiters
    $smarty ->assign('ldelim', "{");
    $mail_smarty->assign('ldelim', "{");

    $smarty ->assign('rdelim', "}");
    $mail_smarty->assign('rdelim', "}");

    if (
        (
            isset($_GET['delimiter'])
            && $_GET['delimiter'] == 'tab'
        ) || (
            isset($_POST['delimiter'])
            && $_POST['delimiter'] == 'tab'
        )
    ) {

        $delimiter = "\t";

    }

    // Assign email regular expression
    $smarty->assign('email_validation_regexp',         func_email_validation_regexp());
    $smarty->assign('clean_url_validation_regexp',     func_clean_url_validation_regexp());
}

/**
 * Session-based cron
 */
if (!defined('QUICK_START') && defined('NEW_SESSION')) {

    $config['General']['cron_call_per_new_session'] = max(intval($config['General']['cron_call_per_new_session']), 0);
    if ($config['General']['cron_call_per_new_session'] > 0) {

        $config['cron_counter'] = max(intval(@$config['cron_counter']), 0);
        $config['cron_counter']++;

        if ($config['cron_counter'] >= $config['General']['cron_call_per_new_session']) {
            define('X_INTERNAL_CRON', true);
            require($xcart_dir . '/cron.php');
            $config['cron_counter'] = 0;
        }

        func_array2insert(
            'config',
            array(
                'name' => 'cron_counter',
                'value' => $config['cron_counter']
            ),
            true
        );
    }
}

/**
 * Clean temporary data
 */
if ((rand() % 10) == 0) {
    db_query("DELETE FROM $sql_tbl[temporary_data] WHERE expire < " . XC_TIME);
}

/**
 * Remember visitor for a long time period
 */
$remember_user = true;

/**
 * Time period for which user info should be stored (days)
 */
$remember_user_days = 30;

$smarty      ->assign('current_area', func_get_current_area());
$mail_smarty ->assign('current_area', func_get_current_area());

/**
 * Redirect from alias host to main host
 */
if (!defined('XCART_EXT_ENV') && $REQUEST_METHOD == 'GET' && isset($_SERVER['HTTP_HOST'])) {
    $tmp = explode(":", $_SERVER['HTTP_HOST'], 2);
    $server_http_host = strtolower($tmp[0]);
    if ($server_http_host != strtolower($xcart_http_host) && $server_http_host != strtolower($xcart_https_host) && (!$HTTPS || !$HTTPS_RELAY))
        func_header_location(($HTTPS ? "https://".$xcart_https_host : "http://".$xcart_http_host) . $REQUEST_URI, true, 301);
}

/**
 * Initialize character set of database. Used in func_translit function
 */
$config['db_charset'] = $sql_vars['character_set_client'];
unset($sql_vars);

// Define name of the auth field depending on login setting: email or username
$login_field_name = func_get_langvar_by_name(
    'lbl_' . ($config['email_as_login'] == 'Y' ? 'email' : 'username'),
    NULL,
    false,
    true
);
$smarty->assign('login_field_name', $login_field_name);

// Detect modal dialog window
if (isset($_GET['open_in_layer'])) {
    $smarty->assign('is_modal_popup', true);
}

if (isset($_GET['is_ajax_request'])) {
    $smarty->assign('is_ajax_request', true);
}

// Check if the cookies are enabled in the browser
require $xcart_dir . '/include/nocookie_warning.php';

/**
 * WARNING !
 * Please ensure that you have no whitespaces / empty lines below this message.
 * Adding a whitespace or an empty line below this line will cause a PHP error.
 */
?>
