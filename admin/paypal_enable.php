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
 * PayPal enabling interface 
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: paypal_enable.php,v 1.22.2.2 2011/01/25 09:43:11 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (
    file_exists('../top.inc.php')
    && is_readable('../top.inc.php')
) {
    include_once '../top.inc.php';
}

if (!defined('DIR_ADMIN')) die("ERROR: Can not initiate application! Please check configuration.");

require_once $xcart_dir.'/init.php';

x_load('backoffice');

if (!empty($paypal_enable_id) && func_query_first_cell("SELECT value FROM $sql_tbl[config] WHERE name='paypal_enable_id' AND value='$paypal_enable_id'") !== false) {
    // activate paypal processing...
    func_disable_paypal_methods($config['paypal_solution'], true);

    x_session_register('top_message');
    $top_message['content'] = func_get_langvar_by_name('msg_paypal_processing_enabled');
    $top_message['type'] = 'I';
    db_query("DELETE FROM $sql_tbl[config] WHERE name='paypal_enable_id' AND value='$paypal_enable_id'");

    x_session_register('login');
    x_session_register('login_type');

    // Check: if admin is already logged in, then redirect him to the settings of paypal

    if (!empty($login) && !empty($login_type)) {
        if (!empty($active_modules['Simple_Mode']) && ($login_type == 'P' || $login_type == 'A') || $login_type == 'A') {
            func_header_location("cc_processing.php?mode=update&cc_processor=ps_paypal.php");
        }
    }
}

func_header_location('home.php');

?>
