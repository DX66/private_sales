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
 * InternetSecure
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_isecure.php,v 1.48.2.1 2011/01/10 13:12:06 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ($_SERVER['REQUEST_METHOD'] == "GET" && isset($_GET["ok"]) && isset($_GET["ordr"])) {
    require './auth.php';

    $bill_output['sessid'] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$ordr."'");
    $bill_output['code'] = $ok == 'yes' ? 1 : 2;

    require($xcart_dir.'/payment/payment_ccend.php');

} else {

    if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

    $accid = $module_params ['param01'];
    switch ($module_params ['testmode']) {
        case 'A':
            $test = "{TEST}";
            break;

        case 'D':
            $test = "{TESTD}";
            break;

        default:
            $test = '';
    }

    $prefix = $module_params ['param03'];
    $curr = $module_params ['param04'];
    $ordr = $prefix.join('-', $secure_oid);

    if (!$duplicate)
        db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."')");

    $p_array = array();
    $flags = '';
    if ($curr == 'US')
        $flags .= "{US}";

    $flags .= $test;

    if ($cart['products']) {
        foreach($cart['products'] as $p) {
            $p_array[] = $p['price']."::".$p['amount']."::".$p['productcode']."::".str_replace("::", " ", $p['product'])."::".$flags;
        }
    }

    if ($cart['giftcerts']) {
        foreach($cart['giftcerts'] as $g) {
            $p_array[] = $g['amount']."::1::GC::GiftCertificate::".$flags;
        }
    }

    $shipping_method = func_query_first_cell("SELECT shipping FROM $sql_tbl[shipping] WHERE shippingid='$cart[shippingid]'");
    if (empty($shipping_method)) {
         $shipping_method = 'Shipping';
    } else {
        $shipping_method = 'Shipping - '.$shipping_method;
    }

    if ($cart['coupon_discount'] > 0 && $cart['coupon_type'] == "free_ship")
        $_shipping_cost = $cart['coupon_discount'];
    else
        $_shipping_cost = $cart['shipping_cost'];

    $p_array[] = $_shipping_cost."::1::::".$shipping_method."::".$flags;

    // taxes
    $taxes_cost = $cart['tax_cost'];
    if ($taxes_cost != 0)
        $p_array[] = $taxes_cost."::1::::Tax::".$flags;

    // discounts
    $p_array[] = (-$cart['coupon_discount'])."::1::::Coupon discount::".$flags;
    $p_array[] = (-$cart['discount'])."::1::::Discount::" . $flags;

    // applied giftcerts
    if ($cart['applied_giftcerts']) {
        foreach ($cart['applied_giftcerts'] as $k=>$v) {
            $p_array[] = ($v['giftcert_cost'] * -1) . "::1::::Applied GiftCertificate #" . $v['giftcert_id'] . "::" . $flags;
        }
    }

    $fields = array(
          'MerchantNumber' => $accid,
        'xxxName' => $bill_name,
        'xxxCompany' => $userinfo["company"],
        'xxxAddress' => $userinfo["b_address"],
        'xxxCity' => $userinfo["b_city"],
        'xxxProvince' => $userinfo["b_state"],
        'xxxCountry' => $userinfo["b_country"],
        'xxxPostal' => $userinfo["b_zipcode"],
        'xxxEmail' => $userinfo["email"],
        'xxxPhone' => $userinfo["phone"],
          'language' => 'English',
        'ReturnURL' => $current_location . '/payment/cc_isecure.php?ordr=' . $ordr . '&ok=yes',
        'Products' => implode('|', $p_array)
    );

    func_create_payment_form('https://secure.internetsecure.com/process.cgi', $fields, 'InternetSecure');
}
exit;

?>
