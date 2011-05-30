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
 * @version    $Id: cc_epdq_result.php,v 1.32.2.1 2011/01/10 13:12:06 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './auth.php';

if (!func_is_active_payment('cc_epdq.php'))
    exit;

if ($REQUEST_METHOD == 'POST' && !empty($oid)) {
    $bill_output['sessid'] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref = '".$oid."'");
    if (empty($bill_output['sessid']))
        exit;

    $eciresult_codes = array(
        0 => "Transaction type does not support Authentication.",
        1 => "An attempted authentication transaction. Either the issuer or the cardholder are not enrolled in Internet Authentication.",
        2 => "A fully authenticated transaction. The cardholder successfully authenticated themselves.",
        3 => "Unsuccessful Internet Authentication transaction. The CPI will decline the transaction for Visa transactions.",
        4 => "A non authenticated transaction.",
        5 => "BIN range is not enrolled.",
        6 => "Attempts transaction. Either the issuer or cardholder are not enrolled."
    );

    $bill_output['code'] = (stristr($transactionstatus, 'Success') ? 1 : 2);
    $bill_output['billmes'] = $transactionstatus;
    if (isset($eciresult) && !zerolen($eciresult) && in_array($eciresult, array_keys($eciresult_codes))) {
        $bill_output['billmes'] .= '; Authorisation result: ' . $eciresult_codes[$eciresult] .' (eciresult: ' . $eciresult . ')';
    }

    if (isset($cardprefix) && !zerolen($cardprefix)) {
        $bill_output['billmes'] .= '; Card Prefix(First digit of the supplied card number): ' . $cardprefix;
    }

    if (isset($total)) {
        $payment_return = array(
            'total' => $total
        );
    }

    $skey = $oid;
    require($xcart_dir.'/payment/payment_ccmid.php');
    require($xcart_dir.'/payment/payment_ccwebset.php');
}
exit;
?>
