{*
$Id: popup_files.tpl,v 1.5 2010/07/02 11:52:50 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{config_load file="$skin_config"}
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset={$default_charset|default:"iso-8859-1"}" />
  <meta http-equiv="X-UA-Compatible" content="IE=8" />
	<title>{$lng.lbl_select_file|wm_remove|escape}</title>
	<link rel="stylesheet" type="text/css" href="{$SkinDir}/css/skin1_admin.css" />
<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
var err_choose_file_first = "{$lng.err_choose_file_first|wm_remove|escape:javascript|strip_tags|replace:"\n":" "|replace:"\r":" "}";
var err_choose_directory_first = "{$lng.err_choose_directory_first|wm_remove|escape:javascript|strip_tags|replace:"\n":" "|replace:"\r":" "}";
var field_filename = window.opener ? window.opener.document.{$smarty.get.field_filename} : false;
var field_path = window.opener ? window.opener.document.{$smarty.get.field_path} : false;
{literal}
function setFile (filename, path) {
  if (field_filename)
    field_filename.value = filename;
  if (field_path)
    field_path.value = path;

  window.close();
}

function setFileInfo () {
  if (document.files_form && document.files_form.path && document.files_form.path.value != "") {
    setFile(document.files_form.path.options[document.files_form.path.selectedIndex].text, document.files_form.path.value);
  } else {
    alert(err_choose_file_first);
  }
}

function checkDirectory () {
  if (document.dir_form.dir.selectedIndex == -1) {
    alert(err_choose_directory_first);
    return false;
  }
  return true;
}

{/literal}
//]]>
</script>
</head>
<body{$reading_direction_tag}>
<br />
{capture name=dialog}
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
  <td width="50%" valign="top">

<form method="get" onsubmit="javascript: return checkDirectory();" name="dir_form" action="popup_files.php">
<input type="hidden" name="field_filename" value="{$smarty.get.field_filename|escape}" />
<input type="hidden" name="field_path" value="{$smarty.get.field_path|escape}" />
{if $product_provider}
<input type="hidden" name="product_provider" value="{$product_provider|escape}" />
{/if}

<b>{$lng.lbl_directories}:</b><br />
<select name="dir" size="20" style="width: 100%" ondblclick="javascript: if (checkDirectory()) document.dir_form.submit();">
{section name=idx loop=$dir_entries}
{if $dir_entries[idx].filetype eq "dir"}
  <option value="{$dir_entries[idx].href}">{$dir_entries[idx].file|truncate:35}/</option>
{/if}
{/section}
</select>
<br /><br />
<center><input type="submit" value="{$lng.lbl_change_directory|strip_tags:false|escape}" /></center>
</form>

  </td>
  <td width="50%" valign="top">

<form method="get" name="files_form" action="popup_files.php">
<input type="hidden" name="dir" value="{$smarty.get.dir|escape:"html"}" />
<input type="hidden" name="field_filename" value="{$smarty.get.field_filename|escape:"html"}" />
<input type="hidden" name="field_path" value="{$smarty.get.field_path|escape:"html"}" />
<b>{$lng.lbl_files}:</b>
<select name="path" size="20" style="width: 100%" ondblclick="javascript: setFileInfo();">
{section name=idx loop=$dir_entries}
{if $dir_entries[idx].filetype ne "dir"}
  <option value="{$dir_entries[idx].href|escape}">{$dir_entries[idx].file|truncate:35}</option>
{/if}
{/section}
</select>
<br /><br />
<center>
<input type="button" value="{$lng.lbl_select|strip_tags:false|escape}" onclick="javascript: setFileInfo();" /></center>

</form>

  </td>
</table>
{/capture}

<div align="center">
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_choose_file extra="width=90%"}
</div>

</body>
</html>
