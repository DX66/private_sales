{*
$Id: related_products.tpl,v 1.1.2.1 2010/10/04 11:51:54 ferz Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $product_links ne "" and $printable ne "Y"}

  {capture name=dialog}

    {include file="customer/simple_products_list.tpl" title=$lng.lbl_related_products products=$product_links open_new_window=$config.Upselling_Products.upselling_new_window class="uproducts"}

  {/capture}

  {if $nodialog}
    {$smarty.capture.dialog}
  {else}
    {include file="customer/dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_related_products}
  {/if}

{/if}
