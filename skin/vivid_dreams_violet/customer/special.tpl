{*
$Id: special.tpl,v 1.4 2010/08/04 10:09:05 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=submenu}

  {if $active_modules.Manufacturers ne "" and $config.Manufacturers.manufacturers_menu ne "Y"}
    <li><a href="manufacturers.php">{$lng.lbl_manufacturers}</a></li>
  {/if}

  {if $active_modules.Gift_Certificates ne ""}
    {include file="modules/Gift_Certificates/gc_menu.tpl"}
  {/if}

  {if $active_modules.Feature_Comparison ne ""}
    {include file="modules/Feature_Comparison/customer_menu.tpl"}
  {/if}

  {if $active_modules.Survey ne ""}
    {include file="modules/Survey/menu_special.tpl"}
  {/if}

  {if $active_modules.Special_Offers ne ""}
    {include file="modules/Special_Offers/menu_special.tpl"}
  {/if}

  {if $active_modules.Wishlist and $active_modules.Gift_Registry}
    <li><a href="giftreg_manage.php">{$lng.lbl_gift_registry}</a></li>
  {/if}

  {if $active_modules.Wishlist and $wlid ne ""}
    <li><a href="cart.php?mode=friend_wl&amp;wlid={$wlid|escape}">{$lng.lbl_friends_wish_list}</a></li>
  {/if}

  {if $user_subscription ne ""}
    <li><a href="orders.php?mode=subscriptions">{$lng.lbl_subscriptions_info}</a></li>
  {/if}

  {if $active_modules.Gift_Registry or $active_modules.RMA or $active_modules.Special_Offers}
    <li class="separator">&nbsp;</li>
  {/if}

  {if $active_modules.Gift_Registry ne ""}
    {include file="modules/Gift_Registry/giftreg_menu.tpl"}
  {/if}

  {if $active_modules.RMA}
    {include file="modules/RMA/customer/menu.tpl"}
  {/if}

  {if $active_modules.Special_Offers ne ""}
    {include file="modules/Special_Offers/menu_cart.tpl"}
  {/if}

  {if $active_modules.Sitemap ne ""}
    {include file="modules/Sitemap/menu_item.tpl"}
  {/if}

  {if $active_modules.Products_Map ne ""}
    {include file="modules/Products_Map/menu_item.tpl"}
  {/if}

{/capture}
{if $smarty.capture.submenu|trim}
  {capture name=menu}
    <ul>
      {$smarty.capture.submenu|trim}
    </ul>
  {/capture}
  {include file="customer/menu_dialog.tpl" title=$lng.lbl_special content=$smarty.capture.menu additional_class="menu-special"}
{/if}
