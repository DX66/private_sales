{*
$Id: select_currency.tpl,v 1.1 2010/05/21 08:32:18 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<select name="{if $name ne ""}{$name}{else}selected_currency{/if}"{if $id} id="{$id}"{/if}{if $onchange} onchange="{$onchange}"{/if}>
  {foreach from=$currencies item=currency}
  <option value="{if $use_curr_int_code eq "Y"}{$currency.code_int}{else}{$currency.code}{/if}"{if $current_currency eq $currency.code} selected="selected"{/if}>{$currency.name} ({$currency.code})</option>
  {/foreach}
</select>
