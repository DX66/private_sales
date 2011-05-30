{*
$Id: offer_languages.tpl,v 1.1 2010/05/21 08:32:48 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript" src="{$SkinDir}/js/popup_image_selection.js"></script>

<!-- PROMO BLOCKS -->
<table>
{if $all_languages_cnt gt 1}
<tr>
  <td colspan="3" align="right" valign="middle">{$lng.lbl_language}:
  <select name="offer_lng_code" onchange="self.location='offers.php?mode=promo&amp;offerid={$offerid}&amp;offer_lng_code='+document.wizardform.offer_lng_code.value;">
{foreach from=$all_languages item=ol}
    <option value="{$ol.code}"{if $ol.code eq $offer_lng_code} selected="selected"{/if}>{$ol.language}</option>
{/foreach}
  </select>
  </td>
</tr>
{elseif $offer_lng_code ne ''}
<tr style="display: none;">
  <td><input type="hidden" name="offer_lng_code" value="{$offer_lng_code|escape}" /></td>
</tr>
{/if}

<tr>
  <td colspan="3">

<input type="hidden" name="img_del_code" value="" />
<table cellpadding="3" cellspacing="0" width="100%">
<tr>
  <td><b>{$lng.lbl_sp_promo_short}:</b></td>
</tr>
<tr>
  <td>

<table cellpadding="3" width="100%">
<tr>
  <td>{$lng.lbl_sp_promo_image}</td>
{if $offer_lng.promo_short_img ne '1'}{assign var="no_delete" value="Y"}{/if}
  <td width="95%">
{include file="main/edit_image.tpl" type="S" id=$offer_lng.code|cat:$offerid delete_url="offers.php?mode=promo&amp;action=delete_image&amp;img_del_code=`$offer_lng.code``$offerid`&amp;offerid=`$offerid`&amp;offer_lng_code=`$offer_lng.code`" button_name=$lng.lbl_update no_delete=$no_delete}
  </td>
</tr>
<tr>
  <td>{$sp_promo_texts.promo_short}</td>
  <td width="95%">
{include file="main/textarea.tpl" name="offer_lng[`$offer_lng.code`][promo_short]" cols=40 rows=3 data=$offer_lng.promo_short width="100%" style="width: 100%;" btn_rows=4}
<br />
{$lng.txt_sp_promo_short_note|substitute:"customer_url":$catalogs.customer:"offerid":$offer.offerid}
  </td>
</tr>
</table>

  </td>
</tr>

{foreach from=$sp_promo_texts item=label key=field}
{if $field ne "promo_short"}
<tr>
  <td><br /><b>{$label}:</b></td>
</tr>
<tr>
  <td>
{include file="main/textarea.tpl" name="offer_lng[`$offer_lng.code`][$field]" cols=50 rows=10 data=$offer_lng[$field] width="100%" style="width: 100%;" btn_rows=4}
  </td>
</tr>
{/if}
{/foreach}

</table>

  </td>
</tr>

<tr align="left">
  <td colspan="3">

<br /><br />

<table width="100%">
<tr>
  <td><input type="submit" value=" {$lng.lbl_update} " /></td>
</tr>
</table>

<hr />
{$lng.txt_sp_promo_blocks_detailed_descr}
  </td>
</tr>

</table>
