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
 * For explanation of Payment Methods please refer to
 * X-Cart developer's documentation
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: payment_methods.php,v 1.67.2.6 2011/03/03 13:28:13 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

define('IS_MULTILANGUAGE', 1);

require './auth.php';
require $xcart_dir . '/include/security.php';

x_load(
    'backoffice',
    'tests',
    'paypal',
    'payment'
);

if (!empty($active_modules['XPayments_Connector'])) {
    func_xpay_func_load();
}

$location[] = array(func_get_langvar_by_name('lbl_payment_methods'), '');

if (X_USE_PAYPAL_FLOW) {
    x_session_register('store_methods', array());
    x_session_register('store_paypal_solution', false);
}

if ($REQUEST_METHOD == 'POST') {

    require $xcart_dir . '/include/safe_mode.php';

    if ($mode == 'set_methods') {

        // PayPal flow : step 2

        $trans = array(
            'wps'     => 'ipn',
            'wpp'     => 'pro',
            'wpppe' => 'uk',
        );

        if (
            isset($methods)
            && is_array($methods)
        ) {

            $store_paypal_solution = (in_array('paypal', $methods) && $paypal_solution && isset($trans[$paypal_solution]))
                ? $trans[$paypal_solution]
                : false;

            $store_methods = $methods;

        } else {

            $store_paypal_solution = 'ipn';

            $store_methods = array();

        }

        func_header_location("payment_methods.php?mode=finalize");

    } elseif ($mode == 'save_methods') {

        // PayPal flow : step 3
        // Add methods

        if (is_array($methods) && count($methods) > 0) {

            $methods = array_merge($store_methods, $methods);

        } else {

            $methods = $store_methods;

        }

        if ($paypal_solution) {

            $store_paypal_solution = $paypal_solution;

        } elseif (!$store_paypal_solution) {

            $store_paypal_solution = 'ipn';

        }

        foreach ($methods as $processor) {

            if ($processor == 'manual') {

                // Add CC manual processing
                func_array2update(
                    'payment_methods',
                    array(
                        'active' => 'Y',
                    ),
                    'paymentid = 1'
                );

                continue;

            }

            if (preg_match('/^pm\.(\d+)$/Ss', $processor, $match)) {

                // Enable non-CC methods
                func_array2update(
                    'payment_methods',
                    array(
                        'active' => 'Y',
                    ),
                    "paymentid = '" . $match[1] . "'"
                );

                continue;

            }

            $orderby = 999;

            if ($processor == 'paypal') {
                $processor = 'ps_paypal.php';
                $orderby = 0;
            }

            $paymentid = func_add_processor($processor, $orderby);

            if (
                $processor == 'ps_paypal.php'
                && $store_paypal_solution
            ) {

                // Enable PayPal Pro methods
                if (
                    $config['paypal_solution'] == 'pro'
                    || $config['paypal_solution'] == 'express'
                ) {

                    func_array2insert(
                        'config',
                        array(
                            'name'     => 'paypal_last_pro_solution',
                            'value' => 'pro',
                        ),
                        true
                    );

                } elseif ($config['paypal_solution'] == 'uk') {

                    func_array2insert(
                        'config',
                        array(
                            'name'     => 'paypal_last_pro_solution',
                            'value' => 'uk',
                        ),
                        true
                    );

                }

                func_array2insert(
                    'config',
                    array(
                        'name'     => 'paypal_solution',
                        'value' => $paypal_solution,
                    ),
                    true
                );

                switch ($paypal_solution) {
                    case 'ipn':
                        func_array2update(
                            'payment_methods',
                            array(
                                'active' => 'Y',
                            ),
                            "processor_file = 'ps_paypal.php'"
                        );

                        break;

                    case 'pro':
                    case 'uk':
                        $pid = func_query_first_cell("SELECT $sql_tbl[payment_methods].paymentid FROM $sql_tbl[payment_methods], $sql_tbl[ccprocessors] WHERE $sql_tbl[payment_methods].processor_file = 'ps_paypal_pro.php' AND $sql_tbl[payment_methods].processor_file = $sql_tbl[ccprocessors].processor AND $sql_tbl[payment_methods].paymentid <> $sql_tbl[ccprocessors].paymentid");

                        func_array2update(
                            'payment_methods',
                            array(
                                'active' => 'Y',
                            ),
                            "paymentid = '" . $pid . "'"
                        );

                    case 'express':
                        $pid = func_query_first_cell("SELECT $sql_tbl[payment_methods].paymentid FROM $sql_tbl[payment_methods], $sql_tbl[ccprocessors] WHERE $sql_tbl[payment_methods].processor_file = 'ps_paypal_pro.php' AND $sql_tbl[payment_methods].processor_file = $sql_tbl[ccprocessors].processor AND $sql_tbl[payment_methods].paymentid = $sql_tbl[ccprocessors].paymentid");

                        func_array2update(
                            'payment_methods',
                            array(
                                'active' => 'Y',
                            ),
                            "paymentid = '" . $pid . "'"
                        );

                        break;
                }

            } elseif ($paymentid) {

                func_array2update(
                    'payment_methods',
                    array(
                        'active' => 'Y',
                    ),
                    "paymentid = '" . $paymentid . "'"
                );

            }

        }

        if ($submode == 'add_paypal') {

            func_header_location("cc_processing.php?mode=update&cc_processor=ps_paypal.php");

        }

    } elseif ($mode == 'change_force_offline_paymentid'){
        func_array2update(
            'config',
            array(
                'value' => $force_offline_paymentid,
            ),
            "name = 'force_offline_paymentid'"
        );
        $top_message['content'] = func_get_langvar_by_name('msg_adm_payment_methods_upd');
        $top_message['anchor'] = 'section_force_offline_paymentid';
    } else {

        if (is_array($posted_data)) {

            $paypal_directid = func_query_first_cell("SELECT $sql_tbl[payment_methods].paymentid FROM $sql_tbl[payment_methods], $sql_tbl[ccprocessors] WHERE $sql_tbl[payment_methods].processor_file='ps_paypal_pro.php' AND $sql_tbl[payment_methods].processor_file=$sql_tbl[ccprocessors].processor AND $sql_tbl[payment_methods].paymentid<>$sql_tbl[ccprocessors].paymentid");

            foreach ($posted_data as $k => $v) {
                settype($v['surcharge'], 'float');
                settype($v['surcharge_type'], 'string');

                $v['active']     = (!empty($v['active']) ? 'Y' : 'N');
                $v['is_cod']     = (!empty($v['is_cod']) ? 'Y' : 'N');
                $v['af_check']     = (!empty($v['af_check']) ? 'Y' : 'N');
                $v['surcharge'] = func_convert_number($v['surcharge']);

                if ($v['surcharge_type'] != "%") {
                    $v['surcharge_type'] = "$";
                }

                func_languages_alt_insert('payment_method_' . $k, $v['payment_method'], $shop_language);
                func_languages_alt_insert('payment_details_' . $k, $v['payment_details'], $shop_language);

                if ($shop_language != $config['default_admin_language']) {
                    unset($v['payment_method'], $v['payment_details']);
                }

                func_membership_update('pmethod', $k, $v['membershipids'], 'paymentid');

                unset($v['membershipids']);

                func_array2update(
                    'payment_methods',
                    $v,
                    "paymentid = '$k'"
                );

                if (
                    $paypal_directid
                    && $paypal_directid == $k
                ) {
                    func_array2update(
                        'payment_methods',
                        array(
                            'orderby' => $v['orderby'],
                        ),
                        "processor_file = 'ps_paypal_pro.php'"
                    );
                }

            }

            func_data_cache_get('payments_https', array(), true);

            func_disable_paypal_methods($config['paypal_solution']);

            func_check_force_offline_paymentid_for_cod();

            $top_message['content'] = func_get_langvar_by_name('msg_adm_payment_methods_upd');

        } else {

            $top_message['content'] = func_get_langvar_by_name('msg_adm_err_payment_methods_upd');

            $top_message['type'] = 'E';

        }
    }

    func_header_location('payment_methods.php');
}

/**
 * Obtain payment methods
 */
$payment_methods = func_query("SELECT pm.*, cc.module_name, cc.processor, cc.type, cc.param01 FROM $sql_tbl[payment_methods] AS pm LEFT JOIN $sql_tbl[ccprocessors] AS cc ON (pm.paymentid=cc.paymentid OR pm.paymentid<>cc.paymentid AND pm.processor_file=cc.processor AND cc.processor != 'cc_xpc.php') ORDER BY pm.active DESC, pm.orderby, pm.paymentid");

$payment_methods = test_payment_methods($payment_methods);

$list_is_empty = func_is_pmethods_list_empty($payment_methods);

if (
    X_USE_PAYPAL_FLOW
    && $mode == 'finalize'
) {

    // Prepare offline payment methods
    foreach ($payment_methods as $k => $p) {

        $payment_methods[$k]['id'] = 'pm.' . $p['paymentid'];

        if ($p['paymentid'] == 1) {

            unset($payment_methods[$k]);

        }

    }

    $smarty->assign('paypal_enabled', !empty($store_paypal_solution));
    $smarty->assign('paypal_solution', $store_paypal_solution);
}

x_session_register('recent_payment_methods');

$smarty->assign('recent_payment_methods',     $recent_payment_methods);
$smarty->assign('use_paypal_flow',             X_USE_PAYPAL_FLOW);
$smarty->assign('list_is_empty',             $list_is_empty);

/**
 * Hide not usable PayPal methods
 */
$is_paypal_exists = false;
$is_paypal_enabled = false;

if (is_array($payment_methods)) {

    $_payment_methods = array();

    $showManualWarning = false;

    foreach ($payment_methods as $pm) {

        $skip = false;

        if (
            true !== $store_cc
            && 1 == $pm['paymentid']
            && 'Y' == $pm['active']
        ) {
            $showManualWarning = true;
        }

        if (
            $pm['processor_file'] == "ps_paypal.php"
            || $pm['processor_file'] == "ps_paypal_pro.php"
        ) {

            $is_paypal_enabled = true;

            // $config['paypal_solution'] = [ ipn | pro | express | uk ]
            switch ($config['paypal_solution']) {
                case 'ipn':

                    if ($pm['processor_file'] == "ps_paypal_pro.php") {

                        $skip = true;

                    }

                    break;

                case 'pro':
                case 'uk':

                    if ($pm['processor_file'] == "ps_paypal.php") {

                        $skip = true;

                    } else {

                        $is_paypal_exists = true;

                    }

                    if (!preg_match('/payment_cc\.tpl$/Ss', $pm["payment_template"])) {

                        $pm['disable_checkbox'] = true;

                    } else {

                        $pm['control_checkbox'] = true;

                    }

                    break;

                case 'express':

                    if (
                        $pm['processor_file'] == "ps_paypal.php"
                        || preg_match("/(payment_cc\.tpl)$/", $pm['payment_template'])
                    ) {

                        $skip = true;

                    } else {

                        $is_paypal_exists = true;

                    }

                    break;
            }
        }

        if ($skip) {

            continue;

        }

        $_payment_methods[] = $pm;

    }

    $payment_methods = array_values($_payment_methods);

    // PayPal Pro methods sorting
    if (
        $is_paypal_enabled
        && (
            $config['paypal_solution'] == 'pro'
            || $config['paypal_solution'] == 'uk'
        )
    ) {

        $i1 = false;
        $i2 = false;

        foreach ($payment_methods as $k => $pm) {

            if (
                $pm['processor_file'] == "ps_paypal.php"
                || $pm['processor_file'] == "ps_paypal_pro.php"
            ) {

                if (preg_match('/payment_cc\.tpl$/Ss', $pm["payment_template"])) {

                    $i1= $k;

                } else {

                    $i2 = $k;

                }

            }

        }

        $tmp = $payment_methods[$i2];

        $payment_methods[$i2] = $payment_methods[$i1];

        $payment_methods[$i1] = $tmp;

    }

}

if (!empty($payment_methods)) {

    foreach ($payment_methods as $k => $v) {

        $tmp = func_get_languages_alt('payment_method_' . $v['paymentid']);

        if (!empty($tmp)) {

            $payment_methods[$k]['payment_method'] = $tmp;

        }

        $tmp = func_get_languages_alt('payment_details_' . $v['paymentid']);

        if (!empty($tmp)) {

            $payment_methods[$k]['payment_details'] = $tmp;

        }

        $tmp = func_query_column("SELECT membershipid FROM $sql_tbl[pmethod_memberships] WHERE paymentid = '$v[paymentid]'");

        if (!empty($tmp)) {

            $payment_methods[$k]['membershipids'] = array();

            foreach ($tmp as $mid) {

                $payment_methods[$k]['membershipids'][$mid] = 'Y';

            }

        }

    }

}

if ($config['active_subscriptions_processor']) {

    $active_sb = test_ccprocessor(func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor='$config[active_subscriptions_processor]'"));

} else {

    $active_sb = array(
        'status' => 1,
    );

}

$smarty->assign('active_sb', $active_sb);

$cc_module_files = func_query("select * from $sql_tbl[ccprocessors] where paymentid='0' and processor<>'ps_paypal_pro.php' order by type,module_name");

if (!empty($active_modules['XPayments_Connector'])) {

    $cc_module_files = xpc_filter_hidden_processors($cc_module_files);

}

$sb_module_files = func_query("select * from $sql_tbl[ccprocessors] where type='C' and background='Y' order by module_name");

$anchors = array(
    'payment_methods'     => 'lbl_payment_methods',
    'payment_gateways'     => 'lbl_payment_gateways',
);

foreach ($anchors as $anchor => $anchor_label) {

    $dialog_tools_data['left'][] = array(
        'link' => "#" . $anchor,
        'title' => func_get_langvar_by_name($anchor_label),
    );
}

$check_active_payments = func_check_active_payments();

if (
    $check_active_payments !== true
    && !X_USE_PAYPAL_FLOW
) {

    $smarty->assign(
        'top_message',
        array(
            'type'         => 'W',
            'content'     => $check_active_payments,
        )
    );

} elseif (
    $showManualWarning
    && !$list_is_empty
) {

    $smarty->assign(
        'top_message',
        array(
            'type'      => 'W',
            'content'   => func_get_langvar_by_name('lbl_manual_processing_warning'),
        )
    );

}

if (
    X_USE_PAYPAL_FLOW
    && $list_is_empty
) {

    if (
        isset($accept)
        && $accept
    ) {

        $smarty->assign('paypal_flow_accept', $accept);

        if ($accept == 'cc') {

            $all_methods = func_query("SELECT cc.*, IF(pm.payment_method IS NULL, '', 'Y') as is_added FROM $sql_tbl[ccprocessors] as cc LEFT JOIN $sql_tbl[payment_methods] as pm ON cc.paymentid = pm.paymentid WHERE cc.processor <> 'ps_paypal_pro.php' AND cc.processor <> 'ps_paypal.php' ORDER BY cc.module_name");

            if (!empty($active_modules['XPayments_Connector'])) {

                $all_methods = xpc_filter_hidden_processors($all_methods);

            }

            $payment_methods_complex = array();
            $payment_methods_gateway = array();
            $payment_methods_other = array();

            foreach ($all_methods as $k => $m) {

                $m['id'] = $m['processor'];

                if ($m['processor'] == 'cc_xpc.php') {

                    $m['id'] .= '_' . $m['param01'];

                }

                if (func_is_complex_processor($m['processor'])) {

                    $payment_methods_complex[]     = $m['id'];
                    $payment_methods_other[]     = $m;

                } elseif (
                    $m['is_added'] == 'Y'
                    || func_is_selected_gateway($m['processor'])
                ) {

                    $payment_methods_gateway[] = $m;

                } else {

                    $payment_methods_other[] = $m;

                }
            }

            function func_cmp_gateways($a, $b) {

                $a = func_is_selected_gateway($a['processor']);

                $b = func_is_selected_gateway($b['processor']);

                if ($a == $b) {

                    return 0;

                }

                return $a ? 1 : -1;
            }

            usort($payment_methods_gateway, 'func_cmp_gateways');

            $smarty->assign('payment_methods_complex',     $payment_methods_complex);
            $smarty->assign('payment_methods_gateway',     $payment_methods_gateway);
            $smarty->assign('payment_methods_other',     $payment_methods_other);
            $smarty->assign('is_paypal_enabled',         $is_paypal_enabled);
        }

    } elseif ($mode == 'finalize') {

        $smarty->assign('submode', $mode);

    } else {

        $store_methods = array();

        $store_paypal_solution = false;

    }

}

$smarty->assign('is_paypal_exists',     $is_paypal_exists);
$smarty->assign('dialog_tools_data',     $dialog_tools_data);
$smarty->assign('cc_modules',             $cc_module_files);
$smarty->assign('sb_modules',             $sb_module_files);
$smarty->assign('memberships',             func_get_memberships());
$smarty->assign('payment_methods',         $payment_methods);
$smarty->assign('main',                 'payment_methods');

// Assign the current location line
$smarty->assign('location', $location);

if (
    file_exists($xcart_dir . '/modules/gold_display.php')
    && is_readable($xcart_dir . '/modules/gold_display.php')
) {
    include $xcart_dir . '/modules/gold_display.php';
}

func_display('admin/home.tpl', $smarty);

?>
