{*
$Id: product_offers_short_list.tpl,v 1.3.2.4 2010/12/15 09:44:41 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $product_offers}

  {capture name=dialog}

  <table cellpadding="0" cellspacing="0" border="0" summary="{$lng.lbl_special_offers|wm_remove|escape}">

  {foreach name=offers from=$product_offers item=offer key=offerid}

  <tr>
    <td colspan="3">  
    {if $offer.promo_items_amount}
      {if $offer.html_items_amount}
        {$offer.promo_items_amount|amp}
      {else}
        <tt>{$offer.promo_items_amount|escape}</tt>
      {/if}
    {else}
      {$lng.lbl_sp_product_generic}
    {/if}
    </td>
  </tr>

  <tr>
    <td colspan="3"><img src="{$ImagesDir}/spacer.gif" width="1" height="10" alt="" /></td>
  </tr>

  {foreach name=prod_sets from=$offer.product_sets item=prod_set key=setid}
  <tr>

    <td>
    <table cellpadding="2" cellspacing="2" width="100%" border="0">
    <tr>
      <td><b>{$lng.lbl_sp_this_item}: </b>{$product.producttitle|amp} <font class="small-note">[{$prod_set.curr_item_amount} {$lng.lbl_sp_items}]</font></td>
    </tr>
    {foreach name=prod_items from=$prod_set.products item=prod_item}
    {if $prod_item.productid ne $product.productid}
    <tr>
      <td><a href="product.php?productid={$prod_item.productid}">{$prod_item.producttitle|amp}</a> <font class="small-note">[{$prod_item.amount} {$lng.lbl_sp_items}]</font></td>
    </tr>
    {/if}
    {/foreach}
    </table>
    </td>

    <td width="20" align="center"><img src="{$ImagesDir}/spacer_grey.gif" width="1" height="60" alt="" /></td>

    <td>
    <table cellpadding="0" cellspacing="0" border="0" class="product-properties">
    <tr>
      <td class="property-name product-price" nowrap="nowrap">{$lng.lbl_sp_price_for_all}:</td>
      <td class="property-value product-price product-price-value" nowrap="nowrap">{currency value=$prod_set.subtotal}</td>
    </tr>
    <tr>
      <td colspan="2"><img src="{$ImagesDir}/spacer.gif" width="1" height="1" alt="" /></td>
    </tr>
    <tr>
      <td colspan="2" nowrap="nowrap">
        <form name="common_prod_set_{$offerid}_{$setid}" action="cart.php" method="post" onsubmit="javascript: sp_get_prod_options('{$offerid}', '{$setid}');">
        <input type="hidden" name="mode" value="add" />
        <input type="hidden" name="productid" value="{$product.productid}" />
        <input type="hidden" name="amount" value="{$prod_set.curr_item_amount}" />
        {foreach name=prod_items from=$prod_set.products item=prod_item}
        {if $prod_item.productid ne $product.productid}
        <input type="hidden" name="prod_amounts[{$prod_item.productid}]" value="{$prod_item.amount}" />
        {/if}
        {/foreach}
        {include file="customer/buttons/button.tpl" button_title=$lng.lbl_sp_add_all_to_cart type="input" additional_button_class="main-button"}
        </form>
      </td>
    </tr>
    </table>
    </td>

  </tr>

  {if not $smarty.foreach.prod_sets.last}
  <tr>
    <td colspan="3"><img src="{$ImagesDir}/spacer.gif" width="1" height="20" alt="" /></td>
  </tr>
  {/if}

  {/foreach}

  {if not $smarty.foreach.offers.last}
  <tr>
    <td colspan="3"><img src="{$ImagesDir}/spacer.gif" width="1" height="40" alt="" /></td>
  </tr>
  {/if}

  {/foreach}

  </table>

  {/capture}
  {if $nodialog}
    {$smarty.capture.dialog}
  {else}
    {include file="customer/dialog.tpl" title=$lng.lbl_special_offers|escape content=$smarty.capture.dialog}
  {/if}

<script type="text/javascript">
//<![CDATA[
function sp_get_prod_options(offer_id, set_id) {ldelim}

  for (var i = 0; i < document.orderform.elements.length; i++)
    if (document.orderform.elements[i].name.search(/^product_options/) !== -1) {ldelim}
      var new_element = document.createElement('input');
      new_element.setAttribute('type', 'hidden');
      new_element.setAttribute('name', document.orderform.elements[i].name);
      new_element.setAttribute('value', document.orderform.elements[i].value);
      document.forms['common_prod_set_' + offer_id + '_' + set_id].appendChild(new_element);
    {rdelim}

{rdelim}
//]]>
</script>
{/if}
