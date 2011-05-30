<?php /* Smarty version 2.6.26, created on 2011-05-27 11:08:41
         compiled from main/start_textarea.tpl */ ?>
<script type="text/javascript">
//<![CDATA[
  var isHTML_Editor = true;
//]]>
</script>
<?php if ($this->_tpl_vars['config']['HTML_Editor']['editor'] == 'ckeditor'): ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['SkinDir']; ?>
/modules/HTML_Editor/editors/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['SkinDir']; ?>
/modules/HTML_Editor/editors/ckeditor/start_textarea.js"></script>
<?php elseif ($this->_tpl_vars['config']['HTML_Editor']['editor'] == 'tinymce'): ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['SkinDir']; ?>
/modules/HTML_Editor/editors/tinymce/tiny_mce.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['SkinDir']; ?>
/modules/HTML_Editor/editors/tinymce/start_textarea.js"></script>
<script type="text/javascript">
//<![CDATA[
tinyMCE.init({
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
});
//]]>
</script>
<?php elseif ($this->_tpl_vars['config']['HTML_Editor']['editor'] == 'innovaeditor'): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/HTML_Editor/editor.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>