{*
$Id: giftcert_notification_subj.tpl,v 1.1 2010/05/21 08:32:13 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}{$config.Company.company_name}: {if $giftcert.recipient}{assign var="rcpt" value=$giftcert.recipient}{else}{assign var="rcpt" value=$giftcert.recipient_email}{/if}{$lng.eml_giftcert_notification_subj|substitute:"recipient":$rcpt}
