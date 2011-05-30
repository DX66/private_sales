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
 * Add return request in customer area
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: add_returns.php,v 1.34.2.1 2011/01/10 13:12:01 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

x_load(
    'mail',
    'user'
);

if (
    $mode == 'add_returns'
    && is_array($returns)
    && !empty($returns)
    && $orderid
) {

    $send = array();

    foreach ($returns as $k => $v) {

        if ($v['avail'] != 'Y')
            continue;

        $insert_data = array(
            'itemid'     =>     $k,
            'amount'     =>     $v['amount'],
            'reason'    =>    $return_reason,
            'action'    =>     $return_action,
            'comment'    =>     $return_comment,
            'date'        =>    XC_TIME,
            'creator'    =>    $current_area,
        );

        $returnid = func_array2insert('returns', $insert_data);

        $return = func_return_data(db_insert_id());

        if ($return !== false)
            $send[] = $return;
    }

    if (
        $send
        && $current_area == 'C'
    ) {
        $mail_smarty->assign('returns', $send);

        $userinfo = func_userinfo($logged_userid, $login_type);

        $mail_smarty->assign('userinfo', $userinfo);

        if ($config['RMA']['eml_rma_request_created'] == 'Y') {

            func_send_mail(
                $config['Company']['orders_department'],
                'mail/rma_request_created_subj.tpl',
                'mail/rma_request_created.tpl',
                $userinfo['email'],
                false
            );

        }

    }

    // Save action to the history
    if (
        $send
        && !empty($active_modules['Advanced_Order_Management'])
    ) {

        $status = func_query_first_cell("SELECT status FROM $sql_tbl[orders] WHERE orderid='$orderid'");

        $details = array(
            'added'         => true,
            'new_status'     => $status,
            'returnid'         => $returnid,
        );

        func_aom_save_history($orderid, 'R', $details);

    }

    if ($send) {

        $prefix = ($current_area == 'C')
            ? 'cust'
            : 'adm';

        $top_message['content'] = func_get_langvar_by_name('txt_rma_add_message_' . $prefix);

    } else {

        $top_message = array(
            'content'     => func_get_langvar_by_name('txt_rma_no_items_selected'),
            'type'         => 'E',
        );

    }

    func_header_location("order.php?orderid=" . $orderid);

}

$return_products = array();

if (
    is_array($order_data['products'])
    && !empty($order_data['products'])
) {

    foreach ($order_data['products'] as $k => $v) {

        $v['amount'] -= (int)func_query_first_cell("SELECT SUM(amount) FROM $sql_tbl[returns] WHERE itemid = '$v[itemid]' AND status <> 'E'");

        if(
            $v['amount'] > 0
            && (
                (
                    ($order_data['order']['date'] + $v['return_time'] * 86400) > XC_TIME
                    && $v['return_time'] > 0
                )
                || $current_area != 'C'
            )
        ) {
            $return_products[] = $v;
        }

    }

    if (!empty($return_products)) {

        $smarty->assign('return_products', $return_products);

    }

}

$rma_disable_form = (
    in_array($order_data['order']['status'], array('F', 'D'))
    || (
        $current_area == 'C'
        && $config['RMA']['rma_allow_for_completed_orders'] != 'Y'
        && $order_data['order']['status'] == 'C'
    )
);

$smarty->assign('rma_disable_form', $rma_disable_form);

$smarty->assign('reasons',             func_get_rma_reasons());
$smarty->assign('actions',             func_get_rma_actions());
?>
