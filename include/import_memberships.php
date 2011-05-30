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
 * Import/export memberships library
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import_memberships.php,v 1.18.2.1 2011/01/10 13:11:49 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

/******************************************************************************
Used cache format:
Memberships:
    data_type:  M
    key:        <Membership name>
    value:      <Membership ID>

Note: RESERVED is used if ID is unknown
******************************************************************************/

if ($import_step == 'define') {

    $import_specification['MEMBERSHIPS'] = array(
        'script'        => '/include/import_memberships.php',
        'permissions'   => 'A',
        'export_sql'    => "SELECT membershipid FROM $sql_tbl[memberships]",
        'table'         => 'memberships',
        'key_field'     => 'membershipid',
        'orderby'       => 5,
        'columns'       => array(
            'membershipid'  => array(
                'type'      => 'N'),
            'membership'    => array(
                'required'  => true),
            'orderby'       => array(
                'type'      => 'N'),
            'area'          => array(
                'type'      => 'E',
                'variants'  => array('C','A','P','B'),
                'default'   => 'C'),
            'flag'          => array(
                'type'      => 'E',
                'variants'  => array('FS', 'RP'))
        )
    );
}
elseif ($import_step == 'process_row') {

    // PROCESS ROW from import file

    // Check membership
    $tmp = func_import_get_cache('M', $values['membership']);
    if (is_null($tmp))
        func_import_save_cache('M', $values['membership'], $values['membershipid'] ? $values['membershipid'] : NULL);

    $data_row[] = $values;
}
elseif ($import_step == 'finalize') {

    // FINALIZE rows processing: update database

    // Drop old data
    if ($import_file['drop'][strtolower($section)] == 'Y') {

        db_query("DELETE FROM $sql_tbl[memberships]");
        $import_file['drop'][strtolower($section)] = '';
    }

    // Import users
    foreach ($data_row as $row) {

        $is_exists = false;
        if (!empty($row['membershipid'])) {
            $is_exists = func_query_first_cell("SELECT membershipid FROM $sql_tbl[memberships] WHERE membershipid = '".$row['membershipid']."'") > 0;
        }

        $data = func_addslashes($row);

        if ($is_exists) {
            // Update membership
            func_array2update('memberships', $data, "membershipid = '$data[membershipid]'");
            $result[strtolower($section)]['updated']++;

        }
        else {
            // Add user
            $data['membershipid'] = func_array2insert("memberships", $data);
            $result[strtolower($section)]['added']++;
        }

        func_import_save_cache('M', $data['membership'], $data['membershipid']);

        if ($data['area'] == 'C') {
            func_import_save_cache('MR', $data['membershipid'], $data['membershipid']);
        }

        func_flush(". ");
    }
} elseif ($import_step == 'complete') {

    // Post-import step
    while (list($cid, $tmp) = func_import_read_cache('MR')) {
        $message = func_get_langvar_by_name('txt_subcategories_and_products_counting_',NULL,false,true);
        func_import_add_to_log($message);
        func_flush("<br />\n".$message."<br />\n");
        func_recalc_subcat_count(false, 10);
        if (!empty($active_modules['Flyout_Menus']) && func_fc_use_cache()) {
            func_fc_build_categories(1);
        }

        func_flush(". ");
    }

    func_import_erase_cache('MR');

}elseif ($import_step == 'export') {
    // Export data

    while ($id = func_export_get_row($data)) {
        if (empty($id))
            continue;

        // Get data
        $row = func_query_first("SELECT * FROM $sql_tbl[memberships] WHERE membershipid = '".intval($id)."'");
        if (empty($row))
            continue;

        // Write row
        if (!func_export_write_row($row))
            break;
    }
}

?>
