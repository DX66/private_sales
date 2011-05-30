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
 * Check product dimensions according to shippers limits
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: unavailable_shipping.php,v 1.15.2.1 2011/01/10 13:11:51 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('product', 'pack');

$productid = intval($id);

$product = func_select_product($productid, @$user_account['membershipid']);

$provider_condition = ($single_mode ? '' : "AND provider='$logged_userid'");

/**
 * Check define shipping methods
 */
$weight = $product['weight'];
$price = $product['price'];

foreach (array('weight', 'width', 'height', 'length', 'price') as $field)
    $box[$field] = $product[$field];

$defined_shippings = func_query("SELECT $sql_tbl[shipping].shipping FROM $sql_tbl[shipping_rates] LEFT JOIN $sql_tbl[shipping] ON $sql_tbl[shipping].shippingid=$sql_tbl[shipping_rates].shippingid WHERE (minweight > '$weight' OR maxweight < '$weight' OR mintotal > '$price' OR maxtotal < '$sum') AND type='D' $provider_condition");

$_realtime_shippings = array();

if ($config['Shipping']['use_intershipper'] != 'Y') {
    $mods = array('UPS', 'USPS', 'CPC', 'ARB', 'DHL', 'FEDEX', 'AP');

    foreach ($mods as $mod) {
        if (file_exists($xcart_dir.'/shipping/mod_'.$mod.'.php'))
            include_once $xcart_dir.'/shipping/mod_'.$mod.'.php';

        $func_ship = 'func_check_limits_'.$mod;

        if (function_exists($func_ship)) {
            if (!$func_ship($box)) {
                $code = ($mod == 'FEDEX') ? 'FDX' : (($mod == 'AP') ? 'APOST' : $mod);
                if (func_query_first_cell("SELECT active FROM $sql_tbl[shipping] WHERE active='Y' AND code='$code'") == 'Y') {
                    $_realtime_shippings[] = $code;
                }
            }
        }
    }
} else {
    include_once $xcart_dir.'/shipping/intershipper.php';

    if (!func_check_limits_INTERSHIPPER($box))
        $_realtime_shippings[] = 'Intershipper';
}

$list = func_get_carriers();
$realtime_shippings = array();
if (!empty($_realtime_shippings))
foreach ($_realtime_shippings as $code) {
    foreach ($list as $carrier) {
        if ($carrier[0] == $code) {
            $realtime_shippings[] = array('code' => $carrier[1]);
            break;
        }
    }
}

$smarty->assign('defined_shippings', $defined_shippings);
$smarty->assign('realtime_shippings', $realtime_shippings);

func_display('main/popup_unavailable_shipping.tpl',$smarty);
?>
