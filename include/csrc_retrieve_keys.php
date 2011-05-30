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
 * Parse CyberSource security script and retrieve keys
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: csrc_retrieve_keys.php,v 1.11.2.1 2011/01/10 13:11:48 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

if (!empty($security_script)) {

    $security_script = func_move_uploaded_file('security_script');
    $data = func_file_get($security_script, true);

    if ($data !== false) {
        $csrc_tokens = array(
            'param01'    => 'MerchantID',
            'param02'    => 'SerialNumber',
            'param05'    => 'PublicKey',
            'param06'    => 'PrivateKey',
        );
        $csrc_error_message = '';
        foreach ($csrc_tokens as $csrc_param => $csrc_token) {
            if (!empty($_POST[$csrc_param])) continue;
            $csrc_pattern = "/function\s*get".$csrc_token."\s*\(\s*\)\s*{\s*return\s*\"(.+)\"\s*;\s*}/i";
            if (preg_match($csrc_pattern, $data, $matches)) {
                $_POST[$csrc_param] = $matches[1];
            } else {
                $csrc_error_message .= "&nbsp;-&nbsp;<b>".$csrc_token."</b><br />";
            }
        }
        if (!empty($csrc_error_message)) {
            $top_message['type'] = 'E';
            $top_message['content'] = func_get_langvar_by_name('msg_adm_cc_csrc_error_parse_script');
            $top_message['content'] .= $csrc_error_message;
        } else {
            $top_message['type'] = 'I';
            $top_message['content'] = func_get_langvar_by_name('msg_adm_cc_csrc_success_parse_script');
        }
    } else {
        $top_message['type'] = 'E';
        $top_message['content'] = func_get_langvar_by_name('msg_err_file_operation');
    }
}

unset($_POST['security_script']);

?>
