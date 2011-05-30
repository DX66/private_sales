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
 * Module configuration
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: config.php,v 1.25.2.2 2011/04/22 12:14:16 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header('Location: ../../'); die('Access denied'); }
/**
 * Global definitions for Gift Certificates module
 */

$css_files['Gift_Certificates'][] = array();

if (defined('IS_IMPORT')) {
    $modules_import_specification['GIFT_CERTIFICATES'] = array(
        'script'        => '/modules/Gift_Certificates/import.php',
        'permissions'    => 'A',
        'parent'        => 'ORDERS',
        'export_sql'    => "SELECT gcid FROM $sql_tbl[giftcerts]",
        'table'            => 'giftcerts',
        'key_field'        => 'gcid',
        'orderby'        => 10,
        'allow_fullfillment' => true,
        'columns'        => array(
            'gcid'                    => array(
                'required'    => true),
            'orderid'                => array(
                'type'        => 'N'),
            'purchaser'                => array(
                'required'    => true),
            'recipient'                => array(
                'required'  => true),
            'send_via'                => array(
                'type'        => 'E',
                'variants'    => array('E','P'),
                'default'    => 'E'),
            'recipient_email'        => array(),
            'recipient_firstname'    => array(),
            'recipient_lastname'    => array(),
            'recipient_address'        => array(),
            'recipient_city'        => array(),
            'recipient_county'        => array(),
            'recipient_state'        => array(),
            'recipient_zipcode'        => array(
                'type'        => 'Z'),
            'recipient_country'        => array(),
            'recipient_phone'        => array(),
            'message'                => array(),
            'amount'                => array(
                'required'    => true,
                'type'        => 'P'),
            'debit'                    => array(
                'type'      => 'P'),
            'status'                => array(
                'type'        => 'E',
                'variants'    => array('P','A','B','D','E','U'),
                'default'    => 'A'),
            'add_date'                => array(
                'type'        => 'D'),
            'block_date'            => array(
                'type'        => 'D'),
            'tpl_file'                => array(),
        )
    );
}

if (defined('TOOLS')) {
    $tbl_keys['giftcerts.orderid'] = array(
        'keys' => array('giftcerts.orderid' => 'orders.orderid'),
        'where' => "giftcerts.orderid > 0",
        'fields' => array('gcid')
    );
    $tbl_demo_data['Gift_Certificates'] = array(
        'giftcerts' => ''
    );
}

$_module_dir  = $xcart_dir . XC_DS . 'modules' . XC_DS . 'Gift_Certificates';
/*
 Load module functions
*/
if (!empty($include_func))
    require_once $_module_dir . XC_DS . 'func.php';
?>
