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
 * Import/export gift certificates
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import.php,v 1.23.2.1 2011/01/10 13:11:57 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/******************************************************************************
Used cache format:
Orders:
    data_type:  O
    key:        <Order ID>
    value:      <Order ID | RESERVED>
Gift certificate:
    data_type:  GC
    key:        <Gift certificate ID>
    value:      <Gift certificate ID | RESERVED>

Note: RESERVED is used if ID is unknown
******************************************************************************/

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

// For func_check_zip function
x_load('user');

if ($import_step == 'process_row') {
/**
 * PROCESS ROW from import file
 */

    // Check orderid
    if (!empty($values['orderid'])) {
        $_orderid = func_import_get_cache('O', $values['orderid']);
        if (is_null($_orderid)) {
            $_orderid = func_query_first_cell("SELECT orderid FROM $sql_tbl[orders] WHERE orderid = '$values[orderid]'");
            if (empty($_orderid)) {
                $_orderid = NULL;
            } else {
                func_import_save_cache('O', $values['orderid'], $_orderid);
            }
        }
        if (is_null($_orderid) || ($action == 'do' && empty($_orderid)))
            $values['orderid'] = 0;
    }

    // Save GC ID
    $_gcid = func_import_get_cache('GC', $values['gcid']);
    if (is_null($_gcid)) {
        func_import_save_cache('GC', $values['gcid']);
    }

    $data_row[] = $values;

} elseif ($import_step == 'finalize') {
/**
 * FINALIZE rows processing: update database
 */

    // Drop old data
    if ($import_file['drop'][strtolower($section)] == 'Y') {

        db_query("DELETE FROM $sql_tbl[giftcerts]");
        $import_file['drop'][strtolower($section)] = '';
    }

    foreach ($data_row as $row) {

    // Import data...

        // Import order items

        // Detect gcid
        $_gcid = func_query_first_cell("SElECT gcid FROM $sql_tbl[giftcerts] WHERE gcid = '".addslashes($row['gcid'])."'");

        $data = func_addslashes($row);

        // Insert gift certificate
        if (empty($_gcid)) {
            func_array2insert('giftcerts', $data);
            $result[strtolower($section)]['added']++;

        // Update gift certificate
        } else {
            func_array2update('giftcerts', $data, "gcid = '$data[gcid]'");
            $result[strtolower($section)]['updated']++;
        }
        func_import_save_cache('GC', $row['gcid'], $row['gcid']);

        echo ". ";
        func_flush();

    }

// Export data
} elseif ($import_step == 'export') {

    while ($id = func_export_get_row($data)) {
        if (empty($id))
            continue;

        $row = func_query_first("SELECT * FROM $sql_tbl[giftcerts] WHERE gcid = '$id'");
        if (empty($row))
            continue;

        if (!func_export_write_row($row))
            break;
    }

}

?>
