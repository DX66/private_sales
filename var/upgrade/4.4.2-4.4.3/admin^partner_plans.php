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
 * Partner plans management
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: partner_plans.php,v 1.40.2.2 2011/01/10 13:11:46 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

define('NUMBER_VARS', 'basic_commission,min_paid');

require './auth.php';

require $xcart_dir . '/include/security.php';

if (!$active_modules['XAffiliate']) {
    func_403(10);
}

$location[] = array(func_get_langvar_by_name('lbl_affiliate_plans'), '');

if ($REQUEST_METHOD == 'POST') {

    /**
     * Update affiliate plans list (title, status)
     */
    $plans = $_POST['plans'];

    if ($mode == 'delete') {

        $where = ' plan_id IN (\'' . implode('\', \'', $ids) . '\')';

        db_query("DELETE FROM $sql_tbl[partner_plans] WHERE " . $where);

        db_query("UPDATE $sql_tbl[partner_commissions] SET plan_id='0' WHERE " . $where);

        if (in_array($config['default_affiliate_plan'], $ids)) {

            db_query("UPDATE $sql_tbl[config] SET value='0' WHERE name='default_affiliate_plan' AND category=''");

        }

        func_header_location('partner_plans.php');
    }

    if ($mode == 'default_plan') {

        if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[config] WHERE name='default_affiliate_plan' AND category=''") == 0) {

            db_query("INSERT INTO $sql_tbl[config] (name, value, category, defvalue) VALUES ('default_affiliate_plan', '$plan_id', '', '')");

        } else {

            db_query("UPDATE $sql_tbl[config] SET value='$plan_id' WHERE name='default_affiliate_plan' AND category=''");

        }

        func_header_location('partner_plans.php');
    }

    if ($mode == 'update') {

        if ($new_plan_title) {

            db_query ("INSERT INTO $sql_tbl[partner_plans] (plan_title, status) VALUES ('$new_plan_title', '$new_status')");

            if ($redirect_to_modify == 'on') {

                $pid = db_insert_id();

                func_header_location("partner_plans.php?mode=edit&planid=$pid");

            }

        }

        func_header_location('partner_plans.php');
    }

    if ($mode == 'edit') {

        if ($plans) {

            foreach ($plans as $pid=>$plan) {

                db_query ("UPDATE $sql_tbl[partner_plans] SET plan_title='$plan[plan_title]', status='$plan[status]'  WHERE plan_id='$pid'");

            }

        }

        func_header_location('partner_plans.php');
    }

    /**
     * Edit/Create affiliate plan commission rates
     */
    if (
        $mode == 'modify'
        || $mode == 'create'
        || $mode == 'delete_rate'
    ) {

        // PRODUCTS COMMISSION RATES PROCESSING

        if ($form == 'products') {

            /**
             * Delete commission rate
             */

            if (
                $mode == 'delete_rate'
                && is_array($productid)
            ) {
                foreach($productid as $prodid) {

                    db_query("DELETE FROM $sql_tbl[partner_plans_commissions] WHERE plan_id='$planid' AND item_id='$prodid' AND item_type='P'");

                }

                func_header_location("partner_plans.php?mode=edit&planid=$planid");

            }

            /**
             * Update commission rates on products
             */

            if (is_array($products)) {

                // Update committions on existing products

                foreach($products as $k => $v) {

                    db_query("UPDATE $sql_tbl[partner_plans_commissions] SET commission='".addslashes(func_convert_number($v["commission"]))."', commission_type='$v[commission_type]' WHERE plan_id='$planid' AND item_id='$k' AND item_type='P'");

                }

            }

            if ($product_ids) {

            // Add new commissions

                $product_ids_array = explode(",", $product_ids);

                if (is_array($product_ids_array)) {

                    foreach ($product_ids_array as $item_id) {

                        $is_exists = (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[partner_plans_commissions] WHERE plan_id = '$planid' AND item_id = '$item_id' AND item_type = 'P'") > 0);

                        if (!$is_exists)
                            db_query("INSERT INTO $sql_tbl[partner_plans_commissions] (plan_id, commission, commission_type, item_id, item_type) VALUES ('$planid', '".addslashes(func_convert_number($new_product_commission))."', '$new_product_commission_type', '$item_id', 'P')");

                    }

                }

            }

        } // if ($form == 'products')

        // CATEGORIES COMMISSION RATES PROCESSING

        elseif ($form == 'categories') {

            /**
             * Delete commission rate
             */
            if (
                $mode == 'delete_rate'
                && is_array($categoryid)
            ) {

                foreach($categoryid as $catid) {

                    db_query("DELETE FROM $sql_tbl[partner_plans_commissions] WHERE plan_id='$planid' AND item_id='$catid' AND item_type='C'");

                }

                func_header_location("partner_plans.php?mode=edit&planid=$planid");

            }

            /**
             * Update commission rates on categories
             */
            if (is_array($categories)) {

            // Update committions on existing categories

                foreach($categories as $k=>$v) {

                    db_query("UPDATE $sql_tbl[partner_plans_commissions] SET commission='".addslashes(func_convert_number($v["commission"]))."', commission_type='$v[commission_type]' WHERE plan_id='$planid' AND item_id='$k' AND item_type='C'");

                }

            }

            if ($new_categoryid) {

            // Add new commissions

                $is_exists = (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[partner_plans_commissions] WHERE plan_id = '$planid' AND item_id = '$new_categoryid' AND item_type = 'C'") > 0);

                if (!$is_exists)
                    db_query("INSERT INTO $sql_tbl[partner_plans_commissions] (plan_id, commission, commission_type, item_id, item_type) VALUES ('$planid', '".addslashes(func_convert_number($new_category_commission))."', '$new_category_commission_type', '$new_categoryid', 'C')");

            }

        }

        // GENERAL COMMISSION RATES PROCESSING
        elseif ($form == 'general') {

            /**
             * Update general commission rate
             */
            if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[partner_plans_commissions] WHERE plan_id='$planid' AND item_id='0' AND item_type='G'") == "0") {

                db_query("INSERT INTO $sql_tbl[partner_plans_commissions] (plan_id, commission, commission_type, item_type) VALUES('$planid', '$basic_commission', '$basic_commission_type', 'G')");

            } else {

                db_query("UPDATE $sql_tbl[partner_plans_commissions] SET commission='$basic_commission', commission_type='$basic_commission_type' WHERE plan_id='$planid' AND item_id='0' AND item_type='G'");

            }

            db_query("UPDATE $sql_tbl[partner_plans] SET min_paid = '$min_paid' WHERE plan_id='$planid'");

            if (!$levels)
                $levels = array();

            $i = count($levels) + 1;

            foreach ($new_level as $v) {

                if (is_numeric($v) && $v > 0) {

                    $levels[$i++] = $v;

                }

            }

            db_query("DELETE FROM $sql_tbl[partner_tier_commissions] WHERE plan_id = '$planid'");

            foreach($levels as $l => $c) {

                $c = max(doubleval($c), 0);

                func_array2insert(
                    'partner_tier_commissions',
                    array(
                        'plan_id'         => $planid,
                        'level'         => $l,
                        'commission'     => $c,
                    ),
                    true
                );

            }

        }

        func_header_location("partner_plans.php?mode=edit&planid=$planid#$form");

    }

} // if ($REQUEST_METHOD == 'POST')

if (
    $mode == 'delete_level'
    && $planid
    && isset($level)
) {

    // Delete MLM level

    $levels = func_query("SELECT * FROM $sql_tbl[partner_tier_commissions] WHERE plan_id = '$planid' AND level != '$level' ORDER BY level");

    db_query("DELETE FROM $sql_tbl[partner_tier_commissions] WHERE plan_id = '$planid'");

    if ($levels) {

        foreach($levels as $k => $v) {

            $v['level'] = $k + 1;

            func_array2insert('partner_tier_commissions', func_addslashes($v));

        }

    }

    func_header_location("partner_plans.php?mode=edit&planid=$planid#mlm");
}

if ($mode == 'edit') {

    if ($planid) {

        x_load('category');
        $smarty->assign('allcategories', func_data_cache_get("get_categories_tree", array(0, true, $shop_language, $user_account['membershipid'])));

        $partner_plan_info = func_get_partner_plan($planid);

        if (is_array($partner_plan_info['commissions'])) {

            foreach($partner_plan_info['commissions'] as $k => $v) {

                switch ($v['item_type']) {
                    case 'P':
                        $partner_plan_info['commissions'][$k]["product"] = func_query_first_cell("SELECT product FROM $sql_tbl[products] WHERE productid='$v[item_id]'");
                        break;

                    case 'C':
                        $partner_plan_info['commissions'][$k]["category"] = func_query_first_cell("SELECT category FROM $sql_tbl[categories] WHERE categoryid='$v[item_id]'");
                        break;

                    case 'G':
                        $general_commission = $v;
                        break;
                }

            }

        }

        $smarty->assign('partner_plans_commissions',     $partner_plan_info['commissions']);
        $smarty->assign('general_commission',             $general_commission);
        $smarty->assign('partner_plan_info',             $partner_plan_info);
        $smarty->assign('mode',                         'modify');

    }

    if (
        !$planid
        || !$partner_plan_info
    ) {
        func_header_location('partner_plans.php');
    }

    $location[count($location) - 1][1] = 'partner_plans.php';

    $location[] = array(func_get_langvar_by_name('lbl_modify_plan'), '');

    $smarty->assign ('main', 'partner_plans_edit');

} else {

    $partner_plans = func_query ("SELECT * FROM $sql_tbl[partner_plans] ORDER BY plan_id");

    if (!empty($partner_plans))
        $smarty->assign ('partner_plans', $partner_plans);

    $smarty->assign ('main', 'partner_plans');
}

// Assign the current location line
$smarty->assign('location', $location);

// Assign the section navigation data
$smarty->assign('dialog_tools_data', $dialog_tools_data);

if (
    file_exists($xcart_dir . '/modules/gold_display.php')
    && is_readable($xcart_dir . '/modules/gold_display.php')
) {
    include $xcart_dir . '/modules/gold_display.php';
}
func_display('admin/home.tpl', $smarty);
?>
