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
 * Functions for Extra fields module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.php,v 1.18.2.1 2011/01/10 13:11:56 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

/**
 * Check service name format
 */
function func_ef_check_service_name($sname, $user, $fieldid = 0)
{
    global $single_mode, $sql_tbl;

    $condition = '';
    if (!$single_mode)
        $condition .= " AND provider = '$user'";
    if ($fieldid > 0)
        $condition .= " AND fieldid != '$fieldid'";

    if (zerolen($sname))
        return 'empty';
    if (!preg_match("/^[\d\w_]+$/S", $sname))
        return 'format';
    if (in_array(strtoupper($sname), array('PRODUCTID', 'PRODUCTCODE', 'PRODUCT')))
        return 'name';
    if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[extra_fields] WHERE service_name = '$sname'".$condition) > 0)
        return 'duplicate';

    return true;
}

/**
 * Regenerate PRODUCTS_EXTRA_FIELD_VALUES import section structure
 * before read section columns names
 */
function func_ef_before_import($section, &$section_data)
{
    global $sql_tbl, $import_file, $import_data_provider, $single_mode;

    if (strtolower($section) != 'products_extra_field_values')
        return false;

    $fields = array();
    if ($import_file['drop'][strtolower($section)] != 'Y') {

        $provider_condition = ($single_mode ? '' : " AND provider = '".$import_data_provider."'");
        $fields = func_query_column("SELECT service_name FROM $sql_tbl[extra_fields] WHERE 1".$provider_condition);
    }

    while (list($sname, $id) = func_import_read_cache('EN')) {
        if (!is_null($id)) {
            $fields[] = $sname;
        }
    }

    if (empty($fields))
        return false;

    foreach ($fields as $f) {
        $section_data['columns'][strtolower($f)] = array();
    }

    return true;
}

/**
 * Regenerate PRODUCTS_EXTRA_FIELD_VALUES import section structure
 * before initialize export procedure
 */
function func_ef_init_export($section, &$section_data)
{
    global $sql_tbl, $export_data, $current_area, $active_modules, $logged_userid;

    if (strtolower($section) != 'products_extra_field_values')
        return false;

    if ($current_area == 'P' && empty($active_modules['Simple_Mode'])) {
        $provider = $logged_userid;
    } elseif (!empty($export_data['provider'])) {
        $provider = $export_data['provider'];
    }

    $provider_condition = (empty($provider) ? '' : " AND provider = '$provider'");
    $fields = func_query_column("SELECT service_name FROM $sql_tbl[extra_fields] WHERE 1".$provider_condition);

    if (empty($fields))
        return false;

    foreach ($fields as $f) {
        $section_data['columns'][strtolower($f)] = array();
    }

    return true;
}

/**
 * Regenerate PRODUCTS_EXTRA_FIELD_VALUES import section structure
 * before initialize import procedure
 */
function func_ef_init_import($section, &$section_data)
{
    global $sql_tbl, $import_file, $import_data_provider, $single_mode;

    if (strtolower($section) != 'products_extra_field_values')
        return false;

    $provider_condition = ($single_mode ? '' : " AND provider = '".addslashes($import_data_provider)."'");
    $fields = func_query_column("SELECT service_name FROM $sql_tbl[extra_fields] WHERE 1".$provider_condition);

    if (empty($fields))
        return true;

    foreach ($fields as $f) {
        $section_data['columns'][strtolower($f)] = array();
    }

    return true;
}

?>
