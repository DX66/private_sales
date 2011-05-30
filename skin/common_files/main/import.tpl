{*
$Id: import.tpl,v 1.4 2010/06/11 08:26:49 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=dialog}

{if $show_error ne ""}

{$lng.txt_import_error}

<br /><br />
{if $import_log_url}
<div align="right">{include file="buttons/button.tpl" href="$import_log_url" button_title=$lng.lbl_view_entire_import_log}</div>

<br />

<table cellspacing="0" cellpadding="1" width="100%">
<tr>
  <td bgcolor="#CCCCCC">

<table cellspacing="0" cellpadding="10" width="100%">
<tr>
  <td class="SectionBox">{$import_log_content}</td>
</tr>
</table>

  </td>
</tr>
</table>
{else}
{$lng.txt_log_file_error|substitute:"file":$import_log_file}
{/if}

<br /><br />

{include file="buttons/button.tpl" href="import.php" button_title=$lng.lbl_back_to_import_page}

<br /><br />

{else}

{$lng.txt_import_data_note}

<br /><br />

{if $need_select_provider and $providers}
<form action="import.php" method="post" name="selectprovider_form">
<table cellpadding="0" cellspacing="0" width="100%">
<tr>
  <td>

{include file="main/subheader.tpl" title=$lng.lbl_import_data_provider}

{$lng.txt_data_provider_login}

<table cellpadding="0" cellspacing="3">
<tr>
  <td><b>{$lng.lbl_data_provider_login}:</b></td>
  <td>

<select name="data_provider" onchange="javascript: this.form.submit();">
{if $data_provider eq ''}    
  <option value="">{$lng.lbl_please_select_one}</option>
{/if}
{foreach from=$providers item=p}
  <option value="{$p.id}"{if $data_provider eq $p.id} selected="selected"{/if}>{$p.firstname}{if $p.lastname} {$p.lastname}{/if} ({$p.login})</option>
{/foreach}
</select>

  </td>
</tr>
</table>

  </td>
</tr>
</table>
<br />
</form>
{/if}

<form action="import.php" method="post" enctype="multipart/form-data" name="importdata_form" onsubmit="javascript: return checkDrops(this);">
<input type="hidden" name="mode" value="import" />

{include file="main/subheader.tpl" title=$lng.lbl_import_data}

{$lng.txt_import_data_note2}

<script type="text/javascript">
//<![CDATA[

var drop_alert = "{$lng.txt_import_data_types_js_warning|wm_remove|escape:javascript}";
var filesrc = '{$import_data_filesrc}';

{literal}
function checkDrops(f) {
  for (var x = 0; x < f.elements.length; x++) {
    if (f.elements[x].name.search(/^drop\[/) != -1 && f.elements[x].checked)
      return confirm(drop_alert);
  }

  return true;
}

function sourceSwitch(fs) {
  if (filesrc !== fs) {
    visibleBox(filesrc, fs);
    filesrc = fs;
    visibleBox(filesrc, fs);
  }
}
{/literal}
//]]>
</script>

<table cellpadding="5" cellspacing="1" width="100%">

<tr>
  <td valign="top" width="50%">
  <b>{$lng.lbl_csv_delimiter}:</b><br />{ include file="provider/main/ie_delimiter.tpl" saved_delimiter=$import_data.delimiter}
  </td>
</tr>

<tr>
  <td colspan="2">
<br />
<b>{$lng.txt_source_import_file}:</b>
<table cellpadding="0" cellspacing="0">
<tr>
  <td width="20">&nbsp;</td>
  <td><input type="radio" id="source_server" name="source" value="server"{if $import_data_filesrc eq 1} checked="checked"{/if} onclick="javascript: sourceSwitch(1);" /></td>
  <td><label for="source_server">{$lng.lbl_server}</label></td>
</tr>
<tr>
  <td width="20">&nbsp;</td>
  <td><input type="radio" id="source_upload" name="source" value="upload"{if $import_data_filesrc eq 2} checked="checked"{/if} onclick="javascript: sourceSwitch(2);" /></td>
  <td><label for="source_upload">{$lng.lbl_home_computer}</label></td>
</tr>
{if $allow_url_fopen}
<tr>
  <td width="20">&nbsp;</td>
  <td><input type="radio" id="source_url" name="source" value="url"{if $import_data_filesrc eq 3} checked="checked"{/if} onclick="javascript: sourceSwitch(3);" /></td>
  <td><label for="source_url">{$lng.lbl_url}</label></td>
</tr>
{/if}
</table>
  </td>
</tr>

<tr>
  <td colspan="2"><br />
<div id="box1" {if $import_data ne '' and $import_data.source ne 'server'} style="display: none;"{/if}>
<b>{$lng.txt_csv_file_is_located_on_the_server}:</b>
<br />
<input type="text" size="70" name="localfile" value="{$import_data.localfile|default:"`$my_files_location`import.csv"}" />
<br />
{$lng.txt_csv_file_is_located_on_the_server_expl|substitute:"my_files_location":$my_files_location}
</div>

<div id="box2"{if $import_data eq '' or $import_data.source ne 'upload'} style="display: none;"{/if}>
<b>{$lng.lbl_csv_file_for_upload}:</b><br /><input type="file" size="70" name="userfile" />

{if $upload_max_filesize}
<br /><font class="Star">{$lng.lbl_warning}!</font> {$lng.txt_max_file_size_that_can_be_uploaded}: {$upload_max_filesize}b.
{/if}
</div>

{if $allow_url_fopen}
<div id="box3"{if $import_data eq '' or $import_data.source ne 'url'} style="display: none;"{/if}>
<b>{$lng.txt_csv_file_is_located_on_the_remote}:</b>
<br />
<input type="text" size="70" name="urlfile" value="{$import_data.urlfile}" />
<br />&nbsp;
</div>
{/if}

  </td>
</tr>
</table>

<br /><br />

<div align="right">
{if $smarty.get.open_options ne 'Y'}
{include file="main/visiblebox_link.tpl" mark="5" title=$lng.lbl_import_options}
{else}
{include file="main/visiblebox_link.tpl" mark="5" title=$lng.lbl_import_options visible=true}
{/if}
</div>
{include file="main/import_options.tpl"}

<br /><br />

<div class="main-button">
  <input type="submit" value="{$lng.lbl_import|strip_tags:false|escape}" />
</div>

</form>

<script type="text/javascript" src="{$SkinDir}/js/reset.js"></script>
<script type="text/javascript">
//<![CDATA[
var importdata_form_def = new Array();
importdata_form_def[0] = new Array('options[category_sep]', '{$import_data.options.category_sep|default:"/"|escape:"javascript"}');
importdata_form_def[1] = new Array('options[categoryid]', '0');
importdata_form_def[2] = new Array('options[images_directory]', '');
importdata_form_def[3] = new Array('options[crypt_order_details]', 'Y');
importdata_form_def[4] = new Array('options[crypt_password]', 'Y');
//]]>
</script>
{if $import_log_url}
<div align="right">{include file="buttons/button.tpl" href="$import_log_url" button_title=$lng.lbl_view_import_log}</div>
{/if}

{/if}

{/capture}
{include file="dialog.tpl" title=$lng.lbl_import_data content=$smarty.capture.dialog extra='width="100%"'}
