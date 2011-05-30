{*
$Id: modify_event_categories.tpl,v 1.1 2010/05/21 08:32:50 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="main/visiblebox_link.tpl" mark="categories" title=$lng.lbl_survey_ordered_products_from_categories visible=$survey.event_elements.D}
<div id="boxcategories" style="padding-left: 20px;{if not $survey.event_elements.D} display: none;{/if}">

<table cellspacing="1" cellpadding="2">
<tr class="TableHead">
  <th width="15">&nbsp;</th>
  <th>{$lng.lbl_category}</th>
</tr>
{if $survey.event_elements.D}
{foreach from=$survey.event_elements.D key=code item=c}
<tr{cycle values=', class="TableSubHead"' name="category"}>
  <td><input type="checkbox" name="check[D][]" value="{$code}" /></td>
  <td>{$c.category_path}</td>
</tr>
{/foreach}
<tr>
  <td colspan="2"><input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: this.form.delete_param.value = 'D'; if (checkMarks(this.form, new RegExp('check\\[D\\]\\[\\]', 'gi'))) submitForm(this, 'delete_event');" /></td>
</tr>
<tr>
  <td colspan="2">{include file="main/subheader.tpl" title=$lng.lbl_survey_add_category class="grey"}</td>
</tr>
{/if}
<tr>
  <td id="add_category_box_1">&nbsp;</td>
  <td id="add_category_box_2">{include file="main/category_selector.tpl" field="new_element[D][0]" display_empty="E"}</td>
  <td>{include file="buttons/multirow_add.tpl" mark="add_category" is_lined=true}</td>
</tr>
</table>

</div>
