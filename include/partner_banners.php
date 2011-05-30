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
 * Partner banners library
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: partner_banners.php,v 1.25.2.4 2011/04/12 12:22:03 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load('image');

x_session_register('file_upload_data');
x_session_register('store_banner_data', array());

$is_partner_area = constant('AREA_TYPE') == 'B';

$location[] = array(func_get_langvar_by_name('lbl_banners'), "");

/**
 * Define data for the navigation within section
 */
$dialog_tools_data['left'][] = array('link' => "partner_banners.php", 'title' => func_get_langvar_by_name('lbl_banners_list'));

if (!$is_partner_area) {

    $dialog_tools_data['left'][] = array(
        'link'  => 'partner_banners.php?banner_type=T',
        'title' => func_get_langvar_by_name('lbl_add_text_link')
    );

    $dialog_tools_data['left'][] = array(
        'link'  => 'partner_banners.php?banner_type=G',
        'title' => func_get_langvar_by_name('lbl_add_graphic_banner')
    );
}

$dialog_tools_data['left'][] = array(
    'link'  => 'partner_banners.php?banner_type=M',
    'title' => func_get_langvar_by_name('lbl_add_media_rich_banner')
);
$dialog_tools_data['left'][] = array(
    'link'  => 'partner_banners.php?banner_type=P',
    'title' => func_get_langvar_by_name('lbl_add_product_banner')
);
$dialog_tools_data['left'][] = array(
    'link'  => 'partner_banners.php?banner_type=C',
    'title' => func_get_langvar_by_name('lbl_add_category_banner')
);

if (!empty($active_modules['Manufacturers'])) {

    $dialog_tools_data['left'][] = array(
        'link'  => 'partner_banners.php?banner_type=F',
        'title' => func_get_langvar_by_name('lbl_add_manufacturer_banner')
    );
}

$dialog_tools_data['right'][] = array(
    'link'  => 'banner_info.php',
    'title' => func_get_langvar_by_name('lbl_banners_statistics')
);

// Partner cannot Add/Modify T G banners
if (
    $is_partner_area
    && $banner_type
    && !in_array($banner_type, array('P', 'C', 'F', 'M'))
) {
    func_header_location('partner_banners.php');
}

$elements_names = array(
    'P' => func_get_langvar_by_name('lbl_product'),
    'C' => func_get_langvar_by_name('lbl_category'),
    'F' => func_get_langvar_by_name('lbl_manufacturer')
);

if (
    $mode == 'upload'
    && $banner_type == 'M'
) {

    // Add media library element

    $id = func_query_first_cell("SELECT MAX(id) FROM $sql_tbl[images_L] GROUP BY id") + 1;

    if (func_check_image_posted($file_upload_data, 'L')) {
        func_save_image($file_upload_data, 'L', $id);
    }

    if ($width > 0 && $height > 0) {
        $type = func_query_first_cell("SELECT image_type FROM $sql_tbl[images_L] WHERE id = '" . $id . "'");

        if ($type == 'application/x-shockwave-flash') {
            func_array2update(
                'images_L',
                array('image_x' => $width, 'image_y' => $height),
                "id = '" . $id . "'"
            );
        }
    }

    $top_message = array(
        'content' => func_get_langvar_by_name('lbl_media_library_element_is_added')
    );

    if ($bannerid) {
        func_header_location("partner_banners.php?bannerid=" . $bannerid);
    }

    func_header_location("partner_banners.php?banner_type=" . $banner_type);

} elseif (
    $mode == 'add'
    && $add
    && $banner_type
) {

    // Add/Modify banner

    if ($banner_type == 'F' && empty($active_modules['Manufacturers'])) {
        func_header_location('partner_banners.php');
    }

    $error = $err_field = false;

    if (empty($add['banner'])) {

        $error = func_get_langvar_by_name('lbl_banner_has_no_name');
        $err_field = 'banner';

    } elseif (
        !empty($add['banner_x'])
        && (
            !is_numeric($add['banner_x'])
            || $add['banner_x'] < 1)
    ) {

        $error = func_get_langvar_by_name('lbl_banner_has_wrong_width');
        $err_field = 'banner_xy';

    } elseif (
        !empty($add['banner_y'])
        && (!is_numeric($add['banner_y'])
        || $add['banner_y'] < 1)
    ) {

        $error = func_get_langvar_by_name('lbl_banner_has_wrong_height');
        $err_field = 'banner_xy';

    } elseif (
        in_array($banner_type, array('T', 'M'))
        && empty($add['body'])
    ) {

        $error = func_get_langvar_by_name('lbl_banner_body_is_empty');
        $err_field = 'body';

    } elseif (
        in_array($banner_type, array('P', 'C', 'F'))
        && empty($add['is_image'])
        && empty($add['is_name'])
        && empty($add['is_descr'])
        && empty($add['is_add'])
    ) {

        $error = func_get_langvar_by_name(
            'lbl_banner_attributes_are_disabled',
            array(
                'element' => $elements_names[$banner_type]
            )
        );

        if ($banner_type != 'P') {

            $error = func_get_langvar_by_name(
                'lbl_banner_attributes_are_disabled',
                array(
                    'element' => $elements_names[$banner_type]
                )
            );

        } else {

            $error = func_get_langvar_by_name('lbl_banner_attributes_are_disabled_P');

        }

        $err_field = 'banner_attributes';

    }

    if ($error) {
        $store_banner_data = $add;
        $store_banner_data['bannerid'] = $bannerid;
        $top_message = array(
            'type' => 'E',
            'content' => $error
        );
        func_header_location('partner_banners.php?banner_type=' . $banner_type . '&bannerid=' . $bannerid . '&err_field=' . $err_field);
    }

    $store_banner_data = array();

    if ($current_area == 'B') {
        if ($banner_type == 'T') {
            $add['body'] = strip_tags($add['body']);

        } elseif ($banner_type == 'M') {
            $add['body'] = func_xss_free($add['body'], false, true);
        }
    }

    $data = array(
        'banner'        => $add['banner'],
        'body'          => $add['body'],
        'avail'         => $add['avail'],
        'is_image'      => $add['is_image'],
        'is_name'       => $add['is_name'],
        'is_descr'      => $add['is_descr'],
        'is_add'        => $add['is_add'],
        'banner_type'   => $banner_type,
        'open_blank'    => $add['open_blank'],
        'legend'        => $add['legend'],
        'alt'           => $add['alt'],
        'direction'     => $add['direction'],
        'banner_x'      => $add['banner_x'],
        'banner_y'      => $add['banner_y']
    );

    if ($is_partner_area) {
        $data['userid'] = $logged_userid;
    }

    $is_new = false;

    if ($bannerid) {

        func_array2update('partner_banners', $data, "bannerid = '$bannerid'");
        $top_message = array(
            'content' => func_get_langvar_by_name('lbl_banner_is_updated')
        );

    } else {

        $bannerid = func_array2insert('partner_banners', $data);
        $top_message = array(
            'content' => func_get_langvar_by_name('lbl_banner_is_created')
        );
        $is_new = true;

    }

    if (
        $banner_type == 'G'
        && $bannerid
        && func_check_image_posted($file_upload_data, 'B')
    ) {
        func_save_image($file_upload_data, 'B', $bannerid);
    }

    if ($banner_type == 'G' && $bannerid) {
        $has_image = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[images_B] WHERE id = '$bannerid'");
        if (!$has_image) {
            func_array2update('partner_banners', array('avail' => ''), "bannerid = '$bannerid'");
            $top_message['content'] .= '<br />'
                . func_get_langvar_by_name(
                    $is_new
                    ? 'lbl_banner_is_created_without_image'
                    : 'lbl_banner_is_updated_without_image'
                );
        }
    }
    $_get = ($is_new || $mode2 == 'choose_elem') ? '&get=1' : '';

    func_header_location('partner_banners.php?bannerid=' . $bannerid . $_get);

} elseif ($mode == 'delete') {

    // Delete banner or media library element
    if ($bannerid) {
        db_query ("DELETE FROM $sql_tbl[partner_banners] WHERE bannerid = '$bannerid'");
        func_delete_image($bannerid, 'B');

        $top_message = array(
            'content' => func_get_langvar_by_name('lbl_banner_is_deleted')
        );
        func_header_location('partner_banners.php');

    } elseif ($id) {
        func_delete_image($id, 'L');
        func_header_location('partner_element_list.php');
    }
}

if ($bannerid && empty($err_field)) {

    $banner = func_query_first("SELECT * FROM $sql_tbl[partner_banners] WHERE bannerid = '$bannerid'");
    if (!$banner) {
        func_header_location('partner_banners.php');
    }

    if ($is_partner_area) {
        if ($banner['userid'] && $banner['userid'] != $logged_userid) {
            $top_message = array(
                'type' => 'E',
                'content' => func_get_langvar_by_name('lbl_banner_access_denied')
            );
            func_header_location('partner_banners.php');
        }

        $banner['can_edit'] = $banner['userid'] == $logged_userid;

    } elseif($banner['userid'] == 0) {
        // Admin can edit own banners only
        $banner['can_edit'] = true;
    }

    $smarty->assign ('banner', $banner);
    $banner_type = $banner['banner_type'];

    $location[count($location) - 1][1] = 'partner_banners.php';
    $location[] = array($banner['banner'], 'partner_banners.php?bannerid=' . $bannerid);

} elseif ($store_banner_data) {

    $smarty->assign ('err_field', $err_field);
    $smarty->assign ('banner', $store_banner_data);
}

if ($banner && $get) {

    if ($banner['can_edit']) {
        $dialog_tools_data['right'][] = array(
            'link' => 'partner_banners.php?bannerid=' . $bannerid,
            'title' => func_get_langvar_by_name('lbl_modify')
        );
    }

    if ($banner['banner_type'] == 'P') {

        // Search producs

        if ($productid) {

            x_load('product');
            $product = func_select_product($productid, $user_account['membershipid']);
            if (!$product)
                func_header_location('partner_banners.php?bannerid=' . $banner['bannerid'] . '&get=1');

            $smarty->assign('productid', $productid);
            $smarty->assign('product', $product);

            $location[] = array(
                func_get_langvar_by_name('lbl_search'),
                'partner_banners.php?bannerid=' . $banner['bannerid'] . '&get=1'
            );
            $location[] = array(
                func_get_langvar_by_name('lbl_products_list'),
                'partner_banners.php?bannerid=' . $banner['bannerid'] . '&get=1&mode=search'
            );
            $location[] = array(
                func_get_langvar_by_name('lbl_banner_html_code'),
                ''
            );

        } else {

            // Product search

            x_session_register('search_data');

            if (!isset($search_data['products'])) {

                // Set default search checkboxes
                $search_data['products'] = array(
                    'by_title' => 'Y',
                    'by_descr' => 'Y',
                    'search_in_subcategories' => 'Y'
                );
            }

            $search_data['products']['forsale'] = 'Y';

            $search_data['products']['category_main'] =
            $search_data['products']['category_extra'] = 'Y';

            $posted_data['by_shortdescr'] =
            $posted_data['by_fulldescr'] =
            $posted_data['by_descr'];

            define('GET_ALL_CATEGORIES', true);

            define('X_SEARCH_URL', 'partner_banners.php?bannerid=' . $banner['bannerid'] . '&get=1');

            include $xcart_dir . '/include/search.php';

            unset($search_data['products']['forsale']);

            if (empty($search_data['products'])) {
                $search_data['products'] = '';
            }

            $smarty->assign('search_prefilled', $search_data['products']);
            $search_data['products']['forsale'] = 'Y';

            $smarty->assign('navigation_script', X_SEARCH_URL . '&mode=search');

            if (
                $REQUEST_METHOD == 'GET'
                && $mode == 'search'
                && empty($products)
                && empty($top_message['content'])
            ) {

                $no_results_warning = array(
                    'type'    => 'W',
                    'content' => func_get_langvar_by_name('lbl_warning_no_search_results', false, false, true)
                );
                $smarty->assign('top_message', $no_results_warning);
            }

            if ($mode == 'search' && !empty($products)) {
                $location[] = array(
                    func_get_langvar_by_name('lbl_search'),
                    'partner_banners.php?bannerid=' . $banner['bannerid'] . '&get=1'
                );
                $location[] = array(
                    func_get_langvar_by_name('lbl_products_list'),
                    ''
                );

            } else {

                $location[] = array(
                    func_get_langvar_by_name('lbl_search'), ''
                );
            }
        }

    } elseif ($banner['banner_type'] == 'C') {

        // Search category

        $categoryid = intval($categoryid);

        x_load('category');

        $location[count($location) - 1][1] = 'partner_banners.php?bannerid=' . $banner['bannerid'];

        if ($categoryid) {

            $category = func_get_category_data($categoryid);

            if (!$category) {
                func_header_location('partner_banners.php?bannerid=' . $banner['bannerid'] . '&get=1');
            }

            $smarty->assign('categoryid', $categoryid);
            $smarty->assign('category',   $category);

            $arr  = func_get_category_path($categoryid);
            $arr1 = func_get_category_path($categoryid, 'category');
            foreach ($arr as $k => $c) {
                $location[] = array(
                    $arr1[$k],
                    'partner_banners.php?bannerid=' . $banner['bannerid'] . '&get=1&categoryid=' . $c
                );
            }

            $location[count($location) - 1][1] = '';

        }

        // Category search

        $categories = func_get_categories_list($categoryid, false);
        $all_categories = func_get_categories_list(0, false, true);

        foreach ($all_categories as $k => $v) {

            if (empty($v['parentid'])) {
                continue;
            }

            if (!is_array($all_categories[$v['parentid']]['childs'])) {
                $all_categories[$v['parentid']]['childs'] = array($k => &$all_categories[$k]);
            } else {
                $all_categories[$v['parentid']]['childs'][$k] = &$all_categories[$k];
            }

            if (isset($categories[$v['parentid']])) {
                $categories[$v['parentid']]['childs'] = $all_categories[$v['parentid']]['childs'];
            }

        }

        if (!empty($categories)) {
            $smarty->assign('categories',     $categories);
            $smarty->assign('all_categories', $all_categories);
        }

    } elseif ($banner['banner_type'] == 'F') {

        // Search manufacturer

        if ($manufacturerid) {

            x_load('category');
            $manufacturer = func_query_first("SELECT $sql_tbl[manufacturers].*, IF($sql_tbl[images_M].id IS NULL, '', 'Y') as is_image, IFNULL($sql_tbl[manufacturers_lng].manufacturer, $sql_tbl[manufacturers].manufacturer) as manufacturer, IFNULL($sql_tbl[manufacturers_lng].descr, $sql_tbl[manufacturers].descr) as descr FROM $sql_tbl[manufacturers] LEFT JOIN $sql_tbl[manufacturers_lng] ON $sql_tbl[manufacturers_lng].manufacturerid = $sql_tbl[manufacturers].manufacturerid AND $sql_tbl[manufacturers_lng].code = '$shop_language' LEFT JOIN $sql_tbl[images_M] ON $sql_tbl[images_M].id = $sql_tbl[manufacturers].manufacturerid WHERE $sql_tbl[manufacturers].manufacturerid = '$manufacturerid'");

            if (!$manufacturer) {
                func_header_location('partner_banners.php?bannerid=' . $banner['bannerid'] . '&get=1');
            }

            $smarty->assign('manufacturerid', $manufacturerid);
            $smarty->assign('manufacturer',   $manufacturer);

            $location[] = array(
                func_get_langvar_by_name('lbl_manufacturers'),
                'partner_banners.php?get=1&bannerid=' . $bannerid
            );
            $location[] = array(
                $manufacturer['manufacturer'], ''
            );

        } else {

            // Manufacturers list

            $manufacturers = func_query("SELECT $sql_tbl[manufacturers].*, IF($sql_tbl[images_M].id IS NULL, '', 'Y') as is_image, IFNULL($sql_tbl[manufacturers_lng].manufacturer, $sql_tbl[manufacturers].manufacturer) as manufacturer, IFNULL($sql_tbl[manufacturers_lng].descr, $sql_tbl[manufacturers].descr) as descr FROM $sql_tbl[manufacturers] LEFT JOIN $sql_tbl[manufacturers_lng] ON $sql_tbl[manufacturers_lng].manufacturerid = $sql_tbl[manufacturers].manufacturerid AND $sql_tbl[manufacturers_lng].code = '$shop_language' LEFT JOIN $sql_tbl[images_M] ON $sql_tbl[images_M].id = $sql_tbl[manufacturers].manufacturerid ORDER BY $sql_tbl[manufacturers].orderby");

            if (!empty($manufacturers))
                $smarty->assign('manufacturers', $manufacturers);

            $location[] = array(
                func_get_langvar_by_name('lbl_manufacturers'),
                ''
            );
        }

    } elseif (in_array($banner_type, array('T', 'G', 'M'))) {

        $location[] = array(
            func_get_langvar_by_name('lbl_banner_html_code'),
            ''
        );
    }

    $smarty->assign('get', true);

} elseif ($banner) {

    $dialog_tools_data['right'][] = array(
        'link' => 'partner_banners.php?get=1&bannerid=' . $bannerid,
        'title' => func_get_langvar_by_name('lbl_banner_html_code')
    );
}

$banners = func_query("SELECT *, IF(userid = '" . ($is_partner_area ? $logged_userid : '') . "', 0, 1) AS own_banner FROM $sql_tbl[partner_banners]" . ($is_partner_area ? " WHERE (userid = '$logged_userid' OR (userid = '0' AND avail = 'Y'))" : '') . " ORDER BY own_banner, banner");

if ($banners) {
    foreach ($banners as $k => $v) {

        $banners[$k]['banner_type_text'] = func_get_banner_type_text($v['banner_type']);

        if ($is_partner_area) {

            $banners[$k]['can_edit'] = $v['userid'] == $logged_userid;

        } elseif ($v['userid'] == 0) {

            // Admin can edit own banners only
            $banners[$k]['can_edit'] = true;

        } else {

            $banners[$k]['user'] = func_get_login_by_userid($v['userid']);
        }

    }

    $smarty->assign('banners', $banners);
}

if ($banner_type) {
    $smarty->assign('banner_type', $banner_type);
}

if ($is_partner_area) {
    $smarty->assign('current_partner', $logged_userid);
}

$smarty->assign(
    'has_elements',
    func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[images_L]")
);

$smarty->assign('now',            XC_TIME);
$smarty->assign('main',           'partner_banners');
$smarty->assign('elements_names', $elements_names);

// Assign the current location line
$smarty->assign('location', $location);

?>
