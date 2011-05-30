{*
$Id: product_selector.tpl,v 1.1 2010/05/21 08:32:21 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $id eq ''}
  {assign var="id" value="productid"}
{/if}

{if $is_product_popup eq 'Y'}

  <script type="text/javascript" src="{$SkinDir}/js/popup_open.js"></script>
  <input type="hidden" id="{$id}"{if $only_id eq ''} name="productid"{/if} />
  <div class="fcomp-select-product">
    <input type="text" size="25" id="{$id}_product" readonly="readonly" />
    {assign var="lbl_select_product" value=$lng.lbl_select_product|wm_remove|escape:javascript}
    {include file="customer/buttons/button.tpl" button_title=$lng.lbl_browse_ href="javascript: window.open('popup_fc_products.php?id=`$id`&amp;no_ids=`$no_ids`&amp;fclassid=`$fclassid`', 'selectproduct','width=600,height=550,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no');"}
  </div>

{else}
  <select{if $only_id eq ''} name="productid"{/if} id="{$id}" class="fcomp-select-product">
    {foreach from=$products item=v}
      <option value="{$v.productid}">{$v.product|amp}</option>
    {/foreach}
  </select>

{/if}
