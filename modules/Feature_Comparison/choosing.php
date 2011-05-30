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
 * Choosing products be features function
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: choosing.php,v 1.50.2.1 2011/01/10 13:11:56 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_SESSION_START')) { header("Location: ../../"); die("Access denied"); }

if (empty($active_modules['Feature_Comparison'])) {
    func_403(57);
}

x_session_register('store_choosing');

if (empty($mode) && empty($fclassid)) {
    $store_choosing = array();
}

// Select Feature class
if (!empty($fclassid)) {
    if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[feature_classes] WHERE fclassid = '$fclassid' AND avail = 'Y'") > 0) {
        $store_choosing['fclassid'] = $fclassid;
        if (!empty($store_choosing['options']) && is_array($store_choosing['options'])) {
            reset($store_choosing['options']);
            list($foptionid,$tmp) = each($store_choosing['options']);
            if (!func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[feature_options] WHERE fclassid = '$fclassid' AND foptionid = '$foptionid' AND avail = 'Y'")) {
                unset($store_choosing['options']);

            } else {
                reset($store_choosing['options']);
            }
        }
    }
}

// Select Feature class options
if (!empty($options) && $mode == 'select_options') {

    if (is_array($options)) {

        // Check options
        $option_types = func_query_hash("SELECT foptionid, option_type FROM $sql_tbl[feature_options] WHERE foptionid IN ('".implode("','", array_keys($options))."')", "foptionid", false, true);

        foreach ($options as $k => $v) {
            if (!isset($option_types[$k]) || (!is_array($v) && strlen($v) == 0)) {
                unset($options[$k]);
                continue;
            }

            switch ($option_types[$k]) {
                case 'N':
                    $options[$k] = array(
                        (zerolen($v[0]) || !is_numeric($v[0])) ? false : doubleval($v[0]),
                        (zerolen($v[1]) || !is_numeric($v[1])) ? false : doubleval($v[1])
                    );
                    break;

                case 'B':
                    $options[$k] = $v == 'Y';
                    break;

                case 'T':
                    $options[$k] = (string)$v;
                    break;

                case 'D':
                    $options[$k] = array(false, false);
                    if (!empty($v[0]['Date_Year'])) {
                        if (empty($v[0]['Date_Month']))
                            $v[0]['Date_Month'] = 1;

                        if (empty($v[0]['Date_Day']))
                            $v[0]['Date_Day'] = 1;

                        $options[$k][0] = intval(mktime(0, 0, 0, $v[0]['Date_Month'], $v[0]['Date_Day'], $v[0]['Date_Year']));
                    }

                    if (!empty($v[1]['Date_Year'])) {
                        if (empty($v[1]['Date_Month']))
                            $v[1]['Date_Month'] = 12;

                        if (empty($v[1]['Date_Day']))
                            $v[1]['Date_Day'] = 31;

                        $options[$k][1] = intval(mktime(0, 0, 0, $v[1]['Date_Month'], $v[1]['Date_Day'], $v[1]['Date_Year']));
                    }

                    break;
            }
        }

        $store_choosing['options'] = $options;
        $store_choosing['including'] = $including;
    }

    func_header_location("choosing.php?mode=choose");
}

// Choose product(s)
if (!empty($store_choosing['fclassid']) && $mode == 'choose') {

    if (!empty($store_choosing['options']) && is_array($store_choosing['options'])) {

        $where = array();

        // Get options types
        $option_types = func_query_hash("SELECT foptionid, option_type FROM $sql_tbl[feature_options] WHERE foptionid IN ('".implode("','", array_keys($store_choosing['options']))."')", "foptionid", false, true);

        foreach ($store_choosing['options'] as $k => $v) {
            if (!isset($option_types[$k]) || (!is_array($v) && strlen($v) == 0)) {
                unset($store_choosing['options'][$k]);
                continue;
            }

            $tbl_name = 'pfo_'.$k;
            $str = '';
            $option_type = $option_types[$k];

            if ($option_type == 'M' && is_array($v)) {

                // Multiple option selector
                $str = $tbl_name.".value LIKE  '%|".implode("|%' AND $tbl_name.value LIKE '%|", $v)."|%'";

            } elseif ($option_type == 'D' && is_array($v)) {

                // Date
                if ($v[0] !== false && $v[1] !== false) {
                    $str = $tbl_name.".value BETWEEN " . $v[0] . " AND " . $v[1];

                } elseif ($v[0] !== false) {
                    $str = $tbl_name.".value >= " . $v[0];

                } elseif ($v[1] !== false) {
                    $str = $tbl_name.".value <= " . $v[1];
                }

            } elseif ($option_type == 'N') {

                // Numeric
                if ($v[0] !== false && $v[1] !== false) {
                    $str = $tbl_name.".value BETWEEN " . $v[0] . " AND " . $v[1];

                } elseif ($v[0] !== false) {
                    $str = $tbl_name.".value >= " . $v[0];

                } elseif ($v[1] !== false) {
                    $str = $tbl_name.".value <= " . $v[1];
                }

            } elseif ($option_type == 'B') {

                // Boolean
                $str = $tbl_name.".value " . ($v ? "=" : "!=") . " 'Y'";

            } else {

                // Text and single option selector
                if ($store_choosing['including'][$k] == 'phrase') {
                    $str = $tbl_name.".value = '$v'";

                } else {
                    $_words = explode(' ', trim($v));
                    if (!empty($_words)) {
                        foreach ($_words as $_k => $_word) {
                            $_words[$_k] = $tbl_name.".value LIKE '%" . trim($_word) . "%'";
                        }

                        $str = '(' . implode(($store_choosing['including'][$k] == 'all') ? ' AND ' : ' OR ', $_words) . ')';
                    }
                }
            }

            // Define WHERE service array
            if (!empty($str)) {
                $where[$tbl_name] = $tbl_name.".foptionid = '$k' AND ".$str;
            }
        }

        unset($option_types);
    }

    // Search products through the standart procedure
    $old_search_data = $search_data['products'];
    $old_mode = $mode;

    $search_data['products'] = array();
    if (empty($where)) {
        $search_data['products']['fclassid'] = $store_choosing['fclassid'];

    } else {
        $search_data['products']['choosing'] = $where;
    }

    $search_data['products']['forsale'] = 'Y';

    if (!isset($sort))
        $sort = $config['Appearance']['products_order'];

    if (!isset($sort_direction))
        $sort_direction = 0;

    $mode = 'search';

    include $xcart_dir.'/include/search.php';

    $search_script = "choosing.php?fclassid=$store_choosing[fclassid]";

    if ($total_items == 0) {
        $top_message = array(
            'content' => func_get_langvar_by_name("lbl_warning_no_search_results"),
            'type' => 'W'
        );
        func_header_location("choosing.php?fclassid=$store_choosing[fclassid]");
    }

    $search_data['products'] = $old_search_data;
    $mode = $old_mode;
    $smarty->assign('navigation_script', "choosing.php?mode=choose");
    $smarty->assign('search_script', $search_script);

    $smarty->assign('search_sort', $sort);
    $smarty->assign('search_sort_direction', $sort_direction);

    if (!empty($active_modules['Gift_Registry'])) {
        include $xcart_dir.'/modules/Gift_Registry/customer_events.php';
    }

} else {

    // Get Feature class options

    if (!empty($store_choosing['fclassid'])) {
        $options = func_query("SELECT $sql_tbl[feature_options].*, IFNULL($sql_tbl[feature_options_lng].option_name, $sql_tbl[feature_options].option_name) as option_name, IFNULL($sql_tbl[feature_options_lng].option_hint, $sql_tbl[feature_options].option_hint) as option_hint FROM $sql_tbl[feature_options] LEFT JOIN $sql_tbl[feature_options_lng] ON $sql_tbl[feature_options].foptionid = $sql_tbl[feature_options_lng].foptionid AND $sql_tbl[feature_options_lng].code = '$shop_language' WHERE $sql_tbl[feature_options].avail = 'Y' AND $sql_tbl[feature_options].fclassid = '$store_choosing[fclassid]' AND $sql_tbl[feature_options].show_in_search='Y' ORDER BY $sql_tbl[feature_options].orderby");

        if (empty($options))
            func_header_location("choosing.php?mode=choose");

        foreach ($options as $k => $v) {
            if ($v['option_type'] != 'S' && $v['option_type'] != 'M')
                continue;

            $options[$k]['variants'] = func_query("SELECT $sql_tbl[feature_variants].fvariantid, $sql_tbl[feature_variants_lng].variant_name FROM $sql_tbl[feature_variants] LEFT JOIN $sql_tbl[feature_variants_lng] ON $sql_tbl[feature_variants].fvariantid=$sql_tbl[feature_variants_lng].fvariantid AND code='$shop_language' WHERE foptionid='$v[foptionid]' ORDER BY orderby");

            if (empty($options[$k]['variants'])) {
                unset($options[$k]);
                continue;
            }
        }

        if ($options) {
            $smarty->assign('options', $options);
            $smarty->assign('view_options_list', true);
        }
    }

    // Get Feature classes
    $avail_condition = '';
    if ($config['General']['show_outofstock_products'] != 'Y')
        $avail_condition = ($config['General']['unlimited_products'] =="N")? " AND $sql_tbl[products].avail>'0' " : "";

    $classes = func_query("SELECT $sql_tbl[feature_classes].*, IFNULL($sql_tbl[feature_classes_lng].class, $sql_tbl[feature_classes].class) as class, $sql_tbl[images_F].image_path, $sql_tbl[images_F].image_x, $sql_tbl[images_F].image_y FROM $sql_tbl[feature_options], $sql_tbl[product_foptions], $sql_tbl[products], $sql_tbl[feature_classes] LEFT JOIN $sql_tbl[feature_classes_lng] ON $sql_tbl[feature_classes].fclassid = $sql_tbl[feature_classes_lng].fclassid AND $sql_tbl[feature_classes_lng].code = '$shop_language' LEFT JOIN $sql_tbl[images_F] ON $sql_tbl[images_F].id = $sql_tbl[feature_classes].fclassid WHERE $sql_tbl[feature_classes].avail = 'Y' AND $sql_tbl[feature_classes].fclassid = $sql_tbl[feature_options].fclassid AND $sql_tbl[feature_options].foptionid = $sql_tbl[product_foptions].foptionid AND $sql_tbl[product_foptions].productid = $sql_tbl[products].productid AND $sql_tbl[products].forsale = 'Y' $avail_condition GROUP BY $sql_tbl[feature_classes].fclassid ORDER BY $sql_tbl[feature_classes].orderby");

    if (!empty($classes)) {
        if (count($classes) == 1 && empty($store_choosing['fclassid']))
            func_header_location("choosing.php?fclassid=".$classes[0]['fclassid']);

        foreach ($classes as $k => $v) {
            $classes[$k]['is_image'] = !is_null($v['image_path']);
            $classes[$k]['image_url'] = func_get_image_url($v['fclassid'], "F", $v['image_path']);
            unset($classes[$k]['image_path']);
        }

        $smarty->assign('rate', 3);
        $smarty->assign('percent', floor(100 / 3 - 1));

        $smarty->assign('classes', $classes);

        if (empty($options)) {
            $smarty->assign('view_classes_list', true);
        }

    } else {
        $top_message['content'] = func_get_langvar_by_name("lbl_product_types_not_defined");
        $top_message['type'] = 'E';
        func_header_location('home.php');
    }
}

if ($store_choosing['fclassid'] > 0) {
    $location[] = array(func_get_langvar_by_name('lbl_product_type_list'), 'choosing.php');
    if (!empty($options) || $mode == 'choose') {
        $class = func_query_first_cell("SELECT IFNULL($sql_tbl[feature_classes_lng].class, $sql_tbl[feature_classes].class) FROM $sql_tbl[feature_classes] LEFT JOIN $sql_tbl[feature_classes_lng] ON $sql_tbl[feature_classes].fclassid = $sql_tbl[feature_classes_lng].fclassid AND $sql_tbl[feature_classes_lng].code = '$shop_language' WHERE $sql_tbl[feature_classes].fclassid = '$store_choosing[fclassid]'");

        if (!empty($options)) {
            $location[] = array(func_get_langvar_by_name('lbl_X_features', array('product_type' => $class), false, true));
            $smarty->assign('current_class', $class);

        } else {
            $location[] = array(func_get_langvar_by_name('lbl_X_features', array('product_type' => $class), false, true), "choosing.php?fclassid=".$store_choosing['fclassid']);
            $location[] = array(func_get_langvar_by_name('lbl_products'));
        }
    }
} else {
    $location[] = array(func_get_langvar_by_name('lbl_product_type_list'));
}

$smarty->assign('choosing', $store_choosing);
?>
