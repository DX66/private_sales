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
 * Ask a question about the product (popup window)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Customer interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: popup_ask.php,v 1.11.2.2 2011/01/10 13:11:43 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './auth.php';
/**
 * Get productid
 */
$productid = intval($productid);

if (
    empty($productid)
) {
    func_close_window();
}

x_load('product');

$product = func_select_product($productid, $user_account['membershipid']);

if (
    empty($product)
) {
    func_close_window();
}

/**
 * Update data
 */
if (
    'POST' === $REQUEST_METHOD
    && 'send_email' === $mode
) {
    $fillError =
        empty($_POST['email'])
        || empty($_POST['uname'])
        || empty($_POST['question'])
        || (
            !empty($active_mod)
        );

    $antibotErr = !empty($active_modules['Image_Verification']) && func_validate_image('on_ask_form', $antibot_input_str);

    $fillError = $fillError || $antibotErr;

    if ($fillError) {

        $top_message['content'] = func_get_langvar_by_name('err_filling_form', false, false, true);

        if ($antibotErr) {
        
            func_register_ajax_message(
                'popupDialogCall',
                array(
                  'action'  => 'jsCall',
                  'toEval'  => 'change_antibot_image(\'on_ask_form\')'
                )
            );

            $top_message['content'] .= "<br />" . func_get_langvar_by_name('msg_err_antibot', false, false, true);
        }

        $top_message['type'] = 'E';

        // Prepare ajax message
        func_register_ajax_message(
            'popupDialogCall',
            array(
                  'action'  => 'message',
                  'message' => $top_message
            )
        );
        
        func_header_location('popup_ask.php?productid=' . $productid);
    }

    $mail_smarty->assign('uname',     $_POST['uname']);
    $mail_smarty->assign('question',  $_POST['question']);
    $mail_smarty->assign('phone',     @$_POST['phone']);
    $mail_smarty->assign('email',     $_POST['email']);
    $mail_smarty->assign('productid', $productid);
    $mail_smarty->assign('product',   $product['product']);

    x_load('mail');

    if (
        !func_send_mail(
            $config['Company']['support_department'],
            'mail/ask_question_subj.tpl',
            'mail/ask_question.tpl',
            $_POST['email'],
            false
        )
    ) {

        $top_message = array(
            'type'    => 'E',
            'content' => func_get_langvar_by_name('lbl_send_mail_error'),
        );

    } else {

        $top_message = array(
            'type'    => 'I',
            'content' => func_get_langvar_by_name('lbl_send_email'),
        );

    }

    func_reload_parent_window();
}

$location = array(
    array($product['product'])
);

$smarty->assign('productid',     $productid);
$smarty->assign('template_name', 'customer/main/ask_question.tpl');

func_display('customer/help/popup_info.tpl', $smarty);
?>
