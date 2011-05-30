{*
$Id: head_admin.tpl,v 1.1.2.1 2011/01/05 11:23:51 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $login ne ""}
{include file="quick_search.tpl"}
{/if}

<div id="head-admin">

  <div id="logo-gray">
    <a href="home.php"><img src="{$ImagesDir}/logo_gray.png" alt="" /></a>
  </div>

{if $login ne ""}
  {include file="authbox_top.tpl"}
{/if}

{******** Remove this line to display how much products there are online ****
  <div class="head-admin-products-online">
    {insert name="productsonline" assign="_productsonline"}
    {if $config.Appearance.show_in_stock eq "Y"}
      {insert name="itemsonline" assign="_itemsonline"}
      {$lng.lbl_products_and_items_online|substitute:"X":$_productsonline:"Y":$_itemsonline}
    {else}
      {$lng.lbl_products_online|substitute:"X":$_productsonline}
    {/if}
  </div>
**** Remove this line to display how much products there are online ********}
</div>
