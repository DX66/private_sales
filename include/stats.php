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
 * Partner statistics
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: stats.php,v 1.33.2.1 2011/01/10 13:11:50 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

$stats_info = array (
    'total_sales' => func_query_first_cell ("SELECT COUNT(*) FROM $sql_tbl[orders], $sql_tbl[partner_payment] WHERE $sql_tbl[orders].orderid=$sql_tbl[partner_payment].orderid AND $sql_tbl[partner_payment].userid='$logged_userid'"),
    'unapproved_sales' => func_query_first_cell ("SELECT COUNT(*) FROM $sql_tbl[orders], $sql_tbl[partner_payment] WHERE $sql_tbl[orders].orderid=$sql_tbl[partner_payment].orderid AND $sql_tbl[partner_payment].userid='$logged_userid' AND $sql_tbl[orders].status NOT IN ('C','P')"),
    'approved_sales' => func_query_first_cell ("SELECT COUNT(*) FROM $sql_tbl[orders], $sql_tbl[partner_payment] WHERE $sql_tbl[orders].orderid=$sql_tbl[partner_payment].orderid AND $sql_tbl[partner_payment].userid='$logged_userid' AND $sql_tbl[orders].status IN ('C','P') AND $sql_tbl[partner_payment].paid != 'Y'"),
    'my_total_sales' => func_query_first_cell ("SELECT COUNT(*) FROM $sql_tbl[orders], $sql_tbl[partner_payment] WHERE $sql_tbl[orders].orderid=$sql_tbl[partner_payment].orderid AND $sql_tbl[partner_payment].userid='$logged_userid' AND $sql_tbl[partner_payment].affiliate = ''"),
    'my_unapproved_sales' => func_query_first_cell ("SELECT COUNT(*) FROM $sql_tbl[orders], $sql_tbl[partner_payment] WHERE $sql_tbl[orders].orderid=$sql_tbl[partner_payment].orderid AND $sql_tbl[partner_payment].userid='$logged_userid' AND $sql_tbl[orders].status NOT IN ('C','P') AND $sql_tbl[partner_payment].affiliate = ''"),
    'my_approved_sales' => func_query_first_cell ("SELECT COUNT(*) FROM $sql_tbl[orders], $sql_tbl[partner_payment] WHERE $sql_tbl[orders].orderid=$sql_tbl[partner_payment].orderid AND $sql_tbl[partner_payment].userid='$logged_userid' AND $sql_tbl[orders].status IN ('C','P') AND $sql_tbl[partner_payment].paid != 'Y' AND $sql_tbl[partner_payment].affiliate = ''"),
    'my_paid_sales' => func_query_first_cell ("SELECT COUNT(*) FROM $sql_tbl[orders], $sql_tbl[partner_payment] WHERE $sql_tbl[orders].orderid=$sql_tbl[partner_payment].orderid AND $sql_tbl[partner_payment].userid='$logged_userid' AND $sql_tbl[partner_payment].paid = 'Y' AND $sql_tbl[partner_payment].affiliate = ''"),
    'pending_commissions' => func_query_first_cell ("SELECT SUM($sql_tbl[partner_payment].commissions) FROM $sql_tbl[partner_payment], $sql_tbl[orders] WHERE $sql_tbl[partner_payment].orderid=$sql_tbl[orders].orderid AND $sql_tbl[partner_payment].userid='$logged_userid' AND $sql_tbl[orders].status NOT IN ('C','P') AND $sql_tbl[partner_payment].paid!='Y'"),
    'approved_commissions' => func_query_first_cell ("SELECT SUM($sql_tbl[partner_payment].commissions) AS numba FROM $sql_tbl[partner_payment], $sql_tbl[orders] WHERE $sql_tbl[partner_payment].orderid=$sql_tbl[orders].orderid AND $sql_tbl[partner_payment].userid='$logged_userid' AND $sql_tbl[orders].status IN ('P','C') AND $sql_tbl[partner_payment].paid!='Y'"),
    'paid_commissions' => func_query_first_cell ("SELECT SUM(commissions) AS numba FROM $sql_tbl[partner_payment] WHERE userid='$logged_userid' AND paid='Y'"),
);

$smarty->assign ('stats_info', $stats_info);

?>
