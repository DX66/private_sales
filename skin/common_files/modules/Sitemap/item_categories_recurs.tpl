{*
$Id: item_categories_recurs.tpl,v 1.4.2.1 2010/11/15 07:00:42 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<li><a href="{$item.url}" title="{$item.name}">{$item.name}</a>
  {if $item.products ne false}
    <ul class="sitemap_products">
      {foreach from=$item.products item="product" key="product_num"}
        <li><a href="{$product.url}" title="{$product.name|escape}">{$product.name|amp}</a></li>
      {/foreach}
    </ul>
  {/if}
  {if $item.subs ne false}
    <ul class="sitemap_categories_sub">
      {foreach from=$item.subs item="sub" key="sub_num"}
        {include file="modules/Sitemap/item_categories_recurs.tpl" item=$sub}
      {/foreach}
    </ul>
  {/if}
</li>
