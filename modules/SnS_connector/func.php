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
 * Functions for the SnS connector module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.php,v 1.49.2.1 2011/01/10 13:12:02 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }
/**
 * Functions for SnS connector module
 */

define('PERSONALIZE_CLIENT_ID', 'personal_client_id');

function func_generate_sns_action($action, $param = false, $is_pure = false)
{
    global $sql_tbl, $config, $active_modules, $http_location, $sns_ext_actions;

    x_load(
        'http',
        'user',
        'category'
    );

    $actions = array("AddToCart","DeleteFromCart","CartChanged","FeaturedProductSelected","BestsellerSelected","Order",'Register','Login','FillContactForm','SiteSearch','AdvancedSearch','ViewLegalInfo','ViewHelp','AddToWishList','WriteReview');

    if (empty($action) || empty($config['SnS_connector']['sns_collector_path_url_http']) || (!in_array($action, $actions) && !isset($sns_ext_actions[$action])))
        return false;

    $host = @parse_url($config['SnS_connector']['sns_collector_path_url_http']."/event.".$config['SnS_connector']['sns_script_extension']);
    if (empty($host['host']) || empty($host['path']))
        return false;

    $ts = XC_TIME;

    $post = array();
    $chain = array($action);
    foreach ($chain as $action) {
        $cpost = '';
        switch ($action) {
        case 'AddToCart':
        case 'DeleteFromCart':
            if ($param === false) {
                global $productid;
            }
            else {
                $productid = $param;
            }

            $tmp = func_sns_select_product($productid);
            if (empty($tmp))
                return false;

            if ($is_pure) {
                $cpost = $tmp;
                break;
            }

            $post[] = "name=$action&".$tmp;
            $action = 'CartChanged';
            /* FALL-THROUGH */
        case 'CartChanged':
            global $cart;

            $cpost = "itemsCount=".((empty($cart['products']) || !is_array($cart['products'])) ? 0 : intval(@count($cart['products'])))."&total=".price_format($cart['total_cost']);
            break;
        case 'BestsellerSelected':
        case 'AddToWishList':
        case 'FeaturedProductSelected':
            if ($param === false) {
                global $productid;
            }
            else {
                $productid = $param;
            }

            $cpost = func_sns_select_product($productid);
            if (empty($cpost))
                return false;
            break;
        case 'Order':
            if ($param === false) {
                global $orderid;
            }
            else {
                $orderid = $param;
            }

            x_load('order');

            $order = func_order_data($orderid);
            if (empty($order) || !in_array($order['order']['status'], array("P","C")) || (empty($order['products']) && empty($order['giftcerts'])))
                return false;

            $ts = $order['order']['date']-$config["Appearance"]["timezone_offset"];
            if (!empty($order['products'])) {
                foreach ($order['products'] as $i) {
                    $tmp = func_sns_select_product($i['productid']);
                    if (!empty($tmp)) {
                        $cpost[] = "profile_id=".urlencode($order['userinfo']['login'])."&orderId=$orderid&total=".round($i['amount']*$i['price'], 2)."&billing_country=".urlencode($order['userinfo']['b_countryname'])."&billing_city=".urlencode($order['userinfo']['b_city'])."&billing_company=".urlencode($order['userinfo']['company'])."&billing_fax=".urlencode($order['userinfo']['fax'])."&billing_phone=".urlencode($order['userinfo']['phone'])."&billing_address=".urlencode($order['userinfo']['b_address'])."&billing_state=".urlencode($order['userinfo']['b_statename'])."&billing_zipcode=".urlencode($order['userinfo']['b_zipcode'])."&billing_firstname=".urlencode($order['userinfo']['firstname'])."&billing_lastname=".urlencode($order['userinfo']['lastname'])."&email=".urlencode($order['userinfo']['email'])."&quantity=$i[amount]&".$tmp;
                    }
                }
            }

            if (!empty($order['giftcerts'])) {
                foreach ($order['giftcerts'] as $i) {
                    $cpost[] = "profile_id=".urlencode($order['userinfo']['login'])."&orderId=$orderid&total=".$i['amount']."&billing_country=".urlencode($order['userinfo']['b_countryname'])."&billing_city=".urlencode($order['userinfo']['b_city'])."&billing_company=".urlencode($order['userinfo']['company'])."&billing_fax=".urlencode($order['userinfo']['fax'])."&billing_phone=".urlencode($order['userinfo']['phone'])."&billing_address=".urlencode($order['userinfo']['b_address'])."&billing_state=".urlencode($order['userinfo']['b_statename'])."&billing_zipcode=".urlencode($order['userinfo']['b_zipcode'])."&billing_firstname=".urlencode($order['userinfo']['firstname'])."&billing_lastname=".urlencode($order['userinfo']['lastname'])."&email=".urlencode($order['userinfo']['email'])."&quantity=1&productId=$i[gcid]&productName=GIFT CERTIFICATE&categoryName=";
                }
            }
            break;
        case 'Register':
            global $uname, $usertype;

            $userinfo = func_userinfo($uname, $usertype);
            /* FALL-THROUGH */
        case 'Login':
            if (!isset($userinfo)) {
                global $logged_userid, $login_type;
                $userinfo = func_userinfo($logged_userid, $login_type);
            }

            $cpost = "profile_id=$userinfo[login]&billing_country=".urlencode($userinfo['b_country'])."&billing_city=".urlencode($userinfo['b_city'])."&billing_company=".urlencode($userinfo['company'])."&billing_fax=".urlencode($userinfo['fax'])."&billing_phone=".urlencode($userinfo['phone'])."&billing_address=".urlencode($userinfo['b_address'])."&billing_state=".urlencode($userinfo['b_state'])."&billing_zipcode=".urlencode($userinfo['b_zipcode'])."&billing_firstname=".urlencode($userinfo['firstname'])."&billing_lastname=".urlencode($userinfo['lastname'])."&email=".urlencode($userinfo['email']);
            break;
        case 'FillContactForm':
            global $contact, $body;

            $cpost = "billing_country=".urlencode($contact['b_country'])."&billing_city=".urlencode($contact['b_city'])."&billing_company=".urlencode($contact['company'])."&billing_fax=".urlencode($contact['fax'])."&billing_phone=".urlencode($contact['phone'])."&billing_address=".urlencode($contact['b_address'])."&billing_state=".urlencode($contact['b_state'])."&billing_zipcode=".urlencode($contact['b_zipcode'])."&billing_firstname=".urlencode($contact['firstname'])."&billing_lastname=".urlencode($contact['lastname'])."&email=".urlencode($contact['email'])."&enquiry=".urlencode($body);
            break;
        case 'SiteSearch':
            global $posted_data;

            $cpost = "searchPhrase=".urlencode($posted_data['substring']);
            break;
        case 'AdvancedSearch':
            global $posted_data;

            $post[] = "name=SiteSearch&searchPhrase=".urlencode($posted_data['substring']);
            $cat = '';
            if (!empty($posted_data['categoryid'])) {
                $cat = func_get_category_path($posted_data['categoryid'], 'category', true);
            }

            $cpost = "searchPhrase=".urlencode($posted_data['substring'])."&categoryName=".urlencode($cat);
            break;
        case 'ViewLegalInfo':
            if ($param === false) {
                global $section;
                $cpost = "pageName=".urlencode($section);
            }
            else {
                $cpost = "pageName=".urlencode($param);
            }
            break;
        case 'ViewHelp':
            global $current_location, $REQUEST_URI;

            $tmp = @parse_url($current_location);
            $cpost = "pageUrl=".urlencode($tmp['scheme']."://".$tmp['host'].$REQUEST_URI);
            break;
        case 'WriteReview':
            global $review_message, $productid;

            $cpost = func_sns_select_product($productid);
            if (empty($cpost))
                return false;

            $cpost .= "&reviewText=".urlencode(stripslashes($review_message));
            break;

        default:
            if (isset($sns_ext_actions[$action]) && !empty($sns_ext_actions[$action]) && function_exists($sns_ext_actions[$action])) {
                if (!$sns_ext_actions[$action]($cpost, $param))
                    continue;
            } else {
                continue;
            }
        }

        if (is_array($cpost)) {
            foreach ($cpost as $cp) {
                $post[] = "name=$action&".$cp;
            }
        }
        else {
            $post[] = "name=$action&".$cpost;
        }
    }

    if (empty($post))
        return false;

    $static_post = "clientId=".func_get_sns_client_id()."&sessionId=$_COOKIE[personal_session_id]&timestamp=".$ts."&shopDisplayName=".urlencode($config['SnS_connector']['sns_shop_display_name'])."&passphrase=".urlencode($config['SnS_connector']['sns_passphrase'])."&site=".urlencode($http_location);
    foreach ($post as $k => $v) {
        if (empty($v)) {
            unset($post[$k]);
            continue;
        }

        $post[$k] = $static_post."&".urlencode("actions[$k]")."=".urlencode($v);
    }

    list($head, $res) = func_http_post_request($host['host'], $host['path'], implode("&",$post));

    return (strpos($head['ERROR'],"200") !== false && strpos($res,"External event registered") !== false);
}



function func_sns_select_product($productid)
{
    global $sql_tbl;

    x_load('category');

    $tmp = func_query_first("SELECT $sql_tbl[products].product, $sql_tbl[categories].categoryid FROM $sql_tbl[products], $sql_tbl[products_categories], $sql_tbl[categories] WHERE $sql_tbl[products].productid = '$productid' AND $sql_tbl[products].productid = $sql_tbl[products_categories].productid AND $sql_tbl[products_categories].main = 'Y' AND $sql_tbl[products_categories].categoryid = $sql_tbl[categories].categoryid");
    if (empty($tmp))
        return false;

    $cats = func_get_category_path($posted_data['categoryid'], 'category', true);

    return "productId=$productid&productName=".urlencode($tmp['product'])."&categoryName=".urlencode($cats);
}

function func_get_sns_client_id()
{

    $client_id = (int)$_COOKIE[constant('PERSONALIZE_CLIENT_ID')];
    if (!empty($client_id)) {
        return $client_id;
    }

    $remote_addr = $_SERVER['REMOTE_ADDR'];
    $forwarded_for = $_SERVER['HTTP_X_FORWARDED_FOR'];
    if (!empty($forwarded_for)) {
        $remote_addr = substr($forwarded_for.", ".$remote_addr, 0, 255);
    }

    $accept_language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    return func_xor(func_xor(crc32($remote_addr),crc32($accept_language)), crc32($user_agent));
}

function func_sns_exec_actions(&$sns_actions)
{
    if (empty($sns_actions))
        return false;

    foreach ($sns_actions as $a => $v) {
        foreach ($v as $v2) {
            func_generate_sns_action($a, $v2);
        }
    }
    $sns_actions = array();
}
?>
