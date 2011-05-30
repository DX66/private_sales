{*
$Id: edit_dir.tpl,v 1.6 2010/07/26 08:30:00 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript">
//<![CDATA[
var txt_delete_file_warning = "{$lng.txt_delete_file_warning|wm_remove|wm_remove|escape:javascript}";
var txt_restore_templates_warning = "{$lng.txt_restore_templates_warning|wm_remove|wm_remove|escape:javascript}";
var txt_compile_templates_warning = "{$lng.txt_compile_templates_warning|wm_remove|wm_remove|escape:javascript}";
//]]>
</script>
{if ($what_to_edit eq "files")}

{include file="page_title.tpl" title=$lng.lbl_browse_files}

{$lng.txt_browse_files_top_text}

{else}

{include file="page_title.tpl" title=$lng.lbl_browse_templates}

{$lng.txt_browse_templates_top_text}

{if $smarty.get.dir eq ""}

<br /><br />

{$lng.txt_using_debug_console_note}

{/if}

{/if}

<br /><br />

<form method="post" action="{$action_script}" name="fm_form" enctype="multipart/form-data">

<input type="hidden" name="dir" value="{if $smarty.get.dir ne ""}{$smarty.get.dir|escape:"html"}{else}{$smarty.post.dir|escape:"html"}{/if}" />
<input type="hidden" name="mode" />
<input type="hidden" name="MAX_FILE_SIZE" value="500000000" />
<input type="hidden" name="my_files" value="{$my_files|escape}" />

<table cellpadding="3" cellspacing="1" width="100%">

{if $smarty.get.dir ne ""}
  <tr>
    <td colspan="2">
      <span class="AdminFolderReference"><img src="{$ImagesDir}/folder.gif" width="16" height="16" alt="" /> {if $what_to_edit eq "templates"}{$root_skin_dir}{/if}{$smarty.get.dir|escape:"html"}</span>
    </td>
  </tr>
{/if}

<tr valign="top">
  <td width="50%">

<table cellspacing="0" cellpadding="2">
{section name=dir_entry loop=$dir_entries}

{if $dir_entries_half gt 0 and $smarty.section.dir_entry.index eq $dir_entries_half}
</table>

  </td>
  <td width="50%">

<table cellspacing="0" cellpadding="2">
{/if}

<tr>
{if $dir_entries[dir_entry].filetype eq "dir"}
  <td><input type="radio" name="filename"{if $dir_entries[dir_entry].file eq ".."} disabled="disabled"{/if} value="{$dir_entries[dir_entry].href|amp|escape}" /></td>
  <td><a href="{$action_script}?dir={$dir_entries[dir_entry].href|escape:"url"}" title="{$dir_entries[dir_entry].file|amp|escape}"><img src="{$ImagesDir}/folder.gif" width="16" height="16" alt="" /></a></td>
  <td><a href="{$action_script}?dir={$dir_entries[dir_entry].href|escape:"url"}" title="{$dir_entries[dir_entry].file|amp|escape}">{$dir_entries[dir_entry].file|truncate:35|amp|escape}/</a></td>

{elseif ($what_to_edit eq "files")}

  <td><input type="radio" name="filename" value="{$dir_entries[dir_entry].href|amp|escape}" /></td>
  <td><a href="getfile.php?file={$dir_entries[dir_entry].href|escape:"url"}" title="{$dir_entries[dir_entry].file|amp|escape}"><img src="{$ImagesDir}/doc.gif" width="16" height="16" alt="" /></a></td>
  <td><a href="getfile.php?file={$dir_entries[dir_entry].href|escape:"url"}" title="{$dir_entries[dir_entry].file|amp|escape}">{$dir_entries[dir_entry].file|truncate:35|amp|escape}</a></td>

{else}

  <td><input type="radio" name="filename" value="{$dir_entries[dir_entry].href|amp|escape}" /></td>
  <td><a href="{$action_script}?dir={$smarty.get.dir|escape:"url"}&amp;file={$dir_entries[dir_entry].href|escape:"url"}" title="{$dir_entries[dir_entry].file|amp|escape}"><img src="{$ImagesDir}/doc.gif" width="16" height="16" alt="" /></a></td>
  <td><a href="{$action_script}?dir={$smarty.get.dir|escape:"url"}&amp;file={$dir_entries[dir_entry].href|escape:"url"}" title="{$dir_entries[dir_entry].file|amp|escape}">{$dir_entries[dir_entry].file|truncate:35|amp|escape}</a></td>

{/if}
</tr>

{/section}
</table>

  </td>
</tr>
</table>

<hr width="100%" align="center" />

{if $is_writeable}

<table cellpadding="3" cellspacing="1">

<tr>
  <td colspan="4">
  <input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick='javascript: if (confirm(txt_delete_file_warning)) submitForm(this, "Delete");' />
  </td>
</tr>

{if ($what_to_edit ne "files")}
<tr>
  <td colspan="4"><br /><br />{include file="main/subheader.tpl" title=$lng.lbl_create_new_file class="grey"}</td>
</tr>

<tr>
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_new_filename}:</td>
  <td><input type="text" size="40" name="new_file" value="" /></td>
  <td colspan="2"><input type="button" value="{$lng.lbl_create|strip_tags:false|escape}" onclick='javascript: submitForm(this, "New file");' /></td>
</tr>
{/if}

<tr>
  <td colspan="4"><br /><br />{include file="main/subheader.tpl" title=$lng.lbl_create_new_directory class="grey"}</td>
</tr>

<tr>
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_new_directory_name}:</td>
  <td><input type="text" size="40" name="new_directory" value="" /></td>
  <td colspan="2"><input type="button" value="{$lng.lbl_create|strip_tags:false|escape}" onclick='javascript: submitForm(this, "New directory");' /></td>
</tr>

<tr>
  <td colspan="4"><br /><br />{include file="main/subheader.tpl" title=$lng.copy_selected_file_to_ class="grey"}</td>
</tr>

<tr>
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_copy_file_name}:</td>
  <td><input type="text" size="40" name="copy_file" value="" /></td>
  <td colspan="2"><input type="button" value=" {$lng.lbl_copy|strip_tags:false|escape} " onclick='javascript: submitForm(this, "Copy to");' /></td>
</tr>

<tr>
  <td colspan="4"><br /><br />{include file="main/subheader.tpl" title=$lng.lbl_upload_file_to_directory class="grey"}</td>
</tr>

<tr>
  <td colspan="2"><input type="file" size="50" name="userfile" /></td>
  <td><input type="button" value="{$lng.lbl_upload|strip_tags:false|escape}" onclick='javascript: document.fm_form.mode.value = "Upload"; document.fm_form.submit();' /></td>
  <td>
<input type="checkbox" id="rewrite_if_exists" name="rewrite_if_exists" value="Y" checked="checked" />
<label for="rewrite_if_exists">{$lng.lbl_rewrite_file_if_exists}</label>
  </td>
</tr>

{if $upload_max_filesize}
<tr>
  <td colspan="3">{$lng.txt_max_file_size_warning|substitute:"size":$upload_max_filesize}</td>
</tr>
{/if}

</table>

</form>

{else}

{$lng.txt_directory_is_not_writable}

{/if}
