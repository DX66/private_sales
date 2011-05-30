{*
$Id: paypal_section.tpl,v 1.1 2010/05/21 08:32:51 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<tr>
  <td>{$lng.txt_xpc_paypal_dp_equals_option}:</td>
  <td>
    <select name="{$conf_prefix}[use_xpc]">
      <option value="Y"{if $xpc_data.use eq 'Y'} selected="selected"{/if}>{$lng.lbl_yes}</option>
      <option value=""{if $xpc_data.use ne 'Y'} selected="selected"{/if}>{$lng.lbl_no}</option>
    </select><br />
    <font class="SmallText">{$lng.txt_xpc_paypal_dp_note}</font>
  </td>
</tr>

<tr>
  <td>{$lng.txt_xpc_paypal_dp_equals_list}:</td>
  <td>
    <select name="{$conf_prefix}[use_xpc_processor]">
      {if $xpc_data.warning eq 'no processor'}
        <option value="">{$lng.lbl_please_select_one}</option>
      {/if}
      {foreach from=$xpc_data.processors item=p}
        <option value="{$p.param01}"{if $p.selected} selected="selected"{/if}>{$p.module_name}</option>
      {/foreach}
    </select>
  </td>
</tr>

{if $xpc_data.warning eq 'no configured'}
<tr>
  <td>&nbsp;</td>
  <td><strong>{$lng.lbl_warning}!</strong> {$lng.txt_xpc_paypal_dp_empty_warning}</td>
</tr>

{elseif $xpc_data.warning eq 'no equal'}
<tr>
  <td>&nbsp;</td>
  <td><strong>{$lng.lbl_warning}!</strong> {$lng.txt_xpc_paypal_dp_equal_warning}</td>
</tr>
{/if}
