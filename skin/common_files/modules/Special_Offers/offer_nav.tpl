{*
$Id: offer_nav.tpl,v 1.1 2010/05/21 08:32:48 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<table width="100%">
<tr>
  <td>&nbsp;</td>
<td>

<table align="center" cellspacing="5">
<tr>
{foreach name=nav from=$nav_data item=nav}
  <td>
{if $nav.mode eq ""}
  {$nav.title}
{else}
{if $nav.mode eq $mode}
{assign var="tmp_title" value="<b>`$nav.title`</b>"}
{else}
{assign var="tmp_title" value=$nav.title}
{/if}
  <a href="offers.php?offerid={$offerid}&amp;mode={$nav.mode}">{$tmp_title}</a>
{/if}
  </td>
{/foreach}
</tr>
</table>

</td>
<td width="1%" nowrap="nowrap">

{assign var="tmp_title" value=$lng.lbl_sp_offer_status}
{if $offer.valid}
{assign var="tmp_link_style" value=' style="COLOR: green;"'}
{else}
{assign var="tmp_link_style" value=' style="COLOR: red;"'}
{/if}

{if $mode eq "status"}
{assign var="tmp_title" value="<b>`$tmp_title`</b>"}
{/if}
<a {$tmp_link_style} href="offers.php?offerid={$offerid}&amp;mode=status">{$tmp_title}</a>

</td>
</tr>
</table>
<hr size="1" noshade="noshade" />
