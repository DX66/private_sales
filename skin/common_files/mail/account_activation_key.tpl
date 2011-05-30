{*
$Id: account_activation_key.tpl,v 1.1 2010/05/21 08:32:13 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}
{include file="mail/mail_header.tpl"}


{include file="mail/salutation.tpl" title=$userinfo.title firstname=$userinfo.firstname lastname=$userinfo.lastname}

{if $reason eq 'long_unused'}
{$lng.eml_account_was_suspended_long_unused|substitute:"number":$config.Security.suspend_admin_after|substitute:"login_name":$userinfo.login}:
{else}
{$lng.eml_account_was_suspended|substitute:"number":$config.Security.lock_login_attempts|substitute:"login_name":$userinfo.login}:
{/if}

{$http_location}/login.php?activation_key={$activation_key}{if $redirect ne ''}&redirect={$redirect}{/if}{if $usertype ne ""}&usertype{$usertype}{/if}

{include file="mail/signature.tpl"}
