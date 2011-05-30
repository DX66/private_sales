{*
$Id: wishlists.tpl,v 1.1.2.1 2011/01/20 08:03:46 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_search_wishlists}

{$lng.txt_admin_wishlists}

<br /><br />

{if $wishlists eq ''}

{capture name=dialog}
<form name="searchform" action="wishlists.php" method="post">
<input type="hidden" name="mode" value="search" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
  <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_customer}:</td>
  <td width="10" height="10">&nbsp;</td>
  <td height="10" width="100%"><input type="text" name="search_data[login]" value="{$search_data.login|escape}" /></td>
</tr>
<tr>
  <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_sku}:</td>
  <td width="10" height="10">&nbsp;</td>
  <td height="10" width="100%"><input type="text" name="search_data[sku]" value="{$search_data.sku|escape}" /></td>
</tr>
<tr>
  <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_productid}:</td>
  <td width="10" height="10">&nbsp;</td>
  <td height="10" width="100%"><input type="text" name="search_data[productid]" value="{$search_data.productid|escape}" /></td>
</tr>
<tr>
  <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_product}:</td>
  <td width="10" height="10">&nbsp;</td>
  <td height="10" width="100%"><input type="text" name="search_data[product]" value="{$search_data.product|escape}" size="40" /></td>
</tr>

<tr>
  <td colspan="3">&nbsp;</td>
</tr>
<tr>
  <td colspan="2">&nbsp;</td>
  <td><input type="submit" value="{$lng.lbl_search|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_search_wishlists content=$smarty.capture.dialog extra='width="100%"'}

<br />

{/if}

{if $mode eq "search"}
{if $total_items gte 1}
{$lng.txt_N_results_found|substitute:"items":$total_items}<br />
{$lng.txt_displaying_X_Y_results|substitute:"first_item":$first_item:"last_item":$last_item}
{else}
{$lng.txt_N_results_found|substitute:"items":0}
{/if}
{/if}

{if $mode eq "search" and $wishlists ne ""}

<br /><br />

{capture name=dialog}

<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_search_again href="wishlists.php"}</div>

<br />

{if $total_pages gte 2}
{assign var="navpage" value=$navigation_page}
{/if}

<table cellpadding="0" cellspacing="0" width="100%">
<tr><td>

{include file="main/navigation.tpl"}

<table cellpadding="3" cellspacing="1" width="100%">
<tr class="TableHead">
  <td>{$lng.lbl_customer}</td>
  <td>{$lng.lbl_items}</td>
</tr>
{foreach from=$wishlists item=v}
<tr{cycle name=c1 values=', class="TableSubHead"'}>
  <td>{if $is_admin_user}<a href="{$catalogs.admin}/user_modify.php?user={$v.userid}&amp;usertype=C">{$v.firstname}&nbsp;{$v.lastname}&nbsp;({$v.login})</a>{else}{$v.firstname}&nbsp;{$v.lastname}&nbsp;({$v.login}){/if}</td>
  <td>
    <a href="wishlists.php?mode=wishlist&amp;customer={$v.userid}">{$lng.lbl_n_items_in_wishlist|substitute:"items":$v.pcounts[0]}</a>
    {if $active_modules.Gift_Registry and $v.is_events}
      {include file="modules/Gift_Registry/wishlists_pcounts.tpl" pcounts=$v.pcounts}
    {/if}
  </td>
</tr>
{/foreach}
</table>

<br />

{include file="main/navigation.tpl"}

</td></tr>

</table>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_search_results content=$smarty.capture.dialog extra='width="100%"'}

{/if}
