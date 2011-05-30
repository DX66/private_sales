{*
$Id: popup_unavailable_shipping.tpl,v 1.1 2010/05/21 08:32:18 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{config_load file="$skin_config"}
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title></title>
  <link rel="stylesheet" type="text/css" href="{$SkinDir}/css/skin1_admin.css" />
</head>
<body{$reading_direction_tag}>
<table cellpadding="10" cellspacing="0" width="100%">
<tr>
  <td>

{if $config.Shipping.enable_shipping ne "Y"}

{if $usertype eq "A" or ($usertype eq "P" and $active_modules.Simple_mode)}
{$lng.txt_shipping_disabled_admin|substitute:"path":$catalogs.admin}
{else}
{$lng.txt_shipping_disabled_provider}
{/if}

<br />

{else}

{capture name=dialog}

{if $defined_shippings or $realtime_shippings}

{if $defined_shippings}
<b>{$lng.lbl_defined_shipping_methods}:</b><br />
{foreach from=$defined_shippings item=shipping}
{$shipping.shipping|trademark}<br />
{/foreach}
<br /><br />
{/if}

{if $realtime_shippings}
<b>{$lng.lbl_realtime_shipping_carriers}:</b><br />
{foreach from=$realtime_shippings item=shipping}
{$shipping.code|trademark}<br />
{/foreach}
{/if}

{else}

{$lng.lbl_all_shippings_available}

{/if}

{/capture}

<div align="center">{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_unavailable_shippings extra='width="100%"'}</div>

{/if}

<p align="right"><a href="javascript:window.close();"><b>{$lng.lbl_close_window}</b></a></p>
  </td>
</tr>
</table>
</body>
</html>

