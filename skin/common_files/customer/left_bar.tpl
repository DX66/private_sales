{*
$Id: left_bar.tpl,v 1.2 2010/06/08 10:17:47 igoryan Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="customer/categories.tpl"}

{if $active_modules.Bestsellers}
  {include file="modules/Bestsellers/menu_bestsellers.tpl"}
{/if}

{if $active_modules.Manufacturers ne "" and $config.Manufacturers.manufacturers_menu eq "Y"}
  {include file="modules/Manufacturers/menu_manufacturers.tpl"}
{/if}

{include file="customer/special.tpl"}

{if $active_modules.Survey and $menu_surveys}
  {foreach from=$menu_surveys item=menu_survey}
    {include file="modules/Survey/menu_survey.tpl"}
  {/foreach}
{/if}

{include file="customer/help/menu.tpl"}
