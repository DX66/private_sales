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
 * Popup products and categories selection library
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: popup_product_category.php,v 1.14.2.2 2011/01/10 13:11:50 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

$id_attr     = ($popup_type == 'C') ? 'categoryid' : 'productid';
$name_attr   = ($popup_type == 'C') ? 'category' : 'product';

$tbl_name    = ($popup_type == 'C') ? 'category_bookmarks' : 'product_bookmarks';
$join_tbl    = ($popup_type == 'C') ? 'categories' : 'products';

$id_value    = $$id_attr;

if ($mode == 'bookmark') {

    $bookmarks_count = func_query_first_cell("SELECT COUNT($id_attr) FROM $sql_tbl[$tbl_name] WHERE $id_attr = '$id_value' AND userid = '$logged_userid'");

    if ($bookmarks_count == 0) {

        $query_data = array(
            $id_attr    => $id_value,
            'userid'    => $logged_userid,
            'add_date'    => XC_TIME,
        );

        func_array2insert($tbl_name, $query_data);
    }
} elseif ($mode == 'delete_bookmark') {

    db_query("DELETE FROM $sql_tbl[$tbl_name] WHERE $id_attr = '$id_value' AND userid='$logged_userid'");
}

$bookmarks = func_query("SELECT $sql_tbl[$tbl_name].$id_attr, $sql_tbl[$join_tbl].$name_attr FROM $sql_tbl[$tbl_name], $sql_tbl[$join_tbl] WHERE $sql_tbl[$tbl_name].$id_attr = $sql_tbl[$join_tbl].$id_attr AND $sql_tbl[$tbl_name].userid = '$logged_userid' ORDER BY $sql_tbl[$tbl_name].add_date DESC");

if ($popup_type == 'C' && is_array($bookmarks)) {

    foreach ($bookmarks as $k => $bookmark) {
        if (isset($all_categories[$bookmark[$id_attr]])) {
            $bookmarks[$k][$name_attr] = $all_categories[$bookmark[$id_attr]]['category_path'];
        }
    }
}

$smarty->assign ('bookmarks', $bookmarks);

x_load('category');
$smarty->assign('allcategories', func_data_cache_get("get_categories_tree", array(0, true, $shop_language, $user_account['membershipid'])));

?>
