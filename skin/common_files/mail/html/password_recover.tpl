{*
$Id: password_recover.tpl,v 1.1 2010/05/21 08:32:16 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}
{include file="mail/html/mail_header.tpl"}

<br />{$lng.eml_dear_customer},

<br />{$lng.eml_password_reset_msg}

<br />
<table cellpadding="1" cellspacing="1">
<tr>
<td colspan="3"><b>{$lng.lbl_account_information}:</b></td>
</tr>
<tr>
<td><tt>{$lng.lbl_usertype}:</tt></td>
<td>&nbsp;</td>
<td><tt>{$account.usertype}</tt></td>
</tr>
<tr>
<td><tt>{$lng.lbl_username}:</tt></td>
<td>&nbsp;</td>
<td><tt>{$account.login}</tt></td>
</tr>
<tr>
<td><tt>{$lng.lbl_password_reset_url}:</tt></td>
<td>&nbsp;</td>
<td><tt><a href="{if $config.Security.use_secure_login_page eq 'Y'}{$https_location}{else}{$http_location}{/if}{if $userpath ne ''}{$userpath}{/if}/change_password.php?password_reset_key={$account.password_reset_key}&amp;user={$account.id}">{if $config.Security.use_https_login eq 'Y'}{$https_location}{else}{$http_location}{/if}{if $userpath ne ''}{$userpath}{/if}/change_password.php?password_reset_key={$account.password_reset_key}&user={$account.id}</a></tt></td>
</tr>
</table>

{include file="mail/html/signature.tpl"}

