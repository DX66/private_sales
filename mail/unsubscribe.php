<?php
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

#
# $Id: unsubscribe.php,v 1.44.2.1 2011/01/10 13:11:54 ferz Exp $
#

require_once "../top.inc.php";
require_once $xcart_dir."/init.php";

x_load('mail');

include $xcart_dir."/include/get_language.php";

$redirect_to = $xcart_catalogs['customer'];

if (empty($active_modules["News_Management"]))
	func_header_location($redirect_to."/home.php");

$email = trim($email);

if (!func_check_email($email)) {
    $top_message = array(
        "type" => "E",
        "content" => func_get_langvar_by_name("err_subscribe_email_invalid")
    );
    func_header_location("home.php");
}

$subscribe_lng = $current_language;
$listid_cond = "";
if (!empty($listid)) {
	$listid_cond = " AND listid='$listid'";
	$subscribe_lng = func_query_first_cell("SELECT lngcode FROM $sql_tbl[newslists] WHERE listid='$listid'");
}

$c = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[newslist_subscription] WHERE email='$email'".$listid_cond);
if ($c < 1) {
	func_header_location($redirect_to."/error_message.php?subscribe_bad_email");
}

db_query("DELETE FROM $sql_tbl[newslist_subscription] WHERE email='$email'".$listid_cond);

$saved_lng = $current_language;

#
# Send mail notification to customer
#
$mail_smarty->assign("email",$email);
if($config['News_Management']['eml_newsletter_unsubscr'] == 'Y') {
	$current_language = $subscribe_lng;
	func_send_mail($email, "mail/newsletter_unsubscribe_subj.tpl", "mail/newsletter_unsubscribe.tpl", $config["News_Management"]["newsletter_email"], false);
}
#
# Send mail notification to admin
#
if($config['News_Management']['eml_newsletter_unsubscr_admin'] == 'Y') {
	$current_language = '';
	func_send_mail($config["News_Management"]["newsletter_email"], "mail/newsltr_unsubscr_admin_subj.tpl", "mail/newsltr_unsubscr_admin.tpl", $email, true);
}

$current_language = $saved_lng;

func_header_location($redirect_to."/home.php?mode=unsubscribed&email=".urlencode(stripslashes($email)));
?>
