{*
$Id: html_message_template.tpl,v 1.1 2010/05/21 08:32:15 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style type="text/css">
{literal}
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
{/literal}
</style>
</head>
<body{$reading_direction_tag}>
{if $mail_body_template}
{include file=$mail_body_template}
{/if}
</body>
</html>
