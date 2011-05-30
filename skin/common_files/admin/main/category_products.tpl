{*
$Id: category_products.tpl,v 1.3 2010/06/08 06:17:38 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_category_products}

{$lng.txt_category_products_top_text}

<br /><br />

<script type="text/javascript">
//<![CDATA[
var txt_delete_products_warning = "{$lng.txt_delete_products_warning|wm_remove|escape:javascript}";
//]]>
</script>

{capture name=dialog}

{include file="admin/main/location.tpl"}

<table width="100%">

<tr>
<td align="center" class="TopLabel">{$lng.lbl_current_category}: "{$current_category.category|default:$lng.lbl_root_level}"
{if $current_category.avail eq "N"}
<div class="ErrorMessage">{$lng.txt_category_disabled}</div>
{/if}
</td>
</tr>

<tr>
<td align="right"><br />
<input type="button" value="{$lng.lbl_modify_category|strip_tags:false|escape}" onclick="javascript: self.location='category_modify.php?cat={$cat}'" />
&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="{$lng.lbl_delete_category|strip_tags:false|escape}" onclick="javascript: self.location='process_category.php?cat={$cat}&amp;mode=delete'" />
</td>
</tr>

</table>

<br /><br />

{include file="main/subheader.tpl" title=$lng.lbl_category_products}

<!-- SEARCH RESULTS SUMMARY -->

{if $mode eq "search"}
{if $total_items gt "1"}
{$lng.txt_N_results_found|substitute:"items":$total_items}<br />
{$lng.txt_displaying_X_Y_results|substitute:"first_item":$first_item:"last_item":$last_item}
{elseif $total_items eq "0"}
<br />
<div align="center">{$lng.txt_no_products_in_cat}</div>
{/if}
{/if}

{if $products}

<!-- SEARCH RESULTS START -->

<br />

{if $total_pages gt 2}
{assign var="navpage" value=$navigation_page}
{/if}

<form action="process_product.php" method="post" name="processproductform">
<input type="hidden" name="section" value="category_products" />
<input type="hidden" name="mode" value="update" />
<input type="hidden" name="navpage" value="{$navpage}" />
<input type="hidden" name="cat" value="{$cat}" />

<table cellpadding="0" cellspacing="0" width="100%">

<tr>
<td>

{include file="main/navigation.tpl"}

{include file="main/products.tpl" products=$products}

<br />

{include file="main/navigation.tpl"}

<br />

<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('productids\[[0-9]+\]', 'gi'))) if (confirm(txt_delete_products_warning)) submitForm(this, 'delete');" />
&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="{$lng.lbl_modify_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('productids\[[0-9]+\]', 'gi'))) {ldelim} document.processproductform.action = 'product_modify.php'; submitForm(this, 'list'); {rdelim}" />
<br /><br /><br />

{$lng.txt_operation_for_first_selected_only}

<br /><br />

<input type="button" value="{$lng.lbl_preview_product|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('productids\[[0-9]+\]', 'gi'))) submitForm(this, 'details');" />
&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="{$lng.lbl_clone_product|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('productids\[[0-9]+\]', 'gi'))) submitForm(this, 'clone');" />
&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="{$lng.lbl_generate_html_links|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('productids\[[0-9]+\]', 'gi'))) submitForm(this, 'links');" />

</td>
</tr>

</table>
</form>

{/if}

<br />

<input type="button" value="{$lng.lbl_add_new_|strip_tags:false|escape}" onclick="javascript: self.location = 'product_modify.php?categoryid={$cat}';" />

<br />

{/capture}
{include file="dialog.tpl" title=$lng.lbl_products content=$smarty.capture.dialog extra='width="100%"'}

