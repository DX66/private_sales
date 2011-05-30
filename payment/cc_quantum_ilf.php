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
 * Quantum Gateway - In Line Frame API
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (C) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_quantum_ilf.php,v 1.10.2.2 2011/01/10 13:12:07 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

// Uncomment the below line to enable the debug log
// define('QUANTUM_DEBUG', 1);

if (
    $_SERVER['REQUEST_METHOD'] == 'POST' 
    && isset($_GET['frame_refresh']) 
    && !empty($_POST['ip']) 
    && !empty($_POST['k'])
) {

    // Ajax request to refresh the session on a gateway server

    require './auth.php';

    if (!func_is_active_payment('cc_quantum_ilf.php'))
        exit;

    $fields = array(
        'ip' => $_POST['ip'],
        'k' => $_POST['k']
    );
    $post = func_http_build_query($fields);

    x_load('http');
    list($a, $return) = func_https_request('POST', 'https://secure.quantumgateway.com:443/cgi/ilf_refresh.php', $post);

    exit();

} elseif (
    isset($_GET['mode']) 
    && $_GET['mode'] == 'cancel'
) {

    require './auth.php';

    if (!func_is_active_payment('cc_quantum_ilf.php'))
        exit;

    $bill_output['code'] = 2;
    $bill_output['billmes'] = 'Canceled by the user';
    $bill_output['sessid'] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='" . addslashes($_GET['secureid']) . "'");

    require $xcart_dir . '/payment/payment_ccend.php';

} elseif (
    isset($_GET['trans_result'])
    && !empty($_GET['trans_result'])
    && isset($_GET['ID'])
    && !empty($_GET['ID'])
) {

    // Process the response

    require './auth.php';

    if (!func_is_active_payment('cc_quantum_ilf.php'))
        exit;

    if (defined('QUANTUM_DEBUG')) {
        func_pp_debug_log('quantum', 'R', $_GET);
    }

    $bill_output['sessid'] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref = '" . $ID . "'");

    if ($trans_result == 'APPROVED') {
        $bill_output['code'] = 1;
    } else {
        $bill_output['code'] = 2;
        $bill_output['billmes'] = 'Declined: ' . $decline_reason;
    }

    $bill_output['billmes'] .= "\n" . 'Transaction ID: ' . $transID;

    $is_iframe = true;
    require $xcart_dir . '/payment/payment_ccend.php';

} else {

    if (!defined('XCART_START')) { header('Location: ../'); die('Access denied'); }

    $order = $module_params['param04'] . join('-', $secure_oid);

    if (!$duplicate) {
        $insert_data = array(
            'ref'       => addslashes($order_secureid),
            'sessionid' => $XCARTSESSID,
            'param1'    => $order
        );
        func_array2insert('cc_pp3_data', $insert_data, true);
    }

    // Send initial request to obtain the key
    $fields = array(
        'API_Username' => $module_params['param01'],
        'API_Key'      => $module_params['param02'],
        'randval'      => md5(uniqid(rand())),
        'lastip'       => $_SERVER['REMOTE_ADDR']
    );

    x_load('http');
    $post = func_http_build_query($fields);
    list($a, $return) = func_https_request('POST', 'https://secure.quantumgateway.com:443/cgi/ilf_authenticate.php', $post);

    // Prepare data and show iframe
    if (!empty($return)) {
        $params = array(
            // General data
            'Amount' => $cart['total_cost'],
            'ID' => $order_secureid,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'k' => $return,
            // Billing info section
            'FNAME'        => $bill_firstname,
            'LNAME'        => $bill_lastname,
            'BADDR1'       => $userinfo['b_address'],
            'BCITY'        => $userinfo['b_city'],
            'BSTATE'       => $userinfo['b_state'],
            'BZIP1'        => $userinfo['b_zipcode'],
            'BCOUNTRY'     => $userinfo['b_country'],
            'phone'        => $userinfo['phone'],
            'BCUST_EMAIL'  => $userinfo['email'],
            // Shipping info section
            'SFNAME'       => $userinfo['firstname'],
            'SLNAME'       => $userinfo['lastname'],
            'SADDR1'       => $userinfo['s_address'],
            'SCITY'        => $userinfo['s_city'],
            'SSTATE'       => $userinfo['s_state'],
            'SZIP1'        => $userinfo['s_zipcode'],
            'SCOUNTRY'     => $userinfo['s_country']
        );

        // Skip shipping info section
        if ($module_params['param03'] != 'Y')
            $params['skip_shipping_info'] = 'Y';

        if (defined('QUANTUM_DEBUG')) {
            func_pp_debug_log('quantum', 'F', $params);
        }

        $iframe_src = 'https://secure.quantumgateway.com/cgi/ilf.php?' . func_http_build_query($params);
        $smarty->assign('ilf_src', $iframe_src);
        $smarty->assign('ilf_key', $return);
        $smarty->assign('ilf_ip', $_SERVER['REMOTE_ADDR']);
        $smarty->assign('cancel_url', 'cc_quantum_ilf.php?mode=cancel&secureid=' . $order_secureid);

        func_flush(func_display('payments/cc_quantum_ilf_frame.tpl', $smarty, false));
    }

    exit;
}
?>
