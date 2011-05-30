{*
$Id: export_specs.tpl,v 1.1 2010/05/21 08:32:17 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{foreach from=$export_spec item=v key=k}
  <tr{if $level eq 0} class="TableSubHead"{/if}>
    <td>
      {if $level eq 0}
        <input type="checkbox" id="check_{$k}" name="check[{$k}]" value="Y" />
      {else}
        &nbsp;
      {/if}
    </td>
    <td nowrap="nowrap" class="exportspec-level-{$level}">
      <table cellspacing="0" cellpadding="0">
        <tr>
          {if $level gt 0}
            <td width="25"><input type="checkbox" id="check_{$k}" name="check[{$k}]" value="Y" /></td>
          {/if}
            <td nowrap="nowrap"><label for="check_{$k}">{$v.display_title}</label></td>
        </tr>
      </table>
    </td>
    {if $level gt 0 or $v.is_range eq ''}
      <td colspan="2">&nbsp;</td>
    {else}
      <td width="25" align="center" nowrap="nowrap">
        {if $v.range_count eq -1}
          {$lng.lbl_all}
        {else}
          {$v.range_count}
        {/if}
      </td>
      <td nowrap="nowrap">
        <a href="{$v.is_range}">{$lng.lbl_change_data_range}</a>
        {if $v.range_count ne -1}
          &nbsp;/&nbsp;
          <a href="import.php?mode=export&amp;action=clear_range&amp;section={$k}">{$lng.lbl_remove_data_range}</a>
        {/if}
      </td>
    {/if}
  </tr>
  {if $level eq 0}
    <tr>
      <td colspan="4" class="SubHeaderGreyLine"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
    </tr>
  {/if}
  {if $v.subsections ne ''}
    {include file="main/export_specs.tpl" export_spec=$v.subsections level=$level+1}
  {/if}
{/foreach}

