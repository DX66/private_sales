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
 * Last phase of the payment action - process response
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: payment_ccend.php,v 1.45.2.1 2011/01/10 13:12:08 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('crypt');

require $xcart_dir . '/payment/payment_ccmid.php';

x_session_register('cart');

if ($bill_error) {

    $request = $current_location
        . DIR_CUSTOMER
        . "/error_message.php?"
        . $sessurl
        . "error="
        . $bill_error
        . $reason;

} else {

    // If successful - Store CC number in database

    if (
        $store_cc
        && !empty($card_type)
        && !empty($card_number)
        && !empty($card_expire)
        && !empty($login)
    ) {

        $query_data = array(
            'card_name'       => $card_name,
            'card_type'       => $card_type,
            'card_number'     => addslashes(text_crypt($card_number)),
            'card_expire'     => $card_expire,
            'card_valid_from' => $card_valid_from,
            'card_issue_no'   => $card_issue_no,
        );

        if ($store_cvv2) {

            $query_data['card_cvv2'] = addslashes(text_crypt($card_cvv2));

        }

        func_array2update(
            'customers',
            $query_data,
            "id = '$logged_userid' AND usertype = '$login_type'"
        );

    }

    if (
        empty($cart['split_query'])
        && $bill_output['code'] != 5
    ) {

        $request = $xcart_catalogs['customer']
            . '/cart.php?'
            . $sessurl
            . 'mode=order_message&orderids='
            . $_orderids;

        $cart = '';

    } else {

        $top_message = array(
            'type'      => 'I',
            'content'   => func_get_langvar_by_name('txt_order_was_partially_paid'),
        );

        $request = $xcart_catalogs['customer']
            . '/cart.php?mode=checkout&paymentid=' . $paymentid;

    }

    x_load('paypal');

    func_paypal_clear_ec_token();

    x_session_save();

    if (!empty($active_modules['SnS_connector'])) {

        func_generate_sns_action('CartChanged');

    }
}

require $xcart_dir . '/payment/payment_ccredirect.php';

?>
