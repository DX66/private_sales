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
 * Checkout by Amazon
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: checkout_callback.php,v 1.8.2.5 2011/04/08 09:59:18 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

// IN $parsed

/**
 * Callback:
 * Calculate taxes, shipping and etc
 * Then store cart and transaction details
 */

// Check if no data passed
$_raw_posted_data = func_get_raw_post_data();
if (empty($_raw_posted_data)) {
    x_log_flag('log_payment_processing_errors', 'PAYMENTS', "Amazon checkout payment module: Script called with no data passed to it.", true);
    exit;
}

if (empty($parsed)) {
    x_log_flag('log_payment_processing_errors', 'PAYMENTS', "Amazon checkout payment module: Received data could not be identified correctly.", true);
    func_acheckout_debug("\t+ Amazon checkout payment module: Received data could not be identified correctly.");
    exit;
}

$type = key($parsed);

func_acheckout_debug("\t+ Sub-message: $type");

if ($type == 'ORDERCALCULATIONSREQUEST') {

    $skey = func_array_path($parsed, "$type/CALLBACKORDERCART/CARTCUSTOMDATA/REF/0/#");
    func_acheckout_debug("\t+ skey: $skey");

    // Restore the session/global vars
    func_acheckout_restore_session_n_global($skey);
    func_acheckout_debug("\t+ login: $login, logged_userid: $logged_userid");

    $response_xml = func_amazon_xml2_OrderCalculationsResponse($parsed);

    func_acheckout_debug("*** XML RESPONSE:\n\n" . $response_xml . "\n\n", true);
    func_acheckout_debug("\t+ Sending message: order-calculations-response");

    // Save amazon callback data

    // Data for reserve way to resolve shipping method
    $_allowed_shipping_methods = func_amazon_get_shipping_methods12($cart);
    $amazon_shipping_methods = array();

    if (!empty($_allowed_shipping_methods)) {
        foreach ($_allowed_shipping_methods as $v) {
            if (!empty($v['amazon_service'])) {
                if (
                    !array_key_exists($v['amazon_service'], $amazon_shipping_methods)
                    || $amazon_shipping_methods[$v['amazon_service']]['rate'] > $v['rate']
                ) {
                        $amazon_shipping_methods[$v['amazon_service']] = array('shippingid' => $v['shippingid'], 'rate' => $v['rate'], 'shipping' => $v['shipping']);
                }
            }
        }
    }
    $cart['amazon_shippings'] = $amazon_shipping_methods;

    @db_query("REPLACE INTO $sql_tbl[amazon_data] (ref,cart,sessionid) VALUES ('$skey','".addslashes(serialize($cart))."','$XCARTSESSID')");
    x_session_save();

    func_amazon_post_response($response_xml, 'order-calculations-response');
}

?>
