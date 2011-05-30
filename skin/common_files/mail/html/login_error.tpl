{*
$Id: login_error.tpl,v 1.1 2010/05/21 08:32:15 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="mail/html/mail_header.tpl"}
<br />{if $usertype eq ''}
{$lng.eml_login_error}
{else}
{$lng.eml_customer_login_error|substitute:"area":$userarea}
{/if}

<br /> 
<table cellpadding="2" cellspacing="1">
{if $smarty.server.REMOTE_ADDR ne ""}
<tr>
<td width="20%"><b>{$lng.lbl_remote_addr}:</b></td> 
<td width="10">&nbsp;</td> 
<td>{$smarty.server.REMOTE_ADDR}</td>
</tr>
{/if}
{if $smarty.server.HTTP_X_FORWARDED_FOR ne ""}
<tr>
<td><b>{$lng.lbl_http_x_forwarded_for}:</b></td>
<td>&nbsp;</td>
<td>{$smarty.server.HTTP_X_FORWARDED_FOR}</td>
</tr>
{/if}
{if $config.Security.send_login_pass eq 'Y'}
<tr>
<td><b>{$lng.lbl_username}:</b></td>
<td>&nbsp;</td>
<td>{$failed_login}</td>
</tr>
<tr>
<td><b>{$lng.lbl_password}:</b></td>
<td>&nbsp;</td>
<td>{$failed_password}</td>
</tr>
{/if}
</table>

{include file="mail/html/signature.tpl"}
