{*
$Id: cart_free.tpl,v 1.1 2010/05/21 08:32:48 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $product.free_amount gt 0 and $product.subtotal eq 0}
  <span class="offers-free-note">{$lng.lbl_sp_cart_free_item}</span>
{/if}
{if $config.Shipping.enable_shipping eq "Y" and $product.free_shipping_used ne ""}
  {if $product.free_shipping_ids}
    {assign var="free_shippings" value=""}
    {assign var="is_first" value=true}
    {foreach from=$shipping item=delivery_method}
      {if $product.free_shipping_ids[$delivery_method.shippingid]}
        {if $is_first}
          {assign var="is_first" value=false}
        {else}
          {assign var="free_shippings" value=$free_shippings|cat:",&nbsp;"}
        {/if}
        {assign var="free_shippings" value=$free_shippings|cat:$delivery_method.shipping}
      {/if}
    {/foreach}
    {if $free_shippings ne ""}
    <span class="offers-free-shipping-note">{$lng.lbl_sp_cart_free_shipping_item}</span>
    <span class="small-note">({$free_shippings})</span>
    {/if}
  {else}
    <span class="offers-free-shipping-note">{$lng.lbl_sp_cart_free_shipping_item}</span>
  {/if}
{/if}
