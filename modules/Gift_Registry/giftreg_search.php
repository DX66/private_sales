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
 * Search for events
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: giftreg_search.php,v 1.31.2.1 2011/01/10 13:11:57 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

x_session_register('search_data');

$location[] = array(func_get_langvar_by_name('lbl_giftreg_search'), '');

if ($REQUEST_METHOD == 'POST') {
/**
 * Update the search_data
 */
    if (is_array($post_data)) {
        $search_data = $post_data;

        $search_data['start_date'] = func_prepare_search_date($start_date);
        $search_data['end_date']   = func_prepare_search_date($end_date, true);

    }
    func_header_location("giftregs.php?mode=search");
}

// Generate start event date if it's empty
if (empty($search_data['start_date'])) {
    $search_data['start_date'] = func_prepare_search_date();
}

// Generate end event date if it's empty
if (empty($search_data['end_date']))
    $search_data['end_date'] = mktime(0,0,0,date('m',$search_data['start_date']),date('d',$search_data['start_date']),date('Y',$search_data['start_date'])+1);

if ($mode == 'search') {
/**
 * Search for Gift Registries
 */
    $query_condition = '1';
    // Search by creator's name
    if (!empty($search_data['name']))
        $query_condition .= " AND ($sql_tbl[customers].firstname LIKE '%$search_data[name]%' OR $sql_tbl[customers].lastname LIKE '%$search_data[name]%' OR CONCAT($sql_tbl[customers].firstname, ' ', $sql_tbl[customers].lastname) LIKE '%$search_data[name]%')";

    // Search by creator's email
    if (!empty($search_data['email']))
        $query_condition .= " AND $sql_tbl[customers].email='$search_data[email]'";

    // Search by substring...
    if (!empty($search_data['substring'])) {
        $substring_condition = "$sql_tbl[giftreg_events].title LIKE '%$search_data[substring]%'";
        // ...including search in description
        if ($search_data['inc_description'] == 'Y') {
            $substring_condition = "($substring_condition OR $sql_tbl[giftreg_events].description LIKE '%$search_data[substring]%')";
        }
        $query_condition .= " AND $substring_condition";
    }

    // Search by status
    if (!empty($search_data['status']))
        $query_condition .= " AND $sql_tbl[giftreg_events].status='$search_data[status]'";
    // else by all statuses
    else
        $query_condition .= " AND ($sql_tbl[giftreg_events].status='P' OR $sql_tbl[giftreg_events].status='G')";

    // Search events from start date through end date
    $query_condition .= " AND $sql_tbl[giftreg_events].event_date>='$search_data[start_date]' AND $sql_tbl[giftreg_events].event_date<='$search_data[end_date]'";

    $total_items = intval(func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[giftreg_events], $sql_tbl[customers] WHERE $query_condition AND $sql_tbl[customers].id=$sql_tbl[giftreg_events].userid"));

    // Navigation code

    $objects_per_page = $config['Gift_Registry']['events_per_page'];

    require $xcart_dir.'/include/navigation.php';

    // Run query
    $result = func_query("SELECT $sql_tbl[giftreg_events].*, $sql_tbl[customers].firstname, $sql_tbl[customers].lastname FROM $sql_tbl[giftreg_events], $sql_tbl[customers] WHERE $query_condition AND $sql_tbl[customers].id=$sql_tbl[giftreg_events].userid ORDER BY $sql_tbl[giftreg_events].event_date LIMIT $first_page,$objects_per_page");

    $smarty->assign('navigation_script',"giftregs.php?mode=search");

    // Get the product counts for each gift registry
    if (is_array($result)) {
        foreach($result as $k=>$v) {
            $result[$k]['products'] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[wishlist] WHERE event_id='$v[event_id]'");
        }
    }

    $smarty->assign('search_result', $result);
    $smarty->assign('items_count', (is_array($result) ? count($result) : 0));

    if ($total_items == 0 && empty($top_message['content'])) {
        $no_results_warning = array('type' => 'W', 'content' => func_get_langvar_by_name("lbl_warning_no_search_results", false, false, true));
        $smarty->assign('top_message', $no_results_warning);
    }
}

$smarty->assign('search_data', func_stripslashes($search_data));

x_session_save();

$smarty->assign('main','giftreg');
?>
