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
 * Gets info about slot product
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: slot_product.php,v 1.18.2.1 2011/01/10 13:12:00 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

if (
    empty($active_modules['Product_Configurator'])
    || empty($_GET['pconf'])
    || empty($_GET['slot'])
    || empty($product_info)
) {
    return;
}

$slot = abs(intval($slot));

$slot_info = func_pconf_get_slot_data($slot);

if (!$slot_info)
    return;

/**
 * Correct qty if product already added to other slots
 */
x_session_register('configurations');

if (!empty($configurations)) {

    $amount_in_slots = 0;

    foreach ($configurations as $c_productid => $c_step_info) {

        if (
            !is_array($c_step_info['steps'])
            || empty($c_step_info['steps'])
        ) {
            continue;
        }

        foreach ($c_step_info['steps'] as $c_stepid => $c_slot_info) {

            if (
                !is_array($c_slot_info['slots'])
                || empty($c_slot_info['slots'])
            ) {
                continue;
            }

             foreach ($c_slot_info['slots'] as $c_slotid => $c_product_info) {

                if (
                    $c_product_info['productid'] != $product_info['productid']
                    || !empty($product_info['variantid'])
                    || $c_slotid == $slot
                ) {
                    continue;
                }

                $amount_in_slots += $c_product_info['amount'];

            } // foreach ($c_slot_info['slots'] as $c_slotid => $c_product_info)

        } // foreach ($c_step_info['steps'] as $c_stepid => $c_slot_info)

    } // foreach ($configurations as $c_productid => $c_step_info)

} // if (!empty($configurations))

$avail = $product_info['avail'] - $amount_in_slots;

/**
 * Adjust qty for slots which can have multiple products
 */
if ($slot_info['multiple'] == 'Y') {

    $product_info['appearance']['empty_stock'] = ($avail < $current_slot['amount_min']);

    $_min_qty = $product_info['appearance']['min_quantity'] = max ($product_info['appearance']['min_quantity'], $slot_info['amount_min']);

    $_max_qty = $product_info['appearance']['max_quantity'] = min ($product_info['appearance']['max_quantity'], $slot_info['amount_max']);

    $product_info['appearance']['loop_quantity'] = $_max_qty + 1;

}

?>
