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
 * Process credit card fields during registration
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: register_ccfields.php,v 1.6.2.1 2011/01/10 13:11:50 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header('Location: ../'); die('Access denied'); }

// Prepare card expire/valid from values

if (!isset($card_expire) && isset($card_expire_Month)) {
    $card_expire_Month = sprintf("%02d",intval($card_expire_Month));
    $card_expire_Year = sprintf("%04d",intval($card_expire_Year));
    $card_expire = $card_expire_Month.substr($card_expire_Year, 2);
    $card_expire_time = mktime(0,0,0,$card_expire_Month,1,$card_expire_Year);
    if (isset($card_expire_Month)) {
        $card_expire_time = $card_expire_time;
    }
} elseif (isset($card_expire)) {
    $card_expire = sprintf("%04d",intval($card_expire));
}

if (!isset($card_valid_from) && isset($card_valid_from_Month)) {
    $card_valid_from_Month = sprintf("%02d", intval($card_valid_from_Month));
    $card_valid_from_Year = sprintf("%04d", intval($card_valid_from_Year));
    $card_valid_from = $card_valid_from_Month . substr($card_valid_from_Year, 2);
    $card_valid_from_time = mktime(0, 0, 0, $card_valid_from_Month, 1, $card_valid_from_Year);

    if (isset($card_valid_from_Month)) {
        $card_valid_from_time = $card_valid_from_time;
    }

} elseif (isset($card_valid_from)) {
    $card_valid_from = sprintf("%04d", intval($card_valid_from));
}

// Prepare cc info array

$cc_fields = array('card_name', 'card_type', 'card_number', 'card_expire', 'card_valid_from', 'card_cvv2');
$cc_crypt_fields = array('card_number', 'card_cvv2');

$cc_profile_data = array();

foreach ($cc_fields as $f) {
    $val = isset(${$f}) ? trim(${$f}) : '';
    $cc_profile_data[$f] = in_array($f, $cc_crypt_fields) ? addslashes(text_crypt($val)) : $val;
}

?>
