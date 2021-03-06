<?php
/* vim: set ts=4 sw=4 sts=4 et: */
/*****************************************************************************\
 +-----------------------------------------------------------------------------+
 | X-Cart                                                                      |
 | Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>                  |
 | All rights reserved.                                                        |
 * -----------------------------------------------------------------------------+
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
 * -----------------------------------------------------------------------------+
\**************************************************************************** */

/**
 * Page generation script for catalog
 *
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @category   X-Cart
 * @package    Modules
 * @subpackage Products Map
 * @version    $Id: html_catalog.php,v 1.4.2.1 2011/01/10 13:12:01 ferz Exp $
 * @since      4.4.0
 */
if (!defined('XCART_START')) { header('Location: ../../'); die('Access denied');}

$GLOBALS['pmap_generation_flag'] = true;

foreach ($hc_state['catalog_dirs'] as $catdir) {

    $hc_state['catalog'] = $catdir;

    $current_lng = $catdir['code'];

    $pmap_symbols = pmap_get_symbols();

    // process each symbol page in the pmap
    foreach ($pmap_symbols as $pmap_symbol => $pmap_display) {
 
        // get source code of first page
        $pmap_page_params = $additional_hc_data_current['page_params'] . $pmap_symbol;

        list($http_headers, $page_src) = func_fetch_page(
            $site_location['host'] . ':' . $site_location['port'],
            $site_location['path'] . DIR_CUSTOMER . '/' . $additional_hc_data_current['page_url'],
            'sl=' . $catdir['code'] . $shop_closed_var . '&' . $pmap_page_params,
            $robot_cookies
        );

        // generate hc file for first page
        $pmap_name_func_params = sprintf($additional_hc_data_current['name_func_params'][0], $pmap_symbol, 1);

        convert_page(
            $catdir['path'],
            $page_src,
            $additional_hc_data_current['name_func'],
            array($pmap_name_func_params)
        );

        // find additonal pages
        $pmap_url = $additional_hc_data_current['page_url'] . '?' . $pmap_page_params;
        $pmap_pattern = '/' . preg_quote($pmap_url) . "&[^\"']*page=(\d+)[&\"']/S";

        if (preg_match_all($pmap_pattern, $page_src, $pmap_matches)) {

            $pmap_add_pages = array_unique($pmap_matches[1]);

        } else {

            continue;

        }

        // generate hc files for additional founded pages
        foreach ($pmap_add_pages as $pmap_add_page) {

            if ($pmap_add_page != 1) {

                list($http_headers, $page_src) = func_fetch_page(
                    $site_location['host'] . ':' . $site_location['port'],
                    $site_location['path'] . DIR_CUSTOMER . '/' . $additional_hc_data_current['page_url'],
                    'sl=' . $catdir['code'] . $shop_closed_var . '&' . $pmap_page_params . '&page=' . $pmap_add_page,
                    $robot_cookies
                );

                $pmap_name_func_params = sprintf($additional_hc_data_current['name_func_params'][0], $pmap_symbol, $pmap_add_page);

                convert_page(
                    $catdir['path'],
                    $page_src,
                    $additional_hc_data_current['name_func'],
                    array($pmap_name_func_params)
                );

            }

        }

    }

}

unset(
    $pmap_symbols, 
    $pmap_symbol, 
    $pmap_display, 
    $pmap_page_params, 
    $pmap_url, 
    $pmap_pattern, 
    $pmap_add_pages, 
    $pmap_matches, 
    $pmap_add_page, 
    $pmap_name_func_params
);

unset(
    $GLOBALS['pmap_generation_flag']
);

?>
