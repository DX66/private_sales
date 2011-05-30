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
 * PGP encryption tests
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: test_pgp.php,v 1.27.2.1 2011/01/10 13:11:47 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */
define('USE_TRUSTED_POST_VARIABLES',1);
$trusted_post_variables = array('source_data');

require './auth.php';
require $xcart_dir.'/include/security.php';

x_load('mail');

$location[] = array(func_get_langvar_by_name('lbl_testing_data_encryption'), '');

if (isset($source_data) && !empty($source_data)) {
    $config['Security']['crypt_method'] = $method;
    $result_data = func_pgp_encrypt($source_data);
    $smarty->assign('source_data', $source_data);
    $smarty->assign('result_data', $result_data);
    $smarty->assign('method', $method);
    if (isset($show_errors)) {
        $smarty->assign('show_errors', $show_errors);
        $smarty->assign('error_output', $config['PGP_output']);
    }
    if (!empty($test_email)) {
        $smarty->assign('test_email', $test_email);
        $current_user = func_query_first("SELECT email FROM $sql_tbl[customers] WHERE id='$logged_userid'");
        func_send_simple_mail($test_email, "Test of PGP/GnuPG", $result_data, $current_user['email']);
    }
}

$crypt_method = func_query_first_cell("SELECT variants FROM $sql_tbl[config] WHERE name='crypt_method' AND category='Security'");
$vars = func_parse_str(trim($crypt_method), "\n", ":");
$vars = func_array_map('trim', $vars);
$methods = array();
foreach ($vars as $k=>$v) {
    if ($v == 'lbl_none') $v = func_get_langvar_by_name($v);
    $methods[$k] = array(
        'id' => $k,
        'name' => $v
    );
}

$smarty->assign('methods', $methods);
$smarty->assign('main', 'test_pgp');

// Assign the current location line
$smarty->assign('location', $location);

if (
    file_exists($xcart_dir.'/modules/gold_display.php')
    && is_readable($xcart_dir.'/modules/gold_display.php')
) {
    include $xcart_dir.'/modules/gold_display.php';
}
func_display('admin/home.tpl',$smarty);

?>
