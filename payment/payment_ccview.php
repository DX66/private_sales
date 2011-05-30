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
 * Validation and other actions before redirect 
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: payment_ccview.php,v 1.27.2.1 2011/01/10 13:12:08 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load(
    'order',
    'payment'
);

// IN: skey

$a = func_query_first("SELECT param1,param2,param3,param4,param5,sessionid,trstat,is_callback FROM $sql_tbl[cc_pp3_data] WHERE ref='" . $skey . "'");

if (
    $a['is_callback'] == 'Y'
    || empty($a['is_callback'])
) {

    // Return before callback
    $counter = 10;

    while (
        func_query_first_cell("SELECT is_callback FROM $sql_tbl[cc_pp3_data] WHERE ref = '" . $skey . "'") != 'N'
        && $counter-- > 0
    ) {

        sleep(1);

    }

    sleep(2);

    $a = func_query_first("SELECT param1,param2,param3,param4,param5,sessionid,trstat,is_callback FROM $sql_tbl[cc_pp3_data] WHERE ref='" . $skey . "'");
}

$sessid = $a['sessionid'];

if ($a['is_callback'] != 'N') {

    if ($XCARTSESSID != $sessid) {

        x_session_start($sessid);

        x_session_register('cart');
        x_session_register('top_message');

    }

    func_array2update(
        'cc_pp3_data',
        array(
            'is_callback' => 'R',
        ),
        "ref = '" . $skey . "'"
    );

    $top_message = array(
        'content' => func_get_langvar_by_name('lbl_cc_return_before_callback'),
    );

    $cart = '';

    if (!empty($active_modules['SnS_connector'])) {

        func_generate_sns_action('CartChanged');

    }

}

$trstat = $a['trstat'];

unset($a['sessionid']);

unset($a['trstat']);

$is_callback = $a['is_callback'];

func_unset($a, 'is_callback');

$oids = explode('|', $trstat);

$status = array_shift($oids);

$url = implode('', $a);

if (empty($url)) {

    if (empty($oids)) {

        $url = 'error_message.php?' . $XCART_SESSION_NAME . '=' . $sessid . '&error=error_ccprocessor_error';

    } else {

        $_orderids = func_get_urlencoded_orderids($oids);

        $url = 'cart.php?' . $XCART_SESSION_NAME . '=' . $sessid . '&mode=order_message&orderids=' . $_orderids;

        define('STATUS_CHANGE_REF', 8);

        func_change_order_status($oids, 'Q');

    }

}

func_array2update(
    'cc_pp3_data',
    array(
        'trstat' => 'END|' . implode('|', $oids),
    ),
    "ref='" . $skey . "'"
);

$request = $xcart_catalogs['customer'] . '/' . $url;

require $xcart_dir . '/payment/payment_ccredirect.php';

?>
