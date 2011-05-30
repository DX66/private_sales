{*
$Id: cc_worldpay.tpl,v 1.1 2010/05/21 08:32:53 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>{$module_data.module_name}</h1>
{$lng.txt_cc_configure_top_text}
<br /><br />
{$lng.txt_cc_worldpay_note|substitute:"current_location":$current_location:"processor":$module_data.processor}
<br /><br />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

<table cellspacing="10">

<tr>
  <td>{$lng.lbl_worldpay_return_url}:</td>
  <td>{$current_location}/payment/{$module_data.processor}</td>
</tr>

<tr>
  <td>{$lng.lbl_cc_worldpay_instanceid}:</td>
  <td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>

<tr>
  <td>{$lng.lbl_cc_currency}:</td>
  <td>
    <select name="param02">
      <option value="ARS"{if $module_data.param02 eq 'ARS'} selected="selected"{/if}>Nuevo Argentine Peso</option>
      <option value="AUD"{if $module_data.param02 eq 'AUD'} selected="selected"{/if}>Australian Dollar</option>
      <option value="BRL"{if $module_data.param02 eq 'BRL'} selected="selected"{/if}>Brazilian Real</option>
      <option value="CAD"{if $module_data.param02 eq 'CAD'} selected="selected"{/if}>Canadian Dollar</option>
      <option value="CHF"{if $module_data.param02 eq 'CHF'} selected="selected"{/if}>Swiss Franc</option>
      <option value="CLP"{if $module_data.param02 eq 'CLP'} selected="selected"{/if}>Chilean Peso</option>
      <option value="CNY"{if $module_data.param02 eq 'CNY'} selected="selected"{/if}>Yuan Renminbi</option>
      <option value="COP"{if $module_data.param02 eq 'COP'} selected="selected"{/if}>Colombian Peso</option>
      <option value="CZK"{if $module_data.param02 eq 'CZK'} selected="selected"{/if}>Czech Koruna</option>
      <option value="DKK"{if $module_data.param02 eq 'DKK'} selected="selected"{/if}>Danish Krone</option>
      <option value="EUR"{if $module_data.param02 eq 'EUR'} selected="selected"{/if}>Euro</option>
      <option value="GBP"{if $module_data.param02 eq 'GBP'} selected="selected"{/if}>Pound Sterling</option>
      <option value="HKD"{if $module_data.param02 eq 'HKD'} selected="selected"{/if}>Hong Kong Dollar</option>
      <option value="HUF"{if $module_data.param02 eq 'HUF'} selected="selected"{/if}>Hungarian Forint</option>
      <option value="IDR"{if $module_data.param02 eq 'IDR'} selected="selected"{/if}>Indonesian Rupiah</option>
      <option value="JPY"{if $module_data.param02 eq 'JPY'} selected="selected"{/if}>Japanese Yen</option>
      <option value="KES"{if $module_data.param02 eq 'KES'} selected="selected"{/if}>Kenyan Shilling</option>
      <option value="KRW"{if $module_data.param02 eq 'KRW'} selected="selected"{/if}>South-Korean Won</option>
      <option value="MXP"{if $module_data.param02 eq 'MXP'} selected="selected"{/if}>Mexican Peso</option>
      <option value="MYR"{if $module_data.param02 eq 'MYR'} selected="selected"{/if}>Malaysian Ringgit</option>
      <option value="NOK"{if $module_data.param02 eq 'NOK'} selected="selected"{/if}>Norwegian Krone</option>
      <option value="NZD"{if $module_data.param02 eq 'NZD'} selected="selected"{/if}>New Zealand Dollar</option>
      <option value="PHP"{if $module_data.param02 eq 'PHP'} selected="selected"{/if}>Philippine Peso</option>
      <option value="PLN"{if $module_data.param02 eq 'PLN'} selected="selected"{/if}>New Polish Zloty</option>
      <option value="PTE"{if $module_data.param02 eq 'PTE'} selected="selected"{/if}>Portugese Escudo</option>
      <option value="SEK"{if $module_data.param02 eq 'SEK'} selected="selected"{/if}>Swedish Krone</option>
      <option value="SGD"{if $module_data.param02 eq 'SGD'} selected="selected"{/if}>Singapore Dollar</option>
      <option value="SKK"{if $module_data.param02 eq 'SKK'} selected="selected"{/if}>Slovak Koruna</option>
      <option value="THB"{if $module_data.param02 eq 'THB'} selected="selected"{/if}>Thai Baht</option>
      <option value="TWD"{if $module_data.param02 eq 'TWD'} selected="selected"{/if}>New Taiwan Dollar</option>
      <option value="USD"{if $module_data.param02 eq 'USD'} selected="selected"{/if}>US Dollars</option>
      <option value="VND"{if $module_data.param02 eq 'VND'} selected="selected"{/if}>Vietnamese New Dong</option>
      <option value="ZAR"{if $module_data.param02 eq 'ZAR'} selected="selected"{/if}>South African Rand</option>
    </select>
  </td>
</tr>

<tr>
  <td>{$lng.lbl_cc_testlive_mode}:</td>
  <td>
    <select name="testmode">
      <option value="T"{if $module_data.testmode eq "T"} selected="selected"{/if}>{$lng.lbl_cc_testlive_wp_authorised}</option>
      <option value="R"{if $module_data.testmode eq "R"} selected="selected"{/if}>{$lng.lbl_cc_testlive_wp_refused}</option>
      <option value="E"{if $module_data.testmode eq "E"} selected="selected"{/if}>{$lng.lbl_cc_testlive_wp_error}</option>
      <option value="C"{if $module_data.testmode eq "C"} selected="selected"{/if}>{$lng.lbl_cc_testlive_wp_captured}</option>
      <option value="N"{if $module_data.testmode eq "N"} selected="selected"{/if}>{$lng.lbl_cc_testlive_live}</option>
    </select>
  </td>
</tr>

<tr>
  <td>{$lng.lbl_cc_worldpay_auto_redirect}:</td>
  <td>
    <select name="param05">
      <option value="Y" {if $module_data.param05 eq 'Y'} selected="selected"{/if}>{$lng.lbl_yes}</option>
      <option value="N" {if $module_data.param05 eq 'N'} selected="selected"{/if}>{$lng.lbl_no}</option>
    </select>
  </td>
</tr>

<tr>
  <td>{$lng.lbl_worldpay_md5_secret}:</td>
  <td><input type="text" name="param06" size="32" maxlength="16" value="{$module_data.param06|escape}" /></td>
</tr>

<tr>
  <td>{$lng.lbl_cc_order_prefix}:</td>
  <td><input type="text" name="param04" size="32" value="{$module_data.param04|escape}" /></td>
</tr>

<tr>
  <td>{$lng.lbl_use_preauth_method}:</td>
  <td>
    <select name="use_preauth">
      <option value="">{$lng.lbl_auth_and_capture_method}</option>
      <option value="Y"{if $module_data.use_preauth eq "Y"} selected="selected"{/if}>{$lng.lbl_auth_method}</option>
    </select>
  </td>
</tr>

</table>
<br /><br />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
