{*
$Id: cart_details.tpl,v 1.2.2.2 2010/12/15 09:44:39 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{assign var="colspan" value=4}

<table cellspacing="1" class="cart-content width-100" summary="{$lng.lbl_products|escape}">

  <tr class="head-row">
    <th class="cart-column-product">{$lng.lbl_product}</th>
    {if $cart.display_cart_products_tax_rates eq "Y"}
      <th class="cart-column-tax">
        {if $cart.product_tax_name ne ""}
          {$cart.product_tax_name}
        {else}
          {$lng.lbl_tax}
        {/if}
      </th>
      {inc value=$colspan assign="colspan"}
    {/if}
    <th class="cart-column-price">{$lng.lbl_price}</th>
    <th>{$lng.lbl_quantity}</th>

    {if $cart.discount gt 0}
      <th class="cart-column-price">{$lng.lbl_discount}</th>
      {inc value=$colspan assign="colspan"}
    {/if}

    {if $active_modules.Discount_Coupons ne "" and $cart.coupon}
      <th class="class="cart-column-price"">{$lng.lbl_discount_coupon}</th>
      {inc value=$colspan assign="colspan"}
    {/if}
    <th class="cart-column-total">{$lng.lbl_subtotal}</th>
  </tr>

  {assign var="products" value=$cart.products}
  {assign var="summary_price" value=0}
  {assign var="summary_discount" value=0}

  {if $active_modules.Discount_Coupons ne ""}
    {assign var="summary_coupon_discount" value=0}
  {/if}

  {assign var="summary_subtotal" value=0}

  {foreach from=$products item=product name=products}

    {if $product.deleted eq "" and $product.hidden eq ""}

      {assign var="have_products" value="Y"}
      {cart_summary price=$product.display_price amount=$product.amount discount=$product.discount coupon_discount=$product.coupon_discount subtotal=$product.display_subtotal}

      <tr{interline index=$smarty.foreach.products.index total=$list_length}>
        <td>

          {capture name=link_title}
            {$product.product|amp}
            {if $product.product_options}:
              {include file="modules/Product_Options/display_options.tpl" options=$product.product_options is_plain='Y'}
            {/if}
          {/capture}

          <a href="product.php?productid={$product.productid}" title="{$smarty.capture.link_title|escape}">

            {if $product.productcode}
              {$product.productcode}
            {else}
              #{$product.productid}
            {/if}
            . {$product.product|truncate:"30":"...":true}
          </a>
          {if $active_modules.Product_Configurator and $product.product_type eq "C"}
            {include file="modules/Product_Configurator/pconf_customer_checkout.tpl" main_product=$product}
          {/if}
        </td>

        {if $cart.display_cart_products_tax_rates eq "Y"}
          <td class="cart-column-tax">

            {foreach from=$product.taxes key=tax_name item=tax}
              {if $tax.tax_value gt 0}
                <div>
                  {if $cart.product_tax_name eq ""}
                    <span style="white-space: nowrap;">{$tax.tax_display_name}</span>
                  {/if}
                  {if $tax.rate_type eq "%"}
                    {$tax.rate_value}%{else}{currency value=$tax.rate_value}
                  {/if}
                {/if}
              </div>
            {/foreach}

          </td>
        {/if}

        <td class="cart-column-price">
          {if $active_modules.Product_Configurator and $product.product_type eq "C"}
          {currency value=$product.pconf_display_price}
          {else}
          {currency value=$product.display_price}
          {/if}
        </td>
        <td>
          {if $config.Appearance.allow_update_quantity_in_cart eq "N" or ($active_modules.Egoods and $product.distribution) or ($active_modules.Subscriptions and $product.sub_plan) or $link_qty eq "Y"}
            {$product.amount}
          {else}
            <input type="text" size="5" name="productindexes[{$product.cartid}]" value="{$product.amount}" class="cart-quantity" />
          {/if}
        </td>

        {if $cart.discount gt 0}
          <td class="cart-column-price">{currency value=$product.discount}</td>
        {/if}

        {if $active_modules.Discount_Coupons and $cart.coupon}
          <td class="cart-column-price">{currency value=$product.coupon_discount}</td>
        {/if}

        <td class="cart-column-total">{currency value=$product.display_subtotal}</td>
      </tr>

    {/if}
  {/foreach}

  {if $cart.products and $have_products eq "Y"}

    <tr class="head-row">

      <td class="summary-cell">{$lng.lbl_summary}:</td>

      {if $cart.display_cart_products_tax_rates eq "Y"}
        <td>&nbsp;</td>
      {/if}

      <td class="cart-column-price">&nbsp;</td>
      <td class="cart-column-price">&nbsp;</td>

      {if $cart.discount gt 0}
        <td class="cart-column-price">{currency value=$summary_discount}</td>
      {/if}

      {if $active_modules.Discount_Coupons ne "" and $cart.coupon}
        <td class="cart-column-price">{currency value=$summary_coupon_discount}</td>
      {/if}

      <td class="cart-column-total"><strong>{currency value=$summary_subtotal}</strong></td>
    </tr>

  {/if}

  {if $active_modules.Gift_Certificates and $cart.giftcerts}
    {include file="modules/Gift_Certificates/gc_cart_details.tpl"}
  {/if}

</table>

{if $cart.products and $have_products eq "Y" and $config.Taxes.display_taxed_order_totals eq "Y"}

  <div class="text-pre-block">
    <strong>{$lng.txt_notes}:</strong><br />
    {$lng.txt_cart_details_notes}

    {if $cart.taxes and $config.Taxes.display_taxed_order_totals eq "Y" and ($cart.discount gt 0 or ($active_modules.Discount_Coupons ne "" and $cart.coupon))}
      <p>{$lng.txt_cart_details_discount_note}</p>
    {/if}

  </div>

{/if}
