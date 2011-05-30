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
 * Manufacturers management
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: manufacturers.php,v 1.65.2.1 2011/01/10 13:11:59 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load(
    'backoffice',
    'image'
);

$location[] = array(func_get_langvar_by_name('lbl_manufacturers'), '');

// NOTES.
// 1. Only administrator can activate manufacturer and set up its position in
// the manufacturers list.
// 2. Provider can view the entire list of manufacturers but edit or delete only
// manufacturers created by the same provider.
// 3. If some manufacturer have assigned products of at least one provider that
// is not owner of this manufacturer, owner will not be able to delete that
// manufacturer.

$provider_condition = ($single_mode || $current_area == 'A') ? '' : " AND provider = '" . $logged_userid . "' ";

$manufacturerid = intval($manufacturerid);

$administrate = $current_area == 'A' || (!empty($active_modules['Simple_Mode']) && $current_area == 'P');

// Check the permissions to update manufacturer details
$do_not_touch = !empty($manufacturerid) && !empty($provider_condition) && func_manufacturer_is_used($manufacturerid, $logged_userid);

// Check if current provider is not owner of the manufacturer
if (
    !empty($manufacturerid)
    && !$single_mode
    && $current_area == 'P'
) {
    $m_provider = func_query_first_cell("SELECT provider FROM " . $sql_tbl['manufacturers'] . " WHERE manufacturerid='" . $manufacturerid . "'");

    if ($m_provider != $logged_userid)
        $do_not_touch = true;
}

if (
    $REQUEST_METHOD == 'POST'
    || (
        $mode == 'delete_image'
        && $manufacturerid
    )
) {
    if (
        $mode == 'details'
        && ($image_perms = func_check_image_storage_perms($file_upload_data, 'M')) !== true
    ) {
        // Check permissions
        $top_message = array(
            'content' => $image_perms['content'],
            'type' => 'E'
        );

    } elseif ($mode == 'details') {

        // Modify manufacturer details

        $orderby = intval($orderby);

        if ($administrate) {

            $clean_url = trim(stripslashes($clean_url));

            $current_clean_url = NULL;

            if (!empty($manufacturerid)) {

                $current_clean_url = func_clean_url_get_raw_resource_url('M', $manufacturerid);

            }

            if (
                $config['SEO']['clean_urls_enabled'] == 'N'
                || !empty($provider_condition)
                || (
                    !empty($manufacturerid)
                    && !zerolen($current_clean_url)
                    && $current_clean_url == $clean_url
                )
            ) {

                $clean_url_check_result = true;

            } else {

                list(
                    $clean_url_check_result,
                    $check_url_error_code
                ) = func_clean_url_validate($clean_url);

            }

            if ($clean_url_check_result == false) {

                $top_message = array(
                    'content'               => func_get_langvar_by_name('err_' . strtolower($check_url_error_code)),
                    'type'                  => 'E',
                    'clean_url_fill_error'  => true
                );

                if (empty($manufacturerid)) {

                    func_header_location("manufacturers.php?mode=add");

                } else {

                    func_header_location("manufacturers.php?manufacturerid=" . $manufacturerid);

                }

            }

        }

        // Check XSS injection

        if (!$user_account['allow_active_content']) {

            $descr = func_xss_free($descr, false, true);
            $tmp_url = func_clear_from_xss('<a href="' . $url . '">test</a>', false, true);
            $tmp_url2 = func_clear_from_xss($url, false, true);

            if ($tmp_url['changed'] || $tmp_url2['changed']) {

                $top_message = array(
                    'type' => 'E',
                    'content' => func_get_langvar_by_name('msg_untrusted_provider')
                );

                if (empty($manufacturerid))
                    func_header_location("manufacturers.php?mode=add");
                else
                    func_header_location("manufacturers.php?manufacturerid=".$manufacturerid);

            }

        }

        if (!empty($manufacturerid)) {

            if (empty($manufacturer)) {

                $top_message = array(
                    'content' => func_get_langvar_by_name('msg_adm_err_manufacturer_empty'),
                    'type' => 'E'
                );

                func_header_location("manufacturers.php?manufacturerid=" . $manufacturerid);

            } elseif (func_query_first_cell("SELECT COUNT(*) FROM " . $sql_tbl['manufacturers'] . " WHERE manufacturer = '" . $manufacturer . "' AND manufacturerid != '" . $manufacturerid . "'")) {

                $top_message = array(
                    'content' => func_get_langvar_by_name('msg_adm_err_manufacturer_exist'),
                    'type' => 'E'
                );

                func_header_location("manufacturers.php?manufacturerid=" . $manufacturerid);

              }

            $url = trim($url);

            if (!empty($url)) {

                if (!func_check_manufacturer_url($url)) {

                    $top_message = array(
                        'type' => 'E',
                        'content' => func_get_langvar_by_name('lbl_wrong_url_format')
                    );

                    func_header_location("manufacturers.php?manufacturerid=".$manufacturerid);

                }

            }

            // Update the manufacturer details

            $query_data = array(
                'url'              => $url,
                'descr'            => $descr,
                'meta_keywords'    => $meta_keywords,
                'meta_description' => $meta_description,
                'title_tag'        => $title_tag,
            );

            $query_data_lng = array(
                'manufacturerid' => $manufacturerid,
                'code'           => $shop_language,
                'descr'          => $descr,
            );

            if (!$do_not_touch) {

                $query_data_lng['manufacturer'] = $manufacturer;

                if (func_query_first_cell("SELECT COUNT(*) FROM " . $sql_tbl['manufacturers'] . " WHERE manufacturer = '" . $manufacturer . "'") == 0) {
                    $query_data['manufacturer'] = $manufacturer;
                }

            } else {

                $query_data_lng['manufacturer'] = func_query_first_cell("SELECT manufacturer FROM " . $sql_tbl['manufacturers_lng'] . " WHERE manufacturerid = '" . $manufacturerid . "' AND code = '" . $shop_language . "'");

                if (empty($query_data_lng['manufacturer']))
                    $query_data_lng['manufacturer'] = func_query_first_cell("SELECT manufacturer FROM " . $sql_tbl['manufacturers_lng'] . " WHERE manufacturerid = '" . $manufacturerid . "'");

            }

            if ($shop_language != $config['default_admin_language']) {
                func_unset($query_data, 'manufacturer', 'descr');
            }

            if ($administrate) {
                $query_data['avail']   = $avail;
                $query_data['orderby'] = $orderby;
            }

            func_array2update(
                'manufacturers',
                $query_data,
                "manufacturerid='" . $manufacturerid . "' " . $provider_condition
            );

            func_array2insert(
                'manufacturers_lng',
                $query_data_lng,
                true
            );

            $top_message['content'] = func_get_langvar_by_name('msg_adm_err_manufacturer_upd');

        } else {

            // Add new manufacturer

            if (empty($manufacturer)) {

                $top_message = array(
                    'content' => func_get_langvar_by_name('msg_adm_err_manufacturer_empty'),
                    'type' => 'E'
                );

                func_header_location("manufacturers.php?mode=add");

            } elseif (func_query_first_cell("SELECT COUNT(*) FROM " . $sql_tbl['manufacturers'] . " WHERE manufacturer = '" . $manufacturer . "'")) {

                $top_message = array(
                    'content' => func_get_langvar_by_name('msg_adm_err_manufacturer_exist'),
                    'type' => 'E'
                );

                func_header_location("manufacturers.php?mode=add");

            } else {

                $url = trim($url);

                if (!empty($url)) {

                    if (!func_check_manufacturer_url($url)) {

                        $top_message = array(
                            'type' => 'E',
                            'content' => func_get_langvar_by_name('lbl_wrong_url_format')
                        );

                        func_header_location("manufacturers.php?mode=add");

                    }

                }

                $max_orderby = func_query_first_cell("SELECT MAX(orderby) FROM " . $sql_tbl['manufacturers']);

                if ($orderby <= 0) {
                    $orderby = $max_orderby + 10;
                }

                $query_data = array(
                    'manufacturer'      => $manufacturer,
                    'provider'          => $logged_userid,
                    'descr'             => $descr,
                    'url'               => $url,
                    'meta_keywords'     => $meta_keywords,
                    'meta_description'  => $meta_description,
                    'title_tag'         => $title_tag,
                );

                if ($administrate) {

                    $query_data['avail']   = $avail;
                    $query_data['orderby'] = $orderby;

                } else {

                    $query_data['avail']   = 'N';
                    $query_data['orderby'] = $max_orderby + 1;

                }

                $manufacturerid = func_array2insert(
                    'manufacturers',
                    $query_data
                );

                $query_data = array(
                    'manufacturerid'    => $manufacturerid,
                    'code'              => $shop_language,
                    'manufacturer'      => $manufacturer,
                    'descr'             => $descr,
                );

                func_array2insert(
                    'manufacturers_lng',
                    $query_data
                );

                $top_message['content'] = func_get_langvar_by_name('msg_adm_err_manufacturer_add');

            }

        }

        if (!empty($manufacturerid) && $administrate) {

               if (empty($provider_condition)) {

                if ($config['SEO']['clean_urls_enabled'] == 'N') {
                    // Autogenerate clean URL.
                    $clean_url = func_clean_url_autogenerate('M', $manufacturerid, array('manufacturer' => $manufacturer));

                    $clean_url_save_in_history = false;

                }

                // Insert/Update Clean URL.
                if (func_clean_url_resource_has_record('M', $manufacturerid)) {

                    func_clean_url_update($clean_url, 'M', $manufacturerid, $clean_url_save_in_history == 'Y');

                } else {

                    func_clean_url_add($clean_url, 'M', $manufacturerid);

                }

            } else {

                // Provider can not edit clean url, so let's assign it automatically if the manufacturer does not have one.
                if (!func_clean_url_resource_has_record('M', $manufacturerid)) {

                    $clean_url = func_clean_url_autogenerate('M', $manufacturerid, array('manufacturer' => $manufacturer));

                    func_clean_url_add($clean_url, 'M', $manufacturerid);

                }

            }

        }

        if (
            $manufacturerid > 0
            && !$do_not_touch
            && func_check_image_posted($file_upload_data, 'M')
        ) {

            func_save_image($file_upload_data, 'M', $manufacturerid);

        }

    } elseif (
        $mode == 'delete'
        && !empty($to_delete)
        && is_array($to_delete)
    ) {

        // Delete selected manufacturers

        $ids = func_query_column("SELECT manufacturerid FROM $sql_tbl[manufacturers] WHERE manufacturerid IN ('" . implode("','", array_keys($to_delete)) . "') " . $provider_condition);

        $implodeIds =  ' IN (\'' . implode("','", $ids) . '\')';

        if (!empty($ids)) {

            db_query("DELETE FROM $sql_tbl[manufacturers] WHERE manufacturerid" . $implodeIds);
            db_query("DELETE FROM $sql_tbl[manufacturers_lng] WHERE manufacturerid" . $implodeIds);
            db_query("UPDATE $sql_tbl[products] SET manufacturerid = '0' WHERE manufacturerid" . $implodeIds);

            func_delete_image($ids, 'M');

            db_query("DELETE FROM $sql_tbl[clean_urls] WHERE resource_type = 'M' AND resource_id" . $implodeIds);
            db_query("DELETE FROM $sql_tbl[clean_urls_history] WHERE resource_type = 'M' AND resource_id" . $implodeIds);

            $top_message = array(
                'content' => func_get_langvar_by_name('msg_adm_manufacturer_del')
            );

        }

    } elseif (
        $mode == 'delete_image'
        && $manufacturerid
        && !$do_not_touch
    ) {

        // Delete image of selected manufacturer

        func_delete_image($manufacturerid, 'M');

    } elseif (
        $mode == 'update'
        && $administrate
    ) {

        // Update manufacturers list

        if (is_array($records)) {

            foreach ($records as $k => $v) {

                func_array2update(
                    'manufacturers',
                    array(
                        'avail'     => empty($v['avail']) ? 'N' : 'Y',
                        'orderby'     => intval($v['orderby']),
                    ),
                    "manufacturerid = '" . $k . "' " . $provider_condition
                );
            }

            $top_message = array(
                'content' => func_get_langvar_by_name('msg_adm_manufacturers_upd')
            );

        }

    } elseif (
        $mode == 'clean_urls_history'
        && $manufacturerid
        && $administrate
    ) {

        if (
            empty($clean_urls_history)
            || !is_array($clean_urls_history)
        ) {

            $top_message = array(
                'content' => func_get_langvar_by_name('err_clean_urls_history_empty'),
                'type' => 'E'
            );

        } elseif (func_clean_url_history_delete(array_keys($clean_urls_history))) {

            $top_message = array(
                'content' => func_get_langvar_by_name('txt_clean_urls_history_deleted')
            );

        } else {

            $top_message = array(
                'content' => func_get_langvar_by_name('err_clean_urls_history_delete'),
                'type' => 'E'
            );

        }

    }

    func_header_location("manufacturers.php?manufacturerid=" . $manufacturerid . (!empty($page) ? "&page=" . $page : ''));
}


/**
 * Process the GET request
 */

if (
    $mode == 'add'
    || !empty($manufacturerid)
) {
/**
 * Get the manufacturer data and display manufacturer details page
 */
    $location[count($location)-1][1] = 'manufacturers.php';

    if (!empty($manufacturerid)) {
        $manufacturer_data = func_query_first("SELECT $sql_tbl[manufacturers].*, IF($sql_tbl[images_M].id IS NULL, '', 'Y') as is_image, IFNULL($sql_tbl[manufacturers_lng].manufacturer, $sql_tbl[manufacturers].manufacturer) as manufacturer, IFNULL($sql_tbl[manufacturers_lng].descr, $sql_tbl[manufacturers].descr) as descr, $sql_tbl[clean_urls].clean_url, $sql_tbl[clean_urls].mtime FROM $sql_tbl[manufacturers] LEFT JOIN $sql_tbl[manufacturers_lng] ON $sql_tbl[manufacturers_lng].manufacturerid = $sql_tbl[manufacturers].manufacturerid AND $sql_tbl[manufacturers_lng].code = '$shop_language' LEFT JOIN $sql_tbl[images_M] ON $sql_tbl[images_M].id = $sql_tbl[manufacturers].manufacturerid LEFT JOIN $sql_tbl[clean_urls] ON $sql_tbl[clean_urls].resource_type = 'M' AND $sql_tbl[clean_urls].resource_id = '$manufacturerid' WHERE $sql_tbl[manufacturers].manufacturerid = '$manufacturerid'");

        if (empty($manufacturer_data)) {

            $top_message = array(
                'content' => func_get_langvar_by_name('msg_adm_err_manufacturer_not_exists'),
                'type' => 'E'
            );

            func_header_location('manufacturers.php');

        } else {

            $manufacturer_data['used_by_others'] = func_manufacturer_is_used($manufacturerid, $manufacturer_data['provider']);

            $manufacturer_data['clean_urls_history'] = func_query_hash("SELECT id, clean_url FROM $sql_tbl[clean_urls_history] WHERE resource_type = 'M' AND resource_id = '$manufacturerid' ORDER BY mtime DESC", "id", false, true);

            $location[] = array($manufacturer_data['manufacturer'], '');

            $smarty->assign('manufacturer', $manufacturer_data);
            $smarty->assign('image', func_image_properties('M', $manufacturerid));
        }

    } else {

        $location[] = array(func_get_langvar_by_name('lbl_add_manufacturer'), '');

    }

    $smarty->assign('mode', 'manufacturer_info');

} else {

    // Get and display the manufacturers list

    $total_items = func_query_first_cell ("SELECT COUNT(*) FROM " . $sql_tbl['manufacturers']);

    if ($total_items > 0) {

        // Prepare the page navigation

        $objects_per_page = $config['Manufacturers']['manufacturers_per_page'];

        include $xcart_dir . '/include/navigation.php';

        // Get the manufacturers list

        $manufacturers = func_query("SELECT $sql_tbl[manufacturers].*, CONCAT($sql_tbl[customers].lastname,', ',$sql_tbl[customers].firstname) as provider_name, IF($sql_tbl[customers].id IS NULL,'','Y') as is_provider FROM $sql_tbl[manufacturers] LEFT JOIN $sql_tbl[customers] ON $sql_tbl[manufacturers].provider=$sql_tbl[customers].id ORDER BY $sql_tbl[manufacturers].orderby, $sql_tbl[manufacturers].manufacturer".($objects_per_page > 0 ? " LIMIT $first_page, $objects_per_page" : ""));

        if (is_array($manufacturers)) {

            foreach ($manufacturers as $k => $v) {
                $manufacturers[$k]['products_count'] = func_query_first_cell ("SELECT COUNT(*) FROM $sql_tbl[products] WHERE manufacturerid = '$v[manufacturerid]'");
                $manufacturers[$k]['used_by_others'] = func_manufacturer_is_used($v['manufacturerid'], $v['provider']);
            }

            $smarty->assign('navigation_script', 'manufacturers.php?');
            $smarty->assign('manufacturers',     $manufacturers);
            $smarty->assign('first_item',        $first_page + 1);
            $smarty->assign('last_item',         min($first_page + $objects_per_page, $total_items));

        }

    }

    $smarty->assign('total_items', $total_items);

}

if (!empty($page)) {
    $smarty->assign('page', $page);
}

$smarty->assign('administrate', $administrate);
?>
