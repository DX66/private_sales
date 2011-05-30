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
 * Product options editing interface (in a popup window)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Customer interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: popup_poptions.php,v 1.37.2.1 2011/01/10 13:11:43 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './auth.php';

/**
 * Check input data
 */
if (
    strlen((string)$id) == 0
    || !in_array($target, array('cart', 'wishlist'))
    || empty($active_modules['Product_Options'])
) {
    func_close_window();
}

/**
 * Get productid
 */
$productid = false;

$eventid = isset($eventid) ? intval($eventid) : 0;
$id = isset($id) ? intval($id) : 0;
$productid = isset($productid) ? intval($productid) : 0;

if (
    $target == 'wishlist'
) {

    $tmp = func_query_first("SELECT productid, amount FROM $sql_tbl[wishlist] WHERE wishlistid = '$id' AND event_id = '$eventid'");
    $productid = $tmp['productid'];
    $min_avail = $tmp['amount'];

} elseif (
    $target == 'cart'
    && !func_is_cart_empty($cart)
) {

    foreach ($cart['products'] as $k => $p) {
        if ($p['cartid'] == $id) {
            $cartindex = $k;
            break;
        }
    }

    if (isset($cartindex)) {
        $productid  = $cart['products'][$cartindex]['productid'];
        $min_avail  = $cart['products'][$cartindex]['avail'];
        $amount     = $cart['products'][$cartindex]['amount'];
        $variantid  = $cart['products'][$cartindex]['variantid'];
    }
}

if (empty($productid)) {
    func_close_window();
}

/**
 * Get Product options list
 */
if (
    $target == 'cart'
    && !func_is_cart_empty($cart)
) {

    $options = $cart['products'][$cartindex]['options'];

} elseif ($target == 'wishlist') {

    $options = unserialize(func_query_first_cell("SELECT options FROM $sql_tbl[wishlist] WHERE wishlistid = '$id' AND event_id = '$eventid'"));

}

if (!empty($options)) {
    foreach ($options as $k => $v) {
        $options[$k] = stripslashes($v);
    }
}

x_load('product');

$product_info = func_select_product($productid, @$user_account['membershipid']);

include $xcart_dir . '/modules/Product_Options/customer_options.php';

if (
    !empty($variants) 
    && isset($variantid)
) {

    $variants[$variantid]['avail'] += $amount;

    $smarty->assign('variants', $variants);

}

/**
 * Update data
 */
if (
    $REQUEST_METHOD == 'POST'
    && $mode == 'update'
    && !empty($_POST['product_options'])
) {

    $poptions = $_POST['product_options'];

    $cart['products'][$cartindex]['options_expired'] = false;

    if (!func_check_product_options($productid, $poptions))
        func_header_location("popup_poptions.php?target=$target&id=$id&err=exception");

    if (
        $target == 'cart'
        && !func_is_cart_empty($cart)
    ) {

        $amount = func_get_options_amount($poptions, $cart['products'][$cartindex]['productid']);

        $is_pconf = false;

        if (!empty($active_modules['Product_Configurator'])) {

            $is_pconf = (func_query_first_cell("SELECT product_type FROM $sql_tbl[products] WHERE productid = '".$cart['products'][$cartindex]['productid']."'") == 'C');

        }

        if (
            $amount >= $cart['products'][$cartindex]['amount']
            || $config['General']['unlimited_products'] == 'Y'
            || $is_pconf
        ) {

            $cart['products'][$cartindex]['options'] = $poptions;

            func_unset($cart['products'][$cartindex], 'variantid');

            $vid = func_get_variantid($cart['products'][$cartindex]['options'], $cart['products'][$cartindex]['productid']);

            if (!empty($vid))
                $cart['products'][$cartindex]['variantid'] = $vid;

        } else {

            func_header_location("popup_poptions.php?target=$target&id=$id&err=avail");

        }

        // Recalculate cart totals after updating
        $products = func_products_in_cart($cart, (!empty($user_account['membershipid']) ? $user_account['membershipid'] : ''));
        $cart = func_array_merge($cart, func_calculate($cart, $products, $logged_userid, $current_area, 0));

    } elseif ($target == 'wishlist') {

        db_query("UPDATE $sql_tbl[wishlist] SET options = '".addslashes(serialize($poptions))."' WHERE wishlistid = '$id' AND event_id = '$eventid'");

    }

    func_reload_parent_window();
}

if (!$min_avail)
    $min_avail = func_query_first_cell("SELECT min_amount FROM $sql_tbl[products] WHERE productid = '$productid'");

if (!$min_avail)
    $min_avail = 1;

$smarty->assign('product',      $product_info);
$smarty->assign('target',       $target);
$smarty->assign('id',           $id);
$smarty->assign('eventid',      $eventid);
$smarty->assign('min_avail',    $min_avail);
$smarty->assign('alert_msg',    'Y');
$smarty->assign('err',          $err);

$location = array(
    array(
        $product_info['product'],
    ),
    array(
        func_get_langvar_by_name('lbl_edit_options'),
    ),
);

$smarty->assign('template_name', 'modules/Product_Options/popup_poptions.tpl');

func_display('customer/help/popup_info.tpl', $smarty);
?>
