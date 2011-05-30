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
 * @version    $Id: cc_payflow_link_result.php,v 1.15.2.1 2011/01/10 13:12:07 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './auth.php';

if (!func_is_active_payment('cc_payflow_link.php'))
    exit;

$cvverr = array(
    'Y' => "The CSC value matches the data on file",
    'N' => "The CSC value does not matches the data on file.",
    'X' => "The cardholder's bank does not support this service"
);
$avserr = array(
    'Y' => 'Match',
    'N' => "No match",
    'X' => "Service unavailable or not completed"
);

if ($REQUEST_METHOD != 'POST')
    exit;

if ($RESULT == 0 && $RESPMSG != 'CSCDECLINED' && $RESPMSG != 'AVSDECLINED')
    $RESPMSG .= " (AUTHCODE: ".$AUTHCODE.")";

if (empty($INVOICE))
    exit;

$a = func_query_first("SELECT * FROM $sql_tbl[cc_pp3_data] WHERE ref = '".$INVOICE."'");
if (empty($a))
    exit;

$bill_output['sessid'] = $a['sessionid'];
$bill_output['cvvmes'] = $cvverr[$CSCMATCH];
$bill_output['avsmes'] =     "AVS Street match: ".$avserr[substr($AVSDATA, 0, 1)]."; ".
                            "AVS Zip match: ".$avserr[substr($AVSDATA, 1, 1)]."; ".
                            "AVS OR Operation: ".$avserr[substr($AVSDATA, 2, 1)].";";

if ($RESULT == 0 && $RESPMSG != 'CSCDECLINED' && $RESPMSG != 'AVSDECLINED') {
    $bill_output['code'] = 1;
    $bill_output['billmes'] = $RESPMSG;

} else {
    $bill_output['code'] = 2;
    $bill_output['billmes'] = $RESPMSG." (Result: ".$RESULT.")";
}

$bill_output['billmes'].= "(PNREF: ".$PNREF.")";

if (isset($AMOUNT)) {
    $payment_return = array(
        'total' => $AMOUNT
    );
}

$skey = $INVOICE;
require($xcart_dir.'/payment/payment_ccmid.php');
require($xcart_dir.'/payment/payment_ccwebset.php');
?>
