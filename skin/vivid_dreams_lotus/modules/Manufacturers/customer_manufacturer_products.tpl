{*
$Id: customer_manufacturer_products.tpl,v 1.1 2010/05/21 08:33:05 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<h1>{$manufacturer.manufacturer}</h1>

{if $manufacturer.is_image eq 'Y' or $manufacturer.descr ne '' or $manufacturer.url ne ''}

  <div class="text-block">

  {if $manufacturer.is_image eq 'Y'}
  <img src="{if $manufacturer.image_url ne ''}{$manufacturer.image_url|amp}{else}{$xcart_web_dir}/image.php?id={$manufacturer.manufacturerid}&amp;type=M{/if}" alt="{$manufacturer.manufacturer|escape}"{if $manufacturer.image_x} width="{$manufacturer.image_x}"{/if}{if $manufacturer.image_y} height="{$manufacturer.image_y}"{/if} />
  {/if}

  {if $manufacturer.url ne ''}
    <div class="man-url">
    {$lng.lbl_url}:
    <a href="{$manufacturer.url|escape:htmlall}">{$manufacturer.url|escape}</a>
    </div>
  {/if}

  {$manufacturer.descr}
  </div>

{/if}

{capture name=dialog}

  {if $products ne ''}

    {include file="customer/main/navigation.tpl"}

    {include file="customer/main/products.tpl"}

    {include file="customer/main/navigation.tpl"}

  {else}

    {$lng.txt_no_products_in_man}

  {/if}

{/capture}
{include file="customer/dialog.tpl" title=$lng.lbl_products content=$smarty.capture.dialog selected=$sort direction=$sort_direction products_sort_url="manufacturers.php?manufacturerid=`$manufacturer.manufacturerid`" sort=true additional_class="products-dialog"}
