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
 * $Id: version.php,v 1.32.2.3 2011/01/10 13:11:51 ferz Exp $
 * This script is required by X-Cart support team
 */

if (
    file_exists('../top.inc.php')
    && is_readable('../top.inc.php')
) {
    include_once '../top.inc.php';
}
if (!defined('XCART_START')) die("ERROR: Can not initiate application! Please check configuration.");

require $xcart_dir . '/init.php';

$xcart_db_version = '';

$res = mysql_query("SELECT value FROM $sql_tbl[config] WHERE name='version'");

if (mysql_num_rows($res) < 1) {

    $xcart_db_version = "<= 2.4.1";

} else {
    for ($i = 0; $i < mysql_num_rows($res);  $i++) {
        list ($version) = mysql_fetch_row($res);
        if ($i != 0) $xcart_db_version .= ", ";

        $xcart_db_version .= $version;
    }
}

mysql_free_result($res);

if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[modules] WHERE module_name = 'Simple_Mode'")) {

    $xcart_db_version .= " PRO";

} else {

    $xcart_db_version .= " GOLD";

}

echo "X-Cart DB Version: $xcart_db_version<br />\n";

$modules = func_query("SELECT module_name FROM $sql_tbl[modules]");

if ($modules) {
    foreach ($modules as $module) {
        if (file_exists($xcart_dir.'/modules/'.$module['module_name']."/config.php")) {
            include_once $xcart_dir.'/modules/'.$module['module_name']."/config.php";
        }
    }
}

$addons['Advanced_Order_Management'] = true;
$addons['RMA'] = true;
$addons['Amazon_Checkout'] = true;
$addons['Google_Checkout'] = true;
$addons['Wishlist'] = true;
$addons['Detailed_Product_Images'] = true;
$addons['Wholesale_Trading'] = true;
$addons['Discount_Coupons'] = true;
$addons['Gift_Certificates'] = true;
$addons['Product_Options'] = true;
$addons['XPayments_Connector'] = true;
$addons['Egoods'] = true;

ksort($addons);

if ($addons) {
    echo "<br />Addons:<br />";
    foreach ($addons as $k => $v) {
        echo str_replace('_', " ", $k);
        if (!empty($active_modules[$k]))
            echo " (enabled)";

        echo ";<br />";
    }
}

echo "<br />Checkout Module: ". $config['General']['checkout_module'];

if (!empty($alt_skin_info)) {
    echo "<br />Current Skin: ".$alt_skin_info['name']." (".$alt_skin_info['web_path'].")";
} else {
    echo "<br />Current Skin: unknown";
}

$language_codes = func_query_column("SELECT DISTINCT code FROM $sql_tbl[languages]");
if (!empty($language_codes))
    echo "<br />Available Languages: ".implode(",", $language_codes);

$variants_in_use = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[classes] WHERE avail='Y' AND is_modifier=''");
if ($variants_in_use)
    echo "<br />Variants (in use)";

?>
