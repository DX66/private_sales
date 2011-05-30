{*
$Id: offer_details.tpl,v 1.1 2010/05/21 08:32:48 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{inc value=$config.Company.end_year assign="end_year"}

<table cellpadding="3" cellspacing="1" width="100%">
<tr>
  <td width="30%" class="FormButton">{$lng.lbl_sp_offer_short_name}:</td>
  <td width="70%"><input type="text" name="offer[name]" value="{$offer.name}" size="50" style="width: 90%;" /></td>
  <td width="10">&nbsp;</td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_active}:</td>
  <td>
  <select name="offer[avail]">
    <option value="N"{if $offer.avail eq "N"} selected="selected"{/if}>{$lng.lbl_no}</option>
    <option value="Y"{if $offer.avail eq "Y"} selected="selected"{/if}>{$lng.lbl_yes}</option>
  </select>
  </td>
  <td>&nbsp;</td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_sp_offer_start_date}:</td>
  <td>{include file="main/datepicker.tpl" name="start_date" date=$offer.offer_start}</td>
  <td>&nbsp;</td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_sp_offer_end_date}:</td>
  <td>{include file="main/datepicker.tpl" name="end_date" date=$offer.offer_end}</td>
  <td>&nbsp;</td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_sp_display_short_promo}:</td>
  <td>
  <select name="offer[show_short_promo]">
    <option value="Y"{if $offer.show_short_promo eq "Y"} selected="selected"{/if}>{$lng.lbl_yes}</option>
    <option value="N"{if $offer.show_short_promo eq "N"} selected="selected"{/if}>{$lng.lbl_no}</option>
  </select>
  </td>
  <td>&nbsp;</td>
</tr>

<tr>
  <td colspan="3" class="SubmitBox"><input type="submit" value=" {$lng.lbl_update} " /></td>
</tr>
</table>
