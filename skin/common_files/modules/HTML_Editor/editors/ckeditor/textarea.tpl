{*
$Id: textarea.tpl,v 1.1 2010/05/21 08:32:23 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript">
<!--
if (popup_html_editor_text!=undefined) {ldelim}
  $("#{$id}").val(popup_html_editor_text);
  CKEDITOR.replace("{$id}");
{rdelim}
-->
</script>
