{*
$Id: quick_search.tpl,v 1.3.2.2 2011/01/20 08:03:45 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_quick_search_results}

<br />

{$lng.txt_N_results_found_for_keywords|substitute:"items":$quick_params.sum:"keyword":$quick_params.query}

<br /><br />

{if $quick_num.products gt 0}
{if $so eq "products"}<span class="quick-search-active-item">{else}<span class="quick-search-item"><a href="quick_search.php?mode=search&amp;so=products">{/if}{$lng.lbl_products} ({$quick_num.products}){if $so ne "products"}</a>{/if}</span>
{/if}
{if $quick_num.users gt 0}
{if $so eq "users"}<span class="quick-search-active-item">{else}<span class="quick-search-item"><a href="quick_search.php?mode=search&amp;so=users">{/if}{$lng.lbl_users} ({$quick_num.users}){if $so ne "users"}</a>{/if}</span>
{/if}
{if $quick_num.orders gt 0}
{if $so eq "orders"}<span class="quick-search-active-item">{else}<span class="quick-search-item"><a href="quick_search.php?mode=search&amp;so=orders">{/if}{$lng.lbl_orders} ({$quick_num.orders}){if $so ne "orders"}</a>{/if}</span>
{/if}

<br /><br /><br />
{strip}
<div align="right" style="width:100%;padding-bottom:4px;">
{if $so eq "orders"}
<a href="orders.php">{$lng.lbl_search_for_orders}</a>
{elseif $so eq "products"}
<a href="search.php">{$lng.lbl_search_for_products}</a>
{elseif $so eq "users"}
<a href="users.php">{$lng.lbl_search_for_users}</a>
{/if}
</div>
{/strip}
{include file="main/navigation.tpl"}

<br />

<table cellpadding="2" cellspacing="1" width="100%" class="quick-search-results">

{if $so eq "orders"}

<tr class="TableHead">
  <td width="5%" nowrap="nowrap">{if $orderby eq "orderid"}{include file="buttons/sort_pointer.tpl" dir=$sort}&nbsp;{/if}<a href="{$qscript|amp}&amp;sort=orderid{if $orderby eq "orderid"}&amp;sort_direction={if $sort eq 1}0{else}1{/if}{/if}">#</a></td>
  <td nowrap="nowrap">{if $orderby eq "status"}{include file="buttons/sort_pointer.tpl" dir=$sort}&nbsp;{/if}<a href="{$qscript|amp}&amp;sort=status{if $orderby eq "status"}&amp;sort_direction={if $sort eq 1}0{else}1{/if}{/if}">{$lng.lbl_status}</a></td>
  <td width="30%" nowrap="nowrap">{if $orderby eq "login"}{include file="buttons/sort_pointer.tpl" dir=$sort}&nbsp;{/if}<a href="{$qscript|amp}&amp;sort=login{if $orderby eq "login"}&amp;sort_direction={if $sort eq 1}0{else}1{/if}{/if}">{$lng.lbl_customer}</a></td>
  <td width="20%" nowrap="nowrap">{if $orderby eq "date"}{include file="buttons/sort_pointer.tpl" dir=$sort}&nbsp;{/if}<a href="{$qscript|amp}&amp;sort=date{if $orderby eq "date"}&amp;sort_direction={if $sort eq 1}0{else}1{/if}{/if}">{$lng.lbl_date}</a></td>
  <td width="20%" align="right" nowrap="nowrap">{if $orderby eq "total"}{include file="buttons/sort_pointer.tpl" dir=$sort}&nbsp;{/if}<a href="{$qscript|amp}&amp;sort=total{if $orderby eq "total"}&amp;sort_direction={if $sort eq 1}0{else}1{/if}{/if}">{$lng.lbl_total}</a></td>
</tr>

{foreach from=$orders item=order}
<tr{cycle values=", class='TableSubHead'"}>
  <td width="5%" nowrap="nowrap"><a href="order.php?orderid={$order.orderid}">#{$order.orderid}</a></td>
  <td nowrap="nowrap"><a href="order.php?orderid={$order.orderid}"><b>{include file="main/order_status.tpl" status=$order.status mode="static"}</b></a></td>
  <td width="30%" nowrap="nowrap"><a href="{$catalogs.admin}/user_modify.php?user={$order.userid}&amp;usertype=C">{$order.login}</a></td>
  <td width="20%" nowrap="nowrap">{$order.date|date_format:$config.Appearance.datetime_format}</td>
  <td width="20%" align="right" nowrap="nowrap"><a href="order.php?orderid={$order.orderid}">{currency value=$order.total}</a></td>
</tr>
{/foreach}

{elseif $so eq "users"}

<tr class="TableHead">
  <td width="5%" nowrap="nowrap">{if $orderby eq "login"}{include file="buttons/sort_pointer.tpl" dir=$sort}&nbsp;{/if}<a href="{$qscript|amp}&amp;sort=login{if $orderby eq "login"}&amp;sort_direction={if $sort eq 1}0{else}1{/if}{/if}">{$lng.lbl_username}</a></td>
  <td nowrap="nowrap">{if $orderby eq "name"}{include file="buttons/sort_pointer.tpl" dir=$sort}&nbsp;{/if}<a href="{$qscript|amp}&amp;sort=name{if $orderby eq "name"}&amp;sort_direction={if $sort eq 1}0{else}1{/if}{/if}">{$lng.lbl_name}</a></td>
  <td width="15%" nowrap="nowrap">{if $orderby eq "usertype"}{include file="buttons/sort_pointer.tpl" dir=$sort}&nbsp;{/if}<a href="{$qscript|amp}&amp;sort=usertype{if $orderby eq "usertype"}&amp;sort_direction={if $sort eq 1}0{else}1{/if}{/if}">{$lng.lbl_usertype}</a></td>
</tr>

{foreach from=$users item=user}
{assign var="_usertype" value=$user.usertype}
<tr{cycle values=", class='TableSubHead'"}>
  <td width="5%" nowrap="nowrap"><a href="{$catalogs.admin}/user_modify.php?user={$user.id}&amp;usertype={$user.usertype}">{$user.login}</a></td>
  <td nowrap="nowrap">{$user.firstname} {$user.lastname}</td>
  <td width="15%" nowrap="nowrap">{$usertypes.$_usertype}</td>
</tr>
{/foreach}

{else}

<tr class="TableHead">
  <td width="5%" nowrap="nowrap">{if $orderby eq "productcode"}{include file="buttons/sort_pointer.tpl" dir=$sort}&nbsp;{/if}<a href="{$qscript|amp}&amp;sort=productcode{if $orderby eq "productcode"}&amp;sort_direction={if $sort eq 1}0{else}1{/if}{/if}">{$lng.lbl_sku}</a></td>
  <td nowrap="nowrap">{if $orderby eq "product"}{include file="buttons/sort_pointer.tpl" dir=$sort}&nbsp;{/if}<a href="{$qscript|amp}&amp;sort=product{if $orderby eq "product"}&amp;sort_direction={if $sort eq 1}0{else}1{/if}{/if}">{$lng.lbl_product}</a></td>
  <td width="5%" nowrap="nowrap">{if $orderby eq "avail"}{include file="buttons/sort_pointer.tpl" dir=$sort}&nbsp;{/if}<a href="{$qscript|amp}&amp;sort=avail{if $orderby eq "avail"}&amp;sort_direction={if $sort eq 1}0{else}1{/if}{/if}">{$lng.lbl_in_stock}</a></td>
  <td width="5%" align="right" nowrap="nowrap">{if $orderby eq "price"}{include file="buttons/sort_pointer.tpl" dir=$sort}&nbsp;{/if}<a href="{$qscript|amp}&amp;sort=price{if $orderby eq "price"}&amp;sort_direction={if $sort eq 1}0{else}1{/if}{/if}">{$lng.lbl_price}</a></td>
</tr>

{foreach from=$products item=product}
<tr{cycle values=", class='TableSubHead'"}>
  <td width="5%" nowrap="nowrap"><a href="product_modify.php?productid={$product.productid}">{$product.productcode}</a></td>
  <td nowrap="nowrap"><a href="product_modify.php?productid={$product.productid}"><b>{$product.product}</b></a></td>
  <td width="5%" align="right" nowrap="nowrap">{$product.avail}</td>
  <td width="5%" align="right" nowrap="nowrap">{currency value=$product.price}</td>
</tr>
{/foreach}

{/if}

</table>

<br />

{include file="main/navigation.tpl"}
