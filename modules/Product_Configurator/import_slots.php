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
 * Import/export slots data
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import_slots.php,v 1.32.2.1 2011/01/10 13:11:59 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/******************************************************************************
Used cache format:
Products (by Product ID):
    data_type:     PI
    key:        <Product ID>
    value:        [<Product code> | RESERVED]
Products (by Product code):
    data_type:     PR
    key:        <Product code>
    value:        [<Product ID> | RESERVED]
Products (by Product name):
    data_type:  PN
    key:        <Product name>
    value:        [<Product ID> | RESERVED]
Wizard steps (by Product ID / Product code / Product name):
    data_type:  St
    key:        <Product ID / Product code / Product name><EOL><Step ID>
    value:      [<Step ID> | RESERVED]
Wizard steps (by Step ID):
    data_type:     Si
    key:        <Step ID>
    value:        <Step ID>
Wizards step slots:
    data_type:  Sl
    key:        <Slot ID>
    value:      <Slot ID>
Memberships:
    data_type:     M
    key:        <Membership name>
    value:        <Membership ID>
Product types:
    data_type:     PT
    key:        <Product type>
    value:        [<Product type ID> | RESERVED]

Note: RESERVED is used if ID is unknown
    EOL - End-of_line symbol (\n)
******************************************************************************/

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if ($import_step == 'process_row') {

    // PROCESS ROW from import file

    // Check stepid
    $_stepid = func_import_get_cache('Si', $values['stepid']);
    if (is_null($_stepid) && $import_file['drop']['product_configurator_steps'] != 'Y') {
        $_stepid = func_query_first_cell("SELECT $sql_tbl[pconf_wizards].stepid FROM $sql_tbl[pconf_wizards], $sql_tbl[products] WHERE $sql_tbl[pconf_wizards].productid = $sql_tbl[products].productid AND $sql_tbl[products].provider = '".addslashes($import_data_provider)."' AND $sql_tbl[pconf_wizards].stepid = '$values[stepid]'");
        if (empty($_stepid)) {
            $_stepid = NULL;

        } else {
            func_import_save_cache('Si', $values['stepid'], $_stepid);
        }
    }

    // Check productid / productcode / product & step name
    if (empty($_stepid) && (!empty($values['productid']) || !empty($values['productcode']) || !empty($values['product']))) {
        $_stepid = func_import_get_pb_cache($values, 'St', $values['stepid']);
        if (is_null($_stepid) && $import_file['drop']['product_configurator_steps'] != 'Y') {
            $_stepid = func_query_first_cell("SELECT $sql_tbl[pconf_wizards].stepid FROM $sql_tbl[pconf_wizards], $sql_tbl[products] WHERE $sql_tbl[pconf_wizards].productid = $sql_tbl[products].productid AND $sql_tbl[pconf_wizards].step_name = '".addslashes($values['step'])."' AND ($sql_tbl[products].productid = '$values[productid]' OR $sql_tbl[products].productid = '".addslashes($values['productcode'])."' OR $sql_tbl[products].productid = '".addslashes($values['product'])."')".$provider_condition);
            if (empty($_stepid)) {
                $_stepid = NULL;

            } else {
                func_import_save_pb_cache($values, 'St', $values['stepid'], $_stepid);
            }
        }
    }

    if (is_null($_stepid) || ($action == 'do' && empty($_stepid))) {
        func_import_module_error('msg_err_import_log_message_24');
        return false;
    }

    $values['stepid'] = $_stepid;

    // Check membership
    if (!empty($values['membership'])) {
        foreach ($values['membership'] as $k => $v) {
            if (empty($v))
                continue;

            $_membershipid = func_import_get_cache('M', $v);
            if (empty($_membershipid)) {
                $_membershipid = func_detect_membership($v, 'C');
                if ($_membershipid == 0) {
                    // Membership is specified but does not exist
                    func_import_module_error('msg_err_import_log_message_5', array('membership'=>$v));
                }
                else {
                    func_import_save_cache('M', $v, $_membershipid);
                }
            }

            if (!empty($_membershipid))
                $values['membershipid'][$k] = $_membershipid;
        }
    }

    // Check rules
    if (!empty($values['rule_types'])) {
        if (!is_array($values['rule_types']))
            $values['rule_types'] = array($values['rule_types']);

        foreach ($values['rule_types'] as $k => $v) {
            if (empty($v)) {
                unset($values['rule_types'][$k]);
                continue;
            }

            $_ptypeid = func_import_get_cache('PT', $v);
            if (is_null($_ptypeid)) {
                $_ptypeid = func_query_first_cell("SELECT ptypeid FROM $sql_tbl[pconf_product_types] WHERE ptype_name = '".addslashes($v)."' AND provider = '".addslashes($import_data_provider)."'");
                if (empty($_ptypeid)) {
                    $_ptypeid = NULL;
                }
                else {
                    func_import_save_cache('PT', $v, $_ptypeid);
                }

                if (is_null($_ptypeid) || ($action == 'do' && empty($_ptypeid))) {
                    func_import_module_error('msg_err_import_log_message_22', array('type' => $values['type']));
                    unset($values['rule_types'][$k]);
                    continue;
                }
            }

            $values['rule_types'][$k] = $_ptypeid;
        }
    }

    // Check default productid / productcode / product
    if (!empty($values['default_productid'][$x]) || !empty($values['default_product'][$x]) || !empty($values['default_productcode'][$x])) {

        $tmp = array(
            'productid'     => $values['default_productid'],
            'product'       => $values['default_product'],
            'productcode'   => $values['default_productcode']
        );

        list($_productid, $_variantid) = func_import_detect_product($tmp);
        if (is_null($_productid) || ($action == 'do' && empty($_productid))) {
            func_import_module_error('msg_err_import_log_message_14');
            return false;
        }
        $values['default_productid'] = $_productid;
    }

    func_import_save_cache('Sl', $values['slotid']);

    $data_row[] = $values;

}
elseif ($import_step == 'finalize') {

    // FINALIZE rows processing: update database

    // Drop old data
    if ($import_file['drop'][strtolower($section)] == 'Y') {
        // Delete data by provider
        if ($provider_condition) {
            $products = db_query("SELECT productid FROM $sql_tbl[products] WHERE provider = '".addslashes($import_data_provider)."'");
            if ($products) {
                while ($productid = db_fetch_array($products)) {
                    $productid = $productid['productid'];
                    $stepids = func_query_column("SELECT stepid FROM $sql_tbl[pconf_wizards] WHERE productid = '$productid'");
                    if (!empty($stepids)) {
                        $slotids = func_query_column("SELECT slotid FROM $sql_tbl[pconf_slots] WHERE stepid IN ('".implode("','", $stepids)."')");
                        if (!empty($slotids)) {
                            db_query("DELETE FROM $sql_tbl[pconf_slots] WHERE slotid IN ('".implode("','", $slotids)."')");
                            db_query("DELETE FROM $sql_tbl[pconf_slot_rules] WHERE slotid IN ('".implode("','", $slotids)."')");
                            db_query("DELETE FROM $sql_tbl[pconf_slot_markups] WHERE slotid IN ('".implode("','", $slotids)."')");
                        }

                    }
                }
            }
        }
        else {
            // Delete all old data
            db_query("DELETE FROM $sql_tbl[pconf_slots]");
            db_query("DELETE FROM $sql_tbl[pconf_slot_rules]");
            db_query("DELETE FROM $sql_tbl[pconf_slot_markups]");
        }

        $import_file['drop'][strtolower($section)] = '';
    }

    // Import data...

    foreach ($data_row as $row) {
        // Import product configurator slots

        // Detect slotid
        $_slotid = func_query_first_cell("SELECT slotid FROM $sql_tbl[pconf_slots] WHERE stepid = '$row[stepid]' AND slotid = '$row[slotid]'");

        $data = func_import_define_data($row, array("stepid", "slot" => 'slot_name', 'descr' => 'slot_descr', 'orderby', 'amount_min', 'amount_max', 'default_amount', 'default_productid', 'status'));

        if (empty($_slotid)) {
            // Add slot
            if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[pconf_slots] WHERE slotid = '$row[slotid]'") == 0)
                $data['slotid'] = $row['slotid'];

            $_slotid = func_array2insert('pconf_slots', $data);
            func_array2update(
                'pconf_slots',
                array(
                    'slot_name' => $language_var_names['slot_name'].$_slotid,
                    'slot_descr' => $language_var_names['slot_descr'].$_slotid
                ),
                "slotid = '$_slotid'"
            );
            func_languages_alt_insert($language_var_names['slot_name'].$_slotid, 'Slot #'.$_slotid, $shop_language);
            func_languages_alt_insert($language_var_names['slot_descr'].$_slotid, 'Slot #'.$_slotid, $shop_language);

            $result[strtolower($section)]['added']++;

        }
        else {
            // Update slot

            db_query("DELETE FROM $sql_tbl[pconf_slot_rules] WHERE slotid = '$_slotid'");

            func_array2update('pconf_slots', $data, "slotid = '$_slotid'");
            $result[strtolower($section)]['updated']++;
        }

        if (empty($_slotid))
            continue;

        // Import slot rules
        if (!empty($row['rule_types'])) {
            $last_index = func_query_first_cell("SELECT MAX(index_by_and) FROM $sql_tbl[pconf_slot_rules] WHERE slotid = '$_slotid'")+1;
            foreach ($row['rule_types'] as $v) {
                if (empty($v))
                    continue;

                $data = array(
                    'slotid'    => $_slotid,
                    'ptypeid'    => $v,
                    'index_by_and'    => $last_index++
                );
                func_array2insert('pconf_slot_rules', $data);
            }
        }

        // Import slot markups
        if (!empty($row['markup'])) {
            // Delete old data
            db_query("DELETE FROM $sql_tbl[pconf_slot_markups] WHERE slotid = '$_slotid'");

            if (!is_array($row['markup']))
                $row['markup'] = array($row['markup']);

            if (!is_array($row['markup_type']))
                $row['markup_type'] = array($row['markup_type']);

            foreach ($row['markup'] as $k => $v) {
                if (empty($v) || func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[pconf_slot_markups] WHERE slotid = '$_slotid' AND membershipid = '".$row['membershipid'][$k]."'"))
                    continue;

                $data = array(
                    'slotid'        => $_slotid,
                    'markup'        => $v,
                    'membershipid'    => $row['membershipid'][$k]
                );
                if (!empty($row['markup_type'][$k]))
                    $data['markup_type'] = $row['markup_type'][$k];

                func_array2insert('pconf_slot_markups', $data);
            }
        }

        func_import_save_cache('Sl', $row['slotid'], $_slotid);

        func_flush(". ");
    }
}
elseif ($import_step == 'export') {
    // Export data

    while (($id = func_export_get_row($data)) !== false) {
        if (empty($id))
            continue;

        // Get data
        $mrow = func_query("SELECT $sql_tbl[pconf_slots].*, $sql_tbl[pconf_wizards].step_name as step, $sql_tbl[pconf_wizards].productid FROM $sql_tbl[pconf_slots], $sql_tbl[pconf_wizards], $sql_tbl[products] WHERE $sql_tbl[pconf_slots].stepid = $sql_tbl[pconf_wizards].stepid AND $sql_tbl[pconf_wizards].productid = $sql_tbl[products].productid AND $sql_tbl[pconf_wizards].productid = '$id'".(empty($provider_sql) ? "" : " AND $sql_tbl[products].provider = '$provider_sql'"));
        if (empty($mrow))
            continue;

        // Get product signature
        $p_row = func_export_get_product($id);
        if (empty($p_row))
            continue;

        foreach ($mrow as $row) {
            $row = func_array_merge($row, $p_row);

            // Export slot markups
            $markups = func_query("SELECT $sql_tbl[pconf_slot_markups].*, $sql_tbl[memberships].membership FROM $sql_tbl[pconf_slot_markups] LEFT JOIN $sql_tbl[memberships] ON $sql_tbl[pconf_slot_markups].membershipid = $sql_tbl[memberships].membershipid WHERE $sql_tbl[pconf_slot_markups].slotid = '$row[slotid]'");
            if (!empty($markups)) {
                foreach ($markups as $v) {
                    $row['markup'][] = $v['markup'];
                    $row['markup_type'][] = $v['markup_type'];
                    $row['membership'][] = $v['membership'];
                }
            }

            // Export slot rules
            $rules = func_query_column("SELECT $sql_tbl[pconf_product_types].ptype_name FROM $sql_tbl[pconf_slot_rules], $sql_tbl[pconf_product_types] WHERE $sql_tbl[pconf_slot_rules].ptypeid = $sql_tbl[pconf_product_types].ptypeid AND $sql_tbl[pconf_slot_rules].slotid = '$row[slotid]' ORDER BY $sql_tbl[pconf_slot_rules].index_by_and");
            if (!empty($rules)) {
                $row['rule_types'] = array_values($rules);
            }

            // Export default product
            if (!empty($row['default_productid'])) {
                $default_product = func_query_first("SELECT productcode as default_productcode, product as default_product FROM $sql_tbl[products] WHERE productid='$row[default_productid]'");
                $row = func_array_merge($row, $default_product);
            }

            // Write row
            if (!func_export_write_row($row))
                break;
        }
    }
}

?>
