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
 * Users search processor
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: search_users.php,v 1.28.2.3 2011/04/27 10:37:09 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

$where = array();
$inner_joins = array();

if (!empty($data['usertype'])) {
    // Search by usertype...
    $where[] = "usertype='".$data["usertype"]."'";
}

if (!empty($data['membershipid'])) {
    // Search by membershipid...
    if (preg_match('/pending_membership/i', $data['membershipid']))
        $where[] = "$sql_tbl[customers].membershipid != $sql_tbl[customers].pending_membershipid AND $sql_tbl[customers].pending_membershipid <> '0'";
    else
        $where[] = "$sql_tbl[customers].membershipid = '".$data["membershipid"]."' ";
}

if (!empty($data['substring'])) {

    // Search for substring in some fields...

    $condition = array();

    if (!empty($data['by_username']))
        $condition[] = "$sql_tbl[customers].login LIKE '%".$data["substring"]."%'";

    if (!empty($data['by_firstname']))
        $condition[] = "$sql_tbl[customers].firstname LIKE '%".$data["substring"]."%'";

    if (!empty($data['by_lastname']))
        $condition[] = "$sql_tbl[customers].lastname LIKE '%".$data["substring"]."%'";

    if (preg_match("/^(.+)(\s+)(.+)$/", $data['substring'], $found) && !empty($data['by_firstname']) && !empty($data['by_lastname']))
        $condition[] = "$sql_tbl[customers].firstname LIKE '%".$found[1]."%' AND $sql_tbl[customers].lastname LIKE '%".$found[3]."%'";

    if (!empty($data['by_email']))
        $condition[] = "$sql_tbl[customers].email LIKE '%".$data["substring"]."%'";

    if (!empty($data['by_company']))
        $condition[] = "$sql_tbl[customers].company LIKE '%".$data["substring"]."%'";

    if (!empty($condition))
        $where[] = "(".implode(" OR ", $condition).")";
}


if (!empty($data['url'])) {
    // Search by web site url...
    $where[] = "$sql_tbl[customers].url LIKE '%".$data["url"]."%'";
}

$address_condition = array();
if (!empty($data['address_type'])) {

    // Search by address...

    if (!empty($data['city']))
        $address_condition[] = "$sql_tbl[address_book].city LIKE '%".$data["city"]."%'";

    if (!empty($data['state']))
        $address_condition[] = "$sql_tbl[address_book].state='".$data["state"]."'";

    if (!empty($data['country']))
        $address_condition[] = "$sql_tbl[address_book].country='".$data["country"]."'";

    if (!empty($data['zipcode'])) {
        if ($config['General']['zip4_support'] == 'Y') {
            $alt_zipcode = preg_replace("/[- ]/", '', $data['zipcode']);
            $address_condition[] = "(CONCAT($sql_tbl[address_book].zipcode, $sql_tbl[address_book].zip4) LIKE '%".$data["zipcode"]."%' OR CONCAT($sql_tbl[address_book].zipcode, $sql_tbl[address_book].zip4) LIKE '%$alt_zipcode%')";
        } else {
            $address_condition[] = "$sql_tbl[address_book].zipcode LIKE '%".$data["zipcode"]."%'";
        }
    }        

    if (!empty($data['phone'])) {
        $alt_phone = preg_replace("/[- ]/", '', $data['phone']);
        $address_condition[] = "($sql_tbl[address_book].phone LIKE '%".$data["phone"]."%' OR $sql_tbl[address_book].phone LIKE '%$alt_phone%' OR $sql_tbl[address_book].fax LIKE '%".$data["phone"]."%' OR $sql_tbl[address_book].fax LIKE '%$alt_phone%')";
    }

    if (!empty($address_condition)) {
        $address_condition = implode(" AND ", $address_condition);
        $_where = '';

        switch ($data['address_type']) {
            case 'B':
                $_where = ' AND ' . $address_condition . " AND $sql_tbl[address_book].default_b='Y'";
                break;
            case 'S':
                $_where = ' AND ' . $address_condition . " AND $sql_tbl[address_book].default_s='Y'";
                break;
            case 'All':
                $_where = ' AND ' . $address_condition;
                break;
        }

        $inner_joins['address_book'] = array(
            'on' => "$sql_tbl[address_book].userid = $sql_tbl[customers].id $_where",
        );
    }

}

/**
 * Search by first or/and last login date condition
 */
$compare_date_fields = array();
if (!empty($data['registration_date']))
    $compare_date_fields[] = 'first_login';

if (!empty($data['last_login_date']))
    $compare_date_fields[] = 'last_login';

if (!empty($data['suspended_by_admin']) && !empty($data['auto_suspended'])) {

    $where[] = "$sql_tbl[customers].status = 'N'";

    $compare_date_fields[] = 'suspend_date';

} elseif (!empty($data['auto_suspended'])) {

    $where[] = "$sql_tbl[customers].status = 'N'";
    $where[] = "$sql_tbl[customers].autolock = 'Y'";

    $compare_date_fields[] = 'suspend_date';

} elseif (!empty($data['suspended_by_admin'])) {

    $where[] = "$sql_tbl[customers].status = 'N'";
    $where[] = "$sql_tbl[customers].autolock != 'Y'";

    $compare_date_fields[] = 'suspend_date';

}

if (!empty($compare_date_fields)) {
    $end_date = XC_TIME;

    // ...dates within specified period
    if ($data['date_period'] == 'C') {
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];
    }
    // ...dates within this month
    else {
        if ($data['date_period'] == 'M')
            $start_date = mktime(0,0,0,date('n',$end_date),1,date('Y',$end_date));
        elseif ($data['date_period'] == 'D')
            $start_date = func_prepare_search_date($end_date);
        elseif ($data['date_period'] == 'W') {
            $first_weekday = $end_date - (date('w',$end_date) * 86400);
            $start_date = func_prepare_search_date($first_weekday);
        }

    }
    foreach ($compare_date_fields as $k=>$v) {
        $where[] = "$sql_tbl[customers].$v >= '$start_date'";
        $where[] = "$sql_tbl[customers].$v <= '$end_date'";
    }
}

if (!empty($active_modules['Simple_Mode'])) {
    $where[] = "$sql_tbl[customers].usertype != 'A'";
}

$sort_string = '';
if (!empty($data['sort_field'])) {

    // Sort the search results...

    $direction = ($data['sort_direction'] ? 'DESC' : 'ASC');
    switch ($data['sort_field']) {
        case 'username':
            $sort_string = " ORDER BY $sql_tbl[customers].login $direction";
            break;

        case 'name':
            $sort_string = " ORDER BY $sql_tbl[customers].lastname $direction, $sql_tbl[customers].firstname $direction";
            break;

        case 'last_login':
            $sort_string = " ORDER BY last_login $direction, $sql_tbl[customers].login";
            break;

        case 'usertype':
            $sort_string = " ORDER BY usertype_cost $direction, $sql_tbl[customers].login";
            break;

        case 'cnt':
            $sort_string = " ORDER BY cnt $direction, $sql_tbl[customers].login";
            break;

        case 'email':
            $sort_string = " ORDER BY email $direction, $sql_tbl[customers].lastname $direction, $sql_tbl[customers].firstname $direction";
    }
}

$having = array();

if (!empty($data['orders_min'])) {
    $having[] = "cnt >= '".intval($data["orders_min"])."'";
}

if (!empty($data['orders_max'])) {
    $having[] = "cnt <= '".intval($data["orders_max"])."'";
}

foreach ($inner_joins as $ijname => $ij) {
    $inner_joins[$ijname]['is_inner'] = true;
}

$inner_join_condition = func_generate_joins($inner_joins);

$left_join_condition = " LEFT JOIN $sql_tbl[orders] ON $sql_tbl[customers].id=$sql_tbl[orders].userid ";

$search_condition = empty($where) ? '' : (" WHERE ".implode(" AND ", $where));

$group_condition = " GROUP BY $sql_tbl[customers].id" . (empty($having) ? '' : (" HAVING ".implode(" AND ", $having)));

/**
 * Calculate the number of rows in the search results
 */
$_total_items = db_query("SELECT COUNT($sql_tbl[customers].id), COUNT(DISTINCT $sql_tbl[orders].orderid) as cnt FROM $sql_tbl[customers]" . $inner_join_condition . $left_join_condition . $search_condition . $group_condition);

$total_items = db_num_rows($_total_items);
db_free_result($_total_items);

if ($total_items == 0)
    return false;

// Export all found users
if (!empty($data['_get_sql_query'])) {
    $sql_query = "SELECT $sql_tbl[customers].id, COUNT(DISTINCT $sql_tbl[orders].orderid) as cnt FROM $sql_tbl[customers] " . $inner_join_condition .  $left_join_condition . $search_condition . $group_condition;
    return true;
}

if (!empty($data['_objects_per_page'])) {

    // Prepare the page navigation

    $page = isset($data['page']) ? $data['page'] : 1;

    $objects_per_page = $data['_objects_per_page'];

    include $xcart_dir.'/include/navigation.php';

    $sort_string .= " LIMIT $first_page, $objects_per_page";
}

/**
 * Perform the SQL query and getting the search results
 */
$users = func_query("SELECT $sql_tbl[customers].*, COUNT(DISTINCT $sql_tbl[orders].orderid) as cnt, IFNULL($sql_tbl[memberships_lng].membership, $sql_tbl[memberships].membership) as membership, CASE WHEN $sql_tbl[customers].usertype = 'A' THEN 10 WHEN $sql_tbl[customers].usertype = 'P' THEN 5 WHEN $sql_tbl[customers].usertype = 'B' THEN 2 WHEN $sql_tbl[customers].usertype = 'C' THEN 1 ELSE 0 END AS usertype_cost FROM $sql_tbl[customers] $inner_join_condition LEFT JOIN $sql_tbl[memberships] ON $sql_tbl[customers].membershipid = $sql_tbl[memberships].membershipid LEFT JOIN $sql_tbl[memberships_lng] ON $sql_tbl[memberships].membershipid = $sql_tbl[memberships_lng].membershipid AND $sql_tbl[memberships_lng].code = '$shop_language'" . $left_join_condition . $search_condition . $group_condition . $sort_string);

if (is_array($users)) {

    // Correct the search results...

    foreach($users as $k => $v) {
        if (!empty($v['last_login']))
            $users[$k]['last_login'] += $config['Appearance']['timezone_offset'];

        if (!empty($users[$k]['first_login']))
            $users[$k]['first_login'] += $config['Appearance']['timezone_offset'];

        if ($v['usertype'] == 'P' && !$single_mode)
            $users[$k]['products'] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products] WHERE provider='" . $v['id'] . "'");

        if (!empty($active_modules['XAffiliate']) && $v["usertype"] == "B")
            $users[$k]['plan_id'] = func_query_first_cell("SELECT plan_id FROM $sql_tbl[partner_commissions] WHERE userid = '" . $v['id'] . "'");
    }
}

?>
