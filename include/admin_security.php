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
 * Check security for admin area
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: admin_security.php,v 1.21.2.1 2011/01/10 13:11:48 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

if (
    ! (
        $current_area == 'A'
        || (
            !empty($active_modules['Simple_Mode'])
            && $current_area == 'P'
        )
    )
    || empty($login)
) {
    return;
}

x_load('backoffice');

if (
    !isset($config['allowed_ips'])
    || empty($config['allowed_ips'])
) {

    func_register_admin_ip($REMOTE_ADDR);

    return;

}

// Check IP registration codes expiration date
if (
    isset($config['ip_register_codes'])
    && !empty($config['ip_register_codes'])
) {

    if (!is_array($config['ip_register_codes']))
        $config['ip_register_codes'] = unserialize($config['ip_register_codes']);

    if (is_array($config['ip_register_codes'])) {

        $changed = false;

        foreach($config['ip_register_codes'] as $k => $v) {

            if ($v['expiry'] < XC_TIME) {

                $changed = true;

                func_unset($config['ip_register_codes'], $k);

            }

        }

        if ($changed) {

            func_array2insert(
                'config',
                array(
                    'name'  => 'ip_register_codes',
                    'value' => addslashes(serialize($config['ip_register_codes'])),
                ),
                true
            );

        }

    }

}

// Check IP address for Admin area
if (!func_check_allow_admin_ip()) {

    // Not allowed IP - log out
    x_session_register('payment_cc_fields');

    $payment_cc_fields = array();

    $utype = $current_area;

    if (
        !empty($active_modules['Simple_Mode'])
        && $utype == 'A'
    ) {
        $utype = 'P';
    }

    func_store_login_action($logged_userid, $utype, 'check_security', 'restricted');

    func_end_user_session();

    func_send_admin_ip_reg();

    $access_status     = '';
    $merchant_password = '';
    $logout_user       = true;

    func_ge_erase();

    x_session_unregister('hide_security_warning');

    x_session_register('top_message');

    $top_message = array(
        'content' => func_get_langvar_by_name('lbl_ip_blocked_for_admin_area_note'),
        'type'    => 'W'
    );

    x_session_register('_session_force_regenerate');

    $_session_force_regenerate = true;

    func_header_location('home.php');

}

define('IS_ADMIN_USER', true);

$smarty->assign('is_admin_user', true);

?>
