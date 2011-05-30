{*
$Id: pconf_hidden_form_fields.tpl,v 1.1 2010/05/21 08:32:46 joy Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
{if $pconf_productid and $pconf_slot}
  <input type="hidden" name="pconf_productid" value="{$pconf_productid}" />
  <input type="hidden" name="pconf_slot" value="{$pconf_slot}" />
{/if}

