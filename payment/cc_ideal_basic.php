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
 * iDEAL Basic payment method
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_ideal_basic.php,v 1.16.2.1 2011/01/10 13:12:06 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!isset($REQUEST_METHOD))
    $REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];

if ($REQUEST_METHOD == 'GET' && !empty($_GET['status']) && !empty($_GET['ordr'])) {
    include('./auth.php');

    $bill_output['sessid'] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref = '".$_GET["ordr"]."'");
    $bill_output['code'] = ($_GET['status'] == 'success' ? 1 : 2);

    switch ($_GET['status']) {
        case 'success':
            $bill_output['billmes'] = "Gateway reports success";
            break;

        case 'cancel':
            $bill_output['billmes'] = "Transaction cancelled by customer";
            break;

        case 'error':
            $bill_output['billmes'] = "Error is occured";
            break;

        default:
            $bill_output['billmes'] = "Unknown status [".$_GET['status']."] is received";
    }

    include($xcart_dir.'/payment/payment_ccend.php');

} else {

    if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

    include($xcart_dir.'/payment/sha1.php');

    $mid = $module_params['param04'];
    $key = $module_params['param03'];
    $ordr = substr($module_params['param05'].join("-",$secure_oid), 0, 16);
    $ptype = 'ideal';
    $sid = 0;
    $desc = $ordr;
    $url = $http_location."/payment/cc_ideal_basic.php?ordr=".$ordr."&status=";
    $amount = 100*$cart['total_cost'];
    $valid = date("Y-m-d", mktime(0,0,0,date('m'), date('d'), date('Y')+1))."T12:00:00:0000Z";

    $hash = $key.$mid.$sid.$amount.$ordr.$ptype.$valid.$ordr.$desc.'1'.$amount;

    $hash = strtr($hash, array(" "=>'',"\t"=>'',"\n"=>''));

    if (!$duplicate)
        db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".$ordr."','".$XCARTSESSID."')");

    $fields = array(
        'merchantID' => $mid,
        'subID' => $sid,
        'amount' => $amount,
        'purchaseID' => $ordr,
        'language' => 'nl',
        'currency' => 'EUR',
        'description' => $desc,
        'hash' => strtolower(sha1($hash)),
        'paymentType' => $ptype,
        'validUntil' => $valid,
        'urlCancel' => $url.'cancel',
        'urlSuccess' => $url.'success',
        'urlError' => $url.'error',
        'itemNumber1' => $ordr,
        'itemDescription1' => $desc,
        'itemQuantity1' => 1,
        'itemPrice1' => $amount,
    );

    func_create_payment_form("https://ideal".($module_params['testmode']=="Y" ? 'test' : '').".secure-ing.com/ideal/mpiPayInitIng.do", $fields, 'iDEAL');
}

exit;
?>
