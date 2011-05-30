{*
$Id: head.tpl,v 1.1 2010/05/21 08:31:52 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="line1">
  <script src="{$AltSkinDir}/hr.js" type="text/javascript"></script>
  <div class="logo">
    <div class="logo1">
      <a onclick="javascript: $('#hr_menu').hide();" href="{$catalogs.customer}/home.php"><img src="{$AltImagesDir}/custom/company_logo.gif" alt="" /></a>
      <div class="logo2"><img src="{$ImagesDir}/spacer.gif" alt="" /></div>
    </div>
  </div>

  {include file="customer/tabs.tpl"}

  {include file="customer/phones.tpl"}

</div>

<div class="line2">
  {if ($main ne 'cart' or $cart_empty) and $main ne 'checkout'}

    {include file="customer/search.tpl"}

    {include file="customer/language_selector.tpl"}

  {else}

    {include file="modules/`$checkout_module`/head.tpl"}

  {/if}
</div>

{if $categories_menu_list}

  <div class="top-categories">

    <div class="hor-categories">
      <ul id="hr_list">

        {foreach from=$categories_menu_list item=c}
          <li><a href="home.php?cat={$c.categoryid}" title="{$c.category|escape}">{$c.category}</a></li>
        {/foreach}

      </ul>
    </div>

    <div class="more-categories" onmouseover="javascript: hrMenuShow('cat_list');" onmouseout="javascript: hrMenuHide('cat_list');">
      <a href="javascript:void(0);"><img id="menu_more" src="{$AltImagesDir}/custom/menu_more.gif" alt="" /></a>

        <ul id="hr_menu" style="display: none;">
          <li>&nbsp;</li>
        </ul>
    </div>

  </div>

{/if}

{include file="customer/noscript.tpl"}
