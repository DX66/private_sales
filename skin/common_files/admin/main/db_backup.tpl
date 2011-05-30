{*
$Id: db_backup.tpl,v 1.4 2010/06/11 08:26:48 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_database_backup_restore}

{$lng.txt_database_backup_restore_top_text}

<br /><br />

<script type="text/javascript">
//<![CDATA[
var txt_operation_is_irreversible_warning = "{$lng.txt_operation_is_irreversible_warning|wm_remove|escape:javascript}";
//]]>
</script>

{capture name=dialog}
<form action="db_backup.php" method="post">

{include file="main/subheader.tpl" title=$lng.lbl_backup_database}

<br />

{$lng.txt_backup_database_text}

<br /><br />
{if $smarty.get.err eq 'sql' and $backup_errors ne ''}
{$lng.txt_db_backup_sql_errors}:
<br /><br />
<table cellpadding="5" cellspacing="5" width="100%" style="border: solid #ccc 1px;">
{foreach from=$backup_errors item=error}
<tr>
  <td>{$error|nl2br}</td>
</tr>
{/foreach}
</table>
<br /></br>
{/if}

<table cellpadding="0" cellspacing="0">
{if $smarty.get.err eq 'sql' and $backup_errors ne ''}
<tr>
  <td><input type="checkbox" id="force_db_backup" name="force_db_backup" value="Y" /></td>
  <td><label for="force_db_backup">{$lng.lbl_force_db_backup}</label></td>
</tr>
<tr>
  <td colspan="2" class="Star">{$lng.txt_force_db_backup_note}<br /><br /></td>
</tr>
{/if}
<tr>
  <td><input type="checkbox" id="write_to_file" name="write_to_file" value="Y" /></td>
  <td><label for="write_to_file">{$lng.txt_write_sql_dump_to_file|substitute:"file":$sqldump_file}</label></td>
</tr>
</table>
<br />
<div class="main-button">
  <input type="submit" value="{$lng.lbl_generate_sql_file|strip_tags:false|escape}" />
</div>
<br />

<input type="hidden" name="mode" value="backup" />
</form>
{$lng.txt_backup_database_note}
<br />
<br />
<br />
<form action="db_backup.php" method="post" name="dbrestoreform" enctype="multipart/form-data" onsubmit='javascript: return confirm(txt_operation_is_irreversible_warning)'>

{include file="main/subheader.tpl" title=$lng.lbl_restore_database}

<br />

{$lng.txt_restore_database_text}

<br />

{if $file_exists}
<input type="hidden" name="local_file" value="" />
<table cellpadding="0" cellspacing="0">
<tr>
  <td valign="top" class="main-button">
    <input type="submit" value="{$lng.lbl_restore|strip_tags:false|escape}" onclick="javascript: document.dbrestoreform.local_file.value = 'on';" />
  </td>
  <td>&nbsp;-&nbsp;</td>
  <td>{$lng.txt_restore_database_from_file|substitute:"file":$sqldump_file}</td>
</tr>
</table>
<br />
{/if}
<div class="main-button">
  <input type="file" name="userfile" />&nbsp;
  <input type="submit" value="{$lng.lbl_restore_from_file|strip_tags:false|escape}" />
  <br /><br />
  {$lng.txt_max_file_size_warning|substitute:"size":$upload_max_filesize}
  <input type="hidden" name="mode" value="restore" />
</div>
</form>
{$lng.txt_restore_database_note}
<br /><br />
{/capture}
{include file="dialog.tpl" title=$lng.lbl_database_backup_restore content=$smarty.capture.dialog extra='width="100%"'}
