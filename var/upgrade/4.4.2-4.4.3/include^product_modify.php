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
 * Product editing library
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: product_modify.php,v 1.251.2.5 2011/01/10 13:11:50 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('backoffice','category','image','product');

$__ge_res = false;

func_set_memory_limit('32M');

/**
 * Special redirect function
 */
function func_refresh($section = '', $added = '')
{
    global $productid, $geid, $config;

    if (!empty($section))
        $section = "&section=".$section;
    if (!empty($geid))
        $redirect_geid = "&geid=".$geid;
    func_header_location("product_modify.php?productid=".$productid.$redirect_geid.$section.$added);
}

/**
 * Fill empty product lng values
 */
function func_fill_empty_product_lng($data, $productid, $edit_lng)
{
    global $sql_tbl;

    $old_int_descr_data = func_query_first("SELECT * FROM $sql_tbl[products_lng] WHERE productid='$productid' AND code='$edit_lng'");

    if (empty($old_int_descr_data)) {
        $ge_product_info = func_query_hash("SELECT productid, product, descr, fulldescr, keywords FROM $sql_tbl[products] WHERE productid='$productid'", false, false);
        foreach ($ge_product_info[$productid] as $k => $v) {
            if (empty($data[$k]) && !empty($v))
                $old_int_descr_data[$k] = $v;
        }
    }

    if (!empty($old_int_descr_data))
        foreach ($old_int_descr_data as $k => $v) {
            if (empty($data[$k]))
                $data[$k] = addslashes($v);
        }

    return $data;
}

/*
* Save international descriptions
*/
function func_save_products_lng($product_lng, $productid, $edit_lng) 
{
    global $sql_tbl;

    if (empty($productid) || empty($edit_lng))
        return false;

    if (
        trim($product_lng["product"]) == "" 
        && trim($product_lng["keywords"]) == "" 
        && trim($product_lng["descr"]) == "" 
        && trim($product_lng["fulldescr"]) == ""
    ) {
        db_query("DELETE FROM $sql_tbl[products_lng] WHERE productid='$productid' AND code='$edit_lng'");
        return false;
    } else {
        func_array2insert("products_lng", $product_lng, true);
        return true;
    }
}

$fillerror = false;

if (!empty($geid)) {
    if (func_ge_count($geid) == 0)
        $geid = false;
}

$redirect_geid = '';

if (!empty($geid)) {

    $redirect_geid = '&geid=' . $geid;

    if (!func_ge_check($geid, $productid)) {

        if (!empty($productid)) {

            $top_message = array(
                'content' => func_get_langvar_by_name('lbl_trying_access_product_not_selected'),
                'type' => 'W'
            );
        }

        $productid = func_ge_each($geid);
        func_refresh();
    }
}

x_session_register('product_modified_data');

if (x_session_is_registered('search_data'))
    $smarty->assign('flag_search_result', 1);

/**
 * Define the location line
 */
$location[] = array(
    func_get_langvar_by_name('lbl_adm_product_management'),
    'search.php'
);

$avail_sections = array('main');

if (
    !empty($all_languages)
    && count($all_languages) > 1
) {
    $avail_sections[] = 'lng';
}

if (!empty($active_modules['Subscriptions'])) {
    $avail_sections[] = 'subscr';
}

if (!empty($active_modules['Product_Options'])) {
    $avail_sections[] = 'options';
    $avail_sections[] = 'variants';
}

if (!empty($active_modules['Wholesale_Trading'])) {
    $avail_sections[] = 'wholesale';
}

if (!empty($active_modules['Upselling_Products'])) {
    $avail_sections[] = 'upselling';
}

if (!empty($active_modules['Detailed_Product_Images'])) {
    $avail_sections[] = 'images';
}

if (!empty($active_modules['Customer_Reviews'])) {
    $avail_sections[] = 'reviews';
}

if (!empty($active_modules['Feature_Comparison'])) {
    $avail_sections[] = 'feature_class';
}

if (!empty($active_modules['Magnifier'])) {
    $avail_sections[] = 'zoomer';
}

if (!empty($active_modules['Product_Configurator'])) {
    $avail_sections[] = 'pclass';
}

/**
 * Define the current section
 */
if (!in_array($section, $avail_sections)) {
    $section = 'main';
}

/**
 * Add, modify product
 * Get product information
 */
if ($mode == 'list') {
    if (empty($productids)) {
        $top_message = array(
            'content' => func_get_langvar_by_name('lbl_please_select_products_for_editing'),
            'type' => 'I'
        );
        if (!empty($HTTP_REFERER)) {
            func_header_location($HTTP_REFERER);
        } else {
            func_header_location('search.php');
        }
    } else {
        $productids = array_keys($productids);
        $geid = func_ge_add($productids);
        $productid = $productids[0];
        func_refresh();
    }

}

if ($productid != '') {

    if(empty($edit_lng)) {
        $edit_lng = $shop_language;
    }

    // Get the product info or display 'Access denied' message if product does not exist
    $product_info = func_select_product($productid, $user_account['membershipid']);
    $product_info['image'] = array(
        'T' => func_image_properties('T', $productid),
        'P' => func_image_properties('P', $productid)
    );

    // Correct the location line
    $location[] = array(
        $product_info['product'],
        'product_modify.php?productid=' . $productid
    );

    // Get the product international descriptions
    $product_languages = func_query_first ("SELECT $sql_tbl[products_lng].* FROM $sql_tbl[products_lng] WHERE $sql_tbl[products_lng].productid='$productid' AND $sql_tbl[products_lng].code = '$edit_lng'");

    $smarty->assign('page_title', func_get_langvar_by_name('lbl_adm_product_management'));

} else {

    $smarty->assign('page_title', func_get_langvar_by_name('lbl_adm_add_product'));
    $location[] = array(func_get_langvar_by_name('lbl_add_product'), '');
}

if (empty($product_info)) {

    if ($login_type == 'A') {

        $providers = func_query("SELECT id, login, title, firstname, lastname FROM $sql_tbl[customers] WHERE usertype='P' ORDER BY login, lastname, firstname");
        if (!empty($providers)) {
            $smarty->assign('providers', $providers);
        } else {
            $top_message['content'] = func_get_langvar_by_name('msg_adm_warn_no_providers');
            $top_message['type'] = 'W';
            $smarty->assign('top_message', $top_message);
            $top_message = '';
            $section = 'error';
        }

    } else {

        $product_owner = $logged_userid;
    }

} else {

    $product_owner = addslashes($product_info['provider']);
}

if (!empty($product_owner)) {
    $provider_info = func_query_first("SELECT id, login, title, firstname, lastname FROM $sql_tbl[customers] WHERE id='$product_owner' AND usertype IN ('P','A')");
    $smarty->assign('provider_info', $provider_info);
}

$gdlib_enabled = func_check_gd();
$generate_thumbnail = '';

if ($REQUEST_METHOD == 'POST') {

    // Remove xss for untrusted providers from the trusted_post_variables

    if (!$user_account['allow_active_content']) {
        if (isset($js_code) && !empty($js_code))
            $js_code = func_xss_free($js_code, false, true);

        if (isset($posted_data) && !empty($posted_data))
            $posted_data = func_xss_free($posted_data, false, true);

        if (isset($efields) && !empty($efields))
            $efields = func_xss_free($efields, false, true);

        if (isset($product_lng) && !empty($product_lng))
            $product_lng = func_xss_free($product_lng, false, true);
    }

    // Delete product thumbnail

    if ($mode == 'delete_thumbnail' && !empty($productid)) {
        func_delete_image($productid, 'T');
        if ($fields['thumbnail'] == 'Y' && !empty($geid)) {
            while ($pid = func_ge_each($geid, 100, $productid)) {
                func_delete_image($pid, 'T');
            }
        }
        func_refresh();

    // Delete product image

    } elseif ($mode == 'delete_product_image' && !empty($productid)) {
        func_delete_image($productid, 'P');
        if ($fields['image'] == 'Y' && !empty($geid)) {
            while ($pid = func_ge_each($geid, 100, $productid)) {
                func_delete_image($pid, 'P');
            }
        }
        func_refresh();

    // Update international descriptions

    } elseif ($mode == 'update_lng') {
        if ($product_lng) {
            db_query("DELETE FROM $sql_tbl[products_lng] WHERE code='$edit_lng' AND productid='$productid'");

            if (!empty($products_lng) && !$user_account['allow_active_content'])
                foreach ($products_lng as $key => $val) {
                    $products_lng[$key] = func_xss_free($products_lng[$key], false, true);
                }

            if (!empty($fields['languages']) && $geid && $product_lng) {
                $_product_lng = $product_lng;
                $product_lng = array();

                foreach($fields['languages'] as $k => $v) {
                    if(isset($_product_lng[$k]))
                        $product_lng[$k] = $_product_lng[$k];
                }
            }

            if ($edit_lng == $config['default_admin_language'])
                func_array2update('products', $product_lng, "productid = '$productid'");

            $product_lng['code'] = $edit_lng;
            $product_lng['productid'] = $productid;

            if (!empty($fields['languages']) && $geid && $product_lng)
                $product_lng = func_fill_empty_product_lng($product_lng, $productid, $edit_lng);

            func_save_products_lng($product_lng, $productid, $edit_lng);

            if (!empty($fields['languages']) && $geid) {
                $product_lng_ge = array();
                foreach($fields['languages'] as $k => $v) {
                    if(isset($product_lng[$k])) {
                        $product_lng_ge[$k] = $product_lng[$k];
                    }
                }
                if(!empty($product_lng_ge)) {
                    $product_lng_ge['code'] = $edit_lng;
                    while ($pid = func_ge_each($geid, 1, $productid)) {
                        db_query("DELETE FROM $sql_tbl[products_lng] WHERE code='$edit_lng' AND productid='$pid'");
                        func_unset($product_lng_ge, 'productid');

                        if ($edit_lng == $config['default_admin_language']) {
                            $product_lng_ge_local = $product_lng_ge;
                            func_unset($product_lng_ge_local, 'code');
                            func_array2update('products', $product_lng_ge_local, "productid = '$pid'");
                        }

                        $product_lng_ge['productid'] = $pid;
                        $_product_lng_ge = array();
                        if ($product_lng_ge)
                            $_product_lng_ge = func_fill_empty_product_lng($product_lng_ge, $pid, $edit_lng);

                        func_save_products_lng($_product_lng_ge, $pid, $edit_lng);
                    }
                }
            }
            $top_message = array(
                'content' => func_get_langvar_by_name('msg_adm_product_int_upd'),
                'type' => 'I'
            );
        }

        func_refresh('lng');

    } elseif ($mode == 'del_lang') {

    // Delete selected international description

        db_query ("DELETE FROM $sql_tbl[products_lng] WHERE productid='$productid' AND code='$edit_lng'");
        if (!empty($del_lang_all)) {
            while ($pid = func_ge_each($geid, 100, $productid)) {
                db_query ("DELETE FROM $sql_tbl[products_lng] WHERE productid IN ('".implode("','", $pid)."') AND code='$edit_lng'");
            }
        }

        $top_message['content'] = func_get_langvar_by_name('msg_adm_product_int_del');
        $top_message['type'] = 'I';
        func_refresh('lng');

    } elseif ($mode == 'clean_urls_history') {
        if (empty($clean_urls_history) || !is_array($clean_urls_history)) {
            $top_message['content'] = func_get_langvar_by_name('err_clean_urls_history_empty');
            $top_message['type'] = 'E';

            func_refresh();
        }

        if (func_clean_url_history_delete(array_keys($clean_urls_history))) {
            $top_message['content'] = func_get_langvar_by_name('txt_clean_urls_history_deleted');
            $top_message['type'] = 'I';
        } else {
            $top_message['content'] = func_get_langvar_by_name('err_clean_urls_history_delete');
            $top_message['type'] = 'E';
        }

        func_refresh();

    } elseif ($mode == 'generate_thumbnail' && $gdlib_enabled) {

        $mode = 'product_modify';
        $generate_thumbnail = 'Y';

    } elseif ($mode == 'links' && !empty($productid)) {

        // Generate HTML-links
        func_header_location("product_links.php?productid=$productid");

    } elseif ($mode == 'delete' && !empty($productid)) {

        // Delete product
        func_header_location("process_product.php?mode=delete&productid=$productid&from=product");

    } elseif ($mode == 'clone' && !empty($productid)) {

        // Clone product
        include $xcart_dir.'/include/product_clone.php';

    } elseif($mode=="details" && !empty($productid)) {

        // Show product details
        func_header_location("product.php?productid=$productid");
    }
}

$smarty->assign('main', 'product_modify');

/**
 * This flag means that this product is configurator
 */
$is_pconf = false;

/**
 * Product Configurator module
 */
if (!empty($active_modules['Product_Configurator'])) {
    include $xcart_dir.'/modules/Product_Configurator/product_modify.php';
}

$pm_link = "product_modify.php?productid=$productid".$redirect_geid;

/**
 * Define data for the navigation within section
 */
$dialog_tools_data['left'][] = array(
    'link'  => $pm_link,
    'title' => func_get_langvar_by_name('lbl_product_details')
);

if (!empty($product_info)) {

    if (
        !empty($all_languages)
        && count($all_languages) > 1
    ) {
        $dialog_tools_data['left'][] = array(
            'link'  => $pm_link . '&section=lng',
            'title' => func_get_langvar_by_name('txt_international_descriptions')
        );
    }

    if (!empty($active_modules['Product_Options'])) {

        $dialog_tools_data['left'][] = array(
            'link'  => $pm_link . '&section=options',
            'title' => func_get_langvar_by_name('lbl_product_options')
        );

        if ($product_info['is_variants'] == 'Y') {
            $dialog_tools_data['left'][] = array(
                'link' => $pm_link . '&section=variants', 'title' => func_get_langvar_by_name('lbl_product_variants')
            );
        }
    }

    if (
        !empty($active_modules['Product_Configurator'])
        && !$is_pconf
    ) {
        $dialog_tools_data['left'][] = array(
            'link'  => $pm_link . '&section=pclass',
            'title' => func_get_langvar_by_name('lbl_pconf_product_classification')
        );
    }

    if (
        !empty($active_modules['Subscriptions'])
        && !$is_pconf
    ) {
        $dialog_tools_data['left'][] = array(
            'link'  => $pm_link . '&section=subscr',
            'title' => func_get_langvar_by_name('lbl_subscriptions')
        );
    }

    if (
        !empty($active_modules['Wholesale_Trading'])
        && $product_info['is_variants'] != 'Y'
        && !$is_pconf
    ) {
        $dialog_tools_data['left'][] = array(
            'link'  => $pm_link . '&section=wholesale',
            'title' => func_get_langvar_by_name('lbl_wholesale_prices')
        );
    }

    if (!empty($active_modules['Upselling_Products'])) {
        $dialog_tools_data['left'][] = array(
            'link'  => $pm_link . '&section=upselling',
            'title' => func_get_langvar_by_name('lbl_related_products')
        );
    }

    if (!empty($active_modules['Detailed_Product_Images'])) {
        $dialog_tools_data['left'][] = array(
            'link'  => $pm_link . '&section=images',
            'title' => func_get_langvar_by_name('lbl_detailed_images')
        );
    }

    if (!empty($active_modules['Magnifier'])) {
        $dialog_tools_data['left'][] = array(
            'link'  => $pm_link . '&section=zoomer',
            'title' => func_get_langvar_by_name('lbl_zoom_images')
        );
    }

    if (!empty($active_modules['Customer_Reviews'])) {
        $dialog_tools_data['left'][] = array(
            'link'  => $pm_link . '&section=reviews',
            'title' => func_get_langvar_by_name('lbl_customer_reviews')
        );
    }

    if (
        !empty($active_modules['Feature_Comparison'])
        && !$is_pconf
    ) {
        $dialog_tools_data['left'][] = array(
            'link'  => $pm_link . '&section=feature_class', 'title' => func_get_langvar_by_name('lbl_feature_class')
        );
    }

    if (!empty($product_info['clean_urls_history'])) {
        $dialog_tools_data['left'][] = array(
            'link'  => '#clean_url_history',
            'title' => func_get_langvar_by_name('lbl_clean_url_history')
        );
    }

    if ($is_pconf) {
        $dialog_tools_data['left'][] = array(
            'separator' => 'Y'
        );
        $dialog_tools_data['left'][] = array(
            'link'  => $pm_link . '&mode=pconf&edit=wizard',
            'title' => func_get_langvar_by_name('lbl_pconf_manage_confwiz')
        );
    }
}

$dialog_tools_data['right'][] = array(
    'link'  => 'search.php',
    'title' => func_get_langvar_by_name('lbl_search_products')
);

$dialog_tools_data['right'][] = array(
    'link'  => 'product_modify.php',
    'title' => func_get_langvar_by_name('lbl_add_product')
);

if (!empty($active_modules['Product_Configurator'])) {
    $dialog_tools_data['right'][] = array(
        'link'  => 'pconf.php',
        'title' => func_get_langvar_by_name('lbl_product_configurator')
    );
}

if (defined('IS_ADMIN_USER')) {
    $dialog_tools_data['right'][] = array(
        'link'  => $xcart_catalogs['admin'] . '/categories.php',
        'title' => func_get_langvar_by_name('lbl_categories')
    );
}
if (!empty($active_modules['Manufacturers'])) {
    $dialog_tools_data['right'][] = array(
        'link'  => 'manufacturers.php',
        'title' => func_get_langvar_by_name('lbl_manufacturers')
    );
}

$dialog_tools_data['right'][] = array(
    'link'  => 'orders.php',
    'title' => func_get_langvar_by_name('lbl_orders')
);

if (!empty($active_modules['Product_Configurator'])) {
    $dialog_tools_data['right'][] = array(
        'separator'=>'Y'
    );
    $dialog_tools_data['right'][] = array(
        'link'  => 'pconf.php?mode=search',
        'title' => func_get_langvar_by_name('lbl_pconf_search')
    );
    $dialog_tools_data['right'][] = array(
        'link'  => 'product_modify.php?mode=pconf',
        'title' => func_get_langvar_by_name('lbl_pconf_confproduct')
    );
    $dialog_tools_data['right'][] = array(
        'link'  => 'pconf.php?mode=types',
        'title' => func_get_langvar_by_name('lbl_pconf_define_types')
    );
    $dialog_tools_data['right'][] = array(
        'link'  => 'pconf.php?mode=about',
        'title' => func_get_langvar_by_name('lbl_pconf_about')
    );
}

/**
 * Update product details or create product
 */
if (($REQUEST_METHOD == 'POST') && ($mode == 'product_modify')) {

    $provider = func_query_first_cell("SELECT id FROM $sql_tbl[customers] WHERE id = '" . intval($login_type == "A" ? $provider : $logged_userid)."'");

    if (!empty($productid) && empty($provider)) {
        $provider = func_query_first_cell("SELECT provider FROM $sql_tbl[products] WHERE productid = '$productid'");
    }

    $sku_is_exist = (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products] WHERE productcode='$productcode' AND productid!='$productid' AND provider = '" . addslashes($login_type == "A" ? $provider : $logged_userid) . "'") ? true : false);
    if (
        !empty($active_modules['Product_Options']) &&
        !$sku_is_exist &&
        func_query_first_cell("SELECT COUNT($sql_tbl[variants].variantid) FROM $sql_tbl[variants], $sql_tbl[products] WHERE $sql_tbl[products].productid = $sql_tbl[variants].productid AND $sql_tbl[variants].productcode = '$productcode' AND $sql_tbl[products].provider = '" . intval($login_type == "A" ? $provider : $logged_userid)."'") > 0
    )
        $sku_is_exist = true;

    // Check if form filled with errors
    $is_variant = false;
    if (!empty($productid) && !empty($active_modules['Product_Options']))
        $is_variant = (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[variants] WHERE productid = '$productid'") > 0);

    $is_configurable = false;
    if (!empty($productid) && !empty($active_modules['Product_Options']))
        $is_variant = (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[variants] WHERE productid = '$productid'") > 0);

    $isnt_perms_T = func_check_image_storage_perms($file_upload_data, 'T');
    $isnt_perms_P = func_check_image_storage_perms($file_upload_data, 'P');

    $esd_err = !empty($distribution) && !is_url($distribution) && !func_is_allowed_file($distribution);

    $_POST['price'] = $price = abs(doubleval($price));
    $_POST['list_price'] = $list_price = abs(doubleval($list_price));

    $clean_url = trim(stripslashes($clean_url));

    // Check Clean URL format.
    if ($config['SEO']['clean_urls_enabled'] == 'N' || !empty($product_info) && isset($product_info['clean_url']) && !zerolen($product_info['clean_url']) && $product_info['clean_url'] == $clean_url) {
        $clean_url_check_result = true;
    } else {
        list($clean_url_check_result, $check_url_error_code) = func_clean_url_validate($clean_url);
    }

    $xssfree = true;
    if (!$user_account['allow_active_content']) {
        $res_descr = func_clear_from_xss($descr, false, true);
        $res_fulldescr = func_clear_from_xss($fulldescr, false, true);
        $xssfree_descr = !$res_descr['changed'];
        $xssfree_fulldescr = !$res_fulldescr['changed'];
        $xssfree = $xssfree_descr && $xssfree_fulldescr;
    }

    $fillerror = (($categoryid == '') ||
        empty($provider) ||
        empty($product) ||
        $clean_url_check_result == false ||
        empty($descr) ||
        ($avail == '' && !$is_variant && !$is_pconf) ||
        ($productcode == '') ||
        $sku_is_exist) ||
        !$xssfree ||
        $esd_err ||
        $isnt_perms_T !== true ||
        $isnt_perms_P !== true;

    if (!$fillerror) {

    // If no errors

        if ($xssfree && !$user_account['allow_active_content']) {
            $descr = $res_descr['html'];
            $fulldescr = $res_fulldescr['html'];
        }

        if (empty($productid)) {

        // Create a new product

            $provider = ($login_type == 'A' ? $provider : $logged_userid);

            // Insert new product into the database and get its productid

            db_query("INSERT INTO $sql_tbl[products] (productcode, provider, add_date) VALUES ('$productcode', '$provider', '".XC_TIME."')");

            $productid = db_insert_id();

            // Insert price and image
            db_query("INSERT INTO $sql_tbl[pricing] (productid, quantity, price) VALUES ('$productid', '1', '".$price."')");

            $save_image_error_T = $save_image_error_P = false;

            // If thumbnail was posted
            if (func_check_image_posted($file_upload_data, 'T')) {
                if (!func_save_image($file_upload_data, 'T', $productid))
                    $save_image_error_T = true;
            }

            // If image was posted
            if (func_check_image_posted($file_upload_data, 'P')) {
                if (!func_save_image($file_upload_data, 'P', $productid))
                    $save_image_error_P = true;
            }

            // Mark product as configurable
            if ($active_modules['Product_Configurator'] && $is_pconf)
                db_query("UPDATE $sql_tbl[products] SET product_type='C' WHERE productid='$productid'");

            $status = 'created';

        } else {

            // Update the existing product

            if (!empty($productid) && !empty($active_modules['Product_Options'])) {
                $is_variant = (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[variants] WHERE productid = '$productid'") > 0);
            }

            // Update the default price
            if (!$is_variant)
                func_array2update('pricing', array('price' => $price), "productid='$productid' AND quantity='1' AND membershipid = '0' AND variantid = '0'");

            if ($fields['price'] == 'Y' && $geid && !$is_variant) {
                while ($pid = func_ge_each($geid, 1, $productid)) {
                    if (
                        empty($active_modules['Product_Options']) ||
                        func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[variants] WHERE productid = '$pid'") == 0
                    ) {
                        func_array2update('pricing', array('price' => $price), "productid='$pid' AND quantity='1' AND membershipid = '0' AND variantid = '0'");
                    }
                }
            }

            $save_image_error_T = $save_image_error_P = false;

            // If thumbnail was posted
            if (func_check_image_posted($file_upload_data, 'T')) {
                if (!func_save_image($file_upload_data, 'T', $productid))
                    $save_image_error_T = true;
            }

            // If image was posted
            if (func_check_image_posted($file_upload_data, 'P')) {
                if (!func_save_image($file_upload_data, 'P', $productid))
                    $save_image_error_P = true;
            }

            if ($generate_thumbnail == 'Y')
                func_generate_image($productid);

            if($fields['thumbnail'] == 'Y' && $geid) {
                if (!$save_image_error_T) {
                    $img = func_addslashes(func_query_first("SELECT * FROM $sql_tbl[images_T] WHERE id = '$productid'"));
                    unset($img['imageid']);
                    while ($pid = func_ge_each($geid, 1, $productid)) {
                        $img['id'] = $pid;
                        func_array2insert($sql_tbl['images_T'], $img, true);
                    }
                }
            }

            if($fields['image'] == 'Y' && $geid) {
                if (!$save_image_error_P) {
                    $img = func_addslashes(func_query_first("SELECT * FROM $sql_tbl[images_P] WHERE id = '$productid'"));
                    unset($img['imageid']);
                    while ($pid = func_ge_each($geid, 1, $productid)) {
                        $img['id'] = $pid;
                        func_array2insert($sql_tbl['images_P'], $img, true);
                    }
                }
            }

            $status = 'modified';
        }

        // For existing product: get the categories list before updating
        if ($product_info) {
            $old_product_categories = func_query_column("SELECT categoryid FROM $sql_tbl[products_categories] WHERE productid='$productid'");
        }

        // Prepare and update categories associated with product...
        $query_data_cat = array(
            'categoryid' => $categoryid,
            'productid'  => $productid,
            'main'       => 'Y'
        );

        if(!func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products_categories] WHERE categoryid = '$categoryid' AND productid = '$productid' AND main = 'Y'")) {
            db_query("DELETE FROM $sql_tbl[products_categories] WHERE productid = '$productid' AND (main = 'Y' OR categoryid = '$categoryid')");
            func_array2insert('products_categories', $query_data_cat);
        }

        if($geid && $fields['categoryid']) {

            while ($pid = func_ge_each($geid, 1, $productid)) {

                if(!func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products_categories] WHERE categoryid = '$categoryid' AND productid = '$pid' AND main = 'Y'")) {
                    db_query("DELETE FROM $sql_tbl[products_categories] WHERE productid = '$pid' AND (main = 'Y' OR categoryid = '$categoryid')");
                    $query_data_cat['productid'] = $pid;
                    func_array2insert('products_categories', $query_data_cat);
                }
            }
        }

        if ($categoryids) {

            foreach ($categoryids as $k=>$v) {
                $query_data_cat = array(
                    'categoryid' => $v,
                    'productid'  => $productid,
                    'main'       => 'N'
                );

                if (!func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products_categories] WHERE categoryid = '$v' AND productid = '$productid'")) {
                    func_array2insert('products_categories', $query_data_cat);
                }

                if($geid && $fields['categoryids']) {

                    while ($pid = func_ge_each($geid, 1, $productid)) {
                        if(!func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products_categories] WHERE categoryid = '$v' AND productid = '$pid'")) {
                            $query_data_cat['productid'] = $pid;
                            func_array2insert('products_categories', $query_data_cat);
                        }
                    }
                }
            }

            db_query("DELETE FROM $sql_tbl[products_categories] WHERE productid = '$productid' AND main = 'N' AND categoryid NOT IN ('".implode("','", $categoryids)."')");

            if ($geid && $fields['categoryids']) {
                while ($pid = func_ge_each($geid, 100, $productid)) {
                    db_query("DELETE FROM $sql_tbl[products_categories] WHERE productid IN ('".implode("','",$pid)."') AND main = 'N' AND categoryid NOT IN ('".implode("','", $categoryids)."')");
                }
            }

        } else {

            db_query("DELETE FROM $sql_tbl[products_categories] WHERE productid = '$productid' AND main = 'N'");
            if($geid && $fields['categoryids']) {
                while ($pid = func_ge_each($geid, 100, $productid)) {
                    db_query("DELETE FROM $sql_tbl[products_categories] WHERE productid IN ('".implode("','",$pid)."') AND main = 'N'");
                }
            }
        }

        // Correct the min_amount
        if (empty($min_amount) || intval($min_amount) == 0) {
            $min_amount = 1;
        }

        // Update product data

        $small_item = $small_item == 'Y' ? 'N' : 'Y';

        $query_data = array(
            'product'           => $product,
            'keywords'          => $keywords,
            'descr'             => $descr,
            'fulldescr'         => $fulldescr,
            'list_price'        => $list_price,
            'productcode'       => $productcode,
            'forsale'           => $forsale,
            'distribution'      => $distribution,
            'free_shipping'     => $free_shipping,
            'shipping_freight'  => $shipping_freight,
            'small_item'        => $small_item,
            'discount_avail'    => $discount_avail,
            'min_amount'        => $min_amount,
            'return_time'       => $return_time,
            'low_avail_limit'   => intval($low_avail_limit),
            'free_tax'          => $free_tax,
            'separate_box'      => $separate_box,
            'meta_keywords'     => $meta_keywords,
            'meta_description'  => $meta_description,
            'title_tag'         => $title_tag,
        );

        if (!$is_variant) {
            $query_data['weight'] = $weight;
            $query_data['avail']  = $avail;
        }

        if ($small_item == 'N') {
            $query_data['length'] = $length;
            $query_data['width']  = $width;
            $query_data['height'] = $height;
        }

        $query_data = func_adjust_ship_box_data($query_data, $small_item, $separate_box, $items_per_box);

        func_array2update('products', $query_data, "productid = '$productid'");

        if ($config['SEO']['clean_urls_enabled'] == 'N') {
            // Autogenerate clean URL.
            $clean_url = func_clean_url_autogenerate('P', $productid, array('product' => $product, 'productcode' => $productcode));
            $clean_url_save_in_history = false;
        }

        // Insert/Update Clean URL.
        if (func_clean_url_resource_has_record('P', $productid)) {
            func_clean_url_update($clean_url, 'P', $productid, $clean_url_save_in_history == 'Y');
        } else {
            func_clean_url_add($clean_url, 'P', $productid);
        }

        if (
            $edit_lng == $config['default_admin_language']
            || empty($product_info)
        ) {

            $int_descr_data = array(
                'productid' => $productid,
                'code'      => (empty($product_info) ? $config['default_admin_language'] : $edit_lng),
                'product'   => $product,
                'descr'     => $descr,
                'fulldescr' => $fulldescr,
                'keywords'  => $keywords
            );

            func_array2insert('products_lng', $int_descr_data, true);
        }

        // Update memberships
        func_membership_update('product', $productid, $membershipids);
        if ($geid && $fields['membershipids'] == 'Y') {
            while($pid = func_ge_each($geid, 1, $productid)) {
                func_membership_update('product', $pid, $membershipids);
            }
        }

        // Update taxes
        db_query("DELETE FROM $sql_tbl[product_taxes] WHERE productid='$productid'");
        if($geid && $fields['taxes']) {
            while ($pid = func_ge_each($geid, 100, $productid)) {
                db_query("DELETE FROM $sql_tbl[product_taxes] WHERE productid IN ('".implode("','", $pid)."')");
            }
        }

        if (!empty($taxes) && is_array($taxes)) {

            foreach ($taxes as $k=>$v) {

                if (intval($v) > 0) {

                    $query_data = array(
                        'productid' => $productid,
                        'taxid' => intval($v)
                    );

                    func_array2insert('product_taxes', $query_data, true);

                    if($geid && $fields['taxes']) {

                        while ($pid = func_ge_each($geid, 1, $productid)) {
                            $query_data['productid'] = $pid;
                            func_array2insert('product_taxes', $query_data, true);
                        }
                    }
                }
            }
        }

        // Group editing of products functionality
        if ($geid && !empty($fields)) {

            $query_data = array();

            foreach($fields as $k => $v) {

                if (
                    !in_array($k, array('efields', 'price', 'image', 'thumbnail', 'categoryid', 'categoryids', 'taxes', 'membershipids','manufacturer','valid_for_gcheckout', 'dimensions', 'sp_data')) &&
                    (!$is_variant || !in_array($k, array('avail', 'weight')))
                ) {
                    $query_data[$k] = $$k;
                }
                elseif ($k == 'dimensions') {
                    $query_data['length'] = $length;
                    $query_data['width'] = $width;
                    $query_data['height'] = $height;
                }
            }

            if (!empty($query_data)) {
                $is_variant_request = !$is_variant && (isset($query_data['avail']) || isset($query_data['weight'])) && !empty($active_modules["Product_Options"]);

                // "Ship in a separate box" depends on "Use the dimensions of this product for shipping cost calculation" bt:84873
                $query_data = func_adjust_ship_box_data($query_data,
                    isset($fields['small_item']) ? $small_item : 'N',
                    isset($fields['separate_box']) ? $separate_box : 'N',
                    $items_per_box
                );

                while ($pid = func_ge_each($geid, $is_variant_request ? 1 : 100, $productid)) {
                    $query_data_sub = $query_data;
                    if ($is_variant_request) {
                        if (
                            !empty($active_modules['Product_Options']) &&
                            func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[variants] WHERE productid = '$pid'") > 0
                        ) {
                            func_unset($query_data_sub, 'avail', 'weight');
                        }
                        func_array2update('products', $query_data_sub, "productid = '$pid'");

                    } else {
                        func_array2update('products', $query_data, "productid IN ('".implode("','", $pid)."')");
                    }
                }

                if ($edit_lng == $config['default_admin_language']) {
                    $int_descr_data = array();
                    foreach ($query_data as $k => $v) {
                        if (in_array($k, array('product', 'descr', 'fulldescr', 'keywords'))) {
                            $int_descr_data[$k] = $v;
                        }
                    }

                    if (!empty($int_descr_data)) {
                        $int_descr_data['code'] = $edit_lng;
                        while ($pid = func_ge_each($geid, 1, $productid)) {
                            $int_descr_data['productid'] = $pid;
                            $_int_descr_data = func_fill_empty_product_lng($int_descr_data, $pid, $edit_lng);
                            func_array2insert('products_lng', $_int_descr_data, true);
                        }
                    }

                }
            }
        }

        // Update categories data cache
        if (!empty($active_modules['Flyout_Menus']) && func_fc_use_cache()) {
            func_fc_build_categories(1);
        }

        // Update products counter for selected categories

        if (is_array($old_product_categories))
            $categoryids = func_array_merge($old_product_categories, $categoryids);
        
        settype($categoryids, 'array');
        $categoryids = func_array_merge($categoryids, array($categoryid));

        $categoryids = func_array_merge($categoryids, func_get_category_parents($categoryids));
        func_recalc_product_count($categoryids);

        if ($status == 'created') {
            $top_message['content'] = func_get_langvar_by_name('msg_adm_product_add');
            $top_message['type'] = 'I';
        }
        elseif ($status == 'modified') {
            $top_message['content'] = func_get_langvar_by_name('msg_adm_product_upd');
            $top_message['type'] = 'I';
        }

        if ($save_image_error_T && $save_image_error_P) {
            $top_message['content'] .= "<br />" . func_get_langvar_by_name('msg_adm_err_save_images');
            $top_message['type'] = 'W';
        } elseif ($save_image_error_T || $save_image_error_P) {
            $top_message['content'] .= "<br />" . func_get_langvar_by_name('msg_adm_err_save_image', array('image' => strtolower(func_get_langvar_by_name(($save_image_error_T ? 'lbl_product_thumbnail' : 'lbl_product_image'), false, false, true))));
            $top_message['type'] = 'W';
        }

        if (!empty($active_modules['Extra_Fields'])) {
            include $xcart_dir.'/modules/Extra_Fields/extra_fields_modify.php';
        }

        if (!empty($active_modules['Manufacturers'])) {
            if (
                file_exists($xcart_dir.'/modules/Manufacturers/product_manufacturer.php')
                && is_readable($xcart_dir.'/modules/Manufacturers/product_manufacturer.php')
            ) {
                include $xcart_dir.'/modules/Manufacturers/product_manufacturer.php';
            }
        }

        if (!empty($active_modules['Google_Checkout']) && $gcheckout_enabled) {
            if (
                file_exists($xcart_dir.'/modules/Google_Checkout/product_modify.php')
                && is_readable($xcart_dir.'/modules/Google_Checkout/product_modify.php')
            ) {
                include $xcart_dir.'/modules/Google_Checkout/product_modify.php';
            }
        }

        if ($active_modules['Special_Offers']) {
            include $xcart_dir.'/modules/Special_Offers/product_modify.php';
        }

        if ($active_modules['Recommended_Products']) {
            func_refresh_product_rnd_keys($productid);
        }

        func_build_quick_flags($productid);
        func_build_quick_prices($productid);

        if ($geid && !empty($fields)) {
            while ($pid = func_ge_each($geid, 100, $productid)) {
                func_build_quick_flags($pid);
                func_build_quick_prices($pid);
            }
        }

    } else {

        // Form filled with errors

        $top_message = array(
            'content' => '',
            'type' => 'E',
        );
        if ($sku_is_exist) {
            $top_message['content'] = func_get_langvar_by_name("msg_adm_err_sku_exist");
            $top_message['fillerror'] = true;

        } elseif ($clean_url_check_result == false) {
            $top_message['content'] = func_get_langvar_by_name('err_'.strtolower($check_url_error_code));
            $top_message['clean_url_fill_error'] = true;

        } elseif ($isnt_perms_T !== true) {
            $top_message['content'] = $isnt_perms_T['content'];

        } elseif ($isnt_perms_P !== true) {
            $top_message['content'] = $isnt_perms_P['content'];

        } elseif ($esd_err) {
            $top_message['content'] = func_get_langvar_by_name("txt_wrong_esd_file_ext");

        } elseif (!$xssfree) {
            $top_message['content'] = func_get_langvar_by_name("msg_untrusted_provider");
            $top_message['fillerror'] = true;

        } else {
            $top_message['content'] = func_get_langvar_by_name("msg_adm_err_product_upd");
            $top_message['fillerror'] = true;
        }

        $product_modified_data = $_POST;
        foreach ($product_modified_data as $k => $v) {
            if (!is_array($v))
                $product_modified_data[$k] = stripslashes($v);
        }
        if (!empty($active_modules['Extra_Fields']) && !empty($product_modified_data['efields'])) {
            $product_modified_data['efields'] = array_map("stripslashes", $product_modified_data['efields']);
        }

        if (!$xssfree) {
            $product_modified_data['xss_descr'] = ($xssfree_descr) ? 'N' : 'Y';
            $product_modified_data['xss_fulldescr'] = ($xssfree_fulldescr) ? 'N' : 'Y';
        }

        $product_modified_data['productid'] = $productid;

        if (!empty($product_modified_data['membershipids'])) {
            if (in_array("-1", $product_modified_data['membershipids'])) {
                $product_modified_data['membershipids'] = false;

            } else {
                $product_modified_data['membershipids'] = array_flip($product_modified_data['membershipids']);
                foreach ($product_modified_data['membershipids'] as $mid => $m) {
                    $product_modified_data['membershipids'][$mid] = true;
                }

            }

        } else {
            $product_modified_data['membershipids'] = false;
        }

        if ($file_upload_data['T'] && $file_upload_data['T']['is_redirect'] && $skip_image['T'] != 'Y') {
            $file_upload_data['T']['is_redirect'] = false;
            $product_modified_data['is_image_T'] = true;
        }

        if ($file_upload_data['P'] && $file_upload_data['P']['is_redirect'] && $skip_image['P'] != 'Y') {
            $file_upload_data['P']['is_redirect'] = false;
            $product_modified_data['is_image_P'] = true;
        }

    }

    func_refresh();
}

/**
 * Detailed_Product_Images module
 */
if (!empty($active_modules['Detailed_Product_Images'])) {
    include $xcart_dir.'/modules/Detailed_Product_Images/product_images_modify.php';
}

/**
 * Magnifier module
 */
if (!empty($active_modules['Magnifier'])) {
    include $xcart_dir.'/modules/Magnifier/product_magnifier_modify.php';
}

if (empty($active_modules['Product_Configurator']) || !$is_pconf) {

    // Subscription module

    if (!empty($active_modules['Subscriptions'])) {
        include $xcart_dir.'/modules/Subscriptions/subscription_modify.php';
    }

    // Wholesale trading module

    if (!empty($active_modules['Wholesale_Trading']) && $product_info['is_variants'] != 'Y') {
        include $xcart_dir.'/modules/Wholesale_Trading/product_wholesale.php';
    }

    // Product Configurator module

    if (!empty($active_modules['Product_Configurator']))
        include $xcart_dir.'/modules/Product_Configurator/pconf_classification.php';
} #/ if ($mode != 'pconf')

/**
 * Manufacturers module
 */
if (!empty($active_modules['Manufacturers'])) {
    if (
        file_exists($xcart_dir.'/modules/Manufacturers/product_manufacturer.php')
        && is_readable($xcart_dir.'/modules/Manufacturers/product_manufacturer.php')
    ) {
        include $xcart_dir.'/modules/Manufacturers/product_manufacturer.php';
    }
}

/**
 * Extra fields module
 */
if (!empty($active_modules['Extra_Fields'])) {
    $extra_fields_provider = ( $current_area == 'A' ? $product_info['provider'] : $logged_userid );
    include $xcart_dir.'/modules/Extra_Fields/extra_fields.php';
}

/**
 * Product options module
 */
if (!empty($active_modules['Product_Options'])) {
    if($section == 'options')
        if (
            file_exists($xcart_dir.'/modules/Product_Options/product_options.php')
            && is_readable($xcart_dir.'/modules/Product_Options/product_options.php')
        ) {
            include $xcart_dir.'/modules/Product_Options/product_options.php';
        }
    if($section == 'variants')
        include $xcart_dir.'/modules/Product_Options/product_variants.php';
}

/**
 * Feature comparision module
 */
if (!empty($active_modules['Feature_Comparison']))
    if (
        file_exists($xcart_dir.'/modules/Feature_Comparison/product_class.php')
        && is_readable($xcart_dir.'/modules/Feature_Comparison/product_class.php')
    ) {
        include $xcart_dir.'/modules/Feature_Comparison/product_class.php';
    }

/**
 * Upselling products module
 */
if (!empty($active_modules['Upselling_Products']))
    include $xcart_dir.'/modules/Upselling_Products/edit_upsales.php';

/**
 * Customer Reviews module
 */
include $xcart_dir.'/include/reviews.php';

if (($productid != '') && !$fillerror) {
    $product_info = func_select_product($productid, $user_account['membershipid']);
    $product_info['image'] = array(
        'T' => func_image_properties('T', $productid),
        'P' => func_image_properties('P', $productid)
    );

    $product_info['internal_url'] = func_get_resource_url("P", $productid);
}

/**
 * Obtain VAT rates
 */
if ($single_mode)
    $provider_condition = '';
elseif ($current_area == 'A')
    $provider_condition = "AND provider='$product_info[provider]'";
else
    $provider_condition = "AND provider='$logged_userid'";

if (empty($product_info))
    $smarty->assign('new_product', 1);

if (!empty($product_modified_data)) {

    // Restore saved product data
    $product_info = $product_modified_data;

    if (!empty($active_modules['Product_Options']) && !empty($product_info['productid'])) {
        $product_info['is_variants'] = (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[variants] WHERE productid = '$product_info[productid]'") > 0) ? "Y" : "";
    }

    if (!empty($active_modules['Extra_Fields']) && !empty($product_info['efields']) && !empty($extra_fields)) {
        foreach ($extra_fields as $fid => $f) {
            if (isset($product_info['efields'][$f['fieldid']])) {
                $extra_fields[$fid]['is_value'] = 'Y';
                $extra_fields[$fid]['field_value'] = $product_info['efields'][$f['fieldid']];
            }
        }
        $smarty->assign('extra_fields', $extra_fields);
        unset($product_info['efields']);
    }

    if (!empty($product_info['categoryids']) && is_array($product_info['categoryids'])) {
        $product_info['add_categoryids'] = array_flip($product_info['categoryids']);
        foreach ($product_info['add_categoryids'] as $k => $v)
            $product_info['add_categoryids'][$k] = true;
    }

    if ($product_modified_data['is_image_T'] && $file_upload_data['T'] && $file_upload_data['T']['is_redirect']) {
        $file_upload_data['T']['is_redirect'] = false;
    }

    if ($product_modified_data['is_image_P'] && $file_upload_data['P'] && $file_upload_data['P']['is_redirect']) {
        $file_upload_data['P']['is_redirect'] = false;
    }

}

if (empty($product_info)) {

    // Define default SKU value
    $sku_prefix = 'SKU';
    $product_info['productcode'] = func_query_first_cell("SELECT MAX(productid) FROM $sql_tbl[products]");
    $plus = 1;
    while (
        func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products] WHERE productcode='".$sku_prefix.($product_info['productcode']+$plus)."'") > 0 ||
        (!empty($active_modules['Product_Options']) &&     func_query_first_cell("SELECT COUNT($sql_tbl[variants].variantid) FROM $sql_tbl[variants], $sql_tbl[products] WHERE $sql_tbl[products].productid = $sql_tbl[variants].productid AND $sql_tbl[variants].productcode = '".$sku_prefix.($product_info['productcode']+$plus)."'") > 0)
        ) {
        $plus++;
    }

    $product_info['productcode'] = $sku_prefix.($product_info['productcode']+$plus);
}

$taxes = func_query("SELECT $sql_tbl[taxes].*, COUNT($sql_tbl[product_taxes].productid) as selected FROM $sql_tbl[taxes] LEFT JOIN $sql_tbl[product_taxes] ON $sql_tbl[product_taxes].taxid = $sql_tbl[taxes].taxid AND $sql_tbl[product_taxes].productid = '$productid' GROUP BY $sql_tbl[taxes].taxid");

if (!empty($product_modified_data['taxes']) && !empty($taxes)) {

    foreach ($taxes as $k => $v) {
        $taxes[$k]['tax_name'] = str_replace(" ", "&nbsp;", $v['tax_name']);
        if (in_array($v['taxid'], $product_modified_data['taxes']))
            $taxes[$k]['selected'] = 1;
    }

}

$smarty->assign('taxes',        $taxes);

$smarty->assign('location',     $location);
$smarty->assign('section',      $section);

$smarty->assign('query_string', urlencode($QUERY_STRING));
$smarty->assign('product',      $product_info);
$smarty->assign('productid',    $product_info['productid']);

if (!empty($geid)) {

    $objects_per_page = $config['Appearance']['products_per_page_admin'];
    $total_items = func_ge_count($geid);
    include $xcart_dir.'/include/navigation.php';
    $smarty->assign('products', func_query("SELECT $sql_tbl[products].product, $sql_tbl[products].productcode, $sql_tbl[products].productid FROM $sql_tbl[products], $sql_tbl[ge_products] WHERE $sql_tbl[products].productid = $sql_tbl[ge_products].productid AND $sql_tbl[ge_products].geid = '$geid' LIMIT $first_page, $objects_per_page"));
    $smarty->assign('first_item', $first_page+1);
    $smarty->assign('last_item', min($first_page+$objects_per_page, $total_items));
    $smarty->assign('redirect_geid', str_replace("&", "&amp;", $redirect_geid));
}

$smarty->assign('navigation_script', "product_modify.php?section=$section&productid=".$productid.$redirect_geid);

$product_modified_data = '';

$smarty->assign('fillerror', $fillerror);

x_session_save();

if (!empty($categoryid)) {
    $smarty->assign('default_categoryid', intval($categoryid));
}

$smarty->assign('product_languages', $product_languages);

$memberships = func_get_memberships('C');
if (!empty($memberships)) {
    $smarty->assign('memberships', $memberships);
}

if (
    !empty($active_modules['Product_Options'])
    && $product_info['is_variants'] == 'Y'
) {
    $smarty->assign('variant_href', $pm_link."&amp;section=variants");
}

$smarty->assign('gdlib_enabled', $gdlib_enabled);
$smarty->assign('user_account',  $user_account);

if (!empty($geid)) {
    $smarty->assign('geid', $geid);
}

x_load('category');
$smarty->assign('allcategories', func_data_cache_get("get_categories_tree", array(0, true, $shop_language, $user_account['membershipid'])));

?>
