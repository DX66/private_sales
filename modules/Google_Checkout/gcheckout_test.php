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
 * Test Google checkout module requirements
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: gcheckout_test.php,v 1.18.2.1 2011/01/10 13:11:58 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

if (empty($mode) || !in_array($mode, array('test_callback', 'test_gc')))
    $mode = 'test_gc';

if ($mode == 'test_callback') {

    // Check if callback URL is accessible via HTTPS connection

    $url_to_test = $https_location . '/payment/ps_gcheckout.php';

    $test_success = false;

    $h = array(
        'Authorization' => "Basic ".base64_encode($config['Google_Checkout']['gcheckout_mid'].":".$config['Google_Checkout']['gcheckout_mkey']),
        'Accept' => 'application/xml'
    );

    x_load('http');

    // Send HTTPS query with HTTP authorization
    list($a, $return) = func_https_request('POST', $url_to_test, array('test'), "", "", "application/xml", "", "", "", $h);

    if (strtolower($return) == 'success') {

        $h = array(
            'Accept' => 'application/xml'
        );

        // Send HTTPS query without HTTP authorization
        list($a, $return) = func_https_request('POST', $url_to_test, array('test'), "", "", "application/xml", "", "", "", $h);

        if (strtolower($return) != 'success') {
            $test_success = true;
        }

    }

    if ($test_success)
        $top_message['content'] = func_get_langvar_by_name('txt_gcheckout_callback_test_success');
    else {
        $top_message['content'] = func_get_langvar_by_name('txt_gcheckout_callback_test_failure');
        $top_message['type'] = 'E';
    }
}

elseif ($mode == 'test_gc') {

    // Check if Google Checkout accepts requests with specified Merchant ID and Merchant Key

    $test_xml =<<<OUT
<?xml version="1.0" encoding="UTF-8"?>
<hello xmlns="http://checkout.google.com/schema/2" />
OUT;

    $parsed = @func_gcheckout_send_xml($test_xml);

    $result = '';

    if (!empty($parsed))
        $result = func_array_path($parsed, 'BYE');

    if (!empty($result))
        $top_message['content'] = func_get_langvar_by_name('txt_gcheckout_test_success');
    else {
        $top_message['content'] = func_get_langvar_by_name('txt_gcheckout_test_failure');
        $top_message['type'] = 'E';
    }

}

func_header_location("configuration.php?option=Google_Checkout");

?>
