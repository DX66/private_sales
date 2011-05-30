{*
$Id: provider_declined.tpl,v 1.1.2.1 2010/09/21 12:13:24 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="mail/html/mail_header.tpl"}
<br /><br />
{include file="mail/salutation.tpl" title=$userinfo.title firstname=$userinfo.firstname lastname=$userinfo.lastname}<br />
<br />
{$lng.eml_partner_declined}<br />
<br />
{if $reason ne ""}
<b>{$lng.eml_reason}:</b><br />
{$reason}<br />
<br />
{/if}

{include file="mail/html/signature.tpl"}
