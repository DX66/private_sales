{*
$Id: cc_usaepay.tpl,v 1.2 2010/08/04 07:15:55 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>USA ePay</h1>
<p>{$lng.txt_cc_configure_top_text}</p>

{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

<table cellspacing="10">

<tr>
  <td>{$lng.lbl_cc_usaepay_key}:</td>
  <td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>

<tr>
  <td>{$lng.lbl_cc_usaepay_pin}:</td>
  <td><input type="text" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
</tr>

<tr>
  <td>{$lng.lbl_cc_testlive_mode}:</td>
  <td>
    <select name="testmode">
      <option value="Y"{if $module_data.testmode eq "Y"} selected="selected"{/if}>{$lng.lbl_cc_testlive_test}</option>
      <option value="N"{if $module_data.testmode eq "N"} selected="selected"{/if}>{$lng.lbl_cc_testlive_live}</option>
      <option value="S"{if $module_data.testmode eq "S"} selected="selected"{/if}>{$lng.lbl_cc_testlive_sandbox}</option>
      <option value="L"{if $module_data.testmode eq "L"} selected="selected"{/if}>{$lng.lbl_cc_testlive_sandbox_live}</option>
    </select>
  </td>
</tr>

<tr>
  <td>{$lng.lbl_cc_currency}:</td>
  <td>
    <select name="param05">
      <option value="971"{if $module_data.param05 eq "971"} selected="selected"{/if}>Afghan Afghani</option>
      <option value="533"{if $module_data.param05 eq "533"} selected="selected"{/if}>Aruban Florin</option>
      <option value="036"{if $module_data.param05 eq "036"} selected="selected"{/if}>Australian Dollars</option>
      <option value="032"{if $module_data.param05 eq "032"} selected="selected"{/if}>Argentine Peso</option>
      <option value="944"{if $module_data.param05 eq "944"} selected="selected"{/if}>Azerbaijanian Manat</option>
      <option value="044"{if $module_data.param05 eq "044"} selected="selected"{/if}>Bahamian Dollar</option>
      <option value="050"{if $module_data.param05 eq "050"} selected="selected"{/if}>Bangladeshi Taka</option>
      <option value="052"{if $module_data.param05 eq "052"} selected="selected"{/if}>Barbados Dollar</option>
      <option value="974"{if $module_data.param05 eq "974"} selected="selected"{/if}>Belarussian Rouble</option>
      <option value="068"{if $module_data.param05 eq "068"} selected="selected"{/if}>Bolivian Boliviano</option>
      <option value="986"{if $module_data.param05 eq "986"} selected="selected"{/if}>Brazilian Real</option>
      <option value="826"{if $module_data.param05 eq "826"} selected="selected"{/if}>British Pounds Sterling</option>
      <option value="975"{if $module_data.param05 eq "975"} selected="selected"{/if}>Bulgarian Lev</option>
      <option value="116"{if $module_data.param05 eq "116"} selected="selected"{/if}>Cambodia Riel</option>
      <option value="124"{if $module_data.param05 eq "124"} selected="selected"{/if}>Canadian Dollars</option>
      <option value="136"{if $module_data.param05 eq "136"} selected="selected"{/if}>Cayman Islands Dollar</option>
      <option value="152"{if $module_data.param05 eq "152"} selected="selected"{/if}>Chilean Peso</option>
      <option value="156"{if $module_data.param05 eq "156"} selected="selected"{/if}>Chinese Renminbi Yuan</option>
      <option value="170"{if $module_data.param05 eq "170"} selected="selected"{/if}>Colombian Peso</option>
      <option value="188"{if $module_data.param05 eq "188"} selected="selected"{/if}>Costa Rican Colon</option>
      <option value="191"{if $module_data.param05 eq "191"} selected="selected"{/if}>Croatia Kuna</option>
      <option value="196"{if $module_data.param05 eq "196"} selected="selected"{/if}>Cypriot Pounds</option>
      <option value="203"{if $module_data.param05 eq "203"} selected="selected"{/if}>Czech Koruna</option>
      <option value="208"{if $module_data.param05 eq "208"} selected="selected"{/if}>Danish Krone</option>
      <option value="214"{if $module_data.param05 eq "214"} selected="selected"{/if}>Dominican Republic Peso</option>
      <option value="951"{if $module_data.param05 eq "951"} selected="selected"{/if}>East Caribbean Dollar</option>
      <option value="818"{if $module_data.param05 eq "818"} selected="selected"{/if}>Egyptian Pound</option>
      <option value="232"{if $module_data.param05 eq "232"} selected="selected"{/if}>Eritrean Nakfa</option>
      <option value="233"{if $module_data.param05 eq "233"} selected="selected"{/if}>Estonia Kroon</option>
      <option value="978"{if $module_data.param05 eq "978"} selected="selected"{/if}>Euro</option>
      <option value="981"{if $module_data.param05 eq "981"} selected="selected"{/if}>Georgian Lari</option>
      <option value="288"{if $module_data.param05 eq "288"} selected="selected"{/if}>Ghana Cedi</option>
      <option value="292"{if $module_data.param05 eq "292"} selected="selected"{/if}>Gibraltar Pound</option>
      <option value="320"{if $module_data.param05 eq "320"} selected="selected"{/if}>Guatemala Quetzal</option>
      <option value="340"{if $module_data.param05 eq "340"} selected="selected"{/if}>Honduras Lempira</option>
      <option value="344"{if $module_data.param05 eq "344"} selected="selected"{/if}>Hong Kong Dollars</option>
      <option value="348"{if $module_data.param05 eq "348"} selected="selected"{/if}>Hungary Forint</option>
      <option value="352"{if $module_data.param05 eq "352"} selected="selected"{/if}>Icelandic Krona</option>
      <option value="356"{if $module_data.param05 eq "356"} selected="selected"{/if}>Indian Rupee</option>
      <option value="360"{if $module_data.param05 eq "360"} selected="selected"{/if}>Indonesia Rupiah</option>
      <option value="376"{if $module_data.param05 eq "376"} selected="selected"{/if}>Israel Shekel</option>
      <option value="388"{if $module_data.param05 eq "388"} selected="selected"{/if}>Jamaican Dollar</option>
      <option value="392"{if $module_data.param05 eq "392"} selected="selected"{/if}>Japanese yen</option>
      <option value="368"{if $module_data.param05 eq "368"} selected="selected"{/if}>Kazakhstan Tenge</option>
      <option value="404"{if $module_data.param05 eq "404"} selected="selected"{/if}>Kenyan Shilling</option>
      <option value="414"{if $module_data.param05 eq "414"} selected="selected"{/if}>Kuwaiti Dinar</option>
      <option value="428"{if $module_data.param05 eq "428"} selected="selected"{/if}>Latvia Lat</option>
      <option value="422"{if $module_data.param05 eq "422"} selected="selected"{/if}>Lebanese Pound</option>
      <option value="440"{if $module_data.param05 eq "440"} selected="selected"{/if}>Lithuania Litas</option>
      <option value="446"{if $module_data.param05 eq "446"} selected="selected"{/if}>Macau Pataca</option>
      <option value="807"{if $module_data.param05 eq "807"} selected="selected"{/if}>Macedonian Denar</option>
      <option value="969"{if $module_data.param05 eq "969"} selected="selected"{/if}>Malagascy Ariary</option>
      <option value="458"{if $module_data.param05 eq "458"} selected="selected"{/if}>Malaysian Ringgit</option>
      <option value="470"{if $module_data.param05 eq "470"} selected="selected"{/if}>Maltese Lira</option>
      <option value="977"{if $module_data.param05 eq "977"} selected="selected"{/if}>Marka</option>
      <option value="480"{if $module_data.param05 eq "480"} selected="selected"{/if}>Mauritius Rupee</option>
      <option value="484"{if $module_data.param05 eq "484"} selected="selected"{/if}>Mexican Pesos</option>
      <option value="508"{if $module_data.param05 eq "508"} selected="selected"{/if}>Mozambique Metical</option>
      <option value="524"{if $module_data.param05 eq "524"} selected="selected"{/if}>Nepalese Rupee</option>
      <option value="532"{if $module_data.param05 eq "532"} selected="selected"{/if}>Netherlands Antilles Guilder</option>
      <option value="901"{if $module_data.param05 eq "901"} selected="selected"{/if}>New Taiwanese Dollars</option>
      <option value="554"{if $module_data.param05 eq "554"} selected="selected"{/if}>New Zealand Dollars</option>
      <option value="558"{if $module_data.param05 eq "558"} selected="selected"{/if}>Nicaragua Cordoba</option>
      <option value="566"{if $module_data.param05 eq "566"} selected="selected"{/if}>Nigeria Naira</option>
      <option value="408"{if $module_data.param05 eq "408"} selected="selected"{/if}>North Korean Won</option>
      <option value="578"{if $module_data.param05 eq "578"} selected="selected"{/if}>Norwegian Krone</option>
      <option value="512"{if $module_data.param05 eq "512"} selected="selected"{/if}>Omani Riyal</option>
      <option value="586"{if $module_data.param05 eq "586"} selected="selected"{/if}>Pakistani Rupee</option>
      <option value="600"{if $module_data.param05 eq "600"} selected="selected"{/if}>Paraguay Guarani</option>
      <option value="604"{if $module_data.param05 eq "604"} selected="selected"{/if}>Peru New Sol</option>
      <option value="608"{if $module_data.param05 eq "608"} selected="selected"{/if}>Philippine Pesos</option>
      <option value="634"{if $module_data.param05 eq "634"} selected="selected"{/if}>Qatari Riyal</option>
      <option value="946"{if $module_data.param05 eq "946"} selected="selected"{/if}>Romanian New Leu</option>
      <option value="643"{if $module_data.param05 eq "643"} selected="selected"{/if}>Russian Federation Ruble</option>
      <option value="682"{if $module_data.param05 eq "682"} selected="selected"{/if}>Saudi Riyal</option>
      <option value="891"{if $module_data.param05 eq "891"} selected="selected"{/if}>Serbian Dinar</option>
      <option value="690"{if $module_data.param05 eq "690"} selected="selected"{/if}>Seychelles Rupee</option>
      <option value="702"{if $module_data.param05 eq "702"} selected="selected"{/if}>Singapore Dollars</option>
      <option value="703"{if $module_data.param05 eq "703"} selected="selected"{/if}>Slovak Koruna</option>
      <option value="705"{if $module_data.param05 eq "705"} selected="selected"{/if}>Slovenia Tolar</option>
      <option value="710"{if $module_data.param05 eq "710"} selected="selected"{/if}>South African Rand</option>
      <option value="410"{if $module_data.param05 eq "410"} selected="selected"{/if}>South Korean Won</option>
      <option value="144"{if $module_data.param05 eq "144"} selected="selected"{/if}>Sri Lankan Rupee</option>
      <option value="968"{if $module_data.param05 eq "968"} selected="selected"{/if}>Surinam Dollar</option>
      <option value="752"{if $module_data.param05 eq "752"} selected="selected"{/if}>Swedish Krona</option>
      <option value="756"{if $module_data.param05 eq "756"} selected="selected"{/if}>Swiss Francs</option>
      <option value="834"{if $module_data.param05 eq "834"} selected="selected"{/if}>Tanzanian Shilling</option>
      <option value="764"{if $module_data.param05 eq "764"} selected="selected"{/if}>Thai Baht</option>
      <option value="780"{if $module_data.param05 eq "780"} selected="selected"{/if}>Trinidad and Tobago Dollar</option>
      <option value="949"{if $module_data.param05 eq "949"} selected="selected"{/if}>Turkish New Lira</option>
      <option value="784"{if $module_data.param05 eq "784"} selected="selected"{/if}>UAE Dirham</option>
      <option value="840"{if $module_data.param05 eq "840"} selected="selected"{/if}>US Dollars</option>
      <option value="800"{if $module_data.param05 eq "800"} selected="selected"{/if}>Ugandian Shilling</option>
      <option value="980"{if $module_data.param05 eq "980"} selected="selected"{/if}>Ukraine Hryvna</option>
      <option value="858"{if $module_data.param05 eq "858"} selected="selected"{/if}>Uruguayan Peso</option>
      <option value="860"{if $module_data.param05 eq "860"} selected="selected"{/if}>Uzbekistani Som</option>
      <option value="862"{if $module_data.param05 eq "862"} selected="selected"{/if}>Venezuela Bolivar</option>
      <option value="704"{if $module_data.param05 eq "704"} selected="selected"{/if}>Vietnam Dong</option>
      <option value="894"{if $module_data.param05 eq "894"} selected="selected"{/if}>Zambian Kwacha</option>
      <option value="716"{if $module_data.param05 eq "716"} selected="selected"{/if}>Zimbabwe Dollar</option>
    </select>
  </td>
</tr>

<tr>
  <td>{$lng.lbl_cc_order_prefix}:</td>
  <td><input type="text" name="param03" size="32" value="{$module_data.param03|escape}" /></td>
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

<br /><br />
<center>{$lng.txt_vbv_admin_note|substitute:"ImagesDir":$ImagesDir}

{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
