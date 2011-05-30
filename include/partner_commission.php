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
 * Partner commission
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: partner_commission.php,v 1.43.2.1 2011/01/10 13:11:50 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

/**
 * Partner commission calculation
 */

if (empty($active_modules['XAffiliate']))
    return;

$partner_commission_value = 0;
/**
 * Get affiliate plan id that has been assigned to the partner
 */
$partner_plan = func_query_first_cell("SELECT $sql_tbl[partner_commissions].plan_id FROM $sql_tbl[partner_commissions], $sql_tbl[partner_plans], $sql_tbl[customers] WHERE $sql_tbl[partner_commissions].plan_id=$sql_tbl[partner_plans].plan_id AND $sql_tbl[partner_commissions].userid='$partner' AND $sql_tbl[customers].id='$partner' AND $sql_tbl[customers].status='Y' AND $sql_tbl[customers].activity='Y' AND $sql_tbl[partner_plans].status='A'");

if ($partner_plan) {

    // Get affiliate plan info

    $tmp = func_query("SELECT * FROM $sql_tbl[partner_plans_commissions] WHERE plan_id='$partner_plan'");
    $plan_info = array();
    if($tmp) {
        foreach($tmp as $v) {
            $plan_info[$v['item_type'].($v['item_id']>0?$v['item_id']:'')] = array('commission_type' => $v['commission_type'], 'commission' => $v['commission']);
        }
    }
    unset($tmp);

    $products_hash = array();
    foreach ($products as $k => $product) {

        if (($single_mode) || ($product['provider'] == $current_order['provider'])) {
            $percent_cost = $product['discounted_price']/100;
            unset($to_partner);

            // Check the products commission rate

            if ($plan_info['P'.$product['productid']])
                $to_partner = $plan_info['P'.$product['productid']]['commission']*($plan_info['P'.$product['productid']]['commission_type'] == '$' ? $product["amount"] : $percent_cost);

            // Check the categories commission rate

            if (!isset($to_partner)) {
                $product_categories = func_query_column("SELECT categoryid FROM $sql_tbl[products_categories] WHERE productid='$product[productid]'");
                foreach ($product_categories as $categoryid) {
                    if (!isset($plan_info['C'.$categoryid]))
                        continue;

                    $tmp = $plan_info['C'.$categoryid]['commission']*($plan_info['C'.$categoryid]['commission_type'] == '$' ? $product["amount"] : $percent_cost);
                    if ($tmp > $to_partner)
                        $to_partner = $tmp;
                }
            }

            // Apply general value of the commission rate

            if (!isset($to_partner) && $plan_info['G'])
                $to_partner = $plan_info['G']["commission"]*($plan_info['G']["commission_type"] == '$'?1:$percent_cost);

            $partner_commission_value += price_format($to_partner);
            db_query("INSERT INTO $sql_tbl[partner_product_commissions] (itemid, orderid, product_commission,userid) VALUES ('$product[itemid]', '$orderid', '".price_format($to_partner)."','$partner')");
            $products_hash[$product['itemid']] = price_format($to_partner);
        }
    } // foreach ($products ...)

    if ($partner_commission_value) {
        db_query ("INSERT INTO $sql_tbl[partner_payment] (userid, orderid, commissions, paid, add_date) VALUES ('$partner', '$orderid', '$partner_commission_value', 'N', '".(isset($xaff_force_time) ? $xaff_force_time : XC_TIME)."')");

        $partner_level = func_get_affiliate_level($partner);
        $parents = func_get_parents($partner);

        if ($parents) {
            $max_level = intval(func_query_first_cell("SELECT MAX(level) FROM $sql_tbl[partner_tier_commissions] WHERE plan_id = '$partner_plan'"));

            foreach ($parents as $v) {
                $level = $partner_level - $v['level'];
                if ($level > $max_level)
                    continue;

                $percent = func_query_first_cell("SELECT commission FROM $sql_tbl[partner_tier_commissions] WHERE plan_id = '$partner_plan' AND level = '$level'");
                $commission = price_format($partner_commission_value*$percent/100);

                if ($commission > 0) {
                    db_query ("INSERT INTO $sql_tbl[partner_payment] (userid, orderid, commissions, paid, affiliate, add_date) VALUES ('$v[userid]', '$orderid', '$commission', 'N', '$partner', '".(isset($xaff_force_time) ? $xaff_force_time : XC_TIME)."')");
                    foreach ($products_hash as $id => $c) {
                        $c = price_format($c*$percent/100);
                        db_query("INSERT INTO $sql_tbl[partner_product_commissions] (itemid, orderid, product_commission, userid) VALUES ('$id', '$orderid', '$c','$v[userid]')");
                    }
                }
            }
        }
    }
}

?>
