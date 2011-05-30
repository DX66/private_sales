{*
$Id: edit_file.tpl,v 1.7 2010/07/26 08:30:00 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_edit_file}

{$lng.txt_edit_file_top_text}<br />
<br />

<img src="{$ImagesDir}/folder.gif" width="16" height="16" alt="" />
<a href="file_edit.php?dir={$smarty.get.dir|escape:"url"}">{$root_skin_dir}{$smarty.get.dir|escape:"html"}</a><br />
<br />
{$lng.lbl_file}: <strong>{$filename}</strong><br />
<br />

{if not $is_writable}
  <strong>{$lng.lbl_warning}:</strong> {if $use_edit_area}{$lng.msg_err_file_permission_denied}{else}{$lng.msg_err_file_cannot_be_modified}{/if}<br />
{/if}

{if $file_type eq "image"}

  <img src="{$SkinDir}{$filename}" alt="" />

{else}

  {if $use_edit_area}

    <script type="text/javascript" src="{$SkinDir}/lib/edit_area/edit_area_full.js"></script>
<script type="text/javascript">
//<![CDATA[
var initData = {ldelim}
  id: "filebody",
{if $smarty.get.toggle_edit_area eq "Y"}
  display: "later",
{/if}
  start_highlight: true,
  allow_resize: "both",
  allow_toggle: true,
  syntax: '{$file_ext|default:html}',
  language: '{$shop_language|default:en}',
{if $file_ext_selector}  syntax_selection_allow: "css,html,js,tpl",{/if}
  toolbar: "search, go_to_line, |, undo, redo, |, select_font, {if $file_ext_selector}|, syntax_selection,{/if}|, change_smooth_selection, highlight, reset_highlight, |, help",
  allow_resize: "no"
{rdelim};

{literal}
if (window.editAreaLoader) {
  editAreaLoader.init(initData);
}
{/literal}
//]]>
</script>

  {/if}

  <form action="file_edit.php" method="post" {if $use_edit_area}onsubmit="javascript: if(!$('#edit_area_toggle_checkbox_filebody').attr('checked'))$('#toggle_edit_area').val('Y');"{/if}>
    <input type="hidden" name="filename" value="{$filename|escape}" />
    <input type="hidden" name="dir" value="{$smarty.get.dir|escape:"html"}" />
    <input type="hidden" name="opener" value="{$opener|escape}" />
    <input type="hidden" name="mode" value="save_file" />
    {if $use_edit_area}
    <input type="hidden" name="toggle_edit_area" id="toggle_edit_area" value="" />
    {/if}

    <textarea cols="100" rows="40" name="filebody" id="filebody" style="width: 100%;">{foreach from=$filebody item=l}{$l|escape:"html"}{/foreach}</textarea>
    <br />
    <br />

    <table width="100%">
    <tr>
      <td class="main-button">
        <input type="submit" value="&nbsp;{$lng.lbl_save|strip_tags:false|escape}&nbsp;" />
      </td>
      <td align="right">
        <input type="button" value="&nbsp;{$lng.lbl_cancel|strip_tags:false|escape}&nbsp;" onclick="javascript: history.go(-1);" />
      </td>
    </tr>
    </table>

  </form>

{/if}

{if $has_backup}

  <form method="post" action="file_edit.php" name="file_restore" onsubmit="javascript: return confirm('{$lng.txt_js_restore_template_note|wm_remove|escape:javascript}');">
    <input type="hidden" name="filename" value="{$filename|escape}" />
    <input type="hidden" name="dir" value="{$smarty.get.dir|escape:"html"}" />
    <input type="hidden" name="mode" value="restore" />
    <input type="hidden" name="opener" value="{$opener|escape}" />

    {$lng.txt_restore_template_note}<br />
    <input type="submit" value="{$lng.lbl_restore_file|strip_tags:false|escape}" />
  </form>

{/if}
