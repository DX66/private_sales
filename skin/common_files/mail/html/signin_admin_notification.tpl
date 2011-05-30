{*
$Id: signin_admin_notification.tpl,v 1.1 2010/05/21 08:32:16 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="mail/html/mail_header.tpl"}

<br />{$lng.eml_signin_admin_notification}

<br />{$lng.lbl_profile_details}:

{if $config.Email.show_passwords_in_notifications eq 'Y'}
{include file="mail/html/profile_data.tpl" show_pwd="Y"}
{else}
{include file="mail/html/profile_data.tpl"}
{/if}

{include file="mail/html/signature.tpl"}

