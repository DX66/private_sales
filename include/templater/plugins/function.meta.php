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
 * Name:     meta
 * Input:    type
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
 * @version    $Id: function.meta.php,v 1.19.2.2 2011/01/10 13:11:53 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../../../"); die("Access denied"); }

function smarty_function_meta($params, &$smarty)
{
    global $active_modules, $sql_tbl, $config;

    if (!isset($params['type']))
        return '';

    if (!isset($params['page_type']))
        $params['page_type'] = '';

    if (!isset($params['page_id']))
        $params['page_id'] = 0;

    $meta = false;
    switch ($params['page_type']) {
        case 'P':
            // Product page
            x_load('product');
            $meta = func_get_product_meta(intval($params['page_id']));
            break;

        case 'C':
            // Category page
            x_load('category');
            $meta = func_get_category_meta(intval($params['page_id']));
            break;

        case 'M':
            // Manufacturer page
            if (empty($active_modules['Manufacturers']))
                break;

            $tmp = func_query_first("SELECT meta_description, meta_keywords FROM $sql_tbl[manufacturers] WHERE manufacturerid = '".intval($params['page_id'])."'");
            if (is_array($tmp) && count($tmp) == 2)
                $meta = array_values($tmp);

            break;

        case 'E':
            // Static page (embedded)
            $tmp = func_query_first("SELECT meta_description, meta_keywords FROM $sql_tbl[pages] WHERE pageid = '".intval($params['page_id'])."'");
            if (is_array($tmp) && count($tmp) == 2)
                $meta = array_values($tmp);

            break;
    }

    if (!is_array($meta)) {
        $meta = array($config['SEO']['meta_descr'], $config['SEO']['meta_keywords']);

    } else {

        if (!isset($meta[0]) || empty($meta[0]))
            $meta[0] = $config['SEO']['meta_descr'];

        if (!isset($meta[1]) || empty($meta[1]))
            $meta[1] = $config['SEO']['meta_keywords'];
    }

    switch ($params['type']) {
        case 'description':
            $return = $meta[0];
            break;

        case 'keywords':
            $return = $meta[1];
            break;

        default:
            return '';
    }

    if (zerolen($return))
        return '';

    // truncate
    $return = func_truncate($return);

    // escape
    if (X_USE_NEW_HTMLSPECIALCHARS) {
        $charset = $smarty->get_template_vars('default_charset') ? $smarty->get_template_vars('default_charset') : 'ISO-8859-1';
        $return = @htmlspecialchars($return, ENT_QUOTES, $charset);

    } else {
        $return = htmlspecialchars($return, ENT_QUOTES);
    }

    return '<meta name="'.$params['type'].'" content="'.$return.'" />';
}
?>
