{*
$Id: condition_points.tpl,v 1.1 2010/05/21 08:32:49 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<table width="100%" cellspacing="3" cellpadding="0" border="0">
<tr>
  <td nowrap="nowrap">{$lng.lbl_sp_min_amount}:</td>
  <td>&nbsp;</td>
  <td width="100%">{$condition.amount_min|string_format:"%d"}</td>
</tr>
<tr valign="top">
  <td nowrap="nowrap">{$lng.lbl_sp_max_amount}:</td>
  <td>&nbsp;</td>
  <td width="100%">{if $condition.amount_max le 0}-{else}{$condition.amount_max|string_format:"%d"}{/if}</td>
</tr>
{if $condition.condition_data.reduce_bp eq "Y"}
<tr>
  <td colspan="3">&nbsp;</td>
</tr>
<tr>
  <td colspan="3">{$lng.lbl_sp_condition_set_action}: <br /><i>{$lng.lbl_sp_reduce_bp_on_complete}</i></td>
</tr>
{/if}
</table>
