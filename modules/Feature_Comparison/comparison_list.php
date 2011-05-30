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
 * $Id: comparison_list.php,v 1.24.2.1 2011/01/10 13:11:56 ferz Exp $
 */
// Comparison list script

if (!defined('XCART_SESSION_START')) { header("Location: ../../"); die("Access denied"); }

if(empty($active_modules['Feature_Comparison'])) {
    func_403(55);
}

// Delete element of comparison list
if ($mode == 'delete') {
    if(!empty($fclassid) && !empty($comparison_list_ids)) {
        foreach($comparison_list_ids as $k => $v) {
            if(func_query_first_cell("SELECT fclassid FROM $sql_tbl[product_features] WHERE productid = '$k'") == $fclassid) {
                unset($comparison_list_ids[$k]);
            }
        }
    } elseif(!empty($productid) && isset($comparison_list_ids[$productid])) {
        unset($comparison_list_ids[$productid]);
    } else {
        $comparison_list_ids = array();
    }

// Add product to comparison list
} elseif ($mode == 'add' && (isset($productid) || isset($productids))) {

    if (!isset($productids)) {
        $productids = array();
    }

    if(!empty($productid)) {
        $productids[$productid] = 'Y';
    }

    // Define feature classes hash
    $hash = array();
    if(!empty($comparison_list_ids)) {
        $tmp = func_query("SELECT $sql_tbl[feature_classes].fclassid FROM $sql_tbl[feature_classes], $sql_tbl[product_features] WHERE $sql_tbl[feature_classes].fclassid = $sql_tbl[product_features].fclassid AND $sql_tbl[feature_classes].avail = 'Y' AND $sql_tbl[product_features].productid IN ('".implode("','", array_keys($comparison_list_ids))."')");
        if(!empty($tmp)) {
            foreach($tmp as $v) {
                $hash[$v['fclassid']]++;
            }
        }
    }

    // Adding product id to comparison list
    $is_limit = false;
    foreach($productids as $id => $v) {
        if($v == 'Y') {
            if($config['Feature_Comparison']['fcomparison_max_product_list'] > count($comparison_list_ids)) {
                $fid = func_query_first_cell("SELECT $sql_tbl[feature_classes].fclassid FROM $sql_tbl[feature_classes], $sql_tbl[product_features] WHERE $sql_tbl[feature_classes].fclassid = $sql_tbl[product_features].fclassid AND $sql_tbl[feature_classes].avail = 'Y' AND $sql_tbl[product_features].productid = '$id'");
                if($hash[$fid]+1 <= $config['Feature_Comparison']['fcomparison_comp_product_limit']) {
                    $comparison_list_ids[$id] = true;
                } else {
                    $is_limit = true;
                }
            } else {
                $is_limit = true;
            }
        }
    }

    // Limit 'Maximum number of products which can be compared' or 'The maximum number of products in the comparison list in the menu column' is break
    if($is_limit) {
        $top_message['content'] = func_get_langvar_by_name("txt_max_number_of_products_exceeded");
        $top_message['type'] = 'W';
    }
}
?>
