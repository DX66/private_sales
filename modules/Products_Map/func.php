<?php
/* vim: set ts=4 sw=4 sts=4 et: */
/*****************************************************************************\
 +-----------------------------------------------------------------------------+
 | X-Cart                                                                      |
 | Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>                  |
 | All rights reserved.                                                        |
 * -----------------------------------------------------------------------------+
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
 * -----------------------------------------------------------------------------+
\**************************************************************************** */

/**
 * Module functions
 *
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @category   X-Cart
 * @package    Modules
 * @subpackage Products Map
 * @version    $Id: func.php,v 1.8.2.6 2011/02/07 15:34:46 aim Exp $
 * @since      4.4.0
 */
if (!defined('XCART_START')) { header('Location: ../../'); die('Access denied');}

x_load('product');

/**
 * Generate necessary data for pmap
 *
 * @return array
 */
function pmap_generate_map()
{
    $map['symbols']    = pmap_get_symbols();
    $map['current']    = pamp_get_current_symbol($map['symbols']);
    $map['products']   = pmap_get_products($map['current']);
    $map['navigation'] = 'products_map.php?symb';

    return $map;
}

/**
 * Return all avalable symbols
 *
 * @return array
 */
function pmap_get_symbols()
{
    global $sql_tbl;

    // create array of all alpa and num
    $all = array_merge(range('A', 'Z'), array('0-9'));

    // fill in with hide option
    $all = pmap_array_fill_keys($all, false);

    // get first letter of product names
    $avail = func_query_column("SELECT DISTINCT(LEFT($sql_tbl[products].product, 1)) AS id FROM $sql_tbl[products] INNER JOIN $sql_tbl[products_categories] ON $sql_tbl[products].productid = $sql_tbl[products_categories].productid INNER JOIN $sql_tbl[categories] ON $sql_tbl[categories].categoryid = $sql_tbl[products_categories].categoryid WHERE $sql_tbl[products].forsale != 'N' AND $sql_tbl[categories].avail != 'N' GROUP BY id ORDER BY id");

    // uppercase
    $avail = array_map('strtoupper', $avail);

    // fill array contain all symbols with aval ones
    $n = count($avail);

    for ($i = 0; $i < $n; $i++) {

        if (is_numeric($avail[$i])) {

            $all['0-9'] = true;

        } elseif (array_key_exists($avail[$i], $all)) {

            $all[$avail[$i]] = true;

        }
    }

    return $all;
}

/**
 * Get current symbol
 *
 * @param  array $avail_symbols
 * @return string
 */
function pamp_get_current_symbol($avail_symbols)
{
    if (
        isset($_GET['symb']) 
        && !empty($_GET['symb'])
    ) {
        $symb = $_GET['symb'];

        if ($symb == '0-9') {

            return '0-9';

        } elseif (strlen($symb) > 1) {

            $symb = $symb[0];

        }

        if (preg_match ("/^[a-z]$/is", $symb)) {

            return $symb;

        }

	}

    return key($avail_symbols);
}

/**
 * Get product for passed symbol
 *
 * @param  string $symb
 * @return array
 */
function pmap_get_products($symb)
{
    global $sql_tbl, $user_account, $smarty, $config, $xcart_dir, $total_items, $objects_per_page;
    global $active_modules, $smarty;

    $query = " AND $sql_tbl[products].product REGEXP '^[{$symb}]'";

    $membershipid = $user_account['membershipid'];

    $orderby = "$sql_tbl[products].product";

    $products_short = func_search_products($query, $membershipid, $orderby, '', true);

    if (is_array($products_short)) {

        // prepare navigation
        $total_items = count($products_short);
        $objects_per_page = $config['Appearance']['products_per_page'];
        $page = isset($_GET['page']) ? intval($_GET['page']) : 0;

        include $xcart_dir . '/include/navigation.php';

        // assign navigation data to smarty
        $smarty->assign('navigation_script', 'products_map.php?symb=' . $symb);
        $smarty->assign('first_item', $first_page + 1);
        $smarty->assign('last_item', min($first_page + $objects_per_page, $total_items));

        // limit products array
        $products_short = array_slice($products_short, $first_page, $objects_per_page);

        foreach ($products_short as $id => $product) {

            $product = func_select_product($product['productid'], $membershipid);

            if (!isset($product['page_url'])) {
                $product['page_url'] = 'product.php?productid=' . $product['productid'];
            }

            $_limit_width = $config['Appearance']['thumbnail_width'];
            $_limit_height = $config['Appearance']['thumbnail_height'];
            $product = func_get_product_tmbn_dims($product, $_limit_width, $_limit_height);

            $products[] = $product;

            if (
                !empty($active_modules['Feature_Comparison'])
                && !isset($products_has_fclasses)
                && !empty($product['fclassid'])
            ) {
                $smarty->assign('products_has_fclasses', true);
            }

        }


        return $products;
    
    } else {

        return false;

    }

}

/**
 * Fill an array with values, specifying keys
 *
 * @link http://php.net/manual/en/function.array-fill-keys.php
 * @param keys array <p>
 * Array of values that will be used as keys. Illegal values
 * for key will be converted to string.
 * </p>
 * @param value mixed <p>
 * Value to use for filling
 * </p>
 * @return array the filled array
 * </p>
 */
function pmap_array_fill_keys($array, $values) 
{

    if (function_exists('array_fill_keys')) {

        $arraydisplay = array_fill_keys($array, $values);

    } else {

        if(is_array($array)) {

            foreach($array as $key => $value) {

                $arraydisplay[$array[$key]] = $values;

            }

        }

    }

    return $arraydisplay;
}

/**
 * Generate page filename for html catalog
 *
 * @param  string $name
 * @return string
 */
function pmap_filename($name)
{

    if (empty($name)) {

        return __FUNCTION__;

    } else {

        return $name;

    }

}

/**
 * Modify url to point to HTML pages of the catalog
 *
 * @param  array  $data current $additional_hc_data spec
 * @param  string $src  page content
 * @return string
 */
function pmap_process_page($data, $src)
{
    // replacement for general page
    $pattern = '/(<a[^<>]+href[ ]*=[ ]*["\']?)([^"\']*' . $data['page_url'] . ')((#[^"\'>]+)?["\'>])/iUS';

    // define first avaliable letter
    $symbols = pmap_get_symbols();

    $symbol = array_search(true, $symbols, true);

    // creates an url of first page of the first avaliable symbol
    // and replace a php url by it
    $GLOBALS['pmap_page_name'] = sprintf($data['name_func_params'][0], $symbol, 1);

    $src = preg_replace_callback($pattern, 'pmap_process_page_callback_general', $src);

    unset($GLOBALS['pmap_page_name']);

    // replacment for urls on the pmap page only
    if (isset($GLOBALS['pmap_generation_flag'])) {

        $GLOBALS['pmap_page_name'] = $data['name_func_params'][0];

        $pattern = '/(<a[^<>]+href[ ]*=[ ]*["\']?)([^"\']*' . $data['page_url'] . ')\?(symb=[^"\'>]+)((#[^"\'>]+)?["\'>])/iUS';

        $src = preg_replace_callback($pattern, 'pmap_process_page_callback_pmap', $src);

        unset($GLOBALS['pmap_page_name']);

    }

    return $src;
}

/**
 * Callback function for pmap_process_page
 *
 * @param  array $found generated by preg_replace_callback
 * @return strnig
 */
function pmap_process_page_callback_general($found)
{
    if (!func_is_current_shop($found[2])) {

        return $found[1] . $found[2] . '?' . $found[3];

    }

    global $hc_state;

    $url = $found[1] . $hc_state['catalog']['webpath'] . $GLOBALS['pmap_page_name'] . $found[3];

    return $url;
}

/**
 * Callback function for pmap_process_page
 *
 * @param  array $found generated by preg_replace_callback
 * @return string
 */
function pmap_process_page_callback_pmap($found)
{
    global $hc_state;

    if (!func_is_current_shop($found[2])) {

        return $found[1] . $found[2] . '?' . $found[3];

    }

    if (preg_match('/page=([0-9]{1})/S', $found[3], $m)) {

        $page = $m[1];

    } else {

        $page = 1;

    }

    if (preg_match('/symb=([A-z]{1})/S', $found[3], $m)) {

        $symbol = $m[1];

    } else {

        $symbol = array_search(true, pmap_get_symbols(), true);

    }

    $url = sprintf($GLOBALS['pmap_page_name'], $symbol, $page);

    $url = $found[1] . $hc_state['catalog']['webpath'] . $url . $found[4];
    
    return $url;
}

?>
