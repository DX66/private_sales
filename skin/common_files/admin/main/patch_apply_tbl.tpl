{*
$Id: patch_apply_tbl.tpl,v 1.2.2.1 2011/01/18 08:53:17 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<table cellpadding="3" cellspacing="1" width="100%">
<tr>
  <td height="14" class="TableHead" nowrap="nowrap">{$lng.lbl_file}</td>
  <td height="14" class="TableHead" nowrap="nowrap" width="100">{$lng.lbl_status}</td>
</tr>
{assign var="prefix" value=$prefix|default:'pf_'}
{section name=index loop=$files}

{if $files[index].status eq 1}
  {assign var=aclass value="1"}
{elseif $files[index].status eq 9}
  {assign var=aclass value="2"}
{else}
  {assign var=aclass value="3"}
{/if}
 
<tr {if %index.index% mod 2 eq 0} class="TableLine"{/if}>
  <td class="patch-status patch-status-{$aclass}">{$files[index].orig_file}</td>
  <td>
    {if $confirmed ne '' and $files[index].status eq 1}
      {assign var="status_tooltip" value=$lng.txt_file_x_successfully_patched|substitute:'file':''}
    {else}
      {assign var="status_tooltip" value=$files[index].status_txt|default:$files[index].status_lbl}
    {/if}
    {include file="main/tooltip_js.tpl" class="patch-status patch-status-`$aclass`" text=$status_tooltip title=$files[index].status_lbl id=$prefix|cat:''|cat:$smarty.section.index.index}
  </td>
</tr>
{/section}
</table>
