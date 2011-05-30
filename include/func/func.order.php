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
 * Orders-related functions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.order.php,v 1.131.2.25 2011/05/04 07:16:14 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load(
    'cart',
    'crypt',
    'mail',
    'user'
);

/**
 * This function creates array with order data
 */
function func_select_order($orderid)
{
    global $sql_tbl, $config, $current_area, $active_modules, $shop_language, $login;

    $o_date = "date+'".$config["Appearance"]["timezone_offset"]."' as date";

    $order = func_query_first("SELECT *, $o_date FROM $sql_tbl[orders] WHERE $sql_tbl[orders].orderid='$orderid'");

    if (empty($order)) {

        return false;

    }

    $order['titleid']   = func_detect_title($order['title']);
    $order['b_titleid'] = func_detect_title($order['b_title']);
    $order['s_titleid'] = func_detect_title($order['s_title']);

    if ($current_area == 'C') {

        $order['title']   = func_get_title($order['titleid']);
        $order['b_title'] = func_get_title($order['b_titleid']);
        $order['s_title'] = func_get_title($order['s_titleid']);

        $tmp = func_get_languages_alt('payment_method_'.$order['paymentid'], $shop_language);

        if (!empty($tmp)) {
            $order['payment_method_orig'] = $order['payment_method'];
            $order['payment_method']      = $tmp;
        }
    }

    $order['discounted_subtotal'] = $order['subtotal'] - $order['discount'] - $order['coupon_discount'];

    if ($order['giftcert_ids']) {

        $order['applied_giftcerts'] = explode("*", $order['giftcert_ids']);

        if ($order['applied_giftcerts']) {

            $tmp = array();

            foreach ($order['applied_giftcerts'] as $k=>$v) {

                if (empty($v))
                    continue;

                list(
                    $arr['giftcert_id'],
                    $arr['giftcert_cost']
                ) = explode(":", $v);

                $tmp[] = $arr;

            }

            $order['applied_giftcerts'] = $tmp;

        }

    }

    $shipping = func_query_first("SELECT shipping FROM $sql_tbl[shipping] WHERE shippingid='".$order["shippingid"]."'");

    if (!empty($shipping))
        $order['shipping'] = $shipping['shipping'];

    $order['shipping_exists'] = !empty($shipping);

    if (
        defined('AREA_TYPE')
        && (
            AREA_TYPE != 'C'
            ||
                (
                    defined('ORDER_PLACEMENT_PROCESS')
                    && AREA_TYPE == 'C'
                    && $config['Email']['show_cc_info'] == "Y"
                )
        )
    ) {

        $order_details_crypt_type = func_get_crypt_type($order['details']);

        if ($order_details_crypt_type != 'C' || func_get_crypt_key("C") !== false) {

            $order['details'] = text_decrypt($order['details']);

            if (is_null($order['details'])) {

                $order['details'] = func_get_langvar_by_name('err_data_corrupted');
                $order['details_corrupted'] = true;

                x_log_flag('log_decrypt_errors', 'DECRYPT', "Could not decrypt order details for the order ".$orderid, true);
                x_log_flag('log_activity', 'ACTIVITY', "'$login' user could not decrypt order details for the order '$orderid'");

            } else {

                $order['details'] = stripslashes($order['details']);

                x_log_flag('log_activity', 'ACTIVITY', "'$login' user has decrypted order details for the order '$orderid'");

            }

        } else {

            $order['details'] = func_get_langvar_by_name('txt_this_data_encrypted');
            $order['details_encrypted'] = true;

        }

    } else {

        $order['details'] = '';

    }

    $order['notes'] = stripslashes($order['notes']);
    $order['extra'] = @unserialize($order['extra']);

    $extras = func_query("SELECT khash, value FROM $sql_tbl[order_extras] WHERE orderid = '$orderid'");

    if (!empty($extras)) {
        foreach($extras as $v)
            $order['extra'][$v['khash']] = $v['value'];
    }

    if (
        defined('AREA_TYPE')
        && AREA_TYPE != 'C'
        && isset($order['extra']['advinfo'])
    ) {

        $order['extra']['advinfo'] = text_decrypt($order['extra']['advinfo']);

        if (is_null($order['extra']['advinfo'])) {

            $order['extra']['advinfo'] = func_get_langvar_by_name("err_data_corrupted");

            x_log_flag('log_decrypt_errors', 'DECRYPT', "Could not decrypt advanced info for the order ".$orderid, true);
            x_log_flag('log_activity', 'ACTIVITY', "'$login' user could not decrypt advanced info for the order '$orderid'");

        } else {

            $order['extra']['advinfo'] = stripslashes($order['extra']['advinfo']);

            x_log_flag('log_activity', 'ACTIVITY', "'$login' user has decrypted advanced info for the order '$orderid'");

        }

    } else {

        $order['extra']['advinfo'] = '';

    }

    if (
        $current_area != 'C'
        && !empty($active_modules['Stop_List'])
    ) {
        if (!func_ip_check($order['extra']['proxy_ip'] ? $order['extra']['proxy_ip'] : $order['extra']['ip'])) {
            $order['blocked'] = 'Y';
        }
    }

    if ($order['taxes_applied'])
        $order['applied_taxes'] = unserialize($order['taxes_applied']);

    if (
        !empty($order['applied_taxes'])
        && is_array($order['applied_taxes'])
    ) {

        foreach ($order['applied_taxes'] as $k => $v) {
            $order['applied_taxes'][$k]['tax_display_name'] = func_get_order_tax_name($v);

        }

    }

    if (preg_match("/NetBanx Reference: ([\w\d]+)/iSs", $order['details'], $preg)) {
        $order['netbanx_reference'] = $preg[1];
    }

    // Assign the display_* vars for displaying in the invoice

    if (
        @$order['extra']['tax_info']['display_taxed_order_totals'] == 'Y'
        && !empty($order['extra']['tax_info']['taxed_subtotal'])
    ) {

        $order['display_subtotal'] = $order['extra']['tax_info']['taxed_subtotal'];

    } else {

        $order['display_subtotal'] = $order['subtotal'];

    }

    if (
        @$order['extra']['tax_info']['display_taxed_order_totals'] == 'Y'
        && !empty($order['extra']['tax_info']['taxed_discounted_subtotal'])
    ) {

        $order['display_discounted_subtotal'] = $order['extra']['tax_info']['taxed_discounted_subtotal'];

    } else {

        $order['display_discounted_subtotal'] = $order['discounted_subtotal'];

    }

    if (
        @$order['extra']['tax_info']['display_taxed_order_totals'] == 'Y'
        && !empty($order['extra']['tax_info']['taxed_shipping'])
    ) {

        $order['display_shipping_cost'] = $order['extra']['tax_info']['taxed_shipping'];

    } else {

        $order['display_shipping_cost'] = $order['shipping_cost'];

    }

    list(
        $order['b_address'],
        $order['b_address_2']
    ) = preg_split("/[\r\n]+/", $order['b_address']);

    $order['b_statename']   = func_get_state($order['b_state'], $order['b_country']);
    $order['b_countryname'] = func_get_country($order['b_country']);

    list(
        $order['s_address'],
        $order['s_address_2']
    ) = preg_split("/[\r\n]+/", $order['s_address']);

    $order['s_statename']   = func_get_state($order['s_state'], $order['s_country']);
    $order['s_countryname'] = func_get_country($order['s_country']);

    if ($config['General']['use_counties'] == 'Y') {

        $order['b_countyname'] = func_get_county($order['b_county']);
        $order['s_countyname'] = func_get_county($order['s_county']);

    }

    return $order;
}

/**
 * This function returns data about specified order ($orderid)
 */
function func_order_data($orderid)
{
    global $sql_tbl, $config, $smarty, $active_modules, $current_area, $xcart_dir;
    global $xcart_catalogs, $shop_language;

    $join        = '';
    $gc_add_date = ", add_date+'" . $config["Appearance"]["timezone_offset"] . "' as add_date";
    $fields      = $gc_add_date;

    if (!empty($active_modules['Egoods'])) {
        $join .= " LEFT JOIN $sql_tbl[download_keys] ON $sql_tbl[order_details].itemid=$sql_tbl[download_keys].itemid AND $sql_tbl[download_keys].productid=$sql_tbl[order_details].productid";

        $fields .= ", $sql_tbl[download_keys].download_key, $sql_tbl[download_keys].expires";
    }

    $products = func_query("SELECT $sql_tbl[order_details].itemid, $sql_tbl[products].*, $sql_tbl[order_details].*, IF($sql_tbl[products].productid IS NULL, 'Y', '') as is_deleted, IF($sql_tbl[order_details].product = '', $sql_tbl[products].product, $sql_tbl[order_details].product) as product $fields FROM $sql_tbl[order_details] LEFT JOIN $sql_tbl[products] ON $sql_tbl[order_details].productid = $sql_tbl[products].productid $join WHERE $sql_tbl[order_details].orderid='$orderid'");

    if (!is_array($products))
        $products = array();

    // If products are not present in products table, but they are present in
    // order_details, then create fake $products from order_details data

    $is_returns = false;

    if (
        !empty($products)
        && !empty($active_modules['RMA'])
    ) {
        foreach ($products as $k => $v) {

            $returns = $products[$k]['returns'] = func_query("SELECT * FROM $sql_tbl[returns] WHERE itemid = '$v[itemid]'");

            if (!empty($returns)) {

                $is_returns = true;
                $products[$k]['returned_to_stock'] = 0;

                foreach ($returns as $ret) {

                    if (in_array($ret['status'], array('A','R','C'))) {

                        settype($products[$k]['returns_sum_'.$ret["status"]], 'int');
                        $products[$k]['returns_sum_'.$ret["status"]] += $ret["amount"];
                        $products[$k]['returned_to_stock'] += $ret["returned_amount"];

                    }

                }

            }

        }

    }

    $giftcerts = func_query("SELECT * $gc_add_date FROM $sql_tbl[giftcerts] WHERE orderid = '$orderid'");

    if (
        !empty($giftcerts)
        && $config['General']['use_counties'] == 'Y'
    ) {

        foreach ($giftcerts as $k => $v) {

            if (!empty($v['recipient_county']))
                $giftcerts[$k]['recipient_countyname'] = func_get_county($v['recipient_county']);

        }

    }

    $order = func_select_order($orderid);

    if (!$order)
        return false;

    $order['is_returns'] = $is_returns;

    if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[order_details], $sql_tbl[download_keys] WHERE $sql_tbl[order_details].orderid = '$orderid' AND $sql_tbl[order_details].itemid = $sql_tbl[download_keys].itemid")) {

        $order['is_egood'] = 'Y';

    } elseif (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[order_details], $sql_tbl[products] WHERE $sql_tbl[order_details].orderid = '$orderid' AND $sql_tbl[order_details].productid = $sql_tbl[products].productid AND $sql_tbl[products].distribution != ''")) {

        $order['is_egood'] = 'E';

    }

    // Get PayPal transactions data
    if (
        isset($order['extra']['paypal_txnid'])
        || isset($order['extra']['pnref'])
    ) {
        x_load('paypal');

        $order['paypal'] = func_paypal_get_order_data($orderid);
    }

    $userinfo = func_query_first("SELECT *, date+'".$config["Appearance"]["timezone_offset"]."' as date FROM $sql_tbl[orders] WHERE orderid = '$orderid'");

    if (
        isset($order['extra']['additional_fields'])
        && is_array($order['extra']['additional_fields'])
    ) {

        foreach($order['extra']['additional_fields'] as $k => $v) {

            $additional_field_title = func_get_languages_alt('lbl_register_field_'.$v['fieldid'], $shop_language, true);

            if (!empty($additional_field_title)) {

                $order['extra']['additional_fields'][$k]['title'] = $additional_field_title;

            }

        }

        $userinfo['additional_fields'] = $order["extra"]['additional_fields'];

    }

    $userinfo['titleid']   = func_detect_title($userinfo['title'],$order["language"]);
    $userinfo['b_titleid'] = func_detect_title($userinfo['b_title'],$order["language"]);
    $userinfo['s_titleid'] = func_detect_title($userinfo['s_title'],$order["language"]);

    if ($current_area == 'C') {

        $userinfo['title']   = func_get_title($userinfo['titleid']);
        $userinfo['b_title'] = func_get_title($userinfo['b_titleid']);
        $userinfo['s_title'] = func_get_title($userinfo['s_titleid']);

    }

    if ($order['userid'] > 0) {

        // Get info about customer
        $_userinfo = func_userinfo($userinfo['userid'], 'C', false, false, 'H', false);

    } 
    
    if (empty($_userinfo)) {

        // Anonymous profile (zero userid)

        $_anonymous_userinfo = $userinfo;
        @$_anonymous_userinfo['address']['S'] = func_create_address($userinfo, 'S');
        @$_anonymous_userinfo['address']['B'] = func_create_address($userinfo, 'B');
        func_set_anonymous_userinfo($_anonymous_userinfo);

        $_userinfo = func_userinfo(0, 'C', false, false, 'H');

    }

    $userinfo = func_array_merge($_userinfo, $userinfo);

    unset($_userinfo);

    list(
        $userinfo['b_address'],
        $userinfo['b_address_2']
    ) = preg_split("/[\r\n]+/", $userinfo['b_address']);

    list(
        $userinfo['s_address'],
        $userinfo['s_address_2']
    ) = preg_split("/[\r\n]+/", $userinfo['s_address']);

    $userinfo['s_countryname'] = $userinfo['s_country_text'] = func_get_country($userinfo['s_country']);
    $userinfo['s_statename']   = $userinfo['s_state_text'] = func_get_state($userinfo['s_state'], $userinfo['s_country']);
    $userinfo['b_statename']   = func_get_state($userinfo['b_state'], $userinfo['b_country']);
    $userinfo['b_countryname'] = func_get_country($userinfo['b_country']);

    if ($config['General']['use_counties'] == 'Y') {

        $userinfo['b_countyname'] = func_get_county($userinfo['b_county']);
        $userinfo['s_countyname'] = func_get_county($userinfo['s_county']);

    }

    $userinfo['s_gmap'] = func_get_gmap($userinfo);
    $userinfo['b_gmap'] = func_get_gmap($userinfo, false);

    if (!$products)
        $products = array ();

    if (preg_match("/(free_ship|percent|absolute)(?:``)(.+)/S", $order['coupon'], $found)) {

        $order['coupon'] = $found[2];
        $order['coupon_type'] = $found[1];

    }

    $order['extra']['tax_info']['product_tax_name'] = '';

    $_product_taxes = array();

    // prepare several service arrays to sort configurable and non-configurable products
    $parentProducts = array();
    $nonparentProducts = array();

    foreach ($products as $k => $v) {

        if ($current_area != 'C')
            $v['provider_login'] = func_query_first_cell("SELECT login FROM $sql_tbl[customers] WHERE id='$v[provider]'");

        if (!empty($active_modules['Extra_Fields']) && $v['is_deleted'] != 'Y') {

            $v['extra_fields'] = func_query("SELECT $sql_tbl[extra_fields].*, $sql_tbl[extra_field_values].*, IF($sql_tbl[extra_fields_lng].field != '', $sql_tbl[extra_fields_lng].field, $sql_tbl[extra_fields].field) as field FROM $sql_tbl[extra_field_values], $sql_tbl[extra_fields] LEFT JOIN $sql_tbl[extra_fields_lng] ON $sql_tbl[extra_fields].fieldid = $sql_tbl[extra_fields_lng].fieldid AND $sql_tbl[extra_fields_lng].code = '$shop_language' WHERE $sql_tbl[extra_fields].fieldid = $sql_tbl[extra_field_values].fieldid AND $sql_tbl[extra_field_values].productid = '$v[productid]' AND $sql_tbl[extra_fields].active = 'Y' ORDER BY $sql_tbl[extra_fields].orderby");

        }

        if ($v['extra_data']) {

            $v['extra_data'] = unserialize($v['extra_data']);

            if (is_array(@$v['extra_data']['display'])) {
                foreach ($v['extra_data']['display'] as $i=>$j) {
                    $v['display_'.$i] = $j;
                }
            }

            if (is_array($v['extra_data']['taxes'])) {
                foreach ($v['extra_data']['taxes'] as $i=>$j) {
                    if ($j['tax_value'] > 0) {
                        //Get saved tax_display_name for current language bt:0096284
                        $v['extra_data']['taxes'][$i]['tax_display_name'] = $_product_taxes[$i] = func_get_order_tax_name($order['applied_taxes'][$i]);
                    }
                }
            }

            if (!empty($active_modules['Gift_Registry']) && !empty($v['extra_data']['event_data']))
                $v['event_data'] = $v['extra_data']['event_data'];

            if (!empty($active_modules['Product_Configurator']) && !empty($v['extra_data']['price_show_sign']))
                $v['price_show_sign'] = true;
        }

        if (!empty($active_modules['Product_Options'])) {
            $v['product_options_txt'] = !empty($v["extra_data"]["product_options_alt"][$shop_language])
                ? $v['extra_data']['product_options_alt'][$shop_language]
                : $v['product_options'];
        }

        $v['original_price'] = $v['ordered_price'] = $v['price'];

        $v['price_deducted_tax'] = 'Y';
        $v["subtotal"] = $v["extra_data"]["subtotal"];
        $v["weight"] = $v["extra_data"]["weight"];

        // Get the original price (current price in the database)

        if ($v['is_deleted'] != 'Y') {

            $v['original_price'] = func_query_first_cell("SELECT MIN($sql_tbl[pricing].price) FROM $sql_tbl[pricing] WHERE $sql_tbl[pricing].productid = '$v[productid]' AND $sql_tbl[pricing].membershipid IN ('$userinfo[membershipid]', '0') AND $sql_tbl[pricing].quantity <= '$v[amount]' AND $sql_tbl[pricing].variantid = '0'");

            if (
                !empty($active_modules['Product_Options'])
                && $v['extra_data']['product_options']
            ) {
                list($variant, $product_options) = func_get_product_options_data($v['productid'], $v['extra_data']['product_options'],$userinfo['membershipid']);

                if ($product_options === false) {

                    unset($product_options);

                } else {

                    if (empty($variant['price']))
                        $variant['price'] = $v["original_price"];

                    $v['original_price'] = $variant['price'];

                    unset($variant['price']);

                    if ($product_options) {

                        foreach ($product_options as $o) {
                            if ($o['modifier_type'] == '%') {
                                $v['original_price'] += $v['original_price']*$o['price_modifier']/100;
                            } else {
                                $v['original_price'] += $o['price_modifier'];
                            }
                        }
                    }

                    $v['product_options'] = $product_options;

                    // Check current and saved product options set
                    if (!empty($v['product_options_txt'])) {

                        $flag_txt = true;

                        // Check saved product options
                        $count = 0;

                        foreach ($v['product_options'] as $opt) {
                            if (preg_match('/'.preg_quote($opt['class'],"/").": ".preg_quote($opt['option_name'], "/")."/Sm", $v['product_options_txt']))
                                $count++;
                        }

                        if ($count != count($v['product_options']))
                            $flag_txt = false;

                        // Check current product options set
                        if ($flag_txt) {

                            $count = 0;
                            $tmp = explode("\n", $v['product_options_txt']);

                            foreach ($tmp as $txt_row) {

                                if (!preg_match("/^([^:]+): (.*)$/S", trim($txt_row), $match))
                                    continue;

                                foreach ($v['product_options'] as $opt) {
                                    if ($match[1] == $opt['class'] && $match[2] == trim($opt['option_name'])) {
                                        $count++;
                                        break;
                                    }
                                }
                            }

                            if ($count != count($tmp))
                                $flag_txt = false;
                        }

                        // Force display saved product options set
                        // if saved and current product options sets wasn't equal
                        if (!$flag_txt)
                            $v['force_product_options_txt'] = true;
                    }

                    if (!empty($variant)) {

                        if (!empty($v['force_product_options_txt'])) {
                            // Restore saved data from old variant
                            $variant['productcode'] = $v['productcode'];
                        }

                        $v = func_array_merge($v, $variant);

                    }

                }

            }

        } // if ($v['is_deleted'] != 'Y')

        $products[$k] = $v;

        if (
            !empty($active_modules['Product_Configurator'])
            && !empty($v['extra_data']['pconf'])
        ) {

            if (!empty($v['extra_data']['pconf']['parent'])) {

                $parentProducts[$v['extra_data']['pconf']['parent']]['children'][$k] = $v;

            } else {

                $parentProducts[@$v['extra_data']['pconf']['cartid']]['parent'] = $v;

            }

        } else {

            $nonparentProducts[$k] = $v;

        }

    } // foreach ($products as $k => $v)

    $products = array();

    if (!empty($parentProducts)) {

        foreach ($parentProducts as $parentGroup) {

            $products[] = $parentGroup['parent'];

            if (!empty($parentGroup['children'])) {

                foreach($parentGroup['children'] as $child) {

                    $products[] = $child;

                }

            }

        }

    }

    if (!empty($nonparentProducts)) {

        foreach ($nonparentProducts as $product) {

            $products[] = $product;

        }

    }

    unset($nonparentProducts, $parentProducts);

    $products = func_translate_products($products, $shop_language);

    if (count($_product_taxes) == 1) {
        $order['extra']['tax_info']['product_tax_name'] = array_pop($_product_taxes);
    }

    if (
        !empty($order['coupon_type'])
        && $order['coupon_type'] == 'free_ship'
    ) {
        $order['shipping_cost']               = $order['coupon_discount'];
        $order['discounted_subtotal']         += $order['coupon_discount'];
        $order['display_discounted_subtotal'] += $order['coupon_discount'];
    }

    $order['discounted_subtotal'] = price_format($order['discounted_subtotal']);

    if (!empty($active_modules['Google_Checkout']))
        include $xcart_dir.'/modules/Google_Checkout/get_order_data.php';

    if (!empty($active_modules['Advanced_Order_Management'])) {

        $history = func_aom_get_history($orderid);

        if (!empty($history)) {
            $order['history'] = $history;
        }

    }

    if (
        isset($order['extra']['giftwrap'])
        && is_array($order['extra']['giftwrap'])
    ) {
        $order = array_merge($order, $order['extra']['giftwrap']);
    }

    // Assemble order payments info
    x_load('payment');

    $order['capture_enable'] = func_order_can_captured(intval($orderid));

    if (
        !$order['capture_enable']
        && $order['status'] == 'A'
    ) {

        $order['non_capture_message'] = func_payment_get_non_capture_message($order['paymentid']);

    } elseif ($order['capture_enable']) {

        // Can Capture / Void
        $order['void_enable']        = func_order_is_voided(intval($orderid));
        $order['capture_full_total'] = func_get_order_full_total($orderid);
        $order['capture_noninit']    = $order['capture_full_total'] != $order['init_total'];
        $order['capture_limit']      = func_check_capture_limit(intval($orderid));

    } elseif (
        in_array($order['status'], array('P', 'C'))
        && func_payment_get_refund_mode($order['paymentid'], $orderid)
    ) {
        // Can Refund
        $order['charge_info'] = array(
            'total'          => isset($order['extra']['captured_total']) ? $order['extra']['captured_total'] : func_get_order_full_total($orderid),
            'refunded_total' => isset($order['extra']['refunded_total']) ? $order['extra']['refunded_total'] : 0,
            'refund_mode'    => func_payment_get_refund_mode($order['paymentid'], $orderid),
        );

        $order['charge_info']['charged_total'] -= $order['charge_info']['refunded_total'];

        if (isset($order['extra']['refund_avail'])) {

            $order['charge_info']['refund_avail'] = $order['extra']['refund_avail'];

        } else {

            $order['charge_info']['refund_avail'] = $order['charge_info']['total'] - $order['charge_info']['refunded_total'];

        }

    }

    if (
        in_array($order['status'], array('P', 'C', 'A', 'Q'))
        && isset($order['extra']['fmf_blocked'])
        && $order['extra']['fmf_blocked'] == 'Y'
    ) {
        // Payment is blocked (Fraud management filter)
        $order['fmf'] = array(
            'blocked'     => !isset($order['extra']['fmf_action']) || !in_array($order['extra']['fmf_action'], array('A', 'D')),
            'can_accept'  => func_order_can_accept($orderid),
            'can_decline' => func_order_can_decline($orderid),
            'action'      => isset($order['extra']['fmf_action']) && in_array($order['extra']['fmf_action'], array('A', 'D'))
                ? $order['extra']['fmf_action']
                : false,
        );
    }

    $order['can_get_info'] = func_order_can_get_info($orderid);

    return array(
        'order'     => $order,
        'products'  => $products,
        'userinfo'  => $userinfo,
        'giftcerts' => $giftcerts,
    );
}

/**
 * This function increments product rating
 */
function func_increment_rating($productid)
{
    global $sql_tbl;

    db_query("UPDATE $sql_tbl[products] SET rating=rating+1 WHERE productid='$productid'");
}

/**
 * Decrease number of products in stock and increase product rating
 */
function func_decrease_quantity($products)
{
    if (!empty($products) && is_array($products)) {

        foreach ($products as $product) {

            func_increment_rating($product['productid']);

        }

    }

    func_update_quantity($products, false);
}

/**
 * This function creates order entry in orders table
 */
function func_place_order($payment_method, $order_status, $order_details, $customer_notes, $extra = array(), $extras = array())
{
    global $cart, $userinfo, $discount_coupon, $mail_smarty, $config, $active_modules, $single_mode, $partner, $adv_campaignid, $partner_clickid;
    global $sql_tbl, $to_customer;
    global $wlid;
    global $xcart_dir, $REMOTE_ADDR, $PROXY_IP, $CLIENT_IP, $gcheckout_saved_ips, $add_to_cart_time;
    global $dhl_ext_country_store, $ship_packages_uniq, $mailchimp_response;
    global $all_languages;

    if (!is_array($extra)) {
        $extra = array();
    }

    $mintime = 10;

    // Lock place order process

    func_lock('place_order');

    $userinfo = func_userinfo_from_scratch($userinfo, 'userinfo_for_payment');

    $userinfo['title']   = func_get_title(@$userinfo['titleid'], $config['default_admin_language']);
    $userinfo['b_title'] = func_get_title(@$userinfo['b_titleid'], $config['default_admin_language']);
    $userinfo['s_title'] = func_get_title(@$userinfo['s_titleid'], $config['default_admin_language']);

    $check_order = func_query_first("SELECT orderid FROM $sql_tbl[orders] WHERE userid='" . addslashes(@$userinfo["id"]) . "' AND '".XC_TIME."'-date < '$mintime'");

    if ($check_order) {
        func_unlock('place_order');

        return false;
    }

    if (!in_array($order_status, array('I', 'Q'))) {
        func_unlock('place_order');

        return false;
    }

    $userinfo['email'] = addslashes($userinfo['email']);

    $orderids = array ();

    // REMOTE_ADDR and PROXY_IP

    x_session_register('gcheckout_saved_ips');

    $extras['ip']       = isset($extras['is_gcheckout']) ? $gcheckout_saved_ips['ip'] : $CLIENT_IP;
    $extras['proxy_ip'] = isset($extras['is_gcheckout']) ? $gcheckout_saved_ips['proxy_ip'] : $PROXY_IP;

    x_session_unregister('gcheckout_saved_ips');

    if (!empty($cart['shipping_warning'])) {
        $extras['shipping_warning'] = $cart['shipping_warning'];
    }

    if ($add_to_cart_time > 0) {
        $extras['add_to_cart_time'] = XC_TIME - $add_to_cart_time;
    }

    if (!empty($_COOKIE['personal_client_id'])) {
        $extras['personal_client_id'] = $_COOKIE['personal_client_id'];
    }

    srand();

    $extras['unique_id'] = md5(func_microtime() . rand(0, 1000000));

    // Validate cart contents

    if (!func_cart_is_valid($cart, $userinfo)) {
        // current state of cart is not valid and we cannot
        // re-calculate it now

        func_unlock('place_order');

        return false;
    }

    $products = $cart['products'];

    func_decrease_quantity($products);

    $giftcert_discount = $cart['giftcert_discount'];

    if ($cart['applied_giftcerts']) {

        foreach ($cart['applied_giftcerts'] as $k=>$v) {

            $giftcert_str = join("*", array(@$giftcert_str, "$v[giftcert_id]:$v[giftcert_cost]"));

            db_query("UPDATE $sql_tbl[giftcerts] SET status='U' WHERE gcid='$v[giftcert_id]'");

        }

    }

    $giftcert_id = @$cart['giftcert_id'];

    if (
        !empty($active_modules['Anti_Fraud']) 
        && defined('IS_AF_CHECK') 
        && !isset($extra['is_gcheckout']) 
        && (
            $cart['total_cost'] > 0 
            || $config['Anti_Fraud']['check_zero_order'] == 'Y'
        )
    ) {
        include $xcart_dir . '/modules/Anti_Fraud/anti_fraud.php';
    }

    $_code = func_query_first_cell("SELECT code FROM $sql_tbl[shipping] WHERE shippingid='$cart[shippingid]'");

    $extra['additional_fields'] = $userinfo['additional_fields'];

    if (
        !empty($dhl_ext_country_store) 
        && !empty($cart['shippingid'])
    ) {
        $is_dhl_shipping = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[shipping] WHERE shippingid = '$cart[shippingid]' AND code = 'ARB' AND destination = 'I'") > 0;

        if ($is_dhl_shipping)
            $extra['dhl_ext_country'] = $dhl_ext_country_store;
    }

    if (
        !empty($active_modules['Mailchimp_Subscription'])
        && is_array($mailchimp_response)
    ) {

        foreach ($mailchimp_response as $mck => $mcv) {

            $extra['mailchimp_' . $mck] = $mcv;

        }

    }

    foreach ($cart['orders'] as $current_order) {

        $_extra = $extra;
        $_extra['tax_info'] = array (
            'display_taxed_order_totals'      => $config['Taxes']['display_taxed_order_totals'],
            'display_cart_products_tax_rates' => $config['Taxes']['display_cart_products_tax_rates'] == 'Y',
            'taxed_subtotal'                  => $current_order['display_subtotal'],
            'taxed_discounted_subtotal'       => $current_order['display_discounted_subtotal'],
            'taxed_shipping'                  => $current_order['display_shipping_cost'],
        );

        if (!empty($current_order['discount_info']))
            $_extra['discount_info'] = $current_order['discount_info'];

        if (!empty($current_order['discount_coupon_info']))
            $_extra['discount_coupon_info'] = $current_order['discount_coupon_info'];

        if (!empty($active_modules['Special_Offers']))
            include $xcart_dir . '/modules/Special_Offers/place_order_extra.php';

        include $xcart_dir . '/modules/Gift_Registry/place_order_extra.php';

        if (!$single_mode) {

            $giftcert_discount = $current_order['giftcert_discount'];
            $giftcert_str = '';

            if (!empty($current_order['applied_giftcerts'])) {

                foreach($current_order['applied_giftcerts'] as $k => $v) {

                    $giftcert_str = join("*", array($giftcert_str, "$v[giftcert_id]:$v[giftcert_cost]"));

                }

            }

        } else {

            $current_order['payment_surcharge'] = $cart['payment_surcharge'];

        }

        // Save tax names for all languages bt:0096284
        if (is_array($current_order['taxes'])) {
            foreach($current_order['taxes'] as $ktax => $vtax) {
                $current_order['taxes'][$ktax]['intl_tax_names'] = func_get_languages_alt('tax_'.$vtax['taxid'], false, false, false, true);
            }
        }
        $taxes_applied = addslashes(serialize($current_order['taxes']));

        $discount_coupon = $current_order['coupon'];

        if (!empty($current_order['coupon'])) {
            $current_order['coupon'] = func_query_first_cell("SELECT coupon_type FROM $sql_tbl[discount_coupons] WHERE coupon='".addslashes($current_order["coupon"])."'")."``".$current_order["coupon"];
        }

        $save_info = $userinfo;

        $userinfo['b_address'] .= "\n".@$userinfo['b_address_2'];
        $userinfo['s_address'] .= "\n".@$userinfo['s_address_2'];

        // Insert into orders

        $insert_data = array (
            'userid'            => !empty($userinfo['id']) ? $userinfo['id'] : 0,
            'membershipid'      => @$userinfo['membershipid'],
            'membership'        => addslashes(@$userinfo['membership']),
            'total'             => $current_order['total_cost'],
            'init_total'        => $current_order['total_cost'],
            'giftcert_discount' => $giftcert_discount,
            'giftcert_ids'      => @$giftcert_str,
            'subtotal'          => $current_order['subtotal'],
            'shipping_cost'     => $current_order['shipping_cost'],
            'shippingid'        => $cart['shippingid'],
            'shipping'          => addslashes(func_query_first_cell("SELECT shipping FROM $sql_tbl[shipping] WHERE shippingid = '$cart[shippingid]'")),
            'tax'               => $current_order['tax_cost'],
            'taxes_applied'     => $taxes_applied,
            'discount'          => $current_order['discount'],
            'coupon'            => addslashes(@$current_order['coupon']),
            'coupon_discount'   => $current_order['coupon_discount'],
            'date'              => XC_TIME,
            'status'            => $order_status,
            'payment_method'    => addslashes($payment_method),
            'paymentid'         => $cart['paymentid'],
            'payment_surcharge' => $current_order['payment_surcharge'],
            'flag'              => 'N',
            'details'           => addslashes(text_crypt($order_details)),
            'customer_notes'    => $customer_notes,
            'clickid'           => $partner_clickid,
            'language'          => @$userinfo['language'],
            'extra'             => addslashes(serialize($_extra)),
        );

        // Generate access key for anonymous orders
        if (
            empty($userinfo['id'])
            || !is_numeric($userinfo['id'])
            || $userinfo['id'] <= 0
        ) {
            $insert_data['access_key'] = substr(md5(uniqid(rand())), 0, 16);
        }

        // copy userinfo
        $_fields = array (
            'title',
            'firstname',
            'lastname',
            'email',
            'url',
            'company',
            'tax_number',
            'tax_exempt',
        );

        foreach ($_fields as $k) {
            if (!isset($userinfo[$k]))
                continue;

            $insert_data[$k] = addslashes($userinfo[$k]);
        }

        $_fields = array (
            'title',
            'firstname',
            'lastname',
            'address',
            'city',
            'county',
            'state',
            'country',
            'zipcode',
            'zip4',
            'phone',
            'fax',
        );

        foreach (array('b_', 's_') as $p) {

            foreach ($_fields as $k) {

                $f = $p . $k;

                if (isset($userinfo[$f])) {
                    $insert_data[$f] = addslashes($userinfo[$f]);
                }

            }

        }

        $orderid = func_array2insert('orders', $insert_data);

        unset($insert_data);

        // Store packages for Shipping_Label_Generator
        if (!empty($active_modules['Shipping_Label_Generator'])) {

            x_session_register('ship_packages_uniq');

            $_pack_index = $current_order['provider'] . $_code;

            if (!empty($ship_packages_uniq[$_pack_index]))
                $extras['ship_packages_uniq' . $_code] = serialize($ship_packages_uniq[$_pack_index]);
        }

        if (!empty($extras) && is_array($extras)) {

            foreach ($extras as $k => $v) {

                if (strlen($v) > 0) {
                    func_array2insert(
                        'order_extras',
                        array(
                            'orderid' => $orderid,
                            'khash'   => addslashes($k),
                            'value'   => addslashes($v),
                        )
                    );
                }

            }

        }

        $userinfo = $save_info;

        $orderids[] = $orderid;

        $order = func_select_order($orderid);

        // Insert into order details

        if (!empty($products) && is_array($products)) {

        foreach ($products as $pk => $product) {

            if (
                $single_mode
                || $product['provider'] == $current_order['provider']
            ) {
                $product['price']                                     = price_format($product['price']);
                $product['extra_data']['product_options']             = $product['options'];
                $product['extra_data']['taxes']                       = $product['taxes'];
                $product['extra_data']['display']['price']            = price_format($product['display_price']);
                $product['extra_data']['display']['discounted_price'] = price_format($product['display_discounted_price']);
                $product['extra_data']['display']['subtotal']         = price_format($product['display_subtotal']);

                // For AOM func_is_shipping_method_allowable function bt:#92268
                $product['extra_data']['subtotal']                    = $product['subtotal'];
                $product['extra_data']['weight']                      = $product['weight'];

                if (empty($product['product_orig'])) {

                    $product['product_orig'] = $product['product'];

                }

                if(!empty($active_modules['Product_Options'])) {

                    $product['product_options'] = func_serialize_options($product['options']);

                    if (
                        $all_languages
                        && is_array($all_languages)
                        && count($all_languages) > 1
                    ) {
                        foreach($all_languages as $lng) {
                            $product['extra_data']['product_options_alt'][$lng['code']] = func_serialize_options($product['options'], false, $lng['code']);
                        }
                    }

                }

                if(!empty($active_modules['Gift_Registry'])) {
                    $product['extra_data']['event_data'] = @$product['event_data'];
                }

                if (isset($product['price_show_sign'])) {
                    $product['extra_data']['price_show_sign'] = true;
                }

                $insert_data = array (
                    'orderid'         => $orderid,
                    'productid'       => $product['productid'],
                    'product'         => addslashes($product['product_orig']),
                    'product_options' => addslashes($product['product_options']),
                    'amount'          => $product['amount'],
                    'price'           => $product['price'],
                    'provider'        => addslashes($product["provider"]),
                    'extra_data'      => addslashes(serialize($product["extra_data"])),
                    'productcode'     => addslashes($product['productcode']),
                );

                $products[$pk]['itemid'] = func_array2insert('order_details', $insert_data);

                unset($insert_data);

                // Insert into subscription_customers table (for subscription products)

                if (!empty($active_modules['Subscriptions']))
                    include $xcart_dir.'/modules/Subscriptions/subscriptions_cust.php';

                // Check if this product is in Wish list

                if (!empty($active_modules['Wishlist']))
                    include $xcart_dir.'/modules/Wishlist/place_order.php';

                if (
                    !empty($active_modules['Recommended_Products'])
                    && !empty($userinfo["id"])
                ) {
                    $rec_counter = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[stats_customers_products] WHERE productid='$product[productid]' AND userid='" . $userinfo["id"] . "'");

                    if (!empty($rec_counter)) {
                        func_array2update(
                            'stats_customers_products',
                            array(
                                'counter' => $rec_counter + 1,
                            ),
                            'productid=\'' . $product['productid'] . '\' AND userid=\'' . $userinfo['id'] . '\''
                        );
                    } else {
                        func_array2insert(
                            'stats_customers_products',
                            array(
                                'productid' => $product['productid'],
                                'userid'    => $userinfo['id'],
                                'counter'   => 1,
                            )
                        );
                    }
                }

            }

        } // foreach ($products as $pk => $product)

        }

        if (!empty($active_modules['XAffiliate'])) {

            // Partner commission
            if (!empty($partner)) {
                include $xcart_dir . '/include/partner_commission.php';
            }

            // Save link order -> advertising campaign
            if ($adv_campaignid)
                include $xcart_dir . '/include/adv_campaign_commission.php';
        }

        // Provider commission
        $current_order['orderid'] = $orderid;
        func_calculate_provider_commission($products, $current_order);

        if (
            (
                $single_mode
                || empty($current_order['provider'])
            ) && !empty($cart['giftcerts'])
        ) {

            foreach($cart['giftcerts'] as $gk => $giftcert) {

                $gcid = substr(strtoupper(md5(uniqid(rand()))), 0, 16);

                // status == Pending!

                $insert_data = array(
                    'gcid'                => $gcid,
                    'orderid'             => $orderid,
                    'purchaser'           => addslashes($giftcert['purchaser']),
                    'recipient'           => addslashes($giftcert['recipient']),
                    'send_via'            => $giftcert['send_via'],
                    'recipient_email'     => @$giftcert['recipient_email'],
                    'recipient_firstname' => addslashes(@$giftcert['recipient_firstname']),
                    'recipient_lastname'  => addslashes(@$giftcert['recipient_lastname']),
                    'recipient_address'   => addslashes(@$giftcert['recipient_address']),
                    'recipient_city'      => addslashes(@$giftcert['recipient_city']),
                    'recipient_county'    => @$giftcert['recipient_county'],
                    'recipient_state'     => addslashes(@$giftcert['recipient_state']),
                    'recipient_country'   => addslashes(@$giftcert['recipient_country']),
                    'recipient_zipcode'   => addslashes(@$giftcert['recipient_zipcode']),
                    'recipient_phone'     => addslashes(@$giftcert['recipient_phone']),
                    'message'             => addslashes($giftcert['message']),
                    'amount'              => $giftcert['amount'],
                    'debit'               => $giftcert['amount'],
                    'status'              => 'P',
                    'add_date'            => XC_TIME,
                );

                if ($giftcert['send_via'] == 'P') {
                    $insert_data['tpl_file'] = $giftcert['tpl_file'];
                }

                func_array2insert('giftcerts', $insert_data);

                unset($insert_data);

                $cart['giftcerts'][$gk]['gcid'] = $gcid;

                // Check if this giftcertificate is in Wish list

                if (!empty($active_modules['Wishlist'])) {
                    include $xcart_dir.'/modules/Wishlist/place_order.php';
                }

            } // foreach($cart['giftcerts'] as $gk => $giftcert)

        }

        // Mark discount coupons used

        if ($discount_coupon) {

            $_per_user = func_query_first_cell("SELECT per_user FROM $sql_tbl[discount_coupons] WHERE coupon = '".addslashes($discount_coupon)."'");

            if ($_per_user == 'Y') {

                $_need_to_update = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[discount_coupons_login] WHERE coupon = '".addslashes($discount_coupon)."' AND userid = '" . $userinfo['id'] . "'");

                if ($_need_to_update > 0) {

                    db_query("UPDATE $sql_tbl[discount_coupons_login] SET times_used = times_used+1 WHERE coupon = '".addslashes($discount_coupon)."' AND userid = '" . $userinfo['id'] . "'");

                } else {

                    func_array2insert(
                        'discount_coupons_login',
                        array(
                            'coupon'     => addslashes($discount_coupon),
                            'userid'     => $userinfo['id'],
                            'times_used' => 1,
                        )
                    );

                }

            } else {

                db_query("UPDATE $sql_tbl[discount_coupons] SET times_used = times_used+1 WHERE coupon = '".addslashes($discount_coupon)."'");

                func_array2update(
                    'discount_coupons',
                    array('status' => 'U'),
                    "coupon = '".addslashes($discount_coupon)."' AND times_used = times"
                );
            }

            $discount_coupon = '';
        }

        // Mail template processing

        $admin_notify = (
            (
                $order_status == 'Q'
                && $config['Email_Note']['enable_order_notif'] == 'Y'
            ) || (
                $order_status == 'I'
                && $config['Email_Note']['enable_init_order_notif'] == 'Y'
            )
        );

        $customer_notify = (
            (
                $order_status == 'Q'
                && $config['Email_Note']['eml_order_q_notif_customer'] == 'Y'
            ) || (
                $order_status == 'I'
                && $config['Email_Note']['enable_init_order_notif_customer'] == 'Y'
            )
        );

        $customer_notify = (
            $customer_notify
            && !(
                defined('GOOGLE_CHECKOUT_CALLBACK')
                && $config['Google_Checkout']['gcheckout_disable_customer_notif'] == 'Y'
            )
        );

        if (!defined('ORDER_PLACEMENT_PROCESS'))
            define('ORDER_PLACEMENT_PROCESS', 1);

        $order_data = func_order_data($orderid);

        $mail_smarty->assign('products',  $order_data['products']);
        $mail_smarty->assign('giftcerts', $order_data['giftcerts']);
        $mail_smarty->assign('order',     $order_data['order']);
        $mail_smarty->assign('userinfo',  $order_data['userinfo']);

        $prefix = $order_status == 'I'
            ? 'init_'
            : '';

        if ($customer_notify) {

            // Notify customer by email

            $to_customer = !empty($userinfo['language'])
                ? $userinfo['language']
                : $config['default_customer_language'];

            $mail_smarty->assign('products', func_translate_products($order_data['products'], $to_customer));

            func_send_mail(
                $userinfo['email'],
                'mail/' . $prefix . 'order_customer_subj.tpl',
                'mail/' . $prefix . 'order_customer.tpl',
                $config['Company']['orders_department'],
                false
            );
        }

        if (!empty($order_data['order']['payment_method_orig'])) {

            $order_data['order']['payment_method'] = $order_data["order"]['payment_method_orig'];

            $mail_smarty->assign('order', $order_data['order']);

        }

        $mail_smarty->assign('products', $order_data['products']);

        if ($admin_notify) {

            // Notify orders department by email

            $mail_smarty->assign('show_order_details', 'Y');

            func_send_mail(
                $config['Company']['orders_department'],
                'mail/' . $prefix . 'order_notification_subj.tpl',
                'mail/order_notification_admin.tpl',
                $userinfo['email'],
                true,
                true
            );

            $mail_smarty->assign('show_order_details', '');

            // Notify provider (or providers) by email

            if (
                !$single_mode
                && $current_order['provider']
                && $config['Email_Note']['send_notifications_to_provider'] == 'Y'
            ) {
                $pr_result = func_query_first ("SELECT email, language FROM $sql_tbl[customers] WHERE id='$current_order[provider]'");

                $prov_email = $pr_result ['email'];

                if ($prov_email != $config['Company']['orders_department']) {

                    $to_customer = $pr_result['language'];

                    if (empty($to_customer))
                        $to_customer = $config['default_admin_language'];

                    func_send_mail(
                        $prov_email,
                        'mail/' . $prefix . 'order_notification_subj.tpl',
                        'mail/order_notification.tpl',
                        $userinfo['email'],
                        false
                    );

                }

            } elseif (
                $config['Email_Note']['send_notifications_to_provider'] == 'Y'
                && !empty($products)
                && is_array($products)
            ) {
                $providers = array();

                foreach($products as $product) {

                    $pr_result = func_query_first("SELECT email, language FROM $sql_tbl[customers] WHERE id='$product[provider]'");

                    if ($pr_result['email'])
                        $providers[$product['provider']] = $pr_result;

                }

                if ($providers) {

                    foreach ($providers as $prov_data) {

                        if ($prov_data['email'] == $config["Company"]["orders_department"])
                            continue;

                        $to_customer = $prov_data['language'];

                        if (empty($to_customer))
                            $to_customer = $config['default_admin_language'];

                        func_send_mail(
                            $prov_data['email'],
                            'mail/' . $prefix . 'order_notification_subj.tpl',
                            'mail/order_notification.tpl',
                            $userinfo['email'],
                            false
                        );
                    } // foreach ($providers as $prov_data)

                } // if ($providers)

            }

        } // if ($admin_notify)

        if (
            !empty($active_modules['Survey'])
            && defined('AREA_TYPE')
            && constant('AREA_TYPE') == 'C'
        ) {
            func_check_surveys_events('OPL', $order_data);
        }

        // Send notifications to orders department and providers when product amount in stock is low

        if ($config['General']['unlimited_products'] != 'Y') {

            foreach($order_data['products'] as $product) {

                if (!empty($product['distribution']) && $active_modules['Egoods'])
                    continue;

                if ($product['product_type'] == 'C' && !empty($active_modules['Product_Configurator']))
                    continue;

                if (
                    !empty($active_modules['Product_Options'])
                    && $product['extra_data']['product_options']
                ) {

                    $avail_now = func_get_options_amount($product['extra_data']['product_options'], $product['productid']);

                } else {

                    $avail_now = func_query_first_cell("SELECT avail FROM $sql_tbl[products] WHERE productid='".$product["productid"]."'");

                }

                if (
                    $product['low_avail_limit'] >= $avail_now
                    && $config['Email_Note']['eml_lowlimit_warning'] == 'Y'
                ) {

                    // Mail template processing

                    $product['avail'] = $avail_now;

                    $mail_smarty->assign('product', $product);

                    func_send_mail(
                        $config['Company']['orders_department'],
                        'mail/lowlimit_warning_notification_subj.tpl',
                        'mail/lowlimit_warning_notification_admin.tpl',
                        $config['Company']['orders_department'],
                        true
                    );

                    $pr_result = func_query_first ("SELECT email, language FROM $sql_tbl[customers] WHERE id='".$product["provider"]."'");

                    if (
                        !$single_mode
                        && $pr_result['email'] != $config['Company']['orders_department']
                        && $config['Email_Note']['eml_lowlimit_warning_provider'] == 'Y'
                    ) {
                        $to_customer = $pr_result['language'];

                        if (empty($to_customer))
                            $to_customer = $config['default_admin_language'];

                        func_send_mail(
                            $pr_result['email'],
                            'mail/lowlimit_warning_notification_subj.tpl',
                            'mail/lowlimit_warning_notification_admin.tpl',
                            $config['Company']['orders_department'],
                            false
                        );
                    }
                }

            } // foreach($order_data['products'] as $product)

        } // if ($config['General']['unlimited_products']!="Y")

        // Store in the order history
        if (!empty($active_modules['Advanced_Order_Management'])) {

            $details = array(
                'old_status' => '',
                'new_status' => $order_status,
            );

            func_aom_save_history($orderid, 'X', $details);

        }

    } // foreach ($cart['orders'] as $current_order)

    // Release previously created lock

    func_unlock('place_order');

    // Save orderids in the session
    if (
        empty($userinfo['id'])
        || !is_numeric($userinfo['id'])
        || $userinfo['id'] <= 0
    ) {
        global $session_orders;

        x_session_register('session_orders', array());

        $session_orders = func_array_merge($session_orders, $orderids);
    }

    return $orderids;
}

/**
 * This function change order status in orders table
 */
function func_change_order_status($orderids, $status, $advinfo = '')
{
    global $config, $mail_smarty, $active_modules, $current_area;
    global $sql_tbl, $to_customer;
    global $session_failed_transaction;
    global $REMOTE_ADDR, $login;

    $allowed_order_status = 'IQPBDFCA';

    if (!strstr($allowed_order_status, $status))
        return;

    if (!is_array($orderids)) $orderids = array($orderids);

    foreach ($orderids as $orderid) {

        $order_data = func_order_data($orderid);

        if (empty($order_data))
            continue;

        if (defined('ORDERS_LIST_UPDATE') && !empty($order_data['goid']))
            continue;

        $order = $order_data['order'];

        db_query("UPDATE $sql_tbl[orders] SET status='$status' WHERE orderid='$orderid'");

        func_store_advinfo($orderid, $advinfo);

        $send_notification = false;

        if (
            $status == 'P'
            && $order['status'] != 'P'
        ) {
            $flag = true;

            if (
                in_array($order['status'], array('I', 'Q'))
                && !empty($active_modules['Anti_Fraud'])
                && $config['Anti_Fraud']['anti_fraud_license']
                && (
                    $current_area != 'A'
                    && $current_area != 'P'
                )
                && !empty($order['extra']['Anti_Fraud'])
                && !isset($extra['is_gcheckout'])
            ) {
                $total_trust_score = $order['extra']['Anti_Fraud']['total_trust_score'];
                $available_request = $order['extra']['Anti_Fraud']['available_request'];
                $used_request      = $order['extra']['Anti_Fraud']['used_request'];

                if (
                    $total_trust_score > $config['Anti_Fraud']['anti_fraud_limit']
                    || (
                        $available_request <= $used_request
                        && $available_request > 0
                    )
                ) {
                    $flag = false;

                    db_query("UPDATE $sql_tbl[orders] SET status = 'Q' WHERE orderid = '$orderid'");

                    $send_notification = true;
                }
            }

            if ($flag) {
                func_process_order($orderid);
            }

        } elseif (
            $status == 'D'
            && $order['status'] != 'D'
            && $order['status'] != 'F'
        ) {

            if ($order['status'] == 'A' && func_order_is_voided($orderid)) {

                func_payment_do_void($orderid, 1);

            }

            func_decline_order($orderid, $status, $order['status']);

        } elseif (
            $status == 'F'
            && $order['status'] != 'F'
            && $order['status'] != 'D'
        ) {

            if ($order['status'] == 'A' && func_order_is_voided($orderid)) {

                func_payment_do_void($orderid);

            }

            func_decline_order($orderid, $status, $order['status']);

            if ($current_area == 'C')
                $session_failed_transaction ++;

        } elseif (
            $status == 'C'
            && $order['status'] != 'C'
        ) {

            func_complete_order($orderid);

        } elseif (
            $status == 'A'
            && $order['status'] == 'I'
        ) {

            func_authorize_order($orderid);

        } elseif (
            $status == 'Q'
            && $order['status'] == 'I'
            && $current_area != 'A'
            && $current_area != 'P'
        ) {

            $send_notification = true;

        }

        // Decrease quantity in stock when 'declined' or 'failed' order is became 'completed', 'processed' or 'queued'

        if (
            $status != $order['status']
            && in_array($order['status'], array('D', 'F'))
            && in_array($status, array('C','P','Q','I','B'))
        ) {
            func_update_quantity($order_data['products'],false);
        }

        if ($send_notification) {

            // Send notification to customer
            $order_data['order']['status'] = $status;

            $to_customer = $order_data['userinfo']['language']
                ? $order_data['userinfo']['language']
                : $config['default_customer_language'];

            $mail_smarty->assign('products',  func_translate_products($order_data['products'], $to_customer));
            $mail_smarty->assign('giftcerts', $order_data['giftcerts']);
            $mail_smarty->assign('order',     $order_data['order']);
            $mail_smarty->assign('userinfo',  $order_data['userinfo']);

            func_send_mail(
                $order_data['userinfo']['email'],
                'mail/order_customer_subj.tpl',
                'mail/order_customer.tpl',
                $config['Company']['orders_department'],
                false
            );

        }

        if (
            !empty($active_modules['Advanced_Order_Management'])
            && $order['status'] != $status
            && !defined('ORDER_HISTORY_SAVED')
        ) {
            $details = array(
                'old_status' => $order['status'],
                'new_status' => $status,
            );

            func_aom_save_history($orderid, 'X', $details);
        }
    } // foreach ($orderids as $orderid)

    $op_message = "Login: $login\nIP: $REMOTE_ADDR\nOperation: change status of orders (" . implode(',', $orderids) . ") to '$status'\n----";

    x_log_flag('log_orders_change_status', 'ORDERS', $op_message, true);

}

/**
 * This function performs activities nedded when order is processed
 */
function func_process_order($orderids)
{
    global $config, $mail_smarty, $active_modules;
    global $sql_tbl, $partner, $to_customer;
    global $single_mode;
    global $xcart_dir;
    global $current_area;

    if (empty($orderids))
        return false;

    if (!is_array($orderids))
        $orderids = array($orderids);

    foreach($orderids as $orderid) {

        if (empty($orderid))
            continue;

        $order_data = func_order_data($orderid);

        if (empty($order_data))
            continue;

        $order     = $order_data['order'];
        $userinfo  = $order_data['userinfo'];
        $products  = $order_data['products'];
        $giftcerts = $order_data['giftcerts'];

        $mail_smarty->assign('customer',  $userinfo);
        $mail_smarty->assign('products',  $products);
        $mail_smarty->assign('giftcerts', $giftcerts);
        $mail_smarty->assign('order',     $order);

        // Order processing routine
        // Send gift certificates

        if ($order['applied_giftcerts']) {

            // Search for enabled to applying GC

            $flag = true;

            foreach ($order['applied_giftcerts'] as $k=>$v) {

                $res = func_query_first("SELECT gcid FROM $sql_tbl[giftcerts] WHERE gcid='$v[giftcert_id]' AND debit>='$v[giftcert_cost]'");

                if (!$res['gcid']) {

                    $flag = false;

                    break;
                }
            }

            // Decrease debit for applied GC

            if (!$flag)
                return false;

            foreach ($order['applied_giftcerts'] as $k=>$v) {
                db_query("UPDATE $sql_tbl[giftcerts] SET debit=debit-'$v[giftcert_cost]' WHERE gcid='$v[giftcert_id]'");
                db_query("UPDATE $sql_tbl[giftcerts] SET status='A' WHERE debit>'0' AND gcid='$v[giftcert_id]'");
                db_query("UPDATE $sql_tbl[giftcerts] SET status='U' WHERE debit<='0' AND gcid='$v[giftcert_id]'");
            }

        }

        if ($giftcerts) {

            foreach ($giftcerts as $giftcert) {

                db_query("update $sql_tbl[giftcerts] set status='A' where gcid='$giftcert[gcid]'");

                if ($giftcert['send_via'] == 'E')
                    func_send_gc($userinfo['email'], $giftcert, $userinfo['userid']);

            }

        }

        // Send E-goods download keys

        if (!empty($active_modules['Egoods']))
            include $xcart_dir.'/modules/Egoods/send_keys.php';

        // Send mail notifications

        if ($config['Email_Note']['eml_order_p_notif_provider'] == 'Y') {

            $providers = func_query("SELECT provider FROM $sql_tbl[order_details] WHERE $sql_tbl[order_details].orderid='$orderid' GROUP BY provider");

            if (is_array($providers)) {

                foreach($providers as $provider) {

                    $email_pro = func_query_first_cell("SELECT email FROM $sql_tbl[customers] WHERE id='$provider[provider]'");

                    if (
                        !empty($email_pro)
                        && $email_pro != $config['Company']['orders_department']
                    ) {
                        $to_customer = func_query_first_cell ("SELECT language FROM $sql_tbl[customers] WHERE id='$provider[provider]'");

                        if(empty($to_customer))
                            $to_customer = $config['default_admin_language'];

                        func_send_mail(
                            $email_pro,
                            'mail/order_notification_subj.tpl',
                            'mail/order_notification.tpl',
                            $config['Company']['orders_department'],
                            false
                        );
                    }

                } // foreach($providers as $provider)

            } // if (is_array($providers))

        } // if ($config['Email_Note']['eml_order_p_notif_provider'] == 'Y')

        $to_customer = func_query_first_cell ("SELECT language FROM $sql_tbl[customers] WHERE id='$userinfo[userid]'");

        if (empty($to_customer))
            $to_customer = $config['default_customer_language'];

        if ($config['Email_Note']['eml_order_p_notif_customer'] == 'Y') {

            $mail_smarty->assign('products', func_translate_products($products, $to_customer));

            $_userinfo = $userinfo;

            $userinfo['title']   = func_get_title($userinfo['titleid'], $to_customer);
            $userinfo['b_title'] = func_get_title($userinfo['b_titleid'], $to_customer);
            $userinfo['s_title'] = func_get_title($userinfo['s_titleid'], $to_customer);

            $mail_smarty->assign('customer', $userinfo);

            $mail_body_template = $current_area == 'C'
                ? 'mail/order_customer.tpl'
                : 'mail/order_customer_processed.tpl';

            func_send_mail(
                $userinfo['email'],
                'mail/order_cust_processed_subj.tpl',
                $mail_body_template,
                $config['Company']['orders_department'],
                false
            );

            $userinfo = $_userinfo;

            unset($_userinfo);

        }

        $mail_smarty->assign('products',           $products);
        $mail_smarty->assign('show_order_details', 'Y');

        if ($config['Email_Note']['eml_order_p_notif_admin'] == 'Y') {

            $to_customer = $config['default_admin_language'];

            func_send_mail(
                $config['Company']['orders_department'],
                'mail/order_notification_subj.tpl',
                'mail/order_notification_admin.tpl',
                $config['Company']['orders_department'],
                true,
                true
            );
        }

        $mail_smarty->assign('show_order_details', '');

        // Update statistics for sold products

        if (!empty($active_modules['Advanced_Statistics'])) {
            include $xcart_dir.'/modules/Advanced_Statistics/prod_sold.php';
        }

        if (!empty($active_modules['SnS_connector'])) {

            $_old = $_COOKIE;

            $_COOKIE['personal_client_id'] = $order['extra']['personal_client_id'];

            func_generate_sns_action('Order', $orderid);

            $_COOKIE = $_old;

        }

        if (
            !empty($active_modules['Survey'])
            && !empty($userinfo)
        ) {
            func_check_surveys_events('OPP', $order_data, $userinfo['userid']);
            func_check_surveys_events('OPB', $order_data, $userinfo['userid']);
        }

    } // foreach($orderids as $orderid)

}

/**
 * This function performs activities needed when order is complete
 */
function func_complete_order($orderid)
{
    global $config, $mail_smarty, $active_modules;
    global $sql_tbl, $to_customer;
    global $xcart_dir;

    $order_data = func_order_data($orderid);

    if (empty($order_data))
        return false;

    $order     = $order_data['order'];
    $userinfo  = $order_data['userinfo'];
    $products  = $order_data['products'];
    $giftcerts = $order_data['giftcerts'];

    $mail_smarty->assign('products',  $products);
    $mail_smarty->assign('giftcerts', $giftcerts);
    $mail_smarty->assign('order',     $order);

    if (!empty($active_modules['Special_Offers'])) {
        include $xcart_dir.'/modules/Special_Offers/complete_order.php';
    }

    // Send mail notifications

    if ($config['Email_Note']['eml_order_c_notif_customer'] == 'Y') {

        $to_customer = func_query_first_cell ("SELECT language FROM $sql_tbl[customers] WHERE id='$userinfo[userid]'");

        if(empty($to_customer)) {
            $to_customer = $config['default_customer_language'];
        }

        $userinfo['title']   = func_get_title($userinfo['titleid'], $to_customer);
        $userinfo['b_title'] = func_get_title($userinfo['b_titleid'], $to_customer);
        $userinfo['s_title'] = func_get_title($userinfo['s_titleid'], $to_customer);

        $mail_smarty->assign('customer', $userinfo);
        $mail_smarty->assign('products', func_translate_products($products, $to_customer));

        func_send_mail(
            $userinfo['email'],
            'mail/order_cust_complete_subj.tpl',
            'mail/order_customer_complete.tpl',
            $config['Company']['orders_department'],
            false
        );

    }

    if (
        !empty($active_modules['Survey'])
        && !empty($userinfo)
    ) {
        func_check_surveys_events('OPC', $order_data, $userinfo['userid']);
        func_check_surveys_events('OPB', $order_data, $userinfo['userid']);
    }

}

/**
 * This function performs activities nedded when order is declined
 * status may be assign (D)ecline or (F)ail
 * (D)ecline order sent mail to customer, (F)ail - not
 */
function func_decline_order($orderids, $status = 'D', $old_status = 'D')
{
    global $config, $mail_smarty;
    global $sql_tbl, $to_customer, $active_modules, $xcart_dir;

    if (($status != 'D') && ($status != 'F')) return;

    if (!is_array($orderids))
        $orderids = array($orderids);

    foreach($orderids as $orderid) {

        // Order decline routine

        $order_data = func_order_data($orderid);

        if (empty($order_data))
            continue;

        $order     = $order_data['order'];
        $userinfo  = $order_data['userinfo'];
        $products  = $order_data['products'];
        $giftcerts = $order_data['giftcerts'];

        // Send mail notifications
        if ($status == 'D') {

            $mail_smarty->assign('customer',  $userinfo);
            $mail_smarty->assign('products',  $products);
            $mail_smarty->assign('giftcerts', $giftcerts);
            $mail_smarty->assign('order',     $order);

            if ($config['Email_Note']['eml_order_d_notif_customer'] == 'Y') {

                $to_customer = func_query_first_cell ("SELECT language FROM $sql_tbl[customers] WHERE id='$userinfo[userid]'");

                if (empty($to_customer))
                    $to_customer = $config['default_customer_language'];

                $mail_smarty->assign('products', func_translate_products($products, $to_customer));

                $userinfo['title']   = func_get_title($userinfo['titleid'], $to_customer);
                $userinfo['b_title'] = func_get_title($userinfo['b_titleid'], $to_customer);
                $userinfo['s_title'] = func_get_title($userinfo['s_titleid'], $to_customer);

                $mail_smarty->assign('customer', $userinfo);

                func_send_mail(
                    $userinfo['email'],
                    'mail/decline_notification_subj.tpl',
                    'mail/decline_notification.tpl',
                    $config['Company']['orders_department'],
                    false
                );

            }

        }

        // Discount restoring

        $discount_coupon = addslashes($order['coupon']);

        if ($discount_coupon) {

            $_per_user = func_query_first_cell("SELECT per_user FROM $sql_tbl[discount_coupons] WHERE coupon='$discount_coupon'");

            if ($_per_user == 'Y') {

                db_query("UPDATE $sql_tbl[discount_coupons_login] SET times_used=IF(times_used>'0', times_used-1, '0') WHERE coupon='$discount_coupon' AND userid='".$userinfo["userid"]."'");

            } else {

                db_query("UPDATE $sql_tbl[discount_coupons] SET status='A' WHERE coupon='$discount_coupon' and times_used=times");
                db_query("UPDATE $sql_tbl[discount_coupons] SET times_used=times_used-1 WHERE coupon='$discount_coupon'");

            }

            $discount_coupon = "";

        }

        // Increase debit for declined GC

        if ($order['applied_giftcerts']) {

            foreach ($order['applied_giftcerts'] as $k=>$v) {

                if (
                    $old_status == 'P'
                    || $old_status == 'C'
                ) {
                    db_query("UPDATE $sql_tbl[giftcerts] SET debit=debit+'$v[giftcert_cost]' WHERE gcid='$v[giftcert_id]'");
                }

                db_query("UPDATE $sql_tbl[giftcerts] SET status='A' WHERE debit>'0' AND gcid='$v[giftcert_id]'");

            }

        }

        // Set GC's status to 'D'
        if ($giftcerts) {

            foreach($giftcerts as $giftcert) {

                db_query("UPDATE $sql_tbl[giftcerts] SET status='D' WHERE gcid='$giftcert[gcid]'");

            }

        }

        if ($config['General']['unlimited_products'] != 'Y') {

            func_update_quantity ($products);

        }

        if (!empty($active_modules['Special_Offers'])) {
            include $xcart_dir.'/modules/Special_Offers/decline_order.php';
        }

    } // foreach($orderids as $orderid)

    if (!empty($active_modules['SnS_connector'])) {

        $_old = $_COOKIE;

        $_COOKIE['personal_client_id'] = $order['extra']['personal_client_id'];

        func_generate_sns_action('Order', $orderid);

        $_COOKIE = $_old;

    }

}

/**
 * This function performs activities needed when payment has been authorized
 */
function func_authorize_order($orderid)
{
    global $config, $mail_smarty, $sql_tbl, $xcart_dir;

    $order_data = func_order_data($orderid);

    if (empty($order_data))
        return false;

    $order         = $order_data['order'];
    $userinfo      = $order_data['userinfo'];
    $products      = $order_data['products'];
    $giftcerts     = $order_data['giftcerts'];

    $mail_smarty->assign('order',       $order);
    $mail_smarty->assign('userinfo',    $userinfo);
    $mail_smarty->assign('products',    $products);
    $mail_smarty->assign('giftcerts',   $giftcerts);

    // Notify orders department by email

    if ($config['Email_Note']['enable_order_notif'] == 'Y') {

        $mail_smarty->assign('show_order_details', 'Y');

        func_send_mail(
            $config['Company']['orders_department'],
            'mail/preauth_order_notification_subj.tpl',
            'mail/preauth_order_notification.tpl',
            $userinfo['email'],
            true,
            true
        );

        $mail_smarty->assign('show_order_details', '');
    }

    // Notify customer by email

    if ($config['Email_Note']['eml_order_q_notif_customer'] == 'Y') {

        $to_customer = $userinfo['language']
            ? $userinfo['language']
            : $config['default_customer_language'];

        $mail_smarty->assign('products', func_translate_products($order_data['products'], $to_customer));

        func_send_mail(
            $userinfo['email'],
            'mail/preauth_order_customer_subj.tpl',
            'mail/preauth_order_customer.tpl',
            $config['Company']['orders_department'],
            false
        );

    }

}

/**
 * This function sends GC emails (called from func_place_order
 * and provider/order.php"
 */
function func_send_gc($from_email, $giftcert, $from_id = '')
{
    global $mail_smarty, $config, $to_customer, $sql_tbl, $admin_safe_mode;

    if ($admin_safe_mode) return;

    $giftcert['purchaser_email'] = $from_email;
    $giftcert['message'] = stripslashes($giftcert['message']);

    $mail_smarty->assign('giftcert', $giftcert);

    if (
        !empty($from_id)
        && empty($to_customer)
    ) {
        $to_customer = func_query_first_cell("SELECT language FROM $sql_tbl[customers] WHERE id = '$from_id'");

        if (empty($to_customer))
            $to_customer = $config['default_customer_language'];
    }

    // Send notification to purchaser

    if (
        @$config['Gift_Certificates']['eml_giftcert_notif_purchaser'] == 'Y'
        && (
            @$config['Gift_Certificates']['eml_giftcert_notif_admin'] != 'Y'
            || $config['Company']['orders_department'] != $from_email
        )
    ) {
        func_send_mail(
            $from_email,
            'mail/giftcert_notification_subj.tpl',
            'mail/giftcert_notification.tpl',
            $config['Company']['orders_department'],
            false
        );
    }

    // Send notification to orders department
    if ( @$config['Gift_Certificates']['eml_giftcert_notif_admin'] == 'Y') {
        func_send_mail(
            $config['Company']['orders_department'],
            'mail/giftcert_notification_subj.tpl',
            'mail/giftcert_notification.tpl',
            $from_email,
            true
        );
    }

    // Send notification to recipient
    func_send_mail(
        $giftcert['recipient_email'],
        'mail/giftcert_subj.tpl',
        'mail/giftcert.tpl',
        $from_email,
        false
    );
}

/**
 * Move products back to the inventory
 */
function func_update_quantity($products, $increase = true)
{
    global $config, $sql_tbl, $active_modules;

    $symbol = $increase
        ? '+'
        : '-';

    if (
        $config['General']['unlimited_products'] != 'Y'
        && is_array($products)
    ) {
        $ids = array();

        foreach ($products as $product) {

            if ($product['product_type'] == 'C' && !empty($active_modules['Product_Configurator']))
                continue;

            $variantid = '';

            if (
                !empty($active_modules['Product_Options'])
                && (
                    !empty($product['extra_data']['product_options'])
                    || !empty($product['options'])
                )
            ) {
                $options = !empty($product['extra_data']['product_options'])
                    ? $product['extra_data']['product_options']
                    : $product['options'];

                $variantid = func_get_variantid($options);
            }

            if (!empty($variantid)) {

                db_query("UPDATE $sql_tbl[variants] SET avail=avail$symbol'$product[amount]' WHERE variantid = '$variantid'");

            } else {
                $egoods_cond = !empty($active_modules['Egoods'])
                ? " AND distribution=''"
                : '';

                db_query("UPDATE $sql_tbl[products] SET avail=avail$symbol'$product[amount]' WHERE productid='$product[productid]'".$egoods_cond);
            }

            $ids[$product['productid']] = true;
        }

        if (!empty($ids)) {

            func_build_quick_flags(array_keys($ids));

            func_build_quick_prices(array_keys($ids));

        }

    }

}

// This function removes orders and related info from the database
// $orders can be: 1) orderid; 2) orders array with orderid keys
function func_delete_order($orders, $restore_data = true)
{
    global $sql_tbl, $xcart_dir;
    global $REMOTE_ADDR, $login;

    x_load('export');

    $_orders = array();

    if (is_array($orders)) {

        foreach($orders as $order) {

            if (!empty($order['orderid'])) {

                $_orders[] = $order['orderid'];

            }

        }

    } elseif (is_numeric($orders)) {

        $_orders[] = $orders;

    }

    x_log_flag(
        'log_orders_delete',
        'ORDERS',
        "Login: $login\nIP: $REMOTE_ADDR\nOperation: delete orders (" . implode(',',$_orders) . ")",
        true
    );

    // Restore quantity and discount coupon usage for not processed orders.

    if ($restore_data) {

        foreach($_orders as $orderid) {

            $order_data = func_order_data($orderid);

            if (empty($order_data))
                continue;

            // Update data only for orders in initial and queued state.
            if (!in_array($order_data['order']['status'], array('I', 'Q'))) {
                continue;
            }

            // Update quantity of products
            func_update_quantity($order_data['products']);

            // Discount restoring
            $discount_coupon = $order_data['order']['coupon'];

            if ($discount_coupon) {

                $_per_user = func_query_first_cell("SELECT per_user FROM $sql_tbl[discount_coupons] WHERE coupon='$discount_coupon'");

                if ($_per_user == 'Y') {

                    db_query("UPDATE $sql_tbl[discount_coupons_login] SET times_used = IF(times_used>'0', times_used-1, '0') WHERE coupon='$discount_coupon' AND userid='" . $order_data['userinfo']['userid'] . "'");

                } else {

                    db_query("UPDATE $sql_tbl[discount_coupons] SET status='A' WHERE coupon='$discount_coupon' and times_used=times");
                    db_query("UPDATE $sql_tbl[discount_coupons] SET times_used=times_used-1 WHERE coupon='$discount_coupon'");

                }

            } // if ($discount_coupon)

        } // foreach($_orders as $orderid)

    } //  if ($restore_data)

    // Delete orders from the database

    if (!isset($sql_tbl['partner_payment']))
        $xaff = func_is_defined_module_sql_tbl('XAffiliate', 'partner_payment');

    if (!isset($sql_tbl['returns']))
        $xrma = func_is_defined_module_sql_tbl('RMA', 'returns');

    if (!isset($sql_tbl['order_status_history']))
        $xaom = func_is_defined_module_sql_tbl('Advanced_Order_Management', 'order_status_history');

    if (!isset($sql_tbl['subscription_customers']))
        $xsubsr = func_is_defined_module_sql_tbl('Subscriptions', 'subscription_customers');

    db_query(
        "LOCK TABLES $sql_tbl[orders] WRITE, $sql_tbl[order_details] WRITE, $sql_tbl[order_extras] WRITE, $sql_tbl[split_checkout] WRITE, $sql_tbl[giftcerts] WRITE, $sql_tbl[shipping_labels] WRITE, $sql_tbl[export_ranges] WRITE, $sql_tbl[provider_commissions] WRITE"
        . (
            $xaff
            ? ", $sql_tbl[partner_payment] WRITE, $sql_tbl[partner_product_commissions] WRITE, $sql_tbl[partner_adv_orders] WRITE"
            :''
        )
        . (
            $xrma
            ? ", $sql_tbl[returns] WRITE"
            : ''
        )
        . (
            $xaom
            ? ", $sql_tbl[order_status_history] WRITE"
            : ''
        )
        . (
            $xsubsr
            ? ", $sql_tbl[subscription_customers] WRITE"
            : ''
        )
    );

    foreach($_orders as $orderid) {

        $itemids = func_query("SELECT itemid FROM $sql_tbl[order_details] WHERE orderid='$orderid'");

        if(!empty($itemids)) {
            foreach($itemids as $k => $v) {
                $itemids[$k] = $v['itemid'];
            }
        }

        // Remove split data structure
        $split_data = func_get_split_checkout_data_by_orderid($orderid);

        if (false !== $split_data) {

            func_remove_orderid_from_split_data($split_data, $orderid);

            if (empty($split_data['orderid'])) {

                // if it is last order in split checkout structure then remove the whole query
                db_query('DELETE FROM ' . $sql_tbl['split_checkout'] . ' WHERE orderids LIKE \'%|' . $orderid . '|%\'');

            } else {

                // Remove orderid from split checkout structure and store it to DB
                func_store_split_checkout_data($split_data);

            }

        }

        db_query("DELETE FROM $sql_tbl[orders] WHERE orderid='$orderid'");
        db_query("DELETE FROM $sql_tbl[order_details] WHERE orderid='$orderid'");
        db_query("DELETE FROM $sql_tbl[order_extras] WHERE orderid='$orderid'");
        db_query("DELETE FROM $sql_tbl[giftcerts] WHERE orderid='$orderid'");
        db_query("DELETE FROM $sql_tbl[provider_commissions] WHERE orderid='$orderid'");

        if ($xaff) {
            db_query("DELETE FROM $sql_tbl[partner_payment] WHERE orderid='$orderid'");
            db_query("DELETE FROM $sql_tbl[partner_product_commissions] WHERE orderid='$orderid'");
            db_query("DELETE FROM $sql_tbl[partner_adv_orders] WHERE orderid='$orderid'");
        }

        if ($xrma && !empty($itemids)) {
            db_query("DELETE FROM $sql_tbl[returns] WHERE itemid IN ('" . implode("','", $itemids) . "')");
        }

        if ($xsubsr) {
            db_query("DELETE FROM $sql_tbl[subscription_customers] WHERE orderid='$orderid'");
        }

        db_query("DELETE FROM $sql_tbl[shipping_labels] WHERE orderid='$orderid'");

        if ($xaom) {
            db_query("DELETE FROM $sql_tbl[order_status_history] WHERE orderid='$orderid'");
        }

        func_export_range_erase('ORDERS', $orderid);

    } // foreach($_orders as $orderid)

    // Check if no orders in the database

    $total_orders = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[orders]");

    if ($total_orders == 0) {

        // Clear Order ID counter (auto increment field in the xcart_orders table)

        db_query("DELETE FROM $sql_tbl[orders]");
        db_query("DELETE FROM $sql_tbl[order_details]");
        db_query("DELETE FROM $sql_tbl[provider_commissions]");

        if ($xaff)
            db_query("DELETE FROM $sql_tbl[partner_payment]");

        db_query("DELETE FROM $sql_tbl[shipping_labels]");

        if ($xsubsr)
            db_query("DELETE FROM $sql_tbl[subscription_customers]");

        if ($xaom)
            db_query("DELETE FROM $sql_tbl[order_status_history]");

    }

    db_query("UNLOCK TABLES");
}

function func_check_merchant_password($config_force = false)
{
    global $merchant_password, $current_area, $active_modules, $config;

    return (
        $merchant_password
        && (
            $current_area == 'A'
            || (
                $current_area == 'P'
                && !empty($active_modules['Simple_Mode'])
            )
        ) && (
            $config['Security']['blowfish_enabled'] == 'Y'
            || $config_force
        )
    );
}

/**
 * This function recrypts data with the Blowfish method.
 */
function func_data_recrypt()
{
    global $sql_tbl;

    if (!func_check_merchant_password())
        return false;

    $orders = db_query("SELECT orderid, details, payment_method, status FROM $sql_tbl[orders] WHERE details NOT LIKE 'C%' AND details != ''");

    if (!$orders)
        return true;

    func_display_service_header('lbl_reencrypting_mkey');

    while ($order = db_fetch_array($orders)) {

        $details = text_decrypt($order['details']);

        if (func_need_recrypt_data($order['status'], $order['payment_method'], $details)) {

            $details = (is_string($details))
                ? addslashes(func_crypt_order_details($details))
                : '';

            func_array2update(
                'orders',
                array(
                    'details' => $details,
                ),
                "orderid = '$order[orderid]'"
            );
        }

        func_flush(". ");
    }

    db_free_result($orders);

    return true;
}

/**
 * This function checks if the order can be recrypted.
 */
function func_need_recrypt_data($status, $payment_method, $details)
{
    // check order status
    if (!in_array($status, array('I', 'Q'))) return true;

    // check if offline payment:
    if (preg_match('/'.preg_quote(" (manual processing)")."$/", $payment_method)) return true;

    // check for credit card data:
    if (preg_match('/'.preg_quote("Cardholder's name:")."/", $details)) return true;
    if (preg_match('/'.preg_quote("Card type:").'/', $details)) return true;
    if (preg_match('/'.preg_quote("Card number:").'/', $details)) return true;
    if (preg_match('/'.preg_quote("Valid from:").'/', $details)) return true;
    if (preg_match('/'.preg_quote("Exp. date:").'/', $details)) return true;
    if (preg_match('/'.preg_quote("Issue No.:").'/', $details)) return true;

    // check for echeck data:
    if (preg_match('/'.preg_quote("Bank account number:").'/', $details)) return true;
    if (preg_match('/'.preg_quote("Bank routing number:").'/', $details)) return true;
    if (preg_match('/'.preg_quote("Fraction number:").'/', $details)) return true;

    return false;
}

/**
 * This function decrypts data Blowfish method -> Standart method.
 */
function func_data_decrypt()
{
    global $sql_tbl;

    if (!func_check_merchant_password(true))
        return false;

    $orders = db_query("SELECT orderid, details FROM $sql_tbl[orders] WHERE details LIKE 'C%'");

    if (!$orders)
        return true;

    func_display_service_header('lbl_reencrypting_skey');

    while ($order = db_fetch_array($orders)) {

        $details = text_decrypt($order['details']);
        $details = is_string($details)
            ? addslashes(text_crypt($details))
            : '';

        func_array2update(
            'orders',
            array(
                'details' => $details,
            ),
            "orderid = '$order[orderid]'"
        );

        func_flush(". ");

    }

    db_free_result($orders);

    return true;
}

// This function recrypts Blowfish-crypted data with new password
// where:
//    old_password - old Merchant password
function func_change_mpassword_recrypt($old_password)
{
    global $sql_tbl, $merchant_password;

    if (
        empty($old_password)
        || !func_check_merchant_password()
    ) {
        return false;
    }

    $orders = db_query("SELECT orderid, details FROM $sql_tbl[orders] WHERE details != ''");

    if (!$orders)
        return true;

    $_merchant_password = $merchant_password;

    func_display_service_header('lbl_reencrypting_new_mkey');

    while ($order = db_fetch_array($orders)) {

        $merchant_password = $old_password;
        $details           = text_decrypt($order['details']);
        $merchant_password = $_merchant_password;
        $details           = is_string($details)
            ? addslashes(func_crypt_order_details($details))
            : '';

        func_array2update(
            'orders',
            array(
                'details' => $details,
            ),
            "orderid = '$order[orderid]'"
        );

        func_flush(". ");
    }

    db_free_result($orders);

    $merchant_password = $_merchant_password;

    return true;
}

/**
 * Encryption of the 'details' field of the orders table
 */
function func_crypt_order_details($data)
{
    if (func_check_merchant_password())
        return text_crypt($data, 'C');

    return text_crypt($data);
}

/**
 * This function create file lock in temporaly directory
 * It will return file descriptor, or false.
 */
function func_lock($lockname, $ttl = 15)
{
    global $file_temp_dir, $_lock_hash;

    if (empty($lockname))
        return false;

    if (!empty($_lock_hash[$lockname]))
        return $_lock_hash[$lockname];

    $fname = $file_temp_dir . XC_DS . $lockname;

    // Generate current id
    $id = md5(uniqid(rand(), true));

    $_lock_hash[$lockname] = $id;

    $file_id = false;
    $read_limit = 3;
    $last_chance = true;

    if (!is_int($ttl))
        $ttl = intval($ttl);

    if ($ttl <= 0)
        $ttl = 15;

    while (true) {

        if (!file_exists($fname)) {

            // Write locking data
            $fp = @fopen($fname, 'w');

            if ($fp) {
                fwrite($fp, $id.XC_TIME);
                fclose($fp);

                func_chmod_file($fname);
            }

        }

        $fp = @fopen($fname, 'r');

        if (!$fp) {
            if (--$read_limit <= 0)
                break;

            sleep(1);
            continue;
        }

        $read_limit = 3;
        $tmp = fread($fp, 43);

        fclose($fp);

        $file_id = substr($tmp, 0, 32);
        $file_time = substr($tmp, 32);

        if ($file_id == $id)
            break;

        if (XC_TIME > $file_time+$ttl) {

            if (!$last_chance || !@unlink($fname))
                break;

            $last_chance = false;

            continue;
        }

        sleep(1);
    }

    return $file_id == $id
        ? $id
        : false;
}

/**
 * This function releases file lock which is previously created by func_lock
 */
function func_unlock($lockname)
{
    global $file_temp_dir, $_lock_hash;

    if (empty($lockname))
        return false;

    if (empty($_lock_hash[$lockname]))
        return false;

    $fname = $file_temp_dir . XC_DS . $lockname;

    if (!file_exists($fname)) {

        func_unset($_lock_hash, $lockname);

        return false;

    }

    $fp = @fopen($fname, 'r');

    if (!$fp) {

        func_unset($_lock_hash, $lockname);

        return false;

    }

    $tmp = fread($fp, 43);

    fclose($fp);

    $file_id = substr($tmp, 0, 32);
    $file_time = substr($tmp, 32);

    if ($file_id == $_lock_hash[$lockname])
        @unlink($fname);

    func_unset($_lock_hash, $lockname);

    return true;
}

/**
 * Translate products names to local product names
 */
function func_translate_products($products, $code)
{
    global $sql_tbl;

    if (
        !is_array($products)
        || empty($products)
        || empty($code)
    ) {
        return $products;
    }

    $hash = array();

    foreach($products as $k => $p) {
        $hash[$p['productid']][] = $k;
    }

    if (empty($hash))
        return $products;

    foreach ($hash as $pid => $keys) {

        $local = func_query_first("SELECT product, descr, fulldescr FROM $sql_tbl[products_lng] WHERE productid = '$pid' AND code = '$code'");

        if (empty($local) || !is_array($local))
            continue;

        foreach($keys as $k) {
            $products[$k] = func_array_merge(
                $products[$k],
                preg_grep("/\S/S", $local)
            );
        }
    }

    return $products;
}

/**
 * This function defines internal fields for storing sensitive information in order details
 */
function func_order_details_fields($all=false)
{
    global $store_cc, $store_ch, $store_cvv2;

    static $all_fields = array (
        'CC' => array (
            'card_name'       => "{CardOwner}",
            'card_type'       => "{CardType}",
            'card_number'     => "{CardNumber}",
            'card_valid_from' => "{ValidFrom}",
            'card_expire'     => "{ExpDate}",
            'card_issue_no'   => "{IssueNumber}",
        ),
        'CC_EXT' => array (
            'card_cvv2' => 'CVV2',
        ),
        'CH' => array (
            // ACH
            'check_name'         => "{AccountOwner}",
            'check_ban'          => "{BankAccount}",
            'check_brn'          => "{BankNumber}",
            'check_number'       => "{FractionNumber}",
            // Direct Debit
            'debit_name'         => "{AccountOwner}",
            'debit_bank_account' => "{BankAccount}",
            'debit_bank_number'  => "{BankNumber}",
            'debit_bank_name'    => "{BankName}",
        )
    );

    $keys = array();

    if (
        $store_cc
        || $all
    ) {

        $keys[] = 'CC';

        if (
            $store_cvv2
            || $all
        ) {
            $keys[] = 'CC_EXT';
        }
    }

    if (
        $store_ch
        || $all
    ) {
        $keys[] = 'CH';
    }

    $rval = array();

    foreach ($keys as $key) {
        $rval = func_array_merge(
            $rval,
            $all_fields[$key]
        );
    }

    return $rval;
}

/**
 * Convert {CardName} => value of lbl_payment_CardName language variable
 */
function func_order_details_fields_as_labels($force = false)
{
    $rval = array();

    foreach (func_order_details_fields(true) as $field) {

        if (preg_match('!^\{(.*)\}$!S', $field, $sublabel)) {

            $rval[$field] = func_get_langvar_by_name(
                'lbl_payment_' . $sublabel[1],
                NULL,
                false,
                $force
            );

        }

    }

    return $rval;
}

/**
 * Remove sensitive information from order details
 */
function func_order_remove_ccinfo($order_details, $save_4_digits)
{
    static $find_re = array (
        1 => array ('/^(?:\{(?:CardOwner|CardType|ExpDate)\}|Cardholder\\\*\'s\s+name|Card\s+type|Exp\.\s+date):.*$/mS', '/^CVV2:.*$/mS'),
        0 => array ('/^(?:\{(?:CardOwner|CardType|CardNumber|ExpDate)\}|Cardholder\\\*\'s\s+name|Card\s+type|Card\s+number|Exp\.\s+date):.*$/mS', '/^CVV2:.*$/mS'),
    );

    $save_4_digits = (int)((bool)$save_4_digits); // can use only 0 & 1

    $order_details = preg_replace($find_re[$save_4_digits], '', $order_details);

    if ($save_4_digits) {

        if (preg_match_all("/^(\{CardNumber\}:)(.*)$/mS", $order_details, $all_matches)) {

            foreach ($all_matches[2] as $matchn => $cardnum) {

                $cardnum = trim($cardnum);

                $order_details = str_replace(
                    $all_matches[0][$matchn],
                    $all_matches[1][$matchn] . " " . str_repeat("*", strlen($cardnum) - 4) . substr($cardnum, -4),
                    $order_details
                );

            }

        }

    }

    return $order_details;
}

/**
 * Replace all occurences of {Label} by corresponding language variable
 */
function func_order_details_translate($order_details, $force = false)
{
    static $labels = array();
    global $shop_language;

    if (empty($labels[$shop_language])) {
        $labels[$shop_language] = func_order_details_fields_as_labels($force);
    }

    $order_details = str_replace(
        array_keys($labels[$shop_language]),
        array_values($labels[$shop_language]),
        $order_details
    );

    return $order_details;
}

function func_order_is_authorized($orderid)
{
    global $sql_tbl;

    if (!is_int($orderid))
        return false;

    $status = func_query_first_cell("SELECT status FROM $sql_tbl[orders] WHERE orderid = '$orderid'");

    if ($status != 'A')
        return false;

    $capture_status = func_query_first_cell("SELECT value FROM $sql_tbl[order_extras] WHERE orderid = '$orderid' AND khash = 'capture_status'");

    if ($capture_status != 'A')
        return false;

    return true;
}

function func_order_can_captured($orderid)
{
    global $sql_tbl;

    if (!func_order_is_authorized($orderid))
        return false;

    x_load('payment');

    $paymentid = func_query_first_cell("SELECT paymentid FROM $sql_tbl[orders] WHERE orderid = '$orderid'");

    return (bool)func_payment_get_capture_func($paymentid);
}

function func_order_is_voided($orderid)
{
    global $sql_tbl;

    if (!func_order_is_authorized($orderid))
        return false;

    x_load('payment');

    $paymentid = func_query_first_cell("SELECT paymentid FROM $sql_tbl[orders] WHERE orderid = '$orderid'");

    return (bool)func_payment_get_void_func($paymentid);
}

function func_order_can_accept($orderid)
{
    global $sql_tbl;

    x_load('payment');

    $data = func_query_first("SELECT paymentid, status FROM $sql_tbl[orders] WHERE orderid = '$orderid'");

    if (!in_array($data['status'], array('A', 'Q', 'P', 'C'))) {

        return false;

    }

    return (bool)func_payment_get_accept_func($data['paymentid']);
}

function func_order_can_decline($orderid)
{
    global $sql_tbl;

    x_load('payment');

    $data = func_query_first("SELECT paymentid, status FROM $sql_tbl[orders] WHERE orderid = '$orderid'");

    if (!in_array($data['status'], array('A', 'Q', 'P', 'C'))) {

        return false;

    }

    return (bool)func_payment_get_decline_func($data['paymentid']);
}

function func_order_can_get_info($orderid)
{
    global $sql_tbl;

    x_load('payment');

    $paymentid = func_query_first_cell("SELECT paymentid FROM $sql_tbl[orders] WHERE orderid = '$orderid'");

    return (bool)func_payment_get_get_info_func($paymentid);
}

/**
 * Process OnCapture event
 */
function func_order_process_capture($orderids, $captured_total = null)
{
    global $sql_tbl, $active_modules;

    if (!is_array($orderids)) {
        $orderids = array($orderids);
    }

    $orderids = array_values($orderids);

    $new_status = 'P';

    if (!empty($active_modules['XPayments_Connector'])) {

        func_xpay_func_load();

        if (xpc_is_xpc_order($orderids[0])) {
            $new_status = xpc_get_order_status_by_action(XPC_CHARGED_ACTION);
        }

    }

    $cnt = 0;

    foreach ($orderids as $orderid) {

        if (!func_order_is_authorized(intval($orderid)))
            continue;

        if ($new_status) {

            define('STATUS_CHANGE_REF', 2);

            func_change_order_status($orderid, $new_status);

        }

        func_array2update(
            'order_extras',
            array(
                'value' => 'C'
            ),
            "orderid = '" . $orderid. "' AND khash = 'capture_status'"
        );

        if (!is_null($captured_total)) {

            $extra = func_query_first_cell("SELECT extra FROM $sql_tbl[orders] WHERE orderid = '" . $orderid . "'");

            if ($extra) {
                $extra = unserialize($extra);
            }

            $extra['captured_total'] = doubleval($captured_total);

            func_array2update(
                'orders',
                array(
                    'extra' => addslashes(serialize($extra))
                ),
                "orderid = '" . $orderid . "'"
            );
        }

        $cnt++;
    }

    return $cnt;
}

/**
 * Process OnVoid event
 */
function func_order_process_void($orderids, $process_mode = 0)
{
    global $sql_tbl, $active_modules;

    if (!is_array($orderids)) {
        $orderids = array($orderids);
    }

    $orderids = array_values($orderids);

    $new_status = 'D';

    if (!empty($active_modules['XPayments_Connector'])) {

        func_xpay_func_load();

        if (xpc_is_xpc_order($orderids[0])) {

            $new_status = xpc_get_order_status_by_action(XPC_DECLINED_ACTION);

        }

    }

    $cnt = 0;

    foreach ($orderids as $orderid) {

        if (!func_order_is_authorized(intval($orderid)))
            continue;

        if ($process_mode !== 1 && $new_status) {

            define('STATUS_CHANGE_REF', 3);

            func_change_order_status($orderid, $new_status);

        }

        func_array2update(
            'order_extras',
            array(
                'value' => 'V'
            ),
            "orderid = '" . $orderid. "' AND khash = 'capture_status'"
        );

        $cnt++;
    }

    return $cnt;
}

/**
 * Process OnRefund event
 */
function func_order_process_refund($orderids, $total, $extra_pair)
{
    global $active_modules;

    if (!is_array($orderids)) {
        $orderids = array($orderids);
    }

    $orderids = array_values($orderids);

    $order = func_order_data($orderids[0]);

    $new_status = $order['order']['charge_info']['refund_avail'] <= $total ? 'D' : false;

    if (!empty($active_modules['XPayments_Connector'])) {

        func_xpay_func_load();

        if (xpc_is_xpc_order($orderids[0])) {

            $new_status = xpc_get_order_status_by_action($new_status == 'D' ? XPC_REFUND_ACTION : XPC_PART_REFUND_ACTION);

        }

    }

    $cnt = 0;

    foreach ($orderids as $orderid) {

        func_array2insert(
            'order_extras',
            array(
                'orderid' => $orderid,
                'khash' => 'refunded_total',
                'value' => $order['order']['charge_info']['refunded_total'] + $total
            ),
            true
        );

        if ($new_status) {
            define('STATUS_CHANGE_REF', 12);

            func_change_order_status($orderid, $new_status);
        }

        $cnt++;
    }

    return $cnt;
}

/**
 * Process OnAccept event
 */
function func_order_process_accept($orderids)
{
    global $sql_tbl;

    if (!is_array($orderids)) {
        $orderids = array($orderids);
    }

    $orderids = array_values($orderids);

    $cnt = 0;

    foreach ($orderids as $orderid) {

        func_array2insert(
            'order_extras',
            array(
                'orderid' => $orderid,
                'khash' => 'fmf_action',
                'value' => 'A'
            ),
            true
        );

        $cnt++;
    }

    return $cnt;
}

/**
 * Process OnDecline event
 */
function func_order_process_decline($orderids)
{
    global $sql_tbl, $active_modules;

    if (!is_array($orderids)) {
        $orderids = array($orderids);
    }

    $orderids = array_values($orderids);

    $new_status = 'D';

    if (!empty($active_modules['XPayments_Connector'])) {

        func_xpay_func_load();

        if (xpc_is_xpc_order($orderids[0])) {

            $new_status = xpc_get_order_status_by_action(XPC_DECLINED_ACTION);

        }

    }

    $cnt = 0;

    foreach ($orderids as $orderid) {

        if ($new_status) {

            define('STATUS_CHANGE_REF', 3);

            func_change_order_status($orderid, $new_status);

        }

        func_array2insert(
            'order_extras',
            array(
                'orderid' => $orderid,
                'khash'   => 'fmf_action',
                'value'   => 'D',
            ),
            true
        );

        $cnt++;
    }

    return $cnt;
}

/**
 * Process OnGetInfo event
 */
function func_order_process_get_info($orderids, $data)
{
    global $sql_tbl;

    if (!is_array($orderids)) {
        $orderids = array($orderids);
    }

    $orderids = array_values($orderids);

    $cnt = 0;

    if ($data['status']) {

        if (!defined('STATUS_CHANGE_REF')) {
            define('STATUS_CHANGE_REF', 13);
        }

        foreach ($orderids as $orderid) {

            if ($data['status']) {
                func_change_order_status($orderid, $data['status']);
            }

            $cnt++;

        }

    }

    return $cnt;
}

/**
 * Get fraud risk factor by orderids
 */
function func_get_orders_fraud_risk_factor($orderids)
{
    global $sql_tbl, $active_modules;

    if (
        !is_array($orderids)
        || empty($active_modules['Anti_Fraud'])
    ) {
        return false;
    }

    $oid = array_shift($orderids);

    $extra = func_query_first_cell("SELECT extra FROM $sql_tbl[orders] WHERE orderid = '$oid'");

    if (empty($extra))
        return false;

    $extra = unserialize($extra);

    if (!is_array($extra))
        return false;

    if (
        isset($extra['Anti_Fraud'])
        && isset($extra['Anti_Fraud']['total_trust_score'])
    ) {
        return $extra['Anti_Fraud']['total_trust_score'];
    }

    return false;
}

/**
 * Save/append advinfo order information to order_extras with khash = 'advinfo'
 */
function func_store_advinfo($orderid, $advinfo)
{
    global $sql_tbl;

    $orderid = intval($orderid);

    if (
        $advinfo
        && func_query_first_cell("SELECT orderid FROM $sql_tbl[orders] WHERE orderid='$orderid'")
    ) {
        $prev_advinfo = text_decrypt(func_query_first_cell("SELECT value FROM $sql_tbl[order_extras] WHERE orderid='$orderid' AND khash='advinfo'"));

        $order_extras = array(
            'orderid' => $orderid,
            'khash'   => 'advinfo',
            'value'   => addslashes(text_crypt($prev_advinfo . "\n--- Advanced info ---\n" . $advinfo))
        );

        func_array2insert('order_extras', $order_extras, true);

    }
}

/**
 * Get all separated orders ids by one order id
 */
function func_get_order_ids($orderid)
{
    global $sql_tbl;

    return func_query_column("SELECT o.orderid FROM $sql_tbl[orders] as o INNER JOIN $sql_tbl[order_extras] as oe1 ON o.orderid = oe1.orderid AND oe1.khash = 'unique_id' INNER JOIN $sql_tbl[order_extras] as oe2 ON oe2.khash = 'unique_id' AND oe1.value = oe2.value AND oe2.orderid = " . intval($orderid));
}

/**
 * Get sum orders totals with same unique id
 */
function func_get_order_full_total($orderid)
{
    global $sql_tbl;

    $orderids = func_get_order_ids($orderid);

    return func_query_first_cell("SELECT SUM(total) FROM $sql_tbl[orders] WHERE orderid IN ('" . implode("','", $orderids). "')");
}

/**
 * Get saved tax display name for current language
 */
function func_get_order_tax_name($tax) {
    global $current_area, $config, $shop_language;
    static $result = array();

    $intl_tax_names = $tax['intl_tax_names'];
    $taxid = $tax['taxid'];
    $default_tax_name = $tax['tax_name'];

    $key = md5(serialize($intl_tax_names) . $taxid . $default_tax_name);
    if (isset($result[$key]))
        return $result[$key];

    if ($current_area == 'C' || $current_area == 'B') {

        $lngs = array(
                $shop_language,
                $config['default_customer_language'],
                $config['default_admin_language'],
                );

    } else {

        $lngs = array(
                $shop_language,
                $config['default_admin_language'],
                $config['default_customer_language'],
                );

    }

    $lngs = array_values(array_unique($lngs));

    $intl_tax_name = '';
    if (is_array($intl_tax_names)) {
        foreach ($lngs as $lng) {
            if (isset($intl_tax_names[$lng])) {
                $intl_tax_name = $intl_tax_names[$shop_language];
                break;
            }
        }
    }

    if (empty($intl_tax_name))
        $intl_tax_name = func_get_languages_alt('tax_' . $taxid);

    if (empty($intl_tax_name) && !empty($default_tax_name))
        $intl_tax_name = $default_tax_name;

    $result[$key] = $intl_tax_name;
    
    return $intl_tax_name;
}

/**
 * Provider commission calculation
 * @param   array    $products    
 * @param   array    $current_order 
 * @param   int      $commission_force_time optional.    
 * @param   array    $paid_commissions_details
 *                    
 * @return  boolean 
 */
function func_calculate_provider_commission($products, $current_order, $commission_force_time=null, $paid_commissions_details=array())
{
    global $single_mode, $shop_type, $config;

    if (
        $single_mode
        || $shop_type == 'GOLD'
        || empty($products)
        || empty($current_order)
    ) {
        return false;
    }

    $orderid = $current_order['orderid'];

    $provider_commission_value = 0;

    foreach ($products as $product) {
        if ($product['provider'] == $current_order['provider']) {
            $to_provider = $product['discounted_price'] * $config['General']['providers_commission'] / 100;
            $provider_commission_value += price_format($to_provider);

            $insert_data = array(
                'itemid'  => $product['itemid'],
                'orderid' => $orderid,
                'userid'  => $current_order['provider'],
                'product_commission' => price_format($to_provider)
            );
            func_array2insert('provider_product_commissions', $insert_data);
        }
    }

    if ($provider_commission_value > 0) {

        $insert_data = array(
            'userid'            => $current_order['provider'],
            'orderid'           => $orderid,
            'commissions'       => $provider_commission_value,
            'paid'              => 'N',
            'add_date'          => (isset($commission_force_time) ? $commission_force_time : XC_TIME),
            'paid_commissions'  => @$paid_commissions_details['paid_commissions'],
            'note'              => @$paid_commissions_details['note']
        );

        func_array2insert('provider_commissions', $insert_data);
    }

    return true;
}

?>
