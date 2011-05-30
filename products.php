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
 * Navigation code
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Customer interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: products.php,v 1.33.2.3 2011/01/25 09:43:11 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: home.php"); die("Access denied"); }

if ($config['General']['show_outofstock_products'] != 'Y') {

    $join      = '';
    $group_by  = '';
    $condition = '';

    if ($config['General']['unlimited_products'] != 'Y') {

        if (!empty($active_modules['Product_Options'])) {

            $join      = "LEFT JOIN $sql_tbl[variants] ON $sql_tbl[variants].productid = $sql_tbl[products].productid";
            $group_by  = "GROUP BY $sql_tbl[products].productid";
            $condition = " AND IFNULL($sql_tbl[variants].avail, $sql_tbl[products].avail) > '0' ";

        } else {

            $condition = " AND $sql_tbl[products].avail > '0' ";

        }

    }

    $current_category['product_count'] = db_num_rows(db_query("SELECT $sql_tbl[products].productid FROM $sql_tbl[products] INNER JOIN $sql_tbl[products_categories] ON $sql_tbl[products].productid=$sql_tbl[products_categories].productid AND $sql_tbl[products].forsale='Y' AND $sql_tbl[products_categories].categoryid='$cat' $join WHERE 1 $condition $group_by"));

    if (is_array($subcategories)) {

        foreach ($subcategories as $k => $v) {

            $subcategories[$k]['product_count'] = db_num_rows(db_query("SELECT $sql_tbl[products].productid FROM $sql_tbl[products] INNER JOIN $sql_tbl[products_categories] ON $sql_tbl[products].productid=$sql_tbl[products_categories].productid AND $sql_tbl[products].forsale='Y' AND $sql_tbl[products_categories].categoryid='$v[categoryid]' $join WHERE 1 $condition $group_by"));

        }

        $smarty->assign('subcategories', $subcategories);

    }

}

if (!empty($active_modules['Advanced_Statistics']) && !defined('IS_ROBOT')) {

    include $xcart_dir . '/modules/Advanced_Statistics/cat_viewed.php';

}

/**
 * Get products data for current category and store it into $products array
 */

$old_search_data = isset($search_data['products']) ? $search_data['products'] : '';

$old_mode = isset($mode) ? $mode : '';

$search_data['products'] = array(
    'categoryid'              => $cat,
    'search_in_subcategories' => '',
    'category_main'           => 'Y',
    'category_extra'          => 'Y',
    'forsale'                 => 'Y',
);

if (!isset($sort)) {

    $sort = $config['Appearance']['products_order'];

}

if (!isset($sort_direction)) {

    $sort_direction = 0;

}

$mode = 'search';

include $xcart_dir . '/include/search.php';

$search_data['products'] = $old_search_data;

$mode = $old_mode;

if (!empty($active_modules['Subscriptions'])) {

    include $xcart_dir . '/modules/Subscriptions/subscription.php';

}

$smarty->assign('cat_products',      isset($products) ? $products : array());
$smarty->assign('navigation_script', "home.php?cat=$cat&sort=$sort&sort_direction=$sort_direction");
?>
