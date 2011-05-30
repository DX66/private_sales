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
 * Common functions for Recommended_Products Module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.php,v 1.1.2.3 2011/01/19 06:32:53 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header('Location: ../../'); die('Access denied'); }

/**
 * Update product rnd keys in the product_rnd_keys table
 *
 * @return void
 * @see    ____func_see____
 */
function func_refresh_product_rnd_keys($productid = '')
{
    global $config, $active_modules, $sql_tbl;
    static $max_rnd_number = '';

    $_time = XC_TIME - intval($config['Recommended_Products']['rnd_keys_refresh_period']) * 3600;
    
    settype($config['last_rnd_keys_refresh_time'], 'int');
    if (
        (intval($config['last_rnd_keys_refresh_time']) > $_time && empty($productid))
        || empty($active_modules['Recommended_Products'])
        || $config['Recommended_Products']['select_recommends_list_randomly'] != 'Y'
    ) {
        return false;
    }

    if (empty($max_rnd_number))
        $max_rnd_number = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products]");

    $rnd = rand();
    if ($productid > 0) {
        func_array2insert(
            'product_rnd_keys',
            array(
                'productid' => intval($productid),
                'rnd_key' => "FLOOR(1 + (RAND(NOW() + $rnd) * $max_rnd_number))"
            ),
            true
        );
        
    } else {    

        db_query("DELETE FROM $sql_tbl[product_rnd_keys]");
        db_query("REPLACE INTO $sql_tbl[product_rnd_keys] (productid, rnd_key) SELECT productid, FLOOR(1 + (RAND(NOW() + $rnd) * $max_rnd_number)) AS rnd_key FROM $sql_tbl[products]");

        func_array2insert(
            'config',
            array(
                'name' => 'last_rnd_keys_refresh_time',
                'value' => XC_TIME
            ),
            true
        );
    }


    return true;
}

?>
