{*
$Id: ga_commerce_form.tpl,v 1.1 2010/05/21 08:32:23 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{*$Id: ga_commerce_form.tpl,v 1.1 2010/05/21 08:32:23 joy Exp $*}
{include file="modules/Google_Analytics/ga_code.tpl" ga_init="Y"}
<script type="text/javascript">
if (pageTracker && pageTracker._addTrans && pageTracker._trackTrans) {ldelim}
{foreach from=$orders item="order"}
pageTracker._addTrans(
"{$order.order.orderid}", // order ID - required
"{$partner|default:'Main stock'}", // affiliation or store name
"{$order.order.total}", // total - required
"{if $order.order.tax gt 0}{$order.order.tax}{/if}", // tax
"{if $order.order.shipping_cost gt 0}{$order.order.shipping_cost}{/if}", // shipping
"{$order.order.b_city|wm_remove|escape:javascript}", // city
"{$order.order.b_state|wm_remove|escape:javascript}", // state or province
"{$order.order.b_countryname|wm_remove|escape:javascript}" // country
);
{foreach from=$order.products item="product"}
pageTracker._addItem(
"{$order.order.orderid}", // order ID - required
"{$product.productcode|wm_remove|escape:javascript}", // SKU/code
"{$product.product|wm_remove|escape:javascript}{if $active_modules.Product_Options ne "" and $product.product_options_txt} ({$product.product_options_txt|replace:"\n":", "|wm_remove|escape:javascript}){/if}", // product name
"{$product.category|default:'Unknown category'}", // category or variation
"{$product.price}", // unit price - required
"{$product.amount}" // quantity - required
);
{/foreach}
{/foreach}
pageTracker._trackTrans();
{rdelim}
</script>
