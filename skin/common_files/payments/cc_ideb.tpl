{*
$Id: cc_ideb.tpl,v 1.1 2010/05/21 08:32:52 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>DIBS</h1>
{$lng.txt_cc_configure_top_text}
<br /><br />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor}" method="post">

<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_ideb_mid}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>

<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td><select name="param04">
<option value="208"{if $module_data.param04 eq "208"} selected="selected"{/if}>Danish Kroner (DKK)</option>
<option value="978"{if $module_data.param04 eq "978"} selected="selected"{/if}>Euro (EUR)</option>
<option value="840"{if $module_data.param04 eq "840"} selected="selected"{/if}>US Dollar $ (USD)</option>
<option value="826"{if $module_data.param04 eq "826"} selected="selected"{/if}>English Pound lb (GBP)</option>
<option value="752"{if $module_data.param04 eq "752"} selected="selected"{/if}>Swedish Kroner (SEK)</option>
<option value="036"{if $module_data.param04 eq "036"} selected="selected"{/if}>Australian Dollar (AUD)</option>
<option value="124"{if $module_data.param04 eq "124"} selected="selected"{/if}>Canadian Dollar (CAD)</option>
<option value="352"{if $module_data.param04 eq "352"} selected="selected"{/if}>Icelandic Kroner (ISK)</option>
<option value="392"{if $module_data.param04 eq "392"} selected="selected"{/if}>Japanese Yen (JPY)</option>
<option value="554"{if $module_data.param04 eq "554"} selected="selected"{/if}>New Zealand Dollar (NZD)</option>
<option value="578"{if $module_data.param04 eq "578"} selected="selected"{/if}>Norwegian Kroner (NOK)</option>
<option value="756"{if $module_data.param04 eq "756"} selected="selected"{/if}>Swiss Franc (CHF)</option>
<option value="949"{if $module_data.param04 eq "949"} selected="selected"{/if}>Turkish Lire (TRY)</option>
</select></td></tr>

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
<td><input type="text" name="param05" size="32" value="{$module_data.param05|escape}" /></td>
</tr>

<tr>
<td nowrap="nowrap">{$lng.lbl_use_preauth_method}:</td>
<td>
  <select name="use_preauth">
    <option value="">{$lng.lbl_auth_and_capture_method}</option>
    <option value="Y"{if $module_data.use_preauth eq 'Y'} selected="selected"{/if}>{$lng.lbl_auth_method}</option>
  </select>
</td>
</tr>

<tr>
<td>{$lng.lbl_password}:</td>
<td><input type="password" name="param06" size="32" value="{$module_data.param06|escape}" /></td>
</tr>

</table>
<br /><br />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>
<br /><br />{$lng.lbl_cc_ideb_note}

{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
