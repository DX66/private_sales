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
 * Functions related to product options
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: product_options.php,v 1.89.2.2 2011/03/07 12:56:43 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

x_load('product');

/**
 * Copy product option to another product
 */
function func_copy_class($classid, $productid = false)
{
    global $sql_tbl, $geid;

    $data['class']                 = func_query_first("SELECT * FROM $sql_tbl[classes] WHERE classid = '$classid'");
    $data['class_options']         = func_query("SELECT * FROM $sql_tbl[class_options] WHERE classid = '$classid'");
    $data['class_lng']             = func_query("SELECT * FROM $sql_tbl[class_lng] WHERE classid = '$classid'");
    $data['class_options_lng']     = func_query("SELECT $sql_tbl[product_options_lng].* FROM $sql_tbl[product_options_lng], $sql_tbl[class_options] WHERE $sql_tbl[product_options_lng].optionid = $sql_tbl[class_options].optionid AND $sql_tbl[class_options].classid = '$classid'");

    if (empty($productid)) {

        while ($pid = func_ge_each($geid, 1, $productid)) {

            if ($pid == $data['class']['productid'])
                continue;

            $local_data = $data;

            if ($data['class']['is_modifier'] == '') {

                $is_product_type = func_query_first_cell("SELECT product_type FROM $sql_tbl[products] WHERE productid = '$pid'");

                if ($is_product_type == 'C')
                    $local_data['class']['is_modifier'] = 'Y';

            }

            func_add_class_data($local_data, $pid);

        } // while ($pid = func_ge_each($geid, 1, $productid))

    } else {

        if (!is_array($productid))
            $productid = array($productid);

        foreach ($productid as $pid) {

            $local_data = $data;

            if ($data['class']['is_modifier'] == '') {

                $is_product_type = func_query_first_cell("SELECT product_type FROM $sql_tbl[products] WHERE productid = '$pid'");

                if ($is_product_type == 'C')
                    $local_data['class']['is_modifier'] = 'Y';

            }

            func_add_class_data($local_data, $pid);

        }

    }

}

/**
 * Add packed class data to product
 */
function func_add_class_data($data, $productid)
{
    global $sql_tbl, $rebuild;

    // Update class data
    $comp = $data['class'];

    $comp['productid'] = $productid;

    func_unset($comp, 'classid');

    $comp = func_addslashes($comp);

    $classid = func_query_first_cell("SELECT classid FROM $sql_tbl[classes] WHERE class = '$comp[class]' AND productid = '$comp[productid]'");

    $is_new = empty($classid);

    if (!empty($classid)) {

        func_array2update(
            'classes',
            $comp,
            "classid = '$classid'"
        );

    } else {

        $classid = func_array2insert('classes', $comp);

    }

    if ($comp['is_modifier'] == '')
        $rebuild = true;

    // Update class multilanguage data
    db_query("DELETE FROM $sql_tbl[class_lng] WHERE classid = '$classid'");

    foreach ($data['class_lng'] as $v) {

        $v['classid'] = $classid;

        $v = func_addslashes($v);

        func_array2insert(
            'class_lng',
            $v,
            true
        );

    }

    // Update class options
    $ids = array();

    foreach ($data['class_options'] as $k => $opt) {

        $opt['classid'] = $classid;

        $old_optionid = $opt['optionid'];

        func_unset($opt, 'optionid');

        $opt = func_addslashes($opt);

        $optionid = func_query_first_cell("SELECT optionid FROM $sql_tbl[class_options] WHERE classid = '$classid' AND option_name = '$opt[option_name]'");

        if (empty($optionid)) {

            $optionid = func_array2insert('class_options', $opt);

        } else {

            func_array2update(
                'class_options',
                $opt,
                "optionid = '$optionid'"
            );

        }

        $ids[$old_optionid] = $optionid;

    }

    // Update class option multilanguage data
    db_query("DELETE FROM $sql_tbl[product_options_lng] WHERE optionid IN ('" . implode("','", array_keys($ids)) . "')");

    foreach ($data['class_options_lng'] as $v) {

        if (!isset($ids[$v['optionid']]))
            continue;

        $v['optionid'] = $ids[$v['optionid']];

        $v = func_addslashes($v);

        func_array2insert(
            'product_options_lng',
            $v,
            true
        );

    }

    // Detect and delete old product option class options
    $ids = func_query_column("SELECT optionid FROM $sql_tbl[class_options] WHERE classid = '$classid' AND optionid NOT IN ('" . implode("','", $ids) . "')");

    if (!empty($ids)) {

        $rebuild = true;

        db_query("DELETE FROM $sql_tbl[class_options] WHERE classid = '$classid' AND optionid IN ('".implode("','", $ids)."')");
        db_query("DELETE FROM $sql_tbl[product_options_lng] WHERE optionid IN ('".implode("','", $ids)."')");
        db_query("DELETE FROM $sql_tbl[product_options_ex] WHERE optionid IN ('".implode("','", $ids)."')");

    }

}

if (empty($edit_language))
    $edit_language = $shop_language;

if ($REQUEST_METHOD == 'POST') {

    $class_names = func_query_hash("SELECT classid, class FROM $sql_tbl[classes] WHERE productid = '$productid'", "classid", false, true);

    $refresh = $rebuild = $rebuild_quick = false;

    // Add/Update product options properties

    if (
        $mode == 'product_options_modify'
        && $po_classes
    ) {

        $old_avails = func_query_hash("SELECT classid, avail FROM $sql_tbl[classes] WHERE classid IN ('".implode("','", array_keys($po_classes))."') AND is_modifier = ''", "classid", false, true);

        foreach ($po_classes as $k => $v) {

            db_query("UPDATE $sql_tbl[classes] SET orderby = '$v[orderby]', avail = '$v[avail]' WHERE classid = '$k'");

            if (
                isset($old_avails[$k])
                && $v['avail'] != $old_avails[$k]
            ) {
                $rebuild = true;
            }

            if (
                $geid
                && $fields['classes'][$k]
            ) {

                func_copy_class($k);

            }

        } // foreach ($po_classes as $k => $v)

        $rebuild_quick          = true;
        $top_message['content'] = func_get_langvar_by_name('msg_adm_product_options_upd');
        $top_message['type']    = 'I';
        $refresh                = true;

    } elseif (
        $mode == 'product_options_delete'
        && $to_delete
    ) {

        // Delete class

        $to_delete = array_keys($to_delete);

        foreach ($to_delete as $cid) {

            func_delete_po_class($cid);

            if (
                $geid
                && $fields['classes'][$cid]
            ) {

                while($pid = func_ge_each($geid, 1, $productid)) {

                    $cid0 = func_query_first_cell("SELECT classid FROM $sql_tbl[classes] WHERE productid = '$pid' AND class = '".addslashes($class_names[$cid])."'");

                    if (empty($cid0))
                        continue;

                    func_delete_po_class($cid0);

                }

            }

        } // foreach ($to_delete as $cid)

        $top_message['content']     = func_get_langvar_by_name('msg_adm_product_option_del');
        $top_message['type']         = 'I';

        $refresh = $rebuild = $rebuild_quick = true;

    } elseif ($mode == 'product_options_add') {

        // Add/Update class/class variants

        $url_anchor = "#modify_class";

        if (
            empty($add['is_modifier'])
            && $product_info['product_type'] == 'C'
        ) {
            $add['is_modifier'] = 'Y';
        }

        if ($add['is_modifier'] == 'T')
            unset($list, $new_list);

        if (
            empty($add['class'])
            || empty($add['classtext'])
        ) {

            if (!empty($classid)) {

                func_refresh('options', "&classid=$classid");

            } else {

                func_refresh('options', '');

            }

        }

        if (!empty($classid)) {

            // Update class
            $query_data = array(
                'orderby'     => $add['orderby'],
                'avail'       => $add['avail'],
                'is_modifier' => $add['is_modifier'],
            );

            if ($edit_lng == $config['default_admin_language']) {

                $query_data['class']         = $add['class'];
                $query_data['classtext']     = $add['classtext'];

            }

            func_array2update(
                'classes',
                $query_data,
                "classid = '$classid'"
            );

            if (
                $geid
                && !empty($fields)
            ) {

                foreach ($query_data as $k => $v) {

                    if (!isset($fields[$k]))
                        unset($query_data[$k]);

                }

                if (!empty($query_data)) {

                    while($pid = func_ge_each($geid, 1, $productid)) {

                        $local_query_data = $query_data;

                        if ($query_data['is_modifier'] == '') {

                            $is_product_type = func_query_first_cell("SELECT product_type FROM $sql_tbl[products] WHERE productid = '$pid'");

                            if ($is_product_type == 'C')
                                $local_query_data['is_modifier'] = 'Y';

                        }

                        func_array2update(
                            'classes',
                            $local_query_data,
                            "productid = '$pid' AND class = '" . addslashes($class_names[$classid]) . "'"
                        );

                    }

                } // if (!empty($query_data))

            }

            // Update multilanguage class data
            $query_data = array(
                'code'      => $edit_lng,
                'classid'   => $classid,
                'class'     => $add['class'],
                'classtext' => $add['classtext'],
            );

            func_array2insert(
                'class_lng',
                $query_data,
                true
            );

            if (
                $geid
                && (
                    $fields['class'] == 'Y'
                    || $fields['classtext'] == 'Y'
                )
            ) {
                if ($fields['class'] != 'Y')
                    unset($query_data['class']);

                if ($fields['classtext'] != 'Y')
                    unset($query_data['classtext']);

                while ($pid = func_ge_each($geid, 1, $productid)) {

                    $query_data['classid'] = func_query_first_cell("SELECT classid FROM $sql_tbl[classes] WHERE productid = '$pid' AND class = '".addslashes($class_names[$classid])."'");

                    if (!empty($query_data['classid'])) {

                        func_array2insert(
                            'class_lng',
                            $query_data,
                            true
                        );

                    }

                } // while ($pid = func_ge_each($geid, 1, $productid))

            }

            // Delete options if class is text option
            if (
                $add['is_modifier'] == 'T'
                || $add['is_modifier'] == 'A'
            ) {

                $opts = func_query_column("SELECT optionid FROM $sql_tbl[class_options] WHERE classid = '$classid'");

                if (!empty($opts)) {

                    db_query("DELETE FROM $sql_tbl[class_options] WHERE classid = '$classid'");
                    db_query("DELETE FROM $sql_tbl[product_options_lng] WHERE optionid IN ('" . implode("','", $opts) . "')");

                }

                // Delete options if class is text option (Group editing of products functionality)
                if (
                    $geid
                    && $fields['is_modifier'] == 'Y'
                ) {

                    while ($pid = func_ge_each($geid, 1, $productid)) {

                        $opts = func_query_column("SELECT o1.optionid FROM $sql_tbl[classes] as c0, $sql_tbl[class_options] as o0, $sql_tbl[classes] as c1, $sql_tbl[class_options] as o1 WHERE c0.classid = o0.classid AND o0.classid = '$classid' AND c1.class = c0.class AND c1.classid = o1.classid AND o0.option_name = o1.option_name AND c1.productid = '$pid'");

                        if (!empty($opts)) {

                            db_query("DELETE FROM $sql_tbl[class_options] WHERE optionid IN ('".implode("','", $opts)."')");
                            db_query("DELETE FROM $sql_tbl[product_options_lng] WHERE optionid IN ('".implode("','", $opts)."')");

                        }

                    }

                }

            } elseif (!empty($list)) {

                // Update class options
                foreach ($list as $k => $v) {

                    if ($add['is_modifier'] != 'Y') {

                        $list[$k]['price_modifier'] = "0.00";
                        $list[$k]['modifier_type']     = "$";

                    }

                    $list[$k]['avail'] = $v['avail'];

                }

                // Update class options (Group editing of products functionality)
                if (
                    $geid
                    && $fields['options'] == 'Y'
                ) {

                    while ($pid = func_ge_each($geid, 1, $productid)) {

                        foreach ($list as $k => $v) {

                            $k1 = func_query_first_cell("SELECT o1.optionid FROM $sql_tbl[classes] as c0, $sql_tbl[class_options] as o0, $sql_tbl[classes] as c1, $sql_tbl[class_options] as o1 WHERE c0.classid = o0.classid AND o0.optionid = '$k' AND c1.class = c0.class AND c1.classid = o1.classid AND o0.option_name = o1.option_name AND c1.productid = '$pid'");

                            if (empty($k1))
                                continue;

                            $v['price_modifier'] = func_convert_number($v['price_modifier']);

                            $query_data = array(
                                'code'             => $edit_lng,
                                'optionid'         => $k1,
                                'option_name'     => $v['option_name'],
                            );

                            if ($edit_lng != $config['default_admin_language']) {

                                unset($v['option_name']);

                            }

                            func_array2update(
                                'class_options',
                                $v,
                                "optionid = '$k1'"
                            );

                            // Update multilanguage option data (Group editing of products functionality)
                            if (!empty($query_data['option_name'])) {

                                func_array2insert(
                                    'product_options_lng',
                                    $query_data,
                                    true
                                );

                            }

                        } // foreach ($list as $k => $v)

                    } // while ($pid = func_ge_each($geid, 1, $productid))

                }

                foreach ($list as $k => $v) {

                    $query_data = array(
                        'code'             => $edit_lng,
                        'optionid'         => $k,
                        'option_name'     => $v['option_name'],
                    );

                    if ($edit_lng != $config['default_admin_language']) {

                        unset($v['option_name']);

                    }

                    $v['price_modifier'] = func_convert_number($v['price_modifier']);

                    func_array2update(
                        'class_options',
                        $v,
                        "optionid = '$k'"
                    );

                    // Update multilanguage option data
                    if (!empty($query_data['option_name'])) {

                        func_array2insert(
                            'product_options_lng',
                            $query_data,
                            true
                        );

                    }

                } // foreach ($list as $k => $v)

            }

            // Add new class option
            foreach ($new_list['option_name'] as $nlk => $option_name) {

                $option_name = trim($option_name);

                if (strlen($option_name) == 0)
                    continue;

                // Define current new option
                $nlist = array(
                    'option_name' => $option_name,
                );

                foreach (array_keys($new_list) as $key) {

                    if ($key != 'option_name')
                        $nlist[$key] = $new_list[$key][$nlk];

                }

                if ($add['is_modifier'] != 'Y')
                    unset($nlist['modifier_type'], $nlist['price_modifier']);

                if (empty($nlist['orderby']))
                    $nlist['orderby'] = func_query_first_cell("SELECT MAX(orderby) FROM $sql_tbl[class_options] WHERE classid = '$classid'")+1;

                $query_data = array(
                    'code'             => $edit_lng,
                    'option_name'     => $nlist['option_name'],
                );

                if ($shop_language != $config['default_admin_language'])
                    unset($nlist['option_name']);

                $nlist['price_modifier']     = func_convert_number($nlist['price_modifier']);
                $nlist['classid']             = $classid;

                $optionid = func_array2insert('class_options', $nlist);

                // Add multilanguage data of new class option
                $query_data['optionid'] = $optionid;

                func_array2insert(
                    'product_options_lng',
                    $query_data,
                    true
                );

                // Add new class option (Group editing of products functionality)
                if (
                    $geid
                    && $fields['options'] == 'Y'
                ) {

                    while ($pid = func_ge_each($geid, 1, $productid)) {

                        $cid = func_query_first_cell("SELECT classid FROM $sql_tbl[classes] WHERE productid = '$pid' AND class = '".addslashes($class_names[$classid])."'");
                        if (empty($cid))
                            continue;

                        $nlist['classid'] = $cid;

                        $optionid = func_array2insert('class_options', $new_list);

                        // Add multilanguage data of new class option (Group editing of products functionality)
                        $query_data = array(
                            'code'             => $edit_lng,
                            'optionid'         => $optionid,
                            'option_name'     => $nlist['option_name'],
                        );

                        func_array2insert(
                            'product_options_lng',
                            $query_data,
                            true
                        );

                    } // while ($pid = func_ge_each($geid, 1, $productid))

                }

            } // foreach ($new_list['option_name'] as $nlk => $option_name)

            $top_message['content'] = func_get_langvar_by_name('msg_adm_product_option_upd');
            $top_message['type']     = 'I';

        } else {
            // Insert new class
            if (
                $add['is_modifier'] == 'T'
                || $add['is_modifier'] == 'A'
            ) {
                unset($list);
            }

            if (empty($add['orderby']))
                $add['orderby'] = func_query_first_cell("SELECT MAX(orderby) FROM $sql_tbl[classes] WHERE productid = '$productid'")+1;

            $add['productid'] = $productid;

            $classid = func_array2insert('classes', $add);

            db_query("DELETE FROM $sql_tbl[class_lng] WHERE classid = '$classid'");

            // Insert multilanguage class data
            $query_data = array(
                'code'      => $shop_language,
                'classid'   => $classid,
                'class'     => $add['class'],
                'classtext' => $add['classtext'],
            );

            func_array2insert(
                'class_lng',
                $query_data,
                true
            );

            // Insert class variants
            if (
                !empty($classid)
                && !empty($list)
            ) {

                if (preg_match_all("/([^=\n]+)[ \t]*=?[ \t]*([\d-+.]*)([$%]?)/Ss", $list, $preg)) {

                    if ($add['is_modifier'] != 'Y')
                        unset($preg[2], $preg[3]);

                    foreach ($preg[1] as $k => $v) {

                        $v = trim($v);

                        if (strlen($v) > 0) {

                            $query_data = array(
                                'classid'         => $classid,
                                'option_name'     => $v,
                                'orderby'         => $k + 1,
                            );

                            if ($add['is_modifier'] == 'Y') {

                                $query_data['price_modifier'] = func_convert_number($preg[2][$k]);
                                $query_data['modifier_type']  = $preg[3][$k];

                                if (!in_array($query_data['modifier_type'], array("%","$")))
                                    $query_data['modifier_type'] = "$";

                            }

                            $optionid = func_array2insert('class_options', $query_data);

                            db_query("DELETE FROM $sql_tbl[product_options_lng] WHERE optionid = '$optionid'");

                            // Insert multilanguage class option data
                            $query_data = array(
                                'code'        => $shop_language,
                                'optionid'    => $optionid,
                                'option_name' => $v,
                            );

                            func_array2insert(
                                'product_options_lng',
                                $query_data,
                                true
                            );

                        } // if (strlen($v) > 0)

                    } // foreach ($preg[1] as $k => $v)

                } // if (preg_match_all("/([^=\n]+)[ \t]*=?[ \t]*([\d-+.]*)([$%]?)/Ss", $list, $preg))

            }

            if (
                $geid
                && $fields['new_class'] == 'Y'
            ) {

                while ($pid = func_ge_each($geid, 1, $productid)) {

                    func_copy_class($classid, $pid);

                }

            }

            $top_message['content'] = func_get_langvar_by_name('msg_adm_product_option_add');
            $top_message['type']     = 'I';

        }

        $refresh = $rebuild = $rebuild_quick = true;

    } elseif (
        $mode == 'product_option_delete'
        && $classid
        && $to_delete
    ) {

        // Delete class option(s)

        $url_anchor = "#modify_class";

        $to_delete = array_keys($to_delete);

        if (
            $geid
            && $fields['options'] == 'Y'
        ) {

            $name_where = func_query_hash("SELECT $sql_tbl[classes].class, $sql_tbl[class_options].option_name FROM $sql_tbl[classes], $sql_tbl[class_options] WHERE $sql_tbl[classes].classid = $sql_tbl[class_options].classid AND $sql_tbl[class_options].optionid IN ('".implode("','", $to_delete)."')", "class", true, true);

            foreach ($name_where as $cn => $opts) {

                $name_where[$cn] = "($sql_tbl[classes].class = '"
                    . addslashes($cn)
                    . "' AND $sql_tbl[class_options].option_name IN ('"
                    . implode("','", func_addslashes($opts))
                    . "'))";

            }

            $name_where = " AND " . implode(" OR ", $name_where);

            while ($pid = func_ge_each($geid, 1, $productid)) {

                $opts = func_query_column("SELECT optionid FROM $sql_tbl[class_options], $sql_tbl[classes] WHERE $sql_tbl[class_options].classid = $sql_tbl[classes].classid AND $sql_tbl[classes].productid = '$pid'".$name_where);

                db_query("DELETE FROM $sql_tbl[class_options] WHERE optionid IN ('".implode("','", $opts)."')");
                db_query("DELETE FROM $sql_tbl[product_options_lng] WHERE optionid IN ('".implode("','", $opts)."')");
                db_query("DELETE FROM $sql_tbl[product_options_ex] WHERE optionid IN ('".implode("','", $opts)."')");

            }

        }

        db_query("DELETE FROM $sql_tbl[class_options] WHERE optionid IN ('".implode("','", $to_delete)."')");
        db_query("DELETE FROM $sql_tbl[product_options_lng] WHERE optionid IN ('".implode("','", $to_delete)."')");
        db_query("DELETE FROM $sql_tbl[product_options_ex] WHERE optionid IN ('".implode("','", $to_delete)."')");

        $top_message['content'] = func_get_langvar_by_name('msg_adm_option_del');
        $top_message['type']     = 'I';

        $refresh = $rebuild = $rebuild_quick = true;

    } elseif (
        $mode == 'product_options_ex_delete'
        && $to_delete
    ) {

        // Delete exception(s)

        $url_anchor = "#exceptions";

        $to_delete = array_keys($to_delete);

        if (
            $geid
            && !empty($fields['exceptions'])
        ) {

            foreach ($to_delete as $eid) {

                if ($fields['exceptions'][$eid] != 'Y')
                    continue;

                $name_where = func_query_hash("SELECT $sql_tbl[classes].class, $sql_tbl[class_options].option_name FROM $sql_tbl[product_options_ex], $sql_tbl[classes], $sql_tbl[class_options] WHERE $sql_tbl[classes].classid = $sql_tbl[class_options].classid AND $sql_tbl[class_options].optionid = $sql_tbl[product_options_ex].optionid AND $sql_tbl[product_options_ex].exceptionid = '$eid'", "class", true, true);

                foreach ($name_where as $cn => $opts) {

                    $name_where[$cn] = "($sql_tbl[classes].class = '"
                        . addslashes($cn)
                        . "' AND $sql_tbl[class_options].option_name IN ('"
                        . implode("','", func_addslashes($opts))
                        . "'))";

                }

                $name_where = empty($name_where)
                    ? ''
                    : (" AND (" . implode(" OR ", $name_where) . ")");

                while ($pid = func_ge_each($geid, 1, $productid)) {

                    $opts = func_query_column("SELECT optionid FROM $sql_tbl[class_options], $sql_tbl[classes] WHERE $sql_tbl[class_options].classid = $sql_tbl[classes].classid AND $sql_tbl[classes].productid = '$pid'".$name_where);

                    if (empty($opts))
                        continue;

                    $eid = func_query_first_cell("SELECT $sql_tbl[product_options_ex].exceptionid, COUNT($sql_tbl[product_options_ex].exceptionid) as cnt, COUNT(ex.exceptionid) as cnt0 FROM $sql_tbl[product_options_ex] LEFT JOIN $sql_tbl[product_options_ex] as ex ON $sql_tbl[product_options_ex].exceptionid = ex.exceptionid WHERE $sql_tbl[product_options_ex].optionid IN ('".implode("','", $opts)."') GROUP BY exceptionid HAVING cnt = cnt0 ORDER BY cnt DESC LIMIT 1");

                    if (!empty($eid))
                        db_query("DELETE FROM $sql_tbl[product_options_ex] WHERE exceptionid = '$eid'");

                } // while ($pid = func_ge_each($geid, 1, $productid))

            } // foreach ($to_delete as $eid)

        }

        db_query("DELETE FROM $sql_tbl[product_options_ex] WHERE exceptionid IN ('" . implode("','", $to_delete) . "')");

        $top_message['content'] = func_get_langvar_by_name('msg_adm_product_option_exc_del');
        $top_message['type']     = 'I';

        $refresh = $rebuild_quick = true;

    } elseif (
        $mode == 'product_options_ex_add'
        && !empty($new_exception)
    ) {

        // Add new exception

        $url_anchor = "#exceptions";

        foreach ($new_exception as $k => $v) {

            if (empty($v)) {

                unset($new_exception[$k]);

            }

        }

        $is_exist = (func_query_first_cell("SELECT COUNT(*) as count FROM $sql_tbl[product_options_ex] WHERE optionid IN ('".implode("','", $new_exception)."') GROUP BY exceptionid ORDER BY count DESC") == count($new_exception));

        if (count($new_exception) > 0) {

            $exception_added = false;

            if (!$is_exist) {

                $exceptionid = func_query_first_cell("SELECT MAX(exceptionid) FROM $sql_tbl[product_options_ex]")+1;

                foreach ($new_exception as $v) {

                    func_array2insert(
                        'product_options_ex',
                        array(
                            'exceptionid'     => $exceptionid,
                            'optionid'         => $v,
                        )
                    );

                    $exception_added = true;

                } // foreach ($new_exception as $v)

            } // if (!$is_exist)

            if (
                $geid
                && $fields['new_exception'] == 'Y'
            ) {

                $name_where = func_query_hash("SELECT $sql_tbl[classes].class, $sql_tbl[class_options].option_name FROM $sql_tbl[classes], $sql_tbl[class_options] WHERE $sql_tbl[classes].classid = $sql_tbl[class_options].classid AND $sql_tbl[class_options].optionid IN ('".implode("','", $new_exception)."')", "class", true, true);

                foreach ($name_where as $cn => $opts) {

                    $name_where[$cn] = "($sql_tbl[classes].class = '"
                        . addslashes($cn)
                        . "' AND $sql_tbl[class_options].option_name IN ('"
                        . implode("','", func_addslashes($opts))
                        . "'))";

                }

                $name_where = empty($name_where)
                    ? ''
                    : (" AND (" . implode(" OR ", $name_where) . ")");

                $names = func_query("SELECT classid, option_name FROM $sql_tbl[class_options] WHERE optionid IN ('" . implode("','", $new_exception) . "')");

                if ($names) {

                    while ($pid = func_ge_each($geid, 1, $productid)) {

                        $opts = func_query_column("SELECT $sql_tbl[class_options].optionid FROM $sql_tbl[classes], $sql_tbl[class_options] WHERE $sql_tbl[classes].classid = $sql_tbl[class_options].classid AND $sql_tbl[classes].productid = '$pid'".$name_where);

                        $ex_count = func_query_first_cell("SELECT COUNT(*) as count FROM $sql_tbl[product_options_ex] WHERE optionid IN ('".implode("','", (array)$opts)."') GROUP BY $sql_tbl[product_options_ex].exceptionid ORDER BY count DESC");

                        $found = ($ex_count == count($new_exception));

                        if (!$found) {

                            $exceptionid = func_query_first_cell("SELECT MAX(exceptionid) FROM $sql_tbl[product_options_ex]")+1;

                            foreach($opts as $v) {

                                func_array2insert(
                                    'product_options_ex',
                                    array(
                                        'exceptionid'     => $exceptionid,
                                        'optionid'         => $v,
                                    )
                                );

                                $exception_added = true;

                            }

                        } // if (!$found)

                    } // while ($pid = func_ge_each($geid, 1, $productid))

                } // if ($names)

            }

            if (
                $is_exist
                && !$exception_added
            ) {

                $top_message['content'] = func_get_langvar_by_name('msg_adm_product_options_exc_no_add');
                $top_message['type'] = 'E';

            } else {

                $top_message['content'] = func_get_langvar_by_name('msg_adm_product_options_exc_add');
                $top_message['type'] = 'I';

            }

        } // if (count($new_exception) > 0)

        $refresh = $rebuild_quick = true;

    } elseif (
        $mode == 'product_options_js_update'
        && $user_account['allow_active_content']
    ) {

        // Update Validation script (Javascript)

        if (
            $geid
            && $fields['js']
        ) {

            while ($pid = func_ge_each($geid, 1)) {

                func_array2insert(
                    'product_options_js',
                    array(
                        'productid'         => $pid,
                        'javascript_code'     => $js_code,
                    ),
                    true
                );

            }

        } else {

            func_array2insert(
                'product_options_js',
                array(
                    'productid'         => $productid,
                    'javascript_code'     => $js_code,
                ),
                true
            );

        }

        $top_message['content'] = func_get_langvar_by_name('msg_adm_product_options_js_upd');
        $top_message['type']     = 'I';

    } elseif (
        $mode == 'product_options_js_update'
        && !$user_account['allow_active_content']
    ) {

        $top_message['content'] = func_get_langvar_by_name('msg_untrusted_provider');
        $top_message['type']     = 'E';

    }

    if ($rebuild) {

        if (!empty($geid)) {

            while ($pid = func_ge_each($geid, 1)) {

                func_rebuild_variants($pid);

            }

        } else {

            func_rebuild_variants($productid);

        }

    }

    if ($rebuild_quick) {

        if ($geid) {

            while ($pid = func_ge_each($geid, 100)) {

                func_build_quick_flags($pid);
                func_build_quick_prices($pid);

            }

        } else {

            func_build_quick_flags($productid);
            func_build_quick_prices($productid);

        }

    }

    if ($refresh) {

        $added = '';

        if (!empty($classid))
            $added = "&classid=$classid";

        func_refresh('options', $added);

    }

} // if ($REQUEST_METHOD == 'POST')

// Get the product options list
$product_options     = func_get_product_classes($productid);
$product_options_ex = func_get_product_exceptions($productid);
$product_options_js = func_get_product_js_code($productid);

if (
    $product_options
    && !empty($classids)
) {

    foreach ($product_options as $k => $v) {

        if ($classids[$v['classid']])
            $product_options[$k]['multi'] = 'Y';

    }

}

if (
    !empty($classid)
    && $product_options
) {

    foreach ($product_options as $v) {

        if ($v['classid'] == $classid) {

            $product_option = $v;

            break;

        }

    }

}

$has_variants_n_ex = false;

if (
    !empty($product_options)
    && !empty($product_options_ex)
) {

    $options = array();

    foreach($product_options as $c) {

        if ($c['is_modifier'] == '')
            $has_variants_n_ex = true;

        if ($c['avail'] != 'Y')
            continue;

        if (
            $c['is_modifier'] == 'T'
            || $c['is_modifier'] == 'A'
        ) {

            $options[$c['classid']] = '';

        } elseif (!empty($c['options'])) {

            foreach ($c['options'] as $oid => $o) {

                if ($o['avail'] == 'Y') {

                    $options[$c['classid']] = $oid;

                    break;

                }

            }

        }

    } // foreach($product_options as $c)

    if (
        $has_variants_n_ex
         && !func_get_default_variantid($productid)
    ) {

        $smarty->assign('def_options_failure', 'all_is_ex');

    } elseif (
        !empty($options)
        && !func_check_product_options($productid, $options)
    ) {

        $smarty->assign('def_options_failure', 'def_is_ex');

    }

}

/**
 * Assign the Smarty variables
 */
if (!empty($product_options))
    $smarty->assign('product_options',         $product_options);

if (!empty($product_options_ex))
    $smarty->assign('product_options_ex',     $product_options_ex);

if (!empty($product_options_js))
    $smarty->assign('product_options_js',     $product_options_js);

if (!empty($product_option)) {

    if (empty($product_option['options']))
        func_unset($product_option, 'options');

    $smarty->assign('product_option',         $product_option);

}

if (!empty($product_option_lng))
    $smarty->assign('product_option_lng',     $product_option_lng);

$smarty->assign('edit_language',             $edit_language);

$smarty->assign('submode',                     $submode);

?>
