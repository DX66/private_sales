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
 * "RBS WorldPay - Global Gateway" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_bibit.php,v 1.28.2.3 2011/01/10 13:12:05 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

if ($module_params['cmpi'] == 'B') {
    // Process with 3D-Secure
    func_pm_load('cc_bibit.php');
    list($a, $response) = func_cc_bibit_request($secure_3d['data']['PaRes'], $secure_3d['data']['echoData']);
} else {
    // Process without 3D-Secure
    list($a, $response) = func_cc_bibit_request();
}

if (func_cc_bibit_error($a, $response, $error)) {
    // An error has been detected
    $bill_output['code'] = 2;
    $bill_output['billmes'] = ($error['code']!='' ? $error['code'] . ' - ' : '') . $error['message'];

} elseif(preg_match("/<payment>(.*)<\/payment>/U", $response, $o)) {
    // 3D-Secure is disabled
    $response = $o[1];

    if (preg_match("/<lastEvent>(.*)<\/lastEvent>/U", $response, $o))
        $last_event = $o[1];

    if (preg_match("/<paymentMethod>(.*)<\/paymentMethod>/U", $response, $o))
        $pm = $o[1];

    if (preg_match("/<balance accountType=\"(.*)\">/U", $response, $o))
        $account_type = $o[1];

    if (preg_match("/<riskScore value=\"(.*)\"\/>/U", $response, $o))
        $risk_score = $o[1];

    if (preg_match("/<CVCResultCode description=\"(.*)\"\/>/iU", $response, $o))
        $bill_output['cvvmes'] = "CVCResultCode: " . $o[1];

    if (preg_match("/<AVSResultCode description=\"(.*)\"\/>/iU", $response, $o))
        $bill_output['avsmes'] = "AVSResultCode: " . $o[1];

    if (in_array($last_event, array('AUTHORISED', 'CAPTURED'))) {
        $bill_output['code'] = 1;
        $bill_output['billmes'] = 'Balance account type: ' . $account_type . '; Risk score: ' . $risk_score . ';';
    } else {
        $bill_output['code'] = 2;
        $bill_output['billmes'] = 'Last event: ' . $last_event;
    }
} elseif (preg_match("/(<requestInfo.*)<\/requestInfo>/U", $response, $o)) {
    // Initial Order Reply Message with 3D-Secure
    // but 3D-Secure is disabled in payment method settings
    $bill_output['code'] = 2;
    $bill_output['billmes'] = "3D-Secure response obtained, but 3D-Secure is disabled for RBS WorldPay Global Gateway in X-Cart.";
    $bill_output['hide_mess'] = true;
    $bill_output['is_error'] = true;
}

?>
