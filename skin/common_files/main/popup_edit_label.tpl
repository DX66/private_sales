{*
$Id: popup_edit_label.tpl,v 1.3 2010/07/13 14:41:46 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{config_load file="$skin_config"}
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{$lng.lbl_label_dialog|wm_remove|escape}</title>
{include file="meta.tpl"}
{include file="presets_js.tpl"}
<script type="text/javascript" src="{$SkinDir}/js/common.js"></script>
<script type="text/javascript" src="{$SkinDir}/js/popup_edit_label.js"></script>
<link rel="stylesheet" type="text/css" href="{$SkinDir}/css/skin1_admin.css" />
<style type="text/css">
{literal}
BODY {
  MARGIN: 5px;
  PADDING: 0px;
  BACKGROUND-COLOR: #FFFBD3;
}
IMG.Icon {
  BORDER: 0px;
  VERTICAL-ALIGN: middle;
  WIDTH: 23px;
  HEIGHT: 22px;
}
.Head {
  FONT-SIZE: 12px;
  FONT-WEIGHT: bold;
}
#labelName {
  FONT-SIZE: 12px;
  PADDING-LEFT: 10px;
}
.webmaster-mode-ie-warn {
  margin: 10px 0;
  color: #b51800;
  text-align: center;
  width: 100%;
}
{/literal}
</style>
</head>
<body{$reading_direction_tag} onload="javascript: getData();" onunload="javascript: rememberXY();">

<form name="lf" action="{$catalogs.admin}/set_label.php" method="post" accept-charset="{$default_charset|default:"iso-8859-1"}" onsubmit="javascript: copyText();">
<input type="hidden" name="lang" value="{$shop_language|escape}" />
<input type="hidden" name="name" value="{$labelName|escape}" />

<table cellspacing="0" cellpadding="0" id="tbl">
<tr>
  <td class="Head">{$lng.lbl_name}:</td>
  <td id="labelName">{$labelName|escape}</td>
</tr>
<tr>
  <td class="Head" valign="top">{$lng.lbl_value}:</td>
  <td style="padding-left: 10px;" valign="top">
{if $tarea}
{if $config.UA.browser eq 'MSIE'}
{include file="main/textarea.tpl" cols=100 rows=10 data=$labelText name="val" width="640px" style="width: 640px;"}
{else}
{include file="main/textarea.tpl" cols=100 rows=5 data=$labelText name="val" width="640px" style="width: 640px;" btn_rows=4}
{/if}
{else}
<input type="text" id="val" name="val" size="50" value="{$labelText|escape}" />
{/if}
</td>
</tr>
<tr>
  <td colspan="2" align="center">
<a onclick="javascript: copyText();" href="javascript:void(0);"><img class="Icon" src="{$ImagesDir}/preview.gif" alt="" />&nbsp;{$lng.lbl_preview}</a>
&nbsp;&nbsp;&nbsp;
<a onclick="javascript: copyText(); document.lf.submit();" href="javascript:void(0);"><img class="Icon" src="{$ImagesDir}/save.gif" alt="" />&nbsp;{$lng.lbl_save}</a>
&nbsp;&nbsp;&nbsp;
<a onclick="javascript: restoreLabel(); window.close();" href="javascript:void(0);"><img class="Icon" src="{$ImagesDir}/cancel.gif" alt="" />&nbsp;{$lng.lbl_cancel}</a>
  </td>
</tr>
</table>
</form>
{if $config.UA.browser eq "MSIE"}
<div class="webmaster-mode-ie-warn">{$lng.txt_webmaster_mode_ie_warn}</div>
{/if}
</body>
</html>
