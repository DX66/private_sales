{*
$Id: cc_quantum_ilf.tpl,v 1.1 2010/05/21 08:32:53 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>{$module_data.module_name}</h1>

{$lng.txt_cc_configure_top_text}
<br /><br />

{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
{$lng.txt_cc_quantum_ilf_note|substitute:"current_location":$current_location:"processor":$module_data.processor}
<br /><br />
<b>{$lng.lbl_note}:</b> {$lng.txt_cc_quantum_acc_note}
<br /><br />

<table cellpadding="10">
<tr>
  <td>{$lng.lbl_cc_quantum_api_username}:</td>
  <td><input type="text" name="param01" size="24" value="{$module_data.param01|escape}" /></td>
</tr>

<tr>
  <td>{$lng.lbl_cc_quantum_api_key}:</td>
  <td><input type="text" name="param02" size="24" value="{$module_data.param02|escape}" />
</tr>

<tr>
  <td>{$lng.lbl_cc_quantum_show_shipping}:</td>
  <td>
    <select name="param03">
      <option value="N"{if $module_data.param03 eq 'N'} selected="selected"{/if}>{$lng.lbl_no}</option>
      <option value="Y"{if $module_data.param03 eq 'Y'} selected="selected"{/if}>{$lng.lbl_yes}</option>
    </select>
  </td>
</tr>

<tr>
  <td>{$lng.lbl_cc_order_prefix}:</td>
  <td><input type="text" name="param04" size="36" value="{$module_data.param04|escape}" /></td>
</tr>
</table>

<br /><br />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />

</form>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
