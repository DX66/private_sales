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
 * Configuration settings import/export library
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import_config.php,v 1.28.2.1 2011/01/10 13:11:49 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/******************************************************************************
Used cache format:

Note: RESERVED is used if ID is unknown
******************************************************************************/

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if ($import_step == 'define') {

    $import_specification['CONFIG'] = array(
        'script'        => '/include/import_config.php',
        'permissions'   => 'A',
        'export_sql'    => "SELECT name FROM $sql_tbl[config] WHERE name != 'version'",
        'orderby'       => 0,
        'columns'       => array(
            'name'      => array(
                'required'  => true,
                'is_key'    => true
            ),
            'category'      => array(),
            'value'         => array(
                'eol_safe'  => true
            ),
            'comment'       => array(),
            'orderby'       => array(
                'type'      => 'N'),
            'type'          => array(
                'type'      => 'E',
                'variants'  => array('text','checkbox','separator','textarea','numeric','selector','multiselector','trimmed_text'),
                'default'   => 'text'),
            'defvalue'      => array(),
            'variants'      => array(
                'array'     => true
            )
        )
    );

} elseif ($import_step == 'process_row') {
/**
 * PROCESS ROW from import file
 */

    $data_row[] = $values;

} elseif ($import_step == 'finalize') {
/**
 * FINALIZE rows processing: update database
 */

    // Drop old data
    if ($import_file['drop'][strtolower($section)] == 'Y') {

        db_query("UPDATE $sql_tbl[config] SET value = defvalue");

        $import_file['drop'][strtolower($section)] = '';

    }

    foreach ($data_row as $row) {

    // Import data...

        // Import config variables

        if ($row['name'] == 'version')
            continue;

        if (is_array($row['variants']))
            $row['variants'] = implode("\n", $row['variants']);

        $data = func_addslashes($row);

        // Update config variables
        if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[config] WHERE name = '$data[name]' AND category = '$data[category]'")) {
            func_array2update('config', $data, "name = '$data[name]' AND category = '$data[category]'");
            $result[strtolower($section)]['updated']++;

        // Add config variables
        } else {
            func_array2insert('config', $data);
            $result[strtolower($section)]['added']++;
        }

        func_flush(". ");

    }

// Export data
} elseif ($import_step == 'export') {

    while ($id = func_export_get_row($data)) {
        if (empty($id))
            continue;

        // Get data
        $row = func_query_first("SELECT * FROM $sql_tbl[config] WHERE name = '$id'");
        if (empty($row))
            continue;

        $row['variants'] = empty($row['variants']) ? array() : explode("\n", $row['variants']);
        if (empty($row['type']) || !in_array($row['type'], array("text","checkbox","separator","textarea","numeric","selector","multiselector","trimmed_text")))
            $row['type'] = 'text';

        // Export row
        if (!func_export_write_row($row))
            break;

    }
}

?>
