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
 * Show banner by bannerid
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: banner.php,v 1.20.2.2 2011/01/10 13:11:48 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

if (empty($active_modules['XAffiliate']))
    return;

if (empty($type))
    $type = 'js';

$iframe_referer = '';
if ($type == 'iframe')
    $iframe_referer = urlencode($HTTP_REFERER);

/**
 * Get banner data
 */
if ($bid)
    $data = func_query_first("SELECT * FROM $sql_tbl[partner_banners] WHERE bannerid = '$bid'");

x_session_register('login');
if ($preview && $type == 'preview' && $login) {
    $preview = stripslashes($preview);
    $trans = array_flip(get_html_translation_table());
    $preview = strtr($preview, $trans);
    $data = array(
        'banner_type' => 'M',
        'body' => $preview
    );
}

if (!$data)
    exit;

/**
 * Add statistic record (banner view)
 */
if (
    $partner
    && !func_is_internal_url($HTTP_REFERER)
    && $partner != $logged_userid
) {

    $query = array(
        'userid' => $partner,
        'add_date' => XC_TIME,
        'bannerid' => $bid
    );

    if ($productid) {
        $query['target'] = 'P';
        $query['targetid'] = $productid;

    } elseif ($categoryid) {
        $query['target'] = 'C';
        $query['targetid'] = $categoryid;

    } elseif ($manufacturerid) {
        $query['target'] = 'M';
        $query['targetid'] = $manufacturerid;
    }

    func_array2insert('partner_views', $query);
}

include_once $xcart_dir.'/include/get_language.php';
$charset = $smarty->get_template_vars('default_charset');
$charset_text = $charset ? '; charset=' . $charset : '';

if ($data['banner_type'] == 'P') {

    // Product banner

    x_load('product');
    if (!$productid && !$partner)
        $productid = func_query_first_cell("SELECT productid FROM $sql_tbl[products] ORDER BY RAND()");

    if (!$productid)
        exit;

    $product = func_select_product($productid, $user_account['membershipid']);
    if (!$product)
        exit;

    $smarty->assign('productid', $productid);
    $smarty->assign('product', $product);

} elseif ($data['banner_type'] == 'C') {

    // Category banner

    x_load('category');

    if (!$categoryid && !$partner)
        $categoryid = func_query_first_cell("SELECT categoryid FROM $sql_tbl[categories] ORDER BY RAND()");

    if (!$categoryid)
        exit;

    $category = func_get_category_data($categoryid);
    if (!$category)
        exit;

    $smarty->assign('categoryid', $categoryid);
    $smarty->assign('category', $category);

} elseif ($data['banner_type'] == 'F') {

    // Manufacturer banner

    if (!$manufacturerid && !$partner)
        $manufacturerid = func_query_first_cell("SELECT manufacturerid FROM $sql_tbl[manufacturers] ORDER BY RAND()");

    if (!$manufacturerid)
        exit;

    $manufacturer = func_query_first("SELECT $sql_tbl[manufacturers].*, IF($sql_tbl[images_M].id IS NULL, '', 'Y') as is_image, IFNULL($sql_tbl[manufacturers_lng].manufacturer, $sql_tbl[manufacturers].manufacturer) as manufacturer, IFNULL($sql_tbl[manufacturers_lng].descr, $sql_tbl[manufacturers].descr) as descr FROM $sql_tbl[manufacturers] LEFT JOIN $sql_tbl[manufacturers_lng] ON $sql_tbl[manufacturers_lng].manufacturerid = $sql_tbl[manufacturers].manufacturerid AND $sql_tbl[manufacturers_lng].code = '$shop_language' LEFT JOIN $sql_tbl[images_M] ON $sql_tbl[images_M].id = $sql_tbl[manufacturers].manufacturerid WHERE $sql_tbl[manufacturers].manufacturerid = '$manufacturerid'");
    if (!$manufacturer)
        exit;

    $smarty->assign('manufacturerid', $manufacturerid);
    $smarty->assign('manufacturer', $manufacturer);

} elseif ($data['banner_type'] == 'M') {

    // Media rich banner

    $smarty->register_modifier('mrb_prepare', 'func_xaff_mrb_prepare');
}

$smarty->assign('banner', $data);
$smarty->assign('partner', $partner);

$smarty->assign('type', 'html');

$body = trim($smarty->fetch('main/display_banner.tpl'));

if ($type == 'iframe' || $type == 'preview') {

    // IFRAME mode
    $body = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><title></title></head><body>' . $body . '</body></html>';

} elseif ($type == 'js') {

    // JS mode
    header("Content-type: text/javascript$charset_text");
    $body = "document.write('" . str_replace(array("'", "\n", "\r"), array("\'", ' ', ''), $body) . "');";

} elseif ($type != 'preview') {

    // HTML mode
    header("Content-type: text/html$charset_text");
}

echo $body;

exit;
?>
