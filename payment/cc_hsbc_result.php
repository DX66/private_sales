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
 * @version    $Id: cc_hsbc_result.php,v 1.30.2.1 2011/01/10 13:12:06 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './auth.php';

if (!func_is_active_payment('cc_hsbc.php'))
    exit;

if ($REQUEST_METHOD != 'POST' || empty($OrderId))
    exit;

$errarr = array(
    '1'    => "The user cancelled the transaction.",
    '2'    => "The processor declined the transaction for an unknown reason.",
    '3'    => "The transaction was declined because of a problem with the card. For example, an invalid card number or expiration date was specified.",
    '4'    => "The processor did not return a response.",
    '5'    => "The amount specified in the transaction was either too high or too low for the processor.",
    '6'    => "The specified currency is not supported by either the processor or the card.",
    '7'    => "The order is invalid because the order ID is a duplicate.",
    '8'    => "The transaction was rejected by FraudShield.",
    '9'    => "The transaction was placed in Review state by FraudShield.1",
    '10'    => "The transaction failed because of invalid input data.",
    '11'    => "The transaction failed because the CPI was configured incorrectly.",
    '12'    => "The transaction failed because the Storefront was configured incorrectly.",
    '13'    => "The connection timed out.",
    '14'    => "The transaction failed because the cardholders browser refused a cookie.",
    '15'    => "The customers browser does not support 128-bit encryption.",
    '16'    => "The CPI cannot communicate with the Secure ePayment engine."
);

$bill_output['sessid'] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref = '".$OrderId."'");
if (empty($bill_output['sessid']))
    exit;

if ($CpiResultsCode) {
    $bill_output['code'] = ($CpiResultsCode == 9 || $CpiResultsCode == 8) ? 3 : 2;
    $bill_output['billmes'] = empty($errarr[$CpiResultsCode]) ? "CpiResultsCode: ".$CpiResultsCode : $errarr[$CpiResultsCode];

} else {
    $bill_output['code'] = 1;
    $bill_output['billmes'] = 'Ok';
}

if (isset($PurchaseAmount)) {
    $hsbc_currency = func_query_first_cell("SELECT param04 FROM $sql_tbl[ccprocessors] WHERE processor = 'cc_hsbc.php'");
    $payment_return = array(
        'total' => ($hsbc_currency != '392' ? $PurchaseAmount/100 : $PurchaseAmount)
    );

    if (isset($PurchaseCurrency)) {
        $payment_return['currency'] = $PurchaseCurrency;
        $payment_return['_currency'] = $hsbc_currency;
    }
}

$skey = $_POST['OrderId'];
require($xcart_dir.'/payment/payment_ccmid.php');
require($xcart_dir.'/payment/payment_ccwebset.php');

?>
