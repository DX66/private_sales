{*
$Id: bonus_membership.tpl,v 1.1 2010/05/21 08:32:48 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $bonus.bonus_data eq ""}
{$lng.txt_sp_empty_params_bonus_generic_edit}
<br /><br />
{/if}
<select name="bonus[{$bonus.bonus_type}][memberships][]" size="5" multiple="multiple">
{foreach from=$bonus.memberships item=membership key=membershipid}
  <option value="{$membershipid}"{if $membership.selected} selected="selected"{/if}>{$membership.name|escape}</option>
{/foreach}
</select>
<br />
<input type="submit" value=" {$lng.lbl_update} " />
