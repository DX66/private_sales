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
 * Import/export configurations related data
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import.php,v 1.25.2.1 2011/01/10 13:11:59 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

/******************************************************************************
Used cache format:
Product types:
    data_type:     PT
    key:        <Product type>
    value:        [<Product type ID> | RESERVED]
Product type specifications:
    data_type:     TS
    key:        <Product type>EOL<Specification name>
    value:        [<Specification ID> | RESERVED]

Note: RESERVED is used if ID is unknown
    EOL - End-of_line symbol (\n)
******************************************************************************/

if ($import_step == 'process_row') {

    // PROCESS ROW from import file

    func_import_save_cache('PT', $values['type']);

    if (!empty($values['specification'])) {

        foreach ($values['specification'] as $v) {

            if (empty($v))
                continue;

            func_import_save_cache('TS', $values['type']."\n".$v);

        }

    }

    $data_row[] = $values;

} elseif ($import_step == 'finalize') {

    // FINALIZE rows processing: update database

    if ($import_file['drop'][strtolower($section)] == 'Y') {

        if ($provider_condition) {

            // Search for products created by provider...
            $ptypes = db_query("SELECT ptypeid FROM $sql_tbl[pconf_product_types] WHERE provider = '".addslashes($import_data_provider)."'");

            db_query("DELETE FROM $sql_tbl[pconf_product_types] WHERE provider = '".addslashes($import_data_provider)."'");

            if ($ptypes) {

                while ($ptypeid = db_fetch_array($ptypes)) {

                    $ptypeid = $ptypeid['ptypeid'];

                    $specs = func_query_column("SELECT specid FROM $sql_tbl[pconf_specifications] WHERE ptypeid = '$ptypeid'");

                    db_query("DELETE FROM $sql_tbl[pconf_specifications] WHERE ptypeid = '$ptypeid'");
                    db_query("DELETE FROM $sql_tbl[pconf_products_classes] WHERE ptypeid = '$ptypeid'");
                    db_query("DELETE FROM $sql_tbl[pconf_class_requirements] WHERE ptypeid = '$ptypeid'");

                    if (!empty($specs)) {

                        db_query("DELETE FROM $sql_tbl[pconf_class_specifications] WHERE specid IN ('" . implode("','", $specs) . "')");

                    }

                }

            }

        } else {

            db_query("DELETE FROM $sql_tbl[pconf_product_types]");
            db_query("DELETE FROM $sql_tbl[pconf_specifications]");
            db_query("DELETE FROM $sql_tbl[pconf_products_classes]");
            db_query("DELETE FROM $sql_tbl[pconf_class_requirements]");
            db_query("DELETE FROM $sql_tbl[pconf_class_specifications]");

        }

        $import_file['drop'][strtolower($section)] = '';

    }

    // Import data...

    foreach ($data_row as $row) {

        // Import product configurator types
        $_ptypeid = false;

        if (!empty($row['typeid'])) {

            $_ptypeid = func_query_first_cell("SELECT ptypeid FROM $sql_tbl[pconf_product_types] WHERE ptypeid = '$row[typeid]' AND provider = '".addslashes($import_data_provider)."'");

        } else {

            $_ptypeid = func_query_first_cell("SELECT ptypeid FROM $sql_tbl[pconf_product_types] WHERE ptype_name = '".addslashes($row['type'])."' AND provider = '".addslashes($import_data_provider)."'");

        }

        $data = array(
            'provider'         => $import_data_provider,
            'ptype_name'     => $row['type'],
            'orderby'         => $row['orderby'],
        );

        $data = func_addslashes($data);

        // Add type
        if (empty($_ptypeid)) {

            if (
                !empty($row['typeid'])
                && func_query_first_cell("SELECT ptypeid FROM $sql_tbl[pconf_product_types] WHERE ptypeid = '$row[typeid]'") == 0
            ) {
                $data['ptypeid'] = $row['typeid'];
            }

            $_ptypeid = func_array2insert(
                'pconf_product_types',
                $data
            );

            $result[strtolower($section)]['added']++;

        } else {
            // Update type

            func_array2update(
                'pconf_product_types',
                $data,
                "ptypeid = '$_ptypeid'"
            );

            $result[strtolower($section)]['updated']++;

        }

        if (!empty($_ptypeid))
            func_import_save_cache('PT', $row['type'], $_ptypeid);

        // Import specifications
        if (
            $_ptypeid
            && !empty($row['specification'])
        ) {

            foreach ($row['specification'] as $k => $v) {

                if (empty($v))
                    continue;

                $_specid = false;

                if (!empty($row['specid'][$k])) {
                    $_specid = func_query_first_cell("SELECT specid FROM $sql_tbl[pconf_specifications] WHERE specid = '".$row['specid'][$k]."' AND ptypeid = '$_ptypeid'");
                }

                if (empty($_specid)) {
                    $_specid = func_query_first_cell("SELECT specid FROM $sql_tbl[pconf_specifications] WHERE spec_name = '".addslashes($v)."' AND ptypeid = '$_ptypeid'");
                }

                $data = array(
                    'ptypeid'    => $_ptypeid,
                    'spec_name'    => $v,
                    'orderby'    => $row['spec_orderby'][$k],
                );

                $data = func_addslashes($data);

                // Add specification
                if (empty($_specid)) {

                    if (
                        !empty($row['specid'][$k])
                        && func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[pconf_specifications] WHERE specid = '" . $row['specid'][$k] . "'") == 0
                    ) {
                        $data['specid'] = $row['specid'][$k];
                    }

                    $_specid = func_array2insert('pconf_specifications', $data);

                } else {

                    // Update specification
                    func_array2update(
                        'pconf_specifications',
                        $data,
                        "specid = '$_specid'"
                    );

                }

                if (!empty($_specid)) {

                    func_import_save_cache('TS', $row['type'] . "\n" . $v, $_specid);

                }

            } // foreach ($row['specification'] as $k => $v)

        }

        echo ". ";
        func_flush();

    } // foreach ($data_row as $row)

} elseif ($import_step == 'export') {
    // Export data

    while ($id = func_export_get_row($data)) {

        if (empty($id))
            continue;

        // Get data
        $row = func_query_first("SELECT * FROM $sql_tbl[pconf_product_types] WHERE ptypeid = '$id'" . (empty($provider_sql) ? "" : " AND provider = '$provider_sql'"));

        if (!$row)
            continue;

        $row = func_export_rename_cell(
            $row,
            array(
                'ptype_name' => 'type',
                'ptypeid' => 'typeid',
            )
        );

        func_unset($row, 'ptypeid', 'provider');

        // Get product type specifications
        $specs = func_query("SELECT * FROM $sql_tbl[pconf_specifications] WHERE ptypeid = '$id'");

        if (!empty($specs)) {

            foreach ($specs as $v) {

                $row['specid'][]         = $v['specid'];
                $row['specification'][] = $v['spec_name'];
                $row['spec_orderby'][]     = $v['orderby'];

            }

        }

        // Write row
        if (!func_export_write_row($row))
            break;

    } // while ($id = func_export_get_row($data))

}

?>
