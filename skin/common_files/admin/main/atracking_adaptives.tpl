{*
$Id: atracking_adaptives.tpl,v 1.1 2010/05/21 08:31:58 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $statistics}
<table cellspacing="1" class="DataSheet">
<tr class="DataSheet">
  <th align="left">{$lng.lbl_browser}</th>
  <th>{$lng.lbl_platform}</th>
  <th>{$lng.lbl_screen_resolution}</th>
  <th>{$lng.lbl_java}</th>
  <th>{$lng.lbl_javascript}</th>
  <th>{$lng.lbl_cookie}</th>
  <th>{$lng.lbl_number}</th>
</tr>

{foreach from=$statistics item=v}
<tr>
  <td nowrap="nowrap">{if $v.browser ne ''}{$v.browser} {$v.version}{else}{$lng.lbl_unknown}{/if}</td>
  <td align="center" nowrap="nowrap">{$v.platform|default:$lng.lbl_unknown}</td>
  <td align="center" nowrap="nowrap">{if $v.screen_x gt 0}{$v.screen_x}x{$v.screen_y}{else}{$lng.lbl_unknown}{/if}</td>
  <td align="center">{if $v.java eq 'Y'}{$lng.lbl_enabled}{else}{$lng.lbl_disabled}{/if}</td>
  <td align="center">{if $v.js eq 'Y'}{$lng.lbl_enabled}{else}{$lng.lbl_disabled}{/if}</td>
  <td align="center">{if $v.cookie eq 'Y'}{$lng.lbl_enabled}{else}{$lng.lbl_disabled}{/if}</td>
  <td align="center">{$v.count}</td>
</tr>
{/foreach}

</table>

{else}

<br />
<center>{$lng.txt_no_statistics}</center>

{/if}

