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
 * @version    $Id: cc_csrc.resp_codes.php,v 1.12.2.1 2011/01/10 13:12:05 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

$avserr = array(
    'A'    => "Street address matches, but both 5-digit ZIP code and 9-digit ZIP code do not match. ",
    'B'    => "Street address matches, but postal code not verified. Returned only for non-U.S.-issued Visa cards. ",
    'C'    => "Street address and postal code not verified. Returned only for non-U.S.-issued Visa cards. ",
    'D'    => "Street address and postal code both match. Returned only for non-U.S.-issued Visa cards. ",
    'E'    => "AVS data is invalid. ",
    'G'    => "Non-U.S. issuing bank does not support AVS. ",
    'I'    => "Address information not verified. Returned only for non-U.S.-issued Visa cards. ",
    'J'    => "Card member name, billing address, and postal code all match. Ship-to information verified and chargeback protection guaranteed through the Fraud Protection Program. This code is returned only if you are signed up to use AAV+ with the American Express Phoenix processor. ",
    'K'    => "Card member's name matches. Both billing address and billing postal code do not match. This code is returned only if you are signed up to use Enhanced AVS or AAV+ with the American Express Phoenix processor. ",
    'L'    => "Card member's name matches. Billing postal code matches, but billing address does not match. This code is returned only if you are signed up to use Enhanced AVS or AAV+ with the American Express Phoenix processor. ",
    'M'    => "Street address and postal code both match. Returned only for non-U.S.-issued Visa cards. ",
    'N'    => "Street address, 5-digit ZIP code, and 9-digit ZIP code all do not match. ",
    'O'    => "Card member name matches. Billing address matches, but billing postal code does not match. This code is returned only if you are signed up to use Enhanced AVS or AAV+ with the American Express Phoenix processor. ",
    'P'    => "Postal code matches, but street address not verified. Returned only for non-U.S.-issued Visa cards. ",
    'Q'    => "Card member name, billing address, and postal code all match. Ship-to information verified but chargeback protection not guaranteed (Standard program). This code is returned only if you are signed up to use AAV+ with the American Express Phoenix processor. ",
    'R'    => "System unavailable. ",
    'S'    => "U.S. issuing bank does not support AVS. ",
    'U'    => "Address information unavailable. Returned if non-U.S. AVS is not available or if the AVS in a U.S. bank is not functioning properly. ",
    'V'    => "Card member name matches. Both billing address and billing postal code match. This code is returned only if you are signed up to use Enhanced AVS or AAV+ with the American Express Phoenix processor. ",
    'W'    => "Street address does not match, but 9-digit ZIP code matches. ",
    'X'    => "Exact match. Street address and 9-digit ZIP code both match. ",
    'Y'    => "Street address and 5-digit ZIP code both match. ",
    'Z'    => "Street address does not match, but 5-digit ZIP code matches. ",
    '1'    => "CyberSource AVS code. AVS is not supported for this processor or card type. ",
    '2'    => "CyberSource AVS code. The processor returned an unrecognized value for the AVS response. ",
);

$cvverr = array(
    'I'    => "Card verification number failed processor's data validation check. ",
    'M'    => "Card verification number matched. ",
    'N'    => "Card verification number not matched. ",
    'P'    => "Card verification number not processed. ",
    'S'    => "Card verification number is on the card but was not included in the request. ",
    'U'    => "Card verification is not supported by the issuing bank. ",
    'X'    => "Card verification is not supported by the card association. ",
    " "    => "Deprecated. Ignore this value. ",
    '1'    => "CyberSource does not support card verification for this processor or card type. ",
    '2'    => "The processor returned an unrecognized value for the card verification response. ",
    '3'    => "The processor did not return a card verification result code. ",
);

$factor = array(
    'F'    => "Hotlist match. The credit card number, street address, email address, or IP address for this order appears on the hotlist. ",
    'G'    => "The customer's geolocation data (phone number) and other factors do not correlate. ",
    'N'    => "Nonsensical input in the customer name or address fields. ",
    'O'    => "Obscenities in the order form. ",
    'P'    => "The bank processor declined the credit card. ",
    'U'    => "Unverifiable billing or shipping address. ",
    'W'    => "Warning for partial match of address to hotlist. ",
);

$reason = array(
    '100'    => "Successful transaction. ",
    '101'    => "The request is missing one or more required fields. ",
    '102'    => "One or more fields in the request contains invalid data. ",
    '150'    => "Error: General system failure. ",
    '151'    => "Error: The request was received but there was a server timeout. This error does not include timeouts between the client and the server. ",
    '152'    => "Error: The request was received, but a service did not finish running in time. ",
    '200'    => "The authorization request was approved by the issuing bank but declined by CyberSource because it did not pass the Address Verification Service (AVS) check. ",
    '201'    => "The issuing bank has questions about the request. You do not receive an authorization code programmatically, but you might receive one verbally by calling the processor. ",
    '202'    => "Expired card. You might also receive this if the expiration date you provided does not match the date the issuing bank has on file.    ",
    '203'    => "General decline of the card. No other information provided by the issuing bank. ",
    '204'    => "Insufficient funds in the account. ",
    '205'    => "Stolen or lost card. ",
    '207'    => "Issuing bank unavailable. ",
    '208'    => "Inactive card or card not authorized for card-not-present transactions. ",
    '209'    => "American Express Card Identification Digits (CID) did not match. ",
    '210'    => "The card has reached the credit limit. ",
    '211'    => "Invalid card verification number. ",
    '221'    => "The customer matched an entry on the processor negative file. ",
    '230'    => "The authorization request was approved by the issuing bank but declined by CyberSource because it did not pass the card verification (CV) check. ",
    '231'    => "Invalid account number. ",
    '232'    => "The card type is not accepted by the payment processor. ",
    '233'    => "General decline by the processor. ",
    '234'    => "There is a problem with your CyberSource merchant configuration. ",
    '235'    => "The requested amount exceeds the originally authorized amount. Occurs, for example, if you try to capture an amount larger than the original authorization amount. ",
    '236'    => "Processor failure. ",
    '237'    => "The authorization has already been reversed. ",
    '238'    => "The authorization has already been captured. ",
    '239'    => "The requested transaction amount must match the previous transaction amount. ",
    '240'    => "The card type sent is invalid or does not correlate with the credit card number. ",
    '241'    => "The request ID is invalid. ",
    '242'    => "You requested a capture, but there is no corresponding, unused authorization record. Occurs if there was not a previously successful authorization request or if the previously successful authorization has already been used by another capture request. ",
    '243'    => "The transaction has already been settled or reversed. ",
    '246'    => "The capture or credit is not voidable because the capture or credit information has already been submitted to your processor. Or, you requested a void for a type of transaction that cannot be voided. ",
    '247'    => "You requested a credit for a capture that was previously voided. ",
    '250'    => "Error: The request was received, but there was a timeout at the payment processor. ",
);

?>
