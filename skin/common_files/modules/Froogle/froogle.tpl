{*
$Id: froogle.tpl,v 1.2 2010/06/23 16:42:46 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_froogle_export}

{$lng.txt_froogle_note}

<br /><br />

<br />

{capture name=dialog}
{$lng.txt_froogle_format_note}
<br />
<br />

<form action="froogle.php" method="post" name="froogleform">
<input type="hidden" name="mode" value="fcreate" />
{if $all_languages_cnt le 1}
<input type="hidden" name="froogle_lng" value="{$config.default_customer_language|escape}" />
{/if}
<table cellspacing="5" cellpadding="0">

{if $all_languages_cnt gt 1}
<tr>
    <td style="padding-bottom: 5px;">{$lng.lbl_froogle_select_language}</td>
    <td>
<select name="froogle_lng">
{foreach from=$all_languages item=l}
  <option value="{$l.code|escape}"{if $froogle_lng eq $l.code} selected="selected"{/if}>{$l.language}</option>
{/foreach}
</select>
  </td>
</tr>
{/if}

<tr>
  <td>{$lng.lbl_filename}</td>
  <td><input type="text" name="froogle_file" value="{$froogle_file|default:$def_froogle_file}" size="25" /></td>
</tr>
<tr>
  <td colspan="2" class="SubmitBox">
  <input type="button" value="{$lng.lbl_export|strip_tags:false|escape}" onclick="javascript: submitForm(this, 'fcreate');" />
  <input type="button" value="{$lng.lbl_download|strip_tags:false|escape}" onclick="javascript: submitForm(this, 'fdownload');" />
  {if $is_ftp_module eq 'Y'}
  <input type="button" value="{$lng.lbl_upload|strip_tags:false|escape}" onclick="javascript: submitForm(this, 'fupload');" />
  {/if}
</td>
</tr>
</table>
</form>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_froogle_export content=$smarty.capture.dialog extra='width="100%"'}

