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
 * Module initialization
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: init.php,v 1.18.2.2 2011/02/04 16:54:14 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

if (defined('QUICK_START'))
    return;

$config['Google_Checkout']['gcheckout_mid'] = trim($config['Google_Checkout']['gcheckout_mid']);
$config['Google_Checkout']['gcheckout_mkey'] = trim($config['Google_Checkout']['gcheckout_mkey']);

// Check requirements for Google Checkout module: true - success, false - fail
$gcheckout_requirements = array();
$gcheckout_requirements['mid'] = (empty($config['Google_Checkout']['gcheckout_mid']) ? 'N' : 'Y');
$gcheckout_requirements['mkey'] = (empty($config['Google_Checkout']['gcheckout_mkey']) ? 'N' : 'Y');
$gcheckout_requirements['tax_included_into_price'] = (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[taxes] WHERE price_includes_tax='Y' AND active='Y'") > 0 ? 'N' : 'Y');
$gcheckout_requirements['display_taxed_order_totals'] = ($config['Taxes']['display_taxed_order_totals'] == 'Y' ? 'N' : 'Y');
$gcheckout_requirements['zone_masks'] = (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[zone_element] WHERE field_type='A' OR field_type='G'") > 0 ? 'W' : 'Y');
$gcheckout_requirements['coupon_codes'] = (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[discount_coupons], $sql_tbl[giftcerts] WHERE $sql_tbl[discount_coupons].coupon=$sql_tbl[giftcerts].gcid") > 0 ? 'W' : 'Y');
$gcheckout_requirements['realtime_shipping_enabled'] = ($config['Shipping']['realtime_shipping'] == 'Y' ? 'W' : 'Y');

foreach ($gcheckout_requirements as $k=>$v) {
    $gcheckout_requirements[$k] = array('result' => $v, 'langvar' => func_get_langvar_by_name('lbl_gcheckout_req_'.$k));
    $_gcheckout_requirements_state[] = $v;
}

if (in_array('N', $_gcheckout_requirements_state)) {
    $gcheckout_enabled = false;
    $smarty->assign('gcheckout_requirements', $gcheckout_requirements);
}
else {
    $gcheckout_enabled = true;

    if (in_array('W', $_gcheckout_requirements_state))
        $smarty->assign('gcheckout_requirements', $gcheckout_requirements);
}

$smarty->assign('gcheckout_enabled', $gcheckout_enabled);

// Use testing sanbox or live environment
if ($config['Google_Checkout']['gcheckout_test_mode'] == 'Y') {
    $gcheckout_env = 'sandbox';
    $gcheckout_sbx = 'checkout/';
}
else {
    $gcheckout_env = 'checkout';
    $gcheckout_sbx = '';
}

// URL for sending HTTPS requests
$gcheckout_xml_url = "https://$gcheckout_env.google.com:443/{$gcheckout_sbx}api/checkout/v2/request/Merchant/".$config['Google_Checkout']['gcheckout_mid'];

// Callback API URL for using on 'General settings/Google Checkout options' page
$smarty->assign('gcheckout_callback_url', $https_location.'/payment/ps_gcheckout.php');

$gcheckout_log_detailed_data = false;
if (defined('GCHECKOUT_DEBUG')) {
    // Logging enabled
    if (GCHECKOUT_DEBUG == 1)
        $gcheckout_log_detailed_data = true;

    $gcheckout_global_log = '';
    $gcheckout_global_xml_log = '';

    register_shutdown_function('func_gcheckout_save_log');
}

if (func_constant('AREA_TYPE') != 'C')
    return;

if (!$gcheckout_enabled) {
    // Disable module for customer area
    unset($active_modules['Google_Checkout']);
    $smarty->assign('active_modules', $active_modules);
    return;
}

x_session_register('cart');

if (!defined('IS_ERROR_MESSAGE'))
    include $xcart_dir.'/modules/Google_Checkout/gcheckout_button.php';

/**
 * If PHP works in CGI mode, HTTP authentication with PHP may not be functioning.
 * Uncomment the line below to ignore authentication in the callback script.
 * Warning! Do it only if your callback script is protected by HTTP
 * authentication via the server configuration (.htaccess)
 */
#define('GCHECKOUT_IGNORE_AUTH', 1);

// For debug purposes, you can enable logging. Logs can be saved
// as files in the directory <xcart_dir>/var/log or be sent to specified e-mail addresses.
/**
 * The format of file names for logs is the following:
 * gcheckout-<date>-<unique_key>.log.php - log of received data (one file per one GET/POST request)
 * x-errors_gcheckout_xml-<date>.php - log of XML code sent to Google Checkout server
 * x-errors_gcheckout-061114.php - general log of Google Checkout module activity
 */
// To enable logging, you should define the constant GCHECKOUT_DEBUG
// at the beginning of this script.
/**
 * Examples:
 */
// define('GCHECKOUT_DEBUG', 1); // write all logs to xcart/var/log

# define('GCHECKOUT_DEBUG', 'test@email.com'); // send general log
# to the e-mail address 'test@email.com'; note that in this case the logs of received data and of XML code sent to Google are not kept or sent.

?>
