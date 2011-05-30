{*
$Id: pconf_customer_summary.tpl,v 1.3.2.3 2010/12/15 11:57:05 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<script type="text/javascript">
//<![CDATA[
var txt_pconf_reset_js_confirmation = '{$lng.txt_pconf_reset_js_confirmation|wm_remove|escape:javascript|strip_tags}';
var txt_pconf_clean_slot_js_confirmation = '{$lng.txt_pconf_clean_slot_js_confirmation|wm_remove|escape:javascript|strip_tags}';
//]]>
</script>

<h1>{$product.producttitle}: {$lng.lbl_summary}</h1>

{include file="form_validation_js.tpl"}

{capture name=dialog}

  {if $smarty.get.error eq "amount"}
    <p class="message">{$lng.err_pconf_avail_exceeded}</p>
  {/if}

  <div class="product-details pconf-product-summary">

    <div class="image">

      <div class="image-box">
        {include file="product_thumbnail.tpl" productid=$product.image_id image_x=$product.image_x image_y=$product.image_y product=$product.product tmbn_url=$product.image_url id="product_thumbnail" type=$product.image_type}
      </div>

    </div>

    <div class="details"{if $config.Appearance.image_width gt 0 or $product.image_x gt 0} style="margin-left: {$config.Appearance.image_width|default:$product.image_x}px;"{/if}>

      <p class="pconf-summary">{$lng.lbl_summary}:</p>

      {foreach from=$wizards item=wz name=steps}

        <div class="pconf-summary-step">
          <div class="pconf-step-title">
            <strong>{$wz.step_name}</strong> ({$lng.lbl_pconf_step_n|substitute:"number":$smarty.foreach.steps.iteration})
            <div class="button-row">
            {strip}
              {include file="customer/buttons/modify.tpl" href="pconf.php?productid=`$product.productid`&amp;step=`$wz.step_counter`&amp;mode=update" style="link"}
            {/strip}
            </div>
          </div>

          <table cellspacing="0" class="pconf-summary-slots" summary="{$wz.step_name|escape}">

            {foreach from=$wz.slots item=slot}

              <tr>
                <td class="pconf-slot-image">
                  {include file="product_thumbnail.tpl" productid=$slot.product.image_id image_x=$slot.product.summary_image_x image_y=$slot.product.summary_image_y product=$slot.product.product tmbn_url=$slot.product.image_url type=$slot.product.image_type}
                </td>
                <td class="pconf-slot-details">
                  <strong class="pconf-slot-title">{$slot.slot_name}:</strong>

                  {if $slot.product.productid}
                    <a href="product.php?productid={$slot.product.productid}" target="productinfo{$slot.product.productid}" class="pconf-slot-product">{$slot.product.product}</a>

                    <p class="pconf-price-row pconf-slot-price">
                      <span class="price">{$lng.lbl_price}:</span>
                      <span class="price-value">{currency value=$slot.product.taxed_price}</span>
                      <span class="market-price">{alter_currency value=$slot.product.taxed_price}</span>

                      {if $slot.product.amount gt 1}
                      <span class="price"> x {$slot.product.amount} = </span>
                      {multi x=$slot.product.taxed_price y=$slot.product.amount assign=subtotal}
                      <span class="price-value">{currency value=$subtotal}</span>
                      <span class="market-price">{alter_currency value=$subtotal}</span>
                      {/if}
                    </p>

                    {if $slot.product.taxes}
                      <div class="pconf-slot-taxes">{include file="customer/main/taxed_price.tpl" taxes=$slot.product.taxes is_subtax=true}</div>
                    {/if}
                    
                    {if $slot.price_modifier}
                      <p class"pconf-slot-price-modifier">
                        {if $slot.price_modifier.markup gt 0}
                          {$lng.lbl_pconf_markup_applied}:
                        {else}
                          {$lng.lbl_pconf_discount_applied}:
                        {/if}
                        <span class="message">
                          {if $slot.price_modifier.markup_type eq "$"}
                            {currency value=$slot.price_modifier.markup}
                          {else}
                            {$slot.price_modifier.markup|abs_value}%
                          {/if}
                        </span>
                      </p>
                    {/if}

                    {if $config.General.unlimited_products ne "Y" and $slot.pconf_out_of_stock and $update eq ''}
                      <p class="message">
                        <strong>{$lng.lbl_note}:</strong>
                        {$lng.lbl_pconf_slot_out_of_stock_note} 
                      <p>
                    {/if}

                  {else}

                    {$lng.txt_not_available}

                  {/if}

                </td>

              </tr>

            {/foreach}

          </table>

        </div>

      {/foreach}
      
      <div class="left-buttons-row buttons-row">
        {if $need_reset_btn}
          {include file="customer/buttons/button.tpl" button_title=$lng.lbl_pconf_reset_configuration style="button" href="javascript: if(confirm(txt_pconf_reset_js_confirmation)) self.location = 'pconf.php?productid=`$product.productid`&amp;mode=reset';" style="link" additional_button_class="simple-delete-button"}
          <div class="button-separator"></div>
        {/if}
        {include file="customer/buttons/button.tpl" button_title=$lng.lbl_pconf_clean_configuration style="button" href="javascript: if(confirm(txt_pconf_clean_slot_js_confirmation)) self.location = 'pconf.php?productid=`$product.productid`&amp;mode=clean';" style="link" additional_button_class="simple-delete-button"}
      </div>

      <form name="orderform" method="post" action="cart.php">
        <input type="hidden" name="mode" value="add" />
        <input type="hidden" name="productid" value="{$smarty.get.productid}" />
        <input type="hidden" name="price" value="{$total_cost}" />

        <table cellspacing="0" class="product-properties" summary="{$lng.lbl_details|escape}">
          {if $product.taxed_price gt 0}
            <tr class="pconf-price-row">
              <td><span class="price">{$lng.lbl_pconf_base_price}:</span></td>
              <td><span class="price-value">{currency value=$product.taxed_price}</span></td>
            </tr>
          {/if}

          {if $active_modules.Product_Options and $product_options}
            {assign_ext var="product[taxes]" value=$taxes}
            {include file="modules/Product_Options/customer_options.tpl"}
          {/if}

          <tr class="pconf-price-row">
            <td class="pconf-summary-total"><span class="price">{$lng.lbl_pconf_total_products_cost}:</span></td>
            <td class="pconf-summary-total"><span class="price-value">{currency value=$taxed_total_cost tag_id="product_price"}</span></td>
          </tr>

          {if $taxes}
          <tr>
            <td colspan="2">
              {include file="customer/main/taxed_price.tpl" taxes=$taxes}
            </td>
          </tr>
          {/if}

          <tr>
            <td class="property-name product-input">{$lng.lbl_quantity}</td>
            <td>

              {if $product.appearance.empty_stock and $variants eq '' or ($variants ne '' and $product.avail le 0)}

<script type="text/javascript">
//<![CDATA[
var min_avail = 1;
var avail = 0;
var product_avail = 0;
//]]>
</script>

                <strong>{$lng.txt_out_of_stock}</strong>

              {elseif not $product.appearance.force_1_amount and $product.forsale ne "B"}

<script type="text/javascript">
//<![CDATA[
var min_avail = {$product.appearance.min_quantity|default:1};
var avail = {$product.appearance.max_quantity|default:1};
var product_avail = {$product.avail|default:"0"};
//]]>
</script>

                <select id="product_avail" name="amount"{if $active_modules.Product_Options ne '' and ($product_options ne '' or $product_wholesale ne '')} onchange="javascript: check_wholesale(this.value);"{/if}>
                  {section name=quantity loop=$product.appearance.loop_quantity start=$product.appearance.min_quantity}
                    <option value="{%quantity.index%}"{if $smarty.get.quantity eq %quantity.index%} selected="selected"{/if}>{%quantity.index%}</option>
                  {/section}
                </select>

              {else}

<script type="text/javascript">
//<![CDATA[
var min_avail = 1;
var avail = 1;
var product_avail = 1;
//]]>
</script>

                <span class="product-one-quantity">1</span>
                <input type="hidden" name="amount" value="1" />
  
                {if $product.distribution ne ""}
                  {$lng.txt_product_downloadable}
                {/if}

              {/if}

            </td>
          </tr>

        </table>

        <br />

        <div class="buttons-row">
          {if $update}
            {include file="customer/buttons/update.tpl" style="button" href="javascript: if (FormValidation()) `$ldelim` document.orderform.action='pconf.php'; document.orderform.mode.value='pconf_update'; document.orderform.submit(); `$rdelim`"}
          {/if}

          {if ($update eq "" or $update eq "wishlist") and not $pconf_out_of_stock}
            {include file="customer/buttons/add_to_cart.tpl" type="input" additional_button_class="main-button"}
          {/if}

          {if $active_modules.Wishlist and ($login ne "" or $config.Wishlist.add2wl_unlogged_user eq "Y") and ($update eq "" or $update eq "cart")}
            <div class="button-separator"></div>
            {if $product.appearance.dropout_actions}
              {include file="customer/buttons/add_to_list.tpl" id=$product.productid js_if_condition="FormValidation()"}
            {else}
              {include file="customer/buttons/add_to_wishlist.tpl" href="javascript: if (FormValidation()) submitForm(document.orderform, 'add2wl'); return false;"}
            {/if}
          {/if}
        </div>

      </form>

    </div>

    <div class="clearing"></div>

  </div>

  {if $active_modules.Product_Options and $product_options}
<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
check_options();
//]]>
</script>
  {/if}

{/capture}
{include file="customer/dialog.tpl" title="`$product.producttitle`: `$lng.lbl_summary`" content=$smarty.capture.dialog noborder=true}
