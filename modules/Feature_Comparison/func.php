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
 * Functions for Feature Comparison module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.php,v 1.28.2.3 2011/02/03 11:39:18 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_SESSION_START')) { header("Location: ../../"); die("Access denied"); }

/**
 * Get product feature classes by productid
 */
function func_get_product_features($productid)
{
    global $sql_tbl, $shop_language, $current_area, $config;

    if (empty($productid))
        return false;

    $lng_fields = ", IF($sql_tbl[feature_classes_lng].class IS NOT NULL, $sql_tbl[feature_classes_lng].class, $sql_tbl[feature_classes].class) as class ";
    $lng_tables = " LEFT JOIN $sql_tbl[feature_classes_lng] ON $sql_tbl[feature_classes].fclassid = $sql_tbl[feature_classes_lng].fclassid AND $sql_tbl[feature_classes_lng].code = '$shop_language'";
    if ($current_area == 'C') {
        $where = " AND $sql_tbl[feature_classes].avail = 'Y'";
    } else {
        $where = '';
    }

    // Get class
    $class = func_query_first("SELECT $sql_tbl[feature_classes].* $lng_fields FROM $sql_tbl[product_features], $sql_tbl[feature_classes] $lng_tables WHERE $sql_tbl[feature_classes].fclassid = $sql_tbl[product_features].fclassid AND $sql_tbl[product_features].productid = '$productid' $where");
    if(empty($class))
        return false;

    $lng_fields = ", IF($sql_tbl[feature_options_lng].option_name IS NOT NULL, $sql_tbl[feature_options_lng].option_name, $sql_tbl[feature_options].option_name) as option_name ";
    $lng_fields .= ", IF($sql_tbl[feature_options_lng].option_hint IS NOT NULL, $sql_tbl[feature_options_lng].option_hint, $sql_tbl[feature_options].option_hint) as option_hint ";

    $lng_tables = " LEFT JOIN $sql_tbl[feature_options_lng] ON $sql_tbl[feature_options].foptionid = $sql_tbl[feature_options_lng].foptionid AND $sql_tbl[feature_options_lng].code = '$shop_language'";
    // Set query for customer area
    if ($current_area == 'C') {
        $where = " AND $sql_tbl[feature_options].avail = 'Y'";
    }

    // Get class options
    $options = func_query("SELECT $sql_tbl[feature_options].*, $sql_tbl[product_foptions].value $lng_fields FROM $sql_tbl[feature_options] LEFT JOIN $sql_tbl[product_foptions] ON $sql_tbl[product_foptions].productid = '$productid' AND $sql_tbl[product_foptions].foptionid = $sql_tbl[feature_options].foptionid $lng_tables WHERE $sql_tbl[feature_options].fclassid = '$class[fclassid]' $where ORDER BY $sql_tbl[feature_options].orderby");
    if (empty($options)) {
        if ($current_area == 'C') {
            return false;
        } else {
            return $class;
        }
    }

    foreach($options as $ko => $o) {
        if($o['option_type'] == 'S' || $o['option_type'] == 'M') {
            $options[$ko]['variants'] = func_query_hash("SELECT $sql_tbl[feature_variants_lng].variant_name, $sql_tbl[feature_variants].*".(empty($o["value"]) ? "" : ", IF($sql_tbl[feature_variants].fvariantid ".($o['option_type'] == 'M' ? "IN ('".implode("','", func_sql_unserialize($o["value"]))."')" : "= '$o[value]'")." AND '$o[value]' != '', 1, '') as selected")." FROM $sql_tbl[feature_variants] LEFT JOIN $sql_tbl[feature_variants_lng] ON $sql_tbl[feature_variants].fvariantid=$sql_tbl[feature_variants_lng].fvariantid WHERE foptionid='$o[foptionid]' AND code='$shop_language' ORDER BY orderby", "fvariantid", false);
        }

        if($current_area == 'C' && ($o['option_type'] == 'N' || $o['option_type'] == 'D')) {
            if( $o['option_type'] == 'N') {
                if(empty($o['format'])) {
                    $options[$ko]['formated_value'] = $o['value'];
                } else {
                    $tmp = explode(";", $o['format']);
                    $options[$ko]['formated_value'] = number_format(doubleval($o['value']), $tmp[0], $tmp[1], $tmp[2]);
                }
            } else {
                $options[$ko]['formated_value'] = strftime($o['format'], $o['value']);
            }
        }
    }

    $class['options'] = $options;

    return $class;
}

/**
 * Get comparison list
 */
function func_get_comparison_list($fclassid = 0, $ids = array())
{
    global $sql_tbl, $comparison_list_ids, $comparison_list_fclassid;

    if(empty($ids))
        $ids = @array_keys($comparison_list_ids);
    if(empty($fclassid))
        $fclassid = $comparison_list_fclassid;
    if(empty($ids) || empty($fclassid))
        return false;

    $ids = func_query("SELECT $sql_tbl[products].productid, $sql_tbl[products].product FROM $sql_tbl[products], $sql_tbl[product_features] WHERE $sql_tbl[products].productid IN ('".@implode("','", $ids)."') AND $sql_tbl[products].productid = $sql_tbl[product_features].productid AND $sql_tbl[product_features].fclassid = '$fclassid'");
    if(empty($ids))
        $ids = false;

    return $ids;
}

/**
 * Check product for adding product to comparison list
 */
function func_check_comparison($productid, $fclassid = false)
{
    global $sql_tbl, $comparison_list_ids, $config;

    if($fclassid === false)
        $fclassid = func_query_first_cell("SELECT fclassid FROM $sql_tbl[product_features] WHERE productid = '$productid'");

    if(empty($productid) || ($config['Feature_Comparison']['fcomparison_show_product_list'] != 'Y') || $config['Feature_Comparison']['fcomparison_max_product_list'] <= @count($comparison_list_ids) || @isset($comparison_list_ids[$productid]))
        return false;

    return ($fclassid>0?'Y':'');
}

/**
 * Data cache function: return count of available product with assigned feature classes
 */
function func_dc_fc_count($is_avail)
{
    global $sql_tbl;

    $avail_condition = '';
    if ($is_avail == 'Y')
        $avail_condition = " AND $sql_tbl[products].avail>'0'";
    return func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[feature_classes], $sql_tbl[feature_options], $sql_tbl[product_features], $sql_tbl[products] WHERE $sql_tbl[feature_classes].avail = 'Y' AND $sql_tbl[feature_classes].fclassid = $sql_tbl[feature_options].fclassid AND $sql_tbl[feature_classes].fclassid = $sql_tbl[product_features].fclassid AND $sql_tbl[product_features].productid = $sql_tbl[products].productid AND $sql_tbl[products].forsale = 'Y'".$avail_condition);
}

/**
 * Convert array to suitable-for-search string
 */
function func_sql_serialize($arr)
{
    if (empty($arr) || !is_array($arr))
        return $arr;

    return "|".implode("||", $arr)."|";
}

/**
 * Convert suitable-for-search string to array
 */
function func_sql_unserialize($str)
{
    if (empty($str) || is_array($str))
        return $str;

    return explode("||", substr($str, 1, -1));
}

/**
 * Add feature variant to both feature_variants and feature_variants_lng tables
 */
function func_add_feature_variant($foptionid, $name, $code=false, $orderby=0)
{
    global $config;

    if ($code === false)
        $code = $config['default_admin_language'];

    $id = func_array2insert('feature_variants', array(
        'foptionid' => $foptionid,
        'orderby' => $orderby
    ));
    func_array2insert('feature_variants_lng', array(
        'fvariantid' => $id,
        'variant_name' => $name,
        'code' => $code
    ));
    return $id;
}

/**
 * Remove feature variants using feature option id
 */
function func_remove_feature_variants($optionid)
{
    global $sql_tbl;
    $res = func_query_column("SELECT fvariantid FROM $sql_tbl[feature_variants] WHERE foptionid='$optionid'");

    if (empty($res))
        return false;

    db_query("DELETE FROM $sql_tbl[feature_variants_lng] WHERE fvariantid IN ('".@implode("','", $res)."')");
    db_query("DELETE FROM $sql_tbl[feature_variants] WHERE foptionid='$optionid'");
}

?>
