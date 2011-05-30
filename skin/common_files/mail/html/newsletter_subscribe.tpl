{*
$Id: newsletter_subscribe.tpl,v 1.1 2010/05/21 08:32:16 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}
{include file="mail/html/mail_header.tpl"}

<br />{$lng.eml_subscribed}

<br />{$lng.eml_unsubscribe_information}
<br />
<a href="{$http_location}/mail/unsubscribe.php?email={$email|escape}">{$http_location}/mail/unsubscribe.php?email={$email|escape}</a>

{include file="mail/html/signature.tpl"}
