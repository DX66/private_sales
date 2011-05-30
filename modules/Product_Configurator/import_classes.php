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
 * Import/export classifications
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import_classes.php,v 1.31.2.1 2011/01/10 13:11:59 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

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
Product types:
    data_type:     PT
    key:        <Product type>
    value:        [<Product type ID> | RESERVED]
Product type specifications:
    data_type:     TS
    key:        <Product type>EOL<Specification name>
    value:        [<Specification ID> | RESERVED]
Product classes:
    data_type:     Pc
    key:        <Product class ID>
    value:        [<Product class ID> | RESERVED]

Note: RESERVED is used if ID is unknown
    EOL - End-of_line symbol (\n)
******************************************************************************/

if ($import_step == 'process_row') {

    // PROCESS ROW from import file

    // Check classid
    if (!empty($values['classid'])) {

        $_classid = func_import_get_cache('Pc', $values['classid']);

        if (is_null($_classid)) {

            func_import_save_cache('Pc', $values['classid']);

        }

    }

    // Check productid / productcode / product
    list(
        $_productid,
        $_variantid
    ) = func_import_detect_product($values);

    if (
        is_null($_productid)
        || (
            $action == 'do'
            && empty($_productid)
        )
    ) {
        func_import_module_error('msg_err_import_log_message_14');

        return false;
    }

    $values['productid'] = $_productid;

    // Check product type
    $_ptypeid = func_import_get_cache('PT', $values['type']);

    if (is_null($_ptypeid)) {

        $_ptypeid = func_query_first_cell("SELECT ptypeid FROM $sql_tbl[pconf_product_types] WHERE ptype_name = '".addslashes($values['type'])."' AND provider = '".addslashes($import_data_provider)."'");

        if (empty($_ptypeid)) {

            $_ptypeid = NULL;

        } else {

            func_import_save_cache('PT', $values['type'], $_ptypeid);

        }

    } // if (is_null($_ptypeid))

    if (
        is_null($_ptypeid)
        || (
            $action == 'do'
            && empty($_ptypeid)
        )
    ) {
        func_import_module_error('msg_err_import_log_message_22', array('type' => $values['type']));

        return false;
    }

    $values['ptypeid'] = $_ptypeid;

    // Check specifications array
    $values['specids'] = array();

    if (!empty($values['specifications'])) {

        foreach ($values['specifications'] as $v) {

            if (empty($v))
                continue;

            $_specid = func_import_get_cache('TS', $values['type']."\n".$v);

            if (
                is_null($_specid)
                && !empty($_ptypeid)
            ) {

                $_specid = func_query_first_cell("SELECT specid FROM $sql_tbl[pconf_specifications] WHERE ptypeid = '$_ptypeid' AND spec_name = '".addslashes($v)."'");
                if (empty($_specid)) {

                    $_specid = NULL;

                } elseif ($action == 'do') {

                    func_import_save_cache('TS', $values['type'] . "\n" . $v, $_specid);

                }

            }

            if (
                is_null($_specid)
                || (
                    $action == 'do'
                    && empty($_specid)
                )
            ) {
                func_import_module_error('msg_err_import_log_message_23', array('spec' => $v));

                return false;
            }

            $values['specids'][] = $_specid;

        } // foreach ($values['specifications'] as $v)

    } // if (!empty($values['specifications']))

    // Check requirements array
    $values['require'] = array();

    if (!empty($values['required_types'])) {

        foreach ($values['required_types'] as $k => $v) {

            if (empty($v))
                continue;

            // Check required type
            $_ptypeid = func_import_get_cache('PT', $v);

            if (is_null($_ptypeid)) {

                $_ptypeid = func_query_first_cell("SELECT ptypeid FROM $sql_tbl[pconf_product_types] WHERE ptype_name = '".addslashes($v)."' AND provider = '".addslashes($import_data_provider)."'");

                if (empty($_ptypeid)) {

                    $_ptypeid = NULL;

                } else {

                    func_import_save_cache('PT', $v, $_ptypeid);

                }

            } // if (is_null($_ptypeid))

            if (
                is_null($_ptypeid)
                || (
                    $action == 'do'
                    && empty($_ptypeid)
                )
            ) {
                func_import_module_error('msg_err_import_log_message_22', array('type' => $v));

                return false;
            }

            // Check required specifications
            $s = $values['required_specs'][$k];

            $_specid = 0;

            if (
                !empty($s)
                && !empty($_ptypeid)
            ) {

                $_specid = func_import_get_cache('TS', $v . "\n" . $s);

                if (is_null($_specid)) {

                    $_specid = func_query_first_cell("SELECT specid FROM $sql_tbl[pconf_specifications] WHERE ptypeid = '$_ptypeid' AND spec_name = '".addslashes($s)."'");

                    if (empty($_specid)) {

                        $_specid = NULL;

                    } elseif ($action == 'do') {

                        func_import_save_cache('TS', $v . "\n" . $s, $_specid);

                    }

                } // if (is_null($_specid))

                if (
                    is_null($_specid)
                    || (
                        $action == 'do'
                        && empty($_specid)
                    )
                ) {
                    func_import_module_error('msg_err_import_log_message_23', array('spec' => $s));

                    return false;
                }

            }

            if (!isset($values['require'][$_ptypeid]))
                $values['require'][$_ptypeid] = array();

            if (!empty($_specid))
                $values['require'][$_ptypeid][] = $_specid;

        } // foreach ($values['required_types'] as $k => $v)

    } // if (!empty($values['required_types']))

    $data_row[] = $values;

} elseif ($import_step == 'finalize') {

    // FINALIZE rows processing: update database

    // Drop old data
    if ($import_file['drop'][strtolower($section)] == 'Y') {

        // Delete data by provider
        if ($provider_condition) {

            $ptypes = db_query("SELECT ptypeid FROM $sql_tbl[pconf_product_types] WHERE provider = '".addslashes($import_data_provider)."'");

            if ($ptypes) {

                while ($ptypeid = db_fetch_array($ptypes)) {

                    $ptypeid = $ptypeid['ptypeid'];

                    $specs = func_query_column("SELECT specid FROM $sql_tbl[pconf_specifications] WHERE ptypeid = '$ptypeid'");

                    db_query("DELETE FROM $sql_tbl[pconf_products_classes] WHERE ptypeid = '$ptypeid'");
                    db_query("DELETE FROM $sql_tbl[pconf_class_requirements] WHERE ptypeid = '$ptypeid'");

                    if (!empty($specs)) {
                        db_query("DELETE FROM $sql_tbl[pconf_class_specifications] WHERE specid IN ('".implode("','", $specs)."')");
                    }

                }

            }

        } else {

            // Delete all old data
            db_query("DELETE FROM $sql_tbl[pconf_products_classes]");
            db_query("DELETE FROM $sql_tbl[pconf_class_requirements]");
            db_query("DELETE FROM $sql_tbl[pconf_class_specifications]");

        }

        $import_file['drop'][strtolower($section)] = '';

    } // if ($import_file['drop'][strtolower($section)] == 'Y')

    // Import data...

    foreach ($data_row as $row) {
        // Import product configurator classes

        // Detect classid
        $_classid = func_query_first_cell("SELECT $sql_tbl[pconf_products_classes].classid FROM $sql_tbl[pconf_products_classes], $sql_tbl[products], $sql_tbl[pconf_product_types] WHERE $sql_tbl[products].provider = '".addslashes($import_data_provider)."' AND $sql_tbl[pconf_product_types].provider = '".addslashes($import_data_provider)."' AND $sql_tbl[pconf_product_types].ptypeid = $sql_tbl[pconf_products_classes].ptypeid AND $sql_tbl[products].productid = $sql_tbl[pconf_products_classes].productid AND $sql_tbl[pconf_products_classes].classid = '$row[classid]'");

        if (empty($_classid)) {
            $_classid = func_query_first_cell("SELECT $sql_tbl[pconf_products_classes].classid FROM $sql_tbl[pconf_products_classes], $sql_tbl[products], $sql_tbl[pconf_product_types] WHERE $sql_tbl[products].productid = '$row[productid]' AND $sql_tbl[pconf_product_types].ptypeid = '$row[ptypeid]' AND $sql_tbl[products].provider = '".addslashes($import_data_provider)."' AND $sql_tbl[pconf_product_types].provider = '".addslashes($import_data_provider)."' AND $sql_tbl[pconf_product_types].ptypeid = $sql_tbl[pconf_products_classes].ptypeid AND $sql_tbl[products].productid = $sql_tbl[pconf_products_classes].productid");
        }

        // Add class
        if (empty($_classid)) {

            $data = array(
                'productid'    => $row['productid'],
                'ptypeid'    => $row['ptypeid'],
            );

            if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[pconf_products_classes] WHERE classid = '$row[classid]'") == 0) {
                $data['classid'] = $row['classid'];
            }

            $_classid = func_array2insert(
                'pconf_products_classes',
                $data,
                true
            );

            $result[strtolower($section)]['added']++;

        } else {

            $result[strtolower($section)]['updated']++;

        }

        if (empty($_classid))
            continue;

        func_import_save_cache('Pc', $row['classid'], $_classid);

        // Delete class old data
        db_query("DELETE FROM $sql_tbl[pconf_class_requirements] WHERE classid = '$_classid'");
        db_query("DELETE FROM $sql_tbl[pconf_class_specifications] WHERE classid  = '$_classid'");

        // Import specifications
        if (!empty($row['specids'])) {

            foreach ($row['specids'] as $v) {

                $data = array(
                    'classid'    => $_classid,
                    'specid'    => $v,
                );

                func_array2insert(
                    'pconf_class_specifications',
                    $data,
                    true
                );

            }

        }

        // Import requirements (types)
        if (!empty($row['require'])) {

            foreach ($row['require'] as $k => $specs) {

                $data = array(
                    'classid' => $_classid,
                    'ptypeid' => $k,
                );

                func_array2insert(
                    'pconf_class_requirements',
                    $data,
                    true
                );

                // Import requirements (specifications)
                if (!empty($specs)) {

                    $specs = array_unique($specs);

                    foreach ($specs as $v) {

                        if (empty($v))
                            continue;

                        $data = array(
                            'classid'    => $_classid,
                            'ptypeid'    => $k,
                            'specid'    => $v,
                        );

                        func_array2insert(
                            'pconf_class_requirements',
                            $data,
                            true
                        );

                    }

                } // if (!empty($specs))

            } // foreach ($row['require'] as $k => $specs)

        } // if (!empty($row['require']))

        echo ". ";
        func_flush();

    } // foreach ($data_row as $row)

} elseif ($import_step == 'export') {
    // Export data

    while ($id = func_export_get_row($data)) {

        if (empty($id))
            continue;

        $_classes = func_query("SELECT $sql_tbl[pconf_products_classes].*, $sql_tbl[pconf_product_types].ptype_name as type FROM $sql_tbl[pconf_products_classes], $sql_tbl[products], $sql_tbl[pconf_product_types] WHERE $sql_tbl[pconf_products_classes].productid = $sql_tbl[products].productid AND $sql_tbl[pconf_product_types].ptypeid = $sql_tbl[pconf_products_classes].ptypeid AND $sql_tbl[pconf_products_classes].productid = '$id'".(empty($provider_sql) ? "" : " AND $sql_tbl[products].provider = '$provider_sql'"));

        if (empty($_classes))
            continue;

        foreach ($_classes as $row) {

            $p_row = func_export_get_product($row['productid']);

            if (empty($p_row))
                continue;

            $row = func_array_merge($row, $p_row);

            // Export specifications
            $specs = func_query_column("SELECT $sql_tbl[pconf_specifications].spec_name FROM $sql_tbl[pconf_class_specifications], $sql_tbl[pconf_specifications] WHERE $sql_tbl[pconf_class_specifications].specid = $sql_tbl[pconf_specifications].specid AND $sql_tbl[pconf_class_specifications].classid = '$row[classid]'");

            if (!empty($specs)) {

                foreach ($specs as $v) {

                    $row['specifications'][] = $v;

                }

            }

            // Export required types
            $req = func_query("SELECT $sql_tbl[pconf_product_types].ptype_name, $sql_tbl[pconf_product_types].ptypeid, $sql_tbl[pconf_class_requirements].specid FROM $sql_tbl[pconf_class_requirements], $sql_tbl[pconf_product_types] WHERE $sql_tbl[pconf_class_requirements].classid = '$row[classid]' AND $sql_tbl[pconf_class_requirements].ptypeid = $sql_tbl[pconf_product_types].ptypeid");

            if (!empty($req)) {

                foreach ($req as $v) {

                    $row['required_types'][] = $v['ptype_name'];
                    $row['required_specs'][] = func_query_first_cell("SELECT spec_name FROM $sql_tbl[pconf_specifications] WHERE specid = '$v[specid]' AND ptypeid = '$v[ptypeid]'");

                }

            }

            if (!func_export_write_row($row))
                break;

        } // foreach ($_classes as $row)

    } // while ($id = func_export_get_row($data))

}

?>
