{*
$Id: cc_postfinanceac.tpl,v 1.1 2010/05/21 08:32:52 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>Post Finance (Advanced e-Commerce)</h1>
{$lng.txt_cc_configure_top_text}<br />
<br />
<p>{$lng.lbl_cc_postfinanceac_top_note|substitute:current_location:$current_location:processor:$module_data.processor}</p>

{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

<table cellspacing="10">

<tr>
  <td>{$lng.lbl_cc_postfinanceac_pspid}:</td>
  <td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>

<tr>
  <td>{$lng.lbl_cc_postfinanceac_add_string}:</td>
  <td><input type="text" name="param03" size="32" value="{$module_data.param03|escape}" /></td>
</tr>

<tr>
  <td>{$lng.lbl_cc_postfinanceac_add_string_out}:</td>
  <td><input type="text" name="param05" size="32" value="{$module_data.param05|escape}" /></td>
</tr>

{include file="payments/currencies.tpl" param_name='param02' current=$module_data.param02}

<tr>
  <td>{$lng.lbl_cc_testlive_mode}:</td>
  <td>
    <select name="testmode">
      <option value="Y"{if $module_data.testmode eq "Y"} selected="selected"{/if}>{$lng.lbl_cc_testlive_test}</option>
      <option value="N"{if $module_data.testmode eq "N"} selected="selected"{/if}>{$lng.lbl_cc_testlive_live}</option>
    </select>
  </td>
</tr>

<tr>
  <td>{$lng.lbl_cc_order_prefix}:</td>
  <td><input type="text" name="param04" size="32" value="{$module_data.param04|escape}" /></td>
</tr>

</table>
<br />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
