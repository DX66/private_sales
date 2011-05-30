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
 * Functions for X-Payments Connector module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: xpc_func.php,v 1.45.2.12 2011/01/25 09:43:13 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

if (!function_exists('x_load')) {
function x_load()
{
    return true;
}
}

if (!function_exists('func_query_column')) {
function func_query_column($query, $column = 0) 
{
    $result = array();

    $fetch_func = is_int($column)
        ? 'db_fetch_row'
        : 'db_fetch_array';

    if ($p_result = db_query($query)) {

        while ($row = $fetch_func($p_result))
            $result[] = $row[$column];

        db_free_result($p_result);
    }

    return $result;
}
}

x_load('payment');

global $config;

/**
 *  Some XP errors details which depend on the current XP connector module realization.
 */
global $xpc_errors;

$xpc_errors = array(
    XPC_API_EXPIRED => func_get_langvar_by_name('txt_xpc_api_expired'),
);

if (
    !function_exists('func_store_advinfo')
    && preg_match("/^4\.2\./", $config['version']) == 0
    && preg_match("/^4\.3\./", $config['version']) == 0
    && preg_match("/^4\.4\./", $config['version']) == 0
) {
function func_store_advinfo($orderid, $advinfo)
{
    global $sql_tbl;

    $orderid = intval($orderid);

    if ( 
        $advinfo
        && func_query_first_cell("SELECT orderid FROM $sql_tbl[orders] WHERE orderid='$orderid'")
    ) {  
        $prev_advinfo = text_decrypt(func_query_first_cell("SELECT value FROM $sql_tbl[order_extras] WHERE orderid='$orderid' AND khash='advinfo'"));

        $order_extras = array(
            'orderid' => $orderid,
            'khash'   => 'advinfo',
            'value'   => addslashes(text_crypt($prev_advinfo . "\n--- Advanced info ---\n" . $advinfo))
        );   

        func_array2insert('order_extras', $order_extras, true);

    }    
}
}

// Salt block length
define('XPC_SALT_LENGTH', 32);

// Salt generator start character code
define('XPC_SALT_BEGIN', 33);

// Salt generator end character code
define('XPC_SALT_END', 255);

// Encryption check length
define('XPC_CHUNK_LENGTH', 128);

define('XPC_NEW_ACTION', 1);
define('XPC_AUTH_ACTION', 2);
define('XPC_CHARGED_ACTION', 4);
define('XPC_DECLINED_ACTION', 3);
define('XPC_REFUND_ACTION', 5);
define('XPC_PART_REFUND_ACTION', 6);

/****************************************** Allowable transactions *******************************************/

define('XPC_TRAN_TYPE_SALE',          'sale');
define('XPC_TRAN_TYPE_AUTH',          'auth');
define('XPC_TRAN_TYPE_CAPTURE',       'capture');
define('XPC_TRAN_TYPE_CAPTURE_PART',  'capturePart');
define('XPC_TRAN_TYPE_CAPTURE_MULTI', 'captureMulti');
define('XPC_TRAN_TYPE_VOID',          'void');
define('XPC_TRAN_TYPE_VOID_PART',     'voidPart');
define('XPC_TRAN_TYPE_VOID_MULTI',    'voidMulti');
define('XPC_TRAN_TYPE_REFUND',        'refund');
define('XPC_TRAN_TYPE_REFUND_PART',   'refundPart');
define('XPC_TRAN_TYPE_REFUND_MULTI',  'refundMulti');
define('XPC_TRAN_TYPE_GET_INFO',      'getInfo');
define('XPC_TRAN_TYPE_ACCEPT',        'accept');
define('XPC_TRAN_TYPE_DECLINE',       'decline');
define('XPC_TRAN_TYPE_TEST',          'test');

/****************************** Common definitions for XML requests / responses ******************************/

// Root-level tag for all XML messages
define('XPC_TAG_ROOT', 'data');

// Value of the 'type' attribute for list items in XML
define('XPC_TYPE_CELL', 'cell');

define('XPC_MODULE_INFO', 'payment_module');

// Include connection API essential functions
include $xcart_dir . '/modules/XPayments_Connector/xpc_api.php';

/*********************************************** XML routines ************************************************/

/**
 * Check if passed variable is an array with numeric keys
 *
 * @param mixed $data data to check
 *
 * @return bool
 */
function xpc_is_anonymous_array($data)
{
    return (is_array($data) && (1 > count(preg_grep('/^\d+$/', array_keys($data), PREG_GREP_INVERT))));
}

/**
 * Write XML tag for current level
 *
 * @param mixed  $data  node content
 * @param string $name  node name
 * @param int    $level current recursion level
 * @param string $type  value for 'type' attribute
 *
 * @return string
 */
function xpc_write_xml_tag($data, $name, $level = 0, $type = '')
{
    $xml    = '';
    $indent = str_repeat('  ', $level);

    // Open tag
    $xml .= $indent . '<' . $name . (empty($type) ? '' : ' type="' . $type . '"') . '>';
    // Sublevel tags or tag value
    $xml .= is_array($data) ? "\n" . xpc_hash2xml($data, $level + 1) . $indent : xpc_local2utf8($data);
    // Close tag
    $xml .= '</' . $name . '>' . "\n";

    return $xml;
}

/**
 * Convert hash array to XML
 *
 * @param array $data  hash array
 * @param int   $level current recursion level
 *
 * @return string
 */
function xpc_hash2xml($data, $level = 0)
{
    $xml = '';

    foreach ($data as $name => $value) {

        if (xpc_is_anonymous_array($value)) {
            foreach ($value as $item) {
                $xml .= xpc_write_xml_tag($item, $name, $level, XPC_TYPE_CELL);
            }
        } else {
            $xml .= xpc_write_xml_tag($value, $name, $level);
        }

    }

    return $xml;
}

/**
 * Convert XML to hash array
 *
 * @param string $xml XML string
 *
 * @return array|string
 * @access private
 * @see    ____func_see____
 * @since  1.0.0
 */
function xpc_xml2hash($xml)
{
    $data = array();

    while (!empty($xml) && preg_match('/<([\w\d]+)(?:\s*type=["\'](\w+)["\']\s*)?>(.*)<\/\1>/Us', $xml, $matches)) {

        // Sublevel tags or tag value
        if (XPC_TYPE_CELL === $matches[2]) {
            $data[$matches[1]][] = xpc_xml2hash($matches[3]);
        } else {
            $data[$matches[1]] = xpc_xml2hash($matches[3]);
        }

        // Exclude parsed part from XML
        $xml = str_replace($matches[0], '', $xml);

    }

    return empty($data) ? $xml : $data;
}

/************************************** Import / export payment methods **************************************/

/**
 * Get list of available payment configurations from X-Payments
 *
 * @return array
 */
function xpc_request_get_payment_methods()
{
    $result = array();

    // Call the "api.php?target=payment_confs&action=get" URL
    list($status, $response) = xpc_api_request(
        'payment_confs',
        'get',
        array(),
        xpc_request_get_payment_methods_schema()
    );

    // Check status
    if ($status) {
        if (!isset($response['payment_module']) || !is_array($response['payment_module'])) {
            $status = array();

        } else {
            $status = $response['payment_module'];
        }
    }

    return $status;
}

/**
 * Import payment methods to the database
 *
 * @param array $methods_list payment methods list
 *
 * @return bool
 */
function xpc_import_payment_methods($methods_list)
{
    global $sql_tbl, $recent_payment_methods, $config;

    $result = false;

    if (!empty($methods_list) && is_array($methods_list)) {

        $result = true;

        // Remove all payment processors imported from X-Payments earlier
        // and all associated with them payment methods
        db_query('DELETE FROM ' . $sql_tbl['ccprocessors'] . ' WHERE processor=\'cc_xpc.php\'');
        db_query('DELETE FROM ' . $sql_tbl['payment_methods'] . ' WHERE processor_file=\'cc_xpc.php\'');

        x_session_register('recent_payment_methods');

        // Remove X-Payments payment methods from recently added payment methods list
        if (!empty($recent_payment_methods) && is_array($recent_payment_methods)) {

            foreach ($recent_payment_methods as $key => $value) {
                if (preg_match('/cc_xpc\.php/S', $key)) {
                    unset($recent_payment_methods[$key]);
                }
            }

        }

        // Translate boolean value into the {Y,N} char
        function getTranTypeFlag($tranTypes, $type)
        {
            return (!isset($tranTypes[$type]) || !$tranTypes[$type]) ? 'N' : 'Y';
        }

        // Save payment processors imported from X-Payments
        foreach ($methods_list as $method) {

            $tranTypes       = $method['transactionTypes'];
            $authCaptureInfo = $method['authCaptureInfo'];

            // Prepare data to insert to the database
            $data = array(
                'module_name'       => 'X-Payments: ' . $method['name'],
                'type'              => 'X',
                'processor'         => 'cc_xpc.php',
                'template'          => 'cc_xpc.tpl',
                'param01'           => $method['id'],
                'param02'           => getTranTypeFlag($tranTypes, XPC_TRAN_TYPE_CAPTURE),
                'param03'           => getTranTypeFlag($tranTypes, XPC_TRAN_TYPE_VOID),
                'param04'           => getTranTypeFlag($tranTypes, XPC_TRAN_TYPE_REFUND),
                'param05'           => getTranTypeFlag($tranTypes, XPC_TRAN_TYPE_REFUND_PART),
                'param06'           => getTranTypeFlag($tranTypes, XPC_TRAN_TYPE_SALE),
                'param07'           => getTranTypeFlag($tranTypes, XPC_TRAN_TYPE_ACCEPT) 
                    . getTranTypeFlag($tranTypes, XPC_TRAN_TYPE_DECLINE)
                    . getTranTypeFlag($tranTypes, XPC_TRAN_TYPE_GET_INFO),
                'param08'           => $method['moduleName'],
                'param09'           => $method['settingsHash'],
                'disable_ccinfo'    => 'Y',
                'background'        => 'N',
            );

            if (
                preg_match("/^4\.0\./", $config['version']) == 0
                && preg_match("/^4\.1\./", $config['version']) == 0
            ) {
                $data = array_merge(
                    $data,
                    array(
                        'is_refund'         => getTranTypeFlag($tranTypes, XPC_TRAN_TYPE_REFUND),
                        'has_preauth'       => getTranTypeFlag($tranTypes, XPC_TRAN_TYPE_AUTH),
                        'preauth_expire'    => $authCaptureInfo['authExp'],
                    )
                );

                if (preg_match("/^4\.2\./", $config['version']) == 0) {
                    $data = array_merge(
                        $data,
                        array(
                            'capture_min_limit' => ($authCaptureInfo['captMinLimit'] * 100) . '%', 
                            'capture_max_limit' => ($authCaptureInfo['captMaxLimit'] * 100 - 100) . '%', 
                        )
                    );
                }
            }

            $id = func_array2insert('ccprocessors', $data);

            $result = ($result && (false !== $id));
        }
    }

    return $result;
}



/***************************************** Functions to init payment *****************************************/

/**
 * Send request to X-Payments to initialize new payment
 *
 * @param int     $paymentid  X-Cart initernal ID for payment method
 * @param string  $refId      order ID
 * @param array   $cart       shopping cart info
 * @param boolean $force_auth Force enable AUTH mode
 *
 * @return array
 * @see    ____func_see____
 * @since  1.0.0
 */
function xpc_request_payment_init($paymentid, $refId, $cart, $force_auth)
{
    global $config, $XCARTSESSID, $current_location, $shop_language;

    // Retrieve module settings
    $module_params = xpc_get_module_params($paymentid);

    if (!$module_params) {
        return xpc_api_error('Unable to retrieve payment module settings');
    }

    // Prepare cart
    $cart = xpc_prepare_cart($cart, $refId, $force_auth);

    if (!$cart) {
        return xpc_api_error('Unable to prepare cart data');
    }

    // Data to send to X-Payments
    $data = array(
        'confId'      => intval($module_params['param01']),
        'refId'       => $refId,
        'cart'        => $cart,
        'returnUrl'   => $current_location . '/payment/cc_xpc.php',
        'callbackUrl' => $current_location . '/payment/cc_xpc.php',
        'language'    => 'en', //$shop_language
    );

    list($status, $response) = xpc_api_request('payment', 'init', $data, xpc_request_payment_init_schema());

    // The main entry in the response is the 'token'
    if (
        $status 
        && (
            !isset($response['token']) 
            || !is_string($response['token'])
        )
    ) {

        xpc_api_error('Transaction token is not found or has a wrong type');

        $status = false;

    }

    if ($status) {

        // Use the default URL if X-Payments did not return one
        if (substr($config['XPayments_Connector']['xpc_xpayments_url'], -1) == '/') {
            $config['XPayments_Connector']['xpc_xpayments_url'] = substr($config['XPayments_Connector']['xpc_xpayments_url'], 0, -1);
        }

        // Set fields for the "Redirect to X-Payments" form
        $response = array(
            'txnId'       => $response['txnId'],
            'module_name' => $module_params['module_name'],
            'url'         => $config['XPayments_Connector']['xpc_xpayments_url'] . '/payment.php',
            'fields'      => array(
                'target' => 'main',
                'action' => 'start',
                'token'  => $response['token'],
            ),
        );

    } else {

        $response = array(
            'detailed_error_message' => $response['error_message'],
        );

    }

    return array($status, $response);
}

/**
 * Prepare shopping cart data
 *
 * @param array  $cart  X-Cart shopping cart
 * @param string $refId order ID
 *
 * @return array
 * @see    ____func_see____
 * @since  1.0.0
 */
function xpc_prepare_cart($cart, $refId, $force_auth)
{
    global $config, $sql_tbl, $active_modules;

    $user_info = $cart['userinfo'];

    $result = array(
        'login'                => $user_info['login'] . ' (User ID #' . $user_info['id'] . ')',
        'billingAddress'       => array(),
        'shippingAddress'      => array(),
        'items'                => array(),
        'currency'             => xpc_get_currency($refId),
        'shippingCost'         => 0.00,
        'taxCost'              => 0.00,
        'discount'             => 0.00,
        'totalCost'            => 0.00,
        'description'          => 'Order(s) #' . $refId,
        'merchantEmail'        => $config['Company']['orders_department'],
        'forceTransactionType' => $force_auth ? 'A' : '',
    );

    $name_prefixes  = array(
        'b_' => 'billing', 
        's_' => 'shipping',
    );

    $address_fields = array(
        'firstname', 
        'lastname', 
        'address', 
        'city', 
        'state', 
        'country', 
        'zipcode', 
        'phone', 
        'fax',
    );

    // Prepare shipping and billing address
    foreach ($name_prefixes as $prefix => $type) {

        $addressIndex = $type . 'Address';

        foreach ($address_fields as $field) {

            $result[$addressIndex][$field] = (
                isset($user_info[$prefix . $field])
                && !empty($user_info[$prefix . $field])
            )
                ? $user_info[$prefix . $field] 
                : (
                    !empty($user_info[$field]) 
                        ? $user_info[$field] 
                        : ''
                );

        }

        foreach (array('company', 'email') as $field) {
            $result[$addressIndex][$field] = isset($user_info[$field]) ? $user_info[$field] : '';
        }

    }

    // Set products
    if (!empty($cart['products']) && is_array($cart['products'])) {

        foreach ($cart['products'] as $product) {
            $result['items'][] = array(
                'sku'      => $product['productcode'],
                'name'     => $product['product'],
                'price'    => $product['price'],
                'quantity' => $product['amount'],
            );
        }

    }

    // Set giftcerts
    if (!empty($cart['giftcerts']) && is_array($cart['giftcerts'])) {

        foreach ($cart['giftcerts'] as $giftcert) {
            $result['items'][] = array(
                'sku'      => $giftcert['gcid'],
                'name'     => 'Gift certificate',
                'price'    => $giftcert['amount'],
                'quantity' => 1,
            );
        }

    }

    // Set costs
    $result['shippingCost'] = round($cart['shipping_cost'], 2);
    $result['taxCost']      = round($cart['tax_cost'], 2);
    $result['totalCost']    = round($cart['total_cost'], 2);
    $result['discount']     = round($cart['discount'], 2);

    // Get admin email if Orders department email is empty
    if (empty($result['merchantEmail'])) {

        x_load('user');

        $usertypes = array('A');

        if (!empty($active_modules['Simple_Mode'])) {
            $usertypes[] = 'P';
        }

        $admin_email = func_query_first_cell("SELECT email FROM $sql_tbl[customers] WHERE usertype IN ('" . implode("', '", $usertypes). "') AND status = 'Y' AND email != ''");

        $result['merchantEmail'] = $admin_email;

    }

    return $result;
}

/**
 * Get currency code (alpha-3)
 *
 * @param string $ref_id Reference id
 *
 * @return string Currency code
 * @see    ____func_see____
 * @since  1.0.0
 */
function xpc_get_currency($ref_id)
{
    global $config;

    return $config['XPayments_Connector']['xpc_currency'];
}

/***************************************** Functions to init payment *****************************************/

/**
 * Get payment info
 *
 * @param string $txn_id      Transaction id
 *
 * @return array Operation status & payment data array
 */
function xpc_request_get_payment_info($txn_id, $refresh = false)
{
    $data = array(
        'txnId' => $txn_id,
        'refresh' => $refresh ? 1 : 0
    );

    list($status, $response) = xpc_api_request('payment', 'get_info', $data);

    if ($status) {
        if (!is_array($response) || !isset($response['status'])) {
            xpc_api_error('GetInfo request. Server response has not status');
            $status = false;

        } elseif (!isset($response['message'])) {
            xpc_api_error('GetInfo request. Server response has not message');
            $status = false;

        } elseif (!isset($response['transactionInProgress'])) {
            xpc_api_error('GetInfo request. Server response has not transaction progress status');
            $status = false;

        } elseif (!isset($response['isFraudStatus'])) {
            xpc_api_error('GetInfo request. Server response has not fraud filter status');
            $status = false;

        } elseif (!isset($response['currency']) || strlen($response['currency']) != 3) {
            xpc_api_error('GetInfo request. Server response has not currency code or currency code has wrong format');
            $status = false;

        } elseif (!isset($response['amount'])) {
            xpc_api_error('GetInfo request. Server response has not payment amount');
            $status = false;

        } elseif (!isset($response['capturedAmount'])) {
            xpc_api_error('GetInfo request. Server response has not captured amount');
            $status = false;

        } elseif (!isset($response['capturedAmountAvail'])) {
            xpc_api_error('GetInfo request. Server response has not available for capturing amount');
            $status = false;

        } elseif (!isset($response['refundedAmount'])) {
            xpc_api_error('GetInfo request. Server response has not refunded amount');
            $status = false;

        } elseif (!isset($response['refundedAmountAvail'])) {
            xpc_api_error('GetInfo request. Server response has not available for refunding amount');
            $status = false;

        } elseif (!isset($response['voidedAmount'])) {
            xpc_api_error('GetInfo request. Server response has not voided amount');
            $status = false;

        } elseif (!isset($response['voidedAmountAvail'])) {
            xpc_api_error('GetInfo request. Server response has not available for cancelling amount');
            $status = false;

        }
    }

    return array($status, $response);
}

/**
 * Update local payment info based on callback request
 *
 * @param string $txn_id      Transaction id
 * @param array  $update_data Callback Transaction id
 *
 * @return array Operations status & message
 */
function xpc_update_payment($txn_id, $update_data)
{

    // Check txn id
    $orderids = xpc_get_order_ids($txn_id);

    if (!$orderids) {
        return xpc_api_error('Process callback data. Transaction id is not found');
    }

    // Check update_data
    if (!is_array($update_data)) {

        return xpc_api_error('Process callback data. Callback data is not array');

    } elseif (
        !isset($update_data['status']) 
        || !is_numeric($update_data['status'])
    ) {

        return xpc_api_error('Process callback data. Status cell is not found or is not numeric');

    }

    $status = xpc_process_get_info($update_data, $orderids);

    if ($status) {

        if (!defined('STATUS_CHANGE_REF')) {
            define('STATUS_CHANGE_REF', 13);
        }

        foreach ($orderids as $orderid) {
            func_change_order_status($orderid, $status);
        }

    }

    return array(true, '');
}

function xpc_process_get_info($update_data, $orderids)
{
    global $sql_tbl;

    x_load('order');

    $order = func_order_data($orderids[0]);

    $capture_total_updated = false;

    switch ($update_data['status']) {

        case XPC_NEW_ACTION:
            break;

        case XPC_AUTH_ACTION:
            break;

        case XPC_CHARGED_ACTION:
            if (
                function_exists('func_order_is_authorized')
                && func_order_is_authorized($orderids[0])
            ) {
                if (
                    !isset($update_data['capturedAmount']) 
                    || !is_numeric($update_data['capturedAmount'])
                ) {
                    return xpc_api_error('Process callback data. Capture amount cell is not found or is not numeric');
                }

                func_order_process_capture($orderids, $update_data['capturedAmount']);

                $capture_total_updated = true;
            }

            break;

        case XPC_DECLINED_ACTION:
            if (
                function_exists('func_order_is_authorized')
                && func_order_is_authorized($orderids[0])
            ) {
                func_order_process_void($orderids);
            }

            break;

        case XPC_REFUND_ACTION:
        case XPC_PART_REFUND_ACTION:
            if (
                !isset($update_data['refundedAmount']) 
                || !is_numeric($update_data['refundedAmount'])
            ) {
                return xpc_api_error('Process callback data. Refund amount cell is not found or is not numeric');
            }

            break;
    }

    // Auto-accept or decline
    if (
        function_exists('func_order_process_decline')
        && isset($order['order']['fmf']) 
        && $order['order']['fmf']['blocked'] 
        && !$update_data['isFraudStatus']
    ) {

        if (XPC_DECLINED_ACTION == $update_data['status']) {

            func_order_process_decline($orderids);

        } else {

            func_order_process_accept($orderids);

        }

    }

    // Update captured amount
    if (
        !$capture_total_updated
        && $update_data['capturedAmount'] > 0
        && $order['extra']['captured_total'] != $update_data['capturedAmount']
    ) {
        foreach ($orderids as $orderid) {

            $extra = func_query_first_cell("SELECT extra FROM $sql_tbl[orders] WHERE orderid = '" . $orderid . "'");

            if ($extra) {
                $extra = unserialize($extra);
            }

            $extra['captured_total'] = doubleval($update_data['capturedAmount']);

            func_array2update(
                'orders',
                array('extra' => addslashes(serialize($extra))),
                "orderid = '" . $orderid . "'"
            );
        }
    }

    // Update refunded amount
    if (
        function_exists('func_order_process_refund')
        && $update_data['refundedAmount'] > 0
        && (!isset($order['order']['charge_info']) || $order['order']['charge_info']['refunded_total'] != $update_data['refundedAmount'])
    ) {

        db_query("DELETE FROM $sql_tbl[order_extras] WHERE khash = 'refunded_total' AND orderid IN ('" . implode("','", $orderids) . "')");

        func_order_process_refund($orderids, $update_data['refundedAmount'], array('xpc_txnid' => $txn_id));

    }

    // Update refund available amount
    if (isset($update_data['refundedAmountAvailGateway'])) {
        foreach ($orderids as $orderid) {
            func_array2insert(
                'order_extras',
                array(
                    'orderid' => $orderid,
                    'khash' => 'refund_avail',
                    'value' => $update_data['refundedAmountAvailGateway']
                ),
                true
            );
        }
    }

    xpc_update_advinfo($update_data, $orderids);

    return xpc_get_order_status_by_action($update_data['status']);
}

/**
 * Update advinfo field for specific orders
 *
 * @param array $data     response data array
 * @param array $orderids orderids array
 *
 * @return void
 * @see    ____func_see____
 * @since  1.0.0
 */
function xpc_update_advinfo($data, $orderids)
{

    if (isset($data['advinfo'])) {

        $advinfo = '';

        foreach ($data['advinfo'] as $name => $value) {

            $advinfo .= "\n" . $name . ':' . $value;

        }

        foreach ($orderids as $orderid) {

            func_store_advinfo($orderid, $advinfo);

        }
    }

}

/**
 * Get additional payment info
 *
 * @param string $orderid Order id
 *
 * @return array Operation status & payment data array
 */
function xpc_request_get_additional_info($orderid)
{
    $order = func_order_data($orderid);
    $txn_id = $order['order']['extra']['xpc_txnid'];
    $data = array(
        'txnId' => $txn_id,
    );

    list($status, $response) = xpc_api_request('payment', 'get_additional_info', $data);

    return array($status, $response);
}

/**
 * Get orders identificators by transaction id
 *
 * @return array
 */
function xpc_get_order_ids ($txn_id)
{
    global $sql_tbl;

    return array_map('intval', func_query_column("SELECT o.orderid FROM $sql_tbl[orders] as o INNER JOIN $sql_tbl[order_extras] as oe1 ON o.orderid = oe1.orderid AND oe1.khash = 'xpc_txnid' AND oe1.value = '" . $txn_id . "'"));
}

/**
 * Check if payment methods from X-Payment have already been imported to the database
 *
 * @return boolean Operation status
 */
function xpc_is_payment_methods_exists ()
{
    global $sql_tbl;

    return func_query_first("SELECT count(*) FROM $sql_tbl[ccprocessors] WHERE processor='cc_xpc.php'") > 0;
}

/**
 * Check if module X-Payments Connector is configured
 *
 * @return boolean Operation status
 */
function xpc_is_module_configured()
{
    return xpc_get_module_system_errors() === 0;
}

/**
 * Get X-Payments Connector configuration errors
 *
 * @return integer
 */
function xpc_get_module_system_errors()
{
    global $config;

    x_load('files');

    $failed = 0;

    // Check shopping cart id
    if (empty($config['XPayments_Connector']['xpc_shopping_cart_id']) || !preg_match('/^[\da-f]{32}$/Ss', $config['XPayments_Connector']['xpc_shopping_cart_id'])) {
        $failed |= XPC_SYSERR_CARTID;
    }

    // Check URL
    if (
        empty($config['XPayments_Connector']['xpc_xpayments_url']) 
        || (
            function_exists('is_url')
            && !is_url($config['XPayments_Connector']['xpc_xpayments_url'])
        )
    ) {
        $failed |= XPC_SYSERR_URL;
    }

    $parsed_url = @parse_url($config['XPayments_Connector']['xpc_xpayments_url']);

    if (!$parsed_url || !isset($parsed_url['scheme']) || $parsed_url['scheme'] != 'https') {
        $failed |= XPC_SYSERR_URL;
    }

    // Check public key
    if (empty($config['XPayments_Connector']['xpc_public_key'])) {
        $failed |= XPC_SYSERR_PUBKEY;
    }

    // Check private key
    if (empty($config['XPayments_Connector']['xpc_private_key'])) {
        $failed |= XPC_SYSERR_PRIVKEY;
    }

    // Check private key password
    if (empty($config['XPayments_Connector']['xpc_private_key_password'])) {
        $failed |= XPC_SYSERR_PRIVKEYPASS;
    }

    return $failed;
}

/************************ Functions to test connection between X-Cart and X-Payments  ************************/


/**
 * Make test request to X-Payments
 *
 * @return bool
 */
function xpc_request_test()
{
    srand();

    // Make test request
    list($status, $response) = xpc_api_request(
        'connect',
        'test',
        array('testCode' => ($hash_code = strval(rand(0, 1000000)))),
        xpc_request_test_schema()
    );

    // Compare MD5 hashes
    if ($status && !($status = (md5($hash_code) === $response['hashCode']))) {
        xpc_api_error('Test connection data is not valid');
    }

    return array(
        'status'   => $status,
        'response' => $response,
    );
}


/************************ Functions to test connection between X-Cart and X-Payments  ************************/

/**
 * Check if X-Cart meets the PCI DSS requirements
 *
 * @return boolean Operation status
 */
function xpc_check_pci_dss_requirements ()
{
    global $store_cc, $store_cvv2, $config, $active_modules, $sql_tbl;

    $result = array();

    if ($store_cvv2) {
        $result['E']['store_cvv2'] = func_get_langvar_by_name('lbl_xpc_recommend_store_cvv2');
    }

    if ($store_cc) {
        $result['W']['store_cc'] = func_get_langvar_by_name('lbl_xpc_recommend_store_cc');
    }

    if ($config['General']['disable_cc'] != 'Y') {
        $result['W']['disable_cc'] = func_get_langvar_by_name('lbl_xpc_recommend_disable_cc');
    }

    if (!empty($active_modules['Subscriptions'])) {
        $result['W']['subscriptions'] = func_get_langvar_by_name('lbl_xpc_recommend_subscriptions');
    }

    $pm_enabled = func_query("SELECT $sql_tbl[ccprocessors].module_name, $sql_tbl[ccprocessors].processor FROM $sql_tbl[payment_methods], $sql_tbl[ccprocessors] WHERE ($sql_tbl[payment_methods].paymentid=$sql_tbl[ccprocessors].paymentid) AND $sql_tbl[ccprocessors].processor!='cc_xpc.php' AND $sql_tbl[ccprocessors].background='Y' AND $sql_tbl[ccprocessors].disable_ccinfo!='Y' AND $sql_tbl[payment_methods].active='Y'");

    $methods = array();
    if (!empty($pm_enabled) && is_array($pm_enabled)) {
        foreach ($pm_enabled as $k => $v) {
            if ($v['processor'] == 'ps_paypal_pro.php') {
                if (in_array($config['paypal_solution'], array('uk', 'pro'))) {
                    $proc = xpc_get_paypal_dp_processor($config['paypal_solution']);
                    if ($proc['use'] == 'xpc') {
                        continue;
                    }

                } elseif ($config['paypal_solution'] == 'express') {
                    continue;
                }
            }

            $methods[] = $v['module_name'];
        }
    }

    $manual_cc_processing = func_query_first_cell("SELECT payment_method FROM $sql_tbl[payment_methods] WHERE paymentid='1' AND active='Y'");
    if (!empty($manual_cc_processing)) {
        $methods[] = $manual_cc_processing;
    }

    if (count($methods) > 0) {
        $result['W']['payment_methods'] = $methods;
    }

    return $result;

}

function xpc_can_capture($paymentid)
{
    $module_params = xpc_get_module_params($paymentid);

    return xpc_is_module_configured()
        && xpc_check_requirements() == 0
        && $module_params['param02'] == 'Y' ? 'xpc_do_capture' : false;
}

function xpc_can_void($paymentid)
{
    $module_params = xpc_get_module_params($paymentid);

    return xpc_is_module_configured()
        && xpc_check_requirements() == 0
        && $module_params['param03'] == 'Y' ? 'xpc_do_void' : false;
}

function xpc_can_refund($paymentid)
{
    return xpc_is_module_configured()
        && xpc_check_requirements() == 0
        && xpc_get_refund_mode($paymentid) ? 'xpc_do_refund' : false;
}

function xpc_get_refund_mode($paymentid)
{
    $module_params = xpc_get_module_params($paymentid);

    if ($module_params['param05'] == 'Y') {
        return 'P';

    } elseif ($module_params['param04'] == 'Y') {
        return 'Y';
    }

    return false;
}

function xpc_can_accept($paymentid)
{
    $module_params = xpc_get_module_params($paymentid);

    return xpc_is_module_configured()
        && xpc_check_requirements() == 0
        && substr($module_params['param07'], 0, 1) == 'Y' ? 'xpc_do_accept' : false;
}

function xpc_can_decline($paymentid)
{
    $module_params = xpc_get_module_params($paymentid);

    return xpc_is_module_configured()
        && xpc_check_requirements() == 0
        && substr($module_params['param07'], 1, 1) == 'Y' ? 'xpc_do_decline' : false;
}

function xpc_can_get_info($paymentid)
{
    $module_params = xpc_get_module_params($paymentid);

    return xpc_is_module_configured()
        && xpc_check_requirements() == 0 ? 'xpc_do_get_info' : false;
}

function xpc_do_capture($order)
{
    $data = array(
        'txnId' => $order["order"]["extra"]["xpc_txnid"],
        'amount' => $order["order"]['total'],
    );

    list($status, $response) = xpc_api_request('payment', 'capture', $data);

    $extra = array(
        'name' => 'xpc_txnid',
        'value' => $data['txnId']
    );

    if (!is_array($response)) {
        $response = array(
            'status' => 0,
            'message' => 'Internal error'
        );
    }

    $status = $response['status'] == 1;
    if ($response['status'] == 2) {
        $status = X_PAYMENT_TRANS_ALREADY_CAPTURED;
    }

    return array(
        $status,
        ($response['status'] == 0 && $response['message']) ? $response['message'] : false,
        $extra
    );
}

function xpc_do_void($order)
{
    $data = array(
        'txnId' => $order["order"]["extra"]["xpc_txnid"],
    );

    list($status, $response) = xpc_api_request('payment', 'void', $data);

    $extra = array(
        'name' => 'xpc_txnid',
        'value' => $data['txnId']
    );

    if (!is_array($response)) {
        $response = array(
            'status' => 0,
            'message' => 'Internal error'
        );
    }

    $status = $response['status'] == 1;
    if ($response['status'] == 2) {
        $status = X_PAYMENT_TRANS_ALREADY_VOIDED;
    }

    return array(
        $status,
        ($response['status'] == 0 && $response['message']) ? $response['message'] : '',
        $extra
    );
}

function xpc_do_refund($order, $amount = false)
{
    $data = array(
        'txnId' => $order["order"]["extra"]["xpc_txnid"],
    );

    if ($amount > 0) {
        $data['amount'] = $amount;
    }

    list($status, $response) = xpc_api_request('payment', 'refund', $data);

    $extra = array(
        'name' => 'xpc_txnid',
        'value' => $data['txnId']
    );

    if (!is_array($response)) {
        $response = array(
            'status' => 0,
            'message' => 'Internal error'
        );
    }

    $status = $response['status'] == 1;
    if ($response['status'] == 2) {
        $status = X_PAYMENT_TRANS_ALREADY_REFUNDED;
    }

    return array(
        $status,
        ($response['status'] == 0 && $response['message']) ? $response['message'] : '',
        $extra
    );

}

function xpc_do_accept($order)
{
    $data = array(
        'txnId' => $order["order"]["extra"]["xpc_txnid"],
    );

    list($status, $response) = xpc_api_request('payment', 'accept', $data);

    $extra = array(
        'name' => 'xpc_txnid',
        'value' => $data['txnId']
    );

    if (!is_array($response)) {
        $response = array(
            'status' => 0,
            'message' => 'Internal error'
        );
    }

    $status = $response['status'] == 1;
    if ($response['status'] == 2) {
        $status = X_PAYMENT_TRANS_ALREADY_ACCEPTED;
    }

    return array(
        $status,
        ($response['status'] == 0 && $response['message']) ? $response['message'] : '',
        $extra
    );

}

function xpc_do_decline($order)
{
    $data = array(
        'txnId' => $order["order"]["extra"]["xpc_txnid"],
    );

    list($status, $response) = xpc_api_request('payment', 'decline', $data);

    $extra = array(
        'name' => 'xpc_txnid',
        'value' => $data['txnId']
    );

    if (!is_array($response)) {
        $response = array(
            'status' => 0,
            'message' => 'Internal error'
        );
    }

    $status = $response['status'] == 1;
    if ($response['status'] == 2) {
        $status = X_PAYMENT_TRANS_ALREADY_DECLINED;
    }

    return array(
        $status,
        ($response['status'] == 0 && $response['message']) ? $response['message'] : '',
        $extra
    );

}

function xpc_do_get_info($order)
{
    $orderids = xpc_get_order_ids($order['order']['extra']['xpc_txnid']);

    list($status, $response) = xpc_request_get_payment_info($order['order']['extra']['xpc_txnid'], true);

    $extra = array(
        'name' => 'xpc_txnid',
        'value' => $order['order']['extra']['xpc_txnid']
    );

    $data = array(
        'status' => xpc_process_get_info($response, $orderids)
    );

    return array(
        $status,
        (!$status && $response['message']) ? $response['message'] : '',
        $extra,
        $data
    );

}

/**
 * Check - order use X-Payment connector as payment module or not
 *
 * @param integer $orderid Order id
 *
 * @return boolean
 */
function xpc_is_xpc_order($orderid)
{
    global $sql_tbl;

    $paymentid = func_query_first_cell("SELECT paymentid FROM $sql_tbl[orders] WHERE orderid = '" . $orderid . "'");

    $module_params = xpc_get_module_params($paymentid);

    return count($module_params) > 0;
}

/**
 * Convert action code to order status
 *
 * @param integer $action Action code
 *
 * @return string Order status code
 */
function xpc_get_order_status_by_action($action)
{
    global $config;

    $action = intval($action);

    $cell = false;

    switch ($action) {
        case XPC_NEW_ACTION:
            $cell = 'xpc_status_new';
            break;

        case XPC_AUTH_ACTION:
            $cell = 'xpc_status_auth';
            break;

        case XPC_CHARGED_ACTION:
            $cell = 'xpc_status_charged';
            break;

        case XPC_DECLINED_ACTION:
            $cell = 'xpc_status_declined';
            break;

        case XPC_REFUND_ACTION:
            $cell = 'xpc_status_refunded';
            break;

        case XPC_PART_REFUND_ACTION:
            $cell = 'xpc_status_part_refunded';
            break;
    }

    return ($cell && isset($config['XPayments_Connector'][$cell]) && $config['XPayments_Connector'][$cell]) 
        ? $config['XPayments_Connector'][$cell] 
        : false;
}

/**
 * Make X-Payments API request
 *
 * @param string $target Request target
 * @param string $action Request action
 * @param array  $data   Request data
 *
 * @return array Operation status & data
 */
function xpc_api_request($target, $action, $data = array(), $schema = array())
{
    global $config;

    // Check requirements
    if (!xpc_is_module_configured()) {
        return xpc_api_error('Module is not configured');
    }

    if (xpc_check_requirements() != 0) {
        return xpc_api_error('Check module requirements is failed');
    }

    $data['target'] = $target;
    $data['action'] = $action;

    // send API version
    $data['api_version'] = XPC_API_VERSION;

    // Convert array to XML
    $xml = xpc_hash2xml($data);

    if (!$xml) {
        return xpc_api_error('Data is not valid');
    }

    // Encrypt
    $xml = xpc_encrypt_xml($xml);

    if (!$xml) {
        return xpc_api_error('Data is not encrypted');
    }

    // HTTPS request
    $post = array(
        'cart_id' => $config['XPayments_Connector']['xpc_shopping_cart_id'],
        'request' => $xml,
    );

    xpc_curl_headers_collector(false);
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $config['XPayments_Connector']['xpc_xpayments_url'] . '/api.php');
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15000);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_HEADERFUNCTION, 'xpc_curl_headers_collector');

    curl_setopt($ch, CURLOPT_SSLVERSION, 3);

    if (!empty($config['General']['https_proxy'])) {
        // uncomment this line if you need proxy tunnel
        // curl_setopt ($ch, CURLOPT_HTTPPROXYTUNNEL, true);
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
        curl_setopt($ch, CURLOPT_PROXY, $config['General']['https_proxy']);
    }

    // insecure key is supported by curl since version 7.10
    $version = curl_version();

    if (is_array($version)) {
        $version = 'libcurl/' . $version['version'];
    }

    if (preg_match('/libcurl\/([^ $]+)/Ss', $version, $m)) {
        $parts = explode('.', $m[1]);
        if ($parts[0] > 7 || ($parts[0] = 7 && $parts[1] >= 10)) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
        }
    }

    $body = curl_exec($ch);
    $errno = curl_errno($ch);
    $error = curl_error($ch);

    $headers = xpc_curl_headers_collector(true);

    curl_close($ch);

    // Check raw data
    if (substr($body, 0, 3) !== 'API') {

        xpc_api_error("Response is not valid.\nResponse headers: " . var_export($headers, true) . "\nResponse: " . $body . "\n");

        return array(false, "Response is not valid.<br />Check logs.");

    }

    // Decrypt
    list($responseStatus, $response) = xpc_decrypt_xml($body);

    if (!$responseStatus) {
        return xpc_api_error('Response is not decrypted (Error: ' . $response . ')');
    }

    // Validate XML
    if (!empty($schema) && !xpc_validate_xml_against_schema($response, $schema, $error)) {
        return xpc_api_error('XML in response has a wrong format. Additional info: "' . $error . '"');
    }

    // Convert XML to array
    $response = xpc_xml2hash($response);

    if (!is_array($response)) {
        return xpc_api_error('Unable to convert response into XML');
    }

    // The 'Data' tag must be set in response
    if (!isset($response[XPC_TAG_ROOT])) {
        return xpc_api_error('Response does not contain any data');
    }

    $response = $response[XPC_TAG_ROOT];

    // Process errors
    $error = xpc_api_process_error($response);

    if ($error) {
        return array(
            null, 
            array(
                'status'        => 0, 
                'message'       => $error,
                'error_message' => '' == $response['is_error_message'] ? '' : $response['error_message'],
            )
        );
    }

    return array(true, $response);
}

/**
 * CURL headers collector callback
 */
function xpc_curl_headers_collector()
{
    static $headers = '';

    $args = func_get_args();

    if (count($args) == 1) {

        $return = '';

        if ($args[0] == true) {
            $return = $headers;
        }

        $headers = '';

        return $return;
    }

    if (trim($args[1]) != '') {
        $headers .= $args[1];
    }

    return strlen($args[1]);

}

/**
 * Encrypt data (RSA)
 *
 * @param string $data Request data
 *
 * @return string Encrypted data
 */
function xpc_encrypt_xml($data)
{
    global $config;

    // Preprocess
    srand(XC_TIME);
    $salt = '';
    for ($i = 0; $i < XPC_SALT_LENGTH; $i++) {
        $salt .= chr(rand(XPC_SALT_BEGIN, XPC_SALT_END));
    }

    $lenSalt = strlen($salt);

    $crcType = 'MD5';
    $crc = xpc_md5_raw($data);

    $crc = str_repeat(' ', 8 - strlen($crcType)) . $crcType . $crc;
    $lenCRC = strlen($crc);

    $lenData = strlen($data);

    $data = str_repeat('0', 12 - strlen((string)$lenSalt)) . $lenSalt . $salt
        . str_repeat('0', 12 - strlen((string)$lenCRC)) . $lenCRC . $crc
        . str_repeat('0', 12 - strlen((string)$lenData)) . $lenData . $data;

    // Encrypt
       $key = openssl_pkey_get_public($config['XPayments_Connector']['xpc_public_key']);
    if (!$key) {
        return false;
    }

    $data = str_split($data, XPC_CHUNK_LENGTH);
    $crypttext = null;
    foreach ($data as $k => $chunk) {
        if (!openssl_public_encrypt($chunk, $crypttext, $key)) {
            return false;
        }

        $data[$k] = $crypttext;
    }

    // Postprocess
    $data = array_map('base64_encode', $data);

    return 'API' . implode("\n", $data);
}

/**
 * Decrypt (RSA)
 *
 * @param string $data Encrypted data
 *
 * @return string Decrypted data
 */
function xpc_decrypt_xml($data)
{
    global $config;

    // Decrypt
    $res = openssl_get_privatekey($config['XPayments_Connector']['xpc_private_key'], $config['XPayments_Connector']['xpc_private_key_password']);
    if (!$res) {
        return array(false, 'Private key is not initialized');
    }

    $data = substr($data, 3);

    $data = explode("\n", $data);
    $data = array_map('base64_decode', $data);
    foreach ($data as $k => $s) {
        if (!openssl_private_decrypt($s, $newsource, $res)) {
            return array(false, 'Can not decrypt chunk');
        }

        $data[$k] = $newsource;
    }

    openssl_free_key($res);

    $data = implode('', $data);

    // Postprocess
    $lenSalt = substr($data, 0, 12);
    if (!preg_match('/^\d+$/Ss', $lenSalt)) {
        return array(false, 'Salt length prefix has wrong format');
    }

    $lenSalt = intval($lenSalt);
    $data = substr($data, 12 + intval($lenSalt));

    $lenCRC = substr($data, 0, 12);
    if (!preg_match('/^\d+$/Ss', $lenCRC) || $lenCRC < 9) {
        return array(false, 'CRC length prefix has wrong format');
    }

    $lenCRC = intval($lenCRC);
    $crcType = trim(substr($data, 12, 8));
    if ($crcType !== 'MD5') {
        return array(false, 'CRC hash is not MD5');
    }
    $crc = substr($data, 20, $lenCRC - 8);

    $data = substr($data, 12 + $lenCRC);

    $lenData = substr($data, 0, 12);
    if (!preg_match('/^\d+$/Ss', $lenData)) {
        return array(false, 'Data block length prefix has wrong format');
    }

    $data = substr($data, 12, intval($lenData));

    $currentCRC = xpc_md5_raw($data);
    if ($currentCRC !== $crc) {
        return array(false, 'Original CRC and calculated CRC is not equal');
    }

    return array(true, $data);
}

/**
 * Check string - UTF-8 encoding or not
 *
 * @param string $data Data
 *
 * @return boolean
 */
function xpc_isUTF8($data)
{
    $len = strlen($data);
    $result = true;
    for ($i = 0; $i < $len && $result; $i++) {
        $c = ord(substr($data, $i, 1));

        $l = false;
        if ($c > 193 && $c < 224) {
            $l = 2;

        } elseif ($c > 223 && $c < 240) {
            $l = 3;

        } elseif ($c > 239 && $c < 245) {
            $l = 4;

        } elseif ($c < 32 || $c > 127) {

            $result = false;
        }

        if ($l) {
            $result = $l + $i <= $len;
            for ($n = $i + 1; $n < $i + $l && $result; $n++) {
                $result = ord(substr($data, $n, 1)) >> 6 === 2;
            }

            $i += $l - 1;
        }

    }

    return $result;
}

/**
 * Process API response errors
 *
 * @param array $response Response data
 *
 * @return boolean True if error
 */
function xpc_api_process_error($response)
{
    global $xpc_errors;

    $error = false;

    if (isset($response['error']) && $response['error']) {

        $error = 'X-Payments error (code: ' . $response['error'] . '): '
            . (isset($response['error_message']) ? $response['error_message'] : 'Unknown')
            . (isset($xpc_errors[$response['error']]) ? $xpc_errors[$response['error']] : '');

        xpc_api_error($error);
    }

    return $error;
}

/**
 * Make MD5 hash in raw format
 *
 * @param string $data Data
 *
 * @return string
 */
function xpc_md5_raw($data)
{
    $crc = md5($data);
    $str = '';
    for ($i = 0; $i < 32; $i += 2) {
        $str .= chr(hexdec(substr($crc, $i, 2)));
    }

    return $str;
}

/**
 * Format and log API errors
 *
 * @param string $msg Error message
 *
 * @return array false & error message
 */
function xpc_api_error($msg)
{
    if (function_exists('x_log_add')) {
        x_log_add('xpay_connector', $msg, true);
    } else {
        error_log($msg, 0);
    }

    return array(false, $msg);
}

/**
 * Get module parameters by payment id
 *
 * @param integer $paymentid Payment id
 *
 * @return array Module parameters
 */
function xpc_get_module_params($paymentid)
{
    global $sql_tbl, $config;

    if (xpc_is_emulated_paypal($paymentid)) {
        $proc = xpc_get_paypal_dp_processor($config['paypal_solution']);
        $module_params = $proc['processor'];

    } else {
        $module_params = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor = 'cc_xpc.php' AND paymentid = '" . $paymentid . "'");
    }

    return $module_params;
}

/**
 * Check payment method - is emulated PayPal WPP / PayPal WPPPE : Direct Payment or not
 *
 * @param integer $paymentid Payment id
 *
 * @return boolean
 */
function xpc_is_emulated_paypal($paymentid)
{
    global $sql_tbl, $config;

    $result = false;
    $is_emulated_paypal_dp = func_query_first_cell("SELECT COUNT(paymentid) FROM $sql_tbl[payment_methods] WHERE paymentid = '" . $paymentid . "' AND processor_file = 'ps_paypal_pro.php'") > 0;
    if ($is_emulated_paypal_dp) {
        $proc = xpc_get_paypal_dp_processor($config['paypal_solution']);
        $result = $proc['use'] == 'xpc';
    }

    return $result;
}

/**
 * Check - exists PayPal Direct Payment integration in imported from X-Payments modules or not
 *
 * @param string $solution Solution code
 *
 * @return boolean
 */
function xpc_is_paypal_dp_exists($solution)
{
    global $sql_tbl, $xpc_paypal_dp_solutions;

    $result = false;

    if (isset($xpc_paypal_dp_solutions[$solution])) {
        $result = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[ccprocessors] WHERE processor = 'cc_xpc.php' AND param08 = '" . $xpc_paypal_dp_solutions[$solution] . "'") > 0;
    }

    return $result;
}

/**
 * Get imported from X-Payments PayPal Direct Payment modules for specified solution
 *
 * @param string $solution Solution code
 *
 * @return array
 */
function xpc_get_paypal_dp_list($solution)
{
    global $sql_tbl, $xpc_paypal_dp_solutions, $config;

    $result = array();

    if (isset($xpc_paypal_dp_solutions[$solution])) {
        $result = func_query("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor = 'cc_xpc.php' AND param08 = '" . $xpc_paypal_dp_solutions[$solution] . "' ORDER BY module_name");
        $selected = isset($config['paypal_dp_use_xpc_processor_' . $solution]) ? $config['paypal_dp_use_xpc_processor_' . $solution] : false;
        if ($selected) {
            foreach ($result as $k => $v) {
                $result[$k]['selected'] = $v['param01'] == $selected;
            }
        }
    }

    return $result;
}

/**
 * Get data for PayPal Direct Payment module
 *
 * @param string $solution Solution code
 *
 * @return array
 */
function xpc_get_paypal_dp_processor($solution)
{
    global $sql_tbl, $xpc_paypal_dp_solutions, $config;

    $result = array(
        'use_xpc' => false,
        'processor' => false,
        'use' => 'local',
        'warning' => false,
    );

    if (isset($xpc_paypal_dp_solutions[$solution])) {
        $result['use_xpc'] = isset($config["paypal_dp_use_xpc_" . $solution]) ? $config["paypal_dp_use_xpc_" . $solution] == 'Y' : false;

        $id = isset($config['paypal_dp_use_xpc_processor_' . $solution]) ? $config['paypal_dp_use_xpc_processor_' . $solution] : false;

        if ($id) {
            $result['processor'] = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor = 'cc_xpc.php' AND param01 = '" . addslashes($id) . "' AND param08 = '" . $xpc_paypal_dp_solutions[$solution] . "' ORDER BY module_name");
        }

        $local = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor = 'ps_paypal_pro.php'");
        if ($local) {
            $hash = false;
            switch ($solution) {
                case 'pro':
                    $hash = $local['param01'];
                    break;

                case 'uk':
                    $hash = $local['param01'] . $local['param02'] . $local['param04'];
                    break;
            }

            if (!empty($hash) && !$result['warning'] && $result['processor']) {
                $hash = md5($hash);

                if ($hash != $result['processor']['param09']) {
                    $result['warning'] = 'no equal';

                } elseif ($result['use_xpc']) {
                    $result['use'] = 'xpc';
                }

            } elseif (empty($hash)) {

                // Local Direct Payment not configured
                $result['warning'] = 'no configured';

            } elseif (!$result['processor']) {

                // Imported X-Payments' Direct Payment not selected
                $result['warning'] = 'no processor';
            }
        }

    } else {

        // Wrong solution (IPN or Express)
        $result['warning'] = 'wrong solution';
    }

    return $result;
}

function xpc_filter_hidden_processors($data)
{
    global $xpc_paypal_dp_solutions;

    foreach ($data as $k => $row) {
        if ($row['processor'] == 'cc_xpc.php' && in_array($row['param08'], $xpc_paypal_dp_solutions)) {
            unset($data[$k]);
        }
    }

    return $data;
}

/**
 *    Convert local string ti UTF-8
 */
function xpc_local2utf8($string, $charset = null)
{
    global $default_charset;

    if (is_null($charset)) {
        $charset = $default_charset;
    }

    $charset = strtolower(trim($charset));

    if (function_exists('utf8_encode') && $charset == 'iso-8859-1') {
        $string = utf8_encode($string);

    } elseif (function_exists('iconv')) {
        $string = iconv($charset, 'utf-8', $string);

    } else {

        $len = strlen($string);
        $data = '';
        for ($i = 0; $i < $len; $i++) {
            $c = ord(substr($string, $i, 1));
            if (!($c < 32 || $c > 127)) {
                $data .= substr($string, $i, 1);
            }
        }

        $string = $data;
    }

    return $string;
}

/**
 * Get configuration array from configuration deployement path
 * 
 * @param string $configuration configuration string 
 * 
 * @return array
 * @see    ____func_see____
 * @since  1.0.0
 */
function func_xpc_get_configuration($configuration)
{
    return unserialize(base64_decode($configuration));
}

/**
 * Check if the deploy configuration is correct array
 * 
 * @param array $configuration configuration array
 *  
 * @return boolean
 * @see    ____func_see____
 * @since  1.0.0
 */
function func_xpc_check_deploy_configuration($configuration)
{
    $required = array(
        'store_id',
        'url',
        'public_key',
        'private_key',
        'private_key_password',
    );

    return is_array($configuration)
        && ($required === array_intersect(array_keys($configuration), $required));
}

/**
 * Store configuration array into DB
 * 
 * @param array $configuration configuration array
 *  
 * @return void
 * @see    ____func_see____
 * @since  1.0.0
 */
function func_xpc_store_configuration($configuration)
{
    $mapFields = array(
        'store_id'              => 'xpc_shopping_cart_id',
        'url'                   => 'xpc_xpayments_url',
        'public_key'            => 'xpc_public_key',
        'private_key'           => 'xpc_private_key',
        'private_key_password'  => 'xpc_private_key_password',
    );

    foreach ($mapFields as $origName => $dbName) {
        func_array2update(
            'config',
            array(
                'value' => addslashes(str_replace("\n", "\r\n", $configuration[$origName])),
            ),
            "name='" . $dbName . "' AND category='XPayments_Connector'"
        );
    }
}

?>
