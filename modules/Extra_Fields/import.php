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
 * Import/export of the product extra-fields
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import.php,v 1.24.2.1 2011/01/10 13:11:56 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/******************************************************************************
Used cache format:

Extra fields (by Service names):
    data_type:  EN
    key:        <Service name>
    value:        [<Field ID> | RESERVED]
Extra fields (by Field ID):
    data_type:  EI
    key:        <Field ID>
    value:        [<Field ID> | RESERVED]

Note: RESERVED is used if ID is unknown
******************************************************************************/

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

$provider_condition = ($single_mode ? '' : " AND provider='".$import_data_provider."'");

if ($import_step == 'process_row') {
/**
 * PROCESS ROW from import file
 */

    $_fieldid = false;
    // Check field ID
    if (!empty($values['fieldid'])) {
        $_fieldid = func_import_get_cache('EI', $values['fieldid']);
        if (!is_null($_fieldid) && $action != 'do') {
            func_import_error('msg_err_import_log_message_39', array('fieldid' => $values['fieldid']));
            return false;
        } elseif (empty($_fieldid)) {
            $_fieldid = func_query_first_cell("SELECT fieldid FROM $sql_tbl[extra_fields] WHERE fieldid = '$values[fieldid]'".$provider_condition);
            if (!empty($_fieldid))
                func_import_save_cache('EI', $values['fieldid'], $_fieldid);
        }
    }

    // Check service name and detect field ID
    $_sname = func_import_get_cache('EN', $values['service_name']);
    if (is_null($_sname)) {
         func_import_save_cache('EN', $values['service_name']);
    } elseif (!is_null($_sname) && $action != 'do') {
        func_import_error('msg_err_import_log_message_38', array('sname' => $values['service_name']));
        return false;
    } elseif (empty($_fieldid)) {
        $_fieldid = func_query_first_cell("SELECT fieldid FROM $sql_tbl[extra_fields] WHERE service_name = '".addslashes($values['service_name'])."'".$provider_condition);
        if (!empty($_fieldid))
            func_import_save_cache('EN', $values['service_name'], $_fieldid);
    }

    // Check service name
    if (($res = func_ef_check_service_name($values['service_name'], $import_data_provider ? $import_data_provider : $logged_userid, $values['fieldid'])) !== true) {
        switch ($res) {
            case 'format':
                func_import_error('msg_err_import_log_message_36', array('sname' => $values['service_name']));
                return false;

            case 'name':
                func_import_error('msg_err_import_log_message_37', array('sname' => $values['service_name']));
                return false;
        }
    }

    $data_row[] = $values;

}
elseif ($import_step == 'finalize') {
/**
 * FINALIZE rows processing: update database
 */

    if ($import_file['drop'][strtolower($section)] == 'Y') {
        if ($provider_condition) {

            // Delete data by provider
            $ids = db_query("SELECT fieldid FROM $sql_tbl[extra_fields] WHERE 1 ".$provider_condition);
            if ($ids) {
                while ($value = db_fetch_array($ids)) {
                    db_query("DELETE FROM $sql_tbl[extra_field_values] WHERE fieldid = '$value[fieldid]'");
                    db_query("DELETE FROM $sql_tbl[extra_fields_lng] WHERE fieldid = '$value[fieldid]'");
                }
                db_free_result($ids);
                db_query("DELETE FROM $sql_tbl[extra_fields] WHERE 1 ".$provider_condition);
            }
        }
        else {

            // Delete all data
            db_query("DELETE FROM $sql_tbl[extra_field_values]");
            db_query("DELETE FROM $sql_tbl[extra_fields_lng]");
            db_query("DELETE FROM $sql_tbl[extra_fields]");
        }

        $import_file['drop'][strtolower($section)] = '';
    }

    foreach ($data_row as $row) {

        // Import ...

        $is_default_field_name = false;
        $field = false;
        foreach ($row['code'] as $k => $v) {
            if ($v == $config['default_admin_language']) {
                $field = $row['field'][$k];
                $is_default_field_name = true;
                break;
            }
        }

        if (empty($field) && !empty($row['field'])) {
            reset($row['field']);
            list($tmp, $field) = each($row['field']);
            reset($row['field']);
        }
        if (empty($field))
            $field = $row['service_name'];

        $data = array(
            'value'            => addslashes($row['default']),
            'service_name'    => addslashes($row['service_name']),
            'field'            => addslashes($field)
        );

        if (isset($row['active']))
            $data['active'] = $row['active'];
        if (isset($row['orderby']))
            $data['orderby'] = $row['orderby'];

        // Get saved fieldid
        $is_old = $_fieldid = false;
        if (!empty($row['fieldid'])) {
            $_fieldid = func_import_get_cache('EI', $row['fieldid']);
        }
        if (empty($_fieldid)) {
            $_fieldid = func_import_get_cache('EN', $row['service_name']);
        }
        if (!empty($_fieldid)) {
            $data['fieldid'] = $_fieldid;
            $is_old = (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[extra_fields] WHERE fieldid = '$_fieldid'") > 0);
        }

        if ($is_old) {
            if (!$is_default_field_name)
                func_unset($data, 'field');
            func_array2update('extra_fields', $data, "fieldid = '$data[fieldid]'");
        } else {
            $data['provider'] = $import_data_provider;
            if (!isset($row['orderby']))
                $data['orderby'] = func_query_first_cell("SELECT MAX(orderby) FROM $sql_tbl[extra_fields] WHERE 1".$provider_condition)+1;
            $_fieldid = func_array2insert('extra_fields', $data, true);
        }

        if (empty($_fieldid))
            continue;

        // Import multilanguage field names
        foreach ($row['code'] as $k => $v) {
            if (empty($row['field'][$k]))
                continue;
            $data = array(
                'fieldid' => $_fieldid,
                'code' => $v,
                'field' => addslashes($row['field'][$k])
            );

            func_array2insert('extra_fields_lng', $data, true);
        }

        if ($is_old) {
            $result[strtolower($section)]['updated']++;
        } else {
            $result[strtolower($section)]['added']++;
        }

        func_import_save_cache('EN', $row['service_name'], $_fieldid);

        func_flush(". ");

    }

// Export data
} elseif ($import_step == 'export') {

    while ($id = func_export_get_row($data)) {
        if (empty($id))
            continue;

        // Get data
        $row = func_query_first("SELECT $sql_tbl[extra_fields].*, $sql_tbl[extra_fields_lng].code, IF($sql_tbl[extra_fields_lng].field != '', $sql_tbl[extra_fields_lng].field, $sql_tbl[extra_fields].field) as field FROM $sql_tbl[extra_fields], $sql_tbl[extra_fields_lng] WHERE $sql_tbl[extra_fields].fieldid = $sql_tbl[extra_fields_lng].fieldid AND $sql_tbl[extra_fields_lng].code = '$current_code' AND $sql_tbl[extra_fields].fieldid = '$id'".(empty($provider_sql) ? "" : " AND provider = '$provider_sql'"));
        if (!$row)
            continue;

        $row = func_export_rename_cell($row, array('value' => 'default'));

        // Write row
        if (!func_export_write_row($row))
            break;

    }

}

?>
