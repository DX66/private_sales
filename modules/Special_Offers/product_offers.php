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
 * Check product offers
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: product_offers.php,v 1.27.2.1 2011/01/10 13:12:02 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }

$offers = func_get_product_offers($logged_userid, $current_area, $productid, false);

if (!empty($offers)) {

    $sorted_offers = func_get_sorted_offers($offers[$productid]);

    $product_offers = array();
    $customer_info = func_userinfo($logged_userid, $current_area);

    if (is_array($sorted_offers)) {
        foreach ($sorted_offers as $offer) {

            if (!is_array($offer['conditions'])) continue;

            $product_sets = array();
            $excluded_sets = array();
            $s_condition = false;

            foreach ($offer['conditions'] as $cnd) {
                if ($cnd['condition_type'] == 'S' && is_array($cnd['product_sets']['I']) && is_array($cnd['params'])) {
                    $s_condition = $cnd;
                    break;
                }
            }

            if ($s_condition) {

                foreach ($s_condition['params'] as $k => $param) {

                    if (in_array($param['param_type'], array('C', 'R'))) {
                        $tmp_condition = array();
                        $tmp_condition['params'][] = $param;
                        $tmp_condition['product_sets']['I'][] = $param['setid'];
                        $tmp_product = $product_info;
                        $tmp_product['amount'] = $param['param_qnty'];
                        $tmp_products = array($tmp_product);

                        if (func_offer_check_condition_set($offer['provider'], $tmp_products, $customer_info, $tmp_condition)) {
                            $param['param_type'] = 'P';
                            $param['param_id'] = $productid;
                        } else {
                            $excluded_sets[$param['setid']] = true;
                            unset($product_sets[$param['setid']]);
                        }
                    }

                    if ($param['param_type'] != 'P' || isset($excluded_sets[$param['setid']])) continue;

                    $add_product = ($param['param_id'] == $productid) ? $product_info : func_select_product($param['param_id'], $customer_info['membershipid']);

                    if ($config['General']['unlimited_products'] != 'Y' &&
                        ($add_product['avail'] <= 0 ||
                         $add_product['avail'] < $add_product['min_amount'] ||
                         $add_product['avail'] < $param['param_qnty'])) {

                        $excluded_sets[$param['setid']] = true;
                        unset($product_sets[$param['setid']]);
                        continue;
                    }

                    if ($add_product) {
                        $product_sets[$param['setid']][$param['param_id']] = array(
                            'productid'                => $add_product['productid'],
                            'productcode'              => $add_product['productcode'],
                            'producttitle'             => $add_product['producttitle'],
                            'display_price'            => $add_product['taxed_price'],
                            'display_discounted_price' => $add_product['taxed_price']*$param['param_qnty'],
                            'amount'                   => $param['param_qnty'],
                        );
                    }
                }
            }

            if (!empty($product_sets)) {
                $product_offer = array();
                foreach ($product_sets as $setid => $prod_set) {
                    if (!empty($prod_set) && func_get_applicable_offers($prod_set, $customer_info, $offer['provider'], '', $offer['offerid']) && isset($prod_set[$productid])) {
                        $product_offer['product_sets'][$setid]['curr_item_amount'] = $prod_set[$productid]['amount'];
                        $product_offer['product_sets'][$setid]['subtotal'] = func_offer_get_subtotal($prod_set);
                        $product_offer['product_sets'][$setid]['products'] = $prod_set;
                    }
                }

                if ($product_offer['product_sets']) {
                    $product_offer['promo_items_amount'] = $offer['promo_items_amount'];
                    $product_offer['html_items_amount'] = $offer['html_items_amount'];
                    $product_offers[$offer['offerid']] = $product_offer;
                }
            }
        }
    }

    $smarty->assign('product_offers', $product_offers);
}

?>
