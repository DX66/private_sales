{*
$Id: wrong_bf_key.tpl,v 1.1 2010/05/21 08:32:15 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="mail/mail_header.tpl"}

{$lng.lbl_cannot_decrypt_password|substitute:"user":$username}

{$lng.txt_bf_key_internal_error}

{include file="mail/signature.tpl"}
