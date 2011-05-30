{*
$Id: condition_total.tpl,v 1.1.2.1 2010/12/15 09:44:41 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<table width="100%" cellspacing="3" cellpadding="0" border="0">
<tr>
  <td nowrap="nowrap">{$lng.lbl_sp_min_amount}:</td>
  <td>&nbsp;</td>
  <td width="100%">{currency value=$condition.amount_min}</td>
</tr>
<tr valign="top">
  <td nowrap="nowrap">{$lng.lbl_sp_max_amount}:</td>
  <td>&nbsp;</td>
  <td width="100%">{if $condition.amount_max le 0}-{else}{currency value=$condition.amount_max}{/if}</td>
</tr>
</table>
