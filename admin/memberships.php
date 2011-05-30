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
 * Memberships management interface
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: memberships.php,v 1.46.2.1 2011/01/10 13:11:46 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

define('IS_MULTILANGUAGE', 1);

require './auth.php';
require $xcart_dir.'/include/security.php';

$location[] = array(func_get_langvar_by_name('lbl_edit_membership_levels'), '');

function get_membership_name_errors($value, $memberships = null, $membershipid = -1)
{
    global $sql_tbl, $shop_language;

    $m_errors = array();
    if (strlen($value['membership']) <= 0) {
        // don't save empty membership names
        $m_errors[] = func_get_langvar_by_name('msg_err_empty_membership').'.';
    } else {

        if (is_null($memberships)) {
            $tmp = func_query("SELECT $sql_tbl[memberships].*, COUNT($sql_tbl[customers].id) as users, IFNULL($sql_tbl[memberships_lng].membership, $sql_tbl[memberships].membership) as membership FROM $sql_tbl[memberships] LEFT JOIN $sql_tbl[customers] ON $sql_tbl[customers].membershipid = $sql_tbl[memberships].membershipid LEFT JOIN $sql_tbl[memberships_lng] ON $sql_tbl[memberships].membershipid = $sql_tbl[memberships_lng].membershipid AND $sql_tbl[memberships_lng].code = '$shop_language' GROUP BY $sql_tbl[memberships].membershipid ORDER BY IF(FIELD($sql_tbl[memberships].area, 'A','P','C','B') > 0, FIELD($sql_tbl[memberships].area, 'A','P','C','B'), 100), $sql_tbl[memberships].orderby");
            if (!empty($tmp)) {
                foreach ($tmp as $v) {
                    if ($v['area'] == $value['area']) {
                        $v['membership'] = addslashes($v['membership']); // add slashes to unify memberships from POST and from DB
                        $memberships[$v['membershipid']] = $v;
                    }
                }
            }
        }

        // don't save dublicate membership names
        $dublicate = array();
        foreach ((array)$memberships as $idx => $vx) {
            if ($idx == $membershipid) continue;
            if (($vx['membership'] == $value['membership'])) {
                $dublicate[] = stripslashes($value['membership']); // strip slashes for output
                break;
            }
        }
        if (!empty($dublicate)) {
            $m_errors[] = func_get_langvar_by_name('msg_err_duplicate_memberships').": <b>".(implode("</b>, <b>", array_unique($dublicate)))."</b>.";
        }
    }
    return $m_errors;
}

$recalc_subcat_count = false;
// Update memberships
if ($mode == 'update' && !empty($posted_data)) {
    $m_errors = array();
    foreach ($posted_data as $id => $v) {
        $err = get_membership_name_errors($v, $posted_data, $id);
        if (!empty($err)) {
            $m_errors = func_array_merge($m_errors, $err);
            unset($v['membership']);
        }

        $membership = $v['membership'];
        if ($shop_language != $config['default_admin_language'])
            unset($v['membership']);
        $v['active'] = $v['active'];
        func_array2update('memberships', $v, "membershipid = '$id'");
        if (strlen($membership) > 0) {
            db_query("REPLACE INTO $sql_tbl[memberships_lng] VALUES ('$id','$shop_language','$membership')");
        }
    }

    if (!empty($m_errors)) {
        $top_message['content'] = implode("<br>", array_unique($m_errors));
        $top_message['type'] = 'E';
    }

// Add membership
} elseif ($mode == 'add') {

    $m_errors = get_membership_name_errors($add);
    if (!empty($m_errors)) {
        $top_message['content'] = implode("<br>", array_unique($m_errors));
        $top_message['type'] = 'E';
    } else {
        if (empty($add['orderby']))
            $add['orderby'] = func_query_first_cell("SELECT MAX(orderby) FROM $sql_tbl[memberships] WHERE area = '$add[area]'")+1;
        $add['active'] = $add['active'];
        $id = func_array2insert('memberships', $add);
        db_query("INSERT INTO $sql_tbl[memberships_lng] VALUES ('$id','$shop_language','$add[membership]')");
        if ($add['area'] == 'C')
            $recalc_subcat_count = true;
    }

// Delete memerbship(s)
} elseif ($mode == 'delete' && !empty($to_delete)) {
    $delete_string = "membershipid IN ('".implode("','", $to_delete)."')";
    if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[memberships] WHERE area = 'C' AND ".$delete_string))
        $recalc_subcat_count = true;
    db_query("DELETE FROM $sql_tbl[memberships] WHERE ".$delete_string);
    db_query("DELETE FROM $sql_tbl[category_memberships] WHERE ".$delete_string);
    db_query("DELETE FROM $sql_tbl[product_memberships] WHERE ".$delete_string);
    db_query("DELETE FROM $sql_tbl[tax_rate_memberships] WHERE ".$delete_string);
    db_query("DELETE FROM $sql_tbl[memberships_lng] WHERE ".$delete_string);
    db_query("DELETE FROM $sql_tbl[pmethod_memberships] WHERE ".$delete_string);
    db_query("DELETE FROM $sql_tbl[pricing] WHERE ".$delete_string);
    db_query("DELETE FROM $sql_tbl[quick_prices] WHERE ".$delete_string);

    func_array2update('customers', array('membershipid' => 0), $delete_string);
    func_array2update('customers', array('pending_membershipid' => 0), 'pending_'.$delete_string);
}

if (!empty($mode)) {
    if ($recalc_subcat_count) {
        x_load('category');
        func_recalc_subcat_count(func_query_column("SELECT categoryid FROM $sql_tbl[categories]"), 10);
    }
    func_header_location('memberships.php');
}

$memberships = array();
if (empty($active_modules['Simple_Mode'])) {
    $memberships['A'] = array();
}
$memberships['P'] = array();
$memberships['C'] = array();
if (!empty($active_modules['XAffiliate'])) {
    $memberships['B'] = array();
}

$tmp = func_query("SELECT $sql_tbl[memberships].*, COUNT($sql_tbl[customers].id) as users, IFNULL($sql_tbl[memberships_lng].membership, $sql_tbl[memberships].membership) as membership FROM $sql_tbl[memberships] LEFT JOIN $sql_tbl[customers] ON $sql_tbl[customers].membershipid = $sql_tbl[memberships].membershipid LEFT JOIN $sql_tbl[memberships_lng] ON $sql_tbl[memberships].membershipid = $sql_tbl[memberships_lng].membershipid AND $sql_tbl[memberships_lng].code = '$shop_language' GROUP BY $sql_tbl[memberships].membershipid ORDER BY IF(FIELD($sql_tbl[memberships].area, 'A','P','C','B') > 0, FIELD($sql_tbl[memberships].area, 'A','P','C','B'), 100), $sql_tbl[memberships].orderby");
if (!empty($tmp)) {
    foreach ($tmp as $v) {
        $memberships[$v['area']][] = $v;
    }
}
if (!empty($active_modules['Simple_Mode']) && isset($memberships['A'])) {
    unset($memberships['A']);
}

$memberships_lbls = array();
foreach ($memberships as $k => $v) {
    $type = ($k == 'P' && !empty($active_modules['Simple_Mode'])) ? "A" : $k;
    $memberships_lbls[$k] = func_get_langvar_by_name('lbl_'.$type.'_membership_levels');
}

foreach ($memberships_lbls as $anchor=>$anchor_label)
    $dialog_tools_data['left'][] = array('link' => "#mem_lvl_".$anchor, 'title' => $anchor_label);
$smarty->assign('dialog_tools_data', $dialog_tools_data);

$smarty->assign('memberships', $memberships);
$smarty->assign('memberships_lbls', $memberships_lbls);

$smarty->assign('main','memberships');

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
