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
 * Coupons management
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Provder interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: coupons.php,v 1.61.2.2 2011/01/10 13:12:08 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

define('NUMBER_VARS', "minimum_new,discount_new");

require './auth.php';

require $xcart_dir.'/include/security.php';

$location[] = array(func_get_langvar_by_name('lbl_coupons'), '');

/**
 * Use this condition when single mode is disabled
 */
$provider_condition = ($single_mode ? '' : "AND provider='$logged_userid'");

if ($REQUEST_METHOD == 'POST') {

    if ($mode == 'delete') {

        // Delete selected coupons

        if (is_array($posted_data)) {
            $deleted = false;
            foreach ($posted_data as $coupon=>$v) {
                if (empty($v['to_delete']))
                    continue;

                db_query("delete from $sql_tbl[discount_coupons] where coupon='$coupon' $provider_condition");
                $deleted = true;
            }

            if ($deleted)
                $top_message['content'] = func_get_langvar_by_name('msg_discount_coupons_del');
        }
    }

    if ($mode == 'update') {

        // Update discount table

        if (is_array($posted_data)) {

            foreach ($posted_data as $coupon=>$v) {
                db_query("UPDATE $sql_tbl[discount_coupons] SET status='$v[status]' WHERE coupon='$coupon' $provider_condition");
            }

            $top_message['content'] = func_get_langvar_by_name('msg_discount_coupons_upd');
        }
    }

    if ($mode == 'add') {

        // Add new coupon

        // Generate timestamp
        $expire_new = func_prepare_search_date($expire_new);
        $expire_new -= $config['Appearance']['timezone_offset'];

        $recursive = ($recursive ? 'Y' : 'N');
        $per_user = ($per_user ? 'Y' : 'N');

        if ($how_to_apply_p != 'N')
            $how_to_apply_p = 'Y'; // Apply discount once per order

        if (!in_array($how_to_apply_c, array('Y', 'N1', 'N2')))
            $how_to_apply_c = 'Y'; // Apply discount once per order

        $apply_category_once = $apply_product_once = 'N';

        switch ($apply_to) {

        case '':
        case 'any':
            $productid_new=0;
            $categoryid_new=0;
            break;

        case 'product':
            $minimum_new = 0;
            $categoryid_new=0;
            $apply_product_once = $how_to_apply_p;
            break;

        case 'category':
            $minimum_new = 0;
            $productid_new=0;

            if ($how_to_apply_c == 'Y') {

                $apply_product_once = $apply_category_once = 'Y';

            } elseif ($how_to_apply_c == 'N1') {

                $apply_product_once = 'N';
                $apply_category_once = 'N';

            } else {

                $apply_product_once = 'Y';
                $apply_category_once = 'N';

            }

            break;

        }

        if (
            empty($coupon_new)
            || preg_match("/" . func_coupon_validation_regexp() . "/", $coupon_new)
            || (
                $discount_new <= 0
                && $coupon_type_new != 'free_ship'
            ) || (
                $discount_new > 100
                && $coupon_type_new == 'percent'
            ) || func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[discount_coupons] WHERE coupon='$coupon_new'") > 0
        ) {

            $coupon_data = $_POST;
            $coupon_data['coupon_new'] = stripslashes($coupon_new);
            $coupon_data['expire_new'] = $expire_new;

            x_session_register('coupon_data');

            $top_message['content'] = func_get_langvar_by_name('msg_err_discount_coupons_add');
            $top_message['type'] = 'E';

        } else {

            $coupon_data = array(
                'coupon'              => $coupon_new,
                'discount'            => $discount_new,
                'coupon_type'         => $coupon_type_new,
                'minimum'             => $minimum_new,
                'times'               => $times_new,
                'per_user'            => $per_user,
                'expire'              => $expire_new,
                'status'              => $status_new,
                'provider'            => $logged_userid,
                'productid'           => $productid_new,
                'categoryid'          => $categoryid_new,
                'recursive'           => $recursive,
                'apply_category_once' => $apply_category_once,
                'apply_product_once'  => $apply_product_once,
            );

            func_array2insert('discount_coupons', $coupon_data);

            $top_message['content'] = func_get_langvar_by_name('msg_discount_coupons_add');
        }
    }

    func_header_location('coupons.php' . (isset($navigation_page) ? '?page=' . $navigation_page : ''));
}

$objects_per_page = $config['Discount_Coupons']['coupons_per_page_admin'];

$query = "SELECT *, (expire + " . doubleval($config['Appearance']['timezone_offset']) . ") as expire FROM $sql_tbl[discount_coupons] WHERE 1 $provider_condition";

$result = db_query($query);
$total_items = db_num_rows($result);

if ($total_items > 0) {

    include $xcart_dir . '/include/navigation.php';

}

$first_page = isset($first_page) ? $first_page : 0;

$limit = ' LIMIT ' . $first_page . ', ' . $objects_per_page;

$coupons = func_query($query . $limit);

if (!empty($active_modules['Google_Checkout'])) {
    func_gcheckout_check_coupons();

    $smarty->assign('permanent_warning', $permanent_warning);
}

if (x_session_is_registered('coupon_data')) {

    x_session_register('coupon_data');

    $smarty->assign('coupon_data', $coupon_data);

    x_session_unregister('coupon_data');
}

if (!empty($coupons)) {

    $smarty->assign('coupons',           $coupons);
    $smarty->assign('navigation_script', 'coupons.php?mode=search');

}

x_load('category');

$smarty->assign('allcategories', func_data_cache_get("get_categories_tree", array(0, true, $shop_language, $user_account['membershipid'])));

$smarty->assign('main', 'coupons');

// Assign the current location line
$smarty->assign('location', $location);

if (
    file_exists($xcart_dir . '/modules/gold_display.php')
    && is_readable($xcart_dir . '/modules/gold_display.php')
) {
    include $xcart_dir . '/modules/gold_display.php';
}
func_display('provider/home.tpl', $smarty);
?>
