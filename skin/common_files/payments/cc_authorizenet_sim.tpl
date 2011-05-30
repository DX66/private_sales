{*
$Id: cc_authorizenet_sim.tpl,v 1.1 2010/05/21 08:32:51 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>Authorize.Net: SIM</h1>
{$lng.txt_cc_configure_top_text}
<br /><br />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

<table cellspacing="10">
<tr>
<td>{$lng.lbl_anet_valid_referrer_url}:</td>
<td>{if $module_data.protocol eq 'https'}{$https_location}{else}{$http_location}{/if}/payment/cc_authorizenet_sim.php</td>
</tr>
<tr>
<td>{$lng.lbl_anet_relay_url}:</td>
<td>{if $module_data.protocol eq 'https'}{$https_location}{else}{$http_location}{/if}/payment/cc_authorizenet_sim.php</td>
</tr>
<tr>
<td>{$lng.lbl_cc_authorizenet_login}:</td>
<td><input type="text" name="param01" size="24" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_authorizenet_trans_key}:</td>
<td><input type="text" name="param02" size="24" value="{$module_data.param02|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_asim_timestamp_offset}:</td>
<td><input type="text" name="param06" size="6" maxlength="6" value="{$module_data.param06|default:"0"|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_currency}:</td>
<td>
<select name="param05">
<option value="AFA"{if $module_data.param05 eq "AFA"} selected="selected"{/if}>Afghani (Afghanistan)</option>
<option value="DZD"{if $module_data.param05 eq "DZD"} selected="selected"{/if}>Algerian Dinar (Algeria)</option>
<option value="ADP"{if $module_data.param05 eq "ADP"} selected="selected"{/if}>Andorran Peseta (Andorra)</option>
<option value="ARS"{if $module_data.param05 eq "ARS"} selected="selected"{/if}>Argentine Peso (Argentina)</option>
<option value="AMD"{if $module_data.param05 eq "AMD"} selected="selected"{/if}>Armenian Dram (Armenia)</option>
<option value="AWG"{if $module_data.param05 eq "AWG"} selected="selected"{/if}>Aruban Guilder (Aruba)</option>
<option value="AUD"{if $module_data.param05 eq "AUD"} selected="selected"{/if}>Australian Dollar (Australia)</option>
<option value="AZM"{if $module_data.param05 eq "AZM"} selected="selected"{/if}>Azerbaijanian Manat (Azerbaijan)</option>
<option value="BSD"{if $module_data.param05 eq "BSD"} selected="selected"{/if}>Bahamian Dollar (Bahamas)</option>
<option value="BHD"{if $module_data.param05 eq "BHD"} selected="selected"{/if}>Bahraini Dinar (Bahrain)</option>
<option value="THB"{if $module_data.param05 eq "THB"} selected="selected"{/if}>Baht (Thailand)</option>
<option value="PAB"{if $module_data.param05 eq "PAB"} selected="selected"{/if}>Balboa (Panama)</option>
<option value="BBD"{if $module_data.param05 eq "BBD"} selected="selected"{/if}>Barbados Dollar (Barbados)</option>
<option value="BYB"{if $module_data.param05 eq "BYB"} selected="selected"{/if}>Belarussian Ruble (Belarus)</option>
<option value="BEF"{if $module_data.param05 eq "BEF"} selected="selected"{/if}>Belgian Franc (Belgium)</option>
<option value="BZD"{if $module_data.param05 eq "BZD"} selected="selected"{/if}>Belize Dollar (Belize)</option>
<option value="BMD"{if $module_data.param05 eq "BMD"} selected="selected"{/if}>Bermudian Dollar (Bermuda)</option>
<option value="VEB"{if $module_data.param05 eq "VEB"} selected="selected"{/if}>Bolivar (Venezuela)</option>
<option value="BOB"{if $module_data.param05 eq "BOB"} selected="selected"{/if}>Boliviano (Bolivia)</option>
<option value="BRL"{if $module_data.param05 eq "BRL"} selected="selected"{/if}>Brazilian Real (Brazil)</option>
<option value="BND"{if $module_data.param05 eq "BND"} selected="selected"{/if}>Brunei Dollar (Brunei Darussalam)</option>
<option value="BGN"{if $module_data.param05 eq "BGN"} selected="selected"{/if}>Bulgarian Lev (Bulgaria)</option>
<option value="BIF"{if $module_data.param05 eq "BIF"} selected="selected"{/if}>Burundi Franc (Burundi)</option>
<option value="CAD"{if $module_data.param05 eq "CAD"} selected="selected"{/if}>Canadian Dollar (Canada)</option>
<option value="CVE"{if $module_data.param05 eq "CVE"} selected="selected"{/if}>Cape Verde Escudo (Cape Verde)</option>
<option value="KYD"{if $module_data.param05 eq "KYD"} selected="selected"{/if}>Cayman Islands Dollar (Cayman Islands)</option>
<option value="GHC"{if $module_data.param05 eq "GHC"} selected="selected"{/if}>Cedi (Ghana)</option>
<option value="XOF"{if $module_data.param05 eq "XOF"} selected="selected"{/if}>CFA Franc BCEAO (Guinea-Bissau)</option>
<option value="XAF"{if $module_data.param05 eq "XAF"} selected="selected"{/if}>CFA Franc BEAC (Central African Republic)</option>
<option value="XPF"{if $module_data.param05 eq "XPF"} selected="selected"{/if}>CFP Franc (New Caledonia)</option>
<option value="CLP"{if $module_data.param05 eq "CLP"} selected="selected"{/if}>Chilean Peso (Chile)</option>
<option value="COP"{if $module_data.param05 eq "COP"} selected="selected"{/if}>Colombian Peso (Colombia)</option>
<option value="KMF"{if $module_data.param05 eq "KMF"} selected="selected"{/if}>Comoro Franc (Comoros)</option>
<option value="BAM"{if $module_data.param05 eq "BAM"} selected="selected"{/if}>Convertible Marks (Bosnia And Herzegovina)</option>
<option value="NIO"{if $module_data.param05 eq "NIO"} selected="selected"{/if}>Cordoba Oro (Nicaragua)</option>
<option value="CRC"{if $module_data.param05 eq "CRC"} selected="selected"{/if}>Costa Rican Colon (Costa Rica)</option>
<option value="CUP"{if $module_data.param05 eq "CUP"} selected="selected"{/if}>Cuban Peso (Cuba)</option>
<option value="CYP"{if $module_data.param05 eq "CYP"} selected="selected"{/if}>Cyprus Pound (Cyprus)</option>
<option value="CZK"{if $module_data.param05 eq "CZK"} selected="selected"{/if}>Czech Koruna (Czech Republic)</option>
<option value="GMD"{if $module_data.param05 eq "GMD"} selected="selected"{/if}>Dalasi (Gambia)</option>
<option value="DKK"{if $module_data.param05 eq "DKK"} selected="selected"{/if}>Danish Krone (Denmark)</option>
<option value="MKD"{if $module_data.param05 eq "MKD"} selected="selected"{/if}>Denar (The Former Yugoslav Republic Of Macedonia)</option>
<option value="DEM"{if $module_data.param05 eq "DEM"} selected="selected"{/if}>Deutsche Mark (Germany)</option>
<option value="AED"{if $module_data.param05 eq "AED"} selected="selected"{/if}>Dirham (United Arab Emirates)</option>
<option value="DJF"{if $module_data.param05 eq "DJF"} selected="selected"{/if}>Djibouti Franc (Djibouti)</option>
<option value="STD"{if $module_data.param05 eq "STD"} selected="selected"{/if}>Dobra (Sao Tome And Principe)</option>
<option value="DOP"{if $module_data.param05 eq "DOP"} selected="selected"{/if}>Dominican Peso (Dominican Republic)</option>
<option value="VND"{if $module_data.param05 eq "VND"} selected="selected"{/if}>Dong (Vietnam)</option>
<option value="GRD"{if $module_data.param05 eq "GRD"} selected="selected"{/if}>Drachma (Greece)</option>
<option value="XCD"{if $module_data.param05 eq "XCD"} selected="selected"{/if}>East Caribbean Dollar (Grenada)</option>
<option value="EGP"{if $module_data.param05 eq "EGP"} selected="selected"{/if}>Egyptian Pound (Egypt)</option>
<option value="SVC"{if $module_data.param05 eq "SVC"} selected="selected"{/if}>El Salvador Colon (El Salvador)</option>
<option value="ETB"{if $module_data.param05 eq "ETB"} selected="selected"{/if}>Ethiopian Birr (Ethiopia)</option>
<option value="EUR"{if $module_data.param05 eq "EUR"} selected="selected"{/if}>Euro (Europe)</option>
<option value="FKP"{if $module_data.param05 eq "FKP"} selected="selected"{/if}>Falkland Islands Pound (Falkland Islands)</option>
<option value="FJD"{if $module_data.param05 eq "FJD"} selected="selected"{/if}>Fiji Dollar (Fiji)</option>
<option value="HUF"{if $module_data.param05 eq "HUF"} selected="selected"{/if}>Forint (Hungary)</option>
<option value="CDF"{if $module_data.param05 eq "CDF"} selected="selected"{/if}>Franc Congolais (The Democratic Republic Of Congo)</option>
<option value="FRF"{if $module_data.param05 eq "FRF"} selected="selected"{/if}>French Franc (France)</option>
<option value="GIP"{if $module_data.param05 eq "GIP"} selected="selected"{/if}>Gibraltar Pound (Gibraltar)</option>
<option value="XAU"{if $module_data.param05 eq "XAU"} selected="selected"{/if}>Gold</option>
<option value="HTG"{if $module_data.param05 eq "HTG"} selected="selected"{/if}>Gourde (Haiti)</option>
<option value="PYG"{if $module_data.param05 eq "PYG"} selected="selected"{/if}>Guarani (Paraguay)</option>
<option value="GNF"{if $module_data.param05 eq "GNF"} selected="selected"{/if}>Guinea Franc (Guinea)</option>
<option value="GWP"{if $module_data.param05 eq "GWP"} selected="selected"{/if}>Guinea-Bissau Peso (Guinea-Bissau)</option>
<option value="GYD"{if $module_data.param05 eq "GYD"} selected="selected"{/if}>Guyana Dollar (Guyana)</option>
<option value="HKD"{if $module_data.param05 eq "HKD"} selected="selected"{/if}>Hong Kong Dollar (Hong Kong)</option>
<option value="UAH"{if $module_data.param05 eq "UAH"} selected="selected"{/if}>Hryvnia (Ukraine)</option>
<option value="ISK"{if $module_data.param05 eq "ISK"} selected="selected"{/if}>Iceland Krona (Iceland)</option>
<option value="INR"{if $module_data.param05 eq "INR"} selected="selected"{/if}>Indian Rupee (India)</option>
<option value="IRR"{if $module_data.param05 eq "IRR"} selected="selected"{/if}>Iranian Rial (Islamic Republic Of Iran)</option>
<option value="IQD"{if $module_data.param05 eq "IQD"} selected="selected"{/if}>Iraqi Dinar (Iraq)</option>
<option value="IEP"{if $module_data.param05 eq "IEP"} selected="selected"{/if}>Irish Pound (Ireland)</option>
<option value="ITL"{if $module_data.param05 eq "ITL"} selected="selected"{/if}>Italian Lira (Italy)</option>
<option value="JMD"{if $module_data.param05 eq "JMD"} selected="selected"{/if}>Jamaican Dollar (Jamaica)</option>
<option value="JOD"{if $module_data.param05 eq "JOD"} selected="selected"{/if}>Jordanian Dinar (Jordan)</option>
<option value="KES"{if $module_data.param05 eq "KES"} selected="selected"{/if}>Kenyan Shilling (Kenya)</option>
<option value="PGK"{if $module_data.param05 eq "PGK"} selected="selected"{/if}>Kina (Papua New Guinea)</option>
<option value="LAK"{if $module_data.param05 eq "LAK"} selected="selected"{/if}>Kip (Lao People's Democratic Republic)</option>
<option value="EEK"{if $module_data.param05 eq "EEK"} selected="selected"{/if}>Kroon (Estonia)</option>
<option value="HRK"{if $module_data.param05 eq "HRK"} selected="selected"{/if}>Kuna (Croatia)</option>
<option value="KWD"{if $module_data.param05 eq "KWD"} selected="selected"{/if}>Kuwaiti Dinar (Kuwait)</option>
<option value="MWK"{if $module_data.param05 eq "MWK"} selected="selected"{/if}>Kwacha (Malawi)</option>
<option value="ZMK"{if $module_data.param05 eq "ZMK"} selected="selected"{/if}>Kwacha (Zambia)</option>
<option value="AOR"{if $module_data.param05 eq "AOR"} selected="selected"{/if}>Kwanza Reajustado (Angola)</option>
<option value="MMK"{if $module_data.param05 eq "MMK"} selected="selected"{/if}>Kyat (Myanmar)</option>
<option value="GEL"{if $module_data.param05 eq "GEL"} selected="selected"{/if}>Lari (Georgia)</option>
<option value="LVL"{if $module_data.param05 eq "LVL"} selected="selected"{/if}>Latvian Lats (Latvia)</option>
<option value="LBP"{if $module_data.param05 eq "LBP"} selected="selected"{/if}>Lebanese Pound (Lebanon)</option>
<option value="ALL"{if $module_data.param05 eq "ALL"} selected="selected"{/if}>Lek (Albania)</option>
<option value="HNL"{if $module_data.param05 eq "HNL"} selected="selected"{/if}>Lempira (Honduras)</option>
<option value="SLL"{if $module_data.param05 eq "SLL"} selected="selected"{/if}>Leone (Sierra Leone)</option>
<option value="ROL"{if $module_data.param05 eq "ROL"} selected="selected"{/if}>Leu (Romania)</option>
<option value="BGL"{if $module_data.param05 eq "BGL"} selected="selected"{/if}>Lev (Bulgaria)</option>
<option value="LRD"{if $module_data.param05 eq "LRD"} selected="selected"{/if}>Liberian Dollar (Liberia)</option>
<option value="LYD"{if $module_data.param05 eq "LYD"} selected="selected"{/if}>Libyan Dinar (Libyan Arab Jamahiriya)</option>
<option value="SZL"{if $module_data.param05 eq "SZL"} selected="selected"{/if}>Lilangeni (Swaziland)</option>
<option value="LTL"{if $module_data.param05 eq "LTL"} selected="selected"{/if}>Lithuanian Litas (Lithuania)</option>
<option value="LSL"{if $module_data.param05 eq "LSL"} selected="selected"{/if}>Loti (Lesotho)</option>
<option value="LUF"{if $module_data.param05 eq "LUF"} selected="selected"{/if}>Luxembourg Franc (Luxembourg)</option>
<option value="MGF"{if $module_data.param05 eq "MGF"} selected="selected"{/if}>Malagasy Franc (Madagascar)</option>
<option value="MYR"{if $module_data.param05 eq "MYR"} selected="selected"{/if}>Malaysian Ringgit (Malaysia)</option>
<option value="MTL"{if $module_data.param05 eq "MTL"} selected="selected"{/if}>Maltese Lira (Malta)</option>
<option value="TMM"{if $module_data.param05 eq "TMM"} selected="selected"{/if}>Manat (Turkmenistan)</option>
<option value="FIM"{if $module_data.param05 eq "FIM"} selected="selected"{/if}>Markka (Finland)</option>
<option value="MUR"{if $module_data.param05 eq "MUR"} selected="selected"{/if}>Mauritius Rupee (Mauritius)</option>
<option value="MZM"{if $module_data.param05 eq "MZM"} selected="selected"{/if}>Metical (Mozambique)</option>
<option value="MXN"{if $module_data.param05 eq "MXN"} selected="selected"{/if}>Mexican Peso (Mexico)</option>
<option value="MXV"{if $module_data.param05 eq "MXV"} selected="selected"{/if}>Mexican Unidad de Inversion (Mexico)</option>
<option value="MDL"{if $module_data.param05 eq "MDL"} selected="selected"{/if}>Moldovan Leu (Republic Of Moldova)</option>
<option value="MAD"{if $module_data.param05 eq "MAD"} selected="selected"{/if}>Moroccan Dirham (Morocco)</option>
<option value="BOV"{if $module_data.param05 eq "BOV"} selected="selected"{/if}>Mvdol (Bolivia)</option>
<option value="NGN"{if $module_data.param05 eq "NGN"} selected="selected"{/if}>Naira (Nigeria)</option>
<option value="ERN"{if $module_data.param05 eq "ERN"} selected="selected"{/if}>Nakfa (Eritrea)</option>
<option value="NAD"{if $module_data.param05 eq "NAD"} selected="selected"{/if}>Namibia Dollar (Namibia)</option>
<option value="NPR"{if $module_data.param05 eq "NPR"} selected="selected"{/if}>Nepalese Rupee (Nepal)</option>
<option value="ANG"{if $module_data.param05 eq "ANG"} selected="selected"{/if}>Netherlands (Netherlands)</option>
<option value="NLG"{if $module_data.param05 eq "NLG"} selected="selected"{/if}>Netherlands Guilder (Netherlands)</option>
<option value="YUM"{if $module_data.param05 eq "YUM"} selected="selected"{/if}>New Dinar (Yugoslavia)</option>
<option value="ILS"{if $module_data.param05 eq "ILS"} selected="selected"{/if}>New Israeli Sheqel (Israel)</option>
<option value="AON"{if $module_data.param05 eq "AON"} selected="selected"{/if}>New Kwanza (Angola)</option>
<option value="TWD"{if $module_data.param05 eq "TWD"} selected="selected"{/if}>New Taiwan Dollar (Province Of China Taiwan)</option>
<option value="ZRN"{if $module_data.param05 eq "ZRN"} selected="selected"{/if}>New Zaire (Zaire)</option>
<option value="NZD"{if $module_data.param05 eq "NZD"} selected="selected"{/if}>New Zealand Dollar (New Zealand)</option>
<option value="BTN"{if $module_data.param05 eq "BTN"} selected="selected"{/if}>Ngultrum (Bhutan)</option>
<option value="KPW"{if $module_data.param05 eq "KPW"} selected="selected"{/if}>North Korean Won (Democratic People's Republic Of Korea)</option>
<option value="NOK"{if $module_data.param05 eq "NOK"} selected="selected"{/if}>Norwegian Krone (Norway)</option>
<option value="PEN"{if $module_data.param05 eq "PEN"} selected="selected"{/if}>Nuevo Sol (Peru)</option>
<option value="MRO"{if $module_data.param05 eq "MRO"} selected="selected"{/if}>Ouguiya (Mauritania)</option>
<option value="TOP"{if $module_data.param05 eq "TOP"} selected="selected"{/if}>Pa'anga (Tonga)</option>
<option value="PKR"{if $module_data.param05 eq "PKR"} selected="selected"{/if}>Pakistan Rupee (Pakistan)</option>
<option value="XPD"{if $module_data.param05 eq "XPD"} selected="selected"{/if}>Palladium</option>
<option value="MOP"{if $module_data.param05 eq "MOP"} selected="selected"{/if}>Pataca (Macau)</option>
<option value="UYU"{if $module_data.param05 eq "UYU"} selected="selected"{/if}>Peso Uruguayo (Uruguay)</option>
<option value="PHP"{if $module_data.param05 eq "PHP"} selected="selected"{/if}>Philippine Peso (Philippines)</option>
<option value="XPT"{if $module_data.param05 eq "XPT"} selected="selected"{/if}>Platinum</option>
<option value="PTE"{if $module_data.param05 eq "PTE"} selected="selected"{/if}>Portuguese Escudo (Portugal)</option>
<option value="GBP"{if $module_data.param05 eq "GBP"} selected="selected"{/if}>Pound Sterling (United Kingdom)</option>
<option value="BWP"{if $module_data.param05 eq "BWP"} selected="selected"{/if}>Pula (Botswana)</option>
<option value="QAR"{if $module_data.param05 eq "QAR"} selected="selected"{/if}>Qatari Rial (Qatar)</option>
<option value="GTQ"{if $module_data.param05 eq "GTQ"} selected="selected"{/if}>Quetzal (Guatemala)</option>
<option value="ZAL"{if $module_data.param05 eq "ZAL"} selected="selected"{/if}>Rand (Financial) (Lesotho)</option>
<option value="ZAR"{if $module_data.param05 eq "ZAR"} selected="selected"{/if}>Rand (South Africa)</option>
<option value="OMR"{if $module_data.param05 eq "OMR"} selected="selected"{/if}>Rial Omani (Oman)</option>
<option value="KHR"{if $module_data.param05 eq "KHR"} selected="selected"{/if}>Riel (Cambodia)</option>
<option value="MVR"{if $module_data.param05 eq "MVR"} selected="selected"{/if}>Rufiyaa (Maldives)</option>
<option value="IDR"{if $module_data.param05 eq "IDR"} selected="selected"{/if}>Rupiah (Indonesia)</option>
<option value="RUB"{if $module_data.param05 eq "RUB"} selected="selected"{/if}>Russian Ruble (Russian Federation)</option>
<option value="RUR"{if $module_data.param05 eq "RUR"} selected="selected"{/if}>Russian Ruble (Russian Federation)</option>
<option value="RWF"{if $module_data.param05 eq "RWF"} selected="selected"{/if}>Rwanda Franc (Rwanda)</option>
<option value="SAR"{if $module_data.param05 eq "SAR"} selected="selected"{/if}>Saudi Riyal (Saudi Arabia)</option>
<option value="ATS"{if $module_data.param05 eq "ATS"} selected="selected"{/if}>Schilling (Austria)</option>
<option value="SCR"{if $module_data.param05 eq "SCR"} selected="selected"{/if}>Seychelles Rupee (Seychelles)</option>
<option value="XAG"{if $module_data.param05 eq "XAG"} selected="selected"{/if}>Silver</option>
<option value="SGD"{if $module_data.param05 eq "SGD"} selected="selected"{/if}>Singapore Dollar (Singapore)</option>
<option value="SKK"{if $module_data.param05 eq "SKK"} selected="selected"{/if}>Slovak Koruna (Slovakia)</option>
<option value="SBD"{if $module_data.param05 eq "SBD"} selected="selected"{/if}>Solomon Islands Dollar (Solomon Islands)</option>
<option value="KGS"{if $module_data.param05 eq "KGS"} selected="selected"{/if}>Som (Kyrgyzstan)</option>
<option value="SOS"{if $module_data.param05 eq "SOS"} selected="selected"{/if}>Somali Shilling (Somalia)</option>
<option value="ESP"{if $module_data.param05 eq "ESP"} selected="selected"{/if}>Spanish Peseta (Spain)</option>
<option value="LKR"{if $module_data.param05 eq "LKR"} selected="selected"{/if}>Sri Lanka Rupee (Sri Lanka)</option>
<option value="SHP"{if $module_data.param05 eq "SHP"} selected="selected"{/if}>St Helena Pound (St Helena)</option>
<option value="ECS"{if $module_data.param05 eq "ECS"} selected="selected"{/if}>Sucre (Ecuador)</option>
<option value="SDD"{if $module_data.param05 eq "SDD"} selected="selected"{/if}>Sudanese Dinar (Sudan)</option>
<option value="SRG"{if $module_data.param05 eq "SRG"} selected="selected"{/if}>Surinam Guilder (Suriname)</option>
<option value="SEK"{if $module_data.param05 eq "SEK"} selected="selected"{/if}>Swedish Krona (Sweden)</option>
<option value="CHF"{if $module_data.param05 eq "CHF"} selected="selected"{/if}>Swiss Franc (Switzerland)</option>
<option value="SYP"{if $module_data.param05 eq "SYP"} selected="selected"{/if}>Syrian Pound (Syrian Arab Republic)</option>
<option value="TJR"{if $module_data.param05 eq "TJR"} selected="selected"{/if}>Tajik Ruble (Tajikistan)</option>
<option value="BDT"{if $module_data.param05 eq "BDT"} selected="selected"{/if}>Taka (Bangladesh)</option>
<option value="WST"{if $module_data.param05 eq "WST"} selected="selected"{/if}>Tala (Samoa)</option>
<option value="TZS"{if $module_data.param05 eq "TZS"} selected="selected"{/if}>Tanzanian Shilling (United Republic Of Tanzania)</option>
<option value="KZT"{if $module_data.param05 eq "KZT"} selected="selected"{/if}>Tenge (Kazakhstan)</option>
<option value="TPE"{if $module_data.param05 eq "TPE"} selected="selected"{/if}>Timor Escudo (East Timor)</option>
<option value="SIT"{if $module_data.param05 eq "SIT"} selected="selected"{/if}>Tolar (Slovenia)</option>
<option value="TTD"{if $module_data.param05 eq "TTD"} selected="selected"{/if}>Trinidad and Tobago Dollar (Trinidad And Tobago)</option>
<option value="MNT"{if $module_data.param05 eq "MNT"} selected="selected"{/if}>Tugrik (Mongolia)</option>
<option value="TND"{if $module_data.param05 eq "TND"} selected="selected"{/if}>Tunisian Dinar (Tunisia)</option>
<option value="TRL"{if $module_data.param05 eq "TRL"} selected="selected"{/if}>Turkish Lira (Turkey)</option>
<option value="UGX"{if $module_data.param05 eq "UGX"} selected="selected"{/if}>Uganda Shilling (Uganda)</option>
<option value="ECV"{if $module_data.param05 eq "ECV"} selected="selected"{/if}>Unidad de Valor Constante (Ecuador)</option>
<option value="CLF"{if $module_data.param05 eq "CLF"} selected="selected"{/if}>Unidades de fomento (Chile)</option>
<option value="USN"{if $module_data.param05 eq "USN"} selected="selected"{/if}>US Dollar (Next day) (United States)</option>
<option value="USS"{if $module_data.param05 eq "USS"} selected="selected"{/if}>US Dollar (Same day) (United States)</option>
<option value="USD"{if $module_data.param05 eq "USD"} selected="selected"{/if}>US Dollar (United States)</option>
<option value="UZS"{if $module_data.param05 eq "UZS"} selected="selected"{/if}>Uzbekistan Sum (Uzbekistan)</option>
<option value="VUV"{if $module_data.param05 eq "VUV"} selected="selected"{/if}>Vatu (Vanuatu)</option>
<option value="KRW"{if $module_data.param05 eq "KRW"} selected="selected"{/if}>Won (Republic Of Korea)</option>
<option value="YER"{if $module_data.param05 eq "YER"} selected="selected"{/if}>Yemeni Rial (Yemen)</option>
<option value="JPY"{if $module_data.param05 eq "JPY"} selected="selected"{/if}>Yen (Japan)</option>
<option value="CNY"{if $module_data.param05 eq "CNY"} selected="selected"{/if}>Yuan Renminbi (China)</option>
<option value="ZWD"{if $module_data.param05 eq "ZWD"} selected="selected"{/if}>Zimbabwe Dollar (Zimbabwe)</option>
<option value="PLN"{if $module_data.param05 eq "PLN"} selected="selected"{/if}>Zloty (Poland)</option>
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
<td><input type="text" name="param04" size="36" value="{$module_data.param04|escape}" /></td>
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

<div style="padding: 0 20px">
  {$lng.txt_cc_authnet_tstamp_note}
</div>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
