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
 * Taxes import/export
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import_taxes.php,v 1.26.2.1 2011/01/10 13:11:49 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/******************************************************************************
Used cache format:
Taxes:
    data_type:  T
    key:        <Tax service name>
    value:      [<Tax ID> | RESERVED]

Note: RESERVED is used if ID is unknown
******************************************************************************/

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if ($import_step == 'define') {
/**
 * Make default definitions (only on first inclusion!)
 */
    define('IMPORT_TAXES', 1);
    $import_specification['TAXES'] = array(
        'script'        => '/include/import_taxes.php',
        'permissions'   => 'A', // Only admin can import taxes
        'need_provider' => false,
        'export_sql'    => "SELECT taxid FROM $sql_tbl[taxes]",
        'orderby'       => 45,
        'columns'       => array(
            'taxid'         => array( // Integer: taxid
                'is_key'    => true,
                'type'      => 'N',
                'required'  => false,  // Required field
                'inherit'   => false,  // Can inherit value
                'default'   => false), // Default value
            'tax'           => array( // String: tax service name
                'is_key'    => true,
                'required'  => true),
            'formula'       => array( // String: tax formula
                'default'   => 'DST'),
            'address_type'  => array( // Char: 'S' or 'B'
                'type'      => 'E',
                'variants'  => array('S','B'),
                'default'   => 'S'),
            'active'        => array( // Char: active flag ('Y' or 'N')
                'type'      => 'B',
                'default'   => 'N'),
            'price_includes_tax' => array( // Char: 'Y' or 'N'
                'type'      => 'B',
                'default'   => 'N'),
            'display_including_tax' => array( // Char: 'Y' or 'N'
                'type'      => 'B',
                'default'   => 'N'),
            'display_info'  => array( // Char: 'R' or 'V' or 'A'
                'type'      => 'E',
                'variants'  => array('R','V','A','N'),
                'default'   => 'N'),
            'regnumber'     => array( // String: tax registration number
                ),
            'priority'      => array( // Integer: tax calculation priority
                'type'      => 'N',
                'default'   => 0)
        )
    );

} elseif ($import_step == 'process_row') {
/**
 * PROCESS ROW from import file
 */

    if (isset($values['taxid']))
        $values['taxid'] = abs($values['taxid']);

    func_import_save_cache('T', $values['tax']);

    $data_row[] = $values;

}
elseif ($import_step == 'finalize') {
/**
 * FINALIZE rows processing: update database
 */

    if ($import_file['drop']['taxes'] == 'Y') {

    // Drop old taxes and all related info

        db_query("DELETE FROM $sql_tbl[tax_rate_memberships]");
        db_query("DELETE FROM $sql_tbl[tax_rates]");
        db_query("DELETE FROM $sql_tbl[product_taxes]");
        db_query("DELETE FROM $sql_tbl[taxes]");

        $import_file['drop']['taxes'] = '';
    }

    foreach ($data_row as $tax) {

    // Import tax levels data...

        // Search if zone already exists...
        $_taxid = false;
        if (!empty($tax['taxid']))
            $_taxid = func_query_first_cell("SELECT taxid FROM $sql_tbl[taxes] WHERE taxid='$tax[taxid]'");

        if (empty($_taxid))
            $_taxid = func_query_first_cell("SELECT taxid FROM $sql_tbl[taxes] WHERE tax_name='".addslashes($tax["tax"])."'");

        $data = array(
            'tax_name'                => addslashes($tax['tax']),
            'formula'                => addslashes($tax['formula']),
            'address_type'            => $tax['address_type'],
            'active'                => $tax['active'],
            'price_includes_tax'    => $tax['price_includes_tax'],
            'display_including_tax'    => $tax['display_including_tax'],
            'display_info'            => $tax['display_info'],
            'regnumber'                => addslashes($tax['regnumber']),
            'priority'                => $tax['priority']
        );

        // Update tax
        if (!empty($_taxid)) {
            func_array2update('taxes', $data, "taxid='$_taxid'");
            $result['taxes']['updated']++;

        // Add tax
        } else {
            if (!empty($tax['taxid']))
                $data['taxid'] = $tax["taxid"];
            $_taxid = func_array2insert('taxes', $data);
            if (!empty($_taxid))
                $result['taxes']['added']++;
        }

        if (empty($_taxid))
            continue;

        // Store $_taxid in the cache
        func_import_save_cache('T', $tax['tax'], $_taxid);

        echo ". ";
        func_flush();

    }

// Export data
} elseif ($import_step == 'export') {

    while ($id = func_export_get_row($data)) {
        if (empty($id))
            continue;

        // Get data
        $row = func_query_first("SELECT * FROM $sql_tbl[taxes] WHERE taxid = '$id'");
        if (!$row)
            continue;

        $row = func_export_rename_cell($row, array('tax_name' => 'tax'));

        // Write row
        if (!func_export_write_row($row))
            break;
    }
}

?>
