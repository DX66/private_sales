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
 * This module counts statistic for product sales
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: prod_sold.php,v 1.38.2.2 2011/01/10 13:11:54 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

if (empty($active_modules['Advanced_Statistics']) || $config['Advanced_Statistics']['enable_shop_statistics'] != 'Y')
    return;

$avail_condition = '';
if ($config['General']['unlimited_products'] == 'N' && $config['General']['show_outofstock_products'] != 'Y')
    $avail_condition = " AND $sql_tbl[products].avail>'0' ";

func_run_delayed_query('views_stats_products');

foreach ($products as $key=>$value) {
    $prod_id = $value['productid'];
    $amt = $value['amount'];
    db_query("UPDATE $sql_tbl[products] SET sales_stats = (sales_stats + '$amt') WHERE productid = '$prod_id' ");
    db_query("INSERT INTO $sql_tbl[stats_shop](id, action, date, multi) VALUES('$prod_id', 'S', '".XC_TIME."', '$amt')");

    // Updating threshold for bestsellers

    $sales_stats = func_query_first_cell("SELECT sales_stats FROM $sql_tbl[products] WHERE productid='$prod_id'");
    if ($sales_stats === false) continue;

    $catid = func_query_first_cell("SELECT categoryid FROM $sql_tbl[products_categories] WHERE productid='$prod_id' AND main='Y'");
    $count = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products], $sql_tbl[products_categories] WHERE $sql_tbl[products].productid=$sql_tbl[products_categories].productid AND $sql_tbl[products_categories].categoryid='$catid' AND $sql_tbl[products].forsale='Y' $avail_condition AND $sql_tbl[products].views_stats>'0' AND $sql_tbl[products].sales_stats>'$sales_stats'");

    if ($count >= $config['Bestsellers']['number_of_bestsellers'])
        db_query("UPDATE $sql_tbl[categories] SET threshold_bestsellers='$sales_stats' WHERE categoryid='$catid'");
}

?>
