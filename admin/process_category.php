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
 * Process modified categories data
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: process_category.php,v 1.57.2.2 2011/01/10 13:11:46 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './auth.php';
require $xcart_dir.'/include/security.php';

x_load('category');

$current_category = func_get_category_data($cat);

func_set_time_limit(86400);

if ($REQUEST_METHOD == 'POST') {

    if ($mode == 'apply') {

        // Update categories list

        if ($posted_data) {
            foreach ($posted_data as $k => $v) {
                $query_data = array(
                    'order_by' => intval($v['order_by']),
                    'avail' => ($v['avail'] == 'Y' ? 'Y' : 'N')
                );
                func_array2update('categories', $query_data, "categoryid='".intval($k)."'");
            }

            // Update categories data cache
            if (!empty($active_modules['Flyout_Menus']) && func_fc_use_cache())
                func_fc_build_categories(1);
        }

        // Rebuild node indexes
        func_cat_tree_rebuild();

        // Update subcategories counters
        if (!empty($cat_org)) {
            $path = func_get_category_path($cat_org);
            if (!empty($path)) {
                func_recalc_subcat_count($path);
            }
        }

        $top_message['content'] = func_get_langvar_by_name('msg_adm_categories_upd');
        $top_message['type'] = 'I';

        func_header_location("categories.php?cat=$cat_org");

    }
    elseif ($mode == 'update') {

        // Go to modify category

        func_header_location("category_modify.php?cat=$cat");

    }
    elseif ($mode == 'delete') {

        // Delete category

        if ($confirmed == 'Y') {

            // Delete category from database
            // Delete all subcategories and associated products

            require $xcart_dir.'/include/safe_mode.php';

            $parent_categoryid = func_delete_category($cat, 1);

            if (!empty($active_modules['Flyout_Menus']) && func_fc_use_cache()) {
                func_fc_build_categories(1);
            }

            // Delete Clean URLs data.
            db_query("DELETE FROM $sql_tbl[clean_urls] WHERE resource_type = 'C' AND resource_id = '$cat'");
            db_query("DELETE FROM $sql_tbl[clean_urls_history] WHERE resource_type = 'C' AND resource_id = '$cat'");

            $top_message['content'] = func_get_langvar_by_name('msg_adm_category_del');
            $top_message['type'] = 'I';

            func_header_location("categories.php?cat=$parent_categoryid");
        }
        else {

            // Go to prepare delete confirmation page

            func_header_location("process_category.php?cat=$cat&mode=delete");
        }
    }
}

if ($mode == 'add') {

    // Add new category

    func_header_location("category_modify.php?$QUERY_STRING");
}

if ($mode == 'delete' && $confirmed != 'Y') {

    // Prepare the delete confirmation page

    $location[] = array(func_get_langvar_by_name('lbl_categories_management'), 'categories.php');
    $location[] = array(func_get_langvar_by_name('lbl_delete_category'), '');

    x_load('category');
    $current_category = func_get_category_data($cat);
    $subcats = func_query("SELECT categoryid, category FROM $sql_tbl[categories] WHERE lpos BETWEEN " . $current_category['lpos'] . " AND " .  $current_category['rpos']);

    if (!is_array($subcats)) {
        $subcats = array();
    }

    if (is_array($subcats)) {
        foreach ($subcats as $k=>$v) {
            $subcats[$k]['products'] = func_query("SELECT $sql_tbl[products].productid, $sql_tbl[products].productcode, $sql_tbl[products].product FROM $sql_tbl[products_categories] INNER JOIN $sql_tbl[products] ON $sql_tbl[products_categories].categoryid='$v[categoryid]' AND $sql_tbl[products_categories].productid=$sql_tbl[products].productid AND $sql_tbl[products_categories].main='Y'");

            $subcats[$k]['products_count'] = (is_array($subcats[$k]['products']) ? count($subcats[$k]['products']) : 0);
        }
    }

    $smarty->assign('subcats', $subcats);
    $smarty->assign('main','category_delete_confirmation');

    // Assign the current location line
    $smarty->assign('location', $location);

    if (
        file_exists($xcart_dir.'/modules/gold_display.php')
        && is_readable($xcart_dir.'/modules/gold_display.php')
    ) {
        include $xcart_dir.'/modules/gold_display.php';
    }
    func_display('admin/home.tpl',$smarty);
}

?>
