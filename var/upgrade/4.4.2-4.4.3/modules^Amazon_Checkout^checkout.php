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
 * Checkout by Amazon
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: checkout.php,v 1.8.2.2 2011/01/10 13:11:55 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

set_time_limit(86400);

define('ALL_CARRIERS', 1);

function func_amazon_detect_state($state, $country)
{
    global $sql_tbl;
    $state = strtolower(trim($state));
    if ($s_code = func_query_first_cell("SELECT code FROM $sql_tbl[states] WHERE (LOWER(state)='$state' OR LOWER(code)='$state') AND country_code='$country'")) {
        return $s_code;
    } else {
        return 'Other';
    }
}

function func_amazon_header_exit($code)
{
    global $_SERVER;
    $codes = array(500 => 'Internal Server Error', 403 => 'Forbidden', 503 => 'Service Unavailable');
    @header("$_SERVER[SERVER_PROTOCOL] $code ".$codes[$code]);
    exit;
}

if (defined('CHECKOUT_STARTED')) {
    echo func_display('modules/Amazon_Checkout/waiting.tpl', $smarty, false);
    require_once $xcart_dir.'/modules/Amazon_Checkout/cart.php';
    $fields = array("order-input" => $encoded_cart);
    func_create_payment_form("https://$amazon_host/checkout/".$config['Amazon_Checkout']['amazon_mid'], $fields, "Checkout by Amazon");
    exit;
}
elseif (defined('IS_STANDALONE')) {

    if (empty($_POST)) {
        func_amazon_header_exit(403);
    }

    x_load('xml','cart');

    include_once $xcart_dir . '/shipping/shipping.php';

    $parse_error = false;
    $options = array(
        'XML_OPTION_CASE_FOLDING' => 1,
        'XML_OPTION_TARGET_ENCODING' => 'ISO-8859-1'
    );

    $request_data = $request_type = $root_node = '';
    $allowed_requests = $trusted_post_variables;
    foreach ($allowed_requests as $name) {
        if (!empty($_POST[$name])) {
            $request_type = $name;
            $request_data = stripslashes(html_entity_decode($_POST[$name]));
            break;
        }
    }

    $check_sign = func_amazon_sign($_POST['UUID'].$_POST['Timestamp']);

    // Save received data to the unique log file
    $filename = $var_dirs['log'] . "/amazon-" . date("Ymd-His") . "-" . uniqid(rand()) . '.log.php';
    if ($fd = @fopen($filename, "a+")) {

        if ($check_sign != $_POST['Signature']) {
            $str[] = "Wrong signature! ".$_POST['Signature'].' vs '.$check_sign;
        }

        foreach ($_POST as $k =>$v) {
            if ($k != $request_type)
                $str[] = "$k: $v";
        }

        $str[] = "$request_type:\n $request_data";

        fwrite($fd, "<?php die(); ?>\n\n" . implode("\n\n", $str));
        fclose($fd);
        func_chmod_file($filename);
    }

    if ($check_sign != $_POST['Signature']) {
        func_amazon_header_exit(403);
    }

    $parsed = func_xml_parse($request_data, $parse_error, $options);

    if ($request_type == 'order-calculations-request') {
        include_once $xcart_dir.'/modules/Amazon_Checkout/checkout_callback.php';
    }
    elseif ($request_type == 'order-calculations-error') {
        // Reserved for future use
        $root_node = 'ORDERCALCULATIONSERROR';
    }
    elseif ($request_type == 'NotificationData') {
        include_once $xcart_dir.'/modules/Amazon_Checkout/order_notifications.php';
    }

}

exit;

?>
