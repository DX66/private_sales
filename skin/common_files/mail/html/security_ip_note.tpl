{*
$Id: security_ip_note.tpl,v 1.1 2010/05/21 08:32:16 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}
{include file="mail/html/mail_header.tpl"}

{if $mode eq 'L'}
{$lng.txt_security_ip_login_note|substitute:"date":$date:"local_login":$local_login:"ip":$ip:"url":$url|replace:"\n":"<br />\n"}
{else}
{$lng.txt_security_ip_note|substitute:"date":$date:"local_login":$local_login:"ip":$ip:"url":$url|replace:"\n":"<br />\n"}
{/if}

{include file="mail/html/signature.tpl"}
