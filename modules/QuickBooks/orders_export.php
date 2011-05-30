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
 * Export to QuickBooks
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: orders_export.php,v 1.34.2.1 2011/01/10 13:12:01 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

x_load('crypt');

define('QUICKBOOKS_MACOS_EOL', false);

/**
 * Export orders (invoices, payments, customers info) to QuickBooks format
 */
foreach ($orders_full as $key=>$value) {
    foreach ($value as $subkey => $subvalue) {
        if ($subkey == 'details')
            $orders_full[$key][$subkey] = text_decrypt($orders_full [$key][$subkey]);

        if ($subkey == 'product_options') {
            $orders_full[$key][$subkey] = strtr($orders_full[$key][$subkey], "\r\t", '');
            if (defined('QUICKBOOKS_MACOS_EOL') && constant('QUICKBOOKS_MACOS_EOL')) {
                $orders_full[$key][$subkey] = str_replace("\n", "   ", $orders_full[$key][$subkey]);
            } else {
                $orders_full[$key][$subkey] = str_replace("\n", "\\n", $orders_full[$key][$subkey]);
            }

        }
        else
            $orders_full[$key][$subkey] = strtr($orders_full[$key][$subkey], "\r\n\t", " ");
    }

    $orders_full[$key]['shipping'] = func_query_first_cell("select shipping from $sql_tbl[shipping] where shippingid='".$value["shippingid"]."'");

    $orders_full[$key]['cost'] = price_format($value['price'] * $value['amount']);

    $orders_full[$key]['b_statename'] = func_get_state($value['b_state'],$value['b_country']);
    $orders_full[$key]['s_statename'] = func_get_state($value['s_state'],$value['s_country']);
    $orders_full[$key]['b_countryname'] = func_get_country($value['b_country']);
    $orders_full[$key]['s_countryname'] = func_get_country($value['s_country']);
    if ($config['General']['use_counties'] == 'Y') {
        $orders_full[$key]['b_countyname'] = func_get_county($value['b_county']);
        $orders_full[$key]['s_countyname'] = func_get_county($value['s_county']);
    }

    $orders_full[$key]['tax_values'] = unserialize($value['taxes_applied']);
    if ($value['giftcert_ids']) {
        $tmp = array();
        foreach (explode("*", $value['giftcert_ids']) as $v){
            if ($v) {
                list($giftcert_id, $giftcert_cost) = explode(":", $v);
                $tmp[] = "GC#".$giftcert_id." (".$giftcert_cost.")";
            }
        }

        $orders_full[$key]['applied_giftcerts'] = join(", ",$tmp);
    }

    if (!empty($config['QuickBooks']['qb_order_prefix'])) {
        $prefix = trim($config['QuickBooks']['qb_order_prefix']);
        if ($prefix != '') {
            $orders_full[$key]['orderid'] = $prefix.$orders_full[$key]['orderid'];
        }
    }

    if ($orders_full[$key]['coupon_discount'] > 0 && strstr($orders_full[$key]['coupon'], 'free_ship') ) {
        $orders_full[$key]['shipping_cost'] += $orders_full[$key]['coupon_discount'];
    }

    if (empty($orders_full[$key]['b_firstname']) && empty($orders_full[$key]['b_lastname'])) {
        $orders_full[$key]['b_firstname'] = $orders_full[$key]['firstname'];
        $orders_full[$key]['b_lastname'] = $orders_full[$key]['lastname'];
    }

    if (empty($orders_full[$key]['s_firstname']) && empty($orders_full[$key]['s_lastname'])) {
        $orders_full[$key]['s_firstname'] = $orders_full[$key]['firstname'];
        $orders_full[$key]['s_lastname'] = $orders_full[$key]['lastname'];
    }

    foreach($orders_full[$key] as $k => $v) {
        if (!is_array($v))
            $orders_full[$key][$k] = preg_replace("/[^\w\d _!@#$%\^\*\(\)\[\];:\.<>\/\?\+=\|\\\\-]/Ss", '', $v);
    }

    if (++$i % $dot_per_row == 0)
        func_flush('.');
}

$smarty->assign('orders', $orders_full);
$export_data = func_display('modules/QuickBooks/orders_export_qb.tpl', $smarty, false);
?>
