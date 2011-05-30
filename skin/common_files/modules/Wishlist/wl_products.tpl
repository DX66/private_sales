{*
$Id: wl_products.tpl,v 1.2.2.3 2010/12/15 11:57:06 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

{if $active_modules.Product_Options}
  <script type="text/javascript" src="{$SkinDir}/modules/Product_Options/edit_product_options.js"></script>
{/if}

{if $script_name eq ""}
  {assign var="script_name" value="cart"}
{/if}

{if $wl_products ne "" or ($active_modules.Gift_Certificates ne "" and $wl_giftcerts ne "")}

  <div class="products cart">

    {if $wl_products ne ""}

      {foreach from=$wl_products item=product}

        <form action="cart.php" method="post" name="update{$product.wishlistid}_form">
          <input type="hidden" name="mode" value="wishlist" />
          <input type="hidden" name="eventid" value="{$eventid|escape}" />
          <input type="hidden" name="wlitem" value="{$product.wishlistid}" />
          <input type="hidden" name="action" value="update_quantity" />

          <table cellspacing="0" class="item width-100" summary="{$lng.lbl_wish_list|escape}">
            <tr>
              <td class="image">
                <a href="product.php?productid={$product.productid}{if $giftregistry}&amp;wishlistid={$product.wishlistid}{/if}">{include file="product_thumbnail.tpl" productid=$product.display_imageid image_x=$product.tmbn_x product=$product.product tmbn_url=$product.pimage_url type=$product.is_pimage}</a>
              </td>
              <td class="details">

                {if $giftregistry and $product.amount_purchased ge $product.amount}
                  <p class="product-details-title">{$lng.lbl_purchased}</p>
                {/if}
                <a href="product.php?productid={$product.productid}{if $giftregistry}&amp;wishlistid={$product.wishlistid}{/if}" class="product-title">{$product.product}</a>

                <div class="descr">{$product.descr}</div>

                {if $product.product_options ne ""}
                  <p class="poptions-title">{$lng.lbl_selected_options}:</p>
                  <div class="poptions-list">
                    {include file="modules/Product_Options/display_options.tpl" options=$product.product_options}
                    {if $giftregistry eq "" and $source ne "giftreg"}
                      {include file="customer/buttons/edit_product_options.tpl" target="wishlist" id="`$product.wishlistid`&amp;eventid=`$eventid`" style="link"}
                    {/if}
                  </div>
                {/if}

                {assign var="price" value=$product.taxed_price}
                {if $active_modules.Product_Configurator and $product.product_type eq "C"}
                  {include file="modules/Product_Configurator/pconf_customer_cart.tpl" main_product=$product products=$product.subproducts}
                {/if}

                {if $active_modules.Subscriptions and $product.sub_plan and $product.product_type ne "C"}

                  {include file="modules/Subscriptions/subscription_priceincart.tpl"}

                {elseif $product.amount_remain gt 0 or $allow_edit}
                  <span class="product-price-text">
                    {currency value=$price} x {if $active_modules.Egoods and $product.distribution}1<input type="hidden"{else}<input type="text" size="3"{/if} name="quantity" id="qty_{$product.wishlistid}" value="{$product.amount}" /> = 
                  </span>
                  <span class="price">
                    {multi x=$price y=$product.amount format="%.2f" assign=unformatted}{currency value=$unformatted}
                  </span>
                  <span class="market-price">
                    {alter_currency value=$unformatted}
                  </span>

                  {if $product.taxes}
                    <div class="taxes">{include file="customer/main/taxed_price.tpl" taxes=$product.taxes is_subtax=true}</div>
                  {/if}
  
                {/if}

                {if $eventid gt 0}
                  <p class="whishlist-purchased-row">
                    {if $product.amount_remain gt 0}
                      {$lng.lbl_giftreg_items_purchased|substitute:"ordered":$product.amount_requested:"bought":$product.amount_purchased:"remain":$product.amount_remain}
                    {else}
                      {$lng.lbl_giftreg_all_items_purchased}
                    {/if}
                  </p>
                {/if}

                {if not ((($wl_products and $product.amount_purchased lt $product.amount and $product.avail gt "0") or $config.General.unlimited_products eq "Y") or $main_mode eq "manager" or $product.product_type eq "C") and $product.amount gt $product.avail}
                  <strong>{$lng.txt_out_of_stock}</strong>
                {/if}

              </td>
            </tr>
            <tr>
              <td class="buttons-row">
                {if not $giftregistry}
                  {include file="customer/buttons/delete_item.tpl" href="cart.php?mode=wldelete&wlitem=`$product.wishlistid`&eventid=`$eventid`" style="link" additional_button_class="simple-delete-button"}
                {/if}

              </td>
              <td class="buttons-row">

                {if $allow_edit eq "Y"}
                  {include file="customer/buttons/update.tpl" type="input" additional_button_class="light-button"}
                  <div class="button-separator"></div>
                {/if}

                {if ((($wl_products and ($product.amount_purchased lt $product.amount or $eventid eq "") and $product.avail gt "0") or $config.General.unlimited_products eq "Y") or $main_mode eq "manager" or $product.product_type eq "C") and ($login or $giftregistry ne "")}

                  {if $giftregistry eq ""}
                    {include file="customer/buttons/add_to_cart.tpl" href="javascript: self.location = 'cart.php?mode=wl2cart&amp;wlitem=`$product.wishlistid`&amp;amount='+$('#qty_`$product.wishlistid`').val()" additional_button_class="light-button"}
                  {else}
                    {include file="customer/buttons/add_to_cart.tpl" href="javascript: self.location = 'cart.php?mode=wl2cart&amp;fwlitem=`$product.wishlistid`&amp;eventid=`$eventid`&amp;amount='+$('#qty_`$product.wishlistid`').val()" additional_button_class="light-button"}
                  {/if}
                {/if}

              </td>
            </tr>

            {if $active_modules.Gift_Registry}
              <tr>
                <td>&nbsp;</td>
                <td>
                  {include file="modules/Gift_Registry/giftreg_wishlist.tpl" wlitem_data=$product form_name="update`$product.wishlistid`_form"}
                </td>
              </tr>
            {/if}

          </table>

        </form>

        <hr />

      {/foreach}

    {/if}

    {if $active_modules.Gift_Certificates}
      {include file="modules/Gift_Certificates/gc_cart.tpl" giftcerts_data=$wl_giftcerts}
    {/if}

  </div>

  {if $giftregistry eq "" and $source ne "giftreg"}

    {include file="customer/buttons/button.tpl" button_title=$lng.lbl_wl_clear href="`$script_name`.php?mode=wlclear"}
    <div class="clearing"></div>

    <form method="post" action="{$script_name}.php" name="sendall_form">
      <input type="hidden" name="mode" value="send_friend" />
      <input type="hidden" name="action" value="entire_list" />

      <table cellspacing="0" class="wishlist-sendlist data-table" summary="{$lng.lbl_send_entire_wishlist|escape}">
        <tr>
          <td class="data-name"><label for="sendall_form-friend_email">{$lng.lbl_send_entire_wishlist}</label>:</td>
          <td><input type="text" id="sendall_form-friend_email" class="input-email input-required" name="friend_email" /></td>
          <td>{include file="customer/buttons/button.tpl" button_title=$lng.lbl_send type="input"}</td>
        </tr>
      </table>
  
    </form>
  {/if}

{else}

  {$lng.lbl_wl_empty}

{/if}
