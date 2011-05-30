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
 * This module adds support for downloading electronicaly distributed goods
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Customer interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: download.php,v 1.45.2.3 2011/01/10 13:11:42 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require    './auth.php';

if (empty($active_modules['Egoods']))
    func_403(64);

func_set_time_limit(2700);

x_load('files','backoffice');

if ($_GET['action'] != 'get') {

    // Prepare the appearing of download page

    require $xcart_dir . '/include/common.php';

    if ($id) {

        $productid = func_query_first_cell("SELECT productid FROM $sql_tbl[download_keys] WHERE download_key = '$id'");

        if ($productid) {

            x_load('product');

            $product_data = func_select_product($productid, $user_account['membershipid']);

            if(!empty($active_modules['Extra_Fields'])) {

                $extra_fields_provider = $product_data['provider'];

                include $xcart_dir . '/modules/Extra_Fields/extra_fields.php';

            }

            $distribution     = $product_data['distribution'];

            $provider         = $product_data['provider'];

            if (is_url($distribution)) {

                $size = func_filesize($distribution, ($config['Egoods']['filesize_by_reading'] == 'Y'));

            } else {

                if (
                    !empty($provider)
                    && !$single_mode
                ) {

                    $provider_flag = func_query_first_cell("SELECT $sql_tbl[memberships].flag FROM $sql_tbl[customers], $sql_tbl[memberships] WHERE $sql_tbl[customers].id = '$provider' AND $sql_tbl[customers].membershipid = $sql_tbl[memberships].membershipid");

                    if ($provider_flag == 'RP')
                        $single_mode = true;

                }

                if (
                    empty($provider)
                    || $single_mode
                    || !empty($active_modules['Simple_Mode'])
                ) {

                    $distribution = $files_dir_name . $distribution;

                } else {

                    $distribution = func_get_files_location($provider, 'P') . $distribution;

                }

                $size = func_filesize($distribution);

            }

            $product_data['length'] = number_format($size, 0, '', ' ');

            $smarty->assign('product', $product_data);

            if (empty($QUERY_STRING)) {

                $smarty->assign('url', $xcart_catalogs['customer'] . "/download.php?id=$id&action=get");

            } else {

                $smarty->assign('url', $xcart_catalogs['customer'] . "/download.php?" . $QUERY_STRING . "&action=get");

            }

            if ($product_data['length'] > 0) {
                $smarty->assign(
                    'title_length',
                    func_get_langvar_by_name(
                        'lbl_file_size_extended',
                        array(
                            'length' => $product_data['length'],
                        )
                    )
                );

            }

        }

    }

    $location[] = array(func_get_langvar_by_name('lbl_download'), '');

    $smarty->assign('main',     'download');

    // Assign the current location line
    $smarty->assign('location', $location);

    func_display('customer/home.tpl', $smarty);

    exit;

}

if (empty($id))
    exit();

$res = func_query_first("SELECT productid, expires FROM $sql_tbl[download_keys] WHERE download_key = '$id'");

// If there is corresponding key in database and not expired
if (
    count($res) > 0
    && $res['expires'] > XC_TIME
) {

    // check if there is valid distribution for this product
    $productid = $res['productid'];

    $result = func_query_first("SELECT distribution, product, provider FROM $sql_tbl[products] WHERE productid = '$productid'");

    $distribution     = $result['distribution'];
    $provider         = $result['provider'];

    $remote_file     = is_url($distribution);

    if (!$remote_file) {

        if (
            !empty($provider)
            && !$single_mode
        ) {

            $provider_flag = func_query_first_cell("SELECT $sql_tbl[memberships].flag FROM $sql_tbl[customers], $sql_tbl[memberships] WHERE $sql_tbl[customers].id = '$provider' AND $sql_tbl[customers].membershipid = $sql_tbl[memberships].membershipid");

            if ($provider_flag == 'RP')
                $single_mode = true;

        }

        if (
            empty($provider)
            || $single_mode
            || !empty($active_modules['Simple_Mode'])
        ) {

            $distribution = $files_dir_name . $distribution;

        } else {

            $distribution = func_get_files_location($provider, 'P') . $distribution;

        }

    } else {

        $distribution = strtr($distribution, array(" " => "%20"));

    }

    $fd = func_fopen($distribution, 'rb');

    if (
        $fd
        || (
            $remote_file
            && ($data = func_url_get($distribution))
        )
    ) {

        header("Content-Type: application/force-download");

        header("Content-Disposition: attachment; filename=\"" . basename($distribution) . "\"");

        header("Content-Length: " . func_filesize($distribution));

        if ($fd) {

            while (
                !feof($fd)
                && connection_status() == 0
            ) {

                print(fread($fd, 8192));

                flush();

            }

            fclose($fd);

        } else {

            print($data);

            flush();

        }

    } else {

        $top_message = array(
            'type'         => 'E',
            'content'     => func_get_langvar_by_name('txt_download_failed_msg'),
        );

        func_header_location("download.php?id=" . $id);

    }

} else {

    db_query("DELETE FROM $sql_tbl[download_keys] WHERE expires <= '" . XC_TIME . "'");

    // Assign the current location line
    $smarty->assign('location', $location);

    func_display('modules/Egoods/wrong_key.tpl', $smarty);
}

?>
