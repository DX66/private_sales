{*
$Id: paypal_enable.tpl,v 1.1 2010/05/21 08:32:14 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{$lng.eml_paypal_enable|substitute:"admin_url":$catalogs.admin:"paypal_enable_id":$paypal_enable_id|strip_tags:false}


{include file="mail/signature.tpl"}
