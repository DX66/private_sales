{*
$Id: categories.tpl,v 1.2.2.1 2010/12/17 15:12:37 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $categories_menu_list ne ''}
{capture name=menu}

{if $active_modules.Flyout_Menus}

  <img src="{$ImagesDir}/spacer.gif" alt="" class="fancy-height-extender" />

  {include file="modules/Flyout_Menus/categories.tpl"}
  {assign var="additional_class" value="menu-fancy-categories-list"}

{else}

  <img src="{$ImagesDir}/spacer.gif" alt="" class="height-extender" />

  <ul>
    {foreach from=$categories_menu_list item=c name=categories}
      <li{interline name=categories}><a href="home.php?cat={$c.categoryid}" title="{$c.category|escape}">{$c.category|amp}</a></li>
    {/foreach}
  </ul>

  {assign var="additional_class" value="menu-categories-list"}

{/if}

<div class="categories-clearing"></div>

{/capture}
{include file="customer/menu_dialog.tpl" title=$lng.lbl_categories content=$smarty.capture.menu}
{/if}
