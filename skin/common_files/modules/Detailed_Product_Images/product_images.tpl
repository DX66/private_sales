{*
$Id: product_images.tpl,v 1.2 2010/07/27 08:29:40 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $images ne ""}

  {capture name=dialog}

    {foreach from=$images item=i name=images}
      <div class="dpimage-container">
        <img src="{if $i.image_url}{$i.image_url|amp}{else}{$xcart_web_dir}/image.php?id={$i.imageid}&amp;type=D{/if}" alt="{$i.alt|escape}"{if $smarty.foreach.images.last} class="last"{/if} />
      </div>
    {/foreach}

  {/capture}
  {if $nodialog}
    <div class="dpimages-list">
      {$smarty.capture.dialog}
    </div>
  {else}
    {include file="customer/dialog.tpl" title=$lng.lbl_detailed_images content=$smarty.capture.dialog additional_class="dpimages-list"}
  {/if}

{/if}
