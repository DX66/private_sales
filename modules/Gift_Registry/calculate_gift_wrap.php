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
 * Calculate gift wrapping cost during cart totals calculation
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: calculate_gift_wrap.php,v 1.24.2.1 2011/01/10 13:11:57 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

if ($config['General']['enable_gift_wrapping'] == 'Y') {

    // Calculate total cost of the giftwrap

    $providers_count = ($single_mode) ? 1 : count($products_providers);

    if ($providers_count < 1) {

        $return['need_giftwrap'] = 'N';

        return;

    }

    $giftwrap_cost = price_format($config['General']['gift_wrapping_cost']);

    if ($config['General']['sum_up_wrapping_cost'] == 'Y')
        $giftwrap_cost *= $providers_count;

    $return['giftwrap_cost'] = $return['taxed_giftwrap_cost'] = $giftwrap_cost;

    if (!$single_mode) {

        // Distribute the cost among orders
        $giftwrap_cost_part = price_format($giftwrap_cost / $providers_count);
        $last_order_index = 0;

        foreach ($return['orders'] as $k => $order) {

            if (empty($order['provider'])) // order contains gift certificate
                continue;

            $return['orders'][$k]['giftwrap_cost'] = $return['orders'][$k]['taxed_giftwrap_cost'] = $giftwrap_cost_part;

            $last_order_index = $k;

        }

        $giftwrap_cost_rest = price_format($giftwrap_cost - ($giftwrap_cost_part*$providers_count));

        if ($giftwrap_cost_rest > 0) {

            $return['orders'][$last_order_index]['giftwrap_cost'] = $giftwrap_cost_rest;
            $return['orders'][$last_order_index]['taxed_giftwrap_cost'] = $giftwrap_cost_rest;

        }

    } else {

        $return['orders'][0]['giftwrap_cost'] = $return['orders'][0]['taxed_giftwrap_cost'] = $giftwrap_cost;

    }

    // Apply taxes

    if (
        !empty($config['General']['gift_wrap_taxes']) 
        && $giftwrap_cost > 0
    ) {

        $tax_cost_surcharge = 0;
        $tax_surcharges = array();

        $taxids = explode(";", $config['General']['gift_wrap_taxes']);

        $taxes = func_query("SELECT $sql_tbl[taxes].* FROM $sql_tbl[taxes] WHERE $sql_tbl[taxes].taxid IN ('".implode("','",$taxids)."') AND $sql_tbl[taxes].active='Y' ORDER BY $sql_tbl[taxes].priority");

        if (
            !empty($taxes) 
            && is_array($taxes)
        ) {

            foreach($return['orders'] as $k => $order) {

                if (empty($return['orders'][$i]['provider'])) // order contains gift certificate
                    continue;

                $_taxes = func_get_giftwrap_tax_rates($taxes, $order['provider']);

                if (!empty($_taxes)) {

                    $tax = func_tax_price($order['giftwrap_cost'], 0, false, NULL, '', $_taxes);

                    $return['orders'][$k]['taxed_giftwrap_cost'] = $tax['taxed_price'];
                    $tax_cost_surcharge                         += array_sum($tax['taxes']);
                    $tax_surcharges[$k]                          = $tax['taxes'];

                }

            }

        }

        $return['taxed_giftwrap_cost'] = $giftwrap_cost + $tax_cost_surcharge;

    }

    // Adjust totals

    if (
        isset($cart['need_giftwrap'])
        && $cart['need_giftwrap'] == 'Y'
    ) {

        $return['total_cost'] += $return['taxed_giftwrap_cost'];
        $return['tax_cost']   += $return['taxed_giftwrap_cost'] - $return['giftwrap_cost'];

        foreach($return['orders'] as $k => $order) {

            $return['orders'][$k]['total_cost'] += $order['taxed_giftwrap_cost'];
            $return['orders'][$k]['tax_cost']   += $order['taxed_giftwrap_cost'] - $order['giftwrap_cost'];

            if (!empty($order['taxes'])) {

                foreach($order['taxes'] as $_tax => $t) {

                    if (
                        isset($tax_surcharges[$k][$t['taxid']])
                        && $tax_surcharges[$k][$t['taxid']] > 0
                    ) {

                        $return['orders'][$k]['taxes'][$_tax]['tax_cost'] += $tax_surcharges[$k][$t['taxid']];
                        $return['taxes'][$_tax]['tax_cost']               += $tax_surcharges[$k][$t['taxid']];

                    }

                }

            }

        }

    }

}
?>
