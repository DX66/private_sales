{*
$Id: category_lng.tpl,v 1.1 2010/05/21 08:31:59 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$title}
<br />

<br />

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

</table>

<form action="category_modify.php" method="post">

<input type="hidden" name="section" value="lng" />
<input type="hidden" name="cat" value="{$cat}" />
<input type="hidden" name="mode" value="update_lng" />

{include file="main/language_selector.tpl" script="category_modify.php?section=lng&cat=`$cat`&"}

<table width="100%">

<tr>
  <td>{$lng.lbl_category_name}</td>
  <td><input type="text" size="45" name="category_lng[category]" value="{$category_lng.category|escape:"html"}" /></td>
</tr>
<tr>
  <td>{$lng.lbl_description}</td>
  <td>{include file="main/textarea.tpl" name="category_lng[description]" cols=45 rows=6 data=$category_lng.description width="100%" style="width: 100%;"}</td>
</tr>

<tr>
  <td>&nbsp;</td>
  <td class="SubmitBox">
<input type="submit" value="{$lng.lbl_submit|strip_tags:false|escape}" />
{if $category_lng.code ne ''}
<input type="button" value="{$lng.lbl_delete|strip_tags:false|escape}" onclick="javascript: self.location='category_modify.php?section=lng&amp;cat={$cat}&amp;mode=del_lang';" />
{/if}
  </td>
</tr>

</table>

</form>

{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.txt_international_descriptions extra='width="100%"'}
