{*
$Id: cc_ideala.tpl,v 1.1 2010/05/21 08:32:52 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>iDeal advanced</h1>
{$lng.txt_cc_configure_top_text}
<br /><br />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

<table border="0" cellspacing="10">

<tr>
  <td>{$lng.lbl_cc_ida_mid}:</td>
  <td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>

<tr>
  <td>{$lng.lbl_cc_ida_sid}:</td>
  <td><input type="text" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
</tr>

<tr>
  <td>{$lng.lbl_cc_ida_cer}:</td>
  <td><input type="text" name="param03" size="32" value="{$module_data.param03|escape}" /></td>
</tr>

<tr>
  <td>{$lng.lbl_cc_ida_pem}:</td>
  <td><input type="text" name="param04" size="32" value="{$module_data.param04|escape}" /></td>
</tr>

<tr>
  <td>{$lng.lbl_cc_ida_pss}:</td>
  <td><input type="password" name="param05" size="32" value="{$module_data.param05|escape}" /></td>
</tr>

<tr>
  <td>{$lng.lbl_cc_currency}:</td>
  <td>

<select name="param06">
  <option value="EUR"{if $module_data.param06 eq "EUR"} selected="selected"{/if}>EUR</option>
  <option value="USD"{if $module_data.param06 eq "USD"} selected="selected"{/if}>USD</option>
</select>

  </td>
</tr>

<tr>
  <td>{$lng.lbl_language}:</td>
  <td>

<select name="param07">
  <option value="en"{if $module_data.param07 eq "en"} selected="selected"{/if}>English</option>
  <option value="nl"{if $module_data.param07 eq "nl"} selected="selected"{/if}>Dutch</option>
</select>

  </td>
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
  <td><input type="text" name="param09" size="32" value="{$module_data.param09|escape}" /></td>
</tr>

</table>
<br /><br />
<input type="submit" value="{$lng.lbl_update}" />
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra="width=100%"}
