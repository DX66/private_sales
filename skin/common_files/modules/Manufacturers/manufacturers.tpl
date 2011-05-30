{*
$Id: manufacturers.tpl,v 1.6 2010/07/27 06:04:05 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_manufacturers}

{include file="check_clean_url.tpl"}

<script type="text/javascript">
//<![CDATA[
var txt_manufacturers_delete_msg = "{$lng.txt_manufacturers_delete_msg|wm_remove|escape:javascript}";
var requiredFields = [
  ['manufacturer', "{$lng.lbl_manufacturer|strip_tags|wm_remove|escape:javascript}", false]{if $config.SEO.clean_urls_enabled eq "Y" and $administrate}, ['clean_url', "{$lng.lbl_clean_url|strip_tags|wm_remove|escape:javascript}", false]{/if}
]
//]]>
</script>

{include file="check_required_fields_js.tpl"}

{$lng.txt_manufacturers_top_text}

{if $active_modules.Simple_Mode eq ""}
<br /><br />

{$lng.txt_manufacturers_note_pro}
{/if}

{if $single_mode eq ""}
<br /><br />

{$lng.txt_manufacturers_notes}

{if $active_modules.Simple_Mode eq "" and $usertype eq "P"}
{$lng.txt_manufacturers_note_pro_provider}
{/if}

{/if}

<br /><br />

{if $mode ne "manufacturer_info"}

{capture name=dialog}

{include file="main/navigation.tpl"}

{if $manufacturers ne ""}

<script type="text/javascript">
//<![CDATA[
checkboxes_form = 'manufform';
checkboxes = [{foreach from=$manufacturers item=v key=k}{if $k gt 0},{/if}'{if not (not $administrate and ($v.provider ne $logged_userid or $v.used_by_others))}to_delete[{$v.manufacturerid}]{/if}'{/foreach}];
 
//]]>
</script>
<script type="text/javascript" src="{$SkinDir}/js/change_all_checkboxes.js"></script>

<div style="line-height:170%"><a href="javascript:change_all(true);">{$lng.lbl_check_all}</a> / <a href="javascript:change_all(false);">{$lng.lbl_uncheck_all}</a></div>

{/if}

<form action="manufacturers.php" method="post" name="manufform">
<input type="hidden" name="mode" value="update" />
<input type="hidden" name="page" value="{$page}" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr class="TableHead">
  {if $manufacturers ne ""}<td width="10">&nbsp;</td>{/if}
  <td width="40%">{$lng.lbl_manufacturer}</td>
  <td width="30%">{$lng.lbl_provider}</td>
  <td width="20%" align="center">{$lng.lbl_products}</td>
  <td width="30" align="center">{$lng.lbl_pos}</td>
  <td width="30" align="center">{$lng.lbl_active}</td>
</tr>

{if $manufacturers ne ""}

{foreach from=$manufacturers item=v}

<tr{cycle values=", class='TableSubHead'"}>
  <td align="center"><input type="checkbox" name="to_delete[{$v.manufacturerid}]"{if not $administrate and ($v.provider ne $logged_userid or $v.used_by_others)} disabled="disabled"{/if} /></td>
  <td><b><a href="manufacturers.php?manufacturerid={$v.manufacturerid}{if $page}&amp;page={$page}{/if}">{$v.manufacturer}</a></b></td>
  <td>{if $v.is_provider eq 'Y'}{$v.provider_name}{else}{$lng.lbl_manuf_owner_lost}{/if}{if $administrate} ({$v.provider}){/if}</td>
  <td align="center">{$v.products_count|default:$lng.txt_not_available}{if $v.used_by_others}*{assign var="show_note" value="Y"}{/if}</td>
  <td align="center"><input type="text" name="records[{$v.manufacturerid}][orderby]" size="5" value="{$v.orderby}"{if not $administrate} disabled="disabled"{/if} /></td>
  <td align="center"><input type="checkbox" name="records[{$v.manufacturerid}][avail]" value="Y"{if $v.avail eq "Y"} checked="checked"{/if}{if not $administrate} disabled="disabled"{/if} /></td>
</tr>

{/foreach}

{if $show_note eq "Y"}
<tr>
  <td colspan="6"><br />{$lng.txt_manufacturers_special_note}</td>
</tr>
{/if}

<tr>
  <td colspan="3" class="main-button">
    <input type="submit" class="big-main-button" value="{$lng.lbl_update|strip_tags:false|escape}" />
  </td>
  <td colspan="3" align="right">
    <input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('^to_delete\\[.+\\]', 'gi')) &amp;&amp;confirm(txt_manufacturers_delete_msg)) submitForm(this.form, 'delete');" />
  </td>
</tr>

{else}

<tr>
  <td colspan="6" align="center"><br />{$lng.txt_no_manufacturers}</td>
</tr>

{/if}

<tr>
<td colspan="6"><br /><input type="button" value="{$lng.lbl_add_new_|strip_tags:false|escape}" onclick="javascript: self.location = 'manufacturers.php?mode=add';" /></td>
</tr>

</table>

</form>

{include file="main/navigation.tpl"}

{/capture}
{include file="dialog.tpl" title=$lng.lbl_manufacturers_list content=$smarty.capture.dialog extra='width="100%"'}

{else}

<script type="text/javascript" src="{$SkinDir}/js/popup_image_selection.js"></script>

{capture name=dialog}

<div align="right">
<table cellspacing="0" cellpadding="0">
<tr>
  <td>{include file="buttons/button.tpl" button_title=$lng.lbl_manufacturers_list href="manufacturers.php?page=`$page`"}</td>
{if $manufacturer.manufacturerid}
  <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
  <td>{include file="buttons/button.tpl" button_title=$lng.lbl_add_manufacturer href="manufacturers.php?mode=add&amp;page=`$page`"}</td>
{/if}
</tr>
</table>
</div>

{if not $administrate and $logged_userid ne $manufacturer.provider and $smarty.get.mode ne "add"}
{assign var="disabled" value=' disabled="disabled"'}
<br />
<font class="ErrorMessage">{$lng.txt_manufacturer_edit_warning}</font>
<br />

{elseif not $administrate and $manufacturer.used_by_others}
<br />
<font class="ErrorMessage">{$lng.txt_manufacturers_warning}</font>
<br />
{/if}

<br />

{if $manufacturer.manufacturerid ne ''}
{include file="main/language_selector.tpl" script="manufacturers.php?manufacturerid=`$manufacturer.manufacturerid`&"}
{/if}

<form action="manufacturers.php" method="post" enctype="multipart/form-data" name="manufacturerform" onsubmit="javascript: return checkRequired(requiredFields){if $config.SEO.clean_urls_enabled eq "Y" and $administrate} &amp;&amp;checkCleanUrl(document.manufacturerform.clean_url){/if};">
<input type="hidden" name="mode" value="details" />
<input type="hidden" name="manufacturerid" value="{$manufacturer.manufacturerid}" />
<input type="hidden" name="page" value="{$page}" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
  <td width="20%" class="FormButton">{$lng.lbl_manufacturer}:</td>
  <td><font class="Star">*</font></td>
  <td width="80%"><input type="text" name="manufacturer" id="manufacturer" size="50" value="{$manufacturer.manufacturer|escape}" style="width:80%"{$disabled} {if $config.SEO.clean_urls_enabled eq "Y" and $administrate}onchange="javascript: if (this.form.clean_url.value == '') copy_clean_url(this, this.form.clean_url)"{/if} /></td>
</tr>

{if $administrate}
  {include file="main/clean_url_field.tpl" clean_url=$manufacturer.clean_url show_req_fields="Y" clean_urls_history=$manufacturer.clean_urls_history clean_url_fill_error=$top_message.clean_url_fill_error}
{/if}

<tr>
  <td class="FormButton">{$lng.lbl_logo}:</td>
  <td>&nbsp;</td>
  {if $manufacturer.is_image ne 'Y'}{assign var="no_delete" value="Y"}{/if}
  <td>{include file="main/edit_image.tpl" type="M" id=$manufacturer.manufacturerid delete_url="manufacturers.php?mode=delete_image&amp;manufacturerid=`$manufacturer.manufacturerid`" button_name=$lng.lbl_save no_delete=$no_delete}</td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_description}:</td>
  <td>&nbsp;</td>
  <td>
{include file="main/textarea.tpl" name="descr" cols=55 rows=10 class="InputWidth" data=$manufacturer.descr width="80%" btn_rows=3}
  </td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_url}:</td>
  <td>&nbsp;</td>
  <td><input type="text" size="50" name="url" value="{$manufacturer.url|escape}" style="width: 80%;"{$disabled} /></td>
</tr>

<tr>
    <td class="FormButton">{$lng.lbl_title_tag}:</td>
    <td>&nbsp;</td>
    <td><textarea name="title_tag" rows="6" cols="85"{$disabled}>{$manufacturer.title_tag}</textarea></td>
</tr>

<tr>
    <td class="FormButton">{$lng.lbl_meta_keywords}:</td>
    <td>&nbsp;</td>
    <td><textarea name="meta_keywords" rows="6" cols="85"{$disabled}>{$manufacturer.meta_keywords}</textarea></td>
</tr>

<tr>
    <td class="FormButton">{$lng.lbl_meta_description}:</td>
    <td>&nbsp;</td>
    <td><textarea name="meta_description" rows="6" cols="85"{$disabled}>{$manufacturer.meta_description}</textarea></td>
</tr>

{if $administrate}
<tr>
  <td class="FormButton">{$lng.lbl_pos}:</td>
  <td>&nbsp;</td>
  <td><input type="text" name="orderby" size="5" value="{$manufacturer.orderby|default:"0"}" /></td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_availability}:</td>
  <td>&nbsp;</td>
  <td><input type="checkbox" name="avail" value="Y"{if $manufacturer.avail eq 'Y' or $manufacturer.manufacturerid eq ''} checked="checked"{/if} /></td>
</tr>
{/if}

</table>

<br />

<div id="sticky_content">
  <div class="main-button">
   <input type="submit" class="big-main-button" value=" {$lng.lbl_apply_changes|strip_tags:false|escape} "{$disabled} />
  </div>
</div>

</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_manufacturer_details content=$smarty.capture.dialog extra='width="100%"'}

{if $administrate and $mode eq "manufacturer_info" and $manufacturer.manufacturerid ne ''}
  <br />
  {include file="main/clean_urls.tpl" resource_name="manufacturerid" resource_id=$manufacturer.manufacturerid clean_url_action="manufacturers.php" clean_urls_history_mode="clean_urls_history" clean_urls_history=$manufacturer.clean_urls_history}
{/if}

{/if}

