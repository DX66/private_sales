{*
$Id: fancy_subcategories_exp.tpl,v 1.2 2010/07/05 12:40:19 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<ul class="fancycat-icons-level-{$level}">

  {assign var="loop_name" value="subcat`$parentid`"}
  {foreach from=$categories_menu_list item=c key=catid name=$loop_name}

    {assign var=additional_class value=''}

    {if $config.Flyout_Menus.icons_disable_subcat_triangle eq 'Y' and $c.subcategory_count gt 0}
      {assign var=additional_class value="sub-link"}

      {if not $c.expanded}
        {assign var=additional_class value=$additional_class|cat:" closed"}
      {/if}

    {/if}

    <li id="cat-layer-{$c.categoryid}"{interline name=$loop_name additional_class=$additional_class}>
      {strip}
      {if $config.Flyout_Menus.icons_disable_subcat_triangle eq 'Y' and $c.subcategory_count gt 0}
        <a href="home.php?cat={$catid}" class="arrow" onclick="javascript: return switchSubcatLayer(this);"></a>
      {/if}
      <a href="home.php?cat={$catid}" class="{if $config.Flyout_Menus.icons_icons_in_categories gte $level+1}icon-link{/if}{if $config.Flyout_Menus.icons_disable_subcat_triangle eq 'Y' and $c.subcategory_count gt 0} sub-link{/if}{if $config.Flyout_Menus.icons_empty_category_vis eq 'Y' and not $c.childs and not $c.product_count} empty-link{/if}{if $config.Flyout_Menus.icons_nowrap_category ne 'Y'} nowrap-link{/if}">
        {$c.category|amp}
        {if $config.Flyout_Menus.icons_display_products_cnt eq 'Y' and $c.top_product_count gt 0}
          &#32;({$c.top_product_count})
        {/if}
      </a>
      {/strip}

      {if $c.childs and $c.subcategory_count gt 0 and ($config.Flyout_Menus.icons_levels_limit eq 0 or $config.Flyout_Menus.icons_levels_limit gt $level)}
        {include file="`$fc_skin_path`/fancy_subcategories_exp.tpl" categories_menu_list=$c.childs parentid=$catid level=$level+1}
      {/if}
    </li>

  {/foreach}

</ul>
