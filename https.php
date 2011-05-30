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
 * HTTP-HTTPS redirection mechanism code
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Customer interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: https.php,v 1.54.2.4 2011/03/30 11:36:43 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: home.php"); die("Access denied"); }

x_load('files');

x_session_register('https_redirect_counter', 0);

x_session_register('https_redirect_forbidden', false);

$https_messages = array(
    array(
        "mode=order_message",
        "orderids="
    ),
    'error_message.php'
);

$https_scripts = array();

$_dir_user = func_get_area_catalog(AREA_TYPE, true);

/**
 * create payment scripts entries in $https_scripts
 */
$payment_data = func_data_cache_get('payments_https');

if (
    $payment_data
    && is_array($payment_data)
    && $current_area != 'A'
) {
    foreach ($payment_data as $payment_method_data) {

        $https_scripts[] = array(
            "paymentid=" . $payment_method_data['paymentid'],
            "mode=checkout"
        );

        if (
            $payment_method_data['processor']
            && !in_array($payment_method_data['processor'], $https_scripts)
        ) {
            $https_scripts[] = $payment_method_data['processor'];
        }
    }
}

if ($config['Security']['use_https_login'] == 'Y') {

    $https_scripts[] = 'register.php';
    $https_scripts[] = 'change_password.php';
    $https_scripts[] = 'login.php';
    $https_scripts[] = array(
        'cart.php',
        "mode=checkout",
    );
    $https_scripts[] = array(
        'cart.php',
        "mode=auth",
    );
    $https_scripts[] = array(
        'help.php', 
        "section=contactus"
    );

    // Login form on the home page
    if (
        $current_area != 'C'
        && empty($login)
    ) {
        $https_scripts[] = 'home.php';
    }
}

if (!function_exists('is_https_link')) {
function is_https_link($link, $https_scripts)
{
    if (empty($https_scripts))
        return false;

    $link = preg_replace('!^/+!S', '', $link);

    foreach ($https_scripts as $https_script) {

        if (!is_array($https_script))
            $https_script = array($https_script);

        $tmp = true;

        foreach ($https_script as $v) {

            $p = strpos($link, $v);

            if ($p === false) {
                $tmp = false;
                break;
            }

            if ($v[strlen($v)-1] === '=') continue;

            if ($p + strlen($v) < strlen($link)) {

                $last = $link[$p+strlen($v)];

                if ($last === '?') continue;

                if ($last !== '&') {

                    $tmp = false;

                    break;
                }

            }

        }

        if ($tmp) return true;
    }

    return false;
}
}

$current_script = '/' . basename($PHP_SELF . ($QUERY_STRING ? "?$QUERY_STRING" : ''));

/**
 * Generate additional PHPSESSID var
 */
$additional_query = ($QUERY_STRING ? "&" : "?")
    . (
        strstr($QUERY_STRING, $XCART_SESSION_NAME)
        ? ''
        : $XCART_SESSION_NAME . "=" . $XCARTSESSID
    );

if (
    !preg_match("/(?:^|&)sl=/", $additional_query)
    && $xcart_http_host != $xcart_https_host
) {
    $additional_query .= "&sl=" . $store_language . "&is_https_redirect=Y";
}

if (
    $REQUEST_METHOD == 'GET'
    && empty($_GET['keep_https'])
    && (
        $HTTPS
        || !$https_redirect_forbidden
    )
) {
    $tmp_location = '';

    if (
        !$HTTPS
        && is_https_link($current_script, $https_scripts)
    ) {

        $tmp_location = $_dir_user . $current_script . $additional_query;

    } elseif (
        !$HTTPS
        && is_https_link($current_script, $https_messages)
        && !strncasecmp($HTTP_REFERER, $https_location, strlen($https_location))
    ) {

        $tmp_location = $_dir_user . $current_script . $additional_query;

    } elseif (
        $config['Security']['leave_https'] == 'Y'
        && $HTTPS
        && !is_https_link($current_script, $https_scripts)
        && !is_https_link($current_script, $https_messages)
        && !func_is_ajax_request()
        && !in_array(AREA_TYPE, array('A', 'P'))
    ) {

        x_session_register('login_redirect');

        $do_redirect = empty($login_redirect);

        x_session_unregister('login_redirect');

        if ($do_redirect) {

            $_dir_user = func_get_area_catalog(AREA_TYPE, false);

            $tmp_location = $_dir_user . $current_script . $additional_query;

        }

    }

    $https_redirect_limit = intval($https_redirect_limit);

    if (
        !empty($tmp_location)
        && !$HTTPS
        && $https_redirect_limit > 0
        && $https_redirect_counter > $https_redirect_limit
    ) {
        $https_redirect_forbidden = true;
    }

    if (
        !empty($tmp_location)
        && (
            $HTTPS
            || !$https_redirect_forbidden
        )
    ) {

        $https_redirect_counter++;

        if ($smarty->webmaster_mode) {
            echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<body>
<script type="text/javascript">
//<![CDATA[
var _smarty_console = window.open("","console","width=360,height=500,resizable,scrollbars=yes");
if (_smarty_console)
    _smarty_console.close();
//]]>
</script>';
            echo "<br /><br />".func_get_langvar_by_name('txt_header_location_note', array('time' => 2, 'location' => $tmp_location), false, true, true);
            echo "<meta http-equiv=\"Refresh\" content=\"0;URL=$tmp_location\" />";
            echo "</body>\n</html>";

            exit;

        } else {

            func_header_location($tmp_location);

        }

    } else {

        $https_redirect_counter = 0;

    }
}

?>
