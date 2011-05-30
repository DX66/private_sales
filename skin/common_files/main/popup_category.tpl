{*
$Id: popup_category.tpl,v 1.5 2010/06/11 13:57:51 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{config_load file="$skin_config"}
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{$lng.lbl_choose_category|strip_tags}</title>
	<meta http-equiv="Content-Type" content="text/html; charset={$default_charset|default:"iso-8859-1"}" />
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
var err_choose_category = "{$lng.err_choose_category|strip_tags|wm_remove|escape:javascript|replace:"\n":" "|replace:"\r":" "}";

var wnd_opener = document;

var id_obj = wnd_opener.{$smarty.get.field_categoryid|stripslashes} ? wnd_opener.{$smarty.get.field_categoryid|stripslashes} : wnd_opener.getElementById('{$smarty.get.field_categoryid}');
var name_obj = wnd_opener.{$smarty.get.field_category|stripslashes} ? wnd_opener.{$smarty.get.field_category|stripslashes} : wnd_opener.getElementById('{$smarty.get.field_category}');

{literal}

function setCategory(categoryid, category) {

	if (id_obj) {
		id_obj.value = categoryid;
  }

	if (name_obj) {
		name_obj.value = category;
  }

  var opener = document;
  if (opener.form_to_submit) {
    opener.forms[opener.form_to_submit].submit();
  }

  $('.popup-dialog').dialog('close');
}

function setCategoryInfo() {

  category_box = document.categories_form.categoryid;
  if (category_box && category_box.value != '') {
    selected_opt = category_box.options[category_box.selectedIndex];
    setCategory(selected_opt.value, selected_opt.text);
  } else {
		alert (err_choose_category);
	}
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
  <a href="javascript:setCategory('{$bookmarks[book_idx].categoryid}','{$bookmarks[book_idx].category|replace:"'":"\'"|replace:'"':"\'\'"}')">{$bookmarks[book_idx].category|truncate:50:"...":true}</a>
	&nbsp;&nbsp;
	<a href="popup_category.php?mode=delete_bookmark&amp;categoryid={$bookmarks[book_idx].categoryid}&amp;field_category={$smarty.get.field_category}&amp;field_categoryid={$smarty.get.field_categoryid|stripslashes}"><b>[{$lng.lbl_delete}]</b></a>
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
	<td width="100%" valign="top">

<form method="get" name="categories_form" action="popup_category.php">
<input type="hidden" name="top_cat" value="{$smarty.get.top_cat}" />
<input type="hidden" name="mode" value="bookmark" />
<input type="hidden" name="field_category" value="{$smarty.get.field_category|stripslashes}" />
<input type="hidden" name="field_categoryid" value="{$smarty.get.field_categoryid|stripslashes}" />

<b>{$lng.lbl_categories}:</b><br />
{include file="main/category_selector.tpl" field="categoryid" extra=' size="20" style="width: 100%" ondblclick="javascript: setCategoryInfo();" onchange="javascript: showTitle(this.options[this.selectedIndex].text, 'left');"' categoryid=$smarty.get.categoryid}
<br /><br />
<center>
  <input type="button" value="{$lng.lbl_select|strip_tags:false|escape}" onclick="javascript: setCategoryInfo();" />
  &nbsp;&nbsp;
  <input type="submit" value="{$lng.lbl_bookmark|strip_tags:false|escape}" />
</center>
</form>

	</td>
</tr>
</table>
{/capture}

<div align="center">
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_choose_category extra="width=90%"}
</div>

</body>
</html>
