{*
$Id: comparison.tpl,v 1.3.2.1 2010/12/15 09:44:40 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $show eq "popup"}
<script type="text/javascript">
//<![CDATA[
{literal}
  function show_fake_image(current) {
    $("a.fcomp-real-image").hide();
    $("img.fcomp-fake-image").show();
    $(current).next().addClass("fcomp-progress-image");
  }

  function add2cart(productid) {
    document.getElementById("addtocartform_productid").value = productid;
    document.addtocartform.submit();
  }
{/literal}
{if $smarty.get.redirect_from_cart eq "Y"}
if (window.opener !== null)
  window.opener.location.reload();
{/if}
//]]>
</script>

{else}

  <h1>{$lng.lbl_product_features_chart}</h1>

{/if}

{capture name=dialog}

  {if $active_modules.Product_Configurator and $pconf_productid and $pconf_slot}
    {assign var=pconf_url_string value="&amp;pconf_productid=`$pconf_productid`&amp;pconf_slot=`$pconf_slot`"}
  {/if}

  {if $matrix.0.productid gt 0 and not $printable}

    <div class="fcomp-toolbar">

      <a href="{$toolbar_url.show_not_equal|amp}{$pconf_url_string}" title="{if $comp_options.show_not_equal eq 'Y'}{$lng.lbl_show_all_features|escape}{else}{$lng.lbl_show_differ_feature|escape}{/if}"><img src="{$ImagesDir}/spacer.gif" class="{if $comp_options.show_not_equal eq 'Y'}fcomp-show-not-equal{else}fcomp-show-equal{/if}" alt="{if $comp_options.show_not_equal eq 'Y'}{$lng.lbl_show_all_features|escape}{else}{$lng.lbl_show_differ_feature|escape}{/if}" /></a>
      <a href="{$toolbar_url.axis|amp}{$pconf_url_string}" title="{$lng.lbl_reverse_axis|escape}"><img src="{$ImagesDir}/spacer.gif" class="fcomp-axis" alt="{$lng.lbl_reverse_axis|escape}" /></a>
      {if $show ne 'popup'}
        <a href="javascript:void(0);" onclick="javascript: window.open('comparison.php?show=popup', 'COMPARISON_POPUP', 'width=600,height=460,toolbar=no,status=no,scrollbars=yes,resizable=yes,menubar=no,location=no,direction=no');" title="{$lng.lbl_open_in_popup_window|escape}"><img src="{$ImagesDir}/spacer.gif" class="fcomp-popup-link" alt="{$lng.lbl_open_in_popup_window|escape}" /></a>
      {/if}

    </div>

  {/if}

  {if $comp_options.disabled_options ne '' and $matrix.0.productid gt 0}
    <div class="fcomp-removed-features">
      <span class="fcomp-removed-features-title">{$lng.lbl_removed_features}:</span>

      {foreach from=$comp_options.disabled_options item=v key=k name=disabled_options}
        {if $v ne ''}
          <a href="comparison.php?mode=add_feature&amp;foptionid={$k}&amp;show={$show}{$pconf_url_string}">{$v}</a>
          {if not $smarty.foreach.disabled_options.last}<span>|</span>{/if}
        {/if}
      {/foreach}
    </div>
  {/if}

  {if $show eq "popup"}
    <form action="cart.php" method="get" name="addtocartform">
    <input type="hidden" name="redirect_to_referer" value="Y" />
    <input type="hidden" name="mode" value="add" />
    <input type="hidden" id="addtocartform_productid" name="productid" value="" />
    <input type="hidden" name="amount" value="1" />
    </form>
  {/if}

  <form action="comparison.php" method="post" name="matrixform" class="fcomp-table">
    <input type="hidden" name="sort" value="{$sort}" />
    <input type="hidden" name="sort_direction" value="{$sort_direction}" />
    <input type="hidden" name="show" value="{$show}" />
    <input type="hidden" name="mode" value="" />
    {if $active_modules.Product_Configurator}
    {include file="modules/Product_Configurator/pconf_hidden_form_fields.tpl"}
    {/if}

    <div class="overflow">
      <table cellspacing="1" class="{if $comp_options.axis eq 'N'}fcomp-yx{else}fcomp-xy{/if}" summary="{$lng.lbl_product_features_chart|escape}">

        {if $matrix.0.productid gt 0}

          {if $comp_options.axis eq 'N'}

            <tr>
              <td class="fcomp-corner">&nbsp;</td>

              {if $matrix_features_cnt gt 0}
                <td colspan="{$matrix_features_cnt}" class="fcomp-title">{$lng.lbl_product_feature}</td>
              {else}
                <td class="fcomp-title" rowspan="2">{$lng.lbl_product_feature}</td>
              {/if}

              {if $config.Appearance.buynow_button_enabled eq "Y"}
                <td rowspan="2" class="fcomp-empty">&nbsp;</td>
              {/if}

            </tr>

            <tr>
              <td class="fcomp-title">{$lng.lbl_products}</td>

              {foreach from=$matrix.0.features item=v}
                <td class="fcomp-title-h">
                  <input type="checkbox" name="foptionids[]" value="{$v.foptionid}" id="foptionid_{$v.foptionid}" /><br />
                  <label for="foptionid_{$v.foptionid}"></label>{include file="modules/Feature_Comparison/option_hint.tpl" opt=$v}
                </td>
              {/foreach}
            </tr>

            {foreach from=$matrix item=v key=matrix_key}
              <tr>
                <td class="fcomp-title-v">
                  <label>
                    <input type="checkbox" name="ids[]" value="{$v.productid}" />
                    <a href="{if $show eq "popup"}javascript:void(0);" onclick="javascript:window.opener.location='{/if}product.php?productid={$v.productid}{$pconf_url_string}{if $show eq "popup"}';{/if}">{$v.product}</a>
                    {if $v.subscription}
                      {include file="modules/Subscriptions/subscription_info_incomparison.tpl" product=$v subscription=$v.subscription}
                    {else}
                      {if $v.taxed_price gt 0}
                        <br />
                        {$lng.lbl_price}: {currency value=$v.taxed_price}
                      {/if}
                    {/if}
                  </label>
                </td>

                {if $matrix_features_cnt gt 0}

                  {foreach from=$v.features item=f}
                    <td class="{if $not_equal_hash[$f.foptionid]}fcomp-hl-cell{else}fcomp-cell{/if}">

                      {if $f.is_empty eq 'Y'}
                        {$lng.txt_not_available}

                      {elseif $f.option_type eq 'T'}
                        {$f.value}

                      {elseif $f.option_type eq 'N' or $f.option_type eq 'D'}
                        {$f.formated_value}

                      {elseif $f.option_type eq 'B' and $f.value eq 'Y'}
                        <img src="{$ImagesDir}/spacer.gif" class="fcomp-yes" alt="{$lng.lbl_yes|escape}" />

                      {elseif $f.option_type eq 'B' and $f.value ne 'Y'}
                        <img src="{$ImagesDir}/spacer.gif" class="fcomp-no" alt="{$lng.lbl_no|escape}" />

                      {elseif $f.option_type eq 'M' or $f.option_type eq 'S'}

                        {foreach from=$f.variants item=fv}
                          {$fv.name}<br />
                        {/foreach}

                      {/if}
                    </td>
                  {/foreach}

                {elseif $matrix_key eq 0}
                  <td rowspan="{$matrix_cnt}" class="fcomp-center-note">{$lng.lbl_no_features_selected}</td>
                {/if}

                {if $config.Appearance.buynow_button_enabled eq "Y"}
                  <td class="fcomp-center-note">
                    {if $active_modules.Product_Configurator and $pconf_productid and $pconf_slot}
                      {include file="modules/Product_Configurator/pconf_add_wo_form.tpl" product=$v}
                    {else}
                      {include file="modules/Feature_Comparison/buy_now.tpl" product=$v}
                    {/if}
                  </td>
                {/if}
              </tr>
            {/foreach}

            {if $printable ne 'Y'}
              <tr>
                {if $matrix_cnt gt 0}
                  <td class="fcomp-delete-cell">
                    <a href="javascript:void(0);" onclick="javascript: submitForm(document.matrixform, 'delete_products');">{$lng.lbl_remove_selected_products}</a>
                  </td>
                {/if}

                {if $matrix_features_cnt gt 0}

                  {if $config.Appearance.buynow_button_enabled eq "Y"}
                    {inc assign="matrix_features_cnt" value=$matrix_features_cnt}
                  {/if}

                  <td class="fcomp-delete-cell" colspan="{$matrix_features_cnt}">
                    <a href="javascript:void(0);" onclick="javascript: submitForm(document.matrixform, 'delete_feature');">{$lng.lbl_remove_selected_features}</a>
                  </td>

                {else}

                  <td class="fcomp-note"{if $config.Appearance.buynow_button_enabled eq "Y"} colspan="2"{/if}>&nbsp;</td>

                {/if}
              </tr>
            {/if}

          {else}

            <tr>
              <td class="fcomp-corner">&nbsp;</td>
              <td class="fcomp-title" colspan="{$matrix_cnt}">{$lng.lbl_products}</td>
            </tr>

            <tr>
              <td class="fcomp-title">{$lng.lbl_product_feature}</td>

              {foreach from=$matrix item=v}
                <td class="fcomp-title-h">
                  <input type="checkbox" name="ids[]" value="{$v.productid}" /><br />
                  <a href="{if $show eq "popup"}javascript:void(0);" onclick="javascript:window.opener.location='{/if}product.php?productid={$v.productid}{$pconf_url_string}{if $show eq "popup"}';{/if}">{$v.product}</a>
                    {if $v.subscription}
                      {include file="modules/Subscriptions/subscription_info_incomparison.tpl" product=$v subscription=$v.subscription}
                    {else}
                      {if $v.taxed_price gt 0} 
                      <br />
                      {$lng.lbl_price}: {currency value=$v.taxed_price}
                    {/if}
                  {/if}
                </td>
              {/foreach}

            </tr>

            {foreach from=$matrix.0.features item=ff key=fn}
              <tr>
                <td class="fcomp-title-v">
                  <input type="checkbox" name="foptionids[]" value="{$ff.foptionid}" id="foptionid_{$ff.foptionid}" />
                  <label for="foptionid_{$ff.foptionid}"></label>{include file="modules/Feature_Comparison/option_hint.tpl" opt=$ff}
                </td>

                {foreach from=$matrix item=v}
                  {assign var="f" value=$v.features.$fn}

                  <td class="{if $not_equal_hash[$ff.foptionid]}fcomp-hl-cell{else}fcomp-cell{/if}">

                    {if $f.is_empty eq 'Y'}
                      {$lng.txt_not_available}

                    {elseif $f.option_type eq 'T'}
                      {$f.value}

                    {elseif $f.option_type eq 'N' or $f.option_type eq 'D'}
                      {$f.formated_value}

                    {elseif $f.option_type eq 'B' and $f.value eq 'Y'}
                      <img src="{$ImagesDir}/spacer.gif" class="fcomp-yes" alt="{$lng.lbl_yes|escape}" />

                    {elseif $f.option_type eq 'B' and $f.value ne 'Y'}
                      <img src="{$ImagesDir}/spacer.gif" class="fcomp-no" alt="{$lng.lbl_no|escape}" />

                    {elseif $f.option_type eq 'M' or $f.option_type eq 'S'}

                      {foreach from=$f.variants item=fv}
                        {$fv.name}<br />
                      {/foreach}

                    {/if}
                  </td>
                {/foreach}

              </tr>

            {/foreach}

            {if $matrix_features_cnt eq 0}
              <tr>
                <td class="fcomp-center-note" colspan="{inc value=$matrix_cnt}">{$lng.lbl_no_features_selected}</td>
              </tr>
            {/if}

            {if $config.Appearance.buynow_button_enabled eq "Y"}
              <tr>
                <td class="fcomp-center-note">&nbsp;</td>

                {foreach from=$matrix item=v}
                  <td class="fcomp-center-note">
                    {if $active_modules.Product_Configurator and $pconf_productid and $pconf_slot}
                      {include file="modules/Product_Configurator/pconf_add_wo_form.tpl" product=$v}
                    {else}
                      {include file="modules/Feature_Comparison/buy_now.tpl" product=$v}
                    {/if}
                  </td>
                {/foreach}
              </tr>
            {/if}

            {if $printable ne 'Y'}
              <tr>
                <td class="fcomp-delete-cell">
                  {if $matrix_features_cnt gt 0}
                    <a href="javascript:void(0);" onclick="javascript:  submitForm(document.matrixform, 'delete_feature');">{$lng.lbl_remove_selected_features}</a>
                  {else}
                    &nbsp;
                  {/if}
                </td>

                {if $matrix_cnt gt 0}
                  <td class="fcomp-delete-cell" colspan="{$matrix_cnt}">
                    <a href="javascript:void(0);" onclick="javascript:  submitForm(document.matrixform, 'delete_products');">{$lng.lbl_remove_selected_products}</a>
                  </td>
                {/if}
              </tr>
            {/if}

          {/if}

        {else}

          <tr>
            <td class="fcomp-center-note"><strong>{$lng.lbl_no_products_selected}</strong></td>
          </tr>

        {/if}

      </table>
      <div class="clearing"></div>
    </div>

  </form>

  {if ($equal_products ne '' or $is_product_popup eq 'Y') and $printable ne 'Y' and $no_add ne 'Y'}
    <div class="fcomp-select-box">
      <form action="comparison.php" method="post" name="addproductform">
        <input type="hidden" name="sort" value="{$sort}" />
        <input type="hidden" name="sort_direction" value="{$sort_direction}" />
        <input type="hidden" name="show" value="{$show}" />
        <input type="hidden" name="mode" value="add_product" />
        {if $active_modules.Product_Configurator}
        {include file="modules/Product_Configurator/pconf_hidden_form_fields.tpl"}
        {/if}

        {include file="modules/Feature_Comparison/product_selector.tpl" products=$equal_products fclassid=$comp_options.fclassid}
        {include file="customer/buttons/button.tpl" button_title=$lng.lbl_add_product style="button" type="input"}
      </form>
    </div>
  {/if}

{/capture}
{include file="customer/dialog.tpl" title=$lng.lbl_products content=$smarty.capture.dialog selected=$sort direction=$sort_direction products_sort_url="comparison.php?show=`$show`"}
