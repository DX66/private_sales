{*
$Id: product_buttons.tpl,v 1.1 2010/05/21 08:32:21 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $product.fclassid gt 0 and ($is_comparison_list eq 'Y' or ($product.other_products or $product.is_product_popup eq 'Y'))}

  <div class="fcomp-product-box">

    {if $is_comparison_list eq 'Y' and $product.appearance.dropout_actions eq ""}
      <div class="buttons-row">
        {include file="modules/Feature_Comparison/add_comparison_list.tpl" productid=$product.productid additional_button_class="light-button"}
      </div>
    {/if}

    {if $product.other_products or $product.is_product_popup eq 'Y'}
      <div class="fcomp-compare-with-title">{$lng.lbl_fcomp_compare_product_with}</div>
        <form action="comparison.php" method="post" name="compareform">
          <input type="hidden" name="mode" value="get_products" />
          <input type="hidden" name="productids[{$product.productid}]" value="Y" />
      <div class="fcomp-select-box">

          {include file="modules/Feature_Comparison/product_selector.tpl" products=$product.other_products is_product_popup=$product.is_product_popup fclassid=$product.fclassid no_ids=$product.productid}
          {include file="customer/buttons/button.tpl" button_title=$lng.lbl_product_comparison type="input" style="image"}
      </div>

        </form>
    {/if}

  </div>

{/if}
