{*
$Id: page_edit.tpl,v 1.4 2010/07/30 10:16:35 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_static_pages}

<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
var txt_fill_page_name_field = "{$lng.txt_fill_page_name_field|strip_tags|escape:javascript}";
var txt_fill_page_content_field = "{$lng.txt_fill_page_content_field|strip_tags|escape:javascript}";
var txt_fill_page_file_field = "{$lng.txt_fill_page_file_field|strip_tags|escape:javascript}";
var is_empty_filename = {if $page_data.filename eq ""}true{else}false{/if};
{literal}
function formSubmit() {
  if (document.pagesform.pagetitle.value == "") {
    document.pagesform.pagetitle.focus();
    alert(txt_fill_page_name_field);
    return false;

  } else if (document.pagesform.pagecontent.value == "") {
    document.pagesform.pagecontent.focus();
    alert(txt_fill_page_content_field);
    return false;

  } else if (is_empty_filename && document.pagesform.filename && document.pagesform.filename.value == "") {
    document.pagesform.filename.focus();
    alert(txt_fill_page_file_field);
    return false;
  }
  {/literal}
  {if $config.SEO.clean_urls_enabled eq "Y"}
  if (!checkCleanUrl(document.pagesform.clean_url))
    return false;
  {/if}
  {literal}
  return true;
}
{/literal}
//]]>
</script>

{include file="check_clean_url.tpl"}

{$lng.txt_edit_static_page_top_text}

<br /><br />

{capture name=dialog}

<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_pages_list href="pages.php"}</div>

<form action="pages.php" method="post" name="pagesform" onsubmit="javascript: return formSubmit();">
<input type="hidden" name="mode" value="modified" />
<input type="hidden" name="pageid" value="{$pageid|escape:"html"}" />
<input type="hidden" name="level" value="{$level}" />
{if $level eq "R"}
<input type="hidden" name="active" value="Y" />
<input type="hidden" name="orderby" value="0" />
{else}
<input type="hidden" name="show_in_menu" value="" />
{/if}

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
  <td height="10" class="FormButton" nowrap="nowrap" valign="top">{$lng.lbl_level}:</td>
  <td width="5"><font class="Star"></font></td>
  <td>{if $level eq "E"}{$lng.lbl_embedded}{elseif $level eq "R"}{$lng.lbl_root}{/if}</td>
</tr>

<tr>
  <td height="10" class="FormButton" nowrap="nowrap" valign="top">{$lng.lbl_page_file}:</td>
  <td><font class="Star">{if $page_data.filename eq ""}*{/if}</font></td>
  <td><i>{$page_path}</i>{if $page_data.filename eq ""}<input type="text" size="25" name="filename" value="{$default_filename|escape}" />{/if}</td>
</tr>

<tr>
  <td height="10" class="FormButton" nowrap="nowrap" valign="top">{$lng.lbl_page_name}:</td>
  <td><font class="Star">*</font></td>
  <td><input type="text" name="pagetitle" value="{$page_data.title|default:"$default_page_title"|escape}" size="45" {if $config.SEO.clean_urls_enabled eq "Y"}onchange="javascript: if (this.form.clean_url.value == '') copy_clean_url(this, this.form.clean_url)"{/if} /></td>
</tr>

{if $level eq "E"}
  {include file="main/clean_url_field.tpl" clean_url=$page_data.clean_url|default:$default_clean_url show_req_fields="Y" clean_urls_history=$page_data.clean_urls_history clean_url_fill_error=$top_message.clean_url_fill_error}
{/if}

<tr>
  <td height="10" class="FormButton" nowrap="nowrap" valign="top">{$lng.lbl_page_content}:</td>
  <td height="10" valign="top"><font class="Star">*</font></td>
  <td>
{if $page_content eq ''}{assign var="page_content" value=$default_page_content}{/if}
{if $level eq "E"}
{include file="main/textarea.tpl" name="pagecontent" cols=50 rows=30 data=$page_content btn_rows=4}
{else}
{include file="main/textarea.tpl" name="pagecontent" cols=50 rows=30 data=$page_content btn_rows=4 html_editor_mode="XHTML"}
{/if}
  </td>
</tr>

{if $level ne "R"}
<tr>
  <td height="10" class="FormButton" nowrap="nowrap" valign="top">{$lng.lbl_title_tag}:</td>
  <td>&nbsp;</td>
  <td><textarea name="title_tag" rows="6" cols="85">{$page_data.title_tag|default:"$default_title_tag"}</textarea></td>
</tr>

<tr>
  <td height="10" class="FormButton" nowrap="nowrap" valign="top">{$lng.lbl_meta_keywords}:</td>
  <td>&nbsp;</td>
  <td><textarea name="meta_keywords" rows="6" cols="85">{$page_data.meta_keywords|default:"$default_meta_keywords"}</textarea></td>
</tr>

<tr>
  <td height="10" class="FormButton" nowrap="nowrap" valign="top">{$lng.lbl_meta_description}:</td>
  <td>&nbsp;</td>
  <td><textarea name="meta_description" rows="6" cols="85">{$page_data.meta_description|default:"$default_meta_description"}</textarea></td>
</tr>
{/if}

{if $level ne "R"}
<tr>
  <td height="10" class="FormButton" nowrap="nowrap" valign="top">{$lng.lbl_status}:</td>
  <td><font class="Star">*</font></td>
  <td>
<select name="active">
  <option value="Y"{if $page_data.active eq "Y"} selected="selected"{/if}>{$lng.lbl_enabled}</option>
  <option value="N"{if $page_data.active eq "N"} selected="selected"{/if}>{$lng.lbl_disabled}</option>
</select>
  </td>
</tr>
{/if}

{if $level ne "R"}
<tr>
  <td height="10" class="FormButton" nowrap="nowrap" valign="top">{$lng.lbl_position}:</td>
  <td><font class="Star"></font></td>
  <td><input type="text" name="orderby" value='{$page_data.orderby|default:"$default_orderby"}' size="5" /></td>
</tr>
{/if}

{if $level eq 'E'}
<tr>
  <td height="10" class="FormButton" nowrap="nowrap" valign="top">{$lng.lbl_page_show_in_menu}:</td>
  <td><font class="Star"></font></td>
  <td><input type="checkbox" name="show_in_menu" value="Y"{if $page_data.show_in_menu eq 'Y' or $pageid eq '0'} checked="checked"{/if} /></td>
</tr>
{/if}

<tr>
  <td colspan="2">&nbsp;</td>
  <td class="SubmitBox"><input type="submit" value=" {$lng.lbl_save|strip_tags:false|escape} " /></td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_static_page_details content=$smarty.capture.dialog extra='width="100%"'}

{if $level eq "E" and $pageid ne 0}
  <br />
  {include file="main/clean_urls.tpl" resource_name="pageid" resource_id=$pageid clean_url_action="pages.php" clean_urls_history_mode="clean_urls_history" clean_urls_history=$page_data.clean_urls_history}
{/if}

{if $level eq "E" and $config.SEO.clean_urls_enabled eq "Y"}
<script type="text/javascript">
//<![CDATA[
{literal}

function clean_url_page_updater() {
  if (document.pagesform.clean_url) {
    if (document.pagesform.clean_url.value == '')
      copy_clean_url(document.pagesform.pagetitle, document.pagesform.clean_url);

    document.pagesform.clean_url.onfocus = function() {
      if (this.value == '')
        copy_clean_url(this.form.pagetitle, this);

      return true;
    }
  }
}

if (window.addEventListener)
  window.addEventListener("load", clean_url_page_updater, false);

else if (window.attachEvent)
  window.attachEvent("onload", clean_url_page_updater);
{/literal}
//]]>
</script>
{/if}
