{*
$Id: compare_selected_button.tpl,v 1.1 2010/05/21 08:32:21 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $products and $products_has_fclasses}

  {counter name="fc_count" start=0 print=false}
  {foreach from=$products item=v}
    {if $v.fclassid gt 0}
      {counter assign="fc_count" name="fc_count" print=false}
    {/if}
  {/foreach}

  {if $fc_count gt 0}

    {if $active_modules.Product_Configurator and $is_pconf}
    {assign var="pconf_query_str" value="?pconf_productid=`$pconf_productid`&amp;pconf_slot=`$slot`"}
    {/if}
    <div class="fcomp-compare-buttons">
      <div class="buttons-row buttons-auto-separator">
        {include file="customer/buttons/button.tpl" button_title=$lng.lbl_compare_selected href="javascript: fe_create_form(this, 'comparison.php`$pconf_query_str`', 'get_products');"}
        {if $is_comparison_list eq 'Y'}
          {include file="modules/Feature_Comparison/add_comparison_list.tpl" href="javascript: fe_create_form(this, 'comparison_list.php', 'add');"}
        {/if}
      </div>
    </div>

  {/if}

{/if}
