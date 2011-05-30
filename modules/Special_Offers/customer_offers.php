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
 * Customer offers
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: customer_offers.php,v 1.43.2.2 2011/01/10 13:12:02 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }

if (!defined('SO_CUSTOMER_OFFERS')) {
    define('SO_CUSTOMER_OFFERS', 1);
}

x_load(
    'product',
    'user'
);

$offers = false;

if (
    empty($mode) 
    || !in_array(
        $mode,
        array(
            'cart',
            'cat',
            'product',
            'offer',
            'add_free', 
            'unused',
        )
    )
) {
    $mode = '';
}

if (
    !empty($offers_return_url) 
) {
    if (
        !is_url($offers_return_url)
        || (
            strncmp($offers_return_url, $http_location, strlen($http_location)) != 0 
            && strncmp($offers_return_url, $https_location, strlen($https_location)) != 0
        )
    ) {
        $offers_return_url = false;
    }
}

if (
    $mode == 'add_free' 
    || $mode == 'unused'
) {

    $offers_return_url = 'cart.php?mode=checkout';

    $smarty->assign('offers_return_checkout', true);

} elseif (
    empty($offers_return_url) 
    && !empty($HTTP_REFERER)
) {

    x_session_register('last_offers_return_url');

    $offers_url = $xcart_catalogs['customer'] . '/offers.php';

    if (
        strncmp($HTTP_REFERER, $offers_url, strlen($offers_url)) 
        && !strncmp($HTTP_REFERER, $xcart_catalogs['customer'], strlen($xcart_catalogs['customer']))
    ) {

        $offers_return_url = substr($HTTP_REFERER, strlen($xcart_catalogs['customer']));

        $offers_return_url = preg_replace('!^/+!', '', $offers_return_url);

    } elseif (
        $last_offers_return_url
        && strncmp($HTTP_REFERER, $offers_url, strlen($offers_url)) === 0
    ) {

        $offers_return_url = $last_offers_return_url;

    }

    $last_offers_return_url = $offers_return_url;

}

if (!empty($offers_return_url)) {

    $smarty->assign('offers_return_url', $offers_return_url);

}

if ($mode == 'add_free') {

    x_session_register('cart');

    if (!empty($cart['not_used_free_products'])) {

        $old_search_data = $search_data['products'];
        $old_mode = $mode;

        $search_data['products'] = array();
        $search_data['products']['search_in_subcategories'] = '';

        $tmp_cond = array();

        if (
            $single_mode
            && !empty($cart['not_used_free_products']['F'])
        ) {

            $tmp_cond = array(1);

        } else {

            if (!empty($cart['not_used_free_products']['P'])) {
                $tmp_cond[] = "$sql_tbl[products].productid IN (".implode(',',array_keys($cart['not_used_free_products']['P'])).")";
            }

            if (!empty($cart['not_used_free_products']['C'])) {
                $tmp_cond[] = "$sql_tbl[products_categories].categoryid IN (".implode(',',array_keys($cart['not_used_free_products']['C'])).")";
            }

            if (!empty($cart['not_used_free_products']['R'])) {

                $id_list = array_keys($cart['not_used_free_products']['R']);

                x_load('category');

                $sub_cond = array();

                foreach ($id_list as $c) {

                    $pos = func_category_get_position($c);

                    $sub_cond[] = "$sql_tbl[categories].lpos BETWEEN " . $pos['lpos'] . ' AND ' . $pos['rpos'];

                }

                $tmp_cond[] = implode(' OR ', $sub_cond);

            }

            if (!empty($cart['not_used_free_products']['F'])) {

                $products_providers = array_unique($cart['not_used_free_products']['F']);

                $tmp_cond[] = "$sql_tbl[products].provider IN ('".implode("','",$products_providers)."')";

            }
 
            if (!empty($cart['not_used_free_products']['F'])) {

                $products_providers = array_unique($cart['not_used_free_products']['F']);

                $tmp_cond[] = "$sql_tbl[products].provider IN ('".implode("','",$products_providers)."')";

            }

        }

        // Prepare products filter by providers if $single_mode = false
        if (
            !$single_mode 
            && !empty($cart['orders']) 
            && is_array($cart['orders'])
        ) {

            $_providers = array();

            foreach ($cart['orders'] as $_order) {

                if (
                    !empty($_order['applied_offers']) 
                    && is_array($_order['applied_offers'])
                ) {

                    foreach ($_order['applied_offers'] as $_applied_offer) {

                        $_providers[] = addslashes($_applied_offer['provider']);

                    }

                }

            }

            $_providers = array_unique($_providers);

            $search_data['products']['provider'] = count($_providers) > 1 
                ? $_providers
                : $_providers[0]; 

        }

        if (empty($tmp_cond)) $tmp_cond = array(1);

        $search_data['products']['_']['where'][] = '(('.implode(') OR (', $tmp_cond).'))';
        $search_data['products']['_']['where'][] = "$sql_tbl[products].product_type <> 'C'";

        $params_join = array();
        $bonuses_join = array();
        $all_bonuses_list = array();

        foreach ($cart['not_used_free_products'] as $k=>$bonus_id_list) {

            $bonus_id_list = array_unique($bonus_id_list);

            $id_str = implode(',', $bonus_id_list);

            $id_condition = "$sql_tbl[offer_bonus_params].bonusid IN (".$id_str.")";

            if ($k == 'DISCOUNT_GEN_P') {

                $all_bonuses_list = func_array_merge($all_bonuses_list, $bonus_id_list);

                $params_join[] = $id_condition." AND $sql_tbl[offer_bonus_params].param_type='P' AND $sql_tbl[products].productid=$sql_tbl[offer_bonus_params].param_id";

            } elseif ($k == 'DISCOUNT_GEN_C') {

                $all_bonuses_list = func_array_merge($all_bonuses_list, $bonus_id_list);

                $params_join[] = $id_condition." AND $sql_tbl[offer_bonus_params].param_type='C' AND $sql_tbl[offer_bonus_params].param_arg<>'Y' AND $sql_tbl[products_categories].categoryid=$sql_tbl[offer_bonus_params].param_id";

            } elseif ($k == 'DISCOUNT_GEN_R') {

                $all_bonuses_list = func_array_merge($all_bonuses_list, $bonus_id_list);

                x_load('category');

                $pos = func_category_get_position($sql_tbl['offer_bonus_params']);
                $cat_condition = "$sql_tbl[categories].lpos BETWEEN " . $pos['lpos'] . ' AND ' . $pos['rpos'];
                
                $params_join[] = $id_condition." AND $sql_tbl[offer_bonus_params].param_type='C' AND $sql_tbl[offer_bonus_params].param_arg='Y' AND ($sql_tbl[products_categories].categoryid=$sql_tbl[offer_bonus_params].param_id OR $cat_condition)";

            } elseif (preg_match('!^DISCOUNT_PROV_(.*)$!S', $k, $m)) {

                $all_bonuses_list = func_array_merge($all_bonuses_list, $bonus_id_list);

                $params_join[] = $id_condition." AND $sql_tbl[offer_bonus_params].param_type IN ('N','D')";

                $bonuses_join[] = "$sql_tbl[offer_bonuses].bonusid IN ('".implode("','", $bonus_id_list)."')";

            }

        }

        $bonuses_join_str = "$sql_tbl[offer_bonuses].avail='Y' AND $sql_tbl[offer_bonuses].bonusid IN ('".implode("','", $all_bonuses_list)."')";

        $parent = '';

        if (!empty($params_join)) {

            $search_data['products']['_']['left_joins']['offer_bonus_params'] = array(
                'on'     => '(('.implode(') OR (', $params_join).'))',
                'parent' => 'category_memberships',
            );

        }

        if (!empty($bonuses_join)) {
            $bonuses_join_str .= " AND ((".implode(') OR (', $bonuses_join)."))";
        }

        $search_data['products']['_']['inner_joins']['offer_bonuses'] = array(
            'on' => $bonuses_join_str,
        );


        $search_data['products']['_']['fields_count'][] = $search_data["products"]['_']['fields'][] =
"MIN($sql_tbl[pricing].price -
IF ($sql_tbl[offer_bonuses].bonus_type='D',
    /* true ('D') */
    IF ($sql_tbl[offer_bonuses].amount_type='%',
        /* % discount */
        IF ( $sql_tbl[offer_bonuses].amount_max>0.00 AND ($sql_tbl[pricing].price * $sql_tbl[offer_bonuses].amount_min / 100) > $sql_tbl[offer_bonuses].amount_max,
            /* true */
            $sql_tbl[offer_bonuses].amount_max
            ,
            /* false (without delimiter) */
            ($sql_tbl[pricing].price * $sql_tbl[offer_bonuses].amount_min / 100)
        )
        ,
        /* $ discount */
        IF ( $sql_tbl[offer_bonuses].amount_max>0.00 AND $sql_tbl[offer_bonuses].amount_min > ($sql_tbl[pricing].price * $sql_tbl[offer_bonuses].amount_max / 100),
            /* true */
            ($sql_tbl[pricing].price * $sql_tbl[offer_bonuses].amount_max / 100)
            ,
            /* false (without delimiter) */
            $sql_tbl[offer_bonuses].amount_min
        )
    )
    ,
    /* false (NOT 'D') */
    IF ($sql_tbl[offer_bonuses].bonus_type='N',
        /* true (FREE PRODUCT) */
        $sql_tbl[pricing].price
        ,
        /* false */
        0.00
    )
)
) AS x_special_price";

        if (!isset($sort)) $sort = 'price';

        if (!isset($sort_direction)) $sort_direction = 0;

        $search_data['products']['show_special_prices'] = true;
        $search_data['products']['forsale'] = 'Y';

        $mode = 'search';

        include $xcart_dir . '/include/search.php';

        $search_data['products'] = $old_search_data;
        $mode = $old_mode;
        $page = $old_page;

        $smarty->clear_assign('products');

        if (!empty($products)) {

            $smarty->assign('free_products', $products);

            $smarty->assign('navigation_script', "offers.php?mode=add_free&sort=$sort&sort_direction=$sort_direction&offers_return_url=" . urlencode($offers_return_url));

            $smarty->_tpl_vars['config']['Appearance']['max_select_quantity'] = 1;

        }

    }

    x_session_register('customer_unused_offers');

    if (!empty($customer_unused_offers)) {

        $info_offers = func_get_sorted_offers($customer_unused_offers);

        $smarty->assign('new_offers', $info_offers);

    }

    $smarty->assign('mode', 'add_free');

    return;
}

if (!empty($cart['products'])) {

    $cart_offers = func_get_offers($logged_userid, $current_area, $cart);

    $smarty->assign('cart_offers', $cart_offers);

}

// check product
if (!empty($productid)) {

    $customer_info = func_userinfo($logged_userid, $current_area);
    $membership = empty($customer_info['membership']) ? "" : $customer_info['membership'];
    $offers_product = func_select_product($productid, $membership, false);

    if (empty($offers_product)) {

        $productid = false;

    } else {

        $smarty->assign('offers_product', $offers_product);

    }

}

// check category
if (!empty($cat)) {

    $offers_category = func_get_category_data($cat);

    if (empty($offers_category)) {

        $cat = false;

    } else {

        $smarty->assign('offers_category', $offers_category);

    }

}

switch ($mode) {

case 'product':

    if (!empty($productid)) {

        $tmp = func_get_product_offers($logged_userid, $current_area, $productid, true);

        if (!empty($tmp)) 
            $offers = array_pop($tmp);

    }

    break;

case 'cat':

    if (!empty($cat)) {

        $offers = func_get_category_offers($logged_userid, $current_area, $cat, true);

    }

    break;

case 'cart':

    $offers = $cart_offers;

    break;

case 'offer':

    if (!empty($offerid)) {

        $tmp = func_get_offer($offerid);

        if ($tmp !== false)
            $offers[] = $tmp;
    }

    break;

case 'unused':

    x_session_register('customer_unused_offers');

    $offers = func_get_sorted_offers($customer_unused_offers);

    break;

default: 

    $mode = '';

    $offers = func_get_offers($logged_userid, $current_area, false);
}

if (is_array($offers)) {

    foreach ($offers as $key => $offer) {

        $promo = func_get_offer_promo($offer['offerid'], $store_language, $offer['provider']);

        $offers[$key] = func_array_merge($offers[$key],$promo);

    }

}

if (empty($offers)) $offers = false;

$smarty->assign('offers',      $offers);
$smarty->assign('mode',        $mode);
$smarty->assign('offers_cart', !empty($cart));

?>
