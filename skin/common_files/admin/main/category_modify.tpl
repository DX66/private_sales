{*
$Id: category_modify.tpl,v 1.6 2010/07/09 10:19:28 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
window.name = "catmodwin";
//]]>
</script>

<script type="text/javascript" src="{$SkinDir}/js/popup_image_selection.js"></script>

{if $section ne 'lng'}

{include file="check_clean_url.tpl"}

<script type="text/javascript">
//<![CDATA[
var requiredFields = [
  ['category_name', "{$lng.lbl_category|strip_tags|wm_remove|escape:javascript}", false]{if $config.SEO.clean_urls_enabled eq "Y"}, ['clean_url', "{$lng.lbl_clean_url|strip_tags|wm_remove|escape:javascript}", false]{/if}
]
//]]>
</script>

{include file="check_required_fields_js.tpl"}

{if $mode eq "add"}
{assign var="title" value=$lng.lbl_add_category}
{else}
{assign var="title" value=$lng.lbl_modify_category}
{/if}

{include file="page_title.tpl" title=$title}

<a name="modify_category"></a>

{include file="admin/main/location.tpl"}

<form name="addform" action="category_modify.php" method="post" enctype="multipart/form-data" onsubmit="javascript: return checkRequired(requiredFields){if $config.SEO.clean_urls_enabled eq "Y"} &amp;&amp;checkCleanUrl(document.addform.clean_url){/if}">
<input type="hidden" name="mode" value="{$mode}" />

{if $mode eq "add"}
  <input type="hidden" name="parent" value="{$cat}" />
{else}
  <input type="hidden" name="cat" value="{$cat}" />
{/if}

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
  <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_category_icon}:</td>
  <td width="10" height="10">&nbsp;</td>
  <td height="10">
    {if $mode ne "add"}
      {if $image.image_size le 0}{assign var="no_delete" value="Y"}{/if}
    {include file="main/edit_image.tpl" type="C" id=$current_category.categoryid delete_url="category_modify.php?mode=delete_icon&amp;cat=`$cat`" button_name=$lng.lbl_save no_delete=$no_delete}
      {else}
    {include file="main/edit_image.tpl" type="C" id=0 delete_url="category_modify.php?mode=delete_icon&amp;cat=`$cat`" button_name=$lng.lbl_save}
    {/if}
  </td>
</tr>

<tr>
  <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_position}:</td>
  <td width="10" height="10">&nbsp;</td>
  <td height="10">
    <input type="text" name="order_by" size="5" value="{$current_category.order_by}" />
  </td>
</tr>

<tr>
  <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_category}:</td>
  <td width="10" height="10"><font class="Star">*</font></td>
  <td height="10">
    <input type="text" name="category" id="category" maxlength="255" size="65" value="{$current_category.category|escape:"html"}" {if $config.SEO.clean_urls_enabled eq "Y"}onchange="javascript: if (this.form.clean_url.value == '') copy_clean_url(this, this.form.clean_url)"{/if}/>
  </td>
</tr>

{if $mode ne "add"}
  {include file="main/clean_url_field.tpl" clean_url=$current_category.clean_url show_req_fields="Y" clean_urls_history=$current_category.clean_urls_history clean_url_fill_error=$top_message.clean_url_fill_error}
{else}
  {include file="main/clean_url_field.tpl" clean_url="" show_req_fields="Y" clean_urls_history=""}
{/if}

<tr>
  <td height="10" class="FormButton" nowrap="nowrap" valign="top">{$lng.lbl_description}:</td>
  <td width="10" height="10"><font class="Star"></font></td>
  <td height="10">
    {include file="main/textarea.tpl" name="description" cols=65 rows=15 data=$current_category.description}
  </td>
</tr>

<tr>
  <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_membership}:</td>
  <td width="10" height="10"><font class="FormButtonOrange"></font></td>
  <td height="10">{include file="main/membership_selector.tpl" data=$current_category}</td>
</tr>

<tr>
  <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_availability}:</td>
  <td width="10" height="10"><font class="Star"></font></td>
  <td height="10">
    <select name="avail">
      <option value='Y' {if ($current_category.avail eq 'Y')} selected="selected"{/if}>{$lng.lbl_enabled}</option>
      <option value='N' {if ($current_category.avail eq 'N')} selected="selected"{/if}>{$lng.lbl_disabled}</option>
    </select>
  </td>
</tr>

<tr>
  <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_title_tag}:</td>
  <td width="10" height="10"><font class="FormButtonOrange"></font></td>
  <td height="10">
    <textarea cols="65" rows="4" name="title_tag">{$current_category.title_tag|escape:"html"}</textarea>
  </td>
</tr>

<tr>
  <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_meta_keywords}:</td>
  <td width="10" height="10"><font class="FormButtonOrange"></font></td>
  <td height="10">
    <textarea cols="65" rows="4" name="meta_keywords">{$current_category.meta_keywords|escape:"html"}</textarea>
  </td>
</tr>

<tr>
  <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_meta_description}:</td>
  <td width="10" height="10"><font class="FormButtonOrange"></font></td>
  <td height="10">
    <textarea cols="65" rows="4" name="meta_description">{$current_category.meta_description|escape:"html"}</textarea>
  </td>
</tr>

<tr>
    <td height="10" class="FormButton">{$lng.lbl_override_child_meta_data}:</td>
    <td width="10" height="10"><font class="FormButtonOrange"></font></td>
    <td height="10">
      <input type="checkbox" name="override_child_meta" value="Y"{if $current_category.override_child_meta eq 'Y'} checked="checked"{/if} /><br />
      <b>{$lng.lbl_note}:</b>&nbsp;{$lng.lbl_override_child_meta_data_note}
  </td>
</tr>
</table>
<br /><br />

<div id="sticky_content">
  <div class="main-button">
    <input type="submit" class="big-main-button" value=" {$lng.lbl_apply_changes|strip_tags:false|escape} " />
  </div>
</div>

<table cellpadding="3" cellspacing="1" width="100%">
{if $mode ne "add"}

<tr>
  <td colspan="3"><br /><br />{include file="main/subheader.tpl" title=$lng.lbl_category_location_title}</td>
</tr>

<tr>
  <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_category_location}</td>
  <td width="10" height="10"><font class="FormButtonOrange"></font></td>
  <td height="10">
<select name="cat_location">
  <option value="0">{$lng.lbl_root_level}</option>
{foreach from=$allcategories item=c key=catid}
{if $c.moving_enabled}
  <option value="{$catid}"{if $catid eq $current_category.parentid} selected="selected"{/if}>{$c.category_path}</option>
{/if}
{/foreach}
</select>
  </td>
</tr>

<tr>
  <td colspan="2" class="FormButton">&nbsp;</td>
  <td class="SubmitBox"><input type="button" value="{$lng.lbl_update|strip_tags:false|escape}" onclick="javascript: submitForm(this, 'move');" /></td>
</tr>

{/if}

</table>
</form>

{if $section ne "lng" and $mode ne "add" and $cat gt 0}
  <br />
  {include file="main/clean_urls.tpl" resource_name="cat" resource_id=$cat clean_url_action="category_modify.php" clean_urls_history_mode="clean_urls_history" clean_urls_history=$current_category.clean_urls_history}
{/if}

{elseif $section eq 'lng' and $mode ne "add" and $cat gt 0}

{include file="admin/main/category_lng.tpl"}

{/if}

