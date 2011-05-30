{*
$Id: main.tpl,v 1.1 2010/05/21 08:32:20 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<h1>{$lng.lbl_download}</h1>

{capture name=dialog}

  {if $product}

    <div class="product-details">

      <div class="image"{if $max_image_width gt 0} style="width: {$max_image_width}px;"{/if}>

        <div class="image-box">
        <a href="product.php?productid={$product.productid}">{include file="product_thumbnail.tpl" productid=$product.image_id image_x=$image_width image_y=$product.image_y product=$product.product tmbn_url=$product.image_url id="product_thumbnail" type=$product.image_type}</a>
        </div>

      </div>

      <a href="product.php?productid={$product.productid}" class="product-title">{$product.producttitle}</a>

      <br /><br />

      <div class="descr">{$product.fulldescr|default:$product.descr}</div>

      <div class="details"{if $max_image_width gt 0} style="width: {$max_image_width}px;"{/if}>

        <table cellspacing="0" cellpadding="0" class="product-properties" summary="{$lng.lbl_description|escape}">

          <tr>
            <td colspan="2" class="product-subtitle"><div>{$lng.lbl_details}</div></td>
          </tr>

          <tr>
            <td class="property-name">{$lng.lbl_sku}</td>
            <td class="property-value" id="product_code">{$product.productcode|escape}</td>
          </tr>

          {if $active_modules.Extra_Fields ne ""}
            {include file="modules/Extra_Fields/product.tpl"}
          {/if}

        </table>

      </div>

    </div>

    <div class="text-block text-pre-block">{$lng.lbl_download_msg}</div>

    <div class="button-row">
      {include file="customer/buttons/button.tpl" button_title=$lng.lbl_download href=$url title=$title_length}
      &nbsp;&nbsp;&nbsp;{$title_length}
    </div>

  {else}

    {$lng.lbl_download_errmsg}

  {/if}

{/capture}
{include file="customer/dialog.tpl" title=$lng.lbl_download content=$smarty.capture.dialog noborder=true}
