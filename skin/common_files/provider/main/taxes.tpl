{*
$Id: taxes.tpl,v 1.3 2010/06/08 06:17:42 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_tax_rates}

{$lng.txt_tax_rates_general_note}

<br /><br />

<br />

{capture name=dialog}

<form action="taxes.php" method="post" name="taxesform">
<input type="hidden" name="mode" value="apply" />

{if $taxes}

<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
checkboxes_form = 'taxesform';
checkboxes = new Array({foreach from=$taxes item=v key=k}{if $k gt 0},{/if}'to_delete[{$v.taxid}]'{/foreach});
//]]>
</script>
<script type="text/javascript" src="{$SkinDir}/js/change_all_checkboxes.js"></script>

<div style="line-height:170%"><a href="javascript:change_all(true);">{$lng.lbl_check_all}</a> / <a href="javascript:change_all(false);">{$lng.lbl_uncheck_all}</a></div>
{/if}

<table cellpadding="3" cellspacing="1" width="100%">

<tr class="TableHead">
  <td width="5%">&nbsp;</td>
  <td width="65%">{$lng.lbl_tax_name}</td>
  <td width="30%" align="center">{$lng.lbl_status}</td>
</tr>

{if $taxes}

{section name=tax loop=$taxes}

<tr{cycle values=", class='TableSubHead'"}>
  <td><input type="checkbox" name="to_delete[{$taxes[tax].taxid}]" /></td>
  <td>
<a href="taxes.php?taxid={$taxes[tax].taxid}">{$taxes[tax].tax_name|replace:" ":"&nbsp;"}</a>
({$lng.txt_N_rates_defined|substitute:"rates":$taxes[tax].rates_count})
  </td>
  <td align="center">{if $taxes[tax].active eq "Y"}{$lng.lbl_enabled}{else}{$lng.lbl_disabled}{/if}</td>
</tr>

{/section}
<tr>
  <td>&nbsp;</td>
</tr>
<tr>
  <td colspan="3"><input type="button" value="{$lng.lbl_apply_selected_taxes_to_all_products|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('to_delete\[[0-9]+\]', 'gi'))) submitForm(this, 'apply');"/></td>
</tr>
{else}

<tr>
  <td colspan="3" align="center">{$lng.txt_no_taxes_defined}</td>
</tr>

{/if}

</table>
</form>

<br /><br />

{/capture}
{include file="dialog.tpl" title=$lng.lbl_taxes content=$smarty.capture.dialog extra='width="100%"'}

