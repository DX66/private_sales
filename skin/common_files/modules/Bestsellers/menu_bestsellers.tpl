{*
$Id: menu_bestsellers.tpl,v 1.1.2.1 2010/10/22 07:52:53 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $config.Bestsellers.bestsellers_menu eq "Y" and $bestsellers}

  {capture name=menu}
    <ul>

      {foreach from=$bestsellers item=b name=bestsellers}
        <li{interline name=bestsellers}>
          <a href="product.php?productid={$b.productid}&amp;cat={$cat}&amp;bestseller=Y">{$b.product|amp}</a>
        </li>
      {/foreach}

    </ul>
  {/capture}
  {include file="customer/menu_dialog.tpl" title=$lng.lbl_bestsellers content=$smarty.capture.menu additional_class="menu-bestsellers"}

{/if}
