{*
$Id: cc_paypointft.tpl,v 1.1 2010/05/21 08:32:52 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>PayPoint Fast Track</h1>
{$lng.txt_cc_configure_top_text}
<br /><br />
{$lng.txt_cc_ppft_note|substitute:"current_location":$current_location:"processor":$module_data.processor}
<br /><br />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

<table cellspacing="10">

<tr>
<td>{$lng.txt_cc_ppft_instid}:</td>
<td><input type="text" name="param01" size="32" maxlength="6" value="{$module_data.param01|escape}" /></td>
</tr>

<tr>
<td>{$lng.txt_cc_ppft_auth_user}:</td>
<td><input type="text" name="param04" size="32" value="{$module_data.param04|escape}" /></td>
</tr>

<tr>
<td>{$lng.txt_cc_ppft_auth_pass}:</td>
<td><input type="text" name="param05" size="32" value="{$module_data.param05|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td>
<select name="param02">
<option{if $module_data.param02 eq "AFA"} selected="selected"{/if} value="AFA">Afghani</option>
<option{if $module_data.param02 eq "ALL"} selected="selected"{/if} value="ALL">Lek</option>
<option{if $module_data.param02 eq "DZD"} selected="selected"{/if} value="DZD">Algerian Dinar</option>
<option{if $module_data.param02 eq "AON"} selected="selected"{/if} value="AON">New Kwanza</option>
<option{if $module_data.param02 eq "ARS"} selected="selected"{/if} value="ARS">Argentine Peso</option>
<option{if $module_data.param02 eq "AWG"} selected="selected"{/if} value="AWG">Aruban Guilder</option>
<option{if $module_data.param02 eq "AUD"} selected="selected"{/if} value="AUD">Australian Dollar</option>
<option{if $module_data.param02 eq "ATS"} selected="selected"{/if} value="ATS">Schilling</option>
<option{if $module_data.param02 eq "BSD"} selected="selected"{/if} value="BSD">Bahamian Dollar</option>
<option{if $module_data.param02 eq "BHD"} selected="selected"{/if} value="BHD">Bahraini Dinar</option>
<option{if $module_data.param02 eq "BDT"} selected="selected"{/if} value="BDT">Taka</option>
<option{if $module_data.param02 eq "BBD"} selected="selected"{/if} value="BBD">Barbados Dollar</option>
<option{if $module_data.param02 eq "BEF"} selected="selected"{/if} value="BEF">Belgian Franc</option>
<option{if $module_data.param02 eq "BZD"} selected="selected"{/if} value="BZD">Belize Dollar</option>
<option{if $module_data.param02 eq "BMD"} selected="selected"{/if} value="BMD">Bermudian Dollar</option>
<option{if $module_data.param02 eq "BOB"} selected="selected"{/if} value="BOB">Boliviano</option>
<option{if $module_data.param02 eq "BAD"} selected="selected"{/if} value="BAD">Bosnian Dinar</option>
<option{if $module_data.param02 eq "BWP"} selected="selected"{/if} value="BWP">Pula</option>
<option{if $module_data.param02 eq "BRL"} selected="selected"{/if} value="BRL">Real</option>
<option{if $module_data.param02 eq "BND"} selected="selected"{/if} value="BND">Brunei Dollar</option>
<option{if $module_data.param02 eq "BGL"} selected="selected"{/if} value="BGL">Lev</option>
<option{if $module_data.param02 eq "XOF"} selected="selected"{/if} value="XOF">CFA Franc BCEAO</option>
<option{if $module_data.param02 eq "BIF"} selected="selected"{/if} value="BIF">Burundi Franc</option>
<option{if $module_data.param02 eq "KHR"} selected="selected"{/if} value="KHR">Cambodia Riel</option>
<option{if $module_data.param02 eq "XAF"} selected="selected"{/if} value="XAF">CFA Franc BEAC</option>
<option{if $module_data.param02 eq "CAD"} selected="selected"{/if} value="CAD">Canadian Dollar</option>
<option{if $module_data.param02 eq "CVE"} selected="selected"{/if} value="CVE">Cape Verde Escudo</option>
<option{if $module_data.param02 eq "KYD"} selected="selected"{/if} value="KYD">Cayman Islands Dollar</option>
<option{if $module_data.param02 eq "CLP"} selected="selected"{/if} value="CLP">Chilean Peso</option>
<option{if $module_data.param02 eq "CNY"} selected="selected"{/if} value="CNY">Yuan Renminbi</option>
<option{if $module_data.param02 eq "COP"} selected="selected"{/if} value="COP">Colombian Peso</option>
<option{if $module_data.param02 eq "KMF"} selected="selected"{/if} value="KMF">Comoro Franc</option>
<option{if $module_data.param02 eq "CRC"} selected="selected"{/if} value="CRC">Costa Rican Colon</option>
<option{if $module_data.param02 eq "HRK"} selected="selected"{/if} value="HRK">Croatian Kuna</option>
<option{if $module_data.param02 eq "CUP"} selected="selected"{/if} value="CUP">Cuban Peso</option>
<option{if $module_data.param02 eq "CYP"} selected="selected"{/if} value="CYP">Cyprus Pound</option>
<option{if $module_data.param02 eq "CZK"} selected="selected"{/if} value="CZK">Czech Koruna</option>
<option{if $module_data.param02 eq "DKK"} selected="selected"{/if} value="DKK">Danish Krone</option>
<option{if $module_data.param02 eq "DJF"} selected="selected"{/if} value="DJF">Djibouti Franc</option>
<option{if $module_data.param02 eq "XCD"} selected="selected"{/if} value="XCD">East Caribbean Dollar</option>
<option{if $module_data.param02 eq "DOP"} selected="selected"{/if} value="DOP">Dominican Peso</option>
<option{if $module_data.param02 eq "TPE"} selected="selected"{/if} value="TPE">Timor Escudo</option>
<option{if $module_data.param02 eq "ECS"} selected="selected"{/if} value="ECS">Ecuador Sucre</option>
<option{if $module_data.param02 eq "EGP"} selected="selected"{/if} value="EGP">Egyptian Pound</option>
<option{if $module_data.param02 eq "SVC"} selected="selected"{/if} value="SVC">El Salvador Colon</option>
<option{if $module_data.param02 eq "EEK"} selected="selected"{/if} value="EEK">Kroon</option>
<option{if $module_data.param02 eq "ETB"} selected="selected"{/if} value="ETB">Ethiopian Birr</option>
<option{if $module_data.param02 eq "XEU"} selected="selected"{/if} value="XEU">ECU</option>
<option{if $module_data.param02 eq "EUR"} selected="selected"{/if} value="EUR">European EURO</option>
<option{if $module_data.param02 eq "FKP"} selected="selected"{/if} value="FKP">Falkland Islands Pound</option>
<option{if $module_data.param02 eq "FJD"} selected="selected"{/if} value="FJD">Fiji Dollar</option>
<option{if $module_data.param02 eq "FIM"} selected="selected"{/if} value="FIM">Markka</option>
<option{if $module_data.param02 eq "FRF"} selected="selected"{/if} value="FRF">French Franc</option>
<option{if $module_data.param02 eq "XPF"} selected="selected"{/if} value="XPF">CFP Franc</option>
<option{if $module_data.param02 eq "GMD"} selected="selected"{/if} value="GMD">Dalasi</option>
<option{if $module_data.param02 eq "DEM"} selected="selected"{/if} value="DEM">Deutsche Mark</option>
<option{if $module_data.param02 eq "GHC"} selected="selected"{/if} value="GHC">Cedi</option>
<option{if $module_data.param02 eq "GIP"} selected="selected"{/if} value="GIP">Gibraltar Pound</option>
<option{if $module_data.param02 eq "GRD"} selected="selected"{/if} value="GRD">Drachma</option>
<option{if $module_data.param02 eq "GTQ"} selected="selected"{/if} value="GTQ">Quetzal</option>
<option{if $module_data.param02 eq "GNF"} selected="selected"{/if} value="GNF">Guinea Franc</option>
<option{if $module_data.param02 eq "GWP"} selected="selected"{/if} value="GWP">Guinea - Bissau Peso</option>
<option{if $module_data.param02 eq "GYD"} selected="selected"{/if} value="GYD">Guyana Dollar</option>
<option{if $module_data.param02 eq "HTG"} selected="selected"{/if} value="HTG">Gourde</option>
<option{if $module_data.param02 eq "HNL"} selected="selected"{/if} value="HNL">Lempira</option>
<option{if $module_data.param02 eq "HKD"} selected="selected"{/if} value="HKD">Hong Kong Dollar</option>
<option{if $module_data.param02 eq "HUF"} selected="selected"{/if} value="HUF">Forint</option>
<option{if $module_data.param02 eq "ISK"} selected="selected"{/if} value="ISK">Iceland Krona</option>
<option{if $module_data.param02 eq "INR"} selected="selected"{/if} value="INR">Indian Rupee</option>
<option{if $module_data.param02 eq "IDR"} selected="selected"{/if} value="IDR">Rupiah</option>
<option{if $module_data.param02 eq "IRR"} selected="selected"{/if} value="IRR">Iranian Rial</option>
<option{if $module_data.param02 eq "IQD"} selected="selected"{/if} value="IQD">Iraqi Dinar</option>
<option{if $module_data.param02 eq "IEP"} selected="selected"{/if} value="IEP">Irish Pound</option>
<option{if $module_data.param02 eq "ILS"} selected="selected"{/if} value="ILS">Shekel</option>
<option{if $module_data.param02 eq "ITL"} selected="selected"{/if} value="ITL">Italian Lira</option>
<option{if $module_data.param02 eq "JMD"} selected="selected"{/if} value="JMD">Jamaican Dollar</option>
<option{if $module_data.param02 eq "JPY"} selected="selected"{/if} value="JPY">Yen</option>
<option{if $module_data.param02 eq "JOD"} selected="selected"{/if} value="JOD">Jordanian Dinar</option>
<option{if $module_data.param02 eq "KZT"} selected="selected"{/if} value="KZT">Tenge</option>
<option{if $module_data.param02 eq "KES"} selected="selected"{/if} value="KES">Kenyan Shilling</option>
<option{if $module_data.param02 eq "KRW"} selected="selected"{/if} value="KRW">Won</option>
<option{if $module_data.param02 eq "KPW"} selected="selected"{/if} value="KPW">North Korean Won</option>
<option{if $module_data.param02 eq "KWD"} selected="selected"{/if} value="KWD">Kuwaiti Dinar</option>
<option{if $module_data.param02 eq "KGS"} selected="selected"{/if} value="KGS">Som</option>
<option{if $module_data.param02 eq "LAK"} selected="selected"{/if} value="LAK">Kip</option>
<option{if $module_data.param02 eq "LVL"} selected="selected"{/if} value="LVL">Latvian Lats</option>
<option{if $module_data.param02 eq "LBP"} selected="selected"{/if} value="LBP">Lebanese Pound</option>
<option{if $module_data.param02 eq "LSL"} selected="selected"{/if} value="LSL">Loti</option>
<option{if $module_data.param02 eq "LRD"} selected="selected"{/if} value="LRD">Liberian Dollar</option>
<option{if $module_data.param02 eq "LYD"} selected="selected"{/if} value="LYD">Libyan Dinar</option>
<option{if $module_data.param02 eq "LTL"} selected="selected"{/if} value="LTL">Lithuanian Litas</option>
<option{if $module_data.param02 eq "LUF"} selected="selected"{/if} value="LUF">Luxembourg Franc</option>
<option{if $module_data.param02 eq "MOP"} selected="selected"{/if} value="MOP">Pataca</option>
<option{if $module_data.param02 eq "MKD"} selected="selected"{/if} value="MKD">Denar</option>
<option{if $module_data.param02 eq "MGF"} selected="selected"{/if} value="MGF">Malagasy Franc</option>
<option{if $module_data.param02 eq "MWK"} selected="selected"{/if} value="MWK">Kwacha</option>
<option{if $module_data.param02 eq "MYR"} selected="selected"{/if} value="MYR">Malaysian Ringitt</option>
<option{if $module_data.param02 eq "MVR"} selected="selected"{/if} value="MVR">Rufiyaa</option>
<option{if $module_data.param02 eq "MTL"} selected="selected"{/if} value="MTL">Maltese Lira</option>
<option{if $module_data.param02 eq "MRO"} selected="selected"{/if} value="MRO">Ouguiya</option>
<option{if $module_data.param02 eq "MUR"} selected="selected"{/if} value="MUR">Mauritius Rupee</option>
<option{if $module_data.param02 eq "MXN"} selected="selected"{/if} value="MXN">Mexico Peso</option>
<option{if $module_data.param02 eq "MNT"} selected="selected"{/if} value="MNT">Mongolia Tugrik</option>
<option{if $module_data.param02 eq "MAD"} selected="selected"{/if} value="MAD">Moroccan Dirham</option>
<option{if $module_data.param02 eq "MZM"} selected="selected"{/if} value="MZM">Metical</option>
<option{if $module_data.param02 eq "MMK"} selected="selected"{/if} value="MMK">Myanmar Kyat</option>
<option{if $module_data.param02 eq "NAD"} selected="selected"{/if} value="NAD">Namibian Dollar</option>
<option{if $module_data.param02 eq "NPR"} selected="selected"{/if} value="NPR">Nepalese Rupee</option>
<option{if $module_data.param02 eq "ANG"} selected="selected"{/if} value="ANG">Netherlands Antilles Guilder</option>
<option{if $module_data.param02 eq "NLG"} selected="selected"{/if} value="NLG">Netherlands Guilder</option>
<option{if $module_data.param02 eq "NZD"} selected="selected"{/if} value="NZD">New Zealand Dollar</option>
<option{if $module_data.param02 eq "NIO"} selected="selected"{/if} value="NIO">Cordoba Oro</option>
<option{if $module_data.param02 eq "NGN"} selected="selected"{/if} value="NGN">Naira</option>
<option{if $module_data.param02 eq "NOK"} selected="selected"{/if} value="NOK">Norwegian Krone</option>
<option{if $module_data.param02 eq "OMR"} selected="selected"{/if} value="OMR">Rial Omani </option>
<option{if $module_data.param02 eq "PKR"} selected="selected"{/if} value="PKR">Pakistan Rupee</option>
<option{if $module_data.param02 eq "PAB"} selected="selected"{/if} value="PAB">Balboa</option>
<option{if $module_data.param02 eq "PGK"} selected="selected"{/if} value="PGK">New Guinea Kina</option>
<option{if $module_data.param02 eq "PYG"} selected="selected"{/if} value="PYG">Guarani</option>
<option{if $module_data.param02 eq "PEN"} selected="selected"{/if} value="PEN">Nuevo Sol</option>
<option{if $module_data.param02 eq "PHP"} selected="selected"{/if} value="PHP">Philippine Peso</option>
<option{if $module_data.param02 eq "PLN"} selected="selected"{/if} value="PLN">New Zloty</option>
<option{if $module_data.param02 eq "PTE"} selected="selected"{/if} value="PTE">Portugese Escudo</option>
<option{if $module_data.param02 eq "QAR"} selected="selected"{/if} value="QAR">Qatari Rial</option>
<option{if $module_data.param02 eq "ROL"} selected="selected"{/if} value="ROL">Leu</option>
<option{if $module_data.param02 eq "RUR"} selected="selected"{/if} value="RUR">Russian Ruble</option>
<option{if $module_data.param02 eq "RWF"} selected="selected"{/if} value="RWF">Rwanda Franc</option>
<option{if $module_data.param02 eq "WST"} selected="selected"{/if} value="WST">Tala</option>
<option{if $module_data.param02 eq "STD"} selected="selected"{/if} value="STD">Dobra</option>
<option{if $module_data.param02 eq "SAR"} selected="selected"{/if} value="SAR">Saudi Riyal</option>
<option{if $module_data.param02 eq "SCR"} selected="selected"{/if} value="SCR">Seychelles Rupee</option>
<option{if $module_data.param02 eq "SLL"} selected="selected"{/if} value="SLL">Leone</option>
<option{if $module_data.param02 eq "SGD"} selected="selected"{/if} value="SGD">Singapore Dollar</option>
<option{if $module_data.param02 eq "SKK"} selected="selected"{/if} value="SKK">Slovak Koruna</option>
<option{if $module_data.param02 eq "SIT"} selected="selected"{/if} value="SIT">Tolar</option>
<option{if $module_data.param02 eq "SBD"} selected="selected"{/if} value="SBD">Solomon Islands Dollar</option>
<option{if $module_data.param02 eq "SOS"} selected="selected"{/if} value="SOS">Somalia Shilling</option>
<option{if $module_data.param02 eq "ZAR"} selected="selected"{/if} value="ZAR">Rand</option>
<option{if $module_data.param02 eq "ESP"} selected="selected"{/if} value="ESP">Spanish Peseta</option>
<option{if $module_data.param02 eq "LKR"} selected="selected"{/if} value="LKR">Sri Lanka Rupee</option>
<option{if $module_data.param02 eq "SHP"} selected="selected"{/if} value="SHP">St Helena Pound</option>
<option{if $module_data.param02 eq "SDP"} selected="selected"{/if} value="SDP">Sudanese Pound</option>
<option{if $module_data.param02 eq "SRG"} selected="selected"{/if} value="SRG">Suriname Guilder</option>
<option{if $module_data.param02 eq "SZL"} selected="selected"{/if} value="SZL">Swaziland Lilangeni</option>
<option{if $module_data.param02 eq "SEK"} selected="selected"{/if} value="SEK">Sweden Krona</option>
<option{if $module_data.param02 eq "CHF"} selected="selected"{/if} value="CHF">Swiss Franc</option>
<option{if $module_data.param02 eq "SYP"} selected="selected"{/if} value="SYP">Syrian Pound</option>
<option{if $module_data.param02 eq "TWD"} selected="selected"{/if} value="TWD">New Taiwan Dollar</option>
<option{if $module_data.param02 eq "TJR"} selected="selected"{/if} value="TJR">Tajik Ruble</option>
<option{if $module_data.param02 eq "TZS"} selected="selected"{/if} value="TZS">Tanzanian Shilling</option>
<option{if $module_data.param02 eq "THB"} selected="selected"{/if} value="THB">Baht</option>
<option{if $module_data.param02 eq "TOP"} selected="selected"{/if} value="TOP">Tonga Pa'anga</option>
<option{if $module_data.param02 eq "TTD"} selected="selected"{/if} value="TTD">Trinidad &amp; Tobago Dollar</option>
<option{if $module_data.param02 eq "TND"} selected="selected"{/if} value="TND">Tunisian Dinar</option>
<option{if $module_data.param02 eq "TRL"} selected="selected"{/if} value="TRL">Turkish Lira</option>
<option{if $module_data.param02 eq "UGX"} selected="selected"{/if} value="UGX">Uganda Shilling</option>
<option{if $module_data.param02 eq "UAH"} selected="selected"{/if} value="UAH">Ukrainian Hryvnia</option>
<option{if $module_data.param02 eq "AED"} selected="selected"{/if} value="AED">United Arab Emirates Dirham</option>
<option{if $module_data.param02 eq "GBP"} selected="selected"{/if} value="GBP">Pounds Sterling</option>
<option{if $module_data.param02 eq "USD"} selected="selected"{/if} value="USD">US Dollar</option>
<option{if $module_data.param02 eq "UYU"} selected="selected"{/if} value="UYU">Uruguayan Peso</option>
<option{if $module_data.param02 eq "VUV"} selected="selected"{/if} value="VUV">Vanuatu Vatu</option>
<option{if $module_data.param02 eq "VEB"} selected="selected"{/if} value="VEB">Venezuela Bolivar</option>
<option{if $module_data.param02 eq "VND"} selected="selected"{/if} value="VND">Viet Nam Dong</option>
<option{if $module_data.param02 eq "YER"} selected="selected"{/if} value="YER">Yemeni Rial</option>
<option{if $module_data.param02 eq "YUM"} selected="selected"{/if} value="YUM">Yugoslavian New Dinar</option>
<option{if $module_data.param02 eq "ZRN"} selected="selected"{/if} value="ZRN">New Zaire</option>
<option{if $module_data.param02 eq "ZMK"} selected="selected"{/if} value="ZMK">Zambian Kwacha</option>
<option{if $module_data.param02 eq "ZWD"} selected="selected"{/if} value="ZWD">Zimbabwe Dollar</option>
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param06" size="32" value="{$module_data.param06|escape}" /></td>
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
<td>{$lng.lbl_use_preauth_method}:</td>
<td>
  <select name="use_preauth">
    <option value="">{$lng.lbl_auth_and_capture_method}</option>
    <option value="Y"{if $module_data.use_preauth eq 'Y'} selected="selected"{/if}>{$lng.lbl_auth_method}</option>
  </select>
</td>
</tr>

</table>
<br /><br />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
