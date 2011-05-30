{*
$Id: popup_images.tpl,v 1.3 2010/06/08 06:17:39 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{config_load file="$skin_config"}
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{$lng.lbl_select_file|strip_tags}</title>
<link rel="stylesheet" type="text/css" href="{$SkinDir}/css/skin1_admin.css" />
<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
var err_choose_file_first = "{$lng.err_choose_file_first|wm_remove|escape:javascript|strip_tags|replace:"\n":" "|replace:"\r":" "}";
var err_choose_directory_first = "{$lng.err_choose_directory_first|wm_remove|escape:javascript|strip_tags|replace:"\n":" "|replace:"\r":" "}";
var field_filename = "{$smarty.get.field_filename}";
var field_path = "{$smarty.get.field_path}";

{literal}
function setFile(filename, path) {
  if (window.opener) {

    if (window.opener.document[field_filename])
      window.opener.document[field_filename].value = filename;
    else if (window.opener.document.getElementById(field_filename))
      window.opener.document.getElementById(field_filename).value = filename;

    if (window.opener.document[field_path])
      window.opener.document[field_path].value = path;
    else if (window.opener.document.getElementById(field_path))
      window.opener.document.getElementById(field_path).value = path;

  }
  window.close ();
}

function setFileInfo() {
  if (document.files_form.path.value != "") {
    setFile(document.files_form.path.options[document.files_form.path.selectedIndex].text, document.files_form.path.value);
  } else {
    alert(err_choose_file_first);
  }
}

function setFilePreview() {
  if (document.files_form.path.value != "") {
    document.files_form.file_preview.value = document.files_form.path.value;
    document.files_form.submit();
  } else {
    alert(err_choose_file_first);
  }
}

function checkDirectory() {
  if (document.dir_form.dir.selectedIndex == -1) {
    alert(err_choose_directory_first);
    return false;
  }

  return true;
}

function setImagePreview() {
  if (document.files_form.enable_preview.checked)
    document.preview.src = 'getfile.php?mode=images&file='+document.files_form.path.value.replace(/&/, "%26");
}

{/literal}
//]]>
</script>
</head>
<body class="background"{$reading_direction_tag}>
<table cellpadding="10" cellspacing="0" width="100%"><tr><td>

{assign var=width value="width=\"33%\""}
<br />
{capture name=dialog}

<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td {$width} valign="top">
<form method="get" onsubmit="javascript: return checkDirectory ()" name="dir_form" action="popup_files.php">
<input type="hidden" name="field_filename" value="{$smarty.get.field_filename|escape}" />
<input type="hidden" name="field_path" value="{$smarty.get.field_path|escape}" />
<input type="hidden" name="mode" value="{$mode|escape}" />
<input type="hidden" name="tp" value="images" />

<b>{$lng.lbl_directories}:</b><br />
<select name="dir" size="20" style="width: 100%" ondblclick="javascript: if(checkDirectory()) document.dir_form.submit();">
{section name=idx loop=$dir_entries}
{if $dir_entries[idx].filetype eq "dir"}
<option value="{$dir_entries[idx].href}">{$dir_entries[idx].file|truncate:35}/</option>
{/if}
{/section}
</select><br /><br />
<center>
<input type="submit" value="{$lng.lbl_change_directory|strip_tags:false|escape}" /></center></form>
</td>

<form method="get" name="files_form" action="popup_files.php">

<td {$width} valign="top">
<input type="hidden" name="dir" value="{$smarty.get.dir|escape:"html"}" />
<input type="hidden" name="field_filename" value="{$smarty.get.field_filename|escape:"html"}" />
<input type="hidden" name="field_path" value="{$smarty.get.field_path|escape:"html"}" />
<input type="hidden" name="mode" value="{$mode|escape}" />
<input type="hidden" name="action" value="" />
<input type="hidden" name="file_preview" value="" />
<input type="hidden" name="tp" value="images" />

<b>{$lng.lbl_files}:</b>
<select name="path" size="20" style="width: 100%" onchange="setImagePreview()" ondblclick="javascript: setFileInfo();">
{section name=idx loop=$dir_entries}
{if $dir_entries[idx].filetype ne "dir"}
<option value="{$dir_entries[idx].href|escape:url}"{if $dir_entries[idx].href eq $file_preview} selected="selected"{/if}>{$dir_entries[idx].file|truncate:35}</option>
{/if}
{/section}
</select><br /><br />
<center>
<input type="button" value="{$lng.lbl_select|strip_tags:false|escape}" onclick="javascript: setFileInfo ();" />
</center>
</td>

<td {$width} valign="top">
<b>&nbsp;</b>

<center>
{if $file_preview}
<img src="getfile.php?file={$file_preview}" name="preview" width="100" height="100" alt="{$lng.lbl_preview_image|escape}" /><br />
{else}
<img src="{$preview_image}" name="preview" width="100" height="100" border="1" alt="{$lng.lbl_preview_image|escape}" /><br />
{/if}
<br />
<input id="enable_preview" type="checkbox" name="enable_preview" value="Y" checked="checked" /><label for="enable_preview">{$lng.lbl_preview}</label>
<table cellpadding="0" cellspacing="2" width="100%"><tr>
<td width="4"><img src="{$ImagesDir}/null.gif" width="4" height="1" alt="" /><br /></td><td><div align="justify">{$lng.txt_preview_images_note}</div></td>
</tr></table>
</center>
</td>
</form>
</tr>
</table>
{/capture}
<div align="center">
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_choose_file extra='width="100%"'}
</div>

<p align="right"><a href="javascript:window.close();"><b>{$lng.lbl_close_window}</b></a></p>
</td></tr></table>

</body>
</html>
