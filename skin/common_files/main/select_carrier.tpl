{*
$Id: select_carrier.tpl,v 1.1 2010/05/21 08:32:18 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<select{if $name} name="{$name}"{/if}{if $id} id="{$id}"{/if}{if $onchange} onchange="{$onchange}"{/if}>
{if $is_ups_carrier_empty ne "Y"}
  <option value="UPS"{if $current_carrier eq "UPS"} selected="selected"{/if}>{$lng.lbl_ups_carrier}</option>
{/if}
{if $is_other_carriers_empty ne "Y"}
  <option value=""{if $current_carrier ne "UPS"} selected="selected"{/if}>{$lng.lbl_other_carriers}</option>
{/if}
</select>
