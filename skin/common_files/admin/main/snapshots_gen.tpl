{*
$Id: snapshots_gen.tpl,v 1.1 2010/05/21 08:32:00 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{*
This template is used for "Files status" section
*}

{capture name=dialog}

{if $snapshots}

{$lng.txt_snapshots_avail_text}

<br /><br />

{include file="main/check_all_row.tpl" style="line-height: 170%; text-align: right;" form="md5checkform" prefix="to_delete"}

<form action="snapshots.php" method="post" name="md5checkform">
<input type="hidden" name="mode" value="delete" />

<table cellpadding="5" cellspacing="1">

<tr class="TableHead">
  <td width="20%">{$lng.lbl_date}</td>
  <td width="80%">{$lng.lbl_snapshot}</td>
  <td nowrap="nowrap">{$lng.lbl_delete}</td>
</tr>

{section name=cwi loop=$snapshots}
{if $snapshots[cwi].no_file eq "Y"}
<tr{cycle values=", class='TableSubHead'" advance=false}>
{else}
<tr{cycle values=", class='TableSubHead'"}>
{/if}
  <td nowrap="nowrap">{$snapshots[cwi].time|date_format:$config.Appearance.datetime_format}</td>
  <td nowrap="nowrap" width="80%">{$snapshots[cwi].descr|default:$lng.lbl_noname_snapshot}</td>
  <td align="center"><input type="checkbox" name="to_delete[{$snapshots[cwi].time}]" value="on"{if $snapshots[cwi].no_file eq "Y"} checked="checked"{/if} /></td>
</tr>

{if $snapshots[cwi].no_file eq "Y"}
<tr{cycle values=", class='TableSubHead'"}>
  <td colspan="3" class="ErrorMessage">{$lng.txt_warn_file_not_found|substitute:"file":$snapshots[cwi].filename}</td>
</tr>
{/if}

{/section}

<tr>
  <td colspan="3" align="right" class="SubmitBox"><input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('to_delete', 'ig'))) document.md5checkform.submit();" /></td>
</tr>

</table>
</form>

<br /><br />

{/if}

{include file="main/subheader.tpl" title=$lng.lbl_generate_snapshot}

<form action="snapshots.php" method="post" name="md5checkform_add">
<input type="hidden" name="mode" value="generate" />

<table cellpadding="3" cellspacing="1">

<tr>
  <td>{$lng.lbl_snapshot_descr}</td>
  <td><input type="text" name="new_descr" value="" /></td>
  <td><input type="submit" value="{$lng.lbl_generate|strip_tags:false|escape}" /></td>
</tr>

<tr>
  <td colspan="3">{$lng.txt_generate_snapshote_note}</td>
</tr>

</table>
</form>

<br /><br />

{include file="main/subheader.tpl" title=$lng.lbl_upload_snapshot}

<form action="snapshots.php" method="post" name="md5checkform_upload" enctype="multipart/form-data">
<input type="hidden" name="mode" value="upload" />

<table cellpadding="3" cellspacing="1">

<tr>
  <td>{$lng.lbl_snapshot_descr}</td>
  <td><input type="text" name="new_descr" value="" /></td>
  <td><input type="file" name="new_file" /></td>
  <td><input type="submit" value="{$lng.lbl_upload|strip_tags:false|escape}" /></td>
</tr>

<tr>
  <td colspan="3">{$lng.txt_generate_snapshote_note}</td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_snapshots content=$smarty.capture.dialog extra='width="100%"'}

