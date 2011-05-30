{*
$Id: newsltr_unsubscr_admin.tpl,v 1.1 2010/05/21 08:32:16 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="mail/html/mail_header.tpl"}

<br />{$lng.eml_unsubscribe_admin_msg|substitute:"email":"<b>`$email`</b>"}

{include file="mail/html/signature.tpl"}
