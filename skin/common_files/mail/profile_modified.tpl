{*
$Id: profile_modified.tpl,v 1.1 2010/05/21 08:32:15 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="mail/mail_header.tpl"}


{include file="mail/salutation.tpl" title=$userinfo.title firstname=$userinfo.firstname lastname=$userinfo.lastname}

{$lng.txt_profile_modified}

{$lng.lbl_profile_details}:
---------------------
{include file="mail/profile_data.tpl"}


{include file="mail/signature.tpl"}
