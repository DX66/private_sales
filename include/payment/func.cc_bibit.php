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
 * General functions for "RBS WorldPay - Global Gateway" payment module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.cc_bibit.php,v 1.1.2.4 2011/03/29 07:38:48 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load('http');

/**
 * Send request to RBS WorldPay gateway
 *
 * @param mixed $PaRes    PaRes value (if available)
 * @param mixed $echoData echoData (if available)
 *
 * @return string 
 * @see    ____func_see____
 */
function func_cc_bibit_request($PaRes = null, $echoData = null)
{
    global $userinfo, $cart, $secure_oid, $module_params, $config, $REMOTE_ADDR, $XCARTSESSID;

    $merchant_id = $module_params['param01'];
    $password    = $module_params['param02'];
    $posturl = ($module_params['testmode']=="Y" ? "-test" : '');
    $x_curr  = $module_params['param05'];
    $x_cexp  = 2;

    if (
        $x_curr == "HUF"
        || $x_curr == "IDR"
        || $x_curr == "JPY"
        || $x_curr == "KRW"
    ) {
        $x_cexp = 0;
    }

    $first4 = 0+substr($userinfo['card_number'], 0, 4);
    if($first4>=4000 && $first4<=4999)$userinfo['card_type']="VISA-SSL"; // VISA
    if($first4>=5100 && $first4<=5999)$userinfo['card_type']="ECMC-SSL"; // MasterCard
    if($first4>=3400 && $first4<=3499)$userinfo['card_type']="AMEX-SSL"; // AmericanExpress
    if($first4>=3700 && $first4<=3799)$userinfo['card_type']="AMEX-SSL"; // AmericanExpress
    if($first4>=3000 && $first4<=3059)$userinfo['card_type']="DINERS-SSL"; // Diners
    if($first4>=3600 && $first4<=3699)$userinfo['card_type']="DINERS-SSL"; // Diners
    if($first4>=3800 && $first4<=3889)$userinfo['card_type']="DINERS-SSL"; // Diners
    if($first4==6011)$userinfo['card_type']="DISCOVER-SSL"; // Discover
    if($first4>=3528 && $first4<=3589)$userinfo['card_type']="JCB-SSL"; // JCB

    $post = array();
    $post[] = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>";
    $post[] = "<!DOCTYPE paymentService PUBLIC \"-//Bibit/DTD Bibit PaymentService v1//EN\" \"http://dtd.wp3.rbsworldpay.com/paymentService_v1.dtd\">";
    $post[] = "<paymentService version=\"1.4\" merchantCode=\"".$merchant_id."\">";
    $post[] = "<submit>";
    $post[] = "<order orderCode=\"".$module_params['param03'].join("-", $secure_oid)."\">";
    $post[] = "<description>".$config['Company']['company_name']."</description>";
    $post[] = "<amount value=\"".(100*$cart['total_cost'])."\" currencyCode=\"".$x_curr."\" exponent=\"".$x_cexp."\"/>";
    $post[] = "<orderContent>";
    $post[] = "<![CDATA[";
    $post[] = "<center>";
    $post[] = "</center>";
    $post[] = "]]>";
    $post[] = "</orderContent>";
    $post[] = "<paymentDetails>";
    $post[] = "<".$userinfo['card_type'].">";
    $post[] = "<cardNumber>".$userinfo['card_number']."</cardNumber>";
    $post[] = "<expiryDate><date month=\"".substr($userinfo['card_expire'],0,2)."\" year=\"".(2000+substr($userinfo['card_expire'],2,2))."\" /></expiryDate>";
    $post[] = "<cardHolderName>".htmlspecialchars($userinfo['card_name'])."</cardHolderName>";
    $post[] = "<cvc>".$userinfo['card_cvv2']."</cvc>";
    $post[] = "<cardAddress><address>";
    $post[] = "<firstName>".htmlspecialchars($userinfo['b_firstname'])."</firstName>";
    $post[] = "<lastName>".htmlspecialchars($userinfo['b_lastname'])."</lastName>";
    $post[] = "<street>".htmlspecialchars($userinfo['b_address'])."</street>";
    $post[] = "<postalCode>".htmlspecialchars($userinfo['b_zipcode'])."</postalCode>";
    $post[] = "<city>".htmlspecialchars($userinfo['b_city'])."</city>";
    $post[] = "<countryCode>".htmlspecialchars($userinfo['b_country'])."</countryCode>";
    $post[] = "<telephoneNumber>".htmlspecialchars($userinfo['b_phone'])."</telephoneNumber>";
    $post[] = "</address></cardAddress>";
    $post[] = "</".$userinfo['card_type'].">";
    $post[] = "<session shopperIPAddress=\"".func_get_valid_ip($REMOTE_ADDR)."\" id=\"".$XCARTSESSID."\" />";
    if (!is_null($PaRes)) {
        $post[] = "<info3DSecure>";
        $post[] = "<paResponse>".$PaRes."</paResponse>";
        $post[] = "</info3DSecure>";
    }
    $post[] = "</paymentDetails>";
    $post[] = "<shopper>";
    if (!empty($userinfo['login'])) {
        $post[] = "<shopperEmailAddress>".$userinfo['email']."</shopperEmailAddress> <authenticatedShopperID>".htmlspecialchars($userinfo['login'])."</authenticatedShopperID>";
    } else {
        $post[] = "<shopperEmailAddress>".$userinfo['email']."</shopperEmailAddress>";
    }
    $post[] = "<browser>";
    $post[] = "<acceptHeader>text/html</acceptHeader>";
    $post[] = "<userAgentHeader>".$_SERVER['HTTP_USER_AGENT']."</userAgentHeader>";
    $post[] = "</browser>";
    $post[] = "</shopper>";
    $post[] = "<shippingAddress>";
    $post[] = "<address>";
    $post[] = "<firstName>".htmlspecialchars($userinfo['s_firstname'])."</firstName>";
    $post[] = "<lastName>".htmlspecialchars($userinfo['s_lastname'])."</lastName>";
    $post[] = "<street>".htmlspecialchars($userinfo['s_address'])."</street>";
    $post[] = "<postalCode>".htmlspecialchars($userinfo['s_zipcode'])."</postalCode>";
    $post[] = "<city>".htmlspecialchars($userinfo['s_city'])."</city>";
    $post[] = "<countryCode>".htmlspecialchars($userinfo['s_country'])."</countryCode>";
    $post[] = "<telephoneNumber>".htmlspecialchars($userinfo['s_phone'])."</telephoneNumber>";
    $post[] = "</address>";
    $post[] = "</shippingAddress>";
    if (!is_null($echoData)) {
        $post[] = "<echoData>".$echoData."</echoData>";
    }
    $post[] = "</order>";
    $post[] = "</submit>";
    $post[] = "</paymentService>";

    if (defined('WORLDPAY_GLOBAL_GATEWAY_DEBUG'))
        func_pp_debug_log('worldpay_global_gateway', 'I', $post);

    list($a, $return) = func_https_request('POST', "https://".urlencode($merchant_id).":".urlencode($password)."@secure".$posturl.".bibit.com:443/jsp/merchant/xml/paymentService.jsp", $post, '', '', 'text/xml');

    if (defined('WORLDPAY_GLOBAL_GATEWAY_DEBUG'))
        func_pp_debug_log('worldpay_global_gateway', 'R', print_r($a, true) . print_r($return, true));

    // Parse response
    return array($a, $return);
}

/**
 * Check whether gateway response contains the error message
 *
 * @param mixed $headers  HTTP headers
 * @param mixed $response Gateway response
 * @param mixed $error    buffer for error info
 *
 * @return bool 
 * @see    ____func_see____
 */
function func_cc_bibit_error($headers, $response, &$error)
{
    $error_codes = array(
        '0'  => 'AUTHORISED',
        '2'  => 'REFERRED',
        '4'  => "HOLD CARD",
        '5'  => 'REFUSED',
        '8'  => "APPROVE AFTER IDENTIFICATION",
        '13' => "INVALID AMOUNT",
        '15' => "INVALID CARD ISSUER",
        '17' => "ANNULATION BY CLIENT",
        '28' => "ACCESS DENIED",
        '29' => "IMPOSSIBLE REFERENCE NUMBER",
        '33' => "CARD EXPIRED",
        '34' => "FRAUD SUSPICION",
        '38' => "SECURITY CODE EXPIRED",
        '41' => "LOST CARD",
        '43' => "STOLEN CARD, PICK UP",
        '51' => "LIMIT EXCEEDED",
        '55' => "INVALID SECURITY CODE",
        '56' => "UNKNOWN CARD",
        '57' => "ILLEGAL TRANSACTION",
        '62' => "RESTRICTED CARD",
        '63' => "SECURITY RULES VIOLATED",
        '75' => "SECURITY CODE INVALID",
        '76' => "CARD BLOCKED",
        '85' => "REJECTED BY CARD ISSUER",
        '91' => "CREDITCARD ISSUER TEMPORARILY NOT REACHABLE",
        '97' => "SECURITY BREACH",
        '3'  => "INVALID ACCEPTOR",
        '12' => "INVALID TRANSACTION",
        '14' => "INVALID ACCOUNT",
        '19' => "REPEAT OF LAST TRANSACTION",
        '20' => "ACQUIRER ERROR",
        '21' => "REVERSAL NOT PROCESSED, MISSING AUTHORISATION",
        '24' => "UPDATE OF FILE IMPOSSIBLE",
        '25' => "REFERENCE NUMBER CANNOT BE FOUND",
        '26' => "DUPLICATE REFERENCE NUMBER",
        '27' => "ERROR IN REFERENCE NUMBER FIELD",
        '30' => "FORMAT ERROR",
        '31' => "UNKNOWN ACQUIRER ACCOUNT CODE",
        '40' => "REQUESTED FUNCTION NOT SUPPORTED",
        '58' => "TRANSACTION NOT PERMITTED",
        '64' => "AMOUNT HIGHER THAN PREVIOUS TRANSACTION AMOUNT",
        '68' => "TRANSACTION TIMED OUT",
        '80' => "AMOUNT NO LONGER AVAILABLE, AUTHORISATION EXPIRED",
        '92' => "CREDITCARD TYPE NOT PROCESSED BY ACQUIRER",
        '94' => "DUPLICATE REQUEST"
    );

    if (preg_match("/401\s+Authorization\s+Required/is", $headers)) {
        // Unauthorized
        $error = array(
            'code' => '',
            'message' => 'Authorization required'
        );
        return true;
    }

    if (preg_match("/(<error.*)<\/error>/U", $response, $o)) {
        $response = $o[1];
        preg_match("/<!\[CDATA\[(.*)\]\]/U", $response, $err);
        preg_match("/<error code=\"(.*)\">/U", $response, $code);
        $error = array(
            'code' => $code[1] . (array_key_exists((string)$code[1], $error_codes) ? ' (' . $error_codes[(string)$code[1]] . ')' : ''),
            'message' => $err[1]
        );
        return true;
    }

    return false;
}

?>
