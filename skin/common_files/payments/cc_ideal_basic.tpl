{*
$Id: cc_ideal_basic.tpl,v 1.1 2010/05/21 08:32:52 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>iDEAL Basic</h1>
{$lng.txt_cc_configure_top_text}
<br /><br />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

<table cellspacing="10">

<tr>
  <td>{$lng.lbl_ideal_basic_mid}:</td>
  <td><input type="text" name="param04" size="24" value="{$module_data.param04|escape}" /></td>
</tr>

<tr>
  <td>{$lng.lbl_ideal_basic_skey}:</td>
  <td><input type="password" name="param03" size="48" value="{$module_data.param03|escape}" /></td>
</tr>

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
  <td><input type="text" name="param05" maxlength="5" size="24" value="{$module_data.param05|escape}" />({$lng.lbl_ideal_basic_limit})</td>
</tr>

</table>
<br /><br />
<input type="submit" value="{$lng.lbl_update|escape}" />
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra="width=100%"}
