{*
$Id: pconf_customer_product.tpl,v 1.1.2.4 2010/12/15 11:57:05 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=dialog}
  <div class="product-details pconf-product-details">

   <div class="image"{if $max_image_width gt 0} style="width: {$max_image_width}px;"{/if}>

      {if $active_modules.Detailed_Product_Images and $config.Detailed_Product_Images.det_image_popup eq 'Y' and $images ne ''}

        {include file="modules/Detailed_Product_Images/popup_image.tpl"}

      {else}

        <div class="image-box">
          {include file="product_thumbnail.tpl" productid=$product.image_id image_x=$product.image_x image_y=$product.image_y product=$product.product tmbn_url=$product.image_url id="product_thumbnail" type=$product.image_type}
        </div>

      {/if}

      {if $active_modules.Magnifier and $config.Magnifier.magnifier_image_popup eq 'Y' and $zoomer_images}
        {include file="modules/Magnifier/popup_magnifier.tpl"}
      {/if}

    </div>

    <div class="details"{if $max_image_width gt 0} style="margin-left: {$max_image_width}px;"{/if}>

       <table cellspacing="0" cellpadding="0" summary="{$lng.lbl_description|escape}">

          <tr>
            <td colspan="2" class="descr">{$product.fulldescr|default:$product.descr}</td>
          </tr>

       </table>

       <table cellspacing="0" class="product-properties" summary="{$lng.lbl_description|escape}">

          <tr>
            <td colspan="3" class="product-subtitle">
              <div>{$lng.lbl_details}</div>
            </td>
          </tr>

          <tr>
            <td class="property-name">{$lng.lbl_sku}</td>
            <td class="property-value" id="product_code">{$product.productcode|escape}</td>
          </tr>

          {if $product.taxed_price gt 0 or $variant_price_no_empty}
            <tr>
              <td class="property-name product-price" valign="top">{$lng.lbl_pconf_base_price}:</td>
              <td class="property-value" valign="top">
                <span class="product-price-value">{currency value=$product.taxed_price tag_id="product_price"}</span>
                <span class="product-market-price">{alter_currency value=$product.taxed_price tag_id="product_alt_price"}</span>
                {if $product.taxes}
                  <br />{include file="customer/main/taxed_price.tpl" taxes=$product.taxes}
                {/if}
              </td>
            </tr>
          {/if}

       </table>

      <div class="button-row">
        {include file="customer/buttons/button.tpl" button_title=$lng.lbl_pconf_configure href="pconf.php?productid=`$product.productid`" additional_button_class="main-button"}
      </div>

    </div>

    <div class="clearing"></div>

  </div>
{/capture}
{include file="customer/dialog.tpl" title=$product.producttitle content=$smarty.capture.dialog noborder=true}
