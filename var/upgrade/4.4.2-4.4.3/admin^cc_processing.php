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
 * Payment processor configuration interface
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_processing.php,v 1.107.2.1 2011/01/10 13:11:45 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */
// For explanation of cc processing please refer to
// X-Cart developer's documentation

require './auth.php';
require $xcart_dir . '/include/security.php';

x_load(
    'backoffice',
    'crypt',
    'tests',
    'payment'
);

if ($active_modules['XPayments_Connector']) {
    func_xpay_func_load();
}

$cc_script = "cc_processing.php?mode=update&cc_processor=$cc_processor";

x_session_register('recent_payment_methods', array());

if (
    $mode == 'add'
    && !empty($processor)
) {

    require $xcart_dir . '/include/safe_mode.php';

    func_add_processor($processor);

    func_header_location('payment_methods.php');

}

if (
    $mode == 'delete'
    && $paymentid
) {

    require $xcart_dir . '/include/safe_mode.php';

    $tmp = func_query_first("SELECT $sql_tbl[ccprocessors].paymentid, $sql_tbl[ccprocessors].processor FROM $sql_tbl[ccprocessors], $sql_tbl[payment_methods] WHERE $sql_tbl[payment_methods].paymentid = '".$paymentid."' AND ($sql_tbl[ccprocessors].paymentid = $sql_tbl[payment_methods].paymentid OR $sql_tbl[ccprocessors].processor = $sql_tbl[payment_methods].processor_file)");

    if (!empty($tmp)) {

        if (
            $tmp['processor'] == 'ps_paypal.php'
            || $tmp['processor'] == 'ps_paypal_pro.php'
        ) {

            db_query("DELETE from $sql_tbl[payment_methods] WHERE processor_file IN ('ps_paypal.php','ps_paypal_pro.php')");

            db_query("UPDATE $sql_tbl[ccprocessors] SET paymentid='0' WHERE processor IN ('ps_paypal.php','ps_paypal_pro.php')");

            if (is_array($recent_payment_methods)) {

                foreach ($recent_payment_methods as $k => $v) {

                    if (
                        $v['script'] == 'ps_paypal.php'
                        || $v['script'] == 'ps_paypal_pro.php'
                    ) {

                        unset($recent_payment_methods[$k]);

                    }

                }

            }

        } else {

            db_query("DELETE FROM $sql_tbl[payment_methods] WHERE paymentid='" . $paymentid . "'");

            db_query("UPDATE $sql_tbl[ccprocessors] SET paymentid='0' where paymentid='" . $paymentid . "'");

        }

        if (isset($recent_payment_methods[$tmp['processor'] . $paymentid])) {

            unset($recent_payment_methods[$tmp['processor'] . $paymentid]);

        }

    }

    func_header_location('payment_methods.php');
}

/**
 * Setup paramxx in ccprocessors table
 */
if (
    $REQUEST_METHOD == 'POST'
    && empty($mode)
) {

    require $xcart_dir . '/include/safe_mode.php';

    if (!empty($cc_processor)) {

        $top_message = array(
            'type'         => 'I',
            'content'     => func_get_langvar_by_name('msg_adm_payment_method_upd'),
        );

        if (
            $cc_processor == 'ps_paypal_pro.php'
            || $cc_processor == 'ps_paypal.php'
        ) {

            $_POST['paypal_suppress_encoding'] = !isset($_POST['paypal_suppress_encoding']) ? 'N' : $_POST['paypal_suppress_encoding'];
            $_POST['paypal_amex'] = !isset($_POST['paypal_amex']) ? 'N' : $_POST['paypal_amex'];

            $map = array (
                'ipn' => 'ps_paypal.php',
            );

            if (!in_array($paypal_solution, array('ipn','pro','uk','express')))
                $paypal_solution = 'ipn';

            if (
                $config['paypal_solution'] == 'pro'
                || $config['paypal_solution'] == 'express'
            ) {

                func_array2insert(
                    'config',
                    array(
                        'name'  => 'paypal_last_pro_solution',
                        'value' => 'pro',
                    ),
                    true
                );

            } elseif ($config['paypal_solution'] == 'uk') {

                func_array2insert(
                    'config',
                    array(
                        'name'  => 'paypal_last_pro_solution',
                        'value' => 'uk',
                    ),
                    true
                );

            }

            if ($paypal_solution == 'pro') {

                $map['pro'] = 'ps_paypal_pro.php';

            } elseif ($paypal_solution == 'express') {

                $map['express'] = 'ps_paypal_pro.php';

            } elseif ($paypal_solution == 'uk') {

                $map['uk'] = 'ps_paypal_pro.php';

            } elseif (
                $paypal_solution == 'ipn'
                && !empty($config['paypal_last_pro_solution'])
            ) {

                $map[$config['paypal_last_pro_solution']] = 'ps_paypal_pro.php';

            }

            func_array2insert(
                'config',
                array(
                    'name'  => 'paypal_solution',
                    'value' => $paypal_solution,
                ),
                true
            );

            func_array2insert(
                'config',
                array(
                    'name'  => 'paypal_suppress_encoding',
                    'value' => $_POST['paypal_suppress_encoding'],
                ),
                true
            );

            func_array2insert(
                'config',
                array(
                    'name'  => 'paypal_amex',
                    'value' => $_POST['paypal_amex'],
                ),  
                true
            );  

            if ($paypal_solution == 'express') {

                func_array2insert(
                    'config',
                    array(
                        'name'  => 'paypal_express_method',
                        'value' => $paypal_express_method,
                    ),
                    true
                );

                if ($paypal_express_email) {

                    func_array2insert(
                        'config',
                        array(
                            'name'  => 'paypal_express_email',
                            'value' => $paypal_express_email,
                        ),
                        true
                    );

                }

            }

            if ($paypal_solution == 'ipn') {

                func_array2insert(
                    'config',
                    array(
                        'name'  => 'paypal_address_override',
                        'value' => isset($_POST['paypal_address_override']) ? $_POST['paypal_address_override'] : 'N',
                    ),
                    true
                );
            }    

            $enable_paypal = (
                $paypal_solution != $config['paypal_solution']
                && (
                    $paypal_solution == 'ipn'
                    || $config['paypal_solution'] == 'ipn'
                )
            );

            func_disable_paypal_methods($paypal_solution, $enable_paypal);

            // set params
            foreach ($map as $map_key => $processor) {

                if (!empty($_POST['conf_data'][$map_key])) {

                    if (
                        $active_modules['XPayments_Connector']
                        && isset($_POST['conf_data'][$map_key]['use_xpc'])
                        && isset($_POST['conf_data'][$map_key]['use_xpc_processor'])
                    ) {

                        func_array2insert(
                            'config',
                            array(
                                'name'  => 'paypal_dp_use_xpc_' . $map_key,
                                'value' => $_POST['conf_data'][$map_key]['use_xpc'],
                            ),
                            true
                        );

                        func_array2insert(
                            'config',
                            array(
                                'name'  => 'paypal_dp_use_xpc_processor_' . $map_key,
                                'value' => $_POST['conf_data'][$map_key]['use_xpc_processor'],
                            ),
                            true
                        );

                        unset($_POST['conf_data'][$map_key]['use_xpc'], $_POST['conf_data'][$map_key]['use_xpc_processor']);

                    }

                    func_array2update(
                        'ccprocessors',
                        $_POST['conf_data'][$map_key],
                        "processor = '" . $processor . "'"
                    );

                }

            }

        } else {

            if (
                stristr($cc_processor, 'cc_anz')
                && !isset($_POST['param05'])
            ) {
                $_POST['param05'] = '';
            }

            if (
                $cc_processor == 'cc_2conew.php'
                && !isset($_POST['param04'])
            ) {
                $_POST['param04'] = 'N';
            }

            if ($cc_processor == 'cc_csrc_form.php') {
                include $xcart_dir . '/include/csrc_retrieve_keys.php';
            }

            if ($cc_processor == 'cc_netbanx.php') {
                $_POST['param05'] = is_array($_POST['param05'])
                    ? serialize($_POST['param05'])
                    : '';
            }

            foreach($_POST as $key => $value) {

                if ($key == $XCART_SESSION_NAME) continue;

                if (
                    (
                        $cc_processor == 'cc_authorizenet.php'
                        || $cc_processor == 'ch_authorizenet.php'
                    ) && (
                        $key == 'param01'
                        || $key == 'param02'
                    )
                ) {
                    $value = text_crypt($value);
                }

                func_array2update(
                    'ccprocessors',
                    array(
                        $key => addslashes($value),
                    ),
                    "processor='" . $cc_processor . "'"
                );

            }

        }

    } // if (!empty($cc_processor))

    func_header_location($cc_script);
}

/**
 * $cc_processing_module
 */
if ($mode == 'update') {

    require $xcart_dir . '/include/safe_mode.php';

    if (
        !empty($cc_processor)
        && $subscribe != 'yes'
    ) {

        $cc_processing_module = func_query_first("SELECT $sql_tbl[ccprocessors].*, $sql_tbl[payment_methods].protocol FROM $sql_tbl[ccprocessors], $sql_tbl[payment_methods] WHERE $sql_tbl[ccprocessors].processor = '$cc_processor' AND $sql_tbl[ccprocessors].paymentid = $sql_tbl[payment_methods].paymentid");

        if (empty($cc_processing_module)) {

            $cc_processor_name = func_query_first_cell("SELECT module_name FROM $sql_tbl[ccprocessors] WHERE $sql_tbl[ccprocessors].processor = '$cc_processor'");
            $top_message['content'] =  func_get_langvar_by_name('err_processor_not_included_in_list', array('processor_name' => $cc_processor_name));

            $top_message['type'] = 'E';

            func_header_location('payment_methods.php');

        }

        if (
            $cc_processor == 'cc_authorizenet.php'
            || $cc_processor == 'ch_authorizenet.php'
        ) {

            $cc_processing_module['param01'] = text_decrypt(trim($cc_processing_module["param01"]));

            if (is_null($cc_processing_module['param01'])) {

                x_log_flag('log_decrypt_errors', 'DECRYPT', "Could not decrypt the field 'param01' for AuthorizeNet: AIM payment module", true);

            }

            $cc_processing_module['param02'] = text_decrypt(trim($cc_processing_module["param02"]));

            if (is_null($cc_processing_module['param02'])) {

                x_log_flag('log_decrypt_errors', 'DECRYPT', "Could not decrypt the field 'param02' for AuthorizeNet: AIM payment module", true);

            }

        } elseif (
            $cc_processor == 'ps_paypal.php'
            || $cc_processor == 'ps_paypal_pro.php'
        ) {

            $cc_processing_module['template'] = 'ps_paypal_group.tpl';

            if ($cc_processor == 'ps_paypal.php') {

                $pkey         = 'ipn';
                $akey         = $config['paypal_last_pro_solution'] == 'uk' ? 'uk' : 'pro';
                $asearch     = 'ps_paypal_pro.php';

            } else {

                $pkey         = $config['paypal_solution'] == 'uk' ? 'uk' : 'pro';
                $akey         = 'ipn';
                $asearch     = 'ps_paypal.php';

            }

            $conf_data[$pkey] = $cc_processing_module;
            $conf_data[$akey] = func_query_first("SELECT $sql_tbl[ccprocessors].* FROM $sql_tbl[ccprocessors] WHERE $sql_tbl[ccprocessors].processor = '$asearch'");

            $smarty->assign('conf_data', $conf_data);

            $default_paypal_email = $config['Company']['orders_department'];

            if (empty($default_paypal_email)) {

                $default_paypal_email = $user_account['email'];

            }

            $smarty->assign('default_paypal_email', $default_paypal_email);

            if ($active_modules['XPayments_Connector']) {

                $xpc_dp_processors = array(
                    'pro' => false,
                    'uk' => false,
                );

                foreach ($xpc_dp_processors as $k => $v) {

                    if (xpc_is_paypal_dp_exists($k)) {

                        $tmp = xpc_get_paypal_dp_processor($k);

                        $v = array('use' => $tmp['use_xpc']);

                        if ($tmp['warning']) {
                            $v['warning'] = $tmp['warning'];
                        }

                        $v['processors'] = xpc_get_paypal_dp_list($k);

                        $xpc_dp_processors[$k] = $v;

                    }

                }

                $smarty->assign('xpc_dp_processors', $xpc_dp_processors);

            }

        } elseif ($cc_processor == 'cc_xpc.php') {

            $cc_processors = func_query("SELECT * FROM $sql_tbl[ccprocessors] WHERE paymentid>0 AND processor='cc_xpc.php'");

            $smarty->assign('cc_processors', $cc_processors);

            $cc_processing_module['module_name'] = 'X-Payments payment methods';

        }

    } elseif ($subscribe == 'yes') {

        func_array2update(
            'config',
            array(
                'value' => $cc_processor,
            ),
            "name='active_subscriptions_processor'"
        );

        $config['active_subscriptions_processor'] = $cc_processor;

        if (!zerolen($cc_processor)) {

            $cc_processing_module = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor='$cc_processor'");

            if (
                $cc_processor == 'cc_authorizenet.php'
                || $cc_processor == 'ch_authorizenet.php'
            ) {

                $cc_processing_module['param01'] = text_decrypt(trim($cc_processing_module["param01"]));
                $cc_processing_module['param02'] = text_decrypt(trim($cc_processing_module["param02"]));

            }

        }

    }

} // if ($mode == 'update')

if (empty($cc_processing_module))
    func_header_location('payment_methods.php');

$cc_processing_module = func_array_merge($cc_processing_module, test_ccprocessor($cc_processing_module));

$location[] = array(
    func_get_langvar_by_name('lbl_payment_gateways'),
    'cc_processing.php',
);

if ($cc_processing_module) {

    $location[] = array($cc_processing_module['module_name'], '');

    $smarty->assign('pm_currencies', func_pm_get_currencies($cc_processor));

}

// cc_gestpay
if ($cc_processor == 'cc_gestpay.php') {

    $smarty->assign('ric_number', func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[cc_gestpay_data] WHERE type = 'C'"));
    $smarty->assign('ris_number', func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[cc_gestpay_data] WHERE type = 'S'"));

}

if ($cc_processor == 'cc_csrc_soap.php') {

    $smarty->assign('csrc_soap_cert_path', CSRS_SOAP_CERT_PATH);

}

if ($cc_processor == 'cc_netbanx.php') {

    $tmp = @unserialize($cc_processing_module['param05']);

    if (is_array($tmp)) {

        $netbanx_ptypes = array();

        foreach ($tmp as $value) {

            $netbanx_ptypes[$value] = true;

        }

        $smarty->assign('netbanx_ptypes', $netbanx_ptypes);

    }

}

$test_description = func_get_langvar_by_name('txt_test_descr_' . substr($cc_processor, 0, -4), false, false, true);

$currencies = func_query("SELECT * FROM $sql_tbl[currencies]");

$smarty->assign('currencies',                     $currencies);
$smarty->assign('location',                     $location);
$smarty->assign('timezone_offset',                floor(date('Z') / 3600));
$smarty->assign('main',                            'cc_processing');
$smarty->assign('module_data',                    $cc_processing_module);
$smarty->assign('processing_module',            'payments/' . $cc_processing_module['template']);
$smarty->assign('module_test_mode_description', $test_description);

if (
    file_exists($xcart_dir . '/modules/gold_display.php')
    && is_readable($xcart_dir . '/modules/gold_display.php')
) {
    include $xcart_dir . '/modules/gold_display.php';
}

func_display('admin/home.tpl', $smarty);

?>
