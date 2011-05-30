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
 * Detailed product images import/export library
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import.php,v 1.29.2.1 2011/01/10 13:11:56 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('backoffice', 'image');

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

Note: RESERVED is used if ID is unknown
******************************************************************************/

if ($import_step == 'process_row') {
/**
 * PROCESS ROW from import file
 */

    // Check productid / productcode / product
    list($_productid, $_variantid) = func_import_detect_product($values);
    if (is_null($_productid) || ($action == 'do' && empty($_productid))) {
        func_import_module_error('msg_err_import_log_message_14');
        return false;
    }
    $values['productid'] = $_productid;

    #Check key fields for image
    if (!isset($values['image'])) {
        // Image is not specified by image or imageid
        if (!isset($values['imageid']) || func_array_empty($values['imageid'])) {
            func_import_module_error('msg_err_import_log_message_56');
            return false;
        } else {
            foreach($values['imageid'] as $_imageid) {
                if (!func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[images_D] WHERE imageid='".addslashes($_imageid)."'")) {
                    #specified imageid is not found in db
                    func_import_module_error('msg_err_import_log_message_56');
                    return false;
                }
            }
        }
    } elseif (!isset($values['imageid'])) {
        $values['imageid'] = array_fill(0, count($values['image']), '');
    }

    if (is_array($values['image']) && in_array('', $values['image'])) {
        foreach ($values['image'] as $k=>$v) {
            if (empty($v) && !func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[images_D] WHERE imageid='".addslashes($values['imageid'][$k])."'")) {
                func_import_module_error('msg_err_import_log_message_56');
                return false;
            }
        }
    }

    $data_row[] = $values;

} elseif ($import_step == 'finalize') {
/**
 * FINALIZE rows processing: update database
 */

    // Drop old data
    if ($import_file['drop'][strtolower($section)] == 'Y') {

        if ($provider_condition) {

            // Delete data by provider
            $ids = db_query("SELECT productid FROM $sql_tbl[products] WHERE provider = '".addslashes($import_data_provider)."'");
            if ($ids) {
                while ($id = db_fetch_array($ids)) {
                    $id = $id['productid'];
                    func_delete_image($id, 'D');
                }
            }

        } else {
            // Delete all old data
            func_delete_images('D');
        }

        $import_file['drop'][strtolower($section)] = '';
    }

    foreach ($data_row as $row) {

        // Import data...

        foreach ($row['imageid'] as $k => $v) {

            $_imageid = func_query_first_cell("SELECT imageid FROM $sql_tbl[images_D] WHERE imageid='".addslashes($v)."'");
            $is_new = empty($_imageid);

            if (isset($row['image']) && !empty($row['image'][$k])) {
                $v = $row['image'][$k];
                if ($_imageid > 0) {
                    func_delete_image($_imageid, 'D', true);
                }

                $insert_imageid = func_import_save_image_data('D', $row['productid'], $v);

                if (empty($insert_imageid))
                    continue;

                if ($_imageid > 0 && !func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[images_D] WHERE imageid='$_imageid'"))
                    db_query("UPDATE $sql_tbl[images_D] set imageid='$_imageid' WHERE imageid='$insert_imageid'");
                else
                    $_imageid = $insert_imageid;
            }

            // Update service data
            $data = array();
            if (isset($row['alt']))
                $data['alt'] = $row['alt'][$k];
            if (isset($row['orderby']))
                $data['orderby'] = $row['orderby'][$k];

            $data = func_addslashes($data);
            if (!empty($data))
                func_array2update('images_D', $data, "imageid = '$_imageid'");

            if ($is_new) {
                $result[strtolower($section)]['added']++;
            } else {
                $result[strtolower($section)]['updated']++;
            }
        }
            func_flush(". ");
    }

// Export data
} elseif ($import_step == 'export' && $export_data['options']['export_images'] == 'Y') {

    while ($id = func_export_get_row($data)) {
        if (empty($id))
            continue;

        // Get data
        $row = func_query("SELECT $sql_tbl[images_D].imageid, $sql_tbl[images_D].alt, $sql_tbl[images_D].orderby FROM $sql_tbl[images_D], $sql_tbl[products] WHERE $sql_tbl[images_D].id = $sql_tbl[products].productid AND $sql_tbl[images_D].id = '$id'".(empty($provider_sql) ? "" : " AND $sql_tbl[products].provider = '$provider_sql'"));
        if (empty($row))
            continue;

        // Get product signature
        $p_row = func_export_get_product($id);
        if (empty($p_row))
            continue;

        foreach ($row as $v) {
            $p_row['image'][]    = $v['imageid'];
            $p_row['alt'][]        = $v['alt'];
            $p_row['orderby'][] = $v['orderby'];
            $p_row['imageid'][] = $v['imageid'];
        }

        // Write row
        if (!func_export_write_row($p_row))
            break;
    }

}

?>
