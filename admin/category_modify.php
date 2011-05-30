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
 * Category editing interface
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: category_modify.php,v 1.136.2.8 2011/01/10 13:11:45 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

define('IS_MULTILANGUAGE', true);
define('USE_TRUSTED_POST_VARIABLES',1);
$trusted_post_variables = array('description','category_lng');

require './auth.php';
require $xcart_dir.'/include/security.php';

x_load('backoffice','category','image');

x_session_register('file_upload_data');
x_session_register('category_modified_data');

/**
 * Update category or create new
 */

if (empty($mode))
    $mode = '';

$errors = array();

if ($REQUEST_METHOD == 'POST') {
/**
 * Add/update/process category data
 */

    if ($mode == 'update_lng') {

        // Process multilingual descriptions

        if ($shop_language == $config['default_admin_language']) {
            $_category_lng = $category_lng;
            if (empty($_category_lng['category']))
                func_unset($_category_lng, 'category');
            func_array2update('categories', $_category_lng, "categoryid = '$cat'");
        }

        $category_lng['code'] = $shop_language;
        $category_lng['categoryid'] = $cat;
        func_array2insert('categories_lng', $category_lng, true);

        // Update categories data cache for Fancy categories module
        if (!empty($active_modules['Flyout_Menus']) && func_fc_use_cache()) {
            func_fc_build_categories(1, false, array($shop_language));
        }

        $top_message = array(
            'content' => func_get_langvar_by_name('msg_adm_category_int_upd'),
            'type' => 'I'
        );

        func_data_cache_clear('get_categories_tree');
        func_data_cache_clear('get_offers_categoryid');
        func_header_location("category_modify.php?section=lng&cat=$cat&lng_updated");

    } elseif ($mode == 'update' || $mode == 'add') {

        // Add/Update category data

        $category = trim($category);

        if (empty($category)) {
            $errors[] = func_get_langvar_by_name('err_filling_form');
        }

        $clean_url = trim(stripslashes($clean_url));

        // Check Clean URL format.
        $current_clean_url = NULL;

        if ($cat > 0) {
            $current_clean_url = func_clean_url_get_raw_resource_url('C', $cat);
        }

        if ($config['SEO']['clean_urls_enabled'] == 'N' || $mode != 'add' && $cat > 0 && !zerolen($current_clean_url) && $current_clean_url == $clean_url) {
            $clean_url_check_result = true;
        } else {
            list($clean_url_check_result, $check_url_error_code) = func_clean_url_validate($clean_url);
        }

        if ($clean_url_check_result == false) {
            $errors[] = func_get_langvar_by_name('err_'.strtolower($check_url_error_code));
            $clean_url_fill_error = true;
        }

        // Check permissions

        $perms_C = func_check_image_storage_perms($file_upload_data, 'C');

        if ($perms_C !== true) {
            $errors[] = $perms_C['content'];
        }

        // Prepare an array for further processing

        $data = array (
            'category'              => $category,
            'description'           => $description,
            'meta_description'      => $meta_description,
            'meta_keywords'         => $meta_keywords,
            'title_tag'             => $title_tag,
            'avail'                 => $avail,
            'order_by'              => $order_by,
            'override_child_meta'   => $override_child_meta
        );

        // Store changes and display errors

        if (!empty($errors)) {

            $category_modified_data = $data;

            $top_message = array(
                'type' => 'E',
                'content' => implode("<br /><br />", $errors),
                'clean_url_fill_error' => $clean_url_fill_error
            );

            func_header_location("category_modify.php?mode=$mode&cat=".($mode == 'add' ? $parent : $cat));
        }

        if ($mode == 'add') {

            // Add new category

            $parent = intval($parent);

            $max_categoryid = func_query_first_cell("SELECT categoryid FROM $sql_tbl[categories] ORDER BY categoryid DESC LIMIT 1");

            // Create a new category: add main data
            $cat = func_insert_category($parent, $order_by, $data['category']);

            if ($cat == $max_categoryid) {
                $top_message = array(
                    'content' => func_get_langvar_by_name('msg_adm_category_max_exceed'),
                    'type' => 'E'
                );
                func_header_location("category_modify.php?mode=add&cat=".($mode == 'add' ? $parent : $cat));
            }

            $top_message = array(
                'content' => func_get_langvar_by_name('msg_adm_category_add'),
                'type' => 'I'
            );

        } else {

            $top_message = array(
                'content' => func_get_langvar_by_name('msg_adm_category_upd'),
                'type' => 'I'
            );
        }

        $old_pos = func_query_first_cell("SELECT order_by FROM $sql_tbl[categories] WHERE categoryid = '$cat'");

        // Update general data of category

        func_array2update('categories', $data, "categoryid = '$cat'");
        func_membership_update('category', $cat, $membershipids);

        if ($config['SEO']['clean_urls_enabled'] == 'N') {
            // Autogenerate clean URL.
            $clean_url = func_clean_url_autogenerate('C', $cat, array('category' => $data['category']));
            $clean_url_save_in_history = false;
        }

        // Insert/Update Clean URL.
        if (func_clean_url_resource_has_record('C', $cat)) {
            func_clean_url_update($clean_url, 'C', $cat, $clean_url_save_in_history == 'Y');
        } else {
            func_clean_url_add($clean_url, 'C', $cat);
        }

        if ($shop_language == $config['default_admin_language']) {

            $int_cat_data = array(
                'categoryid'  => $cat,
                'code'        => $shop_language,
                'category'    => $data['category'],
                'description' => $data['description']
            );

            func_array2insert('categories_lng', $int_cat_data, true);
        }

        // Icon processing

        if (func_check_image_posted($file_upload_data, 'C')) {
            func_save_image($file_upload_data, 'C', $cat);
        }

        // Rebuild category icons cache

        func_image_cache_build('C', func_query_first_cell("SELECT imageid FROM $sql_tbl[images_C] WHERE id='$cat'"));

        // Rebuild node indexes
        if (
            in_array($mode, array('add', 'update')) 
            && $old_pos != $data['order_by']
        ) {
            func_cat_tree_rebuild();
        }

        // Update categories data cache for Fancy categories module
        if (!empty($active_modules['Flyout_Menus']) && func_fc_use_cache()) {
            func_fc_build_categories(1);
        }

        // Update subcategories and products count for selected category and parent categories
        $path = func_get_category_path($cat);

        if (!empty($path)) {
            func_recalc_subcat_count($path);
        }

    } elseif ($mode == 'move' && !empty($cat)) {

        // Move category to another location
        $old_path = func_get_category_path($cat);

        // Get old category path
        db_query("UPDATE $sql_tbl[categories] SET parentid='$cat_location' WHERE categoryid='$cat'");

        // Rebuild node indexes
        func_cat_tree_rebuild();

        // Update categories data cache for Fancy categories module
        if (!empty($active_modules['Flyout_Menus']) && func_fc_use_cache()) {
            func_fc_build_categories(1);
        }

        // Update subcategories and products count for selected category and parent categories
        $path = func_get_category_path($cat);

        if (!empty($path) && !empty($old_path)) {
            $path = array_merge($path, $old_path);
            func_recalc_subcat_count(array_unique($path));
        }

        $top_message = array(
            'content' => func_get_langvar_by_name('msg_adm_category_move'),
            'type' => 'I'
        );

    } elseif ($mode == 'clean_urls_history') {

        if (empty($clean_urls_history) || !is_array($clean_urls_history)) {
            $top_message['content'] = func_get_langvar_by_name('err_clean_urls_history_empty');
            $top_message['type'] = 'E';

            func_header_location("category_modify.php?cat=$cat");
        }

        if (func_clean_url_history_delete(array_keys($clean_urls_history))) {
            $top_message['content'] = func_get_langvar_by_name('txt_clean_urls_history_deleted');
            $top_message['type'] = 'I';
        } else {
            $top_message['content'] = func_get_langvar_by_name('err_clean_urls_history_delete');
            $top_message['type'] = 'E';
        }

    }

    func_data_cache_clear('get_categories_tree');
    func_data_cache_clear('get_offers_categoryid');

    func_header_location("category_modify.php?cat=".$cat);

} // /$REQUEST_METHOD == 'POST'

if ($mode == 'del_lang') {

    // Delete multilingual dscription

    db_query("DELETE FROM $sql_tbl[categories_lng] WHERE categoryid = '$cat' AND code = '$shop_language'");

    if (!empty($active_modules['Flyout_Menus'])) {
        func_fc_build_categories(1, false, array($shop_language));
    }

    $top_message = array(
        'content' => func_get_langvar_by_name('msg_adm_category_int_del'),
        'type' => 'I'
    );
    func_header_location("category_modify.php?section=lng&cat=".$cat);
}

if ($REQUEST_METHOD == 'GET' && $mode == 'delete_icon' && !empty($cat)) {
/**
 * Delete icon
 */
    func_delete_image($cat, 'C');
    $top_message = array(
        'content' => func_get_langvar_by_name('msg_adm_category_icon_del'),
        'type' => 'I'
    );
    func_header_location("category_modify.php?cat=$cat");
}

/**
 * Assign page location
 */
$location[] = array(func_get_langvar_by_name('lbl_categories_management'), 'categories.php');

if ($mode == 'add') {

    $location[] = array(func_get_langvar_by_name('lbl_add_category'), "category_modify.php?mode=add&cat=$cat");

} else {

    $location[] = array(func_get_langvar_by_name('lbl_modify_category'), "category_modify.php?cat=$cat");

    if ($section == 'lng') {

        $location[] = array(func_get_langvar_by_name('txt_international_descriptions'), "category_modify.php?section=lng&cat=$cat");
        $dialog_tools_data['left'][] = array('link' => "category_modify.php?cat=".$cat, 'title' => func_get_langvar_by_name('lbl_modify_category'));

    } else {

        $dialog_tools_data['left'][] = array('link' => "category_modify.php?section=lng&cat=".$cat, 'title' => func_get_langvar_by_name('txt_international_descriptions'));

    }

}

if (
    $cat > 0
    && $mode != 'add'
    && !($current_category = func_get_category_data($cat))
) {

        $top_message = array(
            'content' => func_get_langvar_by_name('msg_category_not_exist'),
            'type'    => 'E',
        );

        func_header_location('categories.php');
}

require './location_adjust.php';

if ($mode !== 'add') {
    $all_categories = func_data_cache_get("get_categories_tree", array(0, false, $shop_language, $user_account['membershipid']));
}

if (
    $mode != 'add'
    && !empty($current_category)
    && !empty($all_categories)
) {
    /**
     * Correct the all_categories array: 'moving_enabled' field
     */
    foreach ($all_categories as $k=>$v) {
        if (
            (
                $current_category['lpos'] < $v['lpos']
                && $current_category['rpos'] < $v['rpos']
            ) || (
                $current_category['lpos'] > $v['lpos']
                && $current_category['rpos'] > $v['rpos']
            )
        ) {
            $all_categories[$k]['moving_enabled'] = 1;
        }
    }

    $smarty->assign('allcategories', $all_categories);

    $dialog_tools_data['left'][] = array(
        'link'  => $xcart_web_dir.DIR_ADMIN . '/category_products.php?cat=' . $cat,
        'title' => func_get_langvar_by_name('lbl_category_products') 
            . ' (' . intval($current_category['top_product_count']) . ')'
    );
}

/**
 * Prepare multi languages
 */
if ($section == 'lng') {

    $category_lng = func_query_first("SELECT $sql_tbl[categories_lng].* FROM $sql_tbl[categories_lng] WHERE $sql_tbl[categories_lng].categoryid='$cat' AND $sql_tbl[categories_lng].code = '$shop_language'");

    $smarty->assign('category_lng', $category_lng);

} else {

    $anchors = array(
        'modify_category' => 'lbl_modify_category',
    );

    if (!empty($current_category['clean_urls_history']))
        $anchors['clean_url_history'] = "lbl_clean_url_history";

    foreach ($anchors as $anchor=>$anchor_label)
        $dialog_tools_data['left'][] = array('link' => "#".$anchor, 'title' => func_get_langvar_by_name($anchor_label));

    $smarty->assign('dialog_tools_data', $dialog_tools_data);
}

/**
 * Check if image selected is not expired
 */
if ($file_upload_data['counter'] == 1) {
    $file_upload_data['counter']++;

    $smarty->assign('file_upload_data', $file_upload_data);
}
else {
    if ($file_upload_data['source'] == 'L')
        @unlink($file_upload_data['file_path']);
    x_session_unregister('file_upload_data');
}

if (!in_array($mode, array('add', 'update')))
    $mode = 'update';

if (!empty($category_modified_data)) {
    $current_category = func_array_merge($current_category, $category_modified_data);
    $category_modified_data = array();
}

$smarty->assign('query_string', urlencode($QUERY_STRING));
$smarty->assign('rand', rand());
$smarty->assign('cat', $cat);
$smarty->assign('mode', $mode);
$smarty->assign('section', $section);
$smarty->assign('main', 'category_modify');
$smarty->assign('current_category', $current_category);

$smarty->assign('memberships', func_get_memberships('C'));

$smarty->assign('image', func_image_properties('C', $cat));

x_session_save();

// Assign the current location line
$smarty->assign('location', $location);
$smarty->assign('dialog_tools_data', $dialog_tools_data);

if (
    file_exists($xcart_dir.'/modules/gold_display.php')
    && is_readable($xcart_dir.'/modules/gold_display.php')
) {
    include $xcart_dir.'/modules/gold_display.php';
}
func_display('admin/home.tpl',$smarty);

?>
