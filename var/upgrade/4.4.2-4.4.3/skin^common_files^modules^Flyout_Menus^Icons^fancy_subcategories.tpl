{*
$Id: fancy_subcategories.tpl,v 1.2 2010/06/08 10:17:47 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<ul class="fancycat-icons-level-{$level}">

  {assign var="loop_name" value="subcat`$parentid`"}
  {foreach from=$categories_menu_list item=c key=catid name=$loop_name}

    <li{interline name=$loop_name} style="z-index: {$smarty.foreach.$loop_name.total|dec:$smarty.foreach.$loop_name.iteration|inc:1|inc:100};">
      {strip}
      <a href="home.php?cat={$catid}" class="{if $config.Flyout_Menus.icons_icons_in_categories gte $level+1}icon-link{/if}{if $config.Flyout_Menus.icons_disable_subcat_triangle eq 'Y' and $c.subcategory_count gt 0} sub-link{/if}{if $config.Flyout_Menus.icons_empty_category_vis eq 'Y' and not $c.childs and not $c.product_count} empty-link{/if}{if $config.Flyout_Menus.icons_nowrap_category ne 'Y'} nowrap-link{/if}">
        {if $config.Flyout_Menus.icons_icons_in_categories gte $level+1 and $c.is_icon}
          <img src="{$c.thumb_url|amp}" alt="" width="{$c.thumb_x}" height="{$c.thumb_y}" />
        {/if}
        {$c.category|amp}
        {if $config.Flyout_Menus.icons_display_products_cnt eq 'Y' and $c.top_product_count gt 0}
          &#32;({$c.top_product_count})
        {/if}
      </a>
      {/strip}

      {if $c.childs and $c.subcategory_count gt 0 and ($config.Flyout_Menus.icons_levels_limit eq 0 or $config.Flyout_Menus.icons_levels_limit gt $level)}
        {include file="`$fc_skin_path`/fancy_subcategories.tpl" categories_menu_list=$c.childs parentid=$catid level=$level+1}
      {/if}
    </li>

  {/foreach}

</ul>
