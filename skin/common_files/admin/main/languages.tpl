{*
$Id: languages.tpl,v 1.4.2.2 2010/10/27 13:58:17 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_edit_languages}

<br />

<script type="text/javascript">
//<![CDATA[
window.name="languageswindow";
//]]>
</script>
<script type="text/javascript" src="{$SkinDir}/js/popup_image_selection.js"></script>

{if $no_flags and not $language_data}
  <strong>{$lng.lbl_warning}:</strong> {$lng.txt_displaying_language_icons_disabled_list|substitute:list:$no_flags_list}<br />
  <br />

{elseif $no_flags and not $language_data.has_icon}

  <strong>{$lng.lbl_warning}:</strong> {$lng.txt_displaying_language_icons_disabled_lang}<br />
  <br />
{/if}

{capture name=dialog}
<a name="edit_lng"></a>
<script type="text/javascript">
//<![CDATA[
var txt_are_you_sure = "{$lng.txt_are_you_sure|wm_remove|escape:javascript}";
//]]>
</script>
<table cellpadding="5" cellspacing="0">

<tr>
  <td class="FormButton">{$lng.lbl_language}:</td>
  <td>
    <select name="language" onchange='javascript: self.location="languages.php?language="+this.value;'>
      <option value=""{if $editing_language eq ""} selected="selected"{/if}>{$lng.lbl_select_one}</option>
        {foreach from=$languages item=l}
          <option value="{$l.code|escape}"{if $editing_language eq $l.code} selected="selected"{/if}>{$l.language}</option>
        {/foreach}
    </select>
  </td>
  {if $editing_language ne "" and $editing_language ne $shop_language}
    <td>
      <input type="button" value="{if $language_data.disabled eq "Y"}{$lng.lbl_enable|strip_tags:false|escape}{else}{$lng.lbl_disable|strip_tags:false|escape}{/if}" onclick="javascript: self.location='languages.php?language={$editing_language|escape:html}&amp;mode=change';" />
      <input type="button" value="{$lng.lbl_delete|strip_tags:false|escape}" onclick="javascript: if (confirm(txt_are_you_sure)) self.location='languages.php?language={$editing_language|escape:html}&amp;mode=del_lang';" />
    </td>
  {/if}
</tr>

</table>

{if $editing_language ne ""}

<br />
<br />

<form method="get" action="languages.php" name="dl_form1">
<input type="hidden" name="mode" value="export" />
<input type="hidden" name="language" value="{$editing_language|escape:"html"}" />

<table cellpadding="5" cellspacing="0">
<tr>
  <td>{$lng.lbl_csv_delimiter}:</td>
  <td>{include file="provider/main/ie_delimiter.tpl"}</td>
  <td><input type="submit" value="{$lng.lbl_export|strip_tags:false|escape}" /></td>
</tr>
</table>

</form>

<br />
<br />

{include file="main/subheader.tpl" title=$lng.lbl_language_options}

<form method="get" action="languages.php" name="dl_form">
<input type="hidden" name="mode" value="update_charset" />
<input type="hidden" name="language" value="{$editing_language|escape:"html"}" />

<table cellpadding="5" cellspacing="0">

<tr>
  <td>{$lng.lbl_charset}:</td>
  <td colspan="2"><input type="text" name="charset" value="{$language_data.charset|escape}" /></td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_language_icon}:</td>
  <td colspan="2">
    {if $language_data.has_icon le 0}{assign var="no_delete" value="Y"}{/if}
    {include file="main/edit_image.tpl" type="G" id=$language_data.lngid delete_url="languages.php?mode=delete_image&amp;language=`$language_data.code`" button_name=$lng.lbl_apply no_delete=$no_delete}
  </td>
</tr>

{* To enable right-to-left text direction, uncomment the following code:
<tr>
  <td>&nbsp;</td>
  <td colspan="2">

    <table cellspacing="2" cellpadding="0">
    <tr>
      <td width="1"><input type="checkbox" id="text_dir" name="text_dir" value="Y"{if $language_data.r2l eq 'Y'} checked="checked"{/if} /></td>
      <td align="left"><label for="text_dir">{$lng.lbl_r2l_text_direction}</label></td>
    </tr>
    <tr>
      <td colspan="2" align="left"><br /><strong>{$lng.lbl_note}: </strong>{$lng.lbl_r2l_text_note}</td>
    </tr>
    </table>

  </td>
</tr>
End of the commented code for the right-to-left text direction feature *} 

<tr>
  <td>&nbsp;</td>
  <td colspan="2"><input type="submit" value="{$lng.lbl_apply|strip_tags:false|escape}" /></td>
</tr>

</table>

</form>

{/if}

{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_edit_language extra='width="100%"'}

<br />

{if $language_data}

{$lng.txt_edit_language_note}

<form method="get" action="languages.php" name="topic_form">
<input type="hidden" name="language" value="{$language_data.code}" />
<br />
{$lng.lbl_select_topic}:
<select name="topic" onchange='javascript: document.topic_form.submit();'>
  <option value=""{if $topic eq ""} selected="selected"{/if}>{$lng.lbl_all}</option>
{foreach from=$topics item=t}
  <option value="{$t|escape}"{if $topic eq $t} selected="selected"{/if}>{$t|escape}</option>
{/foreach}
</select>
&nbsp;
&nbsp;
&nbsp;
{$lng.lbl_apply_filter}:
<input type="text" size="16" name="filter" value="{$filter|escape:"html"}" />&nbsp;<input type="submit" value="{$lng.lbl_go|strip_tags:false|escape}" />
</form>

<br />

{$lng.lbl_total_labels_found}: {$total_labels_found}

<br /><br />

{include file="main/navigation.tpl"}

<script type="text/javascript">
//<![CDATA[
var msg_new_label_empty = "{$lng.msg_new_label_empty|wm_remove|escape:javascript}";
var delete_link = 'languages.php?mode=delete&page={$page}&language={$editing_language}&filter={$filter}&topic={$topic}&var=';

{literal}
function func_checklang() {
  if (document.addlblform.new_var_name.value != '' && document.addlblform.new_var_value.value == '') {
    alert(msg_new_label_empty);
    return false;
  }
  return true;
}

{/literal}
//]]>
</script>

{capture name=dialog}
<a name="edit_lng_ent"></a>
<table cellpadding="0" cellspacing="2" width="100%">

{assign var="current_topic" value=""}
{if $data}
<tr>
  <td>

<form action="languages.php" method="post" name="languagespostform">
<input type="hidden" name="mode" value="update" />
<input type="hidden" name="page" value="{$smarty.get.page|escape:"html"}" />
<input type="hidden" name="topic" value="{$topic|escape:"html"}" />
<input type="hidden" name="filter" value="{$filter|escape:"html"}" />
<input type="hidden" name="language" value="{$editing_language|escape:"html"}" />

<table cellspacing="0" cellpadding="2" width="100%">
{foreach from=$data item=lbl}
{if $lbl.topic ne $current_topic}

{if $current_topic ne ""}
<tr>
  <td colspan="2"><img src="{$ImagesDir}/spacer.gif" width="1" height="20" alt="" /></td>
</tr>
{/if}

<tr>
  <td colspan="2" class="TableHead">{$lng.lbl_topic}: {$lbl.topic|escape}</td>
</tr>

{assign var="current_topic" value=$lbl.topic}

{/if}

<tr class="TableSubHead">
  <td><input type="checkbox" name="ids[]" value="{$lbl.name|escape}" /></td>
  <td width="100%"><b>{$lbl.name}</b></td>
</tr>
<tr class="TableSubHead">
  <td>&nbsp;</td>
  <td>
{if $active_modules.HTML_Editor}
{include file="modules/HTML_Editor/popup_link.tpl" id="var_`$lbl.name`" width="99%"}
{/if}
  <textarea id="var_{$lbl.name}" name="var_value[{$lbl.name}]" cols="70" rows="8" style="width: 99%;">{$lbl.value|escape:"html"}</textarea>
  </td>
</tr>
<tr>
  <td>&nbsp;</td>
</tr>

{/foreach}
<tr>
  <td colspan="2">
    <div id="sticky_content">
      <span class="main-button">
        <input type="submit" value="{$lng.lbl_apply_changes|strip_tags:false|escape}" class="big-main-button" />
      </span>
      &nbsp;&nbsp;&nbsp;
      <input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('ids', 'ig'))) submitForm(this, 'delete');" />
    </div>
&nbsp;&nbsp;
  </td>
</tr>
</table>

<br /><br />

</form>

  </td>
</tr>
<tr>
  <td>
    {include file="main/navigation.tpl"}
    <br />
  </td>
</tr>

{/if}
<tr>
  <td>{include file="main/subheader.tpl" title=$lng.lbl_add_new_entry}</td>
</tr>

<tr>
  <td>

<form action="languages.php" method="post" name="addlblform" onsubmit="javascript: return func_checklang();">

<input type="hidden" name="mode" value="add" />
<input type="hidden" name="page" value="{$smarty.get.page|escape:"html"}" />
<input type="hidden" name="topic" value="{$topic|escape:"html"}" />
<input type="hidden" name="filter" value="{$filter|escape:"html"}" />
<input type="hidden" name="language" value="{$editing_language|escape:"html"}" />

<table cellpadding="3" cellspacing="0" width="100%">

{if $topic eq ""}
{assign var="new_topic_default" value="Labels"}
{else}
{assign var="new_topic_default" value=$topic}
{/if}
<tr>
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_select_topic}: <font class="Star">*</font></td>
  <td>
  <select name="new_topic">
    {foreach from=$topics item=t}
    <option value="{$t|escape}"{if $new_topic_default eq $t} selected="selected"{/if}>{$t}</option>
    {/foreach}
  </select>
  </td>
</tr>

<tr>
  <td class="FormButton" width="10%" nowrap="nowrap">{$lng.lbl_variable}: <font class="Star">*</font></td>
  <td align="left"><input type="text" size="50" name="new_var_name" /></td>
</tr>

<tr>
  <td colspan="2" class="FormButton">{$lng.lbl_value}: <font class="Star">*</font></td>
</tr>

<tr>
  <td colspan="2">
{include file="main/textarea.tpl" name="new_var_value" cols=70 rows=8 data="" width="100%" style="width: 100%;"}
  </td>
</tr>

<tr>
  <td colspan="2"><input type="submit" value="{$lng.lbl_add|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>

  </td>
</tr>
</table>
{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_edit_language_entries extra='width="100%"'}
{/if}

<br />

{capture name=dialog}
<a name="def_lng"></a>
<form method="post" action="languages.php">

<table>
<tr>
  <td><b>{$lng.lbl_default_customer_language}:</b></td>
  <td>
  <select name="new_customer_language">
    <option value="">{$lng.lbl_select_one}</option>
{foreach from=$languages item=l}
{if $l.disabled ne 'Y' or $config.default_customer_language eq $l.code}
    <option value="{$l.code|escape}"{if $config.default_customer_language eq $l.code} selected="selected"{/if}>{$l.language}</option>
{/if}
{/foreach}
  </select>
  </td>
</tr>
<tr>
  <td><b>{$lng.lbl_default_admin_language}:</b></td>
  <td>
  <select name="new_admin_language">
    <option value="">{$lng.lbl_select_one}</option>
{foreach from=$languages item=l}
{if $l.disabled ne 'Y' or $config.default_admin_language eq $l.code}
    <option value="{$l.code|escape}"{if $config.default_admin_language eq $l.code} selected="selected"{/if}>{$l.language}</option>
{/if}
{/foreach}
  </select>
  </td>
</tr>
<tr>
  <td colspan="2"><input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" /></td>
</tr>
</table>

<input type="hidden" name="mode" value="change_defaults" />
<input type="hidden" name="language" value="{$editing_language|escape:"html"}" />
</form>
{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_default_languages extra='width="100%"'}

<br />
{capture name=dialog}
<a name="add_lng"></a>
<form method="post" action="languages.php" enctype="multipart/form-data" name="newlanguageform">
<input type="hidden" name="mode" value="add_lang" />
<table>
<tr>
  <td><b>{$lng.lbl_choose_language}:</b></td>
  <td>
  <select name="new_language">
    <option value="">{$lng.lbl_select_one}</option>
{foreach from=$new_languages item=l}
    <option value="{$l.code|escape}">{$l.language}</option>
{/foreach}
  </select>
  </td>
</tr>
</table>
<br />
<table>
<tr>
  <td><b>{$lng.lbl_csv_delimiter}:</b></td>
  <td>{include file="provider/main/ie_delimiter.tpl"}</td>
</tr>
</table>

<table>
<tr>
  <td colspan="2">
    <script type="text/javascript">
    //<![CDATA[
    filesrc='1';
    //]]>
    </script>
    <br />
    <b>{$lng.txt_source_import_file}:</b>
    <table cellpadding="0" cellspacing="0">
    <tr>
      <td width="20">&nbsp;</td>
      <td><input type="radio" id="source_server" name="source" value="server"{if $import_data eq '' or $import_data.source eq 'server'} checked="checked"{/if} onclick="javascript: if (filesrc=='1') return true; visibleBox(filesrc, 1); filesrc='1'; visibleBox(filesrc, 1);" /></td>
      <td><label for="source_server">{$lng.lbl_server}</label></td>
    </tr>
    <tr>
      <td width="20">&nbsp;</td>
      <td><input type="radio" id="source_upload" name="source" value="upload"{if $import_data.source eq 'upload'} checked="checked"{/if} onclick="javascript: if (filesrc=='2') return true; visibleBox(filesrc, 1); filesrc='2'; visibleBox(filesrc, 1);" /></td>
      <td><label for="source_upload">{$lng.lbl_home_computer}</label></td>
    </tr>
    </table>
  </td>
</tr>
<tr>
  <td colspan="2"><br />
  <div id="box1" {if $import_data ne '' and $import_data.source ne 'server'} style="display: none;"{/if}>
  <b>{$lng.txt_csv_file_is_located_on_the_server}:</b>
  <br />
  <input type="text" size="60" name="localfile" value="{$localfile|escape}" /> 
  <br />
  {$lng.txt_csv_file_is_located_on_the_server_expl|substitute:"my_files_location":$my_files_location}
  </div>

  <div id="box2"{if $import_data eq '' or $import_data.source ne 'upload'} style="display: none;"{/if}>
  <b>{$lng.lbl_csv_file_for_upload}:</b><br /><input type="file" size="60" name="import_file" />

  {if $upload_max_filesize}
  <br /><font class="Star">{$lng.lbl_warning}!</font> {$lng.txt_max_file_size_that_can_be_uploaded}: {$upload_max_filesize}b.
  {/if}
  </div>

  </td>
</tr>  
</table>

<br />
{$lng.txt_import_language_note}
<br /><br />
<input type="submit" value="{$lng.lbl_add_update_language|strip_tags:false|escape}" />
</form>
{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_add_new_language extra='width="100%"'}
