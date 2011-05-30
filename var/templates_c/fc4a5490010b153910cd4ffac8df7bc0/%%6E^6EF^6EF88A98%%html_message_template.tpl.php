<?php /* Smarty version 2.6.26, created on 2011-05-26 16:21:23
         compiled from mail/html/html_message_template.tpl */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style type="text/css">
<?php echo '
BODY {
  MARGIN-TOP: 10px; 
  MARGIN-BOTTOM: 10px;
  MARGIN-LEFT: 10px; 
  MARGIN-RIGHT: 10px;
  FONT-SIZE: 12px; 
  FONT-FAMILY: arial,helvetica,sans-serif;
  PADDING: 0px;
  BACKGROUND-COLOR: #FFFFFF;
  COLOR: #000000;
}
TD {
  FONT-SIZE: 12px; 
  FONT-FAMILY: arial,helvetica,sans-serif;
}
TH {
  FONT-SIZE: 13px; 
  FONT-FAMILY: arial,helvetica,sans-serif;
}
H1 {
    FONT-SIZE: 20px;
}
TABLE,IMG,A {
  BORDER: 0px;
}
'; ?>

</style>
</head>
<body<?php echo $this->_tpl_vars['reading_direction_tag']; ?>
>
<?php if ($this->_tpl_vars['mail_body_template']): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['mail_body_template'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
</body>
</html>