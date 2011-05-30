{*
$Id: title_selector.tpl,v 1.1 2010/05/21 08:32:19 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<select name="{$name|default:"title"}" id="{$id|default:"title"}">
{if $titles}
{foreach from=$titles item=v}
  <option value="{if $use_title_id eq "Y"}{$v.titleid}{else}{$v.title_orig|escape}{/if}"{if $val eq $v.titleid} selected="selected"{/if}>{$v.title}</option>
{/foreach}
{else}
  <option value="{if $use_title_id eq "Y"}{$val}{/if}" selected="selected">{$lng.txt_no_titles_defined}</option>
{/if}
</select>
