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
 * @subpackage Provder interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: auth.php,v 1.73.2.1 2011/01/10 13:12:08 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

define('AREA_TYPE', 'P');

if (
    file_exists('../top.inc.php')
    && is_readable('../top.inc.php')
) {
    include_once '../top.inc.php';
}

if (!defined('DIR_CUSTOMER')) die("ERROR: Can not initiate application! Please check configuration.");

require_once $xcart_dir . '/init.php';

x_load('backoffice');

x_session_register('login');
x_session_register('login_type');
x_session_register('logged');

x_session_register('export_ranges');

x_session_register('top_message');

if (!empty($top_message)) {

    $smarty->assign('top_message', $top_message);

    if($config['Adaptives']['is_first_start'] != 'Y')
        $top_message = '';

    x_session_save('top_message');
}

x_session_register('login_antibot_on', '');

$smarty->assign('login_antibot_on', $login_antibot_on);

$current_area = "P";

require $xcart_dir . '/include/admin_security.php';

if (!defined('HTTPS_CHECK_SKIP')) {

    include $xcart_dir . '/https.php';

}

$_loc = (!empty($active_modules['Simple_Mode'])) ? $xcart_catalogs['admin'].'/' : '';

$location = array();
$location[] = array(func_get_langvar_by_name('lbl_main_page'), $_loc.'home.php');

if (
    file_exists($xcart_dir . '/modules/gold_auth.php')
    && is_readable($xcart_dir . '/modules/gold_auth.php')
) {
    include $xcart_dir . '/modules/gold_auth.php';
}

include $xcart_dir . '/include/check_useraccount.php';

include $xcart_dir . '/include/get_language.php';

/**
 * Single Mode always active for root account
 */
if(!empty($user_account) && $user_account['flag']=="RP") $single_mode = true;

$need_storefront_link = !empty($login) && !empty($active_modules['Simple_Mode']);

$smarty->assign('need_storefront_link', $need_storefront_link);

if ($need_storefront_link) func_storefront_update();

x_session_register('require_change_password');

if (!empty($login) && !strstr($PHP_SELF,'change_password.php') && !empty($require_change_password[$login_type])) {

    // Require password change before proceed
    $top_message['content'] = func_get_langvar_by_name('txt_chpass_msg');
    $top_message['type'] = 'E';
    func_header_location('change_password.php');
}

x_session_save();

$smarty->assign('redirect', 'provider');

?>
