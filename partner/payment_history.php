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
 * Payment history
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Partner interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: payment_history.php,v 1.35.2.1 2011/01/10 13:12:05 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require "./auth.php";
require $xcart_dir."/include/security.php";

$location[] = array(func_get_langvar_by_name("lbl_payment_history"), "");

/**
 * Define data for the navigation within section
 */
$dialog_tools_data["right"][] = array("link" => "stats.php", "title" => func_get_langvar_by_name("lbl_summary_statistics"));

$date_condition = "";

if ($start_date) {
    $start_date = func_prepare_search_date($start_date);
    $end_date   = func_prepare_search_date($end_date, true);
} else {
    $start_date = mktime (0, 0, 0, date("m",XC_TIME), 1, date("Y",time()));
    $end_date = XC_TIME;
}

$date_condition = " AND ($sql_tbl[partner_payment].add_date >= '$start_date' AND $sql_tbl[partner_payment].add_date <= '$end_date') ";

$query_string = "SELECT * FROM $sql_tbl[partner_payment] WHERE userid = '$logged_userid' AND paid = 'Y' $date_condition ORDER BY add_date";
$smarty->assign ("paid_total", func_query_first_cell ("SELECT SUM(commissions) FROM $sql_tbl[partner_payment] WHERE userid = '$logged_userid' AND paid = 'Y' " . $date_condition));

$total_payments = count (func_query ($query_string));

$objects_per_page = 50;

$total_items = $total_payments;
include $xcart_dir."/include/navigation.php";

$payments = func_query ($query_string);

$smarty->assign ("payments", func_query ($query_string . " LIMIT $first_page, $objects_per_page"));
$smarty->assign ("navigation_script", "payment_history.php?mode=$mode&start_date=$start_date&end_date=$end_date");

// Assign the current location line
$smarty->assign("location", $location);

// Assign the section navigation data
$smarty->assign("dialog_tools_data", $dialog_tools_data);

$smarty->assign("start_date", $start_date);
$smarty->assign("end_date", $end_date);
$smarty->assign("main", "payment_history");
func_display("partner/home.tpl", $smarty);
?>
