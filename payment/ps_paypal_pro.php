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
 * PayPal Website Payments Pro
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: ps_paypal_pro.php,v 1.40.2.5 2011/02/10 15:12:33 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) {
    require './auth.php';

    $module_params = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor='ps_paypal_pro.php'");
    $paymentid = isset($paymentid) ? $paymentid : 0;
}

x_load('paypal');

x_session_register('cart');

$pp_locale_codes = array('AU','DE','FR','GB','IT','JP','US');
$pp_supported_charsets = array (
    'Big5', "EUC-JP", "EUC-KR", "EUC-TW", 'gb2312', 'gbk', "HZ-GB-2312",
    "ibm-862", "ISO-2022-CN", "ISO-2022-JP", "ISO-2022-KR", "ISO-8859-1",
    "ISO-8859-2", "ISO-8859-3", "ISO-8859-4", "ISO-8859-5", "ISO-8859-6",
    "ISO-8859-7", "ISO-8859-8", "ISO-8859-9", "ISO-8859-13", "ISO-8859-15",
    "KOI8-R", 'Shift_JIS', "UTF-7", "UTF-8", "UTF-16", "UTF-16BE",
    "UTF-16LE", "UTF-32", "UTF-32BE", "UTF-32LE", "US-ASCII",
    "windows-1250", "windows-1251", "windows-1252", "windows-1253",
    "windows-1254", "windows-1255", "windows-1256", "windows-1257",
    "windows-1258", "windows-874", "windows-949", "x-mac-greek",
    "x-mac-turkish", "x-maccentraleurroman", "x-mac-cyrillic",
    "ebcdic-cp-us", "ibm-1047"
);

$pp_charset = in_array($all_languages[$shop_language]['charset'], $pp_supported_charsets) ? $all_languages[$shop_language]['charset'] : 'ISO-8859-1';

$pp_test = $module_params['testmode'];

$pp_dp_id = func_query_first_cell("SELECT $sql_tbl[payment_methods].paymentid FROM $sql_tbl[payment_methods], $sql_tbl[ccprocessors] WHERE $sql_tbl[payment_methods].paymentid='$paymentid' AND $sql_tbl[payment_methods].processor_file='ps_paypal_pro.php' AND $sql_tbl[payment_methods].processor_file=$sql_tbl[ccprocessors].processor AND $sql_tbl[payment_methods].paymentid<>$sql_tbl[ccprocessors].paymentid AND $sql_tbl[payment_methods].active='Y'");
$pp_dp_allowed = !empty($pp_dp_id);

$pp_total = func_paypal_convert_to_BasicAmountType($cart["total_cost"]);

$pp_final_action = ($module_params['use_preauth'] == 'Y' || func_is_preauth_force_enabled($secure_oid)) ? 'Authorization' : 'Sale';

$use_xpc = false;
if ($pp_dp_allowed && !empty($active_modules['XPayments_Connector'])) {
    func_xpay_func_load();

    $proc = xpc_get_paypal_dp_processor($config['paypal_solution']);
    $use_xpc = $proc['use_xpc'] && $proc['use'] == 'xpc';
}

if ($use_xpc) {
    define('XPC_USE_DP_EMULATION', true);
    require_once $xcart_dir.'/payment/cc_xpc.php';

} elseif ($config['paypal_solution'] == 'uk') {
    require_once $xcart_dir.'/payment/ps_paypal_pro_uk.php';

} else {
    require_once $xcart_dir.'/payment/ps_paypal_pro_us.php';
}
?>
