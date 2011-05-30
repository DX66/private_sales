{*
$Id: waiting.tpl,v 1.5 2010/07/02 11:52:50 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset={$default_charset|default:"iso-8859-1"}" />
  <meta http-equiv="X-UA-Compatible" content="IE=8" />
	<meta http-equiv="Refresh" content="{$time};URL={$url}" />
	<title>{$lng.lbl_please_wait|wm_remove|escape}</title>
<style type="text/css">
//<![CDATA[
{literal}
html {
  width: 100%;
  text-align: center;
}
body {
  font-family: verdana, arial, helvetica, sans-serif;
  color: #2c3e49;
  font-size: 12px;
  margin: 0px;
  padding: 0px;
  background-color: #ffffff;
  text-align: center;
  padding: 44px 15% 0px 15%;
  vertical-align: top;
}
a:link {
  color: #330000;
  text-decoration: underline;
}
a:visited {
  color: #330000;
  text-decoration: underline;
}
a:hover {
  color: #550000;
  text-decoration: none;
}
a:active  {
  color: #330000;
  text-decoration: underline;
}
h1 {
  font-size: 14px;
  color: #a10000;
}
{/literal}
//]]>
</style>
</head>
<body{$reading_direction_tag}>
  <h1>{$lng.lbl_please_wait}</h1>
  <p>{$lng.txt_header_location_note|substitute:"time":$time:"location":$url}</p>
</body>
</html>
