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
 * Ajax quick search
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: ajax_quick_search.php,v 1.20.2.1 2011/01/10 13:11:48 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';

x_session_register('login');

if (
    (
        $is_ajax
        && $_GET['mode'] != 'ajax_search'
    ) || (
        !$is_ajax
        && (
            $_GET['mode'] != 'search'
            || $login == ''
        )
    )
) {
    func_header_location('home.php');
}

if ($login == '') {
    $xml = '{"result": "not_logged_in"}';
    header('Content-Type: text/x-json;');
    echo $xml;
    exit;
}

x_session_register('quick_num');
x_session_register('quick_query');
x_session_register('quick_params');

x_load('quick_search');

if (
    !empty($_GET['mode'])
    && $_GET['mode'] == 'ajax_search'
) {
    $query = $_GET['query'];

    $quick_num = $quick_query = array(
        'orders'     => '',
        'users'     => '',
        'products'     => ''
    );

    list($type, $query) = func_parse_quick_search($query);

    if ($type == 'orders' || $type == 'all')
        list($quick_num['orders'], $quick_query['orders']) = func_get_quick_search_orders($query);

    if (($type == 'users' || $type == 'all') && $current_area == 'A')
        list($quick_num['users'], $quick_query['users']) = func_get_quick_search_users($query);

    if ($type == 'products' || $type == 'all')
        list($quick_num['products'], $quick_query['products']) = func_get_quick_search_products($query);

    $sum = $quick_num['orders'] + $quick_num['users'] + $quick_num['products'];

    $result = ($sum == 0) ? 'N' : 'Y';
    $mode = ($sum == 1) ? 'single' : 'multi';

    $quick_params = array(
        'sum'     => $sum,
        'query' => $_GET['query']
    );

    if ($sum == 1) {

        if ($quick_num['orders'] == 1) {

            $url = "order.php?orderid=" . func_get_quick_search_orders($quick_query['orders'], 'single');

        } elseif ($quick_num['users'] == '1') {

            list($user, $usertype) = func_get_quick_search_users($quick_query['users'], 'single');
            $url = "user_modify.php?user=$user&usertype=$usertype";

        } else {

            $url = "product_modify.php?productid=" . func_get_quick_search_products($quick_query['products'], 'single');

        }

        $top_message = array(
            'content'     => func_get_langvar_by_name('lbl_exact_match_found'),
            'type'         => 'I'
        );
    }

    x_session_save();

    $xml = '{"mode": "' . $mode . '", "result": "' . $result . '"' . (!empty($url) ? ', "url": "' . $url . '"' : '') . ' }';

    header('Content-Type: text/x-json;');
    echo $xml;

    exit;

} elseif (
    !empty($_GET['mode'])
    && $_GET['mode'] == 'search'
) {

    $search_object = (!isset($_GET['so'])) ? 'products' : $_GET['so'];
    $types = array('products', 'users', 'orders');
    $is_type = false;

    if ($quick_num[$search_object] == 0) {
        foreach ($types as $type) {
            if ($quick_num[$type] != 0) {
                $is_type = true;
                $search_object = $type;
                break;
            }
        }

        if (!$is_type)  // Nothing to search
            func_header_location('home.php');
    }

    $total_items = $quick_num[$search_object];
    $objects_per_page = $config['Appearance'][$search_object . '_per_page_admin'];

    include $xcart_dir . '/include/navigation.php';

    $first = $first_page;
    $limit = $objects_per_page;

    $orderby = isset($_GET['sort']) ? $_GET['sort'] : false;
    $sorts = array('ASC', 'DESC');
    $sort = (!isset($_GET['sort_direction']) || !in_array($_GET['sort_direction'], array(0,1))) ? 'ASC' : $sorts[$_GET['sort_direction']];
    $sort_direction = ($sort == 'ASC' ? 0 : 1);
    $query = $quick_query[$search_object];

    switch ($search_object) {

        case 'orders':
            $orders = func_get_quick_search_orders($query, 'search', $orderby, $sort, $first, $limit);
            $smarty->assign('orders', $orders);
            break;

        case 'users':
            $users = func_get_quick_search_users($query, 'search', $orderby, $sort, $first, $limit);
            $smarty->assign('users', $users);
            break;

        case 'products':
            $products = func_get_quick_search_products($query, 'search', $orderby, $sort, $first, $limit);
            $smarty->assign('products', $products);
    }

    $location[] = array(func_get_langvar_by_name('lbl_quick_search_results'), '');

    $smarty->assign('location',             $location);
    $smarty->assign('usertypes',            func_get_usertypes());

    $smarty->assign('navigation_script',    'quick_search.php?mode=search&so=' . $search_object . '&sort=' . $orderby . '&sort_direction=' . $sort_direction);
    $smarty->assign('orderby',              $orderby);
    $smarty->assign('sort',                 $sort_direction);
    $smarty->assign('qscript',              'quick_search.php?mode=search&so=' . $search_object . '&page=' . $page);
    $smarty->assign('quick_num',            $quick_num);
    $smarty->assign('quick_params',         $quick_params);
    $smarty->assign('so',                   $search_object);
    $smarty->assign('main',                 'quick_search');
}
?>
