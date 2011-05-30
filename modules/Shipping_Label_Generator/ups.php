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
 * UPS - WorldShip/TrueShip module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: ups.php,v 1.25.2.1 2011/01/10 13:12:01 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

/**
 * Format UPS shipping label
 */
function func_slg_handler_UPS($order, $extra_args = null)
{
    global $single_mode, $config;


    $response = array('label' => '', 'mime_type' => '', 'error' => '');

    if (empty($order) || empty($order['products'])) {
        $response['error'] = func_get_langvar_by_name("lbl_slg_order_no_products", false, false, true);
        return $response;
    }

    if (!$single_mode) {
        $orig_country = func_query_first_cell("SELECT country FROM $sql_tbl[seller_addresses] WHERE userid='{$order['products'][0]['provider']}'");
        if (empty($orig_country)) 
            $orig_country = $config['Company']['location_country'];

    } else {
        $orig_country = $config['Company']['location_country'];
    }

    $stype = func_ups_check_shippingid($order['order']['shippingid'], $orig_country);
    if (!$stype) {
        $response['error'] = func_get_langvar_by_name("lbl_shipping_label_error",false,false,true). " (shippingid: " . $order['order']['shippingid'] . ")";
        return $response;
    }

    $delimiter = $config['Shipping_Label_Generator']['ups_csv_delimiter'];
    if ($delimiter == 'tab') {
        $delimiter = "\t";
    }

    $p_head = array();
    $strs = array();

    $_name = '';
    $_areas = array('', 'b_', 's_');
    foreach ($_areas as $_a) {
        $_userinfo = $order['userinfo'];
        if ($_userinfo[$_a.'firstname'] != '' || $_userinfo[$_a.'lastname'] != '')
            $_name = $_userinfo[$_a.'firstname'] . " " . $_userinfo[$_a.'lastname'];
    }

    $hash = array();
    $hash['OrderId'] = $order['order']['orderid'];
    $hash['ShipmentInformation_ServiceType'] = $stype;
    $hash['ShipmentInformation_BillingOption'] = 'Prepaid';
    $hash['ShipmentInformation_QvnOption'] = 'Y';
    $hash['ShipmentInformation_QvnShipNotification1Option'] = 'Y';
    $hash['ShipmentInformation_NotificationRecipient1Type'] = 'E-mail';
    $hash['ShipmentInformation_NotificationRecipient1FaxorEmail'] = $order['userinfo']['email'];
    $hash['ShipTo_CompanyOrName'] = $_name;
    $hash['ShipTo_StreetAddress'] = $order['userinfo']['s_address'];
    $hash['ShipTo_RoomFloorAddress2'] = $order['userinfo']['s_address_2'];
    $hash['ShipTo_City'] = $order['userinfo']['s_city'];
    $hash['ShipTo_State'] = $order['userinfo']['s_state']; 
    $hash['ShipTo_Country'] = $order['userinfo']['s_country'];
    $hash['ShipTo_ZipCode'] = $order['userinfo']['s_zipcode'];
    $hash['ShipTo_Telephone'] = $order['userinfo']['s_phone'];
    $hash['ShipTo_ResidentialIndicator'] = 'Y';
    $hash['Package_PackageType'] = 'CP';
    $hash['Package_Reference1'] = func_get_langvar_by_name('lbl_order_id', false, false, true) . ':' . $order['order']['orderid']; 
    $hash['Package_Reference2'] = $order['order']['tracking'];
    $hash['Package_Reference3'] = '';
    $hash['Package_Reference4'] = '';
    $hash['Package_Reference5'] = '';
    $hash['Package_DeclaredValueOption'] = 'Y';
    $hash['Package_DeclaredValueAmount'] = $order['order']['subtotal'];
    $hash['ShipTo_LocationID'] = substr($config['Shipping_Label_Generator']['ups_shipto_locationid'], 0, 10);

    $hash['Package_Weight'] = 0;
    if (in_array($hash['s_country'], array("DO","PR","US"))) {
        $UPS_wunit = 'LBS';
    } else {
        $UPS_wunit = 'KGS';
    }
    if (!empty($order['products'])) {
        foreach($order['products'] as $p) {
            $hash['Package_Weight'] += $p['weight']*$p['amount'];
        }
        $hash['Package_Weight'] = max(0.1, func_units_convert(func_weight_in_grams($hash['weight']), "g", (($UPS_wunit=="LBS") ? "lbs" : "kg"), 1));
    }
    foreach ($hash as $k=>$v) {
        $hash[$k] = func_slg_value_normalize($v, $delimiter, true);
    }
    $strs[] = implode($delimiter,$hash);

    // Create header
    $header = implode($delimiter,func_array_merge(array_keys($hash), $p_head))."\n";
    if (isset($extra_args) && isset($extra_args['skip_header'])) {
        $header = '';
    }

    // Create response
    $response = array(
        'label' => $header.implode("\n", $strs),
        'mime_type' => 'text/csv',
        'error' => ''
    );

    return $response;
}

?>
