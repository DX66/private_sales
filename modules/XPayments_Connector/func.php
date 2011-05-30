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
 * Common functions for X-Payment connector module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.php,v 1.13.2.4 2011/01/10 13:12:04 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

// X-Payment connector requirements codes

define('XPC_REQ_CURL', 1);
define('XPC_REQ_OPENSSL', 2);
define('XPC_REQ_DOM', 4);


define('XPC_SYSERR_CARTID', 1);
define('XPC_SYSERR_URL', 2);
define('XPC_SYSERR_PUBKEY', 4);
define('XPC_SYSERR_PRIVKEY', 8);
define('XPC_SYSERR_PRIVKEYPASS', 16);

define('XPC_WPP_DP', 'PayPal WPP Direct Payment');
define('XPC_WPPPE_DP', 'PayPal WPPPE Direct Payment');

define('XPC_API_EXPIRED', 506);

$xpc_paypal_dp_solutions = array('pro' => XPC_WPP_DP, 'uk' => XPC_WPPPE_DP);

/**
 * Load modules/XPayments_Connector/xpc_func.php script
 */
function func_xpay_func_load()
{
    global $xcart_dir;

    require_once ($xcart_dir . '/modules/XPayments_Connector/xpc_func.php');
}

/**
 * Check module system requirements
 *
 * @return boolean Requirements checking result
 */
function xpc_check_requirements()
{
    $code = 0;

    if (!function_exists('curl_init')) {
        $code = $code | XPC_REQ_CURL;
    }

    if (
        !function_exists('openssl_pkey_get_public') || !function_exists('openssl_public_encrypt')
        || !function_exists('openssl_get_privatekey') || !function_exists('openssl_private_decrypt')
        || !function_exists('openssl_free_key')
    ) {
        $code = $code | XPC_REQ_OPENSSL;
    }

    if (!class_exists('DOMDocument')) {
        $code = $code | XPC_REQ_DOM;
    }

    return $code;
}

/**
 * Check profile fields
 * 
 * @return array
 * @see    ____func_see____
 * @since  1.0.0
 */
function xpc_check_fields()
{
    $required_fields = array(
        'firstname',
        'address',
        'city',
        'state',
        'country',
        'zipcode',
        'phone',
    );

    $fields = func_get_default_fields('C', 'address_book');

    $warning_required_fields = array();
    $error_required_fields = array();

    foreach ($required_fields as $name) {

        if ('' == $fields[$name]['avail']) {

            $error_required_fields[] = $name;

        }

        if ('' == $fields[$name]['required']) {

            $warning_required_fields[] = $name;

        }

    }

    $warning_required_fields = empty($warning_required_fields) ? false : implode(', ', $warning_required_fields);
    $error_required_fields = empty($error_required_fields) ? false : implode(', ', $error_required_fields);

    return array(
        $warning_required_fields,
        $error_required_fields,
    );
}

?>
