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
 * Called from func_calculate()
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: calculate_return.php,v 1.31.2.1 2011/01/10 13:12:02 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }

if (!$single_mode) {

    if (!empty($result['have_offers']))
        $return['have_offers'] = true;

    if (!empty($result['bonuses'])) {

        $return['bonuses']['points'] += $result['bonuses']['points'];

        $return['bonuses']['memberships'] = func_array_merge_assoc($return['bonuses']['memberships'], $result['bonuses']['memberships']);

        if (empty($return['bonuses']['memberships']))
            $return['bonuses']['memberships'] = false;

        if ($return['bonuses']['points'] == 0 && empty($return['bonuses']['memberships']))
            unset($return['bonuses']);
    }

    if (isset($return['extra'])) {
        $return['extra']['special_bonuses'] = false;

        if (!empty($return['bonuses'])) {
            $return['extra']['special_bonuses'] = $return['bonuses'];
        }
    }

    if (empty($return['not_used_free_products'])) {

        $return['not_used_free_products'] = $result['not_used_free_products'];

    } else {

        $return['not_used_free_products'] = func_offer_merge_free_products(
            $return['not_used_free_products'],
            $result['not_used_free_products']
        );

    }

    if (!isset($return['blocked_points'])) {

        $return['blocked_points'] = 0;

    }

    $return['blocked_points'] += $result['blocked_points'];
}

global $store_language, $sp_offer_types;

foreach ($sp_offer_types as $offer_type) {

    $offer_type .= '_offers';

    if (is_array($result[$offer_type])) {

        foreach ($result[$offer_type] as $k => $v) {

            $promo = func_get_offer_promo($v['offerid'], $store_language);

            $result[$offer_type][$k] = func_array_merge($v, $promo);

        }


        if (!isset($return[$offer_type]))  {

            $return[$offer_type] = array();

        }

        $return[$offer_type] = func_array_merge_assoc($return[$offer_type], $result[$offer_type]);

    }

}

if (empty($return['bonuses']))
    $return['bonuses'] = false;

if (empty($return['not_used_free_products']))
    $return['not_used_free_products'] = false;

/**
 * Assign cartid to products and correct max_cartid
 */
$__key = ($single_mode) ? 0 : $key;

func_offer_correct_cartid($return, $__key, $cart, $single_mode);

?>
