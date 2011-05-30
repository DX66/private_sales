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
 * Collect necessary options and variants data for the product page
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: customer_options.php,v 1.54.2.1 2011/01/10 13:12:00 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

if (empty($err)) $err = '';

$product_options    = func_get_product_classes($productid, !empty($product_info['is_taxes']));
$product_options_ex = func_get_product_exceptions($productid);
$product_options_js = ($product_info['allow_active_content'])
    ? func_get_product_js_code($productid)
    : '';
$variants           = func_get_product_variants($productid, $user_account['membershipid']);

$membershipid = ($current_area == 'C')
    ? $user_account['membershipid']
    : $userinfo['membershipid'];

if (empty($options))
    $options = func_get_default_options($productid, $product_info['min_amount'], $user_account['membershipid']);

if (
    !empty($product_options)
    && !empty($options)
    && is_array($options)
) {

    // Define pre-selected options
    foreach ($product_options as $k => $v) {

        if (preg_match("/^\d+$/S", $options[$v['classid']])) {

            if (
                $v['is_modifier'] == 'T'
                || $v['is_modifier'] == 'A'
            ) {

                $product_options[$k]['default'] = $options[$v['classid']];

            } else {

                if (!isset($v['options'][$options[$v['classid']]])) // Correction for X-AOM
                    continue;

                $product_options[$k]['options'][$options[$v['classid']]]['selected'] = 'Y';

            }

        } else {

            $product_options[$k]['default'] = $options[$v['classid']];

        }

    }

}

if (!empty($product_options))
    $smarty->assign('product_options', $product_options);

if (!empty($product_options_ex))
    $smarty->assign('product_options_ex', $product_options_ex);

if (!empty($variants)) {

    foreach ($variants as $v) {

        if ($v['taxed_price'] != 0) {

            $smarty->assign('variant_price_no_empty', true);

            break;

        }

    }

    $smarty->assign('variants', $variants);
}

$smarty->assign('err',                   $err);
$smarty->assign('product_options_count', is_array($product_options) ? count($product_options) : 0);
$smarty->assign('product_options_js',    @trim($product_options_js));

?>
