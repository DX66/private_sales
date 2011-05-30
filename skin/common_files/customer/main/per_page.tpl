{*
$Id: per_page.tpl,v 1.3 2010/07/16 14:01:30 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{strip}
  <span class="per-page-selector">
  <select onchange="javascript:window.location='{$current_location}/{$navigation_script}&amp;objects_per_page=' + this.value;">
    <option value="" selected="selected"></option>
    {foreach from=$per_page_values item="value"}
    <option value="{$value}"{if $value eq $objects_per_page} selected="selected"{/if}>{$value}</option>
    {/foreach}
  </select>
  &nbsp;{$lng.lbl_per_page}
  </span>
{/strip}
