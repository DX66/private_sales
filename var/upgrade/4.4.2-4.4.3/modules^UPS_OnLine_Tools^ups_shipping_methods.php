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
 * Prepare data to filter UPS shipping methods in admin/shipping.php
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: ups_shipping_methods.php,v 1.30.2.1 2011/01/10 13:12:03 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

$origin_code = u_get_origin_code($config['Company']['location_country']);

foreach ($ups_services as $service) {
    if (!empty($service[$origin_code])) {
        $valid_ups_services[] = $service[$origin_code];
    }
}
if (is_array($valid_ups_services)) {
    if ($origin_code == 'US')
        $valid_ups_services[] = '100';  // UPS Standard to Canada
    $valid_ups_services[] = '120'; // UPS Worldwide Express Saver (SM)
    $valid_ups_services[] = '130'; // UPS Worldwide Saver (SM)
    $valid_ups_services[] = '140'; // UPS Express Saver (SM)

    $ups_services_condition = " AND service_code IN (".implode(",",$valid_ups_services).")";
}

/**
 * This condition is used in admin/shipping.php
 */
if (!$ups_only)
    $condition = " AND (is_new = 'Y' OR code<>'UPS' OR (code='UPS' AND service_code!=''".@$ups_services_condition."))";
else
    $condition = " AND (code='UPS' AND service_code!=''".@$ups_services_condition.")";

/**
 * This condition is used in provider/shipping_rates.php
 */
$markup_condition .= $condition;

$carriers_tmp = array();

if (is_array($carriers)) {
    foreach ($carriers as $k=>$v) {
        if ($v['code'] == 'UPS') {
            $v['total_methods'] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[shipping] WHERE code='UPS' AND service_code!=''".@$ups_services_condition);
            $v['total_enabled'] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[shipping] WHERE active='Y' AND code='UPS' AND service_code!=''".@$ups_services_condition);
            $carriers[$k] = $v;

        }
    }
}

?>
