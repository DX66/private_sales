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
 * Welcome page interface
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Provder interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: general.php,v 1.33.2.1 2011/01/10 13:12:09 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './auth.php';
require $xcart_dir.'/include/security.php';

$location[] = array(func_get_langvar_by_name('lbl_summary'), '');

/**
 * Get the orders info
 */
$curtime = XC_TIME; #+24*3600*3;

$start_dates[] = func_query_first_cell("SELECT last_login FROM $sql_tbl[customers] WHERE id='$logged_userid'");  // Since last login
$start_dates[] = func_prepare_search_date($curtime);
$start_week = $curtime - (date('w',$curtime))*24*3600; // Week starts since Sunday
$start_dates[] = func_prepare_search_date($start_week);
$start_dates[] = mktime(0,0,0,date('m',$curtime),1,date('Y',$curtime)); // Current month

// owner filter
$tax_provider_condition = ($single_mode) ? '' : " AND $sql_tbl[tax_rates].provider='$logged_userid'";
$order_provider_condition = ($single_mode) ? '' : " AND $sql_tbl[order_details].provider='$logged_userid'";
$shiprate_provider_condition = ($single_mode) ? '' : " AND $sql_tbl[shipping_rates].provider='$logged_userid'";
$product_provider_condition = ($single_mode) ? '' : " AND $sql_tbl[products].provider='$logged_userid'";

foreach($start_dates as $start_date) {

    $date_condition = "AND $sql_tbl[orders].date>='$start_date' AND $sql_tbl[orders].date<='$curtime' AND $sql_tbl[orders].orderid=$sql_tbl[order_details].orderid ".$order_provider_condition;

    $orders['P'][] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[orders], $sql_tbl[order_details] WHERE $sql_tbl[orders].status='P' $date_condition");
    $orders['F'][] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[orders], $sql_tbl[order_details] WHERE $sql_tbl[orders].status IN ('F','D') $date_condition");
    $orders['I'][] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[orders], $sql_tbl[order_details] WHERE $sql_tbl[orders].status='I' $date_condition");
    $orders['Q'][] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[orders], $sql_tbl[order_details] WHERE $sql_tbl[orders].status='Q' $date_condition");

}

/**
 * Get the shipping methods info
 */
$shipping_methods_count = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[shipping] WHERE active='Y'");
$shipping_mod_enabled = func_query("SELECT code, COUNT(*) as count FROM $sql_tbl[shipping] WHERE active='Y' GROUP BY code ORDER BY code");

/**
 * Get the shipping rates info
 */
$shipping_rates_count = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[shipping_rates] WHERE 1 ".$shiprate_provider_condition);
$shipping_rates_enabled = func_query("SELECT $sql_tbl[shipping].code, COUNT(*) as count FROM $sql_tbl[shipping], $sql_tbl[shipping_rates] WHERE $sql_tbl[shipping].shippingid=$sql_tbl[shipping_rates].shippingid $shiprate_provider_condition GROUP BY $sql_tbl[shipping].code ORDER BY $sql_tbl[shipping].code");

/**
 * Get the products critical properties
 */
$empty_prices = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products], $sql_tbl[pricing] WHERE 1 $product_provider_condition AND $sql_tbl[products].productid=$sql_tbl[pricing].productid AND $sql_tbl[pricing].quantity='1' AND $sql_tbl[pricing].price='0.00' AND $sql_tbl[pricing].variantid = '0'");

/**
 * Check shipping rates
 */
$zone_rates = '';

$zone_rates = func_query("SELECT zoneid, count(zoneid) AS count FROM $sql_tbl[shipping_rates] WHERE 1 $shiprate_provider_condition GROUP BY zoneid ORDER BY zoneid");

if (is_array($zone_rates)) {
    foreach($zone_rates as $k=>$v) {
        if ($v['zoneid'] == 0)
            $zone_rates[$k]['name'] = func_get_langvar_by_name('lbl_zone_default');
        else
            $zone_rates[$k]['name'] = func_query_first_cell("SELECT zone_name FROM $sql_tbl[zones] WHERE zoneid = '$v[zoneid]'");
    }
}

$zone_rates_count = count($zone_rates);

/**
 * Check taxes
 */
$tax_info = func_query("SELECT $sql_tbl[taxes].*, COUNT($sql_tbl[tax_rates].taxid) AS count FROM $sql_tbl[taxes] LEFT JOIN $sql_tbl[tax_rates] ON $sql_tbl[taxes].taxid=$sql_tbl[tax_rates].taxid $tax_provider_condition WHERE $sql_tbl[taxes].active='Y' GROUP BY $sql_tbl[taxes].taxid ORDER BY $sql_tbl[taxes].tax_name");

/**
 * Set up the smarty templates variables
 */
$smarty->assign('single_mode', $single_mode);
$smarty->assign('orders', $orders);
$smarty->assign('shipping_methods_count', $shipping_methods_count);
$smarty->assign('shipping_mod_enabled', $shipping_mod_enabled);
$smarty->assign('shipping_rates_count', $shipping_rates_count);
$smarty->assign('shipping_rates_enabled', $shipping_rates_enabled);
$smarty->assign('empty_prices', $empty_prices);
$smarty->assign('zone_rates', $zone_rates);
$smarty->assign('zone_rates_count', $zone_rates_count);
$smarty->assign('tax_info',$tax_info);

$smarty->assign('main','general_info');

// Assign the current location line
$smarty->assign('location', $location);

if (
    file_exists($xcart_dir.'/modules/gold_display.php')
    && is_readable($xcart_dir.'/modules/gold_display.php')
) {
    include $xcart_dir.'/modules/gold_display.php';
}
func_display('provider/home.tpl',$smarty);
?>
