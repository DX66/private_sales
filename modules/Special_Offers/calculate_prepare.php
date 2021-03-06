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
 * Prepare necessary data for calculation
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: calculate_prepare.php,v 1.27.2.1 2011/01/10 13:12:02 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }

// First step: get all applicable offers and apply the 'products for free' bonus

$offers_order_bonuses = false;
$not_used_free_products = false;

global $customer_unused_offers;
global $customer_available_offers;

$current_offers = array();

$blocked_points = 0;

if (is_array($products)) {

    // clear fields
    foreach ($products as $k => $v) {

        $sp_fields = array(
            'free_amount', 
            'have_offers', 
            'free_shipping_used', 
            'special_price_used', 
            'saved_original_price', 
            'sp_use_certain_free_ship',
        );

        foreach ($sp_fields as $_tmp_k) {
            $products[$k][$_tmp_k] = false;
        }

    }

    $tmp_cart = array('products' => $products);

    if (func_cart_normalize($tmp_cart)) {

        $products = $tmp_cart['products'];

    }

    $all_offers = func_get_applicable_offers($products, $customer_info, $provider_for, '', false, true);

    extract($all_offers);
    unset($all_offers);

    if (is_array($applied_offers)) {

        $sp_loop_limit = 50;
        $sp_counter = 0;

        do {
            $t_products = $products;
        } while (
            false === func_offer_set_free_products($applied_offers, $t_products, $offers_order_bonuses) 
            && $sp_loop_limit > $sp_counter++
        );

        $is_condition_ok = true;

        foreach ($applied_offers as $_offer) {

            foreach ($_offer['conditions'] as $_condition) {

                if (!func_offer_check_condition($provider_for, $t_products, $customer_info, $_condition)) {

                    $is_condition_ok = false;

                    break;

                }

            }

            if (!$is_condition_ok)
                break;

        }

        if ($is_condition_ok) {

            $products = $t_products;

            foreach ($applied_offers as $offer) {

                $current_offers[] = $offer['offerid'];

            }

        }

    } // if (is_array($applied_offers))

} // if (is_array($products))

if (x_session_is_registered('customer_available_offers')) {

    x_session_register('customer_available_offers');

    if (is_array($customer_available_offers)) {

        $new_unused_offers = array_diff($customer_available_offers, $current_offers);

        if (is_array($customer_unused_offers)) {

            $customer_unused_offers = array_intersect($customer_unused_offers, $new_unused_offers);

        } else {

            $customer_unused_offers = $new_unused_offers;

        }

    }

}

if (empty($customer_unused_offers)) {

    $customer_unused_offers = false;

}

global $smarty;

$smarty->assign('customer_unused_offers', $customer_unused_offers);

?>
