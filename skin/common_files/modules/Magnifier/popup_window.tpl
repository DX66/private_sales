{*
$Id: popup_window.tpl,v 1.2.2.1 2011/03/04 14:03:26 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{config_load file="$skin_config"}
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>{$productname|escape}</title>
  {include file="customer/service_css.tpl"}
  {load_defer file="lib/swfobject-min.js" type="js"}
  {include file="customer/meta.tpl"}
  {load_defer file="modules/Magnifier/popup_window.js" type="js"}
  {load_defer file="css/`$smarty.config.CSSFilePrefix`.popup.css" type="css"}
  {if $config.UA.browser eq "MSIE" and $config.UA.version eq "6.0"}
    {load_defer file="css/`$smarty.config.CSSFilePrefix`.popup.IE6.css" type="css"}
  {/if}
{load_defer_code type="css"}
{load_defer_code type="js"}
</head>
<body>
  {include file="modules/Magnifier/product_magnifier.tpl" productid=$productid imageid=$imageid}
</body>
</html>
