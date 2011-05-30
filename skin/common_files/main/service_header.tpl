{*
$Id: service_header.tpl,v 1.2 2010/06/21 13:43:36 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{config_load file="$skin_config"}
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>{$lng.txt_site_title}</title>
  {include file="meta.tpl"}
  <link rel="stylesheet" type="text/css" href="{$SkinDir}/css/skin1_admin.css" />
</head>
<body{$reading_direction_tag}>
{literal}
<script type="text/javascript" language="javascript">
//<![CDATA[
function refresh()
{
    window.scroll(0, 100000);

    setTimeout('refresh()', 1000);
}
function scrollDown()
{
    setTimeout('refresh()', 1000);
}
scrollDown();
//]]>
</script>
{/literal}
<div id="head-admin">

  <div id="logo-gray">
    <a href="{$http_location}/"><img src="{$ImagesDir}/logo_gray.png" alt="" /></a>
  </div>

</div>

<br />
