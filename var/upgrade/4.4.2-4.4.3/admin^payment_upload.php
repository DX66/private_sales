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
 * Upload information about the payment of commissions to partners
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: payment_upload.php,v 1.40.2.1 2011/01/10 13:11:46 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

define('BENCH_BLOCK', true);

require './auth.php';
require $xcart_dir.'/include/security.php';

if (!$active_modules['XAffiliate'])
    func_403(13);

x_load('files');

$location[] = array(func_get_langvar_by_name('lbl_payment_upload'), '');

/**
 * Define data for the navigation within section
 */
$dialog_tools_data['right'][] = array('link' => 'partner_report.php', 'title' => func_get_langvar_by_name('lbl_partner_accounts'));
$dialog_tools_data['right'][] = array('link' => 'partner_orders.php', 'title' => func_get_langvar_by_name('lbl_partners_orders'));

if ($mode == 'upload') {
    require $xcart_dir.'/include/safe_mode.php';
    $userfile = func_move_uploaded_file('userfile');
    $fp = func_fopen($userfile, 'r', true);
    if (!$fp) {
        func_header_location("error_message.php?cant_open_file");
    }

    $line_num = 1;
    while ($columns = fgetcsv ($fp, 65536, $delimiter)) {
        $paid = ((strtoupper(trim($columns[1])) == 'Y') ? 'Y' : 'N');
        $oid = $columns[0];

        if (!is_numeric($oid)) {
            $top_message['content'] = (($line_num == 0) ? func_get_langvar_by_name('msg_payment_upload_error_format') : func_get_langvar_by_name('msg_payment_upload_error_line', array('line_num' => $line_num)));
            $top_message['type'] = 'E';
            func_header_location('payment_upload.php');
        }

        db_query ("UPDATE $sql_tbl[partner_payment] SET paid='".$paid."', add_date='".XC_TIME."' WHERE orderid='$oid'");
        $line_num++;
    }

    fclose($fp);
    $top_message['content'] = func_get_langvar_by_name('msg_payment_upload_success');
    $top_message['type'] = 'I';
    func_header_location('payment_upload.php');
} else {
    $smarty->assign ('main', 'payment_upload');
}

// Assign the current location line
$smarty->assign('location', $location);

// Assign the section navigation data
$smarty->assign('dialog_tools_data', $dialog_tools_data);

if (
    file_exists($xcart_dir.'/modules/gold_display.php')
    && is_readable($xcart_dir.'/modules/gold_display.php')
) {
    include $xcart_dir.'/modules/gold_display.php';
}
func_display('admin/home.tpl',$smarty);
?>
