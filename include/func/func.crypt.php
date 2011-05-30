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
 * Crypt functions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.crypt.php,v 1.26.2.1 2011/01/10 13:11:51 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

function text_crypt($s, $type = 'B', $key = false)
{
    global $blowfish, $encryption_types;

    if (strlen($s) == 0)
        return $s;

    if (!in_array((string)$type, $encryption_types))
        $type = 'B';

    $s = trim($s);
    $s .= func_crc32(md5($s));

    if ($type == 'B' || $type == 'C') {
        // Blowfish
        if ($key === false)
            $key = func_get_crypt_key($type);

        if (!$blowfish || empty($key))
            return $s;

        $s = func_bf_crypt($s, $key);

    }

    return $type."-".$s;
}

function text_decrypt($s, $key = false)
{
    global $blowfish;

    if (strlen($s) == 0)
        return $s;

    // Parse crypted data
    $type = func_get_crypt_type($s);
    if ($type === false) {
        $type = 'N';
        $crc32 = false;

    } elseif (substr($s, 1, 1) == '-') {
        $crc32 = true;
        $s = substr($s, 2);

    } else {
        $crc32 = substr($s, 1, 8);
        $s = substr($s, 9);
    }

    // Blowfish
    if ($type == 'B' || $type == 'C') {
        if ($key === false)
            $key = func_get_crypt_key($type);

        if (!$blowfish) {
            x_log_flag('log_decrypt_errors', 'DECRYPT', "The Blowfish service object is missing", true);
            return false;

        } elseif (empty($key)) {
            x_log_flag('log_decrypt_errors', 'DECRYPT', "The key for the selected type ('".$type."') of encryption is missing", true);
            return false;
        }

        $result = trim(func_bf_decrypt($s, $key));

    } elseif ($type == 'N') {
        // Non-encrypted
        $result = $s;
    }

    // CRC32 check
    if ($crc32 === true) {
        // Inner CRC32
        $crc32 = substr($result, -8);
        $result = substr($result, 0, -8);
        if (func_crc32(md5($result)) != $crc32)
            $result = NULL;

    } elseif ($crc32 !== false) {
        // Outer CRC32
        if (func_crc32($result) != $crc32)
            $result = NULL;
    }

    return $result;
}

/**
 * Get encryptiond/decrtyption key
 */
function func_get_crypt_key($type)
{
    global $blowfish_key, $merchant_password;

    if ($type == 'B') {
        return $blowfish_key;

    } elseif ($type == 'C') {
        x_load('order');
        return func_check_merchant_password() ? $merchant_password : false;
    }

    return false;
}

/**
 * Get crypted string type
 */
function func_get_crypt_type($str)
{
    global $encryption_types;

    $s = substr($str, 0, 1);

    if (!in_array((string)$s, $encryption_types))
        $s = false;

    return $s;
}

/**
 * Check blowfish key
 */
function func_check_blowfish_key()
{
    global $sql_tbl;

    $data = func_query_first_cell("SELECT value FROM $sql_tbl[config] WHERE name = 'crypted_data'");

    return !empty($data) && text_decrypt($data) === 'TEXT';
}

/**
 * Refresh check blowfish data
 */
function func_refresh_check_blowfish_data($new_blowfish_key = null)
{
    global $sql_tbl;

    return func_array2insert(
        'config',
        array(
            'name' => 'crypted_data',
            'value' => text_crypt('TEXT', 'B', $new_blowfish_key ? $new_blowfish_key : false),
        ),
        true
    );
}

?>
