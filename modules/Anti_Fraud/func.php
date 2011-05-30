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
 * Functions for Anti Fraud module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.php,v 1.29.2.1 2011/01/10 13:11:55 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

function func_af_check_error2msg($check_error)
{
    global $__AF_return_labels;

    $default_msg = 'txt_antifraud_service_generror';
    $check_error = trim($check_error);

    if (empty($check_error)) return '';

    $msg = '';

    if (isset($__AF_return_labels[$check_error]))
        $msg = $__AF_return_labels[$check_error];
    else
        $msg = $default_msg;

    return $msg;
}

function func_is_high_risk_country($code)
{
    global $config;

    if (empty($config['high_risk_countries']))
        return false;

    $hrisk = @explode(",", $config['high_risk_countries']);

    return in_array($hrisk, $code);
}

/**
 * Send customer IP address to Anti Fraud server
 */
function func_send_ip_to_af($orderid, $reason = '')
{
    global $sql_tbl, $xcart_http_host, $config;

    x_load('http','tests');

    if (!test_active_bouncer()) {
        // ERROR: cannot continue without https modules
        return false;
    }

    $anti_fraud_url = ANTIFRAUD_URL . '/add_fraudulent_ip.php';

    $ip = func_query_first_cell("SELECT value FROM $sql_tbl[order_extras] WHERE orderid = '$orderid' AND khash = 'ip'");

    if (empty($ip))
        return false;

    $post = array("mode=add_ip");
    $post[] = "ip=" . $ip;
    $post[] = "shop_host=" . $xcart_http_host;
    $post[] = "reason=" . $reason;
    $post[] = "service_key=" . $config['Anti_Fraud']['anti_fraud_license'];

    return func_https_request('POST', $anti_fraud_url, $post);
}

// Check IP address at Anti Fraud server
function func_check_ip_at_af($ip, $proxy_ip = false, $address = false)
{
    global $config;

    x_load('http','tests');

    $anti_fraud_url = ANTIFRAUD_URL . '/check_ip.php';

    if($proxy_ip === false)
        $proxy_ip = $ip;

    $post = '';
    $post[] = 'service_key=' . $config['Anti_Fraud']['anti_fraud_license'];
    $post[] = 'ip=' . $ip;
    $post[] = 'proxy_ip=' . $proxy_ip;

    if ($address) {
        $address = func_stripslashes($address);
        $post[] = 'city=' . $address['city'];
        $post[] = 'state=' . $address['state'];
        $post[] = 'country=' . $address['country'];
        if (
            isset($address['zipcode'])
            && !empty($address['zipcode'])
        ) {
            $post[] = 'zipcode=' . $address['zipcode'];
        }
    }

    list($headers, $result) = func_https_request('POST', $anti_fraud_url, $post);

    $tmp         = explode("\n",$result);
    $status     = unserialize($tmp[0]);
    $resolved     = unserialize($tmp[1]);

    return array(
        'headers'     => $headers,
        'status'     => $status,
        'data'        => $resolved,
    );
}
?>
