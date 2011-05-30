{*
$Id: products_lng.tpl,v 1.1 2010/05/21 08:32:18 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=dialog}
<form action="product_modify.php" method="post" name="modifylng">
<input type="hidden" name="section" value="lng" />
<input type="hidden" name="mode" value="update_lng" />
<input type="hidden" name="productid" value="{$product.productid}" />
<input type="hidden" name="geid" value="{$geid}" />

<table width="100%" {if $geid ne ''}cellspacing="0" cellpadding="4"{else}cellspacing="1" cellpadding="2"{/if} class="product-details-table">
<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="3" align="right">
    {include file="main/language_selector.tpl" script="`$navigation_script`&"}
  </td>
</tr>

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[languages][product]" /></td>{/if}
  <td width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_product_title}:</td>
  <td>&nbsp;</td>
  <td class="ProductDetails" width="80%"><input type="text" size="45" name="product_lng[product]" value="{$product_languages.product|escape:"html"}" class="InputWidth" /></td>
</tr>
<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[languages][keywords]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_keywords}:</td>
  <td>&nbsp;</td>
  <td class="ProductDetails"><input type="text" size="45" name="product_lng[keywords]" value="{$product_languages.keywords|escape:"html"}" class="InputWidth" /></td>
</tr>
<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[languages][descr]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_short_description}:</td>
  <td>&nbsp;</td>
  <td class="ProductDetails">
{include file="main/textarea.tpl" name="product_lng[descr]" cols=45 rows=8 class="InputWidth" data=$product_languages.descr width="80%" btn_rows=4}
  </td>
</tr>
<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[languages][fulldescr]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_det_description}:</td>
  <td>&nbsp;</td>
  <td class="ProductDetails">
{include file="main/textarea.tpl" name="product_lng[fulldescr]" cols=45 rows=12 class="InputWidth" data=$product_languages.fulldescr width="80%" btn_rows=4}
  </td>
</tr>
</table>

<br />
<hr />
<br />

<input type="submit" value="{$lng.lbl_apply|strip_tags:false|escape}" />&nbsp;&nbsp;&nbsp;

{if $geid}
<br /><br />
<table>
<tr>
  <td><input type="checkbox" id="del_lang_all" name="del_lang_all" value="Y" /></td>
  <td><label for="del_lang_all">{$lng.lbl_delete_int_description_for_all_products}</label></td>
</tr>
</table>
{/if}
<input type="button" value="{$lng.lbl_delete|strip_tags:false|escape}" onclick="javascript: submitForm(this, 'del_lang');" />

</form>
{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.txt_international_descriptions extra='width="100%"'}
