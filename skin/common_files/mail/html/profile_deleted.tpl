{*
$Id: profile_deleted.tpl,v 1.1 2010/05/21 08:32:16 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}
{include file="mail/html/mail_header.tpl"}

<br />{include file="mail/salutation.tpl" title=$userinfo.title firstname=$userinfo.firstname lastname=$userinfo.lastname}

<br />{$lng.eml_profile_deleted|substitute:"company":$config.Company.company_name}

{include file="mail/html/signature.tpl"}

