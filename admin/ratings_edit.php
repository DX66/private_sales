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
 * Product ratings edit facility
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: ratings_edit.php,v 1.35.2.1 2011/01/10 13:11:47 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './auth.php';
require $xcart_dir . '/include/security.php';

/**
 * Ratings per page;
 */
$objects_per_page = 25;

$location[] = array(func_get_langvar_by_name('lbl_edit_ratings'), '');

if ($REQUEST_METHOD == 'POST') {
/**
 * Process the POST request
 */
    if ($mode == 'delete') {

    // Delete ratings

        if ($to_delete) {

            $deleted = false;

            foreach ($to_delete as $key => $value) {

                db_query ("DELETE FROM $sql_tbl[product_votes] WHERE vote_id='$key'");

                $deleted = true;

            }

            if ($deleted)
                $top_message['content'] = func_get_langvar_by_name('msg_adm_ratings_del');

        } else {
            $top_message['content'] = func_get_langvar_by_name('msg_adm_warn_ratings_sel');
            $top_message['type'] = 'W';
        }
    }

    if ($mode == 'update') {

    // Update ratings

        if ($update_votes) {

            $updated = false;

            foreach ($update_votes as $key => $value) {
                db_query ("UPDATE $sql_tbl[product_votes] SET vote_value='$value' WHERE vote_id='$key'");
                $updated = true;
            }

            if ($updated)
                $top_message['content'] = func_get_langvar_by_name('msg_adm_ratings_upd');

        }

    }

    func_header_location("ratings_edit.php?sortby=$sortby&sortorder=$orderby&productid=$productid&ip=" . urlencode($ip) . "&page=$page");

} // /if ($REQUEST_METHOD == 'POST')

// sortorder & sortby
if ($sortorder != 0) {

    $sortorder = 1;

    $_sortorder = " DESC ";

} else {

    $sortorder = 0;

    $_sortorder = " ASC ";

}

if ($sortby == 'productcode')
    $_sortby = " $sql_tbl[products].productcode ";
elseif ($sortby == 'product')
    $_sortby = " $sql_tbl[products].product ";
elseif ($sortby == 'ip')
    $_sortby = " $sql_tbl[product_votes].remote_ip ";
elseif ($sortby == 'vote')
    $_sortby = " $sql_tbl[product_votes].vote_value ";
else {
    $sortby = 'productid';
    $_sortby = " $sql_tbl[product_votes].productid ";
}

if ($productid) {

    $condition = " AND $sql_tbl[product_votes].productid='$productid' ";

    $smarty->assign ('product', func_query_first ("SELECT product FROM $sql_tbl[products] WHERE productid='$productid'"));

} elseif ($ip) {

    $condition = " AND $sql_tbl[product_votes].remote_ip='$ip' ";

} else {

    $condition = '';

}

$total_items = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[product_votes] LEFT JOIN $sql_tbl[products] ON $sql_tbl[product_votes].productid=$sql_tbl[products].productid WHERE $sql_tbl[products].productid IS NOT NULL $condition");

require $xcart_dir.'/include/navigation.php';

$stars = func_get_vote_stars();

$ratings = func_query ("SELECT $sql_tbl[product_votes].*, $sql_tbl[products].* FROM $sql_tbl[product_votes], $sql_tbl[products] WHERE $sql_tbl[product_votes].productid=$sql_tbl[products].productid $condition ORDER BY $_sortby $_sortorder LIMIT $first_page, $objects_per_page");

if ($ratings) {
    foreach ($ratings as $k => $v) {
        $ratings[$k]['level'] = round($v['vote_value'] / $stars['cost']);
        $ratings[$k]['index'] = $ratings[$k]['level'] - 1;
    }
}

$smarty->assign('navigation_script', "ratings_edit.php?sortby=$sortby&orderby=$orderby&productid=$productid&ip=$ip");

$smarty->assign ('ratings',      $ratings);
$smarty->assign ('sortby',       $sortby);
$smarty->assign ('sortorder',    $sortorder);
$smarty->assign ('invsortorder', !$sortorder);
$smarty->assign ('productid',    $productid);
$smarty->assign ('ip',           $ip);

$smarty->assign ('stars', $stars);
$smarty->assign ('main',  'ratings_edit');

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
