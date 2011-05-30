{*
$Id: product_list.tpl,v 1.3 2010/07/19 12:38:11 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=menu}

  {if $comparison_products ne ''}

    {foreach from=$comparison_products item=v key=k}
      <form action="comparison.php" method="post" name="comparisonlist_{$k}">
        <input type="hidden" name="mode" value="get_products" />

        <p class="fcomp-subtitle">{$v.class}</p>

        <ul>
          {foreach from=$v.products item=p name=fproducts}
            <li>
              <input type="hidden" name="productids[{$p.productid}]" value="Y" />
              <a href="product.php?productid={$p.productid}">{$p.product}</a>
              <a href="comparison_list.php?mode=delete&amp;productid={$p.productid}"><img class="delete-icon" src="{$ImagesDir}/spacer.gif" alt="{$lng.lbl_delete|escape}" /></a>
            </li>
            {if not $smarty.foreach.fproducts.last}
              <li class="fcomp-line"><hr /></li>
            {/if}
          {/foreach}

        </ul>

        <ul class="simple-list simple-list-left fcomp-buttons">
        <li class="item-left">{include file="customer/buttons/button.tpl" button_title=$lng.lbl_compare additional_button_class="menu-button" type="input"}</li>
        <li class="item-left">{include file="customer/buttons/button.tpl" button_title=$lng.lbl_clear_list href="comparison_list.php?mode=delete&amp;fclassid=`$k`" style="link" additional_button_class="fcomp-menu-link"}</li>
        </ul>
      <div class="clearing"></div>
      </form>
    {/foreach}

  {else}

    <p class="center">{$lng.lbl_comparison_list_empty}</p>

  {/if}

{/capture}
{include file="customer/menu_dialog.tpl" title=$lng.lbl_comparison_list content=$smarty.capture.menu additional_class="fcomp-list"}
