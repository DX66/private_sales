{*
$Id: categories.tpl,v 1.2 2010/06/08 10:17:47 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_categories_management}

<br />

{$lng.txt_categories_management_top_text}

<br /><br />

{capture name=dialog}
<a name="Categories"></a>
{include file="admin/main/location.tpl"}

{if $cat}

<table width="100%">

<tr>
<td align="center" class="TopLabel">{$lng.lbl_current_category}: "{$current_category.category|default:$lng.lbl_root_level}"
{if $current_category.avail eq "N"}
<div class="ErrorMessage">{$lng.txt_category_disabled}</div>
{/if}
</td>
</tr>

<tr>
<td align="right" class="SubmitBox">
<input type="button" value="{$lng.lbl_modify_category|strip_tags:false|escape}" onclick="javascript: self.location='category_modify.php?cat={$cat}'" />
<input type="button" value="{$lng.lbl_category_products|strip_tags:false|escape}" onclick="javascript: self.location='category_products.php?cat={$cat}'" />
<input type="button" value="{$lng.lbl_delete_category|strip_tags:false|escape}" onclick="javascript: self.location='process_category.php?cat={$cat}&amp;mode=delete'" />
</td>
</tr>

</table>

<br /><br />

{include file="main/subheader.tpl" title=$lng.txt_list_of_subcategories}

{/if}

<br />

<form action="process_category.php" method="post" name="processcategoryform">
<input type="hidden" name="cat_org" value="{$smarty.get.cat|escape:"html"}" />

<table cellpadding="2" cellspacing="1" width="100%">

<tr class="TableHead">
  <td>{$lng.lbl_pos}</td>
  <td colspan="2">{$lng.lbl_category_name}</td>
  <td align="center">{$lng.lbl_products}*</td>
  <td align="center">{$lng.lbl_subcategories}</td>
  <td align="center">{$lng.lbl_enabled}</td>
</tr>

{assign var="cat_selected" value=0}
{foreach from=$categories item=c key=catid}

<tr{cycle values=', class="TableSubHead"'}>
  <td width="1%"><input type="text" size="3" name="posted_data[{$catid}][order_by]" maxlength="3" value="{$c.order_by}" /></td>
  <td width="1%"><input type="radio" name="cat" value="{$catid}"{if $cat_selected eq 0} checked="checked"{/if} /></td>
  <td><a href="categories.php?cat={$catid}"><font class="{if $c.avail eq "N"}ItemsListDisabled{else}ItemsList{/if}">{$c.category}</font></a></td>
  <td align="center">
{if $c.product_count eq 0 and $c.product_count_global eq 0}
{$lng.txt_not_available}
{else}
<a href="category_products.php?cat={$catid}">{$c.product_count|default:$lng.txt_not_available}</a> ({$c.product_count_global})
{/if}
  </td>
  <td align="center"><a href="categories.php?cat={$catid}">{$c.subcategory_count|default:$lng.txt_not_available}</a></td>
  <td align="center">
  <select name="posted_data[{$catid}][avail]">
    <option value="Y"{if $c.avail eq "Y"} selected="selected"{/if}>{$lng.lbl_yes}</option>
    <option value="N"{if $c.avail eq "N"} selected="selected"{/if}>{$lng.lbl_no}</option>
  </select>
  </td>
</tr>

{assign var="cat_selected" value=1}

{foreachelse}

<tr>
  <td colspan="6" align="center">{$lng.txt_no_categories}</td>
</tr>

{/foreach}

{if $categories}
<tr>
  <td colspan="6">
<b>*{$lng.lbl_note}:</b> {$lng.txt_categories_management_note}
  </td>
</tr>
<tr>
  <td colspan="6" class="SubmitBox">
<input type="button" value="{$lng.lbl_update|strip_tags:false|escape}" onclick="javascript: submitForm(this, 'apply');" />
<br /><br />
<input type="button" value="{$lng.lbl_modify_selected|strip_tags:false|escape}" onclick="javascript: submitForm(this, 'update');" />
<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: submitForm(this, 'delete');" />
  </td>
</tr>
{/if}

<tr>
  <td colspan="6" class="SubmitBox"><input type="button" value="{$lng.lbl_add_new_|strip_tags:false|escape}" onclick="self.location='category_modify.php?mode=add&amp;cat={$cat}'" /></td>
</tr>

</table>

<input type="hidden" name="mode" value="apply" />
</form>

<br />

{/capture}
{include file="dialog.tpl" title=$lng.lbl_categories content=$smarty.capture.dialog extra='width="100%"'}

<br /><br />

{include file="admin/main/featured_products.tpl"}

