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
 * Inventory update interface
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Provder interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: inv_update.php,v 1.50.2.2 2011/01/10 13:12:09 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './auth.php';
require $xcart_dir.'/include/security.php';

x_load('files');

func_set_time_limit(1800);

$location[] = array(func_get_langvar_by_name('lbl_update_inventory'), '');

if ($REQUEST_METHOD=="POST") {

    $provider_condition= ($single_mode ? '' : " AND $sql_tbl[products].provider='$logged_userid'");

    // Check post_max_size exceeding

    func_check_uploaded_files_sizes('userfile', 522);

    $userfile = func_move_uploaded_file('userfile', 522);
    $pids = $err_rows = array();
    $updated = 0;

    if ($fp = func_fopen($userfile,'r',true)) {

        while ($columns = fgetcsv ($fp, 65536, $delimiter)) {

            if (empty($columns[0])) {
                continue;
            }

            $orig_row = strip_tags(htmlentities(implode($delimiter, $columns)));

            if (count($columns) < 2) {
                $err_rows[] = $orig_row;
                continue;
            }

            $columns[0] = addslashes($columns[0]);

            $pid = func_query_first_cell ("SELECT productid FROM $sql_tbl[products] WHERE (productcode='$columns[0]' OR productid = '$columns[0]') $provider_condition");
            $vid = 0;
            if (!empty($active_modules['Product_Options'])) {
                $vid = func_query_first_cell("SELECT $sql_tbl[variants].variantid FROM $sql_tbl[variants], $sql_tbl[products] WHERE $sql_tbl[variants].productid = $sql_tbl[products].productid AND ($sql_tbl[variants].productcode='$columns[0]' OR $sql_tbl[variants].variantid = '$columns[0]') ".$provider_condition);
            }

            if (empty($pid) && empty($vid)) {
                continue;
            }

            if (!empty($pid)) {
                $pids[] = $pid;
            } else {
                $pids[] = func_query_first_cell("SELECT productid FROM $sql_tbl[variants] WHERE variantid = '$vid'");
            }

            if ($what == 'p') {

                // Check price value
                if (!func_is_price($columns[1])) {
                    $err_rows[] = $orig_row;
                    continue;
                } else {
                    $columns[1] = func_detect_price($columns[1]);
                }

                if (strlen($columns[2]) == 0 || $columns[2] < 1) {
                    $columns[2] = 1;
                }

                $membershipid = func_detect_membership(trim($columns[3]));
                if (!empty($pid)) {
                    db_query ("UPDATE $sql_tbl[pricing] SET price='".$columns[1]."' WHERE productid='$pid' AND quantity='".(int)$columns[2]."' AND membershipid = '$membershipid' AND variantid = '0'");
                }
                if (!empty($vid)) {
                    db_query ("UPDATE $sql_tbl[pricing] SET price='".$columns[1]."' WHERE quantity='".(int)$columns[2]."' AND membershipid = '$membershipid' AND variantid = '$vid'");
                }

            } else {

                $columns[1] = abs(intval($columns[1]));
                if (!empty($pid)) {
                    db_query ("UPDATE $sql_tbl[products] SET avail='".(int)$columns[1]."' WHERE productid='$pid' $provider_condition");
                }
                if (!empty($vid)) {
                    db_query ("UPDATE $sql_tbl[variants] SET avail='".(int)$columns[1]."' WHERE variantid='$vid'");
                }
            }

            $updated++;
        }

        $smarty->assign('main', 'inv_updated');
        $smarty->assign('updated_items', $updated);

        // Display rows with invalid formats for provider (no more then 200 rows on page)

        if (!empty($err_rows)) {
            $smarty->assign('err_rows', array_slice($err_rows, 0, 200));
        }

        if (!empty($pids)) {
            func_build_quick_flags($pids);
            func_build_quick_prices($pids);
        }

        fclose($fp);

    } else {

        $smarty->assign('main', 'error_inv_update');
    }
    @unlink($userfile);

} else {

    $smarty->assign ('main', 'inv_update');
}

$smarty->assign('upload_max_filesize', func_convert_to_megabyte(func_upload_max_filesize()));

// Assign the current location line
$smarty->assign('location', $location);

if (
    file_exists($xcart_dir.'/modules/gold_display.php')
    && is_readable($xcart_dir.'/modules/gold_display.php')
) {
    include $xcart_dir.'/modules/gold_display.php';
}
func_display('provider/home.tpl',$smarty);
?>
