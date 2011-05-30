{*
$Id: rma_decline.tpl,v 1.1 2010/05/21 08:32:16 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}
{include file="mail/html/mail_header.tpl"}

<br />{include file="mail/salutation.tpl" title=$userinfo.title firstname=$userinfo.firstname lastname=$userinfo.lastname}<br />
<br />
{$lng.eml_rma_return_declined|substitute:"returnid":$return.returnid}<br />
<br />
{include file="modules/RMA/return_data.tpl"}
<br />
{include file="mail/html/signature.tpl"}
