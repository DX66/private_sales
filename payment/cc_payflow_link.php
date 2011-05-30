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
 * PayFlow Link
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_payflow_link.php,v 1.16.2.1 2011/01/10 13:12:07 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST["RESULT"]) && isset($_POST["RESPMSG"])) {
    require './auth.php';

    $skey = $INVOICE;
    require($xcart_dir.'/payment/payment_ccview.php');

} else {

    if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

    $pp_login = $module_params['param01'];
    $pp_partner = $module_params['param02'];
    $prefix = $module_params['param03'];
    $userinfo['phone'] = preg_replace("/[^0-9]/",'',$userinfo['phone']);

    $prods = '';
    if ($products) {
        foreach ($products as $product) {
            if (isset($product['display_subtotal']))
                $subtotal = $product['display_subtotal'];
            elseif (isset($product['discounted_price']))
                $subtotal = $product['discounted_price'];
            else
                $subtotal = $product['price'] * $product['amount'];

            $prods .= str_replace('"', "'", $product["product"])."(".price_format($subtotal)."); ";
        }
    }

    if (@is_array($cart['giftcerts']) && count($cart['giftcerts'])>0) {
        foreach ($cart['giftcerts'] as $tmp_gc)
            $prods .= "GIFT CERTIFICATE(".price_format($tmp_gc['amount'])."); ";
    }

    if (strlen($prods) > 200)
        $prods = substr($prods, 0, 200) . '...';

    $_orderids = $prefix . join('-', $secure_oid);

    if (!$duplicate)
        db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($_orderids)."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");

    $fields = array(
        'login' => $pp_login,
        'partner' => $pp_partner,
        'amount' => $cart["total_cost"],
        'type' => 'S',
        'description' => $prods,
          'name' => $bill_name,
        'address' => $userinfo["b_address"] . ' ' . $userinfo["b_address_2"],
        'city' => $userinfo["b_city"],
        'state' => $userinfo["b_state"],
        'zip' => $userinfo["b_zipcode"],
        'country' => $userinfo["b_country"],
        'phone' => $userinfo["phone"],
        'method' => 'cc',
        'orderform' => 'true',
          'nametoship' => $userinfo["s_firstname"] . ' ' . $userinfo["s_lastname"],
        'email' => $userinfo["email"],
        'emailtoship' => $userinfo["email"],
        'phonetoship' => $userinfo["phone"],
        'invoice' => $_orderids,
        'addresstoship' => $userinfo["s_address"] . ' ' . $userinfo["s_address_2"],
        'citytoship' => $userinfo["s_city"],
        'countrytoship' => $userinfo["s_country"],
        'statetoship' => $userinfo["s_state"],
        'ziptoship' => $userinfo["s_zipcode"],
        'echodata' => 'true',
        'showconfirm' => 'false',
        'buttonsource' => 'X-Cart_Cart_PFLINK'
    );

    func_create_payment_form('https://payflowlink.paypal.com', $fields, 'PayFlow Link');
}

exit;

?>
