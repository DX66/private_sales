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
 * Shipping methods management
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: shipping.php,v 1.63.2.1 2011/01/10 13:11:47 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './auth.php';
require $xcart_dir.'/include/security.php';

$location[] = array(func_get_langvar_by_name('lbl_shipping_methods'), '');

$carriers = func_query("SELECT code, shipping, COUNT(*) as total_methods FROM $sql_tbl[shipping] WHERE code!='' GROUP BY code ORDER BY code");

$carrier_valid = false;

if (!empty($carriers)) {
    $carrier_names = array (
        'CPC' => "Canada Post",
        'USPS' => 'U.S.P.S.',
        'APOST' => "Australia Post",
    );

    foreach ($carriers as $k=>$v) {
        if ($v['code'] == @$carrier)
            $carrier_valid = true;

        $_carrier_total_enabled = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[shipping] WHERE code='$v[code]' AND active='Y'");

        if (!empty($carrier_names[$v['code']]))
            $_carrier_name = $carrier_names[$v['code']];
        else
            $_carrier_name = preg_replace("/^([^ ]+).*$/", "\\1", $v['shipping']);

        $carriers[$k]['shipping'] = $_carrier_name;
        $carriers[$k]['total_enabled'] = $_carrier_total_enabled;
    }
}

if (!$carrier_valid)
    $carrier = '';

if ($mode == 'enable_all' || $mode == 'disable_all') {
    db_query("UPDATE $sql_tbl[shipping] SET active='".($mode == "enable_all" ? "Y" : "N")."'");
    func_header_location('shipping.php'.(!empty($carrier) ? "?carrier=$carrier" : ''));
}

if ($REQUEST_METHOD == 'POST') {

    if (!empty($data)) {
        foreach ($data as $id => $arr) {
            $arr['active'] = isset($arr['active']) ? 'Y' : 'N';
            $arr['is_cod'] = isset($arr['is_cod']) ? 'Y' : 'N';
            $arr['weight_min'] = func_convert_number($arr['weight_min']);
            $arr['weight_limit'] = func_convert_number($arr['weight_limit']);
            func_array2update('shipping', $arr, "shippingid = '$id'");
        }
    }

    if (!empty($add['shipping'])) {
        $add['active'] = isset($add['active']) ? 'Y' : 'N';
        $add['is_cod'] = isset($add['is_cod']) ? 'Y' : 'N';
        $add['weight_min'] = func_convert_number($add['weight_min']);
        $add['weight_limit'] = func_convert_number($add['weight_limit']);
        func_array2insert('shipping', $add);
    }

    $top_message['content'] = func_get_langvar_by_name('msg_adm_shipping_methods_upd');

    if (!empty($active_modules['Google_Checkout'])) {
        func_gcheckout_check_shipping();
    }

    func_header_location('shipping.php'.(!empty($carrier) ? "?carrier=$carrier" : ''));
}

if ($mode == 'delete') {
/**
 * Delete shipping option & associated info
 */
    db_query("DELETE FROM $sql_tbl[shipping] WHERE shippingid='$shippingid'");
    db_query("DELETE FROM $sql_tbl[shipping_rates] WHERE shippingid='$shippingid'");
    db_query("DELETE FROM $sql_tbl[delivery] WHERE shippingid='$shippingid'");

    $top_message['content'] = func_get_langvar_by_name('msg_adm_shipping_method_del');

    func_header_location('shipping.php');
}

$condition = '';

if (!empty($active_modules['UPS_OnLine_Tools']) && $config['Shipping']['use_intershipper'] != 'Y') {
    include $xcart_dir.'/modules/UPS_OnLine_Tools/ups_shipping_methods.php';
}

if (!empty($active_modules['Google_Checkout'])) {
    func_gcheckout_check_shipping();
    $smarty->assign('permanent_warning', $permanent_warning);
}

$shipping = func_query("SELECT * FROM $sql_tbl[shipping] WHERE 1 $condition ORDER BY orderby, shipping");
$new_shipping = '';
if (!empty($shipping)) {
    foreach ($shipping as $v) {
        if ($v['is_new'] == 'Y') {
            $new_shipping = 'Y';
            break;
        }
    }
}

$smarty->assign('shipping', $shipping);
$smarty->assign('new_shipping', $new_shipping);
$smarty->assign('carriers', $carriers);
$smarty->assign('carrier', $carrier);

$smarty->assign('main','shipping');

// Assign the current location line
$smarty->assign('location', $location);

include './shipping_tools.php';

// Assign the section navigation data
$smarty->assign('dialog_tools_data', $dialog_tools_data);

if (
    file_exists($xcart_dir.'/modules/gold_display.php')
    && is_readable($xcart_dir.'/modules/gold_display.php')
) {
    include $xcart_dir.'/modules/gold_display.php';
}
func_display('admin/home.tpl',$smarty);
?>
