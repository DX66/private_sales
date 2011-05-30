{*
$Id: product_special_price.tpl,v 1.1.2.1 2010/12/15 09:44:41 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<p class="offers-price">

  {$lng.lbl_sp_special_price}: 
  {if $product.special_price gt 0}
    {currency value=$product.special_price}
  {else}
    {$lng.lbl_sp_special_price_free} ({currency value=$product.special_price})
  {/if}

</p>
