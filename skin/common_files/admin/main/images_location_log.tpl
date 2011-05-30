{*
$Id: images_location_log.tpl,v 1.1 2010/05/21 08:31:59 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{config_load file="$skin_config"}
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>{$lng.txt_site_title}</title>
  {include file="meta.tpl"}
  <link rel="stylesheet" type="text/css" href="{$SkinDir}/css/skin1_admin.css" />
</head>
<body{$reading_direction_tag}>
<table cellpadding="0" cellspacing="0" width="100%">
<tr>
  <td class="HeadLogo"><a href="{$http_location}"><img src="{$ImagesDir}/admin_xlogo.gif" alt="" /></a></td>
</tr>
</table>

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
  <td class="HeadThinLine"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
<tr>
  <td class="HeadLine" height="22"><img src="{$ImagesDir}/spacer.gif" width="1" height="22" alt="" /></td>
</td>
</tr>
<tr>
  <td class="HeadThinLine"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
</table>

<table cellpadding="2" cellspacing="2" width="100%">
<tr>
  <td class="Header">{$lng.lbl_images_transferring_log}</td>
</tr>

<tr>
<td>

<table cellpadding="5" cellspacing="0" width="0"><tr><td>
<!-- begin -->
<pre>
{if $incfile}
{$incfile}
{else}
{$lng.lbl_log_file_empty}
{/if}
</pre>
<!-- end -->
</td></tr></table>

</td>
</tr>
</table>

</body>
</html>
