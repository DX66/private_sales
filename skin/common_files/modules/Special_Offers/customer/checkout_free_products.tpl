{*
$Id: checkout_free_products.tpl,v 1.1 2010/05/21 08:32:48 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=dialog}

  {if $free_products ne ""}

    {include file="customer/main/navigation.tpl"}

    <hr />

    {include file="customer/main/products.tpl" products=$free_products}

    <hr />

    {include file="customer/main/navigation.tpl"}

  {else}

    <p class="center">{$lng.msg_sp_no_free_products_to_add}</p>

    <hr />

  {/if}

  <div class="button-row-right">

    {if $offers_return_url ne ""}

      {if $offers_return_checkout}
        {include file="customer/buttons/button.tpl" button_title=$lng.lbl_continue href=$offers_return_url}
      {else}
        {include file="customer/buttons/button.tpl" button_title=$lng.lbl_continue_shopping href=$offers_return_url}
      {/if}

    {else}

      {include file="customer/buttons/button.tpl" button_title=$lng.lbl_continue_shopping href="cart.php" style="link"}

    {/if}

  </div>
  <div class="clearing"></div>

{/capture}
{if $free_products ne ""}
  {assign var=sort value=true}
{/if}
{include file="customer/dialog.tpl" title=$lng.lbl_sp_add_free_products_title content=$smarty.capture.dialog products_sort_url="offers.php?mode=add_free&amp;offers_return_url=`$offers_return_url`" additional_class="products-dialog"}
