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
 * Assign and modify feature class to product
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: product_class.php,v 1.28.2.1 2011/01/10 13:11:57 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_SESSION_START')) { header("Location: ../../"); die("Access denied"); }

x_load('product');

if ($section == 'feature_class') {

    // Assign feature class to product
    if ($REQUEST_METHOD=="POST" && $mode == 'product_class_assign' && $productid) {
        $old_fclassid = func_query_first_cell("SELECT fclassid FROM $sql_tbl[product_features] WHERE productid = '$productid'");
        if (!empty($old_fclassid)) {
            db_query("DELETE FROM $sql_tbl[product_foptions] WHERE productid = '$productid'");
            db_query("DELETE FROM $sql_tbl[product_features] WHERE productid = '$productid'");
        }
        if (!empty($fclassid)) {
            db_query("REPLACE INTO $sql_tbl[product_features] VALUES ('$productid','$fclassid')");
        }
        if($geid && $fields['fclass'] == 'Y') {
            while($pid = func_ge_each($geid, 1, $productid)) {
                if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[product_features] WHERE productid = '$pid'") > 0) {
                    db_query("DELETE FROM $sql_tbl[product_foptions] WHERE productid = '$pid'");
                    db_query("DELETE FROM $sql_tbl[product_features] WHERE productid = '$pid'");
                }
                if (!empty($fclassid)) {
                    db_query("REPLACE INTO $sql_tbl[product_features] VALUES ('$pid','$fclassid')");
                }
            }
        }

        func_data_cache_get('fc_count', array('Y'), true);
        func_data_cache_get('fc_count', array('N'), true);

        func_refresh('feature_class');

    // Edit class options
    } elseif ($REQUEST_METHOD=="POST" && $mode == 'product_class_modify' && $productid && $options) {
        db_query("DELETE FROM $sql_tbl[product_foptions] WHERE productid = '$productid'");

        foreach($options as $k => $v) {
            $type = func_query_first_cell("SELECT option_type FROM $sql_tbl[feature_options] WHERE foptionid = '$k'");
            if ($type == 'M') {
                $v = func_sql_serialize($v);
            } elseif($type == 'D') {
                $v = func_prepare_search_date($v);
            } elseif($type == 'N') {
                $v = (float)$v;
            }

            func_array2insert('product_foptions', array(
                'foptionid' => $k,
                'productid' => $productid,
                'value' => $v
            ));
            $options[$k] = $v;
        }

        if($geid && !empty($fields['foptions'])) {
            $data = func_query("SELECT * FROM $sql_tbl[product_foptions] WHERE productid = '$productid' AND foptionid IN ('".implode("','", array_keys($fields['foptions']))."')");
            if (!empty($data)) {
                $data = func_addslashes($data);
                while($pid = func_ge_each($geid, 1, $productid)) {
                    foreach ($data as $v) {
                        $v['productid'] = $pid;
                        func_array2insert('product_foptions', $v, true);
                    }
                }
            }
        }

        func_data_cache_get('fc_count', array('Y'), true);
        func_data_cache_get('fc_count', array('N'), true);

        func_refresh('feature_class');
    }

    // Get classes list
    $fc_provider_condition = '';
    if (empty($active_modules['Simple_Mode']))
        $fc_provider_condition = "WHERE $sql_tbl[feature_classes].provider = '$logged_userid' OR $sql_tbl[feature_classes].avail = 'Y'";
    $classes = func_query("SELECT $sql_tbl[feature_classes].fclassid, IFNULL($sql_tbl[feature_classes_lng].class, $sql_tbl[feature_classes].class) as class FROM $sql_tbl[feature_classes] LEFT JOIN $sql_tbl[feature_classes_lng] ON $sql_tbl[feature_classes].fclassid = $sql_tbl[feature_classes_lng].fclassid AND $sql_tbl[feature_classes_lng].code = '$shop_language' $fc_provider_condition ORDER BY orderby");
    if (!empty($classes))
        $smarty->assign('fc_classes', $classes);
}
?>
