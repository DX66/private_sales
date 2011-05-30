{*
$Id: buy_now.tpl,v 1.1 2010/05/21 08:32:21 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{strip}
{if $config.General.unlimited_products eq "Y" or ($product.avail gt 0 and $product.avail ge $product.min_amount) or ($product.variantid and $product.avail gt 0)}
{if ($product.is_product_options eq 'Y' and $config.Product_Options.buynow_with_options_enabled eq 'Y') or ($product.price eq 0) or ($product.min_amount gt $product.avail and $product.is_variants eq "Y") or ($product.product_type eq 'C')}

  {if $show eq 'popup'}
    {include file="modules/Feature_Comparison/f_buy_now.tpl" href="javascript:window.opener.location='product.php?productid=`$product.productid`';" js_link="Y"}
  {else}
    {include file="modules/Feature_Comparison/f_buy_now.tpl" href="product.php?productid=`$product.productid`" js_link="N"}
  {/if}

{else}

  {if $show eq 'popup'}
    {include file="modules/Feature_Comparison/f_buy_now.tpl" href="javascript:add2cart(`$product.productid`);" js_link="Y" fake_image="Y"}
  {else}
    {include file="modules/Feature_Comparison/f_buy_now.tpl" href="cart.php?mode=add&amp;productid=`$product.productid`&amp;amount=1&amp;redirect_to_cart=Y" js_link="N"}
  {/if}

{/if}
{else}
<b>{$lng.txt_out_of_stock}</b>
{/if}
{/strip}
