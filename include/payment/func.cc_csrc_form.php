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
 * Functions for "CyberSource - Hosted Order Page" payment module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.cc_csrc_form.php,v 1.15.2.1 2011/01/10 13:11:52 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

function func_cc_csrc_form_pad($str, $len, $char = "\0", $is_left = true)
{

    return sprintf("%'".$char.($is_left ? "-" : "").$len."s", $str);
}

function func_cc_csrc_form_sha1($str)
{

    return pack("H*", sha1($str));
}

function func_cc_csrc_form_get_timestamp()
{

    $data = explode(" ", microtime());
    $data[0] = func_cc_csrc_form_pad(intval(1000*$data[0]), 3, '0', false);

    return implode('', array_reverse($data));
}

function func_cc_csrc_form_generate_signature($data)
{
    global $sql_tbl;

    $key = func_query_first_cell("SELECT param05 FROM $sql_tbl[ccprocessors] WHERE processor = 'cc_csrc_form.php'");
    $length = 64;

    $o_str = str_repeat(chr(0x5c), $length);
    $i_str = str_repeat(chr(0x36), $length);

    $key = func_cc_csrc_form_pad($key, $length);
    $key = func_cc_csrc_form_pad(func_cc_csrc_form_sha1($key), ($length + strlen($data)));

    return base64_encode(func_cc_csrc_form_sha1(($key^$o_str).func_cc_csrc_form_sha1($key^$i_str.$data)));
}

function func_cc_csrc_form_verify_signature($post)
{

    if (empty($post['signedFields']) ||
        empty($post['orderNumber'])     ||
        empty($post['transactionSignature'])) return false;

    $fields = explode(",", $post['signedFields']);
    $data = '';
    foreach($fields as $field) {
        $data .= $post[$field];
    }

    $signature = func_cc_csrc_form_generate_signature($data);

    return (strcmp($signature, $post['transactionSignature']) == 0);
}

?>
