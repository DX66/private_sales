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
 * Templater plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     get_title
 * Input:
 *           page_type
 *           page_id
 * -------------------------------------------------------------
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: function.get_title.php,v 1.19.2.5 2011/01/14 09:49:53 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../../../"); die("Access denied"); }

function smarty_function_get_title($params, &$smarty)
{
    global $active_modules, $sql_tbl, $config, $current_area;

    settype($params['page_type'], "string");
    settype($params['page_id'], "int");


    // Get by page type & page id
    $title = false;
    $default_title_entity = trim($config['SEO']['site_title']);
    switch ($params['page_type']) {
        case 'P':
            // Product page
            x_load('product');
            $title = func_get_product_title($params['page_id']);
            break;

        case 'C':
            // Category page
            x_load('category');
            $title = func_get_category_title($params['page_id']);

            if (empty($title))
                $title = $default_title_entity;

            break;

        case 'M':
            // Manufacturer page
            if (empty($active_modules['Manufacturers']))
                break;

            $title = func_query_first_cell("SELECT title_tag FROM $sql_tbl[manufacturers] WHERE manufacturerid = '$params[page_id]'");

            if (empty($title))
                $title = $default_title_entity;
            break;

        case 'E':
            // Static page (embedded)
            $title = func_query_first_cell("SELECT title_tag FROM $sql_tbl[pages] WHERE pageid = '$params[page_id]'");

            if (empty($title))
                $title = $default_title_entity;

            break;

        default:
            $title = $default_title_entity;
    }

    if (is_string($title)) {
        $title = str_replace(array("\n", "\r"), array('', ''), trim($title));
    }

    if (empty($title) && $current_area == 'C' && $smarty->get_template_vars('location') !== null && is_array($smarty->get_template_vars('location'))) {

        // Title based on bread crumbs
        $location = $smarty->get_template_vars('location');

        // Adjust Shop name
        $lbl_site_title = strip_tags(func_get_langvar_by_name('lbl_site_title', '', false, true, true));
        if (empty($lbl_site_title))
            $lbl_site_title = $config['Company']['company_name'];

        if (strpos($config['SEO']['page_title_format'], 'long') !== false) {

            if ($location[0][1] == 'home.php') {
                $location[0] = array($lbl_site_title);  
            } else {
                array_unshift($location, array($lbl_site_title));
            }
        } elseif ($location[0][1] == 'home.php') {
            // Unset Shop name for short title
            unset($location[0]);
        }

        if (strpos($config['SEO']['page_title_format'], 'reverse') !== false)
            $location = array_reverse($location);
        
        $title_items = array();
        foreach ($location as $v) {
            $title_items[] = $v[0];
        }

        if (empty($title_items))
            $title_items = array($lbl_site_title);

        $title = str_replace(array("\n", "\r"), array('', ''), trim(implode(' :: ', $title_items)));
    }

    // truncate
    $title = str_replace("&nbsp;", " ", $title);
    if (strlen($title) > $config['SEO']['page_title_limit'] && $config['SEO']['page_title_limit'] > 0) {
        $title = func_truncate($title, $config['SEO']['page_title_limit']);
    }

    // escape
    if (X_USE_NEW_HTMLSPECIALCHARS) {
        $charset = $smarty->get_template_vars('default_charset') ? $smarty->get_template_vars('default_charset') : 'ISO-8859-1';
        $title = @htmlspecialchars($title, ENT_QUOTES, $charset);

    } else {
        $title = htmlspecialchars($title, ENT_QUOTES);
    }

    // correct the page title with enabled webmaster mode
    if ($smarty->webmaster_mode && !empty($title)) {
        $title = strip_tags(str_replace( array("&lt;", "&gt;"), array("<", ">"), $title ));
    }

    return '<title>' . $title . '</title>';
}
?>
