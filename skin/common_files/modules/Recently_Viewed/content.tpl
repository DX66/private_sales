{*
$Id: content.tpl,v 1.4.2.1 2010/10/22 07:52:53 aim Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
{if $rviewed_products}
  {capture name=menu}
    {section name=i loop=$rviewed_products}
      {assign var="url" value="product.php?productid=`$rviewed_products[i].productid`"}
      <div class="item">
        <div class="image">
          <a href="{$url}">{include file="product_thumbnail.tpl" productid=$rviewed_products[i].productid image_x=65 product=$rviewed_products[i].product tmbn_url=$rviewed_products[i].tmbn_url}</a>
        </div>
        <a href="{$url}" class="product-title">{$rviewed_products[i].product|amp}</a>
        {if not $smarty.section.i.last}
          <img src="{$ImagesDir}/spacer.gif" class="separator" alt="" />
        {/if}
      </div>
    {/section}
  {/capture}
  {include file="customer/menu_dialog.tpl" title=$lng.rviewed_section content=$smarty.capture.menu additional_class="menu-rviewed-section"}
{/if}
