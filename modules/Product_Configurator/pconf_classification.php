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
 * Classifications for configurable products
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: pconf_classification.php,v 1.31.2.1 2011/01/10 13:11:59 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

x_load('product');

/**
 * Copy product class product-to-product
 */
function func_copy_pclass($classid, $productid)
{
    global $sql_tbl;

    $cid = func_query_first_cell("SELECT ppc1.classid FROM $sql_tbl[pconf_products_classes] as ppc0, $sql_tbl[pconf_products_classes] as ppc1 WHERE ppc0.ptypeid = ppc1.ptypeid AND ppc0.classid = '$classid' AND ppc1.productid = '$productid'");

    if (!empty($cid)) {

        db_query("DELETE FROM $sql_tbl[pconf_products_classes] WHERE classid = '$cid'");
        db_query("DELETE FROM $sql_tbl[pconf_class_specifications] WHERE classid = '$cid'");
        db_query("DELETE FROM $sql_tbl[pconf_class_requirements] WHERE classid = '$cid'");

    }

    $ptypeid = func_query_first_cell("SELECT ptypeid FROM $sql_tbl[pconf_products_classes] WHERE classid = '$classid'");

    if (empty($ptypeid))
        return false;

    $specs     = func_query("SELECT * FROM $sql_tbl[pconf_class_specifications] WHERE classid = '$classid'");
    $req     = func_query("SELECT * FROM $sql_tbl[pconf_class_requirements] WHERE classid = '$classid'");

    $cid = func_array2insert(
        'pconf_products_classes',
        array(
            'ptypeid'     => $ptypeid,
            'productid' => $productid,
        )
    );

    if (!empty($specs)) {

        foreach ($specs as $v) {

            $v['classid'] = $cid;

            $v = func_addslashes($v);

            func_array2insert('pconf_class_specifications', $v);

        }

    }

    if (!empty($req)) {

        foreach ($req as $v) {

            $v['classid'] = $cid;

            $v = func_addslashes($v);

            func_array2insert('pconf_class_requirements', $v);

        }

    }

    return $cid;
}

if ($single_mode) {

    $provider_condition = '';

} elseif ($current_area == 'A') {

    $provider_condition = "AND $sql_tbl[pconf_product_types].provider='$product_info[provider]'";

} else {

    $provider_condition = "AND $sql_tbl[pconf_product_types].provider='$logged_userid'";

}

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

if (
    $REQUEST_METHOD == 'POST'
    && $mode == 'update_classification'
) {

    if (is_array($posted_data)) {

        foreach ($posted_data as $k => $v) {

            // Delete product classification
            if (!empty($v['delete'])) {

                if (
                    $geid
                    && $fields['classes'][$k] == 'Y'
                ) {

                    while($pid = func_ge_each($geid, 1, $productid)) {

                        $cid = func_query_first_cell("SELECT ppc1.classid FROM $sql_tbl[pconf_products_classes] as ppc0, $sql_tbl[pconf_products_classes] as ppc1 WHERE ppc0.ptypeid = ppc1.ptypeid AND ppc0.productid = '$productid' AND ppc0.classid = '$k' AND ppc1.productid = '$pid'");

                        db_query("DELETE FROM $sql_tbl[pconf_products_classes] WHERE classid = '$cid'");
                        db_query("DELETE FROM $sql_tbl[pconf_class_specifications] WHERE classid = '$cid'");
                        db_query("DELETE FROM $sql_tbl[pconf_class_requirements] WHERE classid = '$cid'");

                    }

                }

                db_query("DELETE FROM $sql_tbl[pconf_products_classes] WHERE classid = '$k'");
                db_query("DELETE FROM $sql_tbl[pconf_class_specifications] WHERE classid = '$k'");
                db_query("DELETE FROM $sql_tbl[pconf_class_requirements] WHERE classid = '$k'");

                continue;

            }

            // Update class specifications
            db_query("DELETE FROM $sql_tbl[pconf_class_specifications] WHERE classid = '$k'");

            if (
                is_array($v['specifications'])
                && !empty($v['specifications'])
            ) {

                foreach ($v['specifications'] as $s) {

                    func_array2insert(
                        'pconf_class_specifications',
                        array(
                            'classid'     => $k,
                            'specid'    => $s,
                        )
                    );

                }

            }

            // Update class requirement types & type specifications
            if (is_array($v['req_types'])) {

                foreach ($v['req_types'] as $k1 => $v1) {

                    if ($v1['delete']) {

                        db_query("DELETE FROM $sql_tbl[pconf_class_requirements] WHERE ptypeid = '$k1' AND classid = '$k'");

                        continue;

                    }

                    db_query("DELETE FROM $sql_tbl[pconf_class_requirements] WHERE ptypeid = '$k1' AND specid != '0' AND classid = '$k'");

                    if (
                        is_array($v1['specifications'])
                        && !empty($v1['specifications'])
                    ) {

                        foreach ($v1['specifications'] as $k2=>$v2) {

                            func_array2insert(
                                'pconf_class_requirements',
                                array(
                                    'classid'     => $k,
                                    'ptypeid'     => $k1,
                                    'specid'     => $v2,
                                )
                            );

                        }

                    }

                } // foreach ($v['req_types'] as $k1 => $v1)

            } // if (is_array($v['req_types']))

            // Add new class requirement type
            if (!empty($v['new_reqtype'])) {

                if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[pconf_class_requirements] WHERE classid='$k' AND ptypeid='$v[new_reqtype]'") == 0) {

                    func_array2insert(
                        'pconf_class_requirements',
                        array(
                            'classid' => $k,
                            'ptypeid' => $v['new_reqtype'],
                        )
                    );

                }

            }

            // Copy class data to other products (Group editing of products functionality)
            if (
                $geid
                && $fields['classes'][$k] == 'Y'
            ) {

                while($pid = func_ge_each($geid, 1, $productid)) {

                    func_copy_pclass($k, $pid);

                }

            }

        } // foreach ($posted_data as $k => $v)

    } // if (is_array($posted_data))

    // Add new classification
    if (!empty($new_type)) {

        $query_data = array(
            'productid'     => $productid,
            'ptypeid'         => $new_type,
        );

        if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[pconf_products_classes] WHERE ptypeid='$new_type' AND productid='$productid'") == 0) {
            func_array2insert(
                'pconf_products_classes',
                $query_data
            );
        }

        if(
            $geid
            && $fields['new_type'] == 'Y'
        ) {

            while($pid = func_ge_each($geid, 1, $productid)) {

                if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[pconf_products_classes] WHERE ptypeid='$new_type' AND productid='$pid'") == 0) {

                    $query_data['productid'] = $pid;

                    func_array2insert(
                        'pconf_products_classes',
                        $query_data
                    );

                }

            }

        }

    } // if (!empty($new_type))

    $top_message['content'] = func_get_langvar_by_name('pconf_msg_adm_product_class_upd');
    $top_message['type']     = 'I';

    func_refresh('pclass');
}

/**
 * Get the prodict types information
 */
$product_types = func_query("SELECT $sql_tbl[pconf_product_types].*, IF($sql_tbl[pconf_products_classes].ptypeid IS NULL, '', 'Y') as is_exist FROM $sql_tbl[pconf_product_types] LEFT JOIN $sql_tbl[pconf_products_classes] ON $sql_tbl[pconf_products_classes].productid = '$productid' AND $sql_tbl[pconf_product_types].ptypeid = $sql_tbl[pconf_products_classes].ptypeid WHERE 1 $provider_condition ORDER BY orderby, ptype_name");

$is_free_types = false;

if (!empty($product_types))

    foreach ($product_types as $k=>$v)

        if ($v['is_exist'] != 'Y') {

            $is_free_types = true;

            break;

        }

/**
 * Get the product's classes
 */
$classes = func_query("SELECT * FROM $sql_tbl[pconf_products_classes], $sql_tbl[pconf_product_types] WHERE $sql_tbl[pconf_products_classes].ptypeid=$sql_tbl[pconf_product_types].ptypeid AND productid='$productid' ORDER BY $sql_tbl[pconf_product_types].orderby, $sql_tbl[pconf_product_types].ptype_name");

if (
    is_array($classes)
    && !empty($classes)
) {

    foreach ($classes as $k => $v) {

        // Get the specifications for product type

        $specs = func_query("SELECT $sql_tbl[pconf_specifications].*, IF($sql_tbl[pconf_class_specifications].specid IS NULL, '', 'Y') as selected FROM $sql_tbl[pconf_specifications] LEFT JOIN $sql_tbl[pconf_class_specifications] ON $sql_tbl[pconf_class_specifications].specid = $sql_tbl[pconf_specifications].specid AND $sql_tbl[pconf_class_specifications].classid = '$v[classid]' WHERE $sql_tbl[pconf_specifications].ptypeid='$v[ptypeid]' ORDER BY $sql_tbl[pconf_specifications].orderby, $sql_tbl[pconf_specifications].spec_name");

        if (!empty($specs))
            $classes[$k]['specifications'] = $specs;

        // Get the requirements for product type

        $req_types = func_query("SELECT $sql_tbl[pconf_product_types].* FROM $sql_tbl[pconf_class_requirements], $sql_tbl[pconf_product_types] WHERE $sql_tbl[pconf_product_types].ptypeid=$sql_tbl[pconf_class_requirements].ptypeid AND $sql_tbl[pconf_class_requirements].classid='$v[classid]' GROUP BY $sql_tbl[pconf_product_types].ptypeid ORDER BY $sql_tbl[pconf_product_types].orderby, $sql_tbl[pconf_product_types].ptype_name");

        if (
            !empty($req_types)
            && is_array($req_types)
        ) {

            $classes[$k]['req_types'] = $req_types;

            foreach ($classes[$k]['req_types'] as $k1 => $v1) {

                $all_specs = func_query("SELECT $sql_tbl[pconf_specifications].*, IF($sql_tbl[pconf_class_requirements].specid IS NULL, '', 'Y') as selected FROM $sql_tbl[pconf_specifications] LEFT JOIN $sql_tbl[pconf_class_requirements] ON $sql_tbl[pconf_class_requirements].specid = $sql_tbl[pconf_specifications].specid AND $sql_tbl[pconf_class_requirements].classid = '$v[classid]' WHERE $sql_tbl[pconf_specifications].ptypeid='$v1[ptypeid]' ORDER BY $sql_tbl[pconf_specifications].orderby, $sql_tbl[pconf_specifications].spec_name");

                if (!empty($all_specs)) {
                    $classes[$k]['req_types'][$k1]['specifications'] = $all_specs;
                }

            } // foreach ($classes[$k]['req_types'] as $k1 => $v1)

        }

    } // foreach ($classes as $k => $v)

}

$smarty->assign('classes',             $classes);
$smarty->assign('product_types',     $product_types);
$smarty->assign('is_free_types',     ($is_free_types ? 'Y' : 'N'));
$smarty->assign('mode',             'types');

?>
