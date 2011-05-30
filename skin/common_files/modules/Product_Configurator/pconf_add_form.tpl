{*
$Id: pconf_add_form.tpl,v 1.1 2010/05/21 08:32:46 joy Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
{if $product.appearance.buy_now_enabled}
<form method="{if $product.appearance.buy_now_cart_enabled}post{else}get{/if}" action="{if $product.appearance.buy_now_cart_enabled}pconf.php{else}product.php{/if}" name="form{$product.productid}">
<input type="hidden" name="mode" value="add" />
<input type="hidden" name="slot" value="{$slot}" />
{if $product.appearance.buy_now_cart_enabled}
<input type="hidden" name="productid" value="{$current_product.productid}" />
{else}
<input type="hidden" name="productid" value="{$product.productid}" />
<input type="hidden" name="pconf" value="{$current_product.productid}" />
<input type="hidden" name="slot" value="{$slot}" />
{/if}
<input type="hidden" name="addproductid" value="{$product.productid}" />

<div class="buy-now">

  {if $slot_data.multiple eq "Y" and not $product.appearance.empty_stock}
    <div class="quantity">
      <span class="quantity-title">{$lng.lbl_quantity}</span>
      <select name="amount">
        {section name=quantity loop=$product.appearance.loop_quantity start=$product.appearance.min_quantity}
        <option value="{%quantity.index%}"{if $product.appearance.default_quantity eq %quantity.index%} selected="selected"{/if}>{%quantity.index%}</option>
        {/section}
      </select>

      {if $product.appearance.min_quantity eq $product.appearance.max_quantity}
        <p>{$lng.txt_add_to_configuration_note|substitute:"items":$product.appearance.min_quantity}</p>
      {/if}

    </div>
  {/if}

  <div class="button-row">
    {include file="customer/buttons/button.tpl" button_title=$lng.lbl_pconf_add_to_configuration type="input"}
  </div>
</div>
</form>

{else}

<div class="button-row">
  {include file="customer/buttons/details.tpl" href="product.php?productid=`$product.productid`&pconf=`$current_product.productid`&slot=`$slot`"}
</div>

{/if}

{if $product.appearance.empty_stock}
<p class="message">
  <strong>{$lng.lbl_note}:</strong> {$lng.lbl_pconf_slot_out_of_stock_note}
</p>
{/if}
