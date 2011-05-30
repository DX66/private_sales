{*
$Id: cc_esec.tpl,v 1.1 2010/05/21 08:32:52 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>eSec</h1>
{$lng.txt_cc_configure_top_text}
<br /><br />
{$lng.txt_cc_esec}
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_esec_merchant}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_testlive_mode}:</td>
<td>
<select name="testmode">
<option value="A"{if $module_data.testmode eq "A"} selected="selected"{/if}>{$lng.lbl_cc_testlive_test_a}</option>
<option value="D"{if $module_data.testmode eq "D"} selected="selected"{/if}>{$lng.lbl_cc_testlive_test_d}</option>
<option value="N"{if $module_data.testmode eq "N"} selected="selected"{/if}>{$lng.lbl_cc_testlive_live}</option>
</select>
</td>
</tr>
<tr>
  <td>{$lng.lbl_cc_esec_3dsecure_enabled}:</td>
  <td>
    <select name="param04">
      <option value=""{if $module_data.param04 eq ""} selected="selected"{/if}>{$lng.lbl_cc_esec_3dsecure_enabled_n}</option>
      <option value="Y"{if $module_data.param04 eq "Y"} selected="selected"{/if}>{$lng.lbl_cc_esec_3dsecure_enabled_y}</option>
    </select>
  </td>
</tr>
<tr>
  <td>{$lng.lbl_cc_esec_eps_password}:</td>
  <td><input type="password" name="param05" size="32" value="{$module_data.param05|escape}" /></td>
</tr>
<tr>
  <td>{$lng.lbl_cc_esec_eps_merchantid}:</td>
  <td><input type="text" name="param06" size="32" value="{$module_data.param06|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param03" size="32" value="{$module_data.param03|escape}" /></td>
</tr>
</table>
<br /><br />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
