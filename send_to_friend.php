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
 * Send a recommendation about the product
 * to a specified email
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Customer interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: send_to_friend.php,v 1.26.2.1 2011/01/10 13:11:44 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: home.php"); die("Access denied"); }

if (!$productid)
    func_403(48);

x_session_register('send_to_friend_info');

if (
    $REQUEST_METHOD != 'POST'
    || $mode != 'send'
) {

    if (!empty($send_to_friend_info)) {

        $smarty->assign('send_to_friend_info', $send_to_friend_info);

    }

    return;

}

x_session_register('antibot_friend_err');

$antibot_friend_err = !empty($active_modules['Image_Verification']) && func_validate_image("on_send_to_friend", $antibot_input_str);

$send_to_friend_info = array(
    'name'      => $name,
    'email'     => $email,
    'from'      => $from,
    'message'   => $message,
    'is_msg'    => isset($is_msg) ? 'Y' : ''
);

$send_to_friend_info = func_stripslashes($send_to_friend_info);

if (
    $email
    && $from
    && $name
    && !$antibot_friend_err
) {

    x_load('mail');

    $is_some_email_incorrect = false;


    if (!func_check_email($email)) {

        $send_to_friend_info['email_failed'] = "Y";

        $is_some_email_incorrect = true;

    }

    if (!func_check_email($from)) {

        $send_to_friend_info['from_failed'] = "Y";

        $is_some_email_incorrect = true;

    }

    if ($is_some_email_incorrect) {

        $top_message['content'] = func_get_langvar_by_name('err_subscribe_email_invalid');
        $top_message['type']    = 'E';

        func_header_location(func_get_resource_url('product', $productid));
    }

    $mail_smarty->assign ('product',    $product_info);
    $mail_smarty->assign ('name',       func_html_entity_decode(stripslashes($name)));
    if (isset($is_msg) && !empty($message)) {
        $mail_smarty->assign ('message', func_html_entity_decode(stripslashes($message)));
    }

    $result = func_send_mail(
        $email,
        'mail/send2friend_subj.tpl',
        'mail/send2friend.tpl',
        $from,
        false
    );

    if ($result) {

        $send_to_friend_info = array();

        $top_message['content'] = func_get_langvar_by_name('txt_recommendation_sent');

    } else {

        $top_message['type']    = 'E';
        $top_message['content'] = func_get_langvar_by_name("lbl_send_mail_error");

    }

} else {

    $top_message['content'] = func_get_langvar_by_name('err_filling_form');

    if ($antibot_friend_err) {
        $top_message['content'] .= "<br />" . func_get_langvar_by_name('msg_err_antibot');
    }

    $top_message['type'] = 'E';

    $send_to_friend_info['antibot_err'] = $antibot_friend_err;
    $send_to_friend_info['fill_err']    = true;

}

func_header_location(func_get_resource_url('product', $productid));

?>
