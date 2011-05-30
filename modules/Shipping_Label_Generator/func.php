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
 * Functions for the Shipping Label Generator module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.php,v 1.28.2.3 2011/02/01 10:51:15 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

function func_slg_cut_value($value, $length, $add = '...')
{

    return  substr($value, 0, $length - strlen($add)).$add;
}

function func_usps_parse_result($result, $xml_head, $type)
{

    $response['type'] = $type;
    $response['error'] = false;
    $res = func_xml2hash($result);

    if ($res['Error']) {
        $response['error'] = true;
        $response['type'] = "txt";
        $response['data'] = $res['Error']['Description'];
        $response['error_code'] = $res['Error']['Number'];

        return $response;
    }
    if ($res[$xml_head.'Response']) {
        if (!empty($res[$xml_head.'Response']['DeliveryConfirmationLabel'])) {
            $response['data'] = base64_decode(str_replace(array("\n"), array(""), $res[$xml_head.'Response']['DeliveryConfirmationLabel']));
        } elseif (!empty($res[$xml_head.'Response']['LabelImage'])) {
            $response['data'] = base64_decode(str_replace(array("\n"), array(""), $res[$xml_head.'Response']['LabelImage']));
        } elseif (!empty($res[$xml_head.'Response']['EMConfirmationNumber']) && $type == 'txt') {
            $response['data'] = $res[$xml_head.'Response']['EMConfirmationNumber'];
        }
    }

    return $response;
}

function func_usps_save_response($data, $method, $num)
{
    global $xcart_dir;

    if (!is_dir("$xcart_dir/var/tmp/usps_test_labels/")) {
        if (!func_mkdir("$xcart_dir/var/tmp/usps_test_labels/")) {
            return false;
        }
    }

    $fp = fopen("$xcart_dir/var/tmp/usps_test_labels/usps_$method($num).".$data['type'], "w");
    if (!$fp) {
        return false;
    }
    fputs($fp, $data['data']);
    fclose($fp);
    func_chmod_file("$xcart_dir/var/tmp/usps_test_labels/usps_$method($num).".$data['type']);

    return true;
}

/**
 * Check shippingid:
 *    1. Is it U.S.P.S shippingid?
 *    2. Is it valid shippingid?
 */
function func_usps_check_shippingid($shippingid)
{
    global $sql_tbl;

    settype($shippingid, 'int');
    $shipping = func_query_first_cell("SELECT shipping FROM $sql_tbl[shipping] WHERE code = 'USPS' AND shippingid = '".$shippingid."'");
    if (empty($shipping))
        return false;

    $signatures = 
    array(
        'USPS Express Mail International' => 'ExpressMailIntl',
        'USPS Express Mail' => 'ExpressMail',

        'USPS Priority Mail International' => 'PriorityMailIntl',
        'USPS Priority Mail' => 'Priority',

        'USPS First-Class Mail International' => 'FirstClassMailIntl',
        'USPS First Class Mail International' => 'FirstClassMailIntl',
        'USPS First-Class Mail' => 'First Class',
        'USPS First Class Mail' => 'First Class',

        'Parcel Post' => 'Parcel Post',

        'USPS Media' => 'Media Mail',

        'USPS Library' => 'Library Mail',
    );

    $shipping = str_replace(array("##R##","##TM##","##SM##"), '', $shipping);
    foreach ($signatures as $signature => $service_type) {
        if (strpos($shipping, $signature) !== false)
            return $service_type;
    }
    
    return 'Error';
}

function func_dhl_check_shippingid($shippingid, $dst_country = 'US')
{
    global $sql_tbl;

    $shipping = func_query_first("SELECT * FROM $sql_tbl[shipping] WHERE code = 'ARB' AND shippingid = '".$shippingid."'");
    if(empty($shipping))
        return false;

    $service_type = false;
    $service_type['add'] = "";
    switch ($shipping['shipping']) {
        case "DHL/Airborne Express 10:30 AM": {
            $service_type['main'] = "E";
            $service_type['add'] = "1030";
        } break;
        case "DHL/Airborne Express Saturday": {
            $service_type['main'] = "E";
            $service_type['add'] = "SAT";
        } break;
        case "DHL/Airborne Express": {
            $service_type['main'] = "E";
            if ($dst_country != 'US') {
                $service_type['main'] = "IE";
            }
        } break;
        case "DHL/Airborne 2nd Day": {
            $service_type['main'] = "S";
        } break;
        case "DHL/Airborne Ground": {
            $service_type['main'] = "G";
        } break;
        case "DHL/Airborne Next Afternoon": {
            $service_type['main'] = "N";
        } break;
    }

    return $service_type;
}

/**
 * Check shippingid:
 *    1. Is it UPS shippingid?
 *    2. Is it valid shippingid?
 */
function func_ups_check_shippingid($shippingid, $orig_country)
{
    global $sql_tbl;

    $shipping = func_query_first("SELECT * FROM $sql_tbl[shipping] WHERE code = 'UPS' AND shippingid = '".$shippingid."'");
    if(empty($shipping))
        return false;

    $service_type = false;
    switch ($shipping['shipping']) {
        case 'UPS Express Plus':
        case 'UPS Worldwide Express Plus##SM##':
            $service_type = 'EP';
            break;
        case 'UPS Express':
        case 'UPS Worldwide Express##SM##':
            $service_type = 'ES';
            break;
        case 'UPS Express Saver##R##':
        case 'UPS Worldwide Express Saver##R##':
        case 'UPS Next Day Air Saver##R##':
        case 'UPS Saver':
            $service_type = '1DP';
            break;
        case 'UPS Expedited##SM##':
        case 'UPS Worldwide Expedited##SM##':
            $service_type = 'EX';
            break;
        case 'UPS Standard':
        case 'UPS Standard to Canada':
            $service_type = 'ST';
            break;
        case 'UPS 3 Day Select##R##':
            $service_type = '3DS';
            break;
        case 'UPS Next Day Air##R## Early A.M. ##SM##':
            $service_type = '1DM';
            break;
        case 'UPS Next Day Air##R##':
            $service_type = '1DA';
            break;
        case 'UPS 2nd Day Air A.M.##R##':
            $service_type = '2DM';
            break;
        case 'UPS 2nd Day Air##R##':
            $service_type = '2DA';
            break;
        case 'UPS Ground':
            $service_type = 'GND';
            break;
    }

    if (
        $orig_country == 'US' 
        || $orig_country == 'PR'
    ) {
        switch ($shipping['shipping']) {
            case 'UPS Worldwide Express Saver##R##':
            case 'UPS Worldwide Saver##R##':
                $service_type = 'SV';
                break;
        }
    }

    return $service_type;
}

/**
 * Get module and carrier name by shippingid
 */
function func_slg_get_module_info($shippingid)
{
    global $sql_tbl, $slg_modules;

    if (empty($shippingid)) {
        return false;
    }

    $code = func_query_first_cell("SELECT code FROM $sql_tbl[shipping] WHERE shippingid = '$shippingid'");
    if (!empty($slg_modules[$code])) {
        return array('carrier_code' => $code, 'slg_module' => $slg_modules[$code]);
    }

    return false;
}

/**
 * Fetch data for all orders with graphical labels at Shipping Labels page of x-cart admin/provider area.
 */
function func_slg_get_img_labels_orders_data()
{
    global $slg_img_orders;

    // Orders info is stored in a session variable slg_img_orders defined in
    // modules/Shipping_Label_Generator/generator.php
    if (empty($slg_img_orders) || !is_array($slg_img_orders)) {
        return null;
    }

    $img_orders = null;
    foreach ($slg_img_orders as $orderid) {
        $img_orders[$orderid]['labels'] = func_slg_get_labels($orderid, false);
    }

    return $img_orders;
}

/**
 * Prepares a CSV file with UPS shipping labels information for all
 * orders at Shipping Labels page of x-cart admin/provider area.
 */
function func_slg_get_ups_labels()
{
    global $xcart_dir, $slg_ups_orders;

    // UPS labels are stored in a session variable slg_ups_orders defined in
    // modules/Shipping_Label_Generator/generator.php
    if (empty($slg_ups_orders) || !is_array($slg_ups_orders)) {
        return null;
    }

    x_load('order');
    require_once $xcart_dir.'/modules/Shipping_Label_Generator/ups.php';

    $ups_labels = array('mime_type' => 'text/csv', 'error' => '', 'orderid' => 'slg_ups_orders', 'label' => '');
    foreach ($slg_ups_orders as $orderid) {
        $order_data = func_order_data($orderid);
        if (empty($order_data)) {
            continue;
        }
        // Skip UPS label headers for all orders but the first one.
        $extra_args = !empty($ups_labels['label']) ? array("skip_header" => true) : null;
        $response = func_slg_handler_UPS($order_data, $extra_args);
        if (!empty($response['label'])) {
            $ups_labels['label'][] = $response['label'];
        }
    }

    if (!empty($ups_labels['label'])) {
        $ups_labels['label'] = join("\n", $ups_labels['label']);
    }

    return $ups_labels;
}

/**
 * Compatibility. Try to fetch label data from xcart_order_extras table.
 */
function func_slg_compat_get_label($orderid, $fetch_label_content = true)
{
    global $sql_tbl;

    if (empty($orderid)) {
        return null;
    }

    $label_data = false;
    if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[order_extras] WHERE orderid = '".$orderid."' AND khash='shipping_label'") > 0) {
        $label_data = array(
            'mime_type' =>  func_query_first_cell("SELECT value FROM $sql_tbl[order_extras] WHERE orderid = '".$orderid."' AND khash='shipping_label_type'"),
            'error' => func_query_first_cell("SELECT value FROM $sql_tbl[order_extras] WHERE orderid = '".$orderid."' AND khash='shipping_label_error'"),
            'orderid' => $orderid
        );
        if ($fetch_label_content) {
            $label_data['label'] = func_query_first_cell("SELECT value FROM $sql_tbl[order_extras] WHERE orderid = '".$orderid."' AND khash='shipping_label'");
        }
    }

    return $label_data;
}

/**
 * Compatibility. Delete informatation about a label in old format from xcart_order_extras table.
 */
function func_slg_compat_del_label($orderid)
{
    global $sql_tbl;

    if (empty($orderid) || !is_numeric($orderid)) {
        return null;
    }

    if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[order_extras] WHERE orderid = '".$orderid."' AND khash='shipping_label'") > 0) {
        db_query("DELETE FROM $sql_tbl[order_extras] WHERE orderid = '".$orderid."' AND khash='shipping_label'");
        db_query("DELETE FROM $sql_tbl[order_extras] WHERE orderid = '".$orderid."' AND khash='shipping_label_type'");
        db_query("DELETE FROM $sql_tbl[order_extras] WHERE orderid = '".$orderid."' AND khash='shipping_label_error'");
    }

    return true;
}

/**
 * Retrieve a shipping label information.
 */
function func_slg_get_label($orderid, $labelid = null, $fetch_label_content = true)
{
    global $sql_tbl;

    if (!empty($labelid)) {
        return func_query_first("SELECT * FROM $sql_tbl[shipping_labels] WHERE labelid = '".$labelid."'");
    }

    $fetch_fields = array('labelid', 'mime_type', 'error');
    if ($fetch_label_content) {
        $fetch_fields[] = 'label';
    }
    $label_data = func_query_first("SELECT " . join(", ", $fetch_fields) . " FROM $sql_tbl[shipping_labels] WHERE orderid = '".$orderid."' ORDER BY labelid");

    // Compatibility scheme. Try to fetch label data from xcart_order_extras table.
    if (empty($label_data)) {
        $label_data = func_slg_compat_get_label($orderid, $fetch_label_content);
    }

    return $label_data;
}

/**
 * Retrieve all shipping labels for an order.
 */
function func_slg_get_labels($orderid, $fetch_label_content = true)
{
    global $sql_tbl;

    $fetch_fields = array('labelid', 'mime_type', 'error', 'orderid', 'descr', 'packages_number', 'is_first');
    if ($fetch_label_content) {
        $fetch_fields[] = 'label';
    }
    $labels_data = func_query_hash("SELECT " . join(", ", $fetch_fields) . " FROM $sql_tbl[shipping_labels] WHERE orderid = '".$orderid."' ORDER BY labelid", 'labelid', false);

    // Compatibility scheme. Try to fetch label data from xcart_order_extras table.
    if (empty($labels_data)) {
        if ($label = func_slg_compat_get_label($orderid, $fetch_label_content)) {
            $labels_data = array($label);
        }
    }

    return $labels_data;
}

/**
 * Delete information about labels with specified label IDs
 */
function func_slg_delete_labels($labelids)
{
    global $sql_tbl;

    if (empty($labelids) || !is_array($labelids)) {
        return null;
    }

    return db_query("DELETE FROM $sql_tbl[shipping_labels] WHERE labelid IN ('".join("', '", $labelids)."')");
}

/**
 * Update information about an order shipping labels
 */
function func_slg_update_order_label($order_data)
{
    global $xcart_dir, $config;

    if (empty($order_data) || !isset($order_data['order']) || !isset($order_data['order']['orderid']) || !is_numeric($order_data['order']['orderid'])) {
        return null;
    }

    $slg_module_info = func_slg_get_module_info($order_data['order']['shippingid']);
    if (empty($slg_module_info)) {
        return null;
    }

    if (file_exists($xcart_dir.'/modules/Shipping_Label_Generator/'.$slg_module_info['slg_module'])) {
        require_once $xcart_dir.'/modules/Shipping_Label_Generator/'.$slg_module_info['slg_module'];
    }

    $_pack_index = 'ship_packages_uniq' . $slg_module_info['carrier_code'];
    $orders_packages = array($order_data);
    if (!empty($order_data['order']['extra'][$_pack_index])) {
        $_packages = @unserialize($order_data['order']['extra'][$_pack_index]);

        // Emulate products in order_data array
        if (is_array($_packages) && !empty($_packages)) {
            $orders_packages = array();
            $lbl_price = func_get_langvar_by_name('lbl_price');
            $lbl_pack = func_get_langvar_by_name('lbl_pack');
            foreach($_packages as $v) {
                if (!isset($v['package']))
                    continue;

                // Form label descr
                $v['package']['package_descr'] = $lbl_pack . '(' . $v['package']['weight'] . ') ' . $v['package']['length'] . 'x' . $v['package']['width'] . 'x' . $v['package']['height'];

                if (isset($v['package']['price']))
                    $v['package']['package_descr'] .= " $lbl_price " . str_replace('x', func_format_number($v['package']['price']), $config['General']['currency_format']);

                $v['package']['amount'] = 1;
                $v['package']['packages_number'] = $v['packages_number'];
                $order_data['products'] = array($v['package']);
                $order_data['order']['subtotal'] = $v['package']['price'];
                $orders_packages[] = $order_data;
            }

            if (empty($orders_packages))
                $orders_packages = array($order_data);
        }
    }

    $func_slg_handler = 'func_slg_handler_'.$slg_module_info['carrier_code'];

    $responses = array();
    if (function_exists($func_slg_handler)) {
        foreach($orders_packages as $order_package)
            $responses[] = $func_slg_handler($order_package);
    }

    return func_slg_store_labels_info($order_data['order']['orderid'], $responses);
}

/**
 * Store shipping labels information.
 */
function func_slg_store_labels_info($orderid, $responses)
{
    global $sql_tbl;

    if (!is_array($responses) || empty($orderid))
        return null;

    $labelids = array();
    $old_labelids = func_query_column("SELECT labelid FROM $sql_tbl[shipping_labels] WHERE orderid = '".$orderid."'");
    $lbl_label = func_get_langvar_by_name('lbl_label');

    foreach($responses as $response) {
        if (empty($response) || !isset($response['label']) || !isset($response['mime_type']) || !isset($response['error'])) {
            continue;
        }

        $label_data = array(
            'orderid' => $orderid,
            'mime_type' => $response['mime_type'],
            'packages_number' => intval($response['packages_number']),
            'error' => $response['error']
        );
        $descr = $response['descr'];

        if (!is_array($response['label'])) {
            $response['label'] = array($response['label']);
        }

        foreach ($response['label'] as $k => $l) {
            $label_data['label'] = $l;
            if (empty($label_data['label']) && empty($label_data['error'])) {
                $label_data['error'] = func_get_langvar_by_name("lbl_shipping_label_error_empty_label", false, false, true);
            }
            $label_data = func_array_map('func_addslashes', $label_data);

            if ($k == 0) {
                $label_data['descr'] = $descr;
                $label_data['is_first'] = 'Y';
                $k++;
                if (count($response['label']) > 1)
                    $label_data['descr'] .= " $lbl_label#" . $k;
            } else {
                // Create descr for addional images
                $k++;
                $label_data['descr'] = str_repeat('&nbsp;.', intval(strlen($descr) / 2)) . " $lbl_label#" . $k;
                $label_data['is_first'] = '';
            }
            if ($labelid = func_array2insert('shipping_labels', $label_data)) {
                $labelids[] = $labelid;
            }
            unset($label_data['label']);
        }

    } // foreach($responses as $response) {

    if (!empty($labelids)) {
        if (!empty($old_labelids)) {
            func_slg_delete_labels($old_labelids);
        }
        // Compatibility. Delete label info from xcart_order_extras table.
        func_slg_compat_del_label($orderid);
    }

    return $labelids;
}

/**
 * Get labels information for orders with specified order IDs
 */
function func_slg_get_orders_labels_data($orderids, $orderids_update, $mode)
{
    global $sql_tbl, $slg_ups_orders, $slg_img_orders;

    x_load('order');

    $orders = array();
    $slg_ups_orders = array();
    $slg_img_orders = array();
    foreach ($orderids as $orderid => $v) {
        $need_update = false;

        // Update labels data if that was explicity requested.
        if ($mode == 'update' && isset($orderids_update[$orderid]) && $orderids_update[$orderid] == 'Y') {
            $need_update = true;
        }

        $order_data = func_order_data($orderid);
        if (empty($order_data)) {
            continue;
        }

        $shipping_carrier_code = func_query_first_cell("SELECT code FROM $sql_tbl[shipping] WHERE shippingid = '".$order_data['order']['shippingid']."'");
        // Collect information about UPS orders
        if ($shipping_carrier_code == 'UPS') {
            $slg_ups_orders[] = $orderid;
        }

        // fetch labels information without actual label content information
        $order_data['labels'] = func_slg_get_labels($orderid, false);
        if (empty($order_data['labels'])) {
            // Update labels data if we do not have labels yet.
            $need_update = true;
        }

        if ($need_update) {
            // Update labels information
            func_slg_update_order_label($order_data);

            // fetch labels information without actual label content information
            $order_data['labels'] = func_slg_get_labels($orderid, false);
        }

        if (!empty($order_data['labels']) && is_array($order_data['labels'])) {
            $lbl = current($order_data['labels']);
            // Add an order to the list of orders with graphic labels if a label's MIME type belongs to the list of image MIME types
            // which can be shown in a document body.
            if (!empty($lbl) && isset($lbl['mime_type']) && in_array(strtolower($lbl['mime_type']), array("image/png", "image/jpeg", "image/gif"))) {
                $slg_img_orders[] = $orderid;
            }
        }
        $orders[$orderid] = $order_data;
    }

    return $orders;
}

function func_slg_value_normalize($value, $delimiter, $force_quote = false)
{
    $value = preg_replace("/\r\n|\n|\r/Ss", " ", $value);
    if ($force_quote || @preg_match("/(".preg_quote($delimiter, '/').")|\t/S", $value)) {
        $value = '"'.str_replace('"', '""', $value).'"';
        if (substr($value, -2) == '\"' && preg_match('/[^\\\](\\\+)"$/Ss', $value, $preg) && strlen($preg[1]) % 2 != 0) {
            $value = substr($value, 0, -2)."\\".substr($value, -2);
        }
    }

    return $value;
}

?>
