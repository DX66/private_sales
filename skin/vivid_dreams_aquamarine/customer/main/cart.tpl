{*
$Id: cart.tpl,v 1.2.2.4 2010/12/15 11:57:06 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>{$lng.lbl_your_shopping_cart}</h1>

{if $cart ne "" and $active_modules.Gift_Certificates}
  <p class="text-block">{$lng.txt_cart_note}</p>
{/if}

{if $cart ne "" and $active_modules.Product_Options}
<script type="text/javascript" src="{$SkinDir}/modules/Product_Options/func.js"></script>
{/if}

<script type="text/javascript">
//<![CDATA[
var txt_are_you_sure = '{$lng.txt_are_you_sure|wm_remove|escape:"javascript"}';
//]]>
</script>

{capture name=dialog}

  {if $products ne ""}

    <br />

    <script type="text/javascript" src="{$SkinDir}/js/cart.js"></script>

    <div class="products cart">

      <form action="cart.php" method="post" name="cartform">

        <input type="hidden" name="action" value="update" />

        {foreach from=$products item=product name=prodcart}
          {if $product.hidden eq ""}

            <table cellspacing="0" class="width-100 item{if $smarty.foreach.prodcart.last} last{/if}" summary="{$product.product|escape}">

              <tr>
                <td class="image">
                  <a href="product.php?productid={$product.productid}">{include file="product_thumbnail.tpl" productid=$product.display_imageid product=$product.product tmbn_url=$product.pimage_url type=$product.is_pimage image_x=$product.tmbn_x}</a>
                  {if $active_modules.Special_Offers ne "" and $product.have_offers}
                    {include file="modules/Special_Offers/customer/product_offer_thumb.tpl"}
                  {/if}

                </td>
                <td class="details">
                  <a href="product.php?productid={$product.productid}" class="product-title">{$product.product|amp}</a>
                  <div class="descr">{$product.descr}</div>

                  {if $product.product_options ne ""}
                    <p class="poptions-title">{$lng.lbl_selected_options}:</p>
                    <div class="poptions-list">
                      {include file="modules/Product_Options/display_options.tpl" options=$product.product_options}
                      {include file="customer/buttons/edit_product_options.tpl" id=$product.cartid}
                    </div>
                  {/if}

                  {assign var="price" value=$product.display_price}
                  {if $active_modules.Product_Configurator and $product.product_type eq "C"}
                    {include file="modules/Product_Configurator/pconf_customer_cart.tpl" main_product=$product}
                    {assign var="price" value=$product.pconf_display_price}
                  {/if}

                  {if $active_modules.Subscriptions and $product.sub_plan and $product.product_type ne "C"}

                    {include file="modules/Subscriptions/subscription_priceincart.tpl"}

                  {else}

                    {if $active_modules.Special_Offers}
                      {include file="modules/Special_Offers/customer/cart_price_special.tpl"}
                    {/if}

                    <span class="product-price-text">
                      {currency value=$price} x {if $active_modules.Egoods and $product.distribution}1<input type="hidden"{else}<input type="text" size="3"{/if} name="productindexes[{$product.cartid}]" value="{$product.amount}" /> = </span>
                    <span class="price">
                      {multi x=$price y=$product.amount assign=unformatted}{currency value=$unformatted}
                    </span>
                    <span class="market-price">
                      {alter_currency value=$unformatted}
                    </span>

                    {if $config.Taxes.display_taxed_order_totals eq "Y" and $product.taxes}
                      <div class="taxes">
                        {include file="customer/main/taxed_price.tpl" taxes=$product.taxes is_subtax=true}
                      </div>
                    {/if}

                    {if $active_modules.Gift_Registry}
                      {include file="modules/Gift_Registry/product_event_cart.tpl"}
                    {/if}

                    {if $active_modules.Special_Offers}
                      {include file="modules/Special_Offers/customer/cart_free.tpl"}
                    {/if}
                  {/if}

                  {if $gcheckout_display_product_note and $product.valid_for_gcheckout eq 'N'}
                    <p>{$lng.lbl_gcheckout_product_disabled}</p>
                  {/if}
                </td>
              </tr>

              <tr>
                <td class="buttons-row">
                  {include file="customer/buttons/delete_item.tpl" href="cart.php?mode=delete&amp;productindex=`$product.cartid`" style="link" additional_button_class="simple-delete-button"}
                </td>
                <td class="buttons-row">
                  {include file="customer/buttons/button.tpl" button_title=$lng.lbl_update_item href="javascript: return updateCartItem(`$product.cartid`);" additional_button_class="light-button"}

                </td>
              </tr>
            </table>

          {/if}
        {/foreach}
        
        {if $active_modules.Special_Offers}
          {include file="modules/Special_Offers/customer/free_offers.tpl"}
        {/if}

        {if $active_modules.Gift_Certificates}
          {include file="modules/Gift_Certificates/gc_cart.tpl" giftcerts_data=$cart.giftcerts}
        {/if}
        
        {if $main eq "checkout"}
          {include file="modules/`$checkout_module`/cart_subtotal.tpl"}
        {/if}
        
        {include file="modules/Gift_Registry/gift_wrapping_cart.tpl"}

        {include file="customer/main/shipping_estimator.tpl"}

        <div class="buttons">

          <div class="left-buttons-row buttons-row">
            {include file="customer/buttons/button.tpl" type="input" button_title=$lng.lbl_update_cart}
            <div class="button-separator"></div>
            {include file="customer/buttons/button.tpl" style="link" additional_button_class="simple-delete-button" button_title=$lng.lbl_clear_cart href="javascript: if (confirm(txt_are_you_sure)) self.location='cart.php?mode=clear_cart'; return false;"}
          </div>

          <div class="right-buttons-row buttons-row">

            {if not $std_checkout_disabled}
            <div class="checkout-button">
              {include file="customer/buttons/button.tpl" button_title=$lng.lbl_checkout  href="cart.php?mode=checkout" additional_button_class="main-button"}
            </div>
            {/if}

            {if $active_modules.Special_Offers}
            <div class="button-separator"></div>
            {include file="modules/Special_Offers/customer/cart_checkout_buttons.tpl" style="link"}
          {/if}

          </div>

          <div class="clearing"></div>
        </div>

      </form>

      {if $paypal_express_active}
        {include file="payments/ps_paypal_pro_express_checkout.tpl" paypal_express_link="button"}
      {/if}

      {if $gcheckout_enabled}
        <div class="right-box">
          {include file="modules/Google_Checkout/gcheckout_button.tpl"}
        </div>
      {/if}

      {if $amazon_enabled}
        <div class="right-box">
          {include file="modules/Amazon_Checkout/checkout_btn.tpl"}
        </div>
      {/if}

    </div>

  {else}

    {$lng.txt_your_shopping_cart_is_empty}

  {/if}

{/capture}
{include file="customer/dialog.tpl" title=$lng.lbl_items_in_cart content=$smarty.capture.dialog noborder=true}

{if $active_modules.Special_Offers and $cart ne ""}
  {include file="modules/Special_Offers/customer/cart_offers.tpl"}
  {include file="modules/Special_Offers/customer/promo_offers.tpl"}
{/if}

{if $cart.coupon_discount eq 0 and $products and $active_modules.Discount_Coupons}
  {include file="modules/Discount_Coupons/add_coupon.tpl}
{/if}
