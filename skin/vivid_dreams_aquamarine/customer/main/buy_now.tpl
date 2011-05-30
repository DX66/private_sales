{*
$Id: buy_now.tpl,v 1.4.2.7 2011/04/21 07:19:58 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{*
Note for designers:
You must register any new template variable in
include/templater/plugins/function.include_cache.php file to handle cache properly
Search for the 'vars_used_in_templates' variable in the file.
*}
<div class="buy-now">

<script type="text/javascript">
//<![CDATA[
products_data[{$product.productid}].quantity = {$product.avail|default:0};
products_data[{$product.productid}].min_quantity = {$product.appearance.min_quantity|default:0};
//]]>
</script>

  {assign var="product_key" value="`$product.productid`_`$product.add_date`_`$featured`"}
  {if $product.appearance.buy_now_form_enabled}

    <form name="orderform_{$product_key}" method="{if $product.appearance.buy_now_cart_enabled}post{else}get{/if}" action="{if $product.appearance.buy_now_cart_enabled}cart.php{else}product.php{/if}" onsubmit="javascript: return check_quantity({$product.productid}, '{$featured}'){if $config.General.ajax_add2cart eq 'Y' and $config.General.redirect_to_cart ne 'Y' and $product.appearance.buy_now_cart_enabled} &amp;&amp; !ajax.widgets.add2cart(this){/if};">
      <input type="hidden" name="mode" value="add" />
      <input type="hidden" name="productid" value="{$product.productid}" />
      <input type="hidden" name="cat" value="{$smarty_get_cat|escape:"html"}" />
      <input type="hidden" name="page" value="{$smarty_get_page|escape:"html"}" />
      <input type="hidden" name="is_featured_product" value="{$featured}" />

      {if $active_modules.Special_Offers eq "Y" and $product.use_special_price and $product.special_price eq 0}
        <input type="hidden" name="is_free_product" value="Y" />
      {/if}

  {/if}

  {if ($product.price eq 0 && !$product.appearance.empty_stock) && ($active_modules.Special_Offers ne "Y" || $product.use_special_price eq '')}

    {assign var="button_href" value=$smarty_get_page|escape:"html"}

    {if $is_matrix_view}
      <div class="quantity-empty"></div>
    {/if}

    <form action="product.php" method="get" name="buynowform{$product.productid}">
      <input type="hidden" name="productid" value="{$product.productid}" />
      <input type="hidden" name="cat" value="{$smarty_get_cat|escape:"html"}" />
      <input type="hidden" name="page" value="{$smarty_get_page|escape:"html"}" />
      <input type="hidden" name="is_featured_product" value="{$featured}" />
      {include file="customer/buttons/buy_now.tpl" additional_button_class="main-button add-to-cart-button" type="input" button_href=$button_href}
    </form>

  {else}

    {if $product.appearance.buy_now_cart_enabled}

      {if $product.appearance.force_1_amount}

        {if $is_matrix_view}
          <div class="quantity-empty"></div>
        {/if}
        <input type="hidden" name="amount" value="1" />

      {else}

        <div class="quantity">
          <span class="quantity-title">{$lng.lbl_quantity}</span>

          {if $product.appearance.empty_stock}

            <span class="out-of-stock">{$lng.txt_out_of_stock}</span>

          {else}
            
            {if $product.appearance.quantity_input_box_enabled}

              <input type="text" id="product_avail_{$product.productid}{$featured}" name="amount" maxlength="11" size="6" onchange="javascript: return check_quantity({$product.productid}, '{$featured}');" value="{$product.appearance.min_quantity|default:"1"}"/>

              {if $config.Appearance.show_in_stock eq 'Y'}
              <span class="quantity-text">{$lng.lbl_product_quantity_from_to|substitute:"min":$product.appearance.min_quantity:"max":$product.avail}</span>
              {/if}
 
            {else}

             <select name="amount">
               {section name=quantity loop=$product.appearance.loop_quantity start=$product.appearance.min_quantity}
                 <option value="{%quantity.index%}"{if $smarty_get_quantity eq %quantity.index%} selected="selected"{/if}>{%quantity.index%}</option>
               {/section}
             </select>

            {/if}

          {/if}

        </div>

      {/if}

    {elseif $product.appearance.empty_stock && !$product.variantid}

      <div class="quantity"><strong>{$lng.txt_out_of_stock}</strong></div>

    {elseif $is_matrix_view}

      <div class="quantity-empty"></div>

    {else}

      <br />

    {/if}

    {if $product.appearance.buy_now_buttons_enabled}

      {capture name="price_button"}
        <span class="price">{currency value=$product.taxed_price}</span>
        <span class="market-price">{alter_currency value=$product.taxed_price}</span>
      {/capture}

      {if $product.appearance.has_price}
        {capture name="price"}
          {if $product.appearance.has_market_price and $product.appearance.market_price_discount gt 0}
            <div class="market-price">
              {$lng.lbl_market_price}: <span class="market-price-value">{currency value=$product.list_price}</span>

            </div>
          {/if}

          {if $product.taxes}
            <div class="taxes">
              {include file="customer/main/taxed_price.tpl" taxes=$product.taxes is_subtax=true}
            </div>
          {/if}
        {/capture}
      {/if}

      {if $is_matrix_view}

        <div class="add-to-cart-row">
          <div class="add-to-cart-layer">
            <div class="button-row">
              {include file="customer/buttons/button.tpl" type="input" additional_button_class="cart-button add-to-cart-button" button_title=$smarty.capture.price_button title=$lng.lbl_add_to_cart}
            </div>
          </div>
        </div>
        <div class="prices-block">{$smarty.capture.price}</div>
        {if $product.appearance.dropout_actions}
          <div class="button-row">
          {include file="customer/buttons/add_to_list.tpl" id=$product.productid form_name="orderform_`$product_key`" prefix=$product_key}
          </div>
        {elseif $active_modules.Wishlist and ($config.Wishlist.add2wl_unlogged_user eq "Y" or $login ne "")}
          <div class="button-row">
            {include file="customer/buttons/add_to_wishlist.tpl" href="javascript: submitForm(document.orderform_`$product_key`, 'add2wl'); return false;"}
          </div>
        {/if}

      {else}

        <div class="buttons-row nopad">
          {include file="customer/buttons/button.tpl" type="input" additional_button_class="cart-button add-to-cart-button" button_title=$smarty.capture.price_button title=$lng.lbl_add_to_cart}
          {if $product.appearance.dropout_actions}
            <div class="button-separator"></div>
            {include file="customer/buttons/add_to_list.tpl" id=$product.productid form_name="orderform_`$product_key`" prefix=$product_key}
          {elseif $active_modules.Wishlist and ($config.Wishlist.add2wl_unlogged_user eq "Y" or $login ne "")}
            <div class="button-separator"></div>
            {include file="customer/buttons/add_to_wishlist.tpl" href="javascript: submitForm(document.orderform_`$product_key`, 'add2wl'); return false;"}
          {/if}
        </div>
        <div class="prices-block2">{$smarty.capture.price}</div>
        <div class="clearing"></div>

      {/if}

    {/if}

    {if $product.min_amount gt 1}
      <div class="product-details-title">{$lng.txt_need_min_amount|substitute:"items":$product.min_amount}</div>
    {/if}

  {/if}

  {if $product.appearance.buy_now_form_enabled}
    </form>
  {/if}

</div>
