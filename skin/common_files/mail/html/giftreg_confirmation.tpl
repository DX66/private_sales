{*
$Id: giftreg_confirmation.tpl,v 1.1 2010/05/21 08:32:15 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}
{include file="mail/html/mail_header.tpl"}

<br />{include file="mail/salutation.tpl" salutation=$recipient_data.recipient_name}

<br />{$lng.eml_giftreg_confirmation_msg|substitute:"sender":"`$userinfo.title` `$userinfo.firstname` `$userinfo.lastname`"}

<hr size="1" noshade="noshade" />

<br />{$lng.lbl_event}: <b>{$event_data.title}</b>

<hr size="1" noshade="noshade" />

<br />{$lng.eml_giftreg_click_to_confirm}:  <a href="{$http_customer_location}/giftregs.php?cc={$confirmation_code}">{$http_customer_location}/giftregs.php?cc={$confirmation_code}</a>

<br />{$lng.eml_giftreg_click_to_decline}:  <a href="{$http_customer_location}/giftregs.php?cc={$decline_code}">{$http_customer_location}/giftregs.php?cc={$decline_code}</a>

<br />
{include file="mail/html/signature.tpl"}
