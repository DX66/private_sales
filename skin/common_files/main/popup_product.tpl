{*
$Id: popup_product.tpl,v 1.6 2010/07/02 11:52:50 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{config_load file="$skin_config"}
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{$lng.lbl_select_product|strip_tags}</title>
	<meta http-equiv="Content-Type" content="text/html; charset={$default_charset|default:"iso-8859-1"}" />
  <meta http-equiv="X-UA-Compatible" content="IE=8" />
	<link rel="stylesheet" type="text/css" href="{$SkinDir}/css/skin1_admin.css" />

</head>
<body{$reading_direction_tag}>
<br />
{capture name=dialog}
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td colspan="2">
<script type="text/javascript">
//<![CDATA[
var err_choose_product = "{$lng.err_choose_product|strip_tags|wm_remove|escape:javascript|replace:"\n":" "|replace:"\r":" "}";
var err_choose_category = "{$lng.err_choose_category|strip_tags|wm_remove|escape:javascript|replace:"\n":" "|replace:"\r":" "}";

var id_obj;
var name_obj;

var id_obj = {if $smarty.get.field_productid ne ""}document.{$smarty.get.field_productid} 
    ? document.{$smarty.get.field_productid} 
    : document.getElementById('{$smarty.get.field_productid}'){else}null{/if};

  name_obj = {if $smarty.get.field_product ne ""}{$opener_prefix}document.{$smarty.get.field_product} 
    ? document.{$smarty.get.field_product} 
    : document.getElementById('{$smarty.get.field_product}'){else}null{/if};

function setProduct (productid, product) {ldelim}

  if (id_obj)
    id_obj.value = productid;
  
  if (name_obj)
    name_obj.value=product;

  var opener = document;
  if (opener.form_to_submit)
    opener.forms[opener.form_to_submit].submit();

  $('.popup-dialog').dialog('close');

{rdelim}

{literal}
function setProductInfo () {
  if (document.products_form.productid.value != "") {
    setProduct (document.products_form.productid.options[document.products_form.productid.selectedIndex].value, document.products_form.productid.options[document.products_form.productid.selectedIndex].text);
  } else {
    alert (err_choose_product);
  }
}

function checkCategory () {
  if (document.cat_form.cat.options.selectedIndex == -1) {
    alert (err_choose_category);
    return false;
  }

  return true;
}

{/literal}
//]]>
</script>

<table cellpadding="2" cellspacing="0">
<tr>
  <td valign="top"><font class="TopLabel">{$lng.lbl_bookmarks}:</font></td>
  <td valign="top">
{if $bookmarks ne ""}
<ul>
{section name=book_idx loop=$bookmarks}
  <li>
  <a href="javascript:setProduct('{$bookmarks[book_idx].productid}','{$bookmarks[book_idx].product|replace:"'":"\'"|replace:'"':"\'\'"}')">{$bookmarks[book_idx].product|truncate:30:"...":true}</a>
  &nbsp;&nbsp;
  <a href="popup_product.php?mode=delete_bookmark&amp;cat={$smarty.get.cat|escape:"html"}&amp;productid={$bookmarks[book_idx].productid}&amp;field_product={$smarty.get.field_product|stripslashes}&amp;field_productid={$smarty.get.field_productid|stripslashes}&amp;only_regular={$smarty.get.only_regular|stripslashes}"><b>[{$lng.lbl_delete}]</b></a>
  </li>
{/section}
</ul>
{else}
&nbsp;
{/if}
  </td>
</tr>
</table>
<hr />
  </td>
</tr>
<tr>
  <td width="50%" valign="top">

<form method="get" onsubmit="return checkCategory();" name="cat_form" action="popup_product.php">
<input type="hidden" name="top_cat" value="{$smarty.get.top_cat}" />
<input type="hidden" name="field_product" value="{$smarty.get.field_product|stripslashes}" />
<input type="hidden" name="field_productid" value="{$smarty.get.field_productid|stripslashes}" />
<input type="hidden" name="only_regular" value="{$smarty.get.only_regular|stripslashes}" />

<b>{$lng.lbl_categories}:</b><br />
{include file="main/category_selector.tpl" field="cat" extra=' size="20" style="width: 100%" ondblclick="javascript: $(this.form).submit();"' categoryid=$smarty.get.cat}<br /><br />
<center><input type="submit" value="{$lng.lbl_show_products|strip_tags:false|escape}" /></center>
</form>

  </td>
  <td width="50%" valign="top">
{if $products eq ""}
{$lng.txt_no_products_in_cat}
{else}

<form method="get" name="products_form" action="popup_product.php">
<input type="hidden" name="cat" value="{$smarty.get.cat|escape:"html"}" />
<input type="hidden" name="mode" value="bookmark" />
<input type="hidden" name="field_productid" value="{$smarty.get.field_productid|escape:"html"}" />
<input type="hidden" name="field_product" value="{$smarty.get.field_product|escape:"html"}" />
<input type="hidden" name="only_regular" value="{$smarty.get.only_regular|escape:"html"}" />

<b>{$lng.lbl_products}:</b><br />
<select name="productid" size="20" style="width: 100%" ondblclick="javascript: setProductInfo();" onchange="javascript: showTitle(this.options[this.selectedIndex].text, 'left');">
{section name=prod_idx loop=$products}
  <option value="{$products[prod_idx].productid}">{$products[prod_idx].product}</option>
{/section}
</select><br /><br />
<center>
  <input type="button" value="{$lng.lbl_select|strip_tags:false|escape}" onclick="javascript: setProductInfo();" />
  &nbsp;&nbsp;
  <input type="submit" value="{$lng.lbl_bookmark|strip_tags:false|escape}" />
</center>
</form>
{/if}

  </td>
</tr>
</table>
{/capture}

<div align="center">
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_choose_product extra="width=90%"}
</div>

</body>
</html>
