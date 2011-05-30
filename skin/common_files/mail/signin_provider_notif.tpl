{*
$Id: signin_provider_notif.tpl,v 1.1 2010/05/31 10:35:32 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="mail/mail_header.tpl"}

{include file="mail/salutation.tpl" title=$userinfo.title firstname=$userinfo.firstname lastname=$userinfo.lastname}

{$lng.eml_signin_provider_notification}

{$lng.lbl_profile_details}:
---------------------
{if $config.Email.show_passwords_in_user_notificat eq 'Y'}
{include file="mail/profile_data.tpl" show_pwd="Y"}
{else}
{include file="mail/profile_data.tpl"}
{/if}


{include file="mail/signature.tpl"}
