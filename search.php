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
 * Products search interface
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Customer interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: search.php,v 1.59.2.2 2011/01/27 12:26:08 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

define('NUMBER_VARS', "posted_data['price_min'],posted_data['price_max'],posted_data['avail_min'],posted_data['avail_max'],posted_data['weight_min'],posted_data['weight_max'],price_min,price_max,avail_min,avail_max,weight_min,weight_max");

define('STORE_NAVIGATION_SCRIPT', 'Y');

require './auth.php';

if (
    !empty($active_modules['SnS_connector'])
    && $REQUEST_METHOD == 'POST'
) {
    func_generate_sns_action($simple_search == 'Y' ? "SiteSearch" : "AdvancedSearch");
}

// The list of the fields allowed for searching
$allowable_search_fields = array (
    'substring',
    'by_title',
    'by_descr',
    'by_shortdescr',
    'by_fulldescr',
    'by_sku',
    'extra_fields',
    'by_keywords',
    'categoryid',
    'search_in_subcategories',
    'price_max',
    'price_min',
    'price_max',
    'avail_min',
    'avail_max',
    'weight_min',
    'weight_max',
    'manufacturers',
    'including',
);

$input_args = array();

if (
    $REQUEST_METHOD == 'GET'
    && $mode == 'search'
) {
    // Check the variables passed from GET-request
    $get_vars = array();

    foreach ($_GET as $k => $v) {

        if (in_array($k, $allowable_search_fields)) {

            $get_vars[$k] = $v;

            $input_args[] = func_data2url_query($k, func_stripslashes($v), true);

        }

    }

    // Prepare the search data
    if (!empty($get_vars)) {

        foreach ($get_vars as $k => $v) {

            $search_data['products'][$k] = $v;

        }

        $input_args = implode('&', $input_args);

    }

}

// Update the search statistics
if (
    $REQUEST_METHOD == 'POST'
    && $mode == 'search'
    && !empty($posted_data['substring'])
) {
    func_array2insert(
        'stats_search',
        array(
            'search' => addslashes(substr(stripslashes($posted_data['substring']), 0, 255)),
            'date'   => XC_TIME,
        )
    );
}

$search_data['products']['forsale']         = 'Y';
$search_data['products']['category_main']   = $search_data['products']['category_extra'] = 'Y';

if (isset($posted_data)) {

    $posted_data['by_shortdescr'] = $posted_data['by_fulldescr'] = $posted_data['by_descr'];

}

include $xcart_dir . '/include/search.php';

func_unset($search_data['products'], 'forsale');

if (
    !empty($search_data['products'])
    && !empty($products)
) {

    if (!empty($active_modules['Subscriptions'])) {

        // Get the subscription plans
        include $xcart_dir . '/modules/Subscriptions/subscription.php';

        $smarty->assign('products', $products);
    }

    // Generate the URL of the search result page for accessing it via GET-request
    $search_url_args = array();

    foreach ($search_data['products'] as $k=>$v) {

        if (
            in_array($k, $allowable_search_fields)
            && !empty($v)
        ) {

            if (is_array($v)) {

                foreach ($v as $k1 => $v1) {

                    $search_url_args[] = $k . "[" . $k1 . "]=" . urlencode($v1);

                }

            } else {

                $search_url_args[] = "$k=" . urlencode($v);

            }

        }

    }

    if (
        $search_url_args
        && $page > 1
    ) {
        $search_url_args[] = "page=$page";
    }

    $search_url = "search.php?mode=search"
        . (
            !empty($search_url_args)
            ? "&" . implode("&", $search_url_args)
            : ''
        );

    $smarty->assign('search_url', $search_url);
}

$search_prefilled = $search_data['products'];

$search_prefilled_default = array(
    'by_title'      => 'Y',
    'by_descr'      => 'Y',
    'by_sku'        => 'Y',
    'price_min'     => preg_replace("/-.*$/", '', @$config['Search_products']['search_products_price_d']),
    'price_max'     => preg_replace("/^.*-/", '', @$config['Search_products']['search_products_price_d']),
    'weight_min'    => preg_replace("/-.*$/", '', @$config['Search_products']['search_products_weight_d']),
    'weight_max'    => preg_replace("/^.*-/", '', @$config['Search_products']['search_products_weight_d']),
    'categoryid'    => isset($config['Search_products']['search_products_category_d'])
                            ? $config['Search_products']['search_products_category_d']
                            : ''
);

$search_prefilled_default['search_in_subcategories'] = 'Y';

foreach (
    array(
        'price_min',
        'price_max',
        'weight_min',
        'weight_max'
    ) as $k) {

    if (
        $search_prefilled
        && isset($search_prefilled[$k])
        && trim($search_prefilled[$k]) != ''
    ) {
        $search_prefilled[$k] = price_format($search_prefilled[$k]);
    }

    if (
        isset($search_prefilled_default[$k])
        && trim($search_prefilled_default[$k]) != ''
    ) {
        $search_prefilled_default[$k] = price_format($search_prefilled_default[$k]);
    }
}

include $xcart_dir . '/include/common.php';

if (
    !empty($active_modules['Manufacturers'])
    && !empty($manufacturers)
) {

    if (
        $config['Manufacturers']['manufacturers_limit'] > 0
        && count($manufacturers) == $config['Manufacturers']['manufacturers_limit']
    ) {
        $manufacturers = func_get_manufacturers_list(true);
    }

    $search_prefilled_default['manufacturerids'] = func_manufacturer_selected_for_search(
        $manufacturers,
        (
            isset($search_prefilled['manufacturers'])
                ? $search_prefilled['manufacturers']
                : false
        )
    );

    $smarty->assign('manufacturers', $manufacturers);

}

if (!empty($search_prefilled)) {

    foreach ($search_prefilled_default as $k => $v) {

        if (!isset($search_prefilled[$k]) && $v) {

            $search_prefilled[$k] = $v;

            $search_prefilled['need_advanced_options'] = true;

        }

    }

} else {

    $smarty->assign('is_empty_search_prefilled', true);

    $search_prefilled = $search_prefilled_default;

    foreach ($search_prefilled_default as $v) {

        if ($v) {

            $search_prefilled['need_advanced_options'] = true;

        }

    }

}

$smarty->assign('search_prefilled_default', $search_prefilled_default);
$smarty->assign('search_prefilled',         $search_prefilled);

if (
    $REQUEST_METHOD == 'GET'
    && $mode == 'search'
    && empty($products)
    && empty($top_message['content'])
) {
    $smarty->assign(
        'top_message',
        array(
            'type'    => 'W',
            'content' => func_get_langvar_by_name("lbl_warning_no_search_results", false, false, true)
        )
    );
}

if (!zerolen(func_qs_remove($QUERY_STRING, $XCART_SESSION_NAME))) {

    $location[] = array(func_get_langvar_by_name('lbl_search_results'), '');

    $smarty->assign('main', 'search');

} else {

    $location[] = array(func_get_langvar_by_name('lbl_advanced_search'), '');

    $smarty->assign('main', 'advanced_search');

}

if (!empty($active_modules['Gift_Registry'])) {

    include $xcart_dir . '/modules/Gift_Registry/customer_events.php';

}

// Assign the current location line
$smarty->assign('location', $location);

func_display('customer/home.tpl', $smarty);
?>
