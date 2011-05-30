{*
$Id: bestsellers.tpl,v 1.1.2.2 2010/12/15 09:44:40 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $bestsellers}

  {capture name=bestsellers}

    <ul class="bestsellers-products-item">
      {foreach from=$bestsellers item=bestseller}

        <li>
          {if $config.Bestsellers.bestsellers_thumbnails eq "Y"}
            <a href="product.php?productid={$bestseller.productid}&amp;cat={$cat}&amp;bestseller=Y">{include file="product_thumbnail.tpl" productid=$bestseller.productid product=$bestseller.product tmbn_url=$bestseller.tmbn_url}</a>
            <div class="details">
              <a class="product-title" href="product.php?productid={$bestseller.productid}&amp;cat={$cat}&amp;bestseller=Y">{$bestseller.product|amp}</a><br />
              {$lng.lbl_our_price}: {currency value=$bestseller.taxed_price}
            </div>
            <div class="clearing"></div>
          {else}
            <a class="product-title" href="product.php?productid={$bestseller.productid}&amp;cat={$cat}&amp;bestseller=Y">{$bestseller.product|amp}</a><br />
            {$lng.lbl_our_price}: {currency value=$bestseller.taxed_price}
          {/if}
        </li>

      {/foreach}
    </ul>

  {/capture}
  {include file="customer/dialog.tpl" title=$lng.lbl_bestsellers content=$smarty.capture.bestsellers}

{/if}
