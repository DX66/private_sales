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
 * "Ogone - Web Based" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_ogoneweb.php,v 1.32.2.1 2011/01/10 13:12:07 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!isset($REQUEST_METHOD))
        $REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];

if ($REQUEST_METHOD == 'GET' && isset($_GET['oid']) && preg_match('/accept|cancel|exception|decline/s', $_GET["mode"])) {

    require './auth.php';

    if (!func_is_active_payment('cc_ogoneweb.php'))
        exit;

    $skey = $oid;

    if ($mode == 'accept') {
        require($xcart_dir.'/payment/payment_ccview.php');
    } else {
        // Acquirer rejects the authorisation more than the maximum of authorised tries (mode=decline) OR
        // Customer cancels the payment (mode=cancel) OR
        // The payment result is uncertain. (mode=exception)
        $bill_output['billmes'].= " (Return code: ".$mode.")";

        $bill_output['code'] = 2;
        require($xcart_dir.'/payment/payment_ccend.php');
    }

} else {

    if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

    require($xcart_dir.'/payment/sha1.php');

    $pp_merch = $module_params['param01'];
    $pp_secret = $module_params['param03'];
    $pp_curr = $module_params['param04'];
    $pp_test = ($module_params['testmode']=='Y') ?
        "https://secure.ogone.com:443/ncol/test/orderstandard.asp" :
        "https://secure.ogone.com:443/ncol/prod/orderstandard.asp";
    $ordr = $module_params['param06'].join("-",$secure_oid);

    if(!$duplicate)
        db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");

    $l = array(
        'en' => 'en_US',
        'fr' => 'fr_FR',
        'nl' => 'nl_NL',
        'it' => 'it_IT',
        'de' => 'de_DE',
        'es' => 'es_ES',
        'no' => 'no_NO'
    );

    $post = array(
        'PSPID' => $pp_merch,
        'orderID' => $ordr,
        'amount' => (100*$cart['total_cost']),
        'currency' => $pp_curr,
        'EMAIL' => $userinfo['email'],
        'Owneraddress' => $userinfo['b_address'],
        'OwnerZip' => $userinfo['b_zipcode'],
        'language' => $l[$store_language] ? $l[$store_language] : 'en_US',
        'SHASign' => sha1($ordr . (100 * $cart['total_cost']) . $pp_curr . $pp_merch . $pp_secret)
    );

    $post['accepturl'] = $post['declineurl'] = $post['exceptionurl'] = $post['cancelurl'] = $http_location."/payment/cc_ogoneweb.php?oid=$ordr&mode=";
    $post['accepturl'] .= 'accept';
    $post['cancelurl'] .= 'cancel';
    $post['exceptionurl'] .= 'exception';
    $post['declineurl'] .= 'decline';

    // For security checking
    $post['COMPLUS'] = md5($pp_curr . $pp_merch . $pp_secret . $XCARTSESSID);

    func_create_payment_form($pp_test, $post, 'Ogone');
}
exit;

?>
