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
 * Base authentication, defining common variables 
 * and including common scripts
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Customer interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: auth.php,v 1.61.2.1 2011/01/10 13:11:42 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

// Prevent double inclusion.
if (defined('INCLUDED_AUTH_PHP')) {

    return;

}

define('INCLUDED_AUTH_PHP', 1);

if (!defined('INCLUDED_PREAUTH_PHP')) {

    require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'preauth.php';

}

if (!defined('INCLUDED_POSTAUTH_PHP')) {

    require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'postauth.php';

}

x_session_register('require_change_password');

if (!empty($login)) {

    if (
        !strstr($PHP_SELF, 'change_password.php')
        && !empty($require_change_password[$login_type])
    ) {

        // Require password change before proceed
        $top_message['content'] = func_get_langvar_by_name('txt_chpass_msg');
        $top_message['type']    = 'E';

        func_header_location('change_password.php');

    }

    if ($REQUEST_METHOD == 'GET') {

        x_load('paypal');

        func_paypal_check_ec_token_ttl();

    }

}

/**
 * Check language flags icons
 */
if (
    $config['Appearance']['line_language_selector'] == 'F'
    && !func_check_languages_flags()
) {
    $config['Appearance']['line_language_selector'] = 'N';

    $smarty->assign('config', $config);

    func_array2update(
        'config',
        array(
            'value' => 'N',
        ),
        'name=\'line_language_selector\''
    );

    x_log_flag(
        'log_database',
        'DATABASE',
        func_get_langvar_by_name(
            'txt_displaying_language_icons_disabled_lang_C',
            false,
            false,
            true
        )
    );
}

x_session_register('login_antibot_on', '');

$smarty->assign('login_antibot_on', $login_antibot_on);

?>
