{*
$Id: products.tpl,v 1.4 2010/07/14 05:35:30 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $products}
  {if $config.General.ajax_add2cart eq 'Y' and $config.General.redirect_to_cart ne 'Y'}
    {include file="customer/ajax.add2cart.tpl" _include_once=1}
  {/if}

  {if $active_modules.Customer_Reviews and $config.Customer_Reviews.ajax_rating eq 'Y'}
    {include file="modules/Customer_Reviews/ajax.rating.tpl" _include_once=1}
  {/if}

  {if $active_modules.Feature_Comparison and not $printable and $products_has_fclasses}
    {include file="modules/Feature_Comparison/compare_selected_button.tpl"}
  {/if}

  {if $config.Appearance.products_per_row gt 1 and ($featured eq "Y" or $config.Appearance.featured_only_multicolumn eq "N")}

    {include file="customer/main/products_t.tpl"}

  {else}

    {include file="customer/main/products_list.tpl"}

  {/if}

  {if $active_modules.Feature_Comparison and not $printable and $products_has_fclasses}
    {include file="modules/Feature_Comparison/compare_selected_button.tpl"}
  {/if}

{/if}
