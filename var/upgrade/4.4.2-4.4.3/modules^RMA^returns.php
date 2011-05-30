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
 * Returns management
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: returns.php,v 1.69.2.3 2011/01/10 13:12:01 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load(
    'backoffice',
    'mail'
);

if (!$active_modules['RMA'])
    func_403(41);

x_session_register('search_data');

x_session_register('inv_err');

if (isset($returnid))
    $returnid = intval($returnid);

if (isset($search['returnid'])) {

    $search['returnid'] = intval($search['returnid']);

}

if (!empty($start_date)) {

    $search_data['returns']['start_date'] = func_prepare_search_date($start_date);
    $search_data['returns']['end_date']   = func_prepare_search_date($end_date, true);

}

if (isset($_GET['new'])) {

    $search_data['returns']['status'] = '';
    $search_data['returns']['filter'] = 'N';

    $mode = 'search';

}

if (empty($search_data['returns']['sort_field'])) {

    $search_data['returns']['sort_field']         = 'date';
    $search_data['returns']['sort_direction']     = 1;

}

if (
    $current_area == 'C'
    && (
        !empty($returnid)
        || !empty($to_delete)
    )
) {

    $returnids = func_array_merge(
        array(
            @$returnid
        ),
        array_keys(
            (array)@$to_delete
        )
    );

    if (!empty($returnids)) {

        $returnids = "'" . implode("','", $returnids) . "'";

        $found = func_query_first_cell("SELECT COUNT($sql_tbl[returns].returnid) FROM $sql_tbl[returns], $sql_tbl[order_details], $sql_tbl[orders] WHERE $sql_tbl[returns].itemid = $sql_tbl[order_details].itemid AND $sql_tbl[order_details].orderid = $sql_tbl[orders].orderid AND $sql_tbl[orders].userid = '$logged_userid' AND $sql_tbl[returns].returnid IN ($returnids)");

        if ($found == 0) {

            func_header_location('returns.php');

        }

    }

}

/**
 * Save search data
 */
if(
    $REQUEST_METHOD == 'POST'
    && $mode == 'search'
    && $search
) {
    $search_data['returns'] = array_merge($search_data['returns'], $search);

    func_header_location("returns.php?mode=search");

/**
 * Create credit
 */
} elseif(
    $mode == 'credit_create'
    && $returnid
    && $current_area != 'C'
) {

    $return = func_return_data($returnid);

    $gcid = substr(strtoupper(md5(uniqid(rand()))), 0, 16);

    $gc_amount = func_convert_number($gc_amount);

    if($gc_amount < 0) {

        $amount = price_format(($return['amount']*$return['product']['price']));

    } else {

        $amount = $gc_amount;

    }

    $insert_data = array(
        'gcid'                  => $gcid,
        'purchaser'             => func_get_langvar_by_name("lbl_return_service", false, $config['default_admin_language'], true),
        'recipient'             => $return['order']['login'],
        'send_via'              => 'E',
        'recipient_email'       => $return['order']['email'],
        'recipient_firstname'   => $return['order']['firstname'],
        'recipient_lastname'    => $return['order']['lastname'],
        'recipient_address'     => $return['order']['b_address'],
        'recipient_city'        => $return['order']['b_city'],
        'recipient_state'       => $return['order']['b_statename'],
        'recipient_country'     => $return['order']['b_countryname'],
        'recipient_zipcode'     => $return['order']['b_zipcode'],
        'recipient_phone'       => $return['order']['phone'],
        'message'               => func_get_langvar_by_name("txt_rma_credit_message", false, $config['default_admin_language'], true),
        'amount'                => $amount,
        'debit'                 => $amount,
        'status'                => 'A',
        'add_date'              => XC_TIME,
    );

    func_array2insert(
        'giftcerts',
        func_addslashes($insert_data)
    );

    $res = func_query_first("SELECT * FROM $sql_tbl[giftcerts] WHERE gcid='$gcid'");

    $mail_smarty->assign('giftcert', $res);

    $mail_smarty->assign('returnid', $returnid);

    func_send_mail(
        $res['recipient_email'],
        'mail/giftcert_return_subj.tpl',
        'mail/giftcert_return.tpl',
        $config['Company']['orders_department'],
        false
    );

    // Remove download key for returned item
    if (
        !empty($active_modules['Egoods'])
    ) {
        db_query("DELETE FROM $sql_tbl[download_keys] WHERE itemid = '$return[itemid]'");
    } 

    func_array2update(
        'returns',
        array(
            'credit' => $gcid,
        ),
        "returnid = '$returnid'"
    );

    // Save changes in the history
    if (!empty($active_modules['Advanced_Order_Management'])) {

        $order = func_query_first("SELECT $sql_tbl[order_details].orderid, $sql_tbl[orders].status FROM $sql_tbl[returns], $sql_tbl[order_details], $sql_tbl[orders] WHERE $sql_tbl[returns].itemid=$sql_tbl[order_details].itemid AND $sql_tbl[returns].returnid = '$returnid' AND $sql_tbl[order_details].orderid = $sql_tbl[orders].orderid");

        if (!empty($order)) {

            $details = array(
                'type'       => 'R',
                'old_status' => $order['status'],
                'new_status' => $order['status'],
                'credit'     => 1,
                'amount'     => $amount,
                'gcid'       => $gcid,
                'returnid'   => $returnid,
            );

            func_aom_save_history($order['orderid'], 'R', $details);

        }

    }

    $top_message['content'] = func_get_langvar_by_name('msg_rma_credit_created', array('returnid' => $returnid));

    func_header_location("returns.php?mode=search");

/**
 * Modify return
 */
} elseif (
    $REQUEST_METHOD == 'POST'
     && $mode == 'modify'
    && $returnid
    && !empty($posted_data)
) {

    $old_data = func_return_data($returnid);

    $old_status = $old_data['status'];

    if($current_area == 'C')
        func_unset($posted_data, 'status');

    if (
        $current_area != 'C'
        || $old_status == 'R'
    ) {

        $posted_data['returned_amount'] = (in_array($posted_data['status'], array('A', 'C')))
            ? min($posted_data['amount'], $posted_data['returned_amount'])
            : 0;

        if (
            $config['General']['unlimited_products'] != 'Y'
            && $posted_data['returned_amount'] != $posted_data['returned_amount_orig']
        ) {

            $amount = $posted_data['returned_amount'] - $posted_data['returned_amount_orig'];

            $is_avail = func_rma_check_inventory($returnid, $amount);

            if ($is_avail) {

                func_rma_update_inventory($returnid, $posted_data['returned_amount'] - $posted_data['returned_amount_orig']);

            } else {

                func_unset($posted_data, 'returned_amount');

                $top_message = array(
                    'type'         => 'W',
                    'content'     => func_get_langvar_by_name('msg_rma_insufficient_inventory')
                );

            }

        }

        func_unset($posted_data, 'returned_amount_orig', 'amount_orig');

        func_array2update(
            'returns',
            $posted_data,
            "returnid='$returnid'"
        );

    }

    if (
        $old_status != $posted_data['status']
        && (
            $posted_data['status'] == 'A'
            || $posted_data['status'] == 'D'
        )
    ) {
        func_rma_send($returnid);
    }

    // Save changes in the history

    if (!empty($active_modules['Advanced_Order_Management'])) {

        $diff = func_aom_prepare_diff('R', $posted_data, $old_data);

        $details = array(
            'new_status'     => $old_data['order']['status'],
            'returnid'       => $returnid,
            'diff'           => $diff,
            'status_changed' => ($old_status != $posted_data['status'])
                ? $posted_data['status']
                : '',
        );

        func_aom_save_history($old_data['order']['orderid'], 'R', $details);

    }

    $top_message['content'] = func_get_langvar_by_name('msg_rma_return_upd');

    func_header_location("returns.php?mode=modify&returnid=" . $returnid);

/**
 * Delete return(s)
 */
} elseif (
    $mode == 'delete'
    && $to_delete
    && is_array($to_delete)
) {

    // Save action to the history
    if (!empty($active_modules['Advanced_Order_Management'])) {

        $tmp = func_query_hash("SELECT $sql_tbl[returns].returnid, $sql_tbl[order_details].orderid FROM $sql_tbl[returns], $sql_tbl[order_details] WHERE $sql_tbl[returns].itemid=$sql_tbl[order_details].itemid AND $sql_tbl[returns].returnid IN ('".implode("','", array_keys($to_delete))."')", "returnid", false, true);

        if (!empty($tmp)) {

            foreach ($tmp as $returnid => $orderid) {

                $details['returnid']     = $returnid;
                $details['status']         = func_query_first_cell("SELECT status FROM $sql_tbl[orders] WHERE orderid='$orderid'");;

                if ($current_area == 'C') {

                    $details['status_change'] = 'E';

                } else {

                    $details['deleted'] = true;

                }

                func_aom_save_history($orderid, 'R', $details);

            }

        } // if (!empty($tmp))

    } // if (!empty($active_modules['Advanced_Order_Management']))

    if ($current_area == 'C') {

        db_query("UPDATE $sql_tbl[returns] SET status = 'E' WHERE returnid IN ('" . implode("','", array_keys($to_delete)) . "')");

    } else {

        db_query("DELETE FROM $sql_tbl[returns] WHERE returnid IN ('" . implode("','", array_keys($to_delete)) . "')");

    }

    $top_message['content'] = func_get_langvar_by_name('msg_rma_return_del');

    func_header_location("returns.php?mode=search");

/**
 * Update returns
 */
} elseif(
    $REQUEST_METHOD == 'POST'
    && $mode == 'update'
    && !empty($update)
    && $current_area != 'C'
) {

    $inv_err = array();

    foreach($update as $k => $v) {

        $old_data = func_return_data($k);

        $old_status = $old_data['status'];

        $v['returned_amount'] = (in_array($v['status'], array('A','C')))
            ? min($v['amount'],$v['returned_amount'])
            : 0;

        if (
            $config['General']['unlimited_products'] != 'Y'
            && $v['returned_amount'] != $v['returned_amount_orig']
        ) {

            $amount = $v['returned_amount'] - $v['returned_amount_orig'];

            $is_avail = func_rma_check_inventory($k, $amount);

            if ($is_avail) {

                func_rma_update_inventory($k, $v['returned_amount'] - $v['returned_amount_orig']);

            } else {

                func_unset($v, 'returned_amount');

                $inv_err[$k] = 1;

            }

        }

        func_unset($v, 'returned_amount_orig', 'amount_orig');

        func_array2update(
            'returns',
            $v,
            "returnid = '$k'"
        );

        if ($old_status != $v['status']) {

            if (
                $v['status'] == 'A'
                || $v['status'] == 'D'
            ) {
                func_rma_send($k);
            }

        }

        if (!empty($active_modules['Advanced_Order_Management'])) {

            $diff = func_aom_prepare_diff('R', $v, $old_data);

            $details = array(
                'new_status'     => $old_data['order']['status'],
                'returnid'       => $k,
                'diff'           => $diff,
                'status_changed' => ($old_status != $v['status'])
                    ? $v['status']
                    : false,
            );

            func_aom_save_history($old_data['order']['orderid'], 'R', $details);

        }

        $top_message['content'] = func_get_langvar_by_name('msg_rma_returns_upd')."<br />";

        if (!empty($inv_err)) {

            $top_message = array(
                'type'    => 'W',
                'content' => func_get_langvar_by_name('msg_rma_insufficient_inventory_list', array('returns_list' => implode(",",$inv_err)))
            );

        }

    }

    func_header_location("returns.php?mode=search");

/**
 * Update reasons, actions
 */
} elseif(
    in_array($mode, array('reasons', 'actions'))
    && $current_area != 'C'
    && $REQUEST_METHOD == 'POST'
) {

    $lng_prefix = substr($mode, 0, strlen($mode) - 1);

    if ($user_action == 'delete') {

        $_data = unserialize($config['rma_' . $mode]);

        foreach ($to_delete as $idx) {

            db_query("DELETE FROM $sql_tbl[languages_alt] WHERE name = 'lbl_rma_" . $lng_prefix . "_$idx'");

            unset($_data[$idx]);

        }

        func_array2insert(
            'config',
            array(
                'name'  => 'rma_' . $mode,
                'value' => addslashes(serialize($_data)),
            ),
            true
        );

        $top_message['content'] = func_get_langvar_by_name('msg_rma_' . $mode . '_del');

    } elseif ($user_action == 'update') {

        if (!empty($new)) {

            $posted_data[] = trim($new);

            $flag_new = true;

        }

        foreach($posted_data as $k => $v) {

            func_languages_alt_insert('lbl_rma_' . $lng_prefix . '_' . $k, $v, $shop_language);

        }

        func_array2insert(
            'config',
            array(
                'name'  => 'rma_' . $mode,
                'value' => addslashes(serialize($posted_data)),
            ),
            true
        );

        $top_message['content'] = func_get_langvar_by_name('msg_rma_' . $mode . '_upd');

        if ($flag_new) {

            $top_message['content'] .= "<br />" . func_get_langvar_by_name('msg_rma_' . $mode . '_add');

        }

    }

    func_header_location("returns.php?mode=$mode");

}

$reasons = func_get_rma_reasons();

if (!empty($reasons))
    $smarty->assign('reasons', $reasons);

$actions = func_get_rma_actions();

if (!empty($actions))
    $smarty->assign('actions', $actions);

if ($mode == 'modify') {

    if (!empty($to_delete))
        list($returnid,) = each($to_delete);

    if ($returnid) {

        $location[1][1] = 'returns.php';

        $location[] = array(
            func_get_langvar_by_name('lbl_return_n', array('id' => $returnid)),
            '',
        );

        $smarty->assign('returnid', $returnid);
        $smarty->assign('return',   func_return_data($returnid));

    } else {

        func_header_location('returns.php');

    }

} elseif (
    $mode == 'print'
    && $returnid
) {

    $smarty->assign('returnid', $returnid);
    $smarty->assign('return',   func_return_data($returnid));

    func_display('modules/RMA/return_slip.tpl', $smarty);

    exit;

} elseif (
    $REQUEST_METHOD == 'GET'
    && $mode == 'search'
    && !empty($search_data['returns'])
) {

    if (
        !empty($sort)
        && in_array(
            $sort,
            array(
                'date',
                'returnid',
                'status',
            )
        )
    ) {
        $search_data['returns']['sort_field'] = $sort;

        if (isset($sort_direction)) {

            $search_data['returns']['sort_direction'] = intval($sort_direction);

        } elseif (!isset($search_data['returns']['sort_direction'])) {

            $search_data['returns']['sort_direction'] = 1;

        }

    }

    // Search returns
    $where = array();

    if (!empty($search_data['returns']['start_date'])) {
        $where[] = "$sql_tbl[returns].date > '"
            . $search_data['returns']['start_date']
            . "' AND $sql_tbl[returns].date < '"
            . $search_data['returns']['end_date']
            . "'";
    }

    if (!empty($search_data['returns']['status'])) {

        $where[] = "$sql_tbl[returns].status = '"
            . $search_data['returns']['status']
            . "'";

    } elseif (
        $current_area != 'C'
        && !empty($search_data['returns']['filter'])
    ) {

        switch ($search_data['returns']['filter']) {

            case 'N':
                $where[] = "$sql_tbl[returns].status = 'R'";
                break;

            case 'A':
                $where[] = "$sql_tbl[returns].status NOT IN ('E','D')";
                break;

        }

    }

    if (!empty($search_data['returns']['returnid']))
        $where[] = "$sql_tbl[returns].returnid = '{$search_data['returns']['returnid']}'";

    if (!empty($search_data['returns']['itemid']))
        $where[] = "$sql_tbl[returns].itemid = '{$search_data['returns']['itemid']}'";

    if (!empty($search_data['returns']['orderid']))
        $where[] = "$sql_tbl[orders].orderid  = '{$search_data['returns']['orderid']}'";

    if ($current_area == 'C')
        $where[] = "$sql_tbl[returns].status <> 'E' AND $sql_tbl[orders].userid = '$logged_userid'";

    $where = (!empty($where))
        ? " AND " . implode(" AND ", $where)
        : '';

    $sort_field = $search_data['returns']['sort_field'];

    $sort_string = " ORDER BY $sql_tbl[returns].$sort_field ".($search_data['returns']['sort_direction'] ? 'DESC' : 'ASC');

    $returns = func_query("SELECT $sql_tbl[returns].*, IFNULL($sql_tbl[products].product, 'PRODUCT (deleted from database)') as product, IFNULL($sql_tbl[products].productid, 0) as productid, $sql_tbl[orders].orderid, $sql_tbl[orders].firstname, $sql_tbl[orders].lastname, $sql_tbl[orders].userid, $sql_tbl[orders].date as order_date, $sql_tbl[order_details].amount as order_amount, $sql_tbl[order_details].price, $sql_tbl[order_details].product_options FROM $sql_tbl[returns], $sql_tbl[orders], $sql_tbl[order_details] LEFT JOIN $sql_tbl[products] ON $sql_tbl[order_details].productid = $sql_tbl[products].productid WHERE $sql_tbl[returns].itemid = $sql_tbl[order_details].itemid AND $sql_tbl[orders].orderid = $sql_tbl[order_details].orderid".$where.$sort_string);

    $smarty->assign('returns_count', 0);

    if (!empty($returns)) {

        $smarty->assign('returns',       $returns);
        $smarty->assign('returns_count', @count($returns));

    } elseif (empty($top_message['content'])) {

        $no_results_warning = array(
            'type'    => 'W',
            'content' => func_get_langvar_by_name("lbl_warning_no_search_results", false, false, true)
        );

        $smarty->assign('top_message', $no_results_warning);

    }

    $mode = 'search';
}

if (
    $mode == 'reasons'
    || $mode == 'actions'
) {
    define('IS_MULTILANGUAGE', 1);
}

if (empty($search_data['returns'])) {

    $tmp = func_query_first("SELECT MIN(date) as min, MAX(date) as max FROM $sql_tbl[returns]");

    if (!empty($tmp['min']))
        $search_data['returns']['start_date'] = $tmp['min'];

    if (!empty($tmp['max']))
        $search_data['returns']['end_date'] = $tmp['max'];
}

if (!empty($inv_err)) {

    $smarty->assign('inv_err',        $inv_err);

    x_session_unregister('inv_err');

}

$smarty->assign('search_prefilled', $search_data['returns']);
$smarty->assign('mode',             $mode);
?>
