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
 * Categories import library
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import_categories.php,v 1.54.2.4 2011/01/10 13:11:49 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('category', 'import');

/******************************************************************************
Used cache format:
Categories:
    data_type: C
    key:       <Category full path>
    value:     [<Category ID> | RESERVED]
Categories:
    data_type: CI
    key:       <Category ID>
    value:     [<Category full path> | RESERVED]
Categories (translation categoryid from file and from DB):
    data_type: CT
    key:       <Category ID>
    value:     [<Category ID> | RESERVED]
Memberships:
    data_type: M
    key:       <Membership name>
    value:     <Membership ID>
Images identificaters:
    data_type: I
    key:       <Image type>_<Image owner id>
    value:     <Image ID>
Categories for Counting the number of subcategories and products in categories:
    data_type: CR
    key:       <Category ID>
    value:     <Category ID>

Note: RESERVED is used if ID is unknown
******************************************************************************/

if (!defined('IMPORT_CATEGORIES')) {
/**
 * Make default definitions (only on first inclusion!)
 */
    define('IMPORT_CATEGORIES', 1);
    $import_specification['CATEGORIES'] = array(
        'script'        => '/include/import_categories.php',
        'tpls'          => array(
            'main/import_option_default_category.tpl',
            'main/import_option_category_path_sep.tpl',
            'main/import_option_images_directory.tpl'),
        'export_tpls'   => array(
            'main/export_option_export_images.tpl',
            'main/export_option_category_path_sep.tpl'),
        'permissions'   => 'A', // Only admin can import categories
        'need_provider' => 0,
        'finalize'      => true,
        'export_sql'    => "SELECT categoryid FROM $sql_tbl[categories] ORDER BY lpos ASC",
        'orderby'       => 25,
        'depending'     => array('C','CI','CT'),
        'columns'       => array(
            'categoryid'    => array(
                'is_key'    => true,
                'type'      => 'N',
                'required'  => false,
                'default'   => 0),
            'category'      => array(
                'is_key'    => true,
                'required'  => true),
            'clean_url'        => array(
                'type'      => 'U'
            ),
            'descr'         => array(
                'eol_safe'  => true),
            'title_tag'     => array(),
            'meta_keywords' => array(),
            'meta_description' => array(),
            'override_child_meta' => array(
                'type'      => 'B',
                'default'   => 'N'),
            'avail'         => array(
                'type'      => 'B',
                'default'   => 'Y'),
            'orderby'       => array(
                'type'      => 'N',
                'default'   => 0),
            'views_stats'   => array(
                'type'      => 'N'),
            'product_count' => array(
                'type'      => 'N'),
            'membershipid'  => array(
                'array'     => true,
                'type'      => 'N'),
            'membership'    => array(
                'array'     => true),
            'icon'          => array(
                'type'      => 'I',
                'itype'     => 'C')
        )
    );

} elseif ($import_step == 'process_row') {
/**
 * PROCESS ROW from import file
 */

    if (isset($values['categoryid']))
        $values['categoryid'] = abs(intval($values['categoryid']));

    $tmp = func_import_get_cache('C', $values['category']);
    if (is_null($tmp)) {
        func_import_save_cache('C', $values['category']);
    }

    if (!empty($values['categoryid'])) {
        $tmp = func_import_get_cache('CI', $values['categoryid']);
        if (is_null($tmp)) {
            func_import_save_cache('CI', $values['categoryid']);
        }
        $tmp = func_import_get_cache('CT', $values['categoryid']);
        if (is_null($tmp)) {
            func_import_save_cache('CT', $values['categoryid']);
        }
    }

    // Check membership
    $values['membershipid'] = array();
    if (!empty($values['membership'])) {
        if (!is_array($values['membership']))
            $values['membership'] = array($values['membership']);
        foreach ($values['membership'] as $v) {
            if (empty($v))
                continue;
            $_membershipid = func_import_get_cache('M', $v);
            if (empty($_membershipid)) {
                $_membershipid = func_detect_membership($v, 'C');
                if ($_membershipid == 0) {
                    // Membership is specified but does not exist
                    func_import_module_error('msg_err_import_log_message_5', array('membership' => $v));
                } else {
                    func_import_save_cache('M', $v, $_membershipid);
                }
            }
            if (!empty($_membershipid))
                $values['membershipid'][] = $_membershipid;
        }
        unset($values['membership']);
    }

    func_import_check_clean_url_uniqueness();

    $data_row[] = $values;

} elseif ($import_step == 'finalize') {
/**
 * FINALIZE rows processing: update database
 */

    // Drop old categories and all related info
    if ($import_file['drop']['categories'] == 'Y') {
        func_import_save_image('C');
        db_query("DELETE FROM $sql_tbl[categories]");
        db_query("DELETE FROM $sql_tbl[categories_subcount]");
        db_query("DELETE FROM $sql_tbl[category_memberships]");
        db_query("DELETE FROM $sql_tbl[products_categories]");
        db_query("DELETE FROM $sql_tbl[categories_lng]");
        db_query("DELETE FROM $sql_tbl[images_C]");
        db_query("DELETE FROM $sql_tbl[clean_urls] WHERE resource_type = 'C'");
        db_query("DELETE FROM $sql_tbl[clean_urls_history] WHERE resource_type = 'C'");

        $import_file['drop']['categories'] = '';
    }

    // Put parent category row above its subcategories M:75990

    if (isset($data_row[0]['categoryid']) && isset($data_row[0]['category'])) {
        if (!function_exists('func_categories_sort')) {
            function func_categories_sort($a, $b) {
                return strcmp($a['category'], $b['category']);
            }
        }
        usort($data_row, 'func_categories_sort');
    }

    // Import category data...
    $category_sep_local = empty($import_file['category_sep']) ? '/' : $import_file['category_sep'];
    foreach ($data_row as $data_row_idx => $category) {

        $cats = explode($category_sep_local, $category['category']);
        if (empty($cats) || !is_array($cats))
            continue;

        // Import category chain
        $_parentid = 0;
        $_path = array();
        foreach ($cats as $kc => $c) {
            if (empty($c))
                continue;

            // Get old categoryid
            $_cid = func_query_first_cell("SELECT categoryid FROM $sql_tbl[categories] WHERE category = '".addslashes($c)."' AND parentid = '$_parentid'");

            $data = array(
                'category' => addslashes($c),
                'parentid' => $_parentid,
            );
            if ($kc == count($cats)-1) {
                // Check categoryid
                if (empty($_cid) && !empty($category['categoryid'])) {
                    if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[categories] WHERE categoryid = '".$category['categoryid']."'") > 0) {
                        $_cid = $category['categoryid'];

                    } else {
                        $data['categoryid'] = $category['categoryid'];
                    }
                }

                if (isset($category['descr']))
                    $data['description'] = addslashes($category['descr']);
                if (isset($category['meta_keywords']))
                    $data['meta_keywords'] = addslashes($category['meta_keywords']);
                if (isset($category['meta_description']))
                    $data['meta_description'] = addslashes($category['meta_description']);
                if (isset($category['override_child_meta']))
                    $data['override_child_meta'] = $category['override_child_meta'];
                if (isset($category['avail']))
                    $data['avail'] = $category['avail'];
                if (isset($category['orderby']))
                    $data['order_by'] = $category['orderby'];
                if (isset($category['views_stats']))
                    $data['views_stats'] = $category['views_stats'];
                if (isset($category['product_count']))
                    $data['product_count'] = $category['product_count'];
            }

            $int_cat_data = array(
                'category'      => $data['category'],
                'description'   => $data['description']
            );
            // Import category data
            if (!empty($_cid)) {
                if ($kc == count($cats) -1) {
                    func_array2update('categories', $data, "categoryid = '$_cid'");
                    func_array2update('categories_lng', $int_cat_data, "categoryid = '$_cid' AND code='$config[default_admin_language]'");
                    $result['categories']['updated']++;
                }
            } else {

                $_cid = func_array2insert('categories', $data);
                if (!empty($_cid)) {
                    $result['categories']['added']++;
                    $_path[$_cid] = $c;

                    $int_cat_data['categoryid'] = $_cid;
                    $int_cat_data['code'] = $config['default_admin_language'];
                    func_array2insert('categories_lng', $int_cat_data, true);

                }
            }
            if (empty($_cid))
                continue;

            $_parentid = $_cid;
            $_path[$_cid] = $c;
            func_import_save_cache('C', implode($category_sep_local, $_path), $_cid);
            func_import_save_cache('CI', $_cid, implode($category_sep_local, $_path));
            if ($kc == count($cats)-1 && $category['categoryid']) {
                func_import_save_cache('CT', $category['categoryid'], $_cid);
            }

            func_import_save_cache('CR', $_cid, $_cid);
        }

        // Import category memberhips
        if (empty($_cid))
            continue;

        // Import category icon
        if (!empty($category['icon'])) {
            func_import_save_image_data('C', $_cid, $category['icon']);
        }

        // Import category memberhips
        db_query("DELETE FROM $sql_tbl[category_memberships] WHERE categoryid = '$_cid'");
        if (!empty($category['membershipid'])) {
            foreach ($category['membershipid'] as $v) {
                func_array2insert('category_memberships', array('categoryid' => $_cid, 'membershipid' => $v));
            }
        }

        if (isset($category['clean_url']) && !zerolen($category['clean_url'])) {
            // Check provided clean URL for uniqueness, format was checked on 'process_row' step.
            if (func_clean_url_has_url($category['clean_url'], array('resource_type' => 'C', 'resource_id' => $_cid))) {
                func_import_module_error('err_clean_url_existing_db_record', null, func_import_get_real_idx($data_row, $data_row_idx));

            } else {
                if (func_clean_url_resource_has_record('C', $_cid)) {
                    // Update clean URL preserving old value in clean URLs history.
                    func_clean_url_update($category['clean_url'], 'C', $_cid, true);
                } else {
                    // Store new clean URL value.
                    func_clean_url_add($category['clean_url'], 'C', $_cid);
                }
            }
        } else {
            // Request clean URL autogeneration if it was not provided in a CSV file and a category does not have one already assigned.
            if (!func_clean_url_resource_has_record('C', $_cid)) {
                func_import_save_cache('CWU', $_cid, $cats[count($cats) - 1]);
            }
        }

        func_flush(". ");
    }

} elseif ($import_step == 'complete') {

    // Post-import steps.

    // Rebuild node indexes
    func_cat_tree_rebuild();

    // Recalculate products/subcategories counts.
    if (func_import_cache_has_records('CR')) {
        $message = func_get_langvar_by_name('txt_subcategories_and_products_counting_',NULL,false,true);
        func_import_add_to_log($message);
        func_flush("<br />\n".$message."<br />\n");

        $cnt = 0;
        while (list($cid, $tmp) = func_import_read_cache('CR')) {
            func_recalc_subcat_count($cid);

            func_flush(". ");
            if (++$cnt % 50 == 0) {
                func_flush("<br />\n");
            }
        }

        if (!empty($active_modules['Flyout_Menus']) && func_fc_use_cache()) {
            func_flush("<br />\n");
            func_fc_build_categories(1);
        }

        func_import_erase_cache('CR');
    }

    // Autogenerate clean URLs for imported categories without explicitly specified clean URLs values.
    if (func_import_cache_has_records('CWU')) {
        $message = func_get_langvar_by_name('txt_generating_clean_urls_for', array('resource_name' => func_get_langvar_by_name("lbl_categories", false, false, true)), false, true);
        func_import_add_to_log($message);
        func_flush("<br />\n".$message."<br />\n");

        $cnt = 0;
        while (list($cid, $category) = func_import_read_cache('CWU')) {

            if (!func_clean_url_resource_has_record('C', $cid)) {
                $autogenerated_url = func_clean_url_autogenerate('C', $cid, array('category' => $category));
                if ($autogenerated_url) {
                    func_clean_url_add($autogenerated_url, 'C', $cid);
                }
            }

            func_flush(". ");
            if (++$cnt % 50 == 0) {
                func_flush("<br />\n");
            }
        }

        func_import_erase_cache('CWU');
    }

    func_data_cache_clear('get_categories_tree');
    func_data_cache_clear('get_offers_categoryid');
} elseif ($import_step == 'export') {

    // Export data
    while ($id = func_export_get_row($data)) {
        if (empty($id))
            continue;

        // Get data
        $row = func_query_first("SELECT * FROM $sql_tbl[categories] WHERE categoryid = '$id'");
        if (empty($row))
            continue;

        $row = func_export_rename_cell($row, array('order_by' => 'orderby', 'description' => 'descr'));
        $c_row = func_export_get_category($id);
        if (empty($c_row))
            continue;
        $row = func_array_merge($row, $c_row);

        // Export memberships
        $mems = func_query("SELECT $sql_tbl[memberships].membershipid, $sql_tbl[memberships].membership FROM $sql_tbl[memberships], $sql_tbl[category_memberships] WHERE $sql_tbl[memberships].membershipid = $sql_tbl[category_memberships].membershipid AND $sql_tbl[category_memberships].categoryid = '$id'");
        if (!empty($mems)) {
            foreach ($mems as $v) {
                $row['membershipid'][] = $v['membershipid'];
                $row['membership'][] = $v['membership'];
            }
        }

        // Export icons
        $row['icon'] = $id;

        // Export Clean URL
        $clean_url = func_query_first_cell("SELECT clean_url FROM $sql_tbl[clean_urls] WHERE resource_type = 'C' AND resource_id = '".$id."'");
        if (!empty($clean_url)) {
            $row['clean_url'] = $clean_url;
        }

        // Export row
        if (!func_export_write_row($row))
            break;

    }
}

?>
