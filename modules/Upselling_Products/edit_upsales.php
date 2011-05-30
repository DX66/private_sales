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
 * Module process adding and deleting upsales links
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: edit_upsales.php,v 1.41.2.1 2011/01/10 13:12:03 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

x_load('product');

if ($REQUEST_METHOD == 'POST') {

    if ($mode == 'upselling_links') {

    // Insert upsales link into database

        $flag = false;

        if(!empty($upselling)) {

            foreach($upselling as $pid => $v) {

                func_array2update(
                    'product_links',
                    array(
                        'orderby' => $v,

                    ),
                    "productid2='$pid' AND productid1 = '$productid'"
                );

                if (
                    $geid
                    && $fields['u_product'][$pid]
                ) {

                    while($pid2 = func_ge_each($geid, 1, $productid)) {

                        if(func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[product_links] WHERE productid1 = '$pid2' AND productid2 = '$pid'")) {

                            func_array2update(
                                'product_links',
                                array(
                                    'orderby' => $v,
                                ),
                                "productid1 = '$pid2' AND productid2 = '$pid'"
                            );

                        } else {

                            func_array2insert(
                                'product_links',
                                array(
                                    'orderby'         => $v,
                                    'productid1'     => $pid2,
                                    'productid2'     => $pid,
                                ),
                                true
                            );

                        }

                    }

                }

                $flag = true;

            }

        }

        if (
            $selected_productid
            && $productid != $selected_productid
        ) {

            if (!func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[product_links] WHERE productid1 = '$productid' AND productid2 = '$selected_productid'")) {

                $orderby = func_query_first_cell("SELECT MAX(orderby) FROM $sql_tbl[product_links] WHERE productid1 = '$productid'")+1;

                func_array2insert(
                    'product_links',
                    array(
                        'productid1'     => $productid,
                        'productid2'    => $selected_productid,
                        'orderby'        => $orderby,
                    )
                );

            }

            if (
                $bi_directional == 'on'
                && !func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[product_links] WHERE productid1 = '$selected_productid' AND productid2 = '$productid'")
            ) {

                $orderby = func_query_first_cell("SELECT MAX(orderby) FROM $sql_tbl[product_links] WHERE productid1 = '$selected_productid'")+1;

                func_array2insert(
                    'product_links',
                    array(
                        'productid2'    => $productid,
                        'productid1'    => $selected_productid,
                        'orderby'       => $orderby,
                    )
                );

            }

            if(
                $geid
                && $fields['new_u_product'] == 'Y'
            ) {

                while($pid = func_ge_each($geid, 1, $productid)) {

                    if(!func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[product_links] WHERE productid1 = '$pid' AND productid2 = '$selected_productid'")) {

                        $orderby = func_query_first_cell("SELECT MAX(orderby) FROM $sql_tbl[product_links] WHERE productid1 = '$pid'") + 1;

                        func_array2insert(
                            'product_links',
                            array(
                                'productid1'    => $pid,
                                'productid2'    => $selected_productid,
                                'orderby'       => $orderby,
                            )
                        );

                    }

                    if (
                        $bi_directional == 'on'
                        && !func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[product_links] WHERE productid2 = '$pid' AND productid1 = '$selected_productid'")
                    ) {

                        $orderby = func_query_first_cell("SELECT MAX(orderby) FROM $sql_tbl[product_links] WHERE productid1 = '$selected_productid'") + 1;

                        func_array2insert(
                            'product_links',
                            array(
                                'productid2'    => $pid,
                                'productid1'    => $selected_productid,
                                'orderby'       => $orderby,
                            )
                        );

                    }

                } // while($pid = func_ge_each($geid, 1, $productid))

            }

            $flag = true;

        }

        if ($flag) {

            $top_message['content'] = func_get_langvar_by_name('msg_adm_product_upselling_upd');
            $top_message['type']     = 'I';

        }

        func_refresh('upselling');

    } elseif ($mode == 'del_upsale_link') {

    // Deleting upsales link from database

        if (!empty($uids)) {

            foreach ($uids as $product_link => $tmp) {

                db_query("DELETE FROM $sql_tbl[product_links] WHERE productid1='$productid' AND productid2='$product_link'");

                if (
                    $geid
                    && $fields['u_product'][$product_link]
                ) {

                    while($pid = func_ge_each($geid, 1, $productid)) {

                        db_query("DELETE FROM $sql_tbl[product_links] WHERE productid1='$pid' AND productid2='$product_link'");

                    }

                }

            } // foreach ($uids as $product_link => $tmp)

            $top_message['content'] = func_get_langvar_by_name('msg_adm_product_upselling_del');
            $top_message['type']     = 'I';

        } // if (!empty($uids))

        func_refresh('upselling');

    }

} // if ($REQUEST_METHOD == 'POST')

/**
 * Select all linked products
 */
$product_links = func_query("SELECT $sql_tbl[product_links].orderby, $sql_tbl[products].productid, $sql_tbl[products].product, $sql_tbl[products].productcode FROM $sql_tbl[products], $sql_tbl[product_links] WHERE ($sql_tbl[products].productid=$sql_tbl[product_links].productid2) AND ($sql_tbl[product_links].productid1='$productid') GROUP BY $sql_tbl[products].productid ORDER BY $sql_tbl[product_links].orderby, $sql_tbl[products].product");

$smarty->assign('product_links',$product_links);

?>
