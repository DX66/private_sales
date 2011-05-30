{*
$Id: newsletter_signature.tpl,v 1.1 2010/05/21 08:32:16 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<hr size="1" noshade="noshade" />
{$lng.eml_unsubscribe_information}
<br />
<a href="{$http_location}/mail/unsubscribe.php?email={$email|escape}&listid={$listid}">{$http_location}/mail/unsubscribe.php?email={$email|escape}&amp;listid={$listid}</a>

{include file="mail/html/signature.tpl"}

