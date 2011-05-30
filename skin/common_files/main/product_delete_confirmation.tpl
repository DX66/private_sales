{*
$Id: product_delete_confirmation.tpl,v 1.2.2.1 2010/12/15 09:44:39 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_delete_products}

<br />

{capture name=dialog}

{if $section eq ""}
<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_return_to_search_results href="search.php?mode=search&amp;page=`$navpage`"}</div>
<br />
{/if}

<form action="process_product.php" method="post" name="processform">

<input type="hidden" name="section" value="{$section}" />
<input type="hidden" name="cat" value="{$cat}" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="confirmed" value="Y" />
<input type="hidden" name="navpage" value="{$navpage}" />

<span class="Text">{$lng.lbl_product_delete_confirmation_header}:</span>

<br /><br />

<ul>
{section name=prod loop=$products}
<li><span class="ProductPriceSmall">{$products[prod].productcode} {$products[prod].product} - {currency value=$products[prod].price}</span>
<dl>
<dd>{$products[prod].category}</dd>
<dd>{$lng.lbl_provider}: {$products[prod].provider}</dd>
</dl>
</li>
{/section}
</ul>

<br />

{$lng.txt_operation_not_reverted_warning}

{if $search_return ne ''}
{assign var="url_to" value=$search_return} 
{elseif $section eq "category_products"}
{assign var="url_to" value="category_products.php?cat=`$cat`&amp;page=`$navpage`"}
{else}
{assign var="url_to" value="search.php?mode=search&amp;page=`$navpage`"}
{/if}

<br /><br />
<table cellspacing="0" cellpadding="2">
<tr>
  <td>{$lng.txt_are_you_sure_to_proceed}</td>
  <td>&nbsp;&nbsp;&nbsp;</td>
  <td>{include file="buttons/yes.tpl" href="javascript:document.processform.mode.value='delete';document.processform.submit()" js_to_href="Y"}</td>
  <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
  <td>{include file="buttons/no.tpl" href=$url_to}</td>
</tr>
</table>

</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_confirmation content=$smarty.capture.dialog extra='width="100%"'}
