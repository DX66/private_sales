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
 * Normalize configurable products in cart
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: pconf_customer_cart_normalization.php,v 1.16.2.1 2011/01/10 13:12:00 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

if (
    empty($active_modules['Product_Configurator'])
    || !isset($cart)
    || !isset($cart['products'])
    || empty($cart['products'])
    || !is_array($cart['products'])
) {
    return;
}

$pconf_hash = array();

foreach ($cart['products'] as $k => $p) {

    if (empty($p['pconf_data']))
        continue;

    $po = !empty($p['options']) && is_array($p['options'])
        ? serialize($p['options'])
        : '';

    $key = $p['productid'] . $po;

    if (!empty($p['pconf_data']['steps'])) {

        foreach ($p['pconf_data']['steps'] as $stepid => $step) {

            if (empty($step['slots']))
                continue;

            foreach ($step['slots'] as $slotid => $slot) {

                $po = !empty($slot['options']) && is_array($slot['options'])
                    ? serialize($slot['options'])
                    : '';

                $key .= "\n" . $slot['productid'] . $po . $slot['free_price'];

            }

        }

    } // if (!empty($p['pconf_data']['steps']))

    if (isset($pconf_hash[$key])) {

        // Unite several product items
        $cart_changed = true;

        $cart['products'][$pconf_hash[$key]]['amount'] += $p['amount'];

        unset($cart['products'][$k]);

        // Unite several product sub items
        $add_amount_slots = array();

        foreach ($cart['products'] as $k2 => $p2) {

            if (
                $p2['hidden']
                && $p2['hidden'] == $p['cartid']
            ) {
                $add_amount_slots[$p2['slotid']] = $p2['amount'];
            }

        }

        if (!empty($add_amount_slots)) {

            foreach ($cart['products'] as $k2 => $p2) {

                if (
                    $p2['hidden']
                    && $p2['hidden'] == $cart['products'][$pconf_hash[$key]]['cartid']
                    && isset($add_amount_slots[$p2['slotid']])
                ) {
                    $cart['products'][$k2]['amount'] += $add_amount_slots[$p2['slotid']];
                }

            }
        }

    } else {

        $pconf_hash[$key] = $k;

    }

} // foreach ($cart['products'] as $k => $p)

?>
