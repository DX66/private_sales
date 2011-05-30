{*
$Id: provider_approved.tpl,v 1.1.2.1 2010/09/21 12:13:23 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="mail/mail_header.tpl"}

{include file="mail/salutation.tpl" title=$userinfo.title firstname=$userinfo.firstname lastname=$userinfo.lastname}

{$lng.eml_partner_approved}

{$lng.lbl_profile_details}:
---------------------
{include file="mail/profile_data.tpl" userinfo=$userinfo}

{include file="mail/signature.tpl"}
