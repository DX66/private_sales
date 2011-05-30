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
 * Functions for the manufacturers module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.php,v 1.17.2.2 2011/01/10 13:11:59 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

/**
 * Check if there are any manufacturer's products assigned to
 * a different $provider (needed to check permissions)
 */
function func_manufacturer_is_used($manufacturerid, $provider)
{
    global $sql_tbl;

    $result = func_query_first_cell("SELECT COUNT(manufacturerid) FROM $sql_tbl[products] WHERE manufacturerid = '".$manufacturerid."' AND provider != '".$provider."'");

    return ($result > 0);
}

/**
 * Set the 'selected' flag for manufacturers and returns selected manufaturers IDs.
 * Needed for advanced search
 */
function func_manufacturer_selected_for_search(&$manufacturers, $selected_ids = false)
{
    global $config;

    $ids = '';

    if (is_array($manufacturers)) {

        array_unshift(
            $manufacturers,
            array(
                'manufacturerid' => 0,
                'manufacturer' => func_get_langvar_by_name('lbl_no_manufacturer')
            )
        );

        $ids = (
            isset($config['Search_products']['search_products_manufacturers_d'])
            && !is_null($config['Search_products']['search_products_manufacturers_d'])
        )
        ? explode("\n", $config['Search_products']['search_products_manufacturers_d'])
        : '';

        $ids_for_check = ($selected_ids !== false)
            ? $selected_ids
            : $ids;

        if ($ids_for_check !== '') {

            foreach ($manufacturers as $k => $v) {

                $manufacturers[$k]['selected'] = @in_array((string)$v['manufacturerid'], $ids_for_check)
                    ? 'Y'
                    : 'N';

            }

            $ids = @implode(",", $ids);

        }

    }

    return $ids;
}

/**
 * Validate manufacturer's URL
 */
function func_check_manufacturer_url($url)
{
    global $xcart_http_host, $current_location;

    return (
        func_check_url($url)
        || func_check_url("http://" . $url)
        || func_check_url("http://" . $xcart_http_host . $url)
        || func_check_url($current_location . $url)
    );
}

/**
 * Retrieve the list of manufacturers 
 * 
 * @param bool  $avail_only Availabale only flag
 * @param int   $limit      Records limit, 0 means no limit
 *  
 * @return array
 * @see    ____func_see____
 */
function func_get_manufacturers_list($avail_only = false, $limit = 0) {
    global $shop_language, $sql_tbl;

    $query = "SELECT $sql_tbl[manufacturers].*, IFNULL($sql_tbl[manufacturers_lng].manufacturer,"
        . " $sql_tbl[manufacturers].manufacturer) AS manufacturer, IFNULL($sql_tbl[manufacturers_lng].descr,"
        . " $sql_tbl[manufacturers].descr) AS descr FROM $sql_tbl[manufacturers]"
        . ($avail_only ? ' USE INDEX (avail)' : '')
        . " LEFT JOIN $sql_tbl[manufacturers_lng] ON $sql_tbl[manufacturers].manufacturerid = $sql_tbl[manufacturers_lng].manufacturerid"
        . " AND $sql_tbl[manufacturers_lng].code = '$shop_language'"
        . ($avail_only ? " WHERE avail = 'Y'" : '')
        . " ORDER BY orderby, manufacturer"
        . ($limit > 0 ? ' LIMIT ' . $limit : '');
    
    return func_query($query);
}

?>
