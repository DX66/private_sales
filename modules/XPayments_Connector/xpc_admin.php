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
 * Core module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: xpc_admin.php,v 1.14.2.5 2011/01/10 13:12:04 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header('Location: ../../'); die('Access denied'); }

func_xpay_func_load();

if ('POST' == $REQUEST_METHOD) {

    if ('deploy_configuration' == $mode) {

        $xpc_config = func_xpc_get_configuration($_POST['deploy_configuration']);

        if (true === func_xpc_check_deploy_configuration($xpc_config)) {

            func_xpc_store_configuration($xpc_config);

            $top_message = array(
                'type'      => 'I',
                'content'   => func_get_langvar_by_name('txt_xpc_msg_configuration_deploy_success'),
            );  

        } else {

            $top_message = array(
                'type'      => 'E',
                'content'   => func_get_langvar_by_name('txt_xpc_msg_configuration_deploy_fail'),
            );

        }

        func_header_location('configuration.php?option=XPayments_Connector');
    }

}


$is_module_configured = xpc_is_module_configured();

// Check if $mode is enabled for XPayments_Connector option
if (!in_array($_GET['mode'], array('test_module', 'export', 'import', 'clear', ''))) {
    func_page_not_found();
}

$mode = $_GET['mode'];

if (!$is_module_configured && !empty($mode)) {
    func_header_location('configuration.php?option=XPayments_Connector');
}

x_session_register('pm_list');

if ($mode == 'export') {

    // Get payment methods list from X-Payments
    $pm_list = null;

    $list = xpc_request_get_payment_methods();

    if ($list) {

        $pm_list = $list;

        func_header_location ('configuration.php?option=XPayments_Connector#import');

    } elseif (is_array($list)) {

        $top_message = array(
            'type' => 'W',
            'content' => func_get_langvar_by_name('txt_xpc_msg_import_request_empty')
        );

    } else {

        $top_message = array(
            'type' => 'E',
            'content' => func_get_langvar_by_name('txt_xpc_msg_import_request_failed')
        );

    }

    func_header_location ('configuration.php?option=XPayments_Connector');

} elseif ($mode == 'import') {

    // Save payment methods to DB
    if (!empty($pm_list) && is_array($pm_list)) {

        $result = xpc_import_payment_methods($pm_list);

        if ($result) {

            $pm_list = null;

            x_session_unregister('pm_list');

            $top_message['content'] = func_get_langvar_by_name('txt_xpc_msg_import_success');

        } else {

            $top_message['type'] = 'E';
            $top_message['content'] = func_get_langvar_by_name('txt_xpc_msg_import_failed');

        }

    } else {

        $result = false;

        $top_message['type'] = 'E';
        $top_message['content'] = func_get_langvar_by_name('txt_xpc_msg_import_no_pm');

    }

    func_header_location ('configuration.php?option=XPayments_Connector');

} elseif ($mode == 'clear') {

    // Clear payment methods list from session
    $pm_list = null;

    x_session_unregister('pm_list');

    func_header_location ('configuration.php?option=XPayments_Connector');

} elseif ($mode == 'test_module') {

    // Test module
    $result = xpc_request_test();

    if (true === $result['status']) {

        $top_message['content'] = func_get_langvar_by_name('txt_xpc_msg_test_success');

    } else {

        $message = false === $result['status']
            ? $result['response']
            : $result['response']['message'];

        $top_message['type'] = 'W';
        $top_message['content'] = func_get_langvar_by_name('txt_xpc_msg_test_failed') . '<br /><br /><strong>' . $message . '</strong>';

    }

    func_header_location ('configuration.php?option=XPayments_Connector');

}

$smarty->assign('is_module_configured', $is_module_configured);

$is_check_requirements = xpc_check_requirements();

$check_requirements_errs = array();

if ($is_check_requirements & XPC_REQ_CURL) {
    $check_requirements_errs[] = func_get_langvar_by_name('txt_xpc_reqerr_curl');
}

if ($is_check_requirements & XPC_REQ_OPENSSL) {
    $check_requirements_errs[] = func_get_langvar_by_name('txt_xpc_reqerr_openssl');
}

if ($is_check_requirements & XPC_REQ_DOM) {
    $check_requirements_errs[] = func_get_langvar_by_name('txt_xpc_reqerr_dom');
}

if (count($check_requirements_errs) > 0) {
    $smarty->assign('system_requirements_errors', $check_requirements_errs);
}

$module_configured_status = xpc_get_module_system_errors();

$check_sys_errs = array();

if ($module_configured_status & XPC_SYSERR_CARTID) {
    $check_sys_errs[] = func_get_langvar_by_name('txt_xpc_syserr_cartid');
}

if ($module_configured_status & XPC_SYSERR_URL) {
    $check_sys_errs[] = func_get_langvar_by_name('txt_xpc_syserr_url');
}

if ($module_configured_status & XPC_SYSERR_PUBKEY) {
    $check_sys_errs[] = func_get_langvar_by_name('txt_xpc_syserr_pubkey');
}

if ($module_configured_status & XPC_SYSERR_PRIVKEY) {
    $check_sys_errs[] = func_get_langvar_by_name('txt_xpc_syserr_privkey');
}

if ($module_configured_status & XPC_SYSERR_PRIVKEYPASS) {
    $check_sys_errs[] = func_get_langvar_by_name('txt_xpc_syserr_privkeypass');
}

if (count($check_sys_errs) > 0) {
    $smarty->assign('check_sys_errs', $check_sys_errs);
}

if (!empty($pm_list) && is_array($pm_list)) {
    $smarty->assign('pm_found', xpc_is_payment_methods_exists());
    $smarty->assign('pm_list', $pm_list);
}

$xpc_recommends = xpc_check_pci_dss_requirements();

list(
    $warning_fields,
    $error_fields
) = xpc_check_fields();

if (false !== $error_fields) {

    $xpc_recommends['E']['error_fields'] = func_get_langvar_by_name(
        'txt_xpc_profiles_fields_error', 
        array(
            'fields' => $error_fields,
        )
    );

} elseif (false !== $warning_fields) {

    $xpc_recommends['W']['warning_fields'] = func_get_langvar_by_name(
        'txt_xpc_profiles_fields_warning', 
        array(
            'fields' => $warning_fields,
        )
    );

}

if (!empty($xpc_recommends)) {
    $smarty->assign('xpc_recommends', $xpc_recommends);
}

?>
