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
 * EPDQ
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_epdq.php,v 1.60.2.1 2011/01/10 13:12:06 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ($_SERVER['REQUEST_METHOD'] == "GET" && isset($_GET["oid"])) {
    require './auth.php';

    $skey = $_GET['oid'];
    require($xcart_dir.'/payment/payment_ccview.php');

} else {

    if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

    x_load('http');

    $merchant = $module_params['param01'];
    $clientid = $module_params['param02'];
    $phrase   = $module_params['param03'];
    $currency = $module_params['param04'];
    $auth     = $module_params['param05'];
    $cpi_logo = $module_params['param06'];
    $ordr = $module_params['param07'].join("-",$secure_oid);

    // the following parameters have been obtained earlier in the merchant's webstore: clientid, passphrase, oid, currencycode, total
    $params="clientid=".$clientid;
    $params.="&password=".$phrase;
    $params.="&oid=".$ordr;
    $params.="&chargetype=".$auth;
    $params.="&currencycode=".$currency;
    $params.="&total=".$cart['total_cost'];

    #perform the HTTP Post
    list($a1, $epdqdata, $a2) = func_http_post_request('secure2.epdq.co.uk', "/cgi-bin/CcxBarclaysEpdqEncTool.e", $params);

    if ($epdqdata == '') {
        $top_message = array(
            'type' => 'E',
            'content' => func_get_langvar_by_name('err_payment_cc_not_found')
        );
        func_header_location($xcart_catalogs['customer']."/cart.php?mode=checkout&paymentid=" . $paymentid);
    }

    $returnurl = $http_location.'/payment/cc_epdq.php';
    if (!$duplicate)
        db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");

?>
  <form action="https://secure2.epdq.co.uk/cgi-bin/CcxBarclaysEpdq.e" method="post" name="process">
    <?php print $epdqdata."\n"; ?>
    <input type="hidden" name="merchantdisplayname" value="<?php echo htmlspecialchars($merchant); ?>" />
    <input type="hidden" name="cpi_logo" value="<?php echo htmlspecialchars($cpi_logo); ?>" />
    <input type="hidden" name="email" value="<?php echo htmlspecialchars($userinfo['email']); ?>" />
    <input type="hidden" name="baddr1" value="<?php echo htmlspecialchars($userinfo['b_address']); ?>" />
    <input type="hidden" name="baddr2" value="<?php echo htmlspecialchars($userinfo['b_address_2']); ?>" />
    <input type="hidden" name="bcity" value="<?php echo htmlspecialchars($userinfo['b_city']); ?>" />
    <input type="hidden" name="bcountry" value="<?php echo htmlspecialchars($userinfo['b_country']); ?>" />
    <input type="hidden" name="bpostalcode" value="<?php echo htmlspecialchars($userinfo['b_zipcode']); ?>" />
    <input type="hidden" name="<?php echo htmlspecialchars(($userinfo['b_country']=="US")?('bstate'):('bcountyprovince')); ?>" value="<?php echo htmlspecialchars(($userinfo['b_country']=="US")?($userinfo['b_state']):($userinfo['b_statename'])); ?>" />
    <input type="hidden" name="<?php echo htmlspecialchars(($userinfo['s_country']=="US")?('sstate'):('scountyprovince')); ?>" value="<?php echo htmlspecialchars(($userinfo['s_country']=="US")?($userinfo['s_state']):($userinfo['s_statename'])); ?>" />
    <input type="hidden" name="saddr1" value="<?php echo htmlspecialchars($userinfo['s_address']); ?>" />
    <input type="hidden" name="saddr2" value="<?php echo htmlspecialchars($userinfo['s_address_2']); ?>" />
    <input type="hidden" name="scity" value="<?php echo htmlspecialchars($userinfo['s_city']); ?>" />
    <input type="hidden" name="spostalcode" value="<?php echo htmlspecialchars($userinfo['s_zipcode']); ?>" />
    <input type="hidden" name="scountry" value="<?php echo htmlspecialchars($userinfo['s_country']); ?>" />
    <input type="hidden" name="returnurl" value="<?php echo $returnurl; ?>" />
    </form>
    <table width="100%" style="height: 100%">
    <tr><td align="center" valign="middle">Please wait while connecting to <b>ePDQ</b> payment gateway...</td></tr>
    </table>

<script type="text/javascript">
//<![CDATA[
setTimeout('document.process.submit();', 500);
//]]>
</script>
<?php
}
exit;

?>
