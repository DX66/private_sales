{*
$Id: profile_modified.tpl,v 1.1 2010/05/21 08:32:16 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="mail/html/mail_header.tpl"}

<br />{include file="mail/salutation.tpl" title=$userinfo.title firstname=$userinfo.firstname lastname=$userinfo.lastname}

<br />{$lng.txt_profile_modified}

<br />{$lng.lbl_your_profile}:

{include file="mail/html/profile_data.tpl"}

{include file="mail/html/signature.tpl"}
