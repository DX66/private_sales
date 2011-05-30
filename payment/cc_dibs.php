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
 * "DIBS (FlexWin)" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_dibs.php,v 1.15.2.1 2011/01/10 13:12:05 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/**
 * DIBS - FlexWin method
 */

// Uncomment the below line to enable the debug log
// define('DIBS_DEBUG', 1);

if (!empty($orderid) && !empty($result) && in_array($result, array('accept', 'cancel'))) {

    // Process the gateway response (POST or GET)

    require './auth.php';

    if (!func_is_active_payment('cc_dibs.php'))
        exit;

    $skey = $orderid;

    $response = $_SERVER['REQUEST_METHOD'] == 'POST' ? $_POST : $_GET;
    if (defined('DIBS_DEBUG')) {
        func_pp_debug_log('dibs', 'R', $response);
    }

    if ($result == 'cancel') {
        $bill_output['code'] = 2;
        $bill_output['billmes'] = 'Canceled by the user';
        require ( $xcart_dir . '/payment/payment_ccend.php' );
    }
    else {
        require ( $xcart_dir . '/payment/payment_ccview.php' );
    }
}
elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['orderid'])) {

    // Process the callback

    require './auth.php';

    if (!func_is_active_payment('cc_dibs.php'))
        exit;

    if (defined('DIBS_DEBUG')) {
        func_pp_debug_log('dibs', 'C', $_POST);
    }

    $skey = $_POST['orderid'];
    $bill_output['sessid'] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='" . $skey . "'");

    $module_params = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor='cc_dibs.php'");

    $order_id         = $_POST['orderid'];
    $dibs_transid     = $_POST['transact'];
    $amount         = $_POST['amount'];
    $fee             = $_POST['fee'];
    $currency         = $_POST['currency'];
    $authkey         = $_POST['authkey'];
    $paytype         = $_POST['paytype'];
    $cardnomask     = $_POST['cardnomask'];

    if($module_params['param08'] == 'Y') {
        $amount += $fee;
    }

    $skey1  = $module_params['param06'];
    $skey2  = $module_params['param07'];
    $md5key    = md5($skey2 . md5($skey1 . 'transact=' . $dibs_transid . '&amount=' . $amount . '&currency=' . $currency));

    $dibs_payment_info = array();
    $dibs_payment_info[] = 'DIBS Trans #: ' . $dibs_transid;

    if ($paytype != '') {
        $dibs_payment_info[] = 'Paytype: ' . $paytype;
    }

    if ($cardnomask != '') {
        $dibs_payment_info[] = 'Card number: ' . $cardnomask;
    }

    $is_valid_sign =  ($skey2 != '' && $skey1 != '' && $md5key == $authkey);

    if ($is_valid_sign) {
        $bill_output['code'] = 1;
        $bill_message = 'Accepted';
        $bill_output['is_preauth'] = $module_params['use_preauth'] == 'Y';
        $payment_return = array(
            'cost'      => $amount/100,
            'currency'  => $currency,
            '_currency' => $module_params['param02']
        );

    } else {
        $bill_output['code'] = 3;
        $bill_message = 'Declined (processor error)';
    }

    $bill_output['billmes'] = join("\n", $dibs_payment_info);

    require $xcart_dir . '/payment/payment_ccmid.php';
    require $xcart_dir . '/payment/payment_ccwebset.php';

}
else {

    // Prepare the form and send a POST request

    if (!defined('XCART_START')) { header('Location: ../'); die('Access denied'); }

    // Prepare necessary variables

    $merchant = $module_params['param01'];
    $currency = $module_params['param02'];
    $language = $module_params['param03'];
    $order      = $module_params['param04'] . join('-', $secure_oid);
    $paytype  = $module_params['param05'];
    $skey1      = $module_params['param06'];
    $skey2      = $module_params['param07'];
    $calcfee  = $module_params['param08'];
    $amount   = number_format(((float)($cart['total_cost'])), 2, '', '');

    // Calculate secure md5 hash
    $md5key   = md5($skey2 . md5($skey1 . 'merchant=' . $merchant . '&orderid=' . $order . '&currency=' . $currency . '&amount='.$amount));

    // Define URLs

    $qs   = '?result=';
    $url  = $current_location . '/payment/cc_dibs.php';

    $callback_url = $url;
    $cancel_url   = $url . $qs . 'cancel';
    $accept_url   = $url . $qs . 'accept';

    // Prepare the array with posted data

    $post = array(
        'merchant'         => $merchant,
        'orderid'         => $order,
        'uniqueoid'     => $order,
        'amount'         => $amount,
        'currency'         => $currency,
        'lang'             => $language,
        'cancelurl'     => $cancel_url,
        'callbackurl'     => $callback_url,
        'accepturl'     => $accept_url
    );

    if ($skey1 != '' && $skey2 != '') {
        $post['md5key'] = $md5key;
    }

    if ($module_params['testmode'] == 'Y') {
        $post['test'] = 'Y';
    }

    if ($paytype != '') {
        $post['paytype'] = $paytype;
    }

    if ($calcfee == 'Y') {
        $post['calcfee'] = $calcfee;
    }

    if ($module_params['use_preauth'] != 'Y') {
        $post['capturenow'] = 'Y';
    }

    $post['delivery01.Firstname']     = $userinfo['s_firstname'];
    $post['delivery02.Lastname']     = $userinfo['s_firstname'];
    $post['delivery03.Address']     = $userinfo['s_address'];
    $post['delivery04.City']         = $userinfo['s_city'];
    $post['delivery05.Country']     = $userinfo['s_country'];
    $post['delivery06.Email']         = $userinfo['email'];
    $post['delivery07.Phone1']         = $userinfo['phone'];
    $post['delivery07.Phone2']         = $userinfo['fax'];
    $post['delivery09.Comment']     = $userinfo['Customer_Notes'];
    $post['ordline0-1'] = 'SKU';
    $post['ordline0-2'] = 'Description';
    $post['ordline0-3'] = 'Quantity';
    $post['ordline0-4'] = 'Price';

    foreach ($products as $i => $p) {
        $post[sprintf('ordline%d-1', $i+1)] = $p['productcode'];
        $post[sprintf('ordline%d-2', $i+1)] = $p['product'];
        $post[sprintf('ordline%d-3', $i+1)] = $p['amount'];
        $post[sprintf('ordline%d-4', $i+1)] = $p['display_subtotal'];
    }

    $pk = 1;
    if ($cart['shipping_cost'] > 0)
        $post['priceinfo' . $pk++ . '.Shipping'] = $cart['shipping_cost'];

    if ($cart['tax_cost'] > 0)
        $post['priceinfo' . $pk++ . '.Tax'] = $cart['tax_cost'];

    if ($cart['discount'] > 0)
        $post['priceinfo' . $pk++ . '.Discount'] = $cart['discount'];

    if ($cart['coupon_discount'] > 0)
        $post['priceinfo' . $pk++ . '.DicountCoupon'] = $cart['coupon_discount'];

    if ($cart['giftcert_discount'] > 0)
        $post['priceinfo' . $pk++ . '.GiftcertDiscount'] = $cart['giftcert_discount'];

    if (defined('DIBS_DEBUG')) {
        func_pp_debug_log('quantum', 'I', $post);
    }

    if (!$duplicate)
        db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($order)."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");

    func_create_payment_form('https://payment.architrade.com/paymentweb/start.action', $post, 'DIBS');
}

exit;

?>
