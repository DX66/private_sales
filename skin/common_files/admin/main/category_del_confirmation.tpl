{*
$Id: category_del_confirmation.tpl,v 1.1 2010/05/21 08:31:59 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_delete_category}

{$lng.txt_delete_category_top_text}

<br /><br />

{capture name=dialog}
<span class="Text">
{$lng.txt_subcats_and_products_will_be_removed}
</span>
<br /><br />
<ul>
{if $subcats}
{section name=subcat loop=$subcats}
<li><span class="ProductPriceSmall">{$subcats[subcat].category}</span>{if $subcats[subcat].products_count gt 0} - {$lng.lbl_N_products|substitute:"products":$subcats[subcat].products_count}{/if}
{if $subcats[subcat].products}
<dl>
{section name=product loop=$subcats[subcat].products}
<dd><a href="product.php?productid={$subcats[subcat].products[product].productid}" target="_blank">#{$subcats[subcat].products[product].productid}. {$subcats[subcat].products[product].productcode} {$subcats[subcat].products[product].product}</a></dd>
{/section}
</dl>
{/if}
</li>
{/section}
</ul>
{/if}

{$lng.txt_operation_not_reverted_warning}

<br /><br />

<form action="process_category.php" method="post" name="processform">

<input type="hidden" name="mode" value="delete" />
<input type="hidden" name="confirmed" value="Y" />
<input type="hidden" name="cat" value="{$smarty.get.cat|escape:"html"}" />

<table cellspacing="1" cellpadding="2">
<tr>
  <td>{$lng.txt_are_you_sure_to_proceed}&nbsp;&nbsp;&nbsp;</td>
  <td>{include file="buttons/button.tpl" button_title=$lng.lbl_yes href="javascript:document.processform.submit()" js_to_href="Y"}</td>
  <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
  <td>{include file="buttons/button.tpl" button_title=$lng.lbl_no href="javascript:history.go(-1)" js_to_href="Y"}</td>
</tr>
</table>

</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_confirmation content=$smarty.capture.dialog extra='width="100%"'}
