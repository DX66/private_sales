{*
$Id: order_preauth_expire.tpl,v 1.1 2010/05/21 08:32:14 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}
{assign var=where value="A"}
{include file="mail/mail_header.tpl"}

{$lng.msg_preauth_ttl_expire_coming_soon|substitute:"orders":$orderids}


{include file="mail/signature.tpl"}
