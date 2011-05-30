{*
$Id: products.tpl,v 1.3 2010/06/08 06:17:40 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $products}

<script type="text/javascript">
//<![CDATA[
if (!isset(products_data))
  var products_data = [];
//]]>
</script>

  {if $active_modules.Customer_Reviews and $config.Customer_Reviews.ajax_rating eq 'Y'}
    {include file="modules/Customer_Reviews/ajax.rating.tpl" _include_once=1}
  {/if}

  {if $active_modules.Feature_Comparison and not $printable and $products_has_fclasses}
    {include file="modules/Feature_Comparison/compare_selected_button.tpl" is_pconf=true pconf_productid=$product.productid}
    <script type="text/javascript" src="{$SkinDir}/modules/Feature_Comparison/products_check.js"></script>
  {/if}

  {if $config.Appearance.products_per_row and ($featured eq "Y" or $config.Appearance.featured_only_multicolumn eq "N")}
    {include file="customer/main/products_t.tpl" current_product=$product is_pconf=true}
  {else}
    {include file="customer/main/products_list.tpl" current_product=$product is_pconf=true}
  {/if}
  
  {if $active_modules.Feature_Comparison and not $printable and $products_has_fclasses}
  {include file="modules/Feature_Comparison/compare_selected_button.tpl" is_pconf=true pconf_productid=$product.productid}
  {/if}

{else}
  {$lng.lbl_pconf_no_products_found}
{/if}

