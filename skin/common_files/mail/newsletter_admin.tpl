{*
$Id: newsletter_admin.tpl,v 1.1 2010/05/21 08:32:14 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="mail/mail_header.tpl"}


{$lng.txt_new_subscriber|strip_tags}

{$email}

{include file="mail/signature.tpl"}
