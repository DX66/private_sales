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
 * Partner information
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: partner_info.php,v 1.49.2.2 2011/01/10 13:11:50 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_session_register ('partner');
x_session_register ('partner_clickid');

if (empty($partner) && (!empty($_GET['partner']) || !empty($_POST['partner_id']))) {

    // Assign current partner value

    if (isset($_POST['partner_id']) && (!empty($_POST['partner_id']))) {
        $partner = $_POST['partner_id'];
    } else {
        $partner = $_GET['partner'];
    }
    $partner = abs(intval($partner));

    // Check if $partner is valid

    $valid_partner = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[customers] WHERE id='$partner' AND usertype='B' AND status='Y'");
    if (!$valid_partner) {
        $partner = $partner_clickid = '';
        return;
    }

    $banner = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[partner_banners] WHERE bannerid = '$bid'");
    if (!$banner) {
        $bid = 0;
    }

    // Users has clicked onto banner
    $query = array(
        'userid' => $partner,
        'add_date' => XC_TIME,
        'bannerid' => $bid,
        'referer' => $HTTP_REFERER
    );

    if (isset($productid)) {
        $query['target'] = 'P';
        $query['targetid'] = $productid;

    } elseif (isset($cat)) {
        $query['target'] = 'C';
        $query['targetid'] = $cat;

    } elseif (isset($manufacturerid)) {
        $query['target'] = 'F';
        $query['targetid'] = $manufacturerid;
    }

    $partner_clickid = func_array2insert('partner_clicks', $query);

    // Set cookies
    $partner_cookie_length = ($config['XAffiliate']['partner_cookie_length'] ? $config['XAffiliate']['partner_cookie_length']*3600*24 : 0);

    if ($partner_cookie_length) {
        $expiry = mktime(0, 0, 0, date('m'), date('d'), date('Y')+1);
        func_setcookie('partner_clickid', $partner_clickid, $expiry);
        func_setcookie('partner', $partner, $expiry);
        func_setcookie('partner_time', XC_TIME + $partner_cookie_length, $expiry);
    }

} elseif (empty($partner) && !empty($_COOKIE['partner']) && !empty($_COOKIE['partner_time'])) {

    if ($_COOKIE['partner_time'] >= XC_TIME) {

        // Assign current partner value
        $partner = $_COOKIE['partner'];
        $partner_clickid = $_COOKIE['partner_clickid'];

        // Check if $partner is valid
        $valid_partner = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[customers] WHERE id='$partner' AND usertype='B' AND status='Y'") > 0;
        if (!$valid_partner) {
            $partner = $partner_clickid = '';
            return;
        }

    } else {

        // Remove cookies if $partner_cookie_length is expired
        func_setcookie('partner');
        func_setcookie('partner_clickid');
        func_setcookie('partner_time');
    }
}
?>
