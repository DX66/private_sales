{*
$Id: textarea.tpl,v 1.3 2010/06/08 06:17:39 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $active_modules.HTML_Editor and not $html_editor_disabled}

{include file="main/start_textarea.tpl" _include_once=1}
{assign var="id" value=$name|regex_replace:"/[^\w\d_]/":""}
{strip}

{if $no_links ne "Y"}
<div class="AELinkBox" style="width: {$width|default:"80%"};">
<a href="javascript:void(0);" style="display: none;" id="{$id}Dis" onclick="javascript: disableEditor('{$id}','{$name}');">{$lng.lbl_default_editor}</a>
<b id="{$id}DisB">{$lng.lbl_default_editor}</b>
&nbsp;&nbsp;
<a href="javascript:void(0);" id="{$id}Enb" onclick="javascript: enableEditor('{$id}','{$name}');">{$lng.lbl_advanced_editor}</a>
<b id="{$id}EnbB" style="display: none;">{$lng.lbl_advanced_editor}</b>
</div>
{/if}

<textarea id="{$id}" name="{$name}"{if $cols} cols="{$cols}"{/if} {if $rows} rows="{$rows}"{/if} class="InputWidth {$class}" style="width: {$width|default:"80%"};">{$data|escape}</textarea>

<div id="{$id}Box" style="display:none;">

<textarea id="{$id}Adv"{if $cols} cols="{$cols}"{/if} {if $rows} rows="{$rows}"{/if} class="InputWidth {$class}" style="width: 576px;{if $no_links eq "Y"}display:none;{/if}">{$data|escape}</textarea>

{if $config.HTML_Editor.editor eq "ckeditor"}
{include file="modules/HTML_Editor/editors/ckeditor/textarea.tpl" id=$id name=$name data=$data}
{elseif $config.HTML_Editor.editor eq "innovaeditor"}
{include file="modules/HTML_Editor/editors/innovaeditor/textarea.tpl" id=$id name=$name data=$data}
{elseif $config.HTML_Editor.editor eq "tinymce"}
{include file="modules/HTML_Editor/editors/tinymce/textarea.tpl" id=$id name=$name data=$data}
{/if}

</div>
{/strip}

<script type="text/javascript">
//<![CDATA[
var isOpen = getCookie('{$id}EditorEnabled');
if (isOpen && isOpen == 'Y')
  $('#{$id}Enb').click();
//]]>
</script>

{else}
<textarea id="{$id}" name="{$name}"{if $cols} cols="{$cols}"{/if}{if $rows} rows="{$rows}"{/if} class="InputWidth {$class}" {if $style} style="{$style}"{/if}{if $disabled} disabled="disabled"{/if}>{$data|escape}</textarea>

{/if}
