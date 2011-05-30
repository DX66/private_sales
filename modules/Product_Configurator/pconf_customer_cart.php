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
 * Operations with configurable products in cart
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: pconf_customer_cart.php,v 1.36.2.3 2011/02/04 15:40:22 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

if (empty($active_modules['Product_Configurator']))
    return;

x_load('cart');

global $pconf_update;

$mode = isset($mode) ? $mode : '';

if ($mode == 'add') {

    // Add configured product to the cart

    if ($added_product['product_type'] == 'C') {

        global $configurations;

        x_session_register('configurations');

        $max_quantity = func_pconf_get_max_quantity($configurations[$productid]);

        if (intval($amount) > $max_quantity) {

            unset($cart['products'][count($cart['products'])-1]);

            func_header_location("pconf.php?productid=$productid&error=amount");

        }

        if (empty($productindex)) {

            $cart['products'][count($cart['products'])-1]['pconf_data'] = $configurations[$productid];
            $cart['products'][count($cart['products'])-1]['extra_data']['pconf'] = array('cartid'=>$cart['products'][count($cart['products'])-1]['cartid']);

        } else {

            $cart['products'][$productindex]['pconf_data'] = $configurations[$productid];
            $cart['products'][$productindex]['extra_data']['pconf'] = array('cartid' => $cart['products'][$productindex]['cartid']);

        }

        if (
            !empty($configurations[$productid]['steps'])
            && is_array($configurations[$productid]['steps'])
        ) {
            foreach ($configurations[$productid]['steps'] as $stepid => $step_info) {

                foreach ($step_info['slots'] as $slotid => $product_info) {

                    $extra_data['pconf'] = array(
                        'parent'             => $cartid,
                        'price_modifier'     => $configurations[$productid]['steps'][$stepid]['slots'][$slotid]['price_modifier']
                    );

                    $cart['products'][] = array(
                        'cartid'         => func_generate_cartid(),
                        'productid'     => $product_info['productid'],
                        'amount'         => $amount * $product_info['amount'],
                        'pcitem_amount' => $product_info['amount'],
                        'options'         => $product_info['options'],
                        'free_price'     => $product_info['price'],
                        'hidden'         => $cartid,
                        'slotid'         => $slotid,
                        'extra_data'    => $extra_data
                    );

                }

            }

            unset($configurations[$productid]);

        }

    }

} elseif ($mode == 'delete') {

    // Delete configured product from the cart

    if (is_array($cart['products'])) {

        foreach ($cart['products'] as $k=>$v) {

            if (@$v['hidden'] != $productindex) {

                $products_tmp[] = $v;

            }

        }

        $cart['products'] = $products_tmp;

        unset($products_tmp);

    }

} elseif (
    $action == 'update'
    && $pconf_update == 'post_update'
) {

    // Update quantity of configured product and all subproducts

    if (is_array($cart['products'])) {

        foreach ($cart['products'] as $k => $v) {

            if ($v['product_type'] == 'C') {

                $need_update = false;
                $pconf_amount = $old_pconf_amount = $v['amount'];

                foreach ($cart['products'] as $k1 => $v1) {

                    if ($v1['hidden'] == $v['cartid']) {

                        $pconf_amount = min($v1['amount'], $pconf_amount);

                        if ($v1['amount'] != $v['amount'] && !func_pconf_is_multiple($v1['slot']))
                            $need_update = true;

                    }

                }

                if ($need_update) {

                    foreach ($cart['products'] as $k1=>$v1) {

                        if ($v1['hidden'] == $v['cartid'])
                            $cart['products'][$k1]['amount'] = $v1['pcitem_amount'] * $pconf_amount;

                    }

                }

                $cart['products'][$k]['amount'] = $pconf_amount;

            }

        }

    }

} elseif ($action == 'update') {

    // Prepare the extended set of products for quantity updating

    if (is_array($cart['products']) && is_array($productindexes)) {

        foreach ($productindexes as $productindex => $new_quantity) {

            if ($cart['products'][$productindex]['product_type'] == 'C') {

                $old_amount = $cart['products'][$productindex]['amount'];
                $cart['products'][$productindex]['amount'] = $new_quantity;

                foreach ($cart['products'] as $k => $v) {

                    if ($v['hidden'] == $cart['products'][$productindex]['cartid'])
                        $productindexes_tmp[$k] = $v['pcitem_amount'] * $new_quantity;

                }

            }

        }

        if (!empty($productindexes_tmp)) {

            foreach ($productindexes_tmp as $productindex => $new_quantity) {

                $productindexes[$productindex] = $new_quantity;

            }

            unset($productindexes_tmp);

        }

    }

}

?>
