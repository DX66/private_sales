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
 * Calculate bonus points
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: calculate_bp_bonus.php,v 1.17.2.1 2011/01/10 13:12:02 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }

foreach ($return['orders'] as $k => $order) {

    if (!is_array($order['applied_offers'])) continue;
    $points_total = 0;
    $is_giftcert = empty($order['provider']);

    foreach ($order['applied_offers'] as $offer_key => $offer) {

        if (!is_array($offer['bonuses'])) continue;
        $mult = empty($offer['mult']) ? 1 : $offer['mult'];

        foreach ($offer['bonuses'] as $bonus_key => $bonus) {

            if ($is_giftcert && $bonus['bonus_type'] == 'B') {
                unset($bonus);
                unset($order['applied_offers'][$offer_key]['bonuses'][$bonus_key]);
            }

            if ($bonus['bonus_type'] != 'B' || empty($bonus)) continue;

            if ($bonus['amount_type'] == 'S') {
                $total = $order['display_discounted_subtotal'];
                if ($bonus['bonus_data']['total_type'] == 'OT' && $total > $order['total_cost'] ) {
                    $total = $order['total_cost'];
                }
                if ($bonus['bonus_data']['apply_to_cnd_sets'] == 'Y') {
                    $bp_amount = func_offer_get_cnd_bonus_points($offer, $order['products'], $bonus);
                }
                else {
                    $bp_amount = ($bonus['amount_max'] > 0) ? round(($total*$bonus['amount_min'])/$bonus['amount_max']) : 0;
                }
            } else {
                $bp_amount = $bonus['amount_min']*$mult;
            }

            $points_total += $bp_amount;
        }

        if ($is_giftcert && empty($order['applied_offers'][$offer_key]['bonuses'])) {
            unset($return['orders'][$k]['applied_offers'][$offer_key]);
        }
    }

    if ($points_total > 0) {
        $return['orders'][$k]['bonuses']['points'] += $points_total;

        if (!isset($return['bonuses']['points'])) $return['bonuses']['points'] = 0;
        $return['bonuses']['points'] += $points_total;
    }
}

?>
