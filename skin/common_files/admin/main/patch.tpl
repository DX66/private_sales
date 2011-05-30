{*
$Id: patch.tpl,v 1.6 2010/07/06 10:38:20 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_patch_upgrade_center}

{if $all_files_to_patch ne ""}
{include file="admin/main/patch_apply.tpl"}
{else}

<br />

{$lng.txt_patch_upgrade_center_top_text}

<br /><br />

{capture name=dialog}
<script type="text/javascript">
//<![CDATA[
{literal}
var agree_checkboxes = ['chk1', 'chk2', 'chk3', 'chk4', 'chk5'];

function check_agree(form) {
  for (var i = 0; i < agree_checkboxes.length; i++) {
    var c = form.elements.namedItem(agree_checkboxes[i]);
    if (c && !c.checked)
      return false;
  }

  return true;
}

function patch_agree(o) {
  var valid = true;
  for (var i = 0; i < agree_checkboxes.length && valid; i++) {
    var c = o.form.elements.namedItem(agree_checkboxes[i]);
    if (c && !c.checked)
      valid = false;
  }

  $('input[name=submit]').button(valid ? 'enable' : 'disable')
  o.form.elements.namedItem('patch_filename').disabled = !valid;

  return true; 
}

{/literal}
//]]>
</script>
<a name="upgrade"></a>
<form action="patch.php" method="post" onsubmit="javascript: return check_agree(this);">
<input type="hidden" name="mode" value="upgrade" />

{if $memory_limit_not_is_set}
<br />
<font class="ErrorMessage">{$lng.lbl_warning}:</font> {$lng.lbl_patch_memory_limit_error|substitute:"memory_limit":$new_memory_limit}<br />
<table cellspacing="0" cellpadding="0">
<tr>
  <td nowrap="nowrap"><label for="skip_memory_limit">{$lng.lbl_patch_continue_upgrading_anyway}</label>&nbsp;</td>
  <td><input type="checkbox" name="skip_memory_limit" value="Y" id="skip_memory_limit" /></td>
</tr>
</table>
<br />
{/if}

<table>

<tr>
  <td nowrap="nowrap">{$lng.lbl_current_version}:</td>
  <td width="100%"><b>{$config.version}</b></td>
</tr>

<tr>
  <td nowrap="nowrap">{$lng.lbl_target_version}:</td>
  <td>
  <select name="patch_filename" disabled="disabled">
{if not $target_versions}
    <option>{$lng.lbl_no_available_patches}</option>
{else}
{section name=ver loop=$target_versions}
    <option value="{$config.version|replace:' ':'_'}-{$target_versions[ver]|replace:' ':'_'}">{$target_versions[ver]}</option>
{/section}
{/if}
  </select>
  </td>
</tr>

{if $corrupted_versions}
<tr>
  <td nowrap="nowrap">{$lng.lbl_corrupted_versions}:</td>
  <td>
  {section name=ver loop=$corrupted_versions}
    {$corrupted_versions[ver]}{if !$smarty.section.ver.last},&nbsp;{/if}
  {/section}
  {include file="main/tooltip_js.tpl" text=$lng.txt_check_filelst type="img" id="corrupted_versions_help"}
  </td>
</tr>
{/if}

{if $target_versions ne ""}

<tr>
  <td colspan="2" style="padding-top: 10px; padding-bottom: 10px;">
    <font class="ErrorMessage">{$lng.txt_patch_agreement_note}</font><br />
    <br />
    {$lng.txt_patch_agreement_chkboxes}:<br />
    <input type="checkbox" name="chk[1]" value="Y" id="chk1" onclick="javascript: patch_agree(this);" /><label for="chk1">{$lng.txt_patch_agreement_chk_1}</label><br />
    <input type="checkbox" name="chk[2]" value="Y" id="chk2" onclick="javascript: patch_agree(this);" /><label for="chk2">{$lng.txt_patch_agreement_chk_2}</label><br />
    <input type="checkbox" name="chk[3]" value="Y" id="chk3" onclick="javascript: patch_agree(this);" /><label for="chk3">{$lng.txt_patch_agreement_chk_3}</label><br />
    <input type="checkbox" name="chk[4]" value="Y" id="chk4" onclick="javascript: patch_agree(this);" /><label for="chk4">{$lng.txt_patch_agreement_chk_4}</label><br />
    <input type="checkbox" name="chk[5]" value="Y" id="chk5" onclick="javascript: patch_agree(this);" /><label for="chk5">{$lng.txt_patch_agreement_chk_5}</label><br />
  </td>
</tr>

<tr>
  <td colspan="2" class="main-button">
    <input type="submit" name="submit" value="{$lng.lbl_apply|strip_tags:false|escape}" disabled="disabled" />
  </td>
</tr>

{/if}

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_upgrade content=$smarty.capture.dialog extra='width="100%"'}

<br /><br />

{$lng.txt_patch_apply_note}

<br /><br />

{capture name=dialog}
<a name="apply_patch"></a>
<form action="patch.php" enctype="multipart/form-data" method="post">
<input type="hidden" name="mode" value="normal" />

<table>

<tr>
  <td>{$lng.lbl_patch_file}:</td>
  <td><input type="file" name="patch_file" size="48" /></td>
</tr>

<tr>
  <td>&nbsp;</td>
  <td><b>{$lng.lbl_or}</b></td>
</tr>

<tr>
  <td>{$lng.lbl_patch_url}:</td>
  <td><input type="text" name="patch_url" size="48" /></td>
</tr>

<tr>
  <td>&nbsp;</td>
  <td>
    <table cellpadding="2" cellspacing="2" border="0">
    <tr>
      <td><input type="checkbox" name="reverse" value="Y" /></td>
      <td>
        {include file="main/tooltip_js.tpl" text=$lng.txt_revert_files_note title=$lng.lbl_revert_files}
      </td>
    </tr>
    </table>
  </td>
</tr>

<tr>
  <td colspan="2" class="main-button">
    <br />
    <input type="submit" value="{$lng.lbl_apply|strip_tags:false|escape}" />
  </td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_apply_patch content=$smarty.capture.dialog extra='width="100%"'}

<br /><br />

<a name="patch_sql"></a>
{$lng.txt_apply_sql_patch_note}

<br /><br />

{capture name=dialog}
<a name="apply_sql_patch"></a>
<form action="patch.php" enctype="multipart/form-data" method="post">
<input type="hidden" name="mode" value="sql" />

<table>

<tr>
  <td>{$lng.lbl_patch_file}:</td>
  <td><input type="file" name="patch_file" size="48" /></td>
</tr>

<tr>
  <td>&nbsp;</td>
  <td><b>{$lng.lbl_or}</b></td>
</tr>

<tr>
  <td>{$lng.lbl_patch_url}:</td>
  <td><input type="text" name="patch_url" size="48" /></td>
</tr>

<tr>
  <td>&nbsp;</td>
  <td><b>{$lng.lbl_or}</b></td>
</tr>

<tr>
  <td>{$lng.lbl_sql_queries}:</td>
  <td><textarea cols="48" rows="5" name="patch_query"></textarea></td>
</tr>

<tr>
  <td colspan="2" class="main-button">
    <br />
    <input type="submit" value="{$lng.lbl_apply|strip_tags:false|escape}" />
  </td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_apply_sql_patch content=$smarty.capture.dialog extra='width="100%"'}

{/if}

