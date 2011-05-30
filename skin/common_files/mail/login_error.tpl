{*
$Id: login_error.tpl,v 1.1 2010/05/21 08:32:14 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="mail/mail_header.tpl"}


{if $usertype eq ''}
{$lng.eml_login_error}
{else}
{$lng.eml_customer_login_error|substitute:"area":$userarea}
{/if}

{if $smarty.server.REMOTE_ADDR ne ""}{$lng.lbl_remote_addr|mail_truncate}{$smarty.server.REMOTE_ADDR}
{/if}
{if $smarty.server.HTTP_X_FORWARDED_FOR ne ""}{$lng.lbl_http_x_forwarded_for|mail_truncate}{$smarty.server.HTTP_X_FORWARDED_FOR}
{/if}
{if $config.Security.send_login_pass eq 'Y'}
{$lng.lbl_username|mail_truncate}{$failed_login}
{$lng.lbl_password|mail_truncate}{$failed_password}
{/if}
{include file="mail/signature.tpl"}
