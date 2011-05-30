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
 * Functions for the Discount coupons module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.php,v 1.23.2.1 2011/01/10 13:11:56 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

/**
 * Check discount
 * Discount coupons
 * Status: A - active, D - disabled, U - used
 */
function func_is_valid_coupon ($coupon)
{
    global $cart, $products, $single_mode, $sql_tbl, $logged_userid;
    global $config;

    $coupon = addslashes($coupon);

    if (empty($coupon)) {
        return 7;
    }

    $my_coupon = func_query_first("SELECT * FROM $sql_tbl[discount_coupons] WHERE coupon='$coupon' AND status='A' AND expire>" . XC_TIME);

    if (!$my_coupon) {
        return 1;
    }

    if (!$single_mode) {

        $products_providers = func_get_products_providers ($products);
        if (!in_array ($my_coupon['provider'], $products_providers))
            return 2;
    }

    if ($my_coupon['per_user'] == 'Y') {

        if (empty($logged_userid))
            return 1;
        
        $_times_used = func_query_first_cell("SELECT times_used FROM $sql_tbl[discount_coupons_login] WHERE coupon='$coupon' AND userid='$logged_userid'");
        
        if (intval($_times_used) >= $my_coupon['times'])
            return 5;
    }

    $cart['coupon_type'] = $my_coupon['coupon_type'];

    if ($my_coupon['coupon_type'] == 'percent' && $my_coupon['discount'] > 100) {
        return 1;
    }

    if ($my_coupon['coupon_type'] == 'free_ship' && $config['Shipping']['enable_shipping'] != 'Y') {
        return 6;
    }

    if ($my_coupon['productid'] > 0) {

        $found = false;

        foreach ($products as $value) {
            if ((!$single_mode) && ($my_coupon['provider'] != $value['provider']))
                next;

            if ($value['productid'] == $my_coupon['productid'])
                $found = true;
        }

        return ($found ? 0 : 4);

    } elseif ($my_coupon['categoryid'] > 0) {

        $found = false;


        if ($my_coupon['recursive'] == 'Y') {
            $category_ids = func_get_category_path($my_coupon['categoryid']);
        } else {
            $category_ids = array($my_coupon['categoryid']);
        }

        if (!is_array($products))
            return 4;

        foreach ($products as $value) {
            if (!$single_mode && $my_coupon['provider'] != $value['provider'])
                continue;
            $product_categories = func_query("SELECT categoryid FROM $sql_tbl[products_categories] WHERE productid='$value[productid]'");
            $is_valid_product = false;
            foreach ($product_categories as $k=>$v) {
                if (in_array($v['categoryid'], $category_ids)) {
                    $is_valid_product = true;
                    break;
                }
            }
            if ($is_valid_product) {
                $found = true;
                break;
            }
        }

        return ($found ? 0 : 4);

    } else {

        $total = 0;

        if (!empty($products) && is_array($products)) {
            foreach ($products as $value) {
                if (($single_mode) || ((!$single_mode) && ($my_coupon['provider'] == $value['provider'])))
                    $total += $value['price']*$value['amount'];
            }
        }

        if ($total < $my_coupon['minimum'])
            return 3;
        else
            return 0;
    }

    return 0;
}

/**
 * Returns regular expression for proper coupon code validation
 */
function func_coupon_validation_regexp() {
    return "[^a-zA-Z0-9_.-]";
}

?>
