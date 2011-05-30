{*
$Id: popup.tpl,v 1.1 2010/05/21 08:32:23 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<script type="text/javascript">
<!--
var id = "{$id}";
{literal}

function save() {
  if (window.opener && window.opener.document.getElementById(id))
    window.opener.document.getElementById(id).value = editor_get_xhtml_body("TArea");
  window.close();
}

if (window.opener && window.opener.document.getElementById(id)) {
  popup_html_editor_text = window.opener.document.getElementById(id).value.replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&amp;/g,'&');
}
{/literal}
-->
</script>

{include file="main/textarea.tpl" name="TArea" cols="65" rows="12" no_links="Y"}

<script type="text/javascript">
<!--
  $("#TArea").val(popup_html_editor_text);
-->
</script>

<br />
<div align="center">
<input type="button" value="{$lng.lbl_apply|strip_tags:false|escape}" onclick="javascript: save();" />
</div>
