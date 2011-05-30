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
 * Anti-fraud functionality
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: anti_fraud.php,v 1.48.2.1 2011/01/10 13:11:55 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

x_load(
    'http',
    'mail',
    'tests'
);

if (empty($active_modules['Anti_Fraud']) || !$userinfo)
    return;

if (!$extras['ip']) {

    $extra['Anti_Fraud']['result'] = "no_user_ip";

} elseif (!test_active_bouncer()) {

    $extra['Anti_Fraud']['result'] = "no_https";

} elseif (empty($config['Anti_Fraud']['anti_fraud_license'])) {

    $extra['Anti_Fraud']['result'] = "sk_invalid";

} else {

    func_unset($extra['Anti_Fraud'],'result');

    $af_result = null;
    $af_data = null;
    $af_response = null;

    $anti_fraud_url = ANTIFRAUD_URL.'/antifraud_service.php';

    $post = array();
    $post[] = "ip=" . $extras['ip'];
    $post[] = "proxy_ip=" . $extras['proxy_ip'];
    $post[] = "email=" . preg_replace("/^[^@]+@/Ss", '', $userinfo['email']);
    $post[] = "country=" . $userinfo['b_country'];
    $post[] = "state=" . $userinfo['b_state'];
    $post[] = "city=" . $userinfo['b_city'];
    $post[] = "zipcode=" . $userinfo['b_zipcode'];
    $post[] = "phone=" . $userinfo['phone'];
    $post[] = "service_key=" . $config['Anti_Fraud']['anti_fraud_license'];
    $post[] = "safe_distance=" . $config['Anti_Fraud']['safe_distance'];

    $multi = 1;

    $added_data = array();

    $order_total = 0;
    if ($cart['orders']) {
        foreach ($cart['orders'] as $v)
            $order_total += $v['total'];
    }

    // Big order
    if (
        $config['Anti_Fraud']['anti_fraud_order_limit'] > 0
        && $order_total > 0
        && $order_total > $config['Anti_Fraud']['anti_fraud_order_limit']
    ) {

        $multi = $multi * 2;

        $added_data['order_limit_excess'] = '1';

    }

    // Has processed orders
    if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[orders] WHERE status IN ('P','C') AND userid = '$userinfo[userid]'") > 0) {

        $multi = $multi / 2;

        $added_data['completed_orders'] = '1';

    }

    // Has cancelled orders
    if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[orders] WHERE status IN ('D','F') AND userid = '$userinfo[userid]'") > 0) {

        $multi = $multi * 1.5;

        $added_data['declined_orders'] = '1';

    }

    // Another name from this IP
    if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[orders], $sql_tbl[order_extras] WHERE $sql_tbl[order_extras].orderid = $sql_tbl[orders].orderid AND $sql_tbl[order_extras].khash = 'ip' AND $sql_tbl[order_extras].value = '$REMOTE_ADDR' AND $sql_tbl[orders].userid <> '$userinfo[userid]'") > 0) {

        $multi = $multi * 2;

        $added_data['foreign_ip_address'] = '1';

    }

    list($header, $response) = func_https_request('POST', $anti_fraud_url, $post);

    if (strpos($header, "200 OK") !== FALSE) {

        list($af_result, $af_data) = explode("\n", $response);

        $af_result     = unserialize($af_result);
        $af_data     = unserialize($af_data);
        $af_data     = func_array_merge($af_data, $added_data);

        if (
            $af_result['available_request'] == $af_result['used_request']
            && $af_result['available_request'] == 0
            && $af_result['error'] == "NOT_AVAILABLE_SERVICE"
        ) {
            $extra['Anti_Fraud']['result'] = "sk_invalid";

            if ($config['Anti_Fraud']['eml_af_sk_invalid'] == 'Y') {
                func_send_mail(
                    $config['Company']['orders_department'],
                    'mail/anti_fraud_sk_invalid_subj.tpl',
                    'mail/anti_fraud_sk_invalid.tpl',
                    $config['Company']['orders_department'],
                    false
                );
            }

        } elseif ($af_result['available_request'] <= $af_result['used_request']) {

            $extra['Anti_Fraud']['result'] = "sk_expire";

            if ($config['Anti_Fraud']['eml_af_sk_expire'] == 'Y') {
                func_send_mail(
                    $config['Company']['orders_department'],
                    'mail/anti_fraud_sk_expire_subj.tpl',
                    'mail/anti_fraud_sk_expire.tpl',
                    $config['Company']['orders_department'],
                    false
                );
            }

        } elseif ($af_result['total_trust_score'] > 0) {

            $extra['Anti_Fraud']['total_trust_score'] = $af_result['total_trust_score']*$multi;

            // High risk country
            if (func_is_high_risk_country($userinfo['b_country'])) {
                $extra['Anti_Fraud']['total_trust_score'] += 7;
            }

            if ($extra['Anti_Fraud']['total_trust_score'] > 10) {
                $extra['Anti_Fraud']['total_trust_score'] = 10;
            }

            $extra['Anti_Fraud']['total_trust_score']     = round($extra['Anti_Fraud']['total_trust_score'], 0);
            $extra['Anti_Fraud']['available_request']     = $af_result['available_request'];
            $extra['Anti_Fraud']['used_request']         = $af_result['used_request'];

            if (!empty($af_result['error'])) {
                $extra['Anti_Fraud']['error'] = $af_result['error'];
            }

            $extra['Anti_Fraud']['data'] = $af_data;

            if (!empty($extra['Anti_Fraud']['data'])) {

                foreach($extra['Anti_Fraud']['data'] as $adk => $adv) {

                    if (is_array($adv))
                        unset($extra['Anti_Fraud']['data'][$adk]);

                }

            }

        } else {

            if (!empty($af_data['check_error'])) {

                if ($af_data['check_error'] == 'IP_NOT_FOUND') {

                    $rk = 'msg';

                } else {

                    $rk = 'warning';

                }

                $extra['Anti_Fraud']['result']         = $rk;
                $extra['Anti_Fraud']['result_msg']     = func_af_check_error2msg($af_data["check_error"]);

            } else {

                $extra['Anti_Fraud']['result'] = "bad_request";

            }

        }

    } else {

        $extra['Anti_Fraud']['result'] = "not_avail";

    }

    unset($af_result,$af_data,$af_response);
}

?>
