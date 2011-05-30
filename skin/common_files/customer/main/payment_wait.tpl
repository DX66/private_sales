{*
$Id: payment_wait.tpl,v 1.1.2.2 2010/12/01 10:34:35 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<?xml version="1.0" encoding="{$default_charset|default:"iso-8859-1"}"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{config_load file="$skin_config"}
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>{$lng.msg_order_is_being_placed|wm_remove|escape}</title>
  {include file="customer/meta.tpl"}
  {load_defer file="css/`$smarty.config.CSSFilePrefix`.css" type="css"}
  {if $config.UA.browser eq "MSIE" and $config.UA.version eq "6.0"}
    {load_defer file="css/`$smarty.config.CSSFilePrefix`.IE6.css" type="css"}
    {load_defer file="css/`$smarty.config.CSSFilePrefix`.IE7.css" type="css"}
  {/if}
  {if $config.UA.browser eq "MSIE" and $config.UA.version eq "7.0"}
    {load_defer file="css/`$smarty.config.CSSFilePrefix`.IE7.css" type="css"}
  {/if}

{if $use_iframe eq 'Y'}
  {load_defer file="lib/jquery-min.js" type="js"}
  {load_defer file="js/ajax.js" type="js"}
{/if}

{load_defer_code type="css"}
{load_defer_code type="js"}

</head>
<body{$reading_direction_tag} class="payment-wait">
<div class="payment-wait-title">
  <h1>{$lng.msg_order_is_being_placed}</h1>
  <img src="{$ImagesDir}/spacer.gif" class="payment-wait-image" alt="" />
</div>
