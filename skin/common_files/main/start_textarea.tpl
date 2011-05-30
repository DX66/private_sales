{*
$Id: start_textarea.tpl,v 1.3 2010/06/08 06:17:39 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript">
//<![CDATA[
  var isHTML_Editor = true;
//]]>
</script>
{if $config.HTML_Editor.editor eq "ckeditor"}
<script type="text/javascript" src="{$SkinDir}/modules/HTML_Editor/editors/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="{$SkinDir}/modules/HTML_Editor/editors/ckeditor/start_textarea.js"></script>
{elseif $config.HTML_Editor.editor eq "tinymce"}
<script type="text/javascript" src="{$SkinDir}/modules/HTML_Editor/editors/tinymce/tiny_mce.js"></script>
<script type="text/javascript" src="{$SkinDir}/modules/HTML_Editor/editors/tinymce/start_textarea.js"></script>
<script type="text/javascript">
//<![CDATA[
tinyMCE.init({ldelim}
  mode : "none",
  theme : "advanced",
  skin : "o2k7",
  skin_variant : "silver",
  relative_urls : false,
  plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
  theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
  theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
  theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
  theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
  theme_advanced_toolbar_location : "top",
  theme_advanced_toolbar_align : "left",
  theme_advanced_statusbar_location : "bottom",
  theme_advanced_resizing : true
{rdelim});
//]]>
</script>
{elseif $config.HTML_Editor.editor eq "innovaeditor"}
{include file="modules/HTML_Editor/editor.tpl"}
{/if}
