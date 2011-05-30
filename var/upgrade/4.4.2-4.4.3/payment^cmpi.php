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
 * Cardinal payment authentication
 * Thin Client
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cmpi.php,v 1.58.2.1 2011/01/10 13:12:08 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) {

    require './auth.php';

    if ($config['CMPI']['cmpi_enabled'] != 'Y') {

        header("Location: ../");

        die("Access denied");

    }

    $stand_alone = true;

} else {

    if ($config['CMPI']['cmpi_enabled'] != 'Y')
        func_403(71);

    $stand_alone = false;
}

x_load(
    'http',
    'order',
    'payment',
    'xml',
    'tests'
);

x_session_register('cmpi_tid');
x_session_register('cmpi_spahf');
x_session_register('cmpi_env');

$sql_tbl['country_currencies']     = 'xcart_country_currencies';
$sql_tbl['currencies']             = 'xcart_currencies';

$timeout = 15;

// Save script enviroment
if (!$stand_alone) {

    $cmpi_env = array(
        '_POST'             => $_POST,
        'products'             => $products,
        'userinfo'             => $userinfo,
        'cart'                 => $cart,
        'is_egoods'         => $is_egoods,
        'order_details'     => $order_details,
        'bill_output'         => $bill_output,
        'orderids'             => $orderids,
        'orderid'             => $orderid,
        'bill_firstname'     => $bill_firstname,
        'bill_lastname'     => $bill_lastname,
        'bill_name'         => $bill_name,
        'ship_firstname'     => $ship_firstname,
        'ship_lastname'     => $ship_lastname,
        'ship_name'         => $ship_name,
    );

} elseif (is_array($cmpi_env)) {

    foreach ($cmpi_env as $var => $value) {

        $$var = $value;

        if ($var == '_POST') {

            $reject = func_init_reject(X_REJECT_OVERRIDE);

            $reject = array_diff(
                $reject,
                array(
                    'paymentid',
                    'action',
                    'xid',
                    'payment_method',
                    'card_type',
                    'card_name',
                    'card_number',
                    'card_expire_Month',
                    'card_expire_Year',
                    'card_cvv2',
                    'Customer_Notes',
                    'partner_login',
                    'card_expire',
                    'card_valid_from',
                )
            );

            func_var_cleanup($var);

            func_init_reject(X_REJECT_CLEAN);

        }

    }

    $_SERVER['REQUEST_METHOD'] = $REQUEST_METHOD = 'POST';

}

if (empty($orderids)) {

    $orderids = array($orderid);

}

if (
    empty($orderids)
    || !test_active_bouncer()
) {
    return false;
}

// Get order(s) data
$order = array();

if (
    !empty($orderids)
    && is_array($orderids)
) {

    foreach ($orderids as $orderid) {

        $tmp = func_order_data($orderid);

        if (empty($tmp))
            continue;

        if (empty($order)) {

            $order = $tmp;

        } else {

            $order['order']['total'] += $tmp['order']['total'];

            $order['products'] = func_array_merge($order['products'], $tmp['products']);

        }

    }

}

if (!isset($order['order'])) {

    return false;

}

$order['order']['desc'] = "Order #" . implode(", ", $orderids) . "; customer: " . $order['userinfo']['login'] . ";";

if (isset($force_userinfo)) {

    $order['order']['userinfo'] = func_array_merge($order['order']['userinfo'], $force_userinfo);

}

// Get CC data
$tmp = explode("\n", $order['order']['details']);

$details = array();

foreach ($tmp as $v) {

    $key = substr($v, 0, strpos($v, ":"));

    $value = substr($v, strpos($v, ":") + 1);

    $details[trim($key)] = trim($value);

}

unset ($tmp);

if (isset($force_details)) {

    $details = func_array_merge($details, $force_details);

}

$hash = array();

// PayPal part
if ($order['order']['payment_method'] == 'PayPal') {

    $details['{CardNumber}'] = "PAYPAL";

    $details['{ExpDate}'] = "";

}

if (
    isset($card_number)
    && !isset($details['{CardNumber}'])
) {
    $details['{CardNumber}'] = $card_number;
}

if (
    isset($card_expire)
    && !isset($details['{ExpDate}'])
) {
    $details['{ExpDate}'] = $card_expire;
}

/**
 * cmpi_lookup method
 */
if (!$stand_alone) {

    $cur = func_query_first_cell("SELECT code_int FROM $sql_tbl[currencies] WHERE code = '" . $config['CMPI']['cmpi_currency'] . "'");

    $hash = array(
        'CardinalMPI' => array(
            'MsgType'             => "cmpi_lookup",
            'Version'             => "1.7",
            'ProcessorId'         => $config['CMPI']['cmpi_proseccorid'],
            'MerchantId'          => $config['CMPI']['cmpi_merchantid'],
            'TransactionPwd'      => $config['CMPI']['cmpi_transpwd'],
            'TransactionType'     => 'C',
            'Amount'              => preg_replace("/\D/Ss", "", $order['order']['total']),
            'CurrencyCode'        => $cur,
            'CardNumber'          => $details['{CardNumber}'],
            'CardExpMonth'        => substr($details['{ExpDate}'], 0, 2),
            'CardExpYear'         => intval(substr($details['{ExpDate}'], 2, 2)) + 2000,
            'OrderNumber'         => $order['order']['orderid'],
            'OrderDesc'           => substr($order['order']['desc'], 0, 125),
            'UserAgent'           => substr($_SERVER['HTTP_USER_AGENT'], 0, 255),
            'BrowserHeader'       => "*/*",
            'EMail'               => substr($order['userinfo']['email'], 0, 255),
            'IPAddress'           => func_get_valid_ip($_SERVER['REMOTE_ADDR']),
            'BillingFirstName'    => substr($order['userinfo']['b_firstname'], 0, 50),
            'BillingLastName'     => substr($order['userinfo']['b_lastname'], 0, 50),
            'BillingAddress1'     => substr($order['userinfo']['b_address'], 0, 50),
            'BillingAddress2'     => substr($order['userinfo']['b_address_2'], 0, 50),
            'BillingCity'         => substr($order['userinfo']['b_city'], 0, 50),
            'BillingState'        => substr($order['userinfo']['b_state'], 0, 50),
            'BillingPostalCode'   => substr($order['userinfo']['b_zipcode'], 0, 10),
            'BillingCountryCode'  => substr($order['userinfo']['b_country'], 0, 3),
            'BillingPhone'        => substr($order['userinfo']['phone'], 0, 20),
            'ShippingFirstName'   => substr($order['userinfo']['s_firstname'], 0, 50),
            'ShippingLastName'    => substr($order['userinfo']['s_lastname'], 0, 50),
            'ShippingAddress1'    => substr($order['userinfo']['s_address'], 0, 50),
            'ShippingAddress2'    => substr($order['userinfo']['s_address_2'], 0, 50),
            'ShippingCity'        => substr($order['userinfo']['s_city'], 0, 50),
            'ShippingState'       => substr($order['userinfo']['s_state'], 0, 50),
            'ShippingPostalCode'  => substr($order['userinfo']['s_zipcode'], 0, 10),
            'ShippingCountryCode' => substr($order['userinfo']['s_country'], 0, 3),
            'ShippingPhone'       => substr($order['userinfo']['phone'], 0, 20),
        )
    );

    $i = 1;

    if (
        isset($order['products'])
        && is_array($order['products'])
    ) {

        foreach ($order['products'] as $p) {

            $hash['CardinalMPI']['Item_Name_' . $i]     = substr($p['product'], 0, 128);
            $hash['CardinalMPI']['Item_Desc_' . $i]     = substr($p['fulldescr'] ? $p['fulldescr'] : $p['descr'], 0, 255);
            $hash['CardinalMPI']['Item_Price_' . $i]    = preg_replace("/\D/Ss", "", $p['price']);
            $hash['CardinalMPI']['Item_Quantity_' . $i] = $p['amount'];
            $hash['CardinalMPI']['Item_SKU_' . $i]      = substr($p['productcode'], 0, 20);

            $i++;

        }

    }

    if (
        isset($order['giftcerts'])
        && is_array($order['giftcerts'])
    ) {

        foreach ($order['giftcerts'] as $p) {

            $hash['CardinalMPI']['Item_Name_' . $i]     = 'Gift certificate #' . $p['gcid'];
            $hash['CardinalMPI']['Item_Price_' . $i]    = preg_replace("/\D/Ss", "", $p['amount']);
            $hash['CardinalMPI']['Item_Quantity_' . $i] = '1';
            $hash['CardinalMPI']['Item_SKU_' . $i]      = substr($p['gcid'], 0, 20);

            $i++;

        }

    }

    foreach ($hash['CardinalMPI'] as $n => $v) {

        $hash['CardinalMPI'][$n] = func_xml_escape($v);

    }

    $xml = func_hash2xml($hash);

    $t = XC_TIME;

    list(
        $header,
        $res
    ) = func_https_request(
        'POST',
        $config['CMPI']['cmpi_url'],
        array(
            "cmpi_msg=" . $xml
        ),
        "&",
        '',
        "application/x-www-form-urlencoded",
        '',
        '',
        '',
        '',
        $timeout
    );

    $res = func_xml2hash($res);

    $res = $res['CardinalMPI'];

    if (empty($res) && XC_TIME - $t >= $timeout) {

        $res = array(
            'Enrolled'  => 'U',
            'ErrorDesc' => "HTTPS: Time out ($timeout)"
        );

    }

    // Redirect customer to Cardinal commerce server
    if (empty($res)) {

        $bill_output['code'] = 4;

        $bill_output['billmes'] = func_get_langvar_by_name("txt_cmpi_customer_error");

        require $xcart_dir . '/payment/payment_ccend.php';

    } elseif (
        $res['ErrorNo'] == 0
        && $res['Enrolled'] == 'Y'
    ) {

        $cmpi_tid   = $res['TransactionId'];
        $cmpi_spahf = $res['SPAHiddenFields'];

        x_session_register('cmpi_iframe_data');

        $cmpi_iframe_data['PaReq']   = $res['Payload'];
        $cmpi_iframe_data['TermUrl'] = $current_location."/payment/cmpi.php?".$XCART_SESSION_NAME."=".$XCARTSESSID."&from_frame";
        $cmpi_iframe_data['MD']      = $XCARTSESSID;
        $cmpi_iframe_data['ACSUrl']  = $res['ACSUrl'];

        if ($card_type == 'MC') {

            $type = func_get_langvar_by_name('lbl_cmpi_mcsc',false,false,true);

        } elseif ($card_type == 'JCB') {

            $type = func_get_langvar_by_name('lbl_cmpi_jcbjs',false,false,true);

        } else {

            $type = func_get_langvar_by_name('lbl_cmpi_vbv',false,false,true);
        }

        echo func_get_langvar_by_name('txt_cmpi_frame_customer_note', array('type' => $type), false, true);
?>
<br />
<br />
<br />
<table width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td valign="top" align="center">
            <iframe width="410" height="410" scrolling="no" marginwidth="0" marginheight="0" src="<?php echo $current_location . "/payment/cmpi.php?" . $XCART_SESSION_NAME . "=" . $XCARTSESSID . "&amp;iframe"; ?>" ></iframe><br />
            <br />
            <a href="<?php echo $current_location . DIR_CUSTOMER; ?>/cart.php?mode=checkout"><?php echo func_get_langvar_by_name('lbl_go_back_to_payment_methods_list', array(), false, true); ?></a>
        </td>
    </tr>
</table>
<?php
        exit;

    } else {

        x_log_add('cardinal_commerce', $res['ErrorNo'].":".$res['ErrorDesc']);

    }

    $cmpi_result = $res;

    unset($res);

    require_once $xcart_dir . '/include/cc_detect.php';

    if (is_visa($details['{CardNumber}'])) {

        $cmpi_result['EciFlag'] = 6;

    } elseif (is_mc($details['{CardNumber}'])) {

        $cmpi_result['EciFlag'] = 1;

    }

    require $xcart_dir . '/payment/' . basename($module_params['processor']);

    require $xcart_dir . '/payment/payment_ccend.php';

    exit;

} elseif (isset($iframe)) {

    // Display IFRAME content

    x_session_register('cmpi_iframe_data');

    if (empty($cmpi_iframe_data)) {

        header("Location: ../");

        die("Access denied");

    }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<body onload="javascript: document.getElementById('frm').submit();">
<form id="frm" name="frm" action="<?php echo func_convert_amp($cmpi_iframe_data['ACSUrl']); ?>" method="post">
    <input type="hidden" name="PaReq" value="<?php echo $cmpi_iframe_data['PaReq']; ?>" />
    <input type="hidden" name="TermUrl" value="<?php echo $cmpi_iframe_data['TermUrl']; ?>" />
    <input type="hidden" name="MD" value="<?php echo $cmpi_iframe_data['MD']; ?>" />
</form>
</body>
</html>
<?php

/**
 * Return from IFRAME
 */
} elseif (isset($close_frame)) {

    require $xcart_dir . '/include/payment_wait.php';

    x_session_unregister('cmpi_env');

    x_session_register('cmpi_result');

    if (empty($cmpi_result)) {

        header("Location: ../");

        die("Access denied");

    }

    $is_paypal_pro = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[payment_methods] WHERE paymentid='" . $paymentid . "' AND processor_file='ps_paypal_pro.php'");

    if ($is_paypal_pro) {

        $where = "processor = 'ps_paypal_pro.php'";

    } else {

        $where = "paymentid = '" . $paymentid . "'";

    }

    $module_params = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE " . $where);

    x_session_register('secure_oid');

    x_session_register('secure_oid_cost');

    // ... last checkout step
    if (
        (
            !in_array($cmpi_result['PAResStatus'], array('Y', 'A'))
            || $cmpi_result['SignatureVerification'] != "Y"
        )
        && (
            empty($cmpi_result['ErrorDesc'])
            || $cmpi_result['ErrorNo'] == 1050
        )
    ) {
        $bill_output['code'] = 4;

        if (
            $cmpi_result['PAResStatus'] == 'Y'
            && $cmpi_result['SignatureVerification'] == "N"
        ) {

            $bill_output['billmes'] = func_get_langvar_by_name("txt_cmpi_customer_error2");

        } elseif (
            $cmpi_result['PAResStatus'] == 'N'
            && $cmpi_result['SignatureVerification'] == "Y"
        ) {

            $bill_output['billmes'] = func_get_langvar_by_name("txt_cmpi_customer_error3");

        } elseif (
            $cmpi_result['PAResStatus'] == 'U'
            && $cmpi_result['SignatureVerification'] == "Y"
        ) {

            $bill_output['billmes'] = func_get_langvar_by_name("txt_cmpi_customer_error2");

        } elseif (!isset($cmpi_result['PAResStatus'])) {

            $bill_output['billmes'] = func_get_langvar_by_name("txt_cmpi_customer_error");

        } elseif ($cmpi_result['ErrorNo'] == 1050) {

            $bill_output['billmes'] = func_get_langvar_by_name("txt_cmpi_customer_error");

        }

        require $xcart_dir . '/payment/payment_ccend.php';

    }

    // ... payment module
    if (!empty($module_params['processor'])) {

        require $xcart_dir . '/include/cc_detect.php';

        if (empty($cmpi_result['EciFlag'])) {

            if (is_visa($details['{CardNumber}'])) {

                $cmpi_result['EciFlag'] = 6;

            } elseif (is_mc($details['{CardNumber}'])) {

                $cmpi_result['EciFlag'] = 1;

            }

        }

        require $xcart_dir . '/payment/' . basename($module_params['processor']);

        require $xcart_dir . '/payment/payment_ccend.php';

        // ... customer interface
     } else {

        func_header_location($current_location . DIR_CUSTOMER . "/cart.php?mode=order_message&orderids=" . func_get_urlencoded_orderids($orderids));

    }

} elseif (isset($from_frame)) {
/**
 * cmpi_authenticate method
 */
    $hash = array(
        'CardinalMPI' => array(
            'MsgType'         => "cmpi_authenticate",
            'Version'         => "1.7",
            'ProcessorId'     => $config['CMPI']['cmpi_proseccorid'],
            'MerchantId'      => $config['CMPI']['cmpi_merchantid'],
            'TransactionPwd'  => $config['CMPI']['cmpi_transpwd'],
            'TransactionType' => 'C',
            'TransactionId'   => $cmpi_tid,
            'PAResPayload'    => $PaRes,
        )
    );

    foreach ($hash['CardinalMPI'] as $n => $v) {

        $hash['CardinalMPI'][$n] = func_xml_escape($v);

    }

    $xml = func_hash2xml($hash);

    $t = XC_TIME;

    list(
        $header,
        $res
    ) = func_https_request(
        'POST',
        $config['CMPI']['cmpi_url'],
        array(
            "cmpi_msg=" . $xml,
        ),
        "&",
        '',
        "application/x-www-form-urlencoded",
        '',
        '',
        '',
        '',
        $timeout
    );

    $res = func_xml2hash($res);

    $res = $res['CardinalMPI'];

    if (
        empty($res)
        && time() - $t >= $timeout
    ) {
        $res = array(
            'ErrorDesc' => "HTTPS: Time out ($timeout)",
        );
    }

    if (!empty($cmpi_spahf)) {
        $res['SPAHiddenFields'] = $cmpi_spahf;
    }

    // Generate inner transaction status
    if ($res['ErrorNo'] == 0) {

        switch ($res['PAResStatus']) {

            case 'Y':
                $res['PAResStatusDesc'] = "Successful authentication. Cardholder successfully authenticated with their Card Issuer.";
                break;

            case 'A':
                $res['PAResStatusDesc'] = "Attempts authentication. Cardholder authentication was attempted.";
                break;

            case 'N':
                $res['PAResStatusDesc'] = "Failed authentication. Cardholder failed to successfully authenticate with their Card Issuer.";
                break;

            case 'U':
                $res['PAResStatusDesc'] = "Authentication unavailable. Authentication with the Card Issuer was unavailable.";
                break;

            default:
                $res['PAResStatusDesc'] = "Inner error";

        }

    }

    $res['TransactionID']   = $cmpi_tid;
    $res['SPAHiddenFields'] = $cmpi_spahf;

    x_session_unregister('cmpi_tid');
    x_session_unregister('cmpi_spahf');

    // Generate common transaction status
    $res['status'] = (
        $res['ErrorNo'] == 0
        && (
            $res['PAResStatus'] == 'Y'
            || $res['PAResStatus'] == 'A'
        )
        && $res['SignatureVerification'] == 'Y'
    )
    ? 'Y'
    : '';

    x_session_register('cmpi_result');

    $cmpi_result = $res;

    // Redirect to ...
?>
<script type="text/javascript">
//<![CDATA[
window.parent.location = '<?php echo $current_location . '/payment/cmpi.php?' . $XCART_SESSION_NAME . "=" . $XCARTSESSID; ?>&close_frame=close_frame';
//]]>
</script>
<?php
    exit;
}
?>
