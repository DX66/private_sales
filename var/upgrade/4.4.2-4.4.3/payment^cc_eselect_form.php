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
 * "eSelect Plus - Hosted Paypage" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_eselect_form.php,v 1.16.2.2 2011/01/10 13:12:06 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!isset($REQUEST_METHOD)) {
    $REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
}

if ($REQUEST_METHOD == 'GET') {

    require './auth.php';

    if (!empty($_GET['response_order_id']) && !empty($_GET['response_code'])) {

        $bill_output['sessid'] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] where ref = '".$response_order_id."'");
        $bill_output['code'] = (($response_code < 50 && $response_code != 'null') ? 1 : 2);
        $bill_output['billmes'] = ($bill_output['code'] == 1) ? '' : $message;

        $es_card_types = array(
            'M'     => 'MasterCard',
            'V'     => 'Visa',
            'AX'    => "American Express",
            'DC'    => "Diners Card",
            'NO'    => "Novus / Discover",
            'SE'    => 'Sear',
            'null'  => 'Unknown',
        );

        if ($bank_transaction_id != 'null')    $bill_output['billmes'].= " (BankTransID: ".$bank_transaction_id.")";
        if ($bank_approval_code != 'null')     $bill_output['billmes'].= " (BankApproval: ".$bank_approval_code.")";
        if (!empty($transactionKey))           $bill_output['billmes'].= " (transactionKey: ".$transactionKey.")";
        if (!empty($txn_num))                  $bill_output['billmes'].= " (txn_num: ".$txn_num.")";
        if (!empty($f4l4))                     $bill_output['billmes'].= " (Card: ".$es_card_types[$card]." #".$f4l4.", Exp.: ".$expiry_date.")";

        require $xcart_dir.'/payment/payment_ccend.php';

    } elseif (!empty($_GET['cancelTXN'])) {

        func_header_location($http_location."/cart.php?mode=checkout");

    }

} else {

    if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

    $eselect_url = "https://".(($module_params['testmode'] == 'Y') ? 'esqa' : 'www3').'.moneris.com/HPPDP/index.php';
    $ordr = $module_params['param04'].join("-", $secure_oid);

    if (!$duplicate) {
        db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."')");
    }

    $post = array(
        'ps_store_id'               => $module_params['param01'],
        'hpp_key'                   => $module_params['param02'],
        'charge_total'              => number_format($cart['total_cost'], 2),
        'order_id'                  => $ordr,
        'cust_id'                   => $login,
        'email'                     => $userinfo['email'],

        'bill_first_name'           => $userinfo['firstname'],
        'bill_last_name'            => $userinfo['lastname'],
        'bill_address_one'          => $userinfo['b_address'],
        'bill_city'                 => $userinfo['b_city'],
        'bill_state_or_province'    => $userinfo['b_state'],
        'bill_postal_code'          => $userinfo['b_zipcode'],
        'bill_country'              => $userinfo['b_country'],
        'bill_phone'                => $userinfo['b_phone'],
        'bill_fax'                  => $userinfo['b_fax'],

        'ship_first_name'           => $userinfo['firstname'],
        'ship_last_name'            => $userinfo['lastname'],
        'ship_address_one'          => $userinfo['s_address'],
        'ship_city'                 => $userinfo['s_city'],
        'ship_state_or_province'    => $userinfo['s_state'],
        'ship_postal_code'          => $userinfo['s_zipcode'],
        'ship_country'              => $userinfo['s_country'],
        'ship_phone'                => $userinfo['s_phone'],
        'ship_fax'                  => $userinfo['s_fax'],

        'shipping_cost'             => number_format($cart['shipping_cost'], 2),
    );

    if ($userinfo["b_firstname"]) $post["bill_first_name"] = $userinfo["b_firstname"];
    if ($userinfo["b_lastname"])  $post["bill_last_name"] = $userinfo["b_lastname"]; 
    if ($userinfo["s_firstname"]) $post["ship_first_name"] = $userinfo["s_firstname"];
    if ($userinfo["s_lastname"])  $post["ship_last_name"] = $userinfo["s_lastname"];

    $counter = 1;

    $products = func_products_in_cart($cart, $userinfo['membershipid']);
    if (is_array($products)) {
        foreach ($products as $item) {
            $post['id'.$counter]             = $item['productcode'];
            $post['description'.$counter]    = $item['product'];
            $post['quantity'.$counter]       = $item['amount'];
            $post['price'.$counter]          = number_format($item['price'], 2);
            $post['subtotal'.$counter]       = number_format($item['price']*$item['amount'], 2);

            $counter++;
        }
    }

    if (is_array($cart['giftcerts'])) {
        foreach ($cart['giftcerts'] as $item) {
            $post['id'.$counter]             = 'GE';
            $post['description'.$counter]    = "Gift certificate";
            $post['quantity'.$counter]       = 1;
            $post['price'.$counter]          = number_format($item['amount'], 2);
            $post['subtotal'.$counter]       = number_format($item['amount'], 2);

            $counter++;
        }
    }

    func_create_payment_form($eselect_url, $post, "eSelect Plus (hosted page)");

    exit();

}

?>
