{*
$Id: product_printable.tpl,v 1.2 2010/05/28 10:25:43 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{config_load file="$skin_config"}
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>{$lng.txt_site_title}</title>
  {include file="meta.tpl"}
  <link rel="stylesheet" href="{$SkinDir}/css/skin1_admin.css" />
</head>
<body{$reading_direction_tag}>
<table cellpadding="10" cellspacing="10">
<tr>
  <td>
{if $active_modules.Product_Configurator and $main eq "product_configurator"}
{include file="modules/Product_Configurator/product.tpl"}
{else}
{include file="main/product.tpl"}
{/if}
  </td>
</tr>
</table>
</body>
</html>
