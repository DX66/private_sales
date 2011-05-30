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
 * Configuration data in the wishlist
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: pconf_customer_wishlist.php,v 1.36.2.1 2011/01/10 13:12:00 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

x_load(
    'product',
    'taxes'
);

$pconf = true;

if (is_array($wl_products)) {

    foreach ($wl_products as $k => $v) {

        if (
            $v['product_type'] == 'C'
            && !empty($v['object'])
        ) {

            // Extract the configurable product

            $productid = $v['productid'];

            $configurations[$productid] = unserialize($v['object']);

            $taxed_price     = 0;
            $taxes             = array();
            $flag_disabled     = false;

            if (is_array($configurations[$productid]['steps'])) {

                foreach ($configurations[$productid]['steps'] as $stepid => $step_info) {

                    if (is_array($step_info['slots'])) {

                        foreach ($step_info['slots'] as $slotid => $productinfo) {

                            $product_data = func_select_product($productinfo['productid'], @$user_account['membershipid'], false);

                            if (
                                is_array($product_data)
                                && is_array($productinfo)
                            ) {
                                $product_data = func_array_merge($product_data, $productinfo);
                            }

                            if (empty($product_data)) {
                                $flag_disabled = true;
                                break;
                            }

                            // Get the price modifier

                            $price_modifier_data = func_query_first("SELECT * FROM $sql_tbl[pconf_slot_markups] WHERE slotid='$slotid' AND membershipid IN ('$user_account[membershipid]', '0') ORDER BY membershipid DESC");

                            if ($price_modifier_data) {

                                if ($price_modifier_data['markup_type'] == "$") {

                                    $product_data['price_modifier'] = $price_modifier_data['markup'];

                                } elseif ($price_modifier_data['markup_type'] == "%") {

                                    $product_data['price_modifier'] = price_format($product_data['price'] * $price_modifier_data['markup'] / 100);

                                }

                                if ($product_data['price'] + $product_data['price_modifier'] < 0)
                                    $product_data['price_modifier'] = $product_data['price'];

                                $product_data['price'] = $product_data['price'] + $product_data['price_modifier'];

                            }

                            $wl_products[$k]['price']         += $product_data['price'];
                            $product_data['taxes']             = func_get_product_taxes($product_data, $user_account['id']);
                            $product_data['display_price']     = $product_data['taxed_price'];

                            if (is_array($product_data['taxes'])) {

                                foreach ($product_data['taxes'] as $tax_name => $product_tax) {

                                    if (!isset($taxes[$tax_name])) {

                                        $taxes[$tax_name] = $product_tax;

                                        $taxes[$tax_name]['tax_value'] = 0;

                                    }

                                    $taxes[$tax_name]['tax_value'] += $product_tax['tax_value'];

                                }

                            }

                            $taxed_price += $product_data['taxed_price'];

                            $wl_products[$k]['subproducts'][] = $product_data;

                        } // foreach ($step_info['slots'] as $slotid => $productinfo)

                    } // if (is_array($step_info['slots']))

                    if ($flag_disabled)
                        break;

                } // foreach ($configurations[$productid]['steps'] as $stepid => $step_info)

            } // if (is_array($configurations[$productid]['steps']))

            if ($flag_disabled) {

                unset($wl_products[$k]);

                $ids_redirect[$v['wishlistid']] = $v['productid'];

                break;
            }

            $wl_products[$k]['options_surcharge'] = 0;

            if (!empty($active_modules['Product_Options'])) {

                list(
                    $variant,
                    $product_options_result
                ) = func_get_product_options_data($wl_products[$k]['productid'], $wl_products[$k]['options'], @$user_account['membershipid']);

                if ($product_options_result) {

                    foreach($product_options_result as $o) {

                        $wl_products[$k]['options_surcharge'] += (
                            $o['modifier_type'] == '%'
                                ? ($wl_products[$k]['price'] * $o['price_modifier'] / 100)
                                : $o['price_modifier']
                        );

                    }

                }

            }

            $wl_products[$k]['options_surcharge']     = price_format($wl_products[$k]['options_surcharge']);
            $wl_products[$k]['price']                 += $wl_products[$k]['options_surcharge'];
            $wl_products[$k]['taxes']                 = $taxes;
            $wl_products[$k]['taxed_price']         = $taxed_price;

        }

    } // foreach ($wl_products as $k => $v)

} // if (is_array($wl_products))

?>
