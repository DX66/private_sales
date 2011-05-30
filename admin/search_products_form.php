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
 * Search products form interface
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: search_products_form.php,v 1.23.2.3 2011/01/10 13:11:47 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if (
    !empty($mode)
    && $mode == 'update_status'
    && !empty($update)
) {
    foreach ($update as $k => $v) {

        if ($k == 'manufacturers') {

            $default = @implode("\n", $v['default']);

        } elseif (
            $k == 'price'
            || $k == 'weight'
        ) {

            $default = $v['default']['begin']."-".$v['default']['end'];

        } else {

            $default = $v['default'];

        }

        x_log_flag('log_activity', 'ACTIVITY', "'$login' user has changed 'Search_products::search_products_$k' option to '$v[avail]\n\nSearch_products::search_products_".$k."_d' option to '$default'");

        db_query("REPLACE INTO $sql_tbl[config] (name, value, category) VALUES ('search_products_$k', '$v[avail]', 'Search_products')");

        db_query("REPLACE INTO $sql_tbl[config] (name, value, category) VALUES ('search_products_" . $k . "_d', '$default', 'Search_products')");
    }

    $default = '';

    if($extra_fields)
        $default = @implode("\n", array_keys($extra_fields));

    x_log_flag('log_activity', 'ACTIVITY', "'$login' user has changed 'Search_products::search_products_extra_fields' option to '$default'");

    db_query("REPLACE INTO $sql_tbl[config] (name, value, category) VALUES ('search_products_extra_fields', '$default', 'Search_products')");

    func_header_location("configuration.php?option=Search_products");
}

$manufacturers = func_query("SELECT manufacturerid, manufacturer FROM $sql_tbl[manufacturers] WHERE avail = 'Y' ORDER BY orderby, manufacturer");

if (
    !empty($active_modules['Manufacturers'])
    && !empty($manufacturers)
) {

    func_manufacturer_selected_for_search($manufacturers);

    $smarty->assign('manufacturers', $manufacturers);

}

$extra_fields = func_query("SELECT fieldid, field FROM $sql_tbl[extra_fields] WHERE active = 'Y' ORDER BY orderby");

if (!empty($extra_fields)) {

    $tmp = explode("\n", $config['Search_products']['search_products_extra_fields']);

    foreach ($extra_fields as $k => $v) {

        if (in_array($v['fieldid'], $tmp)) {

            $extra_fields[$k]['selected'] = 'Y';

        }

    }

    $smarty->assign('extra_fields', $extra_fields);
}

x_load('category');
$smarty->assign('allcategories', func_data_cache_get("get_categories_tree", array(0, true, $shop_language, $user_account['membershipid'])));

?>
