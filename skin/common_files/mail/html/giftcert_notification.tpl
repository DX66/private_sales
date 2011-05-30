{*
$Id: giftcert_notification.tpl,v 1.1 2010/05/21 08:32:15 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}
{include file="mail/html/mail_header.tpl"}
<br />{$lng.eml_gc_notification|substitute:"recipient":$giftcert.recipient}

<br />{$lng.eml_gc_copy_sent|substitute:"email":$giftcert.recipient_email}:

<br />=========================| start |=========================

<table cellpadding="15" cellspacing="0" width="100%"><tr><td bgcolor="#EEEEEE">
{include file="mail/html/giftcert.tpl"}
</td></tr></table>

<br />=========================| end |=========================

{include file="mail/html/signature.tpl"}
