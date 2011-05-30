{*
$Id: partner_report.tpl,v 1.1.2.1 2010/12/15 09:44:38 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_partner_accounts}
{$lng.txt_partner_accounts_note}<br /><br />

<br />

{capture name=dialog}
{$lng.txt_partner_accounts_comment}<br />
<br />
<a href="partner_report.php">{$lng.lbl_all_accounts}</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="partner_report.php?use_limit=Y">{$lng.lbl_accounts_ready_to_be_paid}</a><br />
<br />
{if $result ne ''}
<form action="partner_report.php" method="post">
<input type="hidden" name="mode" value="paid" />

<table cellpadding="2" cellspacing="2" width="100%">
<tr class="TableHead">
  <td rowspan="2">{$lng.lbl_partner}</td>
    <td colspan="4" align="center">{$lng.lbl_commissions}</td>
{if $is_paid eq 'Y'}
    <td rowspan="2" align="center">{$lng.lbl_ready_to_be_paid}</td>
{/if}
</tr>
<tr class="TableHead">
    <td align="center">{$lng.lbl_paid}</td>
    <td align="center">{$lng.lbl_approved}</td>
    <td align="center">{$lng.lbl_pending}</td>
  <td align="center">{$lng.lbl_min_limit}</td>
</tr>
{foreach from=$result item=v}
<tr{cycle values=", class='TableSubHead'"}>
  <td>{$v.firstname} {$v.lastname}</td>
  <td align="right" nowrap="nowrap">{currency value=$v.sum_paid}</td>
  <td align="right" nowrap="nowrap">{currency value=$v.sum_nopaid}</td>
    <td align="right" nowrap="nowrap">{currency value=$v.sum}</td>
    <td align="right" nowrap="nowrap">{currency value=$v.min_paid}</td>
{if $is_paid eq 'Y'}
  <td align="center">{if $v.is_paid eq 'Y'}<input type="checkbox" name="paid[{$v.id}]" value="Y" />{/if}</td>
{/if}
</tr>
{/foreach}
</table>
{if $is_paid eq 'Y'}
<input type="submit" value="{$lng.lbl_paid|strip_tags:false|escape}" /><br />
{/if}
</form>

<br />

<form action="partner_report.php" method="post">
<input type="hidden" name="mode" value="export" />

{include file="main/subheader.tpl" title=$lng.lbl_export_partner_account}
<table cellpadding="2" cellspacing="2">
<tr>
  <td height="10" class="FormButton">{$lng.lbl_csv_delimiter}:</td>
  <td height="10" width="10">&nbsp;</td>
  <td height="10">{include file="provider/main/ie_delimiter.tpl"}</td>
</tr>
<tr>
    <td height="10" class="FormButton">&nbsp;</td>
    <td height="10" width="10">&nbsp;</td>
    <td height="10"><input type="submit" name="export" value="{$lng.lbl_export|strip_tags:false|escape}" /></td>
</tr>
</table>
</form>

{/if}
{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_partner_accounts extra='width="100%"'} 
