{*
$Id: snapshots.tpl,v 1.1 2010/05/21 08:32:00 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{*
This template is used for "Snapshots" section
*}

{include file="page_title.tpl" title=$lng.lbl_system_snapshots}

{$lng.txt_snapshots_top_text}

<br /><br />

<br />

{if $mode eq "generate"}

{include file="admin/main/snapshots_gen.tpl"}

{else}

{capture name=dialog}

<form action="snapshots.php" method="post" name="md5checkform">
<input type="hidden" name="mode" value="process" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
  <td>
<table cellpadding="0" cellspacing="0" width="100%">
<tr class="TableSubHead">
  <td width="20"><input type="radio" name="posted_data[mode]" value="1"{if $search_prefilled.mode eq "" or $search_prefilled.mode eq "1"} checked="checked"{/if} /></td>
  <td>&nbsp;&nbsp;<b>{$lng.lbl_compare_snpst_current}</b></td>
</tr>
</table>
<table cellpadding="2" cellspacing="1">
<tr>
  <td><img src="{$ImagesDir}/spacer.gif" width="25" height="1" alt="" /></td>
  <td align="right">{$lng.lbl_snapshot}:</td>
  <td>
  <select name="posted_data[snapshot]">
  <option value="">{$lng.lbl_select_snapshot}</option>
{section name=cwi loop=$snapshots}
{if $snapshots[cwi].no_file ne "Y"}
  <option value="{$snapshots[cwi].time}"{if ($search_prefilled.snapshot eq "" and %cwi.last%) or $search_prefilled.snapshot eq $snapshots[cwi].time} selected="selected"{/if}>{$snapshots[cwi].time|date_format:$config.Appearance.datetime_format} - {$snapshots[cwi].descr|default:$lng.lbl_noname_snapshot|wm_remove|escape}</option>
{/if}
{/section}
  </select>
  </td>
</tr>
</table>
  </td>
</tr>

<tr>
  <td>&nbsp;</td>
</tr>

{if $total_snapshots gt 1}

<tr>
  <td>
<table cellpadding="0" cellspacing="0" width="100%">
<tr class="TableSubHead">
  <td width="20"><input type="radio" name="posted_data[mode]" value="2"{if $search_prefilled.mode eq "2"} checked="checked"{/if} /></td>
  <td>&nbsp;&nbsp;<b>{$lng.lbl_compare_snapshots}</b></td>
</tr>
</table>
<table cellpadding="2" cellspacing="1">
<tr>
  <td><img src="{$ImagesDir}/spacer.gif" width="25" height="1" alt="" /></td>
  <td align="right">{$lng.lbl_snapshot} #1:</td>
  <td>
  <select name="posted_data[snapshot1]">
  <option value="">{$lng.lbl_select_snapshot}</option>
{section name=cwi loop=$snapshots}
{if $snapshots[cwi].no_file ne "Y"}
  <option value="{$snapshots[cwi].time}"{if ($search_prefilled.snapshot1 eq "" and %cwi.last%) or $search_prefilled.snapshot1 eq $snapshots[cwi].time} selected="selected"{/if}>{$snapshots[cwi].time|date_format:$config.Appearance.datetime_format} - {$snapshots[cwi].descr|default:$lng.lbl_noname_snapshot|wm_remove|escape}</option>
{/if}
{/section}
  </select>
  </td>
</tr>
<tr>
  <td><img src="{$ImagesDir}/spacer.gif" width="25" height="1" alt="" /></td>
  <td align="right">{$lng.lbl_snapshot} #2:</td>
  <td>
  <select name="posted_data[snapshot2]">
  <option value="">{$lng.lbl_select_snapshot}</option>
{section name=cwi loop=$snapshots}
{if $snapshots[cwi].no_file ne "Y"}
  <option value="{$snapshots[cwi].time}"{if ($search_prefilled.snapshot2 eq "" and %cwi.last%) or $search_prefilled.snapshot2 eq $snapshots[cwi].time} selected="selected"{/if}>{$snapshots[cwi].time|date_format:$config.Appearance.datetime_format} - {$snapshots[cwi].descr|default:$lng.lbl_noname_snapshot|wm_remove|escape}</option>
{/if}
{/section}
  </select>
  </td>
</tr>
</table>
  </td>
</tr>

<tr>
  <td>&nbsp;</td>
</tr>

{/if}

<tr class="TableSubHead">
  <td><b>{$lng.lbl_select_snapshot_filter}</b></td>
</tr>

<tr>
  <td>

<table cellpadding="2" cellspacing="1" width="90%">

<tr>
  <td><img src="{$ImagesDir}/spacer.gif" width="25" height="1" alt="" /></td>
  <td width="20"><input type="radio" name="posted_data[filter]" value="T"{if $search_prefilled.filter eq "T"} checked="checked"{/if} /></td>
  <td width="50%">{$lng.lbl_templates_only}</td>
  <td>&nbsp;&nbsp;</td>
  <td width="20"><input type="checkbox" name="posted_data[status][C]" value="C"{if $search_prefilled eq "" or $search_prefilled.status.C eq "C"} checked="checked"{/if} /></td>
  <td width="50%">{$lng.lbl_changed_files}</td>
</tr>

<tr>
  <td> </td>
  <td><input type="radio" name="posted_data[filter]" value="P"{if $search_prefilled.filter eq "P"} checked="checked"{/if} /></td>
  <td>{$lng.lbl_php_scripts_only}</td> 
  <td>&nbsp;&nbsp;</td> 
  <td><input type="checkbox" name="posted_data[status][N]" value="N"{if $search_prefilled eq "" or $search_prefilled.status.N eq "N"} checked="checked"{/if} /></td>
  <td>{$lng.lbl_new_files}</td>
</tr>

<tr>
  <td> </td>
  <td><input type="radio" name="posted_data[filter]" value=""{if $search_prefilled.filter eq ""} checked="checked"{/if} /></td>
  <td>{$lng.lbl_all_files}</td> 
  <td>&nbsp;&nbsp;</td> 
  <td><input type="checkbox" name="posted_data[status][A]" value="A"{if $search_prefilled eq "" or $search_prefilled.status.A eq "A"} checked="checked"{/if} /></td>
  <td>{$lng.lbl_absent_files}</td>
</tr>

<tr>
  <td colspan="4">&nbsp;&nbsp;</td> 
  <td><input type="checkbox" name="posted_data[status][U]" value="U"{if $search_prefilled eq "" or $search_prefilled.status.U eq "U"} checked="checked"{/if} /></td>
  <td>{$lng.lbl_unknown_files}</td>
</tr>

</table>

  </td>
</tr>

{*if $search_prefilled}
<tr>
  <td>
<table cellpadding="0" cellspacing="0" width="100%">
<tr class="TableSubHead">
  <td width="20"><input type="checkbox" name="force_refresh" value="Y" /></td>
  <td>&nbsp;&nbsp;<b>{$lng.lbl_force_snapshot_refresh}</b></td>
</tr>
</table>
  </td>
</tr>
{/if*}

<tr>
  <td><br /><input type="submit" value="{$lng.lbl_compare|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_compare_snapshots content=$smarty.capture.dialog extra='width="100%"'}

{if $mode eq "process"}

<br /><br />

{capture name=dialog}

<table cellpadding="3" cellspacing="1" width="100%">

<tr class="TableHead">
  <td width="70%" nowrap="nowrap">{$lng.lbl_file}</td>
  <td>{$lng.lbl_status}</td>
</tr>

{if $file_log}

{foreach from=$file_log key=file item=fileinfo}

{if $fileinfo.status eq "A"}
{assign var="status_color" value="red"}
{assign var="status_text" value=$lng.lbl_file_not_found}
{elseif $fileinfo.status eq "N"}
{assign var="status_color" value="green"}
{assign var="status_text" value=$lng.lbl_new_file}
{elseif $fileinfo.status eq "R"}
{assign var="status_color" value="gray"}
{assign var="status_text" value=$lng.lbl_file_is_not_readable}
{elseif $fileinfo.status eq "C"}
{assign var="status_color" value="blue"}
{assign var="status_text" value=$lng.lbl_file_modified}
{elseif $fileinfo.status eq "U"}
{assign var="status_color" value="brown"}
{assign var="status_text" value=$lng.lbl_unknown_file}
{else}
{assign var="status_color" value=""}
{assign var="status_text" value=""}
{/if}

<tr{cycle values=", class='TableSubHead'"}>
  <td nowrap="nowrap" class="snapshots snapshots-file{$fileinfo.type}">
<img class="snapshots-type" src="{$ImagesDir}/spacer.gif" alt="" />
&nbsp;{$fileinfo.display_filename}
{if $fileinfo.file}
&nbsp;<a href="file_edit.php?dir={$fileinfo.dir}&amp;file={$fileinfo.file}"><img class="file-edit" src="{$ImagesDir}/spacer.gif" alt="" /></a>
{/if}
</td>
  <td>{if $status_color}<font color="{$status_color}">{/if}{$status_text}{if $status_color}</font>{/if}</td>
</tr>
{/foreach}

{else}

<tr>
  <td colspan="2" align="center">{$lng.lbl_no_files_found}</td>
</tr>

{/if}

</table>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_snapshots_comparison_result content=$smarty.capture.dialog extra='width="100%"'}

{/if}

{/if}
