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
 * Base authentication, defining common variables 
 * and including common scripts
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Customer interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: postauth.php,v 1.42.2.2 2011/01/10 13:11:43 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: home.php"); die("Access denied"); }

// Prevent double inclusion.
if (defined('INCLUDED_POSTAUTH_PHP')) {

    return;

}

define('INCLUDED_POSTAUTH_PHP', 1);

x_session_register('logout_user');
x_session_register('session_failed_transaction');
x_session_register('add_to_cart_time');

x_session_register('always_allow_shop', false);
x_session_register('search_data');

$always_allow_shop = !empty($_GET['shopkey'])
    ? $_GET['shopkey'] === $config['General']['shop_closed_key']
    : $always_allow_shop;

if (
    'Y' === $config['General']['shop_closed']
    && false === $always_allow_shop
) {

    // Close store front
    // Thanks to rubyaryat for the Shop Closed mod

    if (
        is_file($xcart_dir . XC_DS . $shop_closed_file)
        && is_readable($xcart_dir . XC_DS . $shop_closed_file)
    ) {

        func_header_location($shop_closed_file);

    } else {

        echo func_get_langvar_by_name('txt_shop_temporarily_unaccessible', false, false, true);

    }

    exit();
}

if (!defined('HTTPS_CHECK_SKIP')) {

    include $xcart_dir . DIR_CUSTOMER . '/https.php';

}

if (!empty($active_modules['Users_online'])) {

    x_session_register('current_url_page');
    x_session_register('current_date');
    x_session_register('session_create_date');

    $current_url_page = $php_url['url'] . ($php_url['query_string'] ? "?" . $php_url['query_string'] : "");

    $current_date = XC_TIME;

    if (empty($session_create_date)) {

        $session_create_date = $current_date;

    }

}

/**
 * Display
 */
x_session_register('wlid');

if (
    isset($_GET['wlid'])
    && $_GET['wlid']
) {
    $wlid = $_GET['wlid'];
}

$smarty->assign('wlid', $wlid);

x_session_register('top_message');

if (!empty($top_message)) {

    $top_message['type'] = !empty($top_message['type'])
        ? $top_message['type']
        : 'I';

    $title_list = array(
        'E' => 'lbl_error',
        'W'    => 'lbl_warning',
    );

    $top_message['title'] = func_get_langvar_by_name(
        isset($title_list[$top_message['type']])
            ? $title_list[$top_message['type']]
            : 'lbl_information',
        array(),
        false,
        true
    );

    $smarty->assign('top_message', $top_message);

    $top_message = '';

    x_session_save('top_message');
}

if (isset($cat)) {
    $cat = intval($cat);
}

if (isset($page)) {
    $page = intval($page);
}

if (!empty($active_modules['XAffiliate'])) {

    include $xcart_dir . '/include/partner_info.php';
    include $xcart_dir . '/include/adv_info.php';

}

include $xcart_dir . DIR_CUSTOMER . '/referer.php';

include $xcart_dir . '/include/check_useraccount.php';

include $xcart_dir . '/include/get_language.php';

$lbl_site_path = strip_tags(func_get_langvar_by_name('lbl_site_path', '', false, true));

$location = array();

if (!empty($lbl_site_path)) {
    $location[] = array(
        $lbl_site_path,
        'home.php',
    );
}

include $xcart_dir . DIR_CUSTOMER . '/minicart.php';

if (!empty($active_modules['Interneka'])) {

    include $xcart_dir . '/modules/Interneka/interneka.php';

}

if (
    !empty($active_modules['Subscriptions'])
    && $logged_userid
) {
    include $xcart_dir . '/modules/Subscriptions/get_subscription_info.php';

    $smarty->assign('user_subscription', is_user_subscribed($logged_userid));
}

$pages_menu = func_query("SELECT * FROM " . $sql_tbl['pages'] . " WHERE language='" . $store_language . "' AND active='Y' AND level='E' AND show_in_menu='Y' ORDER BY orderby, title");

$smarty->assign('pages_menu', $pages_menu);

$speed_bar = unserialize($config['speed_bar']);

if (!empty($speed_bar)) {

    $tmp_labels = array();

    foreach ($speed_bar as $k => $v) {

        if ($v['active'] != 'Y') {

            unset($speed_bar[$k]);

            continue;

        }

        $speed_bar[$k] = func_array_map('stripslashes', $v);

        $tmp_labels[] = 'speed_bar_' . $v['id'];

    }

    if (!empty($speed_bar)) {

        $tmp = func_get_languages_alt($tmp_labels);

        foreach ($speed_bar as $k => $v) {

            if (isset($tmp['speed_bar_' . $v['id']])) {

                $speed_bar[$k]['title'] = $tmp['speed_bar_' . $v['id']];

            }

        }

        $smarty->assign('speed_bar', array_reverse($speed_bar));

    }

}

unset($speed_bar);

$smarty->assign('redirect', 'customer');

if (!empty($active_modules['News_Management'])) {

    include $xcart_dir . '/modules/News_Management/news_last.php';

}

if (!empty($active_modules['Feature_Comparison'])) {

    include $xcart_dir . '/modules/Feature_Comparison/comparison_products.php';

    if ($config['Feature_Comparison']['fcomparison_show_product_list'] == 'Y') {

        $comparison_list = func_get_comparison_list();

        $smarty->assign('comparison_list',$comparison_list);

    }

}

if (!empty($active_modules['Survey'])) {

    include_once $xcart_dir . '/modules/Survey/surveys_list.php';

}

if (!empty($active_modules['Special_Offers'])) {

    include_once $xcart_dir . '/modules/Special_Offers/check_new_offers.php';

}

if (!empty($active_modules['Gift_Registry'])) {

    include $xcart_dir . '/modules/Gift_Registry/customer_events.php';

}

if (isset($printable)) {

    $smarty->assign('printable', $printable);

}

if (
    !empty($active_modules['Recently_Viewed'])
    && constant('AREA_TYPE') == 'C'
    && $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    $smarty->assign('rviewed_products', rviewed_get_products());
}

$smarty->assign('logout_user', $logout_user);

?>
