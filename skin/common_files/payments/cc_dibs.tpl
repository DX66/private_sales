{*
$Id: cc_dibs.tpl,v 1.1 2010/05/21 08:32:52 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>{$module_data.module_name}</h1>
{$lng.txt_cc_configure_top_text}
<br /><br />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

<table cellspacing="10" width="100%">

<tr>
  <td width="40%">{$lng.lbl_cc_dibs_merchant}:</td>
  <td width="60%"><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>

{include file="payments/currencies.tpl" param_name='param02' current=$module_data.param02}

<tr>
  <td>{$lng.lbl_cc_dibs_language}:</td>
  <td>
    <select name="param03">
      <option value="da"{if $module_data.param03 eq "da"} selected="selected"{/if}>Danish (default)</option>
      <option value="sv"{if $module_data.param03 eq "sv"} selected="selected"{/if}>Swedish</option>
      <option value="no"{if $module_data.param03 eq "no"} selected="selected"{/if}>Norwegian</option>
      <option value="en"{if $module_data.param03 eq "en"} selected="selected"{/if}>English</option>
      <option value="nl"{if $module_data.param03 eq "nl"} selected="selected"{/if}>Dutch</option>
      <option value="de"{if $module_data.param03 eq "de"} selected="selected"{/if}>German</option>
      <option value="fr"{if $module_data.param03 eq "fr"} selected="selected"{/if}>French</option>
      <option value="fi"{if $module_data.param03 eq "fi"} selected="selected"{/if}>Finnish</option>
      <option value="es"{if $module_data.param03 eq "es"} selected="selected"{/if}>Spanish</option>
      <option value="it"{if $module_data.param03 eq "it"} selected="selected"{/if}>Italian</option>
      <option value="fo"{if $module_data.param03 eq "fo"} selected="selected"{/if}>Faroese</option>
      <option value="pl"{if $module_data.param03 eq "pl"} selected="selected"{/if}>Polish</option>
    </select>
  </td>
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

<tr>
  <td>{$lng.lbl_cc_dibs_paytype}:</td>
  <td>
    <select name="param05">
      <option value="">&nbsp;</option>
      <option{if $module_data.param05 eq "ACK"} selected="selected"{/if} value="ACK">Albertslund Centrum Kundekort</option>
      <option{if $module_data.param05 eq "AMEX"} selected="selected"{/if} value="AMEX">American Express</option>
      <option{if $module_data.param05 eq "AMEX(DK)"} selected="selected"{/if} value="AMEX(DK)">American Express (Danish card)</option>
      <option{if $module_data.param05 eq "BHBC"} selected="selected"{/if} value="BHBC">Bauhaus Best card</option>
      <option{if $module_data.param05 eq "CCK"} selected="selected"{/if} value="CCK">Computer City Customer Card</option>
      <option{if $module_data.param05 eq "CKN"} selected="selected"{/if} value="CKN">CityKort Næstved</option>
      <option{if $module_data.param05 eq "COBK"} selected="selected"{/if} value="COBK">COOP Bank Card</option>
      <option{if $module_data.param05 eq "DIN"} selected="selected"{/if} value="DIN">Diners Club</option>
      <option{if $module_data.param05 eq "DIN(DK)"} selected="selected"{/if} value="DIN(DK)">Diners Club (Danish card)</option>
      <option{if $module_data.param05 eq "DK"} selected="selected"{/if} value="DK">Dankort</option>
      <option{if $module_data.param05 eq "ELEC"} selected="selected"{/if} value="ELEC">VISA Electron (Danish card)</option>
      <option{if $module_data.param05 eq "EWORLD"} selected="selected"{/if} value="EWORLD">Electronic World Credit Card</option>
      <option{if $module_data.param05 eq "FCC"} selected="selected"{/if} value="FCC">Ford Credit Card</option>
      <option{if $module_data.param05 eq "FCK"} selected="selected"{/if} value="FCK">Frederiksberg Centret Kundekort</option>
      <option{if $module_data.param05 eq "FFK"} selected="selected"{/if} value="FFK">Forbrugsforeningen Card</option>
      <option{if $module_data.param05 eq "FFK"} selected="selected"{/if} value="FFK">Forbrugsforeningen Card</option>
      <option{if $module_data.param05 eq "FSC"} selected="selected"{/if} value="FSC">Fisketorvet Shopping Card</option>
      <option{if $module_data.param05 eq "FSBK"} selected="selected"{/if} value="FSBK">Frispar Bank card</option>
      <option{if $module_data.param05 eq "FSSBK"} selected="selected"{/if} value="FSSBK">FöreningsSparbanken Bank card</option>
      <option{if $module_data.param05 eq "GSC"} selected="selected"{/if} value="GSC">Glostrup Shopping Card</option>
      <option{if $module_data.param05 eq "GRA"} selected="selected"{/if} value="GRA">Graphium</option>
      <option{if $module_data.param05 eq "HBSBK"} selected="selected"{/if} value="HBSBK">Handelsbanken Bank card</option>
      <option{if $module_data.param05 eq "HMK"} selected="selected"{/if} value="HMK">HM Konto (Hennes og Mauritz)</option>
      <option{if $module_data.param05 eq "ICASBK"} selected="selected"{/if} value="ICASBK">ICA Bank card</option>
      <option{if $module_data.param05 eq "IBC"} selected="selected"{/if} value="IBC">Inspiration Best Card</option>
      <option{if $module_data.param05 eq "IKEA"} selected="selected"{/if} value="IKEA">IKEA kort</option>
      <option{if $module_data.param05 eq "JPSBK"} selected="selected"{/if} value="JPSBK">JP Bankkort</option>
      <option{if $module_data.param05 eq "JCB"} selected="selected"{/if} value="JCB">JCB (Japan Credit Bureau)</option>
      <option{if $module_data.param05 eq "LIC"} selected="selected"{/if} value="LIC">Lærernes IndkøbsCentral</option>
      <option{if $module_data.param05 eq "MC"} selected="selected"{/if} value="MC">Eurocard/Mastercard</option>
      <option{if $module_data.param05 eq "MC(DK)"} selected="selected"{/if} value="MC(DK)">Eurocard/Mastercard (Danish card)</option>
      <option{if $module_data.param05 eq "MC(SE)"} selected="selected"{/if} value="MC(SE)">Eurocard/Mastercard (Swedish card)</option>
      <option{if $module_data.param05 eq "MTRO"} selected="selected"{/if} value="MTRO">Maestro</option>
      <option{if $module_data.param05 eq "MTRO(DK)"} selected="selected"{/if} value="MTRO(DK)">Maestro (Danish card)</option>
      <option{if $module_data.param05 eq "MEDM"} selected="selected"{/if} value="MEDM">Medmera card</option>
      <option{if $module_data.param05 eq "MERLIN(DK)"} selected="selected"{/if} value="MERLIN(DK)">Merlin Credit card (Danish card)</option>
      <option{if $module_data.param05 eq "MOCA"} selected="selected"{/if} value="MOCA">Mobilcash</option>
      <option{if $module_data.param05 eq "NSBK"} selected="selected"{/if} value="NSBK">Nordea Bank card</option>
      <option{if $module_data.param05 eq "OESBK"} selected="selected"{/if} value="OESBK">Östgöta Enskilda bank card</option>
      <option{if $module_data.param05 eq "PGSBK"} selected="selected"{/if} value="PGSBK">PostGirot Bank card</option>
      <option{if $module_data.param05 eq "Q8SK"} selected="selected"{/if} value="Q8SK">Q8 Service card</option>
      <option{if $module_data.param05 eq "Q8LIC"} selected="selected"{/if} value="Q8LIC">Q8 Service card</option>
      <option{if $module_data.param05 eq "RK"} selected="selected"{/if} value="RK">Rejsekonto</option>
      <option{if $module_data.param05 eq "SLV"} selected="selected"{/if} value="SLV">Silvan card</option>
      <option{if $module_data.param05 eq "SBSBK"} selected="selected"{/if} value="SBSBK">Skandiabanken Bank card</option>
      <option{if $module_data.param05 eq "S/T"} selected="selected"{/if} value="S/T">Spies/Tjæreborg card</option>
      <option{if $module_data.param05 eq "SBC"} selected="selected"{/if} value="SBC">Spies Best Card</option>
      <option{if $module_data.param05 eq "SBK"} selected="selected"{/if} value="SBK">Swedish bank card</option>
      <option{if $module_data.param05 eq "SEBSBK"} selected="selected"{/if} value="SEBSBK">Swedish bank card (SEB)</option>
      <option{if $module_data.param05 eq "TKTD"} selected="selected"{/if} value="TKTD">Tjæreborg Customer card</option>
      <option{if $module_data.param05 eq "TUBC"} selected="selected"{/if} value="TUBC">Toys R Us - BestCard</option>
      <option{if $module_data.param05 eq "TLK"} selected="selected"{/if} value="TLK">Tæppeland card</option>
      <option{if $module_data.param05 eq "VSC"} selected="selected"{/if} value="VSC">Vestsjællandscentret card</option>
      <option{if $module_data.param05 eq "V-DK"} selected="selected"{/if} value="V-DK">VISA/Dankort</option>
      <option{if $module_data.param05 eq "VEKO"} selected="selected"{/if} value="VEKO">VEKO card (Danish card)</option>
      <option{if $module_data.param05 eq "VISA"} selected="selected"{/if} value="VISA">VISA card</option>
      <option{if $module_data.param05 eq "VISA(DK)"} selected="selected"{/if} value="VISA(DK)">VISA (Danish card)</option>
      <option{if $module_data.param05 eq "VISA(SE)"} selected="selected"{/if} value="VISA(SE)">VISA (Swedish card)</option>
      <option{if $module_data.param05 eq "ELEC"} selected="selected"{/if} value="ELEC">VISA Electron (Danish card)</option>
      <option{if $module_data.param05 eq "WOCO"} selected="selected"{/if} value="WOCO">Wonderful Copenhagen Card</option>
      <option{if $module_data.param05 eq "AAK"} selected="selected"{/if} value="AAK">Århus City Card</option>
      <option{if $module_data.param05 eq "ACCEPT"} selected="selected"{/if} value="ACCEPT">Accept card</option>
    </select>
  </td>
</tr>

<tr>
  <td>{$lng.lbl_cc_dibs_shop_key} #1:</td>
  <td><input type="text" name="param06" size="32" value="{$module_data.param06|escape}" /></td>
</tr>

<tr>
  <td>{$lng.lbl_cc_dibs_shop_key} #2:</td>
  <td><input type="text" name="param07" size="32" value="{$module_data.param07|escape}" /></td>
</tr>

<tr>
  <td>{$lng.lbl_cc_dibs_calc_fee}:</td>
  <td>
    <select name="param08">
      <option value=""{if $module_data.param08 eq ""} selected="selected"{/if}>{$lng.lbl_no}</option>
      <option value="Y"{if $module_data.param08 eq "Y"} selected="selected"{/if}>{$lng.lbl_yes}</option>
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

</table>

<br /><br />

<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
