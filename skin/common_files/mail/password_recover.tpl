{*
$Id: password_recover.tpl,v 1.1 2010/05/21 08:32:14 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}
{include file="mail/mail_header.tpl"}


{$lng.eml_dear_customer},

{$lng.eml_password_reset_msg}

{$lng.lbl_account_information}:
--------------------
{$lng.lbl_usertype|mail_truncate}{$account.usertype}
{$lng.lbl_username|mail_truncate}{$account.login}
{$lng.lbl_password_reset_url|mail_truncate}{if $config.Security.use_https_login eq 'Y'}{$https_location}{else}{$http_location}{/if}{if $userpath ne ''}{$userpath}{/if}/change_password.php?password_reset_key={$account.password_reset_key}&user={$account.id}


{include file="mail/signature.tpl"}
