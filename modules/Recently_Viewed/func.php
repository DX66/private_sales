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
 * Recently viewed products module functions
 *
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @category   X-Cart
 * @package    Modules
 * @subpackage Recently Viewed
 * @version    $Id: func.php,v 1.8.2.1 2011/01/10 13:12:01 ferz Exp $
 * @since      4.4.0
 */

if (!defined('XCART_START')) { header('Location: ../../'); die('Access denied'); }

/**
 * Return html code for section if products requested via ajax
 * made for html catalog
 *
 * @return string
 */
function func_ajax_info_rviewed()
{
    if (isset($_GET['id'])) {
        rviewed_save_product($_GET['id']);
    }

    $products = rviewed_get_products();

    if (!empty($products)) {

        global $smarty;
 
        $smarty->assign('rviewed_products', $products);

        $src = func_display('modules/Recently_Viewed/content.tpl', $smarty, false);

        return $src;

    } else {

        return '';

    }
}

/**
 * Save viewed product in the session
 *
 * @param  int  $id product id
 * @return void
 */
function rviewed_save_product($id)
{
    $id = intval($id);

    if ($id == 0) {
        return;
    }

    x_session_register('rviewed_products');

    global $rviewed_products, $config;

    // store product id with current time
    $rviewed_products[$id] = time();

    // sort products by time from high to low
    arsort($rviewed_products);

    // remove products which are out of limit
    $limit = intval($config['Recently_Viewed']['rviewed_products_count']);
    $array = array_chunk($rviewed_products, $limit, true);

    $rviewed_products = $array[0];
}

/**
 * Get recently viewed products from the session
 *
 * @param  bool  $detailed return short or detailed information about product
 * @return array numeric array with products data
 */
function rviewed_get_products($detailed = false)
{
    x_session_register('rviewed_products');

    global $rviewed_products;

    if (!empty($rviewed_products)) {

        global $config;

        x_load('product');

        // remove products which are out of limit
        $limit = intval($config['Recently_Viewed']['rviewed_products_count']);
        $array = array_chunk($rviewed_products, $limit, true);

        $rviewed_products = $array[0];

        if (!is_array($rviewed_products)) {
            return false;
        }

        // get membershipid
        if (isset($GLOBALS['user_account']) && isset($GLOBALS['user_account']['membershipid'])) {

            $membershipid = $GLOBALS['user_account']['membershipid'];

        } else {

            $membershipid = 0;

        }

        if ($detailed != true) {

            global $sql_tbl;

            $where = "$sql_tbl[products].productid IN (" . implode(array_keys($rviewed_products), ',') . ')';

            $products = func_search_products(array('where' => array($where)), $membershipid);

        } else {

            foreach ($rviewed_products as $id => $time) {

                $products[] = func_select_product($id, $membershipid);

            }

        }

        return $products;

    } else {

        return false;

    }
}

?>
