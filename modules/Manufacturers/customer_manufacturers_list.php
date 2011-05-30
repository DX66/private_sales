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
 * Manufacturers list
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: customer_manufacturers_list.php,v 1.38.2.2 2011/01/25 09:43:13 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

if (!empty($active_modules['Manufacturers'])) {
    include $xcart_dir.'/modules/Manufacturers/customer_manufacturers.php';
}

$location[] = array(func_get_langvar_by_name('lbl_manufacturers'), '');

if ($manufacturerid) {

    // Get products data for current category and store it into $products array

    $old_search_data = $search_data['products'];
    $old_mode = $mode;

    $search_data['products'] = array();
    $search_data['products']['manufacturers'] = array($manufacturerid);
    $search_data['products']['forsale'] = 'Y';

    if (!isset($sort)) {

        $sort = $config["Appearance"]["products_order"];

    }

    if (!isset($sort_direction)) {

        $search_data['products']['sort_direction'] = 0;

    } else {

        $search_data['products']['sort_direction'] = $sort_direction;

    }

    $mode = 'search';

    include $xcart_dir . '/include/search.php';

    $smarty->assign('sort',$search_data['products']['sort_field']);
    $smarty->assign('sort_direction',$search_data['products']['sort_direction']);

    $search_data['products'] = $old_search_data;

    $mode = $old_mode;

    if (!empty($active_modules['Subscriptions']))
        include $xcart_dir.'/modules/Subscriptions/subscription.php';

    $smarty->assign('products',$products);

    $manufacturer = func_query_first("SELECT $sql_tbl[manufacturers].*, $sql_tbl[images_M].image_path, $sql_tbl[images_M].image_x, $sql_tbl[images_M].image_y, IFNULL($sql_tbl[manufacturers_lng].manufacturer, $sql_tbl[manufacturers].manufacturer) as manufacturer, IFNULL($sql_tbl[manufacturers_lng].descr, $sql_tbl[manufacturers].descr) as descr FROM $sql_tbl[manufacturers] LEFT JOIN $sql_tbl[manufacturers_lng] ON $sql_tbl[manufacturers].manufacturerid = $sql_tbl[manufacturers_lng].manufacturerid AND $sql_tbl[manufacturers_lng].code = '$shop_language' LEFT JOIN $sql_tbl[images_M] ON $sql_tbl[manufacturers].manufacturerid = $sql_tbl[images_M].id WHERE $sql_tbl[manufacturers].manufacturerid = '$manufacturerid' AND $sql_tbl[manufacturers].avail = 'Y' ORDER BY $sql_tbl[manufacturers].orderby");

    if (empty($manufacturer)) {

        $manufacturer_is_exists = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[manufacturers] WHERE manufacturerid = '$manufacturerid'") > 0;

        if ($manufacturer_is_exists) {

            $top_message = array(
                'content' => func_get_langvar_by_name('txt_manufacturer_not_found'),
                'type' => 'E'
            );

            func_header_location('manufacturers.php');

        } else {

            func_page_not_found();

        }

    }

    $manufacturer['is_image'] = !is_null($manufacturer['image_path']);
    $manufacturer['image_url'] = func_get_image_url($manufacturerid, "M", $manufacturer['image_path']);

    $smarty->assign('manufacturer', $manufacturer);

    $smarty->assign('main','manufacturer_products');

    $location[count($location)-1][1] = 'manufacturers.php';
    $location[] = array($manufacturer['manufacturer'], "");

    $smarty->assign('meta_page_type', 'M');
    $smarty->assign('meta_page_id', $manufacturerid);

} else {

    $total_items = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[manufacturers] WHERE avail = 'Y'");

    if ($total_items > 0) {

        $objects_per_page = $config['Manufacturers']['manufacturers_per_page'];

        include $xcart_dir.'/include/navigation.php';

        $manufacturers = func_query("SELECT $sql_tbl[manufacturers].*, IFNULL($sql_tbl[manufacturers_lng].manufacturer, $sql_tbl[manufacturers].manufacturer) as manufacturer, IFNULL($sql_tbl[manufacturers_lng].descr, $sql_tbl[manufacturers].descr) as descr FROM $sql_tbl[manufacturers] LEFT JOIN $sql_tbl[manufacturers_lng] ON $sql_tbl[manufacturers].manufacturerid = $sql_tbl[manufacturers_lng].manufacturerid AND $sql_tbl[manufacturers_lng].code = '$shop_language' WHERE avail = 'Y' ORDER BY $sql_tbl[manufacturers].orderby, manufacturer".($objects_per_page > 0 ? " LIMIT $first_page, $objects_per_page" : ""));

        $smarty->assign('manufacturers', $manufacturers);

    }

    $smarty->assign('main','manufacturers_list');
}

$smarty->assign('navigation_script',"manufacturers.php?manufacturerid=".$manufacturerid."&sort=".$sort."&sort_direction=".$sort_direction);

$smarty->assign('manufacturerid', $manufacturerid);
?>
