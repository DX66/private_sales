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
 * Common functions for "SagePay" payment modules
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.cc_protx_common.php,v 1.18.2.1 2011/01/10 13:11:53 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

/**
 * Common functions used in the Sage Pay payment modules
 */
// The functions below are based on the examples from the PHP Integration
// Kits, which were downloaded from the official Sage Pay website www.sagepay.com.
// The original code was adapted to fit the X-Cart architecture.

// Filters unwanted characters out of an input string.  Useful for tidying up FORM field inputs.
function cleanInput($strRawText, $strType, $maxChars=false, $customPattern=false)
{

    switch ($strType) {
        case 'Number':
            $strClean = '0123456789.';
            $bolHighOrder = false;
            break;
        case 'Digits':
            $strClean = '0123456789';
            $bolHighOrder = false;
            break;
        case 'Text':
              $strClean =" ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789.,'/{}@():?-_&£$=%~<>*+\"";
            $bolHighOrder = true;
            break;
        case 'Custom':
            $strClean = $customPattern;
            $bolHighOrder = false;
            break;
        default:
            break;
    }

    $strCleanedText="";
    $iCharPos = 0;

    do
        {
            // Only include valid characters
            $chrThisChar=substr($strRawText,$iCharPos,1);

            if (strspn($chrThisChar,$strClean,0,strlen($strClean))>0) {
                $strCleanedText=$strCleanedText . $chrThisChar;
            }
            else if ($bolHighOrder==true) {
                // Fix to allow accented characters and most high order bit chars which are harmless
                if (bin2hex($chrThisChar)>=191) {
                    $strCleanedText=$strCleanedText . $chrThisChar;
                }
            }

        $iCharPos=$iCharPos+1;
        }
    while ($iCharPos<strlen($strRawText));

      $cleanInput = ltrim($strCleanedText);

    if ($maxChars && strlen($cleanInput) > $maxChars)
        $cleanInput = substr($cleanInput, 0, $maxChars);

    return $cleanInput;

}

/* Base 64 Encoding function **
** PHP does it natively but just for consistency and ease of maintenance, let's declare our own function **/

function base64Encode($plain)
{
    // Initialise output variable
    $output = '';

    // Do encoding
    $output = base64_encode($plain);

    // Return the result
    return $output;
}

/* Base 64 decoding function **
** PHP does it natively but just for consistency and ease of maintenance, let's declare our own function **/

function base64Decode($scrambled)
{
    // Initialise output variable
    $output = '';

    // Fix plus to space conversion issue
    $scrambled = str_replace(" ","+",$scrambled);

    // Do encoding
    $output = base64_decode($scrambled);

    // Return the result
    return $output;
}

/*  The SimpleXor encryption algorithm                                                                                **
**  NOTE: This is a placeholder really.  Future releases of Form will use AES or TwoFish.  Proper encryption          **
**  This simple function and the Base64 will deter script kiddies and prevent the "View Source" type tampering        **
**  It won't stop a half decent hacker though, but the most they could do is change the amount field to something     **
**  else, so provided the vendor checks the reports and compares amounts, there is no harm done.  It's still          **
**  more secure than the other PSPs who don't both encrypting their forms at all                                      */

function simpleXor($InString, $Key)
{

    // Initialise key array
    $KeyList = array();

    // Initialise out variable
    $output = '';

    // Convert $Key into array of ASCII values
    for($i = 0; $i < strlen($Key); $i++){
        $KeyList[$i] = ord(substr($Key, $i, 1));
    }

    // Step through string a character at a time
    for($i = 0; $i < strlen($InString); $i++) {
        // Get ASCII code from string, get ASCII code from key (loop through with MOD), XOR the two, get the character from the result
        // % is MOD (modulus), ^ is XOR
        $output.= chr(ord(substr($InString, $i, 1)) ^ ($KeyList[$i % strlen($Key)]));
    }

    // Return the result
    return $output;
}

/**
 * Common functions to check and tide up the values
 */

/**
 * Function tides up the values in accordance with the fields
 * specification
 */
function func_sagepay_clean_inputs($data)
{
    $fields_specs = func_sagepay_get_allowed_fields();

    foreach ($fields_specs as $field => $spec) {
        if (!isset($data[$field]) || isset($spec['skip']))
            continue;

        if (isset($fields_specs[$field]['allowed_values'])) {
            if ( !in_array($data[$field], $spec['allowed_values'])) {
                func_unset($data, $field);
            }
            continue;
        }
        $pattern = ($spec['filter'] == 'Custom') ? $spec['pattern'] : false;
        $data[$field] = cleanInput($data[$field], $spec['filter'], $spec['max'], $pattern);
    }

    $_data = array();
    foreach($data as $k => $v) {
        $_data[] = $k."=".$v;
    }

    return $_data;
}

/**
 * Function returns an array of allowed fields
 *  max: max length of the string for Text and Digits filters,
 *  filter: filter to be applied in the cleanInput function
 *  pattern: pattern for Custom filter
 *  skip: skip checking of this input, since it is already perfomed in X-Cart
 */
function func_sagepay_get_allowed_fields()
{

    $fields_specification = array(

        'VendorTxCode' => array(
            'max' => 40,
            'filter' => 'Custom',
            'pattern' => "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_."
        ),
        'Amount' => array(
            'skip' => true,
        ),
        'Currency' => array(
            'skip' => true
        ),
        'Description' => array(
            'max' => 100,
            'filter' => 'Text'
        ),
        'SuccessURL' => array(
            'max' => 2000,
            'filter' => 'Text'
        ),
        'FailureURL' => array(
            'max' => 2000,
            'filter' => 'Text'
        ),
        'CustomerName' => array(
            'max' => 100,
            'filter' => 'Text'
        ),
        'CustomerEMail' => array(
            'max' => 255,
            'filter' => 'Text'
        ),
        'VendorEMail' => array(
            'max' => 255,
            'filter' => 'Text'
        ),
        'SendEMail' => array(
            'allowed_values' => array(0,1,2,3)
        ),
        'eMailMessage' => array(
            'max' => 7500,
            'filter' => 'Text'
        ),
        'BillingSurname' => array(
            'max' => 20,
            'filter' => 'Text'
        ),
        'BillingFirstnames' => array(
            'max' => 20,
            'filter' => 'Text'
        ),
        'BillingAddress1' => array(
            'max' => 100,
            'filter' => 'Text'
        ),
        'BillingAddress2' => array(
            'max' => 100,
            'filter' => 'Text'
        ),
        'BillingCity' => array(
            'max' => 40,
            'filter' => 'Text'
        ),
        'BillingPostCode' => array(
            'max' => 10,
            'filter' => 'Text'
        ),
        'BillingCountry' => array(
            'skip' => true
        ),
        'BillingState'=> array(
            'skip' => true
        ),
        'BillingPhone' => array(
            'max' => 20,
            'filter' => 'Text'
        ),
        'DeliverySurname' => array(
            'max' => 20,
            'filter' => 'Text'
        ),
        'DeliveryFirstnames' => array(
            'max' => 20,
            'filter' => 'Text'
        ),
        'DeliveryAddress1' => array(
            'max' => 100,
            'filter' => 'Text'
        ),
        'DeliveryAddress2' => array(
            'max' => 100,
            'filter' => 'Text'
        ),
        'DeliveryCity' => array(
            'max' => 40,
            'filter' => 'Text'
        ),
        'DeliveryPostCode' => array(
            'max' => 10,
            'filter' => 'Text'
        ),
        'DeliveryCountry' => array(
            'skip' => true
        ),
        'DeliveryState' => array(
            'skip' => true
        ),
        'DeliveryPhone' => array(
            'max' => 20,
            'filter' => 'Text'
        ),
        'Basket' => array(
            'max' => 7500,
            'filter' => 'Text'
        ),
        'AllowGiftAid' => array(
            'allowed_values' => array('0','1')
        ),
        'ApplyAVSCV2' => array(
            'allowed_values' => array('0','1','2','3')
        ),
        'Apply3DSecure' => array(
            'allowed_values' => array('0','1','2','3')
        ),
        'TxType' => array(
            'allowed_values' => array('PAYMENT','DEFERRED','AUTHENTICATE','RELEASE','AUTHORISE','CANCEL','ABORT','MANUAL','REFUND','REPEAT','REPEATDEFERRED','VOID','PREAUTH','COMPLETE')
        ),
        'NotificationURL' => array(
            'max' => 255,
            'filter' => 'Text'
        ),
        'Vendor' => array(
            'max' => 15,
            'filter' => 'Text'
        ),
        'Profile' => array(
            'allowed_values' => array('LOW','NORMAL')
        ),
        'CardHolder' => array(
            'max' => 50,
            'filter' => 'Text'
        ),
        'CardNumber' => array(
            'max' => 20,
            'filter' => 'Digits'
        ),
        'StartDate' => array(
            'max' => 4,
            'filter' => 'Digits'
        ),
        'ExpiryDate' => array(
            'max' => 4,
            'filter' => 'Digits'
        ),
        'IssueNumber' => array(
            'max' => 2,
            'filter' => 'Digits'
        ),
        'CV2' => array(
            'max' => 4,
            'filter' => 'Digits'
        ),
        'CardType' => array(
            'allowed_values' => array('VISA','MC','DELTA','SOLO','MAESTRO','UKE','AMEX','DC','JCB','LASER','PAYPAL')
        ),
        'PayPalCallbackURL' => array(
            'max' => 255,
            'filter' => 'Text'
        ),
        'GiftAidPayment' => array(
            'allowed_values' => array('0','1')
        ),
        'ClientIPAddress' => array(
            'max' => 15,
            'filter' => 'Text'
        ),
        'MD' => array(
            'max' => 35,
            'filter' => 'Text'
        ),
        'PARes' => array(
            'max' => 7500,
            'filter' => 'Text'
        ),
        'VPSTxID' => array(
            'max' => 38,
            'filter' => 'Text'
        ),
        'Accept' => array(
            'allowed_values' => array('Yes','No')
        ),
        'Crypt' => array(
            'max' => 16384,
            'filter' => 'Text'
        ),
        'AccountType' => array(
            'allowed_values' => array('E','M','C')
        )
    );

    return $fields_specification;
}

/**
 * Format cart information for Protx payment methods.
 */
function func_cc_protx_get_basket()
{
    global $cart, $config;

    $cnt = 0;
    $basket = '';

    // Products
    if (isset($cart['products']) && is_array($cart['products'])) {
        $cnt += count($cart['products']);
        foreach($cart['products'] as $product) {
            $basket .= ':'.str_replace(':', ' ', $product['product']).':'.$product['amount'].':---:---:---:'.price_format($product['display_price'] * $product['amount']);
        }
    }

    // Gift Certificates
    if (isset($cart['giftcerts']) && is_array($cart['giftcerts'])) {
        $cnt += count($cart['giftcerts']);
        foreach ($cart['giftcerts'] as $tmp_gc) {
            $basket .= ':GIFT CERTIFICATE:---:---:---:---:'.price_format($tmp_gc['amount']);
        }
    }

    // Discounts
    if ($cart['display_discounted_subtotal'] - $cart['display_subtotal'] != 0) {
        $cnt++;
        $basket .= ':Discount:---:---:---:---:'.price_format($cart['display_discounted_subtotal'] - $cart['display_subtotal']);
    }

    // Shipping
    if ($cart['shipping_cost'] > 0) {
        $cnt++;
        $basket .= ':Shipping cost:---:---:---:---:'.price_format($cart['display_shipping_cost']);
    }

    // Taxes
    if ($cart['tax_cost'] != 0 && $config['Taxes']['display_taxed_order_totals'] != 'Y') {
        $cnt++;
        $basket .= ':Tax:---:---:---:---:'.price_format($cart['tax_cost']);
    }

   // Payment Surcharge
    if (isset($cart['payment_surcharge']) && $cart['payment_surcharge'] != 0) {
        $cnt++;
        $basket .= ':Payment Handling Fee:---:---:---:---:'.price_format($cart['payment_surcharge']);
    }

    // Applied Gift Certificates
    if (isset($cart['giftcert_discount']) && $cart['giftcert_discount'] != 0) {
        $cnt++;
        $basket .= ':Applied Gift Certificates Discount:---:---:---:---:'.price_format($cart['giftcert_discount']*-1);
    }

    $basket = (string)$cnt . $basket;
    $basket = preg_replace("/[&+]/", " ", $basket);

    return $basket;
}

/**
 * Do Protx Direct transaction
 *
 * @param array  $order     Order data
 * @param mixed  $txtype    Transaction type
 * @param string $processor Payment processor
 *
 * @return array
 * @see    ____func_see____
 */
function func_cc_protx_do($order, $txtype, $processor = 'cc_protxdir.php')
{
    global $sql_tbl;

    x_load('http');

    $module_params = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor = '$processor'");
    list($vpstxid, $securitykey, $txauthno, $vendortxcode) = explode("\n", $order['order']['extra']['txnid']);

    switch ($module_params['testmode']) {
        case 'S':
            $url = 'https://test.sagepay.com:443/Simulator/VSPServerGateway.asp?Service=Vendor{service}TX';
            $service = ucfirst(strtolower($txtype));
        break;

        case 'Y':
            $url = 'https://test.sagepay.com:443/gateway/service/{service}.vsp';
            $service = strtolower($txtype);
        break;

        default:
            $url = 'https://live.sagepay.com:443/gateway/service/{service}.vsp';
            $service = strtolower($txtype);
    }

    $post = array(
        "VPSProtocol=2.23",
        "TxType=" . $txtype,
        "Vendor=" . $module_params['param01'],
        "VendorTxCode=" . $vendortxcode,
        "VPSTxId=" . $vpstxid,
        "SecurityKey=" . $securitykey,
        "TxAuthNo=" . $txauthno
    );

    if ($txtype == 'RELEASE') {
        $post[] = "ReleaseAmount=" . $order['order']['total'];
    }

    $url = str_replace("{service}", $service, $url);

    list($a, $return) = func_https_request('POST', $url, $post);

    $ret = str_replace("\r\n", "&", $return);

    $ret_arr = explode("&",$ret);
    $response = array();
    foreach ($ret_arr as $ret) {
        if (preg_match("/([^=]+?)=(.+)/S", $ret, $matches))
            $response[$matches[1]] = trim($matches[2]);
    }

    $status = $response['Status'] == "OK";
    $err_msg = '';

    if (!$status)
        $err_msg = $response['Status'].": ".$response['StatusDetail'];

    $extra = array(
        'name' => 'txnid',
        'value' => $order['order']['extra']['txnid']
    );

    return array($status, $err_msg, $extra);
}

?>
