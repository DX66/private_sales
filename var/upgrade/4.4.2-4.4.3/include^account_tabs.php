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
 * Define account tabs
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: account_tabs.php,v 1.8.2.2 2011/01/10 13:11:48 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header('Location: ../'); die('Access denied'); }

$account_tabs = array();

$account_tabs[] = array(
    'title' => func_get_langvar_by_name('lbl_account_details'),
    'url'   => 'register.php'
);

$account_tabs[] = array(
    'title' => func_get_langvar_by_name('lbl_address_book'),
    'url'   => 'address_book.php'
);

$account_tabs[] = array(
    'title' => func_get_langvar_by_name('lbl_orders_history'),
    'url'   => 'orders.php'
);

if ($active_modules['RMA']) {
    $account_tabs[] = array(
        'title' => func_get_langvar_by_name('lbl_returns'),
        'url'   => 'returns.php'
    );
}

if ($active_modules['Special_Offers']) {
    $account_tabs[] = array(
        'title' => func_get_langvar_by_name('lbl_sp_my_bonuses'),
        'url'   => 'bonuses.php'
    );
}

// Mark selected
foreach ($account_tabs as $k => $tab) {
    if (
        (strpos(strtolower($_SERVER['SCRIPT_NAME']), $tab['url']) > 0)
        || (strpos(strtolower($_SERVER['REQUEST_URI']), $tab['url']) > 0)
        || (strpos(strtolower($_SERVER['PHP_SELF']), $tab['url']) > 0)
    ) {
        $account_tabs[$k]['selected'] = true;
        $location[] = array(func_get_langvar_by_name('lbl_my_account'), '');
        $smarty->assign('page_tabs', $account_tabs);
        break;
    }
}
?>
