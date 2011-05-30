{*
$Id: popup_image_selection.tpl,v 1.2 2010/06/22 12:41:31 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{config_load file="$skin_config"}
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>Image selection</title>
  {include file="meta.tpl"}
  <link rel="stylesheet" type="text/css" href="{$SkinDir}/css/skin1_admin.css" />
</head>
<body{$reading_direction_tag}>

{include file="location.tpl"}

<table cellpadding="10" cellspacing="0" width="100%">
<tr>
  <td>

{include file="main/popup_files_js.tpl"}

{capture name=dialog}
<form action="image_selection.php" method="post" name="imageselform" enctype="multipart/form-data">

<input type="hidden" name="type" value="{$type}" />
<input type="hidden" name="imgid" value="{$imgid}" />
<input type="hidden" name="id" value="{$id}" />
<input type="hidden" name="source" value="" />

<table cellpadding="0" cellspacing="0">
<tr>
  <td colspan="2">
<table border="0">
<tr>
  <td colspan="2">{$lng.txt_select_source_of_image}:</td>
</tr>

<tr>
  <td width="50%">

<table width="100%">
<tr>
  <td colspan="2" bgcolor="#EEEEEE">
  <table cellpadding="0" cellspacing="0" width="100%"><tr><th height="16" align="left">&nbsp;&nbsp;{$lng.lbl_file_on_server}</th></tr></table>
  </td>
</tr>

<tr>
  <td>
  <input type="hidden" name="newpath" id="newpath" />
  <input type="text" size="25" name="newfilename" id="newfilename" readonly="readonly" />
  <input type="button" value="{$lng.lbl_browse_|strip_tags:false|escape}" onclick="javascript: popup_images('newfilename', 'newpath');" />
  </td>
  <td align="right">
  <input type="button" value="{$lng.lbl_apply|strip_tags:false|escape}" onclick="javascript: document.imageselform.source.value='S'; document.imageselform.submit();" />
  </td>
</tr>
</table>

  </td>
</tr>
<tr>
  <td>

<table width="100%">
<tr>
  <td colspan="2" bgcolor="#EEEEEE">
  <table cellpadding="0" cellspacing="0" width="100%"><tr><th height="16" align="left">&nbsp;&nbsp;{$lng.lbl_file_on_local_computer}</th></tr></table>
  </td>
</tr>

<tr>
  <td><input type="file" size="25" name="userfile" /></td>
  <td align="right">
  <input type="button" value="{$lng.lbl_apply|strip_tags:false|escape}" onclick="javascript: document.imageselform.source.value='L'; document.imageselform.submit();" />
  </td>
</tr>

{if $upload_warning}
<tr>
  <td colspan="2">{$upload_warning}</td>
</tr>
{/if}
</table>

  </td>
</tr>

<tr>
  <td colspan="2">

<table width="100%">
<tr>
  <td colspan="2" bgcolor="#EEEEEE">
  <table cellpadding="0" cellspacing="0" width="100%"><tr><th height="16" align="left">&nbsp;&nbsp;{$lng.lbl_file_on_internet}</th></tr></table>
  </td>
</tr>

<tr>
  <td colspan="2"><input type="text" size="60" name="fileurl" /></td>
</tr>
<tr>
  <td colspan="2" align="right">
  <input type="button" value="{$lng.lbl_apply|strip_tags:false|escape}" onclick="javascript: document.imageselform.source.value='U'; document.imageselform.submit();" />
  </td>
</tr>
</table>

  </td>
</tr>

</table>
  </td>
</tr>
</table>

</form>

{/capture}

<div align="center">{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_image_selection extra='width="100%"'}</div>

<p align="right"><a href="javascript:window.close();"><b>{$lng.lbl_close_window}</b></a></p>
  </td>
</tr>
</table>
</body>
</html>

