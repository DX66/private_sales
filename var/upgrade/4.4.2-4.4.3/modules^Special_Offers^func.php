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
 * Common functions for X-Special Offers module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Special Offers
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (C) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.php,v 1.115.2.10 2011/01/10 13:12:02 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }

x_load('cart');

function func_get_column($column, $query)
{
    $result = false;

    if ($p_result = db_query($query)) {
        while ($row = db_fetch_array($p_result))
            $result[] = $row[$column];

        db_free_result($p_result);
    }

    return $result;
}

function func_default_userinfo($user, $type)
{
    global $config, $userinfo;
    static $results_cache = array();
    $data_key = md5(serialize($user) . serialize($type) . empty($userinfo));
    if (isset($results_cache[$data_key]))
        return $results_cache[$data_key];

    if (empty($user)) {
        $result = array();
        foreach (array('b_', 's_') as $p) {
            $result[$p.'country'] = $config['General']['default_country'];
            $result[$p.'state']   = $config['General']['default_state'];
            $result[$p.'county']  = func_default_county($config['General']['default_state'], $config['General']['default_country']);
            $result[$p.'zipcode'] = $config['General']['default_zipcode'];
            $result[$p.'city']    = $config['General']['default_city'];
        }
        $result['membershipid'] = 0;
    }
    else {

        x_load('user');
        $result = func_userinfo($user, 'C', false, false, array('C','H'));
    }

    $results_cache[$data_key] = $result;
    return $result;
}

function func_prepare_bonuses_n_conditions_params($params)
{
    global $sql_tbl, $store_language;

    if (!is_array($params)) return $params;

    foreach ($params as $k => $param) {

        $query = '';
        switch ($param['param_type']) {
            case 'P':
                $query = "$sql_tbl[products].productcode, IF($sql_tbl[products_lng].productid != '', $sql_tbl[products_lng].product, $sql_tbl[products].product) as product FROM $sql_tbl[products] LEFT JOIN $sql_tbl[products_lng] ON $sql_tbl[products_lng].code='".$store_language."' AND $sql_tbl[products_lng].productid = $sql_tbl[products].productid WHERE $sql_tbl[products].productid = '".$param["param_id"]."'";
                break;
            case 'C':
                $query = "category FROM $sql_tbl[categories] WHERE categoryid = '".$param["param_id"]."'";
                break;
            case 'Z':
                $query = "zone_name FROM $sql_tbl[zones] WHERE zoneid = '".$param["param_id"]."'";
                break;
            default:
                //...
        }

        if (!empty($query)) {
            $param = func_array_merge($param, func_query_first("SELECT ".$query));
        }

        $param['param_qnty_work'] = $param['param_qnty'];

        $params[$k] = $param;
    }

    return $params;
}

function func_get_bonuses_n_conditions($offer, $provider, $type)
{
    global $sql_tbl, $single_mode, $current_area;
    static $results_cache = array();

    $data_key = md5($offer["offerid"] . $provider . $type);

    if (isset($results_cache[$data_key]))
        return $results_cache[$data_key];

    $prov_cond = ($single_mode || empty($provider) ? '' : " AND provider='".$provider."'");
    $filter = ($current_area == 'C') ? "AND avail = 'Y'" : "";

    if ($type == 'C') {

        $id_attr    = 'conditionid';
        $tbl_items  = 'offer_conditions';
        $tbl_params = 'offer_condition_params';
        $tbl_memb   = 'condition_memberships';
        $item_data  = 'condition_data';
        $item_type  = 'condition_type';

    } elseif ($type == 'B') {

        $id_attr    = 'bonusid';
        $tbl_items  = 'offer_bonuses';
        $tbl_params = 'offer_bonus_params';
        $tbl_memb   = 'bonus_memberships';
        $item_data  = 'bonus_data';
        $item_type  = 'bonus_type';

    } else {

        // should not occur
        return false;

    }

    $items = func_query("SELECT * FROM $sql_tbl[$tbl_items] WHERE offerid = '".$offer["offerid"]."' ".$prov_cond." ".$filter." ORDER BY ".$id_attr);

    if (!is_array($items)) {
        $results_cache[$data_key] = false;
        return false;
    }

    foreach ($items as $k => $item) {

        $item[$item_data] = @unserialize($item[$item_data]);
        $item['params'] = func_query("SELECT * FROM $sql_tbl[$tbl_params] WHERE ".$id_attr." = '".$item[$id_attr]."' ORDER BY paramid");
        $item['product_sets']['I'] = func_query_column("SELECT setid FROM $sql_tbl[offer_product_sets] WHERE cb_id = '".$item[$id_attr]."' AND set_type = '".$type."' AND appl_type = 'I'");

        $item['params'] = func_prepare_bonuses_n_conditions_params($item['params']);

        if ($item[$item_type] == 'M') {

            $list = func_get_memberships();
            $keys = func_query_column("SELECT membershipid FROM $sql_tbl[$tbl_memb] WHERE ".$id_attr." = '".$item[$id_attr]."'");
            $memberships = $memberships_arr = array();

            if (is_array($list)) {

                foreach($list as $m) {

                    $m['selected'] = in_array($m['membershipid'], $keys);
                    $m['name'] = $m['membership'];
                    $memberships[$m['membershipid']] = $m;
                    if ($m['selected']) {
                        $memberships_arr[$m['membershipid']] = $m['name'];
                    }
                }
            }

            $item['memberships'] = $memberships;
            $item['memberships_arr'] = $memberships_arr;
        }

        $items[$k] = $item;
    }

    $results_cache[$data_key] = $items;
    return $items;
}

function func_offer_get_conditions(&$offer, $provider)
{
    global $sql_tbl;

    $result = func_get_bonuses_n_conditions($offer, $provider, 'C');

    return $result;
}

function func_offer_get_bonuses(&$offer, $provider)
{
    global $sql_tbl, $fake_product_set_id;

    $result = func_get_bonuses_n_conditions($offer, $provider, 'B');

    if (is_array($result)) {

        $prod_set_bonus_types = array('D', 'S', 'N', 'B');
        $prod_set_cnd_types = array('P', 'C');

        $cnd_product_set = func_query("SELECT $sql_tbl[offer_condition_params].*, '".$fake_product_set_id."' as setid FROM $sql_tbl[offer_condition_params] INNER JOIN $sql_tbl[offer_conditions] ON $sql_tbl[offer_conditions].conditionid = $sql_tbl[offer_condition_params].conditionid WHERE $sql_tbl[offer_conditions].offerid = '".$offer["offerid"]."' AND $sql_tbl[offer_conditions].condition_type = 'S' AND $sql_tbl[offer_conditions].avail = 'Y' AND $sql_tbl[offer_condition_params].param_type IN ('".implode("','", $prod_set_cnd_types)."')");
        $offer['has_cnd_product_set'] = !empty($cnd_product_set);

        foreach ($result as $key => $bonus) {

            if (!in_array($bonus['bonus_type'], $prod_set_bonus_types)) continue;

            if (isset($bonus['bonus_data']['use_cnd_sets']) && $bonus['bonus_data']['use_cnd_sets'] == 'Y') {

                $bonus['saved_params'] = array();

                if (is_array($bonus['params'])) {
                    foreach ($bonus['params'] as $k => $param) {
                        if (in_array($param['param_type'], $prod_set_cnd_types)) {
                            $bonus['saved_params'][] = $param;
                            if (array_search($param['setid'], $bonus['product_sets']['I']) === false) {
                                $bonus['product_sets']['I'][] = $param['setid'];
                            }
                            unset($bonus['params'][$k]);
                        }
                    }
                } elseif (empty($bonus['product_sets']['I'])) {
                    $bonus['product_sets']['I'][] = 0;
                }

                $bonus['params'] = func_array_merge($bonus['params'], func_prepare_bonuses_n_conditions_params($cnd_product_set));
                $bonus['product_sets']['I'][] = $fake_product_set_id;
            }

            $bonus['product_sets']['E'] = array();

            if ($bonus['bonus_type'] == 'D') {

                $excl_product_set = func_query("SELECT $sql_tbl[products].productid FROM $sql_tbl[products] LEFT JOIN $sql_tbl[offer_product_params] ON $sql_tbl[offer_product_params].productid = $sql_tbl[products].productid WHERE $sql_tbl[products].provider = '".$provider."' AND $sql_tbl[offer_product_params].sp_discount_avail != 'Y'");

                $excl_set_id = func_query_first_cell("SELECT setid FROM $sql_tbl[offer_product_sets] WHERE offerid = '".$offer["offerid"]."' AND set_type = 'B' AND cb_id = '".$bonus["bonusid"]."' AND cb_type = 'D' AND avail = 'Y' AND appl_type = 'E'");

                if ($excl_set_id && is_array($excl_product_set)) {

                    $excl_params = array();

                    foreach ($excl_product_set as $product) {

                        $excl_params[] = array(
                            'bonusid'     => $bonus['bonusid'],
                            'setid'       => $excl_set_id,
                            'param_type'  => 'P',
                            'param_id'    => $product['productid'],
                            'param_qnty'  => 1,
                            'productcode' => $product['productcode'],
                        );
                    }

                    $bonus['params'] = func_array_merge($bonus['params'], func_prepare_bonuses_n_conditions_params($excl_params));
                    $bonus['product_sets']['E'][] = $excl_set_id;
                }
            }

            $result[$key] = $bonus;
        }
    }

    return $result;
}

function func_offer_count_products(&$products, $productid=false)
{
    $count = array();
    if (!is_array($products) || empty($products)) return $count;

    // Collect quantity of products

    foreach ($products as $product) {

        if (@$product['deleted']) continue; // for Advanced_Order_Management module

        if ($productid!==false && $product['productid'] != $productid)
            continue;

        if (isset($product['is_free_product']) && $product['is_free_product'] == 'Y') continue;

        if (!isset($count[$product['productid']])) {
            $count[$product['productid']] = 0;
        }

        $count[$product['productid']] += $product['amount'];
    }

    return $count;
}

function func_offer_mark_products(&$products, $productid, $value)
{
    global $config;

    if (!is_array($products)) return false;

    if (empty($config['special_offers_mark_products'])) {
        $value = false;
    }

    foreach ($products as $k => $v) {

        if (!empty($v['productid']) && $v['productid'] == $productid) {
            $products[$k]['have_offers'] = (isset($v['free_amount']) && $v['free_amount'] > 0) ? false : $value;
        }

    }

    return true;
}

function func_offer_is_free_product($products, $productid)
{

    $result = false;

    if (is_array($products)) {
        foreach ($products as $product) {
            if ($product['productid'] == $productid) {
                $result = (!empty($product['free_amount']) && $product['free_amount'] > 0);
                break;
            }
        }
    }

    return $result;
}

/**
 * mode ::= E | N
 * E - equal or greater
 * N - Nth product
 */
function func_offer_check_catproducts(&$products, $categoryid, $quantity, $mode, $recursive, &$locked_amount)
{
    global $sql_tbl;
    global $config;
    
    x_load('category');

    $locked_amount = array();
    $count = func_offer_count_products($products);

    if ($recursive == 'Y') {
        $path = func_category_get_position($categoryid);
        if (!empty($path)) {
            $path_condition = "$sql_tbl[categories].lpos BETWEEN " . $path['lpos'] . ' AND ' . $path['rpos'];
        } else {
            $path_condition = 0;
        }

    } else {
        $path_condition = "$sql_tbl[categories].categoryid = '$categoryid'";
    }

    // validate and reduce index
    $local_quantity = 0;

    if (!empty($path_condition)) {
        foreach ($count as $productid=>$amount) {
            $r = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products_categories], $sql_tbl[categories] WHERE
                    $sql_tbl[products_categories].productid = '$productid'
                    AND $sql_tbl[products_categories].categoryid = $sql_tbl[categories].categoryid
                    AND $path_condition
            ");

            if ($r < 1) {
                unset($count[$productid]);
                continue;
            }

            func_offer_mark_products($products, $productid, true);

            $local_quantity += $amount;
        }
    }

    if ($local_quantity < $quantity)
        return false;

    $locked_amount[$categoryid]['amount'] = $quantity;
    $locked_amount[$categoryid]['products'] = $count;

    if ($mode == 'N') {
        $_test_amount = floor($local_quantity / ($quantity + 1));
        return empty($_test_amount) ? 1 : $_test_amount;
    }    

    return true;
}

/**
 * mode ::= E | N
 * E - equal or greater
 * N - Nth product
 */
function func_offer_check_products(&$products, $productid, $quantity, $mode, $recursive, &$locked_amount)
{

    $locked_amount = array();

    $count = func_offer_count_products($products, $productid);
    if (empty($count[$productid])) return false;

    if (($count[$productid] >= $quantity)) {
        func_offer_mark_products($products, $productid, true);
        $locked_amount[$productid] = $quantity;

        if ($mode == 'N') {
            $_test_amount = floor($count[$productid] / ($quantity + 1));
            return empty($_test_amount) ? 1 : $_test_amount;
        }    

        return true;
    }

    return false;
}

function func_offer_check_condition_set($provider, &$products, &$customer_info, &$condition)
{
    global $sql_tbl;

    if (!is_array($condition['product_sets']['I']) || !is_array($condition['params'])) return false;

    $type = (isset($condition['amount_type']) && $condition['amount_type'] == 'N') ? 'N' : 'E';
    $locked_amount = array('C' => array(), 'P' => array(), 'R' => array());

    $check_functions = array(
        'C'    => 'func_offer_check_catproducts',
        'P'    => 'func_offer_check_products',
    );

    $is_cnd_applicable = false;
    $is_cnd_promo = false;
    $cnd_promo_data = array();

    $mult = 0;

    foreach ($condition['product_sets']['I'] as $setid) {

        $is_prod_set_applicable = true;
        $is_prod_set_promo = true;
        $prod_set_promo_data = array();

        $set_mult = 0;

        foreach ($condition['params'] as $param) {

            if ($param['setid'] != $setid || $param['param_qnty'] < 1) continue;

            $result = 0;
            $tmp_locked_amount = array('C' => array(), 'P' => array());
            $check_func = $check_functions[$param['param_type']];

            if (!empty($check_func) && function_exists($check_func)) {
                $result = $check_func($products, $param['param_id'], $param['param_qnty'], $type, $param['param_arg'], $tmp_locked_amount[$param['param_type']]);
            }

            if (!$result) {
                $is_prod_set_applicable = false;
                $set_mult = 0;

                if ($param['param_promo'] != 'Y') {
                    $is_prod_set_promo = false;
                } else {
                    $prod_set_promo_data[] = $param;
                }
            }

            if ($is_prod_set_applicable) {

                $set_mult = ($set_mult > 0) ? min($result, $set_mult) : $result;

                // merge index for products
                foreach ($tmp_locked_amount['P'] as $pid => $qnty) {
                    if (!isset($locked_amount['P'][$pid])) {
                        $locked_amount['P'][$pid] = $qnty;
                    } else {
                        $locked_amount['P'][$pid] = max($locked_amount['P'][$pid], $qnty);
                    }
                }

                // merge index for categories
                $l_key = ($param['param_arg'] == 'Y') ? 'R' : 'C';
                $l_locked_amount = is_array($locked_amount[$l_key]) ? $locked_amount[$l_key] : array();
                foreach ($tmp_locked_amount['C'] as $cid => $value) {
                    $l_value = isset($l_locked_amount[$cid]) ? $l_locked_amount[$cid] : 0;
                    if (!isset($l_value) || $l_value['amount'] < $value['amount']) {
                        $locked_amount[$l_key][$cid] = $value;
                    }
                }
            }
        }

        $is_cnd_applicable = $is_cnd_applicable || $is_prod_set_applicable;
        $is_cnd_promo = $is_cnd_promo || $is_prod_set_promo;
        if ($is_prod_set_promo) {
            $cnd_promo_data[$setid] = $prod_set_promo_data;
        }

        $mult += $set_mult;
    }

    if ($mult > 0 && $type != 'N') {
        $mult = 1;
    }

    $condition['mult'] = $mult;
    $condition['locked_amount'] = $is_cnd_applicable ? $locked_amount : array();

    $condition['is_promo_offer'] = $is_cnd_promo ? 'Y' : 'N';
    $condition['promo_data'] = $is_cnd_promo ? $cnd_promo_data : array();

    return $is_cnd_applicable;
}

function func_offer_check_condition_membership($provider, &$products, &$customer_info, &$condition)
{
    if (empty($condition['memberships_arr'])) {
        if (empty($customer_info['membershipid']))
            return true;
        return false;
    }

    return isset($condition['memberships_arr'][$customer_info['membershipid']]);
}

function func_offer_check_condition_zone($provider, &$products, &$customer_info, &$condition)
{
    x_load('cart'); // for func_get_customer_zones_avail()

    foreach ($condition['params'] as $param) {
        $zones = func_get_customer_zones_avail($customer_info, $provider, $param['param_arg']);

        $weight = 0;
        $check = array();
        foreach ($zones as $zoneid=>$w) {
            if ($w < $weight) break;
            $weight = $w;
            $check[] = $zoneid;
        }

        if (in_array($param['param_id'], $check)) {
            return true;
        }
    }

    return false;
}

function func_offer_get_subtotal($products)
{

    $result = 0;

    foreach ($products as $product) {

        if (@$product['deleted']) continue; // for Advanced_Order_Management module

        $result += $product['amount']*$product['display_price'];
    }

    return price_format($result);
}

function func_offer_check_condition_subtotal($provider, &$products, &$customer_info, &$condition)
{

    $subtotal = func_offer_get_subtotal($products);
    $cnd_data = $condition['condition_data'];

    $result = (($condition['amount_min'] <= $subtotal) &&
               ($condition['amount_max'] == 0.00 || $subtotal <= $condition['amount_max']));

    if (!$result && $cnd_data['show_promo'] == 'Y' && $subtotal >= $cnd_data['promo_amount']) {
        $condition['is_promo_offer'] = 'Y';
        $condition['exceed_amount'] = $condition['amount_min'] - $subtotal;
        if ($condition['exceed_amount'] < 0) {
            $condition['exceed_amount'] = 0;
        }
    } else {
        $condition['is_promo_offer'] = 'N';
    }

    return $result;
}

function func_offer_check_condition_points($provider, &$products, &$customer_info, &$condition)
{
    global $sql_tbl;

    $points = 0;

    if (!empty($customer_info['id'])) {
        $points = func_query_first_cell("SELECT points FROM $sql_tbl[customer_bonuses] WHERE userid='".$customer_info["id"]."'");
        if ($points === false) $points = 0;
    }

    return (($condition['amount_min'] <= $points) && ($condition['amount_max'] == 0 || $points <= $condition['amount_max']));
}

function func_offer_condition_is_empty(&$condition)
{
    static $empty_param_func = array('S','Z');

    if ($condition['avail'] !== 'Y') return true;

    // Ignore some conditions without parameters

    if (empty($condition['params']) && in_array($condition['condition_type'], $empty_param_func)) {
        return true;
    }

    return false;
}

function func_offer_bonus_is_empty(&$bonus)
{
    global $sql_tbl;
    static $empty_param_func = array('N');

    if ($bonus['avail'] !== 'Y') return true;

    if (empty($bonus['params']) && in_array($bonus['bonus_type'], $empty_param_func)) {
        return true;
    }

    if ($bonus['bonus_type'] == 'M') {
        if (!func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[bonus_memberships] WHERE bonusid = '$bonus[bonusid]'"))
            return true;
    }

    return false;
}

function func_offer_check_condition($provider, &$products, &$customer_info, &$condition)
{
    static $functions = array(
        'S' => 'func_offer_check_condition_set',
        'T' => 'func_offer_check_condition_subtotal',
        'M' => 'func_offer_check_condition_membership',
        'B' => 'func_offer_check_condition_points',
        'Z' => 'func_offer_check_condition_zone'
    );

    if (func_offer_condition_is_empty($condition)) return 'I';

    if (!empty($functions[$condition['condition_type']])) {
        $func = $functions[$condition['condition_type']];
        return $func($provider, $products, $customer_info, $condition);
    }

    return false;
}

/**
 * Get offer promo blocks
 */
function func_get_offer_promo($offerid, $lngcode, $provider = '')
{
    global $sql_tbl;

    if (empty($offerid)) return array();

    $promo = func_query_first("SELECT $sql_tbl[offers_lng].promo_short, IF($sql_tbl[images_S].id IS NULL, '', 'Y') AS promo_short_img, IF($sql_tbl[images_S].image_path IS NULL, '', $sql_tbl[images_S].image_path) AS image_path, $sql_tbl[offers_lng].promo_long, $sql_tbl[offers_lng].promo_checkout, $sql_tbl[offers_lng].promo_items_amount FROM $sql_tbl[offers_lng] LEFT JOIN $sql_tbl[images_S] ON $sql_tbl[images_S].id = '$lngcode$offerid' WHERE $sql_tbl[offers_lng].offerid='$offerid' AND $sql_tbl[offers_lng].code='$lngcode'");
    $result = array();

    if (!empty($promo)) {

        x_load('product'); // to call the func_get_allow_active_content
        if (!empty($provider) && !func_get_allow_active_content($provider)) {
            $promo['promo_short'] = func_xss_free($promo['promo_short']);
            $promo['promo_long'] = func_xss_free($promo['promo_long']);
        }

        $result['promo_lng_code'] = $lngcode;
        $result['promo_short'] = $promo['promo_short'];
        $result['promo_short_img'] = $promo['promo_short_img'];
        $result['promo_long'] = $promo['promo_long'];
        $result['promo_checkout'] = $promo['promo_checkout'];
        $result['promo_items_amount'] = $promo['promo_items_amount'];
        $result['html_short'] = (strip_tags($promo['promo_short']) != $promo['promo_short']);
        $result['html_long'] = (strip_tags($promo['promo_long']) != $promo['promo_long']);
        $result['html_checkout'] = (strip_tags($promo['promo_checkout']) != $promo['promo_checkout']);
        $result['html_items_amount'] = (strip_tags($promo['promo_items_amount']) != $promo['promo_items_amount']);
        $result['image_url'] = func_get_image_url($lngcode.$offerid, 'S', $promo['image_path']);
    }

    return $result;
}

/**
 * Get offer by id
 */
function func_get_offer($offerid, $full=false)
{
    global $sql_tbl;
    global $store_language;

    if (empty($offerid)) return false;

    $now = XC_TIME;

    $result = func_query_first("SELECT * FROM $sql_tbl[offers] WHERE offerid='$offerid' AND offer_avail='Y' AND offer_start<='$now' AND offer_end>='$now'");
    if ($result) {
        $promo = func_get_offer_promo($offerid, $store_language, $result['provider']);
        $result = func_array_merge($result, $promo);

        if ($full) {
            $result['conditions'] = func_offer_get_conditions($result, $result['provider']);
            $result['bonuses'] = func_offer_get_bonuses($result, $result['provider']);
        }
    }

    return $result;
}

function func_check_offer(&$offer)
{
    $valid = true;

    // check details
    $now = XC_TIME;
    $offer['incorrect_period'] = $offer['offer_end'] < $offer['offer_start'];
    $offer['upcoming'] = $offer['offer_start'] > $now;
    $offer['expired'] = $now > $offer['offer_end'];

    if ($offer['incorrect_period'])
        $valid = false;

    // check conditions
    $offer['conditions_valid'] = !empty($offer['conditions']);
    if ($offer['conditions_valid']) {
        $non_avail = 0;
        foreach ($offer['conditions'] as $k=>$condition) {
            if ($condition['avail'] !== 'Y') {
                $non_avail++;
                continue;
            }

            if (func_offer_condition_is_empty($condition)) {
                $offer['conditions'][$k]['valid'] = false;
                $valid = false;
                $offer['conditions_valid'] = false;
            }
            else
                $offer['conditions'][$k]['valid'] = true;
        }

        if ($non_avail >= count($offer['conditions'])) {
            $offer['conditions_valid'] = false;
            $valid = false;
        }
    }
    else
        $valid = false;

    // check bonuses
    $offer['bonuses_valid'] = !empty($offer['bonuses']);
    if ($offer['bonuses_valid']) {
        $non_avail = 0;
        foreach ($offer['bonuses'] as $k=>$bonus) {
            if ($bonus['avail'] !== 'Y') {
                $non_avail++;
                continue;
            }
            if (func_offer_bonus_is_empty($bonus)) {
                $offer['bonuses'][$k]['valid'] = false;
                $valid = false;
                $offer['bonuses_valid'] = false;
            }
            else
                $offer['bonuses'][$k]['valid'] = true;
        }

        if ($non_avail >= count($offer['bonuses'])) {
            $offer['bonuses_valid'] = false;
            $valid = false;
        }
    }
    else
        $valid = false;

    $offer['valid'] = $valid;
    if (!$valid || $offer['expired'])
        $offer['invalid'] = true;
    else
        $offer['invalid'] = false;
}

function func_check_free_offer_conditions(&$offer, $offer_cnd)
{
    global $login;

    $conditions = array(
        'reduce_bp' => ($offer_cnd['condition_type'] == 'B' &&
                        $offer_cnd['condition_data']['reduce_bp'] == 'Y' &&
                        !empty($login)),
    );

    $result = false;

    foreach ($conditions as $name => $cnd) {
        $result = $result || $cnd;

        if (!$result) continue;

        switch ($name) {
            case 'reduce_bp':
                $offer['reduce_bp'] = true;
                $offer['amount_min'] = intval($offer_cnd['amount_min']);
                break;
            default:
                //...
        }
    }

    return $result;
}

function func_offer_is_free(&$offer)
{

    $is_free = false;

    if (is_array($offer['conditions'])) {
        foreach ($offer['conditions'] as $offer_cnd) {
            $is_free = $is_free || func_check_free_offer_conditions($offer, $offer_cnd);
        }
    }

    return $is_free;
}

function func_get_applicable_offers(&$products, &$customer_info, $provider, $use_conditions = '', $offerid = false, $include_additional_offers = false)
{
    global $sql_tbl, $config, $single_mode, $cart;

    $now = XC_TIME;

    $provider_condition = '';
    if (!$single_mode && $provider != '') {
        $provider_condition = " AND provider='$provider'";
    }

    $offerid_condition = '';
    if (is_array($offerid) && !empty($offerid)) {
        $offerid_condition = " AND offerid IN (".implode(',',$offerid).")";
    }

    $applied_offers    = array();
    $free_offers    = array();
    $promo_offers    = array();

    $p_result = db_query("SELECT * FROM $sql_tbl[offers] WHERE offer_avail='Y' AND offer_start<='$now' AND offer_end>='$now'".$provider_condition.$offerid_condition);
    if (!$p_result) return false;

    while ($offer = db_fetch_array($p_result)) {
        $offer['conditions'] = func_offer_get_conditions($offer, $offer["provider"]);
        if ($offer['conditions'] === false) continue;

        $offer['mult'] = false;

        $is_promo_offer = true;
        $is_offer_applicable = true;

        foreach ($offer['conditions'] as $condition_key => $condition) {

            if (func_offer_condition_is_empty($condition)) {
                $valid = 'I';
            } else {
                if (!empty($use_conditions) && strpos($use_conditions, $condition['condition_type']) === false) {
                    $valid = true;
                } else {
                    $valid = func_offer_check_condition($offer['provider'], $products, $customer_info, $condition);
                    $offer['conditions'][$condition_key] = $condition;
                }
            }

            if (!$valid) {
                $is_offer_applicable = false;

                if ($include_additional_offers) {
                    if (isset($condition['is_promo_offer']) && $condition['is_promo_offer'] == 'Y') {
                        $offer['exceed_amount'] += $condition['exceed_amount'];
                        $offer['promo_data'] = func_array_merge($offer['promo_data'], $condition['promo_data']);
                    } else {
                        $is_promo_offer = false;
                    }
                }
            }

            if ($valid === true && $is_offer_applicable) {
                if (!empty($condition['mult'])) {
                    if ($offer['mult'] === false) {
                        $offer['mult'] = $condition['mult'];
                    } else {
                        $offer['mult'] = min($offer['mult'], $condition['mult']);
                    }
                }
            }
        }

        if ($offer['mult'] === false)
            $offer['mult'] = 1;

        if ($is_offer_applicable) {
            $offer['bonuses'] = func_offer_get_bonuses($offer, $offer["provider"]);
            if ($offer['bonuses'] !== false) {
                if ($include_additional_offers && func_offer_is_free($offer)) {
                    $free_offers[$offer['offerid']] = $offer;
                    if (isset($cart['applied_free_offers'][$offer['offerid']])) {
                        $applied_offers[$offer['offerid']] = $offer;
                    }
                } else {
                    $applied_offers[$offer['offerid']] = $offer;
                }
            }
        } elseif ($include_additional_offers && $is_promo_offer) {
            $promo_offers[$offer['offerid']] = $offer;
        }
    }
    db_free_result($p_result);

    $all_offers = array(
        'applied_offers' => $applied_offers,
        'free_offers'     => $free_offers,
        'promo_offers'     => $promo_offers,
    );

    $offers = $include_additional_offers ? $all_offers : $applied_offers;

    return $offers;
}

function func_get_applicable_offers_cart($cart, $user, $usertype)
{
    if (empty($cart['orders'])) return false;

    $customer_info = func_default_userinfo($user, $usertype);
    $result = array();

    foreach($cart['orders'] as $order) {
        $offers = func_get_applicable_offers($order['products'], $customer_info, $order['provider']);
        if (!empty($offers)) {
            $result = func_array_merge($result, $offers);
        }
    }

    return empty($result) ? false : $result;
}

function func_get_offers($user, $usertype, $cart)
{
    if (is_array($cart)) return func_get_applicable_offers_cart($cart, $user, $usertype);

    $products = array();
    $customer_info = func_default_userinfo($user, $usertype);

    return func_get_applicable_offers($products, $customer_info, '', 'MZ');
}

function func_bonus_check_product(&$product, &$bonus, $check_all=false)
{
    global $sql_tbl;

    x_load('category');

    $incl_result = true;
    $excl_result = false;

    $matched = array();
    $set_types = array('I', 'E');

    $free_shipping_ids = array();

    foreach ($bonus['params'] as $k => $param) {

        switch ($param['param_type']) {

            case 'P':
                $tmp = ($param['param_id'] == $product['productid']);
                break;

            case 'C':
                $path_condition = "$sql_tbl[categories].categoryid = '" . $param['param_id'] . "'";
                if ($param['param_arg'] == 'Y' || $check_all) {

                    $pos = func_category_get_position($param['param_id']);
                    $param['category_path'] = func_get_category_path($param['param_id'], 'categoryid', true);

                    if ($param['param_arg'] == 'Y') {
                        $path_condition = "$sql_tbl[categories].lpos BETWEEN " . $pos['lpos'] . ' AND ' . $pos['rpos'];
                    }
                }

                $tmp = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products_categories], $sql_tbl[categories] WHERE $sql_tbl[products_categories].productid = '" . $product['productid'] . "' AND $sql_tbl[products_categories].categoryid = $sql_tbl[categories].categoryid AND $path_condition");
                break;

            case 'S':
                if ($bonus['bonus_data']['apply_to_shipping'] == 'Y') {
                    $free_shipping_ids[$param['param_id']] = true;
                }
                continue 2;

            default:
                //...
        }

        foreach ($set_types as $set_type) {
            if (in_array($param['setid'], $bonus['product_sets'][$set_type])) {
                $param['set_type'] = $set_type;
            }
        }
        if (!in_array($param['set_type'], $set_types)) {
            $param['set_type'] = 'I';
        }

        switch ($param['set_type']) {
            case 'I':
                $incl_result = false;
                if ($tmp) {
                    $param['kp'] = $k;
                    $matched[] = $param;
                }
                break;
            case 'E':
                if ($tmp) {
                    $excl_result = true;
                }
                break;
            default:
                //...
        }
    }

    $result = (($incl_result || !empty($matched)) && !$excl_result);
    if (empty($matched) && $incl_result) $matched = true;

    if (!$product['sp_use_certain_free_ship'] && $bonus['bonus_data']['apply_to_shipping'] == 'Y') {
        $product['sp_use_certain_free_ship'] = true;
        $product['free_shipping_ids'] = $free_shipping_ids;
    }

    return $result ? $matched : false;
}

function func_bonus_free_sort($a, $b)
{
    global $config, $offer_products_priority;

    // manualy added product from list at special page have higher priority
    if (is_array($offer_products_priority)) {

        $pa = array_search($a['productid'], $offer_products_priority);
        $pb = array_search($b['productid'], $offer_products_priority);

        if ($pa === false && $pb !== false) return 1;
        if ($pa !== false && $pb === false) return -1;
        if ($pa !== false && $pb !== false && $pb != $pa) return ($pb - $pa);
    }

    // place products from cart first
    if (empty($b['amount']) && !empty($a['amount'])) return -1;
    if (!empty($b['amount']) && empty($a['amount'])) return 1;

    $sort_dir = ($config['Special_Offers']['offers_prod_sort_direction'] == 'E') ? 1 : -1;

    if ($a['price'] == $b['price']) return 0;

    return $sort_dir*(($a['price'] < $b['price']) ? 1 : -1);
}

function func_bonus_free_matches_sort($a, $b)
{
    if ($a['param_type'] == 'P' && $b['param_type'] == 'C') return -1;
    if ($a['param_type'] == 'C' && $b['param_type'] == 'P') return 1;

    if ($a['param_type'] == 'P') return 0;

    $a_cnt = count(explode('/',$a['category_path']));
    $b_cnt = count(explode('/',$b['category_path']));
    return $b_cnt - $a_cnt;
}

/**
 * This function generates the unique cartid number
 */
function func_gen_new_cartid(&$cart, &$products)
{
    if (empty($cart['max_cartid']))
        $cart['max_cartid'] = 0;

    $cartid = $cart['max_cartid']+1;
    if (is_array($products)) {
        foreach ($products as $product) {
            if ($cartid > $product['cartid'])
                continue;
            $cartid = $product['cartid']+1;
        }
    }

    $cart['max_cartid'] = $cartid;
    return $cartid;
}

// correct list of applied offers
function func_offer_unset_wrong_offers(&$offers, $products, $saved_offers)
{

    $cart_product_amounts = func_offer_get_product_amount_idxs($products);

    foreach ($offers as $ko => $offer) {
        $conditional_products = func_offer_get_cnd_products($offer);

        if (empty($conditional_products) || !is_array($conditional_products)) continue;

        foreach ($conditional_products as $productid => $amount) {
            if (!isset($cart_product_amounts[$productid]) || $cart_product_amounts[$productid] < $amount) {
                unset($saved_offers[$ko]);
                $offers = $saved_offers;
                return false;
            }
        }
    }

    return true;
}

/**
 * Correct price for 'products for free' bonus
 */
function func_offer_set_free_products(&$offers, &$products, &$order_bonuses, $in_cart = true)
{
    global $special_offers_max_cartid;

    $saved_offers = $offers;
    $order_bonuses = array('points'=>0, 'memberships'=>array());
    $apply_bonus    = array();

    // Create index for free products and sort it by price in descent order

    $free_idx = array();
    foreach ($products as $pk=>$product) {

        if (@$product['deleted']) continue; // for Advanced_Order_Management module

        if ($product['product_type'] == 'C' || isset($product['catalogprice']) || isset($product['hidden'])) continue;

        $free_idx[$pk] = array(
            'pk'        => $pk,
            'productid'    => $product['productid'],
            'amount'    => $product['amount'],
            'price'        => $product['price']
        );
    }
    usort($free_idx, 'func_bonus_free_sort');

    // Part 1: Calculate free amount

    $free_products = array();
    $discount_idx  = array();
    foreach ($products as $pk=>$product) {

        if (@$product['deleted']) continue; // for Advanced_Order_Management module

        if ($product['product_type'] == 'C' || isset($product['catalogprice']) || isset($product['hidden'])) continue;

        $productid = $product['productid'];

        foreach ($offers as $ko=>$offer) {
            foreach ($offer['bonuses'] as $kb=>$bonus) {
                if ($bonus['bonus_type'] != 'N') continue;

                $found_first = false;
                $bonusid = $bonus['bonusid'];
                if (!isset($discount_idx[$productid][$bonusid])) {
                    $found = true;
                    if (!empty($bonus['params'])) {
                        $found = func_bonus_check_product($products[$pk], $bonus, ($bonus['bonus_type'] == 'N'));
                        if (is_array($found)) {
                            $found_first = true;
                            foreach ($found as $km=>$vm) {
                                $found[$km]['kb'] = $kb;
                                $found[$km]['ko'] = $ko;
                            }
                        }
                    }

                    // Check product according applicable bonuses

                    $discount_idx[$productid][$bonusid] = $found;

                    if (!empty($found) && $found_first) {
                        $matches = $discount_idx[$productid][$bonusid];
                        if (!isset($free_products[$productid])) {
                            $free_products[$productid] = array();
                        }

                        $free_products[$productid] = func_array_merge($free_products[$productid], $matches);
                    }
                }

            } // foreach ($offer['bonuses'] as $kb=>$bonus) {
        } // foreach ($offers as $ko=>$offer) {
    } // foreach ($products as $pk=>$product) {
    unset($discount_idx);

    if (empty($free_products))
        return;

    // Correct index for 'free products'
    foreach ($free_products as $productid=>$matches) {
        usort($matches, 'func_bonus_free_matches_sort');
        $free_products[$productid] = $matches;
    }

    // Part 2: Apply 'products for free' bonus

    foreach ($free_idx as $f) {
        $productid = $f['productid'];
        $free_amount = 0;
        if (!empty($free_products[$productid])) {
            // find bonus param and calculate free amount of product
            $product_amount = $f['amount'];
            $matches = $free_products[$productid];
            foreach ($matches as $key=>$match) {
                $ko = $match['ko'];
                $kb = $match['kb'];
                $kp = $match['kp'];

                $locked_amount = func_offer_get_cnd_products($offers[$ko]);
                $locked_amount = isset($locked_amount[$productid]) ? $locked_amount[$productid] : false;

                $mult = $offers[$ko]['mult'];
                $offers[$ko]['bonuses'][$kb]['params'][$kp]['has_locked'] = (false !== $locked_amount);
                $offers[$ko]['bonuses'][$kb]['params'][$kp]['param_qnty_work'] *= $mult;
                $param_qnty = $offers[$ko]['bonuses'][$kb]['params'][$kp]['param_qnty_work'];

                if ($param_qnty < 1) continue;

                if (!$in_cart) {
                    $products[$f['pk']]['use_special_price'] = true;
                    $products[$f['pk']]['special_price'] = 0.00;
                }

                $amount = min($param_qnty, $product_amount - $free_amount);
                $free_amount += $amount;

                if ((false !== $locked_amount) && (($product_amount - $free_amount) < $locked_amount)) {
                    $free_amount = $amount = $product_amount - $locked_amount;
                }

                $offers[$ko]['bonuses'][$kb]['params'][$kp]['param_qnty_work'] -= $amount;

                if ($offers[$ko]['bonuses'][$kb]['params'][$kp]['param_qnty_work'] < 1) {
                    unset($free_products[$productid][$key]);
                }

                if ($product_amount == $free_amount) break;
            }
        }

        if ($free_amount < 1) {
            $products[$f['pk']]['free_amount'] = 0;
            continue;
        }

        // split cart items
        if ($products[$f['pk']]['amount'] > $free_amount) {
            $new_item = $products[$f['pk']];
            $products[$f['pk']]['amount'] -= $free_amount;
            $products[$f['pk']]['free_amount'] = 0;
            $new_item['amount'] = $free_amount;
            $new_item['free_amount'] = $free_amount;
            $new_item['price'] = 0.00;
            $new_item['cartid'] = ++$special_offers_max_cartid;
            $new_item['is_free_product'] = 'Y';
            unset($products[$f['pk']]['is_free_product']);
            unset($new_item['have_offers']);

            $products[] = $new_item;
        }
        else {
            $products[$f['pk']]['free_amount'] = $products[$f['pk']]['amount'];
            $products[$f['pk']]['price'] = 0.00;
            $products[$f['pk']]['is_free_product'] = 'Y';
            $products[$f['pk']]['not_splitted_free_product'] = true;
            unset($products[$f['pk']]['have_offers']);
        }
    }

    $tmp_cart = array('products' => $products);
    if (func_cart_normalize($tmp_cart)) {
        $products = $tmp_cart['products'];
    }

    $result = func_offer_unset_wrong_offers($offers, $products, $saved_offers);

    foreach ($products as $k => $v) {
        if (!empty($v['not_splitted_free_product']))
            unset($products[$k]['not_splitted_free_product']);
    }

    return $result;
}

/**
 * Returns total discount after applying bonuses
 */
function func_offer_apply_discounts(&$offers, &$products, &$order_bonuses)
{
    global $config;

    // groups of intersected offers: key - ID of conditional product, value - array of offers' indexes
    $conditional_products = array();
    foreach ($offers as $ko => $offer) {
        // get conditional products
        $product_ids = func_offer_get_cnd_products($offer);
        if (empty($product_ids)) continue;

        // set offer indexes
        foreach ($product_ids as $id => $amount) {
            if (!isset($conditional_products[$id])) $conditional_products[$id] = array();
            if (!isset($conditional_products[$id][$ko])) $conditional_products[$id][$ko] = 0;
            $conditional_products[$id][$ko] += $amount;
        }
    }

    // unset groups which have only one element
    foreach ($conditional_products as $id => $offer_info) {
        if (!is_array($offer_info) || count($offer_info) <= 1) unset($conditional_products[$id]);
    }

    $discount_idx = array();
    $apply_bonus = array();
    $discount_total = array();

    // Part 1: Calculate bonuses

    $order_bonuses = array('points'=>0, 'memberships'=>array());
    foreach ($products as $pk=>$product) {

        if (@$product['deleted']) continue; // for Advanced_Order_Management module

        $productid = $product['productid'];
        $apply = array (
            'free_shipping'            => false,
            'discount'                => empty($conditional_products) ? 0 : array(),
            'discount_bonus_idx'    => array(),
        );

        foreach ($offers as $ko=>$offer) {
            foreach ($offer['bonuses'] as $kb=>$bonus) {
                $found_first = false;
                $bonusid = $bonus['bonusid'];
                if (!isset($discount_idx[$productid][$bonusid])) {
                    $found = true;
                    if (!empty($bonus['params'])) {
                        $found = func_bonus_check_product($products[$pk], $bonus, ($bonus['bonus_type'] == 'N'));
                        if (is_array($found)) {
                            $found_first = true;
                            foreach ($found as $km=>$vm) {
                                $found[$km]['kb'] = $kb;
                                $found[$km]['ko'] = $ko;
                            }
                        }
                    }

                    $discount_idx[$productid][$bonusid] = $found;
                }

                // Check product according applicable bonuses

                $product_checked = (!empty($discount_idx[$productid][$bonusid]));

                switch ($bonus['bonus_type']) {
                case 'M': // memberships
                    $memberships = func_array_merge_assoc($order_bonuses['memberships'], $bonus['memberships_arr']);
                    $order_bonuses['memberships'] = $memberships;
                    break;

                case 'S': // free shipping
                    if ($product_checked) {
                        $apply['free_shipping'] = !($products[$pk]['sp_use_certain_free_ship'] && empty($products[$pk]['free_shipping_ids']));
                    } else {
                        $products[$pk]['free_shipping_ids'] = array();
                    }
                    break;

                case 'D': // discount
                    if ($product_checked) {
                        if ($bonus['amount_type'] == '%') {
                            $discount = $product['price'] * $bonus['amount_min'] / 100.00;
                            $taxed_discount = $product['taxed_price'] * $bonus['amount_min'] / 100.00;
                            $limit = price_format($bonus['amount_max']);
                        }
                        else {
                            $discount = price_format($bonus['amount_min']);
                            $limit = price_format($product['price'] * $bonus['amount_max'] / 100.00);
                        }
                        if ($discount > $limit && $limit !== '0.00') {
                            $discount = $taxed_discount = $limit;
                        }
                        if ($discount > $product['price']) {
                            $discount = $taxed_discount = $product['price'];
                        }

                        if (!empty($conditional_products)) {
                            // calculate total discount for each offer
                            $discount_total[$ko] += $discount;
                            // group discounts by offer index
                            $apply['discount'][$ko] = $discount;
                            $apply['taxed_discount'][$ko] = $taxed_discount;
                            $apply['discount_bonus_idx'][$ko] = $kb;
                        } else {
                            $apply['discount'] = max($discount, $apply['discount']);
                            $apply['taxed_discount'] = max($taxed_discount, $apply['taxed_discount']);
                        }

                        if ($bonus['bonus_data']['is_discount_avail'] == 'N') {
                            $products[$pk]['sp_discount_unavail'] = 'Y';
                        }
                    }
                    break;
                case 'B':
                    if ($offer['has_cnd_product_set'] &&
                        isset($product['bonus_params']['bonus_points']) && $product['bonus_params']['bonus_points'] > 0 &&
                        $bonus['bonus_data']['replace_bp'] == 'Y') {

                        foreach ($offer['conditions'] as $condition) {
                            if ($condition['condition_type'] == 'S') {
                                $tmp_products = array($product);
                                $tmp_cust_info = array();
                                if (func_offer_check_condition_set($offer['provider'], $tmp_products, $tmp_cust_info, $condition)) {
                                    unset($products[$pk]['bonus_params']['bonus_points']);
                                }
                                break;
                            }
                        }
                    }
                    break;
                }
            }
        }

        if (isset($products[$pk]['bonus_params']['bonus_points'])) {
            $order_bonuses['points'] += abs(intval($products[$pk]['bonus_params']['bonus_points']))*$products[$pk]['amount'];
        }

        $apply_bonus[$pk] = $apply;
    }

    // if there are intersected offers
    if (!empty($conditional_products)) {

        // only include offers with maximum disount (for each group)
        $include_offers = array();
        $intersected_offer_ids = array();

        foreach ($conditional_products as $productid => $offer_idx) {
            if (!is_array($offer_idx)) continue;
            $max_discount = 0;
            $offer_key = false;
            $intersected_offer_ids = array_merge($intersected_offer_ids, array_keys($offer_idx));

            foreach ($offer_idx as $ko => $amount) {
                if ($discount_total[$ko] > $max_discount) {
                    $max_discount = $discount_total[$ko];
                    $offer_key = $ko;
                }
            }

            if ($offer_key !== false) {
                $include_offers[$offer_key] = true;
            }
        }

        // search offer with maximum disount (for each product)
        foreach ($apply_bonus as $pk => $apply) {
            if (!is_array($apply['discount'])) continue;
            $max_discount = 0;
            $max_taxed_discount = 0;

            foreach ($apply['discount'] as $ko => $discount) {
                if (in_array($ko, array_keys($include_offers))) {
                    $max_discount = max($discount, $max_discount);
                    $max_taxed_discount = max($apply['taxed_discount'][$ko], $max_taxed_discount);
                } else {
                    if (in_array($ko, $intersected_offer_ids)) {
                        // unused offer
                        unset($offers[$ko]['bonuses'][$apply['discount_bonus_idx'][$ko]]);
                        if (empty($offers[$ko]['bonuses'])) {
                            unset($offers[$ko]);
                        }
                    } else {
                        $max_discount = $discount;
                        $max_taxed_discount = $apply['taxed_discount'][$ko];
                    }
                }
            }

            $apply_bonus[$pk]['discount'] = $max_discount;
            $apply_bonus[$pk]['taxed_discount'] = $max_taxed_discount;
        }
    }

    // Part 2: Apply bonuses

    $discount = 0;

    // Apply discount bonus
    foreach ($products as $pk=>$product) {

        if (@$product['deleted']) continue; // for Advanced_Order_Management module

        if (!empty($apply_bonus[$pk])) {
            $bonus = $apply_bonus[$pk];

            if (isset($bonus['discount']) && $bonus['discount'] > 0.00) {
                if ($product['product_type'] == 'C' && !empty($product['pconf_data']))
                    $products[$pk]['saved_original_price'] = $product['pconf_data']['price'] + $product['options_surcharge'];
                elseif ($config['Taxes']['display_taxed_order_totals'] =="Y")
                    $products[$pk]['saved_original_price'] = $products[$pk]['taxed_price'];
                else
                    $products[$pk]['saved_original_price'] = $products[$pk]['price'];
                $products[$pk]['price'] -= $bonus['discount'];
                $products[$pk]['taxed_price'] -= (empty($bonus['taxed_discount']) ? $bonus['discount'] : $bonus['taxed_discount']);
                $products[$pk]['display_price'] -= $bonus['discount'];
                $products[$pk]['special_price_used'] = true;

                $discount += $bonus['discount'];
            }

            if ($bonus['free_shipping']) {
                $products[$pk]['free_shipping'] = 'Y';
                $products[$pk]['free_shipping_used'] = true;
            }
        }
    }

    // Create array of not used 'free products'
    foreach ($offers as $k=>$offer) {
        $not_used_free_products = array('P' => array(), 'C' => array(), 'R' => array(), 'A' => array());
        if (empty($offer['bonuses']))
            continue;

        if (!empty($offer['mult']))
            $mult = $offer['mult'];
        else            
            $mult = 1;

        foreach ($offer['bonuses'] as $bonus) {
            // discount
            if ($bonus['bonus_type'] == 'D' && empty($bonus['params'])) {
                $not_used_free_products['F'][] = $offer['provider'];
                $not_used_free_products['DISCOUNT_PROV_'.$offer['provider']][] = $bonus['bonusid'];
                continue;
            }

            if (($bonus['bonus_type'] != 'N' && $bonus['bonus_type'] != 'D') || empty($bonus['params']))
                continue;

            foreach ($bonus['params'] as $param) {
                if ($bonus['bonus_type'] == 'D')
                    $param['param_qnty_work'] = 1;

                if (($param['param_type'] != 'P' && $param['param_type'] != 'C') || $param['param_qnty_work'] < 1)
                    continue;

                if ($param['param_type'] == 'P') {
                    $key = 'P';
                }
                else if ($param['param_type'] == 'C') {
                    if ($param['param_arg'] == 'Y')
                        $key = 'R';
                    else
                        $key = 'C';
                }

                $id = $param['param_id'];

                if (in_array($param['setid'], $bonus['product_sets']['I'])) {

                    if (!isset($not_used_free_products[$key][$id])) $not_used_free_products[$key][$id] = 0;

                    $not_used_free_products[$key][$id] += $param['param_qnty_work'];
                    $not_used_free_products["DISCOUNT_GEN_$key"][] = $bonus['bonusid'];

                    if ($key == 'P' && $bonus['bonus_type'] == 'N') {
                        if (!isset($not_used_free_products['A'][$id])) $not_used_free_products['A'][$id] = 0;
                        $free_amount = $param['param_qnty'] * $mult;
                        foreach ($products as $pk => $product) {
                            if ($product['free_amount'] > 0 && $product['productid'] == $id) {
                                $free_amount -= $product['amount'];
                                break;
                            }
                        }
                        if ($free_amount > 0) {
                            $not_used_free_products['A'][$id] += $free_amount;
                        }
                    }
                }
            }
        }

        if (!empty($not_used_free_products))
            $offers[$k]['not_used_free_products'] = $not_used_free_products;
    }

    return $discount;
}

/**
 * Get offers applicable to the categoryid(s)
 */
function func_get_offers_categoryid($categoryid)
{
    if (empty($categoryid)) {
        return false;
    }

    if (is_array($categoryid)) {

        $list = $categoryid;

    } else {

        $list = array ($categoryid);

    }

    $result = func_get_offers_categoryid_sub($list, 'C');

    if (!is_array($result)) {
        $result = array();
    }

    $bonuses = func_get_offers_categoryid_sub($list, 'B');

    if (is_array($bonuses)) {
        $result = func_array_merge($result, $bonuses);
    }

    $result = array_unique($result);

    return empty($result) ? false : $result;
}

function func_get_offers_categoryid_sub($list, $tbl_prefix)
{
    assert('/*Func_get_offers_categoryid_sub @params*/ 
    is_array($list) && is_string($tbl_prefix)');

    global $sql_tbl;
    static $results_cache = array();

    $md5_args = md5(serialize(array($list, $tbl_prefix)));
    if (isset($results_cache[$md5_args])) {
        return $results_cache[$md5_args];
    }

    if ($tbl_prefix == 'B') {
        $items_tbl = $sql_tbl['offer_bonuses'];
        $item_params_tbl = $sql_tbl['offer_bonus_params'];
        $items_tbl_link = "$items_tbl.bonusid=$item_params_tbl.bonusid";
    }
    else {
        $items_tbl = $sql_tbl['offer_conditions'];
        $item_params_tbl = $sql_tbl['offer_condition_params'];
        $items_tbl_link = "$items_tbl.conditionid=$item_params_tbl.conditionid";
    }

    $list_str = implode("','", $list);

    x_load('category');

    $sub_cond = array();
    foreach ($list as $c) {
        $pos = func_category_get_position($c);
        $sub_cond[] = $pos['lpos'] . " BETWEEN c2.lpos AND c2.rpos";
    }

    $sub_cond = implode(' OR ', $sub_cond);

    $query = "SELECT DISTINCT $sql_tbl[offers].offerid
                FROM
                    $sql_tbl[categories] c1,
                    $sql_tbl[offers]
                    INNER JOIN $items_tbl ON 
                        $sql_tbl[offers].offerid=$items_tbl.offerid AND $items_tbl.avail = 'Y',
                    $item_params_tbl
                LEFT JOIN $sql_tbl[categories] c2 ON $item_params_tbl.param_id = c2.categoryid
                WHERE
                    $items_tbl_link AND
                    $item_params_tbl.param_type='C' AND (
                        $item_params_tbl.param_id IN ('$list_str') AND
                        $item_params_tbl.param_id=c1.categoryid OR
                        $item_params_tbl.param_arg='Y' AND $sub_cond
                    
                    )";
    
    $offers = func_get_column('offerid', $query);

    $results_cache[$md5_args] = $offers;
    return $offers;
}

/** 
 * Get all offers matching product
 *
 * Arguments:
 *    array of productid's
 *    optional array of categoryid's
 *
 * Return:
 *    associative array of offers.
 */

function func_get_offers_productid(&$list, $categories=false, $full=false)
{
    if (empty($list) && (!is_array($categories) || empty($categories)))
        return false;

    $result = func_get_offers_productid_sub($list, $categories, $full, 'C');
    if (!is_array($result)) $result = array();

    $bonuses = func_get_offers_productid_sub($list, $categories, $full, 'B');
    if (is_array($bonuses)) {
        foreach ($bonuses as $k=>$v) {
            if (isset($result[$k]))
                $result[$k] = func_array_merge($result[$k], $v);
            else
                $result[$k] = $v;
        }
    }

    foreach ($result as $k=>$v) {
        $result[$k] = array_unique($v);
    }

    return empty($result) ? false : $result;
}

function func_get_offers_productid_sub(&$list, $categories, $full, $tbl_prefix)
{
    global $sql_tbl;

    if ($tbl_prefix == 'B') {
        $items_tbl = $sql_tbl['offer_bonuses'];
        $item_params_tbl = $sql_tbl['offer_bonus_params'];
        $items_tbl_link = "$items_tbl.bonusid=$item_params_tbl.bonusid";
    }
    else {
        $items_tbl = $sql_tbl['offer_conditions'];
        $item_params_tbl = $sql_tbl['offer_condition_params'];
        $items_tbl_link = "$items_tbl.conditionid=$item_params_tbl.conditionid";
    }

    $tables = array();
    $tables[] = $sql_tbl['offers'];
    $tables[] = $items_tbl;
    $tables[] = $item_params_tbl;
    $search = array();

    if (!empty($list)) {
        $search[] = "$item_params_tbl.param_id IN ('".implode("','", $list)."')";
    }

    if (!empty($categories)) {

        x_load('category');

        $tables[] = $sql_tbl['products_categories'];
        $tables[] = $sql_tbl['categories'];
        $like = array();

        foreach ($categories as $id) {

            $path = func_get_category_path($id);
            $root_cat = func_category_get_position($path[0]);

            $cat_location[] = "$sql_tbl[categories].lpos BETWEEN " . $root_cat['lpos'] . ' AND ' . $root_cat['rpos'];
        }

        $cat_location_str = implode(' OR ', $cat_location);

        $search[] = "
            $item_params_tbl.param_id=$sql_tbl[products_categories].productid AND
            $sql_tbl[products_categories].categoryid=$sql_tbl[categories].categoryid AND
            $cat_location_str";
    }

    $search_str = implode(' AND ', $search);

    $tables_str = implode(',', $tables);

    $select_columns = "$item_params_tbl.param_id AS productid, $sql_tbl[offers].offerid";

    $query = "SELECT $select_columns
                FROM $tables_str
                WHERE
                    $sql_tbl[offers].offerid=$items_tbl.offerid AND
                    $items_tbl.avail = 'Y' AND
                    $items_tbl_link AND
                    $item_params_tbl.param_type='P' AND
                    $search_str
                    GROUP BY productid, offerid";

    $result = array();
    if ($p_result = db_query($query)) {
        while ($row = db_fetch_array($p_result)) {
            if (!isset($result[$row['productid']]))
                $result[$row['productid']] = array();
            $result[$row['productid']][] = $row['offerid'];
        }
        db_free_result($p_result);

    }

    return empty($result) ? false : $result;
}

/**
 * Get offers applicable to the categoryid(s) and all it products
 */
function func_get_category_offers($user, $usertype, $categoryid, $full = false)
{
    if (empty($categoryid)) return false;

    $customer_info = func_default_userinfo($user, $usertype);

    $result = array();

    if (!is_array($categoryid)) $categoryid = array($categoryid);

    $offers = func_data_cache_get('get_offers_categoryid', array($categoryid));

    if (is_array($offers)) $result = func_array_merge($result, $offers);

    $products = false;

    $product_offers = func_get_offers_productid($products, $categoryid);

    if (is_array($product_offers)) {
        foreach ($product_offers as $offers) {
            $result = func_array_merge($result, $offers);
        }
    }

    if (!empty($result)) {

        $null_products = false;

        $avail_offers = func_get_applicable_offers($null_products, $customer_info, '', 'MZ', $result);

        $result = array();

        if ($full) {

            $result = $avail_offers;

        } elseif (
            is_array($avail_offers)
            && !empty($avail_offers)
        ) {

            foreach ($avail_offers as $offer) {
                $result[] = $offer['offerid'];
            }

        }

    }

    return empty($result) ? false : $result;
}

/**
 * Get offers applicable to the productid(s)
 */
function func_get_product_offers($user, $usertype, &$products, $full=false)
{
    global $sql_tbl, $active_modules, $single_mode;

    if (empty($products)) return false;

    $customer_info = func_default_userinfo($user, $usertype);

    $list = array();

    if ($products !== false) {

        if (!is_array($products)) {

            $list[] = $products;

        } else {

            $v = array_values($products);

            if (!is_array($v[0])) {

                $list = $products;

            } else {

                foreach ($products as $v) {
                    if (!empty($v['productid'])) $list[] = $v['productid'];
                }

            }

        }

    }

    $offers = func_get_offers_productid($list);

    if (empty($offers))
        $offers = array();

    $cat_idx = func_query_hash("SELECT categoryid, productid FROM $sql_tbl[products_categories] WHERE productid IN ('".implode("','",$list)."')", "categoryid", true, true);

    if (empty($cat_idx) || !is_array($cat_idx))
        return false;

    foreach ($cat_idx as $pids) {
        foreach($pids as $pid) {
            if (!isset($offers[$pid]))
                $offers[$pid] = array();
        }
    }

    // add information about offers of product categories
    foreach ($cat_idx as $categoryid => $pids) {
        $cat_offers = func_data_cache_get('get_offers_categoryid', array($categoryid));
        if (!is_array($cat_offers))
            continue;

        if (empty($active_modules['Simple_Mode']) && !$single_mode)
            $products2offers = func_query_hash("SELECT $sql_tbl[offers].offerid, $sql_tbl[products].productid FROM $sql_tbl[offers], $sql_tbl[products] WHERE $sql_tbl[offers].offerid IN ('".implode("','", $cat_offers)."') AND $sql_tbl[products].productid IN ('".implode("','", array_keys($offers))."') AND $sql_tbl[offers].provider = $sql_tbl[products].provider", "productid", true, true);

        foreach ($offers as $productid => $product_offers) {

            if (!in_array($productid, $pids))
                continue;

            if (empty($active_modules['Simple_Mode']) && !$single_mode) {

                if (!isset($products2offers[$productid]) || empty($products2offers[$productid]))
                    continue;

                $cat_offers = $products2offers[$productid];
            }

            $offers[$productid] = func_array_merge($product_offers, $cat_offers);
        }
    }

    if (empty($offers))
        return false;

    // validate offers
    $all_offers = array();
    foreach ($offers as $k => $v) {
        if (is_array($v)) {
            foreach ($v as $id) {
                $all_offers[$id] = false;
            }
        }
    }

    $result = array();
    if (!empty($all_offers)) {

        $null_products = false;

        $avail_offers = func_get_applicable_offers($null_products, $customer_info, '', 'MZ', array_keys($all_offers));

        if (
            is_array($avail_offers) 
            && !empty($avail_offers)
        ) {

            foreach ($avail_offers as $v) {
                $all_offers[$v['offerid']] = $v;
            }

            foreach ($offers as $k=>$v) {
                $validated = array();
                $v = array_unique($v);
                foreach ($v as $id) {
                    if (!empty($all_offers[$id])) {
                        if ($full)
                            $validated[] = $all_offers[$id];
                        else
                            $validated[] = $id;
                    }
                }
                $result[$k] = $validated;
            }
        }
    }

    return empty($result) ? false : $result;
}

/**
 * Check products for offers
 */
function func_offers_check_products($user, $usertype, &$products)
{
    global $config;

    if (!is_array($products)) return;

    $product_offers = func_get_product_offers($user, $usertype, $products);
    if (!is_array($product_offers)) return;

    if (empty($config['special_offers_mark_products']))
        return;

    foreach ($products as $k=>$v) {
        if (empty($product_offers[$v['productid']])) continue;

        $products[$k]['have_offers'] = !(isset($v['free_amount']) && $v['free_amount'] > 0);
    }
}

/**
 * Get customer bonuses
 */
function func_get_customer_bonus($user)
{
    global $sql_tbl, $shop_language;

    $bonus = func_query_first("SELECT * FROM $sql_tbl[customer_bonuses] WHERE userid='$user'");
    if (!is_array($bonus))
        return false;

    $memberships = func_get_memberships('C', true);
    $keys = explode("|", $bonus['memberships']);
    if (!empty($memberships)) {
        foreach ($memberships as $k => $v) {
            if(!in_array($k, $keys))
                unset($memberships[$k]);
        }
    }
    $bonus['memberships'] = (empty($memberships) ? array() : $memberships);

    return $bonus;
}

/**
 * Update customer bonuses
 */
function func_update_customer_bonus($user, $bonus)
{
    static $bonus_keys = array('points', 'memberships');
    global $sql_tbl;

    if (!is_array($bonus)) {
        db_query("DELETE FROM $sql_tbl[customer_bonuses] WHERE userid='$user'");
        return;
    }

    foreach ($bonus as $k=>$v) {
        if (!in_array($k, $bonus_keys)) {
            unset ($bonus[$k]);
            continue;
        }

        if ($k == 'memberships') {
            $bonus[$k] = ((!empty($v) && is_array($v)) ? implode("|", array_keys($v)) : '');
        }
    }

    if (!empty($bonus)) {
        if (!isset($bonus['memberships']))
            $bonus['memberships'] = '';
        $bonus['userid'] = $user;
        func_array2insert('customer_bonuses', $bonus, true);
    }
}

/**
 * Generate sorted list of offers for 'category', 'product' and
 * 'random' pages
 */
function func_get_sorted_offers($offerid_list)
{
    global $sql_tbl;
    global $config;

    if (!is_array($offerid_list)) return false;

    $limit = '';
    if (!empty($config['Special_Offers']['offers_list_limit']))
        $limit = ' LIMIT '.(int)$config['Special_Offers']['offers_list_limit'];

    $tmp = func_get_column('offerid', "SELECT offerid FROM $sql_tbl[offers] WHERE offerid IN ('".join("','",$offerid_list)."') AND show_short_promo='Y' ORDER BY modified_time DESC $limit");

    if ($tmp === false || !is_array($tmp)) return false;

    $result = array();
    foreach($tmp as $offerid) {
        $offer = func_get_offer($offerid, true);
        $empty_offer = empty($offer['promo_short']) && !$offer['promo_short_img'];
        if (empty($result) || !$empty_offer) {
            $result[] = $offer;
        }
    }

    return empty($result) ? false : $result;
}

function func_offer_merge_free_products($orig, $new)
{
    if (empty($new)) return $orig;

    foreach ($new as $key=>$values) {
        if (!isset($orig[$key])) {
            $orig[$key] = $values;
            continue;
        }

        if ($key == 'F' || $key[0]=='D') { // array of provider names ('discount' bonus)
            $orig[$key] = func_array_merge($orig[$key], $values);
            continue;
        }

        foreach ($values as $id=>$qnty) {
            if (!isset($orig[$key][$id])) {
                $orig[$key][$id] = $qnty;
                continue;
            }
            $orig[$key][$id] += $qnty;
        }
    }

    foreach ($orig as $key => $value) {
        if (empty($orig[$key])) {
            unset($orig[$key]);
        }
    }

    return $orig;
}

function func_offers_search_apply_special_price(&$product, $userid)
{
    if ($product['x_special_price'] < 0.00)
        $product['x_special_price'] = 0.00;

    $orig_product = $product;
    $taxes = func_get_product_tax_rates($orig_product, $userid);

    if ($product['x_special_price'] != $product['price'] || $product['price'] == 0) {
        $orig_product['price'] = $orig_product['x_special_price'];
        func_get_product_taxes($orig_product, $userid, false, $taxes);
        $product['use_special_price'] = true;
        $product['special_price'] = $orig_product['taxed_price'];
    }

    func_get_product_taxes($product, $userid, false, $taxes);
    $product['taxes'] = $taxes;
}

function func_offer_correct_cartid(&$return, $__key, &$cart, $single_mode)
{
    $max_cartid = 1;
    if (!empty($cart['max_cartid']) && $cart['max_cartid'] > $max_cartid) $max_cartid = $cart['max_cartid'];
    if (!empty($return['max_cartid']) && $return['max_cartid'] > $max_cartid) $max_cartid = $return['max_cartid'];

    $return['max_cartid'] = $max_cartid;

    if (!is_array($return['orders'][$__key]['products']) || empty($return["orders"][$__key]['products'])) {
        return;
    }

    foreach ($return['orders'][$__key]['products'] as $k=>$v) {
        if (empty($v['cartid'])) {
            // should not occurs
            continue;
        }
        if ($v['cartid'] > $max_cartid) {
            $max_cartid = $v['cartid'];
        }
    }

    $return['max_cartid'] = $max_cartid;
}

function func_check_new_offers()
{
    global $customer_available_offers, $cart, $logged_userid, $login_type;
    global $smarty, $new_offers_message;

    $is_new_visitor = !x_session_is_registered('customer_available_offers');
    x_session_register('customer_available_offers');
    $current_offers = array();

    $avail_offers = func_get_offers($logged_userid, $login_type, $cart);

    if (
        is_array($avail_offers)
        && !empty($avail_offers)
    ) {

        foreach ($avail_offers as $v) {
            $current_offers[] = $v['offerid'];
        }
        
    }

    if (is_array($cart)) {

        $avail_offers = func_get_offers($logged_userid, $login_type, false);

        if (
            is_array($avail_offers)
            && !empty($avail_offers)
        ) {

            foreach ($avail_offers as $v) {
                $current_offers[] = $v['offerid'];
            }

        }
    }

    if (!func_is_cart_empty($cart) && !empty($cart['orders'])) {
        foreach ($cart['orders'] as $order) {
            $tmp = func_get_product_offers($logged_userid, $login_type, $order['products']);
            if (is_array($tmp)) {
                foreach ($tmp as $v) {
                    if (is_array($v))
                        $current_offers = func_array_merge($current_offers, $v);
                }
            }
        }
    }

    if (is_array($current_offers)) {
        $current_offers = array_unique($current_offers);

        // get changes of available offers
        if (!is_array($customer_available_offers) || !is_array($current_offers) || empty($current_offers)) {
            $new_offers = $current_offers;

        } else {
            $new_offers = array();
            foreach ($current_offers as $offerid) {
                if (!in_array($offerid, $customer_available_offers))
                    $new_offers[] = $offerid;
            }
        }

        if (!defined('OFFERS_DONT_SHOW_NEW') && is_array($new_offers) && !empty($new_offers)) {
            $info_offers = func_get_sorted_offers($new_offers);
            $smarty->assign('new_offers', $info_offers);
            $top_data = array(
                'content' => func_display(
                    'modules/Special_Offers/customer/new_offers_short_list.tpl',
                    $smarty,
                    false
                )
            );
            $smarty->assign('new_offers_message', $top_data);
            if ($is_new_visitor)
                $new_offers_message = $top_data;
        }
    }
    $customer_available_offers = $current_offers;
}

function func_offer_add_excl_product_set($offerid, $provider)
{

    $query_data = array(
        'offerid'        => $offerid,
        'bonus_type'    => 'D',
        'provider'        => $provider,
        'avail'            => 'N',
    );
    $bonusid = func_array2insert('offer_bonuses', $query_data);

    $query_data = array(
        'offerid'        => $offerid,
        'set_type'        => 'B',
        'cb_id'            => $bonusid,
        'cb_type'        => 'D',
        'avail'            => 'Y',
        'appl_type'        => 'E',
    );
    $setid = func_array2insert('offer_product_sets', $query_data);

    return $setid;
}

function func_offer_get_cnd_products($offer)
{

    $result = array();

    $s_condition = false;
    foreach ($offer['conditions'] as $cnd) {
        if ($cnd['condition_type'] == 'S') {
            $s_condition = $cnd;
            break;
        }
    }

    if (empty($s_condition['locked_amount'])) return false;

    foreach ($s_condition['locked_amount'] as $type => $amount_data) {

        if (empty($amount_data) || !is_array($amount_data)) continue;

        if (in_array($type, array('C', 'R'))) {
            foreach ($amount_data as $catid => $cat_data) {

                if (empty($cat_data['products']) || !is_array($cat_data['products'])) continue;

                foreach ($cat_data['products'] as $productid => $amount) {
                    if (!isset($result[$productid])) $result[$productid] = 0;
                    $cat_amount = $cat_data['amount']*$s_condition['mult'];
                    $result[$productid] += ($amount <= $cat_amount) ? 0 : $cat_amount;
                }
            }
        } elseif ($type == 'P') {
            foreach ($amount_data as $productid => $amount) {
                if (!isset($result[$productid])) $result[$productid] = 0;
                $result[$productid] += $amount*$s_condition['mult'];
            }
        }
    }

    return $result;
}

function func_sp_is_discount_unavail($product)
{

    if (
        empty($product)
        || !is_array($product)
    ) {
        return false;
    }

    $result = (
        (
            isset($product['free_amount'])
            && !empty($product['free_amount'])
        )
        || (
            isset($product['sp_discount_unavail'])
            && $product['sp_discount_unavail'] == 'Y'
        )
    );

    return $result;
}

function func_offer_get_product_amount_idxs($products)
{

    $result = array();

    if (!empty($products) && is_array($products)) {
        foreach ($products as $product) {
            if (
                isset($product['free_amount']) 
                && $product['free_amount'] > 0
                && empty($product['not_splitted_free_product'])
            ) {
                continue;
            }

            if (!isset($result[$product['productid']])) {
                $result[$product['productid']] = 0;
            }

            $result[$product['productid']] += $product['amount'];
        }
    }

    return $result;
}

/**
 * Calculate bonus points for products from condition set
 */
function func_offer_get_cnd_bonus_points($offer, $products, $bonus)
{
    static $results_cache = array();
    $total_type = $bonus['bonus_data']['total_type'];
    $amount_min = $bonus['amount_min'];
    $amount_max = $bonus['amount_max'];

    if (empty($products) || empty($offer) || min($amount_min, $amount_max) <= 0)
        return 0;

    $data_key = md5(serialize($offer['conditions']) . serialize($products) . $total_type . $amount_min . $amount_max);

    if (isset($results_cache[$data_key]))
        return $results_cache[$data_key];

    $conditional_products = func_offer_get_cnd_products($offer);
    if (empty($conditional_products) || !is_array($conditional_products))
        return $results_cache[$data_key] = 0;

    $conditional_products_ids = array_keys($conditional_products);

    $total = 0;
    foreach ($products as $product) {
        if (in_array($product['productid'], $conditional_products_ids)) {
            if ($total_type == 'OT')
                $total += $product['price'] * $product['amount'];
            else
                $total += $product['discounted_price'];
        }
    }

    $bp_amount = round(($total * $amount_min) / $amount_max);

    return $results_cache[$data_key] = $bp_amount;
}
?>
