{*
$Id: rma_request_created.tpl,v 1.1 2010/05/21 08:32:16 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="mail/html/mail_header.tpl"}
<br />{$lng.eml_rma_request_created|substitute:"creator":"`$userinfo.firstname` `$userinfo.lastname`"}<br />
<br />
{$lng.eml_return_requests}:<br />
{foreach from=$returns item=v}
<hr />
{include file="modules/RMA/return_data.tpl" return=$v}
{/foreach}
</table>
<br />
{include file="mail/html/signature.tpl"}
