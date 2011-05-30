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
 * Shipping rates management interface
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Provder interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: shipping_rates.php,v 1.68.2.1 2011/01/10 13:12:09 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './auth.php';
require $xcart_dir.'/include/security.php';

if ($config['Shipping']['enable_shipping'] != 'Y') {
    func_header_location("error_message.php?shipping_disabled");
}

// This value is used as a default top range value
// for weight and order subtotal ranges (used in Smarty template)

$maxvalue = 999999.99;


// Shipping rates - D (defined rates)
// Shipping markups - R (for realtime methods only)

if ($type != 'R') {
    $type = 'D';
    $location[] = array(func_get_langvar_by_name('lbl_shipping_charges'), '');
}
else {
    if ($config['Shipping']['realtime_shipping'] != 'Y')
        func_header_location("error_message.php?realtime_shipping_disabled");

    $location[] = array(func_get_langvar_by_name('lbl_shipping_markups'), '');
}

$type_condition        = " AND type = '".$type."'";
$provider_condition    = $single_mode ? '' : " AND provider = '".$logged_userid."'";

function func_shipping_set_apply_to_parameter($zone_id, $ship_id, $apply_to)
{
    global $type_condition, $provider_condition;

    $upd_condition = "zoneid = '".$zone_id."' AND shippingid = '".$ship_id."'".$provider_condition.$type_condition;
    $avail_types = array('ST', 'DST');

    $query_data = array(
        'apply_to' => in_array($apply_to, $avail_types) ? $apply_to : 'DST',
    );

    $result = func_array2update('shipping_rates', $query_data, $upd_condition);

    return $result;
}

if ($REQUEST_METHOD=="POST") {

    if ($mode == 'delete') {

        // Delete shipping option

        if (is_array($posted_data)) {
            $deleted = false;
            foreach ($posted_data as $rateid=>$v) {
                if (empty($v['to_delete']))
                    continue;

                db_query("DELETE FROM $sql_tbl[shipping_rates] WHERE rateid='$rateid' $provider_condition $type_condition");
                $deleted = true;
            }

            if ($deleted)
                $top_message['content'] = func_get_langvar_by_name('msg_shipping_rates_del');
        }
    }

    if ($mode == 'update') {

        // Update shipping table

        if (is_array($posted_data)) {
            foreach ($posted_data as $rateid => $v) {

                $upd_condition = "rateid = '".$rateid."' ".$provider_condition." ".$type_condition;
                $query_data = array(
                    'minweight'        => func_convert_number($v['minweight']),
                    'maxweight'        => func_convert_number($v['maxweight']),
                    'mintotal'        => func_convert_number($v['mintotal']),
                    'maxtotal'        => func_convert_number($v['maxtotal']),
                    'rate'            => func_convert_number($v['rate']),
                    'item_rate'        => func_convert_number($v['item_rate']),
                    'rate_p'        => func_convert_number($v['rate_p']),
                    'weight_rate'    => func_convert_number($v['weight_rate']),
                );

                func_array2update('shipping_rates', $query_data, $upd_condition);
            }

            $top_message['content'] = func_get_langvar_by_name('msg_shipping_rates_upd');
        }

        if (is_array($apply_to)) {
            foreach ($apply_to as $zone_id => $shipping_methods) {

                if (!is_array($shipping_methods)) continue;

                foreach ($shipping_methods as $ship_id => $value) {
                    func_shipping_set_apply_to_parameter($zone_id, $ship_id, $value);
                }
            }
        }
    }

    if ($mode == 'add') {

        // Add new shipping rate

        if ($shippingid_new) {

            $query_data = array(
                'shippingid'    => $shippingid_new,
                'minweight'        => func_convert_number($minweight_new),
                'maxweight'        => func_convert_number($maxweight_new),
                'maxamount'        => func_convert_number($maxamount_new),
                'mintotal'        => func_convert_number($mintotal_new),
                'maxtotal'        => func_convert_number($maxtotal_new),
                'rate'            => func_convert_number($rate_new),
                'item_rate'        => func_convert_number($item_rate_new),
                'rate_p'        => func_convert_number($rate_p_new),
                'weight_rate'    => func_convert_number($weight_rate_new),
                'provider'        => $logged_userid,
                'zoneid'        => $zoneid_new,
                'type'            => $type,
                'apply_to'        => $apply_to_new,
            );

            func_array2insert('shipping_rates', $query_data);

            $top_message['content'] = func_get_langvar_by_name('msg_shipping_rate_add');

            func_shipping_set_apply_to_parameter($zoneid_new, $shippingid_new, $apply_to_new);
        }
    }

    func_header_location("shipping_rates.php?zoneid=$zoneid&shippingid=$shippingid&type=$type");
}

$zone_condition = ($zoneid!=""?"and $sql_tbl[shipping_rates].zoneid='$zoneid'":"");
$method_condition = ($shippingid!=""?"and $sql_tbl[shipping_rates].shippingid='$shippingid'":"");

$realtime_condition = ($config['Shipping']['realtime_shipping']=="Y"?"and $sql_tbl[shipping].code=''":"");

if (!empty($active_modules['UPS_OnLine_Tools']) && $config['Shipping']['use_intershipper'] != 'Y') {
    include $xcart_dir.'/modules/UPS_OnLine_Tools/ups_shipping_methods.php';
    $ups_condition = $condition;
}
else {
    $ups_condition = '';
}

$shipping_rates = func_query("SELECT $sql_tbl[shipping_rates].*, $sql_tbl[shipping].shipping, $sql_tbl[shipping].shipping_time, $sql_tbl[shipping].destination FROM $sql_tbl[shipping], $sql_tbl[shipping_rates] WHERE $sql_tbl[shipping_rates].shippingid=$sql_tbl[shipping].shippingid AND $sql_tbl[shipping].active='Y' $provider_condition $type_condition $zone_condition $method_condition ".($type=="R"?" AND code!='' ":$realtime_condition)." ORDER BY $sql_tbl[shipping].orderby, $sql_tbl[shipping_rates].maxweight");

/**
 * Prepare zones list
 */
$zones = array(array('zoneid'=>0,'zone'=>func_get_langvar_by_name('lbl_zone_default')));
$_tmp = func_query("SELECT zoneid, zone_name as zone FROM $sql_tbl[zones] WHERE 1 $provider_condition ORDER BY zoneid");
if (!empty($_tmp))
    $zones = func_array_merge($zones,$_tmp);

if (is_array($zones) && is_array($shipping_rates)) {
    foreach ($zones as $zone) {
        $shipping_rates_list = array();
        foreach ($shipping_rates as $shipping_rate) {
            if ($shipping_rate['zoneid'] != $zone['zoneid'])
                continue;

            $shipping_rates_list[$shipping_rate['shippingid']]['shipping'] = $shipping_rate['shipping'];
            $shipping_rates_list[$shipping_rate['shippingid']]['destination'] = $shipping_rate['destination'];
            $shipping_rates_list[$shipping_rate['shippingid']]['apply_to'] = $shipping_rate['apply_to'];
            $shipping_rates_list[$shipping_rate['shippingid']]['rates'][] = $shipping_rate;

        }

        $_zones_list = array();
        $_zones_list['zone'] = $zone;
        $_zones_list['shipping_methods'] = $shipping_rates_list;
        $zones_list[] = $_zones_list;
    }
}

if ($type == 'R') {
    $markup_condition .= " AND code!=''";

    $shipping = func_query("SELECT * FROM $sql_tbl[shipping] WHERE active='Y' $markup_condition ORDER BY shipping, orderby");
}
else {
    $shipping = func_query("SELECT * FROM $sql_tbl[shipping] WHERE active='Y' $realtime_condition $ups_condition ORDER BY shipping, orderby");
}

$smarty->assign('shipping', $shipping);

$smarty->assign('zones', $zones);
$smarty->assign('shipping_rates', $shipping_rates);
$smarty->assign('shipping_rates_avail', (is_array($shipping_rates) ? count($shipping_rates) : 0));
$smarty->assign('zones_list', $zones_list);
$smarty->assign('type', $type);
$smarty->assign('zoneid', $zoneid);
$smarty->assign('shippingid', $shippingid);
$smarty->assign('maxvalue', $maxvalue);

$smarty->assign('main','shipping_rates');

// Assign the current location line
$smarty->assign('location', $location);

if (
    file_exists($xcart_dir.'/modules/gold_display.php')
    && is_readable($xcart_dir.'/modules/gold_display.php')
) {
    include $xcart_dir.'/modules/gold_display.php';
}
func_display('provider/home.tpl',$smarty);
?>
