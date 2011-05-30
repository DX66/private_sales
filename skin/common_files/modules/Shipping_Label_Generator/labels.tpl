{*
$Id: labels.tpl,v 1.1 2010/05/21 08:32:47 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{ config_load file="$skin_config" }
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{$lng.lbl_shipping_labels|wm_remove|escape}</title>
{ include file="meta.tpl" }
</head>
<body{$reading_direction_tag} onload="javascript: window.print();" style="background-color: white; margin: 0; padding: 0;">
<table cellspacing="0" cellpadding="0">
{foreach from=$orders key=orderid item=v}
<tr>
  <td>
    {if $v.labels}
    {foreach from=$v.labels item=label key=labelid}
      <img src="{$xcart_web_dir}/slabel.php?orderid={$orderid}{if $labelid ne ""}&amp;labelid={$labelid}{/if}" border="0" alt="" />
    {/foreach}
    {else}
      {$lng.txt_not_available}
    {/if}
  </td>
</tr>
{/foreach}
</table>
</body>
</html>
