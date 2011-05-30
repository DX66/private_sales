{*
$Id: giftcert.tpl,v 1.1.2.1 2010/12/15 09:44:39 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}
{include file="mail/html/mail_header.tpl"}
<br />{include file="mail/salutation.tpl" salutation=$giftcert.recipient}

<br />{if $giftcert.purchaser ne ""}{assign var="purchaser" value=$giftcert.purchaser}{else}{assign var="purchaser" value=$giftcert.purchaser_email}{/if}{currency value=$giftcert.amount assign="amount"}{$lng.eml_gc_header|substitute:"purchaser":$purchaser:"amount":$amount}

<br />{$lng.lbl_message}:
<br />
{$giftcert.message}

<br />
<table border="1" cellpadding="20" cellspacing="0">
<tr><td>{$lng.lbl_gc_id}: {$giftcert.gcid}</td></tr>
</table>

<br /><pre>{$lng.eml_gc_body}</pre>

{include file="mail/html/signature.tpl"}
