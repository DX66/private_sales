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
 * Wholesale prices management
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: product_wholesale.php,v 1.46.2.1 2011/01/10 13:12:04 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

x_load('product');

if ($REQUEST_METHOD == 'POST') {

    $pids = array();

    if (
        $mode == 'wholesales_modify'
        && !empty($product_info)
    ) {

        $flag = false;

        // Update wholesale prices
        $pids[] = $productid;

        if ($wprices) {

            $flag = true;

            foreach ($wprices as $priceid => $v) {

                $v['price'] = func_convert_number($v['price']);

                $old_data = func_addslashes(func_query_first("SELECT * FROM $sql_tbl[pricing] WHERE priceid='$priceid'"));

                db_query("DELETE FROM $sql_tbl[pricing] WHERE productid = '$productid' AND membershipid = '$old_data[membershipid]' AND quantity = '$old_data[quantity]' AND variantid = '0'");

                $v['quantity'] = $old_data['quantity'];
                $v['priceid'] = $priceid;
                $v['productid'] = $productid;

                func_array2insert('pricing', $v, true);

                if (
                    $geid
                    && $fields['w_price'][$priceid] == 'Y'
                ) {

                    unset($v['priceid']);

                    while($pid = func_ge_each($geid, 1, $productid)) {

                        db_query("DELETE FROM $sql_tbl[pricing] WHERE productid = '$pid' AND membershipid = '$old_data[membershipid]' AND quantity = '$old_data[quantity]' AND variantid = '0'");

                        $v['productid'] = $pid;

                        $pids[] = $pid;

                        func_array2insert('pricing', $v);

                    }

                }

            } // foreach ($wprices as $priceid => $v)

        } // if ($wprices)

        // Add new wholesale price
        if (
            !empty($newquantity)
            && (
                $newquantity > 1
                || $membershipid > 0
            )
         ) {

            $newprice = func_convert_number($newprice);

            $query_data = array(
                'productid'     => $productid,
                'quantity'         => $newquantity,
                'price'         => abs($newprice),
                'membershipid'     => $membershipid,
            );

            db_query("DELETE FROM $sql_tbl[pricing] WHERE productid = '$productid' AND membershipid = '$membershipid' AND quantity = '$newquantity' AND variantid = '0'");

            func_array2insert('pricing', $query_data);

            $flag = true;

            if(
                $fields['new_w_price']
                && $geid
            ) {

                while($pid = func_ge_each($geid, 1, $productid)) {

                    db_query("DELETE FROM $sql_tbl[pricing] WHERE productid = '$pid' AND membershipid = '$membershipid' AND quantity = '$newquantity' AND variantid = '0'");

                    $query_data['productid'] = $pid;

                    func_array2insert('pricing', $query_data);

                }

            }

        }

        if ($flag) {
            $top_message['content'] = func_get_langvar_by_name('msg_adm_product_wholesale_upd');
            $top_message['type']     = 'I';
        }

    } elseif ($mode == 'wholesales_delete') {

    // Delete pricing

        if (!empty($wpids)) {

            $pids[] = $productid;

            foreach ($wpids as $id => $tmp) {

                if (
                    $geid
                    && $fields['w_price'][$id] == 'Y'
                ) {

                    $old_data = func_query_first("SELECT * FROM $sql_tbl[pricing] WHERE priceid='$id'");

                    while ($pid = func_ge_each($geid, 1, $productid)) {

                        db_query("DELETE FROM $sql_tbl[pricing] WHERE productid = '$pid' AND quantity = '$old_data[quantity]' AND membershipid = '$old_data[membershipid]' AND variantid = '0'");

                        $pids[] = $pid;

                    }

                }

                db_query("DELETE FROM $sql_tbl[pricing] WHERE priceid='$id'");

            }

            $top_message['content'] = func_get_langvar_by_name('msg_adm_product_wholesale_del');
            $top_message['type']     = 'I';

        }

    }

    if (in_array($mode, array('wholesales_modify', 'wholesales_delete'))) {

        if (!empty($pids))
            func_build_quick_prices(array_unique($pids));

        func_refresh('wholesale');

    }

} // if ($REQUEST_METHOD == 'POST')

/**
 * Collect wholesale pricing data
 */
$pricing = func_query("SELECT $sql_tbl[pricing].* FROM $sql_tbl[pricing] LEFT JOIN $sql_tbl[memberships] ON $sql_tbl[memberships].membershipid = $sql_tbl[pricing].membershipid WHERE $sql_tbl[pricing].productid='$productid' AND $sql_tbl[pricing].variantid = '0' ORDER BY $sql_tbl[memberships].orderby, $sql_tbl[pricing].quantity");

$smarty->assign('pricing', $pricing);

?>
