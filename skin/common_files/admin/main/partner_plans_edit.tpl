{*
$Id: partner_plans_edit.tpl,v 1.3 2010/06/08 06:17:38 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript" src="{$SkinDir}/js/popup_product.js"></script>
{include file="main/multirow.tpl"}

<script type="text/javascript">
//<![CDATA[
{literal}
function xaffLevelNIterator() {
  levels = document.getElementById('levels');
  if (!levels)
    return false;

  var td;
  var n = 1;
  for (var i = 0; i < levels.rows.length; i++) {
    td = levels.rows[i].cells[0];
    if (!td)
      continue;

    if (td.innerHTML.search(/^\s*\d+\s*$/) != -1)
      td.innerHTML = n++;
  }

  return true;
}
{/literal}
//]]>
</script>

{include file="page_title.tpl" title=$lng.lbl_affiliate_plan_management}

{$lng.txt_affiliate_plan_management_note}<br />
<br />

{capture name=dialog}

  <table width="100%" cellpadding="0" cellspacing="0">
    <tr>
      <td valign="top"><h2>{$lng.lbl_affiliate_plan}: {$partner_plan_info.plan_title|default:$lng.lbl_new}</h2></td>
      <td align="right" valign="top">{include file="buttons/button.tpl" button_title=$lng.lbl_list_all_affiliate_plans href="partner_plans.php"}</td>
    </tr>
  </table>

  <form action="partner_plans.php" name="commissionsform" method="post">
    <input type="hidden" name="form" value="products" />
    <input type="hidden" name="mode" value="modify" />
    <input type="hidden" name="planid" value="{$smarty.get.planid|escape:"html"}" />

    <table cellpadding="2" cellspacing="2" width="100%">

      <tr>
        <td colspan="5" class="TopLabel">
          <a name="products"></a>
          {include file="main/subheader.tpl" title=$lng.lbl_commission_rates_on_products}
        </td>
      </tr>

      <tr class="TableHead">
        <td width="1%" nowrap="nowrap">&nbsp;</td>
        <td width="1%" nowrap="nowrap">#</td>
        <td width="90%" nowrap="nowrap">{$lng.lbl_product}</td>
        <td width="9%" nowrap="nowrap" colspan="2">{$lng.lbl_commission_rate}</td>
      </tr>

      {assign var="count" value=0}
      {capture name=products_commissions}
        {section name=comm loop=$partner_plans_commissions}
          {if $partner_plans_commissions[comm].item_type eq "P"}
            {inc assign="count" value=$count}
            <tr{cycle name="product" values=", class='TableSubHead'"}>
              <td width="15"><input type="checkbox" name="productid[]" value="{$partner_plans_commissions[comm].item_id}" /></td>
              <td align="left">{$partner_plans_commissions[comm].item_id}</td>
              <td class="ItemsList"><a href="product_modify.php?productid={$partner_plans_commissions[comm].item_id}">{$partner_plans_commissions[comm].product}</a></td>
              <td>
                <input type="text" name="products[{$partner_plans_commissions[comm].item_id}][commission]" size="10" maxlength="13" value="{$partner_plans_commissions[comm].commission|formatprice}" />
              </td>
              <td>
                <select name="products[{$partner_plans_commissions[comm].item_id}][commission_type]">
                  <option value="%"{if $partner_plans_commissions[comm].commission_type eq "%"} selected="selected"{/if}>%</option>
                  <option value="$"{if $partner_plans_commissions[comm].commission_type eq "$"} selected="selected"{/if}>{$config.General.currency_symbol}</option>
                </select>
              </td>
            </tr>
          {/if}
        {/section}
        {if $count gt 0}
          <tr>
            <td colspan="5"><input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('productid', 'gi'))) submitForm(this, 'delete_rate');" /></td>
          </tr>
        {/if}

      {/capture}
      {if $smarty.capture.products_commissions}
        {$smarty.capture.products_commissions}
      {else}
        <tr>
          <td colspan="5" align="center">{$lng.txt_no_products_commission}</td>
        </tr>
      {/if}

      <tr>
        <td colspan="5">&nbsp;</td>
      </tr>

      <tr>
        <td colspan="3">
          <input type="hidden" name="newproduct" />
          {$lng.lbl_product} #:
          <input type="text" name="product_ids" size="30" readonly="readonly" />
          <input type="button" value="{$lng.lbl_find_products_|strip_tags:false|escape}"  onclick="javascript: popup_product('commissionsform.product_ids', 'commissionsform.newproduct');" />
        </td>
        <td><input type="text" name="new_product_commission" size="10" maxlength="13" value="{$zero}" /></td>
        <td>
          <select name="new_product_commission_type">
            <option value="%" selected="selected">%</option>
            <option value="$">{$config.General.currency_symbol}</option>
          </select>
        </td>
      </tr>

      <tr>
        <td colspan="5" class="SubmitBox"><input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" /></td>
      </tr>

      <tr>
        <td colspan="5"><br /><br /></td>
      </tr>

      <tr>
        <td colspan="5" class="TopLabel">
          <a name="categories"></a>
          {include file="main/subheader.tpl" title=$lng.lbl_commission_rates_on_categories}
        </td>
      </tr>

      <tr class="TableHead">
        <td width="1%" nowrap="nowrap">&nbsp;</td>
        <td nowrap="nowrap">#</td>
        <td nowrap="nowrap">{$lng.lbl_category}</td>
        <td nowrap="nowrap" colspan="2">{$lng.lbl_commission_rate}</td>
      </tr>

      {assign var="count" value=0}

      {capture name=products_commissions}
        {section name=comm loop=$partner_plans_commissions}
          {if $partner_plans_commissions[comm].item_type eq "C"}
            {inc assign="count" value=$count}
            <tr{cycle name="category" values=", class='TableSubHead'"}>
              <td width="1%"><input type="checkbox" name="categoryid[]" value="{$partner_plans_commissions[comm].item_id}" /></td>
              <td>{$partner_plans_commissions[comm].item_id}</td>
              <td class="ItemsList"><a href="category_modify.php?cat={$partner_plans_commissions[comm].item_id}">{$partner_plans_commissions[comm].category}</a></td>
              <td>
                <input type="text" name="categories[{$partner_plans_commissions[comm].item_id}][commission]" size="10" maxlength="13" value="{$partner_plans_commissions[comm].commission|formatprice}" />
              </td>
              <td>
                <select name="categories[{$partner_plans_commissions[comm].item_id}][commission_type]">
                  <option value="%"{if $partner_plans_commissions[comm].commission_type eq "%"} selected="selected"{/if}>%</option>
                  <option value="$"{if $partner_plans_commissions[comm].commission_type eq "$"} selected="selected"{/if}>{$config.General.currency_symbol}</option>
                </select>
              </td>
            </tr>
          {/if}
        {/section}
        {if $count gt 0}
          <tr> 
            <td colspan="5"><input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('categoryid', 'gi'))) {ldelim} document.commissionsform.form.value='categories'; submitForm(this, 'delete_rate');{rdelim}" /></td>
          </tr>
        {/if}
      {/capture}
      {if $smarty.capture.products_commissions}
        {$smarty.capture.products_commissions}
      {else}
        <tr>
          <td colspan="5" align="center">{$lng.txt_no_categories_commissions}</td>
        </tr>
      {/if}

      <tr>
        <td colspan="5">&nbsp;</td>
      </tr>

      <tr>
        <td colspan="3">{include file="main/category_selector.tpl" field="new_categoryid"}</td>
        <td><input type="text" name="new_category_commission" size="10" maxlength="13" value="{$zero}" /></td>
        <td>
          <select name="new_category_commission_type">
            <option value="%" selected="selected">%</option>
            <option value="$">{$config.General.currency_symbol}</option>
          </select>
        </td>
      </tr>

      <tr>
        <td colspan="5" class="SubmitBox"><input type="button" value="{$lng.lbl_update|strip_tags:false|escape}" onclick="javascript: document.commissionsform.form.value='categories'; document.commissionsform.submit();" /></td>
      </tr>

      <tr>
        <td colspan="5"><br /><br /></td>
      </tr>

      <tr>
        <td colspan="5" class="TopLabel">
          <a name="general"></a>
          {include file="main/subheader.tpl" title=$lng.lbl_aff_plans_general_settings}
        </td>
      </tr>

      <tr>
        <td colspan="3" align="right">{$lng.lbl_basic_commission_rate}:</td>
        <td><input type="text" name="basic_commission" size="10" maxlength="13" value="{$general_commission.commission|formatprice|default:$zero}" /></td>
        <td>
          <select name="basic_commission_type">
            <option value="%"{if $general_commission.commission_type eq "%"} selected="selected"{/if}>%</option>
            <option value="$"{if $general_commission.commission_type eq "$"} selected="selected"{/if}>{$config.General.currency_symbol}</option>
          </select>
        </td>
      </tr>

      <tr>
        <td colspan="3" align="right">{$lng.lbl_minimum_commission_payment}:</td>
        <td><input type="text" name="min_paid" size="10" maxlength="13" value="{$partner_plan_info.min_paid|formatprice|default:$zero}" /></td>
      </tr>

      <tr>
        <td colspan="5" class="TopLabel">
          <a name="mlm"></a>
          {include file="main/subheader.tpl" title=$lng.lbl_multi_tier_affiliates}
        </td>
      </tr>

      <tr>
        <td colspan="5">

          <table cellspacing="1" cellpadding="3" id="levels">

            <tr class="TableHead">
              <th>{$lng.lbl_level}</th>
              <th>{$lng.lbl_commission}</th>
            </tr>

            {if $partner_plan_info.mlm}
              {foreach from=$partner_plan_info.mlm item=l}

                <tr>
                  <td>{$l.level}</td>
                  <td><input type="text" size="5" name="levels[{$l.level}]" value="{$l.commission|formatprice}" />%</td>
                  <td><a href="partner_plans.php?mode=delete_level&amp;planid={$partner_plan_info.plan_id}&amp;level={$l.level}"><img src="{$ImagesDir}/delete_cross.gif" alt="{$lng.lbl_delete|escape}" /></a></td>
                </tr>

              {/foreach}
            {/if}

            <tr>
              <td id="level_box_1">{inc value=$partner_plan_info.mlm_count}</td>
              <td id="level_box_2"><input type="text" size="5" name="new_level[0]" />%</td>
              <td>{include file="buttons/multirow_add.tpl" mark="level" handler=xaffLevelNIterator}</td>
            </tr>

          </table>

        </td>
      </tr>

      <tr>
        <td colspan="5" class="SubmitBox"><input type="button" value="{$lng.lbl_update|strip_tags:false|escape}" onclick="javascript: document.commissionsform.form.value='general'; document.commissionsform.submit();" /></td>
      </tr>

    </table>
  </form>

{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_affiliate_plan_management extra='width="100%"'}
