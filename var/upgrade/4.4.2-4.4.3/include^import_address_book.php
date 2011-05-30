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
 * Address book import library
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import_address_book.php,v 1.5.2.1 2011/01/10 13:11:49 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/******************************************************************************
Used cache format:
Address book:
    data_type:  AB
    key:        none

******************************************************************************/

if ( !defined('XCART_SESSION_START') ) { header('Location: ../'); die('Access denied'); }

if ($import_step == 'define') {

    /**
     * Make default definitions (only on first inclusion!)
     */

    $import_specification['ADDRESS_BOOK'] = array(
        'script'        => '/include/import_address_book.php',
        'permissions'    => 'A',
        'export_sql'    => 'SELECT id FROM ' . $sql_tbl['address_book'] . ' GROUP BY userid',
        'parent'        => 'USERS',
        'orderby'       => 16,
        'table'         => 'address_book',
        'key_field'     => 'id',
        'columns'        => array(
            'id'        => array(),
            'userid'    => array(),
            'login'     => array(),
            'usertype'  => array(),
            'default_s' => array(
                'type'      => 'B',
                'default'   => 'N'
            ),
            'default_b' => array(
                'type'      => 'B',
                'default'   => 'N'
            ),
            'title'     => array(),
            'firstname' => array(),
            'lastname'  => array(),
            'address'   => array(),
            'address_2' => array(),
            'city'      => array(),
            'county'    => array(),
            'state'     => array(),
            'country'   => array(),
            'zipcode'   => array(
                'type'      => 'Z'
            ),
            'phone'     => array(),
            'fax'       => array(),
        )
    );

} elseif ($import_step == 'process_row') {

    /**
     * Process row from import file
     */

    $_userid = func_import_detect_user($values);

    if (
        is_null($_userid)
        || (
            $action == 'do'
            && empty($_userid)
        )
    ) {
        func_import_module_error('msg_err_import_log_message_57');
        return false;
    }

    $values['userid'] = $_userid;

    $data_row[] = $values;

}
elseif ($import_step == 'finalize') {

    /**
     * FINALIZE rows processing: update database
     */

    if ($import_file['drop'][strtolower($section)] == "Y") {
        db_query("DELETE FROM $sql_tbl[address_book]");

        $import_file['drop'][strtolower($section)] = '';
    }

    foreach ($data_row as $row) {

        // Delete old data
        $tmp = func_import_get_cache('AB', $row['userid']);
        if (strpos($tmp, 'r') === false) {
            db_query("DELETE FROM $sql_tbl[address_book] WHERE userid = '$row[userid]'");
            func_import_save_cache('AB', $row['userid'], $tmp . 'r');
        }

        // Merge address fields into one (as 2 lines)
        if (isset($row['address_2'])) {
            $row['address'] = trim($row['address'] . "\n" . $row['address_2']);
            func_unset($row, 'address_2');
        }

        func_unset($row, 'login', 'usertype');

        // Import address book
        $data = func_addslashes($row);

        func_array2insert('address_book', $data);
        $result['address_book']['added']++;
        func_flush('. ');
    }

} elseif ($import_step == 'export') {

    /**
     * Export data
     */

    while ($userid = func_export_get_row($data)) {

        if (empty($userid))
            continue;

        // Get user signature
        $u_row = func_export_get_user($userid);

        // Get data
        $row = func_query("SELECT * FROM $sql_tbl[address_book] WHERE $sql_tbl[address_book].id = '$userid'");

        if (!$row || !$u_row) {
            continue;
        }

        foreach ($row as $addr) {

            // Split address lines into 2 fields
            list($addr['address'], $addr['address_2']) = preg_split("/[\r\n]+/", $addr['address'], 2);

            $addr = func_array_merge($addr, $u_row);
            func_unset($addr, 'id');

            // Export row
            if (!func_export_write_row($addr)) {
                break;
            }
        }
    }
}

?>
