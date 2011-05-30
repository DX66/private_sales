{*
$Id: comparison_classes_list.tpl,v 1.1 2010/05/21 08:32:21 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=dialog}
  <div class="inline-message">
    <img src="{$ImagesDir}/spacer.gif" alt="" class="icon-w" />
    {$lng.txt_fc_differ_classes}
  </div>

  {foreach from=$classes item=c key=fclassid}
    <p class="fcomp-class-title">{$c.class}</p>

    <form action="comparison.php" method="post" name="classform_{$fclassid}">
      <input type="hidden" name="mode" value="get_products" />

      <ul>
        {foreach from=$c.products item=v}
          <li>
            <input type="hidden" name="productids[{$v.productid}]" value="Y" />
            <a href="product.php?productid={$v.productid}">{$v.product}</a>
          </li>
        {/foreach}
      </ul>

      <div class="right-box">
        {include file="customer/buttons/button.tpl" button_title=$lng.lbl_compare type="input"}
      </div>

    </form>

  {/foreach}

{/capture}
{include file="customer/dialog.tpl" title=$lng.lbl_product_feature_classes content=$smarty.capture.dialog additional_class="fcomp-classes-list"}
