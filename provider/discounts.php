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
 * Discount coupons management interface
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Provder interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: discounts.php,v 1.49.2.1 2011/01/10 13:12:08 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

define('NUMBER_VARS', "minprice_new,discount_new");
require './auth.php';
require $xcart_dir.'/include/security.php';

$location[] = array(func_get_langvar_by_name('lbl_discounts'), '');

/**
 * Use this condition when single mode is disabled
 */
$provider_condition = ($single_mode ? '' : "AND provider='$logged_userid'");

if ($REQUEST_METHOD == 'POST') {

    if ($mode == 'delete') {

        // Delete selected discounts

        if (is_array($posted_data)) {
            $deleted = false;
            foreach ($posted_data as $discountid=>$v) {
                if (empty($v['to_delete']))
                    continue;

                db_query("DELETE FROM $sql_tbl[discounts] WHERE discountid='$discountid' $provider_condition");
                db_query("DELETE FROM $sql_tbl[discount_memberships] WHERE discountid='$discountid'");
                $deleted = true;
            }

            if ($deleted)
                $top_message['content'] = func_get_langvar_by_name('msg_discounts_del');
        }

    } elseif ($mode == 'update') {

        // Update discounts table

        if (is_array($posted_data)) {
            foreach ($posted_data as $discountid => $v) {
                $v['minprice'] = func_convert_number($v['minprice']);
                $v['discount'] = func_convert_number($v['discount']);

                $membership_where = "$sql_tbl[discount_memberships].membershipid IS NULL";
                if (!empty($v['membershipids']) && !in_array(-1, $v['membershipids'])) {
                    $membership_where = "$sql_tbl[discount_memberships].membershipid IN ('".implode("','", $v['membershipids'])."')";
                }

                if (
                    $v['discount'] <= 0 ||
                    ($v['discount_type'] == 'percent' && $v['discount'] > 100) ||
                    func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[discounts] LEFT JOIN $sql_tbl[discount_memberships] ON $sql_tbl[discount_memberships].discountid = $sql_tbl[discounts].discountid WHERE $sql_tbl[discounts].discountid != '$discountid' AND $sql_tbl[discounts].minprice = '$v[minprice]' AND $membership_where $provider_condition") > 0
                    ) {
                    $top_message['content'] = func_get_langvar_by_name('msg_err_discounts_upd');
                    $top_message['type'] = 'E';
                    func_header_location('discounts.php');
                }

                func_array2update('discounts',
                    array(
                        'minprice' => $v['minprice'],
                        'discount' => $v['discount'],
                        'discount_type' => $v['discount_type'],
                    ),
                    "discountid='$discountid' $provider_condition"
                );
                func_membership_update('discount', $discountid, $v['membershipids']);
            }

            $top_message['content'] = func_get_langvar_by_name('msg_discounts_upd');
        }

    } elseif ($mode == 'add') {

        // Add new discount

        $is_err = true;
        if ($discount_new > 0 && ($discount_type_new != 'percent' || $discount_new <= 100)) {
            $ids = func_query_column("SELECT discountid FROM $sql_tbl[discounts] WHERE minprice = '$minprice_new' $provider_condition");

            if (!empty($ids)) {
                if (!empty($discount_membershipids_new) && !in_array(-1, $discount_membershipids_new)) {
                    $is_err = (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[discount_memberships] WHERE discountid IN ('".implode("','", $ids)."') AND membershipid IN ('".implode("','", $discount_membershipids_new)."')") > 0);
                } else {
                    $is_err = (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[discount_memberships] WHERE discountid IN ('".implode("','", $ids)."')") == 0);
                }

            } else {
                $is_err = false;
            }
        }

        if (!$is_err) {
            $_id = func_array2insert('discounts',
                array(
                    'minprice' => $minprice_new,
                    'discount' => $discount_new,
                    'discount_type' => $discount_type_new,
                    'provider' => $logged_userid
                )
            );
            if (!empty($discount_membershipids_new) && !in_array(-1, $discount_membershipids_new)) {
                foreach ($discount_membershipids_new as $v) {
                    db_query("INSERT INTO $sql_tbl[discount_memberships] VALUES ('$_id','$v')");
                }
            }

            $top_message['content'] = func_get_langvar_by_name('msg_discounts_add');
        }
        else {
            $top_message['content'] = func_get_langvar_by_name('msg_err_discounts_add');
            $top_message['type'] = 'E';
        }
    }

    func_header_location('discounts.php');
}

$discounts = func_query("SELECT * FROM $sql_tbl[discounts] WHERE 1 $provider_condition ORDER BY minprice");
if (!empty($discounts)) {
    foreach ($discounts as $k => $v) {
        $tmp = func_query_column("SELECT membershipid FROM $sql_tbl[discount_memberships] WHERE discountid = '$v[discountid]'");
        if (!empty($tmp)) {
            $discounts[$k]['membershipids'] = array();
            foreach ($tmp as $m) {
                $discounts[$k]['membershipids'][$m] = 'Y';
            }
        }
    }
}

$smarty->assign('memberships', func_get_memberships());

$smarty->assign('discounts', $discounts);
$smarty->assign('main','discounts');

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
