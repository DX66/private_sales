{*
$Id: popup_product.tpl,v 1.3 2010/06/08 06:17:40 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript" src="{$SkinDir}/modules/Feature_Comparison/popup_product.js"></script>
<script type="text/javascript">
//<![CDATA[
var err_choose_product = "{$lng.err_choose_product|strip_tags|wm_remove|escape:javascript}";
var err_choose_category = "{$lng.err_choose_category|strip_tags|wm_remove|escape:javascript}";
//]]>
</script>

{capture name=dialog}

  <form method="get" action="popup_fc_products.php" onsubmit="javascript: return checkCategory();" name="cat_form">
    <input type="hidden" name="top_cat" value="{$smarty.get.top_cat}" />
    <input type="hidden" name="id" value="{$smarty.get.id}" />
    <input type="hidden" name="no_ids" value="{$smarty.get.no_ids}" />
    <input type="hidden" name="fclassid" value="{$smarty.get.fclassid}" />

    <div class="fcomp-popup-categories-list">

      <div class="fcomp-popup-column-title">{$lng.lbl_categories}:</div>

      {include file="main/category_selector.tpl" field="cat" size="20" categoryid=$smarty.get.cat extra=' ondblclick="javascript: $(this.form).submit();"'}

      <div class="center">
        <div class="halign-center button-row">{include file="customer/buttons/button.tpl" button_title=$lng.lbl_show_products type="input"}</div>
      </div>

    </div>

    <div class="fcomp-popup-products-list">

      {if $products eq ""}

        {$lng.txt_no_products_in_cat}

      {else}

        <div class="fcomp-popup-column-title">{$lng.lbl_products}:</div>

        <select name="productid" size="20" ondblclick="javascript: setProductInfo();">
          {foreach from=$products item=p}
            <option value="{$p.productid}">{$p.product|amp}</option>
          {/foreach}
        </select>

        <div class="center">
          <div class="halign-center button-row">{include file="customer/buttons/button.tpl" button_title=$lng.lbl_select href="javascript: setProductInfo();"}</div>
        </div>

      {/if}

    </div>
    <div class="clearing"></div>

  </form>

{/capture}
{include file="customer/dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_choose_product additional_class="fcomp-popup-dialog"}
