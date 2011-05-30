{*
$Id: pconf_customer_step.tpl,v 1.3.2.3 2010/12/15 11:57:05 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<script type="text/javascript">
//<![CDATA[
var txt_pconf_clean_slot_js_confirmation = '{$lng.txt_pconf_clean_slot_js_confirmation|wm_remove|escape:javascript|strip_tags}';
var txt_pconf_reset_js_confirmation = '{$lng.txt_pconf_reset_js_confirmation|wm_remove|escape:javascript|strip_tags}';
var txt_pconf_clean_slot_js_confirmation = '{$lng.txt_pconf_clean_slot_js_confirmation|wm_remove|escape:javascript|strip_tags}';
//]]>
</script>

{if $smarty.get.slot eq ""}

  <h1>{$product.producttitle}: {$lng.lbl_step} {$step}</h1>

  {capture name=dialog}

  <div class="product-details pconf-product-configure">

    <div class="image">

      <div class="image-box">
        {include file="product_thumbnail.tpl" productid=$product.image_id image_x=$product.image_x image_y=$product.image_y product=$product.product tmbn_url=$product.image_url id="product_thumbnail" type=$product.image_type}
      </div>

    </div>

    <div class="details"{if $config.Appearance.image_width gt 0 or $product.image_x gt 0} style="margin-left: {$config.Appearance.image_width|default:$product.image_x}px;"{/if}>

      <div class="pconf-step-title">{$lng.lbl_step} {$step}: {$wizard_data.step_name}</div>
      {if $wizard_data.step_descr}
        <div class="pconf-step-descr">{$wizard_data.step_descr}</div>
      {/if}

      {if $wizard_data.slots}

        {foreach from=$wizard_data.slots item=slot name=slots}

          <div class="pconf-slot-configure{if $smarty.foreach.slots.first} pconf-slot-configure-first{/if}">
            <div class="pconf-slot-header">
              <div class="pconf-slot-title">
                {$slot.slot_name}
                {if $slot.status eq 'M'}
                  <span class="pconf-slot-required">({$lng.lbl_slot_required})</span>
                {/if}
              </div>
              <div class="pconf-slot-actions">
                <div class="buttons-row-right">
                  {if $slot.product}
                    {include file="customer/buttons/button.tpl" button_title=$lng.lbl_change href="pconf.php?productid=`$product.productid`&amp;slot=`$slot.slotid`" style="link"}
                    <div class="button-separator"></div>
                    {include file="customer/buttons/button.tpl" button_title=$lng.lbl_pconf_clean_slot href="pconf.php?productid=`$product.productid`&amp;slot=`$slot.slotid`&amp;mode=delete" style="link" additional_button_class="simple-delete-button-woicon"}
                  {else}
                    {include file="customer/buttons/button.tpl" button_title=$lng.lbl_select href="pconf.php?productid=`$product.productid`&amp;slot=`$slot.slotid`" style="link"}
                  {/if}
                </div>
              </div>
            </div>
            {if $slot.slot_descr}
              <div class="pconf-slot-descr">{$slot.slot_descr}</div>
            {/if}

            {if $slot.have_rules}

              {if $slot.product}
                <div class="pconf-slot-product">
                  <div class="image">
                    {include file="product_thumbnail.tpl" productid=$slot.product.productid image_x=$slot.product.slot_image_x image_y=$slot.product.slot_image_y product=$slot.product.product tmbn_url=$slot.product.image_url}
                  </div>
                  <div class="pconf-slot-product-details"{if $config.Appearance.thumbnail_width gt 0 or $slot.product.slot_image_x gt 0} style="margin-left: {$slot.product.slot_image_x|default:$config.Appearance.thumbnail_width}px;"{/if}>
                    <a class="product-title" href="product.php?productid={$slot.product.productid}" target="_blank">{$slot.product.product}</a>

                    {if $slot.product.product_options ne ""}
                      <p>
                        <strong>{$lng.lbl_selected_options}:</strong>
                        {include file="modules/Product_Options/display_options.tpl" options=$slot.product.product_options}
                      </p>
                    {/if}

                    <div class="pconf-price-row">
                      <span class="price">{$lng.lbl_price}:</span>
                      <span class="price-value">{currency value=$slot.product.taxed_price}</span>
                      <span class="market-price">{alter_currency value=$slot.product.taxed_price}</span>

                     {if $slot.product.amount gt 1}
                      <span class="price"> x {$slot.product.amount} = </span>
                      {multi x=$slot.product.taxed_price y=$slot.product.amount assign=subtotal}
                      <span class="price-value">{currency value=$subtotal}</span>
                      <span class="market-price">{alter_currency value=$subtotal}</span>
                      {/if}
                    </div>

                    {if $slot.product.taxes ne ''}
                      {include file="customer/main/taxed_price.tpl" taxes=$slot.product.taxes}
                    {/if}

                    {if $slot.price_modifier}
                      <p>
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

                  </div>
                  <div class="clearing"></div>
                </div>

              {/if}

            {else}

              &lt;{$lng.lbl_pconf_no_rules_defined}&gt;

            {/if}

          </div>

        {/foreach}

      {/if}

      {if $need_reset_btn or $need_clean_btn or $need_cleanall_btn}
        <div class="buttons-row-right">
          {if $need_reset_btn}
            {include file="customer/buttons/button.tpl" button_title=$lng.lbl_pconf_reset_configuration style="button" href="javascript: if(confirm(txt_pconf_reset_js_confirmation)) self.location = 'pconf.php?productid=`$product.productid`&amp;mode=reset';" style="link" additional_button_class="simple-delete-button"}
            <div class="button-separator"></div>
          {/if}
          {if $need_cleanall_btn}
            {include file="customer/buttons/button.tpl" button_title=$lng.lbl_pconf_clean_configuration style="button" href="javascript: if(confirm(txt_pconf_clean_slot_js_confirmation)) self.location = 'pconf.php?productid=`$product.productid`&amp;mode=clean';" style="link" additional_button_class="simple-delete-button"}
          {/if}
          {if $need_clean_btn}
            <div class="button-separator"></div>
            {include file="customer/buttons/button.tpl" href="javascript: if(confirm(txt_pconf_clean_slot_js_confirmation)) self.location='pconf.php?productid=`$product.productid`&amp;step=`$step`&amp;mode=clean';" style="link" button_title=$lng.lbl_pconf_clean_slots additional_button_class="simple-delete-button"}
          {/if}
        </div>
        <div class="text-pre-block"></div>
      {/if}

      {if $previous_step or $continue_button}

        <div class="buttons-row-right">
          {if $continue_button}
            {include file="customer/buttons/button.tpl" button_title=$lng.lbl_continue style="button" href="pconf.php?productid=`$product.productid`&amp;mode=continue"}
            {if $previous_step}
              <div class="button-separator"></div>
            {/if}
          {/if}
          {if $previous_step}
            {include file="customer/buttons/button.tpl" button_title=$lng.lbl_go_back style="button" href="pconf.php?productid=`$product.productid`&amp;mode=back"}
          {/if}
        </div>

      {/if}

    </div>

  </div>

  {/capture}
  {include file="customer/dialog.tpl" title="`$product.producttitle`: `$lng.lbl_step` `$step`" content=$smarty.capture.dialog noborder=true}

{else}

  <h1>{$lng.lbl_pconf_select_slot|substitute:slotname:$slot_data.slot_name}</h1>

  {capture name=dialog}

    <table cellspacing="0" class="pconf-rules" summary="{$lng.lbl_pconf_slot_rules|escape}">
      <tr>
        <td>{$lng.lbl_pconf_displaying_products}:</td>
        <td>
          {foreach from=$rules_by_or item=rules name=rules_by_or}
            {foreach from=$rules.rules_by_and item=r name=rules}
              {$r.ptype_name}
              {if not $smarty.foreach.rules.last}
                <strong>&lt;{$lng.lbl_pconf_and}&gt;</strong>
              {/if}
            {/foreach}
            {if not $smarty.foreach.rules_by_or.last}
              <strong class="pconf-rules-or">&lt;{$lng.lbl_pconf_or}&gt;</strong>
            {/if}
          {/foreach}

          {if $filled_slots}
            <br />
            {$lng.lbl_pconf_compatible_with}:<br />
            {foreach from=$filled_slots item=fslot}
              {$fslot.slot_name}: {$fslot.product.product}<br />
            {/foreach}
          {/if}
        </td>
      </tr>
    </table>

    {if $slot_products}

      {include file="customer/main/navigation.tpl"}
      {include file="modules/Product_Configurator/products.tpl" products=$slot_products}
      {include file="customer/main/navigation.tpl"}

    {else}

      <hr />
      <div class="center">
        <strong>{$lng.lbl_pconf_no_products_found}</strong>
      </div>

    {/if}

  {/capture}
  {if $slot_products}
    {assign var=sort value=true}
  {/if}
  {include file="customer/dialog.tpl" title=$lng.lbl_products content=$smarty.capture.dialog}

{/if}
