{*
$Id: bonus_points.tpl,v 1.1.2.1 2010/12/15 09:44:41 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<table width="100%" cellspacing="3" cellpadding="0" border="0">
{if $bonus.amount_type ne "S"}
<tr>
  <td nowrap="nowrap">{$lng.lbl_sp_points_fixed_amount}:</td>
  <td>&nbsp;</td>
  <td width="100%">{$bonus.amount_min|string_format:"%d"}</td>
</tr>
{else}
<tr>
  <td nowrap="nowrap">
  {assign var="total_type" value=$bonus.bonus_data.total_type}
  {if $total_type eq ""}
    {assign var="total_type" value="ST"}
  {/if}
  {$lng.lbl_amount}:
  </td>
  <td>&nbsp;</td>
  <td width="100%">{$bonus.amount_min|string_format:"%d"} {$lng.lbl_sp_points_per_} {currency value=$bonus.amount_max} {$lng.lbl_of} {$sp_total_types[$total_type]}</td>
</tr>
{/if}
</table>
