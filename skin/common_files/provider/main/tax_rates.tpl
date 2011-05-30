{*
$Id: tax_rates.tpl,v 1.3 2010/06/08 06:17:42 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<a name="rates"></a>

{capture name=dialog}

{include file="main/subheader.tpl" title=$lng.lbl_tax_rates}

<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
checkboxes_form = 'taxratesform';
checkboxes = new Array({foreach from=$tax_rates item=v key=k}{if $k gt 0},{/if}'to_delete[{$v.rateid}]'{/foreach});

//]]>
</script>
<script type="text/javascript" src="{$SkinDir}/js/change_all_checkboxes.js"></script>

<div style="line-height:170%"><a href="javascript:change_all(true);">{$lng.lbl_check_all}</a> / <a href="javascript:change_all(false);">{$lng.lbl_uncheck_all}</a></div>

<form action="taxes.php" method="post" name="taxratesform">
<input type="hidden" name="mode" value="update_rates" />
<input type="hidden" name="taxid" value="{$tax_details.taxid}" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr class="TableHead">
  <td width="10">&nbsp;</td>
  <td width="30%">{$lng.lbl_zone}</td>
  <td width="20%" align="center">{$lng.lbl_membership}</td>
  <td width="30%" align="center">{$lng.lbl_tax_rate_value}</td>
  <td width="20%" align="center">{$lng.lbl_tax_apply_to}</td>
</tr>

{if $tax_rates}

{section name=tax loop=$tax_rates}

<tr{cycle values=", class='TableSubHead'"}>
  <td><input type="checkbox" name="to_delete[{$tax_rates[tax].rateid}]" /></td>
  <td>{if $tax_rates[tax].zoneid eq 0}{$lng.lbl_zone_default}{else}<a href="{$catalogs.provider}/zones.php?zoneid={$tax_rates[tax].zoneid}">{$tax_rates[tax].zone_name}</a>{/if}</td>
  <td align="center">
<a href="taxes.php?taxid={$tax_details.taxid}&amp;rateid={$tax_rates[tax].rateid}#rates">{foreach from=$tax_rates[tax].membershipids item=m}
{$m}<br />
{foreachelse}
{$lng.lbl_all}
{/foreach}</a>
</td>
  <td align="center" nowrap="nowrap">
<input type="text" size="20" maxlength="13" name="posted_data[{$tax_rates[tax].rateid}][rate_value]" value="{$tax_rates[tax].rate_value|formatprice:false:false:3}" />
<select name="posted_data[{$tax_rates[tax].rateid}][rate_type]">
  <option value="%"{if $tax_rates[tax].rate_type eq "%"} selected="selected"{/if}>%</option>
  <option value="$"{if $tax_rates[tax].rate_type eq "$"} selected="selected"{/if}>{$config.General.currency_symbol}</option>
</select>
  </td>
  <td align="center"><a href="taxes.php?taxid={$tax_details.taxid}&amp;rateid={$tax_rates[tax].rateid}#rates">{if $tax_rates[tax].formula eq ""}{$tax_details.formula}{else}{$tax_rates[tax].formula}{/if}</a></td>
</tr>

{/section}

<tr>
  <td colspan="5" class="SubmitBox">
  <input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if(checkMarks(this.form, new RegExp('to_delete', 'gi'))) submitForm(this, 'delete_rates');" />
  <input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
  </td>
</tr>

{else}

<tr>
  <td colspan="5" align="center">{$lng.txt_no_tax_rates_defined}</td>
</tr>

{/if}

</table>
</form>

<br /><br /><br />

{if $rate_details ne ""}

{**** Edit tax rate ****}

{include file="main/subheader.tpl" title=$lng.lbl_edit_tax_rate}

{include file="main/tax_rate_edit.tpl" mode="mode" taxid=$tax_details.taxid rate_details=$rate_details}

{/if}

{if $rate_details eq ""}

{**** Add tax rate ****}

{include file="main/subheader.tpl" title=$lng.lbl_add_tax_rate}

{include file="main/tax_rate_edit.tpl" mode="mode" taxid=$tax_details.taxid rate_details=""}

{/if}

{/capture}
{include file="dialog.tpl" title="`$tax_details.tax_name`: `$lng.lbl_tax_rates`" content=$smarty.capture.dialog extra='width="100%"'}

