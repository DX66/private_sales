{*
$Id: cc_fdg_connect.tpl,v 1.5.2.1 2011/03/10 09:01:03 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>{$module_data.module_name}</h1>
{$lng.txt_cc_configure_top_text}
<br /><br />
{$lng.txt_cc_fdg_connect_note|substitute:"current_location":$current_location:"processor":$module_data.processor}
<br />
{capture name=dialog}

<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
var selected_currency = '{$module_data.param02}';
var selected_shared_secret = '{$module_data.param06}';
var selected_timezone = '{$module_data.param07}';

function change_region(region, ss_value) {ldelim}
  
  var currency_box = document.getElementById('currency');
  if (currency_box) {ldelim}
    currency_box.disabled = (region == 'EMEA') ? '' : 'disabled';
    var new_currency = (region == 'EMEA') ? selected_currency : '840';
    var curr_options = currency_box.options;
    if (curr_options)
      for (var i = 0; i < curr_options.length; i++)
        if (curr_options[i].value == new_currency) currency_box.selectedIndex = i;
  {rdelim}

  var shared_secret_box = document.getElementById('shared_secret');
  if (shared_secret_box) {ldelim}
    shared_secret_box.disabled = (region == 'EMEA') ? '' : 'disabled';
    var new_shared_secret = (region == 'EMEA') ? selected_shared_secret : '';
    shared_secret_box.value = new_shared_secret;
  {rdelim}

  var timezone_box = document.getElementById('timezone');
  if (timezone_box) {ldelim}
    timezone_box.disabled = (region == 'EMEA') ? '' : 'disabled';
    var new_timezone = (region == 'EMEA') ? selected_timezone : 'GMT';
    var tmz_options = timezone_box.options;
    if (tmz_options)
      for (var i = 0; i < tmz_options.length; i++)
        if (tmz_options[i].value == new_timezone) timezone_box.selectedIndex = i;
  {rdelim}

  if (region != 'EMEA') {ldelim}
    $('#secure option[value=N]').attr('selected', 'selected');
    $('#secure').attr('disabled', 'disabled');
  {rdelim} else {ldelim}
    $('#secure').removeAttr('disabled');
  {rdelim}


{rdelim}
//]]>
</script>

<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

<table cellspacing="5" cellpadding="5">

<tr>
  <td>{$lng.lbl_cc_fdg_connect_region}:</td>
  <td>
    <select name="param01" onchange="javascript: change_region(this.value);">
      <option{if $module_data.param01 eq "NA"} selected="selected"{/if} value="NA">NA - North America</option>
      <option{if $module_data.param01 eq "EMEA"} selected="selected"{/if} value="EMEA">EMEA - Europe, the Middle East and Africa</option>
    </select>
  </td>
</tr>

<tr>
  <td>{$lng.lbl_cc_currency}:</td>
  <td>
    <select name="param02" id="currency">
      <option value="978"{if $module_data.param02 eq "978"} selected="selected"{/if}>Euro (EUR)</option>
      <option value="826"{if $module_data.param02 eq "826"} selected="selected"{/if}>Pounds Sterling (GBP)</option>
      <option value="840"{if $module_data.param02 eq "840"} selected="selected"{/if}>US Dollar (USD)</option>
      <option value="756"{if $module_data.param02 eq "756"} selected="selected"{/if}>Swiss Francs (CHF)</option>
      <option value="203"{if $module_data.param02 eq "203"} selected="selected"{/if}>Czech Koruna (CZK)</option>
      <option value="206"{if $module_data.param02 eq "206"} selected="selected"{/if}>Danish Krone (DKK)</option>
      <option value="392"{if $module_data.param02 eq "392"} selected="selected"{/if}>Japanese Yen (JPY)</option>
      <option value="710"{if $module_data.param02 eq "710"} selected="selected"{/if}>South African Rand (ZAR)</option>
      <option value="752"{if $module_data.param02 eq "752"} selected="selected"{/if}>Swedish Krona (SEK)</option>
      <option value="124"{if $module_data.param02 eq "124"} selected="selected"{/if}>Canadian Dollar (CAD)</option>
    </select>
  </td>
</tr>

<tr>
  <td>{$lng.lbl_cc_fdg_connect_storeid}:</td>
  <td>
    <input type="text" name="param03" size="32" value="{$module_data.param03|escape}" />
  </td>
</tr>

<tr>
  <td>{$lng.lbl_cc_fdg_secret_key}:</td>
  <td>
    <input type="password" name="param06" id="shared_secret" size="32" value="{$module_data.param06|escape}" />
  </td>
</tr>

<tr>
  <td>{$lng.lbl_cc_fdg_timezone}:</td>
  <td>
    <select name="param07" id="timezone">
      <option{if $module_data.param07 eq "GMT"} selected="selected"{/if} value="GMT">GMT</option>
      <option{if $module_data.param07 eq "CET"} selected="selected"{/if} value="CET">CET</option>
      <option{if $module_data.param07 eq "EET"} selected="selected"{/if} value="EET">EET</option>
    </select>
  </td>
</tr>

<tr>
  <td>{$lng.lbl_cc_fdg_connect_mode}:</td>
  <td>
    <select name="param04">
      <option{if $module_data.param04 eq "fullpay"} selected="selected"{/if} value="fullpay">FullPay</option>
      <option{if $module_data.param04 eq "payonly"} selected="selected"{/if} value="payonly">PayOnly</option>
      <option{if $module_data.param04 eq "payplus"} selected="selected"{/if} value="payplus">PayPlus</option>
    </select>
  </td>
</tr>

<tr>
  <td>{$lng.lbl_cc_3dsecure}:</td>
  <td>
    <select name="param08" id="secure">
      <option value="Y"{if $module_data.param08 eq "Y"} selected="selected"{/if}>{$lng.lbl_enabled}</option>
      <option value="N"{if $module_data.param08 eq "N"} selected="selected"{/if}>{$lng.lbl_disabled}</option>
    </select>
  </td>
</tr>


<tr>
  <td>{$lng.lbl_cc_order_prefix}:</td>
  <td>
    <input type="text" name="param05" size="32" value="{$module_data.param05|escape}" />
  </td>
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
  <td>{$lng.lbl_cc_form_location}:</td>
  <td>
    <select name="disable_ccinfo">
      <option value="N"{if $module_data.disable_ccinfo eq "N"} selected="selected"{/if}>{$lng.lbl_cc_form_location_store}</option>
      <option value="Y"{if $module_data.disable_ccinfo eq "Y"} selected="selected"{/if}>{$lng.lbl_cc_form_location_pg}</option>
    </select>
  </td>
</tr>

</table>

<br /><br />

<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />

</form>

<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
change_region('{$module_data.param01}', '{$module_data.param06}');
//]]>
</script>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
