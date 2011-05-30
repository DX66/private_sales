{*
$Id: subcategories_t.tpl,v 1.2.2.1 2010/11/16 12:04:31 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{foreach from=$categories item=subcategory}

  <span class="subcategories" style="min-width: {$subcat_div_width}px; width: {$subcat_div_width}px; min-height: {$subcat_div_height}px;">
    {if $subcategory.is_icon}
      <a href="home.php?cat={$subcategory.categoryid}"><img src="{get_category_image_url category=$subcategory}" alt="{$subcategory.category|escape}" width="{$subcategory.image_x}" height="{$subcategory.image_y}" /></a>
    {else}
      <img src="{$ImagesDir}/spacer.gif" alt="" width="1" height="{$subcat_img_height}" />
    {/if}
    <br />
    <a href="home.php?cat={$subcategory.categoryid}">{$subcategory.category|escape}</a><br />
    {if $config.Appearance.count_products eq "Y"}
      {if $subcategory.product_count}
        {$lng.lbl_N_products|substitute:products:$subcategory.product_count}
      {elseif $subcategory.subcategory_count}
        {$lng.lbl_N_categories|substitute:count:$subcategory.subcategory_count}
      {/if}
    {/if}
  </span>

{/foreach}
<div class="clearing"></div>
<br />
