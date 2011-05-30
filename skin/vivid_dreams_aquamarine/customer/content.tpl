{*
$Id: content.tpl,v 1.2 2010/06/17 09:59:25 igoryan Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
<div id="center">
  <div id="center-main">
    {include file="customer/evaluation.tpl"}
<!-- central space -->

    {if ($main eq 'cart' and not $cart_empty) or $main eq 'checkout'}

      {include file="modules/`$checkout_module`/content.tpl"}

    {else}

      {if $main neq "catalog" or $current_category.category neq ""}
        {include file="customer/bread_crumbs.tpl"}
      {/if}

      {if $main ne "cart" and $main ne "checkout" and $main ne "order_message"}
        {if $gcheckout_enabled}
          {include file="modules/Google_Checkout/gcheckout_top_button.tpl"}
        {/if}
        {if $amazon_enabled}
          {include file="modules/Amazon_Checkout/amazon_top_button.tpl"}
        {/if}
      {/if}

      {include file="customer/dialog_message.tpl"}

      {if $active_modules.Special_Offers}
        {include file="modules/Special_Offers/customer/new_offers_message.tpl"}
      {/if}

      {if $page_tabs ne ''}
        {include file="customer/main/top_links.tpl" tabs=$page_tabs}
      {/if}

      {if $page_title}
        <h1>{$page_title|escape}</h1>
      {/if}

      {include file="customer/home_main.tpl"}

    {/if}

<!-- /central space -->

  </div><!-- /center -->

  {if $main ne 'checkout' and $main ne 'cart'}
    <div class="block-news-links">
      <div class="block-news-links-2">

        <div class="imgv-box">
          <img src="{$ImagesDir}/spacer.gif" alt="" class="imgv" />
        </div>

        <table cellpadding="0" cellspacing="0" summary="{$lng.lbl_news|escape}">
          <tr>
            {include file="customer/news.tpl"}
            <td>{include file="customer/special.tpl"}</td>
            <td>{include file="customer/help/menu.tpl"}</td>
          </tr>
        </table>

        {if $active_modules.Users_online}
          {include file="modules/Users_online/menu_users_online.tpl"}
        {/if}

      </div>
    </div>
  {/if}

</div><!-- /center-main -->

{if ($main neq 'cart' or $cart_empty) and $main neq 'checkout'}
<div id="left-bar">
  {include file="customer/left_bar.tpl"}
</div>
{/if}
