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
 * States import/export
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import_states.php,v 1.24.2.1 2011/01/10 13:11:49 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/******************************************************************************
Used cache format:
States:
    data_type:  ST
    key:        <Country code>_<State code>
    value:      <Country code>_<State code>
Countries (by code):
    data_type:  CN
    key:        <Country code>
    value:      <Country code>
Old state IDs:
    data_type:  oST
    key:        <Country code>_<State code>
    value:      <State ID>

Note: RESERVED is used if ID is unknown
******************************************************************************/

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if ($import_step == 'define') {
/**
 * Make default definitions (only on first inclusion!)
 */
    $import_specification['STATES'] = array(
        'script'        => '/include/import_states.php',
        'permissions'   => 'A',
        'finalize'      => true,
        'export_sql'    => "SELECT stateid FROM $sql_tbl[states]",
        'orderby'       => 10,
        'columns'       => array(
            'state'         => array(
                'required'  => true),
            'code'          => array(
                'required'  => true),
            'country'       => array(
                'required'  => true)
        )
    );

} elseif ($import_step == 'process_row') {
/**
 * PROCESS ROW from import file
 */

    $_country = func_import_get_cache('CN', $values['country']);
    if (is_null($_country)) {
        $_country = func_query_first_cell("SELECT code FROM $sql_tbl[countries] WHERE code = '".addslashes($values['country'])."'");
        if (empty($_country)) {
            $_country = NULL;
        } else {
            func_import_save_cache('CN', $_country, $_country, true);
        }
    }

    if (is_null($_country) || ($action == 'do' && empty($_country))) {
        func_import_module_error('msg_err_import_log_message_8', array('code' => $values['country']));
        return false;
    }

    $data_row[] = $values;

}
elseif ($import_step == 'finalize') {
/**
 * FINALIZE rows processing: update database
 */

    if ($import_file['drop'][strtolower($section)] == 'Y') {
        func_import_save_cache_ids('oST', "SELECT stateid, country_code, code FROM $sql_tbl[states]");
        db_query("DELETE FROM $sql_tbl[states]");
        $import_file['drop'][strtolower($section)] = '';
    }

    foreach ($data_row as $row) {

    // Import data...

        $data = array(
            'state' => addslashes($row['state']),
        );

        $_stateid = func_query_first_cell("SELECT stateid FROM $sql_tbl[states] WHERE code = '".addslashes($row['code'])."' AND country_code = '".addslashes($row['country'])."'");

        if (empty($_stateid)) {
            $_stateid = func_import_get_cache('oST', array($row['country'], $row['code']));
            if (!empty($_stateid) && func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[states] WHERE stateid = '$_stateid'") == 0)
                $data['stateid'] = $_stateid;

            $data['code'] = addslashes($row['code']);
            $data['country_code'] = addslashes($row['country']);
            $_stateid = func_array2insert('states', $data);
            $result[strtolower($section)]['added']++;
            func_import_save_cache('CSR', $row['country'], $row['country']);
        } else {
            func_array2update('states', $data, "stateid = '$_stateid'");
            $result[strtolower($section)]['updated']++;
        }

        if (!empty($_stateid)) {
            func_import_save_cache('ST', $row['country']."_".$row['code'], $row['country']."_".$row['code']);
        }

        echo ". ";
        func_flush();

    }

// Post-import step
} elseif ($import_step == 'complete') {

    $_states_ids = array();

    while (list($id, $tmp) = func_import_read_cache('CSR'))
        if (!in_array($id, $_states_ids))
            $_states_ids[] = $id;

    // Update the 'display_states' of xcart_countries table
    func_flush("<br />\n");
    func_update_country_states($_states_ids);
    unset($_states_ids);

    func_import_erase_cache('CSR');

// Export data
} elseif ($import_step == 'export') {

    while ($id = func_export_get_row($data)) {
        if (empty($id))
            continue;

        // Get data
        $row = func_query_first("SELECT * FROM $sql_tbl[states] WHERE stateid = '$id'");
        if (!$row)
            continue;

        $row = func_export_rename_cell($row, array('country_code' => 'country'));
        func_unset($row, 'stateid');

        // Write row
        if (!func_export_write_row($row))
            break;
    }
}

?>
