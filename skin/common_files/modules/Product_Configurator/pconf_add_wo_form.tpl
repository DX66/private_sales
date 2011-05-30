{*
$Id: pconf_add_wo_form.tpl,v 1.1 2010/05/21 08:32:46 joy Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
{strip}
{if $config.General.unlimited_products eq "Y" or ($product.avail gt 0 and $product.avail ge $product.min_amount) or ($product.variantid and $product.avail gt 0)}
{if ($product.is_product_options eq 'Y' and $config.Product_Options.buynow_with_options_enabled eq 'Y') or ($product.price eq 0) or ($product.min_amount gt $product.avail and $product.is_variants eq "Y") or ($product.product_type eq 'C')}

  {if $show eq 'popup'}
    {assign var="href" value="javascript: window.opener.location = 'product.php?productid=`$product.productid`&amp;pconf=`$pconf_productid`&amp;slot=`$pconf_slot`';"}
  {else}
    {assign var="href" value="product.php?productid=`$product.productid`&amp;pconf=`$pconf_productid`&amp;slot=`$pconf_slot`"}
  {/if}

{else}

  {assign var="href" value="pconf.php?mode=add&amp;addproductid=`$product.productid`&amp;productid=`$pconf_productid`&amp;slot=`$pconf_slot`"}

{/if}

{include file="customer/buttons/button.tpl" button_title=$lng.lbl_pconf_add_to_configuration href=$href}

{else}
<b>{$lng.txt_out_of_stock}</b>
{/if}
{/strip}
