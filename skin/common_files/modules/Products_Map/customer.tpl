{*
$Id: customer.tpl,v 1.4 2010/08/02 11:04:36 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>{$lng.pmap_location}</h1>

<div class="pmap_letters">
<p align="center">
  {foreach from=$pmap.symbols item="display" key="symb"}
    {if $display eq false}
      {assign var='span_class' value='class="pmap_disabled"'}
    {elseif $symb eq $pmap.current and $pmap.products ne ""}
      {assign var='span_class' value='class="pmap_current"'}
    {else}
      {assign var='span_class' value=''}
    {/if}

    {strip}
      {if $span_class ne ""}<span {$span_class}>{else}<a href="{$pmap.navigation}={$symb}" title="Products #{$symb}">{/if}
      {if $symb eq "0-9"}#{else}{$symb}{/if}
      {if $span_class ne ""}</span>{else}</a>{/if}
    {/strip}

  {/foreach}
</p>
</div>

<br clear="left" />

{capture name=dialog}

{if $pmap.products ne false}

  {include file="customer/main/navigation.tpl"}
  {include file="customer/main/products.tpl" products=$pmap.products}
  {include file="customer/main/navigation.tpl"}

{else}

  {$lng.lbl_no_items_available}

{/if}

{/capture}
{include file="customer/dialog.tpl" title=$lng.pmap_location content=$smarty.capture.dialog noborder=true}
