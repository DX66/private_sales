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
 * "" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_xpc.php,v 1.24.2.5 2011/01/10 13:12:07 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

$auth_path = is_file('.' . DIRECTORY_SEPARATOR . 'auth.php')
    && is_readable('.' . DIRECTORY_SEPARATOR . 'auth.php');

if (
    $_SERVER['REQUEST_METHOD'] == 'POST' 
    && $_POST['action'] == 'return' 
    && !empty($_POST['refId']) 
    && !empty($_POST['txnId'])
) {

    // Return

    if (!$auth_path)
        @require_once './../auth.php';
    else
        require './auth.php';

    func_xpay_func_load();

    $key = 'XPC' . $_POST['refId'];
    $bill_output['sessid'] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref = '" . $key . "'");

    list($status, $response) = xpc_request_get_payment_info($_POST['txnId']);

    $extra_order_data = array(
        'xpc_txnid' => $_POST['txnId'],
    );

    if ($status) {

        $bill_output['code'] = 2;

        if ($response['status'] == PAYMENT_AUTH_STATUS || $response['status'] == PAYMENT_CHARGED_STATUS) {

            $bill_output['code'] = 1;

        } elseif ($response['transactionInProgress']) {

            $bill_output['code'] = 3;

        }

        $bill_output['billmes'] = ($bill_output['code'] == 1)
            ? $response['message']
                . "\n"
                . '(last 4 card numbers: '
                . @$_POST['last_4_cc_num']
                . ');'
                . "\n"
                . '(card type: '
                . @$_POST['card_type']
                . ');'
            : $response['lastMessage'];

        if (
            $response['status'] == PAYMENT_AUTH_STATUS
            || (
                $response['authorizeInProgress'] > 0 
                && $bill_output['code'] == 3
            )
        ) {

            $extra_order_data['capture_status'] = 'A';

            $bill_output['is_preauth'] = true;

        } else {

            $extra_order_data['capture_status'] = '';

        }

        if (
            $bill_output['code'] == 1 
            && $response['isFraudStatus']
        ) {

            $extra_order_data['fmf_blocked'] = 'Y';

        }

        $payment_return = array(
            'total'     => $response['amount'],
            'currency'  => $response['currency'],
            '_currency' => xpc_get_currency($_POST['refId']),
        );

    } else {

        $bill_output['code'] = 2;
        $bill_output['billmes'] = 'Internal error';

    }

    $weblink = false;

    require($xcart_dir . '/payment/payment_ccend.php');

    exit;

} elseif (
    $_SERVER['REQUEST_METHOD'] == 'GET' 
    && (
        $_GET['action'] == 'cancel'
        || $_GET['action'] == 'abort' 
    ) && !empty($_GET['refId']) 
    && !empty($_GET['txnId'])
) {

    // Cancel

    if (!$auth_path)
        @require_once './../auth.php';
    else
        require './auth.php';

    func_xpay_func_load();

    $key = 'XPC' . $_GET['refId'];

    $bill_output['sessid'] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref = '" . $key . "'");

    $bill_output['code'] = 2;

    $bill_output['billmes'] = 'cancel' == $_GET['action']
        ? 'Cancelled by customer'
        : 'Aborted due to errors during transaction processing';

    $weblink = false;

    require($xcart_dir . '/payment/payment_ccend.php');

    exit;

} elseif (
    $_SERVER['REQUEST_METHOD'] == 'POST' 
    && $_POST['action'] == 'callback' 
    && !empty($_POST['txnId']) 
    && !empty($_POST['updateData'])
) {

    // Callback

    if (!$auth_path)
        @require_once './../auth.php';
    else
        require './auth.php';

    // Check module
    if (empty($active_modules['XPayments_Connector'])) {

        if (function_exists('x_log_add')) {
            x_log_add('xpay_connector', 'X-Payments Connector callback script is called', true);
        } else {
            error_log('xpay_connector: X-Payments Connector callback script is called', 0);
        }

        exit;

    }

    // Check callback IP addresses
    $ips = preg_grep('/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/Ss', array_map('trim', explode(',', $config['XPayments_Connector']['xpc_allowed_ip_addresses'])));

    $found = false;

    foreach ($ips as $ip) {
        if ($_SERVER['REMOTE_ADDR'] == $ip) {
            $found = true;
            break;
        }
    }

    if (
        $ips 
        && !$found
    ) {

        if (function_exists('x_log_add')) {
            x_log_add('xpay_connector', 'X-Payments Connector callback script is called from wrong IP address: \'' . $_SERVER['REMOTE_ADDR'] . '\'', true);
        } else {
            error_log('xpay_connector: X-Payments Connector callback script is called from wrong IP address: \'' . $_SERVER['REMOTE_ADDR'] . '\'', 0);
        }

        exit;

    }

    func_xpay_func_load();

    list($responseStatus, $response) = xpc_decrypt_xml($updateData);

    if (!$responseStatus) {

        xpc_api_error('Callback request is not decrypted (Error: ' . $response . ')');

        exit;

    }

    // Convert XML to array
    $response = xpc_xml2hash($response);

    if (!is_array($response)) {

        xpc_api_error('Unable to convert callback request into XML');

        exit;

    }

    // The 'Data' tag must be set in response
    if (!isset($response[XPC_TAG_ROOT])) {

        xpc_api_error('Callback request does not contain any data');

        exit;

    }

    $response = $response[XPC_TAG_ROOT];

    // Process data
    if (!xpc_api_process_error($response)) {

        xpc_update_payment($txnId, $response);

    }

    exit;

} else {

    // Initialize transaction & redirect to X-Payments

    if (!defined('XCART_START')) { header('Location: ../'); die('Access denied'); }

    func_xpay_func_load();

    if (!function_exists('func_create_payment_form')) {

        function func_create_payment_form($url, $fields, $name, $method = "POST") {

            global $smarty;

            $charset = ""; 

            if (!empty($smarty))
                $charset = $smarty->get_template_vars("default_charset");

            if (empty($charset))
                $charset = "iso-8859-1";

            $method = strtoupper($method);

            if (!in_array($method, array("POST", "GET")))
                $method = "POST";

            $button_title = 'Submit';

            $script_note = 'Please wait while connecting to <b>' . $name . '</b> payment gateway...';
    ?>  
<form action="<?php echo $url; ?>" method="<?php echo $method; ?>" name="process">
<?php
    foreach($fields as $fn => $fv) {
?>  <input type="hidden" name="<?php echo $fn; ?>" value="<?php echo htmlspecialchars($fv); ?>" />
<?php
    }   
?>
<table class="WebBasedPayment" cellspacing="0">
<tr>
    <td id="text_box">
<noscript>
<input type="submit" value="<?php echo $button_title; ?>">
</noscript>
    </td>
</tr>
</table>
</form>
<script type="text/javascript">
<!--
if (document.getElementById('text_box'))
    document.getElementById('text_box').innerHTML = "<?php echo strtr($script_note, array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n','</'=>'<\/')); ?>";
document.process.submit();
-->
</script>
    <?php
        }
    }

    $refId = implode('-', $secure_oid);

    if (!$duplicate) {
        func_array2insert(
            'cc_pp3_data',
            array(
                'ref'       => 'XPC' . $refId, 
                'sessionid' => $XCARTSESSID,
            ), 
            true
        );
    }

    $united_cart = $cart;

    $united_cart['userinfo'] = $userinfo;
    $united_cart['products'] = $products;

    list($status, $response) = xpc_request_payment_init(
        intval($paymentid),
        $refId,
        $united_cart,
        function_exists("func_is_preauth_force_enabled") ? func_is_preauth_force_enabled($secure_oid) : false
    );

    if ($status) {

        foreach ($secure_oid as $oid) {
            func_array2insert(
                'order_extras',
                array(
                    'orderid' => $oid,
                    'khash'   => 'xpc_txnid',
                    'value'   => $response['txnId'],
                ),
                true
            );
        }

        func_create_payment_form($response['url'], $response['fields'], $responses['module_name']);

        exit;

    } else {

        $bill_output['code'] = 2;
        $bill_output['billmes'] = 'Internal error';

        if (
            isset($response['detailed_error_message'])
            && !empty($response['detailed_error_message'])
        ) {

            $bill_output['billmes'] .= ' (' . $response['detailed_error_message'] . ')';

        }

    }

}

?>
