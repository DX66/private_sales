{*
$Id: pconf_customer_cart.tpl,v 1.1.2.1 2010/12/15 09:44:40 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="pconf-cart-list">

  <strong>{$lng.lbl_pconf_selected_products}:</strong>

  <table cellspacing="0" class="data-table" summary="{$lng.lbl_pconf_selected_products|escape}">

    {assign var="pconf_subtotal" value=0}

    {foreach from=$products item=pcitem name=pconf_products}
      {if $pcitem.hidden eq $main_product.cartid}

        {inc value=$pconf_subtotal inc=$pcitem.display_price assign="pconf_subtotal"}

        <tr{interline name=pconf_products}>
          <td{if $pcitem.product_options eq ''} colspan="2"{/if} class="pconf-cart-subproduct">
            {strip}
            <a href="product.php?productid={$pcitem.productid}" title="{$pcitem.product|escape}
            {if $pcitem.product_options}:
              {include file="modules/Product_Options/display_options.tpl" options=$pcitem.product_options is_plain="Y"}
            {/if}
            " target="productinfo{$pcitem.productid}">{$pcitem.product}</a>
            {/strip}
          </td>
          {if $pcitem.product_options ne ''}
            <td>
              {include file="main/visiblebox_link.tpl" mark="pconf_opt_`$pcitem.cartid`" title=$lng.lbl_product_options}
              <div id="boxpconf_opt_{$pcitem.cartid}" style="display: none;">
                {include file="modules/Product_Options/display_options.tpl" options=$pcitem.product_options}</div>
            </td>
          {/if}
          {if $pcitem.price_modifier}
            <td class="pconf-price-modifier">
              {if $pcitem.price_modifier gt 0}+{else}-{/if} {currency value=$pcitem.price_modifier}
            </td>
          {else}
            <td>&nbsp;</td>
          {/if}
          <td class="pconf-price">{currency value=$pcitem.display_price}{if $pcitem.pcitem_amount gt 1}{multi x=$pcitem.display_price y=$pcitem.pcitem_amount assign=pcitem_subtotal} x {$pcitem.pcitem_amount} = {currency value=$pcitem_subtotal}{/if}
          </td>
        </tr>

      {/if}
    {/foreach}
    
    {if $main_product.display_price neq 0}
      <tr class="pconf-products">
        <td colspan="3" class="pconf-cart-total-name">
          {inc value=$pconf_subtotal inc=$main_product.price assign="pconf_subtotal"}
          <strong>{$lng.lbl_pconf_base_price}</strong>
          {if $main_product.options_surcharge lt 0}
            <span class="pconf-negative-price"> {$lng.lbl_pconf_discounted}</span>
          {/if}
        </td>
        <td class="pconf-{if $main_product.options_surcharge lt 0}negative-{/if}price">{currency value=$main_product.display_price display_sign=$main_product.price_show_sign}</td>
      </tr>
    {/if}
  
  </table>

  <div class="button-row">
    {if $main eq "wishlist" or $main eq "giftreg"}
      {include file="customer/buttons/button.tpl" button_title=$lng.lbl_pconf_reconfigure href="pconf.php?productid=`$main_product.productid`&amp;mode=reconfigure&amp;wlitem=`$main_product.wishlistid`" additional_button_class="light-button"}
    {else}
      {include file="customer/buttons/button.tpl" button_title=$lng.lbl_pconf_reconfigure href="pconf.php?productid=`$main_product.productid`&amp;mode=reconfigure&amp;itemid=`$main_product.cartid`" additional_button_class="light-button"}
    {/if}
  </div>

</div>
